<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\PracticalCustomComponent;
use App\Component\ThoeryCustomComponent;
use App\Exports\TheoryExaminerMappingexcelExlExport;
use App\Helper\CustomHelper;
use App\Models\AllotingCopiesExaminer;
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


class TheoryReportController extends Controller
{

    public $custom_component_obj = "";
    public $allotinCopiesexaminer = "";
    public $theory_custom_component_obj = " ";

    function __construct()
    {
        $this->middleware('permission:theory_report_examiner_mapping', ['only' => ['theory_report_examiner_mapping']]);
        $this->middleware('permission:theory_report_student_wise', ['only' => ['theory_report_student_wise']]);
        $this->middleware('permission:downloadtheoryreportexaminermappingexcel', ['only' => ['downloadtheoryreportexaminermappingexcel']]);
        $this->custom_component_obj = new CustomComponent;
        $this->theory_custom_component_obj = new ThoeryCustomComponent;
        $this->allotinCopiesexaminer = new AllotingCopiesExaminer;
    }

    public function theory_report_student_wise(Request $request)
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
        $combo_name = 'absent_nr';
        $absnr = $this->master_details($combo_name);
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();

        $yes_no = $this->master_details('yesno');

        $title = "Theory Students Report";
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

        $deo_district_id = $this->districtsByState(6);
        $user_theory_examiner_id = $this->theory_custom_component_obj->getTheoryExaminerList();
        $custom_component_obj = new CustomComponent;
        $examCenterList = collect($custom_component_obj->getExamCenterWithBothCourseCode());
        $subject_list = $this->subjectList();
        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                'lbl' => 'Theory Marks',
                'fld' => 'final_theory_marks',
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
                'lbl' => 'Theory Absent/NR',
                'fld' => 'theory_absent',
                'input_type' => 'select',
                'options' => $absnr,
                'placeholder' => 'Theory Absent',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                'lbl' => 'Exclude For Theroy ',
                'fld' => 'is_exclude_for_theory',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Exclude For Theroy',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                'lbl' => 'Theory Marks Update',
                'fld' => 'is_update_theory_marks_admin',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Theroy Marks Update',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                "lbl" => "Exam Center Fixcode",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examiner_list,
                'placeholder' => 'Exam Center Fixcode',
                'dbtbl' => 'marking_absent_students',
                'status' => true
            ),

            array(
                'lbl' => 'Examiner Theroy',
                'fld' => 'theory_examiner_id',
                'input_type' => 'select',
                'options' => $user_theory_examiner_id,
                'placeholder' => 'Examiner Theroy',
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
                "lbl" => "Student Fixcode",
                'fld' => 'fixcode',
                'input_type' => 'text',
                'placeholder' => "Student Fixcode",
                'dbtbl' => 'student_allotment_marks',
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
                'fld' => 'theory_max_marks',
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
                "lbl" => "Theory Marks",
                'fld' => 'final_theory_marks',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Student Fixcode",
                'fld' => 'fixcode',
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
                'lbl' => 'Theory Absent/NR',
                'fld' => 'theory_absent',
                'input_type' => 'select',
                'options' => $absnr
            ),

            array(
                "lbl" => "Exclude For Theroy ",
                'fld' => 'is_exclude_for_theory',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            array(
                "lbl" => "Theory Marks Update",
                'fld' => 'is_update_theory_marks_admin',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            array(
                'lbl' => 'Exam Center',
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examCenterList
            ),
            array(
                "lbl" => "Examiner Theory",
                'fld' => 'theory_examiner_id',
                'input_type' => 'select',
                'options' => $user_theory_examiner_id
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
        // $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {

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
                if ($_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP) {
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
                // 	'icon' => '<i class="material-icons" title="Click here to Delete." >delete</i>',
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
                if ($_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP || $_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP3) {
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
            $actions = array();
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
        Session::put($formId . '_conditions', $conditions);

        $master = $this->theory_custom_component_obj->getTheoryStudentsData($formId);


        return view('theory_reports.theory_reports_student_wise', compact('actions', 'master', 'tableData', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'subject_list', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'course', 'stream_id', 'adm_types'));
    }

    public function theory_report_examiner_mapping(Request $request)
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
        $combo_name = 'course';
        $course_dropdown = $this->master_details($combo_name);
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
        $subjects_dropdown = $this->subjectList();
        $subject_list_dropdown = Subject::where('id', '<', '0')->pluck('name', 'id');
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $yes_no = $this->master_details('yesno');
        $title = "Examiner Mapping Theory Report";
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
                'url' => 'downloadtheoryreportexaminermappingexcel',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => false,
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

        if (@$request->course_id) {
            $subject_list_dropdown = $this->subjectList(@$request->course_id);
        }

        $filters = array(
            array(
                "lbl" => "Exam Center Fixcode",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examiner_list,
                'placeholder' => 'Exam Center Fixcode',
                'dbtbl' => 'marking_absent_students',
                'status' => true
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course_id',
                'input_type' => 'select',
                'options' => $course_dropdown,
                'placeholder' => 'Course',
                'dbtbl' => 'marking_absent_students',
                'status' => true
            ),

            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list_dropdown,
                'placeholder' => 'Subject',
                'dbtbl' => 'marking_absent_students',
                'status' => true
            ),
            array(
                "lbl" => "Examiner sso",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => 'SSO',
                'dbtbl' => 'users',
                'status' => true
            ),
            array(
                "lbl" => "Examiner Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => 'Name',
                'dbtbl' => 'users',
                'status' => true
            ),

            array(
                "lbl" => "mobile",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => 'Mobile',
                'dbtbl' => 'users',
                'status' => true
            ),

            array(
                "lbl" => "Total Students Appearing",
                'fld' => 'total_students_appearing',
                'input_type' => 'text',
                'placeholder' => 'Total Students Appearing',
                'dbtbl' => 'marking_absent_students',
                'status' => true
            ),

            array(
                "lbl" => "Total Copies of the subject",
                'fld' => 'total_copies_of_subject',
                'input_type' => 'text',
                'placeholder' => 'Total Copies of the subject',
                'dbtbl' => 'marking_absent_students',
                'status' => true
            ),
            array(
                "lbl" => "Total Absent",
                'fld' => 'total_absent',
                'input_type' => 'text',
                'placeholder' => 'Total Absent',
                'dbtbl' => 'marking_absent_students',
                'status' => true
            ),

            array(
                "lbl" => "Total Copies of the subject",
                'fld' => 'total_nr',
                'input_type' => 'text',
                'placeholder' => 'Total NR',
                'dbtbl' => 'marking_absent_students',
                'status' => true
            ),

            array(
                "lbl" => "Date Of Allotment",
                'fld' => 'allotment_date',
                'input_type' => 'text',
                'placeholder' => 'Date Of Allotment',
                'dbtbl' => 'alloting_copies_examiners',
                'status' => true
            ),
            array(
                'lbl' => 'locksumbitted',
                'fld' => 'marks_entry_completed',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'locksumbitted',
                'dbtbl' => 'alloting_copies_examiners',
            ),

        );

        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Exam Center Fixcode",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examiner_list,
                'search_type' => "text",
                'placeholder' => 'Course Type',
                'dbtbl' => 'marking_absent_students',
            ),

            array(
                "lbl" => "Course",
                'fld' => 'course_id',
                'input_type' => 'select',
                'options' => $course_dropdown,
                'search_type' => "text",
                'placeholder' => 'Course Type',
                'dbtbl' => 'marking_absent_students',
            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_name',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => 'marking_absent_students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Examiner Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Mobile",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Total Students Appearing",
                'fld' => 'total_students_appearing',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => 'marking_absent_students',
            ),
            array(
                "lbl" => "Total Copies of the subject",
                'fld' => 'total_copies_of_subject',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => 'marking_absent_students',
            ),

            array(
                "lbl" => "Total Absent",
                'fld' => 'total_absent',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Total NR",
                'fld' => 'total_nr',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Date Of Allotment",
                'fld' => 'allotment_date',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => '',
            ),
            array(
                'lbl' => 'Is Lock & Submitted',
                'fld' => 'marks_entry_completed',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'locksumbitted',
                'dbtbl' => 'alloting_copies_examiners',
            ),
        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();

        $conditions = array();

        if (in_array("application_dashboard", $permissions)) {
            $actions = array();

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
                if ($_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP) {
                    $deleteVal = true;
                }
            }
            $deleteVal = true;
            if ($deleteVal == true) {
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
                if ($_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP || $_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP3) {
                    $unlockVal = true;
                }
            }
            if ($unlockVal == true) {
            }
        } else {
            $actions = array();
        }

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }


        if ($request->all()) {
            $inputs = $request->all();

            // dd($inputs);
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                            $conditions[$iv['dbtbl'] . "." . $k] = $v;
                        }
                    }
                }
            }

        }

        Session::put($formId . '_conditions', $conditions);


        $master = $this->theory_custom_component_obj->getAllotingExaminerListreports($formId, true);
        return view('theory_reports.theory_reports_student_wise', compact('actions', 'master', 'tableData', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'course', 'stream_id', 'adm_types'));
    }


    public function downloadtheoryreportexaminermappingexcel(Request $request, $type = "xlsx")
    {

        $PracticalExaminer = new TheoryExaminerMappingexcelExlExport;
        $filename = 'TheoryExaminerMappingExcel' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($PracticalExaminer, $filename);
    }
}