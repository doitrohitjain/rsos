<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Exports\LastYearStudentdatalExport;
use App\Helper\CustomHelper;
use App\models\Examination;
use Auth;
use Config;
use DB;
use File;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Response;
use Session;

/* Hall Ticket Addtional Model */

/* Hall Ticket Addtional Model */


class AdministrativeReportsController extends Controller
{
    function __construct()
    {
        //$this->middleware('permission:administrative_custom_report', ['only' => ['administrative_custom_report']]);
    }

    public function administrative_custom_report(Request $request)
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
        $district_list = $this->districtsByState();
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
        $table_id = "Admission_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $custom_component_obj = new CustomComponent;
        $master = $custom_component_obj->getMasterActiveQueries();
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
            ),
            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district_list,
                'placeholder' => 'District',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Are You From Rajasthan And Not",
                'fld' => 'are_you_from_rajasthan',
                'input_type' => 'select',
                'options' => $are_you_from_rajasthan,
                'placeholder' => 'Are You From Rajasthan And Not',
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
                'fld_url' => '../student/persoanl_details/#id#'
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
        );
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $conditions["students.user_id"] = @Auth::user()->id;
        } else {

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

        $master = $custom_component_obj->getApplicationData($formId);

        return view('admission_reports.student_applications', compact('are_you_from_rajasthan', 'district_list', 'actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function administrative_document_listing(Request $request)
    {
        echo "listing and upload utitlity";
        die;
    }

    public function administrative_material_download()
    {
        $pageTitle = "Materials Download";
        $rSOSAdmissionMaterial = $rSOSExaminationMaterial = $examCenterMaterial = $aiCenterMaterial = array();

        $aiCenterMaterial = array(
            array(
                "lbl" => "Nominal Roll",
                'fld' => '1',
                'url' => 'nominalnrgenerateview',
                'allowedPer' => array('aicodenominalnraicenter'), //pending
                'status' => true,
            ),
            array(
                "lbl" => "Hall Tickets",
                'fld' => '2',
                'url' => 'hallticketbulkviews',
                'allowedPer' => array('aicodehallticket'),
                'status' => true,
            ),
        );
        $examCenterMaterial = array(
            array(
                "lbl" => "Nominal Roll",
                'fld' => '1',
                'url' => 'single_exam_center_nominal_roll_pdf_view',
                'allowedPer' => array('aicodenominalnr'),
                'status' => true,
            ),
            array(
                "lbl" => "Attendance Sheets",
                'fld' => '2',
                'url' => 'single_exam_center_attendance_roll_pdf_view',
                'allowedPer' => array('aicodenominalnr'),
                'status' => true,
            ),
            array(
                "lbl" => "Theory Roll",
                'fld' => '3',
                'url' => 'single_exam_center_theorynominal_roll_pdf_view',
                'allowedPer' => array('aicodenominalnr'),
                'status' => true,
            ),
            array(
                "lbl" => "Practical Roll",
                'fld' => '4',
                'url' => 'single_exam_center_practicalnominal_roll_pdf_view',
                'allowedPer' => array('aicodenominalnr'),
                'status' => true,
            ),
            array(
                "lbl" => "Theory Signatare Roll",
                'fld' => '5',
                'url' => 'single_exam_center_theorysignaturenominal_roll_pdf_view',
                'allowedPer' => array('aicodenominalnr'),
                'status' => true,
            ),
            array(
                "lbl" => "Practical Signatare Roll",
                'fld' => '6',
                'url' => 'single_exam_center_practicalsignaturenominal_roll_pdf_view',
                'allowedPer' => array('aicodenominalnr'),
                'status' => true,
            ),
        );
        $rSOSExaminationMaterial = array(
            array(
                "lbl" => "Big strength Nominal Roll",
                'fld' => '1',
                'url' => 'reportexamcenterwise',
                'allowedPer' => array('examination_reportexamcenterwise'),
                'status' => true,
            ),
            array(
                "lbl" => "Board Nominal Roll",
                'fld' => '2',
                'url' => 'abc',
                'allowedPer' => array('board_nominal_roll'),
                'status' => true,
            ),
            array(
                "lbl" => "Aicenter Wise Nominal Roll",
                'fld' => '3',
                'url' => 'nominalnrgenerateview',
                'allowedPer' => array('aicodenominalnraicenterexamination'),
                'status' => true,
            ),
            array(
                "lbl" => "Stickers",
                'fld' => '4',
                'url' => 'abc',
                'allowedPer' => array('examination_stickers'),
                'status' => true,
            ),
            array(
                "lbl" => "Student Fixcode",
                'fld' => '5',
                'url' => 'abc',
                'allowedPer' => array('examination_student_fixcode'),
                'status' => true,
            ),
            array(
                "lbl" => "Examcenter Fixcode",
                'fld' => '6',
                'url' => 'abc',
                'allowedPer' => array('examination_fixcode'),
                'status' => true,
            ),
            array(
                "lbl" => "Marksheet Printing",
                'fld' => '7',
                'url' => 'downloadBulkDocument',
                'allowedPer' => array('serachenrollment'),
                'status' => true,
            ),
        );
        $rSOSAdmissionMaterial = array(
            array(
                "lbl" => "Student Checklist",
                'fld' => '1',
                'url' => 'studentchecklists',
                'allowedPer' => array('examination_report_studentchecklists'),
                'status' => true,
            ),
            array(
                "lbl" => "Supplementary Checklist",
                'fld' => '2',
                'url' => 'SupplementaryChecklists',
                'allowedPer' => array('examination_report_supplementarychecklists'),
                'status' => true,
            ),
            array(
                "lbl" => "Toc Checklist",
                'fld' => '3',
                'url' => 'tocChecklists',
                'allowedPer' => array('examination_report_tocchecklists'),
                'status' => true,
            ),
            array(
                "lbl" => "District wise Checklist",
                'fld' => '4',
                'url' => 'allcenterwisetocchecklist',
                'allowedPer' => array('examination_report_allcenterwisetocchecklist'),
                'status' => true,
            ),
        );

        $tableData = array(
            array(
                "lbl" => "AI Center Material",
                'fld' => '1',
                'status' => true,
                'allowedPer' => array('aicodenominalnraicenter'),
                'content' => $aiCenterMaterial
            ),
            array(
                "lbl" => "Exam Center Material",
                'fld' => '2',
                'status' => true,
                'allowedPer' => array('aicodenominalnr'),
                'content' => $examCenterMaterial
            ),
            array(
                "lbl" => "RSOS Examination Material",
                'fld' => '3',
                'status' => true,
                'allowedPer' => array('examinationmaterial'),
                'content' => $rSOSExaminationMaterial
            ),
            array(
                "lbl" => "RSOS Admission Material",
                'fld' => '4',
                'status' => true,
                'allowedPer' => array('admissionmaterial'),
                'content' => $rSOSAdmissionMaterial
            )
        );

        return view('admission_reports.administrative_material_download', compact('pageTitle', 'tableData'));
    }

    public function generate_last_years_student_subject_data_setting(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        $title = "Genrate Last years students subject Data";
        $type = 'csv';
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
        $developeradminrole = Config::get("global.developer_admin");
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $orderByRaw = " rs_masters.option_val ASC ";
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name, $orderByRaw);

        $user_role = Session::get('role_id');
        $offsetstart = 0;
        $limit = 900000;

        if ($request->isMethod('POST')) {
            $request->all();
            $filename = $request->course . "_" . $admission_sessions[$request->exam_year] . "_" . $request->offsetstart . "_" . $request->limit . "_" . 'Lastyearstudentdata' . '.' . $type;
            $path = "last_years_student_subject_data/" . $filename;
            // $path = public_path("files/last_years_student_subject_data/" . $filename);
            Excel::store(new LastYearStudentdatalExport($request), $path);
            return redirect()->back()->with('message', 'Excel Genrate Successfully.');
        }

        $empty = array();

        return view('admission_reports.lastyearstudentdatagenrateanddownload', compact('courses', 'offsetstart', 'limit', 'exam_month', 'title', 'breadcrumbs', 'developeradminrole', 'user_role', 'empty', 'admission_sessions'));
    }

    public function download_last_years_student_subject_data_setting(Request $request)
    {
        $type = 'csv';
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        if ($request->isMethod('POST')) {
            $request->all();
            $filename = $request->course . "_" . $admission_sessions[$request->exam_year] . "_" . $request->offsetstart . "_" . $request->limit . "_" . 'Lastyearstudentdata' . '.' . $type;
            $path = public_path() . "/excel/last_years_student_subject_data/" . $filename;
            if (File::exists($path)) {
                return Response::download($path);
            } else {
                return redirect()->back()->with('error', 'Please First Genrate the Excel.');
            }
        }

    }

    public function last_years_student_subject_data_setting($course = null, $exam_year = null, $exam_month = null, $limit = 5)
    {
        $students = DB::select('call getLastYearStudentSubjectDetails(?,?,?,?)', array($course, $exam_year, $exam_month, $limit));
        $finalArr = array();
        foreach (@$students as $key => $student) {
            $finalArr[$student->id]['id'] = $student->id;
            $finalArr[$student->id]['enrollment'] = $student->enrollment;
            $finalArr[$student->id]['course'] = $student->course;
            $finalArr[$student->id]['name'] = $student->name;
            $finalArr[$student->id]['mother_name'] = $student->mother_name;
            $finalArr[$student->id]['DOB_figures'] = $student->DOB_figures;
            $finalArr[$student->id]['DOB_words'] = $student->DOB_words;
            $finalArr[$student->id]['subjects'][$student->subject_id]['subject_id'] = $student->subject_id;
            $finalArr[$student->id]['subjects'][$student->subject_id]['subject_name'] = $student->subject_name;
            $finalArr[$student->id]['subjects'][$student->subject_id]['subject_code'] = $student->subject_code;
            $finalArr[$student->id]['subjects'][$student->subject_id]['max_marks'] = $student->max_marks;
            $finalArr[$student->id]['subjects'][$student->subject_id]['theory_arks'] = $student->theory_arks;
            $finalArr[$student->id]['subjects'][$student->subject_id]['practical_marks'] = $student->practical_marks;
            $finalArr[$student->id]['subjects'][$student->subject_id]['sessional_arks'] = $student->sessional_arks;
            $finalArr[$student->id]['subjects'][$student->subject_id]['total_marks'] = $student->total_marks;
        }
        $output = array();
        $i = 0;

        foreach (@$finalArr as $student_id => $value) {
            $output[$i] = array(
                'student_id' => @$student_id,
                'enrollment' => @$value['enrollment'],
                'course' => @$value['course'],
                'name' => @$value['name'],
                'father_name' => @$value['father_name'],
                'mother_name' => @$value['mother_name'],
                'DOB_figures' => @$value['DOB_figures'],
                'DOB_words' => @$value['DOB_words']
            );
            $subjectCounter = 1;
            foreach (@$value['subjects'] as $subject_id => $subject) {
                $output[$i]['subject_code_' . $subjectCounter] = $subject['subject_code'];
                $output[$i]['max_marks_' . $subjectCounter] = $subject['max_marks'];
                $output[$i]['theory_arks_' . $subjectCounter] = $subject['theory_arks'];
                $output[$i]['practical_marks_' . $subjectCounter] = $subject['practical_marks'];
                $output[$i]['sessional_arks_' . $subjectCounter] = $subject['sessional_arks'];
                $output[$i]['total_marks_' . $subjectCounter] = $subject['total_marks'];
                $subjectCounter++;
            }
            $i++;
        }
        dd($output);
        echo "Done " . $course . $exam_year . "_" . $exam_month . "_";
        die;
    }

}

