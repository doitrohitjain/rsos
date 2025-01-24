<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\RevalMarksComponent;
use App\Exports\RevalGeneratExlExport;
use App\Exports\RevalObtainedMarksExlExport;
use App\Helper\CustomHelper;
use App\Models\Registration;
use Auth;
use Config;
use DB;
use File;
use Hash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Redirect;
use Response;
use Route;
use Session;
use Validator;

class RevalMarksController extends Controller
{

    private $request;

    public function __construct(request $request)
    {
        $this->request = $request;
        parent::__construct();
        $this->middleware('permission:reval_marks_listing', ['only' => ['reval_marks_listing']]);
        $this->middleware('permission:reval_obtained_marks', ['only' => ['reval_obtained_marks']]);
    }

    public function reval_marks_listing(Request $request)
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
        $subjectCodes = $this->subjectCodeList();
        $yes_no_temp[""] = "No";
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $role_id = Session::get('role_id');
        $aicenter_id_role = config("global.aicenter_id");
        $yes_no = $this->master_details('yesno');
        $title = "Reval Student Subjects";
        $table_id = "Reval_Student_Subject_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));
        Session::put('formId', $formId);
        $exam_year = CustomHelper::_get_selected_sessions();
        $form_supp_current_admission_session_id = config("global.form_supp_current_admission_session_id");
        $supp_current_admission_exam_month = config("global.supp_current_admission_exam_month");

        $combo_name = 'reval_exam_year';
        $reval_exam_year = $this->master_details($combo_name);
        $combo_name = 'reval_exam_month';
        $reval_exam_month = $this->master_details($combo_name);
        $resultsyntax = array('999' => 'AB', '666' => 'SYCP', '777' => 'SYCT', '888' => 'SYC', 'P' => 'PASS', 'p' => 'PASS');
        //need to put in funciton end
        $custom_component_obj = new CustomComponent;
        $reval_marks_component_obj = new RevalMarksComponent;
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
        $subject_id_for_link = 0;
        $tempInputs = $request->all();
        if (@$tempInputs['subject_id']) {
            $subject_id_for_link = @$tempInputs['subject_id'];
        }

        $exportBtn = array();

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

        $subject_list = $this->subjectList();

