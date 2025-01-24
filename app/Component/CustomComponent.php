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
use App\models\DocumentVerification;
use App\models\User;
use App\models\Supplementary;
use App\models\StudentFee;
use App\models\SuppPaymentIssue;
use App\models\MarksheetPaymentIssue;
use App\Models\PaymentIssue;
use App\models\TocMark;
use App\models\Logs;
use App\Models\AllotingCopiesExaminer;
use App\Models\RevalStudent;
use App\Models\MarkingAbsentStudent;
use App\models\ExamResult;
use App\models\ModelHasRole;
use App\models\ExamcenterDetail;
use App\models\MasterQuerieExcel;
use App\models\CenterAllotment;  
use App\models\MasterAdminDocument;
use App\models\ExamSubject; 
use App\models\StudentOrgFee; 
use App\models\ChangeRequestStudent;
use Validator; 
use Auth; 
use App\Http\Controllers\Controller;
use App\models\SupplementarySubject;  
use App\models\ExamLateFeeDate;
use Carbon\Carbon;
use App\models\AiCenterMap;
use App\Models\TimeTable;
use App\Models\UserExaminerMap;
use App\Component\MarksheetCustomComponent; 
use App\models\Document;
use App\models\Address;
use App\models\Subject;
use PDF;
use Response;
use App\models\Pastdata;
use App\models\MarksheetPrint;
use App\models\AicenterDetail;
use App\models\BankMaster;
use App\models\StudentAllotmentMark;
use App\Models\SingleScreenDate;
use Route;
use App\Models\VerificationLabel;
use App\Models\VerificationMaster;
use App\Models\MarksheetMigrationRequest;
use App\Models\ChangeRequestPaymentIssue;
use Illuminate\Support\Facades\Crypt;
use App\http\Traits\QrCode;
use Illuminate\Support\Facades\URL;
use App\Models\SuppChangeRequestStudents;
class CustomComponent {
	
	public static function _getCountDownTimerDetails(){
		$login = CustomHelper::_getCountDownTimerDetails();
		return json_encode($login);
	}

	public static function _getAllowYearCombo(){
		$objController = new Controller();
		$combo_name="examresult_year_monthcombo";$examresult_year_monthcombo=$objController->master_details($combo_name);
		$combo_name="admission_sessions";$admission_sessions=$objController->master_details($combo_name)->toArray();
		$conditions = null;
		$cacheName = "getAllowYearCombo_";
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) { //
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($examresult_year_monthcombo, $admission_sessions) {
				$names = array();
				foreach($examresult_year_monthcombo as $key => $value){
					$combo = explode("_",$value);
					$names[] = @$admission_sessions[@$combo[0]];
				}
				sort($names);
				$names = array_unique($names);
				$namesToBeShow = null;

				foreach($names as $key => $name){
					$combo = explode("-",$name);
					$namesToBeShow[] = @$combo[0];
				}

				$maxYear = max($namesToBeShow);
				$minYear = min($namesToBeShow);
				$maxYear = 2024;
				$namesToBeShow = $minYear . " to "  . $maxYear;

				$result = "You are allowed to see the result of the years (". $namesToBeShow . "). आपको वर्षों का परिणाम देखने की अनुमति है(". $namesToBeShow . ").";
				return $result;
			});
		}
		return $result;
	}

	public static function _getexamcenterdatils($ai_code=null){

		$exam_year =CustomHelper::_get_selected_sessions();
		$exam_month = config("global.CenterAllotmentStreamId");
		$studentAllotmentData = DB::select("select examcenter_detail_id,course,ai_code from rs_student_allotments where exam_year = $exam_year and exam_month = $exam_month and deleted_at IS null and ai_code = $ai_code GROUP BY ai_code,course,examcenter_detail_id;");
		if(@$studentAllotmentData){
			 return  json_encode($studentAllotmentData);
		}
		return false;

	}
	
	public function getallStudentCount($auth_user_id=null,$exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
        if(!empty($exam_month)){
			$conditions['students.exam_month']=$exam_month;
		}
		$conditions['students.exam_year'] = $selected_session;
		if(!empty($auth_user_id) && $auth_user_id!=null){
		$total_registred_all_students = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereIn('students.ai_code',$auth_user_id)->count();
		}
		else{
		$total_registred_all_students = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->count();
		}

		//$total_registred_all_students =0;


		return $total_registred_all_students;
    }

	public function getallStudentWhoseSSOIdMapped($auth_user_id=null,$exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
        if(!empty($exam_month)){
			$conditions['students.exam_month']=$exam_month;
		}
		$conditions['students.exam_year'] = $selected_session;
		if(!empty($auth_user_id) && $auth_user_id != null){
			$total_registred_all_students = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->whereNotNull('students.ssoid')->where($conditions)->whereIn('students.ai_code',$auth_user_id)->count();
		}else{
		$total_registred_all_students = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->whereNotNull('students.ssoid')->where($conditions)->count();
		}
		return $total_registred_all_students;


    }
	 
    public function getallStudentLockSubmitCount($auth_user_id=null,$exam_month=null){
		$conditions = array();
		$conditions['applications.locksumbitted'] = 1;
        if(!empty($exam_month)){
			$conditions['students.exam_month']=$exam_month;
		}
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['students.exam_year'] = $selected_session;
		if(!empty($auth_user_id) && $auth_user_id!=null){
		$total_lock_submit_all_student = Student::join('applications', 'applications.student_id', '=', 'students.id')
		->where($conditions)->whereIn('students.ai_code',$auth_user_id)->count();
		}else{
		$total_lock_submit_all_student = Student::join('applications', 'applications.student_id', '=', 'students.id')
		->where($conditions)->count();
		}
		return $total_lock_submit_all_student;
    }

	public function getVerificationPart($auth_user_id=null,$exam_month=null,$is_verifier_verify=null){
		$conditions = array();
		$conditions['applications.is_ready_for_verifying'] = 1;
		$conditions['applications.locksumbitted'] = 1;
		$selected_session = CustomHelper::_get_selected_sessions();

		$conditions['students.exam_year'] = $selected_session;

        if(!empty($exam_month)){
			$conditions['students.exam_month']=$exam_month;
		}

		$role_id = Session::get('role_id');
		$verifier_id = config("global.verifier_id");
		$super_admin_id = config("global.super_admin_id");
		if($is_verifier_verify != "all"){
			$conditions['students.verifier_status'] = $is_verifier_verify;
		}

		//$total_all_student = 0;
		if(!empty($auth_user_id) && $auth_user_id != null){


			$total_all_student = Student::join('applications', 'applications.student_id', '=', 'students.id')
			->where($conditions)->whereIn('students.ai_code', $auth_user_id)->count();


		}else{
			//$total_all_student = Student::join('applications', 'applications.student_id', '=', 'students.id')
			//->where($conditions)->count();
		}

		return $total_all_student;
    }

	public function getAOVerificationPart($auth_user_id=null,$exam_month=null,$rec_role_id=null,$is_verifier_verify=null){
		$conditions = array();
		$applicationConditions = array();
		$applicationConditions['applications.is_ready_for_verifying'] = 1;
		$applicationConditions['applications.locksumbitted'] = 1;
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['students.exam_year'] = $selected_session;

        if(!empty($exam_month)){
			$conditions['students.exam_month']=$exam_month;
		}

		$role_id = Session::get('role_id');
		$verifier_id = config("global.verifier_id");
		$super_admin_id = config("global.super_admin_id");
		$academicofficer_id = config('global.academicofficer_id');
		$NotInConditions = 'null';

		if($is_verifier_verify != "all"){
			if($rec_role_id == $verifier_id){
				$conditions['students.verifier_status'] = $is_verifier_verify;
			}
			if($rec_role_id == $academicofficer_id){
				$conditions['students.ao_status'] = $is_verifier_verify;
				//not in 1 verifier_status
			}
		}else{
			if($rec_role_id == $academicofficer_id){
				//not in 1 verifier_status
			}
		}

		$total_all_student = 0;

		if(!empty($auth_user_id) && $auth_user_id != null){

			if($rec_role_id == $academicofficer_id){

				$total_all_student = Student::
				// join('applications', 'applications.student_id', '=', 'students.id')
				// ->
				where($conditions)->whereNotIn('students.verifier_status',[1])->whereIn('students.ai_code',$auth_user_id)
				->whereExists(function($query) use ($applicationConditions) {
					$query->select(DB::raw(1))
						  ->from('applications')
						  ->whereColumn('applications.student_id', 'students.id')
						  ->where($applicationConditions);
				})
				->count();
			}else{

				$total_all_student = Student::
				// join('applications', 'applications.student_id', '=', 'students.id')
				// ->
				where($conditions)->whereIn('students.ai_code',$auth_user_id)
				->whereExists(function($query) use ($applicationConditions) {
					$query->select(DB::raw(1))
						  ->from('applications')
						  ->whereColumn('applications.student_id', 'students.id')
						  ->where($applicationConditions);
				})
				->count();
				// dd($conditions);
			}
		}else{
			if($rec_role_id == $academicofficer_id){
				$total_all_student = Student::
				// join('applications', 'applications.student_id', '=', 'students.id')
				// ->
				where($conditions)->whereNotIn('students.verifier_status',[1])
				->whereExists(function($query) use ($applicationConditions) {
					$query->select(DB::raw(1))
						  ->from('applications')
						  ->whereColumn('applications.student_id', 'students.id')
						  ->where($applicationConditions);
				})
				->count();

			}else{
				$total_all_student = Student::
				// join('applications', 'applications.student_id', '=', 'students.id')
				// ->
				where($conditions)
				->whereExists(function($query) use ($applicationConditions) {
					$query->select(DB::raw(1))
						  ->from('applications')
						  ->whereColumn('applications.student_id', 'students.id')
						  ->where($applicationConditions);;
				})
				->count();
			}
		}
		return $total_all_student;
    }

	public function getAOAgreeeOrNotWithVerifier($exam_month = null,$is_ao_agree_with_verifier=null,$aicenter_mapped_data=null){
		$conditions = array();
		$total_all_student = 0;
		$conditions['applications.is_ready_for_verifying'] = 1;
		$conditions['applications.locksumbitted'] = 1;
		$selected_session = CustomHelper::_get_selected_sessions();
		$rawCond1 = 1;
		$conditions['students.exam_year'] = $selected_session;
        if(!empty($exam_month)){
			$conditions['students.exam_month']=$exam_month;
		}

		if(@$aicenter_mapped_data && $aicenter_mapped_data != null){
			$aicenter_mapped_data = implode(",",@$aicenter_mapped_data);
			$rawCond1 = ' rs_students.ai_code in ( ' .   @$aicenter_mapped_data . " ) ";
		}

        if(!empty($is_ao_agree_with_verifier)){
			$conditions['student_verifications.is_ao_agree_with_verifier']=$is_ao_agree_with_verifier;
		}

		$role_id = Session::get('role_id');
		$academicofficer_id = config('global.academicofficer_id');

	   if(@$aicenter_mapped_data && $aicenter_mapped_data != null){
			$total_all_student = Student::join('applications', 'applications.student_id', '=', 'students.id')
			->LeftJoin('student_verifications', 'student_verifications.student_id', '=', 'students.id')
			->where($conditions)->whereRaw($rawCond1)->count();
	   }

		return $total_all_student;
    }
	 
	public function getDeptVerificationPermanantRejectedPart($is_permanent_rejected_by_dept=1,$exam_month=null){
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['students.exam_year']=@$selected_session;
		$conditions['students.exam_month']=@$exam_month;

		$conditions['student_verifications.is_permanent_rejected_by_dept'] = $is_permanent_rejected_by_dept;
		$count = DocumentVerification::Join('students', 'student_verifications.student_id', '=', 'students.id')
				->Join('applications', 'applications.student_id', '=', 'students.id')
				->where($conditions)
				->where('applications.is_ready_for_verifying',1)
				->count();
		return $count;
	}

	public function getDeptVerificationPart($auth_user_id=null,$exam_month=null,$rec_role_id=null,$is_verifier_verify=null){
		$conditions = array();
		$conditions['applications.is_ready_for_verifying'] = 1;
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['students.exam_year'] = $selected_session;
		$conditions['applications.locksumbitted'] = 1;

        if(!empty($exam_month)){
			$conditions['students.exam_month']=$exam_month;
		}

		$role_id = Session::get('role_id');
		$verifier_id = config("global.verifier_id");
		$super_admin_id = config("global.super_admin_id");
		$academicofficer_id = config("global.academicofficer_id");
		$NotInConditions = 'null';


		if($is_verifier_verify == "all"){

		}else{
			if($rec_role_id == $super_admin_id){
				$conditions['students.department_status'] = $is_verifier_verify;
			}else if($rec_role_id == $academicofficer_id){
				$conditions['students.ao_status'] = $is_verifier_verify;
			}else if($rec_role_id == $verifier_id){
				$conditions['students.verifier_status'] = $is_verifier_verify;
			}
		}
		$total_all_student = 0;

		if(!empty($auth_user_id) && $auth_user_id != null){
			if($rec_role_id == $super_admin_id){
				if($is_verifier_verify == 10){
					$total_all_student = Student::
					LeftJoin('student_verifications', 'student_verifications.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')
					->where($conditions)->whereNull('student_verifications.deleted_at')
					->whereIn('students.ai_code',$auth_user_id)->distinct('students.id')->count();
				}else{
					$total_all_student = Student::
					LeftJoin('student_verifications', 'student_verifications.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')
					->where($conditions)->whereNull('student_verifications.deleted_at')
					->whereIn('students.stage',[4,5])
					->whereIn('students.ai_code',$auth_user_id)->distinct('students.id')->count();
				}
			}elseif($rec_role_id == $academicofficer_id){
				$total_all_student = Student::
				LeftJoin('student_verifications', 'student_verifications.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')
				->where($conditions)->whereNotIn('students.verifier_status',[1])->whereIn('students.ai_code',$auth_user_id)->whereNull('student_verifications.deleted_at')->distinct('students.id')->count();
			}elseif($rec_role_id == $verifier_id){
				$total_all_student = Student::
				LeftJoin('student_verifications', 'student_verifications.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')
				->where($conditions)->whereIn('students.ai_code',$auth_user_id)->whereNull('student_verifications.deleted_at')->distinct('students.id')->count();
			}else{
				$total_all_student = Student::
				LeftJoin('student_verifications', 'student_verifications.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')
				->where($conditions)->whereIn('students.ai_code',$auth_user_id)->whereNull('student_verifications.deleted_at')->distinct('students.id')->count();
			}
		}else{
			if($rec_role_id == $super_admin_id){
				if($is_verifier_verify == 10){
					$total_all_student = Student::
					LeftJoin('student_verifications', 'student_verifications.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')
					->where($conditions)->whereIn('students.stage',[4,5])->whereNull('student_verifications.deleted_at')->distinct('students.id')
					->count();
				}else{
					$total_all_student = Student::
					LeftJoin('student_verifications', 'student_verifications.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')
					->where($conditions)->whereNull('student_verifications.deleted_at')
					->whereIn('students.stage',[4,5])->distinct('students.id')
					->count();
				}

			}elseif($rec_role_id == $academicofficer_id){
				$total_all_student = Student::
				LeftJoin('student_verifications', 'student_verifications.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')
				->where($conditions)->whereNotIn('students.verifier_status',[1])->distinct('students.id')->whereNull('student_verifications.deleted_at')->count();
			}elseif($rec_role_id == $verifier_id){

				// dd($conditions);
				$total_all_student = Student::
				LeftJoin('student_verifications', 'student_verifications.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')->whereNull('student_verifications.deleted_at')
				->where($conditions)->distinct('students.id')->count();
			}else{
				$total_all_student = Student::
				LeftJoin('student_verifications', 'student_verifications.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')->whereNull('student_verifications.deleted_at')
				->where($conditions)->distinct('students.id')->count();
			}
		}
		return $total_all_student;
    }
	
	public function getallStudentEligibleCount($auth_user_id=null,$exam_month=null){
		$conditions = array();
		$conditions['is_eligible'] = 1;
        if(!empty($exam_month)){
			$conditions['students.exam_month']=$exam_month;
		}
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['students.exam_year'] = $selected_session;
		if(!empty($auth_user_id) && $auth_user_id!=null){
			$total_eligible_all_student = Student::join('applications', 'applications.student_id', '=', 'students.id')
		->where($conditions)
		->whereNull('applications.deleted_at')->whereIn('students.ai_code',$auth_user_id)
		->count();
		}else{
		$total_eligible_all_student = Student::join('applications', 'applications.student_id', '=', 'students.id')
		->where($conditions)
		->whereNull('applications.deleted_at')->count();
		}
		return $total_eligible_all_student;
    }

    public function getallStudentpaymentCount($auth_user_id=null,$exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
        if(!empty($exam_month)){
			$conditions['students.exam_month']=$exam_month;
		}
		$conditions['students.exam_year'] = $selected_session;
		if(!empty($auth_user_id) && $auth_user_id!=null){
			$total_registred_all_students_payment = Student::JOIN('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereNotNull('students.challan_tid')->whereNotNull('students.submitted')->where('applications.locksumbitted',1)->whereIn('students.ai_code',$auth_user_id)->count();
		}else{
		$total_registred_all_students_payment = Student::JOIN('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereNotNull('students.challan_tid')->whereNotNull('students.submitted')->where('applications.locksumbitted',1)->count();
		}
		return $total_registred_all_students_payment;
    }

    public function getallEligibleStudentpaymentCount($auth_user_id=null,$exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
        if(!empty($exam_month)){
			$conditions['students.exam_month']=$exam_month;
		}
		$conditions['exam_year'] = $selected_session;
		if(!empty($auth_user_id) && $auth_user_id!=null){
			$conditions['ai_code']=$auth_user_id;
		}

		$total_registred_all_students_payment = Student::where($conditions)->where('is_eligible',1)
									->count();

		return $total_registred_all_students_payment;
    }
	
      public function getallStudentpaymentnotpayCount($auth_user_id=null,$exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
        if(!empty($exam_month)){
			$conditions['students.exam_month']=$exam_month;
		}
		$conditions['students.exam_year'] = $selected_session;
		if(!empty($auth_user_id) && $auth_user_id!=null){
		  $total_registred_students_payment_notpay = Student::join('applications', 'applications.student_id', '=', 'students.id')->join('student_fees', 'student_fees.student_id', '=', 'students.id')->whereNull('students.challan_tid')
		->where('student_fees.total', ">",0)
		->where($conditions)
		->where('applications.locksumbitted',1)
		->whereNull('student_fees.deleted_at')
		->whereIn('students.ai_code',$auth_user_id)->orderBy('student_fees.id','desc')->count();
		}else{
			$total_registred_students_payment_notpay = Student::join('applications', 'applications.student_id', '=', 'students.id')->join('student_fees', 'student_fees.student_id', '=', 'students.id')->whereNull('students.challan_tid')
		->where('student_fees.total', ">",0)
		->where($conditions)
		->whereNull('student_fees.deleted_at')
		->where('applications.locksumbitted',1)->orderBy('student_fees.id','desc')->count();
		}
		return $total_registred_students_payment_notpay;
    }
	
    public function getallStudentzerofeespaymentCount($auth_user_id=null,$exam_month=null){

		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['students.exam_year'] = $selected_session;
        if(!empty($exam_month)){
			$conditions['students.exam_month']=$exam_month;
		}
		if(!empty($auth_user_id) && $auth_user_id!=null){
			$total_registred_students_payment_notpay = Student::join('applications', 'applications.student_id', '=', 'students.id')->join('student_fees', 'student_fees.student_id', '=', 'students.id')->whereNull('students.challan_tid')->where('student_fees.total',0)
		->where($conditions)->where('applications.locksumbitted',1)->whereNull('student_fees.deleted_at')->whereIn('students.ai_code',$auth_user_id)->count();
		}else{
		$total_registred_students_payment_notpay = Student::join('applications', 'applications.student_id', '=', 'students.id')->join('student_fees', 'student_fees.student_id', '=', 'students.id')->whereNull('students.challan_tid')->where('student_fees.total',0)
		->where($conditions)->where('applications.locksumbitted',1)->whereNull('student_fees.deleted_at')->count();
		}
		return $total_registred_students_payment_notpay;

    }
	
	public function getsupplementaryallStudentCount($aicenter_mapped_data=null,$exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
		}
		if(!empty($aicenter_mapped_data) && $aicenter_mapped_data!=null){
			$supplementary_total_registred_all_students = Supplementary::where($conditions)->whereIn('ai_code',$aicenter_mapped_data)->count();
		}else{
			$supplementary_total_registred_all_students = Supplementary::where($conditions)->count();
		}

		return $supplementary_total_registred_all_students;
	}
	
	 public function getRevalallStudentCount($aicenter_mapped_data=null,$exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
		}

		if(!empty($aicenter_mapped_data) && $aicenter_mapped_data!=null){
			$reval_total_registred_all_students = RevalStudent::where($conditions)->whereIn('ai_code',$aicenter_mapped_data)->count();
		}else{
			$reval_total_registred_all_students = RevalStudent::where($conditions)->count();
		}

		return $reval_total_registred_all_students;
	}
	
	public function getRevalallStudentLockSubmitCount($aicenter_mapped_data=null,$exam_month=null){
		$conditions = array();
		$conditions['locksumbitted'] = 1;
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
		}
		if(!empty($aicenter_mapped_data) && $aicenter_mapped_data!=null){
		$reval_total_lock_submit_all_student = RevalStudent::where($conditions)->whereIn('ai_code',$aicenter_mapped_data)->count();
		}else{
		$reval_total_lock_submit_all_student = RevalStudent::where($conditions)->count();
		}
		return $reval_total_lock_submit_all_student;


    }
	
	public function getRevalallStudentpaymentCount($aicenter_mapped_data=null,$exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
		}
		if(!empty($aicenter_mapped_data) && $aicenter_mapped_data!=null){
			$supplementarya_total_registred_all_students_payment = RevalStudent::where($conditions)->whereIn('ai_code',$aicenter_mapped_data)->whereNotNull('challan_tid')->whereNotNull('submitted')->count();
		}else{
		$supplementarya_total_registred_all_students_payment = RevalStudent::where($conditions)->whereNotNull('reval_students.challan_tid')->whereNotNull('reval_students.submitted')->where('reval_students.locksumbitted',1)->count();
		}
		return $supplementarya_total_registred_all_students_payment;
    }
	
	 public function getRevalallStudentpaymentnotpayCount($aicenter_mapped_data=null,$exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
		}
		if(!empty($aicenter_mapped_data) && $aicenter_mapped_data!=null){
			$reval_total_registred_students_payment_notpay = RevalStudent::where($conditions)->whereIn('ai_code',$aicenter_mapped_data)->whereNull('challan_tid')
		->where('locksumbitted',1)->count();
		}else{
		$reval_total_registred_students_payment_notpay = RevalStudent::where($conditions)->whereNull('challan_tid')
		->where('locksumbitted',1)->count();
		}
			return $reval_total_registred_students_payment_notpay;
    }
	
	   public function getEligibleRevalaryallStudentCount($aicenter_mapped_data=null,$exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['exam_year'] = $selected_session;
		$conditions['is_eligible'] = 1;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
		}
		if(!empty($aicenter_mapped_data) && $aicenter_mapped_data!=null){
			$reval_eligible_total_registred_all_students = RevalStudent::where($conditions)->whereIn('ai_code',$aicenter_mapped_data)->count();
		}else{
			$reval_eligible_total_registred_all_students = RevalStudent::where($conditions)->count();
		}
		return $reval_eligible_total_registred_all_students;
	}
	
	public function getEligibleSupplementaryallStudentCount($auth_user_id=null,$exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['exam_year'] = $selected_session;
		$conditions['is_eligible'] = 1;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
		}
		if(!empty($aicenter_mapped_data) && $aicenter_mapped_data!=null){
			$supplementary_eligible_total_registred_all_students = Supplementary::where($conditions)->whereIn('ai_code',$aicenter_mapped_data)->count();
		}else{
			$supplementary_eligible_total_registred_all_students = Supplementary::where($conditions)->count();
		}
		return $supplementary_eligible_total_registred_all_students;
	}

	public function getExaminerMappings($auth_user_id=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$current_exam_month_id = Config::get('global.current_exam_month_id');
		$practicalexaminer = Config::get('global.practicalexaminer');
		$conditions['exam_year'] = $selected_session;
		$conditions['exam_month'] = $current_exam_month_id;
		if(!empty($auth_user_id) && $auth_user_id!=null){
			$conditions['user_id']=$auth_user_id;
		}
		$totalExainersCount = 0;
		return $totalExainersCount;
	}
	
	public function getExamCentersCount($auth_user_id=null){
		$conditions = array();
		$totalCount = 0;
		$selected_session = CustomHelper::_get_selected_sessions();
		$current_exam_month_id = Config::get('global.current_exam_month_id');
		$practicalexaminer = Config::get('global.practicalexaminer');
		$conditions['exam_year'] = $selected_session;
		$conditions['exam_month'] = $current_exam_month_id;
		$totalCount = ExamcenterDetail::count();
		return $totalCount;
	}
	
	public function getExaminers($user_deo_id=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$current_exam_month_id = Config::get('global.current_exam_month_id');
		$practicalexaminer = Config::get('global.practicalexaminer');
		$conditions['users.exam_year'] = $selected_session;
		$conditions['users.exam_month'] = $current_exam_month_id;
		if(!empty($user_deo_id) && $user_deo_id!=null){
			$conditions['user_examiner_maps.user_deo_id']=$user_deo_id;
		}


		$totalExainersCount = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
					->Join('user_examiner_maps', 'user_examiner_maps.user_deo_id', '=', 'users.id')
					->where($conditions)
					->count();
		return $totalExainersCount;
	}
	
	public function getExaminerMaps($user_deo_id=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$current_exam_month_id = Config::get('global.current_exam_month_id');
		$practicalexaminer = Config::get('global.practicalexaminer');
		$conditions['users.exam_year'] = $selected_session;
		$conditions['users.exam_month'] = $current_exam_month_id;
		$conditions['user_examiner_maps.deleted_at'] = null;
		if(!empty($user_deo_id) && $user_deo_id!=null){
			$conditions['user_examiner_maps.user_deo_id']=$user_deo_id;
		}

		$totalExainersCount = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
					->Join('user_examiner_maps', 'user_examiner_maps.user_deo_id', '=', 'users.id')
					->where($conditions)
					->count();
		return $totalExainersCount;
	}
	
	public function getPracticalExaminerMaps($user_deo_id=null){
		$conditions = array();
		$current_admission_session_id = Config::get("global.current_admission_session_id");
		$current_exam_month_id = Config::get("global.current_exam_month_id");
		$current_stream_id = Config::get("defaultStreamId");
		$auth_user_id = @Auth::user()->id;
		$custom_component_obj = new CustomComponent();
		$isAdminStatus = $custom_component_obj->_checkIsAdminRole();
		if($isAdminStatus!=true){
			$conditions['user_examiner_maps.user_deo_id'] = @$auth_user_id;
		}

		$master = UserExaminerMap::join('users', 'users.id', '=', 'user_examiner_maps.user_practical_examiner_id')
			//->Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
			->select(['users.ssoid','user_examiner_maps.*'])
			->where($conditions)
			->where('user_examiner_maps.exam_year',$current_admission_session_id)
			->where('user_examiner_maps.exam_month',$current_exam_month_id)
			->count();
		return $master;
	}
	
	public function _checkIsAdminRole($name=null)
    {
	    $super_admin = config("global.super_admin");
		$admin = config("global.admin");
		$developer_admin = config("global.developer_admin");
		$examination_department = config("global.examination_department");
		$Printer = config("global.Printer");
		$secrecy_admin = config("global.secrecy_admin");
		$evaluation_admin =config("global.evaluation_admin");
		$marksheet_verification =config("global.marksheet_verification");
		$Printer = config("global.Printer");
		// $publication_department =config("global.publication_department");

		$role_id = Session::get('role_id');
		$status = false;
		if(!empty($role_id) && ($role_id == $admin || $role_id == $developer_admin || $role_id == $examination_department || $role_id == $Printer || $role_id == $secrecy_admin || $role_id == $evaluation_admin || $role_id == $Printer || $role_id == $marksheet_verification || $role_id == $super_admin )){
			$status = true;
		}
		return $status;
	}
	
	public function getsupplementaryallStudentLockSubmitCount($aicenter_mapped_data=null,$exam_month=null){
		$conditions = array();
		$conditions['locksumbitted'] = 1;
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['supplementaries.exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
		}
		if(!empty($aicenter_mapped_data) && $aicenter_mapped_data!=null){
		$supplementary_total_lock_submit_all_student = Supplementary::where($conditions)->whereIn('ai_code',$aicenter_mapped_data)->count();
		}else{
		$supplementary_total_lock_submit_all_student = Supplementary::where($conditions)->count();
		}
		return $supplementary_total_lock_submit_all_student;


    }
	
	public function getsupplementaryallStudentpaymentCount($aicenter_mapped_data=null,$exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['supplementaries.exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['supplementaries.exam_month'] = $exam_month;
		}
		if(!empty($aicenter_mapped_data) && $aicenter_mapped_data!=null){
			$supplementarya_total_registred_all_students_payment = Supplementary::where($conditions)->whereIn('ai_code',$aicenter_mapped_data)->whereNotNull('challan_tid')->whereNotNull('submitted')->where('supplementaries.locksumbitted',1)->count();
		}else{
		$supplementarya_total_registred_all_students_payment = Supplementary::where($conditions)->whereNotNull('supplementaries.challan_tid')->whereNotNull('supplementaries.submitted')->where('supplementaries.locksumbitted',1)->count();
		}
		return $supplementarya_total_registred_all_students_payment;
    }

	public function getsupplementaryallStudentpaymentnotpayCount($aicenter_mapped_data=null,$exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['supplementaries.exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
		}
		if(!empty($aicenter_mapped_data) && $aicenter_mapped_data!=null){
			$supplementary_total_registred_students_payment_notpay = Supplementary::where($conditions)->whereIn('ai_code',$aicenter_mapped_data)->whereNull('challan_tid')
		->where('locksumbitted',1)->count();
		}else{
		$supplementary_total_registred_students_payment_notpay = Supplementary::where($conditions)->whereNull('challan_tid')
		->where('locksumbitted',1)->count();
		}
			return $supplementary_total_registred_students_payment_notpay;
    }
	
	/*public function getApplicationData($formId=null,$isPaginate=true){ 

		$conditions = Session::get($formId. '_conditions');
		
		$master = array();
		
		$amount = 0;
		if(@$conditions['student_fees.is_valid_for_fee_pay'] && $conditions['student_fees.is_valid_for_fee_pay'] == 1){
			unset($conditions['student_fees.is_valid_for_fee_pay']);
			if($isPaginate){
				$defaultPageLimit = config("global.defaultPageLimit");
				$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->Join('student_fees', 'student_fees.student_id', '=', 'students.id')->where("student_fees.total",">",$amount)->where($conditions)->orderBy('students.id','desc')
				->paginate($defaultPageLimit,array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','applications.fee_paid_amount'));
			}else{
				$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->Join('student_fees', 'student_fees.student_id', '=', 'students.id')->where("student_fees.total",">",$amount)->where($conditions)->orderBy('students.id','desc')
				->get(array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','applications.fee_paid_amount'));
			} 
		}else{
			unset($conditions['student_fees.is_valid_for_fee_pay']);
			if($isPaginate){
				$defaultPageLimit = config("global.defaultPageLimit");
				$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->Join('student_fees', 'student_fees.student_id', '=', 'students.id')->where("student_fees.total","=",$amount)->where($conditions)->orderBy('students.id','desc')
				->paginate($defaultPageLimit,array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','applications.fee_paid_amount'));

			}else{
				$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->Join('student_fees', 'student_fees.student_id', '=', 'students.id')->where("student_fees.total","=",$amount)->where($conditions)->orderBy('students.id','desc')
				->get(array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','applications.fee_paid_amount'));
			}
		}
		
		
		
		return $master; 
	}*/

	public function getApplicationData($formId=null,$isPaginate=true){
//dd($isPaginate);
		$conditions = Session::get($formId. '_conditions');

		$rawExtraCond2 = $rawConditions =  $rawExtraCond = $rawQueryDateTime = $rawExtraCondMergedAiCodes = $rawExtraCondAdd = 1;
		if(!empty($conditions['students.isssoid'])){
			if($conditions['students.isssoid'] >= 0){
				if($conditions['students.isssoid'] == 1){
					$rawExtraCond = ' rs_students.ssoid is not null ';
				}else {
					$rawExtraCond = ' rs_students.ssoid is null ';
				}
				unset($conditions['students.isssoid']);
			}
		}

		if(!empty($conditions['students.name'])){
			if($conditions['students.name'] != null){
				$rawExtraCondAdd = ' rs_students.name like "%' . $conditions['students.name'] .'%"';
			}
			unset($conditions['students.name']);
		}

		if(!empty($conditions['students.aicenter_mapped_data'])){
			if($conditions['students.aicenter_mapped_data'] != null){
				$rawExtraCond2 = ' rs_students.ai_code in (' . implode(",",@$conditions['students.aicenter_mapped_data']) .')';
			}
			unset($conditions['students.aicenter_mapped_data']);
		}


		$startenddataarr=null;
		$symbol = Session::get($formId. '_symbol');
        $symbols = Session::get($formId. '_symbols');
        $symbolis = Session::get($formId. 'symbolis');
	    $orderByRaw = Session::get($formId. '_orderByRaw');
		// $rawConditions = Session::get($formId. '_rawConditions');
		$rawConditions = 1;

		$arraykeys=array_keys($conditions);

		/* Start End Date */
		$rawQueryDateTime = 1;
		$table_name = "students";$field_name = "created_at";
		if(@$conditions[$table_name.'.start_date'] || @$conditions[$table_name. '.end_date'] ){
			$rawQueryDateTime = CustomHelper::getStartAndEndDate(@$conditions[$table_name.'.start_date'],@$conditions[$table_name.'.end_date'],$table_name,$field_name);
			unset($conditions[$table_name.".start_date"]);
			unset($conditions[$table_name.".end_date"]);
		}
		/* Start End Date */

		if(in_array('students.enrollmentgen',$arraykeys)){
				unset($conditions["students.enrollmentgen"]);
				if(@$symbol){
					$tempCond = array('students.enrollment',$symbol,null);
				}
		}

        if(in_array('students.is_self_filled',$arraykeys)){
				unset($conditions["students.is_self_filled"]);
				if(@$symbols){
					$tempConds = array('students.is_self_filled',$symbols,null);
				}
		}


        if(in_array('students.is_otp_verified',$arraykeys)){
				unset($conditions["students.is_otp_verified"]);
				if(@$symbolis){
					$tempCondsis = array('students.is_otp_verified',$symbolis,null);
				}
		}


		$master = array();
		$fields = array('students.id','students.ssoid','students.is_self_filled','students.is_otp_verified','students.exam_year','students.exam_month','students.created_at','students.name','students.is_eligible','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','applications.fee_paid_amount','is_verified','students.ao_status','students.username','students.original_password','mobile');
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			if(!empty($orderByRaw)){
				if(!empty($rawConditions)){
					$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')
					->where($conditions)
					->where(@$tempCond[0],@$tempCond[1],@$tempCond[2])
                    ->where(@$tempConds[0],@$tempConds[1],@$tempConds[2])
                    ->where(@$tempCondsis[0],@$tempCondsis[1],@$tempCondsis[2])
					->whereRaw(@$rawQueryDateTime)
					->whereRaw(@$rawExtraCond2)
					->whereRaw(@$rawExtraCond)
					->whereRaw(@$rawExtraCondMergedAiCodes)
					->orderByRaw($orderByRaw)
					->paginate($defaultPageLimit,$fields);
				}else{

					$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')
					->where($conditions)
					->whereRaw(@$rawQueryDateTime)
					->where(@$tempCond[0],@$tempCond[1],@$tempCond[2])
                    ->where(@$tempConds[0],@$tempConds[1],@$tempConds[2])
					->orderByRaw($orderByRaw)
					->whereRaw(@$rawExtraCond)
					->whereRaw(@$rawExtraCond2)
					->whereRaw(@$rawExtraCondMergedAiCodes)
					->paginate($defaultPageLimit,$fields);
				}
			}else{
				$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')
				->where($conditions)
				->whereRaw(@$rawQueryDateTime)
				->where(@$tempCond[0],@$tempCond[1],@$tempCond[2])
				->where(@$tempConds[0],@$tempConds[1],@$tempConds[2])
				->where(@$tempCondsis[0],@$tempCondsis[1],@$tempCondsis[2])
				->whereRaw($rawConditions)
				->whereRaw(@$rawExtraCond)
				->whereRaw(@$rawExtraCond2)
				->whereRaw(@$rawExtraCondMergedAiCodes)
				->whereRaw(@$rawExtraCondAdd)
				->paginate($defaultPageLimit,$fields);


			}
		}
		else{
			$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->where($conditions)
			->whereRaw(@$rawQueryDateTime)
			->whereRaw(@$rawExtraCond)
			->whereRaw(@$rawExtraCond2)
			->whereRaw(@$rawExtraCondMergedAiCodes)
			->where(@$tempCond[0],@$tempCond[1],@$tempCond[2])
            ->where(@$tempConds[0],@$tempConds[1],@$tempConds[2])
            ->where(@$tempCondsis[0],@$tempCondsis[1],@$tempCondsis[2])
			->get($fields);
		}

		return $master;

	}
	
	public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
	
	public function getExamcenterDataByUserId($exam_center_user_id){
		$conditions = array();
		$conditions['examcenter_details.user_id'] = $exam_center_user_id;
        $master = ExamcenterDetail::where($conditions)->orderBy('ai_code')->first();
		return $master;
	}
	
	public function getVerifyStudentData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$master = array();
		$extraQ = true;
		$role_id = @Session::get('role_id');
		$aicenter_id_role = config("global.aicenter_id");
		$verifier_id = Config::get('global.verifier_id');
		$academicofficer_id = config('global.academicofficer_id');
		$verifier_admin_id = config('global.verifier_admin_id');
		$super_admin_id = config('global.super_admin_id');
		$rawExtraCond0 = 1;
		$rawExtraCond = 1;
		$rawExtraCond1 = 1;
		$rawExtraCond2 = 1;
		$rawExtraCond3 = 1;
		$rawExtraCond4 = 1;
		$rawExtraCond5 = 1;
		$rawExtraCond6 = 1;
		$rawExtraCond7 = 1;
		if(isset($conditions['stage'] )&& !empty($conditions['stage'])){
			$rawExtraCond6 = "rs_students.stage in('4','5') ";
				unset($conditions['stage']);
		}

		$conditions['applications.is_ready_for_verifying'] = 1;
		$conditions['applications.locksumbitted'] = 1;

		//common start
			/*if(isset($conditions['student_verifications.is_permanent_rejected_by_dept']) &&
				@$conditions['student_verifications.is_permanent_rejected_by_dept'] &&
				$conditions['student_verifications.is_permanent_rejected_by_dept'] == 1){
				$rawExtraCond0 = "student_verifications.is_permanent_rejected_by_dept = 1 ";
				unset($conditions['student_verifications.is_permanent_rejected_by_dept']);
			} */
			if(isset($conditions['students.exam_month']) && $conditions['students.exam_month'] == 'total'){
				unset($conditions['students.exam_month']);
			}
		//common end

		if($role_id == $verifier_id){//verifer
			if(isset($conditions['students.aicenter_mapped_data']) && @$conditions['students.aicenter_mapped_data']){
				$rawExtraCond = ' rs_students.ai_code in ( ' .  implode(",",$conditions['students.aicenter_mapped_data'] ) . " ) ";
				unset($conditions['students.aicenter_mapped_data']);
			}  else{
				$master = Student::whereNull('id')
					->paginate(1);
				return $master;
			}
			if( isset($conditions['students.aicenter_mapped_data']) && $conditions['students.aicenter_mapped_data'] == false){
				unset($conditions['students.aicenter_mapped_data']);
			}
		}else if($role_id == $academicofficer_id){ //ao
			$auth_user_id = @Auth::user()->id;
			$aoAicodes = $this->getAOMappedAiCodes($auth_user_id);
			if(@$aoAicodes){
				$aoAicodes = implode(",",$aoAicodes);
				$rawExtraCond4 = ' rs_students.ai_code in ( ' .  $aoAicodes .' )';
			}


			if(isset($conditions['students.aicenter_mapped_data'])){
				unset($conditions['students.aicenter_mapped_data']);
			}
			if(@$conditions['extra'] == $verifier_id){

			}else{
				//$rawExtraCond3 = ' rs_students.verifier_status not in ( 1 )';
			}


		}else if($role_id == $verifier_admin_id){ //Verifier Admin
			if(isset($conditions['students.aicenter_mapped_data'])){
				unset($conditions['students.aicenter_mapped_data']);
			}

			if(isset($conditions['students.verifier_aicode_user_id'])){

				$temp_user_id = $conditions['students.verifier_aicode_user_id'];
				$aiCentersMapped = $this->getVerifierMappedAiCodes($temp_user_id);

				 if(@$aiCentersMapped){
					$rawExtraCond5 = ' rs_students.ai_code in ( ' . implode(',',$aiCentersMapped) .' )';
				 }

				unset($conditions['students.verifier_aicode_user_id']);
			}




			if(@$conditions['extra'] == $verifier_id){

			}else{
				//$rawExtraCond3 = ' rs_students.verifier_status not in ( 1 )';
			}
		}else if($role_id == $super_admin_id){ //dept
			// dd($conditions);
			if(isset($conditions['students.aicenter_mapped_data'])){
				unset($conditions['students.aicenter_mapped_data']);
			}
			if(@$conditions['extra'] == $verifier_id ){

			}else if	(@$conditions['extra'] == $academicofficer_id){
				//$rawExtraCond3 = ' rs_students.verifier_status not in ( 1 )';
			}else{
				//$rawExtraCond4 = ' rs_students.stage in ( 4,5 )';
			}

		}

		if(isset($conditions["students.verifier_status"]) && $conditions["students.verifier_status"] == '!1' ){
			$rawExtraCond7 ="rs_students.verifier_status not in('1')";
			unset($conditions["students.verifier_status"]);

			}



		$fields = array('students.id','students.name','students.mobile','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.is_verifier_verify','students.is_department_verify','students.verifier_verify_user_id','students.department_verify_user_id','students.verifier_status','students.department_status','students.ao_status',
		'students.verifier_verify_datetime','students.ssoid','students.department_verify_datetime','students.book_learning_type_id','applications.is_ready_for_verifying','applications.locksubmitted_date');


		if(isset($conditions['extra'])){
			unset($conditions['extra']);
		}
		//$conditions['students.exam_month'] = 1;
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");

			if($extraQ){
					
					$master = Student::LeftJoin('student_verifications', function($join) {
                        $join->on('student_verifications.student_id', '=', 'students.id')
                             ->whereNull('student_verifications.deleted_at');
                    })
					->Join('applications', 'applications.student_id', '=', 'students.id')
					->where($conditions)
					->whereRaw(@$rawExtraCond7)
					->whereRaw(@$rawExtraCond6)
					->whereRaw(@$rawExtraCond)
					->whereRaw(@$rawExtraCond2)
					->whereRaw(@$rawExtraCond3)
					->whereRaw(@$rawExtraCond4)
					->whereRaw(@$rawExtraCond5) 
					->groupBy('students.id')
					->orderBy('student_verifications.id', 'DESC')
					->paginate($defaultPageLimit,$fields);
					// echo "<pre>";
					// print_r($rawExtraCond);echo "<br>";
					// print_r($rawExtraCond2);echo "<br>";
					// print_r($rawExtraCond3);echo "<br>";
					// print_r($rawExtraCond4);echo "<br>";
					// dd($conditions);

					// dd($master);
			}else{

				$master = Student::Join('applications', 'applications.student_id', '=', 'students.id')
					->where($conditions)
					->whereRaw(@$rawExtraCond7)
					->whereRaw(@$rawExtraCond6)
					->whereRaw(@$rawExtraCond)
					->whereRaw(@$rawExtraCond2)
					->whereRaw(@$rawExtraCond3)
					->whereRaw(@$rawExtraCond4)
					->whereRaw(@$rawExtraCond5)
					->where('applications.is_ready_for_verifying',1)
					->get($fields);
			}
		}else{

			if($extraQ){

				$master = Student::LeftJoin('student_verifications', function($join) {
                        $join->on('student_verifications.student_id', '=', 'students.id')
                             ->whereNull('student_verifications.deleted_at');
                    })
					->Join('applications', 'applications.student_id', '=', 'students.id')
					->where($conditions)
					->whereRaw(@$rawExtraCond7)
					->whereRaw(@$rawExtraCond6)
					->whereRaw(@$rawExtraCond)
					->whereRaw(@$rawExtraCond2)
					->whereRaw(@$rawExtraCond3)
					->whereRaw(@$rawExtraCond4)
					->whereRaw(@$rawExtraCond5) 
					->groupBy('students.id')
					->orderBy('student_verifications.id', 'DESC')
					->get($fields);
					// echo "<pre>";
					// print_r($rawExtraCond);echo "<br>";
					// print_r($rawExtraCond2);echo "<br>";
					// print_r($rawExtraCond3);echo "<br>";
					// print_r($rawExtraCond4);echo "<br>";
					// dd($conditions);

					// dd($master);
			}else{

				$master = Student::Join('applications', 'applications.student_id', '=', 'students.id')
					->where($conditions)
					->whereRaw($rawExtraCond6)
					->whereRaw($rawExtraCond)
					->whereRaw($rawExtraCond2)
					->whereRaw($rawExtraCond3)
					->whereRaw($rawExtraCond4)
					->whereRaw($rawExtraCond5)
					->where('applications.is_ready_for_verifying',1)
					->get($fields);
			}

		}
		return $master;
	}
	
	public function getAOMappedAiCodes($auth_user_id =null){
		$master = DB::table('ao_aicodes')->where('user_id',$auth_user_id)->whereNull('deleted_at')->first('aicodes');

		if(@$master->aicodes){
			$master = explode(",",$master->aicodes);
		}else{
			return false;
		}
		// $master = str_replace('"','',$master);
		$master = str_replace("'",'',$master);
		return $master;
	}
		
	public function getVerifierMappedAiCodes($auth_user_id =null){

		$master = DB::table('verifier_aicodes')->where('user_id',$auth_user_id)->whereNull('deleted_at')->first('aicodes');

		// dd($auth_user_id);

		if(@$master->aicodes){
			$master = explode(",",$master->aicodes);
		}else{
			return false;
		}
		// $master = str_replace('"','',$master);
		$master = str_replace("'",'',$master);
		return $master;
	}
	
	public function getlocksumbittedStudentData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');

		$fields = array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.is_aicenter_verify','students.is_department_verify','students.aicenter_verify_user_id','students.department_verify_user_id',
		'students.aicenter_verify_datetime','students.department_verify_datetime','students.book_learning_type_id','applications.is_ready_for_verifying');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->where('applications.locksumbitted',1)
			->paginate($defaultPageLimit,$fields);

		}else{
			$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->where('applications.locksumbitted',1)
			->get($fields);
		}
		return $master;
	}
	
	public function allgetlocksumbittedStudentData($formId=null,$isPaginate=true){

		$conditions = Session::get($formId. '_conditions');

		$rawExtraCondMergedAiCodes = 1;
		if(!empty($conditions['students.aicenter_mapped_data'])){
			$rawExtraCondMergedAiCodes = ' rs_students.ai_code in (' . implode(',',$conditions['students.aicenter_mapped_data']). ')';
			unset($conditions['students.aicenter_mapped_data']);
			unset($conditions['ai_code']);
		}



		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereRaw(@$rawExtraCondMergedAiCodes)->where('applications.locksumbitted',1)
			->paginate($defaultPageLimit,array('students.id','students.exam_month','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted'));
		}else{
			$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereRaw(@$rawExtraCondMergedAiCodes)->where('applications.locksumbitted',1)
			->get(array('students.id','students.name','students.gender_id','students.exam_month','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted'));
		}
		return $master;
	}
	
	public function getDeleteStudentdata($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		// where('data','LIKE','%'.@$conditions['data'].'%')-
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::onlyTrashed()->join('applications','applications.student_id','=','students.id')
			->where($conditions)->paginate($defaultPageLimit,array('students.id','students.name','students.challan_tid','students.submitted','students.is_eligible','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.challan_tid','students.submitted','students.deleted_at'));
		}else{
			$master = Student::onlyTrashed()
			->join('applications','applications.student_id','=','student.id')->where($conditions)->get(array('students.id','students.name','students.challan_tid','students.submitted','students.is_eligible','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.challan_tid','students.submitted','students.deleted_at'));
		}
		return $master;
	}

	public function getdeleteUsersData($formId=null,$isPaginate=false){
		$conditions = Session::get($formId. '_conditions');
		//dd($conditions);
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
				$master = User::onlyTrashed()->where($conditions)->paginate($defaultPageLimit);
			}else{
				$master = User::onlyTrashed()->where($conditions)->get();
			}
			return $master;
		}

	public function getStudentPaymentData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereNotNull('students.challan_tid')->whereNotNull('students.submitted')->where('applications.locksumbitted',1)->paginate($defaultPageLimit,array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','applications.fee_paid_amount'));
		}else{
			$master = Student::Join('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereNotNull('students.challan_tid')->whereNotNull('students.submitted')->where('applications.locksumbitted',1)->get(array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','applications.fee_paid_amount'));
		}
		return $master;
	}
	
	public function getallStudentPaymentData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$rawExtraCondMergedAiCodes = 1;
		if(!empty($conditions['students.aicenter_mapped_data'])){
		$rawExtraCondMergedAiCodes = ' rs_students.ai_code in (' . implode(',',$conditions['students.aicenter_mapped_data']). ')';
		unset($conditions['students.aicenter_mapped_data']);
		}
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::JOIN ('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereRaw(@$rawExtraCondMergedAiCodes)->whereNotNull('students.challan_tid')->whereNotNull('students.submitted')->where('applications.locksumbitted',1)->paginate($defaultPageLimit,array('students.id','students.exam_month','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','applications.fee_paid_amount'));
		}else{
			$master = Student::JOIN ('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereNotNull('students.challan_tid')->whereNotNull('students.submitted')->whereRaw(@$rawExtraCondMergedAiCodes)->where('applications.locksumbitted',1)->get(array('students.id','students.exam_month','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','applications.fee_paid_amount'));
		}
		return $master;
	}

	public function getStudentNotPayPaymentData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$master = array();

		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereNull('students.challan_tid')->whereNull('students.submitted')->where('applications.locksumbitted',1)->paginate($defaultPageLimit,array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted'));

		}else{
			$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereNull('students.challan_tid')->whereNull('students.submitted')->where('applications.locksumbitted',1)->get(array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted'));
		}
		return $master;
	}

	public function allgetStudentNotPayPaymentData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$rawExtraCondMergedAiCodes = 1;
		if(!empty($conditions['students.aicenter_mapped_data'])){
		$rawExtraCondMergedAiCodes = ' rs_students.ai_code in (' . implode(',',$conditions['students.aicenter_mapped_data']). ')';
		unset($conditions['students.aicenter_mapped_data']);
		}
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->leftJoin('student_fees', 'student_fees.student_id', '=', 'students.id')->where($conditions)->whereNull('student_fees.deleted_at')->whereNull('students.challan_tid')->whereNull('students.submitted')->where('applications.locksumbitted',1)->whereRaw(@$rawExtraCondMergedAiCodes)->where('student_fees.total', ">",0)->paginate($defaultPageLimit,array('students.id','students.exam_month','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','students.mobile'));
		}else{
			$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->leftJoin('student_fees', 'student_fees.student_id', '=', 'students.id')->where($conditions)->whereNull('student_fees.deleted_at')->whereNull('students.challan_tid')->whereNull('students.submitted')->where('applications.locksumbitted',1)->whereRaw(@$rawExtraCondMergedAiCodes)->where('student_fees.total', ">",0)->get(array('students.id','students.exam_month','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','students.mobile'));
		}
		return $master;
	}
	
	public function allgetStudentzerofeesPaymentData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$rawExtraCondMergedAiCodes = 1;
		if(!empty($conditions['students.aicenter_mapped_data'])){
		$rawExtraCondMergedAiCodes = ' rs_students.ai_code in (' . implode(',',$conditions['students.aicenter_mapped_data']). ')';
		unset($conditions['students.aicenter_mapped_data']);
		}
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::Join('applications', 'applications.student_id', '=', 'students.id')->Join('student_fees', 'student_fees.student_id', '=', 'students.id')->where($conditions)->whereNull('students.challan_tid')->whereNull('students.submitted')->where('applications.locksumbitted',1)->whereRaw(@$rawExtraCondMergedAiCodes)->where('student_fees.total',0)->whereNull('student_fees.deleted_at')->paginate($defaultPageLimit,array('students.id','students.exam_month','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted'));

		}else{
			$master = Student::Join('applications', 'applications.student_id', '=', 'students.id')->Join('student_fees', 'student_fees.student_id', '=', 'students.id')->where($conditions)->whereNull('students.challan_tid')->whereNull('students.submitted')->where('applications.locksumbitted',1)->whereRaw(@$rawExtraCondMergedAiCodes)->whereNull('student_fees.deleted_at')->where('student_fees.total',0)->get(array('students.id','students.exam_month','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted'));
		}
		return $master;
	}

	public function getSuppPaymentIssuesData($formId=null,$isPaginate=true){
	    $exam_month = config("global.supp_current_admission_exam_month");
	    $exam_year = CustomHelper::_get_selected_sessions();
		$conditions = Session::get($formId. '_conditions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = SuppPaymentIssue::leftJoin('students', 'supp_payment_issues.student_id', '=', 'students.id')
			->leftJoin('supp_student_fees', 'supp_student_fees.student_id', '=', 'students.id')
			->Join('supplementaries', 'supplementaries.student_id', '=', 'students.id')
			->where($conditions)
			->whereNotNull('supp_payment_issues.student_id')
			->whereNotNull('supp_payment_issues.enrollment')
			->where('supp_payment_issues.is_archived', '=', 0)
			->where('supplementaries.exam_year',$exam_year)
			->where('supplementaries.exam_month',$exam_month )
			->paginate($defaultPageLimit,array('supp_payment_issues.is_archived','supp_student_fees.total','students.id','students.name',
			'students.gender_id','students.enrollment','students.adm_type'
			,'students.stream','students.course',
			'students.ai_code'
				//,'applications.medium','applications.locksumbitted'));
			));

		}else{
			$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->where($conditions)
			->leftJoin('supp_student_fees', 'supp_student_fees.student_id', '=', 'students.id')
			//->Join('supplementaries', 'supplementaries.student_id', '=', 'students.id')
			->whereNotNull('supp_payment_issues.student_id')
			->whereNotNull('supp_payment_issues.enrollment')
			->where('supp_payment_issues.is_archived', '=', 0)
			->get(array('supp_payment_issues.is_archived','supp_student_fees.total','students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code'));
			//,'applications.medium','applications.locksumbitted'));


		}
		return $master;
	}


	// public function getStudentCountAiCenterWise($formId=null,$isPaginate=true){ 
	// 	$defaultPageLimit = config("global.defaultPageLimit");
	// 	$conditions = Session::get($formId. '_conditions');
	// 	//dd($conditions);
	// 	$aiCenterRoleId = $this->_getRoleIdByName("Aicenter");
	// 	$aiCenters = $this->getAiCentersWithId();
	// 	$master = array();
	// 	if($isPaginate){
	// 	 $master = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
	// 				->where('model_has_roles.role_id',59)->Join('students', 'students.user_id', '=', 'users.id')
	// 				->where($conditions)->paginate($defaultPageLimit);
						
	// 		}else{
	// 		$master = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
	// 				->where('model_has_roles.role_id',59)->with('student')->whereRelation('student',$conditions)->withCount(['studentAllByAicode',
	// 					'studentLockSubmitByAicode',
	// 					'studentNonLockSubmitByAicode'])->get();
	// 	}

	// 	return $master; 
	// }

	public function getPaymentIssuesData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		// dd($conditions);
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = PaymentIssue::leftJoin('students', 'payment_issues.student_id', '=', 'students.id')
			->leftJoin('student_fees', 'student_fees.student_id', '=', 'students.id')
			->where($conditions)
			->where('payment_issues.student_id', "!=",null)
			->where('payment_issues.enrollment', "!=",null)
			->paginate($defaultPageLimit,array('payment_issues.is_archived','student_fees.total','students.challan_tid','students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code'
				//,'applications.medium','applications.locksumbitted'));
			));
		}else{
			$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->where($conditions)
			->leftJoin('student_fees', 'student_fees.student_id', '=', 'students.id')
			->where('payment_issues.student_id', "!=",null)
			->where('payment_issues.enrollment', "!=",null)
			->get(array('payment_issues.is_archived','student_fees.total','students.id','students.name','students.challan_tid','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code'));
			//,'applications.medium','applications.locksumbitted'));
		}
		return $master;
	}

	public function getLogsData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		// @dd($conditions);
	    $master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Logs::where('data','LIKE','%'.@$conditions['data'].'%')->orWhere('table_name',@$conditions['table_name'])->paginate($defaultPageLimit);
		}else{
			$master = Logs::where('data','LIKE','%'.@$conditions['data'].'%')->orWhere('table_name',@$conditions['table_name'])->get();
		  }


		return $master;
	}

	// public function getStudentCountSubjectWisezero($formId=null,$isPaginate=true){ 
	// 	$defaultPageLimit = config("global.defaultPageLimit");
	// 	$conditions = Session::get($formId. '_conditions');
	// 	if($isPaginate){
	// 	$master = DB::table('exam_subjects')
 //                 ->select('exam_subjects.subject_id',DB::raw('count(rs_exam_subjects.subject_id) as total'))->Join('students', 'students.id', '=', 'exam_subjects.student_id')->Join('student_fees', 'student_fees.student_id', '=', 'students.id')->Join('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->where('student_fees.total',0)->where('applications.locksumbitted',1)->whereNull('student_fees.deleted_at')
 //                 ->groupBy('exam_subjects.subject_id')->paginate($defaultPageLimit);
 //     }else{
	// 		$master = DB::table('exam_subjects')
 //                 ->select('exam_subjects.subject_id', DB::raw('count(rs_exam_subjects.subject_id) as total'))->Join('students', 'students.id', '=', 'exam_subjects.student_id')->Join('student_fees', 'student_fees.student_id', '=', 'students.id')->Join('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->where('student_fees.total',0)->where('applications.locksumbitted',1)->whereNull('student_fees.deleted_at')
 //                 ->groupBy('exam_subjects.subject_id')->get();
	// 	}
 //           	return $master; 
	// }

	public function getStudentCheckListData($formId=null,$isPaginate=false){
	   $conditions = Session::get($formId. '_conditions');
		$conditions_applications = Session::get($formId. '_conditions');

		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')
			->where($conditions)->whereRelation('application',$conditions_applications)
			->paginate($defaultPageLimit,array('students.id','students.name','students.gender_id','students.dob','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted'));
		}else{
			$master = Student::
				with('application','document','address','toc','studentfees','admission_subject','toc_subject','exam_subject')
				->where($conditions)
				->whereNotNull("students.submitted")
				->limit(50)
				->get(
					array(
						'students.id','students.submitted','students.name','students.father_name','students.mother_name','students.gender_id','students.dob',
						'students.enrollment','students.adm_type','students.stream','students.course','students.ai_code'
					)
				);
		}
		return $master;
	}

	public function getTocCheckListData($formId=null,$isPaginate=false){

		$conditions = Session::get($formId. '_conditions');
		$conditions_applications = Session::get($formId. '_conditions_applications');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')
			->where($conditions)->whereRelation('application','locksumbitted',1)->whereNotNull('students.submitted')->whereNotNull('students.challan_tid')
			->paginate($defaultPageLimit,array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted'));
		}else{
			$master = Student::where($conditions)
				->with('application','toc','toc_subject')->whereNotNull('students.submitted')->whereNotNull('students.challan_tid')
				->whereRelation('application',$conditions_applications)
			->get();
		}
		return $master;
	}

	public function getStudentCountAiCenterWise($formId=null,$isPaginate=true){
		$defaultPageLimit = config("global.defaultPageLimit");
		$conditions = Session::get($formId. '_conditions');
		//dd($conditions);
		$aiCenterRoleId = $this->_getRoleIdByName("Aicenter");
		$aiCenters = $this->getAiCentersWithId();
		$master = array();
		if($isPaginate){
		 $master = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
					->where('model_has_roles.role_id',59)->with('student')->whereRelation('student',$conditions)->withCount(['studentAllByAicode',
						'studentLockSubmitByAicode',
						'studentNonLockSubmitByAicode'])->paginate($defaultPageLimit);

			}else{
			$master = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
					->where('model_has_roles.role_id',59)->with('student')->whereRelation('student',$conditions)->withCount(['studentAllByAicode',
						'studentLockSubmitByAicode',
						'studentNonLockSubmitByAicode'])->get();
		}

		return $master;
	}
	
	public function _getRoleIdByName($name=null)
    {
	    $conditions = null;
		$result = array();
		if(!empty($name)){
			$conditions = ['name' => $name];
		}
		$mainTable = "roles";
		$cacheName = "roles_".$name;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) { //
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($conditions, $mainTable) {
				return $result = DB::table($mainTable)->where($conditions)->get()->pluck('id');
			});
		}
		return $result;
	}
	
	public function getAiCentersWithId($status=null){
		$aiCenterRoleId = $this->_getRoleIdByName("Aicenter");
		$conditions = array(
			'users.active' => 1,
			'model_has_roles.role_id' => $aiCenterRoleId
		);

		$master = DB::table('model_has_roles')->join('users', 'model_has_roles.model_id', '=', 'users.id')
			->where($conditions)
			->pluck('college_name','users.id');

		return $master;
	}

	//public function getAiCenters($currentUserAiCode=null,$limit=null){ 
        
		//$user_role = Session::get('role_id');
		//$aiCenterRoleId = $this->_getRoleIdByName("Aicenter");
		//if(@$currentUserAiCode){
			//$conditions = array(
				//'users.active' => 1,
				//'users.ai_code' => $currentUserAiCode,
				//'model_has_roles.role_id' => $aiCenterRoleId
			//);  
		//} //else {
			//$conditions = array(
				//'users.active' => 1,
				//'model_has_roles.role_id' => $aiCenterRoleId
			//);  	
		//} 
		
		//$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		//add new bind rs_aicenter_details get aicode from here.
		//$master = ModelHasRole::join('users', 'model_has_roles.model_id', '=', 'users.id')
			//->select(
            	//DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,'')) AS college_name"),'ai_code')
			 	//->where($conditions)
			 	//->whereNull('users.deleted_at')
				//->orderByRaw($queryOrder)
				//->pluck('college_name', 'ai_code');

		//return $master; 
	//}
	
	public function getStudentCountTOCSubjectWise($formId=null){
		$defaultPageLimit = config("global.defaultPageLimit");
		$conditions = Session::get($formId. '_conditions');
		$aiCenterRoleId = $this->_getRoleIdByName("Aicenter");

		$master = TocMark::Join('students', 'students.id', '=', 'toc_marks.student_id')
				->Join('applications', 'applications.student_id', '=', 'toc_marks.student_id')
				->leftJoin('users', 'users.id', '=', 'students.user_id')
			->where($conditions)
			->select('users.college_name','toc_marks.subject_id','users.ai_code')
			->withCount(['tocSubejctStudent'])
			->paginate($defaultPageLimit);

		return $master;
	}
	
	public function getStudentCountSubjectWise($formId=null,$isPaginate=true){
		$defaultPageLimit = config("global.defaultPageLimit");
		$conditions = Session::get($formId. '_conditions');

		if($isPaginate){
		$master = DB::table('student_allotments')
                 ->select('exam_subjects.subject_id', DB::raw('count(rs_exam_subjects.subject_id) as total'))->Join('exam_subjects', 'exam_subjects.student_id', '=', 'student_allotments.student_id')->Join('students', 'students.id', '=', 'student_allotments.student_id')->where($conditions)->where('student_allotments.supplementary',0)->whereNull('exam_subjects.deleted_at')->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->where(function ($query){
            $query->orWhereNull('exam_subjects.final_result')->orWhere(['exam_subjects.final_result' => '!= PASS','exam_subjects.final_result' => '!= P','exam_subjects.final_result' => '!= p']);})->whereNull('exam_subjects.deleted_at')->where('student_allotments.supplementary',0)
                 ->groupBy('exam_subjects.subject_id')->paginate($defaultPageLimit);

     }else{
			$master = DB::table('student_allotments')
                 ->select('exam_subjects.subject_id', DB::raw('count(rs_exam_subjects.subject_id) as total'))->Join('exam_subjects', 'exam_subjects.student_id', '=', 'student_allotments.student_id')->Join('students', 'students.id', '=', 'student_allotments.student_id')->where($conditions)->where('student_allotments.supplementary',0)->whereNull('exam_subjects.deleted_at')->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->where(function ($query){
            $query->orWhereNull('exam_subjects.final_result')->orWhere(['exam_subjects.final_result' => '!= PASS','exam_subjects.final_result' => '!= P','exam_subjects.final_result' => '!= p']);})->whereNull('exam_subjects.deleted_at')->where('student_allotments.supplementary',0)
                 ->groupBy('exam_subjects.subject_id')->get();
		}





           	return $master;
	}

	public function getsupplementaryStudentCountSubjectWise($formId=null,$isPaginate=true){
		$defaultPageLimit = config("global.defaultPageLimit");
		$conditions = Session::get($formId. '_conditions');
		if($isPaginate){
		$master = DB::table('supplementary_subjects')
                 ->select('supplementary_subjects.subject_id', DB::raw('count(rs_supplementary_subjects.subject_id) as total'))->
                 Join('student_allotments', 'student_allotments.student_id', '=', 'supplementary_subjects.student_id')->
                 Join('supplementaries', 'supplementaries.student_id', '=', 'student_allotments.student_id')->
                 where($conditions)->whereNotNull('supplementaries.challan_tid')->whereNotNull('supplementaries.submitted')->whereNull('supplementaries.deleted_at')->whereNull('supplementary_subjects.deleted_at')->whereNull('student_allotments.deleted_at')->where('student_allotments.supplementary',1)
                 ->groupBy('supplementary_subjects.subject_id')->paginate($defaultPageLimit);
     }else{
			$master = DB::table('supplementary_subjects')
			->select('supplementary_subjects.subject_id', DB::raw('count(rs_supplementary_subjects.subject_id) as total'))->
			Join('student_allotments', 'student_allotments.student_id', '=', 'supplementary_subjects.student_id')->
			Join('supplementaries', 'supplementaries.student_id', '=', 'student_allotments.student_id')->
			where($conditions)->whereNotNull('supplementaries.challan_tid')->whereNotNull('supplementaries.submitted')->whereNull('supplementaries.deleted_at')->whereNull('supplementary_subjects.deleted_at')->whereNull('student_allotments.deleted_at')->where('student_allotments.supplementary',1)
			->groupBy('supplementary_subjects.subject_id')->get();
		}
           	return $master;
	}
	
	public function getWithPaginationAiCenters($formId=null,$isPaginate=true){
		$defaultPageLimit = config("global.defaultPageLimit");
		$aiCenterRoleId = $this->_getRoleIdByName("Aicenter");
		$conditions = Session::get($formId. '_conditions');
		$conditions['is_allow_for_admission'] = 1;
		$master = array();
		if($isPaginate){
			//$conditionsNew = array(
			//	'users.active' => 1,
			//	'model_has_roles.role_id' => $aiCenterRoleId
			//);

			$master = AicenterDetail::where($conditions)->paginate($defaultPageLimit, array('aicenter_details.college_name','aicenter_details.ai_code','aicenter_details.district_id'));
			//$master = User::join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
			//	->join('students', 'students.user_id', '=', 'users.id')
			//	->join('aicenter_details', 'aicenter_details.ai_code', '=', 'users.ai_code')
			//	->where($conditions)
			//	->where($conditionsNew)->groupBy('users.id')
				//->paginate($defaultPageLimit, array('aicenter_details.college_name','aicenter_details.ai_code'));

		}else{
			//$conditionsNew = array(
			//	'users.active' => 1,
			//	'model_has_roles.role_id' => $aiCenterRoleId
			//);
			$master = AicenterDetail::where($conditions)->get(['aicenter_details.college_name','aicenter_details.ai_code','aicenter_details.district_id']);
			//$master = User::join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
				//->join('students', 'students.user_id', '=', 'users.id')
			  //  ->join('aicenter_details', 'aicenter_details.ai_code', '=', 'users.ai_code')
				//->where($conditions)
				//->where($conditionsNew)
				//->groupBy('users.id')
				//->get(['aicenter_details.college_name','aicenter_details.ai_code']);
		}

		return $master;
	}
	
	public function getAiCentersByDistrictId($district_id=null,$req_type=null){
		$current_exam_month_id = Config::get('global.current_exam_month_id');
		$selected_session = CustomHelper::_get_selected_sessions();
		$studentallotmentaicode = DB::table('student_allotments')->where('exam_year',$selected_session)->where('exam_month',$current_exam_month_id)->whereNull('deleted_at')->groupBy('ai_code')->pluck('ai_code');
		$aiCenterRoleId = $this->_getRoleIdByName("Aicenter");
		if(@$district_id){
			$conditions = array(
				'aicenter_details.active' => 1,
				'aicenter_details.district_id' => $district_id,

			);
		}else{
			$conditions = array(
				'aicenter_details.active' => 1,

			);
		}


		if(@$req_type){
			$conditions[$req_type] = 1;
		}

		$master = DB::table('aicenter_details')
			->select(
            DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,'')) AS college_name"),'ai_code')
			->where($conditions)
			->whereIn('ai_code',$studentallotmentaicode)
            ->pluck('college_name', 'ai_code');

		$finalMaster = $master;
		return $finalMaster;
	}

	public function getAiCodeByExamCenterDetailId($auth_user_id=null){
		$conditions = array();
		$current_exam_month_id = Config::get('global.current_exam_month_id');
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['exam_year'] = $selected_session;
		$conditions['exam_month'] = $current_exam_month_id;

		if(!empty($auth_user_id) && $auth_user_id != null){
			$conditions['examcenter_detail_id']=$auth_user_id;
		}
		$counter = CenterAllotment::where($conditions)
									->count();


		return $counter;
	}

	public function getAllUsesSSOIds($currentUserAiCode=null){
		$queryOrder = " ssoid ASC";
		// $master = User::where("users.active", 1)
		// 	 	->whereNull('users.deleted_at')
		// 		->orderByRaw($queryOrder)
		// 		->pluck('ssoid', 'id');

		$current_exam_month_id = Config::get('global.current_exam_month_id');
		$selected_session = CustomHelper::_get_selected_sessions();
	$master = User::where("users.active", 1)
			 	->whereNull('users.deleted_at')
				->orderByRaw($queryOrder)
				->pluck('ssoid', 'id');

				/* $master = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
					//->where('model_has_roles.role_id','!=', 60)
					->where("users.active", 1)
			 	->whereNull('users.deleted_at')
				->orderByRaw($queryOrder)
				->pluck('ssoid', 'id');

				*/

		return $master;
	}

	public function getAiCentersWithInActive(){
		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		$master = AicenterDetail::select(
			DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,'')) AS college_name"),'ai_code')
			 ->whereNull('deleted_at')
			->orderByRaw($queryOrder)
			->pluck('college_name', 'ai_code');
		return $master;
	}
	
	public function getAiCentersWithdistrictname(){
		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		$master = AicenterDetail::join('districts', 'districts.id', '=', 'aicenter_details.district_id')->whereNull('districts.deleted_at')
			->orderByRaw($queryOrder)
			->pluck('districts.name', 'aicenter_details.ai_code');
		return $master;
	}
	
	public function getAiCenters($currentUserAiCode=null,$limit=null,$is_allow_for_admission=null){

	   if(@$currentUserAiCode && $currentUserAiCode == 'custom'){

		$currentUserAiCode = array("01075","02116","04051","05073","06035","07019","07063","10047","11047","12011","12079","12110","12113","12207","12220","12262","12270","12288","12289","13010","14004","16001","16051","17022","17108","18074","19055","19062","20028","20053","20056","20060","21062","22001","23019","23023","23030","24001","24006","24022","24040","25012","26029","26037","26039","26079","28001","28076","28079","30001","30009","30022","30028","32041","33004","01001","02137","03017","04001","04022","04032","04045","04049","04053","04055","04057","04061","05099","06006","06061","06062","06078","07001","07033","07044","08008","08017","08019","09001","09057","09060","10001","10027","10034","11044","11045","12003","12027","12114","12153","12218","12219","12222","12224","12234","12273","12281","12285","12290","12296","13001","13003","14022","14028","14031","15001","16015","17002","17004","17008","17009","17011","17114","19002","19024","20005","20031","20058","20059","21061","23002","23028","24061","24062","25001","26001","26063","26066","26077","26086","27005","27016","28082","28089","29023","30004","30010","30011","30023","30029","31050","32035","33001");
		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		//add new bind rs_aicenter_details get aicode from here.
		$master = AicenterDetail::select(
            	DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,'')) AS college_name"),'ai_code')
				->where('active',1)
				->where('is_allow_for_admission',1)
				->whereIn('ai_code',$currentUserAiCode)
			 	->whereNull('deleted_at')
				->orderByRaw($queryOrder)
				->pluck('college_name', 'ai_code');
		} else if(@$currentUserAiCode){
		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		//add new bind rs_aicenter_details get aicode from here.
		$master = AicenterDetail::select(
            	DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,'')) AS college_name"),'ai_code')
				->where('active',1)
				->where('is_allow_for_admission',1)
				->where('ai_code',$currentUserAiCode)
			 	->whereNull('deleted_at')
				->orderByRaw($queryOrder)
				->pluck('college_name', 'ai_code');
		} else{
		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		//add new bind rs_aicenter_details get aicode from here.
		$master = AicenterDetail::select(
            	DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,'')) AS college_name"),'ai_code')
				->where('active',1)
				->where('is_allow_for_admission',1)
			 	->whereNull('deleted_at')
				->orderByRaw($queryOrder)
				->pluck('college_name', 'ai_code');
		}
		return $master;
	}

	public function getAiCentersformaterial($limit=null){

		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		//add new bind rs_aicenter_details get aicode from here.
		$current_exam_month_id = Config::get('global.current_exam_month_id');
		$selected_session = CustomHelper::_get_selected_sessions();
		$studentallotmentaicode = DB::table('student_allotments')->where('exam_year',$selected_session)->where('exam_month',$current_exam_month_id)->whereNull('deleted_at')->groupBy('ai_code')->pluck('ai_code');
		$master = AicenterDetail::select(
            	DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,'')) AS college_name"),'ai_code')
				->where('active',1)
			 	->whereNull('deleted_at')
				->whereIn('ai_code',$studentallotmentaicode)
				->orderByRaw($queryOrder)
				->pluck('college_name', 'ai_code');

		return $master;
	}

	public function getSchoolData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = School::where($conditions)->join('districts', 'districts.id', '=', 'schools.District')
			->leftJoin('examcenter_details', 'examcenter_details.school_id', '=', 'schools.id')->where($conditions)
			->paginate($defaultPageLimit, array('examcenter_details.cent_name','schools.id','School','schools.District','schools.PrincipalName',
			'schools.MobileNo','schools.PrincipalOrHeadmasterEmail','schools.School_Type','schools.School_Category'));
		}else{
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = School::where($conditions)->join('districts', 'districts.id', '=', 'schools.District')
			->leftJoin('examcenter_details', 'examcenter_details.school_id', '=', 'schools.id')->get();

		}

		return $master;
	}
	
	public function getTimeTableData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = UserExaminerMap::where($conditions)
			->paginate($defaultPageLimit);
		}else{
			$master = UserExaminerMap::where($conditions)->get();
		}
		return $master;
	}
	
	public function getTimeTablesdatas($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$orderByRaw = Session::get($formId. '_orderByRaw');
		$exam_year = config("global.current_materials_exam_year");
		$stream = config("global.current_materials_exam_month");
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");

			if(!empty($orderByRaw)){
				$master = TimeTable::where($conditions)->where('exam_year',$exam_year)->where('stream',$stream)->orderByRaw($orderByRaw)
				->paginate($defaultPageLimit);
			}else{
				$master = TimeTable::where($conditions)->where('exam_year',$exam_year)->where('stream',$stream)
			->paginate($defaultPageLimit);
			}


		}else{
			$master = TimeTable::where($conditions)->where('exam_year',$exam_year)->where('stream',$stream)->get();
		}
		return $master;
	}

	public function getAllExamcenterData($formId=null,$isPaginate=true){
		$conditions = Session::get('_conditions');
		$examcenter_deleted_at = Session::get('examcenter_deleted_at');
		$fields = array('examcenter_details.id','examcenter_details.ecenter10','examcenter_details.ecenter12','examcenter_details.district_id','examcenter_details.ai_code','examcenter_details.capacity','examcenter_details.exam_incharge','examcenter_details.mobile','examcenter_details.created_at','examcenter_details.center_supdt','examcenter_details.cent_name','examcenter_details.deleted_at','user_id');
		if($isPaginate){
			$defaultPageLimit = 1000;
		  if(isset($examcenter_deleted_at) && !empty($examcenter_deleted_at)){
				if($examcenter_deleted_at == 'not_null'){
					$master = ExamcenterDetail::withTrashed()->whereNotNull('examcenter_details.deleted_at')->with('userdata')->where($conditions)
					->orderBy('examcenter_details.deleted_at')->orderBy('examcenter_details.ecenter10')->paginate($defaultPageLimit, $fields);
				}else if($examcenter_deleted_at == 'null'){
					$master = ExamcenterDetail::withTrashed()->whereNull('examcenter_details.deleted_at')->with('userdata')->where($conditions)
					->orderBy('examcenter_details.deleted_at')->orderBy('examcenter_details.ecenter10')->paginate($defaultPageLimit, $fields);
				}else {
					$master = ExamcenterDetail::withTrashed()->with('userdata')->where($conditions)
				->orderBy('examcenter_details.deleted_at')->orderBy('examcenter_details.ecenter10')->paginate($defaultPageLimit, $fields);
				}

			}else{
				$master = ExamcenterDetail::withTrashed()->with('userdata')->where($conditions)
				->orderBy('examcenter_details.deleted_at')->orderBy('examcenter_details.ecenter10')->paginate($defaultPageLimit, $fields);
			}

		}else{
			$master = ExamcenterDetail::withTrashed()->with('userdata')->where($conditions)
			->orderBy('examcenter_details.deleted_at')->orderBy('examcenter_details.ecenter10')->get($fields);
		}

		return $master;

	}
	
	public function getExamcenterAllotmentDataByExamcenterDetailId($formId=null,$isPaginate=true){
		$conditions = Session::get('_condtions');
		$exam_year = Config::get('global.current_admission_session_id');
		$stream = config("global.CenterAllotmentStreamId");
		$exam_month = $stream;

		$conditions['center_allotments.exam_year'] = Config::get('global.current_admission_session_id');
		$conditions['center_allotments.exam_month'] = $exam_month;

		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = ExamcenterAllotment::join('examcenter_details', 'examcenter_details.id', '=', 'center_allotments.examcenter_detail_id')
			->where($conditions)->paginate($defaultPageLimit,
				array(
					'examcenter_details.stream','examcenter_details.ecenter10','examcenter_details.ecenter12','examcenter_details.cent_name','examcenter_details.capacity',
					'center_allotments.id','center_allotments.examcenter_detail_id','center_allotments.ai_code',
					'center_allotments.course','center_allotments.student_strem1_10',
					'student_strem1_12','center_allotments.student_strem2_10','center_allotments.student_strem2_12',
					'center_allotments.student_supp_10','center_allotments.student_supp_12','total_of_10',
					'center_allotments.total_of_12','center_allotments.supp_total','center_allotments.stream1_total',
					'center_allotments.stream2_total','center_allotments.total','center_allotments.stream',
					'center_allotments.exam_year','center_allotments.exam_month'
				)
			);
 		}else{
			$master = ExamcenterAllotment::where($conditions)->get();
		}
		// @dd($master);
		return $master;
	}
	
	public function getAllAiCenters($formId=null){
		$conditions = Session::get($formId. '_conditions');
		$selectFields = array(DB::raw('count(*) as count'),"users.id","users.ai_code","users.college_name","students.id","applications.locksumbitted");
		$groupBy = array("users.ai_code");
	 	$master = User::leftJoin('students', 'students.user_id', '=', 'users.id')
			->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
			->leftJoin('applications', 'applications.student_id', '=', 'students.id')
			->select($selectFields)
			->where($conditions)
			->groupBy($groupBy)
			->get();
		return $master;
	}
	
	public function getRolesByUserId($user_id=null){
		$result = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_id',$user_id)->orderBy('sort', 'ASC')
            ->get(['role_id','model_id','name']);
		return $result;
	}

	public function getStudentFees($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_condtions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = StudentFee::Join('students', 'students.id', '=', 'student_fees.student_id')->Join('applications', 'applications.student_id', '=', 'student_fees.student_id')
			->where($conditions)->paginate($defaultPageLimit);
		}else{
			$master = StudentFee::Join('students', 'students.id', '=', 'student_fees.student_id')->Join('applications', 'applications.student_id', '=', 'student_fees.student_id')
			->where($conditions)->get();
		}
		return $master;
	}
	
	public function getSessionalData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_condtions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::Join('applications', 'applications.student_id', '=', 'students.id')
			->Join('exam_subjects', 'exam_subjects.student_id', '=', 'students.id')
			->Join('users', 'users.id', '=', 'students.user_id')
			->where($conditions)
			->paginate($defaultPageLimit,
			array('students.id','students.name','students.gender_id',
			'students.enrollment','students.adm_type','students.stream',
			'students.course','applications.medium','applications.locksumbitted',
			'exam_subjects.subject_id','exam_subjects.sessional_marks'));

		}else{
			$master = Student::Join('applications', 'applications.student_id', '=', 'students.id')
			->Join('exam_subjects', 'exam_subjects.student_id', '=', 'students.id')
			->Join('users', 'users.id', '=', 'students.user_id')
			->where($conditions)
			->get(array('students.id','students.name',
			'students.gender_id','students.enrollment','students.adm_type','students.stream',
			'students.course','applications.medium','applications.locksumbitted',
			'exam_subjects.subject_id','exam_subjects.sessional_marks'));
		}

		return $master;
	}

	public function getSessionalcount(){
		$examyear = config("global.current_sessional_exam_year");
		$user_id = Auth::user()->id;
	    $master = Student::Join('applications', 'applications.student_id', '=', 'students.id')
			->Join('exam_subjects', 'exam_subjects.student_id', '=', 'students.id')
			->Join('users', 'users.id', '=', 'students.user_id')
			->where('students.exam_year',$examyear)->where('students.user_id',$user_id)->count();

		return $master;
	}

	public function getPrepareSessionalcount(){
		$examyear = config("global.current_sessional_exam_year");
		$user_id = Auth::user()->id;
	    $master = Student::Join('applications', 'applications.student_id', '=', 'students.id')
			->Join('prepare_exam_subjects', 'prepare_exam_subjects.student_id', '=', 'students.id')
			->Join('users', 'users.id', '=', 'students.user_id')
			->where('students.exam_year',$examyear)->where('students.user_id',$user_id)->count();

		return $master;
	}

	public function getSessionalDataH($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_condtions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::LeftJoin('applications', 'applications.student_id', '=', 'students.id')
			->LeftJoin('sessional_exam_subjects', 'sessional_exam_subjects.student_id', '=', 'students.id')
			->LeftJoin('users', 'users.id', '=', 'students.user_id')
			->where($conditions)
			->whereNull('students.deleted_at')
			->whereNull('sessional_exam_subjects.deleted_at')
			->where('students.is_eligible',1)
			->groupBy('students.id')
			->orderBy('sessional_exam_subjects.is_sessional_mark_entered', 'ASC')
			->orderBy('students.id')
			->paginate($defaultPageLimit,
				array( 'students.id','students.enrollment','students.name',
				'students.ai_code',DB::raw('GROUP_CONCAT(rs_sessional_exam_subjects.subject_id) as subjects'),
				DB::raw('GROUP_CONCAT(rs_sessional_exam_subjects.sessional_marks) as subject_marks'),'students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream',
				'students.course','applications.medium','applications.locksumbitted',
				'sessional_exam_subjects.subject_id','sessional_exam_subjects.sessional_marks','sessional_exam_subjects.is_sessional_mark_entered',)
			);
		}else{
			$master = Student::LeftJoin('applications', 'applications.student_id', '=', 'students.id')
			->LeftJoin('sessional_exam_subjects', 'sessional_exam_subjects.student_id', '=', 'students.id')
			->LeftJoin('users', 'users.id', '=', 'students.user_id')
			->where($conditions)
			->whereNull('students.deleted_at')
			->whereNull('sessional_exam_subjects.deleted_at')
			->where('students.is_eligible',1)
			->groupBy('students.id')
			->orderBy('sessional_exam_subjects.is_sessional_mark_entered', 'ASC')
			->orderBy('students.id')
			// ->limit(50)
			->get();
		}

		return $master;
	}
	
	public function getPrepareSessionalDataH($formId=null,$isPaginate=true){

		$conditions = Session::get($formId. '_condtions');

		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::LeftJoin('applications', 'applications.student_id', '=', 'students.id')
			->LeftJoin('sessional_exam_subjects', 'sessional_exam_subjects.student_id', '=', 'students.id')
			->LeftJoin('users', 'users.id', '=', 'students.user_id')
			->where($conditions)
			->whereNull('students.deleted_at')
			->whereNull('sessional_exam_subjects.deleted_at')
			->where('students.is_eligible',1)
			->groupBy('students.id')
			->orderBy('sessional_exam_subjects.is_sessional_mark_entered', 'ASC')
			->orderBy('students.id')
			->paginate($defaultPageLimit,
				array( 'students.id','students.enrollment','students.name',
				'students.ai_code',DB::raw('GROUP_CONCAT(rs_sessional_exam_subjects.subject_id) as subjects'),
				DB::raw('GROUP_CONCAT(rs_sessional_exam_subjects.sessional_marks) as subject_marks'),'students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream',
				'students.course','applications.medium','applications.locksumbitted',
				'sessional_exam_subjects.subject_id','sessional_exam_subjects.sessional_marks','sessional_exam_subjects.is_sessional_mark_entered',)
			);

		}else{
			$master = Student::LeftJoin('applications', 'applications.student_id', '=', 'students.id')
			->LeftJoin('sessional_exam_subjects', 'sessional_exam_subjects.student_id', '=', 'students.id')
			->LeftJoin('users', 'users.id', '=', 'students.user_id')
			->where($conditions)
			->whereNull('students.deleted_at')
			->whereNull('sessional_exam_subjects.deleted_at')
			->where('students.is_eligible', 1)
			->groupBy('students.id')
			->orderBy('sessional_exam_subjects.is_sessional_mark_entered', 'ASC')
			->orderBy('students.id')
			->limit(50)
			->get()
			;
		}

		return $master;
	}

	//public function examcentercode($examyear=null,$stream=null,$centercode=null){
		//$condtions = null;
		//$result = array();
		//if($centercode == null ){
			//$centercode = "ecenter10";
		//}
		//if($examyear!=null && $stream!=null && $centercode != null){  
			//$condtions = ['exam_year' => $examyear]; 
			//$condtions = ['exam_month' => $stream]; 
		//}else{
			//$current_exam_month_id = Config::get('global.current_exam_month_id');
			//$selected_session = CustomHelper::_get_selected_sessions();
			//$conditions['exam_year'] = $selected_session;
			//$conditions['exam_month'] = $current_exam_month_id;
		//}
		
		//$mainTable = "examcenter_details";
		//$cacheName = "examcenter_details_code";
		//Cache::forget($cacheName);
		//if (Cache::has($cacheName)) { 
			//$result = Cache::get($cacheName);
		//}else{ 
			//$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable, $centercode) { 
				//return $result = DB::table($mainTable)->where($condtions)->pluck($centercode,'id')->toArray();
			//});			
		//}  
		//return $result;
	//}
	
	public function getSessionalDataForExcel($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_condtions');
		$master = Student::Join('sessional_exam_subjects', 'sessional_exam_subjects.student_id', '=', 'students.id')
				->where($conditions)
				->whereNull('students.deleted_at')
				->whereNull('sessional_exam_subjects.deleted_at')
				->where('students.is_eligible', 1)
				// ->limit(100)
				->get(['students.id','students.name','students.enrollment','students.ai_code','sessional_exam_subjects.is_sessional_mark_entered','sessional_exam_subjects.sessional_marks','sessional_exam_subjects.subject_id']);

		return $master;
	}



	//public function examcentername($examyear=null,$stream=null){
		//$condtions = null;
		//$result = array();
		//if($examyear!=null && $stream!=null){  
			//$condtions = ['exam_year' => $examyear]; 
			//$condtions = ['exam_month' => $stream]; 
		//}
		
		//$mainTable = "examcenter_details";
		//$cacheName = "examcenter_details";
		//Cache::forget($cacheName);
		//if (Cache::has($cacheName)) { 
			//$result = Cache::get($cacheName);
		//}else{ 
			//$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) { 
				//return $result = DB::table($mainTable)->where($condtions)->pluck('cent_name','id')->toArray();
			//});			
		//}  
		//return $result;
	//} 
	
	public function getPrepareSessionalDataForExcel($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_condtions');
		$master = Student::Join('sessional_exam_subjects', 'sessional_exam_subjects.student_id', '=', 'students.id')
				->where($conditions)
				->whereNull('students.deleted_at')
				->whereNull('sessional_exam_subjects.deleted_at')
				->where('students.is_eligible', 1)
				->get(['students.id','students.name','students.enrollment','students.ai_code','sessional_exam_subjects.is_sessional_mark_entered','sessional_exam_subjects.sessional_marks','sessional_exam_subjects.subject_id']);

		return $master;
	}

	public function subjectListcode($course=null){
		$condtions = null;
		$result = array();
		if($course!=null){
			$condtions = ['course' => $course];
		}
		$mainTable = "subjects";
		$cacheName = "Subjects_". $course;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
				return $result = DB::table($mainTable)->where($condtions)->whereNull('deleted_at')->orderBy('subject_code','ASC')->pluck('subject_code','id');;

			});
		}
		return $result;
	}

	public function getSubjectByCoursePracticalTheoryexcel($course=null){
		$condtions = null;
		$result = array();
		if($course!=null){
			$condtions['deleted'] = 0;
		}
		if($course!=null){
			$condtions['course'] = $course;
		}

		$mainTable = "subjects";
		$cacheName = "subjects_name_code_". $course;
		if (Cache::has($cacheName)) { //Cache::forget($mainTable);
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
				$result = DB::table($mainTable)->where($condtions)->orderBy('subject_code','ASC')->pluck('name','id');
				return $result;
			});
		}
		return $result;
	}

	public function examcentercode($examyear=null,$centercode=null){
		$condtions = null;
		$result = array();
		if($centercode == null ){
			$centercode = "ecenter10";
		}
		if($examyear!=null && $centercode != null){
			$condtions = ['exam_year' => $examyear];
		}else{
			$current_exam_month_id = Config::get('global.current_exam_month_id');
			$selected_session = CustomHelper::_get_selected_sessions();
			$conditions['exam_year'] = $selected_session;
		}

		$mainTable = "examcenter_details";
		$cacheName = "examcenter_details_code";
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($mainTable, $centercode) {
				return $result = DB::table($mainTable)->whereNull('deleted_at')->where('active',1)->get(['id','cent_name',$centercode])->toArray();
			});
		}
		return $result;
	}
	


	/* need to be udpate start */

	public function examcentername($examyear=null){
		$condtions = null;
		$result = array();
		if($examyear!=null){
			$condtions = ['exam_year' => $examyear];
		}

		$mainTable = "examcenter_details";
		$cacheName = "examcenter_details";
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
				return $result = DB::table($mainTable)->where($condtions)->pluck('cent_name','id')->toArray();
			});
		}
		return $result;
	}

	public function examcentercodeobj($examyear=null,$stream=null,$centercode=null){
		$condtions = null;
		$result = array();
		if($centercode == null ){
			$centercode = "ecenter10";
		}
		if($examyear!=null && $stream!=null && $centercode != null){
			$condtions = ['exam_year' => $examyear];
			$condtions = ['exam_month' => $stream];
		}else{
			$current_exam_month_id = Config::get('global.current_exam_month_id');
			$selected_session = CustomHelper::_get_selected_sessions();
			$conditions['exam_year'] = $selected_session;
			$conditions['exam_month'] = $current_exam_month_id;
		}

		$mainTable = "examcenter_details";
		$cacheName = "examcenter_details_code_obj";
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable, $centercode) {
				return $result = DB::table($mainTable)->where($condtions)->pluck($centercode,'id');
			});
		}
		return $result;
	}

	public function examcenternameobj($examyear=null,$stream=null){
		$condtions = null;
		$result = array();
		if($examyear!=null && $stream!=null){
			$condtions = ['exam_year' => $examyear];
			$condtions = ['exam_month' => $stream];
		}

		$mainTable = "examcenter_details";
		$cacheName = "examcenter_details_obj";
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
				return $result = DB::table($mainTable)->where($condtions)->pluck('cent_name','id');
			});
		}
		return $result;
	}
	
	/* need to be update end */

	public function getExamCenterWithBothCourseCode(){
		$condtions = null;
		$result = array();
		$exam_year = Config::get('global.current_admission_session_id');
		$exam_month = Config::get('global.current_exam_month_id');
		$fianlList=null;
		// $condtions = ['exam_year' => $exam_year];
		// $condtions = ['exam_month' => $exam_month];

		$mainTable = "examcenter_details";
		$cacheName = "examcenter_details_both_code_obj_";
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
				$data = DB::table($mainTable)->where($condtions)->get(['cent_name','id','ecenter10', 'ecenter12']);

				foreach($data as $k => $v){
					$fianlList[$v->id] = $v->ecenter10 . '-' . $v->ecenter12 . " " . $v->cent_name;
				}

				return $result = @$fianlList;
			});
		}
		return $result;
	}
	
	public function _getsupplementarystudentallotmentstatusforaicode($aicode=null,$stream=null,$course=null){

		$current_admission_session_id = Config::get('global.current_admission_session_id');
		$current_exam_month_id = Config::get('global.current_exam_month_id');

		$suppconditions = array(
				"supplementaries.course" => $course,
				"supplementaries.ai_code" => $aicode,
				"supplementaries.status" => 1,
				"supplementaries.challan_tid" =>'IS NOT NULL',
				"supplementaries.submitted" =>'IS NOT NULL',
				"supplementaries.exam_year" =>$current_admission_session_id,
				"supplementaries.exam_month" =>$current_exam_month_id
			);

		$suppcount = Supplementary::where($suppconditions)->get()->count();

		$conditionArray = array(
				"center_allotments.ai_code" => $aicode,
				"center_allotments.stream" => $stream,
				"center_allotments.exam_year" =>$current_admission_session_id
			);
		$fieldarr = array();
		if($course == 10){
			$field = 'student_supp_10';
		}
		if($course == 12){
			$field = 'student_supp_12';
		}

		// $this->CenterAllotment->virtualFields = array('supp_sum' => 'sum('.$field.')');
		// $total_supp_alotted = CenterAllotment->find('first',array('fields'=>array('supp_sum'),'conditions'=>$conditionArray));
		// dd($conditionArray);
		$total_supp_alotted = CenterAllotment::where($conditionArray)->sum($field);

		if(@$total_supp_alotted == @$suppcount)
			return 1;
		else
			return 0;
	}

	public function _getstudentallotmentstatusforaicode($aicode=null,$stream=null,$course=null){
		//$conditionArray = array();
		//$this->loadModel('CenterAllotment');
		//$this->loadModel('Supplementary');
		//$this->loadModel('Student');
		//$this->loadModel('Application');

		$current_admission_session_id = Config::get('global.current_admission_session_id');
		$exam_month = Config::get('global.current_exam_month_id');
		$studentconditions = array(
				"students.stream" => $stream,
				"students.ai_code" => $aicode,
				"students.submitted" =>'IS NOT NULL',
				"students.exam_year" =>$current_admission_session_id ,
				"students.exam_month" =>$exam_month,
				"students.status" =>1,
				"students.course" => $course,
			);


		//$this->Student->bindModel(array('hasOne'=>array('Application')));
		//$studentscount = $this->Student->find('count',array('conditions'=>$studentconditions));
		$studentscount = Student::where($studentconditions)
		->join('applications', 'applications.student_id', '=', 'students.id')
		->count();

		$conditionArray = array(
				"center_allotments.ai_code" => $aicode,
				"center_allotments.stream" => $stream,
				"center_allotments.exam_year" =>$current_admission_session_id
			);

		$fieldarr = array();

			if($course == 10)
			{
				$field = 'student_strem'.$stream.'_10';

			}
			if($course == 12)
			{
				$field = 'student_strem'.$stream.'_12';
			}

			//$this->CenterAllotment->virtualFields = array('stream_sum' => 'sum('.$field.')');
			//$stream_allotted = $this->CenterAllotment->find('first',array('fields'=>array('stream_sum'),'conditions'=>$conditionArray));
			$stream_allotted = CenterAllotment::where($conditionArray)->sum($field);

			if($stream_allotted  == $studentscount)
				return 1;
			else
				return 0;

	}
			
	public function getExamcenterAllotmentDataForReport($formId=null,$isPaginate=true){
		$conditions = Session::get($formId.'_condtions');
		if(@$conditions['pagecount'] && !empty($conditions['pagecount'])){
			$pagecount = $conditions['pagecount'];
			unset($conditions['pagecount']);
		}
		else{
			$pagecount = $conditions['pagecount'];
			unset($conditions['pagecount']);
		}

         $aicentercode = CenterAllotment::where(function ($query) {
            $query->where('is_student_strem1_10',1)->orWhere(['is_student_strem2_10' => 1,'is_student_strem1_12' => 1,'is_student_strem2_12' => 1, 'is_student_supp_10' => 1, 'is_student_supp_12' => 1 ]);
        })->where($conditions)->groupBy('examcenter_detail_id')->get('examcenter_detail_id');
		$master = array();
		if($isPaginate){
			 if(!empty($pagecount)){
			 $defaultPageLimit = $pagecount;
			 }else{
			 $defaultPageLimit = config("global.defaultPageLimit");
			 }

			// $exam_year = CustomHelper::_get_selected_sessions();
			// $exam_month = Config::get('global.current_exam_month_id');

			// $stuudentAllotmentDetails = StudentAllotment::join('examcenter_details', 'examcenter_details.id' , '=', 'student_allotments.examcenter_detail_id')
			// 	->select('student_allotments.supplementary','examcenter_details.ecenter10','examcenter_details.ecenter12','examcenter_details.cent_name','examcenter_details.capacity','student_allotments.ai_code','student_allotments.course','student_allotments.examcenter_detail_id', DB::raw('count(rs_student_allotments.id) as total'))
			// 	->where('student_allotments.exam_year',$exam_year)
			// 	->where('student_allotments.exam_month',$exam_month)
			// 	->groupBy(['student_allotments.ai_code','student_allotments.course','examcenter_details.id'])
			// 	->orderBy('examcenter_details.ecenter10','ASC','examcenter_details.ecenter12','ASC','student_allotments.ai_code','ASC','student_allotments.course','ASC','student_allotments.supplementary','ASC')
			// 	->paginate($defaultPageLimit);
			// $fianlArr = array();
			// foreach(@$stuudentAllotmentDetails as $k => $value){
			// 	$fianlArr[$value->examcenter_detail_id]['ecenter10'] = $value->ecenter10;
			// 	$fianlArr[$value->examcenter_detail_id]['ecenter12'] = $value->ecenter12;
			// 	$fianlArr[$value->examcenter_detail_id]['cent_name'] = $value->cent_name;
			// 	$fianlArr[$value->examcenter_detail_id]['capacity'] = $value->capacity;
			// 	$fianlArr[$value->examcenter_detail_id]['ai_code'] = $value->ai_code;
			// 	$fianlArr[$value->examcenter_detail_id]['course'] = $value->course;
			// 	$fianlArr[$value->examcenter_detail_id]['supplementary'] = $value->supplementary;
			// 	$fianlArr[$value->examcenter_detail_id]['total'] = $value->total;
			// }

			// dd($fianlArr);
			// dd($stuudentAllotmentDetails);


	//$master = ExamcenterDetail::with("examcenterallotments")->whereIn('id',$aicentercode)->paginate($defaultPageLimit);

			$master = ExamcenterDetail::with(['examcenterallotments' => function ($query) use($conditions) {
			$query->where($conditions);
			}])->whereIn('id',$aicentercode)->paginate($defaultPageLimit);


 		}else{
			$master = ExamcenterDetail::with(['examcenterallotments' => function ($query) use($conditions) {
			$query->where($conditions);
			}])->whereIn('id',$aicentercode)->get();
		}
		// @dd($master);
		return $master;


	}
	
	public function getOldExamcenterAllotmentDataForReport($formId=null,$isPaginate=true){
		$conditions = Session::get($formId.'_condtions');

        $aicentercode = CenterAllotment::groupBy('examcenter_detail_id')->get('examcenter_detail_id');

		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			//$defaultPageLimit = 10;

			// $master = DB::table('examcenter_details')->Join('student_allotments', 'student_allotments.examcenter_detail_id', 'examcenter_details.id')
			// 	->groupby(["student_allotments.ai_code","examcenter_details.ecenter10","examcenter_details.ecenter12"])->paginate($defaultPageLimit);


			// 	dd($master);



			$master = ExamcenterDetail::with("examcenterallotments")->whereIn('id',$aicentercode)->where($conditions)->paginate($defaultPageLimit);


 		}else{
			$master = ExamcenterDetail::with("examcenterallotments")->whereIn('id',$aicentercode)->where($conditions)->get();
		}
		// @dd($master);
		return $master;
	}
	
	public function getNominalnrReport10($formId=null,$isPaginate=true){
		$conditions = Session::get($formId.'_condtions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::with('application','exam_subject')->where($conditions)->where('course',10)->paginate($defaultPageLimit);
 		}else{
			$master = Student::with('application','exam_subject')->where($conditions)->where('course',10)->get();
		}
		// @dd($master);
		return $master;
	}
	
	public function getNominalnrReport12($formId=null,$isPaginate=true){
		$conditions = Session::get($formId.'_condtions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::with('application','exam_subject')->where($conditions)->where('course',12)->paginate($defaultPageLimit);
 		}else{
			$master = Student::with('application','exam_subject')->where($conditions)->where('course',12)->get();
		}
		// @dd($master);
		return $master;
	}
	
	public function centerAllotmentValidations($request){
		$isValid = true;
		$errors = null;
		$validator = Validator::make([], []);
		$errMsg = '';
		$stream = $request->stream;
		$student_strem_10  = "student_strem".$stream."_10";
		$student_strem_12  = "student_strem".$stream."_12";
		$request_total = $request->student_supp_10+$request->student_supp_12+$request->$student_strem_10+$request->$student_strem_12;

		$e_exam_center_id = @$request->id;
		$exam_center_id = Crypt::decrypt($e_exam_center_id);
		$exam_year =  config("global.current_admission_session_id");

		$allotment_count = CenterAllotment::where('ai_code', '=', $request->ai_code)->where('examcenter_detail_id', '=', $exam_center_id)->where('exam_year', '=', $exam_year)->where('stream', '=', $request->stream)->count();
		if($allotment_count!=0){
			$fld = 'ai_code';
			$errMsg = 'You have already allotted for this AI Center - Exam Center combination, You may need to be old combination delete then make entry.';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		if(empty($request->ai_code)){
			$fld = 'ai_code';
			$errMsg = 'Please select mandatory field "AI Center".';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		if($isValid==true &&  isset($request_total) && $request_total==0){
			$fld = 'ai_code';
			$errMsg = "You don't have student for allotment";
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		$exact_data = $this->_getStudentsCountForExamcenter($request->ai_code,$request->stream);
		$remaining_data = $exact_data['remaining'];
		//@dd($request);
		// echo "<pre>"; print_r($request);
		//@dd($remaining_data);

		// if($isValid==true && empty($request->student_supp_10) || $request->student_supp_10 != $remaining_data['supp_10']){
		if($isValid==true && isset($request->student_supp_10) && isset($remaining_data['supp_10']) &&$request->student_supp_10 > $remaining_data['supp_10']){
			$fld = 'student_supp_10';
			$errMsg = 'Please enter vaild no of students of "Supplementary-10th".';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		// if($isValid==true && empty($request->student_supp_12) || $request->student_supp_12 != $remaining_data['supp_12']){
		if($isValid==true && isset($request->student_supp_12) && isset($remaining_data['supp_12']) &&$request->student_supp_12 > $remaining_data['supp_12']){
			$fld = 'student_supp_12';
			$errMsg = 'Please enter vaild no of students of "Supplementary-12th".';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}


		$student_strem_10 = 'student_strem'.$stream.'_10';
		$remaining_stream_field =  'stream'.$stream.'_10';
		// if($isValid==true && empty($request->$student_strem_10) || $request->$student_strem_10 < 0 || $request->$student_strem_10 > $remaining_data[$remaining_stream_field]){
		if($isValid==true && isset($request->$student_strem_10) && isset($remaining_data[$remaining_stream_field]) && $request->$student_strem_10 > $remaining_data[$remaining_stream_field]){
			$fld = $student_strem_10;
			$errMsg = 'Please enter vaild no of students of Stream-'.$stream.'-10th';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		$student_strem_12 = 'student_strem'.$stream.'_12';
		$remaining_stream_field =  'stream'.$stream.'_12';
		//if($isValid==true && empty($request->$student_strem_12) ||  $request->$student_strem_12 < 0 ||  $request->$student_strem_12 > $remaining_data[$remaining_stream_field]){
		if($isValid==true && isset($request->$student_strem_12) && isset($remaining_data[$remaining_stream_field]) && $request->$student_strem_12 > $remaining_data[$remaining_stream_field]){
			$fld = $student_strem_12;
			$errMsg = 'Please enter vaild no of students of Stream-'.$stream.'-12th';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		$response['isValid'] = $isValid;
		$response['errors'] = $errors;
		$response['validator'] = $validator;
		return $response;
	}
	
	public function _getStudentsCountForExamcenter($aicenter = null,$stream=null) {
		$supp10thcount = 0;
		$supp12thcount = 0;
		$stream110thcount = 0;
		$stream112thcount = 0;

		$current_admission_session_id = Config::get('global.current_admission_session_id');
		// $exam_month = Config::get('global.current_exam_month_id');
		$exam_month = $stream;
		$custom_component_obj = new CustomComponent;

		/** Supplementary student count */
		$studentcourse = 10;
		$supplementary_condition_array = array(
			"course" =>$studentcourse,
			"ai_code" => $aicenter,
			"exam_year" =>$current_admission_session_id,
			"exam_month" =>$exam_month,
			"is_eligible"=>1,
		);

		$stream_condition_array = array(
			"students.course" =>$studentcourse,
			"students.ai_code" => $aicenter,
			"students.exam_year" =>$current_admission_session_id,
			"students.is_eligible"=>1,
			"students.exam_month" =>$exam_month,
		);

		$supp10thcount = Supplementary::where($supplementary_condition_array)->count();

		/** Stream1 student count */
		$stream110thcount = Student::where($stream_condition_array)->count();



		$studentcourse = 12;
		$supplementary_condition_array = array(
			"course" =>$studentcourse,
			"ai_code" => $aicenter,
			"exam_year" =>$current_admission_session_id ,
			"exam_month" =>$exam_month,
			"is_eligible"=>1,
		);

		$stream_condition_array = array(
			"students.course" =>$studentcourse,
			"students.ai_code" => $aicenter,
			"students.exam_year" =>$current_admission_session_id,
			"students.is_eligible"=>1,
			"students.exam_month" =>$exam_month,
		);


		$supp12thcount = Supplementary::where($supplementary_condition_array)->whereNull('deleted_at')->count();
			/** Stream1 student count */

	    $stream112thcount = Student::where($stream_condition_array)->whereNull('deleted_at')->count();

	    $getcountallotmnet = StudentAllotment::where('exam_year',$current_admission_session_id)->where('ai_code',$aicenter)->where('exam_month',$exam_month)->count();


		$total = $supp10thcount + $supp12thcount + $stream110thcount + $stream112thcount;

		$arrcount['total'] = array('supp_10'=>$supp10thcount,'supp_12'=>$supp12thcount,'stream'.$stream.'_10'=>$stream110thcount,'stream'.$stream.'_12'=>$stream112thcount,'total'=>$total);

		// get assign/allot  student count
		$assigned_total_supp_10 =$custom_component_obj->_getTotalSuppAllotted($aicenter,$stream,10);
		$assigned_total_supp_12 = $custom_component_obj->_getTotalSuppAllotted($aicenter,$stream,12);
		$assigned_total_stream_10 = $custom_component_obj->_getTotalStreamAllotted($aicenter,$stream,10);
		$assigned_total_stream_12 = $custom_component_obj->_getTotalStreamAllotted($aicenter,$stream,12);


		/*
		echo "supp10thcount : ".$supp10thcount;
		echo "</br>";
		echo "stream110thcount : ". $stream110thcount;
		echo "</br>";

		echo "supp12thcount : ".$supp12thcount;
		echo "</br>";
		echo "stream112thcount : ". $stream112thcount;
		echo "</br>";

		echo "assigned_total_supp_10 : ".$assigned_total_supp_10;
		echo "</br>";
		echo "assigned_total_supp_12 : ".$assigned_total_supp_12;
		echo "</br>";

		echo "assigned_total_stream_10 : ".$assigned_total_stream_10;
		echo "</br>";
		echo "assigned_total_stream_12 : ".$assigned_total_stream_12;
		die;
		*/

		$arrcount['remaining'] = array('supp_10'=>$supp10thcount-$assigned_total_supp_10,'supp_12'=>$supp12thcount-$assigned_total_supp_12,'stream'.$stream.'_10'=>$stream110thcount-$assigned_total_stream_10,'stream'.$stream.'_12'=>$stream112thcount - $assigned_total_stream_12);

		//print_r($arrcount);
		return $arrcount;
	}
	
	// public function getSupplementarCountAiCenterWise($formId=null,$isPaginate=true){ 
	// 	$defaultPageLimit = config("global.defaultPageLimit");
	// 	$conditions = Session::get($formId. '_conditions');
	// 	$aiCenterRoleId = $this->_getRoleIdByName("Aicenter");
	// 	$aiCenters = $this->getAiCentersWithId();
	// 	$master = array();
	// 	if($isPaginate){
	// 		$master = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
	// 				->where('model_has_roles.role_id',59)->with('student')->whereRelation('student',$conditions)->withCount(['supplementarystudentAllByAicode',
	// 					'supplementarystudentLockSubmitByAicode',
	// 					'supplementarystudentNonLockSubmitByAicode'])->paginate($defaultPageLimit);
			
	// 	}else{
	// 	$master = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')->where('model_has_roles.role_id',59)->with('student')->whereRelation('student',$conditions)->withCount(['supplementarystudentAllByAicode',
	// 				'supplementarystudentLockSubmitByAicode',
	// 				'supplementarystudentNonLockSubmitByAicode'])->get();
	// 	}
	// 	return $master; 
	// }

		public function _getTotalSuppAllotted($aicode=null,$stream=null,$course=null){
			$current_admission_session_id = Config::get('global.current_admission_session_id');
			$exam_month = Config::get('global.current_exam_month_id');
			$conditionArray = array(
				"ai_code" => $aicode,
				"stream" => $stream,
				"exam_year" =>$current_admission_session_id
			);
			$fieldarr = array();

			if($course == 10)
			{
				$field = 'student_supp_10';

			}
			if($course == 12)
			{
				$field = 'student_supp_12';
			}
			//$this->CenterAllotment->virtualFields = array('supp_sum' => 'sum('.$field.')');
			//$total_supp_alotted = $this->CenterAllotment->find('first',array('fields'=>array('supp_sum'),'conditions'=>$conditionArray));
			$total_supp_alotted = CenterAllotment::where($conditionArray)->sum($field);

		   return ($total_supp_alotted > 0) ? $total_supp_alotted : 0;



	}
	
	public function _getTotalStreamAllotted($aicode=null,$stream=null,$course=null){
		$current_admission_session_id = Config::get('global.current_admission_session_id');
		$exam_month = Config::get('global.current_exam_month_id');
	    $conditionArray = array(
				"ai_code" => $aicode,
				"stream" => $stream,
				"exam_year" =>$current_admission_session_id
			);
		$fieldarr = array();

			if($course == 10)
			{
				$field = 'student_strem'.$stream.'_10';

			}
			if($course == 12)
			{
				$field = 'student_strem'.$stream.'_12';
			}
			 $stream_allotted = CenterAllotment::where($conditionArray)->sum($field);
		     //if($stream_allotted->$field == $studentscount)
			//$this->CenterAllotment->virtualFields = array('stream_sum' => 'sum('.$field.')');
			//$stream_allotted = $this->CenterAllotment->find('first',array('fields'=>array('stream_sum'),'conditions'=>$conditionArray));
			return ($stream_allotted > 0) ? $stream_allotted  : 0;


	}

	public function supplementariereports($formId=null,$isPaginate=true){
	  	$conditions = Session::get($formId. '_conditions');
	  	//dd($conditions);
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)
			->paginate($defaultPageLimit,array('supplementaries.student_id','supplementaries.ai_code',
			'supplementaries.stream','supplementaries.course','supplementaries.submitted','supplementaries.challan_tid',
			'supplementaries.locksumbitted','supplementaries.enrollment','supplementaries.total_fees','supplementaries.fee_paid_amount','students.name','students.gender_id',
				'students.adm_type'));
		}else{
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)
			->get(array('supplementaries.student_id','supplementaries.ai_code',
			'supplementaries.stream','supplementaries.course','supplementaries.submitted','supplementaries.challan_tid',
			'supplementaries.locksumbitted','supplementaries.enrollment','supplementaries.total_fees','supplementaries.fee_paid_amount','students.name','students.gender_id',
				'students.adm_type'));
		}
		return $master;
	}

	 public function supplementariefeesreports($formId=null,$isPaginate=true){
	  	$conditions = Session::get($formId. '_conditions');
		$master = array();
		if($isPaginate){
			//dd($conditions);
			$defaultPageLimit = config("global.defaultPageLimit");
	        $master = Supplementary::LeftJoin('students', 'students.id', '=', 'supplementaries.student_id')
			->LeftJoin('applications', 'applications.student_id', '=', 'supplementaries.student_id')
			->where($conditions)->whereNotNull('supplementaries.challan_tid')
			->paginate($defaultPageLimit,array('supplementaries.ai_code','supplementaries.id','supplementaries.subject_change_fees','supplementaries.exam_fees','students.enrollment','supplementaries.practical_fees','supplementaries.forward_fees','supplementaries.online_fees','supplementaries.late_fees','supplementaries.total_fees','students.name','supplementaries.application_fee_date','supplementaries.exam_month'));
 		}else{
						//dd($conditions);
			$master = Supplementary::LeftJoin('students', 'students.id', '=', 'supplementaries.student_id')
			->LeftJoin('applications', 'applications.student_id', '=', 'supplementaries.student_id')
			->where($conditions)->whereNotNull('supplementaries.challan_tid')->orderByAsc('supplementaries.application_fee_date')
			->get(array('supplementaries.ai_code','supplementaries.id','supplementaries.subject_change_fees','supplementaries.exam_fees','students.enrollment','supplementaries.practical_fees','supplementaries.forward_fees','supplementaries.online_fees','supplementaries.late_fees','supplementaries.total_fees','students.name','supplementaries.application_fee_date','supplementaries.exam_month'));
		}
		return $master;
	}

	public function getSupplementarCountAiCenterWise($formId=null,$isPaginate=true){
		$defaultPageLimit = config("global.defaultPageLimit");
		$aiCenterRoleId = $this->_getRoleIdByName("Aicenter");
		$conditions = Session::get($formId. '_conditions');
		$master = array();
		if(@$isPaginate){
			$conditionsNew = array(
				'aicenter_details.active' => 1,
				'model_has_roles.role_id' => $aiCenterRoleId
			);
			$master = AicenterDetail::join('model_has_roles', 'model_has_roles.model_id', '=', 'aicenter_details.user_id')
				->join('supplementaries', 'supplementaries.user_id', '=', 'aicenter_details.user_id')->where($conditions)
				->where($conditionsNew)->groupBy('aicenter_details.user_id')
				->paginate($defaultPageLimit, array('aicenter_details.college_name','aicenter_details.ai_code'));
		}else{
		    $aicenterwiseconditions = Session::get('aicenterwiseconditions');
			$conditionsNew = array(
				'aicenter_details.active' => 1,
				'model_has_roles.role_id' => $aiCenterRoleId
			);
			$master = AicenterDetail::join('model_has_roles', 'model_has_roles.model_id', '=', 'aicenter_details.user_id')
			->join('supplementaries', 'supplementaries.user_id', '=', 'aicenter_details.user_id')->where($aicenterwiseconditions)
			->where($conditionsNew)->groupBy('aicenter_details.user_id')->limit(1)
			->get(['aicenter_details.college_name','aicenter_details.ai_code']);
		}


		return $master;
	}

	public function _getOldSuppLateFeeAmount($stream=null,$gender_id=null){
		$master = ExamLateFeeDate::where('stream',$stream)
			->where('gender_id',$gender_id)
			->where('from_date', '<=', Carbon::now())
			->where('to_date', '>=', Carbon::now())
			->where('is_supplementary', '=', 1)
			->first();

		// dd($master);
		if(!@$master->late_fee){
			return 0;
		}
		return @$master->late_fee;
	}

	public function _getSuppLateFeeDetails($stream=null,$gender_id=null){
		$master = ExamLateFeeDate::where('stream',$stream)
			->where('gender_id',$gender_id)
			->where('is_supplementary', '=', 1)
			->get();
		return @$master;
	}

	public function _NotNeedgetSuppLateFeeAmount($stream=null,$gender_id=null,$student_id=null){
		$lateFeeExtraMarginDays = 0;

		if(!empty($student_id) && $student_id > 0){
			$studentsDetails = Supplementary::where('student_id', '=', $student_id)->first();


			if(@$studentsDetails->locksumbitted  && @$studentsDetails->locksubmitted_date){
				$studentLocked = $studentsDetails->locksubmitted_date;
				if($studentsDetails->fee_paid_amount == null){
					$masterMarginDays = ExamLateFeeDate::where('stream',$stream)
						->where('gender_id',$gender_id)
						->where('from_date', '<=', $studentLocked)
						->where('to_date', '>=', $studentLocked)
						->where('is_supplementary', '=', 1)
						->first();

					if(@$masterMarginDays->latefee_extra_days){
						$lateFeeExtraMarginDays = $masterMarginDays->latefee_extra_days;
					}
				}
			}
		}
		$currentDate = date('Y-m-d');
		$toDate = null;
		$afterExtraDaysDate = null;
		if(@$masterMarginDays->to_date){
			$toDate = date("Y-m-d", strtotime($masterMarginDays->to_date));
		}
		if($lateFeeExtraMarginDays > 0 ){
			if(@$toDate){
				$toDate = strtotime($toDate);
				$toDate = strtotime("+" . $lateFeeExtraMarginDays ." day", $toDate);
				$afterExtraDaysDate = date('Y-m-d', $toDate);
			}
		}
		if($currentDate <= $afterExtraDaysDate){
			return @$masterMarginDays->late_fee;
		}

		$master = ExamLateFeeDate::where('stream',$stream)
			->where('gender_id',$gender_id)
			->where('from_date', '<=', Carbon::now())
			->where('to_date', '>=', Carbon::now())
			->where('is_supplementary', '=', 1)
			->first();
		if(!@$master->late_fee){
			return 0;
		}
		return @$master->late_fee;
	}
	
	public function checkAnySuppEntryAllowOrNot(){
		$res = false;
		$exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
		$master = ExamLateFeeDate::where('from_date', '<=', Carbon::now())
			->where('to_date', '>=', Carbon::now())
			->where('stream', '=', $exam_month)
			->where('is_supplementary', '=', 1)
			->count();

		if($master > 0){
			$res = true;
		}

		$custom_component_obj = new CustomComponent();
		$isAdminStatus = $custom_component_obj->_checkIsAdminRole();
		if($isAdminStatus){
			$res = true;
		}
		return $res;
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
	
	public function _getSuppLateFeeAmount($stream=null,$gender_id=null,$student_id=null,$totalNumberOfSubjects=null){

		$lateFeeExtraMarginDays = 0;
		if(!empty($student_id) && $student_id > 0){
			// $studentsDetails = Student::where('students.id', '=', $student_id)
				// ->join('applications', 'applications.student_id', '=', 'students.id')
				// ->first();

			$studentsDetails = Supplementary::where('student_id', '=', $student_id)->first();

			if(@$studentsDetails->locksumbitted  && @$studentsDetails->locksubmitted_date){
				$studentLocked = $studentsDetails->locksubmitted_date;
				if($studentsDetails->fee_paid_amount == null){
					$masterMarginDays = ExamLateFeeDate::where('stream',$stream)
						->where('gender_id',$gender_id)
						->where('from_date', '<=', $studentLocked)
						->where('to_date', '>=', $studentLocked)
						->where('is_supplementary', '=', 1)
						->first();

					if(@$masterMarginDays->latefee_extra_days){
						$lateFeeExtraMarginDays = $masterMarginDays->latefee_extra_days;
					}
				}
			}
		}
		$currentDate = date('Y-m-d');
		$toDate = null;
		$afterExtraDaysDate = null;
		if(@$masterMarginDays->to_date){
			$toDate = date("Y-m-d", strtotime($masterMarginDays->to_date));
		}
		if($lateFeeExtraMarginDays > 0 ){
			if(@$toDate){
				$toDate = strtotime($toDate);
				$toDate = strtotime("+" . $lateFeeExtraMarginDays ." day", $toDate);
				$afterExtraDaysDate = date('Y-m-d', $toDate);
			}
		}
		if($currentDate <= $afterExtraDaysDate){
			$finalLateFee = 0;
			if(@$totalNumberOfSubjects > 0 && @$masterMarginDays->is_subject_wise){
				$finalLateFee = $totalNumberOfSubjects * $masterMarginDays->late_fee;
			}else{
				$finalLateFee = $masterMarginDays->late_fee;
			}
			return @$finalLateFee;
		}

		$master = ExamLateFeeDate::where('stream',$stream)
			->where('gender_id',$gender_id)
			->where('from_date', '<=', Carbon::now())
			->where('to_date', '>=', Carbon::now())
			->where('is_supplementary', '=', 1)
			->first();


		if(!@$master->late_fee){
			return 0;
		}
		if(@$totalNumberOfSubjects > 0 && @$master->is_subject_wise){
			$finalLateFee = $totalNumberOfSubjects * $master->late_fee;
		}else{
			$finalLateFee = $master->late_fee;
		}

		return @$finalLateFee;
	}
	
	public function getUsersDeoData($formId=null,$isPaginate=false){
		$master = array();
		$conditions = Session::get($formId. '_conditions');
		$conditions['model_has_roles.role_id'] = Config::get('global.deo');
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			if(@$conditions['model_has_roles.role_id']){
				$master = User::Join('model_has_roles', function($q) {
						$q->on('users.id', '=', 'model_has_roles.model_id');
					})
					->where('model_has_roles.model_type', '=', 'App\Models\User')
					->where($conditions)
					->paginate($defaultPageLimit);
			}else{
				$master = User::
					where($conditions)
					->paginate($defaultPageLimit);
			}
		}else{
			if(@$conditions['model_has_roles.role_id']){
				$master = User::Join('model_has_roles', function($q) {
						$q->on('users.id', '=', 'model_has_roles.model_id');
					})
					->where('model_has_roles.model_type', '=', 'App\Models\User')
					->where($conditions)
					->get();
			}else{
				$master = User::all();
			}
		}
		return $master;
	}
	
	public function getUsersData($formId=null,$isPaginate=false){
		$conditions = Session::get($formId. '_conditions');
		$orderByRaw = Session::get($formId. '_orderByRaw');


		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			if(@$conditions['model_has_roles.role_id']){
				if(!empty($orderByRaw)){
					$master = User::Join('model_has_roles', function($q) {
						$q->on('users.id', '=', 'model_has_roles.model_id');
					})
					->where('model_has_roles.model_type', '=', 'App\Models\User')
					->where($conditions)
					->orderByRaw($orderByRaw)
					->paginate($defaultPageLimit);
				}else{
					$master = User::Join('model_has_roles', function($q) {
						$q->on('users.id', '=', 'model_has_roles.model_id');
					})
					->where('model_has_roles.model_type', '=', 'App\Models\User')
					->where($conditions)
					->paginate($defaultPageLimit);
				}
			} else {
				if(!empty($orderByRaw)){
					$master = User::
						orderByRaw($orderByRaw)
						->where($conditions)
						->paginate($defaultPageLimit);
				}else{
					$master = User::
						where($conditions)
						->paginate($defaultPageLimit);
				}
			}
		}else{
			if(@$conditions['model_has_roles.role_id']){
				if(!empty($orderByRaw)){
					$master = User::Join('model_has_roles', function($q) {
						$q->on('users.id', '=', 'model_has_roles.model_id');
					})
					->where('model_has_roles.model_type', '=', 'App\Models\User')
					->where($conditions)
					->orderByRaw($orderByRaw)
					->get();
				}else{
					$master = User::Join('model_has_roles', function($q) {
						$q->on('users.id', '=', 'model_has_roles.model_id');
					})
					->where('model_has_roles.model_type', '=', 'App\Models\User')
					->where($conditions)
					->get();
				}



			}else{
				$master = User::all();
			}
		}
		return $master;
	}
	
	public function _getSuppFeeDetailsForDispaly($student_id){
		//$supp_stream = Config::get("global.supp_stream");
		 $supp_stream = Config::get("global.supp_current_admission_exam_month");
		$exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
		//$exam_year_selected = CustomHelper::get_selected_sessions();
		$exam_year=	 $exam_year_selected = CustomHelper::_get_selected_sessions();
		if(@$exam_year){

		}else{
			$exam_year = Config::get('global.form_supp_current_admission_session_id');
		}
		// change by lokedar
		// $exam_year = CustomHelper::_get_selected_sessions();
		// $exam_year=	Config::get("global.form_supp_current_admission_session_id");
		$result = array();
		// $conditions['students.exam_year'] = $exam_year;
		$conditions['students.id'] = $student_id;
		$studentdata = Student::where($conditions)->first(['course','exam_month','gender_id','adm_type']);


		if(!@$studentdata){
			return false;
		}

		$ExamSubjectYearArr = ExamSubject::where('student_id',$student_id)->latest('exam_year')->orderby('exam_month', 'asc')->first(['exam_year','exam_month']);


		$exam_year_latest = null;
		$exam_month_latest = null;
		if(!empty(@$ExamSubjectYearArr->exam_year)){
			$exam_year_latest = @$ExamSubjectYearArr->exam_year;
			$exam_month_latest = @$ExamSubjectYearArr->exam_month;
		}
		$master_supplementary_fees = null;
		// echo @$studentdata->exam_month;die
		if(@$studentdata->exam_month){
			if(empty($exam_year_selected))
			{
				$exam_year_selected = $exam_year;
			}

				$master_supplementary_fees =  DB::table('master_supplementary_fees')
				->where('exam_year',$exam_year_selected)
				->where('exam_month',$exam_month)
				->where('course',$studentdata->course)
				->first();

				
		}

		if(!@$master_supplementary_fees->exam_fees){
			return $result;
		}

		$exam_fees = $master_supplementary_fees->exam_fees;
		$forward_fees = $master_supplementary_fees->forward_fees;
		$online_services_fees = $master_supplementary_fees->online_services_fees;
		$subject_wise_fees = $master_supplementary_fees->subject_fees;
		$practical_subject_wise_fees = $master_supplementary_fees->practical_fees;

		/* logics on tables*/
			$suppChangeSubjectCount = 0;
			$conditionsExamSubject['exam_subjects.exam_year'] = $exam_year_latest;
			$conditionsExamSubject['exam_subjects.exam_month'] = $exam_month_latest;
			$conditionsExamSubject['exam_subjects.student_id'] = $student_id;


			$examSubjectsDetails = ExamSubject::where($conditionsExamSubject)->pluck('subject_id','subject_id')->toArray();


			$conditionssupplementaries['supplementaries.exam_year'] = $exam_year;
			$conditionssupplementaries['supplementaries.exam_month'] = $exam_month;
			$conditionssupplementaries['supplementaries.student_id'] = $student_id;

			$SupplementaryIdArr = Supplementary::where('student_id',$student_id)->where('exam_month',$exam_month)->where('exam_year','=',$exam_year)->latest('id')->first('id');

			$supp_id = null;
			if(!empty($SupplementaryIdArr->id)){
				$supp_id = $SupplementaryIdArr->id;
			}

			$suppSubjectsDetails = Supplementary::where($conditionssupplementaries)
				->join('supplementary_subjects', 'supplementary_subjects.student_id', '=', 'supplementaries.student_id')->where('supplementary_subjects.supplementary_id',$supp_id)
				->whereNull('supplementary_subjects.deleted_at')->pluck('subject_id','subject_id')->toArray();
			// dd($suppSubjectsDetails);
			foreach($suppSubjectsDetails as $k => $suppSubjectId){
				if(!in_array($suppSubjectId, $examSubjectsDetails)){
					$suppChangeSubjectCount++;
				}
			}


			if(@$studentdata->adm_type && $studentdata->adm_type == 1){
				$controller_obj = new Controller;
				$PassedSubjectArr = $controller_obj->getPassedSubject($student_id);
				$passSubjectCount = count($PassedSubjectArr);
				$finalSubjectCheckingCount = count($suppSubjectsDetails) - (count($examSubjectsDetails) - $passSubjectCount);

				if(@$examSubjectsDetails && count($examSubjectsDetails) == 4 && $finalSubjectCheckingCount >= 1){
					$suppChangeSubjectCount = $suppChangeSubjectCount - 1;
				}else if(@$examSubjectsDetails && count($examSubjectsDetails) == 3 && $finalSubjectCheckingCount >= 2){
					$suppChangeSubjectCount = $suppChangeSubjectCount - 2;
				}else if(@$examSubjectsDetails && count($examSubjectsDetails) == 2 && $finalSubjectCheckingCount >= 3){
					$suppChangeSubjectCount = $suppChangeSubjectCount - 3;
				}
			}
			// echo "<pre>";
			// print_r($suppSubjectsDetails);
			// echo "<br>";
			// print_r($suppChangeSubjectCount);
			// echo "<br>";
			// dd($examSubjectsDetails);

			//If general and subject is "<= 4" already subjects taken and want to take till 5th subject and "only subject change fee" till 5th subject not applicable.

			/* We may need to change here start */
				//loop all subjects of rs_supplementary_subjects
				//result syc,syct,sycp
				$SupplementaryIdArr = Supplementary::where('student_id',$student_id)->where('exam_month',$exam_month)->where('exam_year','=',$exam_year)->latest('id')->first('id');
				$supp_id = null;
				if(!empty(@$SupplementaryIdArr->id)){
					$supp_id = @$SupplementaryIdArr->id;
				}


				if(isset($supp_id) && !empty($supp_id)){
					$q1 = "SELECT ss.* FROM rs_supplementary_subjects ss LEFT JOIN rs_supplementaries supp ON supp.id = ss.supplementary_id ";
					$q1 .= " where ss.deleted_at is null and ss.supplementary_id = " . $supp_id;
					$suppSubjects = DB::select($q1);
					$subjectflag = false;

					foreach($suppSubjects as $subkey=>$subval)
					{
						$esql = "SELECT es.final_result FROM rs_exam_subjects es WHERE es.deleted_at is null and es.subject_id = ".$subval->subject_id." AND es.student_id=".$subval->student_id. " AND es.exam_year=".$exam_year_latest." AND es.exam_month=".$exam_month_latest;
						$examSubjects = DB::select($esql);
						//dd($examSubjects->final_result);
						if(!empty($examSubjects))
						{
							foreach($examSubjects as $eskey=>$esval)
							{
								//dd($suppSubjects[$subkey]->student_id);
								$suppSubjects[$subkey]->final_result = $esval->final_result;
							}

						}

					}

					//old--
					/*$q1 .= " LEFT JOIN rs_exam_subjects es ON es.subject_id = ss.subject_id AND es.student_id = ss.student_id where ss.supplementary_id = " . $supp_id . " and es.exam_year=".$exam_year_latest." group by subject_id ";
					//new
					$q1 .= " LEFT JOIN rs_exam_subjects es ON es.student_id = ss.student_id where ss.supplementary_id = " . $supp_id . " and es.exam_year=".$exam_year_latest." group by subject_id ";*/
				}else{
					$es_q1 = "SELECT es.* FROM rs_exam_subjects es where es.deleted_at is null and es.student_id=".$student_id." and es.exam_year=".$exam_year_latest." group by es.subject_id ";

					$suppSubjects = DB::select($es_q1);
				}


				$phSuppSujectsCount = 0;
				$thSuppSujectsCount = 0;

				foreach(@$suppSubjects as $k => $subject){
					$isPracticalSubject = $this->_getIsSubjectPracticalType($subject->subject_id);
					if(@$subject->subject_id == @$subject->origional_subject_id){
						if(@$subject->final_result == '666'){ //Failed in only Practical
							@$phSuppSujectsCount++;
						}else if(@$subject->final_result == '777'){ //Failed in only Theory
							@$thSuppSujectsCount++;
						}else{
							@$thSuppSujectsCount++;
							if($isPracticalSubject && (@$subject->final_result == '666' ||
							@$subject->final_result == '888')){
								@$phSuppSujectsCount++;
							}
						}
					}else{

						/*$thSuppSujectsCount++;
						//$phSuppSujectsCount++;

						//if(@$subject->final_result == '777'){ //Failed in only Theory
							//$thSuppSujectsCount++;
						//}
						if($isPracticalSubject){
							//if(@$subject->final_result == '666'){ //Failed in only Practical
								$phSuppSujectsCount++;
							//}
						}*/

						/* updated code 22032024 start */
							//$thSuppSujectsCount++;
							//$phSuppSujectsCount++;

							if(@$subject->final_result){

								if(@$subject->final_result == '888'){ //Failed in only Theory
								 $thSuppSujectsCount++;
								 if($isPracticalSubject){
									$phSuppSujectsCount++;
								 }
							    }


								if(@$subject->final_result == '777'){ //Failed in only Theory
								 $thSuppSujectsCount++;
							    }
								if($isPracticalSubject){
									if(@$subject->final_result == '666'){ //Failed in only Practical
										$phSuppSujectsCount++;
									}
								}
							}else{
								$thSuppSujectsCount++;
								if($isPracticalSubject){
								$phSuppSujectsCount++;
							    }

							}


						/* updated code 22032024 end */
					}
				}


                $custom_component_obj = new CustomComponent();
				$isAdminStatus = $custom_component_obj->_checkIsAdminRole();
				if(@$isAdminStatus==true){
					// echo "<pre>";
					// echo "<br>";
					// print_r($phSuppSujectsCount);
					// echo "<br>";
					// print_r($thSuppSujectsCount);
					// dd($suppSubjects);
				}

				$suppPracticalSubjectCount = $phSuppSujectsCount;
				$examsubjectfees = $totalNumberOfSubjects = $thSuppSujectsCount;

				/* $suppPracticalSubjectCount	= SupplementarySubject::join('subjects', 'subjects.id', '=', 'supplementary_subjects.subject_id')
					->where('subjects.practical_type',1)
					->where('supplementary_subjects.student_id',$student_id)
					->count();
				$examsubjectfees = $totalNumberOfSubjects = SupplementarySubject::where('supplementary_subjects.student_id',$student_id)->count();*/
			/* We may need to change here end */

			$lateFees = $this->_getSuppLateFeeAmount($supp_stream,@$studentdata->gender_id,@$student_id,@$totalNumberOfSubjects);
			// echo $lateFees; die;

			// dd($master_supplementary_fees);


			// if(isset($supp_id) && in_array($supp_id,array(288464,290480,290674,291103,291188,291194,291719,291737,295190,295197,295609,298246,298730,298734,298736,298830,298832,298836,298837))){
			// 	$lateFees = $totalNumberOfSubjects * 50;
			// }
			//echo "totalNumberOfSubjects :". $totalNumberOfSubjects;
			//echo $lateFees; die;

		/* logics on tables*/



		$suppChangeSubjectFees = $suppChangeSubjectCount * $subject_wise_fees;
		$suppPracticalSubjectFees = $suppPracticalSubjectCount * $practical_subject_wise_fees;
		$SuppExamSubjectFees = $examsubjectfees * $exam_fees;


		$finalFees = $suppChangeSubjectFees + $suppPracticalSubjectFees + $SuppExamSubjectFees + $forward_fees + $online_services_fees + $lateFees;

		$result['online_services_fees'] = @$online_services_fees;
		$result['subject_change_fees'] = @$suppChangeSubjectFees;
		$result['practical_fees'] = @$suppPracticalSubjectFees;
		$result['exam_subject_fees'] = @$SuppExamSubjectFees;
		$result['forward_fees'] = @$forward_fees;
		$result['late_fees'] = @$lateFees;
		$result['final_fees'] = @$finalFees;
		// echo "</br>Change Subject Count : " . $suppChangeSubjectCount;
		// echo "</br>Practical count : " . $suppPracticalSubjectCount;
		// echo "</br>Exam Subject Count : " . $examsubjectfees;
		// dd($result);


		$isZeroSuppFeeStudent = $this->_isZeroSuppFeeStudent($student_id);
		if($isZeroSuppFeeStudent == true){
			$result['online_services_fees'] = 0;
			$result['subject_change_fees'] = 0;
			$result['practical_fees'] = 0;
			$result['exam_subject_fees'] = 0;
			$result['forward_fees'] = 0;
			$result['late_fees'] = 0;
			$result['final_fees'] = 0;
		}

		if($student_id == 619630 ){
			echo "</br>Change Subject Count : " . $suppChangeSubjectCount;
			echo "</br>Practical count : " . $suppPracticalSubjectCount;
			echo "</br>Exam Subject Count : " . $examsubjectfees;
			//dd($result);
		}
		return $result;
	}
	
	public function _getIsSubjectPracticalType($subject_id=null){
		$condtions = null;
		$result = array();
		if($subject_id!=null){
			$condtions = ['practical_type' => 1,'id' => $subject_id,'deleted' => 0];
		}
		$mainTable = "subjects";
		$cacheName = "Subjects_Is_Practical_Update_". $subject_id;
		if (Cache::has($cacheName)) { //Cache::forget($mainTable);
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
				$result = DB::table($mainTable)->where($condtions)->get()->count();
				return $result;
			});
		}
		return $result;
	}

	public function _isZeroSuppFeeStudent($student_id=null){
		$returnStatus = false;

		$exam_year = Config::get('global.form_supp_current_admission_session_id');
		$exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');





	}
	
	public function _rohitgetSuppFeeDetailsForDispaly($student_id){
		$supp_stream = Config::get("global.supp_stream");
		$exam_year = CustomHelper::_get_selected_sessions();
		$exam_month = CustomHelper::_get_selected_sessions();

		$result = array();
		// $conditions['students.exam_year'] = $exam_year;
		$conditions['students.id'] = $student_id;


		$studentdata = Student::where($conditions)->first(['course','exam_month','gender_id']);



		if(!@$studentdata){
			return false;
		}


		if(@$studentdata->exam_month){
			$master_supplementary_fees =  DB::table('master_supplementary_fees')
				->where('exam_year',$exam_year)
				->where('exam_month',$studentdata->exam_month)
				->where('course',$studentdata->course)
				->first();
		}



		if(!@$master_supplementary_fees->exam_fees){
			return $result;
		}
		//dd($master_supplementary_fees);

		$exam_fees = $master_supplementary_fees->exam_fees;
		$forward_fees = $master_supplementary_fees->forward_fees;
		$online_services_fees = $master_supplementary_fees->online_services_fees;
		$subject_wise_fees = $master_supplementary_fees->subject_fees;
		$practical_subject_wise_fees = $master_supplementary_fees->practical_fees;

		/* logics on tables*/


			$suppChangeSubjectCount = 0;

			// $conditionsExamSubject['exam_subjects.exam_year'] = $exam_year;
			$conditionsExamSubject['exam_subjects.student_id'] = $student_id;
			$examSubjectsDetails = ExamSubject::where($conditionsExamSubject)->pluck('subject_id','subject_id')->toArray();

			$conditionssupplementaries['supplementaries.exam_year'] = $exam_year;
			$conditionssupplementaries['supplementaries.student_id'] = $student_id;

			$suppSubjectsDetails = Supplementary::where($conditionssupplementaries)
				->join('supplementary_subjects', 'supplementary_subjects.student_id', '=', 'supplementaries.student_id')
				->pluck('subject_id','subject_id')->toArray();

			$changeSujbects = array();
			foreach($suppSubjectsDetails as $k => $suppSubjectId){
				if(!in_array($suppSubjectId, $examSubjectsDetails)){
					$changeSujbects[$suppSubjectId] = $suppSubjectId;
					$suppChangeSubjectCount++;
				}
			}

			/* practical start */
				$suppChangeSuejctIds = array();
				$suppNoChangeSuejctIds = array();

				$suppPracticalSubjectCount1NF = array();
				$suppPracticalSubjectCount2NF = array();

				$suppPracticalSubjectCount1 = 0;
				$suppPracticalSubjectCount2 = 0;
				$q1 = "SELECT `rs_t1`.`subject_id` FROM `rs_supplementary_subjects` AS `rs_t1` INNER JOIN `rs_supplementary_subjects` AS `rs_t2` ON `rs_t1`.`id` = `rs_t2`.`id` AND `rs_t1`.`subject_id` = rs_t1.origional_subject_id WHERE `rs_t1`.`student_id` = " . $student_id ;
				$varSuppNoChangeSuejctIds = DB::select($q1);

				$q2 = "SELECT `rs_t1`.`subject_id` FROM `rs_supplementary_subjects` AS `rs_t1` INNER JOIN `rs_supplementary_subjects` AS `rs_t2` ON `rs_t1`.`id` = `rs_t2`.`id` AND `rs_t1`.`subject_id` != rs_t1.origional_subject_id WHERE `rs_t1`.`student_id` = " . $student_id ;
				$varSuppChangeSuejctIds = DB::select($q2);

				// echo "<pre>";
				// print_r($varSuppNoChangeSuejctIds);
				// echo "<br>";
				// print_r($varSuppChangeSuejctIds);
				// echo "<br>";
				// die;
				foreach(@$varSuppNoChangeSuejctIds as $k => $v){
					$suppNoChangeSuejctIds[$v->subject_id] = $v->subject_id;
				}
				foreach(@$varSuppChangeSuejctIds as $k => $v){
					$suppChangeSuejctIds[$v->subject_id] = $v->subject_id;
				}

				// echo "<pre>";
				// print_r($suppNoChangeSuejctIds);
				// echo "<br>";
				// print_r($suppChangeSuejctIds);
				// echo "<br>";
				// die;
				if(@$suppNoChangeSuejctIds){
					$q3 = "SELECT count(*) AS AGGREGATE  FROM `rs_supplementary_subjects` INNER JOIN `rs_subjects` ON `rs_subjects`.`id` = `rs_supplementary_subjects`.`subject_id` INNER JOIN `rs_exam_subjects` ON `rs_exam_subjects`.`student_id` = `rs_supplementary_subjects`.`student_id` AND `rs_exam_subjects`.`subject_id` = `rs_supplementary_subjects`.`subject_id` WHERE `rs_supplementary_subjects`.`subject_id` IN ( " ;
					$q3 .= implode(',',$suppNoChangeSuejctIds) . ")  AND `rs_exam_subjects`.`final_result` = 666 AND `rs_subjects`.`practical_type` = 1 AND `rs_supplementary_subjects`.`student_id` = ". $student_id ;
					$suppPracticalSubjectCount1NF = DB::select($q3);
				}

				if(@$suppChangeSuejctIds){
					$q4 = "SELECT count(*) AS AGGREGATE  FROM `rs_supplementary_subjects` INNER JOIN `rs_subjects` ON `rs_subjects`.`id` = `rs_supplementary_subjects`.`subject_id`  WHERE `rs_supplementary_subjects`.`subject_id` IN ( " ;
					$q4 .= implode(',',$suppChangeSuejctIds) . ")   AND `rs_subjects`.`practical_type` = 1 AND `rs_supplementary_subjects`.`student_id` = ". $student_id ;
					$suppPracticalSubjectCount2NF = DB::select($q4);
				}

				// echo "<pre>";
				// print_r($suppPracticalSubjectCount1NF);
				// echo "<br>";
				// print_r($suppPracticalSubjectCount2NF);
				// echo "<br>";
				// die;

				foreach(@$suppPracticalSubjectCount1NF as $k => $v){
					$suppPracticalSubjectCount1  = $v->AGGREGATE;
				}
				foreach(@$suppPracticalSubjectCount2NF as $k => $v){
					$suppPracticalSubjectCount2  = $v->AGGREGATE;
				}

				// echo "<pre>";
				// print_r($suppPracticalSubjectCount1);
				// echo "<br>";
				// print_r($suppPracticalSubjectCount2);
				// echo "<br>";
				// die;

				$suppPracticalSubjectCount = $suppPracticalSubjectCount1 + $suppPracticalSubjectCount2;
			/* practical end */

			/* Theory start */
				$thChangeSuejctIds = array();
				$thNoChangeSuejctIds = array();
				$suppThSubjectCount1NF = array();
				$suppThSubjectCount2NF = array();
				$q1 = "SELECT `rs_t1`.`subject_id` FROM `rs_supplementary_subjects` AS `rs_t1` INNER JOIN `rs_supplementary_subjects` AS `rs_t2` ON `rs_t1`.`id` = `rs_t2`.`id` AND `rs_t1`.`subject_id` = rs_t1.origional_subject_id WHERE `rs_t1`.`student_id` = " . $student_id ;
				$varThNoChangeSuejctIds = DB::select($q1);

				$q2 = "SELECT `rs_t1`.`subject_id` FROM `rs_supplementary_subjects` AS `rs_t1` INNER JOIN `rs_supplementary_subjects` AS `rs_t2` ON `rs_t1`.`id` = `rs_t2`.`id` AND `rs_t1`.`subject_id` != rs_t1.origional_subject_id WHERE `rs_t1`.`student_id` = " . $student_id ;
				$varThChangeSuejctIds = DB::select($q2);


				// echo "<pre>";
				// print_r($varThNoChangeSuejctIds);
				// echo "<br>";
				// print_r($varThChangeSuejctIds);
				// echo "<br>";
				// die;


				foreach(@$varThNoChangeSuejctIds as $k => $v){
					$thNoChangeSuejctIds[$v->subject_id] = $v->subject_id;
				}
				foreach(@$varThChangeSuejctIds as $k => $v){
					$thChangeSuejctIds[$v->subject_id] = $v->subject_id;
				}

				$q3 = null;
				if(@$varThNoChangeSuejctIds){
					$q3 = "SELECT count(*) AS AGGREGATE  FROM `rs_supplementary_subjects` INNER JOIN `rs_subjects` ON `rs_subjects`.`id` = `rs_supplementary_subjects`.`subject_id` INNER JOIN `rs_exam_subjects` ON `rs_exam_subjects`.`student_id` = `rs_supplementary_subjects`.`student_id` AND `rs_exam_subjects`.`subject_id` = `rs_supplementary_subjects`.`subject_id` WHERE ";
					$q3 .= " `rs_supplementary_subjects`.`subject_id` IN ( " . implode(',',$thNoChangeSuejctIds) . ") "  ;
					$q3 .= " AND `rs_exam_subjects`.`final_result` = 777 AND `rs_supplementary_subjects`.`student_id` = ". $student_id ;


					$suppThSubjectCount1NF = DB::select($q3);
				}

				// echo "<pre>";
				// print_r($q3);
				// echo "<br>";
				// print_r($varThNoChangeSuejctIds);
				// echo "<br>";
				// print_r($suppThSubjectCount1NF);
				// echo "<br>";
				// die;

				$q4 = null;
				if(@$thChangeSuejctIds){
					$q4 = "SELECT count(*) AS AGGREGATE  FROM `rs_supplementary_subjects` INNER JOIN `rs_subjects` ON `rs_subjects`.`id` = `rs_supplementary_subjects`.`subject_id` WHERE ";
					$q4 .= " `rs_supplementary_subjects`.`subject_id` IN ( " . implode(',',$thChangeSuejctIds) . ") AND "  ;
					$q4 .= "   `rs_supplementary_subjects`.`student_id` = ". $student_id ;
					$suppThSubjectCount2NF = DB::select($q4);
				}

				// echo "<pre>";
				// print_r($q4);
				// echo "<br>";
				// print_r($thChangeSuejctIds);
				// echo "<br>";
				// print_r($suppThSubjectCount2NF);
				// echo "<br>";
				// die;


				$suppThSubjectCount1  = 0;
				$suppThSubjectCount2  = 0;
				foreach(@$suppThSubjectCount1NF as $k => $v){
					$suppThSubjectCount1  = $v->AGGREGATE;
				}
				foreach(@$suppThSubjectCount2NF as $k => $v){
					$suppThSubjectCount2  = $v->AGGREGATE;
				}

				// echo "<pre>";
				// print_r($thNoChangeSuejctIds);
				// echo "<br>";
				// print_r($thChangeSuejctIds);
				// echo "<br>";

				// print_r($suppThSubjectCount1);
				// echo "<br>";

				// print_r($suppThSubjectCount2);
				// echo "<br>";
				// die;

				$examsubjectfees = $totalNumberOfSubjects = $suppThSubjectCount1 + $suppThSubjectCount2;
			/* Theory end */

			$lateFees = $this->_getSuppLateFeeAmount($supp_stream,@$studentdata->gender_id,@$student_id,@$totalNumberOfSubjects);

		/* logics on tables*/

		$suppChangeSubjectFees = $suppChangeSubjectCount * $subject_wise_fees;
		$suppPracticalSubjectFees = $suppPracticalSubjectCount * $practical_subject_wise_fees;
		$SuppExamSubjectFees = $examsubjectfees * $exam_fees;

		$finalFees = $suppChangeSubjectFees + $suppPracticalSubjectFees + $SuppExamSubjectFees + $forward_fees + $online_services_fees + $lateFees;

		$result['online_services_fees'] = @$online_services_fees;
		$result['subject_change_fees'] = @$suppChangeSubjectFees;
		$result['practical_fees'] = @$suppPracticalSubjectFees;
		$result['exam_subject_fees'] = @$SuppExamSubjectFees;
		$result['forward_fees'] = @$forward_fees;
		$result['late_fees'] = @$lateFees;
		$result['final_fees'] = @$finalFees;


		return $result;
	}

	public function getSupplementaryApplicationData($formId=null,$isPaginate=true,$aicenter_mapped_data_conditions=null){
		$role_id = Session::get('role_id');
		$aicenter_id_role = config("global.aicenter_id");
		$conditions = Session::get($formId. '_conditions');




		$aicenter_mapped_data_conditions = Session::get($formId. '_aicenter_mapped_data_conditions');



		$symbolssoid = Session::get($formId. '_symbolssoid');
		$tempCondssoid=array();
		$symbol = Session::get($formId. '_symbol');
		$symbols = Session::get($formId. '_symbols');
		$symbolstotalfees = Session::get($formId. '_symbolstotalfees');
		$symbol2 = Session::get($formId. '_symbol2');
		$symbolss = Session::get($formId. '_symbolss');
		$arraykeys=array_keys($conditions);
		if(in_array('supplementaries.late_fees',$arraykeys)){
				unset($conditions["supplementaries.late_fees"]);
				if(@$symbol){
					$tempCond = array('supplementaries.late_fees',$symbol,'0');
				}
		}

        // $conditions["supplementaries.is_aicenter_verify"] = 2;
        if(in_array('students.ssoid',$arraykeys)){
				unset($conditions["students.ssoid"]);
				if(@$symbolssoid){
					$tempCondssoid = array('students.ssoid',$symbolssoid,null);

				}
		}

		if(in_array('students.ssoid2',$arraykeys)){
			$conditions['students.ssoid']=$conditions['students.ssoid2'];
			unset($conditions["students.ssoid2"]);
		}
		if(in_array('supplementaries.total_fees',$arraykeys)){
				unset($conditions["supplementaries.total_fees"]);
				if(@$symbolstotalfees){
					$tempConds = array('supplementaries.total_fees',$symbolstotalfees,'0');

				}
		}

		if(in_array('supplementaries.total_fees2',$arraykeys)){
			$conditions["supplementaries.total_fees"]=$conditions["supplementaries.total_fees2"];
			unset($conditions["supplementaries.total_fees2"]);
	    }

		if(in_array('supplementaries.is_self_filled',$arraykeys)){

			unset($conditions["supplementaries.is_self_filled"]);
			if(@$symbolss){
				$suppTempCod = array('supplementaries.is_self_filled',$symbolss,null);
			}
		}
		if(in_array('supplementaries.challan_tid2',$arraykeys)){
			unset($conditions["supplementaries.challan_tid2"]);
			if(@$symbol2){
				$tempCond2 = array('supplementaries.challan_tid',$symbol2,null);
			}
	    }

		/* Start End Date */
		$rawQueryDateTime = 1;
		$table_name = "supplementaries";$field_name = "created_at";//locksubmitted_date
		if(@$conditions[$table_name.'.start_date'] || @$conditions[$table_name. '.end_date'] ){
			$rawQueryDateTime = CustomHelper::getStartAndEndDate(@$conditions[$table_name.'.start_date'],@$conditions[$table_name.'.end_date'],$table_name,$field_name);
			unset($conditions[$table_name.".start_date"]);
			unset($conditions[$table_name.".end_date"]);
		}
		/* Start End Date */

		$master = array();
		if($role_id == $aicenter_id_role){
			if($isPaginate){
				$defaultPageLimit = config("global.defaultPageLimit");
				$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')
				->leftJoin('applications', 'applications.student_id', '=', 'supplementaries.student_id')
				->where($conditions)
				->whereIn('supplementaries.ai_code',$aicenter_mapped_data_conditions)
				->whereRaw($rawQueryDateTime)
				->where(@$tempCond[0],@$tempCond[1],@$tempCond[2])
				->where(@$tempCond2[0],@$tempCond2[1],@$tempCond2[2])
				->where(@$tempConds[0],@$tempConds[1],@$tempConds[2])
				->where(@$suppTempCod[0],@$suppTempCod[1],@$suppTempCod[2])
				->where(@$tempCondssoid[0],@$tempCondssoid[1],@$tempCondssoid[2])
				->paginate($defaultPageLimit,array('supplementaries.student_id','supplementaries.ai_code',
				'supplementaries.stream','supplementaries.course','supplementaries.submitted','supplementaries.challan_tid',
				'supplementaries.locksumbitted','supplementaries.enrollment','supplementaries.total_fees','students.name','students.gender_id',
					'students.adm_type','supplementaries.exam_month','supplementaries.is_self_filled','supplementaries.is_department_verify','supplementaries.is_aicenter_verify','supplementaries.is_eligible','Students.ssoid'));
			}else{
				$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')
				->leftJoin('applications', 'applications.student_id', '=', 'supplementaries.student_id')
				->where($conditions)
				->whereRaw($rawQueryDateTime)
				->where(@$tempCond[0],@$tempCond[1],@$tempCond[2])
				->where(@$tempCond2[0],@$tempCond2[1],@$tempCond2[2])
				->where(@$tempConds[0],@$tempConds[1],@$tempConds[2])
				->where(@$suppTempCod[0],@$suppTempCod[1],@$suppTempCod[2])
				->where(@$tempCondssoid[0],@$tempCondssoid[1],@$tempCondssoid[2])
				->whereIn('supplementaries.ai_code',$aicenter_mapped_data_conditions)
				->get(array('supplementaries.student_id','supplementaries.ai_code','supplementaries.stream','supplementaries.course','supplementaries.submitted','supplementaries.challan_tid','supplementaries.locksumbitted','supplementaries.enrollment','supplementaries.total_fees','students.name','students.gender_id','students.adm_type','supplementaries.exam_month','supplementaries.is_self_filled','supplementaries.is_department_verify','supplementaries.is_aicenter_verify','supplementaries.is_eligible','Students.ssoid'));
			}
		}else{
			if($isPaginate){
				$defaultPageLimit = config("global.defaultPageLimit");
				$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')
				->leftJoin('applications', 'applications.student_id', '=', 'supplementaries.student_id')
				->where($conditions)
				->whereRaw($rawQueryDateTime)
				->where(@$tempCond[0],@$tempCond[1],@$tempCond[2])
				->where(@$tempCond2[0],@$tempCond2[1],@$tempCond2[2])
				->where(@$tempConds[0],@$tempConds[1],@$tempConds[2])
				->where(@$suppTempCod[0],@$suppTempCod[1],@$suppTempCod[2])
				->where(@$tempCondssoid[0],@$tempCondssoid[1],@$tempCondssoid[2])
				->paginate($defaultPageLimit,array('supplementaries.student_id','supplementaries.ai_code',
				'supplementaries.stream','supplementaries.course','supplementaries.submitted','supplementaries.challan_tid',
				'supplementaries.locksumbitted','supplementaries.enrollment','supplementaries.total_fees','students.name','students.gender_id',
					'students.adm_type','supplementaries.exam_month','supplementaries.is_self_filled','supplementaries.is_department_verify','supplementaries.is_aicenter_verify','supplementaries.is_eligible','Students.ssoid'));
					// dd($master);
			}else{

				$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')
				->leftJoin('applications', 'applications.student_id', '=', 'supplementaries.student_id')
				->where($conditions)
				->where(@$tempCond[0],@$tempCond[1],@$tempCond[2])
				->where(@$tempCond2[0],@$tempCond2[1],@$tempCond2[2])
				->where(@$tempConds[0],@$tempConds[1],@$tempConds[2])
				->where(@$tempCondssoid[0],@$tempCondssoid[1],@$tempCondssoid[2])
				->whereRaw($rawQueryDateTime)
				->get(array('supplementaries.student_id','supplementaries.ai_code','supplementaries.stream','supplementaries.course','supplementaries.submitted','supplementaries.challan_tid','supplementaries.locksumbitted','supplementaries.enrollment','supplementaries.total_fees','students.name','students.gender_id','students.adm_type','supplementaries.exam_month','supplementaries.is_self_filled','supplementaries.is_department_verify','supplementaries.is_aicenter_verify','supplementaries.is_eligible','Students.ssoid'));

			}
		}

		return $master;
	}

	public function getSupplementarylocksumbittedStudentData($formId=null,$isPaginate=true,$aicenter_mapped_data_conditions=null){
		$role_id = Session::get('role_id');
		$aicenter_id_role = config("global.aicenter_id");
		$conditions = Session::get($formId. '_conditions');
		$aicenter_mapped_data_conditions = Session::get($formId. '_aicenter_mapped_data_conditions');
		//dd($conditions);
		$master = array();
		if($role_id == $aicenter_id_role){
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->where('supplementaries.locksumbitted',1)->whereIn('supplementaries.ai_code',$aicenter_mapped_data_conditions)
			->paginate($defaultPageLimit,array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted'));
		}else{
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->where('supplementaries.locksumbitted',1)->whereIn('supplementaries.ai_code',$aicenter_mapped_data_conditions)
			->get(array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted'));
		}
		}else{
		 if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->where('supplementaries.locksumbitted',1)->paginate($defaultPageLimit,array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted'));
		}else{
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->where('supplementaries.locksumbitted',1)->get(array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted'));
		}
		}
		return $master;
	}

	public function getSupplementaryallStudentPaymentData($formId=null,$isPaginate=true,$aicenter_mapped_data_conditions=null){
	    $role_id = Session::get('role_id');
		$aicenter_id_role = config("global.aicenter_id");
		$conditions = Session::get($formId. '_conditions');
		$aicenter_mapped_data_conditions = Session::get($formId. '_aicenter_mapped_data_conditions');

		$master = array();
		if($role_id == $aicenter_id_role){
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->whereNotNull('supplementaries.challan_tid')->whereNotNull('supplementaries.submitted')->whereIn('supplementaries.ai_code',$aicenter_mapped_data_conditions)->where('supplementaries.locksumbitted',1)->paginate($defaultPageLimit,array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted','supplementaries.challan_tid','supplementaries.submitted','supplementaries.total_fees'));
		}else{
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->whereNotNull('supplementaries.challan_tid')->whereNotNull('supplementaries.submitted')->whereIn('supplementaries.ai_code',$aicenter_mapped_data_conditions)->where('supplementaries.locksumbitted',1)->get(array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted','supplementaries.challan_tid','supplementaries.submitted','supplementaries.total_fees'));
		}
		}else{
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->whereNotNull('supplementaries.challan_tid')->whereNotNull('supplementaries.submitted')->where('supplementaries.locksumbitted',1)->paginate($defaultPageLimit,array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted','supplementaries.challan_tid','supplementaries.submitted','supplementaries.total_fees'));
		}else{
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->whereNotNull('supplementaries.challan_tid')->whereNotNull('supplementaries.submitted')->where('supplementaries.locksumbitted',1)->get(array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted','supplementaries.challan_tid','supplementaries.submitted','supplementaries.total_fees'));
		}
		}
		return $master;
	}

	public function allgetSupplementaryStudentNotPayPaymentData($formId=null,$isPaginate=true,$aicenter_mapped_data_conditions=null){
		$role_id = Session::get('role_id');
		$aicenter_id_role = config("global.aicenter_id");
		$conditions = Session::get($formId. '_conditions');
		$aicenter_mapped_data_conditions = Session::get($formId. '_aicenter_mapped_data_conditions');
		$master = array();
		if($role_id == $aicenter_id_role){
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->whereNull('supplementaries.challan_tid')->whereNull('supplementaries.submitted')->whereIn('supplementaries.ai_code',$aicenter_mapped_data_conditions)->where('supplementaries.locksumbitted',1)->paginate($defaultPageLimit,array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted','supplementaries.challan_tid','supplementaries.submitted'));

		}else{
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->whereNull('supplementaries.challan_tid')->whereNull('supplementaries.submitted')->whereIn('supplementaries.ai_code',$aicenter_mapped_data_conditions)->where('supplementaries.locksumbitted',1)->get(array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted','supplementaries.challan_tid','supplementaries.submitted'));
		}
		}else{
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->whereNull('supplementaries.challan_tid')->whereNull('supplementaries.submitted')->where('supplementaries.locksumbitted',1)->paginate($defaultPageLimit,array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted','supplementaries.challan_tid','supplementaries.submitted'));


		}else{
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->whereNull('supplementaries.challan_tid')->whereNull('supplementaries.submitted')->where('supplementaries.locksumbitted',1)->get(array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted','supplementaries.challan_tid','supplementaries.submitted'));
		}
		}
		return $master;
	}

	public function getSupplementaryAicenterStudentNotPayPaymentData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->whereNull('supplementaries.challan_tid')->whereNull('supplementaries.submitted')->where('supplementaries.locksumbitted',1)->paginate($defaultPageLimit,array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted','supplementaries.challan_tid','supplementaries.submitted'));

		}else{
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->whereNull('supplementaries.challan_tid')->whereNull('supplementaries.submitted')->where('supplementaries.locksumbitted',1)->get(array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted','supplementaries.challan_tid','supplementaries.submitted'));
		}
		return $master;
	}
	
	public function getSupplementaryaicenterStudentPaymentData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->whereNotNull('supplementaries.challan_tid')->whereNotNull('supplementaries.submitted')->where('supplementaries.locksumbitted',1)->paginate($defaultPageLimit,array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted','supplementaries.challan_tid','supplementaries.submitted','supplementaries.total_fees'));
		}else{
			$master = Supplementary::leftJoin('students', 'students.id', '=', 'supplementaries.student_id')->where($conditions)->whereNotNull('supplementaries.challan_tid')->whereNotNull('supplementaries.submitted')->where('supplementaries.locksumbitted',1)->get(array('supplementaries.student_id','students.name','students.gender_id','supplementaries.enrollment','students.adm_type','supplementaries.stream','supplementaries.course','supplementaries.ai_code','supplementaries.locksumbitted','supplementaries.challan_tid','supplementaries.submitted','supplementaries.total_fees'));
		}

		return $master;
	}
	
	public function suppFindEnrollmentValidationByEnrollment($enrollment=null,$estudent_id=null){
		$isValid = true;
		$errors = null;
		$validator = Validator::make([], []);
		$errMsg = '';
		$student_id = Crypt::decrypt($estudent_id);
		$custom_controller_obj = new Controller;
		$checkchangerequestsssupplementariesAllowOrNotAllow = $custom_controller_obj->_checkchangerequestssupplementariesAllowOrNotAllow();
		$custom_component_obj = new CustomComponent;
		$studentdata = Student::where('enrollment',$enrollment)->first();
		// $studentdata = Student::where('enrollment',$enrollment)->first()->toArray();
		// dd($studentdata);
		//$exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
		//$formOpenAllowOrNot = $custom_component_obj->checkSuppEntryAllowOrNot($current_exam_month_id);
		// $formOpenAllowOrNot = $custom_component_obj->checkSuppEntryAllowOrNot($studentdata['stream']);
        $exam_year = Config::get('global.form_supp_current_admission_session_id');
		$exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
		$formOpenAllowOrNot = $custom_component_obj->checkSuppEntryAllowOrNot($current_exam_month_id);
		$changereqestsupplementaryid = Supplementary::where('student_id',$student_id)->where('exam_year','=',$exam_year)->where('exam_month','=',$exam_month)->where('supp_student_change_requests',2)->first('supp_student_change_requests');

       if(@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$changereqestsupplementaryid->supp_student_change_requests ==2){
	   }else{
		if(!$formOpenAllowOrNot){
			$fld = 'enrollment';
			$errMsg = 'Supplementary Form date has been closed.';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}
	   }
		if($isValid==true &&  empty($enrollment)){
			$fld = 'enrollment';
			$errMsg = 'Please select mandatory field "Enrollment Number".';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		$ai_code = Session::get('ai_code');
		// $ai_code = Session::get('ai_code');
		$logged_user_aicode_list = $custom_component_obj->getAicodeAllowListLoggedUser($ai_code);
		$enrollment_aicode = $custom_component_obj->getAicodeFromEnrollment($enrollment);

		/*
		echo "<pre>";
		print_r($ai_code);
		echo "test1";
		echo "</br>";
		print_r($logged_user_aicode_list);
		echo "-------";
		echo "</br>";
		// @dd($enrollment_aicode);
		*/

		$role_id = @Session::get('role_id');
		$super_admin_id = Config::get("global.super_admin_id");
		$developer_admin  = Config::get("global.developer_admin");
		$examination_department  = Config::get("global.examination_department");
		$student  = Config::get("global.student");
		$superAllowStatus=true;
		if($role_id == $super_admin_id || $role_id == $developer_admin  || $role_id == $student || $role_id == $examination_department){
			$superAllowStatus = false;
		}


		if(@$superAllowStatus){
			if($isValid==true && !in_array($enrollment_aicode,$logged_user_aicode_list)){
				$fld = 'enrollment';
				$errMsg = 'ध्यान! आपको विशिष्ट AI केंद्र की अनुमति नहीं है।(Attention! You are not allowed to specific AI center.)';
				$errors = $errMsg;
				$validator->getMessageBag()->add($fld, $errMsg);
				$isValid = false;
			}
		}

		if($isValid==true && !empty($enrollment)){
			// change the below row query into eloquent query
			$passedResultCount = DB::table('exam_results')->where('enrollment',$enrollment)->whereNull('deleted_at')->where('final_result','PASS')->count();
			// @dd($passedResultCount);
			if($passedResultCount > 0){
				$fld = 'enrollment';
				$errMsg = 'Student already passed.';
				$errors = $errMsg;
				$validator->getMessageBag()->add($fld, $errMsg);
				$isValid = false;
			}
		}

		if($isValid==true && $studentdata['adm_type'] == 4){
			$fld = 'enrollment';
			$errMsg = 'We are not allowed for supplementary exam for "Improvement" Admission Type.';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		if($isValid==true && !empty($enrollment)){
			$studentExistCount = Student::where('enrollment',$enrollment)->count();
			if($studentExistCount ==0 ){
				$fld = 'enrollment';
				$errMsg = 'Error! Enrollment does not found in our portal.';
				$errors = $errMsg;
				$validator->getMessageBag()->add($fld, $errMsg);
				$isValid = false;
			}
		}

		if($isValid==true && !empty($enrollment)){
			$aplicationExistCount = Application::where('enrollment',$enrollment)->where('locksumbitted',1)->count();
			if($aplicationExistCount==0){
				$fld = 'enrollment';
				$errMsg = 'Error! Application not lock & Submitted in our portal.';
				$errors = $errMsg;
				$validator->getMessageBag()->add($fld, $errMsg);
				$isValid = false;
			}
		}


		if($isValid==true && !empty($enrollment)){
			$passedResultCount = DB::table('exam_results')->where('enrollment',$enrollment)->where('final_result','PASS')->whereNull('deleted_at')->count();
			if($passedResultCount > 0){
				$fld = 'enrollment';
				$errMsg = 'Student already passed.';
				$errors = $errMsg;
				$validator->getMessageBag()->add($fld, $errMsg);
				$isValid = false;
			}
		}


		$enrollment_year = $custom_component_obj->getYearFromEnrollment($enrollment);
		$current_supp_exam_month = config("global.supp_current_admission_exam_month");
		$supp_allow_years_list = $custom_component_obj->getSuppAllowedYear($current_supp_exam_month,$studentdata['exam_year']);
		// @dd($enrollment_year);
		 // dd($supp_allow_years_list);
	if(@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$changereqestsupplementaryid->supp_student_change_requests ==2){
	   }else{

		if($isValid==true && !in_array($enrollment_year,$supp_allow_years_list)){
			$fld = 'enrollment';
			$errMsg = 'ध्यान! आपको चालू वर्ष के लिए पूरक परीक्षा फॉर्म भरने की अनुमति नहीं है ।(Attention! You are not allowed to fill out supplementary exam forms for the current year.)';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}
	   }
		$response['isValid'] = $isValid;
		$response['errors'] = $errors;
		$response['validator'] = $validator;
		//@dd($response);
		return $response;
	}
	
	public function checkSuppEntryAllowOrNot($stream){
		$res = false;
		$master = ExamLateFeeDate::where('stream',$stream)
			// ->where('gender_id',$gender_id)
			->where('from_date', '<=', Carbon::now())
			->where('to_date', '>=', Carbon::now())
			->where('is_supplementary', '=', 1)
			->count();
		if($master > 0){
			$res = true;
		}


		$custom_component_obj = new CustomComponent();
		$isAdminStatus = $custom_component_obj->_checkIsAdminRole();
		if($isAdminStatus){
			$res = true;
		}

		return $res;
	}
	
	public function getAicodeAllowListLoggedUser($ai_code=null){
		$logged_user_aicode_list = AiCenterMap::where('parent_aicode','=',$ai_code)->get()->pluck('ai_code')->toArray();
		// @dd($logged_user_aicode_list);
		return $logged_user_aicode_list;
	}
 
	public function getAicodeFromEnrollment($enrollemnt=null){
		$res = '';
		if(!empty($enrollemnt)){
			$res = substr($enrollemnt,0,5);
		}
		return $res;
	}

	public function getYearFromEnrollment($enrollemnt=null){
		$res = '';
		if(!empty($enrollemnt)){
			$res = substr($enrollemnt,5,2);
		}

		return $res;
	}

	public function getSuppAllowedYear($stream=null,$exam_year=null){
		$supp_allow_years_list = array();
		$condtion_supp_allow_years = ['status' => 1,'combo_name' =>'supp_allow_years','option_id' => $stream];
		$supp_allow_years_arr = DB::table('masters')->where($condtion_supp_allow_years)->get()->toArray();
		$supp_allow_years_list = array();
		if(!empty($supp_allow_years_arr[0]->option_val)){
			$supp_allow_years_list = explode(',',$supp_allow_years_arr[0]->option_val);
		}
		// please check the below condtion accrdoing to current requirement
		if(isset($exam_year) && isset($stream) && $exam_year==121 && $stream == 1){
			$supp_allow_years_list[] = 19;
		}
		return $supp_allow_years_list;
	}

	public function suppFindEnrollmentValidation($request){
		$isValid = true;
		$errors = null;
		$validator = Validator::make([], []);
		$errMsg = '';

		$enrollment = $request->enrollment;
		$custom_component_obj = new CustomComponent;
		$studentdata = Student::where('enrollment',$enrollment)->first();

		$ExamSubjectYearArr = ExamSubject::where('student_id',$studentdata['id'])->latest('exam_year')->orderby('exam_month', 'asc')->first('exam_year');
		$exam_year_latest = null;
		if(!empty(@$ExamSubjectYearArr->exam_year)){
			$exam_year_latest = @$ExamSubjectYearArr->exam_year;
		}
		$custom_controller_obj = new Controller;
		$checkchangerequestsssupplementariesAllowOrNotAllow = $custom_controller_obj->_checkchangerequestssupplementariesAllowOrNotAllow();

		$exam_year = Config::get('global.form_supp_current_admission_session_id');
		$exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
		$changereqestsupplementaryid = Supplementary::where('student_id',$studentdata)->where('exam_year','=',$exam_year)->where('exam_month','=',$exam_month)->where('supp_student_change_requests',2)->first('supp_student_change_requests');

		//$exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
		//$formOpenAllowOrNot = $custom_component_obj->checkSuppEntryAllowOrNot($current_exam_month_id);
		$formOpenAllowOrNot = $custom_component_obj->checkSuppEntryAllowOrNot($studentdata['stream']);
		 if(@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$changereqestsupplementaryid->supp_student_change_requests ==2){

		 }else{
		 if(!$formOpenAllowOrNot){
			$fld = 'enrollment';
			$errMsg = 'पूरक आवेदन पत्र की तिथि समाप्त कर दी गई है।(The supplementary Application form date has been closed.)';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}
		 }


		if($isValid==true &&  empty($enrollment)){
			$fld = 'enrollment';
			$errMsg = 'Please select mandatory field "Enrollment Number".';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		if($isValid==true && $studentdata['adm_type'] == 4){
			$fld = 'enrollment';
			$errMsg = 'हमें "सुधार" प्रवेश प्रकार के लिए पूरक परीक्षा की अनुमति नहीं है।(We are not allowed for the supplementary exam for "Improvement" Admission Type.)';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		if($isValid==true && !empty($enrollment)){
			$studentExistCount = Student::where('enrollment',$enrollment)->count();
			if($studentExistCount ==0 ){
				$fld = 'enrollment';
				$errMsg = 'Error! Enrollment does not found in our portal.';
				$errors = $errMsg;
				$validator->getMessageBag()->add($fld, $errMsg);
				$isValid = false;
			}
		}

		if($isValid==true && !empty($enrollment)){
			$aplicationExistCount = Application::where('enrollment',$enrollment)->where('locksumbitted',1)->count();
			if($aplicationExistCount==0){
				$fld = 'enrollment';
				$errMsg = 'Error! Application not lock & Submitted in our portal.';
				$errors = $errMsg;
				$validator->getMessageBag()->add($fld, $errMsg);
				$isValid = false;
			}
		}

		$ai_code = Session::get('ai_code');
		$logged_user_aicode_list = $custom_component_obj->getAicodeAllowListLoggedUser($ai_code);
		$enrollment_aicode = $custom_component_obj->getAicodeFromEnrollment($enrollment);


		// echo "<pre>";
		// print_r($ai_code);
		// echo "test2";
		// echo "</br>";
		// print_r($logged_user_aicode_list);
		// echo "-------";
		// echo "</br>";
		// @dd($enrollment_aicode);


		$role_id = @Session::get('role_id');
		$super_admin_id = Config::get("global.super_admin_id");
		$developer_admin  = Config::get("global.developer_admin");
		$examination_department  = Config::get("global.examination_department");
		$student  = Config::get("global.student");
		$superAllowStatus=true;
		if($role_id == $super_admin_id || $role_id == $developer_admin  || $role_id == $student || $role_id == $examination_department){
			$superAllowStatus = false;
		}

		if(@$superAllowStatus){
			if($isValid==true && !in_array($enrollment_aicode,$logged_user_aicode_list)){
				$fld = 'enrollment';
				$errMsg = 'ध्यान! आपको विशिष्ट AI केंद्र की अनुमति नहीं है।(Attention! You are not allowed to specific AI center.)';
				$errors = $errMsg;
				$validator->getMessageBag()->add($fld, $errMsg);
				$isValid = false;
			}
		}

		$examResDeleareOrNot = $custom_component_obj->checkExamResDeleareOrNotByEnrollment($enrollment);
		if($isValid==true &&  $examResDeleareOrNot==false){
			$fld = 'enrollment';
			$errMsg = "दर्ज नामांकन संख्या का परिणाम घोषित नहीं किया गया है, इसलिए पूरक आवेदन भरने के लिए नामांकन की अनुमति नहीं है। (Entered enrollment number's Result not Declared, So Enrollment is not allowed to fill the supplementary application.)";
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		$examResRwh = $custom_component_obj->checkExamResRwhByEnrollment($enrollment);
		if($isValid==true &&  $examResRwh==true){ // RWH
			$fld = 'enrollment';
			$errMsg = "आपका रिजल्ट नहीं मिल रहा है, कृपया राजस्थान स्टेट ओपन स्कूल विभाग से संपर्क करें। (Your's result is not found, Please contact to Rajasthan State Open School Department.)";
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		if($studentdata['adm_type'] == 3){ //Part admission rule to check each subject result other than final result

			// if($isValid==true && !empty($enrollment)){
			// 	$query = "select count(*) as counter from `rs_exam_subjects` where `enrollment` = " . $enrollment . " and (`final_result` = 'PASS' or `final_result` = 'P')"; //666,777,888,999
			// 	$passedResultCountResult = DB::select($query);
			// 	$passedResultCount = 0;
			// 	if(@$passedResultCountResult[0]->counter){
			// 		$passedResultCount = $passedResultCountResult[0]->counter;
			// 	}

			// 	if($passedResultCount > 0){
			// 		$fld = 'enrollment';
			// 		$errMsg = 'Student already passed.';
			// 		$errors = $errMsg;
			// 		$validator->getMessageBag()->add($fld, $errMsg);
			// 		$isValid = false;
			// 	}
			// }


			if($isValid==true && !empty($enrollment)){
				$query = "select count(*) as counter from `rs_exam_subjects` where `enrollment` = " . $request->enrollment . " and (`final_result` != 'PASS' and `final_result` != 'P' and `final_result` != 'p') and `exam_year` = " .$exam_year_latest;
				$passedResultCountResult = DB::select($query);
				$passedResultCount = 0;
				if(@$passedResultCountResult[0]->counter){
					$passedResultCount = $passedResultCountResult[0]->counter;
				}

				if($passedResultCount <= 0){
					$fld = 'enrollment';
					$errMsg = 'Student already passed.';
					$errors = $errMsg;
					$validator->getMessageBag()->add($fld, $errMsg);
					$isValid = false;
				}
			}
		}else{
			if($isValid==true && !empty($enrollment)){
				$passedResultCount = DB::table('exam_results')->where('enrollment',$enrollment)->where('final_result','PASS')->count();
				if($passedResultCount > 0){
					$fld = 'enrollment';
					$errMsg = 'Student already passed.';
					$errors = $errMsg;
					$validator->getMessageBag()->add($fld, $errMsg);
					$isValid = false;
				}
			}
		}

		$enrollment_year = $custom_component_obj->getYearFromEnrollment($enrollment);
		$supp_allow_years_list = $custom_component_obj->getSuppAllowedYear($studentdata['stream'],$studentdata['exam_year']);
		if($isValid==true && !in_array($enrollment_year,$supp_allow_years_list)){
			$fld = 'enrollment';
			$errMsg = 'ध्यान! आपको चालू वर्ष के लिए पूरक परीक्षा फॉर्म भरने की अनुमति नहीं है।(Attention! You are not allowed to fill out supplementary exam form for the current year.)';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		$response['isValid'] = $isValid;
		$response['errors'] = $errors;
		$response['validator'] = $validator;
		return $response;
	}

	public function checkExamResDeleareOrNotByEnrollment($enrollment=null){
		$res = false;
		$condtion_supp_toc_exist_status = DB::table('exam_results')->where('enrollment',$enrollment)->count();
		if( $condtion_supp_toc_exist_status > 0 ){
			$res = true;
		}
		return $res;
	}
	
	public function checkExamResRwhByEnrollment($enrollment=null){
		$res = false;
		//$supp_exam_year = CustomHelper::_get_selected_sessions();
		//$supp_exam_month = Config::get('global.supp_current_admission_exam_month');
		
		// $condtion_rwh_status = DB::table('exam_results')->where('enrollment',$enrollment)->where('final_result','RWH')->where('exam_year',$supp_exam_year)->where('exam_month',$supp_exam_month)->count();
		$condtion_rwh_status = DB::table('exam_results')->where('enrollment',$enrollment)->whereNull('deleted_at')->where('final_result','RWH')->count();
		if( $condtion_rwh_status > 0 ){
			$res = true;
		}
		return $res;
	}
	
	public function isValidSuppSubjects($inputs=null,$student_data=null,$request_all=null){
		$isValid = true;
		$errors = null;
		$errMsg = '';
		$response = false;
		$validators = null;
		$filledComSubjects = 0;
		$filledAddiSubjects = 0;
		$filledComLanSubjects = 0;
		$filledAddiLangSubjects = 0;
		$checkcount =null;
		$validator = Validator::make([], []);

		$custom_component_obj = new CustomComponent;
		$student_id = Crypt::decrypt($request_all['student_id']);

		$custom_controller_obj = new Controller;
		$checkchangerequestsssupplementariesAllowOrNotAllow = $custom_controller_obj->_checkchangerequestssupplementariesAllowOrNotAllow();

        $exam_year = Config::get('global.form_supp_current_admission_session_id');
		$suppallowstream=config::get("global.supp_current_admission_exam_month");
		$changereqestsupplementaryid = Supplementary::where('student_id',@$student_id)->where('exam_year','=',$exam_year)->where('exam_month','=',$suppallowstream)->where('supp_student_change_requests',2)->first('supp_student_change_requests');
		$formOpenAllowOrNot = $custom_component_obj->checkSuppEntryAllowOrNot($suppallowstream);
		if(@$checkchangerequestsssupplementariesAllowOrNotAllow == true && @$changereqestsupplementaryid->supp_student_change_requests == 2){
		}else{
		if(!$formOpenAllowOrNot){
			$fld = 'subject_id[]';
			$errMsg = 'Supplementary Form date has been closed.';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}
		}

		$ai_code = Session::get('ai_code');

		//$ai_code = '1001'; // That line is temperoray code
		$logged_user_aicode_list = $custom_component_obj->getAicodeAllowListLoggedUser($ai_code);
		$enrollment_aicode = $custom_component_obj->getAicodeFromEnrollment($student_data['enrollment']);

		$role_id = @Session::get('role_id');
		$super_admin_id = Config::get("global.super_admin_id");
		$developer_admin  = Config::get("global.developer_admin");
		$student  = Config::get("global.student");
		$superAllowStatus=true;
		if($role_id == $super_admin_id || $role_id == $developer_admin  || $role_id == $student){
			$superAllowStatus = false;
		}

		if(@$superAllowStatus){
			if($isValid==true && !in_array($enrollment_aicode,$logged_user_aicode_list)){
				$fld = 'enrollment';
				$errMsg = 'ध्यान! आपको विशिष्ट AI केंद्र की अनुमति नहीं है।(Attention! You are not allowed to specific AI center.)';
				$errors = $errMsg;
				$validator->getMessageBag()->add($fld, $errMsg);
				$isValid = false;
			}
		}

		/*
		if( $isValid==true   &&  empty($request_all['mobile'])){
			$fld = 'mobile';
			$errMsg = "Mobile Number is Required";
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}
		*/


		$examResDeleareOrNot = $custom_component_obj->checkExamResDeleareOrNotByEnrollment($student_data['enrollment']);
		if($isValid==true &&  !$examResDeleareOrNot){
			$fld = 'enrollment';
			$errMsg = "Your's Exam Result Not Decalred, So you are not eligibale for sullpementary form.";
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		$examResRwh = $custom_component_obj->checkExamResRwhByEnrollment($student_data['enrollment']);
		if($isValid==true &&  $examResRwh==true){ // RWH
			$fld = 'enrollment';
			$errMsg = "आपका रिजल्ट नहीं मिल रहा है, कृपया राजस्थान स्टेट ओपन स्कूल विभाग से संपर्क करें। (Your's result is not found, Please contact to Rajasthan State Open School Department.)";
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		// filledSubjects
		if($isValid==true && (empty(array_filter($inputs)) || count(array_filter($inputs)) <= 0)){
			$fld = 'subject_id';
			$errMsg = 'Please select at least minimum 1 subjects';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}
		if($isValid==true && $student_data['adm_type']==4){ // Improvement adm_type
			$fld = 'enrollment';
			$errMsg = 'You are not allowed for supplementary exam for "Improvement" Admission Type.';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}

		/*
		if($isValid==true && $student_data['adm_type']==5 && $student_data['course']==12 ){ // ITI adm_type
		// if($isValid==true && $student_data['adm_type']==5){ // ITI adm_type
			$fld = 'enrollment';
			$errMsg = 'You are not allowed for supplementary exam for "ITI Admission" Type & 12th case.';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg);
			$isValid = false;
		}
		*/

		$filledComLanSubjects = 0;
		$filledAddiLangSubjects = 0;
		$controller_obj = new Controller;
		$custom_component_obj = new CustomComponent;
		$supp_passed_subject_arr = $custom_component_obj->getPassedSuppSubject($student_data['id']);
		if(isset($supp_passed_subject_arr) && !empty($supp_passed_subject_arr) ){
			foreach($supp_passed_subject_arr as $supp_passed_subject_id){
				$isLangSubject = $controller_obj->checkIsLanguageSubject($supp_passed_subject_id);
				if($isLangSubject){
					$filledComLanSubjects++;
				}
			}
		}
		if($isValid==true && !empty(array_filter($inputs))){
			$inputs = array_filter($inputs);
			foreach(@$inputs as $k => $v){
				$subject_id = $v;
				$filledSubjects[] = $subject_id;
				if($isValid){
					$isUniqueSubects = $controller_obj->checkIsUniqueSubject($filledSubjects);
					if(!$isUniqueSubects){
						$duplicateSubjectsName = $controller_obj->getDuplicateSubject($filledSubjects);
						$fld = 'subject_id';
						$errMsg = 'Please select failure & unique subjects.  (Duplicate subjects : ' . $duplicateSubjectsName .')';
						$errors = $errMsg;
						$validator->getMessageBag()->add($fld, $errMsg);
						$isValid = false;
					}
				}



				if($isValid==true){
					$tosStatus = $custom_component_obj->checkTocSubjectExist($subject_id,$student_data['id']);
					if($tosStatus==true){
						$fld = 'subject_id';
						$errMsg = 'Error! You have already taken TOC in filled subject.';
						$errors = $errMsg;
						$validator->getMessageBag()->add($fld, $errMsg);
						$isValid = false;
					}
				}



				$custom_component_obj = new CustomComponent;
				// Max 2 OR min 1 Language Subject validation


				if($isValid){

					if($k >= 0 && $k <= 4){ //compulsary
						if($v != null){
							//function check is lang or not
							$isLangSubject = $controller_obj->checkIsLanguageSubject($subject_id);
							if($isLangSubject){
								$filledComLanSubjects++;
							}
						}
					}
					if($k == 5 || $k == 6){ //additonal
						$isLangSubject = $controller_obj->checkIsLanguageSubject($subject_id);
						if($isLangSubject){
							$filledAddiLangSubjects++;
						}
					}
				}

				 // dd($v);
				 //dd($filledAddiLangSubjects);

				/* start for more than 2 com lan subject passed */
					$lang_supp_passed_subject_arr = $custom_component_obj->getPassedLanSuppSubject($student_data['id']);



					$lang_supp_passed_subject_arr_count = count($lang_supp_passed_subject_arr);
					if($lang_supp_passed_subject_arr_count > 2){
						$filledComLanSubjects = 2;
					}



				/* End for more than 2 com lan subject passed  */

				if($isValid==true && $filledComLanSubjects > 2){
					$fld = 'subject_id';
					$errMsg = 'Error! You cant select more then two compulsary language subject.';
					$errors = $errMsg;
					$validator->getMessageBag()->add($fld, $errMsg);
					$isValid = false;
				}


				/* Change Start  */
				 if($filledComLanSubjects == 1 || $filledComLanSubjects == 0){
					 $checkcount = 2;
				 }
				 if($filledComLanSubjects == 2){
					  $checkcount = 1;
				 }

				 /* Change End  */

				if(@$isValid==true && @$checkcount && @$filledAddiLangSubjects > $checkcount ){
					$fld = 'subject_id';
					$errMsg = 'Error! You cant select more then one addtional language subject.';
					$errors = $errMsg;
					$validator->getMessageBag()->add($fld, $errMsg);
					$isValid = false;
				}

				$totalSelectedLanSubjectsCount = $filledComLanSubjects + $filledAddiLangSubjects;



				if($totalSelectedLanSubjectsCount <= 0 && $student_data['adm_type'] != 3){
					$fld = 'subject_id';
					$errMsg = 'Error! Please select at least one language subject.';
					$errors = $errMsg;
					$validator->getMessageBag()->add($fld, $errMsg);
					$isValid = false;
				}

				// Max 2 OR min 1 Language Subject validation
			}
		}
		$PassedSubjectArr = $controller_obj->getPassedSubject($student_data->id);
		if($isValid==true){
			$filledSubjectsArr = array_filter($filledSubjects);

			$subject_passed_arr_same_value = array_intersect($filledSubjectsArr,$PassedSubjectArr);

			// echo "<pre>";
			// print_r($filledSubjectsArr);
			// print_r($PassedSubjectArr);
			// dd($subject_passed_arr_same_value);


			if(!empty($subject_passed_arr_same_value) ){
				$fld = 'subject_id';
				$errMsg = 'You cant select passed subject in supplementary';
				$errors = $errMsg;
				$validator->getMessageBag()->add($fld, $errMsg);
				$isValid = false;
			}
		}


		if($isValid==true && $student_data['adm_type']==5){ // ITI CASE
			$filledSubjectsArr = array_filter($filledSubjects);
			$AllSupSubjectArr = $controller_obj->getAllSuppSubject($student_data->id);
			$subject_diff = array_diff($filledSubjectsArr,$AllSupSubjectArr);
			if(!empty($subject_diff) ){
				$fld = 'subject_id';
				$errMsg = "You can't change subject in ITI admission";
				$errors = $errMsg;
				$validator->getMessageBag()->add($fld, $errMsg);
				$isValid = false;
			}
		}
		if($isValid==true){
			$filledSubjectsArr = array_filter($filledSubjects);
		}

		if($isValid==true && $student_data['adm_type']==3){ // Part Admission CASE
			$AllSupSubjectArr = $controller_obj->getAllSuppSubject($student_data->id);
			$subject_diff = array_diff($filledSubjectsArr,$AllSupSubjectArr);
			if(!empty($subject_diff) ){
				$fld = 'subject_id';
				$errMsg = "You can't change subject in Part admission";
				$errors = $errMsg;
				$validator->getMessageBag()->add($fld, $errMsg);
				$isValid = false;
			}
		}
		@$student_id =$student_data['id'];
		/* new start */
			$exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
			$exam_year = CustomHelper::_get_selected_sessions();
			$minuesValue = 0;
			if($exam_month == 1){
				$minuesValue = 2;
			}else if($exam_month == 2){
				$minuesValue = 3;
			}
			$stuudentDetails = Student::join('applications', 'applications.student_id' , '=', 'students.id')
				->where('students.id',$student_id)
				->where('students.adm_type',1)
				->where('students.course',12)
				->where('applications.year_pass', ">",($exam_year - $minuesValue))
				->where('applications.pre_qualification',10)
				->get()->toArray();

		/* new end */
		/* old start */
			// $exam_year = CustomHelper::_get_selected_sessions();
			// $stuudentDetails = Student::join('applications', 'applications.student_id' , '=', 'students.id')
				// ->where('students.id',$student_data->id)
				// ->where('students.adm_type',1)
				// ->where('students.course',12)
				// ->where('applications.year_pass', ($exam_year - 1))
				// ->where('applications.pre_qualification',10)
				// ->get()->toArray();
		/* old end */
		if($isValid==true){
			// $totalSubjectOfStudent = $filledSubjectsArr;
			$totalSubjectOfStudent = $filledSubjectsArr + $PassedSubjectArr;
		}
		// echo "<pre>";
		// print_r($filledSubjectsArr);
		// echo "<br>";
		// print_r($PassedSubjectArr);
		// dd($stuudentDetails);
		if($isValid==true && @$stuudentDetails){
			if(count($totalSubjectOfStudent) > 4){
				$fld = 'subject_id';
				$errMsg .= 'Fifth subject not allowed.Please select maximum 4 subjects.';
				$errors = $errMsg;
				$validator->getMessageBag()->add($fld, $errMsg);
				$isValid = false;
			}
		}
		$response['isValid'] = $isValid;
		$response['errors'] = $errors;
		$response['validator'] = $validator;

		return $response;
	}
	
	public function getPassedSuppSubject($student_id=null){
		$ExamSubjectYearArr = ExamSubject::where('student_id',$student_id)->latest('exam_year')->orderby('exam_month', 'asc')->first(['exam_year','exam_month']);
		$exam_year_latest = null;
		$exam_month_latest = null;
		if(!empty(@$ExamSubjectYearArr->exam_year)){
			$exam_year_latest = @$ExamSubjectYearArr->exam_year;
		}
		if(!empty(@$ExamSubjectYearArr->exam_month)){
			$exam_month_latest = @$ExamSubjectYearArr->exam_month;
		}
		
		
		$passed_subject_arr = array();
		$passed_subjects_data = ExamSubject::where('student_id',$student_id)
		->where('final_result','=','p')
		->where('exam_subjects.exam_year',$exam_year_latest)
		->where('exam_subjects.exam_month',$exam_month_latest)
		->get()->toArray();
		
		if(isset($passed_subjects_data) && !empty($passed_subjects_data) ){
			foreach($passed_subjects_data as $passed_subject){
				$passed_subject_arr[] = $passed_subject['subject_id'];
			}
		}
		return $passed_subject_arr;
	}
	/*change as par code */

	public function checkTocSubjectExist ($subject_id=null,$student_id=null){
		$res = false;
		$condtion_supp_toc_exist_status = DB::table('toc_marks')->where('subject_id','=',$subject_id)->where('student_id','=',$student_id)->count();
		if( $condtion_supp_toc_exist_status > 0 ){
			$res = true;
		}
		return $res;
	}
	
	public function getPassedLanSuppSubject($student_id=null){
		$ExamSubjectYearArr = ExamSubject::where('student_id',$student_id)->latest('exam_year','desc')->orderby('exam_month', 'asc')->first(['exam_year','exam_month']);

		//->orderby('exam_month', 'asc')
		// dd($ExamSubjectYearArr);

		$exam_year_latest = null;
		$exam_month_latest = null;
		if(!empty(@$ExamSubjectYearArr->exam_year)){
			$exam_year_latest = @$ExamSubjectYearArr->exam_year;
		}
		if(!empty(@$ExamSubjectYearArr->exam_month)){
			$exam_month_latest = @$ExamSubjectYearArr->exam_month;
		}

		$passed_subject_arr = array();
		$passed_subjects_data = ExamSubject::join('subjects', 'subjects.id', '=', 'exam_subjects.subject_id')
		->where('student_id',$student_id)
		->where('subjects.subject_type','A')
		->where('final_result','=','p')
		->where('exam_subjects.exam_year',$exam_year_latest)
		->where('exam_subjects.exam_month',$exam_month_latest)
		->get()->toArray();



		if(isset($passed_subjects_data) && !empty($passed_subjects_data) ){
			foreach($passed_subjects_data as $passed_subject){
				$passed_subject_arr[] = $passed_subject['subject_id'];
			}
		}
		return $passed_subject_arr;
	}
	
	public function getAdminstrativeCustomReport($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$master = array();

		$queryOrder = "CAST(serial_number AS unsigned) desc";

		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = MasterQuerieExcel::where($conditions)
			->orderByRaw($queryOrder)
			->paginate($defaultPageLimit);
		}else{
			$master = MasterQuerieExcel::where($conditions)->orderByRaw($queryOrder)
			->get();
		}
		return $master;
	}
	
	public function getAdminstrativeCustomDocumentReport($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = MasterAdminDocument::where($conditions)
			->paginate($defaultPageLimit);
		}else{
			$master = MasterAdminDocument::where($conditions)->orderBy(DB::raw('CAST(serial_number as UNSIGNED)'),'DESC')
			->get();
		}
		return $master;
	}
	
	public function getStudentFeeSummaryPay($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_condtions');
		$symbol = Session::get($formId. '_symbol');
		$tempCond = null;
		if(@$symbol){
			$tempCond = array('student_fees.late_fee', $symbol ,0);
		}

		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$selected_session = CustomHelper::_get_selected_sessions();
			$master = DB::table('users')
			->select([
				'users.college_name','users.ai_code',
				DB::raw('count(rs_students.id) as number_of_student'),
				DB::raw('sum(rs_student_fees.registration_fees) as registration_fees'),
				DB::raw('sum(rs_student_fees.online_services_fees) as online_services_fees'),
				DB::raw('sum(rs_student_fees.add_sub_fees) as add_sub_fees'),
				DB::raw('sum(rs_student_fees.forward_fees) as forward_fees'),
				DB::raw('sum(rs_student_fees.toc_fees) as toc_fees'),
				DB::raw('sum(rs_student_fees.practical_fees) as practical_fees'),
				DB::raw('sum(rs_student_fees.readm_exam_fees) as readm_exam_fees'),
				DB::raw('sum(rs_student_fees.late_fee) as late_fee'),
				DB::raw('sum(rs_student_fees.total) as total'),
			])
			->Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
			->LeftJoin('students', function($join)
			{
				$join->on('students.user_id', '=', 'users.id')
				->where('students.submitted','!=',null)
				->where('students.challan_tid','!=',null);
				//->where('students.is_eligible',1);

			})
			->LeftJoin('student_fees', 'student_fees.student_id', '=', 'students.id')

			->LeftJoin('applications', function($join)
			{
				$join->on('applications.student_id', '=', 'students.id')
				->where('applications.fee_status', '=', 1)
				->where('applications.locksumbitted', '=', 1);
			})
			->where('model_has_roles.role_id',59)
			->where($conditions)
			->where(@$tempCond[0],@$tempCond[1],@$tempCond[2])
			->groupby("users.id")
			->paginate($defaultPageLimit);

		}else{
			$master = DB::table('users')
				->select([
					'users.college_name','users.ai_code',
					DB::raw('count(rs_students.id) as number_of_student'),
					DB::raw('sum(rs_student_fees.registration_fees) as registration_fees'),
					DB::raw('sum(rs_student_fees.online_services_fees) as online_services_fees'),
					DB::raw('sum(rs_student_fees.add_sub_fees) as add_sub_fees'),
					DB::raw('sum(rs_student_fees.forward_fees) as forward_fees'),
					DB::raw('sum(rs_student_fees.toc_fees) as toc_fees'),
					DB::raw('sum(rs_student_fees.practical_fees) as practical_fees'),
					DB::raw('sum(rs_student_fees.readm_exam_fees) as readm_exam_fees'),
					DB::raw('sum(rs_student_fees.late_fee) as late_fee'),
					DB::raw('sum(rs_student_fees.total) as total'),
				])
				->Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
				->LeftJoin('students', function($join)
				{
					$join->on('students.user_id', '=', 'users.id')
					->where('students.submitted','!=',null)
					->where('students.challan_tid','!=',null);
				})
				->LeftJoin('student_fees', 'student_fees.student_id', '=', 'students.id')
				->LeftJoin('applications', function($join)
				{
					$join->on('applications.student_id', '=', 'students.id')
					->where('applications.fee_status', '=', 1)
					->where('applications.locksumbitted', '=', 1);
				})
				->where('model_has_roles.role_id',59)
				->where($conditions)
				->where(@$tempCond[0],@$tempCond[1],@$tempCond[2])
				->groupby("users.id")
			->get();

		}

		return $master;
	}
	
	public function getStudentFeeSummaryOrg($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_condtions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = DB::table('aicenter_details')
				->select([
					'aicenter_details.college_name','aicenter_details.ai_code',
					DB::raw('count(rs_students.id) as number_of_student'),
					DB::raw('sum(rs_student_fees.registration_fees) as registration_fees'),
					DB::raw('sum(rs_student_fees.online_services_fees) as online_services_fees'),
					DB::raw('sum(rs_student_fees.add_sub_fees) as add_sub_fees'),
					DB::raw('sum(rs_student_fees.forward_fees) as forward_fees'),
					DB::raw('sum(rs_student_fees.toc_fees) as toc_fees'),
					DB::raw('sum(rs_student_fees.practical_fees) as practical_fees'),
					DB::raw('sum(rs_student_fees.readm_exam_fees) as readm_exam_fees'),
					DB::raw('sum(rs_student_fees.late_fee) as late_fee'),
					DB::raw('sum(rs_student_fees.total) as total'),
					DB::raw('sum(rs_student_org_fees.org_registration_fees) as org_registration_fees'),
					DB::raw('sum(rs_student_org_fees.org_online_services_fees) as org_online_services_fees'),
					DB::raw('sum(rs_student_org_fees.org_add_sub_fees) as org_add_sub_fees'),
					DB::raw('sum(rs_student_org_fees.org_forward_fees) as org_forward_fees'),
					DB::raw('sum(rs_student_org_fees.org_toc_fees) as org_toc_fees'),
					DB::raw('sum(rs_student_org_fees.org_practical_fees) as org_practical_fees'),
					DB::raw('sum(rs_student_org_fees.org_readm_exam_fees) as org_readm_exam_fees'),
					DB::raw('sum(rs_student_org_fees.org_late_fee) as org_late_fee'),
					DB::raw('sum(rs_student_org_fees.org_total) as org_total'),
				])
				->LeftJoin('students', function($join)
				{
					$join->on('students.ai_code', '=', 'aicenter_details.ai_code')
					->where('students.submitted','!=',null)
					->where('students.challan_tid','!=',null);
				})
				->LeftJoin('student_fees', 'student_fees.student_id', '=', 'students.id')
				->LeftJoin('student_org_fees', 'student_org_fees.student_id', '=', 'students.id')
				->LeftJoin('applications', function($join)
				{
					$join->on('applications.student_id', '=', 'students.id')
					->where('applications.fee_status', '=', 1)
					->where('applications.locksumbitted', '=', 1);
				})
				->where($conditions)
				->groupby("aicenter_details.ai_code")
				->paginate($defaultPageLimit);


		}else{
			$master = DB::table('aicenter_details')
				->select([
					'aicenter_details.college_name','aicenter_details.ai_code',
					DB::raw('count(rs_students.id) as number_of_student'),
					DB::raw('sum(rs_student_fees.registration_fees) as registration_fees'),
					DB::raw('sum(rs_student_fees.online_services_fees) as online_services_fees'),
					DB::raw('sum(rs_student_fees.add_sub_fees) as add_sub_fees'),
					DB::raw('sum(rs_student_fees.forward_fees) as forward_fees'),
					DB::raw('sum(rs_student_fees.toc_fees) as toc_fees'),
					DB::raw('sum(rs_student_fees.practical_fees) as practical_fees'),
					DB::raw('sum(rs_student_fees.readm_exam_fees) as readm_exam_fees'),
					DB::raw('sum(rs_student_fees.late_fee) as late_fee'),
					DB::raw('sum(rs_student_fees.total) as total'),
					DB::raw('sum(rs_student_org_fees.org_registration_fees) as org_registration_fees'),
					DB::raw('sum(rs_student_org_fees.org_online_services_fees) as org_online_services_fees'),
					DB::raw('sum(rs_student_org_fees.org_add_sub_fees) as org_add_sub_fees'),
					DB::raw('sum(rs_student_org_fees.org_forward_fees) as org_forward_fees'),
					DB::raw('sum(rs_student_org_fees.org_toc_fees) as org_toc_fees'),
					DB::raw('sum(rs_student_org_fees.org_practical_fees) as org_practical_fees'),
					DB::raw('sum(rs_student_org_fees.org_readm_exam_fees) as org_readm_exam_fees'),
					DB::raw('sum(rs_student_org_fees.org_late_fee) as org_late_fee'),
					DB::raw('sum(rs_student_org_fees.org_total) as org_total'),
				])
				->LeftJoin('students', function($join)
				{
					$join->on('students.ai_code', '=', 'aicenter_details.ai_code')
					->where('students.submitted','!=',null)
					->where('students.challan_tid','!=',null);
				})
				->LeftJoin('student_fees', 'student_fees.student_id', '=', 'students.id')
				->LeftJoin('student_org_fees', 'student_org_fees.student_id', '=', 'students.id')
				->LeftJoin('applications', function($join)
				{
					$join->on('applications.student_id', '=', 'students.id')
					->where('applications.fee_status', '=', 1)
					->where('applications.locksumbitted', '=', 1);
				})

				->where($conditions)
				->groupby("aicenter_details.ai_code")
				->get();
		}
		return $master;
	}

	public function checkSuppFeeEntredOrNot($student_id=null,$supp_id=null){
		$res = false;
		$current_exam_month = Config::get('global.supp_current_admission_exam_month');
		$current_exam_year = Config::get('global.form_supp_current_admission_session_id');

		$mainTable = "supplementaries";
		$conditions['id'] = $supp_id;
		$conditions['exam_year'] = $current_exam_year;
		$conditions['exam_month'] = $current_exam_month;
		$suppForCurrentYearCount = DB::table($mainTable)->where($conditions)->orderBy('id','ASC')->count();

		if(@$suppForCurrentYearCount > 0){
			$fee_data_arr = DB::table('supp_student_fees')->where('supplementary_id',$supp_id)->count();


			if( $fee_data_arr > 0 ){
				$res = true;
			}
		}else{
			$res = true;
		}
		return $res;
	}

	public function subjectOrderbyCourseList($course=null){
		$conditions = array();
		$conditions['deleted'] = 0;

		$mainTable = "subjects";
		$result = DB::table($mainTable)->where($conditions)->orderBy('subject_code','ASC')->get()->pluck('name','id');
		return $result;
	}
	
	public function practicalSubjectOrderbyCourseList($course=null){
		$conditions = array();
		$conditions['practical_type'] = 1;
		$conditions['deleted'] = 0;

		$mainTable = "subjects";
		$result = DB::table($mainTable)->where($conditions)->orderBy('subject_code','ASC')->get()->pluck('name','id');
		return $result;
	}
	
	public function getExamDate($stream=null,$subject=null){
		$conditionArray = array();
		$conditionArray['exam_year'] = Config::get('global.current_admission_session_id');
		$conditionArray['stream'] = $stream;
		$conditionArray['subjects'] = $subject;

		$master = TimeTable::where($conditionArray)->first();
		if(!empty($master->exam_date)){
			return date("d/m/Y",strtotime(@$master->exam_date));
		}
	}
	
	public function getSuppSubjectbySuppId($supplementary_id=null){
		$conditions = array();
		$conditions['supplementary_id'] = $supplementary_id;
		$conditions['status'] = 1;

		$mainTable = "supplementary_subjects";
		$result = DB::table($mainTable)->where($conditions)->orderBy('id','ASC')->get()->pluck('id','subject_id');
		return $result;
	}

	public function getOrgSubjectIdByStudentIdSupplementaryIdSubjectId($student_id=null,$supplementary_id=null,$subject_id=null){
		$conditions = array();
		$conditions['supplementary_id'] = $supplementary_id;
		$conditions['subject_id'] = $subject_id;
		$conditions['student_id'] = $student_id;
		$conditions['status'] = 1;

		$mainTable = "supplementary_subjects";
		$result = DB::table($mainTable)->where($conditions)->get()->pluck('id','origional_subject_id');
		if(!empty($result)){
		}
		return $result;
	}

	public function resultList($combo_name=null){
		$condtions = null;
		$result = array();
		if($combo_name!=null){
			$condtions = ['combo_name' => $combo_name,'status' => 1];
		} else{
			$condtions = ['status' => 1];
		}

		$mainTable = "masters";
		$cacheName = "results_". $combo_name;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
				return $result = DB::table($mainTable)->where($condtions)->get()->pluck('option_val','option_id');

			});
		}
		return $result;
	}

	public function getSSOIDDetials($sso_id=null){
		$validSSO =  time() . $sso_id;
		if(!empty($sso_id)){
			$SSO_API_USERNAME = config("global.SSO_API_USERNAME");
			$SSO_API_PASSWORD = config("global.SSO_API_PASSWORD");

			// $url =  SSO_API_URL . 'GetUserDetailJSON/' . $sso_id  . '/' . SSO_API_USERNAME . '/' . SSO_API_PASSWORD;
			$url =  'http://sso.rajasthan.gov.in:8888/SSOREST/'. 'GetUserDetailJSON/' . $sso_id  . '/' . $SSO_API_USERNAME . '/' . $SSO_API_PASSWORD;

			$output = $this->_CallSSO($url);
			return $output;
		}
		return false;
	}

	public function _CallSSO( $url, $data = false, $method =''){
		$curl = curl_init();
		$url =  $url;
		switch ($method){
			case "POST":
				curl_setopt($curl, CURLOPT_POST, 1);

				if ($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				break;
			case "PUT":
				curl_setopt($curl, CURLOPT_PUT, 1);
				break;
			default:
				if ($data)
					$url = sprintf("%s?%s", $url, http_build_query($data));
		}

		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);


		$result = curl_exec($curl);
		curl_error($curl);

		if($result === false){
			echo 'Curl error: ' . curl_error($curl);die;
		}
		else {
			//echo 'Operation completed without any errors';
		}

		curl_close($curl);
		return $result;
	}

	public function getStudentCodeAllotment($ai_code=null,$stream=null,$course=null,$limit=null){
		$exam_year =  config("global.current_admission_session_id");
		$exam_month =  config("global.current_exam_month_id");

		if($stream == 1){
			if($course == 10)
				$start = 2000;
			if($course == 12)
				$start = 3000;
		}
		if($stream == 2){
			if($course == 10)
				$start = 4000;
			if($course == 12)
				$start = 5000;
		}

		$field = 'student_code_to_'.$course;
		$conditionArray = array();
		$conditionArray['exam_year'] = Config::get('global.current_admission_session_id');
		$conditionArray['stream'] = $stream;
		$conditionArray['ai_code'] = $ai_code;

		$stream_allotted = CenterAllotment::select($field)->where($conditionArray)->orderBy($field, 'DESC')->first();

		if(!empty($stream_allotted)){
			$start = $stream_allotted[$field];
		}

		//$academicyear_id = config("global.default_academicyear_id");
		$studentconditions = array();

		$studentconditions['students.course'] = $course;
		$studentconditions['students.is_eligible'] = 1;
		$studentconditions['students.exam_year'] = $exam_year;
		$studentconditions['students.stream'] = $stream;
		$studentconditions['students.ai_code'] = $ai_code;
		// $studentconditions['students.student_code'] = ' > '.$start;


		$student_start_code = Student::select('student_code')->where($studentconditions)->where('students.student_code', '>', $start)->orderBy('student_code','ASC')->first();
		if(!empty($student_start_code['student_code'])){
			$student_start_code = @$student_start_code['student_code'];
		} else {
			$student_start_code = 0;
		}


		/*$student_end_code = DB::table('students')->select('student_code')->where($studentconditions)->where('students.student_code', '>', $start)->orderBy('student_code','ASC')->whereNull('deleted_at')->limit($limit)->get();
		$student_end_code = $student_end_code->toArray();

		if(isset($student_end_code[$limit-1]->student_code) && !empty($student_end_code[$limit-1]->student_code)){

			$student_end_code = @$student_end_code[$limit-1]->student_code;
		} else {
			$student_end_code = 0;
		}
		*/
		$student_end_code = Student::select('student_code')->where($studentconditions)->where('students.student_code', '>', $start)->orderBy('student_code','ASC')->limit($limit)->get();

			if(isset($student_end_code[$limit-1]['student_code']) && !empty($student_end_code[$limit-1]['student_code'])){
			$student_end_code = @$student_end_code[$limit-1]['student_code'];
		} else {
			$student_end_code = 0;
		}


		//$student_end_code = Student::select('student_code')->where($studentconditions)->where('students.student_code', '>', $start)->orderBy('student_code','DESC')->first();

		//if(!empty($student_end_code['student_code'])){
			//$student_end_code = @$student_end_code['student_code'];
		//} else {
			//$student_end_code = 0;
		//}




		//echo "<pre>";
		//print_r($student_start_code);
		//echo "<br>test<br>";
		//print_r($student_end_code);
		//die;

		// echo $student_start_code."-".$student_end_code; die;
		return $student_start_code."-".$student_end_code;

	}

	public function _getTPSubjects($course=null,$practical_type=null){
		$conditions = null;
		$result = array();

		if(@$practical_type){
			if($practical_type == 'T'){
			}else if($practical_type == 'P'){
				$practical_type = 1;
				$conditions['practical_type'] = $practical_type;
			}

		}
		if(@$course){
			$conditions['course'] = $course;
		}

		$mainTable = "subjects";
		$cacheName = "subjects_tp_u_u_aa_" . $practical_type . "_" .$course;

		Cache::forget($cacheName);
		if (Cache::has($cacheName)) { //
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($conditions, $mainTable) {
				return $result = DB::table($mainTable)
							->where($conditions)->whereNull('deleted_at')
							->orderBy('subject_code','ASC')
							->get()->pluck('name','id');

			});
		}
		return $result;
	}

	public function _getTPSubjectscode($subjectcode=null){
		$conditions = null;
		$result = array();

		if(@$subjectcode){
			$conditions['subject_code'] = $subjectcode;
		}

		$mainTable = "subjects";
		$cacheName = "subjects";

		Cache::forget($cacheName);
		if (Cache::has($cacheName)) { //
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($conditions, $mainTable) {
				return $result = DB::table($mainTable)
							->where($conditions)->whereNull('deleted_at')
							->orderBy('subject_code','ASC')
							->get()->pluck('subject_code','id');

			});
		}
		return $result;
	}

	public function _getTPSubjectsname($subjectcode=null){
		$conditions = null;
		$result = array();


		if(@$subjectcode){
			$conditions['subject_code'] = $subjectcode;
		}


		$mainTable = "subjects";
		$cacheName = "subjects";

		Cache::forget($cacheName);
		if (Cache::has($cacheName)) { //
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($conditions, $mainTable) {
				return $result = DB::table($mainTable)
							->where($conditions)
							->orderBy('subject_code','ASC')
							->get()->pluck('name','id');

			});
		}
		return $result;
	}

	public function getExamCentersDropdown(){
		$role_id=Config::get('global.theoryexaminer');
		$user_role_id=@session::get("role_id");
		$user_id=@auth::user()->id;
		$exam_center_list=array();
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		// $exam_year=Config::get('global.current_theory_session_id');
		// $exam_month=Config::get('global.current_thoery_exam_month');
		$condition2=['marking_absent_students.exam_year'=>$exam_year,'marking_absent_students.exam_session'=>$exam_month];
		if($user_role_id==$role_id){
            $exam_center_list=MarkingAbsentStudent::join('alloting_copies_examiners','alloting_copies_examiners.marking_absent_student_id','=','marking_absent_students.id')
			->join('examcenter_details','examcenter_details.id','=','marking_absent_students.examcenter_detail_id')
			->where($condition2)
			->where('alloting_copies_examiners.user_id','=',$user_id)
			->whereNull('alloting_copies_examiners.deleted_at')
			->whereNull('marking_absent_students.deleted_at')
			->whereNull('examcenter_details.deleted_at')
			->select(DB::raw("CONCAT(ecenter10, '/',ecenter12) AS full_center_name"),'examcenter_details.id')
			->pluck('full_center_name', 'examcenter_details.id');
			return  $exam_center_list;
		}else{
			$conditions2=['student_allotments.exam_year'=>$exam_year,'student_allotments.exam_month'=>$exam_month];
			$coditions=['student_allotments.examcenter_detail_id' =>'examcenter_details.id'];
			$exam_center_list=ExamcenterDetail::join('student_allotments',$coditions)->where($conditions2)
			->whereNotNull('examcenter_details.fixcode')->whereNull('student_allotments.deleted_at')
			->whereNull('examcenter_details.deleted_at')->where('examcenter_details.fixcode', "!=", "")
			->orderBy('examcenter_details.fixcode')->groupBy('student_allotments.examcenter_detail_id')
			->select(DB::raw("CONCAT(ecenter10, '/',ecenter12) AS full_center_name"),'examcenter_details.id')
			->pluck('full_center_name', 'examcenter_details.id');
			return $exam_center_list;
	    }
	}

	public function getEligibleStudentOfExamYearAndExamMonths(){
		$conditions = array();
		$exam_year = CustomHelper::_get_selected_sessions();
		$exam_month = Config::get('global.current_exam_month_id');
		$conditions['exam_year'] = $exam_year;
		$conditions['exam_month'] = $exam_month;
		$conditions['is_eligible'] = 1;
		$total_eligible_students = Student::where($conditions)
									->limit(1000)
									->pluck('id','id');

		return $total_eligible_students;
    }

	public function getEligibleSuppStudentOfExamYearAndExamMonths(){
		$conditions = array();
		$exam_year = CustomHelper::_get_selected_sessions();
		$exam_month = Config::get('global.current_exam_month_id');
		$conditions['exam_year'] = $exam_year;
		$conditions['exam_month'] = $exam_month;

		$total_eligible_students = Supplementary::where($conditions)
									->where("supplementaries.challan_tid", "!=", NULL)
									->limit(1000)
									->pluck('student_id','student_id');
		return $total_eligible_students;
    }
	
	public function checkDuplicateAllotment($centerallotmentid=null,$fld=null){
		$stream_global = config("global.CenterAllotmentStreamId");
		$exam_year =  config("global.current_admission_session_id");
		$exam_month =  config("global.current_exam_month_id");

		$status = true;
		$conditions = array();
		$conditions['id'] = $centerallotmentid;
		$conditions[$fld] = 1;
		$conditions['exam_year'] = $exam_year;
		$conditions['exam_month'] = $exam_month;
		$conditions['stream'] = $stream_global;
		$already_entered_status = CenterAllotment::where($conditions)->count();
		if($already_entered_status!=0){
			$status = false;
		}
		return $status;
	}
	
	public function getAiCenters12($currentUserAiCode=null){
		$user_role = Session::get('role_id');
		$aiCenterRoleId = $this->_getRoleIdByName("Aicenter");
		if(@$currentUserAiCode){
			$conditions = array(
				'users.active' => 1,
				'users.ai_code' => $currentUserAiCode,
				'model_has_roles.role_id' => $aiCenterRoleId
			);
		} else {
			$conditions = array(
				'users.active' => 1,
				'model_has_roles.role_id' => $aiCenterRoleId
			);
		}

		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		$master = ModelHasRole::join('users', 'model_has_roles.model_id', '=', 'users.id')
			->select(
            	DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,'')) AS college_name"),'ai_code')
			 	->where($conditions)
			 	->whereNull('users.deleted_at')
				->orderByRaw($queryOrder)->limit(25)
				->pluck('college_name', 'ai_code');

		return $master;
	}
	
	// Captcha function 

	public function getmasteralldata($formId=null,$isPaginate=true,$combo_nameconditions=null){
		$conditions = Session::get($formId. '_conditions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");

			if(!empty($combo_nameconditions)){
				$master = DB::table('masters')->where($conditions)->whereIn('combo_name',$combo_nameconditions)->orderBy("id","DESC")->paginate($defaultPageLimit);
			}else{
				$master = DB::table('masters')->where($conditions)->orderBy("id","DESC")->paginate($defaultPageLimit);
			}

		}else{
			$master = DB::table('rs_masters')->groupBy('combo_name')->get();
		}

		return $master;
	}

	public function checkAllowProvisionResult($course=null){
		if(@$course){
			$resultshow = DB::table('masters')->where('combo_name','result_show')->where('option_id', $course)->where('option_val',1)->count();
		}else{
			$resultshow = DB::table('masters')->where('combo_name','result_show')->where('option_val',1)->count();
		}
		return $resultshow;
	}
	// captcha function
	
	
	// Bulk Download Exam Documents (marksheet,certificate,str)

	public function getresultstudentdata($enrollment=null,$dob=null){
        $dob = date("Y-m-d",strtotime($dob));
	  	$exam_year = config("global.current_result_session_year_id");
		$exam_month = config("global.current_result_session_month_id");
		$result = DB::select('call getStudentResult(?,?,?,?)',array($enrollment,$dob,$exam_year,$exam_month));
		if(@$result[0]){
			return $result[0];
		}
		return false;
	}
	
	public function getresultstudentalldata($students_id=null){
		$exam_year = config("global.current_result_session_year_id");
		$exam_month = config("global.current_result_session_month_id");
		$result = DB::select('call getStudentResultSubject(?,?,?)',array($students_id,$exam_year,$exam_month));
		if(@$result[0]){
			return $result;
		}
		return false;
	}
	
	public function barcode($text=null,$url=null) {
		// $text =  @enrollment
		$filepath = public_path('barcode/enrollment/'.$text.'.png');
		$size = 50;
		$orientation = "horizontal";
		$code_type = "code128";
		$print = false;
		$SizeFactor = 1;

		$code_string = "";
		// Translate the $text into barcode the correct $code_type
		if ( in_array(strtolower($code_type), array("code128", "code128b")) ) {
			$chksum = 104;
			// Must not change order of array elements as the checksum depends on the array's key to validate final code
			$code_array = array(" "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213","*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222","0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122",":"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121","A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133","K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311","U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","["=>"332111","\\"=>"314111","]"=>"221411","^"=>"431111","_"=>"111224","\`"=>"111422","a"=>"121124","b"=>"121421","c"=>"141122","d"=>"141221","e"=>"112214","f"=>"112412","g"=>"122114","h"=>"122411","i"=>"142112","j"=>"142211","k"=>"241211","l"=>"221114","m"=>"413111","n"=>"241112","o"=>"134111","p"=>"111242","q"=>"121142","r"=>"121241","s"=>"114212","t"=>"124112","u"=>"124211","v"=>"411212","w"=>"421112","x"=>"421211","y"=>"212141","z"=>"214121","{"=>"412121","|"=>"111143","}"=>"111341","~"=>"131141","DEL"=>"114113","FNC 3"=>"114311","FNC 2"=>"411113","SHIFT"=>"411311","CODE C"=>"113141","FNC 4"=>"114131","CODE A"=>"311141","FNC 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232","Stop"=>"2331112");
			$code_keys = array_keys($code_array);
			$code_values = array_flip($code_keys);
			for ( $X = 1; $X <= strlen($text); $X++ ) {
				$activeKey = substr( $text, ($X-1), 1);
				$code_string .= $code_array[$activeKey];
				$chksum=($chksum + ($code_values[$activeKey] * $X));
			}
			$code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

			$code_string = "211214" . $code_string . "2331112";
		} elseif ( strtolower($code_type) == "code128a" ) {
			$chksum = 103;
			$text = strtoupper($text); // Code 128A doesn't support lower case
			// Must not change order of array elements as the checksum depends on the array's key to validate final code
			$code_array = array(" "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213","*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222","0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122",":"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121","A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133","K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311","U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","["=>"332111","\\"=>"314111","]"=>"221411","^"=>"431111","_"=>"111224","NUL"=>"111422","SOH"=>"121124","STX"=>"121421","ETX"=>"141122","EOT"=>"141221","ENQ"=>"112214","ACK"=>"112412","BEL"=>"122114","BS"=>"122411","HT"=>"142112","LF"=>"142211","VT"=>"241211","FF"=>"221114","CR"=>"413111","SO"=>"241112","SI"=>"134111","DLE"=>"111242","DC1"=>"121142","DC2"=>"121241","DC3"=>"114212","DC4"=>"124112","NAK"=>"124211","SYN"=>"411212","ETB"=>"421112","CAN"=>"421211","EM"=>"212141","SUB"=>"214121","ESC"=>"412121","FS"=>"111143","GS"=>"111341","RS"=>"131141","US"=>"114113","FNC 3"=>"114311","FNC 2"=>"411113","SHIFT"=>"411311","CODE C"=>"113141","CODE B"=>"114131","FNC 4"=>"311141","FNC 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232","Stop"=>"2331112");
			$code_keys = array_keys($code_array);
			$code_values = array_flip($code_keys);
			for ( $X = 1; $X <= strlen($text); $X++ ) {
				$activeKey = substr( $text, ($X-1), 1);
				$code_string .= $code_array[$activeKey];
				$chksum=($chksum + ($code_values[$activeKey] * $X));
			}
			$code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

			$code_string = "211412" . $code_string . "2331112";
		} elseif ( strtolower($code_type) == "code39" ) {
			$code_array = array("0"=>"111221211","1"=>"211211112","2"=>"112211112","3"=>"212211111","4"=>"111221112","5"=>"211221111","6"=>"112221111","7"=>"111211212","8"=>"211211211","9"=>"112211211","A"=>"211112112","B"=>"112112112","C"=>"212112111","D"=>"111122112","E"=>"211122111","F"=>"112122111","G"=>"111112212","H"=>"211112211","I"=>"112112211","J"=>"111122211","K"=>"211111122","L"=>"112111122","M"=>"212111121","N"=>"111121122","O"=>"211121121","P"=>"112121121","Q"=>"111111222","R"=>"211111221","S"=>"112111221","T"=>"111121221","U"=>"221111112","V"=>"122111112","W"=>"222111111","X"=>"121121112","Y"=>"221121111","Z"=>"122121111","-"=>"121111212","."=>"221111211"," "=>"122111211","$"=>"121212111","/"=>"121211121","+"=>"121112121","%"=>"111212121","*"=>"121121211");

			// Convert to uppercase
			$upper_text = strtoupper($text);

			for ( $X = 1; $X<=strlen($upper_text); $X++ ) {
				$code_string .= $code_array[substr( $upper_text, ($X-1), 1)] . "1";
			}

			$code_string = "1211212111" . $code_string . "121121211";
		} elseif ( strtolower($code_type) == "code25" ) {
			$code_array1 = array("1","2","3","4","5","6","7","8","9","0");
			$code_array2 = array("3-1-1-1-3","1-3-1-1-3","3-3-1-1-1","1-1-3-1-3","3-1-3-1-1","1-3-3-1-1","1-1-1-3-3","3-1-1-3-1","1-3-1-3-1","1-1-3-3-1");

			for ( $X = 1; $X <= strlen($text); $X++ ) {
				for ( $Y = 0; $Y < count($code_array1); $Y++ ) {
					if ( substr($text, ($X-1), 1) == $code_array1[$Y] )
						$temp[$X] = $code_array2[$Y];
				}
			}

			for ( $X=1; $X<=strlen($text); $X+=2 ) {
				if ( isset($temp[$X]) && isset($temp[($X + 1)]) ) {
					$temp1 = explode( "-", $temp[$X] );
					$temp2 = explode( "-", $temp[($X + 1)] );
					for ( $Y = 0; $Y < count($temp1); $Y++ )
						$code_string .= $temp1[$Y] . $temp2[$Y];
				}
			}

			$code_string = "1111" . $code_string . "311";
		} elseif ( strtolower($code_type) == "codabar" ) {
			$code_array1 = array("1","2","3","4","5","6","7","8","9","0","-","$",":","/",".","+","A","B","C","D");
			$code_array2 = array("1111221","1112112","2211111","1121121","2111121","1211112","1211211","1221111","2112111","1111122","1112211","1122111","2111212","2121112","2121211","1121212","1122121","1212112","1112122","1112221");

			// Convert to uppercase
			$upper_text = strtoupper($text);

			for ( $X = 1; $X<=strlen($upper_text); $X++ ) {
				for ( $Y = 0; $Y<count($code_array1); $Y++ ) {
					if ( substr($upper_text, ($X-1), 1) == $code_array1[$Y] )
						$code_string .= $code_array2[$Y] . "1";
				}
			}
			$code_string = "11221211" . $code_string . "1122121";
		}

		// Pad the edges of the barcode
		$code_length = 20;
		if ($print) {
			$text_height = 30;
		} else {
			$text_height = 0;
		}

		for ( $i=1; $i <= strlen($code_string); $i++ ){
			$code_length = $code_length + (integer)(substr($code_string,($i-1),1));
			}

		if ( strtolower($orientation) == "horizontal" ) {
			$img_width = $code_length*$SizeFactor;
			$img_height = $size;
		} else {
			$img_width = $size;
			$img_height = $code_length*$SizeFactor;
		}

		$image = imagecreate($img_width, $img_height + $text_height);
		$black = imagecolorallocate ($image, 0, 0, 0);
		$white = imagecolorallocate ($image, 255, 255, 255);

		imagefill( $image, 0, 0, $white );
		if ( $print ) {
			imagestring($image, 5, 31, $img_height, $text, $black );
		}

		$location = 10;
		for ( $position = 1 ; $position <= strlen($code_string); $position++ ) {
			$cur_size = $location + ( substr($code_string, ($position-1), 1) );
			if ( strtolower($orientation) == "horizontal" )
				imagefilledrectangle( $image, $location*$SizeFactor, 0, $cur_size*$SizeFactor, $img_height, ($position % 2 == 0 ? $white : $black) );
			else
				imagefilledrectangle( $image, 0, $location*$SizeFactor, $img_width, $cur_size*$SizeFactor, ($position % 2 == 0 ? $white : $black) );
			$location = $cur_size;
		}

		// Draw barcode to the screen or save in a file
		if ( $filepath=="" ) {
			header ('Content-type: image/png');
			imagepng($image);
			imagedestroy($image);
		} else {
			imagepng($image,$filepath);
			imagedestroy($image);
		}
	}
	// Bulk Download Exam Documents (marksheet,certificate,str)
	
	public function generateCaptcha($startDigit=1,$endDigit=20,$width=150,$height=30) {
		$num1 = rand($startDigit,$endDigit); //Generate First number between 1 and 99
		$num2 = rand($startDigit,$endDigit); //Generate Second number between 1 and 99
		$captchaValue = $num1 + $num2;
		Session::put('captcha', $captchaValue);

		$image = imagecreatetruecolor(150, 30); //Change the numbers to adjust the size of the image
		$black = imagecolorallocate($image, 0, 0, 0);
		$color = imagecolorallocate($image, 255, 255, 255);
		$white = imagecolorallocate($image, 0, 26, 26);
		
		
		 // Background color: Light gradient
		$bgColor1 = imagecolorallocate($image, 255, 204, 204); // Light Pink
		$bgColor2 = imagecolorallocate($image, 255, 255, 204); // Light Yellow
		imagefill($image, 0, 0, $bgColor2);
		$gradient = imagecolorallocate($image, 66, 204, 224);
		imagefilledrectangle($image, 0, 0, $width, $height, $gradient);
		 

		$mathString = $num1." + ". $num2 ." = ?";
		$font = public_path('arial1.ttf');
		// imagettftext($image, 20, 0, 0, 25, $color, $font, $mathString );//Change the numbers to adjust the font-size
		
		imagestring($image, 20, 0, 0, $mathString, $black); // The '5' here represents the built-in font size (1 to 5, larger means bigger text)


		header("Content-type: image/png");
		imagepng($image);
		$imgData=ob_get_clean();
		imagedestroy($image);
		$captchaImage = '<img src="data:image/png;base64,'.base64_encode($imgData).'" />';
		return $captchaImage;
		die;
	}
	
	public function checkCaptcha($inputs) {
		$result = true;
		$captcha = Session::get('captcha');
		// @dd($captcha); @dd($inputs['captcha']); die;
		if(isset($captcha) && isset($inputs['captcha']) && $captcha!=$inputs['captcha']){
			$result = false;
		}
		return $result;
	}
	
	public function downloadCustomSingleMarksheetPdf($enrollment=null,$marksheet_type='pass'){
		$marksheet_component_obj = new MarksheetCustomComponent;
		$current_admission_session_id = Config::get('global.current_admission_session_id');
		$exam_month = Config::get('global.current_exam_month_id');
		$pastdataurl=config("global.pastdata_document");
		$pastdatadocument=config("global.PAST_DATA_DOCUMENT");
		$title = "Single Marksheet";
        $table_id = "Single Marksheet";
        $formId = ucfirst(str_replace(" ","-",$title));
		$studentdata=Student::where('enrollment',$enrollment)->first(['id','enrollment']);
		$documents = '';
        $totalMarks = 0;
		$grandFinalTotalMarks = 0;

		if($enrollment == null || $enrollment == ''){
			return false;
			return redirect()->route('downloadBulkDocument')->with('message',"Enrollment is not in correct format.");
		}

		$serial_number = $marksheet_component_obj->getSerialNumber(@$enrollment);

		$examresultfields = array('final_result','exam_month','exam_year','total_marks','percent_marks','additional_subjects','is_temp_examresult');
        $final_result = ExamResult::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first($examresultfields);
		if($final_result->is_temp_examresult == "111"){
			return false;
		}

		if(empty($final_result)){
			return false;
		}
		$pastInfo = Pastdata::where('ENROLLNO','=',$enrollment)->orderBy('id','DESC')->first();
		if(!empty($pastInfo) && !empty($final_result)){
			$field=["pastdatas.ENROLLNO  as enrollment","pastdatas.CLASS  as course","pastdatas.NAME as name" ,"pastdatas.FNAME as father_name","pastdatas.MNAME as mother_name", "pastdatas.DOB as dob"];
			$student = Pastdata::where('ENROLLNO','=',$enrollment)->orderBy('id','DESC')->first($field);
		}else{
		    $student = Student::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first();
		}
		$combination = '';
		$resultDate = '';
		if(isset($final_result->exam_month) && isset($final_result->exam_year)){
			$combination = $final_result->exam_month . ' '. $final_result->exam_year;
		}
		$courseVal = '';
        $resultsyntax = array('999'=>'AB','666'=>'SYCP','777'=>'SYCT','888'=>'SYC','P'=>'P');

		if(!empty($student)){
			$application = Application::where('student_id','=',@$student->id)->orderBy('id','DESC')->first();

			$student->display_exam_month_year = $marksheet_component_obj->getDisplayExamMonthYear($combination);
			$newexamresultfields = array('exam_year','exam_month','result_date');
			$final_result_data = ExamResult::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first($newexamresultfields);
			if(isset($final_result_data->result_date) && $final_result_data->result_date != ""){
				 $resultDate = date("d-m-Y", strtotime(@$final_result_data->result_date));
				//$dtarr = explode('-',@$final_result_data->result_date);
				//$resultDate = $dtarr[2]."-".$dtarr[1]."-".$dtarr[0];


			}

			$documents = Document::where('student_id','=',$student->id)->orderBy('id','DESC')->first();
			$courseVal = $student->course;

			$address = Address::where('student_id','=',$student->id)->orderBy('id','DESC')->first();

			$findInFlag = 'Student';
			$dtarr = explode('-',$student->dob);
			$student->dob = $dtarr[2]."-".$dtarr[1]."-".$dtarr[0];
		} else {
            if(empty($final_result)){
				$courseVal = $pastInfo->CLASS;
				$subjectCodeIds = $marksheet_component_obj->_getSubjectsCodeId($courseVal);

				$final_result->final_result = @$pastInfo->RESULT;
				$final_result->total_marks = @$pastInfo->TOTAL_MARK;
				$final_result->percent_marks = @$pastInfo->Percentage;

				$dtarr = explode('-',@$pastInfo->ResultDate);
				$resultDate = $dtarr[2]."-".$dtarr[1]."-".$dtarr[0];

				if(isset($combination) && $combination != ''){
					$student->display_exam_month_year = $marksheet_component_obj->getDisplayExamMonthYear($combination);
				}
			} else {
				// $stuexam=ExamSubject::where('student_id',@$studentdata->id)->latest('exam_year')->first(['exam_year','exam_month']);

				if(@$final_result->exam_year){
					$examSubjectsMarksData = $marksheet_component_obj->getallexamsubjectsdata($enrollment,$final_result->exam_year,$final_result->exam_month);

				}else{
					$examSubjectsMarksData = array();
				}

                $newexamresultfields = array('exam_year','exam_month','result_date');
			    $final_result_data = ExamResult::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first($examresultfields);

                //$newexamresultfields = array('exam_year','exam_month','result_date');
			    //$final_result_data = ExamResult::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first($examresultfields);

				if(isset($final_result_data->result_date) && $final_result_data->result_date != ""){
					$resultDate = $final_result_data->result_date;
				}
				$student->display_exam_month_year = $marksheet_component_obj->getDisplayExamMonthYear($combination);
			}

			$student->ai_code = substr($pastInfo->ENROLLNO,0,5);
			$ai_code = $student->ai_code;
			$student->enrollment = $pastInfo->ENROLLNO;
			$student->name = $pastInfo->NAME;
			$student->father_name = $pastInfo->FNAME;
			$student->mother_name = $pastInfo->MNAME;
			$pastInfo->DOB = $new_date = $pastInfo->DOB;//date('d/m/Y', strtotime($pastInfo['Pastdata']['DOB']));
			$student->dob = $pastInfo->DOB;
			@$dtarr = explode('-',$student['dob']);
			@$student->dob = @$dtarr[2]."-".@$dtarr[1]."-".@$dtarr[0];

			@$student->course = $pastInfo->CLASS;
			@$student->yy = $yy = substr($pastInfo->ENROLLNO,5,2);
			@$student->student_code =  $st_code = substr($pastInfo->ENROLLNO,7);
			@$courseVal = $pastInfo->CLASS;
			@$student->display_exam_month_year=$pastInfo->EX_YR;
			@$addressTemp = $pastInfo->ADDRESS;
			if(isset($pastInfo->DISTRICT) && !empty($pastInfo->DISTRICT)){
				@$addressTemp .= ','.$pastInfo->DISTRICT;
			}
			if(isset($pastInfo->STATE) && !empty($pastInfo->STATE)){
				@$addressTemp .= ','.$pastInfo->STATE;
			}
			if(isset($pastInfo->PIN) && !empty($pastInfo->PIN)){
				@$addressTemp .= '-'.$pastInfo->PIN;
			}
			@$address->address1 = $addressTemp;
			@$address->address2 = '';
			@$address->address3 = '';
			@$address->city_name = '';
			@$address->pincode = '';

			if(isset($pastInfo->MOBILE) && !empty($pastInfo->MOBILE)){
				@$student->mobile = $pastInfo->MOBILE;
			}
			if($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'GENERAL_ADM' || 	$pastInfo->ERTYPE == 'STREAM2'){
				$student->adm_type = 1;
			}else if($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'READMISSION'){
				$student['adm_type'] = 2;
			}else if($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'PARTADMISSION'){
				$student->adm_type = 3;
			}else if($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'IMPROVEMENT'){
				$student->adm_type = 4;
			}else if($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'SUPPLEMENTARY'){
				$student->adm_type = 1;	//11 for supplementary ertype and admission type
			}else{ //if ertype in pastdata table is balnk or null then use gen_adm adm_type
				$student->adm_type = 1;
			}
		}

		$sub_enrollment = substr($enrollment,-6,2);
		if($sub_enrollment >= '17'){
			//$stuexam1=ExamSubject::where('student_id',@$studentdata->id)->orderBy('exam_year', 'DESC')->orderBy('exam_month','DESC')->first(['exam_year','exam_month']);
			$examSubjectsMarksData =$marksheet_component_obj->getallexamsubjectsdata($enrollment,$final_result->exam_year,$final_result->exam_month);

		}else{
			$examsubjectfields = array('id', 'subject_id','final_theory_marks','final_practical_marks','sessional_marks_reil_result','total_marks','final_result');
			$examSubjectsMarksData = ExamSubject::where('enrollment','=',$enrollment)->orderBy('subject_id','desc')->get($examsubjectfields);
		}
		$examsubjectcount=count($examSubjectsMarksData);
		$subjectCodeIds = $marksheet_component_obj->_getSubjectsCodeId($courseVal);
		if(($examsubjectcount == 0)){
			$examSubjectsMarksData=array();
			if(isset($pastInfo->FRES1) && $pastInfo->FRES1 != ''){
				$examSubjectsMarksData[0]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB1];
				$examSubjectsMarksData[0]['final_theory_marks'] = $pastInfo->FTM1;
				$examSubjectsMarksData[0]['final_practical_marks'] = $pastInfo->FPM1;
				$examSubjectsMarksData[0]['sessional_marks_reil_result'] = $pastInfo->fst1;
				$examSubjectsMarksData[0]['total_marks'] = $pastInfo->FTOT1;
				$examSubjectsMarksData[0]['final_result'] = $pastInfo->FRES1;
				$examSubjectsMarksData[0]['max_marks'] = $marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB1]);
				$examSubjectsMarksData[0]['num_words'] = $marksheet_component_obj->numberInWord($examSubjectsMarksData[0]['total_marks']);
				$examSubjectsMarksData[0]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[0]['total_marks']);
			}

            if(isset($pastInfo->FRES2) && $pastInfo->FRES2 != '' ){
				$examSubjectsMarksData[1]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB2];
				$examSubjectsMarksData[1]['final_theory_marks'] = $pastInfo->FTM2;
				$examSubjectsMarksData[1]['final_practical_marks'] = $pastInfo->FPM2;
				$examSubjectsMarksData[1]['sessional_marks_reil_result'] = $pastInfo->fst2;
				$examSubjectsMarksData[1]['total_marks'] = $pastInfo->FTOT2;
				$examSubjectsMarksData[1]['final_result'] = $pastInfo->FRES2;
				$examSubjectsMarksData[1]['max_marks'] =  $marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB2]);
				$examSubjectsMarksData[1]['num_words'] = $marksheet_component_obj->numberInWord($examSubjectsMarksData[1]['total_marks']);
				$examSubjectsMarksData[1]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[1]['total_marks']);
			}

			if(isset($pastInfo->FRES3) && $pastInfo->FRES3 != ''){
				$examSubjectsMarksData[2]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB3];
				$examSubjectsMarksData[2]['final_theory_marks'] = $pastInfo->FTM3;
				$examSubjectsMarksData[2]['final_practical_marks'] = $pastInfo->FPM3;
				$examSubjectsMarksData[2]['sessional_marks_reil_result'] = $pastInfo->fst3;
				$examSubjectsMarksData[2]['total_marks'] = $pastInfo->FTOT3;
				$examSubjectsMarksData[2]['final_result'] = $pastInfo->FRES3;
				$examSubjectsMarksData[2]['max_marks'] = $marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB3]);
				$examSubjectsMarksData[2]['num_words'] = $marksheet_component_obj->numberInWord($examSubjectsMarksData[2]['total_marks']);
				$examSubjectsMarksData[2]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[2]['total_marks']);
			}

			if(isset($pastInfo->FRES4) && $pastInfo->FRES4 != ''){
				$examSubjectsMarksData[3]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB4];
				$examSubjectsMarksData[3]['final_theory_marks'] = $pastInfo->FTM4;
				$examSubjectsMarksData[3]['final_practical_marks'] = $pastInfo->FPM4;
				$examSubjectsMarksData[3]['sessional_marks_reil_result'] = $pastInfo->fst4;
				$examSubjectsMarksData[3]['total_marks'] = $pastInfo->FTOT4;
				$examSubjectsMarksData[3]['final_result'] = $pastInfo->FRES4;
				$examSubjectsMarksData[3]['max_marks'] = $marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB4]);
				$examSubjectsMarksData[3]['num_words'] = $marksheet_component_obj->numberInWord($examSubjectsMarksData[3]['total_marks']);
				$examSubjectsMarksData[3]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[3]['total_marks']);
			}

			if(isset($pastInfo->FRES5) && $pastInfo->FRES5 != ''){
				$examSubjectsMarksData[4]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB5];
				$examSubjectsMarksData[4]['final_theory_marks'] = $pastInfo->FTM5;
				$examSubjectsMarksData[4]['final_practical_marks'] = $pastInfo->FPM5;
				$examSubjectsMarksData[4]['sessional_marks_reil_result'] = $pastInfo->fst5;
				$examSubjectsMarksData[4]['total_marks'] = $pastInfo->FTOT5;
				$examSubjectsMarksData[4]['final_result'] = $pastInfo->FRES5;
				$examSubjectsMarksData[4]['max_marks'] = $marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB5]);
				$examSubjectsMarksData[4]['num_words'] = $marksheet_component_obj->numberInWord($examSubjectsMarksData[4]['total_marks']);
				$examSubjectsMarksData[4]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[4]['total_marks']);
			}

			if(isset($pastInfo->FRES6) && $pastInfo->FRES6 != ''){
				$examSubjectsMarksData[5]['subject_id'] = @$subjectCodeIds[@$pastInfo->EX_SUB9];
				$examSubjectsMarksData[5]['final_theory_marks'] = $pastInfo->FTM6;
				$examSubjectsMarksData[5]['final_practical_marks'] = $pastInfo->FPM6;
				$examSubjectsMarksData[5]['sessional_marks_reil_result'] = $pastInfo->fst6;
				$examSubjectsMarksData[5]['total_marks'] = $pastInfo->FTOT6;
				$examSubjectsMarksData[5]['final_result'] = $pastInfo->FRES6;
				$examSubjectsMarksData[5]['max_marks'] = $marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB9]);
				$examSubjectsMarksData[5]['num_words'] = $marksheet_component_obj->numberInWord($examSubjectsMarksData[5]['total_marks']);
				$examSubjectsMarksData[5]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[5]['total_marks']);
			}

			if(isset($pastInfo->FRES7) && $pastInfo->FRES7 != ''){
				$examSubjectsMarksData[6]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB7];
				$examSubjectsMarksData[6]['final_theory_marks'] = $pastInfo->FTM7;
				$examSubjectsMarksData[6]['final_practical_marks'] = $pastInfo->FPM7;
				$examSubjectsMarksData[6]['sessional_marks_reil_result'] = $pastInfo->fst7;
				$examSubjectsMarksData[6]['total_marks'] = $pastInfo->FTOT7;
				$examSubjectsMarksData[6]['final_result'] = $pastInfo->FRES7;
				$examSubjectsMarksData[6]['max_marks'] = $marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB7]);
				$examSubjectsMarksData[6]['num_words'] = $marksheet_component_obj->numberInWord($examSubjectsMarksData[6]['total_marks']);
				$examSubjectsMarksData[6]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[6]['total_marks']);
			}

        }else{
			if(!empty($examSubjectsMarksData)){
				$k=0;
				foreach($examSubjectsMarksData as $v){
					$examSubjectsMarksData[$k]['max_marks'] = $marksheet_component_obj->getSubjectMaxMarks($v->subject_id);
					$examSubjectsMarksData[$k]['num_words'] = $marksheet_component_obj->numberInWord($v->total_marks);
					$examSubjectsMarksData[$k]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($v->total_marks);
					$totalMarks = $totalMarks + $examSubjectsMarksData[$k]['max_marks'] ;
					$grandFinalTotalMarks = $grandFinalTotalMarks + $v->total_marks;
					$k++;
				}
		    }
		}

		$dobInWords = null;
		if(@($student->dob)){
			$dobInWords = $marksheet_component_obj->getDObInWords($student->dob);
		}

		$subjects = $marksheet_component_obj->_getSubjectsForMarksheet($courseVal);

		/* qr Code */
		$imagepath = asset('public/qrcode/enrollment/'.$enrollment.'.png');
		$custom_component_obj = new CustomComponent;
		$controller_obj = new Controller;
		$alpha = $controller_obj->toAlpha($enrollment);
		$url = URL::to("/qr?$alpha");//Rohit
		$qrcode=$marksheet_component_obj->qrcode($url,$enrollment);
		$imagepath = asset('public/qrcode/enrollment/'.$enrollment.'.png');
		$barcode_img = '<img src="'. $imagepath.'" alt=barcode-'.$enrollment.' style="font-size:0;position:relative;width:65px;height:65px;" >';

		/* bar Code
			$imagepath = asset('public/barcode/enrollment/'.$enrollment.'.png');
			$custom_component_obj = new CustomComponent;
			$barcode = $custom_component_obj->barcode($enrollment);
			$barcode_img = '<img src="'. $imagepath.'" alt=barcode-'.$enrollment.' style="font-size:0;position:relative;width:132px;height:20px;" >';
		*/

		$marksheet_type = 'Issued On ';
		//@dd($examSubjectsMarksData);
		/* if($courseVal == 10){
			// 	return view('resultupdate.marksheet_print_design', compact('pastdataurl','pastdatadocument','barcode_img','pastInfo','formId','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','examSubjectsMarksData','subjects','subjectCodeIds','resultDate','dobInWords','documents','marksheet_type'));
			return PDF::loadView('resultupdate.10_marksheet_print_design', compact('pastdataurl','pastdatadocument','barcode_img','pastInfo','formId','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','examSubjectsMarksData','subjectCodeIds','subjects','resultDate','dobInWords','documents','marksheet_type'));
		}else{
			// 	return view('resultupdate.marksheet_print_design', compact('pastdataurl','pastdatadocument','barcode_img','pastInfo','formId','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','examSubjectsMarksData','subjects','subjectCodeIds','resultDate','dobInWords','documents','marksheet_type'));
			return PDF::loadView('resultupdate.12_marksheet_print_design', compact('pastdataurl','pastdatadocument','barcode_img','pastInfo','formId','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','examSubjectsMarksData','subjectCodeIds','subjects','resultDate','dobInWords','documents','marksheet_type'));
		} */
        return PDF::loadView('resultupdate.marksheet_print_design', compact('pastdataurl','pastdatadocument','barcode_img','pastInfo','formId','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','examSubjectsMarksData','subjectCodeIds','subjects','resultDate','dobInWords','documents','marksheet_type'));
	}
	
	public function downloadCustomSingleCertificatePdf($enrollment=null,$marksheet_type='pass'){
		$marksheet_component_obj = new MarksheetCustomComponent;
		$current_admission_session_id = Config::get('global.current_admission_session_id');
		$exam_month = Config::get('global.current_exam_month_id');

		$title = "Single Certificate";
        $table_id = "Single Certificate";
		$formId = ucfirst(str_replace(" ","-",$title));

		$documents='';
        $totalMarks = 0;
		$resultDate = '';
		$grandFinalTotalMarks = 0;
        if($enrollment < 0 || $enrollment == null || $enrollment == ''){
			return redirect()->route('downloadBulkDocument',$enrollment)->with('message',"Enrollment is not in correct format.");
		}

		$serial_number = $marksheet_component_obj->getSerialNumbercertficate($enrollment);

		$findInFlag = 'Pastdata';
		$pastInfo = Pastdata::where('ENROLLNO','=',$enrollment)->orderBy('id','DESC')->first();

		$examresultfields = array('final_result','exam_month','exam_year','total_marks','percent_marks');
        $final_result = ExamResult::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first($examresultfields);
		if(isset($final_result->exam_month) && isset($final_result->exam_year))	{
			$combination = $final_result->exam_month . ' '. $final_result->exam_year;
		}
		$student = Student::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first();
		$resultsyntax = array('999' => 'AB','666' => 'SYCP','777' => 'SYCT','888'=>'SYC','P'=>'P');

        if(!empty($student)){
			$application = Application::where('student_id','=',$student->id)->orderBy('id','DESC')->first();

			$student->display_exam_month_year = $marksheet_component_obj->getDisplayExamMonthYear($combination);
			$newexamresultfields = array('exam_year','exam_month','result_date');

			$final_result_data = ExamResult::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first($newexamresultfields);
			if(isset($final_result_data->result_date) && $final_result_data->result_date != ""){
				$resultDate=strtotime($final_result_data->result_date);
				$resultDate =  date("d-m-Y",$resultDate);
			}
			$courseVal = $student->course;

			$address = Address::where('student_id','=',$student->id)->orderBy('id','DESC')->first();

			$findInFlag = 'Student';
			$dtarr = explode('-',$student->dob);
			$student->dob = $dtarr[2]."-".$dtarr[1]."-".$dtarr[0];
		}else{

		}

		$dobInWords = null;
		if(@($student->dob)){
			$dobInWords = $marksheet_component_obj->getDObInWords($student->dob);
		}

		/* Get Barcode code
		$imagepath = asset('public/barcode/enrollment/'.$enrollment.'.png');
		$custom_component_obj = new CustomComponent;
		$barcode = $custom_component_obj->barcode($enrollment);
		$barcode_img = '<img src="'.$imagepath.'" alt=barcode-'.$enrollment.' style="font-size:0;position:relative;width:132px;height:20px;" >';
		*/
		/* qr Code */
		$controller_obj = new Controller;
		$alpha = $controller_obj->toAlpha($enrollment);
		$url = URL::to("/qr?$alpha");//Rohit
		$qrcode=$marksheet_component_obj->qrcode($url,$enrollment);
		$imagepath = asset('public/qrcode/enrollment/'.$enrollment.'.png');
		$barcode_img = '<img src="'. $imagepath.'" alt=barcode-'.$enrollment.' style="font-size:0;position:relative;width:58px;height:58px;" >';


		$marksheet_type = 'Issued On ';

		return PDF::loadView('resultupdate.certificate_print_design', compact('barcode_img','formId','marksheet_type','final_result','enrollment','serial_number','student','dobInWords','resultsyntax','resultDate','dobInWords','documents','combination','pastInfo'));
	}

	public function downloadCustomSingleStrPdf($enrollment=null,$stream=null,$marksheet_type='pass',$counter=0){
		$marksheet_component_obj = new MarksheetCustomComponent;
		$current_admission_session_id = CustomHelper::_get_selected_sessions();
		$selected_session = CustomHelper::_get_selected_sessions();
		$current_exam_month_id = $stream;
		$pastData = null;
		$studentData = null;

		$title = "Single Big Font Str";
        $table_id = "Single Big Font Str";
		$formId = ucfirst(str_replace(" ","-",$title));

		$examresultfields = array('final_result','exam_month','exam_year','total_marks','percent_marks','additional_subjects','is_temp_examresult');
        $final_result = ExamResult::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first($examresultfields);
		if($final_result->is_temp_examresult == "111"){
			return false;
		}

		$documents='';
        $totalMarks = 0;
		$resultDate = '';
		$grandFinalTotalMarks = 0;
        if($enrollment < 0 || $enrollment == null || $enrollment == ''){
			return redirect()->route('finalupdate',$enrollment)->with('message',"Enrollment is not in correct format.");
		}

		// $serial_number = $marksheet_component_obj->getSerialNumber($enrollment);
		$subjects = $this->subjectList();
		$studentAllotmentData = StudentAllotment::where('enrollment','=',$enrollment)->whereNull('deleted_at')->count();

		$dataSave = array();
		if(!empty(@$studentAllotmentData) && $studentAllotmentData != '0'){
			$marksheet_number = '';
			$marksheetData=MarksheetPrint::where('enrollment','=',$enrollment)->orderBy('id','DESC')->whereNull('deleted_at')->latest('version')->first();
			if(!empty($marksheetData)){
				$marksheet_number = @$marksheetData->serial_number;
			}
			$conditions = array();
			$conditions['students.exam_year'] = $selected_session;
			$conditions['students.exam_month'] = $current_exam_month_id;
			$conditions['students.enrollment'] = $enrollment;
			$conditions['students.is_eligible'] =1;

			/*
			$studentData = Student::LeftJoin('applications', 'applications.student_id', '=', 'students.id')
			->LeftJoin('documents', 'documents.student_id', '=', 'students.id')
			->LeftJoin('addresses', 'addresses.student_id', '=', 'students.id')
			->LeftJoin('exam_results', 'exam_results.enrollment', '=', 'students.enrollment')
			->where($conditions)
			->whereNull('students.deleted_at')
			->first(array('students.id', 'students.ai_code', 'students.enrollment', 'students.name', 'students.father_name', 'students.mother_name', 'students.mobile', 'students.name_hi', 'students.stream', 'applications.category_a', 'students.dob', 'students.course', 'addresses.district_name', 'addresses.tehsil_name', 'documents.id as document_id', 'documents.student_id', 'documents.photograph', 'documents.signature', 'exam_results.final_result', 'exam_results.total_marks','exam_results.additional_subjects'));
			*/

			$queryResult = DB::select('call getStrByEnrollment(?, ?, ?)', [$selected_session,$current_exam_month_id,$enrollment]);
			// dd($queryResult);
			$result = collect($queryResult);
			//@dd($result);
			$studentData  = @$result[0];
			//@dd($studentData);

			$key =0;
			if(!empty($studentData)){
				$dataSave[$key]['marksheetno'] = @$marksheet_number;
				$dataSave[$key]['id'] = @$studentData->id;
				$dataSave[$key]['name'] = @$studentData->name;
				$dataSave[$key]['father_name'] = @$studentData->father_name;
				$dataSave[$key]['mother_name'] = @$studentData->mother_name;
				$dataSave[$key]['enrollment'] = @$studentData->enrollment;
				$dataSave[$key]['type'] = 'Student';
				$dataSave[$key]['ai_code'] = @$studentData->ai_code;
				$dataSave[$key]['final_result_enr'] = @$studentData->final_result;
				$dataSave[$key]['total_marks_enr'] = @$studentData->total_marks;
				$dataSave[$key]['additional_subjects'] = @$studentData->additional_subjects;
				if (isset($studentData->dob) && !empty(@$studentData->dob)) {
					$dataSave[$key]['dob'] = @$studentData->dob;
				} else {
					$dataSave[$key]['dob'] = '';
				}
				/* FETCH STUDENT'S PHOTOGRAPH */
				if(isset($studentData->photograph)){
					$dataSave[$key]['photograph'] = @$studentData->photograph;
				} else { //default photo
					$dataSave[$key]['photograph'] = 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22100%22%20height%3D%22100%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20100%20100%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_15b3d84a8f2%20text%20%7B%20fill%3A%23AAAAAA%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_15b3d84a8f2%22%3E%3Crect%20width%3D%22100%22%20height%3D%22100%22%20fill%3D%22%23EEEEEE%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2213.5%22%20y%3D%2254.5%22%3EPhotograph%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E';
				}
			} else {
				$pastDataCondtions = array();
				$pastDataCondtions['pastdatas.ENROLLNO'] = @$enrollment;
				$pastDataCondtions['exam_results.exam_year'] = @$selected_session;
				$pastDataCondtions['exam_results.exam_month'] = @$current_exam_month_id;
				$pastDataCondtions['exam_results.enrollment'] = @$enrollment;

				$pastData = Pastdata::LeftJoin('exam_results', 'exam_results.enrollment', '=', 'pastdatas.ENROLLNO')
				->where($pastDataCondtions)
				->orderBy('pastdatas.id','DESC')
				->get(array('pastdatas.ENROLLNO', 'pastdatas.NAME', 'pastdatas.FNAME', 'pastdatas.MNAME', 'pastdatas.MOBILE','pastdatas.CATEGORY', 'pastdatas.DOB', 'pastdatas.CLASS',  'exam_results.final_result', 'exam_results.total_marks','exam_results.additional_subjects'));
			    $pastData=count($pastData);
				if($pastData!=0){
					$dataSave[$key]['marksheetno'] = @$marksheet_number;
					$dataSave[$key]['ai_code'] = substr(@$pastData->ENROLLNO,0,5);
					$ai_code = $dataSave['ai_code'];

					$dataSave[$key]['enrollment'] = @$pastData->ENROLLNO;
					$dataSave[$key]['name'] = $pastData->NAME;
					$dataSave[$key]['father_name'] = @$pastData->FNAME;
					$dataSave[$key]['mother_name'] = @$pastData->MNAME;

					$dataSave[$key]['dob'] = date('d/m/Y', strtotime(@$pastData->DOB));
					$dataSave[$key]['aadhar_number'] = '';

					$dataSave[$key]['stream'] = 0;
					$dataSave[$key]['application'] = '';
					$dataSave[$key]['exam'] = 0;
					$dataSave[$key]['course'] = @$pastData->CLASS;
					$dataSave[$key]['yy'] = $yy = substr(@$pastData->ENROLLNO,5,2);
					$dataSave[$key]['student_code'] =  $st_code = substr(@$pastData->ENROLLNO,7);
					$courseVal = $pastData->CLASS;

					$dataSave[$key]['final_result_enr'] = @$pastData->final_result;
					$dataSave[$key]['total_marks_enr'] = @$pastData->total_marks;
					$dataSave[$key]['additional_subjects'] = @$pastData->additional_subjects;

					if(isset($pastData->MOBILE) && !empty(@$pastData->MOBILE)){
						$dataSave[$key]['mobile'] = @$pastData->MOBILE;
					}

					$dataSave[$key]['photograph'] = 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22100%22%20height%3D%22100%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20100%20100%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_15b3d84a8f2%20text%20%7B%20fill%3A%23AAAAAA%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_15b3d84a8f2%22%3E%3Crect%20width%3D%22100%22%20height%3D%22100%22%20fill%3D%22%23EEEEEE%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2213.5%22%20y%3D%2254.5%22%3EPhotograph%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E';
					//pr($dataSave);die;
				}
			}

			$enrollmentWithZero = '';
			if (strlen($enrollment) == 10) {
				$enrollmentWithZero = '0' . $enrollment;
			} else {
				$enrollmentWithZero = $enrollment;
			}

			$examSubjectData = ExamSubject::Join('subjects', 'subjects.id', '=', 'exam_subjects.subject_id')
			->where('exam_subjects.enrollment','=',$enrollmentWithZero)
			->where('exam_subjects.exam_year','=',$selected_session)
			->where('exam_subjects.exam_month','=',$current_exam_month_id)
			->whereNull('exam_subjects.deleted_at')
			->orderBy('subjects.subject_code','ASC')
			->get(array('exam_subjects.id','exam_subjects.subject_id','exam_subjects.exam_year','exam_subjects.exam_month','exam_subjects.subject_id','exam_subjects.enrollment','is_additional','final_theory_marks','final_practical_marks','sessional_marks','sessional_marks_reil_result_20','sessional_marks_reil_result','total_marks','final_result','is_supplementary_subject','subjects.subject_code'));

			$dataSave[$key]['exam_subjects'] = @$examSubjectData;
			// $dataSave['exam_subjects'] = '1,2,3,4,5';
		}
		//@dd($examSubjectData);
		// @dd($dataSave);

		$resultsyntaxarr = array('999' => 'AB','666' => 'SYCP','777' => 'SYCT','888'=>'SYC','P'=>'P');
		$resultsyntax = array('999' => 'AB','666' => 'SYCP','777' => 'SYCT','888'=>'SYC','P'=>'P');
		$aiCenterDetail= array();

		//$exam1 = Configure::read('Site.exam1');
		//$exam2 = Configure::read('Site.exam2');
		$examDates[1] = '2022';
		$examDates[2] = '2023';
		$ai_code = @$dataSave[$key]['ai_code'];
		return @$dataSave[0];

		return PDF::loadView('str_certificate_marksheet.str_bigfont_view', compact('resultsyntax','serial_number','subjects','resultsyntaxarr','aiCenterDetail','examDates','dataSave','ai_code','pastData','counter'));
	}
	
	public function subjectList($course=null){
		$condtions = null;
		$result = array();
		if($course!=null){
			$condtions = ['course' => $course,'deleted' => 0];
		} else{
			$condtions = ['deleted' => 0];
		}

		$mainTable = "subjects";
		$cacheName = "Subjects_". $course;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
				return $result = DB::table($mainTable)->where($condtions)->get()->pluck('name','id');

			});
		}
		return $result;
	}
	
	public function subjectDetailById($subject_id=null){
		$condtions = null;
		$result = array();
		if($subject_id!=null){
			$condtions = ['id' => $subject_id];
		}

		$mainTable = "subjects";
		$cacheName = "Subjects_". $subject_id;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
				return $result = DB::table($mainTable)->where($condtions)->whereNull('deleted_at')->first();

			});
		}
		return $result;
	}

	public function getEnrollmentListByAiCode($ai_code,$course=null,$stream=null,$resultType=null,$offset=0,$limit=50){

		$enrollmentList = array();
		$ai_code_conditions = array();
		if(!empty($course) && $course==!null){
			$ai_code_conditions["students.course"] = @$course;
		}


		// if(!empty($stream) && $stream==!null){
		// 	$ai_code_conditions["students.stream"] = @$stream;
		// }
		//$ai_code_conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
		//$ai_code_conditions["student_allotments.exam_month"] = @$stream;
		$ai_code_conditions["exam_results.exam_year"] = CustomHelper::_get_selected_sessions();
		$ai_code_conditions["exam_results.exam_month"] = @$stream;
		if($ai_code > 0){
			$ai_code=$this->getAiCentersmappeduserdatacode($ai_code);

			if($resultType=='PASS'){
				/*$enrollmentList=StudentAllotment::join('exam_results','exam_results.student_id','=','student_allotments.student_id')
				->join('students','students.id','=', 'student_allotments.student_id')
				->whereIn('students.ai_code',$ai_code)
				->where($ai_code_conditions)
				->where('exam_results.final_result', 'PASS')
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNull('student_allotments.deleted_at')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->skip($offset)->take($limit)
				->get(array('students.enrollment')); */
				$enrollmentList = Student::join('exam_results','exam_results.student_id','=', 'students.id')
				->whereIn('students.ai_code', $ai_code)
				->where($ai_code_conditions)
				->where('exam_results.final_result', 'PASS')
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->skip($offset)->take($limit)
				->get(array('students.enrollment'));

			} else if(empty($resultType) || $resultType==null){

				/*$enrollmentList=StudentAllotment::join('exam_results','exam_results.student_id','=','student_allotments.student_id')
				->join('students','students.id','=', 'student_allotments.student_id')
				->whereIn('students.ai_code',$ai_code)
				->where($ai_code_conditions)
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNull('student_allotments.deleted_at')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->skip($offset)->take($limit)
				->get(array('students.enrollment'));*/


				$enrollmentList = Student::join('exam_results','exam_results.student_id','=', 'students.id')
				->whereIn('students.ai_code', $ai_code)
				->where($ai_code_conditions)
				->whereNotNull('students.enrollment')
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->skip($offset)->take($limit)
				->get(array('students.enrollment'));
			} else {

				/*$enrollmentList=StudentAllotment::join('exam_results','exam_results.student_id','=','student_allotments.student_id')
				->join('students','students.id','=', 'student_allotments.student_id')
				->whereIn('students.ai_code',$ai_code)
				->where($ai_code_conditions)
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNull('student_allotments.deleted_at')
				->where('exam_results.final_result', '!=' , 'PASS')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->skip($offset)->take($limit)
				->get(array('students.enrollment')); */


				$enrollmentList = Student::join('exam_results','exam_results.student_id','=', 'students.id')
				->whereIn('students.ai_code', $ai_code)
				->where($ai_code_conditions)
				->where('exam_results.final_result', '!=' , 'PASS')
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->skip($offset)->take($limit)
				->get(array('students.enrollment'));
			}
		}

		return $enrollmentList;
	}

	public function getAiCentersmappeduserdatacode($auth_user_id =null){
		$master = DB::table('ai_center_maps')->where('parent_aicode',$auth_user_id)->whereNull('deleted_at')->pluck('ai_code');
		return $master;
	}
	
	public function getEnrollmentCountByAiCode($ai_code,$course=null,$stream=null,$resultType=null){
		$enrollmentCount = 0;
		$ai_code_conditions = array();
		if(!empty($course) && $course==!null){
			$ai_code_conditions["students.course"] = @$course;
		}
		// if(!empty($stream) && $stream==!null){
		// 	$ai_code_conditions["students.stream"] = @$stream;
		// }
		//$ai_code_conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
		//$ai_code_conditions["student_allotments.exam_month"] = @$stream;
		$ai_code_conditions["exam_results.exam_year"] = CustomHelper::_get_selected_sessions();
		$ai_code_conditions["exam_results.exam_month"] = @$stream;



		if($ai_code > 0){
			$ai_code=$this->getAiCentersmappeduserdatacode($ai_code);


			if($resultType=='PASS'){
				/*$enrollmentCount=StudentAllotment::join('exam_results','exam_results.student_id','=','student_allotments.student_id')
				->join('students','students.id','=', 'student_allotments.student_id')
				->whereIn('students.ai_code',$ai_code)
				->where($ai_code_conditions)
				->where('exam_results.final_result', 'PASS')
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNull('student_allotments.deleted_at')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->pluck('students.enrollment')->count();*/

				$enrollmentCount = Student::join('exam_results','exam_results.student_id','=', 'students.id')
				->whereIn('students.ai_code', $ai_code)
				->where($ai_code_conditions)
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->where('exam_results.final_result', 'PASS')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->pluck('students.enrollment')
				->count();
			} else if(empty($resultType) || $resultType==null){
				/*$enrollmentCount=StudentAllotment::join('exam_results','exam_results.student_id','=','student_allotments.student_id')
				->join('students','students.id','=', 'student_allotments.student_id')
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNull('student_allotments.deleted_at')
				->whereIn('students.ai_code',$ai_code)
				->where($ai_code_conditions)
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
			    ->pluck('students.enrollment')->count();*/
				$enrollmentCount = Student::join('exam_results','exam_results.student_id','=', 'students.id')
                ->whereIn('students.ai_code', $ai_code)
                ->where($ai_code_conditions)
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->pluck('students.enrollment')
				->count();
			} else {
				/*$enrollmentCount=StudentAllotment::join('exam_results','exam_results.student_id','=','student_allotments.student_id')
				->join('students','students.id','=', 'student_allotments.student_id')
				->whereIn('students.ai_code',$ai_code)
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNull('student_allotments.deleted_at')
				->where($ai_code_conditions)
				->where('exam_results.final_result', '!=' , 'PASS')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->pluck('students.enrollment')->count();*/


				$enrollmentCount = Student::join('exam_results','exam_results.student_id','=', 'students.id')
				->whereIn('students.ai_code',$ai_code)
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->where($ai_code_conditions)
				->where('exam_results.final_result', '!=' , 'PASS')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->pluck('students.enrollment')->count();
			}
		}
		return $enrollmentCount;
	}

	public function getResultByEnrollment($enrollment,$type=null){
		$result = false;
		$checkResultAcoordingType = ExamResult::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first(array('final_result'));
		if(!empty(@$checkResultAcoordingType->final_result)){
			$result = @$checkResultAcoordingType->final_result;
		}
		return $result;
	}

	public function getCountPassEnrollmentByEnrollmentArr($enrollment_arr){
		$passEnrollmentCount = 0;
		$passEnrollmentCount = ExamResult::whereIn('enrollment', $enrollment_arr)->where('final_result','PASS')->count();
		return $passEnrollmentCount;
	}

	public function resutlprocessallow(){
		$showStatus = false;
        $request_client_ip = Config::get('global.request_client_ip');
        $result_process_allowed_ips =json_decode(Config::get('global.result_process_allowed_ips'),true);

        if(in_array($request_client_ip,$result_process_allowed_ips)){
	        $showStatus = true;
        }

		return $showStatus;
	}

	public function getAicenterDataWithCache($formId=null,$isPaginate=false){

		$orderByRaw = Session::get($formId. '_orderByRaw');
		$conditions = Session::get($formId. '_conditions');
        //$paginatevalue = Session::get($formId. '_paginatevalue');
		$middleName = json_encode($conditions);
      	$master = array();
		// if(@$paginatevalue){
		// 	$defaultPageLimit = $paginatevalue;
		// }else{
		// 	$defaultPageLimit = config("global.defaultPageLimit");
		// }
		$defaultPageLimit = config("global.defaultPageLimit");

		if($isPaginate){
			if(!empty($orderByRaw)){
				$cacheName = "get_aicenter_with_cache_".@$middleName . "_" . $isPaginate ;
				// Cache::forget($cacheName);
				if (Cache::has($cacheName)) {
					$result = Cache::get($cacheName);
				}else{
					$result = Cache::rememberForever($cacheName, function () use ($conditions,$orderByRaw,$defaultPageLimit) {
                       return $result = AicenterDetail::where($conditions)->orderByRaw($orderByRaw)->paginate($defaultPageLimit,array('ssoid','college_name',
						'ai_code','district_id','block_id','principal_name','active',
						'nodal_officer_name','id','principal_mobile_number','nodal_officer_mobile_number','school_account_number','school_account_bank_name','school_account_ifsc','temp_district_id','temp_block_id'));
					});
				}
			}else{
				$cacheName = "get_aicenter_with_cache_order_by_ai_code_".@$middleName . "_" . $isPaginate ;
				// Cache::forget($cacheName);
				if (Cache::has($cacheName)) {
					$result = Cache::get($cacheName);
				}else{
					$result = Cache::rememberForever($cacheName, function () use ($conditions,$defaultPageLimit) {
						return $result = AicenterDetail::where($conditions)->orderBy('ai_code','ASC')->paginate($defaultPageLimit,array('ssoid','college_name',
						'ai_code','district_id','block_id','principal_name','active',
						'nodal_officer_name','id','principal_mobile_number','nodal_officer_mobile_number','school_account_number','school_account_bank_name','school_account_ifsc'));
					});
				}
			}
		}else{
			// $master = AicenterDetail::where($conditions)->orderBy('ai_code', 'ASC')->get(array('ssoid','college_name',
			// 	'ai_code','district_id','block_id','principal_name','active',
			// 	'nodal_officer_name','id','principal_mobile_number','nodal_officer_mobile_number','school_account_number','school_account_bank_name','school_account_ifsc'));
		}
		return $result;
	}

	public function getTempAicenterDataWithCache($formId=null,$isPaginate=false){

		$orderByRaw = Session::get($formId. '_orderByRaw');
		$conditions = Session::get($formId. '_conditions');
		$conditions['is_allow_for_admission'] = 1;
        //$paginatevalue = Session::get($formId. '_paginatevalue');
		$middleName = json_encode($conditions);
      	$master = array();
		// if(@$paginatevalue){
		// 	$defaultPageLimit = $paginatevalue;
		// }else{
		// 	$defaultPageLimit = config("global.defaultPageLimit");
		// }
		$defaultPageLimit = config("global.defaultPageLimit");
		if($isPaginate){
			if(!empty($orderByRaw)){

				$cacheName = "update_temp_get_aicenter_with_cache_".@$middleName . "_" . $isPaginate ;
				Cache::forget($cacheName);
				if (Cache::has($cacheName)) {
					$result = Cache::get($cacheName);
				}else{
					$result = Cache::rememberForever($cacheName, function () use ($conditions,$orderByRaw,$defaultPageLimit) {
                       return $result = AicenterDetail::where($conditions)->where('active',1)->orderByRaw($orderByRaw)->paginate($defaultPageLimit,array('ssoid','college_name',
						'ai_code','temp_district_id','temp_block_id','principal_name','active',
						'nodal_officer_name','id','principal_mobile_number','nodal_officer_mobile_number','school_account_number','school_account_bank_name','school_account_ifsc'));
					});
				}
			}else{
				$cacheName = "update_temp_get_aicenter_with_cache_order_by_ai_code_".@$middleName . "_" . $isPaginate ;
				 Cache::forget($cacheName);
				if (Cache::has($cacheName)) {
					$result = Cache::get($cacheName);
				}else{
					$result = Cache::rememberForever($cacheName, function () use ($conditions,$defaultPageLimit) {
						return $result = AicenterDetail::where($conditions)->where('active',1)->orderBy('ai_code','ASC')->paginate($defaultPageLimit,array('ssoid','college_name',
						'ai_code','temp_district_id','temp_block_id','principal_name','active',
						'nodal_officer_name','id','principal_mobile_number','nodal_officer_mobile_number','school_account_number','school_account_bank_name','school_account_ifsc'));
					});
				}
			}
		}else{
			$result = AicenterDetail::where($conditions)->where('active',1)->orderBy('ai_code','ASC')->get(array('ssoid','college_name',
						'ai_code','temp_district_id','temp_block_id','principal_name','active',
						'nodal_officer_name','id','principal_mobile_number','nodal_officer_mobile_number','school_account_number','school_account_bank_name','school_account_ifsc'));
		}
		//dd($result);
		return $result;
	}

	public function getAicenterData($formId=null,$isPaginate=false){
		$orderByRaw = Session::get($formId. '_orderByRaw');
		$conditions = Session::get($formId. '_conditions');
      	$master = array();

		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			if(!empty($orderByRaw)){
				$master = AicenterDetail::where($conditions)->orderByRaw($orderByRaw)->orderBy('active','DESC')->orderBy('ai_code','ASC')->paginate($defaultPageLimit,array('ssoid','college_name',
				'ai_code','district_id','block_id','principal_name','active',
				'nodal_officer_name','id','principal_mobile_number','nodal_officer_mobile_number','school_account_number','school_account_bank_name','school_account_ifsc','pincode'));
			}else{
				$master = AicenterDetail::where($conditions)->orderBy('active','DESC')->orderBy('ai_code','ASC')->paginate($defaultPageLimit,array('ssoid','college_name',
				'ai_code','district_id','block_id','principal_name','active',
				'nodal_officer_name','id','principal_mobile_number','nodal_officer_mobile_number','school_account_number','school_account_bank_name','school_account_ifsc','pincode'));
			}
		}else{
			$master = AicenterDetail::where($conditions)->orderBy('active','DESC')->orderBy('ai_code','ASC')->get(array('ssoid','college_name',
				'ai_code','district_id','block_id','principal_name','active',
				'nodal_officer_name','id','principal_mobile_number','nodal_officer_mobile_number','school_account_number','school_account_bank_name','school_account_ifsc','pincode'));
		}
		return $master;
	}
	
	public function getAiCentersuserdatacode($aicenter_user_id=null){
	 $exam_year = CustomHelper::_get_selected_sessions();
	 $exam_month = Config::get('global.current_exam_month_id');
			$conditions = array(
				'user_id' => $aicenter_user_id,
			);
		$master = AicenterDetail::where($conditions)
			 	->whereNull('deleted_at')
				->WhereNotNull('user_id')
				->first('ai_code');
		return $master;
	}
	
	public function _getAiCenterByAiCode($ai_code=null){
		$exam_year = CustomHelper::_get_selected_sessions();
		$exam_month = Config::get('global.current_exam_month_id');
		$conditions = array(
			'ai_code' => $ai_code,
		);
		$master = AicenterDetail::where($conditions)
				->whereNull('deleted_at')
				->WhereNotNull('user_id')
				->first('user_id');
		return $master;
	}
	
	public function _checkSessionalMarksEntryAllowOrNotAllow(){
		$objController = new Controller();
		$combo_name = 'sessional_marks_submission_start_date';
		$combo_name2 = 'sessional_marks_submission_end_date';
		$sessional_start_date_arr = $objController->master_details($combo_name);
		$sessional_start_end_arr = $objController->master_details($combo_name2);
		if(strtotime(date("Y-m-d H:i:s")) >= strtotime($sessional_start_date_arr[1]) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime($sessional_start_end_arr[1])){
			$isValid = true;
		}else{
			$isValid = false;
		}
		return $isValid;
	}
	
	public function _checkCopyCheckingTheoryMarksEntryAllowOrNotAllow(){
		$objController = new Controller();
		$combo_name = 'copy_checking_theory_marks_submission_start_date';
		$combo_name2 = 'copy_checking_theory_marks_submission_end_date';
		$sessional_start_date_arr = $objController->master_details($combo_name);
		$sessional_start_end_arr = $objController->master_details($combo_name2);
		if(strtotime(date("Y-m-d H:i:s")) >= strtotime($sessional_start_date_arr[1]) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime($sessional_start_end_arr[1])){
			$isValid = true;
		}else{
			$isValid = false;
		}
		return $isValid;
	}

	public function _validStudentFromStudentAllotment($supp_conditions_pass){
		$getsupplementariesid =  DB::table('student_allotments')
		->join('supplementaries', 'supplementaries.student_id', '=', 'student_allotments.student_id')->whereNull('student_allotments.deleted_at')->where($supp_conditions_pass)->whereNull('supplementaries.deleted_at')
		->pluck('supplementaries.id');
		return $getsupplementariesid;
	}

	public function getrevisedresultstudentdata($enrollment=null,$dob=null){
        $dob = date("Y-m-d",strtotime($dob));
	  	$exam_year = config("global.current_admission_session_id");
		$exam_month = config("global.current_exam_month_id");

		$result = DB::select('call getStudentRevisedResult(?,?,?,?)',array($enrollment,$dob,$exam_year,$exam_month));

		if(@$result[0]){
			return $result[0];
		}
		return false;
	}
	
	public function suppAlreadyExist($student_id=null){
		$exam_year = Config::get('global.current_admission_session_id');
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

		$suppIdExist = false;
		$suppIdExistCount = Supplementary::where('student_id', $student_id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->count();
		if(isset($suppIdExistCount) && $suppIdExistCount > 0){
			$suppIdExist = true;
		}
		return $suppIdExist;
	}

	public function suppChallanTidAlreadyExist($student_id=null){
		$exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

		$challanIdExist = false;
		$challanIdExistCount = Supplementary::where('student_id', $student_id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->whereNotNull('challan_tid')->count();

		if(isset($challanIdExistCount) && $challanIdExistCount > 0){
			$challanIdExist = true;
		}
		return $challanIdExist;
	}
	
	public function studentUpdateByAdmin($student_id=null,$is_update=null){
		$status = false;
		if($student_id != null ){
			$isAllow = Application::where('student_id', $student_id)->where('locksumbitted',1)->count();
			if(@$isAllow && $isAllow > 0){
				$existCount = StudentUpdate::where('student_id', $student_id)->count();
				$studentUpdateDetails['student_id'] = $student_id;
				$studentUpdateDetails['is_update'] = $is_update;
				if(isset($existCount) && $existCount > 0){
					StudentUpdate::where('student_id',$student_id)->update($studentUpdateDetails);
					$status = true;
				}else{
					StudentUpdate::create($studentUpdateDetails);
					$status = true;
				}
			}
		}
		return $status;
	}

	public function countStudentPending($exam_month=null){
		$conditions=array();
		$counter = StudentUpdate::LeftJoin('student_fees', 'student_fees.student_id', '=', 'student_updates.student_id')->where('student_updates.is_update', 1)
		->whereNull('student_fees.student_id')
		->count();
		return $counter;
	}
	
	public function getWithPaginationAiCentersdata(){

	  $master = AicenterDetail::whereNull('deleted_at')->get(['college_name','ai_code']);



	  return $master;
	}
	
	public function _getExamCenterCodeForSummary($centercode=null,$course=null){
		$sa_conditions = null;
		$allowedExamCenters = array();
		$result = array();
		if($centercode == null ){
			$centercode = "ecenter10";
		}
		$current_exam_month_id = Config::get('global.current_exam_month_id');
		$selected_session = CustomHelper::_get_selected_sessions();

		$sa_conditions['exam_month'] = $current_exam_month_id;
		$sa_conditions['exam_year'] = $selected_session;
		$sa_conditions['course'] = $course;
		$allowedExamCenters = DB::table('student_allotments')->where($sa_conditions)->whereNull('deleted_at')->groupBy('student_allotments.examcenter_detail_id')->pluck('examcenter_detail_id','examcenter_detail_id');

		$mainTable = "examcenter_details";
		$cacheName = "get_examcenter_details_code_".$course;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($mainTable, $centercode,$allowedExamCenters) {
				return $result = DB::table($mainTable)
				->whereIn('id',$allowedExamCenters)->whereNull('deleted_at')->where('active',1)->get(['id','cent_name',$centercode])->toArray();
			});
		}
		return $result;
	}
	
	public function getresultrsosyearsList($combo_name=null){
		$condtions = null;
		$result = array();
		$mainTable = "rsos_years";
		$cacheName = "results_". $combo_name;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
				return $result = DB::table($mainTable)->get()->pluck('yearstext','id');

			});
		}
		return $result;
	}
	
	public function _checkIsRouteExists($route_name=null){
		if(!empty($route_name) && Route::has($route_name)) {
			return true;
		}
		return false;
	}

     /* public function _checkIsInValidStudentTweleveSupp($student_id=null){
		$status = false;
		$current_exam_month_id = Config::get('global.form_supp_current_admission_session_id');
	   $current_exam_month_id_supp = Config::get('global.supp_current_admission_exam_month');
	   
	   
		//$current_exam_month_id = $current_exam_month_id - 1;
	   
		$studentgetdata = Student::join('applications', 'applications.student_id', '=', 'students.id')
		->where('students.id',$student_id)->first(['students.exam_month','applications.pre_qualification','applications.year_pass']);
	    
		$current_diff_year = $current_exam_month_id - $studentgetdata->year_pass;
	 
	   
		//if(@$current_exam_month_id_supp == 2 && @$studentgetdata->pre_qualification == 10 && @$current_diff_year <=2 ){
			//$status = true;
		//}
		
		if(@$current_exam_month_id_supp == 2 && @$studentgetdata->pre_qualification == 10 && @$current_diff_year <=2 ){
			$status = true;
		}
		
		return $status;
	}*/
	
		public function getresultstudentdatamarksheet($enrollment=null,$dob=null){
        $dob = date("Y-m-d",strtotime($dob));
	  	$exam_year = config("global.current_admission_session_id");
		$exam_month = config("global.current_result_session_month_id");

		$result = DB::select('call getStudentResult123(?,?,?,?)',array($enrollment,$dob,$exam_year,$exam_month));
		if(@$result[0]){
			return $result[0];
		}

		return false;
	  }

	  public function getresultstudentalldatamarksheet($students_id=null,$exam_year=null,$exam_month=null){
        $exam_year = $exam_year;
		$exam_month = $exam_month;
		$result = DB::select('call getStudentResultSubject123(?,?,?)',array($students_id,$exam_year,$exam_month));
		if(@$result[0]){
			return $result;
		}
		return false;
	}

	public function getStudentProvisionalResult($enrollment=null,$dob=null){
        $dob = date("Y-m-d",strtotime($dob));
	  	$exam_year = config("global.current_result_session_year_id");
		$exam_month = config("global.current_result_session_month_id");
		$result = DB::select('call getStudentProvisionalResult(?,?,?,?)',array($enrollment,$dob,$exam_year,$exam_month));
		if(@$result[0]){
			return $result[0];
		}
		return false;
	}

	public function getStudentProvisionalResultSubject($students_id=null){
		$exam_year = config("global.current_result_session_year_id");
		$exam_month = config("global.current_result_session_month_id");
		$result = DB::select('call getStudentProvisionalResultSubject(?,?,?)',array($students_id,$exam_year,$exam_month));
		if(@$result[0]){
			return $result;
		}
		return false;
	}
	
	public function _checkIsInValidStudentTweleveSupp($student_id=null){
        $status = false;
        $current_exam_month_id = Config::get('global.form_supp_current_admission_session_id');
        $current_exam_month_id = $current_exam_month_id - 1;
        $studentgetdata = Student::join('applications', 'applications.student_id', '=', 'students.id')
        ->where('students.id',$student_id)->first(['students.exam_month','applications.pre_qualification','applications.year_pass']);
        if($studentgetdata->exam_month == 2 && $studentgetdata->pre_qualification == 10 && $studentgetdata->year_pass == $current_exam_month_id){
            $status = true;
        }

        return $status;
    }

	public function _getSubjectWiseFacultyMaster(){
		$conditions = null;
		$result = array();
		$conditions['course']=12;
		$mainTable = "subjects";
		$cacheName = "subject_course_faculty_";
		Cache::forget($cacheName);
		if (Cache::has($cacheName)){
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($conditions, $mainTable) {
				$tempResult = DB::table($mainTable)->where($conditions)->orderBy('subject_code')->get(['name','id','is_science_faculty','is_commerce_faculty','is_arts_faculty','is_allow_faculty']);
				$result = array();
				foreach(@$tempResult as $k => $value){
					if(@$value->is_science_faculty){
						$result['is_science_faculty'][$value->id] = $value->name;
					}
					if(@$value->is_commerce_faculty){
						$result['is_commerce_faculty'][$value->id] = $value->name;
					}
					if(@$value->is_arts_faculty){
						$result['is_arts_faculty'][$value->id] = $value->name;
					}
					if(@$value->is_allow_faculty){
						//$result['is_allow_faculty'][$value->id] = $value->name;
					}
				}
				return $result;
			});
		}
		return $result;
	}

	public function _getSubjectFacultyWise($faculty_id=null){
		$conditions = null;
		if(@$faculty_id){}else{
			return false;
		}
		$result = array();
		$conditions['course']=12;
		if($faculty_id == 1){
			$conditions['is_science_faculty']= 1;
		}
		if($faculty_id == 2){
			$conditions['is_commerce_faculty']= 1;
		}

		if($faculty_id == 3){
			$conditions['is_arts_faculty']= 1;
		}

		if($faculty_id == 4){
			$conditions['is_allow_faculty']= 1;//allow fot all faculty types
		}

		$mainTable = "subjects";
		$cacheName = "subject_course_faculty_wise_u_".$faculty_id;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) { //
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($conditions, $mainTable) {
				return $result = DB::table($mainTable)->where($conditions)->orderBy('name')->get()->pluck('name','id');
			});
		}
		return $result;
	}

	public function _getAiCentersDetailWithContactInfo($ai_code=null){
		$result = array();

		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		$mainTable = "_getAiCentersWithContactInfo";
		$cacheName = "_getAiCentersWithContactInfo_".$ai_code;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) { //
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($queryOrder, $ai_code) {
				return $result = AicenterDetail::select(
                DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,''), ' - ',COALESCE(`pincode`,''), ' Contact: ',COALESCE(`nodal_officer_name`,''), ' - ',COALESCE(`nodal_officer_mobile_number`,'') ) AS college_name"),'ai_code')
                ->where('active',1)
				->where('ai_code',$ai_code)
			    ->whereNull('deleted_at')
                ->orderByRaw($queryOrder)
                ->first('college_name');
			});
		}
		return @$result->college_name;
	}

	public function _getAiCentersWithContactInfo($district_id=null,$block_id=null){

		$result = array();

		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		$mainTable = "_getAiCentersWithContactInfo";
		$cacheName = "_getAiCentersWithContactInfo_";
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($queryOrder, $district_id,$block_id) {
				if(@$district_id){
					$conditions['district_id'] = $district_id;
				}
				if(@$block_id){
					$conditions['block_id'] = $block_id;
				}

				return $result = AicenterDetail::select(
                DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,''), ' - ',COALESCE(`pincode`,''), ' Contact: ',COALESCE(`nodal_officer_name`,''), ' - ',COALESCE(`nodal_officer_mobile_number`,'') , '</span>' ) AS college_name"),'ai_code')
                ->where('active',1)
				->where($conditions)
			    ->whereNull('deleted_at')
                ->orderByRaw($queryOrder)
                ->pluck('college_name', 'ai_code');
			});
		}
		return $result;
	}

	public function _getTempAiCentersWithContactInfo($district_id=null,$block_id=null,$req_type=null){

		$result = array();

		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		$mainTable = "_getTempAiCentersWithContactInfo";
		$cacheName = "_getTempAiCentersWithContactInfo_";

		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($queryOrder, $district_id,$block_id,$req_type) {
				if(@$district_id){
					$conditions['temp_district_id'] = $district_id;
				}
				if(@$block_id){
					$conditions['temp_block_id'] = $block_id;
				}

				if(@$req_type){
					$conditions[$req_type] = 1;
				}

				return $result = AicenterDetail::select(
                DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,''), ' - ',COALESCE(`pincode`,''), ' Contact: ',COALESCE(`nodal_officer_name`,''), ' - ',COALESCE(`nodal_officer_mobile_number`,'') , '</span>' ) AS college_name"),'ai_code')
                ->where('active',1)
				->where($conditions)
			    ->whereNull('deleted_at')
                ->orderByRaw($queryOrder)
                ->pluck('college_name', 'ai_code');
			});
		}
		return $result;
	}
	
	public function _getblockAiCentersWithContactInfo($block_id=null){

		$result = array();

		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		$mainTable = "_getAiCentersWithContactInfo";
		$cacheName = "_getAiCentersWithContactInfo_";
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) { //
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($queryOrder,$block_id) {
				return $result = AicenterDetail::select(
                DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,''), ' - ',COALESCE(`pincode`,''), ' - ',COALESCE(`nodal_officer_name`,''), ' - ',COALESCE(`nodal_officer_mobile_number`,'') ) AS college_name"),'ai_code')
                ->where('active',1)
				->where('block_id',$block_id)
			    ->whereNull('deleted_at')
                ->orderByRaw($queryOrder)
                ->pluck('college_name', 'ai_code');
			});
		}
		return $result;
	}
	
	public function _getblockAiCentersWithContactInfodistrict_id($district_id=null){
		$result = array();
		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		$mainTable = "_getAiCentersWithContactInfo";
		$cacheName = "_getAiCentersWithContactInfo_";
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) { //
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($queryOrder,$district_id) {
				return $result = AicenterDetail::select(
                DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,''), ' - ',COALESCE(`pincode`,''), ' - ',COALESCE(`nodal_officer_name`,''), ' - ',COALESCE(`nodal_officer_mobile_number`,'') ) AS college_name"),'ai_code')
                ->where('active',1)
				->where('district_id',$district_id)
			    ->whereNull('deleted_at')
                ->orderByRaw($queryOrder)
                ->pluck('college_name', 'ai_code');
			});
		}
		return $result;
	}
	
	public function _getTempblockAiCentersWithContactInfodistrict_id($temp_district_id=null,$req_type=null){
		$result = array();
		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		$mainTable = "_getAiCentersWithContactInfo";
		$cacheName = "_getAiCentersWithContactInfo_";
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) { //
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($queryOrder,$temp_district_id,$req_type) {
				return $result = AicenterDetail::select(
                DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,''), ' - ',COALESCE(`pincode`,''), ' - ',COALESCE(`nodal_officer_name`,''), ' - ',COALESCE(`nodal_officer_mobile_number`,'') ) AS college_name"),'ai_code')
                ->where('active',1)
				->where('temp_district_id',$temp_district_id)
			    ->whereNotNull('is_allow_for_admission')
			    ->whereNull('deleted_at')
                ->orderByRaw($queryOrder)
                ->pluck('college_name', 'ai_code');
			});
		}
		return $result;
	}
	
	public function _udpateLastAcotionPermedBy($student_id=null){
		$last_updated_by_user_id = null;
		$isStudentLoigin = $this->_getIsStudentLogin();
		if($isStudentLoigin){
			$last_updated_by_user_id = Auth::guard('student')->user()->id;
		}else{
			$last_updated_by_user_id = Auth::user()->id;
		}

		Student::where('id',$student_id)->update(['last_updated_by_user_id'=>$last_updated_by_user_id]);
		return true;
	}
	
	public function _getIsStudentLogin(){
		$role_id = @Session::get('role_id');
		$student = Config::get("global.student");
		if($role_id == $student){
			return true;
		}
		return false;
	}
	
	public function checkstudentrecord($enrollment=null,$dob=null){
		$dobs = date("Y-m-d",strtotime($dob));
		$conditions = array(
			'enrollment' => $enrollment,
			'dob' => $dobs,
		);
		$master = Student::where($conditions)
			->whereNull('deleted_at')
			//->whereNull('ssoid')
			->first(['id','ssoid','course','exam_month','adm_type']);
		return @$master;
	}

	public function _getAiCentersIdByAiCode($ai_code=null){
		$conditions = array(
			'ai_code' => $ai_code,
		);
		$master = AicenterDetail::where($conditions)
			->whereNull('deleted_at')
			->WhereNotNull('user_id')
			->first('user_id');
		return @$master->user_id;
	}

	public function _checkssoidallreadyaccess($table_name=null,$student_id=null,$ssoid=null,$course=null,$stream =null){
		$current_exam_month_id = Config::get('global.form_current_exam_month_id');
		$current_exam_year_id = Config::get('global.form_current_admission_session_id');
		$conditions = array(
			'ssoid'=>$ssoid,
		);
		if($table_name == "students"){
			$conditions = array(
				'ssoid'=>@$ssoid,
				'course'=>@$course,
				'stream'=>@$stream,
			);
		}
		$count =  DB::table($table_name)->where($conditions)->where('id',"!=",$student_id)
			->whereNull('deleted_at')
			->count();

		$table_name = 'users';
		$conditions = array(
			'ssoid'=>$ssoid
		);
		$countInUser =  DB::table($table_name)->where($conditions)
			->whereNull('deleted_at')
			->count();
		$count = $count+$countInUser;
		return @$count;
	}

	public function _oldgetAiCentersIdByAiCodebolck($districtid=null,$blockid=null,$ai_code=null){
		$conditions = array();
		if(@$blockid){
			$conditions['block_id'] = $blockid;
		}
		if(@$districtid){
			$conditions['district_id'] = $districtid;
		}
		if(@$ai_code){
			$conditions['ai_code'] = $ai_code;
		}
		$middleName = json_encode($conditions);


		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		$master = AicenterDetail::select(
            	DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,'')) AS college_name"),'ai_code')
				->where('active',1)
				->where($conditions)
			 	->whereNull('deleted_at')
				->orderByRaw($queryOrder)
				->pluck('college_name', 'ai_code');
		return $master;
	}

	public function _getAiCentersIdByAiCodebolck($districtid=null,$blockid=null,$ai_code=null){
		$conditions = array();
		$result = array();
		if(@$blockid){
			$conditions['block_id'] = $blockid;
		}
		if(@$districtid){
			$conditions['district_id'] = $districtid;
		}
		if(@$ai_code){
			// $conditions['ai_code'] = $ai_code;
		}
		$middleName = json_encode($conditions);
		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		$cacheName = "getAiCentersIdByAiCodebolck_".@$middleName;
		// Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($conditions,$queryOrder) {
				return $result = AicenterDetail::select(
            	DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,'')) AS college_name"),'ai_code')
				->where('active',1)
				->where($conditions)
			 	->whereNull('deleted_at')
				->orderByRaw($queryOrder)
				->pluck('college_name', 'ai_code');
			});
		}
		return $result;
	}
	
	public function _getTempAiCentersIdByAiCodebolck($districtid=null,$blockid=null,$ai_code=null){
		$conditions = array();
		$result = array();
		if(@$blockid){
			$conditions['temp_block_id'] = $blockid;
		}
		if(@$districtid){
			$conditions['temp_district_id'] = $districtid;
		}
		if(@$ai_code){
			// $conditions['ai_code'] = $ai_code;
		}

		$middleName = json_encode($conditions);
		$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
		$cacheName = "_getUTempAiCentersIdByAiCodebolck_".@$middleName;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) {
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($conditions,$queryOrder) {


				return $result = AicenterDetail::select(
				DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,'')) AS college_name"),'ai_code')
				->where('active',1)
				->where($conditions)
				->whereNotNull('is_allow_for_admission')
				->whereNull('deleted_at')
				->orderByRaw($queryOrder)
				->pluck('college_name', 'ai_code');
			});
		}
		return $result;
	}
	
	public function _setCountDownTimerDetails($studentRoleId=null){
		$t=time();
		$login_min_time = 60;
		$whiteListIp = $this->_getWhiteListCheckAllow();
		if($whiteListIp){
			$login_min_time = 480;
		}
		$studentRoleIdFromConfig = config("global.student");
		if(@$studentRoleId && $studentRoleIdFromConfig == $studentRoleId){
			$login_min_time = 10;
		}
		$login['start'] = $login_start = date("M j, Y H:i:s",$t);
		$login['end'] = $login_end = date('M j, Y H:i:s', strtotime($login_start. ' +' . $login_min_time.' minutes'));
		Session::put('login_start',$login_start);
		Session::put('login_end',$login_end);

		return json_encode($login);
	}
	
	public function _getWhiteListCheckAllow(){
		$showStatus = false;
		$request_client_ip = Config::get('global.request_client_ip');
		$whiteListMasterIps = Config::get("global.whiteListMasterIps");

		// echo $request_client_ip;
		// echo "<br>";
		// dd($whiteListMasterIps);
		if(isset($request_client_ip) && !empty($request_client_ip) && isset($whiteListMasterIps) && !empty($whiteListMasterIps)){
			if(in_array(@$request_client_ip,@$whiteListMasterIps)){
				$showStatus = true;
			}
		}
		return $showStatus;
	}

	public function _removeCountDownTimerDetails(){
		Session::forget('login_start');
		Session::forget('login_end');
		return true;
	}

	public function CenterAllotmentAllowIps(){
		$showStatus = false;
		$showStatus = $this->_getWhiteListCheckAllow();
        $request_client_ip = Config::get('global.CURRENT_IP');
		$objController = new Controller();
		$combo_name="center_allotment_allowed_ips";$center_allotment_allowed_ips=$objController->master_details($combo_name);
		if(@$center_allotment_allowed_ips['1']){
			$result_process_allowed_ips =json_decode($center_allotment_allowed_ips['1'],true);
		}

		if($showStatus){
		}else{
			if(@$result_process_allowed_ips){
				if(in_array($request_client_ip,$result_process_allowed_ips)){
					$showStatus = true;
				}
			}else{
				$showStatus=true;
			}
		}
		return $showStatus;
	}
	
    public function getExamcenterDataById($exam_center_id){
		$conditions = array();
		$conditions['examcenter_details.id'] = $exam_center_id;
        $master = ExamcenterDetail::where($conditions)->orderBy('ai_code')->first();
		return $master;
	}
	
   public function getBankListData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');

		$master = array();
		$fields = array('students.id','students.ssoid','students.is_self_filled','students.is_otp_verified','students.exam_year','students.exam_month','students.created_at','students.name','students.is_eligible','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','applications.fee_paid_amount');

		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = BankMaster::where($conditions)->paginate($defaultPageLimit);

		}
		else{
			$master = BankMaster::where($conditions)->get();
		}

		return $master;

	}
	
	public function tempgetEnrollmentCountByAiCode($ai_code,$course=null,$stream=null,$resultType=null){
		$enrollmentCount = 0;
		$ai_code_conditions = array();
		$studentIds = $this->tempgetIssueMarksheetStudents();
		if(!empty($course) && $course==!null){
			$ai_code_conditions["students.course"] = @$course;
		}
		// if(!empty($stream) && $stream==!null){
		// 	$ai_code_conditions["students.stream"] = @$stream;
		// }
		//$ai_code_conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
		//$ai_code_conditions["student_allotments.exam_month"] = @$stream;
		$ai_code_conditions["exam_results.exam_year"] = CustomHelper::_get_selected_sessions();
		$ai_code_conditions["exam_results.exam_month"] = @$stream;



		if($ai_code > 0){
			$ai_code=$this->getAiCentersmappeduserdatacode($ai_code);


			if($resultType=='PASS'){
				/*$enrollmentCount=StudentAllotment::join('exam_results','exam_results.student_id','=','student_allotments.student_id')
				->join('students','students.id','=', 'student_allotments.student_id')
				->whereIn('students.ai_code',$ai_code)
				->where($ai_code_conditions)
				->where('exam_results.final_result', 'PASS')
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNull('student_allotments.deleted_at')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->pluck('students.enrollment')->count();*/


				$enrollmentCount = Student::join('exam_results','exam_results.student_id','=', 'students.id')
				->whereIn('students.id', $studentIds)
				->whereIn('students.ai_code', $ai_code)
				->where($ai_code_conditions)
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->where('exam_results.final_result', 'PASS')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->pluck('students.enrollment')
				->count();


			} else if(empty($resultType) || $resultType==null){
				/*$enrollmentCount=StudentAllotment::join('exam_results','exam_results.student_id','=','student_allotments.student_id')
				->join('students','students.id','=', 'student_allotments.student_id')
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNull('student_allotments.deleted_at')
				->whereIn('students.ai_code',$ai_code)
				->where($ai_code_conditions)
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
			    ->pluck('students.enrollment')->count();*/
				$enrollmentCount = Student::join('exam_results','exam_results.student_id','=', 'students.id')
				->whereIn('students.id', $studentIds)
                ->whereIn('students.ai_code', $ai_code)
                ->where($ai_code_conditions)
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->pluck('students.enrollment')
				->count();
			} else {
				/*$enrollmentCount=StudentAllotment::join('exam_results','exam_results.student_id','=','student_allotments.student_id')
				->join('students','students.id','=', 'student_allotments.student_id')
				->whereIn('students.ai_code',$ai_code)
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNull('student_allotments.deleted_at')
				->where($ai_code_conditions)
				->where('exam_results.final_result', '!=' , 'PASS')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->pluck('students.enrollment')->count();*/


				$enrollmentCount = Student::join('exam_results','exam_results.student_id','=', 'students.id')
				->whereIn('students.id', $studentIds)
				->whereIn('students.ai_code',$ai_code)
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->where($ai_code_conditions)
				->where('exam_results.final_result', '!=' , 'PASS')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->pluck('students.enrollment')->count();
			}
		}
		return $enrollmentCount;
	}

	public function tempgetIssueMarksheetStudents(){
		$studentIds = array(648433,519434,637662,611716,647557,611770,609214,608765,597278,523698,502212,348731,547216,516668,476275);
		// $studentIds = array(581436,648433,519434,637662,611716,647557,611770,609214,608765,597278,523698,502212,348731,547216,516668,476275,358540,554759,524483,552551,425672,517678,256244,581051,532751,399097,512585,576490,552049,577406,390962,253904,576604,576540,301425,527868,604710,602892,495133,612438,609225,614228,499031,499111,623892,636157,250512,602793,498387,505202,617090,610135,599764,612198,609253,599746,544859,628201,390633,610017,516021,589402,599747,583811,299201,602936,495230,624853,602864,589223,581376,543515,610192,494167,505392,492816,569680,496886,524159,612128,496933,589445,496359,493031,496913,439286,557970,497033,555059,569948,502825,566420,380213,538776,571521,548752,353687,548861,353687,606992,620101,647410,631942,601236,378307,555135,601216,534899,254370,543733,438851,559973,612671,267916,612643,503233,261767,403158,556000,502740,649644,279002,593424,511836,466870,469098,608025,503224,403143,449657,502811,415565,494827,611861,613397,506690,605687,650784,650784,650915,611773,566344,578335,566337,286422,565623,582099,576220,600365,544361,518656,611104,301613,650565,507176,586629,364419,572122,575692,602660,554841,614027,627836,490548,560958,560964,557216,560973,262614,649724,589114,487266,204254,478419,641297,191104,305190,458938,243663,454587,563604,534572,301904,586823,517176,578063,500235,556181,612200,546939,612169,525986,578887,649635,196675,650087,646244,650128,609985,606997,645907,546539,605125,649596,648689,616879,469958,538427,575315,581115,505723,584389,641916,501598,602221,610457,613921,606156,601166,608312,595773,604605,630644,601444,434590,278784,506186,131474,535978,507569,613273,500093,633364,613697,601390,179288,492899,442718,612942,612429,613634,597224,510039,381489,533515,294112,527751,500934,607523,607558,515360,573938,515355,263625,499578,343941,650581,627125,603429,496710,448681,370908,419963,335015,612280,605849,597866,554436,576769,531089,575158,597954,614658,419379,597928,576785,118599,569001,396220,403562,570031,520012,505272,582763,338509,452522,254177,538822,448293,649149,649098,649211,493113,202474,527351,531489,531439,641618,643111,376143,611103,611179,650634,600234,612403,188216,612255,482496,203988,536908,233556,611571,566339,188216,562913,536364,566165,640668,615893,640626,592959,582329,491765,559359,548900,470384,584172,479393,585404,604730,537152,616390,610179,457139,611863,610048,616465,615785,582252,648833,612609,649144,651553,372869,651546,612321,530903,567746,563561,450970,563731,586333,617025,530367,553190,358289,617842,461223,414697,135983,283670,516296,430583,527001,627413,430534,640337,574993,605219,640324,648430,612067,458267,151612,599366,575012,457903,603995,237726,599798,466595,427631,605139,269224,564312,497212,435002,439081,435002,539843,585994,584504,582215,584905,579462,530444,346339,555887,353378,526380,584407,579485,557824,530581,559876,552716,513513,379599,503603,379599,499947,527210,432659,516535,467730,540337,379638,404319,473310,161907,502857,150062,553299,341956,510724,529499,365135,571087,580037,510717,500384,179902,497628,525458,498318,495618,523057,541326,571193,496758,494341,388677,529588,556873,452056,612468,471664,376497,349910,508617,364766,579950,538282,228583,579213,579907,570545,570541,570572,508500,572501,512466,379330,568919,573970,551756,564703,453883,553531,578618,312799,570835,577773,574685,507718,582556,501604,512414,582507,504698,523791,503754,416892,560447,538688,524733,191014,231854,504036,208970,513333,509059,233766,452309,374436,499705,508041,582916,523173,512455,295110,276132,426797,385660,435422,509077,211514,376330,578485,373340,565798,495705,474997,208185,535450,589130,582703,402620,613825,596528,505371,589736,340739,586184,582057,582461,571273,371141,388346,572024,376026,582438,613666,472933,339774,502654,469650,505081,487270,515039,637456,408249,573025,276378,573131,322806,517882,440939,380181,517658,518150,571357,517663,517645,518031,536485,541047,413177,581213,533669,436513,506627,194592,414993,506598,489711,584086,392659,584038,560943,575673,470972,546172,582983,530989,559411,395810,198286,578841,578861,580857,531847,529422,547270,536645,536780,558999,569729,546546,539919,623817,541565,542223,524667,523188,541559,598936,505816,550809,598994,579847,547326,560378,535734,531020,605940,636310,599338,636234,491297,609920,636250,643052,607892,566866,644639,584278,544607,380975,405769,651584,592640,544705,589661,649326,603430,583257,613636,651371,649202,649299,595647,649232,649282,629705,606335,477354,587581,608548,583734,318639,561929,348182,561931,583608,493771,537919,435839,559563,586301,522781,524176,602698,634733,650146,590004,450008,584054,634656,430232,455821,649932,468070,675271,414863,545683,586163,530824,548868,605679,335442,605635,634230,353643,612860,650776,607485,144745,605727,352146,280353,439123,232279,474669,492253,517069,604169,598618,511917,584986,589240,497539,597416,497520,584667,621309,604884,542829,621405,574745,550771,461772,598122,508871,381045,174911,445676,233456,152982,162997,133188,551679,493974,649745,649566,649515,649529,570603,582066,570611,517759,599794,645048,479356,467390,566881,566864,504377,498338,566848,546987,568886,589343,590363,589981,603502,590429,600454,589374,590373,588183,590434,398037,604607,590232,596070,591010,595458,284202,591152,594339,595774,596328,606870,522224,523253,464204,523261,430723,590289,589843,380353,566684,538526,613033,613226,588976,299891,559252,647971,601457,442138,619762,621290,379464,601589,608886,498215,409490,550162,498262,410173,379443,496442,404417,607916,308637,644590,616646,614782,599474,649704,613043,538021,217111,441757,411023,595231,593983,237554,346151,576268,220028,608357,515947,583903,545929,574078,516971,516978,521136,588604,607285,520493,502455,433067,607308,588623,611235,158738,566092,574391,585966,589855,519814,495513,580476,586103,577908,586470,579406,532403,458314,537711,403517,615208,592597,591142,613263,568883,602508,613155,602424,647336,529931,531943,415899,552510,513907,353183,513906,431137,381759,585871,568957,568980,540124,568966,390362,584778,572997,570802,267856,584909,582644,505093,573818,507217,386620,439292,642329,563509,555026,581688,556582,586117,581343,537438,429458,582752,459220,563608,614765,600891,507238,601180,601153,346982,544343,616886,599676,616973,614728,444810,592404,640182,599585,596698,599592,576440,519911,617665,503018,555928,649865,518778,585448,502014,603955,538704,538649,513855,197155,502368,376904,512254,334002,438144,601036,441881,601090,621280,601028,257298,610426,604093,603215,323431,642170,559169,491513,508700,466123,395433,378561,323556,592214,620629,602482,494899,495326,602450,611354,602373,437287,618483,633559,530378,603682,606644,600140,580245,533937,511504,571896,573802,571949,511488,600508,589239,603934,587954,533448,607234,617792,513783,396934,603920,588207,596979,540926,587965,596200,534013,395039,589284,599918,589256,591817,536531,535062,535822,598398,557723,433007,605983,606911,222337,420093,306052,605708,607183,614863,651561,544608,598377,605979,607254,605992,560822,605656,632479,605963,644798,614180,575083,605970,306083,369139,554958,644782,551551,551543,536238,191699,570635,219013,570859,442224,220376,570832,574999,619291,613252,590178,597158,619673,617051,496213,597285,604500,511740,498266,574843,572593,537949,610173,622957,520919,412166,553586,523186,546437,261350,490743,607327,604290,648735,376507,421331,607763,578908,572857,579013,282846,578755,626163,578772,572504,577941,430219,316229,628951,408169,532962,621019,570069,607981,601910,628998,430584,615108,461099,612371,249515,532698,425571,554511,512720,612220,465603,590382,269749,375129,611912,613010,385475,596188,622427,352880,606030,312536,454346,623788,609529,596164,625790,524466,563822,537878,538869,408815,381498,513733,513797,503302,380512,592298,297501,610572,612907,576737,309267,597302,622909,597406,541893,613651,559542,486815,486815,492912,610616,434328,570799,566140,611838,614043,304361,616373,626295,611992,518891,641107,563730,614097,614314,614007,613661,546025,609233,616431,613773,624230,611937,518889,613316,265486,647715,634381,613734,612018,390844,613811,647609,650865,616638,528947,647670,609496,518908,503147,624121,647644,504846,535437,575038,414582,518372,466617,528864,192692,431017,557045,518431,581680,401678,575120,563683,503670,445377,617079,637906,599105,527183,569233,550861,565625,550864,373276,591261,325527,566818,477145,529448,572240,588534,611306,612988,577487,566655,574431,198005,540291,577328,187315,540297,603371,508649,540288,529941,608896,437453,507452,630998,642712,547291,525719,528095,606181,597248,611128,606282,606282,334085,606678,581199,327672,560597,275596,567875,234069,546967,615648,507604,599265,338997,283587,305972,521758,283581,521742,343884,537461,555717,413500,279991,471056,524220,301639,509117,608559,671827,531982,379124,290020,578631,445270,520223,558083,604634,625155,651645,644037,643392,507254,349502,423300,600655,412456,596600,416776,574284,381110,500438,359068,452472,526970,595294,377386,453859,521334,526763,188379,567546,278703,526729,629934,582788,576310,576384,615554,612502,612467,650881,649083,271646,648932,609685,651273,611916,649208,650991,565185,560800,502162,511987,391966,511976,594866,600711,563159,594877,560553,569934,578904,578384,578437,578463,546261,569896,578720,565752,303274,590352,608149,302965,604146,603980,500745,500731,566159,577829,317678,575385,551548,615138,498453,615113,576318,595539,342486,389770,458380,421719,588352,425263,595255,597066,460480,508828,506127,563366,573699,510273,562985,295129,509724,539058,649145,600409,526811,600854,396738,526795,535480,483047,484438,570062,259167,562813,435445,584040,609946,504723,434666,610103,389284,528293,611513,511567,599882,596890,596951,609749,475118,609846,609652,597720,475118,542118,611231,545150,511597,499309,512264,533956,547665,511602,552092,535301,512244,600704,602035,602859,534589,602073,602840,392955,602050,602028,442529,602063,503208,487411,583231,574299,222973,501366,585439,563095,567254,534187,585446,298260,553257,414779,568123,568164,530063,566460,568111,354342,576859,566387,568131,585444,169811,576831,567995,566467,568068,568153,262309,576779,566394,567983,586788,568060,568104,568149,169815,576843,566368,567969,575485,415560,586881,568178,521984,529863,536146,499019,608611,546555,557579,483902,547234,547224,504430,250642,392672,563069,577738,568003,575703,384535,636603,554744,506724,580682,506363,505277,395172,180200,502866,395127,183344,502753,499576,296572,503655,502260,256135,383858,348518,267640,567499,573714,284798,474693,598670,541981,278623,541744,598647,581717,592601,614739,592574,511777,335555,502982,598555,478711,549084,231013,502453,189079,438109,510095,598614,528735,598533,506437,528643,476003,510095,541784,614870,364816,365041,540476,225154,599050,587780,543543,207015,570528,622341,516986,515955,240256,507063,587782,600480,623596,541467,600260,600324,211633,561222,507535,510077,573392,582629,556017,586334,472880,330449,554300,131870,573797,566769,576285,567893,324140,349946,615046,605614,542340,642001,573670,634157,619871,425076,598802,223867,621398,503898,610672,634049,634203,436875,627163,610611,608916,608985,609063,634291,425088,538367,647508,610592,604861,608939,550356,647445,609120,645511,649253,627142,608962,593705,597528,634277,634182,418840,576175,647615,458995,566365,538405,513640,595556,621679,606844,432373,330078,565700,329598,573594,513642,526196,508293,538396,559078,538166,534111,528367,332698,373776,581309,608387,614749,644766,614732,644780,511319,480549,644795,614609,534057,512362,634151,534334,634808,614635,534429,277874,517847,625259,619265,422511,527189,442834,608250,432680,437469,565905,642054,495895,509377,432291,432695,606122,611371,611353,404430,323769,556680,492697,640766,151594,202067,566621,542031,520295,530649,617203,572795,651737,651753,633344,567628,572810,616043,615603,521480,552731,522424,522445,522937,517179,596810,294725,542999,517185,526553,536769,526490,315056,626546,315056,638715,604731,517259,454012,318906,566250,516324,536802,518194,454071,294982,190695,566239,560124,539064,286941,539065,526329,566268,516881,516320,628154,648090,639032,643618,597500,484177,614589,614650,614675,450176,439639,387792,419092,569666,615109,588136,608053,598292,636721,609891,610003,615121,623479,600679,598299,604314,609793,616218,580612,571920,580501,570418,570406,571914,598891,598676,528842,595819,597645,616201,383603,609172,616158,613588,609780,609624,528707,528880,470612,500237,217365,605499,454254,435102,574524,507920,647335,536574,532398,431527,481846,582090,401886,417802,510591,476059,566091,607874,625945,648697,616467,648678,651567,651658,614811,542095,651648,607864,613270,631815,609498,482021,569518,536838,589268,475376,569460,530032,587219,449906,603289,423624,570933,532122,566072,497927,566096,572141,134197,570951,566690,651647,587833,484703,588214,587449,548406,571845,572156,574919,570991,575253,574619,556213,481737,506334,521341,575201,575413,548391,481796,575383,556195,587095,587121,433850,571004,502548,563547,571095,642065,648267,483664,613102,586741,647844,647554,585317,648043,647518,621942,395931,691669,625131,492624,586651,538972,644155,563141,559845,563131,567259,651474,574130,570420,570427,472467,559457,524865,558829,563265,585076,556090,370270,550339,568062,575906,576198,558820,583318,238980,576181,558817,428827,322734,527908,456935,528084,469570,567103,574629,582631,602845,583618,369451,454469,568116,583915,541598,573306,601642,647550,601675,585703,649189,647850,470552,648193,649734,607150,651061,649011,547949,535818,649107,610536,522909,586714,522900,359980,384347,622841,630448,630586,630505,634694,630536,651615,612846,616424,616424,641640,518258,546519,568538,608937,559112,631651,522770,547378,555751,528261,528295,569946,540690,523995,589455,611601,612203,651187,613401,361103,560651,541157,489005,484592,549687,422031,360847,473694,650639,640516,624443,614638,492183,612889,592605,650770,648857,521452,649191,650064,643137,649998,644162,645124,677014,645372,625826,645066,683273,644246,645345,644203,657984,684838,615963,645515,655336,588667,670787,608382,615505,615934,632878,503940,506840,651580,370175,552852,565655,583558,527855,548536,586776,550084,527394,565647,557316,572259,640848,625378,614426,625587,625357,506463,559349,522803,615824,645017,297087,615897,650671,590465,587692,699400,674221,620067,630999,667667,478410,536684,613845,587113,589487,584425,590663,649489,583976,584459,549391,665458,549443,549487,492922,522706,645325,644574,569772,583894,552117,651655,548147,520735,641421,563617,649247,520033,648913,520418,651379,615851,642345,641393,649501,650232,548080,520433,577397,548045,557414,577562,519997,641482,568686,556285,560820,487750,445934,611689,604673,623971,503753,540151,492964,636681,543606,558917,470442,493203,567750,576563,576773,565440,567763,541855,375582,574727,333648,496931,565641,560147,408460,450989,440615,475321,385322,440811,553465,570813,604630,648970,513888,570793,586285,580792,576730,149145,624931,599779,599025,325858,380509,572541,570935,374914,418276,568633,136845,585927,524283,532292,558331,532523,636617,348922,509357,570800,506258,497249,565526,570543,304560,487776,570059,309327,563406,565257,573628,565255,499098,495840,486019,339659,526786,500703,471150,510885,330664,420193,492835,517090,439893,420045,584696,577189,480619,415906,517096,420683,420696,494382,400278,566426,573267,577218,565163,500692,566429,575695,332598,584752,581522,566014,578836,575827,511230,512717,576411,511020,571037,600892,533754,579809,557192,448746,533814,536354,542573,403333,493428,305838,540616,427353,402431,572923,525058,580430,579397,422785,450849,481461,573635,575387,550068,550074,533134,545360,535449,575992,573595,575397,573645,575454,575589,580897,575340,575463,450007,410399,450302,575952,573642,575594,576088,575426,580721,573588,575433,575439,575210,563058,543077,189452,398386,562451,562487,562496,546075,547893,406499,524018,523898,523898,565370,541872,574105,523875,523919,524226,569414,510103,499341,558517,558771,475294,566396,475299,471644,573447,538287,599683,419285,478662,599692,559665,558516,558731,264409,263222,264419,319736,573616,604282,606012,621635,603434,605255,609930,414458,603933,559773,612953,605986,615247,610634,606049,604267,604208,604269,449362,250513,332315,540136,504308,155549,586352,555196,546434,544660,177835,404023,346743,420418,543677,501947,573333,468944,544906,552838,453144,585162,530925,586314,364037,540746,511749,493079,609061,650767,599850,609281,649821,650688,650995,599878,619452,645502,611913,650564,645131,650940,598908,622488,619271,611858,651354,599393,607717,650191,608542,649863,600235,608374,623340,609525,649271,649103,605299,619990,650969,619407,603277,649001,651327,600168,550009,650196,374687,648817,526127,471497,236733,606673,591135,522430,260492,527670,572996,571254,257184,572798,546634,571262,564272,646788,649731,517531,649763,635675,647118,575025,571171,567928,565151,564903,291939,394328,563878,540309,537661,532717,465379,562516,464591,562546,465332,451490,546825,452483,520828,523477,525617,512904,421969,311647,525421,564278,520189,558143,511021,563382,567643,511016,543156,561982,563391,393681,522827,523859,518613,542796,398699,579714,510590,564688,467334,579756,549829,392854,648502,392854,531185,455344,529350,531113,531217,530545,647042,531118,569385,432415,521669,641728,616155,547161,548852,512730,613530,572464,547158,647926,396098,562447,533848,641798,462278,492026,642272,562243,647776,547109,647855,615939,562437,616555,547171,532056,562286,642456,616084,647795,641690,548843,572446,615687,613682,613740,574581,616122,547105,533965,547173,572862,642616,651272,439170,549196,510502,547481,647988,396716,504267,563065,563057,470904,508721,566279,508699,549102,566298,579898,440511,579860,528742,547453,566335,530414,554560,527770,425803,541998,418722,447692,539739,557924,468208,417169,488366,497473,515446,304550,266398,379309,124881,597208,610399,576733,545757,513013,523650,566597,519179,557771,402094,530661,527974,337059,525456,526131,530183,384682,578508,523335,582357,522968,522936,583953,538384,518936,456765,566634,493687,510438,296492,515848,477093,581311,530659,548302,546325,515776,537407,543510,595487,534405,536142,523935,536982,543498,529080,535184,469514,510573,503612,452102,507409,559713,539111,503614,544865,502246,567892,538532,567993,568299,567980,552864,552841,544872,568292,513742,344886,344917,584894,517409,525700,572458,569268,572325,572473,647377,536746,613743,615087,591783,421011,641576,393110,491702,612696,496483,613997,619012,471441,648882,599886,556423,599951,600231,632958,470566,285270,640456,612693,615083,599894,619009,111240,628596,633043,645261,643249,640464,626160,620779,621498,595654,599870,641586,573383,577682,574564,574593,572618,580538,314708,433639,420393,253955,151900,642381,603552,516295,637093,526603,638801,645592,643251,348183,610764,613685,604787,618592,613801,429445,634771,604683,614040,535059,613801,590022,559561,408015,554391,580507,585058,574119,567001,565602,369218,507652,613118,586361,581273,511961,506950,580146,506976,469492,514455,535650,564021,239843,530126,356005,563743,547019,521958,521967,562803,448548,422836,448611,521385,427620,577584,585433,577556,427007,525430,562315,215521,586034,466204,501505,501468,525973,501484,529486,502602,414141,458780,487385,560114,538841,574388,447870,543050,526367,631418,322074,614379,607737,607632,631235,635520,638474,633120,620662,465401,564852,564005,564539,587446,323333,522471,544672,571786,574613,517577,487413,487425,557175,533924,454141,582318,604391,559759,420461,517449,532915,545599,259131,449868,337894,333914,608389,622945,650319,394787,613150,644006,609375,582070,387482,509817,282234,304943,614653,544202,646477,528337,586619,600436,503280,494183,506227,602077,552886,421080,544229,646489,648307,496308,517775,509161,646458,493656,579594,579677,577391,506251,586594,498364,468067,503135,507001,578212,577552,563000,579635,435568,373504,503211,584268,525069,579825,574470,577502,579653,601833,578235,545105,577411,576876,495016,583159,498209,576969,583311,577517,372276,517795,576111,493664,584144,503316,118976,517801,494855,586489,385422,373526,578191,597648,601138,469258,597707,432923,598083,597623,200679,473529,451914,545831,597606,546734,573987,229182,578043,424794,431113,450455,602533,589555,184226,429822,599380,516399,450401,544980,599292,513448,406360,390169,628530,550241,213614,550377,589195,373527,527756,497827,497746,426083,346871,602837,520817,567741,575061,507811,528181,453044,527231,574998,453044,527279,301422,456702,395203,329765,514304,611785,595333,438980,583548,582519,607132,598330,629954,649443,630048,594970,510923,570179,573004,494422,569818,578178,499062,570058,569845,582584,494146,378937,516572,569787,499747,499778,515005,528307,605079,330331,328852,611719,532965,581258,504183,437479,333829,613211,584374,575799,187135,578174,312047,376808,574825,449366,584013,497568,583163,583273,584077,583827,584149,583732,543307,610618,504186,543445,588530,603782,589720,528717,380750,572711,346760,500804,348385,579467,527499,494505,636980,130917,596878,596928,532972,532794,431747,557899,473332,580676,529906,610539,585137,549201,612832,537368,583330,580714,584272,227526,562119,377200,522750,511796,501328,498427,537579,498934,576643,521601,554178,521269,559809,518667,498490,525240,520698,518675,567662,491741,561986,498973,529024,540841,530049,520453,520426,497333,527764,494929,537899,404961,494199,502430,528271,615264,596292,621770,605164,634183,466969,515020,500702,599425,467978,599320,605280,623065,642738,616360,553066,346984,572961,598864,644481,583799,631988,603841,567350,642003,616412,613432,439186,597654,641179,583683,605023,583468,492552,582295,364804,536503,577266,567764,310413,586718,536737,581537,308251,642640,599909,600532,600503,354618,556471,600449,600737,517723,561195,541793,514497,602339,596562,607126,611648,605991,573442,611715,611605,596990,521079,578383,281759,578452,417978,288995,526887,521070,535457,526478,555759,582899,578419,608674,600024,592972,398136,600096,589186,581077,600111,406811,596162,600046,406911,613403,395157,602518,395871,607184,600077,645153,576305,518455,518475,650963,407283,591109,613901,611554,618475,615416,616871,644175,641369,618425,622873,587630,410341,484623,258396,596497,563339,622978,567879,546167,555474,616701,651275,581142,506430,569153,506324,612809,479259,590104,480891,355000,448848,480814,440169,567867,567874,617129,547284,572305,119728,568574,355778,615689,608072,628076,608135,658675,615729,608098,608540,608605,608054,608254,610098,640858,608113,608167,640800,628101,645397,398447,520519,567141,421515,315791,520604,500343,381494,500340,502584,497483,514878,501054,386872,591895,594979,538132,579833,522772,573056,522433,570908,572902,582330,566635,588104,582267,570589,305436,526853,542516,613103,542719,548232,567074,572968,572195,628142,602146,506661,602161,565804,404149,627994,539211,561852,641321,561838,506701,601232,422054,590908,393697,408822,589500,288034,536210,564855,393615,379668,507369,392782,535218,558635,521909,507383,422046,614178,266386,267231,112837,110589,344894,388207,612177,586213,583144,400741,507332,460438,497778,505898,285271,590726,374447,312390,497680,516547,195035,382979,576249,571827,571842,386010,386010,436854,254143,375649,553648,397255,477327,578869,161522,564872,322241,502217,517698,375328,517612,507817,533288,198455,566828,491619,536247,605912,579051,563270,386894,507337,194321,523242,530973,373980,458381,392270,506677,585341,506842,585572,280998,381963,585575,582931,506570,585574,585519,511084,529012,588422,425322,428451,586791,553035,509833,344043,525501,544518,507261,529544,533001,496429,647601,120673,617642,415790,517496,497598,321224,524005,510415,512577,508848,515687,542845,327863,551372,405553,572911,537999,554671,528507,572935,538759,400195,564464,516103,609634,493690,639921,513582,497076,495948,578978,400717,572553,493691,572764,499440,585380,583968,567430,465312,572043,611486,610727,629741,599662,611455,627946,584176,456998,333083,570064,641208,567409,136404,439483,567404,450795,500205,581796,358037,444194,554708,525912,373569,357811,374103,318027,541608,584416,496335,609617,580080,372232,580647,391395,601307,584384,563499,145391,494838,563450,376434,227161,527473,521689,434545,521660,570862,300177,520696,568835,505454,568310,536831,568620,450343,469414,555978,450311,555927,525162,450106,505445,613298,199091,597218,571216,478301,577709,453382,296797,506185,478884,478914,494627,498069,470833,314403,524549,352334,479470,543816,543771,546832,562562,554972,562558,501572,531747,434299,434315,360088,549585,627660,535024,579333,651182,579362,450201,580489,286530,317843,549493,453979,597796,616332,616148,616220,532203,584360,582418,584326,470272,175936,587296,584539,587118,558031,587063,583670,587169,535660,260697,606174,615297,474668,496592,598808,524498,389573,492594,600013,136913,490624,603437,492731,493075,605984,476412,413040,596920,453168,599900,540401,616716,564324,651412,586956,543449,543541,543458,651730,650024,641647,612418,637708,651758,549312,644498,544375,644624,641748,651695,642280,650048,644655,649464,565471,642305,646158,607973,578036,579893,651678,649996,614896,651632,319067,651678,642180,644531,557186,642153,483793,650062,541331,581842,649413,607941,574398,613140,530533,644372,613402,420930,608297,616075,614615,591358,639049,608330,589062,613512,591308,614761,645741,639226,595959,591004,578600,579802,614318,645105,615285,608392,591333,588510,615998,493098,616100,591546,598916,645867,544285,589028,489216,589085,463137,613736,529814,598949,614506,557850,608241,614585,423562,497870,641558,493488,437578,577401,613667,570945,589043,614822,575666,575078,421098,587064,584203,578134,578112,574008,563124,240554,563158,423890,524759,554868,313682,579694,488190,613574,647072,602655,509573,602620,509699,593077,592997,602481,645825,609591,593058,613507,602543,645279,647001,645796,645336,645298,645384,648640,609677,373406,596590,587789,639955,596788,612267,602246,612621,589383,626356,368998,610091,626331,160384,646299,616408,160384,639909,562557,495108,569682,559594,503109,552300,565226,503026,513472,559945,502996,565108,565232,577578,498181,568057,540680,512703,497547,568475,570404,611206,612119,608468,608301,611053,611183,312345,545707,647517,624315,256960,613097,613295,285535,645743,478513,352560,390825,466513,219427,561493,645734,550973,633242,625968,530810,198721,614434,524043,614189,546505,614500,607861,521050,408583,603895,614107,542239,541142,219902,570052,378107,532125,568307,533077,301701,616781,610087,603620,614977,624605,616036,606313,610929,543845,609564,615987,650104,610875,612665,621046,610659,610984,603903,615389,604593,533518,585858,610560,615445,636888,616674,532421,616444,354858,333488,339790,615581,530282,610841,604941,614257,586264,578938,183838,578434,381557,498126,205438,494961,494809,415561,530209,525811,549289,551526,554177,552358,594572,591981,519471,403926,597807,588648,597862,588666,588641,600702,591970,588055,375251,479919,627511,650924,640168,596444,479892,535854,483126,375185,601404,461955,259186,443120,613338,606079,627082,639203,507153,548003,613470,599169,385258,493290,601358,597453,272023,601433,601511,558483,248940,608822,606131,507195,265306,606236,599124,600362,610648,601255,270574,601425,603349,597596,601464,508640,606096,507162,582160,558783,610625,582253,601233,500969,493314,613556,501135,613640,596442,650478,577346,599839,321382,606257,599141,500994,600476,531814,613384,610596,265107,582205,465687,613417,606194,500979,470914,500989,381359,601497,569223,443267,613780,501173,381702,388235,382289,560721,531562,568916,381306,435413,501287,539620,501271,302673,566614,381738,558238,586388,498202,375624,567042,385303,531701,539624,566770,382272,374250,501282,539608,566988,375629,509683,493355,569146,463632,382313,509582,495472,470600,375966,261369,569377,363561,142625,465658,560135,571500,564514,477772,493644,571995,614841,369207,554491,584824,572218,614033,572370,635451,507427,521128,561285,530287,507442,572904,584838,408711,427026,429725,505983,573195,498411,575806,449996,545457,493408,551201,568344,136425,512643,387623,518439,382775,498424,503581,436335,611746,476794,464567,550478,306829,559095,522694,568409,614847,614677,570131,502993,501961,500383,528950,382408,402514,495092,275613,402465,124970,529445,214096,618007,443274,466087,274298,497512,113799,132400,417386,497459,427751,211342,506005,586277,585466,515983,567244,553354,339640,119030,602019,590731,590763,605703,525597,598226,644222,148344,598236,275672,381043,285381,570390,418571,542852,572985,575837,614136,621069,649234,641355,649388,607570,614161,614347,397386,422295,626214,605904,649371,640205,423862,649414,641830,444850,614056,397258,387921,385350,315992,459231,459232,119894,369240,397796,520254,401781,393409,623509,131957,602154,623533,569412,519830,401306,551454,605601,599680,521030,598241,606295,526488,263790,651548,230807,303490,263806,526494,309999,628062,643139,612592,643126,644783,643147,642152,644447,578304,616767,609695,605408,418190,367956,644599,644642,598920,598012,585470,528656,585490,579116,557300,564032,564063,644762,588206,653350,649484,631038,603624,602588,639103,602621,602611,618469,630339,630367,602540,611435,526860,560387,526683,595630,597186,588616,613359,608994,609560,596699,595470,619782,596572,611516,596406,111242,407594,551640,565791,530944,648603,406634,587236,571700,585362,616222,616985,451687,617218,615081,616134,640888,615123,616102,543390,640924,617205,613514,615084,615060,472823,614048,640852,617130,613480,614956,549660,603052,617199,452004,627963,595044,626956,538024,431041,487648,307739,251252,607598,638707,612021,605170,647146,531725,574930,480334,533430,589065,589050,639924,639912,640699,638509,638929,601849,639897,651191,640714,637313,636823,419439,636753,651689,331635,374319,651280,612007,567876,525156,651347,395075,456228,614856,483918,567921,650354,650926,607439,651127,651588,611827,612531,337929,650199,518507,574278,574234,586402,611752,649835,589693,611445,611410,574375,563801,483271,372740,563795,532249,563823,568667,495325,315390,558615,563338,579459,579435,583870,573881,563331,564576,610884,410405,647267,614325,601889,614505,627930,649136,651341,608596,649188,601830,651466,601862,532552,614471,629476,651324,586063,580509,647321,608615,647400,608701,572162,651227,483939,489283,615357,651288,608647,601880,608919,610780,651670,651696,608630,601813,614278,614366,614436,601927,629068,601845,535320,556776,488856,651507,614380,483909,485503,579651,581006,491416,532463,406686,581025,572179,485502,480809,326447,579643,651313,613244,647502,646982,651420,612851,533325,403520,647519,642648,646987,444880,568083,484544,560843,568151,364710,576527,454485,196738,646816,484544,651715,651761,639571,604247,364714,248304,646968,549002,243142,557026,546688,371486,650920,568088,549024,530909,546707,370369,585324,582044,530896,532659,512265,554719,557064,582839,490697,534004,554711,439684,529653,554686,451538,438098,221647,480149,512262,549937,639797,639552,607456,276585,609403,251805,611928,528797,572991,573059,609017,606865,608888,608835,593225,606931,515819,435861,515809,483008,530888,417084,534703,412458,435740,389609,533860,534233,242032,462209,242032,463224,371294,537491,481463,458869,533316,548018,646317,484985,581631,562635,551600,646393,554496,649291,649368,535146,581506,481918,485107,464995,457124,651559,561005,560226,585766,469868,560797,575260,438523,585755,575686,491667,469844,491547,470007,647588,491693,568793,485072,469834,481406,632677,627471,645255,623341,647401,627566,631857,632190,647243,649186,642972,337308,545051,301014,292608,457362,349563,409352,479329,548736,599810,604048,574586,567024,335879,573140,566991,566950,573160,598643,249909,615139,600093,602722,622927,613436,496818,613871,496776,581013,619417,381425,620470,554489,568972,579527,579474,532090,615484,632508,641980,629917,640331,641876,625893,632603,615940,616631,616707,406507,623967,517922,624215,623124,576660,580263,580033,550692,581190,578340,579986,581116,580937,580070,540481,218397,571401,599531,565498,614776,494912,607899,614621,599673,599867,460083,547948,548104,611590,584660,588259,588924,398329,606589,588833,593727,120978,574264,557310,434884,557835,512252,422394,576328,379279,497140,573764,373707,504460,531497,528194,498236,380426,498231,380468,558889,276743,512583,558606,259290,614791,608173,124684,576899,577925,576729,576924,556715,593191,517942,518002,548258,548228,581508,530204,527817,499250,606749,606576,446571,552473,550155,547264,236621,647194,499474,647355,598649,517378,530321,632923,518160,522506,586060,650162,650144,390160,545452,650114,547639,585853,241825,586804,241825,545550,578589,651705,374588,582227,650074,545500,650551,636502,577641,650649,650754,555859,504017,586634,428126,621128,576646,512578,572671,514076,512814,521292,514049,524210,528753,459739,445028,539025,549175,432969,567484,540889,314446,473217,522617,443223,521841,334061,529738,602382,602392,548942,600718,601428,638552,547632,391574,560063,417403,554596,650291,637209,650306,637233,650282,616439,613832,616484,610701,576467,616524,524027,523948,555547,611654,572903,463234,576430,481727,524082,538976,578285,580152,579274,579363,578454,579555,578313,535033,651562,484449,611821,594018,603400,612976,588476,548199,212556,606829,596356,623192,607089,601855,598045,589117,311197,608641,607575,327262,375987,311319,486773,585825,613253,644114,541912,583053,254996,695619,362994,434043,293973,647839,602729,260485,602771,535036,611848,611805,648037,651491,651218,610277,611293,616246,651350,612319,651344,260629,650998,616279,611051,612883,545551,609041,158612,527090,550610,566422,569996,587176,576145,587300,613303,397844,569944,570021,540215,576059,474925,568330,569930,569965,256521,567302,651449,587220,567256,569652,576114,624540,220049,285743,544877,514231,543876,446359,293258,543633,591403,211543,363374,235695,597685,353889,639409,498038,553098,582108,311137,588078,311164,541006,521870,630898,603831,573918,287082,258482,572610,572174);
		return $studentIds;
	}
	
	public function tempgetEnrollmentListByAiCode($ai_code,$course=null,$stream=null,$resultType=null,$offset=0,$limit=50){

		$enrollmentList = array();
		$ai_code_conditions = array();
		if(!empty($course) && $course==!null){
			$ai_code_conditions["students.course"] = @$course;
		}
		$studentIds = $this->tempgetIssueMarksheetStudents();

		// if(!empty($stream) && $stream==!null){
		// 	$ai_code_conditions["students.stream"] = @$stream;
		// }
		//$ai_code_conditions["student_allotments.exam_year"] = CustomHelper::_get_selected_sessions();
		//$ai_code_conditions["student_allotments.exam_month"] = @$stream;
		$ai_code_conditions["exam_results.exam_year"] = CustomHelper::_get_selected_sessions();
		$ai_code_conditions["exam_results.exam_month"] = @$stream;
		if($ai_code > 0){
			$ai_code=$this->getAiCentersmappeduserdatacode($ai_code);

			if($resultType=='PASS'){
				/*$enrollmentList=StudentAllotment::join('exam_results','exam_results.student_id','=','student_allotments.student_id')
				->join('students','students.id','=', 'student_allotments.student_id')
				->whereIn('students.ai_code',$ai_code)
				->where($ai_code_conditions)
				->where('exam_results.final_result', 'PASS')
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNull('student_allotments.deleted_at')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->skip($offset)->take($limit)
				->get(array('students.enrollment')); */
				$enrollmentList = Student::join('exam_results','exam_results.student_id','=', 'students.id')
				->whereIn('students.ai_code', $ai_code)
				->whereIn('students.id', $studentIds)
				->where($ai_code_conditions)
				->where('exam_results.final_result', 'PASS')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->skip($offset)->take($limit)
				->get(array('students.enrollment'));

			} else if(empty($resultType) || $resultType==null){

				/*$enrollmentList=StudentAllotment::join('exam_results','exam_results.student_id','=','student_allotments.student_id')
				->join('students','students.id','=', 'student_allotments.student_id')
				->whereIn('students.ai_code',$ai_code)
				->where($ai_code_conditions)
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNull('student_allotments.deleted_at')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->skip($offset)->take($limit)
				->get(array('students.enrollment'));*/


				$enrollmentList = Student::join('exam_results','exam_results.student_id','=', 'students.id')
				->whereIn('students.ai_code', $ai_code)
				->whereIn('students.id', $studentIds)
				->where($ai_code_conditions)
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->skip($offset)->take($limit)
				->get(array('students.enrollment'));
			} else {

				/*$enrollmentList=StudentAllotment::join('exam_results','exam_results.student_id','=','student_allotments.student_id')
				->join('students','students.id','=', 'student_allotments.student_id')
				->whereIn('students.ai_code',$ai_code)
				->where($ai_code_conditions)
				->whereNull('students.deleted_at')
				->whereNull('exam_results.deleted_at')
				->whereNull('student_allotments.deleted_at')
				->where('exam_results.final_result', '!=' , 'PASS')
				->orderBy('students.enrollment','ASC')
				->groupBy('students.enrollment')
				->skip($offset)->take($limit)
				->get(array('students.enrollment')); */


				$enrollmentList = Student::join('exam_results','exam_results.student_id','=', 'students.id')
				->whereIn('students.ai_code', $ai_code)
				->where($ai_code_conditions)
				->whereIn('students.id', $studentIds)
				->where('exam_results.final_result', '!=' , 'PASS')
				->whereNotNull('students.enrollment')
				->orderBy('students.enrollment','ASC')
				->skip($offset)->take($limit)
				->get(array('students.enrollment'));
			}
		}

		return $enrollmentList;
	}

	public function getDocumentVerificationStartData(){
		$objController = new Controller();
		$combo_name = 'student_document_verification_start_date';
		$theory_start_date_arr = $objController->master_details($combo_name);
		return @$theory_start_date_arr[1];
	}

	public function getDocumentVerificationEndData(){
		$objController = new Controller();
		$combo_name = 'student_document_verification_end_date';
		$theory_end_date_arr = $objController->master_details($combo_name);
		return @$theory_end_date_arr[1];

	}

	public function getAICenterDocumentVerificationStartData(){
		$objController = new Controller();
		$combo_name = 'aicenter_fresh_form_document_verification_start_date';
		$theory_start_date_arr = $objController->master_details($combo_name);
		return @$theory_start_date_arr[1];
	}
	
	public function getAICenterDocumentVerificationEndData(){
		$objController = new Controller();
		$combo_name = 'aicenter_fresh_form_document_verification_end_date';
		$theory_end_date_arr = $objController->master_details($combo_name);
		return @$theory_end_date_arr[1];

	}

	public function getDocumentVerificationAllowOrNot($role_id=null){
		$isValid = false;
		$verifier_id = Config::get('global.verifier_id');
		$super_admin_id = Config::get('global.super_admin_id');
		$academicofficer_id = Config::get('global.academicofficer_id');
		$thoeryStartData = null;
		$thoeryEndData = null;
		if($role_id == $verifier_id){
			$thoeryStartData=$this->getVerifierDocumentVerificationStartData();
			$thoeryEndData=$this->getVerifierDocumentVerificationEndData();
		}else if($role_id == $super_admin_id){
			$thoeryStartData=$this->getDeptDocumentVerificationStartData();
			$thoeryEndData=$this->getDeptDocumentVerificationEndData();
		}else if($role_id == $academicofficer_id){
			$thoeryStartData=$this->getAODocumentVerificationStartData();
			$thoeryEndData=$this->getAODocumentVerificationEndData();
		}
		// if(strtotime(date("Y-m-d H:i:s")) <= strtotime($thoeryEndData)){
			// echo "Valid";
		// }

		// if(strtotime(date("Y-m-d H:i:s")) >= strtotime($thoeryStartData)){
			// echo " Valid ";
		// }

		// echo (date("Y-m-d H:i:s")) . " -- ". ($thoeryStartData) . " -- ".  (date("Y-m-d H:i:s")) . " -- ". ($thoeryEndData);die;
		if(strtotime(date("Y-m-d H:i:s")) >= strtotime($thoeryStartData) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime($thoeryEndData)){
			$isValid = true;
		}

		return $isValid;
	}

	public function getVerifierDocumentVerificationStartData(){
		$objController = new Controller();
		$combo_name = 'verifier_fresh_form_document_verification_start_date';
		$theory_start_date_arr = $objController->master_details($combo_name);
		return @$theory_start_date_arr[1];
	}

	public function getVerifierDocumentVerificationEndData(){
		$objController = new Controller();
		$combo_name = 'verifier_fresh_form_document_verification_end_date';
		$theory_end_date_arr = $objController->master_details($combo_name);
		return @$theory_end_date_arr[1];

	}

	public function getDeptDocumentVerificationStartData(){
		$objController = new Controller();
		$combo_name = 'department_fresh_form_document_verification_start_date';
		$theory_start_date_arr = $objController->master_details($combo_name);
		return @$theory_start_date_arr[1];
	}

	public function getDeptDocumentVerificationEndData(){
		$objController = new Controller();
		$combo_name = 'department_fresh_form_document_verification_end_date';
		$theory_end_date_arr = $objController->master_details($combo_name);
		
		return @$theory_end_date_arr[1];
		
	}  

	public function getAODocumentVerificationStartData(){
		$objController = new Controller();
		$combo_name = 'ao_fresh_form_document_verification_start_date';
		$theory_start_date_arr = $objController->master_details($combo_name);
		return @$theory_start_date_arr[1];
	}
	
	public function getAODocumentVerificationEndData(){
		$objController = new Controller();
		$combo_name = 'ao_fresh_form_document_verification_end_date';
		$theory_end_date_arr = $objController->master_details($combo_name);

		return @$theory_end_date_arr[1];

	}

	public function _replaceTheStringWithX($inputString=null){
		if(@$inputString){
			$inputString = str_pad(substr($inputString, -4), strlen($inputString), 'X', STR_PAD_LEFT);
			return $inputString;
		}else{
			return null;
		}
	}
	
	public function getExamcenterData($formId=null,$isPaginate=true){
		$conditions = Session::get('_condtions');

		if($isPaginate){
			// $defaultPageLimit = config("global.defaultPageLimit");
			$defaultPageLimit = 1000;
			$master = ExamcenterDetail::with('userdata')->where($conditions)->paginate($defaultPageLimit, array('id','ecenter10','ecenter12','ai_code','capacity','exam_incharge','mobile','created_at','center_supdt','cent_name'));

		}else{
			$master = ExamcenterDetail::with('userdata')->where($conditions)->get();
		}

		return $master;
	}

	public function checkAdminSuppFeeEntredOrNot($student_id=null,$supp_id=null,$exam_year=null,$exam_month=null){
		$res = false;
		$current_exam_month = $exam_year;
		$current_exam_year = $exam_month;

		$mainTable = "supplementaries";
		$conditions['id'] = $supp_id;
		$conditions['exam_year'] = $current_exam_year;
		$conditions['exam_month'] = $current_exam_month;
		$suppForCurrentYearCount = DB::table($mainTable)->where($conditions)->orderBy('id','ASC')->count();
		if(@$suppForCurrentYearCount > 0){
			$fee_data_arr = DB::table('supp_student_fees')->where('supplementary_id',$supp_id)->count();
			if( $fee_data_arr > 0 ){
				$res = true;
			}
		}else{
			$res = true;
		}
		return $res;
	}

	public function getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,$is_aicenter_verify=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['exam_year'] = $selected_session;
		$conditions['is_aicenter_verify'] = $is_aicenter_verify;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
		}
		if(!empty($aicenter_mapped_data) && $aicenter_mapped_data!=null){
			$supplementary_eligible_total_registred_all_students = Supplementary::where($conditions)->whereIn('ai_code',$aicenter_mapped_data)->count();
		}else{
			$supplementary_eligible_total_registred_all_students = Supplementary::where($conditions)->count();
		}
		return $supplementary_eligible_total_registred_all_students;
	}
	
	public function getPendingDepartmentLevelSupplementaryallStudentCount($exam_month,$is_department_verify=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions['exam_year'] = $selected_session;
		$conditions['is_aicenter_verify'] = 2;
		$conditions['is_department_verify'] = $is_department_verify;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
		}
		// dd($conditions);
		/* chnage query 28-11-2024
		$supplementary_eligible_total_registred_all_students = Supplementary::where($conditions)->count();
		return $supplementary_eligible_total_registred_all_students;*/
		// $supplementary_eligible_total_registred_all_students = Supplementary::where($conditions)->where('locksumbitted',1)->whereNotNull('challan_tid')->count();
		$supplementary_eligible_total_registred_all_students = Supplementary::where($conditions)->count();
		return $supplementary_eligible_total_registred_all_students;
	}
	
	public function isCurrentRoleVerificationAllowOrNot(){
		$isValid = false;
		$role_id = @Session::get('role_id');
		$objController = new Controller();
		$startDate = null;
		$endDate = null;
		if($role_id == Config::get('global.aicenter_id')){
			$combo_name = 'aicenter_supp_document_verification_start_date';
			$startDate = $objController->master_details($combo_name);
			$startDate = $startDate[1];
			$combo_name = 'aicenter_supp_document_verification_end_date';
			$endDate = $objController->master_details($combo_name);
			$endDate = $endDate[1];
		}else if($role_id == Config::get('global.examination_department')){
			$combo_name = 'department_supp_document_verification_start_date';
			$startDate = $objController->master_details($combo_name);
			$startDate = $startDate[1];
			$combo_name = 'department_supp_document_verification_end_date';
			$endDate = $objController->master_details($combo_name);
			$endDate = $endDate[1];
		}
		if($role_id == Config::get('global.aicenter_id') || $role_id == Config::get('global.examination_department')){
			if(strtotime(date("Y-m-d H:i:s")) >= strtotime($startDate) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime($endDate)){
				//$isValid = true;
			}
		}
		if(!$isValid){
			$custom_component_obj = new CustomComponent();
			$isAdminStatus = $custom_component_obj->_checkIsAdminRole();
			if($isAdminStatus){
				$isValid = true;
			}
		}
		return $isValid;
	}
	
	public function isCurrentRoleFreshVerificationAllowOrNot(){
		$isValid = false;
		$role_id = @Session::get('role_id');
		$objController = new Controller();
		$startDate = null;
		$endDate = null;
		if($role_id == Config::get('global.aicenter_id')){
			$combo_name = 'aicenter_fresh_form_document_verification_start_date';
			$startDate = $objController->master_details($combo_name);
			$startDate = $startDate[1];
			$combo_name = 'aicenter_fresh_form_document_verification_end_date';
			$endDate = $objController->master_details($combo_name);
			$endDate = $endDate[1];
		}else if($role_id == Config::get('global.examination_department')){
			$combo_name = 'department_fresh_form_document_verification_start_date';
			$startDate = $objController->master_details($combo_name);
			$startDate = $startDate[1];
			$combo_name = 'department_fresh_form_document_verification_end_date';
			$endDate = $objController->master_details($combo_name);
			$endDate = $endDate[1];
		}
		if($role_id == Config::get('global.aicenter_id') || $role_id == Config::get('global.examination_department')){
			if(strtotime(date("Y-m-d H:i:s")) >= strtotime($startDate) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime($endDate)){
				$isValid = true;
			}
		}
		if(!$isValid){
			$custom_component_obj = new CustomComponent();
			$isAdminStatus = $custom_component_obj->_checkIsAdminRole();
			if($isAdminStatus){
				$isValid = true;
			}
		}
		return $isValid;
	}
	
	public function examYearExamMonthGetResultStudentDataMarksheet($enrollment=null,$dob=null,$exam_year=null,$exam_month=null){
		$dob = date("Y-m-d",strtotime($dob));

		$result = DB::select('call examYearExamMonthGetStudentResult123(?,?,?,?)',array($enrollment,$dob,$exam_year,$exam_month));
		if(@$result[0]){
			return $result[0];
		}

		return false;


	}

    public function revalAllowOrNot(){
        $objController = new Controller();
        $combo_name = 'reval_start_date';
        $reval_start_date= $objController->master_details($combo_name);
        $reval_start_date=@$reval_start_date[1];

        $combo_name = 'reval_end_date';
        $reval_end_date= $objController->master_details($combo_name);
        $reval_end_date=@$reval_end_date[1];

        $isValidOutput = false;
        if(strtotime(date("Y-m-d H:i:s")) >= strtotime(@$reval_start_date) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime(@$reval_end_date)){
            $isValidOutput = true;
        }
        return $isValidOutput;
    }
	
	public function fsdvAllowOrNot(){
	    $objController = new Controller();
		$combo_name = 'fsdv_start_date';
		$reval_start_date = $objController->master_details($combo_name);
		$reval_start_date=@$reval_start_date[1];
		$isValid = false;
		$objController = new Controller();
		$combo_name = 'fsdv_start_date';
		$reval_start_date= $objController->master_details($combo_name);
		$combo_name = 'fsdv_end_date';
		$reval_end_date = $objController->master_details($combo_name);
		$reval_end_date=@$reval_end_date[1];

		if(strtotime(date("Y-m-d H:i:s")) >= strtotime(@$reval_start_date) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime(@$reval_end_date)){
			$isValid = true;
		}
		return $isValid;
	}
	
	public function _getRevalExamMonth(){
		$objController = new Controller();
		$combo_name = 'reval_exam_month';
		$reval_exam_month = $objController->master_details($combo_name);
		$reval_exam_month=@$reval_exam_month[1];
		return $reval_exam_month;
	}
	
	public function getRevalApplicationData($formId=null,$isPaginate=true,$aicenter_mapped_data_conditions=null){
         $role_id = Session::get('role_id');
		 $aicenter_id_role = config("global.aicenter_id");
		$conditions = Session::get($formId. '_conditions');
		$aicenter_mapped_data_conditions = Session::get($formId. '_aicenter_mapped_data_conditions');



		$symbolssoid = Session::get($formId. '_symbolssoid');
		$tempCondssoid=array();
		$symbol = Session::get($formId. '_symbol');
		$symbols = Session::get($formId. '_symbols');
		$symbol2 = Session::get($formId. '_symbol2');
		$symbolss = Session::get($formId. '_symbolss');
		$arraykeys=array_keys($conditions);
		if(in_array('reval_students.late_fees',$arraykeys)){
				unset($conditions["reval_students.late_fees"]);
				if(@$symbol){
					$tempCond = array('reval_students.late_fees',$symbol,'0');

				}
		}


        if(in_array('students.ssoid',$arraykeys)){
				unset($conditions["students.ssoid"]);
				if(@$symbolssoid){
					$tempCondssoid = array('students.ssoid',$symbolssoid,null);

				}
		}

		if(in_array('students.ssoid2',$arraykeys)){
			$conditions['students.ssoid']=$conditions['students.ssoid2'];
			unset($conditions["students.ssoid2"]);
		}

		if(in_array('reval_students.total_fees',$arraykeys)){
				unset($conditions["reval_students.total_fees"]);
				if(@$symbol){
					$tempConds = array('reval_students.total_fees',$symbols,'0');
				}
		}

		if(in_array('reval_students.total_fees2',$arraykeys)){
			$conditions["reval_students.total_fees"]=$conditions["reval_students.total_fees2"];
			unset($conditions["reval_students.total_fees2"]);
	    }

		if(in_array('reval_students.is_self_filled',$arraykeys)){

			unset($conditions["reval_students.is_self_filled"]);
			if(@$symbolss){
				$suppTempCod = array('reval_students.is_self_filled',$symbolss,null);
			}
		}
		if(in_array('reval_students.challan_tid2',$arraykeys)){
			unset($conditions["reval_students.challan_tid2"]);
			if(@$symbol2){
				$tempCond2 = array('reval_students.challan_tid',$symbol2,null);
			}
	    }

		/* Start End Date */
		$rawQueryDateTime = 1;
		$table_name = "reval_students";$field_name = "created_at";//locksubmitted_date
		if(@$conditions[$table_name.'.start_date'] || @$conditions[$table_name. '.end_date'] ){
			$rawQueryDateTime = CustomHelper::getStartAndEndDate(@$conditions[$table_name.'.start_date'],@$conditions[$table_name.'.end_date'],$table_name,$field_name);
			unset($conditions[$table_name.".start_date"]);
			unset($conditions[$table_name.".end_date"]);
		}
		/* Start End Date */
		$controller_obj = new Controller;
		$combo_name = 'reval_exam_year';$reval_exam_year = $controller_obj->master_details($combo_name);
		$combo_name = 'reval_exam_month';$reval_exam_month = $controller_obj->master_details($combo_name);

		$reval_exam_year = @$reval_exam_year[1];$reval_exam_month = @$reval_exam_month[1];


		$master = array();
		if($role_id == $aicenter_id_role){
			if($isPaginate){
				$defaultPageLimit = config("global.defaultPageLimit");
				$master = RevalStudent::leftJoin('students', 'students.id', '=', 'reval_students.student_id')
				->leftJoin('applications', 'applications.student_id', '=', 'reval_students.student_id')
				->where($conditions)
				->whereIn('reval_students.ai_code',$aicenter_mapped_data_conditions)
				->whereRaw($rawQueryDateTime)
				->where(@$tempCond[0],@$tempCond[1],@$tempCond[2])
				->where(@$tempCond2[0],@$tempCond2[1],@$tempCond2[2])
				->where(@$tempConds[0],@$tempConds[1],@$tempConds[2])
				->where(@$suppTempCod[0],@$suppTempCod[1],@$suppTempCod[2])
				->where(@$tempCondssoid[0],@$tempCondssoid[1],@$tempCondssoid[2])
				->paginate($defaultPageLimit,array('reval_students.id','reval_students.student_id','reval_students.ai_code',
				'reval_students.stream','reval_students.course','reval_students.submitted','reval_students.challan_tid',
				'reval_students.locksumbitted','reval_students.enrollment','reval_students.total_fees','students.name','students.gender_id',
					'students.adm_type','reval_students.exam_month','reval_students.reval_type','reval_students.is_self_filled','reval_students.is_eligible','Students.ssoid'));
			}else{

				$master = RevalStudent::leftJoin('students', 'students.id', '=', 'reval_students.student_id')
				->leftJoin('applications', 'applications.student_id', '=', 'reval_students.student_id')
				->where($conditions)
				->whereRaw($rawQueryDateTime)
				->where(@$tempCond[0],@$tempCond[1],@$tempCond[2])
				->where(@$tempCond2[0],@$tempCond2[1],@$tempCond2[2])
				->where(@$tempConds[0],@$tempConds[1],@$tempConds[2])
				->where(@$suppTempCod[0],@$suppTempCod[1],@$suppTempCod[2])
				->where(@$tempCondssoid[0],@$tempCondssoid[1],@$tempCondssoid[2])
				->whereIn('reval_students.ai_code',$aicenter_mapped_data_conditions)
				->get(array('reval_students.id','reval_students.student_id','reval_students.ai_code',
				'reval_students.stream','reval_students.course','reval_students.submitted','reval_students.challan_tid',
				'reval_students.locksumbitted','reval_students.enrollment','reval_students.total_fees','students.name','students.gender_id',
					'students.adm_type','reval_students.exam_month','reval_students.reval_type','reval_students.is_self_filled','reval_students.is_eligible','Students.ssoid'));
			}
		}else{
			if($isPaginate){
				$rawQuery1 = " rs_student_allotments.exam_year = " . $reval_exam_year . " and rs_student_allotments.exam_month = " . $reval_exam_month . "  ";
				$defaultPageLimit = config("global.defaultPageLimit");
				$master = RevalStudent::leftJoin('students', 'students.id', '=', 'reval_students.student_id')
				->leftJoin('applications', 'applications.student_id', '=', 'reval_students.student_id')
				->leftJoin("student_allotments",function($join) use($reval_exam_year,$reval_exam_month){
					$join->on("student_allotments.id" , "=" ,"reval_students.student_id");
					$join->whereRaw("(rs_student_allotments.exam_year = " . $reval_exam_year ." )");
					$join->whereRaw("(rs_student_allotments.exam_month = " . $reval_exam_month ." )");
				})
				->leftJoin('examcenter_details', 'student_allotments.examcenter_detail_id', '=', 'examcenter_details.id')
				->where($conditions)
				->whereRaw($rawQueryDateTime)
				->where(@$tempCond[0],@$tempCond[1],@$tempCond[2])
				->where(@$tempCond2[0],@$tempCond2[1],@$tempCond2[2])
				->where(@$tempConds[0],@$tempConds[1],@$tempConds[2])
				->where(@$suppTempCod[0],@$suppTempCod[1],@$suppTempCod[2])
				->where(@$tempCondssoid[0],@$tempCondssoid[1],@$tempCondssoid[2])
				->paginate($defaultPageLimit,array('examcenter_details.fixcode as examcenter_fixcode','student_allotments.fixcode','student_allotments.examcenter_detail_id','reval_students.id','reval_students.student_id','reval_students.ai_code',
				'reval_students.stream','reval_students.course','reval_students.submitted','reval_students.challan_tid',
				'reval_students.locksumbitted','reval_students.enrollment','reval_students.total_fees','students.name','students.gender_id',
					'students.adm_type','reval_students.exam_month','reval_students.reval_type','reval_students.is_self_filled','reval_students.is_eligible','Students.ssoid'));

			}else{
				$master = RevalStudent::leftJoin('students', 'students.id', '=', 'reval_students.student_id')
				->leftJoin('applications', 'applications.student_id', '=', 'reval_students.student_id')
				->leftJoin("student_allotments",function($join){
					$join->on("student_allotments.id" , "=" ,"reval_students.student_id");
				})
				->leftJoin('examcenter_details', 'student_allotments.examcenter_detail_id', '=', 'examcenter_details.id')
				->where($conditions)
				->where(@$tempCond[0],@$tempCond[1],@$tempCond[2])
				->where(@$tempCond2[0],@$tempCond2[1],@$tempCond2[2])
				->where(@$tempConds[0],@$tempConds[1],@$tempConds[2])
				->where(@$tempCondssoid[0],@$tempCondssoid[1],@$tempCondssoid[2])
				->whereRaw($rawQueryDateTime)
				->get(array('examcenter_details.fixcode as examcenter_fixcode','student_allotments.fixcode','student_allotments.examcenter_detail_id','reval_students.id','reval_students.student_id','reval_students.ai_code',
				'reval_students.stream','reval_students.course','reval_students.submitted','reval_students.challan_tid',
				'reval_students.locksumbitted','reval_students.enrollment','reval_students.total_fees','students.name','students.gender_id',
					'students.adm_type','reval_students.exam_month','reval_students.reval_type','reval_students.is_self_filled','reval_students.is_eligible','Students.ssoid'));

			}
		}
		return $master;
	}
	
	public function _checkPraticalSlotAllowOrNotAllow(){
		$objController = new Controller();
		$combo_name = 'slot_create_start_date';
		$combo_name2 = 'slot_create_end_date';
		$sessional_start_date_arr = $objController->master_details($combo_name);
		$sessional_start_end_arr = $objController->master_details($combo_name2);
		if(strtotime(date("Y-m-d H:i:s")) >= strtotime($sessional_start_date_arr[1]) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime($sessional_start_end_arr[1])){
			$isValid = true;
		}else{
			$isValid = false;
		}
		return $isValid;
	}
	
	public function getStudentOrgFees($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_condtions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = StudentOrgFee::Join('students', 'students.id', '=', 'student_org_fees.student_id')->Join('applications', 'applications.student_id', '=', 'student_org_fees.student_id')
			->where($conditions)->paginate($defaultPageLimit);
		}else{
			$master = StudentOrgFee::Join('students', 'students.id', '=', 'student_org_fees.student_id')->Join('applications', 'applications.student_id', '=', 'student_org_fees.student_id')
			->where($conditions)->get();
		}
		return $master;
	}
	
	public function _checkSessionalMarksAPIEntryAllowOrNotAllow(){
		$objController = new Controller();
		$combo_name = 'sessional_api_marks_start_date';
		$combo_name2 = 'sessional_api_marks_end_date';
		$sessional_start_date_arr = $objController->master_details($combo_name);
		$sessional_start_end_arr = $objController->master_details($combo_name2);
		if(strtotime(date("Y-m-d H:i:s")) >= strtotime($sessional_start_date_arr[1]) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime($sessional_start_end_arr[1])){
			$isValid = true;
		}else{
			$isValid = false;
		}
		return $isValid;
	}
	
	public function hallticketAllowIps(){
		$showStatus = false;
		$objController = new Controller();
		$result_process_allowed_ips = null;
        $request_client_ip = Config::get('global.request_client_ip');
        $combo_name="hall_ticket_allowed_ips";$hall_ticket_allowed_ips=$objController->master_details($combo_name);
		if(@$hall_ticket_allowed_ips['1']){
			$result_process_allowed_ips =json_decode(@$hall_ticket_allowed_ips['1'],true);
		}
		if(!empty($result_process_allowed_ips)){
			if(in_array($request_client_ip,$result_process_allowed_ips)){
				$showStatus = true;
			}
		}else{
		 $showStatus = true;
		}
		return $showStatus;

	}
	
	public function getIsAllowToShowAdmitCardDownloadForAll(){
		$status = false;
		$objController = new Controller();
		$combo_name = 'student_download_admit_card_start_date';
		$combo_name2 = 'student_download_admit_card_end_date';
		$hall_ticket_start_date_arr = $objController->master_details($combo_name);
		$hall_ticket_start_end_arr = $objController->master_details($combo_name2);

		if(strtotime(date("Y-m-d H:i:s")) >= strtotime($hall_ticket_start_date_arr[1]) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime($hall_ticket_start_end_arr[1])){
			$status = true;
		}
		return $status;
	}
	
	 public function getPracticalmappedallCount($exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$Practicalexam_year = $selected_session;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
		  $Practicalexam_month = $exam_month;
		}
		$Practical_mapped_registered_count =  DB::select("SELECT count(sam.id) FROM rs_student_allotment_marks sam WHERE sam.exam_year = 125 AND sam.exam_month = 1 AND sam.deleted_at IS NULL AND sam.is_exclude_for_practical = 0 AND sam.subject_id IN ( SELECT id FROM rs_subjects WHERE practical_type = 1 ) GROUP BY sam.subject_id, sam.examcenter_detail_id ;");

		$Practical_mapped_registered_counts = count($Practical_mapped_registered_count);
		return $Practical_mapped_registered_counts;
	}
	
	public function getPracticalmappedCount($exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$Practicalexam_year = $selected_session;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
		  $Practicalexam_month = $exam_month;
		}
		$Practical_mapped_count =  DB::select("SELECT count(sam.id) FROM rs_student_allotment_marks sam LEFT JOIN rs_user_examiner_maps uem ON uem.id = sam.user_examiner_map_id AND uem.exam_year = 125 AND uem.exam_month = 1 AND uem.deleted_at IS NULL LEFT JOIN rs_users deouser ON sam.user_deo_id = deouser.id LEFT JOIN rs_users practicaluser ON sam.practical_examiner_id = practicaluser.id INNER JOIN rs_student_allotments sa ON sa.student_id = sam.student_id AND sa.deleted_at IS NULL AND sa.exam_year = 125 AND sa.exam_month = 1 LEFT JOIN rs_examcenter_details ec ON sam.examcenter_detail_id = ec.id WHERE sam.exam_year = 125 AND sam.exam_month = 1 AND sam.deleted_at IS NULL AND sam.is_exclude_for_practical = 0 AND sam.subject_id IN ( SELECT id FROM rs_subjects WHERE practical_type = 1 ) and uem.user_deo_id is not null GROUP BY sam.subject_id, sam.examcenter_detail_id limit 2");

		$Practical_mapped_counts = count($Practical_mapped_count);
		return $Practical_mapped_counts;
	}
	
	public function getPracticalnotmappedCount($exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$Practicalexam_year = $selected_session;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
		  $Practicalexam_month = $exam_month;
		}
		$Practical_not_mapped_count =  DB::select("SELECT count(sam.id) FROM rs_student_allotment_marks sam LEFT JOIN rs_user_examiner_maps uem ON uem.id = sam.user_examiner_map_id AND uem.exam_year = 125 AND uem.exam_month = 1 AND uem.deleted_at IS NULL LEFT JOIN rs_users deouser ON sam.user_deo_id = deouser.id LEFT JOIN rs_users practicaluser ON sam.practical_examiner_id = practicaluser.id INNER JOIN rs_student_allotments sa ON sa.student_id = sam.student_id AND sa.deleted_at IS NULL AND sa.exam_year = 125 AND sa.exam_month = 1 LEFT JOIN rs_examcenter_details ec ON sam.examcenter_detail_id = ec.id WHERE sam.exam_year = 125 AND sam.exam_month = 1 AND sam.deleted_at IS NULL AND sam.is_exclude_for_practical = 0 AND sam.subject_id IN ( SELECT id FROM rs_subjects WHERE practical_type = 1 ) and uem.user_deo_id is null GROUP BY sam.subject_id, sam.examcenter_detail_id limit 2");

		$Practical_not_mapped_counts = count($Practical_not_mapped_count);
		return $Practical_not_mapped_counts;
	}

	 public function getPracticalmappedalldata($formId=null){

		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$Practicalexam_year = $selected_session;
		$curse10 = 10;
		$curse12 = 12;
		$conditions['exam_year'] = $selected_session;

		$Practicalexam_month = 1;

		$Practical_mapped_registered_all_data =  DB::select(DB::raw("SELECT ec.cent_name AS `Exam_Center11_Name`, deouser.ssoid as `DEO_SSOID`, practicaluser.ssoid as `Practical_Examiner_SSOID`, ec.ecenter10 AS `Ecenter10`, ec.ecenter12 AS `Ecenter12`, ec.fixcode AS `Fixcode`, ( CASE WHEN sam.course = '$curse10' THEN '10th' WHEN sam.course = '$curse12' THEN '12th' END ) AS `Course`, sam.subject_name AS `Subject_Name`, sam.subject_code AS `Subject_Code`, sum( 1 ) AS `Total_Student_Count`, sum( IF ( sam.practical_examiner_id = 0, 1, 0 )) AS `Practical_Examiner_Not_Assign_Student_Count`, sum( IF ( sam.practical_examiner_id > '0', '1', '0' )) AS `Practical_Examiner_Assigned_Student_Count`, sum( IF ( sam.is_update_practical_marks_practical_examiner = '1', '1', '0' ) ) AS `Practical_Examiner_Marks_Entered`, sum( IF ( sam.is_update_practical_marks_practical_examiner = '0', '1', '0' )) AS `Practical_Examiner_Not_Marks_Entered`, sum( IF ( sam.is_practical_lock_submit = '0', '1', '0' )) AS `Pending_Lock_Submit`, sum( IF ( sam.is_practical_lock_submit = '1', '1', '0' )) AS `Complete_Lock_Submit`, IF ( uem.document IS NOT NULL, 'Yes', 'No' ) AS `Is_Dcoument_Uploaded`, IF ( practicaluser.ssoid IS NOT NULL, 'Yes', 'No' ) AS `Is_Practical_Examiner_Assigned_By_DEO` FROM rs_student_allotment_marks sam LEFT JOIN rs_user_examiner_maps uem ON uem.id = sam.user_examiner_map_id AND uem.exam_year = '$Practicalexam_year' AND uem.exam_month ='$Practicalexam_month' AND uem.deleted_at IS NULL LEFT JOIN rs_users deouser ON sam.user_deo_id = deouser.id LEFT JOIN rs_users practicaluser ON sam.practical_examiner_id = practicaluser.id INNER JOIN rs_student_allotments sa ON sa.student_id = sam.student_id AND sa.deleted_at IS NULL AND sa.exam_year = '$Practicalexam_year' AND sa.exam_month = '$Practicalexam_month' LEFT JOIN rs_examcenter_details ec ON sam.examcenter_detail_id = ec.id WHERE sam.exam_year = '$Practicalexam_year' AND sam.exam_month = '$Practicalexam_month' AND sam.deleted_at IS NULL AND sam.is_exclude_for_practical = 0 AND sam.subject_id IN ( SELECT id FROM rs_subjects WHERE practical_type = 1 ) GROUP BY sam.subject_id, sam.examcenter_detail_id limit 2"));

		//$collectedItems = collect($Practical_mapped_registered_all_data);
		//$getdata = $collectedItems->paginate(1);
		return $Practical_mapped_registered_all_data;
	}

	public function getAiCentersForVerifier($auth_user_id=null){
		$custom_component_obj = new CustomComponent;
		$aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
		$aiCenters = collect();

		if($aicenter_mapped_data != null){
			$queryOrder = "CAST(ai_code AS DECIMAL(10,0)) ASC";
			$aiCenters = AicenterDetail::select(
				DB::raw("CONCAT(COALESCE(`ai_code`,''),' - ',COALESCE(`college_name`,'')) AS college_name"),'ai_code')
				->where('active',1)
				->whereIn('ai_code',@$aicenter_mapped_data)
				->whereNull('deleted_at')
				->orderByRaw($queryOrder)
				->pluck('college_name', 'ai_code');
			// dd($aiCenters);
		}

		return $aiCenters;
	}
	
	public function saveStudentVerificaitonMainDocDetails($inputs=null){
		//verifier_status upper level check if 1 or etc. also maintin students table.
		//upper level is doc,photograph
		//lower level is doc each checkbox value
		$role_id = Session::get('role_id');
		$baseName = null;
		$baseNameLast = "_is_verify";
		$baseFullNameDocVeriFirst = null;
		if($role_id == Config::get('global.verifier_id')){
			$baseNameFirst = "verifier_";
			$baseFullNameDocVeriFirst = "verifier_";
		}elseif($role_id == Config::get('global.super_admin_id')){
			$baseNameFirst = "dept_";
			$baseFullNameDocVeriFirst = "department_";
		}
		$formSubmitArr = array(
			3 => array(
				'upper' => array(1 => 'yes'),
				'lower' => array(1 => 'yes' , 5 => 'yes')
			),
			9 => array(
				'upper' => array(1 => 'no'),
				'lower' => array(1 => 'yes' , 7 => 'no')
			),
		);

		$docVerification = $baseFullNameDocVeriFirst . 'documents_verification';

		$$docVerification = json_encode($formSubmitArr);
		//dd($$docVerification);
		return true;
	}

	public function getStudentVerificaitonMainDocDetails($student_id=null,$isReverify=null){
		$docsList = Document::where('student_id',$student_id)->first();
		$documentInput = array();
		$role_id = Session::get('role_id');
		$baseName = null;

		$baseNameLast = "_is_verify";
		$baseNameFirst = "verifier_";
		if(Session::get('role_id') == Config::get('global.verifier_id')){
			$baseNameFirst = "verifier_";
		}elseif(Session::get('role_id') == Config::get('global.academicofficer_id')){
			$baseNameFirst = "ao_";
		}elseif(Session::get('role_id') == Config::get('global.super_admin_id')){
			$baseNameFirst = "dept_";
		}
		//if exists in rs_student_verifications with the given role id otherwise
		// dd($baseNameFirst);
		$documentVerificationsLastDetails = DocumentVerification::where('student_id',$student_id)
			->where('role_id',$role_id)
			->orderby("id","DESC")->first();
		// dd($documentVerificationsLastDetails);
		$finalData = array();
		if($isReverify == 1){
			$lastDocumentVerificationsLastDetails = DocumentVerification::where('student_id',$student_id)->orderby("id","DESC")->first();

			if($baseNameFirst == 'ao_'){
				$var_verifier_upper_documents_verification = $lastDocumentVerificationsLastDetails->verifier_upper_documents_verification;
				$var_verifier_upper_documents_verification = json_decode($var_verifier_upper_documents_verification, true);
				// $var_verifier_upper_documents_verification = array_filter($var_verifier_upper_documents_verification, function($value) {
				// 	return $value == 2;
				// });

				$studentCond['id'] = $student_id;
				$student = Student::where($studentCond)->first(["adm_type","course","gender_id"]);
				$verLabelOnlyForMainDocItems = $this->getLabelOnlyForVerificaitonMainDocLists(@$student_id);
				// echo "8134";dd($verLabelOnlyForMainDocItems);
				$studentInputs = $this->getMasterVerificaitonMainDocLists(@$student->adm_type,@$student->course);
				// echo "custom component ->  8139"; dd($student->adm_type);
				// echo "custom component ->  8139"; dd($studentInputs);
				$onlyKeys = array_keys(@$studentInputs);
				// dd($onlyKeys);
				// dd($var_verifier_upper_documents_verification);
				foreach(@$studentInputs as $k => $v){
					if(in_array($k,$onlyKeys)){
						$arrTemp = (array) @$verLabelOnlyForMainDocItems[$k];
						if(@$arrTemp['hindi_name']){
							if(in_array($k,array_keys(@$var_verifier_upper_documents_verification))){
								$finalData[$k]['upper_level'] = $arrTemp;
								$finalData[$k]['upper_level']['lbl'] = @$arrTemp['hindi_name'];
								$finalData[$k]['lower_level'] = @$v;
							}
						}
					}
				}
				// dd($finalData);
			}else{
				$var_ao_upper_documents_verification = null;
				if(@$lastDocumentVerificationsLastDetails->ao_upper_documents_verification){
					$var_ao_upper_documents_verification = @$lastDocumentVerificationsLastDetails->ao_upper_documents_verification;
					
					$var_ao_upper_documents_verification = json_decode($var_ao_upper_documents_verification, true);
					 
					$var_ao_upper_documents_verification = array_filter($var_ao_upper_documents_verification, function($value) {
						return $value == 2;
					});
				}

				$studentCond['id'] = $student_id;
				$student = Student::where($studentCond)->first(["adm_type","course","gender_id"]);
				$verLabelOnlyForMainDocItems = $this->getLabelOnlyForVerificaitonMainDocLists(@$student_id);
				// echo "8134";dd($verLabelOnlyForMainDocItems);
				$studentInputs = $this->getMasterVerificaitonMainDocLists(@$student->adm_type,@$student->course);
				// echo "custom component ->  8139"; dd($student->adm_type);
				// echo "custom component ->  8139"; dd($studentInputs);
				$onlyKeys = array_keys(@$studentInputs);
				// dd($onlyKeys);
				foreach(@$studentInputs as $k => $v){
					if(in_array($k,$onlyKeys)){
						$arrTemp = (array) @$verLabelOnlyForMainDocItems[$k];
						if(@$arrTemp['hindi_name']){
							if(@$var_ao_upper_documents_verification){
								if(in_array($k,array_keys(@$var_ao_upper_documents_verification))){
									$finalData[$k]['upper_level'] = $arrTemp;
									$finalData[$k]['upper_level']['lbl'] = @$arrTemp['hindi_name'];
									$finalData[$k]['lower_level'] = @$v;
								}
							}
						}
					}
				}
			}
		}else{

			// echo "student controller -> 12333";dd($documentVerificationsLastDetails);
			if(@$documentVerificationsLastDetails->id){

			}else{
				$studentCond['id'] = $student_id;
				$student = Student::where($studentCond)->first(["adm_type","course","gender_id"]);
				$verLabelOnlyForMainDocItems = $this->getLabelOnlyForVerificaitonMainDocLists(@$student_id);
				// echo "8134";dd($verLabelOnlyForMainDocItems);
				$studentInputs = $this->getMasterVerificaitonMainDocLists(@$student->adm_type,@$student->course);
				// echo "custom component ->  8324"; dd($student->adm_type);
				// echo "custom component ->  8139"; dd($studentInputs);
				$onlyKeys = array_keys(@$studentInputs);
				// dd($onlyKeys);
				foreach(@$studentInputs as $k => $v){
					if(in_array($k,$onlyKeys)){
						$arrTemp = (array) @$verLabelOnlyForMainDocItems[$k];
						if(@$arrTemp['hindi_name']){
							$finalData[$k]['upper_level'] = $arrTemp;
							$finalData[$k]['upper_level']['lbl'] = @$arrTemp['hindi_name'];
							$finalData[$k]['lower_level'] = @$v;
						}
					}
				}
			}
		}

		// echo "Code-8151";
		// dd($finalData);
		// dd($verLabelOnlyForMainDocItems);
		// dd($studentInputs);
		return @$finalData;
	}

	public function getLabelOnlyForVerificaitonMainDocLists($student_id=null){
		$studentCond['id'] = $student_id;
		$applicationCond['student_id'] = $student_id;
		$extra['student'] = Student::where($studentCond)->first();
		$extra['application'] = Application::where($applicationCond)->first();

		$result = array();
		$conditions = array();
		$fld="status";$conditions[$fld] = 1;
		$fld="field_value";$conditions[$fld] = null;
		$fld = 'adm_type';
		if(@$$fld){
			$conditions[$fld] = $$fld;
		}
		$mainTable = "verification_labels";
		$cacheName = "getVerificaitonMainDocLists_";
		Cache::forget($cacheName);
		if (Cache::has($cacheName)){
			$result = Cache::get($cacheName);
		} else {
			$result = Cache::rememberForever($cacheName, function () use ($conditions, $extra, $mainTable) {

				$tempResult = DB::table($mainTable)->where($conditions)->whereNull('field_name')->get();
				if(@$tempResult){
					$tempResult = $tempResult->toArray();
				}



				$student_category_a = $extra['application']['category_a'];
				$field_name = 'category_a';
				if(in_array($student_category_a,[2,3,4,5])){
					$extraCond = array('status' => 1,'field_name' => $field_name);
					$tempVar = '$' . $field_name . '_result';
					$tempResult[] = DB::table($mainTable)->where($extraCond)
					->where('field_value', 'like', "%" . @$student_category_a ."%")
					->first();
				}else{
					$field_name = 'category_a';
					$extraCond = array('status' => 1,'field_name' => $field_name, 'field_value' => $student_category_a);
					$tempVar = '$' . $field_name . '_result';
					$tempResult[] = DB::table($mainTable)->where($extraCond)->first();
				}

				$field_name = 'disadvantage_group';
				$extraCond = array('status' => 1,'field_name' => $field_name, 'field_value' => $extra['application'][$field_name]);
				$tempVar = '$' . $field_name . '_result';
				$tempResult[] = DB::table($mainTable)->where($extraCond)->first();


				$field_name = 'disability';
				if($extra['application'][$field_name] != 10){
					$extraCond = array('status' => 1,'field_name' => $field_name);
					$tempVar = '$' . $field_name . '_result';

					$tempResult[] = DB::table($mainTable)->where($extraCond)
					->where('field_value', 'like', "%" . @$extra['application'][$field_name] ."%")
					->first();
				}

				$field_name = 'gender_id';
				if($extra['student'][$field_name] == 3){
					$extraCond = array('field_name' => $field_name);
					$tempVar = '$' . $field_name . '_result';

					$tempResult[] = DB::table($mainTable)->where($extraCond)
					->where('field_value', 'like', "%" . @$extra['application'][$field_name] ."%")
					->first();
				}

				$field_name = 'toc';
				if($extra['application'][$field_name] == 1){
					$extraCond = array('field_name' => $field_name);
					$tempVar = '$' . $field_name . '_result';
					$tempResult[] = DB::table($mainTable)->where($extraCond)
					->where('field_value', 'like', "%" . @$extra['application'][$field_name] ."%")
					->first();
				}

				$field_name = 'pre_qualification';
				if($extra['application'][$field_name] != 14){
					$extraCond = array('field_name' => $field_name);
					$tempVar = '$' . $field_name . '_result';
					$tempResult[] = DB::table($mainTable)->where($extraCond)
					->where('field_value', 'like', "%" . @$extra['application'][$field_name] ."%")
					->first();
				}

				$field_name = 'iti_pre_qualification';
				if($extra['student']['adm_type'] == 5){
					$extraCond = array('field_name' => $field_name);
					$tempVar = '$' . $field_name . '_result';
					$tempResult[] = DB::table($mainTable)->where($extraCond)
					->first();
				}

				$finalResult = array();
				if(@$tempResult){
					foreach(@$tempResult as $k => $v){
						if(@$v->id){
							$finalResult[@$v->id] = $v;
							$finalResult[@$v->id]->field_id = @$v->id;
						}
					}
				}
				return $result = $finalResult;
			});
		}
		return $result;
	}
	
	public function getMasterVerificaitonMainDocLists($adm_type=null,$course=null,$main_document_id=null){
		$result = array();
		$conditions = array();
		$fld="status";$conditions[$fld] = 1;
		$fld = 'adm_type';
		if(@$$fld){
			$conditions[$fld] = $$fld;
		}
		$fld = 'course';
		if(@$$fld){
			$conditions[$fld] = $$fld;
		}
		$fld = 'main_document_id';
		if(@$$fld){
			$conditions[$fld] = $$fld;
		}
		$mainTable = "verification_masters";
		$cacheName = "getVerificaitonMainDocLists_".$adm_type . "_" . $course. "_" .$main_document_id;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)){
			$result = Cache::get($cacheName);
		} else {
			$result = Cache::rememberForever($cacheName, function () use ($conditions, $mainTable) {

				$tempResult = DB::table($mainTable)->where($conditions)->orderby('main_document_id')->get();
				// dd($tempResult);
				$finalResult = array();
				if(@$tempResult){
					foreach($tempResult as $k => $v){
						$finalResult[$v->main_document_id][] = $v;
					}
				}
				return $result = $finalResult;
			});
		}
		return $result;
	}
	
	public function getverificationMaster($formId=null,$isPaginate=true,){
		$conditions = Session::get($formId. '_conditions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
				$master = VerificationMaster::where($conditions)->orderBy("id","DESC")->paginate($defaultPageLimit);
		}else{
			$master = VerificationMaster::where($conditions)->orderBy("id","DESC")->get();
		}
		return $master;
	}
	
	public function _checkRevisedAllowOrNotAllow(){
		$objController = new Controller();
		$combo_name = 'marsheets_correction_start_date';
		$combo_name2 = 'marsheets_correction_end_date';
		$marsheets_correction_start_date_arr = $objController->master_details($combo_name);
		$marsheets_correction_start_end_arr = $objController->master_details($combo_name2);
		if(strtotime(date("Y-m-d H:i:s")) >= strtotime($marsheets_correction_start_date_arr[1]) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime($marsheets_correction_start_end_arr[1])){
			$isValid = true;
		}else{
			$isValid = false;
		}
		return $isValid;
	}
	
	public function _checkRevisedLockSubmittedAllowOrNotAllow(){
		$objController = new Controller();
		$combo_name = 'marsheets_correction_lock_submitted_start_date';
		$combo_name2 = 'marsheets_correction_lock_submitted_end_date';
		$marsheets_correction_start_date_arr = $objController->master_details($combo_name);
		$marsheets_correction_start_end_arr = $objController->master_details($combo_name2);
		if(strtotime(date("Y-m-d H:i:s")) >= strtotime($marsheets_correction_start_date_arr[1]) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime($marsheets_correction_start_end_arr[1])){
			$isValid = true;
		}else{
			$isValid = false;
		}
		return $isValid;
	}
	
	public function MarksheetPaymentIssuesData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = MarksheetPaymentIssue::leftJoin('students', 'marksheet_payment_issues.student_id', '=', 'students.id')
			->leftJoin('marksheet_migration_requests', 'marksheet_migration_requests.id', '=', 'marksheet_payment_issues.marksheet_migration_requests_id')
			->where($conditions)
			->where('marksheet_payment_issues.student_id', "!=",null)
			->where('marksheet_payment_issues.enrollment', "!=",null)
			->paginate($defaultPageLimit,array('marksheet_payment_issues.is_archived','marksheet_migration_requests.total_fees','marksheet_migration_requests.challan_tid','students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code'
				//,'applications.medium','applications.locksumbitted'));
			));
		}else{
			$master = MarksheetPaymentIssue::leftJoin('students', 'marksheet_payment_issues.student_id', '=', 'students.id')
			->leftJoin('marksheet_migration_requests', 'marksheet_migration_requests.id', '=', 'marksheet_payment_issues.marksheet_migration_requests_id')
			->where($conditions)
			->where('marksheet_payment_issues.student_id', "!=",null)
			->where('marksheet_payment_issues.enrollment', "!=",null)
			->get(array('marksheet_payment_issues.is_archived','marksheet_migration_requests.total_fees','marksheet_migration_requests.challan_tid','students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code'));
			//,'applications.medium','applications.locksumbitted'));
		}
		return $master;
	}
	
	public function getMarksheetMigrationData($marksheet_type =null,$locksumbitted = null,$feestatus=null){
		$condition = array();
		if($locksumbitted != null){
			$condition['locksumbitted'] = $locksumbitted;
		}
		if($marksheet_type != null){
			$condition['marksheet_type'] = $marksheet_type;
		}

		if($feestatus != null){
			$condition['fee_status'] = $feestatus;
		}

		$data=MarksheetMigrationRequest::join('students','marksheet_migration_requests.student_id','=','students.id')->where($condition)->count();
		return $data;
	}
	
	public function getRevisedDuplicateRequestData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$field =['marksheet_migration_requests.id as mmrid','marksheet_migration_requests.student_id','students.enrollment','students.name','students.ai_code','marksheet_migration_requests.marksheet_type','marksheet_migration_requests.marksheet_type',
		'marksheet_migration_requests.document_type','marksheet_migration_requests.locksumbitted','marksheet_migration_requests.locksubmitted_date','marksheet_migration_requests.fee_paid_amount','marksheet_migration_requests.challan_tid','marksheet_migration_requests.total_fees','marksheet_migration_requests.fee_status','marksheet_migration_requests.correction_update','marksheet_migration_requests.marksheet_migration_status'];
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");

				$master = MarksheetMigrationRequest::Join('students', 'marksheet_migration_requests.student_id', '=', 'students.id')
				->where($conditions)
				->paginate($defaultPageLimit,$field);
		}else{
				$master = MarksheetMigrationRequest::Join('students', 'marksheet_migration_requests.student_id', '=', 'students.id')
				->where($conditions)
				->get($field);
			}
        return $master;
	}
	
	   public function getchangerequestallStudentCount($exam_month=null){
		$conditions = array();
		$changerequertcurrentexamyear = Config::get("global.form_admission_academicyear_id");
		$selected_session = $changerequertcurrentexamyear;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
			$conditions['student_change_requests'] = 1;
		}
		$change_request_total_registred_all_students = Student::where($conditions)->count();
		return $change_request_total_registred_all_students;
	}
	
	  public function getchangerequestallApprovedStudentCount($exam_month=null){
		$conditions = array();
		$changerequertcurrentexamyear = Config::get("global.form_admission_academicyear_id");
		$selected_session = $changerequertcurrentexamyear;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
			$conditions['student_change_requests'] =2;
		}
		$change_request_total_approved_registred_all_students = Student::where($conditions)->count();
		return $change_request_total_approved_registred_all_students;
	}
	
	 public function getchangerequesttotalgeneratedCount($exam_month=null){
		$conditions = array();
		$changerequertcurrentexamyear = Config::get("global.form_admission_academicyear_id");
		$selected_session = $changerequertcurrentexamyear;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
		}
		$change_request_total_generated_count = ChangeRequestStudent::where($conditions)->count();
		return $change_request_total_generated_count;
	}
	
	 public function getchangerequestdepartmentapprovalCount($exam_month=null){
		$conditions = array();
		$changerequertcurrentexamyear = Config::get("global.form_admission_academicyear_id");
		$selected_session = $changerequertcurrentexamyear;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
			$conditions['student_change_requests'] =2;
		}
		$change_request_department_approval_count = ChangeRequestStudent::where($conditions)->count();
		return $change_request_department_approval_count;
	}
	
	 public function getchangerequeststudentupdateapplicationsCount($exam_month=null){
		$conditions = array();
		$changerequertcurrentexamyear = Config::get("global.form_admission_academicyear_id");
		$selected_session = $changerequertcurrentexamyear;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
			$conditions['student_change_requests'] =2;
			$conditions['student_update_application'] =1;
		}
		$change_request_student_update_applications_count = ChangeRequestStudent::where($conditions)->count();
		return $change_request_student_update_applications_count;
	}
	
	public function getchangerequeststudentnotclickupdateapplications($exam_month=null){
		$conditions = array();
		$changerequertcurrentexamyear = Config::get("global.form_admission_academicyear_id");
		$selected_session = $changerequertcurrentexamyear;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
			$conditions['student_change_requests'] =2;
		}
		$change_request_student_update_applications_count = ChangeRequestStudent::where($conditions)->whereNull('student_update_application')->count();
		return $change_request_student_update_applications_count;
	}
	
	 public function getchangerequestnotlocksumbittedCount($exam_month=null){
		$conditions = array();
		$changerequertcurrentexamyear = Config::get("global.form_admission_academicyear_id");
		$selected_session = $changerequertcurrentexamyear;
		$conditions['change_request_students.exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['change_request_students.exam_month'] = $exam_month;
			$conditions['change_request_students.student_change_requests'] =2;
			$conditions['change_request_students.student_update_application'] =1;
		}

		$change_request_student_update_applications_count = ChangeRequestStudent::Join('applications', 'applications.student_id', '=', 'change_request_students.student_id')->
		where($conditions)->whereNull('applications.deleted_at')->whereNull('applications.locksumbitted')->count();
		return $change_request_student_update_applications_count;
	}
	
	public function getchangerequestlocksumbittedCount($exam_month=null){
		$conditions = array();
		$changerequertcurrentexamyear = Config::get("global.form_admission_academicyear_id");
		$selected_session = $changerequertcurrentexamyear;
		$conditions['change_request_students.exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['change_request_students.exam_month'] = $exam_month;
			$conditions['change_request_students.student_change_requests'] =2;
			$conditions['change_request_students.student_update_application'] =1;
		}

		$change_request_student_update_applications_count = ChangeRequestStudent::Join('applications', 'applications.student_id', '=', 'change_request_students.student_id')->
		where($conditions)->whereNull('applications.deleted_at')->where('applications.locksumbitted',1)->count();
		return $change_request_student_update_applications_count;
	}
	
	public function getchangerequestlocksumbittedfeesnotpayCount($exam_month=null){
		$conditions = array();
		$changerequertcurrentexamyear = Config::get("global.form_admission_academicyear_id");
		$selected_session = $changerequertcurrentexamyear;
		$conditions['change_request_students.exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['change_request_students.exam_month'] = $exam_month;

		}

		$change_request_student_update_applications_counts  = DB::select(
		"select count(*) as count from `rs_change_request_students` 
		inner join `rs_applications` on `rs_applications`.`student_id` = `rs_change_request_students`.`student_id` 
		inner join `rs_students` on `rs_students`.`id` = `rs_change_request_students`.`student_id` 
		inner join `rs_change_requert_old_student_fees` on `rs_change_requert_old_student_fees`.`student_id` = `rs_change_request_students`.`student_id` 
		inner join `rs_student_fees` on `rs_student_fees`.`student_id` = `rs_change_request_students`.`student_id` 
		where (`rs_change_request_students`.`exam_year` = $selected_session and `rs_change_request_students`.`exam_month` = $exam_month 
		and `rs_change_request_students`.`student_change_requests` = 2 
		and `rs_change_request_students`.`student_update_application` = 1) 
		and `rs_applications`.`deleted_at` is null and `rs_students`.`deleted_at` is null 
		and `rs_change_requert_old_student_fees`.`deleted_at` is null and `rs_student_fees`.`deleted_at` is null
		and `rs_students`.`update_change_requests_challan_tid` is null and `rs_applications`.`locksumbitted` = 1 
		and `rs_change_requert_old_student_fees`.`total` > `rs_student_fees`.`total` and `rs_students`.`student_change_requests` = 2 
		and `rs_change_request_students`.`deleted_at` is null");

		 $change_request_student_update_applications_count = $change_request_student_update_applications_counts[0]->count;
		return $change_request_student_update_applications_count;
	}
	
	public function getchangerequeststudentcompleted($exam_month=null){
		$conditions = array();
		$changerequertcurrentexamyear = Config::get("global.form_admission_academicyear_id");
		$selected_session = $changerequertcurrentexamyear;
		$conditions['students.exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['students.exam_month'] = $exam_month;
			$conditions['change_request_students.exam_month'] = $exam_month;
			$conditions['change_request_students.exam_year'] = $selected_session;
			$conditions['change_request_students.student_change_requests'] =2;
			$conditions['change_request_students.student_update_application'] =1;
		}

		$change_request_student_update_applications_count = ChangeRequestStudent::Join('students', 'students.id', '=', 'change_request_students.student_id')
		->where($conditions)->whereNull('students.student_change_requests')->count();
		return $change_request_student_update_applications_count;
	}
	
	 public function getchangerequestallStudentdata($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$fields = array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','students.student_change_requests','students.exam_month');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Student::where($conditions)->paginate($defaultPageLimit,$fields);

		}else{
			$master = Student::where($conditions)->get($fields);
		}
		return $master;
	}
	
	public function getchangerequestallStudentdataGenerated($formId=null,$isPaginate=true){

		$conditions = Session::get($formId. '_conditions');
		$fields = array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','students.student_change_requests','students.exam_month');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = ChangeRequestStudent::Join('students', 'students.id', '=', 'change_request_students.student_id')->Join('applications', 'applications.student_id', '=', 'change_request_students.student_id')
			->where($conditions)->paginate($defaultPageLimit,$fields);
		}else{
			$master = ChangeRequestStudent::Join('students', 'students.id', '=', 'change_request_students.student_id')->Join('applications', 'applications.student_id', '=', 'change_request_students.student_id')
			->where($conditions)->paginate($fields);
		}
		return $master;
	}
	
	public function getchangerequeststudnetnotclickupdateapplications($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$fields = array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','students.student_change_requests','students.exam_month');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = ChangeRequestStudent::Join('students', 'students.id', '=', 'change_request_students.student_id')
			->where($conditions)->where('change_request_students.student_change_requests',2)->whereNull('change_request_students.student_update_application')->paginate($defaultPageLimit,$fields);
		}else{
			$master = ChangeRequestStudent::Join('students', 'students.id', '=', 'change_request_students.student_id')
			->where($conditions)->where('change_request_students.student_change_requests',2)->whereNull('change_request_students.student_update_application')->get($fields);

		}
		return $master;
	}
	
	public function getchangerequeststudnetnotlocksumbitted($formId=null,$locksumbitted= null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$fields = array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','students.student_change_requests','students.exam_month');
		$master = array();
		if($isPaginate){

			if($locksumbitted == 1){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = ChangeRequestStudent::Join('students', 'students.id', '=', 'change_request_students.student_id')->Join('applications', 'applications.student_id', '=', 'change_request_students.student_id')
			->where($conditions)->where('change_request_students.student_update_application',1)->where('change_request_students.student_change_requests',2)->paginate($defaultPageLimit,$fields);
			}else{
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = ChangeRequestStudent::Join('students', 'students.id', '=', 'change_request_students.student_id')->Join('applications', 'applications.student_id', '=', 'change_request_students.student_id')
			->where($conditions)->where('change_request_students.student_update_application',1)->where('change_request_students.student_change_requests',2)->whereNull('applications.locksumbitted')->paginate($defaultPageLimit,$fields);
			}

		}else{
			if($locksumbitted == 1){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = ChangeRequestStudent::Join('students', 'students.id', '=', 'change_request_students.student_id')->Join('applications', 'applications.student_id', '=', 'change_request_students.student_id')
			->where($conditions)->where('change_request_students.student_update_application',1)->where('change_request_students.student_change_requests',2)->get($fields);
			}else{
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = ChangeRequestStudent::Join('students', 'students.id', '=', 'change_request_students.student_id')->Join('applications', 'applications.student_id', '=', 'change_request_students.student_id')
			->where($conditions)->where('change_request_students.student_update_application',1)->where('change_request_students.student_change_requests',2)->whereNull('applications.locksumbitted')->get($fields);
			}


		}
		return $master;
	}
	
	public function getchangerequeststudnetompleted($formId=null,$exammomths=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$fields = array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','students.student_change_requests','students.exam_month');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = ChangeRequestStudent::Join('students', 'students.id', '=', 'change_request_students.student_id')
			->where($conditions)->where('change_request_students.student_update_application',1)->where('change_request_students.student_change_requests',2)->where('students.exam_month',$exammomths)->whereNull('students.student_change_requests')->paginate($defaultPageLimit,$fields);
		}else{
			$master = ChangeRequestStudent::Join('students', 'students.id', '=', 'change_request_students.student_id')
			->where($conditions)->where('change_request_students.student_update_application',1)->where('change_request_students.student_change_requests',2)->whereNull('students.student_change_requests')->get($fields);

		}
		return $master;
	}
	
	public function getchangerequestaPaymentIssuesData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		// dd($conditions);
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = ChangeRequestPaymentIssue::leftJoin('students', 'change_request_payment_issues.student_id', '=', 'students.id')
			->leftJoin('student_fees', 'student_fees.student_id', '=', 'students.id')
			->where($conditions)
			->where('change_request_payment_issues.student_id', "!=",null)
			->where('change_request_payment_issues.enrollment', "!=",null)
			->paginate($defaultPageLimit,array('change_request_payment_issues.is_archived','student_fees.total','students.challan_tid','students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code'
				//,'applications.medium','applications.locksumbitted'));
			));
		}else{
			$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->where($conditions)
			->leftJoin('student_fees', 'student_fees.student_id', '=', 'students.id')
			->where('change_request_payment_issues.student_id', "!=",null)
			->where('change_request_payment_issues.enrollment', "!=",null)
			->get(array('change_request_payment_issues.is_archived','student_fees.total','students.id','students.name','students.challan_tid','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code'));
			//,'applications.medium','applications.locksumbitted'));
		}
		return $master;
	}
	
	public function suppgetchangerequestaPaymentIssuesData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		// dd($conditions);
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = SuppChangeRequestPaymentIssue::leftJoin('students', 'supp_change_request_payment_issues.student_id', '=', 'students.id')
			->where($conditions)
			->where('rs_supp_change_request_payment_issues.student_id', "!=",null)
			->where('rs_supp_change_request_payment_issues.enrollment', "!=",null)
			->paginate($defaultPageLimit,array('rs_supp_change_request_payment_issues.is_archived','student_fees.total','students.challan_tid','students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code'
				//,'applications.medium','applications.locksumbitted'));
			));
		}else{
			$master = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->where($conditions)
			->leftJoin('student_fees', 'student_fees.student_id', '=', 'students.id')
			->where('change_request_payment_issues.student_id', "!=",null)
			->where('change_request_payment_issues.enrollment', "!=",null)
			->get(array('change_request_payment_issues.is_archived','student_fees.total','students.id','students.name','students.challan_tid','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code'));
			//,'applications.medium','applications.locksumbitted'));
		}
		return $master;
	}
	
	 public function chnagerequestAllowIps(){
		$showStatus = false;
		$objController = new Controller();
		$result_process_allowed_ips = null;
        $request_client_ip = Config::get('global.request_client_ip');
        $combo_name="chnage_request_allowed_ips";$chnage_request_allowed_ips=$objController->master_details($combo_name);
		if(@$chnage_request_allowed_ips['1']){
			$result_process_allowed_ips =json_decode(@$chnage_request_allowed_ips['1'],true);
		}
		if(!empty($result_process_allowed_ips)){
			if(in_array($request_client_ip,$result_process_allowed_ips)){
				$showStatus = true;
			}
		}else{
		 $showStatus = true;
		}
		return $showStatus;

	}
	
	public function chnagerequestsupplementariesAllowIps(){
		$showStatus = false;
		$objController = new Controller();
		$result_process_allowed_ips = null;
        $request_client_ip = Config::get('global.request_client_ip');
        $combo_name="chnage_request_supplementaries_allowed_ips";$chnage_request_allowed_ips=$objController->master_details($combo_name);
		if(@$chnage_request_allowed_ips['1']){
			$result_process_allowed_ips =json_decode(@$chnage_request_allowed_ips['1'],true);
		}
		if(!empty($result_process_allowed_ips)){
			if(in_array($request_client_ip,$result_process_allowed_ips)){
				$showStatus = true;
			}
		}else{
		 $showStatus = true;
		}
		return $showStatus;

	}
	
	 public function getsuppchangerequestallStudentCount($exam_month=null){
		$conditions = array();
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$selected_session = $changerequertcurrentsuppexamyear;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
			$conditions['supp_student_change_requests'] = 1;
		}
		$change_supp_request_total_registred_all_students = Supplementary::where($conditions)->count();
		return $change_supp_request_total_registred_all_students;
	}
	
	  public function getsuppchangerequestallApprovedStudentCount($exam_month=null){
		$conditions = array();
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$selected_session = $changerequertcurrentsuppexamyear;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
			$conditions['supp_student_change_requests'] =2;
		}
		$change_supp_request_total_approved_registred_all_students = Supplementary::where($conditions)->count();
		return $change_supp_request_total_approved_registred_all_students;
	}
	
	public function getsuppchangerequestalltotalapply($exam_month=null){
		$conditions = array();
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$selected_session = $changerequertcurrentsuppexamyear;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
		}
		$change_supp_request_total_apply = SuppChangeRequestStudents::where($conditions)->count();
		return $change_supp_request_total_apply;
	}
	
	public function getsuppchangerequestapproveddeparmentwise($exam_month=null){
		$conditions = array();
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$selected_session = $changerequertcurrentsuppexamyear;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
			$conditions['supp_student_change_requests'] =2;
		}
		$change_supp_request_total_approved = SuppChangeRequestStudents::where($conditions)->count();
		return $change_supp_request_total_approved;
	}
	
	public function getsuppchangerequestupdateapplicationsbutton($exam_month=null){
		$conditions = array();
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$selected_session = $changerequertcurrentsuppexamyear;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
			$conditions['supp_student_update_application'] =1;
			$conditions['supp_student_change_requests'] =2;
		}
		$change_supp_request_total_applicationsbutton = SuppChangeRequestStudents::where($conditions)->count();
		return $change_supp_request_total_applicationsbutton;
	}
	
	 public function getsuppchangerequeststudentnotclickupdateapplications($exam_month=null){
		$conditions = array();
		$changerequertcurrentexamyear = Config::get("global.form_supp_current_admission_session_id");
		$selected_session = $changerequertcurrentexamyear;
		$conditions['exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['exam_month'] = $exam_month;
			$conditions['supp_student_change_requests'] =2;
		}
		$change_supp_request_student_not_click_update_applications_count = SuppChangeRequestStudents::where($conditions)->whereNull('supp_student_update_application')->count();
		return $change_supp_request_student_not_click_update_applications_count;
	}
	
	 public function getsuppchangerequestallnotlocksumbittedStudentCount($exam_month=null){
		$conditions = array();
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$selected_session = $changerequertcurrentsuppexamyear;
		$conditions['supp_change_request_students.exam_year'] = $selected_session;
		$conditions['supplementaries.exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['supp_change_request_students.exam_month'] = $exam_month;
			$conditions['supplementaries.exam_month'] = $exam_month;
			$conditions['supp_change_request_students.supp_student_change_requests'] =2;
			$conditions['supp_change_request_students.supp_student_update_application'] =1;
		}
		$change_supp_request_total_approved_registred_all_students_locksumbitted =SuppChangeRequestStudents::Join('supplementaries', 'supplementaries.student_id', '=', 'supp_change_request_students.student_id')->
		where($conditions)->whereNull('supplementaries.locksumbitted')->whereNull('supplementaries.locksubmitted_date')->whereNull('supplementaries.deleted_at')->count();
		return $change_supp_request_total_approved_registred_all_students_locksumbitted;
	}
	
	public function getsuppchangerequestalllocksumbittedStudentCount($exam_month=null){
		$conditions = array();
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$selected_session = $changerequertcurrentsuppexamyear;
		$conditions['supp_change_request_students.exam_year'] = $selected_session;
		$conditions['supplementaries.exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['supp_change_request_students.exam_month'] = $exam_month;
			$conditions['supplementaries.exam_month'] = $exam_month;
			$conditions['supp_change_request_students.supp_student_change_requests'] =2;
			$conditions['supp_change_request_students.supp_student_update_application'] =1;
		}
		$change_supp_request_total_approved_registred_all_students_locksumbitted =SuppChangeRequestStudents::Join('supplementaries', 'supplementaries.student_id', '=', 'supp_change_request_students.student_id')->
		where($conditions)->where('supplementaries.locksumbitted',1)->whereNotNull('supplementaries.locksubmitted_date')->whereNull('supplementaries.deleted_at')->count();
		return $change_supp_request_total_approved_registred_all_students_locksumbitted;
	}
	
	public function getsuppchangerequestallnotlocksumbittedfeespayStudentCount($exam_month=null){
		$conditions = array();
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$selected_session = $changerequertcurrentsuppexamyear;
		$conditions['supp_change_request_students.exam_year'] = $selected_session;
		$conditions['supplementaries.exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['supp_change_request_students.exam_month'] = $exam_month;
			$conditions['supplementaries.exam_month'] = $exam_month;
			$conditions['supp_change_request_students.supp_student_change_requests'] =2;
			$conditions['supp_change_request_students.supp_student_update_application'] =1;
			$conditions['supplementaries.supp_student_change_requests'] =2;
			$conditions['supplementaries.locksumbitted'] =1;
		}

		$change_supp_request_total_approved_registred_all_students_locksumbitted_feespay = SuppChangeRequestStudents::Join('students', 'students.id', '=', 'supp_change_request_students.student_id')
		->Join('supp_change_requert_old_student_fees', 'supp_change_requert_old_student_fees.supp_student_change_request_id', '=', 'supp_change_request_students.id')
		->Join('supplementaries', 'supplementaries.student_id', '=', 'supp_change_request_students.student_id')
		->whereColumn('supp_change_requert_old_student_fees.total_fees', '>', 'supplementaries.total_fees')
		->where($conditions)->whereNull('supplementaries.update_supp_change_requests_challan_tid')
		->whereNull('supplementaries.deleted_at')
		->whereNull('students.deleted_at')
		->whereNull('supp_change_requert_old_student_fees.deleted_at')->count();
		return $change_supp_request_total_approved_registred_all_students_locksumbitted_feespay;
	}
	
	public function getsuppchangerequeststudentsitecompleted($exam_month=null){
		$conditions = array();
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$selected_session = $changerequertcurrentsuppexamyear;
		$conditions['supp_change_request_students.exam_year'] = $selected_session;
		$conditions['supplementaries.exam_year'] = $selected_session;
		if(!empty($exam_month) && $exam_month != null){
			$conditions['supp_change_request_students.exam_month'] = $exam_month;
			$conditions['supp_change_request_students.supp_student_update_application'] =1;
			$conditions['supp_change_request_students.supp_student_change_requests'] =2;
			$conditions['supplementaries.exam_month'] = $exam_month;
		}
		$change_supp_request_total_apply = SuppChangeRequestStudents::join('supplementaries', 'supplementaries.student_id', '=', 'supp_change_request_students.student_id')->where($conditions)->whereNull('supplementaries.deleted_at')->whereNull('supplementaries.supp_student_change_requests')->count();
		return $change_supp_request_total_apply;
	}
	
	 public function getsuppchangerequestallStudentdata($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$fields = array('supplementaries.student_id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','supplementaries.supp_student_change_requests','supplementaries.exam_month','supplementaries.locksumbitted');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Supplementary::Join('students', 'students.id', '=', 'supplementaries.student_id')->where('supplementaries.exam_year',$changerequertcurrentsuppexamyear)->where($conditions)->paginate($defaultPageLimit,$fields);

		}else{
			$master = Supplementary::Join('students', 'students.id', '=', 'supplementaries.student_id')->where('supplementaries.exam_year',$changerequertcurrentsuppexamyear)->where($conditions)->get($fields);
		}
		return $master;
	}
	
	public function SuppgetchangerequestallStudentdataGenerated($formId=null,$exammomths=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$fields = array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','supp_change_request_students.supp_student_change_requests','students.exam_month');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = SuppChangeRequestStudents::Join('students', 'students.id', '=', 'supp_change_request_students.student_id')->where('supp_change_request_students.exam_year',$changerequertcurrentsuppexamyear)->where($conditions)->paginate($defaultPageLimit,$fields);
		}else{
			$master = SuppChangeRequestStudents::Join('students', 'students.id', '=', 'supp_change_request_students.student_id')->where('supp_change_request_students.exam_year',$changerequertcurrentsuppexamyear)->where($conditions)->get($fields);
		}
		return $master;
	}
	
	public function getsuppchangerequeststudnetnotclickupdateapplications($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$fields = array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','supp_change_request_students.supp_student_change_requests','students.exam_month');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = SuppChangeRequestStudents::Join('students', 'students.id', '=', 'supp_change_request_students.student_id')->where('supp_change_request_students.exam_year',$changerequertcurrentsuppexamyear)->where($conditions)->whereNull('supp_change_request_students.supp_student_update_application')->paginate($defaultPageLimit,$fields);
		}else{
			$master = SuppChangeRequestStudents::Join('students', 'students.id', '=', 'supp_change_request_students.student_id')->where('supp_change_request_students.exam_year',$changerequertcurrentsuppexamyear)->where($conditions)->whereNull('supp_change_request_students.supp_student_update_application')->get($fields);
		}
		return $master;
	}
	
	public function getsuppchangerequestnotpayfees($formId=null,$exammomths=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$selected_session = $changerequertcurrentsuppexamyear;
		$conditions['supp_change_request_students.exam_year'] = $selected_session;
		$conditions['supplementaries.exam_year'] = $selected_session;
	    $conditions['supplementaries.exam_month'] = $exammomths;
		$conditions['supp_change_request_students.supp_student_change_requests'] =2;
		$conditions['supp_change_request_students.supp_student_update_application'] =1;
		$conditions['supplementaries.supp_student_change_requests'] =2;
		$conditions['supplementaries.locksumbitted'] =1;

		$fields = array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','supp_change_request_students.supp_student_change_requests','students.exam_month');
		$master = array();

		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = SuppChangeRequestStudents::Join('students', 'students.id', '=', 'supp_change_request_students.student_id')
		->Join('supp_change_requert_old_student_fees', 'supp_change_requert_old_student_fees.supp_student_change_request_id', '=', 'supp_change_request_students.id')
		->Join('supplementaries', 'supplementaries.student_id', '=', 'supp_change_request_students.student_id')
		->whereColumn('supp_change_requert_old_student_fees.total_fees', '>', 'supplementaries.total_fees')
		->where($conditions)->whereNull('supplementaries.update_supp_change_requests_challan_tid')
		->whereNull('supplementaries.deleted_at')
		->whereNull('students.deleted_at')
		->whereNull('supp_change_requert_old_student_fees.deleted_at')->paginate($defaultPageLimit,$fields);
		}else{
		 $master = SuppChangeRequestStudents::Join('students', 'students.id', '=', 'supp_change_request_students.student_id')
		->Join('supp_change_requert_old_student_fees', 'supp_change_requert_old_student_fees.supp_student_change_request_id', '=', 'supp_change_request_students.id')
		->Join('supplementaries', 'supplementaries.student_id', '=', 'supp_change_request_students.student_id')
		->whereColumn('supp_change_requert_old_student_fees.total_fees', '>', 'supplementaries.total_fees')
		->where($conditions)->whereNull('supplementaries.update_supp_change_requests_challan_tid')
		->whereNull('supplementaries.deleted_at')
		->whereNull('students.deleted_at')
		->whereNull('supp_change_requert_old_student_fees.deleted_at')->get($fields);
		}
		return $master;
	}
	
	public function getsuppchangerequeststudnetnotlocksumbitted($formId=null,$exammomths=null,$locksumbitted=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$fields = array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','supp_change_request_students.supp_student_change_requests','students.exam_month');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			if($locksumbitted == 1){
			$master = SuppChangeRequestStudents::Join('supplementaries', 'supplementaries.student_id', '=', 'supp_change_request_students.student_id')->Join('students', 'students.id', '=', 'supp_change_request_students.student_id')->where('supp_change_request_students.exam_year',$changerequertcurrentsuppexamyear)->where('supplementaries.exam_year',$changerequertcurrentsuppexamyear)->where('supplementaries.exam_month',$exammomths)->where(
			$conditions)->where('supp_change_request_students.supp_student_update_application',1)->where('supp_change_request_students.supp_student_change_requests',2)->
			whereNull('supplementaries.deleted_at')->paginate($defaultPageLimit,$fields);
			}
			else{
			$master = SuppChangeRequestStudents::Join('supplementaries', 'supplementaries.student_id', '=', 'supp_change_request_students.student_id')->Join('students', 'students.id', '=', 'supp_change_request_students.student_id')->where('supp_change_request_students.exam_year',$changerequertcurrentsuppexamyear)->where('supplementaries.exam_year',$changerequertcurrentsuppexamyear)->where('supplementaries.exam_month',$exammomths)->where(
			'supp_change_request_students.exam_month',$exammomths)->where('supp_change_request_students.supp_student_update_application',1)->where('supp_change_request_students.supp_student_change_requests',2)->whereNull('supplementaries.locksumbitted')->
			whereNull('supplementaries.deleted_at')->paginate($defaultPageLimit,$fields);
			}
		}else{
			if($locksumbitted == 1){
			$master = SuppChangeRequestStudents::Join('supplementaries', 'supplementaries.student_id', '=', 'supp_change_request_students.student_id')->Join('students', 'students.id', '=', 'supp_change_request_students.student_id')->where('supp_change_request_students.exam_year',$changerequertcurrentsuppexamyear)->where('supplementaries.exam_year',$changerequertcurrentsuppexamyear)->where('supplementaries.exam_month',$exammomths)->where(
			$conditions)->where('supp_change_request_students.supp_student_update_application',1)->where('supp_change_request_students.supp_student_change_requests',2)->
			whereNull('supplementaries.deleted_at')->get($fields);
			}
			else{
			$master = SuppChangeRequestStudents::Join('supplementaries', 'supplementaries.student_id', '=', 'supp_change_request_students.student_id')->Join('students', 'students.id', '=', 'supp_change_request_students.student_id')->where('supp_change_request_students.exam_year',$changerequertcurrentsuppexamyear)->where('supplementaries.exam_year',$changerequertcurrentsuppexamyear)->where('supplementaries.exam_month',$exammomths)->where(
			'supp_change_request_students.exam_month',$exammomths)->where('supp_change_request_students.supp_student_update_application',1)->where('supp_change_request_students.supp_student_change_requests',2)->whereNull('supplementaries.locksumbitted')->
			whereNull('supplementaries.deleted_at')->get($fields);
			}
		}
		return $master;
	}
	
	public function getsuppchangerequeststudnetompleted($formId=null,$exammomths=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$fields = array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','supp_change_request_students.supp_student_change_requests','students.exam_month');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");

			$master = SuppChangeRequestStudents::Join('supplementaries', 'supplementaries.student_id', '=', 'supp_change_request_students.student_id')->Join('students', 'students.id', '=', 'supp_change_request_students.student_id')->where('supp_change_request_students.exam_year',$changerequertcurrentsuppexamyear)->where('supplementaries.exam_year',$changerequertcurrentsuppexamyear)->where('supplementaries.exam_month',$exammomths)->where(
			$conditions)->where('supp_change_request_students.supp_student_update_application',1)->where('supp_change_request_students.supp_student_change_requests',2)->whereNull('supplementaries.supp_student_change_requests')->
			whereNull('supplementaries.deleted_at')->paginate($defaultPageLimit,$fields);
		}else{
			$master = SuppChangeRequestStudents::Join('supplementaries', 'supplementaries.student_id', '=', 'supp_change_request_students.student_id')->Join('students', 'students.id', '=', 'supp_change_request_students.student_id')->where('supp_change_request_students.exam_year',$changerequertcurrentsuppexamyear)->where('supplementaries.exam_year',$changerequertcurrentsuppexamyear)->where('supplementaries.exam_month',$exammomths)->where(
			$conditions)->where('supp_change_request_students.supp_student_update_application',1)->where('supp_change_request_students.supp_student_change_requests',2)->whereNull('supplementaries.supp_student_change_requests')->
			whereNull('supplementaries.deleted_at')->get($fields);
		}
		return $master;
	}
	
	 public function getsuppchangerequesttotalapply($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$fields = array('supp_change_request_students.student_id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','supp_change_request_students.supp_student_change_requests','supp_change_request_students.exam_month');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = SuppChangeRequestStudents::Join('students', 'students.id', '=', 'supp_change_request_students.student_id')->where('supp_change_request_students.exam_year',$changerequertcurrentsuppexamyear)->where($conditions)->orderBy('supp_change_request_students.id', 'desc')->groupBy('supp_change_request_students.student_id')->paginate($defaultPageLimit,$fields);

		}else{
			$master = SuppChangeRequestStudents::Join('students', 'students.id', '=', 'supp_change_request_students.student_id')->where('supp_change_request_students.exam_year',$changerequertcurrentsuppexamyear)->where($conditions)->orderBy('supp_change_request_students.id', 'desc')->groupBy('supp_change_request_students.student_id')->get($fields);
		}
		return $master;
	}
	
	 public function getsuppchangerequestallStudentdatanotLockSubmitted($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$fields = array('supplementaries.student_id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','supplementaries.supp_student_change_requests','supplementaries.exam_month');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Supplementary::Join('students', 'students.id', '=', 'supplementaries.student_id')->where('supplementaries.exam_year',$changerequertcurrentsuppexamyear)->where($conditions)->whereNull('supplementaries.locksumbitted')->whereNull('supplementaries.locksubmitted_date')->paginate($defaultPageLimit,$fields);

		}else{
			$master = Supplementary::Join('students', 'students.id', '=', 'supplementaries.student_id')->where('supplementaries.exam_year',$changerequertcurrentsuppexamyear)->where($conditions)->whereNull('supplementaries.locksumbitted')->whereNull('supplementaries.locksubmitted_date')->get($fields);
		}
		return $master;
	}
	
	 public function getsuppchangerequestallStudentdatanotfeespay($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
		$fields = array('supplementaries.student_id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','supplementaries.supp_student_change_requests','supplementaries.exam_month');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = Supplementary::Join('students', 'students.id', '=', 'supplementaries.student_id')->where('supplementaries.exam_year',$changerequertcurrentsuppexamyear)->where($conditions)->whereNotNull('supplementaries.locksumbitted')->whereNotNull('supplementaries.locksubmitted_date')->whereNull('supplementaries.is_department_verify')->paginate($defaultPageLimit,$fields);

		}else{
			$master = Supplementary::Join('students', 'students.id', '=', 'supplementaries.student_id')->where('supplementaries.exam_year',$changerequertcurrentsuppexamyear)->where($conditions)->whereNotNull('supplementaries.locksumbitted')->whereNotNull('supplementaries.locksubmitted_date')->whereNull('supplementaries.is_department_verify')->get($fields);
		}
		return $master;
	}
	
	public function getchangerequestlocksumbittedfeesnotpays($formId=null,$isPaginate=true){ 
		$conditions = Session::get($formId. '_conditions'); 
		$fields = array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','students.ssoid','students.student_change_requests','students.exam_month');
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = ChangeRequestStudent::Join('applications', 'applications.student_id', '=', 'change_request_students.student_id')
		->Join('students', 'students.id', '=', 'change_request_students.student_id')
		->Join('change_requert_old_student_fees', 'change_requert_old_student_fees.student_id', '=', 'change_request_students.student_id')
		->Join('student_fees', 'student_fees.student_id', '=', 'change_request_students.student_id')
		->where('change_request_students.student_change_requests',2)
		->where('change_request_students.student_update_application',1)
		->where('students.student_change_requests',2)
		->whereColumn('change_requert_old_student_fees.total', '>', 'student_fees.total')
		->where($conditions)->whereNull('students.update_change_requests_challan_tid')
		->whereNull('applications.deleted_at')
		->whereNull('students.deleted_at')
		->whereNull('change_requert_old_student_fees.deleted_at')
		->whereNull('student_fees.deleted_at')
		->where('applications.locksumbitted',1)->paginate($defaultPageLimit,$fields);
		}else{
		 $master = $master = ChangeRequestStudent::Join('applications', 'applications.student_id', '=', 'change_request_students.student_id')
		->Join('students', 'students.id', '=', 'change_request_students.student_id')
		->Join('change_requert_old_student_fees', 'change_requert_old_student_fees.student_id', '=', 'change_request_students.student_id')
		->Join('student_fees', 'student_fees.student_id', '=', 'change_request_students.student_id')
		->where('change_request_students.student_change_requests',2)
		->where('change_request_students.student_update_application',1)
		->where('students.student_change_requests',2)
		->whereColumn('change_requert_old_student_fees.total', '>', 'student_fees.total')
		->where($conditions)->whereNull('students.update_change_requests_challan_tid')
		->whereNull('applications.deleted_at')
		->whereNull('students.deleted_at')
		->whereNull('change_requert_old_student_fees.deleted_at')
		->whereNull('student_fees.deleted_at')
		->where('applications.locksumbitted',1)->get($fields);
		}
		return $master; 
	}
	
	
	
	public function getAicenterVerificationPart($auth_user_id=null,$exam_month=null,$is_verifier_verify=null){
		$conditions = array();
		$conditions['applications.is_ready_for_verifying'] = 1;
		$conditions['applications.locksumbitted'] = 1;
		$selected_session = CustomHelper::_get_selected_sessions();
		
		$conditions['students.exam_year'] = $selected_session;
		
        if(!empty($exam_month)){
			$conditions['students.exam_month']=$exam_month;
		}
		
		$role_id = Session::get('role_id');
		$verifier_id = config("global.verifier_id");
		$super_admin_id = config("global.super_admin_id");
		if($is_verifier_verify != "all"){
			$conditions['students.ao_status'] = $is_verifier_verify;
		}		
		
		$total_all_student = 0;
		if(!empty($auth_user_id) && $auth_user_id != null){
			// dd($auth_user_id);
			$total_all_student = Student::join('applications', 'applications.student_id', '=', 'students.id')
			->where($conditions)->whereIn('students.ai_code',$auth_user_id)->count();
		}else{
			//$total_all_student = Student::join('applications', 'applications.student_id', '=', 'students.id')
			//->where($conditions)->count();	
		}
		
		return $total_all_student;
    } 
	
	
	
	public function getDSGStudentData($exam_month=null,$AuthUser_id=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		$conditions ['students.exam_year']=$selected_session;
		$conditions['students.is_dgs']='1';
		if(@$exam_month){
			$conditions['students.exam_month']=$exam_month;
		}
		$conditions['students.exam_year'] = $selected_session;
		 if(!empty($auth_user_id) && $auth_user_id!= null){
		 $total_registred_all_students = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereIn('students.ai_code',$auth_user_id)->count();		}
		 else{
			$total_registred_all_students = Student::leftJoin('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->count();
		}
		
		
		return $total_registred_all_students;
		
	}
	
	
	
	public function getSingleScreenData($formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$master = array();
		$fields=['id','module_id','module','sub_module_id','sub_module','status','global_variables','table_details','table_name'];
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master=SingleScreenDate::where($conditions)->paginate($defaultPageLimit,$fields); 
		}
		else{
			$master=SingleScreenDate::where($conditions)->get($fields); 
		}
		
		return $master; 
		
	}
	
	
	public function getVerficationAicodesData($user_type,$formId=null,$isPaginate=true){
		$conditions = Session::get($formId. '_conditions');
		$table_name=@$user_type."_aicodes";
		$master = array();
		//print_r($conditions);
		$rawExtraCondAdd =1;
		if(!empty($conditions['aicodes'])){
			if($conditions['aicodes'] != null){
				$rawExtraCondAdd = ' aicodes like "%' . $conditions['aicodes'] .'%"';
			} 
			unset($conditions['aicodes']);
		}  
		
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master=Db::table(@$table_name)->whereRaw($rawExtraCondAdd)->where($conditions)->paginate($defaultPageLimit); 
		}
		else{
			$master=Db::table(@$table_name)->whereRaw($rawExtraCondAdd)->where($conditions)->get(); 
		}
		
		return $master; 
		
	}
	
	   
	


}


