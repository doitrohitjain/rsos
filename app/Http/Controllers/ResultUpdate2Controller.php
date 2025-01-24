<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\MarksheetCustomComponent;
use App\models\Address;
use App\models\Application;
use App\models\Document;
use App\models\ExamResult;
use App\models\ExamSubject;
use App\models\Pastdata;
use App\models\Student;
use App\models\Subject;
use App\models\Toc;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use PDF;
use Response;
use Session;

/* Hall Ticket Addtional Model */

//use PDF;
/* Hall Ticket Addtional Model */

class ResultUpdate2Controller extends Controller
{
    public $custom_component_obj = "";
    public $marksheet_component_obj = "";

    function __construct()
    {
        $this->custom_component_obj = new CustomComponent;
        $this->marksheet_component_obj = new MarksheetCustomComponent;
    }

    public function index(Request $request)
    {

        $title = "Result Update";
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_month_session = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $exam_year_session = $this->master_details($combo_name);

        $subjects = array();
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
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                'enrollment' => 'required||numeric|digits_between:11,15',
                'course' => 'required',
                'subjects' => 'required',
            ],
                [
                    'enrollment.required' => 'Enrollment is required',
                    'course.required' => 'Course is required',
                    'subjects.required' => 'Subjects is required',
                    'enrollment.numeric' => 'Please Enter number only',
                    'enrollment.digits_between' => 'Please enter vaild enrollment',
                ]);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $subjects = $this->custom_component_obj->subjectList($request->course);
            $data = DB::table('exam_subjects')->where([['enrollment', '=', $request->enrollment], ['course', '=', $request->course], ['subject_id', '=', $request->subjects]])->get();

            if ($data->isEmpty()) {
                return redirect('resultupdate')->with('error', 'Data not found')->withInput();
            }
            session()->flashInput($request->input());
        }
        return view('resultupdate.index', compact('breadcrumbs', 'title', 'course', 'subjects', 'data', 'exam_month_session', 'exam_year_session'));
    }

    public function edit_result($id = null, Request $request)
    {
        $title = "Edit Result";
        $result = array(
            '888' => 'SYC',
            '777' => 'SYCT',
            '666' => 'SYCP',
            'P' => 'PASS',
        );
        $obj_controller = new Controller();
        $tablename = 'exam_subjects';
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
        $data = ExamSubject::find($id);
        if ($request->isMethod('PUT')) {
            $validator = Validator::make($request->all(), [
                'sessional_marks' => 'required|numeric',
                'sessional_marks_reil_result' => 'required|numeric',
                'final_practical_marks' => 'required|numeric',
                'final_theory_marks' => 'required|numeric',
                'total_marks' => 'required|numeric',
                'final_result' => 'required'
            ],
                [
                    'sessional_marks.required' => 'Sessional Marks is required',
                    'sessional_marks_reil_result.required' => 'sessional_marks_reil_result is required.',
                    'final_practical_marks.required' => 'final_practical_marks is required.',
                    'final_theory_marks.required' => 'final_theory_marks is required.',
                    'total_marks.required' => 'total_marks is required.',
                    'final_result.required' => 'final_result is required.',
                    'sessional_marks.numeric' => 'Please enter number only',
                    'sessional_marks_reil_result.numeric' => 'Please enter number only.',
                    'final_practical_marks.numeric' => 'Please enter number only.',
                    'final_theory_marks.numeric' => 'Please enter number only.',
                    'total_marks.numeric' => 'Please enter number only.',

                ]

            );

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $enrollment = $data->enrollment;
            $exam_subject_log = $obj_controller->updateStudentLog($tablename, $id, $form_type);
            $master = ExamSubject::find($id)->update($request->all());
            if ($master) {
                return redirect()->route('finalupdate', $enrollment)->with('message', 'Result Updated successfully.');
            } else {
                return back()->with('error', 'Result Not Updated successfully');
            }
        }
        $toc_data = Toc::where('student_id', '=', $data->student_id)->first();
        if (!empty($toc_data)) {
            $request->session()->now('message', 'this is Toc Subject.');
        }
        return view('resultupdate.editresult', compact('title', 'breadcrumbs', 'data', 'result'));

    }

    public function update_final_result($enrollment = null, Request $request)
    {
        $title = "Update Marksheet";
        $obj_controller = new Controller();
        $tablename = 'exam_results';
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
        $data = ExamSubject::where('enrollment', '=', $enrollment)->orderBy('subject_id', 'asc')->get();
        $exam_result = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'desc')->first();
        if (empty($exam_result)) {
            return back()->with('message', 'Result Not Declare.');
        }
        if ($request->isMethod('put')) {
            $validator = Validator::make($request->all(), [
                'total_marks' => 'required|numeric',
                'final_result' => 'required',
                'percent_marks' => 'required|regex:/^\d*\.\d{0,2}$/|lt:101',
            ],
                [
                    'total_marks.required' => 'total_marks is required',
                    'final_result.required' => 'final_result is required',
                    'percent_marks.required' => 'Percentage is required',
                    'percent_marks.regex' => 'please Enter valid Percentage.',
                    'percent_marks.lt' => 'Enter a valid Percentage',
                    'total_marks.numeric' => 'Enter number only.',
                ],
            );
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $id = $exam_result->id;
            $enrollment = $exam_result->enrollment;
            $result_data = $request->all();
            $exam_subject_log = $obj_controller->updateStudentLog($tablename, $id, $form_type);
            $result = ExamResult::find($id)->update($result_data);
            if ($result) {
                return redirect()->route('finalupdate', $enrollment)->with('message', 'Result Update Successfully');
            } else {
                return back()->with('eror', 'Result Not Update Successfully');
            }
        }
        return view('resultupdate.finalupdate', compact('breadcrumbs', 'title', 'data', 'exam_result'));
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

        $title = "Duplicate Marksheet";

        $table_id = "Duplicate Marksheet";
        $formId = ucfirst(str_replace(" ", "-", $title));
        if (@$type == 1) {
            $marksheet_type = 'Duplicate';
        } else {
            $marksheet_type = 'Revised';
        }
        $documents = '';
        $totalMarks = 0;
        $grandFinalTotalMarks = 0;
        if (!($enrollment > 0) || $enrollment == null || $enrollment == '') {
            return redirect()->route('finalupdate', $enrollment)->with('message', "Enrollment is not in correct format.");
        }
        //$enrollment = $this->generate_barcode($enrollment);
        $serial_number = $this->marksheet_component_obj->getSerialNumber($enrollment);
        //@dd($serial_number);
        $examresultfields = array('final_result', 'exam_month', 'exam_year_id', 'total_marks', 'percent_marks');
        $final_result = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($examresultfields);
        $student = Student::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first();
        $resultDate = '';
        if (isset($final_result->exam_month) && isset($final_result->exam_year_id)) {
            $combination = $final_result->exam_month . ' ' . $final_result->exam_year_id;
        }
        $courseVal = '';
        $resultsyntax = array('999' => 'AB', '666' => 'SYCP', '777' => 'SYCT', '888' => 'SYC', 'P' => 'P');
        if (!empty($student)) { //fetch data from students

            $application = Application::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();

            $student->display_exam_month_year = $this->marksheet_component_obj->getDisplayExamMonthYear($combination);
            $newexamresultfields = array('exam_year_id', 'exam_month', 'result_date');
            $final_result_data = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($newexamresultfields);


            if (isset($final_result_data->result_date) && $final_result_data->result_date != "") {
                $resultDate = $final_result_data->result_date;
            }
            $documents = Document::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();
            $courseVal = $student->course;

            $address = Address::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();

            $findInFlag = 'Student';
            $dtarr = explode('-', $student->dob);
            $student->dob = $dtarr[2] . "-" . $dtarr[1] . "-" . $dtarr[0];

            //$student->dob = $application->dob;
        } else {
            if (empty($final_result)) {
                $final_result->final_result = $pastInfo->RESULT;
                $final_result->total_marks = $pastInfo->TOTAL_MARK;
                $final_result->percent_marks = $pastInfo->Percentage;

                $dtarr = explode('-', $pastInfo->ResultDate);
                $resultDate = $dtarr[2] . "-" . $dtarr[1] . "-" . $dtarr[0];

                if (isset($combination) && $combination != '') {
                    $student->display_exam_month_year = $this->marksheet_component_obj->getDisplayExamMonthYear($combination);
                }
            } else {
                $newexamresultfields = array('exam_year_id', 'exam_month', 'result_date');
                $final_result_data = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($examresultfields);

                if (isset($final_result_data->result_date) && $final_result_data->result_date != "") {
                    $resultDate = $final_result_data->result_date;
                }
                $student->display_exam_month_year = $this->marksheet_component_obj->getDisplayExamMonthYear($combination);
            }
            $student->ai_code = substr($pastInfo->ENROLLNO, 0, 5);

            $ai_code = $student->ai_code;


            $student->enrollment = $pastInfo->ENROLLNO;
            $student->name = $pastInfo->NAME;
            $student->father_name = $pastInfo->FNAME;
            $student->mother_name = $pastInfo->MNAME;
            $pastInfo->DOB = $new_date = $pastInfo->DOB;//date('d/m/Y', strtotime($pastInfo['Pastdata']['DOB']));
            $student->dob = $pastInfo->DOB;

            $student->aadhar_number = '';
            $student->mobile = '';

            $student->stream = 0;
            $student->application = '';
            $student->exam = 0;
            $student->course = $pastInfo->CLASS;
            $student->yy = $yy = substr($pastInfo->ENROLLNO, 5, 2);
            $student->student_code = $st_code = substr($pastInfo->ENROLLNO, 7);
            $courseVal = $pastInfo->CLASS;


            $addressTemp = $pastInfo->ADDRESS;
            if (isset($pastInfo->DISTRICT) && !empty($pastInfo->DISTRICT)) {
                $addressTemp .= ',' . $pastInfo->DISTRICT;
            }
            if (isset($pastInfo->STATE) && !empty($pastInfo->STATE)) {
                $addressTemp .= ',' . $pastInfo->STATE;
            }
            if (isset($pastInfo->PIN) && !empty($pastInfo->PIN)) {
                $addressTemp .= '-' . $pastInfo->PIN;
            }
            $address->address1 = $addressTemp;
            $address->address2 = '';
            $address->address3 = '';
            $address->city_name = '';
            $address->pincode = '';

            if (isset($pastInfo->MOBILE) && !empty($pastInfo->MOBILE)) {
                $student->mobile = $pastInfo->MOBILE;
            }


            if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'GENERAL_ADM' || $pastInfo->ERTYPE == 'STREAM2') {

                $student->adm_type = 1;
            } else if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'READMISSION') {

                $student->adm_type = 2;
            } else if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'PARTADMISSION') {

                $student->adm_type = 3;
            } else if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'IMPROVEMENT') {

                $student->adm_type = 4;
            } else if ($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'SUPPLEMENTARY') {

                $student->adm_type = 1;    //11 for supplementary ertype and admission type
            } else { //if ertype in pastdata table is balnk or null then use gen_adm adm_type

                $student->adm_type = 1;
            }

            $subjectCodes = $this->marksheet_component_obj->_getSubjectCodes($courseVal);

        }

        $examsubjectfields = array('id', 'subject_id', 'final_theory_marks', 'final_practical_marks', 'sessional_marks_reil_result', 'total_marks', 'final_result');
        $examSubjectsMarksData = ExamSubject::where('enrollment', '=', $enrollment)->orderBy('subject_id', 'desc')->get($examsubjectfields);


        $subjectCodeIds = $this->marksheet_component_obj->_getSubjectsCodeId($courseVal);

        if (empty($examSubjectsMarksData)) {
            /*if(isset($pastInfo->FRES1) && $pastInfo->FRES1 != ''){
				$examSubjectsMarksData[0]['ExamSubject']['subject'] = $subjectCodeIds[$pastInfo->EX_SUB1];
				$examSubjectsMarksData[0]['ExamSubject']['final_theory_marks'] = $pastInfo->FTM1;
				$examSubjectsMarksData[0]['ExamSubject']['final_practical_marks'] = $pastInfo->FPM1;
				$examSubjectsMarksData[0]['ExamSubject']['sessional_marks_reil_result'] = $pastInfo->fst1;
				$examSubjectsMarksData[0]['ExamSubject']['total_marks'] = $pastInfo->FTOT1;
				$examSubjectsMarksData[0]['ExamSubject']['final_result'] = $pastInfo->FRES1;
				$examSubjectsMarksData[0]['ExamSubject']['max_marks'] = $this->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB1]);
				$examSubjectsMarksData[0]['ExamSubject']['num_words'] = $this->numberInWord($examSubjectsMarksData[0]['ExamSubject']['total_marks']);	
				$examSubjectsMarksData[0]['ExamSubject']['grade_marks'] = $this->getGradeOfMarks($examSubjectsMarksData[0]['ExamSubject']['total_marks']);
							
			}
            if(isset($pastInfo['Pastdata']['FRES2']) && $pastInfo['Pastdata']['FRES2'] != '' ){
				$examSubjectsMarksData[1]['ExamSubject']['subject'] = $subjectCodeIds[$pastInfo['Pastdata']['EX_SUB2']];
				// $examSubjectsMarksData[0]['ExamSubject']['max_marks'] = $this->getSubjectMaxMarks($pastInfo['Pastdata']['EX_SUB2']);
				$examSubjectsMarksData[1]['ExamSubject']['final_theory_marks'] = $pastInfo['Pastdata']['FTM2'];
				$examSubjectsMarksData[1]['ExamSubject']['final_practical_marks'] = $pastInfo['Pastdata']['FPM2'];
				$examSubjectsMarksData[1]['ExamSubject']['sessional_marks'] = '';
				$examSubjectsMarksData[1]['ExamSubject']['total_marks'] = $pastInfo['Pastdata']['FTOT2'];
				$examSubjectsMarksData[1]['ExamSubject']['final_result'] = $pastInfo['Pastdata']['FRES2'];			
				$examSubjectsMarksData[1]['ExamSubject']['max_marks'] = $this->getSubjectMaxMarks($subjectCodeIds[$pastInfo['Pastdata']['EX_SUB2']]);
				$examSubjectsMarksData[1]['ExamSubject']['num_words'] = $this->numberInWord($examSubjectsMarksData[1]['ExamSubject']['total_marks']);	
				$examSubjectsMarksData[1]['ExamSubject']['grade_marks'] = $this->getGradeOfMarks($examSubjectsMarksData[1]['ExamSubject']['total_marks']);
			}
			if(isset($pastInfo['Pastdata']['FRES3']) && $pastInfo['Pastdata']['FRES3'] != ''){
				$examSubjectsMarksData[2]['ExamSubject']['subject'] = $subjectCodeIds[$pastInfo['Pastdata']['EX_SUB3']];
				// $examSubjectsMarksData[0]['ExamSubject']['max_marks'] = $this->getSubjectMaxMarks($pastInfo['Pastdata']['EX_SUB3']);
				$examSubjectsMarksData[2]['ExamSubject']['final_theory_marks'] = $pastInfo['Pastdata']['FTM3'];
				$examSubjectsMarksData[2]['ExamSubject']['final_practical_marks'] = $pastInfo['Pastdata']['FPM3'];
				$examSubjectsMarksData[2]['ExamSubject']['sessional_marks'] = '';
				$examSubjectsMarksData[2]['ExamSubject']['total_marks'] = $pastInfo['Pastdata']['FTOT3'];
				$examSubjectsMarksData[2]['ExamSubject']['final_result'] = $pastInfo['Pastdata']['FRES3'];
				$examSubjectsMarksData[2]['ExamSubject']['num_words'] = $this->numberInWord($examSubjectsMarksData[2]['ExamSubject']['total_marks']);
				$examSubjectsMarksData[2]['ExamSubject']['max_marks'] = $this->getSubjectMaxMarks($subjectCodeIds[$pastInfo['Pastdata']['EX_SUB3']]);
				$examSubjectsMarksData[2]['ExamSubject']['num_words'] = $this->numberInWord($examSubjectsMarksData[2]['ExamSubject']['total_marks']);	
				$examSubjectsMarksData[2]['ExamSubject']['grade_marks'] = $this->getGradeOfMarks($examSubjectsMarksData[2]['ExamSubject']['total_marks']);
			}
			if(isset($pastInfo['Pastdata']['FRES4']) && $pastInfo['Pastdata']['FRES4'] != ''){
				$examSubjectsMarksData[3]['ExamSubject']['subject'] = $subjectCodeIds[$pastInfo['Pastdata']['EX_SUB4']];
				$examSubjectsMarksData[3]['ExamSubject']['final_theory_marks'] = $pastInfo['Pastdata']['FTM4'];
				$examSubjectsMarksData[3]['ExamSubject']['final_practical_marks'] = $pastInfo['Pastdata']['FPM4'];
				$examSubjectsMarksData[3]['ExamSubject']['sessional_marks'] = '';
				$examSubjectsMarksData[3]['ExamSubject']['total_marks'] = $pastInfo['Pastdata']['FTOT4'];
				$examSubjectsMarksData[3]['ExamSubject']['final_result'] = $pastInfo['Pastdata']['FRES4'];				
				$examSubjectsMarksData[3]['ExamSubject']['max_marks'] = $this->getSubjectMaxMarks($subjectCodeIds[$pastInfo['Pastdata']['EX_SUB4']]);
				$examSubjectsMarksData[3]['ExamSubject']['num_words'] = $this->numberInWord($examSubjectsMarksData[3]['ExamSubject']['total_marks']);	
				$examSubjectsMarksData[3]['ExamSubject']['grade_marks'] = $this->getGradeOfMarks($examSubjectsMarksData[3]['ExamSubject']['total_marks']);
			}
			if(isset($pastInfo['Pastdata']['FRES5']) && $pastInfo['Pastdata']['FRES5'] != '')
			{
				$examSubjectsMarksData[4]['ExamSubject']['subject'] = $subjectCodeIds[$pastInfo['Pastdata']['EX_SUB5']];
				$examSubjectsMarksData[4]['ExamSubject']['final_theory_marks'] = $pastInfo['Pastdata']['FTM5'];
				$examSubjectsMarksData[4]['ExamSubject']['final_practical_marks'] = $pastInfo['Pastdata']['FPM5'];
				$examSubjectsMarksData[4]['ExamSubject']['sessional_marks'] = '';
				$examSubjectsMarksData[4]['ExamSubject']['total_marks'] = $pastInfo['Pastdata']['FTOT5'];
				$examSubjectsMarksData[4]['ExamSubject']['final_result'] = $pastInfo['Pastdata']['FRES5'];			
				$examSubjectsMarksData[4]['ExamSubject']['max_marks'] = $this->getSubjectMaxMarks($subjectCodeIds[$pastInfo['Pastdata']['EX_SUB5']]);
				$examSubjectsMarksData[4]['ExamSubject']['num_words'] = $this->numberInWord($examSubjectsMarksData[4]['ExamSubject']['total_marks']);	
				$examSubjectsMarksData[4]['ExamSubject']['grade_marks'] = $this->getGradeOfMarks($examSubjectsMarksData[4]['ExamSubject']['total_marks']);				
			}
			if(isset($pastInfo['Pastdata']['FRES6']) && $pastInfo['Pastdata']['FRES6'] != '')
			{
				$examSubjectsMarksData[5]['ExamSubject']['subject'] = $subjectCodeIds[$pastInfo['Pastdata']['EX_SUB9']];
				$examSubjectsMarksData[5]['ExamSubject']['final_theory_marks'] = $pastInfo['Pastdata']['FTM6'];
				$examSubjectsMarksData[5]['ExamSubject']['final_practical_marks'] = $pastInfo['Pastdata']['FPM6'];
				$examSubjectsMarksData[5]['ExamSubject']['sessional_marks'] = '';
				$examSubjectsMarksData[5]['ExamSubject']['total_marks'] = $pastInfo['Pastdata']['FTOT6'];
				$examSubjectsMarksData[5]['ExamSubject']['final_result'] = $pastInfo['Pastdata']['FRES6'];			
				$examSubjectsMarksData[5]['ExamSubject']['max_marks'] = $this->getSubjectMaxMarks($subjectCodeIds[$pastInfo['Pastdata']['EX_SUB9']]);
				$examSubjectsMarksData[5]['ExamSubject']['num_words'] = $this->numberInWord($examSubjectsMarksData[5]['ExamSubject']['total_marks']);	
				$examSubjectsMarksData[5]['ExamSubject']['grade_marks'] = $this->getGradeOfMarks($examSubjectsMarksData[5]['ExamSubject']['total_marks']);
			}
			if(isset($pastInfo['Pastdata']['FRES7']) && $pastInfo['Pastdata']['FRES7'] != ''){
				$examSubjectsMarksData[6]['ExamSubject']['subject'] = $subjectCodeIds[$pastInfo['Pastdata']['EX_SUB7']];
				$examSubjectsMarksData[6]['ExamSubject']['final_theory_marks'] = $pastInfo['Pastdata']['FTM7'];
				$examSubjectsMarksData[6]['ExamSubject']['final_practical_marks'] = $pastInfo['Pastdata']['FPM7'];
				$examSubjectsMarksData[6]['ExamSubject']['sessional_marks'] = '';
				$examSubjectsMarksData[6]['ExamSubject']['total_marks'] = $pastInfo['Pastdata']['FTOT7'];
				$examSubjectsMarksData[6]['ExamSubject']['final_result'] = $pastInfo['Pastdata']['FRES7'];			
				$examSubjectsMarksData[6]['ExamSubject']['max_marks'] = $this->getSubjectMaxMarks($subjectCodeIds[$pastInfo['Pastdata']['EX_SUB7']]);
				$examSubjectsMarksData[6]['ExamSubject']['num_words'] = $this->numberInWord($examSubjectsMarksData[6]['ExamSubject']['total_marks']);	
				$examSubjectsMarksData[6]['ExamSubject']['grade_marks'] = $this->getGradeOfMarks($examSubjectsMarksData[6]['ExamSubject']['total_marks']);
			}*/
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

        if (@($student->dob)) {
            $dobInWords = $this->marksheet_component_obj->getDObInWords($student->dob);
        }

        $subjects = $this->marksheet_component_obj->_getSubjectsForMarksheet($courseVal);

        // Get Barcode code
        $imagepath = asset('public/barcode/enrollment/' . $enrollment . '.png');
        $custom_component_obj = new CustomComponent;
        $barcode = $custom_component_obj->barcode($enrollment);
        $barcode_img = '<img src="' . $imagepath . '" alt=barcode-' . $enrollment . ' style="font-size:0;position:relative;width:132px;height:20px;" >';
        //echo $barcode_img = '<img src="'.$imagepath.'" alt=barcode-'.$enrollment.' style="font-size:0;position:relative;width:132px;height:20px;padding:300px" >';
        //die;
        // Get Barcode code

        // return view('resultupdate.marksheet_print_design', compact('barcode_img','formId','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','examSubjectsMarksData','subjects','resultDate','dobInWords','documents','marksheet_type'));
        $pdf = PDF::loadView('resultupdate.marksheet_print_design', compact('barcode_img', 'formId', 'final_result', 'enrollment', 'serial_number', 'student', 'dobInWords', 'resultsyntax', 'examSubjectsMarksData', 'subjects', 'resultDate', 'dobInWords', 'documents', 'marksheet_type'));//view file ka code
        $path = public_path('resultupdate\marksheet_' . $marksheet_type . '_' . $enrollment . '-' . date('d-m-Y-H-i-s') . '.pdf');//jahan pdf save karni hai
        $pdf->save($path, $pdf, true);
        return (Response::download($path));
    }

    public function download_duplicate_certificate_pdf($type = null, $enrollment = null)
    {

        $title = "Duplicate Certificate";
        $table_id = "Duplicate Certificate";
        $formId = ucfirst(str_replace(" ", "-", $title));
        if (@$type == 1) {
            $marksheet_type = 'Duplicate';
        } else {
            $marksheet_type = 'Revised';
        }
        $documents = '';
        $totalMarks = 0;
        $grandFinalTotalMarks = 0;
        if (!($enrollment > 0) || $enrollment == null || $enrollment == '') {
            return redirect()->route('finalupdate', $enrollment)->with('message', "Enrollment is not in correct format.");
        }
        $serial_number = $this->marksheet_component_obj->getSerialNumber($enrollment);
        //$pastInfo = $this->Pastdata->find('first',array('conditions'=>array('ENROLLNO'=>$enrollment),'order'=>array('id DESC')));
        $findInFlag = 'Pastdata';
        $pastInfo = Pastdata::where('ENROLLNO', '=', $enrollment)->orderBy('id', 'DESC')->first();
        $examresultfields = array('final_result', 'exam_month', 'exam_year_id', 'total_marks', 'percent_marks');
        $final_result = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($examresultfields);
        if (isset($final_result->exam_month) && isset($final_result->exam_year_id)) {
            $combination = $final_result->exam_month . ' ' . $final_result->exam_year_id;

        }
        $student = Student::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first();
        $resultsyntax = array('999' => 'AB', '666' => 'SYCP', '777' => 'SYCT', '888' => 'SYC', 'P' => 'P');
        if (!empty($student)) {
            $application = Application::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();

            $student->display_exam_month_year = $this->marksheet_component_obj->getDisplayExamMonthYear($combination);
            $newexamresultfields = array('exam_year_id', 'exam_month', 'result_date');
            $final_result_data = ExamResult::where('enrollment', '=', $enrollment)->orderBy('id', 'DESC')->first($newexamresultfields);


            if (isset($final_result_data->result_date) && $final_result_data->result_date != "") {
                $resultDate = strtotime($final_result_data->result_date);
                $resultDate = date("d-m-Y", $resultDate);
            }
            $courseVal = $student->course;

            $address = Address::where('student_id', '=', $student->id)->orderBy('id', 'DESC')->first();

            $findInFlag = 'Student';
            $dtarr = explode('-', $student->dob);
            $student->dob = $dtarr[2] . "-" . $dtarr[1] . "-" . $dtarr[0];
        } else {

        }
        $dobInWords = null;

        if (@($student->dob)) {

            $dobInWords = $this->marksheet_component_obj->getDObInWords($student->dob);
        }

        // Get Barcode code
        $imagepath = asset('public/barcode/enrollment/' . $enrollment . '.png');
        $custom_component_obj = new CustomComponent;
        $barcode = $custom_component_obj->barcode($enrollment);
        $barcode_img = '<img src="' . $imagepath . '" alt=barcode-' . $enrollment . ' style="font-size:0;position:relative;width:132px;height:20px;" >';
        //echo $barcode_img = '<img src="'.$imagepath.'" alt=barcode-'.$enrollment.' style="font-size:0;position:relative;width:132px;height:20px;padding:300px" >';
        //die;
        // Get Barcode code

        // return view('resultupdate.certificate_print_design', compact('barcode_img','formId','marksheet_type','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','resultDate','dobInWords','documents','combination'));
        $pdf = PDF::loadView('resultupdate.certificate_print_design', compact('barcode_img', 'formId', 'marksheet_type', 'final_result', 'enrollment', 'serial_number', 'student', 'dobInWords', 'resultsyntax', 'resultDate', 'dobInWords', 'documents', 'combination', 'marksheet_type'));//view file ka code
        $path = public_path('resultupdate\Certificate_' . $marksheet_type . '_' . $enrollment . '-' . date('d-m-Y-H-i-s') . '.pdf');//jahan pdf save karni hai
        $pdf->save($path, $pdf, true);
        return (Response::download($path));
    }


    public function Printmarksheetcertificate($enrollment = null)
    {
        $title = "Download Marksheet And Certificate";
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

    public function result2(Request $request)
    {
        $custom_component_obj = new CustomComponent;
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
            // 'date_of_birth' => 'date_format:Y-m-d|before:today|nullable'

            $captchaStatus = $custom_component_obj->checkCaptcha($inputs);
            if ($captchaStatus == false) {
                return redirect()->back()->with('error', 'Error : Captcha is invalid');
            }

            $combo_name = 'course';
            $course = $this->master_details($combo_name);
            $combo_name = 'exam_session';
            $exam_session = $this->master_details($combo_name);
            $subject_list = Subject::whereNull('deleted_at')->pluck('name', 'id');

            $students = $custom_component_obj->getresultstudentdata($request->enrollment, $request->dob);
            if (!empty($students)) {
                $studentexamsubjects = $custom_component_obj->getresultstudentalldata($students->student_id);
                return view('resultupdate.results', compact('students', 'studentexamsubjects', 'course', 'exam_session', 'subject_list'));
            } else {
                return redirect()->back()->with('error', 'Result not found');
            }
        }

        $captchaImage = $custom_component_obj->generateCaptcha(1, 99, 150, 30);
        return view('resultupdate2.result2', compact('captchaImage', 'title', 'breadcrumbs'));
    }


}
	
