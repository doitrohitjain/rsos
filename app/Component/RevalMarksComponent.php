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
use App\Models\RevalStudent;
use App\Models\RevalStudentSubject;
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

class RevalMarksComponent{ 

	public function getDistrictIdByUserId($user_id=null){
		$conditions = null;
		$result = array();
		if(!empty($user_id)){
			$conditions = ['id' => $user_id];
		} 
		return $result = DB::table('users')->where($conditions)->first();
	}

	public function getRevalMarksApplicationData($formId=null,$isPaginate=true,$aicenter_mapped_data_conditions=null){
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

		$controller_obj = new Controller;
		$combo_name = 'reval_exam_year';$reval_exam_year = $controller_obj->master_details($combo_name);
		$combo_name = 'reval_exam_month';$reval_exam_month = $controller_obj->master_details($combo_name);
		
		$reval_exam_year = @$reval_exam_year[1];$reval_exam_month = @$reval_exam_month[1];
		
		$rawQuery[0] = $rawQuery[1] =  $rawQuery[2] =  $rawQuery[3] =  $rawQuery[4] = 1;
		
		$rawQuery[0] = ' rs_reval_student_subjects.deleted_at is null and rs_student_allotment_marks.deleted_at is null and rs_student_allotments.exam_year = "' . $reval_exam_year . '" and ' . ' rs_student_allotments.exam_month = "' . $reval_exam_month . '"  ';
		$rawQuery[2] = ' rs_reval_student_subjects.exam_year = "' . $reval_exam_year . '" and ' . ' rs_reval_student_subjects.exam_month = "' . $reval_exam_month . '"  ';
		if(in_array('student_allotments.studentfixcode',$arraykeys)){
			$rawQuery[1] = ' rs_student_allotments.fixcode = "' . $conditions["student_allotments.studentfixcode"] . '"';
			unset($conditions["student_allotments.studentfixcode"]);
		} 
		
		if(in_array('examcenter_details.centerfixcode',$arraykeys)){
			$rawQuery[2] = ' rs_examcenter_details.fixcode = ' . $conditions["examcenter_details.centerfixcode"];
			unset($conditions["examcenter_details.centerfixcode"]);
		} 
		
	   	$master = array();
		$fields = array(
			'examcenter_details.fixcode as centerfixcode','student_allotments.fixcode as studentfixcode','reval_student_subjects.subject_id','student_allotment_marks.reval_is_subject_marks_entered',
			'student_allotment_marks.final_theory_marks as marks_on_answer_book_before_reval',
			'student_allotment_marks.reval_final_theory_marks as theory_marks_in_reval',
			'student_allotment_marks.reval_difference_after_reval as reval_difference_after_reval',
			'student_allotment_marks.reval_any_change  as reval_any_change',
			'student_allotment_marks.reval_change_in_result  as reval_change_in_result',
			'student_allotment_marks.reval_is_subject_result_change  as result_after_reval',
			'student_allotment_marks.reval_type_of_mistake',
			'reval_students.id','reval_students.student_id','reval_students.ai_code',
			'reval_students.stream','reval_students.course','reval_students.submitted','reval_students.challan_tid',
			'reval_students.locksumbitted','reval_students.enrollment as reval_students_enrollment','reval_students.total_fees','students.name',
			'students.gender_id','students.adm_type','reval_students.exam_month','reval_students.reval_type',
			'reval_students.is_self_filled','reval_students.is_eligible','students.enrollment','reval_student_subjects.final_result',
			'reval_student_subjects.total_marks','reval_student_subjects.id as reval_student_subjects_id','reval_student_subjects.sessional_marks','reval_student_subjects.final_theory_marks',
			'reval_student_subjects.final_practical_marks','reval_student_subjects.final_result_after_reval',
			'reval_student_subjects.total_marks_after_reval','reval_student_subjects.final_theory_marks_after_reval','student_allotment_marks.id as student_allotment_marks_id','reval_student_subjects.reval_rte_status'
		);
		$groupByText = "rs_reval_student_subjects.student_id, rs_reval_student_subjects.subject_id";
		$orderByText = "rs_reval_student_subjects.subject_id, rs_students.enrollment";
	   	if($isPaginate){ 
			$revalMarksDefaultPageLimit = config("global.revalMarksDefaultPageLimit");
			$master = RevalStudent::Join('students', 'students.id', '=', 'reval_students.student_id')
			->Join('student_allotments', 'student_allotments.student_id', '=', 'reval_students.student_id')
			->Join('reval_student_subjects', 'reval_students.id', '=', 'reval_student_subjects.reval_id')
			->Join('examcenter_details', 'student_allotments.examcenter_detail_id', '=', 'examcenter_details.id')
			->Join("student_allotment_marks",function($join){
				$join->on("student_allotments.id" , "=" ,"student_allotment_marks.student_allotment_id");
				$join->on("student_allotment_marks.subject_id" , "=" ,"reval_student_subjects.subject_id");
			})
			->where($conditions)
			->whereRaw(@$rawQuery[0])
			->whereRaw(@$rawQuery[1])
			->whereRaw(@$rawQuery[2])
			->select($fields)
			->orderByRaw($orderByText)
			->groupByRaw($groupByText)
			->paginate($revalMarksDefaultPageLimit);			
	   	}else{
			$master = RevalStudent::Join('students', 'students.id', '=', 'reval_students.student_id')
			->Join('student_allotments', 'student_allotments.student_id', '=', 'reval_students.student_id')
			->Join('reval_student_subjects', 'reval_students.id', '=', 'reval_student_subjects.reval_id')
			->Join('examcenter_details', 'student_allotments.examcenter_detail_id', '=', 'examcenter_details.id')
			->Join("student_allotment_marks",function($join){
				$join->on("student_allotments.id" , "=" ,"student_allotment_marks.student_allotment_id");
				$join->on("student_allotment_marks.subject_id" , "=" ,"reval_student_subjects.subject_id");
			})
			->where($conditions)
			->whereRaw(@$rawQuery[0])
			->whereRaw(@$rawQuery[1])
			->whereRaw(@$rawQuery[2])
			->select($fields)
			->orderByRaw($orderByText)
			->groupByRaw($groupByText)
			->get();  
	  	}  
		return $master; 
   	}

