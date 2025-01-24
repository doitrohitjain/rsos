<?php 
namespace App\Component;
use App\Http\Controllers\Controller;
use App\Models\AllotingCopiesExaminer;
use App\Models\MarkingAbsentStudent;
use App\Models\StudentAllotmentMark;
use App\Helper\CustomHelper; 
use App\models\Student;
use App\models\ExamSubject;
use App\models\supplementary;
use App\models\StudentAllotment;
use App\Component\CustomComponent;
use App\models\Subject;
use App\models\User;
use App\models\Logs;
use Carbon\Carbon;
use Validator; 
use Session;
use Config;
use Cache;
use Auth;
use DB;


class ThoeryCustomComponent {

    public function getMarkingAbsentStudent($course_id,$examcenter_detail_id,$subject_id){
        $final_result = array();
        $conditions = array();
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		$condition2=['marking_absent_students.exam_year'=>$exam_year,'marking_absent_students.exam_session'=>$exam_month];
        $conditions['marking_absent_students.course_id'] = $course_id;
        $conditions['marking_absent_students.examcenter_detail_id'] = $examcenter_detail_id;
        $conditions['marking_absent_students.subject_id'] = $subject_id;
        $field=['id','total_students_appearing','total_copies_of_subject','total_absent','total_nr'];
        $result=DB::table('marking_absent_students')->where($conditions)->where($condition2)->whereNull('deleted_at')->first($field);
        if(isset($result)&& !empty($result)){ 
            $final_result = $result; 
            return $final_result; 
        }
    }

    public function getAllotingExaminerList($formId=null,$isPaginate=true){ 
		$conditions = Session::get($formId. '_conditions');
		$custom_component_obj = new CustomComponent;
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		$condition2=['marking_absent_students.exam_year'=>$exam_year,'marking_absent_students.exam_session'=>$exam_month];


        $field=['alloting_copies_examiners.id','marking_absent_students.id as markingabsentid','alloting_copies_examiners.allotment_date','alloting_copies_examiners.theory_lastpage_submitted_date','alloting_copies_examiners.marks_entry_completed','users.name','users.ssoid','users.mobile',
        'marking_absent_students.examcenter_detail_id','marking_absent_students.subject_name',
        'marking_absent_students.total_students_appearing','marking_absent_students.total_copies_of_subject',
        'marking_absent_students.total_absent','marking_absent_students.total_nr','marking_absent_students.course_id','marking_absent_students.subject_id'
		];
		
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
            $master=AllotingCopiesExaminer::join('marking_absent_students','marking_absent_students.id','=','alloting_copies_examiners.marking_absent_student_id')
            ->join('users','users.id','=','alloting_copies_examiners.user_id')->join('examcenter_details','examcenter_details.id','=','marking_absent_students.examcenter_detail_id')  
            ->where($conditions)
			->where($condition2)
			->whereNull('alloting_copies_examiners.deleted_at')
			->whereNull('marking_absent_students.deleted_at')
            ->orderBy('examcenter_details.fixcode','ASC')->paginate($defaultPageLimit,$field);
		}else{
            $master=AllotingCopiesExaminer::join('marking_absent_students','marking_absent_students.id','=','alloting_copies_examiners.marking_absent_student_id')
            ->join('users','users.id','=','alloting_copies_examiners.user_id')->join('examcenter_details','examcenter_details.id','=','marking_absent_students.examcenter_detail_id') 
            ->where($conditions)
			->where($condition2)
			->whereNull('alloting_copies_examiners.deleted_at')
			->whereNull('marking_absent_students.deleted_at')
			->orderBy('examcenter_details.fixcode','ASC')->get($field);
			
		}
		  
		

