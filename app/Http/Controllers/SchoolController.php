<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Helper\CustomHelper;
use Auth;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use PDF;
use Redirect;
use Response;
use Session;
use Validator;

class SchoolController extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->middleware('permission:student_dashboard', ['only' => ['dashboard']]);
        $this->middleware('permission:student-list', ['only' => ['index', 'store']]);

    }


    public function listing(Request $request)
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

        $yes_no = $this->master_details('yesno');
        $title = "Schools";
        $table_id = "School_Report";
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
                'url' => 'downloadSchoolExl',
                'status' => false,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadSchoolPdf',
                'status' => false
            ),
        );


        $filters = array(
            array(
                "lbl" => "School",
                'fld' => 'School',
                'input_type' => 'text',
                'placeholder' => "School Name",
                'dbtbl' => 'schools',
            )
        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "School",
                'fld' => 'School',
                'fld_url' => ''
            )
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
                'icon' => '<span title="Click here to Map Exam Center.">MapExamCenter</span>',
                'fld_url' => '../examcenter_details/create_exam_center/#id#'
            )
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
        Session::put($formId . '_conditions', $conditions);
        $master = $custom_component_obj->getSchoolData($formId);

        return view('school.listing', compact('actions', 'tableData', 'master', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs'))->withInput($request->all());
    }


    public function add()
    {
        $title = "Schools";
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
        return view('school.add', compact('title', 'breadcrumbs'));
    }

}

