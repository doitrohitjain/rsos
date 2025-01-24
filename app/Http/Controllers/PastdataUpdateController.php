<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\MarksheetCustomComponent;
use App\Component\PracticalCustomComponent;
use App\Component\ThoeryCustomComponent;
use App\Helper\CustomHelper;
use App\models\ExamResult;
use App\models\ExamSubject;
use App\models\Pastdata;
use App\models\Student;
use Auth;
use Config;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use PDF;
use Response;
use Session;

/* Hall Ticket Addtional Model */

//use PDF;
/* Hall Ticket Addtional Model */

class PastdataUpdateController extends Controller
{
    public $custom_component_obj = "";
    public $marksheet_component_obj = "";


    public $theory_custom_component_obj = " ";

    function __construct(Request $request)
    {
        parent::__construct();
        $this->middleware('permission:pastdataupdate', ['only' => ['enrollmentserach']]);
        $this->custom_component_obj = new CustomComponent;
        $this->marksheet_component_obj = new MarksheetCustomComponent;
        $this->custom_component_obj = new CustomComponent;

        $this->theory_custom_component_obj = new ThoeryCustomComponent;
    }

    public function enrollmentserach(Request $request)
    {
        $page_title = "Search Enrollment";
        if ($request->isMethod('PUT')) {
            $student = new Student;
            $validator = Validator::make($request->all(), $student->rulessessionalmarks);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $enrollment = $request->enrollment;
            $pastdata = $this->marksheet_component_obj->getpastdata($enrollment);
            if (empty($pastdata)) {
                return back()->with('error', 'Enrollment not found');
            } else {
                $id = $pastdata->id;
                return redirect()->route('pastdataupdate', Crypt::encrypt($id));
            }
        }
        return view('pastdataupdate.enrollmentsearch', compact('page_title'));
    }


    public function pastenrollmentserach(Request $request)
    {
        $page_title = "Search Enrollment";
        if ($request->isMethod('PUT')) {
            $student = new Student;
            $validator = Validator::make($request->all(), $student->rulessessionalmarks);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $enrollment = $request->enrollment;
            $pastdata = $this->marksheet_component_obj->getpastdata($enrollment);
            if (empty($pastdata)) {
                return back()->with('error', 'Enrollment not found');
            } else {
                $id = $pastdata->id;
                return redirect()->route('subjectdata', Crypt::encrypt($enrollment));
            }
        }
        return view('pastdataupdate.enrollmentsearch', compact('page_title'));
    }

    public function updatePastData(Request $request, $id = null)
    {
        $title = "Edit Student data";
        $id = Crypt::decrypt($id);
        $is_unlock_allow = true;
        $subjects = $this->custom_component_obj->subjectList();
        $form_type = ucfirst(str_replace(" ", "_", $title));
        $displayexammonth = DB::table('display_month_years')->whereNotNull('id')->whereNotNull('Ex_YR')->orderBy('id', 'ASC')->pluck('display_ex_month_year', 'display_ex_month_year');
        $tablename = 'pastdatas';
        $obj_controller = new Controller();
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
        $data = Pastdata::find($id);

        if ($request->isMethod('put')) {

            $dobArr = explode("/", $request->DOB);
            if (!empty($dobArr) && isset($dobArr['1'])) {
                if (isset($dobArr[0]) && isset($dobArr[1]) && isset($dobArr[2])) {
                    $dobs = $dobArr[2] . "-" . $dobArr[1] . "-" . $dobArr[0];
                }
            } else {
                $dobs = date("Y-m-d", strtotime($request->DOB));
            }

            $dobArr2 = explode("/", $request->ResultDate);

            if (!empty($dobArr2) && isset($dobArr2['1'])) {
                if (isset($dobArr2[0]) && isset($dobArr2[1]) && isset($dobArr2[2])) {
                    $resultdate = $dobArr2[2] . "-" . $dobArr2[1] . "-" . $dobArr2[0];
                }
            } else {
                $resultdate = date("Y-m-d", strtotime($request->ResultDate));
            }

            $svData = ['NAME' => $request->NAME, 'FNAME' => $request->FNAME, 'MNAME' => $request->MNAME,
                'DOB' => $dobs, 'ResultDate' => $resultdate, 'EX_YR' => $request->EX_YR,
            ];
            $exam_subject_log = $obj_controller->updateStudentLog($tablename, $id, $form_type, $is_unlock_allow);
            $updateData = Pastdata::where('id', '=', $id)->update($svData);
            if ($updateData) {
                return redirect()->route('Serach_Enrollment')->with('message', 'Data Update succesfully')->withInput();
            }


        }

        return view('pastdataupdate.updatepastdata', compact('title', 'breadcrumbs', 'data', 'displayexammonth'));
    }


