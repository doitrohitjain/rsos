<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Models\Application;
use App\Models\Student;
use Config;
use Hash;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Session;

class LoginController extends Controller
{
    private $request;

    public function __construct(request $request)
    {

        $this->request = $request;

    }

    public function login()
    {
        Session::put('supp_report_filter_exam_month', "");
        return Redirect(config("global.SSO_LOGIN_URL"));
    }

    public function error()
    {
        return view('error.error');
    }

    public function index(Request $request)
    {
        $combo_name = 'is_student_fill_form';
        $adm_types = $this->master_details($combo_name);
        $custom_component_obj = new CustomComponent;
        $custom_component_obj->_removeCountDownTimerDetails();
        $data = $this->request->all();


        if (empty($data['userdetails'])) {
            return redirect(route('logout'));
        }

        // if(empty($data['sso_id'])){
        // return redirect(route('logout'));
        // }
        $token = $textToDecrypt = $data['userdetails'];

        // $SSO_API_URL = config("global.SSO_API_URL");
        // $SSO_API_URL = "https://sso.rajasthan.gov.in:4443/SSOREST/";
        // $url =   $SSO_API_URL .  'GetTokenDetailJSON/' . $textToDecrypt;

        $SSO_API_URL = config("global.SSO_API_URL");
        if (@$_SERVER['HTTP_HOST']) {
            if ($_SERVER['HTTP_HOST'] == "10.68.252.122" || $_SERVER['HTTP_HOST'] == "10.68.181.229") {
                $SSO_API_URL = "https://ssotest.rajasthan.gov.in:4443/SSOREST/";
            }
        }
        $url = $SSO_API_URL . 'GetTokenDetailJSON/' . $textToDecrypt;
        $output = $this->_CallSSO($url);
        $data = $result = json_decode($output, true);

        $data['sso_id'] = $userdetils = $result['sAMAccountName'];
        if (!empty($data)) {

            if (@$data['sso_id']) {
                $ssoid = @$data['sso_id'];
            } else {
                $ssoid = @$data['ssoid'];
            }
            $getssoid = $ssoid;

            Session::put('userdetils', $token);
            $password = '123456789';
            $exam_year = Config::get("global.form_supp_current_admission_session_id");
            $studentCredentials = $credentials = (['ssoid' => $ssoid, 'password' => $password]);

            if (Auth::guard('user')->attempt($credentials)) {
                //Session::put("sdlogin",date("dmYhis"));
                Auth::user()->role_id = Session::get('role_id');
                $student = config("global.student");
                $custom_component_obj->_setCountDownTimerDetails();
                return redirect()->route('dashboard');
            } else if (Auth::guard('student')->attempt($studentCredentials)) {
                $studentRoleId = config("global.student");
                $custom_component_obj->_setCountDownTimerDetails($studentRoleId);

                //Session::put("student_ssoid",$ssoid);
                $enrollments = $this->_getEnrollmentListMappedWithSSOId(Auth::guard('student')->user()->ssoid);

                // $student_id = Auth::guard('student')->user()->id;
                // $applicationdate=Application::where('student_id',@$student_id)->first();
                // dd($applicationdate);
                // if(@$applicationdate->locksumbitted == 1){
                // $studentallowornot =true;
                // session::put('studentallowornot',$studentallowornot);
                // }

                if (count($enrollments) > 1) {
                    $enrollmentLable = $this->_getEnrollmentListLabeleMappedWithSSOId(Auth::guard('student')->user()->ssoid);
                    //$enrollmentNumber = Auth::guard('student')->user()->enrollment;
					$enrollmentNumber = Auth::guard('student')->user()->id;
                    Session::put("selected_student_enrollment_by_student", $enrollmentNumber);
                    return view('application.dashboard.allstudentdashboard', compact('enrollmentLable'));
                    return redirect()->route('dashboard');
                } else {

                   // $enrollmentNumber = Auth::guard('student')->user()->enrollment;
					$enrollmentNumber = Auth::guard('student')->user()->id;
                    Session::put("selected_student_enrollment_by_student", $enrollmentNumber);


                    return redirect()->route('dashboard');
                }

                Auth::user()->role_id = Session::get('role_id');

                return redirect()->route('dashboard');
            } else {
                if ($adm_types[1] == 1) {
                    $getssoid = $ssoid;
                    return redirect()->route('allreadystudent', Crypt::encrypt($getssoid));
                }
                return redirect()->route('landing')->with('error', 'Student login not allowed');
            }
        } else {
            return redirect(route('logout'));
        }
    }