		return $master; 
	}



	public function getTheoryStudentsData($formId=null,$isPaginate=true){ 
		$conditions = Session::get($formId. '_conditions'); 
		// dd($conditions);
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		
		$conditions1=['student_allotments.exam_year'=>$exam_year,'student_allotments.exam_month'=>$exam_month,
		'student_allotment_marks.exam_year'=>$exam_year,'student_allotment_marks.exam_month'=>$exam_month];
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = StudentAllotmentMark::Join('applications', 'applications.student_id', '=', 'student_allotment_marks.student_id')
			->Join('student_allotments', 'student_allotments.id', '=', 'student_allotment_marks.student_allotment_id')
			->Join('students', 'students.id', '=', 'student_allotment_marks.student_id')->where($conditions)
			->where($conditions1)
			->whereNull('student_allotments.deleted_at')
			->paginate($defaultPageLimit,array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','applications.fee_paid_amount','student_allotment_marks.*'));
			// dd($master);
		}else{
			$master = StudentAllotmentMark::leftJoin('students','applications', 'applications.student_id', '=', 'students.id')
			->where($conditions)
			->where($conditions1)
			->whereNull('student_allotments.deleted_at')
			->get(array('students.id','students.name','students.gender_id','students.enrollment','students.adm_type','students.stream','students.course','students.ai_code','applications.medium','applications.locksumbitted','students.challan_tid','students.submitted','applications.fee_paid_amount','student_allotment_marks.*'));
		}
		return $master; 
	}



	 public function getAllotingExaminerListreports($formId=null,$isPaginate=true){ 
		$conditions = Session::get($formId. '_conditions');
		$custom_component_obj = new CustomComponent;
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		$conditions1=['marking_absent_students.exam_year'=>$exam_year,'marking_absent_students.exam_session'=>$exam_month,
	    
	   ];
        $field=['alloting_copies_examiners.id','marking_absent_students.id as markingabsentid','alloting_copies_examiners.allotment_date','alloting_copies_examiners.theory_lastpage_submitted_date','alloting_copies_examiners.marks_entry_completed','users.name','users.ssoid','users.mobile',
        'marking_absent_students.examcenter_detail_id','marking_absent_students.subject_name','alloting_copies_examiners.marks_entry_completed',
        'marking_absent_students.total_students_appearing','marking_absent_students.total_copies_of_subject',
        'marking_absent_students.total_absent','marking_absent_students.total_nr','marking_absent_students.course_id','marking_absent_students.subject_id'];
		
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
            $master=AllotingCopiesExaminer::join('marking_absent_students','marking_absent_students.id','=','alloting_copies_examiners.marking_absent_student_id')
            ->join('users','users.id','=','alloting_copies_examiners.user_id') 
            ->where($conditions)
			->where($conditions1)
			->wherenull('marking_absent_students.deleted_at')
			->wherenull('alloting_copies_examiners.deleted_at')
            ->paginate($defaultPageLimit,$field);
		}else{
            $master=AllotingCopiesExaminer::join('marking_absent_students','marking_absent_students.id','=','alloting_copies_examiners.marking_absent_student_id')
            ->join('users','users.id','=','alloting_copies_examiners.user_id') 
            ->where($conditions)
			->where($conditions1)
			->wherenull('marking_absent_students.deleted_at')
			->wherenull('alloting_copies_examiners.deleted_at')
			->get($field);
		}

		return $master; 
	}
    
    public function getMarkingAbsentStudentList($formId=null,$isPaginate=true){ 
		$conditions = Session::get($formId. '_conditions');
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		$condition2=['exam_year'=>$exam_year,'exam_session'=>$exam_month];
       $master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
            $master = MarkingAbsentStudent::where($conditions)->where($condition2)->whereNull('deleted_at')->orderBy('examcenter_detail_id','desc')->paginate($defaultPageLimit);
		} else {
            $master = MarkingAbsentStudent::where($conditions)->where($condition2)->whereNull('deleted_at')->orderBy('examcenter_detail_id','desc')->get();
		}
		return $master; 
	}
    
    public function getTheoryMaxMarks($subject_id = null){ 
		$conditions = ['id' => $subject_id];
		$mainTable = "subjects"; 
		$cacheName = $mainTable. "_theory_".$subject_id;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) { 
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($conditions, $mainTable) { 
				$result = DB::table($mainTable)->where($conditions)->whereNull('deleted_at')->first('theory_max_marks'); 
				return $result;
			});			
		}  
		return $result;
	} 
    public function getTheoryMinMarks($subject_id = null){ 
		$conditions = ['id' => $subject_id];
		$mainTable = "subjects"; 
		$cacheName = $mainTable. "_theory_min_".$subject_id;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) { 
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($conditions, $mainTable) { 
				$result = DB::table($mainTable)->where($conditions)->whereNull('deleted_at')->first('theory_min_marks'); 
				return $result;
			});			
		}  
		return $result;
	} 
	
	
    static public function getTheoryAbsentStudent($course_id=null,$subject_id=null,$examcenter_detail_id=null,$fixcode=null){ 
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
        $conditions=['exam_year'=>$exam_year,'exam_month'=>$exam_month,'course'=>$course_id,'subject_id'=>$subject_id,'examcenter_detail_id'=>$examcenter_detail_id,'fixcode'=>$fixcode];
        $data=StudentAllotmentMark::where($conditions)
		->whereNull('deleted_at')
		->orderBy('id','desc')->first('theory_absent');
        return $data;

	}
	
	/*public function get_appearing_student_list_count($course_id=null,$subject_id=null,$examcenter_detail_id=null){
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		$coditions=['student_allotment_marks.student_allotment_id' =>'student_allotments.id'];
		$conditions2=['student_allotment_marks.examcenter_detail_id'=>$examcenter_detail_id,
			'student_allotment_marks.course'=>$course_id,'student_allotment_marks.subject_id'=>$subject_id,
			'student_allotments.exam_year'=>$exam_year,
			'student_allotments.exam_month'=>$exam_month,
			'student_allotment_marks.exam_year'=>$exam_year,
			'student_allotment_marks.exam_month'=>$exam_month,
			'student_allotment_marks.is_exclude_for_theory'=>0,
			'student_allotments.exam_year'=>$exam_year,
		    'student_allotments.exam_month'=>$exam_month,
		];
		
		$result=StudentAllotmentMark::join('student_allotments',$coditions)
			->where($conditions2)->whereNUll('student_allotments.deleted_at')
			->whereNull('student_allotment_marks.deleted_at')
			->whereNotNull('student_allotments.fixcode')
			->count();
			
		return $result;
	}*/
	
	
	public function get_appearing_student_listing($course_id=null,$subject_id=null,$examcenter_detail_id=null){
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		$coditions=['student_allotment_marks.student_allotment_id' =>'student_allotments.id'];
		$conditions2=['student_allotment_marks.examcenter_detail_id'=>$examcenter_detail_id,
			'student_allotment_marks.course'=>$course_id,'student_allotment_marks.subject_id'=>$subject_id,
			'student_allotment_marks.exam_year'=>$exam_year,
			'student_allotment_marks.exam_month'=>$exam_month,
			'student_allotments.exam_year'=>$exam_year,
			'student_allotments.exam_month'=>$exam_month,
			'student_allotment_marks.is_exclude_for_theory'=>0,
		];
		
		$result=StudentAllotmentMark::join('student_allotments',$coditions)
			->where($conditions2)->whereNUll('student_allotments.deleted_at')
			->whereNUll('student_allotment_marks.deleted_at')
			->whereNotNull('student_allotments.fixcode')
			->whereNotNull('student_allotment_marks.fixcode')
			->orderBy('student_allotments.enrollment')
			->get(array('student_allotments.fixcode','student_allotment_marks.theory_absent','student_allotments.enrollment'));
		return $result; 
	}

	public function examiner_list($formId=null,$isPaginate=true){ 
		$conditions = Session::get($formId. '_conditions');
		/*  $exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');*/
		$master = array();
		// $conditions2=['users.exam_year'=>$exam_year,'users.exam_month'=>$exam_month];
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = User::join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')->where('model_has_roles.role_id','=','62')->where($conditions)
			// ->where($conditions2)
			->paginate($defaultPageLimit);
		
		}else{
		$master =  User::join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')->where('model_has_roles.role_id','=','62')->where($conditions)
		// ->where($conditions2)
		->get();
		}
		return $master; 
	}
	
	public static function _getSubjectDetail($subid=null){		
		$list = Subject::where('id','=',$subid)->whereNull('deleted_at')->first();
		
		return $list;
	}

	public static function getAbsentStudent($examcenter_detail_id=null,$course=null,$subject_id=null,$fixcode=null){
		
		$coditions=['examcenter_detail_id'=>$examcenter_detail_id,'course'=>$course,'fixcode'=>$fixcode,
		             'subject_id'=>$subject_id];
		$data=StudentAllotmentMark::where($coditions)->orderBy('id','desc')->first('theory_absent');
		// print_r($data);
		return $data;
	}

	public function getTheoryAllotmentMarks($examcenter_detail_id=null,$course_id=Null,$subject_id=Null){
		$defaultPageLimit = config("global.defaultPageLimit");
		$conditions=['student_allotment_marks.examcenter_detail_id'=>@$examcenter_detail_id,
		'student_allotment_marks.course'=>@$course_id,
			'student_allotment_marks.exam_month'=> Config::get('global.current_exam_month_id'),
			'student_allotment_marks.exam_year'=>Config::get('global.current_admission_session_id'),
			'student_allotment_marks.subject_id'=>@$subject_id,
			'student_allotment_marks.is_exclude_for_theory'=>0,
			'student_allotments.exam_month'=> Config::get('global.current_exam_month_id'),
			'student_allotments.exam_year'=>Config::get('global.current_admission_session_id'),
		];
		$studentdata = StudentAllotmentMark::Join('student_allotments', 'student_allotments.id', '=', 'student_allotment_marks.student_allotment_id')->select('student_allotment_marks.*')->where($conditions)
		->whereNotNull('student_allotments.fixcode')
		->whereNotNull('student_allotment_marks.fixcode')
		->whereNull('student_allotments.deleted_at')
		->orderBy('student_allotment_marks.enrollment')
		 ->paginate($defaultPageLimit);
		return $studentdata;
	}
	
	public function getExminerdetails($id){
		$condtions=['alloting_copies_examiners.marking_absent_student_id'=>$id];
		$field=['users.name','users.ssoid','users.id'];
		$userdata=AllotingCopiesExaminer::join('users','users.id','=','alloting_copies_examiners.user_id')->where($condtions)->whereNull('alloting_copies_examiners.deleted_at') 
		->whereNull('users.deleted_at')->first($field);
		return $userdata;

	}

	public function isvalidtheorymarks($input=null,$maxmarks=null,$minmarks=null){
		$isValid = true; 
		$errors = null; 
		$validator = Validator::make([], []);
		$errMsg = '';
		$response = array();
		
		if($isValid==true && isset($input['data'])){ 
			foreach($input['data'] as $k => $data){
				// dd($data);
				// $final_theory_marks =intval($data['final_theory_marks']);
				$final_theory_marks =@$data['final_theory_marks'];
				$theory_absent =@$data['theory_absent'];
				
				if(isset($data['theory_absent']) && ($data['theory_absent'] == 'on' || $data['theory_absent'] == '1') && isset($final_theory_marks) 
					&& (!empty($final_theory_marks) || $final_theory_marks != 'null') ){
					$fld = 'final_theory_marks';
					$errMsg = "You can't enter absent and marks together at sr. no ". ($k+1);
					$errors = $errMsg;
					$validator->getMessageBag()->add($fld, $errMsg); 
					$isValid = false;
				}
				// dd($theory_absent);
				if(isset($data['theory_absent_nr']) && ($data['theory_absent_nr'] == 'on' || $data['theory_absent_nr'] == '1') && isset($final_theory_marks) 
					&& (!empty($final_theory_marks) || $final_theory_marks != 'null') ){
					$fld = 'theory_absent_nr';
					$errMsg = "You can't enter NR and marks together at sr. no ". ($k+1);
					$errors = $errMsg;
					$validator->getMessageBag()->add($fld, $errMsg); 
					$isValid = false;
				}
				//|| $data['theory_absent'] != '1'
				if( (isset($data['theory_absent']) && ($data['theory_absent'] != 'on')) 
					&& 
					(isset($data['theory_absent_nr']) && ($data['theory_absent_nr'] != 'on' || $data['theory_absent_nr'] != '1')) ){
						
						if($isValid==true && (is_null($final_theory_marks) || $final_theory_marks=='')){
							$fld = 'final_theory_marks';
							$errMsg = 'Please enter valid marks into student at sr. no '. ($k+1);
							$errors = $errMsg;
							$validator->getMessageBag()->add($fld, $errMsg); 
							$isValid = false;
						}
						
						if($isValid==true && !is_numeric($final_theory_marks)) {
							
							$fld = 'final_theory_marks';
							$errMsg = 'Entered marks should be numeric into sr. no '. ($k+1);
							$errors = $errMsg;
							$validator->getMessageBag()->add($fld, $errMsg); 
							$isValid = false;
						}
					
						if($isValid==true){
							if(($final_theory_marks >= $minmarks) && ($final_theory_marks <= $maxmarks)){ 
								$isValid = true;
							} else { 
								$isValid = false;
								$errors = 'Entered marks should be less than or equal to ('.$maxmarks.') and more than or equal to ('.$minmarks.') at sr. no '. ($k+1);
								// $errors = 'Entered marks should be less than or equal to ('.$maxmarks.') and more than or equal to ('.$minmarks.').';
							}
						}
					}
				}
		}
		
		
		$response['isValid'] = $isValid;
		$response['errors'] = $errors; 
		$response['validator'] = $validator;
		// @dd($response); 
		
		return $response;
	}
	
	public function getTheoryMarks($examcenter_detail_id=null,$course_id=Null,$subject_id=Null){
		$defaultPageLimit = config("global.defaultPageLimit");
		$conditions=['student_allotment_marks.examcenter_detail_id'=>@$examcenter_detail_id,
						'student_allotment_marks.course'=>@$course_id,
		                'student_allotment_marks.exam_month'=> Config::get('global.current_exam_month_id'),
						'student_allotment_marks.exam_year'=>Config::get('global.current_admission_session_id'),
				        'student_allotment_marks.subject_id'=>@$subject_id,
						'student_allotment_marks.is_exclude_for_theory'=>0,
						'student_allotments.exam_month'=> Config::get('global.current_exam_month_id'),
			            'student_allotments.exam_year'=>Config::get('global.current_admission_session_id'),
				    ];
		// $studentdata = StudentAllotmentMark::where($conditions)->get();
		$studentdata = StudentAllotmentMark::Join('student_allotments', 'student_allotments.id', '=', 'student_allotment_marks.student_allotment_id')->select('student_allotment_marks.*')->where($conditions)
		->whereNotNull('student_allotments.fixcode')
		->whereNotNull('student_allotment_marks.fixcode')
		->whereNull('student_allotments.deleted_at')
		->orderBy('student_allotment_marks.enrollment')
		->get();
		return $studentdata;
	}
	
	public function getTheoryMarkYetNotFilled($examcenter_detail_id=null,$course_id=Null,$subject_id=Null){
		$defaultPageLimit = config("global.defaultPageLimit");
		$conditions=['student_allotment_marks.examcenter_detail_id'=>@$examcenter_detail_id,
				'student_allotment_marks.course'=>@$course_id,
				'student_allotment_marks.exam_month'=> Config::get('global.current_exam_month_id'),
				'student_allotment_marks.exam_year'=>Config::get('global.current_admission_session_id'),
				'student_allotment_marks.subject_id'=>@$subject_id,
				'student_allotment_marks.is_exclude_for_theory'=>0,
				'student_allotments.exam_month'=> Config::get('global.current_exam_month_id'),
			     'student_allotments.exam_year'=>Config::get('global.current_admission_session_id'),
			];
		// $studentdata = StudentAllotmentMark::where($conditions)->whereNull('final_theory_marks')->count();
		$studentdata = StudentAllotmentMark::Join('student_allotments', 'student_allotments.id', '=', 'student_allotment_marks.student_allotment_id')->where($conditions)->whereNull('student_allotments.deleted_at')
		->whereNotNull('student_allotments.fixcode')
		->whereNotNull('student_allotment_marks.fixcode')
		->whereNull('student_allotment_marks.final_theory_marks')->count();
		return $studentdata;
	}
	
	


	public function checkExaminerType($ssoid=null){
		// $conditions=['ssoid'=>$ssoid,'exam_year'=>Config::get('global.current_admission_session_id'),'exam_month'=>Config::get('global.current_exam_month_id')];
		$conditions=['ssoid'=>$ssoid];
		$examinerData = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
		->where($conditions)
		->whereNull('users.Deleted_at')
		->where('model_has_roles.role_id','=',Config::get('global.theoryexaminer'))
		->first(); 
		return $examinerData;
	}

	public function getDatatomaster($combo_name=null,$option_id=null){
		$conditions1=['combo_name'=>$combo_name,'option_id'=>$option_id];
		$data=DB::table('masters')->where($conditions1)->pluck('option_val','option_id');
		return $data;
	}

	public function checkUserEdit($ssoid=null,$userid=null,$exam_year=null,$exam_month=null){
		$userCount = User::where('ssoid', '=',$ssoid)
					->where('id', '!=',$userid)
					->where('exam_year', '=', $exam_year)
					->where('exam_month', '=', $exam_month)
					->count();
		return 	$userCount;		
	}


	public function checkMarkingAbsent($examcenterid=null,$course_id=null,$subject_id=null,$exam_year=null,$exam_month=null){
		$conditions=['examcenter_detail_id'=>$examcenterid,'course_id'=>$course_id,'subject_id'=>$subject_id,'exam_year'=>$exam_year,'exam_session'=>$exam_month];
		$allreadyExistData=MarkingAbsentStudent::where($conditions)->whereNull('deleted_at')->first();
	    return $allreadyExistData;		
    }

	public function get_appearing_student_listing2($course_id=null,$subject_id=null,$examcenter_detail_id=null){
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		$coditions=['student_allotment_marks.student_allotment_id' =>'student_allotments.id'];
		$conditions2=['student_allotment_marks.examcenter_detail_id'=>$examcenter_detail_id,
		'student_allotment_marks.course'=>$course_id,'student_allotment_marks.subject_id'=>$subject_id,
		'student_allotments.exam_year'=>$exam_year,
		'student_allotments.exam_month'=>$exam_month,
		'student_allotment_marks.exam_year'=>$exam_year,
		'student_allotment_marks.exam_month'=>$exam_month,
		'student_allotment_marks.is_exclude_for_theory'=>0,
		];
		
		$result=StudentAllotmentMark::join('student_allotments',$coditions)
		->where($conditions2)->whereNUll('student_allotments.deleted_at')
		->whereNotNull('student_allotments.fixcode')
		->whereNotNull('student_allotment_marks.fixcode')
		->whereNUll('student_allotment_marks.deleted_at')
		->get('student_allotments.fixcode');
		return $result; 
	}



	public function checkAllotingExaminer($marking_absent_student_id=null,$user_id=null){

		$alredy_exist_data_condtions = array();
		$alredy_exist_data_condtions['alloting_copies_examiners.marking_absent_student_id'] = $marking_absent_student_id;
		$alredy_exist_data_condtions['alloting_copies_examiners.user_id'] = $user_id;
		$alredy_exist_data_condtions['alloting_copies_examiners.is_changed'] = 0;
		$alredy_exist_data=AllotingCopiesExaminer::where($alredy_exist_data_condtions)->whereNull('deleted_at')->first();
	    return $alredy_exist_data; 
	}

	public function allotinsubjectcopies($formId=null,$isPaginate=true){ 
		$conditions = Session::get($formId. '_conditions');
		$custom_component_obj = new CustomComponent();
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		$conditions2=['marking_absent_students.exam_year'=>$exam_year,'marking_absent_students.exam_session'=>$exam_month];
		$isAdminStatus = $custom_component_obj->_checkIsAdminRole();
		$auth_user_id = @Auth::user()->id;
		if($isAdminStatus!=true){
			$conditions['users.id'] = $auth_user_id;
		} 
		
        $field=['alloting_copies_examiners.id as allotid','marking_absent_students.id','alloting_copies_examiners.allotment_date','alloting_copies_examiners.theory_lastpage_submitted_date','alloting_copies_examiners.marks_entry_completed','users.name','users.ssoid','users.mobile',
        'marking_absent_students.examcenter_detail_id','marking_absent_students.subject_name',
        'marking_absent_students.total_students_appearing','marking_absent_students.total_copies_of_subject',
        'marking_absent_students.total_absent','marking_absent_students.total_nr','marking_absent_students.course_id','marking_absent_students.subject_id'];
		
		$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
            $master=AllotingCopiesExaminer::join('marking_absent_students','marking_absent_students.id','=','alloting_copies_examiners.marking_absent_student_id')
            ->join('users','users.id','=','alloting_copies_examiners.user_id') 
            ->where($conditions)
			->where($conditions2)
			->whereNull('alloting_copies_examiners.deleted_at')
			->whereNull('marking_absent_students.deleted_at')
            ->paginate($defaultPageLimit,$field);
		}else{
            $master=AllotingCopiesExaminer::join('marking_absent_students','marking_absent_students.id','=','alloting_copies_examiners.marking_absent_student_id')
            ->join('users','users.id','=','alloting_copies_examiners.user_id') 
            ->where($conditions)
			->where($conditions2)
			->whereNull('alloting_copies_examiners.deleted_at')
			->whereNull('marking_absent_students.deleted_at')
			->get($field);
		}
		return $master; 
	}
     
	 public function getAbsentAndNrstudent($course_id=null,$subject_id=null,$examcenter_detail_id=null){
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		$conditions2=['exam_year'=>$exam_year,'exam_month'=>$exam_month];
		$coditions=['examcenter_detail_id'=>$examcenter_detail_id,'course'=>$course_id,'subject_id'=>$subject_id,'student_allotment_marks.is_exclude_for_theory'=>0];
		$data=StudentAllotmentMark::where($coditions)->where($conditions2)
		->whereIn('theory_absent', ['1', '2'])
		->whereNull('student_allotment_marks.deleted_at')
		->whereNotNull('student_allotment_marks.fixcode')->orderBy('id','desc')->pluck('fixcode','fixcode');
		return $data;

	 }
	 
	 public function getTheoryAbsentCount($course_id=null,$subject_id=null,$examcenter_detail_id=null){
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		$conditions2=['exam_year'=>$exam_year,'exam_month'=>$exam_month];
		$coditions=['examcenter_detail_id'=>$examcenter_detail_id,'course'=>$course_id,'subject_id'=>$subject_id,'student_allotment_marks.is_exclude_for_theory'=>0];
		$data=StudentAllotmentMark::where($coditions)->where($conditions2)
		->whereNull('student_allotment_marks.deleted_at')
		->where('theory_absent',1)->whereNotNull('student_allotment_marks.fixcode')->count();
		return $data;
	}
	 
	 public function getTheoryNrCount($course_id=null,$subject_id=null,$examcenter_detail_id=null){
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		$conditions2=['exam_year'=>$exam_year,'exam_month'=>$exam_month];
		$coditions=['examcenter_detail_id'=>$examcenter_detail_id,'course'=>$course_id,'subject_id'=>$subject_id,'student_allotment_marks.is_exclude_for_theory'=>0];
		$data=StudentAllotmentMark::where($coditions)->where($conditions2)
		->whereNull('student_allotment_marks.deleted_at')
		->where('theory_absent',2)->whereNotNull('student_allotment_marks.fixcode')->count();
		return $data;
	}

	 
	 public function checkAllotingMarkingAbsent($marking_absent_student_id=null){
		$alredy_exist_data_condtions = array();
		$alredy_exist_data_condtions['alloting_copies_examiners.marking_absent_student_id'] = $marking_absent_student_id;
		$alredy_exist_data_condtions['alloting_copies_examiners.is_changed'] = 0;
		$alredy_exist_data=AllotingCopiesExaminer::where($alredy_exist_data_condtions)->whereNull('deleted_at')->first();
	    return $alredy_exist_data; 
	}


	public function getAllotData($id=null){
        // $condition=['']
		// dd($id);

		$field=['alloting_copies_examiners.id','marking_absent_students.id','users.id as usersid','alloting_copies_examiners.allotment_date','alloting_copies_examiners.theory_lastpage_submitted_date','alloting_copies_examiners.marks_entry_completed','users.name','users.ssoid','users.mobile',
        'marking_absent_students.examcenter_detail_id','marking_absent_students.subject_name',
        'marking_absent_students.total_students_appearing','marking_absent_students.total_copies_of_subject',
        'marking_absent_students.total_absent','marking_absent_students.total_nr','marking_absent_students.course_id','marking_absent_students.subject_id'];
		

		$data=AllotingCopiesExaminer::join('marking_absent_students','marking_absent_students.id','=','alloting_copies_examiners.marking_absent_student_id')
		->join('users','users.id','=','alloting_copies_examiners.user_id')->where('alloting_copies_examiners.id','=',$id)
		->whereNull('alloting_copies_examiners.deleted_at')->whereNull('marking_absent_students.deleted_at')
		->orderby('alloting_copies_examiners.id','desc')->first($field); 
		return $data;

	}
    
	public function getAllotDataAccordingUserId($id=null,$course_id=null,$subject_id=null,$examcenter_id=null){
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		$coditions=['student_allotment_marks.student_allotment_id' =>'student_allotments.id'];
		$conditions2=['student_allotment_marks.examcenter_detail_id'=>$examcenter_id,
		'student_allotment_marks.course'=>$course_id,'student_allotment_marks.subject_id'=>$subject_id,
		'student_allotments.exam_year'=>$exam_year,
		'student_allotments.exam_month'=>$exam_month,
		'student_allotment_marks.exam_year'=>$exam_year,
		'student_allotment_marks.exam_month'=>$exam_month,
		'student_allotment_marks.is_exclude_for_theory'=>0,
		'student_allotment_marks.theory_examiner_id'=>$id,
		'student_allotment_marks.theory_lock_submit_user_id'=>$id,
		];
		
		$result=StudentAllotmentMark::join('student_allotments',$coditions)
		->where($conditions2)->whereNUll('student_allotments.deleted_at')->whereNotNull('student_allotments.fixcode')
		->whereNUll('student_allotment_marks.deleted_at')
		->get(['student_allotment_marks.id','student_allotment_marks.fixcode','student_allotment_marks.final_theory_marks','student_allotment_marks.is_theory_lock_submit','student_allotment_marks.theory_examiner_id','student_allotment_marks.theory_lock_submit_user_id']);
		return $result; 
	}

	public function getTheoryExaminer(){
		// $exam_year=CustomHelper::_get_selected_sessions();
		// $exam_month=Config::get('global.current_exam_month_id');
		$master = array();
		// $conditions2=['users.exam_year'=>$exam_year,'users.exam_month'=>$exam_month];
			$master = User::join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')->where('model_has_roles.role_id','=','62')
			// ->where($conditions2)
			->pluck('users.ssoid','users.ssoid');
			return $master;
		
	}


	public function getallotssoid($exam_center_id=null,$course_id=null,$subject_id=null){
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
		$conditions=['users.exam_year'=>$exam_year,
		'users.exam_month'=>$exam_month,
		'marking_absent_students.exam_year'=>$exam_year,
		'marking_absent_students.exam_session'=>$exam_month,
		'marking_absent_students.examcenter_detail_id'=>$exam_center_id,
		'marking_absent_students.course_id'=>$course_id,
		'marking_absent_students.subject_id'=>$subject_id];
		$data=AllotingCopiesExaminer::join('marking_absent_students','marking_absent_students.id','=','alloting_copies_examiners.marking_absent_student_id')
		->join('users','users.id','=','alloting_copies_examiners.user_id')->where($conditions)
		->whereNull('alloting_copies_examiners.deleted_at')->whereNull('marking_absent_students.deleted_at')
		->orderby('alloting_copies_examiners.id','desc')->first(['users.ssoid','users.id','users.name','users.mobile']); 
		// dd($data);
		return $data;
	}

	public function getTheoryExaminerList(){
		// $current_exam_year_id = Config::get('global.admission_academicyear_id');
		// $current_exam_month_id = Config::get('global.current_exam_month_id'); 
		$theoryexaminer = Config::get('global.theoryexaminer');
		$conditions = array();
		// $conditions['users.exam_year'] = $current_exam_year_id;
		// $conditions['users.exam_month'] = $current_exam_month_id;
		$conditions['model_has_roles.role_id'] = $theoryexaminer;
		$conditions['model_has_roles.model_type'] = 'App\Models\User';
		$user_list = User::Join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
					->where($conditions)
					->pluck('ssoid','id');	
		return $user_list;
	} 
	
    public function getTheoryStartData(){
		$objController = new Controller();
		$combo_name = 'theory_marks_submission_start_date';
		$theory_start_date_arr = $objController->master_details($combo_name);
		return $theory_start_date_arr[1];
	}

	public function getTheoryEndData(){
		$objController = new Controller();
		$combo_name = 'theory_marks_submission_end_date';
		$theory_end_date_arr = $objController->master_details($combo_name);
		return $theory_end_date_arr[1];
		
	}
	
	public function getTheoryAllowOrNot(){
		$isValid = false;
		$thoeryStartData=$this->getTheoryStartData();
		$thoeryEndData=$this->getTheoryEndData(); 
		if(strtotime(date("Y-m-d H:i:s")) >= strtotime($thoeryStartData) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime($thoeryEndData)){ 
			$isValid = true;
		}
		return $isValid;
	}

	//yet not completed
	public function setAutoLockSubmitAfterHours($id=null,$course_id=null,$subject_id=null,$examcenter_id=null){
		$exam_year=CustomHelper::_get_selected_sessions();
		$exam_month=Config::get('global.current_exam_month_id');
	
		$beforeHours =  date('Y-m-d H:i:s', strtotime("-48 hours", strtotime(date("Y-m-d H:i:s"))));
	 
		$conditions=[
			'alloting_copies_examiners.marks_entry_completed' => 0,
			'alloting_copies_examiners.theory_lastpage_submitted_date' => $beforeHours
		];
		$filedsList = [
			'marks_entry_completed' => 1,
			'marks_entry_completed_date' => date("Y-m-d H:i:s")
		];
		$result = AllotingCopiesExaminer::where($conditions)->update($filedsList);
		return $result; 
	}

}




