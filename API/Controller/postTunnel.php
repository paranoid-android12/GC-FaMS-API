<?php

use function PHPSTORM_META\type;

// header('Access-Control-Allow-Origin: http://localhost:4200');
// header('Access-Control-Allow-Methods: GET, POST, PATCH, OPTIONS');
// header("Access-Control-Allow-Headers: *");

// Fetches every single file in Model
foreach (glob("./Model/*/*.php") as $filename) {
    include_once $filename;
}
// include_once "./Model/Login/login.php";

include_once __DIR__ . '/./global.php';
class PostTunnel extends GlobalMethods
{
    private $login;
    private $resume;

    private $faculty;
    private $commex;

    public function __construct()
    {
        $this->login = new Login();
        $this->resume = new ResumeInfo();
        $this->faculty = new Faculty();
        $this->commex = new Commex();
        $this->eval = new Evaluation();
        $this->course = new Schedule();
        $this->cv = new CurriculumVitae();
    }

    // public function addFaculty($data){
    //     return $this->faculty->addFaculty($data);
    // }

    public function toValidateLogin($form)
    {
        // return $form->email;
        return $this->login->validateLogin($this->secureDecrypt($form));
        // return $this->secureDecrypt($form);
        // return $this->login->validateLogin($form);
    }

    public function addFaculty($data)
    {

        return $this->faculty->addFaculty();
    }

    public function toAddEval($data, $id)
    {
        return $this->eval->addEval($data, $id);
    }

    public function toAddCourse($data, $id)
    {
        return $this->course->addCourse($data, $id);
    }

    public function toDeleteCourse($courseId, $id)
    {
        return $this->course->deleteCourse($courseId, $id);
    }

    public function toAddCommex()
    {
        return $this->commex->addCommex();
        // return $_POST;
    }

    public function toAddCv($data, $id)
    {
        return $this->cv->generateCv($data, $id);
    }

    public function toSelectCv($data, $id)
    {
        return $this->resume->selectCv($data, $id);
    }

    public function toAddResume($form, $id, $type)
    {
        switch ($type) {
            case 1:
                return $this->resume->addEduc($form, $id);

            case 2:
                return $this->resume->addExp($form, $id);

            case 3:
                return $this->resume->addFacultyCert($form, $id);

            case 4:
                return $this->resume->addProj($form, $id);

            case 5:
                return $this->resume->addSpec($form, $id);

            case 6:
                return $this->resume->addNewCert($form, $id);

            case 7:
                return $this->resume->addNewSpec($form, $id);

            default:
                # code...
                break;
        }
    }

    public function toEditResume($form, $id, $type)
    {
        switch ($type) {
            case 1:
                return $this->resume->editEduc($form, $id);

            case 2:
                return $this->resume->editExp($form, $id);

            case 3:
                return $this->resume->editCert($form, $id);

            case 4:
                return $this->resume->editProj($form, $id);

            case 5:
                return $this->resume->editSpec($form, $id);

            default:
                # code...
                break;
        }
    }

    public function toDeleteResume($id, $type)
    {
        switch ($type) {
            case 1:
                return $this->resume->deleteEduc($id);

            case 2:
                return $this->resume->deleteExp($id);

            case 3:
                return $this->resume->deleteCert($id);

            case 4:
                return $this->resume->deleteProj($id);

            case 5:
                return $this->resume->deleteSpec($id);

            default:
                # code...
                break;
        }
    }

    public function toDeleteFaculty($id)
    {
        return $this->faculty->deleteFaculty($id);
    }

    public function toEditFaculty($data, $id)
    {
        return $this->faculty->editFaculty($data, $id);
    }

    public function toEditProfile($id)
    {
        return $this->faculty->editProfile($id);
    }

    public function toEditCover($id)
    {
        return $this->faculty->editCover($id);
        // switch ($params) {
        //     case 'faculty':
        //         return $this->faculty->editFaculty(null, $id);
        // }
    }

    public function toEditCommex($data, $id)
    {
        return $this->commex->editCommex($data, $id);
    }
    function test($data, $id)
    {
        return $this->faculty->editPassword($this->secureDecrypt($data), $id);
    }
    public function toAddAttendee($faculty_ID, $college_ID)
    {

        $formData = $_POST["attendees"][0];
        $data = json_decode($formData);
        return $this->commex->addAttendee($faculty_ID, $college_ID, $data->commex_ID);
    }

    public function toDeleteAttendee($commex_ID, $faculty_ID)
    {

        return $this->commex->deleteAttendee($commex_ID, $faculty_ID);
        // return $commex_ID;
    }

    public function toDeleteCommex($commex_ID)
    {
        return $this->commex->deleteCommex($commex_ID);
    }

    public function toEditPassword($data, $id)
    {
        return $this->faculty->editPassword($this->secureDecrypt($data), $id);
    }

    public function toEditFaculty2($data, $id)
    {
        return $this->faculty->editFaculty2($data, $id);
    }

    public function toAddEducDocs()
    {


        $uploadDir = 'uploads/';
        foreach ($_FILES['documents']['name'] as $key => $name) {
            $tmpName = $_FILES['documents']['tmp_name'][$key];
            $filePath = $uploadDir . basename($name);
            if (move_uploaded_file($tmpName, $filePath)) {
                echo "File uploaded successfully: $name\n";
            } else {
                echo "Failed to upload file: $name\n";
            }
        }


        return $_FILES;
    }
}
