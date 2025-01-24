<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Exports\AicentersubjectCountAllotmentstudentsuppExlExport;
use App\Exports\AicentersubjectCountExlExport;
use App\Exports\AicentersubjectCountsanssuppExlExport;
use App\Exports\AicentersubjectCountStudentExlExport;
use App\Exports\BoardNrStudentEnrollmentExcel;
use App\Exports\CenterCountExlExport;
use App\Exports\Examcenterwiseexcel;
use App\Exports\SessionalExlExport;
use App\Exports\Studentfeeorgsummaryexcel;
use App\Exports\StudentFeesExlExport;
use App\Exports\Studentfeesummaryexcel;
use App\Exports\supplementaryexamcenterwiseexcel;
use App\Helper\CustomHelper;
use App\models\AicenterDetail;
use App\models\District;
use App\models\ExamcenterDetail;
use App\models\Student;
use App\models\StudentAllotment;
use App\models\StudentAllotmentHardDelete;
use App\models\StudentAllotmentMark;
use App\models\Subject;
use App\models\Supplementary;
use Auth;
use Config;
use DB;
use File;
use FontLib\Font;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Redirect;
use Response;
use Session;
use Validator;

// use Yajra\DataTables\DataTables;

class ExaminationReportController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:examination_report_student_fees', ['only' => ['student_fees']]);
        $this->middleware('permission:examination_report_studentfeesexl', ['only' => ['downloadStudentFeesExl']]);
        $this->middleware('permission:examination_report_studentfeespdf', ['only' => ['downloadStudentFeesPdf']]);
        $this->middleware('permission:examination_report_sessional_report', ['only' => ['sessional_report']]);
        $this->middleware('permission:examination_report_sessional_report_h', ['only' => ['sessional_report_h']]);
    }

    public function examcenter_material(Request $request)
    {
        $title = "Exam Center Material";
        $title = ucfirst(str_replace("_", " ", $title));
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
        $course_10 = Crypt::encrypt('10');
        $course_12 = Crypt::encrypt('12');
        $linksBtn = array(
            array(
                "label" => "Download Zip",
                "title_label" => "Course 10th Exam Centers Material Zip",
                "items" => " 1. Exam Center Studnet Attendances PDF,
					2. Exam Subject Practical Student Rollwise SIGNATURE PDF
					3. Exam Subject Theory Student Rollwise SIGNATURE PDF
					4. Exam Subject Practical Student Rollwise Enrollment PDF
					5. Exam Subject Theory Student Rollwise Enrollment PDF
					6. Exam Center Nominal Roll PDF",
                'course' => $course_10,
                'status' => true,
            ),
            array(
                "label" => "Download Zip",
                "title_label" => "Course 12th Exam Centers Material Zip",
                "items" => " 1. Exam Center Studnet Attendances PDF,
					2. Exam Subject Practical Student Rollwise SIGNATURE PDF
					3. Exam Subject Theory Student Rollwise SIGNATURE PDF
					4. Exam Subject Practical Student Rollwise Enrollment PDF
					5. Exam Subject Theory Student Rollwise Enrollment PDF
					6. Exam Center Nominal Roll PDF",
                'course' => $course_12,
                'status' => true,
            )
        );
        // dd($linksBtn);
        return view('examination_reports.examcenter_material', compact('breadcrumbs', 'title', 'linksBtn', 'course_10', 'course_12'));
    }

    public function examcenter_material_zip_download(Request $request, $course = null)
    {

        $ecourse = $course;
        $course = Crypt::decrypt($ecourse);
        $current_admission_year_string = Config::get('global.current_admission_year_string');
        $exam_month = Config::get('global.current_exam_month_id');
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $user_id = @Auth::user()->id;
        $custom_component_obj = new CustomComponent;
        $master = $custom_component_obj->getExamcenterDataByUserId($user_id);
        if (empty($master)) {
            return Redirect::back()->with('error', 'You are not mapped with us as an Examcenter');
        }
        $centerCode = "ecenter" . $course;
        $districts = $this->districtsByState();
        $districtNameListname = @$districts[$master->district_id];
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $examcentermaterial = "examcentermaterial";
        $examcentermaterial_zip = "examcentermaterial_zip";
        $nextPath = $current_folder_year[1] . "/" . $examcentermaterial . "/" . $exam_month . "/" . $course . "/" . $districtNameListname . "/" . $master->$centerCode . "/";
        $folder_path = public_path("files/reports/" . $nextPath);
        $destdirTemp = 'allzipsave/examcenter/' . $current_admission_year_string . '/' . $exam_month . "/examcenterlogin" . '/';
        $folderPathsTemp = public_path($destdirTemp);
        File::makeDirectory($folderPathsTemp, $mode = 0777, true, true);
        $zip_file_name = $master->$centerCode . "_exam_cetner_material.zip";
        $zip_file_name = 'allzipsave/examcenter/' . $current_admission_year_string . '/' . $exam_month . "/examcenterlogin" . '/' . $zip_file_name;
        $zip_file_name = public_path($zip_file_name);
        $zip_file = $this->_zipAndDownload($folder_path, $zip_file_name);
        return response()->download($zip_file);
    }

    public function student_fees(Request $request)
    {
        $user_role = Session::get('role_id');
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
        $title = "Student Fees Report";
        $table_id = "Student_Fees";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $custom_component_obj = new CustomComponent;
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
                'url' => 'downloadStudentFeesExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadStudentFeesPdf',
                'status' => true
            ),
        );


        $filters = array(
            array(
                "lbl" => "Enrollment NO",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "AiCode",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'placeholder' => "AiCode",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Total",
                'fld' => 'total',
                'input_type' => 'text',
                'placeholder' => "Total",
                'dbtbl' => 'student_fees',
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
            ),

        );

        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "AiCode",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Services",
                'fld' => 'online_services_fees',
                'input_type' => 'text',
                'placeholder' => "Services",
                'dbtbl' => 'student_fees',
            ),
            array(
                "lbl" => "ADD Subject Fees",
                'fld' => 'add_sub_fees',
                'input_type' => 'text',
                'placeholder' => "ADD Subject Fees",
                'dbtbl' => 'student_fees',
            ),
            array(
                "lbl" => "Forward Fees",
                'fld' => 'forward_fees',
                'input_type' => 'text',
                'placeholder' => "Forward Fees",
                'dbtbl' => 'student_fees',
            ),
            array(
                "lbl" => "Toc Fees",
                'fld' => 'toc_fees',
                'input_type' => 'text',
                'placeholder' => "Toc Fees",
                'dbtbl' => 'student_fees',
            ),
            array(
                "lbl" => "Practical Fees",
                'fld' => 'practical_fees',
                'input_type' => 'text',
                'placeholder' => "Practical Fees",
                'dbtbl' => 'student_fees',
            ),
            array(
                "lbl" => "Readm Exam Fees",
                'fld' => 'readm_exam_fees',
                'input_type' => 'text',
                'placeholder' => "Readm Exam Fees",
                'dbtbl' => 'student_fees',
            ),
            array(
                "lbl" => "Late Fees",
                'fld' => 'late_fee',
                'input_type' => 'text',
                'placeholder' => "Late Fees",
                'dbtbl' => 'student_fees',
            ),
            array(
                "lbl" => "Total",
                'fld' => 'total',
                'input_type' => 'text',
                'placeholder' => "Total",
                'dbtbl' => 'student_fees',
            )

        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $condtions = array();
        $condtions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $condtions["students.user_id"] = @Auth::user()->id;
        } else {

        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'draw' && $k != 'columns' && $k != 'order' && $k != 'start' && $k != 'length' && $k != 'search' && $k != '_' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                            $condtions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                        }
                    }
                }
            }
        }

        Session::put($formId . '_condtions', $condtions);
        $master = $custom_component_obj->getStudentFees($formId, true);
        return view('examination_reports.student_fees_reports', compact('tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function downloadStudentFeesExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new StudentFeesExlExport;
        $filename = 'application_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function downloadStudentFeesPdf(Request $request)
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
        $formId = "Student_Fees_Report";
        $custom_component_obj = new CustomComponent;
        $result = $custom_component_obj->getStudentFees($formId, false);
        $fileName = $formId . "_" . date("dmY his");
        $reportname = 'Student Fees Report';
        //return view ('examination_reports.reporting_studentfees_pdf',compact('result','reportname'));
        $pdf = PDF::loadView('examination_reports.reporting_studentfees_pdf', compact('result', 'reportname'));
        return $pdf->download($fileName . '.pdf');
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

        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
        $table_id = "Admission_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $custom_component_obj = new CustomComponent;
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
                "lbl" => "Enrollment NO",
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
                "lbl" => "Enrollment NO",
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
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id
            ),
            array(
                "lbl" => "Admission Type",
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
            )
        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $condtions = array();
        $condtions["students.exam_year"] = CustomHelper::_get_selected_sessions();
        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $condtions["students.user_id"] = @Auth::user()->id;
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
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                            $condtions[$iv['dbtbl'] . "." . $k] = $v;
                        } else {
                            $condtions[$k] = $v;
                        }
                        break;
                    }
                }
            }
        }
        Session::put($formId . '_condtions', $condtions);

        $master = $custom_component_obj->getApplicationData($formId);

        return view('admission_reports.student_applications', compact('tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function sessional_report_h(Request $request)
    {
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $subject_list = $this->subjectList();
        $subject_list_name = $this->subjectListName();

        $yes_no = $this->master_details('yesno');
        $yes_no_temp = $this->master_details('yesno');
        $yes_no_temp[''] = "No";
        $title = "Sessional Report";
        $table_id = "Sessional_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $custom_component_obj = new CustomComponent;
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
                'url' => 'downloadsessionalexportExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => false
            ),
        );


        $filters = array(
            array(
                "lbl" => "Enrollment NO",
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
            // array(
            // 	"lbl" => "Medium",
            // 	'fld' => 'medium',
            // 	'input_type' => 'select',
            // 	'options' => $midium,
            // 	'placeholder' => 'Medium',
            // 	'dbtbl' => 'students',
            // ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'placeholder' => 'Subject',
                'dbtbl' => 'exam_subjects'
            ),
            array(
                "lbl" => "Is Sessional Mark Entered",
                'fld' => 'is_sessional_mark_entered',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is Sessional Mark Entered',
                'dbtbl' => 'exam_subjects'
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
                'fld' => 'srno',
                'order' => 1
            ),
            array(
                "lbl" => "Enrollment NO",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
                'order' => 2
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => '',
                'dbtbl' => 'students',
                'order' => 3
            ),
            /*
			array(
				"lbl" => "Gender",
				'fld' => 'gender_id',
				'input_type' => 'select',
				'options' => $gender_id
			),
			*/
            /*array(
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
				"lbl" => "Admission Type",
				'fld' => 'adm_type',
				'input_type' => 'select',
				'options' => $adm_types
			),*/
            /*
			array(
				"lbl" => "Medium",
				'fld' => 'medium',
				'input_type' => 'select',
				'options' => $midium
			),
			*/
            /* array(
				"lbl" => "Subject",
				'fld' => 'subject_id',
				'input_type' => 'select',
				'options' => $subject_list,
				'dbtbl' => 'exam_subjects',
			),
			array(
				"lbl" => "Sessional marks",
				'fld' => 'sessional_marks',
				'input_type' => 'text',
				'placeholder' => "Sessional Marks",
				'dbtbl' => 'exam_subjects',
			),
			array(
				"lbl" => "Lock & Submit",
				'fld' => 'locksumbitted',
				'input_type' => 'select',
				'options' => $yes_no,
				'dbtbl' => 'applications',
			) */

            array(
                "lbl" => "Subject1",
                'fld' => 'subject1',
                'input_type' => 'select',
                'options' => $subject_list_name,
                'dbtbl' => 'exam_subjects',
                'report_type' => 'sessional',
                'vertical_type' => true,
                'subject_key' => 0,
                'order' => 5
            ),
            array(
                "lbl" => "Subject2",
                'fld' => 'subject2',
                'input_type' => 'select',
                'options' => $subject_list_name,
                'dbtbl' => 'exam_subjects',
                'report_type' => 'sessional',
                'vertical_type' => true,
                'subject_key' => 1,
                'order' => 6
            ),
            array(
                "lbl" => "Subject3",
                'fld' => 'subject3',
                'input_type' => 'select',
                'options' => $subject_list_name,
                'dbtbl' => 'exam_subjects',
                'report_type' => 'sessional',
                'vertical_type' => true,
                'subject_key' => 2,
                'order' => 7
            ),
            array(
                "lbl" => "Subject4",
                'fld' => 'subject4',
                'input_type' => 'select',
                'options' => $subject_list_name,
                'dbtbl' => 'exam_subjects',
                'report_type' => 'sessional',
                'vertical_type' => true,
                'subject_key' => 3,
                'order' => 8
            ),
            array(
                "lbl" => "Subject5",
                'fld' => 'subject5',
                'input_type' => 'select',
                'options' => $subject_list_name,
                'dbtbl' => 'exam_subjects',
                'report_type' => 'sessional',
                'vertical_type' => true,
                'subject_key' => 4,
                'order' => 9
            ),
            array(
                "lbl" => "Subject6",
                'fld' => 'subject6',
                'input_type' => 'select',
                'options' => $subject_list_name,
                'dbtbl' => 'exam_subjects',
                'report_type' => 'sessional',
                'vertical_type' => true,
                'subject_key' => 5,
                'order' => 10
            ),
            array(
                "lbl" => "Subject7",
                'fld' => 'subject7',
                'input_type' => 'select',
                'options' => $subject_list_name,
                'dbtbl' => 'exam_subjects',
                'report_type' => 'sessional',
                'vertical_type' => true,
                'subject_key' => 6,
                'order' => 11
            ),
            array(
                "lbl" => "Is Sessional Mark Entered",
                'fld' => 'is_sessional_mark_entered',
                'input_type' => 'select',
                'options' => $yes_no_temp,
                'order' => 12
            ),
        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $condtions = array();
        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');

            $aicenter_user_id = Auth::user()->id;
            $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
            $user_ai_code = @$aicenter_user_ids->ai_code;
            $condtions["students.ai_code"] = $user_ai_code;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'students'
            );
            $tableData[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'users',
                'order' => 4
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                            $condtions[$iv['dbtbl'] . "." . $k] = $v;
                        }
                    }
                }
            }
        }

        $tableData = $this->_sortArray($tableData);
        Session::put($formId . '_condtions', $condtions);

        $master = $custom_component_obj->getSessionalDataH($formId);

        return view('examination_reports.sessional_report',
            compact('tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters',
                'title', 'breadcrumbs', 'gender_id', 'yes_no', 'yes_no_temp', 'midium'))->withInput($request->all());
    }

    public function downloadsessionalexportExl()
    {
        $examcenter_exl_data = (new SessionalExlExport);
        return Excel::download($examcenter_exl_data, 'Sessional.xlsx');
    }

    public function sessional_report(Request $request)
    {
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $subject_list = $this->subjectList();

        $yes_no = $this->master_details('yesno');
        $title = "Sessional Report";
        $table_id = "Sessional_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $custom_component_obj = new CustomComponent;
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
                "lbl" => "Enrollment NO",
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
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'placeholder' => 'Subject',
                'dbtbl' => 'exam_subjects'
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
                "lbl" => "Enrollment NO",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'fld_url' => '',
                'dbtbl' => 'students'
            ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id
            ),
            array(
                "lbl" => "Admission Type",
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
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'dbtbl' => 'exam_subjects',
            ),
            array(
                "lbl" => "Sessional marks",
                'fld' => 'sessional_marks',
                'input_type' => 'text',
                'placeholder' => "Sessional Marks",
                'dbtbl' => 'exam_subjects',
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no,
                'dbtbl' => 'applications',
            )
        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $condtions = array();
        $condtions["students.exam_year"] = CustomHelper::_get_selected_sessions();
        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $condtions["students.user_id"] = @Auth::user()->id;
        } else {
            // $condtions["students.user_id"] = @Auth::user()->id;
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'users'
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
            $inputs = $request->all();
            $inputs = array_filter($inputs);
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                            $condtions[$iv['dbtbl'] . "." . $k] = $v;
                        }
                    }
                }
            }
        }
        Session::put($formId . '_condtions', $condtions);

        $master = $custom_component_obj->getSessionalData($formId);

        return view('examination_reports.sessional_report',
            compact('tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters',
                'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function studentchecklists(Request $request)
    {
        $title = "Student checklist Report";
        $table_id = "ViewStudent_Checklists_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));
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
                "label" => "Export PDF",
                'url' => 'downloadstudentchecklistsPdf',
                'status' => true
            )
        );
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);


        return view('examination_reports.student_check_lists_report', compact('aiCenters', 'exportBtn', 'title', 'breadcrumbs', 'courses', 'stream_id'));
    }


    public function olddownloadstudentchecklistsPdf($course = null, $stream = null, $ai_code = null, Request $request)
    {

        $custom_component_obj = new CustomComponent;
        $title = "Student checklist Report";
        $table_id = "Student_Checklists_Report";
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $genders = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'category_b';
        $category_b = $this->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $this->master_details($combo_name);
        $combo_name = 'year';
        $year = $this->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $this->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $this->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);
        $combo_name = 'minage';
        $minage = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        //$rsos_years = $this->getListRsosYears();
        $combo_name = "year";
        $rsos_years = $this->master_details($combo_name);


        $subjectCodes = $this->subjectCodeList($course);


        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1];

        $boards = $this->getBoardList();
        // $boards = $this->getRsosYearsList();
        $conditions = array();


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        if ($isAdminStatus == false) {
            $conditions["students.ai_code"] = @Auth::user()->ai_code;
        }

        if (isset($ai_code) && !empty($ai_code) && $ai_code != 0) {
            $conditions["students.ai_code"] = $ai_code;
        }

        if (isset($stream) && !empty($stream) && $stream != 0) {
            $conditions["students.stream"] = $stream;
        }

        if (isset($course) && !empty($course) && $course != 0) {
            $conditions["students.course"] = $course;
        }
        $formId = ucfirst(str_replace(" ", "_", $title));
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        Session::put($formId . '_conditions', $conditions);
        $master = $custom_component_obj->getStudentCheckListData($formId);
        $aicode = Auth::user()->ai_code;

        return view('examination_reports.student_checklists_pdf', compact('categorya', 'midium', 'genders', 'subjectCodes', 'rsos_years', 'master', 'studentDocumentPath', 'ai_code', 'courses', 'course', 'boards', 'adm_types', 'stream', 'aicode'));
        $pdf = PDF::loadView('examination_reports.student_checklists_pdf', compact('categorya', 'genders', 'subjectCodes', 'rsos_years', 'master', 'studentDocumentPath', 'ai_code', 'courses', 'course', 'boards', 'adm_types'));
        $filename = $ai_code . "_checklist.pdf";
        Storage::put("public/files/" . $current_folder_year[1] . "/" . "checklist" . "/" . $stream . "/" . $course . "/" . $ai_code . "/" . $filename, $pdf->output());
        return $pdf->download($filename); //For download

        for ($i = 0; $i < 100; $i++) {
            @$items[$i] = $i + 1;
        }

        $pdf = PDF::loadView('examination_reports.student_checklists_pdf', compact('categorya', 'midium', 'genders', 'subjectCodes', 'rsos_years', 'master', 'studentDocumentPath', 'ai_code', 'courses', 'course', 'boards', 'adm_types', 'items', 'stream', 'aicode'));
        $pdf->getDOMPdf()->set_option('isPhpEnabled', true);
        $path = public_path("files/" . $current_folder_year[1] . "/" . "checklist" . "/" . $stream . "/" . $course . "/" . $aicode . "/");
        File::makeDirectory($path, $mode = 0777, true, true);
        $filename = "checklist.pdf";
        if (@$aicode) {
            $filename = $aicode . "_checklist.pdf";
        } else {
            $filename = date("d-m-Y") . "_checklist.pdf";
        }

        $completepath = $path . $filename;
        $pdf->save($completepath);
        return (Response::download($completepath));
    }

    public function tocChecklists(Request $request)
    {
        $title = "Toc checklist Report";
        $table_id = "ViewToc_Checklists_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));
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
                "label" => "Export TOC Checklist",
                'url' => 'downloadTocCheckListsPdf',
                'status' => true
            )
        );

        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);

        return view('examination_reports.toc_check_lists_report', compact('aiCenters', 'exportBtn', 'title', 'breadcrumbs', 'courses', 'stream_id'));
    }

    public function downloadtocchecklistsPdf1(Request $request)
    {

        $request->validate([
            'ai_code' => 'required',
            'stream' => 'required',
            'course' => 'required',
        ]);


        return redirect()->route('downloadTocCheckListsPdf', array($request->course, $request->stream, $request->ai_code));
    }

    public function downloadTocCheckListsPdf($course = null, $stream = null, $ai_code = null, Request $request)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $custom_component_obj = new CustomComponent;
        if (@$ai_code) {
            $aiCenters = $custom_component_obj->getAiCenters($ai_code);
        } elseif (@$ai_code == 0) {
            $aiCenters = $custom_component_obj->getAiCenters();

        }

        $title = "Toc checklist Report";
        $table_id = "Toc_Checklists_Report";
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $genders = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'category_b';
        $category_b = $this->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $this->master_details($combo_name);
        $combo_name = 'year';
        $year = $this->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $this->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $this->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);
        $combo_name = 'minage';
        $minage = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);

        //$rsos_years = $this->getListRsosYears();
        $combo_name = "year";
        $rsos_years = $this->master_details($combo_name);
        $tocpassyear = DB::table('rsos_years')->pluck('yearstext', 'id');
        $tocpassfail = DB::table('rsos_years_fail')->pluck('yearstext', 'id');
        $current_admission_session_id = Config::get("global.current_admission_session_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");

        $subjectCodes = $this->subjectCodeList($course);
        // dd($subjectCodes);

        $subjectCodes = $this->subjectCodeList($course);
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);

        $studentDocumentPath = $student_document_path[1];

        $boards = $this->getBoardList();
        $rsos_yearsstudent = $this->rsos_years();
        // $boards = $this->getRsosYearsList();

        $aicode = [];
        $checklist = "tocchecklist";
        foreach ($aiCenters as $key => $value) {
            @$aicodetemp = $key;
            @$aicode = $key;
            $conditions = array();
            $conditions["students.ai_code"] = $aicode;
            $conditions["students.stream"] = $stream;
            $conditions["students.course"] = $course;
            $conditions["students.is_eligible"] = 1;
            $conditions_applications["applications.toc"] = 1;
            $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();
            @$reportname = $aicodetemp;

            $master = Student::where($conditions)
                ->with('application', 'toc', 'toc_subject')->whereRelation('application', $conditions_applications)->orderBy('enrollment', 'ASC')->get();

            //return view('examination_reports.student_checklists_pdf',compact('categorya','midium','genders','subjectCodes','rsos_years','master','studentDocumentPath','ai_code','courses','course','boards','adm_types','stream','reportname'));

            $pdf = PDF::loadView('examination_reports.toc_checklists_pdf', compact('categorya', 'midium', 'genders', 'subjectCodes', 'rsos_years', 'master', 'studentDocumentPath', 'aicode', 'courses', 'course', 'boards', 'adm_types', 'stream', 'reportname', 'rsos_yearsstudent', 'tocpassyear', 'tocpassfail'));
            $pdf->setOption('footer-right', 'Page [page] of [toPage]');
            $path = public_path("files/reports/" . $current_folder_year[1] . "/" . $checklist . "/stream" . $stream . "/" . $course . "/");
            File::makeDirectory($path, $mode = 0777, true, true);
            if (@$aicode) {
                $filename = $aicode . "_toc_checklist.pdf";
            } else {
                $filename = $aicode . "_toc_checklist.pdf";
            }

            $completepath = $path . $filename;
            $pdf->save($completepath, $pdf, true);
        }
        if (@$ai_code) {
            //return( Response::download($completepath));
            return redirect()->route('tocChecklists')->with('message', 'TOC Checklists Generated successfully');
        } elseif (@$ai_code == 0) {
            /*$zip_file_name = $course . "_" .  "stream".$stream .  "_Tocchecklist.zip";
		 $folder_path = "files/reports/" . $current_folder_year[1]."/". $checklist ."/". "stream" . $stream ."/". $course;
		 $folder_path = public_path($folder_path);
		 $zip_file = $this->_zipAndDownload($folder_path,$zip_file_name);
		 return response()->download($zip_file);*/
            return redirect()->route('tocChecklists')->with('message', 'TOC Checklists Generated successfully');
        }
    }


    public function getDownloadtocchecklistsingleaicode(Request $request)
    {

        $request->validate([
            'ai_code' => 'required',
            'stream' => 'required',
            'course' => 'required',
        ]);

        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        /*toc checklist */
        if ($request->type == 31) {
            $checklistall = "tocchecklist";
            $filename = $request->ai_code . '_toc_checklist.pdf';
        } /*Supplementary checklist */
        elseif ($request->type == 32) {
            $checklistall = "supplementarychecklist";
            $filename = $request->ai_code . '_Supplementary__checklist.pdf';
        } /*students checklist */
        elseif ($request->type == 33) {
            $checklistall = "checklist";
            $filename = $request->ai_code . '_checklist.pdf';
        }
        $path = public_path("files/reports/" . $current_folder_year[1] . "/" . $checklistall . "/" . "stream" . $request->stream . "/" . $request->course . "/" . $filename);

        if (File::exists($path)) {
            return Response::download($path);
        } else {
            if ($request->type == 31) {
                return redirect()->route('tocChecklists')->with('error', 'TOC Checklist Not Generated');
            }
            if ($request->type == 32) {
                return redirect()->route('SupplementaryChecklists')->with('error', 'Supplementary Checklist Not Generated');
            }
            if ($request->type == 33) {
                return redirect()->route('studentchecklists')->with('error', 'student Checklist Not Generated');
            }
        }
    }


    public function getDownloadtocchecklistzipdownload($course = null, $stream = null, $type = null)
    {
        $current_admission_year_string = Config::get('global.current_admission_year_string');
        $exam_month = Config::get('global.current_exam_month_id');
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        if ($type == 31) {
            $checklistall = "tocchecklist";
            $checklist_zip = "tocchecklist.zip";
            $checklist_login_zip = "tocchecklistslogin";
        } elseif ($type == 32) {
            $checklistall = "supplementarychecklist";
            $checklist_zip = "supplementarychecklist.zip";
            $checklist_login_zip = "supplementarychecklistslogin";
        } elseif ($type == 33) {
            $checklistall = "checklist";
            $checklist_zip = "studentschecklist.zip";
            $checklist_login_zip = "studentschecklistslogin";
        }
        $nextPath = $current_folder_year[1] . "/" . $checklistall . "/" . "stream" . $stream . "/" . $course . "";
        $folder_path = public_path("files/reports/" . $nextPath);

        if (File::exists($folder_path)) {
            $destdirTemp = 'allzipsave/' . $checklistall . '/' . $current_admission_year_string . '/' . "stream" . $stream . "/" . $checklist_login_zip . "/";
            $folderPathsTemp = public_path($destdirTemp);
            File::makeDirectory($folderPathsTemp, $mode = 0777, true, true);
            $zip_file_name = $checklist_zip;
            $zip_file_name = 'allzipsave/' . $checklistall . '/' . $current_admission_year_string . '/' . "stream" . $stream . "/" . $checklist_login_zip . "/" . $zip_file_name;
            $zip_file_name = public_path($zip_file_name);
            $zip_file = $this->_zipAndDownload($folder_path, $zip_file_name);
            return response()->download($zip_file);
        } else {
            if ($type == 31) {
                return redirect()->route('tocChecklists')->with('error', 'TOC Checklist Not Generated');
            }
            if ($type == 32) {
                return redirect()->route('SupplementaryChecklists')->with('error', 'Supplementary Checklist Not Generated');
            }
            if ($type == 33) {
                return redirect()->route('tocChecklists')->with('error', 'TOC Checklist Not Generated');
            }
        }
    }


    public function reportexamcenterwise(Request $request)
    {
        $stream = Config::get("global.defaultStreamId");

        if (!empty($request->stream)) {
            $stream = $request->stream;
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

        // $stream = Crypt::decrypt($stream);

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
                "label" => "Export Excel",
                'url' => 'reportexamcenterwiseexcel',
                'status' => true,
            ),
            array(
                "label" => " Supplementary count Excel ",
                'url' => 'reportexamcenterwisesupplementarycountexcel',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'reportexamcenterwisePdf',
                'status' => true
            ),
        );
        $filters = array(
            array(
                "lbl" => "Stream Type",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream',
                'dbtbl' => 'students',
            ),
        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["center_allotments.stream"] = $stream;
        $conditions["center_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
        Session::put($formId . '_condtions', $conditions);
        $master = $custom_component_obj->getExamcenterAllotmentDataForReport($formId);
        return view('examination_reports.reportexamcenterwise', compact('stream', 'master', 'exportBtn', 'title', 'breadcrumbs', 'filters'))->withInput($request->all());

    }

    public function reportexamcenterwiseexcel(Request $request, $type = "xlsx")
    {
        $examcenter_exl_data = new Examcenterwiseexcel;
        $filename = 'Examcenterwiseexcel_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($examcenter_exl_data, $filename);
    }

    public function reportexamcenterwisesupplementarycountexcel(Request $request, $type = "xlsx")
    {
        $examcenter_exl_data = new supplementaryexamcenterwiseexcel;
        $filename = 'supplementaryexamcenterwiseexcel_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($examcenter_exl_data, $filename);
    }


    public function reportexamcenterwisePdf()
    {

        $stream = Config::get("global.defaultStreamId");
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $custom_component_obj = new CustomComponent;
        $formId = "Exam_Center_Student_Allotment";
        $master = $custom_component_obj->getExamcenterAllotmentDataForReport($formId, false);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $reportname = 'Examcenter wise Report';
        //return view('examination_reports.reportexamcenterwise_pdf',compact('master','reportname','stream','exam_session'));
        $pdf = PDF::loadView('examination_reports.reportexamcenterwise_pdf', compact('master', 'reportname', 'stream', 'exam_session'))->setOrientation('landscape');
        $path = public_path("files/" . $current_folder_year[1] . "/" . "examcenterwise" . "/" . $stream);
        File::makeDirectory($path, $mode = 0777, true, true);
        $pdf->setOption('footer-right', 'Page [page] of [toPage]');
        $filename = date("dmYhms") . "_examcenterwise.pdf";
        $completepath = $path . "/" . $filename;
        $pdf->save($completepath, $pdf, true);
        return (Response::download($completepath));


    }

    public function nominalnrview(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $exam_month = Config::get("global.supp_current_admission_exam_month");
        $aicenername = 'aicodelogin';
        $combo_name = 'aicentermaterialyear';
        $aicentermaterialyear = $this->master_details($combo_name);
        $district = District::where('state_id', '6')->pluck('name', 'name');
        $title = "Nominal Nr Reports";
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
                "label" => "Export PDF",
                'url' => 'nominalnrpdf',
                'status' => false
            ),
        );
        $filters = array(
            array(
                "lbl" => "Ai code",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai code',
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
                "lbl" => "District",
                'fld' => 'district',
                'input_type' => 'select',
                'options' => $district,
                'placeholder' => 'District',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Year",
                'fld' => 'aicentermaterialyear',
                'input_type' => 'select',
                'options' => $aicentermaterialyear,
                'placeholder' => 'Year',
                'dbtbl' => 'students',
            ),
        );
        if (count($request->all()) > 0) {
            $Student = new Student; /// create model object
            $validator = Validator::make($request->all(), $Student->rulesfilter);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            if ($request->stream == 1) {
                $stream = 'stream1';
            } else if ($request->stream == 2) {
                $stream = 'stream2';
            }
            $zip_file_name = "default.zip";
            $folder_path = 'files/reports/' . $request->aicentermaterialyear . '/' . $stream . '/aicentermaterial' . '/' . $request->course;
            if (!empty($request->district)) {
                $folder_path .= '/' . $request->district;
                $zip_file_name = $request->district . ".zip";
            }
            if (!empty($request->ai_code)) {
                $folder_path .= '/' . $request->ai_code;
                $zip_file_name = $request->ai_code . ".zip";
            }
            $folder_path = public_path($folder_path);
            $destdirTemp = 'allzipsave/examcenter/' . $request->aicentermaterialyear . '/' . $exam_month . '/' . $aicenername . '/';
            $folderPathsTemp = public_path($destdirTemp);
            File::makeDirectory($folderPathsTemp, $mode = 0777, true, true);
            $zip_file_name = 'allzipsave/examcenter/' . $request->aicentermaterialyear . '/' . $exam_month . '/' . $aicenername . '/' . $zip_file_name;
            $zip_file_name = public_path($zip_file_name);
            $zip_file = $this->_zipAndDownload($folder_path, $zip_file_name);
            return response()->download($zip_file);
        }
        return view('examination_reports.nominalnr', compact('breadcrumbs', 'title', 'filters', 'exportBtn'));
    }

    public function Nominalnrgenerateview(Request $request)
    {
        if ($request->all()) {
            $inputs = $request->all();
            $path = $this->getDownloadnominalrollaicodecenterwise(null, null, $request);
            return Response::download($path);
        }
        $extraDesc = "Ai Center Material with 2 pdf will be generated as : 
    		AI Center Material (Course wise with stream and supplemenatary), 
    	 	Hall Ticket (Course wise with stream and supplemenatary)";
        $title = "AI Center Nominal Roll";
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
        $user_role = Session::get('role_id');
        $ai_codes = Auth::user()->ai_code;
        $id = Auth::user()->id;
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $developeradminrole = Config::get("global.developer_admin");
        $superadminrole = Config::get("global.super_admin");
        $aicenterrole = Config::get("global.aicenter_id");
        $aicentermaterial = "aicentermaterial";
        $stream = Config::get("global.defaultStreamId");
        $examination_department = Config::get("global.examination_department");
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $course = 10;
        $coursess = 12;
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $current_year = @$current_folder_year[1];

        if ($user_role == 58) {
            return view('examination_reports.nominalnrgenerateview', compact('breadcrumbs', 'title', 'extraDesc', 'aiCenters', 'courses', 'stream_id', 'user_role', 'ai_codes', 'developeradminrole', 'superadminrole', 'aicenterrole', 'stream', 'examination_department'));
        }
        $useraicodeid = AicenterDetail::where('ai_code', $ai_codes)->pluck('district_id', 'district_id')->first();

        if (!($useraicodeid > 0)) {
            return redirect()->back()->with('error', 'Your profile not mapped with your District. Please update and try again!');
        }
        $District = District::where('state_id', 6)->where('id', $useraicodeid)->first('name');
        $filename = 'aicenter_nominalroll_' . $ai_codes . '.pdf';

        $path = public_path("files/reports/" . $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $District->name . "/" . $ai_codes . "/" . $filename);


        $path2 = public_path("files/reports/" . $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $coursess . "/" . $District->name . "/" . $ai_codes . "/" . $filename);


        return view('examination_reports.nominalnrgenerateview', compact('breadcrumbs', 'title', 'extraDesc', 'aiCenters', 'courses', 'stream_id', 'user_role', 'ai_codes', 'developeradminrole', 'superadminrole', 'aicenterrole', 'path', 'path2', 'stream', 'examination_department'));
    }

    public function getDownloadnominalrollaicodecenterwise($course = null, $stream = null, Request $request)
    {
        $ai_code = null;
        $user_role = Session::get('role_id');
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $aicentermaterial = "aicentermaterial";

        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $examination_department = Config::get("global.examination_department");
        $developeradminrole = Config::get("global.developer_admin");

        if ($user_role == $developeradminrole || $user_role == $examination_department) {
            $ai_code = $request->ai_code;
            $course = $request->course;
            $stream = $request->stream;
        } else {
            $ai_code = Auth::user()->ai_code;
            $id = Auth::user()->id;
            if (!empty($id) && !empty($ai_code)) {
            } else {
                return redirect()->back()->with('error', $ai_code . ' Something is not working.Please try again!');
            }
        }
        $districtId = $useraicodeid = AicenterDetail::where('ai_code', $ai_code)->pluck('district_id', 'district_id')->first();

        $District = $this->districtOnlyNameById($districtId);

        $filename = 'aicenter_nominalroll_' . $ai_code . '.pdf';
        $path = public_path("files/reports/" . $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $District->name . "/" . $ai_code . "/" . $filename);
        if ($user_role == $developeradminrole || $user_role == $examination_department) {
            return $path;
        } else {
            return Response::download($path);
        }
    }

    public function downloadnominalnrgenerateviewpdf(Request $request)
    {

        // $request->validate([
        //       'ai_code' => 'required',
        //       'stream' => 'required',
        //       'course' => 'required',
        //   ]);

        return redirect()->route('nominalnrpdf', array($request->course, $request->stream, $request->ai_code));
    }


    //Session::put($formId. '_condtions', $conditions);
    //$master = array();
    //$master[10] = $custom_component_obj->getNominalnrReport10($formId);
    //$master[12] = $custom_component_obj->getNominalnrReport12($formId);
    //$combo_name = 'current_folder_year';$current_folder_year = $this->master_details($combo_name);
    ///$aicentermaterial ="aicentermaterial";
    //for ($i=0; $i < 100; $i++)
    //{
    //$items[$i] = $i + 1;
    //}
    //return view('examination_reports.nominalnr_pdf',compact('subject_list','categorya','ai_code','master','subreportname','reportname'));
    //$pdf =  PDF::loadView('examination_reports.nominalnr_pdf',compact('subject_list','categorya','ai_code','master','subreportname','reportname','items'));
    //$path = public_path("files/reports/" . $current_folder_year[1]. "/stream" . $stream . "/".  $aicentermaterial .  "/".  $ai_code .  "/");
    //File::makeDirectory($path, $mode = 0777, true, true);
    //$filename = "aicentermaterial.pdf";
    //$completepath = $path .$filename;
    //$pdf->save($completepath);
    //return( Response::download( $completepath ) );
    //$pdf = PDF::loadView('myPDF', $data);
    //$pdf->getDOMPdf()->set_option('isPhpEnabled', true);
    //return $pdf->download('reportexamcenterwise.pdf');

    public function Nominalnrpdf($course = null, $stream = null, $ai_code = null, Request $request)
    {

        $stream = $stream;
        $custom_component_obj = new CustomComponent;

        if (@$ai_code) {
            $aiCenters = $custom_component_obj->getAiCenters($ai_code);
        } elseif (@$ai_code == 0) {
            $aiCenters = $custom_component_obj->getAiCenters();

        }
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $subject_list = $this->subjectCodeList();
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $subreportname = "AI CENTER NOMINAL ROLL";
        $aicentermaterial = "aicentermaterial";
        $exam_year = CustomHelper::_get_selected_sessions();
        $aicode = [];
        foreach ($aiCenters as $key => $value) {
            @$aicodetemp = $key;
            @$aicode = $key;

            $districtnmae = District::where('state_id', 6)->pluck('name', 'id');
            $aicodedistrictid = AicenterDetail::where('ai_code', $aicode)->groupBy('district_id')->get('district_id')->toarray();
            $aicodedistrictid1 = @$districtnmae[$aicodedistrictid['0']['district_id']];


            $conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
            $conditions["student_allotments.exam_month"] = $stream;
            $conditions["student_allotments.course"] = $course;
            $conditions["student_allotments.supplementary"] = 0;
            $conditions["student_allotments.ai_code"] = $aicode;

            $supp_conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
            $supp_conditions["student_allotments.exam_month"] = $stream;
            $supp_conditions["student_allotments.course"] = $course;
            $supp_conditions["student_allotments.supplementary"] = 1;
            $supp_conditions["student_allotments.ai_code"] = $aicode;


            @$reportname = $aicodetemp;

            $supp_master = StudentAllotment::with('document', 'student.application')->with('Supplementary', function ($query) use ($exam_year, $stream) {
                $query->where('is_eligible', 1)->whereNull('deleted_at')->where('exam_year', $exam_year)->where('exam_month', $stream);
            })->with('student', function ($query) {
                $query->whereNull('deleted_at');
            })->with('Supplementarysubjects', function ($query) {
                $query->whereNull('deleted_at');
            })
                ->where('ai_code', $aicode)->where($supp_conditions)->whereNull('deleted_at')->orderBy('enrollment', 'ASC')->get()->toArray();

            $master = StudentAllotment::with('student', 'student.application')->with('examsubject', function ($query) {
                $query->whereNull('deleted_at');
            })->with('student', function ($query) {
                $query->where('is_eligible', 1)->whereNull('deleted_at');
            })->where('ai_code', $aicode)->whereNull('deleted_at')->where($conditions)->orderBy('enrollment', 'ASC')->get()->toArray();


            // $master = Student::with('application','exam_subject')->where('students.ai_code',$aicode)
            // 	->where($conditions)->limit('2')->get()
            // 	->toArray();


            // $supp_master = Supplementary::with('SupplementarySubject','student','student.application')
            // 	->where($supp_conditions)->where('supplementaries.ai_code',$aicode)->WhereNotNull('supplementaries.challan_tid')->WhereNotNull('supplementaries.submitted')->limit('2')->get()
            // 	->toArray();

            $finalArr = array();
            @$index = 0;
            foreach ($supp_master as $student) {

                $fld = "is_supp";
                @$finalArr[$index][$fld] = true;
                $fld = "enrollment";
                @$finalArr[$index][$fld] = $student[$fld];
                $fld = "name";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "father_name";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "mother_name";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "dob";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "category_a";
                @$finalArr[$index][$fld] = $student['student'][0]['application'][$fld];
                $fld = "examsubject";
                @$finalArr[$index][$fld] = $student['supplementarysubjects'];


                @$index++;
            }
            foreach ($master as $student) {
                $fld = "is_supp";
                @$finalArr[$index][$fld] = false;
                $fld = "enrollment";
                @$finalArr[$index][$fld] = $student[$fld];
                $fld = "name";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "father_name";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "mother_name";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "dob";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "category_a";
                @$finalArr[$index][$fld] = $student['student'][0]['application'][$fld];
                $fld = "examsubject";
                @$finalArr[$index][$fld] = $student[$fld];
                @$index++;
            }
            $master = $finalArr;
            //@dd($master);
            //return view('examination_reports.nominalnr_pdf',compact('subject_list','categorya','aicode','master','subreportname','reportname','course'));

            if (@$ai_code) {
                $pdf = PDF::loadView('examination_reports.nominalnr_pdf', compact('subject_list', 'categorya', 'aicode', 'master', 'subreportname', 'reportname', 'course', 'exam_session', 'stream'));
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');
                $path = public_path("files/reports/" . $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $aicodedistrictid1 . "/" . $aicodetemp . "/");
                File::makeDirectory($path, $mode = 0777, true, true);
                $filename = 'aicenter_nominalroll_' . $aicodetemp . '.pdf';
                $completepath = $path . $filename;
                $pdf->save($completepath, $pdf, true);
                return (Response::download($completepath));
            } elseif ($ai_code == 0) {

                $pdf = PDF::loadView('examination_reports.nominalnr_pdf', compact('subject_list', 'categorya', 'aicode', 'master', 'subreportname', 'reportname', 'course', 'exam_session', 'stream'));
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');
                $path = public_path("files/reports/" . $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $aicodedistrictid1 . "/" . $aicodetemp . "/");
                File::makeDirectory($path, $mode = 0777, true, true);
                $filename = 'aicenter_nominalroll_' . $aicodetemp . '.pdf';
                $completepath = $path . $filename;
                $pdf->save($completepath, $pdf, true);
                //return(Response::download( $completepath));
            }

        }
    }

    public function board_nr_student_enrollment_view()
    {
        $title = "Board Nr Student Excel";
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
        return view('examination_reports.boardnrstudentenrollmentexcel', compact('breadcrumbs', 'title'));
    }

    public function NominalnrpdfDistrict($course = null, $stream = null, $district = null)
    {

        $stream = $stream;
        $custom_component_obj = new CustomComponent;
        $examyearcurennt = CustomHelper::_get_selected_sessions();
        if ($district) {
            $aiCenters = $custom_component_obj->getAiCentersByDistrictId($district);
        }


        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $subject_list = $this->subjectCodeList();
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $subreportname = "AI CENTER NOMINAL ROLL";
        $aicentermaterial = "aicentermaterial";
        $exam_year = CustomHelper::_get_selected_sessions();
        $aicode = [];
        foreach ($aiCenters as $key => $value) {
            @$aicodetemp = $key;
            @$aicode = $key;

            $districtnmae = District::where('state_id', 6)->pluck('name', 'id');
            $aicodedistrictid = AicenterDetail::where('ai_code', $aicode)->groupBy('district_id')->get('district_id')->toarray();
            $aicodedistrictid1 = @$districtnmae[$aicodedistrictid['0']['district_id']];


            $conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
            $conditions["student_allotments.exam_month"] = $stream;
            $conditions["student_allotments.course"] = $course;
            $conditions["student_allotments.supplementary"] = 0;
            $conditions["student_allotments.ai_code"] = $aicode;

            $supp_conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
            $supp_conditions["student_allotments.exam_month"] = $stream;
            $supp_conditions["student_allotments.course"] = $course;
            $supp_conditions["student_allotments.supplementary"] = 1;
            $supp_conditions["student_allotments.ai_code"] = $aicode;


            @$reportname = $aicodetemp;


            $supp_master = StudentAllotment::with('document', 'student.application')->with('Supplementary', function ($query) use ($exam_year, $stream) {
                $query->where('is_eligible', 1)->whereNull('deleted_at')->where('exam_year', $exam_year)->where('exam_month', $stream);
            })->with('student', function ($query) {
                $query->whereNull('deleted_at');
            })->with('Supplementarysubjects', function ($query) {
                $query->whereNull('deleted_at');
            })
                ->where('ai_code', $aicode)->where($supp_conditions)->whereNull('deleted_at')->orderBy('enrollment', 'ASC')->get()->toArray();

            $master = StudentAllotment::with('student', 'student.application')->with('examsubject', function ($query) {
                $query->whereNull('deleted_at');
            })->with('student', function ($query) {
                $query->where('is_eligible', 1)->whereNull('deleted_at');
            })->where('ai_code', $aicode)->whereNull('deleted_at')->where($conditions)->orderBy('enrollment', 'ASC')->get()->toArray();


            // $master = Student::with('application','exam_subject')->where('students.ai_code',$aicode)
            // 	->where($conditions)->limit('2')->get()
            // 	->toArray();


            // $supp_master = Supplementary::with('SupplementarySubject','student','student.application')
            // 	->where($supp_conditions)->where('supplementaries.ai_code',$aicode)->WhereNotNull('supplementaries.challan_tid')->WhereNotNull('supplementaries.submitted')->limit('2')->get()
            // 	->toArray();

            $finalArr = array();
            @$index = 0;
            foreach ($supp_master as $student) {

                $fld = "is_supp";
                @$finalArr[$index][$fld] = true;
                $fld = "enrollment";
                @$finalArr[$index][$fld] = $student[$fld];
                $fld = "name";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "father_name";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "mother_name";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "dob";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "category_a";
                @$finalArr[$index][$fld] = $student['student'][0]['application'][$fld];
                $fld = "examsubject";
                @$finalArr[$index][$fld] = $student['supplementarysubjects'];

                @$index++;
            }
            foreach ($master as $student) {
                $fld = "is_supp";
                @$finalArr[$index][$fld] = false;
                $fld = "enrollment";
                @$finalArr[$index][$fld] = $student[$fld];
                $fld = "name";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "father_name";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "mother_name";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "dob";
                @$finalArr[$index][$fld] = $student['student'][0][$fld];
                $fld = "category_a";
                @$finalArr[$index][$fld] = $student['student'][0]['application'][$fld];
                $fld = "examsubject";
                @$finalArr[$index][$fld] = $student[$fld];
                @$index++;
            }
            $master = $finalArr;
            //@dd($master);
            //return view('examination_reports.nominalnr_pdf',compact('subject_list','categorya','aicode','master','subreportname','reportname','course'));


            $pdf = PDF::loadView('examination_reports.nominalnr_pdf', compact('subject_list', 'categorya', 'aicode', 'master', 'subreportname', 'reportname', 'course', 'exam_session', 'stream'));
            $pdf->setOption('footer-right', 'Page [page] of [toPage]');
            $path = public_path("files/reports/" . $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $aicodedistrictid1 . "/" . $aicodetemp . "/");
            File::makeDirectory($path, $mode = 0777, true, true);
            $filename = 'aicenter_nominalroll_' . $aicodetemp . '.pdf';
            $completepath = $path . $filename;
            $pdf->save($completepath, $pdf, true);
            //return(Response::download( $completepath));


        }
        echo "Done" . date("Y-m-d h:i:sa") . "<br>";
    }

    /** Board NR i.e. Subject NR for Stream and Supplementary */
    public function board_nr_student_enrollment_sheet_excel_download($course = null, $type = "xlsx")
    {
        $application_exl_data = (new BoardNrStudentEnrollmentExcel($course));
        $filename = 'BoardNrStudentEnrollmentExcel' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);


    }

    public function downloadstudentfeesummaryFeesExl(Request $request, $type = "xlsx")
    {
        $examcenter_exl_data = new Studentfeesummaryexcel;
        $filename = 'Studentfeesummaryexcel_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($examcenter_exl_data, $filename);
    }


    public function downloadstudentfeesummaryPdf(Request $request)
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
        $formId = "Student_Fees_Summary_Report";
        $custom_component_obj = new CustomComponent;
        $result = $custom_component_obj->getStudentFeeSummaryPay($formId, false);
        $fileName = $formId . "_" . date("dmY his");
        $reportname = 'Student Fees Report';
        //return view ('examination_reports.reporting_studentfees_pdf',compact('result','reportname'));
        $pdf = PDF::loadView('examination_reports.reporting_studentfeesummary_pdf', compact('result', 'reportname'));
        return $pdf->download($fileName . '.pdf');
    }

    public function getStudentFeeSummaryPay(Request $request)
    {
        $user_role = Session::get('role_id');
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
        $title = "Student Fees Summary Report";
        $table_id = "Student_Summary_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $custom_component_obj = new CustomComponent;
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
                'url' => 'downloadstudentfeesummaryFeesExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadstudentfeesummaryPdf',
                'status' => true
            ),
        );


        $filters = array(
            array(
                "lbl" => "AiCode",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'placeholder' => "AiCode",
                'dbtbl' => 'students',
            ),
            // array(
            // 	"lbl" => "Total",
            // 	'fld' => 'total',
            // 	'input_type' => 'text',
            // 	'placeholder' => "Total",
            // 	'dbtbl' => 'student_fees',
            // ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
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

        );

        $tableData = array();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $condtions = array();
        $condtions["users.exam_year"] = CustomHelper::_get_selected_sessions();
        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $condtions["students.user_id"] = @Auth::user()->id;
        } else {

        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'draw' && $k != 'columns' && $k != 'order' && $k != 'start' && $k != 'length' && $k != 'search' && $k != '_' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                            $condtions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                        }
                    }
                }
            }
        }

        Session::put($formId . '_condtions', $condtions);
        $master = $custom_component_obj->getStudentFeeSummaryPay($formId, true);
        return view('examination_reports.student_fees_Summary', compact('tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function studentfeeorgsummary(Request $request)
    {
        $user_role = Session::get('role_id');
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
        $title = "Student Fees Org Summary Report";
        $table_id = "Student_Org_Summary_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $custom_component_obj = new CustomComponent;
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
                'url' => 'downloadstudentfeesorgummaryFeesExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadstudentfeesorgummaryPdf',
                'status' => true
            ),
        );


        $filters = array(
            array(
                "lbl" => "AiCode",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'placeholder' => "AiCode",
                'dbtbl' => 'aicenter_details',
            ),
            // array(
            // 	"lbl" => "Total",
            // 	'fld' => 'total',
            // 	'input_type' => 'text',
            // 	'placeholder' => "Total",
            // 	'dbtbl' => 'student_fees',
            // ),
            array(
                "lbl" => "Gender",
                'fld' => 'gender_id',
                'input_type' => 'select',
                'options' => $gender_id,
                'placeholder' => 'Gender',
                'dbtbl' => 'students',
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

        );

        $tableData = array();

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $condtions = array();
        $condtions["users.exam_year"] = CustomHelper::_get_selected_sessions();
        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $condtions["students.user_id"] = @Auth::user()->id;
        } else {

        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'draw' && $k != 'columns' && $k != 'order' && $k != 'start' && $k != 'length' && $k != 'search' && $k != '_' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                            $condtions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                        }
                    }
                }
            }
        }

        Session::put($formId . '_condtions', $condtions);
        $master = $custom_component_obj->getStudentFeeSummaryOrg($formId, true);
        return view('examination_reports.student_fees_Org_Summary', compact('tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function downloadstudentfeesorgummaryFeesExl(Request $request, $type = "xlsx")
    {
        $examcenter_exl_data = new Studentfeeorgsummaryexcel;
        $filename = 'Studentfeeorgsummaryexcel_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($examcenter_exl_data, $filename);
    }

    public function downloadstudentfeesorgummaryPdf(Request $request)
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
        $formId = "Student_Fees_Org_Summary_Report";
        $custom_component_obj = new CustomComponent;
        $result = $custom_component_obj->getStudentFeeSummaryOrg($formId, false);
        $fileName = $formId . "_" . date("dmY his");
        $reportname = 'Student Fees Report';
        //return view ('examination_reports.reporting_studentfees_pdf',compact('result','reportname'));
        $pdf = PDF::loadView('examination_reports.reporting_studentfeeorgsummary_pdf', compact('result', 'reportname'));
        return $pdf->download($fileName . '.pdf');
    }

    public function hallticketbulkviews(Request $request)
    {
        if ($request->all()) {
            $inputs = $request->all();
            $path = $this->getDownloadhallticketaicodecenterwise(null, null, $request);
            return Response::download($path);
        }
        $extraDesc = "Ai Center Material with 2 pdf will be generated as : 
    		AI Center Material (Course wise with stream and supplemenatary), 
    	 	Hall Ticket (Course wise with stream and supplemenatary)";
        $title = "Hall Ticket";
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => ''
            ),
            array(
                "label" => $title,
                'url' => 'hall_ticket_bulk_download'
            )
        );
        $user_role = Session::get('role_id');
        $ai_codes = '01001';
        $id = Auth::user()->id;
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $developeradminrole = Config::get("global.developer_admin");
        $superadminrole = Config::get("global.super_admin");
        $aicenterrole = Config::get("global.aicenter_id");
        $examination_department = Config::get("global.examination_department");
        $stream = Config::get("global.defaultStreamId");
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");

        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $aicentermaterial = "aicentermaterial";
        $course = 10;
        $coursess = 12;

        if ($user_role == 58) {
            return view('examination_reports.hallticketbulkviews', compact('breadcrumbs', 'title', 'extraDesc', 'aiCenters', 'courses', 'stream_id', 'user_role', 'ai_codes', 'developeradminrole', 'superadminrole', 'aicenterrole', 'stream', 'examination_department'));
        }


        $useraicodeid = AicenterDetail::where('ai_code', $ai_codes)->pluck('district_id', 'district_id')->first();


        if (!($useraicodeid > 0)) {
            return redirect()->back()->with('error', 'Your profile not mapped with your District. Please update and try again!');
        }


        $District = District::where('state_id', 6)->where('id', $useraicodeid)->first('name');
        $filename = 'hallticket_' . $ai_codes . '.pdf';


        $path = public_path("files/reports/" . $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $District->name . "/" . $ai_codes . "/" . $filename);


        $path2 = public_path("files/reports/" . $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $coursess . "/" . $District->name . "/" . $ai_codes . "/" . $filename);


        return view('examination_reports.hallticketbulkviews', compact('breadcrumbs', 'title', 'extraDesc', 'aiCenters', 'courses', 'stream_id', 'user_role', 'ai_codes', 'developeradminrole', 'superadminrole', 'aicenterrole', 'stream', 'examination_department', 'path', 'path2'));
    }

    public function getDownloadhallticketaicodecenterwise($course = null, $stream = null, Request $request)
    {
        $ai_code = null;
        $user_role = Session::get('role_id');
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $aicentermaterial = "aicentermaterial";

        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $examination_department = Config::get("global.examination_department");
        $developeradminrole = Config::get("global.developer_admin");

        if ($user_role == $developeradminrole || $user_role == $examination_department) {
            $ai_code = $request->ai_code;
            $course = $request->course;
            $stream = $request->stream;
        } else {
            $ai_code = Auth::user()->ai_code;
            $id = Auth::user()->id;
            if (!empty($id) && !empty($ai_code)) {
            } else {
                return redirect()->back()->with('error', $ai_code . ' Something is not working.Please try again!');
            }
        }
        $districtId = $useraicodeid = AicenterDetail::where('ai_code', $ai_code)->pluck('district_id', 'district_id')->first();

        $District = $this->districtOnlyNameById($districtId);

        $filename = 'hallticket_' . $ai_code . '.pdf';
        $path = public_path("files/reports/" . $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $District->name . "/" . $ai_code . "/" . $filename);


        /* $ip = NULL;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			if(@$$_SERVER['REMOTE_ADDR']){
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		}

		if($ip == '10.68.181.229' || $ip == '10.68.181.236' || $ip == '10.68.181.122'){
			print_r($ip);
			echo "</br>";
			print_r($user_role);
			echo "</br>";
			print_r($developeradminrole);
			echo "</br>";

			print_r($examination_department);
			echo "</br>";

			print_r($path);
			echo "</br>";

		} */


        if ($user_role == $developeradminrole || $user_role == $examination_department) {

            // @dd('test22');
            return $path;
        } else {
            // print_r(Response::download($path));
            // @dd('test33');
            return Response::download($path);
        }
    }

    public function downloadhallticketbulviewpdf(Request $request)
    {

        // $request->validate([
        //       'ai_code' => 'required',
        //       'stream' => 'required',
        //       'course' => 'required',
        //   ]);


        return redirect()->route('hall_ticket_bulk_download', array($request->course, $request->stream, $request->ai_code));
    }

    public function hall_ticket_bulk_download($course = null, $stream = null, $ai_code = null, Request $request)
    {

        $title = "Hall Ticket Report";

        $custom_component_obj = new CustomComponent;
        if (@$ai_code) {
            $aiCenters = $custom_component_obj->getAiCenters($ai_code);
        } elseif (@$ai_code == 0) {
            $aiCenters = $custom_component_obj->getAiCenters();

        }

        $subject_list = $this->subjectCodeList($course);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'exam_time_table_start_end_time';
        $exam_time_table_start_end_time = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $subreportname = "Hall Ticket";
        $aicentermaterial = "aicentermaterial";
        $aicode = [];

        $subjects = Subject::where(array('deleted' => 0))->orderBy('subject_code')->pluck('subject_code', 'id');
        $subjects10 = Subject::where(array('deleted' => 0))->where(array('course' => 10))->orderBy('subject_code')->pluck('name', 'id');
        $subjects12 = Subject::where(array('deleted' => 0))->where(array('course' => 12))->orderBy('subject_code')->pluck('name', 'id');
        $practicalsubjects12 = Subject::where(array('deleted' => 0))->where(array('practical_type' => 1))->orderBy('subject_code')
            ->pluck('subject_code', 'id')->toArray();

        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);

        foreach ($aiCenters as $key => $value) {
            $aicodetemp = $key;
            $ai_code = $aicode = $key;
            $districtnmae = District::pluck('name', 'id');

            $aicodedistrictid = AicenterDetail::where('ai_code', $aicode)->groupBy('district_id')->get('district_id')->toarray();

            $aicodedistrictid1 = $districtnmae[$aicodedistrictid['0']['district_id']];
            //dd($ai_code);
            $reportname = $aicodetemp;

            /* Master data get start */
            $conditions = array();
            $conditionSupplementary = array();
            if (isset($ai_code) && !empty($ai_code)) {

                $conditions [] = ['student_allotments.ai_code', '=', $ai_code];
                $conditions [] = ['student_allotments.supplementary', '=', 0];
                $conditions [] = ['students.is_eligible', '=', 1];
                $conditions [] = ['student_allotments.exam_year', '=', Config::get('global.current_admission_session_id')];
                $conditions [] = ['student_allotments.exam_month', '=', $stream];
                if (isset($course) && !empty($course)) {
                    $conditions['student_allotments.course'] = $course;
                    $conditionSupplementary[] = ['student_allotments.course', '=', $course];
                }
                $aiCenterDetail = AicenterDetail::where('ai_code', $ai_code)->first();

            } else {
                return redirect()->route('hall_ticket_form')->with('error', 'Oop"s! Did you really think you are allowed to see that?');
            }

            /* Suplementary Data Start  */
            $conditionSupplementary [] = ['student_allotments.ai_code', '=', $ai_code];
            $conditionSupplementary [] = ['student_allotments.exam_year', '=', config::get('global.admission_academicyear_id')];
            $conditionSupplementary [] = ['student_allotments.supplementary', '=', 1];
            $conditionSupplementary [] = ['student_allotments.exam_month', '=', $stream];
            $conditionSupplementary [] = ['supplementaries.is_eligible', '=', 1];
            $conditionSupplementary [] = ['supplementaries.exam_year', '=', config::get('global.admission_academicyear_id')];
            $conditionSupplementary [] = ['supplementaries.exam_month', '=', $stream];


            $suppStudents = array();
            $suppStudents = StudentAllotment::select('students.id', 'students.ai_code', 'students.enrollment', 'students.name', 'students.father_name', 'student_allotments.student_id',
                'students.mother_name', 'students.mobile', 'students.name_hi', 'students.stream',
                'applications.category_a', 'students.dob', 'students.course',
                'addresses.district_name', 'addresses.tehsil_name',
                'documents.photograph', 'documents.signature',
                'examcenter_details.ecenter10', 'examcenter_details.ecenter12',
                'examcenter_details.cent_name', 'examcenter_details.cent_add1', 'examcenter_details.cent_add2',
                'examcenter_details.cent_add3')
                ->join('students', 'students.id', '=', 'student_allotments.student_id')
                ->join('documents', 'documents.student_id', '=', 'student_allotments.student_id')
                ->join('addresses', 'addresses.student_id', '=', 'student_allotments.student_id')
                ->join('applications', 'applications.student_id', '=', 'student_allotments.student_id')
                ->join('supplementaries', 'supplementaries.student_id', '=', 'student_allotments.student_id')
                ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                ->where($conditionSupplementary)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('addresses.deleted_at')->whereNull('applications.deleted_at')->whereNull('supplementaries.deleted_at')->whereNull('examcenter_details.deleted_at')
                ->orderBy('student_allotments.student_id', 'ASC')
                ->groupBy('student_allotments.student_id')
                ->get();

            /* Suplementary Data End */


            /* Student  Data Start */
            $students = array();

            $students = StudentAllotment::select(
                'students.id', 'students.ai_code', 'students.enrollment', 'students.name', 'students.father_name',
                'student_allotments.student_id',
                'students.mother_name', 'students.mobile', 'students.name_hi', 'students.stream',
                'applications.category_a', 'students.dob', 'students.course',
                'addresses.district_name', 'addresses.tehsil_name',
                'documents.photograph', 'documents.signature',
                'examcenter_details.ecenter10', 'examcenter_details.ecenter12',
                'examcenter_details.cent_name', 'examcenter_details.cent_add1', 'examcenter_details.cent_add2',
                'examcenter_details.cent_add3',

            )
                ->join('applications', 'applications.student_id', '=', 'student_allotments.student_id')
                ->join('documents', 'documents.student_id', '=', 'student_allotments.student_id')
                ->join('addresses', 'addresses.student_id', '=', 'student_allotments.student_id')
                ->join('students', 'students.id', '=', 'student_allotments.student_id')
                ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                ->where($conditions)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('documents.deleted_at')->whereNull('addresses.deleted_at')->whereNull('applications.deleted_at')->whereNull('examcenter_details.deleted_at')
                ->groupBy('student_allotments.student_id')
                ->orderBy('student_allotments.student_id', 'ASC')->get();

            /* Student  Data End */
            /* Master data get end */


            /* Supplementray Data Set in Array End */
            $dataSave = array();
            $key = 0;

            if (isset($suppStudents) && !empty($suppStudents)) {

                foreach ($suppStudents as $suppKey => $suppStudent) {

                    $dataSave[$suppStudent->course][$key]['index'] = $suppKey;

                    $ai_code = @$suppStudent->ai_code;
                    $dataSave[$suppStudent->course][$key]['type'] = 'Supplementary';

                    $dataSave[$suppStudent->course][$key]['id'] = $suppStudent->id;
                    $dataSave[$suppStudent->course][$key]['student_id'] = $suppStudent->student_id;

                    $dataSave[$suppStudent->course][$key]['ai_code'] = $suppStudent->ai_code;
                    $dataSave[$suppStudent->course][$key]['enrollment'] = $suppStudent->enrollment;

                    $dataSave[$suppStudent->course][$key]['name'] = $suppStudent->name;
                    $dataSave[$suppStudent->course][$key]['father_name'] = $suppStudent->father_name;
                    $dataSave[$suppStudent->course][$key]['mother_name'] = $suppStudent->mother_name;
                    //pr($suppStudent);die;
                    $dataSave[$suppStudent->course][$key]['category_a'] = $suppStudent->category_a;

                    if (isset($suppStudent->course) && !empty($suppStudent->course)) {
                        $dataSave[$suppStudent->course][$key]['course'] = $suppStudent->course;
                    } else {
                        $dataSave[$suppStudent->course][$key]['course'] = array();
                    }
                    if (isset($suppStudent->dob) && !empty($suppStudent->dob)) {
                        if (strpos($suppStudent->dob, '-')) {
                            $ndobarr = explode('-', $suppStudent->dob);
                            $ndob = $ndobarr[2] . "-" . $ndobarr[1] . "-" . $ndobarr[0];
                            $dataSave[$suppStudent->course][$key]['dob'] = $ndob;
                        } else {
                            $dataSave[$suppStudent->course][$key]['dob'] = $suppStudent->dob;
                        }
                    } else {
                        $dataSave[$suppStudent->course][$key]['dob'] = array();
                    }
                    $dataSave[$suppStudent->course][$key]['stream'] = $suppStudent->stream;
                    $dataSave[$suppStudent->course][$key]['exam_subjects'] = null;
                    $dataSave[$suppStudent->course][$key]['exam_subjects'] = $this->getSubjectDetailForHallTicketSupp($suppStudent->student_id);

                    if (isset($suppStudent->photograph) && !empty($suppStudent->photograph)) {
                        $dataSave[$suppStudent->course][$key]['photograph'] = $suppStudent->photograph;
                        $dataSave[$suppStudent->course][$key]['signature'] = $suppStudent->signature;
                    } else {
                        $dataSave[$suppStudent->course][$key]['photograph'] = '';
                        $dataSave[$suppStudent->course][$key]['signature'] = '';
                    }

                    if (isset($suppStudent->district_name) && $suppStudent->district_name != '') {
                        $dataSave[$suppStudent->course][$key]['district'] = $suppStudent->district_name;
                    } else {
                        $dataSave[$suppStudent->course][$key]['district'] = array();
                    }

                    if (isset($suppStudent->tehsil_name) && $suppStudent->tehsil_name != '') {
                        $dataSave[$suppStudent->course][$key]['tehsil'] = $suppStudent->tehsil_name;
                    } else {
                        $dataSave[$suppStudent->course][$key]['tehsil'] = array();
                    }


                    $dataSave[$suppStudent->course][$key]['ecenter10'] = $suppStudent->ecenter10;
                    $dataSave[$suppStudent->course][$key]['ecenter12'] = $suppStudent->ecenter12;
                    $dataSave[$suppStudent->course][$key]['cent_name'] = $suppStudent->cent_name;
                    $dataSave[$suppStudent->course][$key]['cent_add1'] = $suppStudent->cent_add1;
                    $dataSave[$suppStudent->course][$key]['cent_add2'] = $suppStudent->cent_add2;
                    $dataSave[$suppStudent->course][$key]['cent_add3'] = $suppStudent->cent_add3;
                    $key++;
                }

            }
            /* Supplementray Data Set in Array End */

            /* Student Data Set in Array Start */
            if (isset($students) && !empty($students)) {
                foreach ($students as $stKey => $student) {

                    $ai_code = @$suppStudent->ai_code;
                    $dataSave[$student->course][$key]['type'] = 'Student';
                    $dataSave[$student->course][$key]['index'] = $stKey;
                    $dataSave[$student->course][$key]['id'] = $student->id;
                    $dataSave[$student->course][$key]['student_id'] = $student->student_id;
                    $dataSave[$student->course][$key]['ai_code'] = $student->ai_code;
                    $dataSave[$student->course][$key]['enrollment'] = $student->enrollment;
                    $dataSave[$student->course][$key]['name'] = $student->name;
                    $dataSave[$student->course][$key]['father_name'] = $student->father_name;
                    $dataSave[$student->course][$key]['mother_name'] = $student->mother_name;
                    $dataSave[$student->course][$key]['category_a'] = $student->category_a;

                    if (isset($student->course) && !empty($student->course)) {
                        $dataSave[$student->course][$key]['course'] = $student->course;
                    } else {
                        $dataSave[$student->course][$key]['course'] = array();
                    }

                    if (isset($student->dob) && !empty($student->dob)) {
                        if (strpos($student->dob, '-')) {
                            $ndobarr = explode('-', $student->dob);
                            $ndob = $ndobarr[2] . "-" . $ndobarr[1] . "-" . $ndobarr[0];
                            $dataSave[$student->course][$key]['dob'] = $ndob;
                        } else {
                            $dataSave[$student->course][$key]['dob'] = $student->dob;
                        }
                    } else {
                        $dataSave[$student->course][$key]['dob'] = array();
                    }

                    $dataSave[$student->course][$key]['stream'] = $student->stream;

                    $dataSave[$student->course][$key]['exam_subjects'] = $this->getSubjectDetailForHallTicket($student->student_id);

                    if (isset($student->photograph) && !empty($student->photograph)) {
                        $dataSave[$student->course][$key]['photograph'] = $student->photograph;
                        $dataSave[$student->course][$key]['signature'] = $student->signature;
                    } else {
                        $dataSave[$student->course][$key]['photograph'] = '';
                        $dataSave[$student->course][$key]['signature'] = '';
                    }
                    if (isset($student->district_name) && $student->district_name != '') {
                        $dataSave[$student->course][$key]['district'] = $student->district_name;
                    } else {
                        $dataSave[$student->course][$key]['district'] = array();
                    }
                    if (isset($student->tehsil_name) && $student->tehsil_name != '') {
                        $dataSave[$student->course][$key]['tehsil'] = $student->tehsil_name;
                    } else {
                        $dataSave[$student->course][$key]['tehsil'] = array();
                    }

                    $dataSave[$student->course][$key]['ecenter10'] = $student->ecenter10;
                    $dataSave[$student->course][$key]['ecenter12'] = $student->ecenter12;
                    $dataSave[$student->course][$key]['cent_name'] = $student->cent_name;
                    $dataSave[$student->course][$key]['cent_add1'] = $student->cent_add1;
                    $dataSave[$student->course][$key]['cent_add2'] = $student->cent_add2;
                    $dataSave[$student->course][$key]['cent_add3'] = $student->cent_add3;
                    $key++;
                }

            }

            /* Student Data Set in Array End */

            $current_year = @$current_folder_year[1];

            $combo_name = 'student_document_path';
            $student_document_path = $this->master_details($combo_name);
            $studentDocumentPath = $student_document_path[1];


            $students = $dataSave;


            //return view('examination_reports.hall_ticket_view',compact('current_year','stream','subjects','subjects10','studentDocumentPath','subjects12','practicalsubjects12','subject_list','categorya','aicode','students','subreportname','reportname','course','exam_session','exam_time_table_start_end_time'));


            $pdf = PDF::loadView('examination_reports.hall_ticket_view', compact('current_year', 'stream', 'subjects', 'subjects10', 'studentDocumentPath', 'subjects12', 'practicalsubjects12', 'subject_list', 'categorya', 'aicode', 'students', 'subreportname', 'reportname', 'course', 'exam_session', 'exam_time_table_start_end_time'));
            $pdf->setTimeout(2000);
            $path = public_path("files/reports/" . $current_year . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $aicodedistrictid1 . "/" . $aicodetemp . "/");
            File::makeDirectory($path, $mode = 0777, true, true);
            $pdf->setOption('footer-right', 'Page [page] of [toPage]');

            $filename = 'hallticket_' . $aicodetemp . '.pdf';
            $completepath = $path . $filename;
            if (File::exists($completepath)) {
                unlink($path . $filename);
            }
            $pdf->save($completepath, $pdf, true);
            //return $pdf->download($completepath);
            return (Response::download($completepath));


        }


    }

    public function hallticketbulkenrollmentviews()
    {
        $extraDesc = "Ai Center Material with 2 pdf will be generated as : 
    		AI Center Material (Course wise with stream and supplemenatary), 
    	 	Hall Ticket (Course wise with stream and supplemenatary)";
        $title = "Hall Ticket Nr Generate";
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => ''
            ),
            array(
                "label" => $title,
                'url' => 'hall_ticket_bulk_download'
            )
        );
        $user_role = Session::get('role_id');
        $ai_codes = Auth::user()->ai_code;
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $developeradminrole = Config::get("global.developer_admin");
        $superadminrole = Config::get("global.super_admin");
        $aicenterrole = Config::get("global.aicenter_id");
        return view('examination_reports.hallticketbulkviewsenrollment', compact('breadcrumbs', 'title', 'extraDesc', 'aiCenters', 'courses', 'stream_id', 'user_role', 'ai_codes', 'developeradminrole', 'superadminrole', 'aicenterrole'));
    }

    public function downloadhallticketbulviewpdfenrollment(Request $request)
    {

        $request->validate([
            'ai_code' => 'required',
            'stream' => 'required',
            'course' => 'required',
            'enrollment' => 'required',
        ]);


        return redirect()->route('hall_ticket_bulk_enrollmentdownload', array($request->course, $request->stream, $request->ai_code, $request->enrollment));
    }

    public function hall_ticket_bulk_enrollmentdownload($course = null, $stream = null, $ai_code = null, $enrollment = null)
    {

        $title = "Hall Ticket Report";
        $custom_component_obj = new CustomComponent;
        if (@$ai_code) {
            $aiCenters = $custom_component_obj->getAiCenters($ai_code);
        } elseif (@$ai_code == 0) {
            $aiCenters = $custom_component_obj->getAiCenters();

        }
        $subject_list = $this->subjectCodeList();
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'exam_time_table_start_end_time';
        $exam_time_table_start_end_time = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $subreportname = "Hall Ticket";
        $aicentermaterial = "aicentermaterial";
        $aicode = [];

        $subjects = DB::table('subjects')->where(array('deleted' => 0))->orderBy('subject_code')->pluck('subject_code', 'id');
        $subjects10 = DB::table('subjects')->where(array('deleted' => 0))->where(array('course' => 10))->orderBy('subject_code')->pluck('name', 'id');
        $subjects12 = DB::table('subjects')->where(array('deleted' => 0))->where(array('course' => 12))->orderBy('subject_code')->pluck('name', 'id');
        $practicalsubjects12 = DB::table('subjects')->where(array('deleted' => 0))->where(array('practical_type' => 1))->orderBy('subject_code')
            ->pluck('subject_code', 'id')->toArray();

        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);

        foreach ($aiCenters as $key => $value) {
            $aicodetemp = $key;
            $ai_code = $aicode = $key;
            $districtnmae = District::pluck('name', 'id');

            $aicodedistrictid = AicenterDetail::where('ai_code', $aicode)->groupBy('district_id')->get('district_id')->toarray();
            $aicodedistrictid1 = $districtnmae[$aicodedistrictid['0']['district_id']];
            //dd($ai_code);
            $reportname = $aicodetemp;

            /* Master data get start */
            $conditions = array();
            $conditionSupplementary = array();
            if (isset($ai_code) && !empty($ai_code)) {

                $conditions [] = ['student_allotments.ai_code', '=', $ai_code];
                $conditions [] = ['student_allotments.supplementary', '=', 0];
                $conditions [] = ['students.is_eligible', '=', 1];
                $conditions [] = ['student_allotments.enrollment', '=', $enrollment];
                $conditions [] = ['student_allotments.exam_year', '=', Config::get('global.current_admission_session_id')];
                $conditions [] = ['student_allotments.exam_month', '=', $stream];
                if (isset($course) && !empty($course)) {
                    $conditions['student_allotments.course'] = $course;
                    $conditionSupplementary[] = ['student_allotments.course', '=', $course];
                }
                $aiCenterDetail = AicenterDetail::where('ai_code', $ai_code)->first();

            } else {
                return redirect()->route('hall_ticket_form')->with('error', 'Oop"s! Did you really think you are allowed to see that?');
            }

            /* Suplementary Data Start  */
            $conditionSupplementary [] = ['student_allotments.ai_code', '=', $ai_code];
            $conditionSupplementary [] = ['student_allotments.exam_year', '=', config::get('global.admission_academicyear_id')];
            $conditionSupplementary [] = ['student_allotments.supplementary', '=', 1];
            $conditionSupplementary [] = ['student_allotments.exam_month', '=', $stream];
            $conditionSupplementary [] = ['supplementaries.is_eligible', '=', 1];
            $conditionSupplementary [] = ['student_allotments.enrollment', '=', $enrollment];
            $conditionSupplementary [] = ['supplementaries.exam_year', '=', config::get('global.admission_academicyear_id')];
            $conditionSupplementary [] = ['supplementaries.exam_month', '=', $stream];


            $suppStudents = array();
            $suppStudents = StudentAllotment::select('students.id', 'students.ai_code', 'students.enrollment', 'students.name', 'students.father_name', 'student_allotments.student_id',
                'students.mother_name', 'students.mobile', 'students.name_hi', 'students.stream',
                'applications.category_a', 'students.dob', 'students.course',
                'addresses.district_name', 'addresses.tehsil_name',
                'documents.photograph', 'documents.signature',
                'examcenter_details.ecenter10', 'examcenter_details.ecenter12',
                'examcenter_details.cent_name', 'examcenter_details.cent_add1', 'examcenter_details.cent_add2',
                'examcenter_details.cent_add3')
                ->join('students', 'students.id', '=', 'student_allotments.student_id')
                ->join('documents', 'documents.student_id', '=', 'student_allotments.student_id')
                ->join('addresses', 'addresses.student_id', '=', 'student_allotments.student_id')
                ->join('applications', 'applications.student_id', '=', 'student_allotments.student_id')
                ->join('supplementaries', 'supplementaries.student_id', '=', 'student_allotments.student_id')
                ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                ->where($conditionSupplementary)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('addresses.deleted_at')->whereNull('applications.deleted_at')->whereNull('supplementaries.deleted_at')->whereNull('examcenter_details.deleted_at')
                ->orderBy('student_allotments.student_id', 'ASC')
                ->groupBy('student_allotments.student_id')
                ->get();

            /* Suplementary Data End */


            /* Student  Data Start */
            $students = array();

            $students = StudentAllotment::select(
                'students.id', 'students.ai_code', 'students.enrollment', 'students.name', 'students.father_name', 'student_allotments.student_id',
                'students.mother_name', 'students.mobile', 'students.name_hi', 'students.stream',
                'applications.category_a', 'students.dob', 'students.course',
                'addresses.district_name', 'addresses.tehsil_name',
                'documents.photograph', 'documents.signature',
                'examcenter_details.ecenter10', 'examcenter_details.ecenter12',
                'examcenter_details.cent_name', 'examcenter_details.cent_add1', 'examcenter_details.cent_add2',
                'examcenter_details.cent_add3',

            )
                ->join('applications', 'applications.student_id', '=', 'student_allotments.student_id')
                ->join('documents', 'documents.student_id', '=', 'student_allotments.student_id')
                ->join('addresses', 'addresses.student_id', '=', 'student_allotments.student_id')
                ->join('students', 'students.id', '=', 'student_allotments.student_id')
                ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                ->where($conditions)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('documents.deleted_at')->whereNull('addresses.deleted_at')->whereNull('applications.deleted_at')->whereNull('examcenter_details.deleted_at')
                ->groupBy('student_allotments.student_id')
                ->orderBy('student_allotments.student_id', 'ASC')->get();

            /* Student  Data End */
            /* Master data get end */


            /* Supplementray Data Set in Array End */
            $dataSave = array();
            $key = 0;

            if (isset($suppStudents) && !empty($suppStudents)) {
                foreach ($suppStudents as $suppKey => $suppStudent) {

                    $dataSave[$suppStudent->course][$key]['index'] = $suppKey;

                    $ai_code = @$suppStudent->ai_code;
                    $dataSave[$suppStudent->course][$key]['type'] = 'Supplementary';

                    $dataSave[$suppStudent->course][$key]['id'] = $suppStudent->id;
                    $dataSave[$suppStudent->course][$key]['student_id'] = $suppStudent->student_id;

                    $dataSave[$suppStudent->course][$key]['ai_code'] = $suppStudent->ai_code;
                    $dataSave[$suppStudent->course][$key]['enrollment'] = $suppStudent->enrollment;

                    $dataSave[$suppStudent->course][$key]['name'] = $suppStudent->name;
                    $dataSave[$suppStudent->course][$key]['father_name'] = $suppStudent->father_name;
                    $dataSave[$suppStudent->course][$key]['mother_name'] = $suppStudent->mother_name;
                    //pr($suppStudent);die;
                    $dataSave[$suppStudent->course][$key]['category_a'] = $suppStudent->category_a;

                    if (isset($suppStudent->course) && !empty($suppStudent->course)) {
                        $dataSave[$suppStudent->course][$key]['course'] = $suppStudent->course;
                    } else {
                        $dataSave[$suppStudent->course][$key]['course'] = array();
                    }
                    if (isset($suppStudent->dob) && !empty($suppStudent->dob)) {
                        if (strpos($suppStudent->dob, '-')) {
                            $ndobarr = explode('-', $suppStudent->dob);
                            $ndob = $ndobarr[2] . "-" . $ndobarr[1] . "-" . $ndobarr[0];
                            $dataSave[$suppStudent->course][$key]['dob'] = $ndob;
                        } else {
                            $dataSave[$suppStudent->course][$key]['dob'] = $suppStudent->dob;
                        }
                    } else {
                        $dataSave[$suppStudent->course][$key]['dob'] = array();
                    }
                    $dataSave[$suppStudent->course][$key]['stream'] = $suppStudent->stream;
                    $dataSave[$suppStudent->course][$key]['exam_subjects'] = null;
                    $dataSave[$suppStudent->course][$key]['exam_subjects'] = $this->getSubjectDetailForHallTicketSupp($suppStudent->student_id);

                    if (isset($suppStudent->photograph) && !empty($suppStudent->photograph)) {
                        $dataSave[$suppStudent->course][$key]['photograph'] = $suppStudent->photograph;
                        $dataSave[$suppStudent->course][$key]['signature'] = $suppStudent->signature;
                    } else {
                        $dataSave[$suppStudent->course][$key]['photograph'] = '';
                        $dataSave[$suppStudent->course][$key]['signature'] = '';
                    }

                    if (isset($suppStudent->district_name) && $suppStudent->district_name != '') {
                        $dataSave[$suppStudent->course][$key]['district'] = $suppStudent->district_name;
                    } else {
                        $dataSave[$suppStudent->course][$key]['district'] = array();
                    }

                    if (isset($suppStudent->tehsil_name) && $suppStudent->tehsil_name != '') {
                        $dataSave[$suppStudent->course][$key]['district'] = $suppStudent->district_name;
                    } else {
                        $dataSave[$suppStudent->course][$key]['tehsil'] = array();
                    }
                    $dataSave[$suppStudent->course][$key]['ecenter10'] = $suppStudent->ecenter10;
                    $dataSave[$suppStudent->course][$key]['ecenter12'] = $suppStudent->ecenter12;
                    $dataSave[$suppStudent->course][$key]['cent_name'] = $suppStudent->cent_name;
                    $dataSave[$suppStudent->course][$key]['cent_add1'] = $suppStudent->cent_add1;
                    $dataSave[$suppStudent->course][$key]['cent_add2'] = $suppStudent->cent_add2;
                    $dataSave[$suppStudent->course][$key]['cent_add3'] = $suppStudent->cent_add3;
                    $key++;
                }

            }
            /* Supplementray Data Set in Array End */
            //dd($dataSave);

            /* Student Data Set in Array Start */
            if (isset($students) && !empty($students)) {
                foreach ($students as $stKey => $student) {

                    $ai_code = @$suppStudent->ai_code;
                    $dataSave[$student->course][$key]['type'] = 'Student';
                    $dataSave[$student->course][$key]['index'] = $stKey;
                    $dataSave[$student->course][$key]['id'] = $student->id;
                    $dataSave[$student->course][$key]['student_id'] = $student->student_id;
                    $dataSave[$student->course][$key]['ai_code'] = $student->ai_code;
                    $dataSave[$student->course][$key]['enrollment'] = $student->enrollment;
                    $dataSave[$student->course][$key]['name'] = $student->name;
                    $dataSave[$student->course][$key]['father_name'] = $student->father_name;
                    $dataSave[$student->course][$key]['mother_name'] = $student->mother_name;
                    $dataSave[$student->course][$key]['category_a'] = $student->category_a;

                    if (isset($student->course) && !empty($student->course)) {
                        $dataSave[$student->course][$key]['course'] = $student->course;
                    } else {
                        $dataSave[$student->course][$key]['course'] = array();
                    }
                    if (isset($student->dob) && !empty($student->dob)) {
                        if (strpos($student->dob, '-')) {
                            $ndobarr = explode('-', $student->dob);
                            $ndob = $ndobarr[2] . "-" . $ndobarr[1] . "-" . $ndobarr[0];
                            $dataSave[$student->course][$key]['dob'] = $ndob;
                        } else {
                            $dataSave[$student->course][$key]['dob'] = $student->dob;
                        }
                    } else {
                        $dataSave[$student->course][$key]['dob'] = array();
                    }
                    $dataSave[$student->course][$key]['stream'] = $student->stream;

                    $dataSave[$student->course][$key]['exam_subjects'] = $this->getSubjectDetailForHallTicket($student->student_id);

                    if (isset($student->photograph) && !empty($student->photograph)) {
                        $dataSave[$student->course][$key]['photograph'] = $student->photograph;
                        $dataSave[$student->course][$key]['signature'] = $student->signature;
                    } else {
                        $dataSave[$student->course][$key]['photograph'] = '';
                        $dataSave[$student->course][$key]['signature'] = '';
                    }
                    if (isset($student->district_name) && $student->district_name != '') {
                        $dataSave[$student->course][$key]['district'] = $student->district_name;
                    } else {
                        $dataSave[$student->course][$key]['district'] = array();
                    }
                    if (isset($student->tehsil_name) && $student->tehsil_name != '') {
                        $dataSave[$student->course][$key]['tehsil'] = $student->tehsil_name;
                    } else {
                        $dataSave[$student->course][$key]['tehsil'] = array();
                    }

                    $dataSave[$student->course][$key]['ecenter10'] = $student->ecenter10;
                    $dataSave[$student->course][$key]['ecenter12'] = $student->ecenter12;
                    $dataSave[$student->course][$key]['cent_name'] = $student->cent_name;
                    $dataSave[$student->course][$key]['cent_add1'] = $student->cent_add1;
                    $dataSave[$student->course][$key]['cent_add2'] = $student->cent_add2;
                    $dataSave[$student->course][$key]['cent_add3'] = $student->cent_add3;
                    $key++;
                }

            }


            /* Student Data Set in Array End */

            $current_year = @$current_folder_year[1];

            $combo_name = 'student_document_path';
            $student_document_path = $this->master_details($combo_name);
            $studentDocumentPath = $student_document_path[1];

            $students = $dataSave;


            //return view('examination_reports.hall_ticket_view',compact('current_year','stream','subjects','subjects10','studentDocumentPath','subjects12','practicalsubjects12','subject_list','categorya','aicode','students','subreportname','reportname','course','exam_session','exam_time_table_start_end_time'));


            $pdf = PDF::loadView('examination_reports.hall_ticket_view', compact('current_year', 'stream', 'subjects', 'subjects10', 'studentDocumentPath', 'subjects12', 'practicalsubjects12', 'subject_list', 'categorya', 'aicode', 'students', 'subreportname', 'reportname', 'course', 'exam_session', 'exam_time_table_start_end_time'));
            $pdf->setTimeout(60000);
            $pdf->setOption('footer-right', 'Page [page] of [toPage]');

            $filename = 'hallticket_' . $aicodetemp . '.pdf';
            return $pdf->download($filename);


        }
    }

    public function hall_ticket_single_enrollment_download(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'enrollment' => 'required|numeric|digits:11',
        ], [
            'enrollment.required' => 'Enrollment is Required.',
        ]);
        if ($validate->fails()) {
            return back()->withErrors($validate->errors())->withInput();
        }
        $ai_code = Auth::user()->ai_code;
        $stream = Config::get("global.current_exam_month_id");
        $exam_monthyear = Config::get('global.current_admission_session_id');
        $finedstudentallotment = StudentAllotment::where('ai_code', $ai_code)->where('enrollment', $request->enrollment)->where('exam_year', $exam_monthyear)->where('exam_month', $stream)->first('course');
        if (empty($finedstudentallotment)) {
            return Redirect::back()->with('error', 'Enrollment not Found');
        }
        $course = $finedstudentallotment->course;
        $title = "Hall Ticket Report";
        $custom_component_obj = new CustomComponent;
        if (@$ai_code) {
            $aiCenters = $custom_component_obj->getAiCenters($ai_code);
        } elseif (@$ai_code == 0) {
            $aiCenters = $custom_component_obj->getAiCenters();

        }
        $subject_list = $this->subjectCodeList();
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'exam_time_table_start_end_time';
        $exam_time_table_start_end_time = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $subreportname = "Hall Ticket";
        $aicentermaterial = "aicentermaterial";
        $aicode = [];

        $subjects = DB::table('subjects')->where(array('deleted' => 0))->orderBy('subject_code')->pluck('subject_code', 'id');
        $subjects10 = DB::table('subjects')->where(array('deleted' => 0))->where(array('course' => 10))->orderBy('subject_code')->pluck('name', 'id');
        $subjects12 = DB::table('subjects')->where(array('deleted' => 0))->where(array('course' => 12))->orderBy('subject_code')->pluck('name', 'id');
        $practicalsubjects12 = DB::table('subjects')->where(array('deleted' => 0))->where(array('practical_type' => 1))->orderBy('subject_code')
            ->pluck('subject_code', 'id')->toArray();

        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);

        foreach ($aiCenters as $key => $value) {
            $aicodetemp = $key;
            $ai_code = $aicode = $key;
            $districtnmae = District::pluck('name', 'id');

            $aicodedistrictid = AicenterDetail::where('ai_code', $aicode)->groupBy('district_id')->get('district_id')->toarray();
            $aicodedistrictid1 = $districtnmae[$aicodedistrictid['0']['district_id']];
            //dd($ai_code);
            $reportname = $aicodetemp;

            /* Master data get start */
            $conditions = array();
            $conditionSupplementary = array();
            if (isset($ai_code) && !empty($ai_code)) {

                $conditions [] = ['student_allotments.ai_code', '=', $ai_code];
                $conditions [] = ['student_allotments.supplementary', '=', 0];
                $conditions [] = ['students.is_eligible', '=', 1];
                $conditions [] = ['student_allotments.enrollment', '=', $request->enrollment];
                $conditions [] = ['student_allotments.exam_year', '=', Config::get('global.current_admission_session_id')];
                $conditions [] = ['student_allotments.exam_month', '=', $stream];
                $aiCenterDetail = AicenterDetail::where('ai_code', $ai_code)->first();

            } else {
                return redirect()->route('hall_ticket_form')->with('error', 'Oop"s! Did you really think you are allowed to see that?');
            }

            /* Suplementary Data Start  */
            $conditionSupplementary [] = ['student_allotments.ai_code', '=', $ai_code];
            $conditionSupplementary [] = ['student_allotments.exam_year', '=', config::get('global.admission_academicyear_id')];
            $conditionSupplementary [] = ['student_allotments.supplementary', '=', 1];
            $conditionSupplementary [] = ['student_allotments.exam_month', '=', $stream];
            $conditionSupplementary [] = ['supplementaries.is_eligible', '=', 1];
            $conditionSupplementary [] = ['student_allotments.enrollment', '=', $request->enrollment];
            $conditionSupplementary [] = ['supplementaries.exam_year', '=', config::get('global.admission_academicyear_id')];
            $conditionSupplementary [] = ['supplementaries.exam_month', '=', $stream];


            $suppStudents = array();
            $suppStudents = StudentAllotment::select('students.id', 'students.ai_code', 'students.enrollment', 'students.name', 'students.father_name', 'student_allotments.student_id',
                'students.mother_name', 'students.mobile', 'students.name_hi', 'students.stream',
                'applications.category_a', 'students.dob', 'students.course',
                'addresses.district_name', 'addresses.tehsil_name',
                'documents.photograph', 'documents.signature',
                'examcenter_details.ecenter10', 'examcenter_details.ecenter12',
                'examcenter_details.cent_name', 'examcenter_details.cent_add1', 'examcenter_details.cent_add2',
                'examcenter_details.cent_add3')
                ->join('students', 'students.id', '=', 'student_allotments.student_id')
                ->join('documents', 'documents.student_id', '=', 'student_allotments.student_id')
                ->join('addresses', 'addresses.student_id', '=', 'student_allotments.student_id')
                ->join('applications', 'applications.student_id', '=', 'student_allotments.student_id')
                ->join('supplementaries', 'supplementaries.student_id', '=', 'student_allotments.student_id')
                ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                ->where($conditionSupplementary)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('addresses.deleted_at')->whereNull('applications.deleted_at')->whereNull('supplementaries.deleted_at')->whereNull('examcenter_details.deleted_at')
                ->orderBy('student_allotments.student_id', 'ASC')
                ->groupBy('student_allotments.student_id')
                ->get();


            /* Suplementary Data End */


            /* Student  Data Start */
            $students = array();

            $students = StudentAllotment::select(
                'students.id', 'students.ai_code', 'students.enrollment', 'students.name', 'students.father_name', 'student_allotments.student_id',
                'students.mother_name', 'students.mobile', 'students.name_hi', 'students.stream',
                'applications.category_a', 'students.dob', 'students.course',
                'addresses.district_name', 'addresses.tehsil_name',
                'documents.photograph', 'documents.signature',
                'examcenter_details.ecenter10', 'examcenter_details.ecenter12',
                'examcenter_details.cent_name', 'examcenter_details.cent_add1', 'examcenter_details.cent_add2',
                'examcenter_details.cent_add3',

            )
                ->join('applications', 'applications.student_id', '=', 'student_allotments.student_id')
                ->join('documents', 'documents.student_id', '=', 'student_allotments.student_id')
                ->join('addresses', 'addresses.student_id', '=', 'student_allotments.student_id')
                ->join('students', 'students.id', '=', 'student_allotments.student_id')
                ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                ->where($conditions)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('documents.deleted_at')->whereNull('addresses.deleted_at')->whereNull('applications.deleted_at')->whereNull('examcenter_details.deleted_at')
                ->groupBy('student_allotments.student_id')
                ->orderBy('student_allotments.student_id', 'ASC')->get();


            /* Student  Data End */
            /* Master data get end */


            /* Supplementray Data Set in Array End */
            $dataSave = array();
            $key = 0;

            if (isset($suppStudents) && !empty($suppStudents)) {
                foreach ($suppStudents as $suppKey => $suppStudent) {

                    $dataSave[$suppStudent->course][$key]['index'] = $suppKey;

                    $ai_code = @$suppStudent->ai_code;
                    $dataSave[$suppStudent->course][$key]['type'] = 'Supplementary';

                    $dataSave[$suppStudent->course][$key]['id'] = $suppStudent->id;
                    $dataSave[$suppStudent->course][$key]['student_id'] = $suppStudent->student_id;

                    $dataSave[$suppStudent->course][$key]['ai_code'] = $suppStudent->ai_code;
                    $dataSave[$suppStudent->course][$key]['enrollment'] = $suppStudent->enrollment;

                    $dataSave[$suppStudent->course][$key]['name'] = $suppStudent->name;
                    $dataSave[$suppStudent->course][$key]['father_name'] = $suppStudent->father_name;
                    $dataSave[$suppStudent->course][$key]['mother_name'] = $suppStudent->mother_name;
                    //pr($suppStudent);die;
                    $dataSave[$suppStudent->course][$key]['category_a'] = $suppStudent->category_a;

                    if (isset($suppStudent->course) && !empty($suppStudent->course)) {
                        $dataSave[$suppStudent->course][$key]['course'] = $suppStudent->course;
                    } else {
                        $dataSave[$suppStudent->course][$key]['course'] = array();
                    }
                    if (isset($suppStudent->dob) && !empty($suppStudent->dob)) {
                        if (strpos($suppStudent->dob, '-')) {
                            $ndobarr = explode('-', $suppStudent->dob);
                            $ndob = $ndobarr[2] . "-" . $ndobarr[1] . "-" . $ndobarr[0];
                            $dataSave[$suppStudent->course][$key]['dob'] = $ndob;
                        } else {
                            $dataSave[$suppStudent->course][$key]['dob'] = $suppStudent->dob;
                        }
                    } else {
                        $dataSave[$suppStudent->course][$key]['dob'] = array();
                    }
                    $dataSave[$suppStudent->course][$key]['stream'] = $suppStudent->stream;
                    $dataSave[$suppStudent->course][$key]['exam_subjects'] = null;
                    $dataSave[$suppStudent->course][$key]['exam_subjects'] = $this->getSubjectDetailForHallTicketSupp($suppStudent->student_id);

                    if (isset($suppStudent->photograph) && !empty($suppStudent->photograph)) {
                        $dataSave[$suppStudent->course][$key]['photograph'] = $suppStudent->photograph;
                        $dataSave[$suppStudent->course][$key]['signature'] = $suppStudent->signature;
                    } else {
                        $dataSave[$suppStudent->course][$key]['photograph'] = '';
                        $dataSave[$suppStudent->course][$key]['signature'] = '';
                    }

                    if (isset($suppStudent->district_name) && $suppStudent->district_name != '') {
                        $dataSave[$suppStudent->course][$key]['district'] = $suppStudent->district_name;
                    } else {
                        $dataSave[$suppStudent->course][$key]['district'] = array();
                    }

                    if (isset($suppStudent->tehsil_name) && $suppStudent->tehsil_name != '') {
                        $dataSave[$suppStudent->course][$key]['district'] = $suppStudent->district_name;
                    } else {
                        $dataSave[$suppStudent->course][$key]['tehsil'] = array();
                    }
                    $dataSave[$suppStudent->course][$key]['ecenter10'] = $suppStudent->ecenter10;
                    $dataSave[$suppStudent->course][$key]['ecenter12'] = $suppStudent->ecenter12;
                    $dataSave[$suppStudent->course][$key]['cent_name'] = $suppStudent->cent_name;
                    $dataSave[$suppStudent->course][$key]['cent_add1'] = $suppStudent->cent_add1;
                    $dataSave[$suppStudent->course][$key]['cent_add2'] = $suppStudent->cent_add2;
                    $dataSave[$suppStudent->course][$key]['cent_add3'] = $suppStudent->cent_add3;
                    $key++;
                }

            }
            /* Supplementray Data Set in Array End */
            //dd($dataSave);

            /* Student Data Set in Array Start */
            if (isset($students) && !empty($students)) {
                foreach ($students as $stKey => $student) {

                    $ai_code = @$suppStudent->ai_code;
                    $dataSave[$student->course][$key]['type'] = 'Student';
                    $dataSave[$student->course][$key]['index'] = $stKey;
                    $dataSave[$student->course][$key]['id'] = $student->id;
                    $dataSave[$student->course][$key]['student_id'] = $student->student_id;
                    $dataSave[$student->course][$key]['ai_code'] = $student->ai_code;
                    $dataSave[$student->course][$key]['enrollment'] = $student->enrollment;
                    $dataSave[$student->course][$key]['name'] = $student->name;
                    $dataSave[$student->course][$key]['father_name'] = $student->father_name;
                    $dataSave[$student->course][$key]['mother_name'] = $student->mother_name;
                    $dataSave[$student->course][$key]['category_a'] = $student->category_a;

                    if (isset($student->course) && !empty($student->course)) {
                        $dataSave[$student->course][$key]['course'] = $student->course;
                    } else {
                        $dataSave[$student->course][$key]['course'] = array();
                    }
                    if (isset($student->dob) && !empty($student->dob)) {
                        if (strpos($student->dob, '-')) {
                            $ndobarr = explode('-', $student->dob);
                            $ndob = $ndobarr[2] . "-" . $ndobarr[1] . "-" . $ndobarr[0];
                            $dataSave[$student->course][$key]['dob'] = $ndob;
                        } else {
                            $dataSave[$student->course][$key]['dob'] = $student->dob;
                        }
                    } else {
                        $dataSave[$student->course][$key]['dob'] = array();
                    }
                    $dataSave[$student->course][$key]['stream'] = $student->stream;

                    $dataSave[$student->course][$key]['exam_subjects'] = $this->getSubjectDetailForHallTicket($student->student_id);

                    if (isset($student->photograph) && !empty($student->photograph)) {
                        $dataSave[$student->course][$key]['photograph'] = $student->photograph;
                        $dataSave[$student->course][$key]['signature'] = $student->signature;
                    } else {
                        $dataSave[$student->course][$key]['photograph'] = '';
                        $dataSave[$student->course][$key]['signature'] = '';
                    }
                    if (isset($student->district_name) && $student->district_name != '') {
                        $dataSave[$student->course][$key]['district'] = $student->district_name;
                    } else {
                        $dataSave[$student->course][$key]['district'] = array();
                    }
                    if (isset($student->tehsil_name) && $student->tehsil_name != '') {
                        $dataSave[$student->course][$key]['tehsil'] = $student->tehsil_name;
                    } else {
                        $dataSave[$student->course][$key]['tehsil'] = array();
                    }

                    $dataSave[$student->course][$key]['ecenter10'] = $student->ecenter10;
                    $dataSave[$student->course][$key]['ecenter12'] = $student->ecenter12;
                    $dataSave[$student->course][$key]['cent_name'] = $student->cent_name;
                    $dataSave[$student->course][$key]['cent_add1'] = $student->cent_add1;
                    $dataSave[$student->course][$key]['cent_add2'] = $student->cent_add2;
                    $dataSave[$student->course][$key]['cent_add3'] = $student->cent_add3;
                    $key++;
                }

            }


            /* Student Data Set in Array End */

            $current_year = @$current_folder_year[1];

            $combo_name = 'student_document_path';
            $student_document_path = $this->master_details($combo_name);
            $studentDocumentPath = $student_document_path[1];

            $students = $dataSave;


            //return view('examination_reports.hall_ticket_view',compact('current_year','stream','subjects','subjects10','studentDocumentPath','subjects12','practicalsubjects12','subject_list','categorya','aicode','students','subreportname','reportname','course','exam_session','exam_time_table_start_end_time'));


            $pdf = PDF::loadView('examination_reports.hall_ticket_view', compact('current_year', 'stream', 'subjects', 'subjects10', 'studentDocumentPath', 'subjects12', 'practicalsubjects12', 'subject_list', 'categorya', 'aicode', 'students', 'subreportname', 'reportname', 'course', 'exam_session', 'exam_time_table_start_end_time'));
            $pdf->setTimeout(60000);
            $pdf->setOption('footer-right', 'Page [page] of [toPage]');

            $filename = 'hallticket_' . $aicodetemp . '.pdf';
            return $pdf->download($filename);


        }
    }

    public function hall_ticket_bulk_downloads_all($course = null, $stream = null, $ai_code = null)
    {
        $title = "Hall Ticket Report";

        $custom_component_obj = new CustomComponent;
        if (@$ai_code) {
            $aiCenters = $custom_component_obj->getAiCenters($ai_code);
        } elseif (@$ai_code == 0) {
            $aiCenters = $custom_component_obj->getAiCenters();

        }

        $subject_list = $this->subjectCodeList();
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'exam_time_table_start_end_time';
        $exam_time_table_start_end_time = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $subreportname = "Hall Ticket";
        $aicentermaterial = "aicentermaterial";
        $aicode = [];

        $subjects = DB::table('subjects')->where(array('deleted' => 0))->orderBy('subject_code')->pluck('subject_code', 'id');
        $subjects10 = DB::table('subjects')->where(array('deleted' => 0))->where(array('course' => 10))->orderBy('subject_code')->pluck('name', 'id');
        $subjects12 = DB::table('subjects')->where(array('deleted' => 0))->where(array('course' => 12))->orderBy('subject_code')->pluck('name', 'id');
        $practicalsubjects12 = DB::table('subjects')->where(array('deleted' => 0))->where(array('practical_type' => 1))->orderBy('subject_code')
            ->pluck('subject_code', 'id')->toArray();

        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);

        foreach ($aiCenters as $key => $value) {
            $aicodetemp = $key;
            $ai_code = $aicode = $key;
            $districtnmae = District::pluck('name', 'id');

            $aicodedistrictid = AicenterDetail::where('ai_code', $aicode)->groupBy('district_id')->get('district_id')->toarray();

            $aicodedistrictid1 = $districtnmae[$aicodedistrictid['0']['district_id']];
            //dd($ai_code);
            $reportname = $aicodetemp;

            /* Master data get start */
            $conditions = array();
            $conditionSupplementary = array();
            if (isset($ai_code) && !empty($ai_code)) {

                $conditions [] = ['student_allotments.ai_code', '=', $ai_code];
                $conditions [] = ['student_allotments.supplementary', '=', 0];
                $conditions [] = ['students.is_eligible', '=', 1];
                $conditions [] = ['student_allotments.exam_year', '=', Config::get('global.current_admission_session_id')];
                $conditions [] = ['student_allotments.exam_month', '=', $stream];
                if (isset($course) && !empty($course)) {
                    $conditions['student_allotments.course'] = $course;
                    $conditionSupplementary[] = ['student_allotments.course', '=', $course];
                }
                $aiCenterDetail = AicenterDetail::where('ai_code', $ai_code)->first();

            } else {
                return redirect()->route('hall_ticket_form')->with('error', 'Oop"s! Did you really think you are allowed to see that?');
            }

            /* Suplementary Data Start  */
            $conditionSupplementary [] = ['student_allotments.ai_code', '=', $ai_code];
            $conditionSupplementary [] = ['student_allotments.exam_year', '=', config::get('global.admission_academicyear_id')];
            $conditionSupplementary [] = ['student_allotments.supplementary', '=', 1];
            $conditionSupplementary [] = ['student_allotments.exam_month', '=', $stream];
            $conditionSupplementary [] = ['supplementaries.is_eligible', '=', 1];
            $conditionSupplementary [] = ['supplementaries.exam_year', '=', config::get('global.admission_academicyear_id')];
            $conditionSupplementary [] = ['supplementaries.exam_month', '=', $stream];


            $suppStudents = array();
            $suppStudents = StudentAllotment::select('students.id', 'students.ai_code', 'students.enrollment', 'students.name', 'students.father_name', 'student_allotments.student_id',
                'students.mother_name', 'students.mobile', 'students.name_hi', 'students.stream',
                'applications.category_a', 'students.dob', 'students.course',
                'addresses.district_name', 'addresses.tehsil_name',
                'documents.photograph', 'documents.signature',
                'examcenter_details.ecenter10', 'examcenter_details.ecenter12',
                'examcenter_details.cent_name', 'examcenter_details.cent_add1', 'examcenter_details.cent_add2',
                'examcenter_details.cent_add3')
                ->join('students', 'students.id', '=', 'student_allotments.student_id')
                ->join('documents', 'documents.student_id', '=', 'student_allotments.student_id')
                ->join('addresses', 'addresses.student_id', '=', 'student_allotments.student_id')
                ->join('applications', 'applications.student_id', '=', 'student_allotments.student_id')
                ->join('supplementaries', 'supplementaries.student_id', '=', 'student_allotments.student_id')
                ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                ->where($conditionSupplementary)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('addresses.deleted_at')->whereNull('applications.deleted_at')->whereNull('supplementaries.deleted_at')->whereNull('examcenter_details.deleted_at')
                ->orderBy('student_allotments.student_id', 'ASC')
                ->groupBy('student_allotments.student_id')
                ->get();

            /* Suplementary Data End */


            /* Student  Data Start */
            $students = array();

            $students = StudentAllotment::select(
                'students.id', 'students.ai_code', 'students.enrollment', 'students.name', 'students.father_name', 'student_allotments.student_id',
                'students.mother_name', 'students.mobile', 'students.name_hi', 'students.stream',
                'applications.category_a', 'students.dob', 'students.course',
                'addresses.district_name', 'addresses.tehsil_name',
                'documents.photograph', 'documents.signature',
                'examcenter_details.ecenter10', 'examcenter_details.ecenter12',
                'examcenter_details.cent_name', 'examcenter_details.cent_add1', 'examcenter_details.cent_add2',
                'examcenter_details.cent_add3',

            )
                ->join('applications', 'applications.student_id', '=', 'student_allotments.student_id')
                ->join('documents', 'documents.student_id', '=', 'student_allotments.student_id')
                ->join('addresses', 'addresses.student_id', '=', 'student_allotments.student_id')
                ->join('students', 'students.id', '=', 'student_allotments.student_id')
                ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                ->where($conditions)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('documents.deleted_at')->whereNull('addresses.deleted_at')->whereNull('applications.deleted_at')->whereNull('examcenter_details.deleted_at')
                ->groupBy('student_allotments.student_id')
                ->orderBy('student_allotments.student_id', 'ASC')->get();

            /* Student  Data End */
            /* Master data get end */


            /* Supplementray Data Set in Array End */
            $dataSave = array();
            $key = 0;

            if (isset($suppStudents) && !empty($suppStudents)) {
                foreach ($suppStudents as $suppKey => $suppStudent) {
                    $dataSave[$suppStudent->course][$key]['index'] = $suppKey;

                    $ai_code = @$suppStudent->ai_code;
                    $dataSave[$suppStudent->course][$key]['type'] = 'Supplementary';

                    $dataSave[$suppStudent->course][$key]['id'] = $suppStudent->id;
                    $dataSave[$suppStudent->course][$key]['student_id'] = $suppStudent->student_id;

                    $dataSave[$suppStudent->course][$key]['ai_code'] = $suppStudent->ai_code;
                    $dataSave[$suppStudent->course][$key]['enrollment'] = $suppStudent->enrollment;

                    $dataSave[$suppStudent->course][$key]['name'] = $suppStudent->name;
                    $dataSave[$suppStudent->course][$key]['father_name'] = $suppStudent->father_name;
                    $dataSave[$suppStudent->course][$key]['mother_name'] = $suppStudent->mother_name;
                    //pr($suppStudent);die;
                    $dataSave[$suppStudent->course][$key]['category_a'] = $suppStudent->category_a;

                    if (isset($suppStudent->course) && !empty($suppStudent->course)) {
                        $dataSave[$suppStudent->course][$key]['course'] = $suppStudent->course;
                    } else {
                        $dataSave[$suppStudent->course][$key]['course'] = array();
                    }
                    if (isset($suppStudent->dob) && !empty($suppStudent->dob)) {
                        if (strpos($suppStudent->dob, '-')) {
                            $ndobarr = explode('-', $suppStudent->dob);
                            $ndob = $ndobarr[2] . "-" . $ndobarr[1] . "-" . $ndobarr[0];
                            $dataSave[$suppStudent->course][$key]['dob'] = $ndob;
                        } else {
                            $dataSave[$suppStudent->course][$key]['dob'] = $suppStudent->dob;
                        }
                    } else {
                        $dataSave[$suppStudent->course][$key]['dob'] = array();
                    }
                    $dataSave[$suppStudent->course][$key]['stream'] = $suppStudent->stream;
                    $dataSave[$suppStudent->course][$key]['exam_subjects'] = null;
                    $dataSave[$suppStudent->course][$key]['exam_subjects'] = $this->getSubjectDetailForHallTicketSupp($suppStudent->student_id);

                    if (isset($suppStudent->photograph) && !empty($suppStudent->photograph)) {
                        $dataSave[$suppStudent->course][$key]['photograph'] = $suppStudent->photograph;
                        $dataSave[$suppStudent->course][$key]['signature'] = $suppStudent->signature;
                    } else {
                        $dataSave[$suppStudent->course][$key]['photograph'] = '';
                        $dataSave[$suppStudent->course][$key]['signature'] = '';
                    }

                    if (isset($suppStudent->district_name) && $suppStudent->district_name != '') {
                        $dataSave[$suppStudent->course][$key]['district'] = $suppStudent->district_name;
                    } else {
                        $dataSave[$suppStudent->course][$key]['district'] = array();
                    }
                    if (isset($suppStudent->tehsil_name) && $suppStudent->tehsil_name != '') {
                        $dataSave[$suppStudent->course][$key]['tehsil'] = $suppStudent->tehsil_name;
                    } else {
                        $dataSave[$suppStudent->course][$key]['tehsil'] = array();
                    }

                    $dataSave[$suppStudent->course][$key]['ecenter10'] = $suppStudent->ecenter10;
                    $dataSave[$suppStudent->course][$key]['ecenter12'] = $suppStudent->ecenter12;
                    $dataSave[$suppStudent->course][$key]['cent_name'] = $suppStudent->cent_name;
                    $dataSave[$suppStudent->course][$key]['cent_add1'] = $suppStudent->cent_add1;
                    $dataSave[$suppStudent->course][$key]['cent_add2'] = $suppStudent->cent_add2;
                    $dataSave[$suppStudent->course][$key]['cent_add3'] = $suppStudent->cent_add3;
                    $key++;
                }

            }
            /* Supplementray Data Set in Array End */
            //dd($dataSave);

            /* Student Data Set in Array Start */
            if (isset($students) && !empty($students)) {
                foreach ($students as $stKey => $student) {

                    $ai_code = @$suppStudent->ai_code;
                    $dataSave[$student->course][$key]['type'] = 'Student';
                    $dataSave[$student->course][$key]['index'] = $stKey;
                    $dataSave[$student->course][$key]['id'] = $student->id;
                    $dataSave[$student->course][$key]['student_id'] = $student->student_id;
                    $dataSave[$student->course][$key]['ai_code'] = $student->ai_code;
                    $dataSave[$student->course][$key]['enrollment'] = $student->enrollment;
                    $dataSave[$student->course][$key]['name'] = $student->name;
                    $dataSave[$student->course][$key]['father_name'] = $student->father_name;
                    $dataSave[$student->course][$key]['mother_name'] = $student->mother_name;
                    $dataSave[$student->course][$key]['category_a'] = $student->category_a;

                    if (isset($student->course) && !empty($student->course)) {
                        $dataSave[$student->course][$key]['course'] = $student->course;
                    } else {
                        $dataSave[$student->course][$key]['course'] = array();
                    }
                    if (isset($student->dob) && !empty($student->dob)) {
                        if (strpos($student->dob, '-')) {
                            $ndobarr = explode('-', $student->dob);
                            $ndob = $ndobarr[2] . "-" . $ndobarr[1] . "-" . $ndobarr[0];
                            $dataSave[$student->course][$key]['dob'] = $ndob;
                        } else {
                            $dataSave[$student->course][$key]['dob'] = $student->dob;
                        }
                    } else {
                        $dataSave[$student->course][$key]['dob'] = array();
                    }
                    $dataSave[$student->course][$key]['stream'] = $student->stream;

                    $dataSave[$student->course][$key]['exam_subjects'] = $this->getSubjectDetailForHallTicket($student->student_id);

                    if (isset($student->photograph) && !empty($student->photograph)) {
                        $dataSave[$student->course][$key]['photograph'] = $student->photograph;
                        $dataSave[$student->course][$key]['signature'] = $student->signature;
                    } else {
                        $dataSave[$student->course][$key]['photograph'] = '';
                        $dataSave[$student->course][$key]['signature'] = '';
                    }
                    if (isset($student->district_name) && $student->district_name != '') {
                        $dataSave[$student->course][$key]['district'] = $student->district_name;
                    } else {
                        $dataSave[$student->course][$key]['district'] = array();
                    }
                    if (isset($student->tehsil_name) && $student->tehsil_name != '') {
                        $dataSave[$student->course][$key]['tehsil'] = $student->tehsil_name;
                    } else {
                        $dataSave[$student->course][$key]['tehsil'] = array();
                    }

                    $dataSave[$student->course][$key]['ecenter10'] = $student->ecenter10;
                    $dataSave[$student->course][$key]['ecenter12'] = $student->ecenter12;
                    $dataSave[$student->course][$key]['cent_name'] = $student->cent_name;
                    $dataSave[$student->course][$key]['cent_add1'] = $student->cent_add1;
                    $dataSave[$student->course][$key]['cent_add2'] = $student->cent_add2;
                    $dataSave[$student->course][$key]['cent_add3'] = $student->cent_add3;
                    $key++;
                }

            }

            /* Student Data Set in Array End */

            $current_year = @$current_folder_year[1];

            $combo_name = 'student_document_path';
            $student_document_path = $this->master_details($combo_name);
            $studentDocumentPath = $student_document_path[1];


            $students = $dataSave;


            //return view('examination_reports.hall_ticket_view',compact('current_year','stream','subjects','subjects10','studentDocumentPath','subjects12','practicalsubjects12','subject_list','categorya','aicode','students','subreportname','reportname','course','exam_session','exam_time_table_start_end_time'));


            $pdf = PDF::loadView('examination_reports.hall_ticket_view', compact('current_year', 'stream', 'subjects', 'subjects10', 'studentDocumentPath', 'subjects12', 'practicalsubjects12', 'subject_list', 'categorya', 'aicode', 'students', 'subreportname', 'reportname', 'course', 'exam_session', 'exam_time_table_start_end_time'));
            $path = public_path("files/reports/" . $current_year . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $aicodedistrictid1 . "/" . $aicodetemp . "/");
            File::makeDirectory($path, $mode = 0777, true, true);
            $pdf->setOption('footer-right', 'Page [page] of [toPage]');

            $filename = 'hallticket_' . $aicodetemp . '.pdf';
            $completepath = $path . $filename;

            $pdf->save($completepath, $pdf, true);
            //return( Response::download( $completepath ) );


        }

        echo "Today is Done " . date("Y/m/d") . "<br>";
    }

    public function hall_ticket_bulk_downloads_district($course = null, $stream = null, $district = null)
    {
        $title = "Hall Ticket Report";

        $custom_component_obj = new CustomComponent;
        $examyearcurrent = CustomHelper::_get_selected_sessions();
        if ($district) {
            $aiCenters = $custom_component_obj->getAiCentersByDistrictId($district);
        }
        $subject_list = $this->subjectCodeList($course);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'exam_time_table_start_end_time';
        $exam_time_table_start_end_time = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $subreportname = "Hall Ticket";
        $aicentermaterial = "aicentermaterial";
        $aicode = [];

        $subjects = Subject::where(array('deleted' => 0))->orderBy('subject_code')->pluck('subject_code', 'id');
        $subjects10 = Subject::where(array('deleted' => 0))->where(array('course' => 10))->orderBy('subject_code')->pluck('name', 'id');
        $subjects12 = Subject::where(array('deleted' => 0))->where(array('course' => 12))->orderBy('subject_code')->pluck('name', 'id');
        $practicalsubjects12 = Subject::where(array('deleted' => 0))->where(array('practical_type' => 1))->orderBy('subject_code')
            ->pluck('subject_code', 'id')->toArray();
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $districtnmae = $this->districtsByState(6);

        foreach ($aiCenters as $key => $value) {
            $aicodetemp = $key;
            $ai_code = $aicode = $key;

            $aicodedistrictid = AicenterDetail::where('ai_code', $aicode)->groupBy('district_id')->get('district_id')->toarray();
            $aicodedistrictid1 = $districtnmae[$aicodedistrictid['0']['district_id']];
            //dd($ai_code);
            $reportname = $aicodetemp;

            /* Master data get start */
            $conditions = array();
            $conditionSupplementary = array();
            if (isset($ai_code) && !empty($ai_code)) {

                $conditions [] = ['student_allotments.ai_code', '=', $ai_code];
                $conditions [] = ['student_allotments.supplementary', '=', 0];
                $conditions [] = ['students.is_eligible', '=', 1];
                $conditions [] = ['student_allotments.exam_year', '=', Config::get('global.current_admission_session_id')];
                $conditions [] = ['student_allotments.exam_month', '=', $stream];
                if (isset($course) && !empty($course)) {
                    $conditions['student_allotments.course'] = $course;
                    $conditionSupplementary[] = ['student_allotments.course', '=', $course];
                }
                $aiCenterDetail = AicenterDetail::where('ai_code', $ai_code)->first();

            } else {
                return redirect()->route('hall_ticket_form')->with('error', 'Oop"s! Did you really think you are allowed to see that?');
            }

            /* Suplementary Data Start  */
            $conditionSupplementary [] = ['student_allotments.ai_code', '=', $ai_code];
            $conditionSupplementary [] = ['student_allotments.exam_year', '=', config::get('global.admission_academicyear_id')];
            $conditionSupplementary [] = ['student_allotments.supplementary', '=', 1];
            $conditionSupplementary [] = ['student_allotments.exam_month', '=', $stream];
            $conditionSupplementary [] = ['supplementaries.is_eligible', '=', 1];
            $conditionSupplementary [] = ['supplementaries.exam_year', '=', config::get('global.admission_academicyear_id')];
            $conditionSupplementary [] = ['supplementaries.exam_month', '=', $stream];


            $suppStudents = array();
            $suppStudents = StudentAllotment::select('students.id', 'students.ai_code', 'students.enrollment', 'students.name', 'students.father_name', 'student_allotments.student_id',
                'students.mother_name', 'students.mobile', 'students.name_hi', 'students.stream',
                'applications.category_a', 'students.dob', 'students.course',
                'addresses.district_name', 'addresses.tehsil_name',
                'documents.photograph', 'documents.signature',
                'examcenter_details.ecenter10', 'examcenter_details.ecenter12',
                'examcenter_details.cent_name', 'examcenter_details.cent_add1', 'examcenter_details.cent_add2',
                'examcenter_details.cent_add3')
                ->join('students', 'students.id', '=', 'student_allotments.student_id')
                ->join('documents', 'documents.student_id', '=', 'student_allotments.student_id')
                ->join('addresses', 'addresses.student_id', '=', 'student_allotments.student_id')
                ->join('applications', 'applications.student_id', '=', 'student_allotments.student_id')
                ->join('supplementaries', 'supplementaries.student_id', '=', 'student_allotments.student_id')
                ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                ->where($conditionSupplementary)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('addresses.deleted_at')->whereNull('applications.deleted_at')->whereNull('supplementaries.deleted_at')->whereNull('examcenter_details.deleted_at')
                ->orderBy('student_allotments.student_id', 'ASC')
                ->groupBy('student_allotments.student_id')
                ->get();

            /* Suplementary Data End */


            /* Student  Data Start */
            $students = array();

            $students = StudentAllotment::select(
                'students.id', 'students.ai_code', 'students.enrollment', 'students.name', 'students.father_name',
                'student_allotments.student_id',
                'students.mother_name', 'students.mobile', 'students.name_hi', 'students.stream',
                'applications.category_a', 'students.dob', 'students.course',
                'addresses.district_name', 'addresses.tehsil_name',
                'documents.photograph', 'documents.signature',
                'examcenter_details.ecenter10', 'examcenter_details.ecenter12',
                'examcenter_details.cent_name', 'examcenter_details.cent_add1', 'examcenter_details.cent_add2',
                'examcenter_details.cent_add3',

            )
                ->join('applications', 'applications.student_id', '=', 'student_allotments.student_id')
                ->join('documents', 'documents.student_id', '=', 'student_allotments.student_id')
                ->join('addresses', 'addresses.student_id', '=', 'student_allotments.student_id')
                ->join('students', 'students.id', '=', 'student_allotments.student_id')
                ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                ->where($conditions)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('documents.deleted_at')->whereNull('addresses.deleted_at')->whereNull('applications.deleted_at')->whereNull('examcenter_details.deleted_at')
                ->groupBy('student_allotments.student_id')
                ->orderBy('student_allotments.student_id', 'ASC')->get();

            /* Student  Data End */
            /* Master data get end */


            /* Supplementray Data Set in Array End */
            $dataSave = array();
            $key = 0;

            if (isset($suppStudents) && !empty($suppStudents)) {

                foreach ($suppStudents as $suppKey => $suppStudent) {

                    $dataSave[$suppStudent->course][$key]['index'] = $suppKey;

                    $ai_code = @$suppStudent->ai_code;
                    $dataSave[$suppStudent->course][$key]['type'] = 'Supplementary';

                    $dataSave[$suppStudent->course][$key]['id'] = $suppStudent->id;
                    $dataSave[$suppStudent->course][$key]['student_id'] = $suppStudent->student_id;

                    $dataSave[$suppStudent->course][$key]['ai_code'] = $suppStudent->ai_code;
                    $dataSave[$suppStudent->course][$key]['enrollment'] = $suppStudent->enrollment;

                    $dataSave[$suppStudent->course][$key]['name'] = $suppStudent->name;
                    $dataSave[$suppStudent->course][$key]['father_name'] = $suppStudent->father_name;
                    $dataSave[$suppStudent->course][$key]['mother_name'] = $suppStudent->mother_name;
                    //pr($suppStudent);die;
                    $dataSave[$suppStudent->course][$key]['category_a'] = $suppStudent->category_a;

                    if (isset($suppStudent->course) && !empty($suppStudent->course)) {
                        $dataSave[$suppStudent->course][$key]['course'] = $suppStudent->course;
                    } else {
                        $dataSave[$suppStudent->course][$key]['course'] = array();
                    }
                    if (isset($suppStudent->dob) && !empty($suppStudent->dob)) {
                        if (strpos($suppStudent->dob, '-')) {
                            $ndobarr = explode('-', $suppStudent->dob);
                            $ndob = $ndobarr[2] . "-" . $ndobarr[1] . "-" . $ndobarr[0];
                            $dataSave[$suppStudent->course][$key]['dob'] = $ndob;
                        } else {
                            $dataSave[$suppStudent->course][$key]['dob'] = $suppStudent->dob;
                        }
                    } else {
                        $dataSave[$suppStudent->course][$key]['dob'] = array();
                    }
                    $dataSave[$suppStudent->course][$key]['stream'] = $suppStudent->stream;
                    $dataSave[$suppStudent->course][$key]['exam_subjects'] = null;
                    $dataSave[$suppStudent->course][$key]['exam_subjects'] = $this->getSubjectDetailForHallTicketSupp($suppStudent->student_id);

                    if (isset($suppStudent->photograph) && !empty($suppStudent->photograph)) {
                        $dataSave[$suppStudent->course][$key]['photograph'] = $suppStudent->photograph;
                        $dataSave[$suppStudent->course][$key]['signature'] = $suppStudent->signature;
                    } else {
                        $dataSave[$suppStudent->course][$key]['photograph'] = '';
                        $dataSave[$suppStudent->course][$key]['signature'] = '';
                    }

                    if (isset($suppStudent->district_name) && $suppStudent->district_name != '') {
                        $dataSave[$suppStudent->course][$key]['district'] = $suppStudent->district_name;
                    } else {
                        $dataSave[$suppStudent->course][$key]['district'] = array();
                    }

                    if (isset($suppStudent->tehsil_name) && $suppStudent->tehsil_name != '') {
                        $dataSave[$suppStudent->course][$key]['tehsil'] = $suppStudent->tehsil_name;
                    } else {
                        $dataSave[$suppStudent->course][$key]['tehsil'] = array();
                    }


                    $dataSave[$suppStudent->course][$key]['ecenter10'] = $suppStudent->ecenter10;
                    $dataSave[$suppStudent->course][$key]['ecenter12'] = $suppStudent->ecenter12;
                    $dataSave[$suppStudent->course][$key]['cent_name'] = $suppStudent->cent_name;
                    $dataSave[$suppStudent->course][$key]['cent_add1'] = $suppStudent->cent_add1;
                    $dataSave[$suppStudent->course][$key]['cent_add2'] = $suppStudent->cent_add2;
                    $dataSave[$suppStudent->course][$key]['cent_add3'] = $suppStudent->cent_add3;
                    $key++;
                }

            }
            /* Supplementray Data Set in Array End */

            /* Student Data Set in Array Start */
            if (isset($students) && !empty($students)) {
                foreach ($students as $stKey => $student) {

                    $ai_code = @$suppStudent->ai_code;
                    $dataSave[$student->course][$key]['type'] = 'Student';
                    $dataSave[$student->course][$key]['index'] = $stKey;
                    $dataSave[$student->course][$key]['id'] = $student->id;
                    $dataSave[$student->course][$key]['student_id'] = $student->student_id;
                    $dataSave[$student->course][$key]['ai_code'] = $student->ai_code;
                    $dataSave[$student->course][$key]['enrollment'] = $student->enrollment;
                    $dataSave[$student->course][$key]['name'] = $student->name;
                    $dataSave[$student->course][$key]['father_name'] = $student->father_name;
                    $dataSave[$student->course][$key]['mother_name'] = $student->mother_name;
                    $dataSave[$student->course][$key]['category_a'] = $student->category_a;

                    if (isset($student->course) && !empty($student->course)) {
                        $dataSave[$student->course][$key]['course'] = $student->course;
                    } else {
                        $dataSave[$student->course][$key]['course'] = array();
                    }

                    if (isset($student->dob) && !empty($student->dob)) {
                        if (strpos($student->dob, '-')) {
                            $ndobarr = explode('-', $student->dob);
                            $ndob = $ndobarr[2] . "-" . $ndobarr[1] . "-" . $ndobarr[0];
                            $dataSave[$student->course][$key]['dob'] = $ndob;
                        } else {
                            $dataSave[$student->course][$key]['dob'] = $student->dob;
                        }
                    } else {
                        $dataSave[$student->course][$key]['dob'] = array();
                    }

                    $dataSave[$student->course][$key]['stream'] = $student->stream;

                    $dataSave[$student->course][$key]['exam_subjects'] = $this->getSubjectDetailForHallTicket($student->student_id);

                    if (isset($student->photograph) && !empty($student->photograph)) {
                        $dataSave[$student->course][$key]['photograph'] = $student->photograph;
                        $dataSave[$student->course][$key]['signature'] = $student->signature;
                    } else {
                        $dataSave[$student->course][$key]['photograph'] = '';
                        $dataSave[$student->course][$key]['signature'] = '';
                    }
                    if (isset($student->district_name) && $student->district_name != '') {
                        $dataSave[$student->course][$key]['district'] = $student->district_name;
                    } else {
                        $dataSave[$student->course][$key]['district'] = array();
                    }
                    if (isset($student->tehsil_name) && $student->tehsil_name != '') {
                        $dataSave[$student->course][$key]['tehsil'] = $student->tehsil_name;
                    } else {
                        $dataSave[$student->course][$key]['tehsil'] = array();
                    }

                    $dataSave[$student->course][$key]['ecenter10'] = $student->ecenter10;
                    $dataSave[$student->course][$key]['ecenter12'] = $student->ecenter12;
                    $dataSave[$student->course][$key]['cent_name'] = $student->cent_name;
                    $dataSave[$student->course][$key]['cent_add1'] = $student->cent_add1;
                    $dataSave[$student->course][$key]['cent_add2'] = $student->cent_add2;
                    $dataSave[$student->course][$key]['cent_add3'] = $student->cent_add3;
                    $key++;
                }

            }

            /* Student Data Set in Array End */

            $current_year = @$current_folder_year[1];

            $combo_name = 'student_document_path';
            $student_document_path = $this->master_details($combo_name);
            $studentDocumentPath = $student_document_path[1];


            $students = $dataSave;


            //return view('examination_reports.hall_ticket_view',compact('current_year','stream','subjects','subjects10','studentDocumentPath','subjects12','practicalsubjects12','subject_list','categorya','aicode','students','subreportname','reportname','course','exam_session','exam_time_table_start_end_time'));


            $pdf = PDF::loadView('examination_reports.hall_ticket_view', compact('current_year', 'stream', 'subjects', 'subjects10', 'studentDocumentPath', 'subjects12', 'practicalsubjects12', 'subject_list', 'categorya', 'aicode', 'students', 'subreportname', 'reportname', 'course', 'exam_session', 'exam_time_table_start_end_time'));
            $pdf->setTimeout(2000);
            $path = public_path("files/reports/" . $current_year . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $aicodedistrictid1 . "/" . $aicodetemp . "/");
            File::makeDirectory($path, $mode = 0777, true, true);
            $pdf->setOption('footer-right', 'Page [page] of [toPage]');

            $filename = 'hallticket_' . $aicodetemp . '.pdf';
            $completepath = $path . $filename;
            if (File::exists($completepath)) {
                unlink($path . $filename);
            }
            $pdf->save($completepath, $pdf, true);

            //return $pdf->download($completepath); //return( Response::download( $completepath ) );
        }
        echo "Today is Done " . date("Y/m/d") . "<br>";
    }

    public function downloadstudentchecklistsPdf1(Request $request)
    {

        $request->validate([
            'ai_code' => 'required',
            'stream' => 'required',
            'course' => 'required',
        ]);

        return redirect()->route('downloadstudentchecklistsPdf', array($request->course, $request->stream, $request->ai_code));
    }

    public function downloadstudentchecklistsPdf($course = null, $stream = null, $ai_code = null, Request $request)
    {
        ini_set('memory_limit', '10000M');
        ini_set('max_execution_time', '0');
        $custom_component_obj = new CustomComponent;
        if (@$ai_code) {
            $aiCenters = $custom_component_obj->getAiCenters($ai_code);
        } elseif (@$ai_code == 0) {
            $aiCenters = $custom_component_obj->getAiCenters();
        } elseif (@$ai_code == "custom") {
            $aiCenters = $custom_component_obj->getAiCenters("custom");
        }
        $title = "Student checklist Report";
        $table_id = "Student_Checklists_Report";
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $genders = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'category_b';
        $category_b = $this->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $this->master_details($combo_name);
        $combo_name = 'year';
        $year = $this->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $this->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $this->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);
        $combo_name = 'minage';
        $minage = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        //$rsos_years = $this->getListRsosYears();
        $combo_name = "year";
        $rsos_years = $this->master_details($combo_name);
        $rsos_yearsstudent = $this->rsos_years();
        $tocpassyear = DB::table('rsos_years')->pluck('yearstext', 'id');
        $tocpassfail = DB::table('rsos_years_fail')->pluck('yearstext', 'id');


        $subjectCodes = $this->subjectCodeList($course);
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1];

        $boards = $this->getBoardList();
        // $boards = $this->getRsosYearsList();
        $aicode = [];
        $checklist = "checklist";
        foreach ($aiCenters as $key => $value) {
            @$aicodetemp = $key;
            @$aicode = $key;
            $conditions = array();
            $conditions["students.ai_code"] = $aicode;
            $conditions["students.stream"] = $stream;
            $conditions["students.course"] = $course;
            $conditions["students.is_eligible"] = 1;
            $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();
            @$reportname = $aicodetemp;

            $master = Student::
            with('application', 'document', 'address', 'toc', 'studentfees', 'admission_subject', 'toc_subject', 'exam_subject')
                ->where($conditions)->orderBy('enrollment', 'ASC')
                ->get(['students.id', 'students.submitted', 'students.name', 'students.father_name', 'students.mother_name', 'students.gender_id', 'students.dob',
                    'students.enrollment', 'students.adm_type', 'students.stream', 'students.course', 'students.ai_code']);

            // return view('examination_reports.student_checklists_pdf2',compact('categorya','midium','genders','subjectCodes','rsos_years','master','studentDocumentPath','ai_code','courses','course','boards','adm_types','stream','reportname','rsos_yearsstudent','tocpassyear','tocpassfail'));
            //student_checklists_pdf2
            $pdf = PDF::loadView('examination_reports.student_checklists_pdf2', compact('categorya', 'midium', 'genders', 'subjectCodes', 'rsos_years', 'master', 'studentDocumentPath', 'ai_code', 'courses', 'course', 'boards', 'adm_types', 'stream', 'reportname', 'rsos_yearsstudent', 'tocpassyear', 'tocpassfail'));
            $pdf->setTimeout(20000);
            $pdf->setOption('footer-right', 'Page [page] of [toPage]');
            $path = public_path("files/reports/" . $current_folder_year[1] . "/" . $checklist . "/stream" . $stream . "/" . $course . "/");
            File::makeDirectory($path, $mode = 0777, true, true);
            if (@$aicode) {
                $filename = $aicode . "_checklist.pdf";
            } else {
                $filename = $aicode . "_checklist.pdf";
            }

            $completepath = $path . $filename;
            $pdf->save($completepath, $pdf, true);
        }

        if (@$ai_code) {
            return redirect()->route('studentchecklists')->with('message', 'Student Checklists Generated successfully');
            //return( Response::download($completepath));
        } elseif (@$ai_code == 0) {
            /*$zip_file_name = $course . "_" .  "stream".$stream .  "_studentchecklist.zip";
		 $folder_path = "files/reports/" . $current_folder_year[1]."/". $checklist ."/". "stream" . $stream ."/". $course;
		 $folder_path = public_path($folder_path);
		 $zip_file = $this->_zipAndDownload($folder_path,$zip_file_name);
		 return response()->download($zip_file);*/
            return redirect()->route('studentchecklists')->with('message', 'Student Checklists Generated successfully');
        }

    }

    public function exam_center_nominal_roll_pdf($course = null, $stream = null, $districtid = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $custom_component_obj = new CustomComponent;
        if (@$districtid == 0) {
            $districtNameList = $this->districtsByState($state_id);
        } else {
            $districtNameList = $this->districtNameById($districtid);
        }
        $subject_list = $this->subjectCodeList();
        $exam_year = CustomHelper::_get_selected_sessions();
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $current_admission_session_id = CustomHelper::_get_selected_sessions();
        $title = "Student Roll Wise Report";
        $aicentermaterial = "examcentermaterial";
        $current_folder_year = $this->master_details('current_folder_year');
        $current_exam_month_id = $stream;
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);

        $current_year = @$current_folder_year[1];
        $custom_component_obj = new CustomComponent;

        $centercode = 'ecenter' . $course;
        $courseid = $course;
        $centerName = 'cent_name';
        $aiCenters = $custom_component_obj->getAiCenters();

        foreach ($districtNameList as $districtid => $districtvalue) {
            $centercodeandids = ExamcenterDetail::where('district_id', $districtid)->where('active', 1)->pluck($centercode, 'id')->toArray();

            $centercodeandNames = ExamcenterDetail::where('district_id', $districtid)->where('active', 1)->pluck($centerName, 'id')->toArray();

            //here change in single code
            foreach ($centercodeandids as $centerid => $centervalue) {
                //agin check and need to remove again find query
                $districtNameListid = ExamcenterDetail::where($centercode, $centervalue)->where('active', 1)->groupBy('district_id')->get('district_id')->toarray();
                $districtNameListname = @$districtNameList[$districtNameListid['0']['district_id']];
                $final_data = array();
                $i = 0;
                $aicodes = [];
                foreach ($aiCenters as $aicode => $value) {
                    @$aicodes = $aicode;
                    $conditions = null;
                    $conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
                    $conditions["student_allotments.exam_month"] = $stream;
                    $conditions["student_allotments.course"] = $course;
                    $conditions["student_allotments.supplementary"] = 0;
                    $conditions["student_allotments.examcenter_detail_id"] = $centerid;
                    $conditions["student_allotments.ai_code"] = $aicode;

                    $supp_conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
                    $supp_conditions["student_allotments.exam_month"] = $stream;
                    $supp_conditions["student_allotments.course"] = $course;
                    $supp_conditions["student_allotments.supplementary"] = 1;
                    $supp_conditions["student_allotments.examcenter_detail_id"] = $centerid;
                    $supp_conditions["student_allotments.ai_code"] = $aicode;


                    $supp_master = StudentAllotment::with('student.application', 'examcenter')->with('Supplementary', function ($query) use ($exam_year, $stream) {
                        $query->where('is_eligible', 1)->whereNull('deleted_at')->where('exam_year', $exam_year)->where('exam_month', $stream);
                    })->with('student', function ($query) {
                        $query->whereNull('deleted_at');
                    })->with('Supplementarysubjects', function ($query) {
                        $query->orderBy('student_id', 'DESC')->whereNull('deleted_at');
                    })->where($supp_conditions)->whereNull('deleted_at')->orderBy('enrollment', 'ASC')->get()->toArray();


                    $master = StudentAllotment::with('student', 'student.application', 'examcenter')->with('examsubject', function ($query) {
                        $query->orderBy('student_id', 'DESC')->whereNull('deleted_at');
                    })->with('student', function ($query) {
                        $query->where('is_eligible', 1)->whereNull('deleted_at');
                    })->whereNull('deleted_at')->where($conditions)->orderBy('enrollment', 'ASC')->get()->toArray();


                    if (count($master) > 0 || count($supp_master) > 0) {

                    } else {
                        continue;
                    }

                    $finalArr = array();
                    @$index = 0;

                    foreach ($supp_master as $student) {
                        $fld = "is_supp";
                        @$finalArr[$index][$fld] = true;
                        $fld = "enrollment";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "ecenter10";
                        @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                        $fld = "cent_name";
                        @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                        $fld = "ai_code";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "ecenter12";
                        @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                        $fld = "name";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "father_name";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "mother_name";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "dob";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "category_a";
                        @$finalArr[$index][$fld] = $student['student'][0]['application'][$fld];
                        $fld = "examsubject";
                        @$finalArr[$index][$fld] = $student['supplementarysubjects'];
                        @$index++;
                    }

                    foreach ($master as $student) {
                        $fld = "is_supp";
                        @$finalArr[$index][$fld] = false;
                        $fld = "enrollment";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "ai_code";
                        @$finalArr[$index][$fld] = $student[$fld];
                        $fld = "ecenter10";
                        @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                        $fld = "cent_name";
                        @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                        $fld = "ecenter12";
                        @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                        $fld = "name";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "father_name";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "mother_name";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "dob";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "category_a";
                        @$finalArr[$index][$fld] = $student['student'][0]['application'][$fld];
                        $fld = "examsubject";
                        @$finalArr[$index][$fld] = $student[$fld];
                        @$index++;
                    }


                    $final_data[$i]['aicode'] = $aicode;
                    $final_data[$i]['cent_code'] = $centervalue;
                    $final_data[$i]['cent_name'] = @$centercodeandNames[@$centerid];
                    $final_data[$i]['examsubject'] = $finalArr;
                    $i++;
                }


                //return view('examination_reports.exam_center_nominal_roll_pdf',compact('title','stream','master','aicode','centercodeandNames','centervalue','exam_session','centerid','course','aiCenters','categorya','final_data'));


                $pdf = PDF::loadView('examination_reports.exam_center_nominal_roll_pdf', compact('title', 'stream', 'master', 'aicode', 'centercodeandNames', 'centervalue', 'exam_session', 'centerid', 'course', 'aiCenters', 'categorya', 'final_data', 'subject_list', 'aicodes'));

                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $districtNameListname . "/" . $centervalue . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');
                $filename = 'examcenter_nominalroll_' . $centervalue . '.pdf';
                $completepath = $path . $filename;
                $pdf->save($completepath, $pdf, true);
                //return(Response::download( $completepath));

            }
        }
    }

    public function single_exam_center_nominal_roll_pdf_request(Request $request)
    {

        $request->validate([
            'course' => 'required',
            'stream' => 'required',
            'district_id' => 'required',
            'ecenter' => 'required',
        ]);

        return redirect()->route('single_exam_center_nominal_roll_pdf', array($request->course, $request->stream, $request->district_id, $request->ecenter));
    }

    public function single_exam_center_nominal_roll_pdf($course = null, $stream = null, $districtid = null, $ecenter = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $custom_component_obj = new CustomComponent;
        if (@$districtid == 0) {
            $districtNameList = $this->districtsByState($state_id);
        } else {
            $districtNameList = $this->districtNameById($districtid);
        }
        $subject_list = $this->subjectCodeList();
        $exam_year = CustomHelper::_get_selected_sessions();
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $current_admission_session_id = CustomHelper::_get_selected_sessions();
        $title = "Student Roll Wise Report";
        $aicentermaterial = "examcentermaterial";
        $current_folder_year = $this->master_details('current_folder_year');
        $current_exam_month_id = $stream;
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);

        $current_year = @$current_folder_year[1];
        $custom_component_obj = new CustomComponent;

        $centercode = 'ecenter' . $course;
        $courseid = $course;
        $centerName = 'cent_name';
        $aiCenters = $custom_component_obj->getAiCenters();

        foreach ($districtNameList as $districtid => $districtvalue) {
            $centercodeandids = ExamcenterDetail::where($centercode, $ecenter)->where('active', 1)->pluck($centercode, 'id')->toArray();

            $centercodeandNames = ExamcenterDetail::where($centercode, $ecenter)->where('active', 1)->pluck($centerName, 'id')->toArray();

            //here change in single code
            foreach ($centercodeandids as $centerid => $centervalue) {
                //agin check and need to remove again find query
                $districtNameListid = ExamcenterDetail::where($centercode, $centervalue)->where('active', 1)->groupBy('district_id')->get('district_id')->toarray();
                $districtNameListname = @$districtNameList[$districtNameListid['0']['district_id']];
                $final_data = array();
                $i = 0;
                $aicodes = [];
                foreach ($aiCenters as $aicode => $value) {
                    @$aicodes = $aicode;
                    $conditions = null;
                    $conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
                    $conditions["student_allotments.exam_month"] = $stream;
                    $conditions["student_allotments.course"] = $course;
                    $conditions["student_allotments.supplementary"] = 0;
                    $conditions["student_allotments.examcenter_detail_id"] = $centerid;
                    $conditions["student_allotments.ai_code"] = $aicode;

                    $supp_conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
                    $supp_conditions["student_allotments.exam_month"] = $stream;
                    $supp_conditions["student_allotments.course"] = $course;
                    $supp_conditions["student_allotments.supplementary"] = 1;
                    $supp_conditions["student_allotments.examcenter_detail_id"] = $centerid;
                    $supp_conditions["student_allotments.ai_code"] = $aicode;


                    $supp_master = StudentAllotment::with('student.application', 'examcenter')->with('Supplementary', function ($query) use ($exam_year, $stream) {
                        $query->where('is_eligible', 1)->whereNull('deleted_at')->where('exam_year', $exam_year)->where('exam_month', $stream);
                    })->with('student', function ($query) {
                        $query->whereNull('deleted_at');
                    })->with('Supplementarysubjects', function ($query) {
                        $query->orderBy('student_id', 'DESC')->whereNull('deleted_at');
                    })->where($supp_conditions)->whereNull('deleted_at')->orderBy('enrollment', 'ASC')->get()->toArray();


                    $master = StudentAllotment::with('student', 'student.application', 'examcenter')->with('examsubject', function ($query) {
                        $query->orderBy('student_id', 'DESC')->whereNull('deleted_at');
                    })->with('student', function ($query) {
                        $query->where('is_eligible', 1)->whereNull('deleted_at');
                    })->whereNull('deleted_at')->where($conditions)->orderBy('enrollment', 'ASC')->get()->toArray();


                    if (count($master) > 0 || count($supp_master) > 0) {

                    } else {
                        continue;
                    }

                    $finalArr = array();
                    @$index = 0;

                    foreach ($supp_master as $student) {
                        $fld = "is_supp";
                        @$finalArr[$index][$fld] = true;
                        $fld = "enrollment";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "ecenter10";
                        @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                        $fld = "cent_name";
                        @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                        $fld = "ai_code";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "ecenter12";
                        @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                        $fld = "name";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "father_name";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "mother_name";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "dob";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "category_a";
                        @$finalArr[$index][$fld] = $student['student'][0]['application'][$fld];
                        $fld = "examsubject";
                        @$finalArr[$index][$fld] = $student['supplementarysubjects'];
                        @$index++;
                    }

                    foreach ($master as $student) {
                        $fld = "is_supp";
                        @$finalArr[$index][$fld] = false;
                        $fld = "enrollment";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "ai_code";
                        @$finalArr[$index][$fld] = $student[$fld];
                        $fld = "ecenter10";
                        @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                        $fld = "cent_name";
                        @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                        $fld = "ecenter12";
                        @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                        $fld = "name";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "father_name";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "mother_name";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "dob";
                        @$finalArr[$index][$fld] = $student['student'][0][$fld];
                        $fld = "category_a";
                        @$finalArr[$index][$fld] = $student['student'][0]['application'][$fld];
                        $fld = "examsubject";
                        @$finalArr[$index][$fld] = $student[$fld];
                        @$index++;
                    }


                    $final_data[$i]['aicode'] = $aicode;
                    $final_data[$i]['cent_code'] = $centervalue;
                    $final_data[$i]['cent_name'] = @$centercodeandNames[@$centerid];
                    $final_data[$i]['examsubject'] = $finalArr;
                    $i++;
                }


                //return view('examination_reports.exam_center_nominal_roll_pdf',compact('title','stream','master','aicode','centercodeandNames','centervalue','exam_session','centerid','course','aiCenters','categorya','final_data'));


                $pdf = PDF::loadView('examination_reports.exam_center_nominal_roll_pdf', compact('title', 'stream', 'master', 'aicode', 'centercodeandNames', 'centervalue', 'exam_session', 'centerid', 'course', 'aiCenters', 'categorya', 'final_data', 'subject_list', 'aicodes'));

                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $districtNameListname . "/" . $centervalue . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');
                $filename = 'examcenter_nominalroll_' . $centervalue . '.pdf';
                $completepath = $path . $filename;
                $pdf->save($completepath, $pdf, true);
                return (Response::download($completepath));

            }
        }
    }

    public function oldexam_center_nominal_roll_pdf($course = null, $stream = null, $districtid = null)
    {

        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $custom_component_obj = new CustomComponent;
        if (@$districtid == 0) {
            $districtNameList = $this->districtsByState($state_id);
        } else {
            $districtNameList = $this->districtNameById($districtid);
        }
        $exam_year = CustomHelper::_get_selected_sessions();
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $current_admission_session_id = CustomHelper::_get_selected_sessions();
        $title = "Student Roll Wise Report";
        $aicentermaterial = "examcentermaterial";
        $current_folder_year = $this->master_details('current_folder_year');
        $current_exam_month_id = $stream;

        $current_year = @$current_folder_year[1];
        $custom_component_obj = new CustomComponent;

        $centercode = 'ecenter' . $course;
        $courseid = $course;
        $centerName = 'cent_name';

        foreach ($districtNameList as $districtid => $districtvalue) {
            $centercodeandids = ExamcenterDetail::where('district_id', $districtid)->where('active', 1)->pluck($centercode, 'id')->toArray();

            $centercodeandNames = ExamcenterDetail::where('district_id', $districtid)->where('active', 1)->pluck($centerName, 'id')->toArray();
            //here change in single code
            foreach ($centercodeandids as $centerid => $centervalue) {
                //agin check and need to remove again find query
                $districtNameListid = ExamcenterDetail::where($centercode, $centervalue)->where('active', 1)->groupBy('district_id')->get('district_id')->toarray();
                $districtNameListname = @$districtNameList[$districtNameListid['0']['district_id']];

                // $conditions['student_allotments.academic_session'] = $current_admission_session_id;
                $conditions['student_allotments.stream'] = $stream;
                $conditions['student_allotments.examcenter_detail_id'] = $centerid;
                $conditions['student_allotments.course'] = $course;

                $students_data = StudentAllotment::select('student_allotments.ai_code as sa_ai_code', 'student_allotments.enrollment as sa_enrollment', 'students.id as s_id', 'students.ai_code as s_ai_code', 'students.enrollment as s_enrollment', 'students.name as s_name', 'students.father_name as s_father_name', 'students.mother_name as s_mother_name', 'students.stream as s_stream', 'students.course as s_course', 'students.dob as s_dob', 'applications.category_a as a_category_a', 'supplementaries.id as supp_id', 'supplementaries.enrollment as supp_enrollment', 'supplementaries.ai_code as supp_ai_code', 'supplementaries.course as supp_course', 'supplementaries.stream as supp_stream', 'examcenter_details.id as ed_id', 'examcenter_details.ecenter10 as ed_ecenter10', 'examcenter_details.ecenter12 as ed_ecenter12', 'examcenter_details.cent_name as ed_cent_name')
                    ->leftJoin("students", function ($join) {
                        $join->on("student_allotments.student_id", "=", "students.id");
                        $join->whereRaw("(rs_students.is_eligible = 1)");
                    })
                    ->leftJoin("supplementaries", function ($join) use ($current_admission_session_id, $current_exam_month_id) {
                        $join->on("student_allotments.student_id", "=", "supplementaries.student_id");
                        $join->whereRaw("(rs_supplementaries.submitted IS NOT NULL 
				AND rs_supplementaries.challan_tid IS NOT NULL AND rs_supplementaries.status = 1 
				AND rs_supplementaries.deleted_at IS NULL AND 
				rs_supplementaries.exam_year = " . $current_admission_session_id . " 
				AND rs_supplementaries.exam_month = 1)");
                    })
                    /*
			->leftJoin("supplementary_subjects",function($join){
				$join->on("supplementaries.id" , "=" ,"supplementary_subjects.supplementary_id");
				$join->whereRaw("(rs_supplementary_subjects.status = 1 AND rs_supplementary_subjects.deleted_at IS NULL)");
			})
			*/
                    ->leftJoin("applications", function ($join) {
                        $join->on("student_allotments.student_id", "=", "applications.student_id");
                        $join->whereRaw("(rs_applications.locksumbitted = 1 AND rs_applications.deleted_at IS NULL )");
                    })
                    ->join("examcenter_details", function ($join) {
                        $join->on("student_allotments.examcenter_detail_id", "=", "examcenter_details.id");
                    })
                    ->where($conditions)
                    ->orderBy('student_allotments.enrollment', 'ASC')
                    ->groupBy('student_allotments.enrollment')
                    ->get()->toArray();
                // @dd($students_data);

                $final_data = array();
                $centercode10 = '';
                $centercode12 = '';
                $centername = '';
                $course = '';

                $i = 0;
                if (isset($students_data) && !empty($students_data)) {
                    foreach ($students_data as $key => $student) {
                        if (!empty($student['ed_cent_name']) && !empty($student['ed_cent_name'])) {
                            $course = $student['s_course'];
                        }
                        if (!empty($student['ed_cent_name']) && !empty($student['ed_cent_name'])) {
                            $centername = $student['ed_cent_name'];
                            $centercode10 = $student['ed_ecenter10'];
                            $centercode12 = $student['ed_ecenter12'];
                        }

                        if (isset($student['supp_enrollment']) && !empty($student['supp_enrollment']) && isset($student['s_enrollment']) && !empty($student['s_enrollment'])) {
                            if (isset($student['supp_enrollment']) && !empty($student['supp_enrollment'])) { // Supp Case
                                $final_data[$course][$i]['supp_id'] = $student['supp_id'];
                                $final_data[$course][$i]['ai_code'] = $student['supp_ai_code'];
                                $final_data[$course][$i]['enrollment'] = $student['supp_enrollment'];
                            } else {
                                $final_data[$course][$i]['s_id'] = $student['s_id'];
                                $final_data[$course][$i]['ai_code'] = $student['s_ai_code'];
                                $final_data[$course][$i]['enrollment'] = $student['s_enrollment'];
                            }
                            $final_data[$course][$i]['name'] = $student['s_name'];
                            $final_data[$course][$i]['father_name'] = $student['s_father_name'];
                            $final_data[$course][$i]['mother_name'] = $student['s_mother_name'];
                            $final_data[$course][$i]['dob'] = '';
                            if (isset($student['s_dob']) && !empty($student['s_dob'])) {
                                $final_data[$course][$i]['dob'] = date("d/m/Y", strtotime($student['s_dob']));
                            }
                            $final_data[$course][$i]['category'] = $student['a_category_a'];
                            $final_data[$course][$i]['exam_subject'] = $student['s_enrollment'];
                            $i++;
                        }
                    }
                }
                // @dd($final_data);

                $examCenterDetail = $centercodeandNames;


                $examDates = array();
                $examDates[1] = Config::get('global.exam1');
                $examDates[2] = Config::get('global.exam2');

                $combo_name = 'categorya';
                $categorya_list = $this->master_details($combo_name);

                // return view('examination_reports.exam_center_nominal_roll_pdf',compact('title','stream','current_exam_month_id','current_admission_session_id','current_folder_year','current_year','final_data','centercode10','centercode12','centername','examCenterDetail','examDates','categorya_list'));

                $pdf = PDF::loadView('examination_reports.exam_center_nominal_roll_pdf', compact('title', 'stream', 'current_exam_month_id', 'current_admission_session_id', 'current_folder_year', 'current_year', 'final_data', 'centercode10', 'centercode12', 'centername', 'examCenterDetail', 'examDates', 'categorya_list', 'courseid'));


                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/stream" . $stream . "/" . $course . "/" . $districtNameListname . "/" . $centervalue . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');
                $filename = 'subject_wise_roll_number_' . $centervalue . '.pdf';
                $completepath = $path . $filename;
                $pdf->save($completepath, $pdf, true);
                return (Response::download($completepath));
            }
        }
    }

    public function excenterattendancesheet($course = null, $stream = null, $districtid = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $custom_component_obj = new CustomComponent;
        if (@$districtid == 0) {
            $districtNameList = $this->districtsByState($state_id);
        } else {
            $districtNameList = $this->districtNameById($districtid);
        }
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $exam_year = CustomHelper::_get_selected_sessions();
        $subject_list = $this->subjectCodeList();
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $subreportname = "ATTENDANCE ROLL";
        $aicentermaterial = "examcentermaterial";
        $aicodedistrictids = [];
        foreach ($districtNameList as $districtid => $districtvalue) {
            $centercode = 'ecenter' . $course;
            $centercodeandid = ExamcenterDetail::where('district_id', $districtid)->where('active', 1)->pluck($centercode, 'id')->toArray();


            foreach ($centercodeandid as $centerid => $centervalue) {

                $districtNameListid = ExamcenterDetail::where($centercode, $centervalue)->groupBy('district_id')->get('district_id')->toarray();

                $districtNameListname = @$districtNameList[$districtNameListid['0']['district_id']];

                $examyersexamcenter = CustomHelper::_get_selected_sessions();
                $conditions["student_allotments.exam_year"] = $examyersexamcenter;
                $conditions["student_allotments.exam_month"] = $stream;
                $conditions["student_allotments.course"] = $course;
                $conditions["student_allotments.supplementary"] = 0;


                $supp_conditions["student_allotments.exam_year"] = $examyersexamcenter;
                $supp_conditions["student_allotments.exam_month"] = $stream;
                $supp_conditions["student_allotments.course"] = $course;
                $supp_conditions["student_allotments.supplementary"] = 1;


                $supp_master = StudentAllotment::with('examcenter', 'document')->with('Supplementary', function ($query) use ($exam_year, $stream) {
                    $query->where('is_eligible', 1)->whereNull('deleted_at')->where('exam_year', $exam_year)->where('exam_month', $stream);
                })->with('student', function ($query) {
                    $query->whereNull('deleted_at');
                })->with('Supplementarysubjects', function ($query) {
                    $query->whereNull('deleted_at');
                })
                    ->where('examcenter_detail_id', $centerid)->where($supp_conditions)->whereNull('deleted_at')->orderBy('enrollment', 'ASC')->get()->toArray();


                $masterstudent = StudentAllotment::with('student', 'examcenter', 'document')->with('examsubject', function ($query) {
                    $query->whereNull('deleted_at');
                })->with('student', function ($query) {
                    $query->where('is_eligible', 1)->whereNull('deleted_at');
                })->where('examcenter_detail_id', $centerid)->whereNull('deleted_at')->where($conditions)->orderBy('enrollment', 'ASC')->get()->toArray();


                // $supp_master = StudentAllotment::with('examcenter','document')->with('Supplementary', function ($query) {
                // $query->whereNotNull('challan_tid')->whereNull('deleted_at');})->with('examsubject', function ($query) use($finalresult) {
                // $query->whereIn('final_result',$finalresult)->orWhereNull('final_result')->orWhere(['final_result' => '!= PASS','final_result' => '!= P','final_result' => '!= p']);})->with('student', function ($query) {
                // $query->whereNull('deleted_at');})->with('Supplementarysubjects', function ($query) {
                // $query->whereNull('deleted_at');})->where('examcenter_detail_id',$centerid)->where($supp_conditions)->limit('1')->get()->toArray();

                $finalArr = array();
                @$index = 0;

                foreach ($supp_master as $student) {
                    $fld = "is_supp";
                    @$finalArr[$index][$fld] = true;
                    $fld = "enrollment";
                    @$finalArr[$index][$fld] = $student['student'][0][$fld];
                    $fld = "ecenter10";
                    @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                    $fld = "ecenter12";
                    @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                    $fld = "cent_name";
                    @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                    $fld = "name";
                    @$finalArr[$index][$fld] = $student['student'][0][$fld];
                    $fld = "photograph";
                    @$finalArr[$index][$fld] = $student['document'][0][$fld];
                    $fld = "signature";
                    @$finalArr[$index][$fld] = $student['document'][0][$fld];
                    $fld = "student_id";
                    @$finalArr[$index][$fld] = $student['document'][0][$fld];
                    $fld = "id";
                    @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                    $fld = "examsubject";
                    @$finalArr[$index][$fld] = $student['supplementarysubjects'];
                    @$index++;
                }

                foreach ($masterstudent as $student) {

                    $fld = "is_supp";
                    @$finalArr[$index][$fld] = false;
                    $fld = "enrollment";
                    @$finalArr[$index][$fld] = $student['student'][0][$fld];
                    $fld = "ecenter10";
                    @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                    $fld = "cent_name";
                    @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                    $fld = "ecenter12";
                    @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                    $fld = "name";
                    @$finalArr[$index][$fld] = $student['student'][0][$fld];
                    $fld = "photograph";
                    @$finalArr[$index][$fld] = $student['document'][0][$fld];
                    $fld = "signature";
                    @$finalArr[$index][$fld] = $student['document'][0][$fld];
                    $fld = "student_id";
                    @$finalArr[$index][$fld] = $student['document'][0][$fld];
                    $fld = "examsubject";
                    @$finalArr[$index][$fld] = $student[$fld];
                    @$index++;
                }


                $master = $finalArr;

                //return view('examination_reports.attendance_sheet_pdf',compact('subject_list','categorya','master','subreportname','course','stream','exam_session'));

                $pdf = PDF::loadView('examination_reports.attendance_sheet_pdf', compact('subject_list', 'categorya', 'master', 'subreportname', 'course', 'stream', 'exam_session'));
                // ->setOrientation('landscape');
                $pdf->setTimeout(4000);
                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $districtNameListname . "/" . $centervalue . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');

                $filename = 'examcenter_attendanceroll_' . $centervalue . '.pdf';
                $completepath = $path . $filename;
                $pdf->save($completepath, $pdf, true);
                //return(Response::download( $completepath));
            }
        }

    }

    // public function excenterattendancesheet($course=null,$stream=null,$districtid=null){
    //    	$state_id = 6;
    // 	$custom_component_obj = new CustomComponent;
    // 	if(@$districtid == 0){
    // 		$districtNameList = $this->districtsByState($state_id);
    // 	}else{
    // 		$districtNameList = $this->districtNameById($districtid);
    // 	}

    // 	$exam_year = CustomHelper::_get_selected_sessions();
    // 	$subject_list =  $this->subjectCodeList();
    // 	$combo_name = 'categorya';$categorya = $this->master_details($combo_name);
    // 	$combo_name = 'current_folder_year';$current_folder_year = $this->master_details($combo_name);
    // 	$subreportname = "ATTENDANCE ROLL";
    // 	$aicentermaterial ="examcentermaterial";
    // 	$aicodedistrictids =[];
    //        foreach($districtNameList  as $districtid => $districtvalue){
    // 	$centercode = 'ecenter'.$course ;

    // 	 $centercodeandid = ExamcenterDetail::where('district_id',$districtid)->where('exam_year',$exam_year)->where('exam_month',$stream)->pluck($centercode,'id')->toArray();

    // 	 dd($centercodeandid);


    // 		foreach($aicodedistrictid1  as $key => $value){
    // 			@$aicodedistrictids = $value;
    // 			@$aicodedistrictids1  =  $key;

    // 			$districtnmae = District::where('state_id',6)->pluck('name','id');
    // 			if($course == 10){
    // 			$aicodedistrictid2 = ExamcenterDetail::where('ecenter10',$aicodedistrictids)->groupBy('district_id')->get('district_id')->toarray();
    // 			}elseif($course == 12){
    // 			$aicodedistrictid2 = ExamcenterDetail::where('ecenter12',$aicodedistrictids)->groupBy('district_id')->get('district_id')->toarray();
    // 			}
    // 			$aicodedistrictid3 = @$districtnmae[$aicodedistrictid2['0']['district_id']];

    // 			$examyersexamcenter = CustomHelper::_get_selected_sessions();
    // 			$conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
    // 			$conditions["student_allotments.stream"] = $stream;
    // 			$conditions["student_allotments.course"] = $course;
    // 			$conditions["student_allotments.supplementary"] = 0;


    // 			$exam_year = CustomHelper::_get_selected_sessions();

    // 			$supp_conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
    // 			$supp_conditions["student_allotments.stream"] = $stream;
    // 			$supp_conditions["student_allotments.course"] = $course;
    // 			$supp_conditions["student_allotments.supplementary"] = 1;

    // 			@$reportname = $aicodetemp;


    // 			$masterstudent = StudentAllotment::with('student','examcenter','document')->with('examsubject', function ($query) {
    // 			$query->whereNull('final_result');})->where('examcenter_detail_id',$aicodedistrictids1)->where($conditions)->get()->toArray();

    // 			$supp_master = StudentAllotment::with('student','examcenter','Supplementarysubjects','document')->with('Supplementary', function ($query) {
    // 			$query->whereNotNull('challan_tid');})->where('examcenter_detail_id',$aicodedistrictids1)->where($supp_conditions)->get()->toArray();


    // 			$finalArr = array();
    // 			@$index = 0;
    // 			foreach($masterstudent as $student){

    // 				$fld="is_supp"; @$finalArr[$index][$fld] = false;
    // 				$fld="enrollment"; @$finalArr[$index][$fld] = $student['student'][0][$fld];
    // 				$fld="ecenter10"; @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
    // 				$fld="cent_name"; @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
    // 				$fld="ecenter12"; @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
    // 				$fld="name"; @$finalArr[$index][$fld] = $student['student'][0][$fld];
    // 				$fld="photograph"; @$finalArr[$index][$fld] = $student['document'][0][$fld];
    // 				$fld="signature"; @$finalArr[$index][$fld] = $student['document'][0][$fld];
    // 				$fld="student_id"; @$finalArr[$index][$fld] = $student['document'][0][$fld];
    // 				$fld="examsubject"; @$finalArr[$index][$fld] = $student[$fld];
    // 				@$index++;
    // 			}

    // 			foreach($supp_master as $student){
    // 				$fld="is_supp"; @$finalArr[$index][$fld] = true;
    // 				$fld="enrollment"; @$finalArr[$index][$fld] = $student['student'][0][$fld];
    // 				$fld="ecenter10"; @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
    // 				$fld="ecenter12"; @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
    // 				$fld="cent_name"; @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
    // 				$fld="name"; @$finalArr[$index][$fld] = $student['student'][0][$fld];
    // 				$fld="photograph"; @$finalArr[$index][$fld] = $student['document'][0][$fld];
    // 				$fld="signature"; @$finalArr[$index][$fld] = $student['document'][0][$fld];
    // 				$fld="student_id"; @$finalArr[$index][$fld] = $student['document'][0][$fld];
    // 				$fld="id"; @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
    // 				$fld="supplementarysubjects"; @$finalArr[$index][$fld] = $student[$fld];
    // 				@$index++;
    // 			}
    // 			$master = $finalArr;

    // 			//return view('examination_reports.attendance_sheet_pdf',compact('subject_list','categorya','aicode','master','subreportname','reportname','course','aiCenters'));

    // 			$pdf =  PDF::loadView('examination_reports.attendance_sheet_pdf',compact('subject_list','categorya','master','subreportname','reportname','course','stream'));
    // 			// ->setOrientation('landscape');
    // 			$pdf->setOption('footer-right', 'Page [page] of [toPage]');

    // 			$path = public_path("files/reports/" . $current_folder_year[1]. "/stream" . $stream . "/". $aicentermaterial . "/".$course ."/". $aicodedistrictid3 . "/". $aicodedistrictids. "/");

    // 			File::makeDirectory($path, $mode = 0777, true, true);

    // 			$filename = 'attendancesheet' . $aicodedistrictids . '.pdf';
    // 			$completepath = $path .$filename;
    // 			$pdf->save($completepath,$pdf,true);
    // 			// return(Response::download( $completepath));
    // 		}
    // 	}
    // }

    public function single_exam_center_attendance_roll_pdf_request(Request $request)
    {

        $request->validate([
            'course' => 'required',
            'stream' => 'required',
            'district_id' => 'required',
            'ecenter' => 'required',
        ]);


        return redirect()->route('singleexcenterattendancesheet', array($request->course, $request->stream, $request->district_id, $request->ecenter));
    }

    public function singleexcenterattendancesheet($course = null, $stream = null, $districtid = null, $ecenter = null)
    {

        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $custom_component_obj = new CustomComponent;
        if (@$districtid == 0) {
            $districtNameList = $this->districtsByState($state_id);
        } else {
            $districtNameList = $this->districtNameById($districtid);
        }

        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $exam_year = CustomHelper::_get_selected_sessions();
        $subject_list = $this->subjectCodeList();
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $subreportname = "ATTENDANCE ROLL";
        $aicentermaterial = "examcentermaterial";
        $aicodedistrictids = [];
        foreach ($districtNameList as $districtid => $districtvalue) {
            $centercode = 'ecenter' . $course;
            $centercodeandid = ExamcenterDetail::where($centercode, $ecenter)->where('active', 1)->pluck($centercode, 'id')->toArray();


            foreach ($centercodeandid as $centerid => $centervalue) {

                $districtNameListid = ExamcenterDetail::where($centercode, $centervalue)->groupBy('district_id')->get('district_id')->toarray();

                $districtNameListname = @$districtNameList[$districtNameListid['0']['district_id']];

                $examyersexamcenter = CustomHelper::_get_selected_sessions();
                $conditions["student_allotments.exam_year"] = $examyersexamcenter;
                $conditions["student_allotments.exam_month"] = $stream;
                $conditions["student_allotments.course"] = $course;
                $conditions["student_allotments.supplementary"] = 0;


                $supp_conditions["student_allotments.exam_year"] = $examyersexamcenter;
                $supp_conditions["student_allotments.exam_month"] = $stream;
                $supp_conditions["student_allotments.course"] = $course;
                $supp_conditions["student_allotments.supplementary"] = 1;


                $supp_master = StudentAllotment::with('examcenter', 'document')->with('Supplementary', function ($query) use ($exam_year, $stream) {
                    $query->where('is_eligible', 1)->whereNull('deleted_at')->where('exam_year', $exam_year)->where('exam_month', $stream);
                })->with('student', function ($query) {
                    $query->whereNull('deleted_at');
                })->with('Supplementarysubjects', function ($query) {
                    $query->whereNull('deleted_at');
                })
                    ->where('examcenter_detail_id', $centerid)->where($supp_conditions)->whereNull('deleted_at')->orderBy('enrollment', 'ASC')->get()->toArray();


                $masterstudent = StudentAllotment::with('student', 'examcenter', 'document')->with('examsubject', function ($query) {
                    $query->whereNull('deleted_at');
                })->with('student', function ($query) {
                    $query->where('is_eligible', 1)->whereNull('deleted_at');
                })->where('examcenter_detail_id', $centerid)->whereNull('deleted_at')->where($conditions)->orderBy('enrollment', 'ASC')->get()->toArray();


                // $supp_master = StudentAllotment::with('examcenter','document')->with('Supplementary', function ($query) {
                // $query->whereNotNull('challan_tid')->whereNull('deleted_at');})->with('examsubject', function ($query) use($finalresult) {
                // $query->whereIn('final_result',$finalresult)->orWhereNull('final_result')->orWhere(['final_result' => '!= PASS','final_result' => '!= P','final_result' => '!= p']);})->with('student', function ($query) {
                // $query->whereNull('deleted_at');})->with('Supplementarysubjects', function ($query) {
                // $query->whereNull('deleted_at');})->where('examcenter_detail_id',$centerid)->where($supp_conditions)->limit('1')->get()->toArray();

                $finalArr = array();
                @$index = 0;

                foreach ($supp_master as $student) {
                    $fld = "is_supp";
                    @$finalArr[$index][$fld] = true;
                    $fld = "enrollment";
                    @$finalArr[$index][$fld] = $student['student'][0][$fld];
                    $fld = "ecenter10";
                    @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                    $fld = "ecenter12";
                    @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                    $fld = "cent_name";
                    @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                    $fld = "name";
                    @$finalArr[$index][$fld] = $student['student'][0][$fld];
                    $fld = "photograph";
                    @$finalArr[$index][$fld] = $student['document'][0][$fld];
                    $fld = "signature";
                    @$finalArr[$index][$fld] = $student['document'][0][$fld];
                    $fld = "student_id";
                    @$finalArr[$index][$fld] = $student['document'][0][$fld];
                    $fld = "id";
                    @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                    $fld = "examsubject";
                    @$finalArr[$index][$fld] = $student['supplementarysubjects'];
                    @$index++;
                }

                foreach ($masterstudent as $student) {

                    $fld = "is_supp";
                    @$finalArr[$index][$fld] = false;
                    $fld = "enrollment";
                    @$finalArr[$index][$fld] = $student['student'][0][$fld];
                    $fld = "ecenter10";
                    @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                    $fld = "cent_name";
                    @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                    $fld = "ecenter12";
                    @$finalArr[$index][$fld] = $student['examcenter'][0][$fld];
                    $fld = "name";
                    @$finalArr[$index][$fld] = $student['student'][0][$fld];
                    $fld = "photograph";
                    @$finalArr[$index][$fld] = $student['document'][0][$fld];
                    $fld = "signature";
                    @$finalArr[$index][$fld] = $student['document'][0][$fld];
                    $fld = "student_id";
                    @$finalArr[$index][$fld] = $student['document'][0][$fld];
                    $fld = "examsubject";
                    @$finalArr[$index][$fld] = $student[$fld];
                    @$index++;
                }


                $master = $finalArr;

                //return view('examination_reports.attendance_sheet_pdf',compact('subject_list','categorya','master','subreportname','course','stream','exam_session'));

                $pdf = PDF::loadView('examination_reports.attendance_sheet_pdf', compact('subject_list', 'categorya', 'master', 'subreportname', 'course', 'stream', 'exam_session'));
                $pdf->setTimeout(4000);
                // ->setOrientation('landscape');

                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $districtNameListname . "/" . $centervalue . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');

                $filename = 'examcenter_attendanceroll_' . $centervalue . '.pdf';
                $completepath = $path . $filename;
                $pdf->save($completepath, $pdf, true);
                return (Response::download($completepath));
            }
        }

    }

    public function exam_subjectpractical_student_rollwise_pdf($course = null, $stream = null, $districtid = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $custom_component_obj = new CustomComponent;
        if (@$districtid == 0) {
            $districtNameList = $this->districtsByState($state_id);
        } else {
            $districtNameList = $this->districtNameById($districtid);
        }

        $title = "SUBJECT WISE SIGNATURE ROLL";
        $current_folder_year = $this->master_details('current_folder_year');
        $current_year = @$current_folder_year[1];
        $aicentermaterial = "examcentermaterial";
        $subjectType = 'P';
        $custom_component_obj = new CustomComponent;
        $subjectList = $custom_component_obj->_getTPSubjects($course, $subjectType);
        $exam_year = CustomHelper::_get_selected_sessions();
        $conditions = array();
        $supp_conditions = array();
        $centercode = 'ecenter' . $course;
        $centerName = 'cent_name';

        foreach ($districtNameList as $districtid => $districtvalue) {
            $centercodeandids = ExamcenterDetail::where('district_id', $districtid)->where('active', 1)->pluck($centercode, 'id')->toArray();
            $centercodeandNames = ExamcenterDetail::where('district_id', $districtid)->where('active', 1)->pluck($centerName, 'id')->toArray();
            //here change in single code
            foreach ($centercodeandids as $centerid => $centervalue) {
                //agin check and need to remove again find query
                $districtNameListid = ExamcenterDetail::where($centercode, $centervalue)->where('active', 1)->groupBy('district_id')->get('district_id')->toarray();
                $districtNameListname = @$districtNameList[$districtNameListid['0']['district_id']];
                $final_data = array();
                $i = 0;
                $dataSaveItem = null;
                foreach ($subjectList as $subjectid => $subjectname) {
                    $supp_conditions = $conditions = array();

                    $conditions['student_allotments.exam_year'] = $exam_year;
                    $conditions['student_allotments.exam_month'] = $stream;
                    $conditions["student_allotments.course"] = $course;
                    $conditions["student_allotments.examcenter_detail_id"] = $centerid;
                    $supp_conditions = $conditions;
                    $conditions['exam_subjects.subject_id'] = $subjectid;
                    $conditions['student_allotments.supplementary'] = 0;
                    $supp_conditions['supplementary_subjects.subject_id'] = $subjectid;
                    $supp_conditions['supplementary_subjects.exam_year'] = $exam_year;
                    $supp_conditions['supplementary_subjects.exam_month'] = $stream;
                    $supp_conditions['student_allotments.supplementary'] = 1;


                    $SuppStudentData = array();
                    $SuppStudentData = DB::table('student_allotments')->select('student_allotments.enrollment', 'student_allotments.student_id', 'students.name', 'examcenter_details.ecenter10', 'examcenter_details.ecenter12', 'examcenter_details.cent_name', 'supplementary_subjects.subject_id')
                        ->join('supplementary_subjects', 'supplementary_subjects.student_id', '=', 'student_allotments.student_id')
                        ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                        ->join('students', 'students.id', '=', 'student_allotments.student_id')
                        ->join("supplementaries", function ($join) use ($exam_year, $stream) {
                            $join->on("supplementaries.student_id", "=", "student_allotments.student_id");
                            $join->whereRaw("(rs_supplementaries.is_eligible = 1 )");
                            $join->whereRaw("(rs_supplementaries.exam_year = " . $exam_year . " )");
                            $join->whereRaw("(rs_supplementaries.exam_month = " . $stream . " )");
                        })->where($supp_conditions)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('supplementary_subjects.deleted_at')->whereNull('supplementaries.deleted_at')->whereNull('examcenter_details.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->get()->toArray();


                    $studentData = array();
                    $studentData = DB::table('student_allotments')->select('student_allotments.enrollment', 'student_allotments.student_id', 'students.name', 'examcenter_details.ecenter10', 'examcenter_details.ecenter12', 'examcenter_details.cent_name')->join('exam_subjects', 'exam_subjects.student_id', '=', 'student_allotments.student_id')
                        ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                        ->join("students", function ($join) {
                            $join->on("student_allotments.student_id", "=", "students.id");
                            $join->whereRaw("(rs_students.is_eligible = 1)");
                        })->where($conditions)->where(function ($query) {
                            $query->orWhereNull('exam_subjects.final_result')->orWhere(['exam_subjects.final_result' => '!= PASS', 'exam_subjects.final_result' => '!= P', 'exam_subjects.final_result' => '!= p']);
                        })->whereNull('exam_subjects.deleted_at')->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('examcenter_details.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->groupBy('exam_subjects.student_id')->get()->toArray();


                    $result1 = array();
                    foreach ($SuppStudentData as $SuppStudentData1) {

                        $result = CustomHelper::getStudentResult($SuppStudentData1->student_id, $SuppStudentData1->subject_id);


                        if ($course == 12) {
                            if (empty($result) || $result == 888 || $result == 666) {

                                $result1[] = $SuppStudentData1;

                            }
                        } elseif ($course == 10) {
                            if (empty($result) || $result == 888) {

                                $result1[] = $SuppStudentData1;


                            }
                        }

                    }


                    $dataForStudentSetup = $data = array_merge($result1, $studentData);


                    //$students = array_merge($students,$suppstudents);
                    $mcounter = 0;
                    $zcounter = 0;

                    $subkey = $subjectid;
                    $dataSaveItem[$subkey] = array();

                    foreach ($dataForStudentSetup as $sk => $sval) {
                        if ($zcounter % 10 == 0) {
                            $mcounter++;
                        }
                        $fld = "enrollment";
                        $dataSaveItem[$subkey][$mcounter][$zcounter][$fld] = $sval->$fld;
                        $fld = "student_id";
                        $dataSaveItem[$subkey][$mcounter][$zcounter][$fld] = $sval->$fld;
                        $fld = "name";
                        $dataSaveItem[$subkey][$mcounter][$zcounter][$fld] = $sval->$fld;
                        $fld = "ecenter10";
                        $dataSaveItem[$subkey][$mcounter][$zcounter][$fld] = $sval->$fld;
                        $fld = "ecenter12";
                        $dataSaveItem[$subkey][$mcounter][$zcounter][$fld] = $sval->$fld;
                        $fld = "cent_name";
                        $dataSaveItem[$subkey][$mcounter][$zcounter][$fld] = $sval->$fld;
                        // $dataSaveItem[$subkey][$mcounter][$zcounter]['enrollment'] = $sval->$fld;
                        // $dataSaveItem[$subkey][$mcounter][$zcounter]['supplementary'] = $sval['StudentAllotment']['supplementary'];
                        // $dataSaveItem[$subkey][$mcounter][$zcounter]['name'] = (isset($sval['Student']['name']) && $sval['Student']['name'] != "") ? $sval['Student']['name'] : $sval['Pastdata']['name'];
                        $zcounter++;
                    }
                    // if($subjectid != 4){
                    // 	dd($dataSaveItem);
                    // }

                    $final_data['master']['subject_id'] = $subjectid;
                    $final_data['master']['subject_name'] = $subjectname;
                    $final_data['master']['cent_code'] = $centervalue;
                    $final_data['master']['cent_name'] = @$centercodeandNames[@$centerid];
                    // $i++;

                }
                // dd($dataSaveItem);
                $masterData = $final_data['master'];
                // dd($data);
                // return view('examination_reports.exam_subjectspractical_student_rollwise_pdf_final',compact('current_year','stream','exam_session','final_data','subjectList','centervalue','course','dataSaveItem','masterData'));

                $pdf = PDF::loadView('examination_reports.exam_subjectspractical_student_rollwise_pdf_final', compact('current_year', 'stream', 'final_data', 'subjectList', 'centervalue', 'exam_session', 'course', 'masterData', 'dataSaveItem'))->setOrientation('landscape');
                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $districtNameListname . "/" . $centervalue . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');

                $filename = 'examcenter_practicalsignatureroll_' . $centervalue . '.pdf';
                $completepath = $path . $filename;
                $pdf->save($completepath, $pdf, true);

                //return( Response::download( $completepath ) );
            }
        }

    }

    public function single_exam_center_practicalsignaturenominal_roll_pdf_request(Request $request)
    {

        $request->validate([
            'course' => 'required',
            'stream' => 'required',
            'district_id' => 'required',
            'ecenter' => 'required',
        ]);


        return redirect()->route('single_exam_subjectpractical_student_rollwise_pdf', array($request->course, $request->stream, $request->district_id, $request->ecenter));
    }

    public function single_exam_subjectpractical_student_rollwise_pdf($course = null, $stream = null, $districtid = null, $ecenter = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $custom_component_obj = new CustomComponent;
        if (@$districtid == 0) {
            $districtNameList = $this->districtsByState($state_id);
        } else {
            $districtNameList = $this->districtNameById($districtid);
        }

        $title = "SUBJECT WISE SIGNATURE ROLL";
        $current_folder_year = $this->master_details('current_folder_year');
        $current_year = @$current_folder_year[1];
        $aicentermaterial = "examcentermaterial";
        $subjectType = 'P';
        $custom_component_obj = new CustomComponent;
        $subjectList = $custom_component_obj->_getTPSubjects($course, $subjectType);
        $exam_year = CustomHelper::_get_selected_sessions();
        $conditions = array();
        $supp_conditions = array();
        $centercode = 'ecenter' . $course;
        $centerName = 'cent_name';

        foreach ($districtNameList as $districtid => $districtvalue) {
            $centercodeandids = ExamcenterDetail::where($centercode, $ecenter)->where('active', 1)->pluck($centercode, 'id')->toArray();
            $centercodeandNames = ExamcenterDetail::where($centercode, $ecenter)->where('active', 1)->pluck($centerName, 'id')->toArray();
            //here change in single code
            foreach ($centercodeandids as $centerid => $centervalue) {
                //agin check and need to remove again find query
                $districtNameListid = ExamcenterDetail::where($centercode, $centervalue)->where('active', 1)->groupBy('district_id')->get('district_id')->toarray();
                $districtNameListname = @$districtNameList[$districtNameListid['0']['district_id']];
                $final_data = array();
                $i = 0;
                $dataSaveItem = null;
                foreach ($subjectList as $subjectid => $subjectname) {
                    $supp_conditions = $conditions = array();

                    $conditions['student_allotments.exam_year'] = $exam_year;
                    $conditions['student_allotments.exam_month'] = $stream;
                    $conditions["student_allotments.course"] = $course;
                    $conditions["student_allotments.examcenter_detail_id"] = $centerid;
                    $supp_conditions = $conditions;
                    $conditions['exam_subjects.subject_id'] = $subjectid;
                    $conditions['student_allotments.supplementary'] = 0;
                    $supp_conditions['supplementary_subjects.subject_id'] = $subjectid;
                    $supp_conditions['supplementary_subjects.exam_year'] = $exam_year;
                    $supp_conditions['supplementary_subjects.exam_month'] = $stream;
                    $supp_conditions['student_allotments.supplementary'] = 1;

                    $SuppStudentData = array();
                    $SuppStudentData = DB::table('student_allotments')->select('student_allotments.enrollment', 'student_allotments.student_id', 'students.name', 'examcenter_details.ecenter10', 'examcenter_details.ecenter12', 'examcenter_details.cent_name', 'supplementary_subjects.subject_id')
                        ->join('supplementary_subjects', 'supplementary_subjects.student_id', '=', 'student_allotments.student_id')
                        ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                        ->join('students', 'students.id', '=', 'student_allotments.student_id')
                        ->join("supplementaries", function ($join) use ($exam_year, $stream) {
                            $join->on("supplementaries.student_id", "=", "student_allotments.student_id");
                            $join->whereRaw("(rs_supplementaries.is_eligible = 1 )");
                            $join->whereRaw("(rs_supplementaries.exam_year = " . $exam_year . " )");
                            $join->whereRaw("(rs_supplementaries.exam_month = " . $stream . " )");
                        })->where($supp_conditions)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('supplementary_subjects.deleted_at')->whereNull('supplementaries.deleted_at')->whereNull('examcenter_details.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->get()->toArray();


                    $studentData = array();
                    $studentData = DB::table('student_allotments')->select('student_allotments.enrollment', 'student_allotments.student_id', 'students.name', 'examcenter_details.ecenter10', 'examcenter_details.ecenter12', 'examcenter_details.cent_name')->join('exam_subjects', 'exam_subjects.student_id', '=', 'student_allotments.student_id')
                        ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                        ->join("students", function ($join) {
                            $join->on("student_allotments.student_id", "=", "students.id");
                            $join->whereRaw("(rs_students.is_eligible = 1)");
                        })->where($conditions)->where(function ($query) {
                            $query->orWhereNull('exam_subjects.final_result')->orWhere(['exam_subjects.final_result' => '!= PASS', 'exam_subjects.final_result' => '!= P', 'exam_subjects.final_result' => '!= p']);
                        })->whereNull('exam_subjects.deleted_at')->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('examcenter_details.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->groupBy('exam_subjects.student_id')->get()->toArray();


                    $result1 = array();
                    foreach ($SuppStudentData as $SuppStudentData1) {

                        $result = CustomHelper::getStudentResult($SuppStudentData1->student_id, $SuppStudentData1->subject_id);

                        if ($course == 12) {
                            if (empty($result) || $result == 888 || $result == 666) {

                                $result1[] = $SuppStudentData1;

                            }
                        } elseif ($course == 10) {
                            if (empty($result) || $result == 888) {

                                $result1[] = $SuppStudentData1;


                            }
                        }

                    }


                    $dataForStudentSetup = $data = array_merge($result1, $studentData);


                    //$students = array_merge($students,$suppstudents);
                    $mcounter = 0;
                    $zcounter = 0;

                    $subkey = $subjectid;
                    $dataSaveItem[$subkey] = array();

                    foreach ($dataForStudentSetup as $sk => $sval) {
                        if ($zcounter % 10 == 0) {
                            $mcounter++;
                        }
                        $fld = "enrollment";
                        $dataSaveItem[$subkey][$mcounter][$zcounter][$fld] = $sval->$fld;
                        $fld = "student_id";
                        $dataSaveItem[$subkey][$mcounter][$zcounter][$fld] = $sval->$fld;
                        $fld = "name";
                        $dataSaveItem[$subkey][$mcounter][$zcounter][$fld] = $sval->$fld;
                        $fld = "ecenter10";
                        $dataSaveItem[$subkey][$mcounter][$zcounter][$fld] = $sval->$fld;
                        $fld = "ecenter12";
                        $dataSaveItem[$subkey][$mcounter][$zcounter][$fld] = $sval->$fld;
                        $fld = "cent_name";
                        $dataSaveItem[$subkey][$mcounter][$zcounter][$fld] = $sval->$fld;
                        // $dataSaveItem[$subkey][$mcounter][$zcounter]['enrollment'] = $sval->$fld;
                        // $dataSaveItem[$subkey][$mcounter][$zcounter]['supplementary'] = $sval['StudentAllotment']['supplementary'];
                        // $dataSaveItem[$subkey][$mcounter][$zcounter]['name'] = (isset($sval['Student']['name']) && $sval['Student']['name'] != "") ? $sval['Student']['name'] : $sval['Pastdata']['name'];
                        $zcounter++;
                    }
                    // if($subjectid != 4){
                    // 	dd($dataSaveItem);
                    // }

                    $final_data['master']['subject_id'] = $subjectid;
                    $final_data['master']['subject_name'] = $subjectname;
                    $final_data['master']['cent_code'] = $centervalue;
                    $final_data['master']['cent_name'] = @$centercodeandNames[@$centerid];
                    // $i++;

                }
                // dd($dataSaveItem);
                $masterData = $final_data['master'];
                // dd($data);
                // return view('examination_reports.exam_subjectspractical_student_rollwise_pdf_final',compact('current_year','stream','exam_session','final_data','subjectList','centervalue','course','dataSaveItem','masterData'));

                $pdf = PDF::loadView('examination_reports.exam_subjectspractical_student_rollwise_pdf_final', compact('current_year', 'stream', 'final_data', 'subjectList', 'centervalue', 'exam_session', 'course', 'masterData', 'dataSaveItem'))->setOrientation('landscape');
                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $districtNameListname . "/" . $centervalue . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');

                $filename = 'examcenter_practicalsignatureroll_' . $centervalue . '.pdf';
                $completepath = $path . $filename;
                $pdf->save($completepath, $pdf, true);

                return (Response::download($completepath));
            }
        }

    }

    public function exam_subjectthory_student_rollwise_pdf($course = null, $stream = null, $districtid = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $custom_component_obj = new CustomComponent;
        if (@$districtid == 0) {
            $districtNameList = $this->districtsByState($state_id);
        } else {
            $districtNameList = $this->districtNameById($districtid);
        }

        $title = "SUBJECT WISE SIGNATURE ROLL";
        $current_folder_year = $this->master_details('current_folder_year');
        $current_year = @$current_folder_year[1];
        $aicentermaterial = "examcentermaterial";
        $subjectType = 'T';
        $custom_component_obj = new CustomComponent;
        $subjectList = $custom_component_obj->_getTPSubjects($course, $subjectType);
        $exam_year = CustomHelper::_get_selected_sessions();
        $conditions = array();
        $supp_conditions = array();
        $centercode = 'ecenter' . $course;
        $centerName = 'cent_name';

        foreach ($districtNameList as $districtid => $districtvalue) {
            $centercodeandids = ExamcenterDetail::where('district_id', $districtid)->where('active', 1)->pluck($centercode, 'id')->toArray();
            $centercodeandNames = ExamcenterDetail::where('district_id', $districtid)->where('active', 1)->pluck($centerName, 'id')->toArray();
            //here change in single code
            foreach ($centercodeandids as $centerid => $centervalue) {
                //agin check and need to remove again find query
                $districtNameListid = ExamcenterDetail::where($centercode, $centervalue)->where('active', 1)->groupBy('district_id')->get('district_id')->toarray();
                $districtNameListname = @$districtNameList[$districtNameListid['0']['district_id']];
                $final_data = array();
                $i = 0;
                foreach ($subjectList as $subjectid => $subjectname) {
                    $supp_conditions = $conditions = array();

                    $conditions['student_allotments.exam_year'] = $exam_year;
                    $conditions['student_allotments.exam_month'] = $stream;
                    $conditions["student_allotments.course"] = $course;
                    $conditions["student_allotments.examcenter_detail_id"] = $centerid;
                    $supp_conditions = $conditions;

                    $conditions['exam_subjects.subject_id'] = $subjectid;
                    $conditions['student_allotments.supplementary'] = 0;
                    $supp_conditions['supplementary_subjects.subject_id'] = $subjectid;
                    $supp_conditions['supplementary_subjects.exam_year'] = $exam_year;
                    $supp_conditions['supplementary_subjects.exam_month'] = $stream;
                    $supp_conditions['student_allotments.supplementary'] = 1;


                    $studentData = array();
                    $studentData = DB::table('student_allotments')->select('student_allotments.enrollment', 'student_allotments.student_id', 'students.name', 'examcenter_details.ecenter10', 'examcenter_details.ecenter12', 'examcenter_details.cent_name')->join('exam_subjects', 'exam_subjects.student_id', '=', 'student_allotments.student_id')
                        ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                        ->join("students", function ($join) {
                            $join->on("student_allotments.student_id", "=", "students.id");
                            $join->whereRaw("(rs_students.is_eligible = 1)");
                        })->where($conditions)->where(function ($query) {
                            $query->orWhereNull('exam_subjects.final_result')->orWhere(['exam_subjects.final_result' => '!= PASS', 'exam_subjects.final_result' => '!= P', 'exam_subjects.final_result' => '!= p']);
                        })->whereNull('exam_subjects.deleted_at')
                        ->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('examcenter_details.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->groupBy('exam_subjects.student_id')->get()->toArray();

                    $SuppStudentData = array();
                    $SuppStudentData = DB::table('student_allotments')->select('student_allotments.enrollment', 'student_allotments.student_id', 'students.name', 'examcenter_details.ecenter10', 'examcenter_details.ecenter12', 'examcenter_details.cent_name', 'supplementary_subjects.subject_id')
                        ->join('supplementary_subjects', 'supplementary_subjects.student_id', '=', 'student_allotments.student_id')
                        ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                        ->join('students', 'students.id', '=', 'student_allotments.student_id')
                        ->join("supplementaries", function ($join) use ($exam_year, $stream) {
                            $join->on("supplementaries.student_id", "=", "student_allotments.student_id");
                            $join->whereRaw("(rs_supplementaries.is_eligible = 1 )");
                            $join->whereRaw("(rs_supplementaries.exam_year = " . $exam_year . " )");
                            $join->whereRaw("(rs_supplementaries.exam_month = " . $stream . " )");
                        })
                        ->where($supp_conditions)->whereNull('student_allotments.deleted_at')->whereNull('supplementary_subjects.deleted_at')->whereNull('supplementaries.deleted_at')->whereNull('students.deleted_at')->whereNull('examcenter_details.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->get()->toArray();


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


                    $final_data[$i]['subject_id'] = $subjectid;
                    $final_data[$i]['subject_name'] = $subjectname;
                    $final_data[$i]['cent_code'] = $centervalue;
                    $final_data[$i]['cent_name'] = @$centercodeandNames[@$centerid];
                    $final_data[$i]['studentData'] = $studentData;
                    $final_data[$i]['suppStudentData'] = $result1;
                    $i++;


                }

                //return view('examination_reports.exam_subjects_student_rollwise_pdf_final',compact('current_year','stream','final_data','subjectList','centervalue','exam_session','course'));

                $pdf = PDF::loadView('examination_reports.exam_subjects_student_rollwise_pdf_final', compact('current_year', 'stream', 'final_data', 'subjectList', 'centervalue', 'exam_session', 'course'));
                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $districtNameListname . "/" . $centervalue . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');

                $filename = 'examcenter_theorysignatureroll_' . $centervalue . '.pdf';
                $completepath = $path . $filename;
                $pdf->save($completepath, $pdf, true);

                // return( Response::download( $completepath ) );
            }
        }

    }

    public function single_exam_center_theorysignaturenominal_roll_pdf_request(Request $request)
    {

        $request->validate([
            'course' => 'required',
            'stream' => 'required',
            'district_id' => 'required',
            'ecenter' => 'required',
        ]);


        return redirect()->route('single_exam_subjectthory_student_rollwise_pdf', array($request->course, $request->stream, $request->district_id, $request->ecenter));
    }

    public function single_exam_subjectthory_student_rollwise_pdf($course = null, $stream = null, $districtid = null, $ecenter = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $custom_component_obj = new CustomComponent;
        if (@$districtid == 0) {
            $districtNameList = $this->districtsByState($state_id);
        } else {
            $districtNameList = $this->districtNameById($districtid);
        }

        $title = "SUBJECT WISE SIGNATURE ROLL";
        $current_folder_year = $this->master_details('current_folder_year');
        $current_year = @$current_folder_year[1];
        $aicentermaterial = "examcentermaterial";
        $subjectType = 'T';
        $custom_component_obj = new CustomComponent;
        $subjectList = $custom_component_obj->_getTPSubjects($course, $subjectType);
        $exam_year = CustomHelper::_get_selected_sessions();
        $conditions = array();
        $supp_conditions = array();
        $centercode = 'ecenter' . $course;
        $centerName = 'cent_name';

        foreach ($districtNameList as $districtid => $districtvalue) {
            $centercodeandids = ExamcenterDetail::where($centercode, $ecenter)->where('active', 1)->pluck($centercode, 'id')->toArray();
            $centercodeandNames = ExamcenterDetail::where($centercode, $ecenter)->where('active', 1)->pluck($centerName, 'id')->toArray();
            //here change in single code
            foreach ($centercodeandids as $centerid => $centervalue) {
                //agin check and need to remove again find query
                $districtNameListid = ExamcenterDetail::where($centercode, $centervalue)->where('active', 1)->groupBy('district_id')->get('district_id')->toarray();
                $districtNameListname = @$districtNameList[$districtNameListid['0']['district_id']];
                $final_data = array();
                $i = 0;
                foreach ($subjectList as $subjectid => $subjectname) {
                    $supp_conditions = $conditions = array();

                    $conditions['student_allotments.exam_year'] = $exam_year;
                    $conditions['student_allotments.exam_month'] = $stream;
                    $conditions["student_allotments.course"] = $course;
                    $conditions["student_allotments.examcenter_detail_id"] = $centerid;
                    $supp_conditions = $conditions;

                    $conditions['exam_subjects.subject_id'] = $subjectid;
                    $conditions['student_allotments.supplementary'] = 0;
                    $supp_conditions['supplementary_subjects.subject_id'] = $subjectid;
                    $supp_conditions['supplementary_subjects.exam_year'] = $exam_year;
                    $supp_conditions['supplementary_subjects.exam_month'] = $stream;
                    $supp_conditions['student_allotments.supplementary'] = 1;


                    $studentData = array();
                    $studentData = DB::table('student_allotments')->select('student_allotments.enrollment', 'student_allotments.student_id', 'students.name', 'examcenter_details.ecenter10', 'examcenter_details.ecenter12', 'examcenter_details.cent_name')->join('exam_subjects', 'exam_subjects.student_id', '=', 'student_allotments.student_id')
                        ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                        ->join("students", function ($join) {
                            $join->on("student_allotments.student_id", "=", "students.id");
                            $join->whereRaw("(rs_students.is_eligible = 1)");
                        })->where($conditions)->where(function ($query) {
                            $query->orWhereNull('exam_subjects.final_result')->orWhere(['exam_subjects.final_result' => '!= PASS', 'exam_subjects.final_result' => '!= P', 'exam_subjects.final_result' => '!= p']);
                        })->whereNull('exam_subjects.deleted_at')
                        ->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('examcenter_details.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->groupBy('exam_subjects.student_id')->get()->toArray();

                    $SuppStudentData = array();
                    $SuppStudentData = DB::table('student_allotments')->select('student_allotments.enrollment', 'student_allotments.student_id', 'students.name', 'examcenter_details.ecenter10', 'examcenter_details.ecenter12', 'examcenter_details.cent_name', 'supplementary_subjects.subject_id')
                        ->join('supplementary_subjects', 'supplementary_subjects.student_id', '=', 'student_allotments.student_id')
                        ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                        ->join('students', 'students.id', '=', 'student_allotments.student_id')
                        ->join("supplementaries", function ($join) use ($exam_year, $stream) {
                            $join->on("supplementaries.student_id", "=", "student_allotments.student_id");
                            $join->whereRaw("(rs_supplementaries.is_eligible = 1 )");
                            $join->whereRaw("(rs_supplementaries.exam_year = " . $exam_year . " )");
                            $join->whereRaw("(rs_supplementaries.exam_month = " . $stream . " )");
                        })
                        ->where($supp_conditions)->whereNull('student_allotments.deleted_at')->whereNull('supplementary_subjects.deleted_at')->whereNull('supplementaries.deleted_at')->whereNull('students.deleted_at')->whereNull('examcenter_details.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->get()->toArray();


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


                    $final_data[$i]['subject_id'] = $subjectid;
                    $final_data[$i]['subject_name'] = $subjectname;
                    $final_data[$i]['cent_code'] = $centervalue;
                    $final_data[$i]['cent_name'] = @$centercodeandNames[@$centerid];
                    $final_data[$i]['studentData'] = $studentData;
                    $final_data[$i]['suppStudentData'] = $result1;
                    $i++;


                }

                //return view('examination_reports.exam_subjects_student_rollwise_pdf_final',compact('current_year','stream','final_data','subjectList','centervalue','exam_session','course'));

                $pdf = PDF::loadView('examination_reports.exam_subjects_student_rollwise_pdf_final', compact('current_year', 'stream', 'final_data', 'subjectList', 'centervalue', 'exam_session', 'course'));
                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $districtNameListname . "/" . $centervalue . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');

                $filename = 'examcenter_theorysignatureroll_' . $centervalue . '.pdf';
                $completepath = $path . $filename;
                $pdf->save($completepath, $pdf, true);

                return (Response::download($completepath));
            }
        }

    }

    public function exam_student_rollwise_pdf($course = null, $stream = null, $district_id = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $custom_component_obj = new CustomComponent;
        if (@$districtid == 0) {
            $districtNameList = $this->districtsByState($state_id);
        } else {
            $districtNameList = $this->districtNameById($districtid);
        }
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $exam_year = CustomHelper::_get_selected_sessions();
        $subjectType = 'T';
        $custom_component_obj = new CustomComponent;
        $subjectList = $custom_component_obj->_getTPSubjects($course, $subjectType);
        $subject_count = count($subjectList);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $current_year = @$current_folder_year[1];
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $subreportname = "exam_student_rollwise_pdf";
        $aicentermaterial = "examcentermaterial";
        $aicodedistrictids = [];
        foreach ($districtNameList as $districtid => $districtvalue) {
            $centercode = 'ecenter' . $course;
            $centerName = 'cent_name';
            $centercodeandid = ExamcenterDetail::where('district_id', $districtid)->where('active', 1)->pluck($centercode, 'id')->toArray();
            $centercodeandNames = ExamcenterDetail::where('district_id', $districtid)->where('active', 1)->pluck($centerName, 'id')->toArray();
            foreach ($centercodeandid as $centerid => $centervalue) {
                $districtNameListid = ExamcenterDetail::where($centercode, $centervalue)->groupBy('district_id')->get('district_id')->toarray();
                $districtNameListname = @$districtNameList[$districtNameListid['0']['district_id']];
                $final_data = array();
                $i = 0;
                foreach ($subjectList as $subjectid => $subjectname) {
                    $supp_conditions = $conditions = array();

                    $conditions['student_allotments.exam_year'] = $exam_year;
                    $conditions['student_allotments.exam_month'] = $stream;
                    $conditions["student_allotments.course"] = $course;
                    $conditions["student_allotments.examcenter_detail_id"] = $centerid;
                    $supp_conditions = $conditions;

                    $conditions['exam_subjects.subject_id'] = $subjectid;
                    $conditions['student_allotments.supplementary'] = 0;
                    $supp_conditions['supplementary_subjects.subject_id'] = $subjectid;
                    $supp_conditions['supplementary_subjects.exam_year'] = $exam_year;
                    $supp_conditions['supplementary_subjects.exam_month'] = $stream;
                    $supp_conditions['student_allotments.supplementary'] = 1;


                    $studentData = array();
                    $studentData = DB::table('student_allotments')->select('student_allotments.enrollment')->join('exam_subjects', 'exam_subjects.student_id', '=', 'student_allotments.student_id')
                        ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                        ->join("students", function ($join) {
                            $join->on("student_allotments.student_id", "=", "students.id");
                            $join->whereRaw("(rs_students.is_eligible = 1)");
                        })->where($conditions)->where(function ($query) {
                            $query->orWhereNull('exam_subjects.final_result')->orWhere(['exam_subjects.final_result' => '!= PASS', 'exam_subjects.final_result' => '!= P', 'exam_subjects.final_result' => '!= p']);
                        })->whereNull('exam_subjects.deleted_at')
                        ->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('examcenter_details.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->groupBy('exam_subjects.student_id')->get()->toArray();

                    $SuppStudentData = array();
                    $SuppStudentData = DB::table('student_allotments11')->select('student_allotments.enrollment', 'supplementary_subjects.subject_id', 'student_allotments.student_id')
                        ->join('supplementary_subjects', 'supplementary_subjects.student_id', '=', 'student_allotments.student_id')
                        ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                        ->join('students', 'students.id', '=', 'student_allotments.student_id')
                        ->join("supplementaries", function ($join) use ($exam_year, $stream) {
                            $join->on("supplementaries.student_id", "=", "student_allotments.student_id");
                            $join->whereRaw("(rs_supplementaries.is_eligible = 1 )");
                            $join->whereRaw("(rs_supplementaries.exam_year = " . $exam_year . " )");
                            $join->whereRaw("(rs_supplementaries.exam_month = " . $stream . " )");
                        })
                        ->where($supp_conditions)->whereNull('student_allotments.deleted_at')->whereNull('supplementary_subjects.deleted_at')->whereNull('examcenter_details.deleted_at')->whereNull('students.deleted_at')->whereNull('supplementaries.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->get()->toArray();


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


                    $final_data[$i]['subject_id'] = $subjectid;
                    $final_data[$i]['subject_name'] = $subjectname;
                    $final_data[$i]['cent_code'] = $centervalue;
                    $final_data[$i]['cent_name'] = @$centercodeandNames[@$centerid];
                    $final_data[$i]['studentData'] = $studentData;
                    $final_data[$i]['suppStudentData'] = $result1;
                    $i++;
                }


                //return view('examination_reports.exam_student_rollwise_pdf',compact('current_year','stream','final_data','subjectList','subject_count','examcenter_detail_id','examCenterDetail','examDates'));

                $pdf = PDF::loadView('examination_reports.exam_student_rollwise_pdf', compact('current_year', 'stream', 'final_data', 'subjectList', 'centervalue', 'subject_count', 'exam_session', 'centercodeandNames', 'course'));

                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $districtNameListname . "/" . $centervalue . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');

                $filename = 'examcenter_theoryroll' . $centervalue . '.pdf';
                $completepath = $path . $filename;

                $pdf->save($completepath, $pdf, true);
                //return( Response::download( $completepath ) );

            }
        }
    }

    public function single_exam_center_theorynominal_roll_pdf_request(Request $request)
    {

        $request->validate([
            'course' => 'required',
            'stream' => 'required',
            'district_id' => 'required',
            'ecenter' => 'required',
        ]);


        return redirect()->route('single_exam_student_rollwise_pdf', array($request->course, $request->stream, $request->district_id, $request->ecenter));
    }

    public function single_exam_student_rollwise_pdf11($course = null, $stream = null, $district_id = null, $ecenter = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $custom_component_obj = new CustomComponent;
        if (@$districtid == 0) {
            $districtNameList = $this->districtsByState($state_id);
        } else {
            $districtNameList = $this->districtNameById($districtid);
        }
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $exam_year = CustomHelper::_get_selected_sessions();
        $subjectType = 'T';
        $custom_component_obj = new CustomComponent;
        $subjectList = $custom_component_obj->_getTPSubjects($course, $subjectType);
        $subject_count = count($subjectList);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $current_year = @$current_folder_year[1];
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $subreportname = "exam_student_rollwise_pdf";
        $aicentermaterial = "examcentermaterial";
        $aicodedistrictids = [];
        foreach ($districtNameList as $districtid => $districtvalue) {
            $centercode = 'ecenter' . $course;
            $centerName = 'cent_name';
            $centercodeandid = ExamcenterDetail::where($centercode, $ecenter)->where('active', 1)->pluck($centercode, 'id')->toArray();
            $centercodeandNames = ExamcenterDetail::where($centercode, $ecenter)->where('active', 1)->pluck($centerName, 'id')->toArray();
            foreach ($centercodeandid as $centerid => $centervalue) {
                $districtNameListid = ExamcenterDetail::where($centercode, $centervalue)->groupBy('district_id')->get('district_id')->toarray();
                $districtNameListname = @$districtNameList[$districtNameListid['0']['district_id']];
                $final_data = array();
                $i = 0;
                foreach ($subjectList as $subjectid => $subjectname) {
                    $exam_year = $exam_year;
                    $exam_month = $stream;
                    $course = $course;
                    $subject_id = $subjectid;
                    $examcenter_detail_id = $centerid;
                    $studentData = array();
                    $studentData = DB::select('call GetAllDataStudentFresh(?,?,?,?,?)', array($exam_year, $exam_month, $course, $examcenter_detail_id, $subject_id));
                    $SuppStudentData = array();
                    $SuppStudentData = DB::select('call GetAllDataStudentSupp(?,?,?,?,?)', array($exam_year, $exam_month, $course, $examcenter_detail_id, $subject_id));
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

                    $final_data[$i]['subject_id'] = $subjectid;
                    $final_data[$i]['subject_name'] = $subjectname;
                    $final_data[$i]['cent_code'] = $centervalue;
                    $final_data[$i]['cent_name'] = @$centercodeandNames[@$centerid];
                    $final_data[$i]['studentData'] = $studentData;
                    $final_data[$i]['suppStudentData'] = $result1;
                    $i++;
                }


                //return view('examination_reports.exam_student_rollwise_pdf',compact('current_year','stream','final_data','subjectList','subject_count','examcenter_detail_id','examCenterDetail','examDates'));

                $pdf = PDF::loadView('examination_reports.exam_student_rollwise_pdf', compact('current_year', 'stream', 'final_data', 'subjectList', 'centervalue', 'subject_count', 'exam_session', 'centercodeandNames', 'course'));

                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $districtNameListname . "/" . $centervalue . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');

                $filename = 'examcenter_theoryroll' . $centervalue . '.pdf';
                $completepath = $path . $filename;

                $pdf->save($completepath, $pdf, true);
                return (Response::download($completepath));

            }
        }
    }

    public function single_exam_student_rollwise_pdf($course = null, $stream = null, $district_id = null, $ecenter = null)
    {
        $exam_year = 124;
        $exam_month = 1;
        $course = 10;
        $district_id = 8;
        $subjectType = 0;
        $studentData = DB::select('call p_generate_snapshot(?,?,?,?,?)', array($exam_year, $exam_month, $course, $district_id, $subjectType));
        dd($studentData);
    }

    public function exam_practical_student_rollwise_pdf($course = null, $stream = null, $district_id = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $custom_component_obj = new CustomComponent;
        if (@$districtid == 0) {
            $districtNameList = $this->districtsByState($state_id);
        } else {
            $districtNameList = $this->districtNameById($districtid);
        }
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $exam_year = CustomHelper::_get_selected_sessions();
        $subjectType = 'P';
        $custom_component_obj = new CustomComponent;
        $subjectList = $custom_component_obj->_getTPSubjects($course, $subjectType);
        $subject_count = count($subjectList);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $current_year = @$current_folder_year[1];
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $subreportname = "exam_student_rollwise_pdf";
        $aicentermaterial = "examcentermaterial";
        $aicodedistrictids = [];
        foreach ($districtNameList as $districtid => $districtvalue) {
            $centercode = 'ecenter' . $course;
            $centerName = 'cent_name';
            $centercodeandid = ExamcenterDetail::where('district_id', $districtid)->where('active', 1)->pluck($centercode, 'id')->toArray();
            $centercodeandNames = ExamcenterDetail::where('district_id', $districtid)->where('active', 1)->pluck($centerName, 'id')->toArray();
            foreach ($centercodeandid as $centerid => $centervalue) {
                $districtNameListid = ExamcenterDetail::where($centercode, $centervalue)->groupBy('district_id')->get('district_id')->toarray();
                $districtNameListname = @$districtNameList[$districtNameListid['0']['district_id']];
                $final_data = array();
                $i = 0;
                foreach ($subjectList as $subjectid => $subjectname) {
                    $supp_conditions = $conditions = array();

                    $conditions['student_allotments.exam_year'] = $exam_year;
                    $conditions['student_allotments.exam_month'] = $stream;
                    $conditions["student_allotments.course"] = $course;
                    $conditions["student_allotments.examcenter_detail_id"] = $centerid;
                    $supp_conditions = $conditions;

                    $conditions['exam_subjects.subject_id'] = $subjectid;
                    $conditions['student_allotments.supplementary'] = 0;
                    $supp_conditions['supplementary_subjects.subject_id'] = $subjectid;
                    $supp_conditions['supplementary_subjects.exam_year'] = $exam_year;
                    $supp_conditions['supplementary_subjects.exam_month'] = $stream;
                    $supp_conditions['student_allotments.supplementary'] = 1;


                    $studentData = array();
                    $studentData = DB::table('student_allotments')->select('student_allotments.enrollment')->join('exam_subjects', 'exam_subjects.student_id', '=', 'student_allotments.student_id')
                        ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                        ->join("students", function ($join) {
                            $join->on("student_allotments.student_id", "=", "students.id");
                            $join->whereRaw("(rs_students.is_eligible = 1)");
                        })->where($conditions)->where(function ($query) {
                            $query->orWhereNull('exam_subjects.final_result')->orWhere(['exam_subjects.final_result' => '!= PASS', 'exam_subjects.final_result' => '!= P', 'exam_subjects.final_result' => '!= p']);
                        })->whereNull('exam_subjects.deleted_at')
                        ->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('examcenter_details.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->groupBy('exam_subjects.student_id')->get()->toArray();

                    $SuppStudentData = array();
                    $SuppStudentData = DB::table('student_allotments')->select('student_allotments.enrollment', 'supplementary_subjects.subject_id', 'student_allotments.student_id')
                        ->join('supplementary_subjects', 'supplementary_subjects.student_id', '=', 'student_allotments.student_id')
                        ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                        ->join('students', 'students.id', '=', 'student_allotments.student_id')
                        ->join("supplementaries", function ($join) use ($exam_year, $stream) {
                            $join->on("supplementaries.student_id", "=", "student_allotments.student_id");
                            $join->whereRaw("(rs_supplementaries.is_eligible = 1 )");
                            $join->whereRaw("(rs_supplementaries.exam_year = " . $exam_year . " )");
                            $join->whereRaw("(rs_supplementaries.exam_month = " . $stream . " )");
                        })
                        ->where($supp_conditions)->whereNull('student_allotments.deleted_at')->whereNull('examcenter_details.deleted_at')->whereNull('students.deleted_at')->whereNull('supplementary_subjects.deleted_at')->whereNull('supplementaries.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->get()->toArray();

                    $result1 = array();
                    foreach ($SuppStudentData as $SuppStudentData1) {

                        $result = CustomHelper::getStudentResult($SuppStudentData1->student_id, $SuppStudentData1->subject_id);

                        if ($course == 12) {
                            if (empty($result) || $result == 888 || $result == 666) {

                                $result1[] = $SuppStudentData1;

                            }
                        } elseif ($course == 10) {
                            if (empty($result) || $result == 888) {

                                $result1[] = $SuppStudentData1;


                            }
                        }

                    }


                    $final_data[$i]['subject_id'] = $subjectid;
                    $final_data[$i]['subject_name'] = $subjectname;
                    $final_data[$i]['cent_code'] = $centervalue;
                    $final_data[$i]['cent_name'] = @$centercodeandNames[@$centerid];
                    $final_data[$i]['studentData'] = $studentData;
                    $final_data[$i]['suppStudentData'] = $result1;
                    $i++;
                }


                //return view('examination_reports.exam_student_rollwise_pdf',compact('current_year','stream','final_data','subjectList','subject_count','examcenter_detail_id','examCenterDetail','examDates'));

                $pdf = PDF::loadView('examination_reports.exam_practical_student_rollwise_pdf', compact('current_year', 'stream', 'final_data', 'subjectList', 'centervalue', 'subject_count', 'exam_session', 'centercodeandNames', 'course'));

                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $districtNameListname . "/" . $centervalue . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');

                $filename = 'examcenter_practicalroll' . $centervalue . '.pdf';
                $completepath = $path . $filename;

                $pdf->save($completepath, $pdf, true);
                //return( Response::download( $completepath ) );

            }
        }
    }

    public function single_exam_center_practicalnominal_roll_pdf_request(Request $request)
    {

        $request->validate([
            'course' => 'required',
            'stream' => 'required',
            'district_id' => 'required',
            'ecenter' => 'required',
        ]);


        return redirect()->route('single_exam_practical_student_rollwise_pdf', array($request->course, $request->stream, $request->district_id, $request->ecenter));
    }

    public function single_exam_practical_student_rollwise_pdf($course = null, $stream = null, $district_id = null, $ecenter = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $custom_component_obj = new CustomComponent;
        if (@$districtid == 0) {
            $districtNameList = $this->districtsByState($state_id);
        } else {
            $districtNameList = $this->districtNameById($districtid);
        }
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $exam_year = CustomHelper::_get_selected_sessions();
        $subjectType = 'P';
        $custom_component_obj = new CustomComponent;
        $subjectList = $custom_component_obj->_getTPSubjects($course, $subjectType);
        $subject_count = count($subjectList);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $current_year = @$current_folder_year[1];
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $subreportname = "exam_student_rollwise_pdf";
        $aicentermaterial = "examcentermaterial";
        $aicodedistrictids = [];
        foreach ($districtNameList as $districtid => $districtvalue) {
            $centercode = 'ecenter' . $course;
            $centerName = 'cent_name';
            $centercodeandid = ExamcenterDetail::where($centercode, $ecenter)->where('active', 1)->pluck($centercode, 'id')->toArray();
            $centercodeandNames = ExamcenterDetail::where($centercode, $ecenter)->where('active', 1)->pluck($centerName, 'id')->toArray();
            foreach ($centercodeandid as $centerid => $centervalue) {
                $districtNameListid = ExamcenterDetail::where($centercode, $centervalue)->groupBy('district_id')->get('district_id')->toarray();
                $districtNameListname = @$districtNameList[$districtNameListid['0']['district_id']];
                $final_data = array();
                $i = 0;
                foreach ($subjectList as $subjectid => $subjectname) {
                    $supp_conditions = $conditions = array();

                    $conditions['student_allotments.exam_year'] = $exam_year;
                    $conditions['student_allotments.exam_month'] = $stream;
                    $conditions["student_allotments.course"] = $course;
                    $conditions["student_allotments.examcenter_detail_id"] = $centerid;
                    $supp_conditions = $conditions;

                    $conditions['exam_subjects.subject_id'] = $subjectid;
                    $conditions['student_allotments.supplementary'] = 0;
                    $supp_conditions['supplementary_subjects.subject_id'] = $subjectid;
                    $supp_conditions['supplementary_subjects.exam_year'] = $exam_year;
                    $supp_conditions['supplementary_subjects.exam_month'] = $stream;
                    $supp_conditions['student_allotments.supplementary'] = 1;


                    $studentData = array();
                    $studentData = DB::table('student_allotments')->select('student_allotments.enrollment')->join('exam_subjects', 'exam_subjects.student_id', '=', 'student_allotments.student_id')
                        ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                        ->join("students", function ($join) {
                            $join->on("student_allotments.student_id", "=", "students.id");
                            $join->whereRaw("(rs_students.is_eligible = 1)");
                        })->where($conditions)->where(function ($query) {
                            $query->orWhereNull('exam_subjects.final_result')->orWhere(['exam_subjects.final_result' => '!= PASS', 'exam_subjects.final_result' => '!= P', 'exam_subjects.final_result' => '!= p']);
                        })->whereNull('exam_subjects.deleted_at')
                        ->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('examcenter_details.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->groupBy('exam_subjects.student_id')->get()->toArray();

                    $SuppStudentData = array();
                    $SuppStudentData = DB::table('student_allotments')->select('student_allotments.enrollment', 'supplementary_subjects.subject_id', 'student_allotments.student_id')
                        ->join('supplementary_subjects', 'supplementary_subjects.student_id', '=', 'student_allotments.student_id')
                        ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
                        ->join('students', 'students.id', '=', 'student_allotments.student_id')
                        ->join("supplementaries", function ($join) use ($exam_year, $stream) {
                            $join->on("supplementaries.student_id", "=", "student_allotments.student_id");
                            $join->whereRaw("(rs_supplementaries.is_eligible = 1 )");
                            $join->whereRaw("(rs_supplementaries.exam_year = " . $exam_year . " )");
                            $join->whereRaw("(rs_supplementaries.exam_month = " . $stream . " )");
                        })
                        ->where($supp_conditions)->whereNull('student_allotments.deleted_at')->whereNull('examcenter_details.deleted_at')->whereNull('students.deleted_at')->whereNull('supplementary_subjects.deleted_at')->whereNull('supplementaries.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->get()->toArray();


                    $result1 = array();
                    foreach ($SuppStudentData as $SuppStudentData1) {

                        $result = CustomHelper::getStudentResult($SuppStudentData1->student_id, $SuppStudentData1->subject_id);

                        if ($course == 12) {
                            if (empty($result) || $result == 888 || $result == 666) {

                                $result1[] = $SuppStudentData1;

                            }
                        } elseif ($course == 10) {
                            if (empty($result) || $result == 888) {

                                $result1[] = $SuppStudentData1;


                            }
                        }

                    }

                    $final_data[$i]['subject_id'] = $subjectid;
                    $final_data[$i]['subject_name'] = $subjectname;
                    $final_data[$i]['cent_code'] = $centervalue;
                    $final_data[$i]['cent_name'] = @$centercodeandNames[@$centerid];
                    $final_data[$i]['studentData'] = $studentData;
                    $final_data[$i]['suppStudentData'] = $result1;
                    $i++;
                }


                //return view('examination_reports.exam_student_rollwise_pdf',compact('current_year','stream','final_data','subjectList','subject_count','examcenter_detail_id','examCenterDetail','examDates'));

                $pdf = PDF::loadView('examination_reports.exam_practical_student_rollwise_pdf', compact('current_year', 'stream', 'final_data', 'subjectList', 'centervalue', 'subject_count', 'exam_session', 'centercodeandNames', 'course'));

                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $districtNameListname . "/" . $centervalue . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                $pdf->setOption('footer-right', 'Page [page] of [toPage]');

                $filename = 'examcenter_practicalroll' . $centervalue . '.pdf';
                $completepath = $path . $filename;

                $pdf->save($completepath, $pdf, true);
                return (Response::download($completepath));

            }
        }
    }

    public function enrollment_fixcode_view_bulk($subjectscode = null, $stream = null, $course = null, $start = null)
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
            $subjectList = $custom_component_obj->_getTPSubjectscode();
        }

        $title = "fixcode";
        $current_folder_year = $this->master_details('current_folder_year');
        $current_year = @$current_folder_year[1];
        $aicentermaterial = "fixcode";
        $custom_component_obj = new CustomComponent;
        $subjectListname = $custom_component_obj->_getTPSubjectsname();
        $exam_year = CustomHelper::_get_selected_sessions();
        $conditions = array();
        $supp_conditions = array();
        $centercode = 'ecenter' . $course;
        $centerName = 'cent_name';
        $fixcode = 'fixcode';

        foreach ($subjectList as $subjectid => $subjectname) {
            $centercodeandids = ExamcenterDetail::where('active', 1)->pluck($centercode, 'id')->toArray();
            $centercodeandNames = ExamcenterDetail::where('active', 1)->pluck($centerName, 'id')->toArray();
            $centerfixcode = ExamcenterDetail::where('active', 1)->pluck($fixcode, 'id')->toArray();

            foreach ($centercodeandids as $centerid => $centervalue) {
                $final_data = array();
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

                $final_data[$i]['subject_name'] = $subjectname;
                $final_data[$i]['subject_id'] = $subjectid;
                $final_data[$i]['cent_code'] = $centervalue;
                $final_data[$i]['cent_name'] = @$centercodeandNames[@$centerid];
                $final_data[$i]['fix_code'] = @$centerfixcode[@$centerid];
                $final_data[$i]['studentData'] = $data;
                $i++;

                //return view('examination_reports.enrollment_fixcode_view',compact('current_year','stream','exam_session','final_data','subjectList','centervalue','course','subjectListname'));

                if (count($data) > 0) {

                    $pdf = PDF::loadView('examination_reports.enrollment_fixcode_view', compact('current_year', 'stream', 'exam_session', 'final_data', 'subjectList', 'centervalue', 'course', 'subjectListname'));
                } else {
                    continue;
                }


                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $subjectname . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                // $pdf->setOption('header-html', base_path('views/Modulos/Funcional/OrdemServico/Os/header.blade.php'));
                // $pdf->setOption('header-html', View::make('path.to.header')->render());
                //$pdf->setOption('header-html', "Hiii this is name");
                // $pdf->setOption('--no-header-line',true);
                $pdf->setOption("encoding", "UTF-8");
                $pdf->setOption('margin-left', '0mm');
                $pdf->setOption('margin-right', '0mm');
                $pdf->setOption('margin-top', '0mm');
                // $pdf->setOption('footer-right', 'Page [page] of [toPage]');
                $filename = 'student_fixcode_code_' . $centervalue . '.pdf';
                $completepath = $path . $filename;
                $pdf->save($completepath, $pdf, true);

                //return( Response::download( $completepath ) );


            }


        }

        echo "Today is Done " . date("Y/m/d") . "<br>";

    }

    public function enrollment_fixcode_view($subjectscode = null, $stream = null, $course = null, $start = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $custom_component_obj = new CustomComponent;
        // if(@$districtid == 0){
        // 	$districtNameList = $this->districtsByState($state_id);
        // }else{
        // 	$districtNameList = $this->districtNameById($districtid);
        // }

        $title = "fixcode";
        $current_folder_year = $this->master_details('current_folder_year');
        $current_year = @$current_folder_year[1];
        $aicentermaterial = "fixcode";
        $custom_component_obj = new CustomComponent;
        $subjectList = $custom_component_obj->_getTPSubjectscode($subjectscode);
        $subjectListname = $custom_component_obj->_getTPSubjectsname($subjectscode);
        $exam_year = CustomHelper::_get_selected_sessions();
        $conditions = array();
        $supp_conditions = array();
        $centercode = 'ecenter' . $course;
        $centerName = 'cent_name';
        $fixcode = 'fixcode';
        // foreach($districtNameList  as $districtid => $districtvalue){
        $centercodeandids = ExamcenterDetail::where('active', 1)->pluck($centercode, 'id')->toArray();
        $centercodeandNames = ExamcenterDetail::where('active', 1)->pluck($centerName, 'id')->toArray();
        $centerfixcode = ExamcenterDetail::where('active', 1)->pluck($fixcode, 'id')->toArray();

        foreach ($centercodeandids as $centerid => $centervalue) {
            $final_data = array();
            $i = 0;
            foreach ($subjectList as $subjectid => $subjectname) {
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

                $final_data[$i]['subject_name'] = $subjectname;
                $final_data[$i]['subject_id'] = $subjectid;
                $final_data[$i]['cent_code'] = $centervalue;
                $final_data[$i]['cent_name'] = @$centercodeandNames[@$centerid];
                $final_data[$i]['fix_code'] = @$centerfixcode[@$centerid];
                $final_data[$i]['studentData'] = $data;
                $i++;
            }


            //return view('examination_reports.enrollment_fixcode_view',compact('current_year','stream','exam_session','final_data','subjectList','centervalue','course','subjectListname'));

            if (count($data) > 0) {

                $pdf = PDF::loadView('examination_reports.enrollment_fixcode_view', compact('current_year', 'stream', 'exam_session', 'final_data', 'subjectList', 'centervalue', 'course', 'subjectListname'));
            } else {
                continue;
            }

            $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $subjectscode . "/";
            $path = public_path("files/reports/" . $nextPath);

            File::makeDirectory($path, $mode = 0777, true, true);
            // $pdf->setOption('header-html', base_path('views/Modulos/Funcional/OrdemServico/Os/header.blade.php'));
            // $pdf->setOption('header-html', View::make('path.to.header')->render());
            //$pdf->setOption('header-html', "Hiii this is name");
            // $pdf->setOption('--no-header-line',true);
            $pdf->setOption("encoding", "UTF-8");
            $pdf->setOption('margin-left', '0mm');
            $pdf->setOption('margin-right', '0mm');
            $pdf->setOption('margin-top', '0mm');
            // $pdf->setOption('footer-right', 'Page [page] of [toPage]');
            $filename = 'student_fixcode_code_' . $centervalue . '.pdf';
            $completepath = $path . $filename;
            $pdf->save($completepath, $pdf, true);

            //return( Response::download( $completepath ) );


        }
        echo "Today is Done " . date("Y/m/d") . "<br>";

        // }

    }

    public function enrollment_fixcode_views()
    {

        $title = "FIX CODE ";
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
        $user_role = Session::get('role_id');
        return view('examination_reports.enrollment_fixcode_views', compact('courses', 'stream_id', 'title', 'breadcrumbs', 'empty'));


    }

    public function enrollment_fixcode_views_requests(Request $request)
    {

        $request->validate([
            'course' => 'required',
            'stream' => 'required',
            'subjects' => 'required',
            'ecenter' => 'required',
        ]);


        return redirect()->route('single_enrollment_fixcode_view', array($request->subjects, $request->stream, $request->course, $request->ecenter));
    }

    public function single_enrollment_fixcode_view($subjectscode = null, $stream = null, $course = null, $ecenter = null)
    {

        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $custom_component_obj = new CustomComponent;
        // if(@$districtid == 0){
        //     $districtNameList = $this->districtsByState($state_id);
        // }else{
        //     $districtNameList = $this->districtNameById($districtid);
        // }

        $title = "fixcode";
        $current_folder_year = $this->master_details('current_folder_year');
        $current_year = @$current_folder_year[1];
        $aicentermaterial = "fixcode";
        $custom_component_obj = new CustomComponent;
        $subjectList = $custom_component_obj->_getTPSubjectscode($subjectscode);
        $subjectListname = $custom_component_obj->_getTPSubjectsname($subjectscode);


        $exam_year = CustomHelper::_get_selected_sessions();
        $conditions = array();
        $supp_conditions = array();
        $centercode = 'ecenter' . $course;
        $centerName = 'cent_name';
        $fixcode = 'fixcode';
        // foreach($districtNameList  as $districtid => $districtvalue){
        $centercodeandids = ExamcenterDetail::where($centercode, $ecenter)->where('active', 1)->pluck($centercode, 'id')->toArray();
        $centercodeandNames = ExamcenterDetail::where($centercode, $ecenter)->where('active', 1)->pluck($centerName, 'id')->toArray();
        $centerfixcode = ExamcenterDetail::where($centercode, $ecenter)->where('active', 1)->pluck($fixcode, 'id')->toArray();

        foreach ($centercodeandids as $centerid => $centervalue) {
            $final_data = array();
            $i = 0;
            foreach ($subjectList as $subjectid => $subjectname) {
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
                    'examcenter_details.ecenter12', 'examcenter_details.cent_name', 'exam_subjects.final_result')->join('exam_subjects', 'exam_subjects.student_id', '=', 'student_allotments.student_id')
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

                $final_data[$i]['subject_name'] = $subjectname;
                $final_data[$i]['subject_id'] = $subjectid;
                $final_data[$i]['cent_code'] = $centervalue;
                $final_data[$i]['cent_name'] = @$centercodeandNames[@$centerid];
                $final_data[$i]['fix_code'] = @$centerfixcode[@$centerid];
                $final_data[$i]['studentData'] = $data;
                $i++;
            }


            //return view('examination_reports.enrollment_fixcode_view',compact('current_year','stream','exam_session','final_data','subjectList','centervalue','course','subjectListname'));


            $pdf = PDF::loadView('examination_reports.enrollment_fixcode_view', compact('current_year', 'stream', 'exam_session', 'final_data', 'subjectList', 'centervalue', 'course', 'subjectListname'));

            $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $subjectscode . "/";
            $path = public_path("files/reports/" . $nextPath);

            File::makeDirectory($path, $mode = 0777, true, true);
            // $pdf->setOption('header-html', base_path('views/Modulos/Funcional/OrdemServico/Os/header.blade.php'));
            // $pdf->setOption('header-html', View::make('path.to.header')->render());
            //$pdf->setOption('header-html', "Hiii this is name");
            // $pdf->setOption('--no-header-line',true);
            $pdf->setOption("encoding", "UTF-8");
            $pdf->setOption('margin-left', '0mm');
            $pdf->setOption('margin-right', '0mm');
            $pdf->setOption('margin-top', '0mm');
            $filename = 'student_fixcode_code_' . $centervalue . '.pdf';
            $completepath = $path . $filename;
            $pdf->save($completepath, $pdf, true);

            return (Response::download($completepath));


        }


        // }

    }

    public function SupplementaryChecklists(Request $request)
    {
        $title = "supplementary checklist Report";
        $table_id = "ViewSupplementary_Checklists_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));
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
                "label" => "Export TOC Checklist",
                'url' => 'downloadsupplementaryCheckListsPdf',
                'status' => true
            )
        );

        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);

        return view('examination_reports.supplementary_check_lists_report', compact('aiCenters', 'exportBtn', 'title', 'breadcrumbs', 'courses', 'stream_id'));
    }

    public function downloadsupplementarychecklistsPdf1(Request $request)
    {

        $request->validate([
            'ai_code' => 'required',
            'stream' => 'required',
            'course' => 'required',
        ]);


        return redirect()->route('downloadsupplementaryCheckListsPdf', array($request->course, $request->stream, $request->ai_code));
    }

    public function downloadsupplementaryCheckListsPdf($course = null, $stream = null, $ai_code = null, Request $request)
    {

        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $custom_component_obj = new CustomComponent;
        if (@$ai_code) {
            $aiCenters = $custom_component_obj->getAiCenters($ai_code);
        } elseif (@$ai_code == 0) {
            $aiCenters = $custom_component_obj->getAiCenters();

        }

        $title = "Supplementary checklist Report";
        $table_id = "Toc_Checklists_Report";
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $genders = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'category_b';
        $category_b = $this->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $this->master_details($combo_name);
        $combo_name = 'year';
        $year = $this->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $this->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $this->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);
        $combo_name = 'minage';
        $minage = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);

        $rsos_years = $this->getListRsosYears();
        $combo_name = "year";
        $rsos_years = $this->master_details($combo_name);
        $tocpassyear = DB::table('rsos_years')->pluck('yearstext', 'id');
        $tocpassfail = DB::table('rsos_years_fail')->pluck('yearstext', 'id');

        $current_admission_session_id = Config::get("global.current_admission_session_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");

        $subjectCodes = $this->subjectCodeList($course);
        // dd($subjectCodes);

        $subjectCodes = $this->subjectCodeList($course);
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);

        $studentDocumentPath = $student_document_path[1];
        $practicalsubjects12 = DB::table('subjects')->where('practical_type', 1)->where('course', $course)->whereNull('deleted_at')->orderBy('subject_code')
            ->pluck('id')->toArray();
        $boards = $this->getBoardList();
        $rsos_yearsstudent = $this->rsos_years();

        // $boards = $this->getRsosYearsList();

        $aicode = [];
        $checklist = "supplementarychecklist";
        foreach ($aiCenters as $key => $value) {
            @$aicodetemp = $key;
            @$aicode = $key;
            $conditions = array();
            $conditions["supplementaries.ai_code"] = $aicode;
            $conditions["supplementaries.exam_month"] = $stream;
            $conditions["supplementaries.course"] = $course;
            $conditions["supplementaries.is_eligible"] = 1;
            $conditions["supplementaries.exam_year"] = CustomHelper::_get_selected_sessions();

            @$reportname = $aicodetemp;

            $suppStudents = Supplementary::select('students.enrollment',
                'students.name', 'students.mobile', 'students.id')
                ->join('students', 'students.id', '=', 'supplementaries.student_id')
                ->join('supplementary_subjects', 'supplementary_subjects.supplementary_id', '=', 'supplementaries.id')
                ->where($conditions)->whereNull('students.deleted_at')->whereNull('supplementary_subjects.deleted_at')
                ->orderBy('supplementaries.student_id', 'ASC')
                ->groupBy('supplementaries.student_id')
                ->get();

            $dataSave = array();
            $key = 0;

            if (isset($suppStudents) && !empty($suppStudents)) {
                foreach ($suppStudents as $suppKey => $suppStudent) {
                    $dataSave[$key]['id'] = $suppStudent->id;
                    $dataSave[$key]['mobile'] = $suppStudent->mobile;
                    $dataSave[$key]['enrollment'] = $suppStudent->enrollment;
                    $dataSave[$key]['name'] = $suppStudent->name;
                    $dataSave[$key]['exam_subjects'] = null;
                    $dataSave[$key]['exam_subjects'] = $this->getSubjectDetailForSupp($suppStudent->id);
                    $key++;
                }

            }

            $master = $dataSave;

            // $master = Supplementary::where($conditions)->with('SupplementarySubject','student')->get();;

            //return view('examination_reports.supplementarieschecklists_pdf',compact('categorya','midium','genders','subjectCodes','rsos_years','master','studentDocumentPath','aicode','courses','course','boards','adm_types','stream','reportname','rsos_yearsstudent','tocpassyear','tocpassfail','practicalsubjects12','admission_sessions','current_admission_session_id'));

            $pdf = PDF::loadView('examination_reports.supplementarieschecklists_pdf', compact('categorya', 'midium', 'genders', 'subjectCodes', 'rsos_years', 'master', 'studentDocumentPath', 'aicode', 'courses', 'course', 'boards', 'adm_types', 'stream', 'reportname', 'rsos_yearsstudent', 'tocpassyear', 'tocpassfail', 'practicalsubjects12', 'admission_sessions', 'current_admission_session_id'))->setOrientation('landscape');
            $pdf->setOption('footer-right', 'Page [page] of [toPage]');
            $path = public_path("files/reports/" . $current_folder_year[1] . "/" . $checklist . "/stream" . $stream . "/" . $course . "/");
            File::makeDirectory($path, $mode = 0777, true, true);
            if (@$aicode) {
                $filename = $aicode . "_Supplementary__checklist.pdf";
            } else {
                $filename = $aicode . "_Supplementary_checklist.pdf";
            }

            $completepath = $path . $filename;
            $pdf->save($completepath, $pdf, true);
        }
        if (@$ai_code) {
            return redirect()->route('SupplementaryChecklists')->with('message', 'Supplementary Checklists Generated successfully');
            //return( Response::download($completepath));
        } elseif (@$ai_code == 0) {
            /*$zip_file_name = $course . "_" .  "stream".$stream .  "Supplementary_checklist.zip";
		 $folder_path = "files/reports/" . $current_folder_year[1]."/". $checklist ."/". "stream" . $stream ."/". $course;
		 $folder_path = public_path($folder_path);
		 $zip_file = $this->_zipAndDownload($folder_path,$zip_file_name);
		 return response()->download($zip_file);*/
            return redirect()->route('SupplementaryChecklists')->with('message', 'Supplementary Checklists Generated successfully');
        }
    }

    public function examcentersubjectscount($course = null)
    {
        $examcenter_exl_data = (new CenterCountExlExport($course));
        return Excel::download($examcenter_exl_data, 'examcentersubjectscount.xlsx');
    }

    //check procedure for generate fixcode

    public function aicodewisesubjectsdatastudents()
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

        return view('examination_reports.aicodewisesubjectsdatastudents', compact('courses', 'stream_id', 'title', 'breadcrumbs', 'developeradminrole', 'user_role', 'district_list', 'empty', 'midium'));
    }

    public function downloadaicodewisesubjectsdatastudents(Request $request, $type = "xlsx")
    {

        $request->validate([
            'course' => 'required',
            'stream' => 'required',

        ]);

        return Excel::download(new AicentersubjectCountExlExport($request), 'Aicodesubjectswise studentdata.xlsx');
    }

    public function fixcodegenerate($stream = null)
    {
        $aicodecountround10_arr = array();
        for ($p = 10; $p < 700; $p += 10) {
            $aicodecountround10_arr[] = $p;
        }

        $fixcodeupdatedata = DB::select('call generateFixcodeData(?,?,?)', array(10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200, 210, 220, 230, 240, 250, 260, 270, 280, 290, 300, 310, 320, 330, 340, 350, 360, 370, 380, 390, 400, 410, 420, 430, 440, 450, 460, 470, 480, 490, 500, 510, 520, 530, 540, 550, 560, 570, 580, 590, 600, 610, 620, 630, 640, 650, 660, 670, 680, 690), 124, 1);
        dd($fixcodeupdatedata);


    }

    public function fixcodegenerate2($stream = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $aicode_conditions['student_allotments.exam_year'] = Config::get('global.current_admission_session_id');
        $aicode_conditions['student_allotments.exam_month'] = $stream;
        //$aicodemaster = DB::table('student_allotments')->select('student_allotments.ai_code',DB::raw('count(rs_student_allotments.enrollment) as student_count'))->where($aicode_conditions)->groupBy('student_allotments.ai_code')->skip(20)->take(532)->orderBy('student_count', 'DESC')->get()->toArray();
        $aicodemaster = DB::table('student_allotments')->select('student_allotments.ai_code', DB::raw('count(rs_student_allotments.enrollment) as student_count'))->where($aicode_conditions)->groupBy('student_allotments.ai_code')->orderBy('student_count', 'DESC')->get()->toArray();
        $fixcodeprefix = 0;
        $fixcodenotallottedarr = array();
        $aicodecountround10_arr = array();
        for ($p = 10; $p < 700; $p += 10) {
            $aicodecountround10_arr[] = $p;
        }
        //dd($aicodecountround10_arr);

        if (!empty($aicodemaster)) {
            $aicounter = 1;
            foreach ($aicodemaster as $k => $starr) {
                if (in_array($aicounter, array_values($aicodecountround10_arr))) {
                    $aicounter = $aicounter + 1;
                }
                $aiidstr = strlen((string)$aicounter);
                if ($aiidstr == 1)
                    $fixcodeprefix = $aicounter * 100000;
                if ($aiidstr == 2)
                    $fixcodeprefix = $aicounter * 10000;
                if ($aiidstr == 3)
                    $fixcodeprefix = $aicounter * 1000;

                $conditions = array();
                $conditions['exam_year'] = Config::get('global.current_admission_session_id');
                $conditions['exam_month'] = $stream;;
                $conditions['ai_code'] = $starr->ai_code;
                $studentarr = StudentAllotmentHardDelete::withTrashed()
                    ->where($conditions)
                    ->get(['id', 'enrollment']);
                if (!empty($studentarr)) {
                    $encount = 1;
                    foreach ($studentarr as $stenkey => $stenval) {
                        $fixcode = $fixcodeprefix + $encount;
                        $fixconditions = array();
                        $fixconditions['student_allotments.fixcode'] = $fixcode;
                        $fixconditions['student_allotments.exam_year'] = Config::get('global.current_admission_session_id');
                        $fixconditions['student_allotments.exam_month'] = $stream;
                        //$fixconditions['StudentAllotment.deleted'] = 0;
                        //$studentexist = $this->StudentAllotment->find('first',array('fields'=>array('id','enrollment','fixcode'),'conditions'=>$fixconditions));
                        $studentexist = DB::table('student_allotments')->select('student_allotments.id', 'student_allotments.enrollment', 'student_allotments.fixcode')->where($fixconditions)->get();
                        $fixdata = array();

                        if (@$studentexist) {
                            $studentarray = ['fixcode' => $fixcode];
                            $Student = StudentAllotment::where('id', $stenval->id)->update($studentarray);
                        } else {
                            $fixcodenotallottedarr[] = $studentexist['StudentAllotment'];
                        }
                        $encount++;
                    }
                }
                $aicounter++;

            }
        }
        //$aicodearr = $this->StudentAllotment->find('all',array('fields'=>array('DISTINCT `StudentAllotment`.`ai_code`','COUNT(enrollment)'),'conditions'=>$aicode_conditions,'group'=>'ai_code','order'=>'COUNT(enrollment) DESC'));

    }

    public function aicodewisesubjectsdatastudentsmediumtype()
    {

        $title = "Ai code wise subject data student midium";
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

        return view('examination_reports.aicodewisesubjectsdatastudentsmediumtype', compact('courses', 'stream_id', 'title', 'breadcrumbs', 'developeradminrole', 'user_role', 'district_list', 'empty', 'midium'));
    }

    public function downloadaicodewisesubjectsdatastudentsmediumtype(Request $request, $type = "xlsx")
    {

        $request->validate([
            'course' => 'required',
            'stream' => 'required',
            'midium' => 'required',
        ]);


        return Excel::download(new AicentersubjectCountStudentExlExport($request), 'Aicodesubjectswise studentdata.xlsx');
    }

    public function aicodewisesubjectsdataallotmentstudentsdata()
    {

        $title = "Ai code wise subject data student supp and Fersh";
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

        return view('examination_reports.aicodewisesubjectsdataallotmentstudentsdata', compact('courses', 'stream_id', 'title', 'breadcrumbs', 'developeradminrole', 'user_role', 'district_list', 'empty', 'midium'));
    }

    public function downloadaicodewisesubjectsdataallotmentstudentsdata(Request $request, $type = "xlsx")
    {

        $request->validate([
            'course' => 'required',
            'stream' => 'required',

        ]);


        return Excel::download(new AicentersubjectCountAllotmentstudentsuppExlExport($request), 'Aicodesubjectswise studentdata.xlsx');
    }

    public function getDownloadnominalrollexamcenterwise(Request $request)
    {

        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $aicentermaterial = "examcentermaterial";
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $examination_department = Config::get("global.examination_department");
        $developeradminrole = Config::get("global.developer_admin");
        $District = $this->districtOnlyNameById($request->district_id);

        $filename = $request->type . $request->ecenter . '.pdf';
        $path = public_path("files/reports/" . $current_folder_year[1] . "/" . $aicentermaterial . "/" . $request->stream . "/" . $request->course . "/" . $District->name . "/" . $request->ecenter . "/" . $filename);

        return Response::download($path);

    }

    // public function getDownloadhallticketaicodecenterwiserequest($course =null, $stream =null){

    // 	$user_role = Session::get('role_id');
    // 	$combo_name = 'current_folder_year';$current_folder_year = $this->master_details($combo_name);
    // 	$aicentermaterial ="aicentermaterial";

    // 	$current_admission_session_id = Config::get("global.admission_academicyear_id");
    // 	$current_exam_month_id = Config::get("global.current_exam_month_id");
    // 	$examination_department = Config::get("global.examination_department");
    // 	$developeradminrole = Config::get("global.developer_admin");
    // 	    $ai_code = Auth::user()->ai_code;
    // 	    $id = Auth::user()->id;

    // 		if(!empty($id) && !empty($ai_code) ){}else{
    // 			return redirect()->back()->with('error', $ai_code . ' Something is not working.Please try again!');
    // 		}

    // 	$districtId = $useraicodeid = User::where('ai_code',$ai_code)->where('exam_year',$current_admission_session_id)->where('exam_month',$current_exam_month_id)->pluck('district_id','district_id')->first();

    // 	$District = $this->districtOnlyNameById($districtId);

    // 	$filename = 'hallticket_' . $ai_code . '.pdf';
    // 	$path = public_path("files/reports/" . $current_folder_year[1]. "/" . $aicentermaterial . "/". $stream . "/".$course ."/".$District->name ."/".$ai_code."/".	$filename);

    //     return Response::download($path);

    public function getpublishaicentermaterial(Request $request)
    {

        $title = "Generate";
        $table_id = "Generate";
        $combo_name = $defaultPageLimit = config("global.defaultPageLimit");
        $master = DB::table('masters')->groupBy('combo_name')->pluck('combo_name', 'combo_name');
        $formId = ucfirst(str_replace(" ", "_", $title));
        $conditions = array();
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
                'url' => 'downloaduserExl',
                'status' => false,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloaduserPdf',
                'status' => false
            )
        );

        $user_role = Session::get('role_id');
        $examination_department = Config::get("global.examination_department");
        $combo_nameconditions = null;
        $filters = array();
        if ($user_role == $examination_department) {
            $combo_nameconditions = array("is_exam_center_material_publish", "is_ai_center_material_publish");
        } else {
            $filters = array(
                array(
                    "lbl" => "Combo Name",
                    'fld' => 'combo_name',
                    'input_type' => 'select',
                    'options' => $master,
                    'placeholder' => 'Combo Name',
                ),
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
        $custom_component_obj = new CustomComponent;
        $getmasterdata = $custom_component_obj->getmasteralldata($formId, true, $combo_nameconditions);

        return view('examination_reports.aicentermaterialgenerate', compact('breadcrumbs', 'examination_department', 'exportBtn', 'title', 'filters', 'getmasterdata', 'user_role'));

    }


    public function getpublishaicentermaterialedit(Request $request, $id)
    {

        $user_role = Session::get('role_id');
        $examination_department = Config::get("global.examination_department");
        //$examination_department = 71;
        if ($user_role == $examination_department) {
            $combo_nameconditions = array("is_exam_center_material_publish", "is_ai_center_material_publish");
        }

        if (count($request->all()) > 0) {
            if ($user_role == $examination_department) {
                $studentarray = [
                    'option_val' => $request->option_val
                ];
            } else {
                $studentarray = [
                    'option_id' => $request->option_id,
                    'option_val' => $request->option_val,
                    'status' => $request->status,
                ];
            }


            $updated = DB::table('masters')
                ->where('id', $id)
                ->update($studentarray);
            if ($updated) {
                return redirect()->route('getpublishaicentermaterial')->with('message', 'Generate  successfully.');
            } else {
                return redirect()->route('getpublishaicentermaterial')->with('error', 'Failed! Generate');
            }
        } else {
            $model = "useradd";
            $status = array("1" => "Active", "2" => "InActive");
            $publishedOptions = array('true' => "Publish", "false" => "Un-Publish");
            $user = DB::table('masters')->where('id', $id)->first();
            return view('examination_reports.getpublishaicentermaterialedit', compact('user', 'model', 'user_role', 'examination_department', 'status', 'publishedOptions'));
        }
    }


    public function single_exam_center_attendance_roll_pdf_view()
    {
        $title = "Exam Center Attendance Roll";
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
        $user_role = Session::get('role_id');
        $district_list = $this->districtsByState(6);
        $type = "examcenter_attendanceroll_";
        $path = "single_exam_center_attendance_roll_pdf_request";
        $action = "excenterattendancesheet";
        return view('examination_reports.single_material_reports', compact('path', 'type', 'action', 'courses', 'stream_id', 'title', 'breadcrumbs', 'developeradminrole', 'user_role', 'district_list', 'empty'));
    }

    public function single_exam_center_nominal_roll_pdf_view()
    {
        $title = "Exam Center Nominal Roll";
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
        $user_role = Session::get('role_id');
        $district_list = $this->districtsByState(6);
        $type = "examcenter_nominalroll_";
        $path = "single_exam_center_nominal_roll_pdf_request";
        $action = "exam_center_nominal_roll_pdf";
        return view('examination_reports.single_material_reports', compact('path', 'type', 'action', 'courses', 'stream_id', 'title', 'breadcrumbs', 'developeradminrole', 'user_role', 'district_list', 'empty'));
    }

    public function single_exam_center_theorynominal_roll_pdf_view()
    {
        $title = "Exam Center Theory Roll";
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
        $user_role = Session::get('role_id');
        $district_list = $this->districtsByState(6);
        $type = "examcenter_theoryroll";
        $path = "single_exam_center_theorynominal_roll_pdf_request";
        $action = "exam_student_rollwise_pdf";
        return view('examination_reports.single_material_reports', compact('path', 'type', 'action', 'courses', 'stream_id', 'title', 'breadcrumbs', 'developeradminrole', 'user_role', 'district_list', 'empty'));
    }

    public function single_exam_center_practicalnominal_roll_pdf_view()
    {
        $title = "Exam Center Practical Nominal Roll";
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
        $user_role = Session::get('role_id');
        $district_list = $this->districtsByState(6);
        $type = "examcenter_practicalroll";
        $path = "single_exam_center_practicalnominal_roll_pdf_request";
        $action = "exam_practical_student_rollwise_pdf";
        return view('examination_reports.single_material_reports', compact('path', 'type', 'action', 'courses', 'stream_id', 'title', 'breadcrumbs', 'developeradminrole', 'user_role', 'district_list', 'empty'));
    }

    public function single_exam_center_practicalsignaturenominal_roll_pdf_view()
    {
        $title = "Exam Center Practical signature Nominal Roll";
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
        $user_role = Session::get('role_id');
        $district_list = $this->districtsByState(6);
        $type = "examcenter_practicalsignatureroll_";
        $path = "single_exam_center_practicalsignaturenominal_roll_pdf_request";
        $action = "exam_subjectpractical_student_rollwise_pdf";
        return view('examination_reports.single_material_reports', compact('path', 'type', 'action', 'courses', 'stream_id', 'title', 'breadcrumbs', 'developeradminrole', 'user_role', 'district_list', 'empty'));
    }

    public function single_exam_center_theorysignaturenominal_roll_pdf_view()
    {
        $title = "Exam Center Theory signature Nominal Roll";
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
        $user_role = Session::get('role_id');
        $district_list = $this->districtsByState(6);
        $type = "examcenter_theorysignatureroll_";
        $path = "single_exam_center_theorysignaturenominal_roll_pdf_request";
        $action = "exam_subjectthory_student_rollwise_pdf";
        return view('examination_reports.single_material_reports', compact('path', 'type', 'action', 'courses', 'stream_id', 'title', 'breadcrumbs', 'developeradminrole', 'user_role', 'district_list', 'empty'));
    }

    public function allcenterwisetocchecklist(Request $request)
    {

        $title = "All Ai code wise Toc checklist ";
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

        if (count($request->all()) > 0) {
            $request->validate([
                'course' => 'required',
                'stream' => 'required'
            ]);

            return redirect()->route('downloadTocCheckListsallaicenterwisePdf', array($request->course, $request->stream));
        }
        return view('examination_reports.allaicodewisetocchecklist', compact('courses', 'stream_id', 'title', 'breadcrumbs', 'developeradminrole', 'user_role', 'district_list', 'empty', 'midium'));


    }


    public function downloadTocCheckListsallaicenterwisePdf($course = null, $stream = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $genders = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'category_b';
        $category_b = $this->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $this->master_details($combo_name);
        $combo_name = 'year';
        $year = $this->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $this->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $this->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);
        $combo_name = 'minage';
        $minage = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $combo_name = "year";
        $rsos_years = $this->master_details($combo_name);
        $subjectCodes = $this->subjectCodeList($course);
        $tocpassyear = DB::table('rsos_years')->pluck('yearstext', 'id');
        $tocpassfail = DB::table('rsos_years_fail')->pluck('yearstext', 'id');
        $boards = $this->getBoardList();
        $rsos_yearsstudent = $this->rsos_years();
        $conditions = array();
        $conditions["students.stream"] = $stream;
        $conditions["students.course"] = $course;
        $conditions["students.is_eligible"] = 1;
        $conditions_applications["applications.toc"] = 1;
        $conditions["students.exam_year"] = Config::get("global.admission_academicyear_id");


        $master = Student::where($conditions)
            ->with('application', 'toc', 'toc_subject')->whereRelation('application', $conditions_applications)->orderByRaw('CAST( IF( LEFT(ai_code, 1) =0 , SUBSTR(ai_code, 2),ai_code)  AS UNSIGNED)', 'ASC')->orderBy('enrollment', 'ASC')->get();


        $pdf = PDF::loadView('examination_reports.toc_checklists_pdf2', compact('categorya', 'midium', 'genders', 'subjectCodes', 'rsos_years', 'master', 'courses', 'course', 'boards', 'adm_types', 'stream', 'rsos_yearsstudent', 'tocpassyear', 'tocpassfail', 'stream'));
        $pdf->setOption('footer-right', 'Page [page] of [toPage]');

        $filename = "toc_checklist.pdf";
        return $pdf->download('toc_checklist.pdf');


    }

    public function selectfixcodewisedata(Request $request)
    {

        $title = "Fixcode";
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

        return view('examination_reports.selectfixcode', compact('courses', 'stream_id', 'title', 'breadcrumbs', 'developeradminrole', 'user_role', 'district_list', 'empty', 'midium'));


    }


    public function selected_enrollment_fixcode_view_bulk(Request $request)
    {

        $course = $request->course;
        $stream = $request->stream;
        $tempStudentIds1 = $request->tempStudentIds1;
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $state_id = 6;
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $custom_component_obj = new CustomComponent;
        $subjectList = $custom_component_obj->_getTPSubjectscode();
        $title = "fixcode";
        $current_folder_year = $this->master_details('current_folder_year');
        $current_year = @$current_folder_year[1];
        $aicentermaterial = "fixcode";
        $custom_component_obj = new CustomComponent;
        $subjectListname = $custom_component_obj->_getTPSubjectsname();
        $exam_year = CustomHelper::_get_selected_sessions();
        $conditions = array();
        $supp_conditions = array();
        $centercode = 'ecenter' . $course;
        $centerName = 'cent_name';
        $fixcode = 'fixcode';

        $tempStudentIds = (explode(',', $tempStudentIds1));

        $uniqueExamCenterIds = StudentAllotmentMark::whereIn('student_id', $tempStudentIds)->where('exam_year', $exam_year)->where('exam_month', $stream)->groupBy('examcenter_detail_id')->get('examcenter_detail_id');

        $centercodearray = ExamcenterDetail::whereIn('id', $uniqueExamCenterIds)->where('active', 1)->pluck($centercode, 'id')->toArray();
        foreach ($subjectList as $subjectid => $subjectname) {

            $centercodeandids = ExamcenterDetail::whereIn($centercode, $centercodearray)->where('active', 1)->pluck($centercode, 'id')->toArray();

            $centercodeandNames = ExamcenterDetail::whereIn($centercode, $centercodearray)->where('active', 1)->pluck($centerName, 'id')->toArray();
            $centerfixcode = ExamcenterDetail::whereIn($centercode, $centercodearray)->where('active', 1)->pluck($fixcode, 'id')->toArray();

            foreach ($centercodeandids as $centerid => $centervalue) {
                $final_data = array();
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
                    })->where($conditions)->whereIn('student_allotments.student_id', $tempStudentIds)->where(function ($query) {
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
                    })->where($supp_conditions)->whereIn('student_allotments.student_id', $tempStudentIds)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('supplementaries.deleted_at')->whereNull('supplementary_subjects.deleted_at')->orderBy('student_allotments.enrollment', 'ASC')->get()->toArray();


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


                $final_data[$i]['subject_name'] = $subjectname;
                $final_data[$i]['subject_id'] = $subjectid;
                $final_data[$i]['cent_code'] = $centervalue;
                $final_data[$i]['cent_name'] = @$centercodeandNames[@$centerid];
                $final_data[$i]['fix_code'] = @$centerfixcode[@$centerid];
                $final_data[$i]['studentData'] = $data;
                $i++;

                //return view('examination_reports.enrollment_fixcode_view',compact('current_year','stream','exam_session','final_data','subjectList','centervalue','course','subjectListname'));

                if (count($data) > 0) {
                    $pdf = PDF::loadView('examination_reports.enrollment_fixcode_view', compact('current_year', 'stream', 'exam_session', 'final_data', 'subjectList', 'centervalue', 'course', 'subjectListname'));
                } else {
                    continue;
                }


                $nextPath = $current_folder_year[1] . "/" . $aicentermaterial . "/" . $stream . "/" . $course . "/" . $subjectname . "/";
                $path = public_path("files/reports/" . $nextPath);

                File::makeDirectory($path, $mode = 0777, true, true);
                // $pdf->setOption('header-html', base_path('views/Modulos/Funcional/OrdemServico/Os/header.blade.php'));
                // $pdf->setOption('header-html', View::make('path.to.header')->render());
                //$pdf->setOption('header-html', "Hiii this is name");
                // $pdf->setOption('--no-header-line',true);
                $pdf->setOption("encoding", "UTF-8");
                $pdf->setOption('margin-left', '0mm');
                $pdf->setOption('margin-right', '0mm');
                $pdf->setOption('margin-top', '0mm');
                // $pdf->setOption('footer-right', 'Page [page] of [toPage]');
                $filename = 'student_fixcode_code_' . $centervalue . '_' . $subjectname . '_updated.pdf';
                $completepath = $path . $filename;
                $pdf->save($completepath, $pdf, true);

                //return( Response::download( $completepath ) );

            }
        }
        echo "Today is Done " . date("Y/m/d") . "<br>";

    }

    public function aicodewisesubjectsdatastudentsAndSupp()
    {

        $title = "Ai code wise subject data student And SUPP";
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

        return view('examination_reports.aicodewisesubjectsdatastudentssupp', compact('courses', 'stream_id', 'title', 'breadcrumbs', 'developeradminrole', 'user_role', 'district_list', 'empty', 'midium'));
    }


    public function downloadaicodewisesubjectsdatastudentsSupp(Request $request, $type = "xlsx")
    {

        $request->validate([
            'course' => 'required',
            'stream' => 'required',

        ]);

        return Excel::download(new AicentersubjectCountsanssuppExlExport($request), 'Aicodesubjectswise studentdata.xlsx');
    }


}



   
   