	public function getRevalMarksPdfData($subject_id=null,$formId=null,$isPaginate=false){
		   
		$rawQuery[0] = $rawQuery[1] =  $rawQuery[2] =  $rawQuery[3] =  $rawQuery[4] = 1;
		
		$controller_obj = new Controller;
		$combo_name = 'reval_exam_year';$reval_exam_year = $controller_obj->master_details($combo_name);
		$combo_name = 'reval_exam_month';$reval_exam_month = $controller_obj->master_details($combo_name);
		$reval_exam_year = @$reval_exam_year[1];$reval_exam_month = @$reval_exam_month[1];
		 
		$rawQuery[0] = ' rs_reval_student_subjects.deleted_at is null and rs_student_allotment_marks.deleted_at is null and rs_student_allotments.exam_year = "' . $reval_exam_year . '" and ' . ' rs_student_allotments.exam_month = "' . $reval_exam_month . '"  ';
		$rawQuery[2] = ' rs_reval_student_subjects.exam_year = "' . $reval_exam_year . '" and ' . ' rs_reval_student_subjects.exam_month = "' . $reval_exam_month . '"  ';
		
		$conditions = Session::get($formId. '_conditions');
		if($subject_id > 0){
			$rawQuery[1] = ' rs_reval_student_subjects.subject_id = ' . $subject_id;
		} 
		$orderByText = "rs_student_allotment_marks.subject_code, rs_students.enrollment";
		$groupByText = "rs_reval_student_subjects.student_id, rs_reval_student_subjects.subject_id";
	   	$master = array();
		$fields = array(
			'examcenter_details.fixcode as centerfixcode','student_allotments.fixcode as studentfixcode','reval_student_subjects.subject_id','student_allotment_marks.reval_is_subject_marks_entered',
			'student_allotment_marks.final_theory_marks as marks_on_answer_book_before_reval',
			'student_allotment_marks.reval_final_theory_marks as theory_marks_in_reval',
			'student_allotment_marks.reval_difference_after_reval as reval_difference_after_reval',
			'student_allotment_marks.reval_any_change  as reval_any_change',
			'student_allotment_marks.reval_change_in_result  as reval_change_in_result',
			'student_allotment_marks.reval_is_subject_result_change  as result_after_reval',
			'student_allotment_marks.reval_type_of_mistake',
			'reval_students.id','reval_students.student_id','reval_students.ai_code',
			'reval_students.stream','reval_students.course','reval_students.submitted','reval_students.challan_tid',
			'reval_students.locksumbitted','reval_students.enrollment as reval_students_enrollment','reval_students.total_fees','students.name',
			'students.gender_id','students.adm_type','reval_students.exam_month','reval_students.reval_type',
			'reval_students.is_self_filled','reval_students.is_eligible','students.enrollment','reval_student_subjects.final_result',
			'reval_student_subjects.total_marks','reval_student_subjects.id as reval_student_subjects_id','reval_student_subjects.sessional_marks','reval_student_subjects.final_theory_marks',
			'reval_student_subjects.final_practical_marks','reval_student_subjects.final_result_after_reval',
			'reval_student_subjects.total_marks_after_reval','reval_student_subjects.final_theory_marks_after_reval','student_allotment_marks.id as student_allotment_marks_id','reval_student_subjects.reval_rte_status','student_allotment_marks.subject_code',
		);
		$groupByText = "rs_reval_student_subjects.student_id, rs_reval_student_subjects.subject_id";

		$orderByText = "rs_student_allotment_marks.subject_code, rs_students.enrollment";
	   	if($isPaginate){ 
			$revalMarksDefaultPageLimit = config("global.revalMarksDefaultPageLimit");
			$master = RevalStudent::Join('students', 'students.id', '=', 'reval_students.student_id')
			->Join('student_allotments', 'student_allotments.student_id', '=', 'reval_students.student_id')
			->Join('reval_student_subjects', 'reval_students.id', '=', 'reval_student_subjects.reval_id')
			->Join('examcenter_details', 'student_allotments.examcenter_detail_id', '=', 'examcenter_details.id')
			->Join("student_allotment_marks",function($join){
				$join->on("student_allotments.id" , "=" ,"student_allotment_marks.student_allotment_id");
				$join->on("student_allotment_marks.subject_id" , "=" ,"reval_student_subjects.subject_id");
			})
			->where($conditions)
			->whereRaw(@$rawQuery[0])
			->whereRaw(@$rawQuery[1])
			->whereRaw(@$rawQuery[2])
			->select($fields)
			->orderByRaw($orderByText)
			->groupByRaw($groupByText)
			->paginate($revalMarksDefaultPageLimit);			
	   	}else{
			$master = RevalStudent::Join('students', 'students.id', '=', 'reval_students.student_id')
			->Join('student_allotments', 'student_allotments.student_id', '=', 'reval_students.student_id')
			->Join('reval_student_subjects', 'reval_students.id', '=', 'reval_student_subjects.reval_id')
			->Join('examcenter_details', 'student_allotments.examcenter_detail_id', '=', 'examcenter_details.id')
			->Join("student_allotment_marks",function($join){
				$join->on("student_allotments.id" , "=" ,"student_allotment_marks.student_allotment_id");
				$join->on("student_allotment_marks.subject_id" , "=" ,"reval_student_subjects.subject_id");
			})
			->where($conditions)
			->whereRaw(@$rawQuery[0])
			->whereRaw(@$rawQuery[1])
			->whereRaw(@$rawQuery[2])
			->select($fields)
			->orderByRaw($orderByText)
			->groupByRaw($groupByText)
			->get();  
	  	}  
		return $master; 
   	}

	
	public function reval_process_result($inputs=null) {
		
		$custom_component_obj= new CustomComponent;	
		$controller_obj = new Controller;		
		$resultprocess_component_obj = new ResultProcessCustomComponent; 
		$subjects =  $controller_obj->subjectList();
		$subjectdata = $subjectCodes =  array();
		$counter=0; 
		$conditions = array(); 
		
		$student_allotment_marks_id = Crypt::decrypt(@$inputs['key']);
		$revalStSubId = Crypt::decrypt(@$inputs['revalStSubId']);
		$revalSubjectId = $controller_obj->subjectIdByCode(@$inputs['revalStSubCode']);
		$item = @$inputs['item'];
		$valTheoryMarksInput = @$inputs['val'];
		
		if($item != "reval_final_theory_marks"){
			return false;
		}
		
		
		$combo_name = 'reval_exam_year';$reval_exam_year = $controller_obj->master_details($combo_name);
		$combo_name = 'reval_exam_month';$reval_exam_month = $controller_obj->master_details($combo_name);
		
		$reval_exam_year = @$reval_exam_year[1];$reval_exam_month = @$reval_exam_month[1];
		$conditions ["student_allotments.exam_year"]= $reval_exam_year;
		$conditions ["student_allotments.exam_month"]= $reval_exam_month; 
		$conditions ["student_allotment_marks.id"]= $student_allotment_marks_id;  
		
		$revalStudentSubject = RevalStudentSubject::where("id","=",$revalStSubId)->first();  
		// dd($revalStudentSubject);
		
		$resultdata = $students = StudentAllotmentMark::
			join('student_allotments', 'student_allotments.student_id', '=', 'student_allotment_marks.student_id')
					->where($conditions)->whereNull("student_allotments.deleted_at")
					->get()
					->toArray(); 
		
		/* Grace Marks Start */
			if(!empty($resultdata)){ //grace marks increes
				$increesMarks = 1;
				$result_code_syct = "SYCT";
				$result_code_sycp = "SYCP";
				$result_code_syc = "SYC";
				
				foreach($resultdata as $key=>$val){
					$subjectId = $subjectid = $val['subject_id'];
					$course = $val['course'];
					$datasave = array();
					
					$marksarr = $resultprocess_component_obj->getSubjectMaxMinMarksMaster($subjectId,81);
					$datasave['TH_MIN'] = $marksarr['TH_MIN'];
					$datasave['TH_MAX'] = $marksarr['TH_MAX'];
					$datasave['PR_MIN'] = $marksarr['PR_MIN'];
					$datasave['PR_MAX'] = $marksarr['PR_MAX'];
					$datasave['subject_id'] = $subjectid;
					$datasave['practical_marks'] = @$revalStudentSubject->final_practical_marks;;
					$datasave['sessional_marks'] = @$revalStudentSubject->sessional_marks;;
					$datasave['is_practical_type'] = 'No';
					if(in_array($val['subject_id'],array(23,24,25,28,33,35,37,38,39))){
						$datasave['is_practical_type'] = 'Yes';
					}
					
					$theoryMarks = $newtheoryMarks = $valTheoryMarksInput;
					$datasave['is_recalulate_theory'] = 'No';
					if($valTheoryMarksInput >= 0 && $valTheoryMarksInput <= 100){
						if($revalStudentSubject->sessional_marks == 0 || $revalStudentSubject->sessional_marks == '999'){}else{
							$newtheoryMarks=round($theoryMarks * 0.9);
							$datasave['is_recalulate_theory'] = 'Yes';
						}
						$datasave['new_theory_marks'] = $newtheoryMarks;
					} 
					
					$theoryMarks =  $calTotalMarks = $valTheoryMarksInput = $newtheoryMarks;

					$calTotalMarks = $calTotalMarks + $revalStudentSubject->sessional_marks;
					if(@$revalStudentSubject->final_practical_marks != "999"){
						$calTotalMarks = $valTheoryMarksInput + $revalStudentSubject->final_practical_marks + $revalStudentSubject->sessional_marks;//here need to be change adding sessional marks from rss table
					}  
					
					if(isset($course) && !empty($course)){
						if($course == 10){
							if($calTotalMarks == 32){
								$datasave['total_marks_after_reval'] = $calTotalMarks + $increesMarks;
								$datasave['grace_marks_after_reval'] = $increesMarks;
								$datasave['is_grace_marks_given_after_reval'] = 1;//incress in theory
							}else{
								$datasave['total_marks_after_reval'] = $calTotalMarks;
								$datasave['grace_marks_after_reval'] = 0;
								$datasave['is_grace_marks_given_after_reval'] = 0;
							} 
							
							$marksarr = $resultprocess_component_obj->getSubjectMaxMinMarksMaster($subjectId,81);
							$tempDatasave['final_result_after_reval'] = $result_code_syc;
							if($calTotalMarks >= $marksarr['TH_MIN'] && $calTotalMarks <= $marksarr['TH_MAX']){ 
								$tempDatasave['final_result_after_reval'] = 'P';
							} 
							$datasave['final_result_after_reval'] = @$tempDatasave['final_result_after_reval'];
							// RevalStudentSubject::where('id', '=', $revalStSubId)->update($datasave);
							return $datasave;
							continue;
						}else if($course == 12){
							// $tempDatasave['final_result_after_reval'] = $result_code_syc;
							$tempDatasave['final_result_after_reval'] = null; 
							$tempDatasave['is_only_theory_subject'] = null;
							// dd($revalStudentSubject);
							if(in_array($val['subject_id'],array(23,24,25,28,33,35,37,38,39))){ //practical subjects  
								if(($valTheoryMarksInput == 0 || $valTheoryMarksInput == 999) && (@$revalStudentSubject->final_practical_marks == 0 || @$revalStudentSubject->final_practical_marks == 999)){
									$tempDatasave['final_result_after_reval'] = $result_code_syc;
								}else if(($valTheoryMarksInput == 0 || $valTheoryMarksInput == 999)){
									$tempDatasave['final_result_after_reval'] = $result_code_syct;
								}else if(@$revalStudentSubject->final_practical_marks == 0 || @$revalStudentSubject->final_practical_marks == 999){
									$tempDatasave['final_result_after_reval'] = $result_code_sycp;
								}
							}else{ 
								$tempDatasave['is_only_theory_subject'] = $result_code_syc;
								if(($valTheoryMarksInput == 0 || $valTheoryMarksInput == 999)){
									$tempDatasave['final_result_after_reval'] = $result_code_syc;
								}
							}
							
							
							
							if(!empty($tempDatasave['is_only_theory_subject']) && $tempDatasave['is_only_theory_subject'] == $result_code_syc){
								if($calTotalMarks == 32){
									$datasave['total_marks_after_reval'] = $calTotalMarks + $increesMarks;
									$datasave['grace_marks_after_reval'] = $increesMarks;
									$datasave['is_grace_marks_given_after_reval'] = 1;//incress in theory
								}else{
									$datasave['total_marks_after_reval'] = $calTotalMarks;
									$datasave['grace_marks_after_reval'] = 0;
									$datasave['is_grace_marks_given_after_reval'] = 0;
								}
								$marksarr = $resultprocess_component_obj->getSubjectMaxMinMarksMaster($subjectId,81);
								 
								
								$tempDatasave['final_result_after_reval'] = $result_code_syc;
								if($calTotalMarks >= $marksarr['TH_MIN'] && $calTotalMarks <= $marksarr['TH_MAX']){ 
									$tempDatasave['final_result_after_reval'] = 'P';
								} 
								$datasave['final_result_after_reval'] = @$tempDatasave['final_result_after_reval'];
								//RevalStudentSubject::where('id', '=', $revalStSubId)->update($datasave);
								return $datasave;
								continue;
							}else{
								
								$practicalmarks = @$revalStudentSubject->final_practical_marks;
								if(@$revalStudentSubject->final_practical_marks == 999){
									$practicalmarks = null;
								} 
								
								if($tempDatasave['final_result_after_reval'] != $result_code_syc){
									$marksarr = $resultprocess_component_obj->getSubjectMaxMinMarksMaster($subjectId,81);
									//$tempDatasave['final_result_after_reval'] = null; 
									
									
									
									if(($theoryMarks >= $marksarr['TH_MIN'] && $theoryMarks <= $marksarr['TH_MAX']) && ($practicalmarks >= $marksarr['PR_MIN'] && $practicalmarks <= $marksarr['PR_MAX']))
									{ 
										$tempDatasave['final_result_after_reval'] = 'P';
									}else{

										

									
										if($practicalmarks < $marksarr['PR_MIN'] && $theoryMarks < $marksarr['TH_MIN'])
										{  
											$tempDatasave['final_result_after_reval'] = $result_code_syc;
										}else if($theoryMarks < $marksarr['TH_MIN'])
										{  
											$tempDatasave['final_result_after_reval'] = $result_code_syct;
										}else if($practicalmarks < $marksarr['PR_MIN']){  
											$tempDatasave['final_result_after_reval'] = $result_code_sycp;
										} 
										
										// print_r($theoryMarks);
										// echo "<br>";
										// print_r($marksarr['TH_MIN']); 
										// echo "<br>";
										// print_r($practicalmarks); 
										// dd($tempDatasave);
										
									}
								}
							$datasave['final_result_after_reval'] = $tempDatasave['final_result_after_reval'];
							$datasave['total_marks_after_reval'] = $calTotalMarks;
							$datasave['grace_marks_after_reval'] = 0;
							$datasave['is_grace_marks_given_after_reval'] = 0;
							// dd($datasave);
							
							if($tempDatasave['final_result_after_reval'] == $result_code_syct){
								// total - practical and checking if 1 lesser with th_min then true
								$tempGraceCal = $calTotalMarks - $revalStudentSubject->final_practical_marks;
								
								if(($tempGraceCal+$increesMarks) == $marksarr['TH_MIN']){
									$datasave['total_marks_after_reval'] = $calTotalMarks + $increesMarks;
									$datasave['final_result_after_reval'] = "P";;
									$datasave['grace_marks_after_reval'] = $increesMarks;
									$datasave['is_grace_marks_given_after_reval'] = 1;//incress in theory
									// RevalStudentSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
									// unset($datasave);
									// $this->_updateGraceMarksThPr($val['id'],'t');
									// continue; 
								}
							}else if($tempDatasave['final_result_after_reval'] == $result_code_sycp){  
								// if p 1 lesser then p_min then true. 
								if(($revalStudentSubject->final_practical_marks+$increesMarks) == $marksarr['PR_MIN']){
									$datasave['total_marks_after_reval'] = $calTotalMarks + $increesMarks;
									$datasave['final_result_after_reval'] = "P";;
									$datasave['grace_marks_after_reval'] = $increesMarks;
									$datasave['is_grace_marks_given_after_reval'] = 2;//incress in practical
									// RevalStudentSubject::where('prepare_exam_subjects.id', '=', $val['id'])->update($datasave);
									// unset($datasave);
									// $this->_updateGraceMarksThPr($val['id'],'p');
									// continue;
								}
							}else{ 
								
								/* Error 1 */ 
									/* if($calTotalMarks == 32){
										$datasave['total_marks_after_reval'] = $calTotalMarks + $increesMarks;
										$datasave['grace_marks_after_reval'] = $increesMarks;
										$datasave['is_grace_marks_given_after_reval'] = 1;//incress in theory
									}else{
										$datasave['total_marks_after_reval'] = $calTotalMarks;
										$datasave['grace_marks_after_reval'] = 0;
										$datasave['is_grace_marks_given_after_reval'] = 0;
									}
									$marksarr = $resultprocess_component_obj->getSubjectMaxMinMarksMaster($subjectId,81);								
									// $tempDatasave['final_result_after_reval'] = $result_code_syc;
									if($calTotalMarks >= $marksarr['TH_MIN'] && $calTotalMarks <= $marksarr['TH_MAX']){ 
										$tempDatasave['final_result_after_reval'] = 'P';
									} 
									$datasave['final_result_after_reval'] = @$tempDatasave['final_result_after_reval'];
									//RevalStudentSubject::where('id', '=', $revalStSubId)->update($datasave); */
								/* Error 1 End */ 
							}




								
								
								return $datasave;
								continue;
								
							} 
						} //12th course end
					}
				}
			} 
		/* Grace Marks End */


		// $data['total_marks_after_reval'] = @$revalStudentSubject->total_marks_after_reval;
		// $data['final_result_after_reval'] = @$revalStudentSubject->final_result_after_reval;
		
		
		
		
		
		dd($resultdata);
	}
	
}