        $filters = array(
            array(
                "lbl" => "Is Result Change",
                'fld' => 'reval_is_subject_result_change',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is Result Change',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'reval_students',
            ),
            array(
                "lbl" => "Student Fixcode",
                'fld' => 'studentfixcode',
                'input_type' => 'text',
                'placeholder' => "Student Fixcode",
                'dbtbl' => 'student_allotments',
            ),
            array(
                "lbl" => "Center Fixcode",
                'fld' => 'centerfixcode',
                'input_type' => 'text',
                'placeholder' => "Center Fixcode",
                'dbtbl' => 'examcenter_details',
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
            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'placeholder' => 'Subject',
                'dbtbl' => 'reval_student_subjects',
            ),
            array(
                "lbl" => "Are Reval Marks Entered",
                'fld' => 'reval_is_subject_marks_entered',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Are Reval Marks Entered',
                'dbtbl' => 'student_allotment_marks',
            )
        );
        $tableData = array(
            array(
                "lbl" => "Reval Application Type",
                'fld' => 'reval_type',
                'input_type' => 'select',
                'options' => $reval_types
            )
        );
        $conditions = array();
        $conditions["reval_students.exam_year"] = CustomHelper::_get_selected_sessions();
        $conditions["reval_students.exam_month"] = @$reval_exam_month[1];
        $conditions["reval_students.is_eligible"] = 1;
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
        }
        $actions = array();
        if (in_array("Reval_student_dashboard", $permissions)) {
            $actions = array();
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
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (@$iv['fld'] == $k && $iv['fld'] == $k) {
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
        $master = $reval_marks_component_obj->getRevalMarksApplicationData($formId);
        // dd($master);
        return view('reval_marks.reval_marks_listing', compact('resultsyntax', 'subject_list', 'subjectCodes', 'subject_id_for_link', 'actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function reval_obtained_marks(Request $request)
    {
        $model = "StudentAllotmentMark";
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
        $subjectCodes = $this->subjectCodeList();
        $yes_no_temp[""] = "No";
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $role_id = Session::get('role_id');
        $aicenter_id_role = config("global.aicenter_id");
        $yes_no = $this->master_details('yesno');
        $title = "Reval Student Subject Obtained Marks Entries";
        $table_id = "Reval_Student_Subject_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));//Reval_Student_Subject_Obtained_Marks_Entries
        Session::put('formId', $formId);
        $exam_year = CustomHelper::_get_selected_sessions();
        $form_supp_current_admission_session_id = config("global.form_supp_current_admission_session_id");
        $supp_current_admission_exam_month = config("global.supp_current_admission_exam_month");

        $combo_name = 'reval_exam_year';
        $reval_exam_year = $this->master_details($combo_name);
        $combo_name = 'reval_exam_month';
        $reval_exam_month = $this->master_details($combo_name);

        //need to put in funciton end
        $custom_component_obj = new CustomComponent;
        $reval_marks_component_obj = new RevalMarksComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $permissions = CustomHelper::roleandpermission();
        $yes_no_temp = $this->master_details('yesno');
        $yes_no_temp[""] = "No";
        $resultsyntax = array('999' => 'AB', '666' => 'SYCP', '777' => 'SYCT', '888' => 'SYC', 'P' => 'PASS', 'p' => 'PASS');
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
        $subject_id_for_link = 0;
        $tempInputs = $request->all();
        if (@$tempInputs['subject_id']) {
            $subject_id_for_link = @$tempInputs['subject_id'];
        }

        $exportBtn = array();


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

        $subject_list = $this->subjectList();

        $filters = array(
            array(
                "lbl" => "Is Result Change",
                'fld' => 'reval_is_subject_result_change',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is Result Change',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'reval_students',
            ),
            array(
                "lbl" => "Student Fixcode",
                'fld' => 'studentfixcode',
                'input_type' => 'text',
                'placeholder' => "Student Fixcode",
                'dbtbl' => 'student_allotments',
            ),
            array(
                "lbl" => "Center Fixcode",
                'fld' => 'centerfixcode',
                'input_type' => 'text',
                'placeholder' => "Center Fixcode",
                'dbtbl' => 'examcenter_details',
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
            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'placeholder' => 'Subject',
                'dbtbl' => 'reval_student_subjects',
            ),
            array(
                "lbl" => "Are Reval Marks Entered",
                'fld' => 'reval_is_subject_marks_entered',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Are Reval Marks Entered',
                'dbtbl' => 'student_allotment_marks',
            )
        );
        $tableData = array();
        $conditions = array();
        $conditions["reval_students.exam_year"] = CustomHelper::_get_selected_sessions();
        $conditions["reval_students.exam_month"] = @$reval_exam_month[1];
        $conditions["reval_students.is_eligible"] = 1;
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
        }
        $actions = array();
        if (in_array("Reval_student_dashboard", $permissions)) {
            $actions = array();
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
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (@$iv['fld'] == $k && $iv['fld'] == $k) {
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
        $master = $reval_marks_component_obj->getRevalMarksApplicationData($formId);

        return view('reval_marks.reval_obtained_marks', compact('model', 'subject_list', 'subjectCodes', 'subject_id_for_link', 'actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'resultsyntax', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function reval_rte_copies(Request $request)
    {
        $model = "StudentAllotmentMark";
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
        $subjectCodes = $this->subjectCodeList();
        $yes_no_temp[""] = "No";
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $combo_name = 'reval_rte_status';
        $reval_rte_status = $this->master_details($combo_name);
        $role_id = Session::get('role_id');
        $aicenter_id_role = config("global.aicenter_id");
        $yes_no = $this->master_details('yesno');
        $title = "Reval Student Subject RTE Copies Entries";
        $table_id = "Reval_Student_Subject_RTE_Copies_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));//Reval_Student_Subject_Obtained_Marks_Entries
        $exam_year = CustomHelper::_get_selected_sessions();
        $form_supp_current_admission_session_id = config("global.form_supp_current_admission_session_id");
        $supp_current_admission_exam_month = config("global.supp_current_admission_exam_month");

        $combo_name = 'reval_exam_year';
        $reval_exam_year = $this->master_details($combo_name);
        $combo_name = 'reval_exam_month';
        $reval_exam_month = $this->master_details($combo_name);
        $resultsyntax = array('999' => 'AB', '666' => 'SYCP', '777' => 'SYCT', '888' => 'SYC', 'P' => 'PASS', 'p' => 'PASS');
        //need to put in funciton end
        $custom_component_obj = new CustomComponent;
        $reval_marks_component_obj = new RevalMarksComponent;
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
        $subject_id_for_link = 0;
        $tempInputs = $request->all();
        if (@$tempInputs['subject_id']) {
            $subject_id_for_link = @$tempInputs['subject_id'];
        }

        $exportBtn = array();
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

        $subject_list = $this->subjectList();

        $filters = array(
            array(
                "lbl" => "Is Result Change",
                'fld' => 'reval_is_subject_result_change',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is Result Change',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'reval_students',
            ),
            array(
                "lbl" => "Student Fixcode",
                'fld' => 'studentfixcode',
                'input_type' => 'text',
                'placeholder' => "Student Fixcode",
                'dbtbl' => 'student_allotments',
            ),
            array(
                "lbl" => "Center Fixcode",
                'fld' => 'centerfixcode',
                'input_type' => 'text',
                'placeholder' => "Center Fixcode",
                'dbtbl' => 'examcenter_details',
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
                "lbl" => "RTE Status",
                'fld' => 'reval_rte_status',
                'input_type' => 'select',
                'options' => $reval_rte_status,
                'placeholder' => 'RTE Status',
                'dbtbl' => 'reval_student_subjects',
            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'placeholder' => 'Subject',
                'dbtbl' => 'reval_student_subjects',
            ),
            array(
                "lbl" => "Are Reval Marks Entered",
                'fld' => 'reval_is_subject_marks_entered',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Are Reval Marks Entered',
                'dbtbl' => 'student_allotment_marks',
            )
        );
        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Enrollemnt",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Student Fixcode",
                'fld' => 'studentfixcode',
                'input_type' => 'text',
                'dbtbl' => 'student_allotments',
            ),
            array(
                "lbl" => "Center Fixcode",
                'fld' => 'centerfixcode',
                'input_type' => 'text',
                'dbtbl' => 'examcenter_details',
            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'options' => $subject_list,
                'input_type' => 'select',
                'dbtbl' => 'reval_student_subjects',
            ),
            array(
                "lbl" => "Marks On Answer Book before reval",
                'fld' => 'final_practical_marks',
                'input_type' => 'text',
                'dbtbl' => 'exam_subjects',
            ),
            array(
                "lbl" => "Theory Marks in before reval",
                'fld' => 'reval_final_theory_marks',
                'input_type' => 'text',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                "lbl" => "Remarks",
                'fld' => 'reval_type_of_mistake',
                'input_type' => 'text',
                'dbtbl' => 'student_allotment_marks',
            ),
            // array(
            // "lbl" => "Are Reval Marks Entered",
            // 'fld' => 'reval_is_subject_marks_entered',
            // 'input_type' => 'select',
            // 'options' => $yes_no,
            // 'dbtbl' => 'student_allotment_marks',
            // ),
            array(
                "lbl" => "Sessional Marks in Result",
                'fld' => 'sessional_marks',
                'input_type' => 'select',
                'dbtbl' => 'exam_subjects',
            ),
            array(
                "lbl" => "Practical Marks in Result",
                'fld' => 'final_practical_marks',
                'input_type' => 'select',
                'dbtbl' => 'exam_subjects',
            ),
            // array(
            // "lbl" => "Difference After Revaluation",
            // 'fld' => 'reval_difference_after_reval',
            // 'input_type' => 'text',
            // 'dbtbl' => 'student_allotment_marks',
            // ),
            array(
                "lbl" => "Final Result after Reval",
                'fld' => 'reval_subject_final_result_after_reval',
                'input_type' => 'text',
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                "lbl" => "Any Change",
                'fld' => 'reval_any_change',
                'input_type' => 'select',
                'options' => $yes_no,
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                "lbl" => "Is Result Change",
                'fld' => 'reval_is_subject_result_change',
                'input_type' => 'select',
                'options' => $yes_no,
                'dbtbl' => 'student_allotment_marks',
            ),
            array(
                "lbl" => "Reval Application Type",
                'fld' => 'reval_type',
                'input_type' => 'select',
                'options' => $reval_types
            )
        );
        $conditions = array();
        $conditions["reval_students.exam_year"] = CustomHelper::_get_selected_sessions();
        $conditions["reval_students.exam_month"] = @$reval_exam_month[1];
        $conditions["reval_students.reval_type"] = 2;
        $conditions["reval_students.is_eligible"] = 1;
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
        }
        $actions = array();
        if (in_array("Reval_student_dashboard", $permissions)) {
            $actions = array();
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
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (@$iv['fld'] == $k && $iv['fld'] == $k) {
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

        $master = $reval_marks_component_obj->getRevalMarksApplicationData($formId);

        return view('reval_marks.reval_rte_copies', compact('resultsyntax', 'model', 'subject_list', 'subjectCodes', 'subject_id_for_link', 'actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'reval_rte_status'))->withInput($request->all());
    }

    public function reval_generate_template($subject_id = 0, Request $request)
    {
        //generate and download the pdf
        $title = "Reval Student Subjects";
        $formId = Session::get('formId');
        //$formId = ucfirst(str_replace(" ","_",$title));
        $inputs = $request->all();
        $type = 'consolidated';
        if (@$subject_id > 0) {
            $type = 'seprate';
        }
        $docType = 'template';
        $combo_name = 'exam_month';
        $exam_month_master = $this->master_details($combo_name);
        $combo_name = 'reval_exam_year';
        $reval_exam_year = $this->master_details($combo_name);
        $combo_name = 'reval_exam_month';
        $reval_exam_month = $this->master_details($combo_name);
        $reval_exam_year = @$reval_exam_year[1];
        $reval_exam_month = @$reval_exam_month[1];
        $resultsyntax = array('999' => 'AB', '666' => 'SYCP', '777' => 'SYCT', '888' => 'SYC', 'P' => 'PASS', 'p' => 'PASS');
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $reval_marks_component_obj = new RevalMarksComponent;
        $subjectCodes = $this->subjectCodeList();
        $master = $reval_marks_component_obj->getRevalMarksPdfData($subject_id, $formId);
        $finalArr = array();
        $revalMarksDefaultPageLimit = Config::get('global.revalMarksDefaultPageLimit');
        if (@$master) {
            foreach ($master as $k => $v) {
                if (@$v->subject_id) {
                    $subject_id = $v->subject_id;
                    $finalArr[$subject_id][] = $v;
                }
            }
        }

        $fields = array('finalArr', 'subject_id', 'exam_month_master', 'reval_exam_year', 'reval_exam_month', 'admission_sessions', 'revalMarksDefaultPageLimit', 'subjectCodes', 'resultsyntax');
        //return view('reval_marks.generate_reval_' . $docType .'_pdf', compact($fields));

        $date = time();

        $filename = 'RevalMarks_' . $docType . '_' . $type . '_' . $subject_id . '_' . $date . '.pdf';
        $pdf = PDF::loadView('reval_marks.generate_reval_' . $docType . '_pdf', compact($fields));
        // $pdf->setOption('footer-right', 'Page [page] of [toPage]');

        $path = public_path('reval_marks/' . $docType . '/' . $reval_exam_year . '/' . $reval_exam_month . '/' . $filename);

        $pdf->save($path, $pdf, true);
        return $pdf->download($filename);
    }

    public function reval_generate_pdf_obtained_marks($subject_id = 0, Request $request)
    {
        //generate and download the pdf
        $title = "Reval Student Subjects";
        $formId = Session::get('formId');
        //$formId = ucfirst(str_replace(" ","_",$title));
        $inputs = $request->all();
        $type = 'consolidated';
        if (@$subject_id > 0) {
            $type = 'seprate';
        }
        $docType = 'obtained';
        $combo_name = 'exam_month';
        $exam_month_master = $this->master_details($combo_name);
        $combo_name = 'reval_exam_year';
        $reval_exam_year = $this->master_details($combo_name);
        $combo_name = 'reval_exam_month';
        $reval_exam_month = $this->master_details($combo_name);
        $reval_exam_year = @$reval_exam_year[1];
        $reval_exam_month = @$reval_exam_month[1];
        $resultsyntax = array('999' => 'AB', '666' => 'SYCP', '777' => 'SYCT', '888' => 'SYC', 'P' => 'PASS', 'p' => 'PASS');
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);

        $reval_marks_component_obj = new RevalMarksComponent;
        $subjectCodes = $this->subjectCodeList();
        $master = $reval_marks_component_obj->getRevalMarksPdfData($subject_id, $formId);
		
        $finalArr = array();
        $revalMarksDefaultPageLimit = Config::get('global.revalMarksDefaultPageLimit') + 0;
        if (@$master) {
            foreach ($master as $k => $v) {
                if (@$v->subject_id) {
                    $subject_id = $v->subject_id;
                    $finalArr[$subject_id][] = $v;
                }
            }
        }
        $fields = array('finalArr', 'subject_id', 'exam_month_master', 'reval_exam_year', 'reval_exam_month', 'admission_sessions', 'revalMarksDefaultPageLimit', 'subjectCodes', 'resultsyntax');
        // return view('reval_marks.generate_reval_' . $docType .'_pdf', compact($fields));

        $date = time();
        $filename = 'RevalMarks_' . $docType . '_' . $type . '_' . $subject_id . '_' . $date . '.pdf';
        $pdf = PDF::loadView('reval_marks.generate_reval_' . $docType . '_pdf', compact($fields))->setOrientation('landscape');//$pdf->setOption('landscape');
        $pdf->setOption('footer-right', 'Page [page] of [toPage]');
        $pdf->setOption('margin-top', '4mm');

        $path = public_path('reval_marks/' . $docType . '/' . $reval_exam_year . '/' . $reval_exam_month . '/' . $filename);

        $pdf->save($path, $pdf, true);
        return $pdf->download($filename);
    }

    public function reval_generate_excel_obtained_marks(Request $request)
    {
        $conditions = Session::get("Reval_Student_Subject_Obtained_Marks_Entries_conditions");
        $subject_id = '0';
        $subject_id = @$conditions['reval_student_subjects.subject_id'];
        $type = 'consolidated';
        if (@$subject_id > 0) {
            $type = 'seprate';
        }
        $data = new RevalObtainedMarksExlExport;
        $date = time();
        $filename = 'Reval_Obtained_Marks_' . $type . "_" . $subject_id . '_' . $date . '.xlsx';

        return Excel::download($data, $filename);
    }

    public function downloadrevalgeneratetemplateExl($subject_id = 0, Request $request, $type = "xlsx")
    {
        $types = 'consolidated';
        if (@$subject_id > 0) {
            $types = 'seprate';
        }
        $application_exl_data = new RevalGeneratExlExport;
        $filename = $types . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

}
	
