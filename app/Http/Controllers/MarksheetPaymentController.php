<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Exports\MarksheetlistingPaymentExlExport;
use App\Models\MarksheetErequest;
use App\Models\MarksheetMigrationRequest;
use App\Models\MarksheetPaymentIssue;
use App\Models\MasterRevalStudentFee;
use App\Models\Registration;
use App\Models\RevalErequest;
use App\Models\RevalPaymentIssue;
use App\Models\RevalStudent;
use App\Models\Student;
use App\Models\SuppStudentFee;
use Auth;
use Cache;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Redirect;
use Response;
use Route;
use Session;
use Validator;

class MarksheetPaymentController extends Controller
{
    private $request;

    public function __construct(request $request)
    {
        $this->request = $request;
    }

    public function marksheet_listing_payment_issues(Request $request)
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
        $title = "Marksheet Payment Issues Report";
        $table_id = "Payment_Issue_Report";
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
                'url' => 'MarksheetdownloadApplicationExl',
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
                'dbtbl' => 'marksheet_payment_issues'
            ),
            array(
                "lbl" => "Fee Amount",
                'fld' => 'total_fees',
                'input_type' => 'text',
                'placeholder' => "Fee Amount",
                'dbtbl' => 'marksheet_migration_requests',
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
                'fld' => 'total_fees',
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
        $aiCenters = $custom_component_obj->getAiCenters();
        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
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
                'fld' => 'marksheet_verify_request',
                'icon' => '<i class="material-icons" title="Click here to Verify.">check</i>',
                'fld_url' => '../marksheet_payments/marksheet_verify_request/#enrollment#'
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
        $master = $custom_component_obj->MarksheetPaymentIssuesData($formId);
        return view('marksheet_payment.marksheet_listing_payment_issues', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }


    public function marksheet_revised_duplicate_fee_payment(Request $request)
    {
        $searchBoxClass = "show";
        $student = array();
        $page_title = 'Marksheet Revised Duplicate  Form Fee Payment';
        $model = "Student";
        // $showSuppStatus =$this->_getCheckAllowToCheckSupp();
        //if(!$showSuppStatus){
        //return redirect()->route("landing")->with('error', 'Invalid Access!');
        //}
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
            $combo_name = 'reval_exam_year';
            $reval_exam_year = $this->master_details($combo_name);
            $reval_exam_year = $reval_exam_year[1];
            $combo_name = 'reval_exam_month';
            $reval_exam_month = $this->master_details($combo_name);
            $reval_exam_month = $reval_exam_month[1];

            $RevalStudentIdArr = RevalStudent::where('student_id', $student_id)->where('exam_year', '=', $reval_exam_year)->where('exam_month', '=', $reval_exam_month)->latest('id')->first('id');
            $supp_id = null;
            if (!empty(@$RevalStudentIdArr->id)) {
                $supp_id = @$RevalStudentIdArr->id;
            }
            $student = RevalStudent::leftJoin('reval_student_fees', 'reval_student_fees.student_id', '=', 'supplementaries.student_id')
                //->where('supplementaries.enrollment', "=",$enrollment)
                ->where('supplementaries.student_id', "=", $student_id)
                // ->where('supplementaries.dob', "=",$dob)
                ->where('supplementaries.id', "=", $supp_id)
                ->whereNull('supplementaries.challan_tid')
                ->first(['reval_student_fees.total', 'supplementaries.*']);
            //dd($student);
            if (!@$student->id) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! You entred details not matched with us! Please try again');
            }

            $student_id = $student->id;
            if (@$student->locksumbitted && @$student->locksubmitted_date) {

            } else {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! Your supplementary form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay RevalStudent fees!');
            }

            $application_fee = 0;
            if ($student->total_fees) {
                $application_fee = $student->total_fees;
            }


            if ($application_fee <= 0) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! Please contact to AI Centre.Your admission fees is zero(0)!');
            }
            return redirect()->route('reval_registration_fee', Crypt::encrypt($student->enrollment))->with('message', 'Student has been found successfully!');
        }
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        return view('reval_payment.reval_admission_fee_payment', compact('page_title', 'searchBoxClass', 'student', 'model', 'gender_id'));
    }

    public function marksheet_revsied_duplicate_registration_fee(Request $request, $enrollment)
    {
        $custom_component_obj = new CustomComponent;
        //$formOpenAllowOrNot = $custom_component_obj->checkAnySuppEntryAllowOrNot();
        $role_id = @Session::get('role_id');
        if (@$role_id) {
        } else {
            $errMsg = 'आगे बढ़ने के लिए कृपया दोबारा लॉग इन करें।(Kindly log in again to proceed.)';
            return redirect()->route('landing')->with('error', $errMsg);
        }

        $errMsg = null;
        $table = $model = "Student";
        $page_title = 'संशोधित/डुप्लीकेट मार्कशीट/माइग्रेशन के लिए भुगतान जमा करें(Submit Payment for Revised/Duplicate Marksheet/Migration)';
        $eenrollment = $enrollment;
        $enrollment = Crypt::decrypt($enrollment);
        $student = array();

        $model = "Student";
        $fld = "enrollment";
        $custom_component_obj = new CustomComponent;
        $combo_name = 'reval_exam_year';
        $reval_exam_year = $this->master_details($combo_name);
        $reval_exam_year = $reval_exam_year[1];
        $combo_name = 'reval_exam_month';
        $reval_exam_month = $this->master_details($combo_name);
        $reval_exam_month = $reval_exam_month[1];

        $studentDetails = Student::where('students.enrollment', $enrollment)->first('id');
        $student_id = @$studentDetails->id;
        $marksheetDetails = MarksheetMigrationRequest::where('student_id', $student_id)
            ->orderBy('id', 'desc')->first();
        if (empty($marksheetDetails)) {
            return back()->with('error', "Data Not found");
        }
        $marksheet_id = $marksheetDetails->id;
        $student = Student::where('students.id', $student_id)
            ->with('marksheet_migration_requests', function ($query) use ($marksheet_id) {
                $query->where('id', '=', $marksheet_id);
            })
            ->first();
        if (!@$student->id) {
            if (@$validator) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! You entred details not matched with us! Please try again');
            }
            return redirect()->route('reval_admission_fee_payment')->with('error', 'Failed! Your supplementary form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay fees!');
        }
        $estudent_id = Crypt::encrypt($student_id);

        if (@$student->marksheet_migration_requests->locksumbitted && @$student->marksheet_migration_requests->locksubmitted_date) {
        } else {
            return redirect()->route('reval_admission_fee_payment')->with('error', 'Failed! Your reval form still not locked and submitted. Please first lock and submit your form.Hence,You are not allowed to pay fees!');
        }

        $application_fee = 0;
        if (@$student->marksheet_migration_requests->total_fees) {
            $application_fee = $student->marksheet_migration_requests->total_fees;
        }
        if ($application_fee <= 0) {
            if (isset($validator)) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! Your reval fees is zero(0) hence does not need to pay reval fees!');
            } else {
                return redirect()->route('reval_admission_fee_payment')->with('error', 'Failed! Your reval fees is zero(0) hence does not need to pay reval fees!');
            }
        }

        $isAlreadyRaisedRequest = MarksheetErequest::where('marksheet_migration_requests_id', $marksheet_id)
            ->whereIn('status', [0, 2])
            ->count();


        $erequestCount = MarksheetErequest::where('marksheet_migration_requests_id', $marksheet_id)
            ->where('rtype', 1)
            ->whereIn('status', [1, 7])
            ->count();

        $issueCount = MarksheetPaymentIssue::where('student_id', $student_id)->count();

        $paymentIssueDetails = array();
        if ($issueCount > 0) {
            $paymentIssueDetails = MarksheetPaymentIssue::where('student_id', $student_id)->get();
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
                $this->reval_before_payment_verify_request($marksheet_id);
            }

            $EmitraConfigEnvironment = config("global.Emitra_environment");
            $SERVICEID = config("global.Emitra_adm_only_payment_service");

            $academicyear_id = $reval_exam_year;
            $erequestToBeSave = ['service_id' => $SERVICEID, 'marksheet_migration_requests_id' => $marksheet_id, 'student_id' => $student_id, 'emitra_id' => null, 'amount' => $application_fee, 'rtype' => 1, 'status' => 0];
            $RevalErequestSaved = MarksheetErequest::create($erequestToBeSave);
            $erequest_id = $RevalErequestSaved->id;
            //$REQUESTID = $academicyear_id . '' . 'S' . $student_id . 'S' . $erequest_id;
            // $CONSUMERKEY = 'RSOS-' . '-SUPP-'. $academicyear_id . '-SUPP-ROH-' . $student_id;
            $REQUESTID = $erequest_id . 'S' . $marksheet_id . 'S' . $student_id;

            $CONSUMERKEY = 'U1-RSOS-MARK-' . $student_id . 'S' . $marksheet_id;
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

            $SUCCESSURL = $FAILUREURL = route('marksheet_response'); //payment_response.php
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

            Log::info('Online Marksheet PaymentRequest:' . $fields);

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

        return view('marksheet_payment.marksheet_registration_fee', compact('stream_id', 'estudent_id', 'eenrollment', 'adm_types', 'course', 'enrollment', 'page_title', 'student', 'isAlreadyRaisedRequest', 'erequestCount', 'issueCount', 'categorya', 'paymentIssueDetails', 'student', 'model', 'gender_id', 'application_fee', 'marksheet_id'));
    }

    public function reval_before_payment_verify_request($mmr_id = null)
    {
        $combo_name = 'reval_exam_year';
        $reval_exam_year = $this->master_details($combo_name);
        $reval_exam_year = $reval_exam_year[1];
        $combo_name = 'reval_exam_month';
        $reval_exam_month = $this->master_details($combo_name);
        $reval_exam_month = $reval_exam_month[1];

        $student = RevalStudent::where('reval_students.id', "=", $reval_id)
            ->where('reval_students.exam_year', "=", $reval_exam_year)
            ->where('reval_students.exam_month', "=", $reval_exam_month)
            ->first();

        if (!@$student->student_id) {
            if (@$validator) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! You entred details not matched with us! Please try again');
            }
            return redirect()->back()->with('error', 'Failed! You entred details not matched with us! Please try again');
        }
        $student_id = $student->student_id;

        $challan_tid = RevalStudent::where('reval_students.id', "=", $reval_id)
            ->where('exam_year', "=", $reval_exam_year)
            ->where('exam_month', "=", $reval_exam_month)
            ->where('challan_tid', "!= ", "")
            ->count();

        if (@$student->locksumbitted && @$student->locksubmitted_date) {
        } else {
            return redirect()->route('reval_admission_fee_payment')->with('error', 'Failed! Your admission form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay fees!');
        }

        if ($challan_tid != 0) {
            return redirect()->route('reval_registration_fee', Crypt::encrypt($student->enrollment))->with('error', 'Failed! Invalid request access!challan number already present with this request.');
        }

        $RevalErequests = RevalErequest::where('student_id', $student_id)
            ->where('reval_id', $reval_id)
            ->whereIn('rtype', [1, 2])
            ->whereIn('status', [1, 7])
            ->get();

        if (!@$RevalErequests) {
            return redirect()->route('reval_registration_fee', Crypt::encrypt($student->enrollment))->with('error', 'Failed! Invalid request access!challan number already present with this request.');
        } else {
            $found = null;
            if (!empty($RevalErequests) && count($RevalErequests) > 0) {
                foreach ($RevalErequests as $RevalErequest) {
                    $found = $this->_verifyByRevalErequestId(@$RevalErequest->id);
                }
            }

            if ($found == false) {
                if ($found == false) {
                    if (Auth::check()) {
                        $user_id = Auth::user()->id;
                        $role_id = Session::get('role_id');
                        return redirect()->route('reval_listing_payment_issues');
                    }
                    return redirect()->route('reval_registration_fee', Crypt::encrypt($student->enrollment))->with('info', 'Your Transaction has not been verified yet or there is no transaction found to verify!');
                }
                return redirect()->route('reval_registration_fee', Crypt::encrypt($student->enrollment));
            }

            if (Auth::check()) {
                $user_id = Auth::user()->id;
                $role_id = Session::get('role_id');
                return redirect()->route('reval_listing_payment_issues')->with('success', 'Your Transaction has been made successfully!');
            }

            return redirect()->route('reval_registration_fee', Crypt::encrypt($student->enrollment))->with('success', 'Your Transaction has been made successfully!');
        }
    }

    public function marksheet_response(Request $request)
    {
        $responoseData = $request->all();
        $enrollment = null;
        if (isset($responoseData['STATUS'])) {
            $STATUS = $responoseData['STATUS'];
            $textToDecrypt = $responoseData['ENCDATA'];
            Log::info('Online Marksheet Payment STATUS :' . $STATUS);
            Log::info('Online Marksheet Payment encrypted_data Response :' . $textToDecrypt);

            //$academicyear_id = config("global.admission_academicyear_id");
            $EmitraConfigEnvironment = config("global.Emitra_environment");
            $SERVICEID = config("global.Emitra_adm_only_payment_service");

            Log::info('Online Marksheet Payment STATUS :' . $STATUS);
            $fld = "ENCKEY";
            $encryption_key = $$fld = config("global.EmitraService_" . $SERVICEID . "_" . $fld);

            $encryptedStr = $responoseData['ENCDATA'];
            $decrypted_data = $this->_paymentdecrypt($encryptedStr, $encryption_key);
            $fields = $this->_paymentgetresponse($decrypted_data);
            Log::info('Online Marksheet PaymentPost Data :' . json_encode($responoseData));
            Log::info('Online Marksheet Paymentdecrypted_data Response :' . $decrypted_data);
            $masterDetails = explode('S', $fields['PRN']);
            $marksheet_id = @$masterDetails[1];
            $marksheetErequestData = MarksheetErequest::where('marksheet_migration_requests_id', $marksheet_id)->first();
            $student_id = @$marksheetErequestData->student_id;
            $studentMaster = Student::where('students.id', $student_id)->first();
            $fld = "enrollment";
            if (@$studentMaster->$fld) {
                $$fld = $studentMaster->$fld;
            }
            if ($responoseData['STATUS'] == 'SUCCESS') {
                if ($STATUS == 'SUCCESS' || $STATUS == 'PENDING') {
                    $masterDetails = explode('S', $fields['PRN']);
                    $marksheetStudentIdArr = MarksheetMigrationRequest::where('id', $marksheet_id)->first('student_id');
                    $student_id = @$marksheetStudentIdArr->student_id;
                    $REQUESTID = $transaction_id = $erequest_id = $masterDetails[0];
                    $SERVICEID = config("global.Emitra_adm_only_payment_service");
                    $marksheetErequest = MarksheetErequest::where('id', $transaction_id)->first();
                    if (@$marksheetErequest) {
                        if ($STATUS == 'SUCCESS') {
                            $updateMarksheetErequest = MarksheetErequest::find($REQUESTID);
                            $updateMarksheetErequest->student_id = $student_id = $fields['UDF1'];
                            $updateMarksheetErequest->amount = $MarksheetErequestSave['amount'] = $fields['AMOUNT'];
                            $updateMarksheetErequest->challan_tid = $MarksheeetErequestSave['challan_tid'] = $fields['RECEIPTNO'];
                            $updateMarksheetErequest->service_id = $MarksheetErequestSave['service_id'] = $SERVICEID;
                            $updateMarksheetErequest->transaction_id = $MarksheetErequestSave['transaction_id'] = $fields['TRANSACTIONID'];
                            $updateMarksheetErequest->prn = $MarksheetErequestSave['prn'] = $fields['PRN'];
                            $updateMarksheetErequest->status = $MarksheetErequestSave['status'] = 1;
                            $updateMarksheetErequest->response = $MarksheetErequestSave['response'] = $decrypted_data;
                            $updateMarksheetErequest->ENCDATA = $MarksheetErequestSave['ENCDATA'] = $encryptedStr;
                            $updateMarksheetErequest->id = $MarksheetErequestSave['id'] = $REQUESTID;

                            $updateMarksheetErequest->save();
                            $updateMarksheetStudentStudent = MarksheetMigrationRequest::where('id', '=', $marksheet_id)
                                ->first();
                            $updateMarksheetStudentStudent->challan_tid = $fields['RECEIPTNO'];
                            $currentDateTime = date('Y-m-d H:i:s');
                            $updateMarksheetStudentStudent->application_fee_date = $currentDateTime;
                            $updateMarksheetStudentStudent->submitted = $currentDateTime;
                            $updateMarksheetStudentStudent->fee_status = 1;
                            $updateMarksheetStudentStudent->fee_paid_amount = $fields['AMOUNT'];
                            $updateMarksheetStudentStudent->save();
                            $paymentIssueUpdate = MarksheetPaymentIssue::where('student_id', $student_id)->first();
                            if (@$paymentIssueUpdate) {
                                $paymentIssueUpdate->status = 1;
                                $paymentIssueUpdate->is_archived = 1;
                                $paymentIssueUpdate->save();
                            }
                            if (@$student_id) {
                                //$this->_sendRevalPaymentSubmitMessage($student_id);
                            }
                            //$status = $this->reLoginCurrentStudentAfterPayment($student_id);
                            /* if($status){
                                 return redirect()->route('corr_marksheet_previews',Crypt::encrypt($updateMarksheetStudentStudent->id))->with('message', 'Your payment has successfully completed.');
                             }else{
                                 return redirect()->route('marksheet_revsied_duplicate_registration_fee',Crypt::encrypt($studentMaster->enrollment))->with('message','Your Transaction has been made successfully!');
                             }*/
                            return redirect()->route('marksheet_revsied_duplicate_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('message', 'Your Transaction has been made successfully!');
                        } else {
                            $updateMarksheetErequestElseCase = MarksheetErequest::find($REQUESTID);
                            $updateMarksheetErequestElseCase->response = $MarksheetErequestSave['response'] = $decrypted_data;
                            $updateMarksheetErequestElseCase->ENCDATA = $MarksheetErequestSave['ENCDATA'] = $encryptedStr;
                            $updateMarksheetErequestElseCase->status = $MarksheetErequestSave['status'] = 2;
                            $updateMarksheetErequestElseCase->marksheet_migration_requests_id = $MarksheetErequestSave['id'] = $REQUESTID;
                            $updateMarksheetErequestElseCase->save();
                            $status = $this->reLoginCurrentStudentAfterPayment($student_id);
                            if ($status) {
                                return redirect()->route('corr_marksheet_previews', Crypt::encrypt($$updateMarksheetErequestElseCase->marksheet_migration_requests_id))->with('message', 'Your payment has successfully completed.');
                            } else {
                                return redirect()->route('marksheet_revsied_duplicate_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('message', 'Your Transaction has been made successfully!');
                            }
                            return redirect()->route('marksheet_revsied_duplicate_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('error', 'Your Transaction is pending from bank!');
                        }
                    }
                } else {
                    return redirect()->route('marksheet_revsied_duplicate_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('error', $fields['RESPONSEMESSAGE']);
                }
            } else {
                $msg = (isset($fields['RESPONSEMESSAGE'])) ? $fields['RESPONSEMESSAGE'] : 'There was some error, Please try again!';
                return redirect()->route('marksheet_revsied_duplicate_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('error', $msg);
            }
        } else {
            // dd('test');
            $updateMarksheetErequestElseCase = MarksheetErequest::find($REQUESTID);
            $updateMarksheetErequestElseCase->response = $MarksheetErequestSave['response'] = $decrypted_data;
            $updateMarksheetErequestElseCase->status = $MarksheetErequestSave['status'] = 2;
            $updateMarksheetErequestElseCase->marksheet_migration_requests_id = $MarksheetErequestSave['id'] = $REQUESTID;
            $updateMarksheetErequestElseCase->save();
            return redirect()->route('marksheet_revsied_duplicate_registration_fee', Crypt::encrypt($studentMaster->enrollment))->with('info', 'Your Transaction is pending from bank!');
        }
        return redirect()->route('marksheet_revsied_duplicate_registration_fee', Crypt::encrypt($studentMaster->enrollment));
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

    public function marksheet_raise_request(Request $request, $enrollment)
    {
        $enrollment = Crypt::decrypt($enrollment);
        $student = MarksheetMigrationRequest::where('enrollment', $enrollment)->orderBy('id', 'desc')->first();
        $mmar_id = $student->id;
        $student_id = $student->student_id;
        $isAlreadyRaisedRequest = MarksheetPaymentIssue::where('marksheet_migration_requests_id', $mmar_id)->count();
        if ($isAlreadyRaisedRequest > 0) {
            return redirect()->route('reval_registration_fee', Crypt::encrypt($enrollment))->with('error', 'Request has been already made by you.');
        }

        $Countchallan_tid = MarksheetMigrationRequest::where('id', "=", $mmar_id)
            ->where('challan_tid', "!=", null)->count();

        if (@$Countchallan_tid) {
            return redirect()->route('reval_registration_fee', Crypt::encrypt($enrollment))->with('error', 'Your challan number already present with us!');
        }

        $RevalErequests = MarksheetErequest::
        where('student_id', $student_id)
            ->whereIn('rtype', [1, 2])
            ->whereNotIn('status', [1, 7])
            ->get();

        if (empty($RevalErequests)) {
            return redirect()->route('marksheet_revsied_duplicate_registration_fee', Crypt::encrypt($enrollment))->with('error', 'Invalid request access!');
        } else {
            $paymentIssueToBeSave = ['marksheet_migration_requests_id' => $mmar_id, 'student_id' => $student_id, 'enrollment' => $enrollment, 'is_archived' => 0, 'status' => 0];
            MarksheetPaymentIssue::create($paymentIssueToBeSave);
            return redirect()->route('marksheet_revsied_duplicate_registration_fee', Crypt::encrypt($enrollment))->with('message', 'Your request to admin for Transaction has been made successfully!');
        }
    }

    public function reval_sendSMSMessageForFeePaid(Request $request)
    {
        $students = RevalStudent::where('locksumbitted', "=", 1)
            ->where('enrollment', "!=", NULL)
            ->where('submitted', "=", NULL)
            ->where('fee_paid_amount', "=", NULL)
            ->where('student_id', "=", 192)
            ->pluck('student_id');

        foreach ($students as $k => $student_id) {
            $this->_sendRevalStudentLockSubmittedMessage($student_id);
            echo ($k + 1) . $student_id . "<br>";
        }
        echo "Message sent to " . count($students) . " students.";
        die;
    }

    public function marksheet_verify_request(Request $request, $enrollment)
    {
        $eenrollment = $enrollment;
        $enrollment = Crypt::decrypt($enrollment);
        $combo_name = 'reval_exam_year';
        $reval_exam_year = $this->master_details($combo_name);
        $reval_exam_year = $reval_exam_year[1];
        $combo_name = 'reval_exam_month';
        $reval_exam_month = $this->master_details($combo_name);
        $reval_exam_month = $reval_exam_month[1];

        if (@$reval_exam_year) {
        } else {
            $combo_name = 'reval_exam_year';
            $reval_exam_year = $this->master_details($combo_name);
            $reval_exam_year = $reval_exam_year[1];
        }

        $student = MarksheetMigrationRequest::where('enrollment', "=", $enrollment)->
        where('marksheet_migration_status', '0')->
        orderBy('id', 'desc')
            ->first();
        if (!@$student) {
            return redirect()->back()->with('error', 'Failed! Student not found! Please try again');
        }
        $student_id = $student->student_id;
        $mmr_id = $student->id;
        if (!@$student->student_id) {
            if (@$validator) {
                return redirect()->back()->withErrors($validator)->with('error', 'Failed! You entred details not matched with us! Please try again');
            }
            return redirect()->back()->with('error', 'Failed! You entred details not matched with us! Please try again');
        }

        $challan_tid = MarksheetMigrationRequest::where('challan_tid', "!=", null)
            ->where('id', $mmr_id)->count();

        if (@$student->locksumbitted && @$student->locksubmitted_date) {
        } else {
            //return redirect()->route('reval_admission_fee_payment')->with('error', 'Failed! Your admission form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay fees!');
        }

        if ($challan_tid > 0) {
            return redirect()->route('marksheet_revsied_duplicate_registration_fee', Crypt::encrypt($student->enrollment))->with('error', 'Failed! Invalid request access! Challan number already present with this request.');
        }

        $marksheetErequests = MarksheetErequest::where('student_id', $student_id)
            ->where('marksheet_migration_requests_id', $mmr_id)
            // ->whereIn('rtype', [1,2])
            // ->whereIn('status', [1,7])
            ->whereIn('status', [0, 2])
            ->get();
        if (empty($marksheetErequests)) {
            return redirect()->route('marksheet_revsied_duplicate_registration_fee', Crypt::encrypt($student->enrollment))->with('error', 'Failed! Invalid request access! Still not found any request with your payment request.');
        } else {
            $found = false;
            foreach ($marksheetErequests as $RevalErequest) {
                if (!$found) {
                    $found = $this->_verifyByMarksheetErequestId($RevalErequest->id);
                }
            }

            if ($found == false) {

                if (Auth::check() && Auth::user()->id) {
                    $user_id = Auth::user()->id;
                    $role_id = Session::get('role_id');
                    if (!empty($user_id)) {
                        return redirect()->route('reval_listing_payment_issues')->with('error', 'Amount not verified at payment gatway!');
                    }
                }
                return redirect()->route('reval_registration_fee', Crypt::encrypt($student->enrollment))->with('info', 'Your Transaction has not been verified yet or there is no transaction found to verify!');
            }
            if (Auth::check() && Auth::user()->id) {
                $user_id = Auth::user()->id;
                $role_id = Session::get('role_id');
                if (!empty($user_id)) {
                    return redirect()->route('reval_listing_payment_issues')->with('success', 'Your Transaction has been made successfully!');;
                }
            }
            return redirect()->route('reval_registration_fee', Crypt::encrypt($student->enrollment))->with('success', 'Your Transaction has been made successfully!');
        }
    }

    public function _verifyByMarksheetErequestId($id)
    {
        $found = false;
        $marksheetErequest = MarksheetErequest::where('id', $id)->first();

        if (!empty($marksheetErequest)) {
            $fld = "id";
            $$fld = $marksheetErequest->$fld;
            $fld = "amount";
            $AMOUNT = $application_fee = $$fld = $marksheetErequest->$fld;
            // $reval_exam_year = Config::get('global.current_admission_session_id');

            $fld = "student_id";
            $$fld = $marksheetErequest->$fld;
            $student_details = MarksheetMigrationRequest::where('marksheet_migration_status', '0')->where('student_id', $student_id)->orderBy('id', 'desc')
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

            $mmr_id = $marksheetErequest->marksheet_migration_requests_id;
            $student_id = $marksheetErequest->student_id;
            $PRN = $erequest_id . 'S' . $mmr_id . 'S' . $student_id;
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
                                $updateMarksheetErequest = MarksheetErequest::find($erequest_id);
                                $updateMarksheetErequest->response = $MarksheetErequestSave['marksheet_response'] = $string;
                                $updateMarksheetErequest->status = $MarksheetErequestSave['status'] = 1;
                                $updateMarksheetErequest->is_eligible = $MarksheetErequestSave['is_eligible'] = 1;
                                $updateMarksheetErequest->prn = $MarksheetErequestSave['prn'] = $PRN;
                                $updateMarksheetErequest->challan_tid = $MarksheetErequestSave['challan_tid'] = $fields['RECEIPTNO'];
                                $updateMarksheetErequest->student_id = $MarksheetErequestSave['student_id'] = $fields['UDF1'];
                                $MarksheetErequestSave->transaction_id = $updateMarksheetErequest['transaction_id'] = $fields['TRANSACTIONID'];
                                $updateMarksheetErequest->save();

                                $currentDateTime = date('Y-m-d H:i:s');
                                $updateMarksheetStudentStudent = MarksheetMigrationRequest::where('student_id', $student_id)->orderBy('id', 'desc')
                                    ->first();
                                $updateMarksheetStudentStudent->challan_tid = $fields['RECEIPTNO'];
                                $updateMarksheetStudentStudent->application_fee_date = $currentDateTime;
                                $updateMarksheetStudentStudent->submitted = $currentDateTime;
                                $updateMarksheetStudentStudent->fee_status = 1;
                                //$updateRevalStudentStudent->is_eligible = 1;
                                $updateMarksheetStudentStudent->fee_paid_amount = $fields['AMOUNT'];
                                $updateMarksheetStudentStudent->save();

                                $paymentIssueUpdate = MarksheetPaymentIssue::where('student_id', $student_id)->first();
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
                            $updateMarksheetErequest = MarksheetErequest::find($erequest_id);
                            $updateMarksheetErequest->response = $marksheetErequestSave['reval_response'] = $string;
                            $updateMarksheetErequest->status = $marksheetErequestSave['status'] = 2;
                            $updateMarksheetErequest->save();
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

    public function testrjsupp_verify_request(Request $request, $enrollment)
    {
        $eenrollment = $enrollment;
        $enrollment = Crypt::decrypt($enrollment);

        $combo_name = 'reval_exam_year';
        $reval_exam_year = $this->master_details($combo_name);
        $reval_exam_year = $reval_exam_year[1];
        $combo_name = 'reval_exam_month';
        $reval_exam_month = $this->master_details($combo_name);
        $reval_exam_month = $reval_exam_month[1];

        if (@$reval_exam_year) {
        } else {
            $combo_name = 'reval_exam_year';
            $reval_exam_year = $this->master_details($combo_name);
            $reval_exam_year = $reval_exam_year[1];
        }

        $student = RevalStudent::Join('reval_student_fees', 'reval_student_fees.student_id', '=', 'supplementaries.student_id')
            ->where('supplementaries.enrollment', "=", $enrollment)
            ->where('supplementaries.exam_year', "=", $reval_exam_year)
            ->where('supplementaries.exam_month', "=", $reval_exam_month)
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
        $challan_tid = RevalStudent::where('challan_tid', "!=", null)
            ->where('student_id', $student_id)
            ->where('exam_year', $reval_exam_year)
            ->where('exam_month', $reval_exam_month)
            ->count();

        if (@$student->locksumbitted && @$student->locksubmitted_date) {
        } else {
            return redirect()->route('reval_admission_fee_payment')->with('error', 'Failed! Your admission form still not locked and submitted. Please first lock and submit your form.Hence you are not allowed to pay fees!');
        }

        if ($challan_tid > 0) {
            return redirect()->route('reval_registration_fee', Crypt::encrypt($student->enrollment))->with('error', 'Failed! Invalid request access! Challan number already present with this request.');
        }
        $RevalErequests = RevalErequest::where('student_id', $student_id)
            // ->whereIn('rtype', [1,2])
            // ->whereIn('status', [1,7])
            ->whereIn('status', [0, 2])
            ->get();

        if (empty($RevalErequests)) {
            return redirect()->route('reval_registration_fee', Crypt::encrypt($student->enrollment))->with('error', 'Failed! Invalid request access! Still not found any request with your payment request.');
        } else {
            $found = false;
            foreach ($RevalErequests as $RevalErequest) {
                if (!$found) {
                    $found = $this->_testrjverifyByRevalErequestId($RevalErequest->id);
                }
            }

            if ($found == false) {
                if ($found == false) {
                    if (Auth::check()) {
                        $user_id = Auth::user()->id;
                        $role_id = Session::get('role_id');
                        if (!empty($user_id)) {
                            return redirect()->route('reval_listing_payment_issues')->with('error', 'Amount not verified at payment gatway!');
                        }
                    }
                    return redirect()->route('reval_registration_fee', Crypt::encrypt($student->enrollment))->with('info', 'Your Transaction has not been verified yet or there is no transaction found to verify!');
                }
                return redirect()->route('reval_registration_fee', Crypt::encrypt($student->enrollment));
            }
            if (Auth::check()) {
                $user_id = Auth::user()->id;
                $role_id = Session::get('role_id');
                if (!empty($user_id)) {
                    return redirect()->route('reval_listing_payment_issues')->with('success', 'Your Transaction has been made successfully!');;
                }
            }
            return redirect()->route('reval_registration_fee', Crypt::encrypt($student->enrollment))->with('success', 'Your Transaction has been made successfully!');
        }
    }

    public function _testrjverifyByRevalErequestId($id)
    {
        $found = false;
        $RevalErequest = RevalErequest::where('id', $id)->first();

        if (!empty($RevalErequest)) {
            $fld = "id";
            $$fld = $RevalErequest->$fld;
            $fld = "amount";
            $AMOUNT = $application_fee = $$fld = $RevalErequest->$fld;
            // $reval_exam_year = Config::get('global.current_admission_session_id');
            $combo_name = 'reval_exam_year';
            $reval_exam_year = $this->master_details($combo_name);
            $reval_exam_year = $reval_exam_year[1];
            $combo_name = 'reval_exam_month';
            $reval_exam_month = $this->master_details($combo_name);
            $reval_exam_month = $reval_exam_month[1];

            $fld = "student_id";
            $$fld = $RevalErequest->$fld;
            $academicyear_id = config("global.admission_academicyear_id");

            $student_details = RevalStudent::where('student_id', $student_id)
                ->where('exam_year', $reval_exam_year)
                ->where('exam_month', $reval_exam_month)
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
            $supplementary_id = $RevalErequest->supplementary_id;
            $oldPRN = $REQUESTID = $academicyear_id . '' . 'S' . $student_id . 'S' . $erequest_id;

            $PRN = $academicyear_id . $reval_exam_month . 'S' . $erequest_id . 'S' . $supplementary_id;

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
                                $updateRevalErequest = RevalErequest::find($erequest_id);
                                $updateRevalErequest->response = $RevalErequestSave['reval_response'] = $string;
                                $updateRevalErequest->status = $RevalErequestSave['status'] = 1;
                                $updateRevalErequest->prn = $RevalErequestSave['prn'] = $PRN;
                                $updateRevalErequest->challan_tid = $RevalErequestSave['challan_tid'] = $fields['RECEIPTNO'];
                                $updateRevalErequest->student_id = $RevalErequestSave['student_id'] = $fields['UDF1'];
                                $updateRevalErequest->transaction_id = $RevalErequestSave['transaction_id'] = $fields['TRANSACTIONID'];
                                $updateRevalErequest->save();

                                $currentDateTime = date('Y-m-d H:i:s');
                                $updateRevalStudentStudent = RevalStudent::where('student_id', $student_id)
                                    ->where('exam_year', $reval_exam_year)
                                    ->where('exam_month', $reval_exam_month)
                                    ->first();
                                $updateRevalStudentStudent->challan_tid = $fields['RECEIPTNO'];
                                $updateRevalStudentStudent->application_fee_date = $currentDateTime;
                                $updateRevalStudentStudent->submitted = $currentDateTime;
                                $updateRevalStudentStudent->fee_status = 1;
                                $updateRevalStudentStudent->is_eligible = 1;
                                $updateRevalStudentStudent->is_aicenter_verify = 1;
                                $updateRevalStudentStudent->is_department_verify = 1;
                                $updateRevalStudentStudent->fee_paid_amount = $fields['AMOUNT'];
                                $updateRevalStudentStudent->save();

                                $paymentIssueUpdate = RevalPaymentIssue::where('student_id', $student_id)->first();
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
                            $updateRevalErequest = RevalErequest::find($erequest_id);
                            $updateRevalErequest->response = $RevalErequestSave['reval_response'] = $string;
                            $updateRevalErequest->status = $RevalErequestSave['status'] = 2;
                            $updateRevalErequest->save();
                        }
                    }
                }
            }
        }
        return $found;
    }


    public function MarksheetdownloadApplicationExl(Request $request, $type = "xlsx")
    {
        $application_exl_data = new MarksheetlistingPaymentExlExport;
        $filename = 'marksheet_listing_payment_issues' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }


}