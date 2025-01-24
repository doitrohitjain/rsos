<?php 
namespace App\Component;
use DB;
use Cache;
use Config;
use Session;
use App\Helper\CustomHelper; 
use App\models\Application;
use App\Models\ExamcenterAllotment;
use App\Models\StudentAllotment;
use App\models\Student;
use App\models\School;
use App\models\StudentUpdate;
use App\models\User;
use App\models\Supplementary;
use App\models\StudentFee;
use App\models\SuppPaymentIssue;
use App\Models\PaymentIssue;
use App\models\TocMark;
use App\models\Logs;
use App\Models\AllotingCopiesExaminer;
use App\Models\MarkingAbsentStudent;
use App\models\ExamResult;
use App\models\ModelHasRole;
use App\models\ExamcenterDetail;
use App\models\MasterQuerieExcel;
use App\models\CenterAllotment;  
use App\models\MasterAdminDocument;
use App\models\ExamSubject; 
use Validator; 
use Auth; 
use App\Http\Controllers\Controller;
use App\models\SupplementarySubject;  
use App\models\ExamLateFeeDate;
use Carbon\Carbon;
use App\models\AiCenterMap;
use App\Models\TimeTable;
use App\Models\UserExaminerMap;
use App\Component\CustomComponent; 
use App\models\Document;
use App\models\Address;
use App\models\Subject;
use PDF;
use Response;
use App\models\Pastdata;
use App\models\MarksheetPrint;
use App\models\AicenterDetail;
use App\models\BankMaster;
use Route;
use Illuminate\Support\Facades\Crypt;

class WebapiupdateComponent {
	
     public function getAPIStudentLoginDetails($ssoid=null,$dob=null,$st_key=null){
        $master = array();
        $studentFields = array('students.id','students.is_eligible','students.course','students.stream','students.enrollment','students.name','students.dob','students.ssoid','students.adm_type','students.student_status_at_different_level','students.exam_year','students.exam_month','applications.is_ready_for_verifying');
        //$student = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where('students.ssoid',$ssoid)->where('students.dob',$dob)->first($studentFields);
        // $student = Student::where('students.ssoid',$ssoid)->where('students.dob',$dob)->first($studentFields);
       
        $student = array();
		
		$admissistuon_academicyear_id = Config::get("global.admissistuon_academicyear_id");
		$current_exam_month_id = Config::get("global.current_exam_month_id");
				
		$objController = new Controller();  
		$combo_name = 'course';$course = $objController->master_details($combo_name);
		$combo_name = 'stream_id';$stream_id = $objController->master_details($combo_name);
		$combo_name = 'adm_type';$adm_types = $objController->master_details($combo_name);
		
        $cacheName = "api_student_login_". $ssoid . "_". $dob;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $student = Cache::get($cacheName);
        }else{
            $student = Cache::rememberForever($cacheName, function () use ($course,$adm_types,$stream_id,$ssoid,$dob,$st_key,$studentFields,$admissistuon_academicyear_id,$current_exam_month_id) { 
				if(@$st_key){ 
					$temp = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where('applications.locksumbitted','1')->where('students.ssoid',$ssoid)->where('students.id',$st_key)->where('students.dob',$dob)
					//->where('students.exam_year',$admissistuon_academicyear_id)
					// ->where('students.exam_month',$current_exam_month_id)
					->orderBy('students.exam_year','DESC')->orderBy('students.exam_month','asc')
					->first($studentFields);
					if(@$temp->id){
						$temp->st_key = @$temp->id;  
						$temp->enc_enrollment = Crypt::encrypt(@$temp->enrollment);  
						$temp->display = @$course[@$temp->course] . " - ".  @$stream_id[@$temp->stream] . " - ".  @$adm_types[@$temp['adm_type']]; ;  
						unset($temp->id);
					}
					return $result = $student = $temp;
				} 
                $temp = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where('applications.locksumbitted','1')
				->where('students.ssoid',$ssoid)->where('students.dob',$dob)
				// ->where('students.exam_year',$admissistuon_academicyear_id)
				// ->where('students.exam_month',$current_exam_month_id)
				->orderBy('students.exam_year','DESC')->orderBy('students.exam_month','asc')
				->first($studentFields); 
				if(@$temp->id){
					$temp->st_key = @$temp->id;   
					$temp->enc_enrollment = Crypt::encrypt(@$temp->enrollment);  
					$temp->display = @$course[@$temp->course] . " - ".  @$stream_id[@$temp->stream] . " - ".  @$adm_types[@$temp['adm_type']];
					unset($temp->id);
				}
				return $result = $student = $temp;
            });
        }   
        
