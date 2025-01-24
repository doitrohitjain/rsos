<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Helper\CustomHelper;
use App\models\ExamcenterDetail;
use App\Models\StudentAllotment;
use Auth;
use Config;
use DB;
use Hash;
use PDF;
use Redirect;
use Response;
use Route;
use Session;
use Validator;


class ArrPrintForTestsController extends Controller
{

    function __construct()
    {
        /* $this->middleware('permission:theory_examiner_add', ['only' => ['index']]);
         $this->middleware('permission:theory_examiner_add', ['only' => ['add']]);
         $this->middleware('permission:Theory_examiner_delete', ['only' => ['delete']]);*/
    }

    public function ai_center_nominal_nr()
    {
        $course = 10;
        $stream = 2;
        $stream = $stream;
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $subject_list = $this->subjectCodeList();

        $exam_year = CustomHelper::_get_selected_sessions();
        $aicode = [];
        //foreach($aiCenters  as $key => $value){

        @$aicodetemp = $key;
        @$aicode = $key;
        $conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
        $conditions["student_allotments.exam_month"] = $stream;
        $conditions["student_allotments.course"] = $course;
        $conditions["student_allotments.supplementary"] = 0;
        //$conditions["student_allotments.ai_code"] = $aicode;

        $supp_conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
        $supp_conditions["student_allotments.exam_month"] = $stream;
        $supp_conditions["student_allotments.course"] = $course;
        $supp_conditions["student_allotments.supplementary"] = 1;
        //$supp_conditions["student_allotments.ai_code"] = $aicode;

        @$reportname = $aicodetemp;
        //---->remove document joinny
        $supp_master = StudentAllotment::with('document', 'student.application')->with('Supplementary', function ($query) use ($exam_year, $stream) {
            $query->where('is_eligible', 1)->whereNull('deleted_at')->where('exam_year', $exam_year)->where('exam_month', $stream);
        })->with('student', function ($query) {
            $query->whereNull('deleted_at');
        })->with('Supplementarysubjects', function ($query) {
            $query->whereNull('deleted_at');
        })
            ->where($supp_conditions)->whereNull('deleted_at')->orderBy('enrollment', 'ASC')->get()->toArray();

        $master = StudentAllotment::with('student', 'student.application')->with('examsubject', function ($query) {
            $query->whereNull('deleted_at');
        })->with('student', function ($query) {
            $query->where('is_eligible', 1)->whereNull('deleted_at');
        })->whereNull('deleted_at')->where($conditions)->orderBy('enrollment', 'ASC')->get()->toArray();


        $finalArr = array();
        @$index = 0;
        foreach ($supp_master as $student) {

            $fld = "is_supp";
            @$finalArr[$index][$fld] = true;
            $fld = "enrollment";
            @$finalArr[$index][$fld] = $student[$fld];

            $fld = "examsubject";
            @$finalArr[$index][$fld] = $student['supplementarysubjects'];


            @$index++;
        }
        foreach ($master as $student) {
            $fld = "is_supp";
            @$finalArr[$index][$fld] = false;
            $fld = "enrollment";
            @$finalArr[$index][$fld] = $student[$fld];

            $fld = "examsubject";
            @$finalArr[$index][$fld] = $student[$fld];
            @$index++;
        }
        $master = $finalArr;
        $response = array();
        foreach ($finalArr as $studentsdata) {
            $concat = null;
            foreach ($studentsdata["examsubject"] as $data) {

                $concat = $data["subject_id"];
                $response[$studentsdata['enrollment'] . "_" . $data["subject_id"]]['enrollment'] = $studentsdata['enrollment'];

                $response[$studentsdata['enrollment'] . "_" . $data["subject_id"]]['concat'] = $concat;
            }
        }

        $html = "";
        $html .= "<table width='100%' border='1px;'>";
        $html .= "<tr>";
        $html .= "<th>Enrollment</th>";
        $html .= "<th>Concat</th>";
        $html .= "</tr>";
        foreach ($response as $student_id => $data) {
            $html .= "<tr>";
            $html .= "<td>" . $data['enrollment'] . "</td>";
            $html .= "<td>" . $data['concat'] . "</td>";
            $html .= "</tr>";
        }

        //}

        $html .= "</table>";
        echo $html;
        die;

    }


