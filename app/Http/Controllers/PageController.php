<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Models\Application;
use App\models\ModelHasRole;
use App\Models\Registration;
use App\Models\SingleScreenDate;
use App\Models\Student;
use App\models\User;
use Auth;
use Cache;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use PDF;
use Redirect;
use Response;
use Route;
use Session;
use Validator;


class PageController extends Controller
{

    public function _allowOnlyPostRequest(Request $request)
    {
        $actionName = Route::getCurrentRoute();
        $actionName = @$actionName->action['as'];
        if (in_array($actionName, array('select_aicenter', 'self_registration')) && $request->isMethod('get')) {
            return redirect()->route('landing')->with('error', 'Failed! Inavalid request so access deined!');
        }
    }

    public function new_term_conditions(Request $request)
    {
        $title = "Terms & Conditions(नियम एवं शर्तें)";
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
        $streams = $this->_getAllowedStreams();
        $streams = json_decode($streams);

        if (!$streams->status) {
            return Redirect::back()->with('error', 'Failed! Registration date has been closed!');
        }
        $egetssoid = @$request->ssoid;
        $getssoid = Crypt::decrypt($request->ssoid);
        $boards = $this->getBoardList();
        $termsText = "";
        $combo_name = 'terms_conditions';
        $newtermconditions = $this->master_details($combo_name);
        return view('pages.new_term_conditions', compact('boards', 'title', 'breadcrumbs', 'termsText', 'egetssoid', 'getssoid', 'newtermconditions'));
    }

    public function new_bothcourse_registraion(Request $request, $ecourse = null)
    {
        $course = Crypt::decrypt($ecourse);
        Session::put('new_extra_reg_course', $course);

        $title = "Choose the Course";

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
        $streams = $this->_getAllowedStreams();
        $streams = json_decode($streams);
        if (!$streams->status) {
            return Redirect::back()->with('error', 'Failed! Registration date has been closed!');
        }
        $getssoid = Auth::guard('student')->user()->ssoid;
        $egetssoid = Crypt::encrypt($getssoid);
        $boards = $this->getBoardList();
        $termsText = "";
        $combo_name = 'terms_conditions';
        $newtermconditions = $this->master_details($combo_name);
        return view('pages.new_bothcourse_registraion', compact('course', 'boards', 'title', 'breadcrumbs', 'termsText', 'egetssoid', 'getssoid', 'newtermconditions'));
    }


    public function select_aicenter(Request $request)
    {
        $role_id = @Session::get('role_id');
        if ($role_id == Config::get('global.student')) {
        } else {
            if (@$role_id) {
                return redirect()->route('dashboard')->with("error", "Please logout first to fill the form.");
            }
        }

        $streams = $this->_getAllowedStreams();
        $streams = json_decode($streams);

        if (!$streams->status) {
            return Redirect::back()->with('error', 'Failed! Registration date has been closed!');
        }

        $custom_component_obj = new CustomComponent;
        $aiCenters = collect();
        $block_list = $this->block_details();

        if ($request->all()) {
            if ($request->ssoid) {
                Session::forget('tempsso');
            }
        }
        if (@$request->ssoid) {
            $ssoid = $request->ssoid;
            Session::put('tempsso', $ssoid);
        } else {
            $ssoid = Session::get('tempsso');
        }


        $dssoid = Crypt::decrypt($ssoid);
        if (empty($dssoid)) {
            return redirect()->route('landing')->with("error", "Your session has been expired.Please login again.");
        }

        $action_type = encrypt('self_registration');

        $district_list = $this->districtsByState(6);
        $master_block_list = $this->block_details();
        if (!empty($request->district_id)) {
            $block_list = $this->block_details($request->district_id);
        }
        $aiCenters = $custom_component_obj->_getTempAiCentersIdByAiCodebolck(@$request->temp_district_id, @$request->temp_block_id, @$request->ai_code);

        $query_array = $request->query();
        $showPopup = true;
        if (@$query_array) {
            $showPopup = false;
        }

        $conditions = array();
        $title = "AI Centre Details (एआई केंद्र विवरण)";
        $table_id = "AiCenter_Details";
        $combo_name = 'paginator_limit';
        $pagalimit = $this->master_details($combo_name);

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

            // array(
            // "label" => "Export PDF",
            // 'url' => 'downloaduserPdf',
            // 'status' => false,
            // )
        );

