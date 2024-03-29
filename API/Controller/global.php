<?php
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Access-Control-Allow-Headers: *");


use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

require_once('../vendor/autoload.php');

include_once "./Model/database.php";

class GlobalMethods extends Connection
{

    private $env;


    function __construct()
    {
        $this->env = parse_ini_file('.env');
    }
    /**
     * Global function to execute queries
     *
     * @param string $sqlString
     *   string representing sql query.
     *
     * @return array
     *   the result of query.
     */
    public function executeGetQuery($sqlString)
    {
        $data = array();
        $errmsg = "";
        $code = 0;

        try {
            if ($result = $this->connect()->query($sqlString)->fetchAll()) {
                foreach ($result as $record) {
                    array_push($data, $record);
                }
                $code = 200;
                $result = null;
                return array("code" => $code, "data" => $data);
            } else {
                $errmsg = "No data found";
                $code = 404;
            }
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 403;
        }
        return array("code" => $code, "errmsg" => $errmsg, "data" => $data);
    }

    public function executePostQuery($stmt)
    {
        $errmsg = "";
        $code = 0;

        try {
            if ($stmt->execute()) {
                $code = 200;
                return array("code" => $code, "msg" => 'Successful Query.');
            } else {
                $errmsg = "No data found";
                $code = 404;
            }
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 403;
        }
        return array("code" => $code, "errmsg" => $errmsg);
    }

    public function saveImage($dir) //A reusable function for saving images based on provided directory location
    {
        //Declare temporary holders for parameter and value for sql
        $tempFile = '';
        $fileName = '';

        //Iterates through the file uploaded (image)
        foreach ($_FILES as $key => $file) {
            //Assigngs the parameter and value (filename)
            $tempFile = $file['tmp_name'];
            $fileName = $file['name'];
        }
        //Fetch last autoincrement id on commex
        $lastIncrementID = $this->getLastID('commex');
        //Declares folder location
        $fileFolder = __DIR__ . $dir . "$lastIncrementID/";

        //Creates directory if it doesn't exist yet
        if (!file_exists($fileFolder)) {
            mkdir($fileFolder, 0777);
        }

        //Declares location for image file itself.
        $filepath = __DIR__ . $dir . "$lastIncrementID/$fileName";

        //If file exists in path, delete it.
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        //Add file to give nfilepath
        if (!move_uploaded_file($tempFile, $filepath)) {
            return array("code" => 404, "errmsg" => "Upload unsuccessful");
        }

        return $filepath = str_replace("C:\\xampp\\htdocs", "", $filepath);
    }

    public function verifyToken()
    {
        //Check existence of token
        if (!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            header('HTTP/1.0 403 Forbidden');
            echo 'Token not found in request';
            exit;
        }

        //Check header
        $jwt = $matches[1];
        if (!$jwt) {
            header('HTTP/1.0 403 Forbidden');
            echo 'Token is missing but header exists';
            exit;
        }
        //Separate token to 3 parts
        $jwtArr = explode('.', $jwt);

        $headers = new stdClass();
        // $env = parse_ini_file('.env');
        $secretKey = $this->env["GCFAMS_API_KEY"];

        //Decode received token
        $payload = JWT::decode($jwt, new Key($secretKey, 'HS512'), $headers);

        //Decode payload part
        $parsedPayload = json_decode(json_encode($payload), true);

        //Re-encode decoded payload with the stored signature key to check for tampers
        $toCheckSignature = JWT::encode($parsedPayload, $secretKey, 'HS512');
        $toCheckSignature = explode('.', $toCheckSignature);

        //If re-encoded token is equal to received token, validate token.
        if ($toCheckSignature[2] == $jwtArr[2]) {
            return array(
                "code" => 200,
                "payload" => $payload->id
            );
        } else {
            header('HTTP/1.0 403 Forbidden');
            echo 'Currently encoded payload does not matched initially signed payload';
            exit;
        }
    }

    public function prepareAddBind($table, $params, $form)
    {
        $sql = "INSERT INTO `$table`(";
        $tempParam = "(";
        $tempValue = "";

        foreach ($params as $key => $col) {
            //Insertion columns details
            sizeof($params) - 1 != $key ? $sql = $sql . $col . ', ' : $sql = $sql . $col . ')';
            //Question marks
            sizeof($params) - 1 != $key ? $tempParam = $tempParam . '?' . ', ' : $tempParam = $tempParam . '?' . ')';
        }

        $sql = $sql . " VALUES " . $tempParam;
        $stmt = $this->connect()->prepare($sql);

        foreach ($form as $key => $value) {
            $stmt->bindParam(($key + 1), $form[$key]);
        }

        return $this->executePostQuery($stmt);
        // return $sql;
    }

    public function prepareEditBind($table, $params, $form, $rowId)
    {
        // UPDATE `educattainment`
        // SET `faculty_ID` = 3, `educ_title` = 'My nutsacks', `educ_school` = 'Nutsack School', `year_start` = '2022', `year_end` = '2023', `educ_details` = 'very nuts, much sacks'
        // WHERE `educattainment_ID` = 26;


        $sql = "UPDATE `$table`
                SET ";

        foreach ($params as $key => $col) {
            //Insertion columns details
            sizeof($params) - 1 != $key ? $sql = $sql . "`$col` = ?, " : $sql = $sql . "`$col` = ? ";
        }
        $sql = $sql . "WHERE `$rowId` = ?";
        $stmt = $this->connect()->prepare($sql);
        foreach ($form as $key => $value) {
            $stmt->bindParam(($key + 1), $form[$key]);
        }

        return $this->executePostQuery($stmt);
    }

    public function prepareDeleteBind($table, $col, $id)
    {
        $sql = "DELETE FROM `$table` WHERE `$col` = ?";

        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(1, $id);

        return $this->executePostQuery($stmt);
    }

    public function getLastID($table)
    {

        $DBName = $this->env["DB_NAME"];
        $sql = "SELECT AUTO_INCREMENT 
                FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = '$DBName' AND TABLE_NAME = '$table'";

        return $this->executeGetQuery($sql)['data'][0]['AUTO_INCREMENT'];
    }
}
