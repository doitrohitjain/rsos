<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\ThoeryCustomComponent;
use App\Exports\MarkingExlExport;
use App\Helper\CustomHelper;
use App\Models\MarkingAbsentStudent;
use App\Models\StudentAllotmentMark;
use App\Models\Subject;
use Auth;
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

class MarkingAbsentController extends Controller
{
    public $custom_component_obj = "";
    public $theory_custom_component_obj = " ";

    function __construct()
    {
        parent::__construct();
        $this->middleware('permission:marking_absent', ['only' => ['index']]);
        $this->middleware('permission:Marking_Absent_Edit', ['only' => ['edit']]);
        $this->custom_component_obj = new CustomComponent;
        $this->theory_custom_component_obj = new ThoeryCustomComponent;
    }

    public function index(Request $request)
    {
        $title = "Marking Absent List";
        $table_id = "List Of Student Marked Absent";
        $combo_name = 'course';
        $course_dropdown = $this->master_details($combo_name);
        $subjects_dropdown = array();
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
        $theory_Custom_component = new ThoeryCustomComponent;
        $current_exam_year = CustomHelper::_get_selected_sessions();
        $current_exam_month = Config::get('global.current_exam_month_id');
        $exam_year = $theory_Custom_component->getDatatomaster('admission_sessions', $current_exam_year);
        $exam_sessions = $theory_Custom_component->getDatatomaster('exam_session', $current_exam_month);
        $formId = ucfirst(str_replace(" ", "_", $title));
        $combo_name = 'course';
        $course_dropdown = $this->master_details($combo_name);
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
        $subjects_dropdown = array();
        $permissions = CustomHelper::roleandpermission();
        $subject_list_dropdown = Subject::where('id', '<', '0')->pluck('name', 'id');

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
                'url' => 'markingAbsentexceldownload',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'markingAbsentListPdf',
                'status' => true
            ),
        );

        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Exam Center",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examiner_list,
                'search_type' => "text",
                'placeholder' => 'Course Type',
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course_id',
                'input_type' => 'select',
                'options' => $course_dropdown,
                'search_type' => "text",
                'placeholder' => 'Course Type',
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_name',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Students Appearing",
                'fld' => 'total_students_appearing',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Copies of the subject",
                'fld' => 'total_copies_of_subject',
                'input_type' => 'text',
                'placeholder' => "",
                'dbtbl' => '',
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
                "lbl" => "Exam Year",
                'fld' => 'exam_year',
                'input_type' => 'select',
                'options' => $exam_year,
                'search_type' => "text",
                'placeholder' => 'Course Type',
                'dbtbl' => '',
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
        );

        if (in_array("Marking_Absent_Edit", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="btn btn-default" title="Click here to View Students List.">StudentList</i>',
                    'fld_url' => 'marking_absents/view/#id#'
                ),
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => 'marking_absents/edit/#id#'
                ),


            );
        } else {
            $actions = array(
                array(
                    'fld' => 'view',
                    'icon' => '<i class="btn btn-default" title="Click here to View Students List.">StudentList</i>',
                    'fld_url' => 'marking_absents/view/#id#'
                )
            );
        }
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
        $master = $this->theory_custom_component_obj->getMarkingAbsentStudentList($formId, true);
        return view('marking_absents.index', compact('title', 'breadcrumbs', 'master', 'exportBtn', 'tableData', 'actions', 'filters', 'course_dropdown'));


    }

    public function markingAbsentexceldownload(Request $request, $type = "xlsx")
    {
        $marking_exl_data = new MarkingExlExport;
        $filename = 'markingAbsentexcel' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($marking_exl_data, $filename);
    }

    public function add(Request $request)
    {
        $title = "Marking Absent Student";
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
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $subjects = array();
        $theory_Custom_component = new ThoeryCustomComponent;
        $current_exam_year = CustomHelper::_get_selected_sessions();
        $current_exam_month = Config::get('global.current_exam_month_id');
        $exam_year_session = $theory_Custom_component->getDatatomaster('admission_sessions', $current_exam_year);
        $exam_month_session = $theory_Custom_component->getDatatomaster('exam_session', $current_exam_month);
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();

        // $examiner_list = $examiner_list->toArray();
        $allotmentdata = array();
        $conditions = array();
        if ($request->isMethod('post')) {
            $svData = $request->all();
            $marking = new MarkingAbsentStudent();
            $validator = Validator::make($request->all(), $marking->rules, $marking->message);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $allreadyExistData = $theory_Custom_component->checkMarkingAbsent(@$request->examcenter_detail_id, @$request->course_id, @$request->subject_id, @$current_exam_year, $current_exam_month);
            if (!empty($allreadyExistData)) {
                return back()->with('error', "Selected Marking Absent Combination already exists.")->withInput($request->all());
            }

            if (@$svData['Absent']) {
                foreach ($svData['Absent'] as $data) {
                    $allotmentdata = array();

                    $allotmentdata['fixcode'] = $data;
                    $allotmentdata['theory_absent'] = 1;

                    $conditions = ['fixcode' => $allotmentdata['fixcode'], 'exam_year' => $current_exam_year, 'exam_month' => $current_exam_month, 'course' => $request->course_id, 'subject_id' => $request->subject_id, 'examcenter_detail_id' => $request->examcenter_detail_id];

                    $data = StudentAllotmentMark::where($conditions)->update(@$allotmentdata);
                }
            }

            if (@$svData['NR']) {
                foreach ($svData['NR'] as $data) {
                    $allotmentdata = array();
                    @$allotmentdata['fixcode'] = $data;
                    @$allotmentdata['theory_absent'] = 2;
                    $conditions = ['fixcode' => $allotmentdata['fixcode'], 'exam_year' => $current_exam_year, 'exam_month' => $current_exam_month, 'course' => $request->course_id, 'subject_id' => $request->subject_id, 'examcenter_detail_id' => $request->examcenter_detail_id];
                    $data = StudentAllotmentMark::where($conditions)->update($allotmentdata);
                }
            }
            $subject_name = $this->theory_custom_component_obj->_getSubjectDetail(@$request->subject_id);
            $svData['subject_name'] = $subject_name->name;
            $svData['exam_year'] = $current_exam_year;
            $svData['exam_session'] = $current_exam_month;
            $marking_absent_student = MarkingAbsentStudent::create($svData);
            if ($marking_absent_student) {
                return redirect()->route('marking_absents')->with('message', 'Student Mark Absent');
            }
        }
        return view('marking_absents.add', compact('breadcrumbs', 'title', 'exam_year_session', 'exam_month_session', 'course', 'subjects', 'examiner_list'));

    }


    public function edit($id = null, Request $request)
    {
        $title = "Update Marking Absent Student";
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
        $data1 = MarkingAbsentStudent::find($id);
        $current_exam_year = CustomHelper::_get_selected_sessions();
        $current_exam_month = Config::get('global.current_exam_month_id');
        $data = $this->theory_custom_component_obj->get_appearing_student_listing($data1->course_id, $data1->subject_id, $data1->examcenter_detail_id);
        $markingAbsentlist = $this->theory_custom_component_obj->getAbsentAndNrstudent(@$data1->course_id, @$data1->subject_id, @$data1->examcenter_detail_id);
        $subjects = $subjects = $this->custom_component_obj->subjectList($data1->course_id);
        $combo_name = 'admission_sessions';
        $exam_year_session = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_month_session = $this->master_details($combo_name);
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $conditions = array();
        if ($request->isMethod('put')) {
            $svData = $request->all();
			
            $markingAbsentlist = $this->theory_custom_component_obj->getAbsentAndNrstudent(@$data1->course_id, @$data1->subject_id, @$data1->examcenter_detail_id);
            if (!empty($markingAbsentlist)) {
                foreach ($markingAbsentlist as $data) {
                    @$allotmentdata['fixcode'] = $data;
                    @$allotmentdata['theory_absent'] = 0;
                    $conditions = ['fixcode' => $allotmentdata['fixcode'], 'exam_year' => $current_exam_year, 'exam_month' => $current_exam_month, 'course' => @$data1->course_id, 'subject_id' => @$data1->subject_id, 'examcenter_detail_id' => @$data1->examcenter_detail_id];

                    $data = StudentAllotmentMark::where($conditions)->update($allotmentdata);
                }
            }
            if (@$svData['Absent']) {
                foreach ($svData['Absent'] as $data) {
                    @$allotmentdata['fixcode'] = $data;
                    @$allotmentdata['theory_absent'] = 1;
                    $conditions = ['fixcode' => $allotmentdata['fixcode'], 'exam_year' => $current_exam_year, 'exam_month' => $current_exam_month, 'course' => @$data1->course_id, 'subject_id' => @$data1->subject_id, 'examcenter_detail_id' => @$data1->examcenter_detail_id];
                    $data = StudentAllotmentMark::where($conditions)->update($allotmentdata);
                }
            }
            if (@$svData['NR']) {
                foreach ($svData['NR'] as $data) {
                    @$allotmentdata['fixcode'] = $data;
                    @$allotmentdata['theory_absent'] = 2;
                    $conditions = ['fixcode' => $allotmentdata['fixcode'], 'exam_year' => $current_exam_year, 'exam_month' => $current_exam_month, 'course' => @$data1->course_id, 'subject_id' => @$data1->subject_id, 'examcenter_detail_id' => @$data1->examcenter_detail_id];
                    $data = StudentAllotmentMark::where($conditions)->update($allotmentdata);
                }
            }
            $sv2Data = ['total_students_appearing' => @$request->total_students_appearing, 'total_copies_of_subject' => @$request->total_copies_of_subject,
                'total_absent' => @$request->total_absent, 'total_nr' => @$request->total_nr];
            $marking_absent_student = MarkingAbsentStudent::where('id', '=', $id)->update($sv2Data);

            if ($marking_absent_student) {
                return redirect()->route('marking_absents')->with('message', 'Student Mark Absent');
            }

        }
        return view('marking_absents.edit', compact('data', 'breadcrumbs', 'exam_year_session', 'exam_month_session', 'course', 'subjects', 'examiner_list', 'title', 'data1', 'markingAbsentlist'));
    }

    public function get_appearing_student_listing(Request $request)
    {
        echo "get_appearing_student_listing code here";
        die;
        $custom_component_obj = new CustomComponent;
        if (!empty($request->all())) {
            $examcenter_detail_id = $request->exam_center_id;
            $course_id = $request->course_id;
            $subject_id = $request->subjects_id;
            $result = $this->theory_custom_component_obj->get_appearing_student_listing($course_id, $subject_id, $examcenter_detail_id);
        }
        return view('elements.get_appering_list', compact('result'));
    }

    public function view($id = null, Request $request)
    {
        $title = "Student List Marked Absent";
        $combo_name = 'course';
        $course_dropdown = $this->master_details($combo_name);
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
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
        $result_student_list = $this->theory_custom_component_obj->get_appearing_student_listing($data->course_id, $data->subject_id, $data->examcenter_detail_id);


        return view('marking_absents.view', compact('data', 'title', 'breadcrumbs', 'examiner_list', 'result_student_list', 'course_dropdown'));

    }

    public function delete($id = null, Request $request)
    {
        $id = Crypt::decrypt($id);
        MarkingAbsent::find($id)->delete();
        return back()->with('message', 'Student Absent Marking Deleted Successfully');
    }

    public function markingAbsentListPdf()
    {
        $title = "Marking Absent List";
        $table_id = "Marking Absent List";
        $examiner_list = $this->custom_component_obj->getExamCentersDropdown();
        $current_exam_year = CustomHelper::_get_selected_sessions();
        $current_exam_month = Config::get('global.current_exam_month_id');
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $examYear = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $examSession = $this->master_details($combo_name);
        $formId = ucfirst(str_replace(" ", "_", $title));
        $conditions = array();
        Session::put($formId . '_condtions', $conditions);
        $result = $this->theory_custom_component_obj->getMarkingAbsentStudentList($formId, false);
        // dd($result);
        if (empty($result)) {
            return redirect()->route('/')->with('error', 'Failed! Details not found');
        }

        // return view('marking_absents.marking_absent_list_pdf', compact('formId','result','title','examiner_list','course','examYear','examSession'));


        $pdf = PDF::loadView('marking_absents.marking_absent_list_pdf', compact('formId', 'result', 'current_exam_month', 'title', 'examiner_list', 'course', 'examYear', 'examSession', 'current_exam_year'));
        $pdf->setOption('footer-right', 'Page [page] of [toPage]');
        $path = public_path('marking_absents\Pdf-Thoery-' . $formId . '-' . date('d-m-Y-H-i-s') . '.pdf');
        $pdf->save($path, $pdf, true);
        return (Response::download($path));
    }

    // Need to change below function name

    public function centerMarkingAbsentListPdf()
    {
        echo "Exam center student absent marking pdf code here";
        die;
        $title = "Exam center student absent List";
        $table_id = "Exam center student absent List";
        $formId = ucfirst(str_replace(" ", "-", $title));

        $conditions = array();
        $conditions["users.exam_year"] = CustomHelper::_get_selected_sessions();
        Session::put($formId . '_condtions', $conditions);
        // $result = $this->theory_custom_component_obj->examiner_list($formId,false);
        if (empty($result)) {
            return redirect()->route('/')->with('error', 'Failed! Details not found');
        }
        //return view('mapping_examiners.generatelistpdf', compact('formId','result'));

        $pdf = PDF::loadView('marking_absents.center_marking_absent_list_pdf', compact('formId', 'result'));
        $pdf->setOption('footer-right', 'Page [page] of [toPage]');
        $path = public_path('marking_absents\Pdf-Thoery-' . $formId . '-' . date('d-m-Y-H-i-s') . '.pdf');
        $pdf->save($path, $pdf, true);
        return (Response::download($path));
    }

    public function getAppearingStudent(Request $request)
    {
        if (!empty($request->all())) {
            $data = $this->theory_custom_component_obj->get_appearing_student_listing($request->course_id, $request->subjects_id, $request->exam_center_id);
            return view('marking_absents.get_appearing_student', compact('data'));
        }


    }

}




