<?php 
namespace App\Component;
use DB;
use Cache;
use Config;
use Session;
use App\Helper\CustomHelper; 
use App\models\Logs;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Component\CustomComponent;
use Validator; 
use Auth;
use App\Models\UserExaminerMap; 
use App\Models\StudentPracticalSlots; 
use App\Models\Student; 
use App\Models\ExamcenterDetail; 
use App\Models\StudentAllotmentMark; 
use Illuminate\Support\Facades\Crypt;
use App\Models\UserPracticalExaminer;
use App\Models\User;

class PracticalCustomComponent { 

	public function getDistrictIdByUserId($user_id=null){
		$conditions = null;
		$result = array();
		if(!empty($user_id)){
			$conditions = ['id' => $user_id];
		} 
		return $result = DB::table('users')->where($conditions)->first();
	}
	
	public function getCourseExamCenters($course=null){
		$conditions = array();
		if(@$course){
			$conditions['student_allotments.course'] = $course;
		}
		$role_id = Session::get('role_id');
		$deoRole = config("global.deo");
		if($role_id == $deoRole){ 
			$examiner_district_id = @Auth::user()->district_id;
			$conditions['examcenter_details.district_id'] = $examiner_district_id;
		}
		
	    $data = ExamcenterDetail::Join('student_allotments', 'student_allotments.examcenter_detail_id', '=', 'examcenter_details.id')->select('examcenter_details.id','examcenter_details.cent_name','examcenter_details.ecenter10','examcenter_details.ecenter12')->where($conditions)->groupBy('examcenter_details.id')->orderBy('examcenter_details.id','ASC')->get();
		$result = array();
		foreach($data as $k => $v){
			if($course == 10) {
				$result[$v->id] = $v->ecenter10 . '-'. $v->cent_name;
			}
			if($course == 12) {
				$result[$v->id] = $v->ecenter12 . '-'. $v->cent_name;
			}
		}
		return $result;
	}

	public function getExamcenterDetail($examcenter_detail_id=null){
		$conditions = null;
		$result = array();
		if(!empty($examcenter_detail_id)){
			$conditions = ['id' => $examcenter_detail_id];
		} 
		return $result = DB::table('examcenter_details')->where($conditions)->first();
	}
	
	public function getExaminerMappingList($formId=null,$isPaginate=true){
		$defaultPageLimit = config("global.defaultPageLimit");
		$conditions = array();
		
		$current_admission_session_id = Config::get("global.current_admission_session_id");
		$current_exam_month_id = Config::get("global.current_exam_month_id");
		$current_stream_id = Config::get("defaultStreamId");
		$auth_user_id = @Auth::user()->id;

		$conditions = Session::get($formId. '_conditions');
		$custom_component_obj = new CustomComponent();
		$isAdminStatus = $custom_component_obj->_checkIsAdminRole();
		if($isAdminStatus!=true){
			$conditions['user_examiner_maps.user_deo_id'] = @$auth_user_id;
		} 
	
		$master = array(); 
		if($isPaginate){ 
			$master = UserExaminerMap::join('users', 'users.id', '=', 'user_examiner_maps.user_practical_examiner_id')->join('examcenter_details', 'examcenter_details.id', '=', 'user_examiner_maps.examcenter_detail_id')
			//->Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
			->select(['users.ssoid','users.mobile','users.name','examcenter_details.ecenter10','examcenter_details.ecenter12','user_examiner_maps.*'])
			->where($conditions)
			->where('user_examiner_maps.exam_year',$current_admission_session_id)
			->where('user_examiner_maps.exam_month',$current_exam_month_id)
			->paginate($defaultPageLimit);
		} else {
			$master = UserExaminerMap::join('users', 'users.id', '=', 'user_examiner_maps.user_practical_examiner_id')->join('examcenter_details', 'examcenter_details.id', '=', 'user_examiner_maps.examcenter_detail_id')
			//->Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
			->select(['users.ssoid','users.mobile','users.name','examcenter_details.ecenter10','examcenter_details.ecenter12','user_examiner_maps.*'])
			->where($conditions)
			->where('user_examiner_maps.exam_year',$current_admission_session_id)
			->where('user_examiner_maps.exam_month',$current_exam_month_id)
			->get();
			// $master = UserExaminerMap::where($conditions)->get();
		}
		
		return $master; 
	}
	
