<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Exports\CenterStudentallotmentreportExcel;
use App\Exports\ExamcenterExlExport;
use App\Helper\CustomHelper;
use App\Models\AicenterDetail;
use App\Models\ExamcenterDetail;
use App\Models\ModelHasRole;
use App\Models\School;
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

class ExamcenterDetailController extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->middleware('permission:examcenter_listing', ['only' => ['listing', 'all_listing']]);
        $this->middleware('permission:examcenter_add', ['only' => ['add']]);
        $this->middleware('permission:examcenter_downloadExamCenterExl', ['only' => ['downloadExamCenterExl']]);

    }


    public function listing(Request $request)
    {
        return redirect()->route('all_listing');
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
        $activearr = ["1" => "Active", "0" => "Inactive"];
        $district = $this->districtsByState();

        // $centername = ExamcenterDetail::pluck(DB::raw('CONCAT(ecenter10,"-",ecenter12) AS name'),'cent_name');
        $centername = ExamcenterDetail::get()->pluck('full_name', 'id');

        $yes_no = $this->master_details('yesno');
        $title = "ExamCenter";
        $table_id = "ExamCenter_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $stream = config("global.defaultStreamId");

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
                'url' => 'downloadExamCenterExl',
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
                "lbl" => "Center Name",
                'fld' => 'id',
                'input_type' => 'select',
                'options' => $centername,
                'placeholder' => 'Center Name',
                'dbtbl' => 'examcenter_details',
            ),
            /*
            array(
                "lbl" => "Superintendent Name",
                'fld' => 'center_supdt',
                'input_type' => 'text',
                'placeholder' => "Superintendent Name",
                'dbtbl' => 'examcenter_details',
            ),
            */
            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district,
                'placeholder' => 'District',
                'dbtbl' => 'examcenter_details',
            ),

        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "10th",
                'fld' => 'ecenter10',
                'fld_url' => ''
            ),
            array(
                "lbl" => "12th",
                'fld' => 'ecenter12',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Capacity",
                'fld' => 'capacity',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Type",
                'fld' => 'active',
                'input_type' => 'select',
                'options' => $activearr
            ),
            array(
                "lbl" => "Center name",
                'fld' => 'cent_name',
                'fld_url' => ''
            ),
            /*
            array(
                "lbl" => "Superintendent",
                'fld' => 'center_supdt',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Exam incharge",
                'fld' => 'exam_incharge',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Mobile",
                'fld' => 'mobile',
                'fld_url' => ''
            ),

            array(
                "lbl" => "Created Date",
                'fld' => 'created_at',
                'fld_url' => ''
            )
            */

        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        // $conditions["examcenter_details.exam_year"] = CustomHelper::_get_selected_sessions();
        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
        } else {

        }

        $actions = array(
            array(
                'fld' => 'edit',
                'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                'fld_url' => '../examcenter_details/edit/#id#'
            ),
            array(
                'fld' => 'Button',
                'icon' => '<span class="btn cyan waves-effect waves-light border-round  gradient-45deg-deep-orange-orange">AllotCenter</span>',
                // 'fld_url' => '../examcenter_allotment/examcenter_aicenter_mapping_stream' . $stream .'/#id#'
                'fld_url' => '../examcenter_allotment/examcenter_aicenter_mapping_stream' . '/#id#'
            ),
            array(
                'fld' => 'Button',
                'lbl' => 'View Mapping',
                'icon' => '<span class="material-icons" title="Click here to View .">visibility</span>',
                'fld_url' => '../examcenter_allotment/preview_centerallotment_stream' . $stream . '/#id#'
            ),

        );

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }


        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                            $conditions[$iv['dbtbl'] . "." . $k] = $v;
                        } else {
                            $conditions[$k] = $v;
                        }
                        break;
                    }
                }
            }
        }
        Session::put('_condtions', $conditions);
        $master = $custom_component_obj->getExamcenterData($formId);

        return view('examcenter_details.listing', compact('actions', 'tableData', 'master', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs'))->withInput($request->all());
    }


    public function all_listing(Request $request)
    {

        $custom_component_obj = new CustomComponent;
        $allowips = $custom_component_obj->CenterAllotmentAllowIps();
        if ($allowips == false) {
            return redirect()->route('landing')->with('error', 'You are not allow to Center Allotment.');
        }

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
        $activearr = ["1" => "Active", "0" => "Inactive"];
        $district = $this->districtsByState(6);

        // $centername = ExamcenterDetail::pluck(DB::raw('CONCAT(ecenter10,"-",ecenter12) AS name'),'cent_name');
        $centername = ExamcenterDetail::withTrashed()->get()->pluck('full_name', 'id');
        $yes_no = $this->master_details('yesno');
        $title = "ExamCenter";
        $table_id = "ExamCenter_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $stream = config("global.defaultStreamId");


        $aiCenters = $custom_component_obj->getAiCenters();
        $users = $custom_component_obj->getAllUsesSSOIds();
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
                'url' => 'downloadExamCenterExl',
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
                "lbl" => "Center Name",
                'fld' => 'id',
                'input_type' => 'select',
                'options' => $centername,
                'placeholder' => 'Center Name',
                'dbtbl' => 'examcenter_details',
            ),
            /*
            array(
                "lbl" => "Superintendent Name",
                'fld' => 'center_supdt',
                'input_type' => 'text',
                'placeholder' => "Superintendent Name",
                'dbtbl' => 'examcenter_details',
            ),
            */
            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district,
                'placeholder' => 'District',
                'dbtbl' => 'examcenter_details',
            ),
            array(
                "lbl" => "Is Deleted",
                'fld' => 'deleted_at',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is Deleted',
                'dbtbl' => 'examcenter_details',
            ), array(
                "lbl" => "SSO ID",
                'fld' => 'user_id',
                'input_type' => 'select',
                'options' => $users,
                'placeholder' => 'SSO ID',
                'dbtbl' => 'examcenter_details',
            ),
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "10th",
                'fld' => 'ecenter10',
                'fld_url' => ''
            ),
            array(
                "lbl" => "12th",
                'fld' => 'ecenter12',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'fld_url' => ''
            ),
            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district
            ),
            array(
                "lbl" => "Capacity",
                'fld' => 'capacity',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Center name",
                'fld' => 'cent_name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "SSOID",
                'fld' => 'user_id',
                'input_type' => 'select',
                'options' => $users
            ),
            /*
            array(
                "lbl" => "Superintendent",
                'fld' => 'center_supdt',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Exam incharge",
                'fld' => 'exam_incharge',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Mobile",
                'fld' => 'mobile',
                'fld_url' => ''
            ),

            array(
                "lbl" => "Created Date",
                'fld' => 'created_at',
                'fld_url' => ''
            )
            */

        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        // $conditions["examcenter_details.exam_year"] = CustomHelper::_get_selected_sessions();
        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
        } else {

        }

        $actions = array(
            array(
                'fld' => 'edit',
                'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                'fld_url' => '../examcenter_details/edit/#id#'
            )
        );

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        Session::put('examcenter_deleted_at', null);
        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($k != 'deleted_at') {
                            if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            } else {
                                $conditions[$k] = $v;
                            }
                        } else {
                            if ($k == 'deleted_at') {
                                if ($inputs[$k] == 1) {
                                    Session::put('examcenter_deleted_at', 'not_null');
                                } else if ($inputs[$k] == 0) {
                                    Session::put('examcenter_deleted_at', 'null');
                                }
                            }
                        }
                        break;
                    }
                }
            }
        }
        Session::put('_conditions', $conditions);
        $master = $custom_component_obj->getAllExamcenterData($formId);

        return view('examcenter_details.all_listing', compact('tableData', 'stream', 'master', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs'))->withInput($request->all());
    }

    public function all_examcenterlisting(Request $request)
    {

        $custom_component_obj = new CustomComponent;
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
        $activearr = ["1" => "Active", "0" => "Inactive"];
        $district = $this->districtsByState(6);

        // $centername = ExamcenterDetail::pluck(DB::raw('CONCAT(ecenter10,"-",ecenter12) AS name'),'cent_name');
        $centername = ExamcenterDetail::withTrashed()->get()->pluck('full_name', 'id');
        $yes_no = $this->master_details('yesno');
        $title = "ExamCenter";
        $table_id = "ExamCenter_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $stream = config("global.defaultStreamId");


        $aiCenters = $custom_component_obj->getAiCenters();
        $users = $custom_component_obj->getAllUsesSSOIds();
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
                'url' => 'downloadExamCenterExl',
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
                "lbl" => "Center Name",
                'fld' => 'id',
                'input_type' => 'select',
                'options' => $centername,
                'placeholder' => 'Center Name',
                'dbtbl' => 'examcenter_details',
            ),
            /*
            array(
                "lbl" => "Superintendent Name",
                'fld' => 'center_supdt',
                'input_type' => 'text',
                'placeholder' => "Superintendent Name",
                'dbtbl' => 'examcenter_details',
            ),
            */
            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district,
                'placeholder' => 'District',
                'dbtbl' => 'examcenter_details',
            ),
            array(
                "lbl" => "Is Deleted",
                'fld' => 'deleted_at',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is Deleted',
                'dbtbl' => 'examcenter_details',
            ), array(
                "lbl" => "SSO ID",
                'fld' => 'user_id',
                'input_type' => 'select',
                'options' => $users,
                'placeholder' => 'SSO ID',
                'dbtbl' => 'examcenter_details',
            ),
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "10th",
                'fld' => 'ecenter10',
                'fld_url' => ''
            ),
            array(
                "lbl" => "12th",
                'fld' => 'ecenter12',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Ai Code",
                'fld' => 'ai_code',
                'fld_url' => ''
            ),
            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district
            ),
            array(
                "lbl" => "Capacity",
                'fld' => 'capacity',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Center name",
                'fld' => 'cent_name',
                'fld_url' => ''
            ),
            array(
                "lbl" => "SSOID",
                'fld' => 'user_id',
                'input_type' => 'select',
                'options' => $users
            ),
            /*
            array(
                "lbl" => "Superintendent",
                'fld' => 'center_supdt',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Exam incharge",
                'fld' => 'exam_incharge',
                'fld_url' => ''
            ),
            array(
                "lbl" => "Mobile",
                'fld' => 'mobile',
                'fld_url' => ''
            ),

            array(
                "lbl" => "Created Date",
                'fld' => 'created_at',
                'fld_url' => ''
            )
            */

        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        // $conditions["examcenter_details.exam_year"] = CustomHelper::_get_selected_sessions();
        if ($isAdminStatus == false) {
            $role_id = @Session::get('role_id');
        } else {

        }

        $actions = array(
            array(
                'fld' => 'edit',
                'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                'fld_url' => '../examcenter_details/editexamcenter/#id#'
            )
        );

        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }

        Session::put('examcenter_deleted_at', null);
        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($k != 'deleted_at') {
                            if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            } else {
                                $conditions[$k] = $v;
                            }
                        } else {
                            if ($k == 'deleted_at') {
                                if ($inputs[$k] == 1) {
                                    Session::put('examcenter_deleted_at', 'not_null');
                                } else if ($inputs[$k] == 0) {
                                    Session::put('examcenter_deleted_at', 'null');
                                }
                            }
                        }
                        break;
                    }
                }
            }
        }
        Session::put('_conditions', $conditions);
        $master = $custom_component_obj->getAllExamcenterData($formId);

        return view('examcenter_details.all_examcenterlisting', compact('tableData', 'stream', 'master', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs'))->withInput($request->all());
    }


    public function add(Request $request, $school_id = null)
    {
        $page_title = 'Add Exam Center Details';
        $model = "ExamcenterDetail";
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $district = $this->districtsByState();
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $users = $custom_component_obj->getAllUsesSSOIds();
        $eschool_id = null;
        $schoolData = array();
        if (@$school_id) {
            $eschool_id = $school_id;
            $school_id = Crypt::decrypt($eschool_id);
            $schoolData = School::where('id', $school_id)->first();
        }
        @$schoolDataschoolid = @$schoolData->id;
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => ''
            ),
            array(
                "label" => $page_title,
                'url' => ''
            )
        );
        $formFields = array(
            array(
                "lbl" => "Exam Center 10th",
                'fld' => 'ecenter10',
                'input_type' => 'text',
                'placeholder' => 'Exam Center 10th',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
            ),
            array(
                "lbl" => "Exam Center 12th",
                'fld' => 'ecenter12',
                'input_type' => 'text',
                'placeholder' => 'Exam Center 12th',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Capacity",
                'fld' => 'capacity',
                'input_type' => 'text',
                'placeholder' => 'Capacity',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'users',
                'is_mandatory' => false
            ),
            array(
                "lbl" => "Center Name",
                'fld' => 'cent_name',
                'input_type' => 'text',
                'placeholder' => 'Center Name',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => @$schoolData->School,
            ),
            array(
                "lbl" => "STD Code",
                'fld' => 'std_code',
                'input_type' => 'text',
                'placeholder' => 'STD Code',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Phone Office",
                'fld' => 'phone_off',
                'input_type' => 'text',
                'placeholder' => 'Phone Office',
                'dbtbl' => 'examcenter_details',
                'default_value' => @$schoolData->MobileNo,
            ),
            array(
                "lbl" => "Phone Residence",
                'fld' => 'phone_res',
                'input_type' => 'text',
                'placeholder' => 'Phone Residence',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),

            array(
                "lbl" => "Exam Center Superintendent",
                'fld' => 'center_supdt',
                'input_type' => 'text',
                'placeholder' => 'Exam Center Superintendent',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Mobile Center Superintendent",
                'fld' => 'mobile_centsupdt',
                'input_type' => 'text',
                'placeholder' => 'Mobile Center Superintendent',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Exam Incharge",
                'fld' => 'exam_incharge',
                'input_type' => 'text',
                'placeholder' => "Exam Incharge",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Mobile Exam Incharge",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Mobile Exam Incharge",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Email",
                'fld' => 'email',
                'input_type' => 'text',
                'placeholder' => "Email",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => @$schoolData->SchoolEmailID
            ),
            array(
                "lbl" => "Center Address 1",
                'fld' => 'cent_add1',
                'input_type' => 'text',
                'placeholder' => "Center Address 1",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Center Address 2",
                'fld' => 'cent_add2',
                'input_type' => 'text',
                'placeholder' => "Center Address 2",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district,
                'placeholder' => 'District',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Pincode",
                'fld' => 'pin',
                'input_type' => 'text',
                'placeholder' => "Pincode",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Police Station",
                'fld' => 'police_station',
                'input_type' => 'text',
                'placeholder' => "Police Station",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Police Station Distance",
                'fld' => 'ps_distance',
                'input_type' => 'text',
                'placeholder' => "Police Station Distance",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Bank Account Number",
                'fld' => 'accountno',
                'input_type' => 'text',
                'placeholder' => "Bank Account Number",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Bank Name",
                'fld' => 'bank_name',
                'input_type' => 'text',
                'placeholder' => "Bank Name",
                'dbtbl' => 'Police Station',
                'class' => 'txtOnly',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Bank IFSC",
                'fld' => 'bank_ifsc',
                'input_type' => 'text',
                'placeholder' => "Bank IFSC",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Sec Ans Book",
                'fld' => 'sec_ansbook',
                'input_type' => 'text',
                'placeholder' => "Sec Ans Book",
                'dbtbl' => 'Police Station',
            ),
            array(
                "lbl" => "Sr.Sec. Ans Book",
                'fld' => 'srsec_ansbook',
                'input_type' => 'text',
                'placeholder' => "Sr.Sec. Ans Book",
                'dbtbl' => 'Police Station',
            ),
            array(
                "lbl" => " Practical Ans Book",
                'fld' => 'practical_ansbook',
                'input_type' => 'text',
                'placeholder' => "Practical Ans Book",
                'dbtbl' => 'Police Station',
            ),
            array(
                "lbl" => "SSOID",
                'fld' => 'user_id',
                'input_type' => 'select',
                'options' => $users,
                'placeholder' => 'SSOID',
                'dbtbl' => 'users'
            )
        );

        $schoolData = array();
        if (count($request->all()) > 0) {
            $userSsoIdCount = ExamcenterDetail::where('user_id', '=', $request->user_id)->count();
            $ExamcenterDetail = new ExamcenterDetail; /// create model object
            //$validator = Validator::make($request->all(),$ExamcenterDetail->rulesexamcenterdetils);

            /* Check is already mapped with other exam center start */
            $userSsoIdCount = ExamcenterDetail::where('user_id', '=', $request->user_id)->count();
            if ($userSsoIdCount > 0) {
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'SSO already mapped as Exam Center');
            }
            /* Check is already mapped with other exam center end */


            //if ($validator->fails()) {
            //return redirect()->back()->withErrors($validator)->withInput($request->all());
            //}


            $current_admission_session_id = Config::get("global.current_admission_session_id");
            $current_exam_month_id = Config::get("global.current_exam_month_id");
            $examcenterdatas = request()->all();
            $examcenterdatas['active'] = 1;
            $fld = 'sec_ansbook';
            if (empty($examcenterdatas[$fld])) {
                $examcenterdatas[$fld] = 0;
            }

            $fld = 'srsec_ansbook';
            if (empty($examcenterdatas[$fld])) {
                $examcenterdatas[$fld] = 0;
            }

            $fld = 'practical_ansbook';
            if (empty($examcenterdatas[$fld])) {
                $examcenterdatas[$fld] = 0;
            }
            $fld = 'user_id';
            if (empty($examcenterdatas[$fld])) {
                $examcenterdatas[$fld] = 0;
            }


            // $examcenterdatas['exam_year'] = $current_admission_session_id;
            // $examcenterdatas['exam_month'] = $current_exam_month_id;
            $centerexam = ExamcenterDetail::create($examcenterdatas);

            if (@$centerexam->id) {
                $examcenter_ids = $centerexam->id;
                /* Udpate for ssoid and role start */
                $aexamcenterarray = ['user_id' => $request->user_id];
                $assignRole = ['role_id' => 60, 'model_type' => 'App\Models\User', 'model_id' => $request->user_id];
                $model_has_roles = DB::table('model_has_roles')->where('role_id', 60)->where('model_id', @$request->user_id)->count();
                if ($model_has_roles > 0) {
                    $centerexamupdate = ExamcenterDetail::where('id', $examcenter_ids)->update($aexamcenterarray);
                } else {
                    if (!empty($request->user_id)) {
                        $studentfeesupdate = ModelHasRole::create($assignRole);
                        $centerexamupdate = ExamcenterDetail::where('id', $examcenter_ids)->update($aexamcenterarray);
                    } else {
                        $centerexamupdate = ExamcenterDetail::where('id', $examcenter_ids)->update($aexamcenterarray);
                    }
                }
                /* Udpate for ssoid and role end */
            }


            if ($centerexam) {
                return redirect()->route('listing')->with('message', 'Exam center details has been successfully submitted.');
            } else {
                return redirect()->route('listing')->with('error', 'Failed! Personal details has been not submitted');
            }
        }
        return view('examcenter_details.add', compact('formFields', 'page_title', 'eschool_id', 'school_id', 'model', 'breadcrumbs', 'schoolData', 'users', 'schoolDataschoolid'));
    }

    public function create_exam_center($school_id = null, Request $request)
    {
        $page_title = 'Add Exam Center Details';
        $model = "ExamcenterDetail";
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $district = $this->districtsByState();
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $users = $custom_component_obj->getAllUsesSSOIds();
        $eschool_id = null;
        $schoolData = array();
        if (@$school_id) {
            $eschool_id = $school_id;
            $school_id = Crypt::decrypt($eschool_id);
            $schoolData = School::where('id', $school_id)->first();
        }
        @$schoolDataschoolid = @$schoolData->id;
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => ''
            ),
            array(
                "label" => $page_title,
                'url' => ''
            )
        );
        $formFields = array(
            array(
                "lbl" => "Exam Center 10th",
                'fld' => 'ecenter10',
                'input_type' => 'text',
                'placeholder' => 'Exam Center 10th',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
            ),
            array(
                "lbl" => "Exam Center 12th",
                'fld' => 'ecenter12',
                'input_type' => 'text',
                'placeholder' => 'Exam Center 12th',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Capacity",
                'fld' => 'capacity',
                'input_type' => 'text',
                'placeholder' => 'Capacity',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'users',
                'is_mandatory' => false
            ),
            array(
                "lbl" => "Center Name",
                'fld' => 'cent_name',
                'input_type' => 'text',
                'placeholder' => 'Center Name',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => @$schoolData->School,
            ),
            array(
                "lbl" => "STD Code",
                'fld' => 'std_code',
                'input_type' => 'text',
                'placeholder' => 'STD Code',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Phone Office",
                'fld' => 'phone_off',
                'input_type' => 'text',
                'placeholder' => 'Phone Office',
                'dbtbl' => 'examcenter_details',
                'default_value' => @$schoolData->MobileNo,
            ),
            array(
                "lbl" => "Phone Residence",
                'fld' => 'phone_res',
                'input_type' => 'text',
                'placeholder' => 'Phone Residence',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),

            array(
                "lbl" => "Exam Center Superintendent",
                'fld' => 'center_supdt',
                'input_type' => 'text',
                'placeholder' => 'Exam Center Superintendent',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Mobile Center Superintendent",
                'fld' => 'mobile_centsupdt',
                'input_type' => 'text',
                'placeholder' => 'Mobile Center Superintendent',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Exam Incharge",
                'fld' => 'exam_incharge',
                'input_type' => 'text',
                'placeholder' => "Exam Incharge",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Mobile Exam Incharge",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Mobile Exam Incharge",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Email",
                'fld' => 'email',
                'input_type' => 'text',
                'placeholder' => "Email",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => @$schoolData->SchoolEmailID
            ),
            array(
                "lbl" => "Center Address 1",
                'fld' => 'cent_add1',
                'input_type' => 'text',
                'placeholder' => "Center Address 1",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Center Address 2",
                'fld' => 'cent_add2',
                'input_type' => 'text',
                'placeholder' => "Center Address 2",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district,
                'placeholder' => 'District',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Pincode",
                'fld' => 'pin',
                'input_type' => 'text',
                'placeholder' => "Pincode",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Police Station",
                'fld' => 'police_station',
                'input_type' => 'text',
                'placeholder' => "Police Station",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Police Station Distance",
                'fld' => 'ps_distance',
                'input_type' => 'text',
                'placeholder' => "Police Station Distance",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Bank Account Number",
                'fld' => 'accountno',
                'input_type' => 'text',
                'placeholder' => "Bank Account Number",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Bank Name",
                'fld' => 'bank_name',
                'input_type' => 'text',
                'placeholder' => "Bank Name",
                'dbtbl' => 'Police Station',
                'class' => 'txtOnly',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Bank IFSC",
                'fld' => 'bank_ifsc',
                'input_type' => 'text',
                'placeholder' => "Bank IFSC",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Sec Ans Book",
                'fld' => 'sec_ansbook',
                'input_type' => 'text',
                'placeholder' => "Sec Ans Book",
                'dbtbl' => 'Police Station',
            ),
            array(
                "lbl" => "Sr.Sec. Ans Book",
                'fld' => 'srsec_ansbook',
                'input_type' => 'text',
                'placeholder' => "Sr.Sec. Ans Book",
                'dbtbl' => 'Police Station',
            ),
            array(
                "lbl" => " Practical Ans Book",
                'fld' => 'practical_ansbook',
                'input_type' => 'text',
                'placeholder' => "Practical Ans Book",
                'dbtbl' => 'Police Station',
            ),
            array(
                "lbl" => "SSOID",
                'fld' => 'user_id',
                'input_type' => 'select',
                'options' => $users,
                'placeholder' => 'SSOID',
                'dbtbl' => 'users'
            )
        );

        $schoolData = array();
        if (count($request->all()) > 0) {

            $userSsoIdCount = ExamcenterDetail::where('user_id', '=', $request->user_id)->count();
            $ExamcenterDetail = new ExamcenterDetail; /// create model object
            $validator = Validator::make($request->all(), $ExamcenterDetail->rulesexamcenterdetils);

            /* Check is already mapped with other exam center start */
            $userSsoIdCount = ExamcenterDetail::where('user_id', '=', $request->user_id)->count();
            if ($userSsoIdCount > 0) {
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'SSO already mapped as Exam Center');
            }
            /* Check is already mapped with other exam center end */


            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }


            $current_admission_session_id = Config::get("global.current_admission_session_id");
            $current_exam_month_id = Config::get("global.current_exam_month_id");
            $examcenterdatas = request()->all();
            $examcenterdatas['active'] = 1;
            $fld = 'sec_ansbook';
            if (empty($examcenterdatas[$fld])) {
                $examcenterdatas[$fld] = 0;
            }

            $fld = 'srsec_ansbook';
            if (empty($examcenterdatas[$fld])) {
                $examcenterdatas[$fld] = 0;
            }

            $fld = 'practical_ansbook';
            if (empty($examcenterdatas[$fld])) {
                $examcenterdatas[$fld] = 0;
            }
            $fld = 'user_id';
            if (empty($examcenterdatas[$fld])) {
                $examcenterdatas[$fld] = 0;
            }


            // $examcenterdatas['exam_year'] = $current_admission_session_id;
            // $examcenterdatas['exam_month'] = $current_exam_month_id;
            $centerexam = ExamcenterDetail::create($examcenterdatas);

            if (@$centerexam->id) {
                $examcenter_ids = $centerexam->id;
                /* Udpate for ssoid and role start */
                $aexamcenterarray = ['user_id' => $request->user_id];
                $assignRole = ['role_id' => 60, 'model_type' => 'App\Models\User', 'model_id' => $request->user_id];
                $model_has_roles = DB::table('model_has_roles')->where('role_id', 60)->where('model_id', @$request->user_id)->count();
                if ($model_has_roles > 0) {
                    $centerexamupdate = ExamcenterDetail::where('id', $examcenter_ids)->update($aexamcenterarray);
                } else {
                    if (!empty($request->user_id)) {
                        $studentfeesupdate = ModelHasRole::create($assignRole);
                        $centerexamupdate = ExamcenterDetail::where('id', $examcenter_ids)->update($aexamcenterarray);
                    } else {
                        $centerexamupdate = ExamcenterDetail::where('id', $examcenter_ids)->update($aexamcenterarray);
                    }
                }
                /* Udpate for ssoid and role end */
            }

            if ($centerexam) {
                return redirect()->route('listing')->with('message', 'Exam center details has been successfully submitted.');
            } else {
                return redirect()->route('listing')->with('error', 'Failed! Personal details has been not submitted');
            }
        }
        return view('examcenter_details.add', compact('formFields', 'page_title', 'eschool_id', 'school_id', 'model', 'breadcrumbs', 'schoolData', 'users', 'schoolDataschoolid'));
    }


    public function downloadExamCenterExl(Request $request, $type = "xlsx")
    {
        $examcenter_exl_data = new ExamcenterExlExport;
        $filename = 'examcenter_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($examcenter_exl_data, $filename);
    }


    public function edit(Request $request, $examcenter_id = null)
    {
        $page_title = 'Update Exam Center Details';
        $model = "ExamcenterDetail";
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $district = $this->districtsByState();
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $examcenter_ids = Crypt::decrypt($examcenter_id);
        $examcenterdata = ExamcenterDetail::where('id', $examcenter_ids)->first();
        $users = $custom_component_obj->getAllUsesSSOIds();

        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => ''
            ),
            array(
                "label" => $page_title,
                'url' => ''
            )
        );
        $formFields = array(
            // array(
            // 	"lbl" => "Stream",
            // 	'fld' => 'stream',
            // 	'input_type' => 'select',
            // 	'options' => $stream_id,
            // 	'placeholder' => 'Stream',
            // 	'dbtbl' => 'examcenter_details',
            // 	'is_mandatory' => true,
            // 	'default_value' =>$examcenterdata->stream
            // ),
            array(
                "lbl" => "Exam Center 10th",
                'fld' => 'ecenter10',
                'input_type' => 'text',
                'placeholder' => 'Exam Center 10th',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->ecenter10
            ),
            array(
                "lbl" => "Exam Center 12th",
                'fld' => 'ecenter12',
                'input_type' => 'text',
                'placeholder' => 'Exam Center 12th',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->ecenter12
            ),
            array(
                "lbl" => "Capacity",
                'fld' => 'capacity',
                'input_type' => 'text',
                'placeholder' => 'Capacity',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->capacity
            ),
            array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'users',
                'default_value' => $examcenterdata->ai_code
            ),
            array(
                "lbl" => "Center Name",
                'fld' => 'cent_name',
                'input_type' => 'text',
                'placeholder' => 'Center Name',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->cent_name
            ),
            array(
                "lbl" => "STD Code",
                'fld' => 'std_code',
                'input_type' => 'text',
                'placeholder' => 'STD Code',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->std_code
            ),
            array(
                "lbl" => "Phone Office",
                'fld' => 'phone_off',
                'input_type' => 'text',
                'placeholder' => 'Phone Office',
                'dbtbl' => 'examcenter_details',
                'default_value' => $examcenterdata->phone_off
            ),
            array(
                "lbl" => "Phone Residence",
                'fld' => 'phone_res',
                'input_type' => 'text',
                'placeholder' => 'Phone Residence',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->phone_res
            ),
            array(
                "lbl" => "Exam Center Superintendent",
                'fld' => 'center_supdt',
                'input_type' => 'text',
                'placeholder' => 'Exam Center Superintendent',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->center_supdt
            ),
            array(
                "lbl" => "Mobile Center Superintendent",
                'fld' => 'mobile_centsupdt',
                'input_type' => 'text',
                'placeholder' => 'Mobile Center Superintendent',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->mobile_centsupdt
            ),
            array(
                "lbl" => "Exam Incharge",
                'fld' => 'exam_incharge',
                'input_type' => 'text',
                'placeholder' => "Exam Incharge",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->exam_incharge
            ),
            array(
                "lbl" => "Mobile Exam Incharge",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Mobile Exam Incharge",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->mobile
            ),
            array(
                "lbl" => "Email",
                'fld' => 'email',
                'input_type' => 'text',
                'placeholder' => "Email",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->email
            ),
            array(
                "lbl" => "Center Address 1",
                'fld' => 'cent_add1',
                'input_type' => 'text',
                'placeholder' => "Center Address 1",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->cent_add1
            ),
            array(
                "lbl" => "Center Address 2",
                'fld' => 'cent_add2',
                'input_type' => 'text',
                'placeholder' => "Center Address 2",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->cent_add2
            ),
            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district,
                'placeholder' => 'District',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->district_id

            ),
            array(
                "lbl" => "Pincode",
                'fld' => 'pin',
                'input_type' => 'text',
                'placeholder' => "Pincode",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->pin
            ),
            array(
                "lbl" => "Police Station",
                'fld' => 'police_station',
                'input_type' => 'text',
                'placeholder' => "Police Station",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->police_station
            ),
            array(
                "lbl" => "Police Station Distance",
                'fld' => 'ps_distance',
                'input_type' => 'text',
                'placeholder' => "Police Station Distance",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->ps_distance
            ),
            array(
                "lbl" => "Bank Account Number",
                'fld' => 'accountno',
                'input_type' => 'text',
                'placeholder' => "Bank Account Number",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->accountno
            ),
            array(
                "lbl" => "Bank Name",
                'fld' => 'bank_name',
                'input_type' => 'text',
                'placeholder' => "Bank Name",
                'dbtbl' => 'Police Station',
                'class' => 'txtOnly',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->bank_name
            ),
            array(
                "lbl" => "Bank IFSC",
                'fld' => 'bank_ifsc',
                'input_type' => 'text',
                'placeholder' => "Bank IFSC",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->bank_ifsc
            ),
            array(
                "lbl" => "Sec Ans Book",
                'fld' => 'sec_ansbook',
                'input_type' => 'text',
                'placeholder' => "Sec Ans Book",
                'dbtbl' => 'Police Station',
                'default_value' => $examcenterdata->sec_ansbook

            ),
            array(
                "lbl" => "Sr.Sec. Ans Book",
                'fld' => 'srsec_ansbook',
                'input_type' => 'text',
                'placeholder' => "Sr.Sec. Ans Book",
                'dbtbl' => 'Police Station',
                'default_value' => $examcenterdata->srsec_ansbook

            ),
            array(
                "lbl" => " Practical Ans Book",
                'fld' => 'practical_ansbook',
                'input_type' => 'text',
                'placeholder' => "Practical Ans Book",
                'dbtbl' => 'Police Station',
                'default_value' => $examcenterdata->practical_ansbook
            ),
            array(
                "lbl" => "SSOID",
                'fld' => 'user_id',
                'input_type' => 'select',
                'options' => $users,
                'placeholder' => 'SSOID',
                'dbtbl' => 'users',
                'default_value' => $examcenterdata->user_id
            ),
        );

        $schoolData = array();
        if (count($request->all()) > 0) {
            // dd($request->all());
            $examcenter_ids = Crypt::decrypt($examcenter_id);
            $ExamcenterDetail = new ExamcenterDetail; /// create model object
            $validator = Validator::make($request->all(), $ExamcenterDetail->rulesexamcenterdetils);
            $userSsoIdCount = ExamcenterDetail::where('user_id', '=', $request->user_id)
                ->where('id', "!=", $examcenter_ids)->count();
            $errors = null;
            $isValid = true;
            // dd($userSsoIdCount);
            if ($userSsoIdCount > 0) {
                /*
                $fld = 'ssoid';
                $errMsg = 'Entred SSOID already use with other User.';
                $errors[$fld] = $errMsg;
                $validator->getMessageBag()->add($fld, $errMsg);
                $isValid = false;
                */
                // return redirect()->route('listing')->with('error', 'SSO ID already mapped as Exam Center');
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'SSO ID already mapped as Exam Center');
            }
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $aexamcenterarray = ['stream' => $request->stream, 'ecenter10' => $request->ecenter10, 'ecenter12' => $request->ecenter12, 'capacity' => $request->capacity,
                'ai_code' => $request->ai_code, 'cent_name' => $request->cent_name, 'std_code' => $request->std_code,
                'phone_off' => $request->phone_off, 'phone_res' => $request->phone_res, 'center_supdt' => $request->center_supdt,
                'mobile_centsupdt' => $request->mobile_centsupdt, 'exam_incharge' => $request->exam_incharge,
                'mobile' => $request->mobile, 'email' => $request->email, 'cent_add1' => $request->cent_add1, 'cent_add2' => $request->cent_add2,
                'district_id' => $request->district_id, 'pin' => $request->pin, 'police_station' => $request->police_station, 'ps_distance' => $request->ps_distance,
                'accountno' => $request->accountno, 'bank_name' => $request->bank_name, 'bank_ifsc' => $request->bank_ifsc, 'sec_ansbook' => $request->sec_ansbook,
                'srsec_ansbook' => $request->srsec_ansbook, 'practical_ansbook' => $request->practical_ansbook, 'user_id' => $request->user_id];
            if (!empty($request->user_id)) {
                if ($examcenterdata->user_id != $request->user_id && $examcenterdata->user_id != 0) {
                    $model_has_roles = DB::table('model_has_roles')->where('role_id', 60)->where('model_id', $examcenterdata->user_id)->delete();
                }
            }

            $assignRole = ['role_id' => 60, 'model_type' => 'App\Models\User', 'model_id' => $request->user_id];
            $model_has_roles = DB::table('model_has_roles')->where('role_id', 60)->where('model_id', @$request->user_id)->count();
            if ($model_has_roles > 0) {
                // $studentfeesupdate = ModelHasRole::where('model_id',$request->user_id)->where('role_id',60)update($assignRole);
                $centerexamupdate = ExamcenterDetail::where('id', $examcenter_ids)->update($aexamcenterarray);
            } else {
                if (!empty($request->user_id)) {
                    $studentfeesupdate = ModelHasRole::create($assignRole);
                    $centerexamupdate = ExamcenterDetail::where('id', $examcenter_ids)->update($aexamcenterarray);
                } else {
                    $centerexamupdate = ExamcenterDetail::where('id', $examcenter_ids)->update($aexamcenterarray);
                }
            }
            if ($centerexamupdate) {
                return redirect()->route('all_listing')->with('message', 'Exam center details has been successfully updated.');
            } else {
                return redirect()->route('all_listing')->with('error', 'Failed! Personal details has been not submitted');
            }
        }
        return view('examcenter_details.edit', compact('examcenter_id', 'formFields', 'page_title', 'model', 'breadcrumbs', 'schoolData'));
    }

    public function mark_active($id = null)
    {
        $id = Crypt::decrypt($id);
        $updateStatus = DB::table('examcenter_details')->where('id', $id)->update(['deleted_at' => NULL]);
        if ($updateStatus) {
            return redirect()->route('all_listing')->with('message', 'Exam center successfully activated.');
        } else {
            return redirect()->route('all_listing')->with('error', 'Failed! Exam center not found!');
        }
    }

    public function examcenter_aicenter_unmaapeduserid($id = null)
    {
        $id = Crypt::decrypt($id);
        $getrecors = DB::table('examcenter_details')->where('id', $id)->first('user_id');
        $deleterole = DB::table('model_has_roles')->where('role_id', 60)->where('model_id', $getrecors->user_id)->delete();
        $updateStatus = DB::table('examcenter_details')->where('id', $id)->update(['user_id' => NULL]);
        if ($updateStatus) {
            return redirect()->route('all_listing')->with('message', 'SSOID Empty successfully .');
        } else {
            return redirect()->route('all_listing')->with('error', 'Failed! Exam center not found!');
        }
    }

    public function center_student_allotment_report(Request $request)
    {
        ini_set('memory_limit', '10000M');
        ini_set('max_execution_time', '0');
        $page_title = 'Allotment Student Details';
        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = config("global.CenterAllotmentStreamId");

        $exportBtn = array(
            array(
                "label" => "Export Excel",
                'url' => 'center_student_allotment_report_excel',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadSchoolPdf',
                'status' => false
            ),
        );

        $filters = array();

        $aiCenters = AicenterDetail::where('active', 1)->pluck('ai_code', 'ai_code')->toArray();
        $aiCentersName = AicenterDetail::where('active', 1)->pluck('college_name', 'ai_code')->toArray();

        $studentData = DB::select("select course,ai_code,count(id) as count from rs_students where exam_year = $exam_year and exam_month = $exam_month and is_eligible = 1 and deleted_at IS null GROUP BY ai_code,course;");

        $examcenteretail = ExamcenterDetail::orderBy('examcenter_details.id', 'ASC')->get(['id', 'cent_name']);
        $examcenteretail10 = ExamcenterDetail::orderBy('examcenter_details.id', 'ASC')->pluck('ecenter10', 'id');
        $examcenteretail12 = ExamcenterDetail::orderBy('examcenter_details.id', 'ASC')->pluck('ecenter12', 'id');


        $studentDataFinal = array();
        foreach ($studentData as $studentKey => $student) {
            $studentDataFinal[$student->ai_code][$student->course] = $student->count;
        }
        $supplementaryData = DB::select("select course,ai_code,count(id) as count from rs_supplementaries where exam_year = $exam_year and exam_month = $exam_month and is_eligible = 1 and deleted_at IS null  GROUP BY ai_code,course;");

        $supplementaryDataFinal = array();
        foreach ($supplementaryData as $studentKey => $student) {
            $supplementaryDataFinal[$student->ai_code][$student->course] = $student->count;
        }
        $examCenters = array();
        $studentAllotmentData = DB::select("select examcenter_detail_id,course,ai_code,count(id) as count from rs_student_allotments where exam_year = $exam_year and exam_month = $exam_month and deleted_at IS null GROUP BY ai_code,course;");

        $examCenetIds = array();
        $studentAllotmentDataFinal = array();
        foreach ($studentAllotmentData as $studentKey => $student) {
            $studentAllotmentDataFinal[$student->ai_code][$student->course] = $student->count;
        }
        $custom_component_obj = new CustomComponent;

        $finalArr = array();
        foreach ($aiCenters as $id => $ai_code) {
            $examcenters = null;
            $html = null;
            $finalArr[10] = array();
            $finalArr[12] = array();
            if (@$ai_code) {
                $examcentersTemp = $custom_component_obj->_getexamcenterdatils($ai_code);
                if (@$examcentersTemp) {
                    $examcentersTemp = json_decode($examcentersTemp, true);
                    foreach ($examcentersTemp as $data) {
                        if (@$data['examcenter_detail_id']) {
                            if (@$data['course']) {
                                if ($data['course'] == 10 && @$data['examcenter_detail_id']) {
                                    $finalArr[10][] = @$examcenteretail10[@$data['examcenter_detail_id']];
                                }
                                if ($data['course'] == 12 && @$data['examcenter_detail_id']) {
                                    $finalArr[12][] = @$examcenteretail12[@$data['examcenter_detail_id']];
                                }
                            }
                        }
                    }
                }

            }
            if (@$finalArr[10]) {
                $html .= "(10th :" . implode(",", $finalArr[10]) . ")";
            }

            if (@$finalArr[12]) {
                $html .= "(12th :" . implode(",", $finalArr[12]) . ")";
            }


            $examcenters = $html;

            $finalArr[$ai_code] = array();
            $finalArr[$ai_code]['other']['ai_name'] = $aiCentersName[$ai_code];
            $finalArr[$ai_code]['other']['ai_code'] = $ai_code;
            $finalArr[$ai_code]['other']['examcenters'] = @$examcenters;
            $finalArr[$ai_code]['student'][10] = 0;
            $finalArr[$ai_code]['student'][12] = 0;
            $finalArr[$ai_code]['supplementary'][10] = 0;
            $finalArr[$ai_code]['supplementary'][12] = 0;
            $finalArr[$ai_code]['student_allotment'][10] = 0;
            $finalArr[$ai_code]['student_allotment'][12] = 0;
            $finalArr[$ai_code]['reaming_student_allotment'][10] = 0;
            $finalArr[$ai_code]['reaming_student_allotment'][12] = 0;

            if (@$studentDataFinal[$ai_code]) {
                $finalArr[$ai_code]['student'][10] = @$studentDataFinal[$ai_code][10];
                $finalArr[$ai_code]['student'][12] = @$studentDataFinal[$ai_code][12];
            }
            if (@$supplementaryDataFinal[$ai_code]) {
                $finalArr[$ai_code]['supplementary'][10] = @$supplementaryDataFinal[$ai_code][10];
                $finalArr[$ai_code]['supplementary'][12] = @$supplementaryDataFinal[$ai_code][12];
            }

            $finalArr[$ai_code]['total_student'][10] = $finalArr[$ai_code]['student'][10] + $finalArr[$ai_code]['supplementary'][10];
            $finalArr[$ai_code]['total_student'][12] = $finalArr[$ai_code]['student'][12] + $finalArr[$ai_code]['supplementary'][12];
            $finalArr[$ai_code]['total'] = $finalArr[$ai_code]['total_student'][10] + $finalArr[$ai_code]['total_student'][12];

            if (@$studentAllotmentDataFinal[$ai_code]) {
                $finalArr[$ai_code]['student_allotment'][10] = @$studentAllotmentDataFinal[$ai_code][10];
                $finalArr[$ai_code]['student_allotment'][12] = @$studentAllotmentDataFinal[$ai_code][12];
            }
            if (@$studentDataFinal[$ai_code]) {
                $finalArr[$ai_code]['reaming_student_allotment'][10] =
                    (($finalArr[$ai_code]['student'][10] + $finalArr[$ai_code]['supplementary'][10]) - $finalArr[$ai_code]['student_allotment'][10]);
                $finalArr[$ai_code]['reaming_student_allotment'][12] = (($finalArr[$ai_code]['student'][12] + $finalArr[$ai_code]['supplementary'][12]) - $finalArr[$ai_code]['student_allotment'][12]);
            }
        }

        return view('examcenter_details.allotmentstudentdetail', compact('page_title', 'finalArr', 'exportBtn', 'filters'));
    }

    public function center_student_allotment_report_excel(Request $request, $type = "xlsx")
    {
        $examcenter_exl_data = new CenterStudentallotmentreportExcel;
        $filename = 'center_student_allotment_report' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($examcenter_exl_data, $filename);
    }


    public function editexamcenter(Request $request, $examcenter_id = null)
    {
        $page_title = 'Update Exam Center Details';
        $model = "ExamcenterDetail";
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $district = $this->districtsByState();
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $examcenter_ids = Crypt::decrypt($examcenter_id);
        $examcenterdata = ExamcenterDetail::where('id', $examcenter_ids)->first();
        $users = $custom_component_obj->getAllUsesSSOIds();

        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => ''
            ),
            array(
                "label" => $page_title,
                'url' => ''
            )
        );
        $formFields = array(
            // array(
            // 	"lbl" => "Stream",
            // 	'fld' => 'stream',
            // 	'input_type' => 'select',
            // 	'options' => $stream_id,
            // 	'placeholder' => 'Stream',
            // 	'dbtbl' => 'examcenter_details',
            // 	'is_mandatory' => true,
            // 	'default_value' =>$examcenterdata->stream
            // ),
            array(
                "lbl" => "Exam Center 10th",
                'fld' => 'ecenter10',
                'input_type' => 'text',
                'placeholder' => 'Exam Center 10th',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->ecenter10
            ),
            array(
                "lbl" => "Exam Center 12th",
                'fld' => 'ecenter12',
                'input_type' => 'text',
                'placeholder' => 'Exam Center 12th',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->ecenter12
            ),
            array(
                "lbl" => "Capacity",
                'fld' => 'capacity',
                'input_type' => 'text',
                'placeholder' => 'Capacity',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->capacity
            ),
            array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'users',
                'default_value' => $examcenterdata->ai_code
            ),
            array(
                "lbl" => "Center Name",
                'fld' => 'cent_name',
                'input_type' => 'text',
                'placeholder' => 'Center Name',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->cent_name
            ),
            array(
                "lbl" => "STD Code",
                'fld' => 'std_code',
                'input_type' => 'text',
                'placeholder' => 'STD Code',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->std_code
            ),
            array(
                "lbl" => "Phone Office",
                'fld' => 'phone_off',
                'input_type' => 'text',
                'placeholder' => 'Phone Office',
                'dbtbl' => 'examcenter_details',
                'default_value' => $examcenterdata->phone_off
            ),
            array(
                "lbl" => "Phone Residence",
                'fld' => 'phone_res',
                'input_type' => 'text',
                'placeholder' => 'Phone Residence',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->phone_res
            ),
            array(
                "lbl" => "Exam Center Superintendent",
                'fld' => 'center_supdt',
                'input_type' => 'text',
                'placeholder' => 'Exam Center Superintendent',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->center_supdt
            ),
            array(
                "lbl" => "Mobile Center Superintendent",
                'fld' => 'mobile_centsupdt',
                'input_type' => 'text',
                'placeholder' => 'Mobile Center Superintendent',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->mobile_centsupdt
            ),
            array(
                "lbl" => "Exam Incharge",
                'fld' => 'exam_incharge',
                'input_type' => 'text',
                'placeholder' => "Exam Incharge",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->exam_incharge
            ),
            array(
                "lbl" => "Mobile Exam Incharge",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Mobile Exam Incharge",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->mobile
            ),
            array(
                "lbl" => "Email",
                'fld' => 'email',
                'input_type' => 'text',
                'placeholder' => "Email",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->email
            ),
            array(
                "lbl" => "Center Address 1",
                'fld' => 'cent_add1',
                'input_type' => 'text',
                'placeholder' => "Center Address 1",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->cent_add1
            ),
            array(
                "lbl" => "Center Address 2",
                'fld' => 'cent_add2',
                'input_type' => 'text',
                'placeholder' => "Center Address 2",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->cent_add2
            ),
            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district,
                'placeholder' => 'District',
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->district_id

            ),
            array(
                "lbl" => "Pincode",
                'fld' => 'pin',
                'input_type' => 'text',
                'placeholder' => "Pincode",
                'dbtbl' => 'examcenter_details',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->pin
            ),
            array(
                "lbl" => "Police Station",
                'fld' => 'police_station',
                'input_type' => 'text',
                'placeholder' => "Police Station",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->police_station
            ),
            array(
                "lbl" => "Police Station Distance",
                'fld' => 'ps_distance',
                'input_type' => 'text',
                'placeholder' => "Police Station Distance",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->ps_distance
            ),
            array(
                "lbl" => "Bank Account Number",
                'fld' => 'accountno',
                'input_type' => 'text',
                'placeholder' => "Bank Account Number",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->accountno
            ),
            array(
                "lbl" => "Bank Name",
                'fld' => 'bank_name',
                'input_type' => 'text',
                'placeholder' => "Bank Name",
                'dbtbl' => 'Police Station',
                'class' => 'txtOnly',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->bank_name
            ),
            array(
                "lbl" => "Bank IFSC",
                'fld' => 'bank_ifsc',
                'input_type' => 'text',
                'placeholder' => "Bank IFSC",
                'dbtbl' => 'Police Station',
                'is_mandatory' => true,
                'default_value' => $examcenterdata->bank_ifsc
            ),
            array(
                "lbl" => "Sec Ans Book",
                'fld' => 'sec_ansbook',
                'input_type' => 'text',
                'placeholder' => "Sec Ans Book",
                'dbtbl' => 'Police Station',
                'default_value' => $examcenterdata->sec_ansbook

            ),
            array(
                "lbl" => "Sr.Sec. Ans Book",
                'fld' => 'srsec_ansbook',
                'input_type' => 'text',
                'placeholder' => "Sr.Sec. Ans Book",
                'dbtbl' => 'Police Station',
                'default_value' => $examcenterdata->srsec_ansbook

            ),
            array(
                "lbl" => " Practical Ans Book",
                'fld' => 'practical_ansbook',
                'input_type' => 'text',
                'placeholder' => "Practical Ans Book",
                'dbtbl' => 'Police Station',
                'default_value' => $examcenterdata->practical_ansbook
            ),
            array(
                "lbl" => "SSOID",
                'fld' => 'user_id',
                'input_type' => 'select',
                'options' => $users,
                'placeholder' => 'SSOID',
                'dbtbl' => 'users',
                'default_value' => $examcenterdata->user_id
            ),
        );

        $schoolData = array();
        if (count($request->all()) > 0) {
            // dd($request->all());
            $examcenter_ids = Crypt::decrypt($examcenter_id);
            $ExamcenterDetail = new ExamcenterDetail; /// create model object
            $validator = Validator::make($request->all(), $ExamcenterDetail->rulesexamcenterdetils);
            $userSsoIdCount = ExamcenterDetail::where('user_id', '=', $request->user_id)
                ->where('id', "!=", $examcenter_ids)->count();
            $errors = null;
            $isValid = true;
            // dd($userSsoIdCount);
            if ($userSsoIdCount > 0) {
                /*
                $fld = 'ssoid';
                $errMsg = 'Entred SSOID already use with other User.';
                $errors[$fld] = $errMsg;
                $validator->getMessageBag()->add($fld, $errMsg);
                $isValid = false;
                */
                // return redirect()->route('listing')->with('error', 'SSO ID already mapped as Exam Center');
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'SSO ID already mapped as Exam Center');
            }
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $aexamcenterarray = ['stream' => $request->stream, 'ecenter10' => $request->ecenter10, 'ecenter12' => $request->ecenter12, 'capacity' => $request->capacity,
                'ai_code' => $request->ai_code, 'cent_name' => $request->cent_name, 'std_code' => $request->std_code,
                'phone_off' => $request->phone_off, 'phone_res' => $request->phone_res, 'center_supdt' => $request->center_supdt,
                'mobile_centsupdt' => $request->mobile_centsupdt, 'exam_incharge' => $request->exam_incharge,
                'mobile' => $request->mobile, 'email' => $request->email, 'cent_add1' => $request->cent_add1, 'cent_add2' => $request->cent_add2,
                'district_id' => $request->district_id, 'pin' => $request->pin, 'police_station' => $request->police_station, 'ps_distance' => $request->ps_distance,
                'accountno' => $request->accountno, 'bank_name' => $request->bank_name, 'bank_ifsc' => $request->bank_ifsc, 'sec_ansbook' => $request->sec_ansbook,
                'srsec_ansbook' => $request->srsec_ansbook, 'practical_ansbook' => $request->practical_ansbook, 'user_id' => $request->user_id];
            if (!empty($request->user_id)) {
                if ($examcenterdata->user_id != $request->user_id && $examcenterdata->user_id != 0) {
                    $model_has_roles = DB::table('model_has_roles')->where('role_id', 60)->where('model_id', $examcenterdata->user_id)->delete();
                }
            }

            $assignRole = ['role_id' => 60, 'model_type' => 'App\Models\User', 'model_id' => $request->user_id];
            $model_has_roles = DB::table('model_has_roles')->where('role_id', 60)->where('model_id', @$request->user_id)->count();
            if ($model_has_roles > 0) {
                // $studentfeesupdate = ModelHasRole::where('model_id',$request->user_id)->where('role_id',60)update($assignRole);
                $centerexamupdate = ExamcenterDetail::where('id', $examcenter_ids)->update($aexamcenterarray);
            } else {
                if (!empty($request->user_id)) {
                    $studentfeesupdate = ModelHasRole::create($assignRole);
                    $centerexamupdate = ExamcenterDetail::where('id', $examcenter_ids)->update($aexamcenterarray);
                } else {
                    $centerexamupdate = ExamcenterDetail::where('id', $examcenter_ids)->update($aexamcenterarray);
                }
            }
            if ($centerexamupdate) {
                return redirect()->route('all_examcenterlisting')->with('message', 'Exam center details has been successfully updated.');
            } else {
                return redirect()->route('all_examcenterlisting')->with('error', 'Failed! Personal details has been not submitted');
            }
        }
        return view('examcenter_details.editexamcenter', compact('examcenter_id', 'formFields', 'page_title', 'model', 'breadcrumbs', 'schoolData'));
    }


    public function examcenters_aicenter_unmaapeduserid($id = null)
    {
        $id = Crypt::decrypt($id);
        $getrecors = DB::table('examcenter_details')->where('id', $id)->first('user_id');
        $deleterole = DB::table('model_has_roles')->where('role_id', 60)->where('model_id', $getrecors->user_id)->delete();
        $updateStatus = DB::table('examcenter_details')->where('id', $id)->update(['user_id' => NULL]);
        if ($updateStatus) {
            return redirect()->route('all_examcenterlisting')->with('message', 'SSOID Empty successfully .');
        } else {
            return redirect()->route('all_examcenterlisting')->with('error', 'Failed! Exam center not found!');
        }
    }


}

