<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Exports\CompleteFeesSummaryExlExport;
use App\Models\Student;
use App\Models\VerificationMaster;
use Cache;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use Session;


class MasterReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function routes()
    {
        $routes = [];
        foreach (\Route::getRoutes()->getIterator() as $k => $route) {
            $routes[] = $routePath = $route->uri . " and Methed : " . $route->methods[0];
        }
        return [
            "status" => 1,
            "data" => $routes
        ];
    }


    public function banks_master()
    {
        $master = $this->getBanksMaster();
        return [
            "status" => 1,
            "data" => $master
        ];
    }


    public function completeFeesSummary()
    {
        $title = "Complete Fees Summary";
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
        $feeType = array('registration_fees' => 'registration_fees', 'practical_fees' => 'practical_fees', 'forward_fees ' => 'forward_fees');
        $custom_component_obj = new CustomComponent;
        $combo_name = 'course';
        $courses = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $aiCenters = $custom_component_obj->getAiCenters();

        return view('master_reports.completefeessummary', compact('breadcrumbs', 'aiCenters', 'courses', 'stream_id', 'gender_id', 'title', 'are_you_from_rajasthan', 'feeType'));
    }

    public function downloadcompleteFeesSummaryexcel(Request $request, $type = "xlsx")
    {

        $request->validate([
            'course' => 'required',
            'stream' => 'required',
            //    'feeType'  => 'required',

        ]);

        $obj = new CompleteFeesSummaryExlExport($request);
        $filename = 'completeFeesSummary.' . $type;
        Excel::store($obj, $filename);
        return Excel::download($obj, $filename);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function generate_barcode()
    {
        return view('master_reports.generate_barcode');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function completeFeesSummarydownload()
    {
        return response()->download('public/template/completeFeesSummaryformat.xlsx');
    }

    public function test()
    {

        $data = Student::select('id', 'enrollment', 'ai_code')->with(['exam_subject' => function ($query) {
            $query->select('student_id', 'enrollment');
        }])->limit(1)->get();


        return response()->json($data);


    }

    public function requiredDocList(Request $request)
    {
        $title = "Required Document listing";
        $table_id = "req_doc_list";
        $combo_name = $defaultPageLimit = config("global.defaultPageLimit");
        $verfication_label = $this->getVerificationLabel('1', true);
        $combo_name = 'adm_type';
        $adm_type = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'active_inactive';
        $status = $this->master_details($combo_name);
        //active_inactive

        $formId = ucfirst(str_replace(" ", "_", $title));
        $conditions = array();
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
                'status' => false,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloaduserPdf',
                'status' => false
            )
        );
        $master = array();
        $user_role = Session::get('role_id');
        $filters = array();
        $filters = array(
            array(
                "lbl" => "Admission Type",
                'fld' => 'adm_type',
                'input_type' => 'select',
                'options' => $adm_type,
                'placeholder' => 'Admission Type',
                'dbtbl' => 'verification_masters',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course',
                'dbtbl' => 'verification_masters',
            ),
            array(
                "lbl" => "Verfication Label",
                'fld' => 'main_document_id',
                'input_type' => 'select',
                'options' => $verfication_label,
                'placeholder' => 'Verfication Label',
                'dbtbl' => 'verification_masters',
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

        Session::put($formId . '_conditions', $conditions);
        $custom_component_obj = new CustomComponent;
        $verficationmasterdata = $custom_component_obj->getverificationMaster($formId, true);
        return view('master_reports.required_doc_list', compact('breadcrumbs', 'exportBtn', 'title', 'filters', 'verficationmasterdata', 'adm_type', 'course', 'status', 'verfication_label'));


    }

    public function addReqDoc(Request $request)
    {
        $verfication_label = $this->getVerificationLabel('1');
        $combo_name = 'adm_type';
        $adm_type = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'active_inactive';
        $status = $this->master_details($combo_name);
        if (count($request->all()) > 0) {
            $svData = $request->all();
            if (@$svData['field_id']) {
                $already = VerificationMaster::where('field_id', $svData['field_id'])->first();
                if (!empty($already)) {
                    return redirect()->back()->with('error', "This field id allready taken.");
                }
            }
            $data = VerificationMaster::create($svData);
            if (@$data) {
                return redirect()->route('requireddoclist')->with('message', "Master create successfully.");
            } else {
                return redirect()->route('requireddoclist')->with('error', "Something is wrong.");
            }


        }
        return view('master_reports.add_doc_req', compact('verfication_label', 'adm_type', 'course', 'status'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function editReqDoc(Request $request, $id = null)
    {
        $id = Crypt::decrypt($id);
        $verficationData = VerificationMaster::find($id);
        $verfication_label = $this->getVerificationLabel('1');
        $combo_name = 'adm_type';
        $adm_type = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'active_inactive';
        $status = $this->master_details($combo_name);
        if (count($request->all()) > 0) {


            $svData = ['adm_type' => $request->adm_type, 'course' => $request->course,
                'main_document_id' => $request->main_document_id,
                'field_id' => $request->field_id, 'form_filled_tbl' => $request->form_filled_tbl, 'form_filled_ref' => $request->form_filled_ref, 'field_name' => $request->field_name, 'status' => $request->status];
            if (@$svData['field_id'] && @$svData['field_id'] != @$verficationData->field_id) {
                $already = VerificationMaster::where('field_id', $svData['field_id'])->first();
                if (!empty($already)) {
                    return redirect()->back()->with('error', "This field id allready taken.");
                }
            }
            $data = VerificationMaster::where('id', $id)->update($svData);
            if (@$data) {
                return redirect()->route('requireddoclist')->with('message', "Master edit successfully.");
            } else {
                return redirect()->route('requireddoclist')->with('error', "Something is wrong.");
            }


        }
        return view('master_reports.edit_doc_req', compact('verfication_label', 'adm_type', 'course', 'status', 'verficationData'));

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
        //
    }


}
