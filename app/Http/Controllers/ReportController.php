<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Exports\MasterQuerieExcelExport;
use App\Helper\CustomHelper;
use App\models\MasterAdminDocument;
use App\models\MasterQuerieExcel;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use Session;
use Storage;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()
    {
        //$this->middleware('permission:report-list', ['only' => ['index','store']]);
        //$this->middleware('permission:report-create', ['only' => ['create','store']]);
        //$this->middleware('permission:report-show', ['only' => ['show']]);
        //$this->middleware('permission:report-edit', ['only' => ['edit','update']]);
        //$this->middleware('permission:report-delete', ['only' => ['destroy']]);
        //$this->middleware('permission:report-backupdb', ['only' => ['backupdb']]);
    }


    public function administrative_custom_report(Request $request)
    {
        $title = "Administrative Report";
        $table_id = "Administrative_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $tableData = array();

        $custom_component_obj = new CustomComponent;

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
            // array(
            // 	"label" => "Export Excel",
            // 	'url' => 'downloadApplicationExl',
            // 	'status' => false,
            // ),
            // array(
            // 	"label" => "Export PDF",
            // 	'url' => 'downloadApplicationPdf',
            // 	'status' => false
            // ),
        );


        $filters = array(
            array(
                "lbl" => "Link Text",
                'fld' => 'link_text',
                'input_type' => 'text',
                'search_type' => 'like',
                'placeholder' => "Link Text",
                'dbtbl' => '',
            )
        );

        $tableData = array(
            array(
                "lbl" => "Serial No.",
                'fld' => 'serial_number',
                'input_type' => 'text',
                'placeholder' => "Title",
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Particular",
                'fld' => 'link_text',
                'input_type' => 'text',
                'placeholder' => "Title",
                'dbtbl' => '',
            ),
        );
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();

        $conditions["is_link"] = 1;
        $conditions["status"] = 1;

        // if($isAdminStatus == false){
        // 	return redirect()->back()->with('error', 'Failed! Invalid Request!');
        // }


        if (in_array("administrative_custom_report", $permissions)) {

        } else {
            return redirect()->back()->with('error', 'Failed! Invalid Request!');
        }
        $actions = null;
        if (in_array("administrative_custom_report", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<img  title="Click here to Download Excel" height2="15%" width="15%" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAACcklEQVR4nGNgGAWjYBSMgmEJDOsNpfRqDX11aw0bdGsNN+vWGj5jGIzAvt6eRb9GX1uvxiBOt8Zwol6t0W69WsM3erVG/9HxQLuVQb3Uilev2thGt9YoDeRY3RrDI3o1ht+xOXbAPaBboSsIdmyNUb5ujdEi3Rqjq3o1Rn+JdeyAe0CPAofiwj7rA8jC3uv9jwxpD/isDyA99kY9UDsaA/8HXRJCBjCH4QOjHtBDCr3Obd0ooVO+qgpF3q7d6f+XH1/g8j07+gZXDBjWm/6/9eI23PC7r+7+N6gzgcsvPb4cLnfn5Z3/Rg1mg68YTZmfjhJCuUsKwOIevT7/f/7+CRb79+/f/+R5aYO3Hth7bT/cA5ceXwaLbbmwFS62+cKWwZ2Jvfp8//+AhjYIlKws///3318w+9P3T/8dO10Gtwf0ao3+zzowB27Jx28f4ez2LZ1Doxg1b7b+//LjSxTLrj+78d+gHpGpB20e0Ks1+m/VaofhgWfvn/03a7IaGh5YfXot3OGvP7+Bs2funz34k1Di3BRwUQkC3399/5+3tBBuIago9Z0QOHg9YNJo8f/uq3twC+YcnAcWP3r7GFzs2J0Tg9cDMw/MRil9bNscwOKhUyPgRSkIlK6sGHx5IGhy6P9ff37hbOvsvLwLJV+AMvqg8YBBncn/8w8vwB0IKoFMmyxRHTch4P+fv3/gahYfWzr4kpAehXjUAwMdA4OmItMb9UDAaAwMqSTkvd7/MMOIG9zFNbyuW2tkDJsLGNTD6+RMcOjVGnWAZmMG7QQHCYBRq95QRbfOOFS31rBNr8Zom16N4XNSDBgFo2AUjAKGIQEAkqNB3aFhJ4wAAAAASUVORK5CYII=" >',
                    'fld_url' => 'exportr/#id#'
                ),
                // array(
                // 	'fld' => 'view',
                // 	'icon' => '<i class="btn btn-default">Downlod PDF</i>',
                // 	'fld_url' => 'reporting_pdf/#id#'
                // )
            );
        }


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
        $master = MasterQuerieExcel::where($conditions)->orderBy(DB::raw('CAST(serial_number as UNSIGNED)'), 'DESC')->get();
        //$master = $custom_component_obj->getAdminstrativeCustomReport($formId);
        return view('report.administrative_custom_report', compact('master', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'tableData', 'breadcrumbs', 'actions'))->withInput($request->all());
    }

    public function administrative_custom_document_report(Request $request)
    {
        $title = "Administrative Document Report";
        $table_id = "Administrative_Document_Report";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $combo_name = 'admin_document_types';
        $documentype = $this->master_details($combo_name);

        $tableData = array();

        $custom_component_obj = new CustomComponent;

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
            // array(
            //  "label" => "Export Excel",
            //  'url' => 'downloadApplicationExl',
            //  'status' => false,
            // ),
            // array(
            //  "label" => "Export PDF",
            //  'url' => 'downloadApplicationPdf',
            //  'status' => false
            // ),
        );


        $filters = array(
            array(
                "lbl" => "Link Text",
                'fld' => 'link_text',
                'input_type' => 'text',
                'search_type' => 'like',
                'placeholder' => "Link Text",
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Document Type",
                'fld' => 'doc_type',
                'input_type' => 'select',
                'options' => $documentype,
                'search_type' => 'like',
                'placeholder' => "Document Type",
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Serial No.",
                'fld' => 'serial_number',
                'input_type' => 'text',
                'search_type' => 'like',
                'placeholder' => "Serial No.",
                'dbtbl' => '',
            )

        );

        $tableData = array(
            array(
                "lbl" => "Serial No.",
                'fld' => 'serial_number',
                'input_type' => 'text',
                'placeholder' => "Title",
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Particular",
                'fld' => 'link_text',
                'input_type' => 'text',
                'placeholder' => "Title",
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Document Type",
                'fld' => 'doc_type',
                'input_type' => 'select',
                'options' => $documentype,
                'placeholder' => 'Document Type',
                'dbtbl' => '',
            ),

        );
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();

        $conditions["is_link"] = 1;
        $conditions["status"] = 1;
        // if($isAdminStatus == false){
        // 	return redirect()->back()->with('error', 'Failed! Invalid Request!');
        // }

        if (in_array("administrative_custom_document_report", $permissions)) {

        } else {
            return redirect()->back()->with('error', 'Failed! Invalid Request!');
        }
        $actions = null;
        if (in_array("administrative_custom_document_report", $permissions)) {
            $actions = array(
                array(
                    'fld' => 'edit',
                    'icon' => '<i class="material-icons" title="Click here to Download.">arrow_downward</i>',
                    'fld_url' => 'downloaddocument/#document#'
                ),
                // array(
                //  'fld' => 'view',
                //  'icon' => '<i class="btn btn-default">Downlod PDF</i>',
                //  'fld_url' => 'reporting_pdf/#id#'
                // )
            );
        }


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

        $master = $custom_component_obj->getAdminstrativeCustomDocumentReport($formId);

        return view('report.administrative_custom_document_report', compact('master', 'exportBtn', 'formId', 'table_id', 'filters', 'title', 'tableData', 'breadcrumbs', 'actions'))->withInput($request->all());
    }

    public function oldadministrative_custom_report(Request $request)
    {

    }

    public function index()
    {
        $masterquerieexcel = MasterQuerieExcel::orderBy(DB::raw('CAST(serial_number as UNSIGNED)'), 'DESC')->get();
        return view('report.index', compact('masterquerieexcel'));
    }


    public function alldocumentlist()
    {
        $combo_name = 'yes_no_2';
        $yesno = $this->master_details($combo_name);
        $combo_name = 'admin_document_types';
        $documentype = $this->master_details($combo_name);

        $masteralldocumentlist = MasterAdminDocument::where('status', '=', 1)->orderBy(DB::raw('CAST(serial_number as UNSIGNED)'), 'DESC')->get();


        return view('report.alldocumentlist', compact('masteralldocumentlist', 'documentype', 'yesno'));
    }

    public function createalldocument()
    {
        ini_set('post_max_size', '64M');
        ini_set('upload_max_filesize', '64M');
        $combo_name = 'yes_no_2';
        $yesno = $this->master_details($combo_name);
        $combo_name = 'admin_document_types';
        $documentype = $this->master_details($combo_name);

        return view('report.createalldocument', compact('yesno', 'documentype'));
    }

    public function downloaddocument($path = null)
    {

        $download_path = (public_path('MasterAdminDocuments') . "/" . $path);
        // dd($download_path);
        Response::download($download_path);
        return (Response::download($download_path));
    }

    public function alldocumentstore(Request $request)
    {

        ini_set('post_max_size', '64M');
        ini_set('upload_max_filesize', '64M');

        $this->validate($request, [
            'status' => 'required|numeric',
            'doc_type' => 'required|numeric',
            'title' => 'required',
            'text' => 'required',
            'link_text' => 'required',
            'is_link' => 'required',
            'document' => 'required',

        ]);

        if (!empty($request->serial_number)) {
            $this->validate($request, [
                'serial_number' => 'unique:master_admin_documents',
            ]);
        }
        $imageName = time() . '.' . $request->document->extension();

        $request->document->move(public_path('MasterAdminDocuments'), $imageName);

        $alldocumentarray = ['status' => $request->status, 'doc_type' => $request->doc_type, 'title' => $request->title, 'text' => $request->text, 'link_text' => $request->link_text, 'is_link' => $request->is_link, 'document' => $imageName, 'serial_number' => $request->serial_number];

        $document = MasterAdminDocument::Create($alldocumentarray);
        if (empty($document->serial_number)) {
            $svdata['serial_number'] = $document->id + 100;
            $document = MasterAdminDocument::where('id', $document->id)->update($svdata);
        }

        if ($document) {
            return redirect()->route('alldocumentlist')->with('message', 'Document has been successfully submitted.');
        } else {
            return redirect()->route('alldocumentlist')->with('message', 'Document not submitted.');
        }


        $data = new MasterAdminDocument();
        if ($request->file('document')) {
            $imageName = time() . '.' . $request->document->extension();
            $request->document->move(public_path('alldocuments'), $imageName);
            $data['document'] = $filename;

            /* Store $imageName name in DATABASE from HERE */
        }
        $data->save();
        return back()
            ->with('success', 'You have successfully upload image.')
            ->with('image', $imageName);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $combo_name = 'yes_no_2';
        $yesno = $this->master_details($combo_name);

        return view('report.create', compact('yesno'));
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
        $this->validate($request, [
            'status' => 'required|numeric',
            'pdf' => 'required|numeric',
            'excel' => 'required|numeric',
            'title' => 'required',
            'text' => 'required',
            'link_text' => 'required',
            'is_link' => 'required',
            'serial_number' => 'unique:master_querie_excels,serial_number,' . $id,
        ]);
        $masterquerieexcel = MasterQuerieExcel::findOrFail($id);
        $input = $request->all();

        $masterquerieexcel->fill($input)->save();
        if (empty($request->serial_number)) {
            $svdata['serial_number'] = $id + 100;
            $masterquerieexcel->fill($svdata)->save();
        }
        if ($masterquerieexcel) {
            return redirect()->route('reports.index')->with('message', 'Master Queries successfully updated.');
        } else {
            return redirect()->route('reports.index')->with('error', 'Failed! Master Queries not updated.');
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
        $masterquerieexcel = MasterQuerieExcel::findOrFail(Crypt::decrypt($id));
        $combo_name = 'yes_no_2';
        $yesno = $this->master_details($combo_name);


        return view('report.edit', compact('masterquerieexcel', 'yesno'));
    }


    public function alldocumentedit($id)
    {
        $alldocumentedit = MasterAdminDocument::findOrFail(Crypt::decrypt($id));
        $combo_name = 'yes_no_2';
        $yesno = $this->master_details($combo_name);
        $combo_name = 'admin_document_types';
        $documentype = $this->master_details($combo_name);
        return view('report.alldocumentedit', compact('alldocumentedit', 'yesno', 'documentype'));
    }

    public function alldocumentupdate(Request $request)
    {

        $this->validate($request, [
            'status' => 'required|numeric',
            'doc_type' => 'required|numeric',
            'title' => 'required',
            'text' => 'required',
            'link_text' => 'required',
            'is_link' => 'required',

        ]);

        if (!empty($request->serial_number)) {
            $this->validate($request, [
                'serial_number' => 'unique:master_admin_documents,serial_number,' . $request->id,

            ]);

        }
        if ($request->hasFile('document')) {
            $imageName = time() . '.' . $request->document->extension();
            $request->document->move(public_path('MasterAdminDocuments'), $imageName);
            $alldocumentarray = ['status' => $request->status, 'doc_type' => $request->doc_type, 'title' => $request->title, 'text' => $request->text, 'link_text' => $request->link_text, 'is_link' => $request->is_link, 'document' => $imageName, 'serial_number' => $request->serial_number];

        } else {
            $alldocumentarray = ['status' => $request->status, 'doc_type' => $request->doc_type, 'title' => $request->title, 'text' => $request->text, 'link_text' => $request->link_text, 'is_link' => $request->is_link, 'serial_number' => $request->serial_number];
        }

        $document = MasterAdminDocument::where('id', $request->id)->update($alldocumentarray);
        if (empty($request->serial_number)) {
            $svdata['serial_number'] = $request->id + 100;
            MasterAdminDocument::where('id', $request->id)->update($svdata);
        }
        if ($document) {
            return redirect()->route('alldocumentlist')->with('message', 'Document has been successfully Updated.');
        } else {
            return redirect()->route('alldocumentlist')->with('message', 'Document not updated.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $MasterQuerieExcel = MasterQuerieExcel::where('id', Crypt::decrypt($id))->delete();
        return response()->json(['success' => 'Record successfully Deleted']); //
    }

    public function export($id)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
	
        $masterquerieexcel = MasterQuerieExcel::findOrFail(Crypt::decrypt($id));
        if (@$masterquerieexcel->title) {
            $fileName = $masterquerieexcel->title;
        }
        if (@$masterquerieexcel->title) {
            $fileName = $masterquerieexcel->link_text;
        }
        //Excel::store(new MasterQuerieExcelExport(Crypt::decrypt($id)), 'Super.csv' );
        Storage::disk('public');
        //Excel::store(new MasterQuerieExcelExport(Crypt::decrypt($id)), $fileName.'_' . date("d-m-Y-h-i-s") .'.xlsx');
        return Excel::download(new MasterQuerieExcelExport(Crypt::decrypt($id)), $fileName . '_' . date("d-m-Y") . '.xlsx');
    }

    public function reporting_pdf($id)
    {


        $masterquerieexcel = MasterQuerieExcel::findOrFail(Crypt::decrypt($id));
        if (@$masterquerieexcel->title) {
            $fileName = $masterquerieexcel->title;
        }
        if (@$masterquerieexcel->title) {
            $fileName = $masterquerieexcel->link_text;
        }
        Excel::store(new MasterQuerieExcelExport(Crypt::decrypt($id)), $fileName . '_' . date("d-m-Y-h-i-s") . '.pdf');
        return Excel::download(new MasterQuerieExcelExport(Crypt::decrypt($id)), $fileName . '_' . date("d-m-Y") . '.pdf');


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        ini_set('post_max_size', '64M');
        ini_set('upload_max_filesize', '64M');

        $this->validate($request, [
            'status' => 'required|numeric',
            'pdf' => 'required|numeric',
            'excel' => 'required|numeric',
            'title' => 'required',
            'text' => 'required',
            'link_text' => 'required',
            'is_link' => 'required',
            'serial_number' => 'required|numeric',

        ]);
        if (!empty($request->serial_number)) {
            $this->validate($request, [
                'serial_number' => 'unique:master_querie_excels'
            ]);
        }
        $masterquerieexcel = MasterQuerieExcel::create($request->all());
        if (empty($masterquerieexcel->serial_number)) {
            $svdata['serial_number'] = $masterquerieexcel->id + 100;
            $masterquerieexcel = MasterQuerieExcel::where('id', $masterquerieexcel->id)->update($svdata);
        }
        if ($masterquerieexcel) {
            return redirect()->route('reports.index')->with('message', 'Master Queries successfully created');
        } else {
            return redirect()->route('reports.index')->with('error', 'Failed! Master Queries not created');
        }
    }

    public function backupdblisting(Request $request)
    {
        if (!empty($request->tablename)) {
            $tablename = $request->tablename;
            //ENTER THE RELEVANT INFO BELOW
            $mysqlHostName = Config::get('global.DB_HOST');;
            $mysqlUserName = Config::get('global.DB_USERNAME');
            $mysqlPassword = Config::get('global.DB_PASSWORD');
            $DbName = Config::get('global.DB_DATABASE');
            $backup_name = "mybackup.sql";
            $tables = array($tablename); //here your tables...
            foreach ($tables as $table) {
                foreach ($table as $tName) {
                    $result1[] = $tName;
                }
            }
            $connect = new \PDO("mysql:host=$mysqlHostName;dbname=$DbName;charset=utf8", "$mysqlUserName", "$mysqlPassword", array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
            $get_all_table_query = "SHOW TABLES";
            $statement = $connect->prepare($get_all_table_query);
            $statement->execute();
            $result = $statement->fetchAll();


            $output = '';
            foreach ($result1 as $table) {
                $show_table_query = "SHOW CREATE TABLE " . $table . "";
                $statement = $connect->prepare($show_table_query);
                $statement->execute();
                $show_table_result = $statement->fetchAll();

                foreach ($show_table_result as $show_table_row) {
                    $output .= "\n\n" . $show_table_row["Create Table"] . ";\n\n";
                }

                $select_query = "SELECT * FROM " . $table . "";
                $statement = $connect->prepare($select_query);
                $statement->execute();
                $total_row = $statement->rowCount();

                for ($count = 0; $count < $total_row; $count++) {
                    $single_result = $statement->fetch(\PDO::FETCH_ASSOC);
                    $table_column_array = array_keys($single_result);
                    $table_value_array = array_values($single_result);
                    $output .= "\nINSERT INTO $table (";
                    $output .= "" . implode(", ", $table_column_array) . ") VALUES (";
                    $output .= "'" . implode("','", $table_value_array) . "');\n";
                }
            }

            $file_name = 'database_backup_on_' . date('y-m-d') . '.sql';
            $file_handle = fopen($file_name, 'w+');
            fwrite($file_handle, $output);
            fclose($file_handle);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file_name));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_name));
            ob_clean();
            flush();
            readfile($file_name);
            unlink($file_name);
        } else {
            $tables = DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                foreach ($table as $tName) {
                    $result[] = $tName;
                }
            }
            return view('report.backupdblisting', ['result' => $result]);
        }
    }


    public function querysqldump(Request $request)
    {
        if (count($request->all()) > 0) {
            $inputs = $request->all();


            $query = $inputs['query'];
            $table = $inputs['tablename'];

            //ENTER THE RELEVANT INFO BELOW
            $mysqlHostName = Config::get('global.DB_HOST');;
            $mysqlUserName = Config::get('global.DB_USERNAME');
            $mysqlPassword = Config::get('global.DB_PASSWORD');
            $DbName = Config::get('global.DB_DATABASE');


            $connect = new \PDO("mysql:host=$mysqlHostName;dbname=$DbName;charset=utf8", "$mysqlUserName", "$mysqlPassword", array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
            $output = '';
            $statement = $connect->prepare($query);
            $statement->execute();
            $total_row = $statement->rowCount();

            if ($total_row > 0) {
                for ($count = 0; $count < $total_row; $count++) {
                    $single_result = $statement->fetch(\PDO::FETCH_ASSOC);
                    $table_column_array = array_keys($single_result);
                    $table_value_array = array_values($single_result);
                    $output .= "\nINSERT INTO $table (";
                    $output .= "" . implode(", ", $table_column_array) . ") VALUES (";
                    $output .= "'" . implode("','", $table_value_array) . "');\n";
                }
            } else {
                $output .= "No Data Found";
            }

            $file_name = 'query_database_backup_on_' . date('y-m-d') . '.sql';
            $file_handle = fopen($file_name, 'w+');
            fwrite($file_handle, $output);
            fclose($file_handle);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file_name));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_name));
            ob_clean();
            flush();
            readfile($file_name);
            unlink($file_name);
        } else {
            $tables = DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                foreach ($table as $tName) {
                    $result[$tName] = $tName;
                }
            }
            return view('report.querysqldump', ['result' => $result]);
        }
    }

    public function backupdb()
    {
        //ENTER THE RELEVANT INFO BELOW
        //$result = DB::table('databasetables')->first();
        //ENTER THE RELEVANT INFO BELOW
        $mysqlHostName = Config::get('global.DB_HOST');;
        $mysqlUserName = Config::get('global.DB_USERNAME');
        $mysqlPassword = Config::get('global.DB_PASSWORD');
        $DbName = Config::get('global.DB_DATABASE');
        $file_name = 'database_backup_on_' . date('y-m-d') . '.sql';
        $queryTables = \DB::select(\DB::raw('SHOW TABLES'));
        foreach ($queryTables as $table) {
            foreach ($table as $tName) {
                $tables[] = $tName;

            }
        }
        // $tables  = array("users","products","categories"); //here your tables...
        $connect = new \PDO("mysql:host=$mysqlHostName;dbname=$DbName;charset=utf8", "$mysqlUserName", "$mysqlPassword", array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $get_all_table_query = "SHOW TABLES";
        $statement = $connect->prepare($get_all_table_query);
        $statement->execute();
        $result = $statement->fetchAll();
        $output = '';
        foreach ($tables as $table) {
            $show_table_query = "SHOW CREATE TABLE " . $table . "";
            $statement = $connect->prepare($show_table_query);
            $statement->execute();
            $show_table_result = $statement->fetchAll();

            foreach ($show_table_result as $show_table_row) {
                $output .= "\n\n" . $show_table_row["Create Table"] . ";\n\n";
            }
            $select_query = "SELECT * FROM " . $table . "";
            $statement = $connect->prepare($select_query);
            $statement->execute();
            $total_row = $statement->rowCount();

            for ($count = 0; $count < $total_row; $count++) {
                $single_result = $statement->fetch(\PDO::FETCH_ASSOC);
                $table_column_array = array_keys($single_result);
                $table_value_array = array_values($single_result);
                $output .= "\nINSERT INTO $table (";
                $output .= "" . implode(", ", $table_column_array) . ") VALUES (";
                $output .= "'" . implode("','", $table_value_array) . "');\n";
            }
        }

        $file_handle = fopen($file_name, 'w+');
        fwrite($file_handle, $output);
        fclose($file_handle);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file_name));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_name));
        ob_clean();
        flush();
        readfile($file_name);
        unlink($file_name);
    }

    public function resultsedit(Request $request)
    {
        $title = "Edit Results";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
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
        $filters = array(
            array(
                "lbl" => "Enrollment",
                'fld' => 'enrollment',
                'input_type' => 'text',
                'placeholder' => "Enrollment Number",
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => "course",
                'dbtbl' => '',
            ),
            array(
                "lbl" => "Subject Code",
                'fld' => 'subject_id',
                'input_type' => 'text',
                'placeholder' => "subjects",
                'dbtbl' => '',
            )
        );

        $exportBtn = array();
        return view('report.updateresult', compact('title', 'breadcrumbs', 'filters', 'exportBtn'));
    }

    public function alldocumentdestory($id)
    {
        $masterdmindocument = MasterAdminDocument::findOrFail(Crypt::decrypt($id));
        $download_path = (public_path('MasterAdminDocuments') . "/" . $masterdmindocument->document);
        if (!File::exists($download_path)) {
            File::delete($download_path);
        }
        $MasterQuerieExcel = MasterAdminDocument::where('id', Crypt::decrypt($id))->delete();
        return response()->json(['success' => 'Record successfully Deleted']); //
    }
}
	
