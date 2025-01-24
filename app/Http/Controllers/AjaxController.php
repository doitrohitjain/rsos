<?php

namespace App\Http\Controllers;

use App\Component\BookRequirementCustomComponent;
use App\Component\CustomComponent;
use App\Component\MarksheetCustomComponent;
use App\Component\practicalCustomComponent;
use App\Component\RevalMarksComponent;
use App\Component\ThoeryCustomComponent;
use App\Helper\CustomHelper;
use App\Models\Address;
use App\Models\AdmissionSubject;
use App\Models\AicenterSittingMapped;
use App\Models\Application;
use App\Models\BookVolumeMaster;
use App\Models\CenterAllotment;
use App\Models\DocumentVerification;
use App\Models\ExamcenterDetail;
use App\Models\ExamResult;
use App\Models\ModelHasRole;
use App\Models\Pastdata;
use App\Models\Registration;
use App\Models\RevalStudentSubject;
use App\Models\Student;
use App\Models\StudentAllotment;
use App\Models\StudentAllotmentMark;
use App\Models\StudentDocumentVerification;
use App\Models\User;
use App\Models\UserExaminerMap;
use App\Models\UserPracticalExaminer;
use Auth;
use Config;
use DB;
use File;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Session;
use Validator;

class AjaxController extends Controller
{
    function __construct()
    {

    }

    public function get_district_by_state_id(Request $request)
    {
        $district_list = $this->districtsByState($request->id);
        return $district_list;
    }

    public function get_states(Request $request)
    {
        $state_list = $this->state_details();
        return $state_list;
    }

    public function get_subject_faculty_wise(Request $request)
    {
        $list = $this->_get_subject_faculty_wise(@$request->selected);
        return $list;
    }


    public function get_tehsil_by_district_id(Request $request)
    {

        $tehsil_list = $this->tehsilsByDistrictId($request->id);
        return $tehsil_list;
    }

    public function get_block_by_district_id(Request $request)
    {
        $block_list = $this->block_details($request->id);
        return $block_list;
    }


    public function ajaxtest(Request $request)
    {
        dd($request);
    }


    public function uploadDocument(Request $request)
    {
        $input = $request->all();
        dd($input);
        if ($request->ajax()) {
            $status = false;
            $error = null;
            $data = null;
            $input = $request->all();
            dd($input);
            if (count($request->all()) > 0) {
                $galleryId = 2;
                $input['image'] = time() . '.' . $request->image->extension();
                $request->image->move(public_path('images/' . $galleryId), $input['image']);
                Post::create($input);
                return redirect()->route('image');


            }
            $output[] = array('status' => $status, 'error' => $error, 'data' => $data);
            return response()->json($output);
        } else {
            //$output[] = array('status' => true,'error' => $error,'data' => 'else condtion');
            //return response()->json($output);
        }
    }

    public function ajaxRevalObtainedMarksSubjectValidationAndSubmission(Request $request)
    {
        if ($request->ajax()) {
            $status = false;
            $error = null;
            $data = null;
            if (count($request->all()) > 0) {
                $inputs = $request->all();

                $student_allotment_marks_id = Crypt::decrypt(@$inputs['key']);
                $revalStSubId = Crypt::decrypt(@$inputs['revalStSubId']);

                $revalSubjectId = $this->subjectIdByCode(@$inputs['revalStSubCode']);
                $item = (@$inputs['item']);
                $val = (@$inputs['val']);
                //here is login start
                $saveData = null;
                if ($item == "reval_final_theory_marks" || $item == "reval_type_of_mistake") {
                    $saveData[$item] = $val;
                } else {
                    $error = "Something is wrong!";
                    $output = array('status' => false, 'error' => $error, 'data' => null);
                    return response()->json($output);
                }


                $theory_custom_component_obj = new ThoeryCustomComponent;
                $getMaxMarks = $theory_custom_component_obj->getTheoryMaxMarks(@$revalSubjectId);

                $maxmarks = @$getMaxMarks->theory_max_marks;

                if ($item == "reval_final_theory_marks") {
                    if ($val > $maxmarks) {
                        $error = "Theory marks should be less then equal to max marks(" . $maxmarks . ").";
                        $output = array('status' => false, 'error' => $error, 'data' => null);
                        return response()->json($output);
                    }
                }


                $oldRevalStudentSubject = RevalStudentSubject::where("id", "=", $revalStSubId)->first();
                $oldStudentAllotmentMarks = StudentAllotmentMark::where("id", "=", $student_allotment_marks_id)->first();


                $reval_custom_component_obj = new RevalMarksComponent;
                if ($item == "reval_final_theory_marks") {
                    $responae_reval_process_result = $reval_custom_component_obj->reval_process_result($inputs);
                    $RevalStudentSubjectSaveData['final_theory_marks_after_reval'] = $responae_reval_process_result['new_theory_marks'];
                    $RevalStudentSubjectSaveData['is_grace_marks_given_after_reval'] = $responae_reval_process_result['is_grace_marks_given_after_reval'];
                    $RevalStudentSubjectSaveData['grace_marks_after_reval'] = $responae_reval_process_result['grace_marks_after_reval'];
                    $responae_reval_process_result['final_result_after_reval'] = $this->checkValType($responae_reval_process_result['final_result_after_reval']);

                    $RevalStudentSubjectSaveData['final_result_after_reval'] = $responae_reval_process_result['final_result_after_reval'];
                    $RevalStudentSubjectSaveData['total_marks_after_reval'] = $responae_reval_process_result['total_marks_after_reval'];
                    $revalStudentSubjectUpdateStatus = RevalStudentSubject::where("id", "=", $revalStSubId)->update($RevalStudentSubjectSaveData);
                }


                if ($item == "reval_final_theory_marks") {
                    $saveData["reval_any_change"] = true;
                    $saveData["reval_difference_after_reval"] = "";
                    $saveData["reval_is_subject_result_change"] = true;
                    $saveData["reval_is_subject_marks_entered"] = true;

                    $fld = "final_result_after_reval";
                    $saveData["reval_change_in_result"] = true;
                    if ($oldRevalStudentSubject[$fld] != $responae_reval_process_result[$fld]) {
                        $saveData["reval_change_in_result"] = false;
                        $saveData["reval_is_subject_result_change"] = false;
                    }
                }
                $studentAllotmentMarks = StudentAllotmentMark::where("id", "=", $student_allotment_marks_id)->update($saveData);


                $studentAllotmentMarks = StudentAllotmentMark::where("id", "=", $student_allotment_marks_id)->first();
                $studentAllotment = StudentAllotment::where("id", "=", $studentAllotmentMarks->student_allotment_id)->first();
                $revalStudentSubject = RevalStudentSubject::where("id", "=", $revalStSubId)->first();

                $data['final_theory_marks_before_reval'] = @$revalStudentSubject->final_theory_marks;
                $data['final_result_before_reval'] = @$revalStudentSubject->final_result;
                $data['total_marks_before_reval'] = @$revalStudentSubject->total_marks;
                $data['sessional_marks_before_reval'] = @$revalStudentSubject->sessional_marks;
                $data['final_practical_marks_before_reval'] = @$revalStudentSubject->final_practical_marks;

                $data['final_result_after_reval'] = @$revalStudentSubject->final_result_after_reval;
                $data['total_marks_after_reval'] = @$revalStudentSubject->total_marks_after_reval;
                $data['final_theory_marks_after_reval'] = @$revalStudentSubject->final_theory_marks_after_reval;
                $data['msg'] = "Updated!";
                $status = true;
            }
            $output = array('status' => $status, 'error' => $error, 'data' => $data, 'extra' => @$responae_reval_process_result);
        } else {
            $output = array('status' => true, 'error' => $error, 'data' => 'else condtion');

        }
        return response()->json($output);
    }


    public function checkValType($value = null)
    {
        if ($value != '' && $value != NULL) {
            if ($value == 'AB' || $value == 'A') {
                return 999; //For AB(Absent) value
            } else if ($value == 'SYCP') {
                return 666; //For SYCP value
            } else if ($value == 'SYCT') {
                return 777; //For SYCT value
            } else if ($value == 'SYC') {
                return 888; //For SYC(Supplementary) value
            } else if ($value == 'WH') {
                return 222; //For SYC(Supplementary) value
            } else if ($value == 'RW') {
                return 333; //For SYC(Supplementary) value
            } else if ($value == 'RWH') {
                return 444; //For SYC(Supplementary) value
            } else {
                return $value;
            }
        } else {
            return 0;
        }
    }

    public function ajaxRevalRTISubmission(Request $request)
    {
        if ($request->ajax()) {
            $status = false;
            $error = null;
            $data = null;
            if (count($request->all()) > 0) {
                $inputs = $request->all();

                $student_allotment_marks_id = Crypt::decrypt(@$inputs['key']);
                $revalStSubId = Crypt::decrypt(@$inputs['revalStSubId']);

                $combo_name = 'reval_rte_status';
                $reval_rte_status = $this->master_details($combo_name);
                $item = (@$inputs['item']);
                $val = (@$inputs['val']);
                //here is login start
                $saveData = null;
                if ($item == "reval_rte_status") {
                    $saveData[$item] = $val;
                } else {
                    $error = "Something is wrong!";
                    $output = array('status' => false, 'error' => $error, 'data' => null);
                    return response()->json($output);
                }

                $revalStudentSubject = RevalStudentSubject::where("id", "=", $revalStSubId)->update($saveData);

                $data["reval_rte_status"] = @$reval_rte_status[@$val];
                $data['msg'] = "Updated!";
                $status = true;
            }
            $output = array('status' => $status, 'error' => $error, 'data' => $data);
        } else {
            $output = array('status' => true, 'error' => $error, 'data' => 'else condtion');

        }
        return response()->json($output);
    }

    public function ajaxCheckSsoAlreadyExamCenter(Request $request)
    {
        if ($request->ajax()) {
            $status = false;
            $error = null;
            $data = null;
            if (count($request->all()) > 0) {
                $ExamcenterDetail = new ExamcenterDetail;
                $inputs = $request->all();
                $rulesexamcenterdetils = $ExamcenterDetail->rulesexamcenterdetils;

                if (isset($inputs['form_type']) && $inputs['form_type'] == 'add') {
                    $rulesexamcenterdetils['user_id'] = 'unique:examcenter_details';
                    $customMessages = [
                        'user_id.unique' => 'The SSO already mapped as Exam Center.',
                    ];
                    $validator = Validator::make($inputs, $rulesexamcenterdetils, $customMessages);
                } else {
                    $validator = Validator::make($inputs, $rulesexamcenterdetils);
                }

                if ($validator->fails()) {
                    $error = $validator->getMessageBag()->toArray();
                } else {
                    $status = true;
                }

            }
            $output = array('status' => $status, 'error' => $error, 'data' => $data);
            return response()->json($output);
        } else {
            $output = array('status' => true, 'error' => $error, 'data' => 'else condtion');
            return response()->json($output);
        }
    }

