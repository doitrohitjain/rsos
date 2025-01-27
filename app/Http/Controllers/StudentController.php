<?php
namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\MarksheetCustomComponent;
use App\Exports\Deactivedstudentsexcel;
use App\Helper\CustomHelper;
use App\Models\Address;
use App\Models\AdmissionSubject;
use App\Models\Application;
use App\Models\BankDetail;
use App\Models\ChangeRequertOldStudentFees;
use App\Models\ChangeRequestStudent;
use App\Models\ChangeRequestStudentTarils;
use App\Models\Document;
use App\Models\DocumentVerification;
use App\Models\ExamResult;
use App\Models\ExamSubject;
use App\Models\ModelHasRole;
use App\Models\Registration;
use App\Models\RevalStudent;
use App\Models\Student;
use App\models\StudentAllotment;
use App\Models\StudentDocumentVerification;
use App\Models\StudentFee;
use App\Models\StudentOrgFee;
use App\Models\Supplementary;
use App\Models\Toc;
use App\Models\TocMark;
use Auth;
use Cache;
use Config;
use DB;
use File;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Redirect;
use Response;
use Route;
use Session;
use Spatie\Permission\Models\Role;
use Validator;

class StudentController extends Controller
{

    function __construct(Request $request)
    {
        // $role_id = @Session::get('role_id');
        // dd($role_id);

        //$routeArray = app('request')->route()->getAction();
        // dd($routeArray);
        // if(@$routeArray['as'] == "registration"){
        // 	$this->_redirectForWhiteList();
        // }

        // echo "<h1>Please wait we are coming soon.</h1>";die;
		

        $this->middleware('permission:student_dashboard', ['only' => ['dashboard']]);
        $this->middleware('permission:student-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:student-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:student-show', ['only' => ['show']]);
        $this->middleware('permission:student-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:student-delete', ['only' => ['destroy']]);
        $this->middleware('permission:student_registration', ['only' => ['registration']]);
        $this->middleware('permission:update_student_details', ['only' => ['update_basic_details']]);


        // $role_id = Auth::user();dd($role_id);
        // // $role_id = Session::get('role_id');

        // $rolehaspermissions = DB::table('role_has_permissions')->where('role_id',$role_id)->pluck('permission_id');
        // $permissions = DB::table('permissions')->whereIn('id',$rolehaspermissions)->pluck('name')->toarray();


        // dd($permissions);
        // $this->middleware('permission:student_persoanl_details', ['only' => ['persoanl_details']]);
        // Pending start
        // $this->middleware('permission:student_persoanl_details', ['only' => ['persoanl_details']]);
        // $this->middleware('permission:student_download', ['only' => ['download']]);
        // $this->middleware('permission:student_address_details', ['only' => ['address_details']]);
        // $this->middleware('permission:student_bank_details', ['only' => ['bank_details']]);
        // $this->middleware('permission:student_document_details', ['only' => ['document_details']]);
        // $this->middleware('permission:student_admission_subject_details', ['only' => ['admission_subject_details']]);
        // $this->middleware('permission:student_toc_subject_details', ['only' => ['toc_subject_details']]);
        // $this->middleware('permission:dev_toc_subject_details', ['only' => ['dev_toc_subject_details']]);
        // $this->middleware('permission:student_exam_subject_details', ['only' => ['exam_subject_details']]);
        // $this->middleware('permission:student_fee_details', ['only' => ['fee_details']]);
        // $this->middleware('permission:student_preview_details', ['only' => ['preview_details']]);
        // $this->middleware('permission:student_view_details', ['only' => ['view_details']]);
        // Pending end


        //$this->middleware('permission:all_delete_students', ['only' => ['deletestudent']]);
        $this->middleware('permission:all_delete_students', ['only' => ['studentdeleteactive']]);
        $this->middleware('permission:studentunlock', ['only' => ['studentunlock']]);
        $this->middleware('permission:printerupdatestudentdata', ['only' => ['UpdateStudentDetail']]);
        $this->middleware('permission:searchstudentdata', ['only' => ['SearchStudentDetail']]);

        //if(!empty(Auth::user()->id)){
        //$this->middleware('permission:student_generate_student_pdf', ['only' => ['generate_student_pdf']]);
        //}


        //$this->middleware('permission:admission_report_student_edit', ['only' => ['persoanl_details','download','address_details',
        //'bank_details','document_details','admission_subject_details','toc_subject_details','exam_subject_details',
        //'fee_details','preview_details','generate_student_pdf','view_details']]);
        //$this->middleware('permission:admission_report_student_view', ['only' => ['persoanl_details','download','address_details',
        //'bank_details','document_details','admission_subject_details','toc_subject_details','exam_subject_details',
        //'fee_details','preview_details','generate_student_pdf','view_details']]);\
		
		$action = Route::getCurrentRoute()->getAction();
		if(@$action['as'] && $action['as'] != "" && $action['as'] != "printupdatestudentdetalis"){
			Session::put('last_action_name',@$action['as']);
		}
    }

    public function redirectoother()
    {
        return redirect()->route('aicenterdashboard')->with('error', 'Failed! Registration date has been closed!');
    }

    public function registration(Request $request)
    {
        $page_title = 'Student Registration';
        $model = "Student";

        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);

        $streams = $this->_getAllowedStreams();
        $streams = json_decode($streams);

