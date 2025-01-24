<?php

namespace App\Http\Controllers;

use App\Component\WebapiComponent;
use App\models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;


class WebapiController extends Controller
{
    public function api_student_login(Request $request)
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
        $ssoid = @$inputs['ssoid'];
        $dob = @$inputs['dob'];

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid key.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $validator = Validator::make($inputs, [
            'ssoid' => 'required',
            'dob' => 'required|string|min:6|max:50'
        ]);

        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
            $data = $componentObj->getAPIStudentLoginDetails($ssoid, $dob);
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

    public function api_student_exam_subjects(Request $request)
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
        $ssoid = @$inputs['ssoid'];
        $dob = @$inputs['dob'];

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid key.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $validator = Validator::make($inputs, [
            'ssoid' => 'required',
            'dob' => 'required|string|min:6|max:50'
        ]);

        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
            $data = $componentObj->getAPIStudentDetails($ssoid, $dob);
            if (@$data) {
                $status = true;
            } else {
                $error[] = "Not found.";
            }
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        return $response;
    }

    public function api_master_subjects(Request $request)
    {
        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);
        $inputs = $request->all();
        $course = @$inputs['course'];

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);

        if (!$isValidToken) {
            $error = 'Invalid key.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        if (@$course) {
            $status = true;
            $data = $this->subjectList($course);
        } else {
            $error[] = "Not found.";
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        return $response;
    }

    public function api_student_application_form_pdf(Request $request)
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


    public function api_student_application_form_view(Request $request)
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
        $dob = @$inputs['dob'];

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid key.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $validator = Validator::make($inputs, [
            'enrollment' => 'required',
            'dob' => 'required|string|min:6|max:50'
        ]);

        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
            $studentFields = array('students.id', 'students.enrollment');
            $student = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where('students.enrollment', $enrollment)->where('students.dob', $dob)->first($studentFields);
            if (@$student) {

                $master['student'] = $student->toArray();
                $student_id = $master['student']['id'];
                //$data = $path = "https://rsosadmission.rajasthan.gov.in/rsos/mobile_view/" . Crypt::encrypt($student_id);
                $data = $path = route('mobile_view', Crypt::encrypt($student_id));

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


    public function api_set_student_sessional_exam_subject_marks(Request $request)
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


        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['token']);
        if (!$isValidToken) {
            $error = 'Invalid key.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $enorollment = @$inputs['enorollment'];

        $validator = Validator::make($inputs, [
            'enorollment' => 'required'
        ]);

        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {

            $response = $this->isValidSessionalMarks($inputs);
            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];

            if ($isValid) {
                $data = $componentObj->setAPIStudentSessionalMarksDetails($inputs);
                if (@$data) {
                    $status = true;
                } else {
                    $error[] = "Not found.";
                }
            } else {
                $error[] = $customerrors;
            }
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        return $response;
    }


}
