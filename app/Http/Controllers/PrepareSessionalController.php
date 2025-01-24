<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Models\Application;
use App\Models\Registration;
use App\Models\SessionalExamSubject;
use App\Models\Student;
use Auth;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use PDF;
use Redirect;
use Response;
use Route;
use Session;
use Validator;

class PrepareSessionalController extends Controller
{
    public $customComponent = "";
    private $request;

    public function __construct(request $request)
    {
        $this->request = $request;
        parent::__construct();
        $this->middleware('permission:prepare_sessional_find_enrollment', ['only' => ['find_enrollment']]);
        $this->middleware('permission:prepare_sessional_marks_details', ['only' => ['marks_details']]);
        $this->middleware('permission:prepare_sessional_marks_preview_details', ['only' => ['marks_preview_details']]);
        $this->customComponent = new CustomComponent();
    }

    public function find_enrollment(Request $request)
    {
        $showStatus = $this->_getCheckAllowSessionaldata();
        if (!$showStatus) {
            return redirect()->back()->with('error', 'You are not allowed to see the Sessional');
        }

        $allowedOrNot = $this->customComponent->_checkSessionalMarksEntryAllowOrNotAllow();
        $role_id = @Session::get('role_id');
        $aicenter_id = Config::get("global.aicenter_id");
        $devloper_admin = Config::get("global.developer_admin");
        if ($role_id != $devloper_admin) {
            if ($allowedOrNot == false) {
                return redirect()->back()->with('error', 'Failed! Sessional marks entry has been closed!');
            }
        }

        $table = $model = "Student";
        $page_title = 'Sessional Marks Details(सेशनल मार्क्स विवरण)';
        $routeUrl = "find_enrollment";
        $master = null;
        $estudent_id = null;
        $student_id = null;

        if (count($request->all()) > 0) {
            $modelObj = new Student;
            $validator = Validator::make($request->all(), $modelObj->rulessessionalmarks);
            $inputs = $request->all();

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $user_ai_code = Auth::user()->ai_code;
            $exam_year = Config::get("global.current_sessional_exam_year");
            $exam_month = Config::get("global.current_sessional_exam_month");
            if ($role_id == $aicenter_id) {
                $master = Student::where('ai_code', $user_ai_code)->where('is_eligible', 1)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->where('enrollment', $inputs['enrollment'])->first();
            } elseif ($role_id == $devloper_admin) {
                $master = Student::where('exam_year', $exam_year)->where('is_eligible', 1)->where('exam_month', $exam_month)->where('enrollment', $inputs['enrollment'])->first();
            }
            $student_id = $master['id'];
            $estudent_id = Crypt::encrypt($student_id);

            $isLockAndSubmit = $this->_isStudentFormLockAndSubmitForSessional($student_id);
            if ($isLockAndSubmit) {

            } else {
                return redirect()->back()->with('error', 'Failed! Application form yet not locked and submitted.')->withInput($request->all());
            }

            if ($master) {
                return redirect()->route('prepare_marks_details', Crypt::encrypt($student_id))->with('message', 'Enrollment details found.');
            } else {
                return redirect()->back()->with('error', 'Failed! Student not found.')->withInput($request->all());
            }
        }
        return view('preparesessional.find_enrollment', compact('model', 'master', 'estudent_id', 'student_id', 'page_title'));
    }

    public function marks_details(Request $request, $student_id)
    {

        $showStatus = $this->_getCheckAllowSessionaldata();
        if (!$showStatus) {
            return redirect()->back()->with('error', 'You are not allowed to see the Sessional');
        }
        $allowedOrNot = $this->customComponent->_checkSessionalMarksEntryAllowOrNotAllow();
        $role_id = @Session::get('role_id');
        $aicenter_id = Config::get("global.aicenter_id");
        $devloper_admin = Config::get("global.developer_admin");
        if ($role_id != $devloper_admin) {
            if ($allowedOrNot == false) {
                return redirect()->back()->with('error', 'Failed! Sessional marks entry has been closed!');
            }
        }
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);

        if (empty($student_id)) {
            return redirect()->route('prepare_find_enrollment')->with('error', 'Please first find student enrollment number.');
        }