        $pos = strpos(@$streams->msg, 'Date&Time not');
        if (!$streams->status) {
            return redirect()->route('aicenterdashboard')->with('error', 'Failed! Registration date has been closed!');
        }
        $tempStreams = array();
        if (!empty($streams->info)) {
            foreach (@$streams->info as $k => $stream) {
                $tempStreams[$stream->stream] = $stream_id[$stream->stream];
            }
        } else {
            return back()->with('error', 'Date is closed.');
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


        if (count($request->all()) > 0) {
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
                $user_id = Auth::user()->id;
                $aicode = $custom_component_obj->getAiCentersuserdatacode($user_id);

                if ($request->are_you_from_rajasthan == '1') {

                    //dd($dataTobeSave);
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
                            return redirect()->back()->with('error', 'Student DOB should not be lesser than ' . date('d-m-Y', strtotime($min_dob_date[$request->course])) . ' not allowed.')->withInput($request->all());
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
                        'mobile' => @$dataTobeSave['Student']['mobile'],
                        'adm_type' => $request->adm_type, 'course' => $request->course, 'stream' => $request->stream, 'user_id' => $user_id, 'ai_code' => @$aicode->ai_code];
                } else {
                    $studentarray = ['are_you_from_rajasthan' => $request->are_you_from_rajasthan, 'adm_type' => $request->adm_type, 'course' => $request->course, 'stream' => $request->stream, 'user_id' => $user_id, 'ai_code' => @$aicode->ai_code, 'exam_month' => $request->stream, 'exam_year' => $current_admission_session_id];
                }

                $countAlreadyPresentDetails = 0;
                if ($request->are_you_from_rajasthan == 1) {
                    $countAlreadyPresentDetails = $this->checkIsAlreadyPresentCombination($studentarray);
                }


                if ($countAlreadyPresentDetails > 0) {
                    return redirect()->route('registration')->with('error', 'Failed!Something is Wrong. Student not created');
                }

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
                $application = Application::create($applicationarray);

                $password = Hash::make('123456789');
                $updatestudent = Student::find($Student->id);
                $updatestudent->password = $password;
                $updatestudent->save();

                $addressArray = array();


                if (@$dataTobeSave['Address']['addressEng']) {
                    $addressArray['address1'] = @$dataTobeSave['Address']['addressEng'];
                    $addressArray['address2'] = @$dataTobeSave['Address']['ward'];
                    $addressArray['student_id'] = $Student->id;
                    $addressArray['state_id'] = 6;
                    $addressArray['state_name'] = 'Rajasthan';
                    if (@$dataTobeSave['Address']['districtName']) {
                        $districtId = $this->districtOnlyIdByName($dataTobeSave['Address']['districtName']);
                        $addressArray['district_id'] = @$districtId->id;
                        $addressArray['district_name'] = @$dataTobeSave['Address']['districtName'];
                    }
                    if (@$dataTobeSave['Address']['block_city']) {
                        // $tehsil_id = $this->tehsilOnlyIdByName($dataTobeSave['Address']['block_city']);
                        // $addressArray['tehsil_id'] = @$tehsil_id->id;
                        // $addressArray['tehsil_name'] = @$dataTobeSave['Address']['block_city'];
                    }
                    $addressArray['city_name'] = @$dataTobeSave['Address']['block_city'];
                    $addressArray['pincode'] = @$dataTobeSave['Address']['pin'];

                    $addressSaved = Address::create($addressArray);
                }
                $status = false;
                if (@$dataTobeSave['Student']['mobile']) {
                    $status = $this->_sendOTPToStudent($Student->id);
                }
                if ($Student->id && $application->id) {
                    if ($status) {
                        return redirect()->route('persoanl_details', Crypt::encrypt($Student->id))->with('message', 'Student profile successfully created.A OTP also sent to your registred mobile number.');
                    } else {
                        return redirect()->route('persoanl_details', Crypt::encrypt($Student->id))->with('message', 'Student successfully created');
                    }

                } else {
                    return redirect()->route('registration')->with('error', 'Failed! Student not created');
                }

            } else {
                $customerrors = implode(",", @$responseFinal[$k]['customerrors']);
                return redirect()->back()->withErrors($responseFinal['validator'])->withInput($request->all());
            }
        }
        return view('student.registration', compact('page_title', 'adm_types', 'are_you_from_rajasthan', 'course', 'stream_id', 'model'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('student.create', compact('roles'));
    }

    public function student_history_details(Request $request, $student_id)
    {
        $estudent_id = $student_id;
        Session::put('studentallowornot', "");
        $studentallowornot = false;
        $student_id = Crypt::decrypt($estudent_id);

        $estudent_id = Crypt::encrypt($student_id);
        $data = $this->_getStudentHistoryDetails($estudent_id);


        if (@$data['application']->locksumbitted && !empty(@$data['application']->locksumbitted)) {
            $studentallowornot = true;

        }
        session::put('studentallowornot', $studentallowornot);
        if ($studentallowornot == false) {
            return redirect()->back()->with('error', "Your Form is not lock and submitted.");
        }

        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $stream = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1] . $student_id;
        $page_title = "विद्यार्थी प्रोफ़ाइल(Student's Profile" . ' ' . @$stream[@$data['student']['stream']] . ' ' . @$admission_sessions[@$data['student']['exam_year']] . ")";
        $student_photos = $signature = $category_a = $category_b = $pre_qualification = $disability = $cast_certificate = $category_c = $category_d = null;
        $photographimageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$data['student_document']['photograph'];

        $student = array();
        if (file_exists($photographimageurl) && !empty($data['student_document']['photograph'])) {
            $student_path = url('public/' . $studentDocumentPath . '/' . @$data['student_document']['photograph']);
            $student['student_photos'] = array(
                "label" => "Photo",
                "value" => route('download', Crypt::encrypt('/' . $studentDocumentPath . "/" . @$data['student_document']['photograph'])));
        } else {
            $student_path = url('public/app-assets/images/users1.png');
        }
        $signatureimageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$data['student_document']['signature'];
        if (file_exists($signatureimageurl) && !empty($data['student_document']['signature'])) {
            $student['signature'] = array("label" => "Signature",
                'value' => route('download', Crypt::encrypt('/' . $studentDocumentPath . "/" . @$data['student_document']['signature'])));
        }

        $categoryaimageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$data['student_document']['category_a'];
        if (file_exists($categoryaimageurl) && !empty($data['student_document']['category_a'])) {
            $student['category_a'] = array("label" => " DOB Certificate",
                'value' => route('download', Crypt::encrypt('/' . $studentDocumentPath . "/" . @$data['student_document']['category_a'])));

        }

        $categorybimageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$data['student_document']['category_b'];

        if (file_exists($categorybimageurl) && !empty($data['student_document']['category_b'])) {

            $student['category_b'] = array("label" => "Address",
                'value' => route('download', Crypt::encrypt('/' . $studentDocumentPath . "/" . @$data['student_document']['category_b'])));

        }


        $qualifaictaionimageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$data['student_document']['pre_qualification'];

        if (file_exists($qualifaictaionimageurl) && !empty($data['student_document']['pre_qualification'])) {
            $student['pre_qualification'] = array("label" => "Pre qualification",
                'value' => route('download', Crypt::encrypt('/' . $studentDocumentPath . "/" . @$data['student_document']['pre_qualification'])));
        }
        $disabilityimageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$data['student_document']['disability'];

        if (file_exists($disabilityimageurl) && !empty($data['student_document']['disability'])) {
            $student['disability'] = array("label" => "Disability",
                'value' => $disability = route('download', Crypt::encrypt('/' . $studentDocumentPath . "/" . @$data['student_document']['disability'])));;

        }

        $castcertificateimageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$data['student_document']['cast_certificate'];

        if (file_exists($castcertificateimageurl) && !empty($data['student_document']['cast_certificate'])) {
            $student['cast_certificate'] = array("label" => "Cast certificate",
                'value' => route('download', Crypt::encrypt('/' . $studentDocumentPath . "/" . @$data['student_document']['cast_certificate'])));
        }

        $categorycimageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$data['student_document']['category_c'];

        if (file_exists($categorycimageurl) && !empty($data['student_document']['category_c'])) {
            $student['category_c'] = array("label" => "Other-I",
                'value' => route('download', Crypt::encrypt('/' . $studentDocumentPath . "/" . @$data['student_document']['category_c'])));

        }

        $categorydimageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$data['student_document']['category_d'];

        if (file_exists($categorydimageurl) && !empty($data['student_document']['category_d'])) {
            $student['category_d'] = array("label" => "Other-II",
                'value' => route('download', Crypt::encrypt('/' . $studentDocumentPath . "/" . @$data['student_document']['category_d'])));
        }

        $isAllowToShowAdmitCardDownloadForStudent = $this->getIsAllowToShowAdmitCardDownloadForStudent(@$data['student_allotment']);

        return view('student.student_history_details', compact('isAllowToShowAdmitCardDownloadForStudent', 'data', 'student_path', 'student_id', 'estudent_id', 'page_title', 'stream', 'course', 'admission_sessions', 'student'));
    }

    public function _getStudentHistoryDetails($estudent_id = null)
    {
        $student_id = Crypt::decrypt($estudent_id);
        $data = array();

        $data['student'] = $student = Student::where('id', $student_id)->first();
        $data['student_document'] = $documetnt = Document::where('student_id', $student_id)->first();


        $student_admit_card_download_exam_year = Config::get('global.student_admit_card_download_exam_year');
        $student_admit_card_download_exam_month = Config::get('global.student_admit_card_download_exam_month');
        $data['student_allotment'] = StudentAllotment::
        where('student_id', $student_id)
            ->where('exam_year', $student_admit_card_download_exam_year)
            ->where('exam_month', $student_admit_card_download_exam_month)
            ->first();
        $data['application'] = $documetnt = Application::where('student_id', $student_id)->first(['locksubmitted_date', 'locksumbitted']);
        $data['supplementary'] = $supplementary = Supplementary::where('student_id', $student_id)->orderBy('exam_year', 'DESC')->orderBy('exam_month', 'asc')->get();


        $marksheetCustomComponentObj = new MarksheetCustomComponent;

        if (@$data['supplementary']) {
            foreach (@$data['supplementary'] as $k => $supplementaryDetails) {
                $combination = '';
                if (isset($supplementaryDetails->exam_month) && isset($supplementaryDetails->exam_year)) {
                    $combination = $supplementaryDetails->exam_month . ' ' . $supplementaryDetails->exam_year;
                }
                $data['supplementary'][$k]['displayExamMonthYear'] = $marksheetCustomComponentObj->getDisplayExamMonthYear($combination);

                $data['supplementary'][$k]['displayExamMonthYear'] = str_replace("MAR-MAY", "March-May", $data['supplementary'][$k]['displayExamMonthYear']);
                $data['supplementary'][$k]['displayExamMonthYear'] = str_replace("OCT-NOV", "October-November", $data['supplementary'][$k]['displayExamMonthYear']);
            }
        }
		
        $data['examResult'] = $examResult = ExamResult::where('student_id', $student_id)->orderBy('exam_year', 'DESC')->orderBy('exam_month', 'asc')->get();		

		/* Stop result showing till not decleard start */
			$priter_not_allowed_exam_year = config::get('global.priter_not_allowed_exam_year');
			$priter_not_allowed_exam_month = config::get('global.priter_not_allowed_exam_month');
			@$examresulttemp = @$data['examResult'][0];
			if(@$examresulttemp->exam_year == @$priter_not_allowed_exam_year && @$examresulttemp->exam_month == @$priter_not_allowed_exam_month){
				 $data['examResult'] = [];  
			}
		/* Stop result showing till not decleard end */
        $data['reval'] = $reval = RevalStudent::where('student_id', $student_id)->orderBy('exam_year', 'DESC')->orderBy('exam_month', 'asc')->get();

        /*$combo_name = 'admit_card_download_exam_year';$admit_card_download_exam_year = $this->master_details($combo_name);
		$admit_card_download_exam_year = @$admit_card_download_exam_year[1];
		$combo_name = 'admit_card_download_exam_month';$admit_card_download_exam_month = $this->master_details($combo_name);
		$admit_card_download_exam_month = @$admit_card_download_exam_month[1];

		$data['studentAllotment'] = $studentAllotment = StudentAllotment::where('student_id',$student_id)
			->where('exam_year',$admit_card_download_exam_year)
			->where('exam_month',$admit_card_download_exam_month)
			->first();*/
        return $data;
    }


    public function persoanl_details(Request $request, $student_id)
    {
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters(null, null, 1);
        $table = $model = "Students";
        $page_title = 'Personal Details';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($estudent_id);
        $role_id = Session::get('role_id');
        $docrejectednotification = $this->student_doc_rejected_notification($student_id);
        $studentdata = Student::findOrFail($student_id);

        $is_dgs_student = false;
        if ($studentdata->is_dgs == 1) {
            $is_dgs_student = true;
        }

        $applicationdata = Application::where('student_id', $student_id)->first();
        if (@$applicationdata->id) {
        } else {
            return redirect()->route('landing')->with('error', 'Something is wrong with your filled detail.');
        }
        $changerequeststudent = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
        $this->_checkStudentEntryAllowOrNotAllow($studentdata);

        $state_list = $this->state_details();

        $is_update = 1;
        $flagUpdateStatus = $this->updateStudentInfoFlag($student_id, $is_update);

        $model = "Student";
        if (empty($studentdata)) {
            return redirect()->route('registrations')->with('error', 'Failed! registration not saved');
        }

        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);

        if ($isLockAndSubmit == 1) {
            return redirect()->route('view_details', Crypt::encrypt($student_id))->with('message', 'Form already successfully locked and submitted.');
        }
        $isItiStudent = $this->_isItiStudent($student_id);

        /* Is ssoid input box start */
        $allowSsoInput = false;
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        if ($isAdminStatus == true) {
            $allowSsoInput = true;
        } else {
            $isStudent = $custom_component_obj->_getIsStudentLogin();
            if (@$isStudent) {
            } else {
                $allowSsoInput = true;
            }
        }
        /* Is ssoid input box end */
        if (count($request->all()) > 0) {
            $checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
            if (@$studentdata->student_change_requests == 2 && $checkchangerequestsAllowOrNotAllow == true) {
                if (@$studentdata->course != @$request->course || @$studentdata->exam_month != @$request->stream || @$studentdata->ai_code != @$request->ai_code) {
                    $changerequerststudentapproveds = DB::table('change_requerst_student_approveds')->where('student_id', $student_id)->update(['enrollment' => @$studentdata->enrollment]);
                    $Student = Student::where('id', $student_id)->update(['is_change_enrollment' => 1]);
                    // $application = Application::where('student_id',$student_id)->update(['enrollment'=>NULL]);
                }
            }
            $table_name = 'students';
            $ssoid = $request->ssoid;
            $custom_component_obj = new CustomComponent;
            $ssoid = @$request->ssoid;
            if (empty($ssoid)) {
                return redirect()->route('persoanl_details', Crypt::encrypt($student_id))->with('error', 'Something is wrong.Please validate sso.');
            }

            $checkssoidallreadyaccessCount = $custom_component_obj->_checkssoidallreadyaccess($table_name, $student_id, $ssoid);
            if ($checkssoidallreadyaccessCount > 0) {
                return redirect()->route('persoanl_details', Crypt::encrypt($student_id))->with('error', 'SSO  already exists with another.');
            }
            $isChange = $this->isChangeInFormData($model, $request, $studentdata);

            if ($isChange) {
                $deleteStatus = $this->deleteDataStudentId($student_id, $model);
            }

            $responses = $this->PersonalDetailsValidation($request);
            // dd($responses);
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
                // $combo_name = 'minage';$minage = $this->master_details($combo_name);
                $combo_name = 'min_dob_date';
                $min_dob_date = $this->master_details($combo_name);
                $dobs = date('Y-m-d', strtotime($request->dob));
                if ($dobs == "1970-01-01") {
                    $dobArr = explode("/", $request->dob);
                    if (isset($dobArr[0]) && isset($dobArr[1]) && isset($dobArr[2])) {
                        $dobs = $dobArr[2] . "-" . $dobArr[1] . "-" . $dobArr[0] . "";
                    }
                }

                if ($dobs > $min_dob_date[$request->course]) {
                    return redirect()->back()->with('error', 'Student DOB should not be lesser than ' . date('d-m-Y', strtotime($min_dob_date[$request->course])) . ' not allowed.')->withInput($request->all());
                }
                if ($request->course == 10) {
                    $board = 0;
                    $year_pass = 0;

                } else if ($request->course == 12) {
                    $board = $request->board;
                    $year_pass = $request->year_pass;
                }

                $jan_aadhar_number = null;
                if ($request->are_you_from_rajasthan == 1) {
                    $jan_aadhar_number = $request->jan_aadhar_number;

                } else if ($request->are_you_from_rajasthan == 0) {
                    $jan_aadhar_number = '';
                }

                if (!empty($studentdata->aadhar_number)) {
                    $aadhar_number = $studentdata->aadhar_number;
                } else {
                    $aadhar_number = $request->aadhar_number;
                }

                // Student Data Updation Log Enteries
                $custom_component_obj = new CustomComponent;
                $isStudent = $custom_component_obj->_getIsStudentLogin();
                if (@$isStudent) {
                    $studentfeedata['is_self_filled'] = 1;
                    $last_updated_by_user_id['last_updated_by_user_id'] = $student_id;
                } else {
                    $studentfeedata['is_self_filled'] = null;
                    $last_updated_by_user_id['last_updated_by_user_id'] = @Auth::id();
                }
                $table_primary_id = $student_id;
                $table_name = 'students';
                $form_type = 'Admission';
                $controller_obj = new Controller;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type

                );

                // Student Data Updation Log Enteries

                $dataTobeSave['Student']['name'] = $request->first_name . " " . $request->middle_name . " " . $request->last_name;

                if (@$studentdata->student_change_requests == 2 && $checkchangerequestsAllowOrNotAllow == true) {

                    $studentarray = [
                        'exam_month' => $request->stream, 'stream' => $request->stream, 'adm_type' => $request->adm_type, 'course' => $request->course,
                        'gender_id' => $request->gender_id, 'mobile' => $request->mobile, 'email' => $request->email, 'ai_code' => $request->ai_code,];
                } else {
                    $studentarray = ['name' => $dataTobeSave['Student']['name'], 'first_name' => $request['first_name'], 'middle_name' => $request['middle_name'], 'last_name' => $request['last_name'],
                        'exam_month' => $request->stream, 'stream' => $request->stream, 'adm_type' => $request->adm_type,
                        'course' => $request->course, 'name' => $dataTobeSave['Student']['name'],
                        'father_name' => $request->father_name, 'mother_name' => $request->mother_name,
                        'gender_id' => $request->gender_id, 'mobile' => $request->mobile, 'dob' => $dobs, 'email' => $request->email, 'ai_code' => $request->ai_code,];
                }
                $modeltype = 'App\Models\Student';
                $studentroles = Config::get("global.student");
                $studentroless = ['role_id' => $studentroles,
                    'model_type' => $modeltype,
                    'model_id' => $student_id,];
                $password = Hash::make('123456789');
                $isStudentLoigin = $custom_component_obj->_getIsStudentLogin();
                if ($isStudentLoigin) {
                } else {
                    $studentarray['ssoid'] = $request->ssoid;
                    $studentarray['password'] = $password;
                }
                //if(@$studentdata->student_change_requests == 2 && $checkchangerequestsAllowOrNotAllow == true){
                // $Student = Student::where('id',$student_id)->update($studentarray);
                //}else{
                //$studentarray['ai_code'] = $request->ai_code;
                //}

                $Student = Student::where('id', $student_id)->update($studentarray);

                $model_has_roles = DB::table('model_has_roles')->where('role_id', $studentroles)->where('model_type', $modeltype)->where('model_id', $student_id)->first();
                if (!empty($model_has_roles)) {
                    $model_has_roles = DB::table('model_has_roles')->where('role_id', $studentroles)->where('model_type', $modeltype)->where('model_id', $student_id)->update($studentroless);
                } else {
                    $model_has_roles = ModelHasRole::create($studentroless);
                }

                $custom_component_obj = new CustomComponent;
                $isStudentLoigin = $custom_component_obj->_udpateLastAcotionPermedBy($student_id);
                // Student Application Data Updation Log Enteries
                $table_primary_id = @$applicationdata->id;
                $table_name = 'applications';
                $form_type = 'Admission';
                $controller_obj = new Controller;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
                // Student Application Data Updation Log Enteries

                $applicationarray = ['nationality' => $request->nationality, 'board' => $board, 'religion_id' => $request->religion_id, 'category_a' => $request->category_a,
                    'aadhar_number' => $aadhar_number, 'disability' => $request->disability, 'disability_percentage' => $request->disability_percentage, 'disadvantage_group' => $request->disadvantage_group,
                    'medium' => $request->medium, 'rural_urban' => $request->rural_urban, 'employment' => $request->employment, 'pre_qualification' => $request->pre_qualification, 'year_pass' => $year_pass];

                $application = Application::where('student_id', $student_id)->update($applicationarray);
                $applications2 = StudentFee::where('student_id', $student_id)->delete();
                if ($Student && $application) {
                    return redirect()->route('address_details', Crypt::encrypt($student_id))->with('message', 'Personal details has been successfully submitted.');
                } else {
                    return redirect()->route('registration')->with('error', 'Failed! Personal details has been not submitted');
                }
            } else {
                $customerrors = implode(",", @$responseFinal[$k]['customerrors']);
                return redirect()->back()->withErrors($responseFinal['validator'])->withInput($request->all());

            }
        }

        // dd($studentdata); "course" => 10 /"stream" => 1
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $this->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);

        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $this->master_details($combo_name);
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $combo_name = 'special_abilities_percentage';
        $special_abilities_percentage = $this->master_details($combo_name);
        $combo_name = 'employment';
        $employment = $this->master_details($combo_name);

        if (@$studentdata->course && @$studentdata->adm_type && @$studentdata->stream) {
            $result = DB::table('master_previous_qualifications')->where('course', $studentdata->course)
                ->where('adm_type', $studentdata->adm_type)->where('stream', $studentdata->stream)
                ->first('previous_qualification_name');

            $result = explode(",", @$result->previous_qualification_name);
            $combo_name = 'pre-qualifi';
            $pre_qualifi = $this->master_details($combo_name);

            $finalArr = array();
            foreach (@$result as $v) {
                $finalArr[$v] = $pre_qualifi[$v];
            }
            $pre_qualifi = $finalArr;
        } else {
            $combo_name = 'pre-qualifi';
            $pre_qualifi = $this->master_details($combo_name);
        }

        $adm_types = array();

        if (@$studentdata->stream) {
            if ($studentdata->stream == 1) {
                $adm_types = DB::table('masters')->where('combo_name', 'adm_type')->pluck('option_val', 'option_id');
            } elseif ($studentdata->stream == 2) {
                $adm_types = DB::table('masters')->where('combo_name', 'adm_type')->where('id', '3')->pluck('option_val', 'option_id');

            }
        } else {
            $combo_name = 'adm_type';
            $adm_types = $this->master_details($combo_name);
        }


        if (@$studentdata->adm_type) {
            $admtype = $studentdata->adm_type;
            if ($admtype == 1 || $admtype == 3 || $admtype == 5) { //General,
                $boards = DB::table('boards')->get()->pluck('name', 'id');
            } elseif ($admtype == 2) { //Re-Admission
                $boards = DB::table('boards')->whereIn('id', [81])->pluck('name', 'id');
            } elseif ($admtype == 4) { //Improvment
                $boards = DB::table('boards')
                    ->whereIn('id', [81, 56])
                    ->pluck('name', 'id');
            }
        } else {
            $getBoardList = $this->getBoardList();
        }

        $combo_name = 'year';
        $year = $this->master_details($combo_name);
        $rsos_years = $this->rsos_years();
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);

        $combo_name = 'minage';
        $minage = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'yesno';
        $yesno = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $getBoardList = $this->getBoardList();
        $studentroless = Config::get("global.student");
        $role_ai_code = Config::get("global.student");
        @$checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
        if ($role_id == $studentroless || $role_id == $role_ai_code) {
            if (@$studentdata->student_change_requests == 2 && @$checkchangerequestsAllowOrNotAllow == true) {

                $combo_name = 'stream_id';
                $stream_id = $this->master_details($combo_name);
                unset($stream_id[2]);
            } else {
                unset($stream_id[2]); // if we want to only stream 1 option in dropdown
                //unset($stream_id[1]); // if we want to only stream 2 option in dropdown
            }
        }
        if ($studentdata->gender_id != 2) {
            unset($dis_adv_group[4]);
        }
        if (@$studentdata->student_change_requests == 2 && @$checkchangerequestsAllowOrNotAllow == true) {
            $combo_name = 'stream_id';
            $stream_id = $this->master_details($combo_name);
            unset($stream_id[2]);
        } else {
            //unset($stream_id[2]); // if we want to only stream 1 option in dropdown
            //unset($stream_id[1]); // if we want to only stream 2 option in dropdown
        }

        if (@$studentdata->is_dgs && $studentdata->is_dgs == 1) {
            unset($dis_adv_group[1]);
            unset($dis_adv_group[2]);
            unset($dis_adv_group[3]);
        }


        return view('student.persoanl_details', compact('changerequeststudent', 'allowSsoInput', 'exam_month', 'isItiStudent', 'special_abilities_percentage', 'are_you_from_rajasthan', 'state_list', 'estudent_id', 'exam_session', 'isLockAndSubmit', 'page_title', 'gender_id', 'categorya', 'is_dgs_student', 'nationality', 'religion', 'disability', 'dis_adv_group', 'midium', 'rural_urban', 'employment', 'pre_qualifi', 'year', 'studentdata', 'adm_types', 'course', 'student_id', 'applicationdata', 'model', 'exam_session', 'stream_id', 'admission_sessions', 'getBoardList', 'yesno', 'rsos_years', 'role_id', 'aiCenters', 'docrejectednotification'));
    }

    public function _checkStudentEntryAllowOrNotAllow($studentdata = null)
    {
        $role_id = @Session::get('role_id');
        $aicenter_id = Config::get("global.aicenter_id");
        if ($role_id == $aicenter_id) {
            $currentAction = Route::getCurrentRoute()->getActionMethod();
            if ($currentAction !== "registration") {
                $allowedOrNot = $this->_checkFormAllowedOrNot($studentdata->stream);
                $allowedOrNot = json_decode($allowedOrNot);
                if (!$allowedOrNot->status) {
                    return redirect()->route('sdlogin')->with('error', 'Failed! Registration date has been closed!');
                }
            }
        }
    }

    public function updateStudentInfoFlag($student_id = null, $is_update = null)
    {
        $role_id = @Session::get('role_id');
        $super_admin_id = Config::get("global.super_admin_id");
        $developer_admin = Config::get("global.developer_admin");
        $status = null;
        if ($role_id == $super_admin_id || $role_id == $developer_admin) {
            $custom_component_obj = new CustomComponent;
            $status = $custom_component_obj->studentUpdateByAdmin($student_id, $is_update);
        }
        return $status;
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'ssoid' => 'required',
            'email' => 'required|email',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $student = Student::find($id);
        $student->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();

        $student->assignRole($request->input('roles'));

        if ($student) {
            return redirect()->route('students.index')->with('message', 'Student successfully updated');
        } else {
            return redirect()->route('students.index')->with('error', 'Failed! Student not updated');
        }
    }

    public function address_details(Request $request, $student_id)
    {
        $table = $model = "Address";
        $route = url()->full();
        $state_list = $this->state_details();
        $page_title = $model . ' Details';
        $district_list = $current_district_list = $this->districtsByState();
        $tehsil_list = $current_tehsil_list = $this->tehsilsByDistrictId();
        $tehsil_lists = array();
        $current_block_list = $block_list = $this->block_details();
        $docrejectednotification = $this->student_doc_rejected_notification($student_id);

        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $studentdata = Student::findOrFail($student_id);
        $this->_checkStudentEntryAllowOrNotAllow($studentdata);
        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);
        if ($isLockAndSubmit == 1) {
            return redirect()->route('view_details', Crypt::encrypt($student_id))->with('message', 'Form already successfully locked and submitted.');
        }
        $isItiStudent = $this->_isItiStudent($student_id);

        $routeUrl = "persoanl_details";
        $isValid = Application::where('student_id', $student_id)->first();
        if (!@$isValid->nationality) {
            return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! Please first fill the details!');
        }
        $master = Address::where('student_id', $student_id)->first();
        // dd($master);

        if (isset($master->state_id) && !empty($master->state_id)) {
            $district_list = $this->districtsByState($master->state_id);
        }

        if (isset($master->district_id) && !empty($master->district_id)) {
            $tehsil_list = $this->tehsilsByDistrictId($master->district_id);
        }

        if (isset($master->district_id) && !empty($master->district_id)) {
            $block_list = $this->block_details($master->district_id);
        }

        // current address data
        if (isset($master->current_state_id) && !empty($master->current_state_id)) {
            $current_district_list = $this->districtsByState($master->current_state_id);
        }

        if (isset($master->current_district_id) && !empty($master->current_district_id)) {
            $current_tehsil_list = $this->tehsilsByDistrictId($master->current_district_id);
        }

        if (isset($master->current_district_id) && !empty($master->current_district_id)) {
            $current_block_list = $this->block_details($master->current_district_id);
        }

        // current address data


        if (count($request->all()) > 0) {
            if (isset($request->state_id) && !empty($request->state_id)) {
                $district_list = $this->districtsByState($request->state_id);
            }

            if (isset($request->district_id) && !empty($request->district_id)) {
                $tehsil_list = $this->tehsilsByDistrictId($request->district_id);
            }

            if (isset($request->district_id) && !empty($request->district_id)) {
                $block_list = $this->block_details($request->district_id);
            }


            $state_name = (isset($state_list[$request->state_id])) ? $state_list[$request->state_id] : '';
            $district_data = $this->get_table_data_by_id($request->district_id, 'districts');
            $district_name = (isset($district_data[0])) ? $district_data[0] : '';
            $tehsil_data = (isset($request->tehsil_id)) ? $this->get_table_data_by_id($request->tehsil_id, 'tehsils') : '';
            $tehsil_name = (isset($tehsil_data[0])) ? $tehsil_data[0] : '';
            $block_data = (isset($request->block_id)) ? $this->get_table_data_by_id($request->block_id, 'blocks') : '';
            $block_name = (isset($block_data[0])) ? $block_data[0] : '';

            // current address data
            if (isset($request->current_state_id) && !empty($request->current_state_id)) {
                $current_district_list = $this->districtsByState($request->current_state_id);
            }

            if (isset($request->current_district_id) && !empty($request->current_district_id)) {
                $current_tehsil_list = $this->tehsilsByDistrictId($request->current_district_id);
            }

            if (isset($request->current_district_id) && !empty($request->current_district_id)) {
                $current_block_list = $this->block_details($request->current_district_id);
            }

            $current_state_name = (isset($state_list[$request->current_state_id])) ? $state_list[$request->current_state_id] : '';
            $current_district_data = $this->get_table_data_by_id($request->current_district_id, 'districts');
            $current_district_name = (isset($current_district_data[0])) ? $current_district_data[0] : '';
            $current_tehsil_data = (isset($request->current_tehsil_id)) ? $this->get_table_data_by_id($request->current_tehsil_id, 'tehsils') : '';
            $current_tehsil_name = (isset($current_tehsil_data[0])) ? $current_tehsil_data[0] : '';
            $current_block_data = (isset($request->current_block_id)) ? $this->get_table_data_by_id($request->current_block_id, 'blocks') : '';
            $current_block_name = (isset($current_block_data[0])) ? $current_block_data[0] : '';

            // current address data
            $request_input = $request->all();

            if ($request->state_id != 6 && isset($request->tehsil_name) && !empty($request->tehsil_name)) {
                $tehsil_name = $request->tehsil_name;
                $request_input['tehsil_id'] = 0;
            }

            if ($request->state_id != 6 && isset($request->block_name) && !empty($request->block_name)) {
                $block_name = $request->block_name;
                $request_input['block_id'] = 0;
            }

            // current address data
            if ($request->current_state_id != 6 && isset($request->current_tehsil_name) && !empty($request->current_tehsil_name)) {
                $current_tehsil_name = $request->current_tehsil_name;
                $request_input['current_tehsil_id'] = 0;
            }

            if ($request->state_id != 6 && isset($request->current_block_name) && !empty($request->current_block_name)) {
                $current_block_name = $request->current_block_name;
                $request_input['current_block_id'] = 0;
            }
            // current address data

            $modelObj = new Address;

            // Student Address Data Updation Log Enteries
            $table_primary_id = @$master->id;
            $table_name = 'addresses';
            $form_type = 'Admission';
            $controller_obj = new Controller;
            $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            // Student Address Data Updation Log Enteries
            //save the current address and is_same_both in db
            $is_both_same = '0';
            if (@$request->is_both_same) {
                $is_both_same = '1';
            }


            // current address set data using conditions


            if ($is_both_same == '1') {
                $current_address1 = strip_tags(@$request->address1);
                $current_address2 = strip_tags(@$request->address2);
                $current_address3 = strip_tags(@$request->address3);
                $current_state_id = strip_tags(@$request->state_id);
                $current_state_name = strip_tags(@$state_name);
                $current_district_id = strip_tags(@$request->district_id);
                $current_district_name = strip_tags(@$district_name);
                $current_tehsil_id = strip_tags(@$request->tehsil_id);
                $current_tehsil_name = strip_tags(@$tehsil_name);
                $current_block_id = strip_tags(@$request->block_id);
                $current_block_name = strip_tags(@$block_name);
                $current_city_name = strip_tags(@$request->city_name);
                $current_pincode = strip_tags(@$request->pincode);
            } else {
                $current_address1 = strip_tags(@$request->current_address1);
                $current_address2 = strip_tags(@$request->current_address2);
                $current_address3 = strip_tags(@$request->current_address3);
                $current_state_id = strip_tags(@$request->current_state_id);
                $current_state_name = strip_tags(@$current_state_name);
                $current_district_id = strip_tags(@$request->current_district_id);
                $current_district_name = strip_tags(@$current_district_name);
                $current_tehsil_id = strip_tags(@$request->current_tehsil_id);
                $current_tehsil_name = strip_tags(@$current_tehsil_name);
                $current_block_id = strip_tags(@$request->current_block_id);
                $current_block_name = strip_tags(@$current_block_name);
                $current_city_name = strip_tags(@$request->current_city_name);
                $current_pincode = strip_tags(@$request->current_pincode);

            }
            // current address set data using conditions

            $custom_data = array(
                'address1' => strip_tags(@$request->address1),
                'address2' => strip_tags(@$request->address2),
                'address3' => strip_tags(@$request->address3),
                'state_id' => strip_tags(@$request->state_id),
                'state_name' => strip_tags(@$state_name),
                'district_id' => strip_tags(@$request->district_id),
                'district_name' => strip_tags(@$district_name),
                'tehsil_id' => strip_tags(@$request->tehsil_id),
                'tehsil_name' => strip_tags(@$tehsil_name),
                'block_id' => strip_tags(@$request->block_id),
                'block_name' => strip_tags(@$block_name),
                'city_name' => strip_tags(@$request->city_name),
                'pincode' => strip_tags(@$request->pincode),
                'is_both_same' => $is_both_same,
                'current_address1' => $current_address1,
                'current_address2' => $current_address2,
                'current_address3' => $current_address3,
                'current_state_id' => $current_state_id,
                'current_state_name' => $current_state_name,
                'current_district_id' => $current_district_id,
                'current_district_name' => $current_district_name,
                'current_tehsil_id' => $current_tehsil_id,
                'current_tehsil_name' => $current_tehsil_name,
                'current_block_id' => $current_block_id,
                'current_block_name' => $current_block_name,
                'current_city_name' => $current_city_name,
                'current_pincode' => $current_pincode

            );
            $newUser = Address::updateOrCreate(['student_id' => $student_id], $custom_data);
            $custom_component_obj = new CustomComponent;
            $isStudentLoigin = $custom_component_obj->_udpateLastAcotionPermedBy($student_id);


            if ($newUser) {
                return redirect()->route('bank_details', Crypt::encrypt($student_id))->with('message', $model . ' successfully saved');
            } else {
                return redirect()->back()->with('error', 'Failed! Student not created');
            }
        }
        return view('student.address_details', compact('isItiStudent', 'isLockAndSubmit', 'model', 'master', 'estudent_id', 'student_id', 'page_title', 'state_list', 'district_list', 'tehsil_list', 'block_list', 'current_district_list', 'current_tehsil_list', 'current_block_list', 'route', 'docrejectednotification', 'tehsil_lists'));
    }

    public function bank_details(Request $request, $student_id)
    {
        $table = $model = "Bank";
        $page_title = 'Bank Details';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);

        $banks = $this->_getListBanksName();

        $ifcs_list = array();
        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);
        $docrejectednotification = $this->student_doc_rejected_notification($student_id);

        if ($isLockAndSubmit == 1) {
            return redirect()->route('view_details', Crypt::encrypt($student_id))->with('message', 'Form already successfully locked and submitted.');
        }
        $isItiStudent = $this->_isItiStudent($student_id);

        $routeUrl = "address_details";
        $previousTableName = "addresses";
        $isValid = $this->getRecordExistorNot($student_id, $previousTableName);
        if (!$isValid) {
            return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! Please first fill the details!');
        }
        $master = BankDetail::where('student_id', $student_id)->first();
        $ifsccodefecthdata = null;
        if (@$master->ifsc_code) {
            $ifsccodefecthdata = DB::table('bank_masters')->where('ifsc_code', @$master->ifsc_code)->whereNull('deleted_at')->orderBy('BRANCH_ADDRESS')->first(['MICR', 'BRANCH_ADDRESS']);
        }

        $state_list = $this->state_details();
        $flipBanks = array_flip($banks);
        $bank_id = @$flipBanks[$master->bank_name];

        if (@$bank_id) {
            $ifcs_list = $this->_getListBanksIfscCode($bank_id);
        }

        $studentdata = Student::findOrFail($student_id);
        $is_dgs_student = false;
        if ($studentdata->is_dgs == 1) {
            $is_dgs_student = true;
        }
        $this->_checkStudentEntryAllowOrNotAllow($studentdata);
        if (count($request->all()) > 0) {
            $modelObj = new BankDetail;

            if ($studentdata->is_dgs == 1) {
                $validator = Validator::make($request->all(), $modelObj->dgsrules);
            } else {
                $validator = Validator::make($request->all(), $modelObj->rules);
            }

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            // Student Bank Detail Updation Log Enteries
            $table_primary_id = @$master->id;
            $table_name = 'bank_details';
            $form_type = 'Admission';
            $controller_obj = new Controller;
            $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            // Student Bank Detail Updation Log Enteries
            $custom_data = array(
                'account_holder_name' => strip_tags(@$request->account_holder_name),
                'branch_name' => strip_tags(@$request->branch_name),
                'account_number' => strip_tags(@$request->account_number),
                'ifsc_code' => strip_tags(@$request->ifsc_code),
                'state_id' => strip_tags(@$request->state_id),
                'bank_name' => (@$banks[@$request->bank_name]),
                'linked_mobile' => strip_tags(@$request->linked_mobile),
                'is_mobile_verified' => 0
            );

            $newUser = BankDetail::updateOrCreate(['student_id' => $student_id,], $custom_data);
            $custom_component_obj = new CustomComponent;
            $isStudentLoigin = $custom_component_obj->_udpateLastAcotionPermedBy($student_id);
            if ($newUser) {
                return redirect()->route('document_details', Crypt::encrypt($student_id))->with('message', $model . ' successfully saved');
            } else {
                return redirect()->back()->with('error', 'Failed! Student not created');
            }
        }

        return view('student.bank_details', compact('state_list', 'isItiStudent', 'flipBanks', 'ifcs_list', 'banks', 'isLockAndSubmit', 'model', 'master', 'is_dgs_student', 'studentdata', 'estudent_id', 'student_id', 'page_title', 'ifsccodefecthdata', 'docrejectednotification'));
    }

    public function document_details(Request $request, $student_id)
    {
        $table = $model = "Document";
        $page_title = 'Document Details';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $studentdata = Student::findOrFail($student_id);
        $this->_checkStudentEntryAllowOrNotAllow($studentdata);
        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);
        $docrejectednotification = $this->student_doc_rejected_notification($student_id);
        if ($isLockAndSubmit == 1) {
            return redirect()->route('view_details', Crypt::encrypt($student_id))->with('message', 'Form already successfully locked and submitted.');
        }
        $isItiStudent = $this->_isItiStudent($student_id);

        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1] . $student_id;

        $routeUrl = "bank_details";
        $previousTableName = "bank_details";
        $isValid = $this->getRecordExistorNot($student_id, $previousTableName);
        if (!$isValid) {
            return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! Please first fill the details!');
        }

        $imageInput = array(
            "photograph" => "फोटोग्राफ(Photograph)",
            "signature" => "(हस्ताक्षर)Signature",
        );

        $master = Document::where('student_id', $student_id)->first();
        $applicationMaster = Application::where('student_id', $student_id)->first();

        if (count($request->all()) > 0) {
            $rr = $request->document_type;
            $document = new Document; /// create model object
            if ($request->document_type == 'i') {
                $validate = Validator::make($request->all(), [
                    $request->document_input => 'required|mimes:jpg,png,jpeg,gif,svg|between:10,50',
                ]);

            } else if ($request->document_type == 'd') {
                $validate = Validator::make($request->all(), [
                    $request->document_input => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|between:50,500'
                ]);

            }
			
            if ($validate->fails()) {
                return back()->withErrors($validate->errors())->withInput();
            }

            //$this->validate($request,$rulesdocument);

            // $validator = Validator::make($request->all(),$rulesdocument);
            // if ($validator->fails()) {
            // return redirect()->back()->withErrors($validator)->withInput($request->all());
            // }

            $input = $request->all();
            $inputName = $input['document_input'];
            $input[$input['document_input']] = $inputName . '.' . $request->$inputName->extension();

            $request->$inputName->move(public_path($studentDocumentPath), $input[$inputName]);

            /* $combo_name = 'document_new_rel_path';
				$document_new_rel_path = $this->master_details($combo_name);
				$document_new_rel_path = $document_new_rel_path[1];
				$combo_name = 'student_document_path';
				$student_document_path = $this->master_details($combo_name);
				$studentDocumentPath = $student_document_path[1].$student_id;
				$finalPath = $document_new_rel_path .  $studentDocumentPath ;
				$finalPath = "C:\rsos\documents\6";
				$arr[] = public_path($studentDocumentPath);
				$arr[] = $finalPath;
				$request->$inputName->move($finalPath, $input[$inputName]);
				dd($arr);
				dd($inputName); */

            // Student Documents Data Updation Log Enteries
            //$table_primary_id = @$master->id;
            //$table_name ='documents';
            //$form_type='Admission';
            //$controller_obj = new Controller;
            //$log_status = $controller_obj->updateStudentLog($table_name,$table_primary_id,$form_type);
            // Student Documents Data Updation Log Enteries

            $document = Document::updateOrCreate(['student_id' => $student_id,], $input);
            $custom_component_obj = new CustomComponent;
            $isStudentLoigin = $custom_component_obj->_udpateLastAcotionPermedBy($student_id);
            if ($document) {
                return redirect()->back()->with('message', 'Document has been successfully submitted.');
            } else {
                return redirect()->back()->with('error', 'Document not submitted.');
            }
        }

        $documentInput = $this->getStudentRequriedDocument($student_id);
        $docIputs = array_keys($documentInput);
        $docContents = $this->getDocumentsContent($docIputs);
        return view('student.document_details', compact('applicationMaster', 'isItiStudent', 'isLockAndSubmit', 'studentDocumentPath', 'page_title', 'estudent_id', 'model', 'student_id', 'docContents', 'imageInput', 'documentInput', 'master', 'docrejectednotification'));
    }

    public function admission_subject_details(Request $request, $student_id)
    {
        $modelObj = new AdmissionSubject;
        $combo_name = 'adminsion_subject_info';
        $adminsion_subject_info = $this->master_details($combo_name);
        $table = $model = "AdmissionSubject";
        $page_title = 'Admission Subject Details(प्रवेश विषय विवरण)';
        $customerrors = array();
        $response = array();
        $subject_list = array();

        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);

        $studentdata = Student::findOrFail($student_id);
        $this->_checkStudentEntryAllowOrNotAllow($studentdata);
        $docrejectednotification = $this->student_doc_rejected_notification($student_id);

        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);
        if ($isLockAndSubmit == 1) {
            return redirect()->route('view_details', Crypt::encrypt($student_id))->with('message', 'Form already successfully locked and submitted.');
        }
        $isItiStudent = $this->_isItiStudent($student_id);

        $documentErrors = $this->getPendingDocuemntDetails($student_id);
        // dd($documentErrors);
        Session::put('studentcourse', @$studentdata->course);
        if (count($documentErrors) > 0) {
            return redirect()->route('document_details', Crypt::encrypt($student_id))->with('error', 'Please upload all document first.');
        }
        $routeUrl = "document_details";
        $studentdata = Student::findOrFail($student_id);
        if (empty($studentdata)) {
            return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! Student not found!');
        }

        $previousTableName = "addresses";
        if ($studentdata->adm_type != 5) {
            $isValid = $this->getRecordExistorNot($student_id, $previousTableName);
            if (!$isValid) {
                return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! Please first fill the details!');
            }
        }


        $subjectCountDetails = $this->getSubjectvalidations($studentdata->adm_type, $studentdata->course, $studentdata->stream, $studentdata->pre_qualification, $studentdata->year_pass);
        // @dd($subjectCountDetails); die;

        $cmax_input = (isset($subjectCountDetails) && !empty($subjectCountDetails)) ? $subjectCountDetails->com_subject_count : 0;
        $amax_input = (isset($subjectCountDetails) && !empty($subjectCountDetails)) ? ($subjectCountDetails->addi_subject_count) : 0;

        $cmin_input = (isset($subjectCountDetails) && !empty($subjectCountDetails)) ? $subjectCountDetails->comp_subject_requried_count : 0;
        $amin_input = (isset($subjectCountDetails) && !empty($subjectCountDetails)) ? ($subjectCountDetails->addi_subject_requried_count) : 0;

        if (isset($amax_input) && is_numeric($amax_input) && $amax_input != null) {
            $aSubArrCount = $amax_input + 5;
        } else {
            $aSubArrCount = 0;
        }

        $subjectWiseFacultyMaster = $faculties = array();
        if ($studentdata->course == 12) {
            $custom_component_obj = new CustomComponent;
            $combo_name = 'faculties';
            $faculties = $this->master_details($combo_name);
            $subjectWiseFacultyMaster = $custom_component_obj->_getSubjectWiseFacultyMaster();
            // $subjectWiseFacultyMaster = $custom_component_obj->_getSubjectFacultyWise(1);//1,2,3,4
        }
        $master = AdmissionSubject::where('student_id', $student_id)->get();
        $application = Application::where('student_id', $student_id)->first();

        if (@$application->faculty_type_id) {

            $subject_list = $this->_get_subject_faculty_wise($application->faculty_type_id);
        } else {
            $subject_list = $this->subjectList($studentdata->course);

        }
        if ($studentdata->adm_type == 5 && $studentdata->course == 10) {
            $subject_list = array('1' => 'Hindi(201)', '2' => 'English(202)');
        }
        if ($studentdata->adm_type == 5 && $studentdata->course == 12) {
            $subject_list = array('19' => 'English(302)');
        }

        $combo_name = 'book_learning_type';
        $book_learning_type = $this->master_details($combo_name);


        if (count($request->all()) > 0) {
            $isValid = true;

            $inputs = $request->subject_id;
            $response = $this->isValidSubjectSelection($inputs, $subjectCountDetails);

            @$faculty_type_id = $request->faculty_type_id;
            @$book_learning_type_id = $request->book_learning_type_id;
            if (@$studentdata->stream == 2) {
                @$book_learning_type_id = 2;
            }
            $response = $this->isValidSubjectSelection($inputs, $subjectCountDetails, $studentdata->course, $faculty_type_id, $book_learning_type_id);
            //here check the given selected inputs and save the information selected_faculty,is_mutiple_faculty,faculty_type_id

            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];

            if ($isValid) {
                $isChange = $this->isChangeInFormData($model, $inputs, $master);
                if ($isChange) {
                    $deleteStatus = $this->deleteDataStudentId($student_id, $model);
                }


                //valid case true save
                $inputs = array_filter($request->subject_id);
                if (isset($inputs) && !empty($inputs)) {

                    // Student Admission Subject Data Updation Log Enteries
                    $table_name = 'admission_subjects';
                    $form_type = 'Admission';
                    $controller_obj = new Controller;
                    foreach (@$master as $masterKey) {
                        $table_primary_id = @$masterKey->id;
                        $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
                    }
                    // Student Admission Subject Data Updation Log Enteries

                    foreach ($inputs as $key => $subject_id) {
                        $is_additional = 0;
                        if ($key == 5 || $key == 6) {
                            $is_additional = 1;
                        }

                        $custom_data[] = array(
                            'student_id' => $student_id,
                            'subject_id' => strip_tags($subject_id),
                            'is_additional' => $is_additional,
                            'exam_year' => $studentdata->exam_year,
                            'stream' => $studentdata->stream,
                            'course' => $studentdata->course,
                            'exam_month' => $studentdata->exam_month,
                            'adm_type' => $studentdata->adm_type,
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s"),
                        );
                    }
                    $combo_name = 'faculties';
                    $faculties = $this->master_details($combo_name);
                    $is_multiple_faculty = @$request->is_multiple_faculty;
                    $selected_faculty = @$faculties[@$faculty_type_id];

                    //update into applications table
                    $updateApplication = Application::where('student_id', $student_id)->update(['faculty_type_id' => $faculty_type_id, 'is_multiple_faculty' => $is_multiple_faculty, 'selected_faculty' => $selected_faculty]);
                    $updatestudent = Student::where('id', $student_id)->update(['book_learning_type_id' => $book_learning_type_id]);

                    $old_subject_data = AdmissionSubject::where("student_id", "=", $student_id);
                    $old_subject_data->delete();
                    $custom_component_obj = new CustomComponent;
                    $isStudentLoigin = $custom_component_obj->_udpateLastAcotionPermedBy($student_id);
                    if (AdmissionSubject::insert($custom_data)) {
                        $cacheName = "AdjustStudentDetailsbject" . $student_id;
                        if (Cache::has($cacheName)) {
                            Cache::forget($cacheName);
                        }

                        if ($studentdata->adm_type == 5) {
                            return redirect()->route('exam_subject_details', Crypt::encrypt($student_id))->with('message', $model . ' successfully saved');
                        } else {
                            return redirect()->route('toc_subject_details', Crypt::encrypt($student_id))->with('message', $model . ' successfully saved');
                        }

                    } else {
                        return redirect()->route('toc_subject_details')->with('error', 'Failed! ' . $model . ' not saved');
                    }
                } else {
                    return redirect()->back()->withErrors($customerrors)->with('error', 'Failed! Please select at least 1 subject.');
                }
            } else {
                return redirect()->back()->withErrors($customerrors)->withInput($request->all());
            }
        }

        $rsos_years_dropdown = $this->rsos_years();
        // dd($studentdata->stream);
        return view('student.admission_subject_details', compact('application', 'book_learning_type', 'adminsion_subject_info', 'faculties', 'subjectWiseFacultyMaster', 'rsos_years_dropdown', 'isItiStudent', 'customerrors', 'aSubArrCount', 'model', 'master', 'estudent_id', 'student_id', 'page_title', 'subject_list', 'cmax_input', 'amax_input', 'studentdata', 'docrejectednotification'));
    }

    public function toc_subject_details(Request $request, $student_id)
    {
        $table = $model = "Toc";
        $page_title = $model . ' Details';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        unset($admission_sessions['121'], $admission_sessions['124'], $admission_sessions['123'], $admission_sessions['122'], $admission_sessions['120'], $admission_sessions['126'], $admission_sessions['13']);
        $previous_year = config("global.previous_year");


        $studentdata = Student::findOrFail($student_id);
        $this->_checkStudentEntryAllowOrNotAllow($studentdata);

        $customerrors = array();
        $response = array();

        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);
        $docrejectednotification = $this->student_doc_rejected_notification($student_id);
        if ($isLockAndSubmit == 1) {
            return redirect()->route('view_details', Crypt::encrypt($student_id))->with('message', 'Form already successfully locked and submitted.');
        }

        $isPartAdmissionStudent = $this->_isPartAdmissionStudent($student_id);

        $studentdata = Student::findOrFail($student_id);
        if (empty($studentdata)) {
            return redirect()->route('registrations')->with('error', 'Failed! registration not saved');
        }
        $subject_list = $this->subjectList($studentdata->course);

        // echo $student_id; die;
        $master = Toc::where('student_id', $student_id)->first();
        // dd($master);

        $toc_marks_master = TocMark::where('student_id', $student_id)->get();

        $application_master = Application::where('student_id', $student_id)->first();
        // dd($application_master);

        // $show_paas_field =$this->showPassFailFieldToc($studentdata->adm_type,$studentdata->stream);
        $show_paas_field = 0;

        $isItiStudent = $this->_isItiStudent($student_id);
        if ($isItiStudent == 1) {
            // removed toc data if we have in db for ITI admission case because student not allowed for TOC
            // Student TOC TAble Data Updation Log Enteries
            $table_name = 'toc';
            $form_type = 'Admission';
            $table_primary_id = @$master->id;
            $controller_obj = new Controller;
            $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            // Student TOC TAble Data Updation Log Enteries

            $old_toc_data_iti_student = Toc::where("student_id", "=", $student_id);
            $old_toc_data_iti_student->delete();


            // Student TOC Marks Data Updation Log Enteries
            $table_name = 'toc_marks';
            $form_type = 'Admission';
            $controller_obj = new Controller;
            foreach (@$toc_marks_master as $tocMarksMasterKey) {
                $table_primary_id = @$tocMarksMasterKey->id;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            }
            // Student TOC Marks Data Updation Log Enteries

            $old_toc_mark_data_iti_student = TocMark::where("student_id", "=", $student_id);
            $old_toc_mark_data_iti_student->delete();

            $toc_status_change_iti_studenmt = Application::where("student_id", "=", $student_id)->update(['toc' => '0', 'is_toc_marked' => '0']);
            // removed toc data if we have in db for ITI admission case because student not allowed for TOC

            return redirect()->route('exam_subject_details', Crypt::encrypt($student_id))->with('message', 'You are not allowed for TOC section.');
        }

        if (count($request->all()) > 0 && $request->is_toc == 1) {
            $modelObj = new Toc;

            if (empty($request->is_toc)) {
                return redirect()->route('dev_toc_subject_details', Crypt::encrypt($student_id))->with('error', 'Please select toc');
            }

            $toc_submit_subject = 0;
            if (!empty($request->toc_subject)) {
                foreach ($request->toc_subject as $key => $each) {
                    if (!empty($each['subject_id'])) {
                        $toc_submit_subject++;
                    }
                }
            }

            $response = $this->tocValidations($request, $request->board, $studentdata->adm_type, $toc_submit_subject);
            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];

            if ($isValid) {
                //valid case true save

                $isChange = $this->isChangeInFormData($model, $request->toc_subject, $toc_marks_master);
                if ($isChange) {
                    $deleteStatus = $this->deleteDataStudentId($student_id, $model);
                }

                // Student TOC TAble Data Updation Log Enteries
                $table_name = 'toc';
                $form_type = 'Admission';
                $table_primary_id = @$master->id;
                $controller_obj = new Controller;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
                // Student TOC TAble Data Updation Log Enteries

                $custom_data = array(
                    'student_id' => $student_id,
                    'year_fail' => strip_tags($request->year_fail),
                    'course' => strip_tags($studentdata->course),
                    'stream' => strip_tags($studentdata->stream),
                    'exam_year' => strip_tags($studentdata->exam_year),
                    'exam_month' => strip_tags($studentdata->exam_month),
                    'board' => strip_tags($request->board),
                    'roll_no' => strip_tags($request->roll_no),
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                );

                $old_toc_data = Toc::where("student_id", "=", $student_id);
                $old_toc_data->delete();
                $custom_component_obj = new CustomComponent;
                $isStudentLoigin = $custom_component_obj->_udpateLastAcotionPermedBy($student_id);

                $toc_id = Toc::insertGetId($custom_data);

                $toc_custom_data = array();
                foreach ($request->toc_subject as $key => $each) {
                    if (isset($each['subject_id']) && !empty($each['subject_id'])) {
                        $toc_custom_data[$key]['student_id'] = $student_id;
                        $toc_custom_data[$key]['toc_id'] = $toc_id;
                        $toc_custom_data[$key]['subject_id'] = $each['subject_id'];
                        $toc_custom_data[$key]['theory'] = $each['theory'];
                        $toc_custom_data[$key]['practical'] = $each['practical'];
                        $toc_custom_data[$key]['total_marks'] = $each['total'];

                        $toc_custom_data[$key]['conv_practical'] = $this->_getCONVTocMarkPractical($request->board, $each['subject_id'], $each['practical'], $each['theory']);


                        $toc_custom_data[$key]['conv_theory'] = $this->_getCONVTocMarkTheory($request->board, $each['subject_id'], $each['theory'], $toc_custom_data[$key]['conv_practical'], $each['practical']);

                        $toc_custom_data[$key]['conv_total_marks'] = ($toc_custom_data[$key]['conv_theory'] + $toc_custom_data[$key]['conv_practical']);

                        /**code copy from landing controller line number 584 to 597*/
                        /*if($studentdata->course == 10){
							 $ddSave['TocMark']['conv_total_marks'] = $each['total'];
							if($request->board == 56){

								$toc_custom_data[$key]['conv_practical'] = $this->_getBserCONVTocMarkPractical($request->board,$each['subject_id'],$each['theory']);

								$toc_custom_data[$key]['conv_theory'] = $this->_getBserCONVTocMarkTheory($request->board,$each['subject_id'],$each['theory'],$toc_custom_data[$key]['conv_practical']);

								$toc_custom_data[$key]['conv_total_marks'] =  $ddSave['TocMark']['conv_total_marks'] = $each['total'];


							}
							if($request->board == 15 && ( $each['subject_id'] == 3 ||  $each['subject_id'] == 4)){
								$toc_custom_data[$key]['conv_practical'] =  $ddSave['TocMark']['conv_practical'] = round(($each['total'] * 15)/100,0);
								$toc_custom_data[$key]['conv_theory'] = $ddSave['TocMark']['conv_theory'] = round(($each['total'] - $ddSave['TocMark']['conv_practical']),0);
								$toc_custom_data[$key]['conv_total_marks'] = $ddSave['TocMark']['conv_total_marks'] = $each['total'];
							}
						}*/

                        $toc_custom_data[$key]['created_at'] = date("Y-m-d H:i:s");
                        $toc_custom_data[$key]['updated_at'] = date("Y-m-d H:i:s");
                    }
                }
                // Student TOC Marks Data Updation Log Enteries
                $table_name = 'toc_marks';
                $form_type = 'Admission';
                $controller_obj = new Controller;
                foreach (@$toc_marks_master as $tocMarksMasterKey) {
                    $table_primary_id = @$tocMarksMasterKey->id;
                    $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
                }
                // Student TOC Marks Data Updation Log Enteries

                $TocMarkmodelObj = new TocMark;
                $old_toc_marks_data = TocMark::where("student_id", "=", $student_id);
                $old_toc_marks_data->delete();
                $TocMarkmodelObj = TocMark::insert($toc_custom_data);

                $toc_status_change = Application::where("student_id", "=", $student_id)->update(['toc' => '1', 'is_toc_marked' => '1']);

                if ($modelObj && $TocMarkmodelObj) {
                    return redirect()->route('exam_subject_details', Crypt::encrypt($student_id))->with('message', $model . ' successfully saved');
                } else {
                    return redirect()->route('exam_subject_details')->with('error', 'Failed! ' . $model . ' not saved');
                }
            } else {
                return redirect()->back()->withErrors($customerrors)->withInput($request->all());
            }
        } else if (count($request->all()) > 0 && $request->is_toc == 0 && $request->is_toc != null) {
            // Student TOC TAble Data Updation Log Enteries
            $table_name = 'toc';
            $form_type = 'Admission';
            $table_primary_id = @$master->id;
            $controller_obj = new Controller;
            $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            // Student TOC TAble Data Updation Log Enteries

            $old_toc_data = Toc::where("student_id", "=", $student_id);
            $old_toc_data->delete();

            // Student TOC Marks Data Updation Log Enteries
            $table_name = 'toc_marks';
            $form_type = 'Admission';
            $controller_obj = new Controller;
            foreach (@$toc_marks_master as $tocMarksMasterKey) {
                $table_primary_id = @$tocMarksMasterKey->id;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            }
            // Student TOC Marks Data Updation Log Enteries

            $old_toc_marks_data = TocMark::where("student_id", "=", $student_id);
            $old_toc_marks_data->delete();
            $custom_component_obj = new CustomComponent;
            $isStudentLoigin = $custom_component_obj->_udpateLastAcotionPermedBy($student_id);

            $toc_status_change = Application::where("student_id", "=", $student_id)->update(['toc' => '0', 'is_toc_marked' => '1']);
            return redirect()->route('exam_subject_details', Crypt::encrypt($student_id))->with('message', $model . ' successfully saved');

        } else if (count($request->all()) > 0 && (!isset($request->is_toc) || $request->is_toc == null)) {

            // Student TOC Table Data Updation Log Enteries
            $table_name = 'toc';
            $form_type = 'Admission';
            $table_primary_id = @$master->id;
            $controller_obj = new Controller;
            $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            // Student TOC Table Data Updation Log Enteries

            $old_toc_data = Toc::where("student_id", "=", $student_id);
            $old_toc_data->delete();

            // Student TOC Marks Data Updation Log Enteries
            $table_name = 'toc_marks';
            $form_type = 'Admission';
            $controller_obj = new Controller;
            foreach (@$toc_marks_master as $tocMarksMasterKey) {
                $table_primary_id = @$tocMarksMasterKey->id;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            }
            // Student TOC Marks Data Updation Log Enteries

            $old_toc_marks_data = TocMark::where("student_id", "=", $student_id);
            $old_toc_marks_data->delete();
            $custom_component_obj = new CustomComponent;
            $isStudentLoigin = $custom_component_obj->_udpateLastAcotionPermedBy($student_id);
            $custom_data = array(
                'student_id' => $student_id,
                'year_fail' => strip_tags($request->year_fail),
                'year_pass' => strip_tags($request->year_pass),
                'course' => strip_tags($studentdata->course),
                'stream' => strip_tags($studentdata->stream),
                'exam_year' => strip_tags($studentdata->exam_year),
                'exam_month' => strip_tags($studentdata->exam_month),
                'board' => strip_tags($request->board),
                'roll_no' => strip_tags($request->roll_no),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            );
            $toc_id = Toc::insertGetId($custom_data);

            $toc_status_change = Application::where("student_id", "=", $student_id)->update(['toc' => '0', 'is_toc_marked' => '1']);
            return redirect()->route('exam_subject_details', Crypt::encrypt($student_id))->with('message', $model . ' successfully saved');
        }

        $routeUrl = "admission_subject_details";
        $previousTableName = "admission_subjects";
        $isValid = $this->getRecordExistorNot($student_id, $previousTableName);
        if (!$isValid) {
            return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! Please first fill the details!');
        }

        $toc_yes_no = $this->master_details('yesno');

        // @dd($student_id);
        $isImprovementStudent = $this->_isImprovementStudent($student_id);
        $student_subject_dropdown = $this->studentSubjectDropdown($student_id, $isImprovementStudent);
        // $student_subject_count =  $this->studentSubjectData($student_id,$isImprovementStudent);

        $student_subject_count = $this->studentSubjectCount($student_id, $isImprovementStudent);


        $rsos_years_dropdown = $this->getRsosYearsList();
        // $combo_name="year"; $rsos_years_dropdown = $this->master_details($combo_name);


        $rsos_years_fail_dropdown = $this->getRsosFailYearsList($request->board);
        // @dd($rsos_years_fail_dropdown);

        // $board_dropdown = $this->getBoardList();


        $board_dropdown = $this->getAdmissionTypeBords($studentdata->adm_type);
        return view('student.toc_subject_details', compact('isImprovementStudent', 'rsos_years_fail_dropdown', 'isPartAdmissionStudent', 'isItiStudent', 'toc_marks_master', 'application_master', 'studentdata', 'show_paas_field', 'customerrors', 'model', 'master', 'estudent_id', 'student_id', 'page_title', 'subject_list', 'toc_yes_no', 'student_subject_dropdown', 'student_subject_count', 'board_dropdown', 'rsos_years_dropdown', 'docrejectednotification', 'admission_sessions', 'previous_year'));
    }

    public function exam_subject_details(Request $request, $student_id)
    {

        $table = $model = "ExamSubject";
        $page_title = $model . ' Details';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);

        $studentdata = Student::findOrFail($student_id);
        $this->_checkStudentEntryAllowOrNotAllow($studentdata);
        $docrejectednotification = $this->student_doc_rejected_notification($student_id);


        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);

        if ($isLockAndSubmit == 1) {
            return redirect()->route('view_details', Crypt::encrypt($student_id))->with('message', 'Form already successfully locked and submitted.');
        }
        $isItiStudent = $this->_isItiStudent($student_id);

        $studentdata = Student::findOrFail($student_id);
        if (empty($studentdata)) {
            return redirect()->route('registrations')->with('error', 'Failed! registration not saved');
        }
        $subject_list = $this->subjectList($studentdata->course);
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $exam_month = @$exam_session[@$studentdata->stream];
        $ExamSubject1 = ExamSubject::where('student_id', $student_id)->get();
        $role_id = Session::get('role_id');
        $super_admin = config("global.super_admin");
        $admin = config("global.admin");
        $developer_admin = config("global.developer_admin");


        if (count($request->all()) > 0) {
            $response = $this->ExamSubjectValidation($request);
            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];

            if ($isValid) {
                $inputs = $request->subject_id;

                $isChange = $this->isChangeInFormData($model, $inputs, $ExamSubject1);
                if ($isChange) {
                    $deleteStatus = $this->deleteDataStudentId($student_id, $model);
                }

                // Student Exam Subject Data Updation Log Enteries
                $table_name = 'exam_subjects';
                $form_type = 'Admission';
                $controller_obj = new Controller;
                foreach (@$ExamSubject1 as $examSubjectMasterKey) {
                    $table_primary_id = @$examSubjectMasterKey->id;
                    $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
                }
                // Student Exam Subject Data Updation Log Enteries

                $deleteOldData = ExamSubject::where('student_id', $student_id)->delete();

                $addtional_subject_arr = AdmissionSubject::where('student_id', $student_id)->where('is_additional', 1)->pluck('subject_id');
                $custom_addtional_subject_arr = array();
                if (!empty($addtional_subject_arr)) {
                    foreach ($addtional_subject_arr as $key => $value) {
                        $custom_addtional_subject_arr[] = $value;
                    }
                }
                foreach ($request->subject_id as $key => $subject_id) {
                    $is_additional = 0;
                    if (in_array($subject_id, $custom_addtional_subject_arr)) {
                        $is_additional = 1;
                    }
                    $studentsubjectdata = array('student_id' => $student_id, 'is_additional' => $is_additional, 'exam_year' => $studentdata->exam_year, 'subject_id' => $subject_id, 'adm_type' => $studentdata->adm_type, 'exam_month' => $studentdata->exam_month, 'course' => $studentdata->course, 'stream' => $studentdata->stream);
                    $studentsubjectsupdate = ExamSubject::updateOrCreate($studentsubjectdata);
                }

                //if admin,developer then true otherwise false and stream-1
                if ($role_id == $developer_admin || $role_id == $super_admin || $role_id == $admin && $studentdata->exam_month == 1) {
                    $showStatus = $this->getupdatesessionasubjectnewtable($student_id);

                }
                $custom_component_obj = new CustomComponent;
                $isStudentLoigin = $custom_component_obj->_udpateLastAcotionPermedBy($student_id);

                if ($studentsubjectsupdate) {
                    return redirect()->route('fee_details', Crypt::encrypt($student_id))->with('message', $model . ' successfully saved');
                } else {
                    return redirect()->back()->with('error', 'Student subject not submitted.');
                }
            } else {
                return redirect()->back()->withErrors($customerrors)->withInput($request->all());
            }
        }


        if ($studentdata->adm_type != 5) {
            $routeUrl = "toc_subject_details";
            $previousTableName = "applications";
            $isValid = $this->getRecordExistorNot($student_id, $previousTableName);
            if (!$isValid) {
                return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! Please first fill the details!');
            }
        }

        $master = $this->_getAdmissionSubjects($student_id, @$studentdata->adm_type);
        return view('student.exam_subject_details', compact('isItiStudent', 'ExamSubject1', 'subject_list', 'model', 'master', 'student_id', 'estudent_id', 'page_title', 'docrejectednotification'));
    }

    public function fee_details(Request $request, $student_id)
    {
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $table = $model = "Fee";
        $page_title = $model . ' Details';
        $routeUrl = "exam_subject_details";
        $previousTableName = "exam_subjects";
        $isValid = $this->getRecordExistorNot($student_id, $previousTableName);
        $docrejectednotification = $this->student_doc_rejected_notification($student_id);
        @$checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
        if (!$isValid) {
            return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! Please first fill the details!');
        }

        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);
        if ($isLockAndSubmit == 1) {
            return redirect()->route('view_details', Crypt::encrypt($student_id))->with('message', 'Form already successfully locked and submitted.');
        }
        $isItiStudent = $this->_isItiStudent($student_id);

        $studentdata = Student::findOrFail($student_id);
        $this->_checkStudentEntryAllowOrNotAllow($studentdata);
        $combo_name = 'book_learning_type';
        $book_learning_type = $this->master_details($combo_name);
        if (count($request->all()) > 0) {
            @$checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
            /* Pay fee start */
            $studentDetailedFees = $this->_getStudentDetailedFee($student_id);
            $master = $this->_getFeeDetailsForDispaly($studentDetailedFees, $student_id);
            if (@$studentdata->student_change_requests == 2 && $checkchangerequestsAllowOrNotAllow == true) {
                @$chnagerequerstfees = $master['final_fees'] - $master['late_fees'];
                $studentfeedata = array('student_id' => $student_id, 'stream' => $studentdata->stream, 'adm_type' => $studentdata->adm_type, 'registration_fees' => $master['registration_fees'],
                    'forward_fees' => $master['forward_fees'], 'online_services_fees' => $master['online_services_fees'],
                    'add_sub_fees' => $master['add_sub_fees'], 'toc_fees' => $master['toc_fees'], 'practical_fees' => $master['practical_fees'],
                    'readm_exam_fees' => $master['readm_exam_fees'], 'total' => $chnagerequerstfees);
            } else {
                $studentfeedata = array('student_id' => $student_id, 'stream' => $studentdata->stream, 'adm_type' => $studentdata->adm_type, 'registration_fees' => $master['registration_fees'],
                    'forward_fees' => $master['forward_fees'], 'online_services_fees' => $master['online_services_fees'],
                    'add_sub_fees' => $master['add_sub_fees'], 'toc_fees' => $master['toc_fees'], 'practical_fees' => $master['practical_fees'],
                    'readm_exam_fees' => $master['readm_exam_fees'], 'late_fee' => $master['late_fees'], 'total' => $master['final_fees']);
            }
            $alredyPresent = StudentFee::where('student_id', $student_id)->first();
            if (@$alredyPresent->id) {
                // Student Fee Data Updation Log Enteries
                $table_name = 'student_fees';
                $form_type = 'Admission';
                $controller_obj = new Controller;
                $table_primary_id = @$alredyPresent->id;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
                // Student Fee Data Updation Log Enteries
                $studentfeesupdate = StudentFee::where('student_id', $student_id)->update($studentfeedata);

                if ($studentdata->student_change_requests == 2 && $checkchangerequestsAllowOrNotAllow == true) {
                    $checkchangerequestupdatefees = $this->changerequestchecklatefeedupdate($student_id);
                }
            } else {
                $studentfeesupdate = StudentFee::updateOrCreate($studentfeedata);
                if ($studentdata->student_change_requests == 2 && $checkchangerequestsAllowOrNotAllow == true) {
                    $checkchangerequestupdatefees = $this->changerequestchecklatefeedupdate($student_id);
                }
            }
            /* Pay fee end */

            /* Pay fee start */
            $studentorgDetailedFees = $this->_getorgStudentDetailedFee($student_id);
            $orgmaster = $this->_getorgFeeDetailsForDispaly($studentorgDetailedFees, $student_id);
            $studentorgfeedata = array('student_id' => $student_id, 'stream' => $studentdata->stream, 'adm_type' => $studentdata->adm_type, 'org_registration_fees' => $orgmaster['org_registration_fees'],
                'org_forward_fees' => $orgmaster['org_forward_fees'], 'org_online_services_fees' => $orgmaster['org_online_services_fees'],
                'org_add_sub_fees' => $orgmaster['org_add_sub_fees'], 'org_toc_fees' => $orgmaster['org_toc_fees'], 'org_practical_fees' => $orgmaster['org_practical_fees'],
                'org_readm_exam_fees' => $orgmaster['org_readm_exam_fees'], 'org_late_fee' => $orgmaster['late_fees'], 'org_total' => $orgmaster['orgfinalFees']);

            $alredyPresent = StudentOrgFee::where('student_id', $student_id)->first();
            if (@$alredyPresent->id) {

                // Student Org Fee Data Updation Log Enteries
                $table_name = 'student_org_fees';
                $form_type = 'Admission';
                $controller_obj = new Controller;
                $table_primary_id = @$alredyPresent->id;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
                // Student Org Fee Data Updation Log Enteries

                $studentorgfeesupdate = StudentOrgFee::where('student_id', $student_id)->update($studentorgfeedata);
            } else {
                $studentorgfeesupdate = StudentOrgFee::updateOrCreate($studentorgfeedata);
            }
            /* Pay fee end */

            $is_update = 0;
            $flagUpdateStatus = $this->updateStudentInfoFlag($student_id, $is_update);
            $custom_component_obj = new CustomComponent;
            $isStudentLoigin = $custom_component_obj->_udpateLastAcotionPermedBy($student_id);
            if ($studentfeesupdate) {
                return redirect()->route('preview_details', Crypt::encrypt($student_id))->with('message', $model . ' successfully saved');
            } else {
                return redirect()->route('preview_details')->with('error', 'Failed! ' . $model . ' not saved');
            }
        }

        $studentDetailedFees = $this->_getStudentDetailedFee($student_id);


        $master = $this->_getFeeDetailsForDispaly($studentDetailedFees, $student_id);


        $studentdata = Student::findOrFail($student_id);
        return view('student.fee_details', compact('isItiStudent', 'book_learning_type', 'master', 'model', 'estudent_id', 'student_id', 'page_title', 'studentdata', 'docrejectednotification', 'checkchangerequestsAllowOrNotAllow'));
    }

    public function preview_details(Request $request, $student_id)
    {
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $docrejectednotification = $this->student_doc_rejected_notification($student_id);
        $combo_name = 'supp_verfication_status';
        $verfication_status = $this->master_details($combo_name);
        @$checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
        @$changerequeststudentdata = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
        @$changerequeststudentdatagets = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->count();

        @$tempCountOfMainMakePaymentButton = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->count();
        @$changerequeststreamgatdata = DB::table('masters')->where('combo_name', '=', 'change_request_stream')->first();
        $isMainPaymentButtonShow = true;
        if ($tempCountOfMainMakePaymentButton > 0) {
            $isMainPaymentButtonShow = false;
        }
        // $smsStatus = $this->_sendEnrollmentGeneratedSubmittedMessage($student_id);
        // echo "Test";die;

        $table = $model = "Preview";
        $page_title = 'Preview Details';
        $documentErrors = null;
        // $sub_code = $this->getSubjectCode($student_id);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);
        if ($isLockAndSubmit == 1) {
            return redirect()->route('view_details', Crypt::encrypt($student_id))->with('message', 'Form already successfully locked and submitted.');
        }
        $isItiStudent = $this->_isItiStudent($student_id);
        $master = $this->getStudentDetails($student_id);

        /* Replace string with X start */
        $custom_component_obj = new CustomComponent;
        $master['personalDetails']['data']['mobile']['value'] = $custom_component_obj->_replaceTheStringWithX(@$master['personalDetails']['data']['mobile']['value']);

        $master['personalDetails']['data']['jan_aadhar_number']['value'] = $custom_component_obj->_replaceTheStringWithX(@$master['personalDetails']['data']['jan_aadhar_number']['value']);
        $master['personalDetails']['data']['aadhar_number']['value'] = $custom_component_obj->_replaceTheStringWithX(@$master['personalDetails']['data']['aadhar_number']['value']);
        if (@$master['bankDetails']['data']['account_number']['value']) {
            $master['bankDetails']['data']['account_number']['value'] = $custom_component_obj->_replaceTheStringWithX(@$master['bankDetails']['data']['account_number']['value']);
        }

        if (@$master['bankDetails']['data']['linked_mobile']['value']) {
            $master['bankDetails']['data']['linked_mobile']['value'] = $custom_component_obj->_replaceTheStringWithX(@$master['bankDetails']['data']['linked_mobile']['value']);
        }
        if (@$master['bankDetails']['data']['ifsc_code']['value']) {
            $master['bankDetails']['data']['ifsc_code']['value'] = $custom_component_obj->_replaceTheStringWithX(@$master['bankDetails']['data']['ifsc_code']['value']);
        }

        $master['TransactionDetails']['data']['challan_tid']['value'] = $custom_component_obj->_replaceTheStringWithX(@$master['TransactionDetails']['data']['challan_tid']['value']);
        /* Replace string with X end */


        // Check Current date for allow lock & submit
        $stream = '';
        if (@$master['personalDetails']['data']['stream']['value'] == 'Stream-I') {
            $stream = 1;
        } else if (@$master['personalDetails']['data']['stream']['value'] == 'Stream-II') {
            $stream = 2;
        }

        $feePaymentAllowOrNot = $this->_checkPaymentAllowedOrNot($stream);

        $feePaymentAllowOrNot = json_decode($feePaymentAllowOrNot);
        $feePaymentAllowOrNotStatus = $feePaymentAllowOrNot->status;
        $getpaymentstudnets = false;
        // $getpaymentstudnets = $this->_checkPaymentAllowedstudentgetrsos($student_id);
        // @dd($feePaymentAllowOrNot);
        // Check Current date for make payment

        $gender_id = '';
        if (@$master['personalDetails']['data']['gender_id']['value'] == 'Female') {
            $gender_id = 1;
        } else if (@$master['personalDetails']['data']['gender_id']['value'] == 'Male') {
            $gender_id = 2;
        }

        $currentDateAllowOrNot = $this->_checkFormAllowedOrNot($stream, $gender_id);

        $currentDateAllowOrNot = json_decode($currentDateAllowOrNot);
        $currentDateAllowOrNotStatus = $currentDateAllowOrNot->status;

        // Check Current date for allow lock & submit

        $mastertocdetails = DB::table('toc_marks')->where('student_id', $student_id)->get();
        $tocdetails = DB::table('toc')->where('student_id', $student_id)->get();
        $tocpassyear = DB::table('rsos_years')->pluck('yearstext', 'id');
        $tocpassfail = DB::table('rsos_years_fail')->pluck('yearstext', 'id');
        $student_subject_list = $this->studentSubjectDropdown($student_id);

        $documentErrors = $this->getPendingDocuemntDetails($student_id);
        $getBoardList = $this->getBoardList();
        $application_fee = $master['studentfeesDetails']['data']['total']['value'];


        $studentdata = Student::findOrFail($student_id);

        $this->_checkStudentEntryAllowOrNotAllow($studentdata);


        /* Fee Details Update Start */
        /* $studentDetailedFees = $this->_getStudentDetailedFee($student_id);
			$master2 = $this->_getFeeDetailsForDispaly($studentDetailedFees,$student_id);

			$studentfeedata2 = array('stream' =>$studentdata->stream,'adm_type' =>$studentdata->adm_type,'registration_fees'=>$master2['registration_fees'],
					'forward_fees'=>$master2['forward_fees'],'online_services_fees'=>$master2['online_services_fees'],
					'add_sub_fees'=>$master2['add_sub_fees'],'toc_fees'=>$master2['toc_fees'],'practical_fees'=>$master2['practical_fees'],
					'readm_exam_fees'=>$master2['readm_exam_fees'],'late_fee'=>$master2['late_fees'],'total'=>$master2['final_fees']);
			$studentfeesupdate = StudentFee::where('student_id',$student_id)->update($studentfeedata2);
		  */
        /* Fee Details Update End */

        $masterrecord = Application::where('student_id', $student_id)->first();
        @$makepaymentchangerequerts = $this->changerequestcheckfees($student_id);
        @$checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();

        if (count($request->all()) > 0) {
            $checkAllowToUpdateFinalLockOrPaymentStatus = $this->checkAllowToUpdateFinalLockOrPayment($studentdata->exam_year, $studentdata->exam_month);
            if (@$checkAllowToUpdateFinalLockOrPaymentStatus) {
            } else {
                return redirect()->back()->with('error', 'Failed! Registration date has been closed for last year student!');
            }
            $application = new Application; /// create model object
            $validator = Validator::make($request->all(), $application->rulesapplicationandstudent);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            if ($request->locksumbitted == 'on') {
                $locksumbitted = '1';
            }
            $student = Student::where('id', $student_id)->first();


            // $student_code = $this->_getStCode($student->stream,$student->course,$student->ai_code);
            $student_code = null;
            $enrollment = null;
            if ($student->stream == "" || $student->course == "" || $student->ai_code == "") {
                return redirect()->back()->with('error', 'Failed! Something is wrong with stream,course and ai center code. hence not submitted.')->withInput($request->all());
            }
            // $enrollment = $this->_generateEnrollment($student->stream,$student->course,$student->ai_code);

            // @dd($enrollment);
            $locksubmitted_date = date("Y-m-d H:i:s");
            if (@$checkchangerequestsAllowOrNotAllow == true && @$student->student_change_requests == 2) {
                $applicationarray = ['locksumbitted' => $locksumbitted, 'locksubmitted_date' => $locksubmitted_date];
            } else {
                $applicationarray = ['locksumbitted' => $locksumbitted, 'locksubmitted_date' => $locksubmitted_date, 'enrollment' => $enrollment];
            }
            // Student tbl Updation Log Enteries
            $table_name = 'applications';
            $form_type = 'Admission';
            $controller_obj = new Controller;
            $table_primary_id = @$masterrecord->id;
            $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            // Student tbl Updation Log Enteries
            $applicationarray = Application::where('student_id', $student_id)->update($applicationarray);

            // $studentenrollmentnum = ['enrollment' => $enrollment,'student_code' => $student_code];
            // Student tbl Updation Log Enteries
            $table_name = 'students';
            $form_type = 'Admission';
            $controller_obj = new Controller;
            $table_primary_id = @$student_id;
            $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            // Student tbl Updation Log Enteries
            // $studentenrollmentnum = Student::where('id',$student_id)->update($studentenrollmentnum);
            if (@$checkchangerequestsAllowOrNotAllow == 'true' && @$makepaymentchangerequerts == 'true' && @$student->student_change_requests == 2) {
                @$smsStatus = $this->_changerequestsendLockSubmittedMessage($student_id);
                $studentsarray = array('update_change_requests_challan_tid' => NULL,
                    'update_change_requests_submitted' => NULL,
                    'is_verifier_verify' => NULL,
                    'is_department_verify' => NULL,
                    'verifier_verify_user_id' => NULL,
                    'department_verify_user_id' => NULL,
                    'verifier_verify_datetime' => NULL,
                    'department_verify_datetime' => NULL,
                    'department_status' => NULL,
                    'verifier_status' => 1,
                    'stage' => NULL,
                    'ao_status' => NULL,
                    'ao_verify_user_id' => NULL,
                    'ao_verify_datetime' => NULL,
                    'is_ao_verify' => NULL,
                    'is_doc_rejected' => 0,
                );
                $table_name = 'students';
                $form_type = 'change_requerst';
                $controller_obj = new Controller;
                $table_primary_id = @$student_id;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
                $applicationarray = Student::where('id', $student_id)->update($studentsarray);
                $rsstudentverifications = DB::table('student_verifications')->where('student_id', $student_id)->update(['deleted_at' => date("Y-m-d H:i:s")]);
                // $studentEligiableData = array('is_eligible' => 1);
                // $studentEligiableData = Student::where('id',$student_id)->update($studentEligiableData);
                //$enrollment = $this->_setEnrollmentAndIsEligiable($student_id);
                // $smsStatus = $this->old_sendoldEnrollmentGeneratedSubmittedMessage($student_id);
                //$smsStatus = $this->_sendLockSubmittedMessage($student_id);
            } elseif (@$checkchangerequestsAllowOrNotAllow == 'true' && @$makepaymentchangerequerts == 'false' && @$student->student_change_requests == 2) {
                $applicationarrays = array('is_ready_for_verifying' => 1);
                $studentsarray = array(
                    'update_change_requests_challan_tid' => NULL,
                    'update_change_requests_submitted' => NULL,
                    'is_verifier_verify' => NULL,
                    'is_department_verify' => NULL,
                    'verifier_verify_user_id' => NULL,
                    'department_verify_user_id' => NULL,
                    'verifier_verify_datetime' => NULL,
                    'department_verify_datetime' => NULL,
                    'department_status' => NULL,
                    'verifier_status' => 1,
                    'stage' => NULL,
                    'ao_status' => NULL,
                    'ao_verify_user_id' => NULL,
                    'ao_verify_datetime' => NULL,
                    'is_ao_verify' => NULL,
                    'is_doc_rejected' => 0,
                    'student_change_requests' => NULL,
                );
                $studentsarrayt = array(
                    'student_id' => @$studentdata->id,
                    'exam_year' => @$studentdata->exam_year,
                    'exam_month' => @$studentdata->exam_month,
                    'student_change_request_id' => @$changerequeststudentdata->id,
                    'prn' => NULL,
                    'amount' => NULL,
                    'change_request_status' => 'NO',
                );
                $table_name = 'students';
                $form_type = 'change_requerst';
                $controller_obj = new Controller;
                $table_primary_id = @$student_id;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
                $table_name = 'applications';
                $form_type = 'change_requerst';
                $controller_obj = new Controller;
                $table_primary_id = @$student_id;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
                $studnetarrays = Student::where('id', $student_id)->update($studentsarray);
                $studnetarraytrail = ChangeRequestStudentTarils::create($studentsarrayt);
                $applicationarray = Application::where('student_id', $student_id)->update($applicationarrays);

                /* Enrolment Change When Change Request By Student Start */
                $is_change_enrollment = $studentdata->is_change_enrollment;
                if ($is_change_enrollment) {
                    $changeReqStatus = $this->_settingChangeReqEnrollment($student_id, $is_change_enrollment);
                }
                /* Enrolment Change When Change Request By Student End */

                $rsstudentverifications = DB::table('student_verifications')->where('student_id', $student_id)->update(['deleted_at' => date("Y-m-d H:i:s")]);
            } else {
                $studentDetailedFees = $this->_getStudentDetailedFee($student_id);
                $master = $this->_getFeeDetailsForDispaly($studentDetailedFees, $student_id);
                $studentfeedata = array('student_id' => $student_id, 'stream' => $studentdata->stream, 'adm_type' => $studentdata->adm_type, 'registration_fees' => $master['registration_fees'],
                    'forward_fees' => $master['forward_fees'], 'online_services_fees' => $master['online_services_fees'],
                    'add_sub_fees' => $master['add_sub_fees'], 'toc_fees' => $master['toc_fees'], 'practical_fees' => $master['practical_fees'],
                    'readm_exam_fees' => $master['readm_exam_fees'], 'late_fee' => $master['late_fees'], 'total' => $master['final_fees']);
                $studentfeesupdate = StudentFee::updateOrCreate($studentfeedata);
                $custom_component_obj = new CustomComponent;
                $isStudentLoigin = $custom_component_obj->_udpateLastAcotionPermedBy($student_id);

                if (@$master['final_fees'] && $master['final_fees'] > 0) {
                    $smsStatus = $this->_sendLockSubmittedMessage($student_id);
                } else {
                    $applicationarray = array();
                    $applicationarray = array('is_ready_for_verifying' => 1);
                    $applicationarray = Application::where('student_id', $student_id)->update($applicationarray);
                    // $studentEligiableData = array('is_eligible' => 1);
                    // $studentEligiableData = Student::where('id',$student_id)->update($studentEligiableData);
                    //$enrollment = $this->_setEnrollmentAndIsEligiable($student_id);
                    // $smsStatus = $this->old_sendoldEnrollmentGeneratedSubmittedMessage($student_id);
                    //$smsStatus = $this->_sendLockSubmittedMessage($student_id);
                }
            }

            if ($applicationarray) {
                return redirect()->route('preview_details', Crypt::encrypt($student_id))->with('message', 'Your complete details has been successfully submitted.');
            } else {
                return redirect()->route('preview_details', Crypt::encrypt($student_id))->with('error', 'Failed! Personal details has been not submitted');
            }
        }

        $combo_name = 'student_declaration';
        $student_declaration = $this->master_details($combo_name);

        if (empty($master)) {
            return redirect()->route('/')->with('error', 'Failed! Details not found');
        }

        $routeUrl = "fee_details";
        $previousTableName = "student_fees";
        $isValid = $this->getRecordExistorNot($student_id, $previousTableName);
        if (!$isValid) {
            return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! Please first fill the details!');
        }

        $documentDetails = Document::where('student_id', $student_id)->first();
        if (@$documentDetails->photograph && @$documentDetails->signature) {
            if ($documentDetails->photograph == "" || $documentDetails->signature == "") {
                return redirect()->route('document_details', Crypt::encrypt($student_id))->with('error', 'Failed! Something is wrong with stream,course and ai center code. hence not submitted.');
            }
        } else {
            return redirect()->route('document_details', Crypt::encrypt($student_id))->with('error', 'Again Failed! Something is wrong with stream,course and ai center code. hence not submitted.');
        }


        /*to show the approved staus to get the Academic Officer verification status */

        /* if((@$master['verifiyDetails']['data']['verifier_status']['value'] && $master['verifiyDetails']['data']['verifier_status']['value'] == "Approved")
		|| (@$master['verifiyDetails']['data']['department_status']['value'] && $master['verifiyDetails']['data']['department_status']['value'] == "Approved")
		|| (@$master['verifiyDetails']['data']['ao_status']['value'] && $master['verifiyDetails']['data']['ao_status']['value'] == "Approved")){
			$finalVerifyStatus = "Approved";
		} */
        $student = Student::where('id', $student_id)->first();
        $finalVerifyStatus = "Not Approved";
        if ((@$student['verifier_status'] && $student['verifier_status'] == 2)
            || (@$student['department_status'] && $student['department_status'] == 2)
            || (@$student['ao_status'] && $student['ao_status'] == 2)) {
            $finalVerifyStatus = "Approved";
        }


        $isShowVerifcationPart = false;
        if ($student->exam_year >= 126) {
            $isShowVerifcationPart = true;
        }
        // dd($masterrecord);

        return view('student.preview_details', compact('changerequeststreamgatdata', 'isShowVerifcationPart', 'checkchangerequestsAllowOrNotAllow', 'makepaymentchangerequerts', 'isMainPaymentButtonShow', 'finalVerifyStatus', 'feePaymentAllowOrNotStatus', 'application_fee', 'categorya', 'isItiStudent', 'student_declaration', 'isLockAndSubmit', 'model', 'master', 'estudent_id', 'student_id', 'documentErrors', 'page_title', 'masterrecord', 'mastertocdetails', 'student_subject_list', 'currentDateAllowOrNotStatus', 'tocdetails', 'tocpassyear', 'tocpassfail', 'getBoardList', 'docrejectednotification', 'studentdata', 'verfication_status', 'checkchangerequestsAllowOrNotAllow', 'changerequeststudentdata', 'changerequeststudentdatagets', 'getpaymentstudnets'));
    }

    public function request_to_dept(Request $request, $student_id = null)
    {
        $table = $model = "Student";
        $page_title = 'Request to Department for Basic details Update';

        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);

        $master = $this->getStudentDetails($student_id);
        $studentdata = Student::findOrFail($student_id);

        if (count($request->all()) > 0) {
            $inputs = $request->all();
            if (@$inputs['request_to_dept_remarks']) {
                $studentSaveData = array('verifier_status' => 5, 'department_status' => 5, 'request_to_dept_remarks' => $inputs['request_to_dept_remarks']);
                $studentEligiableData = Student::where('id', $student_id)->update($studentSaveData);
                return redirect()->route('verify_documents', Crypt::encrypt($student_id))->with('message', 'Reason of student basic details has been successfully submitted.');
            } else {
                return redirect()->back()->withInput($request->all())->with('error', 'Failed! Something is wrong.');
            }
        }
        return view('student.request_to_dept', compact('student_id', 'master', 'estudent_id', 'model', 'page_title', 'studentdata'));
    }

    public function view_details(Request $request, $student_id)
    {
        $table = $model = "Student";
        $page_title = 'View Details';

        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $docrejectednotification = $this->student_doc_rejected_notification($student_id);
        $documentErrors = null;
        $studentdata = Student::findOrFail($student_id);
        $combo_name = 'supp_verfication_status';
        $verfication_status = $this->master_details($combo_name);
        @$changerequeststudentdata = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();

        @$changerequeststreamgatdata = DB::table('masters')->where('combo_name', '=', 'change_request_stream')->first();

        @$tempCountOfMainMakePaymentButton = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->count();

        $isMainPaymentButtonShow = true;
        if ($tempCountOfMainMakePaymentButton > 0) {
            $isMainPaymentButtonShow = false;
        }

        // $smsStatus = $this->_sendLockSubmittedMessage($student_id);
        // dd($smsStatus);

        /* Fee Details Update Start */
        // $studentdata = Student::findOrFail($student_id);
        // $studentDetailedFees = $this->_getStudentDetailedFee($student_id);
        // $master = $this->_getFeeDetailsForDispaly($studentDetailedFees,$student_id);

        // $studentfeedata = array('stream' =>$studentdata->stream,'adm_type' =>$studentdata->adm_type,'registration_fees'=>$master['registration_fees'],
        // 		'forward_fees'=>$master['forward_fees'],'online_services_fees'=>$master['online_services_fees'],
        // 		'add_sub_fees'=>$master['add_sub_fees'],'toc_fees'=>$master['toc_fees'],'practical_fees'=>$master['practical_fees'],
        // 		'readm_exam_fees'=>$master['readm_exam_fees'],'late_fee'=>$master['late_fees'],'total'=>$master['final_fees']);
        // $studentfeesupdate = StudentFee::where('student_id',$student_id)->update($studentfeedata);

        /* Fee Details Update End */
        /* change requert code start */
        $checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
        $master = $this->getStudentDetails($student_id);
        $makepaymentchangerequerts = $this->changerequestcheckfees($student_id);
        $changemakepayment = $this->changerequestcheckfeesdifference($student_id);
        /* change requert code end */


        // Check Current date for make payment

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

        $feePaymentAllowOrNot = $this->_checkPaymentAllowedOrNot($stream);
        // dd($feePaymentAllowOrNot);
        $feePaymentAllowOrNot = json_decode($feePaymentAllowOrNot);
        $feePaymentAllowOrNotStatus = $feePaymentAllowOrNot->status;
        // @dd($feePaymentAllowOrNotStatus);
        // Check Current date for make payment

        $masterrecord = Application::where('student_id', $student_id)->first();

        $student_subject_list = $this->studentSubjectDropdown($student_id);
        $mastertocdetails = DB::table('toc_marks')->where('student_id', $student_id)->get();
        $tocdetails = DB::table('toc')->where('student_id', $student_id)->get();
        $tocpassyear = DB::table('rsos_years')->pluck('yearstext', 'id');
        $tocpassfail = DB::table('rsos_years_fail')->pluck('yearstext', 'id');
        $application_fee = $master['studentfeesDetails']['data']['total']['value'];
        $getBoardList = $this->getBoardList();
        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);


        if ($isLockAndSubmit != 1) {
            return redirect()->route('preview_details', Crypt::encrypt($student_id))->with('error', 'Form not successfully locked and submitted.');
        }
        $isItiStudent = $this->_isItiStudent($student_id);
        if (empty($master)) {
            return redirect()->route('/')->with('error', 'Failed! Details not found');
        }
        $student = Student::where('id', $student_id)->first();
        $finalVerifyStatus = "Not Approved";
        $isShowVerifcationPart = false;
        if ($student->exam_year >= 126) {
            $isShowVerifcationPart = true;
        }

        // if((@$master['verifiyDetails']['data']['verifier_status']['value'] && $master['verifiyDetails']['data']['verifier_status']['value'] == "Approved")
        // || (@$master['verifiyDetails']['data']['department_status']['value'] && $master['verifiyDetails']['data']['department_status']['value'] == "Approved")
        // || (@$master['verifiyDetails']['data']['ao_status']['value'] && $master['verifiyDetails']['data']['ao_status']['value'] == "Approved")
        // ){
        // $finalVerifyStatus = "Approved";
        // }

        if ((@$student['verifier_status'] && $student['verifier_status'] == 2)
            || (@$student['department_status'] && $student['department_status'] == 2)
            || (@$student['ao_status'] && $student['ao_status'] == 2)) {
            $finalVerifyStatus = "Approved";
        }

        return view('student.preview_details', compact('isShowVerifcationPart', 'isMainPaymentButtonShow', 'finalVerifyStatus', 'application_fee', 'isItiStudent', 'documentErrors', 'model', 'isLockAndSubmit', 'master', 'estudent_id', 'student_id', 'page_title', 'masterrecord', 'mastertocdetails', 'student_subject_list', 'feePaymentAllowOrNotStatus', 'tocdetails', 'tocpassyear', 'tocpassfail', 'getBoardList', 'studentdata', 'docrejectednotification', 'verfication_status', 'makepaymentchangerequerts', 'changemakepayment', 'checkchangerequestsAllowOrNotAllow', 'changerequeststudentdata', 'changerequeststreamgatdata'));
    }

    public function index(Request $request)
    {

        // $status = $this->_sendLockSubmittedMessage(587397);dd($status);

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
        $aiCenters = $custom_component_obj->getAiCenters(null, null, 1);
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
                'url' => 'downloadApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => true
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
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Father Name",
                'fld' => 'father_name',
                'input_type' => 'text',
                'filter_type' => 'like',
                'placeholder' => "Father Name",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Mother Name",
                'fld' => 'mother_name',
                'input_type' => 'text',
                'filter_type' => 'like',
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
                'dbtbl' => 'applications',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'filter_type' => 'like',
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
                'dbtbl' => 'applications',
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
                'dbtbl' => 'students',
            ), array(
                "lbl" => "AI Code",
                'fld' => 'ai_code',
                'input_type' => 'text',
                'placeholder' => "AI Code",
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
        $rawConditions = null;
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $conditions["students.user_id"] = @Auth::user()->id;
        } else {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'students',
            );
            $filters[] = array(
                "lbl" => "Student id",
                'fld' => 'id',
                'input_type' => 'text',
                'placeholder' => "Student id",
                'dbtbl' => 'students',
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
                    'fld_url' => 'student/persoanl_details/#id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => 'student/preview_details/#id#'
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
                if (@$_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP) {
                    $deleteVal = true;
                }
            }
            $deleteVal = true;
            if ($deleteVal == true) {
                // $actions[] = array(
                // 	'fld' => 'studentdelete', //For active studentdeleteactive
                // 	'class' => 'delete-confirm',
                // 	'icon' => '<i class="material-icons" title="Click here to Delete.">delete</i>',
                // 	'fld_url' => 'studentdelete/#id#' //For active studentdeleteactive
                // );

                $actions[] = array(
                    'fld' => 'studentrejectdelete', //For active studentdeleteactive
                    'class' => 'delete-confirm2',
                    'icon' => '<i class="material-icons" title="Click here to Delete.">delete</i>',
                    'fld_url' => 'student/studentrejectdelete/#id#' //For active studentdeleteactive
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
                if (@$_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP || @$_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP3) {
                    $unlockVal = true;
                }
            }
            if ($unlockVal == true) {
                $actions[] = array(
                    'fld' => 'studentunlock', //For active studentdeleteactive
                    'class' => 'unlock-student',
                    'icon' => '<i class="material-icons md-18" title="Click here to Active.">lock</i>',
                    'fld_url' => 'studentunlock/#id#' //For active studentdeleteactive
                );
            }

        } else {
            $actions = array(
                // array(
                // 	'fld' => 'view',
                // 	'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                // 	'fld_url' => '../student/preview_details/#id#'
                // ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => 'student/preview_details/#id#'
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
                if (isset($filters[$ik]['filter_type']) && $filters[$ik]['filter_type'] == 'like' && !empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    if ($rawConditions === null) {
                        $rawConditions = " rs_" . $iv['dbtbl'] . "." . $iv['fld'] . " like '%" . $inputs[$iv['fld']] . "%' ";
                    } else {
                        if (!empty($conditions)) {
                            $rawConditions .= ' and rs_' . $iv['dbtbl'] . "." . $iv['fld'] . " like '%" . $inputs[$iv['fld']] . "%' ";
                        } else {
                            $rawConditions .= ' rs_' . $iv['dbtbl'] . "." . $iv['fld'] . " like '%" . $inputs[$iv['fld']] . "%' ";
                        }
                    }
                } else if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);
        Session::put($formId . '_rawConditions', $rawConditions);

        $master = $custom_component_obj->getApplicationData($formId);
        return view('student.index', compact('actions', 'master', 'tableData', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'course', 'stream_id', 'adm_types'));
    }

    public function deletestudent(Request $request)
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
        $yes_no = $this->master_details('yesno');
        $title = "Deactived Students";
        $table_id = "Deactived Students";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $yes_no = $this->master_details('yesno');
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters(null, null, 1);
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
                'url' => 'downloaddeactivedstudentsexcel',
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
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'search_type' => "like",
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
                "lbl" => "Is Eligible",
                'fld' => 'is_eligible',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Is Eligiable',
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Challan Number",
                'fld' => 'challan_tid',
                'input_type' => 'text',
                'placeholder' => "Challan Number",
                'search_type' => "text", //like
                'dbtbl' => 'students',
            ),
            array(
                "lbl" => "Lock & Submit",
                'fld' => 'locksumbitted',
                'input_type' => 'select',
                'options' => $yes_no,
                'search_type' => "text",
                'placeholder' => 'Lock & Submit',
                'dbtbl' => 'applications',
            )
        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $conditions["students.exam_year"] = CustomHelper::_get_selected_sessions();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
            $conditions["students.user_id"] = @Auth::user()->id;
        } else {

        }
        if (in_array("application_dashboard", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => '../student/persoanl_details/#id#'
                ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to View.">remove_red_eye</i>',
                    'fld_url' => '../student/preview_details/#id#'
                ),

            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to view.">remove_red_eye</i>',
                    'fld_url' => '../student/preview_details/#id#'
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
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (@$iv['fld'] == $k && $iv['fld'] == $k) {
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


        $data = $custom_component_obj->getDeleteStudentdata($formId);

        return view('student.deletestudent', compact('actions', 'data', 'aiCenters', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'gender_id', 'yes_no', 'midium', 'course', 'stream_id', 'adm_types'));
    }

    public function downloaddeactivedstudentsexcel(Request $request, $type = "xlsx")
    {
        $application_exl_data = new Deactivedstudentsexcel;
        $filename = 'Deactivedstudentsexceldata' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function download($path = null)
    {
        $path = Crypt::decrypt($path);
        $download_path = (public_path() . $path);
        Response::download($download_path);
        return (Response::download($download_path));
    }

    public function dashboard()
    {
        $records['total_students'] = Student::where('id', '!=', '')->count();
        return view('student.dashboard', ['records' => $records]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'ssoid' => 'required|unique:students',
            'email' => 'required|email',
            'roles' => 'required',
            'name' => 'required',

        ]);

        $student = new Student;
        $student->ssoid = $request->ssoid;
        $student->email = $request->email;
        $student->password = Hash::make('123456789');
        $student->save();

        $student->assignRole($request->input('roles'));
        if ($student) {
            return redirect()->route('students.index')->with('message', 'Student successfully created');
        } else {
            return redirect()->route('students.index')->with('error', 'Failed! User not created');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $student = Student::find($id);
        return view('student.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $student = Student::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $student->roles->pluck('name', 'name')->all();

        return view('student.edit', compact('student', 'roles', 'userRole'));
    }

    public function studentunlock($id)
    {
        $combo_name = 'student_deactive_remarks';
        $student_deactive_remarks = $this->master_details($combo_name);

        $unlockVal = false;
        $masterIP = '10.68.181.236';
        if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == $masterIP) {
            $masterIP2 = '10.68.181.213';
            $masterIP3 = '10.68.181.229';
            $masterIP4 = '10.68.181.249';
            $masterIP5 = '10.68.181.51';
            if ($_SERVER['REMOTE_ADDR'] == $masterIP || $_SERVER['REMOTE_ADDR'] == $masterIP3 || $_SERVER['REMOTE_ADDR'] == $masterIP2) {
                $unlockVal = true;
            }
        } else if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'rsosadmission.rajasthan.gov.in' || $_SERVER['HTTP_HOST'] == 'www.rsosadmission.rajasthan.gov.in')) {
            if (@$_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP) {
                $unlockVal = true;
            }
        }
        if ($unlockVal == true) {
            $student_id = Crypt::decrypt($id);
            $appLock = Application::where('student_id', '=', $student_id)->where('locksumbitted', '=', 1)->count();

            if ($appLock <= 0) {
                return redirect()->route('students.index')->with('error', 'Failed! Student application still not locked and submitted.');
            }

            $studentarray = ['enrollment' => NULL, 'student_code' => NULL];
            $Student = Student::where('id', $student_id)->update($studentarray);

            $applicationarray = ['enrollment' => NULL, 'locksumbitted' => NULL, 'locksubmitted_date' => NULL];
            $Application = Application::where('student_id', $student_id)->update($applicationarray);

            return redirect()->route('students.index')->with('message', 'Student successfully unlocked');
        } else {
            return redirect()->route('students.index')->with('error', 'Failed! Student does not unlocked');
        }
    }

    public function destroy($id)
    {
        $combo_name = 'student_deactive_remarks';
        $student_deactive_remarks = $this->master_details($combo_name);
        $deleteVal = false;
        $masterIP = '10.68.181.236';
        if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == $masterIP) {
            $masterIP2 = '10.68.181.213';
            $masterIP3 = '10.68.181.229';
            $masterIP4 = '10.68.181.249';
            $masterIP5 = '10.68.181.51';
            if ($_SERVER['REMOTE_ADDR'] == $masterIP || $_SERVER['REMOTE_ADDR'] == $masterIP3) {
                $deleteVal = true;
            }
        } else if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'rsosadmission.rajasthan.gov.in' || $_SERVER['HTTP_HOST'] == 'www.rsosadmission.rajasthan.gov.in')) {
            if (@$_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP) {
                $deleteVal = true;
            }
        }
        if ($deleteVal == true) {
            $id = Crypt::decrypt($id);
            $updatestudent = Student::find($id);
            $updatestudent->is_refund = 0;
            $updatestudent->remarks = $student_deactive_remarks[1] . "_ " . date("Y-m-d H:i:s");
            $updatestudent->save();
            $updateApplication = Application::where('student_id', $id)->first();

            $student = Student::where('id', $id)->delete();

            $fld = "id";
            if (@$updateApplication->$fld) {
                $fld = "jan_id";
                $updateApplication->$fld = $updateApplication->$fld . "_" . $id;
                $fld = "jan_aadhar_number";
                $updateApplication->$fld = $updateApplication->$fld . "_" . $id;
                $fld = "aadhar_number";
                $updateApplication->$fld = $updateApplication->$fld . "_" . $id;
                $updateApplication->save();
            } else {
                return redirect()->route('students.index')->with('message', 'Student successfully Deactive but Application detials not found.');
            }

            if ($student) {
                return redirect()->route('students.index')->with('message', 'Student successfully Deactive.');
            } else {
                return redirect()->route('students.index')->with('error', 'Failed! Student not Deactive');
            }
        } else {
            return redirect()->route('students.index')->with('error', 'Failed! Student not Deactive');
        }
    }

    public function studentdeleteactive($id = null)
    {
        $combo_name = 'student_active_remarks';
        $student_active_remarks = $this->master_details($combo_name);
        $deleteVal = false;
        $masterIP = '10.68.181.236';
        if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == $masterIP) {
            $masterIP2 = '10.68.181.213';
            $masterIP3 = '10.68.181.229';
            $masterIP4 = '10.68.181.249';
            $masterIP5 = '10.68.181.51';
            if ($_SERVER['REMOTE_ADDR'] == $masterIP || $_SERVER['REMOTE_ADDR'] == $masterIP3) {
                $deleteVal = true;
            }
        } else if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'rsosadmission.rajasthan.gov.in' || $_SERVER['HTTP_HOST'] == 'www.rsosadmission.rajasthan.gov.in')) {
            if (@$_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP) {
                $deleteVal = true;
            }
        }
        if ($deleteVal == true) {
            $id = Crypt::decrypt($id);
            Student::withTrashed()->where('id', '=', $id)->restore();
            $student = $updatestudent = Student::find($id);
            $updatestudent->active_remarks = $student_active_remarks[1] . "_ " . date("Y-m-d H:i:s");
            $updatestudent->save();
            $updateApplication = Application::where('student_id', $id)->first();


            $fld = "id";
            if (@$updateApplication->$fld) {
                $fld = "jan_id";
                $updateApplication->$fld = $updateApplication->$fld . "_" . $id;
                $fld = "aadhar_number";
                $updateApplication->$fld = $updateApplication->$fld . "_" . $id;
                $updateApplication->save();
            } else {
                return redirect()->route('deletestudent')->with('message', 'Student successfully Active but Application detials not found.');
            }
            if ($student) {
                return redirect()->route('deletestudent')->with('message', 'Student successfully Active.');
            } else {
                return redirect()->route('deletestudent')->with('error', 'Failed! Student not Active');
            }
        } else {
            return redirect()->route('deletestudent')->with('error', 'Failed! Student not Active');
        }
    }

    public function supp_download($student_id = null, $filename = null)
    {

        $studentcurrentmonth = Config::get('global.supp_current_admission_exam_month');
        $combo_name = 'student_supplementary_document_path';
        $student_document_path = $this->master_details($combo_name);
        $current_folder_year = $this->getCurrentYearFolderName();

        $studentDocumentPath = $student_document_path[1] . $current_folder_year . '/' . $studentcurrentmonth . '/' . ($student_id) . '/' . ($filename);
        $download_path = public_path($studentDocumentPath);

        Response::download($download_path);
        return (Response::download($download_path));
    }

    public function generate_student_pdf($student_id = null)
    {
        $table = $model = "Student";
        $page_title = 'View Details';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);

        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters(null, null, 1);

        $examyear = CustomHelper::_get_selected_sessions();

        $changerequeststudent = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();

        $changerequeststudentgetdatachangerequests = ChangeRequeStstudent::join('change_request_erequests', 'change_request_erequests.student_change_request_id', '=', 'change_request_students.id')->
        where('change_request_students.student_id', $student_id)->where('change_request_students.student_update_application', 1)->where('change_request_erequests.rtype', 1)->where('change_request_erequests.status', 1)->whereNotNull('change_request_erequests.challan_tid')->whereNotNull('change_request_erequests.prn')->get();

        $changerequeststudentcount = ChangeRequestStudentTarils::where('student_id', $student_id)->get();
        $changerequeststudentgetdatecount = count($changerequeststudentcount);
        $changerequeststudentgetdate = count($changerequeststudentgetdatachangerequests);
        // dd($changerequeststudentgetdate);


        $master = Student::with('application', 'document', 'address', 'admission_subject', 'toc_subject', 'exam_subject', 'StudentFee', 'tocdetils', 'bankdetils')->where('id', $student_id)->first();
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
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $combo_name = 'book_learning_type';
        $book_learning_type = $this->master_details($combo_name);
        $combo_name = 'fresh_student_verfication_status';
        $fresh_student_verfication_status = $this->master_details($combo_name);
        $subject_list = $this->subjectList();
        $rsos_years = $this->rsos_years();
        $getBoardList = $this->getBoardList();
        $studentDocumentPath = $student_document_path[1] . $student_id;
        $master_subject_details = $this->getStudentPdfDetails($student_id);
        $tocpassyear = DB::table('rsos_years')->pluck('yearstext', 'id');
        $tocpassfail = DB::table('rsos_years_fail')->pluck('yearstext', 'id');

        $passfailyers = $this->getfailandpassingyears(@$master->adm_type, @$master->stream, @$master->tocdetils->board, @$master->application->toc);


        if (empty($master)) {
            return redirect()->route('/')->with('error', 'Failed! Details not found');
        }

        $fld = "documentDetails";
        if (isset($master[$fld])) {
            unset($master[$fld]);
        }
        $url = url('/payments/admission_fee_payment');

        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $current_admission_session_id = Config::get("global.current_admission_session_id");
        $curent_session_text = @$admission_sessions[$current_admission_session_id];


        /* Replace string with X start */
        $custom_component_obj = new CustomComponent;
        //$master->mobile = $custom_component_obj->_replaceTheStringWithX(@$master->mobile);
        $master->application->jan_aadhar_number = $custom_component_obj->_replaceTheStringWithX(@$master->application->jan_aadhar_number);
        $master->application->aadhar_number = $custom_component_obj->_replaceTheStringWithX(@$master->application->aadhar_number);
        if (@$master->bankdetils->account_number) {
            $master->bankdetils->account_number = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->account_number);
        }
        if (@$master->bankdetils->linked_mobile) {
            $master->bankdetils->linked_mobile = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->linked_mobile);
        }
        if (@$master->bankdetils->ifsc_code) {
            $master->bankdetils->ifsc_code = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->ifsc_code);
        }
        $master->challan_tid = $custom_component_obj->_replaceTheStringWithX(@$master->challan_tid);
        /* Replace string with X end */
        $super_admin_id = Config::get("global.super_admin_id");
        $documentverifications = DocumentVerification::where('student_id', $student_id)
            ->where('role_id', $super_admin_id)
            // ->whereNotNull('challan_tid')
            ->orderby("id", "DESC")->first();
        $finalVerifyStatus = "Not Approved";

        if ((@$master['verifier_status'] && $master['verifier_status'] == 2)
            || (@$master['department_status'] && $master['department_status'] == 2)
            || (@$master['ao_status'] && $master['ao_status'] == 2)) {
            $finalVerifyStatus = "Approved";
        }
        $student = Student::where('id', $student_id)->first();
        $isShowVerifcationPart = false;
        if ($student->exam_year >= 126) {
            $isShowVerifcationPart = true;
        }
        $combo_name = 'department_fresh_form_rejection_charge_amount';
        $department_fresh_form_rejection_charge_amount = $this->master_details($combo_name);
        $studentamount = $department_fresh_form_rejection_charge_amount[1];

        $pdf = PDF::loadView('student.generate_student_pdf', compact('isShowVerifcationPart', 'changerequeststudent', 'studentamount', 'documentverifications', 'finalVerifyStatus', 'admission_sessions', 'exam_month', 'curent_session_text', 'yesno', 'master_subject_details', 'subject_list', 'master', 'studentDocumentPath', 'student_id', 'page_title', 'estudent_id', 'model', 'gender_id', 'categorya', 'nationality', 'religion', 'disability', 'dis_adv_group', 'midium', 'rural_urban', 'employment', 'pre_qualifi', 'adm_types', 'course', 'exam_session', 'rsos_years', 'getBoardList', 'passfailyers', 'url', 'aiCenters', 'stream_id', 'are_you_from_rajasthan', 'tocpassyear', 'tocpassfail', 'book_learning_type', 'fresh_student_verfication_status', 'changerequeststudentgetdate', 'changerequeststudentgetdatachangerequests', 'changerequeststudentcount', 'changerequeststudentgetdatecount', 'examyear'));
        // dd($pdf);
        // return view('student.generate_student_pdf', compact('isShowVerifcationPart','changerequeststudent','studentamount','documentverifications','finalVerifyStatus','admission_sessions','exam_month','curent_session_text','yesno','master_subject_details','subject_list','master','studentDocumentPath','student_id', 'page_title', 'estudent_id', 'model','gender_id','categorya','nationality','religion','disability','dis_adv_group','midium','rural_urban','employment','pre_qualifi','adm_types','course','exam_session','rsos_years','getBoardList','passfailyers','url','aiCenters','stream_id','are_you_from_rajasthan','tocpassyear','tocpassfail','book_learning_type','fresh_student_verfication_status','changerequeststudentgetdate','changerequeststudentgetdatachangerequests'));

        $path = public_path('studentpdf/ApplicationForm-' . $student_id . '.pdf');

        $pdf->save($path, $pdf, true);
        // echo $path;die;
        return $pdf->download('studentadmission.pdf');
        //return view('student.generate_student_pdf', compact('studentamount','master_subject_details','subject_list','master','studentDocumentPath','student_id', 'page_title', 'estudent_id', 'model','gender_id','categorya','nationality','religion','disability','dis_adv_group','midium','rural_urban','employment','pre_qualifi','adm_types','course','exam_session','rsos_years','getBoardList','passfailyers','url','aiCenters','stream_id','are_you_from_rajasthan','book_learning_type'));
    }

    public function _getNewJanAadharDetails($janAadharNumberOrAckNumber = null)
    { //1088-UAJP-15800 sample
        $ch = curl_init();
        $url = 'https://api.sewadwaar.rajasthan.gov.in/app/live/Janaadhaar/Prod/Service/action/fetchJayFamily/' . $janAadharNumberOrAckNumber . '?client_id=f6de7747-60d3-4cf0-a0ae-71488abd6e95';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "someusername:secretpassword");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $result = curl_exec($ch);
        return $result;
    }

    public function _settingNewJanAadharDetails($response = null)
    {

        $memberDetails = array();
        if ($response['cmsg'] != 110) {
            $counter = 0;
            $members = $response['personalInfo']['member'];
            // echo $member_number;
            // echo "<br>";
            // echo "<pre>";
            // print_r($jan_id);

            if ($total_member == 1) {
                $v = $members;
                $fld = "jan_mid";
                $memberDetails["Application"][$fld] = $v[$fld];
                $fld = "jan_mid";
                $memberDetails["Application"][$fld] = $v[$fld];
                $fld = "hof_jan_m_id";
                $memberDetails["Application"][$fld] = $v[$fld];
                $fld = "nameEng";
                $memberDetails["Student"]['name'] = $v[$fld];
                $memberDetails["Application"]['student_name'] = $v[$fld];


                $fld = "fnameEng";
                $memberDetails["Student"]['father_name'] = $v[$fld];
                $fld = "mnameEng";
                $memberDetails["Student"]['mother_name'] = $v[$fld];
                $fld = "dob";
                $memberDetails["Student"][$fld] = $v[$fld];
                $fld = "mobile";
                $memberDetails["Student"][$fld] = '';
                $fld = "janaadhaarId";
                $memberDetails["Application"]['jan_aadhar_number'] = $v[$fld];
                $fld = "aadhar";
                $memberDetails["Application"][$fld] = $v[$fld];
                $fld = "gender";
                if ($v[$fld] == 'Male') {
                    $memberDetails["Student"]['gender_id'] = 1;
                }
                if ($v[$fld] == 'Female') {
                    $memberDetails["Student"]['gender_id'] = 2;
                }
                $fld = "age";
                $memberDetails["Application"][$fld] = $v[$fld];

            } else {

                if (isset($members) && !empty($members)) {
                    foreach ($members as $k => $v) {
                        if ($v['jan_mid'] == "0") {
                            $v['jan_mid'] = $v['hof_jan_m_id'];
                        }
                        if ($v['jan_mid'] != $jan_id) {
                            continue;
                        }
                        $fld = "jan_mid";
                        $memberDetails["Application"][$fld] = $v[$fld];
                        $fld = "jan_mid";
                        $memberDetails["Application"][$fld] = $v[$fld];
                        $fld = "hof_jan_m_id";
                        if (isset($v[$fld])) {
                            $memberDetails["Application"][$fld] = $v[$fld];
                        }

                        $fld = "nameEng";
                        $memberDetails["Student"]['name'] = $v[$fld];
                        $memberDetails["Application"]['student_name'] = $v[$fld];


                        $fld = "fnameEng";
                        $memberDetails["Student"]['father_name'] = $v[$fld];
                        $fld = "mnameEng";
                        $memberDetails["Student"]['mother_name'] = $v[$fld];
                        $fld = "dob";
                        $memberDetails["Student"][$fld] = $v[$fld];
                        $fld = "mobile";
                        $memberDetails["Student"][$fld] = '';
                        $fld = "janaadhaarId";
                        $memberDetails["Application"]['jan_aadhar_number'] = $v[$fld];
                        $fld = "aadhar";
                        $memberDetails["Application"][$fld] = $v[$fld];
                        $fld = "gender";
                        if ($v[$fld] == 'Male') {
                            $memberDetails["Student"]['gender_id'] = 1;
                        }
                        if ($v[$fld] == 'Female') {
                            $memberDetails["Student"]['gender_id'] = 2;
                        }
                        $fld = "age";
                        $memberDetails["Application"][$fld] = $v[$fld];
                        break;
                    }
                }
            }
        }
        return $memberDetails;
    }


    public function studentrejectdelete(Request $request, $student_id)
    {

        $combo_name = 'student_delete_reason';
        $student_delete_reasons = $this->master_details($combo_name);

        if (count($request->all()) > 0) {
            $modelObj = new Student;
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
                $Student = Student::where('id', $student_ids)->update($custom_data);
                $Studentdetele = StudentAllotment::where('student_id', '=', $student_ids)->delete();
            } elseif ($request->deleted_reason == 2) {
                $Student = Student::where('id', $student_ids)->update($custom_data);
                $Studentdetele = StudentAllotment::where('student_id', $student_ids)->update($custom_dataupdate);

            } else {
                $Student = Student::where('id', $student_ids)->update($custom_data);
            }

            if ($Student) {
                return redirect()->route('students.index')->with('message', 'Student Reject successfully saved');
            } else {
                return redirect()->back()->with('error', 'Failed! Student not created');
            }
        }

        return view('student.studentrejectdelete', compact('student_delete_reasons', 'student_id'));

    }


    public function SearchStudentDetail(Request $request)
    {
        $page_title = "Search Enrollment";
        if ($request->isMethod('PUT')) {
            $enrollment = $request->enrollment;

            $marksheets_obj = new MarksheetCustomComponent;
            $studentdata = $marksheets_obj->getstudentdata($enrollment);
            if (empty($studentdata)) {
                return back()->with('error', 'Enrollment not found');
            } else {
                $id = $studentdata->id;
                return redirect()->route('printupdatestudentdetalis', Crypt::encrypt($id))->with('message', 'Enrollment Found');
            }
        }
        return view('student.SearchStudentDetail', compact('page_title'));
    }


    public function UpdateStudentDetail($student_id = null, Request $request)
    {
        $table = $model = "Student";
        $page_title = 'Update Basic Details';
        $tablename = 'students';
        $tablename2 = 'applications';
        $form_type = 'updatestudentprintdetails';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $aoRole = config::get('global.academicofficer_id');
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $combo_name = 'midium';
        $medium = $this->master_details($combo_name);
        $role_id = Session::get('role_id');
        $obj_controller = new Controller();
        $studentdata = Student::findOrFail($student_id);
		$last_action_name = 'searchstudentdetail';
		if($role_id == $aoRole){
			if(@$studentdata->ao_status ==  9){
				$last_action_name = 'ao_rejected_verify_documents';
			}else{
				$last_action_name = 'ao_verify_documents';
			}
		}
		
        $state_list = $this->state_details();
        $rsos_years = $this->rsos_years();
        $marksheets_obj = new MarksheetCustomComponent;
        $pre_qualifi = $marksheets_obj->previousqualificationget($studentdata->course, $studentdata->stream, $studentdata->adm_type);
        $getBoardList = $marksheets_obj->previousboardget($studentdata->adm_type);
        $applicationdata = Application::where('student_id', $student_id)->first();
        $model = "Student";
        if (empty($studentdata)) {
            return redirect()->route('searchstudentdetail')->with('error', 'Failed! Student data not found.');
        }
        $course = @$studentdata->course;
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1] . $student_id;
        $imageInput = array(
            "photograph" => "फोटोग्राफ(Photograph)",
            "signature" => "(हस्ताक्षर)Signature",
        );
        $master = Document::where('student_id', $student_id)->first();
        if ($request->isMethod('put')) {
            $document = new Document;
            if (isset($request->document_type)) {
                if ($request->document_type == 'i') {
                    $validate = Validator::make($request->all(), [
                        $request->document_input => 'required|mimes:jpg,png,jpeg,gif,svg|between:10,50',
                    ]);

                }
                if ($validate->fails()) {
                    return back()->withErrors($validate->errors())->withInput();
                }


                $input = $request->all();
                $fid = $input['document_input'];
                $filepath = public_path($studentDocumentPath . '/' . $master->$fid);
                //@dd($filepath);
                if (File::exists($filepath)) {
                    File::delete($filepath);
                }
                $inputName = $input['document_input'];
                $input[$input['document_input']] = $inputName . '.' . $request->$inputName->extension();
                $request->$inputName->move(public_path($studentDocumentPath), $input[$inputName]);

                $table_primary_id = @$master->id;
                $table_name = 'documents';
                $form_type = 'Admission';
                $controller_obj = new Controller;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
                // Student Documents Data Updation Log Enteries

                $document = Document::updateOrCreate(['student_id' => $student_id,], $input);
                $custom_component_obj = new CustomComponent;
                $isStudentLoigin = $custom_component_obj->_udpateLastAcotionPermedBy($student_id);
                if ($document) {
                    return redirect()->route('printupdatestudentdetalis', $estudent_id)->with('message', 'Document has been successfully submitted.');
                } else {
                    return redirect()->back()->with('error', 'Document not submitted.');
                }
            }


            $modelObj = new Student;
            $validator = Validator::make($request->all(), $modelObj->updatedetailsprintrules, $modelObj->updatedetailsprintmessage);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }


            $dobArr = explode("/", $request->dob);
            if (!empty($dobArr) && isset($dobArr['1'])) {
                if (isset($dobArr[0]) && isset($dobArr[1]) && isset($dobArr[2])) {
                    $dobs = $dobArr[2] . "-" . $dobArr[1] . "-" . $dobArr[0];
                }
            } else {
                $dobs = date("Y-m-d", strtotime($request->dob));
            }

            $combo_name = 'min_dob_date';
            $min_dob_date = $this->master_details($combo_name);
            $dobs = date('Y-m-d', strtotime($dobs));
            if ($dobs == "1970-01-01") {
                $dobArr = explode("/", $dobs);
                if (isset($dobArr[0]) && isset($dobArr[1]) && isset($dobArr[2])) {
                    $dobs = $dobArr[2] . "-" . $dobArr[1] . "-" . $dobArr[0] . "";
                }
            }

            if ($dobs > $min_dob_date[$course]) {
                return redirect()->back()->with('error', 'Student DOB should not be lesser than ' . date('d-m-Y', strtotime($min_dob_date[$course])) . ' not allowed.')->withInput($request->all());
            }


            $svData = ['name' => $request->name, 'father_name' => $request->father_name, 'mother_name' => $request->mother_name, 'mobile' => $request->mobile,
                'dob' => $dobs];
            $svdata2 = ['pre_qualification' => $request->pre_qualification, 'year_pass' => $request->year_pass, 'board' => $request->board, 'medium' => $request->medium];
            $exam_subject_log = $obj_controller->updateStudentLog($tablename, $student_id, $form_type);
            $exam_subject_log2 = $obj_controller->updateStudentLog($tablename2, @$applicationdata->id, $form_type);
            $mmr_data = $this->updateFlagInMarkMigrationRequest('correction_update', $student_id, '1');
            $studentupdate = Student::where('id', '=', $student_id)->update($svData);
            $studentupdate = Application::where('id', '=', $applicationdata->id)->update($svdata2);
            if ($studentupdate) {

                $Fresh_Student_Verificaiton_conditions = false;
                $super_admin_id = Config::get("global.super_admin_id");
                $academicofficer_id = Config::get("global.academicofficer_id");
                if ($role_id == $super_admin_id || $role_id == $academicofficer_id) {
                    $Fresh_Student_Verificaiton_conditions = true;
                }
                if ($Fresh_Student_Verificaiton_conditions) {
                    return redirect()->back()->with('message', 'Student details has been updated successfully.');
                }
                if ($mmr_data == true) {
                    return redirect()->back()->with('message', 'Student details has been updated successfully.');
                }
                return redirect()->route('searchstudentdetail')->with('message', 'Student details has been updated successfully.');
            }
        }
        $Fresh_Student_Verificaiton_conditions = Session::get('Fresh_Student_Verificaiton_conditions');
       
		return view('student.updatestudentdetalis', compact('last_action_name','page_title', 'Fresh_Student_Verificaiton_conditions', 'rsos_years', 'applicationdata', 'getBoardList', 'medium', 'pre_qualifi', 'course',
            'student_id', 'model', 'studentdata', 'estudent_id', 'studentDocumentPath', 'imageInput', 'master', 'role_id', 'aoRole'));
    }

    public function studentUpdateEligible($enrollment = null, $mark = null)
    {
        $enrollment = Crypt::decrypt($enrollment);
        $svdata = ['is_eligible' => $mark];
        if (@$mark) {
            $updatedata = Student::where('enrollment', '=', $enrollment)->update($svdata);
            if (@$updatedata) {
                return back()->with('message', 'Student Mark Eligable successfully.');
            } else {
                return back()->with('error', 'Student Not Mark Eligable .');
            }
        } else {
            return back()->with('error', 'Student Not Mark Eligable .');
        }

    }

    public function dev_toc_subject_details(Request $request, $student_id)
    {
        $table = $model = "Toc";
        $page_title = $model . ' Details';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $aoRole = config::get('global.academicofficer_id');
        $role_id = Session::get('role_id');
        $studentdata = Student::findOrFail($student_id);
        $customerrors = array();
        $response = array();

        $isPartAdmissionStudent = $this->_isPartAdmissionStudent($student_id);

        $studentdata = Student::findOrFail($student_id);
        if (empty($studentdata)) {
            return redirect()->route('registrations')->with('error', 'Failed! registration not saved');
        }
        $subject_list = $this->subjectList($studentdata->course);

        // echo $student_id; die;
        $master = Toc::where('student_id', $student_id)->first();
        // dd($master);

        $toc_marks_master = TocMark::where('student_id', $student_id)->get();
        //dd($toc_marks_master);

        $application_master = Application::where('student_id', $student_id)->first();
        // dd($application_master);

        // $show_paas_field =$this->showPassFailFieldToc($studentdata->adm_type,$studentdata->stream);
        $show_paas_field = 0;

        $isItiStudent = $this->_isItiStudent($student_id);
        if ($isItiStudent == 1) {
            // removed toc data if we have in db for ITI admission case because student not allowed for TOC
            // Student TOC TAble Data Updation Log Enteries
            $table_name = 'toc';
            $form_type = 'Admission';
            $table_primary_id = @$master->id;
            $controller_obj = new Controller;
            $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            // Student TOC TAble Data Updation Log Enteries

            $old_toc_data_iti_student = Toc::where("student_id", "=", $student_id);
            $old_toc_data_iti_student->delete();


            // Student TOC Marks Data Updation Log Enteries
            $table_name = 'toc_marks';
            $form_type = 'Admission';
            $controller_obj = new Controller;
            foreach (@$toc_marks_master as $tocMarksMasterKey) {
                $table_primary_id = @$tocMarksMasterKey->id;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            }
            // Student TOC Marks Data Updation Log Enteries

            $old_toc_mark_data_iti_student = TocMark::where("student_id", "=", $student_id);
            $old_toc_mark_data_iti_student->delete();

            $toc_status_change_iti_studenmt = Application::where("student_id", "=", $student_id)->update(['toc' => '0', 'is_toc_marked' => '0']);
            // removed toc data if we have in db for ITI admission case because student not allowed for TOC

            return redirect()->route('exam_subject_details', Crypt::encrypt($student_id))->with('message', 'You are not allowed for TOC section.');
        }

        if (count($request->all()) > 0 && $request->is_toc == 1) {
            $modelObj = new Toc;

            if (empty($request->is_toc)) {
                return redirect()->route('dev_toc_subject_details', Crypt::encrypt($student_id))->with('error', 'Please select toc');
            }

            $toc_submit_subject = 0;
            if (!empty($request->toc_subject)) {
                foreach ($request->toc_subject as $key => $each) {
                    if (!empty($each['subject_id'])) {
                        $toc_submit_subject++;
                    }
                }
            }

            $response = $this->tocValidations($request, $request->board, $studentdata->adm_type, $toc_submit_subject);
            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];
            if (true) {
                //valid case true save

                $isChange = $this->isChangeInFormData($model, $request->toc_subject, $toc_marks_master);
                if ($isChange) {
                    $deleteStatus = $this->deleteDataStudentId($student_id, $model);
                }

                // Student TOC TAble Data Updation Log Enteries
                $table_name = 'toc';
                $form_type = 'Admission';
                $table_primary_id = @$master->id;
                $controller_obj = new Controller;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
                // Student TOC TAble Data Updation Log Enteries

                $custom_data = array(
                    'student_id' => $student_id,
                    'year_fail' => strip_tags($request->year_fail),
                    'course' => strip_tags($studentdata->course),
                    'stream' => strip_tags($studentdata->stream),
                    'exam_year' => strip_tags($studentdata->exam_year),
                    'exam_month' => strip_tags($studentdata->exam_month),
                    'board' => strip_tags($request->board),
                    'roll_no' => strip_tags($request->roll_no),
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                );


                $old_toc_data = Toc::where("student_id", "=", $student_id);
                $old_toc_data->delete();
                $toc_id = Toc::insertGetId($custom_data);

                $toc_custom_data = array();
                foreach ($request->toc_subject as $key => $each) {
                    if (isset($each['subject_id']) && !empty($each['subject_id'])) {
                        $toc_custom_data[$key]['student_id'] = $student_id;
                        $toc_custom_data[$key]['toc_id'] = $toc_id;
                        $toc_custom_data[$key]['subject_id'] = $each['subject_id'];
                        $toc_custom_data[$key]['theory'] = $each['theory'];
                        $toc_custom_data[$key]['practical'] = $each['practical'];
                        $toc_custom_data[$key]['total_marks'] = $each['total'];

                        $toc_custom_data[$key]['conv_practical'] = $this->_getCONVTocMarkPractical($request->board, $each['subject_id'], $each['practical']);
                        $toc_custom_data[$key]['conv_theory'] = $this->_getCONVTocMarkTheory($request->board, $each['subject_id'], $each['theory'], $toc_custom_data[$key]['conv_practical'], $each['practical']);
                        $toc_custom_data[$key]['conv_total_marks'] = ($toc_custom_data[$key]['conv_theory'] + $toc_custom_data[$key]['conv_practical']);

                        $toc_custom_data[$key]['created_at'] = date("Y-m-d H:i:s");
                        $toc_custom_data[$key]['updated_at'] = date("Y-m-d H:i:s");
                    }
                }

                // Student TOC Marks Data Updation Log Enteries
                $table_name = 'toc_marks';
                $form_type = 'Admission';
                $controller_obj = new Controller;
                foreach (@$toc_marks_master as $tocMarksMasterKey) {
                    $table_primary_id = @$tocMarksMasterKey->id;
                    $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
                }
                // Student TOC Marks Data Updation Log Enteries

                $TocMarkmodelObj = new TocMark;
                $old_toc_marks_data = TocMark::where("student_id", "=", $student_id);
                $old_toc_marks_data->delete();
                $TocMarkmodelObj = TocMark::insert($toc_custom_data);


                $toc_status_change = Application::where("student_id", "=", $student_id)->update(['toc' => '1', 'is_toc_marked' => '1']);

                if ($modelObj && $TocMarkmodelObj) {
                    return redirect()->route('dev_toc_subject_details', Crypt::encrypt($student_id))->with('message', $model . ' successfully saved');
                } else {
                    return redirect()->route('exam_subject_details')->with('error', 'Failed! ' . $model . ' not saved');
                }
            } else {
                return redirect()->back()->withErrors($customerrors)->withInput($request->all());
            }
        } else if (count($request->all()) > 0 && $request->is_toc == 0 && $request->is_toc != null) {
            // Student TOC TAble Data Updation Log Enteries
            $table_name = 'toc';
            $form_type = 'Admission';
            $table_primary_id = @$master->id;
            $controller_obj = new Controller;
            $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            // Student TOC TAble Data Updation Log Enteries

            $old_toc_data = Toc::where("student_id", "=", $student_id);
            $old_toc_data->delete();

            // Student TOC Marks Data Updation Log Enteries
            $table_name = 'toc_marks';
            $form_type = 'Admission';
            $controller_obj = new Controller;
            foreach (@$toc_marks_master as $tocMarksMasterKey) {
                $table_primary_id = @$tocMarksMasterKey->id;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            }
            // Student TOC Marks Data Updation Log Enteries

            $old_toc_marks_data = TocMark::where("student_id", "=", $student_id);
            $old_toc_marks_data->delete();

            $toc_status_change = Application::where("student_id", "=", $student_id)->update(['toc' => '0', 'is_toc_marked' => '1']);
            return redirect()->route('dev_toc_subject_details', Crypt::encrypt($student_id))->with('message', $model . ' successfully saved');

        } else if (count($request->all()) > 0 && (!isset($request->is_toc) || $request->is_toc == null)) {

            // Student TOC Table Data Updation Log Enteries
            $table_name = 'toc';
            $form_type = 'Admission';
            $table_primary_id = @$master->id;
            $controller_obj = new Controller;
            $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            // Student TOC Table Data Updation Log Enteries

            $old_toc_data = Toc::where("student_id", "=", $student_id);
            $old_toc_data->delete();
            // Student TOC Marks Data Updation Log Enteries
            $table_name = 'toc_marks';
            $form_type = 'Admission';
            $controller_obj = new Controller;
            foreach (@$toc_marks_master as $tocMarksMasterKey) {
                $table_primary_id = @$tocMarksMasterKey->id;
                $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            }
            // Student TOC Marks Data Updation Log Enteries

            $old_toc_marks_data = TocMark::where("student_id", "=", $student_id);
            $old_toc_marks_data->delete();

            $custom_data = array(
                'student_id' => $student_id,
                'year_fail' => strip_tags($request->year_fail),
                'year_pass' => strip_tags($request->year_pass),
                'course' => strip_tags($studentdata->course),
                'stream' => strip_tags($studentdata->stream),
                'exam_year' => strip_tags($studentdata->exam_year),
                'exam_month' => strip_tags($studentdata->exam_month),
                'board' => strip_tags($request->board),
                'roll_no' => strip_tags($request->roll_no),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            );
            $toc_id = Toc::insertGetId($custom_data);

            $toc_status_change = Application::where("student_id", "=", $student_id)->update(['toc' => '0', 'is_toc_marked' => '1']);
            return redirect()->route('dev_toc_subject_details', Crypt::encrypt($student_id))->with('message', $model . ' successfully saved');
        }

        $routeUrl = "admission_subject_details";
        $previousTableName = "admission_subjects";
        $isValid = $this->getRecordExistorNot($student_id, $previousTableName);
        if (!$isValid) {
            return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! Please first fill the details!');
        }

        $toc_yes_no = $this->master_details('yesno');
        $student_subject_dropdown = $this->studentSubjectDropdown($student_id);
        // @dd($student_subject_dropdown);
        $student_subject_count = $this->studentSubjectCount($student_id);

        $rsos_years_dropdown = $this->getRsosYearsList();
        // $combo_name="year"; $rsos_years_dropdown = $this->master_details($combo_name);
        // @dd($rsos_years_dropdown);

        $rsos_years_fail_dropdown = $this->getRsosFailYearsList($request->board);
        // @dd($rsos_years_fail_dropdown);

        // $board_dropdown = $this->getBoardList();

        $isImprovementStudent = $this->_isImprovementStudent($student_id);

        $board_dropdown = $this->getAdmissionTypeBords($studentdata->adm_type);
        return view('student.dev_toc_subject_details', compact('isImprovementStudent', 'rsos_years_fail_dropdown', 'isPartAdmissionStudent', 'isItiStudent', 'toc_marks_master', 'application_master', 'studentdata', 'show_paas_field', 'customerrors', 'model', 'master', 'estudent_id', 'student_id', 'page_title', 'subject_list', 'toc_yes_no', 'student_subject_dropdown', 'student_subject_count', 'board_dropdown', 'rsos_years_dropdown', 'aoRole', 'role_id'));
    }

    public function update_basic_details(Request $request, $student_id)
    {
        $page_title = "Update Student Details";
        $student_id = Crypt::decrypt($student_id);
        $studentdata = Student::findOrFail($student_id);
        if (empty($studentdata)) {
            return redirect()->back()->with('error', 'Failed! registration not saved');
        }

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'ssoid' => 'required',
            ],
                [
                    'ssoid.required' => 'SSO is required.',
                ]);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $studentrole = config("global.student");
            $modeltype = 'App\Models\Student';
            $custom_component_obj = new CustomComponent;
            $table_name = 'students';
            $checkssoidallreadyaccessCount = $custom_component_obj->_checkssoidallreadyaccess($table_name, $request->student_id, $request->ssoid);
            if (@$checkssoidallreadyaccessCount > 0) {
                return redirect()->back()->with('error', 'SSOID already exist.');
            }
            $password = Hash::make('123456789');
            $data = ['ssoid' => $request->ssoid, 'password' => $password];
            $student_data = Student::where('id', $student_id)->update($data);
            $svdata = ['role_id' => $studentrole, 'model_type' => 'App\Models\Student', 'model_id' => $student_id];
            $model_has_roles = DB::table('model_has_roles')->where('role_id', $studentrole)->where('model_type', $modeltype)->where('model_id', $student_id)->first();

            if (!empty($model_has_roles)) {
                $model_has_roles = DB::table('model_has_roles')->where('role_id', $studentrole)->where('model_type', $modeltype)->where('model_id', $student_id)->update($svdata);
            } else {
                $model_has_roles = ModelHasRole::create($svdata);
            }

            if (@$student_data) {
                return redirect()->route('student_applications')->with('message', 'Student SSO has been updated successfully.');
            }
        }


        return view('student.updatestudentdetalis2', compact('page_title', 'student_id', 'studentdata'));

    }

    public function student_mark_reject(Request $request, $student_id)
    {
        $page_title = "Student Mark Reject";
        $student_id = Crypt::decrypt($student_id);
        $studentdata = Student::findOrFail($student_id);
        $user_id = Auth::user()->id;
        $rejectvalue = "Student mark reject as on " . " " . date("Y-m-d H:i:s");
        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'remarks' => 'required',
            ],
                [
                    'remarks.required' => 'Remarks is required.',
                ]);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $updateApplication = Application::where('student_id', $student_id)->first();
            $data = ['remarks' => $request->remarks, 'deleted_at' => date("Y-m-d H:i:s"), 'rejected_by_user_id' => $user_id];
            $student_data = Student::where('id', $student_id)->update($data);
            $fld = "id";
            $id = $student_id;
            if (@$updateApplication->$fld) {
                $fld = "jan_id";
                $updateApplication->$fld = $updateApplication->$fld . "_" . $id;
                $fld = "jan_aadhar_number";
                $updateApplication->$fld = $updateApplication->$fld . "_" . $id;
                $fld = "aadhar_number";
                $updateApplication->$fld = $updateApplication->$fld . "_" . $id;
                $updateApplication->save();
            }
            if (@$student_data) {
                return redirect()->route('student_applications')->with('message', 'Student successfully Reject.');
            }
        }
        return view('student.studentmarkreject', compact('page_title', 'student_id', 'studentdata', 'rejectvalue'));

    }

    public function resend_student_otp_personal(Request $request, $student_id = null)
    {
        $student_id = Crypt::decrypt($student_id);
        $checkstudentrecord = Student::find($student_id);
        if (@$checkstudentrecord->id) {
            $status = $this->_sendOTPToStudent($checkstudentrecord->id);
            return redirect()->back()->with("message", "OTP has been sent on registered mobile number.");
        } else {
            return redirect()->route('landing')->with("error", "Student not found!");
        }
    }

    public function verify_documents(Request $request, $student_id = null)
    {
        $loginuserid = Auth::user()->id;
        $table = $model = "DocumentVerification";
        $page_title = 'Student Document Verification';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $role_id = @Session::get('role_id');
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $studentdata = Student::findOrFail($student_id);
        $master_subject_details = $this->getStudentPdfDetails($student_id);
        $super_admin_id = Config::get('global.super_admin_id');
        $verifier_id = Config::get('global.verifier_id');
        $fldNameAliase = "verifier_";
        if ($role_id == Config::get('global.super_admin_id')) { //Acedmic Department
            $fldNameAliase = "department_";
        } else if ($role_id == Config::get('global.academicofficer_id')) { //Acedmic Department
            $fldNameAliase = "ao_";
        }
        $studentamount = 0;
        $fldNameAsperLogin = $fldNameAliase . 'status';

        if ($studentdata->$fldNameAsperLogin == 9) {
            return redirect()->route('rejected_verify_documents', Crypt::encrypt($student_id))->with('success', 'Clerification has been received.');
        }
        $custom_component_obj = new CustomComponent;
        //dd($student_id);
        $masterInputArr = $custom_component_obj->getStudentVerificaitonMainDocDetails($student_id);
        // echo "student controller -> 12333";dd($masterInputArr);
        // $combo_name = 'fresh_student_doc_update_status';$fresh_student_doc_update_status = $this->master_details($combo_name);
        //@dd($masterInputArr);
        if (@$masterInputArr) {
        } else {
            return redirect()->back()->with('error', 'Master details not found! Something is wrong.');
        }
        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);

        if ($isLockAndSubmit == 1) {
        } else {
            return redirect()->route('view_details', Crypt::encrypt($student_id))->with('error', 'Form not locked and submitted.');
        }

        $isAllowTofill = $this->getStudentVerificatonCount($student_id, 0, $verifier_id);
        if (@$isAllowTofill) {
        } else {
            return redirect()->back()->with('error', 'You are not allowed! Something is wrong.');
        }

        $isItiStudent = false;//$this->_isItiStudent($student_id);
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1] . $student_id;
        $studentDocumentPathTemp = "documents" . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR;
        $custom_component_obj = new CustomComponent;

        $dateIsOpen = false;
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();

        if ($isAdminStatus) {
            $dateIsOpen = true;
        } else {
            $dateIsOpen = $custom_component_obj->getDocumentVerificationAllowOrNot($role_id);
        }
        // echo "student controller -> 12333";dd($dateIsOpen);
        if (!$dateIsOpen) {
            return redirect()->route("student_applications")->with('error', 'Failed! Date  has been closed!');
        }
        //here check it's clerification document by student then go to rejected_verify_documents

        $master = Document::where('student_id', $student_id)->first();

        $combo_name = 'doc_verification_status';
        $doc_verification_status = $this->master_details($combo_name);
        if (@$doc_verification_status[1]) {
            unset($doc_verification_status[1]);
        }
        if (@$doc_verification_status[4]) {
            unset($doc_verification_status[4]);
        }
        // dd($doc_verification_status);
        $documentverifications = null;
        //$documentverifications = DocumentVerification::where('student_id',$student_id)->first();

        $documentInput = $this->getStudentDocumentListForVerification($student_id);
        $masterDetails = Student::with('application', 'document', 'address', 'admission_subject', 'toc_subject', 'exam_subject', 'StudentFee', 'tocdetils', 'bankdetils')->where('id', $student_id)->first();


        if (count($request->all()) > 0) {
            $inputs = $request->all();
            // dd($inputs);
            $finalInputs = array();
            // $is_ao_agree_with_verifier = 1;
            foreach ($masterInputArr as $k => $v) {
                if (isset($v['lower_level'])) {
                    // dd($v['lower_level']);
                    foreach ($v['lower_level'] as $lk => $lv) {
                        // dd($inputs['upper_level'][$k][$lv->field_id]);
                        if (isset($inputs['upper_level'][$k][$lv->field_id]) && $inputs['upper_level'][$k][$lv->field_id] == 'on') {
                            $finalInputs[@$lv->main_document_id][@$lv->field_id] = 1;
                        } else {
                            // $is_ao_agree_with_verifier = 2;
                            $finalInputs[@$lv->main_document_id][@$lv->field_id] = 2;
                        }
                    }
                }
            }
            $upperLevelFinalInputs = array();
            // dd($finalInputs);
            foreach ($finalInputs as $k => $v) {
                $upperLevelFinalInputs[$k] = 1;
                foreach ($v as $lk => $lv) {
                    // dd($lv);
                    if ($lv == 2) {
                        $upperLevelFinalInputs[$k] = 2;
                        $inputs['isAllRejected'] = 1;
                        break;
                    }
                }
            }
            // dd($upperLevelFinalInputs);
            // echo "<pre>";
            // print_r(json_encode($finalInputs));
            // dd($finalInputs);


            $inputs['upper_level'] = $finalInputs;
            $inputs['only_upper_level'] = $upperLevelFinalInputs;

            $tempInputs = $inputs;
            unset($tempInputs['ajaxRequest']);
            unset($tempInputs['_method']);
            unset($tempInputs['_token']);
            unset($tempInputs['action']);

            // echo "<pre>";
            // print_r($finalInputs);
            // dd($tempInputs);


            $responseFinal = null;
            $responses = true;
            /* Start */
            $docverificationstatus = null;
            $studentdocverificationstatus = null;
            if ($responses == true) {
                $role_id = @Session::get('role_id');
                $fldNameAliase = null;

                $finalStatus = 7;
                $isVerified = 1;
                $is_permanent_rejected_by_dept = 0;

                $super_admin_id = Config::get("global.super_admin_id");

                if (@$inputs['isAllRejected'] == 1) {//i.e. finally rejected
                    $finalStatus = 8;
                    $isVerified = 2;
                }
                if ($role_id == Config::get('global.verifier_id')) {
                    $fldNameAliase = "verifier_";
                    $studentamount = 0;
                }
                $docverificationstatus = [
                    $fldNameAliase . 'verify_user_id' => $loginuserid,
                    'role_id' => $role_id,
                    $fldNameAliase . 'status' => $finalStatus,
                    'student_id' => $student_id,
                    // 'is_ao_agree_with_verifier' => $is_ao_agree_with_verifier,
                    'verifier_upper_documents_verification' => json_encode(@$inputs['only_upper_level']),
                    'verifier_documents_verification' => json_encode(@$inputs['upper_level']),
                    $fldNameAliase . 'verify_datetime' => date('Y-m-d H:i:s'),
                    'amount' => @$studentamount,
                ];
                $studentdocverificationstatus = [
                    $fldNameAliase . 'verify_user_id' => $loginuserid,
                    'is_doc_rejected' => null,
                    'is_' . $fldNameAliase . 'verify' => $isVerified,
                    $fldNameAliase . 'verify_datetime' => date('Y-m-d H:i:s'),
                    $fldNameAliase . 'status' => $finalStatus,
                    'ao_status' => 1,
                ];
                // $docverificationstatus= array_merge($docverificationstatus,$inputs);
            }

            $DocumentVerification = DocumentVerification::create($docverificationstatus);

            $StudentDocumentVerification = DB::table('students')->where('id', $student_id)->update($studentdocverificationstatus);

            $academicofficer_id = Config::get('global.academicofficer_id');
            if ($role_id == Config::get('global.super_admin_id') || $role_id == $academicofficer_id) {
                if (@$inputs['isAllRejected'] == 1) {
                } else {
                    $enrollment = $this->_setEnrollmentAndIsEligiable($student_id);
                }
            }
            /* End */

            if ($docverificationstatus) {
                $exam_month = @$studentdata->exam_month;
                $fldNameAliase = "verifier_status";
                if ($role_id == Config::get('global.super_admin_id')) { //Acedmic Department
                    $fldNameAliase = "department_status";
                }
                return redirect()->route("verifying_student_applications", ['exam_month' => $exam_month, $fldNameAliase => 1])->with('message', 'Document vertification has been successfully submitted.');
            } else {
                return redirect()->route('verify_documents', Crypt::encrypt($student_id))->with('error', 'Failed! Document vertification details has been not submitted');
            }
        }

        $rejectionType = "firsttime";
        $documentVerifierVerifications = array();

        $lastEnteredBy = DocumentVerification::where('student_id', $student_id)
            ->orderby("id", "DESC")->first(['role_id']);
        if (isset($lastEnteredBy->role_id) && $lastEnteredBy->role_id == $role_id) {
            return redirect()->back()->with('error', 'Your verification is already submitted! Something is wrong.');
        }

        if ($role_id == Config::get('global.super_admin_id')) {
            $documentVerifierVerifications = DocumentVerification::where('student_id', $student_id)
                ->where('role_id', $verifier_id)
                ->orderby("id", "DESC")->first();
        }
        // dd($masterInputArr);

        return view('student.verify_documents', compact('masterInputArr', 'role_id', 'rejectionType', 'documentVerifierVerifications', 'master_subject_details', 'stream_id', 'masterDetails', 'doc_verification_status', 'documentInput', 'studentDocumentPath', 'page_title', 'estudent_id', 'studentdata', 'model', 'student_id', 'master', 'studentDocumentPathTemp'));
    }

    public function getStudentVerificatonCount($student_id = null, $needCount = null, $forWhichRoleId = null)
    {
        $role_id = @Session::get('role_id');

        if ($forWhichRoleId == $role_id) {
            $count = DocumentVerification::where('student_id', $student_id)->where('role_id', $role_id)->count();
            if ($needCount == $count) {
                return true;
            }
        }
        return false;
    }

    public function ao_verify_documents(Request $request, $student_id = null)
    {	 
        $loginuserid = Auth::user()->id;

        $table = $model = "DocumentVerification";
        $page_title = 'Student Document Verification';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $role_id = @Session::get('role_id');
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'verifier_status_label';
        $verifier_status_label = $this->master_details($combo_name);
        $studentdata = Student::findOrFail($student_id);
        $master_subject_details = $this->getStudentPdfDetails($student_id);

        $verifier_id = Config::get('global.verifier_id');
        $academicofficer_id = Config::get('global.academicofficer_id');

        $isAllowTofill = $this->getStudentVerificatonCount($student_id, 0, $academicofficer_id);

        if (@$isAllowTofill) {
        } else {
            return redirect()->back()->with('error', 'You are not allowed! Something is wrong.');
        }

        $super_admin_id = Config::get('global.super_admin_id');

        $fldNameAliase = "verifier_";
        if ($role_id == Config::get('global.super_admin_id')) { //Acedmic Department
            $fldNameAliase = "department_";
        } else if ($role_id == Config::get('global.academicofficer_id')) { //Acedmic Department
            $fldNameAliase = "ao_";
        }
        $studentamount = 0;
        $fldNameAsperLogin = $fldNameAliase . 'status';

        if ($studentdata->$fldNameAsperLogin == 9) {
            return redirect()->route('rejected_verify_documents', Crypt::encrypt($student_id))->with('success', 'Clerification has been received.');
        }
        $custom_component_obj = new CustomComponent;
        // dd($student_id);
        $lastEnteredBy = DocumentVerification::where('student_id', $student_id)
            ->orderby("id", "DESC")->first();
        // dd($lastEnteredBy);
        if (isset($lastEnteredBy->role_id) && $lastEnteredBy->role_id == $role_id) {
            return redirect()->back()->with('error', 'Your verification is already submitted! Something is wrong.');
        }


        $masterInputArr = $custom_component_obj->getStudentVerificaitonMainDocDetails($student_id);
        // echo "student controller -> 12333";dd($masterInputArr);
        // $combo_name = 'fresh_student_doc_update_status';$fresh_student_doc_update_status = $this->master_details($combo_name);

        if (@$masterInputArr) {
        } else {
            return redirect()->back()->with('error', 'Master details not found! Something is wrong.');
        }
        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);

        if ($isLockAndSubmit == 1) {
        } else {
            return redirect()->route('view_details', Crypt::encrypt($student_id))->with('error', 'Form not locked and submitted.');
        }
        $isItiStudent = false;//$this->_isItiStudent($student_id);
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1] . $student_id;
        $studentDocumentPathTemp = "documents" . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR;

        $custom_component_obj = new CustomComponent;

        $dateIsOpen = false;
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();

        if ($isAdminStatus) {
            $dateIsOpen = true;
        } else {
            $dateIsOpen = $custom_component_obj->getDocumentVerificationAllowOrNot($role_id);
        }
        // echo "student controller -> 12333";dd($dateIsOpen);
        if (!$dateIsOpen) {
            return redirect()->route("student_applications")->with('error', 'Failed! Date  has been closed!');
        }
        //here check it's clerification document by student then go to rejected_verify_documents

        $master = Document::where('student_id', $student_id)->first();
        // dd($master);
        $combo_name = 'doc_verification_status';
        $doc_verification_status = $this->master_details($combo_name);
        if (@$doc_verification_status[1]) {
            unset($doc_verification_status[1]);
        }
        if (@$doc_verification_status[4]) {
            unset($doc_verification_status[4]);
        }
        // dd($doc_verification_status);
        $documentverifications = null;
        //$documentverifications = DocumentVerification::where('student_id',$student_id)->first();

        $documentInput = $this->getStudentDocumentListForVerification($student_id);
        $masterDetails = Student::with('application', 'document', 'address', 'admission_subject', 'toc_subject', 'exam_subject', 'StudentFee', 'tocdetils', 'bankdetils')->where('id', $student_id)->first();


        $role_id = @Session::get('role_id');
        $verifier_id = Config::get('global.verifier_id');
        $super_admin_id = Config::get('global.super_admin_id');
        $academicofficer_id = Config::get('global.academicofficer_id');

        $verifier_documents_verification = array();
        $isCurrentacademicofficer_id = false;
        if ($role_id == $academicofficer_id) {
            $isCurrentacademicofficer_id = true;
            if (@$lastEnteredBy->verifier_upper_documents_verification) {
                $verifier_upper_documents_verification = json_decode(@$lastEnteredBy->verifier_upper_documents_verification, true);
            }
            if (@$lastEnteredBy->verifier_documents_verification) {
                $verifier_documents_verification = json_decode(@$lastEnteredBy->verifier_documents_verification, true);
            }
        }

        if (count($request->all()) > 0) {
            $inputs = $request->all();
            $inputs['isAllRejected'] = null;
            // dd($verifier_documents_verification);
            $is_ao_agree_with_verifier = 1;
            $finalInputs = array();
            /* Old */
            /* foreach($verifier_documents_verification as $k => $v){
					foreach($v as $lk => $lv){
						$finalInputs[$k][$lk] = @$inputs['upper_level'][$k][$lk];
						if(@$inputs['upper_level'][$k][$lk]){
							if( @$inputs['upper_level'][$k][$lk] == "on"){
								$finalInputs[$k][$lk] =  $lv;
							}else{
								if($inputs['upper_level'][$k][$lk] == 1){
									$finalInputs[$k][$lk] =  2;
								}else{
									$finalInputs[$k][$lk] =  1;
								}
							}
						}else{
							$is_ao_agree_with_verifier = 2;
							if($lv == 1){
								$finalInputs[$k][$lk] =  2;
							}else{
								$finalInputs[$k][$lk] =  1;
							}
						}
					}
				} */
            /* Old */
            /* New */
            foreach ($masterInputArr as $k => $v) {
                if (isset($v['lower_level'])) {
                    // dd($v['lower_level']);
                    foreach ($v['lower_level'] as $lk => $lv) {
                        // dd($inputs['upper_level'][$k][$lv->field_id]);
                        if (isset($inputs['upper_level'][$k][$lv->field_id]) && $inputs['upper_level'][$k][$lv->field_id] == 'on') {
                            $finalInputs[@$lv->main_document_id][@$lv->field_id] = 1;
                        } else {
                            $is_ao_agree_with_verifier = 2;
                            $finalInputs[@$lv->main_document_id][@$lv->field_id] = 2;
                        }
                    }
                }
            }
            /* New */
            $upperLevelFinalInputs = array();
            foreach ($finalInputs as $k => $v) {
                $upperLevelFinalInputs[$k] = 1;
                foreach ($v as $lk => $lv) {
                    // dd($lv);
                    if ($lv == 2) {
                        $inputs['isAllRejected'] = 1;
                        $upperLevelFinalInputs[$k] = 2;
                        break;
                    }
                }
            }

            // echo "<pre>";
            // print_r($verifier_documents_verification);
            // print_r($upperLevelFinalInputs);
            // dd($finalInputs);


            // dd($upperLevelFinalInputs);
            // echo "<pre>";
            // print_r(json_encode($finalInputs));
            // dd($finalInputs);


            $inputs['upper_level'] = $finalInputs;
            $inputs['only_upper_level'] = $upperLevelFinalInputs;

            $tempInputs = $inputs;
            unset($tempInputs['ajaxRequest']);
            unset($tempInputs['_method']);
            unset($tempInputs['_token']);
            unset($tempInputs['action']);

            // echo "<pre>";
            // print_r($finalInputs);
            // dd($tempInputs);


            $responseFinal = null;
            $responses = true;
            /* Start */
            $docverificationstatus = null;
            $studentdocverificationstatus = null;
            if ($responses == true) {
                $role_id = @Session::get('role_id');
                $fldNameAliase = null;

                $finalStatus = 2;
                $isVerified = 1;
                $stage = null;
                $is_doc_rejected = null;
                $is_permanent_rejected_by_dept = 0;

                $super_admin_id = Config::get("global.super_admin_id");
                $academicofficer_id == Config::get('global.academicofficer_id');

                if (@$inputs['isAllRejected'] == 1) {//i.e. finally rejected
                    $finalStatus = 3;
                    $isVerified = 2;
                    $is_doc_rejected = 1;
                    $stage = 3;
                }
                if ($role_id == Config::get('global.academicofficer_id')) {
                    $fldNameAliase = "ao_";
                    $studentamount = 0;
                }
                $docverificationstatus = [
                    $fldNameAliase . 'verify_user_id' => $loginuserid,
                    'role_id' => $role_id,
                    $fldNameAliase . 'status' => $finalStatus,
                    'student_id' => $student_id,
                    $fldNameAliase . 'upper_documents_verification' => json_encode(@$inputs['only_upper_level']),
                    $fldNameAliase . 'documents_verification' => json_encode(@$inputs['upper_level']),
                    $fldNameAliase . 'verify_datetime' => date('Y-m-d H:i:s'),
                    'is_ao_agree_with_verifier' => $is_ao_agree_with_verifier,
                    'amount' => @$studentamount,
                ];

                $studentdocverificationstatus = [
                    $fldNameAliase . 'verify_user_id' => $loginuserid,
                    'is_doc_rejected' => $is_doc_rejected,
                    'stage' => $stage,
                    $fldNameAliase . 'verify_datetime' => date('Y-m-d H:i:s'),
                    'is_' . $fldNameAliase . 'verify' => $isVerified,
                    $fldNameAliase . 'status' => $finalStatus,
                ];
                // echo "<pre>";print_r($studentdocverificationstatus);
                // dd($docverificationstatus);
            }

            $DocumentVerification = DocumentVerification::create($docverificationstatus);
            $StudentDocumentVerification = DB::table('students')->where('id', $student_id)->update($studentdocverificationstatus);

            $academicofficer_id = Config::get('global.academicofficer_id');
            if ($role_id == Config::get('global.super_admin_id') || $role_id == $academicofficer_id) {
                if (@$inputs['isAllRejected'] == 1) {
                } else {
                    $enrollment = $this->_setEnrollmentAndIsEligiable($student_id);
                }
                /* sms send start */
                $detailsHindi = $details = null;
                if (@$inputs['isAllRejected'] == 1) {
                    $details = $this->getVerifcaionSMSSend('deficient', 'eng');
                    $detailsHindi = $this->getVerifcaionSMSSend('deficient', 'hindi');
                } else {
                    $details = $this->getVerifcaionSMSSend('approved', 'eng');
                    $detailsHindi = $this->getVerifcaionSMSSend('approved', 'hindi');
                }
                $detailsHindi['mobile'] = $details['mobile'] = @$masterDetails->mobile;
                if ($details['mobile'] != null && $details['sms'] != null && $details['templateID'] != "") {
                    $smsStatus = $this->_sendSMS($details['mobile'], $details['sms'], $details['templateID']);
                }
                if ($detailsHindi['mobile'] != null && $detailsHindi['sms'] != null && $detailsHindi['templateID'] != "") {
                    $smsStatus = $this->_sendSMS($detailsHindi['mobile'], $detailsHindi['sms'], $detailsHindi['templateID']);
                }
                /* sms send end  */
            }
            /* End */

            if ($docverificationstatus) {
                $exam_month = @$studentdata->exam_month;
                $fldNameAliase = "verifier_status";
                if ($role_id == Config::get('global.super_admin_id')) { //Acedmic Department
                    $fldNameAliase = "department_status";
                } else if ($role_id == Config::get('global.academicofficer_id')) {
                    $fldNameAliase = "ao_status";
                }

                /* Redirect last pasge star */
                $temp_url_for_back = Session::get('temp_url_for_back');
                if (@$temp_url_for_back) {
                    $protocol = "http://";
                    if (@$_SERVER['REQUEST_SCHEME']) {
                        $protocol = $_SERVER['REQUEST_SCHEME'] . "://";
                    }
                    $temp_url_for_back = $protocol . $_SERVER['HTTP_HOST'] . $temp_url_for_back;
                    return Redirect::to($temp_url_for_back);
                }
                return redirect()->route("verifying_student_applications", ['exam_month' => $exam_month, $fldNameAliase => 1])->with('message', 'Document vertification has been successfully submitted.');
                /* Redirect last pasge end */

            } else {
                return redirect()->route('verify_documents', Crypt::encrypt($student_id))->with('error', 'Failed! Document vertification details has been not submitted');
            }
        }

        $rejectionType = "firsttime";
        $documentVerifierVerifications = array();


        if (@$lastEnteredBy->verifier_documents_verification && !empty($lastEnteredBy->verifier_documents_verification)) {
        } else {
            return redirect()->back()->with('error', 'Verifier verification details not found! Something is wrong.');
        }
        if ($role_id == Config::get('global.super_admin_id')) {
            $documentVerifierVerifications = DocumentVerification::where('student_id', $student_id)
                ->where('role_id', $verifier_id)
                ->orderby("id", "DESC")->first();
        }
        // dd($masterDetails);
        return view('student.ao_verify_documents', compact('isCurrentacademicofficer_id', 'verifier_documents_verification', 'verifier_upper_documents_verification', 'lastEnteredBy', 'masterInputArr', 'role_id', 'rejectionType', 'documentVerifierVerifications', 'master_subject_details', 'verifier_status_label', 'stream_id', 'masterDetails', 'doc_verification_status', 'documentInput', 'studentDocumentPath', 'page_title', 'estudent_id', 'studentdata', 'model', 'student_id', 'master', 'studentDocumentPathTemp'));
    }

    public function verification_trail(Request $request, $student_id = null)
    {
        $loginuserid = Auth::user()->id;
        $table = $model = "DocumentVerification";
        $page_title = 'Student Verification Trailhead';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $role_id = @Session::get('role_id');
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'ao_dept_status_label';
        $ao_dept_status_label = $this->master_details($combo_name);
        $studentdata = Student::findOrFail($student_id);
        // $master_subject_details = $this->getStudentPdfDetails($student_id);
        $super_admin_id = Config::get('global.super_admin_id');
        $verifier_id = Config::get('global.verifier_id');
        $fldNameAliase = "verifier_";
        if ($role_id == Config::get('global.super_admin_id')) { //Acedmic Department
            $fldNameAliase = "department_";
        } else if ($role_id == Config::get('global.academicofficer_id')) { //Acedmic Department
            $fldNameAliase = "ao_";
        }
        $studentamount = 0;
        $fldNameAsperLogin = $fldNameAliase . 'status';

        $masterDetails = Student::with('application')->where('id', $student_id)->first();
        $documentverifications = DocumentVerification::where('student_id', $student_id)->orderby('id', 'desc')->get();
        //dd($documentverifications);
        $roles = $this->_getRoles();
        $labels = $this->getVerificationDetailedLabels();
        $combo_name = 'verifier_status_label';
        $verifier_status_label = $this->master_details($combo_name);
        $combo_name = 'ao_dept_status_label';
        $ao_dept_status_label = $this->master_details($combo_name);

        if (count($request->all()) > 0) {
            $tempInputs = $inputs = $request->all();
            if ($role_id == $super_admin_id) {
                if (isset($tempInputs['main']) && count($tempInputs['main']) > 0) {
                    foreach (@$tempInputs['main'] as $k => $v) {
                        if ($k != "") {
                            $k = decrypt($k);
                        }
                        //also soft delete rs_student_document_verifications where student_verification_id is $k
                        $studentverificationsdatasoftdelete = DB::table('student_verifications')->where('id', $k)->update(['deleted_at' => date("Y-m-d H:i:s"),]);
                        $studentdocumentdatasoftdelete = DB::table('student_document_verifications')->where('student_verification_id', $k)->update(['deleted_at' => date("Y-m-d H:i:s"),]);
                    }
                }
                if ($studentverificationsdatasoftdelete && $studentdocumentdatasoftdelete) {
                    return redirect()->route('verification_trail', Crypt::encrypt($estudent_id))->with('message', 'Your selected trail has been removed successfully.');
                } else {
                    return redirect()->route('verification_trail')->with('error', 'Failed! Something is wrong.');
                }
            }
        }

        return view('student.verification_trail', compact('ao_dept_status_label', 'verifier_status_label', 'roles', 'labels', 'estudent_id', 'model', 'page_title', 'documentverifications', 'masterDetails', 'student_id'));

    }

    public function rejected_verify_documents(Request $request, $student_id = null)
    {
        $loginuserid = Auth::user()->id;
		
		
        $table = $model = "DocumentVerification";
        $page_title = 'Student Document Re-Verification';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $role_id = @Session::get('role_id');
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $studentdata = Student::findOrFail($student_id);
        $master_subject_details = $this->getStudentPdfDetails($student_id);

        $verifier_id = Config::get('global.verifier_id');
        $isAllowTofill = $this->getStudentVerificatonCount($student_id, 1, $verifier_id);
        if (@$isAllowTofill) {
        } else {
            return redirect()->back()->with('error', 'You are not allowed! Something is wrong.');
        }

        $super_admin_id = Config::get('global.super_admin_id');

        $fldNameAliase = "verifier_";
        if ($role_id == Config::get('global.super_admin_id')) { //Acedmic Department
            $fldNameAliase = "department_";
        } else if ($role_id == Config::get('global.academicofficer_id')) { //Acedmic Department
            $fldNameAliase = "ao_";
        }
        $studentamount = 0;
        $fldNameAsperLogin = $fldNameAliase . 'status';

        if ($studentdata->$fldNameAsperLogin == 9) {
        } else {
            return redirect()->back()->with('error', 'Something is wrong.');
        }
        $custom_component_obj = new CustomComponent;
        // dd($student_id);
        $isReverify = 1;
        $masterInputArr = $custom_component_obj->getStudentVerificaitonMainDocDetails($student_id, $isReverify);

        // echo "student controller -> 4354";dd($masterInputArr);
        // $combo_name = 'fresh_student_doc_update_status';$fresh_student_doc_update_status = $this->master_details($combo_name);

        if (@$masterInputArr) {
        } else {
			
            return redirect()->back()->with('error', 'Master details not found! Something is wrong.');
        }
        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);