    public function ai_center_hall_ticket()
    {
        echo "Test";
        die;
    }

    public function exam_center_nominal_nr()
    {
        echo "test3";
        die;
    }

    public function exam_center_attendance_roll()
    {
        echo "test4";
        die;
    }

    public function exam_center_theory_roll()
    {
        echo "test5";
        die;
    }

    public function exam_center_practical_roll()
    {
        echo "test6";
        die;
    }

    public function exam_center_practical_signature_roll()
    {
        echo "test6";
        die;
    }

    public function exam_center_theory_signature_roll()
    {
        echo "test7";
        die;
    }

    public function arr_enrollment_fixcode_view_bulk($subjectscode = 0, $stream = 2, $course = 10)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $custom_component_obj = new CustomComponent;
        if (@$subjectscode == 0) {
            $subjectList = $custom_component_obj->_getTPSubjectscode();
        } else {
            $subjectList = $custom_component_obj->_getTPSubjectscode($subjectscode);
        }
        // dd($subjectList);

        $title = "fixcode";
        $current_folder_year = $this->getCurrentYearFolderName();
        $current_year = @$current_folder_year;
        $aicentermaterial = "fixcode";
        $custom_component_obj = new CustomComponent;
        $subjectListname = $custom_component_obj->_getTPSubjectsname();
        $exam_year = CustomHelper::_get_selected_sessions();
        $conditions = array();
        $supp_conditions = array();
        $centercode = 'ecenter' . $course;
        $centerName = 'cent_name';
        $fixcode = 'fixcode';
        $final_data = array();
        $full_final_data = array();

        $html = "<table border='1'>";
        $html .= "<tr>";
        $html .= "<th>Center Code</th>";
        $html .= "<th>Subject Name</th>";
        $html .= "<th>Subject Id</th>";
        $html .= "<th>Center Fixcode</th>";
        $html .= "<th>Student Id</th>";
        $html .= "<th>Enrollment</th>";
        $html .= "<th>Fixcode</th>";
        $html .= "</tr>";


