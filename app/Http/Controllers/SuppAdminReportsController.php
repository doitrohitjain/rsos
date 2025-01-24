<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Helper\CustomHelper;
use App\Models\ExamSubject;
use App\Models\Registration;
use App\models\Student;
use App\Models\Supplementary;
use App\Models\SupplementarySubject;
use App\Models\SuppStudentFees;
use Auth;
use Config;
use DB;
use File;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use PDF;
use Redirect;
use Response;
use Route;
use Session;
use Validator;

class SuppAdminReportsController extends Controller
{
    function __construct()
    {
        //$this->middleware('permission:admission_report_student_applications', ['only' => ['student_applications']]);
        //$this->middleware('permission:admission_report_student_downloadapplicationexl', ['only' => ['downloadApplicationExl']]);
        //$this->middleware('permission:admission_report_student_downloadapplicationpdf', ['only' => //['downloadApplicationPdf']]);
        //$this->middleware('permission:admission_report_student_application_ai_center_wise_count', ['only' => ['student_application_ai_center_wise_count']]);
        //$this->middleware('permission:subject_wise_student_count', ['only' => ['subject_wise_student_count']]);
        //$this->middleware('permission:admission_report_toc_students_ai_center_wise_count', ['only' => ['toc_students_ai_center_wise_count']]);
    }


