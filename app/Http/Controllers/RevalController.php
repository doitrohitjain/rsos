<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Models\ExamSubject;
use App\Models\Registration;
use App\Models\RevalStudent;
use App\Models\RevalStudentSubject;
use App\Models\Student;
use App\Models\StudentAllotmentMark;
use App\Models\Toc;
use App\Models\TocMark;
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


class RevalController extends Controller
{

    private $request;

    public function __construct(request $request)
    {
        $this->request = $request;
        parent::__construct();
        // $this->_redirectForWhiteList();

        $this->middleware('permission:reval_find_enrollment', ['only' => ['reval_find_enrollment']]);
        // $this->middleware('permission:reval_subjects_details', ['only' => ['reval_subjects_details']]);
        // $this->middleware('permission:reval_preview_details', ['only' => ['reval_preview_details']]);
    }

    public function reval_find_enrollment(Request $request)
    {
    }


    public function reval_subjects_details(Request $request, $estudent_id)
    {
        $table = $model = "RevalStudentSubject";
        $page_title = "पुनर्मूल्यांकन विषय विवरण (Reval Subject " . ' Details )';
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $student_id = Crypt::decrypt($estudent_id);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $rsos_years = $this->rsos_years();

        /* Is input box start */
        $custom_component_obj = new CustomComponent;
        $allowSsoInput = false;
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        if ($isAdminStatus == true) {

        } else {
            $isStudent = $custom_component_obj->_getIsStudentLogin();
            if (@$isStudent) {
                $current_student_id = Auth::guard('student')->user()->id;
                if ($current_student_id == $student_id) {

                } else {
                    return redirect("/")->with('error', 'Invalid access.');
                }
            } else {
                $authid = @Auth::user()->id;
                if (@$authid) {

                } else {
                    return redirect("/")->with('error', 'Invalid access.');
                }
            }
        }
        /* Is input box end */


        $master = $studentdata = Student::findOrFail($student_id);


        $isItiStudent = $this->_isItiStudent($student_id);
        $subject_list = $this->subjectList($studentdata->course);
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $exam_month = @$exam_session[@$studentdata->stream];

        $combo_name = 'reval_exam_year';
        $reval_exam_year = $this->master_details($combo_name);
        $combo_name = 'reval_exam_month';
        $reval_exam_month = $this->master_details($combo_name);
        $combo_name = 'reval_types';
        $reval_types = $this->master_details($combo_name);
        $combo_name = 'reval_per_subject_fee';
        $reval_per_subject_fee = $this->master_details($combo_name);
        $reval_exam_year = $reval_exam_year[1];
        $reval_exam_month = $reval_exam_month[1];

        $toc = Toc::where('student_id', $student_id)->where('exam_year', $reval_exam_year)->where('exam_month', $reval_exam_month)->first();
        $tocSubjectIds = TocMark::where('toc_id', @$toc->id)->pluck('subject_id');


        $studentAllotmentMarkSubjectIds = StudentAllotmentMark::where('student_id', $student_id)
            ->where('exam_year', $reval_exam_year)
            ->where('exam_month', $reval_exam_month)
            ->whereNotIn('subject_id', $tocSubjectIds)
            ->pluck('subject_id', 'subject_id');

        $ExamSubject = ExamSubject::where('student_id', $student_id)
            ->where('exam_year', $reval_exam_year)
            ->where('exam_month', $reval_exam_month)
           ->whereIn('subject_id', @$studentAllotmentMarkSubjectIds)
            ->get();

        $role_id = Session::get('role_id');
        $super_admin = config("global.super_admin");
        $admin = config("global.admin");
        $developer_admin = config("global.developer_admin");
        $reval_id = null;

        $alredyPresent = RevalStudent::where('student_id', $student_id)->where('exam_year', '=', $reval_exam_year)->where('exam_month', '=', $reval_exam_month)->latest('id')->first(['id', 'reval_type']);

        if (@$alredyPresent->id) {
            $reval_id = $alredyPresent->id;
        }
        $isLockAndSubmit = $this->_isCheckrevalStudentFormLockAndSubmit($reval_id);

        if ($isLockAndSubmit == 1) {
            return redirect()->route('reval_preview_details', Crypt::encrypt($reval_id))->with('message', 'Reval application form already locked & submitted.');
        }


        if (count($request->all()) > 0) {
            $inputs = $request->all();

            $response = $this->ExamRevalSubjectValidation($request);
            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];
            if (@$isValid) {
                //check is already exists if no(insert into rs_reval_students) otherwise update.
                $revalStudentDetails = RevalStudent::where('student_id', $student_id)->where('exam_year', '=', $reval_exam_year)->where('exam_month', '=', $reval_exam_month)->latest('id')->first('id');
                $reval_id = null;
                if (!empty(@$revalStudentDetails->id)) {
                    $reval_id = @$revalStudentDetails->id;
                }
                $numberOfSubjects = count($request->subject_id);

                $response = $this->_revalSubjectFeesCalculate($numberOfSubjects, @$request->reval_type);
                $role_id = @Session::get('role_id');
                $student_role = Config::get("global.student");
                $current_student_id = null;
                if ($role_id == $student_role) {
                    $is_self_filled = 1;
                    $current_student_id = Auth::guard('student')->user()->id;
                } else {
                    $is_self_filled = NUll;
                    $current_student_id = @Auth::user()->id;
                }

                $saveData = array(
                    'student_id' => $student_id,
                    'late_fees' => $response['reval_late_fees'],
                    'total_fees' => $response['total_fees'],
                    'course' => $master->course,
                    'reval_type' => @$request->reval_type,
                    'stream' => $master->stream,
                    'ai_code' => $master->ai_code,
                    'enrollment' => $master->enrollment,
                    'last_updated_by_user_id' => $current_student_id,
                    'dob' => $master->dob,
                    'is_self_filled' => $is_self_filled,
                    'exam_year' => $reval_exam_year,
                    'exam_month' => $reval_exam_month,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                );
                if (@$reval_id) {
                    RevalStudent::where('id', $reval_id)->update($saveData);
                } else {
                    RevalStudent::updateOrCreate($saveData);
                    $alredyPresent = RevalStudent::where('student_id', $student_id)->where('exam_year', '=', $reval_exam_year)->where('exam_month', '=', $reval_exam_month)->latest('id')->first('id');
                    if (@$alredyPresent->id) {
                        $reval_id = $alredyPresent->id;
                    }
                }

                //find in exam subject and
                $examSubjectDetails = ExamSubject::where('student_id', $student_id)->where('exam_year', $reval_exam_year)->where('exam_month', $reval_exam_month)->whereNotIn('subject_id', $tocSubjectIds)->get();

                $examSubjectArr = array();
                foreach ($examSubjectDetails as $ok => $ov) {
                    $examSubjectArr[$ov->subject_id]['subject_id'] = $ov->subject_id;
                    $examSubjectArr[$ov->subject_id]['final_theory_marks'] = $ov->final_theory_marks;
                    $examSubjectArr[$ov->subject_id]['final_practical_marks'] = $ov->final_practical_marks;
                    // $examSubjectArr[$ov->subject_id]['sessional_marks'] = $ov->sessional_marks;//here need to be change
                    $examSubjectArr[$ov->subject_id]['sessional_marks'] = $ov->sessional_marks_reil_result;//here changed
                    $examSubjectArr[$ov->subject_id]['total_marks'] = $ov->total_marks;
                    $examSubjectArr[$ov->subject_id]['final_result'] = $ov->final_result;
                }

                RevalStudentSubject::where('reval_id', $reval_id)->delete();
                if ($numberOfSubjects > 0) {
                    foreach ($request->subject_id as $key => $subject_id) {
                        $studentsubjectdata = array(
                            'student_id' => $student_id,
                            'reval_id' => $reval_id,
                            'subject_id' => $subject_id,
                            'exam_year' => $reval_exam_year,
                            'exam_month' => $reval_exam_month,
                            'final_practical_marks' => @$examSubjectArr[$subject_id]['final_practical_marks'],
                            'final_theory_marks' => @$examSubjectArr[$subject_id]['final_theory_marks'],
                            'sessional_marks' => @$examSubjectArr[$subject_id]['sessional_marks'],
                            'total_marks' => @$examSubjectArr[$subject_id]['total_marks'],
                            'final_result' => @$examSubjectArr[$subject_id]['final_result'],
                        );
                        $studentsubjectsupdate = RevalStudentSubject::updateOrCreate($studentsubjectdata);
                    }
                    if ($studentsubjectsupdate) {
                        return redirect()->route('reval_preview_details', Crypt::encrypt($reval_id))->with('message', 'Reval Subject Details successfully saved.');
                    } else {
                        return redirect()->back()->with('error', 'Student subject not submitted.');
                    }
                }
            }

        }

