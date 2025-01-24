<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Helper\CustomHelper;
use App\Models\CenterAllotment;
use App\Models\ExamcenterAllotment;
use App\Models\Student;
use App\Models\StudentAllotment;
use App\models\Supplementary;
use Auth;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use PDF;
use Redirect;
use Response;
use Session;
use Validator;

class ExamcenterAllotmentController extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->middleware('permission:student_dashboard', ['only' => ['dashboard']]);
        $this->middleware('permission:student-list', ['only' => ['index', 'store']]);
    }

    public function examcenter_aicenter_mapping_stream($id = null)
    {
        $page_title = 'Student Allotment';
        $model = "ExamcenterAllotment";
        $custom_component_obj = new CustomComponent;
        $allowips = $custom_component_obj->CenterAllotmentAllowIps();

        if ($allowips == false) {
            return redirect()->route('landing')->with('error', 'You are not allow to Center Allotment.');
        }
        $stream = config("global.CenterAllotmentStreamId");
        $e_exam_center_id = $id;
        $exam_center_id = Crypt::decrypt($e_exam_center_id);
        return redirect()->route('examcenter_aicenter_mapping_stream' . $stream, $e_exam_center_id);
    }

    public function examcenter_aicenter_mapping_stream1(Request $request, $id = null)
    {

        $page_title = 'Student Allotment';
        $model = "ExamcenterAllotment";
        $custom_component_obj = new CustomComponent;
        $allowips = $custom_component_obj->CenterAllotmentAllowIps();

        if ($allowips == false) {
            return redirect()->route('landing')->with('error', 'You are not allow to Center Allotment.');
        }
        // $stream = config("global.SuppDefaultStreamId");
        $stream = 1;
        $exam_year = config("global.current_admission_session_id");
        $exam_month = config("global.current_exam_month_id");

        $custom_component_obj = new CustomComponent;
        $e_exam_center_id = $id;
        $exam_center_id = Crypt::decrypt($e_exam_center_id);


        $stream_global = config("global.CenterAllotmentStreamId");
        if ($stream_global != 1) {
            return redirect()->route('examcenter_aicenter_mapping_stream' . $stream_global, $e_exam_center_id);
        }

        if (empty($exam_center_id)) {
            return redirect()->route('examcenter_aicenter_mapping_stream1')->with('error', 'Failed! Exam Center id does not found!');
        }

        $examCenterData = $custom_component_obj->getExamcenterDataById($exam_center_id);

        $aiCenterList = $custom_component_obj->getAiCenters();
        unset($aiCenterList[@$examCenterData->ai_code]);
        // @dd($aiCenterList);

        if (count($request->all()) > 0) {
            $student_strem_10 = 'student_strem' . $stream . '_10';
            $student_strem_12 = 'student_strem' . $stream . '_12';
            $custom_component_obj = new CustomComponent;
            $response = $custom_component_obj->centerAllotmentValidations($request);
            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];

            if ($isValid) {

                $exam_center_id = Crypt::decrypt(@$request->id);

                $student_strem_stream_10_limit = "student_strem" . $stream . "_10";

                $studentcodefromto10 = $custom_component_obj->getStudentCodeAllotment($request->ai_code, $stream, 10, $request->$student_strem_stream_10_limit);
                $arr = explode('-', $studentcodefromto10);
                $student_code_from_10 = $arr[0];
                $student_code_to_10 = $arr[1];

                $student_strem_stream_12_limit = "student_strem" . $stream . "_12";
                $studentcodefromto12 = $custom_component_obj->getStudentCodeAllotment($request->ai_code, $stream, 12, $request->$student_strem_stream_12_limit);
                $arr = explode('-', $studentcodefromto12);
                $student_code_from_12 = $arr[0];
                $student_code_to_12 = $arr[1];

                //echo "<pre>"; print_r($studentcodefromto10);
                //@dd($studentcodefromto12);

                /** Code commented due to allow individual course supplementary in center allotment */

                /*$supp_10 = 0;
                $supp_12 = 0;
                $ceneter_allotment_data = CenterAllotment::where('ai_code',$request->ai_code)->where('exam_month',$request->stream)->where('exam_year',$exam_year)->count();
                if($ceneter_allotment_data == 0){
                    $supp_data = $custom_component_obj->_getStudentsCountForExamcenter(@$request->ai_code,$stream);
                    $supp_10 = $supp_data['total']['supp_10'];
                    $supp_12 = $supp_data['total']['supp_12'];
                }*/


                $supp_data = $custom_component_obj->_getStudentsCountForExamcenter(@$request->ai_code, $stream);
                $supp_10 = $supp_data['total']['supp_10'];
                $supp_12 = $supp_data['total']['supp_12'];

                if ($request->student_supp_10 > 0 && $request->student_supp_10 < $supp_10) {
                    return redirect()->back()->with('error', "Supp Students Can't be Divided");
                }
                if ($request->student_supp_12 > 0 && $request->student_supp_12 < $supp_12) {
                    return redirect()->back()->with('error', "Supp Students Can't be divided");
                } else {
                    $student_allotment_array = array(
                        'examcenter_detail_id' => $exam_center_id,
                        'ai_code' => @$request->ai_code,
                        'course' => 0,
                        'student_strem' . $stream . '_10' => @$request->$student_strem_10,
                        'student_strem' . $stream . '_12' => @$request->$student_strem_12,
                        'student_supp_10' => @$request->student_supp_10,
                        'student_supp_12' => @$request->student_supp_12,
                        'total_of_10' => (@$request->$student_strem_10 + @$request->student_supp_10),
                        'total_of_12' => (@$request->$student_strem_12 + @$request->student_supp_12),
                        'supp_total' => (@$request->student_supp_10 + @$request->student_supp_12),
                        'student_code_from_10' => $student_code_from_10,
                        'student_code_to_10' => $student_code_to_10,
                        'student_code_from_12' => $student_code_from_12,
                        'student_code_to_12' => $student_code_to_12,
                        'stream' . $stream . '_total' => (@$request->$student_strem_10 + @$request->$student_strem_12),
                        'total' => (@$request->$student_strem_10 + @$request->$student_strem_12 + @$request->student_supp_10 + @$request->student_supp_12),
                        'stream' => @$stream,
                        'exam_year' => @$exam_year,
                        'exam_month' => @$exam_month,
                        'academic_session' => Config::get('global.current_admission_session_id'),
                        'exam_event' => @$exam_month
                    );


                }
            } else {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $student = ExamcenterAllotment::create($student_allotment_array);

            if ($student) {
                return redirect()->route('preview_centerallotment_stream' . $stream, $e_exam_center_id)->with('message', 'Data has been submitted sucessfully');
            } else {
                return redirect()->route('preview_centerallotment_stream' . $stream)->with('error', 'Failed! Data does not submitted');
            }
        }

        return view('examcenter_allotment.examcenter_aicenter_mapping_stream1', compact('stream', 'examCenterData', 'aiCenterList', 'model', 'page_title', 'e_exam_center_id', 'exam_center_id'));
    }


    public function examcenter_aicenter_mapping_stream2(Request $request, $id = null)
    {
        $page_title = 'Student Allotment';
        $model = "ExamcenterAllotment";
        $custom_component_obj = new CustomComponent;
        $allowips = $custom_component_obj->CenterAllotmentAllowIps();

        if ($allowips == false) {
            return redirect()->route('landing')->with('error', 'You are not allow to Center Allotment.');
        }
        // $stream = config("global.SuppDefaultStreamId");
        $stream = 2;
        $exam_year = config("global.current_admission_session_id");
        $exam_month = config("global.current_exam_month_id");


        $e_exam_center_id = $id;
        $exam_center_id = Crypt::decrypt($e_exam_center_id);

        $stream_global = config("global.CenterAllotmentStreamId");
        if ($stream_global != 2) {
            return redirect()->route('examcenter_aicenter_mapping_stream' . $stream_global, $e_exam_center_id);
        }

        if (empty($exam_center_id)) {
            return redirect()->route('examcenter_aicenter_mapping_stream2')->with('error', 'Failed! Exam Center id does not found!');
        }

        $custom_component_obj = new CustomComponent;
        $examCenterData = $custom_component_obj->getExamcenterDataById($exam_center_id);

        $aiCenterList = $custom_component_obj->getAiCenters();
        unset($aiCenterList[@$examCenterData->ai_code]);
        // @dd($aiCenterList);

        if (count($request->all()) > 0) {

            $student_strem_10 = 'student_strem' . $stream . '_10';
            $student_strem_12 = 'student_strem' . $stream . '_12';

            $custom_component_obj = new CustomComponent;
            $response = $custom_component_obj->centerAllotmentValidations($request);
            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];

            if ($isValid) {
                $exam_center_id = Crypt::decrypt(@$request->id);
                $aiCenterList = $custom_component_obj->getAiCenters();

                $student_strem_stream_10_limit = "student_strem" . $stream . "_10";
                $studentcodefromto10 = $custom_component_obj->getStudentCodeAllotment($request->ai_code, $stream, 10, $request->$student_strem_stream_10_limit);
                $arr = explode('-', $studentcodefromto10);
                $student_code_from_10 = $arr[0];
                $student_code_to_10 = $arr[1];

                $student_strem_stream_12_limit = "student_strem" . $stream . "_12";
                $studentcodefromto12 = $custom_component_obj->getStudentCodeAllotment($request->ai_code, $stream, 12, $request->$student_strem_stream_12_limit);
                $arr = explode('-', $studentcodefromto12);
                $student_code_from_12 = $arr[0];
                $student_code_to_12 = $arr[1];

                // echo "<pre>"; print_r($studentcodefromto10);
                // @dd($studentcodefromto12);

                $supp_10 = 0;
                $supp_12 = 0;
                $ceneter_allotment_data = CenterAllotment::where('exam_year', $exam_year)->where('exam_month', $exam_month)->where('ai_code', $request->ai_code)->count();

                if ($ceneter_allotment_data == 0) {
                    $supp_data = $custom_component_obj->_getStudentsCountForExamcenter(@$request->ai_code, $stream);
                    $supp_10 = $supp_data['total']['supp_10'];
                    $supp_12 = $supp_data['total']['supp_12'];
                }


                $student_allotment_array = array(
                    'examcenter_detail_id' => $exam_center_id,
                    'ai_code' => @$request->ai_code,
                    'course' => 0,
                    'student_strem' . $stream . '_10' => @$request->$student_strem_10,
                    'student_strem' . $stream . '_12' => @$request->$student_strem_12,
                    'student_supp_10' => @$supp_10,
                    'student_supp_12' => @$supp_12,
                    'total_of_10' => (@$request->$student_strem_10 + @$request->student_supp_10),
                    'total_of_12' => (@$request->$student_strem_12 + @$request->student_supp_12),
                    'supp_total' => (@$supp_10 + @$supp_12),
                    'student_code_from_10' => $student_code_from_10,
                    'student_code_to_10' => $student_code_to_10,
                    'student_code_from_12' => $student_code_from_12,
                    'student_code_to_12' => $student_code_to_12,
                    'stream' . $stream . '_total' => (@$request->$student_strem_10 + @$request->$student_strem_12),
                    'total' => (@$request->$student_strem_10 + @$request->$student_strem_12 + @$request->student_supp_10 + @$request->student_supp_12),
                    'stream' => @$stream,
                    'exam_year' => @$exam_year,
                    'exam_month' => @$exam_month,
                    'academic_session' => Config::get('global.current_admission_session_id'),
                    'exam_event' => @$exam_month
                );
            } else {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $student = ExamcenterAllotment::create($student_allotment_array);

            if ($student) {
                return redirect()->route('preview_centerallotment_stream' . $stream, $e_exam_center_id)->with('message', 'Data has been submitted sucessfully');
            } else {
                return redirect()->route('preview_centerallotment_stream' . $stream)->with('error', 'Failed! Data does not submitted');
            }
        }

        return view('examcenter_allotment.examcenter_aicenter_mapping_stream2', compact('stream', 'examCenterData', 'aiCenterList', 'model', 'page_title', 'e_exam_center_id', 'exam_center_id'));
    }

    public function preview_centerallotment_stream1($examcenter_detail_id = null, Request $request)
    {
        $stream = 1;
        $custom_component_obj = new CustomComponent;
        $allowips = $custom_component_obj->CenterAllotmentAllowIps();
        if ($allowips == false) {
            return redirect()->route('landing')->with('error', 'You are not allow to Center Allotment.');
        }
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $district = $this->districtsByState();

        $e_examcenter_detail_id = $examcenter_detail_id;
        $examcenter_detail_id = Crypt::decrypt($e_examcenter_detail_id);

        $yes_no = $this->master_details('yesno');
        $title = "Exam Center Student Allotment";
        $table_id = "ExamCenter_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $permissions = CustomHelper::roleandpermission();
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => ''
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );

        $exportBtn = array(
            array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadExamCenterAllotmentStream1Exl',
                    'status' => false,
                ),
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadSchoolPdf',
                'status' => false
            ),
        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["center_allotments.examcenter_detail_id"] = $examcenter_detail_id;
        Session::put('_condtions', $conditions);

        $master = $custom_component_obj->getExamcenterAllotmentDataByExamcenterDetailId($formId);
        return view('examcenter_allotment.preview_centerallotment_stream' . $stream, compact('stream', 'master', 'exportBtn', 'formId', 'table_id', 'title', 'breadcrumbs', 'examcenter_detail_id'))->withInput($request->all());
    }


    public function preview_centerallotment_stream2($examcenter_detail_id = null, Request $request)
    {
        $stream = 2;
        $custom_component_obj = new CustomComponent;
        $allowips = $custom_component_obj->CenterAllotmentAllowIps();
        if ($allowips == false) {
            return redirect()->route('landing')->with('error', 'You are not allow to Center Allotment.');
        }
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $district = $this->districtsByState();

        $e_examcenter_detail_id = $examcenter_detail_id;
        $examcenter_detail_id = Crypt::decrypt($e_examcenter_detail_id);

        $yes_no = $this->master_details('yesno');
        $title = "Exam Center Student Allotment";
        $table_id = "ExamCenter_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $permissions = CustomHelper::roleandpermission();
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => ''
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );

        $exportBtn = array(
            array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadExamCenterAllotmentStream1Exl',
                    'status' => false,
                ),
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadSchoolPdf',
                'status' => false
            ),
        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["center_allotments.examcenter_detail_id"] = $examcenter_detail_id;
        Session::put('_condtions', $conditions);

        $master = $custom_component_obj->getExamcenterAllotmentDataByExamcenterDetailId($formId);
        // @dd($master);
        return view('examcenter_allotment.preview_centerallotment_stream' . $stream, compact('stream', 'master', 'exportBtn', 'formId', 'table_id', 'title', 'breadcrumbs'))->withInput($request->all());
    }


    public function studentenrollmentallotment($ai_code = null, $course = null, $stream = null, $examcenterdetailid = null, $centerallotmentid = null, $supplementary = 0)
    {
        $conditionArray = array("id" => $centerallotmentid);
        $centerallotment = CenterAllotment::where($conditionArray)->first();
        $fld = 'is_student_strem' . $stream . '_' . $course;
        $custom_component_obj = new CustomComponent;
        $check_allotment_status = $custom_component_obj->checkDuplicateAllotment($centerallotmentid, $fld);

        if ($check_allotment_status == false) {
            return redirect()->route('preview_centerallotment_stream' . $stream, Crypt::encrypt($examcenterdetailid))->with('error', 'Students already Alloted');
        }

        $allot_flag_data = array($fld => 1);
        $allot_flag_status = CenterAllotment::where('id', $centerallotmentid)->update($allot_flag_data);

        $studentcodestart = 0;
        $studentcodeend = 0;
        if ($course == 10) {
            $studentcodestart = $centerallotment->student_code_from_10;
            $studentcodeend = $centerallotment->student_code_to_10;
        }
        if ($course == 12) {
            $studentcodestart = $centerallotment->student_code_from_12;
            $studentcodeend = $centerallotment->student_code_to_12;
        }

        $exam_year = Config::get('global.current_admission_session_id');
        $exam_month = $stream;

        $studentconditions['students.course'] = $course;
        $studentconditions['students.is_eligible'] = 1;
        $studentconditions['students.exam_year'] = $exam_year;
        $studentconditions['students.exam_month'] = $exam_month;
        $studentconditions['students.ai_code'] = $ai_code;

        $students = Student::where($studentconditions)
            ->whereNull('deleted_at')
            ->where('students.student_code', '>=', $studentcodestart)
            ->where('students.student_code', '<=', $studentcodeend)
            ->orderBy('students.student_code', 'ASC')
            ->get(['enrollment', 'id']);


        foreach ($students as $key => $value) {
            $saveData = array();
            $saveData['exam_year'] = $exam_year;
            $saveData['ai_code'] = $ai_code;
            $saveData['examcenter_detail_id'] = $examcenterdetailid;
            $saveData['center_allotment_id'] = $centerallotmentid;
            $saveData['exam_month'] = $exam_month;
            $saveData['stream'] = $stream;
            $saveData['course'] = $course;
            $saveData['supplementary'] = $supplementary;
            $saveData['enrollment'] = $value->enrollment;
            $saveData['student_id'] = $value->id;
            $saveData['created_at'] = date("Y-m-d H:i:s");
            $saveData['updated_at'] = date("Y-m-d H:i:s");
            StudentAllotment::insert($saveData);
        }
        return redirect()->route('preview_centerallotment_stream' . $stream, Crypt::encrypt($examcenterdetailid))->with('message', 'Student alloted successfully!.');
    }

    public function suppstudentenrollmentallotment($ai_code = null, $course = null, $stream = null, $examcenterdetailid = null, $centerallotmentid = null, $supplementary = 1)
    {
        $conditionArray = array("id" => $centerallotmentid);
        $centerallotment = CenterAllotment::where($conditionArray)->first();

        $fld = 'is_student_supp_' . $course;
        $custom_component_obj = new CustomComponent;
        $check_allotment_status = $custom_component_obj->checkDuplicateAllotment($centerallotmentid, $fld);
        if ($check_allotment_status == false) {
            return redirect()->route('preview_centerallotment_stream' . $stream, Crypt::encrypt($examcenterdetailid))->with('error', 'Students already Alloted');
        }

        $allot_supp_flag_data = array($fld => 1);
        $allot_flag_status = CenterAllotment::where('id', $centerallotmentid)->update($allot_supp_flag_data);

        $exam_year = Config::get('global.current_admission_session_id');
        // $exam_month = Config::get('global.current_exam_month_id');
        $exam_month = $stream;

        $suppconditions = array();
        $suppconditions['supplementaries.course'] = $course;
        $suppconditions['supplementaries.ai_code'] = $ai_code;
        $suppconditions['supplementaries.exam_year'] = $exam_year;
        $suppconditions['supplementaries.exam_month'] = $exam_month;
        $suppconditions['supplementaries.is_eligible'] = 1;

        $stream_condition_array = array(
            "students.course" => $course,
            "students.ai_code" => $ai_code,
            "students.exam_year" => $exam_year,
            "students.is_eligible" => 1,
            "students.exam_month" => $exam_month,
        );

        $supplementary_data = Supplementary::where($suppconditions)
            ->whereNull('deleted_at')
            ->orderBy('supplementaries.id', 'ASC')
            ->get();

        foreach ($supplementary_data as $key => $value) {
            $saveData = array();
            $saveData['exam_year'] = $exam_year;
            $saveData['ai_code'] = $ai_code;
            $saveData['examcenter_detail_id'] = $examcenterdetailid;
            $saveData['center_allotment_id'] = $centerallotmentid;
            $saveData['exam_year'] = $exam_year;
            $saveData['stream'] = $stream;
            $saveData['exam_month'] = $exam_month;
            $saveData['course'] = $course;
            $saveData['supplementary'] = $supplementary;
            $saveData['enrollment'] = $value->enrollment;
            $saveData['student_id'] = $value->student_id;
            $saveData['created_at'] = date("Y-m-d H:i:s");
            $saveData['updated_at'] = date("Y-m-d H:i:s");
            StudentAllotment::insert($saveData);
        }
        return redirect()->route('preview_centerallotment_stream' . $stream, Crypt::encrypt($examcenterdetailid))->with('message', 'Student alloted successfully!.');
    }
}




