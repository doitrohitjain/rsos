<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\MarksheetCustomComponent;
use App\Helper\CustomHelper;
use App\Models\AiCenterMap;
use App\Models\ExamSubject;
use App\Models\Registration;
use App\Models\Student;
use App\Models\StudentAllotment;
use App\Models\SuppChangeRequertOldStudentFees;
use App\Models\SuppChangeRequestStudents;
use App\Models\SuppChangeRequestStudentTarils;
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


class SupplementaryController extends Controller
{

    private $request;

    public function __construct(request $request)
    {
        $this->request = $request;
        parent::__construct();
        // $this->_redirectForWhiteList();

        $this->middleware('permission:supp_find_enrollment', ['only' => ['supp_find_enrollment']]);
        // $this->middleware('permission:supp_subjects_details', ['only' => ['supp_subjects_details']]);
        // $this->middleware('permission:supp_fees_details', ['only' => ['supp_fees_details']]);
        // $this->middleware('permission:supp_preview_details', ['only' => ['supp_preview_details']]);
    }


    public function supp_find_enrollment(Request $request)
    {

        $showStatus = $this->_getCheckAllowSuppForm();
        if (!$showStatus) {
            return redirect()->route("landing")->with('error', 'You are not allowed to see fill the supplementary form.');
        }

        $selected_session = CustomHelper::_get_selected_sessions();
        $role_id = @Session::get('role_id');
        if (@$selected_session == Config::get('global.form_supp_current_admission_session_id') || $role_id == Config::get('global.super_admin_id')) {

        } else {
            return redirect()->route('dashboard')->with('error', 'Invalid Access!');
        }

        $showSuppStatus = $this->_getCheckAllowToCheckSupp();
        if (!$showSuppStatus) {
            return redirect()->back()->with('error', 'Invalid Access!');
        }


        $table = $model = "Student";
        $page_title = 'पूरक आवेदन पत्र के लिए नामांकन खोजें (Find Enrollment For Supplementary Application Form)';
        $routeUrl = "supp_find_enrollment";
        $custom_component_obj = new CustomComponent;
        $master = null;
        $estudent_id = null;
        $student_id = null;

        $formOpenAllowOrNot = $custom_component_obj->checkAnySuppEntryAllowOrNot();

        $errMsg = null;
        if (!$formOpenAllowOrNot) {
            $errMsg = 'पूरक आवेदन पत्र की तिथि समाप्त कर दी गई है।(The supplementary Application form date is closed.)';
            return redirect()->route('aicenterdashboard')->with('error', $errMsg);
        }

        if (count($request->all()) > 0 && !empty($request->enrollment)) {
            $master = Student::where('enrollment', $request->enrollment)->first();
            if (empty($master->id)) {
                return redirect()->route('supp_find_enrollment')->with('error', 'Failed! Student Enrollment not found,Please check student enrollment details.');
            }
            if (empty($master->stream)) {
                return redirect()->route('supp_find_enrollment')->with('error', 'Failed! Student stream not found, Please check student stream details.');
            }

            $exam_result = ExamResult::where('student_id', '=', $master->id)->orderBy('exam_year', 'desc')->orderBy('exam_month', 'asc')->first();

            /* if($exam_result->is_temp_examresult == "111"){
				return redirect()->back()->with('error', 'The Examination has been Cancelled.');
			}*/

            if (@$master->course && $master->course == 12) {
                $isInValidStudent = $custom_component_obj->_checkIsInValidStudentTweleveSupp($master->id);
                if ($isInValidStudent) {
                    return redirect()->route('supp_find_enrollment')
                        ->with('error', 'Failed! You are passed previous qualifcation but passing years yet not completed 1.5 years so that you are not allow to fill the form.(असफल! आपने पिछली योग्यता उत्तीर्ण कर ली है, लेकिन डेढ़ वर्ष पूरे नहीं हुए हैं, इसलिए आपको फॉर्म भरने की अनुमति नहीं दी जाएगी।)');
                }
            }

            $ExamSubjectYearArr = ExamSubject::where('enrollment', $request->enrollment)->latest('exam_year')->first('exam_year', 'exam_month');
            $exam_year_latest = null;
            if (!empty(@$ExamSubjectYearArr->exam_year)) {
                $exam_year_latest = @$ExamSubjectYearArr->exam_year;
            }
            $studentdata = Student::where('enrollment', $request->enrollment)->first();
            if (!empty($request->enrollment) && $studentdata['adm_type'] == 3) {
                // please change convert to row query to eloquent query
                $query = "select count(*) as counter from `rs_exam_subjects` where `enrollment` = " . $request->enrollment . " and (`final_result` != 'PASS' and `final_result` != 'P' and `final_result` != 'p') and `deleted_at` IS NULL and `exam_year` = " . $exam_year_latest;
                $passedResultCountResult = DB::select($query);
                $passedResultCount = 0;
                if (@$passedResultCountResult[0]->counter) {
                    $passedResultCount = $passedResultCountResult[0]->counter;
                }
                if ($passedResultCount == 0) {
                    return redirect()->route('supp_find_enrollment')->with('error', 'Failed! You are already passed in all subject');
                }
            }
            $challanIdExist = $custom_component_obj->suppChallanTidAlreadyExist($studentdata['id']);
            $errMsg = null;

            $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
            if ($isAdminStatus == false) {
                if ($challanIdExist == true) {
                    return redirect()->route('supp_preview_details', Crypt::encrypt($studentdata['id']))->with('message', 'Payment has been already done');
                }
            }
            $response = $custom_component_obj->suppFindEnrollmentValidation($request);
            $responseFinal = null;

            if (@$response) {
                $responseFinal['isValid'] = $response['isValid'];
                $responseFinal['customerrors'] = $response['errors'];
                $responseFinal['validator'] = $response['validator'];
            }

            // $responseFinal['isValid'] = true;   // This is temporay code.
            if ($responseFinal['isValid'] == true) {
                $student_id = $master->id;

                $estudent_id = Crypt::encrypt($student_id);

                $checkValidNumberOfSubjectForSuppForm = $this->_isValidStudentSubjectsCount($student_id);
                if ($checkValidNumberOfSubjectForSuppForm) {
                } else {
                    return redirect()->back()->with('error', 'Enrollment found but your subjects are more than 7. Please contact to RSOS');
                }
                return redirect()->route('supp_subjects_details', $estudent_id)->with('message', 'Enrollment details found.');
            } else {
                $customerrors = @$responseFinal['customerrors'];
                return redirect()->back()->withErrors($responseFinal['validator'])->withInput($request->all());
            }
        }
        return view('supplementary.supp_find_enrollment', compact('model', 'master', 'estudent_id', 'student_id', 'page_title'));
    }

    public function supp_subjects_details(Request $request, $estudent_id)
    {
        /* student not allowed start */
        $role_id = @Session::get('role_id');
        if ($role_id == Config::get('global.student')) {
            //return redirect()->route('studentsdashboards')->with('error', 'Failed! You are not allowed!');
        }
        $checkchangerequestsssupplementariesAllowOrNotAllow = $this->_checkchangerequestssupplementariesAllowOrNotAllow();
        /* student not allowed end */
        $table = $model = "Supplementary";
        $page_title = 'पूरक विषय विवरण (Supplementary Subjects Details)';
        $routeUrl = "supp_find_enrollment";
        $supplementary_id = null;

        $exam_year = CustomHelper::_get_selected_sessions();
        $rsos_years = $this->rsos_years();
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);