//echo "student controller -> 4354";


        if ($isLockAndSubmit == 1) {
        } else {
            return redirect()->route('view_details', Crypt::encrypt($student_id))->with('error', 'Form not locked and submitted.');
        }
        $isItiStudent = false;//$this->_isItiStudent($student_id);
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1] . $student_id;

        $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
        $current_exam_month_id = Config::get("global.form_current_exam_month_id");

        $combo_name = 'student_verification_documents';
        $student_verification_documents = $this->master_details($combo_name);

        $studentDocumentVerificaitonData = StudentDocumentVerification::where('student_id', $student_id)->orderby("id", "desc")->first();
        $studentDocumentPath = $student_verification_documents[1] . $current_admission_session_id . DIRECTORY_SEPARATOR . $current_exam_month_id . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$studentDocumentVerificaitonData->id . DIRECTORY_SEPARATOR;
        $studentDocumentPathTemp = "documents" . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR;

        // dd($studentDocumentPath);

        $custom_component_obj = new CustomComponent;

        $dateIsOpen = false;
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();

        if ($isAdminStatus) {
            $dateIsOpen = true;
        } else {
            $dateIsOpen = $custom_component_obj->getDocumentVerificationAllowOrNot($role_id);
        }
        //echo "student controller -> 12333";dd($dateIsOpen);
        if (!$dateIsOpen) {
            return redirect()->route("student_applications")->with('error', 'Failed! Date  has been closed!');
        }
        //here check it's clerification document by student then go to rejected_verify_documents

        // $master = Document::where('student_id',$student_id)->first();


        $master = StudentDocumentVerification::where('student_id', $student_id)->orderby("id", "desc")->first();
		// dd($master);
        $combo_name = 'doc_verification_status';
        $doc_verification_status = $this->master_details($combo_name);
        if (@$doc_verification_status[1]) {
            unset($doc_verification_status[1]);
        }
        if (@$doc_verification_status[4]) {
            unset($doc_verification_status[4]);
        }
        // dd($doc_verification_status);
        $documentverifications = null;
        //$documentverifications = DocumentVerification::where('student_id',$student_id)->first();

        $documentInput = $this->getStudentDocumentListForVerification($student_id);
        $masterDetails = Student::with('application', 'document', 'address', 'admission_subject', 'toc_subject', 'exam_subject', 'StudentFee', 'tocdetils', 'bankdetils')->where('id', $student_id)->first();


        if (count($request->all()) > 0) {
            $inputs = $request->all();
            // dd($inputs);
            $finalInputs = array();
            foreach ($masterInputArr as $k => $v) {
                if (isset($v['lower_level'])) {
                    // dd($v['lower_level']);
                    foreach ($v['lower_level'] as $lk => $lv) {
                        // dd($inputs['upper_level'][$k][$lv->field_id]);
                        if (isset($inputs['upper_level'][$k][$lv->field_id]) && $inputs['upper_level'][$k][$lv->field_id] == 'on') {
                            $finalInputs[@$lv->main_document_id][@$lv->field_id] = 1;
                        } else {
                            $finalInputs[@$lv->main_document_id][@$lv->field_id] = 2;
                            $inputs['isAllRejected'] = 1;
                        }
                    }
                }
            }
            $upperLevelFinalInputs = array();
            // dd($finalInputs);
            foreach ($finalInputs as $k => $v) {
                $upperLevelFinalInputs[$k] = 1;
                foreach ($v as $lk => $lv) {
                    // dd($lv);
                    if ($lv == 2) {
                        $upperLevelFinalInputs[$k] = 2;
                        break;
                    }
                }
            }
            // dd($upperLevelFinalInputs);
            // echo "<pre>";
            // print_r(json_encode($finalInputs));
            // dd($finalInputs);


            $inputs['upper_level'] = $finalInputs;
            $inputs['only_upper_level'] = $upperLevelFinalInputs;

            $tempInputs = $inputs;
            unset($tempInputs['ajaxRequest']);
            unset($tempInputs['_method']);
            unset($tempInputs['_token']);
            unset($tempInputs['action']);

            // echo "<pre>";
            // print_r($finalInputs);
            // dd($tempInputs);


            $responseFinal = null;
            $responses = true;
            /* Start */
            $docverificationstatus = null;
            $studentdocverificationstatus = null;
            if ($responses == true) {
                $role_id = @Session::get('role_id');
                $fldNameAliase = null;

                $finalStatus = 7;
                $isVerified = 1;
                $is_permanent_rejected_by_dept = 0;

                $super_admin_id = Config::get("global.super_admin_id");

                if (@$inputs['isAllRejected'] == 1) {//i.e. finally rejected
                    $finalStatus = 8;
                    $isVerified = 2;
                }
                if ($role_id == Config::get('global.verifier_id')) {
                    $fldNameAliase = "verifier_";
                    $studentamount = 0;
                }
                $docverificationstatus = [
                    $fldNameAliase . 'verify_user_id' => $loginuserid,
                    'role_id' => $role_id,
                    $fldNameAliase . 'status' => $finalStatus,
                    'student_id' => $student_id,
                    'verifier_upper_documents_verification' => json_encode(@$inputs['only_upper_level']),
                    'verifier_documents_verification' => json_encode(@$inputs['upper_level']),
                    $fldNameAliase . 'verify_datetime' => date('Y-m-d H:i:s'),
                    'amount' => @$studentamount,
                ];
                $studentdocverificationstatus = [
                    $fldNameAliase . 'verify_user_id' => $loginuserid,
                    'is_doc_rejected' => null,
                    $fldNameAliase . 'verify_datetime' => date('Y-m-d H:i:s'),
                    'is_' . $fldNameAliase . 'verify' => $isVerified,
                    $fldNameAliase . 'status' => $finalStatus,
                    'ao_status' => 9,
                ];

                // $docverificationstatus= array_merge($docverificationstatus,$inputs);
            }

            $DocumentVerification = DocumentVerification::create($docverificationstatus);
            $StudentDocumentVerification = DB::table('students')->where('id', $student_id)->update($studentdocverificationstatus);

            $academicofficer_id = Config::get('global.academicofficer_id');
            if ($role_id == Config::get('global.super_admin_id') || $role_id == $academicofficer_id) {
                if (@$inputs['isAllRejected'] == 1) {
                } else {
                    $enrollment = $this->_setEnrollmentAndIsEligiable($student_id);
                }
            }
            /* End */

            if ($docverificationstatus) {
                $exam_month = @$studentdata->exam_month;
                $fldNameAliase = "verifier_status";
                if ($role_id == Config::get('global.super_admin_id')) { //Acedmic Department
                    $fldNameAliase = "department_status";
                }
                return redirect()->route("verifying_student_applications", ['exam_month' => $exam_month, $fldNameAliase => 1])->with('message', 'Document vertification has been successfully submitted.');
            } else {
                return redirect()->route('verify_documents', Crypt::encrypt($student_id))->with('error', 'Failed! Document vertification details has been not submitted');
            }
        }

        $rejectionType = "firsttime";
        $documentVerifierVerifications = array();
        $lastEnteredBy = DocumentVerification::where('student_id', $student_id)
            ->orderby("id", "DESC")->first(['role_id']);
        if (isset($lastEnteredBy->role_id) && $lastEnteredBy->role_id == $role_id) {
            return redirect()->back()->with('error', 'Your verification is already submitted! Something is wrong.');
        }

        if ($role_id == Config::get('global.super_admin_id')) {
            $documentVerifierVerifications = DocumentVerification::where('student_id', $student_id)
                ->where('role_id', $verifier_id)
                ->orderby("id", "DESC")->first();
        }
        return view('student.rejected_verify_documents', compact('masterInputArr', 'role_id', 'rejectionType', 'documentVerifierVerifications', 'master_subject_details', 'stream_id', 'masterDetails', 'doc_verification_status', 'documentInput', 'studentDocumentPath', 'page_title', 'estudent_id', 'studentdata', 'model', 'student_id', 'master', 'studentDocumentPathTemp'));
    }

    public function ao_rejected_verify_documents(Request $request, $student_id = null)
    {
		
		
        $loginuserid = Auth::user()->id;
        $table = $model = "DocumentVerification";
        $page_title = 'Student Document Re-Verification';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $role_id = @Session::get('role_id');
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'verifier_status_label';
        $verifier_status_label = $this->master_details($combo_name);
        $studentdata = Student::findOrFail($student_id);
        $master_subject_details = $this->getStudentPdfDetails($student_id);
        $super_admin_id = Config::get('global.super_admin_id');
        $verifier_id = Config::get('global.verifier_id');
        $academicofficer_id = Config::get('global.academicofficer_id');


        $isAllowTofill = $this->getStudentVerificatonCount($student_id, 1, $academicofficer_id);
        if (@$isAllowTofill) {
        } else {
            return redirect()->back()->with('error', 'You are not allowed! Something is wrong.');
        }
        $fldNameAliase = "verifier_";
        if ($role_id == Config::get('global.super_admin_id')) { //Acedmic Department
            $fldNameAliase = "department_";
        } else if ($role_id == Config::get('global.academicofficer_id')) { //Acedmic Department
            $fldNameAliase = "ao_";
        }
        $studentamount = 0;
        $fldNameAsperLogin = $fldNameAliase . 'status';

        $combo_name = 'student_verification_documents';
        $student_verification_documents = $this->master_details($combo_name);

        if ($studentdata->$fldNameAsperLogin == 9) {
        } else {
            return redirect()->back()->with('error', 'Something is wrong.');
        }
        $custom_component_obj = new CustomComponent;
        // dd($student_id);
        $lastEnteredBy = DocumentVerification::where('student_id', $student_id)
            ->orderby("id", "DESC")->first();
        // dd($lastEnteredBy);
        if (isset($lastEnteredBy->role_id) && $lastEnteredBy->role_id == $role_id) {
            return redirect()->back()->with('error', 'Your verification is already submitted!Previous steps have not yet been completed.Something is wrong.');
        }
        $isReverify = 1;
        $masterInputArr = $custom_component_obj->getStudentVerificaitonMainDocDetails($student_id, $isReverify);
        // echo "student controller -> 4570";dd($masterInputArr);
        // $combo_name = 'fresh_student_doc_update_status';$fresh_student_doc_update_status = $this->master_details($combo_name);

        if (@$masterInputArr) {
        } else {
            return redirect()->back()->with('error', 'Master details not found! Something is wrong.');
        }
        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);

        if ($isLockAndSubmit == 1) {
        } else {
            return redirect()->route('view_details', Crypt::encrypt($student_id))->with('error', 'Form not locked and submitted.');
        }
        $isItiStudent = false;//$this->_isItiStudent($student_id);
        // $combo_name = 'student_document_path';
        // $student_document_path = $this->master_details($combo_name);
        // $studentDocumentPath = $student_document_path[1].$student_id;

        $custom_component_obj = new CustomComponent;

        $dateIsOpen = false;
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();

        if ($isAdminStatus) {
            $dateIsOpen = true;
        } else {
            $dateIsOpen = $custom_component_obj->getDocumentVerificationAllowOrNot($role_id);
        }
        // echo "student controller -> 12333";dd($dateIsOpen);
        if (!$dateIsOpen) {
            return redirect()->back()->with('error', 'Failed! Date  has been closed!');
        }
        //here check it's clerification document by student then go to rejected_verify_documents
        $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
        $current_exam_month_id = Config::get("global.form_current_exam_month_id");

        // $master = Document::where('student_id',$student_id)->first();

        $master = $studentDocumentVerificaitonData = StudentDocumentVerification::where('student_id', $student_id)->orderby("id", "desc")->first();
        $studentDocumentPath = $student_verification_documents[1] . $current_admission_session_id . DIRECTORY_SEPARATOR . $current_exam_month_id . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$studentDocumentVerificaitonData->id . DIRECTORY_SEPARATOR;
        $studentDocumentPathTemp = "documents" . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR;


        $combo_name = 'doc_verification_status';
        $doc_verification_status = $this->master_details($combo_name);
        if (@$doc_verification_status[1]) {
            unset($doc_verification_status[1]);
        }
        if (@$doc_verification_status[4]) {
            unset($doc_verification_status[4]);
        }
        // dd($doc_verification_status);
        $documentverifications = null;
        //$documentverifications = DocumentVerification::where('student_id',$student_id)->first();

        $documentInput = $this->getStudentDocumentListForVerification($student_id);
        $masterDetails = Student::with('application', 'document', 'address', 'admission_subject', 'toc_subject', 'exam_subject', 'StudentFee', 'tocdetils', 'bankdetils')->where('id', $student_id)->first();


        $role_id = @Session::get('role_id');
        $verifier_id = Config::get('global.verifier_id');
        $super_admin_id = Config::get('global.super_admin_id');
        $academicofficer_id = Config::get('global.academicofficer_id');

        $verifier_documents_verification = array();
        $isCurrentacademicofficer_id = false;
        if ($role_id == $academicofficer_id) {
            $isCurrentacademicofficer_id = true;
            if (@$lastEnteredBy->verifier_upper_documents_verification) {
                $verifier_upper_documents_verification = json_decode(@$lastEnteredBy->verifier_upper_documents_verification, true);
            }
            if (@$lastEnteredBy->verifier_documents_verification) {
                $verifier_documents_verification = json_decode(@$lastEnteredBy->verifier_documents_verification, true);
            }
        }

        if (count($request->all()) > 0) {
            $inputs = $request->all();
            $inputs['isAllRejected'] = null;
            $is_ao_agree_with_verifier = 1;
            $finalInputs = array();
            /* Old */
            /* foreach($verifier_documents_verification as $k => $v){
					foreach($v as $lk => $lv){
						$finalInputs[$k][$lk] = @$inputs['upper_level'][$k][$lk];
						if(@$inputs['upper_level'][$k][$lk]){
							if( @$inputs['upper_level'][$k][$lk] == "on"){
								$finalInputs[$k][$lk] =  $lv;
							}else{
								$is_ao_agree_with_verifier = 2;
								if($inputs['upper_level'][$k][$lk] == 1){
									$finalInputs[$k][$lk] =  2;
								}else{
									$finalInputs[$k][$lk] =  1;
								}
							}
						}else{
							if($lv == 1){
								$finalInputs[$k][$lk] =  2;
							}else{
								$finalInputs[$k][$lk] =  1;
							}
						}
					}
				}
				*/
            /* Old */

            /* New */
            foreach ($masterInputArr as $k => $v) {
                if (isset($v['lower_level'])) {
                    // dd($v['lower_level']);
                    foreach ($v['lower_level'] as $lk => $lv) {
                        // dd($inputs['upper_level'][$k][$lv->field_id]);
                        if (isset($inputs['upper_level'][$k][$lv->field_id]) && $inputs['upper_level'][$k][$lv->field_id] == 'on') {
                            $finalInputs[@$lv->main_document_id][@$lv->field_id] = 1;
                        } else {
                            $is_ao_agree_with_verifier = 2;
                            $finalInputs[@$lv->main_document_id][@$lv->field_id] = 2;
                        }
                    }
                }
            }
            /* New */
            $upperLevelFinalInputs = array();
            // dd($finalInputs);
            foreach ($finalInputs as $k => $v) {
                $upperLevelFinalInputs[$k] = 1;
                foreach ($v as $lk => $lv) {
                    // dd($lv);
                    if ($lv == 2) {
                        $inputs['isAllRejected'] = 1;
                        $upperLevelFinalInputs[$k] = 2;
                        break;
                    }
                }
            }

            // echo "<pre>";
            // print_r($verifier_documents_verification);
            // print_r($upperLevelFinalInputs);
            // dd($finalInputs);


            // dd($upperLevelFinalInputs);
            // echo "<pre>";
            // print_r(json_encode($finalInputs));
            // dd($finalInputs);


            $inputs['upper_level'] = $finalInputs;
            $inputs['only_upper_level'] = $upperLevelFinalInputs;

            $tempInputs = $inputs;
            unset($tempInputs['ajaxRequest']);
            unset($tempInputs['_method']);
            unset($tempInputs['_token']);
            unset($tempInputs['action']);

            // echo "<pre>";
            // print_r($finalInputs);
            // dd($tempInputs);


            $responseFinal = null;
            $responses = true;
            /* Start */
            $docverificationstatus = null;
            $studentdocverificationstatus = null;
            if ($responses == true) {
                $role_id = @Session::get('role_id');
                $fldNameAliase = null;

                $finalStatus = 2;
                $isVerified = 1;
                $stage = 3;
                $is_doc_rejected = null;
                $is_permanent_rejected_by_dept = 0;

                $super_admin_id = Config::get("global.super_admin_id");
                $academicofficer_id == Config::get('global.academicofficer_id');

                if (@$inputs['isAllRejected'] == 1) {//i.e. finally rejected
                    $finalStatus = 3;
                    $isVerified = 2;
                    $is_doc_rejected = 1;
                    $stage = 4;
                }
                if ($role_id == Config::get('global.academicofficer_id')) {
                    $fldNameAliase = "ao_";
                    $studentamount = 0;
                }
                $docverificationstatus = [
                    $fldNameAliase . 'verify_user_id' => $loginuserid,
                    'role_id' => $role_id,
                    $fldNameAliase . 'status' => $finalStatus,
                    'student_id' => $student_id,
                    $fldNameAliase . 'upper_documents_verification' => json_encode(@$inputs['only_upper_level']),
                    $fldNameAliase . 'documents_verification' => json_encode(@$inputs['upper_level']),
                    $fldNameAliase . 'verify_datetime' => date('Y-m-d H:i:s'),
                    'amount' => @$studentamount,
                    'is_ao_agree_with_verifier' => $is_ao_agree_with_verifier
                ];

                $studentdocverificationstatus = [
                    $fldNameAliase . 'verify_user_id' => $loginuserid,
                    'is_doc_rejected' => $is_doc_rejected,
                    'stage' => $stage,
                    $fldNameAliase . 'verify_datetime' => date('Y-m-d H:i:s'),
                    'is_' . $fldNameAliase . 'verify' => $isVerified,
                    $fldNameAliase . 'status' => $finalStatus,
                ];
                // echo "<pre>";print_r($studentdocverificationstatus);
                // dd($docverificationstatus);
            }

            if (@$inputs['isAllRejected'] == 1) {
            } else {
                $master_id = @$studentDocumentVerificaitonData->id;
                if (@$master_id) {
                    $isValid = $this->_movementOfFreshDocuemnts($student_id, @$master_id);
                }
            }
            $DocumentVerification = DocumentVerification::create($docverificationstatus);
            $StudentDocumentVerification = DB::table('students')->where('id', $student_id)->update($studentdocverificationstatus);

            if ($role_id == Config::get('global.super_admin_id') || $role_id == $academicofficer_id) {
                if (@$inputs['isAllRejected'] == 1) {
                } else {
                    // $master_id = $studentDocumentVerificaitonData->id;
                    // $isValid = $this->_movementOfFreshDocuemnts($student_id,$master_id);
                    // echo "<pre>";print_r($studentdocverificationstatus);
                    // print_r($master_id);
                    // echo "<br>";
                    // print_r($isValid);
                    // echo "<br>";
                    // print_r($student_id);
                    // dd($docverificationstatus);
                    $enrollment = $this->_setEnrollmentAndIsEligiable($student_id);
                }

                /* sms send start */
                $detailsHindi = $details = null;
                if (@$inputs['isAllRejected'] == 1) {
                    $details = $this->getVerifcaionSMSSend('deficient', 'eng');
                    $detailsHindi = $this->getVerifcaionSMSSend('deficient', 'hindi');
                } else {
                    $details = $this->getVerifcaionSMSSend('approved', 'eng');
                    $detailsHindi = $this->getVerifcaionSMSSend('approved', 'hindi');
                }
                $detailsHindi['mobile'] = $details['mobile'] = @$masterDetails->mobile;
                if ($details['mobile'] != null && $details['sms'] != null && $details['templateID'] != "") {
                    $smsStatus = $this->_sendSMS($details['mobile'], $details['sms'], $details['templateID']);
                }
                if ($detailsHindi['mobile'] != null && $detailsHindi['sms'] != null && $detailsHindi['templateID'] != "") {
                    $smsStatus = $this->_sendSMS($detailsHindi['mobile'], $detailsHindi['sms'], $detailsHindi['templateID']);
                }
                /* sms send end  */
            }
            /* End */

            if ($docverificationstatus) {
                
				
				/* Old Start */
					// $exam_month = @$studentdata->exam_month;
					// $fldNameAliase = "verifier_status";
					// if ($role_id == Config::get('global.super_admin_id')) { //Acedmic Department
						// $fldNameAliase = "department_status";
					// } else if ($role_id == Config::get('global.academicofficer_id')) {
						// $fldNameAliase = "ao_status";
					// }
					// return redirect()->route("verifying_student_applications", ['exam_month' => $exam_month, $fldNameAliase => @$studentdata->$fldNameAsperLogin])->with('message', 'Document vertification has been successfully submitted.');
				/* Old End */
				
				/* Udpate Start */
				$tempCond = @Session::get('tempCond');  
				return redirect()->route("verifying_student_applications",[
					'enrollment' => @$tempCond['students.enrollment'],
					'ssoid' => @$tempCond['students.ssoid'],
					'ai_code' => @$tempCond['students.ai_code'],
					'name' => @$tempCond['students.name'],
					'gender_id' => @$tempCond['students.gender_id'],
					'medium' => @$tempCond['applications.medium'],
					'is_permanent_rejected_by_dept' => @$tempCond['student_verifications.is_permanent_rejected_by_dept'],
					'adm_type' => @$tempCond['students.adm_type'],
					'exam_month' => @$tempCond['students.exam_month'],
					'course' => @$tempCond['students.course'],
					'verifier_status' => @$tempCond['students.verifier_status'],
					'ao_status' => @$tempCond['students.ao_status'], 
					'department_status' => @$tempCond['students.department_status'], 
				])->with('message', 'Document vertification has been successfully submitted.');
				 
				
				/* Udpate End */
				//need to code here
				
				
            } else {
                return redirect()->route('verify_documents', Crypt::encrypt($student_id))->with('error', 'Failed! Document vertification details has been not submitted');
            }
        }

        $rejectionType = "firsttime";
        $documentVerifierVerifications = array();

        // dd($lastEnteredBy);

        if (@$lastEnteredBy->verifier_documents_verification && !empty($lastEnteredBy->verifier_documents_verification)) {
        } else {
            return redirect()->back()->with('error', 'Verifier verification details not found! Something is wrong.');
        }

        if ($role_id == Config::get('global.super_admin_id')) {
            $documentVerifierVerifications = DocumentVerification::where('student_id', $student_id)
                ->where('role_id', $verifier_id)
                ->orderby("id", "DESC")->first();
        }
        return view('student.ao_rejected_verify_documents', compact('isCurrentacademicofficer_id', 'verifier_documents_verification', 'verifier_upper_documents_verification', 'lastEnteredBy', 'masterInputArr', 'role_id', 'rejectionType', 'documentVerifierVerifications', 'master_subject_details', 'verifier_status_label', 'stream_id', 'masterDetails', 'doc_verification_status', 'documentInput', 'studentDocumentPath', 'page_title', 'estudent_id', 'studentdata', 'model', 'student_id', 'master', 'studentDocumentPathTemp'));
    }

    public function dept_rejected_verify_documents(Request $request, $student_id = null)
    {
        $loginuserid = Auth::user()->id;
        $table = $model = "DocumentVerification";
        $page_title = 'Student Document Re-Verification';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);


        $role_id = @Session::get('role_id');
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'ao_dept_status_label';
        $ao_dept_status_label = $this->master_details($combo_name);
        $studentdata = Student::findOrFail($student_id);
        $master_subject_details = $this->getStudentPdfDetails($student_id);
        $super_admin_id = Config::get('global.super_admin_id');
        $isAllowTofill = $this->getStudentVerificatonCount($student_id, 0, $super_admin_id);
        if (@$isAllowTofill) {
        } else {
            return redirect()->back()->with('error', 'You are not allowed! Something is wrong.');
        }


        $verifier_id = Config::get('global.verifier_id');
        $fldNameAliase = "verifier_";
        if ($role_id == Config::get('global.super_admin_id')) { //Acedmic Department
            $fldNameAliase = "department_";
        } else if ($role_id == Config::get('global.academicofficer_id')) { //Acedmic Department
            $fldNameAliase = "ao_";
        }
        $studentamount = 0;
        $fldNameAsperLogin = $fldNameAliase . 'status';

        if ($studentdata->$fldNameAsperLogin == 10) {
        } else {
            return redirect()->back()->with('error', 'Something is wrong.');
        }
        $custom_component_obj = new CustomComponent;
        // dd($student_id);
        $lastEnteredBy = DocumentVerification::where('student_id', $student_id)
            ->orderby("id", "DESC")->first();
        // dd($lastEnteredBy);
        if (isset($lastEnteredBy->role_id) && $lastEnteredBy->role_id == $role_id) {
            return redirect()->back()->with('error', 'Your verification is already submitted! Something is wrong.');
        }
        $isReverify = 1;
        $masterInputArr = $custom_component_obj->getStudentVerificaitonMainDocDetails($student_id, $isReverify);
        // echo "student controller -> 4570";dd($masterInputArr);
        // $combo_name = 'fresh_student_doc_update_status';$fresh_student_doc_update_status = $this->master_details($combo_name);

        if (@$masterInputArr) {
        } else {
            return redirect()->back()->with('error', 'Master details not found! Something is wrong.');
        }
        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);

        if ($isLockAndSubmit == 1) {
        } else {
            return redirect()->route('view_details', Crypt::encrypt($student_id))->with('error', 'Form not locked and submitted.');
        }
        $isItiStudent = false;//$this->_isItiStudent($student_id);
        // $combo_name = 'student_document_path';
        // $student_document_path = $this->master_details($combo_name);
        // $studentDocumentPath = $student_document_path[1].$student_id;

        $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
        $current_exam_month_id = Config::get("global.form_current_exam_month_id");
        $combo_name = 'student_verification_documents';
        $student_verification_documents = $this->master_details($combo_name);
        $studentDocumentVerificaitonData = StudentDocumentVerification::where('student_id', $student_id)->orderby("id", "desc")->first();
        $studentDocumentPath = $student_verification_documents[1] . $current_admission_session_id . DIRECTORY_SEPARATOR . $current_exam_month_id . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$studentDocumentVerificaitonData->id . DIRECTORY_SEPARATOR;
        $studentDocumentPathTemp = "documents" . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR;

        // dd($studentDocumentPath);

        $custom_component_obj = new CustomComponent;

        $dateIsOpen = false;
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();

        if ($isAdminStatus) {
            $dateIsOpen = true;
        } else {
            $dateIsOpen = $custom_component_obj->getDocumentVerificationAllowOrNot($role_id);
        }
        // echo "student controller -> 12333";dd($dateIsOpen);
        if (!$dateIsOpen) {
            return redirect()->route("student_applications")->with('error', 'Failed! Date  has been closed!');
        }

        $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
        $current_exam_month_id = Config::get("global.form_current_exam_month_id");

        $combo_name = 'student_verification_documents';
        $student_verification_documents = $this->master_details($combo_name);
        $current_exam_month_id = Config::get("global.form_current_exam_month_id");
        $studentDocumentVerificaitonData = StudentDocumentVerification::where('student_id', $student_id)->orderby("id", "desc")->first();
        $studentDocumentPath = $student_verification_documents[1] . $current_admission_session_id . DIRECTORY_SEPARATOR . $current_exam_month_id . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$studentDocumentVerificaitonData->id . DIRECTORY_SEPARATOR;


        //here check it's clerification document by student then go to rejected_verify_documents

        // $master = Document::where('student_id',$student_id)->first();

        $master = StudentDocumentVerification::where('student_id', $student_id)->orderby("id", "desc")->first();

        $combo_name = 'doc_verification_status';
        $doc_verification_status = $this->master_details($combo_name);
        if (@$doc_verification_status[1]) {
            unset($doc_verification_status[1]);
        }
        if (@$doc_verification_status[4]) {
            unset($doc_verification_status[4]);
        }
        // dd($doc_verification_status);
        $documentverifications = null;
        //$documentverifications = DocumentVerification::where('student_id',$student_id)->first();

        $documentInput = $this->getStudentDocumentListForVerification($student_id);
        $masterDetails = Student::with('application', 'document', 'address', 'admission_subject', 'toc_subject', 'exam_subject', 'StudentFee', 'tocdetils', 'bankdetils')->where('id', $student_id)->first();


        $role_id = @Session::get('role_id');
        $verifier_id = Config::get('global.verifier_id');
        $super_admin_id = Config::get('global.super_admin_id');
        $academicofficer_id = Config::get('global.academicofficer_id');

        $ao_documents_verification = array();
        $ao_upper_documents_verification = array();
        $isCurrentSuperAdmin_id = false;

        $temp_ao_documents_verification = [];
        if ($role_id == $super_admin_id) {
            $isCurrentSuperAdmin_id = true;
            if (@$lastEnteredBy->ao_upper_documents_verification) {
                $ao_upper_documents_verification = json_decode(@$lastEnteredBy->ao_upper_documents_verification, true);
                $ao_upper_documents_verification = array_filter($ao_upper_documents_verification, function ($value) {
                    return $value == 2;
                });
            }
            if (@$lastEnteredBy->ao_documents_verification) {
                $ao_documents_verification = $temp_ao_documents_verification = json_decode(@$lastEnteredBy->ao_documents_verification, true);
            }

            foreach ($temp_ao_documents_verification as $k => $v) {
                $isNeed = true;
                foreach ($v as $ik => $iv) {
                    if ($iv == 2) {
                        $isNeed = false;
                        break;
                    }
                }
                if ($isNeed) {
                    unset($ao_documents_verification[$k]);
                }
            }
        }


        if (count($request->all()) > 0) {
            $inputs = $request->all();
            $inputs['isAllRejected'] = null;

            $finalInputs = array();
            foreach ($ao_documents_verification as $k => $v) {
                foreach ($v as $lk => $lv) {
                    $finalInputs[$k][$lk] = @$inputs['upper_level'][$k][$lk];
                    if (@$inputs['upper_level'][$k][$lk]) {
                        if (@$inputs['upper_level'][$k][$lk] == "on") {
                            $finalInputs[$k][$lk] = 1;
                        } else {
                            if ($inputs['upper_level'][$k][$lk] == 1) {
                                $finalInputs[$k][$lk] = 1;
                            } elseif ($inputs['upper_level'][$k][$lk] == null) {
                                $finalInputs[$k][$lk] = 2;
                            } else {
                                $finalInputs[$k][$lk] = 2;
                            }
                        }
                    } else {
                        $finalInputs[$k][$lk] = 2;
                    }
                }
            }

            $upperLevelFinalInputs = array();

            foreach ($finalInputs as $k => $v) {
                $upperLevelFinalInputs[$k] = 1;
                foreach ($v as $lk => $lv) {
                    if ($lv == 2) {
                        $inputs['isAllRejected'] = 1;
                        $upperLevelFinalInputs[$k] = 2;
                        break;
                    }
                }
            }

            // echo "<pre>";
            // print_r($verifier_documents_verification);
            // print_r($upperLevelFinalInputs);
            // dd($finalInputs);


            // dd($upperLevelFinalInputs);
            // echo "<pre>";
            // print_r(json_encode($finalInputs));
            // dd($finalInputs);


            $inputs['upper_level'] = $finalInputs;
            $inputs['only_upper_level'] = $upperLevelFinalInputs;

            $tempInputs = $inputs;
            unset($tempInputs['ajaxRequest']);
            unset($tempInputs['_method']);
            unset($tempInputs['_token']);
            unset($tempInputs['action']);

            // echo "<pre>";
            // print_r($finalInputs);
            // dd($tempInputs);


            $responseFinal = null;
            $responses = true;
            /* Start */
            $docverificationstatus = null;
            $studentdocverificationstatus = null;
            if ($responses == true) {
                $role_id = @Session::get('role_id');
                $fldNameAliase = null;

                $finalStatus = 2;
                $isVerified = 1;
                $stage = 4;
                $is_doc_rejected = null;
                $is_permanent_rejected_by_dept = 0;

                $super_admin_id = Config::get("global.super_admin_id");
                $academicofficer_id == Config::get('global.academicofficer_id');
                // dd($inputs);
                if (@$inputs['isAllRejected'] == 1) {//i.e. finally rejected
                    $finalStatus = 3;
                    $isVerified = 2;
                    $is_doc_rejected = 1;
                    $stage = 5;
                    $is_permanent_rejected_by_dept = 1;
                }
                if ($role_id == Config::get('global.academicofficer_id')) {
                    $fldNameAliase = "ao_";
                    $studentamount = 0;
                } elseif ($role_id == Config::get('global.super_admin_id')) {
                    $fldNameAliase = "department_";
                    $studentamount = 0;
                }
                $docverificationstatus = [
                    $fldNameAliase . 'verify_user_id' => $loginuserid,
                    'role_id' => $role_id,
                    $fldNameAliase . 'status' => $finalStatus,
                    'student_id' => $student_id,
                    $fldNameAliase . 'upper_documents_verification' => json_encode(@$inputs['only_upper_level']),
                    $fldNameAliase . 'documents_verification' => json_encode(@$inputs['upper_level']),
                    $fldNameAliase . 'verify_datetime' => date('Y-m-d H:i:s'),
                    'amount' => @$studentamount,
                    'is_permanent_rejected_by_dept' => $is_permanent_rejected_by_dept,
                ];

                $studentdocverificationstatus = [
                    $fldNameAliase . 'verify_user_id' => $loginuserid,
                    'is_doc_rejected' => $is_doc_rejected,
                    'stage' => $stage,
                    $fldNameAliase . 'verify_datetime' => date('Y-m-d H:i:s'),
                    'is_' . $fldNameAliase . 'verify' => $isVerified,
                    $fldNameAliase . 'status' => $finalStatus,
                ];
                // echo "<pre>";print_r($studentdocverificationstatus);
                // dd($docverificationstatus);
            }

            if (@$inputs['isAllRejected'] == 1) {
            } else {
                $master_id = $studentDocumentVerificaitonData->id;
                $isValid = $this->_movementOfFreshDocuemnts($student_id, $master_id);
            }
            // echo "5138";die;
            $DocumentVerification = DocumentVerification::create($docverificationstatus);
            $StudentDocumentVerification = DB::table('students')->where('id', $student_id)->update($studentdocverificationstatus);

            $academicofficer_id = Config::get('global.academicofficer_id');
            if ($role_id == Config::get('global.super_admin_id') || $role_id == $academicofficer_id) {
                if (@$inputs['isAllRejected'] == 1) {
                } else {
                    $enrollment = $this->_setEnrollmentAndIsEligiable($student_id);
                }

                /* sms send start */
                $detailsHindi = $details = null;
                if (@$inputs['isAllRejected'] == 1) {
                    $details = $this->getVerifcaionSMSSend('deficient', 'eng');
                    $detailsHindi = $this->getVerifcaionSMSSend('deficient', 'hindi');
                } else {
                    $details = $this->getVerifcaionSMSSend('approved', 'eng');
                    $detailsHindi = $this->getVerifcaionSMSSend('approved', 'hindi');
                }
                $detailsHindi['mobile'] = $details['mobile'] = @$masterDetails->mobile;
                if ($details['mobile'] != null && $details['sms'] != null && $details['templateID'] != "") {
                    $smsStatus = $this->_sendSMS($details['mobile'], $details['sms'], $details['templateID']);
                }
                if ($detailsHindi['mobile'] != null && $detailsHindi['sms'] != null && $detailsHindi['templateID'] != "") {
                    $smsStatus = $this->_sendSMS($detailsHindi['mobile'], $detailsHindi['sms'], $detailsHindi['templateID']);
                }
                /* sms send end  */
            }
            /* End */

            if ($docverificationstatus) {
                $exam_month = @$studentdata->exam_month;
                $fldNameAliase = "verifier_status";
                if ($role_id == Config::get('global.super_admin_id')) { //Acedmic Department
                    $fldNameAliase = "department_status";
                } else if ($role_id == Config::get('global.academicofficer_id')) {
                    $fldNameAliase = "ao_status";
                }
                return redirect()->route("verifying_student_applications", ['exam_month' => $exam_month, $fldNameAliase => 1])->with('message', 'Document vertification has been successfully submitted.');
            } else {
                return redirect()->route('verify_documents', Crypt::encrypt($student_id))->with('error', 'Failed! Document vertification details has been not submitted');
            }
        }

        $rejectionType = "firsttime";
        $documentVerifierVerifications = array();


        if (@$lastEnteredBy->ao_documents_verification && !empty($lastEnteredBy->ao_documents_verification)) {
        } else {
            return redirect()->back()->with('error', 'AO verification details not found! Something is wrong.');
        }

        // dd($lastEnteredBy->ao_documents_verification);


        if ($role_id == Config::get('global.super_admin_id')) {
            $documentVerifierVerifications = DocumentVerification::where('student_id', $student_id)
                ->where('role_id', $verifier_id)
                ->orderby("id", "DESC")->first();
        }
        // dd($studentDocumentPath);
        return view('student.ao_rejected_verify_documents', compact('isCurrentSuperAdmin_id', 'ao_documents_verification', 'ao_upper_documents_verification', 'lastEnteredBy', 'masterInputArr', 'role_id', 'rejectionType', 'documentVerifierVerifications', 'studentDocumentVerificaitonData', 'master_subject_details', 'ao_dept_status_label', 'stream_id', 'masterDetails', 'doc_verification_status', 'documentInput', 'studentDocumentPath', 'page_title', 'estudent_id', 'studentdata', 'model', 'student_id', 'master', 'studentDocumentPathTemp'));
    }

    public function _checkValidToken($key = null)
    {
        if (@$key && $key == Config("global.api_token")) {
            return true;
        }
        return false;
    }

    public function _generatesecure_token($filedName = null)
    {
        $filedName = $filedName . date("d_m");
        return Crypt::encrypt($filedName);
    }


    public function mobile_view(Request $request)
    {
        $inputs = $request->all();

        //$enrollment = "21061232057";$ssoid = "00DHARASINGH.DHOBI";$dob = "1999-05-18";
        $ssoid = @$inputs['ssoid'];
        $secure_token = @$inputs['secure_token'];
        $student_id = @$inputs['student'];

        /* Secure Token check end */
        $isValidsecure_token = $this->_checksecure_token(@$secure_token, @$ssoid);

        if (!$isValidsecure_token) {
            $data = null;
            $status = false;
            $error = 'Invalid secure token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            print_r($response);
            die;
        }
        /* Secure Token check end */

        $table = $model = "Student";
        $page_title = 'View Details';
        $estudent_id = $student_id;
        $enrolment = Crypt::decrypt($student_id);
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters(null, null, 1);
        $master = Student::with('application', 'document', 'address', 'admission_subject', 'toc_subject', 'exam_subject', 'StudentFee', 'tocdetils', 'bankdetils')->where('enrollment', $enrolment)->first();
        $student_id = (@$master->id);

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
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $subject_list = $this->subjectList();
        $rsos_years = $this->rsos_years();
        $getBoardList = $this->getBoardList();
        $studentDocumentPath = $student_document_path[1] . $student_id;
        $master_subject_details = $this->getStudentPdfDetails($student_id);
        $tocpassyear = DB::table('rsos_years')->pluck('yearstext', 'id');
        $tocpassfail = DB::table('rsos_years_fail')->pluck('yearstext', 'id');

        $passfailyers = $this->getfailandpassingyears(@$master->adm_type, @$master->stream, @$master->tocdetils->board, @$master->application->toc);


        if (empty($master)) {
            return redirect()->route('/')->with('error', 'Failed! Details not found');
        }

        $fld = "documentDetails";
        if (isset($master[$fld])) {
            unset($master[$fld]);
        }
        $url = url('/payments/admission_fee_payment');

        /* Replace string with X start */
        $custom_component_obj = new CustomComponent;
        $master->mobile = $custom_component_obj->_replaceTheStringWithX(@$master->mobile);
        $master->application->jan_aadhar_number = $custom_component_obj->_replaceTheStringWithX(@$master->application->jan_aadhar_number);
        $master->application->aadhar_number = $custom_component_obj->_replaceTheStringWithX(@$master->application->aadhar_number);
        if (@$master->bankdetils->account_number) {
            $master->bankdetils->account_number = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->account_number);
        }
        if (@$master->bankdetils->linked_mobile) {
            $master->bankdetils->linked_mobile = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->linked_mobile);
        }
        if (@$master->bankdetils->ifsc_code) {
            $master->bankdetils->ifsc_code = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->ifsc_code);
        }
        $master->challan_tid = $custom_component_obj->_replaceTheStringWithX(@$master->challan_tid);
        /* Replace string with X end */

        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $current_admission_session_id = Config::get("global.current_admission_session_id");
        $curent_session_text = @$admission_sessions[$current_admission_session_id];
        $pdf = PDF::loadView('student.mobile_view', compact('curent_session_text', 'yesno', 'master_subject_details', 'subject_list', 'master', 'studentDocumentPath', 'student_id', 'page_title', 'estudent_id', 'model', 'gender_id', 'categorya', 'nationality', 'religion', 'disability', 'dis_adv_group', 'midium', 'rural_urban', 'employment', 'pre_qualifi', 'adm_types', 'course', 'exam_session', 'rsos_years', 'getBoardList', 'passfailyers', 'url', 'aiCenters', 'stream_id', 'are_you_from_rajasthan', 'tocpassyear', 'tocpassfail'));

        return view('student.mobile_view', compact('curent_session_text', 'yesno', 'master_subject_details', 'subject_list', 'master', 'studentDocumentPath', 'student_id', 'page_title', 'estudent_id', 'model', 'gender_id', 'categorya', 'nationality', 'religion', 'disability', 'dis_adv_group', 'midium', 'rural_urban', 'employment', 'pre_qualifi', 'adm_types', 'course', 'exam_session', 'rsos_years', 'getBoardList', 'passfailyers', 'url', 'aiCenters', 'stream_id', 'are_you_from_rajasthan'));

        //$path = public_path('studentpdf/ApplicationForm-'. $student_id . '.pdf');
        //$pdf->save($path,$pdf,true);
        //return $pdf->download('studentadmission.pdf');

        //return view('student.generate_student_pdf', compact('master_subject_details','subject_list','master','studentDocumentPath','student_id', 'page_title', 'estudent_id', 'model','gender_id','categorya','nationality','religion','disability','dis_adv_group','midium','rural_urban','employment','pre_qualifi','adm_types','course','exam_session','rsos_years','getBoardList','passfailyers','url','aiCenters','stream_id','are_you_from_rajasthan'));
    }


    public function fresh_form_doc_mark_verfication(Request $request, $student_id = null, $type = null)
    {
        $esupp_id = $student_id;

        $student_id = Crypt::decrypt($student_id);
        $student = Student::where('id', $student_id)->first(['id', 'course']);
        $estudent_id = Crypt::encrypt($student->student_id);
        $role_id = @Session::get('role_id');
        if ($role_id == Config::get('global.aicenter_id')) {
            $custom_data = array(
                'is_aicenter_verify' => $type,
                'aicenter_verify_user_id' => @Auth::id(),
                'aicenter_verify_datetime' => date("Y-m-d H:i:s"),
            );
            $supplementariesupdatedata = DB::table('students')->where('id', $student->id)->update($custom_data);
            $msg = "Student has been marked approved successfully.";

            //$movementofsuppDocuemnts = $this->_movementOfSuppDocuemnts($student_id,$estudent_id,$student->course);

            $studentMobiledetails = Student::where('id', $student->id)->first('mobile');
            $mobile = @$studentMobiledetails->mobile;
            //$this->sendSupplementaryDocuemntVerificationSMS($mobile,Config::get('global.aicenter_id'),2);


            if ($supplementariesupdatedata) {
                return redirect()->route('view_details', $esupp_id)->with('message', $msg);
            } else {
                $msg = "Student not successfully Submitted.";
                return redirect()->route('view_details', $esupp_id)->with('error', $msg);
            }

        } else if ($role_id == Config::get('global.examination_department')) {
            $custom_data = array(
                'is_eligible' => 1,
                'is_department_verify' => $type,
                'department_verify_user_id' => @Auth::id(),
                'department_verify_datetime' => date("Y-m-d H:i:s")
            );
            $supplementariesupdatedata = DB::table('students')->where('id', $student->id)->update($custom_data);
            //$movementofsuppDocuemnts = $this->_movementOfSuppDocuemnts($supp_id,$estudent_id,$student->course);
            $msg = "Student has been marked approved successfully.";

            $studentMobiledetails = Student::where('id', $student->id)->first('mobile');
            $mobile = @$studentMobiledetails->mobile;
            //$this->sendSupplementaryDocuemntVerificationSMS($mobile,Config::get('global.examination_department'),2);


            if ($supplementariesupdatedata) {
                return redirect()->route('view_details', $esupp_id)->with('message', $msg);
            } else {
                $msg = "Student not successfully Submitted.";
                return redirect()->route('view_details', $esupp_id)->with('error', $msg);
            }
        }
    }

    public function rejected_document_details(Request $request, $student_id)
    { //student document submission
        $table = $model = "DocumentVerification";
        $page_title = 'Clarification Document Details(स्पष्टीकरण दस्तावेज़ विवरण)';
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $studentdata = Student::findOrFail($student_id);
        $this->_checkStudentEntryAllowOrNotAllow($studentdata);

        $isLockAndSubmit = $this->_isCheckStudentFormLockAndSubmit($student_id);

        $docrejectednotification = $this->student_doc_rejected_notification($student_id);
        $isItiStudent = $this->_isItiStudent($student_id);

        $combo_name = 'student_verification_documents';
        $student_verification_documents = $this->master_details($combo_name);

        $routeUrl = "rejected_document_details";
        $previousTableName = "bank_details";
        $isValid = $this->getRecordExistorNot($student_id, $previousTableName);

        if (!$isValid) {
            //return redirect()->route($routeUrl, $estudent_id)->with('error', 'Failed! Please first fill the details!');
        }
        //dd($isValid);
        $imageInput = array(
            "photograph" => "फोटोग्राफ(Photograph)",
            "signature" => "(हस्ताक्षर)Signature",
        );
        // $documentInput = $this->getStudentRequriedDocument($student_id);
        $documentInput = [];
        $verifier_id = Config::get('global.verifier_id');
        $academicofficer_id = Config::get('global.academicofficer_id');
        $super_admin_id = Config::get('global.super_admin_id');


        $getroleid = $master = DocumentVerification::where('student_id', $student_id)
            ->whereIn('role_id', [$super_admin_id, $academicofficer_id])
            ->orderby("id", "DESC")->first();
        $fieldBaseName = $fldNameAliase = "ao_";
        if (@$getroleid->role_id == @$super_admin_id) { //Acedmic Department
            $fieldBaseName = $fldNameAliase = "department_";
        }
        $var_documents_verification = $fldNameAliase . 'documents_verification';
        $var_upper_documents_verification = $fldNameAliase . 'upper_documents_verification';
        // @dd($getroleid);
        $upper_documents_verification = @$getroleid->$var_upper_documents_verification;
        $documents_verification = @$getroleid->$var_documents_verification;

        $upper_documents_verification_arr = json_decode($upper_documents_verification, true);
        $upper_documents_verification_arr = array_filter($upper_documents_verification_arr, function ($value) {
            return $value == 2;
        });

        $isValidJson = $this->isValidJson($documents_verification);
        $documents_verification_arr = [];
        if ($isValidJson) {
            $documents_verification_arr = json_decode($documents_verification, true);
        }

        $allow_keys = array_keys(@$upper_documents_verification_arr);
        $documents_verification_arr = array_intersect_key($documents_verification_arr, array_flip($allow_keys));

        $documents_verification_arr = $this->_getfreshVerNotRequriedDocInput($documents_verification_arr);

        $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
        $current_exam_month_id = Config::get("global.form_current_exam_month_id");

        $verificationLabels = $this->getVerificationDetailedLabels();

        $verificationLowerLabels = $this->getVerificationMainDocumentLowerDetailedLabels(@$documents_verification_arr, @$studentdata->adm_type, @$studentdata->course);


        $studentDocumentVerificaitonData = StudentDocumentVerification::where('student_id', $student_id)->where('student_verification_id', $getroleid->id)->orderby("id", "desc")->first();
        // dd($studentDocumentVerificaitonData);
        $studentDocumentPath = $student_verification_documents[1] . $current_admission_session_id . DIRECTORY_SEPARATOR . $current_exam_month_id . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$studentDocumentVerificaitonData->id . DIRECTORY_SEPARATOR;


        // $documentInput = $this->getStudentRequriedRejectedDocument($master);

        if (count($request->all()) > 0) {

            if (!empty($request->type)) {
                $type = Crypt::decrypt(@$request->type);
                // dd($type);
                if ($type == 1) {
                    /* start */
                    $fldNameAliase = "ao_";
                    $studentSavaData = array('verifier_status' => 9);
                    if ($getroleid->role_id == Config::get('global.super_admin_id')) { //Acedmic Department
                        $fldNameAliase = "department_";
                    }
                    if ($studentdata->stage == 4) {
                        $studentSavaData = array('department_status' => 10);
                    }
                    $studentfeesupdate = Student::where('id', $student_id)->update($studentSavaData);
                    /* end */

                    if ($getroleid->role_id == $academicofficer_id) {
                        $updatedocumentVerification = StudentDocumentVerification::where('student_verification_id', $getroleid->id)->update(['is_eligible_for_verify' => 1]);
                        return redirect()->route('studentsdashboards')->with('message', 'Clarification Document has been successfully submitted.');
                    }

                    /* elseif($getroleid->role_id == $super_admin_id ){

						$combo_name = 'department_fresh_form_rejection_charge_amount';$department_fresh_form_rejection_charge_amount = $this->master_details($combo_name);
						$studentamount= $department_fresh_form_rejection_charge_amount[1];
						if($studentamount <= 0){
							$updatedocumentVerification = StudentDocumentVerification::where('student_verification_id',$getroleid->id)->update(['is_eligible_for_verify' => 1]);
							return redirect()->route('studentsdashboards')->with('message', 'Clarification Document has been successfully submitted.');
						}
						$studentenrollment = Crypt::encrypt($studentdata->enrollment);
						$updatedocumentVerification = StudentDocumentVerification::where('student_verification_id',$getroleid->id)->update(['is_eligible_for_verify' => 0]);
						return redirect()->route('fsdv_registration_fee',[$studentenrollment]);
					}	*/
                }
            }
            $rr = $request->document_type;

            $documentVerification = new DocumentVerification; /// create model object
            if ($request->document_type == 'i') {
                $validate = Validator::make($request->all(), [
                    $request->document_input => 'required|mimes:jpg,png,jpeg,gif,svg|between:10,50',
                ]);
            } else if ($request->document_type == 'd') {
                $validate = Validator::make($request->all(), [
                    $request->document_input => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|between:50,500'
                ]);
            }
            if ($validate->fails()) {
                return back()->withErrors($validate->errors())->withInput();
            }

            $input = $request->all();

            $inputName = $input['document_input'];
            $input[$input['document_input']] = $inputName . '.' . $request->$inputName->extension();


            // Student Documents Data Updation Log Enteries
            $table_primary_id = @$master->id;
            $table_name = 'student_document_verifications';
            $form_type = 'Verfication';
            $controller_obj = new Controller;
            $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
            // Student Documents Data Updation Log Enteries


            // dd($input);

            $documentVerification = StudentDocumentVerification::updateOrCreate(['student_id' => $student_id, 'student_verification_id' => $getroleid->id,], $input);


            $studentDocumentPath = $student_verification_documents[1] . $current_admission_session_id . DIRECTORY_SEPARATOR . $current_exam_month_id . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$documentVerification->id . DIRECTORY_SEPARATOR;

            $request->$inputName->move(public_path($studentDocumentPath), $input[$inputName]);
            // dd($studentDocumentPath);
            $custom_component_obj = new CustomComponent;
            $isStudentLoigin = $custom_component_obj->_udpateLastAcotionPermedBy($student_id);

            if ($documentVerification) {
                return redirect()->back()->with('message', 'Clarification Document has been successfully uploaded.');
            } else {
                return redirect()->back()->with('error', 'Clarification Document not uploaded.');
            }
        }
        // $remarksFields = array();
        // foreach($documentInput as $k => $v){
        // 	if($k == 'label'){
        // 		continue;
        // 	}
        // 	$remarksFields[$k] = @$getroleid[$fieldBaseName . $k . "_is_verify_remarks"];
        // }
        // echo "Test";die;
        return view('student.rejected_document_details', compact('verificationLowerLabels', 'fieldBaseName', 'getroleid', 'isItiStudent', 'isLockAndSubmit', 'studentDocumentPath', 'page_title', 'verificationLabels', 'estudent_id', 'model', 'student_id', 'imageInput', 'docrejectednotification', 'upper_documents_verification_arr', 'documents_verification_arr', 'studentDocumentVerificaitonData'));
    }


    public function studentupdateaicenter(Request $request, $student_id)
    {
        $page_title = 'Update AiCentre Details';
        $estudent_id = $student_id;
        $exam_year = Config::get("global.form_admission_academicyear_id");
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters(null, null, 1);

        $aicodes = array("01117");
        $allowedStudentIds = Student::
        where('verifier_status', 1)
            ->whereIn('ai_code', $aicodes)
            ->pluck("id");

        $students_id = Crypt::decrypt($estudent_id);
        $studentdata = Student::
        where('id', $students_id)
            ->first();

        if (@$allowedStudentIds) {
            $allowedStudentIds = $allowedStudentIds->toArray();
        }

        $isAllowToUpdateAiCode = in_array($students_id, $allowedStudentIds);
        if (!$isAllowToUpdateAiCode) {
            return redirect()->route('studentsdashboards')->with('error', 'You are not allowed to see.');
        }
        if (count($request->all()) > 0) {
            $this->validate($request, [
                'ai_code' => 'required|numeric',
            ]);
            $custom_component_obj = new CustomComponent;
            $user_id = $custom_component_obj->_getAiCentersIdByAiCode(@$request->ai_code);
            $custom_data = array(
                'user_id' => $user_id,
                'ai_code' => $request->ai_code
            );
            $Student = Student::where('id', $students_id)->where('exam_year', $exam_year)->update($custom_data);
            if (!empty($Student)) {
                return redirect()->route('studentsdashboards')->with('message', 'AiCentre detils has been successfully updated.');
            } else {
                return redirect()->back()->with('error', 'AiCentre detils not updated.');
            }
        }
        return view('student.studentupdateaicenter', compact('isAllowToUpdateAiCode', 'aiCenters', 'page_title', 'estudent_id', 'studentdata'));
    }

    public function mobile_number_details_edit(Request $request, $student_id)
    {
        $page_title = 'Mobile Number';
        $title = "Create DEO Details";
        $table_id = "User_Create_Details";
        $estudent_id = $student_id;
        $students_id = Crypt::decrypt($estudent_id);
        $data = ($estudent_id);
        $updatestudentmobile = Student::where('id', $students_id)->first('mobile');
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
        if (count($request->all()) > 0) {
            $this->validate($request, [
                'mobile' => 'required|numeric',
            ]);
            $custom_data = array(
                'mobile' => $request->mobile
            );
            $Student = Student::where('id', $students_id)->update($custom_data);
            if (!empty($Student)) {
                return redirect()->route('student_applications')->with('message', 'Mobile Number has been updated successfully updated.');
            } else {
                return redirect()->back()->with('error', 'AiCentre detils not updated.');
            }
        }

        $model = "useradd";
        return view('student.mobile_number_details_edit', compact('model', 'title', 'page_title', 'estudent_id', 'updatestudentmobile'));
    }


    public function student_change_requests(Request $request, $student_id)
    {
        $checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
        if (@$checkchangerequestsAllowOrNotAllow != 'true') {
            return redirect()->back()->with('error', 'Change Request date has been closed');
        }
        $role_id = @Session::get('role_id');
        $studentrole = Config::get("global.student");
        if (@$role_id == $studentrole) {
            $students_id = Crypt::decrypt($student_id);
            $studentgetdata = Student::where('id', $students_id)->first();
            $applicationgetdata = Application::where('student_id', $students_id)->first();
            if (!empty($students_id)) {
                $studentarray = [
                    'student_id' => $studentgetdata->id,
                    'exam_year' => $studentgetdata->exam_year,
                    'exam_month' => $studentgetdata->exam_month,
                    'ssoid' => $studentgetdata->ssoid,
                    'enrollment' => $studentgetdata->enrollment,
                    'student_change_requests' => 1,
                ];
                $Student = Student::where('id', $students_id)->update(['student_change_requests' => 1]);
                $changerequeststudent = ChangeRequestStudent::Create($studentarray);
                if (!empty($Student && $changerequeststudent)) {
                    return redirect()->back()->with('message', 'You have applied for Change in your application successfully');
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


    public function student_change_requests_approved(Request $request, $student_id)
    {
        $checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
        if (@$checkchangerequestsAllowOrNotAllow != 'true') {
            return redirect()->back()->with('error', 'Change Request date has been closed');
        }
        $role_id = @Session::get('role_id');
        $user_id = Auth::user()->id;
        $superadminrole = Config::get("global.super_admin");
        if (@$role_id == $superadminrole) {
            $students_id = Crypt::decrypt($student_id);
            $studentchallanupdatedid = Student::where('id', $students_id)->first();
            $studentfeesdat = StudentFee::where('student_id', $students_id)->first();
            $changerequeststudent = ChangeRequeStstudent::where('student_id', $students_id)->orderBy('id', 'desc')->first();
            if (!empty($students_id)) {
                $curr_timestamp = date('Y-m-d H:i:s');
                $data = array('user_id' => $user_id, "student_id" => $students_id, "role_id" => $role_id);
                $studentarray = [
                    'student_id' => @$studentfeesdat->student_id,
                    'adm_type' => @$studentfeesdat->adm_type,
                    'stream' => @$studentfeesdat->stream,
                    'adm_type' => @$studentfeesdat->adm_type,
                    'registration_fees' => @$studentfeesdat->registration_fees,
                    'online_services_fees' => @$studentfeesdat->online_services_fees,
                    'add_sub_fees' => @$studentfeesdat->add_sub_fees,
                    'forward_fees' => @$studentfeesdat->forward_fees,
                    'toc_fees' => @$studentfeesdat->toc_fees,
                    'practical_fees' => @$studentfeesdat->practical_fees,
                    'readm_exam_fees' => @$studentfeesdat->readm_exam_fees,
                    'late_fee' => @$studentfeesdat->late_fee, 'total' => @$studentfeesdat->total,
                    'session' => @$studentfeesdat->session,
                    'old_challan_tid' => @$studentchallanupdatedid->update_change_requests_challan_tid,
                    'student_change_request_id' => @$changerequeststudent->id,];
                $Student = Student::where('id', $students_id)->update(['student_change_requests' => 2]);
                //$Student = Student::where('id',$students_id)->update(['student_change_requests'=>2,'is_eligible'=>NULL]);
                $changerequeststudents = ChangeRequestStudent::where('id', $changerequeststudent->id)->where('student_id', $students_id)->update(['student_change_requests' => 2, 'deparment_approved_date' => $curr_timestamp]);
                //$application = Application::where('student_id',$students_id)->update(['locksumbitted'=>NULL,'locksubmitted_date'=>NULL]);
                $changerequerststudentapproveds = DB::table('change_requerst_student_approveds')->insert($data);
                $smsStatus = $this->_changerequestsendapprovedMessage($students_id);
                $ChangeRequertOldStudentFees = ChangeRequertOldStudentFees::create($studentarray);
                $studentDocumentDetails = Document::where('student_id', $students_id)->first();
                $fields = ["photograph", "signature", "category_a", "category_b", "category_c", "category_d", "cast_certificate", "disability", "disadvantage_group", "pre_qualification", "identiy_proof", "minority"];
                if (@$studentDocumentDetails) {
                    $studentDocumentDetails = $studentDocumentDetails->toArray();
                    foreach ($studentDocumentDetails as $k => $v) {
                        if (in_array($k, $fields) && $v != null) {
                            $custom_data[$k] = @$v;
                        }
                    }
                }
                foreach (@$custom_data as $k => $getdata) {

                    if (!empty($getdata)) {


                        /* Start */

                        $combo_name = 'student_document_path';
                        $student_document_path = $this->master_details($combo_name);
                        $studentDocumentPath = $student_document_path[1] . $students_id . '/';
                        $studentDocumentPath = public_path($studentDocumentPath . $getdata);
                        $exam_monthyear = Config::get("global.form_current_exam_month_id");
                        $current_folder_year = $this->getCurrentYearFolderNamematerialschecklist($exam_monthyear);
                        $path = public_path("changerequest/" . @$current_folder_year . "/" . @$students_id . "/" . @$changerequeststudent->id . "/");
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
                    return redirect()->back()->with('message', 'Change Request application for update approved successfully');
                } else {
                    return redirect()->back()->with('error', 'Change Request application  not approved');
                }
            } else {
                return redirect()->back()->with('message', 'record Not found.');
            }
        } else {
            return redirect()->back()->with('message', '403 USER DOES NOT HAVE THE RIGHT PERMISSIONS.');
        }
    }

    public function student_change_requests_update_application(Request $request, $student_id)
    {
        $checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
        if (@$checkchangerequestsAllowOrNotAllow != 'true') {
            return redirect()->back()->with('error', 'Change Request date has been closed');
        }
        $role_id = @Session::get('role_id');
        $studentrole = Config::get("global.student");
        if (@$role_id == $studentrole) {
            $students_id = Crypt::decrypt($student_id);
            $changerequeststudent = ChangeRequeStstudent::where('student_id', $students_id)->orderBy('id', 'desc')->first();
            $studentdata = Student::where('id', $students_id)->first('student_change_requests');
            if ($changerequeststudent->student_update_application == 1 && $studentdata->student_change_requests == 2) {
                return redirect()->route('persoanl_details', Crypt::encrypt($students_id))->with('message', 'Change Request application for update approved successfully');
            }
            if (!empty($students_id)) {
                $curr_timestamp = date('Y-m-d H:i:s');
                $studentalltablebackups = $this->studentalltablebackup($students_id);
                $Studentgets = Student::where('id', $students_id)->first();
                $Student = Student::where('id', $students_id)->update(['is_eligible' => NULL, 'is_verifier_verify' => NULL, 'is_department_verify' => NULL, 'verifier_verify_user_id' => NULL, 'department_verify_user_id' => NULL, 'verifier_verify_datetime' => NULL,
                    'department_verify_datetime' => NULL, 'department_status' => NULL, 'verifier_status' => NULL, 'stage' => NULL, 'ao_status' => NULL, 'ao_verify_user_id' => NULL, 'ao_verify_datetime' => NULL, 'is_ao_verify' => NULL, 'is_doc_rejected' => 0,]);
                $rsstudentverifications = DB::table('student_verifications')->where('student_id', $student_id)->update(['deleted_at' => date("Y-m-d H:i:s")]);
                $changerequeststudents = ChangeRequestStudent::where('id', $changerequeststudent->id)->where('student_id', $students_id)->update(['student_update_application' => 1, 'student_update_application_date' => $curr_timestamp]);
                $application = Application::where('student_id', $students_id)->update(['locksumbitted' => NULL, 'locksubmitted_date' => NULL, 'is_ready_for_verifying' => NULL]);
                /* Enrolment Change When Change Request By Student Start */
                $is_change_enrollment = $Studentgets->is_change_enrollment;
                // if($is_change_enrollment){
                $changeReqStatus = $this->_settingChangeReqEnrollment($students_id, $is_change_enrollment);
                // }

                /* Enrolment Change When Change Request By Student End */
                //$smsStatus = $this->_changerequestsendapprovedMessage($students_id);
                if (!empty($Student && $application && $changerequeststudents)) {
                    return redirect()->route('persoanl_details', Crypt::encrypt($students_id))->with('message', 'Change Request application for update approved successfully');
                } else {
                    return redirect()->back()->with('error', 'Change Request application  not approved');
                }
            } else {
                return redirect()->back()->with('message', 'record Not found.');
            }
        } else {
            return redirect()->back()->with('message', '403 USER DOES NOT HAVE THE RIGHT PERMISSIONS.');
        }
    }

    public function student_change_requests_end_date_after_update_data($exam_year, $exam_month)
    {
        $changerequeststudent = ChangeRequeStstudent::where('exam_year', $exam_year)->where('exam_month', $exam_month)->where('student_update_application', 1)->orderBy('id', 'desc')->get();
        foreach ($changerequeststudent as $studentgetdata) {

            dd($studentgetdata);

        }

    }


    public function student_remove_ssoid(Request $request, $student_id)
    {
        $estudent_id = $student_id;
        $students_id = Crypt::decrypt($estudent_id);
        $updatestudentmobile = Student::where('id', $students_id)->first('ssoid');
        $applicationupdate = Application::where('student_id', $students_id)->first(['jan_aadhar_number', 'jan_id']);
        if (!empty(@$updatestudentmobile)) {
            @$string1 = '_incorrect';
            @$janaadhaarstring = '10';
            @$janaadhaarId = $applicationupdate->jan_aadhar_number . '_' . $janaadhaarstring;
            @$janIds = $applicationupdate->jan_id . '_' . $janaadhaarstring;
            @$ssoidremove = @$updatestudentmobile->ssoid . '_' . $string1;
            $Student = Student::where('id', $students_id)->update(['ssoid' => $ssoidremove]);
            if (!empty(@$applicationupdate->jan_aadhar_number && $applicationupdate->jan_id)) {
                $applications = Application::where('student_id', $students_id)->update(['jan_aadhar_number' => $janaadhaarId, 'jan_id' => $janIds]);
            }
            if (!empty($Student)) {
                return redirect()->route('student_applications')->with('message', 'Student ssoid has been removed successfully.');
            } else {
                return redirect()->back()->with('error', 'AiCentre detils not updated.');
            }
        }
    }

    // public function ao_permanant_reject(Request $request,$student_id=null){
    // dd("TTTT");
    // }

}