        foreach ($subjectList as $subjectid => $subjectname) {
            $centercodeandids = ExamcenterDetail::where('active', 1)->pluck($centercode, 'id')->toArray();
            $centercodeandNames = ExamcenterDetail::where('active', 1)->pluck($centerName, 'id')->toArray();
            $centerfixcode = ExamcenterDetail::where('active', 1)->pluck($fixcode, 'id')->toArray();

            foreach ($centercodeandids as $centerid => $centervalue) {
                $i = 0;
                $supp_conditions = $conditions = array();
                $conditions['student_allotments.exam_year'] = $exam_year;
                $conditions['student_allotments.exam_month'] = $stream;
                $conditions["student_allotments.examcenter_detail_id"] = $centerid;
                $conditions["student_allotments.course"] = $course;
                $supp_conditions = $conditions;
                $conditions['exam_subjects.subject_id'] = $subjectid;
                $conditions['student_allotments.supplementary'] = 0;
                $supp_conditions['supplementary_subjects.subject_id'] = $subjectid;
                $supp_conditions['supplementary_subjects.exam_year'] = $exam_year;
                $supp_conditions['supplementary_subjects.exam_month'] = $stream;
                $supp_conditions['student_allotments.supplementary'] = 1;

                $studentData = array();
                $studentData = DB::table('student_allotments')->select('student_allotments.enrollment',
                    'student_allotments.student_id',
                    'student_allotments.ai_code',
                    'examcenter_details.ecenter10',
                    'student_allotments.fixcode',
                    'examcenter_details.ecenter12', 'examcenter_details.cent_name')->join('exam_subjects', 'exam_subjects.student_id', '=', 'student_allotments.student_id')
                    ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                    ->join("students", function ($join) {
                        $join->on("student_allotments.student_id", "=", "students.id");
                        $join->whereRaw("(rs_students.is_eligible = 1)");
                    })->where($conditions)->where(function ($query) {
                        $query->orWhereNull('exam_subjects.final_result')->orWhere(['exam_subjects.final_result' => '!= PASS', 'exam_subjects.final_result' => '!= P', 'exam_subjects.final_result' => '!= p']);
                    })->whereNull('exam_subjects.deleted_at')->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->groupBy('exam_subjects.student_id')->get()->toArray();


                $SuppStudentData = array();
                $SuppStudentData = DB::table('student_allotments')->select('student_allotments.enrollment', 'student_allotments.student_id', 'student_allotments.fixcode', 'student_allotments.ai_code', 'examcenter_details.ecenter10', 'examcenter_details.ecenter12', 'examcenter_details.cent_name', 'supplementary_subjects.subject_id')
                    ->join('supplementary_subjects', 'supplementary_subjects.student_id', '=', 'student_allotments.student_id')
                    ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                    ->join('students', 'students.id', '=', 'student_allotments.student_id')
                    ->join("supplementaries", function ($join) use ($exam_year, $stream) {
                        $join->on("supplementaries.student_id", "=", "student_allotments.student_id");
                        $join->whereRaw("(rs_supplementaries.is_eligible = 1 )");
                        $join->whereRaw("(rs_supplementaries.exam_year = " . $exam_year . " )");
                        $join->whereRaw("(rs_supplementaries.exam_month = " . $stream . " )");
                    })
                    ->where($supp_conditions)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('supplementaries.deleted_at')->whereNull('supplementary_subjects.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->get()->toArray();


                $result1 = array();

                foreach ($SuppStudentData as $SuppStudentData1) {

                    $result = CustomHelper::getStudentResult($SuppStudentData1->student_id, $SuppStudentData1->subject_id);

                    if ($course == 12) {
                        if (empty($result) || $result == 888 || $result == 777) {

                            $result1[] = $SuppStudentData1;

                        }
                    } elseif ($course == 10) {
                        if (empty($result) || $result == 888) {

                            $result1[] = $SuppStudentData1;

                        }
                    }

                }

                $data = array_merge($studentData, $result1);
                if (count($data) <= 0) {
                    continue;
                }
                $final_data[$subjectid][$centerid][$i]['subject_name'] = $subjectname;
                $final_data[$subjectid][$centerid][$i]['subject_id'] = $subjectid;
                $final_data[$subjectid][$centerid][$i]['cent_code'] = $centervalue;
                $final_data[$subjectid][$centerid][$i]['cent_name'] = @$centercodeandNames[@$centerid];
                $final_data[$subjectid][$centerid][$i]['fix_code'] = @$centerfixcode[@$centerid];
                $final_data[$subjectid][$centerid][$i]['studentData'] = $data;
                $i++;

                foreach ($data as $k => $v) {
                    $full_final_data[$subjectid][$centerid][$v->student_id]['subjectname'] = @$subjectname;
                    $full_final_data[$subjectid][$centerid][$v->student_id]['subject_id'] = @$subjectid;
                    $full_final_data[$subjectid][$centerid][$v->student_id]['cent_code'] = @$centervalue;
                    $full_final_data[$subjectid][$centerid][$v->student_id]['fix_code'] = @$centerfixcode[@$centerid];
                    $full_final_data[$subjectid][$centerid][$v->student_id]['student_id'] = $v->student_id;
                    $full_final_data[$subjectid][$centerid][$v->student_id]['enrollment'] = $v->enrollment;
                    $full_final_data[$subjectid][$centerid][$v->student_id]['fixcode'] = $v->fixcode;
                }


                foreach ($full_final_data as $k => $v) {
                    foreach ($v as $ik => $iv) {
                        foreach ($iv as $uik => $uiv) {
                            $html .= "<tr>";
                            $html .= "<td>" . $uiv['cent_code'] . "</td>";
                            $html .= "<td>" . $uiv['subjectname'] . "</td>";
                            $html .= "<td>" . $uiv['subject_id'] . "</td>";
                            $html .= "<td>" . $uiv['fix_code'] . "</td>";
                            $html .= "<td>" . $uiv['student_id'] . "</td>";
                            $html .= "<td>" . $uiv['enrollment'] . "</td>";
                            $html .= "<td>" . $uiv['fixcode'] . "</td>";
                            $html .= "</tr>";
                        }
                    }
                }
            }
            $html .= "</table>";
            echo $html;
            die;
            dd($final_data);
            echo "Today is Done " . date("Y/m/d") . "<br>";
            die;
        }
    }


}
