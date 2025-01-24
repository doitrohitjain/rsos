<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Models\DocumentVerification;
use App\Models\FsdvPaymentIssue;
use App\Models\MasterStudentVerificationFee;
use App\Models\Registration;
use App\Models\Student;
use App\Models\StudentDocumentVerification;
use App\Models\StudentFormVerificationErequest;
use App\Models\StudentVerificationSubject;
use App\Models\SuppStudentFee;
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

class FreshStudentDocumentVeriyPaymentController extends Controller
{
    private $request;

    public function __construct(request $request)
    {
        $this->request = $request;
    }

    public function fsdv_registration_fee(Request $request, $enrollment)
    {
        $custom_component_obj = new CustomComponent;
        //$formOpenAllowOrNot = $custom_component_obj->checkAnySuppEntryAllowOrNot();

        $role_id = @Session::get('role_id');
        if (@$role_id) {
        } else {
            $errMsg = 'आगे बढ़ने के लिए कृपया दोबारा लॉग इन करें।(Kindly log in again to proceed.)';
            return redirect()->route('landing')->with('error', $errMsg);
        }

        // pl. use dept_rejected_payment_document_verification_start_date for verification
        $isAllowForFsdvApplicaitonForm = $this->_checkIsAllowStudentForFSDVApplicationForm($request);

        $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
        $current_exam_month_id = Config::get("global.form_current_exam_month_id");

        $errMsg = null;
        if (!$isAllowForFsdvApplicaitonForm) {
            $errMsg = 'भुगतान स्पष्टिकरण के लिए प्रस्तुति की अंतिम तिथि अब समाप्त हो गई है।(The Payment of Clarifiation submission date is closed.)';
            $ip = NULL;
            if (!empty(@$_SERVER['HTTP_CLIENT_IP'])) {
                $ip = @$_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty(@$_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                if (@$$_SERVER['REMOTE_ADDR']) {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
            }
            if ($ip == "10.68.181.236") {

            } else {
                return redirect()->route('landing')->with('error', $errMsg);
            }
        }

        $table = $model = "Student";
        $page_title = 'आवेदन स्पष्टीकरण के लिए भुगतान जमा करें।(Submit Payment for Application Clarification)';
        $eenrollment = $enrollment;
        $enrollment = Crypt::decrypt($enrollment);

        $student = array();
        $model = "Student";
        $fld = "enrollment";
        $custom_component_obj = new CustomComponent;

        $studentDetails = Student::where('students.enrollment', $enrollment)
            ->where('students.exam_year', $current_admission_session_id)
            ->where('students.exam_month', $current_exam_month_id)
            ->first('id');
        $student_id = @$studentDetails->id;

        $fields = array("student_verifications.*", "students.name", "students.dob", "students.enrollment", "students.name", "students.father_name", "students.course", "students.stream", "students.department_status", "students.verifier_status");
        $student = DocumentVerification::join('students', 'students.id', '=', 'student_verifications.student_id')
            ->where('student_id', $student_id)->orderby("student_verifications.id", "DESC")->first($fields);
        $student_verification_id = @$student->id;


        if (@$student_verification_id) {
        } else {
            return redirect()->route('landing')->with('error', 'Failed! Something is wrong Err : 3001');
        }
        $fldNameAliase = "aicenter_";
        if ($student->role_id == Config::get('global.super_admin_id')) { //Acedmic Department
            $fldNameAliase = "department_";
        }
        $fldNameAliase = $fldNameAliase . 'status';

        if (@$student->$fldNameAliase != 4) {
            if (@$validator) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! You entred details not matched with us! Please try again');
            }
            return redirect()->route('landing')->with('error', 'Failed! Your clarification form still not submitted. Please first submit clarificatioin form.Hence you are not allowed to pay fees!');
        }
        $estudent_id = Crypt::encrypt($student_id);
        $application_fee = 0;
        if (@$student->amount) {
            $application_fee = $student->amount;
        }

        if ($application_fee <= 0) {
            if (isset($validator)) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! Your clarificatioin fees is zero(0) hence does not need to pay clarificatioin fees!');
            } else {
                return redirect()->route('landing')->with('error', 'Failed! Your clarificatioin fees is zero(0) hence does not need to pay clarificatioin fees!');
            }
        }
        $isAlreadyRaisedRequest = StudentFormVerificationErequest::where('student_id', $student_id)
            ->whereIn('status', [0, 2])
            ->count();


        $erequestCount = StudentFormVerificationErequest::where('student_id', $student_id)
            ->where('rtype', 1)
            ->whereIn('status', [1, 7])
            ->count();

        $issueCount = FsdvPaymentIssue::where('student_id', $student_id)->count();

        $paymentIssueDetails = array();
        if ($issueCount > 0) {
            $paymentIssueDetails = FsdvPaymentIssue::where('student_id', $student_id)->get();
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

        if (count($request->all()) > 0) {
            $inputs = $request->all();

            if ($erequestCount > 0) {
                $this->fsdv_before_payment_verify_request($student_id);
            }


            $EmitraConfigEnvironment = config("global.Emitra_environment");
            $SERVICEID = config("global.Emitra_adm_only_payment_service");


            $erequestToBeSave = ['service_id' => $SERVICEID, 'student_verification_id' => $student_verification_id, 'student_id' => $student_id, 'emitra_id' => null, 'amount' => $application_fee, 'rtype' => 1, 'status' => 0];

            $StudentFormVerificationErequestSaved = StudentFormVerificationErequest::create($erequestToBeSave);

            $academicyear_id = @$current_admission_session_id;
            $fsdv_exam_month = @$current_exam_month_id;
            $erequest_id = $StudentFormVerificationErequestSaved->id;


            //$REQUESTID = $academicyear_id . '' . 'S' . $student_id . 'S' . $erequest_id;
            // $CONSUMERKEY = 'RSOS-' . '-SUPP-'. $academicyear_id . '-SUPP-ROH-' . $student_id;
            $REQUESTID = $academicyear_id . $fsdv_exam_month . 'R' . $erequest_id . 'R' . $student_verification_id . 'R' . $student_id;

            $CONSUMERKEY = 'RSOS-VER-' . $academicyear_id . 'R' . $fsdv_exam_month . 'R' . $student_id . 'R' . $student_verification_id;

            if (strlen($student->name) <= 3) {
                $USERNAME = $student->name . " ForPayment";
            } else {
                $USERNAME = str_replace(array('\'', '"', ',', ';', "'", "`", "@", "#", "$", "!", '0', '<', '>', '.', '(', ")"), ' ', $student->name);
            }
            $USERNAME = trim($USERNAME);
            if ($student->mobile == "") {
                $student->mobile = "9999919241";
            }
            $UDF2 = $USERMOBILE = $student->mobile;
            $fld = 'email';
            if ($student->$fld == "") {
                $USEREMAIL = 'engrohitjain5@gmail.com';
            } else {
                $USEREMAIL = $student->$fld;
            }
            // $USEREMAIL = 'engrohitjain5@gmail.com';

            $SUCCESSURL = $FAILUREURL = route('fsdv_response');
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
            $CHECKSUM = md5($REQUESTID . "|" . $AMOUNT . "|" . $CHECKSUMKEY);


            $UDF1 = $student_id;
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


            //Log::info('Online Reval PaymentRequest:' . $fields);

            $CHECKSUMSTR = $MERCHANTCODE . $SERVICEID . $REQUESTID . $CHANNEL . $REQTIMESTAMP . $AMOUNT . $SUCCESSURL . $FAILUREURL . $USERNAME . $USERMOBILE . $USEREMAIL . $CONSUMERKEY . $OFFICECODE . $REVENUEHEAD . $UDF1 . $UDF2 . $LOOKUPID . $COMMTYPE . $CHECKSUMKEY;


            $CHECKSUM = hash('sha256', $CHECKSUMSTR);
            $plainData = 'PRN=' . $REQUESTID . '::CHANNEL=' . $CHANNEL . '::REQTIMESTAMP=' . $REQTIMESTAMP . '::AMOUNT=' . $AMOUNT . '::SUCCESSURL=' . $SUCCESSURL . '::FAILUREURL=' . $FAILUREURL . '::USERNAME=' . $USERNAME . '::USERMOBILE=' . $USERMOBILE . '::USEREMAIL=' . $USEREMAIL . '::CONSUMERKEY=' . $CONSUMERKEY . '::OFFICECODE=' . $OFFICECODE . '::REVENUEHEAD=' . $REVENUEHEAD . '::UDF1=' . $UDF1 . '::UDF2=' . $UDF2 . '::LOOKUPID=' . $LOOKUPID . '::COMMTYPE=' . $COMMTYPE . '::CHECKSUM=' . $CHECKSUM;
            $encryptedStr = $this->_paymentencrypt($plainData, $ENCKEY);

            $form = '<form action="' . $URL . '" method="POST"   id="myForm1" style="display:none;">
            @csrf
            <input type="hidden" name="MERCHANTCODE" value="' . $MERCHANTCODE . '">
            <input type="hidden" name="SERVICEID" value="' . $SERVICEID . '">
            <input type="hidden" name="ENCDATA" value="' . $encryptedStr . '"><script type="text/javascript">document.getElementById("myForm1").submit();</script>
            </form>';
            echo $form;
            // dd($fields);
            exit;
        }
        return view('fsdv_payment.fsdv_registration_fee', compact('fldNameAliase', 'stream_id', 'estudent_id', 'eenrollment', 'adm_types', 'course', 'enrollment', 'page_title', 'student', 'isAlreadyRaisedRequest', 'erequestCount', 'issueCount', 'categorya', 'paymentIssueDetails', 'student', 'model', 'gender_id', 'application_fee'));
    }

    public function fsdv_before_payment_verify_request($student_id = null)
    {
        $found = false;
        $fsdv_exam_year = $academicyear_id = $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
        $fsdv_exam_month = $current_exam_month_id = Config::get("global.form_current_exam_month_id");


        $studentBaseDetails = Student::where('students.id', $student_id)
            ->where('students.exam_year', $current_admission_session_id)
            ->where('students.exam_month', $current_exam_month_id)->first();
        $studentCount = $studentBaseDetails->count();

        if (@$studentCount && $studentCount > 0) {
        } else {
            return redirect()->back()->with('error', 'Failed! You entred details not matched with us! Please try again');
        }

        $super_admin_id = Config::get('global.super_admin_id');
        $documentVerificationDetails = DocumentVerification::where('student_id', $student_id)
            ->where('student_verifications.role_id', @$super_admin_id)
            ->where('student_verifications.challan_tid', "!= ", "")
            ->orderby("student_verifications.id", "DESC")
            ->first();

        if (@$documentVerificationDetails->department_status && @$documentVerificationDetails->department_status == 4) {

        } else {
            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($studentBaseDetails->enrollment))->with('error', 'Failed! Your clarification form still not submitted. Please first submit your form.Hence you are not allowed to pay fees!');
        }

        if ($documentVerificationDetails->challan_tid != null) {
            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($studentBaseDetails->enrollment))->with('error', 'Failed! Invalid request access!challan number already present with this request.');
        }