	public function getPracticalMappedCenterListOrCount($formId=null,$count=false){
		$selected_session = CustomHelper::_get_selected_sessions();
		$custom_component_obj = new CustomComponent();
		$current_exam_year_id = Config::get('global.admission_academicyear_id');
		$current_exam_month_id = Config::get('global.current_exam_month_id');
		$current_stream = Config::get('global.defaultStreamId');
		$practicalexaminer = Config::get('global.practicalexaminer');
		$defaultPageLimit = config("global.defaultPageLimit");
		
		$conditions = Session::get($formId. '_conditions');
		
		$auth_user_id = Auth::user()->id;
		$conditions['user_examiner_maps.exam_year'] = $selected_session;
		$conditions['user_examiner_maps.exam_month'] = $current_exam_month_id;

		$isAdminStatus = $custom_component_obj->_checkIsAdminRole();
		if($isAdminStatus==true){
		}else{
			if(!empty($auth_user_id) && $auth_user_id != null){
				$conditions['user_examiner_maps.user_practical_examiner_id'] = $auth_user_id;
			}
		}

		
		$result = '';
		if($count == true){
			$result = UserExaminerMap::Join('users','users.id','=','user_examiner_maps.user_deo_id')
			->where($conditions)->count();
		} else { 
			$result = UserExaminerMap::Join('users','users.id','=','user_examiner_maps.user_deo_id')
			->select('user_examiner_maps.id','user_examiner_maps.user_practical_examiner_id','user_examiner_maps.is_lock_submit','user_examiner_maps.document','user_examiner_maps.user_deo_id','user_examiner_maps.examcenter_detail_id','user_examiner_maps.course','user_examiner_maps.subject_id','user_examiner_maps.stream','users.name','users.ssoid','user_examiner_maps.is_unlock','update_marks_entry')->where($conditions)->get();
		}
		return $result; 
	}

	public function getPracticalMappedCenterList($formId=null,$count=false){
		
		$selected_session = CustomHelper::_get_selected_sessions();
		$custom_component_obj = new CustomComponent();
		$current_exam_year_id = Config::get('global.admission_academicyear_id');
		$current_exam_month_id = Config::get('global.current_exam_month_id');
		$current_stream = Config::get('global.defaultStreamId');
		$practicalexaminer = Config::get('global.practicalexaminer');
		$defaultPageLimit = config("global.defaultPageLimit");
		
		$conditions = Session::get($formId. '_conditions');
		
		$auth_user_id = Auth::user()->id;
		$conditions['user_examiner_maps.exam_year'] = $selected_session;
		$conditions['user_examiner_maps.exam_month'] = $current_exam_month_id;
		$isAdminStatus = $custom_component_obj->_checkIsAdminRole();
		if($isAdminStatus==true){
		}else{
			if(!empty($auth_user_id) && $auth_user_id != null){
				$conditions['user_examiner_maps.user_practical_examiner_id'] = $auth_user_id;
			}
		}

	
		$result = '';
		if($count == true){
			$result = UserExaminerMap::Join('users','users.id','=','user_examiner_maps.user_deo_id')
			->where($conditions)->count();
		} else { 
			$result = UserExaminerMap::Join('users','users.id','=','user_examiner_maps.user_deo_id')
			->select('user_examiner_maps.id','user_examiner_maps.user_practical_examiner_id','user_examiner_maps.is_lock_submit','user_examiner_maps.document','user_examiner_maps.user_deo_id','user_examiner_maps.examcenter_detail_id','user_examiner_maps.course','user_examiner_maps.subject_id','user_examiner_maps.stream','users.name','users.ssoid','user_examiner_maps.is_unlock','update_marks_entry')->where($conditions)->paginate($defaultPageLimit);
		}
		return $result; 
	}
	
