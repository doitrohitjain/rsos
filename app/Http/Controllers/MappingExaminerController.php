<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\ThoeryCustomComponent;
use App\Exports\TheoryExaminerExlExport;
use App\Helper\CustomHelper;
use App\Models\ModelHasRole;
use App\Models\User;
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
use Route;
use Session;
use Validator;

class MappingExaminerController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:theory_examiner_add', ['only' => ['index']]);
        $this->middleware('permission:theory_examiner_add', ['only' => ['add']]);
        $this->middleware('permission:Theory_examiner_delete', ['only' => ['delete']]);
    }

    public function index(Request $request)
    {


        $title = "Theory Examiner List";
        $table_id = "Examiner_List";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $combo_name = 'exam_session';
        $current_sesions = $this->master_details($combo_name);
        $custom_component_obj = new CustomComponent;
        $permissions = CustomHelper::roleandpermission();
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => "mapping_examiners"
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );

        $exportBtn = array(
            array(
                "label" => "Export Excel",
                'url' => 'theoryExaminerExcel',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'theoryExaminerListPdf',
                'status' => true
            ),
        );

        $filters = array(
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "Enter SSO",
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Examiner Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Theory Examiner Name",
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Examiner Mobile",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Mobile NO.",
                'dbtbl' => 'users',
            ),

        );


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Name of the Examiner",
                'fld' => 'name',
                'input_type' => 'text',
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Mobile",
                'fld' => 'mobile',
                'input_type' => 'text',
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Designation",
                'fld' => 'designation',
                'input_type' => 'text',
                'dbtbl' => 'users',
            )
            // ,array(
            // 	"lbl" => "Exam Month",
            // 	'fld' => 'exam_month',
            // 	'input_type' => 'select',
            // 	'options' =>$current_sesions
            // ),
        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        // $conditions["users.exam_year"] = CustomHelper::_get_selected_sessions();
        $actions = array();
        if (in_array("Theory_examiner_delete", $permissions)) {
            $actions = array(
                // array(
                // 	'fld' => 'edit',
                // 	'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                // 	'fld_url' => 'mapping_examiners/edit/#id#'
                // ),
                array(
                    'fld' => 'view',
                    'icon' => '<i class="material-icons" title="Click here to Delete.">delete</i>',
                    'fld_url' => 'mapping_examiners/delete/#id#'
                ),
            );
        };


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
                        if (isset($iv['search_type']) && !empty($iv['search_type']) && $iv['search_type'] == "like") {
                            if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            } else {
                                $conditions[$k] = $v;
                            }
                        } else {
                            if (!empty($iv['dbtbl']) && $iv['fld'] == $k) {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            } else {
                                $conditions[$k] = $v;
                            }
                        }
                        break;
                    }
                }
            }
        }
        $theory_Custom_component = new ThoeryCustomComponent;
        Session::put($formId . '_conditions', $conditions);
        $master = $theory_Custom_component->examiner_list($formId, true);
        return view('mapping_examiners.index', compact('tableData', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'master', 'actions'))->withInput($request->all());
    }

    public function add(Request $request)
    {
        $title = "Add Theory Examiner";
        $theory_Custom_component = new ThoeryCustomComponent;
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
        $current_exam_year = Config::get('global.current_admission_session_id');
        $current_exam_month = Config::get('global.current_exam_month_id');
        $exam_year = $theory_Custom_component->getDatatomaster('admission_sessions', $current_exam_year);
        $exam_sessions = $theory_Custom_component->getDatatomaster('exam_session', $current_exam_month);
        if ($request->isMethod('post')) {
            $User = new User;
            $validator = Validator::make($request->all(), $User->mapping_examiner, $User->mapping_examiner_messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            $custom_component_obj = new CustomComponent;
            $svdata = ['ssoid' => $request->ssoid, 'name' => $request->name, 'mobile' => $request->mobile,
                'designation' => $request->designation, 'password' => Hash::make('123456789')];
            $objAjaxController = new AjaxController();
            $response = $objAjaxController->getSSOIDDetialsByTheoryMappingTbl($request);
            if (@$response['status']) {
                $examinerData = $theory_Custom_component->checkExaminerType($response['ssoid']);
                if (@$examinerData) {
                    return redirect()->route('mapping_examiners.add')->with('error', 'User Already Mapped.')->WithInput($request->all());
                } else {
                    $sso_data = $custom_component_obj->getSSOIDDetials($request->ssoid);
                    $theoryExaminer = Config::get('global.theoryexaminer');
                    $user = User::find($response['id'])->update($svdata);
                    $assign = ModelHasRole::create(['role_id' => $theoryExaminer, 'model_type' => "App\Models\User", 'model_id' => $response['id']]);

                    if ($user && $assign) {
                        return redirect()->route('mapping_examiners')->with('message', 'Theory Examiner create succsfully');
                    }
                }
            } else {
                $theoryExaminer = Config::get('global.theoryexaminer');
                $user = User::create($svdata);
                $assign = ModelHasRole::create(['role_id' => $theoryExaminer, 'model_type' => "App\Models\User", 'model_id' => $user->id]);

                if ($user && $assign) {
                    return redirect()->route('mapping_examiners')->with('message', 'Examiner Map Successfully');
                }
            }
        }
        return view("mapping_examiners.add", compact('exam_year', 'exam_sessions', 'title', 'breadcrumbs'));
    }

    public function edit($id = null, Request $request)
    {
        $title = "Edit Theory Examiner";
        $theory_Custom_component = new ThoeryCustomComponent;
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
        $current_exam_year = Config::get('global.current_admission_session_id');
        $current_exam_month = Config::get('global.current_exam_month_id');
        $exam_year = $theory_Custom_component->getDatatomaster('admission_sessions', $current_exam_year);
        $exam_sessions = $theory_Custom_component->getDatatomaster('exam_session', $current_exam_month);
        $id = Crypt::decrypt($id);
        $User = User::find($id);
        if ($request->isMethod('put')) {
            $validator = Validator::make($request->all(), $User->mapping_examiner, $User->mapping_examiner_messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            $userCount = $theory_Custom_component->checkUserEdit($request->ssoid, $User->id, $User->exam_year, $User->exam_month);
            if ($userCount > 0) {
                $errMsg = "SSOID already used by somewhere.";
                return redirect()->back()->with('error', "$errMsg")->withInput($request->all());
            }
            $custom_component_obj = new CustomComponent;
            $sso_data = $custom_component_obj->getSSOIDDetials($request->ssoid);
            $sso_data = json_decode($sso_data);
            $svdata = ['ssoid' => $request->ssoid, 'name' => $sso_data->displayName, 'mobile' => $sso_data->mobile,
                'designation' => $sso_data->designation, 'password' => Hash::make('123456789')];
            $user = User::where('id', $id)->update($svdata);
            if ($user) {
                return redirect()->route('mapping_examiners')->with('message', 'Theory Examiner update Successfully');
            }
        }
        return view('mapping_examiners.edit', compact('exam_year', 'exam_sessions', 'title', 'breadcrumbs', 'User'));

    }

    public function delete($id = null, Request $request)
    {
        $id = Crypt::decrypt($id);
        $conditions = ['model_id' => $id, 'role_id' => Config::get('global.theoryexaminer')];
        $count = DB::table('alloting_copies_examiners')->where('user_id', '=', $id)->where('marks_entry_completed', '=', 0)->whereNull('marks_entry_completed_date')->whereNull('deleted_at')->orderby('id', 'desc')->count();
        if ($count > 0) {
            return back()->with('error', 'Examiner Copies Allready Allot');
        } else {
            DB::table('model_has_roles')->where($conditions)->delete();
            return back()->with('message', 'Examiner Deleted Successfully');
        }
    }

    public function theoryExaminerListPdf(Request $request)
    {
        $title = "Theory Examiner List";
        $table_id = "Examiner List";
        $combo_name = 'exam_session';
        $current_sesions = $this->master_details($combo_name);
        $currentexameyear = Config::get('global.current_admission_session_id');
        $combo_name = 'admission_sessions';
        $exam_year = $this->master_details($combo_name);
        $formId = ucfirst(str_replace(" ", "_", $title));
        $theory_Custom_component = new ThoeryCustomComponent;
        $result = $theory_Custom_component->examiner_list($formId, false);
        if (empty($result)) {
            return redirect()->back()->with('error', 'Failed! Details not found');
        }
        // return view('mapping_examiners.theory_examiner_list_pdf', compact('formId','result','exam_year','title'));

        $pdf = PDF::loadView('mapping_examiners.theory_examiner_list_pdf', compact('formId', 'result', 'title', 'exam_year', 'currentexameyear', 'current_sesions'));

        $pdf->setOption('footer-right', 'Page [page] of [toPage]');
        $path = public_path('mapping_examiners\Pdf-' . $formId . '-' . date('d-m-Y-H-i-s') . '.pdf');
        $pdf->save($path, $pdf, true);
        return (Response::download($path));
    }

    public function theoryExaminerExcel(Request $request, $type = "xlsx")
    {
        $marking_exl_data = new TheoryExaminerExlExport;
        $filename = 'theoryExaminerexcel' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($marking_exl_data, $filename);
    }


}