    public function checkAddressValidation(Request $request)
    {
        if ($request->ajax()) {
            $status = false;
            $error = null;
            $data = null;

            if (count($request->all()) > 0) {
                $AddressObj = new Address;
                $inputs = $request->all();


                $is_both_same = false;
                if (@$inputs['is_both_same']) {
                    $is_both_same = true;
                }

                if ($is_both_same == true) {
                    $fld = 'state_id';
                    if ($inputs[$fld] == "चयन राज्य (State)") {
                        $inputs[$fld] = null;
                    }
                    if (isset($inputs[$fld]) && $inputs[$fld] == 6) {
                        $validator = Validator::make($inputs, $AddressObj->forRajasthanAddressValidation);
                    } else if (isset($inputs[$fld]) && $inputs[$fld] != 6) {
                        $validator = Validator::make($inputs, $AddressObj->outOfRajasthanAddressValidation);
                    } else {
                        $validator = Validator::make($inputs, $AddressObj->forRajasthanAddressValidation);
                    }
                } else {


                    $fld = 'state_id';
                    if ($inputs[$fld] == "चयन राज्य (State)") {
                        $inputs[$fld] = null;
                    }
                    if (isset($inputs[$fld]) && $inputs[$fld] == 6) {
                        $validator = Validator::make($inputs, $AddressObj->permanatforRajasthanCurrentAddressValidation, $AddressObj->customMessage);
                    } else if (isset($inputs[$fld]) && $inputs[$fld] != 6) {
                        $validator = Validator::make($inputs, $AddressObj->permanatoutOfRajasthanCurrentAddressValidation, $AddressObj->customMessage);
                    } else {
                        $validator = Validator::make($inputs, $AddressObj->permanatforRajasthanCurrentAddressValidation, $AddressObj->customMessage);
                    }

                    if ($validator->fails()) {
                    } else {
                        $fld = 'current_state_id';
                        if ($inputs[$fld] == "चयन राज्य (State)") {
                            $inputs[$fld] = null;
                        }
                        if (isset($inputs[$fld]) && $inputs[$fld] == 6) {
                            $validator = Validator::make($inputs, $AddressObj->correspondanceforRajasthanCurrentAddressValidation, $AddressObj->customMessage);
                        } else if (isset($inputs[$fld]) && $inputs[$fld] != 6) {
                            $validator = Validator::make($inputs, $AddressObj->correspondanceoutOfRajasthanCurrentAddressValidation, $AddressObj->customMessage);
                        } else {
                            $validator = Validator::make($inputs, $AddressObj->correspondanceforRajasthanCurrentAddressValidation, $AddressObj->customMessage);
                        }
                    }


                }

                if ($validator->fails()) {
                    $error = $validator->getMessageBag()->toArray();
                } else {
                    $status = true;
                }
            }
            $output[] = array('status' => $status, 'error' => $error, 'data' => $data);
            return response()->json($output);
        } else {
            $output[] = array('status' => true, 'error' => $error, 'data' => 'else condtion');
            return response()->json($output);
        }
    }


    public function checkRegistration(Request $request)
    {
        // dd($request);

        //layout shold be ajax
        $status = false;
        $error = null;
        $data = null;
        if (count($request->all()) > 0) {
            $Student = new Student;
            $validator = Validator::make($request->all(), $Student->rules);
            if ($validator->fails()) {
                $error = $validator->getMessageBag()->toArray();
            } else {
                $status = true;
            }
        }
        $output[] = array('status' => $status, 'error' => $error, 'data' => $data);
        return response()->json($output);
    }

    public function _isAadharNumberExists($aadhar_number = null)
    {
        $counter = 0;
        if (@$aadhar_number) {
            $counter = Application::where('aadhar_number', $aadhar_number)->count();
        }
        return $counter;
    }

    public function _isJanNumberExists($jan_id = null)
    {
        $counter = 0;
        return $counter;
        if (@$jan_id) {
            $exam_year = Config::get("global.form_admission_academicyear_id");
            $counter = Application::where('jan_id', $jan_id)
                ->where('exam_year', $exam_year)
                ->count();
        }
        return $counter;
    }

    public function checkPersoanldetailValidation(Request $request)
    {
        $status = false;
        $error = null;
        $data = null;

        if (count($request->all()) > 0) {
            $Student = new Student;
            $validator = Validator::make($request->all(), $Student->rules);
            if ($validator->fails()) {
                $error = $validator->getMessageBag()->toArray();
            } else {
                $status = true;
            }
        }
        $output[] = array('status' => $status, 'error' => $error, 'data' => $data);
        return response()->json($output);
    }

    public function checkresultstudent(Request $request)
    {
        $data = false;
        $custom_component_obj = new CustomComponent;
        $checkCaptchaStatus = $custom_component_obj->checkCaptcha($request);
        if ($checkCaptchaStatus == false) {
            $data = 'captchaFalse';
        }

        if ($data != 'captchaFalse') {
            $students = $custom_component_obj->getresultstudentdata($request->enrollment, $request->dob);
            if (!empty($students)) {
                $data = true;
            } else {
                $data = false;
            }
        }
        return $data;
    }

    public function checkresultstudentold(Request $request)
    {
        $data = false;
        $custom_component_obj = new CustomComponent;
        $checkCaptchaStatus = $custom_component_obj->checkCaptcha($request);
        if ($checkCaptchaStatus == false) {
            $data = 'captchaFalse';
        }

        if ($data != 'captchaFalse') {
            $students = $custom_component_obj->getresultstudentdatamarksheet($request->enrollment, $request->dob);
            if (!empty($students)) {
                $data = true;
            } else {
                $data = false;
            }
        }
        return $data;
    }


    public function checksessionaltudent(Request $request)
    {
        $role_id = @Session::get('role_id');
        $aicenter_id = Config::get("global.aicenter_id");
        $devloper_admin = Config::get("global.developer_admin");
        $custom_component_obj = new CustomComponent;
        $aicenter_user_id = Auth::user()->id;
        $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
        $user_ai_code = @$aicenter_user_ids->ai_code;

        if (($request->all()) > 0) {
            $exam_year = Config::get("global.current_sessional_exam_year");
            $exam_month = Config::get("global.current_sessional_exam_month");
            if ($role_id == $aicenter_id) {
                $master = Student::where('ai_code', $user_ai_code)->where('is_eligible', 1)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->where('enrollment', $request->enrollment)->first();
            } else if ($role_id == $devloper_admin) {
                $master = Student::where('exam_year', $exam_year)->where('is_eligible', 1)->where('exam_month', $exam_month)->where('enrollment', $request->enrollment)->first();
            }

            if (!empty($master)) {
                $data = true;
            } else {
                $data = false;
            }
        }
        return $data;
    }

    public function ajaxGenerateCaptcha()
    {
        $custom_component_obj = new CustomComponent;
        $captchaImage = $custom_component_obj->generateCaptcha(1, 99, 150, 30);
        return $captchaImage;
    }

