<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\PracticalCustomComponent;
use App\Helper\CustomHelper;
use App\Models\Course;
use App\Models\ExamcenterDetail;
use App\Models\StudentAllotmentMark;
use App\Models\StudentPracticalSlotLog;
use App\Models\StudentPracticalSlots;
use App\Models\Subject;
use App\Models\UserExaminerMap;
use App\Models\UserPracticalExaminer;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use PDF;
use Response;
use Session;
use Validator;

class PracticalController extends Controller
{
    public $praticalCustomComponent = null;

    function __construct()
    {
        $this->middleware('permission:create_slot', ['only' => ['create_slot']]);
        $this->middleware('permission:PRATICAL_EXAMINER_SLOT', ['only' => ['createpracticalexaminerslot']]);
        $this->praticalCustomComponent = new PracticalCustomComponent();

    }

    public function index(Request $request)
    {
        $title = "Practicals Details";
        $table_id = "Practical_Details";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $combo_name = 'exam_session';
        $exam_month_session = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $exam_year_session = $this->master_details($combo_name);
        $current_exam_year = Config::get("global.admission_academicyear_id");
        $current_exam_month = Config::get("global.current_exam_month_id");
        // $examcenter_list = ExamcenterDetail::pluck('cent_name','id');
        $subject_list = $this->subjectList();
        $combo_name = 'admission_sessions';
        $exam_year_arr = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_month_arr = $this->master_details($combo_name);
        $combo_name = 'course';
        $allowOld = $this->praticalCustomComponent->allowOldPraticalMarks();
        $viewName = 'practical.index';
        if ($allowOld == 'true') {
            $viewName = 'practical.old_index';
        }
        $course_dropdown = $this->master_details($combo_name);
        $examcenter_datails_dropdown = array();
        $subjects_dropdown = array();

        $specialNoteRemarks = array();
        $specialNoteRemarks[0] = "Note: ऑनलाइन प्रैक्टिकल और Practical मार्क्स भरने में कोई भी प्रॉब्लम आने पर दी गयी मैल आई डी पर मैल करे और अत्यधिक आवश्यकता होने पर फ़ोन करे मैल आइ डी :- rsositcell@gmail.com फ़ोन नम्बर :- 0141-2717081 .";

        $custom_component_obj = new CustomComponent;
        $examcenter_list_dropdown = $examcenter_list = collect($custom_component_obj->getExamCenterWithBothCourseCode());

        $subject_list_dropdown = $subject_list = $this->getSubjectByCoursePracticalTheory(null, 1);

        $role_id = Session::get('role_id');
        $routeDashboard = "";
        if ($role_id == config("global.deo")) {
            $routeDashboard = route("deodashboard");
        } else if ($role_id == config("global.practicalexaminer")) {
            $routeDashboard = route("practicalexaminerdashboard");
        }
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => $routeDashboard
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );


        $exportBtn = array(
            array(
                "label" => "Export Excel",
                'url' => 'downloadExamCenterMappingExl',
                'status' => false,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadExamCenterMappingPdf',
                'status' => false
            )
        );

