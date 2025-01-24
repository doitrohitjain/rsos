<?php

namespace App\Http\Controllers;

use App\Component\BookRequirementCustomComponent;
use App\Component\CustomComponent;
use App\Exports\BookRequirementExlExport;
use App\Exports\BookrequirementExportExcel;
use App\Exports\BookstockExportExcel;
use App\Models\BookVolumeMaster;
use App\Models\PublicationBook;
use Auth;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Response;
use Session;
use Validator;

class BookRequirementController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:books_requrement', ['only' => ['index']]);
        $this->middleware('permission:add_books_requrement', ['only' => ['bookadd']]);
        $this->middleware('permission:edit_books_requrement', ['only' => ['bookedit']]);

    }

    public function index(Request $request)
    {
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'book_publication_volumes';
        $book_publication_volumes = $this->master_details($combo_name);
        $subject_list = $this->subjectList();
        $book_dep_role = Config::get("global.publication_department");
        $aicentar_role = Config::get("global.aicenter_id");
        $BookRequirementCustomComponent = new BookRequirementCustomComponent();
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $title = "Books Requirement";
        $table_id = "Books_Requrement";
        $role_id = Session::get('role_id');
        $formId = ucfirst(str_replace(" ", "_", $title));
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $exportBtn = array();
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
        if ($isAdminStatus == true || $role_id == $book_dep_role || $role_id == $aicentar_role) {
            $exportBtn = array(
                array(
                    "label" => "Book requirement Export PDF",
                    'url' => 'letter_twelve_generate_report_pdf',
                    'status' => false,

                ), array(
                    "label" => "Book requirement Excel Export ",
                    'url' => 'downloadbookrequirementExportExcel',
                    'status' => true
                )
            , array(
                    "label" => " Book Stock Excel Export",
                    'url' => 'downloadbookrequirementExportExcels',
                    'status' => true
                )
            );
        }

        $filters = array(
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course,
                'placeholder' => 'Course',
                'dbtbl' => 'publication_books',

            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subject_list,
                'placeholder' => 'Subject',
                'dbtbl' => 'publication_books',

            ),
            array(
                "lbl" => "Volume",
                'fld' => 'subject_volume_id',
                'input_type' => 'select',
                'options' => $book_publication_volumes,
                'placeholder' => 'Volume',
                'dbtbl' => 'publication_books',

            ),


        );

        if ($isAdminStatus == true || $role_id == $book_dep_role) {
            $filters[] = array(
                "lbl" => "Ai Center",
                'fld' => 'ai_code',
                'input_type' => 'select',
                'options' => $aiCenters,
                'placeholder' => 'Ai Center',
                'dbtbl' => 'aicenter_details',

            );

        }

        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        $conditions = array();
        $current_exam_year = Config::get("global.current_books_requirement_exam_year");
        $current_exam_month = Config::get("global.current_books_requirement_exam_month");
        $conditions["publication_books.exam_year"] = $current_exam_year;
        $conditions["publication_books.exam_month"] = $current_exam_month;

        if ($isAdminStatus == false) {
            if ($book_dep_role != $role_id) {
                $role_id = @Session::get('role_id');
                $ai_code = Session::get('ai_code');
                $conditions["publication_books.ai_code"] = @$ai_code;
            }
        }


        if ($request->all()) {

            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }

        }

        Session::put($formId . '_conditions', $conditions);
        $data = $BookRequirementCustomComponent->getBooksRequrementData($formId, true);
        //'inputs','sortingField',
        return view('books_requrement.index', compact('data', 'breadcrumbs', 'exportBtn', 'title', 'filters', 'subject_list', 'book_publication_volumes'));
    }

    public function bookadd(Request $request)
    {
        $title = "Create Books Requirement";
        $table_id = "Books_Requrement";
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
        $model = "BooksRequrement";
        $book_dep_role = Config::get("global.publication_department");
        $custom_component_obj = new CustomComponent;
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        if (count($request->all()) > 0) {
            $responses = $this->BooksRequrementDetailsValidation($request);
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
                $user_id = Auth::user()->id;
                $role_id = Session::get('role_id');
                $aicenter_role = Config::get("global.aicenter_id");
                $developer_admin = Config::get("global.developer_admin");
                if ($role_id == $aicenter_role) {
                    $ai_code = Session::get('ai_code');
                } elseif ($role_id == $developer_admin || $role_id == $book_dep_role) {
                    $ai_code = $request->ai_code;
                }

                $bookRequirementCustomComponent = new BookRequirementCustomComponent();
                $dataesxists = $bookRequirementCustomComponent->bookDataAllReadyExists(@$request->course, @$request->subject_id, @$ai_code, null, @$request->subject_volume_id, null);
                if (count(@$dataesxists) != 0) {
                    return redirect()->back()->with('error', 'Selected Combination already Exists.');
                }

                $current_exam_year = Config::get("global.current_books_requirement_exam_year");
                $current_exam_month = Config::get("global.current_books_requirement_exam_month");
                $booksrequrementarray = ['exam_year' => $current_exam_year,
                    'exam_month' => $current_exam_month,
                    'course' => $request->course,
                    'subject_id' => $request->subject_id,
                    'subject_volume_id' => $request->subject_volume_id,
                    'hindi_auto_student_count' => $request->hindi_auto_student_count,
                    'english_auto_student_count' => $request->english_auto_student_count,
                    'hindi_last_year_book_stock_count' => $request->hindi_last_year_book_stock_count,
                    'english_last_year_book_stock_count' => $request->english_last_year_book_stock_count,
                    'hindi_required_book_count' => $request->hindi_required_book_count,
                    'english_required_book_count' => $request->english_required_book_count,
                    'last_update_by_user_id' => $user_id,
                    'user_id' => $user_id,
                    'ai_code' => $ai_code,];

                $publicationBookDetail = PublicationBook::create($booksrequrementarray);
                if (!empty($publicationBookDetail)) {
                    return redirect()->route('booklisting')->with('message', 'Your Data successfully Create.');
                }
                $customerrors = implode(",", @$responseFinal[$k]['customerrors']);
                return redirect()->back()->withErrors($responseFinal['validator'])->withInput($request->all());
            }
        }
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $book_publication_volumes = array();
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $subject_list = $this->subjectList();
        $subject = array();
        return view('books_requrement.bookadd', compact('breadcrumbs', 'isAdminStatus', 'model', 'course', 'subject', 'book_publication_volumes', 'aiCenters'));
    }

    public function generate_report_pdf(Request $request)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $aiCenters = null;
        return view('books_requrement.generate_report_pdf');
    }

    public function letter_twelve_generate_report_pdf(Request $request, $input_ai_code = 0)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        $current_exam_year = Config::get("global.current_books_requirement_exam_year");
        $current_exam_month = Config::get("global.current_books_requirement_exam_month");
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $aicodewxists = $custom_component_obj->getAiCenters($input_ai_code);
        if (count($aicodewxists) == 0) {
            return redirect()->route('booklisting')->with('error', 'Aicode Not found.');
        }
        $combo_name = 'book_publication_volumes';
        $book_publication_volumes = $this->master_details($combo_name);
        $combo_name = 'hindi_course_lable';
        $hindi_course_lable = $this->master_details($combo_name);
        $bookRequirementCustomComponent = new BookRequirementCustomComponent();
        $tempResult = $bookRequirementCustomComponent->getDataForPdfBooksRequrementData($input_ai_code);
        $finalResult = null;

        if (@$tempResult) {
            foreach (@$tempResult as $key => $item) {
                if (@$item->ai_ai_code) {
                    if (@$item->course == 10) {
                        $finalResult[$item->ai_ai_code][$item->course][] = $item;
                    }
                    if (@$item->course == 12) {
                        $finalResult[$item->ai_ai_code][$item->course][] = $item;
                    }
                }
            }
        }
        $dd = null;
        if (@$aiCenters) {
            if ($input_ai_code == 0) {
                foreach (@$aiCenters as $ai_code => $aiCenter) {
                    if (@$finalResult[$ai_code][10]) {
                    } else {
                        $finalResult[$ai_code][10] = false;
                    }
                    if (@$finalResult[$ai_code][12]) {
                    } else {
                        $finalResult[$ai_code][12] = false;
                    }
                }
            } else {
                if (@$finalResult[$input_ai_code][10]) {
                } else {
                    $finalResult[$input_ai_code][10] = false;
                }
                if (@$finalResult[$input_ai_code][12]) {
                } else {
                    $finalResult[$input_ai_code][12] = false;
                }
            }

        }

        //path for save pdf public\files\books_requirement\125\2\12 and file name should be ai_code_12
        return view('books_requrement.letter_twelve_generate_report_pdf', compact('hindi_course_lable', 'finalResult', 'book_publication_volumes', 'aiCenters'));
        //dyanmic years,border css

        $pdf = PDF::loadView('books_requrement.letter_twelve_generate_report_pdf', compact('finalResult', 'hindi_course_lable', 'book_publication_volumes', 'aiCenters'));
        $pdf->setOption('footer-right', 'Page [page] of [toPage]');
        $path = public_path("files/books_requirement/$current_exam_year/$current_exam_month/12/" . $input_ai_code . '_12' . date('d-m-Y-H-i-s') . '.pdf');

        $pdf->save($path, $pdf, true);
        return (Response::download($path));


    }

    /*public function letter_twelve_generate_report_pdf(Request $request,$input_ai_code=0) {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        $current_exam_year = Config::get("global.current_books_requirement_exam_year");
        $current_exam_month = Config::get("global.current_books_requirement_exam_month");
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        $aicodewxists=$custom_component_obj->getAiCenters($input_ai_code);
        if(count($aicodewxists) == 0){
            return redirect()->route('booklisting')->with('error', 'Aicode Not found.');
        }
        $combo_name = 'book_publication_volumes';$book_publication_volumes = $this->master_details($combo_name);
        $combo_name = 'hindi_course_lable';$hindi_course_lable = $this->master_details($combo_name);
        $bookRequirementCustomComponent = new BookRequirementCustomComponent();
        $tempResult = $bookRequirementCustomComponent->getDataForPdfBooksRequrementData($input_ai_code);
        $finalResult = null;





        if(@$tempResult){
            foreach(@$tempResult as $key => $item){
                if(@$item->ai_ai_code){
                    if(@$item->course == 10){
                        $finalResult[$item->ai_ai_code][$item->course][] = $item;
                    }
                    if(@$item->course == 12){
                        $finalResult[$item->ai_ai_code][$item->course][] = $item;
                    }
                }
            }
        }
        $dd = null;
        if(@$aiCenters){
            if($input_ai_code == 0){
                foreach(@$aiCenters as $ai_code => $aiCenter){
                    if(@$finalResult[$ai_code][10]){
                    }else{
                        $finalResult[$ai_code][10] = false;
                    }
                    if(@$finalResult[$ai_code][12]){
                    }else{
                        $finalResult[$ai_code][12] = false;
                    }
                }
            }else{
                if(@$finalResult[$input_ai_code][10]){
                }else{
                    $finalResult[$input_ai_code][10] = false;
                }
                if(@$finalResult[$input_ai_code][12]){
                }else{
                    $finalResult[$input_ai_code][12] = false;
                }
            }

        }

        //path for save pdf public\files\books_requirement\125\2\12 and file name should be ai_code_12
        //return view('books_requrement.letter_twelve_generate_report_pdf',compact('hindi_course_lable','finalResult','book_publication_volumes','aiCenters'));
        //dyanmic years,border css


        $pdf =  PDF::loadView('books_requrement.letter_twelve_generate_report_pdf',compact('finalResult','hindi_course_lable','book_publication_volumes','aiCenters'));
        $pdf->setOption('footer-right', 'Page [page] of [toPage]');
        $path = public_path("files/books_requirement/$current_exam_year/$current_exam_month/12/".$input_ai_code.'_12'.date('d-m-Y-H-i-s').'.pdf');

        $pdf->save($path,$pdf,true);
        return( Response::download($path));



        }*/

    public function letter_thirteen_generate_report_pdf(Request $request, $ai_code = 0)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        $bookRequirementCustomComponent = new BookRequirementCustomComponent();
        $result = $bookRequirementCustomComponent->getDataForPdfBooksRequrementData($ai_code);
        //path for save pdf public\files\books_requirement\125\2\13 and file name should be ai_code_13
        return view('books_requrement.letter_twelve_generate_report_pdf', compact('result'));
    }

    public function bookdelete($id)
    {
        $ids = Crypt::decrypt($id);
        $user = PublicationBook::where('id', $ids)->delete();
        if ($user) {
            return redirect()->route('booklisting')->with('message', 'Successfully Deleted');
        } else {
            return redirect()->route('booklisting')->with('error', 'Failed! User not Deleted');
        }

    }

    public function bookedit(Request $request, $id = null)
    {
        $id = Crypt::decrypt($id);
        $publicationbook_data = PublicationBook::find($id);
        $bookRequirementCustomComponent = new BookRequirementCustomComponent();
        $title = "Update Books Requirement";
        $table_id = "Books_Requrement";
        $current_exam_year = Config::get("global.current_books_requirement_exam_year");
        $current_exam_month = Config::get("global.current_books_requirement_exam_month");
        $book_dep_role = Config::get("global.publication_department");
        $formId = ucfirst(str_replace(" ", "_", $title));
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => ''
            ),
            array(
                "label" => @$title,
                'url' => ''
            )
        );

        $model = "BooksRequrement";
        $subject_list = $this->subjectList($publicationbook_data->course);
        $subject = $subject_list;
        $objpractical = new  PracticalController();
        if (count($request->all()) > 0) {
            $responses = $this->BooksRequrementDetailsValidation($request);
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
                $user_id = Auth::user()->id;
                $role_id = Session::get('role_id');
                $aicenter_role = Config::get("global.aicenter_id");
                $developer_admin = Config::get("global.developer_admin");
                if ($role_id == $aicenter_role) {
                    $ai_code = Session::get('ai_code');
                } elseif ($role_id == $developer_admin || $role_id == $book_dep_role) {
                    $ai_code = $request->ai_code;
                }
                $conditions = [
                    'exam_year' => $current_exam_year,
                    'exam_month' => $current_exam_month,
                    'ai_code' => $ai_code,
                    'course' => $request->course,
                    'subject_id' => $request->subject_id,
                    'subject_volume_id' => $request->subject_volume_id,
                ];

                $booksrequrementarray = [
                    'course' => $request->course,
                    'subject_id' => $request->subject_id,
                    'subject_volume_id' => $request->subject_volume_id,
                    'hindi_auto_student_count' => $request->hindi_auto_student_count,
                    'english_auto_student_count' => $request->english_auto_student_count,
                    'hindi_last_year_book_stock_count' => $request->hindi_last_year_book_stock_count,
                    'english_last_year_book_stock_count' => $request->english_last_year_book_stock_count,
                    'hindi_required_book_count' => $request->hindi_required_book_count,
                    'english_required_book_count' => $request->english_required_book_count,
                    'last_update_by_user_id' => $user_id,
                    'user_id' => $user_id,
                    'ai_code' => $ai_code];

                $objpractical->slotLogs('publication_books', $id);

                $publicationBookDetail = PublicationBook::where('id', $id)->update($booksrequrementarray);

                if (!empty($publicationBookDetail)) {
                    return redirect()->route('booklisting')->with('message', 'Your Data Edit successfully.');
                }
                $customerrors = implode(",", @$responseFinal[$k]['customerrors']);
                return redirect()->back()->withErrors($responseFinal['validator'])->withInput($request->all());
            }
        }
        $combo_name = 'course';
        $course = $this->master_details($combo_name);

        $condtions = ['combo_name' => 'book_publication_volumes'];
        $vloumeData = BookVolumeMaster::where('subject_id', $publicationbook_data->subject_id)->pluck('medium', 'volume');
        $volume = array_keys($vloumeData->toArray());
        $subjectmedium = implode(',', array_unique(array_values($vloumeData->toArray())));
        $book_publication_volumes = DB::table('masters')->where('status', 1)->where($condtions)->whereIn('option_id', $volume)->orderBy("option_val")->get()->pluck('option_val', 'option_id');
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        return view('books_requrement.bookedit', compact('breadcrumbs', 'publicationbook_data', 'model', 'course', 'subject', 'book_publication_volumes', 'aiCenters', 'subjectmedium'));
    }

    public function downloadbookrequirementExportExcel(Request $request, $type = "xlsx", $datatype = null)
    {
        $application_exl_data = new BookrequirementExportExcel;
        $filename = ' Book_Requirement' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function downloadbookrequirementExportExcels(Request $request, $type = "xlsx", $datatype = null)
    {
        $application_exl_data = new BookstockExportExcel;
        $filename = ' Book_Requirement' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function devloper_auto_retotaling_books_publication(Request $request)
    {
        $current_exam_year = Config::get("global.current_books_requirement_exam_year");

        $masterQ1 = 'SELECT `rs_students`.`ai_code`, `rs_students`.`exam_month`, `rs_students`.`course`, `rs_applications`.`medium`, `rs_exam_subjects`.`subject_id`, count(*) AS AGGREGATE, case when `rs_applications`.`medium` = 1 then concat("update rs_publication_books set hindi_auto_student_count = ", count(*) ," where ai_code=\'" , `rs_students`.`ai_code` ,	"\' and exam_month=" , `rs_students`.`exam_month` ,	" and course=" , `rs_students`.`course` ,	" and " , "subject_id=" , `rs_exam_subjects`.`subject_id` ,";") when `rs_applications`.`medium` = 2 then concat("update rs_publication_books set english_auto_student_count = ", count(*) ," where ai_code=\'" , `rs_students`.`ai_code` ,	"\' and exam_month=" , `rs_students`.`exam_month` ,	" and course=" , `rs_students`.`course` ,	" and " , "subject_id=" , `rs_exam_subjects`.`subject_id` ,";") end as queryfinal FROM `rs_students` INNER JOIN `rs_exam_subjects` ON `rs_exam_subjects`.`student_id` = `rs_students`.`id` INNER JOIN `rs_applications` ON `rs_applications`.`student_id` = `rs_students`.`id` WHERE `rs_applications`.`is_ready_for_verifying` in (1) AND `rs_students`.`exam_year` = ' . $current_exam_year . '  AND `rs_exam_subjects`.`exam_year` = ' . $current_exam_year . ' AND `rs_exam_subjects`.`exam_month` = 1 AND `rs_exam_subjects`.`deleted_at` IS NULL  AND `rs_students`.`deleted_at` IS NULL AND `rs_applications`.`deleted_at` IS NULL AND `rs_students`.`exam_month` = 1 GROUP BY `rs_students`.`ai_code`, `rs_students`.`exam_month`, `rs_students`.`course`, `rs_applications`.`medium`, `rs_exam_subjects`.`subject_id` ORDER BY `rs_students`.`ai_code`, `rs_students`.`course`, `rs_applications`.`medium`, `rs_exam_subjects`.`subject_id`;';
        // print_r($masterQ1);die;
        $masterQ2 = 'SELECT pb.id, pb.ai_code, pb.course, pb.subject_id, pb.subject_volume_id, pb.hindi_auto_student_count, pb.hindi_last_year_book_stock_count, pb.hindi_required_book_count, pb.english_auto_student_count, pb.english_last_year_book_stock_count, pb.english_required_book_count, concat("update rs_publication_books set hindi_required_book_count =  ",   pb.hindi_auto_student_count - pb.hindi_last_year_book_stock_count , " ,  english_required_book_count =  ",    pb.english_auto_student_count - pb.english_last_year_book_stock_count   , " where id = ", pb.id ,";") as queryfinal, pb.* FROM rs_publication_books pb WHERE pb.exam_year = ' . $current_exam_year . ' AND pb.deleted_at IS NULL  ORDER BY pb.ai_code, pb.course, pb.subject_id, pb.subject_volume_id;';
        // print_r($masterQ2);die;
        $oneresults = DB::select($masterQ1);
        $tempStatus = true;
        $tempQueryWhichRun = [];
        $tempTwoQueryWhichRun = [];


        $qCourseUpdate1 = "UPDATE rs_publication_books SET course = 10 WHERE subject_id IN ( SELECT id FROM rs_subjects WHERE course = 10);";
        $qCourseUpdate2 = "UPDATE rs_publication_books SET course = 12 WHERE subject_id IN (SELECT id FROM rs_subjects WHERE course = 12);";
        DB::select($qCourseUpdate1);
        DB::select($qCourseUpdate2);

        if ($tempStatus) {
            foreach ($oneresults as $k => $v) {
                if (@$v->queryfinal) {

                    $tempQueryWhichRun[] = $queryfinal = $v->queryfinal; //hindi_auto_student_count english_auto_student_count
                    DB::select($queryfinal);
                    // echo $queryfinal . " one run";die;
                }
            }
            // dd($tempQueryWhichRun);
            $tworesults = DB::select($masterQ2);
            // dd($tworesults);
            foreach ($tworesults as $k => $v) {
                $tempQuery = "UPDATE rs_publication_books SET hindi_required_book_count = " . ($v->hindi_auto_student_count - $v->hindi_last_year_book_stock_count) . ", english_required_book_count = " . ($v->english_auto_student_count - $v->english_last_year_book_stock_count) . " WHERE id = " . $v->id . ";";
                // dd($tempQuery);
                $v->queryfinal = $tempQuery;
                if (@$v->queryfinal) {
                    $tempTwoQueryWhichRun[] = $queryfinal = $v->queryfinal;
                    DB::select($queryfinal);
                }
            }
        }
        $queryfinal = 'UPDATE rs_publication_books  SET hindi_required_book_count = if(hindi_required_book_count <= 0, 0, hindi_required_book_count),english_required_book_count = if(english_required_book_count <= 0, 0, english_required_book_count) WHERE deleted_at IS NULL;';
        DB::select($queryfinal);
        // dd($tempQueryWhichRun);
        // dd($tempTwoQueryWhichRun);
        $diplayQ = 'SELECT pb.id, pb.ai_code, pb.course, pb.subject_id, pb.subject_volume_id, pb.hindi_auto_student_count, pb.hindi_last_year_book_stock_count, pb.hindi_required_book_count, pb.english_auto_student_count, pb.english_last_year_book_stock_count, pb.english_required_book_count, pb.* FROM rs_publication_books pb WHERE pb.exam_year = ' . $current_exam_year . ' AND pb.deleted_at IS NULL ORDER BY pb.ai_code, pb.course, pb.subject_id, pb.subject_volume_id;';

        echo "Done Please check with below query : <br> <br> <br>  " . $diplayQ;
        die;

    }
}