    public function ajaxSuppSubjectValidation(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $table = $model = "Supplementary";
        $customerrors = array();
        $response = array();
        $student_id = Crypt::decrypt($request->student_id);
        $studentdata = Student::findOrFail($student_id);

        if (count($request->all()) > 0) {
            $isValid = true;
            $inputs = $request->subject_id;
            $input_all = $request->all();
            $response = $custom_component_obj->isValidSuppSubjects($inputs, $studentdata, $input_all);

            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];

            if ($isValid) {
                return true;
            } else {
                return $response;
            }
        }
        return $response;
    }

    public function ajaxSuppSubjectdocumentValidation(Request $request)
    {

        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $response = $this->SuppSubjectdocumentDetailsValidation($request);

            $responseFinal = null;
            if (@$response) {
                $responseFinal = @$response['errors'];
                $isValid = @$response['isValid'];
            }
        }
        $output = array('isValid' => $isValid, 'errors' => @$responseFinal);

        return response()->json($output);
    }

    public function ajaxFacultySubjectValidation(Request $request)
    {
        $table = $model = "AdmissionSubject";
        $customerrors = null;
        $response = array();

        $student_id = $request->student_id;
        $student_id = Crypt::decrypt($student_id);
        $studentdata = Student::findOrFail($student_id);

        $subject_list = $this->subjectList();

        $subjectCountDetails = $this->getSubjectvalidations($studentdata->adm_type, $studentdata->course, $studentdata->stream, $studentdata->pre_qualification, $studentdata->year_pass);

        $modelObj = new AdmissionSubject;
        $master = AdmissionSubject::where('student_id', $student_id)->get();

        if (count($request->all()) > 0) {
            if (@$studentdata->course == 12) {

                $isValid = true;
                $inputs = $request->subject_id;
                $faculty_type_id = null;


                if (@$request->faculty_type_id) {
                    $faculty_type_id = $request->faculty_type_id;
                }

                $response = $this->getCountFacultySubjectSelection($inputs, $subjectCountDetails, $studentdata->course, $faculty_type_id);


                $isValid = $response['isValid'];

                if ($isValid) {
                    return 1;
                } else {
                    $customerrors = $response['errors'];
                    return 2;
                }
            }
        }
        return $customerrors;
    }

    public function get_blocks(Request $request)
    {
        $district_id = $request->id;
        if (!empty($district_id)) {
            $blockdetails = $this->block_details($district_id);
        } else {
            $blockdetails = $this->block_details();
        }
        return $blockdetails;
    }

    public function get_temp_blocks(Request $request)
    {
        $district_id = $request->id;
        $req_type = @$request->req_type;
        if (!empty($district_id)) {
            $blockdetails = $this->temp_block_details($district_id, $req_type);
        } else {
            $blockdetails = $this->temp_block_details(null, $req_type);
        }
        return $blockdetails;
    }


    public function get_aicode(Request $request)
    {
        $district_id = $request->district_id;
        $block_id = $request->block_id;
        if (!empty($district_id)) {
            $custom_component_obj = new CustomComponent;
            $aicodedetails = $custom_component_obj->_getAiCentersWithContactInfo($district_id, $block_id);
        } else {
            $custom_component_obj = new CustomComponent;
            if (!empty($block_id)) {
                $aicodedetails = $custom_component_obj->_getAiCentersWithContactInfo(null, $block_id);
            } else {
                $aicodedetails = $custom_component_obj->getAiCenters();
            }
        }
        return $aicodedetails;
    }

    public function get_temp_aicode(Request $request)
    {
        $district_id = $request->district_id;
        $block_id = $request->block_id;
        $req_type = @$request->req_type;
        if (!empty($district_id)) {
            $custom_component_obj = new CustomComponent;
            $aicodedetails = $custom_component_obj->_getTempAiCentersWithContactInfo($district_id, $block_id, $req_type);
        } else {
            $custom_component_obj = new CustomComponent;
            if (!empty($block_id)) {
                $aicodedetails = $custom_component_obj->_getTempAiCentersWithContactInfo(null, $block_id, $req_type);
            } else {
                $aicodedetails = $custom_component_obj->getAiCenters(null, $req_type);
            }
        }
        return $aicodedetails;
    }


    public function blockgetaicode(Request $request)
    {
        $block_id = $request->block_id;
        if (!empty($block_id)) {
            $custom_component_obj = new CustomComponent;
            $aicodedetails = $custom_component_obj->_getblockAiCentersWithContactInfo($block_id);
        } else {
            $custom_component_obj = new CustomComponent;
            $aicodedetails = $custom_component_obj->getAiCenters();
        }
        return $aicodedetails;

    }

    public function getdistrictaicenter(Request $request)
    {
        $district_id = $request->id;
        if (!empty($district_id)) {
            $custom_component_obj = new CustomComponent;
            $aicodedetails = $custom_component_obj->_getblockAiCentersWithContactInfodistrict_id($district_id);
        }
        return $aicodedetails;
    }

    public function getTempdistrictaicenter(Request $request)
    {
        $district_id = $request->id;
        $req_type = @$request->req_type;
        $aicodedetails = null;
        if (!empty($district_id)) {
            $custom_component_obj = new CustomComponent;
            $aicodedetails = $custom_component_obj->_getTempblockAiCentersWithContactInfodistrict_id($district_id, $req_type);
        }
        return @$aicodedetails;
    }

    public function AjaxSelfRegistrationValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();
        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->AjaxSelfsRegistrationValidation($request);

            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);
    }


    public function ajaxSubjectValidation(Request $request)
    {
        $table = $model = "AdmissionSubject";
        $customerrors = array();
        $response = array();

        $student_id = $request->student_id;
        $student_id = Crypt::decrypt($student_id);
        $studentdata = Student::findOrFail($student_id);

        $subject_list = $this->subjectList();

        $subjectCountDetails = $this->getSubjectvalidations($studentdata->adm_type, $studentdata->course, $studentdata->stream, $studentdata->pre_qualification, $studentdata->year_pass);

        $modelObj = new AdmissionSubject;
        $master = AdmissionSubject::where('student_id', $student_id)->get();

        if (count($request->all()) > 0) {
            $isValid = true;

            $mainInput = $request->all();

            $inputs = $request->subject_id;

            $faculty_type_id = null;
            if (@$request->faculty_type_id) {
                $faculty_type_id = $request->faculty_type_id;
            }

            $book_learning_type_id = @$mainInput['book_learning_type_id'];

            $response = $this->isValidSubjectSelection($inputs, $subjectCountDetails, $studentdata->course, $faculty_type_id, $book_learning_type_id);
            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];

            if ($isValid) {
                return true;
            } else {
                return $customerrors;
            }
        }
        return $customerrors;
    }

    public function getJanAadharDetails($janAadharNumber)
    {
        if (!empty($janAadharNumber)) {
            $responose = $this->_getJanAadharDetails($janAadharNumber);
            return $responose;
        } else {
            return false;
        }
    }

    public function set_current_session(Request $request)
    {
        $current_admission_session_id = Config::get('global.current_admission_session_id');
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        Session::put("current_admission_sessions", $current_admission_session_id);

        if (@$request->selectedVal) {
            Session::put("current_admission_sessions", $request->selectedVal);
        }
        $role_id = Session::get('role_id');
        //return redirect()->route('dashboard')->with('message', $admission_sessions[$request->current_session] . ' Session has been selected.');
        return $role_id;
    }


    public function set_current_role(Request $request)
    {
        if (@$request->selectedVal) {
            Session::put("role_id", $request->selectedVal);
        }
        $role_id = Session::get('role_id');
        $routes = $this->_getRoleRoute($role_id);
        $basePath = config("global.APP_URL");

        return route($routes->route);
    }

    public function ajaxshowPassFieldToc(Request $request)
    {
        $is_pass_filed_show = $this->showPassFailFieldToc($request->adm_type, $request->stream, $request->board);
        return $is_pass_filed_show;
    }

    public function ajaxRsosFailYearsList(Request $request)
    {
        $rsos_fail_year_list = $this->getRsosFailYearsList($request->board);
        return $rsos_fail_year_list;
    }

    public function ajaxRsosPassYearsList(Request $request)
    {
        $rsos_pass_year_list = $this->getRsosYearsList($request->board);
        return $rsos_pass_year_list;
    }

    public function ajaxIsPracticalSubject(Request $request)
    {
        $is_practical_subject = $this->checkIsPracticalSubject($request->subject_id, $request->board_id);
        return $is_practical_subject;
    }

    public function ajaxTocValidation(Request $request)
    {
        $table = $model = "Toc";
        $customerrors = array();
        $response = array();

        $student_id = $request->student_id;
        $student_id = Crypt::decrypt($student_id);
        $studentdata = Student::findOrFail($student_id);

        if (count($request->all()) > 0) {
            $isValid = true;

            $toc_submit_subject = 0;
            foreach ($request->toc_subject as $key => $each) {
                if (!empty($each['subject_id']) && $each['subject_id'] != null) {
                    $toc_submit_subject++;
                }
            }

            $response = $this->tocValidations($request, $request->board, $studentdata->adm_type, $toc_submit_subject);

            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];
        }
        $output = array('isValid' => $isValid, 'error' => $customerrors, 'validator' => $validator);
        return response()->json($output);
    }

    public function ajaxExamSubjectValidation(Request $request)
    {
        $table = $model = "ExamSubject";
        $customerrors = array();
        $response = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $response = $this->ExamSubjectValidation($request);
            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];
        }
        $output = array('isValid' => $isValid, 'error' => $customerrors, 'validator' => $validator);
        return response()->json($output);
    }


    public function ajaxRevalSubjectValidation(Request $request)
    {
        $table = $model = "RevalStudentSubject";
        $customerrors = array();
        $response = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $response = $this->ExamRevalSubjectValidation($request);
            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];
        }
        $output = array('isValid' => $isValid, 'error' => $customerrors, 'validator' => $validator);
        return response()->json($output);
    }

    public function ajaxPersonalDetilasValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->PersonalDetailsValidation($request);

            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }
        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);

        return response()->json($output);
    }


    public function ajaxRegistrationDetilasValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();
        $isValid = true;
        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->RegistrationDetailsValidation($request);
            // dd($responses);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {

                    if (!$response['isValid']) {
                        $isValid = false;
                    }

                    $responseFinal[$k]['isValid'] = @$response['isValid'];
                    $responseFinal[$k]['customerrors'] = @$response['errors'];
                    $responseFinal[$k]['validator'] = @$response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }


        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);

        return response()->json($output);
    }

    public function ajaxSessinalMarksValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $isValid = false;
        $customerrors = null;
        $validator = null;

        if (count($request->all()) > 0) {
            $isValid = true;
            $inputs = $request->all();
            $response = $this->isValidSessionalMarks($inputs);
            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];
        }
        $output = array('isValid' => $isValid, 'error' => $customerrors, 'validator' => $validator);

        return response()->json($output);
    }


    /* Update need start */
    public function getStudentsCount(Request $request)
    {
        $arr = null;
        $aicenter = $request->aicenter;
        $stream = $request->stream;

        if (isset($aicenter) && $aicenter != '') {
            $custom_component_obj = new CustomComponent;
            $finalData = array();

            $course = 10;
            $supptenthstatus = $custom_component_obj->_getsupplementarystudentallotmentstatusforaicode($aicenter, $stream, $course);
            $tenthstatus = $custom_component_obj->_getstudentallotmentstatusforaicode($aicenter, $stream, $course);

            $course = 12;
            $supptweilthstatus = $custom_component_obj->_getsupplementarystudentallotmentstatusforaicode($aicenter, $stream, $course);
            $tweilthstatus = $custom_component_obj->_getstudentallotmentstatusforaicode($aicenter, $stream, $course);

            $arr = array();
            $arr['supptenthstatus'] = $supptenthstatus;
            $arr['tenthstatus'] = $tenthstatus;
            $arr['supptweilthstatus'] = $supptweilthstatus;
            $arr['tweilthstatus'] = $tweilthstatus;

            $current_admission_session_id = Config::get('global.current_admission_session_id');

            $datacondtions = array(
                "aicode" => $aicenter,
                "stream" => $stream,
                "exam_year" => $current_admission_session_id
            );

            $aicenter_all_student_mapped = AicenterSittingMapped::where($datacondtions)->first();

            if ($supptenthstatus == 1 && $tenthstatus == 1 && $supptweilthstatus == 1 && $tweilthstatus == 1 && empty($aicenter_all_student_mapped)) {

                $finalData = array(
                    "aicode" => $aicenter,
                    "stream" => $stream,
                    "exam_year" => $current_admission_session_id,
                );
                $AicenterSittingMappedfiled = AicenterSittingMapped::save($finalData);

            } else {
                if (@$aicenter_all_student_mapped) {
                    AicenterSittingMapped::delete($aicenter_all_student_mapped['AicenterSittingMapped']['id']);
                }
            }
        }

        return response()->json($arr);
    }

    public function getStudentsStatusForAiCode($aicenter = null, $stream = null)
    {
        if (count($request->all()) > 0) {
            $finalData = array();
            $custom_component_obj = new CustomComponent;
            $finalData = $custom_component_obj->_getStudentsCountForExamcenter($aicenter, $stream);
        }
        return response()->json($finalData);
    }

    public function ajaxviewenrollments(Request $request)
    {
        if (count($request->all()) > 0) {
            $stream_global = config("global.CenterAllotmentStreamId");
            $exam_year = config("global.current_admission_session_id");
            $exam_month = config("global.current_exam_month_id");

            $conditionArray = array();
            $finalData = array(
                "examcenter_detail_id" => $request->examcenterdetailid,
                "center_allotment_id" => $request->centerallotmentid,
                // "ai_code" =>$request->aicode,
                "course" => $request->course,
                "exam_year" => $exam_year,
                "exam_month" => $exam_month,
                "stream" => $request->stream,
                "supplementary" => $request->supp
            );

            $enrollmentarr = StudentAllotment::where($finalData)->pluck('enrollment', 'id');
            $enrollmentarr = $enrollmentarr->toArray();
            $enrollmentarr = join(' , ', array_values($enrollmentarr));
            return $enrollmentarr;
        }
    }

    public function ajaxdeleteexamcenterallotment(Request $request)
    {
        $inputs = $request->all();

        $responose["status"] = true;
        $responose["msg"] = null;
        @$centerallotmentid = $inputs['centerallotment_id'];
        @$examcenterid = $inputs['examcenter_detail_id'];
        $id = $centerallotmentid;

        if (@$id) {
        } else {
            $responose["status"] = false;
            $responose["msg"] = "Invalid Access.";
        }

        if ($responose["status"]) {
            $master = CenterAllotment::find($id);
            if ($master) {
            } else {
                $responose["status"] = false;
                $responose["msg"] = "Invalid Access.";
            }
        }


        if ($responose["status"]) {

            $current_admission_session_id = Config::get('global.current_admission_session_id');
            $exam_month = Config::get('global.current_exam_month_id');

            $objStudentAllotment = StudentAllotment::where("center_allotment_id", "=", $centerallotmentid);
            // $objStudentAllotment->delete();

            // StudentAllotment Log Enteries Start
            $table_primary_id = $centerallotmentid;
            $controller_obj = new Controller;
            $log_status = $controller_obj->_updateStudentAllotmentLog($table_primary_id);
            // StudentAllotment Log Enteries End

            $objStudentAllotment->forceDelete();


            $getdataexamcenter = ExamcenterDetail::where("id", "=", $request->examcenter_detail_id)->first();
            $getdataaicode = User::where("ai_code", "=", $master->ai_code)->first();
            $district_list = $this->districtsByState(6);
            $districtname = @$district_list[$getdataexamcenter->district_id];
            $sicodedistrictname = @$district_list[$getdataaicode->district_id];
            $stream = $exam_month;
            $examcentermaterial = "examcentermaterial";
            $aicentermaterial = "aicentermaterial";
            $current_folder_year = $this->getCurrentYearFolderName();
            $path1 = public_path("files/reports/" . $current_folder_year . "/" . $examcentermaterial . "/" . $stream . "/10/" . $districtname . "/" . $getdataexamcenter->ecenter10 . "/");
            $path2 = public_path("files/reports/" . $current_folder_year . "/" . $examcentermaterial . "/" . $stream . "/12/" . $districtname . "/" . $getdataexamcenter->ecenter12 . "/");
            $path3 = public_path("files/reports/" . $current_folder_year . "/" . $aicentermaterial . "/" . $stream . "/10/" . $sicodedistrictname . "/" . $master->ai_code . "/");
            $path4 = public_path("files/reports/" . $current_folder_year . "/" . $aicentermaterial . "/" . $stream . "/12/" . $sicodedistrictname . "/" . $master->ai_code . "/");
            $movefolder1 = File::deleteDirectory($path1);
            $movefolder2 = File::deleteDirectory($path2);
            $movefolder3 = File::deleteDirectory($path3);
            $movefolder4 = File::deleteDirectory($path4);

            $datacondtions = array(
                'stream' => $master->stream,
                'aicode' => $master->ai_code,
                'exam_year' => $current_admission_session_id
            );
            $aicenter_all_student_mapped = AicenterSittingMapped::where($datacondtions)->first();
            if (@$aicenter_all_student_mapped) {
                $objAicenterSittingMapped = AicenterSittingMapped::where("id", "=", $aicenter_all_student_mapped->id);
                // $objAicenterSittingMapped->delete();

                // AicenterSittingMapped Log Enteries Start
                $table_primary_id = $aicenter_all_student_mapped->id;
                $controller_obj = new Controller;
                $log_status = $controller_obj->_updateAicenterSittingMappedLog($table_primary_id);
                // AicenterSittingMapped Log Enteries End


                $objAicenterSittingMapped->forceDelete();
            }
            $objCenterAllotment = CenterAllotment::where("id", "=", $id);
            // $objCenterAllotment->delete();


            // CenterAllotment Log Enteries Start
            $table_primary_id = $id;
            $controller_obj = new Controller;
            $log_status = $controller_obj->_updateCenterAllotmentLog($table_primary_id);
            // CenterAllotment Log Enteries End

            $objCenterAllotment->forceDelete();
            $responose["msg"] = "Deleted.";

        }
        return response()->json($responose);
    }

    public function noneedoldajaxdeleteexamcenterallotment(Request $request)
    {
        $inputs = $request->all();
        $responose["status"] = true;
        $responose["msg"] = null;

        $centerallotmentid = $inputs['centerallotment_id'];
        $examcenterid = $inputs['examcenter_detail_id'];
        $id = $centerallotmentid;


        if (@$id) {
        } else {
            $responose["status"] = false;
            $responose["msg"] = "Invalid Access.";
        }

        if ($responose["status"]) {
            $master = CenterAllotment::find($id);
            if ($master) {
            } else {
                $responose["status"] = false;
                $responose["msg"] = "Invalid Access.";
            }
        }

        if ($responose["status"]) {
            $current_admission_session_id = Config::get('global.current_admission_session_id');
            $exam_month = Config::get('global.current_exam_month_id');

            $objStudentAllotment = StudentAllotment::where("center_allotment_id", "=", $centerallotmentid);
            // $objStudentAllotment->delete();
            $objStudentAllotment->forceDelete();

            $getdataexamcenter = ExamcenterDetail::where("id", "=", $request->examcenter_detail_id)->first();
            $getdataaicode = User::where("ai_code", "=", $master->ai_code)->first();
            $district_list = $this->districtsByState(6);
            $districtname = @$district_list[$getdataexamcenter->district_id];
            $sicodedistrictname = @$district_list[$getdataaicode->district_id];
            $stream = $exam_month;
            $examcentermaterial = "examcentermaterial";
            $aicentermaterial = "aicentermaterial";
            $current_folder_year = $this->getCurrentYearFolderName();
            $path1 = public_path("files/reports/" . $current_folder_year . "/" . $examcentermaterial . "/" . $stream . "/10/" . $districtname . "/" . $getdataexamcenter->ecenter10 . "/");
            $path2 = public_path("files/reports/" . $current_folder_year . "/" . $examcentermaterial . "/" . $stream . "/12/" . $districtname . "/" . $getdataexamcenter->ecenter12 . "/");
            $path3 = public_path("files/reports/" . $current_folder_year . "/" . $aicentermaterial . "/" . $stream . "/10/" . $sicodedistrictname . "/" . $master->ai_code . "/");
            $path4 = public_path("files/reports/" . $current_folder_year . "/" . $aicentermaterial . "/" . $stream . "/12/" . $sicodedistrictname . "/" . $master->ai_code . "/");
            $movefolder1 = File::deleteDirectory($path1);
            $movefolder2 = File::deleteDirectory($path2);
            $movefolder3 = File::deleteDirectory($path3);
            $movefolder4 = File::deleteDirectory($path4);

            $datacondtions = array(
                'stream' => $master->stream,
                'aicode' => $master->ai_code,
                'exam_year' => $current_admission_session_id
            );
            $aicenter_all_student_mapped = AicenterSittingMapped::where($datacondtions)->first();
            if (@$aicenter_all_student_mapped) {
                $objAicenterSittingMapped = AicenterSittingMapped::where("id", "=", $aicenter_all_student_mapped->id);
                // $objAicenterSittingMapped->delete();
                $objAicenterSittingMapped->forceDelete();
            }
            $objCenterAllotment = CenterAllotment::where("id", "=", $id);
            // $objCenterAllotment->delete();
            $objCenterAllotment->forceDelete();
            $responose["msg"] = "Deleted.";


        }
        return response()->json($responose);
    }

    public function getStudentsCountForExamcenter(Request $request)
    {
        $aicenter = $request->aicenter;
        $stream = $request->stream;
        $finalData = array();
        if (isset($aicenter) && $aicenter != '' && $stream && $stream != '') {
            $custom_component_obj = new CustomComponent;
            $finalData = $custom_component_obj->_getStudentsCountForExamcenter($aicenter, $stream);
        }
        // @dd($finalData);
        return response()->json($finalData);
    }

    public function practicalexaminer_destroy(Request $request)
    {
        $e_id = $request->id;
        $user_id = Crypt::decrypt($e_id);

        $e_user_deo_id = $request->deoId;
        $user_deo_id = Crypt::decrypt($e_user_deo_id);

        // $user = User::where('id',$id)->delete();
        $user_practical_examiner = UserPracticalExaminer::where('user_id', $user_id)->where('user_deo_id', $user_deo_id)->delete();

        $practicalexaminerrole = config("global.practicalexaminer");
        $user = ModelHasRole::where('model_id', $user_id)->where('role_id', $practicalexaminerrole)->delete();

        return response()->json(['success' => 'Record successfully Deleted']);
    }

    public function deo_destroy(Request $request)
    {
        $e_id = $request->id;
        $id = Crypt::decrypt($e_id);
        $deoRole = config("global.deo");
        $user = ModelHasRole::where('model_id', $id)->where('role_id', $deoRole)->delete();
        return response()->json(['success' => 'Record successfully Deleted']);
    }

    public function studentdetailsupdate(Request $request)
    {
        $id = Crypt::decrypt($request->estudent_id);
        $user = Student::where('id', $id)->update(['exam_month' => $request->value, 'stream' => $request->value]);
        return $user;
    }

    public function previousqualificationget(Request $request)
    {

        $result = DB::table('master_previous_qualifications')->where('course', $request->course)
            ->where('adm_type', $request->adm_type)->where('stream', $request->stream)
            ->first('previous_qualification_name');


        $result = explode(",", @$result->previous_qualification_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);

        $finalArr = array();
        foreach (@$result as $v) {
            $finalArr[$v] = @$pre_qualifi[$v];
        }
        return response()->json($finalArr);
    }

    public function getdisadvantagegroup(Request $request)
    {
        $gender_id = $request->gender_id;
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);
        $dis_adv_group = $dis_adv_group->toArray();
        if ($gender_id != 2) {
            unset($dis_adv_group[4]);
        }

        $role_id = Session::get('role_id');
        $studentrole = Config::get("global.student");
        if ($studentrole == $role_id) {
            $studentdata = Auth::guard('student')->user();
            if (@$studentdata->is_dgs && $studentdata->is_dgs == 1) {
                unset($dis_adv_group[1]);
                unset($dis_adv_group[2]);
                unset($dis_adv_group[3]);
            }
        }
        return response()->json($dis_adv_group);
    }

    public function ajaxPersonaladmtype(Request $request)
    {
        if ($request->stream == 1) {
            $adm_types = DB::table('masters')->where('combo_name', 'adm_type')->pluck('option_val', 'option_id');
        } elseif ($request->stream == 2) {
            $adm_types = DB::table('masters')->where('combo_name', 'adm_type')
                ->where('id', '3')->pluck('option_val', 'option_id');
        }
        return $adm_types;
    }

    public function ajaxCourseExamcenters(Request $request)
    {
        $conditions = array();
        $conditions['student_allotments.course'] = $request->course;

        $role_id = Session::get('role_id');
        $deoRole = config("global.deo");
        if ($role_id == $deoRole) {
            $examiner_district_id = @Auth::user()->district_id;
            $conditions['examcenter_details.district_id'] = $examiner_district_id;
        }

        $data = ExamcenterDetail::Join('student_allotments', 'student_allotments.examcenter_detail_id', '=', 'examcenter_details.id')->select('examcenter_details.id', 'examcenter_details.cent_name', 'examcenter_details.ecenter10', 'examcenter_details.ecenter12')->where($conditions)->groupBy('examcenter_details.id')->orderBy('examcenter_details.id', 'ASC')->get();
        $result = array();
        foreach ($data as $k => $v) {
            if ($request->course == 10) {
                $result[$v->id] = $v->ecenter10 . '-' . $v->cent_name;
            }
            if ($request->course == 12) {
                $result[$v->id] = $v->ecenter12 . '-' . $v->cent_name;
            }
        }
        return $result;
    }

    public function ajaxCoursesubjects(Request $request)
    {

        $subjects = array();
        if (!empty($request->course)) {
            $subjects = $this->getSubjectByCoursePracticalTheory($request->course, 1);
            //change by lokendar
            //$subjects = $this->getSubjectByCoursePracticalTheory($request->course);
        }
        return $subjects;
    }


    public function ajaxCoursesubjectsfixcode(Request $request)
    {
        $subjects = array();
        if (!empty($request->course)) {
            $subjects = DB::table('subjects')->whereNull('deleted_at')->where('course', $request->course)->pluck('name', 'subject_code');
        }
        return $subjects;
    }

    public function ajaxCourseExamcentersfixcode(Request $request)
    {
        $examcenter_details = array();
        if (!empty($request->course)) {
            $centercode = 'ecenter' . $request->course;
            $examcenter_details = DB::table('examcenter_details')->orderBy('id', 'ASC')->pluck('cent_name', $centercode);
            foreach ($examcenter_details as $k => $v) {
                $result[$k] = $k . '-' . $v;
            }
        }
        return $result;
    }


    public function ajaxexamcentercode(Request $request)
    {
        $examcenter = array();
        $centercode = 'ecenter' . $request->course;
        $centercodefull = 'full_name_ecenter' . $request->course . '_code';
        $examcenterrole = Config::get("global.Examcenter");
        $conditions = array();
        $user_role = Session::get('role_id');
        $user_id = @Auth::user()->id;
        if (!empty($request->district)) {
            $conditions["examcenter_details.district_id"] = @$request->district;
            if ($user_role == $examcenterrole) {
                $conditions["examcenter_details.user_id"] = @$user_id;
            }
            $examcenter = ExamcenterDetail::
            where($conditions)
                ->get()
                ->pluck($centercodefull, $centercode);
        }
        return $examcenter;
    }


    public function ajaxPersonalborad(Request $request)
    {
        $boards = $this->getAdmissionTypeBords($request->admtype);
        return $boards;
    }

    public function getmonthlabel(Request $request)
    {
        $inputs = $request->all();
        $responose = false;
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $value = $request->value;
        if (@$exam_session[$value]) {
            $responose = @$exam_session[$value];
        }
        return response()->json($responose);
    }

    public function ajaxIsVerifyTocEnrollemnt(Request $request)
    {

        $result = 0;
        $inputs = $request->all();
        if (isset($inputs['toc_roll_no']) && $inputs['toc_roll_no'] != '' && $inputs['course'] != '') {
            $result_count = Student::where('enrollment', $inputs['toc_roll_no'])->where('course', $inputs['course'])->count();
            if (isset($result_count) && $result_count > 0) {
                $result = 1;
            }
        }

        if ($result == 0 && isset($inputs['toc_roll_no']) && $inputs['toc_roll_no'] != '' && $inputs['course'] != '') {
            $result_count = Pastdata::where('ENROLLNO', $inputs['toc_roll_no'])->where('CLASS', $inputs['course'])->count();
            if (isset($result_count) && $result_count > 0) {
                $result = 1;
            }
        }
        return $result;
    }

    public function ajaxUserDetilasValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->UserDetailsValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);
    }

    public function ajaxSuppFindEnrollmentValidation(Request $request)
    {
        $finalData = array();
        if (count($request->all()) > 0) {
            $custom_component_obj = new CustomComponent;
            $finalData = $custom_component_obj->suppFindEnrollmentValidation($request);
        }
        return response()->json($finalData);
    }

    public function get_subjects(Request $request)
    {
        ;
        $course = $request->id;
        if (!empty($course)) {
            $subjects = $this->subjectList($course);
        } else {
            $subjects = $this->subjectList();
        }
        return $subjects;
    }


    public function soft_deleted(Request $request)
    {
        $obj_controller = new Controller();
        $tablename = 'exam_subjects';
        $form_type = 'updateresult';
        $id = $request->id;
        $data = DB::table('exam_subjects')->where('id', $id)->first();
        $provisionalConditions = ['student_id' => @$data->student_id,
            'exam_year' => @$data->exam_year, 'exam_month' => @$data->exam_month, 'subject_id' => @$data->subject_id];
        if (empty($data->deleted_at)) {
            $exam_subject_log = $obj_controller->updateStudentLog($tablename, $id, $form_type);
            $data = date("Y-m-d h:i:s");
            $donor = DB::table('exam_subjects')->where('id', $id)->update(['deleted_at' => $data]);
            DB::table('provisional_exam_subjects')->where($provisionalConditions)->update(['deleted_at' => $data]);
            return true;
        } else {
            $data = NULL;
            $exam_subject_log = $obj_controller->updateStudentLog($tablename, $id, $form_type);
            $donor = DB::table('exam_subjects')->where('id', $id)->update(['deleted_at' => $data]);
            DB::table('provisional_exam_subjects')->where($provisionalConditions)->update(['deleted_at' => $data]);
            return true;

        }
    }

    public function ajaxGetSSOIDDetials(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        if (count($request->all()) > 0) {
            $sso_id = $request->sso_id;
            $response = $custom_component_obj->getSSOIDDetials($sso_id);
            return $response;
        }
    }

    public function ajaxMappingExaminerValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->MappingExaminerValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);
    }

    public function get_appearing_student_count(Request $request)
    {
        $theory_custom_component_obj = new ThoeryCustomComponent;
        if (!empty($request->all())) {
            $examcenter_detail_id = $request->exam_center_id;
            $course_id = $request->course_id;
            $subject_id = $request->subjects_id;
            $result = count($theory_custom_component_obj->get_appearing_student_listing($course_id, $subject_id, $examcenter_detail_id));
            return $result;
        }
    }

    public function getSSOIDDetialsByMappingExaminerTbl(Request $request)
    {
        $result = array();
        $practicalexaminer = Config::get('global.practicalexaminer');
        $current_admission_session_id = Config::get("global.current_admission_session_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $current_stream_id = Config::get("defaultStreamId");

        if (!empty(@$request->sso_id)) {
            $ssoid = $request->sso_id;
            $isCheckAllRoles = $request->isCheckAllRoles;
            // $isCheckAllRoles = 1;
        }

        if (!empty($ssoid)) {
            if ($isCheckAllRoles) {
                $examinerData = User::where('ssoid', '=', $ssoid)->first();
            } else {
                $examinerData = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                    ->where('users.ssoid', '=', $ssoid)
                    ->where('model_has_roles.role_id', '=', $practicalexaminer)
                    ->where('users.exam_year', $current_admission_session_id)
                    ->where('users.exam_month', $current_exam_month_id)
                    ->first();
            }

            if (isset($examinerData->id) && !empty($examinerData->id)) {
                $result['status'] = true;
                $result['id'] = $examinerData->id;
                $result['name'] = $examinerData->name;
                $result['mobile'] = $examinerData->mobile;
                $result['email'] = $examinerData->email;
            } else {
                $result['status'] = false;
            }
        }
        return $result;
    }

    public function getDataMarkingAbsentStudent(Request $request)
    {
        $theory_custom_component_obj = new ThoeryCustomComponent;
        $Result = null;
        $Result = $theory_custom_component_obj->getMarkingAbsentStudent($request->course_id, $request->exam_center_id, $request->subjects_id);
        if (!empty($Result)) {
            return $Result;
        } else {
            return true;
        }
    }

    public function ajaxAllotingCopiesExaminerValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->AllotingCopiesExaminerValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        if (@$finalErrors) {
            $customerrors = implode(",", $finalErrors);
        }
        $output = array('isValid' => $isValid, 'errors' => $customerrors);
        return response()->json($output);
    }

    public function ajaxPracticalExaminerValidation(Request $request)
    {
        if ($request->ajax()) {
            $isValid = false;
            $error = null;
            $data = null;

            if (count($request->all()) > 0) {
                $PracticalExaminerObj = new UserExaminerMap;
                $inputs = $request->all();

                $validator = Validator::make($inputs, $PracticalExaminerObj->practicalExaminerValidation);
                if ($validator->fails()) {
                    $error = $validator->getMessageBag()->toArray();
                } else {
                    $isValid = true;
                }

                $practicalCustomComponent = new practicalCustomComponent();
                $student_exist = $practicalCustomComponent->getPracticalStudentList($request->examcenter_detail, $request->subject);
                $tempArr = $student_exist->toArray();

                if (isset($tempArr['total'])) {
                    if ($tempArr['total'] <= 0) {
                        //return redirect()->back()->with('error', 'Failed! Student not found in given combination.');
                        $fld = 'course';
                        $errMsg = 'Failed! Student not found as per selected combination.';

                        $validator->getMessageBag()->add($fld, $errMsg);
                        $error = $validator->getMessageBag()->toArray();
                        $isValid = false;
                    }
                }

                $practical_custom_component_obj = new PracticalCustomComponent;
                $alreadyMappingExist = $practical_custom_component_obj->getAlreadyExaminerMapping($request->examcenter_detail, $request->course, $request->subject);
                if (isset($alreadyMappingExist) && $alreadyMappingExist == true) {
                    $fld = 'course';
                    $errMsg = 'Same subject already mapped on same exam center.';
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $error = $validator->getMessageBag()->toArray();
                    $isValid = false;
                }
            }
            $output = array('isValid' => $isValid, 'error' => $error, 'data' => $data);
            return response()->json($output);
        } else {
            $output = array('isValid' => true, 'error' => $error, 'data' => 'else condtion');
            return response()->json($output);
        }
    }

    public function ajaxAddPracticalValidation(Request $request)
    {
        $isValid = false;
        $error = null;
        $data = null;

        if ($request->ajax()) {
            if (count($request->all()) > 0) {
                $inputs = $request->all();
                $User = new User; /// create model object
                $validator = Validator::make($inputs, $User->rules);
                if ($validator->fails()) {
                    $error = $validator->getMessageBag()->toArray();
                } else {
                    $isValid = true;
                }
            }

            $output = array('isValid' => $isValid, 'error' => $error, 'data' => $data);
            return response()->json($output);
        } else {
            $output = array('isValid' => true, 'error' => $error, 'data' => 'else condtion');
            return response()->json($output);
        }
    }

    public function ajaxEditPracticalValidation(Request $request)
    {
        $isValid = false;
        $error = null;
        $data = null;

        if ($request->ajax()) {
            if (count($request->all()) > 0) {
                $inputs = $request->all();
                $User = new User; /// create model object
                $validator = Validator::make($inputs, $User->rules);
                if ($validator->fails()) {
                    $error = $validator->getMessageBag()->toArray();
                } else {
                    $isValid = true;
                }
            }

            $output = array('isValid' => $isValid, 'error' => $error, 'data' => $data);
            return response()->json($output);
        } else {
            $output = array('isValid' => true, 'error' => $error, 'data' => 'else condtion');
            return response()->json($output);
        }
    }

    public function ajaxAddDeoValidation(Request $request)
    {
        $isValid = false;
        $error = null;
        $data = null;

        if ($request->ajax()) {
            if (count($request->all()) > 0) {
                $inputs = $request->all();
                $User = new User;
                $validator = Validator::make($inputs, $User->deoCreaterules);
                if ($validator->fails()) {
                    $error = $validator->getMessageBag()->toArray();
                } else {
                    $isValid = true;
                }
            }

            $output = array('isValid' => $isValid, 'error' => $error, 'data' => $data);
            return response()->json($output);
        } else {
            $output = array('isValid' => true, 'error' => $error, 'data' => 'else condtion');
            return response()->json($output);
        }
    }

    public function ajaxEditDeoValidation(Request $request)
    {
        $isValid = false;
        $error = null;
        $data = null;

        if ($request->ajax()) {
            if (count($request->all()) > 0) {
                $inputs = $request->all();
                $User = new User;
                $validator = Validator::make($inputs, $User->deoUpdaterules);
                if ($validator->fails()) {
                    $error = $validator->getMessageBag()->toArray();
                } else {
                    $isValid = true;
                }
            }

            $output = array('isValid' => $isValid, 'error' => $error, 'data' => $data);
            return response()->json($output);
        } else {
            $output = array('isValid' => true, 'error' => $error, 'data' => 'else condtion');
            return response()->json($output);
        }
    }

    public function ajaxMarkingAbsentValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $isValid = true;

            $responses = $this->markingAbsentStudentValidation($request);

            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }

        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }
        if (@$finalErrors) {
            $customerrors = implode(",", $finalErrors);
        }

        $output = array('isValid' => $isValid, 'errors' => $customerrors);

        return response()->json($output);
    }

    public function ajaxPracticalValidation(Request $request)
    {
        $response = array();
        $practicalCustomComponent = new practicalCustomComponent();
        if (count($request->all()) > 0) {
            $isValid = true;
            $subjectMinMarks = crypt::decrypt($request->min_marks);
            $subjectMinMarks = 0;
            $subjectMaxMarks = crypt::decrypt($request->max_marks);
            $response = $practicalCustomComponent->isValidPracticalMarks($request, $subjectMinMarks, $subjectMaxMarks);
        }
        return $response;
    }

    public function getSSOIDDetialsByTheoryMappingTbl(Request $request)
    {
        $result = array();
        $theoryexaminer = Config::get('global.theoryexaminer');

        if (!empty(@$request->sso_id)) {
            $ssoid = $request->sso_id;
            $isCheckAllRoles = $request->isCheckAllRoles;
        } else if (!empty(@$request->ssoid)) {
            $ssoid = $request->ssoid;
            $isCheckAllRoles = 1;
        }
        if (!empty($ssoid)) {
            if ($isCheckAllRoles) {
                // $conditions=['ssoid'=>$ssoid,'exam_year'=>Config::get('global.current_admission_session_id'),'exam_month'=>Config::get('global.current_exam_month_id')];
                $conditions = ['ssoid' => $ssoid];
                $examinerData = User::where($conditions)->first();
            }

            if (isset($examinerData->id) && !empty($examinerData->id)) {
                $result['status'] = true;
                $result['id'] = $examinerData->id;
                $result['ssoid'] = $examinerData->ssoid;
                $result['name'] = $examinerData->name;
                $result['mobile'] = $examinerData->mobile;
                $result['email'] = $examinerData->email;
            } else {
                $result['status'] = false;
            }
        }
        return $result;
    }

    public function getTheoryExaminer(Request $request)
    {

        if (!empty($request->sso_id)) {
            $field = ['users.name', 'users.mobile', 'users.id'];
            $conditions = ['ssoid' => $request->sso_id];
            $examinerData = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                ->where($conditions)
                ->whereNull('users.Deleted_at')
                ->where('model_has_roles.role_id', '=', 62)
                ->first($field);

            if (!empty($examinerData)) {

                $result['name'] = $examinerData->name;
                $result['mobile'] = $examinerData->mobile;
                $result['id'] = $examinerData->id;
                return $result;
            } else {
                return true;
            }
        }
    }

    public function ajaxMarkSubmmisionsValidation(Request $request)
    {
        $response = array();

        $theorycustomcomponent = new ThoeryCustomComponent();
        if (count($request->all()) > 0) {
            $svdata = $request->all();


            $isValid = true;
            $subjectMinMarks = $request->min_marks;
            $subjectMaxMarks = crypt::decrypt($request->max_marks);
            $response = $theorycustomcomponent->isvalidtheorymarks($svdata, $subjectMaxMarks, $subjectMinMarks);

        }
        return $response;
    }


    public function getDeoListByDistrictId(Request $request)
    {
        $district_id = $request->id;
        $selected_session = CustomHelper::_get_selected_sessions();
        $current_exam_month_id = Config::get('global.current_exam_month_id');
        $practicalexaminer = Config::get('global.practicalexaminer');
        $deoRole = config("global.deo");

        $conditions = array();
        $conditions['users.exam_year'] = $selected_session;
        $conditions['users.exam_month'] = $current_exam_month_id;
        $conditions['users.district_id'] = $district_id;
        $conditions['model_has_roles.role_id'] = $deoRole;
        // $conditions['model_has_roles.model_type'] = 'App\Models\User';
        $deo_user_list = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->where($conditions)
            ->pluck('name', 'id');
        return $deo_user_list;
    }


    public function getallotssoid(Request $request)
    {
        $theory_custom_component_obj = new ThoeryCustomComponent;
        $Result = null;
        $Result = $theory_custom_component_obj->getallotssoid($request->exam_center_id, $request->course_id, $request->subjects_id);
        if (!empty($Result)) {
            return $Result;
        } else {
            return true;
        }
    }


    public function checkMarkingAbsentdata(Request $request)
    {
        $theory_custom_component_obj = new ThoeryCustomComponent;
        $result = $theory_custom_component_obj->getMarkingAbsentStudent($request->course_id, $request->exam_center_id, $request->subjects_id);
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    public function reloadCaptcha()
    {
        return response()->json(['captcha' => captcha_img()]);
    }

    public function pastDataUpdataValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->UpdatePastdataValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);

    }

    public function updateStudentDetailsPrintValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();
        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->updateStudentDetailPrintValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];
                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }
        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);

    }

    public function checkstudentpastdata(Request $request)
    {
        $data = false;
        $custom_component_obj = new CustomComponent;
        $marksheet_component_obj = new MarksheetCustomComponent;
        $students = ExamResult::where('enrollment', '=', $request->enrollment)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
        $pastdata = $marksheet_component_obj->getpastdata($request->enrollment);
        if (!empty($students) || !empty($pastdata)) {
            $data = true;
        } else {
            $data = false;
        }

        return $data;
    }

    public function searchstudentdata(Request $request)
    {
        $data = false;
        $custom_component_obj = new CustomComponent;
        $marksheet_component_obj = new MarksheetCustomComponent;

        $student = $marksheet_component_obj->getstudentdata($request->enrollment);
        if (!empty($student)) {
            $data = true;
        } else {
            $data = false;
        }

        return $data;
    }

    public function getstudentexamsubjectdata(Request $request)
    {
        $data = false;

        $custom_component_obj = new CustomComponent;
        $marksheet_component_obj = new MarksheetCustomComponent;
        $student = $marksheet_component_obj->getexamsubjectsdata($request->enrollment);

        if (count($student) > 0) {
            $data = true;
        } else {
            $data = false;
        }

        return $data;
    }

    public function finalresultupdatevalidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();
        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->finalsresultupdatevalidation($request);
            $responseFinal = null;

            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);
    }

    public function updateStudentSubjectsDataValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();
        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->studentSubjectsDataValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);
    }

    public function AddsubjectValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();
        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->studentaddsubjectValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);
    }

    public function getstudentsubjectdata(Request $request)
    {
        $conditions = ['subject_id' => $request->subject_id, 'student_id' => $request->student_id];
        $data = DB::table('exam_subjects')->whereNull('deleted_at')->where($conditions)->count();
        // dd($data);
        if ($data == 0) {
            return 1;
        } else {
            return 0;
        }

    }


    public function ajaxAicenterDetilasValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->AicnterDetailsValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);
    }

    public function ajaxMyProfileAicenterDetilasValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->MyProfileAicnterDetailsValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);
    }

    public function checkreviedresultstudent(Request $request)
    {
        dd('hii');
        $data = false;
        $custom_component_obj = new CustomComponent;
        $checkCaptchaStatus = $custom_component_obj->checkCaptcha($request);
        if ($checkCaptchaStatus == false) {
            $data = 'captchaFalse';
        }

        if ($data != 'captchaFalse') {
            $students = $custom_component_obj->getrevisedresultstudentdata($request->enrollment, $request->dob);
            if (!empty($students)) {
                $data = true;
            } else {
                $data = false;
            }
        }
        return $data;
    }

    public function ajaxqueryValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->queryDetailsValidation($request);

            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }
        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);
    }


    public function pastSubjectDataUpdataValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->UpdatePastsubjectdataValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);

    }

    public function ajaxsubjectsDetilasValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->subjectsDetailsValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);
    }

    public function htmlGrpahicalData()
    {
        $data = null;
        $data = $this->grpahicalGetApplicationdata();
        return view('elements.graphical.box')->with('data', json_decode($data, true));;
    }

    public function grpahicalGetApplicationdata()
    {
        $applicationCount = null;
        $custom_component_obj = new CustomComponent;
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $current_session = CustomHelper::_get_selected_sessions();
        $combo_name = 'application_dashboard';
        $application_dashboard = $this->master_details($combo_name);
        /* Start */
        $tempIndex = 'total';
        $applicationCount[$tempIndex]['status'] = $application_dashboard[3];
        $applicationCount[$tempIndex]['label'] = "Applications(" . $admission_sessions["$current_session"] . ')';
        $applicationCount[$tempIndex]['total_registered_student']['lable'] = 'Total Generated Applications';
        $applicationCount[$tempIndex]['total_registered_student']['status'] = true;
        $applicationCount[$tempIndex]['total_registered_student']['matarical_icon'] = 'material-icons background-round mt-5';
        $applicationCount[$tempIndex]['total_registered_student']['url'] = route("student_applications");
        $applicationCount[$tempIndex]['total_registered_student']['bgcolor'] = "gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box";
        $applicationCount[$tempIndex]['total_registered_student']['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['total_registered_student']['value'] = 0;

        $applicationCount[$tempIndex]['total_lock_Submit_student']['label'] = "Lock & Submitted Applications";
        $applicationCount[$tempIndex]['total_lock_Submit_student']['status'] = true;
        $applicationCount[$tempIndex]['total_lock_Submit_student']['matarical_icon'] = "material-icons background-round mt-5";
        $applicationCount[$tempIndex]['total_lock_Submit_student']['url'] = route('allstudent_locksumbited');
        $applicationCount[$tempIndex]['total_lock_Submit_student']['bgcolor'] = "gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box";
        $applicationCount[$tempIndex]['total_lock_Submit_student']['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['total_lock_Submit_student']['value'] = 0;

        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['label'] = "Fee Not Paid Applications";
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['status'] = true;
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['matarical_icon'] = "material-icons background-round mt-5";
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['url'] = route('allstudent_not_pay_details');
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['bgcolor'] = "gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box";
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['value'] = 0;


        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count']['label'] = "Zero(0)Fee Paid Applications";
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count']['status'] = true;
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count']['matarical_icon'] = "material-icons background-round mt-5";
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count']['url'] = route('allstudent_zero_fees_pay_details');
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count']['bgcolor'] = "card white-text animate fadeLeft dashboard-link-box";
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count']['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count']['value'] = 0;


        $applicationCount[$tempIndex]['get_Student_payment_Count']['lable'] = "Fee Paid Applications";
        $applicationCount[$tempIndex]['get_Student_payment_Count']['status'] = true;
        $applicationCount[$tempIndex]['get_Student_payment_Count']['url'] = route('allstudent_payment_details');
        $applicationCount[$tempIndex]['get_Student_payment_Count']['bgcolor'] = "card white-text animate fadeLeft dashboard-link-box";
        $applicationCount[$tempIndex]['get_Student_payment_Count']['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['get_Student_payment_Count']['value'] = 0;

        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count']['lable'] = "Eligable Students";
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count']['status'] = true;
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count']['matarical_icon'] = "material-icons background-round mt-5";
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count']['url'] = route('student_applications', ['is_eligible' => 1]);
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count']['bgcolor'] = "card white-text animate fadeLeft dashboard-link-box";
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count']['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count']['value'] = 0;


        $tempIndex = 2;
        $applicationCount[$tempIndex]['status'] = $application_dashboard["$tempIndex"];
        $applicationCount[$tempIndex]['label'] = "Applications(" . $admission_sessions["$current_session"] . ' ' . $exam_month["$tempIndex"] . ')';
        $applicationCount[$tempIndex]['total_registered_student']['lable'] = 'Total Generated Applications';
        $applicationCount[$tempIndex]['total_registered_student']['status'] = true;
        $applicationCount[$tempIndex]['total_registered_student']['url'] = route('student_applications', ['exam_month' => $tempIndex]);
        $applicationCount[$tempIndex]['total_registered_student']['bgcolor'] = "gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box";
        $applicationCount[$tempIndex]['total_registered_student']['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['total_registered_student']['matarical_icon'] = 'material-icons background-round mt-5';
        $applicationCount[$tempIndex]['total_registered_student']['value'] = 0;

        $applicationCount[$tempIndex]['total_lock_Submit_student']['lable'] = "Lock & Submitted Applications";
        $applicationCount[$tempIndex]['total_lock_Submit_student']['status'] = true;
        $applicationCount[$tempIndex]['total_lock_Submit_student']['matarical_icon'] = "material-icons background-round mt-5";
        $applicationCount[$tempIndex]['total_lock_Submit_student']['url'] = route('allstudent_locksumbited', ['exam_month' => $tempIndex]);
        $applicationCount[$tempIndex]['total_lock_Submit_student']['bgcolor'] = "gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box";;
        $applicationCount[$tempIndex]['total_lock_Submit_student']['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['total_lock_Submit_student']['value'] = 0;


        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['lable'] = "Fee Not Paid Applications";
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['status'] = true;
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['matarical_icon'] = "material-icons background-round mt-5";
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['url'] = route('allstudent_not_pay_details', ['exam_month' => $tempIndex]);
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['bgcolor'] = "gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box";;
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['value'] = 0;

        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count'] ['lable'] = "Zero(0)Fee Paid Applications";
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count'] ['status'] = true;
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count']['matarical_icon'] = "material-icons background-round mt-5";
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count'] ['url'] = route('allstudent_zero_fees_pay_details', ['exam_month' => $tempIndex]);
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count'] ['bgcolor'] = "card white-text animate fadeLeft dashboard-link-box";;
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count'] ['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count'] ['value'] = 0;

        $applicationCount[$tempIndex]['get_Student_payment_Count']['lable'] = "Fee Paid Applications";
        $applicationCount[$tempIndex]['get_Student_payment_Count']['status'] = true;
        $applicationCount[$tempIndex]['get_Student_payment_Count']['matarical_icon'] = "material-icons background-round mt-5";
        $applicationCount[$tempIndex]['get_Student_payment_Count']['url'] = route('allstudent_payment_details', ['exam_month' => $tempIndex]);
        $applicationCount[$tempIndex]['get_Student_payment_Count']['bgcolor'] = "card white-text animate fadeLeft dashboard-link-box";
        $applicationCount[$tempIndex]['get_Student_payment_Count']['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['get_Student_payment_Count']['value'] = 0;

        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count'] ['lable'] = "Eligable Students";
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count'] ['status'] = true;
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count']['matarical_icon'] = "material-icons background-round mt-5";
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count'] ['url'] = route('student_applications', ['is_eligible' => 1, 'exam_month' => $tempIndex]);
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count'] ['bgcolor'] = "card white-text animate fadeLeft dashboard-link-box";
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count'] ['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count'] ['value'] = 0;


        $tempIndex = 1;
        $applicationCount[$tempIndex]['status'] = $application_dashboard["$tempIndex"];
        $applicationCount[$tempIndex]['label'] = "Applications(" . $admission_sessions["$current_session"] . ' ' . $exam_month["$tempIndex"] . ')';
        $applicationCount[$tempIndex]['total_registered_student']['lable'] = 'Total Generated Applications';
        $applicationCount[$tempIndex]['total_registered_student']['status'] = true;
        $applicationCount[$tempIndex]['total_registered_student']['url'] = route('student_applications', ['exam_month' => $tempIndex]);
        $applicationCount[$tempIndex]['total_registered_student']['bgcolor'] = "gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box";
        $applicationCount[$tempIndex]['total_registered_student']['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['total_registered_student']['matarical_icon'] = 'material-icons background-round mt-5';
        $applicationCount[$tempIndex]['total_registered_student']['value'] = 0;

        $applicationCount[$tempIndex]['total_lock_Submit_student']['lable'] = "Lock & Submitted Applications";
        $applicationCount[$tempIndex]['total_lock_Submit_student']['status'] = true;
        $applicationCount[$tempIndex]['total_lock_Submit_student']['matarical_icon'] = "material-icons background-round mt-5";
        $applicationCount[$tempIndex]['total_lock_Submit_student']['url'] = route('allstudent_locksumbited', ['exam_month' => $tempIndex]);
        $applicationCount[$tempIndex]['total_lock_Submit_student']['bgcolor'] = "gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box";;
        $applicationCount[$tempIndex]['total_lock_Submit_student']['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['total_lock_Submit_student']['value'] = 0;


        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['lable'] = "Fee Not Paid Applications";
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['status'] = true;
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['matarical_icon'] = "material-icons background-round mt-5";
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['url'] = route('allstudent_not_pay_details', ['exam_month' => $tempIndex]);
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['bgcolor'] = "gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box";;
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['value'] = 0;

        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count'] ['lable'] = "Zero(0)Fee Paid Applications";
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count'] ['status'] = true;
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count']['matarical_icon'] = "material-icons background-round mt-5";
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count'] ['url'] = route('allstudent_zero_fees_pay_details', ['exam_month' => $tempIndex]);
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count'] ['bgcolor'] = "card white-text animate fadeLeft dashboard-link-box";;
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count'] ['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count'] ['value'] = 0;

        $applicationCount[$tempIndex]['get_Student_payment_Count']['lable'] = "Fee Paid Applications";
        $applicationCount[$tempIndex]['get_Student_payment_Count']['status'] = true;
        $applicationCount[$tempIndex]['get_Student_payment_Count']['matarical_icon'] = "material-icons background-round mt-5";
        $applicationCount[$tempIndex]['get_Student_payment_Count']['url'] = route('allstudent_payment_details', ['exam_month' => $tempIndex]);
        $applicationCount[$tempIndex]['get_Student_payment_Count']['bgcolor'] = "card white-text animate fadeLeft dashboard-link-box";
        $applicationCount[$tempIndex]['get_Student_payment_Count']['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['get_Student_payment_Count']['value'] = 0;

        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count'] ['lable'] = "Eligable Students";
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count'] ['status'] = true;
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count']['matarical_icon'] = "material-icons background-round mt-5";
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count'] ['url'] = route('student_applications', ['is_eligible' => 1, 'exam_month' => $tempIndex]);
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count'] ['bgcolor'] = "card white-text animate fadeLeft dashboard-link-box";
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count'] ['textcolor'] = "white-text";
        $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count'] ['value'] = 0;

        $tempIndex = 1;
        if ($applicationCount[$tempIndex]['status'] == $application_dashboard[1]) {
            $applicationCount[$tempIndex]['total_registered_student']['value'] = $custom_component_obj->getallStudentCount(null, $tempIndex);
            $applicationCount[$tempIndex]['total_lock_Submit_student']['value'] = $custom_component_obj->getallStudentLockSubmitCount(null, $tempIndex);
            $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['value'] = $custom_component_obj->getallStudentpaymentnotpayCount(null, $tempIndex);
            $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count'] ['value'] = $custom_component_obj->getallStudentzerofeespaymentCount(null, $tempIndex);
            $applicationCount[$tempIndex]['get_Student_payment_Count']['value'] = $custom_component_obj->getallStudentpaymentCount(null, $tempIndex);
            $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count'] ['value'] = $custom_component_obj->getallStudentEligibleCount(null, $tempIndex);
        }
        $tempIndex = 2;
        if ($applicationCount[$tempIndex]['status'] == $application_dashboard[2]) {
            $applicationCount[$tempIndex]['total_registered_student']['value'] = $custom_component_obj->getallStudentCount(null, $tempIndex);
            $applicationCount[$tempIndex]['total_lock_Submit_student']['value'] = $custom_component_obj->getallStudentLockSubmitCount(null, $tempIndex);
            $applicationCount[$tempIndex]['get_Student_payment_not_pay_Count']['value'] = $custom_component_obj->getallStudentpaymentnotpayCount(null, $tempIndex);
            $applicationCount[$tempIndex]['get_Student_zero_fees_payment_Count'] ['value'] = $custom_component_obj->getallStudentzerofeespaymentCount(null, $tempIndex);
            $applicationCount[$tempIndex]['get_Student_payment_Count']['value'] = $custom_component_obj->getallStudentpaymentCount(null, $tempIndex);
            $applicationCount[$tempIndex]['eligible_get_Student_payment_not_pay_Count'] ['value'] = $custom_component_obj->getallStudentEligibleCount(null, $tempIndex);
        }
        if ($applicationCount['total']['status'] == $application_dashboard[3]) {
            $fld = "total_registered_student";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "total_lock_Submit_student";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_payment_not_pay_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_zero_fees_payment_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_payment_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "eligible_get_Student_payment_not_pay_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
        }
        $status = true;
        $error = false;
        $output = array('status' => $status, 'error' => $error, 'data' => $applicationCount);
        // return json_encode($output);
        return response()->json($output);

    }

    public function ajaxBooksRequrementDetilasValidation(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->BooksRequrementDetailsValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {

                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);

        return response()->json($output);
    }


    public function checkPublishBookdata(Request $request)
    {
        $book_custom_component_obj = new BookRequirementCustomComponent;
        $result = $book_custom_component_obj->bookDataAllReadyExists(@$request->course, @$request->subject_id, @$request->ai_code, null, @$request->volume_id, @$request->publicbookid);


        if (count($result) != 0) {

            return true;
        } else {

            return false;
        }
    }

    public function expectedstudentcountdata(Request $request)
    {
        $current_exam_year = Config::get("global.current_books_requirement_exam_year");
        $current_exam_month = Config::get("global.current_books_requirement_exam_month");

        $result['hindi'] = DB::table('students')->join('exam_subjects', 'exam_subjects.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')->where('students.is_eligible', 1)->where('students.exam_year', $current_exam_year)->where('students.ai_code', $request->aicode)->
        where('students.exam_month', $current_exam_month)->where('students.course', $request->course)->where('applications.medium', 1)->where('exam_subjects.subject_id', $request->subject)->whereNull('exam_subjects.deleted_at')->whereNull('students.deleted_at')->whereNull('applications.deleted_at')->count();

        $result['engilsh'] = DB::table('students')->join('exam_subjects', 'exam_subjects.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')->where('students.is_eligible', 1)->where('students.exam_year', $current_exam_year)->where('students.ai_code', $request->aicode)->
        where('students.exam_month', $current_exam_month)->where('students.course', $request->course)->where('applications.medium', 2)->where('exam_subjects.subject_id', $request->subject)->whereNull('exam_subjects.deleted_at')->whereNull('students.deleted_at')->whereNull('applications.deleted_at')->count();
        $subject_id = $request->subject;
        $combo_name = "book_publication_volumes";
        $condtions = ['status' => 1, 'combo_name' => $combo_name];
        $mainTable = "masters";
        if (!empty($subject_id)) {
            $condition = ['subject_id' => $subject_id];
            $volumedata = BookVolumeMaster::where($condition)->pluck('medium', 'volume');
            if ($volumedata->count() != 0) {
                $volume = array_keys($volumedata->toArray());
                $result['medium'] = implode(',', array_unique(array_values($volumedata->toArray())));
                $result['volumes'] = DB::table($mainTable)->where($condtions)->whereIn('option_id', $volume)->orderBy("option_val")->get()->pluck('option_val', 'option_id');
            }
        }
        return response()->json($result);
    }

    public function setPagntorValue(Request $request)
    {
        $value = $this->setPagintorPerPageLimit(@$request->value);
        return $value;
    }


    public function ajaxallreadystudentDetilasValidation(Request $request)
    {
        dd('test');
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->AjaxallreadystudentValidation($request);

            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {

                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);

        return response()->json($output);
    }

    public function ajaxUpdateSsoDetilasValidations(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();
        $isValid = true;
        if (count($request->all()) > 0) {

            $responses = $this->ajaxUpdateSsoDetilasValidation($request);

            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {

                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);

        return response()->json($output);
    }

    public function ajaxchecktopdetail(Request $request)
    {
        $ssoid = Crypt::decrypt($request->ssoid);
        $student_id = Crypt::decrypt($request->student);
        $otp = $request->otp;
        if (!empty($otp)) {
            $status = $this->_verifyStudentOTP($student_id, $otp, $ssoid);
            if ($status == false) {
                $response = 'false';
            } elseif ($status == true) {
                $response = 'true';
            }
        } else {
            $response = 'null';
        }
        return $response;
    }

    public function setCountDownTimerDetails(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $removeCountDown = $custom_component_obj->_removeCountDownTimerDetails();
        $setCountDown = $custom_component_obj->_setCountDownTimerDetails();
        return true;
    }

    public function checkcaptchastudent(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $checkCaptchaStatus = $custom_component_obj->checkCaptcha($request);
        if ($checkCaptchaStatus == false) {
            $data = 'false';
        } elseif ($checkCaptchaStatus == true) {
            $data = 'true';
        }
        return $data;
    }

    public function ajaxViewCreateDate(Request $request)
    {
        if (count($request->all()) > 0) {
            $stream_global = config("global.CenterAllotmentStreamId");
            $exam_year = config("global.current_admission_session_id");
            $exam_month = config("global.current_exam_month_id");

            $conditionArray = array();
            $finalData = array(
                "examcenter_detail_id" => $request->examcenterdetailid,
                "center_allotment_id" => $request->centerallotmentid,
                // "ai_code" =>$request->aicode,
                "course" => $request->course,
                "exam_year" => $exam_year,
                "exam_month" => $exam_month,
                "stream" => $request->stream,
                "supplementary" => $request->supp
            );

            $allotmentdata = StudentAllotment::where($finalData)->first();
            $newtime = strtotime($allotmentdata->created_at);;
            $data = date('d-m-Y', $newtime);
            return $data;

        }
    }

    public function send_otp_to_student(Request $request)
    {

        $mobile = $request->mobile;
        $student_id = Crypt::decrypt($request->student_id);
        $master = Student::where('id', $student_id)->first();
        if (@$master->mobile) {
        } else {
            $studentarray['mobile'] = $mobile;
            $Student = Student::where('id', $student_id)->update($studentarray);
        }

        $status = $this->_sendOTPToStudent($student_id);
        $status = true;
        return $status;

    }

    public function _verify_only_mobile_student_otp(Request $request)
    {
        $student_id = Crypt::decrypt($request->student_id);
        $otp = $request->otp;

        $status = $this->_verifyOnlyMobileStudentOTP($student_id, $otp);
        if ($status) {
            $status = true;
        } else {
            $status = '0';
        }
        return $status;

    }

    public function getListBanksIfscCode(Request $request)
    {
        $status = $this->_getListBanksIfscCode($request->bank_id, $request->state_id);
        return $status;
    }

    public function getBankBranchDetails(Request $request)
    {
        $status = $this->_getBankBranchDetails($request->ifsc_code);
        return $status;
        //return json_encode($status);

    }

    public function ajaxDocumentVerificationValidation(Request $request)
    {
        $isValid = false;
        $extra = "";
        $isAllRejected = null;
        $customerrors = array();
        $responses = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {

            $inputs = $request->all();

            $tempInputs = $inputs;

            unset($tempInputs['ajaxRequest']);
            unset($tempInputs['_method']);
            unset($tempInputs['_token']);
            unset($tempInputs['action']);
            $inputInterMids = array();

            foreach (@$tempInputs['upper_level'] as $k => $v) {
                if (@$v && is_array($v) && count($v) > 0) {
                    if (in_array("on", $v)) {
                        $isValid = true;
                        break;
                    }
                }
            }

            if (@$isValid && $isValid == true) {
            } else {
                $extra = "You haven't checked any checkboxes yet. Do you still want to proceed?";
            }
            $isValid = true;
            $customerrorFromModel['documents'] = null;
            if (@$isValid && $isValid == true) {
            } else {
                $customerrorFromModel['documents'] = "Please select at least one field.";
            }
        }

        if (@$customerrorFromModel) {
            $customerrors = implode(",", $customerrorFromModel);
        }
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel, 'extra' => $extra, 'isAllRejected' => @$isAllRejected);

        return response()->json($output);
    }

    public function set_student_multi_enrollment(Request $request)
    {
        if (@$request->selectedVal) {
            Session::put("selected_student_enrollment_by_student_ajax", $request->selectedVal);
            $response = $this->_getCurrentStudentLogoutandLogin();
            return $response;
            //controller call pic the session, current student id i.e. enrollment logout,login selected_student_enrollment_by_student_ajax.
        }
    }


    public function ajaxcreatepracticalexaminerslotValidation(Request $request)
    {
        $table = $model = "RevalStudentSubject";
        $customerrors = array();
        $response = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $response = $this->createpracticalexaminerslotValidation($request);
            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];
        }
        $output = array('isValid' => $isValid, 'error' => $customerrors, 'validator' => $validator);
        return response()->json($output);
    }


    public function ajaxcreatepracticalexaminerslotValidation123(Request $request)
    {
        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->createpracticalexaminerslotValidation123($request);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);
    }

    public function updatePraticalMarks(Request $request)
    {
        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get('global.current_exam_month_id');
        if ($request->all()) {

            if (@$request->absent == 1) {
                $svData = ['final_practical_marks' => null, 'practical_absent' => 1, 'is_practical_lock_submit' => 0];
            } else {

                $svData = ['final_practical_marks' => $request->value, 'practical_absent' => '0', 'is_practical_lock_submit' => 0];
            }

            if (!empty(@$request->id)) {
                $data = StudentAllotmentMark::where('id', $request->id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->update($svData);
            }

        }
        return true;
    }

    public function ajaxrejecteddocumentDetilasValidation(Request $request)
    {
        $status = false;
        $error = null;
        $data = null;
        if ($request->ajax()) {
            $type = Crypt::decrypt($request->type);
            if ($type == 1) {
                $student_id = Session::get('temp_student_id');
                // $student_id = Auth::guard('student')->user()->id;
                // $master = DocumentVerification::where('student_id',$student_id)->orderby("id","desc")->first();
                $super_admin_id = Config::get("global.super_admin_id");

                // $fieldBaseName = "ao_";
                // if($master->role_id == $super_admin_id){
                // 	$fieldBaseName = "dept_";
                // }
                // $finalArr = array();
                // foreach($fields as $k => $field){
                // 	$fName = $fieldBaseName . $field;
                // 	if(@$master->$fName == 2){
                // 		$finalArr[$k] = str_ireplace("_is_verify","",$field);
                // 	}
                // }
                // $documentInput = $this->getStudentRequriedRejectedDocument($master);
                // @$mainFields = $finalArr;

                $getroleid = $master = DocumentVerification::where('student_id', $student_id)->orderby("id", "desc")->first();


                $studentDocumentVerificationDetails = StudentDocumentVerification::where('student_id', $student_id)->where('student_verification_id', @$getroleid->id)->orderby("id", "desc")->first();

                if (@$studentDocumentVerificationDetails && !empty($studentDocumentVerificationDetails)) {
                    $studentDocumentVerificationDetails = $studentDocumentVerificationDetails->toArray();
                }
                //get requried doc list and loop
                $verificationLabels = $this->getVerificationDetailedLabels();


                $fieldBaseName = $fldNameAliase = "ao_";
                if (@$getroleid->role_id == @$super_admin_id) { //Acedmic Department
                    $fieldBaseName = $fldNameAliase = "department_";
                }
                $var_documents_verification = $fldNameAliase . 'documents_verification';
                $var_upper_documents_verification = $fldNameAliase . 'upper_documents_verification';

                $upper_documents_verification = @$getroleid->$var_upper_documents_verification;
                $documents_verification = @$getroleid->$var_documents_verification;

                $upper_documents_verification_arr = json_decode($upper_documents_verification, true);
                $upper_documents_verification_arr = array_filter(@$upper_documents_verification_arr, function ($value) {
                    return $value == 2;
                });
                $isErr = false;
                $upper_documents_verification_arr = $this->_getfreshVerNotRequriedDocInput($upper_documents_verification_arr);
                if (@$studentDocumentVerificationDetails && !empty($studentDocumentVerificationDetails)) {
                    foreach ($upper_documents_verification_arr as $k => $v) {
                        $fieldName = str_replace("doc_", "", $verificationLabels[$k]['name']);

                        /* if(@$studentDocumentVerificationDetails[$fieldName] && $studentDocumentVerificationDetails[$fieldName] == null){
							$isErr = true;
							$status = false;
							$error[$fieldName] =  ucfirst(@$verificationLabels[$k]['hindi_name'])  .  " आवश्यक है";
						}*/

                        if (@$studentDocumentVerificationDetails[$fieldName]) {
                        } else {
                            $isErr = true;
                            $status = false;
                            $error[$fieldName] = ucfirst(@$verificationLabels[$k]['hindi_name']) . " आवश्यक है";
                        }

                    }
                } else {
                    $fieldName = "photograph";
                    $isErr = true;
                    $status = false;
                    $error[$fieldName] = "Please upload requried docuements.";
                }
                if (!$isErr) {
                    $status = true;
                }
            }
        }
        $response = array('status' => $status, 'error' => $error, 'data' => $data);
        return response()->json($response);
    }

    public function getVolumeBySubject(Request $request)
    {
        $subject_id = $request->subject_id;
        $result = array();
        $combo_name = "book_publication_volumes";
        $condtions = ['status' => 1, 'combo_name' => $combo_name];
        $mainTable = "masters";
        if (!empty($subject_id)) {
            $condition = ['subject_id' => $subject_id];
            $volumedata = BookVolumeMaster::where($condition)->pluck('medium', 'volume');
            if ($volumedata->count() != 0) {
                $volume = array_keys($volumedata->toArray());
                $result['medium'] = implode(',', array_unique(array_values($volumedata->toArray())));
                $result['volumes'] = DB::table($mainTable)->where($condtions)->whereIn('option_id', $volume)->orderBy("option_val")->get()->pluck('option_val', 'option_id');
            }
        }
        return $result;
    }

    public function ajaxVerificationTrailValidation(Request $request)
    {
        $isValid = false;
        $extra = null;
        $isAllRejected = null;
        $customerrors = array();
        $responses = array();
        $response = array();
        $customerrorFromModel = array();

        if (count($request->all()) > 0) {

            $inputs = $request->all();

            $tempInputs = $inputs;

            unset($tempInputs['ajaxRequest']);
            unset($tempInputs['_method']);
            unset($tempInputs['_token']);
            unset($tempInputs['action']);
            $inputInterMids = array();

            if (isset($tempInputs['main']) && count($tempInputs['main']) > 0) {
                foreach (@$tempInputs['main'] as $k => $v) {
                    if ($k != "") {
                        $k = decrypt($k);
                    }
                    if ($v == "on") {
                        $isValid = true;
                        break;
                    }
                }
            }

            $customerrorFromModel['documents'] = null;
            if (@$isValid && $isValid == true) {
            } else {
                $customerrorFromModel['documents'] = "Please select at least one field.";
            }
        }

        if (@$customerrorFromModel) {
            $customerrors = implode(",", $customerrorFromModel);
        }
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);

        return response()->json($output);
    }

    public function ajaxCorrectionMarksheetValidation(Request $request)
    {

        $customerrors = array();
        $response = array();
        $customerrorFromModel = array();
        if (count($request->all()) > 0) {
            $isValid = true;
            $responses = $this->MarksheetValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach (@$responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                    $customerrorFromModel[$k] = $response['errors'];
                }
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }

        $customerrors = implode(",", $customerrorFromModel);
        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);
    }


    public function ajaxDgsAddStudensValidation(Request $request)
    {
        $isValid = true;
        $errors = null;
        $customerrors = array();
        $responses = array();
        $customerrorFromModel = array();
        $finalErrors = null;
        $customerrorFromModel = array();

        $validator = Validator::make([], []);
        if (count($request->all()) > 0) {

            $nextStepStatus = false;
            if (@$request->name) {
                foreach ($request->name as $key => $value) {
                    if ($value != "") {
                        $nextStepStatus = true;
                        break;
                    }
                }
            }
            if (@$request->dob) {
                foreach ($request->dob as $key => $value) {
                    if ($value != "") {
                        $nextStepStatus = true;
                        break;
                    }
                }
            }
            if ($nextStepStatus) {
                if ($request->name) {
                    foreach ($request->name as $key => $value) {
                        if (!empty($value) && empty($request->dob[$key])) {
                            $fld = "Dob";
                            $errMsg = 'Please Enter DOB in serial No.' . $key + 1;
                            $errors = $errMsg;
                            $validator->getMessageBag()->add($fld, $errMsg);
                            $isValid = false;
                            break;
                        } elseif (empty($value) && !empty($request->dob[$key])) {
                            $fld = "name" . "_$key";
                            $errMsg = 'Enter Name in  serial no. ' . $key + 1;
                            $errors = $errMsg;
                            $validator->getMessageBag()->add($fld, $errMsg);
                            $isValid = false;
                            break;
                        }
                    }
                }
            } else {
                $fld = "Dob";
                $errMsg = 'कृपया कम से कम एक छात्र के विवरण दर्ज करें।(Please provide the details of at least one student.)';
                $errors = $errMsg;
                $validator->getMessageBag()->add($fld, $errMsg);
                $isValid = false;
            }
        }
        $responses['0']['isValid'] = $isValid;
        $responses['0']['errors'] = $errors;
        $responses['0']['validator'] = $validator;

        if (@$responses) {
            foreach (@$responses as $k => $response) {
                if (!$response['isValid']) {
                    $isValid = false;
                }
                $responseFinal[$k]['isValid'] = $response['isValid'];
                $responseFinal[$k]['customerrors'] = $response['errors'];
                $responseFinal[$k]['validator'] = $response['validator'];

                $customerrorFromModel[$k] = $response['errors'];
            }
        }
        $finalErrors = null;
        foreach (@$customerrorFromModel as $k => $v) {
            $finalErrors[$k] = $v;
        }
        $customerrors = implode(",", $customerrorFromModel);

        $output = array('isValid' => $isValid, 'error' => $customerrorFromModel);
        return response()->json($output);
    }
	
	public function printScreen(Request $request)
    {
        $isValid = false;
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$enrollment = $_POST['enrollment'];
			$file = public_path().'/counters/provisional_marksheet_prints.txt';
			file_put_contents($file, $enrollment . PHP_EOL, FILE_APPEND);
			$isValid = true;
		}
		return $isValid;
	}
	
	public function sdloginOTP(Request $request){
		$respones= array();
		$respones['status'] = true;
		$respones['error'] = null;
		 
		$allowMobile = DB::table('masters')->where('combo_name', 'valid_mobile_numbers')->pluck('option_val', 'option_id');
		$arr = $allowMobile->toArray();
		if (@$arr[1]) {
			$allowMobile = json_decode($arr[1], true);
		}   
		@$ReqMobile=$request->mobile; 
		if (in_array($ReqMobile, $allowMobile)){
			$otp = random_int(100000, 999999);
			Session::put("temp_otp",$otp); 
			$sms = "Dear Applicant, Please verify your mobile number. The OTP is :" . $otp . ". - RSOS,GoR";
			$templateID = "1007609835962970505";
			$this->_sendSMS(@$ReqMobile, $sms, $templateID);
			$respones['message']= "Otp send to mobile number."; 
		}else{
			$respones['status'] = false;
			$respones['error']= "Mobile Number Not allowed!"; 
		}
		return $respones;
	}
}
	


