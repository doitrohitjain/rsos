<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Models\Application;
use App\Models\ChangeRequestErequest;
use App\Models\ChangeRequestPaymentIssue;
use App\Models\ChangeRequeStstudent;
use App\Models\ChangeRequestStudentTarils;
use App\Models\Registration;
use App\Models\Student;
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

class ChangeRequestPaymentController extends Controller
{
    function __construct()
    {
        //parent::__construct(); 
        // echo "<h1>Please wait we are coming soon.</h1>";die;
    }

    public function index()
    {
        // code here
    }

    public function send_sample_sms_to_student(Request $request, $mobile = '9999919241')
    {

        // $student = Student::where('mobile','=', $mobile)->first();

        // if(empty($student)){
        // echo $mobile. " Not Found";die;
        // }
        $paymentUrl = $_SERVER['HTTP_HOST'] . "/rsos";
        $enrollment = 888;
        $application_fee = 999;
        $sms = null;
        if ($application_fee > 0) {
            $sms = 'Dear Applicant, Your application has been registered successfully with enrollment number ' . $enrollment . '. Please pay admission fees Rs.' . $application_fee . ' by clicking on URL  ' . $paymentUrl . ' to complete your admission application.-RSOS,GoR';
        }
        // $student_id = $student->id;
        //$smsStatus = $this->_sendLockSubmittedMessage($student_id);echo $smsStatus;die;
        $smsStatus = $this->_samplesendSMS($mobile, $sms);
        echo $mobile . " sent " . $smsStatus;
        die;
    }

    public function change_request_listing_payment_issues(Request $request)
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
        $combo_name = 'yes_no';
        $yes_nos = $this->master_details($combo_name);

