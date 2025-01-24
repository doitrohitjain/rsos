<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Exports\LogsExlExport;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Session;

class LogController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:logslisting', ['only' => ['logslisting']]);
        $this->middleware('permission:logdebug', ['only' => ['logDebug']]);
    }

    public function logslisting(Request $request)
    {
        //dd($request->all());
        $title = "Logs Details";
        $table_id = "Logs_Details";
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
                'url' => 'downloadlogslistingexcel',
                'status' => true,
            )
        );
        $tableData = array(
            array(
                "lbl" => "Sr.No.",
                'fld' => 'srno'
            ),
            array(
                "lbl" => "User Id",
                'fld' => 'user_id',
                'input_type' => 'text',
                'placeholder' => "user id",
                'dbtbl' => 'logs',
            ),
            array(
                "lbl" => "Log Date",
                'fld' => 'log_date',
                'input_type' => 'text',
                'placeholder' => "Log Dates",
                'dbtbl' => 'logs'
            ),
            array(
                "lbl" => "Table Name",
                'fld' => 'table_name',
                'input_type' => 'text',
                'placeholder' => "Table Name",
                'dbtbl' => 'logs'
            ),
            array(
                "lbl" => "Course Type",
                'fld' => 'log_type',
                'input_type' => 'text',
                'placeholder' => "Log Types",
                'dbtbl' => 'logs'
            ),
            array(
                "lbl" => "All Data",
                'fld' => 'data',
                'input_type' => 'text',
                'placeholder' => "All Data",
                'dbtbl' => 'logs'
            )
        );

        $conditions = array();
        if (!empty($request->all())) {
            $conditions = $request->all();
        }

        //$old_session_codition = Session::get($formId. '_conditions');
        //if(isset($old_session_codition) && !empty($old_session_codition['data'])){
        //$conditions['data'] =  $old_session_codition['data'];
        //}

        Session::put($formId . '_conditions', $conditions);
        $custom_component_obj = new CustomComponent;
        $master = $custom_component_obj->getLogsData($formId);

        return view('activitylog.logslisting', compact('master', 'title', 'breadcrumbs', 'exportBtn', 'tableData'))->withInput($request->all());
    }

    public function downloadlogslistingexcel(Request $request, $type = "xlsx")
    {
        $application_exl_data = new LogsExlExport;
        $filename = 'Logs_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }


    public function logDebug()
    {
        $title = "Log Debug";
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => ""
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );
        return view("logs.logdebug", compact('title', 'breadcrumbs'));

    }

    public function downloadlog()
    {
        $file_path = storage_path('logs\laravel.log');
        if (file_exists($file_path)) {
            return response()->download($file_path);
        } else {
            return redirect()->back()->with('message', 'File not Exists');
        }
    }

    public function deletelog()
    {
        $file_path = storage_path('logs\laravel.log');
        File::delete($file_path);
        return back()->with('message', "Log Delete Successfully");
    }

    public function viewlog()
    {
        $file_path = storage_path() . '/logs/laravel.log';

        if (file_exists($file_path)) {
            $logs = fopen($file_path, "r");
            return $logs;
        } else {
            return redirect()->back()->with('message', 'File not Exists');
        }
    }


}
