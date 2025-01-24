<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\ThoeryCustomComponent;
use App\Helper\CustomHelper;
use App\Models\AllotingCopiesExaminer;
use App\Models\MarkingAbsentStudent;
use App\Models\StudentAllotmentMark;
use App\Models\Subject;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use PDF;
use Redirect;
use Response;
use Session;
use Validator;

class TheroryMarkSubmissionController extends Controller
{
    public $custom_component_obj = "";
    public $allotinCopiesexaminer = "";
    public $theory_custom_component_obj = " ";

    function __construct()
    {
        parent::__construct();
        $this->middleware('permission:therory_mark_submission', ['only' => ['index']]);
        $this->custom_component_obj = new CustomComponent;
        $this->theory_custom_component_obj = new ThoeryCustomComponent;
        $this->allotinCopiesexaminer = new AllotingCopiesExaminer;
    }

    public function index(Request $request)
    {
        $title = " Mark Submissions list";
        $table_id = " Mark Submissions list";
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $course_dropdown = $this->master_details($combo_name);
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
        $yes_no = $this->master_details('yesno');
        $formId = ucfirst(str_replace(" ", "_", $title));
        $subjects_dropdown = array();
        $subject_list_dropdown = Subject::where('id', '<', '0')->pluck('name', 'id');
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

        $filters = array(
            array(
                "lbl" => "Exam Center Fixcode",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examiner_list,
                'placeholder' => 'Exam Center Fixcode',
                'dbtbl' => 'marking_absent_students',
                'status' => true
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course_id',
                'input_type' => 'select',
                'options' => $course_dropdown,
                'placeholder' => 'Course',
                'dbtbl' => 'marking_absent_students',
                'status' => true
            ),

            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list_dropdown,
                'placeholder' => 'Subject',
                'dbtbl' => 'marking_absent_students',
                'status' => true
            ),

            array(
                "lbl" => "Is Lock & Sumbitted",
                'fld' => 'marks_entry_completed',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is Lock & Sumbitted',
                'dbtbl' => 'alloting_copies_examiners',
                'status' => true
            ),

        );

        $exportBtn = array(
            array(
                "label" => "Export Excel",
                'url' => 'downloadStudentFeesExl',
                'status' => false,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadStudentFeesPdf',
                'status' => false
            ),
        );
        $conditions = array();

        $actions = array();