	public function getPracticalSubjectMaxMarks($subject_id=null){   
		$conditions = null;
		$result = array();
		if(!empty($subject_id)){
			$conditions['subjects.id'] = $subject_id; 
		} 
		
		$mainTable = "subjects";
		$cacheName = "practical_subjects_min_max_marks_".$subject_id;
		Cache::forget($cacheName);
		$result['practical_min_marks'] = 0;
		$result['practical_max_marks'] = 0;
		
		if (Cache::has($cacheName)){
			$result = Cache::get($cacheName);
		}else{ 
			$result = Cache::rememberForever($cacheName, function () use ($conditions, $mainTable) { 
				$data = DB::table($mainTable)->where($conditions)->select('practical_min_marks','practical_max_marks')->first();
				$result['practical_min_marks'] = $data->practical_min_marks;
				$result['practical_max_marks'] = $data->practical_max_marks;
				return $result;
			});
		}  
		return $result;
	}


	public function getPracticalStudentList($examcenter_detail_id=null,$subject_id=null,$paginate=true,$formId=null){
		$selected_session = CustomHelper::_get_selected_sessions();
		$defaultPageLimit = config("global.defaultPageLimit");
		// $defaultPageLimit = 5;
		$current_exam_year_id = Config::get('global.admission_academicyear_id');
		$current_exam_month_id = Config::get('global.current_exam_month_id');
		$current_stream = Config::get('global.defaultStreamId');
		$conditions = array(); 		
		$conditions = Session::get($formId. '_conditions');
		$conditions['student_allotment_marks.exam_year'] = $selected_session;
		$conditions['student_allotment_marks.exam_month'] = $current_exam_month_id;
		$conditions['student_allotment_marks.examcenter_detail_id'] = $examcenter_detail_id;
		$conditions['student_allotment_marks.subject_id'] = $subject_id;
		$conditions['student_allotment_marks.is_exclude_for_practical'] = 0;
		
		if($paginate){
			$result = StudentAllotmentMark::Join("students", "students.id","=", "student_allotment_marks.student_id")->Join("student_allotments", "student_allotments.id","=", "student_allotment_marks.student_allotment_id")
			->where($conditions)
			->whereNull('student_allotments.deleted_at')
			->orderBy('student_allotment_marks.enrollment')
			->select(['student_allotment_marks.id','student_allotment_marks.student_practical_slot_id','student_allotment_marks.enrollment','student_allotment_marks.practical_absent','student_allotment_marks.final_practical_marks','students.name','student_allotment_marks.is_practical_lock_submit','student_allotment_marks.is_update_practical_marks_practical_examiner','students.name'])->paginate($defaultPageLimit);
		} else {
			$result = StudentAllotmentMark::Join("students", "students.id","=", "student_allotment_marks.student_id")->Join("student_allotments", "student_allotments.id","=", "student_allotment_marks.student_allotment_id")
			->where($conditions)
			->whereNull('student_allotments.deleted_at')
			->orderBy('student_allotment_marks.enrollment')
			->select(['student_allotment_marks.id','student_allotment_marks.student_practical_slot_id','student_allotment_marks.enrollment','student_allotment_marks.practical_absent','student_allotment_marks.final_practical_marks','students.name','student_allotment_marks.is_practical_lock_submit','student_allotment_marks.is_update_practical_marks_practical_examiner','students.name'])->get();
		}
		return $result; 
	}
	
	public function isValidPracticalMarks($inputs,$subjectMinMarks,$subjectMaxMarks) { 
		$isValid = true; 
		$errors = null; 
		$value =1;
		if(isset($inputs['page_type'])){
			$value=0;
		}
		
		$validator = Validator::make([], []);
		$errMsg = '';
		$response = array();
		//@dd($inputs['data']);
		$objController = new Controller();
		$combo_name = 'practical_marks_submission_start_date';
		$practcal_start_date_arr = $objController->master_details($combo_name);
		$practical_start_date = @$practcal_start_date_arr[1];
		
		$combo_name = 'practical_marks_submission_end_date';
		$practcal_end_date_arr = $objController->master_details($combo_name);
		$practical_last_date = @$practcal_end_date_arr[1];
		
		if($isValid==true && strtotime(date("Y-m-d H:i:s")) < strtotime($practical_start_date)){ 
			$fld = 'final_practical_marks';
			$errMsg = 'Practical marks submission will be start on '.date("d-m-Y",strtotime($practical_start_date));
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg); 
			$isValid = false;
		}
	