    public function updatePastStudentSubjecData($enrollment = null)
    {
        $enrollment = Crypt::decrypt($enrollment);
        $title = "Pastdata Result Update";
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month_session = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $exam_year_session = $this->master_details($combo_name);

        $permissions = CustomHelper::roleandpermission();
        $data = array();
        $data2 = 0;
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
        $pastdataroute = 1;
        $resultsyntax = array('999' => 'AB', '666' => 'SYCP', '777' => 'SYCT', '888' => 'SYC', 'P' => 'PASS', 'p' => 'PASS');
        $pastdata = Pastdata::where('enrollno', $enrollment)->wherenull('deleted_at')->first();
        DD($pastdata);
        $subjectCodeIds = $this->marksheet_component_obj->_getSubjectsCodeId($pastdata->CLASS1);
        $combo_name = 'subject_result_type';
        $result = $this->master_details($combo_name);
        $subjects = $this->custom_component_obj->subjectList($pastdata->CLASS1);
        $studentdata = array();
        $subject_detalis = $this->theory_custom_component_obj->_getSubjectDetail();
        $data = DB::table('exam_subjects')->where('enrollment', $enrollment)->get();

        $subjectcount = count($data);
        if (count($data) != 0) {
            return view('resultupdate.index', compact('breadcrumbs', 'pastdataroute', 'resultsyntax', 'subjectcount', 'studentdata', 'title', 'permissions', 'course', 'subjects', 'data', 'exam_month_session', 'exam_year_session', 'data2'));
        } else {
            return view('pastdataupdate.updatesubject', compact('breadcrumbs', 'title', 'pastdata', 'result', 'subjectCodeIds', 'subjects'));

        }


    }

    public function updatePastStudentSubjectmarks($id = null, request $request)
    {
        $title = "Update Subject Marks";
        $combo_name = 'subject_result_type';
        $result = $this->master_details($combo_name);
        $obj_controller = new Controller();
        $tablename = 'exam_subjects';
        $subjects = $this->custom_component_obj->subjectList();
        $practical_custom_component_obj = new PracticalCustomComponent;
        $form_type = ucfirst(str_replace(" ", "_", $title));
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
        $toc_data = array();
        $id = Crypt::decrypt($id);
        $data = Examsubject::find($id);
        $subjects = $subjects["$data->subject_id"];
        $subject_detalis = $this->theory_custom_component_obj->_getSubjectDetail(@$data->subject_id);
        if ($request->isMethod('PUT')) {

            $examsubjects = new ExamSubject();
            $validator = Validator::make($request->all(), $examsubjects->rules, $examsubjects->message);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $svdata = $request->all();
            if ($request->final_result == 1) {
                $svdata['final_result'] = 'P';
            }
            $enrollment = $data->enrollment;
            $sessionalmarks = @$subject_detalis->sessional_max_marks;
            $theorymaxmarks = @$subject_detalis->theory_max_marks - @$sessionalmarks;

            if ($request->final_theory_marks != 777 || $request->final_theory_marks != 888) {
                if ($request->final_theory_marks > @$subject_detalis->theory_max_marks && @$data->stream == 2) {
                    return back()->with('error', "Please Enter Marks should be less than $subject_detalis->theory_max_marks  marks.")->withInput();

                }
                if ($request->final_theory_marks > @$theorymaxmarks && @$data->stream == 1 && empty($toc_data)) {
                    return back()->with('error', "Please Enter Marks should be less than $theorymaxmarks  marks.")->withInput();

                }
                if ($request->final_theory_marks > @$subject_detalis->theory_max_marks && @$data->stream == 1 && !empty($toc_data)) {
                    return back()->with('error', "Please Enter Marks should be less than $subject_detalis->theory_max_marks  marks.")->withInput();
                }
            }
            $exam_subject_log = $obj_controller->updateStudentLog($tablename, $id, $form_type);

            $master = ExamSubject::find($id)->update($svdata);
            if ($master) {
                return redirect()->route('subjectdata', [Crypt::encrypt($enrollment)])->with('message', 'Result Updated successfully.');
            } else {
                return back()->with('error', 'Result Not Updated successfully');
            }
        }
        return view('resultupdate.editresult', compact('title', 'breadcrumbs', 'data', 'result', 'subjects', 'subject_detalis', 'toc_data'));


    }