        if ($request->all()) {
            $inputs = $request->all();
            // dd($inputs);
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                            $conditions[$iv['dbtbl'] . "." . $k] = $v;
                        }
                    }
                }
            }

        }

        Session::put($formId . '_conditions', $conditions);
        $master = $this->theory_custom_component_obj->allotinsubjectcopies($formId, true);

        $isAdminStatus = $this->custom_component_obj->_checkIsAdminRole();
        if ($isAdminStatus) {
            $isAllowDate = true;
        } else {
            $isAllowDate = $this->theory_custom_component_obj->getTheoryAllowOrNot();
        }

        return view('therory_mark_submissions.index', compact('permissions', 'isAllowDate', 'title', 'breadcrumbs', 'exportBtn', 'master', 'course', 'examiner_list', 'filters', 'subjects_dropdown'));

    }

    public function add($id = null, Request $request)
    {
        $isAdminStatus = $this->custom_component_obj->_checkIsAdminRole();
        if ($isAdminStatus) {
            $isAllowDate = true;
        } else {
            $isAllowDate = $this->theory_custom_component_obj->getTheoryAllowOrNot();
        }
        if ($isAllowDate) {
        } else {
            return redirect()->route('theroyexaminerdashboard')->with('error', 'Theory marks submission date not open.');
        }
        $title = " Add Marks";
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
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

        $id = Crypt::decrypt($id);
        $data = MarkingAbsentStudent::find($id);
        $defaultPageLimit = config("global.defaultPageLimit");
        $userdetails = $this->theory_custom_component_obj->getExminerdetails(@$id);
        $subjects = $this->custom_component_obj->subjectList();
        $getMaxMarks = $this->theory_custom_component_obj->getTheoryMaxMarks(@$data->subject_id);
        $maxmarks = @$getMaxMarks->theory_max_marks;

        $result = $this->theory_custom_component_obj->getTheoryAllotmentMarks(@$data->examcenter_detail_id, @$data->course_id, @$data->subject_id);
        $student_allotment_marks_id = @$data['student_allotment_marks_id'];
        // if($request->isMethod('get')){
        //     if($result->currentPage()>1 ){
        // 		dd($results->perPage());
        // 		$resultdata=$result->previous();
        // 		dd($resultdata);
        // 		 foreach($resultdata as $resultdata1){
        // 			if($resultdata1->theory_absent==0 && $resultdata1->is_theory_lock_submit==0){
        // 			return redirect()->back()->with('error','hiii baby');
        // 		   }
        // 		 }
        // 	}
        // }


        if ($request->isMethod('post')) {
            $maxmarks = $getMaxMarks->theory_max_marks;
            $dataUpdateCounter = 0;
            $svData = $request->all();
            $response = $this->theory_custom_component_obj->isvalidtheorymarks($svData, $getMaxMarks->theory_max_marks, 0);
            $isValid = $response['isValid'];
            $customerrors = "Error: " . $response['errors'];
            $validator = $response['validator'];
            foreach ($svData['data'] as $dataEach) {
                if (isset($dataEach['theory_absent']) && ($dataEach['theory_absent'] == 'on' || $dataEach['theory_absent'] == '1')) {
                    $updateMarks['theory_absent'] = 1;
                    $updateMarks['final_theory_marks'] = 0;
                } else if (isset($dataEach['theory_absent_nr']) && ($dataEach['theory_absent_nr'] == 'on' || $dataEach['theory_absent_nr'] == '2')) {
                    $updateMarks['theory_absent'] = 2;
                    $updateMarks['final_theory_marks'] = 0;
                } else {
                    $updateMarks['theory_absent'] = 0;
                    $updateMarks['final_theory_marks'] = @$dataEach['final_theory_marks'];
                }

                $updateMarks['is_theory_lock_submit'] = 1;
                $updateMarks['theory_examiner_id'] = $userdetails['id'];
                $updateMarks['theory_lock_submit_user_id'] = $userdetails['id'];

                $allotmentMarksId = Crypt::decrypt(@$dataEach['student_allotment_marks_id']);
                $entermaks = StudentAllotmentMark::where('id', '=', $allotmentMarksId)->update($updateMarks);
                $dataUpdateCounter++;

                //  update count MarkingAbsentStudent table
                $absentCount = $this->theory_custom_component_obj->getTheoryAbsentCount(@$data->course_id, @$data->subject_id, @$data->examcenter_detail_id);

                $nrCount = $this->theory_custom_component_obj->getTheoryNrCount(@$data->course_id, @$data->subject_id, @$data->examcenter_detail_id);

                $total_copies_of_subject = @$data->total_students_appearing - ($absentCount + $nrCount);

                $updateMarkingAbNr = array();
                $updateMarkingAbNr['total_copies_of_subject'] = $total_copies_of_subject;
                $updateMarkingAbNr['total_absent'] = @$absentCount;
                $updateMarkingAbNr['total_nr'] = @$nrCount;

                $entermaks = MarkingAbsentStudent::where('id', '=', $id)->update($updateMarkingAbNr);
                //  update count MarkingAbsentStudent table
            }
            $currentPageId = Crypt::decrypt($request->current_page_id);
            $lastPageId = Crypt::decrypt($request->last_page_id);
            $next_page_id = $currentPageId + 1;
            if ($lastPageId == $currentPageId) {
                return redirect()->route('theorymarkpreview', Crypt::encrypt($id))->with('message', ' Student marks has been successfully saved.');
            } else {
                return redirect()->route('theory_add_marks', Crypt::encrypt($id) . '?page=' . $next_page_id)->with('message', $dataUpdateCounter . ' Student marks has been successfully saved.');
            }
        }
        return view('therory_mark_submissions.add', compact('title', 'breadcrumbs', 'data', 'examiner_list', 'course', 'subjects', 'getMaxMarks', 'userdetails', 'defaultPageLimit', 'result', 'maxmarks', 'student_allotment_marks_id'));
    }

    public function edit($id = null, Request $request)
    {
        $isAdminStatus = $this->custom_component_obj->_checkIsAdminRole();
        if ($isAdminStatus) {
            $isAllowDate = true;
        } else {
            $isAllowDate = $this->theory_custom_component_obj->getTheoryAllowOrNot();
        }
        if ($isAllowDate) {
        } else {
            return redirect()->route('theroyexaminerdashboard')->with('error', 'Theory marks submission date not open.');
        }

        $title = "Update Marks";
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
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
        $id = Crypt::decrypt($id);
        $data = MarkingAbsentStudent::find($id);


        $data2 = AllotingCopiesExaminer::where('marking_absent_student_id', @$data->id)->first();
        if (@$data2->marks_entry_completed && $data2->marks_entry_completed == 1) {
            return redirect()->back()->withErrors("The selected combination already locked and submitted.")->withInput($request->all());
        }

        $defaultPageLimit = config("global.defaultPageLimit");
        $userdetails = $this->theory_custom_component_obj->getExminerdetails(@$id);


        $subjects = $this->custom_component_obj->subjectList();
        $getMaxMarks = $this->theory_custom_component_obj->getTheoryMaxMarks(@$data->subject_id);
        $maxmarks = $getMaxMarks->theory_max_marks;

        $result = $this->theory_custom_component_obj->getTheoryAllotmentMarks(@$data->examcenter_detail_id, @$data->course_id, @$data->subject_id);
        if ($request->isMethod('post')) {
            // dd($request->all());
            $maxmarks = $getMaxMarks->theory_max_marks;
            $dataUpdateCounter = 0;
            $svData = $request->all();
            $response = $this->theory_custom_component_obj->isvalidtheorymarks($svData, $getMaxMarks->theory_max_marks, 0);
            $isValid = $response['isValid'];
            $customerrors = "Error: " . $response['errors'];
            $validator = $response['validator'];

            foreach ($svData['data'] as $dataEach) {
                if (isset($dataEach['theory_absent']) && ($dataEach['theory_absent'] == 'on' || $dataEach['theory_absent'] == '1')) {
                    $updateMarks['theory_absent'] = 1;
                    $updateMarks['final_theory_marks'] = 0;
                } else if (isset($dataEach['theory_absent_nr']) && ($dataEach['theory_absent_nr'] == 'on' || $dataEach['theory_absent_nr'] == '2')) {
                    $updateMarks['theory_absent'] = 2;
                    $updateMarks['final_theory_marks'] = 0;
                } else {
                    $updateMarks['theory_absent'] = 0;
                    $updateMarks['final_theory_marks'] = @$dataEach['final_theory_marks'];
                }

                $updateMarks['is_theory_lock_submit'] = 1;
                $updateMarks['theory_examiner_id'] = $userdetails['id'];
                $updateMarks['theory_lock_submit_user_id'] = $userdetails['id'];
                $allotmentMarksId = Crypt::decrypt(@$dataEach['student_allotment_marks_id']);
                $entermaks = StudentAllotmentMark::where('id', '=', $allotmentMarksId)->update($updateMarks);
                $dataUpdateCounter++;

                //  update count MarkingAbsentStudent table
                $absentCount = $this->theory_custom_component_obj->getTheoryAbsentCount(@$data->course_id, @$data->subject_id, @$data->examcenter_detail_id);

                $nrCount = $this->theory_custom_component_obj->getTheoryNrCount(@$data->course_id, @$data->subject_id, @$data->examcenter_detail_id);

                $total_copies_of_subject = @$data->total_students_appearing - ($absentCount + $nrCount);

                $updateMarkingAbNr = array();
                $updateMarkingAbNr['total_copies_of_subject'] = $total_copies_of_subject;
                $updateMarkingAbNr['total_absent'] = @$absentCount;
                $updateMarkingAbNr['total_nr'] = @$nrCount;

                $entermaks = MarkingAbsentStudent::where('id', '=', $id)->update($updateMarkingAbNr);
                //  update count MarkingAbsentStudent table
            }
            $currentPageId = Crypt::decrypt($request->current_page_id);
            $lastPageId = Crypt::decrypt($request->last_page_id);
            $next_page_id = $currentPageId + 1;

            if ($lastPageId == $currentPageId) {
                return redirect()->route('theorymarkpreview', Crypt::encrypt($id))->with('message', 'Student marks has been successfully saved.');
            } else {
                return redirect()->route('theory_Edit_marks', Crypt::encrypt($id) . '?page=' . $next_page_id)->with('message', $dataUpdateCounter . ' Student marks has been successfully saved.');
            }
        }

        return view('therory_mark_submissions.edit', compact('title', 'breadcrumbs', 'data', 'examiner_list', 'course', 'subjects', 'getMaxMarks', 'userdetails', 'defaultPageLimit', 'result', 'maxmarks'));
    }

    public function theoryMarkSubmmisiosnPreview($id = null, Request $request)
    {
        $isAdminStatus = $this->custom_component_obj->_checkIsAdminRole();
        if ($isAdminStatus) {
            $isAllowDate = true;
        } else {
            $isAllowDate = $this->theory_custom_component_obj->getTheoryAllowOrNot();
        }
        if ($isAllowDate) {
        } else {
            return redirect()->route('theroyexaminerdashboard')->with('error', 'Theory marks submission date not open.');
        }

        $eid = $id;

        $id = Crypt::decrypt($id);
        $title = "Preview";
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
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

        $data = MarkingAbsentStudent::find($id);

        $defaultPageLimit = config("global.defaultPageLimit");
        $userdetails = $this->theory_custom_component_obj->getExminerdetails(@$id);
        $subjects = $this->custom_component_obj->subjectList();
        $getMaxMarks = $this->theory_custom_component_obj->getTheoryMaxMarks(@$data->subject_id);
        $result = $this->theory_custom_component_obj->getTheoryMarks(@$data->examcenter_detail_id, @$data->course_id, @$data->subject_id);


        $nullMarksStudentCount = $this->theory_custom_component_obj->getTheoryMarkYetNotFilled(@$data->examcenter_detail_id, @$data->course_id, @$data->subject_id);
        $message = null;

        if ($nullMarksStudentCount > 0) {
            $message = $nullMarksStudentCount . " Students marks not yet entered please enter marks first.";
        }

        if ($request->all()) {
            if ($nullMarksStudentCount > 0) {
                return redirect()->route('theory_add_marks', Crypt::encrypt($id))->with('error', 'All students marks not yet entered please enter marks first.');
            }
            $updateData['marks_entry_completed'] = 1;
            $updateData['theory_lastpage_submitted_date'] = Carbon::now();
            $updateData['marks_entry_completed_date'] = Carbon::now();
            $data = AllotingCopiesExaminer::where('marking_absent_student_id', '=', Crypt::decrypt($request['Marking_absent_id']))->update($updateData);
            return redirect()->route('therory_mark_submissions')->with('message', 'Marks Submmited successfully');
        }
        return view('therory_mark_submissions.preview', compact('eid', 'message', 'title', 'breadcrumbs', 'userdetails', 'data', 'getMaxMarks', 'result', 'id', 'examiner_list', 'course', 'subjects'));
    }


    public function theoryMarkPdf($id = null, Request $request)
    {
        $id = Crypt::decrypt($id);
        $formId = "Mark SHeets";

        $current_exam_year = CustomHelper::_get_selected_sessions();
        $current_exam_month = Config::get('global.current_exam_month_id');
        $combo_name = 'exam_month';
        $exam_sessions = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $subject_list_dropdown = Subject::pluck('subject_code', 'id');

        $title = "Evaluation Answer Sheet For Examiners" . ' ' . $exam_sessions[$current_exam_month] . ' ' . date('Y');
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
        $data = MarkingAbsentStudent::find($id);

        $defaultPageLimit = config("global.defaultPageLimit");
        $userdetails = $this->theory_custom_component_obj->getExminerdetails(@$id);
        $subjects = $this->custom_component_obj->subjectList();
        $getMaxMarks = $this->theory_custom_component_obj->getTheoryMaxMarks(@$data->subject_id);
        $result = $this->theory_custom_component_obj->getTheoryMarks(@$data->examcenter_detail_id, @$data->course_id, @$data->subject_id);
        $pdfname = "TheoryMarkEntryPdf";

        $foldarName = 'theorymarkentrypdf' . '/' . $current_folder_year[$current_exam_year] .
            '/' . 'stream' . $current_exam_month . '/' . @$data->course_id . '/' .
            @$subject_list_dropdown[@$data->subject_id] . '/' .
            @$data->examcenter_detail_id . '/' . '_' . $pdfname . '_' . date('d-m-Y-H-i-s') . '.pdf';

        // return view('therory_mark_submissions.theory_mark_pdf',compact('userdetails','data','getMaxMarks','result','id','examiner_list','course','subjects','title'));
        $pdf = PDF::loadView('therory_mark_submissions.theory_mark_pdf', compact('userdetails', 'data', 'getMaxMarks', 'result', 'id', 'examiner_list', 'course', 'subjects', 'title'));
        $pdf->setOption('footer-right', 'Page [page] of [toPage]');
        $path = public_path($foldarName);
        $pdf->save($path, $pdf, true);
        return (Response::download($path));

    }


}




