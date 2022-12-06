<?php

namespace Src\Controller;

use Src\Controller\ExposeDataController;

class AdminController extends ExposeDataController
{

    public function fetchPrograms(int $type)
    {
        $param = array();
        if ($type != 0) {
            $query = "SELECT * FROM programs WHERE `type` = :t";
            $param = array(':t' => $type);
        } else {
            $query = "SELECT * FROM programs";
        }
        return $this->getData($query, $param);
    }

    public function fetchApplicants($country, $type, $program)
    {
        $WHERE = "";

        if ($country != "All") {
            $WHERE .= " AND p.`nationality` = '$country'";
        }
        if ($type != "All") {
            $WHERE .= " AND f.`id` = $type";
        }
        if ($program != "All") {
            $WHERE .= " AND r.`first_prog` LIKE '%$program%'";
        }

        $query = "SELECT a.`id`, p.`first_name`, p.`last_name`, p.`nationality`, f.`name` AS `app_type`, r.`first_prog`, fs.`declaration` 
                FROM `personal_information` AS p, `applicants_login` AS a, `form_type` AS f, 
                `purchase_detail` AS d, `program_info` AS r, `form_sections_chek` AS fs  
                WHERE p.`app_login` = a.`id` AND d.`form_type` = f.`id` AND d.`id` = a.`purchase_id` AND 
                r.`app_login` = a.`id` AND fs.`app_login` = a.`id`$WHERE";
        return $this->getData($query);
    }

    public function fetchAllApplicants()
    {
        $query = "SELECT a.`id`, p.`first_name`, p.`middle_name`, p.`last_name`, p.`nationality`, f.`name` AS `app_type`, r.`first_prog`, fs.`declaration` 
                FROM `personal_information` AS p, `applicants_login` AS a, `form_type` AS f, 
                `purchase_detail` AS d, `program_info` AS r, `form_sections_chek` AS fs  
                WHERE p.`app_login` = a.`id` AND d.`form_type` = f.`id` AND d.`id` = a.`purchase_id` AND 
                r.`app_login` = a.`id` AND fs.`app_login` = a.`id`";
        return $this->getData($query);
    }

    public function fetchTotalApplications()
    {
        $query = "SELECT COUNT(*) AS total 
                FROM purchase_detail AS p, admission_period AS a, form_sections_chek AS f, applicants_login AS l  
                WHERE a.id = p.admission_period AND a.active = 1 AND f.app_login = l.id AND l.purchase_id = p.id";
        return $this->getData($query);
    }

    public function fetchTotalSubmittedOrUnsubmittedApps(bool $submitted = true)
    {
        $query = "SELECT COUNT(*) AS total 
                FROM purchase_detail AS p, admission_period AS a, form_sections_chek AS f, applicants_login AS l  
                WHERE a.id = p.admission_period AND a.active = 1 AND f.app_login = l.id AND l.purchase_id = p.id 
                AND f.declaration = :s";
        return $this->getData($query, array(":s" => (int) $submitted));
    }

    public function fetchTotalAdmittedApplicants()
    {
        $query = "SELECT COUNT(*) AS total 
                FROM form_sections_chek AS f, applicants_login as l, admission_period AS p, purchase_detail AS d 
                WHERE d.`id` = l.`purchase_id` AND d.`admission_period` = p.`id` AND l.`id` = f.`app_login` AND 
                p.`active` = 1 AND f.`admitted` = 1";
        return $this->getData($query);
    }

    public function getAllApplicantsID($certificate, $progCategory)
    {
        $query = "SELECT l.`id`, p.`first_name`, p.`middle_name`, p.`last_name`, p.`email_addr`, i.`$progCategory` AS programme
                FROM 
                    `personal_information` AS p, `academic_background` AS a, 
                    `applicants_login` AS l, `form_sections_chek` AS f, `program_info` AS i 
                WHERE 
                    p.`app_login` = l.`id` AND a.`app_login` = l.`id` AND 
                    f.`app_login` = l.`id` AND i.`app_login` = l.`id` AND
                    a.`awaiting_result` = 0 AND f.`declaration` = 1 AND f.`admitted` = 0";
        $param = array();
        if (strtolower($certificate) != "all") {
            $query .= " AND a.`cert_type` = :c";
            $param = array(":c" => $certificate);
        }

        return $this->getData($query, $param);
    }

    public function getAppCourseSubjects(int $loginID)
    {
        $query = "SELECT 
                    r.`type`, r.`subject`, r.`grade` 
                FROM 
                    academic_background AS a, high_school_results AS r, applicants_login AS l
                WHERE 
                    l.`id` = a.`app_login` AND r.`acad_back_id` = a.`id` AND a.`id` = :i";
        return $this->getData($query, array(":i" => $loginID));
    }

    public function fetchBroadsheetData($certificate, $progCategory)
    {
        $allAppData = $this->getAllApplicantsID($certificate, $progCategory);
        if (empty($allAppData)) return 0;

        $data = [];

        foreach ($allAppData as  $appData) {
            $applicant = [];
            $subjs = $this->getAppCourseSubjects($appData["id"]);
            $applicant["app_pers"] = $appData;
            $applicant["sch_rslt"] = $subjs;
            array_push($data, $applicant);
        }

        return $data;
    }

    public function admitCatA()
    {
    }

