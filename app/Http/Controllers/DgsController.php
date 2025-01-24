<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Helper\CustomHelper;
use App\Models\ModelHasRole;
use App\Models\Student;
use Auth;
use Config;
use DB;
use File;
use Hash;
use Illuminate\Http\Request;
use PDF;
use Redirect;
use Response;
use Route;
use Session;
use Validator;


class DgsController extends Controller
{
    private $request;

    public function __construct(request $request)
    {
        $this->request = $request;
        parent::__construct();
        // $this->middleware('permission:reval_find_enrollment', ['only' => ['reval_find_enrollment']]);
    }

    public function listing(Request $request)
    {
        // table -> students->is_dgs = 1
        // name,dob,mobile,username,password,action (edit/delete)
        // show Add button on view
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
        $yes_no = $this->master_details('yesno');
        $title = "Disadvantage Group Student Admission Report";
        $table_id = "Disadvantage Group Student Admission Report";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;
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
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Student's Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Username",
                'fld' => 'username',
                'input_type' => 'text',
                'placeholder' => "Username",
                'dbtbl' => 'students',
            ),
            /*array(
                "lbl" => "Mobile",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Mobile",
                'dbtbl' => 'students',
            )*/
        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();
        $conditions["students.is_dgs"] = '1';
        $role_id = @Session::get('role_id');

        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Username",
                'fld' => 'username',
                'input_type' => 'text',
                'placeholder' => "Username",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Password",
                'fld' => 'original_password',
                'input_type' => 'text',
                'placeholder' => "Password",
                'dbtbl' => 'students',
            ),

            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
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

            /*array(
                "lbl" => "Mobile",
                'fld' => 'mobile',
                'placeholder' => "Mobile",
                'dbtbl' => 'students',
            ),*/
            array(
                "lbl" => "Course ",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            ),
            array(
                "lbl" => "Exam Month",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month,
                'dbtbl' => 'students',
            ),

        );


        if (in_array("dgs_dashbarod", $permissions)) {
            $actions = array(

                // array(
                // 'fld' => 'edit',
                // 'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                // 'fld_url' => '../dgs/update_profile/#id#'
                // ),
            );
        } else {
            $actions = array();
        }
        // }

        // $tableData[] = array(
        // "lbl" => "Action",
        // 'fld' => 'action'
        // );
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
        $orderByRaw = "rs_students.id desc";
        /* Sorting Fields Set Session Start 3*/
        Session::put($formId . '_orderByRaw', $orderByRaw);
        Session::put($formId . '_conditions', $conditions);
        $master = $custom_component_obj->getApplicationData($formId);
        return view('dgs.listing', compact('actions', 'tableData', 'master', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'yes_no'))->withInput($request->all());

    }

    public function create_profile(Request $request)
    {
        $title = "Set up student profile";
        $custom_component_obj = new CustomComponent;
        $aicenter_list = $custom_component_obj->getAiCenters(null, null, 1);
        return view('dgs.create_profile', compact('title', 'aicenter_list'));

    }

    public function store_profile(Request $request)
    {
        $title = "Add Student";
        if ($request->isMethod('POST')) {
            if (@$request->createprofilelink == '1') {
                $ai_code = @$request->ai_code;
                $StudentCount = @$request->studentcount;
                if ($StudentCount <= 0) {
                    return Redirect()->route('create_profile')->with('error', "Enter valid count number of students.");
                }
                return view('dgs.store_profile', compact('title', 'ai_code', 'StudentCount'));
            } else {
                $inputs = $request->all();
                // dd($inputs['ai_code']);
                $counter = 0;
                if ($inputs['name']) {
                    $dobs = $inputs['dob'];
                    foreach ($inputs['name'] as $key => $detail) {
                        if (@$detail[$key] && @$dobs[$key]) {
                            $counter++;
                            $saveDetails['name'] = $name = @$detail;
                            $saveDetails['dob'] = $dob = @$dobs[$key];
                            $saveDetails['ai_code'] = $ai_code = $inputs['ai_code'];
                            if (@$name && $dob && @$ai_code) {
                                $saveStatus = $this->createDGSStudentProfile($saveDetails);
                            } else {
                                continue;
                            }
                        } else {
                            continue;
                        }
                    }
                }
                return redirect()->route('create_profile')->with('message', "The student profile has been successfully created.");
            }
        } else {
            return redirect()->route('create_profile')->with('error', "Something is wrong.");
        }
    }


    public function createDGSStudentProfile($saveDetails = null)
    {

        $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
        $current_exam_month_id = Config::get("global.form_current_exam_month_id");

        $saveDetails['dob'] = date('Y-m-d', strtotime($saveDetails['dob']));
        $user_id = Auth::user()->id;

        $studentArr = ['is_dgs' => 1, 'first_name' => @$saveDetails['name'], 'name' => @$saveDetails['name'], 'dob' => @$saveDetails['dob'], 'ai_code' => $saveDetails['ai_code'], 'user_id' => @$user_id, 'ai_code' => @$saveDetails['ai_code'], 'exam_month' => @$current_exam_month_id, 'exam_year' => @$current_admission_session_id];

        $Student = Student::create($studentArr);

        $student_id = $Student->id;
        $response = $this->_getUsernameWithPassword($student_id);
        $original_password = $response['password'];
        $password = Hash::make($response['password']);
        $updatestudent = Student::find($student_id);
        $updatestudent->original_password = $original_password;
        $updatestudent->password = $password;
        $updatestudent->mobile = '999999999';
        $updatestudent->otp = '1111';
        $updatestudent->is_otp_verified = 1;
        $updatestudent->ssoid = $response['username'];
        $updatestudent->username = $response['username'];
        $updatestudent->save();

        $modeltype = 'App\Models\Student';
        $studentroles = Config::get("global.student");
        $model_has_roles = DB::table('model_has_roles')->where('role_id', $studentroles)->where('model_type', $modeltype)->where('model_id', $student_id)->first();

        $studentroless = ['role_id' => $studentroles,
            'model_type' => $modeltype,
            'model_id' => $student_id,];

        if (!empty($model_has_roles)) {
            $model_has_roles = DB::table('model_has_roles')->where('role_id', $studentroles)->where('model_type', $modeltype)->where('model_id', $student_id)->update($studentroless);
        } else {
            $model_has_roles = ModelHasRole::create($studentroless);
        }
        return true;
    }

    public function update_profile(Request $request, $student_id)
    {
        //single udpate
        @dd('hii');
    }
}
	
