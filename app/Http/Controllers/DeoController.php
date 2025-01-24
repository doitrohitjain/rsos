<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Exports\PracticalExaminerExlExport;
use App\Models\ModelHasRole;
use App\Models\User;
use Auth;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use Validator;


class DeoController extends Controller
{
    function __construct()
    {
    }

    public function index(Request $request)
    {
        $combo_name = 'exam_session';
        $exam_month_session = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $exam_year_session = $this->master_details($combo_name);
        $roles = $this->_getRoles();
        $conditions = array();

        $practical_deo_role = config("global.deo");
        $conditions["model_has_roles.role_id"] = $practical_deo_role;
        $title = "DEO Details";
        $table_id = "Deo_Details";
        $formId = "Users_DEO_details";
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
                'url' => 'downloadDeoExl',
                'status' => true,
            ),
            // array(
            // 	"label" => "Export PDF",
            // 	'url' => 'downloaduserPdf',
            // 	'status' => true
            // )
        );

        $filters = array(
            array(
                "lbl" => "SSoid",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSOID",
                'dbtbl' => 'users',
            ),
            // array(
            // 	"lbl" => "Exam Year Type",
            // 	'fld' => 'exam_year',
            // 	'input_type' => 'select',
            // 	'options' => $exam_year_session,
            // 	'placeholder' => 'Exam Year',
            // 	'dbtbl' => 'users',
            // ),
            // array(
            // 	"lbl" => "Exam Month Type",
            // 	'fld' => 'exam_month',
            // 	'input_type' => 'select',
            // 	'options' => $exam_month_session,
            // 	'placeholder' => 'Exam Month',
            // 	'dbtbl' => 'users',
            // )
        );

        $custom_component_obj = new CustomComponent;
        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                    /*
                    if(isset($iv['fld']) && $iv['fld']=='ssoid'){
                        $conditions[$iv['dbtbl'] . "." . $iv['fld']] ." like %". $inputs[$iv['fld']]."%";
                    }else {
                        $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                    }
                    */
                }
            }
        }

        Session::put($formId . '_conditions', $conditions);
        $data = $custom_component_obj->getUsersData($formId, true);

        return view('deo.index', compact('data', 'breadcrumbs', 'exportBtn', 'title', 'filters', 'exam_year_session', 'exam_month_session'));
    }

    public function deoshow(Request $request, $id)
    {
        $title = "DEO Details";
        $e_id = $id;
        $id = Crypt::decrypt($e_id);
        $master = User::find($id);
        return view('deo.deoshow', compact('title', 'master'));
    }

    public function store(Request $request)
    {
        $title = "Create DEO Details";
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

        $district_list = $this->districtsByState(6);
        if (count($request->all()) > 0) {
            $id = Auth::user()->id;
            $User = new User; /// create model object
            $validator = Validator::make($request->all(), $User->deoCreaterules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $user_deo_id = Auth::user()->id;
            $current_admission_session_id = Config::get("global.current_admission_session_id");
            $current_exam_month_id = Config::get("global.current_exam_month_id");
            $current_stream_id = Config::get("defaultStreamId");
            $deoRole = config("global.deo");

            $ssoAlreadyExist = DB::table('users')
                ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('users.ssoid', $request->ssoid)
                ->where('model_has_roles.role_id', $deoRole)
                // ->where('users.exam_year',$current_admission_session_id)
                // ->where('users.exam_month',$current_exam_month_id)
                ->count();
            if (@$ssoAlreadyExist > 0) {
                return redirect()->back()->with('error', 'SSO ID already exist with DEO role.')->withInput($request->all());
            }

            $custom_component_obj = new CustomComponent;
            $sso_data = $custom_component_obj->getSSOIDDetials($request->ssoid);
            $sso_data = json_decode($sso_data);
            if (!(isset($sso_data) && !empty($sso_data))) {
                return redirect()->back()->with('error', 'Please enter valid SSO ID')->withInput($request->all());
            }

            $ssoAlreadyExistWithAnyRole = DB::table('users')
                ->where('users.ssoid', $request->ssoid)
                // ->where('users.exam_year',$current_admission_session_id)
                // ->where('users.exam_month',$current_exam_month_id)
                ->first();
            if (isset($ssoAlreadyExistWithAnyRole) && !empty($ssoAlreadyExistWithAnyRole)) {
                //$ssoAlreadyExistWithAnyRole = new User($ssoAlreadyExistWithAnyRole->id);
                //$ssoAlreadyExistWithAnyRole->assignRole(['roles' => $roles]);

                $model_has_role_data = array();
                $model_has_role_data['role_id'] = $deoRole;
                $model_has_role_data['model_type'] = 'App\Models\User';
                $model_has_role_data['model_id'] = $ssoAlreadyExistWithAnyRole->id;
                $TocMarkmodelObj = ModelHasRole::insert($model_has_role_data);

                $userarray = [
                    'mobile' => $request->mobile, 'email' => $request->email, 'name' => $request->name,
                    'district_id' => $request->district_id, 'district_name' => @$district_list[$request->district_id]
                ];
                $user = User::where('id', $ssoAlreadyExistWithAnyRole->id)->update($userarray);
            } else {
                $input = $request->all();
                $password = Hash::make('123456789');
                $current_admission_session_id = Config::get("global.current_admission_session_id");
                $current_exam_month_id = Config::get("global.current_exam_month_id");
                $userarray = ['district_name' => @$district_list[$request->district_id],
                    'district_id' => @$request->district_id, 'mobile' => @$request->mobile, 'ssoid' => @$request->ssoid,
                    'email' => @$request->email, 'password' => @$password, 'name' => @$request->name];

                $user = User::create($userarray);
                $user->assignRole(['roles' => $deoRole]);
            }

            return redirect()->route('deo')->with('message', 'DEO  successfully created');
            /*
            if ($user || $ssoAlreadyExistWithAnyRole) {
                return redirect()->route('deo')->with('message', 'DEO  successfully created');
            }else {
                return redirect()->route('deo')->with('error', 'Failed! DEO  not created');
            }
            */
        }
        return view('deo.create', compact('title', 'district_list', 'breadcrumbs'));
    }

    public function downloaddeoExl(Request $request, $type = "xlsx")
    {
        $users_exl_data = new PracticalExaminerExlExport;
        $filename = 'DEO  ' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($users_exl_data, $filename);
    }

    public function deoedit(Request $request, $id)
    {
        $e_id = $id;
        $id = Crypt::decrypt($e_id);
        $district_list = $this->districtsByState(6);
        $title = "DEO Details";
        $table_id = "Deo_Details";
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

        $user_data = User::find($id);

        if (count($request->all()) > 0) {
            $User = new User; /// create model object
            $validator = Validator::make($request->all(), $User->deoUpdaterules);
            //here we check this ssoid is already with this session or not other then current user id.
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $custom_component_obj = new CustomComponent;
            $sso_data = $custom_component_obj->getSSOIDDetials($request->ssoid);
            $sso_data = json_decode($sso_data);
            if (!(isset($sso_data) && !empty($sso_data))) {
                return redirect()->back()->with('error', 'Please enter valid SSO ID')->withInput($request->all());
            }

            $userCount = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                ->where('model_has_roles.role_id', '=', config("global.deo"))
                ->where('ssoid', '=', $request->ssoid)
                ->where('id', '!=', $user_data->id)
                // ->where('exam_year', '=', $user_data->exam_year)
                // ->where('exam_month', '=', $user_data->exam_month)
                ->count();
            if ($userCount > 0) {
                //$fld = "ssoid";
                //$errMsg = "SSOID already used with deo role";
                //$validator->getMessageBag()->add($fld, $errMsg);
                //return redirect()->back()->withErrors($validator)->withInput($request->all());
                return redirect()->back()->with('error', 'SSO ID already mapped with deo role')->withInput($request->all());
            }

            /*
            $userDistrict = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                    ->where('model_has_roles.role_id', '=', config("global.deo"))
                    ->where('users.district_id', '=', $request->district_id)
                    ->where('users.id', '!=', $user_data->id)
                    ->where('users.exam_year', '=', $user_data->exam_year)
                    ->where('users.exam_month', '=', $user_data->exam_month)
                    ->count();
            if($userDistrict > 0){
                $fld = "district_id";
                $errMsg = "District already mapped with another ssoid.";
                $validator->getMessageBag()->add($fld, $errMsg);
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            */

            $input = $request->all();
            $roles = Config::get("global.practicalexaminer");
            $password = Hash::make('123456789');
            $current_admission_session_id = Config::get("global.current_admission_session_id");
            $current_exam_month_id = Config::get("global.current_exam_month_id");

            $userarray = ['district_name' => $district_list[$request->district_id], 'district_id' => $request->district_id, 'mobile' => $request->mobile, 'ssoid' => $request->ssoid, 'email' => $request->email, 'password' => $password, 'name' => $request->name];


            $user = User::where('id', $id)->update($userarray);

            if ($user) {
                return redirect()->route('deo')->with('message', 'DEO successfully updated');
            } else {
                return redirect()->route('deo')->with('error', 'Failed!DEO not updated');
            }
        }
        return view('deo.deoedit', compact('title', 'user_data', 'breadcrumbs', 'district_list'));
    }
}