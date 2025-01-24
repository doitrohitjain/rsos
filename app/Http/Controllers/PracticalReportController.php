<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\PracticalCustomComponent;
use App\Exports\PracticalExaminerMappingexcelExlExport;
use App\Exports\PracticalSlotexcelExlExport;
use App\Helper\CustomHelper;
use App\models\Subject;
use Auth;
use Config;
use DB;
use File;
use FontLib\Font;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Response;
use Session;
use Validator;

// use Yajra\DataTables\DataTables;


class PracticalReportController extends Controller
{
    function __construct()
    {
        //$this->middleware('permission:practical_report_student_wise', ['only' => ['practical_report_student_wise']]);
        //$this->middleware('permission:practical_report_examiner_mapping', ['only' => ['practical_report_examiner_mapping']]);
        //$this->middleware('permission:downloadpracticalreportexaminermappingexcel', ['only' => ['downloadpracticalreportexaminermappingexcel']]);
        $this->middleware('permission:practical_slot_wise_report', ['only' => ['practical_Slot_Report']]);
    }

    public function practical_report_student_wise(Request $request)
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
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);

        $yes_no = $this->master_details('yesno');
        $title = "Practical Students Report";
        $table_id = "Admission_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $user_deo_id = $deo_district_id = $user_practical_examiner_id = array();
        $custom_component_obj = new CustomComponent;
        $practicalCustomComponent = new practicalCustomComponent();

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
            // array(
            // 	"label" => "Export Excel",
            // 	'url' => 'downloadApplicationExl',
            // 	'status' => true,
            // ),
            // array(
            // 	"label" => "Export PDF",
            // 	'url' => 'downloadApplicationPdf',
            // 	'status' => true
            // ),
        );
        $deo_district_id = $district_list = $this->districtsByState();
        $user_deo_id = $practicalCustomComponent->getDEOList();
        $deo_district_id = $this->districtsByState(6);
        $user_practical_examiner_id = $practicalCustomComponent->getPracticalExaminerList();

        $custom_component_obj = new CustomComponent;
        $examCenterList = collect($custom_component_obj->getExamCenterWithBothCourseCode());

        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                'lbl' => 'Practical Marks',
                'fld' => 'final_practical_marks',
                'fld_url' => '',
                'placeholder' => 'Admission Type',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                'lbl' => 'Subject Code',
                'fld' => 'subject_code',
                'fld_url' => '',
                'placeholder' => 'Subject Codee',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                'lbl' => 'Subject Name',
                'fld' => 'subject_name',
                'fld_url' => '',
                'placeholder' => 'Subject Name',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                'lbl' => 'Practical Absent',
                'fld' => 'practical_absent',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Practical Absent',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                'lbl' => 'Exclude For Practical ',
                'fld' => 'is_exclude_for_practical',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Exclude For Practical',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                'lbl' => 'Practical Marks Update',
                'fld' => 'is_update_practical_marks_admin',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Practical Marks Update',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                'lbl' => 'DEO',
                'fld' => 'user_deo_id',
                'input_type' => 'select',
                'options' => $user_deo_id,
                'placeholder' => 'DEO',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                'lbl' => 'Exam Center',
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examCenterList,
                'placeholder' => 'Exam Center',
                'dbtbl' => 'student_allotment_marks',
            ),

            array(
                'lbl' => 'DEO District',
                'fld' => 'deo_district_id',
                'input_type' => 'select',
                'options' => $deo_district_id,
                'placeholder' => 'DEO District',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                'lbl' => 'Examiner Practical',
                'fld' => 'user_practical_examiner_id',
                'input_type' => 'select',
                'options' => $user_practical_examiner_id,
                'placeholder' => 'Examiner Practical',
                'dbtbl' => 'student_allotment_marks',
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
                'dbtbl' => 'students',
            ),
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
        );

        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                'lbl' => 'Max Marks',
                'fld' => 'practical_max_marks',
                'input_type' => 'text',
                'placeholder' => 'Max Marks',
                'dbtbl' => 'student_allotment_marks',
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
                "lbl" => "Practical Marks",
                'fld' => 'final_practical_marks',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Subject Code",
                'fld' => 'subject_code',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Subject Name",
                'fld' => 'subject_name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Practical Absent",
                'fld' => 'practical_absent',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            array(
                "lbl" => "Exclude For Practical ",
                'fld' => 'is_exclude_for_practical',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            array(
                "lbl" => "Practical Marks Update",
                'fld' => 'is_update_practical_marks_admin',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            array(
                "lbl" => "DEO",
                'fld' => 'user_deo_id',
                'input_type' => 'select',
                'options' => $user_deo_id
            ),
            array(
                'lbl' => 'Exam Center',
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examCenterList
            ),
            array(
                "lbl" => "DEO District",
                'fld' => 'deo_district_id',
                'input_type' => 'select',
                'options' => $deo_district_id
            ),
            array(
                "lbl" => "Examiner Practical",
                'fld' => 'user_practical_examiner_id',
                'input_type' => 'select',
                'options' => $user_practical_examiner_id
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

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();
        $conditions["students.exam_month"] = Config::get("global.current_exam_month_id");

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $conditions["students.user_id"] = @Auth::user()->id;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'students',
            );

            $filters[] = array(
                "lbl" => "Student id",
                'fld' => 'id',
                'input_type' => 'text',
                'placeholder' => "Student id",
                'dbtbl' => 'students',
            );
            // $tableData[] = array(
            // 	"lbl" => "Ai Center",
            // 	'fld' => 'ai_code',
            // 	'input_type' => 'select',
            // 	'options' => $aiCenters,
            // 	'placeholder' => 'Ai Center',
            // 	'dbtbl' => 'users'
            // );
        }

        if (in_array("application_dashboard", $permissions)) {
            $actions = array(
                // array(
                // 	'fld' => 'edit',
                // 	'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                // 	'fld_url' => 'student/persoanl_details/#id#'
                // ),
                // array(
                // 	'fld' => 'view',
                // 	'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                // 	'fld_url' => 'student/preview_details/#id#'
                // ),
            );

            $deleteVal = false;
            $masterIP = '10.68.181.236';
            if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == $masterIP) {
                $masterIP2 = '10.68.181.213';
                $masterIP3 = '10.68.181.229';
                $masterIP4 = '10.68.181.249';
                $masterIP5 = '10.68.181.51';
                if ($_SERVER['REMOTE_ADDR'] == $masterIP || $_SERVER['REMOTE_ADDR'] == $masterIP3 || $_SERVER['REMOTE_ADDR'] == $masterIP2) {
                    $deleteVal = true;
                }
            } else if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'rsosadmission.rajasthan.gov.in' || $_SERVER['HTTP_HOST'] == 'www.rsosadmission.rajasthan.gov.in')) {
                if (@$_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP) {
                    $deleteVal = true;
                }
            }
            $deleteVal = true;
            if ($deleteVal == true) {
                // $actions[] = array(
                // 	'fld' => 'studentdelete', //For active studentdeleteactive
                // 	'class' => 'delete-confirm',
                // 	'icon' => '<i class="material-icons" title="Click here to Delete.">delete</i>',
                // 	'fld_url' => 'studentdelete/#id#' //For active studentdeleteactive
                // );

                // $actions[] = array(
                // 	'fld' => 'studentrejectdelete', //For active studentdeleteactive
                // 	'class' => 'delete-confirm2',
                // 	'icon' => '<i class="material-icons" title="Click here to Delete.">delete</i>',
                // 	'fld_url' => 'student/studentrejectdelete/#id#' //For active studentdeleteactive
                // );
            }

            $unlockVal = true;
            $masterIP = '10.68.181.236';
            $masterIP2 = '10.68.181.213';
            $masterIP3 = '10.68.181.229';
            $masterIP5 = '10.68.181.51';
            if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == $masterIP) {

                if ($_SERVER['REMOTE_ADDR'] == $masterIP || $_SERVER['REMOTE_ADDR'] == $masterIP2 || $_SERVER['REMOTE_ADDR'] == $masterIP3) {
                    $unlockVal = true;
                }
            } else if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'rsosadmission.rajasthan.gov.in' || $_SERVER['HTTP_HOST'] == 'www.rsosadmission.rajasthan.gov.in')) {
                if (@$_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP || @$_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP3) {
                    $unlockVal = true;
                }
            }
            if ($unlockVal == true) {
                // $actions[] = array(
                // 	'fld' => 'studentunlock', //For active studentdeleteactive
                // 	'class' => 'unlock-student',
                // 	'icon' => '<i class="material-icons md-18" title="Click here to Unlock.">lock</i>',
                // 	'fld_url' => 'studentunlock/#id#' //For active studentdeleteactive
                // );
            }
        } else {
            // $actions = array(
            // 	array(
            // 		'fld' => 'view',
            // 		'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
            // 		'fld_url' => '../student/preview_details/#id#'
            // 	),
            // );
        }

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
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


        $tempPracticalSubjects = $this->getSubjectIdByPracticalTheory(1);
        Session::put('tempPracticalSubjects', $tempPracticalSubjects);
        Session::put($formId . '_conditions', $conditions);

        $master = $practicalCustomComponent->getPracticalStudentsData($formId);


        return view('admission_reports.student_payment_details', compact('actions', 'master', 'tableData', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'course', 'stream_id', 'adm_types'));
    }

    public function practical_report_examiner_mapping(Request $request)
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
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);

        $yes_no = $this->master_details('yesno');
        $title = "Subject Practical Examiner Status Report";
        $table_id = "Admission_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $user_deo_id = $deo_district_id = $user_practical_examiner_id = array();
        $custom_component_obj = new CustomComponent;
        $practicalCustomComponent = new practicalCustomComponent();

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
                'url' => 'downloadpracticalreportexaminermappingexcel',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => false
            ),
        );
        $deo_district_id = $district_list = $this->districtsByState();
        $user_deo_id = $practicalCustomComponent->getDEOList();
        $deo_district_id = $this->districtsByState(6);
        $user_practical_examiner_id = $practicalCustomComponent->getPracticalExaminerList();
        $subject_list = $this->subjectList();
        $yes_no = $this->master_details('yesno');

        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get("global.current_exam_month_id");

        $custom_component_obj = new CustomComponent;
        $examCenterList = $examcenter_datails_dropdown = collect($custom_component_obj->getExamCenterWithBothCourseCode());

        // $examCenter10CodeList = $custom_component_obj->examcentercodeobj($exam_year,$exam_month,'ecenter10');
        // $examCenter12CodeList = $custom_component_obj->examcentercodeobj($exam_year,$exam_month,'ecenter12');


        $filters = array(
            array(
                'lbl' => 'Subject Name',
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'placeholder' => 'Subject Name',
                'dbtbl' => 'user_examiner_maps',
            ),
            array(
                'lbl' => 'Is Lock & Submit',
                'fld' => 'is_lock_submit',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is Lock & Submit',
                'dbtbl' => 'user_examiner_maps',
            ),
            array(
                'lbl' => 'DEO - District',
                'fld' => 'user_deo_id',
                'input_type' => 'select',
                'options' => $user_deo_id,
                'placeholder' => 'DEO - District',
                'dbtbl' => 'user_examiner_maps',
            ),
            array(
                'lbl' => 'Examiner Practical',
                'fld' => 'user_practical_examiner_id',
                'input_type' => 'select',
                'options' => $user_practical_examiner_id,
                'placeholder' => 'Examiner Practical',
                'dbtbl' => 'user_examiner_maps',
            ),
            array(
                "lbl" => "Exam Center",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examCenterList,
                'placeholder' => 'Exam Center',
                'dbtbl' => 'user_examiner_maps'
            ),
            array(
                'lbl' => 'Is Signed PDF',
                'fld' => 'is_signed_pdf',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is Signed PDF',
                'dbtbl' => 'user_examiner_maps',
            )
            // array(
            // 	"lbl" => "Center Code 10th",
            // 	'fld' => 'examcenter_detail_id',
            // 	'input_type' => 'select',
            // 	'options' => $examCenter10CodeList,
            // 	'placeholder' => 'Center Code 10th',
            // 	'dbtbl' => 'user_examiner_maps',
            // ),
            // array(
            // 	"lbl" => "Center Code 12th",
            // 	'fld' => 'examcenter_detail_id',
            // 	'input_type' => 'select',
            // 	'options' => $examCenter12CodeList,
            // 	'placeholder' => 'Center Code 12th',
            // 	'dbtbl' => 'user_examiner_maps',
            // ),


        );
        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            // array(
            // 	'lbl' => 'Subject Name',
            // 	'fld' => 'subject_id',
            // 	'input_type' => 'select',
            // 	'options' => $subject_list,
            // 	'placeholder' => 'Subject Name',
            // 	'dbtbl' => 'user_examiner_maps',
            // ),
            array(
                "lbl" => "Exam Center",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examCenterList
            ),
            array(
                "lbl" => "Course ",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                'lbl' => 'Subject Name',
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'placeholder' => 'Subject Name',
                'dbtbl' => 'user_examiner_maps',
            ),
            array(
                "lbl" => "Examiner SSO",
                'fld' => 'user_practical_examiner_id',
                'input_type' => 'select',
                'options' => $user_practical_examiner_id
            ),

            array(
                'lbl' => 'DEO - District',
                'fld' => 'user_deo_id',
                'input_type' => 'select',
                'options' => $user_deo_id,
                'placeholder' => 'DEO - District',
                'dbtbl' => 'user_examiner_maps',
            ),


            // array(
            // 	"lbl" => "Center Code 10th",
            // 	'fld' => 'examcenter_detail_id',
            // 	'input_type' => 'select',
            // 	'options' => $examCenter10CodeList
            // ),
            // array(
            // 	"lbl" => "Center Code 12th",
            // 	'fld' => 'examcenter_detail_id',
            // 	'input_type' => 'select',
            // 	'options' => $examCenter12CodeList
            // ),

            array(
                'lbl' => 'Is Lock & Submit',
                'fld' => 'is_lock_submit',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is Lock & Submit',
                'dbtbl' => 'user_examiner_maps',
            ),
            array(
                'lbl' => 'Is Signed PDF',
                'fld' => 'is_signed_pdf',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is Signed PDF',
                'dbtbl' => 'user_examiner_maps',
            )
        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["user_examiner_maps.exam_year"] = CustomHelper::_get_selected_sessions();
        $conditions["user_examiner_maps.exam_month"] = Config::get("global.current_exam_month_id");


        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $conditions["students.user_id"] = @Auth::user()->id;
        } else {

            // $tableData[] = array(
            // 	"lbl" => "Ai Center",
            // 	'fld' => 'ai_code',
            // 	'input_type' => 'select',
            // 	'options' => $aiCenters,
            // 	'placeholder' => 'Ai Center',
            // 	'dbtbl' => 'users'
            // );
        }

        if (in_array("application_dashboard", $permissions)) {
            $actions = array(
                // array(
                // 	'fld' => 'edit',
                // 	'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                // 	'fld_url' => 'student/persoanl_details/#id#'
                // ),
                // array(
                // 	'fld' => 'view',
                // 	'icon' => '<i class="material-icons" title="Click here to Delete.">remove_red_eye</i>',
                // 	'fld_url' => 'student/preview_details/#id#'
                // ),

            );

            $deleteVal = false;
            $masterIP = '10.68.181.236';
            if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == $masterIP) {
                $masterIP2 = '10.68.181.213';
                $masterIP3 = '10.68.181.229';
                $masterIP4 = '10.68.181.249';
                $masterIP5 = '10.68.181.51';
                if ($_SERVER['REMOTE_ADDR'] == $masterIP || $_SERVER['REMOTE_ADDR'] == $masterIP3 || $_SERVER['REMOTE_ADDR'] == $masterIP2) {
                    $deleteVal = true;
                }
            } else if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'rsosadmission.rajasthan.gov.in' || $_SERVER['HTTP_HOST'] == 'www.rsosadmission.rajasthan.gov.in')) {
                if (@$_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP) {
                    $deleteVal = true;
                }
            }
            $deleteVal = true;
            if ($deleteVal == true) {
                // $actions[] = array(
                // 	'fld' => 'studentdelete', //For active studentdeleteactive
                // 	'class' => 'delete-confirm',
                // 	'icon' => '<i class="material-icons">delete</i>',
                // 	'fld_url' => 'studentdelete/#id#' //For active studentdeleteactive
                // );

                // $actions[] = array(
                // 	'fld' => 'studentrejectdelete', //For active studentdeleteactive
                // 	'class' => 'delete-confirm2',
                // 	'icon' => '<i class="material-icons" title="Click here to Delete.">delete</i>',
                // 	'fld_url' => 'student/studentrejectdelete/#id#' //For active studentdeleteactive
                // );
            }

            $unlockVal = true;
            $masterIP = '10.68.181.236';
            $masterIP2 = '10.68.181.213';
            $masterIP3 = '10.68.181.229';
            $masterIP5 = '10.68.181.51';
            if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == $masterIP) {

                if ($_SERVER['REMOTE_ADDR'] == $masterIP || $_SERVER['REMOTE_ADDR'] == $masterIP2 || $_SERVER['REMOTE_ADDR'] == $masterIP3) {
                    $unlockVal = true;
                }
            } else if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'rsosadmission.rajasthan.gov.in' || $_SERVER['HTTP_HOST'] == 'www.rsosadmission.rajasthan.gov.in')) {
                if (@$_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP || @$_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP3) {
                    $unlockVal = true;
                }
            }
            if ($unlockVal == true) {
                // $actions[] = array(
                // 	'fld' => 'studentunlock', //For active studentdeleteactive
                // 	'class' => 'unlock-student',
                // 	'icon' => '<i class="material-icons md-18" title="Click here to Active.">lock</i>',
                // 	'fld_url' => 'studentunlock/#id#' //For active studentdeleteactive
                // );
            }
        } else {
            // $actions = array(
            // 	array(
            // 		'fld' => 'view',
            // 		'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
            // 		'fld_url' => '../student/preview_details/#id#'
            // 	),
            // );
        }

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            // foreach($filters as $ik => $iv){
            // 	if( !empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']]) ){
            // 		$conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
            // 	}
            // }

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

        $master = $practicalCustomComponent->getPracticalExaminerMapping($formId);


        return view('admission_reports.student_payment_details', compact('actions', 'master', 'tableData', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'course', 'stream_id', 'adm_types'));
    }

    public function downloadpracticalreportexaminermappingexcel(Request $request, $type = "xlsx")
    {
        $PracticalExaminer = new PracticalExaminerMappingexcelExlExport;
        $filename = 'PracticalExaminerMappingExcel' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($PracticalExaminer, $filename);
    }

    public function downloadpracticalreportslotexcel(Request $request, $type = "xlsx")
    {
        $PracticalExaminer = new PracticalSlotexcelExlExport;
        $filename = 'PracticalSoltExcel' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($PracticalExaminer, $filename);
    }


    public function practical_Slot_Report(Request $request)
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
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);

        $yes_no = ['1' => 'Yes', '0' => 'No'];
        $title = "Practical Slot Report";
        $table_id = "Admission_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $user_deo_id = $deo_district_id = $user_practical_examiner_id = array();
        $custom_component_obj = new CustomComponent;
        $practicalCustomComponent = new practicalCustomComponent();

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
                'url' => 'downloadpracticalreportslotexcel',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => false
            ),
        );
        $deo_district_id = $district_list = $this->districtsByState();
        $user_deo_id = $practicalCustomComponent->getDEOList();
        $deo_district_id = $this->districtsByState(6);
        $user_practical_examiner_id = $practicalCustomComponent->getPracticalExaminerList();
        $subject_list = Subject::where('practical_type', 1)->pluck('name', 'id');
        $yes_no = $this->master_details('yesno');

        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get("global.current_exam_month_id");

        $custom_component_obj = new CustomComponent;
        $examCenterList = $examcenter_datails_dropdown = collect($custom_component_obj->getExamCenterWithBothCourseCode());

        $filters = array(
            array(
                "lbl" => "Start date",
                'fld' => 'start_date',
                'input_type' => 'datetime-local',
                'placeholder' => "Start Date",
                'dbtbl' => 'student_practical_slots',
            ),
            array(
                "lbl" => "End date",
                'fld' => 'end_date',
                'input_type' => 'datetime-local',
                'placeholder' => "End Date",
                'dbtbl' => 'student_practical_slots',
            ),
            array(
                'lbl' => 'Subject Name',
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'placeholder' => 'Subject Name',
                'dbtbl' => 'user_examiner_maps',
            ),
            array(
                'lbl' => 'Is Lock & Submit',
                'fld' => 'entry_done',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is Lock & Submit',
                'dbtbl' => '',
            ),
            array(
                'lbl' => 'Examiner Practical',
                'fld' => 'user_practical_examiner_id',
                'input_type' => 'select',
                'options' => $user_practical_examiner_id,
                'placeholder' => 'Examiner Practical',
                'dbtbl' => 'user_examiner_maps',
            ),
            array(
                "lbl" => "Exam Center",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examCenterList,
                'placeholder' => 'Exam Center',
                'dbtbl' => 'user_examiner_maps'
            ),
            array(
                "lbl" => "Skip Slot",
                'fld' => 'skip_slot',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Skip Slot',
                'dbtbl' => ''
            ),


        );

        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Exam Center",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examCenterList
            ),
            array(
                "lbl" => "Course ",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                'lbl' => 'Subject Name',
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'placeholder' => 'Subject Name',
                'dbtbl' => 'user_examiner_maps',
            ),
            array(
                "lbl" => "Examiner SSO",
                'fld' => 'user_practical_examiner_id',
                'input_type' => 'select',
                'options' => $user_practical_examiner_id
            ),

            array(
                'lbl' => 'Batch Student Count',
                'fld' => 'batch_student_count',
                'input_type' => 'text',
                'placeholder' => 'DEO - District',
                'dbtbl' => '',
            ),
            array(
                'lbl' => 'Start Date Time',
                'fld' => 'date_time_start',
                'input_type' => 'date',
                'placeholder' => '',
                'dbtbl' => '',
            ),
            array(
                'lbl' => 'End Date Time',
                'fld' => 'date_time_end',
                'input_type' => 'date',
                'placeholder' => '',
                'dbtbl' => '',
            ),
            array(
                'lbl' => 'Slot Lock & Submit',
                'fld' => 'entry_done',
                'input_type' => 'select',
                'options' => $yes_no,
            ),
            array(
                "lbl" => "Skip Slot",
                'fld' => 'skip_slot',
                'input_type' => 'select',
                'options' => $yes_no,

            ),


        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["user_examiner_maps.exam_year"] = CustomHelper::_get_selected_sessions();
        $conditions["user_examiner_maps.exam_month"] = Config::get("global.current_exam_month_id");
        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            //$conditions["students.user_id"] = @Auth::user()->id;
        } else {


        }


        $actions = array();
        if (in_array("delete-slot", $permissions)) {
            $actions = array(
                // array(
                // 	'fld' => 'edit',
                // 	'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                // 	'fld_url' => 'student/persoanl_details/#id#'
                // ),
                // array(
                // 	'fld' => 'view',
                // 	'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                // 	'fld_url' => 'student/preview_details/#id#'
                // ),
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

        $master = $practicalCustomComponent->getPraticalSlotData($formId);

        return view('practical_reports.practical_report_student_wise', compact('actions', 'master', 'tableData', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'course', 'stream_id', 'adm_types', 'subject_list', 'examCenterList', 'user_practical_examiner_id'));
    }

}