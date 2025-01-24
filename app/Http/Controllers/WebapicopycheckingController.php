<?php

namespace App\Http\Controllers;

use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class WebapicopycheckingController extends Controller
{
    public function _generatesecure_token($filedName = null)
    {
        $filedName = $filedName . date("d_m");
        return Crypt::encrypt($filedName);
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

    public function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function master_subjects(Request $request)
    {
        $start_time = $this->start_time();
        $inputs = $request->all();

        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['secure_token']);

        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $course = @$inputs['course'];

        $validator = Validator::make($inputs, [
            'course' => 'required'
        ]);
        // dd($course);
        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
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

    public function _checkValidToken($key = null)
    {
        if (@$key && ($key == Config("global.api_token3"))) {
            return true;
        }
        return false;
    }

    public function master_theory_examiners(Request $request)
    {
        $start_time = $this->start_time();
        $inputs = $request->all();

        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['secure_token']);

        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $secure_token = @$inputs['secure_token'];
        $validator = Validator::make($inputs, [
            'secure_token' => 'required'
        ]);
        // dd($isValidToken);
        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
            $data = $this->getTheoryExaminersList();
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

    public function master_students_subjects(Request $request)
    {
        $start_time = $this->start_time();
        $inputs = $request->all();

        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['secure_token']);

        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $secure_token = @$inputs['secure_token'];
        $validator = Validator::make($inputs, [
            'secure_token' => 'required'
        ]);
        // dd($isValidToken);
        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
            $limit = 1100000;
            $data = $this->getStudentsTheorySubjectsList($limit);
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

    public function set_student_theory_marks(Request $request)
    {
        $start_time = $this->start_time();
        $inputs = $request->all();

        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['secure_token']);

        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $student_id = $subject_id = $final_theory_marks = $theory_absent = $theory_examiner_id = null;
        $student_id = @$inputs['student_id'];
        $subject_id = @$inputs['subject_id'];
        $final_theory_marks = @$inputs['final_theory_marks'];
        $theory_absent = @$inputs['theory_absent'];
        $theory_examiner_id = @$inputs['theory_examiner_id'];

        $validator = Validator::make($inputs, [
            'secure_token' => 'required',
            'student_id' => 'required',
            'subject_id' => 'required',
            'final_theory_marks' => 'required',
            'theory_absent' => 'required',
            'theory_examiner_id' => 'required',
        ]);
        // dd($isValidToken);
        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
            $data = $this->getSubmitStudentsTheoryMarks($student_id, $subject_id, $final_theory_marks, $theory_absent, $theory_examiner_id);
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

    public function master_student_enrollments(Request $request)
    {
        $start_time = $this->start_time();
        $inputs = $request->all();

        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);

        /* Token check start */
        $isValidToken = $this->_checkValidToken(@$inputs['secure_token']);

        if (!$isValidToken) {
            $error = 'Invalid token.';
            $response = array('status' => $status, "data" => $data, "error" => $error);
            return $response;
        }
        /* Token check end */

        $secure_token = @$inputs['secure_token'];
        $validator = Validator::make($inputs, [
            'secure_token' => 'required'
        ]);
        // dd($isValidToken);
        if ($validator->fails()) {
            $error[] = $validator->messages();
        } else {
            $data = $this->getStudentEnrollmentList();
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

}
