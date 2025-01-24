<?php 
namespace App\Component;
use DB;
use Cache;
use Config;
use Session;
use App\Helper\CustomHelper; 
use App\models\Application;
use App\Models\ExamcenterAllotment;
use App\models\Student;
use App\models\School;
use App\models\User;
use App\models\StudentFee;
use App\models\TocMark;
use App\models\Logs;
use App\models\ExamcenterDetail;
use App\models\CenterAllotment;  
use App\models\Supplementary;  
use App\models\SupplementarySubject;  
use App\models\ExamLateFeeDate;
use Carbon\Carbon;

use Validator; 


class CustomDeveloperComponent {

	public function _getSupplemetaryStudentDetailedFee($student_id=null){
		$studentdata = Student::with('application')->where('id',$student_id)->first();
		$condtions = null;
		$result = array();
		$mainTable = "fee_structures";

		$fld = "adm_type"; $$fld = $studentdata->$fld;
		$fld = "course"; $$fld = $studentdata->$fld;
		$fld = "is_jail_inmates";  $$fld = $studentdata->$fld;
		$fld = "gender_id";  $$fld = $studentdata->$fld;
		$fld = "category_a";  $$fld = $studentdata->application->$fld;
		$fld = "disability";  $$fld = $studentdata->application->$fld;
		$fld = "is_wdp_wpp";  $$fld = null; 
		
		$cacheName = "StudentDetailedFeeMaster_".$adm_type . "_ ". $course .  "_ ". $is_jail_inmates .  "_ ". $gender_id .  "_ ". $category_a .  "_ ". $disability .  "_ ". $is_wdp_wpp;
	
		/* Login start */ 
			$fld = "adm_type"; if(!empty($fld)){ $condtions[$fld] = $$fld; }
			$fld = "course"; if(!empty($fld)){ $condtions[$fld] = $$fld; }
			$fld = "gender_id"; if(!empty($fld)){ $condtions[$fld] = $$fld; }
			
			$fld = "is_jail_inmates"; 
			if(!empty($fld) && $fld == 1 ){ 
				$condtions[$fld] = $$fld; 
			}

			$fld = "disability";
			if(!empty($fld) && $fld != "10" ){ 
				$condtions[$fld] = 1; 
			}
			$categoryACondition = array();
			if(@$condtions[$fld]){}else{
				$fld = 'category_a';
				$categoryACondition = [$fld,[$studentdata->application->category_a]];
				$condtions[$fld] = [$fld,[$studentdata->application->category_a]];
			}

			if(@$condtions[$fld]){

			}else{
				$fld = "is_wdp_wpp";
				if(!empty($fld)){ 
					$condtions[$fld] = 1; 
				}
			}
 

		/* Login end */
		
		Cache::forget($cacheName);
		if (Cache::has($cacheName)){ 
			$result = Cache::get($cacheName);
		} else {
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable,$studentdata, $categoryACondition) {
				if(@$categoryACondition){
					$result = DB::table($mainTable)->where($condtions)->whereIn($categoryACondition)->first(); 
				}else{
					$result = DB::table($mainTable)->where($condtions)->first(); 
				} 
				return $result;
					
			});			
		}  
		return $result;
	}

		   public function _getSuppLateFeeAmount($stream=null,$gender_id=null){
				$master = ExamLateFeeDate::where('stream',$stream)
					->where('gender_id',$gender_id)
					->where('from_date', '<=', Carbon::now())
					->where('to_date', '>=', Carbon::now())
					->first();
				if(!@$master->late_fee){
					return 0;
				}
				return @$master->late_fee;
			}	
	
	public function _getFeeDetailsForDispaly($student_id){
		$supp_stream = Config::get("global.supp_stream");
		$exam_year = CustomHelper::_get_selected_sessions(); 
		$exam_month = CustomHelper::_get_selected_sessions(); 
		$conditions['students.exam_year'] = $exam_year;
		$conditions['students.id'] = $student_id;
		$conditionssupplementaries['supplementaries.exam_year'] = $exam_year;
		$conditionssupplementaries['supplementaries.student_id'] = $student_id;
		$result = array();
		$studentdata = Student::where($conditions)->first(['course','exam_month','gender_id']);
		$exam_month = $studentdata->exam_month;
	    $master_supplementary_fees =  DB::table('master_supplementary_fees')
			->where('exam_year',$exam_year)
			->where('exam_month',$exam_month)
			->where('course',$studentdata->course)
			->first();
		$exam_fees = $master_supplementary_fees->exam_fees;
		$forward_fees = $master_supplementary_fees->forward_fees;
		$online_services_fees = $master_supplementary_fees->online_services_fees;
		$subject_wise_fees = $master_supplementary_fees->subject_fees;
		$practical_subject_wise_fees = $master_supplementary_fees->practical_fees;
		
		$complusery_subject = Supplementary::where($conditionssupplementaries)->join('supplementary_subjects', 'supplementary_subjects.student_id', '=', 'supplementaries.student_id')
			->where('supplementary_subjects.is_additional_subject','=', '')
			->where('supplementary_subjects.previous_subject_id','!=', '')
			->count();		
		$suppPracticalSubjectCount	= SupplementarySubject::join('subjects', 'subjects.id', '=', 'supplementary_subjects.subject_id')
            ->where('subjects.practical_type',1)
			->where('supplementary_subjects.student_id',$student_id)
            ->count();
		$examsubjectfees = SupplementarySubject::where('supplementary_subjects.student_id',$student_id)->count();
		$lateFees = $this->_getSuppLateFeeAmount($supp_stream,@$studentdata->gender_id);
		$complusery_subject_fees = $complusery_subject * $subject_wise_fees;
		$complusery_practical_subject_fees = $suppPracticalSubjectCount * $practical_subject_wise_fees;
		$exam_subject_fees = $examsubjectfees * $exam_fees;
		$finalFees = $complusery_subject_fees + $complusery_practical_subject_fees + $exam_subject_fees + $forward_fees + $online_services_fees + $lateFees;
		    
		$result['online_services_fees'] = @$online_services_fees;
		$result['subject_change_fees'] = @$complusery_subject_fees;
		$result['practical_fees'] = @$complusery_practical_subject_fees;
		$result['exam_subject_fees'] = @$exam_subject_fees;
		$result['forward_fees'] = @$forward_fees;
		$result['late_fees'] = @$lateFees;
		$result['final_fees'] = @$finalFees; 
		return $result;
	}
	
}