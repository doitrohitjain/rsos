<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Exports\AicentersubjectdatastudentExlExport;
use App\Exports\AiCenterWiseSubjectCountExlExport;
use App\Exports\AllStudentNotPayPaymentDetailsExlExport;
use App\Exports\StudentAicenterCourseWiseCountExlExport;
use App\Exports\AllStudentPaymentDetailsApplicationExlExport;
use App\Exports\AllStudentZeroTeesPaydetailsApplicationExl;
use App\Exports\ApplicationExlExport;
use App\Exports\ApplicationLockSubmitDataExlExport;
use App\Exports\ChangeRequertStudentExlExport;
use App\Exports\Femaleandmalefeesexcelsdownload;
use App\Exports\FreshStudentVerifyingExlExport;
use App\Exports\RevalApplicationExlExport;
use App\Exports\StudentAicenterWiseCountExlExport;
use App\Exports\StudentupdatedataExlExport;
use App\Exports\SubjectWiseCountExlExport;
use App\Exports\supplementarieArieaicenterWiseCountexcelExport;
use App\Exports\SupplementarieFeesExlExport;
use App\Exports\SupplementarieSubjectExlExport;
use App\Exports\SupplementaryApplicationExlExport;
use App\Exports\SupplementaryStudentLockSumbitedApplicationsExlExport;
use App\Exports\SupplementaryStudentNotPayPaymentDetailsExlExport;
use App\Exports\SupplementaryStudentPaymentDetailsExlExport;
use App\Exports\SupplementarySubjectWiseCountExlExport;
use App\Helper\CustomHelper;
use App\models\Examination;
use App\models\Student;
use App\models\Supplementary;
use App\models\User;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Session;
use Yajra\DataTables\DataTables;

/* Hall Ticket Addtional Model */

/* Hall Ticket Addtional Model */