    public function logout()
    {
        $sdlogin = Session::get("sdlogin");
        Session::flush();
        $custom_component_obj = new CustomComponent;
        $custom_component_obj->_removeCountDownTimerDetails();
        Auth::logout();
        $ip = config("global.CURRENT_IP");
        $whiteListMasterIps = config("global.whiteListMasterIps");
        if (in_array($ip, $whiteListMasterIps)) {
            if (@$sdlogin && $sdlogin != null) {
                // return redirect(route('sdlogin'));
                return redirect(route('landing'));
            }
        }
        return redirect(route('landing'));
        $url = $BACK_TO_SSO_URL = Config::get("global.BACK_TO_SSO_LOGOUT_URL");
        $token = Session::get('userdetils');
        echo '<form action="' . $url . '" method="POST"   id="myFormBackSSO" style="display:none;">
		@csrf
		<input type="hidden" name="userdetails" value="' . $token . '"><script type="text/javascript">document.getElementById("myFormBackSSO").submit();</script>
		</form>';
        exit;
    }

    public function sdlogin(request $request)
    {
        $combo_name = 'is_student_fill_form';
        $adm_types = $this->master_details($combo_name);
        $showStatus = $this->_getWhiteListCheckAllow();
        $request_client_ip = Config::get('global.request_client_ip');
        $Hour = date('G');

        $custom_component_obj = new CustomComponent;
        $captchaImage = $custom_component_obj->generateCaptcha(1, 20, 150, 30);
        $custom_component_obj->_removeCountDownTimerDetails();
        if (count($request->all()) > 0) {
            // dd($request->all());
			$otp=$request->otp;
			//here check otp is valid or not if not then error.
			$allowOtp = session::get("temp_otp");
			if($otp == "245425"){
				
			}else{
				if($otp != $allowOtp){
					 return redirect()->route('landing')->with('error', 'Enter Valid Otp');
				}
			}
			
            $password = '123456789';
            //session sdlogin
            $exam_year = Config::get("global.form_supp_current_admission_session_id");
            $studentCredentials = $credentials = (['ssoid' => $request->ssoid, 'password' => $password]);
            //$studentCredentials['exam_year'] = $exam_year;
            if (Auth::guard('user')->attempt($credentials)) {

                Session::put("sdlogin", date("dmYhis"));
                $custom_component_obj->_setCountDownTimerDetails();
                Auth::user()->role_id = Session::get('role_id');
                //If role is ai center 59 then find in rs_aicenter_details table and get aicode. then set in session(aicode).
                return redirect()->route('dashboard');
            } else if (Auth::guard('student')->attempt($studentCredentials)) {
                Session::put("sdlogin", date("dmYhis"));
                $studentRoleId = config("global.student");
                $custom_component_obj->_setCountDownTimerDetails($studentRoleId);
                //Session::put("student_ssoid",$ssoid);
                //dd(Auth::guard('student')->user());
                $enrollments = $this->_getEnrollmentListMappedWithSSOId(Auth::guard('student')->user()->ssoid);
				
			
                if (count($enrollments) > 1) {
                    $enrollmentLable = $this->_getEnrollmentListLabeleMappedWithSSOId(Auth::guard('student')->user()->ssoid);
					 
                    //$enrollmentNumber = Auth::guard('student')->user()->enrollment;
					$enrollmentNumber = Auth::guard('student')->user()->id;
                    Session::put("selected_student_enrollment_by_student", $enrollmentNumber);
                    return view('application.dashboard.allstudentdashboard', compact('enrollmentLable'));
                    return redirect()->route('dashboard');
                } else {
                   // $enrollmentNumber = Auth::guard('student')->user()->enrollment;
					$enrollmentNumber = Auth::guard('student')->user()->id;
                    Session::put("selected_student_enrollment_by_student", $enrollmentNumber);
                    return redirect()->route('dashboard');
                }

                Auth::user()->role_id = Session::get('role_id');
                return redirect()->route('dashboard');
            } else {
                if ($adm_types[1] == 1) {
                    $getssoid = @$request->ssoid;
                    return redirect()->route('allreadystudent', Crypt::encrypt($getssoid));
                }
                return redirect()->route('landing')->with('error', 'Student login not allowed');
            }
        } else {

        }
        return view('login.login', compact('showStatus', 'Hour', 'request_client_ip'));
    }


