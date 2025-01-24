<?php 
namespace App\Helper;
use DB;
use Cache;
use Route;
use Config;
use Session;
use Auth;
use App\Models\ExamcenterDetail;
use Illuminate\Support\Facades\Crypt;
use App\Models\Student;
use App\Models\AicenterDetail;
use App\Models\AdmissionSubject; 
use App\Models\Application;
use App\Models\Toc;
use App\Models\TocMark;
use App\Models\Address;
use App\models\ExamSubject;
use App\Models\Supplementary;
use App\Http\Controllers\Controller;
class CustomHelper {
	
    public static function examSubjectIsCheckedStatus($student_id,$subject_id=null){
		$isChecked = true; 
		$table = DB::table('exam_subjects');
		$table->where('student_id', $student_id);
		$table->where('subject_id', $subject_id);
		$table->where('deleted_at', null);
		// $get_rows = $table->get();
		$count_rows = $table->count();
		if($count_rows === 0){
			$isChecked = false;
		}
		return $isChecked;
    }  
 

	public static function getWhichRouteRedirect($student_id=null){
		$studentsso = Student::where('id', $student_id)->orderby('course','desc')->get(['ssoid'])->first(); 
		 
		$student = Student::where('ssoid', $studentsso->ssoid)->orderby('course','desc')->get(['is_eligible','course','id','exam_month','enrollment','exam_year'])->first(); 
		$student_id = $student->id;
		$application = Application::where('student_id', $student_id)->get(['id','locksumbitted'])->first();
		$custom_controller_obj = new Controller; 
		$streams = $custom_controller_obj->_getAllowedStreams();
		
		$streams = json_decode($streams);
		
		$pos = strpos(@$streams->msg, ' Date&Time not');
		$routeName = null; 
		$registrationdate = $streams->status;
		
		if(@$registrationdate && $registrationdate == true){}else{
			return $routeName;  
		}
		$current_admission_session_id = Config::get("global.form_admission_academicyear_id");
		$current_exam_month_id = Config::get("global.form_current_exam_month_id");
		
		//not match with current session show 10th and 12th option for new registraion
		// if(@$student->exam_year == $current_admission_session_id && @$student->exam_month == $current_exam_month_id
		
		if(@$student->exam_year == $current_admission_session_id ){
			
			if(@$student->course && $student->course == 12){
				if($student->is_eligible == 1){
					$routeName = route("preview_details",Crypt::encrypt($student_id)); 
				}else{ 
					if(@$application->locksumbitted && $application->locksumbitted == 1){
						$routeName = route("preview_details",Crypt::encrypt($student_id)); 
					}else{
						
						$routeName = route("persoanl_details",Crypt::encrypt($student_id)); 
					}
				}
			}else if(@$student->course && $student->course == 10){
				if($student->is_eligible == 1){
					$routeName = route("new_bothcourse_registraion",Crypt::encrypt(12));
				}else{ 
					if(@$application->locksumbitted == 1){
						$routeName = route("preview_details",Crypt::encrypt($student_id)); 
					}else{
						$routeName = route("persoanl_details",Crypt::encrypt($student_id)); 
					}
				}
			}
		}else{ 
			$routeName = route("new_bothcourse_registraion",Crypt::encrypt(14));
			return $routeName;   
			
			if(@$student->exam_year >= 124){
				if(@$student->course && $student->course == 12){
					if($student->is_eligible == 1){
						$routeName = null;
					}else{ 
						if($application->locksumbitted == 1){
							$routeName = route("new_bothcourse_registraion",Crypt::encrypt(12));
						}else{
							$routeName = route("persoanl_details",Crypt::encrypt($student_id)); 
						}
					}
					if($student->is_eligible == 1 && $application->locksumbitted == 1){
						//nothing
					}else{
						$routeName = route("new_bothcourse_registraion",Crypt::encrypt(12));
					} 
				}else if(@$student->course && $student->course == 10){
					if($student->is_eligible == 1){
						$routeName = route("new_bothcourse_registraion",Crypt::encrypt(12));
					}else{ 
						if($application->locksumbitted == 1){ 
							$routeName = route("new_bothcourse_registraion",Crypt::encrypt(14));
						}else{
							$routeName = route("new_bothcourse_registraion",Crypt::encrypt(14));
						}
					}
				}
			}else{
				if(@$student->course && $student->course == 12){
					if($application->locksumbitted == 1){
						$routeName = null;
					}else{
						$routeName = route("new_bothcourse_registraion",Crypt::encrypt(12));
					}
				}else if(@$student->course && $student->course == 10){
					if(@$application->locksumbitted && $application->locksumbitted == 1){
						$routeName = route("new_bothcourse_registraion",Crypt::encrypt(12));
					}else{
						// $routeName = "10 personal";
						$routeName = route("new_bothcourse_registraion",Crypt::encrypt(14));
					}
				}
			} 
		} 
		return $routeName;   
	}
	public static function revalSubjectIsCheckedStatus($student_id,$subject_id=null){
		$isChecked = true; 
 
		$combo_name = 'reval_exam_year';

		$custom_controller_obj = new Controller; 
		$reval_exam_year = $custom_controller_obj->master_details($combo_name);
 
		$combo_name = 'reval_exam_month';
		$reval_exam_month = $custom_controller_obj->master_details($combo_name);
 
		$reval_exam_year = $reval_exam_year[1];
		$reval_exam_month = $reval_exam_month[1];
 
	

		$table = DB::table('reval_student_subjects');
		$table->where('student_id', $student_id);
		$table->where('subject_id', $subject_id);
		$table->where('exam_year', $reval_exam_year);
		$table->where('exam_month', $reval_exam_month);
		$table->where('deleted_at', null);
		// $get_rows = $table->get();
		$count_rows = $table->count();
		
		if($count_rows === 0){
			$isChecked = false;
		}
		return $isChecked;
    } 

	
	public static function helper_master_details($combo_name=null,$orderBy=null){ 
		$condtions = null;
		$result = array();
		
		if(!empty($combo_name)){
			$condtions = ['status' => 1,'combo_name' => $combo_name];
		} 
		$mainTable = "masters";
		$cacheName = $mainTable. "_".$combo_name;
		Cache::forget($cacheName);
		
		
		
		
		if (Cache::has($cacheName)) { 
			$result = Cache::get($cacheName);
		}else{ 
			
		
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable,$orderBy) { 
				$current_role = @session::get("role_id");
				$form_admission_academicyear_id = Config::get('global.form_admission_academicyear_id');
				
				
				$admission_academicyear_id = Config::get('global.admission_academicyear_id');
				$current_year_id=Config::get('global.current_year');
				$previous_year_id=Config::get('global.previous_year');
				$formsession=Config::get('global.form_session_changed');
				$allowOnlyPreviousYears=Config::get('global.allowOnlyPreviousYears');
				$allowOnlyCurrentYears=Config::get('global.allowOnlyCurrentYears');
				$allowPreviousAndCureentYears=Config::get('global.allowPreviousAndCureentYears');
				$extraCondtions = null;
				//dd($allowOnlyPreviousYears);
				if(in_array($current_role,$allowOnlyPreviousYears)){
					$extraCondtions = array($previous_year_id);
                }else if(in_array($current_role,$allowOnlyCurrentYears)){
					$extraCondtions = array($current_year_id);
                }else if(in_array($current_role,$allowPreviousAndCureentYears)){
					$extraCondtions = array($current_year_id,$previous_year_id);
                }    
				if($current_role == Config::get('global.Printer')){
					$extraCondtions = array(125,126);
				}
				
				//@dd($extraCondtions);
				$orderByCol = "id";
				$orderByType = "DESC";
				if($orderBy == null){
					$orderByCol = "option_val";
					$orderByType = "ASC";
				}
				if(@$extraCondtions){ 
					$resultTemp = DB::table($mainTable)
					->whereIn('option_id',$extraCondtions)
					->where($condtions)->orderBy($orderByCol, $orderByType)->get()->pluck('option_val','option_id');  
				}else{
					$resultTemp = DB::table($mainTable)->where($condtions)->orderBy($orderByCol, $orderByType)->get()->pluck('option_val','option_id');
				}  
				$result = array();				
				$currentYearShow = true;
				if(!in_array($current_role,$formsession)){
					$currentYearShow = false;
                }
				
				
				if(@$resultTemp){
					foreach($resultTemp as $k => $year){
						if($k == $current_year_id){
							if(@$currentYearShow){
								$result[$k] = $year . "(Current Admission Year)";
							}
						}else if($k == $previous_year_id){
							$result[$k] =  $year. "(Previous Admission Year)";
						}else {
							$result[$k] = $year ;
						}
					}
				} 
				$result = collect($result); 
				
				return $result; 
			});
		}
		return $result; 
	}		
	  
	
	public static function _get_medium($combo_name=null){ 
		$combo_name = 'midium';
		$result = CustomHelper::helper_master_details($combo_name);
		return $result;
	}	
	public static function _get_disability($combo_name=null){ 
		$result = CustomHelper::helper_master_details($combo_name); 
		return $result;
	}		
	public static function _get_category_a($combo_name=null){ 
		$combo_name = 'categorya';
		$result = CustomHelper::helper_master_details($combo_name); 
		return $result;
	}		
	public static function _get_gender_id($combo_name=null){ 
		$combo_name = 'gender';
		$result = CustomHelper::helper_master_details($combo_name); 
		
		return $result;
	}
	
	public static function _get_admission_sessions($combo_name=null){ 
		$combo_name = 'admission_sessions';
		$result = CustomHelper::helper_master_details($combo_name);
		
		return $result;
	} 

	public static function _get_selected_sessions($combo_name=null){
		$currentSession = Session::get("current_admission_sessions");
		return $currentSession;
	} 

	public static function _get_subject($combo_name=null){
		$condtions = null;
		$result = array();
		if(@$course && $course != null){  
			$condtions = ['course' => $course,'deleted' => 0]; 
		} else{
			$condtions = ['deleted' => 0];
		}
		
		$mainTable = "subjects";
		$cacheName = "Subjects";
		if(@$course && $course != null){  
			$cacheName = "Subjects_". $course;
		}
		if (Cache::has($cacheName)) { //Cache::forget($mainTable);
			$result = Cache::get($cacheName);
		}else{ 
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) { 
				return $result = DB::table($mainTable)->where($condtions)->get()->pluck('name','id');
				
			});			
		}  
		return $result; 
	}
	
	public static function roleandpermission(){ 
		$role_id = Session::get('role_id'); 
		$rolehaspermissions = DB::table('role_has_permissions')->where('role_id',$role_id)->pluck('permission_id');
		$permissions = DB::table('permissions')->whereIn('id',$rolehaspermissions)->pluck('name')->toarray();
	    return $permissions;
	}


	public static function getdatamaster(){ 
		$getdatamaster = DB::table('masters')->where('combo_name','is_ai_center_material_publish')->first('option_val');
	    return $getdatamaster;
	}

	public static function getdatamastersexam(){ 
	$getdatamaster = DB::table('masters')->where('combo_name','is_exam_center_material_publish')->first('option_val');
	    return $getdatamaster;
	}

	

	public static function checkAlredaySchoolMapForExamCenter($school_id=null){
		//check school id exist in the exam center detail table.
		$current_admission_session_id = Config::get('global.current_admission_session_id');
		$masterCount = ExamcenterDetail::where('school_id',$school_id)->where('exam_year',$current_admission_session_id)->count();
		return $masterCount;
	} 
	
	public static function _changerole(){
		$result= 0;
		if (Auth::check()) {
			$user_id = Auth::user()->id;
			$result = DB::table('model_has_roles')
				->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
				->where('model_id',$user_id)->orderBy('sort', 'ASC') 
				->get()->pluck('name','role_id');
		}
		
		return $result;
	}
	public static function _getCurrentLoginRole(){
		$role_id = Session::get('role_id'); 
		return $role_id;
	}
	

	public static function getstudentenrollmentstatus($aicode=null,$stream=null,$course=null,$supplementary=0,$examcenterdetailid = null,$centerallotmentid=null){
		
		$conditionArray = array();	
		
		$conditionArray['exam_year'] = config("global.current_admission_session_id");  
		$conditionArray['stream'] = @$stream;
		$conditionArray['course'] = @$course;
		$conditionArray['supplementary'] = @$supplementary; 
		$conditionArray['examcenter_detail_id'] = @$examcenterdetailid;
		$conditionArray['ai_code'] = @$aicode;		
		$conditionArray['center_allotment_id'] = @$centerallotmentid; 
		
		$enrollmentcount = DB::table('student_allotments')->where($conditionArray)->get('enrollment')->count(); 
		if($enrollmentcount > 0){
			return 1;
		}else{
			return 0;
		}
	}

	public static function _getStudentAiCodeWise($ai_code=null,$conditionstream=null){ 
		$selected_session = CustomHelper::_get_selected_sessions(); 
		if(@$selected_session){
			$conditions['students.exam_year'] = $selected_session;
		}
		if(@$ai_code){
			$conditions['students.ai_code'] = $ai_code ; 
		}
		 if(!empty($conditionstream)){
			$totalStudents = Student::where($conditions)->where('students.stream',$conditionstream)->count();
		 }else{
		  $totalStudents = Student::where($conditions)->count(); 
		 }
		
		return $totalStudents;
	}


	public static function _getStudentSupplementaryAiCodeWise($ai_code=null,$exam_month=null){ 
		$selected_session = CustomHelper::_get_selected_sessions(); 
		if(@$selected_session){
			$conditions['supplementaries.exam_year'] = $selected_session;
		}
		if(@$ai_code){
			$conditions['supplementaries.ai_code'] = $ai_code ; 
		}
		if(@$exam_month){
			$conditions['supplementaries.exam_month'] = $exam_month ; 
		}
		$totalStudents = Supplementary::where($conditions)->count();
		return $totalStudents;
	}


	public static function _getStudentLockSubmttedAiCodeWise($ai_code=null,$conditionstream=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		if(@$selected_session){
			$conditions['students.exam_year'] = $selected_session;
		}
		if(@$ai_code){
			$conditions['students.ai_code'] = $ai_code  ; 
		} 
		$conditions['applications.locksumbitted'] = 1;
		if(!empty($conditionstream)){
		$totalStudents = DB::table('students')
			->leftJoin('applications', 'applications.student_id', '=', 'students.id')
			->where($conditions)->where('students.stream',$conditionstream)->count(); 
		}else{
		 $totalStudents = DB::table('students')
			->leftJoin('applications', 'applications.student_id', '=', 'students.id')
			->where($conditions)->count(); 	
		}			
		return $totalStudents;
		
		
	}
	
	
	public static function _getStudentLockSubmttedcourse10AiCodeWise($ai_code=null,$conditionstream=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		if(@$selected_session){
			$conditions['students.exam_year'] = $selected_session;
		}
		if(@$ai_code){
			$conditions['students.ai_code'] = $ai_code  ; 
		} 
		$conditions['applications.locksumbitted'] = 1;
		$conditions['students.course'] = 10;
		if(!empty($conditionstream)){
		$totalStudents = DB::table('students')
			->leftJoin('applications', 'applications.student_id', '=', 'students.id')
			->where($conditions)->where('students.stream',$conditionstream)->count(); 
		}else{
		 $totalStudents = DB::table('students')
			->leftJoin('applications', 'applications.student_id', '=', 'students.id')
			->where($conditions)->count(); 	
		}			
		return $totalStudents;
		
		
	}
	
	public static function _getStudentLockSubmttedcourse12AiCodeWise($ai_code=null,$conditionstream=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		if(@$selected_session){
			$conditions['students.exam_year'] = $selected_session;
		}
		if(@$ai_code){
			$conditions['students.ai_code'] = $ai_code  ; 
		} 
		$conditions['applications.locksumbitted'] = 1;
		$conditions['students.course'] = 12;
		if(!empty($conditionstream)){
		$totalStudents = DB::table('students')
			->leftJoin('applications', 'applications.student_id', '=', 'students.id')
			->where($conditions)->where('students.stream',$conditionstream)->count(); 
		}else{
		 $totalStudents = DB::table('students')
			->leftJoin('applications', 'applications.student_id', '=', 'students.id')
			->where($conditions)->count(); 	
		}			
		return $totalStudents;
		
		
	}
	

	public static function _getSupplementaryStudentLockSubmttedAiCodeWise($ai_code=null,$exam_month =null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		if(@$selected_session){
			$conditions['supplementaries.exam_year'] = $selected_session;
		}
		if(@$ai_code){
			$conditions['supplementaries.ai_code'] = $ai_code  ; 
		}
		if(@$exam_month){
			$conditions['supplementaries.exam_month'] = $exam_month ; 
		}
		$conditions['supplementaries.locksumbitted'] = 1;
		 
		$totalStudents = DB::table('supplementaries')->where($conditions)->count();  
		return $totalStudents;
	}


	public static function _getStudentFeePaidAiCodeWise($ai_code=null,$conditionstream=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		if(@$selected_session){
			$conditions['students.exam_year'] = $selected_session;
		}
		if(@$ai_code){
			$conditions['students.ai_code'] = $ai_code  ; 
		} 
		$conditions['students.is_eligible'] = 1; 
        if(!empty($conditionstream)){
		$totalStudents = DB::table('students')
			->where($conditions)->where('students.stream',$conditionstream)->count(); 
		}else{
		$totalStudents = DB::table('students')
			->where($conditions)->count(); 	
		}
		return $totalStudents;
	} 
	
	
	public static function _getStudentnotFeePaidAiCodeWisebutlocksumbitted($ai_code=null,$conditionstream=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		if(@$selected_session){
			$conditions['students.exam_year'] = $selected_session;
		}
		if(@$ai_code){
			$conditions['students.ai_code'] = $ai_code  ; 
		} 
		$conditions['applications.locksumbitted'] = 1; 
		$conditions['applications.fee_status'] = 0; 
        if(!empty($conditionstream)){
		$totalStudents = DB::table('students')->
		Join('student_fees', 'student_fees.student_id', '=', 'students.id')->
		Join('applications', 'applications.student_id', '=', 'students.id')->
		where($conditions)->
		where('student_fees.total', ">",0)->
		where('students.stream',$conditionstream)->count();  	
		}else{
		$totalStudents = DB::table('students')->
		Join('student_fees', 'student_fees.student_id', '=', 'students.id')->
		Join('applications', 'applications.student_id', '=', 'students.id')->
		where($conditions)->
		where('student_fees.total', ">",0)->count(); 	
		}
		 
		return $totalStudents;
	} 

	public static function _getSupplementaryStudentFeePaidAiCodeWise($ai_code=null,$exam_month=null){
		$conditions = array();
		$selected_session = CustomHelper::_get_selected_sessions();
		if(@$selected_session){
			$conditions['supplementaries.exam_year'] = $selected_session;
		}
		if(@$ai_code){
			$conditions['supplementaries.ai_code'] = $ai_code  ; 
		}
        if(@$exam_month){
			$conditions['supplementaries.exam_month'] = $exam_month ; 
		} 		
		$conditions['supplementaries.fee_status'] = 1; 

		$totalStudents = DB::table('supplementaries')->where($conditions)->count();  
		return $totalStudents;
	} 

	public static function getStudentResult($studentid = null,$subject_id=null)
	{ 
		$conditions = array(); 
		$conditions['exam_subjects.student_id'] = $studentid;
		$conditions['exam_subjects.subject_id'] = $subject_id;
		
		 $result1 = ExamSubject::where($conditions)->whereNull('deleted_at')->orderBy('exam_year','DESC')->first('exam_year','final_result');
		
		if(empty($result1->exam_year)){
			return @$result1->final_result; 
		}else{
			$result2 = ExamSubject::where($conditions)->whereNull('deleted_at')->where('exam_year',$result1->exam_year)->get()->count();

			if($result2 ==2){
				$conditions['exam_subjects.exam_year'] = $result1->exam_year;
				$conditions['exam_subjects.exam_month'] = 1;
				$result = ExamSubject::where($conditions)->whereNull('deleted_at')->first('final_result');
			}else{
				$conditions['exam_subjects.exam_year'] = $result1->exam_year;
				$result = ExamSubject::where($conditions)->whereNull('deleted_at')->first('final_result');
			  }	
			return @$result->final_result; 
		}
	}


	public static function rohitgetStudentResult($studentid = null,$subject_id=null){ 
		$conditions = array(); 
		$conditions['exam_subjects.student_id'] = $studentid;
		$conditions['exam_subjects.subject_id'] = $subject_id;
		
		$resultExamYearExamMonth = ExamSubject::where($conditions)->whereNull('deleted_at')->orderBy('exam_year','DESC')->orderBy('exam_month','ASC')->first('exam_year','exam_month','final_result');
		
		if(empty($resultExamYearExamMonth->exam_year)){
			return @$resultExamYearExamMonth->final_result; 
		}else{
			return @$resultExamYearExamMonth->final_result; 
		}
	}

	public static function getSubjectMaxMinMarksMaster($subjectid=null,$boardid=null){ 
		
		$tsmconditions = array();
		//$tsmconditions['TocSubjectMaster.status'] = 1;
		$tsmconditions['subject_id'] = $subjectid;
		$tsmconditions['board_id'] = $boardid;
		$tocsubjectmasterdata = DB::table('toc_subject_masters')->where($tsmconditions)->get();
		$maxmarksarr = array();
		if(!empty($tocsubjectmasterdata))
		{
			foreach($tocsubjectmasterdata as $tsmdkey=>$tsmdval)
			{
				$maxmarksarr[$tsmdval->type] = $tsmdval->value;
			}
		}
		return $maxmarksarr;
	}
	
	public static function _checkIsRouteExists($route_name=null){
		if(!empty($route_name) && Route::has($route_name)) {
			return true;
		}
		return false;
	}
	
	public static function getStartAndEndDate($start_date=null,$end_date=null,$table_name=null,$field_name='created_at'){
		$table_name = 'rs_'.$table_name;
		$date=array();
		$rawQuery = null;
		$start_date=str_replace("T", " ",@$start_date);
		$end_date=str_replace("T", " ",@$end_date);
		
		if(@$start_date && @$end_date){
			
			$rawQuery =$table_name. '.' . $field_name . " between '" . $start_date . "' and '" . $end_date . "'";
		}else if(@$start_date){
			$rawQuery = $table_name . '.' . $field_name . " >= '" . $start_date . "'"; 
		}else if(@$end_date){
			$rawQuery = $table_name . '.' . $field_name . " <= '" . $end_date . "'"; 
		}		
		return $rawQuery;
	}
	
	public static function _getIsStudentLogin(){
		$role_id = @Session::get('role_id'); 
		
		$student = Config::get("global.student");
		if($role_id == $student){
			return true;
		}
		return false;
	} 

	public static function _getCountDownTimerDetails(){
		$login['start'] = $login_start = Session::get('login_start');
		$login['end'] = $login_end = Session::get('login_end'); 
		$t=time();
		$current = date("M j, Y H:i:s",$t);
		$custom_controller_obj = new Controller; 
		$ip = $custom_controller_obj->my_current_ip();
		
		$whiteListMasterIps = Config::get("global.whiteListMasterIps");
		$login['currentwithaddingtenmin'] = null;
		if(in_array($ip,$whiteListMasterIps)){
			$login['currentwithaddingtenmin'] = date('M j, Y H:i:s', strtotime($current. ' +60 minutes'));
		}else{
			$login['currentwithaddingtenmin'] = date('M j, Y H:i:s', strtotime($current. ' +20 minutes'));
		} 
		return json_encode($login); 
	}
	
	public static function _checkPaymentAllowedOrNotHelper($stream=null,$gender_id=0){
		$custom_controller_obj = new Controller;
		return $result = $custom_controller_obj->_checkPaymentAllowedOrNot($stream=null,$gender_id=0);
	}
	
	public static function _CheckStudentFormLockAndSubmittHelper($student_id){
		$custom_controller_obj = new Controller; 
		return $result = $custom_controller_obj->_isCheckStudentFormLockAndSubmit($student_id);
	}
	
	public static function _getMyCurrentIp(){
		$custom_controller_obj = new Controller; 
		return $result = $custom_controller_obj->my_current_ip();
	}
	public static function _getMyMacAddress(){
		$custom_controller_obj = new Controller; 
		return $result = $custom_controller_obj->_my_mac_address();
	}

	public static function _getEnrollmentListMappedWithSSOId($ssoid=null){
		$custom_controller_obj = new Controller; 
		return $result = $custom_controller_obj->_getEnrollmentListMappedWithSSOId($ssoid);
	}
	public static function _getEnrollmentListLabeleMappedWithSSOId($ssoid=null){
		$custom_controller_obj = new Controller; 
		return $result = $custom_controller_obj->_getEnrollmentListLabeleMappedWithSSOId($ssoid);
	}

	public static function _get_selected_student_enrollment_by_student(){
		$currentSession = Session::get("selected_student_enrollment_by_student");
		return $currentSession;
	} 

	public static function _getCurrentLoginStudentEnrollment(){
		$current_student_enrollment_by_student = Session::get('selected_student_enrollment_by_student'); 
		return $current_student_enrollment_by_student;
	}
	
	
	public static function extractFirstNumberOfWords($text,$limit=50) {
		// Split the text into words
		$words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
		
		// Extract the first 50 words
		$first50Words = array_slice($words, 0, $limit);
		
		// Join the words back into a string
		$result = implode(' ', $first50Words);
		if(count($words) > $limit){
			$result .= " ... ";
		}
		return $result;
	}

	
	public static function _getAdddressForDisplay($student_id=null){
		$master = Address::where('student_id',$student_id)->first(); 

		$dispaly = "<span style='text-align: justify;text-justify: inter-word;'>" .  @$master->address1 . " " . 
					@$master->address2 . " " .  
					@$master->address3 . " " .  
					@$master->city_name . " " . 
					@$master->block_name  . " " . 
					@$master->district_name . " " . 
					@$master->state_name . "</span>";
		return @$dispaly;
	}
	
	
	public static function _getAdmissionSubjectsForDisplay($student_id=null){
		$fields = ['subject_id','is_additional'];
		$display = "";
		$master = AdmissionSubject::where('student_id',$student_id)->get($fields);
		$custom_controller_obj = new Controller; 
		$subject_list =  $custom_controller_obj->subjectList();
		if(@$master){
			$display .= "<span style='text-align: justify;text-justify: inter-word;'>";
			$coutner = 1;
			foreach($master as $k => $v){ 
				$display .= $coutner++ . ". " . @$subject_list[$v->subject_id];
				if(@$v->is_additional){
					$display .= "<span style='color:blue;font-size:12px;'>(Additional)</span>"; 
				}
				$display .= "<br>";
			}
			$display .= "</span>"; 
		}
		return @$display;
	}
	
	public static function _getTOCSubjectsForDisplay($student_id=null){
		$display = "";
		$fields = ['id'];		
		$masterTOC = Toc::where('student_id',$student_id)->first();
		$studentdata = Student::where('id',$student_id)->first(['adm_type']);
		if(@$masterTOC->id){
			$custom_controller_obj = new Controller; 
			$board_dropdown = $custom_controller_obj->getAdmissionTypeBords($studentdata->adm_type);
			 
			$display .= "<table>";
				$display .= "<tr style='color:black !important;'>";
					$display .= "<th>IS TOC</th>";
					$display .= "<th>Name Of Board</th>";
					$display .= "<th>Roll No</th>";
					$display .= "<th>Course</th>";
					if(@$masterTOC->year_fail){
						$display .= "<th>Years of Failing</th>";
					}
					if(@$masterTOC->year_pass){
						$display .= "<th>Years of Passing</th>";
					} 
				$display .= "</tr>"; 
				
				$display .= "<tr>";
					$display .= "<td>Yes</td>";
					$display .= "<td>" . @$board_dropdown[$masterTOC->board] . "</td>";
					$display .= "<td>" . @$masterTOC->roll_no . "</td>";
					$display .= "<td>" . @$masterTOC->course . "</td>";
					if(@$masterTOC->year_fail){
						$rsos_years_fail_dropdown = $custom_controller_obj->getRsosFailYearsList($masterTOC->year_fail);
						$display .= "<td>" . @$rsos_years_fail_dropdown[$masterTOC->year_fail] . "</td>";
					}
					if(@$masterTOC->year_pass){
						$rsos_years_dropdown = $custom_controller_obj->rsos_years();
						$display .= "<td>" . @$rsos_years_dropdown[$masterTOC->year_pass] . "</td>";
					}  
				$display .= "</tr>";  
			$display .= "</table>";
			
			if($studentdata->adm_type != 3){
				$fields = ["subject_id","theory","practical","total_marks"];
				$master = TocMark::where('toc_id',$masterTOC->id)->where('student_id',$student_id)->get($fields);
				$subject_list =  $custom_controller_obj->subjectList();
				if(@$master){
					$display .= "<table>";
					$display .= "<tr style='color:black !important;'>";
					$display .= "<th>Sr.No</th>";
					$display .= "<th>Subject</th>";
					$display .= "<th>Theory</th>";
					$display .= "<th>Practical</th>";
					$display .= "<th>Total Marks</th>";
					$display .= "</tr>"; 
					$counter = 1;
					foreach($master as $k => $v){ 
						$display .= "<tr>";
						$display .= "<td> " . $counter++ ."</td>"; 
						$display .= "<td> " .  @$subject_list[$v->subject_id] . " </td>";
						$display .= "<td> " .  @$v->theory . " </td>";
						$display .= "<td> " .  @$v->practical . " </td>";
						$display .= "<td> " .  @$v->total_marks . " </td>";
						$display .= "</tr>"; 
					} 
					$display .= "</table>";
					  
				}
			}
			
		} 
		return @$display;
	}
	
	
	public static function _getExamSubjectsForDisplay($student_id=null){
		$fields = ['subject_id','is_additional'];
		$display = "";
		$master = ExamSubject::where('student_id',$student_id)->get($fields);
		$custom_controller_obj = new Controller; 
		$subject_list =  $custom_controller_obj->subjectList();
		if(@$master){
			$display .= "<span style='text-align: justify;text-justify: inter-word;'>";
			$coutner = 1;
			foreach($master as $k => $v){ 
				$display .= $coutner++ . ". " . @$subject_list[$v->subject_id];
				if(@$v->is_additional){
					$display .= "<span style='color:blue;font-size:12px;'>(Additional)</span>"; 
				}
				$display .= "<br>";
			}
			$display .= "</span>"; 
		}
		return @$display;
	}
	
	
	
	
	public static function _getPreQualifcaionForDisplay($pre_qualification=null){ 
		$combo_name = 'pre-qualifi';
		$custom_controller_obj = new Controller; 
		$list = $custom_controller_obj->master_details($combo_name);
		$dispaly =  @$list[$pre_qualification];
		return @$dispaly;
	}
	
	public static function helpercheckIsStudentVerificationAllowAtVerifier($student_payment_datetime=null){
		$custom_controller_obj = new Controller; 
		$student_payment_datetime = $custom_controller_obj->checkIsStudentVerificationAllowAtVerifier($student_payment_datetime);
		return $student_payment_datetime;
	}
	
	public static function helpercheckIsPaymentRecivedOrNot($student_id=null){
		$custom_controller_obj = new Controller; 
		$status = $custom_controller_obj->checkIsPaymentRecivedOrNot($student_id);
		return $status;
	}
	public static function helpercheckIsClarificationMakePaymentAllowOrNot($student_id=null){
		$custom_controller_obj = new Controller; 
		$status = $custom_controller_obj->checkIsClarificationMakePaymentAllowOrNot($student_id);
		return $status;
	} 
	public static function getAICenterDetailsByAiCode($ai_code=null){  
		if(@$ai_code){
			$conditions['aicenter_details.ai_code'] = $ai_code ; 
		} 
		$name = AicenterDetail::where($conditions)->first(['college_name']);
		return @$ai_code . " - " . @$name->college_name;
	}
	
			
	public static function getUserIdOfVerifierAiCode($aicodes =null){ 
		$master = DB::table('verifier_aicodes')->where('aicodes','LIKE','%'.@$aicodes.'%')->whereNull('deleted_at')->first('user_id');  
		if(@$master->user_id){
			$master = @$master->user_id;
		}else{
			return false;
		} 
		return $master; 
	}

	



	
}