        $table = $model = "SessionalExamSubject";

        $page_title = 'Sessional Marks Details(सेशनल मार्क्स विवरण)';
        $routeUrl = "exam_subject_details";

        $master = null;
        $isLockAndSubmit = $this->_isStudentFormLockAndSubmitForSessional($student_id);
        if ($isLockAndSubmit) {

        } else {
            return redirect()->back()->with('error', 'Failed! Application form yet not locked and submitted.')->withInput($request->all());
        }

        $studentdata = Student::findOrFail($student_id);
        $master = $this->getSessionalStudentPersoanlDetails($student_id);


        $maxMarks = $this->getSessionalSubjectMaxMarks();
        $minMarks = $this->getSessionalSubjectMinMarks();

        $subject_list = $this->subjectList($studentdata->course);
        $examSubjectsList = $this->_getSessionalExamSubjectsList($student_id);


        if (count($examSubjectsList) <= 0) {
            return redirect()->route('prepare_find_enrollment')->with('error', 'Failed! Student exam subjects not found.')->withInput($request->all());
        }

        if (count($request->all()) > 0) {


            $modelObj = new SessionalExamSubject;
            $isValid = true;
            $customerrors = null;
            $inputs = $request->all();
            $response = $this->isValidSessionalMarks($inputs);
            $isValid = $response['isValid'];
            $customerrors = $response['errors'];
            $validator = $response['validator'];

            if ($isValid) {
                //here udpate the sessionl marks
                foreach ($request->subject_id as $key => $value) {
                    $exam_year = Config::get("global.current_sessional_exam_year");
                    $exam_month = Config::get("global.current_sessional_exam_month");
                    if ($value == 'AB') {
                        $value = '999';
                    }
                    $studentsessionalmarks = array('sessional_marks' => $value, 'is_sessional_mark_entered' => 1);
                    $studentupdatesessionalmarks = SessionalExamSubject::where('exam_month', $exam_month)->where('exam_year', $exam_year)->where('student_id', $student_id)->where('subject_id', $key)->update($studentsessionalmarks);
                }
                return redirect()->route('prepare_marks_preview_details', Crypt::encrypt($student_id))->with('message', '' . 'Marks has been saved.');
            } else {
                return redirect()->back()->withErrors($customerrors)->withInput($request->all());
            }
        }
        return view('preparesessional.marks_details', compact('maxMarks', 'minMarks', 'subject_list', 'master', 'examSubjectsList', 'model', 'estudent_id', 'student_id', 'page_title'));
    }

    public function marks_preview_details(Request $request, $student_id)
    {
        $showStatus = $this->_getCheckAllowSessionaldata();
        if (!$showStatus) {
            return redirect()->back()->with('error', 'You are not allowed to see the Sessional');
        }
        //$this->_checkSessionalMarksEntryAllowOrNotAllow();
        $estudent_id = $student_id;
        $student_id = Crypt::decrypt($student_id);
        $masterrecord = Application::where('student_id', $student_id)->first('locksumbitted');

        if (empty($student_id)) {
            return redirect()->route('prepare_find_enrollment')->with('error', 'Please first find student enrollment number.');
        }
        $table = $model = "SessionalExamSubject";
        $page_title = '  Sessional Marks Preview Details(सेशनल मार्क्स पूर्वावलोकन विवरण)';
        $documentErrors = null;

        // $sub_code = $this->getSubjectCode($student_id);

        $studentdata = Student::findOrFail($student_id);
        $master = $this->getPrepareSessionalStudentPersoanlDetails($student_id);
        $subject_list = $this->subjectList($studentdata->course);
        $examSubjectsList = $this->_getSessionalExamSubjectsList($student_id);
        if (empty($master)) {
            return redirect()->route('/')->with('error', 'Failed! Details not found');
        }
        //->pluck(DB::raw('(case when sessional_marks = "999" then "Absent" else sessional_marks end )'),'subject_id');
        $routeUrl = "preview_details";
        return view('preparesessional.marks_preview_details', compact('model', 'masterrecord', 'master', 'examSubjectsList', 'estudent_id', 'student_id', 'documentErrors', 'page_title'));
    }

}