        $filters = array(
            array(
                "lbl" => "District(ज़िला)",
                'fld' => 'temp_district_id',
                'input_type' => 'select',
                'options' => $district_list,
                'placeholder' => 'District(ज़िला)',
                'dbtbl' => 'aicenter_details',
            ),
            array(
                "lbl" => "Block(खंड)",
                'fld' => 'temp_block_id',
                'input_type' => 'select',
                'options' => $block_list,
                'placeholder' => 'Block(खंड)',
                'dbtbl' => 'aicenter_details',
            ),
            array(
                "lbl" => "AI Centre's(एआई सेंटर)",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => "AI Centre's(एआई सेंटर)",
                'dbtbl' => 'aicenter_details',

            ),

        );
        /* Sorting Fields Set Start 1*/
        $sorting = array();
        $orderByRaw = "";
        $inputs = "";
        $sortFilters = $filters;
        $sortFilters = array();
        if (@$sortFilters[1]) {
            unset($sortFilters[1]);
        }
        if (@$sortFilters[0]) {
            unset($sortFilters[0]);
        }
        $sortingField = $this->_getSortingFields($sortFilters);
        /* Sorting Fields Set End 1*/

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
            /* Sorting Order By Set Start 2*/
            $orderByRaw = $this->_setSortingArrayFields(@$inputs['sorting'], $sortingField);
            /* Sorting Order By Set End 2*/
        }
        /* Sorting Fields Set Session Start 3*/
        Session::put($formId . '_orderByRaw', $orderByRaw);
        /* Sorting Fields Set Session End 3*/
        Session::put($formId . '_conditions', $conditions);
        // if(!empty($request->pagevalue)){
        // 	$value=$request->pagevalue;
        // 	Session::put($formId. '_paginatevalue', $value);
        // }
        $data = $custom_component_obj->getTempAicenterDataWithCache($formId, true);

        //'inputs','sortingField',
        return view('student.select_aicenter', compact('action_type', 'ssoid', 'showPopup', 'inputs', 'formId', 'master_block_list', 'pagalimit', 'sortingField', 'block_list', 'district_list', 'data', 'breadcrumbs', 'exportBtn', 'title', 'filters', 'aiCenters'));

    }

    public function oldselect_aicenter(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $role_id = @Session::get('role_id');
        if (@$role_id) {
            return redirect()->route('dashboard')->with("error", "Please logout first to fill the form.");
        }
        @$ssoid = $request->ssoid;
        $blockdetails = array();
        $state_id = 6;
        $action_type = encrypt('self_registration');
        $district_list = $this->districtsByState($state_id);
        $aicodedetails = $custom_component_obj->getAiCenters();

        return view('student.select_aicenter', compact('district_list', 'action_type', 'blockdetails', 'ssoid', 'aicodedetails'));
    }


    public function already_student_otp(Request $request, $student_id = null, $ssoid = null)
    {
        $model = 'allreadyotp';
        $role_id = @Session::get('role_id');
        if ($role_id == Config::get('global.student')) {

        } else {
            if (@$role_id) {
                return redirect()->route('dashboard')->with("error", "Please logout first to fill the form.");
            }
        }

        $title = "ओ.टी.पी(OTP)";
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($estudent_id);

        $essoid = $ssoid;
        $ssoid = Crypt::decrypt($essoid);

        $custom_component_obj = new CustomComponent;
        $ssoDetails = $custom_component_obj->getSSOIDDetials($ssoid);
        if (@$ssoDetails) {
            $ssoDetails = json_decode($ssoDetails, true);
        }
        $Student = $student = Student::find($student_id);
        $mobilenumber = substr($student->mobile, -4);

        if (@$student) {
        } else {
            return redirect()->back()->with('error', 'Student not found.')->withInput($request->all());
        }
        if ($request->all()) {
            $inputs = $request->all();
            $otp = $request->otp;
            $status = $this->_verifyStudentOTP($student_id, $otp, $ssoid);
            if ($status) {
                $currentSsoid = $ssoid;
                $modelstudentid = $Student->id;
                $modeltype = 'App\Models\Student';
                $studentroles = Config::get("global.student");
                $studentroless = ['role_id' => $studentroles,
                    'model_type' => $modeltype,
                    'model_id' => $Student->id,];
                $model_has_roles = DB::table('model_has_roles')->where('role_id', $studentroles)->where('model_type', $modeltype)->where('model_id', $modelstudentid)->first();
                if (!empty($model_has_roles)) {
                    $model_has_roles = DB::table('model_has_roles')->where('role_id', $studentroles)->where('model_type', $modeltype)->where('model_id', $modelstudentid)->update($studentroless);
                } else {
                    $model_has_roles = ModelHasRole::create($studentroless);
                }
                $password = '123456789';
                $exam_year = Config::get("global.form_supp_current_admission_session_id");
                $studentrole = Config::get("global.student");
                $credentials = (['ssoid' => $currentSsoid, 'password' => $password]);
                if (Auth::guard('student')->attempt($credentials)) {
                    Session::put("sdlogin", date("dmYhis"));
                    Session::put("current_student", $Student);
                    $student_id = Auth::guard('student')->user()->id;
                    Session::put('role_id', $studentrole);
                    $studentRoleId = config("global.student");
                    $custom_component_obj->_setCountDownTimerDetails($studentRoleId);
                    return redirect()->route('preview_details', Crypt::encrypt($student_id))->with('message', 'Your profile has successfully updated.');
                }
            } else {
                return redirect()->back()->with('error', 'Invalid OTP.Please enter valid OTP.')->withInput($request->all());
            }
        }
        $oldOtp = @$Student->otp;

        return view('student.already_student_otp', compact('ssoDetails', 'title', 'oldOtp', 'student', 'essoid', 'estudent_id', 'mobilenumber', 'ssoid', 'model'));
    }


    public function by_pass_otp_already_student(Request $request, $student_id = null, $ssoid = null)
    {
        $model = 'allreadyotp';
        $role_id = @Session::get('role_id');
        if ($role_id == Config::get('global.student')) {

        } else {
            if (@$role_id) {
                return redirect()->route('dashboard')->with("error", "Please logout first to fill the form.");
            }
        }

        $title = "छात्र विवरण(Student Details)";
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($estudent_id);

        $essoid = $ssoid;
        $ssoid = Crypt::decrypt($essoid);

        $custom_component_obj = new CustomComponent;
        $ssoDetails = $custom_component_obj->getSSOIDDetials($ssoid);
        if (@$ssoDetails) {
            $ssoDetails = json_decode($ssoDetails, true);
        }
        $student = $Student = $student = Student::find($student_id);
        $mobilenumber = substr($student->mobile, -4);

        if (@$student) {
        } else {
            return redirect()->back()->with('error', 'Student not found.')->withInput($request->all());
        }
        if ($request->all()) {
            $inputs = $request->all();
            $status = true;
            if (@$student_id) {
                $updateStudent['ssoid'] = $ssoid;
                $password = Hash::make('123456789');
                $updateStudent['password'] = $password;
                Student::where('id', $student_id)->update($updateStudent);
                $status = true;
            }
            if ($status) {
                $currentSsoid = $ssoid;
                $modelstudentid = $Student->id;
                $modeltype = 'App\Models\Student';
                $studentroles = Config::get("global.student");
                $studentroless = ['role_id' => $studentroles,
                    'model_type' => $modeltype,
                    'model_id' => $Student->id,];
                $model_has_roles = DB::table('model_has_roles')->where('role_id', $studentroles)->where('model_type', $modeltype)->where('model_id', $modelstudentid)->first();
                if (!empty($model_has_roles)) {
                    $model_has_roles = DB::table('model_has_roles')->where('role_id', $studentroles)->where('model_type', $modeltype)->where('model_id', $modelstudentid)->update($studentroless);
                } else {
                    $model_has_roles = ModelHasRole::create($studentroless);
                }
                $password = '123456789';
                $exam_year = Config::get("global.form_supp_current_admission_session_id");
                $studentrole = Config::get("global.student");
                $credentials = (['ssoid' => $currentSsoid, 'password' => $password]);

                if (Auth::guard('student')->attempt($credentials)) {
                    Session::put("sdlogin", date("dmYhis"));
                    Session::put("current_student", $Student);
                    $student_id = Auth::guard('student')->user()->id;
                    Session::put('role_id', $studentrole);
                    $studentRoleId = config("global.student");

                    $enrollmentNumber = Auth::guard('student')->user()->enrollment;
                    Session::put("selected_student_enrollment_by_student", $enrollmentNumber);

                    $custom_component_obj->_setCountDownTimerDetails($studentRoleId);
                    return redirect()->route('preview_details', Crypt::encrypt($student_id))->with('message', 'Your profile has successfully updated.');
                }
            } else {
                return redirect()->back()->with('error', 'Invalid.Please enter valid details.')->withInput($request->all());
            }
        }
        $oldOtp = @$Student->otp;

        return view('student.by_pass_otp_already_student', compact('ssoDetails', 'title', 'oldOtp', 'student', 'essoid', 'estudent_id', 'mobilenumber', 'ssoid', 'model'));
    }

    public function allreadystudent(Request $request, $getssoid = null)
    {
        $role_id = @Session::get('role_id');
        $dob = null;

        if ($role_id == Config::get('global.student')) {
            $dob = @Auth::guard('student')->user()->dob;
            if (@$dob) {
                $dob = date("M d, Y", strtotime($dob));
            }
        } else {
            if (@$role_id) {
                return redirect()->route('dashboard')->with("error", "Please logout first to fill the form.");
            }
        }

        //
        $combo_name = 'student_login_new_registration_form_allow_or_not';
        $isOnlySingle = $this->master_details($combo_name);
	

        if (@$isOnlySingle[1]) {
            //check at least one strem should be active for today
            $streams = $this->_getAllowedStreams();
            $streams = json_decode($streams);
            $pos = strpos(@$streams->msg, ' Date&Time not');
			
            if ($pos > 0) {
                $isOnlySingle[1] = 0;
            }
        }
        $egetssoid = $getssoid;
        $getssoid = Crypt::decrypt($egetssoid);

        $custom_component_obj = new CustomComponent;
        if ($request->all()) {
            $inputs = $request->all();

            $request->validate(['enrollment' => 'required|numeric|digits:11', 'dob' => 'required', 'captcha' => 'required|numeric']);
            $captchaStatus = $custom_component_obj->checkCaptcha($inputs);
            if ($captchaStatus == false) {
                return redirect()->back()->with('error', 'Error : Captcha is invalid');
            }

            $checkstudentrecord = $custom_component_obj->checkstudentrecord($request->enrollment, $request->dob);
            $isAlreadyMapped = 0;
            $role_id = @Session::get('role_id');
            if ($role_id == Config::get('global.student')) {
                $authSSOID = Auth::guard('student')->user()->ssoid;
            }
            // dd($checkstudentrecord);
            //where("enrollment",$request->enrollment)
            if (@$checkstudentrecord->course && @$checkstudentrecord->exam_month && @$checkstudentrecord->adm_type) {
                if (@$authSSOID && !empty($authSSOID)) {
                    $isAlreadyMapped = Student::
                    where("course", @$checkstudentrecord->course)
                        ->where("ssoid", $authSSOID)
                        ->where("exam_month", @$checkstudentrecord->exam_month)
                        ->where("adm_type", @$checkstudentrecord->adm_type)
                        // ->whereNotNull('ssoid')
                        ->count();
                } else {
                }

                if (@$isAlreadyMapped > 0) {
                    return redirect()->route('studentsdashboards')->with("error", "You are already mapped with given course,stream and admission type combination.");
                }
            }

            if (@$checkstudentrecord->id && empty($checkstudentrecord->ssoid)) {
                //check is already login then go to after otp screen
                $role_id = @Session::get('role_id');
                if ($role_id == Config::get('global.student')) {
                    $authId = Auth::guard('student')->user()->id;
                    return redirect()->route('by_pass_otp_already_student', [Crypt::encrypt($checkstudentrecord->id), Crypt::encrypt($getssoid)])->with("succes", "Verify the student details.");
                } else {
                    $status = $this->_sendOTPToStudent($checkstudentrecord->id);
                    return redirect()->route('already_student_otp', [Crypt::encrypt($checkstudentrecord->id), Crypt::encrypt($getssoid)])->with("succes", "OTP sent on registered mobile number.");
                }
            } elseif (@$checkstudentrecord->id && !empty($checkstudentrecord->ssoid)) {
                $captchaImage = $custom_component_obj->generateCaptcha(1, 20, 150, 30);
                return redirect()->route('allreadystudent', Crypt::encrypt($getssoid))->with("error", "SSO already mapped with the given enrollment.");
            } else {
                $captchaImage = $custom_component_obj->generateCaptcha(1, 20, 150, 30);
                return redirect()->route('allreadystudent', Crypt::encrypt($getssoid))->with("error", "Student Not Found.");
            }
            //here find the student if not then
        }

        $captchaImage = $custom_component_obj->generateCaptcha(1, 20, 150, 30);
        return view('login.yet_not_mapped', compact('captchaImage', 'dob', 'getssoid', 'egetssoid', 'isOnlySingle', 'role_id'));
    }

    public function resend_student_otp(Request $request, $student_id = null)
    {

        $role_id = @Session::get('role_id');
        if (@$role_id) {
            return redirect()->route('dashboard')->with("error", "Please logout first to fill the form.");
        }

        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($estudent_id);

        $checkstudentrecord = Student::find($student_id);
        if (@$checkstudentrecord->id) {
            $status = $this->_sendOTPToStudent($checkstudentrecord->id);
            return redirect()->back()->with("message", "OTP has been sent on registered mobile number.");
        } else {
            return redirect()->route('landing')->with("error", "Student not found!");
        }
    }

    public function self_registration(Request $request)
    {
        $role_id = @Session::get('role_id');

        if ($role_id == Config::get('global.student')) {
        } else {
            if (@$role_id) {
                return redirect()->route('dashboard')->with("error", "Please logout first to fill the form.");
            }
        }


        //$this->_allowOnlyPostRequest($request);
        $page_title = 'Student Registration';
        $is_self_filled = encrypt('1');
        $model = "Student";
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $new_extra_reg_course = Session::get('new_extra_reg_course');
        if ($new_extra_reg_course == 10) {
            unset($course[12]);
        } else if ($new_extra_reg_course == 12) {
            unset($course[10]);
        }


        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);

        $streams = $this->_getAllowedStreams();
        $streams = json_decode($streams);
        $ssoidget = $request->ssoid;
        if (!$streams->status) {
            return redirect()->route('aicenterdashboard')->with('error', 'Failed! Registration date has been closed!');
        }
        $tempStreams = array();
        foreach (@$streams->info as $k => $stream) {
            $tempStreams[$stream->stream] = $stream_id[$stream->stream];
        }
        $stream_id = $tempStreams;

        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
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

        $selectedAiCenter = null;
        $selectedAiCode = null;
        if (count($request->all()) > 0 && @$request->action_type) {
            $custom_component_obj = new CustomComponent;
            $selectedAiCenter = $custom_component_obj->_getAiCentersDetailWithContactInfo($request->ai_code);
            $selectedAiCode = $request->ai_code;
        } else {
            if (count($request->all()) > 0) {
                $is_self_filled = Crypt::decrypt($request->is_self_filled);
                $getssoidstudent = Crypt::decrypt($ssoidget);
                $dataTobeSave = array();
                $inputs = $request->all();
                $role_id = @Session::get('role_id');
                $aicenter_id = Config::get("global.aicenter_id");
                if ($role_id == $aicenter_id) {
                    $currentAction = Route::getCurrentRoute()->getActionMethod();
                    if ($currentAction != "registration") {
                        $allowedOrNot = $this->_checkFormAllowedOrNot($request->stream);
                        $allowedOrNot = json_decode($allowedOrNot);
                        if (!$allowedOrNot->status) {
                            return redirect()->route('aicenterdashboard')->with('error', 'Failed! Registration date has been closed!');
                        }
                    }
                }
                $janAadharNumber = null;
                $janAadharAckNumber = null;
                if (strpos(@$inputs['jan_aadhar_number'], '-') !== false) {
                    $janAadharNumber = $janAadharAckNumber = $inputs['jan_aadhar_number'];
                } else {
                    if (@$inputs['jan_aadhar_number']) {
                        $janAadharNumber = $inputs['jan_aadhar_number'];
                    }
                }
                if (@$inputs['member_number']) {
                    if (@$janAadharNumber) {
                        $response = $this->_getJanAadharDetails($janAadharNumber);
                        $dataTobeSave = $this->_settingJanAadharDetails($response, $inputs['member_number'], $inputs['total_member'], $inputs['jan_id']);
                    }
                }
                if (@$dataTobeSave['Application']['jan_aadhar_number'] == null) {
                    $dataTobeSave['Application']['jan_aadhar_number'] = $janAadharNumber;
                }
                $responses = $this->RegistrationDetailsValidation($request);

                $responseFinal = null;
                if (@$responses) {
                    foreach ($responses as $k => $response) {
                        if (!$response['isValid']) {
                            $isValid = false;
                        }
                        $responseFinal[$k]['isValid'] = $response['isValid'];
                        $responseFinal[$k]['customerrors'] = $response['errors'];
                        $responseFinal[$k]['validator'] = $response['validator'];
                    }
                }

                if ($responses == false) {
                    if ($request->stream == 2 && $request->adm_type != 1) {
                        $fld = 'adm_type';
                        $errMsg = 'You allowed only "General Admission" option for stream 2.';
                        $validator->getMessageBag()->add($fld, $errMsg);
                        return redirect()->back()->withErrors($validator)->withInput($request->all());
                    }
                    $custom_component_obj = new CustomComponent;
                    $user_id = $custom_component_obj->_getAiCentersIdByAiCode(@$request->ai_code);

                    $aicode = $custom_component_obj->getAiCentersuserdatacode($user_id);

                    if ($request->are_you_from_rajasthan == '1') {
                        $alreadyPresent = null;
                        $dbJanId = null;
                        /* For Checking Start */
                        if (@$request->jan_id) {
                            $dbJanId = null;
                            if (@$request->jan_id == 0) {
                                $dbJanId = @$dataTobeSave['Application']['hof_jan_m_id'];
                            } else {
                                $dbJanId = @$request->jan_id;
                            }
                            $alreadyPresent = Student::join('applications', 'Students.id', '=', 'applications.student_id')
                                ->where("applications.jan_id", $dbJanId)->where("applications.jan_aadhar_number", @$request->jan_aadhar_number)
                                ->where('students.adm_type', @$request->adm_type)
                                ->where('students.course', @$request->course)
                                ->where("students.is_eligible", '1')
                                ->whereNull("applications.deleted_at")
                                ->count();
                        }
                        if (@$alreadyPresent) {
                            return redirect()->back()->with('error', 'Student Allready Registered with us.So you are not allowed.')->withInput($request->all());
                        }
                        /* For Checking End */

                        /*
                        $alreadyJanIdPresent = Application::where("jan_aadhar_number" , @$request->jan_aadhar_number)->count();
                        if(@$alreadyJanIdPresent) {
                            return redirect()->back()->with('error', 'Student already registred with us.So you are not allowed.')->withInput($request->all());
                        }
                        */

                        if (isset($dataTobeSave['Student']['dob']) && !empty($dataTobeSave['Student']['dob'])) {
                            $dob = $dataTobeSave['Student']['dob'];
                            $combo_name = 'min_dob_date';
                            $min_dob_date = $this->master_details($combo_name);
                            $dobs = date('Y-d-m', strtotime($dob));


                            if ($dobs == "1970-01-01") {
                                $dobArr = explode("/", $dob);
                                if (isset($dobArr[0]) && isset($dobArr[1]) && isset($dobArr[2])) {
                                    $dobs = $dobArr[2] . "-" . $dobArr[1] . "-" . $dobArr[0] . "";

                                }
                            }

                            if ($dobs > $min_dob_date[$request->course]) {
                                return redirect("/")->with('error', 'Student DOB should not be lesser than ' . date('d-m-Y', strtotime($min_dob_date[$request->course])) . ' not allowed.')->withInput($request->all());
                            }

                        }
                    } else {
                        $dataTobeSave['Student']['dob'] = null;
                    }

                    $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
                    $current_exam_month_id = Config::get("global.form_current_exam_month_id");


                    if ($request->are_you_from_rajasthan == 1) {
                        $studentarray = [
                            'first_name' => $dataTobeSave['Student']['name'],
                            'are_you_from_rajasthan' => $request->are_you_from_rajasthan,
                            'name' => $dataTobeSave['Student']['name'],
                            'gender_id' => $dataTobeSave['Student']['gender_id'],
                            'father_name' => $dataTobeSave['Student']['father_name'],
                            'mother_name' => $dataTobeSave['Student']['mother_name'],
                            'exam_year' => $current_admission_session_id,
                            'exam_month' => $request->stream,
                            'dob' => $dobs,
                            'mobile' => $dataTobeSave['Student']['mobile'],
                            'adm_type' => $request->adm_type, 'course' => $request->course, 'stream' => $request->stream, 'user_id' => $user_id, 'ai_code' => $aicode->ai_code];

                    } else {
                        $studentarray = ['are_you_from_rajasthan' => $request->are_you_from_rajasthan, 'adm_type' => $request->adm_type, 'course' => $request->course, 'stream' => $request->stream, 'user_id' => $user_id, 'ai_code' => $aicode->ai_code, 'exam_month' => $request->stream, 'exam_year' => $current_admission_session_id];
                    }

                    $countAlreadyPresentDetails = 0;
                    if ($request->are_you_from_rajasthan == 1) {
                        $countAlreadyPresentDetails = $this->checkIsAlreadyPresentCombination($studentarray);
                    }


                    if ($countAlreadyPresentDetails > 0) {
                        return redirect()->route('registration')->with('error', 'Failed!Something is Wrong. Student not created');
                    }
                    //here we studentarray ssoid
                    $Student = Student::create($studentarray);

                    if ($request->are_you_from_rajasthan == 1) {
                        if ($dataTobeSave['Application']['jan_mid'] == 0) {
                            $janid = $dataTobeSave['Application']['hof_jan_m_id'];
                        } else {
                            $janid = $dataTobeSave['Application']['jan_mid'];
                        }
                        if (empty($dataTobeSave['Application']['aadhar'])) {
                            $dataTobeSave['Application']['aadhar'] = null;
                        }
                        $applicationarray = ['jan_id' => $janid, 'exam_year' => $current_admission_session_id, 'aadhar_number' => $dataTobeSave['Application']['aadhar'], 'jan_aadhar_number' => $dataTobeSave['Application']['jan_aadhar_number'], 'student_name' => $dataTobeSave['Application']['student_name'], 'student_id' => $Student->id,];
                    } else {
                        $applicationarray = ['exam_year' => $current_admission_session_id, 'student_id' => $Student->id];
                    }
                    if (!empty($getssoidstudent)) {
                        @$ssoidgetdetails = $getssoidstudent;
                    } else {
                        @$ssoidgetdetails = $Student->id;
                    }

                    $application = Application::create($applicationarray);
                    $password = Hash::make('123456789');
                    $updatestudent = Student::find($Student->id);
                    $modelstudentid = $Student->id;
                    $updatestudent->password = $password;
                    $currentSsoid = $ssoidgetdetails;
                    $updatestudent->ssoid = $currentSsoid;
                    $updatestudent->is_self_filled = $is_self_filled;

                    $updatestudent->save();
                    $modeltype = 'App\Models\Student';
                    $studentroles = Config::get("global.student");
                    $studentroless = ['role_id' => $studentroles,
                        'model_type' => $modeltype,
                        'model_id' => $Student->id,];
                    $model_has_roles = DB::table('model_has_roles')->where('role_id', $studentroles)->where('model_type', $modeltype)->where('model_id', $modelstudentid)->first();
                    if (!empty($model_has_roles)) {
                        $model_has_roles = DB::table('model_has_roles')->where('role_id', $studentroles)->where('model_type', $modeltype)->where('model_id', $modelstudentid)->update($studentroless);
                    } else {
                        $model_has_roles = ModelHasRole::create($studentroless);
                    }

                    //attempt login with student table
                    $password = '123456789';
                    $exam_year = Config::get("global.form_supp_current_admission_session_id");
                    $studentrole = Config::get("global.student");
                    $credentials = (['ssoid' => $currentSsoid, 'password' => $password]);
                    if (Auth::guard('student')->attempt($credentials)) {

                        $custom_component_obj = new CustomComponent;
                        $custom_component_obj->_removeCountDownTimerDetails();

                        $studentRoleId = config("global.student");
                        $custom_component_obj->_setCountDownTimerDetails($studentRoleId);
                        Session::put("sdlogin", date("dmYhis"));
                        //Session::put("current_student_ssoid",$currentSsoid);
                        Session::put("current_student", $Student);
                        $student_id = Auth::guard('student')->user()->id;
                        Session::put('role_id', $studentrole);
                        // dd(Auth::guard('student')->user()->id);
                        // return redirect()->route('studentdashboard');
                        return redirect()->route('persoanl_details', Crypt::encrypt($Student->id))->with('message', 'Your profile has successfully created.Please complete the form.');
                    }


                    if ($Student->id && $application->id) {

                    } else {
                        return redirect()->route('/')->with('error', 'Failed! Student not created');
                    }
                } else {
                    $customerrors = implode(",", @$responseFinal[$k]['customerrors']);
                    return redirect()->back()->withErrors($responseFinal['validator'])->withInput($request->all());
                }
            }

        }
        return view('pages.self_registration', compact('selectedAiCenter', 'selectedAiCode', 'page_title', 'adm_types', 'are_you_from_rajasthan', 'course', 'stream_id', 'model', 'ssoidget', 'is_self_filled'));
    }

    public function dgs_self_registration(Request $request)
    {
        $role_id = @Session::get('role_id');

        if ($role_id == Config::get('global.student')) {
        } else {
            if (@$role_id) {
                return redirect()->route('dashboard')->with("error", "Please logout first to fill the form.");
            }
        }
        //$this->_allowOnlyPostRequest($request);
        $page_title = 'Disadvantage Student';
        $is_self_filled = encrypt('1');
        $model = "Student";
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $new_extra_reg_course = Session::get('new_extra_reg_course');
        if ($new_extra_reg_course == 10) {
            unset($course[12]);
        } else if ($new_extra_reg_course == 12) {
            unset($course[10]);
        }
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);

        $streams = $this->_getAllowedStreams();
        $is_dgs = Auth::guard('student')->user()->is_dgs;
        if (@$is_dgs && $is_dgs == 1) {
            $streams = '{"status":true,"msg":null,"info":[{"stream":"1"},{"stream":"2"}]}';
        }
        $streams = json_decode($streams);
        $ssoidget = @$request->ssoid;

        if (!$streams->status) {
            return redirect()->route('aicenterdashboard')->with('error', 'Failed! Registration date has been closed!');
        }
        $tempStreams = array();
        foreach (@$streams->info as $k => $stream) {
            $tempStreams[$stream->stream] = $stream_id[$stream->stream];
        }
        $stream_id = $tempStreams;

        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
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

        $selectedAiCenter = null;
        $selectedAiCode = null;
        // dd(Auth::guard('student')->user()->course);
        $student_id = Auth::guard('student')->user()->id;
        $studentDetails = Student::where('id', $student_id)->first();
        if (@$studentDetails->ai_code) {
            $custom_component_obj = new CustomComponent;
            $selectedAiCenter = $custom_component_obj->_getAiCentersDetailWithContactInfo($studentDetails->ai_code);
            $selectedAiCode = $studentDetails->ai_code;
        }

        if (count($request->all()) > 0) {
            $is_self_filled = Crypt::decrypt($request->is_self_filled);

            $getssoidstudent = null;
            if (@$ssoidget) {
                $getssoidstudent = Crypt::decrypt(@$ssoidget);
            }

            $dataTobeSave = array();
            $inputs = $request->all();

            $role_id = @Session::get('role_id');
            $aicenter_id = Config::get("global.aicenter_id");


            if ($role_id == $aicenter_id) {
                $currentAction = Route::getCurrentRoute()->getActionMethod();
                if ($currentAction != "registration") {
                    $allowedOrNot = $this->_checkFormAllowedOrNot($request->stream);
                    $allowedOrNot = json_decode($allowedOrNot);
                    if (!$allowedOrNot->status) {
                        return redirect()->route('aicenterdashboard')->with('error', 'Failed! Registration date has been closed!');
                    }
                }
            }
            $janAadharNumber = null;
            $janAadharAckNumber = null;
            if (strpos(@$inputs['jan_aadhar_number'], '-') !== false) {
                $janAadharNumber = $janAadharAckNumber = $inputs['jan_aadhar_number'];
            } else {
                if (@$inputs['jan_aadhar_number']) {
                    $janAadharNumber = $inputs['jan_aadhar_number'];
                }
            }
            if (@$inputs['member_number']) {
                if (@$janAadharNumber) {
                    $response = $this->_getJanAadharDetails($janAadharNumber);
                    $dataTobeSave = $this->_settingJanAadharDetails($response, $inputs['member_number'], $inputs['total_member'], $inputs['jan_id']);
                }
            }
            if (@$dataTobeSave['Application']['jan_aadhar_number'] == null) {
                $dataTobeSave['Application']['jan_aadhar_number'] = $janAadharNumber;
            }

            $responses = $this->DGSRegistrationDetailsValidation($request);

            $responseFinal = null;
            if (@$responses) {
                foreach ($responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];
                }
            }

            if ($responses == false) {


                if ($request->stream == 2 && $request->adm_type != 1) {
                    $fld = 'adm_type';
                    $errMsg = 'You allowed only "General Admission" option for stream 2.';
                    $validator->getMessageBag()->add($fld, $errMsg);
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }
                $custom_component_obj = new CustomComponent;
                $user_id = $custom_component_obj->_getAiCentersIdByAiCode(@$request->ai_code);

                $aicode = $custom_component_obj->getAiCentersuserdatacode($user_id);


                if ($request->are_you_from_rajasthan == '1') {
                    $alreadyPresent = null;
                    $dbJanId = null;
                    /* For Checking Start */
                    if (@$request->jan_id) {
                        $dbJanId = null;
                        if (@$request->jan_id == 0) {
                            $dbJanId = @$dataTobeSave['Application']['hof_jan_m_id'];
                        } else {
                            $dbJanId = @$request->jan_id;
                        }
                        $alreadyPresent = Student::join('applications', 'Students.id', '=', 'applications.student_id')
                            ->where("applications.jan_id", $dbJanId)->where("applications.jan_aadhar_number", @$request->jan_aadhar_number)
                            ->where('students.adm_type', @$request->adm_type)
                            ->where('students.course', @$request->course)
                            ->where("students.is_eligible", '1')
                            ->whereNull("applications.deleted_at")
                            ->count();
                    }
                    if (@$alreadyPresent) {
                        return redirect()->back()->with('error', 'Student already Registered with us.So you are not allowed.')->withInput($request->all());
                    }
                    /* For Checking End */

                    /*
                    $alreadyJanIdPresent = Application::where("jan_aadhar_number" , @$request->jan_aadhar_number)->count();
                    if(@$alreadyJanIdPresent) {
                        return redirect()->back()->with('error', 'Student already registred with us.So you are not allowed.')->withInput($request->all());
                    }
                    */

                    if (isset($dataTobeSave['Student']['dob']) && !empty($dataTobeSave['Student']['dob'])) {
                        $dob = $dataTobeSave['Student']['dob'];
                        $combo_name = 'min_dob_date';
                        $min_dob_date = $this->master_details($combo_name);
                        $dobs = date('Y-d-m', strtotime($dob));


                        if ($dobs == "1970-01-01") {
                            $dobArr = explode("/", $dob);
                            if (isset($dobArr[0]) && isset($dobArr[1]) && isset($dobArr[2])) {
                                $dobs = $dobArr[2] . "-" . $dobArr[1] . "-" . $dobArr[0] . "";

                            }
                        }

                        if ($dobs > $min_dob_date[$request->course]) {
                            return redirect("/")->with('error', 'Student DOB should not be lesser than ' . date('d-m-Y', strtotime($min_dob_date[$request->course])) . ' not allowed.')->withInput($request->all());
                        }

                    }
                } else {
                    $dataTobeSave['Student']['dob'] = null;
                }

                $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
                $current_exam_month_id = Config::get("global.form_current_exam_month_id");


                // dd($request->are_you_from_rajasthan);
                if ($request->are_you_from_rajasthan == 1) {
                    $studentarray = [
                        'first_name' => $dataTobeSave['Student']['name'],
                        'are_you_from_rajasthan' => $request->are_you_from_rajasthan,
                        'name' => $dataTobeSave['Student']['name'],
                        'gender_id' => $dataTobeSave['Student']['gender_id'],
                        'father_name' => $dataTobeSave['Student']['father_name'],
                        'mother_name' => $dataTobeSave['Student']['mother_name'],
                        'exam_year' => $current_admission_session_id,
                        'exam_month' => $request->stream,
                        'dob' => $dobs,
                        'mobile' => $dataTobeSave['Student']['mobile'],
                        'adm_type' => $request->adm_type, 'course' => $request->course, 'stream' => $request->stream, 'user_id' => $user_id, 'ai_code' => $aicode->ai_code];
                } else {
                    $studentarray = ['are_you_from_rajasthan' => $request->are_you_from_rajasthan, 'adm_type' => $request->adm_type, 'course' => $request->course, 'stream' => $request->stream, 'user_id' => $user_id, 'ai_code' => $aicode->ai_code, 'exam_month' => $request->stream, 'exam_year' => $current_admission_session_id];
                }

                $countAlreadyPresentDetails = 0;
                if (@$request->are_you_from_rajasthan && $request->are_you_from_rajasthan == 1) {
                    $countAlreadyPresentDetails = $this->checkIsAlreadyPresentCombination($studentarray);
                }


                if ($countAlreadyPresentDetails > 0) {
                    return redirect()->route('registration')->with('error', 'Failed!Something is Wrong. Student not created');
                }

                Student::where('id', $student_id)->update($studentarray);
                $Student = Student::where('id', $student_id)->first();

                if ($request->are_you_from_rajasthan == 1) {
                    if ($dataTobeSave['Application']['jan_mid'] == 0) {
                        $janid = $dataTobeSave['Application']['hof_jan_m_id'];
                    } else {
                        $janid = $dataTobeSave['Application']['jan_mid'];
                    }
                    if (empty($dataTobeSave['Application']['aadhar'])) {
                        $dataTobeSave['Application']['aadhar'] = null;
                    }
                    $applicationarray = ['jan_id' => $janid, 'exam_year' => $current_admission_session_id, 'aadhar_number' => $dataTobeSave['Application']['aadhar'], 'jan_aadhar_number' => $dataTobeSave['Application']['jan_aadhar_number'], 'student_name' => $dataTobeSave['Application']['student_name'], 'student_id' => $Student->id,];
                } else {
                    $applicationarray = ['exam_year' => $current_admission_session_id, 'student_id' => $Student->id];
                }
                if ($Student->is_dgs == 1) {
                    $applicationarray['disadvantage_group'] = 6; // Students of Registered Juvenile Homes(राजकीय पंजीकृत बालसुधार गृह के छात्र)
                }

                if (!empty($getssoidstudent)) {
                    @$ssoidgetdetails = $getssoidstudent;
                } else {
                    $ssoidgetdetails = $Student->username;
                }
                $ssoidgetdetails = $Student->username;

                $application = Application::create($applicationarray);
                // dd($application);
                // $password = Hash::make('123456789');
                $updatestudent = Student::find($Student->id);
                $modelstudentid = $Student->id;

                $currentSsoid = $ssoidgetdetails;
                $updatestudent->ssoid = $currentSsoid;
                $updatestudent->is_self_filled = $is_self_filled;

                $updatestudent->save();


                $modeltype = 'App\Models\Student';
                $studentroles = Config::get("global.student");
                $studentroless = ['role_id' => $studentroles,
                    'model_type' => $modeltype,
                    'model_id' => $Student->id,];
                $model_has_roles = DB::table('model_has_roles')->where('role_id', $studentroles)->where('model_type', $modeltype)->where('model_id', $modelstudentid)->first();
                if (!empty($model_has_roles)) {
                    $model_has_roles = DB::table('model_has_roles')->where('role_id', $studentroles)->where('model_type', $modeltype)->where('model_id', $modelstudentid)->update($studentroless);
                } else {
                    $model_has_roles = ModelHasRole::create($studentroless);
                }

                $studentrole = Config::get("global.student");

                if (@$studentDetails->is_dgs && $studentDetails->is_dgs == 1) {
                    Session::put("sdlogin", date("dmYhis"));
                    //Session::put("current_student_ssoid",$currentSsoid);
                    Session::put("current_student", $Student);
                    $student_id = Auth::guard('student')->user()->id;
                    Session::put('role_id', $studentrole);
                    // dd(Auth::guard('student')->user()->id);
                    // return redirect()->route('studentdashboard');
                    return redirect()->route('persoanl_details', Crypt::encrypt($Student->id))->with('message', 'Your profile has successfully created.Please complete the form.');
                } else {
                    //attempt login with student table
                    $password = '123456789';
                    $exam_year = Config::get("global.form_supp_current_admission_session_id");

                    $credentials = (['ssoid' => $currentSsoid, 'password' => $password]);
                    if (Auth::guard('student')->attempt($credentials)) {

                        $custom_component_obj = new CustomComponent;
                        $custom_component_obj->_removeCountDownTimerDetails();

                        $studentRoleId = config("global.student");
                        $custom_component_obj->_setCountDownTimerDetails($studentRoleId);
                        Session::put("sdlogin", date("dmYhis"));
                        //Session::put("current_student_ssoid",$currentSsoid);
                        Session::put("current_student", $Student);
                        $student_id = Auth::guard('student')->user()->id;
                        Session::put('role_id', $studentrole);
                        // dd(Auth::guard('student')->user()->id);
                        // return redirect()->route('studentdashboard');
                        return redirect()->route('persoanl_details', Crypt::encrypt($Student->id))->with('message', 'Your profile has successfully created.Please complete the form.');
                    }
                }

                if ($Student->id && $application->id) {

                } else {
                    return redirect()->route('/')->with('error', 'Failed! Student not created');
                }
            } else {
                $customerrors = implode(",", @$responseFinal[$k]['customerrors']);
                return redirect()->back()->withErrors($responseFinal['validator'])->withInput($request->all());
            }
        }


        return view('pages.dgs_self_registration', compact('selectedAiCenter', 'selectedAiCode', 'page_title', 'adm_types', 'are_you_from_rajasthan', 'course', 'stream_id', 'model', 'ssoidget', 'is_self_filled'));
    }


    public function single_screen_dates(Request $request)
    {
        $title = "Single Screen Dates";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $scrCon = array();
        $moduleName = SingleScreenDate::groupBy('module_id')->pluck('module', 'module_id');
        if (@$request->module_id && !empty(@$request->module_id)) {
            $scrCon = ['module_id' => @$request->module_id];
        }

        $subModuleName = SingleScreenDate::where(@$scrCon)->pluck('sub_module', 'id');
        $custom_component_obj = new CustomComponent;
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'yesno';
        $yesno = $this->master_details($combo_name);
        $exportBtn = array();

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
                "lbl" => "Module Name",
                'fld' => 'module_id',
                'input_type' => 'select',
                'options' => $moduleName,
                'placeholder' => 'Module Name',
                'dbtbl' => 'single_screen_dates',
                'status' => true
            ),
            array(
                "lbl" => "Sub Module",
                'fld' => 'id',
                'input_type' => 'select',
                'options' => $subModuleName,
                'placeholder' => 'Sub Module',
                'dbtbl' => 'single_screen_dates',
                'status' => true
            ),
        );

        $conditions = array();
        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }

        Session::put($formId . '_conditions', $conditions);
        $master = $custom_component_obj->getSingleScreenData($formId, false);
		
        $finalArr = array();
        foreach ($master as $k => $v) {
            $finalArr[$v->module_id][$v->sub_module_id]['module_name'] = @$v->module;
            $finalArr[$v->module_id][$v->sub_module_id]['sub_module_name'] = @$v->sub_module;
            $finalArr[$v->module_id][$v->sub_module_id]['status'] = @$v->status;
            $finalArr[$v->module_id][$v->sub_module_id]['table_name'] = @$v->table_name;
            $finalArr[$v->module_id][$v->sub_module_id]['global_variables'] = null;
            $finalArr[$v->module_id][$v->sub_module_id]['table_details'] = null;

            if (@$v->global_variables) {
                $global_variables = explode(",", $v->global_variables);
                $finalArr[$v->module_id][$v->sub_module_id]['global_variables'] = @$global_variables;
            }
            if (@$v->table_details) {
                $table_details = explode(",", $v->table_details);
                $finalArr[$v->module_id][$v->sub_module_id]['table_details'] = @$table_details;
            }
        }

        return view('pages.single_screen_dates', compact('breadcrumbs', 'subModuleName', 'gender_id', 'stream_id', 'yesno', 'moduleName', 'title', 'filters', 'exportBtn', 'finalArr', 'master', 'formId'));

    }


    public function getSubModuleList(Request $request)
    {
        $module_id = $request->module_id;
        if (!empty($module_id)) {
            $data = SingleScreenDate::where('module_id', $module_id)->pluck('sub_module', 'id');
        } else {
            $data = SingleScreenDate::pluck('sub_module', 'id');
        }
        return $data;

    }


    public function updateAjaxSingleScreenDetails(Request $request)
    {

        $indexValue = @$request->variable_value;
        $indexItem = @$request->variable_name;
        $table_name = @$request->table_name;
        $field_name = @$request->field_name;
        if (@$request->table_name && @$table_name) {
            $response = $this->updateSingleScreenDetails($table_name, $indexItem, $indexValue, $field_name);
        } else {
            $response = $this->updateInGlobalVariable($indexItem, $indexValue);
        }
        return $response;
    }


    public function verfication_aicodes_details(Request $request, $user_type = null)
    {
        if (empty($user_type)) {
            return redirect()->back()->with('error', "Enter User Type");
        }
        $title = ucfirst($user_type) . " Verification Aicodes Details ";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $exportBtn = array();
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
        $role_id = null;
        if (@$user_type == 'ao') {
            $role_id = Config::get('global.academicofficer_id');
        } elseif ($user_type == "verifier") {
            $role_id = Config::get('global.verifier_id');
        }
        $role_user_id = ModelHasRole::where('role_id', $role_id)->pluck('model_id', 'model_id');
        $user_data = User::whereIn('id', $role_user_id)->pluck('ssoid', 'id');

        $filters = array();
        $filters = array(
            array(
                "lbl" => "SSO",
                'fld' => 'user_id',
                'input_type' => 'select',
                'options' => $user_data,
                'placeholder' => 'SSO',
                'status' => true
            ),
            array(
                "lbl" => "Aicodes",
                'fld' => 'aicodes',
                'input_type' => 'text',
                'placeholder' => "Aicodes",
                'search_type' => "like", //like
            ),
        );

        $conditions = array();
        if ($request->all()) {
            $inputs = $request->all();

            foreach ($filters as $ik => $iv) {

                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];

                } else {
                    if (!empty($inputs[$iv['fld']])) {
                        $conditions[$iv['fld']] = $inputs[$iv['fld']];
                    }
                }
            }
        }
        $custom_component_obj = new CustomComponent;
        Session::put($formId . '_conditions', $conditions);
        $master = $custom_component_obj->getVerficationAicodesData($user_type, $formId, true);
        return view('pages.verification_aicodes_details', compact('master', 'user_data', 'exportBtn', 'breadcrumbs', 'filters', 'title', 'user_type'));

    }

    public function verficationDataUpdate(Request $request)
    {
        $ai_codes = $request->aicodes;
        $id = crypt::decrypt($request->id);
        $table_type = crypt::decrypt($request->table_type);
        $table_name = @$table_type . "_aicodes";
        if (empty(@$request->user_id)) {
            return redirect()->back()->with('error', "Select SSO");
        }
        $ai_codes = str_replace('"', "'", $ai_codes);
        $updateData = ['aicodes' => $ai_codes, 'user_id' => $request->user_id];
        $this->updateStudentLog(@$table_name, $id, @$table_name, '1');
        $master = Db::table(@$table_name)->where('id', $id)->update($updateData);
        return redirect()->route('verfication_aicodes_details', $table_type)->with('message', "Ai Codes Update Succesfully.");

    }


}

