<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use App\Component\CustomComponent;
use App\Component\MarksheetCustomComponent;
use App\Component\PracticalCustomComponent;
use App\Component\ThoeryCustomComponent;
use App\Helper\CustomHelper;
use App\models\Address;
use App\models\Application;
use App\Models\ProvisionalResultView;
use App\models\Document;
use App\models\ExamResult;
use App\models\ExamSubject;
use App\Models\MarksheetMigrationRequest;
use App\models\Pastdata;
use App\models\RevisedCorrection;
use App\models\Student;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use stdClass; 
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use PDF;
use Response;
use Session;

/* Hall Ticket Addtional Model */

//use PDF;
/* Hall Ticket Addtional Model */

class ResultUpdateController extends Controller
{
    public $custom_component_obj = "";
    public $marksheet_component_obj = "";
    public $theory_custom_component_obj = " ";

    function __construct()
    {
        $this->middleware('permission:result_process_link', ['only' => ['result_process_steps']]);
        $this->middleware('permission:serachenrollment', ['only' => ['serachEnrollment']]);
        $this->middleware('permission:result_update', ['only' => ['index', 'edit_result', 'update_final_result']]);
        $this->middleware('permission:direct_provisional_results', ['only' => ['direct_provisional_results']]);
        $this->custom_component_obj = new CustomComponent;
        $this->marksheet_component_obj = new MarksheetCustomComponent;
        $this->theory_custom_component_obj = new ThoeryCustomComponent;
    }

