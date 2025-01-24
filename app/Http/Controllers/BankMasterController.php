<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\models\BankMaster;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Redirect;
use Session;
use Validator;

class BankMasterController extends Controller
{
    function __construct()
    {

    }

    public function index(Request $request)
    {
        $title = "Bank List";
        $table_id = "bank list";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $banklist = array();
        $custom_component_obj = new CustomComponent;
        //$bank=$this->_getListBanksName();
        //dd($bank); 
        $bank_list = BankMaster::whereNotNull('BANK_ID')->whereNotNull('BANK_NAME')->whereNull('deleted_at')->groupBy('BANK_NAME')->orderBy('BANK_NAME')->pluck('BANK_NAME', 'BANK_NAME');

        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => "Bank list"
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );

        $exportBtn = array(
            array(
                "label" => "Export Excel",
                'url' => 'downloadStudentFeesExl',
                'status' => false,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'theoryExaminerListPdf',
                'status' => false
            ),
        );
        $filters = array(
            array(
                "lbl" => "Bank Name",
                'fld' => 'BANK_NAME',
                'input_type' => 'select',
                'options' => $bank_list,
                'placeholder' => 'Bank Name',
                'dbtbl' => '',
            ),
        );

        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "Bank Name",
                'fld' => 'BANK_NAME',
                'input_type' => 'text',
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Ifsc Code",
                'fld' => 'IFSC_CODE',
                'input_type' => 'text',
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Branch Name",
                'fld' => 'BRANCH',
                'input_type' => 'text',
                'dbtbl' => '',
            ),

        );


        $conditions = array();
        $actions = array();
        $actions = array(
            array(
                'fld' => 'edit',
                'icon' => '<i class="material-icons" title="Click here to Edit.">edit</i>',
                'fld_url' => 'bank_masters/edit/#id#'
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
        Session::put($formId . '_conditions', $conditions);
        $master = $custom_component_obj->getBankListData($formId, true);
        return view('bankmaster.index', compact('tableData', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'breadcrumbs', 'master', 'actions'))->withInput($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $model = 'BankMaster';
        if (count($request->all()) > 0) {
            $svdata = ['name' => $request->BANK_NAME, 'BANK_ID' => $request->BANK_ID, 'BANK_NAME' => $request->BANK_NAME,
                'BANKNAME_MANGAL' => $request->BANKNAME_MANGAL, 'IFSC_CODE' => $request->IFSC_CODE, 'MICR' => $request->MICR,
                'BANK_BRANCH_ID' => $request->BANK_BRANCH_ID, 'BRANCH' => $request->BRANCH, 'BRANCH_MANGAL' => $request->BRANCH_MANGAL,
                'BRANCH_ADDRESS' => $request->BRANCH_ADDRESS, 'IS_ACTIVE' => '1'
            ];

            $bankmasters = BankMaster::create($svdata);
            if ($bankmasters) {
                return redirect()->route('bank_masters.index')->with('message', 'Bank Create Successfully.');
            } else {
                return redirect()->route('bank_masters.index')->with('erro', 'Subject not successfully created');
            }

        }
        return view('bankmaster.create', compact('model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd('hii');
        if (count($request->all()) > 0) {

            if ($Subject) {
                return redirect()->route('bank_masters.index')->with('message', 'Subject successfully created');
            } else {
                return redirect()->route('bank_masters.index')->with('error', 'Failed! District not created');
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

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = crypt::decrypt($id);
        $model = 'BankMaster';
        $bankdata = BankMaster::findOrFail($id);
        return view('bankmaster.edit', compact('bankdata', 'model'));
    }


    public function update(Request $request, $id)
    {
        if (count($request->all()) > 0) {
            $svdata = ['name' => $request->BANK_NAME, 'BANK_ID' => $request->BANK_ID, 'BANK_NAME' => $request->BANK_NAME,
                'BANKNAME_MANGAL' => $request->BANKNAME_MANGAL, 'IFSC_CODE' => $request->IFSC_CODE, 'MICR' => $request->MICR,
                'BANK_BRANCH_ID' => $request->BANK_BRANCH_ID, 'BRANCH' => $request->BRANCH, 'BRANCH_MANGAL' => $request->BRANCH_MANGAL,
                'BRANCH_ADDRESS' => $request->BRANCH_ADDRESS, 'IS_ACTIVE' => '1'
            ];
            $bankmasters = BankMaster::where('id', $id)->update($svdata);
            if ($bankmasters) {
                return redirect()->route('bank_masters.index')->with('message', 'Bank update Successfully.');
            } else {
                return redirect()->route('bank_masters.index')->with('erro', 'Subject not update successfully created');
            }

        }
    }


}


