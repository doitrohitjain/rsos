<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\ResultProcessCustomComponent;
use App\models\ExamResult;
use App\models\ExamSubject;
use App\models\PrepareExamResult;
use App\models\PrepareExamSubject;
use App\models\Student;
use Auth;
use Cache;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PDF;
use Response;
use Session;

class ImportProcessedResultController extends Controller
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
        $this->allow_mannual = true; //true
        $this->conditionsEnrollment = array('24009193065', '02035213062', '09054223100', '17079223173', '20060233007', '04001233003', '14001233025', '09001232002', '07009233009', '12110232010', '08008232006', '02142232002', '12207233033', '06076232004', '17026233004', '30028233009', '23031232006', '01033235008', '17002233021', '17114233138', '15001233031', '17114233118', '03002232008', '04025233081', '12011233025', '21003233083', '06046233026', '18005232053', '06071233047', '25004233035', '12079233029', '06078233051', '19042232040', '20059233028', '28001233044', '12011233060', '19042233055', '26069233058', '21057232003', '07009233088', '09062232009', '14004233390', '03002233027', '20045233086', '14004233119', '09035233015', '12011233132', '12281233009', '12011232094', '21051233070', '20028233030', '28083233007', '04025233160', '01078233037', '09024232097', '14004233138', '04045233155', '12207233085', '01076233094', '04032233046', '09001233091', '21003233126', '12011233117', '32036233017', '04045233202', '12141233008', '12027232226', '01001233035', '17108233059', '12121233011', '02116233035', '04051233030', '19001232025', '10071233012', '04025233232', '04047233024', '11048233063', '14007233122', '13003232061', '32001233075', '14026233113', '32035233080', '14004233194', '17014233053', '09035232041', '26072232055', '23032232033', '21039233057', '28001233087', '10003232002', '01046233259', '06061232132', '17003233103', '08018233022', '02096233028', '20060233290', '18038232086', '04025233310', '23014233100', '04001233389', '04001233391', '01020233355', '23020232105', '06065233083', '01065233098', '01065233083', '09055233085', '26001233188', '11044233150', '04020233152', '04067233045', '12207233145', '26001232191', '04001233439', '32035232130', '17041232207', '07010233216', '08019233072', '01064232088', '17026233043', '26029233169', '17006233124', '06060233101', '11051233018', '32035233171', '06067232080', '17004232404', '04053233065', '12141232017', '01020233466', '32035233182', '14012233210', '08020233091', '14028232142', '01075233109', '30025232078', '17002232169', '12141232018', '06002233169', '25005233016', '12141232019', '14026233179', '26071233150', '26020233292', '26045233078', '01079232138', '01065233181', '04025233470', '04034232038', '04065232252', '30008233155', '01020233523', '06078233212', '19042233179', '04032233172', '14029233122', '20059233195', '04001233603', '06047232129', '14029233148', '26037233132', '12058233157', '02035233175', '04051233094', '07063233199', '20031233334', '19042233199', '21003233282', '01020233574', '04057233235', '30023233634');
        $this->appUrl = Config::get("global.APP_URL");
        $this->baseStart = $this->appUrl . "import_prcessed_result/";
    }
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
        $html .= "<ol>";
        $html .= "<h2> Exam Subjects Fresh Stuent : ";

        $baseStart = $this->appUrl . "import_prcessed_result/import_prepare_exam_subjects/";
        $baseEnd = "/" . $offset . "/" . $limit;
        $html .= "<li><a target='_blank' href=" . $baseStart . "0/10" . $baseEnd . "> is_supplementary=0,course=10,offset=" . $offset . ",limit=" . $limit . "</a></li><br>";
        // $html .= "<li><a target='_blank' href=". $baseStart . "0/12" . $baseEnd . "> is_supplementary=0,course=12,offset=" . $offset .",limit=" . $limit ."</a></li><br>";

        //$html .= "</ol>";

        $html .= "<h2> Exam Subjects Supplementary Stuent :  ";
        //$html .= "<ol>";
        $baseStart = $this->appUrl . "import_prcessed_result/import_prepare_exam_subjects/";
        $baseEnd = "/" . $offset . "/" . $limit;
        $html .= "<li><a target='_blank' href=" . $baseStart . "1/10" . $baseEnd . "> is_supplementary=1,course=10,offset=" . $offset . ",limit=" . $limit . "</a></li><br>";
        // $html .= "<li><a target='_blank' href=". $baseStart . "1/12" . $baseEnd . "> is_supplementary=1,course=12,offset=" . $offset .",limit=" . $limit ."</a></li><br>";
        //$html .= "</ol>";


        $html .= "<h2 style='color:green;'> Result Fresh Stuent : ";
        //$html .= "<ol>";
        $baseStart = $this->appUrl . "import_prcessed_result/import_prepare_results/";
        $baseEnd = "/" . $offset . "/" . $limit;
        $html .= "<li><a target='_blank' href=" . $baseStart . "0/10" . $baseEnd . "> is_supplementary=0,course=10,offset=" . $offset . ",limit=" . $limit . "</a></li><br>";
        // $html .= "<li><a target='_blank' href=". $baseStart . "0/12" . $baseEnd . "> is_supplementary=0,course=12,offset=" . $offset .",limit=" . $limit ."</a></li><br>";

        //$html .= "</ol>";

        $html .= "<h2 style='color:green;'> Result Supplementary Stuent : ";
        //$html .= "<ol>";
        $baseStart = $this->appUrl . "import_prcessed_result/import_prepare_results/";
        $baseEnd = "/" . $offset . "/" . $limit;
        $html .= "<li><a target='_blank' href=" . $baseStart . "1/10" . $baseEnd . "> is_supplementary=1,course=10,offset=" . $offset . ",limit=" . $limit . "</a></li><br>";
        // $html .= "<li><a target='_blank' href=". $baseStart . "1/12" . $baseEnd . "> is_supplementary=1,course=12,offset=" . $offset .",limit=" . $limit ."</a></li><br>";

        $html .= "<br>Make sure run import_provisional_subject_and_results<br>";


        $html .= "</ol>";
        // $html .= "</h2><br>URL: result_process_start\is_supplementary\course\offset\limit<br>";
        // $otherLink = $this->appUrl . "supp_result_process/show_combination/0/1";
        // $html .= "Other Result <li><a target='_blank' href=". $otherLink . "> " . $otherLink ." </a></li><br>";
        echo $html;
        die;
    }

    public function import_prepare_exam_subjects($is_supplementary = 0, $course = null, $offset = 0, $limit = 3000, Request $request)
    {
        $exam_year = Config::get("global.admission_academicyear_id");
        $exam_month = Config::get("global.current_exam_month_id");

        $conditions = array();

        $conditions['prepare_exam_subjects.is_examsub_migrated'] = 0;
        $conditions['prepare_exam_subjects.course'] = $course;
        $conditions['prepare_exam_subjects.is_supplementary'] = $is_supplementary;
        $conditions['prepare_exam_subjects.exam_year'] = $exam_year;
        $conditions['prepare_exam_subjects.exam_month'] = $exam_month;


        $fields = array("id", "student_id", "course", "toc", "enrollment", "subject_id", "theory_marks", "practical_marks", "sessional_marks", "sessional_marks_reil_result", "total_marks", "final_result", "subject_type", "is_supplementary", "is_examsub_migrated", "is_grace_marks_given", "grace_marks");
        $studentLists = $this->_getValidStudentList($is_supplementary, $course, $offset, $limit);


        $subjectData = PrepareExamSubject::whereNull('prepare_exam_subjects.deleted_at')
            ->whereIn('student_id', $studentLists)
            ->where($conditions)->get($fields);


        if (@$subjectData && !empty($subjectData)) {
            $subjectData = $subjectData->toArray();
        } else {
            $subjectData = array();
        }

        $subjects = $this->subjectList($course);
        $subjectCodes = $this->subjectCodeList($course);
        $counter = 0;
        if (!empty($subjectData)) {
            if (!empty($subjectData)) {
                foreach ($subjectData as $key => $preData) {
                    $counter++;
                    $savedata = array();
                    $checkExamData = array();
                    if ($is_supplementary) {
                        $conditionsI = array();
                        $conditionsI['exam_subjects.exam_year'] = $exam_year;
                        $conditionsI['exam_subjects.exam_month'] = $exam_month;
                        $conditionsI['exam_subjects.course'] = $preData['course'];
                        $conditionsI['exam_subjects.subject_id'] = $preData['subject_id'];
                        $conditionsI['exam_subjects.student_id'] = $preData['student_id'];
                        $checkExamData = ExamSubject::whereNull('exam_subjects.deleted_at')->where($conditionsI)
                            ->orderBy("exam_year", "DESC")
                            ->orderBy("exam_month", "ASC")
                            ->take($limit)->skip($offset)->first(['id']);
                        if (@$checkExamData && !empty($checkExamData)) {
                            $checkExamData = $checkExamData->toArray();
                        } else {
                            $checkExamData = array();
                        }
                    } else {
                        $conditionsI = array();
                        $conditionsI['exam_subjects.course'] = $preData['course'];
                        $conditionsI['exam_subjects.subject_id'] = $preData['subject_id'];
                        $conditionsI['exam_subjects.student_id'] = $preData['student_id'];
                        $checkExamData = ExamSubject::whereNull('exam_subjects.deleted_at')->where($conditionsI)
                            ->orderBy("exam_year", "DESC")
                            ->orderBy("exam_month", "DESC")
                            ->take($limit)->skip($offset)->first(['id']);
                        if (@$checkExamData && !empty($checkExamData)) {
                            $checkExamData = $checkExamData->toArray();
                        } else {
                            $checkExamData = array();
                        }
                    }

                    $savedata['final_practical_marks'] = 0;
                    $savedata['final_theory_marks'] = 0;

                    if (@$preData['theory_marks']) {
                        $savedata['final_theory_marks'] = $preData['theory_marks'];
                        if (@$preData['is_grace_marks_given'] && $preData['is_grace_marks_given'] == 1) {
                            $savedata['final_theory_marks'] = $preData['theory_marks'] + $preData['grace_marks'];
                        }
                    }
                    if (@$preData['practical_marks']) {
                        $savedata['final_practical_marks'] = $preData['practical_marks'];
                        if (@$preData['is_grace_marks_given'] && $preData['is_grace_marks_given'] == 2) {
                            $savedata['final_practical_marks'] = $preData['practical_marks'] + $preData['grace_marks'];
                        }
                    }

                    $savedata['sessional_marks_reil_result'] = 0;
                    if (@$preData['sessional_marks_reil_result']) {
                        $savedata['sessional_marks_reil_result'] = $preData['sessional_marks_reil_result'];
                    }
                    $savedata['sessional_marks'] = $preData['sessional_marks'];
                    $savedata['total_marks'] = $preData['total_marks'];
                    $savedata['course'] = $preData['course'];
                    $savedata['final_result'] = $this->checkValType($preData['final_result']);

                    $savedata['exam_year'] = $exam_year;
                    $savedata['exam_month'] = $exam_month;
                    $savedata['is_supplementary_subject'] = $preData['is_supplementary'];
                    $savedata['subject_type'] = (!empty($preData['subject_type'])) ? $preData['subject_type'] : "";
                    $savedata['adm_type'] = "";
                    $savedata['is_temp_exam_subject'] = 1;
                    $savedata['enrollment'] = $preData['enrollment'];

                    if (@$checkExamData && @$checkExamData['id']) {
                        ExamSubject::where('id', $checkExamData['id'])->update($savedata);
                    } else {
                        $savedata['student_id'] = $preData['student_id'];
                        $savedata['subject_id'] = $preData['subject_id'];
                        if (ExamSubject::create($savedata)) {
                        } else {
                        }
                    }
                    // if($preData['subject_id']  == 28 ){
                    // 	echo "<pre>";
                    // 	print_r($preData);
                    // 	dd($savedata);
                    // }
                    unset($savedata);
                    $svdata = array();
                    $svdata['is_examsub_migrated'] = 1;
                    PrepareExamSubject::where('prepare_exam_subjects.id', $preData['id'])->update($svdata);

                    unset($svdata);
                }
            }
        }
        echo "<h1>Step 1 : " . $course . " prepare_exam_subjects data imported in exam_subjects " . $counter . "</h1>";
        if ($course == 10) {
            $course = 12;
            $newOffset = $offset + $limit;
            $newOffset = $offset;
            $baseAction = "import_prepare_exam_subjects";
            $this->setAndHitNextUrl($baseAction, $is_supplementary, $course, $newOffset, $limit);
        }
        die;
    }

    public function _getValidStudentList($is_supplementary = 0, $course = null, $offset = null, $limit = null)
    {
        $exam_year = Config::get("global.admission_academicyear_id");
        $exam_month = Config::get("global.current_exam_month_id");
        $conditions = array();
        $conditions['prepare_exam_subjects.is_examsub_migrated'] = 0;
        $conditions['prepare_exam_subjects.course'] = $course;
        $conditions['prepare_exam_subjects.is_supplementary'] = $is_supplementary;
        $conditions['prepare_exam_subjects.exam_year'] = $exam_year;
        $conditions['prepare_exam_subjects.exam_month'] = $exam_month;
        $conditionsStudentIds = array();
        // $conditionsStudentIds=array(502825);
        // $conditionsStudentIds=array(577939,614836,615293,615402,616145,619774,620681,622052,622955,624438,624785,624913,638405,642801,646643,649896,649910,649942,653136,661692,662417,671301,671303,672041,672062,672093,672154,672156,691830,692509,707951,708038,708295,714865,598849,604473,606558,614887,614982,614997,623182,627044,627083,643573,643884,646243,653837,660067,710737);
        $fields = array("student_id", "student_id");
        // dd($conditionsStudentIds);
        if (!empty($conditionsStudentIds)) {
            $studentLists = PrepareExamSubject::whereNull('prepare_exam_subjects.deleted_at')
                ->where($conditions)
                ->whereIn('prepare_exam_subjects.student_id', $conditionsStudentIds)
                ->take($limit)->skip($offset)
                ->groupBy("prepare_exam_subjects.student_id")
                ->pluck('student_id', 'student_id');
        } else {
            $studentLists = PrepareExamSubject::whereNull('prepare_exam_subjects.deleted_at')
                ->where($conditions)
                ->take($limit)->skip($offset)
                ->groupBy("prepare_exam_subjects.student_id")
                ->pluck('student_id', 'student_id');
        }

        return $studentLists;
    }

    public function checkValType($value = null)
    {
        if ($value != '' && $value != NULL) {
            if ($value == 'AB' || $value == 'A') {
                return 999; //For AB(Absent) value
            } else if ($value == 'SYCP') {
                return 666; //For SYCP value
            } else if ($value == 'SYCT') {
                return 777; //For SYCT value
            } else if ($value == 'SYC') {
                return 888; //For SYC(Supplementary) value
            } else if ($value == 'WH') {
                return 222; //For SYC(Supplementary) value
            } else if ($value == 'RW') {
                return 333; //For SYC(Supplementary) value
            } else if ($value == 'RWH') {
                return 444; //For SYC(Supplementary) value
            } else {
                return $value;
            }
        } else {
            return 0;
        }
    }

    /* Step 1 Move data from prepare_exam_subjects to exam_subjects */
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

    /* Step 2 Move data from prepare_exam_results to exam_results */

    public function import_prepare_results($is_supplementary = 0, $course = null, $offset = 0, $limit = 3000, Request $request)
    {
        $exam_year = Config::get("global.admission_academicyear_id");
        $exam_month = Config::get("global.current_exam_month_id");
        $conditions = array();
        $conditions['prepare_exam_results.is_examresult_migrated'] = 0;
        $conditions['prepare_exam_results.course'] = $course;
        $conditions['prepare_exam_results.is_supplementary'] = $is_supplementary;
        if ($is_supplementary == 0) {
            $conditions['prepare_exam_results.is_supplementary'] = null;
        }

        $conditions['prepare_exam_results.exam_year'] = $exam_year;
        $conditions['prepare_exam_results.exam_month'] = $exam_month;

        $fields = array("id", "student_id", "course", "revised", "enrollment", "additional_subjects", "result_date", "total_marks", "final_result", "is_supplementary");
        $resultData = PrepareExamResult::whereNull('prepare_exam_results.deleted_at')->where($conditions)->take($limit)->skip($offset)->get($fields);


        if (@$resultData && !empty($resultData)) {
            $resultData = $resultData->toArray();
        } else {
            $resultData = array();
        }
        $counter = 0;

        if (!empty($resultData)) {
            foreach ($resultData as $key => $preData) {
                $student = Student::where('students.id', $preData['student_id'])->get(['adm_type', 'course', 'id'])->first();
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

                $counter++;
                $savedata = array();
                $conditionsI = array();
                $conditionsI['exam_results.student_id'] = $preData['student_id'];
                $conditionsI['exam_results.exam_year'] = $exam_year;
                $conditionsI['exam_results.exam_month'] = $exam_month;
                $checkExamData = ExamResult::whereNull('exam_results.deleted_at')->where($conditionsI)->first(['id']);

                if (@$checkExamData && !empty($checkExamData)) {
                    $checkExamData = $checkExamData->toArray();
                } else {
                    $checkExamData = array();
                }
                $savedata['result_date'] = $preData['result_date'];
                $savedata['revised'] = $preData['revised'];
                $savedata['total_marks'] = $preData['total_marks'];
                $savedata['is_temp_examresult'] = 2;
                $savedata['additional_subjects'] = $preData['additional_subjects'];
                $savedata['percent_marks'] = 0;
                if (@$maxMarks > 0) {
                    $savedata['percent_marks'] = ($preData['total_marks'] / $maxMarks) * 100;
                }
                // $savedata['final_result'] = $this->checkValType($preData['final_result']);
                $savedata['final_result'] = $preData['final_result'];
                $savedata['exam_year'] = $savedata['exam_year_id'] = $exam_year;
                $savedata['exam_month'] = $exam_month;
                $savedata['supplementary'] = 0;
                if (@$preData['is_supplementary']) {
                    $savedata['supplementary'] = $preData['is_supplementary'];
                }

                $savedata['enrollment'] = $preData['enrollment'];
                if (@$checkExamData && @$checkExamData['id']) {
                    ExamResult::where('id', $checkExamData['id'])->update($savedata);
                } else {
                    $savedata['student_id'] = $preData['student_id'];
                    ExamResult::create($savedata);
                }
                unset($savedata);
                $svdata = array();
                $svdata['is_examresult_migrated'] = 1;
                PrepareExamResult::where('id', $preData['id'])->update($svdata);
                unset($svdata);
            }
        } else {
            echo "No Data Found Completed";
            die;
        }
        echo "<h1>" . $course . " Exam result import Completed " . $counter . "</h1>";
        // echo "Temp stoped";die;
        if ($course == 10) {
            $course = 12;
            $newOffset = $offset + $limit;
            $newOffset = $offset;
            $baseAction = "import_prepare_results";
            $this->setAndHitNextUrl($baseAction, $is_supplementary, $course, $newOffset, $limit);
        }
        die;
    }

    public function import_provisional_subecjt_and_results($exam_year = null, $exam_month = null)
    {
        echo "<h1>Two Task Completed</h1>";
        $result = DB::select('call setProvisionalSubjectAndResult(?,?)', array($exam_year, $exam_month));
        echo "Provisioanl Subject and Result has been imported successfully. <br>";

        $result = DB::select('call OptimizeAndCreateResultView(?,?)', array($exam_year, $exam_month));
        echo "Data imported successfully in table rs_provisional_result_views table.";

        die;
    }

    public function export_to_sessional_exam_subjects_from_exam_subjects($exam_year = null, $exam_month = null)
    {
        //125,1
        /*
            drop table if exists rs_sessional_exam_subjects;
            CREATE TABLE rs_sessional_exam_subjects (
            SELECT es.* FROM rs_students s
            INNER JOIN rs_exam_subjects es ON es.student_id = s.id AND es.exam_year = 125 AND es.exam_month = 1
            AND es.deleted_at IS NULL
            WHERE s.is_eligible = 1 AND s.deleted_at IS NULL AND s.exam_year = 125 AND s.exam_month = 1
            LIMIT 2000);
            SELECT count(id) from rs_sessional_exam_subjects;
            -- SELECT * from rs_sessional_exam_subjects;
        */
        $result = DB::select('call exportToSessionalExamSubjectsFromExamSubjects(?,?)', array($exam_year, $exam_month));
        echo "Export to sessional exam subjects from exam subjects has been successfully";
        die;
    }

    public function import_to_exam_subjects_from_sessional_exam_subjects($exam_year = null, $exam_month = null)
    {

        $result = DB::select('call importToSessionalExamSubjectsFromExamSubjects(?,?)', array($exam_year, $exam_month));

        echo "Export to sessional exam subjects from exam subjects has been successfully";
        die;
    }


}
	
