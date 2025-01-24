<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Exports\ReportMasterQueryExcelExport;
use App\models\ReportMasterQuery;
use App\models\RoleHasPermission;
use App\models\User;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Session;
use Spatie\Permission\Models\Permission;
use Validator;


class ReportMasterQueriesController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:admission_report_student_applications', ['only' => ['student_applications']]);
    }


    public function listing(Request $request)
    {
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'yes_no_2';
        $yes_no_2 = $this->master_details($combo_name);
        $combo_name = 'report_category';
        $reportcategory = $this->master_details($combo_name);
        $subject_list = $this->subjectList();
        $subject_list_name = $this->subjectListName();

        $yes_no = $this->master_details('yesno');
        $yes_no_temp = $this->master_details('yesno');

        $yes_no_temp[''] = "No";
        $title = "Report Master";
        $table_id = "Report_Master";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();


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
                'url' => 'downloadsessionalexportExl',
                'status' => false,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadApplicationPdf',
                'status' => false
            ),
        );
        $roles = $this->_getRoles(1); //for only active 1
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $superTableData1 = array();
        $superTableData2 = array();
        if ($isAdminStatus) {
            $superTableData1 =
                array(
                    "lbl" => "Permissions",
                    'fld' => 'permissions',
                    'input_type' => 'text',
                    'filter_type' => 'like',
                    'placeholder' => "Permissions",
                    'dbtbl' => 'report_master_queries',
                );
            $superTableData2 =
                array(
                    "lbl" => "Cateogry",
                    'fld' => 'report_category_id',
                    'input_type' => 'select',
                    'options' => $reportcategory,
                    'placeholder' => "Cateogry",
                    'dbtbl' => 'report_master_queries',
                );

        }
        $filters = array(
            array(
                "lbl" => "Title",
                'fld' => 'title',
                'input_type' => 'text',
                'placeholder' => "Title",
                'filter_type' => 'like',
                'dbtbl' => 'report_master_queries',
            ),
            array(
                "lbl" => "Is SQL",
                'fld' => 'is_sql',
                'input_type' => 'select',
                'options' => $yes_no,
                'placeholder' => 'Is SQL',
                'dbtbl' => 'report_master_queries'
            ),
            $superTableData1,
            $superTableData2
        );


        $superTableData1 = array();
        $superTableData2 = array();
        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'serial_number',
                'input_type' => 'text',
                'placeholder' => "Sr.No.",
                'dbtbl' => 'report_master_queries',
                'order' => 1
            ),
            array(
                "lbl" => "Title",
                'fld' => 'title',
                'input_type' => 'text',
                'placeholder' => "Title",
                'dbtbl' => 'report_master_queries',
                'order' => 2
            ),
            array(
                "lbl" => "Is Sql",
                'fld' => 'is_sql',
                'input_type' => 'select',
                'options' => $yes_no,
                'order' => 3
            ),
            array(
                "lbl" => "SQL",
                'fld' => 'sql',
                'input_type' => 'text',
                'placeholder' => "SQL",
                'dbtbl' => 'report_master_queries',
                'order' => 4
            ),
            array(
                "lbl" => "URL",
                'fld' => 'url',
                'input_type' => 'text',
                'placeholder' => "url",
                'dbtbl' => 'report_master_queries',
                'order' => 5
            )
        );

        if ($isAdminStatus) {
            $tableData[] =
                array(
                    "lbl" => "Permissions",
                    'fld' => 'permissions',
                    'input_type' => 'text',
                    'placeholder' => "Permissions",
                    'dbtbl' => 'report_master_queries',
                    'order' => 6
                );
            $tableData[] =
                array(
                    "lbl" => "Allowed Roles",
                    'fld' => 'role_id',
                    'input_type' => 'select',
                    'customCheck' => true,
                    'options' => $roles,
                    'order' => 7
                );
            $tableData[] =
                array(
                    "lbl" => "Is Show Link",
                    'fld' => 'is_show_link',
                    'input_type' => 'select',
                    'options' => $yes_no_2,
                    'order' => 8
                );

            $tableData[] =
                array(
                    "lbl" => "Cateogry",
                    'fld' => 'report_category_id',
                    'input_type' => 'select',
                    'options' => $reportcategory,
                    'order' => 8
                );
        }
        $actions = null;

        if ($isAdminStatus) {
            $actions[] =
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                    'fld_url' => 'edit/#id#'
                );
            $actions[] =
                array(
                    'fld' => 'delete',
                    'icon' => '<i class="material-icons" title="Click here to Delete.">delete</i>',
                    'fld_url' => 'destory/#id#'
                );
        }


        $actions[] = array(
            'fld' => 'view',
            'icon' => '<i class="btn btn-default" title="Click here to view">Click here</i>',
            'customCheck' => true,
            'fld_url' => 'export/#id#'
        );


        if (!empty($actions)) {
            $tableData[] = array(
                "lbl" => "Action",
                'fld' => 'action'
            );
        }
        $rawConditions = null;
        $conditions = array();
        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (isset($filters[$ik]['filter_type']) && $filters[$ik]['filter_type'] == 'like' && !empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    if ($rawConditions === null) {
                        $rawConditions = " rs_" . $iv['dbtbl'] . "." . $iv['fld'] . " like '%" . $inputs[$iv['fld']] . "%' ";
                    } else {
                        $rawConditions .= ' and rs_' . $iv['dbtbl'] . "." . $iv['fld'] . " like '%" . $inputs[$iv['fld']] . "%' ";
                    }
                } else if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }

        $tableData = $this->_sortArray($tableData);
        Session::put($formId . '_conditions', $conditions);
        Session::put($formId . '_rawConditions', $rawConditions);

        $rawConditions = Session::get($formId . '_rawConditions');
        $defaultPageLimit = config("global.defaultPageLimit");

        if (!empty($rawConditions)) {
            $master = ReportMasterQuery::
            with("rolehaspermission")
                ->where($conditions)
                ->whereRaw($rawConditions)
                ->orderBy(DB::raw('CAST(serial_number as UNSIGNED)'), 'DESC')
                ->paginate($defaultPageLimit, array('*'));
        } else {
            $master = ReportMasterQuery::
            with("rolehaspermission")
                ->where($conditions)
                ->orderBy(DB::raw('CAST(serial_number as UNSIGNED)'), 'DESC')
                ->paginate($defaultPageLimit, array('*'));
        }


        return view('reportmasterqueries.listing',
            compact('tableData', 'master', 'exportBtn', 'formId', 'table_id', 'filters',
                'title', 'breadcrumbs', 'yes_no', 'actions', 'roles'))->withInput($request->all());
    }

    public function create(Request $request)
    {
        $page_title = 'Queries Add';
        $model = "Queries_Add";
        $roles = $this->_getRoles(1); //for only active 1
        $combo_name = 'yes_no_2';
        $yesno = $this->master_details($combo_name);
        $combo_name = 'select_sql_url';
        $selectsqlurl = $this->master_details($combo_name);
        $combo_name = 'report_category';
        $reportcategory = $this->master_details($combo_name);
        $extraPermissionSymbol = config("global.extraPermissionSymbol");

        $formFields = array(
            array(
                "lbl" => "Is Show Link to Deptartment",
                'fld' => 'is_show_link',
                'input_type' => 'select',
                'options' => $yesno,
                'placeholder' => 'Is Show Link to Deptartment',
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Active/Incactive Status",
                'fld' => 'status',
                'input_type' => 'select',
                'options' => $yesno,
                'placeholder' => 'Active/Incactive Status',
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Title",
                'fld' => 'title',
                'input_type' => 'text',
                'placeholder' => "Title",
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Show Link Text",
                'fld' => 'link_text',
                'input_type' => 'text',
                'placeholder' => "Show Link Text",
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true
            ),
            // array(
            // 	"lbl" => "Serial Number",
            // 	'fld' => 'serial_number',
            // 	'input_type' => 'text',
            // 	'placeholder' => 'Serial Number',
            // 	'dbtbl' => 'report_master_queries',
            // 	'class' => 'num',
            // 	'is_mandatory' => true
            // ),
            array(
                "lbl" => "Permission (" . $extraPermissionSymbol . ")",
                'fld' => 'permissions',
                'input_type' => 'text',
                'placeholder' => "Permission",
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Allowed to see Roles",
                'fld' => 'role',
                'input_type' => 'select',
                'options' => $roles,
                'is_multiple' => true,
                'placeholder' => 'Allowed to see Roles',
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Category",
                'fld' => 'report_category_id',
                'input_type' => 'select',
                'options' => $reportcategory,
                'placeholder' => 'Category',
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Query OR URL",
                'fld' => 'is_sql',
                'input_type' => 'select',
                'options' => $selectsqlurl,
                'placeholder' => 'Query OR URL',
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "SQL Query",
                'fld' => 'sql',
                'input_type' => 'textarea',
                'placeholder' => "SQL Query",
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true,
                'is_dependent' => 1
            ),
            array(
                "lbl" => "URL",
                'fld' => 'url',
                'input_type' => 'text',
                'placeholder' => "URL",
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true,
                'is_dependent' => 2
            ),
        );

        $devloperadminrole = Config::get('global.developer_admin');
        if (count($request->all()) > 0) {
            $is_pdf = false;
            $sql = "";
            $url = "";
            $is_excel = false;
            if ($request->is_sql == 1) {
                $sql = $request->sql;
                $url = "";
                $is_excel = true;
            } else if ($request->is_sql == 2) {
                $sql = "";
                $url = $request->url;
                $is_excel = false;
            }

            $permissions = $extraPermissionSymbol . $request->permissions;


            $qureyarray = [
                'status' => $request->status,
                'is_pdf' => $is_pdf,
                'is_excel' => $is_excel,
                'title' => $request->title,
                'link_text' => $request->link_text,
                'is_show_link' => $request->is_show_link,
                'sql' => $sql,
                'url' => $url,
                'remarks' => null,
                'is_sql' => $request->is_sql,
                'tooltip_text' => $request->link_text,
                'permissions' => $permissions,
                'report_category_id' => $request->report_category_id,
            ];


            $reportMasterQuery = ReportMasterQuery::create($qureyarray);
            $permission = permission::create(['guard_name' => 'web', 'name' => $permissions]);
            ReportMasterQuery::where('id', $reportMasterQuery->id)->update(['permission_id' => $permission->id]);
            $serial_number = $reportMasterQuery->id;
            ReportMasterQuery::where('id', $reportMasterQuery->id)->update(['serial_number' => $serial_number]);

            foreach (@$request->role as $k => $roleId) {
                DB::table('role_has_permissions')->insert(['permission_id' => $permission->id, 'role_id' => $roleId]);
            }
            if ($reportMasterQuery) {
                return redirect()->route('listings')->with('message', 'Master Query successfully created');
            } else {
                return redirect()->route('listings')->with('error', 'Failed! Master Query not created');
            }
        }
        return view('reportmasterqueries.create', compact('formFields', 'devloperadminrole', 'extraPermissionSymbol', 'page_title', 'model'));
    }

    public function edit(Request $request, $id)
    {
        $eid = $id;
        $id = Crypt::decrypt($id);
        $master = ReportMasterQuery::where('id', '=', $id)->first();

        if (@$master) {
        } else {
            return back()->with('error', 'Not Found.');
        }

        $page_title = 'Queries Edit';
        $model = "Queries_Edit";
        $user = User::find($id);
        $roles = $this->_getRoles(1); //for only active 1
        //$userRole = $user->roles->pluck('id','id')->all();
        $userRole = RoleHasPermission::where('permission_id', $master->permission_id)->pluck('role_id', 'role_id');
        $combo_name = 'yes_no_2';
        $yesno = $this->master_details($combo_name);
        $combo_name = 'select_sql_url';
        $selectsqlurl = $this->master_details($combo_name);
        $combo_name = 'report_category';
        $reportcategory = $this->master_details($combo_name);
        $extraPermissionSymbol = config("global.extraPermissionSymbol");


        $formFields = array(
            array(
                "lbl" => "Is Show Link to Deptartment",
                'fld' => 'is_show_link',
                'input_type' => 'select',
                'options' => $yesno,
                'placeholder' => 'Is Show Link to Deptartment',
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Active/Incactive Status",
                'fld' => 'status',
                'input_type' => 'select',
                'options' => $yesno,
                'placeholder' => 'Active/Incactive Status',
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Title",
                'fld' => 'title',
                'input_type' => 'text',
                'placeholder' => "Title",
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Show Link Text",
                'fld' => 'link_text',
                'input_type' => 'text',
                'placeholder' => "Show Link Text",
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true
            ),
            // array(
            // 	"lbl" => "Serial Number",
            // 	'fld' => 'serial_number',
            // 	'input_type' => 'text',
            // 	'placeholder' => 'Serial Number',
            // 	'dbtbl' => 'report_master_queries',
            // 	'class' => 'num',
            // 	'is_mandatory' => true
            // ),
            array(
                "lbl" => "Permission",
                'fld' => 'permissions',
                'input_type' => 'text',
                'placeholder' => "Permission",
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true

            ),
            array(
                "lbl" => "Allowed to see Roles",
                'fld' => 'role',
                'input_type' => 'selectrole'
            ),
            array(
                "lbl" => "Category",
                'fld' => 'report_category_id',
                'input_type' => 'select',
                'options' => $reportcategory,
                'placeholder' => 'Category',
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "Query OR URL",
                'fld' => 'is_sql',
                'input_type' => 'select',
                'options' => $selectsqlurl,
                'placeholder' => 'Query OR URL',
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true
            ),
            array(
                "lbl" => "SQL Query",
                'fld' => 'sql',
                'input_type' => 'textarea',
                'placeholder' => "SQL Query",
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true,
                'is_dependent' => 1
            ),
            array(
                "lbl" => "URL",
                'fld' => 'url',
                'input_type' => 'text',
                'placeholder' => "URL",
                'dbtbl' => 'report_master_queries',
                'is_mandatory' => true,
                'is_dependent' => 2
            ),
        );
        if (count($request->all()) > 0) {
            $input = $request->all();
            $is_pdf = false;
            $sql = "";
            $url = "";
            $is_excel = false;
            if ($request->is_sql == 1) {
                $sql = $request->sql;
                $url = "";
                $is_excel = true;
            } else if ($request->is_sql == 2) {
                $sql = "";
                $url = $request->url;
                $is_excel = false;
            }


            $qureyarray = [
                'status' => $request->status,
                'is_pdf' => $is_pdf,
                'is_excel' => $is_excel,
                'title' => $request->title,
                'link_text' => $request->link_text,
                'is_show_link' => $request->is_show_link,
                'sql' => $sql,
                'url' => $url,
                'remarks' => null,
                'is_sql' => $request->is_sql,
                'tooltip_text' => $request->link_text,
                'report_category_id' => $request->report_category_id,
            ];

            $reportMasterQuery = ReportMasterQuery::where('id', $id)->update($qureyarray);
            //$user = User::find($id);
            //$user->assignRole($request->input('role'));
            $delete_permission = RoleHasPermission::where('permission_id', $master->permission_id)->delete();
            foreach (@$request->role as $k => $roleId) {
                DB::table('role_has_permissions')->insert(['permission_id' => $master->permission_id, 'role_id' => $roleId]);
            }
            if ($reportMasterQuery) {
                return redirect()->route('listings')->with('message', 'Master Query successfully udpated.');
            } else {
                return redirect()->route('listings')->with('error', 'Failed! Master Query not udpated.');
            }
        }
        return view('reportmasterqueries.edit', compact('formFields', 'extraPermissionSymbol', 'page_title', 'model', 'master', 'roles', 'userRole'));
    }

    public function destory($id = null)
    {
        $data = ReportMasterQuery::where('id', Crypt::decrypt($id))->delete();
        return response()->json(['success' => 'Record successfully Deleted']);
    }

    public function export($id = null)
    {

        $masterquerieexcel = ReportMasterQuery::findOrFail(Crypt::decrypt($id));

        if (@$masterquerieexcel->title) {
            $fileName = $masterquerieexcel->title;
        }
        if (@$masterquerieexcel->title) {
            $fileName = $masterquerieexcel->link_text;
        }
        return Excel::download(new ReportMasterQueryExcelExport(Crypt::decrypt($id)), $fileName . '_' . date("d-m-Y") . '.xlsx');
    }

    public function front_view(Request $request)
    {
        $pageTitle = "Reports";
        $condtions = array();
        $master = array();
        $combo_name = 'report_category';
        $report_category = $this->master_details($combo_name);
        $defaultPageLimit = config("global.defaultPageLimit");
        $permissions = $this->roleandpermission();

        $condtions['is_show_link'] = true;
        $condtions['status'] = true;
        $masterTemp = ReportMasterQuery::where('status', '=', 1)
            ->whereIn('permissions', $permissions)
            ->where($condtions)
            ->orderBy(DB::raw('CAST(serial_number as UNSIGNED)'), 'DESC')
            ->get();

        if (@$masterTemp) {
            $masterTemp = $masterTemp->toArray();
            $key = 0;
            foreach ($masterTemp as $value) {
                $master[$value['report_category_id']]['report_category_id'] = @$value['report_category_id'];
                $master[$value['report_category_id']]['status'] = true;
                $master[$value['report_category_id']]['title'] = @$report_category[$value['report_category_id']];
                $master[$value['report_category_id']]['content'][$key] = @$value;
                $key++;
            }
            ksort($master);
        }
        return view('reportmasterqueries.front_view', compact('pageTitle', 'master'));
    }
}
	