    public function index(Request $request, $enrollment = null)
    {
        // $name=$this->marksheet_component_obj->qrcode();
        // dd($name);
        $title = "Result Update";
        $pastdataroute = 0;
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month_session = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $exam_year_session = $this->master_details($combo_name);
        $subjects = $this->custom_component_obj->subjectList();
        $permissions = CustomHelper::roleandpermission();
        $resultsyntax = array('999' => 'AB', '666' => 'SYCP', '777' => 'SYCT', '888' => 'SYC', 'P' => 'PASS', 'p' => 'PASS');
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
        $data = array();
        $data2 = array();
        $subjectcount = array();
        $studentdata = array();
        if (!empty($enrollment)) {
            $enrollment = Crypt::decrypt($enrollment);
            $data = $this->marksheet_component_obj->getexamsubjectsdata($enrollment);
            $data2 = count($this->marksheet_component_obj->getexamsubjectresultnullcount($enrollment));
            $exam_result2 = ExamResult::where('enrollment', '=', $enrollment)->orderBy('exam_year', 'desc')->orderBy('exam_month', 'asc')->first();
            $subjectcount = count($this->marksheet_component_obj->getallexamsubjectsdata($enrollment, $exam_result2->exam_year, $exam_result2->exam_month));
            $studentdata = Student::where('enrollment', '=', $enrollment)->first();
        }
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                'enrollment' => 'required||numeric|digits_between:11,15',
            ],
                [
                    'enrollment.required' => 'Enrollment is required',
                ]);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $subjects = $this->custom_component_obj->subjectList();
            $exam_result = ExamResult::where('enrollment', '=', $request->enrollment)->orderBy('exam_year', 'desc')->orderBy('exam_month', 'asc')->first();
            $data = $this->marksheet_component_obj->getexamsubjectsdata($request->enrollment);
            $subjectcount = count($this->marksheet_component_obj->getallexamsubjectsdata(@$request->enrollment, @$exam_result->exam_year, @$exam_result->exam_month));
            $studentdata = Student::where('enrollment', '=', $request->enrollment)->first();
            $data2 = count($this->marksheet_component_obj->getexamsubjectresultnullcount($request->enrollment));
            if (@$exam_result->is_temp_examresult == "111") {
                return redirect()->back()->with('error', 'The Examination has been Cancelled.');
            }
            if ($data->isEmpty()) {
                return redirect('updateindex')->with('error', 'Data not found')->withInput();
            }
            session()->flashInput($request->input());
        }
        return view('resultupdate.index', compact('breadcrumbs', 'resultsyntax', 'pastdataroute', 'subjectcount', 'studentdata', 'title', 'permissions', 'course', 'subjects', 'data', 'exam_month_session', 'exam_year_session', 'data2'));
    }

    public function edit_result($id = null, Request $request)
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
        $id = Crypt::decrypt($id);
        $data = DB::table('exam_subjects')->join('students', 'exam_subjects.student_id', '=', 'students.id')
            ->whereNull('students.deleted_at')
            ->where([['exam_subjects.id', '=', $id]])->first(array('exam_subjects.*', 'students.enrollment', 'students.stream'));
        $conditions = ['student_id' => $data->student_id, 'subject_id' => $data->subject_id];
        $toc_data = DB::table('toc_marks')->where($conditions)->whereNull('deleted_at')->first();
        $subjects = $subjects["$data->subject_id"];
        $subject_detalis = $this->theory_custom_component_obj->_getSubjectDetail(@$data->subject_id);
        $svdata = array();
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
                if ($request->final_theory_marks != 999) {
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
            }
            $exam_subject_log = $obj_controller->updateStudentLog($tablename, $id, $form_type);
            $master = ExamSubject::find($id)->update($svdata);
            $allow_update_provisionl_result = config("global.allow_update_provisional_result");
            if ($allow_update_provisionl_result == true) {
                $provisional_update = $this->update_provisional_subject_result($id);
            }
            if ($master) {
                return redirect()->route('updateindex', [Crypt::encrypt($enrollment)])->with('message', 'Result Updated successfully.');
            } else {
                return back()->with('error', 'Result Not Updated successfully');
            }
        }


        if (!empty($toc_data)) {
            $request->session()->now('message', 'this is Toc Subject.');
        }
        return view('resultupdate.editresult', compact('title', 'breadcrumbs', 'data', 'result', 'subjects', 'subject_detalis', 'toc_data'));

    }

    public function update_provisional_subject_result($id = null)
    {
        $current_result_year = config("global.current_result_session_year_id");
        $current_result_month = config("global.current_result_session_month_id");
        $exam_subject_data = ExamSubject::where('id', $id)->whereNull('deleted_at')->first();
        if ($current_result_year != $exam_subject_data->exam_year || $current_result_month != $exam_subject_data->exam_month) {
            return true;
        }
        if (@$exam_subject_data->student_id) {
            DB::table('provisional_exam_subjects')
                ->where('student_id', $exam_subject_data->student_id)
                ->where('subject_id', $exam_subject_data->subject_id)
                ->where('exam_year', $exam_subject_data->exam_year)
                ->where('exam_month', $exam_subject_data->exam_month)
                ->Delete();
            $svdata = [
                'student_id' => @$exam_subject_data->student_id,
                'enrollment' => @$exam_subject_data->enrollment,
                'pastdata_id' => @$exam_subject_data->pastdata_id,
                'subject_id' => @$exam_subject_data->subject_id,
                'is_additional' => @$exam_subject_data->is_additional,
                'final_theory_marks' => @$exam_subject_data->final_theory_marks,
                'final_practical_marks' => @$exam_subject_data->final_practical_marks,
                'sessional_marks' => @$exam_subject_data->sessional_marks,
                'sessional_marks_reil_result_20' => @$exam_subject_data->sessional_marks_reil_result_20,
                'sessional_marks_reil_result' => @$exam_subject_data->sessional_marks_reil_result,
                'total_marks' => @$exam_subject_data->total_marks,
                'final_result' => @$exam_subject_data->final_result,
                'exam_year' => @$exam_subject_data->exam_year,
                'exam_month' => @$exam_subject_data->exam_month,
                'course' => @$exam_subject_data->course,
                'is_sessional_mark_entered' => @$exam_subject_data->is_sessional_mark_entered,
                'is_temp_exam_subject' => @$exam_subject_data->is_temp_exam_subject,
                'is_supplementary_subject' => @$exam_subject_data->is_supplementary_subject,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            $datainsert = DB::table('provisional_exam_subjects')->insert($svdata);
            if (@$datainsert) {
                return true;
            }
        }

    }

    public function update_final_result($enrollment = null, Request $request)
    {
        $enrollment = Crypt::decrypt($enrollment);
        $permissions = CustomHelper::roleandpermission();
        $pagetitle = "Note:- Please check the Subject wise Numbers Entered & Update the Total Number and Final Result before submitting.";
        $title = "Update Marksheet";
        $obj_controller = new Controller();
        $combo_name = 'final_result';
        $finalresults = $this->master_details($combo_name);
        $tablename = 'exam_results';
        $studentdata = Student::where('enrollment', $enrollment)->first(['id', 'enrollment']);
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
        //$data2=ExamSubject::where('student_id',$studentdata->id)->latest('exam_year')->first(['exam_year','exam_month']);
        $data2 = ExamSubject::where('student_id', $studentdata->id)->orderBy('exam_year', 'desc')->orderBy('exam_month', 'asc')->first(['exam_year', 'exam_month']);

        $data = $this->marksheet_component_obj->getallexamsubjectsdata($enrollment, $data2->exam_year, $data2->exam_month);
        $exam_result = ExamResult::where('enrollment', '=', $enrollment)->orderBy('exam_year', 'desc')->orderBy('exam_month', 'asc')->first();

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
            } else if ($request->final_result == 2) {
                $svdata['final_result'] = 'RWH';
            } else {
                $svdata['final_result'] = 'XXXX';

            }

            $exam_subject_log = $obj_controller->updateStudentLog($tablename, $id, $form_type);
            $result = ExamResult::find($id)->update($svdata);
            $provisional_allow = config("global.allow_update_provisional_result");

            if ($provisional_allow == true) {
                $update_provisional_result = $this->update_provisional_result($id);
            }
            if ($result) {
                return redirect()->route('updateindex', Crypt::encrypt($enrollment))->with('message', 'Result Update Successfully');
            } else {
                return back()->with('error', 'Result Not Update Successfully');
            }
        }
        return view('resultupdate.finalupdate', compact('breadcrumbs', 'permissions', 'resultsyntax', 'subjects', 'pagetitle', 'title', 'data', 'exam_result', 'finalresults'));
    }

    public function update_provisional_result($id = null)
    {
        $current_result_year = config("global.current_result_session_year_id");
        $current_result_month = config("global.current_result_session_month_id");
        $exam_result = ExamResult::where('id', '=', $id)->first();
        if ($current_result_year != $exam_result->exam_year || $current_result_month != $exam_result->exam_month) {
            return true;
        }


        if (@$exam_result->student_id) {
            DB::table('provisional_exam_results')
                ->where('student_id', $exam_result->student_id)
                ->where('exam_year', $exam_result->exam_year)
                ->where('exam_month', $exam_result->exam_month)
                ->Delete();
            $svdata = [
                'student_id' => @$exam_result->student_id,
                'enrollment' => @$exam_result->enrollment,
                'final_result' => @$exam_result->final_result,
                'exam_year_id' => @$exam_result->exam_year_id,
                'exam_year' => @$exam_result->exam_year,
                'exam_month' => @$exam_result->exam_month,
                'result_date' => @$exam_result->result_date,
                'revised' => @$exam_result->revised,
                'total_marks' => @$exam_result->total_marks,
                'percent_marks' => @$exam_result->percent_marks,
                'is_temp_examresult' => @$exam_result->is_temp_examresult,
                'is_deleted' => @$exam_result->is_deleted,
                'supplementary' => @$exam_result->supplementary,
                'additional_subjects' => @$exam_result->additional_subjects,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $datainsert = DB::table('provisional_exam_results')->insert($svdata);

            if (@$datainsert) {
                return true;
            }
        } else {
            return false;
        }

    }

    public function finddata(Request $request)
    {
        $title = "Search";
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
        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",

            ),
        );
        $exportBtn = array();
        if (!empty($request->all())) {
            $validator = Validator::make($request->all(), [
                'enrollment' => 'required|numeric|digits_between:10,15',
            ],
                [
                    'enrollment.required' => 'Enrollment is Required.',
                    'enrollment.numeric' => 'The Enrollment must be a number.',
                    'enrollment.digits_between' => 'Please enter valid enrollment.',

                ],
            );
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $enrollment = $request->enrollment;
            return redirect()->route('finalupdate', $enrollment)->with('message', "Enrollment Found.");

        }
        return view('resultupdate.findmarksheets', compact('title', 'breadcrumbs', 'filters', 'exportBtn'));
    }

    public function download_duplicate_marksheet_pdf($type = null, $enrollment = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $title = "Duplicate Marksheet";
        $table_id = "Duplicate Marksheet";
        $pastdatadocument = config("global.PAST_DATA_DOCUMENT");
        $pastdataurl = config("global.pastdata_document");
        $formId = ucfirst(str_replace(" ", "-", $title));
        $enrollment = Crypt::decrypt($enrollment);
        $type = Crypt::decrypt($type);
        $studentdata = Student::where('enrollment', $enrollment)->first(['id', 'enrollment']);
        $marksheetsarray = $combo_name = 'marksheet_type';
        $marksheetsarray = $this->master_details($combo_name);
        if (@$type == 1) {
            $marksheet_type = 'Duplicate';
            $marksheet_verification_role_id = config("global.marksheet_verification");
            $currentRoleId = Session::get('role_id');
            if ($marksheet_verification_role_id == $currentRoleId) {
                $marksheet_type = 'Verification';
            }
        } else {
            $marksheet_type = 'Revised';
        }
        $data = ExamResult::where('enrollment', '=', $enrollment)->get();
        $documents = '';
        $totalMarks = 0;
        $grandFinalTotalMarks = 0;

        if (!($enrollment > 0) || $enrollment == null || $enrollment == '') {
            return redirect()->route('finalupdate', $enrollment)->with('message', "Enrollment is not in correct format.");
        }
        $serial_number = $this->marksheet_component_obj->getSerialNumber($enrollment);
        $examresultfields = array('final_result', 'exam_month', 'exam_year', 'total_marks', 'percent_marks', 'additional_subjects');

        $final_result = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($examresultfields);

        $pastInfo = Pastdata::where('ENROLLNO', '=', $enrollment)->orderBy('id', 'DESC')->first();

        if (!empty($pastInfo) && !empty($final_result)) {
            $field = ["pastdatas.ENROLLNO  as enrollment", "pastdatas.CLASS  as course", "pastdatas.NAME as name", "pastdatas.FNAME as father_name", "pastdatas.MNAME as mother_name", "pastdatas.DOB as dob"];
            $student = Pastdata::where('ENROLLNO', '=', $enrollment)->orderBy('id', 'DESC')->first($field);
        } else {
            $student = Student::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first();
        }

        $resultDate = '';

        if (isset($final_result->exam_month) && isset($final_result->exam_year)) {
            $combination = $final_result->exam_month . ' ' . $final_result->exam_year;
        }
        $courseVal = '';
        $resultsyntax = array('999' => 'AB', '666' => 'SYCP', '777' => 'SYCT', '888' => 'SYC', 'P' => 'P', 'PASS' => 'P', 'pass' => 'P');

        if (!empty($student)) { //fetch data from students
            $application = Application::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();
            $student->display_exam_month_year = $this->marksheet_component_obj->getDisplayExamMonthYear($combination);
            $newexamresultfields = array('exam_year', 'exam_month', 'result_date');
            $final_result_data = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($newexamresultfields);

            if (isset($final_result_data->result_date) && $final_result_data->result_date != "") {
                $resultDate = strtotime($final_result_data->result_date);
                $resultDate = date("d-m-Y", $resultDate);
            }

            $documents = Document::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();
            $courseVal = $student->course;
            $address = Address::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();
            $findInFlag = 'Student';
            @$dtarr = explode('-', @$student->dob);
            @$student->dob = @$dtarr[2] . "-" . @$dtarr[1] . "-" . @$dtarr[0];

        } else {
            if (empty($final_result)) {
                $courseVal = $pastInfo->CLASS;
                $subjectCodeIds = $this->marksheet_component_obj->_getSubjectsCodeId($courseVal);
                $final_result['final_result'] = $pastInfo->RESULT;
                $final_result['total_marks'] = $pastInfo->TOTAL_MARK;
                $final_result['percent_marks'] = $pastInfo->Percentage;
                @$dtarr = explode('-', @$pastInfo->ResultDate);
                $resultDate = @$dtarr[2] . "-" . @$dtarr[1] . "-" . @$dtarr[0];
                if (isset($combination) && $combination != '') {
                    $student->display_exam_month_year = $this->marksheet_component_obj->getDisplayExamMonthYear($combination);
                }
            } else {
                if (@$final_result->exam_year) {
                    $examSubjectsMarksData = $this->marksheet_component_obj->getallexamsubjectsdata($enrollment, $final_result->exam_year, $final_result->exam_month);

                } else {
                    $examSubjectsMarksData = array();
                }

                $newexamresultfields = array('exam_year', 'exam_month', 'result_date');
                $final_result_data = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($examresultfields);

                if (isset($final_result_data->result_date) && $final_result_data->result_date != "") {
                    $resultDate = strtotime($final_result_data->result_date);
                    $resultDate = date("d-m-Y", $resultDate);

                }
                @$student->display_exam_month_year = $this->marksheet_component_obj->getDisplayExamMonthYear(@$combination);
            }

            @$student['ai_code'] = substr($pastInfo->ENROLLNO, 0, 5);
            @$ai_code = $student['ai_code'];
            @$student['enrollment'] = $pastInfo->ENROLLNO;
            @$student['name'] = $pastInfo->NAME;
            @$student['father_name'] = $pastInfo->FNAME;
            @$student['mother_name'] = $pastInfo->MNAME;
            @$pastInfo->DOB = $new_date = $pastInfo->DOB;//date('d/m/Y', strtotime($pastInfo['Pastdata']['DOB']));
            @$student['dob'] = @$pastInfo->DOB;
            @$dtarr = explode('-', $student['dob']);
            @$student['dob'] = @$dtarr[2] . "-" . @$dtarr[1] . "-" . @$dtarr[0];
            @$student['course'] = $pastInfo->CLASS;
            @$student['yy'] = $yy = substr($pastInfo->ENROLLNO, 5, 2);
            @$student['student_code'] = $st_code = substr($pastInfo->ENROLLNO, 7);
            @$courseVal = $pastInfo->CLASS;
            @$student['display_exam_month_year'] = $pastInfo->EX_YR;
            @$addressTemp = $pastInfo->ADDRESS;
            if (isset($pastInfo->DISTRICT) && !empty($pastInfo->DISTRICT)) {
                @$addressTemp .= ',' . $pastInfo->DISTRICT;
            }
            if (isset($pastInfo->STATE) && !empty($pastInfo->STATE)) {
                @$addressTemp .= ',' . $pastInfo->STATE;
            }
            if (isset($pastInfo->PIN) && !empty($pastInfo->PIN)) {
                @$addressTemp .= '-' . $pastInfo->PIN;
            }
            @$address['address1'] = $addressTemp;
            @$address['address2'] = '';
            @$address['address3'] = '';
            @$address['city_name'] = '';
            @$address['pincode'] = '';
            if (isset($pastInfo->MOBILE) && !empty($pastInfo->MOBILE)) {
                @$student['mobile'] = $pastInfo->MOBILE;
            }
            if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'GENERAL_ADM' || $pastInfo->ERTYPE == 'STREAM2') {
                $student['adm_type'] = 1;
            } else if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'READMISSION') {
                $student['adm_type'] = 2;
            } else if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'PARTADMISSION') {

                $student['adm_type'] = 3;
            } else if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'IMPROVEMENT') {

                $student['adm_type'] = 4;
            } else if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'SUPPLEMENTARY') {

                $student['adm_type'] = 1;    //11 for supplementary ertype and admission type
            } else {//if ertype in pastdata table is balnk or null then use gen_adm adm_type

                $student['adm_type'] = 1;
            }
            // $subjectCodes = $this->marksheet_component_obj->_getSubjectCodes($courseVal);

        }

        $sub_enrollment = substr($enrollment, -6, 2);
        if ($sub_enrollment >= '17') {
            $examSubjectsMarksData = $this->marksheet_component_obj->getallexamsubjectsdata($enrollment, $final_result->exam_year, $final_result->exam_month);

        } else {
            $examsubjectfields = array('id', 'subject_id', 'final_theory_marks', 'final_practical_marks', 'sessional_marks_reil_result', 'total_marks', 'final_result');
            $examSubjectsMarksData = ExamSubject::where('enrollment', '=', $enrollment)->orderBy('exam_year', 'desc')->orderBy('exam_month', 'asc')->orderBy('subject_id', 'desc')->groupBy('subject_id')->get($examsubjectfields);
        }

        $subjectCodeIds = $this->marksheet_component_obj->_getSubjectsCodeId($courseVal);
        $examsubjectcount = count($examSubjectsMarksData);

        if (($examsubjectcount == 0)) {
            $examSubjectsMarksData = array();
            if (isset($pastInfo->FRES1) && $pastInfo->FRES1 != '') {
                $examSubjectsMarksData[0]['subject_id'] = @$subjectCodeIds[@$pastInfo->EX_SUB1];
                $examSubjectsMarksData[0]['final_theory_marks'] = $pastInfo->FTM1;
                $examSubjectsMarksData[0]['final_practical_marks'] = $pastInfo->FPM1;
                $examSubjectsMarksData[0]['sessional_marks_reil_result'] = $pastInfo->fst1;
                $examSubjectsMarksData[0]['total_marks'] = $pastInfo->FTOT1;
                $examSubjectsMarksData[0]['final_result'] = $pastInfo->FRES1;
                $examSubjectsMarksData[0]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks(@$subjectCodeIds[@$pastInfo->EX_SUB1]);
                $examSubjectsMarksData[0]['num_words'] = $this->marksheet_component_obj->numberInWord($examSubjectsMarksData[0]['total_marks']);
                $examSubjectsMarksData[0]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[0]['total_marks']);
            }

            if (isset($pastInfo->FRES2) && $pastInfo->FRES2 != '') {
                $examSubjectsMarksData[1]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB2];
                $examSubjectsMarksData[1]['final_theory_marks'] = $pastInfo->FTM2;
                $examSubjectsMarksData[1]['final_practical_marks'] = $pastInfo->FPM2;
                $examSubjectsMarksData[1]['sessional_marks_reil_result'] = $pastInfo->fst2;
                $examSubjectsMarksData[1]['total_marks'] = $pastInfo->FTOT2;
                $examSubjectsMarksData[1]['final_result'] = $pastInfo->FRES2;
                $examSubjectsMarksData[1]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB2]);
                $examSubjectsMarksData[1]['num_words'] = $this->marksheet_component_obj->numberInWord($examSubjectsMarksData[1]['total_marks']);
                $examSubjectsMarksData[1]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[1]['total_marks']);

            }

            if (isset($pastInfo->FRES3) && $pastInfo->FRES3 != '') {
                $examSubjectsMarksData[2]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB3];
                $examSubjectsMarksData[2]['final_theory_marks'] = $pastInfo->FTM3;
                $examSubjectsMarksData[2]['final_practical_marks'] = $pastInfo->FPM3;
                $examSubjectsMarksData[2]['sessional_marks_reil_result'] = $pastInfo->fst3;
                $examSubjectsMarksData[2]['total_marks'] = $pastInfo->FTOT3;
                $examSubjectsMarksData[2]['final_result'] = $pastInfo->FRES3;
                $examSubjectsMarksData[2]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB3]);
                $examSubjectsMarksData[2]['num_words'] = $this->marksheet_component_obj->numberInWord($examSubjectsMarksData[2]['total_marks']);
                $examSubjectsMarksData[2]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[2]['total_marks']);

            }

            if (isset($pastInfo->FRES4) && $pastInfo->FRES4 != '') {
                $examSubjectsMarksData[3]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB4];
                $examSubjectsMarksData[3]['final_theory_marks'] = $pastInfo->FTM4;
                $examSubjectsMarksData[3]['final_practical_marks'] = $pastInfo->FPM4;
                $examSubjectsMarksData[3]['sessional_marks_reil_result'] = $pastInfo->fst4;
                $examSubjectsMarksData[3]['total_marks'] = $pastInfo->FTOT4;
                $examSubjectsMarksData[3]['final_result'] = $pastInfo->FRES4;
                $examSubjectsMarksData[3]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB4]);
                $examSubjectsMarksData[3]['num_words'] = $this->marksheet_component_obj->numberInWord($examSubjectsMarksData[3]['total_marks']);
                $examSubjectsMarksData[3]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[3]['total_marks']);

            }

            if (isset($pastInfo->FRES5) && $pastInfo->FRES5 != '') {
                $examSubjectsMarksData[4]['subject_id'] = @$subjectCodeIds[@$pastInfo->EX_SUB5];
                $examSubjectsMarksData[4]['final_theory_marks'] = $pastInfo->FTM5;
                $examSubjectsMarksData[4]['final_practical_marks'] = $pastInfo->FPM5;
                $examSubjectsMarksData[4]['sessional_marks_reil_result'] = $pastInfo->fst5;
                $examSubjectsMarksData[4]['total_marks'] = $pastInfo->FTOT5;
                $examSubjectsMarksData[4]['final_result'] = $pastInfo->FRES5;
                $examSubjectsMarksData[4]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks(@$subjectCodeIds[$pastInfo->EX_SUB5]);
                $examSubjectsMarksData[4]['num_words'] = $this->marksheet_component_obj->numberInWord($examSubjectsMarksData[4]['total_marks']);
                $examSubjectsMarksData[4]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[4]['total_marks']);
            }

            if (isset($pastInfo->FRES6) && $pastInfo->FRES6 != '') {

                $examSubjectsMarksData[5]['subject_id'] = @$subjectCodeIds[@$pastInfo->EX_SUB9];
                $examSubjectsMarksData[5]['final_theory_marks'] = $pastInfo->FTM6;
                $examSubjectsMarksData[5]['final_practical_marks'] = $pastInfo->FPM6;
                $examSubjectsMarksData[5]['sessional_marks_reil_result'] = $pastInfo->fst6;
                $examSubjectsMarksData[5]['total_marks'] = $pastInfo->FTOT6;
                $examSubjectsMarksData[5]['final_result'] = $pastInfo->FRES6;
                $examSubjectsMarksData[5]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks(@$subjectCodeIds[@$pastInfo->EX_SUB9]);
                $examSubjectsMarksData[5]['num_words'] = $this->marksheet_component_obj->numberInWord($examSubjectsMarksData[5]['total_marks']);
                $examSubjectsMarksData[5]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[5]['total_marks']);

            }

            if (isset($pastInfo->FRES7) && $pastInfo->FRES7 != '') {
                $examSubjectsMarksData[6]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB7];
                $examSubjectsMarksData[6]['final_theory_marks'] = $pastInfo->FTM7;
                $examSubjectsMarksData[6]['final_practical_marks'] = $pastInfo->FPM7;
                $examSubjectsMarksData[6]['sessional_marks_reil_result'] = $pastInfo->fst7;
                $examSubjectsMarksData[6]['total_marks'] = $pastInfo->FTOT7;
                $examSubjectsMarksData[6]['final_result'] = $pastInfo->FRES7;
                $examSubjectsMarksData[6]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB7]);
                $examSubjectsMarksData[6]['num_words'] = $this->marksheet_component_obj->numberInWord($examSubjectsMarksData[6]['total_marks']);
                $examSubjectsMarksData[6]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[6]['total_marks']);
            }

        } else {
            if (!empty($examSubjectsMarksData)) {
                $k = 0;
                foreach ($examSubjectsMarksData as $v) {
                    $examSubjectsMarksData[$k]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks($v->subject_id);
                    $examSubjectsMarksData[$k]['num_words'] = $this->marksheet_component_obj->numberInWord($v->total_marks);
                    $examSubjectsMarksData[$k]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($v->total_marks);
                    $totalMarks = $totalMarks + $examSubjectsMarksData[$k]['max_marks'];
                    $grandFinalTotalMarks = $grandFinalTotalMarks + $v->total_marks;
                    $k++;
                }
            }
        }
        $dobInWords = null;
        if (@($student['dob'])) {
            $dobInWords = $this->marksheet_component_obj->getDObInWords($student['dob']);
        }

        $subjects = $this->marksheet_component_obj->_getSubjectsForMarksheet($courseVal);

        /* qr Code */
        $imagepath = asset('public/qrcode/enrollment/' . $enrollment . '.png');
        $custom_component_obj = new CustomComponent;
        $alpha = $this->toAlpha($enrollment);
        $url = URL::to("/qr?$alpha");//Rohit
        $qrcode = $this->marksheet_component_obj->qrcode($url, $enrollment);
        $imagepath = asset('public/qrcode/enrollment/' . $enrollment . '.png');
        $barcode_img = '<img src="' . $imagepath . '" alt=barcode-' . $enrollment . ' style="font-size:0;position:relative;width:65px;height:65px;" >';

        /* bar Code
		$imagepath = asset('public/barcode/enrollment/'.$enrollment.'.png');
		$custom_component_obj = new CustomComponent;
		$barcode = $custom_component_obj->barcode($enrollment);
		$barcode_img = '<img src="'. $imagepath.'" alt=barcode-'.$enrollment.' style="font-size:0;position:relative;width:132px;height:20px;" >';
		*/

        //return View('resultupdate.marksheet_print_design', compact('type','pastdataurl','pastdatadocument','barcode_img','pastInfo','formId','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','examSubjectsMarksData','subjectCodeIds','subjects','resultDate','dobInWords','documents','marksheet_type'));
        $pdf = PDF::loadView('resultupdate.marksheet_print_design', compact('type', 'pastdataurl', 'pastdatadocument', 'barcode_img', 'pastInfo', 'formId', 'final_result', 'enrollment', 'serial_number', 'student', 'dobInWords', 'resultsyntax', 'examSubjectsMarksData', 'subjectCodeIds', 'subjects', 'resultDate', 'dobInWords', 'documents', 'marksheet_type'));
        $pdf->setTimeout(60000);
        $path = public_path('resultupdate\marksheet_' . $marksheet_type . '_' . $enrollment . '-' . date('d-m-Y-H-i-s') . '.pdf');//jahan pdf save karni hai
        $pdf->save($path, $pdf, true);
        return (Response::download($path));
    }

    public function qr(Request $request)
    {
        $enrollment = array_keys($request->all());
        $alpha = @$enrollment[0];
        $enrollment = $this->toNum($alpha);
        $crpteno = Crypt::encrypt($enrollment);
        $url = URL::to("/resultprevious?enrollment={$crpteno}&qr_marksheets=1");
        return redirect($url);
    }

    public function download_duplicate_certificate_pdf($type = null, $enrollment = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $title = "Duplicate Certificate";
        $table_id = "Duplicate Certificate";
        $enrollment = Crypt::decrypt($enrollment);
        $type = Crypt::decrypt($type);
        $combination = '';
        $formId = ucfirst(str_replace(" ", "-", $title));

        if (@$type == 1) {
            $marksheet_type = 'Duplicate';
        } else {
            $marksheet_type = 'Revised';
        }

        $documents = '';
        $totalMarks = 0;
        $resultDate = '';
        $grandFinalTotalMarks = 0;

        if (!($enrollment > 0) || $enrollment == null || $enrollment == '') {
            return redirect()->route('finalupdate', $enrollment)->with('message', "Enrollment is not in correct format.");
        }

        $serial_number = $this->marksheet_component_obj->getSerialNumbercertficate($enrollment);
        $findInFlag = 'Pastdata';
        $pastInfo = Pastdata::where('ENROLLNO', '=', $enrollment)->orderBy('id', 'DESC')->first();
        $examresultfields = array('final_result', 'exam_month', 'exam_year', 'total_marks', 'percent_marks');
        $final_result = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($examresultfields);

        if (!empty(@$final_result) && (@$final_result->final_result != 'PASS')) {
            return redirect()->back()->with('error', "Student Not Pass");
        }


        if (isset($final_result->exam_month) && isset($final_result->exam_year)) {
            $combination = $final_result->exam_month . ' ' . $final_result->exam_year;
        }

        if (!empty($pastInfo) && !empty($final_result)) {
            $field = ["pastdatas.ENROLLNO  as enrollment", "pastdatas.CLASS  as course", "pastdatas.NAME as name", "pastdatas.FNAME as father_name", "pastdatas.MNAME as mother_name", "pastdatas.DOB as dob"];
            $student = Pastdata::where('ENROLLNO', '=', $enrollment)->orderBy('id', 'DESC')->first($field);
        } else {
            $student = Student::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first();
        }

        $resultsyntax = array('999' => 'AB', '666' => 'SYCP', '777' => 'SYCT', '888' => 'SYC', 'P' => 'P');

        if (!empty($student)) {
            $application = Application::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();
            $student->display_exam_month_year = $this->marksheet_component_obj->getDisplayExamMonthYear($combination);
            $newexamresultfields = array('exam_year', 'exam_month', 'result_date');
            $final_result_data = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($newexamresultfields);

            if (isset($final_result_data->result_date) && $final_result_data->result_date != "") {
                $resultDate = strtotime($final_result_data->result_date);
                $resultDate = date("d-m-Y", $resultDate);
            }
            $courseVal = $student->course;
            $address = Address::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();
            $findInFlag = 'Student';
            @$dtarr = explode('-', @$student['dob']);
            $student['dob'] = @$dtarr[2] . "-" . @$dtarr[1] . "-" . @$dtarr[0];
        } else {
            @$dtarr = explode('-', @$pastInfo->ResultDate);
            $resultDate = @$dtarr[2] . "-" . @$dtarr[1] . "-" . @$dtarr[0];
            $final_result['final_result'] = $pastInfo->RESULT;
            $student['ai_code'] = substr($pastInfo->ENROLLNO, 0, 5);
            $ai_code = $student['ai_code'];
            $student['display_exam_month_year'] = $pastInfo->EX_YR;
            $student['enrollment'] = $pastInfo->ENROLLNO;
            $student['name'] = $pastInfo->NAME;
            $student['father_name'] = $pastInfo->FNAME;
            $student['mother_name'] = $pastInfo->MNAME;
            @$pastInfo->DOB = @$new_date = @$pastInfo->DOB;//date('d/m/Y', strtotime($pastInfo['Pastdata']['DOB']));
            $student['dob'] = @$pastInfo->DOB;
            @$dtarr = explode('-', @$student['dob']);
            @$student['dob'] = @$dtarr[2] . "-" . @$dtarr[1] . "-" . @$dtarr[0];

        }
        $dobInWords = null;
        if (@($student['dob'])) {
            $dobInWords = $this->marksheet_component_obj->getDObInWords($student['dob']);
        }
        /* Get Barcode code
		$imagepath = asset('public/barcode/enrollment/'.$enrollment.'.png');
		$custom_component_obj = new CustomComponent;
		$barcode = $custom_component_obj->barcode($enrollment);
		$barcode_img = '<img src="'.$imagepath.'" alt=barcode-'.$enrollment.' style="font-size:0;position:relative;width:132px;height:20px;" >';
		*/
        /* qr Code */
        $alpha = $this->toAlpha($enrollment);
        $url = URL::to("/qr?$alpha");//Rohit
        $qrcode = $this->marksheet_component_obj->qrcode($url, $enrollment);
        $imagepath = asset('public/qrcode/enrollment/' . $enrollment . '.png');
        $barcode_img = '<img src="' . $imagepath . '" alt=barcode-' . $enrollment . ' style="font-size:0;position:relative;width:58px;height:58px;" >';


        /* view file code
		return view('resultupdate.certificate_print_design', compact('barcode_img','formId','marksheet_type','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','resultDate','dobInWords','documents','combination'));
		 */


        $pdf = PDF::loadView('resultupdate.certificate_print_design', compact('barcode_img', 'formId', 'marksheet_type', 'final_result', 'enrollment', 'serial_number', 'student', 'dobInWords', 'resultsyntax', 'resultDate', 'dobInWords', 'documents', 'combination', 'marksheet_type'));
        $pdf->setTimeout(60000);
        $path = public_path('resultupdate\Certificate_' . $marksheet_type . '_' . $enrollment . '-' . date('d-m-Y-H-i-s') . '.pdf');//jahan pdf save karni hai
        $pdf->save($path, $pdf, true);
        return (Response::download($path));
    }

    public function Printmarksheetcertificate($enrollment = null)
    {
        $enrollment = Crypt::decrypt($enrollment);
        $title = "Download Marksheet And Certificate";
        $studentData = Student::where('enrollment', $enrollment)->first(['id', 'enrollment']);
        $marksheet_verification_role_id = config("global.marksheet_verification");
        $currentRoleId = Session::get('role_id');
        $respones = $this->updateFlagInMarkMigrationRequest('marksheet_migration_status', @$studentData->id, 1);
        if ($marksheet_verification_role_id == $currentRoleId) {
            $title = "Verify Marksheet";
        }
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
        return view('resultupdate.printmarksheetcertificate', compact('title', 'breadcrumbs', 'enrollment'));
    }

    public function result(Request $request)
    {
        $showStatus = $this->_getCheckAllowToCheckResult();

        if (!$showStatus) {
            return redirect()->route("landing")->with('error', 'You are not allowed to see the Result. Yet not declared !');
        }
        $custom_component_obj = new CustomComponent;
        $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult();
        if (@$resultCheckStatus) {
        } else {
            return redirect()->route("landing")->with('error', 'Result yet not published!');
        }

        $title = "Download Result";
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
            $inputs = $request->all();
            $request->validate(['enrollment' => 'required|numeric|digits:11', 'dob' => 'required', 'captcha' => 'required|numeric']);

            $captchaStatus = $custom_component_obj->checkCaptcha($inputs);
            if ($captchaStatus == false) {
                return redirect()->back()->with('error', 'Error : Captcha is invalid');
            }

            $combo_name = 'course';
            $course = $this->master_details($combo_name);
            $combo_name = 'result_type';
            $result_type = $this->master_details($combo_name);
            // $combo_name = 'exam_session';$exam_session = $this->master_details($combo_name);

            $combo_name = 'result_session';
            $result_session = $this->master_details($combo_name);
            $current_exam_month_id = Config::get('global.current_result_session_month_id');
            $result_session = $result_session[$current_exam_month_id];
            $subject_list = $this->subjectList();

            $students = $custom_component_obj->getresultstudentdata($request->enrollment, $request->dob);

            $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult(@$students->course);
            if (@$resultCheckStatus) {
            } else {
                return redirect()->back()->with('error', $course[$students->course] . ' Result yet not published!');
            }
            if (!empty($students)) {
                $studentexamsubjects = $custom_component_obj->getresultstudentalldata($students->student_id);
                if ($studentexamsubjects == false) {
                    return redirect()->back()->with('error', 'Result Not Declare.');
                }
                return view('resultupdate.results', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type'));
            } else {
                return redirect()->back()->with('error', 'Result not found');
            }
        }
        $captchaImage = $custom_component_obj->generateCaptcha(1, 20, 150, 30);
        $allowYearCombo = $custom_component_obj->_getAllowYearCombo();
        return view('resultupdate.result', compact('captchaImage', 'title', 'breadcrumbs', 'allowYearCombo'));
    }

    public function resultdownloadpdf($enrollment = null, $dob = null)
    {
        $custom_component_obj = new CustomComponent;
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'result_session';
        $result_session = $this->master_details($combo_name);
        $combo_name = 'result_type';
        $result_type = $this->master_details($combo_name);
        $current_exam_month_id = Config::get('global.current_result_session_month_id');
        $result_session = $result_session[$current_exam_month_id];

        $subject_list = $this->subjectList();
        $students = $custom_component_obj->getresultstudentdata($enrollment, $dob);
        if (!empty($students)) {
            $studentexamsubjects = $custom_component_obj->getresultstudentalldata($students->student_id);
            $pdf = PDF::loadView('resultupdate.provisionalmarksheet', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type'));
            return $pdf->download('provisional_marksheet.pdf');
        }
    }

    public function serachEnrollment(Request $request)
    {
        $page_title = "Search Enrollment";
        if ($request->isMethod('PUT')) {
            $student = new Student;
            $validator = Validator::make($request->all(), $student->rulessessionalmarks);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
			$priter_not_allowed_exam_year = config::get('global.priter_not_allowed_exam_year');
			$priter_not_allowed_exam_month = config::get('global.priter_not_allowed_exam_month');
            $enrollment = $request->enrollment;
			$examresult = DB::table('exam_results')->where('enrollment', '=', $enrollment)->orderBy('id', 'desc')->whereNull('deleted_at')
				->first(['enrollment', 'is_temp_examresult','exam_month','exam_year']);
			 
			if(@$examresult->exam_year == @$priter_not_allowed_exam_year && @$examresult->exam_month == @$priter_not_allowed_exam_month){
				   return redirect()->back()->with('error', 'The Enrollment not allowed to view the result.');
			}
            if (@$examresult->is_temp_examresult == "111") {
                return redirect()->back()->with('error', 'The Examination has been Cancelled.');
            }
            $pastdata = Pastdata::where('ENROLLNO', '=', $enrollment)->orderBy('id', 'desc')->whereNull('deleted_at')->first('ENROLLNO');
            if (empty($examresult) && empty($pastdata)) {
                return back()->with('error', 'Enrollment not found');
            } else {

                return redirect()->route('printduplicatemarksheetcertificate', Crypt::encrypt($enrollment))->with('message', 'Enrollment Found');
            }
        }
        return view('resultupdate.serachenrollment', compact('page_title'));
    }

    public function addSubject($enrollment = null, Request $request)
    {
        $title = "Add Subjects";
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
        $combo_name = 'admission_sessions';
        $exam_year = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);

        $permissions = CustomHelper::roleandpermission();
        $enrollment = Crypt::decrypt($enrollment);
        $data = Student::where('enrollment', '=', $enrollment)->first();
        $subjects = $this->custom_component_obj->subjectList($data->course);
        if ($request->isMethod('PUT')) {
            $svdata = ['student_id' => $data->id, 'enrollment' => $enrollment, 'subject_id' => $request->subject_id, 'stream' => $data->stream, 'exam_year' => $request->exam_year, 'exam_month' => $request->exam_month, 'course' => $data->course];
            $data2 = ExamSubject::create($svdata);
            if ($data2) {
                return redirect()->route('updateindex', Crypt::encrypt($enrollment))->with('message', 'Result Update Successfully');
            } else {
                return back()->with('error', 'Result Not Update Successfully');
            }
        }
        return view('resultupdate.addsubjects', compact('title', 'breadcrumbs', 'data', 'subjects', 'exam_year', 'permissions', 'exam_month'));

    }

    public function resultsprocess()
    {
        $title = "Result Process Steps";
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
        $base_url = Config::get('global.APP_URL');
        $resultprocessallow = $this->custom_component_obj->resutlprocessallow();
        if ($resultprocessallow != true) {
            return back()->with('error', 'Not allow to Result process.');
        }

        $arr[0]['label'] = 'Result Process Fresh.';
        $arr[0]['link'] = 'result_process/show_combination/0/1';
        $arr[1]['label'] = 'Supplementary Result Process.';
        $arr[1]['link'] = 'supp_result_process/show_combination/0/1';
        $arr[2]['label'] = 'Import Processed Result for 10th and 12th';
        $arr[2]['link'] = 'import_prcessed_result/show_combination/0/10';
        // $arr[3]['label'] = 'Import Processed Result for 12th.';$arr[3]['link'] = 'import_prcessed_result/show_combination/0/12';

        return view('resultupdate.resultprocess', compact('arr', 'base_url', 'breadcrumbs', 'title'));
    }

    public function revisedresult(Request $request)
    {
        $showStatus = $this->_getCheckAllowToCheckRevisedResult();
        if (!$showStatus) {
            return redirect()->route("landing")->with('error', 'You are not allowed to see the Revised Result. Yet not declared !');
        }
        $custom_component_obj = new CustomComponent;
        $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult();

        if (@$resultCheckStatus) {
        } else {
            return redirect()->route("landing")->with('error', 'Result yet not published!');
        }

        $title = "Download Result";
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
            $inputs = $request->all();
            $request->validate(['enrollment' => 'required|numeric|digits:11', 'dob' => 'required', 'captcha' => 'required|numeric']);

            $captchaStatus = $custom_component_obj->checkCaptcha($inputs);
            if ($captchaStatus == false) {
                return redirect()->back()->with('error', 'Error : Captcha is invalid');
            }

            $combo_name = 'course';
            $course = $this->master_details($combo_name);
            $combo_name = 'result_type';
            $result_type = $this->master_details($combo_name);
            // $combo_name = 'exam_session';$exam_session = $this->master_details($combo_name);

            $combo_name = 'result_session';
            $result_session = $this->master_details($combo_name);
            $current_exam_month_id = Config::get('global.current_exam_month_id');
            $result_session = $result_session[$current_exam_month_id];

            $subject_list = $this->subjectList();

            $students = $custom_component_obj->getresultstudentdata($request->enrollment, $request->dob);

            $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult(@$students->course);
            if (@$resultCheckStatus) {
            } else {
                return redirect()->back()->with('error', $course[$students->course] . ' Result yet not published!');
            }
            if (!empty($students)) {
                $studentexamsubjects = $custom_component_obj->getresultstudentalldata($students->student_id);
                if ($studentexamsubjects == false) {
                    return redirect()->back()->with('error', 'Result Not Declare.');
                }
                return view('resultupdate.results', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type'));
            } else {
                return redirect()->back()->with('error', 'Result not found');
            }
        }
        $captchaImage = $custom_component_obj->generateCaptcha(1, 20, 150, 30);
        return view('resultupdate.revisedresults', compact('captchaImage', 'title', 'breadcrumbs'));
    }

    public function download_duplicate_certificate_pdf_new($type = null, $enrollment = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $title = "Duplicate Certificate";
        $table_id = "Duplicate Certificate";
        $enrollment = $enrollment;
        $type = $type;
        $combination = '';
        $formId = ucfirst(str_replace(" ", "-", $title));
        if (@$type == 1) {
            $marksheet_type = 'Duplicate';
        } else {
            $marksheet_type = 'Revised';
        }
        $documents = '';
        $totalMarks = 0;
        $resultDate = '';
        $grandFinalTotalMarks = 0;
        if (!($enrollment > 0) || $enrollment == null || $enrollment == '') {
            return redirect()->route('finalupdate', $enrollment)->with('message', "Enrollment is not in correct format.");
        }

        $serial_number = $this->marksheet_component_obj->getSerialNumbercertficate($enrollment);
        $findInFlag = 'Pastdata';
        $pastInfo = Pastdata::where('ENROLLNO', '=', $enrollment)->orderBy('id', 'DESC')->first();
        $examresultfields = array('final_result', 'exam_month', 'exam_year', 'total_marks', 'percent_marks');
        $final_result = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($examresultfields);
        if (isset($final_result->exam_month) && isset($final_result->exam_year)) {
            $combination = $final_result->exam_month . ' ' . $final_result->exam_year;

        }

        if (!empty($pastInfo) && !empty($final_result)) {
            $field = ["pastdatas.ENROLLNO  as enrollment", "pastdatas.CLASS  as course", "pastdatas.NAME as name", "pastdatas.FNAME as father_name", "pastdatas.MNAME as mother_name", "pastdatas.DOB as dob"];
            $student = Pastdata::where('ENROLLNO', '=', $enrollment)->orderBy('id', 'DESC')->first($field);
        } else {
            $student = Student::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first();
        }

        $resultsyntax = array('999' => 'AB', '666' => 'SYCP', '777' => 'SYCT', '888' => 'SYC', 'P' => 'P');
        if (!empty($student)) {
            $application = Application::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();
            $student->display_exam_month_year = $this->marksheet_component_obj->getDisplayExamMonthYear($combination);
            $newexamresultfields = array('exam_year', 'exam_month', 'result_date');
            $final_result_data = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($newexamresultfields);
            if (isset($final_result_data->result_date) && $final_result_data->result_date != "") {
                $resultDate = strtotime($final_result_data->result_date);
                $resultDate = date("d-m-Y", $resultDate);
            }
            $courseVal = $student->course;

            $address = Address::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();
            $findInFlag = 'Student';
            @$dtarr = explode('-', $student['dob']);
            $student['dob'] = @$dtarr[2] . "-" . @$dtarr[1] . "-" . @$dtarr[0];
        } else {
            @$dtarr = explode('-', @$pastInfo->ResultDate);
            $resultDate = @$dtarr[2] . "-" . @$dtarr[1] . "-" . @$dtarr[0];
            $final_result['final_result'] = $pastInfo->RESULT;
            $student['ai_code'] = substr($pastInfo->ENROLLNO, 0, 5);
            $ai_code = $student['ai_code'];
            $student['enrollment'] = $pastInfo->ENROLLNO;
            $student['name'] = $pastInfo->NAME;
            $student['father_name'] = $pastInfo->FNAME;
            $student['mother_name'] = $pastInfo->MNAME;
            $courseVal = $pastInfo->CLASS;
            $pastInfo->DOB = $new_date = $pastInfo->DOB;//date('d/m/Y', strtotime($pastInfo['Pastdata']['DOB']));
            $student['dob'] = $pastInfo->DOB;
            $dtarr = explode('-', $student['dob']);
            $student['dob'] = $dtarr[2] . "-" . $dtarr[1] . "-" . $dtarr[0];

        }
        $dobInWords = null;
        if (@($student['dob'])) {
            $dobInWords = $this->marksheet_component_obj->getDObInWords($student['dob']);
        }
        // Get Barcode code
        $imagepath = asset('public/barcode/enrollment/' . $enrollment . '.png');
        $custom_component_obj = new CustomComponent;
        $barcode = $custom_component_obj->barcode($enrollment);
        $barcode_img = '<img src="' . $imagepath . '" alt=barcode-' . $enrollment . ' style="font-size:0;position:relative;width:132px;height:20px;" >';
        //return view('resultupdate.certificate_print_design_new', compact('barcode_img','formId','marksheet_type','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','resultDate','dobInWords','documents','combination'));
        if ($courseVal == 12) {
            $pdf = PDF::loadView('resultupdate.certificate_print_design_new_12', compact('barcode_img', 'formId', 'marksheet_type', 'final_result', 'enrollment', 'serial_number', 'student', 'dobInWords', 'resultsyntax', 'resultDate', 'dobInWords', 'documents', 'combination', 'marksheet_type'));//view file ka code
        } else {
            $pdf = PDF::loadView('resultupdate.certificate_print_design_new', compact('barcode_img', 'formId', 'marksheet_type', 'final_result', 'enrollment', 'serial_number', 'student', 'dobInWords', 'resultsyntax', 'resultDate', 'dobInWords', 'documents', 'combination', 'marksheet_type'));//view file ka code
        }

        $pdf->setTimeout(60000);
        $pdf->setOption('margin-top', 5);
        $pdf->setOption('margin-bottom', 5);
        $pdf->setOption('margin-left', 5);
        $pdf->setOption('margin-right', 5);
        $pdf->setOption('page-size', 'Letter');
        $path = public_path('resultupdate\Certificate_' . $marksheet_type . '_' . $enrollment . '-' . date('d-m-Y-H-i-s') . '.pdf');//jahan pdf save karni hai
        $pdf->save($path, $pdf, true);
        return (Response::download($path));
    }

    public function download_duplicate_marksheet_pdf_new($type = null, $enrollment = null)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $title = "Duplicate Marksheet";
        $table_id = "Duplicate Marksheet";
        $pastdatadocument = config("global.PAST_DATA_DOCUMENT");
        $pastdataurl = config("global.pastdata_document");
        $formId = ucfirst(str_replace(" ", "-", $title));
        $enrollment = $enrollment;
        $type = $type;
        $studentdata = Student::where('enrollment', $enrollment)->first(['id', 'enrollment']);

        if (@$type == 1) {
            $marksheet_type = 'Duplicate';
        } else {
            $marksheet_type = 'Revised';
        }
        $data = ExamResult::where('enrollment', '=', $enrollment)->get();
        $documents = '';
        $totalMarks = 0;
        $grandFinalTotalMarks = 0;
        if (!($enrollment > 0) || $enrollment == null || $enrollment == '') {
            return redirect()->route('finalupdate', $enrollment)->with('message', "Enrollment is not in correct format.");
        }

        $serial_number = $this->marksheet_component_obj->getSerialNumber($enrollment);
        $examresultfields = array('final_result', 'exam_month', 'exam_year', 'total_marks', 'percent_marks', 'additional_subjects');
        $final_result = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($examresultfields);

        $pastInfo = Pastdata::where('ENROLLNO', '=', $enrollment)->orderBy('id', 'DESC')->first();
        if (!empty($pastInfo) && !empty($final_result)) {
            $field = ["pastdatas.ENROLLNO  as enrollment", "pastdatas.CLASS  as course", "pastdatas.NAME as name", "pastdatas.FNAME as father_name", "pastdatas.MNAME as mother_name", "pastdatas.DOB as dob"];
            $student = Pastdata::where('ENROLLNO', '=', $enrollment)->orderBy('id', 'DESC')->first($field);
        } else {
            $student = Student::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first();
        }

        $resultDate = '';
        if (isset($final_result->exam_month) && isset($final_result->exam_year)) {
            $combination = $final_result->exam_month . ' ' . $final_result->exam_year;
        }

        $courseVal = '';
        $resultsyntax = array('999' => 'AB', '666' => 'SYCP', '777' => 'SYCT', '888' => 'SYC', 'P' => 'P', 'PASS' => 'P', 'pass' => 'P');

        if (!empty($student)) { //fetch data from students
            $application = Application::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();
            $student->display_exam_month_year = $this->marksheet_component_obj->getDisplayExamMonthYear($combination);
            $newexamresultfields = array('exam_year', 'exam_month', 'result_date');
            $final_result_data = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($newexamresultfields);
            if (isset($final_result_data->result_date) && $final_result_data->result_date != "") {
                $resultDate = $final_result_data->result_date;
            }
            $documents = Document::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();
            $courseVal = $student->course;
            $address = Address::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();
            $findInFlag = 'Student';
            @$dtarr = explode('-', @$student->dob);
            @$student->dob = @$dtarr[2] . "-" . @$dtarr[1] . "-" . @$dtarr[0];
        } else {
            if (empty($final_result)) {
                $courseVal = $pastInfo->CLASS;
                $subjectCodeIds = $this->marksheet_component_obj->_getSubjectsCodeId($courseVal);
                $final_result['final_result'] = $pastInfo->RESULT;
                $final_result['total_marks'] = $pastInfo->TOTAL_MARK;
                $final_result['percent_marks'] = $pastInfo->Percentage;
                @$dtarr = explode('-', @$pastInfo->ResultDate);
                $resultDate = @$dtarr[2] . "-" . @$dtarr[1] . "-" . @$dtarr[0];
                if (isset($combination) && $combination != '') {
                    $student->display_exam_month_year = $this->marksheet_component_obj->getDisplayExamMonthYear($combination);
                }
            } else {

                $stuexam = ExamSubject::where('student_id', @$studentdata->id)->orderBy('exam_year', 'desc')->orderBy('exam_month', 'asc')->first(['exam_year', 'exam_month']);
                $examSubjectsMarksData = $this->marksheet_component_obj->getallexamsubjectsdata($enrollment, $stuexam->exam_year, $stuexam->exam_month);
                $newexamresultfields = array('exam_year', 'exam_month', 'result_date');
                $final_result_data = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($examresultfields);
                if (isset($final_result_data->result_date) && $final_result_data->result_date != "") {
                    $resultDate = $final_result_data->result_date;
                }
                @$student->display_exam_month_year = $this->marksheet_component_obj->getDisplayExamMonthYear($combination);
            }
            @$student['ai_code'] = substr($pastInfo->ENROLLNO, 0, 5);
            @$ai_code = $student['ai_code'];
            @$student['enrollment'] = $pastInfo->ENROLLNO;
            @$student['name'] = $pastInfo->NAME;
            @$student['father_name'] = $pastInfo->FNAME;
            @$student['mother_name'] = $pastInfo->MNAME;
            @$pastInfo->DOB = $new_date = $pastInfo->DOB;//date('d/m/Y', strtotime($pastInfo['Pastdata']['DOB']));
            @$student['dob'] = @$pastInfo->DOB;
            @$dtarr = explode('-', $student['dob']);
            @$student['dob'] = @$dtarr[2] . "-" . @$dtarr[1] . "-" . @$dtarr[0];
            @$student['course'] = $pastInfo->CLASS;
            @$student['yy'] = $yy = substr($pastInfo->ENROLLNO, 5, 2);
            @$student['student_code'] = $st_code = substr($pastInfo->ENROLLNO, 7);
            @$courseVal = $pastInfo->CLASS;
            @$student['display_exam_month_year'] = $pastInfo->EX_YR;
            @$addressTemp = $pastInfo->ADDRESS;
            if (isset($pastInfo->DISTRICT) && !empty($pastInfo->DISTRICT)) {
                @$addressTemp .= ',' . $pastInfo->DISTRICT;
            }
            if (isset($pastInfo->STATE) && !empty($pastInfo->STATE)) {
                @$addressTemp .= ',' . $pastInfo->STATE;
            }
            if (isset($pastInfo->PIN) && !empty($pastInfo->PIN)) {
                @$addressTemp .= '-' . $pastInfo->PIN;
            }
            @$address['address1'] = $addressTemp;
            @$address['address2'] = '';
            @$address['address3'] = '';
            @$address['city_name'] = '';
            @$address['pincode'] = '';
            if (isset($pastInfo->MOBILE) && !empty($pastInfo->MOBILE)) {
                @$student['mobile'] = $pastInfo->MOBILE;
            }
            if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'GENERAL_ADM' || $pastInfo->ERTYPE == 'STREAM2') {

                $student['adm_type'] = 1;
            } else if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'READMISSION') {

                $student['adm_type'] = 2;
            } else if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'PARTADMISSION') {

                $student['adm_type'] = 3;
            } else if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'IMPROVEMENT') {

                $student['adm_type'] = 4;
            } else if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'SUPPLEMENTARY') {

                $student['adm_type'] = 1;    //11 for supplementary ertype and admission type
            } else {//if ertype in pastdata table is balnk or null then use gen_adm adm_type

                $student['adm_type'] = 1;
            }
            // $subjectCodes = $this->marksheet_component_obj->_getSubjectCodes($courseVal);

        }

        $sub_enrollment = substr($enrollment, -6, 2);
        if ($sub_enrollment >= '17') {
            $stuexam1 = ExamSubject::where('student_id', @$studentdata->id)->orderBy('exam_year', 'desc')->orderBy('exam_month', 'asc')->first(['exam_year', 'exam_month']);
            $examSubjectsMarksData = $this->marksheet_component_obj->getallexamsubjectsdata($enrollment, $stuexam1->exam_year, $stuexam1->exam_month);
        } else {
            $examsubjectfields = array('id', 'subject_id', 'final_theory_marks', 'final_practical_marks', 'sessional_marks_reil_result', 'total_marks', 'final_result');
            $examSubjectsMarksData = ExamSubject::where('enrollment', '=', $enrollment)->orderBy('exam_year', 'desc')->orderBy('exam_month', 'asc')->orderBy('subject_id', 'desc')->groupBy('subject_id')->get($examsubjectfields);
        }
        $subjectCodeIds = $this->marksheet_component_obj->_getSubjectsCodeId($courseVal);
        $examsubjectcount = count($examSubjectsMarksData);
        if (($examsubjectcount == 0)) {
            $examSubjectsMarksData = array();
            if (isset($pastInfo->FRES1) && $pastInfo->FRES1 != '') {
                $examSubjectsMarksData[0]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB1];
                $examSubjectsMarksData[0]['final_theory_marks'] = $pastInfo->FTM1;
                $examSubjectsMarksData[0]['final_practical_marks'] = $pastInfo->FPM1;
                $examSubjectsMarksData[0]['sessional_marks_reil_result'] = $pastInfo->fst1;
                $examSubjectsMarksData[0]['total_marks'] = $pastInfo->FTOT1;
                $examSubjectsMarksData[0]['final_result'] = $pastInfo->FRES1;
                $examSubjectsMarksData[0]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB1]);
                $examSubjectsMarksData[0]['num_words'] = $this->marksheet_component_obj->numberInWord($examSubjectsMarksData[0]['total_marks']);
                $examSubjectsMarksData[0]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[0]['total_marks']);
            }

            if (isset($pastInfo->FRES2) && $pastInfo->FRES2 != '') {
                $examSubjectsMarksData[1]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB2];
                $examSubjectsMarksData[1]['final_theory_marks'] = $pastInfo->FTM2;
                $examSubjectsMarksData[1]['final_practical_marks'] = $pastInfo->FPM2;
                $examSubjectsMarksData[1]['sessional_marks_reil_result'] = $pastInfo->fst2;
                $examSubjectsMarksData[1]['total_marks'] = $pastInfo->FTOT2;
                $examSubjectsMarksData[1]['final_result'] = $pastInfo->FRES2;
                $examSubjectsMarksData[1]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB2]);
                $examSubjectsMarksData[1]['num_words'] = $this->marksheet_component_obj->numberInWord($examSubjectsMarksData[1]['total_marks']);
                $examSubjectsMarksData[1]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[1]['total_marks']);

            }

            if (isset($pastInfo->FRES3) && $pastInfo->FRES3 != '') {
                $examSubjectsMarksData[2]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB3];
                $examSubjectsMarksData[2]['final_theory_marks'] = $pastInfo->FTM3;
                $examSubjectsMarksData[2]['final_practical_marks'] = $pastInfo->FPM3;
                $examSubjectsMarksData[2]['sessional_marks_reil_result'] = $pastInfo->fst3;
                $examSubjectsMarksData[2]['total_marks'] = $pastInfo->FTOT3;
                $examSubjectsMarksData[2]['final_result'] = $pastInfo->FRES3;
                $examSubjectsMarksData[2]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB3]);
                $examSubjectsMarksData[2]['num_words'] = $this->marksheet_component_obj->numberInWord($examSubjectsMarksData[2]['total_marks']);
                $examSubjectsMarksData[2]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[2]['total_marks']);

            }

            if (isset($pastInfo->FRES4) && $pastInfo->FRES4 != '') {
                $examSubjectsMarksData[3]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB4];
                $examSubjectsMarksData[3]['final_theory_marks'] = $pastInfo->FTM4;
                $examSubjectsMarksData[3]['final_practical_marks'] = $pastInfo->FPM4;
                $examSubjectsMarksData[3]['sessional_marks_reil_result'] = $pastInfo->fst4;
                $examSubjectsMarksData[3]['total_marks'] = $pastInfo->FTOT4;
                $examSubjectsMarksData[3]['final_result'] = $pastInfo->FRES4;
                $examSubjectsMarksData[3]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB4]);
                $examSubjectsMarksData[3]['num_words'] = $this->marksheet_component_obj->numberInWord($examSubjectsMarksData[3]['total_marks']);
                $examSubjectsMarksData[3]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[3]['total_marks']);

            }


            if (isset($pastInfo->FRES5) && $pastInfo->FRES5 != '') {
                $examSubjectsMarksData[4]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB5];
                $examSubjectsMarksData[4]['final_theory_marks'] = $pastInfo->FTM5;
                $examSubjectsMarksData[4]['final_practical_marks'] = $pastInfo->FPM5;
                $examSubjectsMarksData[4]['sessional_marks_reil_result'] = $pastInfo->fst5;
                $examSubjectsMarksData[4]['total_marks'] = $pastInfo->FTOT5;
                $examSubjectsMarksData[4]['final_result'] = $pastInfo->FRES5;
                $examSubjectsMarksData[4]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB5]);
                $examSubjectsMarksData[4]['num_words'] = $this->marksheet_component_obj->numberInWord($examSubjectsMarksData[4]['total_marks']);
                $examSubjectsMarksData[4]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[4]['total_marks']);
            }

            if (isset($pastInfo->FRES6) && $pastInfo->FRES6 != '') {

                $examSubjectsMarksData[5]['subject_id'] = @$subjectCodeIds[@$pastInfo->EX_SUB9];
                $examSubjectsMarksData[5]['final_theory_marks'] = $pastInfo->FTM6;
                $examSubjectsMarksData[5]['final_practical_marks'] = $pastInfo->FPM6;
                $examSubjectsMarksData[5]['sessional_marks_reil_result'] = $pastInfo->fst6;
                $examSubjectsMarksData[5]['total_marks'] = $pastInfo->FTOT6;
                $examSubjectsMarksData[5]['final_result'] = $pastInfo->FRES6;
                $examSubjectsMarksData[5]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB9]);
                $examSubjectsMarksData[5]['num_words'] = $this->marksheet_component_obj->numberInWord($examSubjectsMarksData[5]['total_marks']);
                $examSubjectsMarksData[5]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[5]['total_marks']);

            }

            if (isset($pastInfo->FRES7) && $pastInfo->FRES7 != '') {
                $examSubjectsMarksData[6]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB7];
                $examSubjectsMarksData[6]['final_theory_marks'] = $pastInfo->FTM7;
                $examSubjectsMarksData[6]['final_practical_marks'] = $pastInfo->FPM7;
                $examSubjectsMarksData[6]['sessional_marks_reil_result'] = $pastInfo->fst7;
                $examSubjectsMarksData[6]['total_marks'] = $pastInfo->FTOT7;
                $examSubjectsMarksData[6]['final_result'] = $pastInfo->FRES7;
                $examSubjectsMarksData[6]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB7]);
                $examSubjectsMarksData[6]['num_words'] = $this->marksheet_component_obj->numberInWord($examSubjectsMarksData[6]['total_marks']);
                $examSubjectsMarksData[6]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[6]['total_marks']);
            }

        } else {
            if (!empty($examSubjectsMarksData)) {
                $k = 0;
                foreach ($examSubjectsMarksData as $v) {
                    $examSubjectsMarksData[$k]['max_marks'] = $this->marksheet_component_obj->getSubjectMaxMarks($v->subject_id);
                    $examSubjectsMarksData[$k]['num_words'] = $this->marksheet_component_obj->numberInWord($v->total_marks);
                    $examSubjectsMarksData[$k]['grade_marks'] = $this->marksheet_component_obj->getGradeOfMarks($v->total_marks);
                    $totalMarks = $totalMarks + $examSubjectsMarksData[$k]['max_marks'];
                    $grandFinalTotalMarks = $grandFinalTotalMarks + $v->total_marks;
                    $k++;
                }
            }
        }
        $dobInWords = null;
        if (@($student['dob'])) {
            $dobInWords = $this->marksheet_component_obj->getDObInWords($student['dob']);
        }

        $subjects = $this->marksheet_component_obj->_getSubjectsForMarksheet($courseVal);
        if (!empty($pastInfo)) {

        }

        $imagepath = asset('public/barcode/enrollment/' . $enrollment . '.png');
        $custom_component_obj = new CustomComponent;
        $barcode = $custom_component_obj->barcode($enrollment);
        $barcode_img = '<img src="' . $imagepath . '" alt=barcode-' . $enrollment . ' style="font-size:0;position:relative;width:132px;height:20px;" >';
        /* if($courseVal == 10){
				// 	return view('resultupdate.marksheet_print_design', compact('pastdataurl','pastdatadocument','barcode_img','pastInfo','formId','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','examSubjectsMarksData','subjects','subjectCodeIds','resultDate','dobInWords','documents','marksheet_type'));
				$pdf =  PDF::loadView('resultupdate.10_marksheet_print_design', compact('pastdataurl','pastdatadocument','barcode_img','pastInfo','formId','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','examSubjectsMarksData','subjectCodeIds','subjects','resultDate','dobInWords','documents','marksheet_type'));//view file ka code
				$pdf->setTimeout(60000);
				$path = public_path('resultupdate\marksheet_'.$marksheet_type.'_'.$enrollment.'-'.date('d-m-Y-H-i-s'). '.pdf');//jahan pdf save karni hai
				$pdf->save($path,$pdf,true);
				return( Response::download($path));
			}else{
				// 	return view('resultupdate.marksheet_print_design', compact('pastdataurl','pastdatadocument','barcode_img','pastInfo','formId','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','examSubjectsMarksData','subjects','subjectCodeIds','resultDate','dobInWords','documents','marksheet_type'));
				$pdf =  PDF::loadView('resultupdate.12_marksheet_print_design', compact('pastdataurl','pastdatadocument','barcode_img','pastInfo','formId','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','examSubjectsMarksData','subjectCodeIds','subjects','resultDate','dobInWords','documents','marksheet_type'));//view file ka code
				$pdf->setTimeout(60000);
				$path = public_path('resultupdate\marksheet_'.$marksheet_type.'_'.$enrollment.'-'.date('d-m-Y-H-i-s'). '.pdf');//jahan pdf save karni hai
				$pdf->save($path,$pdf,true);
				return( Response::download($path));
			}*/


        return view('resultupdate.marksheet_print_design_new', compact('pastdataurl', 'pastdatadocument', 'barcode_img', 'pastInfo', 'formId', 'final_result', 'enrollment', 'serial_number', 'student', 'dobInWords', 'resultsyntax', 'examSubjectsMarksData', 'subjectCodeIds', 'subjects', 'resultDate', 'dobInWords', 'documents', 'marksheet_type'));
        $pdf = PDF::loadView('resultupdate.marksheet_print_design_new', compact('pastdataurl', 'pastdatadocument', 'barcode_img', 'pastInfo', 'formId', 'final_result', 'enrollment', 'serial_number', 'student', 'dobInWords', 'resultsyntax', 'examSubjectsMarksData', 'subjectCodeIds', 'subjects', 'resultDate', 'dobInWords', 'documents', 'marksheet_type'));//view file ka code
        $pdf->setTimeout(60000);

        $pdf->setOption('margin-top', 5);
        $pdf->setOption('margin-bottom', 5);
        $pdf->setOption('margin-left', 5);
        $pdf->setOption('margin-right', 5);
        $pdf->setOption('page-size', 'Letter');

        $path = public_path('resultupdate\marksheet_' . $marksheet_type . '_' . $enrollment . '-' . date('d-m-Y-H-i-s') . '.pdf');//jahan pdf save karni hai
        $pdf->save($path, $pdf, true);
        return (Response::download($path));

    }

    public function getresultmarksheet(Request $request)
    {
        //return redirect()->route("landing")->with('error', 'Not allow to see the previous years result!');
        $custom_component_obj = new CustomComponent;
        $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult();

        if (@$resultCheckStatus) {
        } else {
            return redirect()->route("landing")->with('error', 'Result yet not published!');
        }


        $title = "Download Result";
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
            $inputs = $request->all();
            if (@$inputs['student_login']) {

            } else if (@$inputs['qr_marksheets']) {

                $student_data = student::where('enrollment', crypt::decrypt($request->enrollment))->first(['enrollment', 'dob']);
                $final_result = ExamResult::where('enrollment', '=', crypt::decrypt($request->enrollment))->orderBy('id', 'DESC')->first(['enrollment', 'exam_year', 'exam_month']);
                $request->enrollment = @$student_data->enrollment;
                $request->dob = @$student_data->dob;
                $request->exam_year = @$final_result->exam_year;
                $request->exam_month = @$final_result->exam_month;
            } else {
                $request->validate(['enrollment' => 'required|numeric|digits:11', 'dob' => 'required', 'captcha' => 'required|numeric']);

                $captchaStatus = $custom_component_obj->checkCaptcha($inputs);
                if ($captchaStatus == false) {
                    return redirect()->back()->with('error', 'Error : Captcha is invalid');
                }
            }

            $combo_name = 'course';
            $course = $this->master_details($combo_name);
            $combo_name = 'result_type';
            $result_type = $this->master_details($combo_name);
            $combo_name = 'exam_month';
            $exam_month = $this->master_details($combo_name);
            $combo_name = 'examresult_year_monthcombo';
            $examresult_year_monthcombo = $this->master_details($combo_name);
            // $combo_name = 'exam_session';$exam_session = $this->master_details($combo_name);
            $combo_name = 'result_session';
            $result_session = $this->master_details($combo_name);
            $current_exam_month_id = Config::get('global.current_result_session_month_id');
            $result_session = $result_session[$current_exam_month_id];
            $subject_list = $this->subjectList();
            if (@$inputs['student_login'] || @$inputs['qr_marksheets']) {
                $students = $custom_component_obj->examYearExamMonthGetResultStudentDataMarksheet(@$request->enrollment, @$request->dob, @$request->exam_year, @$request->exam_month);
            } else {
                $students = $custom_component_obj->getresultstudentdatamarksheet($request->enrollment, $request->dob);
            }
            if (@$students->adm_type == 4 && !@$inputs['qr_marksheets']) {
                return redirect()->back()->with('error', 'Your admission type is "Improvement" so you are not allow to see the previous years result.');
            }

            $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult(@$students->course);
            if (@$resultCheckStatus) {
            } else {
                return redirect()->back()->with('error', $course[$students->course] . ' Result yet not published!');
            }

            if (!empty($students)) {
                $combination = $students->exam_year . '_' . $students->result_sexam_month;
                if (!@$inputs['qr_marksheets']) {
                    if (!in_array($combination, $examresult_year_monthcombo->toArray())) {
                        return redirect()->back()->with('error', 'Result not found');
                    }
                }
                $getresultrsosyesrs = $custom_component_obj->getresultrsosyearsList();
                $studentexamsubjects = $custom_component_obj->getresultstudentalldatamarksheet($students->student_id, $students->exam_year, $students->result_sexam_month);
                if ($studentexamsubjects == false) {
                    return redirect()->back()->with('error', 'Result Not Declare.');
                }
                return view('resultupdate.oldresults', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type', 'exam_month', 'getresultrsosyesrs'));
            } else {
                return redirect()->back()->with('error', 'Result not found');
            }
        }
        $captchaImage = $custom_component_obj->generateCaptcha(1, 20, 150, 30);
        $allowYearCombo = $custom_component_obj->_getAllowYearCombo();
        return view('resultupdate.oldresult', compact('captchaImage', 'title', 'breadcrumbs', 'allowYearCombo'));
    }

    public function oldresultdownloadpdf(Request $request, $enrollment = null, $dob = null)
    {
        $custom_component_obj = new CustomComponent;
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'result_session';
        $result_session = $this->master_details($combo_name);
        $combo_name = 'result_type';
        $result_type = $this->master_details($combo_name);
        $current_exam_month_id = Config::get('global.current_result_session_month_id');
        $result_session = $result_session[$current_exam_month_id];

        $subject_list = $this->subjectList();
        $students = null;
        if (count($request->all()) > 0) {
            $inputs = $request->all();
            if (@$inputs['student_login'] && $inputs['student_login'] == true) {
                $enrollment = decrypt($request->enrollment);
                $dob = decrypt($request->dob);
                $exam_year = decrypt($request->exam_year);
                $exam_month = decrypt($request->exam_month);
                $students = $custom_component_obj->examYearExamMonthGetResultStudentDataMarksheet(@$enrollment, @$dob, @$exam_year, @$exam_month);

            }
        }
        if (@$students) {
        } else {

            $students = $custom_component_obj->getresultstudentdatamarksheet($enrollment, $dob);
        }
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        if (!empty($students)) {
            $getresultrsosyesrs = $custom_component_obj->getresultrsosyearsList();
            $studentexamsubjects = $custom_component_obj->getresultstudentalldatamarksheet($students->student_id, $students->exam_year, $students->result_sexam_month);
            $pdf = PDF::loadView('resultupdate.oldprovisionalmarksheet', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type', 'getresultrsosyesrs', 'exam_month'));
            return $pdf->download('provisional_marksheet.pdf');
        }
    }

    //provisional_result

    public function direct_provisioanl_marksheet(Request $request)
    {
        $enrollment = $request->enrollment;
        $enrollment = Crypt::decrypt($enrollment);
        $tempS = Student::where('enrollment', @$enrollment)->first();
        $enrollment = @$tempS->enrollment;
        $course = @$tempS->course;
        if (@$enrollment) {
            $dob = @$tempS->dob;
            $custom_component_obj = new CustomComponent;
            $combo_name = 'course';
            $course = $this->master_details($combo_name);
            $combo_name = 'result_session';
            $result_session = $this->master_details($combo_name);
            $subject_list = $this->subjectList();
            $students = $custom_component_obj->getresultstudentdata($enrollment, $dob);
            $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult(@$students->course);
            $current_exam_month_id = Config::get('global.current_result_session_month_id');
            $result_session = $result_session[$current_exam_month_id];
            $combo_name = 'result_type';
            $result_type = $this->master_details($combo_name);
            $combo_name = 'exam_month';
            $exam_month = $this->master_details($combo_name);
            if (@$resultCheckStatus) {
            } else {
                return redirect()->back()->with('error', $course[$students->course] . ' Result yet not published!');
            }
            if (!empty($students)) {
                $studentexamsubjects = $custom_component_obj->getresultstudentalldata($students->student_id);

                if ($studentexamsubjects == false) {
                    return redirect()->back()->with('error', 'Result Not Declare.');
                }
                return view('resultupdate.results', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type', 'exam_month'));
            } else {
                return redirect()->back()->with('error', 'Result not found');
            }
        } else {
            return redirect()->back()->with('error', 'Result not found');
        }
    }

    public function temprptest($id = null)
    {
        $tempS = Student::where('id', $id)->first();
        $enrollment = @$tempS->enrollment;
        $course = @$tempS->course;

        if (@$enrollment) {
            $dob = @$tempS->dob;
            $custom_component_obj = new CustomComponent;
            $combo_name = 'course';
            $course = $this->master_details($combo_name);
            $combo_name = 'result_session';
            $result_session = $this->master_details($combo_name);
            $subject_list = $this->subjectList();
            $students = $custom_component_obj->getresultstudentdata($enrollment, $dob);
            $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult(@$students->course);
            $current_exam_month_id = Config::get('global.current_result_session_month_id');
            $result_session = $result_session[$current_exam_month_id];
            $combo_name = 'result_type';
            $result_type = $this->master_details($combo_name);
            $combo_name = 'exam_month';
            $exam_month = $this->master_details($combo_name);

            if (@$resultCheckStatus) {
            } else {
                return redirect()->back()->with('error', $course[$students->course] . ' Result yet not published!');
            }

            if (!empty($students)) {
                $studentexamsubjects = $custom_component_obj->getresultstudentalldata($students->student_id);

                if ($studentexamsubjects == false) {
                    return redirect()->back()->with('error', 'Result Not Declare.');
                }
                return view('resultupdate.results', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type', 'exam_month'));
            } else {
                return redirect()->back()->with('error', 'Result not found');
            }
        } else {
            return redirect()->back()->with('error', 'Result not found');
        }
    }

    public function provisional_result(Request $request)
    {
        /*
		$inputTemp = ($request->all());
		if($inputTemp['RJDOITC'] != 'RJDOITC'){
			return redirect()->route("landing")->with('error', 'You are not allowed to see the Result. Yet not declared !');
		}
		*/
        // return redirect('http://103.122.38.42/result');
        //return redirect()->route('view_result');
		$showStatus = $this->_getCheckAllowToCheckResult();
		
		
        if (!$showStatus) {
            return redirect()->route("landing")->with('error', 'You are not allowed to see the Result. Yet not declared !');
        }
        $custom_component_obj = new CustomComponent;
        $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult();
		
        if (@$resultCheckStatus) {
        } else {
            return redirect()->route("landing")->with('error', 'Result yet not published!');
        }

        $title = "Download Result";
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
            $inputs = $request->all();
            $request->validate(['enrollment' => 'required|numeric|digits:11', 'dob' => 'required', 'captcha' => 'required|numeric']);

            $captchaStatus = $custom_component_obj->checkCaptcha($inputs);
            if ($captchaStatus == false) {
                return redirect()->back()->with('error', 'Error : Captcha is invalid');
            }

            $combo_name = 'course';
            $course = $this->master_details($combo_name);
            $combo_name = 'result_type';
            $result_type = $this->master_details($combo_name);
            // $combo_name = 'exam_session';$exam_session = $this->master_details($combo_name);

            $combo_name = 'result_session';
            $result_session = $this->master_details($combo_name);
            $current_exam_month_id = Config::get('global.current_result_session_month_id');
            $result_session = $result_session[$current_exam_month_id];
            $subject_list = $this->subjectList();

            $students = $custom_component_obj->getStudentProvisionalResult($request->enrollment, $request->dob);


            $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult(@$students->course);
            if (@$resultCheckStatus) {
            } else {
                return redirect()->back()->with('error', $course[$students->course] . ' Result yet not published!');
            }


            if (!empty($students)) {
                if ($students->is_temp_examresult == "111") {
                    return redirect()->back()->with('error', 'The Examination has been Cancelled.');
                }


                $studentexamsubjects = $custom_component_obj->getStudentProvisionalResultSubject($students->student_id);

                if ($studentexamsubjects == false) {
                    return redirect()->back()->with('error', 'Result Not Declare.');
                }
                return view('resultupdate.results', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type'));
            } else {
                return redirect()->back()->with('error', 'Result not found');
            }
        }
        $captchaImage = $custom_component_obj->generateCaptcha(1, 20, 150, 30);
        return view('resultupdate.result', compact('captchaImage', 'title', 'breadcrumbs'));
    }

    public function provisional_result_pdf(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $showStatus = $this->_getCheckAllowToCheckResult();
        if (!$showStatus) {
            return redirect()->route("landing")->with('error', 'You are not allowed to see the Result. Yet not declared !');
        }
        $custom_component_obj = new CustomComponent;
        $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult();
        if (@$resultCheckStatus) {
        } else {
            return redirect()->route("landing")->with('error', 'Result yet not published!');
        }

        $title = "Download Result";
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
            $inputs = $request->all();
            $request->validate(['enrollment' => 'required|numeric|digits:11', 'dob' => 'required', 'captcha' => 'required|numeric']);

            $captchaStatus = $custom_component_obj->checkCaptcha($inputs);
            if ($captchaStatus == false) {
                return redirect()->back()->with('error', 'Error : Captcha is invalid');
            }
            $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult(@$students->course);
            if (@$resultCheckStatus) {
            } else {
                return redirect()->back()->with('error', $course[$students->course] . ' Result yet not published!');
            }
            $enrollment = $request->enrollment;

            //just get the pdf path and download the pdf of the enrollment
            //Path : public\files\provisional_marksheet\124\1\

            $filename = $enrollment . '.pdf';
            $path = public_path("files/provisional_marksheet/124/1/" . $filename);
            if (file_exists($path)) {
                // return Response::make(file_get_contents($path), 200, [
                // 	'Content-Type' => 'application/pdf',
                // 	'Content-Disposition' => 'inline; filename="'.$filename.'"'
                // ]);
                // return Response::download($path);
                return Response::download($path, $filename, [], 'inline');

            } else {
                return redirect()->back()->with('error', 'Result not Found!');
            }


        }
        $captchaImage = $custom_component_obj->generateCaptcha(1, 20, 150, 30);
        return view('resultupdate.result', compact('captchaImage', 'title', 'breadcrumbs'));
    }

    public function resultProvisionaldownloadpdf($enrollment = null, $dob = null)
    {
        $custom_component_obj = new CustomComponent;
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'result_session';
        $result_session = $this->master_details($combo_name);
        $combo_name = 'result_type';
        $result_type = $this->master_details($combo_name);
        $current_exam_month_id = Config::get('global.current_result_session_month_id');
        $result_session = $result_session[$current_exam_month_id];

        $subject_list = $this->subjectList();
        $students = $custom_component_obj->getStudentProvisionalResult($enrollment, $dob);
        if (!empty($students)) {
            $studentexamsubjects = $custom_component_obj->getStudentProvisionalResultSubject($students->student_id);
            $pdf = PDF::loadView('resultupdate.provisionalmarksheet', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type'));
            return $pdf->download('provisional_marksheet.pdf');
        }
    }

    public function direct_provisional_results(Request $request)
    {
        $page_title = "Search Enrollment for Provisional Marksheet";
        if ($request->isMethod('PUT')) {
            $student = new Student;
            $validator = Validator::make($request->all(), $student->rulessessionalmarks);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $enrollment = $request->enrollment;
            $examresult = DB::table('exam_results')->where('enrollment', '=', $enrollment)->orderBy('id', 'desc')->whereNull('deleted_at')->first(['enrollment', 'is_temp_examresult']);
            if ($examresult->is_temp_examresult == "111") {
                return redirect()->back()->with('error', 'The Examination has been Cancelled.');
            }
            $pastdata = Pastdata::where('ENROLLNO', '=', $enrollment)->orderBy('id', 'desc')->whereNull('deleted_at')->first('ENROLLNO');

            if (empty($examresult) && empty($pastdata)) {
                return back()->with('error', 'Enrollment not found');
            } else {

                $tempS = Student::where('enrollment', @$enrollment)->first();
                $enrollment = @$tempS->enrollment;
                $course = @$tempS->course;

                if (@$enrollment) {

                    $dob = @$tempS->dob;
                    $custom_component_obj = new CustomComponent;
                    $combo_name = 'course';
                    $course = $this->master_details($combo_name);
                    $combo_name = 'result_session';
                    $result_session = $this->master_details($combo_name);
                    $subject_list = $this->subjectList();
                    $students = $custom_component_obj->getresultstudentdatamarksheet($enrollment, $dob);
                    $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult(@$students->course);

                    $current_exam_month_id = Config::get('global.current_result_session_month_id');
                    $combo_name = 'result_type';
                    $result_type = $this->master_details($combo_name);
                    $combo_name = 'exam_month';
                    $exam_month = $this->master_details($combo_name);
                    $combo_name = 'admission_sessions';
                    $admission_sessions = $this->master_details($combo_name);
                    $result_session = null;
                    if (@$resultCheckStatus) {
                    } else {
                        return redirect()->back()->with('error', $course[$students->course] . ' Result yet not published!');
                    }
                    if (!empty($students)) {
                        $result_session = @$exam_month[@$students->result_sexam_month] . ' ' . $admission_sessions[@$students->exam_year];
                        $studentexamsubjects = $custom_component_obj->getresultstudentalldatamarksheet($students->student_id, $students->exam_year, $students->result_sexam_month);
                        if ($studentexamsubjects == false) {
                            return redirect()->back()->with('error', 'date not found.');
                        }
                        return view('resultupdate.results', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type', 'exam_month'));
                    } else {
                        return redirect()->back()->with('error', 'Result not found');
                    }
                } else {
                    @dd('noo');
                    return redirect()->back()->with('error', 'Result not found');
                }

                //return redirect()->route('direct_provisioanl_marksheet',Crypt::encrypt($enrollment))->with('message','Enrollment Found');
            }

        }
        return view('resultupdate.serachenrollment', compact('page_title'));
    }


    public function marksheetCorreaction($student_id = null, Request $request)
    {
        $breadcrumbs = array();
        $pagetitle = "marksheet & Migration";
        $student_id = Crypt::decrypt($student_id);
        $combo_name = 'marksheet_print_option';
        $marksheet_print_option = $this->master_details($combo_name);
        $combo_name = 'marksheet_migartion_fees';
        $marksheet_migartion_fees = $this->master_details($combo_name);
        $final_results = ExamResult::where('student_id', $student_id)->orderBy('id', 'desc')->first();
		
		$correctionAllowOrNot = $this->custom_component_obj->_checkRevisedAllowOrNotAllow();
		if(@$correctionAllowOrNot == false){
			return redirect()->route('studentsdashboards')->with('error','Correction Date has been closed.');
		}
		
		
        $studentDocumentPath = "marksheets/$student_id/";;
        $student_data = Student::where('id', $student_id)->first();
        $student_data2 = Student::where('id', $student_id)->first();
        $alreadyData = MarksheetMigrationRequest::where('student_id', $student_id)->where('marksheet_migration_status', '0')->orderBy('id', 'desc')->first();
        $mmr_id = @$alreadyData->id;
        if (!empty(@$alreadyData)) {
            $revised_data = RevisedCorrection::where('marksheet_migration_request_id', @$alreadyData->id)->pluck('correct_value', 'correction_field');
            if (@$revised_data && count($revised_data) != 0) {
                foreach ($revised_data as $key => $data) {
                    $student_data2->$key = $data;
                }
            }
        }
        if (@$alreadyData && @$alreadyData->locksumbitted == 1) {
            return redirect()->route('corr_marksheet_previews', Crypt::encrypt($alreadyData->id))->with('message', 'Form Already Lock and Submitted');
        }
        $finalresultstatus = false;
        if (@$final_results->final_result == 'xxxx' || @$final_results->final_result == 'XXXX') {
            $finalresultstatus = true;
            unset($marksheet_print_option['2'], $marksheet_print_option['3']);
        }
        if (count($request->all()) > 0) {

            $svarray = array();
			$correctionAllowOrNot = $this->custom_component_obj->_checkRevisedAllowOrNotAllow();
			if(@$correctionAllowOrNot == false){
				return redirect()->route('studentsdashboards')->with('error','Correction Date has been closed.');
			}
            $marksheetmigrationrequest = new MarksheetMigrationRequest;
            $validator = Validator::make($request->all(), [
                //'support_document2' => "mimes:jpg,png,jpeg,gif,svg,pdf|between:50,100",
            ],
                [

                ]);
            //@dd($validator);
            if ($validator->fails()) {

                return back()->withErrors($validator)->withInput();
            }
            $input = $request->all();
            if (@$input['support_document2']) {
                $file = $request->file('support_document2');
                $filename = $imageName = "support_document" . '.' . @$request->support_document2->extension();
                $uploadFilePath = "marksheets/$student_id/";
                File::makeDirectory(public_path($uploadFilePath), $mode = 0777, true, true);
                $request->support_document2->move(public_path($uploadFilePath), $filename);
            } else {
                $filename = @$alreadyData->support_document;
            }

            $marksSvData = ['student_id' => @$student_id, 'enrollment' => $student_data->enrollment, 'marksheet_type' => $request->marksheet_type, 'document_type' => $request->document_type, 'support_document' => $filename, 'total_fees' => @$marksheet_migartion_fees[@$request->document_type]
            ];

            $svarray['incorrect_value'] = [$student_data->name, date("d-m-Y", strtotime(@$student_data->dob)), $student_data->father_name, $student_data->mother_name];
            $dobArr = explode("/", $request->dob);
            if (!empty($dobArr) && isset($dobArr['1'])) {
                if (isset($dobArr[0]) && isset($dobArr[1]) && isset($dobArr[2])) {
                    $dob = $dobArr[2] . "-" . $dobArr[1] . "-" . $dobArr[0];
                }
            } else {
                $dob = date("Y-m-d", strtotime($request->dob));
            }
            $svarray['correct_value'] = [strtoupper(@$request->name), @$dob, strtoupper(@$request->father_name), strtoupper(@$request->mother_name)];
            $condition = ['id' => @$alreadyData->id];

            $markdata = MarksheetMigrationRequest::updateOrCreate($condition, $marksSvData);
            $condition = ['marksheet_migration_request_id' => $markdata->id];
            if ($request->marksheet_type == 1) {
                $loopcount = count($input['field']);
                RevisedCorrection::where($condition)->forceDelete();
                for ($i = 0; $i < $loopcount; $i++) {
                    $svData['marksheet_migration_request_id'] = $markdata->id;
                    $svData['student_id'] = $student_id;
                    $svData['correction_field'] = $input['field'][$i];
                    $svData['correct_value'] = $svarray['correct_value'][$i];
                    $svData['incorrect_value'] = $svarray['incorrect_value'][$i];
                    RevisedCorrection::Create($svData);
                }
            } else {
                RevisedCorrection::where($condition)->forceDelete();
            }
            return redirect()->route('corr_marksheet_previews', Crypt::encrypt($markdata->id))->with('message', "data Save Succefully");


        }
        $combo_name = 'marsheet_type';
        $marsheet_type = $this->master_details($combo_name);
        $permissions = CustomHelper::roleandpermission();

        return view('resultupdate.correctionlist', compact('final_results', 'breadcrumbs', 'marsheet_type', 'permissions', 'student_data', 'marksheet_print_option', 'pagetitle', 'student_id', 'alreadyData', 'mmr_id', 'student_data2', 'studentDocumentPath', 'marksheet_migartion_fees', 'final_results', 'finalresultstatus'));
    }


    public function corr_marksheet_previews(Request $request, $mmr_id = null)
    {
        $mmr_id = Crypt::decrypt($mmr_id);
        $page_title = "Marksheet";
        $mmr_data = MarksheetMigrationRequest::where('marksheet_migration_status', '0')->find($mmr_id);
        $student_id = @$mmr_data->student_id;
		$correctionAllowOrNot = $this->custom_component_obj->_checkRevisedAllowOrNotAllow();
		if(@$correctionAllowOrNot == false){
			return redirect()->route('studentsdashboards')->with('error','Correction Date has been closed.');
		}
       /* $lockandsubmittedallowornot = $this->custom_component_obj->_checkRevisedLockSubmittedAllowOrNotAllow();*/
		$lockandsubmittedallowornot =true;
        $master = $this->getMarksheetRequestStudentDetails($student_id, $mmr_id);
        $revised_correction_data = RevisedCorrection::where('marksheet_migration_request_id', $mmr_id)->get();
        if (count($request->all()) > 0) {
			$correctionAllowOrNot = $this->custom_component_obj->_checkRevisedAllowOrNotAllow();
			if(@$correctionAllowOrNot == false){
				return redirect()->route('studentsdashboards')->with('error','Correction Date has been closed.');
			} 
            $revalStudent = new MarksheetMigrationRequest; /// create model object
            $validator = Validator::make($request->all(), $revalStudent->ruleslocksubmit, $revalStudent->rulesapplicationandstudent);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            if ($request->locksumbitted == 'on') {
                $locksumbitted = '1';
            }
            $locksubmitted_date = date("Y-m-d H:i:s");

            $applicationarray = ['locksumbitted' => $locksumbitted, 'locksubmitted_date' => $locksubmitted_date];
            MarksheetMigrationRequest::where('id', $mmr_id)->update($applicationarray);
            $this->_marksheetCorrectionLockAndSubmittedMessage($student_id);
            if ($applicationarray) {
                return redirect()->route('corr_marksheet_previews', Crypt::encrypt($mmr_id))->with('message', 'Your complete details has been successfully submitted.');
            } else {
                return redirect()->route('corr_marksheet_previews', Crypt::encrypt($mmr_id))->with('error', 'Failed! Reval details has been not submitted');
            }

        }


        return view('resultupdate.marksheetcorrectionpreviews', compact('page_title', 'mmr_data', 'mmr_id', 'master', 'lockandsubmittedallowornot', 'student_id', 'revised_correction_data'));
    }


    public function marksheet_generate_student_pdf($mmrid = null)
    {
        $table = $model = "MarksheetMigrationRequest";
        $page_title = 'View Details';
        $mmrid = Crypt::decrypt($mmrid);
        $MarksheetStudent = MarksheetMigrationRequest::where('id', $mmrid)->first();
        $student_id = $MarksheetStudent->student_id;
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $master = Student::with('document', 'address', 'application')->where('id', $student_id)->first();
        $ReviserdStudentData = RevisedCorrection::where('marksheet_migration_request_id', $mmrid)->get();
        $subject_list = $this->subjectList($master->course);
        $master['MarksheetStudent'] = $MarksheetStudent;
        $master['ReviserdStudentData'] = $ReviserdStudentData;
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
        $combo_name = 'student_marksheet_document_path';
        $student_marksheet_document_path = $this->master_details($combo_name);
        $combo_name = 'reval_types';
        $reval_types = $this->master_details($combo_name);
        $combo_name = 'reval_per_subject_fee';
        $reval_per_subject_fee = $this->master_details($combo_name);
        $combo_name = 'marsheet_type';
        $marsheet_type = $this->master_details($combo_name);
        $combo_name = 'marksheet_print_option';
        $document_type = $this->master_details($combo_name);
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
        $studentDocumentPath = $student_document_path[1] . $student_id;
        $marksheetDocumentPath = $student_marksheet_document_path[1];
        $filename = $student_id . '_' . $MarksheetStudent->id . '_' . 'marksheet.pdf';
        $path = public_path($marksheetDocumentPath . "$student_id" . '/' . $filename);
        //return view('resultupdate.marksheet_generate_student_pdf', compact('reval_per_subject_fee','reval_types','mmrid','master','studentDocumentPath','stream_id','admission_sessions','exam_month','model','gender_id','categorya','nationality','religion','disability','dis_adv_group','midium','rural_urban','employment','pre_qualifi','adm_types','course','exam_session','rsos_years','aiCenters','are_you_from_rajasthan','marsheet_type','subject_list','are_you_from_rajasthan','yesno','document_type'));
        $pdf = PDF::loadView('resultupdate.marksheet_generate_student_pdf', compact('reval_per_subject_fee', 'reval_types', 'mmrid', 'master', 'studentDocumentPath', 'stream_id', 'admission_sessions', 'exam_month', 'model', 'gender_id', 'categorya', 'nationality', 'religion', 'disability', 'dis_adv_group', 'midium', 'rural_urban', 'employment', 'pre_qualifi', 'adm_types', 'course', 'exam_session', 'rsos_years', 'aiCenters', 'are_you_from_rajasthan', 'marsheet_type', 'subject_list', 'are_you_from_rajasthan', 'yesno', 'document_type'));
        $pdf->save($path, $pdf, true);
        return $pdf->download('marksheetCorreaction.pdf');
    }
	
	
	 public function view_result(Request $request)
    {
        /*
		$inputTemp = ($request->all());
		if($inputTemp['RJDOITC'] != 'RJDOITC'){
			return redirect()->route("landing")->with('error', 'You are not allowed to see the Result. Yet not declared !');
		}
		*/
        // return redirect('http://103.122.38.42/result');
        $showStatus = $this->_getCheckAllowToCheckResult();
		
        if (!$showStatus) {
            return redirect()->route("landing")->with('error', 'You are not allowed to see the Result. Yet not declared !');
        }
        $custom_component_obj = new CustomComponent;
        $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult();
		
        if (@$resultCheckStatus) {
        } else {
            return redirect()->route("landing")->with('error', 'Result yet not published!');
        }

        $title = "Download Result";
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
            $inputs = $request->all();
            $request->validate(['enrollment' => 'required|numeric|digits:11', 'dob' => 'required', 'captcha' => 'required|numeric']);

			//	$request->validate(['enrollment' => 'required|numeric|digits:11', 'dob' => 'required']);
            $captchaStatus = $custom_component_obj->checkCaptcha($inputs);
            if ($captchaStatus == false) {
                return redirect()->back()->with('error', 'Error : Captcha is invalid');
             }

            $combo_name = 'course';
            $course = $this->master_details($combo_name);
            $combo_name = 'result_type';
            $result_type = $this->master_details($combo_name);
            // $combo_name = 'exam_session';$exam_session = $this->master_details($combo_name);

            $combo_name = 'result_session';
            $result_session = $this->master_details($combo_name);
            $current_exam_month_id = Config::get('global.current_result_session_month_id');
            $result_session = $result_session[$current_exam_month_id];
            $subject_list = $this->subjectList();

            //$students = $custom_component_obj->getStudentProvisionalResult($request->enrollment, $request->dob);
			$dob = date("Y-m-d",strtotime($request->dob));
			$conditions = ['enrollment'=>$request->enrollment,'dob'=>$dob];
			$ResultComData=ProvisionalResultView::where($conditions)->orderBy('subject_code')->get();
			if(count($ResultComData) == 0){
				return redirect()->back()->with('error',"Result Not Found");
			}
			$students=array();
			$ResultComDataArr = $ResultComData->toArray();
			
		
			//$studentexamsubjects=array();
			$count=0;
			foreach(@$ResultComData as $data){
				$students["enrollment"] =@$data->enrollment;
				$students["student_id"] = @$data->student_id;
				$students["total_marks"] = @$data->total_marks;
				$students["exam_month"] =@$data->exam_month;
				$students["name"]=@$data->name;
				$students["father_name"]=@$data->father_name;
				$students["mother_name"]=@$data->mother_name;
				$students["dob"]=@$data->dob;
				$students["course"]=@$data->course;
				$students["additional_subjects"]=@$data->additional_subjects;
				$students["final_result"]=@$data->final_result;
				$students["is_temp_examresult"]=@$data->is_temp_examresult;
				$students["stream"]=@$data->stream;
				$students["revised"]=@$data->revised;
				 
				 
				// $studentexamsubjects=(object)$studentexamsubjects[$count];
				// $studentexamsubjects->
				$studentexamsubjects[$count]['student_id'] =@$data->student_id;
				$studentexamsubjects[$count]['subject_id'] =@$data->subject_id;
				$studentexamsubjects[$count]['final_theory_marks'] =@$data->final_theory_marks;
				$studentexamsubjects[$count]['sessional_marks_reil_result'] =@$data->sessional_marks_reil_result;
				$studentexamsubjects[$count]['final_practical_marks'] =@$data->final_practical_marks;
				$studentexamsubjects[$count]['total_marks'] =@$data->subject_total_marks;
				$studentexamsubjects[$count]['final_result'] =@$data->subject_result;
				$studentexamsubjects[$count] = (object)($studentexamsubjects[$count]);
				$count++;
				
			} 
			$students = (object)$students; 
            $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult(@$students->course);
            if (@$resultCheckStatus) {
            } else {
                return redirect()->back()->with('error', $course[$students->course] . ' Result yet not published!');
            }
            if (!empty($students)) {
                if ($students->is_temp_examresult == "111") {
                    return redirect()->back()->with('error', 'The Examination has been Cancelled.');
                }
                //$studentexamsubjects = $custom_component_obj->getStudentProvisionalResultSubject($students->student_id);
                if (empty(@$studentexamsubjects)) {
                    return redirect()->back()->with('error', 'Result Not Declare.');
                }
                return view('resultupdate.view_results', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type'));
            } else {
                return redirect()->back()->with('error', 'Result not found');
            }
        }
        $captchaImage = $custom_component_obj->generateCaptcha(1, 20, 150, 30);
        return view('resultupdate.view_result', compact('captchaImage', 'title', 'breadcrumbs'));
    }


}