<?php

namespace App\Http\Controllers;

use App\Component\SmsComponent;
use DB;
use Illuminate\Http\Request;
use Session;

class SmsController extends Controller
{
    public function index(Request $request)
    {
        $defaultPageLimit = config("global.defaultPageLimit");
        $yes_no = $this->master_details('yesno');
        $yes_no_temp = $this->master_details('yesno');


        $yes_no_temp[""] = "No";

        $title = "लघु संदेश सेवा रिपोर्ट(SMS Short Message Service Report)";
        $table_id = "SMS_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));

        $sms_component_obj = new SmsComponent;
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
        $permissions = array();

        $exportBtn = array(
            array(
                "label" => "Export Excel",
                'url' => 'downloadApplicationExl',
                'status' => true,
            ),
            array(
                "label" => "Import Excel",
                'url' => 'downloadApplicationPdf',
                'status' => true
            ),
            array(
                "label" => "Send SMS",
                'url' => 'downloadApplicationPdf',
                'status' => true
            ),
        );

        $role_id = @Session::get('role_id');
        $Printer = config("global.Printer");
        if ($role_id != $Printer) {
            $filters = array(
                array(
                    "lbl" => "Start date",
                    'fld' => 'start_date',
                    'input_type' => 'datetime-local',
                    'placeholder' => "Start Date",
                    'dbtbl' => 'sms_managements',
                ),
                array(
                    "lbl" => "End date",
                    'fld' => 'end_date',
                    'input_type' => 'datetime-local',
                    'placeholder' => "End Date",
                    'dbtbl' => 'sms_managements',
                ),
                array(
                    "lbl" => "Mobile",
                    'fld' => 'mobile',
                    'input_type' => 'text',
                    'placeholder' => "Mobile",
                    'search_type' => "text",
                    'dbtbl' => 'sms_managements',
                ),
                array(
                    "lbl" => "Is Active",
                    'fld' => 'status',
                    'input_type' => 'select',
                    'options' => $yes_no,
                    'search_type' => "text",
                    'placeholder' => 'Is Active',
                    'dbtbl' => 'sms_managements',
                ),
            );
        } else {
            $filters = array(
                array(
                    "lbl" => "Start date",
                    'fld' => 'start_date',
                    'input_type' => 'datetime-local',
                    'placeholder' => "Start Date",
                    'dbtbl' => 'sms_managements',
                ),
                array(
                    "lbl" => "End date",
                    'fld' => 'end_date',
                    'input_type' => 'datetime-local',
                    'placeholder' => "End Date",
                    'dbtbl' => 'sms_managements',
                ),
                array(
                    "lbl" => "Mobile",
                    'fld' => 'mobile',
                    'input_type' => 'text',
                    'placeholder' => "Mobile",
                    'search_type' => "like", //like
                    'dbtbl' => 'sms_managements',
                ),
                array(
                    "lbl" => "Status",
                    'fld' => 'status',
                    'input_type' => 'select',
                    'options' => $yes_no,
                    'search_type' => "text",
                    'placeholder' => 'Status',
                    'dbtbl' => 'sms_managements',
                ),


            );
        }


        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Mobile",
                'fld' => 'mobile',
                'input_type' => 'text',
                'placeholder' => "Mobile",
                'dbtbl' => 'sms_managements',
            ),
            array(
                "lbl" => "Status",
                'fld' => 'status',
                'input_type' => 'select',
                'options' => $yes_no,
                'dbtbl' => 'sms_managements',
            )
        );
        $ssoUpdateShowMessage = false;
        $isAdminStatus = true;
        $conditions["sms_managements.status"] = 1;


        $role_id = @Session::get('role_id');
        $actions = array();

        if (in_array("update_student_details", $permissions)) {
            $actions[] = array(
                'fld' => 'view',
                'extraCondition' => 'student_applications',
                'icon' => '<i class="waves-effect waves-teal btn gradient-45deg-deep-blue-blue white-text secondary-content" title="Click here to Update SSO.">SSO</i>',
                'fld_url' => '../student/update_basic_details/#id#'
            );
            $ssoUpdateShowMessage = true;
        }

        if (in_array("student_mark_reject", $permissions)) {
            $actions[] = array(
                'fld' => 'view',
                'icon' => '<i class="waves-effect waves-teal btn gradient-45deg-deep-blue-blue white-text secondary-content" title="Click here to MarkReject.">MarkReject</i>',
                'fld_url' => '../student/student_mark_reject/#id#'
            );
            $ssoUpdateShowMessage = true;
        }
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
        $symbol = null;
        $symbols = null;
        $symbolis = null;
        if ($request->all()) {
            $inputs = $request->all();
            if (@$inputs['is_full'] && $inputs['is_full'] == 1) {
                if ($isAdminStatus == true) {
                    unset($conditions["students.exam_year"]);
                }
            }
            foreach ($inputs as $k => $v) {
                if ($k == 'is_full') {
                    continue;
                }
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if (@$iv['fld'] == $k && $iv['fld'] == $k) {
                            if ($iv['fld'] == 'enrollmentgen' && $inputs[$iv['fld']] == 1) {
                                $symbol = "!=";
                            } elseif ($iv['fld'] == 'enrollmentgen' && $inputs[$iv['fld']] == 0) {
                                $symbol = "=";
                            } else {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            }

                            if ($iv['fld'] == 'is_otp_verified' && $inputs[$iv['fld']] == 1) {
                                $symbolis = "!=";
                            } elseif ($iv['fld'] == 'is_otp_verified' && $inputs[$iv['fld']] == 0) {
                                $symbolis = "=";
                            } else {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            }
                            if ($iv['fld'] == 'is_self_filled' && $inputs[$iv['fld']] == 1) {
                                $symbols = "!=";
                            } elseif ($iv['fld'] == 'is_self_filled' && $inputs[$iv['fld']] == 0) {
                                $symbols = "=";
                            } else {
                                $conditions[$iv['dbtbl'] . "." . $k] = $v;
                            }
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    //$conditions[ $iv['dbtbl'] . "." . $k] = $v;
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }

            /* Sorting Order By Set Start 2*/
            $orderByRaw = $this->_setSortingArrayFields(@$inputs['sorting'], $sortingField);
            /* Sorting Order By Set End 2*/
        }

        if ($isAdminStatus == false) {
            $aicenter_user_id = Auth::user()->id;
            $aicenter_user_ids = $sms_component_obj->getAiCentersuserdatacode($aicenter_user_id);
            $auth_user_id = $aicenter_user_ids->ai_code;
            $aicenter_mapped_data = $sms_component_obj->getAiCentersmappeduserdatacode($auth_user_id);
            $role_id = Session::get('role_id');
            if ($role_id == config("global.aicenter_id")) {
                $conditions['students.aicenter_mapped_data'] = @$aicenter_mapped_data->toArray();
            }
        } else if (isset($inputs['ai_code'])) {
            $auth_user_id = @$inputs['ai_code'];
            $aicenter_mapped_data = $sms_component_obj->getAiCentersmappeduserdatacode($auth_user_id);

            $conditions['students.aicenter_mapped_data'] = @$aicenter_mapped_data->toArray();
            unset($conditions['students.ai_code']);
        }


        /* Sorting Fields Set Session Start 3*/
        Session::put($formId . '_orderByRaw', $orderByRaw);
        /* Sorting Fields Set Session End 3*/
        Session::put($formId . '_conditions', $conditions);
        Session::put($formId . '_symbol', $symbol);
        Session::put($formId . '_symbols', $symbols);
        Session::put($formId . 'symbolis', $symbolis);

        $master = $sms_component_obj->getListData($formId);


        return view('sms.index', compact('ssoUpdateShowMessage', 'sortingField', 'yes_no_temp', 'actions', 'tableData', 'master', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs'))->withInput($request->all());
    }

    public function import()
    {
        echo "Test";
        die;
    }

    public function export()
    {
        echo "Test";
        die;
    }
}