    public function updatepastdatafinalresult($enrollment = null, request $request)
    {
        $enrollment = Crypt::decrypt($enrollment);
        $permissions = CustomHelper::roleandpermission();
        $pagetitle = "Note:- Please check the Subject wise Numbers Entered & Update the Total Number and Final Result before submitting.";
        $title = "Pastdata Update Marksheet";
        $obj_controller = new Controller();
        $combo_name = 'final_result';
        $finalresults = $this->master_details($combo_name);
        $tablename = 'exam_results';
        $subjects = $this->custom_component_obj->subjectList();
        $form_type = ucfirst(str_replace(" ", "_", $title));
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
        $data2 = ExamSubject::where('enrollment', $enrollment)->latest('exam_year')->first('exam_year');
        $data = ExamSubject::where('enrollment', $enrollment)->where('exam_year', $data2->exam_year)->get();
        $exam_result = ExamResult::where('enrollment', '=', $enrollment)->orderBy('exam_year', 'desc')->first();
        $resultsyntax = array('999' => 'AB', '666' => 'SYCP', '777' => 'SYCT', '888' => 'SYC', 'P' => 'PASS', 'p' => 'PASS');
        if (empty($exam_result)) {
            return back()->with('message', 'Result Not Declare.');
        }
        if ($request->isMethod('put')) {
            $examresults = new ExamResult();
            $validator = Validator::make($request->all(), $examresults->rules, $examresults->message);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $id = $exam_result->id;
            $enrollment = $exam_result->enrollment;
            $svdata = array();
            $svdata = $request->all();
            if (@$request->additional_subjects) {
                $adsub = $request->additional_subjects;
                $svdata['additional_subjects'] = (!empty($adsub)) ? serialize($adsub) : null;
            }
            if ($request->final_result == 1) {
                $svdata['final_result'] = 'PASS';

            } else {
                $svdata['final_result'] = 'XXXX';

            }
            $exam_subject_log = $obj_controller->updateStudentLog($tablename, $id, $form_type);
            $result = ExamResult::find($id)->update($svdata);
            if ($result) {
                return redirect()->route('subjectdata', Crypt::encrypt($enrollment))->with('message', 'Result Update Successfully');
            } else {
                return back()->with('error', 'Result Not Update Successfully');
            }
        }
        return view('resultupdate.finalupdate', compact('breadcrumbs', 'permissions', 'resultsyntax', 'subjects', 'pagetitle', 'title', 'data', 'exam_result', 'finalresults'));

    }

    public function uploadDocAtAnyPath(Request $request)
    {
        $page_title = "Upload Any Document";
        $model = "student";
        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'upload_file' => 'required|max:2048',
                'document_path' => 'required'],
                [
                    'upload_file.required' => 'Upload Document  is required.',
                    'document_path.required' => 'document Upload Path is Required.'
                ]);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $input = $request->all();
            $file = $request->file('upload_file');
            $uploadFilePath = $request->document_path;
            File::makeDirectory(base_path($uploadFilePath), $mode = 0777, true, true);
            $fileName = $file->getClientOriginalName();
            $file->move(base_path($uploadFilePath), $file->getClientOriginalName());
            return redirect()->back()->with('message', "file Upload Successfully.");
        }

        return view('resultupdate.uploadDoc', compact('page_title', 'model'));
    }


}
	