    public function admitByCat($bs_data, $category, $certificate)
    {
        $final_result = [];

        if ($category == 'A') {

            // set all qualified grades
            $grade_range = array(
                array('grade' => 'A1', 'score' => 1),
                array('grade' => 'B2', 'score' => 2),
                array('grade' => 'B3', 'score' => 3),
                array('grade' => 'C4', 'score' => 4),
                array('grade' => 'C5', 'score'  => 5),
                array('grade' => 'C6', 'score'  => 6),
                array('grade' => 'A', 'score' => 1),
                array('grade' => 'B', 'score' => 2),
                array('grade' => 'C', 'score' => 3),
                array('grade' => 'D', 'score' => 4)
            );

            foreach ($bs_data as $data) {

                $total_core_score = 0;
                $required_core_passed = 0;
                $any_one_core_passed = 0;
                $any_one_core_score = 7;

                $any_three_elective_passed = 0;
                $total_elective_score = 0;
                $any_three_elective_scores = [];

                foreach ($data["sch_rslt"] as $result) {

                    $score = 7;
                    for ($i = 0; $i < count($grade_range); $i++) {
                        if ($result["grade"] == $grade_range[$i]["grade"]) {
                            $score = $grade_range[$i]['score'];
                        }
                    }

                    if ($result["type"] == "core") {
                        if ($result["subject"] == "CORE MATHEMATICS" || $result["subject"] == "ENGLISH LANGUAGE") {
                            if ($score != 7) {
                                $required_core_passed += 1;
                                $total_core_score += $score;
                            }
                        } else {
                            if ($score < $any_one_core_score) {
                                if (!empty($any_one_core_passed)) $total_core_score -= $any_one_core_score;
                                if (empty($any_one_core_passed)) $any_one_core_passed += 1;
                                $total_core_score += $score;
                                $any_one_core_score = $score;
                            }
                        }
                    }

                    if ($result["type"] == "elective") {
                        if ($score != 7) {
                            $any_three_elective_passed += 1;
                            array_push($any_three_elective_scores, $score);
                        }
                    }
                }

                $array_before_sort = $any_three_elective_scores;
                asort($any_three_elective_scores);
                $array_with_new_values = array_values($any_three_elective_scores);
                $any_three_elective_scores = array_values($any_three_elective_scores);
                if (count($any_three_elective_scores) > 3) unset($any_three_elective_scores[count($any_three_elective_scores) - 1]);
                $total_elective_score = array_sum($any_three_elective_scores);

                $feed["total_core_score"] = $total_core_score;
                $feed["total_elective_score"] = $total_elective_score;
                $feed["total_score"] = $total_core_score + $total_elective_score;
                $feed["required_core_passed"] = $required_core_passed;
                $feed["any_one_core_passed"] = $any_one_core_passed;
                $feed["any_one_core_score"] = $any_one_core_score;
                $feed["any_three_elective_passed"] = $any_three_elective_passed;
                $feed["any_three_elective_scores"] = $any_three_elective_scores;
                $feed["array_before_sort"] = $array_before_sort;
                $feed["array_with_new_values"] = $array_with_new_values;

                $app_result["id"] = $data["app_pers"]["id"];
                $app_result["data"] = $feed;
                $app_result["admitted"] = false;
                $app_result["email_sent_status"] = false;

                // Admit applicant
                if ($required_core_passed == 2 && $any_one_core_passed > 0 && $any_three_elective_passed >= 3) {
                    if ($feed["total_score"] <= 36 && ($certificate == 'WASSCE' || $certificate == 'NECO')) {
                        $query = "UPDATE `form_sections_chek` SET `admitted` = 1 WHERE `app_login` = :i";
                        if ($this->getData($query, array(":i" => $app_result["id"]))) {
                            $app_result["admitted"] = true;
                            $subject = "ADMISSIONS";
                            $full_name = !empty($bs_data["app_pers"]["middle_name"])
                                ? $bs_data["app_pers"]["first_name"] . " " . $bs_data["app_pers"]["middle_name"] . " " . $bs_data["app_pers"]["last_name"]
                                : $bs_data["app_pers"]["first_name"] . " " . $bs_data["app_pers"]["last_name"];
                            $message = "Congratulations " . $full_name . "! <br> You have been offered admission at Regional Maritime University to study "
                                . $bs_data["app_pers"]['programme'];
                            if ($this->sendEmail(strtolower($bs_data["app_pers"]["email_address"]), $subject, $message)) {
                                $app_result["email_sent_status"] = true;
                            }
                        }
                    }
                }

                array_push($final_result, $app_result);
            }
        }

        if ($category == 'B') {
            foreach ($bs_data as $data) {
            }
        }

        if ($category == 'C') {
            foreach ($bs_data as $data) {
            }
        }

        if ($category == 'D') {
            foreach ($bs_data as $data) {
            }
        }
        return $final_result;
    }

    public function admitQualifiedStudents($certificate, $progCategory)
    {
        $bs_data = $this->fetchBroadsheetData($certificate, $progCategory);

        $qualifications = array(
            "A" => array('WASSCE', 'SSSCE', 'GBCE', 'NECO'),
            "B" => array('GCE', "GCE 'A' Level", "GCE 'O' Level"),
            "C" => array('HND'),
            "D" => array('IB', 'International Baccalaureate', 'Baccalaureate'),
        );

        // check program group
        if (in_array($certificate, $qualifications['A'])) {
            $total_catA_admitted = $this->admitByCat($bs_data, 'A', $certificate);
        }

        if (in_array($certificate, $qualifications['B'])) {
            $total_catA_admitted = $this->admitByCat($bs_data, 'B', $certificate);
        }

        if (in_array($certificate, $qualifications['C'])) {
            $total_catA_admitted = $this->admitByCat($bs_data, 'C', $certificate);
        }

        if (in_array($certificate, $qualifications['D'])) {
            $total_catA_admitted = $this->admitByCat($bs_data, 'D', $certificate);
        }

        return array("success" => true, "message" => $total_catA_admitted);
    }
}