        if(@$student){
            $master['student'] = $student->toArray();
        } 
        return $master;
    }
	
	public function getAPIStudentEnrollmentList($ssoid=null,$dob=null){
		$master = array();
		$studentFields = array('students.id','students.id'); 
		
		$student = array();
		$masterFinal = array();
		$admissistuon_academicyear_id = Config::get("global.admissistuon_academicyear_id");
		$current_exam_month_id = Config::get("global.current_exam_month_id");
				
		$cacheName = "api_student_enrollment_list_". $ssoid . "_". $dob;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			//$student = Cache::get($cacheName);
		}else{
			$student = Cache::rememberForever($cacheName, function () use ($ssoid,$dob,$studentFields,$admissistuon_academicyear_id,$current_exam_month_id) {
				//need bind with applications and get course stream enrolment and id for array return.
				$student = Student::where('students.ssoid',$ssoid)->where('students.dob',$dob)
					// ->where('students.exam_year',$admissistuon_academicyear_id)
					// ->whereNotNull('students.enrollment')
					// ->where('students.exam_month',$current_exam_month_id)
					->orderBy('students.exam_year','DESC')->orderBy('students.exam_month','asc')
					->get(['students.stream','students.id','students.course','students.adm_type']);
					return $student;
					 
				});
		}  
		if(@$student){
			$master = $student->toArray();
			
			$objController = new Controller();  	
			$combo_name = 'course';$course = $objController->master_details($combo_name);
			$combo_name = 'stream_id';$stream_id = $objController->master_details($combo_name);
			$combo_name = 'adm_type';$adm_types = $objController->master_details($combo_name);
			if(count($master) > 0){
				foreach($master as $k => $value){
					$masterFinal['display'][@$value['id']] = @$course[@$value['course']] . " - ".  @$stream_id[@$value['stream']] . " - ".  @$adm_types[@$value['adm_type']];  
					$masterFinal['st_keys'][@$value['id']] = @$value['id'];
				}				
			}
		} 
		return $masterFinal;
	}

    
	public function getAPIStudentDetails($ssoid=null,$dob=null,$st_key=null){
        $master = array();
        $studentFields = array('students.id','students.ai_code','students.enrollment','students.name','students.dob','students.ssoid','students.father_name','students.mother_name','students.gender_id','students.course','students.is_eligible');
        $student = Student::where('students.ssoid',$ssoid)->where('students.id',$st_key)->where('students.dob',$dob)->orderBy('students.exam_year','DESC')->orderBy('students.exam_month','asc')->first($studentFields);
        $custom_component_obj = new CustomComponent;
        $aiCenters = $custom_component_obj->getAiCenters();
        if(@$student){
            $master['student'] = $student->toArray(); 
            $master['student']['ai_center_name'] =  @$aiCenters[@$master['student']['ai_code']];
            $student_id = $master['student']['id'];
            if(false){
                // $examResultFields = array("final_result","result_date","total_marks","percent_marks","supplementary","additional_subjects");
                // $exam_result = ExamResult::where('student_id','=',$student_id)->orderBy('exam_year','desc')->orderBy('exam_month','asc')->first($examResultFields);
                // if(@$exam_result){
                //     $master['exam_result'] = $exam_result->toArray();
                // }
            }
            // $examsubjectfields = array( 'subject_id','final_theory_marks','final_practical_marks','sessional_marks_reil_result','total_marks','final_result');
			
			
            $examsubjectfields = array( 'subject_id');				
            // $exam_subjects =  ExamSubject::where('student_id','=',$student_id)->orderBy('exam_year','desc')->orderBy('exam_month','asc')->orderBy('subject_id','desc')->groupBy('subject_id')->get($examsubjectfields);
			$exam_subjects = array();
            
			
			$cacheName = "api_exam_subject_details_". $ssoid . "_".$student_id; 
			
			//Cache::forget($cacheName);
			if (Cache::has($cacheName)) {
				$exam_subjects = Cache::get($cacheName);
			}else{
				$exam_subjects = Cache::rememberForever($cacheName, function () use ($student_id,$examsubjectfields) {
					return $result = $exam_subjects =  ExamSubject::where('student_id','=',$student_id)->orderBy('exam_year','desc')->orderBy('exam_month','asc')->orderBy('subject_id','desc')->groupBy('subject_id')->get($examsubjectfields);
						
				});		
			}   
            if(@$exam_subjects){
                $master['exam_subjects'] = $exam_subjects->toArray();
            }
        } 
        return $master;
    } 

    public function setAPIStudentSessionalMarksDetails($inputs=null){
        $response = "Someting is wrong";
        $enrollment = @$inputs['enrollment']; 

        $exam_year = Config::get("global.current_sessional_exam_year");
        $exam_month = Config::get("global.current_sessional_exam_month");

        $studentFields = array('students.id','students.exam_year','students.exam_month');
        $student = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where('students.is_eligible',1)->where('students.dob',@$enrollment)->first($studentFields);
        $student_id = $student['student']['id'];
        if(@$student_id){
            foreach($inputs as $k => $v){
                $subject_id = @$v['subject_id'];
                $student_id = @$v['student_id'];
                $value = $v['sesional_marks'];
                if($value == 'AB'){
                    $value = '999';
                }
                $saveData['sesional_marks'] = $value;
                $saveData['is_sessional_mark_entered'] = 1;
                SessionalExamSubject::where('exam_year',$exam_year)->where('exam_month',$exam_month)->where('student_id',$student_id)->where('subject_id',$subject_id)->update($saveData);
            } 
            $response = "Sessional marks has been udpated succefully.";
        }  
        return $response;
    }
        
	 
}


