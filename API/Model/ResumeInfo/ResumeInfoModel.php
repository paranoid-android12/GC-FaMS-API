<?php
    header('Access-Control-Allow-Origin: http://localhost:4200');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, OPTIONS');
    header("Access-Control-Allow-Headers: *");

    include_once "./Controller/global.php";
    
    class ResumeInfo extends GlobalMethods{
        //Faculty id GET sched
        public function getResumeInfo($id){
            $educAttain = [];
            $certs = [];
            $experience = [];
            $expertise = [];
            $projects = [];

            $educSQL = "SELECT * 
                        FROM `educattainment` 
                        WHERE faculty_ID = $id;";

            $educAttain = $this->executeGetQuery($educSQL)['data'];

            // $certSQL = "SELECT * FROM `certifications-faculty` 
            //             INNER JOIN `certifications` on `certifications-faculty`.cert_ID=`certifications`.cert_ID 
            //             WHERE faculty_ID = $id;";

            $certSQL = "SELECT * FROM `certifications-faculty`
                        WHERE faculty_ID = $id;";
            $certs = $this->executeGetQuery($certSQL)['data'];

        

            $expSQL = "SELECT * FROM `experience-faculty`
                       WHERE faculty_ID = $id;";
            $experience = $this->executeGetQuery($expSQL)['data'];

            $specSQL = "SELECT * FROM `expertise`
                        WHERE faculty_ID = $id;";
            $expertise = $this->executeGetQuery($specSQL)['data'];


            $projSQL = "SELECT * FROM `projects`
                        WHERE faculty_ID = $id;";
            $projects = $this->executeGetQuery($projSQL)['data'];

            return array("educAttainment" => $educAttain, "certifications" => $certs, "industryExp" => $experience, "expertise" => $expertise, "projects" => $projects);
        }

        public function addEduc($form, $id){
            // $sql = "INSERT INTO `educattainment`(faculty_ID, educ_title, educ_school, year_start, year_end educ_details)
            // VALUES (?,?,?,?,?,?)";
            $params = array('faculty_ID','educ_title','educ_school','year_start','year_end','educ_details');
            $tempForm = array($id,
                            $form->educ_title, 
                            $form->educ_school,
                            $form->year_start,
                            $form->year_end,
                            $form->educ_details);
            return $this->prepareAddBind('educattainment', $params, $tempForm);
        }

        public function addExp($form, $id){
            $params = array('faculty_ID','experience_place','experience_title','experience_details','experience_from','experience_to');
            $tempForm = array($id,
                            $form->experience_place, 
                            $form->experience_title,
                            $form->experience_details,
                            $form->experience_from,
                            $form->experience_to);
            return $this->prepareAddBind('experience-faculty', $params, $tempForm);
        }
        public function addCert($form, $id){
            $params = array('faculty_ID','accomplished_date','cert_name','cert_details','cert_corporation');
            $tempForm = array($id,
                            $form->accomplished_date, 
                            $form->cert_name,
                            $form->cert_details,
                            $form->cert_corporation);
            return $this->prepareAddBind('certifications-faculty', $params, $tempForm);
        }

        public function addProj($form, $id){
            $params = array('faculty_ID','project_name','project_date','project_detail','project_link');
            $tempForm = array($id,
                            $form->project_name, 
                            $form->project_date,
                            $form->project_detail,
                            $form->project_link);
            return $this->prepareAddBind('projects', $params, $tempForm);
        }

        public function addSpec($form, $id){
            $params = array('faculty_ID','expertise_name','expertise_confidence');
            $tempForm = array($id,
                            $form->expertise_name, 
                            $form->expertise_confidence);
            return $this->prepareAddBind('expertise', $params, $tempForm);
        }

        public function editEduc($form, $id){
            $params = array('educ_title','educ_school','year_start','year_end','educ_details');
            $tempForm = array($form->educ_title, 
                            $form->educ_school,
                            $form->year_start,
                            $form->year_end,
                            $form->educ_details,
                            $id);
            return $this->prepareEditBind('educattainment', $params, $tempForm, 'educattainment_ID');
        }

        public function editExp($form, $id){
            $params = array('experience_title','experience_place','experience_from','experience_to','experience_details');
            $tempForm = array($form->experience_title, 
                            $form->experience_place,
                            $form->experience_from,
                            $form->experience_to,
                            $form->experience_details,
                            $id);
            return $this->prepareEditBind('experience-faculty', $params, $tempForm, 'experience_ID');
        }

        public function editCert($form, $id){
            $params = array('accomplished_date','cert_name','cert_details','cert_corporation');
            $tempForm = array($form->accomplished_date, 
                            $form->cert_name,
                            $form->cert_details,
                            $form->cert_corporation,
                            $id);
            return $this->prepareEditBind('certifications-faculty', $params, $tempForm, 'cert_ID');
        }

        public function editProj($form, $id){
            $params = array('project_name','project_date','project_detail','project_link');
            $tempForm = array($form->project_name, 
                            $form->project_date,
                            $form->project_detail,
                            $form->project_link,
                            $id);
            return $this->prepareEditBind('projects', $params, $tempForm, 'project_ID');
        }

        public function editSpec($form, $id){
            $params = array('expertise_name','expertise_confidence');
            $tempForm = array($form->expertise_name, 
                            $form->expertise_confidence,
                            $id);
            return $this->prepareEditBind('expertise', $params, $tempForm, 'expertise_ID');
        }

        public function deleteEduc($id){
            return $this->prepareDeleteBind('educattainment', 'educattainment_ID', $id);
        }

        public function deleteExp($id){
            return $this->prepareDeleteBind('experience-faculty', 'experience_ID', $id);
        }

        public function deleteCert($id){
            return $this->prepareDeleteBind('certifications-faculty', 'cert_ID', $id);
        }

        public function deleteProj($id){
            return $this->prepareDeleteBind('projects', 'project_ID', $id);
        }

        public function deleteSpec($id){
            return $this->prepareDeleteBind('expertise', 'expertise_ID', $id);
        }
    }
?>