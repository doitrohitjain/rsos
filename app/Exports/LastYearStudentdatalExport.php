<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use App\Component\MarksheetCustomComponent; 
use Session;
use App\Component\CustomComponent;
use App\Http\Controllers\Controller;

class LastYearStudentdatalExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
	
	
	//protected $id;
	function __construct($request) {
		$this->course = $request->course;
		$this->exam_year = $request->exam_year;
		$this->offsetstart = $request->offsetstart;
		$this->limit = $request->limit;
	}
	
	
    public function collection(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '0');
		$students = DB::select('call getLastYearStudentSubjectDetails(?,?,?,?)',array($this->course,$this->exam_year,$this->offsetstart,$this->limit));
		$finalArr = array();
		
		$custom_controller_obj = new Controller;
		$combo_name = 'gender';$gender = $custom_controller_obj->master_details($combo_name);
		$categorya = ["1"=>"GEN","2"=>"SC","3"=>"ST","4"=>"X","5"=>"OBC","6"=>"X","7"=>"X"];
        $religion = ["1"=>"H","2"=>"I","3"=>"S","4"=>"C","5"=>"J","6"=>"B","7"=>"O"];
		$combo_name = 'admission_sessions';$admission_sessions = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'stream_id';$stream = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'exam_month';$exam_month = $custom_controller_obj->master_details($combo_name);
		$custom_component_obj = new CustomComponent;
		$course= ['10'=>'X','12'=>'XII'];
		$grace=null;
		$aiCenters = $custom_component_obj->getAiCenters();
		$marksheet_component_obj = new MarksheetCustomComponent;
		foreach(@$students as $key => $student){
			
			$aadhar_number = null;
			if(@$student->aadhar_number){
				$trim_aadhar_number = trim($student->aadhar_number);
				$trim_aadhar_number = $custom_controller_obj->_getOnlyNumbers($trim_aadhar_number);
				if(strlen($trim_aadhar_number) == 12){
					$aadhar_number = $trim_aadhar_number;
				}
			}
			
			
			
			$finalArr[$student->id]['id'] = $student->id;
			$finalArr[$student->id]['enrollment'] = $student->enrollment; 
			$finalArr[$student->id]['course'] = @$course[$student->course]; 
			$finalArr[$student->id]['name'] = $custom_controller_obj->_removeSpecialCharOtherThenSpace(@$student->name); 
			$finalArr[$student->id]['gender'] = @$gender[$student->gender_id]; 
			$finalArr[$student->id]['ai_code'] = $student->ai_code; 
			$finalArr[$student->id]['ai_center_name'] = @$aiCenters[$student->ai_code]; 
			$finalArr[$student->id]['address'] = @$student->address; 
			$finalArr[$student->id]['cast'] =@$categorya[$student->category_a]; 



			$yearDisplay = array("13_2" => 2017 ,"13_1" => 2018,"120_2" => 2018 ,"120_1" => 2019,"121_2" => 2019 ,
			                     "121_1" => 2020,"122_1" => 2021,"123_1" => 2022,"124_1" => 2023,"124_2" => 2022,
								 "125_1" => 2024,"125_2" => 2023);
								 
			$monthDisplay = array("13_2" => '10',"13_1" => '03',"120_2" => '10' ,"120_1" => '03',"121_2" => '10' ,
			                     "121_1" => '10',"122_1" => '09',"123_1" => '05',"124_1" => '10',"124_2" => '03',
								 "125_1" => '03',"125_2" => '10');
			
		

			$index = $student->exam_year . "_" . $student->exam_month;
			
			$finalArr[$student->id]['exam_year'] = $yearDisplay[$index];
			$finalArr[$student->id]['exam_month'] = $monthDisplay[$index];
			
			// $finalArr[$student->id]['exam_year'] =@$admission_sessions[@$student->exam_year];
            // $finalArr[$student->id]['exam_month'] =@$exam_month[@$student->exam_month];	

			
			$finalArr[$student->id]['father_name'] = $custom_controller_obj->_removeSpecialCharOtherThenSpace(@$student->father_name);
 			$finalArr[$student->id]['mother_name'] = $custom_controller_obj->_removeSpecialCharOtherThenSpace(@$student->mother_name);
			$finalArr[$student->id]['DOB_figures'] = @$student->DOB_figures; 
			$finalArr[$student->id]['email'] = @$student->email;
            $finalArr[$student->id]['mobile'] = @$student->mobile;
            $finalArr[$student->id]['aadhaar_no'] = @$aadhar_number;
			$finalArr[$student->id]['religion'] = @$religion[@$student->religion];
			$finalArr[$student->id]['disability'] = @$student->disability;
			$finalArr[$student->id]['stream'] = @$stream[@$student->exam_month];
			$finalArr[$student->id]['created_at'] =@$student->created_at;
			//$finalArr[$student->id]['DOB_figures'] = date("d/m/Y"); 
			$finalArr[$student->id]['DOB_words'] = @$student->DOB_words;
			$finalArr[$student->id]['subjects'][$student->subject_id]['subject_id'] = $student->subject_id;
			$finalArr[$student->id]['subjects'][$student->subject_id]['subject_name'] = $student->subject_name;
			$finalArr[$student->id]['subjects'][$student->subject_id]['subject_code'] = $student->subject_code;
			$finalArr[$student->id]['subjects'][$student->subject_id]['max_marks'] = $student->max_marks;
			$finalArr[$student->id]['subjects'][$student->subject_id]['min_marks'] = $student->min_marks;
			$finalArr[$student->id]['subjects'][$student->subject_id]['theory_max_marks'] = $student->theory_max_marks;
			$finalArr[$student->id]['subjects'][$student->subject_id]['theory_min_marks'] = $student->theory_min_marks;
			$finalArr[$student->id]['subjects'][$student->subject_id]['practical_max_marks'] = $student->practical_max_marks;
			$finalArr[$student->id]['subjects'][$student->subject_id]['practical_min_marks'] = $student->practical_min_marks;
			$finalArr[$student->id]['subjects'][$student->subject_id]['sessional_max_marks'] = $student->sessional_max_marks;
			$finalArr[$student->id]['subjects'][$student->subject_id]['sessional_min_marks'] = $student->sessional_min_marks;
			$theory_arks = $student->theory_arks; 
			$pratical_arks  = $student->practical_marks;
			
			if(@$student->theory_arks){
				if($student->theory_arks == '999'){
					$theory_arks = "AB";
				}else if ($student->theory_arks == '888'){
					$theory_arks = "SYC";
				}else if ($student->theory_arks == '777'){
					$theory_arks = "SYCT";
				}else if ($student->theory_arks == '666'){
					$theory_arks = "SYCP";
				}else if ($student->theory_arks == '444'){
					$theory_arks = "RWH";
				}else if ($student->theory_arks == '333'){
					$theory_arks = "RW";
				}else if ($student->theory_arks == '222'){
					$theory_arks = "WH";
				}else if ($student->theory_arks == 'P' || $student->theory_arks == 'p' || $student->theory_arks == 'PASS' || $student->theory_arks == 'pass' || $student->theory_arks == 'Pass'){
					$theory_arks = "PASS";
				}else{
					$theory_arks = $student->theory_arks;
				}
			}
			$finalArr[$student->id]['subjects'][$student->subject_id]['theory_arks'] = $theory_arks;
			if(@$student->practical_marks){
				
				if($student->practical_marks == '999'){
					$pratical_arks = "AB";
				}else if ($student->practical_marks == '888'){
					$pratical_arks = "SYC";
				}else if ($student->practical_marks == '777'){
					$pratical_arks = "SYCT";
				}else if ($student->practical_marks == '666'){
					$pratical_arks = "SYCP";
				}else if ($student->practical_marks == '444'){
					$pratical_arks = "RWH";
				}else if ($student->practical_marks == '333'){
					$pratical_arks = "RW";
				}else if ($student->practical_marks == '222'){
					$pratical_arks = "WH";
				}else if ($student->practical_marks == 'P' || $student->practical_marks == 'p' || $student->practical_marks == 'PASS' || $student->practical_marks == 'pass' || $student->practical_marks == 'Pass'){
					$pratical_arks = "PASS";
				}else{
					$theory_arks = $student->practical_marks;
				}
			}
			$finalArr[$student->id]['subjects'][$student->subject_id]['practical_marks'] = @$pratical_arks;
			$finalArr[$student->id]['subjects'][$student->subject_id]['sessional_arks'] = @$student->sessional_arks;
			$finalArr[$student->id]['subjects'][$student->subject_id]['total_marks'] = @$student->total_marks; 
			$finalArr[$student->id]['subjects'][$student->subject_id]['grade'] = @$marksheet_component_obj->getGradeOfMarks($student->total_marks);
			$final_result = null;
			if(@$student->final_result){
				if($student->final_result == '999'){
					$final_result = "AB";
				}else if ($student->final_result == 'P' || $student->final_result == 'p' || $student->final_result == 'PASS' || $student->final_result == 'pass' || $student->final_result == 'Pass'){
					$final_result = "PASS";
				}else{
					$final_result = "FAIL";
				}
			}
			$finalArr[$student->id]['subjects'][$student->subject_id]['final_result'] = $final_result;
			
			if(@$student->er_total_marks){
				$finalArr[$student->id]['er_total_marks'] = $student->er_total_marks;
			}else{
				$finalArr[$student->id]['er_total_marks'] = 0;
			}
			
			$finalArr[$student->id]['er_total_marks_in_words'] = null;
			if($finalArr[$student->id]['er_total_marks'] >= 0){
				$finalArr[$student->id]['er_total_marks_in_words'] = $marksheet_component_obj->numberInWord($finalArr[$student->id]['er_total_marks']);
			}  
			$final_results=$student->er_final_result;
			if($student->er_final_result == 'PASS' || $student->er_final_result == 'PASS' || $student->er_final_result == 'pass' || $student->er_final_result == 'Pass'){
				$final_results='Qualified';
			}
			$finalArr[$student->id]['er_percent_marks'] = $student->er_percent_marks;
			$finalArr[$student->id]['er_final_result'] = $final_results;
			$finalArr[$student->id]['er_result_date'] = $student->er_result_date;
			$finalArr[$student->id]['er_grand_total_marks'] = $student->er_total_marks;
		}
		$output = array();
		$i = 0;
		foreach(@$finalArr as $student_id => $value){
			$CGPA = 0;
			$er_total_marks = '0';
			if(@$value['er_percent_marks'] && $value['er_percent_marks'] > 0){
				$CGPA = $value['er_percent_marks']/10;
			}

			if(@$value['er_total_marks'] && $value['er_total_marks'] > 0){
				$er_total_marks = @$value['er_total_marks'];
			}

			$total_marks='0';
			$total_min='0';
			foreach(@$value['subjects'] as $subject_id => $subjectminmarks){
				// $total_marks +=$subjectminmarks['max_marks'];
				$total_min +=$subjectminmarks['min_marks'];
			}
			$total_marks = 500;
			$output[$i] = array(
			    'ORG_CODE' => "8094",
				'ORG_NAME' => "RSOS",
				'ORG_NAME_L'=>"",
				'ORG_ADDRESS'=>'',
				'ORG_CITY'=>'',
				'ORG_STATE'=>'',
				'ORG_PIN'=>'',
				'STD'=>@$value['course'],
				'STREAM'=>'',
				'STREAM_L'=>'',
				'SESSION'=>'',
			    'id' => @$value['enrollment'],      			
				'enrollment' => @$value['enrollment'],
				'AADHAAR_NO'=>"",//@$value['aadhaar_no'],
                'LOCKER_ID'=>"",
				'name' => @$value['name'],
				'gender' =>substr(@$value['gender'], 0, 1),
				'DOB_figures' => $value['DOB_figures'],
				'BLOOD_GROUP'=>"",
				'caste' => "",//@$value['cast'],
				'RELIGION'=>"",//@$value['religion'],
				'NATIONALITY'=>"",//'IN',
				'PH'=>"",//@$value['disability'],
				'MOBILE'=>"",//@$value['mobile'],
				'EMAIL'=>"",//@$value['email'],
                'father_name' =>@$value['father_name'],
				'mother_name' =>@$value['mother_name'],
				'GNAME' =>"",
				'STUDENT_ADDRESS' =>"",//@$value['address'],
				'PHOTO' =>"",
				'SIGN' =>"",
				'MRKS_REC_STATUS' => "O",
				'er_final_result'=>@$value['er_final_result'],		
				'YEAR'=> @$value['exam_year'],
				'MONTH'=>@$value['exam_month'],
				'DIVISION'=>"",
				'GRADE'=>"",
				'er_percent_marks'=>@$value['er_percent_marks'],
				'DOR'=> "",//@$value['created_at'],//T created date in students 
				'DOI'=> "",//@$value['er_result_date'],//T result date in exam_results
				'DOV'  =>"",
				'CERT_NO'    =>"",
				'EXAM_TYPE'  =>"", 
				'TOT_MAX'  =>$total_marks,  //T (sub of all subjects max marks)
				'TOT_MIN'  => "",  // $total_min T (sub of all subjects min marks)
				'TOT_MRKS'   =>$er_total_marks,
				'TOT_MRKS_WRDS'   => @$value['er_total_marks_in_words'], //T
				'GRACE'   =>"",//$grace,
				'TOT_MARKS_AFTER_GRACE'    => "",
				'TOT_MARKS_AFTER_GRACE_WRDS' => "",
				'CGPA'  =>""
			);
			
			$subjectCounter=1;
			
			
			foreach(@$value['subjects'] as $subject_id => $subject){
				$output[$i]['subject_name_'. $subjectCounter] = @$subject['subject_name'];
				$output[$i]['subject_code_'. $subjectCounter] = @$subject['subject_code'];
				$output[$i]['max_marks_'. $subjectCounter] = @$subject['max_marks'];
				$output[$i]['min_max_'. $subjectCounter] = "";//@$subject['min_marks'];
				$output[$i]['th_max_'. $subjectCounter] = @$subject['theory_max_marks'];
				$output[$i]['th_min_'. $subjectCounter] = @$subject['theory_min_marks'];
				$output[$i]['pr_max_'. $subjectCounter] = @$subject['practical_max_marks'];
				$output[$i]['pr_min_'. $subjectCounter] = @$subject['practical_min_marks'];
				$output[$i]['ce_max_'. $subjectCounter] = @$subject['sessional_max_marks'];
				$output[$i]['ce_min_'. $subjectCounter] = @$subject['sessional_min_marks'];
				$output[$i]['theory_arks_'. $subjectCounter] = @$subject['theory_arks'];
				$output[$i]['pratical_arks_'. $subjectCounter] =$subject['practical_marks'];
				$output[$i]['sessional_arks_'. $subjectCounter] = $subject['sessional_arks'];
				$output[$i]['total_marks_'. $subjectCounter] = @$subject['total_marks'];
				$output[$i]['subject_status_'. $subjectCounter] = @$subject['final_result'];;
				$output[$i]['grade_'. $subjectCounter] = @$subject['grade'];
				$output[$i]['subject_grade_points_'. $subjectCounter] = @$subject['grade'];
				$output[$i]['subject_credit_'. $subjectCounter] = "";
				$output[$i]['subject_credit_points_'. $subjectCounter] = "";
				$output[$i]['subject_grace_'. $subjectCounter] = "";
				$output[$i]['subject_addl_filed_1_'. $subjectCounter] = "";
				$output[$i]['subject_addl_filed_2_'. $subjectCounter] = "";
				$subjectCounter++;
			} 
			for($di=$subjectCounter;$di<=7;$di++){
				$output[$i]['subject_name_'. $di] = "";
				$output[$i]['subject_code_'. $di] = "";
				$output[$i]['subject_max_'. $di] = "";
				$output[$i]['subject_min_'. $di] = "";
				$output[$i]['subject_th_max_'. $di] = "";
				$output[$i]['subject_th_min_'. $di] = "";
				$output[$i]['subject_pr_max_'. $di] = "";
				$output[$i]['subject_pr_min_'. $di] = "";
				$output[$i]['subject_ce_max_'. $di] = "";
				$output[$i]['subject_ce_min_'. $di] = "";
				$output[$i]['theory_arks_'. $di] = "";
				$output[$i]['pratical_marks_'. $di] ="";
				$output[$i]['sessional_marks_'. $di] ="";
				$output[$i]['total_marks_'. $di] = "";
				$output[$i]['subject_status_'. $di] = "";
				$output[$i]['grade_'. $di] = "";
				$output[$i]['subject_grade_points_'. $di] = "";
				$output[$i]['subject_credit_'. $di] = "";
				$output[$i]['subject_credit_points_'. $di] = "";
				$output[$i]['subject_grace_'. $di] = "";
				$output[$i]['subject_addl_filed_1_'. $di] = "";
				$output[$i]['subject_addl_filed_2_'. $di] = "";
			}
			
			$i++;
		}
		
		return collect($output);
    }

	public function headings(): array{
        return ['ORG_CODE','ORG_NAME','ORG_NAME_L','ORG_ADDRESS','ORG_CITY','ORG_STATE','ORG_PIN','STD','STREAM','STREAM_L',
				'SESSION','REGN_NO','RROLL','AADHAAR_NO','LOCKER_ID','CNAME','GENDER','DOB','BLOOD_GROUP','CASTE','RELIGION',
				'NATIONALITY','PH','MOBILE','EMAIL','FNAME','MNAME','GNAME','STUDENT_ADDRESS','PHOTO','SIGN','MRKS_REC_STATUS',
				'RESULT','YEAR','MONTH','DIVISION','GRADE','PERCENT','DOR','DOI','DOV','CERT_NO','EXAM_TYPE','TOT_MAX','TOT_MIN',
				'TOT_MRKS','TOT_MRKS_WRDS','GRACE','TOT_MARKS_AFTER_GRACE','TOT_MARKS_AFTER_GRACE_WRDS','CGPA','SUB1NM','SUB1',
				'SUB1MAX','SUB1MIN','SUB1_TH_MAX','SUB1_TH_MIN','SUB1_PR_MAX','SUB1_PR_MIN','SUB1_CE_MAX','SUB1_CE_MIN','SUB1_TH_MRKS',
				'SUB1_PR_MRKS','SUB1_CE_MRKS','SUB1_TOT','SUB1_STATUS','SUB1_GRADE','SUB1_GRADE_POINTS','SUB1_CREDIT','SUB1_CREDIT_POINTS',
				'SUB1_GRACE','SUB1_ADDL_FIELD1','SUB1_ADDL_FIELD2','SUB2NM','SUB2','SUB2MAX','SUB2MIN','SUB2_TH_MAX','SUB2_TH_MIN','SUB2_PR_MAX',
				'SUB2_PR_MIN','SUB2_CE_MAX','SUB2_CE_MIN','SUB2_TH_MRKS','SUB2_PR_MRKS','SUB2_CE_MRKS','SUB2_TOT','SUB2_STATUS','SUB2_GRADE',
				'SUB2_GRADE_POINTS','SUB2_CREDIT','SUB2_CREDIT_POINTS','SUB2_GRACE','SUB2_ADDL_FIELD1','SUB2_ADDL_FIELD2','SUB3NM','SUB3',
				'SUB3MAX','SUB3MIN','SUB3_TH_MAX','SUB3_TH_MIN','SUB3_PR_MAX','SUB3_PR_MIN','SUB3_CE_MAX','SUB3_CE_MIN','SUB3_TH_MRKS',
				'SUB3_PR_MRKS','SUB3_CE_MRKS','SUB3_TOT','SUB3_STATUS','SUB3_GRADE','SUB3_GRADE_POINTS','SUB3_CREDIT','SUB3_CREDIT_POINTS',
				'SUB3_GRACE','SUB3_ADDL_FIELD1','SUB3_ADDL_FIELD2','SUB4NM','SUB4','SUB4MAX','SUB4MIN','SUB4_TH_MAX','SUB4_TH_MIN',
				'SUB4_PR_MAX','SUB4_PR_MIN','SUB4_CE_MAX','SUB4_CE_MIN','SUB4_TH_MRKS','SUB4_PR_MRKS','SUB4_CE_MRKS','SUB4_TOT','SUB4_STATUS',
				'SUB4_GRADE','SUB4_GRADE_POINTS','SUB4_CREDIT','SUB4_CREDIT_POINTS','SUB4_GRACE','SUB4_ADDL_FIELD1','SUB4_ADDL_FIELD2','SUB5NM',
				'SUB5','SUB5MAX','SUB5MIN','SUB5_TH_MAX','SUB5_TH_MIN','SUB5_PR_MAX','SUB5_PR_MIN','SUB5_CE_MAX','SUB5_CE_MIN','SUB5_TH_MRKS',
				'SUB5_PR_MRKS','SUB5_CE_MRKS','SUB5_TOT','SUB5_STATUS','SUB5_GRADE','SUB5_GRADE_POINTS','SUB5_CREDIT','SUB5_CREDIT_POINTS',
				'SUB5_GRACE','SUB5_ADDL_FIELD1','SUB5_ADDL_FIELD2','SUB6NM','SUB6','SUB6MAX','SUB6MIN','SUB6_TH_MAX','SUB6_TH_MIN','SUB6_PR_MAX',
				'SUB6_PR_MIN','SUB6_CE_MAX','SUB6_CE_MIN','SUB6_TH_MRKS','SUB6_PR_MRKS','SUB6_CE_MRKS','SUB6_TOT','SUB6_STATUS','SUB6_GRADE',
				'SUB6_GRADE_POINTS','SUB6_CREDIT','SUB6_CREDIT_POINTS','SUB6_GRACE','SUB6_ADDL_FIELD1','SUB6_ADDL_FIELD2','SUB7NM','SUB7',
				'SUB7MAX','SUB7MIN','SUB7_TH_MAX','SUB7_TH_MIN','SUB7_PR_MAX','SUB7_PR_MIN','SUB7_CE_MAX','SUB7_CE_MIN','SUB7_TH_MRKS',
				'SUB7_PR_MRKS','SUB7_CE_MRKS','SUB7_TOT','SUB7_STATUS','SUB7_GRADE','SUB7_GRADE_POINTS','SUB7_CREDIT','SUB7_CREDIT_POINTS',
				'SUB7_GRACE','SUB7_ADDL_FIELD1','SUB7_ADDL_FIELD2']; 
    }	  
	 
	 /* start */
		// $aadhar_number = "abc123de-444f456";
		// $res = $this->_getOnlyNumbers($aadhar_number);
		// print_r($res);
		// echo "<br>";

		// $str = "Rohit Jain Is my Name abc123de,444f456";
		// $res = $this->_removeSpecialChar($str);
		// print_r($res);
		// echo "<br>";

		// $str = "Rohit Jain 2 ]";
		// $res = $this->_removeSpecialCharOtherThenSpace($str);
		// print_r($res);
		// echo "<br>";
		// die;

	 /* end */
}