        $filters = array(
            /*
            array(
                "lbl" => "SSO ID",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => 'SSO ID',
                'dbtbl' => 'users',
                'status' => true
            ),
            */
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course_dropdown,
                'placeholder' => 'Course',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            ),
            array(
                "lbl" => "Exam Center",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examcenter_list_dropdown,
                'placeholder' => 'Exam Center',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list_dropdown,
                'placeholder' => 'Subject',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            ),
            // array(
            // "lbl" => "Stream",
            // 'fld' => 'stream',
            // 'input_type' => 'text',
            // 'options' =>array(1=>'Stream1',2=>'Stream2'),
            // 'placeholder' => 'Stream',
            // 'dbtbl' => 'user_examiner_maps',
            // 'status' => true
            // )
        );

        $conditions = array();


        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }


        /* Practical Docuemnt Path Start */
        $current_folder_year = $this->getCurrentYearFolderName();
        $current_year = @$current_folder_year;


        $stream = Config::get("global.defaultStreamId");
        $yearMonthFolder = $current_year . "\\" . $stream . "\\";
        $combo_name = 'practical_document_path';
        $practical_document_path = $this->master_details($combo_name);
        $practicalDocumentPath = $practical_document_path[1] . $yearMonthFolder;
        /* Practical Docuemnt Path End */


        Session::put($formId . '_conditions', $conditions);
        $practicalCustomComponent = new practicalCustomComponent();
        $master = $practicalCustomComponent->getPracticalMappedCenterList($formId);


        return view($viewName, compact('master', 'filters', 'exportBtn', 'role_id', 'formId', 'practicalDocumentPath', 'table_id', 'title', 'breadcrumbs', 'examcenter_list', 'current_exam_year', 'current_exam_month', 'exam_year_arr', 'exam_month_arr', 'subject_list', 'course_dropdown', 'subjects_dropdown', 'examcenter_datails_dropdown'))->withInput($request->all());
    }

    public function mapped_examiner(Request $request, $user_id)
    {
        return view('practical.mapped_examiner', compact('data'));
    }

    public function marks_entry_delete(Request $request, $user_examiner_map_id = null)
    {
        return view('practical.marks_entry_delete', compact('data'));
    }

    public function create_slot(Request $request, $user_examiner_map_id)
    {
        $model = 'StudentPracticalSlot';
        $title = "Practical Slots";
        $table_id = "student_practical_slots";
        $practicalCustomComponent = new PracticalCustomComponent();
        $CustomComponent = new CustomComponent();
        $isAdminStatus = $CustomComponent->_checkIsAdminRole();
        if ($isAdminStatus) {
            $isAllowDate = true;
        } else {
            $isAllowDate = $CustomComponent->_checkPraticalSlotAllowOrNotAllow();
        }


        $formId = ucfirst(str_replace(" ", "_", $title));
        $role_id = Session::get('role_id');
        $routeDashboard = "";
        $combo_name = 'course';
        $course_list = $this->master_details($combo_name);
        $examcenter_list = ExamcenterDetail::pluck('cent_name', 'id');
        $permissions = CustomHelper::roleandpermission();
        $combo_name = 'yesno';
        $yes_no = $this->master_details($combo_name);
        $subject_list = Subject::pluck('name', 'id');
        if ($role_id == config("global.deo")) {
            $routeDashboard = route("deodashboard");
        } else if ($role_id == config("global.practicalexaminer")) {
            $routeDashboard = route("practicalexaminerdashboard");
        }
        $allowOrNot = $CustomComponent->_checkPraticalSlotAllowOrNotAllow();


        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => $routeDashboard
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );
        $user_examiner_map_id = decrypt($user_examiner_map_id);
        $skip = $this->skipPraticalSlot($user_examiner_map_id);

        $examinerMapData = UserExaminerMap::where('id', $user_examiner_map_id)->first();
        if (empty($examinerMapData)) {
            return redirect()->back()->with('error', 'Failed! You are not mapped with any center.');
        }
        $pending_student_count = $practicalCustomComponent->getPracticalStudentListPendingCount($examinerMapData->examcenter_detail_id, $examinerMapData->subject_id);
        $message = null;
        if (count($pending_student_count) != 0) {
            $message = count($pending_student_count) . ' ' . "Student Pending  in slot.(स्लॉट में " . count($pending_student_count) . " छात्र लंबित हैं।)";
        }

        $conditions['student_practical_slots.examiner_mapping_id'] = $user_examiner_map_id;
        $tableData = array();

        $actions = array();

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }


        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);

        $master = $practicalCustomComponent->getPraticalSlotData($formId);


        return view('practical.create_slot', compact('title', 'actions', 'message', 'table_id', 'master', 'tableData', 'actions', 'formId', 'breadcrumbs', 'user_examiner_map_id', 'isAllowDate', 'allowOrNot', 'examcenter_list', 'subject_list', 'yes_no', 'course_list'));
    }

    public function skipPraticalSlot($UserExaminerMapid = null)
    {
        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get('global.current_exam_month_id');
        $practicalCustomComponent = new practicalCustomComponent();
        $title = "skipPraticalSlot";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $examinerMapData = UserExaminerMap::where('id', $UserExaminerMapid)->first();

        if (empty($examinerMapData)) {
            return redirect()->route('add_marks')->with('error', 'Failed! You are not mapped with any center.');
        }
        $slotdata = StudentPracticalSlots::where('examiner_mapping_id', $UserExaminerMapid)->where('date_time_end', '<', Carbon::now())->where('entry_done', 0)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->get('id')->toArray();
        $conditions = array();
        $conditions['slotid'] = $slotdata;
        if (!empty($slotdata)) {
            Session::put($formId . '_conditions', $conditions);
            $conditions = Session::get($formId . '_conditions');
            $data = $practicalCustomComponent->getPracticalStudentListAllotingToSlot($examinerMapData->examcenter_detail_id, $examinerMapData->subject_id, false, $formId);
            $slotSvData = ['skip_slot' => 1];
            StudentPracticalSlots::whereIn('id', $slotdata)->update($slotSvData);
            if (!empty($data)) {
                foreach ($data as $datas) {
                    $svdata = ['practical_absent' => '1', 'skip_student' => '1'];
                    $data = StudentAllotmentMark::where('id', $datas->id)->where('exam_year', $datas->exam_year)->where('exam_month', $datas->exam_month)->where('is_practical_lock_submit', '0')->whereNull('final_practical_marks')
                        ->where('theory_absent', '0')->update($svdata);

                }
                return false;
            }
        } else {
            return false;
        }

    }

    public function createpracticalexaminerslot(Request $request, $user_examiner_map_id)
    {
        $CustomComponent = new CustomComponent;
        $isAdminStatus = $CustomComponent->_checkIsAdminRole();
        if ($isAdminStatus) {
            $isAllowDate = true;
        } else {
            $isAllowDate = $CustomComponent->_checkPraticalSlotAllowOrNotAllow();
        }

        if ($isAllowDate) {

        } else {
            return redirect()->back()->with('error', 'Slot Create Date has been closed.');
        }

        $specialNoteRemarks = array();
        $specialNoteRemarks[0] = "Note: Click on “Preview Practical Marks” button to finally “Lock & Submit” the students practical marks. After “Lock & Submit” you can download the PDF.";
        $specialNoteRemarks[1] = "नोट: ऑनलाइन प्रैक्टिकल और Practical मार्क्स भरने में कोई भी प्रॉब्लम आने पर दी गयी मैल आई डी पर मैल करे और अत्यधिक आवश्यकता होने पर फ़ोन करे मैल आइ डी :- rsositcell@gmail.com फ़ोन नम्बर :- 0141-2717081 .";

        $practicalCustomComponent = new practicalCustomComponent();
        $model = 'practicalexamineradd';
        $title = "Practical Slot Create";
        $table_id = "student_practical_slots";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $current_exam_year = CustomHelper::_get_selected_sessions();
        $current_exam_month = Config::get("global.current_exam_month_id");
        $role_id = Session::get('role_id');
        $routeDashboard = "";
        if ($role_id == config("global.deo")) {
            $routeDashboard = route("deodashboard");
        } else if ($role_id == config("global.practicalexaminer")) {
            $routeDashboard = route("practicalexaminerdashboard");
        }
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => $routeDashboard
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );
        $e_user_examiner_map_id = Crypt::decrypt($user_examiner_map_id);
        $auth_user_id = Auth::user()->id;
        $user_examiner_map_id = Crypt::decrypt($user_examiner_map_id);
        $examinerMapData = UserExaminerMap::where('id', $user_examiner_map_id)->first();

        if (empty($examinerMapData)) {
            return redirect()->back()->with('error', 'Failed! You are not mapped with any center.');
        }

        $master = $practicalCustomComponent->getPracticalStudentList($examinerMapData->examcenter_detail_id, $examinerMapData->subject_id, false);

        if ($request->method() == 'PUT' && count($request->all()) > 0) {


            $validtion = $this->createpracticalexaminerslotValidation($request);
            if ($validtion['isValid'] == false) {
                return redirect()->back()->with('error', $validtion['errors'])->withInput($request->all());
            }

            $svData = ['examiner_mapping_id' => $user_examiner_map_id,
                'date_time_start' => $request->date_time_start,
                'date_time_end' => $request->date_time_end,
                'batch_student_count' => $request->batch_student_count,
                'entry_done' => 0,
                'exam_year' => $current_exam_year,
                'exam_month' => $current_exam_month];

            $allotSlotData = StudentPracticalSlots::create($svData);
            $slotid = $allotSlotData->id;
            if ($slotid) {
                foreach ($request->data as $id) {
                    $student_allotment_mark_id = decrypt($id);
                    $svdata = ['student_practical_slot_id' => $slotid];
                    StudentAllotmentMark::where('id', $student_allotment_mark_id)->update($svdata);
                }
            }

            if ($allotSlotData) {
                return redirect()->route('create_slot', Crypt::encrypt($user_examiner_map_id))->with('message', 'Student practical slots details successfully created');

            } else {
                return redirect()->route('createpracticalexaminerslot', Crypt::encrypt($user_examiner_map_id))->with('error', 'Student practical slots details Failed');

            }

        }
        return view('practical.createpracticalexaminerslot', compact('title', 'table_id', 'formId', 'breadcrumbs', 'user_examiner_map_id', 'master', 'model', 'specialNoteRemarks'));
    }

    public function add_marks(Request $request, $user_examiner_map_id)
    {
        $custom_component_obj = new CustomComponent;
        $practicalCustomComponent = new practicalCustomComponent();
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        if ($isAdminStatus) {
            $isAllowDate = true;
        } else {
            $isAllowDate = $practicalCustomComponent->_checkPraticalAllowOrNot();
        }

        if ($isAllowDate) {

        } else {
            return redirect()->back()->with('error', 'Pratical marks submission date not open.');
        }
        $model = 'PracticalMarkSubmission';
        $practicalCustomComponent = new practicalCustomComponent();
        $title = "Practicals Marks Submission";
        $table_id = "student_allotment_marks";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $examcenter_list = ExamcenterDetail::pluck('cent_name', 'id');
        $subject_list = Subject::pluck('name', 'id');
        $combo_name = 'course';
        $course_list = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $exam_year_arr = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_month_arr = $this->master_details($combo_name);

        $role_id = Session::get('role_id');
        $routeDashboard = "";
        if ($role_id == config("global.deo")) {
            $routeDashboard = route("deodashboard");
        } else if ($role_id == config("global.practicalexaminer")) {
            $routeDashboard = route("practicalexaminerdashboard");
        }
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => $routeDashboard
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );

        $specialNoteRemarks = array();
        $specialNoteRemarks[0] = "Note: Click on “Preview Practical Marks” button to finally “Lock & Submit” the students practical marks. After “Lock & Submit” you can download the PDF.";
        $specialNoteRemarks[1] = "नोट: ऑनलाइन प्रैक्टिकल और Practical मार्क्स भरने में कोई भी प्रॉब्लम आने पर दी गयी मैल आई डी पर मैल करे और अत्यधिक आवश्यकता होने पर फ़ोन करे मैल आइ डी :- rsositcell@gmail.com फ़ोन नम्बर :- 0141-2717081 .";
        $defaultPageLimit = config("global.defaultPageLimit");

        $role_id = Session::get('role_id');
        if (!empty($role_id) && !in_array($role_id, array(58, 71, 72, 63))) {
            return redirect()->back()->with('error', 'Failed! You are not authorized for this action.');
        }

        $e_user_examiner_map_id = $user_examiner_map_id;
        $auth_user_id = Auth::user()->id;
        $user_examiner_map_id = Crypt::decrypt($user_examiner_map_id);
        //$skip=$this->skipPraticalSlot($user_examiner_map_id);
        $examinerMapData = UserExaminerMap::where('id', $user_examiner_map_id)->first();

        $slotdata = StudentPracticalSlots::where('examiner_mapping_id', $user_examiner_map_id)->where('date_time_start', '<', Carbon::now())->where('date_time_end', '>', Carbon::now())->where('entry_done', 0)->get('id')->Toarray();

        $slotdata2 = StudentPracticalSlots::where('examiner_mapping_id', $user_examiner_map_id)->where('date_time_end', '<', Carbon::now())->where('entry_done', 0)->get('id')->Toarray();
        $currentSlot = StudentPracticalSlots::wherein('id', $slotdata)->first(['date_time_start', 'date_time_end']);
        $slotdata = array_merge($slotdata, $slotdata2);
        if (empty($slotdata)) {
            return redirect()->back()->with('error', "examiner don't have any slot with this current date.");
        }


        if (empty($examinerMapData)) {
            return redirect()->route('add_marks', $e_user_examiner_map_id)->with('error', 'Failed! You are not mapped with any center.');
        }

        if (@$examinerMapData->is_lock_submit == 0 && empty(@$examinerMapData->document)) {

        } else if (@$examinerMapData->is_lock_submit == 0) {
            return redirect()->route('add_marks', $e_user_examiner_map_id)->with('error', 'Failed! Please do form lock & submit');
        } else if (empty(@$examinerMapData->document)) {
            return redirect()->route('examiner_marks_docupload', $e_user_examiner_map_id)->with('error', 'Failed! Please upload your signed document');
        } else if (@$examinerMapData->is_lock_submit == 1 || !empty(@$examinerMapData->document)) {
            return redirect()->route('practicals', $e_user_examiner_map_id)->with('error', 'Failed! You are already lock & submitted form');
        }

        $subjectMinMaxMarks = $practicalCustomComponent->getPracticalSubjectMaxMarks($examinerMapData->subject_id);
        $subjectMinMarks = $subjectMinMaxMarks['practical_min_marks'];
        $subjectMinMarks = 0;
        $subjectMaxMarks = $subjectMinMaxMarks['practical_max_marks'];
        $conditions = array();
        $conditions['slotid'] = $slotdata;
        //$conditions['is_practical_lock_submit']=0;
        Session::put($formId . '_conditions', $conditions);

        $master = $practicalCustomComponent->getPracticalStudentListAllotingToSlot($examinerMapData->examcenter_detail_id, $examinerMapData->subject_id, true, $formId);


        if ($request->method() == 'PUT' && count($request->all()) > 0) {

            $isValid = true;
            $inputs = $request->all();
            $last_page_id = Crypt::decrypt($inputs['last_page_id']);
            $current_page_id = Crypt::decrypt($inputs['current_page_id']);


            $practicalCustomComponent = new practicalCustomComponent();
            $response = $practicalCustomComponent->isValidPracticalMarks($inputs, $subjectMinMarks, $subjectMaxMarks);
            $isValid = $response['isValid'];
            $customerrors = "Error: " . $response['errors'];
            $validator = $response['validator'];


            $dataUpdateCounter = 0;
            if ($isValid == true && isset($inputs['data'])) {

                foreach ($inputs['data'] as $key => $value) {

                    $deo_data = $practicalCustomComponent->getDistrictIdByUserId($examinerMapData->user_deo_id);
                    $practical_user_data = $practicalCustomComponent->getDistrictIdByUserId($examinerMapData->user_practical_examiner_id);

                    $practical_absent = 0;
                    if (isset($value['practical_absent']) && $value['practical_absent'] = !0) {
                        $practical_absent = '1';
                    }

                    $saveData = array();
                    $saveData['user_examiner_map_id'] = $examinerMapData->id;
                    $saveData['user_deo_id'] = $examinerMapData->user_deo_id;
                    if (!empty(@$deo_data->district_id)) {
                        $saveData['deo_district_id'] = $deo_data->district_id;
                    }
                    $saveData['practical_min_marks'] = $subjectMinMarks;
                    $saveData['practical_max_marks'] = $subjectMaxMarks;
                    $saveData['final_practical_marks'] = $value['final_practical_marks'];
                    $saveData['is_update_practical_marks_practical_examiner'] = 1;
                    $saveData['practical_absent'] = $practical_absent;
                    $saveData['practical_examiner_id'] = $examinerMapData->user_practical_examiner_id;
                    if (!empty(@$practical_user_data->district_id)) {
                        $saveData['practical_examiner_district_id'] = $practical_user_data->district_id;
                    }

                    //$saveData['is_practical_lock_submit'] = 1;
                    $saveData['user_practical_examiner_id'] = $examinerMapData->user_practical_examiner_id;
                    $saveData['updated_at'] = date("Y-m-d H:i:s");

                    $custom_component_obj = new CustomComponent;
                    $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
                    if ($isAdminStatus == true) {
                        $saveData['is_update_practical_marks_admin'] = 1;
                    }
                    foreach ($slotdata as $slotid) {
                        $saveData['student_practical_slot_entry_id'] = @$slotid['id'];
                    }

                    StudentAllotmentMark::where('id', '=', $value['student_allotment_marks_id'])->update($saveData);
                    $dataUpdateCounter++;
                }

                $next_page_id = $current_page_id + 1;
                if ($last_page_id == $current_page_id) {

                    $saveUserExaminerData = array();
                    $saveUserExaminerData['practical_lastpage_submitted_date'] = date("Y-m-d H:i:s");
                    UserExaminerMap::where('id', '=', $examinerMapData->id)->update($saveUserExaminerData);
                    $slotdone = ['entry_done' => '1'];
                    StudentPracticalSlots::whereIn('id', $slotdata)->update($slotdone);
                    //$saveUserPracticalExaminer = array();
                    //$saveUserPracticalExaminer['is_complete_lock_submit'] = 1;
                    //UserPracticalExaminer::where('id','=',$examinerMapData->user_practical_examiner_id)->update($saveUserPracticalExaminer);

                    return redirect()->route('practicals')->with('message', $dataUpdateCounter . ' Student marks has been successfully saved.');
                } else {
                    return redirect()->route('add_marks', $e_user_examiner_map_id . '?page=' . $next_page_id)->with('message', $dataUpdateCounter . ' Student marks has been successfully saved.');
                }

            } else {
                return redirect()->back()->withErrors($customerrors)->withInput($request->all());
            }
        }
        return view('practical.add_marks', compact('subjectMinMarks', 'e_user_examiner_map_id', 'currentSlot', 'subjectMaxMarks', 'title', 'table_id', 'formId', 'examinerMapData', 'specialNoteRemarks', 'breadcrumbs', 'exam_year_arr', 'exam_month_arr', 'master', 'examcenter_list', 'course_list', 'subject_list', 'defaultPageLimit', 'model', 'e_user_examiner_map_id'));
    }

    public function add_marksunlock(Request $request, $user_examiner_map_id)
    {
        ini_set('max_input_vars', '2000');
        $model = 'PracticalMarkSubmission';
        $practicalCustomComponent = new practicalCustomComponent();
        $title = "Practicals Marks Submission";
        $table_id = "student_allotment_marks";

        $formId = ucfirst(str_replace(" ", "_", $title));
        $examcenter_list = ExamcenterDetail::pluck('cent_name', 'id');
        $subject_list = Subject::pluck('name', 'id');
        $combo_name = 'course';
        $course_list = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $exam_year_arr = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_month_arr = $this->master_details($combo_name);

        $role_id = Session::get('role_id');
        $routeDashboard = "";
        if ($role_id == config("global.deo")) {
            $routeDashboard = route("deodashboard");
        } else if ($role_id == config("global.practicalexaminer")) {
            $routeDashboard = route("practicalexaminerdashboard");
        }
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => $routeDashboard
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );

        $specialNoteRemarks = array();
        $specialNoteRemarks[0] = "Note: Click on “Preview Practical Marks” button to finally “Lock & Submit” the students practical marks. After “Lock & Submit” you can download the PDF.";
        $specialNoteRemarks[1] = "नोट: ऑनलाइन प्रैक्टिकल और Practical मार्क्स भरने में कोई भी प्रॉब्लम आने पर दी गयी मैल आई डी पर मैल करे और अत्यधिक आवश्यकता होने पर फ़ोन करे मैल आइ डी :- rsositcell@gmail.com फ़ोन नम्बर :- 0141-2717081 .";
        $defaultPageLimit = config("global.defaultPageLimit");

        $role_id = Session::get('role_id');
        if (!empty($role_id) && !in_array($role_id, array(58, 71, 72, 63))) {
            return redirect()->back()->with('error', 'Failed! You are not authorized for this action.');
        }

        $e_user_examiner_map_id = $user_examiner_map_id;
        $auth_user_id = Auth::user()->id;
        $user_examiner_map_id = Crypt::decrypt($user_examiner_map_id);
        $examinerMapData = UserExaminerMap::where('id', $user_examiner_map_id)->first();

        if (empty($examinerMapData)) {
            return redirect()->route('add_marksunlock', $e_user_examiner_map_id)->with('error', 'Failed! You are not mapped with any center.');
        }

        if (@$examinerMapData->is_lock_submit == 0 && empty(@$examinerMapData->document)) {


        } else if (empty(@$examinerMapData->document)) {
            return redirect()->route('examiner_marks_docupload', $e_user_examiner_map_id)->with('error', 'Failed! Please upload your signed document');
        } else if (@$examinerMapData->is_lock_submit == 1 || !empty(@$examinerMapData->document)) {
            return redirect()->route('practicals', $e_user_examiner_map_id)->with('error', 'Failed! You are already lock & submitted form');
        }
        $formId = "pracital_data";
        $masters = $practicalCustomComponent->getPracticalStudentList($examinerMapData->examcenter_detail_id, $examinerMapData->subject_id, false, $formId);

        $subjectMinMaxMarks = $practicalCustomComponent->getPracticalSubjectMaxMarks($examinerMapData->subject_id);

        $subjectMinMarks = $subjectMinMaxMarks['practical_min_marks'];
        $subjectMinMarks = 0;
        $subjectMaxMarks = $subjectMinMaxMarks['practical_max_marks'];
        $current_exam_year_id = Config::get('global.admission_academicyear_id');
        $current_exam_month_id = Config::get('global.current_exam_month_id');
        $studentpracticalslots = StudentPracticalSlots::where('exam_year', $current_exam_year_id)->where('exam_month', $current_exam_month_id)->where('examiner_mapping_id', $user_examiner_map_id)
            ->where('date_time_start', '<', Carbon::now())->get(['id', 'date_time_start', 'date_time_end']);
        $final_data = array();
        $i = 0;
        $slot = [];
        if (count($studentpracticalslots) == 0) {
            return redirect()->back()->with('error', "Slot not found in current date.");
        }
        $conditions = array();
        foreach ($studentpracticalslots as $key => $value) {
            @$date_time_start = $value->date_time_start;
            @$date_time_end = $value->date_time_end;
            $title2 = "getpraticaldata";
            $conditions['student_allotment_marks.student_practical_slot_id'] = $value->id;
            $formId = ucfirst(str_replace(" ", "_", $title2));
            Session::put($formId . '_conditions', $conditions);
            $master = $practicalCustomComponent->getPracticalStudentList($examinerMapData->examcenter_detail_id, $examinerMapData->subject_id, false, $formId);
            @$finalArr = array();
            @$index = 0;
            foreach ($master as $student) {
                $fld = "id";
                @$finalArr[$index][$fld] = $student->id;
                $fld = "enrollment";
                @$finalArr[$index][$fld] = $student->enrollment;
                $fld = "is_practical_lock_submit";
                @$finalArr[$index][$fld] = $student->is_practical_lock_submit;
                $fld = "is_update_practical_marks_practical_examiner";
                @$finalArr[$index][$fld] = $student->is_update_practical_marks_practical_examiner;

                $fld = "practical_absent";
                @$finalArr[$index][$fld] = $student->practical_absent;
                $fld = "final_practical_marks";
                @$finalArr[$index][$fld] = $student->final_practical_marks;
                $fld = "name";
                @$finalArr[$index][$fld] = $student->name;
                $fld = "getpracticalstudentList";
                @$finalArr[$index][$fld] = $student->id;
                @$index++;
            }

            $final_data[$i]['date_time_start'] = $date_time_start;
            $final_data[$i]['date_time_end'] = $date_time_end;
            $final_data[$i]['getpracticalstudentList'] = $finalArr;
            $i++;

        }

        if (count($request->all()) > 0) {

            $isValid = true;
            $inputs = $request->all();
            $practicalCustomComponent = new practicalCustomComponent();
            $response = $practicalCustomComponent->isValidPracticalMarks($inputs, $subjectMinMarks, $subjectMaxMarks);
            $isValid = $response['isValid'];
            $customerrors = "Error: " . $response['errors'];
            $validator = $response['validator'];
            $dataUpdateCounter = 0;
            if ($isValid == true && isset($inputs['data'])) {
                foreach ($inputs['data'] as $key => $value) {
                    $deo_data = $practicalCustomComponent->getDistrictIdByUserId($examinerMapData->user_deo_id);
                    $practical_user_data = $practicalCustomComponent->getDistrictIdByUserId($examinerMapData->user_practical_examiner_id);

                    $practical_absent = 0;
                    if (isset($value['practical_absent']) && $value['practical_absent'] = !0) {
                        $practical_absent = '1';
                    }

                    $saveData = array();
                    $saveData['user_examiner_map_id'] = $examinerMapData->id;
                    $saveData['user_deo_id'] = $examinerMapData->user_deo_id;
                    if (!empty(@$deo_data->district_id)) {
                        $saveData['deo_district_id'] = $deo_data->district_id;
                    }
                    $saveData['practical_min_marks'] = $subjectMinMarks;
                    $saveData['practical_max_marks'] = $subjectMaxMarks;
                    $saveData['final_practical_marks'] = $value['final_practical_marks'];
                    $saveData['is_update_practical_marks_practical_examiner'] = 1;
                    $saveData['practical_absent'] = $practical_absent;
                    $saveData['practical_examiner_id'] = $examinerMapData->user_practical_examiner_id;
                    if (!empty(@$practical_user_data->district_id)) {
                        $saveData['practical_examiner_district_id'] = $practical_user_data->district_id;
                    }

                    //$saveData['is_practical_lock_submit'] = 1;
                    $saveData['user_practical_examiner_id'] = $examinerMapData->user_practical_examiner_id;
                    $saveData['updated_at'] = date("Y-m-d H:i:s");

                    $custom_component_obj = new CustomComponent;
                    $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
                    if ($isAdminStatus == true) {
                        $saveData['is_update_practical_marks_admin'] = 1;
                    }

                    StudentAllotmentMark::where('id', '=', $value['student_allotment_marks_id'])->update($saveData);
                    $dataUpdateCounter++;
                }

                if (isset($studentpracticalslots)) {
                    $svData = ['entry_done' => 1];
                    foreach ($studentpracticalslots as $sps) {
                        StudentPracticalSlots::where('id', $sps->id)->update($svData);
                    }
                }

                $saveUserExaminerData = array();
                $saveUserExaminerData['practical_lastpage_submitted_date'] = date("Y-m-d H:i:s");
                UserExaminerMap::where('id', '=', $examinerMapData->id)->update($saveUserExaminerData);

                //$saveUserPracticalExaminer = array();
                //$saveUserPracticalExaminer['is_complete_lock_submit'] = 1;
                //UserPracticalExaminer::where('id','=',$examinerMapData->user_practical_examiner_id)->update($saveUserPracticalExaminer);

                if (count($masters) == $inputs['data']) {
                    return redirect()->route('examiner_marks_entries_preview', $e_user_examiner_map_id)->with('message', $dataUpdateCounter . ' Student marks has been successfully saved.');
                }
                return redirect()->route('practicals')->with('message', $dataUpdateCounter . ' Student marks has been successfully saved.');
            } else {

                return redirect()->back()->withErrors($customerrors)->withInput($request->all());
            }
        }
        return view('practical.add_marksunlock', compact('subjectMinMarks', 'subjectMaxMarks', 'title', 'table_id', 'formId', 'examinerMapData', 'specialNoteRemarks', 'breadcrumbs', 'exam_year_arr', 'exam_month_arr', 'examcenter_list', 'course_list', 'subject_list', 'defaultPageLimit', 'model', 'e_user_examiner_map_id', 'final_data'));
    }

    public function examiner_marks_entries_preview(Request $request, $user_examiner_map_id = null)
    {
        $practicalCustomComponent = new practicalCustomComponent();
        $model = 'PracticalMarkSubmission';
        $title = "Practicals Marks Preview";
        $table_id = "student_allotment_marks";
        $examcenter_list = ExamcenterDetail::pluck('cent_name', 'id');
        $subject_list = Subject::pluck('name', 'id');
        $combo_name = 'course';
        $course_list = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $exam_year_arr = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_month_arr = $this->master_details($combo_name);

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

        $role_id = Session::get('role_id');
        if (!empty($role_id) && !in_array($role_id, array(58, 71, 72, 63))) {
            return redirect()->back()->with('error', 'Failed! You are not authorized for this action.');
        }

        $allowOld = $this->praticalCustomComponent->allowOldPraticalMarks();
        $route = 'add_marks';
        if ($allowOld == 'true') {
            $route = 'old_add_marks';
        }
        $e_user_examiner_map_id = $user_examiner_map_id;
        $auth_user_id = Auth::user()->id;
        $user_examiner_map_id = Crypt::decrypt($user_examiner_map_id);
        $examinerMapData = UserExaminerMap::where('id', $user_examiner_map_id)->first();
        if (empty($examinerMapData)) {
            return redirect()->route($route, $e_user_examiner_map_id)->with('error', 'Failed! You are not mapped with any center.');
        }

        if (@$examinerMapData->is_lock_submit == 0 && empty(@$examinerMapData->document)) {

        } else if (@$examinerMapData->is_lock_submit == 0) {
            return redirect()->route($route, $e_user_examiner_map_id)->with('error', 'Failed! Please do form lock & submit');
        } else if (empty(@$examinerMapData->document)) {
            return redirect()->route('examiner_marks_docupload', $e_user_examiner_map_id)->with('error', 'Failed! Please upload your signed document');
        } else if (@$examinerMapData->is_lock_submit == 1 || !empty(@$examinerMapData->document)) {
            return redirect()->route('practicals', $e_user_examiner_map_id)->with('error', 'Failed! You are already lock & submitted form');
        }

        $subjectMinMaxMarks = $practicalCustomComponent->getPracticalSubjectMaxMarks($examinerMapData->subject_id);
        $subjectMinMarks = $subjectMinMaxMarks['practical_min_marks'];
        $subjectMaxMarks = $subjectMinMaxMarks['practical_max_marks'];

        $specialNoteRemarks = array();
        $specialNoteRemarks[0] = "Note: Click on “Preview Practical Marks” button to finally “Lock & Submit” the students practical marks. After “Lock & Submit” you can download the PDF.";
        $specialNoteRemarks[1] = "नोट: ऑनलाइन प्रैक्टिकल और Practical मार्क्स भरने में कोई भी प्रॉब्लम आने पर दी गयी मैल आई डी पर मैल करे और अत्यधिक आवश्यकता होने पर फ़ोन करे मैल आइ डी :- rsositcell@gmail.com फ़ोन नम्बर :- 0141-2717081 .";

        $master = $practicalCustomComponent->getPracticalStudentList($examinerMapData->examcenter_detail_id, $examinerMapData->subject_id, false);

        if ($request->method() == 'PUT' && count($request->all()) > 0) {
            $isValid = true;
            $inputs = $request->all();
            /*
            $response = $practicalCustomComponent->isValidPracticalMarks($inputs,$subjectMinMarks,$subjectMaxMarks);
            $isValid = $response['isValid'];
            $customerrors = "Error: ".$response['errors'];
            $validator = $response['validator'];
            */

            $dataUpdateCounter = 1;
            if ($isValid == true && isset($inputs['data'])) {
                foreach ($inputs['data'] as $key => $value) {
                    $saveData = array();
                    $saveData['is_practical_lock_submit'] = 1;
                    $saveData['user_practical_examiner_id'] = $examinerMapData->user_practical_examiner_id;
                    $saveData['updated_at'] = date("Y-m-d H:i:s");
                    StudentAllotmentMark::where('id', '=', crypt::decrypt($value['student_allotment_marks_id']))->update($saveData);
                    $dataUpdateCounter++;
                }


                $saveUserExaminerData = array();
                $saveUserExaminerData['is_lock_submit'] = 1;
                $saveUserExaminerData['lock_submit_created'] = date("Y-m-d H:i:s");
                $saveUserExaminerData['updated_at'] = date("Y-m-d H:i:s");
                $saveUserExaminerData['practical_lock_submit_user_id'] = $auth_user_id;
                UserExaminerMap::where('id', '=', $user_examiner_map_id)->update($saveUserExaminerData);
                $dataUpdateCounter++;

                $saveUserPracticalExaminerData = array();
                $saveUserPracticalExaminerData['is_complete_lock_submit'] = 1;
                UserPracticalExaminer::where('id', '=', $examinerMapData->user_practical_examiner_id)->update($saveUserPracticalExaminerData);

                return redirect()->route('examiner_marks_docupload', $e_user_examiner_map_id)->with('message', 'Lock & Submitted done, Please generate Marks PDF & upload it for confirmation.');
            } else {
                return redirect()->back()->withErrors($customerrors)->withInput($request->all());
            }
        }

        return view('practical.examiner_marks_entries_preview', compact('specialNoteRemarks', 'model', 'title', 'breadcrumbs', 'master', 'e_user_examiner_map_id', 'subjectMinMarks', 'subjectMaxMarks', 'examinerMapData', 'examcenter_list', 'subject_list', 'course_list', 'exam_year_arr', 'exam_month_arr'));
    }

    public function examiner_marks_docupload(Request $request, $user_examiner_map_id = null)
    {
        $practicalCustomComponent = new practicalCustomComponent();
        $model = 'PracticalMarkSubmission';
        $title = "Practicals Marks Preview";
        $table_id = "student_allotment_marks";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $combo_name = 'exam_session';
        $exam_month_session = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $exam_year_session = $this->master_details($combo_name);
        $current_exam_year = Config::get("global.admission_academicyear_id");
        $current_exam_month = Config::get("global.current_exam_month_id");
        $examcenter_list = ExamcenterDetail::pluck('cent_name', 'id');
        $subject_list = Subject::pluck('name', 'id');
        $combo_name = 'admission_sessions';
        $exam_year_arr = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_month_arr = $this->master_details($combo_name);
        $combo_name = 'course';
        $course_list = $this->master_details($combo_name);
        $examcenter_datails_dropdown = array();
        $subjects_dropdown = array();
        $allowOld = $this->praticalCustomComponent->allowOldPraticalMarks();
        $route = 'add_marks';
        if ($allowOld == 'true') {
            $route = 'old_add_marks';
        }
        $role_id = Session::get('role_id');
        $routeDashboard = "";
        if ($role_id == config("global.deo")) {
            $routeDashboard = route("deodashboard");
        } else if ($role_id == config("global.practicalexaminer")) {
            $routeDashboard = route("practicalexaminerdashboard");
        }
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => $routeDashboard
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );


        $exportBtn = array(
            array(
                "label" => "Export Excel",
                'url' => 'downloadExamCenterMappingExl',
                'status' => false,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadExamCenterMappingPdf',
                'status' => false
            )
        );

        $filters = array(
            array(
                "lbl" => "SSO ID",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => 'SSO ID',
                'dbtbl' => 'users',
                'status' => true
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course_list,
                'placeholder' => 'Course',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            ),
            array(
                "lbl" => "Exam Center",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examcenter_list,
                'placeholder' => 'Exam Center',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'placeholder' => 'Subject',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            ),
            array(
                "lbl" => "Stream",
                'fld' => 'stream',
                'input_type' => 'text',
                'options' => array(1 => 'Stream1', 2 => 'Stream2'),
                'placeholder' => 'Stream',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            )
        );

        $role_id = Session::get('role_id');
        if (!empty($role_id) && !in_array($role_id, array(58, 71, 72, 63))) {
            return redirect()->back()->with('error', 'Failed! You are not authorized for this action.');
        }

        $e_user_examiner_map_id = $user_examiner_map_id;
        $auth_user_id = Auth::user()->id;
        $user_examiner_map_id = Crypt::decrypt($user_examiner_map_id);
        $examinerMapData = UserExaminerMap::where('id', $user_examiner_map_id)->first();
        if (empty($examinerMapData)) {
            return redirect()->route($route, $e_user_examiner_map_id)->with('error', 'Failed! You are not mapped with any center.');
        }

        if (@$examinerMapData->is_lock_submit == 0 && empty(@$examinerMapData->document)) {

        } else if (@$examinerMapData->is_lock_submit == 0) {
            return redirect()->route($route, $e_user_examiner_map_id)->with('error', 'Failed! Please do form lock & submit');
        } else if (empty(@$examinerMapData->document)) {
            // return redirect()->route('examiner_marks_docupload',$e_user_examiner_map_id)->with('error', 'Failed! Please upload your signed document');
        } else if (@$examinerMapData->is_lock_submit == 1 || !empty(@$examinerMapData->document)) {
            return redirect()->route('practicals', $e_user_examiner_map_id)->with('error', 'Failed! You are already lock & submitted form');
        }

        $subjectMinMaxMarks = $practicalCustomComponent->getPracticalSubjectMaxMarks($examinerMapData->subject_id);
        $subjectMinMarks = $subjectMinMaxMarks['practical_min_marks'];
        $subjectMaxMarks = $subjectMinMaxMarks['practical_max_marks'];

        $specialNoteRemarks = array();
        $specialNoteRemarks[0] = "Note: Click on “Preview Practical Marks” button to finally “Lock & Submit” the students practical marks. After “Lock & Submit” you can download the PDF.";
        $specialNoteRemarks[1] = "नोट: ऑनलाइन प्रैक्टिकल और Practical मार्क्स भरने में कोई भी प्रॉब्लम आने पर दी गयी मैल आई डी पर मैल करे और अत्यधिक आवश्यकता होने पर फ़ोन करे मैल आइ डी :- rsositcell@gmail.com फ़ोन नम्बर :- 0141-2717081 .";
        $pratical_message = 'Please download practical marks pdf ,sign it, upload the signed pdf and click on "Final Submit" (कृपया प्रैक्टिकल मार्क्स पीडीएफ डाउनलोड करें, उस पर हस्ताक्षर करें, हस्ताक्षरित पीडीएफ अपलोड करें और "फाइनल सबमिट" पर क्लिक करें।)';

        $master = $practicalCustomComponent->getPracticalStudentList($examinerMapData->examcenter_detail_id, $examinerMapData->subject_id, false);


        if ($request->method() == 'PUT' && count($request->all()) > 0) {
            $isValid = true;
            $inputs = $request->all();
            // @dd($inputs);

            $dataUpdateCounter = 1;
            if ($isValid == true && isset($inputs['data'])) {
                if (empty($request->practical_marks_pdf) || !empty($request->practical_marks_pdf)) {
                    $rules = ['practical_marks_pdf' => 'required|mimes:pdf|max:2048'];
                    $validation = $this->validate($request, $rules);

                    /* Practical Docuemnt Path Start */
                    $current_folder_year = $this->getCurrentYearFolderName();
                    $current_year = @$current_folder_year;
                    $stream = Config::get("global.defaultStreamId");
                    $yearMonthFolder = $current_year . "\\" . $stream . "\\";
                    $combo_name = 'practical_document_path';
                    $practical_document_path = $this->master_details($combo_name);
                    $practicalDocumentPath = $practical_document_path[1] . $yearMonthFolder . $user_examiner_map_id;
                    /* Practical Docuemnt Path End */

                    $filename = 'practical_marks_' . $user_examiner_map_id . '.' . $request->practical_marks_pdf->extension();
                    $request->practical_marks_pdf->move(public_path($practicalDocumentPath), $filename);
                } else {
                    $filename = $request->practical_marks_pdf;
                }
                $saveUserExaminerData = array();
                $saveUserExaminerData['document'] = $filename;
                $saveUserExaminerData['is_signed_pdf'] = 1;
                $saveUserExaminerData['updated_at'] = date("Y-m-d H:i:s");
                UserExaminerMap::where('id', '=', $user_examiner_map_id)->update($saveUserExaminerData);

                // return redirect()->back()->with('message','File has been successfully uploaded.');
                return redirect()->route('practicals', $e_user_examiner_map_id)->with('message', 'File has been successfully uploaded and from finally submitted.');
            } else {
                return redirect()->back()->withErrors($customerrors)->withInput($request->all());
            }
        }

        return view('practical.examiner_marks_docupload', compact('specialNoteRemarks', 'model', 'title', 'breadcrumbs', 'master', 'e_user_examiner_map_id', 'subjectMinMarks', 'subjectMaxMarks', 'examinerMapData', 'examcenter_list', 'subject_list', 'course_list', 'exam_year_arr', 'exam_month_arr', 'exportBtn', 'filters'));
    }

    public function practicalMarksSubmissionPdf($user_examiner_map_id = null)
    {
        $current_practical_session = Config::get("global.current_practical_session");
        $title = "Marks Submission";
        $table_id = "Marks Submission";
        $formId = ucfirst(str_replace(" ", "-", $title));
        $examcenter_list = ExamcenterDetail::pluck('cent_name', 'id');
        $subject_list = Subject::pluck('name', 'id');
        $combo_name = 'course';
        $course_list = $this->master_details($combo_name);

        $user_practical_examiner_id = Auth::user()->id;
        $practical_examiner_sso_id = Auth::user()->ssoid;
        $practical_examiner_name = Auth::user()->name;
        $allowOld = $this->praticalCustomComponent->allowOldPraticalMarks();
        $route = 'add_marks';
        if ($allowOld == 'true') {
            $route = 'old_add_marks';
        }
        $e_user_examiner_map_id = $user_examiner_map_id;
        $user_examiner_map_id = Crypt::decrypt($e_user_examiner_map_id);
        $examinerMapData = UserExaminerMap::where('id', $user_examiner_map_id)->first();
        if (empty($examinerMapData)) {
            return redirect()->route($route)->with('error', 'Failed! You are not mapped with any center.');
        }

        $subject_list_code = Subject::pluck('subject_code', 'id');

        $practicalCustomComponent = new practicalCustomComponent();
        $subjectMinMaxMarks = $practicalCustomComponent->getPracticalSubjectMaxMarks($examinerMapData->subject_id);
        $subjectMinMarks = $subjectMinMaxMarks['practical_min_marks'];
        $subjectMaxMarks = $subjectMinMaxMarks['practical_max_marks'];

        $examcenterDetailData = $practicalCustomComponent->getExamcenterDetail($examinerMapData->examcenter_detail_id);

        $conditions = array();
        Session::put($formId . '_condtions', $conditions);
        $master = $practicalCustomComponent->getPracticalStudentList($examinerMapData->examcenter_detail_id, $examinerMapData->subject_id, false);
        // @dd($master);
        if (empty($master)) {
            return redirect()->back()->with('error', 'Failed! Details not found');
        }

        // return view('practical.practical_marks_submission_pdf', compact('formId','master','examinerMapData','examcenter_list','subject_list','course_list','subjectMinMarks','subjectMaxMarks','subject_list_code','practical_examiner_sso_id','practical_examiner_name','examcenterDetailData','current_practical_session'));

        $pdf = PDF::loadView('practical.practical_marks_submission_pdf', compact('formId', 'master', 'examinerMapData', 'examcenter_list', 'subject_list', 'course_list', 'subjectMinMarks', 'subjectMaxMarks', 'subject_list_code', 'practical_examiner_sso_id', 'practical_examiner_name', 'examcenterDetailData', 'e_user_examiner_map_id', 'user_examiner_map_id', 'current_practical_session'));
        $path = public_path('practical\Pdf-Practical-' . $formId . '-' . date('d-m-Y-H-i-s') . '.pdf');
        $pdf->save($path, $pdf, true);
        return (Response::download($path));
    }

    public function deleteSlot($slot_id)
    {
        $slot_id = Crypt::decrypt($slot_id);
        if (@$slot_id) {
            $svdata = ['student_practical_slot_id' => null, 'is_update_practical_marks_practical_examiner' => '0'];
            $data = StudentAllotmentMark::where('student_practical_slot_id', $slot_id)->update($svdata);

        }
        $slotLogs = $this->slotLogs('student_practical_slots', $slot_id);
        StudentPracticalSlots::where('id', '=', $slot_id)->forceDelete();
        return redirect()->back()->with('message', 'Slot Delete successfully.');
    }

    public function slotLogs($table_name = null, $table_primary_id = null)
    {
        $status = false;
        $exam_year = Config::get('global.current_admission_session_id');
        $exam_month = Config::get('global.current_exam_month_id');
        //$macAddress = $this->_my_mac_address();
        $user_role = Session::get('role_id');
        $user_ip_address = Config::get('global.request_client_ip');
        $user_id = Auth::user()->id;
        if (!empty($table_name) && !empty($table_primary_id)) {
            $table_data = DB::table($table_name)->where('id', $table_primary_id)->first();
            if (!empty($table_data)) {
                $svData = [
                    'Table_name' => $table_name,
                    'table_primary_id' => $table_primary_id,
                    'table_data' => json_encode($table_data),
                    'user_id' => $user_id,
                    'user_role' => $user_role,
                    'user_ip_address' => $user_ip_address,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ];
                $data = StudentPracticalSlotLog::create($svData);
                $status = true;

            }
        }
        return $status;
    }

    public function viewSlotData($slot_id)
    {
        $title = "View Practical Slot";
        $slot_id = Crypt::decrypt($slot_id);
        $combo_name = 'yesno';
        $yes_no = $this->master_details($combo_name);
        $selected_session = CustomHelper::_get_selected_sessions();
        $current_exam_month_id = Config::get('global.current_exam_month_id');
        $conditions['student_allotment_marks.exam_year'] = $selected_session;
        $conditions['student_allotment_marks.exam_month'] = $current_exam_month_id;
        $conditions['student_allotments.exam_year'] = $selected_session;
        $conditions['student_allotments.exam_month'] = $current_exam_month_id;
        $slotData = StudentPracticalSlots::where('id', $slot_id)->first();
        $data = StudentAllotmentMark::join('students', 'students.id', '=', 'student_allotment_marks.student_id')
            ->join('student_allotments', 'student_allotments.student_id', '=', 'student_allotment_marks.student_id')
            ->where($conditions)
            ->where('student_practical_slot_id', $slot_id)
            ->whereNull('student_allotments.deleted_at')
            ->whereNull('students.deleted_at')
            ->get(['student_allotment_marks.enrollment', 'student_allotment_marks.is_practical_lock_submit', 'students.name', 'student_allotment_marks.is_update_practical_marks_practical_examiner', 'student_allotment_marks.practical_absent', 'student_allotment_marks.final_practical_marks']);
        return view('practical.view', compact('title', 'slotData', 'data', 'yes_no'));

    }

    public function old_add_marks(Request $request, $user_examiner_map_id)
    {
        $model = 'PracticalMarkSubmission';
        $practicalCustomComponent = new practicalCustomComponent();
        $title = "Practicals Marks Submission";
        $table_id = "student_allotment_marks";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $examcenter_list = ExamcenterDetail::pluck('cent_name', 'id');
        $subject_list = Subject::pluck('name', 'id');
        $combo_name = 'course';
        $course_list = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $exam_year_arr = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_month_arr = $this->master_details($combo_name);

        $role_id = Session::get('role_id');
        $routeDashboard = "";
        if ($role_id == config("global.deo")) {
            $routeDashboard = route("deodashboard");
        } else if ($role_id == config("global.practicalexaminer")) {
            $routeDashboard = route("practicalexaminerdashboard");
        }
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => $routeDashboard
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );

        $specialNoteRemarks = array();
        $specialNoteRemarks[0] = "Note: Click on “Preview Practical Marks” button to finally “Lock & Submit” the students practical marks. After “Lock & Submit” you can download the PDF.";
        $specialNoteRemarks[1] = "नोट: ऑनलाइन प्रैक्टिकल और Practical मार्क्स भरने में कोई भी प्रॉब्लम आने पर दी गयी मैल आई डी पर मैल करे और अत्यधिक आवश्यकता होने पर फ़ोन करे मैल आइ डी :- rsositcell@gmail.com फ़ोन नम्बर :- 0141-2717081 .";
        $defaultPageLimit = config("global.defaultPageLimit");

        $role_id = Session::get('role_id');
        if (!empty($role_id) && !in_array($role_id, array(58, 71, 72, 63))) {
            return redirect()->back()->with('error', 'Failed! You are not authorized for this action.');
        }

        $e_user_examiner_map_id = $user_examiner_map_id;
        $auth_user_id = Auth::user()->id;
        $user_examiner_map_id = Crypt::decrypt($user_examiner_map_id);
        $examinerMapData = UserExaminerMap::where('id', $user_examiner_map_id)->first();
        if (empty($examinerMapData)) {
            return redirect()->route('add_marks', $e_user_examiner_map_id)->with('error', 'Failed! You are not mapped with any center.');
        }

        if (@$examinerMapData->is_lock_submit == 0 && empty(@$examinerMapData->document)) {

        } else if (@$examinerMapData->is_lock_submit == 0) {
            return redirect()->route('add_marks', $e_user_examiner_map_id)->with('error', 'Failed! Please do form lock & submit');
        } else if (empty(@$examinerMapData->document)) {
            return redirect()->route('examiner_marks_docupload', $e_user_examiner_map_id)->with('error', 'Failed! Please upload your signed document');
        } else if (@$examinerMapData->is_lock_submit == 1 || !empty(@$examinerMapData->document)) {
            return redirect()->route('practicals', $e_user_examiner_map_id)->with('error', 'Failed! You are already lock & submitted form');
        }

        $subjectMinMaxMarks = $practicalCustomComponent->getPracticalSubjectMaxMarks($examinerMapData->subject_id);
        $subjectMinMarks = $subjectMinMaxMarks['practical_min_marks'];
        $subjectMinMarks = 0;
        $subjectMaxMarks = $subjectMinMaxMarks['practical_max_marks'];


        $master = $practicalCustomComponent->getPracticalStudentList($examinerMapData->examcenter_detail_id, $examinerMapData->subject_id);
        //@dd($master);


        if ($request->method() == 'PUT' && count($request->all()) > 0) {
            $isValid = true;
            $inputs = $request->all();
            $last_page_id = Crypt::decrypt($inputs['last_page_id']);
            $current_page_id = Crypt::decrypt($inputs['current_page_id']);

            $practicalCustomComponent = new practicalCustomComponent();
            $response = $practicalCustomComponent->isValidPracticalMarks($inputs, $subjectMinMarks, $subjectMaxMarks);
            $isValid = $response['isValid'];
            $customerrors = "Error: " . $response['errors'];
            $validator = $response['validator'];


            $dataUpdateCounter = 0;
            if ($isValid == true && isset($inputs['data'])) {
                foreach ($inputs['data'] as $key => $value) {
                    $deo_data = $practicalCustomComponent->getDistrictIdByUserId($examinerMapData->user_deo_id);
                    $practical_user_data = $practicalCustomComponent->getDistrictIdByUserId($examinerMapData->user_practical_examiner_id);

                    $practical_absent = 0;
                    if (isset($value['practical_absent']) && $value['practical_absent'] = !0) {
                        $practical_absent = '1';
                    }

                    $saveData = array();
                    $saveData['user_examiner_map_id'] = $examinerMapData->id;
                    $saveData['user_deo_id'] = $examinerMapData->user_deo_id;
                    if (!empty(@$deo_data->district_id)) {
                        $saveData['deo_district_id'] = $deo_data->district_id;
                    }
                    $saveData['practical_min_marks'] = $subjectMinMarks;
                    $saveData['practical_max_marks'] = $subjectMaxMarks;
                    $saveData['final_practical_marks'] = $value['final_practical_marks'];
                    $saveData['is_update_practical_marks_practical_examiner'] = 1;
                    $saveData['practical_absent'] = $practical_absent;
                    $saveData['practical_examiner_id'] = $examinerMapData->user_practical_examiner_id;
                    if (!empty(@$practical_user_data->district_id)) {
                        $saveData['practical_examiner_district_id'] = $practical_user_data->district_id;
                    }

                    //$saveData['is_practical_lock_submit'] = 1;
                    $saveData['user_practical_examiner_id'] = $examinerMapData->user_practical_examiner_id;
                    $saveData['updated_at'] = date("Y-m-d H:i:s");

                    $custom_component_obj = new CustomComponent;
                    $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
                    if ($isAdminStatus == true) {
                        $saveData['is_update_practical_marks_admin'] = 1;
                    }

                    StudentAllotmentMark::where('id', '=', crypt::decrypt($value['student_allotment_marks_id']))->update($saveData);
                    $dataUpdateCounter++;
                }

                $next_page_id = $current_page_id + 1;
                if ($last_page_id == $current_page_id) {
                    $saveUserExaminerData = array();
                    $saveUserExaminerData['practical_lastpage_submitted_date'] = date("Y-m-d H:i:s");
                    UserExaminerMap::where('id', '=', $examinerMapData->id)->update($saveUserExaminerData);

                    //$saveUserPracticalExaminer = array();
                    //$saveUserPracticalExaminer['is_complete_lock_submit'] = 1;
                    //UserPracticalExaminer::where('id','=',$examinerMapData->user_practical_examiner_id)->update($saveUserPracticalExaminer);

                    return redirect()->route('examiner_marks_entries_preview', $e_user_examiner_map_id)->with('message', $dataUpdateCounter . ' Student marks has been successfully saved.');
                } else {
                    return redirect()->route('old_add_marks', $e_user_examiner_map_id . '?page=' . $next_page_id)->with('message', $dataUpdateCounter . ' Student marks has been successfully saved.');
                }

            } else {
                return redirect()->back()->withErrors($customerrors)->withInput($request->all());
            }
        }
        return view('practical.old_add_marks', compact('subjectMinMarks', 'subjectMaxMarks', 'title', 'table_id', 'formId', 'examinerMapData', 'specialNoteRemarks', 'breadcrumbs', 'exam_year_arr', 'exam_month_arr', 'master', 'examcenter_list', 'course_list', 'subject_list', 'defaultPageLimit', 'model', 'e_user_examiner_map_id'));
    }
}
	
	
		
		
		
	