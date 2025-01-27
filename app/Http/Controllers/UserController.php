<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Exports\UserDeodetailsExlExport;
use App\Exports\UserdetailsExlExport;
use App\Helper\CustomHelper;
use App\Models\User;
use Auth;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use Spatie\Permission\Models\Role;
use Validator;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:user-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-show', ['only' => ['show']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
        $this->middleware('permission:aicenter_dashboard', ['only' => ['dashboard']]);
        $this->middleware('permission:all_delete_users', ['only' => ['deleteusers']]);
        $this->middleware('permission:all_delete_users', ['only' => ['userdeleteactive']]);
    }

    public function dashboard()
    {
        $custom_component_obj = new CustomComponent;
        $aicenter_user_id = Auth::user()->id;
        $aicenter_user_ids = $custom_component_obj->getAiCentersuserdatacode($aicenter_user_id);
        $auth_user_id = @$aicenter_user_ids->ai_code;
        Session::put("ai_code", $auth_user_id);
        $aicenter_mapped_data = $custom_component_obj->getAiCentersmappeduserdatacode($auth_user_id);

        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'application_dashboard';
        $application_dashboard = $this->master_details($combo_name);
        $current_session = CustomHelper::_get_selected_sessions();
        $combo_name = 'exam_month';
        $exam_monthall = $this->master_details($combo_name);
        /* Start */
        // $applicationCount['total']['status'] = $application_dashboard[3];
        // $applicationCount['total']['total_registered_student'] =$custom_component_obj->getallStudentCount($auth_user_id);
        // $applicationCount['total']['total_lock_Submit_student'] = $custom_component_obj->getallStudentLockSubmitCount($auth_user_id);
        // $applicationCount['total']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount($auth_user_id);
        // $applicationCount['total']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount($auth_user_id);
        // $applicationCount['total']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount($auth_user_id);
        // $applicationCount['total']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount($auth_user_id);

        // $applicationCount['2']['status'] = $application_dashboard[2];
        // $applicationCount['2']['total_registered_student'] = $custom_component_obj->getallStudentCount($auth_user_id,2);
        // $applicationCount['2']['total_lock_Submit_student'] =$custom_component_obj->getallStudentLockSubmitCount($auth_user_id,2);
        // $applicationCount['2']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount($auth_user_id,2);
        // $applicationCount['2']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount($auth_user_id,2);
        // $applicationCount['2']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount($auth_user_id,2);
        // $applicationCount['2']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount($auth_user_id,2);

        // $applicationCount['1']['status'] = $application_dashboard[1];
        // $applicationCount['1']['total_registered_student'] = $custom_component_obj->getallStudentCount($auth_user_id,1);
        // $applicationCount['1']['total_lock_Submit_student'] =$custom_component_obj->getallStudentLockSubmitCount($auth_user_id,1);
        // $applicationCount['1']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount($auth_user_id,1);
        // $applicationCount['1']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount($auth_user_id,1);
        // $applicationCount['1']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount($auth_user_id,1);
        // $applicationCount['1']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount($auth_user_id,1);


        $applicationVerifyCount['1']['status'] = $applicationCount['1']['status'] = $application_dashboard[1];
        $applicationVerifyCount['2']['status'] = $applicationCount['2']['status'] = $application_dashboard[2];
        $applicationVerifyCount['total']['status'] = $applicationCount['total']['status'] = $application_dashboard[3];
        if ($applicationCount['1']['status'] == @$application_dashboard[1]) {
            $exam_month = 1;
            $applicationCount['1']['total_registered_student'] = $custom_component_obj->getallStudentCount($aicenter_mapped_data, 1);
            $applicationCount['1']['fresh_get_ao_rejected'] = $custom_component_obj->getAicenterVerificationPart($aicenter_mapped_data, $exam_month, 3);
            $applicationCount['1']['total_lock_Submit_student'] = $custom_component_obj->getallStudentLockSubmitCount($aicenter_mapped_data, 1);
            $applicationCount['1']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount($aicenter_mapped_data, 1);
            $applicationCount['1']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount($aicenter_mapped_data, 1);
            $applicationCount['1']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount($aicenter_mapped_data, 1);
            $applicationCount['1']['get_sso_updated_student_count'] = $custom_component_obj->getallStudentWhoseSSOIdMapped($aicenter_mapped_data, 1);
            $applicationCount['1']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount($aicenter_mapped_data, 1);

            $applicationVerifyCount[$exam_month]['fresh_get_aicenter_not_verfied'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 1);
            $applicationVerifyCount[$exam_month]['fresh_get_aicenter_verfied'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 2);
            $applicationVerifyCount[$exam_month]['fresh_get_aicenter_rejected'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 3);
            $applicationVerifyCount[$exam_month]['fresh_get_aicenter_clarification'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 4);

        }

        if ($applicationCount['2']['status'] == @$application_dashboard[2]) {
            $exam_month = 2;
            $applicationCount['2']['total_registered_student'] = $custom_component_obj->getallStudentCount($aicenter_mapped_data, 2);
            $applicationCount['2']['fresh_get_ao_rejected'] = $custom_component_obj->getAicenterVerificationPart($aicenter_mapped_data, $exam_month, 3);
            $applicationCount['2']['total_lock_Submit_student'] = $custom_component_obj->getallStudentLockSubmitCount($aicenter_mapped_data, 2);
            $applicationCount['2']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount($aicenter_mapped_data, 2);
            $applicationCount['2']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount($aicenter_mapped_data, 2);
            $applicationCount['2']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount($aicenter_mapped_data, 2);
            $applicationCount['2']['get_sso_updated_student_count'] = $custom_component_obj->getallStudentWhoseSSOIdMapped($aicenter_mapped_data, 2);
            $applicationCount['2']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount($aicenter_mapped_data, 2);

            $applicationVerifyCount[$exam_month]['fresh_get_aicenter_not_verfied'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 1);
            $applicationVerifyCount[$exam_month]['fresh_get_aicenter_verfied'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 2);
            $applicationVerifyCount[$exam_month]['fresh_get_aicenter_rejected'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 3);
            $applicationVerifyCount[$exam_month]['fresh_get_aicenter_clarification'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 4);


        }

        if ($applicationCount['total']['status'] == @$application_dashboard[3]) {
            $fld = "total_registered_student";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "fresh_get_ao_rejected";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];

            $fld = "total_lock_Submit_student";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_payment_not_pay_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_zero_fees_payment_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_payment_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "eligible_get_Student_payment_not_pay_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_sso_updated_student_count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];

            $fld = "fresh_get_aicenter_not_verfied";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_aicenter_verfied";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_aicenter_rejected";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_aicenter_clarification";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];


        }


        /* End */
        /*reval dashboard start*/
        $exam_month = 1;
        $reval[$exam_month]['reval_total_registered_student'] = $custom_component_obj->getRevalallStudentCount($aicenter_mapped_data, $exam_month);
        $reval[$exam_month]['reval_total_lock_Submit_student'] = $custom_component_obj->getRevalallStudentLockSubmitCount($aicenter_mapped_data, $exam_month);
        $reval[$exam_month]['reval_get_Student_payment_Count'] = $custom_component_obj->getRevalallStudentpaymentCount($aicenter_mapped_data, $exam_month);
        $reval[$exam_month]['reval_get_Student_payment_not_pay_Count'] = $custom_component_obj->getRevalallStudentpaymentnotpayCount($aicenter_mapped_data, $exam_month);
        $reval[$exam_month]['reval_get_Eligiable_Students'] = $custom_component_obj->getEligibleRevalaryallStudentCount($aicenter_mapped_data, $exam_month);


        $exam_month = 2;
        $reval[$exam_month]['reval_total_registered_student'] = $custom_component_obj->getRevalallStudentCount($aicenter_mapped_data, $exam_month);
        $reval[$exam_month]['reval_total_lock_Submit_student'] = $custom_component_obj->getRevalallStudentLockSubmitCount($aicenter_mapped_data, $exam_month);
        $reval[$exam_month]['reval_get_Student_payment_Count'] = $custom_component_obj->getRevalallStudentpaymentCount($aicenter_mapped_data, $exam_month);
        $reval[$exam_month]['reval_get_Student_payment_not_pay_Count'] = $custom_component_obj->getRevalallStudentpaymentnotpayCount($aicenter_mapped_data, $exam_month);
        $reval[$exam_month]['reval_get_Eligiable_Students'] = $custom_component_obj->getEligibleRevalaryallStudentCount($aicenter_mapped_data, $exam_month);
        /* End */


        $supp = array();

        $supp_exam_month = Config::get('global.supp_current_admission_exam_month');
        if ($supp_exam_month == 1) {
            $exam_month = $supp_exam_month;
            $supp[$exam_month]['supplementary_total_registered_student'] = $custom_component_obj->getsupplementaryallStudentCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_total_lock_Submit_student'] = $custom_component_obj->getsupplementaryallStudentLockSubmitCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_get_Student_payment_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_get_Student_payment_not_pay_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentnotpayCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_get_Eligiable_Students'] = $custom_component_obj->getEligibleSupplementaryallStudentCount($aicenter_mapped_data, $exam_month);

            $supp[$exam_month]['supplementary_get_aicenter_not_verfied'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data, $exam_month, '1');
            $supp[$exam_month]['supplementary_get_aicenter_verified'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data, $exam_month, '2');
            $supp[$exam_month]['supplementary_get_aicenter_rejected'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data, $exam_month, '3');
            $supp[$exam_month]['supplementary_get_aicenter_clarification'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data, $exam_month, '4');
        } else {
            $exam_month = $supp_exam_month;
            $supp[$exam_month]['supplementary_total_registered_student'] = $custom_component_obj->getsupplementaryallStudentCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_total_lock_Submit_student'] = $custom_component_obj->getsupplementaryallStudentLockSubmitCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_get_Student_payment_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_get_Student_payment_not_pay_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentnotpayCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_get_Eligiable_Students'] = $custom_component_obj->getEligibleSupplementaryallStudentCount($aicenter_mapped_data, $exam_month);

            $supp[$exam_month]['supplementary_get_aicenter_not_verfied'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data, $exam_month, '1');
            $supp[$exam_month]['supplementary_get_aicenter_verified'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data, $exam_month, '2');
            $supp[$exam_month]['supplementary_get_aicenter_rejected'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data, $exam_month, '3');
            $supp[$exam_month]['supplementary_get_aicenter_clarification'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data, $exam_month, '4');
        }


        return view('application.dashboard.aicenterdashboard', compact('applicationVerifyCount', 'applicationCount', 'current_session', 'admission_sessions', 'supp', 'reval', 'exam_monthall'));
    }

    public function index(Request $request)
    {
        $combo_name = 'exam_session';
        $exam_month_session = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $exam_year_session = $this->master_details($combo_name);

        $roles = $this->_getRoles();
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $district_list = $this->districtsByState(6);
        $block_list = $this->block_details();

        $conditions = array();
        // $conditions["users.exam_year"] = CustomHelper::_get_selected_sessions();

        $title = "Users Details";
        $table_id = "User_Details";
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
                "label" => "Export Excel",
                'url' => 'downloaduserExl',
                'status' => true,
            ),
            // array(
            // "label" => "Export PDF",
            // 'url' => 'downloaduserPdf',
            // 'status' => false,
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
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Name",
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Email",
                'fld' => 'email',
                'input_type' => 'text',
                'placeholder' => "Email",
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Mobile",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Mobile",
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district_list,
                'placeholder' => 'District',
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Block",
                'fld' => 'block_id',
                'input_type' => 'select',
                'options' => $block_list,
                'placeholder' => 'Block',
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Role",
                'fld' => 'role_id',
                'input_type' => 'select',
                'options' => $roles,
                'placeholder' => 'Role',
                'dbtbl' => 'model_has_roles',
            )
        );

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
        // if($conditions == 'users.ai_code1'){
        // 	echo 'sss';
        // }else{

        // }

        /* Sorting Fields Set Session Start 3*/
        Session::put($formId . '_orderByRaw', $orderByRaw);
        /* Sorting Fields Set Session End 3*/

        Session::put($formId . '_conditions', $conditions);
        $data = $custom_component_obj->getUsersData($formId, true);
        //'inputs','sortingField',
        return view('user.index', compact('data', 'breadcrumbs', 'exportBtn', 'title', 'filters', 'exam_year_session', 'exam_month_session', 'aiCenters', 'district_list', 'block_list'));
    }


    public function deleteusers(Request $request)
    {
        $combo_name = 'exam_session';
        $exam_month_session = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $exam_year_session = $this->master_details($combo_name);
        $district_list = $this->districtsByState(6);
        $block_list = $this->block_details();

        $roles = $this->_getRoles();
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();

        $conditions = array();
        // $conditions["users.exam_year"] = CustomHelper::_get_selected_sessions();

        $title = "Users Details";
        $table_id = "User_Details";
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
                "label" => "Export Excel",
                'url' => 'downloaduserExl',
                'status' => true,
            ),
            // array(
            // "label" => "Export PDF",
            // 'url' => 'downloaduserPdf',
            // 'status' => true
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
            array(
                "lbl" => "Name",
                'fld' => 'name',
                'input_type' => 'text',
                'placeholder' => "Name",
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Email",
                'fld' => 'email',
                'input_type' => 'text',
                'placeholder' => "Email",
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Mobile",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Mobile",
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "District",
                'fld' => 'district_id',
                'input_type' => 'select',
                'options' => $district_list,
                'placeholder' => 'District',
                'dbtbl' => 'users',
            ),
            array(
                "lbl" => "Block",
                'fld' => 'block_id',
                'input_type' => 'select',
                'options' => $block_list,
                'placeholder' => 'Block',
                'dbtbl' => 'users',
            ),


        );
        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }
        if ($conditions == 'users.ai_code1') {
            echo 'sss';
        } else {

        }

        Session::put($formId . '_conditions', $conditions);
        $data = $custom_component_obj->getdeleteUsersData($formId, true);


        return view('user.deleteuser', compact('data', 'breadcrumbs', 'exportBtn', 'title', 'filters', 'exam_year_session', 'exam_month_session', 'aiCenters', 'district_list', 'block_list'));
    }

    public function downloaduserExl(Request $request, $type = "xlsx")
    {
        $users_exl_data = new UserdetailsExlExport;
        $filename = 'Users_details' . date('d-m-Y H:i:s') . '.' . $type;
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
            $responses = $this->UserDetailsValidation($request);
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
                $password = Hash::make('123456789');
                $dobFormat = $request->dob;
                $dobFormats = date("Y-m-d", strtotime(@$dobFormat));
                $current_admission_session_id = Config::get("global.current_admission_session_id");
                $current_exam_month_id = Config::get("global.current_exam_month_id");
                $studentarray = ['ssoid' => $request->ssoid, 'name' => $request->name,
                    'father_name' => $request->father_name, 'password' => $password,
                    'mother_name' => $request->mother_name, 'email' => $request->email, 'stream' => $request->stream,
                    'gender_id' => $request->gender_id, 'exam_year' => $current_admission_session_id,
                    'exam_month' => $current_exam_month_id, 'district_id' => $request->district_id,
                    'block_id' => $request->block_id, 'account_holder_name' => $request->account_holder_name,
                    'bank_name' => $request->bank_name, 'ifsc' => $request->ifsc, 'dob' => $dobFormats,
                    'mobile' => $request->mobile, 'pincode' => $request->pincode, 'account_number' => $request->account_number,];
                $user = User::create($studentarray);
                $user->assignRole($request->input('roles'));

                if ($user) {
                    return redirect()->route('users.index')->with('message', 'User successfully created');
                } else {
                    return redirect()->route('users.index')->with('error', 'Failed! User not created');
                }
            } else {
                $customerrors = implode(",", @$responseFinal[$k]['customerrors']);
                return redirect()->back()->withErrors($responseFinal['validator'])->withInput($request->all());

            }
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
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
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
        $roles = Role::where('status', 1)->pluck('name', 'name')->all();
        $district_list = $this->districtsByState(6);
        $block_list = $this->block_details();
        return view('user.create', compact('roles', 'district_list', 'breadcrumbs', 'block_list', 'model', 'stream_id', 'gender_id'));
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
        $model = "useradd";
        $user = User::find($id);
        $roles = Role::where('status', 1)->pluck('name', 'name')->all();
        $district_list = $this->districtsByState(6);
        $block_list = $this->block_details($user->district_id);
        $userRole = $user->roles->pluck('name', 'name')->all();
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        return view('user.edit', compact('user', 'roles', 'userRole', 'district_list', 'block_list', 'model', 'gender_id', 'stream_id'));
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
        $aicenter_details = DB::table('aicenter_details')->where('user_id', $id)->update(['ssoid' => NULL, 'user_id' => NULL]);
        if ($user) {
            return redirect()->route('users.index')->with('message', 'User successfully Deleted');
        } else {
            return redirect()->route('users.index')->with('error', 'Failed! User not Deleted');
        }

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
        $userSsoIdCount = User::where('ssoid', '=', $request->ssoid)
            ->where('id', "!=", $id)->count();
        $modelObj = new User;
        $validator = Validator::make($request->all(), $modelObj->uerseditmakerules);

        $errors = null;
        $isValid = true;
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
        $dobFormat = $request->dob;
        $dobFormats = date("Y-d-m", strtotime(@$dobFormat));
        $current_admission_session_id = Config::get("global.current_admission_session_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $studentarray = ['ssoid' => $request->ssoid, 'name' => $request->name,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name, 'email' => $request->email, 'stream' => $request->stream,
            'gender_id' => $request->gender_id, 'exam_year' => $current_admission_session_id,
            'exam_month' => $current_exam_month_id, 'district_id' => $request->district_id,
            'block_id' => $request->block_id, 'account_holder_name' => $request->account_holder_name,
            'bank_name' => $request->bank_name, 'ifsc' => $request->ifsc, 'dob' => $dobFormats,
            'mobile' => $request->mobile, 'pincode' => $request->pincode, 'account_number' => $request->account_number,];
        $user = User::find($id);
        $user->update($studentarray);
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));

        if ($user) {
            return redirect()->route('users.index')->with('message', 'User successfully updated');
        } else {
            return redirect()->route('users.index')->with('error', 'Failed! User not updated');
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


}