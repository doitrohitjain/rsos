<?php

namespace App\Http\Controllers;

use App\Component\WebapiComponent;
use App\Models\SessionalExamSubject;
use App\models\Student;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class WebapinewController extends Controller
{
    public function old_generatesecure_token($filedName = null)
    {
        return Crypt::encrypt($filedName);
    }

    public function old_checksecure_token($secure_token = null, $ssoid = null)
    {
        if (@$secure_token && @$ssoid) {
            $orgVal = Crypt::decrypt($secure_token);
            if ($orgVal == $ssoid) {
                return true;
            }
        }
        return false;
    }

    public function api_student_admit_card_pdf(Request $request)
    {
        $inputs = $request->all();
        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid key.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $componentObj = new WebapiComponent;
        //$enrollment = "21061232057";$ssoid = "00DHARASINGH.DHOBI";$dob = "1999-05-18";
        $enrollment = @$inputs['enrollment'];

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid key.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $validator = Validator::make($inputs, [
            'enrollment' => 'required'
        ]);

        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
            $studentFields = array('students.id', 'students.enrollment');
            $student = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where('students.enrollment', $enrollment)->first($studentFields);
            if (@$student) {

                $master['student'] = $student->toArray();
                $student_id = $master['student']['id'];
                //$data = $path = "https://rsosadmission.rajasthan.gov.in/rsos/generate_student_pdf/" . Crypt::encrypt($student_id);
                $data = $path = route('generate_student_pdf', Crypt::encrypt($student_id));
            }
            if (@$data) {
                $status = true;
            } else {
                $error[] = "Not found.";
            }
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        return $response;
    }

    public function _checkValidToken($key = null)
    {
        if (@$key && $key == Config("global.api_token")) {
            return true;
        }
        return false;
    }

    public function api_hall_ticket_for_mobile_single_enrollment_view(Request $request)
    {
        $inputs = $request->all();
        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);
        // dd($inputs);
        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid key.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $componentObj = new WebapiComponent;
        //$enrollment = "21061232057";$ssoid = "00DHARASINGH.DHOBI";$dob = "1999-05-18";

        $ssoid = @$inputs['ssoid'];
        $enrollment = @$inputs['enrollment'];
        $dob = @$inputs['dob'];

        $validator = Validator::make($inputs, [
            'secure_token' => 'required',
            'ssoid' => 'required',
            'enrollment' => 'required',
            'dob' => 'required|string|min:6|max:50'
        ]);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid key.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */


        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {

            // $enrollment = "21061232057";
            // echo Crypt::encrypt($enrollment);die;
            //$data = $path = "https://rsosadmission.rajasthan.gov.in/rsos/mobile_view/" . Crypt::encrypt($enrollment);
            //$data = $path = route('mobile_view_hallticketbulview',array($enrollment));

            $studentFields = array('students.id', 'students.enrollment');
            //$student = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where('students.enrollment',$enrollment)->where('students.dob',$dob)->first($studentFields);
            $student = Student::where('students.enrollment', $enrollment)->where('students.dob', $dob)->first($studentFields);

            if (@$student) {

                $master['student'] = $student->toArray();
                $enrollment = $master['student']['enrollment'];
                //$data = $path = "https://rsosadmission.rajasthan.gov.in/rsos/mobile_view/" . Crypt::encrypt($student_id);
                $data = $path = route('hall_ticket_api_download', array(Crypt::encrypt($enrollment)));


                //$data = $path = "https://rsosadmission.rajasthan.gov.in/rsos/generate_student_pdf/" . Crypt::encrypt($student_id);
                // $data = $path = route('hall_ticket_api_download','enrollment' => Crypt::encrypt($enrollment));
                //$data = array('url' => route('hall_ticket_api_download'),'enrollment' => Crypt::encrypt($enrollment));

            }


            if (@$data) {
                $status = true;
            } else {
                $error[] = "Not found.";
            }
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        return $response;
    }

    public function old_new_api_student_login(Request $request)
    {
        $start_time = $this->start_time();
        $inputs = $request->all();
        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $componentObj = new WebapiComponent;
        //$enrollment = "21061232057";$ssoid = "00DHARASINGH.DHOBI";$dob = "1999-05-18";
        $ssoid = @$inputs['ssoid'];
        $dob = @$inputs['dob'];

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $validator = Validator::make($inputs, [
            'ssoid' => 'required',
            'dob' => 'required|string|min:6|max:50'
        ]);
        $secure_token = null;
        $validTill = null;
        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
            $data = $componentObj->getAPIStudentLoginDetails($ssoid, $dob);
            if (@$data) {
                $secure_token = $this->_generatesecure_token($ssoid);
                $validTill = date("d-m-Y");
                $status = true;
            } else {

                $error[] = "Not found.";
            }
        }
        $response = array('status' => $status, "data" => $data, "secure_token" => $secure_token, "secure_token_valid_till" => $validTill, "error" => $error);
        $this->end_time($start_time);
        return $response;
    }

    public function _generatesecure_token($filedName = null)
    {
        $filedName = $filedName . date("d_m");
        return Crypt::encrypt($filedName);
    }

    public function new_api_student_login(Request $request)
    {
        $start_time = $this->start_time();
        $inputs = $request->all();
        $status = false;
        $data = false;
        $error = null;
        $extra = null;
        $response = array('status' => false, "data" => $data, "error" => $error);
        $type = 'login';

        $extra['is_sessional_marks_entries_allowed'] = false;
        $extra['is_admit_card_allowed'] = false;


        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $componentObj = new WebapiComponent;
        //$enrollment = "21061232057";$ssoid = "00DHARASINGH.DHOBI";$dob = "1999-05-18";
        $ssoid = @$inputs['ssoid'];
        $dob = @$inputs['dob'];
        $enrollment = @$inputs['enrollment'];

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $validator = Validator::make($inputs, [
            'ssoid' => 'required',
            'dob' => 'required|string|min:6|max:50'
        ]);

        $admissistuon_academicyear_id = Config::get("global.admissistuon_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");

        $student_admit_card_download_exam_year = Config::get("global.student_admit_card_download_exam_year");
        $student_admit_card_download_exam_month = Config::get("global.student_admit_card_download_exam_month");

        $secure_token = null;
        $validTill = null;
        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
            if (@$enrollment) {
                $data = $componentObj->getAPIStudentLoginDetails($ssoid, $dob, $enrollment);

                if (@$data) {
                    $secure_token = $this->_generatesecure_token($ssoid);
                    $validTill = date("d-m-Y");
                    $status = true;
                    if ($data['student']['exam_year'] == $admissistuon_academicyear_id && $data['student']['exam_month'] == $current_exam_month_id) {
                        $extra['is_sessional_marks_entries_allowed'] = true;
                    }
                    if ($data['student']['exam_year'] == $student_admit_card_download_exam_year && $data['student']['exam_month'] == $student_admit_card_download_exam_month) {
                        $extra['is_admit_card_allowed'] = true;
                    }
                    // unset($data['student']['exam_year']);
                    // unset($data['student']['exam_month']);
                } else {
                    $error[] = "Not found.";
                }
            } else {
                $enrollments = $componentObj->getAPIStudentEnrollmentList($ssoid, $dob);
                if (count($enrollments) > 1) {
                    $enrollments = array_values($enrollments);
                    $type = "enrollments";
                    if (@$enrollments) {
                        $status = true;
                        $data = $enrollments;
                    } else {
                        $error[] = "Not found.";
                    }
                } else {
                    $enrollments = array_values($enrollments);
                    $data = $componentObj->getAPIStudentLoginDetails($ssoid, $dob, @$enrollments[0]);
                    if (@$data) {
                        $secure_token = $this->_generatesecure_token($ssoid);
                        $validTill = date("d-m-Y");
                        $status = true;
                        if ($data['student']['exam_year'] == $admissistuon_academicyear_id && $data['student']['exam_month'] == $current_exam_month_id) {
                            $extra['is_sessional_marks_entries_allowed'] = true;
                        }
                        if ($data['student']['exam_year'] == $student_admit_card_download_exam_year && $data['student']['exam_month'] == $student_admit_card_download_exam_month) {
                            $extra['is_admit_card_allowed'] = true;
                        }
                        // unset($data['student']['exam_year']);
                        // unset($data['student']['exam_month']);
                    } else {
                        $error[] = "Not found.";
                    }
                }
            }
        }
        if ($type == "enrollments") {
            $response = array('type' => $type, 'status' => $status, "data" => $data, "error" => $error);
        } else {
            $response = array('extra' => $extra, 'type' => $type, 'status' => $status, "data" => $data, "secure_token" => $secure_token, "secure_token_valid_till" => $validTill, "error" => $error);
        }
        $this->end_time($start_time);
        return $response;
    }

    public function new_api_student_exam_subjects(Request $request)
    {
        $start_time = $this->start_time();
        $inputs = $request->all();

        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);

        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $componentObj = new WebapiComponent;
        //$enrollment = "21061232057";$ssoid = "00DHARASINGH.DHOBI";$dob = "1999-05-18";
        $enrollment = @$inputs['enrollment'];
        $ssoid = @$inputs['ssoid'];
        $dob = @$inputs['dob'];
        $secure_token = @$inputs['secure_token'];

        /* Secure Token check end */
        $isValidsecure_token = $this->_checksecure_token(@$secure_token, @$ssoid);
        if (!$isValidsecure_token) {
            $error = 'Invalid secure token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Secure Token check end */

        $validator = Validator::make($inputs, [
            'ssoid' => 'required',
            'enrollment' => 'required',
            'dob' => 'required|string|min:6|max:50'
        ]);

        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
            $data = $componentObj->getAPIStudentDetails($ssoid, $dob, $enrollment);
            if (@$data) {
                $status = true;
            } else {
                $error[] = "Not found.";
            }
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        $this->end_time($start_time);
        return $response;
    }

    public function _checksecure_token($secure_token = null, $ssoid = null)
    {
        $ssoid = $ssoid . date("d_m");
        if (@$secure_token && @$ssoid) {
            $orgVal = Crypt::decrypt($secure_token);
            if ($orgVal == $ssoid) {
                return true;
            }
        }
        return false;
    }

    public function new_api_student_application_form_view(Request $request)
    {
        $inputs = $request->all();

        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $componentObj = new WebapiComponent;
        //$enrollment = "21061232057";$ssoid = "00DHARASINGH.DHOBI";$dob = "1999-05-18";
        $ssoid = @$inputs['ssoid'];
        $enrollment = @$inputs['enrollment'];
        $dob = @$inputs['dob'];

        $validator = Validator::make($inputs, [
            'secure_token' => 'required',
            'ssoid' => 'required',
            'enrollment' => 'required',
            'dob' => 'required|string|min:6|max:50'
        ]);

        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
            $secure_token = @$inputs['secure_token'];
            /* Secure Token check end */
            $isValidsecure_token = $this->_checksecure_token(@$secure_token, @$ssoid);
            if (!$isValidsecure_token) {
                $error = 'Invalid secure token.';
                $response = array('status' => $status, "data" => $data, "error" => $error);
                return $response;
            }
            /* Secure Token check end */

            $studentFields = array('students.id', 'students.enrollment');
            //$student = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where('students.enrollment',$enrollment)->where('students.dob',$dob)->first($studentFields);
            $student = Student::where('students.enrollment', $enrollment)->where('students.dob', $dob)->first($studentFields);
            if (@$student) {

                $master['student'] = $student->toArray();
                $student_id = $master['student']['id'];
                //$data = $path = "https://rsosadmission.rajasthan.gov.in/rsos/mobile_view/" . Crypt::encrypt($student_id);
                $data = $path = route('mobile_view', Crypt::encrypt($student_id));

                $student_id = $master['student']['id'];
                //$data = $path = "https://rsosadmission.rajasthan.gov.in/rsos/mobile_view/" . Crypt::encrypt($student_id);
                // $path = route('mobile_view',Crypt::encrypt($student_id));
                $data = array('url' => route('mobile_view'), 'student' => Crypt::encrypt($student_id));


            }
            if (@$data) {
                $status = true;
            } else {
                $error[] = "Not found.";
            }
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        return $response;
    }


    public function new_api_student_application_form_pdf(Request $request)
    {
        $inputs = $request->all();
        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $componentObj = new WebapiComponent;
        //$enrollment = "21061232057";$ssoid = "00DHARASINGH.DHOBI";$dob = "1999-05-18";
        $enrollment = @$inputs['enrollment'];

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $validator = Validator::make($inputs, [
            'enrollment' => 'required'
        ]);

        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
            $studentFields = array('students.id', 'students.enrollment');
            //$student = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where('students.enrollment',$enrollment)->first($studentFields);
            $student = Student::where('students.enrollment', $enrollment)->where('students.dob', $dob)->first($studentFields);
            if (@$student) {

                $master['student'] = $student->toArray();
                $student_id = $master['student']['id'];
                //$data = $path = "https://rsosadmission.rajasthan.gov.in/rsos/generate_student_pdf/" . Crypt::encrypt($student_id);
                $data = $path = route('generate_student_pdf', Crypt::encrypt($student_id));

            }
            if (@$data) {
                $status = true;
            } else {
                $error[] = "Not found.";
            }
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        return $response;
    }

    public function new_api_student_admit_card_pdf(Request $request)
    {
        $inputs = $request->all();
        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $componentObj = new WebapiComponent;
        //$enrollment = "21061232057";$ssoid = "00DHARASINGH.DHOBI";$dob = "1999-05-18";
        $enrollment = @$inputs['enrollment'];

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $validator = Validator::make($inputs, [
            'enrollment' => 'required'
        ]);

        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
            $studentFields = array('students.id', 'students.enrollment');
            $student = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where('students.enrollment', $enrollment)->first($studentFields);
            if (@$student) {

                $master['student'] = $student->toArray();
                $student_id = $master['student']['id'];
                //$data = $path = "https://rsosadmission.rajasthan.gov.in/rsos/generate_student_pdf/" . Crypt::encrypt($student_id);
                $data = $path = route('generate_student_pdf', Crypt::encrypt($student_id));
            }
            if (@$data) {
                $status = true;
            } else {
                $error[] = "Not found.";
            }
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        return $response;
    }

    public function new_api_master_subjects(Request $request)
    {
        $start_time = $this->start_time();

        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);
        $inputs = $request->all();
        $course = @$inputs['course'];
        $ssoid = @$inputs['ssoid'];

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);

        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $validator = Validator::make($inputs, [
            'ssoid' => 'required'
        ]);

        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {

            $secure_token = @$inputs['secure_token'];
            /* Secure Token check end */
            $isValidsecure_token = $this->_checksecure_token(@$secure_token, @$ssoid);
            if (!$isValidsecure_token) {
                $error = 'Invalid secure token.';
                $response = array('status' => $status, "data" => $data, "error" => $error);
                return $response;
            }
            /* Secure Token check end */


            if (@$course) {
                $status = true;
                $data = $this->subjectList($course);
            } else {
                $error[] = "Not found.";
            }
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        $this->end_time($start_time);
        return $response;
    }

    public function new_api_set_bulk_student_sessional_exam_subject_marks_by_mobile_admin(Request $request)
    {
        $start_time = $this->start_time();
        $inputs = $request->all();

        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);

        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */
        $componentObj = new WebapiComponent;

        $data = file_get_contents($_FILES['bulk_inputs']['tmp_name'], "r");

        $exam_year = Config::get("global.current_sessional_exam_year");
        $exam_month = Config::get("global.current_sessional_exam_month");

        $isJson = $this->isJson($data);
        $isValidJsonName = "invalid";
        if (@$isJson) {
            $isValidJsonName = "valid";
        }
        $inputFile = @$_FILES['bulk_inputs'];
        $datetime = date("_d_m_Y_H_i_s");
        $inputName = $isValidJsonName . "_" . $_FILES['bulk_inputs']['name'] . $datetime . ".json";
        $responseFileName = "response_" . $_FILES['bulk_inputs']['name'] . $datetime . ".json";

        $documentPath = "apibulkmarks" . DIRECTORY_SEPARATOR . $exam_year . DIRECTORY_SEPARATOR . $exam_month . DIRECTORY_SEPARATOR;
        $request->bulk_inputs->move(public_path($documentPath), $inputName);
        if ($isJson) {
        } else {
            $error = 'Invalid JSON input in file.';
            $response = array('status' => $status, "data" => 'Json File', "error" => $error);
            file_put_contents(public_path($documentPath) . $responseFileName, json_encode($response));
            return $response;
        }
        $bulk_inputs = json_decode($data, true); // decode the JSON feed
        $isValidInputString = array();
        foreach (@$bulk_inputs as $key => $input) {
            $enrollment = @$input['enrollment'];
            $studentFields = array('students.id', 'students.enrollment');
            $student = Student::where('students.enrollment', $enrollment)->first($studentFields);
            $details = SessionalExamSubject::where('exam_month', $exam_month)->where('exam_year', $exam_year)->where('student_id', @$student->id)->where('subject_id', $input['subject_id'])->first('id');
            if (@$details->id) {
            } else {
                $isValidInputString[$input['enrollment'] . "_" . $input['subject_id']] = "Enrollment subject details Not found.";
            }
        }
        if (@$isValidInputString && !empty($isValidInputString)) {
        } else {
            foreach (@$bulk_inputs as $key => $input) {
                $responseValidCheck = $this->isValidAPISessionalMarks($input);
                $isValid = $responseValidCheck['isValid'];
                $customerrors = $responseValidCheck['errors'];
                $validator = $responseValidCheck['validator'];
                if ($isValid) {
                } else {
                    $isValidInputString[$input['enrollment'] . "_" . $input['subject_id']] = $customerrors;
                }
            }
        }

        $totalUpdated = 0;
        if (@$isValidInputString) {
        } else {
            foreach ($bulk_inputs as $key => $input) {
                $enrollment = @$input['enrollment'];
                $studentFields = array('students.id', 'students.enrollment');
                $student = Student::where('students.enrollment', $enrollment)->first($studentFields);
                if (@$student) {
                } else {
                    $error = 'Invalid student.';
                    $response = array('status' => $status, "data" => $data, "error" => $error);
                    return $response;
                }
                $master['student'] = @$student->toArray();
                $student_id = @$master['student']['id'];

                if (@$student_id) {
                    $isValid = true;
                    if ($isValid) {
                        $exam_year = Config::get("global.current_sessional_exam_year");
                        $exam_month = Config::get("global.current_sessional_exam_month");
                        $key = @$input['subject_id'];
                        $value = @$input['obtained_marks'];
                        if ($value == 'AB') {
                            $value = '999';
                        }
                        $studentsessionalmarks = array('sessional_marks' => $value, 'is_sessional_mark_entered' => 6);
                        $updateStatus = SessionalExamSubject::where('exam_month', $exam_month)->where('exam_year', $exam_year)->where('student_id', $student_id)->where('subject_id', $key)->update($studentsessionalmarks);
                        if (@$updateStatus) {
                            $status = true;
                            $totalUpdated++;
                        } else {
                            $isValidInputString[$input['enrollment'] . "_" . $input['subject_id']] = "Sessional Marks details Not found.";
                        }
                    } else {
                        $isValidInputString[$input['enrollment'] . "_" . $input['subject_id']] = $customerrors;
                    }
                }
            }
        }
        $data = "Inputs";
        if (@$isValidInputString) {
            $error = $isValidInputString;
        } else {
            $data = "Total of " . @$totalUpdated . " enrollment subject combinations have been successfully updated.";
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        file_put_contents(public_path($documentPath) . $responseFileName, json_encode($response));
        $this->end_time($start_time);
        return $response;
    }

    public function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function new_api_set_student_sessional_exam_subject_marks(Request $request)
    {
        $start_time = $this->start_time();
        $inputs = $request->all();

        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);

        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $componentObj = new WebapiComponent;


        /* Token check start */
        /* $isValidToken = $this->_checkValidToken(@$inputs['token']);
         if(!$isValidToken){
             $error = 'Invalid token.';
             $response = array('status' => $status,"data" => $data, "error" => $error);
             return $response;
         }*/
        /* Token check end */

        $enrollment = @$inputs['enrollment'];

        $studentFields = array('students.id', 'students.enrollment');
        //$student = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where('students.enrollment',$enrollment)->first($studentFields);
        $student = DB::table('students')->where('students.enrollment', $enrollment)->first($studentFields);

        if (@$student) {
        } else {
            $error = 'Invalid student.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        $master['student'] = @$student->toArray();
        $student_id = @$master['student']['id'];

        if (@$student_id) {
            $validator = Validator::make($request->all(), [
                'enrollment' => 'required',
                'subject_id' => 'required',
                'obtained_marks' => 'required'
            ]);


            if ($validator->fails()) {
                $error[] = $validator->messages();
            } else {
                $response = $this->isValidAPISessionalMarks($inputs);

                $isValid = $response['isValid'];
                $customerrors = $response['errors'];
                $validator = $response['validator'];
                if ($isValid) {
                    $exam_year = Config::get("global.current_sessional_exam_year");
                    $exam_month = Config::get("global.current_sessional_exam_month");
                    $key = @$inputs['subject_id'];
                    $value = @$inputs['obtained_marks'];
                    if ($value == 'AB') {
                        $value = '999';
                    }
                    $studentsessionalmarks = array('sessional_marks' => $value, 'is_sessional_mark_entered' => 5);
                    $sessional_marks = $value;
                    $is_sessional_mark_entered = 5;
                    // $data = SessionalExamSubject::where('exam_month',$exam_month)->where('exam_year',$exam_year)->where('student_id',$student_id)->where('subject_id',$key)->update($studentsessionalmarks);
                    $data = DB::select('call updateSessionalMarksInSessionalExamSubject(?,?,?,?,?)', array($exam_year, $exam_month, $student_id, $sessional_marks, $is_sessional_mark_entered));


                    if (@$data) {
                        $status = true;
                        $data = "Sessional marks have been successfully submitted.";
                    } else {
                        $error[] = "Sessional Marks details Not found.";
                    }
                } else {
                    $error[] = $customerrors;
                }
            }
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        $this->end_time($start_time);
        return $response;
    }


    public function new_api_send_sms(Request $request)
    {
        $start_time = $this->start_time();
        $inputs = $request->all();

        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);

        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */
        $componentObj = new WebapiComponent;

        $data = file_get_contents($_FILES['bulk_inputs']['tmp_name'], "r");

        $isJson = $this->isJson($data);
        $isValidJsonName = "invalid";
        if (@$isJson) {
            $isValidJsonName = "valid";
        }
        $inputFile = @$_FILES['bulk_inputs'];
        $datetime = date("_d_m_Y_H_i_s");
        $inputName = $isValidJsonName . "_" . $_FILES['bulk_inputs']['name'] . $datetime . ".json";
        $responseFileName = "response_" . $_FILES['bulk_inputs']['name'] . $datetime . ".json";

        $documentPath = "sendmobilesms" . DIRECTORY_SEPARATOR;
        $request->bulk_inputs->move(public_path($documentPath), $inputName);
        if ($isJson) {
        } else {
            $error = 'Invalid JSON input in file.';
            $response = array('status' => $status, "data" => 'Json File', "error" => $error);
            file_put_contents(public_path($documentPath) . $responseFileName, json_encode($response));
            return $response;
        }
        $bulk_inputs = json_decode($data, true);
        $isValidInputString = array();
        $totalUpdated = 0;
        foreach (@$bulk_inputs as $key => $input) {


            if (@$input["mobile"] && @$input["sms"] && @$input["template_id"]) {
                $status = true;
                $mobile = @$input["mobile"];
                $templateID = @$input["template_id"];
                $sms = @$input["sms"];
                $smsResp = $this->_sendSMS($mobile, $sms, $templateID);
                $totalUpdated++;
            } else {
                continue;
            }
        }
        $data = "Inputs";
        if (@$isValidInputString) {
            $error = $isValidInputString;
        } else {
            $data = "Total of " . @$totalUpdated . " sms sent.";
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        file_put_contents(public_path($documentPath) . $responseFileName, json_encode($response));
        $this->end_time($start_time);
        return $response;
    }

}
