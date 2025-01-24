<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Exports\AicenterExlExport;
use App\Exports\UserDeodetailsExlExport;
use App\Helper\CustomHelper;
use App\Models\AicenterDetail;
use App\Models\AicenterDetaillog;
use App\Models\AiCenterMap;
use App\Models\District;
use App\Models\ModelHasRole;
use App\Models\User;
use Auth;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Route;
use Session;
use Spatie\Permission\Models\Role;
use Validator;


class AicenterUserController extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->middleware('permission:user-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-show', ['only' => ['show']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
        $this->middleware('permission:aicenter_dashboard', ['only' => ['dashboard']]);
        $this->middleware('permission:all_delete_users', ['only' => ['deleteusers']]);
        $this->middleware('permission:all_delete_users', ['only' => ['userdeleteactive']]);
    }


    public function update_my_profile(Request $request)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $id = null;
        $role_id = Session::get('role_id');
        $routeDashboard = "";
        $aicenterrole = config("global.aicenter_id");
        if ($role_id == config("global.aicenter_id")) {
            $routeDashboard = route("aicenterdashboard");
        }
        $title = "My Profile";
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

        $userRole = array();
        $user = array();
        if (@$role_id == config("global.aicenter_id")) {
            $id = @Auth::user()->id;
            $user = AicenterDetail::where('user_id', '=', $id)->first();
            $userRole = @$user->roles->pluck('name', 'name')->all();
        }
        $model = "AicenterDetail";
        //$user = AicenterDetail::where('user_id', '=', $id)->first();

        $roles = Role::pluck('name', 'name')->all();
        $state_id = 6;
        $district_list = $this->districtsByState($state_id);
        $block_list = $this->temp_block_details(@$user->temp_district_id);
        $banks = $this->getBanksMaster();
        //$userRole = @$user->roles->pluck('name','name')->all();
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $existUserSso = array();
        if (!empty(@$user->ssoid)) {
            $existUserSso[$user->ssoid] = $user->ssoid;
        }

        if ($request->all()) {
            $input = $request->all();
            $updateData = [
                'school_account_ifsc' => $request->school_account_ifsc,
                'email' => $request->email,
                'school_account_number' => $request->school_account_number,
                'principal_name' => $request->principal_name,
                'school_account_bank_name' => $request->school_account_bank_name,
                'principal_mobile_number' => $request->principal_mobile_number,
                'nodal_officer_name' => $request->nodal_officer_name,
                'nodal_officer_mobile_number' => $request->nodal_officer_mobile_number,
                'pincode' => $request->pincode,
                // 'district_id' =>$request->district_id,
                // 'block_id' =>$request->block_id,
                'temp_district_id' => $request->temp_district_id,
                'temp_block_id' => $request->temp_block_id,

            ];

            $current_admission_session_id = Config::get("global.current_admission_session_id");
            $current_exam_month_id = Config::get("global.current_exam_month_id");

            $AicenterDetail = AicenterDetail::where('user_id', '=', $id)->update($updateData);
            return redirect()->route('update_my_profile')->with('message', 'Your profile successfully updated.');

        }
        $allssoid = array();
        return view('aicenterdetail.update_my_profile', compact('breadcrumbs', 'banks', 'title', 'allssoid', 'user', 'roles', 'userRole', 'district_list', 'block_list', 'model', 'gender_id', 'stream_id', 'role_id', 'aicenterrole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $userSsoIdCount = AicenterDetail::where('ssoid', '=', $request->ssoid)
            ->where('id', "!=", $id)->count();
        $modelObj = new AicenterDetail;
        $validator = Validator::make($request->all(), $modelObj->uerseditmakerules);

        $errors = null;
        $isValid = true;
        // dd($userSsoIdCount);
        if ($userSsoIdCount > 0) {
            $fld = 'ssoid';
            $errMsg = 'Entred SSOID already use with other User.';
            $errors[$fld] = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        }
        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        if (!$isValid) {
            return redirect()->back()->withErrors($errors)->withInput($request->all());
        }
        $input = $request->all();
        $current_admission_session_id = Config::get("global.current_admission_session_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $allssoid = User::where('ssoid', $request->ssoid)->first('id');
        $aicenterroles = Config::get("global.aicenter_id");
        $modeltype = 'App\Models\User';
        $studentarray = ['ssoid' => $request->ssoid,
            'school_account_ifsc' => $request->school_account_ifsc,
            'email' => $request->email,
            'college_name' => $request->college_name,
            'ai_code' => $request->ai_code,
            'exam_year' => $current_admission_session_id,
            'exam_month' => $current_exam_month_id,
            'district_id' => $request->district_id,
            'block_id' => $request->block_id,
            'pincode' => $request->pincode,
            'school_account_number' => $request->school_account_number,
            'principal_name' => $request->principal_name,
            'principal_mobile_number' => $request->principal_mobile_number,
            'nodal_officer_name' => $request->nodal_officer_name,
            'nodal_officer_mobile_number' => $request->nodal_officer_mobile_number,
            'school_account_bank_name' => $request->school_account_bank_name,
            'user_id' => $allssoid->id,];
        $aicenterrole = ['role_id' => $aicenterroles,
            'model_type' => $modeltype,
            'model_id' => $allssoid->id,];
        $AicenterDetail = AicenterDetail::find($id);
        $AicenterDetail->update($studentarray);
        $aicenterroleaicode = ['ai_code' => $AicenterDetail->ai_code];
        $userupdateaicode = user::where('ssoid', $AicenterDetail->ssoid)->where('id', $AicenterDetail->user_id)->update($aicenterroleaicode);
        if ($request->old_user_id != $allssoid->id) {
            $userupdateaicode = user::where('id', $request->old_user_id)->update(['ai_code' => Null]);
            $model_has_roles = DB::table('model_has_roles')->where('role_id', $aicenterroles)->where('model_type', $modeltype)->where('model_id', $request->old_user_id)->delete();
        }
        $model_has_roles = DB::table('model_has_roles')->where('role_id', $aicenterroles)->where('model_type', $modeltype)->where('model_id', $allssoid->id)->first();
        if (empty($model_has_roles)) {
            //$model_has_roles = DB::table('model_has_roles')->where('role_id',$aicenterroles)->where('model_type',$modeltype)->where('model_id',$allssoid->id)->update($aicenterrole);
            $model_has_roles = ModelHasRole::create($aicenterrole);
        }
        if ($AicenterDetail) {
            return redirect()->route('aicenterusers.index')->with('message', 'Aicenter successfully updated');
        } else {
            return redirect()->route('aicenterusers.index')->with('error', 'Failed! Aicenter not updated');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = "Create DEO Details";
        $table_id = "User_Create_Details";
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

        $model = "useradd";
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $district_list = $this->districtsByState(6);
        $block_list = $this->block_details();
        $selected_session = CustomHelper::_get_selected_sessions();

        $aicenterSsoid = AicenterDetail::whereNotNull('user_id')->get('user_id');
        $allssoid = User::whereNotIn('users.id', $aicenterSsoid)->pluck('ssoid', 'ssoid');
        $banks = $this->getBanksMaster();

        return view('aicenterdetail.create', compact('allssoid', 'district_list', 'breadcrumbs', 'block_list', 'model', 'gender_id', 'stream_id', 'banks'));
    }

    public function index(Request $request)
    {
        $combo_name = 'exam_session';
        $exam_month_session = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $exam_year_session = $this->master_details($combo_name);

        $roles = $this->_getRoles();
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCentersWithInActive();
        $district_list = $this->districtsByState(6);
        $block_list = $this->block_details();
        $conditions = array();
        // session::get()
        $allowonlyotherrole = false;
        $role_id = Session::get('role_id');
        if ($role_id == Config::get("global.publication_department")) {
            $allowonlyotherrole = true;
        }
        //$conditions["exam_year"] = CustomHelper::_get_selected_sessions();

        $title = "Ai Center Details";
        $table_id = "AiCenter_Details";
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

        $exportBtn = array(

            array(
                "label" => "Book requirement Export PDF",
                'url' => 'letter_twelve_generate_report_pdf',
                'status' => true,

            )
        );

        $filters = array(
            array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'aicenter_details',

            ),

            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district_list,
                'placeholder' => 'District',
                'dbtbl' => 'aicenter_details',
            ),
            array(
                "lbl" => "Block",
                'fld' => 'block_id',
                'input_type' => 'select',
                'options' => $block_list,
                'placeholder' => 'Block',
                'dbtbl' => 'aicenter_details',
            ),
            array(
                "lbl" => "New District",
                'fld' => 'temp_district_id',
                'input_type' => 'select',
                'options' => $district_list,
                'placeholder' => 'New District',
                'dbtbl' => 'aicenter_details',
            ),

            array(
                "lbl" => "New Block",
                'fld' => 'temp_block_id',
                'input_type' => 'select',
                'options' => $block_list,
                'placeholder' => 'New Block',
                'dbtbl' => 'aicenter_details',
            ),
        );

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        if ($isAdminStatus == true) {
            $filters[] = array(
                "lbl" => "SSoid",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSOID",
                'dbtbl' => 'aicenter_details',

            );
            $filters[] = array(
                "lbl" => "Principal Name",
                'fld' => 'principal_name',
                'input_type' => 'text',
                'placeholder' => "Principal Name",
                'dbtbl' => 'aicenter_details',

            );
            $filters[] = array(
                "lbl" => "Name",
                'fld' => 'nodal_officer_name',
                'input_type' => 'text',
                'placeholder' => "Nodal Officer Name",
                'dbtbl' => 'aicenter_details',
            );
            $exportBtn[] = array(
                "label" => "Export Excel",
                'url' => 'aiCentersdownloadExl',
                'status' => true,
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
        /* Sorting Fields Set Session Start 3*/
        Session::put($formId . '_orderByRaw', $orderByRaw);
        /* Sorting Fields Set Session End 3*/
        Session::put($formId . '_conditions', $conditions);
        $data = $custom_component_obj->getAicenterData($formId, true);
        //'inputs','sortingField',
        return view('aicenterdetail.index', compact('inputs', 'sortingField', 'allowonlyotherrole', 'block_list', 'roles', 'district_list', 'data', 'breadcrumbs', 'exportBtn', 'title', 'filters', 'exam_year_session', 'exam_month_session', 'aiCenters'));
    }

    public function aicenterdelete(Request $request, $id)
    {
        $aicenterroles = Config::get("global.aicenter_id");
        $modeltype = 'App\Models\User';
        $userdatafecth = DB::table('aicenter_details')->where('id', $id)->first();
        $userdataexamyearmonth = DB::table('users')->where('id', $userdatafecth->user_id)->first('exam_year');
        $studentarray = ['ssoid' => $userdatafecth->ssoid,
            'college_name' => $userdatafecth->college_name,
            'ai_code' => $userdatafecth->ai_code,
            'exam_year' => @$userdataexamyearmonth->exam_year,
            'district_id' => $userdatafecth->district_id,
            'block_id' => $userdatafecth->block_id,
            'principal_name' => $userdatafecth->principal_name,
            'nodal_officer_name' => $userdatafecth->nodal_officer_name,];
        $AicenterDetail = AicenterDetaillog::create($studentarray);
        $aicenter_delete = ModelHasRole::where('role_id', $aicenterroles)->where('model_type', $modeltype)->where('model_id', $userdatafecth->user_id)->delete();
        $aicenter_details = DB::table('aicenter_details')->where('id', $id)->update(['ssoid' => NULL, 'user_id' => NULL]);
        $aicenters_details = DB::table('users')->where('ssoid', $userdatafecth->ssoid)->update(['ai_code' => NULL]);
        if ($aicenter_details && $aicenter_delete) {
            return redirect()->route('aicenterusers.index')->with('message', 'Aicenter successfully Remove created');
        } else {
            return redirect()->route('aicenterusers.index')->with('error', 'Failed! Aicenter not created');
        }
    }

    public function aicenterusersactive($id, $active)
    {
        $aicenter_details = DB::table('aicenter_details')->where('id', $id)->update(['active' => $active]);

        if ($aicenter_details) {
            if ($active == 1) {
                return redirect()->route('aicenterusers.index')->with('message', 'Aicenter successfully  Active ');
            } elseif ($active == 0) {
                return redirect()->route('aicenterusers.index')->with('message', 'Aicenter successfully INActive ');
            }
        } else {
            return redirect()->route('aicenterusers.index')->with('error', 'Failed! Aicenter not created');
        }
    }

    public function livetableupdate(Request $request)
    {
        $livetableupdate = DB::table('aicenter_details')->where('id', $request->id)->update([$request->name => $request->value]);
        return $livetableupdate;
    }

    public function aiCentersdownloadExl(Request $request, $type = "xlsx")
    {
        $users_exl_data = new AicenterExlExport;
        $filename = 'Aicenter_details' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($users_exl_data, $filename);
    }

    public function downloadDeoExl(Request $request, $type = "xlsx")
    {
        $users_exl_data = new UserDeodetailsExlExport;
        $filename = 'Users_DEO_details' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($users_exl_data, $filename);
    }

    public function store(Request $request)
    {
        if (count($request->all()) > 0) {
            $responses = $this->AicnterDetailsValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach ($responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                }
            }
            if ($responses == false) {
                $input = $request->all();
                $allssoid = User::where('ssoid', $request->ssoid)->first('id');
                $current_admission_session_id = Config::get("global.current_admission_session_id");
                $aicenterroles = Config::get("global.aicenter_id");
                $modeltype = 'App\Models\User';
                $current_exam_month_id = Config::get("global.current_exam_month_id");
                $district_name = District::where('id', $request->district_id)->first('name');
                $studentarray = ['ssoid' => $request->ssoid,
                    'school_account_ifsc' => $request->school_account_ifsc,
                    'email' => $request->email,
                    'college_name' => $request->college_name,
                    'ai_code' => $request->ai_code,
                    'exam_year' => $current_admission_session_id,
                    'exam_month' => $current_exam_month_id,
                    'temp_district_id' => $request->district_id,
                    'temp_block_id' => $request->block_id,
                    'temp_district_name' => $district_name->name,
                    'district_id' => $request->district_id,
                    'block_id' => $request->block_id,
                    'pincode' => $request->pincode,
                    'school_account_number' => $request->school_account_number,
                    'school_account_bank_name' => $request->school_account_bank_name,
                    'principal_name' => $request->principal_name,
                    'principal_mobile_number' => $request->principal_mobile_number,
                    'nodal_officer_name' => $request->nodal_officer_name,
                    'nodal_officer_mobile_number' => $request->nodal_officer_mobile_number,
                    'user_id' => $allssoid->id,];
                $aicenterrole = ['role_id' => $aicenterroles,
                    'model_type' => $modeltype,
                    'model_id' => $allssoid->id,];
                $AicenterDetail = AicenterDetail::create($studentarray);
                $aicenterroleaicode = ['ai_code' => $AicenterDetail->ai_code];
                $aicentermapping = ['parent_aicode' => $AicenterDetail->ai_code,
                    'ai_code' => $AicenterDetail->ai_code,
                    'is_deleted' => 0];
                $userupdateaicode = user::where('ssoid', $AicenterDetail->ssoid)->where('id', $AicenterDetail->user_id)->update($aicenterroleaicode);
                $model_has_roles = DB::table('model_has_roles')->where('role_id', $aicenterroles)->where('model_type', $modeltype)->where('model_id', $allssoid->id)->first();
                if (!empty($model_has_roles)) {
                    $model_has_roles = DB::table('model_has_roles')->where('role_id', $aicenterroles)->where('model_type', $modeltype)->where('model_id', $allssoid->id)->update($aicenterrole);
                } else {
                    $model_has_roles = ModelHasRole::create($aicenterrole);
                }
                $aicentermappingtable = DB::table('ai_center_maps')->where('parent_aicode', $aicenterroles)->where('ai_code', $modeltype)->first();
                if (!empty($aicentermappingtable)) {
                    $aicentermappingtable = DB::table('ai_center_maps')->where('parent_aicode', $aicenterroles)->where('ai_code', $modeltype)->where('id', $aicentermappingtable->id)->update($aicentermapping);
                } else {
                    $aicentermappingtable = AiCenterMap::create($aicentermapping);
                }
                if ($AicenterDetail) {
                    return redirect()->route('aicenterusers.index')->with('message', 'Aicenter successfully created');
                } else {
                    return redirect()->route('aicenterusers.index')->with('error', 'Failed! Aicenter not created');
                }
            } else {
                $customerrors = implode(",", @$responseFinal[$k]['customerrors']);
                return redirect()->back()->withErrors($responseFinal['validator'])->withInput($request->all());

            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $model = "AicenterDetail";
        $user = AicenterDetail::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $district_list = $this->districtsByState(6);
        $block_list = $this->block_details($user->district_id);
        $userRole = $user->roles->pluck('name', 'name')->all();
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);

        $existUserSso = array();
        if (!empty(@$user->ssoid)) {
            $existUserSso[$user->ssoid] = $user->ssoid;
        }
        $aicenterSsoid = AicenterDetail::whereNotNull('user_id')->get('user_id');
        $allssoid = User::whereNotIn('users.id', $aicenterSsoid)->pluck('ssoid', 'ssoid')->toArray();
        $allssoid[@$existUserSso[$user->ssoid]] = @$existUserSso[$user->ssoid];
        $banks = $this->getBanksMaster();

        return view('aicenterdetail.edit', compact('allssoid', 'user', 'roles', 'userRole', 'district_list', 'block_list', 'model', 'gender_id', 'stream_id', 'banks'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('id', $id)->delete();
        if ($user) {
            return redirect()->route('users.index')->with('message', 'User successfully Deleted');
        } else {
            return redirect()->route('users.index')->with('error', 'Failed! User not Deleted');
        }

    }

    public function userdeleteactive($id)
    {
        $student = DB::table('users')
            ->where('id', $id)
            ->update(['deleted_at' => NULL]);
        if ($student) {
            return redirect()->route('users.index')->with('message', 'User successfully Active');
        } else {
            return redirect()->route('users.index')->with('error', 'Failed! User not Active');
        }
    }

    public function showListAicenters(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $aiCenters = collect();
        $block_list = collect();

        $district_list = $this->districtsByState(6);
        $master_block_list = $this->block_details();
        if (!empty($request->temp_district_id)) {
            $block_list = $this->block_details($request->temp_district_id);
        }

        $aiCenters = $custom_component_obj->_getTempAiCentersIdByAiCodebolck(@$request->temp_district_id, @$request->block_id, @$request->ai_code);


        $query_array = $request->query();
        $showPopup = true;
        if (@$query_array) {
            $showPopup = false;
        }

        $conditions = array();
        $title = "AI Centre Details (एआई केंद्र विवरण)";
        $table_id = "AiCenter_Details";
        $combo_name = 'paginator_limit';
        $pagalimit = $this->master_details($combo_name);

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

        $exportBtn = array(

            // array(
            // "label" => "Export PDF",
            // 'url' => 'downloaduserPdf',
            // 'status' => false,
            // )
        );

        $filters = array(
            array(
                "lbl" => "District(ज़िला)",
                'fld' => 'temp_district_id',
                'input_type' => 'select',
                'options' => $district_list,
                'placeholder' => 'District(ज़िला)',
                'dbtbl' => 'aicenter_details',
            ),
            array(
                "lbl" => "Block(खंड)",
                'fld' => 'temp_block_id',
                'input_type' => 'select',
                'options' => $block_list,
                'placeholder' => 'Block(खंड)',
                'dbtbl' => 'aicenter_details',
            ),
            array(
                "lbl" => "AI Centre's(एआई सेंटर)",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => "AI Centre's(एआई सेंटर)",
                'dbtbl' => 'aicenter_details',

            ),

        );
        /* Sorting Fields Set Start 1*/
        $sorting = array();
        $orderByRaw = "";
        $inputs = "";
        $sortFilters = $filters;
        if (@$sortFilters[1]) {
            unset($sortFilters[1]);
        }
        if (@$sortFilters[0]) {
            unset($sortFilters[0]);
        }
        $sortingField = $this->_getSortingFields($sortFilters);
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
        /* Sorting Fields Set Session Start 3*/
        Session::put($formId . '_orderByRaw', $orderByRaw);
        /* Sorting Fields Set Session End 3*/
        Session::put($formId . '_conditions', $conditions);
        // if(!empty($request->pagevalue)){
        // 	$value=$request->pagevalue;
        // 	Session::put($formId. '_paginatevalue', $value);
        // }getAicenterDataWithCache
        $data = $custom_component_obj->getTempAicenterDataWithCache($formId, false);
        //'inputs','sortingField',

        return view('aicenterdetail.aicenteruserslist', compact('showPopup', 'inputs', 'formId', 'master_block_list', 'pagalimit', 'sortingField', 'block_list', 'district_list', 'data', 'breadcrumbs', 'exportBtn', 'title', 'filters', 'aiCenters'));

    }


}