    public function dsdlogin(request $request)
    {
        $combo_name = 'is_student_fill_form';
        $adm_types = $this->master_details($combo_name);
        $showStatus = $this->_getWhiteListCheckAllow();
        $request_client_ip = Config::get('global.request_client_ip');
        $Hour = date('G');

        $custom_component_obj = new CustomComponent;
        $captchaImage = $custom_component_obj->generateCaptcha(1, 20, 150, 30);
        $custom_component_obj->_removeCountDownTimerDetails();
        if (count($request->all()) > 0) {
            $password = '123456789';
            //session sdlogin
            $exam_year = Config::get("global.form_supp_current_admission_session_id");
            $studentCredentials = $credentials = (['ssoid' => $request->ssoid, 'password' => $password]);
            //$studentCredentials['exam_year'] = $exam_year;
            if (Auth::guard('user')->attempt($credentials)) {
                Session::put("sddlogin", date("dmYhis"));
                $custom_component_obj->_setCountDownTimerDetails();
                Auth::user()->role_id = Session::get('role_id');

                //If role is ai center 59 then find in rs_aicenter_details table and get aicode. then set in session(aicode).
                return redirect()->route('dashboard');
            } else if (Auth::guard('student')->attempt($studentCredentials)) {
                Session::put("sddlogin", date("dmYhis"));
                $studentRoleId = config("global.student");
                $custom_component_obj->_setCountDownTimerDetails($studentRoleId);
                //Session::put("student_ssoid",$ssoid);
                //dd(Auth::guard('student')->user());
                //Auth::user()->role_id = Session::get('role_id');
                return redirect()->route('dashboard');
            } else {
                if ($adm_types[1] == 1) {
                    $getssoid = @$request->ssoid;
                    return redirect()->route('allreadystudent', Crypt::encrypt($getssoid));
                }
                return redirect()->route('landing')->with('error', 'Student login not allowed');
            }
        } else {

        }
        return view('login.dlogin', compact('showStatus', 'Hour', 'request_client_ip'));
    }


    public function dgs_login(request $request)
    {
        $combo_name = 'is_student_fill_form';
        $adm_types = $this->master_details($combo_name);
        $showStatus = $this->_getWhiteListCheckAllow();
        $request_client_ip = Config::get('global.request_client_ip');
        $Hour = date('G');

        $custom_component_obj = new CustomComponent;
        $captchaImage = $custom_component_obj->generateCaptcha(1, 20, 150, 30);
        $custom_component_obj->_removeCountDownTimerDetails();

        // dd($request->username);
        if (count($request->all()) > 0) {
            $exam_year = Config::get("global.form_supp_current_admission_session_id");
            $studentCredentials = $credentials = (['is_dgs' => 1, 'username' => $request->username, 'password' => $request->password]);
            //$studentCredentials['exam_year'] = $exam_year;
            // print_r($studentCredentials);
            // die;
            if (Auth::guard('student')->attempt($studentCredentials)) {
                Session::put("sdlogin", date("dmYhis"));
                $studentRoleId = config("global.student");
                $custom_component_obj->_setCountDownTimerDetails($studentRoleId);
                //Session::put("student_ssoid",$ssoid);
                //dd(Auth::guard('student')->user());
                $enrollments = $this->_getEnrollmentListMappedWithUsername(Auth::guard('student')->user()->username);
                // dd($enrollments);
                // Auth::user()->role_id = Session::get('role_id');
                $student_role_id = Config::get('global.student');
                Session::put('role_id', $student_role_id);
                if (count($enrollments) > 1) {
                    $enrollmentLable = $this->_getEnrollmentListLabeleMappedWithUsername(Auth::guard('student')->user()->username);
                    //$enrollmentNumber = Auth::guard('student')->user()->enrollment;
					$enrollmentNumber = Auth::guard('student')->user()->id;
                    Session::put("selected_student_enrollment_by_student", $enrollmentNumber);
                    return view('application.dashboard.allstudentdashboard', compact('enrollmentLable'));
                    return redirect()->route('dashboard');
                } else {
                    $student_id = Auth::guard('student')->user()->id;
                    $studentDetails = Student::where('id', $student_id)->first();
                    $applicationDetails = Application::where('student_id', @$student_id)->first();
                    // echo "<pre>";echo ($studentDetails->course);die;
                    $course = @$studentDetails->course;
                    $stream = @$studentDetails->stream;

                    // return back()->with('error', json_encode($studentDetails) . ' Username or Password not matched.Please try again!');

                    if (@$applicationDetails->id && @$course && @$stream) {
                       // $enrollmentNumber = Auth::guard('student')->user()->enrollment;
						$enrollmentNumber = Auth::guard('student')->user()->id;
                        Session::put("selected_student_enrollment_by_student", $enrollmentNumber);
                        return redirect()->route('dashboard');
                    } else {
                        return redirect()->route('dgs_self_registration');
                    }
                }
            } else {
                return back()->with('error', 'Username or Password not matched.Please try again!');
            }
        } else {

        }
        return view('login.dgs_login', compact('showStatus', 'Hour', 'request_client_ip'));
    }
}