class AdmissionReportsController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:admission_report_student_applications', ['only' => ['student_applications']]);
        $this->middleware('permission:admission_report_student_downloadapplicationexl', ['only' => ['downloadApplicationExl']]);
        $this->middleware('permission:admission_report_student_downloadapplicationpdf', ['only' => ['downloadApplicationPdf']]);
        $this->middleware('permission:admission_report_student_application_ai_center_wise_count', ['only' => ['student_application_ai_center_wise_count']]);
        $this->middleware('permission:subject_wise_student_count', ['only' => ['subject_wise_student_count']]);
        $this->middleware('permission:admission_report_toc_students_ai_center_wise_count', ['only' => ['toc_students_ai_center_wise_count']]);
    }

    public function supplementary_subject_wise_student_count(Request $request)
    {
        $custom_component_obj = new CustomComponent;
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

        $yes_no = $this->master_details('yesno');
        $title = "Supplementary Subject Wise Student Report";
        $table_id = "Supplementary_Subject_Wise_Student_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $aiCenters = $custom_component_obj->getAiCenters();

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
                "label" => "Export Excel",
                'url' => 'downloadsupplementarysubjectwisecountExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => false
            ),
        );
        $subject_list = $this->subjectList();
        $filters = array(
            array(
                "lbl" => "Enrollment Number",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'placeholder' => 'Subject',
                'dbtbl' => 'supplementary_subjects',
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course',
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream',
                'dbtbl' => 'supplementaries',
            ),

        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        if ($isAdminStatus == false) {
            $role_id = Session::get('role_id');
            $conditions["users.id"] = @Auth::user()->id;
        } else {
            // $filters[] = array(
            // 	"lbl" => "Ai Center",
            // 	'fld' => 'ai_code',
            // 	'input_type' => 'select',
            // 	'options' => $aiCenters,
            // 	'placeholder' => 'Ai Center'
            // );
            // $tableData[] = array(
            // 	"lbl" => "Ai Center",
            // 	'fld' => 'ai_code',
            // 	'input_type' => 'select',
            // 	'options' => $aiCenters,
            // 	'placeholder' => 'Ai Center',
            // 	'dbtbl' => 'users'
            // );
        }

        if ($request->all()) {
            $conditions = array();
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }
        $conditions["supplementaries.exam_year"] = CustomHelper::_get_selected_sessions();
        $conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();

        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
        $conditions["supplementaries.exam_month"] = $conditions["student_allotments.exam_month"] = $exam_month;

        $conditions["supplementaries.locksumbitted"] = 1;
        //$conditions["students.is_eligible"] = 1;
        Session::put($formId . '_conditions', $conditions);
        $conditions = Session::get($formId . '_conditions');
        $master = $custom_component_obj->getsupplementaryStudentCountSubjectWise($formId);
        return view('admission_reports.supplementary_subject_student_wise_count', compact('aiCenters', 'master', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'subject_list'));
    }

    public function downloadsupplementarysubjectwisecountExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new SupplementarySubjectWiseCountExlExport;
        $filename = 'Supplementarysubjecwisecount_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }


    public function subject_wise_student_count(Request $request)
    {

        $custom_component_obj = new CustomComponent;

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
        $yes_no = $this->master_details('yesno');
        $title = "Subject Wise Student Report";
        $table_id = "Subject_Wise_Student_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $aiCenters = $custom_component_obj->getAiCenters();


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
                "label" => "Export Excel",
                'url' => 'downloadsubjectwisecountExl',
                'status' => false,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => false
            ),
        );
        $subject_list = $this->subjectList();
        $filters = array(

            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course',
                'dbtbl' => 'student_allotments',
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream',
                'dbtbl' => 'student_allotments',
            ),


            // array(
            // 	"lbl" => "Lock & Submit",
            // 	'fld' => 'locksumbitted',
            // 	'input_type' => 'select',
            // 	'options' => $yes_no,
            // 	'placeholder' => 'Lock & Submit',
            // 	'dbtbl' => 'applications',
            // )
        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        if ($isAdminStatus == false) {
            $role_id = Session::get('role_id');

        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center'
            );

        }


        if ($request->all()) {
            $conditions = array();
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }

        $role_ids = Session::get('role_id');

        $subjectList = $custom_component_obj->subjectListcode($request->course);


        $month = config("global.current_exam_month_id");

        $exam_year = CustomHelper::_get_selected_sessions();

        $final_data = array();
        $i = 0;
        foreach ($subjectList as $subjectid => $subjectname) {


            $conditions['student_allotments.exam_year'] = $exam_year;
            if (!empty($request->exam_month)) {
                $conditions['student_allotments.exam_month'] = $request->exam_month;
            } else {
                $conditions['student_allotments.exam_month'] = $month;
            }
            if (!empty($request->course)) {
                $conditions["student_allotments.course"] = $request->course;
            }

            if (!empty($request->ai_code)) {
                $conditions["student_allotments.ai_code"] = $request->ai_code;
            } elseif ($role_ids == 71) {
                $conditions["student_allotments.ai_code"] = NULL;
            } else {
                $ai_code = @Auth::user()->ai_code;
                $conditions["student_allotments.ai_code"] = $ai_code;
            }

            $conditions['exam_subjects.subject_id'] = $subjectid;
            $conditions['student_allotments.supplementary'] = 0;


            $supp_conditions['student_allotments.exam_year'] = $exam_year;
            if (!empty($request->exam_month)) {
                $supp_conditions['student_allotments.exam_month'] = $request->exam_month;
            } else {
                $supp_conditions['student_allotments.exam_month'] = $month;
            }
            if (!empty($request->course)) {
                $supp_conditions["student_allotments.course"] = $request->course;
            }

            if (!empty($request->ai_code)) {
                $supp_conditions["student_allotments.ai_code"] = $request->ai_code;
            } elseif ($role_ids == 71) {
                $supp_conditions["student_allotments.ai_code"] = NULL;
            } else {
                $ai_code = @Auth::user()->ai_code;
                $supp_conditions["student_allotments.ai_code"] = $ai_code;
            }


            $supp_conditions['supplementary_subjects.subject_id'] = $subjectid;
            $supp_conditions['student_allotments.supplementary'] = 1;

            $studentData = array();
            $studentData = DB::table('student_allotments')->select('student_allotments.enrollment', 'student_allotments.student_id', 'students.name')->join('exam_subjects', 'exam_subjects.student_id', '=', 'student_allotments.student_id')
                ->join("students", function ($join) {
                    $join->on("student_allotments.student_id", "=", "students.id");
                    $join->whereRaw("(rs_students.is_eligible = 1)");
                })->where($conditions)->whereNull('exam_subjects.deleted_at')
                ->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->get()->count();

            $SuppStudentData = array();
            $SuppStudentData = DB::table('student_allotments')->select('student_allotments.enrollment', 'student_allotments.student_id')
                ->join('supplementary_subjects', 'supplementary_subjects.student_id', '=', 'student_allotments.student_id')
                ->join("supplementaries", function ($join) {
                    $join->on("supplementaries.student_id", "=", "student_allotments.student_id");
                    $join->whereRaw("(rs_supplementaries.is_eligible = 1 )");
                })
                ->where($supp_conditions)->whereNull('student_allotments.deleted_at')->whereNull('supplementary_subjects.deleted_at')->whereNull('supplementaries.deleted_at')->get()->count();

            $final_data[$i]['subject_id'] = $subjectid;
            $final_data[$i]['studentData'] = $studentData;
            $final_data[$i]['SuppStudentData'] = $SuppStudentData;
            $i++;

        }


        return view('admission_reports.subject_student_wise_count', compact('aiCenters', 'final_data', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'subject_list'));

    }


    public function subject_wise_student_count_steams(Request $request)
    {

        $custom_component_obj = new CustomComponent;

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

        //$yes_no = $this->master_details('yesno');
		@$yes_nos = array("1" => "YES", "2" => "NO");
        @$yes_no = collect($yes_nos);
        $title = "Subject Wise Student Report";
        $table_id = "Subject_Wise_Student_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $aiCenters = $custom_component_obj->getAiCenters();


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
                "label" => "Export Excel",
                'url' => 'downloadsubjectwisecountExl',
                'status' => false,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => false
            ),
        );
        $subject_list = $this->subjectList();
        $filters = array(

            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream',
                'dbtbl' => 'students',
            ),


            array(
            	"lbl" => "is eligible",
             	'fld' => 'is_eligible',
             	'input_type' => 'select',
             	'options' => $yes_no,
             	'placeholder' => 'is eligible',
             	'dbtbl' => 'students',
             )
        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        if ($isAdminStatus == false) {
            $role_id = Session::get('role_id');

        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center'
            );

        }
		

        if ($request->all()) {
            $conditions = array();
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }

        $role_ids = Session::get('role_id');

        $subjectList = $custom_component_obj->subjectListcode($request->course);


        $month = config("global.current_exam_month_id");

        $exam_year = CustomHelper::_get_selected_sessions();

        $final_data = array();
        $i = 0;
        foreach ($subjectList as $subjectid => $subjectname) {
            $conditions['students.exam_year'] = $exam_year;
            if (!empty($request->stream)) {
                $conditions['students.stream'] = $request->stream;
            } else {
                $conditions['students.stream'] = $month;
            }
            if (!empty($request->course)) {
                $conditions["students.course"] = $request->course;
            }
			if(!empty($request->is_eligible)){
				
				if($request->is_eligible == 1){
                 $supp_conditions['supplementaries.is_eligible'] = 1;				 
				}
				elseif($request->is_eligible == 2){
			     $conditions["applications.is_ready_for_verifying"] = 1;
               
				}
			
			}

            if (!empty($request->ai_code)) {
                $conditions["students.ai_code"] = $request->ai_code;
            } elseif ($role_ids == 71) {
                //$conditions["students.ai_code"] =  NULL;
            } else {
                $ai_code = @Auth::user()->ai_code;
                $conditions["students.ai_code"] = $ai_code;
            }

            $conditions['exam_subjects.subject_id'] = $subjectid;


            $supp_conditions['supplementaries.exam_year'] = $exam_year;
            if (!empty($request->stream)) {
                $supp_conditions['supplementaries.exam_month'] = $request->stream;
            } else {
                $supp_conditions['supplementaries.exam_month'] = $month;
            }
            if (!empty($request->course)) {
                $supp_conditions["supplementaries.course"] = $request->course;
            }

            if (!empty($request->ai_code)) {
                $supp_conditions["supplementaries.ai_code"] = $request->ai_code;
            } elseif ($role_ids == 71) {
                //$supp_conditions["supplementaries.ai_code"] =  NULL;
            }elseif ($role_ids != 59) {
               //$ai_code = @Auth::user()->ai_code;
                //$supp_conditions["supplementaries.ai_code"] = $ai_code;
            }
			else {
                $ai_code = @Auth::user()->ai_code;
                $supp_conditions["supplementaries.ai_code"] = $ai_code;
            }

            $supp_conditions['supplementary_subjects.subject_id'] = $subjectid;
			
			if(!empty($request->course) && !empty($request->stream) && !empty($request->is_eligible)){
	        unset($conditions['students.is_eligible']);
		
	        if(@$conditions['applications.is_ready_for_verifying'] == 1){
			 $studentData = array();
             $studentData = DB::table('students')->join('exam_subjects', 'exam_subjects.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereNull('exam_subjects.deleted_at')->whereNull('students.deleted_at')->whereNull('applications.deleted_at')->orderBy('subject_id', 'DESC')->get()->count();
            $SuppStudentData = array();
            $SuppStudentData = DB::table('supplementaries')
                ->join('supplementary_subjects', 'supplementary_subjects.supplementary_id', '=', 'supplementaries.id')
                ->where($supp_conditions)->whereNotNull('supplementaries.challan_tid')->
				whereNull('supplementary_subjects.deleted_at')->whereNull('supplementaries.deleted_at')->orderBy('subject_id', 'DESC')->get()->count();	
			}
			else{
			$studentData = array();
            $studentData = DB::table('students')->join('exam_subjects', 'exam_subjects.student_id', '=', 'students.id')->where('students.is_eligible',1)->
			where($conditions)->whereNull('exam_subjects.deleted_at')->whereNull('students.deleted_at')->orderBy('subject_id', 'DESC')->get()->count();
            $SuppStudentData = array();
            $SuppStudentData = DB::table('supplementaries')
                ->join('supplementary_subjects', 'supplementary_subjects.supplementary_id', '=', 'supplementaries.id')
                ->where($supp_conditions)->whereNull('supplementary_subjects.deleted_at')->whereNull('supplementaries.deleted_at')->orderBy('subject_id', 'DESC')->get()->count();
			}
			
            $final_data[$i]['subject_id'] = $subjectid;
            $final_data[$i]['studentData'] = $studentData;
            $final_data[$i]['SuppStudentData'] = $SuppStudentData;
            $i++;	
			}
			 
			
        }


        return view('admission_reports.subject_wise_student_count_steams', compact('aiCenters', 'final_data', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'subject_list'));

    }

    public function downloadsubjectwisecountExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new SubjectWiseCountExlExport;
        $filename = 'subjecwisecount_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function downloadstudentupdatedataExl($type = "xlsx")
    {
        $application_exl_data = new StudentupdatedataExlExport;
        $filename = 'studentupdate_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function student_payment_details(Request $request)
    {
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

        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
        $table_id = "Admission_Report";
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
                "label" => "Export Excel",
                'url' => 'downloadApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => true
            ),
        );


        $filters = array(
            array(
                "lbl" => "Enrollment Number",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Amount",
                'fld' => 'fee_paid_amount',
                'input_type' => 'text',
                'placeholder' => "Amount",
                'dbtbl' => 'applications',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission Type",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Lock & Submit',
                'dbtbl' => 'students',
            )
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                "lbl" => "Stream",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id
            ),
            array(
                "lbl" => "Admission",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            array(
                "lbl" => "Challan Number",
                'fld' => 'challan_tid'
            ), array(
                "lbl" => "Fees Amount",
                'fld' => 'fee_paid_amount'
            ),
            array(
                "lbl" => "Submitted ",
                'fld' => 'submitted'
            )
        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $conditions["students.user_id"] = @Auth::user()->id;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center'
            );
            $tableData[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'users'
            );
        }

        if (in_array("application_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.>edit</i>',
                    'fld_url' => '../student/persoanl_details/#id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.>remove_red_eye</i>',
                    'fld_url' => '../student/preview_details/#id#'
                ),

            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.>remove_red_eye</i>',
                    'fld_url' => '../student/preview_details/#id#'
                ),
            );
        }

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                            $conditions[$iv['dbtbl'] . "." . $k] = $v;
                        } else {
                            $conditions[$k] = $v;
                        }
                        break;
                    }
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);

        $master = $custom_component_obj->getStudentPaymentData($formId);

        return view('admission_reports.student_payment_details', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }


    public function allstudent_payment_details(Request $request)
    {
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
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);

        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
        $table_id = "Admission_Report";
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
                "label" => "Export Excel",
                'url' => 'downloadallstudentpaymentdetailsApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => false,
            ),
        );


        $filters = array(
            array(
                "lbl" => "Enrollment Number",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Amount",
                'fld' => 'fee_paid_amount',
                'input_type' => 'text',
                'placeholder' => "Amount",
                'dbtbl' => 'applications',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission Type",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            // array(
            // 	"lbl" => "Lock & Submit",
            // 	'fld' => 'locksumbitted',
            // 	'input_type' => 'select',
            // 	'options' => $yes_no,
            // 	'placeholder' => 'Lock & Submit',
            // 	'dbtbl' => 'students',
            // )
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),

            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month
            ),
            array(
                "lbl" => "Stream",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id
            ),
            array(
                "lbl" => "Admission",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            ),
            // array(
            // 	"lbl" => "Lock & Submit",
            // 	'fld' => 'locksumbitted',
            // 	'input_type' => 'select',
            // 	'options' => $yes_no
            // ),
            array(
                "lbl" => "Challan Number",
                'fld' => 'challan_tid'
            ), array(
                "lbl" => "Fees Amount",
                'fld' => 'fee_paid_amount'
            ),
            array(
                "lbl" => "Submitted",
                'fld' => 'submitted'
            )
        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $aicenter_user_id = Auth::user()->id;
            $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
            //$conditions["students.ai_code"] = $aicenter_user_ids->ai_code;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center'
            );
            /* $tableData[] = array(
				"lbl" => "Ai Center",
				'fld' => 'ai_code',
				'input_type' => 'select',
				'options' => $aiCenters,
				'placeholder' => 'Ai Center',
				'dbtbl' => 'users'
			); */
            $tableData[] = array(
                "lbl" => "AiCode",
                'fld' => 'ai_code',
                'fld_url' => ''
            );

        }

        if (in_array("application_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => '../student/persoanl_details/#id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../student/preview_details/#id#'
                ),

            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons">remove_red_eye</i>',
                    'fld_url' => '../student/preview_details/#id#'
                ),
            );
        }

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }

        if ($isAdminStatus == false) {
            $aicenter_user_id = Auth::user()->id;
            $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
            $auth_user_id = $aicenter_user_ids->ai_code;
            $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode($auth_user_id);
            $role_id = Session::get('role_id');
            if ($role_id == config("global.aicenter_id")) {
                $conditions['students.aicenter_mapped_data'] = @$aicenter_mapped_data->toArray();
            }
        } else if (isset($inputs['ai_code'])) {
            $auth_user_id = @$inputs['ai_code'];
            $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode($auth_user_id);
            $conditions['students.aicenter_mapped_data'] = @$aicenter_mapped_data->toArray();
            unset($conditions['students.ai_code']);
        }

        Session::put($formId . '_conditions', $conditions);

        $master = $custom_component_obj->getallStudentPaymentData($formId);

        return view('admission_reports.allstudent_payment_details', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function downloadApplicationlocksubmitdataExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new ApplicationLockSubmitDataExlExport;
        $filename = 'allstudent_locksumbited' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function downloadallstudentzerofeespaydetailsApplicationExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new AllStudentZeroTeesPaydetailsApplicationExl;
        $filename = 'allstudent_zero_fees_pay_details' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function downloadallstudentpaymentdetailsApplicationExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new AllStudentPaymentDetailsApplicationExlExport;
        $filename = 'allstudent_payment_details' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function downloadsupplementarystudentlocksumbitedapplicationsExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new SupplementaryStudentLockSumbitedApplicationsExlExport;
        $filename = 'supplementary_student_locksumbited_applications' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function downloadallsupplementarystudentnotpaypaymentdetailsExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new SupplementaryStudentNotPayPaymentDetailsExlExport;
        $filename = 'allsupplementary_student_not_pay_payment_details' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function downloadallsupplementarystudentpaymentdetailsExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new SupplementaryStudentPaymentDetailsExlExport;
        $filename = 'allsupplementary_student_payment_details' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }


    public function student_not_pay_payment_details(Request $request)
    {

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

        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
        $table_id = "Admission_Report";
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
                "label" => "Export Excel",
                'url' => 'downloadApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => true
            ),
        );


        $filters = array(
            array(
                "lbl" => "Enrollment Number",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission Type",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium,
                'placeholder' => 'Medium Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Lock & Submit',
                'dbtbl' => 'students',
            )
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                "lbl" => "Stream",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id
            ),
            array(
                "lbl" => "Admission",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            array(
                "lbl" => "Challan Number",
                'fld' => 'challan_tid'
            ),
            array(
                "lbl" => "Submitted",
                'fld' => 'submitted'
            )
        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $conditions["students.user_id"] = @Auth::user()->id;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center'
            );
            $tableData[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'users'
            );
        }

        if (in_array("application_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => '../student/persoanl_details/#id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../student/preview_details/#id#'
                ),

            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../student/preview_details/#id#'
                ),
            );
        }

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                            $conditions[$iv['dbtbl'] . "." . $k] = $v;
                        } else {
                            $conditions[$k] = $v;
                        }
                        break;
                    }
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);

        $master = $custom_component_obj->getStudentNotPayPaymentData($formId);

        return view('admission_reports.student_not_payment_details', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }


    public function allstudent_not_pay_payment_details(Request $request)
    {

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
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);

        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
        $table_id = "Admission_Report";
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
                "label" => "Export Excel",
                'url' => 'downloadallstudent_not_pay_payment_detailsExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadallstudent_not_pay_payment_detailsExl',
                'status' => false
            ),
        );


        $filters = array(
            array(
                "lbl" => "Enrollment Number",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Mobile Number",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Mobile Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission Type",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium,
                'placeholder' => 'Medium Type',
                'dbtbl' => 'applications',
            ),
            // array(
            // 	"lbl" => "Lock & Submit",
            // 	'fld' => 'locksumbitted',
            // 	'input_type' => 'select',
            // 	'options' => $yes_no,
            // 	'placeholder' => 'Lock & Submit',
            // 	'dbtbl' => 'students',
            // )
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                "lbl" => "Stream",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id
            ),
            array(
                "lbl" => "Exam _month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month
            ),
            array(
                "lbl" => "Admission",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium
            ),
            /* array(
				"lbl" => "Lock & Submit",
				'fld' => 'locksumbitted',
				'input_type' => 'select',
				'options' => $yes_no
			),
			array(
				"lbl" => "Challan Number",
				'fld' => 'challan_tid'
			),
			array(
				"lbl" => "submitted ",
				'fld' => 'submitted'
			) */
        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $aicenter_user_id = Auth::user()->id;
            $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
            //$conditions["students.ai_code"] = $aicenter_user_ids->ai_code;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'students',
            );
            $tableData[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'users'
            );
        }

        if (in_array("application_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => '../student/persoanl_details/#id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../student/preview_details/#id#'
                ),

            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons">remove_red_eye</i>',
                    'fld_url' => '../student/preview_details/#id#'
                ),
            );
        }

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        if ($isAdminStatus == false) {
            $aicenter_user_id = Auth::user()->id;
            $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
            $auth_user_id = $aicenter_user_ids->ai_code;
            $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode($auth_user_id);
            $role_id = Session::get('role_id');
            if ($role_id == config("global.aicenter_id")) {
                $conditions['students.aicenter_mapped_data'] = @$aicenter_mapped_data->toArray();
            }
        } else if (isset($inputs['ai_code'])) {
            $auth_user_id = @$inputs['ai_code'];
            $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode($auth_user_id);
            $conditions['students.aicenter_mapped_data'] = @$aicenter_mapped_data->toArray();
            unset($conditions['students.ai_code']);
        }
        Session::put($formId . '_conditions', $conditions);

        $master = $custom_component_obj->allgetStudentNotPayPaymentData($formId);

        return view('admission_reports.allstudent_not_payment_details', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }


    public function downloadallstudent_not_pay_payment_detailsExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new AllStudentNotPayPaymentDetailsExlExport;
        $filename = 'allstudent_not_pay_payment_detail' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }


    public function allstudent_zero_fees_pay_details(Request $request)
    {

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
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);

        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
        $table_id = "Admission_Report";
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
                "label" => "Export Excel",
                'url' => 'downloadallstudentzerofeespaydetailsApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => false,
            ),
        );

        $actions = array();
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


        $filters = array(
            array(
                "lbl" => "Enrollment Number",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month,
                'placeholder' => 'Exam month',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission Type",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium,
                'placeholder' => 'Medium Type',
                'dbtbl' => 'applications',
            ),
            // array(
            // 	"lbl" => "Lock & Submit",
            // 	'fld' => 'locksumbitted',
            // 	'input_type' => 'select',
            // 	'options' => $yes_no,
            // 	'placeholder' => 'Lock & Submit',
            // 	'dbtbl' => 'students',
            // )
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                "lbl" => "Stream",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month
            ),
            array(
                "lbl" => "Admission",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium
            ),
            /* array(
				"lbl" => "Lock & Submit",
				'fld' => 'locksumbitted',
				'input_type' => 'select',
				'options' => $yes_no
			),
			array(
				"lbl" => "Challan Number",
				'fld' => 'challan_tid'
			),
			array(
				"lbl" => "submitted ",
				'fld' => 'submitted'
			) */
        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $aicenter_user_id = Auth::user()->id;
            $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
            //$conditions["students.ai_code"] = $aicenter_user_ids->ai_code;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center'
            );
            $tableData[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'users'
            );
        }

        if (in_array("application_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => '../student/persoanl_details/#id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../student/preview_details/#id#'
                ),

            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../student/preview_details/#id#'
                ),
            );
        }

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }

        if ($isAdminStatus == false) {
            $aicenter_user_id = Auth::user()->id;
            $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
            $auth_user_id = $aicenter_user_ids->ai_code;
            $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode($auth_user_id);
            $role_id = Session::get('role_id');
            if ($role_id == config("global.aicenter_id")) {
                $conditions['students.aicenter_mapped_data'] = @$aicenter_mapped_data->toArray();
            }
        } else if (isset($inputs['ai_code'])) {
            $auth_user_id = @$inputs['ai_code'];
            $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode($auth_user_id);
            $conditions['students.aicenter_mapped_data'] = @$aicenter_mapped_data->toArray();
            unset($conditions['students.ai_code']);
        }

        Session::put($formId . '_conditions', $conditions);

        $master = $custom_component_obj->allgetStudentzerofeesPaymentData($formId);

        return view('admission_reports.allstudent_not_payment_details', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'actions'))->withInput($request->all());
    }

    public function verifying_student_applications(Request $request)
    {
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
        $combo_name = 'exam_month';
        $exam_months = $this->master_details($combo_name);

        $combo_name = 'fresh_student_verfication_status';
        $fresh_student_verfication_status = $this->master_details($combo_name);

        $yes_no_yes_only = $yes_no = $this->master_details('yesno');
        unset($yes_no_yes_only[0]);
        $title = "Fresh Student Verification";
        $table_id = "verifying_student_applications";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $custom_component_obj = new CustomComponent;
        $aiCenters = array();
        $role_id = Session::get('role_id');
        $verifier_id = Config::get("global.verifier_id");
        $super_admin_id = Config::get("global.super_admin_id");
        $academicofficer_id = Config::get("global.academicofficer_id");
        if ($role_id == $verifier_id) {
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            // dd($auth_user_id);
            $aiCenters = $custom_component_obj->getAiCentersForVerifier($auth_user_id);
        } else if ($role_id == $academicofficer_id) {
            $auth_user_id = Auth::user()->id;
            $aiCenters = $custom_component_obj->getAiCenters(null, null, 1);

            $aoAicodes = $custom_component_obj->getAOMappedAiCodes($auth_user_id);

            $finalList = array();
            foreach ($aiCenters as $k => $v) {
                if (@$aoAicodes && in_array($k, $aoAicodes)) {
                    $finalList[$k] = $v;
                }
            }
            $aiCenters = collect($finalList);
        } else {
            $aiCenters = $custom_component_obj->getAiCenters(null, null, 1);
        }
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
        $exportBtn = array();
        if ($super_admin_id == $role_id) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadFreshStudentVerifyingExl',
                    'status' => true,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        }

        $verifiers = collect();
        if ($role_id == $academicofficer_id) {
            //verifiers whose ai code same as verifier ao code
        } else {
            $verifier_id = config("global.verifier_id");
            $verifiers = User::WhereHas('userrole', function ($query) use ($verifier_id) {
                $query->where('role_id', $verifier_id);
            })->pluck("ssoid", "id");
        }


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Code',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            // array(
            // 	"lbl" => "Stream ",
            // 	'fld' => 'stream',
            // 	'input_type' => 'select',
            // 	'options' => $stream_id,
            // 	'placeholder' => 'Stream Type',
            // 	'dbtbl' => 'students',
            // ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium,
                'placeholder' => 'Medium Type',
                'dbtbl' => 'applications',
            ),
            array(
                "lbl" => "Is Verifier Verify",
                'fld' => 'verifier_status',
                'input_type' => 'select',
                'options' => $fresh_student_verfication_status,
                'placeholder' => 'Is Verifier Verify',
                'dbtbl' => 'students',
            ),
            // array(
            // 	"lbl" => "Lock & Submit",
            // 	'fld' => 'locksumbitted',
            // 	'input_type' => 'select',
            // 	'options' => $yes_no,
            // 	'placeholder' => 'Lock & Submit',
            // 	'dbtbl' => 'applications',
            // ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_months,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'students',
            ),
        );
        $role_id = Session::get('role_id');

        if ($role_id == config("global.super_admin_id") || $role_id == config("global.developer_admin") || $role_id == config("global.academicofficer_id")) {
            $filters[] = array(
                "lbl" => "Is Department Verify",
                'fld' => 'department_status',
                'input_type' => 'select',
                'options' => $fresh_student_verfication_status,
                'placeholder' => 'Is Department Verify',
                'dbtbl' => 'students'
            );
            $filters[] = array(
                "lbl" => "Is AO Verify",
                'fld' => 'ao_status',
                'input_type' => 'select',
                'options' => $fresh_student_verfication_status,
                'placeholder' => 'Is AO Verify',
                'dbtbl' => 'students'
            );
            $filters[] = array(
                "lbl" => "Is Permanent Rejected By Department",
                'fld' => 'is_permanent_rejected_by_dept',
                'input_type' => 'select',
                'options' => $yes_no_yes_only,
                'placeholder' => 'Is Permanent Rejected By Department',
                'dbtbl' => 'student_verifications'
            );
        }

        if ($role_id == config("global.verifier_admin_id")) {
            $filters[] = array(
                "lbl" => "Verifier",
                'fld' => 'verifier_aicode_user_id',
                'input_type' => 'select',
                'options' => $verifiers,
                'placeholder' => 'Verifier',
                'dbtbl' => 'students'
            );
        }


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Is Verifier Verify",
                'fld' => 'verifier_status',
                'input_type' => 'select',
                'options' => $fresh_student_verfication_status,
                'placeholder' => 'Is Verifier Verify',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Verifier",
                'fld' => 'verifier_status',
                'input_type' => 'select',
                'options' => $verifiers,
                'placeholder' => 'Verifier',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ), array(
                "lbl" => "AI Code",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'placeholder' => "AI Code",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Mobile Number",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Mobile Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course ",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                "lbl" => "Stream ",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            )
        );


        if ($role_id == config("global.super_admin_id") || $role_id == config("global.developer_admin") || $role_id == config("global.academicofficer_id")) {
            $tableData[] = array(
                "lbl" => "Is AO Verify",
                'fld' => 'ao_status',
                'input_type' => 'select',
                'options' => $fresh_student_verfication_status,
                'placeholder' => 'Is AO Verify',
                'dbtbl' => 'students',
            );

            $tableData[] = array(
                "lbl" => "Is Department Verify",
                'fld' => 'department_status',
                'input_type' => 'select',
                'options' => $fresh_student_verfication_status,
                'placeholder' => 'Is Department Verify',
                'dbtbl' => 'students'
            );

        }

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();
        $conditions["applications.is_ready_for_verifying"] = 1;


        if ($role_id == config("global.super_admin_id")) {
            // $conditions["students.verifier_status"] = array(2,5,6);
        }
        $conditions["applications.locksumbitted"] = 1;

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            Session::put("ai_code", $auth_user_id);
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
            $conditions["students.aicenter_mapped_data"] = $aicenter_mapped_data;
        } else {
        }
        $actions = array();
        if (in_array("document_verification_of_student", $permissions)) {
            $actions[] = array(
                'fld' => 'verify_documents',
                'extraCondition' => 'student_applications',
                'icon' => '<i title="Verify the documents" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Verify</i>',
                'fld_url' => '../student/verify_documents/#id#'
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        // echo "ttt";
        if (@$inputs['extra'] && !empty($inputs['extra'])) {
            $conditions['extra'] = $inputs['extra'];
        }

        if (@$inputs['stage'] && !empty($inputs['stage'])) {
            $conditions['stage'] = $inputs['stage'];
        }

        //dd($conditions["students.verifier_status"]);
        //dd($formId . '_conditions'); //Fresh_Student_Verificaiton_conditions
        Session::put($formId . '_conditions', $conditions); 
        Session::put('tempCond', $conditions);
		
        $master = $custom_component_obj->getVerifyStudentData($formId);
        Session::put('temp_url_for_back', \Request::getRequestUri());
        return view('admission_reports.verifying_student_applications', compact('verifiers', 'tableData', 'actions', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'course', 'adm_types', 'stream_id', 'fresh_student_verfication_status', 'role_id'))->withInput($request->all());
    }

    public function student_applications(Request $request)
    {
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
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $combo_name = 'faculties';
        $faculties = $this->master_details($combo_name);
        $combo_name = 'yes_no_2';
        $yes_no_2 = $this->master_details($combo_name);
        $combo_name = 'doc_verification_status';
        $doc_verification_status = $this->master_details($combo_name);
        $combo_name = 'fresh_student_verfication_status';
        $fresh_student_verfication_status = $this->master_details($combo_name);
        $district_list = $this->districtsByState();
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $fields = array('applications.locksumbitted');
        $defaultPageLimit = config("global.defaultPageLimit");
        // $staundtdata = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where('applications.locksumbitted',1)->paginate($defaultPageLimit,$fields);
        $yes_no = $this->master_details('yesno');
        $yes_no_temp = $this->master_details('yesno');


        //$feePaymentAllowOrNot = $this->_checkPaymentAllowedOrNot($stream);
        //$feePaymentAllowOrNot = json_decode($feePaymentAllowOrNot);
        //$feePaymentAllowOrNotStatus = $feePaymentAllowOrNot->status;


        //dd($feePaymentAllowOrNotStatus);

        $yes_no_temp[""] = "No";
        $isDocVerified = $doc_verification_status;
        $title = "Admission Report";
        $table_id = "Admission_Report";
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
        if (in_array("download_Application_Exl", $permissions)) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadApplicationExl',
                    'status' => true,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        } else {
            $exportBtn = array();
        }
        $role_id = @Session::get('role_id');
        $Printer = config("global.Printer");
        if ($role_id != $Printer) {
            $filters = array(
                array(
                    "lbl" => "Start date",
                    'fld' => 'start_date',
                    'input_type' => 'datetime-local',
                    'placeholder' => "Start Date",
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "End date",
                    'fld' => 'end_date',
                    'input_type' => 'datetime-local',
                    'placeholder' => "End Date",
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Enrollment",
                    'fld' => 'enrollment',
                    'input_type' => 'text',
                    'placeholder' => "Enrollment Number",
                    'search_type' => "text",
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Amount",
                    'fld' => 'fee_paid_amount',
                    'input_type' => 'text',
                    'placeholder' => "Amount",
                    'search_type' => "text",
                    'dbtbl' => 'applications',
                ),
                array(
                    "lbl" => "Name",
                    'fld' => 'name',
                    'input_type' => 'text',
                    'placeholder' => "Student Name",
                    'search_type' => "like", //like
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Challan Number",
                    'fld' => 'challan_tid',
                    'input_type' => 'text',
                    'placeholder' => "Challan Number",
                    'search_type' => "text", //like
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Gender",
                    'fld' => 'gender_id',
                    'input_type' => 'select',
                    'options' => $gender_id,
                    'search_type' => "text",
                    'placeholder' => 'Gender Type',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Course",
                    'fld' => 'course',
                    'input_type' => 'select',
                    'options' => $course,
                    'search_type' => "text",
                    'placeholder' => 'Course Type',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Stream ",
                    'fld' => 'stream',
                    'input_type' => 'select',
                    'options' => $stream_id,
                    'search_type' => "text",
                    'placeholder' => 'Stream Type',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Admission ",
                    'fld' => 'adm_type',
                    'input_type' => 'select',
                    'options' => $adm_types,
                    'search_type' => "text",
                    'placeholder' => 'Admission Type',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Medium",
                    'fld' => 'medium',
                    'input_type' => 'select',
                    'search_type' => "text",
                    'options' => $midium,
                    'placeholder' => 'Medium Type',
                    'dbtbl' => 'applications',
                ),
                array(
                    "lbl" => "Lock & Submit",
                    'fld' => 'locksumbitted',
                    'input_type' => 'select',
                    'options' => $yes_no,
                    'search_type' => "text",
                    'placeholder' => 'Lock & Submit',
                    'dbtbl' => 'applications',
                ),
                array(
                    "lbl" => "Toc Yes or No",
                    'fld' => 'toc',
                    'input_type' => 'select',
                    'options' => $yes_no,
                    'search_type' => "text",
                    'placeholder' => 'Toc Yes or No',
                    'dbtbl' => 'applications',
                ),
                array(
                    "lbl" => "Enrollment Genrate",
                    'fld' => 'enrollmentgen',
                    'class' => 'enrollment',
                    'input_type' => 'select',
                    'options' => $yes_no,
                    'search_type' => "text",
                    'placeholder' => 'Enrollment Genrate',
                    'dbtbl' => 'students',
                ),

                array(
                    "lbl" => "Is Eligible",
                    'fld' => 'is_eligible',
                    'input_type' => 'select',
                    'options' => $yes_no,
                    'search_type' => "text",
                    'placeholder' => 'Is Eligible',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Exam Month",
                    'fld' => 'exam_month',
                    'input_type' => 'select',
                    'options' => $exam_month,
                    'search_type' => "text",
                    'placeholder' => 'Exam Month',
                    'dbtbl' => 'students',
                ),

                /*array(
				"lbl" => "Is Valid For Fee Pay",
				'fld' => 'is_valid_for_fee_pay',
				'input_type' => 'select',
				'options' => $yes_no,
				'search_type' => "text",
				'placeholder' => 'Is Valid For Fee Pay',
				'dbtbl' => 'student_fees',
			),*/
                /*array(
				"lbl" => "District",
				'fld' => 'district_id',
				'input_type' => 'select',
				'options' => $district_list,
				'placeholder' => 'District',
				'search_type' => "text",
				'dbtbl' => 'students',
			),*/
                array(
                    "lbl" => "Are You From Rajasthan And Not",
                    'fld' => 'are_you_from_rajasthan',
                    'input_type' => 'select',
                    'options' => $are_you_from_rajasthan,
                    'search_type' => "text",
                    'placeholder' => 'Are You From Rajasthan And Not',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Is Self Filled",
                    'fld' => 'is_self_filled',
                    'input_type' => 'select',
                    'options' => $yes_no,
                    'search_type' => "text",
                    'placeholder' => 'Is Self Filled',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "SSO",
                    'fld' => 'ssoid',
                    'input_type' => 'text',
                    'placeholder' => "SSO",
                    'search_type' => "like", //like
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Preferred Faculty",
                    'fld' => 'faculty_type_id',
                    'input_type' => 'select',
                    'options' => $faculties,
                    'search_type' => "text",
                    'placeholder' => 'Preferred Faculty',
                    'dbtbl' => 'applications',
                ),
                array(
                    "lbl" => "Multiple Faculty Subjects",
                    'fld' => 'is_multiple_faculty',
                    'input_type' => 'select',
                    'options' => $yes_no,
                    'search_type' => "text",
                    'placeholder' => 'Multiple Faculty Subjects',
                    'dbtbl' => 'applications',
                ),
                array(
                    "lbl" => "Is SSO Filled",
                    'fld' => 'isssoid',
                    'input_type' => 'select',
                    'options' => $yes_no_2,
                    'search_type' => "text",
                    'placeholder' => 'Is SSO Filled',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Is Mobile Verified",
                    'fld' => 'is_otp_verified',
                    'input_type' => 'select',
                    'options' => $yes_no,
                    'search_type' => "text",
                    'placeholder' => 'Is Mobile Verified',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Is Verify Status",
                    'fld' => 'ao_status',
                    'input_type' => 'select',
                    'options' => $fresh_student_verfication_status,
                    'placeholder' => 'Is Verify Status',
                    'dbtbl' => 'students'
                ),
                array(
                    "lbl" => "Disadvantage Group student",
                    'fld' => 'is_dgs',
                    'input_type' => 'select',
                    'options' => $yes_no,
                    'placeholder' => 'Disadvantage Group student',
                    'dbtbl' => 'students'
                ),


            );
        } else {
            $filters = array(
                array(
                    "lbl" => "Start date",
                    'fld' => 'start_date',
                    'input_type' => 'datetime-local',
                    'placeholder' => "Start Date",
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "End date",
                    'fld' => 'end_date',
                    'input_type' => 'datetime-local',
                    'placeholder' => "End Date",
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Enrollment",
                    'fld' => 'enrollment',
                    'input_type' => 'text',
                    'placeholder' => "Enrollment Number",
                    'search_type' => "text",
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Amount",
                    'fld' => 'fee_paid_amount',
                    'input_type' => 'text',
                    'placeholder' => "Amount",
                    'search_type' => "text",
                    'dbtbl' => 'applications',
                ),
                array(
                    "lbl" => "Name",
                    'fld' => 'name',
                    'input_type' => 'text',
                    'placeholder' => "Student Name",
                    'search_type' => "like", //like
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Challan Number",
                    'fld' => 'challan_tid',
                    'input_type' => 'text',
                    'placeholder' => "Challan Number",
                    'search_type' => "text", //like
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Gender",
                    'fld' => 'gender_id',
                    'input_type' => 'select',
                    'options' => $gender_id,
                    'search_type' => "text",
                    'placeholder' => 'Gender Type',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Course",
                    'fld' => 'course',
                    'input_type' => 'select',
                    'options' => $course,
                    'search_type' => "text",
                    'placeholder' => 'Course Type',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Stream ",
                    'fld' => 'stream',
                    'input_type' => 'select',
                    'options' => $stream_id,
                    'search_type' => "text",
                    'placeholder' => 'Stream Type',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Admission ",
                    'fld' => 'adm_type',
                    'input_type' => 'select',
                    'options' => $adm_types,
                    'search_type' => "text",
                    'placeholder' => 'Admission Type',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Medium",
                    'fld' => 'medium',
                    'input_type' => 'select',
                    'search_type' => "text",
                    'options' => $midium,
                    'placeholder' => 'Medium Type',
                    'dbtbl' => 'applications',
                ),
                /*array(
				"lbl" => "Lock & Submit",
				'fld' => 'locksumbitted',
				'input_type' => 'select',
				'options' => $yes_no,
				'search_type' => "text",
				'placeholder' => 'Lock & Submit',
				'dbtbl' => 'applications',
			),*/
                array(
                    "lbl" => "Toc Yes or No",
                    'fld' => 'toc',
                    'input_type' => 'select',
                    'options' => $yes_no,
                    'search_type' => "text",
                    'placeholder' => 'Toc Yes or No',
                    'dbtbl' => 'applications',
                ),
                /*array(
				"lbl" => "Enrollment Genrate",
				'fld' => 'enrollmentgen',
				'class'=>'enrollment',
				'input_type' => 'select',
				'options' => $yes_no,
				'search_type' => "text",
				'placeholder' => 'Enrollment Genrate',
				'dbtbl' => 'students',
			),*/

                /*array(
				"lbl" => "Is Eligible",
				'fld' => 'is_eligible',
				'input_type' => 'select',
				'options' => $yes_no,
				'search_type' => "text",
				'placeholder' => 'Is Eligible',
				'dbtbl' => 'students',
			),*/
                array(
                    "lbl" => "Exam Month",
                    'fld' => 'exam_month',
                    'input_type' => 'select',
                    'options' => $exam_month,
                    'search_type' => "text",
                    'placeholder' => 'Exam Month',
                    'dbtbl' => 'students',
                ),

                /*array(
				"lbl" => "Is Valid For Fee Pay",
				'fld' => 'is_valid_for_fee_pay',
				'input_type' => 'select',
				'options' => $yes_no,
				'search_type' => "text",
				'placeholder' => 'Is Valid For Fee Pay',
				'dbtbl' => 'student_fees',
			),*/
                /*array(
				"lbl" => "District",
				'fld' => 'district_id',
				'input_type' => 'select',
				'options' => $district_list,
				'placeholder' => 'District',
				'search_type' => "text",
				'dbtbl' => 'students',
			),*/
                array(
                    "lbl" => "Are You From Rajasthan And Not",
                    'fld' => 'are_you_from_rajasthan',
                    'input_type' => 'select',
                    'options' => $are_you_from_rajasthan,
                    'search_type' => "text",
                    'placeholder' => 'Are You From Rajasthan And Not',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Is Self Filled",
                    'fld' => 'is_self_filled',
                    'input_type' => 'select',
                    'options' => $yes_no,
                    'search_type' => "text",
                    'placeholder' => 'Is Self Filled',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "SSO",
                    'fld' => 'ssoid',
                    'input_type' => 'text',
                    'placeholder' => "SSO",
                    'search_type' => "like", //like
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Preferred Faculty",
                    'fld' => 'faculty_type_id',
                    'input_type' => 'select',
                    'options' => $faculties,
                    'search_type' => "text",
                    'placeholder' => 'Preferred Faculty',
                    'dbtbl' => 'applications',
                ),
                array(
                    "lbl" => "Multiple Faculty Subjects",
                    'fld' => 'is_multiple_faculty',
                    'input_type' => 'select',
                    'options' => $yes_no,
                    'search_type' => "text",
                    'placeholder' => 'Multiple Faculty Subjects',
                    'dbtbl' => 'applications',
                ),
                array(
                    "lbl" => "Is SSO Filled",
                    'fld' => 'isssoid',
                    'input_type' => 'select',
                    'options' => $yes_no_2,
                    'search_type' => "text",
                    'placeholder' => 'Is SSO Filled',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Is Mobile Verified",
                    'fld' => 'is_otp_verified',
                    'input_type' => 'select',
                    'options' => $yes_no,
                    'search_type' => "text",
                    'placeholder' => 'Is Mobile Verified',
                    'dbtbl' => 'students',
                ),
                array(
                    "lbl" => "Is Verify Status",
                    'fld' => 'ao_status',
                    'input_type' => 'select',
                    'options' => $fresh_student_verfication_status,
                    'placeholder' => 'Is Verify Status',
                    'dbtbl' => 'students'
                ),
				array(
                    "lbl" => "Is Department Status",
                    'fld' => 'department_status',
                    'input_type' => 'select',
                    'options' => $fresh_student_verfication_status,
                    'placeholder' => 'Is Department Status',
                    'dbtbl' => 'students'
                ),


            );
        }

        if (in_array("complete_full_year_student_applications", $permissions)) {
            $filters[] = array(
                "lbl" => "All Years Student Report",
                'fld' => 'is_full',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'All Years Student Report',
                'dbtbl' => 'students',
            );
        }

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        if ($isAdminStatus == true) {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'students',
            );

            $filters[] = array(
                "lbl" => "Jan Addhar Number",
                'fld' => 'jan_aadhar_number',
                'input_type' => 'text',
                'placeholder' => "Jan Addhar Number",
                'search_type' => "text",
                'dbtbl' => 'applications',

            );

            $filters[] = array(
                "lbl" => "Aadhar card",
                'fld' => 'aadhar_number',
                'input_type' => 'text',
                'placeholder' => "Aadhar card number",
                'search_type' => "text",
                'dbtbl' => 'applications',

            );

            $filters[] = array(
                "lbl" => "Jan Member id",
                'fld' => 'jan_id',
                'input_type' => 'text',
                'placeholder' => "Jan id",
                'search_type' => "text",
                'dbtbl' => 'applications',

            );
        }

        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "AI Code",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'placeholder' => "AI Code",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'placeholder' => "Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course ",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                "lbl" => "Exam Year",
                'fld' => 'exam_year',
                'input_type' => 'select',
                'options' => $admission_sessions
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month
            ),

            array(
                "lbl" => "Is Self Filled",
                'fld' => 'is_self_filled',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no_temp
            ),
            array(
                "lbl" => "Is Eligible",
                'fld' => 'is_eligible',
                'input_type' => 'select',
                'options' => $yes_no_temp
            ),
            array(
                "lbl" => "Fees Amount",
                'fld' => 'fee_paid_amount'
            ),
            array(
                "lbl" => "Challan Number",
                'fld' => 'challan_tid'
            ),
            array(
                "lbl" => "Submitted",
                'fld' => 'submitted'
            ),
            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district_list
            ),
            array(
                "lbl" => "Is Mobile Verified",
                'fld' => 'is_otp_verified',
                'input_type' => 'select',
                'options' => $yes_no_temp
            ),
            array(
                "lbl" => "Doc Verified",
                'fld' => 'is_verified',
                'input_type' => 'select',
                'options' => $isDocVerified
            ),
            array(
                "lbl" => "Both same Address",
                'fld' => 'is_both_same',
                'input_type' => 'select',
                'options' => $yes_no_temp
            ),
            array(
                "lbl" => "Is Verify Status",
                'fld' => 'ao_status',
                'input_type' => 'select',
                'options' => $fresh_student_verfication_status,
                'notExtraCls' => true,
                'fld_url' => "../student/verification_trail/#id#",
                'placeholder' => 'Is Verify Status',

            ),
        );
        $ssoUpdateShowMessage = false;
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();


        $role_id = @Session::get('role_id');
        if ($role_id != 65) {
            if (in_array("application_dashboard", $permissions)) {
                $actions = array(
                    array(
                        'fld' => 'view',
                        'extraCondition' => 'student_applications',
                        'icon' => '<i title="View Student Profile"  class="material-icons">perm_identity</i>',
                        'fld_url' => '../student/student_history_details/#id#'
                    ),
                    array(
                        'fld' => 'edit',
                        'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                        'fld_url' => '../student/persoanl_details/#id#'
                    ),
                    array(
                        'fld' => 'view',
                        'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                        'fld_url' => '../student/preview_details/#id#'
                    ),
                    array(
                        'fld' => 'view',
                        'icon' => '<i class="waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text secondary-content" title="Click here to Update TOC.">TOC</i>',
                        'fld_url' => '../student/dev_toc_subject_details/#id#'
                    ),
                    //array(
                    //'fld' => 'view',
                    //'icon' => '<i class="waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text //secondary-content">Verify</i>',
                    //'fld_url' =>'../student/verify_documents/#id#'
                    //),
                );
            } elseif (in_array("printerupdatestudentdata", $permissions)) {
                $actions = array(
                    array(
                        'fld' => 'printedit',
                        'extraCondition' => 'student_applications',
                        'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                        'fld_url' => '../student/printupdatestudentdetalis/#id#'
                    ),
                );
            } else {
                $role_id = Session::get('role_id');
                if ($role_id == config("global.aicenter_id")) {

                } else {
                    $actions = array(
                        array(
                            'fld' => 'view',
                            'extraCondition' => 'student_applications',
                            'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                            'fld_url' => '../student/preview_details/#id#'
                        ),
                    );
                }

                // }
                // }
            }
        } else {
            $actions = array();
        }


        if (in_array("update_student_details", $permissions)) {
            $actions[] = array(
                'fld' => 'view',
                'extraCondition' => 'student_applications',
                'icon' => '<i class="waves-effect waves-teal btn gradient-45deg-deep-blue-blue white-text secondary-cont.ent" title="Click here to Update SSO.">SSO</i>',
                'fld_url' => '../student/update_basic_details/#id#'
            );
            $ssoUpdateShowMessage = true;
        }

        if (in_array("mobile_number_details_edit", $permissions)) {
            $actions[] = array(
                'fld' => 'view',
                'extraCondition' => 'student_applications',
                'icon' => '<i class="material-icons" title="Click here to Update Mobile Number.">phone_android</i>',
                'fld_url' => '../student/mobile_number_details_edit/#id#'
            );
            $ssoUpdateShowMessage = true;
        }

        if (in_array("student_remove_ssoid", $permissions)) {
            $actions[] = array(
                'fld' => 'view',
                'extraCondition' => 'student_applications',
                'icon' => '<i class="material-icons" title="Click here to remove filled ssoid">remove_circle_outline</i>',
                'fld_url' => '../student/student_remove_ssoid/#id#'
            );
            $ssoUpdateShowMessage = true;
        }

        if (in_array("student_mark_reject", $permissions)) {
            $actions[] = array(
                'fld' => 'view',
                'icon' => '<i class="waves-effect waves-teal btn gradient-45deg-deep-blue-blue white-text secondary-content" title="Click here to MarkReject.">MarkReject</i>',
                'fld_url' => '../student/student_mark_reject/#id#'
            );
            $ssoUpdateShowMessage = true;
        }
        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        /* Sorting Fields Set Start 1*/
        $sorting = array();
        $orderByRaw = "";
        $inputs = "";
        $sortingField = $this->_getSortingFields($filters);
        /* Sorting Fields Set End 1*/
        $symbol = null;
        $symbols = null;
        $symbolis = null;
        if ($request->all()) {

            $inputs = $request->all();

            if (@$inputs['is_full'] && $inputs['is_full'] == 1) {
                if ($isAdminStatus == true) {
                    unset($conditions["students.exam_year"]);
                }
            }
            foreach ($inputs as $k => $v) {
                if ($k == 'is_full') {
                    continue;
                }
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (@$iv['fld'] == $k && $iv['fld'] == $k) {
                            if ($iv['fld'] == 'enrollmentgen' && $inputs[$iv['fld']] == 1) {
                                $symbol = "!=";
                            } elseif ($iv['fld'] == 'enrollmentgen' && $inputs[$iv['fld']] == 0) {
                                $symbol = "=";
                            } else {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            }

                            if ($iv['fld'] == 'is_otp_verified' && $inputs[$iv['fld']] == 1) {
                                $symbolis = "!=";
                            } elseif ($iv['fld'] == 'is_otp_verified' && $inputs[$iv['fld']] == 0) {
                                $symbolis = "=";
                            } else {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            }
                            if ($iv['fld'] == 'is_self_filled' && $inputs[$iv['fld']] == 1) {
                                $symbols = "!=";
                            } elseif ($iv['fld'] == 'is_self_filled' && $inputs[$iv['fld']] == 0) {
                                $symbols = "=";
                            } else {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            }
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    //$conditions[ $iv['dbtbl'] . "." . $k] = $v;
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }

            /* Sorting Order By Set Start 2*/
            $orderByRaw = $this->_setSortingArrayFields(@$inputs['sorting'], $sortingField);

            /* Sorting Order By Set End 2*/
        }

        if ($isAdminStatus == false) {
            @$aicenter_user_id = Auth::user()->id;
            @$aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
            @$auth_user_id = $aicenter_user_ids->ai_code;
            @$aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode($auth_user_id);
            $role_id = Session::get('role_id');
            if ($role_id == config("global.aicenter_id")) {
                $conditions['students.aicenter_mapped_data'] = @$aicenter_mapped_data->toArray();
            }
        } else if (isset($inputs['ai_code'])) {
            $auth_user_id = @$inputs['ai_code'];
            @$aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode(@$auth_user_id);

            $conditions['students.aicenter_mapped_data'] = @$aicenter_mapped_data->toArray();
            unset($conditions['students.ai_code']);
        }


        //@dd($orderByRaw);
        /* Sorting Fields Set Session Start 3*/
        Session::put($formId . '_orderByRaw', $orderByRaw);
        /* Sorting Fields Set Session End 3*/
        Session::put($formId . '_conditions', $conditions);
        Session::put($formId . '_symbol', $symbol);
        Session::put($formId . '_symbols', $symbols);
        Session::put($formId . 'symbolis', $symbolis);


        $master = $custom_component_obj->getApplicationData($formId);

        return view('admission_reports.student_applications', compact('ssoUpdateShowMessage', 'sortingField', 'yes_no_temp', 'are_you_from_rajasthan', 'district_list', 'actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function student_locksumbited_applications(Request $request)
    {
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

        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
        $table_id = "Admission_Report";
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
                "label" => "Export Excel",
                'url' => 'downloadApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => true
            ),
        );


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'placeholder' => "Ai Code",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Stream ",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium,
                'placeholder' => 'Medium Type',
                'dbtbl' => 'applications',
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Lock & Submit',
                'dbtbl' => 'applications',
            )
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ), array(
                "lbl" => "AI Code",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'placeholder' => "AI Code",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course ",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                "lbl" => "Stream ",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no
            ),

        );
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $conditions["students.user_id"] = @Auth::user()->id;
        } else {

        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                            $conditions[$iv['dbtbl'] . "." . $k] = $v;
                        } else {
                            $conditions[$k] = $v;
                        }
                        break;
                    }
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);

        $master = $custom_component_obj->getlocksumbittedStudentData($formId);

        return view('admission_reports.student_locksumbited_applications', compact('tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function allstudent_locksumbited_applications(Request $request)
    {
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
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);

        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
        $table_id = "Admission_Report";
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
                "label" => "Export Excel",
                'url' => 'downloadApplicationlocksubmitdataExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => false,
            ),
        );

        $actions = array();
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


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month,
                'placeholder' => "Exam month",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => "Ai Code",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Stream ",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium,
                'placeholder' => 'Medium Type',
                'dbtbl' => 'applications',
            ),
            // array(
            // 	"lbl" => "Lock & Submit",
            // 	'fld' => 'locksumbitted',
            // 	'input_type' => 'select',
            // 	'options' => $yes_no,
            // 	'placeholder' => 'Lock & Submit',
            // 	'dbtbl' => 'students',
            // )
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ), array(
                "lbl" => "AI Code",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'placeholder' => "AI Code",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course ",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                "lbl" => "Stream ",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id
            ),
            array(
                "lbl" => "Exam Month ",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            ),
            // array(
            // 	"lbl" => "Lock & Submit",
            // 	'fld' => 'locksumbitted',
            // 	'input_type' => 'select',
            // 	'options' => $yes_no
            // ),

        );
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();


        if (in_array("application_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => '../student/persoanl_details/#id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../student/preview_details/#id#'
                ),

            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../student/preview_details/#id#'
                ),
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                            $conditions[$iv['dbtbl'] . "." . $k] = $v;
                        } else {
                            $conditions[$k] = $v;
                        }
                        break;
                    }
                }
            }
        }


        if ($isAdminStatus == false) {
            $aicenter_user_id = Auth::user()->id;
            $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
            $auth_user_id = $aicenter_user_ids->ai_code;
            $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode($auth_user_id);
            $role_id = Session::get('role_id');
            if ($role_id == config("global.aicenter_id")) {
                $conditions['students.aicenter_mapped_data'] = @$aicenter_mapped_data->toArray();
            }
        } else if (isset($inputs['ai_code'])) {
            $auth_user_id = @$inputs['ai_code'];
            $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode($auth_user_id);
            $conditions['students.aicenter_mapped_data'] = @$aicenter_mapped_data->toArray();
            unset($conditions['students.ai_code']);
        }

        Session::put($formId . '_conditions', $conditions);

        $master = $custom_component_obj->allgetlocksumbittedStudentData($formId);


        return view('admission_reports.allstudent_locksumbited_applications', compact('tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'actions'))->withInput($request->all());
    }

    public function downloadApplicationExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new ApplicationExlExport;
        $filename = 'application_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function downloadFreshStudentVerifyingExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new FreshStudentVerifyingExlExport;
        $filename = 'Fresh_Student_Verifying' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function downloadSupplementaryApplicationExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new SupplementaryApplicationExlExport;
        $filename = 'supplementaryapplication_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function downloadApplicationPdf(Request $request)
    {
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
        $output = array();
        $formId = "Admission_Report";
        $reportname = 'Student Applications Report';
        $custom_component_obj = new CustomComponent;
        $result = $custom_component_obj->getApplicationData($formId, false);
        $fileName = $formId . "_" . date("dmY his");
        //return view('admission_reports.reporting_application_pdf',compact('result',
        //'gender_id','midium','course','stream_id','adm_types','reportname'));
        $pdf = PDF::loadView('admission_reports.reporting_application_pdf', compact('result',
            'gender_id', 'midium', 'course', 'stream_id', 'adm_types', 'reportname'));
        return $pdf->download($fileName . '.pdf');
    }

    public function downloadSupplementaryApplicationPdf(Request $request)
    {
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
        $output = array();
        $formId = "Supplementary_Applicaiton_Report";
        $reportname = 'Student Applications Report';
        $custom_component_obj = new CustomComponent;
        $result = $custom_component_obj->getSupplementaryApplicationData($formId, false);
        $fileName = $formId . "_" . date("dmY his");
        //return view('admission_reports.reporting_application_pdf',compact('result',
        //'gender_id','midium','course','stream_id','adm_types','reportname'));
        $pdf = PDF::loadView('admission_reports.Supplementary_reporting_application_pdf', compact('result',
            'gender_id', 'midium', 'course', 'stream_id', 'adm_types', 'reportname'));
        return $pdf->download($fileName . '.pdf');
    }


    public function student_application_ai_center_wise_count(Request $request)
    {
        $custom_component_obj = new CustomComponent;
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

        $yes_no = $this->master_details('yesno');
        $title = "AiCenters Wise Admission Report";
        $table_id = "AiCenters_Wise_Admission_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        if ($isAdminStatus) {
            $aiCenters = $custom_component_obj->getAiCenters();
        } else {
            $role_id = Session::get('role_id');
            $currentUserAiCode = @Auth::user()->ai_code;
            $aiCenters = $custom_component_obj->getAiCenters($currentUserAiCode);
        }
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

        $exportBtn = array();

        $exportBtn = array(
            array(
                "label" => "Export Excel",
                'url' => 'downloadstudentaicenterwisecountExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadstudentaicenterwisecountPdf',
                'status' => false
            ),
        );
        $filters = array();


        // $tableData[] = array(
        // 	"lbl" => "Sr.No.",
        // 	'fld' => 'srno'
        // );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();

        $conditions1["applications.exam_year"] = CustomHelper::_get_selected_sessions();
        if ($isAdminStatus == false) {
            $role_id = Session::get('role_id');
            if ($role_id == 59) {
                $conditions["users.id"] = @Auth::user()->id;
            }
            $filters[] = array(
                "lbl" => "Stream ",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream Type',
                'dbtbl' => 'students',
            );

            $conditions["students.user_id"] = @Auth::user()->id;
        } else {
            $filters = array(
                array(
                    "lbl" => "Ai Center",
                    'fld' => 'ai_code',
                    'input_type' => 'select',
                    'options' => $aiCenters,
                    'placeholder' => 'Ai Center',
                    'dbtbl' => 'aicenter_details',
                ),
                array(
                    "lbl" => "Stream ",
                    'fld' => 'stream',
                    'input_type' => 'select',
                    'options' => $stream_id,
                    'placeholder' => 'Stream Type',
                    'dbtbl' => 'students',
                )
            );

        }
        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }


        $conditionstream = null;

        if (isset($conditions['students.stream'])) {
            $conditionstream = $conditions['students.stream'];
            Session::put($formId . '_conditionstream', $conditionstream);
            unset($conditions['students.stream']);
        }


        Session::put($formId . '_conditions', $conditions);
        $conditions = Session::get($formId . '_conditions');
        $master = $custom_component_obj->getWithPaginationAiCenters($formId, true);

        $masterlocksumbitted = null;
        return view('admission_reports.student_application_ai_center_wise_count', compact('aiCenters', 'master', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'masterlocksumbitted', 'conditionstream'));

    }
	
	
	public function student_application_ai_center_course_wise_count(Request $request)
    {
        $custom_component_obj = new CustomComponent;
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

        $yes_no = $this->master_details('yesno');
        $title = "AiCenters Wise Admission Report";
        $table_id = "AiCenters_Wise_Admission_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        if ($isAdminStatus) {
            $aiCenters = $custom_component_obj->getAiCenters();
        } else {
            $role_id = Session::get('role_id');
            $currentUserAiCode = @Auth::user()->ai_code;
            $aiCenters = $custom_component_obj->getAiCenters($currentUserAiCode);
        }
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

        $exportBtn = array();

        $exportBtn = array(
            array(
                "label" => "Export Excel",
                'url' => 'downloadstudentaicentercoursewisecountExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadstudentaicenterwisecountPdf',
                'status' => false
            ),
        );
        $filters = array();


        // $tableData[] = array(
        // 	"lbl" => "Sr.No.",
        // 	'fld' => 'srno'
        // );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();

        $conditions1["applications.exam_year"] = CustomHelper::_get_selected_sessions();
        if ($isAdminStatus == false) {
            $role_id = Session::get('role_id');
            if ($role_id == 59) {
                $conditions["users.id"] = @Auth::user()->id;
            }
            $filters[] = array(
                "lbl" => "Stream ",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream Type',
                'dbtbl' => 'students',
            );

            $conditions["students.user_id"] = @Auth::user()->id;
        } else {
            $filters = array(
                array(
                    "lbl" => "Ai Center",
                    'fld' => 'ai_code',
                    'input_type' => 'select',
                    'options' => $aiCenters,
                    'placeholder' => 'Ai Center',
                    'dbtbl' => 'aicenter_details',
                ),
                array(
                    "lbl" => "Stream ",
                    'fld' => 'stream',
                    'input_type' => 'select',
                    'options' => $stream_id,
                    'placeholder' => 'Stream Type',
                    'dbtbl' => 'students',
                )
            );

        }
        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }


        $conditionstream = null;

        if (isset($conditions['students.stream'])) {
            $conditionstream = $conditions['students.stream'];
            Session::put($formId . '_conditionstream', $conditionstream);
            unset($conditions['students.stream']);
        }


        Session::put($formId . '_conditions', $conditions);
        $conditions = Session::get($formId . '_conditions');
        $master = $custom_component_obj->getWithPaginationAiCenters($formId, true);
        $masterlocksumbitted = null;
        return view('admission_reports.student_application_ai_center_course_wise_count', compact('aiCenters', 'master', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'masterlocksumbitted', 'conditionstream'));

    }

    public function downloadstudentaicenterwisecountExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new StudentAicenterWiseCountExlExport;
        $filename = 'StudentAicenterWiseCount' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }
	
	public function downloadstudentaicentercoursewisecountExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new StudentAicenterCourseWiseCountExlExport;
        $filename = 'StudentAicenterWiseCount' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function downloadstudentaicenterwisecountPdf(Request $request)
    {
        $output = array();
        $formId = "AiCenters_Wise_Admission_Report";
        $custom_component_obj = new CustomComponent;
        $master = $custom_component_obj->getWithPaginationAiCenters($formId, false);
        $fileName = $formId . "_" . date("dmY his");
        $reportname = 'Student Aicenter Wise Count Report';
        $pdf = PDF::loadView('admission_reports.reporting_studentaicenterwisecount_pdf', compact('master', 'reportname'));
        return $pdf->download($fileName . '.pdf');
    }

    public function supplementariedownloadstudentaicenterwisecountPdf(Request $request)
    {
        $output = array();
        $formId = "AiCenters_Wise_Supplementarie_Report";
        $custom_component_obj = new CustomComponent;
        $master = $custom_component_obj->getSupplementarCountAiCenterWise($formId, false);
        $fileName = $formId . "_" . date("dmY his");
        $reportname = 'Supplementary Student Aicenter Wise Count Report';
        $pdf = PDF::loadView('admission_reports.reporting_supplementariestudentaicenterwisecount_pdf', compact('master', 'reportname'));
        return $pdf->download($fileName . '.pdf');
    }

    public function applications(Request $request)
    {
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

        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
        $table_id = "Admission_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $custom_component_obj = new CustomComponent;
        $master = $custom_component_obj->getApplicationData($formId);
        $aiCenters = $custom_component_obj->getAiCenters();


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
                "label" => "Export Excel",
                'url' => 'downloadApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => true
            ),
        );


        $filters = array(
            array(
                "lbl" => "Enrollment Number",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender'
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course'
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream'
            ),
            array(
                "lbl" => "Admission Type",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type'
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium,
                'placeholder' => 'Medium'
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Lock & Submit'
            ),
            array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'students'
            )
        );

        if ($request->ajax()) {
            $conditions = array();
            $inputs = $request->all();

            foreach ($inputs as $k => $v) {
                if ($k != 'draw' && $k != 'columns' && $k != 'order' && $k != 'start' && $k != 'length' && $k != 'search' && $k != '_' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                            $conditions[$iv['dbtbl'] . "." . $k] = $v;
                        } else {
                            $conditions[$k] = $v;
                        }
                        break;
                    }
                }
            }
            Session::put($formId . '_conditions', $conditions);


            return Datatables::of($master)->addIndexColumn()->make(true);
        }
        return view('admission_reports.applications', compact('aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'));
    }

    public function application_ai_center_wise_count(Request $request)
    {

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

        $yes_no = $this->master_details('yesno');
        $title = "AiCenters Wise Admission Report";
        $table_id = "AiCenters_Wise_Admission_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $custom_component_obj = new CustomComponent;


        $aiCenters = $custom_component_obj->getAiCenters();
        $conditions = array();
        $result = $custom_component_obj->getAllAiCenters($formId);
        $filters = array(
            array(
                "lbl" => "Enrollment Number",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender'
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course'
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream'
            ),
            array(
                "lbl" => "Admission Type",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type'
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium,
                'placeholder' => 'Medium'
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Lock & Submit'
            ),
            array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'students',
            )
        );


        if ($request->ajax()) {

            $inputs = $request->all();

            foreach ($inputs as $k => $v) {
                if ($k != 'draw' && $k != 'columns' && $k != 'order' && $k != 'start' && $k != 'length' && $k != 'search' && $k != '_' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                            $conditions[$iv['dbtbl'] . "." . $k] = $v;
                        } else {
                            $conditions[$k] = $v;
                        }
                        break;
                    }
                }
            }

            $aiCenterRoleId = config("global.roles.Aicenter");
            $aiCenterRoleId = 59;//AiCenter
            $result = User::WhereHas('userrole', function ($query) use ($aiCenterRoleId) {
                $query->where('role_id', $aiCenterRoleId);
            })->withCount([
                'studentAllByAicode',
                'studentLockSubmitByAicode',
                'studentNonLockSubmitByAicode'
            ])->get();
            //dd($result);
            Session::put($formId . '_conditions', $conditions);
            return Datatables::of($result)->addIndexColumn()->make(true);
        }


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
                "label" => "Export Excel",
                'url' => 'downloadApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => true
            ),
        );


        $filters = array(
            array(
                "lbl" => "Enrollment Number",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender'
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course'
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream'
            ),
            array(
                "lbl" => "Admission Type",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type'
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium,
                'placeholder' => 'Medium'
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Lock & Submit'
            ),
            array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center'
            )
        );

        return view('admission_reports.application_ai_center_wise_count', compact('aiCenters', 'result', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'));

    }

    public function toc_students_ai_center_wise_count(Request $request)
    {
        $custom_component_obj = new CustomComponent;

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

        $yes_no = $this->master_details('yesno');
        $title = "AiCenters Wise TOC Subject Report";
        $table_id = "AiCenters_Wise_TOC_Subject_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $aiCenters = $custom_component_obj->getAiCenters();

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
                "label" => "Export Excel",
                'url' => 'downloadApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => true
            ),
        );
        $subject_list = $this->subjectList();
        $filters = array(
            array(
                "lbl" => "Enrollment Number",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'placeholder' => 'Subject',
                'dbtbl' => 'toc_marks',
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission Type",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium,
                'placeholder' => 'Medium',
                'dbtbl' => 'applications',
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Lock & Submit',
                'dbtbl' => 'applications',
            )
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'dbtbl' => 'exam_subjects',
            ),
            array(
                "lbl" => "Student TOC Subject Count",
                'fld' => 'toc_subejct_student_count',
                'fld_url' => 'student_applications?ai_code=#ai_code#&locksumbitted=1'
            ),
        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["applications.toc"] = 1;
        if ($isAdminStatus == false) {
            $role_id = Session::get('role_id');
            $conditions["users.id"] = @Auth::user()->id;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center'
            );
            $tableData[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'users'
            );
        }

        if ($request->all()) {
            $conditions = array();
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);
        $conditions = Session::get($formId . '_conditions');
        $master = $custom_component_obj->getStudentCountTOCSubjectWise($formId);


        return view('admission_reports.toc_students_ai_center_wise_count', compact('aiCenters', 'master', 'tableData', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'));

    }


    public function oldhall_ticket_view($stream = null, $ai_code = null, $course = null, $enrollment = null)
    {
        $title = "Hall Ticket Report";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;

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

        $stream = 1;
        $ai_code = 1020;
        $course = 12;
        $enrollment = 01020222001;

        $subjects = DB::table('subjects')->where(array('deleted' => 0))->orderBy('subject_code')->get()->pluck('subject_code', 'id');
        $subjects10 = DB::table('subjects')->where(array('deleted' => 0))->where(array('course' => 10))->orderBy('subject_code')->get()->pluck('name', 'id');
        $subjects12 = DB::table('subjects')->where(array('deleted' => 0))->where(array('course' => 12))->orderBy('subject_code')->get()->pluck('name', 'id');
        $practicalsubjects12 = DB::table('subjects')->where(array('deleted' => 0))->where(array('practical_type' => 1))->orderBy('subject_code')->get()->pluck('name', 'id');

        $aiCenters = $custom_component_obj->getAiCenters();

        $conditions = array();
        $conditionSupplementary = array();
        if (isset($ai_code) && !empty($ai_code)) {

            $conditions [] = ['students.ai_code', '=', $ai_code];
            // $conditions []= ['applications.is_deleted', '=', 0];
            $conditions [] = ['student_allotments.deleted', '=', 0];
            $conditions [] = ['students.submitted', '!=', 'NULL'];
            //$conditions []= ['applications.status', '=', 1];
            $conditions [] = ['students.exam_year', '=', Config::get('global.current_admission_session_id')];
            $conditions [] = ['students.stream', '=', $stream];

            if (isset($enrollment) && !empty($enrollment)) {
                $conditions['students.enrollment'] = $enrollment;
                // $conditionSupplementary[] = ['supplementaries.student_id', '=', $enrollment];
            }
            if (isset($course) && !empty($course)) {
                $conditions['students.course'] = $course;
                $conditionSupplementary[] = ['supplementaries.course', '=', $course];
            }

            $aiCenterDetail = User::where('ai_code', $ai_code)->first();
        } else {
            // return redirect()->route('hall_ticket_form')->with('error', 'Oop"s!Did you really think you are allowed to see that?');
        }

        // Suplementary Data //
        $conditionSupplementary [] = ['supplementaries.ai_code', '=', $ai_code];
        $conditionSupplementary [] = ['student_allotments.deleted', '=', 0];
        $conditionSupplementary [] = ['supplementaries.is_deleted', '=', 0];
        $conditionSupplementary [] = ['supplementaries.exam_year', '=', config::get('global.admission_academicyear_id')];
        $conditionSupplementary [] = ['supplementaries.exam_month', '=', config::get('global.current_exam_month_id')];
        $conditionSupplementary [] = ['supplementaries.submitted', '!=', 'NULL'];
        $conditionSupplementary [] = ['supplementaries.status', '=', 1];

        $suppStudents = array();
        $suppStudents = Supplementary::select('supplementaries.*', 'students.*', 'students.dob',
            'students.course', 'applications.category_a',
            'examcenter_details.id', 'examcenter_details.ecenter10', 'examcenter_details.ecenter12',
            'examcenter_details.cent_name', 'examcenter_details.cent_add1', 'examcenter_details.cent_add2',
            'examcenter_details.cent_add3',
            'addresses.district_name', 'addresses.tehsil_name',
            'documents.id', 'documents.student_id', 'documents.photograph', 'documents.signature')
            ->join('students', 'students.id', '=', 'supplementaries.student_id')
            ->join('documents', 'documents.student_id', '=', 'students.id')
            ->join('addresses', 'addresses.student_id', '=', 'students.id')
            ->join('applications', 'applications.student_id', '=', 'students.id')
            ->join('student_allotments', 'student_allotments.student_id', '=', 'supplementaries.student_id')
            ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
            ->where($conditionSupplementary)
            ->orderBy('supplementaries.student_id', 'ASC')->get();
        // Suplementary Data //
        // @dd($suppStudents);

        // Student Data //
        $students = array();
        $students = Student::select(
            'students.id', 'students.ai_code', 'students.enrollment', 'students.name', 'students.father_name',
            'students.mother_name', 'students.mobile', 'students.name_hi', 'students.stream',
            'applications.category_a', 'students.dob', 'students.course',
            'addresses.district_name', 'addresses.tehsil_name',
            'documents.id', 'documents.student_id', 'documents.photograph', 'documents.signature',
            'examcenter_details.id', 'examcenter_details.ecenter10', 'examcenter_details.ecenter12',
            'examcenter_details.cent_name', 'examcenter_details.cent_add1', 'examcenter_details.cent_add2',
            'examcenter_details.cent_add3',
        )
            ->join('applications', 'applications.student_id', '=', 'students.id')
            ->join('documents', 'documents.student_id', '=', 'students.id')
            ->join('addresses', 'addresses.student_id', '=', 'students.id')
            ->join('student_allotments', 'student_allotments.student_id', '=', 'students.id')
            ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
            ->where($conditions)
            ->orderBy('students.id', 'ASC')->get();
        @dd($students);
        // Student Data //

        // Supplementray Data Set in Array
        $dataSave = array();
        $key = 0;

        if (isset($suppStudents) && !empty($suppStudents)) {
            foreach ($suppStudents as $suppKey => $suppStudent) {
                $dataSave[$suppStudent['Supplementary']['course']][$key]['index'] = $suppKey;
                $ai_code = $suppStudent['Supplementary']['ai_code'];
                $dataSave[$suppStudent['Supplementary']['course']][$key]['type'] = 'Supplementary';
                if (isset($suppStudent['pastData']['ENROLLNO']) && !empty($suppStudent['pastData']['ENROLLNO'])) {
                    //$dataSave[$suppStudent['Supplementary']['course']][$key]['id'] = $student['Supplementary']['ai_code'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['ai_code'] = $ai_code;
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['enrollment'] = $suppStudent['pastData']['ENROLLNO'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['name'] = $suppStudent['pastData']['NAME'];

                    $dataSave[$suppStudent['Supplementary']['course']][$key]['district'] = $suppStudent['pastData']['DISTRICT'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['tehsil'] = '';

                    $dataSave[$suppStudent['Supplementary']['course']][$key]['father_name'] = $suppStudent['pastData']['FNAME'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['mother_name'] = $suppStudent['pastData']['MNAME'];
                    //$dataSave[$suppStudent['Supplementary']['course']][$key]['category_a'] = $suppStudent['pastData']['CATEGORY'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['category_a'] = '';

                    if (isset($suppStudent['Supplementary']['course']) && !empty($suppStudent['Supplementary']['course'])) {
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['course'] = $suppStudent['Supplementary']['course'];
                    } else {
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['course'] = '';
                    }
                    if (isset($suppStudent['Supplementary']['course']) && !empty($suppStudent['Supplementary']['course'])) {
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['course'] = $suppStudent['Supplementary']['course'];
                    } else {
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['course'] = '';
                    }
                    if (isset($suppStudent['pastData']['DOB']) && !empty($suppStudent['pastData']['DOB'])) {
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['dob'] = date('d/m/Y', strtotime($suppStudent['pastData']['DOB']));
                    } else {
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['dob'] = '';
                    }
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['stream'] = $suppStudent['Supplementary']['stream'];
                    $subjectList = array();
                    for ($i = 1; $i <= 7; $i++) {
                        $sub = 'subject' . $i;
                        if ($suppStudent['Supplementary'][$sub] != 0) {
                            $subjectList[] = $suppStudent['Supplementary'][$sub];
                        }
                    }
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['exam_subjects'] = $this->getSubjectCodeWithDetailByEnrollmentSupp($subjectList);
                    if (isset($suppStudent['Document']['id']) && !empty($suppStudent['Document']['id'])) {
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['photograph'] = $suppStudent['Document']['photograph'];
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['signature'] = $suppStudent['Document']['signature'];
                    } else {
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['photograph'] = '';
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['signature'] = '';
                    }

                    $dataSave[$suppStudent['Supplementary']['course']][$key]['ecenter10'] = $suppStudent['Examination']['ecenter10'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['ecenter12'] = $suppStudent['Examination']['ecenter12'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['cent_name'] = $suppStudent['Examination']['cent_name'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['cent_add1'] = $suppStudent['Examination']['cent_add1'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['cent_add2'] = $suppStudent['Examination']['cent_add2'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['cent_add3'] = $suppStudent['Examination']['cent_add3'];
                } else {
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['id'] = $suppStudent['Student']['id'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['ai_code'] = $suppStudent['Student']['ai_code'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['enrollment'] = $suppStudent['Student']['enrollment'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['name'] = $suppStudent['Student']['name'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['father_name'] = $suppStudent['Student']['father_name'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['mother_name'] = $suppStudent['Student']['mother_name'];
                    //pr($suppStudent);die;
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['category_a'] = $suppStudent['Application']['category_a'];

                    if (isset($suppStudent['Supplementary']['course']) && !empty($suppStudent['Supplementary']['course'])) {
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['course'] = $suppStudent['Application']['course'];
                    } else {
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['course'] = '';
                    }
                    if (isset($suppStudent['Application']['dob']) && !empty($suppStudent['Application']['dob'])) {
                        if (strpos($suppStudent['Application']['dob'], '-')) {
                            $ndobarr = explode('-', $suppStudent['Application']['dob']);
                            $ndob = $ndobarr[2] . "/" . $ndobarr[1] . "/" . $ndobarr[0];
                            $dataSave[$suppStudent['Supplementary']['course']][$key]['dob'] = $ndob;
                        } else {
                            $dataSave[$suppStudent['Supplementary']['course']][$key]['dob'] = $suppStudent['Application']['dob'];
                        }

                    } else {
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['dob'] = '';
                    }
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['stream'] = $suppStudent['Student']['stream'];
                    $subjectList = array();
                    for ($i = 1; $i <= 7; $i++) {
                        $sub = 'subject' . $i;
                        if ($suppStudent['Supplementary'][$sub] != 0) {
                            $subjectList[] = $suppStudent['Supplementary'][$sub];
                        }
                    }

                    $dataSave[$suppStudent['Supplementary']['course']][$key]['exam_subjects'] = $this->getSubjectCodeWithDetailByEnrollmentSupp($subjectList);

                    if (isset($suppStudent['Document']['id']) && !empty($suppStudent['Document']['id'])) {
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['photograph'] = $suppStudent['Document']['photograph'];
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['signature'] = $suppStudent['Document']['signature'];
                    } else {
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['photograph'] = '';
                        $dataSave[$suppStudent['Supplementary']['course']][$key]['signature'] = '';
                    }

                    if (isset($suppStudent['Address']['district_name']) && $suppStudent['Address']['district_name'] != '') {
                        $dataSave[$suppStudent['Application']['course']][$key]['district'] = $suppStudent['Address']['district_name'];
                    } else {
                        $dataSave[$suppStudent['Application']['course']][$key]['district'] = '';
                    }
                    if (isset($suppStudent['Address']['tehsil_name']) && $suppStudent['Address']['tehsil_name'] != '') {
                        $dataSave[$suppStudent['Application']['course']][$key]['tehsil'] = $suppStudent['Address']['tehsil_name'];
                    } else {
                        $dataSave[$suppStudent['Application']['course']][$key]['tehsil'] = '';
                    }
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['ecenter10'] = $suppStudent['Examination']['ecenter10'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['ecenter12'] = $suppStudent['Examination']['ecenter12'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['cent_name'] = $suppStudent['Examination']['cent_name'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['cent_add1'] = $suppStudent['Examination']['cent_add1'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['cent_add2'] = $suppStudent['Examination']['cent_add2'];
                    $dataSave[$suppStudent['Supplementary']['course']][$key]['cent_add3'] = $suppStudent['Examination']['cent_add3'];
                }
                $key++;
            }
        }
        // Supplementray Data Set in Array

        // Student Data Set in Array
        if (isset($students) && !empty($students)) {
            foreach ($students as $stKey => $student) {
                $ai_code = $student['Student']['ai_code'];
                $dataSave[$student['Application']['course']][$key]['type'] = 'Student';
                $dataSave[$student['Application']['course']][$key]['index'] = $stKey;
                $dataSave[$student['Application']['course']][$key]['id'] = $student['Student']['id'];
                $dataSave[$student['Application']['course']][$key]['ai_code'] = $student['Student']['ai_code'];
                $dataSave[$student['Application']['course']][$key]['enrollment'] = $student['Student']['enrollment'];
                $dataSave[$student['Application']['course']][$key]['name'] = $student['Student']['name'];
                $dataSave[$student['Application']['course']][$key]['father_name'] = $student['Student']['father_name'];
                $dataSave[$student['Application']['course']][$key]['mother_name'] = $student['Student']['mother_name'];
                $dataSave[$student['Application']['course']][$key]['category_a'] = $student['Application']['category_a'];

                if (isset($student['Application']['course']) && !empty($student['Application']['course'])) {
                    $dataSave[$student['Application']['course']][$key]['course'] = $student['Application']['course'];
                } else {
                    $dataSave[$student['Application']['course']][$key]['course'] = '';
                }
                if (isset($student['Application']['dob']) && !empty($student['Application']['dob'])) {
                    $dataSave[$student['Application']['course']][$key]['dob'] = $student['Application']['dob'];
                } else {
                    $dataSave[$student['Application']['course']][$key]['dob'] = '';
                }
                $dataSave[$student['Application']['course']][$key]['stream'] = $student['Student']['stream'];
                $dataSave[$student['Application']['course']][$key]['exam_subjects'] = $this->getSubjectCodeWithDetailByEnrollment($student['Student']['enrollment']);

                if (isset($student['Document']['id']) && !empty($student['Document']['id'])) {
                    $dataSave[$student['Application']['course']][$key]['photograph'] = $student['Document']['photograph'];
                    $dataSave[$student['Application']['course']][$key]['signature'] = $student['Document']['signature'];
                } else {
                    $dataSave[$student['Application']['course']][$key]['photograph'] = '';
                    $dataSave[$student['Application']['course']][$key]['signature'] = '';
                }
                if (isset($student['Address']['district_name']) && $student['Address']['district_name'] != '') {
                    $dataSave[$student['Application']['course']][$key]['district'] = $student['Address']['district_name'];
                } else {
                    $dataSave[$student['Application']['course']][$key]['district'] = '';
                }
                if (isset($student['Address']['tehsil_name']) && $student['Address']['tehsil_name'] != '') {
                    $dataSave[$student['Application']['course']][$key]['tehsil'] = $student['Address']['tehsil_name'];
                } else {
                    $dataSave[$student['Application']['course']][$key]['tehsil'] = '';
                }


                $dataSave[$student['Application']['course']][$key]['ecenter10'] = $student['Examination']['ecenter10'];
                $dataSave[$student['Application']['course']][$key]['ecenter12'] = $student['Examination']['ecenter12'];
                $dataSave[$student['Application']['course']][$key]['cent_name'] = $student['Examination']['cent_name'];
                $dataSave[$student['Application']['course']][$key]['cent_add1'] = $student['Examination']['cent_add1'];
                $dataSave[$student['Application']['course']][$key]['cent_add2'] = $student['Examination']['cent_add2'];
                $dataSave[$student['Application']['course']][$key]['cent_add3'] = $student['Examination']['cent_add3'];
                $key++;
            }
        }
        // Student Data Set in Array


        return view('admission_reports.hall_ticket_view',
            compact(
                'suppStudents', 'students', 'subjects', 'subjects10', 'subjects12', 'practicalsubjects12',
                'stream', 'aiCenterDetail', 'ai_code', 'course', 'enrollment', 'aiCenters', 'title',
                'formId', 'breadcrumbs'
            ));
    }

    public function supplementariesubjectexcel(Request $request, $type = "xlsx")
    {
        $application_exl_data = new SupplementarieSubjectExlExport;
        $filename = 'Supplementarie_student' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function supplementariefeesreport(Request $request)
    {
        //dd('test');
        $custom_component_obj = new CustomComponent;
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'supp_late_fees';
        $supp_late_fees = $this->master_details($combo_name);

        $yes_no = $this->master_details('yesno');
        $title = "Supplementaries Fees Report";
        $table_id = "Supplementaries_Fees_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $aiCenters = $custom_component_obj->getAiCenters();

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
                "label" => "Export Excel",
                'url' => 'supplementariefeesexcel',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => false
            ),
        );
        $subject_list = $this->subjectList();
        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Category Type",
                'fld' => 'category_a',
                'input_type' => 'select',
                'options' => $categorya,
                'placeholder' => 'Category',
                'dbtbl' => 'applications',
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream',
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Admission Type",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            // array(
            // 	"lbl" => "Supp Late Fees",
            // 	'fld' => 'late_fees',
            // 	'input_type' => 'select',
            // 	'options' => $supp_late_fees,
            // 	'placeholder' => 'Supp Late Fees',
            // 	'dbtbl' => 'supplementaries',
            // ),
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment Number",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Subject Change Fee",
                'fld' => 'subject_change_fees',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Exam fee",
                'fld' => 'exam_fees',
                'input_type' => 'text'
            ),
            array(
                "lbl" => "Practical Fee",
                'fld' => 'practical_fees',
                'input_type' => 'text'
            ),
            array(
                "lbl" => "Online Fee",
                'fld' => 'online_fees',
                'input_type' => 'text'
            ),
            array(
                "lbl" => "Late Fee",
                'fld' => 'late_fees',
                'input_type' => 'text'
            ),
            array(
                "lbl" => "Forward Fee",
                'fld' => 'forward_fees',
                'input_type' => 'text'
            ),
            array(
                "lbl" => "Total Fee",
                'fld' => 'total_fees',
                'input_type' => 'text'
            )

        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();


        $conditions["supplementaries.exam_year"] = CustomHelper::_get_selected_sessions();
        $conditions["supplementaries.exam_month"] = $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
        $conditions["supplementaries.locksumbitted"] = 1;
        $conditions["supplementaries.fee_status"] = 1;
        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $conditions["supplementaries.user_id"] = @Auth::user()->id;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'supplementaries'
            );
            $tableData[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'supplementaries'
            );
        }
        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);
        $conditions = Session::get($formId . '_conditions');
        $master = $custom_component_obj->supplementariefeesreports($formId);
        return view('admission_reports.supplementarie_fees_report', compact('master', 'tableData', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'));
    }

    public function supplementariefeesexcel(Request $request, $type = "xlsx")
    {
        $application_exl_data = new SupplementarieFeesExlExport;
        $filename = 'Supplementarie_Fees' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function supplementarieaicenterwisecount(Request $request)
    {
        $custom_component_obj = new CustomComponent;
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
        $combo_name = 'exam_month';
        $exam_month_filter = $this->master_details($combo_name);


        $yes_no = $this->master_details('yesno');
        $title = "AiCenters Wise Supplementary Report";
        $table_id = "AiCenters_Wise_Supplementarie_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $aiCenters = $custom_component_obj->getAiCenters();

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
                "label" => "Export Excel",
                'url' => 'supplementarieaicenterwisecountexcel',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'supplementariedownloadstudentaicenterwisecountPdf',
                'status' => true
            ),
        );

        $filters = array();


        // $tableData[] = array(
        // 	"lbl" => "Sr.No.",
        // 	'fld' => 'srno'
        // );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["supplementaries.exam_year"] = CustomHelper::_get_selected_sessions();
        //$conditions["supplementaries.exam_month"] = $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
        if ($isAdminStatus == false) {
            $role_id = Session::get('role_id');
            $conditions["supplementaries.user_id"] = @Auth::user()->id;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'aicenter_details',
            );

            $filters[] = array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month_filter,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'supplementaries',
            );
        }
        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }
        if (!empty($request->exam_month)) {
            $exam_momth = @$request->exam_month;
        } else {
            $exam_momth = NULL;
        }

        Session::put($formId . '_conditions', $conditions);
        Session::put('aicenterwiseconditions', $conditions);
        // $conditions = Session::get($formId. '_conditions');
        $master = $custom_component_obj->getSupplementarCountAiCenterWise($formId, true);
        //dd($master);
        $masterlocksumbitted = null;

        return view('admission_reports.supplementary_student_application_ai_center_wise_count', compact('aiCenters', 'master', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'exam_momth'));

    }

    public function supplementarieaicenterwisecountexcel(Request $request, $type = "xlsx")
    {
        $application_exl_data = new supplementarieArieaicenterWiseCountexcelExport;
        $filename = 'Supplementarie_Arieaicenter_Wise_Count' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }


    public function Femaleandmalefeesexceldownload(Request $request, $type = "xlsx")
    {
        $application_exl_data = new Femaleandmalefeesexcelsdownload;
        $filename = 'Supplementarie_Fees' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }


    public function supplementary_student_applications(Request $request)
    {
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'supp_verfication_status';
        $supp_verfication_status = $this->master_details($combo_name);
        $district_list = $this->districtsByState();
        $yes_no_temp = $this->master_details('yesno');
        $yes_no_temp[""] = "No";
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $role_id = Session::get('role_id');
        $aicenter_id_role = config("global.aicenter_id");
        $yes_no = $this->master_details('yesno');
        $title = "Supplementary Applicaiton Report";
        $table_id = "Admission_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $exam_year = CustomHelper::_get_selected_sessions();
        $form_supp_current_admission_session_id = config("global.form_supp_current_admission_session_id");
        $supp_current_admission_exam_month = config("global.supp_current_admission_exam_month");

        if ($exam_year == $form_supp_current_admission_session_id && $request->exam_month == $supp_current_admission_exam_month) {
        } else {
            return redirect()->route('supplementary_admin_student_applications', ['exam_month' => $request->exam_month, 'exam_year' => $exam_year]);
        }
        //need to put in funciton end
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $permissions = CustomHelper::roleandpermission();
        $yes_no_temp = $this->master_details('yesno');
        $yes_no_temp[""] = "No";

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
                "label" => "Export Excel",
                'url' => 'downloadSupplementaryApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadSupplementaryApplicationPdf',
                'status' => false,
            ),
        );

        $supp_exam_month = Config::get('global.supp_current_admission_exam_month');
        $temp_exam_month = @$exam_month->toArray();
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        if (@$temp_exam_month[1]) {
            if ($isAdminStatus == false) {
                if ($supp_exam_month == 2) {
                    unset($temp_exam_month[1]);
                }
            }
        }
        if ($request->exam_month == 1) {
            unset($exam_month[2]);
        } else {
            unset($exam_month[1]);
        }


        $filters = array(
            array(
                "lbl" => "Start date",
                'fld' => 'start_date',
                'input_type' => 'datetime-local',
                'placeholder' => "Start Date",
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "End date",
                'fld' => 'end_date',
                'input_type' => 'datetime-local',
                'placeholder' => "End Date",
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Payment Done",
                'fld' => 'challan_tid2',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Payment',
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Is Eligible",
                'fld' => 'is_eligible',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Is Eligible',
                'dbtbl' => 'supplementaries',
            ),

            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Total Fees",
                'fld' => 'total_fees2',
                'input_type' => 'text',
                'placeholder' => "Total fees",
                'dbtbl' => 'supplementaries',
            ),

            array(
                "lbl" => "Mobile Number",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Mobile Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Challan Number",
                'fld' => 'challan_tid',
                'input_type' => 'text',
                'placeholder' => "Challan Number",
                'dbtbl' => 'supplementaries',
            ),

            array(
                "lbl" => "Late Fees",
                'fld' => 'late_fees',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Late Fees',
                'dbtbl' => 'supplementaries',
            ),
            //array(
            //"lbl" => "Total Fees",
            //'fld' => 'total_fees',
            //'input_type' => 'select',
            //'options' => $yes_no,
            //'placeholder' => 'Total Fees',
            //'dbtbl' => 'supplementaries',
            //),
            array(
                "lbl" => "Father Name",
                'fld' => 'father_name',
                'input_type' => 'text',
                'placeholder' => "Father Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Mother Name",
                'fld' => 'mother_name',
                'input_type' => 'text',
                'placeholder' => "Mother Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Jan Aadhar Number",
                'fld' => 'jan_aadhar_number',
                'input_type' => 'text',
                'placeholder' => "Jan Aadhar Number",
                'dbtbl' => 'applications',
            ),
            array(
                "lbl" => "Aadhar Number",
                'fld' => 'aadhar_number',
                'input_type' => 'text',
                'placeholder' => "Aadhar Number",
                'dbtbl' => 'applications',
            ),
            array(
                "lbl" => "Amount",
                'fld' => 'fee_paid_amount',
                'input_type' => 'text',
                'placeholder' => "Amount",
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'supplementaries',
            ),
            /*array(
				"lbl" => "Stream ",
				'fld' => 'stream',
				'input_type' => 'select',
				'options' => $stream_id,
				'placeholder' => 'Stream Type',
				'dbtbl' => 'supplementaries',
			),*/
            array(
                "lbl" => "Exam Month ",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Admission Type ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Lock & Submit',
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Are You From Rajasthan And Not",
                'fld' => 'are_you_from_rajasthan',
                'input_type' => 'select',
                'options' => $are_you_from_rajasthan,
                'search_type' => "text",
                'placeholder' => 'Are You From Rajasthan And Not',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Is Self Filled",
                'fld' => 'is_self_filled',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Is Self Filled',
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Is SSO Filled",
                'fld' => 'ssoid',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Is SSO Filled',
                'dbtbl' => 'students',
            ),
            array(
             "lbl" => "Is Aicenter Verify",
             'fld' => 'is_aicenter_verify',
             'input_type' => 'select',
             'options' =>$supp_verfication_status,
             'search_type' => "text",
             'placeholder' => 'Is Aicenter Verify',
            'dbtbl' => 'supplementaries',
             ),

            array(
                "lbl" => "SSO",
                'fld' => 'ssoid2',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),

        );

        if ($isAdminStatus == true) {
            $filters[] = array(
                "lbl" => "Is Department Verify",
                'fld' => 'is_department_verify',
                'input_type' => 'select',
                'options' => $supp_verfication_status,
                'search_type' => "text",
                'placeholder' => 'Is Department Verify',
                'dbtbl' => 'supplementaries',
            );
            $filters[] = array(
                "lbl" => "Is Permanent Rejected",
                'fld' => 'is_per_rejected',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => "Is Permanent Rejected",
                'dbtbl' => 'supplementaries',
            );
        }

        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number"
            )
        , array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO"
            ),
            array(
                "lbl" => "Is Eligible",
                'fld' => 'is_eligible',
                'input_type' => 'select',
                'options' => $yes_no_temp
            )
        , array(
                "lbl" => "AI Code",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'placeholder' => "AI Code"
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course ",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            /*array(
				"lbl" => "Stream ",
				'fld' => 'stream',
				'input_type' => 'select',
				'options' => $stream_id
			),*/
            array(
                "lbl" => "Exam Month ",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month
            ),

            // array(
            // 	"lbl" => "Admission ",
            // 	'fld' => 'adm_type',
            // 	'input_type' => 'select',
            // 	'options' => $adm_types
            // ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            array(
                "lbl" => "Fees Amount",
                'fld' => 'total_fees'
            ),
            array(
                "lbl" => "Challan Number",
                'fld' => 'challan_tid'
            ),

            array(
                "lbl" => "Is Self Filled",
                'fld' => 'is_self_filled',
                'input_type' => 'select',
                'options' => $yes_no_temp
            ),
            array(
            "lbl" => "Is Aicenter Verify",
             'fld' => 'is_aicenter_verify',
             'input_type' => 'select',
             'options' =>$supp_verfication_status
             ),


            array(
                "lbl" => "Submitted",
                'fld' => 'submitted',
            ),

        );

        if ($isAdminStatus == true) {
            $tableData[] = array(
                "lbl" => "Is Department Verify",
                'fld' => 'is_department_verify',
                'input_type' => 'select',
                'options' => $supp_verfication_status
            );

            // $tableData[] = array(
            // "lbl" => "Is Permanent Rejected",
            // 'fld' => 'is_per_rejected',
            // 'input_type' => 'select',
            // 'options' =>$yes_no_temp
            // );
        }


        $conditions = array();
        $conditions["supplementaries.exam_year"] = CustomHelper::_get_selected_sessions();


        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            //if role is 59 ai center then get session aicode and then aicenter_detail_id put in condition
            $aicenter_user_id = Auth::user()->id;
            $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode(@$aicenter_user_id);
            $auth_user_id = @$aicenter_user_ids->ai_code;
            $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode(@$auth_user_id);
            $aicenter_mapped_data_conditions = $aicenter_mapped_data;


        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'supplementaries'
            );

        }

        if (in_array("Supplementary_student_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),

            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),
            );
        }

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }
        $symbol = null;
        $symbolstotalfees = null;
        $symbol2 = null;
        $symbolss = null;
        $symbolssoid = null;
        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (@$iv['fld'] == $k && $iv['fld'] == $k) {
                            if ($iv['fld'] == 'late_fees' && $inputs[$iv['fld']] == 1) {
                                $symbol = "!=";
                            } else {
                                $symbol = "=";
                            }

                            if ($iv['fld'] == 'challan_tid2' && $inputs[$iv['fld']] == 1) {
                                $symbol2 = "!=";
                            } elseif ($iv['fld'] == 'challan_tid2' && $inputs[$iv['fld']] == 0) {
                                $symbol2 = "=";
                            }

                            if ($iv['fld'] == 'total_fees' && $inputs[$iv['fld']] == 1) {
                                $symbolstotalfees = "!=";
                            } elseif ($iv['fld'] == 'total_fees' && $inputs[$iv['fld']] == 0) {
                                $symbolstotalfees = "=";
                            }
                            if ($iv['fld'] == 'is_self_filled' && $inputs[$iv['fld']] == 1) {
                                $symbolss = "!=";
                            } elseif ($iv['fld'] == 'is_self_filled' && $inputs[$iv['fld']] == 0) {
                                $symbolss = "=";
                            }
                            if ($iv['fld'] == 'ssoid' && $inputs[$iv['fld']] == 1) {
                                $symbolssoid = "!=";
                            } elseif ($iv['fld'] == 'ssoid' && $inputs[$iv['fld']] == 0) {
                                $symbolssoid = "=";
                            }
                            if ($iv['fld'] == 'locksumbitted' && $inputs[$iv['fld']] == 1) {
                                $symbolssoid = "!=";
                            } elseif ($iv['fld'] == 'locksumbitted' && $inputs[$iv['fld']] == 0) {
                                $symbolssoid = "=";
                            } else {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            }
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }

        Session::put($formId . '_conditions', $conditions);
        Session::put($formId . '_aicenter_mapped_data_conditions', @$aicenter_mapped_data_conditions);
        Session::put($formId . '_symbol', $symbol);
        Session::put($formId . '_symbolstotalfees', $symbolstotalfees);
        Session::put($formId . '_symbol2', $symbol2);
        Session::put($formId . '_symbolss', $symbolss);
        Session::put($formId . '_symbolssoid', $symbolssoid);

        if ($role_id == $aicenter_id_role) {
            $master = $custom_component_obj->getSupplementaryApplicationData($formId, true, $aicenter_mapped_data_conditions);
        } else {

            $master = $custom_component_obj->getSupplementaryApplicationData($formId);
        }

        return view('admission_reports.supplementary_student_applications', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());

    }

    public function supplementary_student_locksumbited_applications(Request $request)
    {
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
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);

        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
        $table_id = "Admission_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $role_id = Session::get('role_id');
        $aicenter_id_role = config("global.aicenter_id");
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
                "label" => "Export Excel",
                'url' => 'downloadsupplementarystudentlocksumbitedapplicationsExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadSupplementaryApplicationPdf',
                'status' => false,
            ),
        );


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'supplementaries',
            ),
            // array(
            // 	"lbl" => "Ai Code",
            // 	'fld' => 'ai_code',
            // 	'input_type' => 'text',
            // 	'placeholder' => "Ai Code",
            // 	'dbtbl' => 'supplementaries',
            // ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'supplementaries',
            ),
            /*array(
				"lbl" => "Stream ",
				'fld' => 'stream',
				'input_type' => 'select',
				'options' => $stream_id,
				'placeholder' => 'Stream Type',
				'dbtbl' => 'supplementaries',
			),*/
            array(
                "lbl" => "Eaxm Month ",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            )

        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'supplementaries',
            ), array(
                "lbl" => "AI Code",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'placeholder' => "AI Code",
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course ",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            /*array(
				"lbl" => "Stream ",
				'fld' => 'stream',
				'input_type' => 'select',
				'options' => $stream_id
			),*/
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no
            ),

        );
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["supplementaries.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            //if role is 59 ai center then get session aicode and then aicenter_detail_id put in condition
            $aicenter_user_id = Auth::user()->id;
            $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
            $auth_user_id = $aicenter_user_ids->ai_code;
            $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode($auth_user_id);
            $aicenter_mapped_data_conditions = $aicenter_mapped_data;
        } else {

            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'supplementaries'
            );


        }

        if (in_array("Supplementary_student_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),

            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);
        Session::put($formId . '_aicenter_mapped_data_conditions', @$aicenter_mapped_data_conditions);
        if ($role_id == $aicenter_id_role) {
            $master = $custom_component_obj->getSupplementarylocksumbittedStudentData($formId, true, $aicenter_mapped_data_conditions);
        } else {
            $master = $custom_component_obj->getSupplementarylocksumbittedStudentData($formId);
        }


        return view('admission_reports.supplementary_student_locksumbited_applications', compact('tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'actions'))->withInput($request->all());
    }


    public function allsupplementary_student_payment_details(Request $request)
    {
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
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $role_id = Session::get('role_id');
        $aicenter_id_role = config("global.aicenter_id");
        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
        $table_id = "Admission_Report";
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
                "label" => "Export Excel",
                'url' => 'downloadallsupplementarystudentpaymentdetailsExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadSupplementaryApplicationPdf',
                'status' => false,
            ),
        );


        $filters = array(
            array(
                "lbl" => "Enrollment Number",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Amount",
                'fld' => 'total_fees',
                'input_type' => 'text',
                'placeholder' => "Amount",
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'supplementaries',
            ),
            /*array(
				"lbl" => "Stream Type",
				'fld' => 'stream',
				'input_type' => 'select',
				'options' => $stream_id,
				'placeholder' => 'Stream Type',
				'dbtbl' => 'supplementaries',
			),*/
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Admission Type",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            /*array(
				"lbl" => "Exam Month",
				'fld' => 'exam_month',
				'input_type' => 'select',
				'options' => $exam_month,
				'placeholder' => 'Exam Month',
				'dbtbl' => 'supplementaries',
			),*/
            // array(
            // 	"lbl" => "Lock & Submit",
            // 	'fld' => 'locksumbitted',
            // 	'input_type' => 'select',
            // 	'options' => $yes_no,
            // 	'placeholder' => 'Lock & Submit',
            // 	'dbtbl' => 'supplementaries',
            // )
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            /*array(
				"lbl" => "Stream",
				'fld' => 'stream',
				'input_type' => 'select',
				'options' => $stream_id
			),*/
            array(
                "lbl" => "Admission",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            ),
            // array(
            // 	"lbl" => "Lock & Submit",
            // 	'fld' => 'locksumbitted',
            // 	'input_type' => 'select',
            // 	'options' => $yes_no
            // ),
            array(
                "lbl" => "Challan Number",
                'fld' => 'challan_tid'
            ), array(
                "lbl" => "Fees Amount",
                'fld' => 'total_fees'
            ),
            array(
                "lbl" => "Submitted",
                'fld' => 'submitted'
            )
        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["supplementaries.exam_year"] = CustomHelper::_get_selected_sessions();
        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $aicenter_user_id = Auth::user()->id;
            $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
            $auth_user_id = $aicenter_user_ids->ai_code;
            $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode($auth_user_id);
            $aicenter_mapped_data_conditions = $aicenter_mapped_data;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'supplementaries',

            );
            /* $tableData[] = array(
				"lbl" => "Ai Center",
				'fld' => 'ai_code',
				'input_type' => 'select',
				'options' => $aiCenters,
				'placeholder' => 'Ai Center',
				'dbtbl' => 'users'
			); */
            $tableData[] = array(
                "lbl" => "AiCode",
                'fld' => 'ai_code',
                'fld_url' => ''
            );

        }

        if (in_array("Supplementary_student_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),

            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons">remove_red_eye</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),
            );
        }

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }

        Session::put($formId . '_conditions', $conditions);
        Session::put($formId . '_aicenter_mapped_data_conditions', @$aicenter_mapped_data_conditions);
        if ($role_id == $aicenter_id_role) {
            $master = $custom_component_obj->getSupplementaryallStudentPaymentData($formId, true, $aicenter_mapped_data_conditions);
        } else {
            $master = $custom_component_obj->getSupplementaryallStudentPaymentData($formId);
        }


        return view('admission_reports.supplementary_allstudent_payment_details', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }


    public function allsupplementary_student_not_pay_payment_details(Request $request)
    {

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
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $role_id = Session::get('role_id');
        $aicenter_id_role = config("global.aicenter_id");
        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
        $table_id = "Admission_Report";
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
                "label" => "Export Excel",
                'url' => 'downloadallsupplementarystudentnotpaypaymentdetailsExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadSupplementaryApplicationPdf',
                'status' => false,
            ),
        );
        if ($request->exam_month == 1) {
            unset($exam_month[2]);
        } else {
            unset($exam_month[1]);
        }

        $filters = array(
            array(
                "lbl" => "Enrollment Number",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            /*array(
				"lbl" => "Stream Type",
				'fld' => 'stream',
				'input_type' => 'select',
				'options' => $stream_id,
				'placeholder' => 'Stream Type',
				'dbtbl' => 'students',
			),*/
            array(
                "lbl" => "Admission Type",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium,
                'placeholder' => 'Medium Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month ",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'supplementaries',
            ),
            // array(
            // 	"lbl" => "Lock & Submit",
            // 	'fld' => 'locksumbitted',
            // 	'input_type' => 'select',
            // 	'options' => $yes_no,
            // 	'placeholder' => 'Lock & Submit',
            // 	'dbtbl' => 'students',
            // )
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            /*array(
				"lbl" => "Stream",
				'fld' => 'stream',
				'input_type' => 'select',
				'options' => $stream_id
			),*/
            array(
                "lbl" => "Admission",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium
            ),
            /* array(
				"lbl" => "Lock & Submit",
				'fld' => 'locksumbitted',
				'input_type' => 'select',
				'options' => $yes_no
			),
			array(
				"lbl" => "Challan Number",
				'fld' => 'challan_tid'
			),
			array(
				"lbl" => "submitted ",
				'fld' => 'submitted'
			) */
        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["supplementaries.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $aicenter_user_id = Auth::user()->id;
            $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
            $auth_user_id = $aicenter_user_ids->ai_code;
            $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode($auth_user_id);
            $aicenter_mapped_data_conditions = $aicenter_mapped_data;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'supplementaries',
            );
            $tableData[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'supplementaries'
            );
        }

        if (in_array("application_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),

            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),
            );
        }

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        Session::put($formId . '_aicenter_mapped_data_conditions', @$aicenter_mapped_data_conditions);
        Session::put($formId . '_conditions', $conditions);
        if ($role_id == $aicenter_id_role) {
            $master = $custom_component_obj->allgetSupplementaryStudentNotPayPaymentData($formId, true, $aicenter_mapped_data_conditions);
        } else {
            $master = $custom_component_obj->allgetSupplementaryStudentNotPayPaymentData($formId);
        }


        return view('admission_reports.supplementary_allstudent_not_payment_details', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function supplementary_aicenter_student_not_pay_payment_details(Request $request)
    {

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

        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
        $table_id = "Admission_Report";
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
                "label" => "Export Excel",
                'url' => 'downloadSupplementaryApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadSupplementaryApplicationPdf',
                'status' => true
            ),
        );


        $filters = array(
            array(
                "lbl" => "Enrollment Number",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            /*array(
				"lbl" => "Stream Type",
				'fld' => 'stream',
				'input_type' => 'select',
				'options' => $stream_id,
				'placeholder' => 'Stream Type',
				'dbtbl' => 'students',
			),*/
            array(
                "lbl" => "Admission Type",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium,
                'placeholder' => 'Medium Type',
                'dbtbl' => 'students',
            ),
            // array(
            // 	"lbl" => "Lock & Submit",
            // 	'fld' => 'locksumbitted',
            // 	'input_type' => 'select',
            // 	'options' => $yes_no,
            // 	'placeholder' => 'Lock & Submit',
            // 	'dbtbl' => 'students',
            // )
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            /*array(
				"lbl" => "Stream",
				'fld' => 'stream',
				'input_type' => 'select',
				'options' => $stream_id
			),*/
            array(
                "lbl" => "Admission",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium
            ),
            /* array(
				"lbl" => "Lock & Submit",
				'fld' => 'locksumbitted',
				'input_type' => 'select',
				'options' => $yes_no
			),
			array(
				"lbl" => "Challan Number",
				'fld' => 'challan_tid'
			),
			array(
				"lbl" => "submitted ",
				'fld' => 'submitted'
			) */
        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $conditions["supplementaries.user_id"] = @Auth::user()->id;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center'
            );
            $tableData[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'users'
            );
        }

        if (in_array("application_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),

            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),
            );
        }

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                            $conditions[$iv['dbtbl'] . "." . $k] = $v;
                        } else {
                            $conditions[$k] = $v;
                        }
                        break;
                    }
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);

        $master = $custom_component_obj->getSupplementaryAicenterStudentNotPayPaymentData($formId);

        return view('admission_reports.supplementary_allstudent_not_payment_details', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function aicodewisesubjectsdatastudentsall()
    {

        $title = "Ai code wise subject data student";
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => ''
            ),
            array(
                "label" => $title,
                'url' => 'exam_center_nominal_roll_pdf'
            )
        );

        $empty = array();
        $developeradminrole = Config::get("global.developer_admin");
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $user_role = Session::get('role_id');
        $district_list = $this->districtsByState(6);

        return view('admission_reports.aicodewisesubjectsdatastudentsall', compact('courses', 'stream_id', 'title', 'breadcrumbs', 'developeradminrole', 'user_role', 'district_list', 'empty', 'midium'));
    }

    public function downloadaicodewisesubjectsdatastudentsall(Request $request, $type = "xlsx")
    {
        $request->validate([
            'stream' => 'required'
        ]);
        $obj = new AicentersubjectdatastudentExlExport($request);
        $filename = 'aicodesubjectswise_studentdata.' . $type;
        Excel::store($obj, $filename);
        return Excel::download($obj, $filename);

    }


    public function aicenterWiseCountFreshAndSupplementary(Request $request)
    {
        $title = "AiCenter Wise Student Count";
        $exam_year = CustomHelper::_get_selected_sessions();
        //$exam_month = config("global.current_exam_month_id");
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
        $combo_name = 'fresh_supp_options';
        $fresh_supp_options = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        if (@$request->all()) {
            $custom_component_obj = new CustomComponent;
            $aiCenters = $custom_component_obj->getAiCenters();

            $stream = $request->stream;
            if (@$request->fresh_supp_option == 1) {
                $query1 = DB::select("SELECT ac.ai_code as Ai_code, SUM(CASE WHEN s.course =10 THEN 1 ELSE 0 	END) as Course10, SUM(CASE WHEN s.course =12 THEN 1 ELSE 0 END) as Course12, count(*) as Total FROM rs_aicenter_details ac inner JOIN rs_students s ON s.ai_code = ac.ai_code WHERE s.exam_month=$stream and s.is_eligible = 1 and s.exam_year =  $exam_year and s.deleted_at is null group by ac.ai_code ;");
            } elseif (@$request->fresh_supp_option == 2) {
                $query2 = DB::select("SELECT ac.ai_code as Ai_code, SUM(CASE WHEN s.course =10 THEN 1 ELSE 0 END) as Course10, SUM(CASE WHEN s.course =12 THEN 1 ELSE 0 END) as Course12, count(*) as Total FROM rs_aicenter_details ac inner JOIN rs_supplementaries s ON s.ai_code = ac.ai_code WHERE s.exam_month=$stream and s.is_eligible = 1 and s.exam_year = $exam_year and s.deleted_at is null group by ac.ai_code");
            } else {
                $query1 = DB::select("SELECT ac.ai_code as Ai_code, SUM(CASE WHEN s.course =10 THEN 1 ELSE 0 	END) as Course10, SUM(CASE WHEN s.course =12 THEN 1 ELSE 0 END) as Course12, count(*) as Total FROM rs_aicenter_details ac inner JOIN rs_students s ON s.ai_code = ac.ai_code WHERE s.exam_month=$stream and s.is_eligible = 1 and s.exam_year =  $exam_year and s.deleted_at is null group by ac.ai_code ;");
                $query2 = DB::select("SELECT ac.ai_code as Ai_code, SUM(CASE WHEN s.course =10 THEN 1 ELSE 0 END) as Course10, SUM(CASE WHEN s.course =12 THEN 1 ELSE 0 END) as Course12, count(*) as Total FROM rs_aicenter_details ac inner JOIN rs_supplementaries s ON s.ai_code = ac.ai_code WHERE s.exam_month=$stream and s.is_eligible = 1 and s.exam_year = $exam_year and s.deleted_at is null group by ac.ai_code");
            }

            $freshArr = array();
            if (@$query1) {
                foreach ($query1 as $k => $v) {
                    $freshArr[$v->Ai_code]['Course10'] = "0";
                    if ($v->Course10 > 0) {
                        $freshArr[$v->Ai_code]['Course10'] = "$v->Course10";
                    }

                    $freshArr[$v->Ai_code]['Course12'] = "0";
                    if ($v->Course12 > 0) {
                        $freshArr[$v->Ai_code]['Course12'] = "$v->Course12";
                    }

                    $freshArr[$v->Ai_code]['Total'] = "0";
                    if ($v->Total > 0) {
                        $freshArr[$v->Ai_code]['Total'] = $v->Total;
                    }
                }
            }


            $suppArr = array();
            if (@$query2) {
                foreach ($query2 as $k => $v) {
                    $suppArr[$v->Ai_code]['Course10'] = "0";
                    if ($v->Course10 > 0) {
                        $suppArr[$v->Ai_code]['Course10'] = "$v->Course10";
                    }

                    $suppArr[$v->Ai_code]['Course12'] = "0";
                    if ($v->Course12 > 0) {
                        $suppArr[$v->Ai_code]['Course12'] = "$v->Course12";
                    }

                    $suppArr[$v->Ai_code]['Total'] = "0";
                    if ($v->Total > 0) {
                        $suppArr[$v->Ai_code]['Total'] = "$v->Total";
                    }
                }
            }
            $data = array();
            foreach ($aiCenters as $ai_code => $ai_name) {
                $data[$ai_code] = array(
                    'Ai_code' => @$ai_code,
                    'Course10' => @$freshArr[$ai_code]['Course10'] + @$suppArr[$ai_code]['Course10'],
                    'Course12' => @$freshArr[$ai_code]['Course12'] + @$suppArr[$ai_code]['Course12'],
                    'Total' => @$freshArr[$ai_code]["Total"] + @$suppArr[$ai_code]["Total"]
                );
            }
            $type = "xlsx";
            $subject_count_data = new AiCenterWiseSubjectCountExlExport($data);
            $filename = 'aicenterWiseCountFreshAndSupplementary' . date('d-m-Y H:i:s') . '.' . $type;
            return Excel::download($subject_count_data, $filename);
        }

        return view('admission_reports.aicodewisefreshandsupplemtarydata', compact('fresh_supp_options', 'stream_id', 'title', 'breadcrumbs'));
    }

    public function reval_student_applications(Request $request)
    {
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $combo_name = 'reval_types';
        $reval_types = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'supp_verfication_status';
        $supp_verfication_status = $this->master_details($combo_name);
        $district_list = $this->districtsByState();
        $yes_no_temp = $this->master_details('yesno');
        $yes_no_temp[""] = "No";
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $role_id = Session::get('role_id');
        $aicenter_id_role = config("global.aicenter_id");
        $yes_no = $this->master_details('yesno');
        $title = "Reval Applicaiton Report";
        $table_id = "Admission_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $exam_year = CustomHelper::_get_selected_sessions();
        $form_supp_current_admission_session_id = config("global.form_supp_current_admission_session_id");
        $supp_current_admission_exam_month = config("global.supp_current_admission_exam_month");
        //need to put in funciton end
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $permissions = CustomHelper::roleandpermission();
        $yes_no_temp = $this->master_details('yesno');
        $yes_no_temp[""] = "No";

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
                "label" => "Export Excel",
                'url' => 'downloadRevalApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadSupplementaryApplicationPdf',
                'status' => false,
            ),
        );

        $supp_exam_month = Config::get('global.supp_current_admission_exam_month');
        $temp_exam_month = @$exam_month->toArray();
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        if (@$temp_exam_month[1]) {
            if ($isAdminStatus == false) {
                if ($supp_exam_month == 2) {
                    unset($temp_exam_month[1]);
                }
            }
        }
        if ($request->exam_month == 1) {
            unset($exam_month[2]);
        } else {
            unset($exam_month[1]);
        }


        $filters = array(
            array(
                "lbl" => "Start date",
                'fld' => 'start_date',
                'input_type' => 'datetime-local',
                'placeholder' => "Start Date",
                'dbtbl' => 'reval_students',
            ),
            array(
                "lbl" => "End date",
                'fld' => 'end_date',
                'input_type' => 'datetime-local',
                'placeholder' => "End Date",
                'dbtbl' => 'reval_students',
            ),
            array(
                "lbl" => "Payment Done",
                'fld' => 'challan_tid2',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Payment',
                'dbtbl' => 'reval_students',
            ),
            array(
                "lbl" => "Is Eligible",
                'fld' => 'is_eligible',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Is Eligible',
                'dbtbl' => 'reval_students',
            ),

            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'reval_students',
            ),
            array(
                "lbl" => "Total Fees",
                'fld' => 'total_fees2',
                'input_type' => 'text',
                'placeholder' => "Total fees",
                'dbtbl' => 'reval_students',
            ),

            array(
                "lbl" => "Mobile Number",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Mobile Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Challan Number",
                'fld' => 'challan_tid',
                'input_type' => 'text',
                'placeholder' => "Challan Number",
                'dbtbl' => 'reval_students',
            ),

            array(
                "lbl" => "Late Fees",
                'fld' => 'late_fees',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Late Fees',
                'dbtbl' => 'reval_students',
            ),
            // array(
            // 	"lbl" => "Total Fees",
            // 	'fld' => 'total_fees',
            // 	'input_type' => 'select',
            // 	'options' => $yes_no,
            // 	'placeholder' => 'Total Fees',
            // 	'dbtbl' => 'reval_students',
            // ),
            array(
                "lbl" => "Father Name",
                'fld' => 'father_name',
                'input_type' => 'text',
                'placeholder' => "Father Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Mother Name",
                'fld' => 'mother_name',
                'input_type' => 'text',
                'placeholder' => "Mother Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Jan Aadhar Number",
                'fld' => 'jan_aadhar_number',
                'input_type' => 'text',
                'placeholder' => "Jan Aadhar Number",
                'dbtbl' => 'applications',
            ),
            array(
                "lbl" => "Aadhar Number",
                'fld' => 'aadhar_number',
                'input_type' => 'text',
                'placeholder' => "Aadhar Number",
                'dbtbl' => 'applications',
            ),
            array(
                "lbl" => "Amount",
                'fld' => 'fee_paid_amount',
                'input_type' => 'text',
                'placeholder' => "Amount",
                'dbtbl' => 'reval_students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'reval_students',
            ),
            array(
                "lbl" => "Reval Application Type",
                'fld' => 'reval_type',
                'input_type' => 'select',
                'options' => $reval_types,
                'placeholder' => 'Reval Application Type',
                'dbtbl' => 'reval_students',
            ),
            /*array(
				"lbl" => "Stream ",
				'fld' => 'stream',
				'input_type' => 'select',
				'options' => $stream_id,
				'placeholder' => 'Stream Type',
				'dbtbl' => 'supplementaries',
			),*/
            array(
                "lbl" => "Exam Month ",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'reval_students',
            ),
            array(
                "lbl" => "Admission Type ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Lock & Submit',
                'dbtbl' => 'reval_students',
            ),
            array(
                "lbl" => "Are You From Rajasthan And Not",
                'fld' => 'are_you_from_rajasthan',
                'input_type' => 'select',
                'options' => $are_you_from_rajasthan,
                'search_type' => "text",
                'placeholder' => 'Are You From Rajasthan And Not',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Is Self Filled",
                'fld' => 'is_self_filled',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Is Self Filled',
                'dbtbl' => 'reval_students',
            ),
            array(
                "lbl" => "Is SSO Filled",
                'fld' => 'ssoid',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Is SSO Filled',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid2',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),

        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number"
            )
        , array(
                "lbl" => "Student Fixcode",
                'fld' => 'fixcode',
                'input_type' => 'text',
                'placeholder' => "Student Fixcode"
            )
        , array(
                "lbl" => "Center Fixcode",
                'fld' => 'examcenter_fixcode',
                'input_type' => 'text',
                'placeholder' => "Center Fixcode"
            ), array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO"
            ),
            array(
                "lbl" => "Is Eligible",
                'fld' => 'is_eligible',
                'input_type' => 'select',
                'options' => $yes_no_temp
            ),
            array(
                "lbl" => "Reval Application Type",
                'fld' => 'reval_type',
                'input_type' => 'select',
                'options' => $reval_types
            )
        , array(
                "lbl" => "AI Code",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'placeholder' => "AI Code"
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course ",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            /*array(
				"lbl" => "Stream ",
				'fld' => 'stream',
				'input_type' => 'select',
				'options' => $stream_id
			),*/
            array(
                "lbl" => "Exam Month ",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month
            ),

            // array(
            // 	"lbl" => "Admission ",
            // 	'fld' => 'adm_type',
            // 	'input_type' => 'select',
            // 	'options' => $adm_types
            // ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            array(
                "lbl" => "Fees Amount",
                'fld' => 'total_fees'
            ),
            array(
                "lbl" => "Challan Number",
                'fld' => 'challan_tid'
            ),

            array(
                "lbl" => "Is Self Filled",
                'fld' => 'is_self_filled',
                'input_type' => 'select',
                'options' => $yes_no_temp
            ),


            array(
                "lbl" => "Submitted",
                'fld' => 'submitted',
            ),

        );


        $conditions = array();
        $conditions["reval_students.exam_year"] = CustomHelper::_get_selected_sessions();
        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $evaluation_department = config("global.evaluation_department");
            if ($role_id == $evaluation_department) {
            } else {
                //if role is 59 ai center then get session aicode and then aicenter_detail_id put in condition
                $aicenter_user_id = Auth::user()->id;
                $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
                $auth_user_id = $aicenter_user_ids->ai_code;
                $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode($auth_user_id);
                $aicenter_mapped_data_conditions = $aicenter_mapped_data;
            }
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'reval_students'
            );

        }

        if (in_array("Reval_student_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => '../reval/reval_preview_details/#id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../reval/reval_preview_details/#id#'
                ),

            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../reval/reval_preview_details/#id#'
                ),
            );
        }

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }
        $symbol = null;
        $symbols = null;
        $symbol2 = null;
        $symbolss = null;
        $symbolssoid = null;
        if ($request->all()) {
            $inputs = $request->all();
            $this->validate($request, [
                'exam_month' => 'required|numeric',
            ], [
                'exam_month.required' => 'Exam Month is required',
            ]);
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (@$iv['fld'] == $k && $iv['fld'] == $k) {
                            if ($iv['fld'] == 'late_fees' && $inputs[$iv['fld']] == 1) {
                                $symbol = "!=";
                            } else {
                                $symbol = "=";
                            }

                            if ($iv['fld'] == 'challan_tid2' && $inputs[$iv['fld']] == 1) {
                                $symbol2 = "!=";
                            } elseif ($iv['fld'] == 'challan_tid2' && $inputs[$iv['fld']] == 0) {
                                $symbol2 = "=";
                            }

                            if ($iv['fld'] == 'total_fees' && $inputs[$iv['fld']] == 1) {
                                $symbols = "!=";
                            } else {
                                $symbols = "=";
                            }
                            if ($iv['fld'] == 'is_self_filled' && $inputs[$iv['fld']] == 1) {
                                $symbolss = "!=";
                            } elseif ($iv['fld'] == 'is_self_filled' && $inputs[$iv['fld']] == 0) {
                                $symbolss = "=";
                            }
                            if ($iv['fld'] == 'ssoid' && $inputs[$iv['fld']] == 1) {
                                $symbolssoid = "!=";
                            } elseif ($iv['fld'] == 'ssoid' && $inputs[$iv['fld']] == 0) {
                                $symbolssoid = "=";
                            } else {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            }
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }

        Session::put($formId . '_conditions', $conditions);
        Session::put($formId . '_aicenter_mapped_data_conditions', @$aicenter_mapped_data_conditions);
        Session::put($formId . '_symbol', $symbol);
        Session::put($formId . '_symbols', $symbols);
        Session::put($formId . '_symbol2', $symbol2);
        Session::put($formId . '_symbolss', $symbolss);
        Session::put($formId . '_symbolssoid', $symbolssoid);

        if ($role_id == $aicenter_id_role) {
            $master = $custom_component_obj->getRevalApplicationData($formId, true, $aicenter_mapped_data_conditions);
        } else {
            $master = $custom_component_obj->getRevalApplicationData($formId);
        }
        return view('admission_reports.reval_student_applications', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());

    }


    public function downloadRevalApplicationExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new RevalApplicationExlExport;
        $filename = 'revalapplication_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function Practical_mapped(Request $request)
    {
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

        $yes_no = $this->master_details('yesno');
        $title = "Practical Mapped Report";
        $table_id = "Practical_Mapped_Report";
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
                "label" => "Export Excel",
                'url' => 'downloadApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => false
            ),
        );


        /* $filters =  array(
			array(
				"lbl" => "Enrollment",
				'fld' => 'enrollment',
				'input_type' => 'text',
				'placeholder' => "Enrollment Number",
				'dbtbl' => 'students',
			),
			array(
				"lbl" => "Ai Code",
				'fld' => 'ai_code',
				'input_type' => 'text',
				'placeholder' => "Ai Code",
				'dbtbl' => 'students',
			),
			array(
				"lbl" => "Name",
				'fld' => 'name',
				'input_type' => 'text',
				'placeholder' => "Student Name",
				'dbtbl' => 'students',
			),
			array(
				"lbl" => "Gender",
				'fld' => 'gender_id',
				'input_type' => 'select',
				'options' => $gender_id,
				'placeholder' => 'Gender Type',
				'dbtbl' => 'students',
			),
			array(
				"lbl" => "Course",
				'fld' => 'course',
				'input_type' => 'select',
				'options' => $course,
				'placeholder' => 'Course Type',
				'dbtbl' => 'students',
			),
			array(
				"lbl" => "Stream ",
				'fld' => 'stream',
				'input_type' => 'select',
				'options' => $stream_id,
				'placeholder' => 'Stream Type',
				'dbtbl' => 'students',
			),
			array(
				"lbl" => "Admission ",
				'fld' => 'adm_type',
				'input_type' => 'select',
				'options' => $adm_types,
				'placeholder' => 'Admission Type',
				'dbtbl' => 'students',
			),
			array(
				"lbl" => "Medium",
				'fld' => 'medium',
				'input_type' => 'select',
				'options' => $midium,
				'placeholder' => 'Medium Type',
				'dbtbl' => 'applications',
			),
			array(
				"lbl" => "Lock & Submit",
				'fld' => 'locksumbitted',
				'input_type' => 'select',
				'options' => $yes_no,
				'placeholder' => 'Lock & Submit',
				'dbtbl' => 'applications',
			)
		);*/
        $filters = array();
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                            $conditions[$iv['dbtbl'] . "." . $k] = $v;
                        } else {
                            $conditions[$k] = $v;
                        }
                        break;
                    }
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);

        $master = $custom_component_obj->getPracticalmappedalldata($formId);

        return view('admission_reports.Practical_mapped_all_data', compact('master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function getMarksheetRequestData(Request $request)
    {
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
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $combo_name = 'faculties';
        $faculties = $this->master_details($combo_name);
        $combo_name = 'yes_no_2';
        $yes_no_2 = $this->master_details($combo_name);
        $combo_name = 'doc_verification_status';
        $doc_verification_status = $this->master_details($combo_name);
        $combo_name = 'marsheet_type';
        $marsheet_type = $this->master_details($combo_name);
        $combo_name = 'marksheet_print_option';
        $document_type = $this->master_details($combo_name);

        $district_list = $this->districtsByState();
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $defaultPageLimit = config("global.defaultPageLimit");
        $yes_no = $this->master_details('yesno');
        $yes_no_temp = $this->master_details('yesno');
        $fee_paid_status = $yes_no;
        unset($yes_no_temp['0']);
        $yes_no_temp[""] = "No";
        $isDocVerified = $doc_verification_status;
        $title = "Revised/Duplicate Marksheet/Migration Report";
        $table_id = "";
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
        if (in_array("download_Application_Exl", $permissions)) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadApplicationExl',
                    'status' => true,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        } else {
            $exportBtn = array();
        }
        $role_id = @Session::get('role_id');
        $Printer = config("global.Printer");

        $filters = array(
            /*array(
				"lbl" => "Start date",
				'fld' => 'start_date',
				'input_type' => 'datetime-local',
				'placeholder' => "Start Date",
				'dbtbl' => 'students',
			),
			array(
				"lbl" => "End date",
				'fld' => 'end_date',
				'input_type' => 'datetime-local',
				'placeholder' => "End Date",
				'dbtbl' => 'students',
			),*/
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'search_type' => "text",
                'dbtbl' => 'students',
            ),
            //array(
            //"lbl" => "Amount",
            //'fld' => 'fee_paid_amount',
            //'input_type' => 'text',
            //'placeholder' => "Amount",
            //'search_type' => "text",
            //'dbtbl' => 'applications',
            //),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'search_type' => "like", //like
                'dbtbl' => 'students',
            ),
            //array(
            //	"lbl" => "Challan Number",
            //	'fld' => 'challan_tid',
            //	'input_type' => 'text',
            //'placeholder' => "Challan Number",
            //'search_type' => "text", //like
            //	'dbtbl' => 'students',
            //),
            //array(
            //	"lbl" => "Gender",
            //	'fld' => 'gender_id',
            //	'input_type' => 'select',
            //	'options' => $gender_id,
            //	'search_type' => "text",
            //	'placeholder' => 'Gender Type',
            //	'dbtbl' => 'students',
            //),
            //array(
            //	"lbl" => "Course",
            //	'fld' => 'course',
            //	'input_type' => 'select',
            //	'options' => $course,
            //	'search_type' => "text",
            //	'placeholder' => 'Course Type',
            //	'dbtbl' => 'students',
            //),
            /*
			array(
				"lbl" => "Stream ",
				'fld' => 'stream',
				'input_type' => 'select',
				'options' => $stream_id,
				'search_type' => "text",
				'placeholder' => 'Stream Type',
				'dbtbl' => 'students',
			),
			array(
				"lbl" => "Admission ",
				'fld' => 'adm_type',
				'input_type' => 'select',
				'options' => $adm_types,
				'search_type' => "text",
				'placeholder' => 'Admission Type',
				'dbtbl' => 'students',
			),*/
            array(
                "lbl" => "Marksheet Type",
                'fld' => 'marksheet_type',
                'input_type' => 'select',
                'search_type' => "text",
                'options' => $marsheet_type,
                'placeholder' => 'Marksheet type',
                'dbtbl' => 'marksheet_migration_requests',
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no_temp,
                'search_type' => "text",
                'placeholder' => 'Lock & Submit',
                'dbtbl' => 'marksheet_migration_requests',
            ),
            array(
                "lbl" => "Fee Status",
                'fld' => 'fee_status',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Fee Status',
                'dbtbl' => 'marksheet_migration_requests',
            ),
            array(
                "lbl" => "Correction Update Status",
                'fld' => 'correction_update',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Correction Update Status',
                'dbtbl' => 'marksheet_migration_requests',
            ),
            array(
                "lbl" => "Marksheet Migration Status",
                'fld' => 'marksheet_migration_status',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Marksheet Migration Status',
                'dbtbl' => 'marksheet_migration_requests',
            ),

        );

        $conditions = array();
        $actions = array();
        /* Sorting Fields Set Start 1*/
        $sorting = array();
        $orderByRaw = "";
        $inputs = "";
        $sortingField = $this->_getSortingFields($filters);
        /* Sorting Fields Set End 1*/
        $symbol = null;
        $symbols = null;
        $symbolis = null;
        if ($request->all()) {
            $inputs = $request->all();
            if (@$inputs['is_full'] && $inputs['is_full'] == 1) {
                if ($isAdminStatus == true) {
                    unset($conditions["students.exam_year"]);
                }
            }
            foreach ($inputs as $k => $v) {
                if ($k == 'is_full') {
                    continue;
                }
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (@$iv['fld'] == $k && $iv['fld'] == $k) {
                            if ($iv['fld'] == 'enrollmentgen' && $inputs[$iv['fld']] == 1) {
                                $symbol = "!=";
                            } elseif ($iv['fld'] == 'enrollmentgen' && $inputs[$iv['fld']] == 0) {
                                $symbol = "=";
                            } else {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            }

                            if ($iv['fld'] == 'is_otp_verified' && $inputs[$iv['fld']] == 1) {
                                $symbolis = "!=";
                            } elseif ($iv['fld'] == 'is_otp_verified' && $inputs[$iv['fld']] == 0) {
                                $symbolis = "=";
                            } else {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            }
                            if ($iv['fld'] == 'is_self_filled' && $inputs[$iv['fld']] == 1) {
                                $symbols = "!=";
                            } elseif ($iv['fld'] == 'is_self_filled' && $inputs[$iv['fld']] == 0) {
                                $symbols = "=";
                            } else {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            }
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    //$conditions[ $iv['dbtbl'] . "." . $k] = $v;
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }

            /* Sorting Order By Set Start 2*/
            //$orderByRaw = $this->_setSortingArrayFields(@$inputs['sorting'],$sortingField);
            /* Sorting Order By Set End 2*/
        }
        /* Sorting Fields Set Session Start 3*/
        Session::put($formId . '_orderByRaw', $orderByRaw);
        /* Sorting Fields Set Session End 3*/
        Session::put($formId . '_conditions', $conditions);
        Session::put($formId . '_symbol', $symbol);
        Session::put($formId . '_symbols', $symbols);
        Session::put($formId . 'symbolis', $symbolis);

        $master = $custom_component_obj->getRevisedDuplicateRequestData($formId);
        return view('admission_reports.marksheet_migration_request_report', compact('yes_no_temp', 'are_you_from_rajasthan', 'district_list', 'actions', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'document_type', 'marsheet_type', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'fee_paid_status'))->withInput($request->all());


    }

    public function change_request_student_applications(Request $request)
    {
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
        $checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
        $combo_name = 'change_request_stream';
        $change_request_stream = $this->master_details($combo_name);
        $role_id = @Session::get('role_id');
        $superadminrole = Config::get("global.super_admin");
        if (@$role_id != $superadminrole) {
            return redirect()->back()->with('message', '403 USER DOES NOT HAVE THE RIGHT PERMISSIONS.');
        }
        $changerequeststreamapproved = DB::table('masters')->where('combo_name', '=', 'change_request_stream')->first();
        @$exammomths = $request->exam_month;
        if (@$request->exam_month == 1) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[2]);
            $student_change_request = array("1" => "Pending", "2" => "Approved");
            $student_change_requests = collect($student_change_request);
            if (@$request->student_change_requests == 1) {
                unset($student_change_requests[2]);
            } else {
                unset($student_change_requests[1]);
            }
        } elseif (@$request->exam_month == 2) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[1]);
            $student_change_request = array("1" => "Pending", "2" => "Approved");
            $student_change_requests = collect($student_change_request);
            if (@$request->student_change_requests == 1) {
                unset($student_change_requests[2]);
            } else {
                unset($student_change_requests[1]);
            }
        }
        $title = "Change Request Student Applications";
        $table_id = "verifying_student_applications";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;
        $aiCenters = array();
        $role_id = Session::get('role_id');
        $verifier_id = Config::get("global.verifier_id");
        $super_admin_id = Config::get("global.super_admin_id");
        if ($role_id == $verifier_id) {
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            $aiCenters = $custom_component_obj->getAiCentersForVerifier($auth_user_id);
        } else {
            $aiCenters = $custom_component_obj->getAiCenters();
        }
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
        $exportBtn = array();
        if ($super_admin_id == $role_id) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadchangerequertStudentExl',
                    'status' => true,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        }


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Code',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Medium",
                'fld' => 'medium',
                'input_type' => 'select',
                'options' => $midium,
                'placeholder' => 'Medium Type',
                'dbtbl' => 'applications',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_months,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Student Change Request",
                'fld' => 'student_change_requests',
                'input_type' => 'select',
                'options' => $student_change_requests,
                'placeholder' => 'Student Change Request',
                'dbtbl' => 'students',
            ),

        );
        $role_id = Session::get('role_id');

        $tableData = array();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($role_id == config("global.super_admin_id")) {

        }

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            Session::put("ai_code", $auth_user_id);
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
            $conditions["students.aicenter_mapped_data"] = $aicenter_mapped_data;
        } else {
        }
        $actions = array();
        if (in_array("document_verification_of_student", $permissions)) {
            $actions[] = array(
                'fld' => 'verify_documents',
                'extraCondition' => 'student_applications',
                'icon' => '<i title="Verify the documents" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Verify</i>',
                'fld_url' => '../student/verify_documents/#id#'
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        // echo "ttt";
        if (@$inputs['extra'] && !empty($inputs['extra'])) {
            $conditions['extra'] = $inputs['extra'];
        }

        //dd($formId . '_conditions'); //Fresh_Student_Verificaiton_conditions
        Session::put($formId . '_conditions', $conditions);
        $master = $custom_component_obj->getchangerequestallStudentdata($formId);

        return view('change_request.change_request_student_applications', compact('exammomths', 'changerequeststreamapproved', 'change_request_stream', 'tableData', 'actions', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'midium', 'course', 'adm_types', 'stream_id', 'checkchangerequestsAllowOrNotAllow'))->withInput($request->all());
    }

    public function downloadchangerequertStudentExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new ChangeRequertStudentExlExport;
        $filename = 'Change_Requert_Student' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }


    public function supp_change_request_student_applications(Request $request)
    {
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
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $checkchangerequestsssupplementariesAllowOrNotAllow = $this->_checkchangerequestssupplementariesAllowOrNotAllow();
        $combo_name = 'change_request_stream';
        $change_request_stream = $this->master_details($combo_name);
        $changerequeststreamapproved = DB::table('masters')->where('combo_name', '=', 'change_request_stream')->first();
        @$exammomths = $request->exam_month;
        if (@$request->exam_month == 1) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[2]);
            $student_change_request = array("1" => "Pending", "2" => "Approved");
            $supp_student_change_requests = collect($student_change_request);
            if (@$request->supp_student_change_requests == 1) {
                unset($supp_student_change_requests[2]);
            } else {
                unset($supp_student_change_requests[1]);
            }
        } elseif (@$request->exam_month == 2) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[1]);
            $student_change_request = array("1" => "Pending", "2" => "Approved");
            $supp_student_change_requests = collect($student_change_request);
            if (@$request->supp_student_change_requests == 1) {
                unset($supp_student_change_requests[2]);
            } else {
                unset($supp_student_change_requests[1]);
            }
        }
        $title = "Supp Change Request Student Applications";
        $table_id = "supp_verifying_student_applications";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;
        $role_id = Session::get('role_id');
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
        $exportBtn = array();
        $examinationdepartment = Config::get("global.examination_department");
        if ($examinationdepartment == $role_id) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadchangerequertStudentExl',
                    'status' => false,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        }


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Code',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_months,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Supp Student Change Request",
                'fld' => 'supp_student_change_requests',
                'input_type' => 'select',
                'options' => $supp_student_change_requests,
                'placeholder' => 'Student Change Request',
                'dbtbl' => 'supplementaries',
            ),

        );
        $role_id = Session::get('role_id');

        $tableData = array();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        //$conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($role_id == config("global.super_admin_id")) {

        }

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            Session::put("ai_code", $auth_user_id);
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
            $conditions["students.aicenter_mapped_data"] = $aicenter_mapped_data;
        } else {
        }
        $actions = array();
        if (in_array("document_verification_of_student", $permissions)) {
            $actions[] = array(
                'fld' => 'verify_documents',
                'extraCondition' => 'student_applications',
                'icon' => '<i title="Verify the documents" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Verify</i>',
                'fld_url' => '../student/verify_documents/#id#'
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        // echo "ttt";
        if (@$inputs['extra'] && !empty($inputs['extra'])) {
            $conditions['extra'] = $inputs['extra'];
        }

        //dd($formId . '_conditions'); //Fresh_Student_Verificaiton_conditions
        Session::put($formId . '_conditions', $conditions);
        $master = $custom_component_obj->getsuppchangerequestallStudentdata($formId);

        return view('supp_change_request.supp_change_request_student_applications', compact('exammomths', 'changerequeststreamapproved', 'change_request_stream', 'tableData', 'actions', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'midium', 'course', 'adm_types', 'stream_id', 'checkchangerequestsssupplementariesAllowOrNotAllow'))->withInput($request->all());
    }


    public function supp_change_request_student_total_Generated(Request $request)
    {

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
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $checkchangerequestsssupplementariesAllowOrNotAllow = $this->_checkchangerequestssupplementariesAllowOrNotAllow();
        @$exammomths = $request->exam_month;
        $yes_no = $this->master_details('yesno');
        if (@$request->exam_month == 1) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[2]);
            @$supp_student_change_requests = array("1" => "Pending", "2" => "Approved");
            @$supp_student_change_requests = collect($supp_student_change_requests);
            if (@$request->supp_student_change_requests == 1) {
                unset($supp_student_change_requests[2]);
            } else {
                unset($supp_student_change_requests[1]);
            }
        } elseif (@$request->exam_month == 2) {
            $combo_name = 'exam_month';
            @$exam_months = $this->master_details($combo_name);
            unset($exam_months[1]);
            @$supp_student_change_requests = array("1" => "Pending", "2" => "Approved");
            $supp_student_change_requests = collect($supp_student_change_requests);
            if (@$request->supp_student_change_requests == 1) {
                unset($supp_student_change_requests[2]);
            } else {
                unset($supp_student_change_requests[1]);
            }
        }
        if (@$request->supp_student_change_requests == 2) {
            $title = "Supp Change Request Department Approved";
        } elseif (@$request->student_update_application == 1) {
            $title = "Supp Change Request Student Application update";
        } elseif (@$request->locksumbitted == 1) {
            $title = "Supp Change Request Student Lock Sumbitted";
        } else {
            $title = "Supp Change Request Total Generated";
        }


        $table_id = "supp_verifying_student_applications";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;
        $role_id = Session::get('role_id');
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
        $exportBtn = array();
        $examinationdepartment = Config::get("global.examination_department");
        if ($examinationdepartment == $role_id) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadchangerequertStudentExl',
                    'status' => false,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        }


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Code',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_months,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'supp_change_request_students',
            ),

            array(
                "lbl" => "Supp Student Change Request",
                'fld' => 'supp_student_change_requests',
                'input_type' => 'select',
                'options' => $supp_student_change_requests,
                'placeholder' => 'Supp Student Change Request',
                'dbtbl' => 'supp_change_request_students',
            ),

            array(
                "lbl" => "Supp Student Update Application",
                'fld' => 'supp_student_update_application',
                'input_type' => 'select',
                'options' => $supp_student_change_requests,
                'placeholder' => 'Supp Student Change Request',
                'dbtbl' => 'supp_change_request_students',
            ),


        );


        $role_id = Session::get('role_id');

        $tableData = array();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        //$conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($role_id == config("global.super_admin_id")) {

        }

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            Session::put("ai_code", $auth_user_id);
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
            $conditions["students.aicenter_mapped_data"] = $aicenter_mapped_data;
        } else {
        }
        $actions = array();
        if (in_array("document_verification_of_student", $permissions)) {
            $actions[] = array(
                'fld' => 'verify_documents',
                'extraCondition' => 'student_applications',
                'icon' => '<i title="Verify the documents" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Verify</i>',
                'fld_url' => '../student/verify_documents/#id#'
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        // echo "ttt";
        if (@$inputs['extra'] && !empty($inputs['extra'])) {
            $conditions['extra'] = $inputs['extra'];
        }
        $is_supplementary = null;
        //if(@$conditions['is_supplementary'] && !empty($conditions['is_supplementary'])){
        //$is_supplementary = $conditions['is_supplementary'];
        //unset($conditions['is_supplementary']);
        //}


        //dd($formId . '_conditions'); //Fresh_Student_Verificaiton_conditions
        Session::put($formId . '_conditions', $conditions);
        $master = [];

        $master = $custom_component_obj->SuppgetchangerequestallStudentdataGenerated($formId, $exammomths);

        return view('supp_change_request.supp_change_request_total_applications', compact('exammomths', 'tableData', 'actions', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'midium', 'course', 'adm_types', 'stream_id', 'checkchangerequestsssupplementariesAllowOrNotAllow'))->withInput($request->all());
    }

    public function supp_change_request_student_student_not_update_applications(Request $request)
    {

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
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $checkchangerequestsssupplementariesAllowOrNotAllow = $this->_checkchangerequestssupplementariesAllowOrNotAllow();
        @$exammomths = $request->exam_month;
        $yes_no = $this->master_details('yesno');
        if (@$request->exam_month == 1) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[2]);
            @$supp_student_change_requests = array("1" => "Pending", "2" => "Approved");
            @$supp_student_change_requests = collect($supp_student_change_requests);
            if (@$request->supp_student_change_requests == 1) {
                unset($supp_student_change_requests[2]);
            } else {
                unset($supp_student_change_requests[1]);
            }
        } elseif (@$request->exam_month == 2) {
            $combo_name = 'exam_month';
            @$exam_months = $this->master_details($combo_name);
            unset($exam_months[1]);
            @$supp_student_change_requests = array("1" => "Pending", "2" => "Approved");
            $supp_student_change_requests = collect($supp_student_change_requests);
            if (@$request->supp_student_change_requests == 1) {
                unset($supp_student_change_requests[2]);
            } else {
                unset($supp_student_change_requests[1]);
            }
        }
        $title = "Supp Change Request Department Approved student not click Application update";

        $table_id = "supp_verifying_student_applications";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;
        $role_id = Session::get('role_id');
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
        $exportBtn = array();
        $examinationdepartment = Config::get("global.examination_department");
        if ($examinationdepartment == $role_id) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadchangerequertStudentExl',
                    'status' => false,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        }


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Code',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_months,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'supp_change_request_students',
            ),


        );


        $role_id = Session::get('role_id');

        $tableData = array();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        //$conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($role_id == config("global.super_admin_id")) {

        }

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            Session::put("ai_code", $auth_user_id);
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
            $conditions["students.aicenter_mapped_data"] = $aicenter_mapped_data;
        } else {
        }
        $actions = array();
        if (in_array("document_verification_of_student", $permissions)) {
            $actions[] = array(
                'fld' => 'verify_documents',
                'extraCondition' => 'student_applications',
                'icon' => '<i title="Verify the documents" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Verify</i>',
                'fld_url' => '../student/verify_documents/#id#'
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        // echo "ttt";
        if (@$inputs['extra'] && !empty($inputs['extra'])) {
            $conditions['extra'] = $inputs['extra'];
        }
        $is_supplementary = null;
        //if(@$conditions['is_supplementary'] && !empty($conditions['is_supplementary'])){
        //$is_supplementary = $conditions['is_supplementary'];
        //unset($conditions['is_supplementary']);
        //}


        //dd($formId . '_conditions'); //Fresh_Student_Verificaiton_conditions
        Session::put($formId . '_conditions', $conditions);
        $master = [];

        $master = $custom_component_obj->getsuppchangerequeststudnetnotclickupdateapplications($formId, $exammomths);

        return view('supp_change_request.supp_change_request_total_applications', compact('exammomths', 'tableData', 'actions', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'midium', 'course', 'adm_types', 'stream_id', 'checkchangerequestsssupplementariesAllowOrNotAllow'))->withInput($request->all());
    }


    public function supp_change_request_student_not_locksumbitted(Request $request)
    {

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
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $checkchangerequestsssupplementariesAllowOrNotAllow = $this->_checkchangerequestssupplementariesAllowOrNotAllow();
        @$exammomths = $request->exam_month;
        @$locksumbitted = $request->locksumbitted;
        $yes_no = $this->master_details('yesno');
        if (@$request->exam_month == 1) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[2]);
            @$supp_student_change_requests = array("1" => "Pending", "2" => "Approved");
            @$supp_student_change_requests = collect($supp_student_change_requests);
            if (@$request->supp_student_change_requests == 1) {
                unset($supp_student_change_requests[2]);
            } else {
                unset($supp_student_change_requests[1]);
            }
        } elseif (@$request->exam_month == 2) {
            $combo_name = 'exam_month';
            @$exam_months = $this->master_details($combo_name);
            unset($exam_months[1]);
            @$supp_student_change_requests = array("1" => "Pending", "2" => "Approved");
            $supp_student_change_requests = collect($supp_student_change_requests);
            if (@$request->supp_student_change_requests == 1) {
                unset($supp_student_change_requests[2]);
            } else {
                unset($supp_student_change_requests[1]);
            }
        }
        if (@$request->locksumbitted == 1) {
            $title = "Supp Change Request lock & submitted";
        } else {
            $title = "Supp Change Request not lock & submitted";
        }


        $table_id = "supp_verifying_student_applications";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;
        $role_id = Session::get('role_id');
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
        $exportBtn = array();
        $examinationdepartment = Config::get("global.examination_department");
        if ($examinationdepartment == $role_id) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadchangerequertStudentExl',
                    'status' => false,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        }


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Code',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_months,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'supp_change_request_students',
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Lock & Submit',
                'dbtbl' => 'supplementaries',
            ),


        );


        $role_id = Session::get('role_id');

        $tableData = array();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        //$conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($role_id == config("global.super_admin_id")) {

        }

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            Session::put("ai_code", $auth_user_id);
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
            $conditions["students.aicenter_mapped_data"] = $aicenter_mapped_data;
        } else {
        }
        $actions = array();
        if (in_array("document_verification_of_student", $permissions)) {
            $actions[] = array(
                'fld' => 'verify_documents',
                'extraCondition' => 'student_applications',
                'icon' => '<i title="Verify the documents" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Verify</i>',
                'fld_url' => '../student/verify_documents/#id#'
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        // echo "ttt";
        if (@$inputs['extra'] && !empty($inputs['extra'])) {
            $conditions['extra'] = $inputs['extra'];
        }
        $is_supplementary = null;
        //if(@$conditions['is_supplementary'] && !empty($conditions['is_supplementary'])){
        //$is_supplementary = $conditions['is_supplementary'];
        //unset($conditions['is_supplementary']);
        //}


        //dd($formId . '_conditions'); //Fresh_Student_Verificaiton_conditions
        Session::put($formId . '_conditions', $conditions);
        $master = [];

        $master = $custom_component_obj->getsuppchangerequeststudnetnotlocksumbitted($formId, $exammomths, $locksumbitted);

        return view('supp_change_request.supp_change_request_total_applications', compact('exammomths', 'tableData', 'actions', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'midium', 'course', 'adm_types', 'stream_id', 'checkchangerequestsssupplementariesAllowOrNotAllow'))->withInput($request->all());
    }


    public function supp_change_request_student_not_fees_pay(Request $request)
    {

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
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $checkchangerequestsssupplementariesAllowOrNotAllow = $this->_checkchangerequestssupplementariesAllowOrNotAllow();
        @$exammomths = $request->exam_month;
        @$locksumbitted = $request->locksumbitted;
        $yes_no = $this->master_details('yesno');
        if (@$request->exam_month == 1) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[2]);
            @$supp_student_change_requests = array("1" => "Pending", "2" => "Approved");
            @$supp_student_change_requests = collect($supp_student_change_requests);
            if (@$request->supp_student_change_requests == 1) {
                unset($supp_student_change_requests[2]);
            } else {
                unset($supp_student_change_requests[1]);
            }
        } elseif (@$request->exam_month == 2) {
            $combo_name = 'exam_month';
            @$exam_months = $this->master_details($combo_name);
            unset($exam_months[1]);
            @$supp_student_change_requests = array("1" => "Pending", "2" => "Approved");
            $supp_student_change_requests = collect($supp_student_change_requests);
            if (@$request->supp_student_change_requests == 1) {
                unset($supp_student_change_requests[2]);
            } else {
                unset($supp_student_change_requests[1]);
            }
        }
        if (@$request->locksumbitted == 1) {
            $title = "Supp Change Request lock & submitted";
        } else {
            $title = "Supp Change Request not lock & submitted";
        }


        $table_id = "supp_verifying_student_applications";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;
        $role_id = Session::get('role_id');
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
        $exportBtn = array();
        $examinationdepartment = Config::get("global.examination_department");
        if ($examinationdepartment == $role_id) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadchangerequertStudentExl',
                    'status' => false,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        }


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Code',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_months,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'supp_change_request_students',
            ),


        );


        $role_id = Session::get('role_id');

        $tableData = array();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        //$conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($role_id == config("global.super_admin_id")) {

        }

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            Session::put("ai_code", $auth_user_id);
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
            $conditions["students.aicenter_mapped_data"] = $aicenter_mapped_data;
        } else {
        }
        $actions = array();
        if (in_array("document_verification_of_student", $permissions)) {
            $actions[] = array(
                'fld' => 'verify_documents',
                'extraCondition' => 'student_applications',
                'icon' => '<i title="Verify the documents" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Verify</i>',
                'fld_url' => '../student/verify_documents/#id#'
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        // echo "ttt";
        if (@$inputs['extra'] && !empty($inputs['extra'])) {
            $conditions['extra'] = $inputs['extra'];
        }
        $is_supplementary = null;
        //if(@$conditions['is_supplementary'] && !empty($conditions['is_supplementary'])){
        //$is_supplementary = $conditions['is_supplementary'];
        //unset($conditions['is_supplementary']);
        //}


        //dd($formId . '_conditions'); //Fresh_Student_Verificaiton_conditions
        Session::put($formId . '_conditions', $conditions);
        $master = [];

        $master = $custom_component_obj->getsuppchangerequestnotpayfees($formId, $exammomths);

        return view('supp_change_request.supp_change_request_total_applications', compact('exammomths', 'tableData', 'actions', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'midium', 'course', 'adm_types', 'stream_id', 'checkchangerequestsssupplementariesAllowOrNotAllow'))->withInput($request->all());
    }


    public function supp_change_request_student_completed(Request $request)
    {

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
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $checkchangerequestsssupplementariesAllowOrNotAllow = $this->_checkchangerequestssupplementariesAllowOrNotAllow();
        @$exammomths = $request->exam_month;
        $yes_no = $this->master_details('yesno');
        if (@$request->exam_month == 1) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[2]);
            @$supp_student_change_requests = array("1" => "Pending", "2" => "Approved");
            @$supp_student_change_requests = collect($supp_student_change_requests);
            if (@$request->supp_student_change_requests == 1) {
                unset($supp_student_change_requests[2]);
            } else {
                unset($supp_student_change_requests[1]);
            }
        } elseif (@$request->exam_month == 2) {
            $combo_name = 'exam_month';
            @$exam_months = $this->master_details($combo_name);
            unset($exam_months[1]);
            @$supp_student_change_requests = array("1" => "Pending", "2" => "Approved");
            $supp_student_change_requests = collect($supp_student_change_requests);
            if (@$request->supp_student_change_requests == 1) {
                unset($supp_student_change_requests[2]);
            } else {
                unset($supp_student_change_requests[1]);
            }
        }
        $title = "Supp Change Request student completed";

        $table_id = "supp_verifying_student_applications";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;
        $role_id = Session::get('role_id');
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
        $exportBtn = array();
        $examinationdepartment = Config::get("global.examination_department");
        if ($examinationdepartment == $role_id) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadchangerequertStudentExl',
                    'status' => false,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        }


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Code',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_months,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'supp_change_request_students',
            ),


        );


        $role_id = Session::get('role_id');

        $tableData = array();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        //$conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($role_id == config("global.super_admin_id")) {

        }

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            Session::put("ai_code", $auth_user_id);
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
            $conditions["students.aicenter_mapped_data"] = $aicenter_mapped_data;
        } else {
        }
        $actions = array();
        if (in_array("document_verification_of_student", $permissions)) {
            $actions[] = array(
                'fld' => 'verify_documents',
                'extraCondition' => 'student_applications',
                'icon' => '<i title="Verify the documents" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Verify</i>',
                'fld_url' => '../student/verify_documents/#id#'
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        // echo "ttt";
        if (@$inputs['extra'] && !empty($inputs['extra'])) {
            $conditions['extra'] = $inputs['extra'];
        }
        $is_supplementary = null;
        //if(@$conditions['is_supplementary'] && !empty($conditions['is_supplementary'])){
        //$is_supplementary = $conditions['is_supplementary'];
        //unset($conditions['is_supplementary']);
        //}


        //dd($formId . '_conditions'); //Fresh_Student_Verificaiton_conditions
        Session::put($formId . '_conditions', $conditions);
        $master = [];

        $master = $custom_component_obj->getsuppchangerequeststudnetompleted($formId, $exammomths);

        return view('supp_change_request.supp_change_request_total_applications', compact('exammomths', 'tableData', 'actions', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'midium', 'course', 'adm_types', 'stream_id', 'checkchangerequestsssupplementariesAllowOrNotAllow'))->withInput($request->all());
    }


    public function change_request_student_total_Generated(Request $request)
    {
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
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
        $combo_name = 'change_request_stream';
        $change_request_stream = $this->master_details($combo_name);
        $changerequeststreamapproved = DB::table('masters')->where('combo_name', '=', 'change_request_stream')->first();
        @$exammomths = $request->exam_month;
        $yes_no = $this->master_details('yesno');
        if (@$request->exam_month == 1) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[2]);
            @$student_change_request = array("1" => "Pending", "2" => "Approved");
            @$student_change_requests = collect($student_change_request);
            if (@$request->student_change_requests == 1) {
                unset($student_change_requests[2]);
            } else {
                unset($student_change_requests[1]);
            }
        } elseif (@$request->exam_month == 2) {
            $combo_name = 'exam_month';
            @$exam_months = $this->master_details($combo_name);
            unset($exam_months[1]);
            @$student_change_request = array("1" => "Pending", "2" => "Approved");
            $student_change_requests = collect($student_change_request);
            if (@$request->student_change_requests == 1) {
                unset($student_change_requests[2]);
            } else {
                unset($student_change_requests[1]);
            }
        }
        if (@$request->student_change_requests == 2) {
            $title = "Change Request Total Generated";
        } elseif (@$request->student_update_application == 1) {
            $title = "Change Request Student Application update";
        } elseif (@$request->locksumbitted == 1) {
            $title = "Change Request Student Lock Sumbitted";
        } else {
            $title = "Change Request Total Generated";
        }


        $table_id = "supp_verifying_student_applications";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;
        $role_id = Session::get('role_id');
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
        $exportBtn = array();
        $examinationdepartment = Config::get("global.examination_department");
        if ($examinationdepartment == $role_id) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadchangerequertStudentExl',
                    'status' => false,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        }


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Code',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_months,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'change_request_students',
            ),

            array(
                "lbl" => "Student Change Request",
                'fld' => 'student_change_requests',
                'input_type' => 'select',
                'options' => $student_change_requests,
                'placeholder' => 'Student Change Request',
                'dbtbl' => 'change_request_students',
            ),

            array(
                "lbl" => "Student Update Application",
                'fld' => 'student_update_application',
                'input_type' => 'select',
                'options' => $student_change_requests,
                'placeholder' => 'Student Change Request',
                'dbtbl' => 'change_request_students',
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Lock & Submit',
                'dbtbl' => 'applications',
            ),


        );


        $role_id = Session::get('role_id');

        $tableData = array();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["change_request_students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($role_id == config("global.super_admin_id")) {

        }

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            Session::put("ai_code", $auth_user_id);
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
            $conditions["students.aicenter_mapped_data"] = $aicenter_mapped_data;
        } else {
        }
        $actions = array();
        if (in_array("document_verification_of_student", $permissions)) {
            $actions[] = array(
                'fld' => 'verify_documents',
                'extraCondition' => 'student_applications',
                'icon' => '<i title="Verify the documents" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Verify</i>',
                'fld_url' => '../student/verify_documents/#id#'
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        // echo "ttt";
        if (@$inputs['extra'] && !empty($inputs['extra'])) {
            $conditions['extra'] = $inputs['extra'];
        }
        $is_supplementary = null;
        //if(@$conditions['is_supplementary'] && !empty($conditions['is_supplementary'])){
        //$is_supplementary = $conditions['is_supplementary'];
        //unset($conditions['is_supplementary']);
        //}


        //dd($formId . '_conditions'); //Fresh_Student_Verificaiton_conditions
        Session::put($formId . '_conditions', $conditions);
        $master = [];

        $master = $custom_component_obj->getchangerequestallStudentdataGenerated($formId);

        return view('change_request.change_request_total_generated', compact('exammomths', 'changerequeststreamapproved', 'change_request_stream', 'tableData', 'actions', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'midium', 'course', 'adm_types', 'stream_id', 'checkchangerequestsAllowOrNotAllow'))->withInput($request->all());
    }


    public function change_request_student_student_not_update_applications(Request $request)
    {

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
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
        $combo_name = 'change_request_stream';
        $change_request_stream = $this->master_details($combo_name);
        $changerequeststreamapproved = DB::table('masters')->where('combo_name', '=', 'change_request_stream')->first();
        @$exammomths = $request->exam_month;
        $yes_no = $this->master_details('yesno');
        if (@$request->exam_month == 1) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[2]);
            $student_change_request = array("1" => "Pending", "2" => "Approved");
            $student_change_requests = collect($student_change_request);
            if (@$request->student_change_requests == 1) {
                unset($student_change_requests[2]);
            } else {
                unset($student_change_requests[1]);
            }
        } elseif (@$request->exam_month == 2) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[1]);
            $student_change_request = array("1" => "Pending", "2" => "Approved");
            $student_change_requests = collect($student_change_request);
            if (@$request->student_change_requests == 1) {
                unset($student_change_requests[2]);
            } else {
                unset($student_change_requests[1]);
            }
        }

        $title = "Change Request Department Approved student not click Application update";


        $table_id = "supp_verifying_student_applications";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;
        $role_id = Session::get('role_id');
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
        $exportBtn = array();
        $examinationdepartment = Config::get("global.examination_department");
        if ($examinationdepartment == $role_id) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadchangerequertStudentExl',
                    'status' => false,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        }


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Code',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_months,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'change_request_students',
            ),


        );


        $role_id = Session::get('role_id');

        $tableData = array();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["change_request_students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($role_id == config("global.super_admin_id")) {

        }

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            Session::put("ai_code", $auth_user_id);
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
            $conditions["students.aicenter_mapped_data"] = $aicenter_mapped_data;
        } else {
        }
        $actions = array();
        if (in_array("document_verification_of_student", $permissions)) {
            $actions[] = array(
                'fld' => 'verify_documents',
                'extraCondition' => 'student_applications',
                'icon' => '<i title="Verify the documents" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Verify</i>',
                'fld_url' => '../student/verify_documents/#id#'
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        // echo "ttt";
        if (@$inputs['extra'] && !empty($inputs['extra'])) {
            $conditions['extra'] = $inputs['extra'];
        }
        $is_supplementary = null;
        //if(@$conditions['is_supplementary'] && !empty($conditions['is_supplementary'])){
        //$is_supplementary = $conditions['is_supplementary'];
        //unset($conditions['is_supplementary']);
        //}


        //dd($formId . '_conditions'); //Fresh_Student_Verificaiton_conditions
        Session::put($formId . '_conditions', $conditions);
        $master = [];

        $master = $custom_component_obj->getchangerequeststudnetnotclickupdateapplications($formId);

        return view('change_request.change_request_total_generated', compact('exammomths', 'changerequeststreamapproved', 'change_request_stream', 'tableData', 'actions', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'midium', 'course', 'adm_types', 'stream_id', 'checkchangerequestsAllowOrNotAllow'))->withInput($request->all());
    }


    public function change_request_student_not_locksumbitted(Request $request)
    {

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
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
        $combo_name = 'change_request_stream';
        $change_request_stream = $this->master_details($combo_name);
        $changerequeststreamapproved = DB::table('masters')->where('combo_name', '=', 'change_request_stream')->first();
        @$exammomths = $request->exam_month;
        @$locksumbitted = $request->locksumbitted;
        $yes_no = $this->master_details('yesno');
        if (@$request->exam_month == 1) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[2]);
            $student_change_request = array("1" => "Pending", "2" => "Approved");
            $student_change_requests = collect($student_change_request);
            if (@$request->student_change_requests == 1) {
                unset($student_change_requests[2]);
            } else {
                unset($student_change_requests[1]);
            }
        } elseif (@$request->exam_month == 2) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[1]);
            $student_change_request = array("1" => "Pending", "2" => "Approved");
            $student_change_requests = collect($student_change_request);
            if (@$request->student_change_requests == 1) {
                unset($student_change_requests[2]);
            } else {
                unset($student_change_requests[1]);
            }
        }
        if (@$request->locksumbitted == 1) {
            $title = "Change Request lock & submitted";
        } else {
            $title = "Change Request not lock & submitted";
        }


        $table_id = "supp_verifying_student_applications";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;
        $role_id = Session::get('role_id');
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
        $exportBtn = array();
        $examinationdepartment = Config::get("global.examination_department");
        if ($examinationdepartment == $role_id) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadchangerequertStudentExl',
                    'status' => false,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        }


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Code',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_months,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'change_request_students',
            ),

            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Lock & Submit',
                'dbtbl' => 'applications',
            ),


        );


        $role_id = Session::get('role_id');

        $tableData = array();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["change_request_students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($role_id == config("global.super_admin_id")) {

        }

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            Session::put("ai_code", $auth_user_id);
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
            $conditions["students.aicenter_mapped_data"] = $aicenter_mapped_data;
        } else {
        }
        $actions = array();
        if (in_array("document_verification_of_student", $permissions)) {
            $actions[] = array(
                'fld' => 'verify_documents',
                'extraCondition' => 'student_applications',
                'icon' => '<i title="Verify the documents" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Verify</i>',
                'fld_url' => '../student/verify_documents/#id#'
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        // echo "ttt";
        if (@$inputs['extra'] && !empty($inputs['extra'])) {
            $conditions['extra'] = $inputs['extra'];
        }
        $is_supplementary = null;
        //if(@$conditions['is_supplementary'] && !empty($conditions['is_supplementary'])){
        //$is_supplementary = $conditions['is_supplementary'];
        //unset($conditions['is_supplementary']);
        //}


        //dd($formId . '_conditions'); //Fresh_Student_Verificaiton_conditions
        Session::put($formId . '_conditions', $conditions);
        $master = [];

        $master = $custom_component_obj->getchangerequeststudnetnotlocksumbitted($formId, @$locksumbitted);

        return view('change_request.change_request_total_generated', compact('exammomths', 'changerequeststreamapproved', 'change_request_stream', 'tableData', 'actions', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'midium', 'course', 'adm_types', 'stream_id', 'checkchangerequestsAllowOrNotAllow'))->withInput($request->all());
    }


    public function change_request_student_completed(Request $request)
    {

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
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
        $combo_name = 'change_request_stream';
        $change_request_stream = $this->master_details($combo_name);
        $changerequeststreamapproved = DB::table('masters')->where('combo_name', '=', 'change_request_stream')->first();
        @$exammomths = $request->exam_month;
        $yes_no = $this->master_details('yesno');
        if (@$request->exam_month == 1) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[2]);
            $student_change_request = array("1" => "Pending", "2" => "Approved");
            $student_change_requests = collect($student_change_request);
            if (@$request->student_change_requests == 1) {
                unset($student_change_requests[2]);
            } else {
                unset($student_change_requests[1]);
            }
        } elseif (@$request->exam_month == 2) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[1]);
            $student_change_request = array("1" => "Pending", "2" => "Approved");
            $student_change_requests = collect($student_change_request);
            if (@$request->student_change_requests == 1) {
                unset($student_change_requests[2]);
            } else {
                unset($student_change_requests[1]);
            }
        }

        $title = "change_request_student_completed";


        $table_id = "supp_verifying_student_applications";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;
        $role_id = Session::get('role_id');
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
        $exportBtn = array();
        $examinationdepartment = Config::get("global.examination_department");
        if ($examinationdepartment == $role_id) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadchangerequertStudentExl',
                    'status' => false,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        }


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Code',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_months,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'change_request_students',
            ),


        );


        $role_id = Session::get('role_id');

        $tableData = array();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["change_request_students.exam_year"] = CustomHelper::_get_selected_sessions();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($role_id == config("global.super_admin_id")) {

        }

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            Session::put("ai_code", $auth_user_id);
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
            $conditions["students.aicenter_mapped_data"] = $aicenter_mapped_data;
        } else {
        }
        $actions = array();
        if (in_array("document_verification_of_student", $permissions)) {
            $actions[] = array(
                'fld' => 'verify_documents',
                'extraCondition' => 'student_applications',
                'icon' => '<i title="Verify the documents" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Verify</i>',
                'fld_url' => '../student/verify_documents/#id#'
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        // echo "ttt";
        if (@$inputs['extra'] && !empty($inputs['extra'])) {
            $conditions['extra'] = $inputs['extra'];
        }
        $is_supplementary = null;
        //if(@$conditions['is_supplementary'] && !empty($conditions['is_supplementary'])){
        //$is_supplementary = $conditions['is_supplementary'];
        //unset($conditions['is_supplementary']);
        //}


        //dd($formId . '_conditions'); //Fresh_Student_Verificaiton_conditions
        Session::put($formId . '_conditions', $conditions);
        $master = [];

        $master = $custom_component_obj->getchangerequeststudnetompleted($formId, $exammomths);

        return view('change_request.change_request_total_generated', compact('exammomths', 'changerequeststreamapproved', 'change_request_stream', 'tableData', 'actions', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'midium', 'course', 'adm_types', 'stream_id', 'checkchangerequestsAllowOrNotAllow'))->withInput($request->all());
    }


    public function change_request_student_not_fees_pay(Request $request)
    {

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
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
        $combo_name = 'change_request_stream';
        $change_request_stream = $this->master_details($combo_name);
        $changerequeststreamapproved = DB::table('masters')->where('combo_name', '=', 'change_request_stream')->first();
        @$exammomths = $request->exam_month;
        $yes_no = $this->master_details('yesno');
        if (@$request->exam_month == 1) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[2]);
            $student_change_request = array("1" => "Pending", "2" => "Approved");
            $student_change_requests = collect($student_change_request);
            if (@$request->student_change_requests == 1) {
                unset($student_change_requests[2]);
            } else {
                unset($student_change_requests[1]);
            }
        } elseif (@$request->exam_month == 2) {
            $combo_name = 'exam_month';
            $exam_months = $this->master_details($combo_name);
            unset($exam_months[1]);
            $student_change_request = array("1" => "Pending", "2" => "Approved");
            $student_change_requests = collect($student_change_request);
            if (@$request->student_change_requests == 1) {
                unset($student_change_requests[2]);
            } else {
                unset($student_change_requests[1]);
            }
        }

        $title = "change_request_student_completed";


        $table_id = "supp_verifying_student_applications";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;
        $role_id = Session::get('role_id');
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
        $exportBtn = array();
        $examinationdepartment = Config::get("global.examination_department");
        if ($examinationdepartment == $role_id) {
            $exportBtn = array(
                array(
                    "label" => "Export Excel",
                    'url' => 'downloadchangerequertStudentExl',
                    'status' => false,
                ),
                array(
                    "label" => "Export PDF",
                    'url' => 'downloadApplicationPdf',
                    'status' => false
                ),
            );
        }


        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSO",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Code',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_months,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'change_request_students',
            ),


        );


        $role_id = Session::get('role_id');

        $tableData = array();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["change_request_students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($role_id == config("global.super_admin_id")) {

        }

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $auth_user_id = $verifier_user_id = Auth::user()->id;
            Session::put("ai_code", $auth_user_id);
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
            $conditions["students.aicenter_mapped_data"] = $aicenter_mapped_data;
        } else {
        }
        $actions = array();
        if (in_array("document_verification_of_student", $permissions)) {
            $actions[] = array(
                'fld' => 'verify_documents',
                'extraCondition' => 'student_applications',
                'icon' => '<i title="Verify the documents" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Verify</i>',
                'fld_url' => '../student/verify_documents/#id#'
            );
        }


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        // echo "ttt";
        if (@$inputs['extra'] && !empty($inputs['extra'])) {
            $conditions['extra'] = $inputs['extra'];
        }
        $is_supplementary = null;
        //if(@$conditions['is_supplementary'] && !empty($conditions['is_supplementary'])){
        //$is_supplementary = $conditions['is_supplementary'];
        //unset($conditions['is_supplementary']);
        //}


        //dd($formId . '_conditions'); //Fresh_Student_Verificaiton_conditions
        Session::put($formId . '_conditions', $conditions);
        $master = [];

        $master = $custom_component_obj->getchangerequestlocksumbittedfeesnotpays($formId);

        return view('change_request.change_request_total_generated', compact('exammomths', 'changerequeststreamapproved', 'change_request_stream', 'tableData', 'actions', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'midium', 'course', 'adm_types', 'stream_id', 'checkchangerequestsAllowOrNotAllow'))->withInput($request->all());
    }


}
	
