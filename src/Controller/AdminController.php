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
        $query = "SELECT l.`id`, p.`first_name`, p.`middle_name`, p.`last_name`, 
                    p.`email_addr`, i.`$progCategory` AS programme, a.`cert_type` 
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

    public function getAppProgDetails($program)
    {
        $query = "SELECT `id`, `type`, `group` FROM programs WHERE `name` = :p";
        return $this->getData($query, array(":p" => $program));
    }

    public function fetchBroadsheetData($certificate, $progCategory)
    {
        $allAppData = $this->getAllApplicantsID($certificate, $progCategory);
        if (empty($allAppData)) return 0;

        $data = [];

        foreach ($allAppData as  $appData) {
            $applicant = [];
            $applicant["app_pers"] = $appData;
            $applicant["app_pers"]["prog_category"] = $progCategory;
            $subjs = $this->getAppCourseSubjects($appData["id"]);
            $applicant["sch_rslt"] = $subjs;
            $progs = $this->getAppProgDetails($appData["programme"]);
            $applicant["prog_info"] = $progs;
            array_push($data, $applicant);
        }

        return $data;
    }

    /*
    * Admit applicants in groups by their certificate category
    */
    public function admitApplicantsByCertCat($data, $qualifications)
    {
        $final_result = [];

        foreach ($data as $std_data) {
            if (in_array($std_data["app_pers"]["cert_type"], $qualifications["A"])) {
                array_push($final_result, $this->admitByCatA($std_data));
                continue;
            }
            if (in_array($std_data["app_pers"]["cert_type"], $qualifications["B"])) {
                array_push($final_result, $this->admitByCatB($std_data));
                continue;
            }
            if (in_array($std_data["app_pers"]["cert_type"], $qualifications["C"])) {
                array_push($final_result, $this->admitByCatC($std_data));
                continue;
            }
            if (in_array($std_data["app_pers"]["cert_type"], $qualifications["D"])) {
                array_push($final_result, $this->admitByCatD($std_data));
                continue;
            }
        }
        return $final_result;
    }

    public function admitCatAApplicant($app_result, $prog_choice, $cert_type)
    {

        $qualified = false;
        // Admit applicant
        if ($app_result["feed"]["required_core_passed"] == 2 && $app_result["feed"]["any_one_core_passed"] > 0 && $app_result["feed"]["any_three_elective_passed"] >= 3) {

            if (in_array($cert_type, ["SSSCE", "NECO", "GBCE"]) && $app_result["feed"]["total_score"] <= 24) {
                $qualified = true;
            }

            if (in_array($cert_type, ["WASSCE"]) && $app_result["feed"]["total_score"] <= 36) {
                $qualified = true;
            }

            if ($qualified) {
                $query = "UPDATE `form_sections_chek` SET `admitted` = 1, `$prog_choice` = 1 WHERE `app_login` = :i";
                $this->getData($query, array(":i" => $app_result["id"]));
                return $qualified;
            }
        } else {
            $query = "UPDATE `form_sections_chek` SET`admitted` = 0,  `$prog_choice` = 1 WHERE `app_login` = :i";
            $this->getData($query, array(":i" => $app_result["id"]));
            return $qualified;
        }
    }

    public function admitByCatA($data)
    {

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
        $app_result["feed"] = $feed;
        $app_result["admitted"] = false;
        $app_result["emailed"] = false;

        $prog_choice = $data["app_pers"]["prog_category"] . "_qualified";

        $app_result["admitted"] = $this->admitCatAApplicant($app_result, $prog_choice, $data["app_pers"]["cert_type"]);
        // Admit applicant

        $subject = "RMU ADMISSIONS";
        $full_name = !empty($data["app_pers"]["middle_name"])
            ? $data["app_pers"]["first_name"] . " " . $data["app_pers"]["middle_name"] . " " . $data["app_pers"]["last_name"]
            : $data["app_pers"]["first_name"] . " " . $data["app_pers"]["last_name"];
        $message = "Congratulations " . $full_name . "! <br> You have been offered admission at Regional Maritime University to study "
            . $data["app_pers"]['programme'] . ". Please follow the link <a href='https://admissions.rmuictonline.com/apply/'>here</a> to complete process. ";

        if ($this->sendEmail(strtolower($data["app_pers"]["email_addr"]), $subject, $message)) {
            $app_result["emailed"] = true;
        }

        return $app_result;
    }

    public function admitByCatB($bs_data)
    {
        $final_result = [];

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

        return $final_result;
    }

    public function admitByCatC($bs_data)
    {
        $final_result = [];

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

        return $final_result;
    }

    public function admitByCatD($bs_data)
    {
        $final_result = [];

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

        return $final_result;
    }

    /*
    * Admit applicants in groups by their certificate category
    */
    /*public function admitStudentsByCertCat($cat_A_stds_data, $qualify_cat, $certificate)
    {
        $final_result = [];
        switch ($qualify_cat) {
            case 'A':
                foreach ($cat_A_stds_data as $data) {
                    array_push($final_result, $this->admitByCatA($data, $certificate));
                }
                break;
            case 'B':
                foreach ($cat_A_stds_data as $data) {
                    array_push($final_result, $this->admitByCatB($data, $certificate));
                }
                break;
            case 'C':
                foreach ($cat_A_stds_data as $data) {
                    array_push($final_result, $this->admitByCatC($data, $certificate));
                }
                break;
            case 'D':
                foreach ($cat_A_stds_data as $data) {
                    array_push($final_result, $this->admitByCatD($data, $certificate));
                }
                break;

            default:
                $final_result["message"] = "No match found for this category";
                break;
        }
        return $final_result;
    }*/

    public function admitQualifiedStudents($certificate, $progCategory)
    {
        $students_bs_data = $this->fetchBroadsheetData($certificate, $progCategory);

        if (!empty($students_bs_data)) {
            $qualifications = array(
                "A" => array('WASSCE', 'SSSCE', 'GBCE', 'NECO'),
                "B" => array('GCE', "GCE 'A' Level", "GCE 'O' Level"),
                "C" => array('HND'),
                "D" => array('IB', 'International Baccalaureate', 'Baccalaureate'),
            );

            return $this->admitApplicantsByCertCat($students_bs_data, $qualifications);
        }

        return 0;
    }
}
