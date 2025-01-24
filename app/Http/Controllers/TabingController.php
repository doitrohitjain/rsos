<?php

namespace App\Http\Controllers;

use Auth;
use Config;
use DB;
use Hash;
use PDF;
use Redirect;
use Response;
use Route;
use Session;
use Validator;

class TabingController extends Controller
{

    public function index()
    {


        // Student Updation logs Function call
        /*
        $table_primary_id = 627721;
        $table_name ='students';
        $form_type='Admission';
        $controller_obj = new Controller;
        echo $log_status = $controller_obj->updateStudentLog($table_name,$table_primary_id,$form_type);
        die;
        */
        // Student Updation logs Function call

        $current_admission_session_id = Config::get("global.current_admission_session_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $current_stream_id = Config::get("defaultStreamId");

        $title = "Develoepr Details";
        $table_id = "Develoepr_Details";
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

        $content = array();

        //  resultprocess data
        $content['resultprocess'][] = array('link' => 'result_process/show_combination/0/1', 'linktext' => 'Result process fresh student', 'status' => 1);
        $content['resultprocess'][] = array('link' => 'supp_result_process/show_combination/0/1', 'linktext' => 'Result process supp student', 'status' => 1);
        $content['resultprocess'][] = array('link' => 'result_process/get_toppers', 'linktext' => 'Get Topper of current session year & month', 'status' => 1);
        //  resultprocess data

        //  resultprocess data
        $content['practical'][] = array('link' => 'data_settings/setup_student_allotment_marks', 'linktext' => 'Setup student allotment marks', 'status' => 1);
        //  resultprocess data
        //  resultprocess data

        $tab_arr = array(
            array(
                "name" => "Result Process",
                'id' => 'resultprocess',
                'class' => 'tablinks',
                'content' => @$content['resultprocess'],
                'status' => true,
            ),
            array(
                "name" => "Practical Module",
                'id' => 'practical',
                'class' => 'tablinks',
                'content' => @$content['practical'],
                'status' => true,
            ),
            array(
                "name" => "Theory Module",
                'id' => 'theory',
                'class' => 'tablinks',
                'content' => @$content['theory'],
                'status' => true,
            ),
            array(
                "name" => "Reports Module",
                'id' => 'reports',
                'class' => 'tablinks',
                'content' => @$content['reports'],
                'status' => true,
            )
        );


        return view('tabing.index', compact('tab_arr', 'content', 'breadcrumbs', 'title'));
    }

}