        $student_verification_id = $documentVerificationDetails->id;
        $StudentFormVerificationErequests = StudentFormVerificationErequest::where('student_id', $student_id)
            // ->where('student_verification_id',$student_verification_id)
            ->whereIn('rtype', [1, 2])
            ->whereIn('status', [1, 7])
            ->get();

        if (!@$StudentFormVerificationErequests) {
            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($studentBaseDetails->enrollment))->with('error', 'Failed! Invalid request access!challan number already present with this request.');
        } else {
            if (!empty($StudentFormVerificationErequests) && count($StudentFormVerificationErequests) > 0) {
                foreach ($StudentFormVerificationErequests as $StudentFormVerificationErequest) {
                    $found = $this->_verifyByStudentFormVerificationErequestId(@$StudentFormVerificationErequest->id);
                }
            }
            if ($found == false) {
                return $found;
            }
            if (Auth::check()) {
                $user_id = Auth::user()->id;
                $role_id = Session::get('role_id');
                return redirect()->route('fsdv_listing_payment_issues')->with('success', 'Your Transaction has been made successfully!');
            }
            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($studentBaseDetails->enrollment))->with('success', 'Your Transaction has been made successfully!');
        }
    }

    public function _verifyByStudentFormVerificationErequestId($id, $documentVerificationDetailsId = null)
    {
        $found = false;
        $StudentFormVerificationErequest = StudentFormVerificationErequest::where('id', $id)->first();

        if (!empty($StudentFormVerificationErequest)) {
            $fld = "id";
            $$fld = $StudentFormVerificationErequest->$fld;
            $fld = "amount";
            $AMOUNT = $application_fee = $$fld = $StudentFormVerificationErequest->$fld;

            $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
            $current_exam_month_id = Config::get("global.form_current_exam_month_id");
            $academicyear_id = $fsdv_exam_year = @$current_admission_session_id;
            $fsdv_exam_month = @$current_exam_month_id;


            $fld = "student_id";
            $$fld = $StudentFormVerificationErequest->$fld;

            $student_details = DocumentVerification::where('id', $documentVerificationDetailsId)
                // ->where('exam_year',$fsdv_exam_year)
                // ->where('exam_month',$fsdv_exam_month)
                ->first();

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

            $student_verification_id = $StudentFormVerificationErequest->student_verification_id;
            $student_id = $StudentFormVerificationErequest->student_id;
            $PRN = $academicyear_id . $fsdv_exam_month . 'R' . $erequest_id . 'R' . $student_verification_id . 'R' . $student_id;

            $fld = "verifyUrl";
            $$fld = config("global.Emitra_" . $EmitraConfigEnvironment . "_" . $fld);


            $encrypted_fields = array("MERCHANTCODE" => "$MERCHANTCODE", "SERVICEID" => "$SERVICEID", "PRN" => "$PRN", "AMOUNT" => "$AMOUNT");

            $encryption_key = $ENCKEY;

            $res1 = $this->_invokePostAPINewPg($verifyUrl, json_encode($encrypted_fields));

            if ($res1['status'] == 1) {
                $output = json_decode($res1['json_output'], true);

                if (isset($output['data']['STATUS'])) {
                    $json_output = $output['data'];
                    $encryptedStr = $json_output['ENCDATA'];
                    $string = $this->_paymentdecrypt($encryptedStr, $encryption_key);
                    $response = $this->_paymentgetresponse($string);
                    $fields = $response;

                    $studentDocumentVerificationSave['is_eligible_for_verify'] = 0;
                    if (@$json_output['STATUS'] && $json_output['STATUS'] == 'SUCCESS') {
                        $studentDocumentVerificationSave['is_eligible_for_verify'] = 1;
                    }
                    $studentDocumentVerificationDetails = StudentDocumentVerification::where('student_verification_id', $student_verification_id)->where('student_id', $student_id)->orderby("id", "DESC")->update($studentDocumentVerificationSave);

                    if ($json_output['STATUS'] == 'SUCCESS') {
                        if (@$response['RECEIPTNO']) {
                            if (empty($student_details->challan_tid)) {
                                $updateStudentFormVerificationErequest = StudentFormVerificationErequest::find($erequest_id);
                                $updateStudentFormVerificationErequest->response = $StudentFormVerificationErequestSave['response'] = $string;
                                $updateStudentFormVerificationErequest->status = $StudentFormVerificationErequestSave['status'] = 1;
                                $updateStudentFormVerificationErequest->is_eligible = $StudentFormVerificationErequestSave['is_eligible'] = 1;
                                $updateStudentFormVerificationErequest->prn = $StudentFormVerificationErequestSave['prn'] = $PRN;
                                $updateStudentFormVerificationErequest->challan_tid = $StudentFormVerificationErequestSave['challan_tid'] = $fields['RECEIPTNO'];
                                $updateStudentFormVerificationErequest->student_id = $StudentFormVerificationErequestSave['student_id'] = $fields['UDF1'];
                                $updateStudentFormVerificationErequest->transaction_id = $StudentFormVerificationErequestSave['transaction_id'] = $fields['TRANSACTIONID'];
                                $updateStudentFormVerificationErequest->save();

                                $currentDateTime = date('Y-m-d H:i:s');
                                $updateStudentVerificationStudent = DocumentVerification::where('id', $documentVerificationDetailsId)
                                    // ->where('exam_year',$fsdv_exam_year)
                                    // ->where('exam_month',$fsdv_exam_month)
                                    ->first();
                                $updateStudentVerificationStudent->challan_tid = $fields['RECEIPTNO'];
                                $updateStudentVerificationStudent->fee_date = $currentDateTime;
                                $updateStudentVerificationStudent->submitted = $currentDateTime;
                                $updateStudentVerificationStudent->fee_status = 1;
                                $updateStudentVerificationStudent->is_fee_paid = 1;
                                // $updateStudentVerificationStudent->is_eligible = 1;
                                $updateStudentVerificationStudent->fee_paid_amount = $fields['AMOUNT'];
                                $updateStudentVerificationStudent->save();
                                $paymentIssueUpdate = FsdvPaymentIssue::where('student_id', $student_id)->first();
                                if (@$paymentIssueUpdate) {
                                    $paymentIssueUpdate->status = 1;
                                    $paymentIssueUpdate->is_archived = 1;
                                    $paymentIssueUpdate->save();
                                }
                                if (@$student_details) {
                                }
                                $found = true;
                            } else {
                                $found = true;
                            }
                        } else {
                            $updateStudentFormVerificationErequest = StudentFormVerificationErequest::find($erequest_id);
                            $updateStudentFormVerificationErequest->response = $StudentFormVerificationErequestSave['response'] = $string;
                            $updateStudentFormVerificationErequest->status = $StudentFormVerificationErequestSave['status'] = 2;
                            $updateStudentFormVerificationErequest->save();
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

    public function fsdv_response(Request $request)
    {
        $responoseData = $request->all();

        $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
        $current_exam_month_id = Config::get("global.form_current_exam_month_id");
        $academicyear_id = $fsdv_exam_year = @$current_admission_session_id;
        $fsdv_exam_month = @$current_exam_month_id;
        $super_admin_id = Config::get('global.super_admin_id');
        $enrollment = null;
        // dd($responoseData);
        if (isset($responoseData['STATUS'])) {
            $STATUS = $responoseData['STATUS'];
            $textToDecrypt = $responoseData['ENCDATA'];
            // Log::info('Online Fsdv Payment STATUS :' . $STATUS);
            // Log::info('Online Fsdv Payment encrypted_data Response :' . $textToDecrypt);

            $academicyear_id = config("global.admission_academicyear_id");
            $EmitraConfigEnvironment = config("global.Emitra_environment");
            $SERVICEID = config("global.Emitra_adm_only_payment_service");

            // Log::info('Online Fsdv Payment STATUS :' . $STATUS);
            $fld = "ENCKEY";
            $encryption_key = $$fld = config("global.EmitraService_" . $SERVICEID . "_" . $fld);

            $encryptedStr = $responoseData['ENCDATA'];
            $decrypted_data = $this->_paymentdecrypt($encryptedStr, $encryption_key);
            $fields = $this->_paymentgetresponse($decrypted_data);

            // Log::info('Online Fsdv PaymentPost Data :' . json_encode($responoseData));
            // Log::info('Online Fsdv Paymentdecrypted_data Response :' . $decrypted_data);
            $masterDetails = explode('R', $fields['PRN']);
            $student_verification_id = @$masterDetails[2];
            $student_id = @$masterDetails[3];


            $StudentFormVerificationErequestData = StudentFormVerificationErequest::where('student_verification_id', $student_verification_id)->first();
            $student_id = @$StudentFormVerificationErequestData->student_id;
            $studentMaster = Student::where('students.id', $student_id)->first();

            $fld = "enrollment";
            if (@$studentMaster->$fld) {
                $$fld = $studentMaster->$fld;
            }
            $studentDocumentVerificationSave['is_eligible_for_verify'] = 0;
            if (@$responoseData['STATUS'] && $responoseData['STATUS'] == 'SUCCESS') {
                $studentDocumentVerificationSave['is_eligible_for_verify'] = 1;
            }
            $studentDocumentVerificationDetails = StudentDocumentVerification::where('student_verification_id', $student_verification_id)->where('student_id', $student_id)->orderby("id", "DESC")->update($studentDocumentVerificationSave);
            if ($responoseData['STATUS'] == 'SUCCESS') {
                if ($STATUS == 'SUCCESS' || $STATUS == 'PENDING') {

                    $masterDetails = explode('R', $fields['PRN']);

                    // $combo_name = 'fsdv_exam_year';$fsdv_exam_year = $this->master_details($combo_name);$fsdv_exam_year = $fsdv_exam_year[1];
                    // $combo_name = 'reval_exam_month';$fsdv_exam_month = $this->master_details($combo_name);$fsdv_exam_month = $fsdv_exam_month[1];

                    $StudentVerificationIdArr = DocumentVerification::where('id', $student_verification_id)->first('student_id');
                    $student_id = @$StudentVerificationIdArr->student_id;

                    // key 1 = erequest id
                    // key 2 = student reval id

                    $REQUESTID = $transaction_id = $erequest_id = @$masterDetails[1];
                    $SERVICEID = config("global.Emitra_adm_only_payment_service");
                    $StudentFormVerificationErequest = StudentFormVerificationErequest::where('student_form_verification_erequests.id', $transaction_id)
                        ->first();


                    if (@$StudentFormVerificationErequest) {

                        if ($STATUS == 'SUCCESS') {

                            $updateStudentFormVerificationErequest = StudentFormVerificationErequest::find($REQUESTID);

                            $updateStudentFormVerificationErequest->student_id = $student_id = $fields['UDF1'];
                            $updateStudentFormVerificationErequest->amount = $StudentFormVerificationErequestSave['amount'] = $fields['AMOUNT'];
                            $updateStudentFormVerificationErequest->challan_tid = $StudentFormVerificationErequestSave['challan_tid'] = $fields['RECEIPTNO'];
                            $updateStudentFormVerificationErequest->service_id = $StudentFormVerificationErequestSave['service_id'] = $SERVICEID;
                            $updateStudentFormVerificationErequest->transaction_id = $StudentFormVerificationErequestSave['transaction_id'] = $fields['TRANSACTIONID'];
                            $updateStudentFormVerificationErequest->prn = $StudentFormVerificationErequestSave['prn'] = $fields['PRN'];
                            $updateStudentFormVerificationErequest->status = $StudentFormVerificationErequestSave['status'] = 1;

                            $updateStudentFormVerificationErequest->response = $StudentFormVerificationErequestSave['response'] = $decrypted_data;
                            $updateStudentFormVerificationErequest->ENCDATA = $StudentFormVerificationErequestSave['ENCDATA'] = $encryptedStr;
                            $updateStudentFormVerificationErequest->id = $StudentFormVerificationErequestSave['id'] = $REQUESTID;
                            $updateStudentFormVerificationErequest->save();


                            $updateStudentVerificationStudent = DocumentVerification::where('id', '=', $student_verification_id)
                                // ->where('exam_year', "=",$fsdv_exam_year)
                                // ->where('exam_month', "=",$fsdv_exam_month)
                                ->first();


                            $updateStudentVerificationStudent->challan_tid = $fields['RECEIPTNO'];
                            $currentDateTime = date('Y-m-d H:i:s');
                            $updateStudentVerificationStudent->fee_date = $currentDateTime;
                            $updateStudentVerificationStudent->submitted = $currentDateTime;
                            $updateStudentVerificationStudent->fee_status = 1;
                            $updateStudentVerificationStudent->is_fee_paid = 1;
                            // $updateStudentVerificationStudent->is_eligible = 1;
                            $updateStudentVerificationStudent->fee_paid_amount = $fields['AMOUNT'];

                            $updateStudentVerificationStudent->save();

                            $paymentIssueUpdate = FsdvPaymentIssue::where('student_id', $student_id)->first();


                            if (@$paymentIssueUpdate) {
                                $paymentIssueUpdate->status = 1;
                                $paymentIssueUpdate->is_archived = 1;
                                $paymentIssueUpdate->save();
                            }
                            if (@$student_id) {
                                //$this->_sendFsdvPaymentSubmitMessage($student_id);
                            }
                            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('message', 'Your Transaction has been made successfully!');
                        } else {
                            $updateStudentFormVerificationErequestElseCase = StudentFormVerificationErequest::find($REQUESTID);
                            $updateStudentFormVerificationErequestElseCase->response = $StudentFormVerificationErequestSave['response'] = $decrypted_data;
                            $updateStudentFormVerificationErequest->ENCDATA = $StudentFormVerificationErequestSave['ENCDATA'] = $encryptedStr;
                            $updateStudentFormVerificationErequestElseCase->status = $StudentFormVerificationErequestSave['status'] = 2;
                            $updateStudentFormVerificationErequestElseCase->id = $StudentFormVerificationErequestSave['id'] = $REQUESTID;
                            $updateStudentFormVerificationErequestElseCase->save();
                            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('error', 'Your Transaction is pending from bank!');
                        }
                    }
                } else {
                    return redirect()->route('fsdv_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('error', $fields['RESPONSEMESSAGE']);
                }
            } else {
                $msg = (isset($fields['RESPONSEMESSAGE'])) ? $fields['RESPONSEMESSAGE'] : 'There was some error, Please try again!';
                return redirect()->route('fsdv_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('error', $msg);
            }
        } else {
            // dd('test');
            $updateStudentFormVerificationErequestElseCase = StudentFormVerificationErequest::find($REQUESTID);
            $updateStudentFormVerificationErequestElseCase->response = $StudentFormVerificationErequestSave['response'] = $decrypted_data;
            $updateStudentFormVerificationErequestElseCase->status = $StudentFormVerificationErequestSave['status'] = 2;
            $updateStudentFormVerificationErequestElseCase->id = $StudentFormVerificationErequestSave['id'] = $REQUESTID;
            $updateStudentFormVerificationErequestElseCase->save();
            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('info', 'Your Transaction is pending from bank!');
        }
        return redirect()->route('fsdv_registration_fee', Crypt::encrypt($studentMaster->enrollment));
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

    public function fsdv_listing_payment_issues(Request $request)
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
        $title = "Clarification Payment Issues Report";
        $table_id = "Clarification_Payment_Issue_Report";
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
        dd($breadcrumbs);
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
                'fld' => 'challan_tid'
            )
        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $academicyear_id = config("global.form_admission_academicyear_id");
        $exam_month = Config::get('global.form_current_exam_month_id');
        $conditions["students.exam_year"] = @$academicyear_id;
        $conditions["students.exam_month"] = @$academicyear_id;
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
                'fld' => 'verify_request',
                'icon' => '<i class="material-icons" title="Click here to Verify.">check</i>',
                'fld_url' => '../payments/verify_request/#enrollment#'
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

        $master = $custom_component_obj->getPaymentIssuesData($formId);

        return view('payment.listing_payment_issues', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function fsdv_raise_request(Request $request, $enrollment)
    {
        $enrollment = Crypt::decrypt($enrollment);

        $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
        $current_exam_month_id = Config::get("global.form_current_exam_month_id");
        $academicyear_id = $fsdv_exam_year = @$current_admission_session_id;
        $fsdv_exam_month = @$current_exam_month_id;

        $studentDetails = Student::where('students.enrollment', $enrollment)
            ->where('students.exam_year', $current_admission_session_id)
            ->where('students.exam_month', $current_exam_month_id)
            ->first('id');


        $student_id = @$studentDetails->id;

        $studentDocumentVerificationDetails = DocumentVerification::where('student_verifications.student_id', "=", $student_id)
            // ->where('exam_year', "=",$fsdv_exam_year)
            // ->where('exam_month', "=",$fsdv_exam_month)
            ->first();

        $student_verification_id = $studentDocumentVerificationDetails->id;
        $student_id = $studentDocumentVerificationDetails->student_id;
        $isAlreadyRaisedRequest = FsdvPaymentIssue::where('id', $student_verification_id)->count();

        if ($isAlreadyRaisedRequest > 0) {
            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($enrollment))->with('error', 'Request has been already made by you.');
        }

        $Countchallan_tid = DocumentVerification::where('id', "=", $student_verification_id)
            // ->where('exam_year', "=",$fsdv_exam_year)
            // ->where('exam_month', "=",$fsdv_exam_month)
            ->where('challan_tid', "!=", null)->count();

        if (@$Countchallan_tid) {
            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($enrollment))->with('error', 'Your challan number already present with us!');
        }

        $StudentFormVerificationErequests = StudentFormVerificationErequest::
        where('student_id', $student_id)
            ->whereIn('rtype', [1, 2])
            ->whereNotIn('status', [1, 7])
            ->get();

        if (empty($StudentFormVerificationErequests)) {
            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($enrollment))->with('error', 'Invalid request access!');
        } else {
            $paymentIssueToBeSave = ['student_verification_id' => $student_verification_id, 'student_id' => $student_id, 'enrollment' => $enrollment, 'is_archived' => 0, 'status' => 0];
            FsdvPaymentIssue::create($paymentIssueToBeSave);
            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($enrollment))->with('message', 'Your request to admin for Transaction has been made successfully!');
        }
    }

    public function fsdv_sendSMSMessageForFeePaid(Request $request)
    {
        $students = DocumentVerification::where('locksumbitted', "=", 1)
            ->where('enrollment', "!=", NULL)
            ->where('submitted', "=", NULL)
            ->where('fee_paid_amount', "=", NULL)
            ->where('student_id', "=", 192)
            ->pluck('student_id');

        foreach ($students as $k => $student_id) {
            $this->_sendStudentVerificationLockSubmittedMessage($student_id);
            echo ($k + 1) . $student_id . "<br>";
        }
        echo "Message sent to " . count($students) . " students.";
        die;
    }

    public function fsdv_verify_request(Request $request, $enrollment)
    {
        $eenrollment = $enrollment;
        $enrollment = Crypt::decrypt($enrollment);

        $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
        $current_exam_month_id = Config::get("global.form_current_exam_month_id");
        $academicyear_id = $fsdv_exam_year = @$current_admission_session_id;
        $fsdv_exam_month = @$current_exam_month_id;

        $studentBaseDetails = $studentDetails = Student::where('students.enrollment', $enrollment)->first('id');


        $student_id = @$studentDetails->id;

        $studentDocumentVerificationDetails = DocumentVerification::where('student_id', "=", $student_id)
            // ->where('exam_year', "=",$fsdv_exam_year)
            // ->where('exam_month', "=",$fsdv_exam_month)
            ->first();

        if (!@$studentDocumentVerificationDetails) {
            return redirect()->back()->with('error', 'Failed! Student not found! Please try again');
        }

        $student_verification_id = $studentDocumentVerificationDetails->id;


        if (!@$studentDocumentVerificationDetails->student_id) {
            if (@$validator) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! You entred details not matched with us! Please try again');
            }
            return redirect()->back()->with('error', 'Failed! You entred details not matched with us! Please try again');
        }


        $super_admin_id = Config::get('global.super_admin_id');


        $documentVerificationDetails = DocumentVerification::where('student_id', $student_id)
            ->where('student_verifications.role_id', @$super_admin_id)
            ->orderby("student_verifications.id", "DESC")
            ->first();
        // dd($documentVerificationDetails->department_status);//may take once again test here
        if (@$documentVerificationDetails->department_status && @$documentVerificationDetails->department_status == 4) {

        } else {
            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($studentBaseDetails->enrollment))->with('error', 'Failed! Your clarification form still not submitted. Please first submit your form.Hence you are not allowed to pay fees!');
        }

        if (@$documentVerificationDetails->challan_tid != null) {
            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($student->enrollment))->with('error', 'Failed! Invalid request access! Challan number already present with this request.');
        }
        $StudentFormVerificationErequests = StudentFormVerificationErequest::where('student_id', $student_id)
            // ->whereIn('rtype', [1,2])
            // ->whereIn('status', [1,7])
            ->whereIn('status', [0, 2])
            ->get();

        if (empty($StudentFormVerificationErequests)) {
            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($student->enrollment))->with('error', 'Failed! Invalid request access! Still not found any request with your payment request.');
        } else {
            $found = null;
            if (!empty($StudentFormVerificationErequests) && count($StudentFormVerificationErequests) > 0) {
                foreach ($StudentFormVerificationErequests as $StudentFormVerificationErequest) {
                    $found = $this->_verifyByStudentFormVerificationErequestId(@$StudentFormVerificationErequest->id, @$documentVerificationDetails->id);
                }
            }
            if ($found == false) {
                if (Auth::check() && Auth::user()->id) {
                    $user_id = Auth::user()->id;
                    $role_id = Session::get('role_id');
                    if (!empty($user_id)) {
                        return redirect()->route('fsdv_listing_payment_issues')->with('error', 'Amount not verified at payment gatway!');
                    }
                }
                return redirect()->route('fsdv_registration_fee', Crypt::encrypt($enrollment))->with('info', 'Your Transaction has not been verified yet or there is no transaction found to verify!');
            }
            if (Auth::check() && Auth::user()->id) {
                $user_id = Auth::user()->id;
                $role_id = Session::get('role_id');
                if (!empty($user_id)) {
                    return redirect()->route('fsdv_listing_payment_issues')->with('success', 'Your Transaction has been made successfully!');;
                }
            }
            return redirect()->route('fsdv_registration_fee', Crypt::encrypt($enrollment))->with('success', 'Your Transaction has been made successfully!');
        }
    }

}