        return view('reval.reval_subjects_details', compact('isItiStudent', 'alredyPresent', 'reval_per_subject_fee', 'reval_types', 'ExamSubject', 'subject_list', 'model', 'adm_types', 'master', 'student_id', 'pre_qualifi', 'rsos_years', 'estudent_id', 'reval_id', 'page_title'));

    }

    public function reval_preview_details(Request $request, $ereval_id)
    {
        $reval_id = Crypt::decrypt($ereval_id);

        $docrejectednotification = null;

        // $smsStatus = $this->_sendEnrollmentGeneratedSubmittedMessage($student_id);
        // echo "Test";die;

        $table = $model = "Preview";
        $page_title = 'Preview Details';
        $documentErrors = null;
        // $sub_code = $this->getSubjectCode($student_id);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);

        $combo_name = 'reval_exam_year';
        $reval_exam_year = $this->master_details($combo_name);
        $combo_name = 'reval_exam_month';
        $reval_exam_month = $this->master_details($combo_name);
        $reval_exam_year = $reval_exam_year[1];
        $reval_exam_month = $reval_exam_month[1];

        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');


        $RevalStudent = RevalStudent::where('id', $reval_id)->first('student_id');
        $student_id = null;

        if (!empty($RevalStudent->student_id)) {
            $student_id = $RevalStudent->student_id;
        }


        /* Is input box start */
        $custom_component_obj = new CustomComponent;
        $allowSsoInput = false;
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        if ($isAdminStatus == true) {

        } else {
            $isStudent = $custom_component_obj->_getIsStudentLogin();
            if (@$isStudent) {
                $current_student_id = Auth::guard('student')->user()->id;
                if ($current_student_id == $student_id) {

                } else {
                    return redirect("/")->with('error', 'Invalid access.');
                }
            } else {
                $authid = @Auth::user()->id;
                if (@$authid) {

                } else {
                    return redirect("/")->with('error', 'Invalid access.');
                }
            }
        }
        /* Is input box end */

        $estudent_id = Crypt::encrypt($student_id);


        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);

        if ($isLockAndSubmit == 1) {
            // return redirect()->route('view_details',Crypt::encrypt($student_id))->with('message', 'Form already successfully locked and submitted.');
        }

        $isItiStudent = $this->_isItiStudent($student_id);
        $master = $this->getRevalStudentDetails($student_id, $reval_id);

        /* Replace string with X start */
        $custom_component_obj = new CustomComponent;
        $master['personalDetails']['data']['mobile']['value'] = $custom_component_obj->_replaceTheStringWithX(@$master['personalDetails']['data']['mobile']['value']);

        if (@$master['personalDetails']['data']['jan_aadhar_number']['value']) {
            $master['personalDetails']['data']['jan_aadhar_number']['value'] = $custom_component_obj->_replaceTheStringWithX(@$master['personalDetails']['data']['jan_aadhar_number']['value']);
        }
        if (@$master['personalDetails']['data']['aadhar_number']['value']) {
            $master['personalDetails']['data']['aadhar_number']['value'] = $custom_component_obj->_replaceTheStringWithX(@$master['personalDetails']['data']['aadhar_number']['value']);
        }

        if (@$master['bankDetails']['data']['account_number']['value']) {
            $master['bankDetails']['data']['account_number']['value'] = $custom_component_obj->_replaceTheStringWithX(@$master['bankDetails']['data']['account_number']['value']);
        }

        if (@$master['bankDetails']['data']['linked_mobile']['value']) {
            $master['bankDetails']['data']['linked_mobile']['value'] = $custom_component_obj->_replaceTheStringWithX(@$master['bankDetails']['data']['linked_mobile']['value']);
        }
        if (@$master['bankDetails']['data']['ifsc_code']['value']) {
            $master['bankDetails']['data']['ifsc_code']['value'] = $custom_component_obj->_replaceTheStringWithX(@$master['bankDetails']['data']['ifsc_code']['value']);
        }
        if (@$master['TransactionDetails']['data']['challan_tid']['value']) {
            $master['TransactionDetails']['data']['challan_tid']['value'] = $custom_component_obj->_replaceTheStringWithX(@$master['TransactionDetails']['data']['challan_tid']['value']);
        }

        /* Replace string with X end */


        // Check Current date for allow lock & submit
        $stream = '';
        if (@$master['personalDetails']['data']['stream']['value'] == 'Stream-I') {
            $stream = 1;
        } else if (@$master['personalDetails']['data']['stream']['value'] == 'Stream-II') {
            $stream = 2;
        }
        $gender_id = '';
        if (@$master['personalDetails']['data']['gender_id']['value'] == 'Female') {
            $gender_id = 1;
        } else if (@$master['personalDetails']['data']['gender_id']['value'] == 'Male') {
            $gender_id = 2;
        }

        $currentDateAllowOrNotStatus = $this->_checkIsAllowStudentForRevalApplicationForm($student_id);

        $documentErrors = $this->getPendingDocuemntDetails($student_id);
        $getBoardList = $this->getBoardList();
        $application_fee = $master['studentfeesDetails']['data']['total']['value'];


        $studentdata = Student::findOrFail($student_id);


        $masterrecord = RevalStudent::where('id', $reval_id)->first();

        if (count($request->all()) > 0) {
            if (@$currentDateAllowOrNotStatus) {
            } else {
                return redirect()->back()->with('error', 'Failed! Registration date has been closed for last year student!');
            }
            $revalStudent = new RevalStudent; /// create model object
            $validator = Validator::make($request->all(), $revalStudent->ruleslocksubmit, $revalStudent->rulesapplicationandstudent);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            if ($request->locksumbitted == 'on') {
                $locksumbitted = '1';
            }
            $locksubmitted_date = date("Y-m-d H:i:s");

            $applicationarray = ['locksumbitted' => $locksumbitted, 'locksubmitted_date' => $locksubmitted_date];

            // Student tbl Updation Log Enteries
            $table_name = 'reval_students';
            $form_type = 'Reval';
            $controller_obj = new Controller;
            $table_primary_id = @$masterrecord->id;
            $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            // Student tbl Updation Log Enteries


            $applicationarray = RevalStudent::where('id', $masterrecord->id)->update($applicationarray);
            $RevalStudent = RevalStudent::where('id', $masterrecord->id)->first();
            $numberOfSubjects = RevalStudentSubject::where('reval_id', $reval_id)->count();

            $response = $this->_revalSubjectFeesCalculate($numberOfSubjects, $RevalStudent->reval_type);
            $revalFeeData = array(
                'student_id' => $student_id,
                'late_fees' => $response['reval_late_fees'],
                'total_fees' => $response['total_fees']
            );


            $revalFeeDataUpdated = RevalStudent::where('id', $masterrecord->id)->update($revalFeeData);

            if (@$response['total_fees'] && $response['total_fees'] > 0) {
                $smsStatus = $this->_sendLockSubmittedMessageReval($student_id);
            }

            if ($applicationarray) {
                return redirect()->route('reval_preview_details', Crypt::encrypt($reval_id))->with('message', 'Your complete details has been successfully submitted.');
            } else {
                return redirect()->route('reval_preview_details', Crypt::encrypt($reval_id))->with('error', 'Failed! Reval details has been not submitted');
            }
        }

        $combo_name = 'reval_student_declaration';
        $reval_student_declaration = $this->master_details($combo_name);

        if (empty($master)) {
            return redirect()->route('/')->with('error', 'Failed! Details not found');
        }

        $routeUrl = "reval_subjects_details";
        $previousTableName = "reval_student_subjects";
        $isValid = $this->getRecordExistorNot($student_id, $previousTableName);
        if (!$isValid) {
            //return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! Please first fill the details!');
        }


        return view('reval.reval_preview_details', compact('application_fee', 'categorya', 'isItiStudent', 'reval_student_declaration', 'isLockAndSubmit', 'model', 'master', 'estudent_id', 'student_id', 'documentErrors', 'pre_qualifi', 'page_title', 'masterrecord', 'ereval_id', 'reval_id', 'currentDateAllowOrNotStatus', 'getBoardList', 'docrejectednotification'));
    }

    public function reval_generate_student_pdf($reval_id = null)
    {

        $table = $model = "reval_students";
        $page_title = 'View Details';
        $reval_id = Crypt::decrypt($reval_id);
        $RevalStudent = RevalStudent::where('id', $reval_id)->first();
        $student_id = $RevalStudent->student_id;


        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $master = Student::with('document', 'address', 'application')->where('id', $student_id)->first();
        $RevalStudentSubject = RevalStudentSubject::where('reval_id', $reval_id)->get();

        $subject_list = $this->subjectList($master->course);
        $master['RevalStudent'] = $RevalStudent;
        $master['RevalStudentSubject'] = $RevalStudentSubject;


        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
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
        $combo_name = 'yesno';
        $yesno = $this->master_details($combo_name);
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
        $combo_name = 'student_reval_document_path';
        $student_reval_document_path = $this->master_details($combo_name);
        $combo_name = 'reval_types';
        $reval_types = $this->master_details($combo_name);
        $combo_name = 'reval_per_subject_fee';
        $reval_per_subject_fee = $this->master_details($combo_name);


        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $combo_name = 'reval_exam_year';
        $reval_exam_year = $this->master_details($combo_name);
        $combo_name = 'reval_exam_month';
        $reval_exam_month = $this->master_details($combo_name);
        $rsos_years = $this->rsos_years();
        $reval_exam_year = $reval_exam_year[1];
        $reval_exam_month = $reval_exam_month[1];
        $studentDocumentPath = $student_document_path[1] . $student_id;
        $revalDocumentPath = $student_reval_document_path[1];
        $currentfolderpath = $current_folder_year[$reval_exam_year];
        $filename = $student_id . '_' . $RevalStudent->id . '_' . 'reval.pdf';
        $path = public_path($revalDocumentPath . $currentfolderpath . '/' . $reval_exam_month . '/' . $student_id . '/' . $filename);
        $pdf = PDF::loadView('reval.reval_generate_student_pdf', compact('reval_per_subject_fee', 'reval_types', 'reval_id', 'master', 'studentDocumentPath', 'stream_id', 'admission_sessions', 'exam_month', 'model', 'gender_id', 'categorya', 'nationality', 'religion', 'disability', 'dis_adv_group', 'midium', 'rural_urban', 'employment', 'pre_qualifi', 'adm_types', 'course', 'exam_session', 'rsos_years', 'reval_exam_year', 'reval_exam_month', 'aiCenters', 'are_you_from_rajasthan', 'subject_list', 'are_you_from_rajasthan', 'yesno'));
        $pdf->save($path, $pdf, true);
        return $pdf->download('reval.pdf');

        //return view('reval.reval_generate_student_pdf', compact('reval_id','master','studentDocumentPath','stream_id','subject_list'));

    }
}
	
