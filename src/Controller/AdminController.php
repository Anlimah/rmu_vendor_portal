<?php

namespace Src\Controller;

use Src\Controller\ExposeDataController;

class AdminController extends ExposeDataController
{

    public function getAcademicPeriod()
    {
        $query = "SELECT YEAR(`start_date`) AS start_year, YEAR(`end_date`) AS end_year, info 
                FROM admission_period WHERE active = 1";
        return $this->getData($query);
    }

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

    public function fetchAvailableformTypes()
    {
        return $this->getData("SELECT * FROM form_type");
    }

    public function getFormTypeName(int $form_type)
    {
        $query = "SELECT * FROM form_type WHERE id = :i";
        return $this->getData($query, array(":i" => $form_type));
    }

    /**
     * Fetching forms sale data totals
     */

    public function fetchTotalFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
                FROM 
                    purchase_detail AS pd, form_type AS ft, form_price AS fp, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_type = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1";
        return $this->getData($query);
    }

    public function fetchTotalPostgradsFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, form_type AS ft, form_price AS fp, 
            admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_type = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
            AND ft.name LIKE '%Post%' OR ft.name LIKE '%Master%'";
        return $this->getData($query);
    }

    public function fetchTotalUdergradsFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, form_type AS ft, form_price AS fp, 
            admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_type = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
            AND (ft.name LIKE '%Degree%' OR ft.name LIKE '%Diploma%')";
        return $this->getData($query);
    }

    public function fetchTotalShortCoursesFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, form_type AS ft, form_price AS fp, 
            admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_type = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
            AND ft.name LIKE '%Short%'";
        return $this->getData($query);
    }

    public function fetchTotalVendorsFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, form_type AS ft, form_price AS fp, 
            admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_type = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
            AND v.vendor_name NOT LIKE '%ONLINE%'";
        return $this->getData($query);
    }

    public function fetchTotalOnlineFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, form_type AS ft, form_price AS fp, 
            admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_type = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
            AND v.vendor_name LIKE '%ONLINE%'";
        return $this->getData($query);
    }

    /**
     * Fetching form sales data by statistics
     */
    public function fetchFormsSoldStatsByVendor()
    {
        $query = "SELECT 
                    v.vendor_name, COUNT(pd.id) AS total, SUM(fp.amount) AS amount 
                FROM 
                    purchase_detail AS pd, form_type AS ft, form_price AS fp, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_type = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
                GROUP BY pd.vendor";
        return $this->getData($query);
    }

    public function fetchFormsSoldStatsByPaymentMethod()
    {
        $query = "SELECT 
                    pd.payment_method, COUNT(pd.id) AS total, SUM(fp.amount) AS amount 
                FROM 
                    purchase_detail AS pd, form_type AS ft, form_price AS fp, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_type = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
                GROUP BY pd.payment_method";
        return $this->getData($query);
    }

    public function fetchFormsSoldStatsByFormType()
    {
        $query = "SELECT 
                    ft.name, COUNT(pd.id) AS total, SUM(fp.amount) AS amount 
                FROM 
                    purchase_detail AS pd, form_type AS ft, form_price AS fp, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_type = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
                GROUP BY pd.form_type";
        return $this->getData($query);
    }

    public function fetchFormsSoldStatsByCountry()
    {
        $query = "SELECT 
                    pd.country_name, pd.country_code, COUNT(pd.id) AS total, SUM(fp.amount) AS amount 
                FROM 
                    purchase_detail AS pd, form_type AS ft, form_price AS fp, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_type = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
                GROUP BY pd.country_code";
        return $this->getData($query);
    }

    public function fetchFormsSoldStatsByPurchaseStatus()
    {
        $query = "SELECT 
                    pd.status, COUNT(pd.id) AS total, SUM(fp.amount) AS amount 
                FROM 
                    purchase_detail AS pd, form_type AS ft, form_price AS fp, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_type = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
                GROUP BY pd.status";
        return $this->getData($query);
    }

    /**
     * fetching applicants data
     */

    public function fetchAppsSummaryData($data)
    {
        // extract the array values into variables
        // create a new array with the keys of $data as the values and the values of $data as the keys
        // and then extract the values of the new array into variables
        extract(array_combine(array_keys($data), array_values($data)));

        $SQL_COND = "AND ";
        if ($country != "All") $SQL_COND .= " AND p.`nationality` = '$country'";
        if ($form_type != "All") $SQL_COND .= " AND ft.`id` = $form_type";
        if ($program != "All") $SQL_COND .= " AND pi.`first_prog` = '$program' OR pi.`second_prog` = '$program'";

        $SQL_COND;

        $result = array();
        switch ($action) {
            case 'apps-total':
                $result = $this->fetchAllApplication($SQL_COND);
                break;
            case 'apps-submitted':
                $result = $this->fetchAllSubmittedOrUnsubmittecApplication(true, $SQL_COND);
                break;

            case 'apps-in-progress':
                $result = $this->fetchAllSubmittedOrUnsubmittecApplication(false, $SQL_COND);
                break;

            case 'apps-admitted':
                $result = $this->fetchAllAdmittedOrUnAdmittedApplication(true, $SQL_COND);
                break;

            case 'apps-unadmitted':
                $result = $this->fetchAllAdmittedOrUnAdmittedApplication(false, $SQL_COND);
                break;

            case 'apps-awaiting':
                $result = $this->fetchAllAwaitingApplication($SQL_COND);
                break;
        }
        return $result;
    }

    public function fetchAllApplication($SQL_COND)
    {
        $query = "SELECT 
                    al.id, CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) AS fullname, 
                    p.nationality, ft.name AS app_type, pi.first_prog, pi.second_prog, fs.declaration 
                FROM 
                    personal_information AS p, applicants_login AS al, 
                    form_type AS ft, purchase_detail AS pd, program_info AS pi, 
                    form_sections_chek AS fs, admission_period AS ap 
                WHERE 
                    p.app_login = al.id AND pi.app_login = al.id AND fs.app_login = al.id AND
                    pd.admission_period = ap.id AND pd.form_type = ft.id AND pd.id = al.purchase_id AND 
                    ap.active = 1$SQL_COND";
        return $this->getData($query);
    }

    public function fetchAllSubmittedOrUnsubmittecApplication(bool $submitted, $SQL_COND)
    {
        $query = "SELECT 
                    a.id, CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) AS fullname, 
                    p.nationality, ft.name AS app_type, pi.first_prog, pi.second_prog, fs.declaration 
                FROM 
                    personal_information AS p, applicants_login AS a, 
                    form_type AS ft, purchase_detail AS pd, program_info AS pi, 
                    form_sections_chek AS fs, admission_period AS ap, academic_background AS ab 
                WHERE 
                    p.app_login = a.id AND pi.app_login = a.id AND fs.app_login = a.id AND ab.app_login = a.id AND
                    pd.admission_period = ap.id AND pd.form_type = ft.id AND pd.id = a.purchase_id AND 
                    ap.active = 1 AND fs.declaration = :s$SQL_COND";
        return $this->getData($query, array(":s" => (int) $submitted));
    }

    public function fetchAllAdmittedOrUnAdmittedApplication(bool $admitted, $SQL_COND)
    {
        $query = "SELECT 
                    a.id, CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) AS fullname, 
                    p.nationality, ft.name AS app_type, pi.first_prog, pi.second_prog, fs.declaration 
                FROM 
                    personal_information AS p, applicants_login AS a, 
                    form_type AS ft, purchase_detail AS pd, program_info AS pi, 
                    form_sections_chek AS fs, admission_period AS ap, academic_background AS ab 
                WHERE 
                    p.app_login = a.id AND pi.app_login = a.id AND fs.app_login = a.id AND ab.app_login = a.id AND
                    pd.admission_period = ap.id AND pd.form_type = ft.id AND pd.id = a.purchase_id AND 
                    ap.active = 1 AND fs.admitted = :s$SQL_COND";
        return $this->getData($query, array(":s" => (int) $admitted));
    }

    public function fetchAllAwaitingApplication($SQL_COND)
    {
        $query = "SELECT 
                    a.id, CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) AS fullname, 
                    p.nationality, ft.name AS app_type, pi.first_prog, pi.second_prog, fs.declaration 
                FROM 
                    personal_information AS p, applicants_login AS a, 
                    form_type AS ft, purchase_detail AS pd, program_info AS pi, 
                    form_sections_chek AS fs, admission_period AS ap, academic_background AS ab 
                WHERE 
                    p.app_login = a.id AND pi.app_login = a.id AND fs.app_login = a.id AND ab.app_login = a.id AND
                    pd.admission_period = ap.id AND pd.form_type = ft.id AND pd.id = a.purchase_id AND 
                    ap.active = 1 AND fs.declaration = 1 AND ab.awaiting_result = 1$SQL_COND";
        return $this->getData($query);
    }

    public function fetchTotalApplications(int $form_type = 100)
    {
        if ($form_type == 100) {
            $query = "SELECT 
                    COUNT(*) AS total 
                FROM 
                    purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, form_type AS ft
                WHERE 
                    ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id 
                    AND pd.form_type = ft.id";
            return $this->getData($query);
        } else {
            $query = "SELECT 
                    COUNT(*) AS total 
                FROM 
                    purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, form_type AS ft
                WHERE 
                    ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id 
                    AND pd.form_type = ft.id AND ft.id = :f";
            return $this->getData($query, array(":f" => $form_type));
        }
    }

    public function fetchTotalSubmittedOrUnsubmittedApps(int $form_type, bool $submitted = true)
    {
        $query = "SELECT COUNT(*) AS total 
                FROM 
                    purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, form_type AS ft
                WHERE 
                    ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id 
                AND pd.form_type = ft.id AND fc.declaration = :s AND ft.id = :f";
        return $this->getData($query, array(":s" => (int) $submitted, ":f" => $form_type));
    }

    public function fetchTotalAdmittedOrUnadmittedApplicants(int $form_type, bool $admitted = true)
    {
        $query = "SELECT COUNT(*) AS total 
                FROM purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, form_type AS ft
                WHERE ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id AND 
                pd.form_type = ft.id AND fc.`admitted` = :s AND ft.id = :f";
        return $this->getData($query, array(":s" => (int) $admitted, ":f" => $form_type));
    }

    public function fetchTotalAwaitingResultsByFormType(int $form_type)
    {
        $query = "SELECT COUNT(*) AS total 
                FROM purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, form_type AS ft, 
                academic_background AS ab 
                WHERE ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id AND 
                ab.app_login = al.id AND pd.form_type = ft.id AND fc.`declaration` = 1 AND ab.`awaiting_result` = 1 AND ft.id = :f";
        return $this->getData($query, array(":f" => $form_type));
    }

    public function fetchTotalAwaitingResults()
    {
        $query = "SELECT COUNT(*) AS total 
                FROM purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, form_type AS ft, 
                academic_background AS ab 
                WHERE ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id AND 
                ab.app_login = al.id AND pd.form_type = ft.id AND fc.`declaration` = 1 AND ab.`awaiting_result` = 1";
        return $this->getData($query);
    }

    public function getAllAdmitedApplicants($cert_type)
    {
        $in_query = "";
        if (in_array($cert_type, ["WASSCE", "NECO"])) $in_query = "AND ab.cert_type IN ('WASSCE', 'NECO')";
        if (in_array($cert_type, ["SSSCE", "GBCE"])) $in_query = "AND ab.cert_type IN ('SSSCE', 'GBCE')";
        if (in_array($cert_type, ["BACCALAUREATE"])) $in_query = "AND ab.cert_type IN ('BACCALAUREATE')";

        $query = "SELECT a.`id`, p.`first_name`, p.`middle_name`, p.`last_name`, pg.name AS programme, b.program_choice 
                FROM `personal_information` AS p, `applicants_login` AS a, broadsheets AS b, programs AS pg,  academic_background AS ab  
                WHERE p.app_login = a.id AND b.app_login = a.id AND ab.app_login = a.id AND pg.id = b.program_id AND 
                a.id IN (SELECT b.app_login AS id FROM broadsheets AS b) $in_query";
        return $this->getData($query);
    }

    public function getAllUnadmitedApplicants($certificate, $progCategory)
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

    public function bundleApplicantsData($data, $prog_category = "")
    {
        $store = [];
        foreach ($data as  $appData) {
            if ($prog_category == "") $prog_category = $appData["program_choice"];
            $applicant = [];
            $applicant["app_pers"] = $appData;
            $applicant["app_pers"]["prog_category"] = $prog_category;
            $subjs = $this->getAppCourseSubjects($appData["id"]);
            $applicant["sch_rslt"] = $subjs;
            $progs = $this->getAppProgDetails($appData["programme"]);
            $applicant["prog_info"] = $progs;
            array_push($store, $applicant);
        }
        return $store;
    }

    public function fetchAllUnadmittedApplicantsData($certificate, $progCategory)
    {
        $allAppData = $this->getAllUnadmitedApplicants($certificate, $progCategory);
        if (empty($allAppData)) return 0;

        $store = $this->bundleApplicantsData($allAppData, $progCategory);
        return $store;
    }

    public function fetchAllAdmittedApplicantsData($cert_type)
    {
        $allAppData = $this->getAllAdmitedApplicants($cert_type);
        if (empty($allAppData)) return 0;

        $store = $this->bundleApplicantsData($allAppData);
        return $store;
    }

    public function saveAdmittedApplicantData(int $admin_period, int $appID, int $program_id, $admitted_data, $prog_choice)
    {
        if (empty($appID) || empty($admin_period) || empty($program_id) || empty($admitted_data)) return 0;

        $query = "INSERT INTO `broadsheets` (`admin_period`,`app_login`,`program_id`,
                `required_core_passed`,`any_one_core_passed`,`total_core_score`,`any_three_elective_passed`,
                `total_elective_score`,`total_score`,`program_choice`) 
                VALUES (:ap, :al, :pi, :rcp, :aocp, :tcs, :atep, :tes, :ts, :pc)";
        $params = array(
            ":ap" => $admin_period,
            ":al" => $appID,
            ":pi" => $program_id,
            ":rcp" => $admitted_data["required_core_passed"],
            ":aocp" => $admitted_data["any_one_core_passed"],
            ":tcs" => $admitted_data["total_core_score"],
            ":atep" => $admitted_data["any_three_elective_passed"],
            ":tes" => $admitted_data["total_elective_score"],
            ":ts" => $admitted_data["total_score"],
            ":pc" => $prog_choice
        );
        $this->inputData($query, $params);
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
        //$app_result["emailed"] = false;

        $prog_choice = $data["app_pers"]["prog_category"] . "_qualified";

        $app_result["admitted"] = $this->admitCatAApplicant($app_result, $prog_choice, $data["app_pers"]["cert_type"]);
        $admin_period = $this->getCurrentAdmissionPeriodID();

        if ($app_result["admitted"]) {
            $this->saveAdmittedApplicantData($admin_period, $app_result["id"], $data["prog_info"][0]["id"], $app_result["feed"], $data["app_pers"]["prog_category"]);
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
        $students_bs_data = $this->fetchAllUnadmittedApplicantsData($certificate, $progCategory);

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
