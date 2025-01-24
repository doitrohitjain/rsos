<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Models\Registration;
use App\Models\Student;
use App\Models\SuppErequest;
use App\Models\Supplementary;
use App\Models\SuppPaymentIssue;
use App\Models\SuppStudentFee;
use App\Models\SuppStudentFees;
use Auth;
use Cache;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use PDF;
use Redirect;
use Response;
use Route;
use Session;
use Validator;

class SuppPaymentController extends Controller
{
    function __construct()
    {
    }


    public function supp_listing_payment_issues(Request $request)
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
        $title = "Supplementary Payment Issues Report";
        $table_id = "Supplementary_Payment_Issue_Report";
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
                'status' => false,
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
                'dbtbl' => 'supplementaries',
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
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Is Issue Resolved",
                'fld' => 'is_archived',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is Resolved',
                'dbtbl' => 'supp_payment_issues'
            ),
            array(
                "lbl" => "Fee Amount",
                'fld' => 'total',
                'input_type' => 'text',
                'placeholder' => "Fee Amount",
                'dbtbl' => 'supp_student_fees',
            ),
        );

        $tableData = array(
            array(
                "lbl" => "Sr. No.",
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
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            array(
                "lbl" => "Is Resolved",
                'fld' => 'is_archived',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            // array(
            // 	"lbl" => "Challan Number",
            // 	'fld' => 'challan_tid'
            // )
        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();

        $aiCenters = $custom_component_obj->getAiCenters();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $conditions["supplementaries.user_id"] = @Auth::user()->id;
        } else {
            // $filters[] = array(
            // 	"lbl" => "Ai Center",
            // 	'fld' => 'ai_code',
            //     'input_type' => 'select',
            // 	'options' => $aiCenters,
            // 	'placeholder' => "Ai Center",
            // 	'dbtbl' => 'supplementaries',
            // );

            $tableData[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'supplementaries'
            );
        }

        $actions = array(
            array(
                'fld' => 'supp_verify_request',
                'icon' => '<i class="material-icons" title="Click here to Verify.">check</i>',
                'fld_url' => '../supp_payments/supp_verify_request/#enrollment#'
            ),
        );

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "",
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

        $master = $custom_component_obj->getSuppPaymentIssuesData($formId);


        return view('supp_payment.supp_listing_payment_issues', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }


    public function supp_admission_fee_payment(Request $request)
    {
        $searchBoxClass = "show";
        $student = array();
        $page_title = 'Supplementary Application Form Fee Payment';
        $model = "Student";
        $showSuppStatus = $this->_getCheckAllowToCheckSupp();
        if (!$showSuppStatus) {
            return redirect()->route("landing")->with('error', 'Invalid Access!');
        }
        if (count($request->all()) > 0) {
            $isValid = true;
            $data = $request->all();
            $modelObj = new Student;

            $validator = Validator::make($request->all(), $modelObj->forPaymentFindStudentBaseRule);
            if ($validator->fails()) {
                $isValid = false;
            }
            if (!$isValid) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $student = array();
            $fld = "dob";
            if (isset($data[$fld]) && !empty($data[$fld])) {
                $$fld = $data[$fld];
            }
            $fld = "enrollment";
            if (isset($data[$fld]) && !empty($data[$fld])) {
                $$fld = $data[$fld];
            }

            $dobArr = explode("/", $dob);
            if (isset($dobArr[0]) && isset($dobArr[1]) && isset($dobArr[2])) {
                $dob = $dobArr[2] . "-" . $dobArr[1] . "-" . $dobArr[0] . "";
            }

            if (count($request->all()) > 0 && !empty($request->enrollment)) {
                $master = Student::where('enrollment', $request->enrollment)->first();
                if (empty($master->id)) {
                    return redirect()->back()->with('error', 'Failed! Student Enrollment not found,Please check student enrollment details.');
                } else {
                    $student_id = $master->id;
                }
            }
            $exam_year = Config::get('global.form_supp_current_admission_session_id');
            $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

            $SupplementaryIdArr = Supplementary::where('student_id', $student_id)->where('exam_year', '=', $exam_year)->where('exam_month', '=', $exam_month)->latest('id')->first('id');
            $supp_id = null;
            if (!empty(@$SupplementaryIdArr->id)) {
                $supp_id = @$SupplementaryIdArr->id;
            }
            $student = Supplementary::leftJoin('supp_student_fees', 'supp_student_fees.student_id', '=', 'supplementaries.student_id')
                //->where('supplementaries.enrollment', "=",$enrollment)
                ->where('supplementaries.student_id', "=", $student_id)
                // ->where('supplementaries.dob', "=",$dob)
                ->where('supplementaries.id', "=", $supp_id)
                ->whereNull('supplementaries.challan_tid')
                ->first(['supp_student_fees.total', 'supplementaries.*']);
            //dd($student);
            if (!@$student->id) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! You entred details not matched with us! Please try again');
            }

            $student_id = $student->id;
            if (@$student->locksumbitted && @$student->locksubmitted_date) {

            } else {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! Your supplementary form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay Supplementary fees!');
            }

            $application_fee = 0;
            if ($student->total_fees) {
                $application_fee = $student->total_fees;
            }


            if ($application_fee <= 0) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! Please contact to AI Centre.Your admission fees is zero(0)!');
            }
            return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment))->with('message', 'Student has been found successfully!');
        }
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        return view('supp_payment.supp_admission_fee_payment', compact('page_title', 'searchBoxClass', 'student', 'model', 'gender_id'));
    }

    public function supp_registration_fee(Request $request, $enrollment)
    {
        $custom_component_obj = new CustomComponent;

        $formOpenAllowOrNot = $custom_component_obj->checkAnySuppEntryAllowOrNot();
        $errMsg = null;
        if (!$formOpenAllowOrNot) {
            $errMsg = 'पूरक आवेदन पत्र की तिथि समाप्त कर दी गई है।(The supplementary Application form date is closed.)';
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
                return redirect()->route('aicenterdashboard')->with('error', $errMsg);
            }
        }

        $table = $model = "Student";
        $page_title = 'Make Payment For Supplementary Application';
        $eenrollment = $enrollment;
        $enrollment = Crypt::decrypt($enrollment);
        $student = array();

        $model = "Student";
        $fld = "enrollment";
        $custom_component_obj = new CustomComponent;
        $exam_year = Config::get('global.form_supp_current_admission_session_id');
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

        // $exam_month = $current_exam_month_id = 1;

        $student = Supplementary::Join('applications', 'applications.student_id', '=', 'supplementaries.student_id')
            ->Join('supp_student_fees', 'supp_student_fees.student_id', '=', 'supplementaries.student_id')
            ->Join('students', 'supplementaries.student_id', '=', 'students.id')
            ->where('supplementaries.enrollment', "=", $enrollment)
            ->where('supplementaries.exam_year', "=", $exam_year)
            ->where('supplementaries.exam_month', "=", $exam_month)
            ->get(['supp_student_fees.*', 'students.name', 'students.father_name', 'supplementaries.*'])
            ->first();


        $supplementary_id = $student->supplementary_id;
        $student_id = $student->student_id;

        if (!@$student->id) {
            if (@$validator) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! You entred details not matched with us! Please try again');
            }
            return redirect()->route('supp_admission_fee_payment')->with('error', 'Failed! Your supplementary form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay fees!');
        }

        $student_id = @$student->student_id;
        $supplementary_id = @$student->supplementary_id;
        $estudent_id = Crypt::encrypt($student_id);

        if (@$student->locksumbitted && @$student->locksubmitted_date) {
        } else {
            return redirect()->route('supp_admission_fee_payment')->with('error', 'Failed! Your supplementary form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay fees!');
        }

        $application_fee = 0;
        if (@$student->total_fees) {
            $application_fee = $student->total_fees;
        }
        if ($application_fee <= 0) {
            if (isset($validator)) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! Your supplementary fees is zero(0) hence does not need to pay supplementary fees!');
            } else {
                return redirect()->route('supp_admission_fee_payment')->with('error', 'Failed! Your supplementary fees is zero(0) hence does not need to pay supplementary fees!');
            }
        }

        $isAlreadyRaisedRequest = SuppErequest::where('student_id', $student_id)
            ->whereIn('status', [0, 2])
            ->count();

        $erequestCount = SuppErequest::where('student_id', $student_id)
            ->where('rtype', 1)
            ->whereIn('status', [1, 7])
            ->count();

        $issueCount = SuppPaymentIssue::where('student_id', $student_id)->count();

        $paymentIssueDetails = array();
        if ($issueCount > 0) {
            $paymentIssueDetails = SuppPaymentIssue::where('student_id', $student_id)->get();
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
                $this->supp_before_payment_verify_request($enrollment);
            }

            $EmitraConfigEnvironment = config("global.Emitra_environment");
            $SERVICEID = config("global.Emitra_adm_only_payment_service");

            $academicyear_id = config("global.admission_academicyear_id");
            $erequestToBeSave = ['service_id' => $SERVICEID, 'supplementary_id' => $supplementary_id, 'student_id' => $student_id, 'emitra_id' => null, 'amount' => $application_fee, 'rtype' => 1, 'status' => 0];

            $SuppErequestSaved = SuppErequest::create($erequestToBeSave);
            $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

            $erequest_id = $SuppErequestSaved->id;
            //$REQUESTID = $academicyear_id . '' . 'S' . $student_id . 'S' . $erequest_id;
            // $CONSUMERKEY = 'RSOS-' . '-SUPP-'. $academicyear_id . '-SUPP-ROH-' . $student_id;
            $REQUESTID = $academicyear_id . $exam_month . 'S' . $erequest_id . 'S' . $supplementary_id;
            $CONSUMERKEY = 'U-RSOS-' . '-SUPP-' . $academicyear_id . 'S' . $exam_month . '-SUPP-' . 'S' . $supplementary_id;

            if (strlen($student->name) <= 3) {
                $USERNAME = $student->name . " ForPayment";
            } else {
                $USERNAME = str_replace(array('\'', '"', ',', ';', "'", "`", "@", "#", "$", "!", '0', '<', '>', '.', '(', ")"), ' ', $student->name);
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
            // $USEREMAIL = 'engrohitjain5@gmail.com';

            $SUCCESSURL = $FAILUREURL = route('supp_response'); //payment_response.php
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
            Log::info('Online Supp PaymentRequest:' . $fields);

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


        if (@$student->fee_status == 1 && $student->submitted != null && $student->challan_tid != null) {

        } else {
            $student_id = $student->student_id;
            $supplementary_id = $student->supplementary_id;
            /* Supp Fee Details Update Start */
            // echo $student_id;die;
            $master_fee = $custom_component_obj->_getSuppFeeDetailsForDispaly($student_id);
            // dd($master_fee);
            $studentfeedata = array(
                'subject_change_fees' => @$master_fee['subject_change_fees'],
                'exam_fees' => @$master_fee['exam_subject_fees'],
                'practical_fees' => @$master_fee['practical_fees'],
                'forward_fees' => @$master_fee['forward_fees'],
                'online_fees' => @$master_fee['online_services_fees'],
                'late_fees' => @$master_fee['late_fees'],
                'total_fees' => @$master_fee['final_fees']
            );

            $supplementary_fee_update = Supplementary::where('id', $supplementary_id)->update($studentfeedata);

            $studentSubjectfeedata = array(
                'student_id' => $student_id,
                'supplementary_id' => $supplementary_id,
                'total' => @$master_fee['final_fees'],
            );
            $alredyPresent = SuppStudentFees::where('student_id', $student_id)->where('supplementary_id', $supplementary_id)->first();
            if (@$alredyPresent->id) {
                $studentfeesupdate = SuppStudentFees::where('student_id', $student_id)->where('supplementary_id', $supplementary_id)->update($studentSubjectfeedata);
            } else {
                $studentfeesupdate = SuppStudentFees::updateOrCreate($studentSubjectfeedata);
            }
            /* Supp Fee Details Update End */
        }

        /* Because fee update and need to be get updated fee amount start */
        $student = Supplementary::Join('applications', 'applications.student_id', '=', 'supplementaries.student_id')
            ->Join('supp_student_fees', 'supp_student_fees.student_id', '=', 'supplementaries.student_id')
            ->Join('students', 'supplementaries.student_id', '=', 'students.id')
            ->where('supplementaries.enrollment', "=", $enrollment)
            ->where('supplementaries.exam_year', "=", $exam_year)
            ->where('supplementaries.exam_month', "=", $exam_month)
            ->get(['supplementaries.*', 'supp_student_fees.*', 'students.name', 'students.father_name'])
            ->first();

        $application_fee = 0;
        if (@$student->total_fees) {
            $application_fee = $student->total_fees;
        }
        if ($application_fee <= 0) {
            if (isset($validator)) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! Your supplementary fees is zero(0) hence does not need to pay supplementary fees!');
            } else {
                return redirect()->route('supp_admission_fee_payment')->with('error', 'Failed! Your supplementary fees is zero(0) hence does not need to pay supplementary fees!');
            }
        }
        /* Because fee update and need to be get updated fee amount end */

        return view('supp_payment.supp_registration_fee', compact('stream_id', 'estudent_id', 'eenrollment', 'adm_types', 'course', 'enrollment', 'page_title', 'student', 'isAlreadyRaisedRequest', 'erequestCount', 'issueCount', 'categorya', 'paymentIssueDetails', 'student', 'model', 'gender_id', 'application_fee'));
    }

    public function supp_before_payment_verify_request($enrollment)
    {
        $exam_year = Config::get('global.form_supp_current_admission_session_id');
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

        $student = Supplementary::Join('supp_student_fees', 'supp_student_fees.student_id', '=', 'supplementaries.student_id')
            ->where('supplementaries.enrollment', "=", $enrollment)
            ->where('supplementaries.exam_year', "=", $exam_year)
            ->where('supplementaries.exam_month', "=", $exam_month)
            ->first();

        if (!@$student->student_id) {
            if (@$validator) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! You entred details not matched with us! Please try again');
            }
            return redirect()->back()->with('error', 'Failed! You entred details not matched with us! Please try again');
        }
        $student_id = $student->student_id;

        $challan_tid = Supplementary::where('student_id', $student_id)
            ->where('exam_year', "=", $exam_year)
            ->where('exam_month', "=", $exam_month)
            ->where('challan_tid', "!= ", "")
            ->count();

        if (@$student->locksumbitted && @$student->locksubmitted_date) {
        } else {
            return redirect()->route('supp_admission_fee_payment')->with('error', 'Failed! Your admission form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay fees!');
        }

        if ($challan_tid != 0) {
            return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment))->with('error', 'Failed! Invalid request access!challan number already present with this request.');
        }

        $SuppErequests = SuppErequest::where('student_id', $student_id)
            ->where('supplementary_id', $student->supplementary_id)
            ->whereIn('rtype', [1, 2])
            ->whereIn('status', [1, 7])
            ->get();

        if (!@$SuppErequests) {
            return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment))->with('error', 'Failed! Invalid request access!challan number already present with this request.');
        } else {
            $found = null;
            if (!empty($SuppErequests) && count($SuppErequests) > 0) {
                foreach ($SuppErequests as $SuppErequest) {
                    $found = $this->_verifyBySuppErequestId(@$SuppErequest->id);
                }
            }

            if ($found == false) {
                if ($found == false) {
                    if (Auth::check()) {
                        $user_id = Auth::user()->id;
                        $role_id = Session::get('role_id');
                        return redirect()->route('supp_listing_payment_issues');
                    }
                    return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment))->with('info', 'Your Transaction has not been verified yet or there is no transaction found to verify!');
                }
                return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment));
            }

            if (Auth::check()) {
                $user_id = Auth::user()->id;
                $role_id = Session::get('role_id');
                return redirect()->route('supp_listing_payment_issues')->with('success', 'Your Transaction has been made successfully!');
            }

            return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment))->with('success', 'Your Transaction has been made successfully!');
        }
    }

    public function _verifyBySuppErequestId($id)
    {
        $found = false;
        $SuppErequest = SuppErequest::where('id', $id)->first();

        if (!empty($SuppErequest)) {
            $fld = "id";
            $$fld = $SuppErequest->$fld;
            $fld = "amount";
            $AMOUNT = $application_fee = $$fld = $SuppErequest->$fld;
            // $exam_year = Config::get('global.current_admission_session_id');
            $exam_year = Config::get('global.form_supp_current_admission_session_id');
            $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

            $fld = "student_id";
            $$fld = $SuppErequest->$fld;
            $academicyear_id = config("global.admission_academicyear_id");

            $student_details = Supplementary::where('student_id', $student_id)
                ->where('exam_year', $exam_year)
                ->where('exam_month', $exam_month)
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

            $supplementary_id = $SuppErequest->supplementary_id;
            $oldPRN = $REQUESTID = $academicyear_id . '' . 'S' . $student_id . 'S' . $erequest_id;
            $PRN = $academicyear_id . $exam_month . 'S' . $erequest_id . 'S' . $supplementary_id;


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
                    if ($json_output['STATUS'] == 'SUCCESS') {
                        if (@$response['RECEIPTNO']) {
                            if (empty($student_details->challan_tid)) {
                                $updateSuppErequest = SuppErequest::find($erequest_id);
                                $updateSuppErequest->response = $SuppErequestSave['supp_response'] = $string;
                                $updateSuppErequest->status = $SuppErequestSave['status'] = 1;
                                $updateSuppErequest->prn = $SuppErequestSave['prn'] = $PRN;
                                $updateSuppErequest->challan_tid = $SuppErequestSave['challan_tid'] = $fields['RECEIPTNO'];
                                $updateSuppErequest->student_id = $SuppErequestSave['student_id'] = $fields['UDF1'];
                                $updateSuppErequest->transaction_id = $SuppErequestSave['transaction_id'] = $fields['TRANSACTIONID'];
                                $updateSuppErequest->save();

                                $currentDateTime = date('Y-m-d H:i:s');
                                $updateSupplementaryStudent = Supplementary::where('student_id', $student_id)
                                    ->where('exam_year', $exam_year)
                                    ->where('exam_month', $exam_month)
                                    ->first();
                                $updateSupplementaryStudent->challan_tid = $fields['RECEIPTNO'];
                                $updateSupplementaryStudent->application_fee_date = $currentDateTime;
                                $updateSupplementaryStudent->submitted = $currentDateTime;
                                $updateSupplementaryStudent->fee_status = 1;
                                $updateSupplementaryStudent->is_eligible = 0;
                                $updateSupplementaryStudent->is_aicenter_verify = 2;
                                $updateSupplementaryStudent->is_department_verify = 1;
                                $updateSupplementaryStudent->fee_paid_amount = $fields['AMOUNT'];
                                $updateSupplementaryStudent->save();

                                $paymentIssueUpdate = SuppPaymentIssue::where('student_id', $student_id)->first();
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
                            $updateSuppErequest = SuppErequest::find($erequest_id);
                            $updateSuppErequest->response = $SuppErequestSave['supp_response'] = $string;
                            $updateSuppErequest->status = $SuppErequestSave['status'] = 2;
                            $updateSuppErequest->save();
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

    public function supp_response(Request $request)
    {
        $responoseData = $request->all();

        $exam_year = Config::get('global.form_supp_current_admission_session_id');
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');


        $enrollment = null;


        if (isset($responoseData['STATUS'])) {
            $STATUS = $responoseData['STATUS'];
            $textToDecrypt = $responoseData['ENCDATA'];
            Log::info('Online Supp Payment STATUS :' . $STATUS);
            Log::info('Online Supp Payment encrypted_data Response :' . $textToDecrypt);

            $academicyear_id = config("global.admission_academicyear_id");
            $EmitraConfigEnvironment = config("global.Emitra_environment");
            $SERVICEID = config("global.Emitra_adm_only_payment_service");

            Log::info('Online Supp Payment STATUS :' . $STATUS);
            $fld = "ENCKEY";
            $encryption_key = $$fld = config("global.EmitraService_" . $SERVICEID . "_" . $fld);

            $encryptedStr = $responoseData['ENCDATA'];
            $decrypted_data = $this->_paymentdecrypt($encryptedStr, $encryption_key);
            $fields = $this->_paymentgetresponse($decrypted_data);

            Log::info('Online Supp PaymentPost Data :' . json_encode($responoseData));
            Log::info('Online Supp Paymentdecrypted_data Response :' . $decrypted_data);

            $studentMaster = Supplementary::leftJoin('supp_student_fees', 'supp_student_fees.student_id', '=', 'supplementaries.student_id')
                ->where('supplementaries.student_id', "=", $fields['UDF1'])
                ->where('supplementaries.exam_year', "=", $exam_year)
                ->where('supplementaries.exam_month', "=", $exam_month)
                ->get(['supplementaries.*', 'supp_student_fees.total'])
                ->first();

            $fld = "enrollment";
            if (@$studentMaster->$fld) {
                $$fld = $studentMaster->$fld;
            }

            //@dd($responoseData);
            if ($responoseData['STATUS'] == 'SUCCESS') {
                if ($STATUS == 'SUCCESS' || $STATUS == 'PENDING') {
                    $masterDetails = explode('S', $fields['PRN']);

                    $exam_year = Config::get('global.form_supp_current_admission_session_id');
                    $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

                    $supp_id = @$masterDetails[2];
                    $SupplementaryIdArr = Supplementary::where('id', $supp_id)->where('exam_year', '=', $exam_year)->where('exam_month', '=', $exam_month)->first('student_id');
                    $student_id = @$SupplementaryIdArr->student_id;

                    // key 1 = erequest id
                    // key 2 = supplementaries id

                    $REQUESTID = $transaction_id = $erequest_id = $masterDetails[1];
                    $SERVICEID = config("global.Emitra_adm_only_payment_service");
                    $SuppErequest = SuppErequest::where('supp_erequests.id', $transaction_id)
                        ->first();

                    if (@$SuppErequest) {

                        if ($STATUS == 'SUCCESS') {

                            $updateSuppErequest = SuppErequest::find($REQUESTID);

                            $updateSuppErequest->student_id = $student_id = $fields['UDF1'];
                            $updateSuppErequest->amount = $SuppErequestSave['amount'] = $fields['AMOUNT'];
                            $updateSuppErequest->challan_tid = $SuppErequestSave['challan_tid'] = $fields['RECEIPTNO'];
                            $updateSuppErequest->service_id = $SuppErequestSave['service_id'] = $SERVICEID;
                            $updateSuppErequest->transaction_id = $SuppErequestSave['transaction_id'] = $fields['TRANSACTIONID'];
                            $updateSuppErequest->prn = $SuppErequestSave['prn'] = $fields['PRN'];
                            $updateSuppErequest->status = $SuppErequestSave['status'] = 1;
                            $updateSuppErequest->response = $SuppErequestSave['supp_response'] = $decrypted_data;
                            $updateSuppErequest->ENCDATA = $SuppErequestSave['ENCDATA'] = $encryptedStr;
                            $updateSuppErequest->id = $SuppErequestSave['id'] = $REQUESTID;
                            $updateSuppErequest->save();

                            $updateSupplementaryStudent = Supplementary::where('student_id', '=', $student_id)
                                ->where('exam_year', "=", $exam_year)
                                ->where('exam_month', "=", $exam_month)
                                ->first();

                            $updateSupplementaryStudent->challan_tid = $fields['RECEIPTNO'];
                            $currentDateTime = date('Y-m-d H:i:s');
                            $updateSupplementaryStudent->application_fee_date = $currentDateTime;
                            $updateSupplementaryStudent->submitted = $currentDateTime;
                            $updateSupplementaryStudent->fee_status = 1;
                            $updateSupplementaryStudent->is_eligible = 0;
                            $updateSupplementaryStudent->is_aicenter_verify = 2;
                            $updateSupplementaryStudent->is_department_verify = 1;
                            $updateSupplementaryStudent->fee_paid_amount = $fields['AMOUNT'];


                            $updateSupplementaryStudent->save();

                            $paymentIssueUpdate = SuppPaymentIssue::where('student_id', $student_id)->first();
                            if (@$paymentIssueUpdate) {
                                $paymentIssueUpdate->status = 1;
                                $paymentIssueUpdate->is_archived = 1;
                                $paymentIssueUpdate->save();
                            }
                            if (@$student_id) {
                                $this->_sendPaymentSubmitMessage($student_id);
                            }
                            return redirect()->route('supp_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('message', 'Your Transaction has been made successfully!');
                        } else {


                            $updateSuppErequestElseCase = SuppErequest::find($REQUESTID);
                            $updateSuppErequestElseCase->response = $SuppErequestSave['supp_response'] = $decrypted_data;
                            $updateSuppErequest->ENCDATA = $SuppErequestSave['ENCDATA'] = $encryptedStr;
                            $updateSuppErequestElseCase->status = $SuppErequestSave['status'] = 2;
                            $updateSuppErequestElseCase->id = $SuppErequestSave['id'] = $REQUESTID;
                            $updateSuppErequestElseCase->save();
                            return redirect()->route('supp_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('error', 'Your Transaction is pending from bank!');
                        }
                    }
                } else {
                    return redirect()->route('supp_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('error', $fields['RESPONSEMESSAGE']);
                }
            } else {
                $msg = (isset($fields['RESPONSEMESSAGE'])) ? $fields['RESPONSEMESSAGE'] : 'There was some error, Please try again!';
                return redirect()->route('supp_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('error', $msg);
            }
        } else {
            // dd('test');
            $updateSuppErequestElseCase = SuppErequest::find($REQUESTID);
            $updateSuppErequestElseCase->response = $SuppErequestSave['supp_response'] = $decrypted_data;
            $updateSuppErequestElseCase->status = $SuppErequestSave['status'] = 2;
            $updateSuppErequestElseCase->id = $SuppErequestSave['id'] = $REQUESTID;
            $updateSuppErequestElseCase->save();
            return redirect()->route('supp_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('info', 'Your Transaction is pending from bank!');
        }
        return redirect()->route('supp_registration_fee', Crypt::encrypt($studentMaster->enrollment));
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

    public function supp_bulk_verify_payment_issues($isSuppPaymentIssue = 1, $limit = 1000)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", -1);

        if ($isSuppPaymentIssue == 1) {
            $flagMsg = "<h1>Supplementary Only Raise Request Payment Issues <br></h1>";
            $student_ids = SuppPaymentIssue::where('is_archived', "!=", "")
                ->pluck('student_id');
        } else {
            $flagMsg = "<h1>All Payment Issues <br></h1>";
            $student_ids = SuppErequest::groupBy('student_id')
                ->where('supp_erequests.status', "!=", 1)
                ->pluck('student_id');
        }
        $students = Student::with('application', 'suppstudentfee') //supp_student_fees
        ->whereIn('students.id', $student_ids)
            ->where('students.challan_tid', "!=", "")
            ->limit($limit)
            ->orderBy('id', 'asc')
            ->pluck('enrollment', 'id');
        $yesCounter = 0;
        $noCounter = 0;
        $result = array();
        $found = false;


        foreach ($students as $student_id => $enrollment) {

            $SuppErequests = SuppErequest::groupBy('student_id')
                ->whereIn('rtype', [1, 2])
                ->whereNotIn('status', [1, 7])
                ->where('student_id', $student_id)
                ->get();

            foreach ($SuppErequests as $SuppErequest) {
                $found = $this->_verifyBySuppErequestId($SuppErequest->id);
                if ($found) {
                    break;
                }
            }
            if ($found) {
                $result[$student_id] = "Yes";
                $yesCounter++;
            } else {
                $result[$student_id] = "No";
                $noCounter++;
            }
        }
        echo "<br>" . $flagMsg;
        echo "<br> <h2>Supplementary Summary </h2><br>Total Students : " . ($yesCounter + $noCounter);

        echo "<br> <h2>Supplementary Summary </h2><br>Total Supplementary student payment verified : " . $yesCounter;
        echo "<br> <h2>Supplementary Summary </h2><br>Total Supplementary student payment not : " . $noCounter;
        echo "<br>";
        echo "<br>";
        echo "<table  border='1px'>";
        echo '<tr>';
        echo '<td>Student Id</td>';
        echo '<td>Payment Status</td>';
        echo '</tr>';
        if (count($result) > 0) {
            foreach ($result as $sid => $status) {
                echo '<tr>';
                echo '<td>' . $sid . '</td>';
                echo '<td>' . $status . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr>';
            echo '<td colspan="2">No Data</td>';
            echo '</tr>';
        }
        echo "</table>";
        die;
    }

    public function supp_bulk_find_duplicate_payment_issues()
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", -1);

        $students = SuppErequest::Join('students', 'supp_erequests.student_id', '=', 'students.id')
            ->groupBy('student_id')
            ->where("students.challan_tid", "!=", NULL)
            ->havingRaw('COUNT(student_id) > ?', [1])
            ->get('supp_erequests.*');

        $result = array();
        $counter = 1;
        foreach ($students as $student_idold => $student) {
            echo "Processing on " . $counter . " Supplementary Number student <br>";
            $yesCounter = 0;
            $noCounter = 0;
            $student_id = $student->student_id;
            $SuppErequests = SuppErequest::where('student_id', $student_id)->pluck('id');
            foreach ($SuppErequests as $erequest_id) {
                $found = false;
                $found = $this->_verifyBySuppErequestId($erequest_id);
                if ($found) {
                    $yesCounter++;
                } else {
                    $noCounter++;
                }
            }
            if ($yesCounter > 1) {
                $result[$student_id] = $yesCounter . " Supplementary number of times success translations found! Hitted : " . count($SuppErequests);
            } else if ($yesCounter == 1) {
                $result[$student_id] = $yesCounter . " Supplementary exact success translation found! Hitted : " . count($SuppErequests);
            } else if ($yesCounter <= 0) {
                $result[$student_id] = $yesCounter . " Supplementary No any translation found! Hitted : " . count($SuppErequests);
            }
            $counter++;
        }

        echo "<br> <h2>Summary </h2><br>Total Supplementary  Students : " . ($yesCounter + $noCounter);

        echo "<br> <h2>Summary </h2><br>Total Supplementary student payment verified : " . $yesCounter;
        echo "<br> <h2>Summary </h2><br>Total Supplementary student payment not : " . $noCounter;
        echo "<br>";
        echo "<br>";
        echo "<table  border='1px'>";
        echo '<tr>';
        echo '<td>Student Id</td>';
        echo '<td>Payment Status</td>';
        echo '</tr>';
        if (count($result) > 0) {
            foreach ($result as $sid => $status) {
                echo '<tr>';
                echo '<td>' . $sid . '</td>';
                echo '<td>' . $status . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr>';
            echo '<td colspan="2">No Data</td>';
            echo '</tr>';
        }
        echo "</table>";
        die;
    }

    public function supp_raise_request(Request $request, $enrollment)
    {
        $enrollment = Crypt::decrypt($enrollment);

        $exam_year = Config::get('global.form_supp_current_admission_session_id');
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

        $student = Supplementary::where('supplementaries.enrollment', "=", $enrollment)
            ->where('exam_year', "=", $exam_year)
            ->where('exam_month', "=", $exam_month)
            ->first();

        $supplementary_id = $student->id;
        $student_id = $student->student_id;
        $isAlreadyRaisedRequest = SuppPaymentIssue::where('student_id', $student_id)->count();

        if ($isAlreadyRaisedRequest > 0) {
            return redirect()->route('supp_registration_fee', Crypt::encrypt($enrollment))->with('error', 'Request has been already made by you.');
        }

        $Countchallan_tid = Supplementary::where('student_id', "=", $student_id)
            ->where('exam_year', "=", $exam_year)
            ->where('exam_month', "=", $exam_month)
            ->where('challan_tid', "!=", null)->count();

        if (@$Countchallan_tid) {
            return redirect()->route('supp_registration_fee', Crypt::encrypt($enrollment))->with('error', 'Your challan number already present with us!');
        }

        $SuppErequests = SuppErequest::
        where('student_id', $student_id)
            ->whereIn('rtype', [1, 2])
            ->whereNotIn('status', [1, 7])
            ->get();

        if (empty($SuppErequests)) {
            return redirect()->route('supp_registration_fee', Crypt::encrypt($enrollment))->with('error', 'Invalid request access!');
        } else {
            $paymentIssueToBeSave = ['supplementary_id' => $supplementary_id, 'student_id' => $student_id, 'enrollment' => $enrollment, 'is_archived' => 0, 'status' => 0];
            SuppPaymentIssue::create($paymentIssueToBeSave);
            return redirect()->route('supp_registration_fee', Crypt::encrypt($enrollment))->with('message', 'Your request to admin for Transaction has been made successfully!');
        }
    }

    public function supp_sendSMSMessageForFeePaid(Request $request)
    {
        $students = Supplementary::where('locksumbitted', "=", 1)
            ->where('enrollment', "!=", NULL)
            ->where('submitted', "=", NULL)
            ->where('fee_paid_amount', "=", NULL)
            ->where('student_id', "=", 192)
            ->pluck('student_id');

        foreach ($students as $k => $student_id) {
            $this->_sendSupplementaryLockSubmittedMessage($student_id);
            echo ($k + 1) . $student_id . "<br>";
        }
        echo "Message sent to " . count($students) . " students.";
        die;
    }

    public function supp_verify_request(Request $request, $enrollment)
    {
        $eenrollment = $enrollment;
        $enrollment = Crypt::decrypt($enrollment);

        $exam_year = Config::get('global.form_supp_current_admission_session_id');
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

        if (@$exam_year) {
        } else {
            $exam_year = Config::get('global.form_supp_current_admission_session_id');
        }

        $student = Supplementary::Join('supp_student_fees', 'supp_student_fees.student_id', '=', 'supplementaries.student_id')
            ->where('supplementaries.enrollment', "=", $enrollment)
            ->where('supplementaries.exam_year', "=", $exam_year)
            ->where('supplementaries.exam_month', "=", $exam_month)
            ->first();
        if (!@$student) {
            return redirect()->back()->with('error', 'Failed! Student not found! Please try again');
        }
        $student_id = $student->student_id;
        if (!@$student->student_id) {
            if (@$validator) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! You entred details not matched with us! Please try again');
            }
            return redirect()->back()->with('error', 'Failed! You entred details not matched with us! Please try again');
        }
        $challan_tid = Supplementary::where('challan_tid', "!=", null)
            ->where('student_id', $student_id)
            ->where('exam_year', $exam_year)
            ->where('exam_month', $exam_month)
            ->count();

        if (@$student->locksumbitted && @$student->locksubmitted_date) {
        } else {
            return redirect()->route('supp_admission_fee_payment')->with('error', 'Failed! Your admission form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay fees!');
        }

        if ($challan_tid > 0) {
            return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment))->with('error', 'Failed! Invalid request access! Challan number already present with this request.');
        }
        $SuppErequests = SuppErequest::where('student_id', $student_id)
            // ->whereIn('rtype', [1,2])
            // ->whereIn('status', [1,7])
            ->whereIn('status', [0, 2])
            ->get();

        if (empty($SuppErequests)) {
            return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment))->with('error', 'Failed! Invalid request access! Still not found any request with your payment request.');
        } else {
            $found = false;
            foreach ($SuppErequests as $SuppErequest) {
                if (!$found) {
                    $found = $this->_verifyBySuppErequestId($SuppErequest->id);
                }
            }

            if ($found == false) {
                if ($found == false) {
                    if (Auth::check()) {
                        $user_id = Auth::user()->id;
                        $role_id = Session::get('role_id');
                        if (!empty($user_id)) {
                            return redirect()->route('supp_listing_payment_issues')->with('error', 'Amount not verified at payment gatway!');
                        }
                    }
                    return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment))->with('info', 'Your Transaction has not been verified yet or there is no transaction found to verify!');
                }
                return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment));
            }
            if (Auth::check()) {
                $user_id = Auth::user()->id;
                $role_id = Session::get('role_id');
                if (!empty($user_id)) {
                    return redirect()->route('supp_listing_payment_issues')->with('success', 'Your Transaction has been made successfully!');;
                }
            }
            return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment))->with('success', 'Your Transaction has been made successfully!');
        }
    }

    public function testrjsupp_verify_request(Request $request, $enrollment)
    {
        $eenrollment = $enrollment;
        $enrollment = Crypt::decrypt($enrollment);

        $exam_year = Config::get('global.form_supp_current_admission_session_id');
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

        if (@$exam_year) {
        } else {
            $exam_year = Config::get('global.form_supp_current_admission_session_id');
        }

        $student = Supplementary::Join('supp_student_fees', 'supp_student_fees.student_id', '=', 'supplementaries.student_id')
            ->where('supplementaries.enrollment', "=", $enrollment)
            ->where('supplementaries.exam_year', "=", $exam_year)
            ->where('supplementaries.exam_month', "=", $exam_month)
            ->first();
        if (!@$student) {
            return redirect()->back()->with('error', 'Failed! Student not found! Please try again');
        }
        $student_id = $student->student_id;
        if (!@$student->student_id) {
            if (@$validator) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! You entred details not matched with us! Please try again');
            }
            return redirect()->back()->with('error', 'Failed! You entred details not matched with us! Please try again');
        }
        $challan_tid = Supplementary::where('challan_tid', "!=", null)
            ->where('student_id', $student_id)
            ->where('exam_year', $exam_year)
            ->where('exam_month', $exam_month)
            ->count();

        if (@$student->locksumbitted && @$student->locksubmitted_date) {
        } else {
            return redirect()->route('supp_admission_fee_payment')->with('error', 'Failed! Your admission form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay fees!');
        }

        if ($challan_tid > 0) {
            return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment))->with('error', 'Failed! Invalid request access! Challan number already present with this request.');
        }
        $SuppErequests = SuppErequest::where('student_id', $student_id)
            // ->whereIn('rtype', [1,2])
            // ->whereIn('status', [1,7])
            ->whereIn('status', [0, 2])
            ->get();

        if (empty($SuppErequests)) {
            return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment))->with('error', 'Failed! Invalid request access! Still not found any request with your payment request.');
        } else {
            $found = false;
            foreach ($SuppErequests as $SuppErequest) {
                if (!$found) {
                    $found = $this->_testrjverifyBySuppErequestId($SuppErequest->id);
                }
            }

            if ($found == false) {
                if ($found == false) {
                    if (Auth::check()) {
                        $user_id = Auth::user()->id;
                        $role_id = Session::get('role_id');
                        if (!empty($user_id)) {
                            return redirect()->route('supp_listing_payment_issues')->with('error', 'Amount not verified at payment gatway!');
                        }
                    }
                    return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment))->with('info', 'Your Transaction has not been verified yet or there is no transaction found to verify!');
                }
                return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment));
            }
            if (Auth::check()) {
                $user_id = Auth::user()->id;
                $role_id = Session::get('role_id');
                if (!empty($user_id)) {
                    return redirect()->route('supp_listing_payment_issues')->with('success', 'Your Transaction has been made successfully!');;
                }
            }
            return redirect()->route('supp_registration_fee', Crypt::encrypt($student->enrollment))->with('success', 'Your Transaction has been made successfully!');
        }
    }

    public function _testrjverifyBySuppErequestId($id)
    {
        $found = false;
        $SuppErequest = SuppErequest::where('id', $id)->first();

        if (!empty($SuppErequest)) {
            $fld = "id";
            $$fld = $SuppErequest->$fld;
            $fld = "amount";
            $AMOUNT = $application_fee = $$fld = $SuppErequest->$fld;
            // $exam_year = Config::get('global.current_admission_session_id');
            $exam_year = Config::get('global.form_supp_current_admission_session_id');
            $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

            $fld = "student_id";
            $$fld = $SuppErequest->$fld;
            $academicyear_id = config("global.admission_academicyear_id");

            $student_details = Supplementary::where('student_id', $student_id)
                ->where('exam_year', $exam_year)
                ->where('exam_month', $exam_month)
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
            $supplementary_id = $SuppErequest->supplementary_id;
            $oldPRN = $REQUESTID = $academicyear_id . '' . 'S' . $student_id . 'S' . $erequest_id;

            $PRN = $academicyear_id . $exam_month . 'S' . $erequest_id . 'S' . $supplementary_id;

            $fld = "verifyUrl";
            $$fld = config("global.Emitra_" . $EmitraConfigEnvironment . "_" . $fld);


            $encrypted_fields = array("MERCHANTCODE" => "$MERCHANTCODE", "SERVICEID" => "$SERVICEID", "PRN" => "$PRN", "AMOUNT" => "$AMOUNT");

            $encryption_key = $ENCKEY;

            $res1 = $this->_invokePostAPINewPg($verifyUrl, json_encode($encrypted_fields));

            if ($res1['status'] == 1) {
                dd($res1);
                $output = json_decode($res1['json_output'], true);
                if (isset($output['data']['STATUS'])) {
                    $json_output = $output['data'];
                    $encryptedStr = $json_output['ENCDATA'];
                    $string = $this->_paymentdecrypt($encryptedStr, $encryption_key);
                    $response = $this->_paymentgetresponse($string);
                    $fields = $response;
                    if ($json_output['STATUS'] == 'SUCCESS') {
                        if (@$response['RECEIPTNO']) {
                            if (empty($student_details->challan_tid)) {
                                $updateSuppErequest = SuppErequest::find($erequest_id);
                                $updateSuppErequest->response = $SuppErequestSave['supp_response'] = $string;
                                $updateSuppErequest->status = $SuppErequestSave['status'] = 1;
                                $updateSuppErequest->prn = $SuppErequestSave['prn'] = $PRN;
                                $updateSuppErequest->challan_tid = $SuppErequestSave['challan_tid'] = $fields['RECEIPTNO'];
                                $updateSuppErequest->student_id = $SuppErequestSave['student_id'] = $fields['UDF1'];
                                $updateSuppErequest->transaction_id = $SuppErequestSave['transaction_id'] = $fields['TRANSACTIONID'];
                                $updateSuppErequest->save();

                                $currentDateTime = date('Y-m-d H:i:s');
                                $updateSupplementaryStudent = Supplementary::where('student_id', $student_id)
                                    ->where('exam_year', $exam_year)
                                    ->where('exam_month', $exam_month)
                                    ->first();
                                $updateSupplementaryStudent->challan_tid = $fields['RECEIPTNO'];
                                $updateSupplementaryStudent->application_fee_date = $currentDateTime;
                                $updateSupplementaryStudent->submitted = $currentDateTime;
                                $updateSupplementaryStudent->fee_status = 1;
                                $updateSupplementaryStudent->is_eligible = 0;
                                $updateSupplementaryStudent->is_aicenter_verify = 2;
                                $updateSupplementaryStudent->is_department_verify = 1;
                                $updateSupplementaryStudent->fee_paid_amount = $fields['AMOUNT'];
                                $updateSupplementaryStudent->save();

                                $paymentIssueUpdate = SuppPaymentIssue::where('student_id', $student_id)->first();
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
                            $updateSuppErequest = SuppErequest::find($erequest_id);
                            $updateSuppErequest->response = $SuppErequestSave['supp_response'] = $string;
                            $updateSuppErequest->status = $SuppErequestSave['status'] = 2;
                            $updateSuppErequest->save();
                        }
                    }
                }
            }
        }
        return $found;
    }

}