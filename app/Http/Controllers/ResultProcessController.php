<?php

namespace App\Http\Controllers;
use App\Component\CustomComponent;
use App\Component\ResultProcessCustomComponent;
use App\models\Application;
use App\models\ExamResult;
use App\models\ExamSubject;
use App\models\PrepareExamResult;
use App\models\PrepareExamSubject;
use App\models\ResultTopper;
use App\models\SessionalExamSubject;
use App\models\Student;
use App\models\StudentAllotment;
use App\models\StudentAllotmentMark;
use App\models\Toc;
use Auth;
use Cache;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PDF;
use Response;
use Session;

class ResultProcessController extends Controller
{
    public $custom_component_obj = "";
    public $marksheet_component_obj = "";

    function __construct()
    {


        ini_set('max_execution_time', 0);
        ini_set("memory_limit", "-1");

        echo "<title> Fresh " . ucwords(str_replace("_", " ", Route::getCurrentRoute()->getName())) . " Time " . date('d-m-y h:i:s') . " </title>";
        echo "<style>.alerts-border {
			border: 15px #ff0000 solid;
			animation: blink 1s;
			animation-iteration-count: 500;
		}
		@keyframes blink { 50% { border-color:#fff ; }  }</style>";
        $this->custom_component_obj = new CustomComponent;
        $this->resultprocess_component_obj = new ResultProcessCustomComponent;
        $this->allow_mannual = false; //true //28067203022,26037203153 adm type 1

        $this->conditionsEnrollment = array('24009193065', '02035213062', '09054223100', '17079223173', '20060233007', '04001233003', '14001233025', '09001232002', '07009233009', '12110232010', '08008232006', '02142232002', '12207233033', '06076232004', '17026233004', '30028233009', '23031232006', '01033235008', '17002233021', '17114233138', '15001233031', '17114233118', '03002232008', '04025233081', '12011233025', '21003233083', '06046233026', '18005232053', '06071233047', '25004233035', '12079233029', '06078233051', '19042232040', '20059233028', '28001233044', '12011233060', '19042233055', '26069233058', '21057232003', '07009233088', '09062232009', '14004233390', '03002233027', '20045233086', '14004233119', '09035233015', '12011233132', '12281233009', '12011232094', '21051233070', '20028233030', '28083233007', '04025233160', '01078233037', '09024232097', '14004233138', '04045233155', '12207233085', '01076233094', '04032233046', '09001233091', '21003233126', '12011233117', '32036233017', '04045233202', '12141233008', '12027232226', '01001233035', '17108233059', '12121233011', '02116233035', '04051233030', '19001232025', '10071233012', '04025233232', '04047233024', '11048233063', '14007233122', '13003232061', '32001233075', '14026233113', '32035233080', '14004233194', '17014233053', '09035232041', '26072232055', '23032232033', '21039233057', '28001233087', '10003232002', '01046233259', '06061232132', '17003233103', '08018233022', '02096233028', '20060233290', '18038232086', '04025233310', '23014233100', '04001233389', '04001233391', '01020233355', '23020232105', '06065233083', '01065233098', '01065233083', '09055233085', '26001233188', '11044233150', '04020233152', '04067233045', '12207233145', '26001232191', '04001233439', '32035232130', '17041232207', '07010233216', '08019233072', '01064232088', '17026233043', '26029233169', '17006233124', '06060233101', '11051233018', '32035233171', '06067232080', '17004232404', '04053233065', '12141232017', '01020233466', '32035233182', '14012233210', '08020233091', '14028232142', '01075233109', '30025232078', '17002232169', '12141232018', '06002233169', '25005233016', '12141232019', '14026233179', '26071233150', '26020233292', '26045233078', '01079232138', '01065233181', '04025233470', '04034232038', '04065232252', '30008233155', '01020233523', '06078233212', '19042233179', '04032233172', '14029233122', '20059233195', '04001233603', '06047232129', '14029233148', '26037233132', '12058233157', '02035233175', '04051233094', '07063233199', '20031233334', '19042233199', '21003233282', '01020233574', '04057233235', '30023233634');
        // $this->conditionsEnrollment = array('04022223089');
        $this->appUrl = Config::get("global.APP_URL");
        $this->baseStart = $this->appUrl . "result_process/";


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
        // $offset = 0;// $limit = 200;
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
        $html .= "<h2> Fresh Stuent : ";
        $html .= "<ol>";
        $baseStart = $this->appUrl . "result_process/result_process_start/";
        $baseEnd = "/" . $offset . "/" . $limit;

        $html .= "<li><a target='_blank' href=" . $baseStart . "0/10" . $baseEnd . "> is_supplementary=0,course=10,offset=" . $offset . ",limit=" . $limit . "</a></li><br>";

        $html .= "<li><a target='_blank' href=" . $baseStart . "0/12" . $baseEnd . "> is_supplementary=0,course=12,offset=" . $offset . ",limit=" . $limit . "</a></li><br>";

        // $html .= "<li><a target='_blank'  href=". $baseStart . "1/10" . $baseEnd . "> is_supplementary=1,course=10,offset=" . $offset .",limit=" . $limit ."</a></li><br>";

        // $html .= "<li><a target='_blank' href=". $baseStart . "1/12" . $baseEnd . "> is_supplementary=1,course=12,offset=" . $offset .",limit=" . $limit ."</a></li><br>";

        $html .= "</ol>";
        $html .= "</h2><br>URL: result_process_start\is_supplementary\course\offset\limit<br>";
        $otherLink = $this->appUrl . "supp_result_process/show_combination/0/1";
        $html .= "Other Result <li><a target='_blank' href=" . $otherLink . "> " . $otherLink . " </a></li><br>";
        echo $html;
        die;
    }

    /* Fresh Student Of 10th and 12th start */
    /* Step 1 Move data from exam subject prepare_exam_subjects */

    public function result_process_start($is_supplementary = 0, $course = null, $offset = 0, $limit = 3000, Request $request)
    {
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");

        $studentLists = $this->_getValidStudentList($is_supplementary, $course, $offset, $limit);
        $subjects = $this->subjectList($course);
        $subjectCodes = $this->subjectCodeList($course);
        $counter = 0;
        try {
            $exconditions = array();
            $exconditions['exam_year'] = $current_admission_session_id;
            $exconditions['exam_month'] = $current_exam_month_id;
            $tempStudentIds = [];
            if (count($studentLists) > 0) {
                $tempStudentIds = array_keys($studentLists->toArray());
            }

            if($current_exam_month_id == 2){
                $sessionalExamSubjectDetails = SessionalExamSubject::where($exconditions)->whereIn('student_id', $tempStudentIds)->get();
                if (count($sessionalExamSubjectDetails) > 0) {
                    foreach ($sessionalExamSubjectDetails as $key => $detail) {
                        $exconditions = array();
                        $exconditions['exam_year'] = $current_admission_session_id;
                        $exconditions['exam_month'] = $current_exam_month_id;
                        $exconditions['student_id'] = $detail->student_id;
                        $exconditions['subject_id'] = $detail->subject_id;

                        $studentsessionalmarks = array('sessional_marks' => @$detail->sessional_marks, 'remarks' => "Syc api marks to main");
                        ExamSubject::whereNull('deleted_at')->where($exconditions)->update($studentsessionalmarks);
                    }
                }
            }


            foreach ($studentLists as $student_id => $enrollment) {
                $exconditions = array();
                $exconditions['student_id'] = $student_id;
                $exconditions['exam_year'] = $current_admission_session_id;
                $exconditions['exam_month'] = $current_exam_month_id;
                $examSubjectDetails = ExamSubject::whereNull('deleted_at')->where($exconditions)->get(['enrollment', 'student_id', 'subject_id', 'final_practical_marks', 'final_theory_marks', 'sessional_marks', 'sessional_marks_reil_result', 'subject_type', 'total_marks', 'final_result', 'exam_year', 'exam_month']);

                if (@$examSubjectDetails && !empty($examSubjectDetails)) {
                    $examSubjectDetails = $examSubjectDetails->toArray();
                } else {
                    $examSubjectDetails = array();
                }
                $studentDetails = Student::where('id', "=", $student_id)->first(["enrollment", "ai_code"]);

                if (@$studentDetails && !empty($studentDetails)) {
                    $studentDetails = $studentDetails->toArray();
                } else {
                    $studentDetails = array();
                }


                if (isset($studentDetails['ai_code']) && !empty($studentDetails['ai_code'])) {
                } else {
                    continue;//echo $student_id . " student id not found  <br>";die;
                }

                foreach (@$examSubjectDetails as $exkey => $exval) {
                    $savedata = array();
                    $savedata['student_id'] = $exval['student_id'];

                    if (@$exval['enrollment'] && !empty($exval['enrollment'])) {
                        $savedata['enrollment'] = $exval['enrollment'];
                    } else {
                        $savedata['enrollment'] = $exval['enrollment'] = $studentDetails['enrollment'];
                    }
                    if ($savedata['enrollment'] == '') {
                        $savedata['enrollment'] = 'enrollment';
                    }
                    $savedata['subject_id'] = $exval['subject_id'];
                    if (@$exval['subject_id'] && @$subjects[$exval['subject_id']]) {
                        $savedata['subject_name'] = $subjects[$exval['subject_id']];
                        $savedata['subject_code'] = $subjectCodes[$exval['subject_id']];
                    }
                    $savedata['sessional_marks'] = $exval['sessional_marks'];
                    $savedata['sessional_marks_reil_result'] = $exval['sessional_marks_reil_result'];
                    $savedata['theory_marks'] = $exval['final_theory_marks'];
                    // $savedata['practical_marks'] = $exval['final_practical_marks'];

                    if (!in_array($exval['subject_id'], array(3, 4, 7, 13, 16, 23, 24, 25, 28, 33, 35, 37, 38, 39))) {
                        $savedata['practical_marks'] = 999;
                    } else {
                        if (@$exval['final_practical_marks']) {
                            $savedata['practical_marks'] = $exval['final_practical_marks'];
                        }
                    }
                    $savedata['exam_year'] = $current_admission_session_id;
                    $savedata['exam_month'] = $current_exam_month_id;
                    $savedata['subject_type'] = $exval['subject_type'];
                    $savedata['total_marks'] = $exval['total_marks'];
                    $savedata['final_result'] = $exval['final_result'];
                    $savedata['ai_code'] = $studentDetails['ai_code'];
                    $savedata['course'] = $course;
                    $savedata['status'] = 1;
                    $savedata['student_id'] = $exval['student_id'];

                    PrepareExamSubject::create($savedata);
                }
                $counter++;
            }
        } catch (Exception $e) {
            die('Error loading file : ' . $e->getMessage());
        }
        echo "<h1>Step 1 : Prepare exam subjects data imported " . $counter . "</h1>";
//         echo "TTT";die;
        if (@$is_supplementary) {
            // $this->update_practical_theory_marks($is_supplementary,$course,$offset,$limit,$request);
        }
        $newOffset = $offset + $limit;
        $newOffset = $offset;
        $baseAction = "update_practical_theory_marks";
        $this->setAndHitNextUrl($baseAction, $is_supplementary, $course, $newOffset, $limit);
        die;
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
                    $studentLists = array_flip($studentLists);
                    $studentLists = StudentAllotmentMark::join('students', 'students.id', '=', 'student_allotment_marks.student_id')
                        ->join('student_allotments', 'student_allotments.student_id', '=', 'student_allotment_marks.student_id')->whereIn("student_allotments.student_id", $studentLists)->where('student_allotments.course', $course)->whereNull('students.student_status_at_different_level')->whereNull('student_allotments.deleted_at')->where($conditions)->groupBy("student_id")->take($limit)->skip($offset)->pluck("student_allotments.enrollment", "student_allotments.student_id");
                    return $studentLists;
                });
            }
        } else {
            $currentActionName = \Request::route()->getName();
            $cacheName = $course . "_" . $limit . "_" . $offset . "_" . $is_supplementary;
            if ($currentActionName == "result_process_start" || $currentActionName == "final_result") {
                Cache::forget($cacheName);
            }
            if (Cache::has($cacheName)) {
                $studentLists = Cache::get($cacheName);
            } else {
                $studentLists = Cache::rememberForever($cacheName, function () use ($conditions, $is_supplementary, $course, $offset, $limit) {
                    $studentLists = StudentAllotmentMark::
                    join('students', 'students.id', '=', 'student_allotment_marks.student_id')
                        ->join('student_allotments', 'student_allotments.student_id', '=', 'student_allotment_marks.student_id')->whereNull('students.student_status_at_different_level')->whereNull('student_allotments.deleted_at')->where('student_allotments.course', $course)->where($conditions)->groupBy("student_id")->take($limit)->skip($offset)->pluck("student_allotments.enrollment", "student_allotments.student_id");
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

        $studentLists = StudentAllotmentMark::join('student_allotments', 'student_allotments.student_id', '=', 'student_allotment_marks.student_id')
            ->whereIn('student_allotments.enrollment', $conditionsEnrollment)
            ->where('student_allotments.course', $course)
            ->whereNull('student_allotments.deleted_at')
            ->where($conditions)
            ->groupBy("student_id")->take($limit)->skip($offset)
            ->pluck("student_allotments.enrollment", "student_allotments.student_id");


        return $studentLists;
    }


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
		$('.countdown').html('Next Screen Hit in ' + minutes + ':' + seconds + ' Seconds ');
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
        $students = PrepareExamSubject::whereIn("student_id", $studentLists)
            ->where($conditions)->whereNull("deleted_at")->orderBy("id")
            ->get(['student_id', 'enrollment', "subject_id", "id", "final_result", "sessional_marks", "sessional_marks_reil_result", "old_theory_marks"]);

        if (@$students && !empty($students)) {
            $students = $students->toArray();
        } else {
            $students = array();
        }
        try {
            foreach (@$students as $sekey => $seval) {
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
                    /** Why we are using this line, We can do same for practical as we did for theory */
                    $practicalMark = (!empty($theoryPracticals['final_practical_marks']) && $theoryPracticals['final_practical_marks'] != 0) ? $theoryPracticals['final_practical_marks'] : 999;

                    $savedata['practical_marks'] = (!empty($theoryPracticals['practical_absent'])) ? 999 : $practicalMark;
                    $savedata['theory_marks'] = (!empty($theoryPracticals['theory_absent'])) ? 999 : $theoryPracticals['final_theory_marks'];

                    if (@$savedata['theory_marks']) {
                        $savedata['old_theory_marks'] = $savedata['theory_marks'];
                    }
                    $savedata['is_supplementary'] = (!empty($theoryPracticals['is_supplementary'])) ? $theoryPracticals['is_supplementary'] : 0;
                    PrepareExamSubject::where('id', $seval['id'])->update($savedata);
                    unset($savedata);
                }
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
        //lokesh sessional marks will calculate only for stream1
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");

        if ($current_exam_month_id == 1) {
            $conditions = array();
            $studentLists = $this->_getValidStudentList($is_supplementary, $course, $offset, $limit)->toArray();
            $studentLists = array_flip($studentLists);


            $conditions = null;
            $conditions ["prepare_exam_subjects.exam_year"] = $current_admission_session_id;
            $conditions ["prepare_exam_subjects.exam_month"] = $current_exam_month_id;
            $conditions['prepare_exam_subjects.is_supplementary'] = $is_supplementary;
            $conditions['prepare_exam_subjects.toc'] = 0;
            if (isset($course) && !empty($course)) {
                $conditions['prepare_exam_subjects.course'] = $course;
            }
            $counter = 1;
            $students = PrepareExamSubject::whereIn("student_id", $studentLists)
                ->where($conditions)->whereNull("deleted_at")->orderBy("id")
                ->get(['student_id', 'enrollment', "id", "subject_id", "old_theory_marks", "sessional_marks"])
                ->toArray();

            if (!empty($students)) {
                foreach (@$students as $data) {
                    $subject_id = $subjectId = $data["subject_id"];
                    $id = $data["id"];
                    $marksArr = $this->resultprocess_component_obj->getSubjectMaxMinMarksMaster($subjectId, 81);

                    $strMax = $marksArr['STR_MAX'];
                    $theoryMarks = (int)$data["old_theory_marks"];
                    $newsessionalMarks = $sessionalMarks = (int)$data["sessional_marks"];
                    //$maxThRa=$marksArr['TH_MAX_RATIO'];
                    if ($theoryMarks >= 0 && $theoryMarks <= 100) {
                        //$thmakspercent = ($theoryMarks/$marksArr['TH_MAX'])*100;
                        $newtheoryMarks = round($theoryMarks * 0.9);
                        if ($theoryMarks < $marksArr['TH_MIN'] && ($sessionalMarks == 999 || $sessionalMarks == 0)) {
                            /*Point 2.5 */
                            if (in_array($strMax, array("8", "9", "10"))) {
                                $newsessionalMarks = 3;
                            } elseif (in_array($strMax, array("4", "6"))) {
                                $newsessionalMarks = 2;
                            } elseif (in_array($strMax, array("3"))) {
                                $newsessionalMarks = 1;
                            } else {
                                $newsessionalMarks = 0;
                            }
                        } else if ($sessionalMarks > 0 && $sessionalMarks <= 10) {
                            if (($theoryMarks >= 0 && $theoryMarks <= 100) || ($theoryMarks == 999)) {
                                if ($sessionalMarks <= 3 && in_array($strMax, array("8", "9", "10"))) {
                                    $newsessionalMarks = 3;
                                } elseif ($sessionalMarks <= 2 && in_array($strMax, array("4", "6"))) {
                                    $newsessionalMarks = 2;
                                } elseif ($sessionalMarks <= 1 && in_array($strMax, array("3"))) {
                                    $newsessionalMarks = 1;
                                } else {
                                    $newsessionalMarks = $sessionalMarks;
                                }
                            }
                        } elseif ($sessionalMarks == 999 || $sessionalMarks == 0) {
                            if (($theoryMarks >= 0 && $theoryMarks <= 100) || ($theoryMarks == 999)) {
                                if (in_array($strMax, array("8", "9", "10"))) {
                                    $newsessionalMarks = 3;
                                } elseif (in_array($strMax, array("4", "6"))) {
                                    $newsessionalMarks = 2;
                                } elseif (in_array($strMax, array("3"))) {
                                    $newsessionalMarks = 1;
                                }
                            }
                        }
                        $sv['is_supplementary'] = (!empty($theoryPracticals['is_supplementary'])) ? $theoryPracticals['is_supplementary'] : 0;
                        $sv["theory_marks"] = $newtheoryMarks;
                        $sv["is_th_sess_updated"] = 1;
                        $sv["sessional_marks_reil_result"] = $newsessionalMarks;
                    } else {
                        if ($sessionalMarks == 999 || $sessionalMarks == 0) {
                            if (in_array($strMax, array("8", "9", "10"))) {
                                $newsessionalMarks = 3;
                            } elseif (in_array($strMax, array("4", "6"))) {
                                $newsessionalMarks = 2;
                            } elseif (in_array($strMax, array("3"))) {
                                $newsessionalMarks = 1;
                            } else {
                                $newsessionalMarks = 0;
                            }
                        } else {
                            if ($sessionalMarks <= 3 && in_array($strMax, array("8", "9", "10"))) {
                                $newsessionalMarks = 3;
                            } elseif ($sessionalMarks <= 2 && in_array($strMax, array("4", "6"))) {
                                $newsessionalMarks = 2;
                            } elseif ($sessionalMarks <= 1 && in_array($strMax, array("3"))) {
                                $newsessionalMarks = 1;
                            } else {
                                $newsessionalMarks = $sessionalMarks;
                            }
                        }
                        $sv["is_th_sess_updated"] = 1;
                        $sv["sessional_marks_reil_result"] = $newsessionalMarks;
                    }
                    PrepareExamSubject::where('id', $id)->update($sv);
                    unset($sv);
                    $counter++;
                }
            } else {
            }
        }
        echo "<h1>Step 3 : Work only for stream-1  Calculate Theory Marks (0.9) and Sessional marks (min marks) Updated " . $counter . "</h1>";
        $newOffset = $offset + $limit;
        $newOffset = $offset;
        $baseAction = "manage_toc_marks";
        $this->setAndHitNextUrl($baseAction, $is_supplementary, $course, $newOffset, $limit);
        die;
    }

    /*  Step 4 and 5 TOC Marks Migrate into PES  Note Toc Marks already done the process of calculate of theory Marks	*/
    public function manage_toc_marks($is_supplementary = 0, $course = null, $offset = 0, $limit = 3000, Request $request)
    {

        $subarr = $this->subjectCodeList($course)->toArray();

        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $conditions = null;

        $subjects = $this->subjectList($course);
        $subjectCodes = $this->subjectCodeList($course);


        $conditions["applications.toc"] = 1;
        $studentLists = $this->_getValidStudentList($is_supplementary, $course, $offset, $limit)->toArray();
        $studentLists = array_flip($studentLists);
        $arr = Student::join('applications', 'applications.student_id', '=', 'students.id')->whereIn("students.id", $studentLists)->where($conditions)->take($limit)->skip($offset)->get(["students.enrollment", "students.adm_type", "applications.student_id", "students.course", "students.ai_code"])->toArray();
        $counter = 1;


        if (!empty($arr)) {
            foreach ($arr as $arkey => $arval) {

                $conditions = array();

                $conditions['Toc.student_id'] = $arval['student_id'];

                $tocarr = Toc::with("toc_marks")->where($conditions)->first();
                if (@$tocarr) {
                    $tocarr = $tocarr->toArray();
                } else {
                    $tocarr = array();
                }

                $tocval['Toc'] = $tocarr;

                if (isset($tocval['Toc']['toc_marks'])) {
                    $tocval['TocMark'] = $tocval['Toc']['toc_marks'];
                    unset($tocval['Toc']['toc_marks']);
                }


                if (@$tocval['Toc']['board'] == 81) {

                    if (count($tocval['TocMark']) > 0) {

                        if ($arval['adm_type'] == 4) {
                            foreach ($tocval['TocMark'] as $tocmkey => $tocmval) {
                                $conditions = array();
                                $conditions['student_id'] = $arval['student_id'];
                                $conditions['course'] = $arval['course'];
                                $conditions['exam_year'] = $current_admission_session_id;
                                $conditions['exam_month'] = $current_exam_month_id;
                                $conditions['course'] = $arval['course'];
                                $conditions['is_supplementary'] = $is_supplementary;
                                $conditions['toc'] = 0;
                                //$conditions['flg'] = 3;
                                $conditions['subject_id'] = $tocmval['subject_id'];
                                $preparesubjects = PrepareExamSubject::where($conditions)->whereNull("deleted_at")->first();
                                if (@$preparesubjects && !empty($preparesubjects)) {
                                    $preparesubjects = $preparesubjects->toArray();
                                } else {
                                    $preparesubjects = array();
                                }
                                // dd($preparesubjects);
                                if (!empty($preparesubjects)) {
                                    $datasave = array();
                                    $datasave['id'] = $preparesubjects['id'];
                                    $total_theory_marks = (round($preparesubjects['old_theory_marks'] * 0.9)) + $preparesubjects['sessional_marks_reil_result'];
                                    if (($tocmval['theory'] > $total_theory_marks) || ($preparesubjects['old_theory_marks'] == 999) || $preparesubjects['old_theory_marks'] == 0) {
                                        $datasave['theory_marks'] = $tocmval['theory'];
                                        $datasave['sessional_marks_reil_result'] = 0;
                                        $datasave['sessional_marks'] = 0;
                                    }
                                    /* Start Need to be check 999 in practical marks as per theory marks 1208 */
                                    if ($preparesubjects['practical_marks'] == 999) {
                                        $preparesubjects['practical_marks'] = 0;
                                    }
                                    /* End Need to be check 999 in practical marks as per theory marks 1208 */
                                    if ($tocmval['practical'] > $preparesubjects['practical_marks']) {
                                        $datasave['practical_marks'] = $tocmval['practical'];
                                    }
                                    $datasave['flg'] = 4;
                                    $datasave['student_id'] = $arval['student_id'];
                                    $datasave['ai_code'] = $arval['ai_code'];
                                    PrepareExamSubject::where('prepare_exam_subjects.id', '=', $datasave['id'])->update($datasave);
                                } else {
                                    $datasave = array();
                                    $datasave['toc'] = 1;
                                    $datasave['theory_marks'] = $tocmval['theory'];
                                    $datasave['practical_marks'] = $tocmval['practical'];
                                    $datasave['total_marks'] = $tocmval['total_marks'];
                                    $datasave['enrollment'] = $arval['enrollment'];
                                    $datasave['course'] = $arval['course'];
                                    $datasave['subject_id'] = $tocmval['subject_id'];
                                    $datasave['subject_code'] = @$subarr[$tocmval['subject_id']];
                                    if (@$tocmval['subject_id'] && @$subjects[$tocmval['subject_id']]) {
                                        $datasave['subject_name'] = $subjects[$tocmval['subject_id']];
                                        $datasave['subject_code'] = $subjectCodes[$tocmval['subject_id']];
                                    }
                                    $datasave['final_result'] = '';
                                    $datasave['exam_year'] = $current_admission_session_id;
                                    $datasave['exam_month'] = $current_exam_month_id;
                                    $datasave['is_supplementary'] = $is_supplementary;
                                    $datasave['total_new'] = 0;
                                    $datasave['flg'] = 4;
                                    $datasave['student_id'] = $arval['student_id'];
                                    $datasave['ai_code'] = $arval['ai_code'];
                                    PrepareExamSubject::create($datasave);
                                }
                            }
                        } else {
                            foreach ($tocval['TocMark'] as $tocmkey => $tocmval) {
                                $datasave = array();
                                $datasave['toc'] = 1;
                                $datasave['theory_marks'] = $tocmval['theory'];
                                $datasave['practical_marks'] = $tocmval['practical'];
                                $datasave['total_marks'] = $tocmval['total_marks'];
                                $datasave['enrollment'] = $arval['enrollment'];
                                $datasave['course'] = $arval['course'];
                                $datasave['subject_id'] = $tocmval['subject_id'];
                                $datasave['subject_code'] = @$subarr[$tocmval['subject_id']];
                                $datasave['final_result'] = '';
                                $datasave['exam_year'] = $current_admission_session_id;
                                $datasave['exam_month'] = $current_exam_month_id;
                                $datasave['is_supplementary'] = $is_supplementary;
                                $datasave['total_new'] = 0;
                                $datasave['flg'] = 4;
                                $datasave['student_id'] = $arval['student_id'];

                                if (@$tocmval['subject_id'] && @$subjects[$tocmval['subject_id']]) {
                                    $datasave['subject_name'] = $subjects[$tocmval['subject_id']];
                                    $datasave['subject_code'] = $subjectCodes[$tocmval['subject_id']];
                                }
                                $datasave['ai_code'] = $arval['ai_code'];
                                PrepareExamSubject::create($datasave);
                            }
                        }
                    }
                } else if (@$tocval['Toc']['board'] != 81) { //Other board data
                    if (count($tocval['TocMark']) > 0) {
                        if ($arval['adm_type'] == 4) {
                            foreach ($tocval['TocMark'] as $tocmkey => $tocmval) {
                                $conditions = array();
                                $conditions['student_id'] = $arval['student_id'];
                                $conditions['course'] = $arval['course'];
                                $conditions['toc'] = 0;
                                $conditions['is_supplementary'] = $is_supplementary;
                                $conditions['exam_year'] = $current_admission_session_id;
                                $conditions['exam_month'] = $current_exam_month_id;
                                $conditions['subject_id'] = $tocmval['subject_id'];


                                $preparesubjects = PrepareExamSubject::where($conditions)->whereNull("deleted_at")->first();

                                if (@$preparesubjects && !empty($preparesubjects)) {
                                    $preparesubjects = $preparesubjects->toArray();
                                } else {
                                    $preparesubjects = array();
                                }


                                if (!empty($preparesubjects)) {
                                    $datasave = array();
                                    $datasave['id'] = $preparesubjects['id'];

                                    $total_theory_marks = (round($preparesubjects['old_theory_marks'] * 0.9)) + $preparesubjects['sessional_marks_reil_result'];
                                    if (($tocmval['conv_theory'] > $total_theory_marks) || ($preparesubjects['old_theory_marks'] == 999) || $preparesubjects['old_theory_marks'] == 0) {
                                        $datasave['theory_marks'] = $tocmval['conv_theory'];
                                        $datasave['sessional_marks_reil_result'] = 0;
                                        $datasave['sessional_marks'] = 0;
                                    }

                                    /* Start Need to be check 999 in practical marks as per theory marks 1208 */
                                    if ($preparesubjects['practical_marks'] == 999) {
                                        $preparesubjects['practical_marks'] = 0;
                                    }
                                    /* End Need to be check 999 in practical marks as per theory marks 1208 */

                                    if ($tocmval['conv_practical'] > $preparesubjects['practical_marks']) {
                                        $datasave['practical_marks'] = $tocmval['conv_practical'];
                                    }
                                    $datasave['flg'] = 3;
                                    $datasave['student_id'] = $arval['student_id'];
                                    if (@$tocmval['subject_id'] && @$subjects[$tocmval['subject_id']]) {
                                        $datasave['subject_name'] = $subjects[$tocmval['subject_id']];
                                        $datasave['subject_code'] = $subjectCodes[$tocmval['subject_id']];
                                    }
                                    $datasave['ai_code'] = $arval['ai_code'];
                                    PrepareExamSubject::where('prepare_exam_subjects.id', '=', $datasave['id'])->update($datasave);
                                    unset($datasave);
                                } else {
                                    $datasave = array();
                                    $datasave['toc'] = 1;
                                    $datasave['theory_marks'] = $tocmval['conv_theory'];
                                    $datasave['practical_marks'] = $tocmval['conv_practical'];
                                    $datasave['total_marks'] = $tocmval['conv_total_marks'];
                                    $datasave['enrollment'] = $arval['enrollment'];
                                    $datasave['course'] = $arval['course'];
                                    $datasave['subject_id'] = $tocmval['subject_id'];
                                    $datasave['subject_code'] = @$subarr[$tocmval['subject_id']];
                                    $datasave['final_result'] = '';
                                    $datasave['exam_year'] = $current_admission_session_id;
                                    $datasave['exam_month'] = $current_exam_month_id;
                                    $datasave['supplementary'] = $is_supplementary;
                                    $datasave['total_new'] = 0;
                                    $datasave['flg'] = 3;
                                    $datasave['student_id'] = $arval['student_id'];
                                    if (@$tocmval['subject_id'] && @$subjects[$tocmval['subject_id']]) {
                                        $datasave['subject_name'] = $subjects[$tocmval['subject_id']];
                                        $datasave['subject_code'] = $subjectCodes[$tocmval['subject_id']];
                                    }
                                    $datasave['ai_code'] = $arval['ai_code'];
                                    PrepareExamSubject::create($datasave);
                                }
                            }
                        } else {


                            foreach ($tocval['TocMark'] as $tocmkey => $tocmval) {
                                $datasave = array();
                                $datasave['toc'] = 1;
                                $datasave['theory_marks'] = $tocmval['conv_theory'];
                                $datasave['practical_marks'] = $tocmval['conv_practical'];
                                $datasave['total_marks'] = $tocmval['conv_total_marks'];
                                $datasave['student_id'] = $arval['student_id'];
                                $datasave['enrollment'] = $arval['enrollment'];
                                $datasave['course'] = $arval['course'];
                                $datasave['subject_id'] = $tocmval['subject_id'];
                                $datasave['subject_code'] = @$subarr[$tocmval['subject_id']];
                                $datasave['final_result'] = '';
                                $datasave['exam_year'] = $current_admission_session_id;
                                $datasave['exam_month'] = $current_exam_month_id;
                                $datasave['supplementary'] = $is_supplementary;
                                $datasave['total_new'] = 0;
                                $datasave['flg'] = 3;
                                $datasave['student_id'] = $arval['student_id'];
                                if (@$tocmval['subject_id'] && @$subjects[$tocmval['subject_id']]) {
                                    $datasave['subject_name'] = $subjects[$tocmval['subject_id']];
                                    $datasave['subject_code'] = $subjectCodes[$tocmval['subject_id']];
                                }
                                $datasave['ai_code'] = $arval['ai_code'];


                                PrepareExamSubject::create($datasave);

                            }
                        }
                    }

                }
                $counter++;
            }
        } else {
            //echo "Completed";die;
            //$this->loadModel('TenthPrepareExamSubject');
            //$this->TenthPrepareExamSubject->query("update `tenth_prepare_exam_subjects` set total_marks =(CASE WHEN theory_marks <= 100 THEN theory_marks ELSE 0 END) + (CASE WHEN practical_marks <= 100 THEN practical_marks ELSE 0 END) +(CASE WHEN sessional_marks_reil_result <= 10 THEN sessional_marks_reil_result ELSE 0 END)");
            //  die;
        }
        $q1 = "update `rs_prepare_exam_subjects` set total_marks =(CASE WHEN theory_marks > 0 &&   theory_marks <= 100 THEN theory_marks ELSE 0 END) + (CASE WHEN practical_marks > 0 && practical_marks <= 100 THEN practical_marks ELSE 0 END) + (CASE WHEN sessional_marks_reil_result > 0 &&  sessional_marks_reil_result <= 10 THEN sessional_marks_reil_result ELSE 0 END) where course = " . $course . " and is_supplementary = " . $is_supplementary . " and exam_year = " . $current_admission_session_id . " and exam_month = " . $current_exam_month_id . "";

        DB::statement($q1);

        echo "<h1>Step 4 TOC Marks Migrate into PES  Note Toc Marks already done the process of calculate of theory Marks Updated " . $counter . "</h1>";

        $newOffset = $offset + $limit;
        $newOffset = $offset;
        $baseAction = "process_result";
        $this->setAndHitNextUrl($baseAction, $is_supplementary, $course, $newOffset, $limit);
        die;
    }

    /* Step 6 Process Result */
    public function process_result($is_supplementary = 0, $course = null, $offset = 0, $limit = 3000, Request $request)
    {
        $subjects = $this->subjectList($course);
        $subjectdata = $subjectCodes = $this->subjectCodeList($course);
        $counter = 0;
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

                            /* Make code update for next year start 16082023*/

                            // $marksarr = $this->resultprocess_component_obj->getSubjectMaxMinMarksMaster($subjectId,81);
                            // if($val['practical_marks'] < $marksarr['PR_MIN']){
                            // 	$tempDatasave['is_only_theory_subject'] = $result_code_sycp;
                            // }

                            /* End */
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
            ->get(['student_id', 'enrollment', "subject_id", "id", "final_result", "sessional_marks", "sessional_marks_reil_result", "is_grace_marks_given", "grace_marks", "old_theory_marks", "total_marks", "course", "theory_marks", "practical_marks"])
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
                    PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($savedata);
                    continue;
                }
                if ($val['course'] == 10) {
                    $markspercent = ($val['total_marks'] * 100) / $marksarr['GT_MAX'];
                    if ($val['total_marks'] >= $marksarr['GT_MIN'] && $val['total_marks'] <= 100) {
                        $datasave['final_result'] = 'P';
                        $datasave['is_final_res_updated'] = '1';
                        PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                        unset($datasave);
                        continue;
                    }
                    if ($val['total_marks'] < $marksarr['GT_MIN'] && $val['total_marks'] >= 0) {
                        $datasave['final_result'] = 'SYC';
                        $datasave['is_final_res_updated'] = '1';
                        PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                        unset($datasave);
                        continue;
                    }
                }


                if ($val['course'] == 12) {
                    // if(in_array($subjectid,array_keys($subjectdataarr))){ //check is practical or not
                    if (in_array($val['subject_id'], array(23, 24, 25, 28, 33, 35, 37, 38, 39))) {
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

                    /* if($val['student_id'] == 653473){
						echo "<pre>";
						echo "<br>";
						print_r($result_code_syc);
						echo "<br>";
						print_r($val);
						echo "<br>";
						print_r($subjectdataarr);
						echo "<br>";
						print_r($marksarr);
						echo "<br>";
						print_r($practicalmarks);
						echo "<br>";
						print_r($val['id']);
						if($practicalmarks < $marksarr['PR_MIN'] && $theorymarks < $marksarr['TH_MIN'])
						{
							echo "T";
						}
						dd($val);
					} */
                    if (!is_numeric($val['theory_marks'])) {
                        $val['theory_marks'] = 0;
                    }

                    if (!is_numeric($val['sessional_marks_reil_result'])) {
                        $val['sessional_marks_reil_result'] = 0;
                    }
                    $theorymarks = (@$val['theory_marks'] + @$val['sessional_marks_reil_result']);
                    if ($val['is_grace_marks_given'] == 1) {
                        $theorymarks = $theorymarks + $val['grace_marks'];
                    }


                    // if(in_array($subjectid,array_keys($subjectdataarr))){ //check is practical or not
                    if (in_array($val['subject_id'], array(23, 24, 25, 28, 33, 35, 37, 38, 39))) {
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
                            PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                            unset($datasave);
                            continue;
                        } else {
                            if ($practicalmarks < $marksarr['PR_MIN'] && $theorymarks < $marksarr['TH_MIN']) {
                                $datasave = array();
                                $datasave['final_result'] = $result_code_syc;
                                PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                                unset($datasave);
                                continue;
                            }
                            if ($theorymarks < $marksarr['TH_MIN']) {
                                $datasave = array();
                                $datasave['final_result'] = $result_code_syct;
                                PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
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
                                PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
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
                    } else { //lokesh why this else part written

                        // if($val['subject_id'] == 27){

                        // 	print_r($marksarr);dd($val['total_marks']);
                        // }

                        //if only theory subject then check from total marks

                        if (($val['total_marks'] >= $marksarr['TH_MIN'] && $val['total_marks'] <= $marksarr['TH_MAX'])) {
                            $datasave = array();
                            $datasave['final_result'] = 'P';
                            PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                            unset($datasave);
                            //$this->PrepareExamSubject->updateAll(array('PrepareExamSubject.final_result'=>'PASS'),array('PrepareExamSubject.enrollment'=>$val['enrollment'],'PrepareExamSubject.subject_id'=>$val['subject_id']));
                        } else {
                            $datasave = array();
                            $datasave['final_result'] = $result_code_syc;
                            PrepareExamSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
                            unset($datasave);
                        }
                    }
                } //end 12th course condition
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
    /*Step 7 Final 10th result Move Data into PrepareExamResult Table */
    //lokesh 12th syct sycp result condition not implemented

    public function final_result($is_supplementary = 0, $course = null, $offset = 0, $limit = 3000, Request $request)
    {
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");

        $stallotmentarr = $studentLists = $this->_getValidStudentList($is_supplementary, $course, $offset, $limit);

        // dd($stallotmentarr);
        $subjects = $this->subjectList($course);
        $subjectCodes = $this->subjectCodeList($course);
        $subjectTypes = $this->subjectTypeList($course);
        $result_date = Config::get("global.result_date");
        $counter = 0;

        // dd($stallotmentarr);

        if (!empty($stallotmentarr)) {
            foreach ($stallotmentarr as $student_id => $enrollment) {
                $counter++;
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
                    $enrollmentsubjects = PrepareExamSubject::where($conditions)->whereNull("deleted_at")
                        ->orderBy("exam_year", "DESC")
                        ->orderBy("exam_month", "DESC")
                        ->orderBy("final_result", "ASC")
                        ->orderBy("total_marks", "DESC")
                        ->groupBy("student_id")
                        ->groupBy("subject_id")
                        ->get(['student_id', 'enrollment', "subject_id", "id", "final_result", "sessional_marks", "sessional_marks_reil_result", "old_theory_marks", "toc", "total_marks"])
                        ->toArray();


                    $compulsarysubjects = array();
                    $additionalsubjects = array();
                    $compulsarylangsubjects = array();
                    $additionallangsubjects = array();

                    $tocNorm = $toc_compulsarysubjects = array();
                    $tocLang = $toc_compulsarylangsubjects = array();
                    $subject_result = array();


                    foreach ($enrollmentsubjects as $ensubkey => $ensubval) {
                        $currentSubjectType = @$subjectTypes[$ensubval['subject_id']];
                        $subject_result[@$ensubval['subject_id']] = $ensubval['final_result'];
                        // if($ensubval['subject_id'] == 1){

                        // }

                        if ($currentSubjectType == 'B') { // Normal subject
                            if ($ensubval['toc'] == 1) {
                                $tocNorm[] = @$ensubval['subject_id'];
                                $toc_compulsarysubjects[@$ensubval['subject_id']] = $ensubval['total_marks'];
                            } else {
                                $compulsarysubjects[@$ensubval['subject_id']] = $ensubval['total_marks'];
                            }
                        } else if ($currentSubjectType == 'A') {  // Language subject
                            if ($ensubval['toc'] == 1) {
                                $tocLang[] = @$ensubval['subject_id'];
                                $toc_compulsarylangsubjects[@$ensubval['subject_id']] = $ensubval['total_marks'];
                            } else {
                                $compulsarylangsubjects[@$ensubval['subject_id']] = $ensubval['total_marks'];
                            }
                        }
                    }


                    // echo "Compulsarysubjects <br>";print_r($compulsarysubjects);echo "<br>";
                    // dd("Test");


                    // echo "<pre>";
                    // echo "compulsarylangsubjects <br>";print_r($compulsarylangsubjects);
                    // echo "toc_compulsarylangsubjects <br>";print_r($toc_compulsarylangsubjects);
                    // echo "toc_compulsarysubjects <br>";print_r($toc_compulsarysubjects);
                    // echo "compulsarysubjects <br>";print_r($compulsarysubjects);
                    // dd($enrollmentsubjects);


                    arsort($toc_compulsarysubjects);
                    arsort($toc_compulsarylangsubjects);

                    arsort($compulsarysubjects);
                    arsort($compulsarylangsubjects);


                    $toc_subjects = array();
                    $main_subjects = array();

                    //dd($compulsarysubjects);
                    //dd($compulsarylangsubjects);

                    if (count($compulsarysubjects) > 0 || count($compulsarylangsubjects) > 0) {
                        $main_subjects = array_combine(
                            array_merge(
                                array_keys($compulsarysubjects),
                                array_keys($compulsarylangsubjects)
                            ),
                            array_merge(
                                array_values($compulsarysubjects),
                                array_values($compulsarylangsubjects))
                        );
                        // print_r($main_subjects);
                        arsort($main_subjects);
                    }

                    //dd($main_subjects);


                    // echo "main <pre><br>";
                    // print_r(@$main_subjects);
                    // echo "compulsarysubjects <pre><br>";  print_r($compulsarysubjects);
                    // echo "compulsarylangsubjects <pre><br>";  print_r($compulsarylangsubjects);
                    // dd($enrollmentsubjects);


                    $temp = null;
                    if (count($main_subjects) > 0) {
                        foreach ($main_subjects as $mskey => $msval) {
                            $totalCounding = count($toc_compulsarysubjects) + count($toc_compulsarylangsubjects);

                            if ($totalCounding < 5 && count($toc_compulsarylangsubjects) < 2 && in_array($mskey, array_keys($compulsarylangsubjects))) {
                                $temp[$mskey . $msval] = 1;
                                $toc_compulsarylangsubjects[$mskey] = $msval;
                            } else if ($totalCounding < 5 && in_array($mskey, array_keys($compulsarysubjects)) && (
                                    (count($toc_compulsarylangsubjects) < 2 && count($toc_compulsarysubjects) < 4) || (count($toc_compulsarylangsubjects) < 3 && count($toc_compulsarysubjects) < 3))) {
                                $toc_compulsarysubjects[$mskey] = $msval;
                                $temp[$mskey . $msval] = 2;
                            } else {
                                $additionalsubjects[$mskey] = $msval;
                                $temp[$mskey . $msval] = 3;
                            }
                        }
                    }


                    // echo "additionalsubjects <br>";print_r($additionalsubjects);
                    // dd($main_subjects);


                    // echo "<pre>";
                    // echo "compulsarylangsubjects <br>";print_r($compulsarylangsubjects);
                    // echo "toc_compulsarylangsubjects <br>";print_r($toc_compulsarylangsubjects);
                    // echo "toc_compulsarysubjects <br>";print_r($toc_compulsarysubjects);
                    // echo "compulsarysubjects <br>";print_r($compulsarysubjects);
                    // echo "additionalsubjects <br>";print_r($additionalsubjects);
                    // dd($main_subjects);

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

                    $countLangAddi = 0;


                    // echo "Main Subject <pre><br>";
                    // print_r($enrollmentsubjects);
                    // print_r($main_subjects);
                    // dd($additionalsubjects);

                    foreach ($additionalsubjects as $tk => $tval) {
                        $resultAddiArr[$tk] = $subject_result[$tk];
                        $resultAddiArrTemp[$tk] = $subject_result[$tk];
                        if (isset($compulsarylangsubjects[$tk])) {
                            $countLangAddi++;
                        }
                    }


                    $tocChk = array_merge($tocNorm, $tocLang);

                    // echo "<pre>";
                    // echo "compulsarylangsubjects <br>";print_r($compulsarylangsubjects);
                    // echo "toc_compulsarylangsubjects <br>";print_r($toc_compulsarylangsubjects);
                    // echo "toc_compulsarysubjects <br>";print_r($toc_compulsarysubjects);
                    // echo "compulsarysubjects <br>";print_r($compulsarysubjects);
                    // echo "additionalsubjects <br>";print_r($additionalsubjects);
                    // dd($tocChk);


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
                    $totalLanSubjects = 0;
                    if (@$langsubjects) {
                        $totalLanSubjects = count($langsubjects);
                    }

                    // Final udpate in the exam year 124 and month 1 end
                    if ($course == 10) {
                        if (!empty($resultAddiArr)) {
                            if ($totalLanSubjects <= 2) {
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
                        }
                        $tempAddArr = array();
                        /* If student still failed then reorder the failed subject in DESC order by number */
                        if (count($resultAddiArr) >= 2 && (in_array("SYC", $result_arr))) {
                            $storeResultAddiArr = $resultAddiArr;
                            foreach ($storeResultAddiArr as $ind => $v) {
                                $tempAddArr[$ind] = @$main_subjects[$ind];
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

                        if ($totalLanSubjects <= 2) {
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
                        }

                        $tempAddArr = array();
                        /* If student still failed then reorder the failed subject in DESC order by number */
                        // echo "Result Arr";


                        if (count($resultAddiArr) >= 2 && (in_array("SYC", $result_arr) || in_array("SYCT", $result_arr) || in_array("SYCP", $result_arr))) {

                            $storeResultAddiArr = $resultAddiArr;
                            foreach ($storeResultAddiArr as $ind => $v) {
                                $tempAddArr[$ind] = @$main_subjects[$ind];
                            }

                            // echo "tempAddArr <pre>";
                            // print_r($storeResultAddiArr);
                            // die;

                            arsort($tempAddArr);
                            // echo "<pre>Temp Additional Result ";
                            // print_r($tempAddArr);

                            // echo "Sort New Additional /Result ";
                            // print_r($tempAddArr);


                            $resultAddiArr = array();
                            foreach ($tempAddArr as $tempind => $v) {
                                $resultAddiArr[$tempind] = $storeResultAddiArr[$tempind];
                            }


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
                                            if (@$main_subjects[@$resArrindex] < @$main_subjects[@$addindex]) {
                                                $result_arr[$addindex] = @$resultAddiArr[$addindex];
                                                $resultAddiArr[$resArrindex] = $result_arr[$resArrindex];
                                                if (@$result_arr[$resArrindex]) {
                                                    unset($result_arr[$resArrindex]);
                                                }
                                                if (@$resultAddiArr[$addindex]) {
                                                    unset($resultAddiArr[$addindex]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }


                    $tsum = 0;
                    foreach ($result_arr as $k => $tmark) {
                        if (isset($toc_compulsarylangsubjects[$k])) {
                            $tsum += $toc_compulsarylangsubjects[$k];
                        } elseif (isset($toc_compulsarysubjects[$k])) {
                            $tsum += $toc_compulsarysubjects[$k];
                        } elseif (isset($additionalsubjects[$k])) {
                            $tsum += $additionalsubjects[$k];
                        }
                    }
                    /* END */
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
                        $savedata['course'] = $course;
                        $savedata['final_result'] = 'XXXX';
                        $savedata['is_examresult_migrated'] = 0;
                        $savedata['exam_year'] = $current_admission_session_id;
                        $savedata['exam_month'] = $current_exam_month_id;

                        $savedata['result_date'] = $result_date;
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


                    /* start from here 23rd Nov., 2022 in the morning */


                    if (count($toc_compulsarylangsubjects) == 0) {
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
                        $savedata['student_id'] = (!empty($adm_type["id"])) ? $adm_type["id"] : 0;

                        if (@$checkR['id'] && !empty($checkR['id'])) {
                            PrepareExamResult::where('id', '=', $checkR['id'])->update($savedata);
                        } else {
                            PrepareExamResult::create($savedata);
                        }
                        unset($savedata);
                        continue;
                    }

                    if ((count($toc_compulsarylangsubjects) + count($toc_compulsarysubjects)) < 5 && $admission_type < 5) {
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
                        $savedata['student_id'] = (!empty($adm_type["id"])) ? $adm_type["id"] : 0;

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
                    } else if (in_array('SYC', array_values($result_arr))) {
                        $savedata['final_result'] = 'XXXX';
                    } else if (in_array('SYCT', array_values($result_arr))) {
                        $savedata['final_result'] = 'XXXX';
                    } else if (in_array('SYCP', array_values($result_arr))) {
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
                    $savedata['additional_subjects'] = (!empty($resultAddiArr)) ? serialize($resultAddiArr) : null;
                    $savedata['total_marks'] = ($tsum > 0) ? $tsum : array_sum(array_values($toc_compulsarylangsubjects)) + array_sum(array_values($toc_compulsarysubjects));
                    $savedata['student_id'] = (!empty($adm_type["id"])) ? $adm_type["id"] : 0;


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
        echo "<div class='countdown alerts-border' style='text-align:center;font-size: 60px;margin-top: 0px;background:linear-gradient(45deg, #a5e362, #287a7373);'>All Step Done For Fresh Students " . date('d-m-y h:i:s') . "</div>";
//        die("Temp hold for course " . $course);
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
//        die;

    }
    /* Fresh Student Of 10th and 12th end */

    // Extra for testing
    public function check_final_result($studentIds = null)
    {
        $offset = 0;
        $limit = 100000;
        $current_admission_session_id = Config::get("global.admission_academicyear_id");
        $current_exam_month_id = Config::get("global.current_exam_month_id");
        $conditions = null;

        $conditions ["student_allotments.exam_year"] = $current_admission_session_id;
        $conditions ["student_allotments.exam_month"] = $current_exam_month_id;

        $stallotmentarr = array();
        if (isset($studentIds)) {
            $studentIds = explode(",", $studentIds);
            $stallotmentarr = StudentAllotment::whereIn("student_id", $studentIds)->where($conditions)->take($limit)->skip($offset)->pluck("student_allotments.enrollment", "student_allotments.student_id");
        } else {
            $stallotmentarr = StudentAllotment::where($conditions)->take($limit)->skip($offset)->pluck("student_allotments.enrollment", "student_allotments.student_id");
        }
        if (@$stallotmentarr && !empty($stallotmentarr)) {
            $stallotmentarr = $stallotmentarr->toArray();
        } else {
            $stallotmentarr = array();
        }
        if (!empty($stallotmentarr)) {
            $cc = 0;
            echo "Enrollment Nubmers : <br>";
            foreach ($stallotmentarr as $student_id => $enrollment) {
                $cc++;
                echo $cc . ". " . $enrollment . "<br>";
                echo "======<br>";
            }
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
                    /*if($admission_type == 4)
					{
						$tocconditions=array();
						$tocconditions['Toc.enrollment'] = $stallotval['StudentAllotment']['enrollment'];
						$this->Toc->bindModel(array('hasMany'=>array('TocMark')),false);
						$tocarr = $this->Toc->find('all',array('conditions'=>$tocconditions));

					}*/
                    $conditions = array();
                    //$langconditions['ExamSubject.exam_year'] = Configure::read('Site.default_academicyear_id');//Configure::read('Site.default_academicyear_id');
                    // $conditions['ExamSubject.enrollment'] = $stallotval['StudentAllotment']['enrollment'];
                    // $conditions['ExamSubject.is_deleted'] = 0;
                    // $conditions['Subject.deleted']=0;
                    // $conditions['Subject.course']=$stallotval['StudentAllotment']['course'];
                    // $this->ExamSubject->bindModel(array('belongsTo'=>array('Subject'=>array('className' => 'Subject','foreignKey' => 'subject') )));
                    // $enrollmentsubjects = $this->ExamSubject->find('all',array('conditions'=>$conditions));
                    // pr($enrollmentsubjects);die;
                    $conditions = null;
                    $conditions ["prepare_exam_subjects.exam_year"] = $current_admission_session_id;
                    $conditions ["prepare_exam_subjects.exam_month"] = $current_exam_month_id;
                    $conditions['prepare_exam_subjects.student_id'] = $student_id;
                    if (isset($course) && !empty($course)) {
                        $conditions['prepare_exam_subjects.course'] = $course;
                    }
                    $enrollmentsubjects = PrepareExamSubject::where($conditions)->whereNull("deleted_at")
                        ->orderBy("id")
                        ->get(['student_id', 'enrollment', "course", "subject_id", "subject_name", "id", "final_result", "sessional_marks", "sessional_marks_reil_result", "total_marks", "subject_type", "old_theory_marks"]);

                    if (@$enrollmentsubjects && !empty($enrollmentsubjects)) {
                        $enrollmentsubjects = $enrollmentsubjects->toArray();
                    } else {
                        $enrollmentsubjects = array();
                    }
                    $isToc = Application::where("student_id", $student_id)->where("toc", 1)->count();
                    $compulsarysubjects = array();
                    $additionalsubjects = array();
                    $compulsarylangsubjects = array();
                    $additionallangsubjects = array();

                    $tocNorm = $toc_compulsarysubjects = array();
                    $tocLang = $toc_compulsarylangsubjects = array();
                    $subject_result = array();


                    foreach ($enrollmentsubjects as $ensubkey => $ensubval) {
                        $course = $ensubval['course'];
                        $subjectTypes = $this->subjectTypeList($course)->toArray();
                        $ensubval['subject_type'] = $subjectTypes[$ensubval['subject_id']];
                        $subject_result[$ensubval['subject_id']] = $ensubval['final_result'];
                        if ($ensubval['subject_type'] == 'B') { // Normal subject
                            if (!empty($isToc) && $isToc == 1) {
                                $tocNorm[] = $ensubval['subject_id'];
                                $toc_compulsarysubjects[$ensubval['subject_id']] = $ensubval['total_marks'];
                            } else {
                                $compulsarysubjects[$ensubval['subject_id']] = $ensubval['total_marks'];
                            }
                        }


                        if ($ensubval['subject_type'] == 'A') {  // Language subject
                            if (!empty($isToc) && $isToc == 1) {
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
                    /* pr($toc_compulsarylangsubjects);
					pr($toc_compulsarysubjects);
					die;
					pr($compulsarylangsubjects);
					pr($compulsarysubjects);
					pr($main_subjects); */
                    if (count($main_subjects) > 0) {
                        foreach ($main_subjects as $mskey => $msval) {

                            $totalCounding = count($toc_compulsarysubjects) + count($toc_compulsarylangsubjects);

                            if ($totalCounding < 5 && count($toc_compulsarylangsubjects) < 2 && in_array($mskey, array_keys($compulsarylangsubjects))) {
                                $toc_compulsarylangsubjects[$mskey] = $msval;

                            } else if ($totalCounding < 5 && in_array($mskey, array_keys($compulsarysubjects)) && ((count($toc_compulsarylangsubjects) < 2 && count($toc_compulsarysubjects) < 4) || (count($toc_compulsarylangsubjects) < 3 && count($toc_compulsarysubjects) < 3))) {
                                //echo $mskey;die;
                                $toc_compulsarysubjects[$mskey] = $msval;
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

                    $countLangAddi = 0;
                    foreach ($additionalsubjects as $tk => $tval) {
                        $resultAddiArr[$tk] = $subject_result[$tk];
                        $resultAddiArrTemp[$tk] = $subject_result[$tk];

                        if (isset($compulsarylangsubjects[$tk])) {
                            $countLangAddi++;
                        }
                    }

                    $tocChk = array_merge($tocNorm, $tocLang);

                    //  pr($resultArrForSort);

                    asort($resultArrForSort);
                    //  echo "Additinal Arr ";
                    //  pr($resultAddiArr);
                    /*Sort result array by total marks  */
                    $result_arr = array();
                    $countLagSubInRes = 0;
                    foreach ($resultArrForSort as $subId => $totalM) {
                        $result_arr[$subId] = $resultTempArr[$subId];
                        if (isset($compulsarylangsubjects[$subId])) {
                            $countLagSubInRes++;
                        }
                    }
                    if (!empty($resultAddiArr)) {
                        foreach ($resultAddiArr as $index => $addi) {
                            $resultAddiSortArr[$index] = $additionalsubjects[$index];
                            if ($addi == "P" && (count($result_arr) > 1) && (in_array("888", $result_arr) || in_array("666", $result_arr) || in_array("777", $result_arr))) {

                                if (count($compulsarylangsubjects) > 2) {
                                    if (!isset($compulsarylangsubjects[$index])) {
                                        $resArrindex = array_search("777", $result_arr);
                                        $resArrindex = (empty($resArrindex)) ? array_search("666", $result_arr) : $resArrindex;
                                        $resArrindex = (empty($resArrindex)) ? array_search("888", $result_arr) : $resArrindex;
                                        $resultAddiArr[$resArrindex] = $result_arr[$resArrindex];
                                        $result_arr[$index] = $resultAddiArr[$index];
                                        unset($result_arr[$resArrindex]);
                                        unset($resultAddiArr[$index]);
                                    }
                                } else {
                                    $resArrindex = array_search("777", $result_arr);
                                    $resArrindex = (empty($resArrindex)) ? array_search("666", $result_arr) : $resArrindex;
                                    $resArrindex = (empty($resArrindex)) ? array_search("888", $result_arr) : $resArrindex;

                                    if (isset($compulsarylangsubjects[$index]) && $resultAddiArr[$index] == "p") {
                                        if ($countLagSubInRes < 2) {
                                            $resultAddiArr[$resArrindex] = $result_arr[$resArrindex];
                                            $result_arr[$index] = $resultAddiArr[$index];
                                            unset($result_arr[$resArrindex]);
                                            unset($resultAddiArr[$index]);
                                        }
                                    } else {
                                        if ($countLagSubInRes == 1 && isset($compulsarylangsubjects[$resArrindex]) && $countLangAddi == 0) {

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
                    // echo "Result Arr";
                    if (count($resultAddiArr) >= 2 && (in_array("888", $result_arr) || in_array("777", $result_arr) || in_array("666", $result_arr))) {

                        $storeResultAddiArr = $resultAddiArr;
                        foreach ($storeResultAddiArr as $ind => $v) {
                            $tempAddArr[$ind] = $main_subjects[$ind];
                        }
                        // echo "Temp Additional Result ";
                        // pr($tempAddArr);

                        arsort($tempAddArr);

                        // echo "Sort New Additional /Result ";
                        // pr($tempAddArr);


                        $resultAddiArr = array();
                        foreach ($tempAddArr as $tempind => $v) {
                            $resultAddiArr[$tempind] = $storeResultAddiArr[$tempind];
                        }


                        foreach ($result_arr as $resArrindex => $res) {
                            if ($res != "P" && (in_array("888", $resultAddiArr) || in_array("666", $resultAddiArr) || in_array("777", $resultAddiArr))) {

                                if (count($compulsarylangsubjects) > 2) {
                                    $addindex = array_search("777", $resultAddiArr);
                                    $addindex = (empty($addindex)) ? array_search("666", $resultAddiArr) : $addindex;
                                    $addindex = (empty($addindex)) ? array_search("888", $resultAddiArr) : $addindex;
                                    if (!isset($compulsarylangsubjects[$resArrindex])) {
                                        if ($main_subjects[$resArrindex] < $main_subjects[$addindex]) {
                                            $result_arr[$addindex] = $resultAddiArr[$addindex];
                                            $resultAddiArr[$resArrindex] = $result_arr[$resArrindex];
                                            unset($result_arr[$resArrindex]);
                                            unset($resultAddiArr[$addindex]);
                                        }
                                    }
                                } else {


                                    $addindex = array_search("777", $resultAddiArr);
                                    $addindex = (empty($addindex)) ? array_search("666", $resultAddiArr) : $addindex;
                                    $addindex = (empty($addindex)) ? array_search("888", $resultAddiArr) : $addindex;

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

                    echo "==========<br/>";
                    $tsum = 0;
                    foreach ($result_arr as $k => $tmark) {
                        if (isset($toc_compulsarylangsubjects[$k])) {
                            $tsum += $toc_compulsarylangsubjects[$k];
                        } elseif (isset($toc_compulsarysubjects[$k])) {
                            $tsum += $toc_compulsarysubjects[$k];
                        } elseif (isset($additionalsubjects[$k])) {
                            $tsum += $additionalsubjects[$k];
                        }
                    }
                    /* END */
                    echo "<pre>Additional Subject ";
                    print_r($resultAddiArr);

                    //  echo "Additional Result ";

                    echo "Result Arr";
                    print_r($result_arr);
                    echo "Sum: " . $tsum . "<br/> ";


                    //  echo "Main subject ";
                    //  pr($main_subjects);
                    //  echo "remove die"; die;

                    // $count=$this->ExamResult->find("count",array("conditions"=>array("enrollment"=>$stallotval['StudentAllotment']['enrollment'])));

                    $checkR = ExamResult::where("student_id", "=", $student_id)->first(['id', 'student_id', 'enrollment']);
                    if ($admission_type == 3 && empty($stallotval['StudentAllotment']["supplementary"])) {
                        echo "Result shoud be Fail <br/>";
                        echo "Condition:admission_type == 3";
                        exit;

                    }
                    if (count($toc_compulsarylangsubjects) == 0) {
                        echo "Result shoud be Fail <br/>";
                        echo "Condition:count toc_compulsarylangsubjects == 0";
                        exit;
                    }
                    /* pr($toc_compulsarylangsubjects);
					pr($toc_compulsarysubjects);

					die; */

                    if (((count($toc_compulsarylangsubjects) + count($toc_compulsarysubjects)) < 5) && $admission_type < 5) {
                        echo "Result shoud be Fail <br/>";
                        echo "Condition: (count toc_compulsarylangsubjects+ count toc_compulsarysubjects) < 0 and admission_type < 5";
                        exit;
                    }
                    //if(in_array('888',array_values($result_arr))){
                    if (in_array('888', array_values($result_arr)) || in_array('777', array_values($result_arr)) || in_array('666', array_values($result_arr))) {
                        echo "Result shoud be Fail <br/>";
                        echo "Condition: count 888,777,666 in result_arr";
                        exit;
                    } else {
                        echo "Result shoud be PASS <br/>";
                        exit;
                    }
                }
            }
        } else {
            echo "<h1> Enter Students not Found. </h1>";
        }
        die;
    }

    public function get_toppers()
    {
        ini_set('memory_limit', '5000M');
        ini_set('max_execution_time', '0');
        /* $last_total_marks=null;	$rank=0;
		$data2 = array(1 => 10,2=> 10,3=> 4,4=> 16,5=> 35,6=> 50,7=> 40);
		asort($data2);
		foreach(@$data2 as $k3 => $data3){
			if(@$last_total_marks){
				if($last_total_marks == $data3){
				}else{
					$rank++;
				}
			}else{
				$rank++;
			}
			$last_total_marks = $data3;
			echo $k3 . " of Rank " . $rank . "<br>";
		}
		echo "<br>Out of Loop";
		echo "Rank " . $rank . "<br>";die; */


        //can we use old_district_id(33) but why mapping showing same,district_id,temp_district_id

        $result1 = $result2 = $result3 = null;

        $isOldDistrictWise = true;
        if ($isOldDistrictWise) {
            $districts = $district_list = $this->getOldDistrictsByState(6); //with 33
        } else {
            $districts = $district_list = $this->districtsByState(6); //with 50
        }

        // $district_list = collect(array(1 => 'Sri Ganganagar'));
        $states = $this->state_details();
        $blocks = $this->block_details();

        $title = "Result Toppers For Testing Only";
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

        $alreadyRankedStudentIds = array();
        $tehsil_list = array();
        $block_list = array();
        // $mainTable = "rs_prepare_exam_results"; //rs_exam_results //for testing currently set
        $mainTable = "rs_exam_results"; //rs_exam_results //for testing currently set
        $forTest = " ";
        $limit = 4;
        // $forTest = " 1 OR ";$limit = 2;

        $current_admission_session_id = Config::get("global.current_result_session_year_id");
        $current_exam_month_id = Config::get("global.current_result_session_month_id");
        $conditions ["student_allotments.exam_year"] = $current_admission_session_id;
        $conditions ["student_allotments.exam_month"] = $current_exam_month_id;
        $combo_name = 'gender';
        $genders = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);

        // echo $current_admission_session_id . $current_exam_month_id ;die;
        //this old is correct
        // $baseCond = "  r.final_result = 'PASS' AND r.exam_year= " . $current_admission_session_id ." AND r.exam_month = " . $current_exam_month_id . " AND (sa.supplementary = 0 or sa.supplementary = null  )  AND t.id IS NULL AND ";
        //for testing only
        // $baseCond = "  r.final_result = 'XXXX' AND r.exam_year= " . $current_admission_session_id ." AND r.exam_month = " . $current_exam_month_id ." AND  ";
        //this is correct only --Failed OR Passed Allow  r.final_result = 'XXXX' or

        //line number just next and after just next.
        $minTotalMarks = 300; //300
        // 680762 from rsos remove from toppe list as on 23-08-2023

        // dd($minTotalMarks);
        $baseCond = "  (  r.deleted_at is null and r.final_result = 'PASS' or r.final_result = 'PASS') AND sa.exam_year= " . $current_admission_session_id . " AND sa.exam_month = " . $current_exam_month_id . " AND r.exam_year= " . $current_admission_session_id . " AND r.exam_month = " . $current_exam_month_id . " AND sa.supplementary != 1 AND s.are_you_from_rajasthan = 1 AND s.adm_type = 1  AND t.id IS NULL AND r.total_marks >= " . $minTotalMarks . " AND ";
        $gropupBy = " group by s.id ";

        $cols = " r.total_marks,r.percent_marks,r.student_id,r.enrollment,r.final_result,s.gender_id,sa.course,u.district_id,u.block_id,s.father_name,s.mother_name,DATE_FORMAT(s.dob,'%d %M,%Y') as dob,s.name ";
        $tm = null;
        $masters = array("gender" => $genders->toArray(), "course" => $course->toArray(), 'states' => $states->toArray(), 'districts' => $districts->toArray(), 'blocks' => $blocks->toArray());

        //$exam_year = Config::get("global.admission_academicyear_id");
        //$exam_month = Config::get("global.current_exam_month_id");
        $exam_year = $current_admission_session_id;
        $exam_month = $current_exam_month_id;

        $tempSql = null;
        /* State Level Start */
        if (true) {
            foreach ($course as $course_id => $course_name) {
                foreach ($genders as $gender_id => $gender_name) {
                    /* Setting max Marks start */
                    $tempsqlQ1[] = $sqlQ1 = " SELECT distinct CAST(total_marks AS UNSIGNED) as total_marks " . " FROM " . $mainTable . " r  INNER JOIN rs_student_allotments sa ON sa.student_id = r.student_id LEFT JOIN rs_students s ON s.id = r.student_id LEFT JOIN rs_aicenter_details u ON u.ai_code = sa.ai_code LEFT JOIN rs_toc t ON t.student_id = r.student_id WHERE " . $forTest . $baseCond . " s.gender_id= " . $gender_id . " AND sa.course=" . $course_id . $gropupBy . " ORDER BY CAST(total_marks AS UNSIGNED) DESC LIMIT 0 , 2 ";

                    // print_r($sqlQ1);die;
                    $maxMarks = DB::select($sqlQ1);
                    $maxMarksArr = array();
                    foreach ($maxMarks as $k => $v) {
                        $maxMarksArr[] = $v->total_marks;
                    }
                    $maxMarksArr = implode(",", $maxMarksArr);
                    $finalMaxMarksSql = "";
                    if (!empty($maxMarksArr)) {
                        $tempSql[] = $finalMaxMarksSql = "  AND r.total_marks in (" . $maxMarksArr . ")  ";
                    }
                    /* Setting max Marks end */
                    $q1 = "SELECT " . $cols . " FROM " . $mainTable . " r  INNER JOIN rs_student_allotments sa ON sa.student_id = r.student_id LEFT JOIN rs_students s ON s.id = r.student_id LEFT JOIN rs_aicenter_details u ON u.ai_code = sa.ai_code LEFT JOIN rs_toc t ON t.student_id = r.student_id WHERE " . $forTest . $baseCond . " s.gender_id= " . $gender_id . " AND sa.course=" . $course_id . $finalMaxMarksSql . $gropupBy . "   ORDER BY CAST(r.total_marks AS UNSIGNED) DESC limit " . $limit . " ;";
                    $result1[$course_id][$gender_id] = DB::select($q1);
                }
            }
        }
        /* State Level End */

        $last_total_marks = null;
        $rank = 0;
        if (@$result1) {
            foreach (@$result1 as $k1 => $data1) {
                foreach (@$data1 as $k2 => $data2) {
                    foreach (@$data2 as $k3 => $data3) {
                        $alreadyRankedStudentIds[$data3->student_id] = $data3->student_id;
                        /* Save Into Table Start */
                        if (!empty($last_total_marks)) {
                            if ($last_total_marks == $data3->total_marks) {

                            } else {
                                $rank++;
                            }
                        } else {
                            $rank++;
                        }
                        $last_total_marks = $data3->total_marks;
                        $fld = "total_marks";
                        $saveData[$fld] = $data3->$fld;
                        $fld = "percent_marks";
                        $saveData[$fld] = $data3->$fld;
                        $fld = "student_id";
                        $saveData[$fld] = $data3->$fld;
                        $fld = "enrollment";
                        $saveData[$fld] = $data3->$fld;
                        $fld = "gender_id";
                        $saveData[$fld] = $data3->$fld;
                        $fld = "course";
                        $saveData[$fld] = $data3->$fld;
                        $fld = "final_result";
                        $saveData[$fld] = $data3->$fld;
                        $fld = "district_id";
                        $saveData[$fld] = $data3->$fld;
                        $fld = "block_id";
                        $saveData[$fld] = $data3->$fld;
                        $fld = "father_name";
                        $saveData[$fld] = $data3->$fld;
                        $fld = "mother_name";
                        $saveData[$fld] = $data3->$fld;
                        $fullDob = null;
                        $fld = "dob";
                        $dobT = explode(" ", @$data3->$fld);
                        $dobT2 = explode(",", @$dobT[1]);
                        $d = @$dobT[0];
                        $m = date_parse(@$dobT2[0]);
                        $m = @$m['month'];
                        $y = @$dobT2[1];
                        $fullDob = $y . "-" . $m . "-" . $d;
                        $saveData[$fld] = $fullDob;
                        $fld = "name";
                        $saveData[$fld] = $data3->$fld;
                        $fld = "exam_year";
                        $saveData[$fld] = $exam_year;
                        $fld = "exam_month";
                        $saveData[$fld] = $exam_month;
                        $fld = "type";
                        $saveData[$fld] = 'state';
                        $fld = "rank";
                        $saveData[$fld] = $rank;
                        // echo "Test33";dd($saveData);
                        if (ResultTopper::create($saveData)) {
                            //dd($saveData);
                        }
                        // echo "Test";die;
                        unset($saveData);
                        /* Save Into Table End */
                    }
                }
            }
        }
        $alreadyRankedStudentSql = '  ';
        if (@$alreadyRankedStudentIds) {
            $alreadyRankedStudentSql = " s.id not in ( " . implode(",", $alreadyRankedStudentIds) . ") AND ";
        }

        /* District Level Start */
        if (false) {
            $tempSql = null;
            foreach ($course as $course_id => $course_name) {
                foreach ($genders as $gender_id => $gender_name) {
                    foreach ($district_list as $district_id => $district_name) {
                        /* Setting max Marks start */
                        $tempsqlQ1[] = $sqlQ1 = " SELECT distinct CAST(total_marks AS UNSIGNED) as total_marks " . " FROM " . $mainTable . " r  INNER JOIN rs_student_allotments sa ON sa.student_id = r.student_id LEFT JOIN rs_students s ON s.id = r.student_id LEFT JOIN rs_aicenter_details u ON u.ai_code = sa.ai_code LEFT JOIN rs_toc t ON t.student_id = r.student_id WHERE " . $forTest . $baseCond . $alreadyRankedStudentSql . " s.gender_id= " . $gender_id . " AND sa.course=" . $course_id . " AND u.district_id = " . $district_id . $gropupBy . " ORDER BY CAST(total_marks AS UNSIGNED) DESC LIMIT 0 , 2 ";
                        $maxMarks = DB::select($sqlQ1);
                        $maxMarksArr = array();
                        foreach ($maxMarks as $k => $v) {
                            $maxMarksArr[] = $v->total_marks;
                        }
                        $maxMarksArr = implode(",", $maxMarksArr);
                        $finalMaxMarksSql = "";
                        if (!empty($maxMarksArr)) {
                            $tempSql[] = $finalMaxMarksSql = "  AND r.total_marks in (" . $maxMarksArr . ")  ";
                        }
                        /* Setting max Marks end */
                        $q1 = "SELECT " . $cols . " FROM " . $mainTable . " r  INNER JOIN rs_student_allotments sa ON sa.student_id = r.student_id LEFT JOIN rs_students s ON s.id = r.student_id LEFT JOIN rs_aicenter_details u ON u.ai_code = sa.ai_code LEFT JOIN rs_toc t ON t.student_id = r.student_id WHERE " . $forTest . $baseCond . $alreadyRankedStudentSql . " s.gender_id= " . $gender_id . " AND sa.course=" . $course_id . " AND u.district_id = " . $district_id . $finalMaxMarksSql . $gropupBy . "   ORDER BY CAST(r.total_marks AS UNSIGNED) DESC limit " . $limit . " ;";
                        $result2[$course_id][$district_id][$gender_id] = DB::select($q1);
                    }
                }
            }
        }
        /* District Level End */

        $last_total_marks = null;
        $rank = 0;
        if (!empty($result2)) {
            foreach (@$result2 as $k1 => $data1) {
                foreach (@$data1 as $k2 => $data2) {
                    foreach (@$data2 as $k3 => $dataold) {
                        $district_rank = 0;
                        foreach (@$dataold as $k3 => $data3) {
                            $alreadyRankedStudentIds[$data3->student_id] = $data3->student_id;
                            /* Save Into Table Start */
                            if (!empty($last_total_marks)) {
                                if ($last_total_marks == $data3->total_marks) {

                                } else {
                                    $rank++;
                                    $district_rank++;
                                }
                            } else {
                                $rank++;
                                $district_rank++;
                            }
                            $last_total_marks = $data3->total_marks;
                            $fld = "total_marks";
                            $saveData[$fld] = $data3->$fld;
                            $fld = "percent_marks";
                            $saveData[$fld] = $data3->$fld;
                            $fld = "student_id";
                            $saveData[$fld] = $data3->$fld;
                            $fld = "enrollment";
                            $saveData[$fld] = $data3->$fld;
                            $fld = "gender_id";
                            $saveData[$fld] = $data3->$fld;
                            $fld = "course";
                            $saveData[$fld] = $data3->$fld;
                            $fld = "final_result";
                            $saveData[$fld] = $data3->$fld;
                            $fld = "district_id";
                            $saveData[$fld] = $data3->$fld;
                            $fld = "block_id";
                            $saveData[$fld] = $data3->$fld;
                            $fld = "father_name";
                            $saveData[$fld] = $data3->$fld;
                            $fld = "mother_name";
                            $saveData[$fld] = $data3->$fld;
                            $fld = "dob";
                            $saveData[$fld] = date("Y-m-d", strtotime($data3->$fld));
                            $fld = "name";
                            $saveData[$fld] = $data3->$fld;
                            $fld = "exam_year";
                            $saveData[$fld] = $exam_year;
                            $fld = "exam_month";
                            $saveData[$fld] = $exam_month;
                            $fld = "type";
                            $saveData[$fld] = 'district';
                            $fld = "rank";
                            $saveData[$fld] = $rank;
                            $fld = "district_rank";
                            $saveData[$fld] = $district_rank;
                            ResultTopper::create($saveData);
                            unset($saveData);
                            /* Save Into Table End */
                        }
                    }
                }
            }
        }
        $alreadyRankedStudentSql = '  ';
        if (@$alreadyRankedStudentIds) {
            $alreadyRankedStudentSql = " s.id not in ( " . implode(",", $alreadyRankedStudentIds) . ") AND ";
        }
        // dd("Done State with districts");
        /* Block Level Start */
        if (false) {
            foreach ($course as $course_id => $course_name) {
                foreach ($genders as $gender_id => $gender_name) {

                    foreach ($district_list as $district_id => $district_name) {
                        $tehsil_list = $this->block_details($district_id);
                        foreach ($tehsil_list as $tehsil_id => $tehsil_name) {
                            /* Setting max Marks start */
                            $tempsqlQ1[] = $sqlQ1 = " SELECT distinct CAST(total_marks AS UNSIGNED) as total_marks " . " FROM " . $mainTable . " r  INNER JOIN rs_student_allotments sa ON sa.student_id = r.student_id LEFT JOIN rs_students s ON s.id = r.student_id LEFT JOIN rs_aicenter_details u ON u.ai_code = sa.ai_code LEFT JOIN rs_toc t ON t.student_id = r.student_id WHERE " . $forTest . $baseCond . $alreadyRankedStudentSql . " s.gender_id= " . $gender_id . " AND sa.course=" . $course_id . " AND u.district_id = " . $district_id . "  AND u.block_id = " . $tehsil_id . $gropupBy . "  ORDER BY CAST(total_marks AS UNSIGNED) DESC LIMIT 0 , 2 ";
                            $maxMarks = DB::select($sqlQ1);
                            $maxMarksArr = array();
                            foreach ($maxMarks as $k => $v) {
                                $maxMarksArr[] = $v->total_marks;
                            }
                            $maxMarksArr = implode(",", $maxMarksArr);
                            $finalMaxMarksSql = "";
                            if (!empty($maxMarksArr)) {
                                $tempSql[] = $finalMaxMarksSql = "  AND r.total_marks in (" . $maxMarksArr . ")  ";
                            }
                            /* Setting max Marks end */
                            $q1 = "SELECT " . $cols . " FROM " . $mainTable . " r  INNER JOIN rs_student_allotments sa ON sa.student_id = r.student_id LEFT JOIN rs_students s ON s.id = r.student_id LEFT JOIN rs_aicenter_details u ON u.ai_code = sa.ai_code LEFT JOIN rs_toc t ON t.student_id = r.student_id WHERE " . $forTest . $baseCond . $alreadyRankedStudentSql . " s.gender_id= " . $gender_id . " AND sa.course=" . $course_id . " AND u.district_id = " . $district_id . "  AND u.block_id = " . $tehsil_id . $finalMaxMarksSql . $gropupBy . "   ORDER BY CAST(r.total_marks AS UNSIGNED) DESC limit " . $limit . " ;";

                            // if($alreadyRankedStudentSql != '  '){
                            // 	print_r($q1);die;
                            // }
                            // print_r($q1);die;
                            $result3 = array();
                            $result3[$course_id][$district_id][$tehsil_id][$gender_id] = DB::select($q1);
                            $last_total_marks = null;
                            $rank = 0;

                            if (!empty($result3)) {
                                foreach (@$result3 as $k1 => $data1) {
                                    foreach (@$data1 as $k2 => $data2) {
                                        foreach (@$data2 as $k3 => $dataold) {
                                            foreach (@$dataold as $k3 => $data3old) {
                                                foreach (@$data3old as $k3 => $data3) {
                                                    $alreadyRankedStudentIds[$data3->student_id] = $data3->student_id;
                                                    /* Save Into Table Start */
                                                    if (!empty($last_total_marks)) {
                                                        if ($last_total_marks == $data3->total_marks) {

                                                        } else {
                                                            $rank++;
                                                        }
                                                    } else {
                                                        $rank++;
                                                    }
                                                    $last_total_marks = $data3->total_marks;
                                                    $fld = "total_marks";
                                                    $saveData[$fld] = $data3->$fld;
                                                    $fld = "percent_marks";
                                                    $saveData[$fld] = $data3->$fld;
                                                    $fld = "student_id";
                                                    $saveData[$fld] = $data3->$fld;
                                                    $fld = "enrollment";
                                                    $saveData[$fld] = $data3->$fld;
                                                    $fld = "gender_id";
                                                    $saveData[$fld] = $data3->$fld;
                                                    $fld = "course";
                                                    $saveData[$fld] = $data3->$fld;
                                                    $fld = "final_result";
                                                    $saveData[$fld] = $data3->$fld;
                                                    $fld = "district_id";
                                                    $saveData[$fld] = $data3->$fld;
                                                    $fld = "block_id";
                                                    $saveData[$fld] = $data3->$fld;
                                                    $fld = "father_name";
                                                    $saveData[$fld] = $data3->$fld;
                                                    $fld = "mother_name";
                                                    $saveData[$fld] = $data3->$fld;
                                                    $fld = "dob";
                                                    $saveData[$fld] = date("Y-m-d", strtotime($data3->$fld));
                                                    $fld = "name";
                                                    $saveData[$fld] = $data3->$fld;
                                                    $fld = "exam_year";
                                                    $saveData[$fld] = $exam_year;
                                                    $fld = "exam_month";
                                                    $saveData[$fld] = $exam_month;
                                                    $fld = "type";
                                                    $saveData[$fld] = 'block';
                                                    $fld = "rank";
                                                    $saveData[$fld] = $rank;

                                                    ResultTopper::create($saveData);
                                                    unset($saveData);
                                                    /* Save Into Table End */
                                                }
                                            }
                                        }
                                    }
                                }
                            }


                            $alreadyRankedStudentSql = '  ';
                            if (@$alreadyRankedStudentIds) {

                                $alreadyRankedStudentSql = " s.id not in ( " . implode(",", $alreadyRankedStudentIds) . ") AND ";

                                // echo "One District " . $district_name . " " . $district_id . " Done District Done and block " . $tehsil_id ."<br>";
                                // dd($alreadyRankedStudentSql);

                            }
                        }
                        // echo "One District " . $district_name . " " . $district_id . " Done District Done <br>";die;
                    }
                }
            }
        }
        /* Block Level End */
        $finalResult = array('masters' => $masters, 'state' => $result1, 'district' => $result2, 'block' => $result3);
        return view('resultprocess.get_toppers', compact('finalResult', 'title', 'breadcrumbs'));
    }

    //only for year 124-1
    public function subjectWiseUpdateGraceMarks()
    {
        $resultdata = PrepareExamSubject::
        whereIn("is_grace_marks_given", array(1, 2))->whereNull("deleted_at")->orderBy("id")->get(['student_id', 'enrollment', "subject_id", "id", "final_result", "is_grace_marks_given", "sessional_marks", "sessional_marks_reil_result", "old_theory_marks", "total_marks", "course", "theory_marks", "practical_marks"])
            ->toArray();
        $counter = 0;
        if (!empty($resultdata)) { //grace marks incress theory or practical field
            foreach ($resultdata as $key => $val) {
                $counter++;
                if ($val['course'] == 12) {
                    if ($val['is_grace_marks_given'] == 1) {
                        $this->_updateGraceMarksThPr($val['id'], 't');
                    } else if ($val['is_grace_marks_given'] == 2) {
                        $this->_updateGraceMarksThPr($val['id'], 'p');
                    }
                } else if ($val['course'] == 10) {
                    if ($val['theory_marks'] == 999) {
                        $this->_updateGraceMarksThPr($val['id'], 'p');
                    } else if ($val['theory_marks'] != 999) {
                        $this->_updateGraceMarksThPr($val['id'], 't');
                    }
                }
            }
        }
        echo $counter . " Done";
        die;
    }

    ///rsos/result_process/updateStudentAllotmentMarksFromCopyCheckingTable/126/2
    public function updateStudentAllotmentMarksFromCopyCheckingTable($exam_year=null,$exam_month=null){
        if(@$exam_year && @$exam_month){
            $condtitions =['exam_year'=>$exam_year,'exam_month'=>$exam_month];
            $data=['is_update_theory_marks_admin'=>null,'theory_lastpage_submitted_date'=>null];
            StudentAllotmentMark::where($condtitions)->update($data);
            $q2 = DB::select("UPDATE rs_student_allotment_marks AS sam 
				INNER JOIN rs_student_theory_copy_checking_marks AS stccm
					ON sam.student_id = stccm.student_id  and sam.subject_id = stccm.subject_id 
				and sam.deleted_at is null and stccm.deleted_at is null
				SET  
					sam.is_update_theory_marks_admin = 1,
					sam.theory_lastpage_submitted_date = NOW(),
					sam.final_theory_marks = stccm.final_theory_marks,
					sam.theory_absent = stccm.theory_absent,
					sam.theory_examiner_id = stccm.theory_examiner_id 
				WHERE sam.exam_year = $exam_year AND sam.exam_month = $exam_month;");

            echo "Update Student Allotment Marks From Copy Checking Table Has Been Done " .now();die;
        }
    }
}
	
