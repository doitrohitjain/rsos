<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Helper\CustomHelper;
use App\models\ExamSubject;
use App\models\StudentAllotmentMark;
use App\models\SupplementarySubject;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
use PDF;
use Session;

/* Hall Ticket Addtional Model */

/* Hall Ticket Addtional Model */

class DataSettingController extends Controller
{
    public $custom_component_obj = "";

    function __construct()
    {
        $this->custom_component_obj = new CustomComponent;
    }

    public function setup_student_allotment_marks(Request $request)
    {
        $title = "Setup Students Alloment Marks Data";
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        $updateLimit = 9999999;
        $limit = 9999999;
        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = config("global.CenterAllotmentStreamId");

        $exam_year = 126;
        $exam_month = 2;

        $query = null;
        $callSP = "call enterDataIntoStudentAllotmentMarksNew(" . $exam_year . "," . $exam_month . ");";
        $totalRecords = DB::select($callSP);

        $updateRestulDetailsQ1 = 'SELECT CONCAT("UPDATE rs_student_allotment_marks set is_exclude_for_theory = " , CASE WHEN esub.final_result = 666 THEN "1" else "0" END , " , is_exclude_for_practical = " , CASE WHEN esub.final_result = 777 THEN "1" else "0"  END  , " where id = ", sam.id , ";") as q1 FROM  rs_student_allotment_marks sam INNER JOIN rs_exam_subjects esub ON esub.student_id = sam.student_id AND esub.subject_id = sam.subject_id and esub.deleted_at is null WHERE is_supplementary = 1 ORDER BY esub.student_id LIMIT ' . $updateLimit . ';';
        $result = DB::select($updateRestulDetailsQ1);

        foreach (@$result as $k => $q) {
            $query = $q->q1;
            DB::select($query);
        }
        $q3 = 'SELECT concat( "update rs_student_allotment_marks set final_theory_marks=", es.final_theory_marks, " where id = ", sa.id, ";" ) AS q1, CONCAT( "update rs_student_allotment_marks set final_theory_marks=", es.final_theory_marks, ", theory_absent = ", ( CASE WHEN es.final_theory_marks = 999 THEN 1 END ), " where id = ", sa.id, ";" ) AS q2, concat( "update rs_student_allotment_marks set final_practical_marks=", es.final_theory_marks, " where id = ", sa.id, ";" ) AS q3, CONCAT( "update rs_student_allotment_marks set final_practical_marks=", es.final_theory_marks, ", practical_absent = ", ( CASE WHEN es.final_theory_marks = 999 THEN 1 END ), " where id = ", sa.id, ";" ) AS q4 FROM rs_student_allotment_marks sa INNER JOIN rs_exam_subjects es ON es.student_id = sa.student_id AND es.subject_id = sa.subject_id WHERE sa.is_supplementary = 1 GROUP BY es.student_id,es.subject_id ORDER BY es.exam_year DESC, es.exam_month DESC;';

        $updateRestulDetailsQ2 = "UPDATE rs_student_allotment_marks set final_practical_marks = null;";
        $runData = DB::select($updateRestulDetailsQ2);
        $runData = null;
        $qArr = array();
        $runData = DB::select($q3);
        foreach ($runData as $k => $v) {
            if (!empty($v->q1)) {
                $qArr[] = $v->q1;
            }
            if (!empty($v->q2)) {
                $qArr[] = $v->q2;
            }
            if (!empty($v->q3)) {
                $qArr[] = $v->q3;
            }
            if (!empty($v->q4)) {
                $qArr[] = $v->q4;
            }
        }
        /*foreach(@$qArr as $k => $v){
            $runData = DB::select($v);
        }*/
        echo "<h1>" . $title . " has been Done.</h1>";
        die;
    }


    public function get_running_process_on_db(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        $q1 = "show PROCESSLIST;";
        $restult = DB::select($q1);
        dd($restult);
        echo "<h1>" . $title . " has been Done.</h1>";
        die;
    }

    public function old_setup_student_allotment_marks(Request $request)
    {
        ini_set('memory_limit', '300000M');
        ini_set('max_execution_time', '0');

        $title = "Setup Students Alloment Marks Data";
        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get('global.current_exam_month_id');

        $tableTruncateQ1 = "truncate table `rs_student_allotment_marks`";
        $tableTruncate = DB::select($tableTruncateQ1);

        $custom_component_obj = new CustomComponent;
        $studentIds = $custom_component_obj->getEligibleStudentOfExamYearAndExamMonths();
        $suppStudentIds = $custom_component_obj->getEligibleSuppStudentOfExamYearAndExamMonths();

        $masterExamSubjects = array();
        if (@$suppStudentIds) {
            $masterExamSubjects = ExamSubject::
            join('students', 'exam_subjects.student_id', '=', 'students.id')
                ->whereIn('student_id', $studentIds)->get()->toArray();
        }
        $masterSuppSubjects = array();
        if (@$suppStudentIds) {
            $masterSuppSubjects = SupplementarySubject::
            join('supplementaries', 'supplementary_subjects.student_id', '=', 'supplementaries.student_id')
                ->whereIn('student_id', $suppStudentIds)->get()->toArray();
        }
        $finalList = array_merge($masterExamSubjects, $masterSuppSubjects);
        $subject_name_list = $this->subjectListName();
        $subject_code_list = $this->subjectCodeList();
        foreach (@$finalList as $k => $item) {
            $saveData = null;
            $fld = 'student_id';
            $saveData[$fld] = false;
            if (@$item['supplementary_id']) {
                $saveData[$fld] = true;
            }
            $fld = 'student_id';
            $saveData[$fld] = @$item[$fld];
            $fld = 'enrollment';
            $saveData[$fld] = @$item[$fld];
            $fld = 'student_id';
            $saveData[$fld] = @$item[$fld];
            $fld = 'course';
            $saveData[$fld] = @$item[$fld];
            $fld = 'exam_year';
            $saveData[$fld] = @$exam_year;
            $fld = 'exam_month';
            $saveData[$fld] = @$exam_month;
            $fld = 'subject_id';
            $saveData[$fld] = @$item[$fld];
            $subject_name = @$subject_name_list[@$item[$fld]];
            $fld = 'subject_name';
            $saveData[$fld] = @$$fld;
            $fld = "subject_id";
            $subject_code = @$subject_code_list[@$item[$fld]];
            $fld = 'subject_code';
            $saveData[$fld] = @$$fld;
            StudentAllotmentMark::create($saveData);
            echo "<br>";
            echo "Save for " . $saveData['enrollment'] . " with subject " . $subject_name . " and subject code " . $subject_code;
            echo "<br>";
        }
        echo "<h1>" . $title . " has been Done.</h1>";
        die;
    }


}
	