        $role_id = @Session::get('role_id');
        if ($role_id == Config::get('global.student')) {
            $exam_year = Config::get('global.form_supp_current_admission_session_id');
        }
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
        $estudent_id = $estudent_id;
        $student_id = Crypt::decrypt($estudent_id);

        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);

        /*
		if(count($request->all()) > 0){
			$nonAdditionalSubjectCount =  count(array_filter(@$request->origional_subject_id));
			$AdditionalSubjectCount = count(array_filter(@$request->subject_id));
			// echo $nonAdditionalSubjectCount;echo "</br>";echo $AdditionalSubjectCount;@dd('test');
			if($nonAdditionalSubjectCount < 5 && !empty($AdditionalSubjectCount) ){
				return redirect()->route('supp_subjects_details',$estudent_id)->with('error', 'Failed! Please select compulsary subject before additional subject.');
			}
		}
		*/

        // $master = Student::with('exam_subject','Address','supplementary','supplementary_subject')->where('students.id',$student_id)->whereRelation('supplementary',$conditions_supp)->first();

        /*
		$master = Student::where('students.id',$student_id)
		->with('supplementary', function ($query) use($exam_year,$exam_month) {
			$query->where('exam_year','=',$exam_year)->where('exam_month','=',$exam_month);
		})
		->with('exam_subject','Address','Supplementary.supplementary_subject_by_suppid')
		->first();
		*/
        $changereqestsupplementaryid = Supplementary::where('student_id', $student_id)->where('exam_year', '=', $exam_year)->where('exam_month', '=', $exam_month)->where('supp_student_change_requests', 2)->first('supp_student_change_requests');
        if (@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$changereqestsupplementaryid->supp_student_change_requests == 2) {
        } else {
            $custom_component_obj = new CustomComponent;
            $challanIdExist = $custom_component_obj->suppChallanTidAlreadyExist($student_id);
            $errMsg = null;
            $isAdminStatus = $custom_component_obj->_checkIsAdminRole();


            if ($isAdminStatus == false) {
                if ($challanIdExist == true) {
                    return redirect()->route('supp_preview_details', Crypt::encrypt($student_id))->with('message', 'Payment has been already done');
                }
            }
        }
		
        $SupplementaryIdArr = Supplementary::where('student_id', $student_id)->where('exam_year', '=', $exam_year)->where('exam_month', '=', $exam_month)->latest('id')->first('id');
        $supp_id = null;
        if (!empty(@$SupplementaryIdArr->id)) {
            $supp_id = @$SupplementaryIdArr->id;
        }


        /* Exam Month and Year */
        $ExamSubjectYearArr = ExamSubject::where('student_id', $student_id)->orderBy('exam_year', 'DESC')->first(['exam_year', 'exam_month']);
        $exam_year_latest = null;
        if (!empty(@$ExamSubjectYearArr->exam_year)) {
            $exam_year_latest = @$ExamSubjectYearArr->exam_year;
        }
        $tempExamMonth = null;
        if (!empty(@$ExamSubjectYearArr->exam_month)) {
            $tempExamMonth = @$ExamSubjectYearArr->exam_month;
        }
        $orderByEammMonth = 'ASC';
        if ($tempExamMonth == 1) {
            $orderByEammMonth = 'DESC';
        }
        $examSubjectMonthArr = ExamSubject::where('student_id', $student_id)->where('exam_year', $exam_year_latest)->orderBy('exam_month', $orderByEammMonth)->first('exam_month');
        $exam_month_latest = null;
        if (!empty(@$examSubjectMonthArr->exam_month)) {
            $exam_month_latest = @$examSubjectMonthArr->exam_month;
        }
        /* Exam Month and Year */
        $master = Student::where('students.id', $student_id)
            ->with('supplementary', function ($query) use ($exam_year, $exam_month) {
                $query->where('exam_year', '=', $exam_year)->where('exam_month', '=', $exam_month);
            })->with('supplementary_subject', function ($query) use ($supp_id) {
                $query->where('supplementary_id', '=', $supp_id);
            })->with('exam_subject', function ($query) use ($exam_year_latest, $exam_month_latest) {
                $query->where('exam_year', '=', $exam_year_latest);
                $query->where('exam_month', '=', $exam_month_latest);
                // $query->orderBy('exam_year','desc');
                // $query->orderBy('exam_subjects.exam_month','asc');
                // $query->orderBy('exam_subjects.id2','asc');
                $query->groupBy('exam_subjects.subject_id');

            })
            ->with('Address')
            ->first();

        $exist_sub_arr = array();
        if (count($request->all()) > 0) {
            $exist_sub_arr = array_filter($request['subject_id']);
        }

        $fail_subject_arr = array();
        if (!empty(@$master->exam_subject)) {
            foreach (@$master->exam_subject as $exam_subject) {
                if (@$exam_subject->final_result && $exam_subject->final_result != 'P') {
                    $fail_subject_arr[$exam_subject->subject_id] = $exam_subject->subject_id;
                }
            }
        }

        /* Start  */
        $allowedMaxSubject = $this->_getSuppMaxSubjectAllowedNumber($master->id);
        // dd($allowedMaxSubject);
        /* End */

        // $master = Student::with('exam_subject','Address')->where('students.id',$student_id)->first();

        if (empty($master->stream)) {
            return redirect()->route('supp_find_enrollment')->with('error', 'Failed! Student stream not found, Please check student stream details.');
        }

        $isLockAndSubmit = $this->_isSuppFormLockAndSubmit($student_id);

        if ($isLockAndSubmit == 1) {
            return redirect()->route('supp_preview_details', Crypt::encrypt($student_id))->with('message', 'Form already successfully locked and submitted.');
        }

        if (empty($master)) {
            return redirect()->route('supp_find_enrollment')->with('error', 'Failed! Student data not found');
        }

        $custom_component_obj = new CustomComponent;
        $isStudent = $custom_component_obj->_getIsStudentLogin();

        if (@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$changereqestsupplementaryid->supp_student_change_requests == 2) {
        } else {
            if (@$isStudent) {
            } else {
                $supp_exam_month = Config::get('global.supp_current_admission_exam_month');
                $formOpenAllowOrNot = $custom_component_obj->checkSuppEntryAllowOrNot($supp_exam_month);
                $errMsg = null;

                if (!$formOpenAllowOrNot) {
                    $errMsg = 'पूरक आवेदन पत्र की तिथि समाप्त कर दी गई है।(The supplementary Application form date is closed.)';
                    return redirect()->route('aicenterdashboard')->with('error', $errMsg);
                }
            }
        }

        if (empty($master->id)) {
            return redirect()->route('supp_find_enrollment')->with('error', 'Failed! Student Enrollment not found,Please check student enrollment details.');
        }
        $response = $custom_component_obj->suppFindEnrollmentValidationByEnrollment($master->enrollment, $estudent_id);

        // $findEnrollemntRulesResponse['isValid'] = true;   // This is temporay code.
        $findEnrollemntRulesResponse = null;
        if (@$response) {
            $findEnrollemntRulesResponse['isValid'] = $response['isValid'];
            $findEnrollemntRulesResponse['customerrors'] = $response['errors'];
            $findEnrollemntRulesResponse['validator'] = $response['validator'];
        }

      
        $suppOrigionalSubLists = array();
        foreach (@$master->supplementary_subject->toArray() as $supp_exam_subject_key => $supp_exam_subject_value) {
            $suppOrigionalSubLists[$supp_exam_subject_value['origional_subject_id']] = $supp_exam_subject_value['origional_subject_id'];
        }
        if ($findEnrollemntRulesResponse['isValid'] == true) {
            $supp_subject_arr = array();
            $k = 0;

            if (!empty($master->supplementary_subject->toArray())) {

                $k = 0;
                $subjectsInSupp = array();
                foreach (@$master->supplementary_subject->toArray() as $supp_exam_subject_key => $supp_exam_subject_value) {
                    if (@$supp_exam_subject_value['is_additional_subject']) {
                        continue;
                    }
                    if (empty($supp_exam_subject_value['previous_subject_id']) || $supp_exam_subject_value['previous_subject_id'] == null) {
                        $supp_subject_arr[$k]['previous_subject_id'] = $supp_exam_subject_value['subject_id'];
                    } else {
                        $supp_subject_arr[$k]['previous_subject_id'] = $supp_exam_subject_value['previous_subject_id'];
                    }
                    if (@$supp_exam_subject_value['is_additional_subject'] && !empty($supp_exam_subject_value['is_additional_subject'])) {
                        $supp_subject_arr[$k]['additional'] = true;
                    }
                    if (@$supp_exam_subject_value['origional_subject_id'] && !empty($supp_exam_subject_value['origional_subject_id'])) {
                        $supp_subject_arr[$k]['origional_subject_id'] = $supp_exam_subject_value['origional_subject_id'];
                    }
                    $supp_subject_arr[$k]['subject_id'] = $supp_exam_subject_value['subject_id'];
                    $supp_subject_arr[$k]['final_result'] = '';
                    $supp_subject_arr[$k]['type'] = 'supp';

                    if (@$supp_subject_arr[$k]['origional_subject_id']) {
                        $subjectsInSupp[$supp_subject_arr[$k]['origional_subject_id']] = @$supp_subject_arr[$k]['origional_subject_id'];
                    }
                    $k++;
                }
                foreach (@$master->exam_subject->toArray() as $exam_subject_key => $exam_subject_value) {

                    if (!in_array($exam_subject_value['subject_id'], $subjectsInSupp) && !in_array($exam_subject_value['subject_id'], $suppOrigionalSubLists)) {
                        $supp_subject_arr[$k]['subject_id'] = $exam_subject_value['subject_id'];
                        if (@$exam_subject_value['final_result'] == 'P' || @$exam_subject_value['final_result'] == 'p' || @$exam_subject_value['final_result'] == 'PASS') {
                        } else {
                            $supp_subject_arr[$k]['subject_id'] = null;
                        }
                        $supp_subject_arr[$k]['origional_subject_id'] = $exam_subject_value['subject_id'];
                        $supp_subject_arr[$k]['final_result'] = '';
                        $supp_subject_arr[$k]['type'] = 'exam';
                        if (@$exam_subject_value['final_result'] == 'P' || @$exam_subject_value['final_result'] == 'p' || @$exam_subject_value['final_result'] == 'PASS') {
                            $supp_subject_arr[$k]['final_result'] = 'p';
                        }
                        $k++;
                    }
                }
                foreach (@$master->supplementary_subject->toArray() as $supp_exam_subject_key => $supp_exam_subject_value) {

                    if (@$supp_exam_subject_value['is_additional_subject']) {
                    } else {
                        continue;
                    }
                    if (empty($supp_exam_subject_value['previous_subject_id']) || $supp_exam_subject_value['previous_subject_id'] == null) {
                        $supp_subject_arr[$k]['previous_subject_id'] = $supp_exam_subject_value['subject_id'];
                    } else {
                        $supp_subject_arr[$k]['previous_subject_id'] = $supp_exam_subject_value['previous_subject_id'];
                    }
                    if (@$supp_exam_subject_value['is_additional_subject'] && !empty($supp_exam_subject_value['is_additional_subject'])) {
                        $supp_subject_arr[$k]['additional'] = true;
                    }
                    if (@$supp_exam_subject_value['origional_subject_id'] && !empty($supp_exam_subject_value['origional_subject_id'])) {
                        $supp_subject_arr[$k]['origional_subject_id'] = $supp_exam_subject_value['origional_subject_id'];
                    }
                    $supp_subject_arr[$k]['subject_id'] = $supp_exam_subject_value['subject_id'];
                    $supp_subject_arr[$k]['final_result'] = '';
                    $supp_subject_arr[$k]['type'] = 'supp';

                    if (@$supp_subject_arr[$k]['origional_subject_id']) {
                        $subjectsInSupp[$supp_subject_arr[$k]['origional_subject_id']] = @$supp_subject_arr[$k]['origional_subject_id'];
                    }
                    $k++;
                }

            } else if (!empty($master->exam_subject->toArray())) {
                foreach (@$master->exam_subject->toArray() as $exam_subject_key => $exam_subject_value) {

                    // echo "<br>" . $exam_subject_value['subject_id'] . " __ ". $exam_subject_value['final_result'];

                    if (@$exam_subject_value['final_result'] == 'p' || @$exam_subject_value['final_result'] == 'P' || @$exam_subject_value['final_result'] == 'PASS') {
                        $supp_subject_arr[$k]['origional_subject_id'] = $exam_subject_value['subject_id'];
                        $supp_subject_arr[$k]['subject_id'] = $exam_subject_value['subject_id'];
                        $supp_subject_arr[$k]['final_result'] = 'p';
                    } else {
                        $supp_subject_arr[$k]['origional_subject_id'] = $exam_subject_value['subject_id'];
                        $supp_subject_arr[$k]['subject_id'] = $exam_subject_value['subject_id'];
                        $supp_subject_arr[$k]['final_result'] = '';
                    }
                    $k++;
                }
                // echo "Before supp";  @dd(@$supp_subject_arr);
            }

            $byPassBelowIds = array();
            if (in_array($master->id, $byPassBelowIds)) {

            } else {

                //support with php 7+
                if (@$supp_subject_arr) {
                    usort($supp_subject_arr, function ($item1, $item2) {
                        return $item2['final_result'] <=> $item1['final_result'];
                    });
                }
            }

            // $supp_subject_arr[5] = $supp_subject_arr[6];
            // unset($supp_subject_arr[6]);
            // echo "Before supp";@dd(@$supp_subject_arr);
            // @dd(@$supp_subject_arr);

            $subject_list_origional = $subject_list_two = $subject_list = $this->subjectListArray($master->course);

            $supplementaryDetails = Supplementary::with('SupplementarySubject')
                ->where('student_id', $student_id)
                ->where('exam_year', $exam_year)
                ->where('exam_month', $exam_month)
                ->first();


            //$tempSupplementaryDetails = Supplementary::where('student_id',$student_id)
            //->where('exam_year',$exam_year)
            //->where('exam_month',$exam_month)
            //->first('exam_month');
            //$studentcurrentmonth = @$supplementaryDetails->exam_month;

            // unset($supp_subject_arr[6]);
            //$supplementaryDetails = Supplementary::join('supplementary_subjects','supplementary_subjects.student_id', '=', 'supplementaries.student_id')->where('Supplementary.student_id',$student_id)->get(['supplementaries.*','supplementary_subjects.*','supplementary_subjects.id as supplementary_subjects_id']);
            // @dd(@$supp_subject_arr);
            $studentcurrentmonth = Config::get('global.supp_current_admission_exam_month');
            $combo_name = 'student_supplementary_document_path';
            $student_document_path = $this->master_details($combo_name);
            $current_folder_year = $this->getCurrentYearFolderName();
            $studentDocumentPath = $student_document_path[1] . $current_folder_year . '/' . $studentcurrentmonth . '/' . $student_id . '/';

            if (count($request->all()) > 0) {
                $input = $request->all();
                $subject_id_array = $request->subject_id;
                $custom_component_obj = new CustomComponent;
                $response = $custom_component_obj->isValidSuppSubjects($subject_id_array, $master, $input);

                $isValid = $response['isValid'];
                $customerrors = $response['errors'];
                $validator = $response['validator'];

                // $isValid = true;   // This is temporay code.
                if ($isValid) {
                    // Docuemnt upload process

                    // if(empty($request->marksheet_doc_hidden)){
                    if ((empty($request->marksheet_doc_hidden) || !empty($request->marksheet_doc)) && $request->marksheet_doc != null) {

                        $rules = ['marksheet_doc' => 'required|mimes:jpg,png,jpeg,gif,pdf,svg|between:10,100'];

                        /* For MIME type issue on server side */
                        $validator = Validator::make($request->all(), $rules);
                        $errors = null;
                        if ($validator->fails()) {
                            $errors = $validator->errors()->first();
                        }
                        if (!empty($errors)) {
                            return redirect()->back()->with('error', $errors);
                        }
                        /* For MIME type issue on server side */

                        $validation = $this->validate($request, $rules);

                        $filename = 'supp_marksheet_' . $student_id . '_' . date("dmY") . '.' . $request->marksheet_doc->extension();
                        $request->marksheet_doc->move(public_path($studentDocumentPath), $filename);
                    } else {
                        $filename = $request->marksheet_doc_hidden;
                    }

                    if ((empty($request->sec_marksheet_doc_hidden) || !empty($request->sec_marksheet_doc)) && $request->sec_marksheet_doc != null) {
                        $rules = ['sec_marksheet_doc' => 'required|mimes:jpg,png,jpeg,gif,pdf,svg|between:10,100'];

                        /* For MIME type issue on server side */
                        $validator = Validator::make($request->all(), $rules);
                        $errors = null;
                        if ($validator->fails()) {
                            $errors = $validator->errors()->first();
                        }
                        if (!empty($errors)) {
                            return redirect()->back()->with('error', $errors);
                        }
                        /* For MIME type issue on server side */

                        $validation = $this->validate($request, $rules);
                        $sec_filename = 'sec_supp_marksheet_' . $student_id . '_' . date("dmY") . '.' . $request->sec_marksheet_doc->extension();
                        $request->sec_marksheet_doc->move(public_path($studentDocumentPath), $sec_filename);
                    } else {
                        $sec_filename = $request->sec_marksheet_doc_hidden;
                    }
                    $ai_code = Session::get('ai_code');
                    if (@$ai_code) {

                    } else {
                        $parent_ai_code = $this->getParentAiCodeByEnrollment($master->enrollment);
                        $ai_code = @$parent_ai_code[0];
                        //$master->enrollment get his parent aicode.
                    }

                    $role_id = @Session::get('role_id');
                    if ($role_id == Config::get('global.student')) {
                        $exam_year = Config::get('global.form_supp_current_admission_session_id');
                    }
                    // Docuemnt upload process
                    $supplementary_save_data = array(
                        'student_id' => $student_id,
                        'ai_code' => @$ai_code,
                        'enrollment' => @$master->enrollment,
                        'exam_year' => @$exam_year,
                        'exam_month' => @$exam_month,
                        'stream' => @$master->stream,
                        'course' => @$master->course,
                        'dob' => @$master->dob,
                        'marksheet_doc' => @$filename,
                        'sec_marksheet_doc' => @$sec_filename,
                        'subject_change_fees' => 0,
                        'exam_fees' => 0,
                        'practical_fees' => 0,
                        'forward_fees' => 0,
                        'online_fees' => 0,
                        'late_fees' => 0,
                        'total_fees' => 0,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    );

                    if (@$isStudent) {
                        $custom_component_obj = new CustomComponent;
                        $user_id_details = $custom_component_obj->_getAiCenterByAiCode(@$ai_code);
                        $supplementary_save_data['user_id'] = @$user_id_details->user_id;
                        $supplementary_save_data['is_self_filled'] = 1;
                        $supplementary_save_data['last_updated_by_user_id'] = $student_id;
                    } else {
                        $supplementary_save_data['is_self_filled'] = null;
                        $supplementary_save_data['last_updated_by_user_id'] = $supplementary_save_data['user_id'] = @Auth::id();
                    }

                    // $studentdataupdate=['mobile'=>$input['mobile']];

                    // $supplementary_id = Supplementary::updateOrCreate(['student_id' ,"=", $student_id,'exam_year',"=",$master->exam_year,'exam_month',"=",$master->exam_month],$supplementary_save_data)->id;
                    // $oldSupplementaryData = Supplementary::where("student_id","=",$student_id)->where("exam_year","=",$exam_year)->where("exam_month","=",$exam_month)->delete();

                    $alredyPresent = Supplementary::where('student_id', $student_id)->where("exam_year", "=", $exam_year)->where("exam_month", "=", $exam_month)->first();

                    if (@$alredyPresent->id) {
                        $supplementaryDetails = Supplementary::where('id', $alredyPresent->id)->update($supplementary_save_data);

                        //$studentdata=Student::where('id',$student_id)->update($studentdataupdate);
                    } else {
                        $supplementaryDetails = Supplementary::updateOrCreate($supplementary_save_data);

                        // $studentdata=Student::where('id',$student_id)->update($studentdataupdate);

                        $alredyPresent = Supplementary::where('student_id', $student_id)->where("exam_year", "=", $exam_year)->where("exam_month", "=", $exam_month)->first();
                    }


                    $supplementary_id = null;
                    if (@$alredyPresent->id) {
                        $supplementary_id = $alredyPresent->id;
                    }
                    // $supplementary_id = Supplementary::updateOrCreate(['student_id' => $student_id,'exam_year' => $master->exam_year,'exam_month' => $master->exam_month],$supplementary_save_data)->id;


                    $supplementaries_fee_data = SuppStudentFees::
                    where('student_id', $student_id)
                        ->where('supplementary_id', $supplementary_id)
                        ->delete();

                    if (@$supplementary_id) {
                        $is_additional_subject = 0;
                        $previous_subject_id = NULL;

                        // echo "<pre>";
                        // print_r($subject_id_array);
                        if (isset($subject_id_array) && !empty($subject_id_array)) {
                            $counter = 0;
                            foreach ($subject_id_array as $key => $value) {
                                if ($key > 4) {
                                    // maintain is_additional_subject flag
                                    $is_additional_subject = 1;
                                }
                                if ($value != null) {
                                    $previous_subject_id = $origional_subject_id = $input['origional_subject_id'][$key];
                                    // if(@$master->exam_subject[$key]->subject_id != $value){
                                    // 	$previous_subject_id = @$master->exam_subject[$key]->subject_id;
                                    // }
                                    // $origional_subject_id = @$master->exam_subject[$key]->subject_id;

                                    // echo "</br>origional_subject_id : ".$origional_subject_id;
                                    // echo "</br>previous_subject_id : ".$previous_subject_id;
                                    // echo "</br>subject id : ".$value . "</br>";


                                    if (!empty(@$fail_subject_arr) && in_array($value, $fail_subject_arr)) {
                                        //update as per last issue found 31082023
                                        $supplementary_subject_save_data[] = array(
                                            'student_id' => $student_id,
                                            'supplementary_id' => $supplementary_id,
                                            'subject_id' => @$value,
                                            'is_additional_subject' => @$is_additional_subject,
                                            // 'previous_subject_id' => @$value,
                                            // 'origional_subject_id' => @$value,
                                            'previous_subject_id' => @$previous_subject_id,
                                            'origional_subject_id' => @$origional_subject_id,
                                            'created_at' => date("Y-m-d H:i:s"),
                                            'updated_at' => date("Y-m-d H:i:s"),
                                            'exam_year' => @$exam_year,
                                            'exam_month' => @$exam_month,
                                            // 'temp' => 1,
                                        );
                                        // print_r($value);
                                        // echo "</br>";
                                        // print_r($fail_subject_arr);

                                    } else if (!empty(@$fail_subject_arr) && in_array($previous_subject_id, $fail_subject_arr) && !empty(@$previous_subject_id)) {
                                        $supplementary_subject_save_data[] = array(
                                            'student_id' => $student_id,
                                            'supplementary_id' => $supplementary_id,
                                            'subject_id' => @$value,
                                            'is_additional_subject' => @$is_additional_subject,
                                            'previous_subject_id' => @$previous_subject_id,
                                            'origional_subject_id' => @$origional_subject_id,
                                            'created_at' => date("Y-m-d H:i:s"),
                                            'updated_at' => date("Y-m-d H:i:s"),
                                            'exam_year' => @$exam_year,
                                            'exam_month' => @$exam_month,
                                            // 'temp' => 2,
                                        );
                                    } else {
                                        $supplementary_subject_save_data[] = array(
                                            'student_id' => $student_id,
                                            'supplementary_id' => $supplementary_id,
                                            'subject_id' => @$value,
                                            'is_additional_subject' => @$is_additional_subject,
                                            'previous_subject_id' => null,
                                            'origional_subject_id' => null,
                                            'created_at' => date("Y-m-d H:i:s"),
                                            'updated_at' => date("Y-m-d H:i:s"),
                                            'exam_year' => @$exam_year,
                                            'exam_month' => @$exam_month,
                                            // 'temp' => 3,
                                        );
                                    }


                                    // print_r($supplementary_subject_save_data);
                                    // echo "Counter : " . $counter++ . "<br> -------------------- <br>";

                                }
                            }

                            // dd($supplementary_subject_save_data);

                            $old_subject_data = SupplementarySubject::where("supplementary_id", "=", $supplementary_id)->delete();


                            if (SupplementarySubject::insert($supplementary_subject_save_data)) {
                                return redirect()->route('supp_fees_details', $estudent_id)->with('message', 'Data has been successfully submitted.');
                            } else {
                                return redirect()->back()->with('error', 'Data not submitted.');
                            }
                        }
                    } else {
                        return redirect()->back()->with('error', 'Data not submitted.');
                    }
                } else {
                    return redirect()->back()->withErrors($customerrors)->withInput($supp_subject_arr);
                }
            }

        } else {
            // @dd($findEnrollemntRulesResponse);

            $customerrors = @$findEnrollemntRulesResponse['customerrors'];
            return redirect()->back()->withErrors($customerrors)->withInput($request->all());
        }

        $documentInput = $this->getSupplementaryStudentRequriedDocument($student_id);


        $CustomComponent = new CustomComponent;
        $suppLateFeeDetails = $CustomComponent->_getSuppLateFeeDetails();
        $controller_obj = new Controller;
        $PassedSubjectArr = $controller_obj->getPassedSubject($student_id);

        if (@$master->adm_type == 5 && @$master->course == 12) {
            $subject_list = array();
            $subject_list[19] = "English (302)";
        } else if (@$master->adm_type == 5 && @$master->course == 10) {
            $subject_list = array();
            $subject_list[1] = "Hindi (201)";
            $subject_list[2] = "English (202)";
        }

        $arr1 = ($PassedSubjectArr);
        $arr2 = array_flip($subject_list);
        $diff1 = array_diff($arr1, $arr2);
        $subject_list = array_flip(array_diff($arr2, $arr1));


        return view('supplementary.supp_subjects_details', compact('subject_list_origional', 'suppLateFeeDetails', 'supplementary_id', 'adm_types', 'supp_subject_arr', 'subject_list', 'subject_list_two', 'supplementaryDetails', 'documentInput', 'master', 'model', 'estudent_id', 'student_id', 'page_title', 'studentDocumentPath', 'rsos_years', 'pre_qualifi'));
    }

    public function getParentAiCodeByEnrollment($enrollemnt = null)
    {
        $child_ai_code = '';
        if (!empty($enrollemnt)) {
            $child_ai_code = substr($enrollemnt, 0, 5);
        }
        $parent_ai_code = AiCenterMap::where('ai_code', '=', @$child_ai_code)->get()->pluck('parent_aicode')->toArray();
        return $parent_ai_code;
    }

    public function supp_fees_details(Request $request, $student_id)
    {
        /* student not allowed start */
        $role_id = @Session::get('role_id');
        if ($role_id == Config::get('global.student')) {
            //return redirect()->route('studentsdashboards')->with('error', 'Failed! You are not allowed!');
        }
        /* student not allowed end */
        $checkchangerequestsssupplementariesAllowOrNotAllow = $this->_checkchangerequestssupplementariesAllowOrNotAllow();
        $CustomComponent = new CustomComponent;
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $table = $model = "Supplementary";
        $page_title = "पूरक आवेदन शुल्क विवरण (Supplementary Application Fees Details.)";

        $master = Student::with('exam_subject', 'Address')->where('students.id', $student_id)->first();
        if (empty($master->id)) {
            return redirect()->route('supp_find_enrollment')->with('error', 'Failed! Student Enrollment not found,Please check student enrollment details.');
        }


        $studentdata = Student::findOrFail($student_id);
        $routeUrl = "supp_subjects_details";
        $previousTableName = "supplementaries";
        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

        $role_id = @Session::get('role_id');
        if ($role_id == Config::get('global.student')) {
            $exam_year = Config::get('global.form_supp_current_admission_session_id');
        }

        $SupplementaryIdArr = Supplementary::where('student_id', $student_id)->where('exam_year', '=', $exam_year)->where('exam_month', '=', $exam_month)->latest('id')->first(['id', 'supp_student_change_requests']);

        if (!$SupplementaryIdArr) {
            return redirect()->route('supp_subjects_details', $estudent_id)->with('error', 'Failed! Please first fill the details!');
        }

        //$isValid = $this->getRecordExistorNot($student_id,$previousTableName,$exam_year,$exam_month);
        $isValid = $this->getRecordExistorNot($student_id, $previousTableName);


        if (!$isValid) {
            return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! Please first fill the details!');
        }

        $isLockAndSubmit = $this->_isSuppFormLockAndSubmit($student_id);
        if ($isLockAndSubmit == 1) {
            return redirect()->route('supp_preview_details', Crypt::encrypt($student_id))->with('message', 'Form already successfully locked and submitted.');
        }
        if (@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$SupplementaryIdArr->supp_student_change_requests == 2) {
        } else {
            $custom_component_obj = new CustomComponent;
            $challanIdExist = $custom_component_obj->suppChallanTidAlreadyExist($student_id);
            $errMsg = null;
            $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
            if ($isAdminStatus == false) {
                if ($challanIdExist == true) {
                    return redirect()->route('supp_preview_details', Crypt::encrypt($studentdata['id']))->with('message', 'Payment has been already done');
                }
            }
            $supp_exam_month = Config::get('global.supp_current_admission_exam_month');
            $formOpenAllowOrNot = $custom_component_obj->checkSuppEntryAllowOrNot($supp_exam_month);

            if (!$formOpenAllowOrNot) {
                return redirect()->route('supp_find_enrollment')->with('error', 'Failed! Supplementary Form date has been closed.');
            }
        }

        $isLockAndSubmit = $this->_isSuppFormLockAndSubmit($student_id);
        if ($isLockAndSubmit == 1) {
            return redirect()->route('supp_preview_details', Crypt::encrypt($student_id))->with('message', 'Form already successfully locked and submitted.');
        }
        if (@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$SupplementaryIdArr->supp_student_change_requests == 2) {
        } else {
            $errMsg = null;
            if (!$formOpenAllowOrNot) {
                $errMsg = 'पूरक आवेदन पत्र की तिथि समाप्त कर दी गई है।(The supplementary Application form date is closed.)';
                return redirect()->route('aicenterdashboard')->with('error', $errMsg);
            }
        }

        if (empty($master->id)) {
            return redirect()->route('supp_find_enrollment')->with('error', 'Failed! Student Enrollment not found,Please check student enrollment details.');
        }

        if (count($request->all()) > 0) {

            $supp_stream = Config::get("global.supp_stream");
            $exam_year = CustomHelper::_get_selected_sessions();
            if (@$exam_year) {

            } else {
                $exam_year = Config::get('global.form_supp_current_admission_session_id');
            }

            $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

            $conditions['supplementaries.exam_year'] = $exam_year;
            $conditions['supplementaries.exam_month'] = $exam_month;
            // $conditions['supplementaries.stream'] = $supp_stream;
            $conditions['supplementaries.student_id'] = $student_id;
            // dd($conditions);
            $supplementary_data = Supplementary::where($conditions)->get()->first()->toArray();

            if (empty($supplementary_data['id'])) {
                return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! You are not allowed!');
            }
            $supplementary_id = $supplementary_data['id'];

            // $master_supplementary_fees =  DB::table('master_supplementary_fees')
            // 	->where('exam_year',$exam_year)
            // 	->where('exam_month',$studentdata->exam_month)
            // 	->where('course',$studentdata->course)
            // 	->first();

            $master_fee = $CustomComponent->_getSuppFeeDetailsForDispaly($student_id);

            if (@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$SupplementaryIdArr->supp_student_change_requests == 2) {
                $changereqestsupptotalfess = $master_fee['final_fees'] - $master_fee['late_fees'];
                $studentfeedata = array(
                    'subject_change_fees' => $master_fee['subject_change_fees'],
                    'exam_fees' => $master_fee['exam_subject_fees'],
                    'practical_fees' => $master_fee['practical_fees'],
                    'forward_fees' => $master_fee['forward_fees'],
                    'online_fees' => $master_fee['online_services_fees'],
                    'late_fees' => 0,
                    'total_fees' => $changereqestsupptotalfess
                );

            } else {
                $studentfeedata = array(
                    'subject_change_fees' => $master_fee['subject_change_fees'],
                    'exam_fees' => $master_fee['exam_subject_fees'],
                    'practical_fees' => $master_fee['practical_fees'],
                    'forward_fees' => $master_fee['forward_fees'],
                    'online_fees' => $master_fee['online_services_fees'],
                    'late_fees' => $master_fee['late_fees'],
                    'total_fees' => $master_fee['final_fees']
                );
            }


            $custom_component_obj = new CustomComponent;
            $isStudent = $custom_component_obj->_getIsStudentLogin();
            if (@$isStudent) {
                $studentfeedata['is_self_filled'] = 1;
                $supplementary_save_data['last_updated_by_user_id'] = $student_id;
            } else {
                $studentfeedata['is_self_filled'] = null;
                $studentfeedata['last_updated_by_user_id'] = @Auth::id();
            }

            $supplementary_fee_update = Supplementary::where('id', $supplementary_id)->update($studentfeedata);
            if (@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$SupplementaryIdArr->supp_student_change_requests == 2) {
                $suppchangerequestchecklatefeedupdate = $this->suppchangerequestchecklatefeedupdate($student_id);
            }
            if (@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$SupplementaryIdArr->supp_student_change_requests == 2) {
                $studentSubjectfeedata = array(
                    'student_id' => $student_id,
                    'supplementary_id' => $supplementary_id,
                    'total' => $changereqestsupptotalfess
                );
            } else {
                $studentSubjectfeedata = array(
                    'student_id' => $student_id,
                    'supplementary_id' => $supplementary_id,
                    'total' => $master_fee['final_fees'],
                );
            }


            $alredyPresent = SuppStudentFees::where('student_id', $student_id)->where('supplementary_id', $supplementary_id)->first();


            if (@$alredyPresent->id) {
                $studentfeesupdate = SuppStudentFees::where('student_id', $student_id)->where('supplementary_id', $supplementary_id)->update($studentSubjectfeedata);
            } else {
                $studentfeesupdate = SuppStudentFees::updateOrCreate($studentSubjectfeedata);
            }
            if ($supplementary_fee_update) {

                return redirect()->route('supp_preview_details', Crypt::encrypt($student_id))->with('message', $model . ' successfully saved');
            } else {
                return redirect()->route('supp_preview_details')->with('error', 'Failed! ' . $model . ' not saved');
            }
        }

        $exam_year = CustomHelper::_get_selected_sessions();

        $master = $CustomComponent->_getSuppFeeDetailsForDispaly($student_id);


        return view('supplementary.supp_fee_details', compact('master', 'model', 'estudent_id', 'student_id', 'page_title', 'checkchangerequestsssupplementariesAllowOrNotAllow', 'SupplementaryIdArr'));
    }

    public function supp_preview_details(Request $request, $student_id)
    {
        /* student not allowed start */
        $role_id = @Session::get('role_id');
        if ($role_id == Config::get('global.student')) {
            //return redirect()->route('studentsdashboards')->with('error', 'Failed! You are not allowed!');
        }
        /* student not allowed end */

        $combo_name = 'supp_verfication_status';
        $supp_verfication_status = $this->master_details($combo_name);
        $suppchangerequestcheckfees = array();
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $exam_year = Session::get("current_admission_sessions");
        $role_id = @Session::get('role_id');
        if ($role_id == Config::get('global.student')) {
            $exam_year = Config::get('global.form_supp_current_admission_session_id');
        }

        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
        $SupplementaryIdArr = Supplementary::where('student_id', $student_id)->where('exam_year', '=', $exam_year)->where('exam_month', '=', $exam_month)->latest('id')->first(['id', 'supp_student_change_requests', 'exam_month']);
        @$changerequeststudent = SuppChangeRequestStudents::where('student_id', $student_id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->where('supp_id', @$SupplementaryIdArr->id)->orderBy('id', 'desc')->first(['id', 'supp_student_update_application']);
        $checkchangerequestsssupplementariesAllowOrNotAllow = $this->_checkchangerequestssupplementariesAllowOrNotAllow();
        if ($checkchangerequestsssupplementariesAllowOrNotAllow == true) {
            $suppchangerequestcheckfees = $this->suppchangerequestcheckfees($student_id);
        }
        $supp_id = null;
        if (!empty($SupplementaryIdArr->id)) {
            $supp_id = $SupplementaryIdArr->id;
        }
        // dd($supp_id);//326257
        $suppData = Supplementary::where('id', $supp_id)->first();
        $suppVerifcationData = array();
        if (@$suppData->is_department_verify == 2 && @$suppData->is_aicenter_verify == 2) {
        } else {
            $suppVerifcationData = DB::table('supplementary_verifications')->where('supplementary_id', @$supp_id)->whereNull('deleted_at')->orderBy('id', 'desc')->first();
        }


        //dd($suppVerifcationData->aicenter_status);

        $ExamSubjectYearArr = ExamSubject::where('student_id', $student_id)->latest('exam_year')->first(['exam_year', 'exam_month']);

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
            return redirect()->route('supp_find_enrollment')->with('error', 'Failed! Student Enrollment not found,Please check student enrollment details.');
        }

        $table = $model = "Application";
        $page_title = 'Supplementary Application Preview';
        $documentErrors = null;

        if (@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$SupplementaryIdArr->supp_student_change_requests == 2) {

        } else {
            $custom_component_obj = new CustomComponent;

            $suppFeeEnteredOrNot = false;
            /*
		if(@$master->exam_year && $master->exam_year <= 123){
			$suppFeeEnteredOrNot = true;
		}else{ */
            $suppFeeEnteredOrNot = $custom_component_obj->checkSuppFeeEntredOrNot($student_id, $supp_id);
            /* } */

            if (!$suppFeeEnteredOrNot) {
                return redirect()->route('supp_fees_details', $estudent_id)->with('error', 'Failed! Supplementary Fee does not saved.');
            }
        }


        //$sub_code = $this->getSubjectCode($student_id);

        $supp_master = $this->getSupplementaryStudentDetails($student_id);


        /* Replace string with X start */
        $custom_component_obj = new CustomComponent;
        //$supp_master['personalDetails']['data']['mobile']['value'] = $custom_component_obj->_replaceTheStringWithX(@$supp_master['personalDetails']['data']['mobile']['value']);
        /* Replace string with X end */


        // @dd($supp_master);

        // $documentErrors = $this->getPendingDocuemntDetails($student_id);
        $documentErrors = null;

        $mastersuppcount = SupplementarySubject::where('student_id', $student_id)->where('is_additional_subject', '<>', '')->where('supplementary_id', $supp_id)->count();


        //$masterSuppExamYears = ExamSubject::where('student_id',$student_id)
        //->where('final_result','P',)->latest('exam_year')->first(['exam_year','exam_month']);

        $masterSuppExamYears = ExamSubject::where('student_id', $student_id)
            ->where('final_result', 'P')->orderBy('exam_year', 'desc')->orderBy('exam_month', 'asc')->first(['exam_year', 'exam_month']);

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

        $isZeroSuppFeeStudent = $custom_component_obj->_isZeroSuppFeeStudent($student_id);
        $application_fee = @$masterrecord->total_fees;

        if (@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$SupplementaryIdArr->supp_student_change_requests == 2) {

        } else {
            if ($isZeroSuppFeeStudent) {
            } else {
                if (@$masterrecord && $application_fee == 0) {
                    return redirect()->route('supp_fees_details', $estudent_id)->with('error', 'Failed! Supplementary Fee does not saved.');
                }
            }
        }

        if (count($request->all()) > 0) {
            if ($checkchangerequestsssupplementariesAllowOrNotAllow == true && $SupplementaryIdArr->supp_student_change_requests == 2) {

            } else {
                if ($isZeroSuppFeeStudent) {
                } else {
                    if ($application_fee == 0) {
                        return redirect()->route('supp_fees_details', $estudent_id)->with('error', 'Failed! Supplementary Fee does not saved.');
                    }
                }
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

            if (@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$SupplementaryIdArr->supp_student_change_requests == 2) {
            } else {
                if ($isZeroSuppFeeStudent) {
                } else {

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
                }
            }

            if (@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$SupplementaryIdArr->supp_student_change_requests == 2 && $suppchangerequestcheckfees == 'true') {
                @$smsStatus = $this->_suppchangerequestsendLockSubmittedMessage($student_id);
                $locksubmitted_date = date("Y-m-d H:i:s");
                $supplementaryarray = [
                    'locksumbitted' => $locksumbitted,
                    'locksubmitted_date' => $locksubmitted_date,
                    'update_supp_change_requests_challan_tid' => NULL,
                    'update_supp_change_requests_submitted' => NULL,
                    'supp_change_fee_status' => NULL,
                    'supp_change_fee_paid_amount' => NULL,
                    'aicenter_verify_datetime' => NULL,
                    'aicenter_verify_user_id' => NULL,
                    'department_verify_user_id' => NULL,
                    'department_verify_datetime' => NULL,
                ];
            } elseif (@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$SupplementaryIdArr->supp_student_change_requests == 2 && @$suppchangerequestcheckfees == 'false') {
                $is_aicenter_verify = 2;
                $is_department_verify = 1;
                $locksubmitted_date = date("Y-m-d H:i:s");
                $supplementaryarray = [
                    'locksumbitted' => $locksumbitted,
                    'locksubmitted_date' => $locksubmitted_date,
                    'is_department_verify' => $is_department_verify,
                    'aicenter_verify_datetime' => NULL,
                    'aicenter_verify_user_id' => NULL,
                    'department_verify_user_id' => NULL,
                    'department_verify_datetime' => NULL,
                    'is_aicenter_verify' => $is_aicenter_verify,
                    'supp_student_change_requests' => NULL,
                    'update_supp_change_requests_challan_tid' => NULL,
                    'update_supp_change_requests_submitted' => NULL,
                    'supp_change_fee_status' => NULL,
                    'supp_change_fee_paid_amount' => NULL,
                ];
                $suppstudentTarils = [
                    'student_id' => $student_id,
                    'exam_year' => $exam_year,
                    'exam_month' => $exam_month,
                    'supp_student_change_request_id' => @$changerequeststudent->id,
                    'challan_tid' => NULL,
                    'prn' => NULL,
                    'amount' => NULL,
                    'supp_change_request_status' => 'NO',
                ];
                $suppchangerequeststudenttarils = SuppChangeRequestStudentTarils::create($suppstudentTarils);
            } else {
                $is_aicenter_verify = 2;
                $is_department_verify = null;
                $locksubmitted_date = date("Y-m-d H:i:s");
                $supplementaryarray = [
                    'locksumbitted' => $locksumbitted,
                    'locksubmitted_date' => $locksubmitted_date,
                    'is_department_verify' => $is_department_verify,
                    'is_aicenter_verify' => $is_aicenter_verify,
                    'enrollment' => $masterrecord->enrollment
                ]; 
                if ($isZeroSuppFeeStudent) {
                    $supplementaryarray['is_eligible'] = 0;
                    $supplementaryarray['is_aicenter_verify'] = 2;
                    $supplementaryarray['is_department_verify'] = 1;
                } else {

                }
            }
            $custom_component_obj = new CustomComponent;
            $isStudent = $custom_component_obj->_getIsStudentLogin();
            if (@$isStudent) {
                $studentfeedata['is_self_filled'] = 1;
                $supplementary_save_data['last_updated_by_user_id'] = $student_id;
            } else {
                $studentfeedata['is_self_filled'] = null;
                $studentfeedata['last_updated_by_user_id'] = @Auth::id();
            }

            //$supplementarylocksumbitted = Supplementary::where('student_id',$student_id)->update($supplementaryarray);
            $supplementarylocksumbitted = Supplementary::where('id', $supplementary_id)->where('student_id', $student_id)->update($supplementaryarray);

            if ($supplementarylocksumbitted) {
                if (@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$SupplementaryIdArr->supp_student_change_requests == 2) {
                } else {
                    $this->_sendSupplementaryLockSubmittedMessage($student_id);
                }
                if ($isZeroSuppFeeStudent) {
                    return redirect()->route('supp_preview_details', Crypt::encrypt($student_id))->with('message', 'Your application has been successfully Lock & Sumbitted for Verfication.');
                } else {
                    return redirect()->route('supp_preview_details', Crypt::encrypt($student_id))->with('message', 'Your application has been successfully Lock & Sumbitted. Please procced for online payment for complete your applicaiton!');
                }
            } else {
                return redirect()->route('supp_preview_details')->with('error', 'Failed! locksumbitted  details has been not submitted');
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
            return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! Please first fill the details!');
        }

        $CustomComponent = new CustomComponent;
        $getlatefeesdate = 0;
        $suppLateFeeDetails = null;
        if (@$masterrecord->stream) {
            $suppLateFeeDetails = $CustomComponent->_getSuppLateFeeDetails(@$masterrecord->stream, @$masterStudent->gender_id);
            $getlatefeesdate = $this->getfeesdates();
        }

        $isAllowSuppSubjectDelete = false;
        if ($role_id == Config::get('global.super_admin_id') || $role_id == Config::get('global.examination_department')) {
            $isAllowSuppSubjectDelete = true;
        }
        return view('supplementary.supp_preview_details', compact('exam_month', 'isAllowSuppSubjectDelete', 'isZeroSuppFeeStudent', 'suppVerifcationData', 'supp_verfication_status', 'supp_master', 'suppData', 'suppLateFeeDetails', 'mastersupp', 'result', 'application_fee', 'mastersuppcount', 'masterrecord', 'student_declaration', 'model', 'master', 'subject_list', 'estudent_id', 'student_id', 'documentErrors', 'page_title', 'checkchangerequestsssupplementariesAllowOrNotAllow', 'SupplementaryIdArr', 'suppchangerequestcheckfees', 'changerequeststudent'));
    }

    public function getfeesdates()
    {
        $stream = config("global.supp_current_admission_exam_month");
        $data = DB::table('exam_late_fee_dates')
            ->where('is_supplementary', 1)
            ->where('stream', $stream)
            ->get();
        return $data;
    }

    public function supp_generate_student_pdf(Request $request, $student_id = null)
    {
        $table = $model = "Student";
        $page_title = 'View Details';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);


        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');


        $suppchangerequeststudentgetdatachangerequests = SuppChangeRequestStudents::join('supp_change_request_erequests', 'supp_change_request_erequests.supp_student_change_request_id', '=', 'supp_change_request_students.id')->
        where('supp_change_request_students.student_id', $student_id)->where('supp_change_request_students.supp_student_update_application', 1)->where('supp_change_request_erequests.rtype', 1)->where('supp_change_request_erequests.status', 1)->whereNotNull('supp_change_request_erequests.challan_tid')->whereNotNull('supp_change_request_erequests.prn')->get();


        $suppchangerequeststudentgetdate = count($suppchangerequeststudentgetdatachangerequests);

        if (count($request->all()) > 0) {
            $inputs = $request->all();
            if (@$inputs['student_login'] && $inputs['student_login'] == true) {
                @$supp_id = decrypt(@$inputs['supp_id']);
                $supplementaryDetails = Supplementary::where('supplementaries.id', @$supp_id)->first();
                $exam_year = $supplementaryDetails->exam_year;
                $exam_month = $supplementaryDetails->exam_month;
            }

        }

        $master = Student::with('application', 'studentfees', 'document', 'address', 'admission_subject', 'toc_subject')
            ->where('id', $student_id)->first();
        $role_id = @Session::get('role_id');
        $examSubjectDetails = ExamSubject::where('student_id', $student_id)
            ->first();
        if (@$exam_year) {

        } else {
            $exam_year = Config::get('global.form_supp_current_admission_session_id');
        }
        $supplementaryDetails = Supplementary::with('SupplementarySubject')
            ->where('supplementaries.student_id', $student_id)
            ->where('supplementaries.exam_year', $exam_year)
            ->where('supplementaries.exam_month', $exam_month)
            ->first();

        $combination = '';
        if (isset($supplementaryDetails->exam_month) && isset($supplementaryDetails->exam_year)) {
            $combination = $supplementaryDetails->exam_month . ' ' . $supplementaryDetails->exam_year;
        }


        $marksheetCustomComponentObj = new MarksheetCustomComponent;
        $displayExamMonthYear = $marksheetCustomComponentObj->getDisplayExamMonthYear($combination);
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
        $combo_name = 'supp_verfication_status';
        $supp_verfication_status = $this->master_details($combo_name);
        $combo_name = 'yesno';
        $yesno = $this->master_details($combo_name);
        $yesno[""] = "No";
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
            ->where('final_result', 'P')->orderBy('exam_year', 'desc')->orderBy('exam_month', 'asc')->first(['exam_year', 'exam_month']);
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
        //dd($master->supplementary);
        /* Replace string with X start */
        $custom_component_obj = new CustomComponent;
        $master->mobile = $custom_component_obj->_replaceTheStringWithX(@$master->mobile);
        /* Replace string with X end */
        $masterExamYear = $supplementaryDetails->exam_year;
        $masterExamMonth = $supplementaryDetails->exam_month;
        $masterCourse = $supplementaryDetails->course;
        $path = public_path("Supplementarypdf/" . $masterExamYear . "/" . $masterExamMonth . "/" . $masterCourse . "/");

        $suppVerifcationData = array();

        if ($master->supplementary->is_department_verify == 2 && $master->supplementary->is_aicenter_verify == 2) {
        } else {
            $suppVerifcationData = DB::table('supplementary_verifications')->where('supplementary_id', @$master->supplementary->id)->whereNull('deleted_at')->orderBy('id', 'desc')->first();
        }

        File::makeDirectory($path, $mode = 0777, true, true);
        $path .= "SupplementaryForm-" . $student_id . ".pdf";
        //return view('supplementary.supp_generate_student_pdf', compact('displayExamMonthYear','supplementaryDetails','subject_code_list','master_subject_details','subject_list','master','studentDocumentPath','student_id', 'page_title', 'estudent_id', 'model','gender_id','categorya','nationality','religion','disability','dis_adv_group','midium','rural_urban','employment','pre_qualifi','adm_types','course','exam_session','mastersupp','mastersuppcount','result'));
        $pdf = PDF::loadView('supplementary.supp_generate_student_pdf', compact('displayExamMonthYear', 'role_id', 'supplementaryDetails', 'suppVerifcationData', 'supp_verfication_status', 'subject_code_list', 'master_subject_details', 'subject_list', 'master', 'studentDocumentPath', 'student_id', 'page_title', 'estudent_id', 'model', 'gender_id', 'categorya', 'nationality', 'religion', 'disability', 'dis_adv_group', 'midium', 'yesno', 'rural_urban', 'employment', 'pre_qualifi', 'adm_types', 'course', 'exam_session', 'mastersupp', 'mastersuppcount', 'result', 'exam_months', 'rsos_years', 'suppchangerequeststudentgetdate', 'suppchangerequeststudentgetdatachangerequests'));

        if (file_exists($path)) {
            //return Response::download($path);//lock and submitted
        }
        $pdf->save($path, $pdf, true);
        return (Response::download($path));
        //return view('supplementary.supp_generate_student_pdf', compact('mastersuppcount','mastersupp','master_subject_details','subject_list','master','studentDocumentPath','student_id', 'page_title', 'estudent_id', 'model','gender_id','categorya','nationality','religion','disability','dis_adv_group','midium','rural_urban','employment','pre_qualifi','adm_types','course','exam_session','result','exam_months'));
    }

    public function supp_delete($student_id)
    {
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $supplementarie_data = Supplementary::where('student_id', $student_id)->delete();
        $supplementarie_data = SupplementarySubject::where('student_id', $student_id)->delete();
        return redirect()->back()->with('success', 'Record successfully Deleted');
    }

    public function supp_masterstudent(Request $request)
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
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);

        $yes_no = $this->master_details('yesno');
        $title = "Admission Report";
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
                'status' => false,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadSupplementaryApplicationPdf',
                'status' => false
            ),
        );
        $district_list = $this->districtsByState();

        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            // array(
            // 	"lbl" => "Ai Code",
            // 	'fld' => 'ai_code',
            // 	'input_type' => 'text',
            // 	'placeholder' => "Ai Code",
            // 	'dbtbl' => 'students',
            // ),
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
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Stream ",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream Type',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Admission ",
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
                'options' => $yes_no,
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
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'supplementaries',
            ), array(
                "lbl" => "AI Code",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'placeholder' => "AI Code",
                'dbtbl' => 'supplementaries',
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
            array(
                "lbl" => "Stream ",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id
            ),
            array(
                "lbl" => "Admission ",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_types
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no
            ),
            array(
                "lbl" => "Fees Amount",
                'fld' => 'fee_paid_amount'
            ),
            array(
                "lbl" => "Challan Number",
                'fld' => 'challan_tid'
            ),
            array(
                "lbl" => "Submitted",
                'fld' => 'submitted'
            ),
            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district_list
            ),
        );


        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["supplementaries.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $conditions["supplementaries.user_id"] = @Auth::user()->id;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'supplementaries',
            );
            $filters[] = array(
                "lbl" => "Student id",
                'fld' => 'id',
                'input_type' => 'text',
                'placeholder' => "Student id",
                'dbtbl' => 'supplementaries',
            );
            // $tableData[] = array(
            // 	"lbl" => "Ai Center",
            // 	'fld' => 'ai_code',
            // 	'input_type' => 'select',
            // 	'options' => $aiCenters,
            // 	'placeholder' => 'Ai Center',
            // 	'dbtbl' => 'users'
            // );
        }

        if (in_array("application_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),

            );

            $deleteVal = false;
            $masterIP = '10.68.181.236';
            if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == $masterIP) {
                $masterIP2 = '10.68.181.213';
                $masterIP3 = '10.68.181.229';
                $masterIP4 = '10.68.181.249';
                $masterIP5 = '10.68.181.51';
                if ($_SERVER['REMOTE_ADDR'] == $masterIP || $_SERVER['REMOTE_ADDR'] == $masterIP3 || $_SERVER['REMOTE_ADDR'] == $masterIP2) {
                    $deleteVal = true;
                }
            } else if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'rsosadmission.rajasthan.gov.in' || $_SERVER['HTTP_HOST'] == 'www.rsosadmission.rajasthan.gov.in')) {
                if ($_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP) {
                    $deleteVal = true;
                }
            }
            $deleteVal = true;
            if ($deleteVal == true) {
                $actions[] = array(
                    'fld' => 'suppstudentrejectdelete', //For active studentdeleteactive
                    'class' => 'delete-confirm2',
                    'icon' => '<i class="material-icons" title="Click here to Delete.">delete</i>',
                    'fld_url' => '../supp/suppstudentrejectdelete/#student_id#' //For active studentdeleteactive
                );
            }

            $unlockVal = true;
            $masterIP = '10.68.181.236';
            $masterIP2 = '10.68.181.213';
            $masterIP3 = '10.68.181.229';
            $masterIP4 = '10.68.181.249';
            $masterIP5 = '10.68.181.51';
            if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == $masterIP) {

                if ($_SERVER['REMOTE_ADDR'] == $masterIP || $_SERVER['REMOTE_ADDR'] == $masterIP2 || $_SERVER['REMOTE_ADDR'] == $masterIP3) {
                    $unlockVal = true;
                }
            } else if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'rsosadmission.rajasthan.gov.in' || $_SERVER['HTTP_HOST'] == 'www.rsosadmission.rajasthan.gov.in')) {
                if ($_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP || $_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP3) {
                    $unlockVal = true;
                }
            }
            // if($unlockVal == true){
            // 	$actions[] = array(
            // 		'fld' => 'studentunlock', //For active studentdeleteactive
            // 		'class' => 'unlock-student',
            // 		'icon' => '<i class="material-icons md-18" title="Click here to Unlock.">lock</i>',
            // 		'fld_url' => 'studentunlock/#id#' //For active studentdeleteactive
            // 	);
            // }

        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../supp/supp_preview_details/#student_id#'
                ),
            );
        }

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);

        $master = $custom_component_obj->getSupplementaryApplicationData($formId);

        return view('student.index', compact('actions', 'master', 'tableData', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'course', 'stream_id', 'adm_types'));
    }

    public function suppstudentrejectdelete(Request $request, $student_id)
    {

        $combo_name = 'student_delete_reason';
        $student_delete_reasons = $this->master_details($combo_name);

        if (count($request->all()) > 0) {
            $modelObj = new Supplementary;
            $validator = Validator::make($request->all(), $modelObj->rulesvalidatiton);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            $id = Auth::user()->id;
            $ldate = date('Y-m-d H:i:s');
            $custom_data = array(
                'remarks' => $request->remarks,
                'is_eligible' => 0,
                'deleted_by_user_id' => $id,
                'deleted_at' => $ldate,
                'deleted_reason' => $request->deleted_reason,
                'deleted_date_by_user' => $ldate

            );

            $custom_dataupdate = array(
                'deleted_at' => $ldate,
            );

            $student_ids = Crypt::decrypt($student_id);

            if ($request->deleted_reason == 1) {
                $Student = Supplementary::where('student_id', $student_ids)->update($custom_data);
                $Studentdetele = StudentAllotment::where('student_id', '=', $student_ids)->delete();
            } elseif ($request->deleted_reason == 2) {
                $Student = Supplementary::where('student_id', $student_ids)->update($custom_data);
                $Studentdetele = StudentAllotment::where('student_id', $student_ids)->update($custom_dataupdate);

            } else {
                $Student = Supplementary::where('student_id', $student_ids)->update($custom_data);
            }

            if ($Student) {
                return redirect()->route('supp_masterstudent')->with('message', 'Student Reject successfully saved');
            } else {
                return redirect()->back()->with('error', 'Failed! Student not created');
            }
        }

        return view('supplementary.suppstudentrejectdelete', compact('student_delete_reasons', 'student_id'));

    }

    public function livetableupdate(Request $request)
    {
        $livetableupdate = DB::table('students')->where('id', $request->id)->update([$request->name => $request->value]);
        return $livetableupdate;
    }

    public function documentdownload($student_id = null, $type = null, $marksheet_doc = null)
    {

        $student_id = Crypt::decrypt($student_id);
        // $type = Crypt::decrypt($type);
        // $marksheet_doc = Crypt::decrypt($marksheet_doc);


        if (@$type == 'supplementary') {
            $filename = 'SupplementaryForm-' . $student_id . '.pdf';
            $current_folder_year = $this->getCurrentYearFolderName();
            $studentcurrentmonth = Config::get('global.supp_current_admission_exam_month');
            $path = public_path("supplementary_documents/" . $current_folder_year . "/" . $studentcurrentmonth . "/" . $student_id . "/" . $marksheet_doc);
            return Response::download($path);
        }
    }


    public function supp_verfication_document(Request $request)
    {
        $page_title = 'Verfication Document Details';
        $model = null;
        $combo_name = 'student_supplementary_document_path';
        $student_document_path = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $current_folder_year = $this->getCurrentYearFolderName();
        $selected_student_enrollment = @Session::get('selected_student_enrollment_by_student');
        $getstudent_id = Auth::guard('student')->user()->id;
        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get('global.supp_current_admission_exam_month');
        $getcurrentsupplementariesdata = Supplementary::where('student_id', $getstudent_id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->first(['student_id', 'id', 'is_aicenter_verify', 'is_department_verify', 'course']);
        $supp_id = @$getcurrentsupplementariesdata->id;
        $supplementaryDetails = DB::table('supplementary_verification_documents')->where('supplementary_id', $supp_id)->orderBy('id', 'DESC')->first(['id', 'supp_doc', 'sec_marksheet_doc']);
        $studentDocumentPath = $student_document_path[2] . $current_folder_year . '/' . $exam_month . '/' . $getstudent_id . '/';
        $documentInput = $this->getSupplementaryStudentRequriedDocument($getstudent_id);
        //get all old upload list of verification docs.
        //get last record from supp_verifictionas table. supp_id
        $suppVerifcationData = DB::table('supplementary_verifications')->where('supplementary_id', @$supp_id)->whereNull('deleted_at')->orderBy('id', 'desc', 'aicenter_rejected_marksheet_document', 'department_rejected_marksheet_document')->first();
        if (count($request->all()) > 0) {

            $student_id = $getcurrentsupplementariesdata->student_id;
            $supplementary_id = $getcurrentsupplementariesdata->id;
            $is_aicenter_verify_check = $getcurrentsupplementariesdata->is_aicenter_verify;
            $is_department_verify_check = $getcurrentsupplementariesdata->is_department_verify;
            $verify_check_value = '4';
            if ($is_aicenter_verify_check == 3) {
                $verify_check = array('is_aicenter_verify' => $verify_check_value);
            } elseif ($is_department_verify_check == 3) {
                $verify_check = array('is_department_verify' => $verify_check_value);
            }

            if (@$suppVerifcationData->aicenter_rejected_marksheet_document == 3 || @$suppVerifcationData->department_rejected_marksheet_document == 3) {
                if (empty($request->marksheet_doc_hidden) || !empty($request->marksheet_doc)) {
                    $rules = ['marksheet_doc' => 'required|mimes:jpg,png,jpeg,gif,pdf,svg|between:10,100'];
                    $validation = $this->validate($request, $rules);
                    $filename = 'supp_marksheet_' . $supplementary_id . '_' . $student_id . '_' . date("dmY-h-i-sa") . '.' . $request->marksheet_doc->extension();
                    $request->marksheet_doc->move(public_path($studentDocumentPath), $filename);
                } else {
                    $filename = $request->marksheet_doc_hidden;
                }
                if (empty($request->sec_marksheet_doc_hidden) || !empty($request->sec_marksheet_doc)) {
                    $rules = ['sec_marksheet_doc' => 'required|mimes:jpg,png,jpeg,gif,pdf,svg|between:10,100'];
                    $validation = $this->validate($request, $rules);
                    $sec_filename = 'sec_supp_marksheet_' . $supplementary_id . '_' . $student_id . '_' . date("dmY-h-i-sa") . '.' . $request->sec_marksheet_doc->extension();
                    $request->sec_marksheet_doc->move(public_path($studentDocumentPath), $sec_filename);
                } else {
                    $sec_filename = $request->sec_marksheet_doc_hidden;
                }
                $custom_data = array(
                    'supplementary_id' => $supplementary_id,
                    'supp_doc' => $filename,
                    'sec_marksheet_doc' => $sec_filename,
                );
            } elseif ($suppVerifcationData->aicenter_rejected_marksheet_document == 2 || @$suppVerifcationData->department_rejected_marksheet_document == 2) {
                if (empty($request->marksheet_doc_hidden) || !empty($request->marksheet_doc)) {
                    $rules = ['marksheet_doc' => 'required|mimes:jpg,png,jpeg,gif,pdf,svg|between:10,100'];
                    $validation = $this->validate($request, $rules);
                    $filename = 'supp_marksheet_' . $supplementary_id . '_' . $student_id . '_' . date("dmY-h-i-sa") . '.' . $request->marksheet_doc->extension();
                    $request->marksheet_doc->move(public_path($studentDocumentPath), $filename);
                } else {
                    $filename = $request->marksheet_doc_hidden;
                }
                $custom_data = array(
                    'supplementary_id' => $supplementary_id,
                    'supp_doc' => $filename,
                );
            } elseif (@$suppVerifcationData->aicenter_rejected_marksheet_document == 1 || @$suppVerifcationData->department_rejected_marksheet_document == 1) {
                if ($getcurrentsupplementariesdata->course == 12) {
                    if (empty($request->sec_marksheet_doc_hidden) || !empty($request->sec_marksheet_doc)) {
                        $rules = ['sec_marksheet_doc' => 'required|mimes:jpg,png,jpeg,gif,pdf,svg|between:10,100'];
                        $validation = $this->validate($request, $rules);
                        $sec_filename = 'sec_supp_marksheet_' . $supplementary_id . '_' . $student_id . '_' . date("dmY-h-i-sa") . '.' . $request->sec_marksheet_doc->extension();
                        $request->sec_marksheet_doc->move(public_path($studentDocumentPath), $sec_filename);
                    } else {
                        $sec_filename = $request->sec_marksheet_doc_hidden;
                    }
                    $custom_data = array(
                        'supplementary_id' => $supplementary_id,
                        'sec_marksheet_doc' => $sec_filename,
                    );
                } else if ($getcurrentsupplementariesdata->course == 10) {
                    if (empty($request->marksheet_doc_hidden) || !empty($request->marksheet_doc)) {
                        $rules = ['marksheet_doc' => 'required|mimes:jpg,png,jpeg,gif,pdf,svg|between:10,100'];
                        $validation = $this->validate($request, $rules);
                        $filename = 'supp_marksheet_' . $supplementary_id . '_' . $student_id . '_' . date("dmY-h-i-sa") . '.' . $request->marksheet_doc->extension();
                        $request->marksheet_doc->move(public_path($studentDocumentPath), $filename);
                    } else {
                        $filename = $request->marksheet_doc_hidden;
                    }
                    $custom_data = array(
                        'supplementary_id' => $supplementary_id,
                        'supp_doc' => $filename,
                    );
                }
            }
            $document = DB::table('supplementary_verification_documents')->insert($custom_data);
            $supplementariesupdatedata = DB::table('supplementaries')->where('id', $supplementary_id)->where('student_id', $student_id)->update($verify_check);
            if ($document && $supplementariesupdatedata) {
                return redirect()->route('studentsdashboards')->with('message', 'Clarification has been successfully submitted with us we further proceed on the updated document.');
            } else {
                return redirect()->route('studentsdashboards')->with('error', 'Clarification not submitted.');
            }
        }


        return view('supplementary.supp_verfication_document', compact('page_title', 'documentInput', 'model', 'supplementaryDetails', 'studentDocumentPath', 'suppVerifcationData', 'getcurrentsupplementariesdata'));
    }

    public function supp_doc_mark_rejected_verfication(Request $request, $supp_id = null, $type = null)
    {
        $table = $model = "Supplementary";
        $page_title = 'Student Mark Rejected';
        $msg = "Student has been marked rejected successfully.";
        $esupp_id = $supp_id;
        $supp_id = Crypt::decrypt($supp_id);
        $student = Supplementary::where('id', $supp_id)->first(['student_id', 'course']);

        $combo_name = 'supp_rejection_value_status';
        $supp_rejection_value_status = $this->master_details($combo_name);
        if (@$student->course && $student->course == 10) {
            unset($supp_rejection_value_status[2]);
            unset($supp_rejection_value_status[3]);
        }

        $estudent_id = Crypt::encrypt($student->student_id);
        $role_id = @Session::get('role_id');

        if (count($request->all()) > 0) {
            $this->validate($request, [
                'aicenter_remark' => 'required',
                'aicenter_rejected_marksheet_document' => 'required|numeric',
            ],
                [
                    'aicenter_remark.required' => 'Please enter remarks.',
                    'aicenter_rejected_marksheet_document.required' => 'Please select which docuement is found wrong.'
                ]
            );

            if ($role_id == Config::get('global.aicenter_id')) {
                $custom_data = array(
                    'is_aicenter_verify' => $type,
                    'aicenter_verify_user_id' => @Auth::id(),
                    'aicenter_verify_datetime' => date("Y-m-d H:i:s"),
                );
                $custom_data_verifications = array(
                    'aicenter_verify_user_id' => @Auth::id(),
                    'aicenter_remark' => $request->aicenter_remark,
                    'aicenter_rejected_marksheet_document' => $request->aicenter_rejected_marksheet_document,
                    'aicenter_verify_datetime' => date("Y-m-d H:i:s"),
                    'supplementary_id' => $supp_id,
                    'aicenter_status' => $type,
                );
                $supplementariesupdatedata = DB::table('supplementaries')->where('id', $supp_id)->update($custom_data);

                $msg = "Student has been marked rejected successfully.";
                $supplementary_verifications = DB::table('supplementary_verifications')->insert($custom_data_verifications);
                $studentMobiledetails = Student::where('id', $student->student_id)->first('mobile');
                $mobile = @$studentMobiledetails->mobile;
                $this->sendSupplementaryDocuemntVerificationSMS($mobile, Config::get('global.aicenter_id'), 3);
                if ($supplementariesupdatedata && $supplementary_verifications) {
                    return redirect()->route('supp_preview_details', $estudent_id)->with('message', $msg);
                } else {
                    $msg = "Student not successfully Submitted.";
                    return redirect()->route('supp_preview_details', $estudent_id)->with('error', $msg);
                }

            } else if ($role_id == Config::get('global.examination_department')) {

                $suppVerifcationData = DB::table('supplementary_verifications')->where('supplementary_id', @$supp_id)->whereNull('deleted_at')->orderBy('id', 'desc')->count();

                $is_per_rejected = 0;
                if ($suppVerifcationData >= 2) {
                    $is_per_rejected = 1;
                }

                $custom_data = array(
                    'is_department_verify' => $type,
                    'is_per_rejected' => $is_per_rejected,
                    'department_verify_user_id' => @Auth::id(),
                    'department_verify_datetime' => date("Y-m-d H:i:s"),
                );


                $custom_data_verifications = array(
                    'supplementary_id' => $supp_id,
                    'department_verify_user_id' => @Auth::id(),
                    'department_remark' => $request->aicenter_remark,
                    'department_rejected_marksheet_document' => $request->aicenter_rejected_marksheet_document,
                    'department_verify_datetime' => date("Y-m-d H:i:s"),
                    'department_status' => $type,
                );


                $supplementariesupdatedata = DB::table('supplementaries')->where('id', $supp_id)->update($custom_data);

                $msg = "Student has been marked rejected successfully.";
                $supplementary_verifications = DB::table('supplementary_verifications')->insert($custom_data_verifications);


                $studentMobiledetails = Student::where('id', $student->student_id)->first('mobile');
                $mobile = @$studentMobiledetails->mobile;
                //$this->sendSupplementaryDocuemntVerificationSMS($mobile,Config::get('global.examination_department'),3);

                if ($supplementariesupdatedata && $supplementary_verifications) {
                    return redirect()->route('supp_preview_details', $estudent_id)->with('message', $msg);
                } else {
                    $msg = "Student not successfully Submitted.";
                    return redirect()->route('supp_preview_details', $estudent_id)->with('error', $msg);
                }
            }
        }
        return view('supplementary.supp_doc_mark_rejected_verfication', compact('supp_rejection_value_status', 'supp_id', 'type', 'estudent_id', 'model', 'esupp_id', 'page_title'));

    }


    public function supp_doc_mark_verfication(Request $request, $supp_id = null, $type = null)
    {
        $esupp_id = $supp_id;
        $supp_id = Crypt::decrypt($supp_id);
        $student = Supplementary::where('id', $supp_id)->first(['student_id', 'course']);
        $estudent_id = Crypt::encrypt($student->student_id);
        $role_id = @Session::get('role_id');
        if ($role_id == Config::get('global.aicenter_id')) {
            $custom_data = array(
                'is_aicenter_verify' => $type,
                'aicenter_verify_user_id' => @Auth::id(),
                'aicenter_verify_datetime' => date("Y-m-d H:i:s"),
            );
            $supplementariesupdatedata = DB::table('supplementaries')->where('id', $supp_id)->update($custom_data);
            $msg = "Student has been marked approved successfully.";

            $movementofsuppDocuemnts = $this->_movementOfSuppDocuemnts($supp_id, $estudent_id, $student->course);

            $studentMobiledetails = Student::where('id', $student->student_id)->first('mobile');
            $mobile = @$studentMobiledetails->mobile;
            $this->sendSupplementaryDocuemntVerificationSMS($mobile, Config::get('global.aicenter_id'), 2);


            if ($supplementariesupdatedata) {
                return redirect()->route('supp_preview_details', $estudent_id)->with('message', $msg);
            } else {
                $msg = "Student not successfully Submitted.";
                return redirect()->route('supp_preview_details', $estudent_id)->with('error', $msg);
            }

        } else if ($role_id == Config::get('global.examination_department')) {
            $custom_data = array(
                'is_eligible' => 1,
                'is_department_verify' => $type,
                'department_verify_user_id' => @Auth::id(),
                'department_verify_datetime' => date("Y-m-d H:i:s")
            );
            $supplementariesupdatedata = DB::table('supplementaries')->where('id', $supp_id)->update($custom_data);
            $movementofsuppDocuemnts = $this->_movementOfSuppDocuemnts($supp_id, $estudent_id, $student->course);
            $msg = "Student has been marked approved successfully.";


            $studentMobiledetails = Student::where('id', $student->student_id)->first('mobile');
            $mobile = @$studentMobiledetails->mobile;
            $this->sendSupplementaryDocuemntVerificationSMS($mobile, Config::get('global.examination_department'), 2);


            if ($supplementariesupdatedata) {
                return redirect()->route('supp_preview_details', $estudent_id)->with('message', $msg);
            } else {
                $msg = "Student not successfully Submitted.";
                return redirect()->route('supp_preview_details', $estudent_id)->with('error', $msg);
            }
        }
    }

    public function revartVerifyStatus($supp_id = null, $role_id = null)
    {
        $supp_id = Crypt::decrypt($supp_id);
        $ai_centar_role = config::get('global.aicenter_id');
        $department_role = config::get('global.examination_department');
        $svData = array();
        $name = null;

        if (!empty($role_id) && $role_id == $ai_centar_role) {
            $svData = ['is_aicenter_verify' => 1, 'is_eligible' => 0];
            $name = "Aicenter";
        }

        if (!empty($role_id) && $role_id == $department_role) {
            $svData = ['is_department_verify' => 1, 'is_eligible' => 0];
            $name = "Department";
        }

        $dataupdate = Supplementary::where('id', $supp_id)->update($svData);
        if (@$dataupdate) {
            return redirect()->back()->with('message', "Student Mark Revart as $name");
        } else {
            return redirect()->back()->with('error', "Some things is wrong");
        }

    }

    public function student_supp_change_requests(Request $request, $student_id)
    {
        $checkchangerequestsssupplementariesAllowOrNotAllow = $this->_checkchangerequestssupplementariesAllowOrNotAllow();
        if (@$checkchangerequestsssupplementariesAllowOrNotAllow != 'true') {
            return redirect()->back()->with('error', 'Supp Change Request date has been closed');
        }
        $role_id = @Session::get('role_id');
        $studentrole = Config::get("global.student");
        if (@$role_id == $studentrole) {
            $students_id = Crypt::decrypt($student_id);
            $current_supp_exam_month_id = Config::get("global.supp_current_admission_exam_month");
            $changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
            $conditions['exam_year'] = $changerequertcurrentsuppexamyear;
            $conditions['exam_month'] = $current_supp_exam_month_id;
            $studentgetdata = Supplementary::where('student_id', $students_id)->where($conditions)->first();
            if (!empty($students_id)) {
                $studentarray = [
                    'student_id' => $studentgetdata->student_id,
                    'exam_year' => $studentgetdata->exam_year,
                    'exam_month' => $studentgetdata->exam_month,
                    'supp_id' => $studentgetdata->id,
                    'enrollment' => $studentgetdata->enrollment,
                    'supp_student_change_requests' => 1];
                $Student = Supplementary::where('student_id', $students_id)->where($conditions)->update(['supp_student_change_requests' => 1]);
                $changerequeststudent = SuppChangeRequestStudents::Create($studentarray);
                if (!empty($Student && $changerequeststudent)) {
                    return redirect()->back()->with('message', 'You have applied for Change in your Supp application successfully');
                } else {
                    return redirect()->back()->with('error', 'Unable to apply for Change');
                }
            } else {
                return redirect()->back()->with('message', 'record Not found.');
            }
        } else {
            return redirect()->back()->with('message', '403 USER DOES NOT HAVE THE RIGHT PERMISSIONS.');
        }
    }

    public function supp_student_change_requests_approveds(Request $request, $student_id)
    {
        $checkchangerequestsssupplementariesAllowOrNotAllow = $this->_checkchangerequestssupplementariesAllowOrNotAllow();
        if (@$checkchangerequestsssupplementariesAllowOrNotAllow != 'true') {
            return redirect()->back()->with('error', 'Supp Change Request date has been closed');
        }
        $role_id = @Session::get('role_id');
        $user_id = Auth::user()->id;
        $examinationdepartment = Config::get("global.examination_department");
        $current_supp_exam_month_id = Config::get("global.supp_current_admission_exam_month");
        $changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
        $conditions['exam_year'] = $changerequertcurrentsuppexamyear;
        $conditions['exam_month'] = $current_supp_exam_month_id;
        if (@$role_id == $examinationdepartment) {
            $students_id = Crypt::decrypt($student_id);
            $suppstudentfeesdat = Supplementary::where('student_id', $students_id)->where($conditions)->first();
            $changerequeststudent = SuppChangeRequestStudents::where('student_id', $students_id)->where($conditions)->orderBy('id', 'desc')->first();
            if (!empty($students_id)) {
                $curr_timestamp = date('Y-m-d H:i:s');
                $data = array('user_id' => $user_id, "student_id" => $students_id, "role_id" => $role_id, "supp_id" => $suppstudentfeesdat->id, "supp_student_change_requests" => $changerequeststudent->id);
                $studentarray = [
                    'student_id' => @$suppstudentfeesdat->student_id,
                    'supp_id' => @$suppstudentfeesdat->id,
                    'supp_student_change_request_id' => $changerequeststudent->id,
                    'subject_change_fees' => @$suppstudentfeesdat->subject_change_fees,
                    'exam_fees' => @$suppstudentfeesdat->exam_fees,
                    'practical_fees' => @$suppstudentfeesdat->practical_fees,
                    'forward_fees' => @$suppstudentfeesdat->forward_fees,
                    'online_fees' => @$suppstudentfeesdat->online_fees,
                    'late_fees' => @$suppstudentfeesdat->late_fees,
                    'total_fees' => @$suppstudentfeesdat->total_fees,
                    'old_challan_tid' => @$studentchallanupdatedid->update_supp_change_requests_challan_tid,
                ];
                $Student = Supplementary::where('student_id', $students_id)->where($conditions)->update(['supp_student_change_requests' => 2]);
                $changerequeststudents = SuppChangeRequestStudents::where('id', $changerequeststudent->id)->where('student_id', $students_id)->where($conditions)->update(['supp_student_change_requests' => 2, 'deparment_approved_date' => $curr_timestamp]);
                $changerequerststudentapproveds = DB::table('supp_change_requerst_student_approveds')->insert($data);
                $smsStatus = $this->_suppchangerequestsendapprovedMessage($students_id);
                $suppChangeRequertOldStudentFees = SuppChangeRequertOldStudentFees::create($studentarray);
                $fields = ["marksheet_doc", "sec_marksheet_doc"];
                if (@$suppstudentfeesdat) {
                    $suppstudentfeesdat = $suppstudentfeesdat->toArray();
                    foreach ($suppstudentfeesdat as $k => $v) {
                        if (in_array($k, $fields) && $v != null) {
                            $custom_data[$k] = @$v;
                        }
                    }
                }
                foreach (@$custom_data as $k => $getdata) {

                    if (!empty($getdata)) {


                        /* Start */

                        $combo_name = 'student_supplementary_document_path';
                        $student_document_path = $this->master_details($combo_name);
                        $exam_yearsupp = Config::get("global.form_supp_current_admission_session_id");
                        $exam_monthsupp = Config::get("global.supp_current_admission_exam_month");
                        $current_folder_year = $this->getCurrentYearFolderNamematerialschecklist($exam_yearsupp);
                        $studentDocumentPath = $student_document_path[1];
                        $studentDocumentPath = public_path($studentDocumentPath . @$current_folder_year . "/" . @$exam_monthsupp . "/" . @$students_id . "/" . $getdata);
                        $path = public_path("suppchangerequest/" . @$current_folder_year . "/" . @$exam_monthsupp . "/" . @$students_id . "/" . @$changerequeststudent->id . "/");
                        File::makeDirectory($path, $mode = 0777, true, true);

                        $studentoldsDocumentPath = $path;
                        $studentoldDocumentPath = ($studentoldsDocumentPath . $getdata);

                        if (file_exists($studentDocumentPath)) {
                            $move = File::copy($studentDocumentPath, $studentoldDocumentPath);
                        } else {
                            $isValid = false;
                        }
                        /* End */

                    }
                }
                if (!empty($Student && $changerequeststudents)) {
                    return redirect()->back()->with('message', 'Supp Change Request application for update approved successfully');
                } else {
                    return redirect()->back()->with('error', 'Supp Change Request application  not approved');
                }
            } else {
                return redirect()->back()->with('message', 'record Not found.');
            }
        } else {
            return redirect()->back()->with('message', '403 USER DOES NOT HAVE THE RIGHT PERMISSIONS.');
        }
    }

    public function supp_student_change_requests_update_application(Request $request, $student_id)
    {

        $checkchangerequestsssupplementariesAllowOrNotAllow = $this->_checkchangerequestssupplementariesAllowOrNotAllow();

        if (@$checkchangerequestsssupplementariesAllowOrNotAllow != 'true') {
            return redirect()->back()->with('error', 'Supp Change Request date has been closed');
        }
        $role_id = @Session::get('role_id');
        $studentrole = Config::get("global.student");
        if (@$role_id == $studentrole) {
            $students_id = Crypt::decrypt($student_id);
            $current_supp_exam_month_id = Config::get("global.supp_current_admission_exam_month");
            $changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
            $conditions['exam_year'] = $changerequertcurrentsuppexamyear;
            $conditions['exam_month'] = $current_supp_exam_month_id;
            $studentdata = Supplementary::where('student_id', $students_id)->where($conditions)->first(['supp_student_change_requests', 'id']);
            $changerequeststudent = SuppChangeRequestStudents::where('student_id', $students_id)->where($conditions)->where('supp_id', @$studentdata->id)->orderBy('id', 'desc')->first();

            if (@$changerequeststudent->supp_student_update_application == 1 && @$studentdata->supp_student_change_requests == 2) {
                return redirect()->route('supp_subjects_details', Crypt::encrypt($students_id))->with('message', 'Supp Change Request application for update approved successfully');
            }
            if (!empty($students_id)) {
                $studentalltablebackups = $this->suppstudentalltablebackup($students_id);
                $curr_timestamp = date('Y-m-d H:i:s');
                $Student = Supplementary::where('student_id', $students_id)->where('id', @$studentdata->id)->where($conditions)->update(['is_eligible' => NULL, 'locksubmitted_date' => NULL, 'locksumbitted' => NULL, 'is_department_verify' => NULL, 'is_aicenter_verify' => NULL]);
                $changerequeststudents = SuppChangeRequestStudents::where('id', $changerequeststudent->id)->where('student_id', $students_id)->where('supp_id', @$studentdata->id)->where($conditions)->update(['supp_student_update_application' => 1, 'student_update_application_date' => $curr_timestamp]);
                //$smsStatus = $this->_changerequestsendapprovedMessage($students_id);
                if (!empty($Student && $changerequeststudents)) {
                    return redirect()->route('supp_subjects_details', Crypt::encrypt($students_id))->with('message', 'Supp Change Request application for update approved successfully');
                } else {
                    return redirect()->back()->with('error', 'Supp Change Request application  not approved');
                }
            } else {
                return redirect()->back()->with('message', 'record Not found.');
            }
        } else {
            return redirect()->back()->with('message', '403 USER DOES NOT HAVE THE RIGHT PERMISSIONS.');
        }
    }

    public function suppsubjectdelete($supp_id = null)
    {
        $esupp_id = $supp_id;
        $supp_id = Crypt::decrypt($supp_id);

        $ldate = date('Y-m-d H:i:s');
        $custom_data = array(
            'remarks' => "Deleted by Exam Dept. Login as one " . $ldate,
            'deleted_by_user_id' => @Auth::id(),
            'deleted_at' => $ldate
        );
        SupplementarySubject::where('id', $supp_id)->update($custom_data);

        return redirect()->back()->with('success', 'Subject has been successfully deleted.');
    }


}
	