		// echo "date :".date("Y-m-d h:i:s")."</br>P :".$practical_last_date."</br></br>"; die;
		if($isValid==true && strtotime(date("Y-m-d H:i:s")) > strtotime($practical_last_date)){  
			$fld = 'final_practical_marks';
			$errMsg = 'Practical marks submission date has been closed';
			$errors = $errMsg;
			$validator->getMessageBag()->add($fld, $errMsg); 
			$isValid = false;
		}
		// echo "isValid : " .$isValid; die;
		
		if($isValid==true && isset($inputs['data'])){ 
		//@dd($data);
			foreach($inputs['data'] as $k => $data){
			
				$final_practical_marks = $data['final_practical_marks'];
				
				if(isset($data['practical_absent']) && $data['practical_absent'] == 'on' && isset($final_practical_marks) 
					&& (!empty($final_practical_marks) || $final_practical_marks != 'null') ){
					$fld = 'final_practical_marks';
					$errMsg = 'You cant enter absent and marks together at sr. no '. ($k+$value);
					// $errMsg = 'You cant enter absent and marks together at sr. no ';
					$errors = $errMsg;
					$validator->getMessageBag()->add($fld, $errMsg); 
					$isValid = false;
				}
				
				if(!isset($data['practical_absent'])){				
					if($isValid==true && $final_practical_marks!=0 && ($final_practical_marks=='null')){
						$fld = 'final_practical_marks';
						$errMsg = 'Entered marks should be valid at  sr. no '. ($k+$value);
						$errors = $errMsg;
						$validator->getMessageBag()->add($fld, $errMsg); 
						$isValid = false;
					}
					
					if($isValid==true && !is_numeric($final_practical_marks)) {
						$fld = 'final_practical_marks';
						$errMsg = 'Entered marks should be valid at sr. no '. ($k+$value);
						$errors = $errMsg;
						$validator->getMessageBag()->add($fld, $errMsg); 
						$isValid = false;
					}
					
					if($isValid==true){
						// if(($subjectMinMarks <= $final_practical_marks) && ($final_practical_marks <= $subjectMaxMarks)){ 
						if($final_practical_marks >= $subjectMinMarks && $final_practical_marks <= $subjectMaxMarks) {
							$isValid = true;
						}  else { 
							$isValid = false;
							$errors = 'Entered marks should be less than or equal to '.$subjectMaxMarks.' at sr. no '. ($k+$value);
						}
					}
				}
			}
		}
		