        $yes_no = $this->master_details('yesno');
        $title = "Change Request Payment Issues Report";
        $table_id = "Change_Request_Payment_Issue_Report";
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
                "lbl" => "Email",
                'fld' => 'email',
                'input_type' => 'text',
                'placeholder' => "Student Email",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Student Mobile",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Student Mobile",
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
            ),
            array(
                "lbl" => "Is Issue Resolved",
                'fld' => 'is_archived',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is Resolved',
                'dbtbl' => 'payment_issues'
            ),
            array(
                "lbl" => "Fee Amount",
                'fld' => 'total',
                'input_type' => 'text',
                'placeholder' => "Fee Amount",
                'dbtbl' => 'student_fees',
            ),
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
                "lbl" => "Fee Amount",
                'fld' => 'total',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Is Resolved",
                'fld' => 'is_archived',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            array(
                "lbl" => "Challan Number",
                'fld' => 'update_change_requests_challan_tid'
            )
        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $academicyear_id = config("global.form_admission_academicyear_id");
        //$exam_month = Config::get('global.form_current_exam_month_id');
        $conditions["students.exam_year"] = @$academicyear_id;
        //$conditions["students.exam_month"] = @$academicyear_id; 
        $aiCenters = $custom_component_obj->getAiCenters();

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

        $actions = array(
            array(
                'fld' => 'change_request_verify_request',
                'icon' => '<i class="material-icons" title="Click here to Verify.">check</i>',
                'fld_url' => '../Change_Request_payments/change_request_verify_request/#enrollment#'
            ),
        );

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "",
                'fld' => 'action'
            );
        }

        /* if ($request->all() ){
             // dd($request->all());
             $inputs = $request->all();
             foreach($inputs as $k => $v){
                 if($k != 'page' && $v != "" ){
                     foreach($filters as $ik => $iv){
                         if(!empty($iv['dbtbl']) && $iv['fld'] == $k){
                             $conditions[ $iv['dbtbl'] . "." . $k] = $v;
                         }else{
                             $conditions[$k] = $v;
                         }
                         break;
                     }
                 }
             }
         } */

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

        $master = $custom_component_obj->getchangerequestaPaymentIssuesData($formId);

        return view('change_request_payment.change_request_listing_payment_issues', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function change_request_admission_fee_payment(Request $request)
    {
        $searchBoxClass = "show";
        $student = array();
        $page_title = 'Change Request Application Form Fee Payment';
        $model = "Student";
        $mobile = null;
        if (count($request->all()) > 0) {


            $isValid = true;
            $data = $request->all();
            $modelObj = new Student;
            $validator = Validator::make($request->all(), $modelObj->forPaymentFindStudentBaseRule);
            $fieldAadhar = 'aadhar_number';
            $fieldJan = 'jan_aadhar_number';
            if ($validator->fails()) {
                $isValid = false;
            } else {
                if ($data[$fieldAadhar] == '' && $data[$fieldJan] == '') {
                    $validator = Validator::make($request->all(), $modelObj->forPaymentFindAadharStudentRule);
                }
                if ($validator->fails()) {
                    $isValid = false;
                }
            }

            if (!$isValid) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            $student = array();
            $fld = "dob";
            if (isset($data[$fld]) && !empty($data[$fld])) {
                $$fld = $data[$fld];
            }

            $dobs = null;
            $$fld = $data[$fld];
            $dobArr = explode("/", $dob);
            if (isset($dobArr[0]) && isset($dobArr[1]) && isset($dobArr[2])) {
                $dobs = $dobArr[2] . "-" . $dobArr[1] . "-" . $dobArr[0] . "";
            }


            $fld = "mobile";
            if (isset($data[$fld]) && !empty($data[$fld])) {
                $$fld = $data[$fld];
            }

            $fld = "aadhar_number";
            if (isset($data[$fld]) && !empty($data[$fld])) {
                $$fld = $data[$fld];
            }


            $academicyear_id = config("global.form_admission_academicyear_id");
            $exam_month = Config::get('global.form_current_exam_month_id');

            $form_current_allowed_exam_month_id = Config::get('global.form_current_allowed_exam_month_id');
            $form_current_allowed_exam_month_id = array(1 => 1, 2 => 2);

            $isLockedCount = Student::Join('applications', 'applications.student_id', '=', 'students.id')
                ->where('students.mobile', @$mobile)
                ->where('applications.locksumbitted', 1)
                ->where('students.exam_year', @$academicyear_id)
                ->whereIn('students.exam_month', @$form_current_allowed_exam_month_id)
                ->where('students.dob', @$dobs)
                ->count();
            if ($isLockedCount >= 1) {

            } else {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! Your admission from yet not locked and submitted.');
            }

            $fields = array('students.id', 'applications.locksumbitted', 'applications.locksubmitted_date', 'student_fees.total');
            if (@$data[$fld]) {
                $student = Student::Join('applications', 'applications.student_id', '=', 'students.id')
                    ->Join('student_fees', 'student_fees.student_id', '=', 'students.id')
                    ->where('applications.aadhar_number', $$fld)
                    ->where('students.mobile', $mobile)
                    ->where('students.exam_year', $academicyear_id)
                    ->whereIn('students.exam_month', $form_current_allowed_exam_month_id)
                    ->where('students.dob', $dobs)
                    ->first($fields);
            }
            $fld = "jan_aadhar_number";
            $$fld = null;
            if (isset($data[$fld]) && !empty($data[$fld])) {
                $$fld = $data[$fld];
            }
            if (isset($data[$fld]) && !empty($data[$fld])) {
                $$fld = $data[$fld];
                $student = Student::Join('applications', 'applications.student_id', '=', 'students.id')
                    ->Join('student_fees', 'student_fees.student_id', '=', 'students.id')
                    ->where('applications.jan_aadhar_number', $$fld)
                    ->where('students.mobile', $mobile)
                    ->where('students.dob', $dobs)
                    ->where('students.exam_year', $academicyear_id)
                    //->where('students.exam_month',$exam_month)
                    ->whereIn('students.exam_month', $form_current_allowed_exam_month_id)
                    ->first($fields);
                //dd($student);
            }


            if (!@$student->id) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! You entred details not matched with us! Please try again');
            }
            $student_id = $student->id;


            if (@$student->locksumbitted && @$student->locksubmitted_date) {
            } else {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! Your admission form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay fees!');
            }
            $application_fee = 0;
            if ($student->total) {
                $application_fee = $student->total;
            }

            if ($application_fee <= 0) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! Your admission fees is zero(0) hence does not need to pay admission fees!');
            }
            return redirect()->route('change_request_registration_fee', Crypt::encrypt($student->id))->with('message', 'Student has been found successfully!');
        }
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        return view('change_request_payment.change_request_admission_fee_payment', compact('page_title', 'searchBoxClass', 'student', 'model', 'gender_id'));
    }

    public function change_request_registration_fee(Request $request, $student_id)
    {
        $table = $model = "Student";
        $page_title = 'Change Request Make Payment For Application Form';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();

        $student = array();
        $page_title = ' Change Request Application Make Fee Payment';
        $model = "Student";
        $fld = "id";
        @$master = $this->getStudentDetails($student_id);
        @$makepaymentchangerequerts = $this->changerequestcheckfees($student_id);
        if (@$makepaymentchangerequerts == true) {
            @$changemakepayment = $this->changerequestcheckfeesdifference($student_id);
        }

        $studentdata = $student = Student::with('application', 'studentfee')
            ->where('students.id', $student_id)
            ->first();

        $custom_component_obj = new CustomComponent;
        $isStudent = $custom_component_obj->_getIsStudentLogin();
        if (!@$student->id) {
            if (isset($validator)) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! You entred details not matched with us! Please try again');
            } else {
                return redirect()->back()->with('error', 'Failed! You entred details not matched with us! Please try again');
            }
        }

        if (@$student->application->locksumbitted && @$student->application->locksubmitted_date) {
        } else {
            return redirect()->route('change_request_admission_fee_payment')->with('error', 'Failed! Your admission form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay fees!');
        }


        //$checkAllowToUpdateFinalLockOrPaymentStatus = $this->checkAllowToUpdateFinalLockOrPayment($studentdata->exam_year,$studentdata->exam_month);			
        //if(@$checkAllowToUpdateFinalLockOrPaymentStatus){}else{
        //return redirect()->back()->with('error', 'Failed! Registration date has been closed for last year student!');
        //}


        // Check Current date for make payment
        // @dd($master);
        //$feePaymentAllowOrNot = $this->_checkPaymentAllowedOrNot($student['stream']);
        // $feePaymentAllowOrNot = $this->_checkPaymentAllowedOrNot($student['stream'],$student['gender_id']);
        //$feePaymentAllowOrNot = json_decode($feePaymentAllowOrNot);
        //$feePaymentAllowOrNotStatus = $feePaymentAllowOrNot->status;
        // @dd($feePaymentAllowOrNotStatus);
        // Check Current date for make payment


        if (@$student->application->change_request_fee_status == 1 && $student->update_change_requests_submitted != null && $student->update_change_requests_challan_tid != null) {

        } else {
            /* Pay fee end */
        }

        @$application_fee = @$changemakepayment;
        @$changerequeststudent = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
        $isAlreadyRaisedRequest = ChangeRequestErequest::where('student_id', $student_id)->where('student_change_request_id', $changerequeststudent->id)
            ->whereIn('status', [0, 2])
            ->count();


        $erequestCount = ChangeRequestErequest::where('student_id', $student_id)->where('student_change_request_id', $changerequeststudent->id)
            ->where('rtype', 1)
            ->whereIn('status', [1, 7])
            ->count();


        $issueCount = ChangeRequestPaymentIssue::where('student_id', $student_id)->where('student_change_request_id', @$changerequeststudent->id)->count();
        $paymentIssueDetails = array();
        if ($issueCount > 0) {
            $paymentIssueDetails = ChangeRequestPaymentIssue::where('student_id', $student_id)->where('student_change_request_id', @$changerequeststudent->id)->get();
        }

        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $eenrollment = $enrollment = null;
        if (count($request->all()) > 0) {
            if ($erequestCount > 0) {
                $this->before_payment_verify_request($student_id);
            }
            $EmitraConfigEnvironment = config("global.Emitra_environment");
            $SERVICEID = config("global.Emitra_adm_only_payment_service");
            $payment_user_id = null;
            $custom_component_obj = new CustomComponent;
            $isStudent = $custom_component_obj->_getIsStudentLogin();
            if (@$isStudent && @Auth::guard('student')->user()->id) {
                $payment_user_id = Auth::guard('student')->user()->id;
            } else if (@Auth::user()->id) {
                $payment_user_id = Auth::user()->id;
            } else {
                $payment_user_id = $student_id;
            }
            $academicyear_id = config("global.form_admission_academicyear_id");
            @$changerequeststudent = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
            $erequestToBeSave = ['service_id' => $SERVICEID, 'payment_user_id' => $payment_user_id, 'student_id' => $student_id, 'emitra_id' => null, 'AMOUNT' => $application_fee, 'rtype' => 1, 'status' => 0, 'student_change_request_id' => $changerequeststudent->id];

            $ErequestSaved = ChangeRequestErequest::create($erequestToBeSave);


            $erequest_id = $ErequestSaved->id;
            $REQUESTID = $academicyear_id . '' . 'C' . $student_id . 'C' . $erequest_id . 'C' . $changerequeststudent->id;


            $students = array(839442);//tempstudents
            if (in_array($student_id, $students)) {
                $CONSUMERKEY = 'RSOS-C' . $academicyear_id . '-VER-' . $student_id . 'CGR' . $changerequeststudent->id;
            } else {
                $CONSUMERKEY = 'RSOS-C' . $academicyear_id . '-VER-' . $student_id . 'CGR' . $changerequeststudent->id;
            }
            if (strlen($student->name) <= 3) {
                $USERNAME = $student->name . " ForPayment";
            } else {
                if (strlen($student->name) > 49) {
                    $USERNAME = substr($student->name, 0, 50);
                } else {
                    $USERNAME = $student->name;
                }
            }
            $USERNAME = trim($USERNAME);
            if ($student->mobile == "") {
                $student->mobile = "9999919241";
            }
            // $student->mobile = "9999919241";
            $UDF2 = $USERMOBILE = $student->mobile;
            $fld = 'email';
            if ($student->$fld == "") {
                $USEREMAIL = 'engrohitjain5@gmail.com';
            } else {
                $USEREMAIL = $student->$fld;
            }
            $USEREMAIL = 'engrohitjain5@gmail.com';

            $SUCCESSURL = $FAILUREURL = route('change_request_response'); //payment_response.php
            $fld = "REVENUEHEAD1";
            $REVENUEHEADS = config("global.EmitraService_" . $SERVICEID . "_" . $fld);
            $vars = array('{application_fee}');
            $REVENUEHEAD = str_replace($vars, array($application_fee), $REVENUEHEADS);
            $fld = "MERCHANTCODE";
            $$fld = config("global.EmitraService_" . $SERVICEID . "_" . $fld);
            $AMOUNT = $application_fee;
            $fld = "OFFICECODE";
            $$fld = config("global.EmitraService_" . $SERVICEID . "_" . $fld);
            $fld = "COMMTYPE";
            $$fld = config("global.EmitraService_" . $SERVICEID . "_" . $fld);
            $fld = "CHECKSUMKEY";
            $$fld = config("global.EmitraService_" . $SERVICEID . "_" . $fld);
            $fld = "ENCKEY";
            $$fld = config("global.EmitraService_" . $SERVICEID . "_" . $fld);

            $REQTIMESTAMP = date('YmdHis') . '000';


            // echo "CHECKSUMKEY : ".$CHECKSUMKEY . "<br>";
            // echo "AMOUNT : ".$AMOUNT . "<br>";
            // echo "REQUESTID : ".$REQUESTID . "<br>";

            $CHECKSUM = md5($REQUESTID . "|" . $AMOUNT . "|" . $CHECKSUMKEY);


            //$UDF1 = $student_id.'C'.$changerequeststudent->id;
            $UDF1 = $changerequeststudent->id;

            $CHANNEL = 'ONLINE';
            $LOOKUPID = $erequest_id;
            $fld = "URL";
            $$fld = config("global.Emitra_" . $EmitraConfigEnvironment . "_" . $fld);
            $fields = '{
                "MERCHANTCODE": "' . $MERCHANTCODE . '",
                "PRN": "' . $REQUESTID . '",
                "REQTIMESTAMP": "' . $REQTIMESTAMP . '",
                "AMOUNT": "' . $AMOUNT . '",
                "SUCCESSURL": "' . $SUCCESSURL . '",
                "FAILUREURL": "' . $FAILUREURL . '",
                "USERNAME": "' . $USERNAME . '",
                "USERMOBILE": "' . $USERMOBILE . '",
                "USEREMAIL": "' . $USEREMAIL . '",
                "UDF1": "' . $UDF1 . '",
                "UDF2": "' . $CONSUMERKEY . '",
                "SERVICEID": "' . $SERVICEID . '",
                "OFFICECODE": "' . $OFFICECODE . '",
                "REVENUEHEAD": "' . $REVENUEHEAD . '",
                "COMMTYPE": "' . $COMMTYPE . '",
                "CHECKSUM":"' . $CHECKSUM . '"
            }';

            // dd($fields);
            //Log::info('Online Payment Request:' . $fields);


            // $fld = "MERCHANTCODE"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "SERVICEID"; echo  $fld . " :" ; echo $$fld;echo "<br>";
            // $fld = "REQUESTID"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "REQUESTID"; echo  "PRN " . " :" . $$fld;echo "<br>";
            // $fld = "CHANNEL"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "REQTIMESTAMP"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "AMOUNT"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "SUCCESSURL"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "FAILUREURL"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "USERNAME"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "USERMOBILE"; echo  $fld . " :" ; echo $$fld;echo "<br>";
            // $fld = "USEREMAIL"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "CONSUMERKEY"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "OFFICECODE"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "REVENUEHEAD"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "UDF1"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "UDF2"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "LOOKUPID"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "COMMTYPE"; echo  $fld . " :" . $$fld;echo "<br>";
            // $fld = "CHECKSUMKEY"; echo  $fld . " :" . $$fld;echo "<br>"; 
            // echo "<br>";

            $CHECKSUMSTR = $MERCHANTCODE . $SERVICEID . $REQUESTID . $CHANNEL . $REQTIMESTAMP . $AMOUNT . $SUCCESSURL . $FAILUREURL . $USERNAME . $USERMOBILE . $USEREMAIL . $CONSUMERKEY . $OFFICECODE . $REVENUEHEAD . $UDF1 . $UDF2 . $LOOKUPID . $COMMTYPE . $CHECKSUMKEY;

            // echo "CHECKSUMSTR : "; echo $CHECKSUMSTR;echo "<br>";


            //echo "ENCKEY : " . $ENCKEY . "<br>";

            $CHECKSUM = hash('sha256', $CHECKSUMSTR);
            // echo "CHECKSUM : ". $CHECKSUM;echo "<br>";
            $plainData = 'PRN=' . $REQUESTID . '::CHANNEL=' . $CHANNEL . '::REQTIMESTAMP=' . $REQTIMESTAMP . '::AMOUNT=' . $AMOUNT . '::SUCCESSURL=' . $SUCCESSURL . '::FAILUREURL=' . $FAILUREURL . '::USERNAME=' . $USERNAME . '::USERMOBILE=' . $USERMOBILE . '::USEREMAIL=' . $USEREMAIL . '::CONSUMERKEY=' . $CONSUMERKEY . '::OFFICECODE=' . $OFFICECODE . '::REVENUEHEAD=' . $REVENUEHEAD . '::UDF1=' . $UDF1 . '::UDF2=' . $UDF2 . '::LOOKUPID=' . $LOOKUPID . '::COMMTYPE=' . $COMMTYPE . '::CHECKSUM=' . $CHECKSUM;
            $encryptedStr = $this->_paymentencrypt($plainData, $ENCKEY);
            $form = '<form action="' . $URL . '" method="POST"   id="myForm1" style="display:none;">
            @csrf
            <input type="hidden" name="MERCHANTCODE" value="' . $MERCHANTCODE . '">
            <input type="hidden" name="SERVICEID" value="' . $SERVICEID . '">
            <input type="hidden" name="ENCDATA" value="' . $encryptedStr . '"><script type="text/javascript">document.getElementById("myForm1").submit();</script>
            </form>';
            echo $form;


            /*echo "encryptedStr : " ; echo $encryptedStr; echo "<br>";
            echo "plainData : "; echo $plainData;echo "<br>";
            print_r($URL);
            echo "<br>";
            echo "CHECKSUM : ". $CHECKSUM;echo "<br>";
            echo "ENCKEY : " . $ENCKEY . "<br>";
            print_r($MERCHANTCODE);
            echo "<br>";
            print_r($SERVICEID);
            echo "<br>";
            print_r($fields); 
            $finalParms = $this->_paymentdecrypt($encryptedStr,$ENCKEY);
            dd($finalParms);*/
            exit;
        }


        return view('change_request_payment.change_request_registration_fee', compact('changemakepayment', 'checkchangerequestsAllowOrNotAllow', 'isStudent', 'stream_id', 'estudent_id', 'eenrollment', 'adm_types', 'course', 'enrollment', 'page_title', 'student', 'isAlreadyRaisedRequest', 'erequestCount', 'issueCount', 'categorya', 'paymentIssueDetails', 'student', 'model', 'gender_id', 'application_fee'));
    }

    public function before_payment_verify_request($student_id)
    {

        $student = Student::with('application')
            ->where('students.id', $student_id)
            ->first();
        if (@$student->id) {
        } else {
            return redirect()->back()->with('error', 'Failed! You entred details not matched with us! Please try again');
        }
        $student_id = $student->id;
        $challan_tid = Student::with('application')
            ->where('students.id', $student_id)
            ->where('students.update_change_requests_challan_tid', "!= ", "")
            ->count();

        if (@$student->application->locksumbitted && @$student->application->locksubmitted_date) {
        } else {
            return redirect()->route('change_request_admission_fee_payment')->with('error', 'Failed! Your admission form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay fees!');
        }

        if ($challan_tid != 0) {
            return redirect()->route('change_request_registration_fee', Crypt::encrypt($student->id))->with('error', 'Failed! Invalid request access!challan number already present with this request.');
        }
        @$changerequeststudent = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
        $Erequests = ChangeRequestErequest::where('student_id', $student_id)->where('student_change_request_id', $changerequeststudent->id)
            ->whereIn('rtype', [1, 2])
            ->whereIn('status', [1, 7])
            ->get();

        if (@$Erequests) {
            return redirect()->route('change_request_registration_fee', Crypt::encrypt($student->id))->with('error', 'Failed! Invalid request access!challan number already present with this request.');
        } else {
            foreach ($Erequests as $Erequest) {
                $found = $this->_verifyByErequestId($Erequest->id);
            }
            if ($found == false) {
                if ($found == false) {
                    if (Auth::check()) {
                        $user_id = Auth::user()->id;
                        $role_id = Session::get('role_id');
                        return redirect()->route('change_request_listing_payment_issues');
                    }
                    return redirect()->route('change_request_registration_fee', Crypt::encrypt($student->id))->with('info', 'Your Transaction has not been verified yet or there is no transaction found to verify!');
                }
                return redirect()->route('change_request_registration_fee', Crypt::encrypt($student->id));
            }
            if (Auth::check()) {
                $user_id = Auth::user()->id;
                $role_id = Session::get('role_id');
                return redirect()->route('change_request_listing_payment_issues');
            }
            $this->redirect(array('action' => 'change_request_registration_fee', $id))->with('success', 'Your Transaction has been made successfully!');
        }
    }

    public function _verifyByErequestId($id)
    {
        $found = false;
        $Erequest = ChangeRequestErequest::where('id', $id)->first();
        if (!empty($Erequest)) {

            $fld = "student_id";
            $$fld = $Erequest->$fld;

            $fld = "id";
            $$fld = $Erequest->$fld;
            $fld = "amount";
            $AMOUNT = $application_fee = $$fld = $Erequest->$fld;


            if ($application_fee == null) {
                $studentdd = $this->changerequestcheckfeesdifference($Erequest->student_id);
                $application_fee = 0;
                if (isset($studentdd)) {
                    $application_fee = $studentdd;
                }
            }

            $academicyear_id = config("global.form_admission_academicyear_id");
            $student_details = Student::where('students.id', $student_id)->first();

            $EmitraConfigEnvironment = config("global.Emitra_environment");
            $SERVICEID = config("global.Emitra_adm_only_payment_service");

            $fld = "REVENUEHEAD1";
            $REVENUEHEADS = config("global.EmitraService_" . $SERVICEID . "_" . $fld);

            $AMOUNT = $application_fee;
            $vars = array('{application_fee}');
            $REVENUEHEAD = str_replace($vars, array($application_fee), $REVENUEHEADS);
            $fld = "MERCHANTCODE";
            $$fld = config("global.EmitraService_" . $SERVICEID . "_" . $fld);

            $fld = "OFFICECODE";
            $$fld = config("global.EmitraService_" . $SERVICEID . "_" . $fld);
            $fld = "COMMTYPE";
            $$fld = config("global.EmitraService_" . $SERVICEID . "_" . $fld);
            $fld = "CHECKSUMKEY";
            $$fld = config("global.EmitraService_" . $SERVICEID . "_" . $fld);
            $fld = "ENCKEY";
            $$fld = config("global.EmitraService_" . $SERVICEID . "_" . $fld);
            $erequest_id = $id;
            $PRN = $REQUESTID = $academicyear_id . '' . 'C' . $student_id . 'C' . $erequest_id . 'C' . $Erequest->student_change_request_id;

            $fld = "verifyUrl";
            $$fld = config("global.Emitra_" . $EmitraConfigEnvironment . "_" . $fld);

            /* For old payment amount clearing start */

            $tempStudents = array();
            $tempStudents = array(856702, 888063);
            if (in_array($student_id, $tempStudents)) {
                //dd($tempStudents);
                //$AMOUNT = $AMOUNT - 250;
            }


            /* For old payment amount clearing end */

            $encrypted_fields = array("MERCHANTCODE" => "$MERCHANTCODE", "SERVICEID" => "$SERVICEID", "PRN" => "$PRN", "AMOUNT" => "$AMOUNT");

            $encryption_key = $ENCKEY;


            $res1 = $this->_invokePostAPINewPg($verifyUrl, json_encode($encrypted_fields));
            //dd($res1);

            if (in_array($student_id, $tempStudents)) {
                //dd($res1);
                //$AMOUNT = $AMOUNT - 250;
            }

            if ($res1['status'] == 1) {
                $output = json_decode($res1['json_output'], true);
                if (isset($output['data']['STATUS'])) {
                    $json_output = $output['data'];
                    $encryptedStr = $json_output['ENCDATA'];
                    $string = $this->_paymentdecrypt($encryptedStr, $encryption_key);
                    $response = $this->_paymentgetresponse($string);
                    $fields = $response;
                    if ($json_output['STATUS'] == 'SUCCESS') {
                        if (@$response['RECEIPTNO']) {
                            if (empty($student_details->update_change_requests_challan_tid)) {
                                $updateErequest = ChangeRequestErequest::find($erequest_id);
                                $updateErequest->response = $ErequestSave['response'] = $string;
                                $updateErequest->status = $ErequestSave['status'] = 1;
                                $updateErequest->prn = $ErequestSave['prn'] = $PRN;
                                $updateErequest->challan_tid = $ErequestSave['challan_tid'] = $fields['RECEIPTNO'];
                                $updateErequest->transaction_id = $ErequestSave['transaction_id'] = $fields['TRANSACTIONID'];
                                $updateErequest->save();
                                $studentsarrayt = array(
                                    'student_id' => @$updateErequest->student_id,
                                    'exam_year' => @$updateErequest->exam_year,
                                    'exam_month' => @$updateErequest->exam_month,
                                    'student_change_request_id' => @$updateErequest->student_change_request_id,
                                    'challan_tid' => @$updateErequest->challan_tid,
                                    'prn' => $updateErequest->prn,
                                    'amount' => $updateErequest->amount,
                                    'change_request_status' => 'YES',
                                );
                                $studnetarraytrail = ChangeRequestStudentTarils::create($studentsarrayt);

                                $currentDateTime = date('Y-m-d H:i:s');
                                $studentMaster = $updatestudent = Student::find($student_id);
                                $updatestudent->update_change_requests_challan_tid = $fields['RECEIPTNO'];

                                if (isset($fields['RECEIPTNO']) && !empty($fields['RECEIPTNO'])) {
                                    // $updatestudent->is_eligible=1;
                                    //$enrollment = $this->_setEnrollmentAndIsEligiable($student_id);
                                }
                                $updatestudent->update_change_requests_submitted = $currentDateTime;
                                $updatestudent->student_change_requests = NULL;
                                $updatestudent->save();
                                $rsstudentverifications = DB::table('student_verifications')->where('student_id', $student_id)->update(['deleted_at' => date("Y-m-d H:i:s")]);
                                $applicationdata = Application::where('student_id', $student_id)->first();
                                $updateApplication = Application::find($applicationdata->id);
                                $updateApplication->change_request_status = 1;
                                $updateApplication->is_ready_for_verifying = 1;
                                $updateApplication->change_request_fee_status = 1;
                                $updateApplication->change_request_fee_paid_amount = $fields['AMOUNT'];
                                $updateApplication->save();

                                /* Enrolment Change When Change Request By Student Start */
                                $is_change_enrollment = $studentMaster->is_change_enrollment;
                                if ($is_change_enrollment) {
                                    $changeReqStatus = $this->_settingChangeReqEnrollment($student_id, $is_change_enrollment);
                                }
                                /* Enrolment Change When Change Request By Student End */

                                @$changerequeststudent = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
                                $paymentIssueUpdate = ChangeRequestPaymentIssue::where('student_id', $student_id)->where('student_change_request_id', $changerequeststudent->id)->first();
                                if (@$paymentIssueUpdate) {
                                    $paymentIssueUpdate->status = 1;
                                    $paymentIssueUpdate->is_archived = 1;
                                    $paymentIssueUpdate->save();
                                }
                                if (@$student_details) {
                                    //$this->_sendPaymentSubmitMessage($student_id);
                                }
                                $found = true;
                            } else {
                                $found = true;
                            }
                        } else {
                            $updateErequest = ChangeRequestErequest::find($erequest_id);
                            $updateErequest->response = $ErequestSave['response'] = $string;
                            $updateErequest->status = $ErequestSave['status'] = 2;
                            $updateErequest->save();
                        }
                    }
                }
            }
        }
        return $found;
    }

    public function _invokePostAPINewPg($url, $fields)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => array(
                'X-Api-Name: PAYMENT_STATUS',
                'Content-Type: application/json'
            ),
        ));

        $result = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);


        if ($err) {
            $response['status'] = 0;
            $response['message'] = "cURL Error #:" . $err;;
        } else {
            $response['status'] = 1;
            $response['json_output'] = $result;
        }
        return $response;
    }

    public function _invokePostAPI($url, $fields)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => array(
                'X-Api-Name: PAYMENT_STATUS',
                'Content-Type: application/json'
            ),
        ));

        $result = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);


        if ($err) {
            $response['status'] = 0;
            $response['message'] = "cURL Error #:" . $err;;
        } else {
            $response['status'] = 1;
            $response['json_output'] = $result;
        }
        return $response;

    }

    public function change_request_response(Request $request)
    {
        $responoseData = $request->all();
        $enrollment = null;
        // dd($responoseData);
        if (isset($responoseData['STATUS'])) {
            $STATUS = $responoseData['STATUS'];
            $textToDecrypt = $responoseData['ENCDATA'];
            // Log::info('Online Payment STATUS :' . $STATUS);
            // Log::info('Online Payment encrypted_data Response :' . $textToDecrypt);

            $academicyear_id = config("global.form_admission_academicyear_id");
            $EmitraConfigEnvironment = config("global.Emitra_environment");
            $SERVICEID = config("global.Emitra_adm_only_payment_service");

            //Log::info('Online Payment STATUS :' . $STATUS);
            $fld = "ENCKEY";
            $encryption_key = $$fld = config("global.EmitraService_" . $SERVICEID . "_" . $fld);

            $encryptedStr = $responoseData['ENCDATA'];
            $decrypted_data = $this->_paymentdecrypt($encryptedStr, $encryption_key);
            $fields = $this->_paymentgetresponse($decrypted_data);

            // Log::info('Online Payment Post Data :' . json_encode($responoseData));
            // Log::info('Online Payment decrypted_data Response :' . $decrypted_data);
            $getdatastudent = ChangeRequestErequest::where('student_change_request_id', $fields['UDF1'])->orderBy('id', 'desc')->first();
            $studentMaster = Student::with('application')
                ->where('students.id', @$getdatastudent->student_id)
                ->first();

            $fld = "enrollment";
            if (@$studentMaster->$fld) {
                $$fld = $studentMaster->$fld;
            }
            if ($responoseData['STATUS'] == 'SUCCESS') {
                if ($STATUS == 'SUCCESS' || $STATUS == 'PENDING') {
                    $masterDetails = explode("C", $fields['PRN']);
                    $student_idget = $masterDetails[1];
                    $REQUESTID = $transaction_id = $erequest_id = $masterDetails[1];
                    $SERVICEID = config("global.Emitra_adm_only_payment_service");
                    $Erequest = ChangeRequestErequest::where('student_change_request_id', $fields['UDF1'])->orderBy('id', 'desc')->first();
                    if (@$Erequest) {
                        if ($STATUS == 'SUCCESS') {
                            $updateErequest = ChangeRequestErequest::where('student_change_request_id', $fields['UDF1'])->orderBy('id', 'desc')->first();
                            //$updateErequest = ChangeRequestErequest::find($REQUESTID);

                            $updateErequest->student_id = $student_id = $updateErequest->student_id;
                            $updateErequest->amount = $ErequestSave['amount'] = $fields['AMOUNT'];
                            $updateErequest->challan_tid = $ErequestSave['challan_tid'] = $fields['RECEIPTNO'];
                            $updateErequest->service_id = $ErequestSave['service_id'] = $SERVICEID;
                            $updateErequest->transaction_id = $ErequestSave['transaction_id'] = $fields['TRANSACTIONID'];
                            $updateErequest->prn = $ErequestSave['prn'] = $fields['PRN'];
                            $updateErequest->status = $ErequestSave['status'] = 1;
                            $updateErequest->response = $ErequestSave['response'] = $decrypted_data;
                            $updateErequest->ENCDATA = $ErequestSave['ENCDATA'] = $encryptedStr;
                            $updateErequest->save();
                            @$student_id = $updateErequest->student_id;
                            $studentsarrayt = array(
                                'student_id' => @$updateErequest->student_id,
                                'exam_year' => @$updateErequest->exam_year,
                                'exam_month' => @$updateErequest->exam_month,
                                'challan_tid' => @$updateErequest->challan_tid,
                                'student_change_request_id' => @$updateErequest->student_change_request_id,
                                'prn' => $updateErequest->prn,
                                'amount' => $updateErequest->amount,
                                'change_request_status' => 'YES',
                            );
                            $studnetarraytrail = ChangeRequestStudentTarils::create($studentsarrayt);
                            $updatestudent = Student::where('id', @$updateErequest->student_id)->first();
                            $updatestudent->update_change_requests_challan_tid = $fields['RECEIPTNO'];

                            if (isset($fields['RECEIPTNO']) && !empty($fields['RECEIPTNO'])) {
                                // $updatestudent->is_eligible=1;
                                //$enrollment = $this->_setEnrollmentAndIsEligiable($student_id);
                            }
                            $currentDateTime = date('Y-m-d H:i:s');
                            $updatestudent->update_change_requests_submitted = $currentDateTime;
                            $updatestudent->student_change_requests = NULL;
                            $updatestudent->save();
                            $rsstudentverifications = DB::table('student_verifications')->where('student_id', $student_id)->update(['deleted_at' => date("Y-m-d H:i:s")]);
                            $applicationdata = Application::where('student_id', $student_id)->first();

                            $updateApplication = Application::find($applicationdata->id);
                            $updateApplication->change_request_status = 1;
                            $updateApplication->change_request_fee_status = 1;
                            $updateApplication->is_ready_for_verifying = 1;
                            $updateApplication->change_request_fee_paid_amount = $fields['AMOUNT'];
                            $updateApplication->save();
                            @$changerequeststudent = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
                            $paymentIssueUpdate = ChangeRequestPaymentIssue::where('student_id', $student_id)->where('student_change_request_id', $changerequeststudent->id)->first();
                            if (@$paymentIssueUpdate) {
                                $paymentIssueUpdate->status = 1;
                                $paymentIssueUpdate->is_archived = 1;
                                $updateApplication->save();
                            }

                            /* Enrolment Change When Change Request By Student Start */
                            $is_change_enrollment = $studentMaster->is_change_enrollment;
                            if ($is_change_enrollment) {
                                $changeReqStatus = $this->_settingChangeReqEnrollment($student_id, $is_change_enrollment);
                            }
                            /* Enrolment Change When Change Request By Student End */

                            if (@$student_id) {
                                //$this->_sendPaymentSubmitMessage($student_id);
                            }
                            $role_id = @Session::get('role_id');


                            //2212232
                            $studentRoleId = Config::get("global.student");
                            if ($role_id == $studentRoleId) {
                                $status = $this->reLoginCurrentStudentAfterPayment($student_id);
                                if ($status) {
                                    return redirect()->route('preview_details', Crypt::encrypt($student_id))->with('message', 'Your payment has successfully completed.');
                                } else {
                                    return redirect()->route('change_request_registration_fee', Crypt::encrypt($studentMaster->id))->with('message', 'Your Transaction has been made successfully!');
                                }
                            }
                            return redirect()->route('change_request_registration_fee', Crypt::encrypt($studentMaster->id))->with('message', 'Your Transaction has been made successfully!');
                        } else {
                            $updateErequestElseCase = ChangeRequestErequest::where('student_change_request_id', $fields['UDF1'])->orderBy('id', 'desc')->first();
                            $updateErequestElseCase->response = $ErequestSave['response'] = $decrypted_data;
                            $updateErequest->ENCDATA = $ErequestSave['ENCDATA'] = $encryptedStr;
                            $updateErequestElseCase->status = $ErequestSave['status'] = 2;
                            $updateErequestElseCase->save();

                            $role_id = @Session::get('role_id');
                            $studentRoleId = Config::get("global.student");
                            if ($role_id == $studentRoleId) {
                                $status = $this->reLoginCurrentStudentAfterPayment($student_id);
                                if ($status) {
                                    return redirect()->route('preview_details', Crypt::encrypt($student_id))->with('error', 'Your Transaction is pending from bank!');
                                } else {
                                    return redirect()->route('change_request_registration_fee', Crypt::encrypt($studentMaster->id))->with('error', 'Your Transaction is pending from bank!');
                                }
                            }
                            return redirect()->route('change_request_registration_fee', Crypt::encrypt($studentMaster->id))->with('error', 'Your Transaction is pending from bank!');
                        }
                    }
                } else {
                    return redirect()->route('change_request_registration_fee', Crypt::encrypt($studentMaster->id))->with('error', $fields['RESPONSEMESSAGE']);
                }
            } else {
                $msg = (isset($fields['RESPONSEMESSAGE'])) ? $fields['RESPONSEMESSAGE'] : 'There was some error, Please try again!';
                return redirect()->route('change_request_registration_fee', Crypt::encrypt($studentMaster->id))->with('error', $msg);
            }
        } else {
            $updateErequestElseCase = ChangeRequestErequest::where('student_change_request_id', $fields['UDF1'])->orderBy('id', 'desc')->first();
            $updateErequestElseCase->response = $ErequestSave['response'] = $decrypted_data;
            $updateErequestElseCase->status = $ErequestSave['status'] = 2;
            $updateErequestElseCase->save();
            return redirect()->route('change_request_registration_fee', Crypt::encrypt($studentMaster->id))->with('info', 'Your Transaction is pending from bank!');
        }
        return redirect()->route('change_request_registration_fee', Crypt::encrypt($studentMaster->id));
    }

    public function change_request_verify_request(Request $request, $student_id)
    {
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $student = Student::with('application')
            ->where('students.id', $student_id)
            ->first();

        if (!@$student->id) {
            return redirect()->back()->with('error', 'Failed! You entred details not matched with us! Please try again');
        }

        $student_id = $student->id;
        $challan_tid = Student::with('application')
            ->where('students.id', $student_id)
            ->where('students.update_change_requests_challan_tid', "!=", null)
            ->count();


        if (@$student->application->locksumbitted && @$student->application->locksubmitted_date) {
        } else {
            return redirect()->route('change_request_admission_fee_payment')->with('error', 'Failed! Your admission form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay fees!');
        }

        if ($challan_tid > 0) {
            return redirect()->route('change_request_registration_fee', Crypt::encrypt($student->id))->with('error', 'Failed! Invalid request access! Challan number already present with this request.');
        }
        @$changerequeststudent = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
        $Erequests = ChangeRequestErequest::where('student_id', $student_id)->where('student_change_request_id', @$changerequeststudent->id)
            // ->whereIn('rtype', [1,2])
            // ->whereIn('status', [1,7])
            ->whereIn('status', [0, 2])
            ->get();

        if (empty($Erequests)) {
            return redirect()->route('change_request_registration_fee', Crypt::encrypt($student->id))->with('error', 'Failed! Invalid request access! Still not found any request with your payment request.');
        } else {
            $found = false;
            foreach ($Erequests as $Erequest) {
                if (!$found) {
                    $found = $this->_verifyByErequestId($Erequest->id);
                }
            }

            if ($found == false) {
                if ($found == false) {
                    if (Auth::check()) {
                        $user_id = Auth::user()->id;
                        $role_id = Session::get('role_id');
                        if (!empty($user_id)) {
                            return redirect()->route('change_request_listing_payment_issues')->with('error', 'Amount not verified!');
                        }
                    }
                    return redirect()->route('change_request_registration_fee', Crypt::encrypt($student->id))->with('info', 'Your Transaction has not been verified yet or there is no transaction found to verify!');
                }
                return redirect()->route('change_request_registration_fee', Crypt::encrypt($student->id));
            }
            if (Auth::check()) {
                $user_id = Auth::user()->id;
                $role_id = Session::get('role_id');
                if (!empty($user_id)) {
                    return redirect()->route('change_request_listing_payment_issues')->with('success', 'Your Transaction has been made successfully!');
                }
            }
            return redirect()->route('change_request_registration_fee', Crypt::encrypt($student->id))->with('success', 'Your Transaction has been made successfully!');
        }
    }

    public function change_request_raise_request(Request $request, $student_id)
    {
        $student_id = Crypt::decrypt($student_id);
        $student = Student::where('id', $student_id)->first();
        @$changerequeststudent = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
        $student_id = $student->id;
        $isAlreadyRaisedRequest = ChangeRequestPaymentIssue::where('student_id', $student_id)->where('student_change_request_id', $changerequeststudent->id)->count();

        if ($isAlreadyRaisedRequest > 0) {
            return redirect()->route('change_request_registration_fee', Crypt::encrypt($student_id))->with('error', 'Request has been already made by you.');
        }
        $Countchallan_tid = Student::where('id', $student_id)->where('update_change_requests_challan_tid', "!=", null)->count();

        if (@$Countchallan_tid) {
            return redirect()->route('change_request_registration_fee', Crypt::encrypt($student_id))->with('error', 'Your challan number already present with us!');
        }

        $Erequests = ChangeRequestErequest::
        where('student_id', $student_id)->where('student_change_request_id', $changerequeststudent->id)
            ->whereIn('rtype', [1, 2])
            ->whereNotIn('status', [1, 7])
            ->get();
        if (empty($Erequests)) {
            return redirect()->route('change_request_registration_fee', Crypt::encrypt($student_id))->with('error', 'Invalid request access!');
        } else {
            @$changerequeststudent = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
            $paymentIssueToBeSave = ['student_id' => $student_id, 'enrollment' => $student_id, 'is_archived' => 0, 'status' => 0, 'student_change_request_id' => $changerequeststudent->id];
            ChangeRequestPaymentIssue::create($paymentIssueToBeSave);
            return redirect()->route('change_request_registration_fee', Crypt::encrypt($student_id))->with('message', 'Your request to admin for Transaction has been made successfully!');
        }
    }

    public function sendSMSMessageForFeePaid(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", -1);
        $students = Application::
        Join('students', 'applications.student_id', '=', 'students.id')
            // ->where('mobile',"=",'9999919241')
            ->where('locksumbitted', "=", 1)
            ->where('mobile', "!=", NULL)
            ->where('students.enrollment', "!=", NULL)
            ->where('students.submitted', "=", NULL)
            ->where('applications.fee_paid_amount', "=", NULL)
            ->pluck('student_id');

        foreach ($students as $k => $student_id) {
            // $this->_sendLockSubmittedMessage($student_id);
            echo ($k + 1) . " -" . $student_id . "-<br>";
        }
        echo "Message sent to " . count($students) . " students.";
        die;
    }


}