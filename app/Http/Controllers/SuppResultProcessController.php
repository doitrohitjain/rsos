<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\ResultProcessCustomComponent;
use App\models\ExamSubject;
use App\models\PrepareExamResult;
use App\models\PrepareExamSubject;
use App\models\Student;
use App\models\StudentAllotmentMark;
use App\models\Supplementary;
use App\models\SupplementarySubject;
use Auth;
use Cache;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PDF;
use Response;
use Session;

class SuppResultProcessController extends Controller
{
    public $custom_component_obj = "";
    public $marksheet_component_obj = "";

    function __construct()
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", "-1");
        echo "<title> Supp " . ucwords(str_replace("_", " ", Route::getCurrentRoute()->getName())) . " Time " . date('d-m-y h:i:s') . " </title>";
        echo "<style>.alerts-border {
			border: 15px #ff0000 solid;
			animation: blink 1s;
			animation-iteration-count: 500;
		}
		@keyframes blink { 50% { border-color:#fff ; }  }</style>";
        $this->custom_component_obj = new CustomComponent;
        $this->resultprocess_component_obj = new ResultProcessCustomComponent;
        $this->allow_mannual = true; //true

        $this->conditionsEnrollment = array("06006203063","24001212137","14001212150","28089223004","04057223114","14001233020","04061233007","04053232001","33007232017","09006232068","30001232039","30001232043","09060232057","04065232038","14016233051","09017233165","01074233181","17077233275","26028232303");
//        $this->conditionsEnrollment = array("24001212137");
        // $this->conditionsEnrollment = array("12276225007"); //11037223155
        $this->appUrl = Config::get("global.APP_URL");
        $this->baseStart = $this->appUrl . "supp_result_process/";
    }


    //Make sure suplementary subject table should check exam_month,exam_year,deleted_at.
    // table start
    // student_allotments
    // student_allotment_marks
    // students
    // applications
    // exam_subjects
    // supplementary
    // supplementary_subjects
    // toc
    // toc_marks
    // rs_prepare_exam_results
    // rs_prepare_exam_subjects
    // rs_provisional_exam_results
    // rs_provisional_exam_subjects
    // rs_student_theory_copy_checking_marks
    // rs_exam_results

    // select count(id) as c from rs_students where exam_year= 126 and exam_month = 2;
    // select count(id) as c from rs_applications where exam_year = 126;
    // select count(id) as c from rs_student_allotments where exam_year= 126 and exam_month = 2  and deleted_at is null;;
    // select count(id) as c from rs_student_allotment_marks where exam_year= 126 and exam_month = 2 and deleted_at is null;
    // select count(id) as c from rs_exam_subjects where exam_year= 126 and exam_month = 2;
    // select count(id) as c from rs_toc where exam_year= 126 and exam_month = 2;
    // select count(id) as c from rs_supplementaries where exam_year= 126 and exam_month = 2;
    // select count(id) as c from rs_supplementary_subjects where exam_year= 126 and exam_month = 2;
    // select count(id) as c from rs_student_theory_copy_checking_marks where exam_year= 126 and exam_month = 2;
    // select count(id) as c from rs_supplementary_subjects where exam_year= 126 and exam_month = 2;
    // select count(id) as c from rs_exam_results where exam_year= 126 and exam_month = 2;
    // table end
	
    public function show_combination($offset = null, $limit = null, Request $request)
    {

        // $offset = 0;$limit = 200;
        $css = ' <style> @import url("https://fonts.googleapis.com/css?family=Lato:400,400i,700"); ol {
				max-width: 350px;
				counter-reset: my-awesome-counter;
				list-style: none;
				padding-left: 40px;
			}
			ol li {
				margin: 0 0 0.5rem 0;
				counter-increment: my-awesome-counter;
				position: relative;
			}
			ol li::before {
			content: counter(my-awesome-counter);
			color: #fcd000;
			font-size: 1.5rem;
			font-weight: bold;
			position: absolute;
			--size: 32px;
			left: calc(-1 * var(--size) - 10px);
			line-height: var(--size);
			width: var(--size);
			height: var(--size);
			top: 0;
			transform: rotate(-10deg);
			background: black;
			border-radius: 50%;
			text-align: center;
			box-shadow: 1px 1px 0 #999;
			} 
			body { 
			font-family: Lato, sans-serif;
			line-height: 1.4;
			font-size: 90%;
			margin: 2rem;
			}</style>';
        $html = $css;
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $html .= "<h2 style='color:red;'> Exam Year : " . $current_admission_session_id . " and exam_month : " . $current_exam_month_id . "</h2>";
        $html .= "<h2> Supplementary Stuent : ";
        $html .= "<ol>";
        $baseStart = $this->appUrl . "supp_result_process/result_process_start/";
        $baseEnd = "/" . $offset . "/" . $limit;
        //$html .= "<li><a target='_blank' href=". $baseStart . "0/10" . $baseEnd . "> is_supplementary=0,course=10,offset=" . $offset .",limit=" . $limit ."</a></li><br>";

        //$html .= "<li><a target='_blank' href=". $baseStart . "0/12" . $baseEnd . "> is_supplementary=0,course=12,offset=" . $offset .",limit=" . $limit ."</a></li><br>";

        // dd($baseStart . "1/10" . $baseEnd);

        $html .= "<li><a target='_blank'  href=" . $baseStart . "1/10" . $baseEnd . "> is_supplementary=1,course=10,offset=" . $offset . ",limit=" . $limit . "</a></li><br>";

        $html .= "<li><a target='_blank' href=" . $baseStart . "1/12" . $baseEnd . "> is_supplementary=1,course=12,offset=" . $offset . ",limit=" . $limit . "</a></li><br>";

        $html .= "</ol>";
        $html .= "</h2><br>URL: result_process_start\is_supplementary\course\offset\limit<br>";

        $otherLink = $this->appUrl . "result_process/show_combination/0/1";
        $html .= "Other Result <li><a target='_blank' href=" . $otherLink . "> " . $otherLink . " </a></li><br>";

        echo $html;
        die;
    }

    public function result_process_start($is_supplementary = 0, $course = null, $offset = 0, $limit = 3000, Request $request)
    {
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");

        $studentLists = $this->_getValidStudentList($is_supplementary, $course, $offset, $limit);

        $subjects = $this->subjectList($course);
        $subjectCodes = $this->subjectCodeList($course);

//        dd(count($studentLists));

        $counter = 1;
        try {
            //valid from student allotment table
            //
            foreach ($studentLists as $student_id => $enrollment) {
                $exconditions = array();
                $extra_exconditions = array();
                $exconditions['student_id'] = $student_id;


                /* Update after result done because exam subject picking wrong exam month start */
                $needToPickupExamYearAndMonth = ExamSubject::whereNull('deleted_at')->groupBy('exam_year', 'exam_month')->where($exconditions)->orderBy("exam_year", "DESC")->orderBy("exam_month", "asc")->first(['exam_month', 'exam_year']);
                $examMonthYearData['exam_year'] = null;
                $examMonthYearData['exam_month'] = null;
                if (isset($needToPickupExamYearAndMonth)) {
                    $totalEntries = @$needToPickupExamYearAndMonth->count();
                    if ($totalEntries > 0) {
                        $examMonthYearDataTemp = $needToPickupExamYearAndMonth->toArray();
                        $examMonthYearData['exam_year'] = $examMonthYearDataTemp['exam_year'];
                        $examMonthYearData['exam_month'] = $examMonthYearDataTemp['exam_month'];
                    }
                }
                $extra_exconditions['exam_year'] = $examMonthYearData['exam_year'];
                $extra_exconditions['exam_month'] = $examMonthYearData['exam_month'];
                /* Update after result done because exam subject picking wrong exam month end */

                $examSubjectDetails = ExamSubject::whereNull('deleted_at')->where($extra_exconditions)->where($exconditions)->orderBy("exam_year", "DESC")->orderBy("exam_month", "asc")->get(['enrollment', 'student_id', 'subject_id', 'final_practical_marks', 'final_theory_marks', 'sessional_marks', 'sessional_marks_reil_result', 'subject_type', 'total_marks', 'final_result', 'exam_year', 'exam_month']);

                $studentAllomentMarksDetails = StudentAllotmentMark::whereNull('deleted_at')->orderBy("exam_year", "DESC")->where($exconditions)->get(['subject_id', 'is_exclude_for_theory', 'is_exclude_for_practical'])->groupBy("subject_id");

                $oldThPrMarks = array();
                $finalSubjectDetails = array();
                $oldSubjectLists = array();





                if (@$examSubjectDetails && !empty($examSubjectDetails)) {
                    $examSubjectDetails = $examSubjectDetails->toArray();

                    foreach ($examSubjectDetails as $k => $v) {

                        $tt = $v;
                        unset($v);
                        $v[0] = $tt;

                        $finalSubjectDetails[$v[0]['subject_id']] = $v[0];
                        // $oldSubjectLists[$k] = $k;
                        $oldSubjectLists[$v[0]['subject_id']] = $v[0]['subject_id'];


                        if (@$v[0]['final_theory_marks']) {
                            $oldThPrMarks[$v[0]['subject_id']]['final_theory_marks'] = $v[0]['final_theory_marks'];
                        }
                        if (@$v[0]['final_practical_marks']) {
                            $oldThPrMarks[$v[0]['subject_id']]['final_practical_marks'] = $v[0]['final_practical_marks'];
                        }
                    }
//                    dd($finalSubjectDetails);
                } else {
                    $examSubjectDetails = array();
                }
                // dd($examSubjectDetails->toArray());


                $studentAllotmentNewSubjectLists = array();
                if (@$studentAllomentMarksDetails && !empty($studentAllomentMarksDetails)) {
                    $studentAllomentMarksDetails = $studentAllomentMarksDetails->toArray();
                    // dd($studentAllomentMarksDetails);
                    foreach ($studentAllomentMarksDetails as $subject_id => $v) {
                        $studentAllotmentNewSubjectLists[$subject_id]['is_exclude_for_theory'] = $studentAllomentMarksDetails[$subject_id][0]['is_exclude_for_theory'];
                    }
                } else {
                    $examSubjectDetails = array();
                }


                /* Remove subject which is replaced in supplementary form start */
                $conditionsSupp = array();
                $conditionsSupp['supplementaries.student_id'] = $student_id;
                $conditionsSupp['supplementaries.exam_year'] = $current_admission_session_id;
                $conditionsSupp['supplementaries.exam_month'] = $current_exam_month_id;

                //oldsubjects list remove which is exists in subject_id coloumn

                $noNeedToCheckInOldSubjects = SupplementarySubject::
                join('supplementaries', 'supplementary_subjects.supplementary_id', '=', 'supplementaries.id')
                    ->whereNull('supplementaries.deleted_at')->whereNull('supplementary_subjects.deleted_at')
                    ->where($conditionsSupp)
                    ->get("supplementary_subjects.*");
                if(count($noNeedToCheckInOldSubjects) > 0){
                    $noNeedToCheckInOldSubjects = $noNeedToCheckInOldSubjects->toArray();
                }
//                echo "<pre>";
//                print_r($oldSubjectLists);
//                print_r($noNeedToCheckInOldSubjects);
//                die;

                foreach($noNeedToCheckInOldSubjects as $k => $v){
                    if(in_array($v['subject_id'],$oldSubjectLists)){
                        unset($oldSubjectLists[$v['subject_id']]);
                    }
                }

//                echo "<pre>";
//                print_r($oldSubjectLists);
//                print_r($noNeedToCheckInOldSubjects);
//                die;



                $suppSubjectReplaced = SupplementarySubject::
                join('supplementaries', 'supplementary_subjects.supplementary_id', '=', 'supplementaries.id')
                    ->whereNull('supplementaries.deleted_at')->whereNull('supplementary_subjects.deleted_at')
                    ->where($conditionsSupp)
                    ->whereIn("previous_subject_id", $oldSubjectLists)
                    ->get("supplementary_subjects.*");

//                echo "<pre>";
//                print_r($oldSubjectLists);
//                print_r($noNeedToCheckInOldSubjects);
//                dd(count($suppSubjectReplaced));
//                die;

                $previewsSubjectIds = array();
                if (@$suppSubjectReplaced && !empty($suppSubjectReplaced) && count($suppSubjectReplaced) > 0) {
                    $suppSubjectReplaced = $suppSubjectReplaced->toArray();
//                    dd($suppSubjectReplaced);
                    foreach ($suppSubjectReplaced as $k => $v) {
                        $previewsSubjectIds[$v['previous_subject_id']] = $v['previous_subject_id'];
                        $v['sessional_marks'] = @$finalSubjectDetails[$v['subject_id']]['sessional_marks'];
                        $v['sessional_marks_reil_result'] = @$finalSubjectDetails[$v['subject_id']]['sessional_marks_reil_result'];

                        if (@$studentAllomentMarksDetails[$v['subject_id']][0]['is_exclude_for_practical'] == 1) {
                            if(isset($finalSubjectDetails[$v['subject_id']]['final_practical_marks'])){
                                $v['final_practical_marks'] = $finalSubjectDetails[$v['subject_id']]['final_practical_marks'];
                            }
                        }

                        if (@$studentAllomentMarksDetails[$v['subject_id']][0]['is_exclude_for_theory'] == 1) {
                            if(isset($finalSubjectDetails[$v['subject_id']]['final_theory_marks'])){
                                $v['final_theory_marks'] = $finalSubjectDetails[$v['subject_id']]['final_theory_marks'];
                            }
                        }
                        $finalSubjectDetails[$v['previous_subject_id']] = @$v;

                    }
                } else {
                    $suppSubjectReplaced = array();
//                    $noNeedToCheckInOldSubjects 1,4
                    foreach($noNeedToCheckInOldSubjects as  $k  => $v){
                        if(isset($finalSubjectDetails[$v['subject_id']])){

                        }else{
                            $finalSubjectDetails[$v['subject_id']] = $v;
                        }
                    }
                }
                /* Remove subject which is replaced in supplementary form end */
//                dd($studentAllomentMarksDetails);
//                dd($finalSubjectDetails); //rohit rahul error interchange in subject of supplementary

//                dd($finalSubjectDetails);
                /* Update-A subject which is replaced in supplementary form start */
                $conditionsSupp = array();
                $conditionsSupp['supplementaries.student_id'] = $student_id;
                $conditionsSupp['supplementaries.exam_year'] = $current_admission_session_id;
                $conditionsSupp['supplementaries.exam_month'] = $current_exam_month_id;

                $suppSubjectReplaced = SupplementarySubject::
                join('supplementaries', 'supplementary_subjects.supplementary_id', '=', 'supplementaries.id')
                    ->whereNull('supplementaries.deleted_at')->whereNull('supplementary_subjects.deleted_at')->where($conditionsSupp)->whereNull("previous_subject_id")->get("supplementary_subjects.*");

                $previewsSubjectIds = array();
                if (@$suppSubjectReplaced && !empty($suppSubjectReplaced)) {
                    $suppSubjectReplaced = $suppSubjectReplaced->toArray();
                    foreach ($suppSubjectReplaced as $k => $v) {
                        $finalSubjectDetails[$v['subject_id']] = @$v;
                    }
                } else {
                    $suppSubjectReplaced = array();
                }
                /* Update-A subject which is replaced in supplementary form end */


                $studentDetails = Student::where('id', "=", $student_id)->first(["ai_code", "enrollment"]);

                if (@$studentDetails && !empty($studentDetails)) {
                    $studentDetails = $studentDetails->toArray();
                } else {
                    $studentDetails = array();
                }
                // if($student_id == '1438'){
                // 	continue;
                // }


                if (isset($studentDetails['ai_code']) && !empty($studentDetails['ai_code'])) {
                } else {
                    continue;//echo $student_id . " student id not found  <br>";die;
                }

                //dd($finalSubjectDetails); //examSubjectDetails


                /* Update for duplicate subject from exam and supplementary subjects start 1 */
                $subjectIdCount = array();
                foreach (@$finalSubjectDetails as $exkey => $exval) {
                    if (@$subjectIdCount[$exval['subject_id']]) {
                        $subjectIdCount[$exval['subject_id']]++;
                    } else {
                        $subjectIdCount[$exval['subject_id']] = 1;
                    }
                }
                /* Update for duplicate subject from exam and supplementary subjects start 1 */

//                dd($finalSubjectDetails);
                $tempSaveTest = array();
                foreach (@$finalSubjectDetails as $exkey => $exval) {
                    /* Update for duplicate subject from exam and supplementary subjects start 2 */
                    if ($subjectIdCount[$exval['subject_id']] > 1) {
                        if (@$exval['supplementary_id']) {

                        } else {
                            continue;
                        }
                    }
                    /* Update for duplicate subject from exam and supplementary subjects start 2 */
                    $savedata = array();
                    $savedata['student_id'] = $exval['student_id'];
                    if (@$exval['enrollment']) {
                        $savedata['enrollment'] = $exval['enrollment'];
                    } else {
                        $savedata['enrollment'] = $exval['enrollment'] = $studentDetails['enrollment'];
                    }
                    $savedata['subject_id'] = $exval['subject_id'];
                    if (@$exval['subject_id'] && @$subjects[$exval['subject_id']]) {
                        $savedata['subject_name'] = $subjects[$exval['subject_id']];
                        $savedata['subject_code'] = $subjectCodes[$exval['subject_id']];
                    }
                    $savedata['is_supplementary'] = $is_supplementary;
                    if (@$exval['sessional_marks']) {

                    } else {
                        //dd($exval);
                    }

                    $savedata['sessional_marks'] = 0;
                    if (@$exval['sessional_marks']) {
                        $savedata['sessional_marks'] = @$exval['sessional_marks'];
                    }
                    $savedata['sessional_marks_reil_result'] = 0;
                    if (@$exval['sessional_marks_reil_result']) {
                        $savedata['sessional_marks_reil_result'] = @$exval['sessional_marks_reil_result'];
                    }

                    $savedata['theory_marks'] = 0;
                    if (isset($studentAllotmentNewSubjectLists[$exval['subject_id']]['is_exclude_for_theory']) && $studentAllotmentNewSubjectLists[$exval['subject_id']]['is_exclude_for_theory'] == 1) {
                        if (@$oldThPrMarks[$exval['subject_id']]['final_theory_marks']) {
                            $savedata['theory_marks'] = @$oldThPrMarks[$exval['subject_id']]['final_theory_marks'];
                        }
                    } else {
                        if (@$exval['final_theory_marks']) {
                            $savedata['theory_marks'] = @$exval['final_theory_marks'];
                        }
                    }

                    $savedata['practical_marks'] = 0;
                    if (isset($studentAllotmentNewSubjectLists[$exval['subject_id']]['is_exclude_for_practical']) && $studentAllotmentNewSubjectLists[$exval['subject_id']]['is_exclude_for_practical'] == 1) {
                        if (!in_array($exval['subject_id'], array(3, 4, 7, 13, 16, 23, 24, 25, 28, 33, 35, 37, 38, 39))) {
                            $savedata['practical_marks'] = 999;
                        } else {
                            if (@$oldThPrMarks[$exval['subject_id']]['final_practical_marks']) {
                                $savedata['practical_marks'] = @$oldThPrMarks[$exval['subject_id']]['final_practical_marks'];
                            }
                        }
                    } else {
                        if (!in_array($exval['subject_id'], array(3, 4, 7, 13, 16, 23, 24, 25, 28, 33, 35, 37, 38, 39))) {
                            $savedata['practical_marks'] = 999;
                        } else {
                            if (@$exval['final_practical_marks']) {
                                $savedata['practical_marks'] = $exval['final_practical_marks'];
                            }
                        }
                    }

                    /* $savedata['practical_marks'] = 0;
					if(!in_array($exval['subject_id'],array(3,4,7,13,16,23,24,25,28,33,35,37,38,39))){
						$savedata['practical_marks'] = 999;
					}else{
						if(@$exval['final_practical_marks']){
							$savedata['practical_marks'] = $exval['final_practical_marks'];
						}
					} */

                    $savedata['exam_year'] = $current_admission_session_id;
                    $savedata['exam_month'] = $current_exam_month_id;

                    $savedata['subject_type'] = 0;
                    if (@$exval['subject_type']) {
                        $savedata['subject_type'] = @$exval['subject_type'];
                    }

                    $savedata['total_marks'] = 0;
                    if (@$exval['total_marks']) {
                        $savedata['total_marks'] = @$exval['total_marks'];
                    }
                    $savedata['final_result'] = 0;
                    if (@$exval['final_result']) {
                        $savedata['final_result'] = @$exval['final_result'];
                    }
                    $savedata['old_theory_marks'] = 0;
                    if (@$savedata['theory_marks']) {
                        $savedata['old_theory_marks'] = $savedata['theory_marks'];
                    }

                    $savedata['ai_code'] = $studentDetails['ai_code'];
                    $savedata['course'] = $course;
                    $savedata['status'] = 1;
                    $savedata['student_id'] = $exval['student_id'];

                    // if($exval['subject_id'] == 24){
                    // 	dd($savedata);
                    // }
                    $tempSaveTest[] = $savedata;
                    // dd($savedata);
                    PrepareExamSubject::create($savedata);
                }

                $counter++;
            }
        } catch (Exception $e) {
            die('Error loading file : ' . $e->getMessage());
        }
        echo "<h1>Step 1 : Prepare exam subjects data imported " . $counter . "</h1>";
        // die;
        $newOffset = $offset + $limit;
        $newOffset = $offset;
        $baseAction = "update_practical_theory_marks";
        $this->setAndHitNextUrl($baseAction, $is_supplementary, $course, $newOffset, $limit);
    }

    public function _getValidStudentList($is_supplementary = 0, $course = null, $offset = null, $limit = null)
    {
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $conditions = null;
        $conditions ["student_allotments.exam_year"] = $current_admission_session_id;
        $conditions ["student_allotments.exam_month"] = $current_exam_month_id;
        $conditions['student_allotments.supplementary'] = $is_supplementary;
        if (isset($course) && !empty($course)) {
            $conditions['student_allotments.course'] = $course;
        }


        if ($this->allow_mannual == true) {
            $currentActionName = Route::getCurrentRoute()->getActionName();
            $cacheName = $course . "_" . $limit . "_" . $offset . "_" . $is_supplementary;
            if ($currentActionName == "result_process_start" || $currentActionName == "final_result") {
                Cache::forget($cacheName);
            }
            Cache::forget($cacheName);
            if (Cache::has($cacheName)) {
                $studentLists = Cache::get($cacheName);
            } else {

                $studentLists = Cache::rememberForever($cacheName, function () use ($conditions, $is_supplementary, $course, $offset, $limit) {

                    $studentLists = $this->getListOfManualSupplyenrollment($is_supplementary, $course, $offset, $limit);
                    if (@$studentLists && !empty($studentLists)) {
                        $studentLists = $studentLists->toArray();
                    } else {
                        $studentLists = array();
                    }


                    //$studentLists = array_flip($studentLists);

                    $studentLists = StudentAllotmentMark::join('student_allotments', 'student_allotments.student_id', '=', 'student_allotment_marks.student_id')->whereIn('student_allotments.enrollment', $studentLists)->whereNull('student_allotments.deleted_at')->where('student_allotments.course', $course)->where($conditions)->groupBy("student_id")->take($limit)->skip($offset)->pluck("student_allotments.enrollment", "student_allotments.student_id");

                    return $studentLists;
                });
            }
        } else {
            $currentActionName = Route::getCurrentRoute()->getActionName();
            $cacheName = $course . "_" . $limit . "_" . $offset . "_" . $is_supplementary;
            if ($currentActionName == "result_process_start" || $currentActionName == "final_result") {
                Cache::forget($cacheName);
            }
            if (Cache::has($cacheName)) {
                $studentLists = Cache::get($cacheName);
            } else {
                $studentLists = Cache::rememberForever($cacheName, function () use ($conditions, $is_supplementary, $course, $offset, $limit) {
                    $studentLists = StudentAllotmentMark::join('student_allotments', 'student_allotments.student_id', '=', 'student_allotment_marks.student_id')->whereNull('student_allotments.deleted_at')->where('student_allotments.course', $course)->where($conditions)->groupBy("student_id")->take($limit)->skip($offset)->pluck("student_allotments.enrollment", "student_allotments.student_id");

                    return $studentLists;
                });
            }
        }

        return $studentLists;
    }

    public function getListOfManualSupplyenrollment($is_supplementary = 0, $course = null, $offset = null, $limit = null)
    {
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $conditions = null;
        $conditions ["student_allotments.exam_year"] = $current_admission_session_id;
        $conditions ["student_allotments.exam_month"] = $current_exam_month_id;
        $conditions['student_allotments.supplementary'] = $is_supplementary;
        if (isset($course) && !empty($course)) {
            $conditions['student_allotments.course'] = $course;
        }
        $conditionsEnrollment = $this->conditionsEnrollment;

        $studentLists = StudentAllotmentMark::join('student_allotments', 'student_allotments.student_id', '=', 'student_allotment_marks.student_id')->whereNull('student_allotments.deleted_at')->whereIn('student_allotments.enrollment', $conditionsEnrollment)->where('student_allotments.course', $course)->where($conditions)->groupBy("student_id")->take($limit)->skip($offset)->pluck("student_allotments.enrollment", "student_allotments.student_id");

        return $studentLists;
    }
    /* Fresh Student Of 10th and 12th start */
    /* Step 1 Move data from exam subject prepare_exam_subjects */

    public function setAndHitNextUrl($baseAction = null, $is_supplementary = null, $course = null, $newOffset = null, $limit = null)
    {
        $seconds = "0:3";
        $realSeconds = "3000";
        $nextRefreshRealSeconds = "3000";
        $newUrl = $this->baseStart . $baseAction . "/" . $is_supplementary . "/" . $course . "/" . $newOffset . "/" . $limit;
        echo "<div class='countdown' style='text-align:center;font-size: 60px;margin-top: 0px;background:linear-gradient(45deg, #a5e362, #287a7373);'></div> <script>
		
		var timer2 = '" . $seconds . "';
		var interval = setInterval(function() { 
		var timer = timer2.split(':');
		//by parsing integer, I avoid all extra string processing
		var minutes = parseInt(timer[0], 10);
		var seconds = parseInt(timer[1], 10);
		--seconds;
		minutes = (seconds < 0) ? --minutes : minutes;
		if (minutes < 0) clearInterval(interval);
		seconds = (seconds < 0) ? 59 : seconds;
		seconds = (seconds < 10) ? '0' + seconds : seconds;
		//minutes = (minutes < 10) ?  minutes : minutes;
		$('.countdown').html(minutes + ':' + seconds);
		timer2 = minutes + ':' + seconds;
		}, " . $realSeconds . ");

		</script><script src='https://code.jquery.com/jquery-3.5.1.min.js'></script> <script>
		setTimeout(
			setTimeoutAction
		, " . $nextRefreshRealSeconds . ");  
		function setTimeoutAction(){
			var win = window.open('" . $newUrl . "', '_blank');
			if (win) { 
				win.focus();
			} else { 
				alert('Please allow popups for this website');
			} 
		}
		 </script>";
        exit();
    }

    /* Step 2 Update Theory and practical marks from student_allotment_marks table to PES(prepare_exam_subjects) */

    public function update_practical_theory_marks($is_supplementary = 0, $course = null, $offset = 0, $limit = 3000, Request $request)
    {

        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $conditions = array();
        $studentLists = $this->_getValidStudentList($is_supplementary, $course, $offset, $limit);
        if (@$studentLists && !empty($studentLists)) {
            $studentLists = $studentLists->toArray();
        } else {
            $studentLists = array();
        }

        $counter = 1;
        $studentLists = array_flip($studentLists);
        $conditions = null;
        $conditions ["prepare_exam_subjects.exam_year"] = $current_admission_session_id;
        $conditions ["prepare_exam_subjects.exam_month"] = $current_exam_month_id;
        $conditions['prepare_exam_subjects.is_supplementary'] = $is_supplementary;
        if (isset($course) && !empty($course)) {
            $conditions['prepare_exam_subjects.course'] = $course;
        }
        $students = PrepareExamSubject::
        join('student_allotment_marks', 'prepare_exam_subjects.student_id', '=', 'student_allotment_marks.student_id')
            ->whereIn("student_allotment_marks.student_id", $studentLists)
            ->where($conditions)->whereNull("student_allotment_marks.deleted_at")->orderBy("student_allotment_marks.id")
            ->get(['prepare_exam_subjects.student_id', 'prepare_exam_subjects.enrollment', "prepare_exam_subjects.subject_id", "prepare_exam_subjects.id", "prepare_exam_subjects.final_result", "prepare_exam_subjects.sessional_marks", "prepare_exam_subjects.sessional_marks_reil_result", "prepare_exam_subjects.theory_marks", "prepare_exam_subjects.practical_marks", "prepare_exam_subjects.old_theory_marks", "student_allotment_marks.is_exclude_for_theory", "student_allotment_marks.is_exclude_for_practical",]);

        // $students = PrepareExamSubject::whereIn("student_id",$studentLists)
        // 	->where($conditions)->whereNull("deleted_at")->orderBy("id")
        // 	->get(['student_id','enrollment',"subject_id","id","final_result","sessional_marks","sessional_marks_reil_result","old_theory_marks"]);


        if (@$students && !empty($students)) {
            $students = $students->toArray();
        } else {
            $students = array();
        }

        try {
            foreach (@$students as $sekey => $seval) { //just update the marks from student allotment marks to our prepare exam subject table. 25/11/2022
                $exconditions = array();
                $exconditions ["exam_year"] = $current_admission_session_id;
                $exconditions ["exam_month"] = $current_exam_month_id;
                $exconditions['is_supplementary'] = $is_supplementary;
                if (isset($course) && !empty($course)) {
                    $exconditions['course'] = $course;
                }
                $exconditions['student_id'] = $seval["student_id"];
                $exconditions['subject_id'] = $seval["subject_id"];


                $theoryPracticals = StudentAllotmentMark::where($exconditions)
                    ->whereNull("deleted_at")->first();
                if (@$theoryPracticals && !empty($theoryPracticals)) {
                    $theoryPracticals = $theoryPracticals->toArray();
                } else {
                    $theoryPracticals = array();
                }

                if (!empty($theoryPracticals)) {
                    if (@$theoryPracticals['is_exclude_for_practical'] && $theoryPracticals['is_exclude_for_practical'] == 1) {
                        $savedata['practical_marks'] = $seval['practical_marks'];
                    } else {
                        $practicalMark = (!empty($theoryPracticals['final_practical_marks']) && $theoryPracticals['final_practical_marks'] != 0) ? $theoryPracticals['final_practical_marks'] : 999;
                        $savedata['practical_marks'] = (!empty($theoryPracticals['practical_absent'])) ? 999 : $practicalMark;
                    }
                    // dd($seval);
                    if (@$theoryPracticals['is_exclude_for_theory'] && $theoryPracticals['is_exclude_for_theory'] == 1) {
                        $savedata['theory_marks'] = $savedata['old_theory_marks'] = $seval['theory_marks'];
                    } else {
                        $savedata['theory_marks'] = (!empty($theoryPracticals['theory_absent'])) ? 999 : $theoryPracticals['final_theory_marks'];
                        if (@$savedata['theory_marks']) {
                            $savedata['old_theory_marks'] = $savedata['theory_marks'];
                        }
                    }
                    $savedata['is_supplementary'] = (!empty($theoryPracticals['is_supplementary'])) ? $theoryPracticals['is_supplementary'] : 0;
                    PrepareExamSubject::where('id', $seval['id'])->update($savedata);
                    unset($savedata);
                }


                $suppSavedata['is_moved_in_pses'] = 1;
                $suppConditions = array();
                $suppConditions ["exam_year"] = $current_admission_session_id;
                $suppConditions ["exam_month"] = $current_exam_month_id;
                if (isset($course) && !empty($course)) {
                    $suppConditions['course'] = $course;
                }
                $suppConditions['student_id'] = $seval["student_id"];
                Supplementary::where($suppConditions)->update($suppSavedata);
                $counter++;
            }
        } catch (Exception $e) {
            die('Error loading file : ' . $e->getMessage());
        }

        echo "<h1>Step 2 : Updated Theory and practical marks from StudentAllotmentMark table to PrepareExamSubject " . $counter . "</h1>";
        $newOffset = $offset + $limit;
        $newOffset = $offset;
        $baseAction = "calculate_theory_sessional_marks";
        $this->setAndHitNextUrl($baseAction, $is_supplementary, $course, $newOffset, $limit);
        die;
    }

    /* Step 3 Calculate Theory Marks (0.9) and Sessional marks (min marks) */
    public function calculate_theory_sessional_marks($is_supplementary = 0, $course = null, $offset = 0, $limit = 3000, Request $request)
    {
        $counter = 0;
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        if ($current_exam_month_id == 1 || $current_exam_month_id == 2) {
            $conditions = array();
            $studentLists = $this->_getValidStudentList($is_supplementary, $course, $offset, $limit)->toArray();
            $studentLists = array_flip($studentLists);
            $conditions = null;
            $conditions ["prepare_exam_subjects.exam_year"] = $current_admission_session_id;
            $conditions ["prepare_exam_subjects.exam_month"] = $current_exam_month_id;
            $conditions['prepare_exam_subjects.is_supplementary'] = $is_supplementary;
            $conditions['students.stream'] = 1;
            // $conditions['prepare_exam_subjects.toc'] = 0;
            if (isset($course) && !empty($course)) {
                $conditions['prepare_exam_subjects.course'] = $course;
            }
            $counter = 1;
            $students = PrepareExamSubject::join('students', 'students.id', '=', 'prepare_exam_subjects.student_id')
                ->whereIn("prepare_exam_subjects.student_id", $studentLists)
                ->where($conditions)->whereNull("prepare_exam_subjects.deleted_at")->orderBy("id")
                ->get(['prepare_exam_subjects.student_id', 'prepare_exam_subjects.enrollment', "prepare_exam_subjects.id", "prepare_exam_subjects.subject_id", "prepare_exam_subjects.old_theory_marks", "prepare_exam_subjects.sessional_marks", "prepare_exam_subjects.theory_marks", "prepare_exam_subjects.practical_marks", "prepare_exam_subjects.sessional_marks_reil_result"])
                ->toArray();

            if (!empty($students)) {
                foreach (@$students as $seval) {

                    $id = $seval["id"];
                    $exconditions = array();
                    $exconditions ["exam_year"] = $current_admission_session_id;
                    $exconditions ["exam_month"] = $current_exam_month_id;
                    $exconditions['is_supplementary'] = $is_supplementary;
                    if (isset($course) && !empty($course)) {
                        $exconditions['course'] = $course;
                    }
                    $exconditions['student_id'] = $seval["student_id"];
                    $exconditions['subject_id'] = $seval["subject_id"];
                    $theoryPracticals = StudentAllotmentMark::where($exconditions)
                        ->whereNull("deleted_at")->first();

                    if (@$theoryPracticals && !empty($theoryPracticals)) {
                        $theoryPracticals = $theoryPracticals->toArray();
                    } else {
                        $theoryPracticals = array();
                    }


                    $suppSubjectDetail = SupplementarySubject::where("student_id", $seval["student_id"])
                        ->where("subject_id", $seval["subject_id"])
                        ->whereNull("supplementary_subjects.deleted_at")->first();

                    if (@$suppSubjectDetail && !empty($suppSubjectDetail)) {
                        $suppSubjectDetail = $suppSubjectDetail->toArray();
                    } else {
                        $suppSubjectDetail = array();
                    }
                    $savedata['practical_marks'] = 0;
                    $theoryMark = 0;


                    if (!empty($theoryPracticals)) {

                        //$practicalMark=(!empty($theoryPracticals['final_practical_marks'])&&!in_array($theoryPracticals['final_practical_marks'],array(0,999)))?$theoryPracticals['final_practical_marks']:999;
                        if (empty($theoryPracticals['final_practical_marks'])) {
                            $theoryPracticals['final_practical_marks'] = 0;
                        }
                        $practicalMark = $theoryPracticals['final_practical_marks'];
                        $theoryMark = ($theoryPracticals['final_theory_marks'] != "999") ? $theoryPracticals['final_theory_marks'] : "999";
                        if ($theoryPracticals["is_exclude_for_theory"] == 1 && $theoryPracticals["is_exclude_for_practical"] == 1) {
                            continue;
                        }

                        if (($theoryPracticals["is_exclude_for_practical"] == 0) && ($theoryPracticals["is_exclude_for_theory"] == 1)) {
                            /* Update only Practical Marks */
                            $savedata['theory_marks'] = @$seval['theory_marks'];
                            if (!in_array($seval['subject_id'], array(3, 4, 7, 13, 16, 23, 24, 25, 28, 33, 35, 37, 38, 39))) {
                                $savedata['practical_marks'] = 999;
                            } else {
                                $savedata['practical_marks'] = (!empty($theoryPracticals['practical_absent'])) ? 999 : $practicalMark;
                            }
                            $savedata['is_theory_mark_updated'] = 1;
                            $savedata['practical_flag'] = 1;
                        } else if (($theoryPracticals["is_exclude_for_theory"] == 0) && ($theoryPracticals["is_exclude_for_practical"] == 1)) {
                            /* Update only Theory Marks */
                            $savedata['practical_marks'] = @$seval['practical_marks'];
                            $savedata['old_theory_marks'] = (!empty($theoryPracticals['theory_absent'])) ? 999 : $theoryMark;
                            $savedata['theory_marks'] = $theoryMark;
                            // $savedata['is_practical_mark_updated'] =1;
                            $checkStream = $this->_checkStreamTwoStudent($seval["student_id"]);
                            if (empty($checkStream)) {
                                $calTheory = $theoryMark;
                                if (isset($theoryMark) && $theoryMark > 0) {
                                    if (@$suppSubjectDetail['previous_subject_id'] == @$suppSubjectDetail['subject_id']) {
                                        $calTheory = ($theoryMark == 999) ? 999 : round($theoryMark * 0.9);
                                    }
                                }
                                $savedata['theory_marks'] = (!empty($theoryPracticals['theory_absent'])) ? 999 : $calTheory;
                                $savedata['sessional_marks_reil_result'] = $this->getDefaultSupplySessionalMarks($seval, $theoryMark);
                                $savedata['old_theory_marks'] = (!empty($theoryPracticals['theory_absent'])) ? 999 : $theoryMark;
                            }
                        } else {


                            /* Update only Practical and Theory Marks */
                            $checkStream = $this->_checkStreamTwoStudent($seval["student_id"]);

                            if (!in_array($seval['subject_id'], array(3, 4, 7, 13, 16, 23, 24, 25, 28, 33, 35, 37, 38, 39))) {
                                $savedata['practical_marks'] = 999;
                            } else {
                                $savedata['practical_marks'] = (!empty($theoryPracticals['practical_absent'])) ? 999 : $practicalMark;
                            }

                            $savedata['theory_marks'] = $theoryMark;


                            // if($seval["subject_id"] == 30){
                            // 	dd($savedata);
                            // }

                            if (empty($checkStream)) {
                                $calTheory = $theoryMark;
                                if (isset($theoryMark) && $theoryMark > 0) {
                                    if (@$suppSubjectDetail['previous_subject_id'] == @$suppSubjectDetail['subject_id']) {
                                        $calTheory = ($theoryMark == 999) ? 999 : round($theoryMark * 0.9);
                                    }
                                }
                                $savedata['theory_marks'] = (!empty($theoryPracticals['theory_absent'])) ? 999 : $calTheory;
                                $savedata['sessional_marks_reil_result'] = $this->getDefaultSupplySessionalMarks($seval, $theoryMark);
                                $savedata['old_theory_marks'] = (!empty($theoryPracticals['theory_absent'])) ? 999 : $theoryMark;
                            }
                            $savedata['is_supplementary'] = (!empty($theoryPracticals['is_supplementary'])) ? $theoryPracticals['is_supplementary'] : 0;
                            $savedata['is_theory_mark_updated'] = 1;
                            // if($seval["subject_id"] == 16){
                            // 	dd($savedata);
                            // }
                        }


                        if ($theoryPracticals["is_exclude_for_theory"] == 0) {
                            $diffTheoryMark = intval($theoryMark) - intval($calTheory);
                            $subjectId = $seval["subject_id"];
                            $marksArr = $this->resultprocess_component_obj->getSubjectMaxMinMarksMaster($subjectId, 81);
                            if (@$diffTheoryMark > @$savedata['sessional_marks_reil_result']) {
                                $minStrMark = 0;
                                if ($marksArr['STR_MAX'] == 10 || $marksArr['STR_MAX'] == 9 || $marksArr['STR_MAX'] == 8) {
                                    $minStrMark = 3;
                                } else if ($marksArr['STR_MAX'] == 6 || $marksArr['STR_MAX'] == 4) {
                                    $minStrMark = 2;
                                } else if ($marksArr['STR_MAX'] == 3) {
                                    $minStrMark = 1;
                                }
                                if ($minStrMark <= $diffTheoryMark) {
                                    $savedata['sessional_marks_reil_result'] = $diffTheoryMark;
                                }
                            }
                        }

                        // if(@$suppSubjectDetail && !empty($suppSubjectDetail)){
                        // 	$suppSubjectDetail = $suppSubjectDetail->toArray();
                        // }else{
                        // 	$suppSubjectDetail = array();
                        // }
                        $savedata['sessional_marks_reil_result'] = 0;
                        if (@$suppSubjectDetail['previous_subject_id'] == @$suppSubjectDetail['subject_id']) {
                            $savedata['sessional_marks_reil_result'] = $this->getDefaultSupplySessionalMarks($seval, $theoryMark);
                        }
                        PrepareExamSubject::where('id', $id)->update($savedata);
                        unset($savedata);
                        $counter++;
                    }

                }
            } else {
            }
        }
        echo "<h1>Step 3 : Work only for stream-1 Calculate Theory Marks (0.9) and Sessional marks (min marks) Updated " . $counter . "</h1>";
        $newOffset = $offset + $limit;
        $newOffset = $offset;
        $baseAction = "total_marks_update";
        $this->setAndHitNextUrl($baseAction, $is_supplementary, $course, $newOffset, $limit);
        die;
    }

    /*  Step 4 Total Marks update done */

    private function _checkStreamTwoStudent($student_id = null)
    {
        $count = 0;
        if (!empty($student_id)) {
            $count = Student::where('id', '=', $student_id)->where('stream', '=', 2)->count();
        }
        return $count;
    }

    /* Step 5 Process Result */

    private function getDefaultSupplySessionalMarks($data, $theoryMark = "")
    {
        $subjectId = $data["subject_id"];
        $id = $data["id"];
        $marksArr = $this->resultprocess_component_obj->getSubjectMaxMinMarksMaster($subjectId, 81);

        $strMax = $marksArr['STR_MAX'];
        $theoryMarks = (!empty($theoryMark)) ? (int)$theoryMark : 0;

        if (isset($data["sessional_marks_reil_result"])) {
            $newsessionalMarks = $sessionalMarks = (int)$data["sessional_marks_reil_result"];
        }
        if ($sessionalMarks == 0 || $sessionalMarks == 999) {
            if (($theoryMarks >= 0 && $theoryMarks <= 100) || ($theoryMarks == 999)) {  // Session marks not inserting for RWH case in Theory
                if (in_array($strMax, array("8", "9", "10"))) {
                    $newsessionalMarks = 3;
                } elseif (in_array($strMax, array("4", "6"))) {
                    $newsessionalMarks = 2;
                } elseif (in_array($strMax, array("3"))) {
                    $newsessionalMarks = 1;
                }
            }
        }
        return $newsessionalMarks;
    }

    /*Step 6 Final 10th result Move Data into PrepareExamResult Table */

    public function total_marks_update($is_supplementary = 0, $course = null, $offset = 0, $limit = 3000, Request $request)
    {
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $q1 = "update `rs_prepare_exam_subjects` set total_marks =(CASE WHEN theory_marks <= 100 THEN theory_marks ELSE 0 END) + (CASE WHEN practical_marks <= 100 THEN practical_marks ELSE 0 END) +(CASE WHEN sessional_marks_reil_result <= 10 THEN sessional_marks_reil_result ELSE 0 END) where  course = " . $course . " and is_supplementary = " . $is_supplementary . " and exam_year = " . $current_admission_session_id . " and exam_month = " . $current_exam_month_id . ";";
        DB::statement($q1);


        echo "<h1>Step 4 Total Marks Updated " . "</h1>";
        $newOffset = $offset + $limit;
        $newOffset = $offset;
        $baseAction = "process_result";
        $this->setAndHitNextUrl($baseAction, $is_supplementary, $course, $newOffset, $limit);
        die;
    }

    /* Fresh Student Of 10th and 12th end */
    public function process_result($is_supplementary = 0, $course = null, $offset = 0, $limit = 3000, Request $request)
    {
        $subjects = $this->subjectList($course);
        $subjectdata = $subjectCodes = $this->subjectCodeList($course);
        $counter = 1;
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $conditions = array();
        $studentLists = $this->_getValidStudentList($is_supplementary, $course, $offset, $limit)->toArray();
        $studentLists = array_flip($studentLists);
        $conditions = null;
        $conditions ["prepare_exam_subjects.exam_year"] = $current_admission_session_id;
        $conditions ["prepare_exam_subjects.exam_month"] = $current_exam_month_id;
        $conditions['prepare_exam_subjects.is_supplementary'] = $is_supplementary;
        if (isset($course) && !empty($course)) {
            $conditions['prepare_exam_subjects.course'] = $course;
        }
        $resultdata = $students = PrepareExamSubject::whereIn("student_id", $studentLists)
            ->where($conditions)->whereNull("deleted_at")->orderBy("id")
            ->get(['student_id', 'enrollment', "subject_id", "id", "final_result", "sessional_marks", "sessional_marks_reil_result", "old_theory_marks", "total_marks", "course", "theory_marks", "practical_marks"])
            ->toArray();

        $subjectdataarr = $subjectdata->toArray();

        /* Grace Marks Start */
        if (!empty($resultdata)) { //grace marks increes
            $increesMarks = 1;
            $result_code_syct = "SYCT";
            $result_code_sycp = "SYCP";
            $result_code_syc = "SYC";
            foreach ($resultdata as $key => $val) {
                $subjectId = $subjectid = $val['subject_id'];
                if (isset($course) && !empty($course)) {
                    if ($course == 10) {
                        if ($val['total_marks'] == 32) {
                            $datasave['total_marks'] = $val['total_marks'] + $increesMarks;
                            $datasave['grace_marks'] = $increesMarks;
                            $datasave['is_grace_marks_given'] = 1;//incress in theory
                            PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                            unset($datasave);
                            $this->_updateGraceMarksThPr($val['id'], 't');
                            continue;
                        }
                    } else if ($course == 12) {
                        $tempDatasave['final_result'] = null;
                        $tempDatasave['is_only_theory_subject'] = null;
                        if (in_array($val['subject_id'], array(23, 24, 25, 28, 33, 35, 37, 38, 39))) { //practical subjects
                            if (($val['theory_marks'] == 0 || $val['theory_marks'] == 999) && ($val['practical_marks'] == 0 || $val['practical_marks'] == 999)) {
                                $tempDatasave['final_result'] = $result_code_syc;
                            } else if (($val['theory_marks'] == 0 || $val['theory_marks'] == 999)) {
                                $tempDatasave['final_result'] = $result_code_syct;
                            } else if ($val['practical_marks'] == 0 || $val['practical_marks'] == 999) {
                                $tempDatasave['final_result'] = $result_code_sycp;
                            }
                        } else {
                            $tempDatasave['is_only_theory_subject'] = $result_code_syc;
                            if (($val['theory_marks'] == 0 || $val['theory_marks'] == 999)) {
                                $tempDatasave['final_result'] = $result_code_syc;
                            }
                        }


                        if (!empty($tempDatasave['is_only_theory_subject']) && $tempDatasave['is_only_theory_subject'] == $result_code_syc) {
                            if ($val['total_marks'] == 32) {
                                $datasave['total_marks'] = $val['total_marks'] + $increesMarks;
                                $datasave['grace_marks'] = $increesMarks;
                                $datasave['is_grace_marks_given'] = 1;//incress in theory
                                PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                                unset($datasave);
                                $this->_updateGraceMarksThPr($val['id'], 't');
                                continue;
                            }
                        } else {
                            $theorymarks = $val['theory_marks'];
                            $practicalmarks = $val['practical_marks'];

                            if ($tempDatasave['final_result'] != $result_code_syc) {
                                $marksarr = $this->resultprocess_component_obj->getSubjectMaxMinMarksMaster($subjectId, 81);
                                $tempDatasave['final_result'] = null;
                                if (($theorymarks >= $marksarr['TH_MIN'] && $theorymarks <= $marksarr['TH_MAX']) && ($practicalmarks >= $marksarr['PR_MIN'] && $practicalmarks <= $marksarr['PR_MAX'])) {
                                    $tempDatasave['final_result'] = 'P';
                                } else {
                                    if ($practicalmarks < $marksarr['PR_MIN'] && $theorymarks < $marksarr['TH_MIN']) {
                                        $tempDatasave['final_result'] = $result_code_syc;
                                    } else if ($theorymarks < $marksarr['TH_MIN']) {
                                        $tempDatasave['final_result'] = $result_code_syct;
                                    } else if ($practicalmarks < $marksarr['PR_MIN']) {
                                        $tempDatasave['final_result'] = $result_code_sycp;
                                    }
                                }
                            }
                            if ($tempDatasave['final_result'] == $result_code_syct) {
                                // total - practical and checking if 1 lesser with th_min then true
                                $tempGraceCal = $val['total_marks'] - $val['practical_marks'];

                                if (($tempGraceCal + $increesMarks) == $marksarr['TH_MIN']) {
                                    $datasave['total_marks'] = $val['total_marks'] + $increesMarks;
                                    $datasave['grace_marks'] = $increesMarks;
                                    $datasave['is_grace_marks_given'] = 1;//incress in theory
                                    PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                                    unset($datasave);
                                    $this->_updateGraceMarksThPr($val['id'], 't');
                                    continue;
                                }
                            } else if ($tempDatasave['final_result'] == $result_code_sycp) {
                                // if p 1 lesser then p_min then true.
                                if (($val['practical_marks'] + $increesMarks) == $marksarr['PR_MIN']) {
                                    $datasave['total_marks'] = $val['total_marks'] + $increesMarks;
                                    $datasave['grace_marks'] = $increesMarks;
                                    $datasave['is_grace_marks_given'] = 2;//incress in practical
                                    PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                                    unset($datasave);
                                    $this->_updateGraceMarksThPr($val['id'], 'p');
                                    continue;
                                }
                            }
                        }
                    } //12th course end
                }
            }
        }
        /* Grace Marks End */

        $resultdata = $students = PrepareExamSubject::whereIn("student_id", $studentLists)
            ->where($conditions)->whereNull("deleted_at")->orderBy("id")
            ->get(['student_id', 'enrollment', "subject_id", "id", "final_result", "sessional_marks", "sessional_marks_reil_result", "old_theory_marks", "is_grace_marks_given", "grace_marks", "total_marks", "course", "theory_marks", "practical_marks"])
            ->toArray();

        if (!empty($resultdata)) {
            foreach ($resultdata as $key => $val) {
                $subjectId = $subjectid = $val['subject_id'];
                $marksarr = $this->resultprocess_component_obj->getSubjectMaxMinMarksMaster($subjectId, 81);
                $result_code_syct = "";
                $result_code_sycp = "";
                $result_code_syc = "";
                if (isset($subjectid) && in_array($subjectid, array_keys($subjectdataarr))) {
                    $result_code_syct = "SYCT";
                    $result_code_sycp = "SYCP";
                    $result_code_syc = "SYC";
                } else {
                    $result_code_syct = "SYC";
                    //$result_code_sycp = "";
                    $result_code_syc = "SYC";
                }


                $savedata = array();
                $savedata['is_examsub_migrated'] = 0;
                if ($val['total_marks'] > $marksarr['GT_MAX']) {
                    $savedata['final_result'] = 'RWH';
                    PrepareExamSubject::where('id', '=', $val['id'])->update($savedata);
                    continue;
                }
                if ($val['course'] == 10) {
                    $markspercent = ($val['total_marks'] * 100) / $marksarr['GT_MAX'];
                    if ($val['total_marks'] >= $marksarr['GT_MIN'] && $val['total_marks'] <= 100) {
                        $datasave['final_result'] = 'P';
                        $datasave['is_final_res_updated'] = '1';
                        PrepareExamSubject::where('id', '=', $val['id'])->update($datasave);
                        unset($datasave);
                        continue;
                    }
                    if ($val['total_marks'] < $marksarr['GT_MIN'] && $val['total_marks'] >= 0) {
                        $datasave['final_result'] = 'SYC';
                        $datasave['is_final_res_updated'] = '1';
                        PrepareExamSubject::where('id', '=', $val['id'])->update($datasave);
                        unset($datasave);
                        continue;
                    }
                }

                if ($val['course'] == 12) {
                    /* if(in_array($val['subject_id'],array(3,4,7,13,16,23,24,25,28,33,35,37,38,39))){
						if(($val['theory_marks'] == 0 || $val['theory_marks'] == 999) && ($val['practical_marks'] == 0 || $val['practical_marks'] == 999)){
							$datasave = array();
							$datasave['final_result'] = $result_code_syc;
							PrepareExamSubject::where('id', '=', $val['id'])->update($datasave);
							unset($datasave);
							continue;
						}
					}else{
						if($val['theory_marks'] == 0 || $val['theory_marks'] == 999){
							$datasave = array();
							$datasave['final_result'] = $result_code_syct;
							PrepareExamSubject::where('id', '=', $val['id'])->update($datasave);
							unset($datasave);
							continue;
						}
					} */

                    if (in_array($val['subject_id'], array(3, 4, 7, 13, 16, 23, 24, 25, 28, 33, 35, 37, 38, 39))) {
                        if (($val['theory_marks'] == 0 || $val['theory_marks'] == 999) && ($val['practical_marks'] == 0 || $val['practical_marks'] == 999)) {
                            $datasave = array();
                            $datasave['final_result'] = $result_code_syc;
                            PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                            unset($datasave);
                            continue;
                        } else if (($val['theory_marks'] == 0 || $val['theory_marks'] == 999)) {
                            $datasave = array();
                            $datasave['final_result'] = $result_code_syct;
                            PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                            unset($datasave);
                            // continue;
                        } else if ($val['practical_marks'] == 0 || $val['practical_marks'] == 999) {
                            $datasave = array();
                            $datasave['final_result'] = $result_code_sycp;
                            PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                            unset($datasave);
                            // continue;
                        }
                    } else {
                        if (($val['theory_marks'] == 0 || $val['theory_marks'] == 999)) {
                            $datasave = array();
                            $datasave['final_result'] = $result_code_syc;
                            PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                            unset($datasave);
                            continue;
                        }
                    }


                    if ($val['theory_marks'] == 999) {
                        $val['theory_marks'] = 0;
                    }

                    if ($val['practical_marks'] == 999) {
                        $val['practical_marks'] = 0;
                    }
                    if ($val['sessional_marks_reil_result'] == 999) {
                        $val['sessional_marks_reil_result'] = 0;
                    }
                    $theorymarks = 0;
                    if (is_numeric($val['theory_marks']) && is_numeric($val['sessional_marks_reil_result'])) {
                        $theorymarks = (@$val['theory_marks'] + @$val['sessional_marks_reil_result']);
                    } else if (is_numeric($val['theory_marks'])) {
                        $theorymarks = @$val['theory_marks'];
                    } else if (is_numeric($val['sessional_marks_reil_result'])) {
                        $theorymarks = @$val['sessional_marks_reil_result'];
                    }


                    if ($val['is_grace_marks_given'] == 1) {
                        $theorymarks = $theorymarks + $val['grace_marks'];
                    }

                    // if(in_array($subjectid,array_keys($subjectdataarr))){
                    if (in_array($val['subject_id'], array(3, 4, 7, 13, 16, 23, 24, 25, 28, 33, 35, 37, 38, 39))) {
                        $practicalmarks = 0;
                        if (isset($val['practical_marks'])) {
                            $practicalmarks = $val['practical_marks'];
                        }
                        /* Change as per issue start on date 07-08-2023 */
                        if (isset($val['practical_marks'])) {
                            if ($val['is_grace_marks_given'] == 2) {
                                $practicalmarks = $val['practical_marks'] + $val['grace_marks'];
                            }
                        }
                        /* Change as per issue end on date 07-08-2023 */
                        //($val['practical_marks']==0)?0:(($val['practical_marks']*100)/$marksarr['PR_MAX']);

                        if (($theorymarks >= $marksarr['TH_MIN'] && $theorymarks <= $marksarr['TH_MAX']) && ($practicalmarks >= $marksarr['PR_MIN'] && $practicalmarks <= $marksarr['PR_MAX'])) {
                            $datasave = array();
                            $datasave['final_result'] = 'P';
                            PrepareExamSubject::where('id', '=', $val['id'])->update($datasave);
                            unset($datasave);
                            continue;
                        } else {
                            if ($practicalmarks < $marksarr['PR_MIN'] && $theorymarks < $marksarr['TH_MIN']) {
                                $datasave = array();
                                $datasave['final_result'] = $result_code_syc;
                                PrepareExamSubject::where('id', '=', $val['id'])->update($datasave);
                                unset($datasave);
                                continue;
                            }
                            if ($theorymarks < $marksarr['TH_MIN']) {
                                $datasave = array();
                                $datasave['final_result'] = $result_code_syct;
                                PrepareExamSubject::where('id', '=', $val['id'])->update($datasave);
                                unset($datasave);
                                continue;
                            }
                            if ($theorymarks > $marksarr['TH_MAX']) {
                                $datasave = array();
                                $datasave['final_result'] = 'RWH';
                                PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                                unset($datasave);
                                continue;
                            }
                            if ($practicalmarks < $marksarr['PR_MIN']) {
                                $datasave = array();
                                $datasave['final_result'] = $result_code_sycp;
                                PrepareExamSubject::where('id', '=', $val['id'])->update($datasave);
                                unset($datasave);
                                continue;
                            }
                            if ($practicalmarks > $marksarr['PR_MAX']) {
                                $datasave = array();
                                $datasave['final_result'] = 'RWH';
                                PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                                unset($datasave);
                                continue;
                            }
                        }
                    } else {
                        // if(($theorymarks >= $marksarr['TH_MIN'] && $theorymarks <= $marksarr['TH_MAX'])){
                        if (($val['total_marks'] >= $marksarr['TH_MIN'] && $val['total_marks'] <= $marksarr['TH_MAX'])) {
                            $datasave = array();
                            $datasave['final_result'] = 'P';
                            PrepareExamSubject::where('id', '=', $val['id'])->update($datasave);
                            unset($datasave);
                            //$this->PrepareExamSubject->updateAll(array('PrepareExamSubject.final_result'=>'PASS'),array('PrepareExamSubject.enrollment'=>$val['enrollment'],'PrepareExamSubject.subject_id'=>$val['subject_id']));
                        } else {
                            $datasave = array();
                            $datasave['final_result'] = $result_code_syc;
                            PrepareExamSubject::where('id', '=', $val['id'])->update($datasave);
                            unset($datasave);
                        }
                    }
                }
                $counter++;
            }
        } else {
            //echo "Completed";
        }
        echo "<h1>Step 6 Process Result Updated " . $counter . "</h1>";
        $newOffset = $offset + $limit;
        $newOffset = $offset;
        $baseAction = "final_result";
        $this->setAndHitNextUrl($baseAction, $is_supplementary, $course, $newOffset, $limit);
        die;
    }

    public function final_result($is_supplementary = 0, $course = null, $offset = 0, $limit = 3000, Request $request)
    {
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");

        $stallotmentarr = $studentLists = $this->_getValidStudentList($is_supplementary, $course, $offset, $limit);
        // dd($studentLists);
        $subjects = $this->subjectList($course);
        $subjectCodes = $this->subjectCodeList($course);
        $subjectTypes = $this->subjectTypeList($course);
        $result_date = Config::get("global.result_date");
        $counter = 1;

        if (!empty($stallotmentarr)) {
            foreach ($stallotmentarr as $student_id => $enrollment) {
                $adm_type = Student::where("id", "=", $student_id)->whereNull('deleted_at')->first(['id', 'adm_type']);

                if (@$adm_type && !empty($adm_type)) {
                    $adm_type = $adm_type->toArray();
                } else {
                    $adm_type = array();
                }

                if (empty($adm_type)) {
                    $admission_type = 1;
                } else {
                    $admission_type = $adm_type['adm_type'];
                }

                if (in_array($admission_type, array(1, 2, 3, 4, 5))) {
                    $conditions = array();
                    //$langconditions['TenthPrepareExamSubject.exam_year'] = Configure::read('Site.default_academicyear_id');//Configure::read('Site.default_academicyear_id');
                    $conditions['prepare_exam_subjects.student_id'] = $student_id;
                    $conditions ["prepare_exam_subjects.exam_year"] = $current_admission_session_id;
                    $conditions ["prepare_exam_subjects.exam_month"] = $current_exam_month_id;
                    $conditions['prepare_exam_subjects.is_supplementary'] = $is_supplementary;
                    if (isset($course) && !empty($course)) {
                        $conditions['prepare_exam_subjects.course'] = $course;
                    }
                    // $enrollmentsubjects = PrepareExamSubject::where($conditions)->whereNull("deleted_at")
                    // 			->orderBy("id")
                    // 			->get(['student_id','enrollment',"subject_id","id","final_result","sessional_marks","sessional_marks_reil_result","old_theory_marks","toc","total_marks"])
                    // 			->toArray();
                    $enrollmentsubjects = PrepareExamSubject::where($conditions)->whereNull("deleted_at")
                        ->orderBy("exam_year", "DESC")
                        ->orderBy("exam_month", "DESC")
                        ->groupBy("student_id")
                        ->groupBy("subject_id")
                        ->get(['student_id', 'enrollment', "subject_id", "id", "final_result", "sessional_marks", "sessional_marks_reil_result", "old_theory_marks", "toc", "total_marks"])
                        ->toArray();

                    // dd($enrollmentsubjects);


                    $compulsarysubjects = array();
                    $additionalsubjects = array();
                    $compulsarylangsubjects = array();
                    $additionallangsubjects = array();

                    $tocNorm = $toc_compulsarysubjects = array();
                    $tocLang = $toc_compulsarylangsubjects = array();
                    $subject_result = array();
                    foreach ($enrollmentsubjects as $ensubkey => $ensubval) {
                        $currentSubjectType = $subjectTypes[$ensubval['subject_id']];
                        $subject_result[$ensubval['subject_id']] = $ensubval['final_result'];
                        if ($currentSubjectType == 'B') { // Normal subject
                            if ($ensubval['toc'] == 1) {
                                $tocNorm[] = $ensubval['subject_id'];
                                $toc_compulsarysubjects[$ensubval['subject_id']] = $ensubval['total_marks'];
                            } else {
                                $compulsarysubjects[$ensubval['subject_id']] = $ensubval['total_marks'];
                            }
                        } else if ($currentSubjectType == 'A') {  // Language subject
                            if ($ensubval['toc'] == 1) {
                                $tocLang[] = $ensubval['subject_id'];
                                $toc_compulsarylangsubjects[$ensubval['subject_id']] = $ensubval['total_marks'];
                            } else {
                                $compulsarylangsubjects[$ensubval['subject_id']] = $ensubval['total_marks'];
                            }
                        }
                    }
                    arsort($toc_compulsarysubjects);
                    arsort($toc_compulsarylangsubjects);

                    arsort($compulsarysubjects);
                    arsort($compulsarylangsubjects);

                    $toc_subjects = array();
                    $main_subjects = array();

                    if (count($compulsarysubjects) > 0 || count($compulsarylangsubjects) > 0) {
                        $main_subjects = array_combine(array_merge(array_keys($compulsarysubjects), array_keys($compulsarylangsubjects)), array_merge(array_values($compulsarysubjects), array_values($compulsarylangsubjects)));
                        arsort($main_subjects);
                    }

                    $mainlangsub = array();
                    $mainsub = array();
                    $additionalsubjects = array();

                    /* if(count($main_subjects) > 0){
						foreach($main_subjects as $mskey=>$msval){
							$totalCounding = count($toc_compulsarysubjects) + count($toc_compulsarylangsubjects);

							if($totalCounding < 5 && count($toc_compulsarylangsubjects) < 2 && in_array($mskey,array_keys($compulsarylangsubjects))){
								$toc_compulsarylangsubjects[$mskey] = $msval;

							}else if($totalCounding < 5 && in_array($mskey,array_keys($compulsarysubjects)) && ((count($toc_compulsarylangsubjects) < 2 && count($toc_compulsarysubjects) < 4) || (count($toc_compulsarylangsubjects) < 3 && count($toc_compulsarysubjects) < 3))){
								//echo $mskey;die;
								$toc_compulsarysubjects[$mskey] = $msval;
							}else{
								$additionalsubjects[$mskey] = $msval;
							}
						}
					} */

                    if (count($main_subjects) > 0) {
                        foreach ($main_subjects as $mskey => $msval) {
                            $totalCounding = count($mainlangsub) + count($mainsub);

                            if ($totalCounding < 5 && in_array($mskey, array_keys($compulsarylangsubjects)) && count($mainlangsub) < 2) {
                                $mainlangsub[$mskey] = $msval;
                            } else if ($totalCounding < 5 && in_array($mskey, array_keys($compulsarysubjects)) && ((count($mainlangsub) < 2 && count($mainsub) < 4) || (count($mainlangsub) < 3 && count($mainsub) < 3))) {
                                $mainsub[$mskey] = $msval;
                            } else {
                                $additionalsubjects[$mskey] = $msval;
                            }
                        }
                    }
                    $normArrResult = $langArrResult = $resultAddiSortArr = $resultAddiArr = $resultAddiArrTemp = $resultArrForSort = $resultTempArr = array();

                    foreach ($toc_compulsarylangsubjects as $tk => $tval) {
                        $resultTempArr[$tk] = $subject_result[$tk];
                        $resultArrForSort[$tk] = $tval;
                        $langArrResult[$tk] = $subject_result[$tk];
                    }
                    foreach ($toc_compulsarysubjects as $tk => $tval) {
                        $resultTempArr[$tk] = $subject_result[$tk];
                        $resultArrForSort[$tk] = $tval;
                        $normArrResult[$tk] = $subject_result[$tk];
                    }
                    foreach ($mainlangsub as $tk => $tval) {
                        $resultTempArr[$tk] = $subject_result[$tk];
                        $resultArrForSort[$tk] = $tval;
                        $langArrResult[$tk] = $subject_result[$tk];
                    }
                    foreach ($mainsub as $tk => $tval) {
                        $resultTempArr[$tk] = $subject_result[$tk];
                        $resultArrForSort[$tk] = $tval;
                        $normArrResult[$tk] = $subject_result[$tk];
                    }

                    $countLangAddi = 0;
                    foreach ($additionalsubjects as $tk => $tval) {
                        $resultAddiArr[$tk] = $subject_result[$tk];
                        $resultAddiArrTemp[$tk] = $subject_result[$tk];
                        if (isset($compulsarylangsubjects[$tk])) {
                            $countLangAddi++;
                        }
                    }
                    $tocChk = array_merge($tocNorm, $tocLang);

                    // pr($resultArrForSort);

                    asort($resultArrForSort);
                    // echo "Additinal Arr ";
                    // pr($resultAddiArr);
                    /*Sort result array by total marks  */
                    $result_arr = array();
                    $countLagSubInRes = 0;
                    foreach ($resultArrForSort as $subId => $totalM) {
                        $result_arr[$subId] = $resultTempArr[$subId];
                        if (isset($compulsarylangsubjects[$subId])) {
                            $countLagSubInRes++;
                        }
                    }
                    // Final udpate in the exam year 124 and month 1 start
                    $countOfComLanSubjects = count($compulsarylangsubjects);
                    $countOfTocLanSubjects = count($toc_compulsarylangsubjects);
                    if (count($toc_compulsarylangsubjects) > 0 || count($compulsarylangsubjects) > 0) {
                        $langsubjects = array_combine(
                            array_merge(
                                array_keys($compulsarylangsubjects),
                                array_keys($toc_compulsarylangsubjects)
                            ),
                            array_merge(
                                array_values($compulsarylangsubjects),
                                array_values($toc_compulsarylangsubjects))
                        );
                        //arsort($main_subjects);
                    }
                    //$totalLanSubjects = $countOfComLanSubjects + $countOfTocLanSubjects;
                    $totalLanSubjects = count($langsubjects);
                    // Final udpate in the exam year 124 and month 1 end
                    if ($course == 10) {
                        if (!empty($resultAddiArr)) {
                            foreach ($resultAddiArr as $index => $addi) {
                                $resultAddiSortArr[$index] = $additionalsubjects[$index];
                                if ($addi == "P" && (count($result_arr) > 1) && in_array("SYC", $result_arr)) {
                                    if (count($compulsarylangsubjects) > 2) {
                                        if (!isset($compulsarylangsubjects[$index])) {
                                            $resArrindex = array_search("SYC", $result_arr);
                                            $resultAddiArr[$resArrindex] = $result_arr[$resArrindex];
                                            $result_arr[$index] = $resultAddiArr[$index];
                                            unset($result_arr[$resArrindex]);
                                            unset($resultAddiArr[$index]);
                                        }
                                    } else {
                                        $resArrindex = array_search("SYC", $result_arr);
                                        if (isset($compulsarylangsubjects[$index]) && $resultAddiArr[$index] == "p") {
                                            if ($countLagSubInRes < 2) {
                                                $resultAddiArr[$resArrindex] = $result_arr[$resArrindex];
                                                $result_arr[$index] = $resultAddiArr[$index];
                                                unset($result_arr[$resArrindex]);
                                                unset($resultAddiArr[$index]);
                                            }
                                        } else {
                                            if ($countLagSubInRes == 1 && isset($compulsarylangsubjects[$resArrindex]) && $countLangAddi == 0) {
                                                continue;
                                            } else {
                                                $resultAddiArr[$resArrindex] = $result_arr[$resArrindex];
                                                $result_arr[$index] = $resultAddiArr[$index];
                                                unset($result_arr[$resArrindex]);
                                                unset($resultAddiArr[$index]);
                                            }
                                        }
                                        // $resultAddiArr[$resArrindex]=$result_arr[$resArrindex];
                                        // $result_arr[$index]=$resultAddiArr[$index];
                                        // unset($result_arr[$resArrindex]);
                                        // unset($resultAddiArr[$index]);
                                    }
                                }
                                unset($resArrindex);
                            }
                        }
                        $tempAddArr = array();
                        /* If student still failed then reorder the failed subject in DESC order by number */
                        if (count($resultAddiArr) >= 2 && (in_array("SYC", $result_arr))) {
                            $storeResultAddiArr = $resultAddiArr;
                            foreach ($storeResultAddiArr as $ind => $v) {
                                $tempAddArr[$ind] = $main_subjects[$ind];
                            }
                            // echo "Temp Additional Result ";
                            // pr($tempAddArr);
                            arsort($tempAddArr);
                            //  echo "Sort New Additional /Result ";
                            // pr($tempAddArr);
                            $resultAddiArr = array();
                            foreach ($tempAddArr as $tempind => $v) {
                                $resultAddiArr[$tempind] = $storeResultAddiArr[$tempind];
                            }
                            // echo "Result Arr";
                            foreach ($result_arr as $resArrindex => $res) {
                                if ($res != "P" && in_array("SYC", $resultAddiArr)) {
                                    if (count($compulsarylangsubjects) > 2) {
                                        $addindex = array_search("SYC", $resultAddiArr);
                                        if (!isset($compulsarylangsubjects[$resArrindex])) {
                                            if ($main_subjects[$resArrindex] < $main_subjects[$addindex]) {
                                                $result_arr[$addindex] = $resultAddiArr[$addindex];
                                                $resultAddiArr[$resArrindex] = $result_arr[$resArrindex];
                                                unset($result_arr[$resArrindex]);
                                                unset($resultAddiArr[$addindex]);
                                            }
                                        }
                                    } else {
                                        $addindex = array_search("SYC", $resultAddiArr);
                                        if (!isset($compulsarylangsubjects[$resArrindex])) {
                                            if (isset($main_subjects[$resArrindex]) && isset($main_subjects[$addindex])) {
                                                if ($main_subjects[$resArrindex] < $main_subjects[$addindex]) {
                                                    $result_arr[$addindex] = $resultAddiArr[$addindex];
                                                    $resultAddiArr[$resArrindex] = $result_arr[$resArrindex];
                                                    unset($result_arr[$resArrindex]);
                                                    unset($resultAddiArr[$addindex]);
                                                }
                                            }
                                        }
                                    }
                                    unset($addindex);
                                }
                            }
                        }
                        // echo  "==========<br/>";dd($result_arr);
                    } else {


                        if (!empty($resultAddiArr)) {
                            foreach ($resultAddiArr as $index => $addi) {
                                $resultAddiSortArr[$index] = $additionalsubjects[$index];
                                if ($addi == "P" && (count($result_arr) > 1) && (in_array("SYC", $result_arr) || in_array("SYCP", $result_arr) || in_array("SYCT", $result_arr))) {

                                    if (count($compulsarylangsubjects) > 2) {

                                        if (!isset($compulsarylangsubjects[$index])) {
                                            $resArrindex = array_search("SYCT", $result_arr);
                                            $resArrindex = (empty($resArrindex)) ? array_search("SYCP", $result_arr) : $resArrindex;
                                            $resArrindex = (empty($resArrindex)) ? array_search("SYC", $result_arr) : $resArrindex;
                                            $resultAddiArr[$resArrindex] = $result_arr[$resArrindex];
                                            $result_arr[$index] = $resultAddiArr[$index];
                                            unset($result_arr[$resArrindex]);
                                            unset($resultAddiArr[$index]);
                                        }
                                    } else {
                                        $resArrindex = array_search("SYCT", $result_arr);
                                        $resArrindex = (empty($resArrindex)) ? array_search("SYCP", $result_arr) : $resArrindex;
                                        $resArrindex = (empty($resArrindex)) ? array_search("SYC", $result_arr) : $resArrindex;

                                        if (isset($compulsarylangsubjects[$index]) && $resultAddiArr[$index] == "p") {
                                            if ($countLagSubInRes < 2) {
                                                $resultAddiArr[$resArrindex] = $result_arr[$resArrindex];
                                                $result_arr[$index] = $resultAddiArr[$index];
                                                unset($result_arr[$resArrindex]);
                                                unset($resultAddiArr[$index]);
                                            }
                                        } else {
                                            if ($countLagSubInRes == 1 && isset($compulsarylangsubjects[$resArrindex]) && $countLangAddi == 0) {
                                                continue;
                                            } else {
                                                $resultAddiArr[$resArrindex] = $result_arr[$resArrindex];
                                                $result_arr[$index] = $resultAddiArr[$index];
                                                unset($result_arr[$resArrindex]);
                                                unset($resultAddiArr[$index]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $tempAddArr = array();
                        /* If student still failed then reorder the failed subject in DESC order by number */
                        if (count($resultAddiArr) >= 2 && (in_array("SYC", $result_arr) || in_array("SYCP", $result_arr) || in_array("SYCT", $result_arr))) {

                            $storeResultAddiArr = $resultAddiArr;
                            foreach ($storeResultAddiArr as $ind => $v) {
                                $tempAddArr[$ind] = $main_subjects[$ind];
                            }
                            // echo "Temp Additional Result ";
                            // pr($tempAddArr);

                            arsort($tempAddArr);
                            //  echo "Sort New Additional /Result ";
                            // pr($tempAddArr);


                            $resultAddiArr = array();
                            foreach ($tempAddArr as $tempind => $v) {
                                $resultAddiArr[$tempind] = $storeResultAddiArr[$tempind];
                            }
                            // echo "Result Arr";
                            // pr($result_arr);

                            foreach ($result_arr as $resArrindex => $res) {
                                if ($res != "P" && (in_array("SYC", $resultAddiArr) || in_array("SYCP", $resultAddiArr) || in_array("SYCT", $resultAddiArr))) {

                                    if (count($compulsarylangsubjects) > 2) {
                                        $addindex = array_search("SYCT", $resultAddiArr);
                                        $addindex = (empty($addindex)) ? array_search("SYCP", $resultAddiArr) : $addindex;
                                        $addindex = (empty($addindex)) ? array_search("SYC", $resultAddiArr) : $addindex;
                                        if (!isset($compulsarylangsubjects[$resArrindex])) {
                                            if ($main_subjects[$resArrindex] < $main_subjects[$addindex]) {
                                                $result_arr[$addindex] = $resultAddiArr[$addindex];
                                                $resultAddiArr[$resArrindex] = $result_arr[$resArrindex];
                                                unset($result_arr[$resArrindex]);
                                                unset($resultAddiArr[$addindex]);
                                            }
                                        }
                                    } else {


                                        $addindex = array_search("SYCT", $resultAddiArr);
                                        $addindex = (empty($addindex)) ? array_search("SYCP", $resultAddiArr) : $addindex;
                                        $addindex = (empty($addindex)) ? array_search("SYC", $resultAddiArr) : $addindex;

                                        if (!isset($compulsarylangsubjects[$resArrindex])) {
                                            if ($main_subjects[$resArrindex] < $main_subjects[$addindex]) {
                                                $result_arr[$addindex] = $resultAddiArr[$addindex];
                                                $resultAddiArr[$resArrindex] = $result_arr[$resArrindex];
                                                unset($result_arr[$resArrindex]);
                                                unset($resultAddiArr[$addindex]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }


                    // echo "<pre>";
                    // print_r($mainlangsub);
                    // echo "<br>";
                    // print_r($mainsub);
                    // echo "<br>";
                    // print_r($additionalsubjects);
                    // echo "<br>";

                    $tsum = 0;
                    foreach ($result_arr as $k => $tmark) {
                        if (isset($mainlangsub[$k])) {
                            $tsum += $mainlangsub[$k];
                        } elseif (isset($mainsub[$k])) {
                            $tsum += $mainsub[$k];
                        } elseif (isset($additionalsubjects[$k])) {
                            $tsum += $additionalsubjects[$k];
                        }
                    }

                    // print_r($tsum);
                    // dd($result_arr);
                    /* END */
                    // echo $admission_type;die;
                    // echo "Additional Subject ";
                    // $this->pr($resultAddiArr);
                    // echo "Additional Result ";
                    // $this->pr($result_arr);
                    // echo "Sum: ".$tsum."<br/> ";
                    // echo "Main subject ";
                    // $this->pr($main_subjects);
                    // echo "remove die"; die;

                    // $count=$this->PrepareExamResult->find("count",array("conditions"=>array("enrollment"=>$stallotval['StudentAllotment']['enrollment'])));
                    // if($count >0){
                    // continue;
                    // }


                    $checkR = PrepareExamResult::where("student_id", "=", $student_id)->first(['id', 'student_id', 'enrollment']);


                    if ($admission_type == 3) {
                        // array('1' => 'General Admission','2' => 'Re-admission','3' => 'Part Admission','4' => 'Improvement','5' => 'Dual Admission');
                        // As per document point 4.1  Removed the $admission_type == 3 || FOR Part Admission
                        $savedata['student_id'] = $student_id;
                        $savedata['enrollment'] = $enrollment;
                        $savedata['final_result'] = 'XXXX';
                        $savedata['is_examresult_migrated'] = 0;
                        $savedata['exam_year'] = $current_admission_session_id;
                        $savedata['exam_month'] = $current_exam_month_id;
                        $savedata['course'] = $course;
                        $savedata['result_date'] = $result_date;
                        $savedata['is_supplementary'] = $is_supplementary;
                        $savedata['additional_subjects'] = (!empty($resultAddiArr)) ? serialize($resultAddiArr) : null;
                        $savedata['total_marks'] = ($tsum > 0) ? $tsum : array_sum(array_values($toc_compulsarylangsubjects)) + array_sum(array_values($toc_compulsarysubjects));
                        $savedata['student_id'] = (!empty($adm_type["id"])) ? $adm_type["id"] : 0;;
                        if (!empty($checkR)) {
                            PrepareExamResult::where('id', '=', $checkR['id'])->update($savedata);
                        } else {
                            PrepareExamResult::create($savedata);
                        }
                        unset($savedata);
                        continue;
                    }


                    if (count($compulsarylangsubjects) == 0) {
                        $savedata = array();
                        $savedata['student_id'] = $student_id;
                        $savedata['enrollment'] = $enrollment;
                        $savedata['final_result'] = 'XXXX';
                        $savedata['is_examresult_migrated'] = 0;
                        $savedata['exam_year'] = $current_admission_session_id;
                        $savedata['exam_month'] = $current_exam_month_id;
                        $savedata['result_date'] = $result_date;
                        $savedata['course'] = $course;
                        $savedata['additional_subjects'] = (!empty($resultAddiArr)) ? serialize($resultAddiArr) : null;
                        $savedata['total_marks'] = ($tsum > 0) ? $tsum : array_sum(array_values($toc_compulsarylangsubjects)) + array_sum(array_values($toc_compulsarysubjects));
                        $savedata['is_supplementary'] = $is_supplementary;
                        $savedata['student_id'] = (!empty($adm_type["id"])) ? $adm_type["id"] : 0;
                        if (@$checkR['id'] && !empty($checkR['id'])) {
                            PrepareExamResult::where('id', '=', $checkR['id'])->update($savedata);
                        } else {
                            PrepareExamResult::create($savedata);
                        }
                        unset($savedata);
                        continue;
                    }


                    // if((count($compulsarylangsubjects)+count($compulsarysubjects)) < 5){
                    if (((count($compulsarylangsubjects) + count($compulsarysubjects)) < 5) && $admission_type < 5) {
                        $savedata = array();
                        $savedata['student_id'] = $student_id;
                        $savedata['enrollment'] = $enrollment;
                        $savedata['final_result'] = 'XXXX';
                        $savedata['is_examresult_migrated'] = 0;
                        $savedata['exam_year'] = $current_admission_session_id;
                        $savedata['exam_month'] = $current_exam_month_id;
                        $savedata['result_date'] = $result_date;
                        $savedata['course'] = $course;
                        $savedata['is_supplementary'] = $is_supplementary;
                        $savedata['additional_subjects'] = (!empty($resultAddiArr)) ? serialize($resultAddiArr) : null;
                        $savedata['total_marks'] = ($tsum > 0) ? $tsum : array_sum(array_values($toc_compulsarylangsubjects)) + array_sum(array_values($toc_compulsarysubjects));
                        $savedata['student_id'] = (!empty($adm_type["id"])) ? $adm_type["id"] : 0;

                        /* percent start */
                        $student = Student::where('students.id', $student_id)->get(['adm_type', 'course', 'id'])->first();
                        $maxMarks = 0;
                        if (@$student->adm_type) {
                            if ($student->adm_type == 5) {
                                if ($student->course == 10) {
                                    $maxMarks = 200;
                                } else if ($student->course == 12) {
                                    $maxMarks = 100;
                                }
                            } else if ($student->adm_type == 1 || $student->adm_type == 2 || $student->adm_type == 4) {
                                $maxMarks = 500;
                            }
                        }
                        $savedata['percent_marks'] = 0;
                        if (@$maxMarks > 0) {
                            $savedata['percent_marks'] = ($savedata['total_marks'] / $maxMarks) * 100;
                        }
                        /* percent end */

                        if (@$checkR['id'] && !empty($checkR['id'])) {
                            PrepareExamResult::where('id', '=', $checkR['id'])->update($savedata);
                        } else {
                            PrepareExamResult::create($savedata);
                        }
                        unset($savedata);
                        continue;
                    }


                    $savedata = array();
                    if (in_array('RWH', array_values($result_arr))) {
                        $savedata['final_result'] = 'RWH';
                    } else if (in_array('SYC', array_values($result_arr)) || in_array('SYCT', array_values($result_arr)) || in_array('SYCP', array_values($result_arr))) {
                        $savedata['final_result'] = 'XXXX';
                    } else {
                        $savedata['final_result'] = 'PASS';
                    }
                    $savedata['student_id'] = $student_id;
                    $savedata['enrollment'] = $enrollment;
                    $savedata['is_examresult_migrated'] = 0;
                    $savedata['exam_year'] = $current_admission_session_id;
                    $savedata['exam_month'] = $current_exam_month_id;
                    $savedata['course'] = $course;
                    $savedata['result_date'] = $result_date;
                    $savedata['is_supplementary'] = $is_supplementary;
                    $savedata['additional_subjects'] = (!empty($resultAddiArr)) ? serialize($resultAddiArr) : null;


                    $savedata['total_marks'] = ($tsum > 0) ? $tsum : array_sum(array_values($toc_compulsarylangsubjects)) + array_sum(array_values($toc_compulsarysubjects));
                    $savedata['student_id'] = (!empty($adm_type["id"])) ? $adm_type["id"] : 0;
                    // dd($tsum);
                    // dd($savedata);

                    /* percent start */
                    $student = Student::where('students.id', $student_id)->get(['adm_type', 'course', 'id'])->first();
                    $maxMarks = 0;
                    if (@$student->adm_type) {
                        if ($student->adm_type == 5) {
                            if ($student->course == 10) {
                                $maxMarks = 200;
                            } else if ($student->course == 12) {
                                $maxMarks = 100;
                            }
                        } else if ($student->adm_type == 1 || $student->adm_type == 2 || $student->adm_type == 4) {
                            $maxMarks = 500;
                        }
                    }
                    $savedata['percent_marks'] = 0;
                    if (@$maxMarks > 0) {
                        $savedata['percent_marks'] = ($savedata['total_marks'] / $maxMarks) * 100;
                    }
                    /* percent end */

                    if (@$checkR['id'] && !empty($checkR['id'])) {
                        PrepareExamResult::where('id', '=', $checkR['id'])->update($savedata);
                    } else {
                        PrepareExamResult::create($savedata);
                    }
                    unset($savedata);
                    continue;
                }
                $counter++;
            }
        } else {
            // echo "Completed";
            // die;
        }
        echo "<h1>Step 7 Final " . $course . "th result imported (" . $counter . ")</h1>";
        echo "<div class='countdown alerts-border' style='text-align:center;font-size: 60px;margin-top: 0px;background:linear-gradient(45deg, #a5e362, #287a7373);'>All Step Done For Supplementary Students </div>";
        /* echo "Temp Stop"; die; */
        if ($course == 10) {
            /* For next course start */
            $newOffset = $offset + $limit;
            $newOffset = $offset;
            $baseAction = "result_process_start";
            $course = 12;
            $this->setAndHitNextUrl($baseAction, $is_supplementary, $course, $newOffset, $limit);
            die;
            /* For next course start */
        }
        die;
    }
}




