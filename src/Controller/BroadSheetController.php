<?php

namespace Src\Controller;

use Src\Controller\ExposeDataController;

class BroadSheetController extends ExposeDataController
{
    private $total_core_score = 0;
    private $required_core_passed = 0;
    private $any_one_core_passed = 0;
    private $any_one_core_score = 7;

    private $any_three_elective_passed = 0;
    private $total_elective_score = 0;
    private $any_three_elective_scores = [];

    private $school_records = null;

    private $grade_range = array(
        array(
            'type' => array('WASSCE', 'NECO'),
            'system' => array(
                array('grade' => 'A1', 'score' => 1),
                array('grade' => 'B2', 'score' => 2),
                array('grade' => 'B3', 'score' => 3),
                array('grade' => 'C4', 'score' => 4),
                array('grade' => 'C5', 'score' => 5),
                array('grade' => 'C6', 'score' => 6),
            )
        ),
        array(
            'type' => array('SSSCE', 'GBCE'),
            'system' => array(
                array('grade' => 'A', 'score' => 1),
                array('grade' => 'B', 'score' => 2),
                array('grade' => 'C', 'score' => 3),
                array('grade' => 'D', 'score' => 4),
            )
        )
    );

    public function __constuct($certificate, $progCategory)
    {
        $this->certificate_type = $certificate;
        $this->program_category = $progCategory;
    }

    public function getAllApplicantsID($certificate, $progCategory)
    {
        $query = "SELECT l.`id`, p.`first_name`, p.`middle_name`, p.`last_name`, i.`$progCategory` AS programme
                FROM 
                    `personal_information` AS p, `academic_background` AS a, 
                    `applicants_login` AS l, `form_sections_chek` AS f, `program_info` AS i 
                WHERE 
                    p.`app_login` = l.`id` AND a.`app_login` = l.`id` AND 
                    f.`app_login` = l.`id` AND i.`app_login` = l.`id` AND
                    a.`awaiting_result` = 0 AND a.`cert_type` = :c";
        return $this->getData($query, array(":c" => $certificate));
    }

    public function getApplicantsSubjects(int $loginID)
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
            $subjs = $this->getApplicantsSubjects($appData["id"]);
            $applicant["app_pers"] = $appData;
            $applicant["sch_rslt"] = $subjs;
            array_push($data, $applicant);
        }

        return $data;
    }

    public function admitByCat($bs_data, $category)
    {
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

                    $score = 0;
                    for ($i = 0; $i < count($grade_range); $i++) {
                        if ($result["grade"] == $grade_range[$i]["grade"]) {
                            $score = $grade_range[$i]['score'];
                        }
                    }

                    if ($result["type"] == "core") {
                        if ($result["subject"] == "CORE MATHEMATICS" || $result["subject"] == "ENGLISH LANGUAGE") {
                            if ($score) $required_core_passed += 1;
                            $total_core_score += $score;
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
                        $any_three_elective_passed += 1;
                        array_push($any_three_elective_scores, $score);
                        if ($any_three_elective_passed == 4) {
                            asort($any_three_elective_scores);
                            $any_three_elective_scores = array_values($any_three_elective_scores);
                            unset($any_three_elective_scores[count($any_three_elective_scores) - 1]);
                            $total_elective_score = array_sum($any_three_elective_scores);
                        }
                    }
                }

                $feed["total_core_score"] = $total_core_score;
                $feed["total_elective_score"] = $total_elective_score;
                $feed["total_score"] = $total_core_score + $total_elective_score;
                $feed["required_core_passed"] = $required_core_passed;
                $feed["any_one_core_passed"] = $any_one_core_passed;
                $feed["any_one_core_score"] = $any_one_core_score;
                $feed["any_three_elective_passed"] = $any_three_elective_passed;

                return $feed;
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
        return $bs_data;
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
            $total_catA_admitted = $this->admitByCat($bs_data, 'A');
        }

        if (in_array($certificate, $qualifications['B'])) {
            $total_catA_admitted = $this->admitByCat($bs_data, 'B');
        }

        if (in_array($certificate, $qualifications['C'])) {
            $total_catA_admitted = $this->admitByCat($bs_data, 'C');
        }

        if (in_array($certificate, $qualifications['D'])) {
            $total_catA_admitted = $this->admitByCat($bs_data, 'D');
        }

        return array("success" => true, "message" => $total_catA_admitted);
    }
}
