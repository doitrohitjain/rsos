<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\ThoeryCustomComponent;
use App\Exports\AllotingCopiesExaminerExlExport;
use App\Helper\CustomHelper;
use App\Models\AllotingCopiesExaminer;
use App\Models\StudentAllotmentMark;
use App\Models\Subject;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Redirect;
use Response;
use Session;
use Validator;

class AllotingCopiesExaminerController extends Controller
{
    public $custom_component_obj = "";
    public $allotinCopiesexaminer = "";
    public $theory_custom_component_obj = " ";

    function __construct()
    {
        parent::__construct();
        $this->middleware('permission:alloting_copies', ['only' => ['index']]);
        $this->custom_component_obj = new CustomComponent;
        $this->theory_custom_component_obj = new ThoeryCustomComponent;
        $this->allotinCopiesexaminer = new AllotingCopiesExaminer;
    }

    public function index(Request $request)
    {
        $title = "Alloting Copies Examiner List";
        $table_id = "List of Examnier";
        $combo_name = 'course';
        $course_dropdown = $this->master_details($combo_name);
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
        $subjects_dropdown = array();
        $subject_list_dropdown = Subject::where('id', '<', '0')->pluck('name', 'id');
        $formId = ucfirst(str_replace(" ", "_", $title));
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
                'url' => 'downloadallotingcopiesexaminerExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'allotingCopiesListPdf',
                'status' => true
            ),
        );

        $filters = array(
            array(
                "lbl" => "Exam Center Fixcode",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examiner_list,
                'placeholder' => 'Exam Center',
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
                "lbl" => "Examiner sso",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => 'SSO',
                'dbtbl' => 'users',
                'status' => true
            ),
            array(
                "lbl" => "Examiner Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => 'Name',
                'dbtbl' => 'users',
                'status' => true
            ),

        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Exam Center Fixcode",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examiner_list,
                'search_type' => "text",
                'placeholder' => 'Course Type',
                'dbtbl' => 'marking_absent_students',
            ),

            array(
                "lbl" => "Course",
                'fld' => 'course_id',
                'input_type' => 'select',
                'options' => $course_dropdown,
                'search_type' => "text",
                'placeholder' => 'Course Type',
                'dbtbl' => 'marking_absent_students',
            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_name',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => 'marking_absent_students',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Examiner Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Mobile",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Total Students Appearing",
                'fld' => 'total_students_appearing',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => 'marking_absent_students',
            ),
            array(
                "lbl" => "Total Copies of the subject",
                'fld' => 'total_copies_of_subject',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => 'marking_absent_students',
            ),

            array(
                "lbl" => "Total Absent",
                'fld' => 'total_absent',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Total NR",
                'fld' => 'total_nr',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Date Of Allotment",
                'fld' => 'allotment_date',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => '',
            ),
        );

        $actions = array();
        if (in_array("theory_mark_unlock", $permissions)) {
            $actions[] = array(
                'fld' => 'view',
                'icon' => '<span class="btn btn-lg btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" title="Blank Scoring Sheet Download">Unlock&nbsp;Marks&nbsp;Entry</span>',
                'fld_url' => 'alloting_copies_examiners/unlock_theory_mark_entry/#id#'
            );

        }
        if (in_array("theory_examiner_mapping_delete", $permissions)) {
            $actions[] =
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to Delete.">delete</i>',
                    'fld_url' => 'alloting_copies_examiners/delete/#id#'
                );
        }

        $actions[] =
            array(
                'fld' => 'PDF',
                'icon' => '<span class="btn btn-lg btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" title="Blank Scoring Sheet Download">Blank&nbsp;Scoring&nbsp;Sheet</span>',
                'fld_url' => 'alloting_copies_examiners/blankScoringSheetPdf/#id#'
            );


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        $conditions = array();
        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);
        $master = $this->theory_custom_component_obj->getAllotingExaminerList($formId, true);
        //dd($formId);
        return view('alloting_copies_examiners.index', compact('title', 'breadcrumbs', 'exportBtn', 'master', 'tableData', 'actions', 'filters'));
    }


    public function downloadallotingcopiesexaminerExl(Request $request, $type = "xlsx")
    {
        $marking_exl_data = new AllotingCopiesExaminerExlExport;
        $filename = 'allotingcopiesexaminerexcel' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($marking_exl_data, $filename);
    }


    public function add(Request $request)
    {
        $title = "Allotting Examination Copies To Examiner";
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

        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $subjects = array();
        $current_exam_year = CustomHelper::_get_selected_sessions();
        $current_exam_month = Config::get('global.current_exam_month_id');
        $theoryExaminerList = $this->theory_custom_component_obj->getTheoryExaminer();
        if ($request->isMethod('post')) {
            $alredy_exist_user_data = $this->theory_custom_component_obj->checkAllotingExaminer($request->marking_absent_student_id, $request->user_id);
            if (isset($alredy_exist_user_data) && !empty($alredy_exist_user_data)) {
                return redirect()->route('alloting_copies_examiners.add')->with('error', 'Subject Already Mapped with this sso.')->withInput($request->all());
            }
            $alreayexistsmarkingabsentid = $this->theory_custom_component_obj->checkAllotingMarkingAbsent($request->marking_absent_student_id);

            if (@$alreayexistsmarkingabsentid && !empty($alreayexistsmarkingabsentid)) {
                $updatedata = AllotingCopiesExaminer::where('id', '=', @$alreayexistsmarkingabsentid->id)->update(['is_changed' => 1, 'changed_date' => Carbon::now(), 'deleted_at' => Carbon::now(), 'changed_by_user_id' => auth::user()->id]);
            }
            $svData = array();
            $svData['marking_absent_student_id'] = $request->marking_absent_student_id;
            $svData['user_id'] = $request->user_id;
            $svData['allotment_date'] = date('Y-m-d H:i:s');
            $svData['is_changed'] = 0;
            $allotCopies = AllotingCopiesExaminer::create($svData);

            //here put the theory_examiner_id in student allomtent marks table.
            $updateStudentAllotmentMarks['theory_examiner_id'] = $request->user_id;
            StudentAllotmentMark::where('course', '=', $request->course_id)
                ->where('examcenter_detail_id', '=', $request->examcenter_detail_id)
                ->where('subject_id', '=', $request->subject_id)
                ->where('exam_year', '=', $current_exam_year)
                ->where('exam_month', '=', $current_exam_month)
                ->update($updateStudentAllotmentMarks);

            if ($allotCopies) {
                return redirect()->back()->with('message', 'Data save succesfully');
            }

        }
        return view('alloting_copies_examiners.add', compact('breadcrumbs', 'title', 'theoryExaminerList', 'examiner_list', 'course', 'subjects'));
    }

    public function view($id = null, Request $request)
    {

    }
    // public function edit($id=null,Request $request){
    // 	echo "edit alloting copies examiners theory code here "; die;
    // }

    public function delete($id = null, Request $request)
    {
        $id = Crypt::decrypt($id);
        $data = $this->theory_custom_component_obj->getAllotData($id);
        $current_exam_year = CustomHelper::_get_selected_sessions();
        $current_exam_month = Config::get('global.current_exam_month_id');
        if (!empty($data)) {
            $allotdata = $this->theory_custom_component_obj->getAllotDataAccordingUserId($data->usersid, $data->course_id, $data->subject_id, $data->examcenter_detail_id);
            if (isset($allotdata) && !empty($allotdata)) {
                foreach ($allotdata as $allotdata1) {
                    $updatedata['final_theory_marks'] = 0;
                    $updatedata['is_theory_lock_submit'] = 0;
                    $updatedata['theory_examiner_id'] = NULL;
                    $updatedata['theory_lock_submit_user_id'] = NULL;
                    $studentAllotmentdata = StudentAllotmentMark::where('id', '=', $allotdata1->id)->where('exam_year', '=', $current_exam_year)->where('exam_month', '=', $current_exam_month)->update($updatedata);
                }
            }
        }
        AllotingCopiesExaminer::where('id', '=', $id)->update(['deleted_at' => Carbon::now(), 'deleted_by' => auth::user()->id]);
        return back()->with('message', 'Alloting Deleted Successfully');
    }

    public function allotingCopiesListPdf(Request $request)
    {
        $title = "Alloting Copies Examiner List";
        $table_id = "Alloting Copies Examiner List";
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $formId = ucfirst(str_replace(" ", "_", $title));
        $conditions = array();
        Session::put($formId . '_condtions', $conditions);
        $result = $this->theory_custom_component_obj->getAllotingExaminerList($formId, false);

        if (empty($result)) {
            return redirect()->route('/')->with('error', 'Failed! Details not found');
        }

        // return view('alloting_copies_examiners.alloting_copies_list_pdf', compact('formId','result','title','examiner_list','course'));

        $pdf = PDF::loadView('alloting_copies_examiners.alloting_copies_list_pdf', compact('formId', 'result', 'title', 'examiner_list', 'course'));
        $pdf->setOption('footer-right', 'Page [page] of [toPage]');
        $path = public_path('alloting_copies_examiners\Pdf-Thoery-' . $formId . '-' . date('d-m-Y-H-i-s') . '.pdf');
        $pdf->save($path, $pdf, true);
        return (Response::download($path));
    }

    public function blankScoringSheetPdf($id = null, Request $request)
    {
        $id = Crypt::decrypt($id);
        $data = $this->theory_custom_component_obj->getAllotData($id);

        $title = "Blank Scoring sheets";
        $formId = ucfirst(str_replace(" ", "-", $title));

        $current_exam_year = CustomHelper::_get_selected_sessions();
        $current_exam_month = Config::get('global.current_exam_month_id');

        $combo_name = 'exam_month';
        $exam_sessions = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $title2 = "Evaluation Answer Sheet For Examiners" . ' ' . $exam_sessions[@$current_exam_month] . ' ' . date('Y');

        $sso_id = $data->ssoid;
        $course_id = $data->course_id;
        $subject_id = $data->subject_id;
        $examcenter_detail_id = $data->examcenter_detail_id;
        $result = $this->theory_custom_component_obj->get_appearing_student_listing($course_id, $subject_id, $examcenter_detail_id);
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
        $subject_list_dropdown = Subject::pluck('subject_code', 'id');
        $subjects = $this->custom_component_obj->subjectList();

        $username = $this->custom_component_obj->getSSOIDDetials($sso_id);
        $usersname = json_decode($username);
        $markingAbsentData = $this->theory_custom_component_obj->getMarkingAbsentStudent($course_id, $examcenter_detail_id, $subject_id);
        $getMaxMarks = $this->theory_custom_component_obj->getTheoryMaxMarks($subject_id);
        $foldarName = 'alloting_copies_examiners' . '/' . $current_folder_year[$current_exam_year] .
            '/' . 'stream' . $current_exam_month . '/' . $course_id . '/' . $subject_list_dropdown[$subject_id] .
            '/' . $examcenter_detail_id . '/' . '-' . $formId . '-' . date('d-m-Y-H-i-s') . '.pdf';

        // return view('alloting_copies_examiners.blank_scoreing_sheet_pdf',compact('course_id','subject_id','examcenter_detail_id','examiner_list','course','subjects','sso_id','usersname','markingAbsentData','getMaxMarks','result','current_exam_month','exam_sessions'));
        $pdf = PDF::loadView('alloting_copies_examiners.blank_scoreing_sheet_pdf', compact('course_id', 'title2', 'subject_id', 'examcenter_detail_id', 'examiner_list', 'course', 'subjects', 'admission_sessions', 'sso_id', 'usersname', 'markingAbsentData', 'getMaxMarks', 'result', 'current_exam_year', 'current_exam_month', 'exam_sessions'));
        $pdf->setOption('footer-right', 'Page [page] of [toPage]');
        $path = public_path($foldarName);
        $pdf->save($path, $pdf, true);
        return (Response::download($path));
    }

    public function unlockMarkEntry($id = null)
    {
        $id = Crypt::decrypt($id);
        $user_id = auth()->user()->id;
        $user_role_id = Session::get('role_id');
        $svdata = ['theory_lastpage_submitted_date' => null, 'marks_entry_completed' => 0, 'unlock_user_id' => $user_id, 'unlock_role_id' => $user_role_id];
        $result = AllotingCopiesExaminer::where('id', '=', $id)->update($svdata);
        return redirect()->back()->with('message', 'Marks Entry unlock.');
    }

}