    public function supplementary_admin_student_applications(Request $request)
    {

        Session::put('selected_exam_month', "");
        Session::put('selected_exam_year', "");

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
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $district_list = $this->districtsByState();
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $role_id = Session::get('role_id');
        $aicenter_id_role = config("global.aicenter_id");
        $yes_no = $this->master_details('yesno');
        $title = "Supplementary Applicaiton Report";
        $table_id = "Admission_Report";
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
                'url' => 'downloadSupplementaryApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadSupplementaryApplicationPdf',
                'status' => false,
            ),
        );

        $locksumbitted = $this->master_details('yesno');
        // selected exam_year and exam_month		
        Session::put('selected_exam_month', $request->exam_month);
        // Session::put('selected_exam_year', $request->exam_year);
        $exam_year_for_session = CustomHelper::_get_selected_sessions();
        Session::put('selected_exam_year', $exam_year_for_session);


        $supp_exam_month = Config::get('global.supp_current_admission_exam_month');
        $temp_exam_month = @$exam_month->toArray();
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        if (@$temp_exam_month[2]) {
            if ($isAdminStatus == false) {
                if ($supp_exam_month == 1) {
                    unset($temp_exam_month[2]);
                }
            }
        }
        // if($request->exam_month == 1){
        // 	unset($exam_month[2]);
        // }else{
        // 	unset($exam_month[1]);
        // }
        // $locksumbitted = $yes_no;
        // unset ($locksumbitted[0]);

        $filters = array(
            array(
                "lbl" => "Start date",
                'fld' => 'start_date',
                'input_type' => 'datetime-local',
                'placeholder' => "Start Date",
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "End date",
                'fld' => 'end_date',
                'input_type' => 'datetime-local',
                'placeholder' => "End Date",
                'dbtbl' => 'supplementaries',
            ),

            array(
                "lbl" => "Payment Done",
                'fld' => 'challan_tid2',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Payment',
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Is Eligible",
                'fld' => 'is_eligible',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Is Eligible',
                'dbtbl' => 'supplementaries',
            ),

            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'supplementaries',
            ),

            array(
                "lbl" => "Total Fees",
                'fld' => 'total_fees2',
                'input_type' => 'text',
                'placeholder' => "Total fees",
                'dbtbl' => 'supplementaries',
            ),

            array(
                "lbl" => "Mobile Number",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Mobile Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Challan Number",
                'fld' => 'challan_tid',
                'input_type' => 'text',
                'placeholder' => "Challan Number",
                'dbtbl' => 'supplementaries',
            ),

            array(
                "lbl" => "Late Fees",
                'fld' => 'late_fees',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Late Fees',
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Total Fees",
                'fld' => 'total_fees',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Total Fees',
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Father Name",
                'fld' => 'father_name',
                'input_type' => 'text',
                'placeholder' => "Father Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Mother Name",
                'fld' => 'mother_name',
                'input_type' => 'text',
                'placeholder' => "Mother Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Jan Aadhar Number",
                'fld' => 'jan_aadhar_number',
                'input_type' => 'text',
                'placeholder' => "Jan Aadhar Number",
                'dbtbl' => 'applications',
            ),
            array(
                "lbl" => "Aadhar Number",
                'fld' => 'aadhar_number',
                'input_type' => 'text',
                'placeholder' => "Aadhar Number",
                'dbtbl' => 'applications',
            ),
            array(
                "lbl" => "Amount",
                'fld' => 'fee_paid_amount',
                'input_type' => 'text',
                'placeholder' => "Amount",
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
                'dbtbl' => 'supplementaries',
            ),
            /*array(
                "lbl" => "Stream ",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream Type',
                'dbtbl' => 'supplementaries',
            ),*/
            array(
                "lbl" => "Exam Month ",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month,
                'placeholder' => 'Exam Month',
                'dbtbl' => 'supplementaries',
                'required' => 'true',


            ),

            array(
                "lbl" => "Admission Type ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $locksumbitted,
                'placeholder' => 'Lock & Submit',
                'dbtbl' => 'supplementaries',
            ),
            array(
                "lbl" => "Are You From Rajasthan And Not",
                'fld' => 'are_you_from_rajasthan',
                'input_type' => 'select',
                'options' => $are_you_from_rajasthan,
                'search_type' => "text",
                'placeholder' => 'Are You From Rajasthan And Not',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Is Self Filled",
                'fld' => 'is_self_filled',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Is Self Filled',
                'dbtbl' => 'supplementaries',
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
                'placeholder' => "Enrollment Number"
            )
        , array(
                "lbl" => "AI Code",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'placeholder' => "AI Code"
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
                "lbl" => "Course ",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            /*array(
                "lbl" => "Stream ",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id
            ),*/
            array(
                "lbl" => "Exam Month ",
                'fld' => 'exam_month',
                'input_type' => 'select',
                'options' => $exam_month
            ),

            // array(
            // 	"lbl" => "Admission ",
            // 	'fld' => 'adm_type',
            // 	'input_type' => 'select',
            // 	'options' => $adm_types
            // ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            array(
                "lbl" => "Fees Amount",
                'fld' => 'total_fees'
            ),
            array(
                "lbl" => "Challan Number",
                'fld' => 'challan_tid'
            ),

            array(
                "lbl" => "Is Self Filled",
                'fld' => 'is_self_filled',
                'input_type' => 'select',
                'options' => $yes_no
            ),

            array(
                "lbl" => "Submitted",
                'fld' => 'submitted',
            ),

        );

        $conditions = array();
        //$conditions["supplementaries.locksumbitted"] = 1;
        $conditions["supplementaries.exam_year"] = CustomHelper::_get_selected_sessions();


        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            //if role is 59 ai center then get session aicode and then aicenter_detail_id put in condition
            $aicenter_user_id = Auth::user()->id;
            $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode(@$aicenter_user_id);
            $auth_user_id = @$aicenter_user_ids->ai_code;
            $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode(@$auth_user_id);
            $aicenter_mapped_data_conditions = $aicenter_mapped_data;


        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'supplementaries'
            );

        }

        if (in_array("Supplementary_student_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../supp_admission_report/supp_admin_preview_details/#student_id#'
                ),

            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../supp_admission_report/supp_admin_preview_details/#student_id#'
                ),
            );
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
        if ($request->all()) {
            $inputs = $request->all();
            $this->validate($request, [
                'exam_month' => 'required|numeric',
            ], [
                'exam_month.required' => 'Exam Month is required',
            ]);


            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (@$iv['fld'] == $k && $iv['fld'] == $k) {
                            if ($iv['fld'] == 'late_fees' && $inputs[$iv['fld']] == 1) {
                                $symbol = "!=";
                            } else {
                                $symbol = "=";
                            }

                            if ($iv['fld'] == 'challan_tid2' && $inputs[$iv['fld']] == 1) {
                                $symbol2 = "!=";
                            } elseif ($iv['fld'] == 'challan_tid2' && $inputs[$iv['fld']] == 0) {
                                $symbol2 = "=";
                            }

                            if ($iv['fld'] == 'total_fees' && $inputs[$iv['fld']] == 1) {
                                $symbols = "!=";
                            } else {
                                $symbols = "=";
                            }

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
        //dd($conditions);
        if ($role_id == $aicenter_id_role) {
            $master = $custom_component_obj->getSupplementaryApplicationData($formId, true, $aicenter_mapped_data_conditions);
        } else {
            $master = $custom_component_obj->getSupplementaryApplicationData($formId);
        }
        // dd($master);
        return view('admission_reports.supplementary_student_applications', compact('actions', 'tableData', 'master', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium'))->withInput($request->all());
    }

    public function supp_admin_preview_details(Request $request, $student_id)
    {
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $exam_year = Session::get("selected_exam_year");
        $exam_month = Session::get("selected_exam_month");


        $SupplementaryIdArr = Supplementary::where('student_id', $student_id)->where('exam_year', '=', $exam_year)->where('exam_month', '=', $exam_month)->latest('id')->first('id');


        $supp_id = null;
        if (!empty($SupplementaryIdArr->id)) {
            $supp_id = $SupplementaryIdArr->id;
        }

        $extraCondtion = " exam_year < " . $exam_year;
        if ($exam_month == 1) {
            $extraCondtion = " exam_year <= " . $exam_year;
        }

        $ExamSubjectYearArr = ExamSubject::where('student_id', $student_id)->whereRaw($extraCondtion)->orderBy('exam_year', 'DESC')->orderBy('exam_month', 'ASC')->first(['exam_year', 'exam_month']);


        $exam_year_latest = null;
        $exam_month_latest = null;
        if (!empty(@$ExamSubjectYearArr->exam_year)) {
            $exam_year_latest = @$ExamSubjectYearArr->exam_year;
            $exam_month_latest = @$ExamSubjectYearArr->exam_month;
        }

        $master = Student::where('students.id', $student_id)
            ->with('supplementary', function ($query) use ($exam_year, $exam_month) {
                $query->where('exam_year', '=', $exam_year)->where('exam_month', '=', $exam_month);
            })->with('supplementary_subject', function ($query) use ($supp_id) {
                $query->where('supplementary_id', '=', $supp_id);
            })->with('exam_subject', function ($query) use ($exam_year_latest, $exam_month_latest) {
                $query->where('exam_year', '=', $exam_year_latest);
                $query->where('exam_month', '=', $exam_month_latest);
            })
            ->with('Address')
            ->first();
        // $master = Student::with('exam_subject','Address')->where('students.id',$student_id)->first();


        if (empty($master->id)) {
            return redirect()->back()->with('error', "Enrollment Not Found Please Check the Enrollment.");
        }

        $table = $model = "Application";
        $page_title = 'Supplementary Application Preview';
        $documentErrors = null;

        $custom_component_obj = new CustomComponent;

        $suppFeeEnteredOrNot = false;
        /*
        if(@$master->exam_year && $master->exam_year <= 123){
            $suppFeeEnteredOrNot = true;
        }else{ */
        $suppFeeEnteredOrNot = $custom_component_obj->checkAdminSuppFeeEntredOrNot($student_id, $supp_id, $exam_year, $exam_month);

        /* } */

        if (!$suppFeeEnteredOrNot) {
            return redirect()->back()->with('error', 'Failed! Supplementary Fee does not saved.');
        }

        //$sub_code = $this->getSubjectCode($student_id);

        $supp_master = $this->getAdminSupplementaryStudentDetails($student_id, $exam_year, $exam_month);
        /* Replace string with X start */
        $custom_component_obj = new CustomComponent;
        $supp_master['personalDetails']['data']['mobile']['value'] = $custom_component_obj->_replaceTheStringWithX(@$supp_master['personalDetails']['data']['mobile']['value']);
        /* Replace string with X end */


        // @dd($supp_master);

        // $documentErrors = $this->getPendingDocuemntDetails($student_id);
        $documentErrors = null;

        $mastersuppcount = SupplementarySubject::where('student_id', $student_id)->where('is_additional_subject', '<>', '')->where('supplementary_id', $supp_id)->count();

        $exam_year_latest = null;
        $exam_month_latest = null;
        if (!empty(@$ExamSubjectYearArr->exam_year)) {
            $exam_year_latest = @$ExamSubjectYearArr->exam_year;
            $exam_month_latest = @$ExamSubjectYearArr->exam_month;
        }


        //$masterSuppExamYears = ExamSubject::where('student_id',$student_id)
        //->where('final_result','P',)->latest('exam_year')->first(['exam_year','exam_month']);
        $masterSuppExamYears = ExamSubject::where('student_id', $student_id)
            ->where('final_result', 'P')->where('exam_year', '=', $exam_year_latest)->where('exam_month', '=', $exam_month_latest)->first(['exam_year', 'exam_month']);

        $mastersupp = SupplementarySubject::where('student_id', $student_id)->where('is_additional_subject', '<>', '')->where('supplementary_id', $supp_id)->get();

        $mastersuppexamsubject = array();
        if (@!empty($masterSuppExamYears->exam_year && $masterSuppExamYears->exam_month)) {
            $mastersuppexamsubject = ExamSubject::where('student_id', $student_id)->where('exam_year', $masterSuppExamYears->exam_year)->where('exam_month', $masterSuppExamYears->exam_month)->where('final_result', 'p')->get()->toArray();
        }
        $master_subject_details1 = SupplementarySubject::where('student_id', $student_id)->where('is_additional_subject', 'IS NULL', null)->where('supplementary_id', $supp_id)->get()->toArray();

        $result = array_merge($mastersuppexamsubject, $master_subject_details1);

        $subject_list = $this->subjectList();
        $masterrecord = Supplementary::with('SupplementarySubject')
            ->where('student_id', $student_id)
            ->where('supplementaries.exam_year', $exam_year)
            ->where('supplementaries.exam_month', $exam_month)
            ->first();
        // @dd($masterrecord);

        $masterStudent = Student::where('id', $student_id)->first();
        $application_fee = @$masterrecord->total_fees;
        if (@$masterrecord && $application_fee == 0) {
            return redirect()->back()->with('error', 'Failed! Supplementary Fee does not saved.');
        }
        if (count($request->all()) > 0) {

            if ($application_fee == 0) {
                return redirect()->back()->with('error', 'Failed! Supplementary Fee does not saved.');
            }
            $supplementary = new Supplementary; /// create model object
            $validator = Validator::make($request->all(), $supplementary->rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            if ($request->locksumbitted == 'on') {
                $locksumbitted = '1';
            }


            $supplementary_id = $masterrecord->id;
            /* Supp Fee Details Update Start */
            $master_fee = $custom_component_obj->_getSuppFeeDetailsForDispaly($student_id);


            $studentfeedata = array(
                'subject_change_fees' => $master_fee['subject_change_fees'],
                'exam_fees' => $master_fee['exam_subject_fees'],
                'practical_fees' => $master_fee['practical_fees'],
                'forward_fees' => $master_fee['forward_fees'],
                'online_fees' => $master_fee['online_services_fees'],
                'late_fees' => $master_fee['late_fees'],
                'total_fees' => $master_fee['final_fees']
            );
            $supplementary_fee_update = Supplementary::where('id', $supplementary_id)->update($studentfeedata);
            $studentSubjectfeedata = array(
                'student_id' => $student_id,
                'supplementary_id' => $supplementary_id,
                'total' => $master_fee['final_fees'],
            );

            $alredyPresent = SuppStudentFees::where('student_id', $student_id)->where('supplementary_id', $supplementary_id)->first();
            if (@$alredyPresent->id) {
                $studentfeesupdate = SuppStudentFees::where('student_id', $student_id)->where('supplementary_id', $supplementary_id)->update($studentSubjectfeedata);
            } else {
                $studentfeesupdate = SuppStudentFees::updateOrCreate($studentSubjectfeedata);
            }
            /* Supp Fee Details Update End */

            $locksubmitted_date = date("Y-m-d H:i:s");
            $supplementaryarray = ['locksumbitted' => $locksumbitted, 'locksubmitted_date' => $locksubmitted_date, 'enrollment' => $masterrecord->enrollment];

            $custom_component_obj = new CustomComponent;
            $isStudent = $custom_component_obj->_getIsStudentLogin();
            if (@$isStudent) {
                $supplementaryarray['is_self_filled'] = 1;
            } else {
                $supplementaryarray['is_self_filled'] = null;
            }
            $supplementaryarray['last_updated_by_user_id'] = @Auth::id();


            //$supplementarylocksumbitted = Supplementary::where('student_id',$student_id)->update($supplementaryarray);
            $supplementarylocksumbitted = Supplementary::where('id', $supplementary_id)->where('student_id', $student_id)->update($supplementaryarray);

            if ($supplementarylocksumbitted) {
                $this->_sendSupplementaryLockSubmittedMessage($student_id);
                return redirect()->route('supp_admin_preview_details', Crypt::encrypt($student_id))->with('message', 'Your application has been successfully Lock & Sumbitted. Please procced for online payment for complete your applicaiton!');
            } else {
                return redirect()->route('supp_admin_preview_details')->with('error', 'Failed! locksumbitted  details has been not submitted');
            }
        }
        $combo_name = 'student_declaration';
        $student_declaration = $this->master_details($combo_name);

        if (empty($master)) {
            return redirect()->route('/')->with('error', 'Failed! Details not found');
        }

        $routeUrl = "supp_fees_details";
        $previousTableName = "supp_student_fees";
        $isValid = true;
        if (@$master->exam_year && $master->exam_year <= 123) {
            $isValid = true;
        } else {
            $isValid = $this->getRecordExistorNot($student_id, $previousTableName);
        }
        if (!$isValid) {
            return redirect()->back()->with('error', 'Failed! Please first fill the details!');
        }

        $CustomComponent = new CustomComponent;

        $suppLateFeeDetails = null;

        if (@$masterrecord->exam_month) {
            $suppLateFeeDetails = $CustomComponent->_getSuppLateFeeDetails(@$masterrecord->exam_month, @$masterStudent->gender_id);

        }

        return view('supplementary.suppa_preview_details', compact('supp_master', 'suppLateFeeDetails', 'mastersupp', 'result', 'application_fee', 'mastersuppcount', 'masterrecord', 'student_declaration', 'model', 'master', 'subject_list', 'estudent_id', 'student_id', 'documentErrors', 'page_title'));
    }


    public function supp_generate_admin_student_pdf($student_id)
    {
        $table = $model = "Student";
        $page_title = 'View Details';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);

        $exam_year = Session::get("selected_exam_year");
        $exam_month = Session::get("selected_exam_month");

        $master = Student::with('application', 'studentfees', 'document', 'address', 'admission_subject', 'toc_subject')
            ->where('id', $student_id)->first();

        $extraCondtion = " exam_year < " . $exam_year;
        if ($exam_month == 1) {
            $extraCondtion = " exam_year <= " . $exam_year;
        }

        $examSubjectDetails = ExamSubject::where('student_id', $student_id)->whereRaw($extraCondtion)->orderBy('exam_year', 'DESC')->orderBy('exam_month', 'ASC')->first(['exam_year', 'exam_month']);


        if (@$exam_year) {

        } else {
            $exam_year = Config::get('global.form_supp_current_admission_session_id');
        }
        $supplementaryDetails = Supplementary::with('SupplementarySubject')
            ->where('supplementaries.student_id', $student_id)
            ->where('supplementaries.exam_year', $exam_year)
            ->where('supplementaries.exam_month', $exam_month)
            ->first();

        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_months = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
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
        $combo_name = 'employment';
        $employment = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $rsos_years = $this->rsos_years();
        $subject_list = $this->subjectList();
        $subject_code_list = $this->subjectCodeList();
        $studentDocumentPath = $student_document_path[1] . $student_id;


        $master_subject_details = $this->getsuppStudentPdfDetails($student_id);
        /* Com. subjects start */
        $mastersupp = Supplementary::
        join('supplementary_subjects', "supplementaries.id", "supplementary_subjects.supplementary_id")
            ->where('supplementaries.student_id', $student_id)
            ->where('supplementaries.exam_year', $exam_year)
            ->where('supplementaries.exam_month', $exam_month)
            // ->whereNotNull('supplementary_subjects.is_additional_subject')
            ->whereNull('supplementary_subjects.deleted_at')
            ->where('supplementary_subjects.is_additional_subject', 1)
            // ->groupBy('supplementaries.student_id')
            ->orderBy('supplementaries.id', 'desc')
            ->get();
        // dd($mastersupp);

        /* Com. subjects end */
        $mastersuppexamsubject = array();
        /* Already Passed subjects start */
        $mastersuppexamsubjects = ExamSubject::where('student_id', $student_id)
            ->where('final_result', 'P')->where('exam_year', $examSubjectDetails->exam_year)->where('exam_month', $examSubjectDetails->exam_month)->first(['exam_year', 'exam_month']);
        $mastersuppexamsubject = array();
        if (!empty(@$mastersuppexamsubjects->exam_year && @$mastersuppexamsubjects->exam_month)) {
            $mastersuppexamsubject = ExamSubject::where('student_id', $student_id)
                ->where('final_result', 'P')->where('exam_year', $mastersuppexamsubjects->exam_year)->where('exam_month', @$mastersuppexamsubjects->exam_month)->get()
                ->toArray();
        }

        /* Already Passed subjects end */
        /* Additional subjects start */
        $master_subject_details1 = Supplementary::
        join('supplementary_subjects', "supplementaries.id", "supplementary_subjects.supplementary_id")
            ->where('supplementaries.student_id', $student_id)
            ->where('supplementaries.exam_year', $exam_year)
            ->where('supplementaries.exam_month', $exam_month)
            ->whereNull('supplementary_subjects.deleted_at')
            ->where('supplementary_subjects.is_additional_subject', 'IS NULL', null)
            // ->groupBy('supplementaries.student_id')
            ->orderBy('supplementaries.id', 'desc')
            ->get()
            ->toArray();
        /* Additional subjects end */
        $mastersuppcount = Supplementary::
        join('supplementary_subjects', "supplementaries.id", "supplementary_subjects.supplementary_id")
            ->where('supplementaries.student_id', $student_id)
            ->where('supplementaries.exam_year', $exam_year)
            ->where('supplementaries.exam_month', $exam_month)
            ->whereNull('supplementary_subjects.deleted_at')
            // ->whereNotNull('supplementary_subjects.is_additional_subject')
            ->where('supplementary_subjects.is_additional_subject', '<>', '')
            // ->groupBy('supplementaries.student_id')
            ->orderBy('supplementaries.id', 'desc')
            ->count();


        //$mastersuppcount = SupplementarySubject::where('student_id',$student_id)->where('is_additional_subject','<>', '')->count();
        //$mastersupp = SupplementarySubject::where('student_id',$student_id)->where('is_additional_subject','<>', '',)->get();
        //$mastersuppexamsubject = ExamSubject::where('student_id',$student_id)->where('final_result','p',)->get()->toArray();
        //$master_subject_details1 = SupplementarySubject::where('student_id',$student_id)->where('is_additional_subject','IS NULL', null,)->get()->toArray();


        $result = array_merge($mastersuppexamsubject, $master_subject_details1);
        if (empty($master)) {
            return redirect()->route('/')->with('error', 'Failed! Details not found');
        }
        $fld = "documentDetails";
        if (isset($master[$fld])) {
            unset($master[$fld]);
        }
        $master->supplementary = $supplementaryDetails;

        /* Replace string with X start */
        $custom_component_obj = new CustomComponent;
        $master->mobile = $custom_component_obj->_replaceTheStringWithX(@$master->mobile);
        /* Replace string with X end */
        $masterExamYear = $supplementaryDetails->exam_year;
        $masterExamMonth = $supplementaryDetails->exam_month;
        $masterCourse = $supplementaryDetails->course;
        $path = public_path("Supplementarypdf/" . $masterExamYear . "/" . $masterExamMonth . "/" . $masterCourse . "/");
        File::makeDirectory($path, $mode = 0777, true, true);
        $path .= "SupplementaryForm-" . $student_id . ".pdf";

        // return view('supplementary.supp_generate_student_pdf', compact('supplementaryDetails','subject_code_list','master_subject_details','subject_list','master','studentDocumentPath','student_id', 'page_title', 'estudent_id', 'model','gender_id','categorya','nationality','religion','disability','dis_adv_group','midium','rural_urban','employment','pre_qualifi','adm_types','course','exam_session','mastersupp','mastersuppcount','result'));
        $pdf = PDF::loadView('supplementary.supp_admin_generate_student_pdf', compact('supplementaryDetails', 'subject_code_list', 'master_subject_details', 'subject_list', 'master', 'studentDocumentPath', 'student_id', 'page_title', 'estudent_id', 'model', 'gender_id', 'categorya', 'nationality', 'religion', 'disability', 'dis_adv_group', 'midium', 'rural_urban', 'employment', 'pre_qualifi', 'adm_types', 'course', 'exam_session', 'mastersupp', 'mastersuppcount', 'result', 'exam_months', 'rsos_years', 'masterExamYear', 'masterExamMonth', 'admission_sessions'));

        if (file_exists($path)) {
            return Response::download($path);
        }
        $pdf->save($path, $pdf, true);
        return (Response::download($path));

        //return view('supplementary.supp_generate_student_pdf', compact('mastersuppcount','mastersupp','master_subject_details','subject_list','master','studentDocumentPath','student_id', 'page_title', 'estudent_id', 'model','gender_id','categorya','nationality','religion','disability','dis_adv_group','midium','rural_urban','employment','pre_qualifi','adm_types','course','exam_session','result','exam_months'));
    }


}
	