		$response['isValid'] = $isValid;
		$response['errors'] = $errors; 
		$response['validator'] = $validator;
		 //@dd($response); 
		return $response;
	}
	
	
	public function getPracticalExaminerList(){
		$current_exam_year_id = Config::get('global.admission_academicyear_id');
		$current_exam_month_id = Config::get('global.current_exam_month_id'); 
		$practicalexaminer = Config::get('global.practicalexaminer');
		$conditions = array();
		// $conditions['users.exam_year'] = $current_exam_year_id;
		// $conditions['users.exam_month'] = $current_exam_month_id;
		$conditions['model_has_roles.role_id'] = $practicalexaminer;
		$conditions['model_has_roles.model_type'] = 'App\Models\User';
		$user_list = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
					->where($conditions)
					->pluck('ssoid','id');	
		return $user_list;
	} 
	
	public function getDEOList(){
		$current_exam_year_id = Config::get('global.admission_academicyear_id');
		$current_exam_month_id = Config::get('global.current_exam_month_id'); 
		$deoRole = config("global.deo"); 
		$conditions = array();
		// $conditions['users.exam_year'] = $current_exam_year_id;
		// $conditions['users.exam_month'] = $current_exam_month_id;
		$conditions['model_has_roles.role_id'] = $deoRole;
		$conditions['model_has_roles.model_type'] = 'App\Models\User';
		$deo_user_list = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
					->where($conditions)->get(['ssoid','id','district_name'])->toArray();
		$list=array();
		foreach(@$deo_user_list as $k => $v){
			$list[$v['id']] = $v['ssoid'] . " - " . $v['district_name'];
		}
		$deo_user_list = collect($list);
		return $deo_user_list;  
		 
	}

	public function getDeoExaminerListOrCount($formId=null,$count=false){
		$current_exam_year_id = Config::get('global.admission_academicyear_id');
		$current_stream = Config::get('global.defaultStreamId');
		$practicalexaminer = Config::get('global.practicalexaminer');
		
		$selected_session = CustomHelper::_get_selected_sessions();
		$current_exam_month_id = Config::get('global.current_exam_month_id');
		
		
		$conditions = Session::get($formId. '_conditions');
		$auth_deo_id = Auth::user()->id;
		$custom_component_obj = new CustomComponent();
		$isAdminStatus = $custom_component_obj->_checkIsAdminRole();
		if($isAdminStatus==true){
			$auth_deo_id = null;
		}
		$conditions['user_practical_examiners.exam_year'] = $selected_session;
		$conditions['user_practical_examiners.exam_month'] = $current_exam_month_id;
		if(!empty($auth_deo_id) && $auth_deo_id!=null){
			$conditions['user_practical_examiners.user_deo_id'] = $auth_deo_id;
		}
		$result = "";
		if($count == true){
			$practicalexaminerrole = config("global.practicalexaminer");
			$role_id = Session::get('role_id');
			if($role_id != config("global.secrecy_admin")){
				$conditions["model_has_roles.role_id"] = $practicalexaminerrole;
			}
			
			$result = User::Join('model_has_roles', function($q){ $q->on('users.id', '=', 'model_has_roles.model_id');})
					->Join('user_practical_examiners','users.id','=','user_practical_examiners.user_id')
					->where('model_has_roles.model_type', '=', 'App\Models\User')
					->where($conditions)
					->whereNull('user_practical_examiners.deleted_at')
					->count();
			 
		} else {  
			$defaultPageLimit = config("global.defaultPageLimit");
			$result = User::Join('model_has_roles', function($q){$q->on('users.id', '=', 'model_has_roles.model_id');})
					->Join('user_practical_examiners','users.id','=','user_practical_examiners.user_id')
					->where('model_has_roles.model_type', '=', 'App\Models\User')
					->where($conditions)
					->whereNull('user_practical_examiners.deleted_at')
					->select(['users.*','user_practical_examiners.user_deo_id'])
					->paginate($defaultPageLimit);
		}
		// @dd($result);
		return $result; 
	}
	
	public function getDuplicateUser($user_id=null,$roll_id=null){ 
		$user_duplicate_status = false;
		$result = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_id',$user_id)->count();
		if($result > 0){
			$user_duplicate_status = true;
		}
		return $result;
	}
	
	public function getWithoutAjaxDeoListByDistrictId($district_id) {
		$selected_session = CustomHelper::_get_selected_sessions();
		$current_exam_month_id = Config::get('global.current_exam_month_id');
		$practicalexaminer = Config::get('global.practicalexaminer');
		$deoRole = config("global.deo");
		
		$conditions = array();
		// $conditions['users.exam_year'] = $selected_session;
		// $conditions['users.exam_month'] = $current_exam_month_id;
		$conditions['users.district_id'] = $district_id;
		$conditions['model_has_roles.role_id'] = $deoRole;
		// $conditions['model_has_roles.model_type'] = 'App\Models\User';
		$deo_user_list = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
					->where($conditions)
					->pluck('name','id');	
		return $deo_user_list; 
    }
	
	
	// public function getAlreadyExaminerMapping($user_practical_examiner_id,$examcenter_detail_id,$course,$subject_id){
	public function getAlreadyExaminerMapping($examcenter_detail_id,$course,$subject_id){
		$current_admission_session_id = Config::get("global.current_admission_session_id");
		$current_exam_month_id = Config::get("global.current_exam_month_id");
		$current_stream_id = Config::get("defaultStreamId");
			
		$status = false;
		$alreadyMappingExist = DB::table('user_examiner_maps')
		// ->where('user_examiner_maps.user_practical_examiner_id',$request->user_practical_examiner_id)
		->where('user_examiner_maps.examcenter_detail_id',$examcenter_detail_id)
		->where('user_examiner_maps.course',$course)
		->where('user_examiner_maps.subject_id',$subject_id)
		->where('user_examiner_maps.exam_year',$current_admission_session_id)
		->where('user_examiner_maps.exam_month',$current_exam_month_id)
		->whereNull('user_examiner_maps.deleted_at')
		->count();
		if(isset($alreadyMappingExist) && @$alreadyMappingExist > 0) {
			$status = true;
		}
		return $status;
    }
	
	public function getDeoDataById($deo_id){
		$deo_data = User::where('users.id',$deo_id)->get();
		return $deo_data;
	}


	public function getPracticalStudentsData($formId=null,$isPaginate=true){ 
		$conditions = Session::get($formId. '_conditions'); 
		$tempPracticalSubjects = Session::get('tempPracticalSubjects');

		$conditions['student_allotment_marks.is_exclude_for_practical'] = 0;
		$master = array();

		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = StudentAllotmentMark::Join('applications', 'applications.student_id', '=', 'student_allotment_marks.student_id')
			->Join('students', 'students.id', '=', 'student_allotment_marks.student_id')
			->where($conditions)
			->whereIn('subject_id',$tempPracticalSubjects)
			->paginate($defaultPageLimit,array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','applications.fee_paid_amount','student_allotment_marks.*'));
		} else {
			$master = StudentAllotmentMark::leftJoin('students','applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereIn('subject_id',$tempPracticalSubjects)
			->get(array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','applications.fee_paid_amount','student_allotment_marks.*'));
		}
		return $master; 
	}

	public function getPracticalExaminerMapping($formId=null,$isPaginate=true){ 
		$conditions = Session::get($formId. '_conditions'); 
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = UserExaminerMap::where($conditions)
			->paginate($defaultPageLimit,array('user_examiner_maps.*'));
		}else{
			$master = UserExaminerMap::where($conditions)
			->get(array('user_examiner_maps.*')); 
		}
		return $master; 
	}
	
	
	public function isAllowUnlockPracticalMarks(){   
		$role_id = Session::get('role_id');
		$status = false;
		
		// if(!empty($role_id) && ($role_id == 58 || $role_id == 71 || $role_id == 72 || $role_id == 70) ){ // allow DEO
		if(!empty($role_id) && ($role_id == 58 || $role_id == 71 || $role_id == 72) ){
			$status = true;
		} 
		return $status;
	}

	public function getPraticalSlotData($formId=null,$isPaginate=true){ 
		$conditions = Session::get($formId. '_conditions');
		
		$rawQueryDateTime = 1;
		$table_name = "student_practical_slots";$field_name = "date_time_start";
		if(@$conditions[$table_name.'.start_date']){	
	
			$rawQueryDateTime = CustomHelper::getStartAndEndDate(@$conditions[$table_name.'.start_date'],null,$table_name,$field_name);
			unset($conditions[$table_name.".start_date"]);
		}
		
		$rawQueryDateTime2 = 1;
		$field_name2 = "date_time_end";
		if(@$conditions[$table_name.'.end_date']){
			$rawQueryDateTime2 = CustomHelper::getStartAndEndDate(Null,@$conditions[$table_name.'.end_date'],$table_name,$field_name2);
			unset($conditions[$table_name.".end_date"]);
			
		}
		
		$custom_component_obj = new CustomComponent;
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		$condition2=['student_practical_slots.exam_year'=>$exam_year,'student_practical_slots.exam_month'=>$exam_month,
		'user_examiner_maps.exam_year'=>$exam_year,'user_examiner_maps.exam_month' =>$exam_month];

        $field=['student_practical_slots.id as id','student_practical_slots.examiner_mapping_id','users.name','users.ssoid','student_practical_slots.date_time_start','student_practical_slots.date_time_end',
		'student_practical_slots.batch_student_count','user_examiner_maps.examcenter_detail_id',
		'user_examiner_maps.course','user_examiner_maps.subject_id','student_practical_slots.entry_done','user_examiner_maps.user_practical_examiner_id','student_practical_slots.skip_slot'];
		
		$master = array();
		if($isPaginate){
			
			$defaultPageLimit = config("global.defaultPageLimit");
            $master=StudentPracticalSlots::join('user_examiner_maps','user_examiner_maps.id','=','student_practical_slots.examiner_mapping_id')
            ->join('users','users.id','=','user_examiner_maps.user_practical_examiner_id')
			->where($conditions)
			->whereRaw($rawQueryDateTime)
			->whereRaw($rawQueryDateTime2) 
			->where($condition2)
			->whereNull('student_practical_slots.deleted_at')
			->whereNull('users.deleted_at')
            ->paginate($defaultPageLimit,$field);
		}else{
            $master=StudentPracticalSlots::join('users','users.id','=','student_practical_slots.examiner_mapping_id')
            ->where($conditions)
			->where($condition2)
			->whereRaw($rawQueryDateTime)
			->whereRaw($rawQueryDateTime2)
			->whereNull('student_practical_slots.deleted_at')
			->whereNull('users.deleted_at')
            ->get($field);
			
		}
		  

		return $master; 
	}

    public static function getSlotAllotCount($user_examiner_id=null){
		$dataCount=0;
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
         $condition=$condition2=['student_practical_slots.exam_year'=>$exam_year,'student_practical_slots.exam_month'=>$exam_month,'examiner_mapping_id'=>$user_examiner_id,];
		 $dataCount=StudentPracticalSlots::where($condition)->count();
		return $dataCount;
		
	}

	public static function getCompleteSlot($user_examiner_id=null){
		$dataCount=0;
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
         $condition=$condition2=['student_practical_slots.exam_year'=>$exam_year,'student_practical_slots.exam_month'=>$exam_month,'examiner_mapping_id'=>$user_examiner_id,];
		 $dataCount=StudentPracticalSlots::where($condition)->where('entry_done','!=',1)->count();
		 
		return $dataCount;
		
	}
	
	public function getPracticalStudentListAllotingToSlot($examcenter_detail_id=null,$subject_id=null,$paginate=true,$formId=null){
		$conditions = array(); 
		$conditions = Session::get($formId. '_conditions');
		
		$selected_session = CustomHelper::_get_selected_sessions();
		$defaultPageLimit = config("global.defaultPageLimit");
		// $defaultPageLimit = 5;
		$current_exam_year_id = Config::get('global.admission_academicyear_id');
		$current_exam_month_id = Config::get('global.current_exam_month_id');
		$current_stream = Config::get('global.defaultStreamId');  
		
		$arraykeys=array_keys($conditions);
	
		if(in_array('slotid',$arraykeys)){
			$slot_id=$conditions['slotid'];
			unset($conditions['slotid']);
			
		}

		$conditions['student_allotment_marks.exam_year'] = $selected_session;
		$conditions['student_allotment_marks.exam_month'] = $current_exam_month_id;
		$conditions['student_allotment_marks.examcenter_detail_id'] = $examcenter_detail_id;
		$conditions['student_allotment_marks.subject_id'] = $subject_id;
		$conditions['student_allotment_marks.is_exclude_for_practical'] = 0;
		
		
		
		if($paginate){
			$result = StudentAllotmentMark::Join("students", "students.id","=", "student_allotment_marks.student_id")->Join("student_allotments", "student_allotments.id","=", "student_allotment_marks.student_allotment_id")
			->where($conditions)
			->whereIn('student_practical_slot_id',$slot_id)
			->whereNull('student_allotments.deleted_at')
			->orderBy('student_allotment_marks.enrollment')
			->select(['student_allotment_marks.id','student_allotment_marks.student_practical_slot_id','student_allotment_marks.enrollment','student_allotment_marks.practical_absent','student_allotment_marks.final_practical_marks','students.name','student_allotment_marks.exam_year','student_allotment_marks.exam_month','student_allotment_marks.is_practical_lock_submit'])->paginate($defaultPageLimit);
		} else {
			
			$result = StudentAllotmentMark::Join("students", "students.id","=", "student_allotment_marks.student_id")->Join("student_allotments", "student_allotments.id","=", "student_allotment_marks.student_allotment_id")
			->where($conditions)
			->whereIn('student_practical_slot_id',$slot_id)
			->whereNull('student_allotments.deleted_at')
			->orderBy('student_allotment_marks.enrollment')
			->select(['student_allotment_marks.id','student_allotment_marks.student_practical_slot_id','student_allotment_marks.enrollment','student_allotment_marks.practical_absent','student_allotment_marks.final_practical_marks','students.name','student_allotment_marks.exam_year','student_allotment_marks.exam_month','student_allotment_marks.is_practical_lock_submit'])->get();
		}
		
		return $result; 
	}


	public  static function getPracticalStudentListPendingCount($examcenter_detail_id=null,$subject_id=null){
		$selected_session = CustomHelper::_get_selected_sessions();
		$defaultPageLimit = config("global.defaultPageLimit");

		$current_exam_year_id = Config::get('global.admission_academicyear_id');
		$current_exam_month_id = Config::get('global.current_exam_month_id');
	    $conditions = array(); 
		$conditions['student_allotment_marks.exam_year'] = $selected_session;
		$conditions['student_allotment_marks.exam_month'] = $current_exam_month_id;
		$conditions['student_allotment_marks.examcenter_detail_id'] = $examcenter_detail_id;
		$conditions['student_allotment_marks.subject_id'] = $subject_id;
		$conditions['student_allotment_marks.is_exclude_for_practical'] = 0;
		//$conditions['student_allotment_marks.student_practical_slot_id'] = 0;

		
		
			$result = StudentAllotmentMark::Join("students", "students.id","=", "student_allotment_marks.student_id")->Join("student_allotments", "student_allotments.id","=", "student_allotment_marks.student_allotment_id")
			->where($conditions)
			->whereNull('student_allotments.deleted_at')
			->whereNull('student_allotment_marks.student_practical_slot_id')
			->orderBy('student_allotment_marks.enrollment')
			->select(['student_allotment_marks.id','student_allotment_marks.enrollment','student_allotment_marks.student_practical_slot_id','student_allotment_marks.practical_absent','student_allotment_marks.final_practical_marks','students.name'])->get();
		
		return $result; 
	}
	
	public function _checkPraticalAllowOrNot(){ 
		$objController = new Controller();
		$combo_name = 'practical_marks_submission_start_date';
		$combo_name2 = 'practical_marks_submission_end_date';
		$sessional_start_date_arr = $objController->master_details($combo_name); 
		$sessional_start_end_arr = $objController->master_details($combo_name2); 
		if(strtotime(date("Y-m-d H:i:s")) >= strtotime($sessional_start_date_arr[1]) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime($sessional_start_end_arr[1])){ 
			$isValid = true;
		}else{
			$isValid = false;
		}
		return $isValid;
	}


    public function allowOldPraticalMarks(){
        $objController = new Controller();
		$combo_name = "allow_old_pratical_marks";
		$allow_old_pratical_marks = $objController->master_details($combo_name); 
        return $allow_old_pratical_marks['1'];
}

	public  static function getPracticalStudentNotSubmitByExaminer($examcenter_detail_id=null,$subject_id=null){
		$selected_session = CustomHelper::_get_selected_sessions();
		$defaultPageLimit = config("global.defaultPageLimit");

		$current_exam_year_id = Config::get('global.admission_academicyear_id');
		$current_exam_month_id = Config::get('global.current_exam_month_id');
	    $conditions = array(); 
		$conditions['student_allotment_marks.exam_year'] = $selected_session;
		$conditions['student_allotment_marks.exam_month'] = $current_exam_month_id;
		$conditions['student_allotment_marks.examcenter_detail_id'] = $examcenter_detail_id;
		$conditions['student_allotment_marks.subject_id'] = $subject_id;
		$conditions['student_allotment_marks.is_exclude_for_practical'] = 0;
		//$conditions['student_allotment_marks.student_practical_slot_id'] = 0;

		
		
			$result = StudentAllotmentMark::Join("students", "students.id","=", "student_allotment_marks.student_id")->Join("student_allotments", "student_allotments.id","=", "student_allotment_marks.student_allotment_id")
			->where($conditions)
			->whereNull('student_allotments.deleted_at')
			->where('is_update_practical_marks_practical_examiner',0)
			//->whereNull('student_allotment_marks.student_practical_slot_id')
			->orderBy('student_allotment_marks.enrollment')
			->select(['student_allotment_marks.id','student_allotment_marks.enrollment','student_allotment_marks.student_practical_slot_id','student_allotment_marks.practical_absent','student_allotment_marks.final_practical_marks','students.name'])->count();
		
		return $result; 
	}

	
}


