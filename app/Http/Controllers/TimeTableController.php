<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Exports\TimetabledetailsExlExport;
use App\Helper\CustomHelper;
use App\Models\Subject;
use App\Models\TimeTable;
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


class TimeTableController extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->middleware('permission:student_dashboard', ['only' => ['dashboard']]);
        $this->middleware('permission:student-list', ['only' => ['index', 'store']]);

    }


    public function tabletableslisting(Request $request)
    {
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'exam_time_table_start_end_time';
        $exam_time_table_start_end_time = $this->master_details($combo_name);
        $allsubjects = Subject::pluck('name', 'id');

        $district = $this->districtsByState();
        $admission_sessions = CustomHelper::_get_admission_sessions();

        $yes_no = $this->master_details('yesno');
        $title = "Time Sheet Details";
        $table_id = "Time_Table_details";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
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
                'url' => 'timetableexportexcel',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadSchoolPdf',
                'status' => false
            ),
        );


        $filters = array(

            array(
                "lbl" => "Exam Date",
                'fld' => 'exam_date',
                'input_type' => 'text',
                'placeholder' => "Exam Date",
                'dbtbl' => 'timetables',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => "Course",
                'dbtbl' => 'timetables',
            ),
            array(
                "lbl" => "Stream",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => "Stream",
                'dbtbl' => 'timetables',
            ),
            array(
                "lbl" => "Subject Type",
                'fld' => 'subjects',
                'input_type' => 'select',
                'options' => $allsubjects,
                'placeholder' => "Subject Type",
                'dbtbl' => 'timetables',
            ),
            array(
                "lbl" => "Exam Time Start",
                'fld' => 'exam_time_start',
                'input_type' => 'select',
                'options' => $exam_time_table_start_end_time,
                'placeholder' => "Exam Time Start",
                'dbtbl' => 'timetables',
            ),
            array(
                "lbl" => "Exam Time End",
                'fld' => 'exam_time_end',
                'input_type' => 'select',
                'options' => $exam_time_table_start_end_time,
                'placeholder' => "Exam Time End",
                'dbtbl' => 'timetables',
            ),

        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course
            ),
            array(
                "lbl" => "Stream",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id
            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subjects',
                'input_type' => 'select',
                'options' => $allsubjects
            ),
            array(
                "lbl" => "Exam Date",
                'fld' => 'exam_date',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Exam Time Start",
                'fld' => 'exam_time_start',
                'input_type' => 'select',
                'options' => $exam_time_table_start_end_time
            ),
            array(
                "lbl" => "Exam Time Start",
                'fld' => 'exam_time_end',
                'input_type' => 'select',
                'options' => $exam_time_table_start_end_time
            ),

        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();

        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
        } else {

        }

        $actions = array(
            array(
                'fld' => 'edit',
                'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                'fld_url' => '../tabletables/edit/#id#'
            ),
            array(
                'fld' => 'view',
                'class' => 'delete-confirm',
                'icon' => '<i class="material-icons" title="Click here to Delete.">delete</i>',
                'fld_url' => '../tabletables/delete/#id#'
            ),
        );

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        /* Sorting Fields Set Start 1*/
        $sorting = array();
        $orderByRaw = "";
        $inputs = "";
        $sortingField = $this->_getSortingFields($filters);
        /* Sorting Fields Set End 1*/


        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
            /* Sorting Order By Set Start 2*/
            $orderByRaw = $this->_setSortingArrayFields(@$inputs['sorting'], $sortingField);
            /* Sorting Order By Set End 2*/
        }
        //$conditions["exam_year"] = CustomHelper::_get_selected_sessions();

        /* Sorting Fields Set Session Start 3*/
        Session::put($formId . '_orderByRaw', $orderByRaw);
        /* Sorting Fields Set Session End 3*/
        Session::put($formId . '_conditions', $conditions);
        $master = $custom_component_obj->getTimeTablesdatas($formId);
        return view('timetable.listing', compact('inputs', 'sortingField', 'actions', 'tableData', 'master', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs'))->withInput($request->all());
    }


    public function timetableexportexcel(Request $request, $type = "xlsx")
    {
        $examcenter_exl_data = new TimetabledetailsExlExport;
        $filename = 'timetable_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($examcenter_exl_data, $filename);
    }


    public function tabletablescreate(Request $request)
    {
        $model = 'timetable';
        $empty = array();
        $page_title = 'Add Time Table';
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'exam_time_table_start_end_time';
        $exam_time_table_start_end_time = $this->master_details($combo_name);
        $combo_name = 'exam_time_table_start_end_time';
        $exam_time_table_start_end_time1 = $this->master_details($combo_name);


        if (count($request->all()) > 0) {
            $modelObj = new TimeTable;
            $validator = Validator::make($request->all(), $modelObj->rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            $exam_year = CustomHelper::_get_selected_sessions();
            $exam_event = 1;
            $exam_date = Carbon::createFromFormat('d/m/Y', $request->exam_date)->format('Y-m-d');
            $custom_data = array(
                'course' => strip_tags($request->course),
                'stream' => strip_tags($request->stream),
                'subjects' => strip_tags($request->subjects),
                'exam_time_start' => strip_tags($request->exam_time_start),
                'exam_time_end' => strip_tags($request->exam_time_end),
                'exam_date' => $exam_date,
                'exam_year' => strip_tags($exam_year),
                'exam_event' => strip_tags($exam_event),
            );

            $newUser = TimeTable::Create($custom_data);
            if ($newUser) {
                return redirect()->route('tabletableslisting')->with('message', ' successfully created Time Table');
            } else {
                return redirect()->back()->with('error', 'Failed! Time table is not created');
            }
        }
        return view('timetable.create', compact('page_title', 'course', 'exam_time_table_start_end_time', 'exam_time_table_start_end_time1', 'model', 'empty', 'stream_id'));

    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $timetable = TimeTable::where('id', $id)->delete();
        if ($timetable) {
            return redirect()->route('tabletableslisting')->with('message', 'TimeTable successfully Deleted.');
        } else {
            return redirect()->route('tabletableslisting')->with('error', 'Failed! TimeTable not Deleted');
        }
    }


    public function edit(Request $request, $id)
    {
        $timetimeid = Crypt::decrypt($id);
        $timetabledetails = TimeTable::where('id', $timetimeid)->first();
        $allsubjects = Subject::where('course', $timetabledetails->course)->pluck('name', 'id');
        $model = 'timetable';
        $page_title = 'Edit Time Table';
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'exam_time_table_start_end_time';
        $exam_time_table_start_end_time = $this->master_details($combo_name);
        $combo_name = 'exam_time_table_start_end_time';
        $exam_time_table_start_end_time1 = $this->master_details($combo_name);


        if (count($request->all()) > 0) {
            $timetimeid = Crypt::decrypt($id);
            $modelObj = new TimeTable;
            $validator = Validator::make($request->all(), $modelObj->rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            $exam_year = CustomHelper::_get_selected_sessions();
            $exam_event = 1;
            $exam_date = Carbon::createFromFormat('d/m/Y', $request->exam_date)->format('Y-m-d');
            $custom_data = array(
                'course' => strip_tags($request->course),
                'subjects' => strip_tags($request->subjects),
                'stream' => strip_tags($request->stream),
                'exam_time_start' => strip_tags($request->exam_time_start),
                'exam_time_end' => strip_tags($request->exam_time_end),
                'exam_date' => $exam_date,
                'exam_year' => strip_tags($exam_year),
                'exam_event' => strip_tags($exam_event),
            );
            $updatetimetable = TimeTable::where('id', $timetimeid)->update($custom_data);
            if ($updatetimetable) {
                return redirect()->route('tabletableslisting')->with('message', ' successfully updated Time Table');
            } else {
                return redirect()->back()->with('error', 'Failed! Time table is not updated');
            }
        }
        return view('timetable.edit', compact('page_title', 'course', 'exam_time_table_start_end_time', 'exam_time_table_start_end_time1', 'model', 'timetabledetails', 'id', 'allsubjects', 'stream_id'));

    }


}

