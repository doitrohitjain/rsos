<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use Config;
use App\Component\CustomComponent;
use App\Models\ExamcenterDetail;
use App\Models\Subject;
use App\Exports\CenterCountExlExport;
use App\Helper\CustomHelper;


class AicentersubjectCountExlExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
	
	function __construct($request) {
		$this->course = $request->course;
		$this->stream = $request->stream;
		$this->fresh_supp_option = $request->fresh_supp_option;
		$this->midium = $request->midium;
		$this->is_eligible = $request->is_eligible;
		$this->book_learning_type_id = $request->book_learning_type_id;
		
	 }
	 
	public function collection(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '0');
         
		 
		$examyear = CustomHelper::_get_selected_sessions();
		$course =  $this->course;
		$stream =  $this->stream;
		$fresh_supp_option =  $this->fresh_supp_option;
		$custom_component_obj = new CustomComponent;
	    $aiCenters = $custom_component_obj->getAiCenters(null,null,1);
	
		$extraConditions = null;
		$bothconditions = 1;
		if(!empty($this->midium)){
			$extraConditions .= " AND ap.medium = " . $this->midium;
		}
		if(!empty($this->book_learning_type_id)){
			$extraConditions .= " AND s.book_learning_type_id = " . $this->book_learning_type_id;
		}
		
		if(isset($this->is_eligible)){			
		  if($this->is_eligible == 1){
			$eligible = " AND s.is_eligible = " .$this->is_eligible;  
			$bothconditions = " AND s.is_eligible = " .$this->is_eligible; 
		  } 
		   elseif($this->is_eligible == 2){
			if($fresh_supp_option == 1){  
				$eligible = " AND ap.is_ready_for_verifying = 1";
								
			}else if($fresh_supp_option == 2){
				$eligible = "and s.fee_status = 1";
			}else{
				$eligible = " AND ap.is_ready_for_verifying = 1"; 
				$bothconditions = "and s.fee_status = 1";
			}
		  }else{
			$eligible = '';  
		  }
		}
	
		if($fresh_supp_option == 1){
			$output = array();
			if($course == 10){
				$query = "SELECT
				CONCAT(ad.ai_code,'-', college_name) AS college_name , ad.ai_code as  Ai_code,
				SUM(CASE WHEN es.subject_id=1 THEN 1 ELSE 0 END) as Sub_201,
				SUM(CASE WHEN es.subject_id=2 THEN 1 ELSE 0 END) as Sub_202,
				SUM(CASE WHEN es.subject_id=3 THEN 1 ELSE 0 END) as Sub_212,
				SUM(CASE WHEN es.subject_id=4 THEN 1 ELSE 0 END) as Sub_211,
				SUM(CASE WHEN es.subject_id=5 THEN 1 ELSE 0 END) as Sub_213,
				SUM(CASE WHEN es.subject_id=6 THEN 1 ELSE 0 END) as Sub_209,
				SUM(CASE WHEN es.subject_id=7 THEN 1 ELSE 0 END) as Sub_229,
				SUM(CASE WHEN es.subject_id=9 THEN 1 ELSE 0 END) as Sub_206,
				SUM(CASE WHEN es.subject_id=10 THEN 1 ELSE 0 END) as Sub_207,
				SUM(CASE WHEN es.subject_id=11 THEN 1 ELSE 0 END) as Sub_210,
				SUM(CASE WHEN es.subject_id=12 THEN 1 ELSE 0 END) as Sub_214,
				SUM(CASE WHEN es.subject_id=13 THEN 1 ELSE 0 END) as Sub_216,
				SUM(CASE WHEN es.subject_id=16 THEN 1 ELSE 0 END) as Sub_225,
				SUM(CASE WHEN es.subject_id=17 THEN 1 ELSE 0 END) as Sub_222,
				SUM(CASE WHEN es.subject_id=30 THEN 1 ELSE 0 END) as Sub_215,
				SUM(CASE WHEN es.subject_id=40 THEN 1 ELSE 0 END) as Sub_223,
				SUM(CASE WHEN es.subject_id=41 THEN 1 ELSE 0 END) as Sub_208
				  FROM rs_aicenter_details ad
					LEFT JOIN rs_students s ON s.ai_code = ad.ai_code and s.stream = '$stream' AND s.exam_year = '$examyear' AND s.course= '$course' and s.deleted_at  IS NULL  
					LEFT JOIN rs_applications ap ON ap.student_id = s.id
					LEFT JOIN rs_exam_subjects es ON es.student_id= s.id AND es.deleted_at IS null
					WHERE
					 ad.is_allow_for_admission = 1
					AND ad.active = 1
					" . $extraConditions ." 
					".$eligible."
					group by ad.ai_code 
					ORDER BY
					ad.ai_code ASC";
			
				$aicenterdatacode = DB::select($query);
				$i =1 ;
				foreach($aicenterdatacode as $data){
					$output[] = array(
						'id' => $i,
						'Ai_code' => @$data->Ai_code,
						'college_name' => @$data->college_name,
						'Sub_201' => @$data->Sub_201,
						'Sub_202' => @$data->Sub_202,
						'Sub_206' => @$data->Sub_206,
						'Sub_207' => @$data->Sub_207,
						'Sub_208' => @$data->Sub_208,
						'Sub_209' => @$data->Sub_209,
						'Sub_210' => @$data->Sub_210,
						'Sub_211' => @$data->Sub_211,
						'Sub_212' => @$data->Sub_212,
						'Sub_213' => @$data->Sub_213,
						'Sub_214' => @$data->Sub_214,
						'Sub_215' => @$data->Sub_215,
						'Sub_216' => @$data->Sub_216,
						'Sub_222' => @$data->Sub_222,
						'Sub_223' => @$data->Sub_223,
						'Sub_225' => @$data->Sub_225,
						'Sub_229' => @$data->Sub_229,					
					);
					$i++;
				}
			}elseif($course == 12){
				$query = "SELECT
					CONCAT(ad.ai_code,'-', college_name) AS college_name , ad.ai_code as  Ai_code,
					SUM(CASE WHEN es.subject_id=18 THEN 1 ELSE 0 END) as Sub_301,
					SUM(CASE WHEN es.subject_id=19 THEN 1 ELSE 0 END) as Sub_302,
					SUM(CASE WHEN es.subject_id=20 THEN 1 ELSE 0 END) as Sub_306,
					SUM(CASE WHEN es.subject_id=21 THEN 1 ELSE 0 END) as Sub_309,
					SUM(CASE WHEN es.subject_id=22 THEN 1 ELSE 0 END) as Sub_311,
					SUM(CASE WHEN es.subject_id=23 THEN 1 ELSE 0 END) as Sub_312,
					SUM(CASE WHEN es.subject_id=24 THEN 1 ELSE 0 END) as Sub_313,
					SUM(CASE WHEN es.subject_id=25 THEN 1 ELSE 0 END) as Sub_314,
					SUM(CASE WHEN es.subject_id=26 THEN 1 ELSE 0 END) as Sub_315,
					SUM(CASE WHEN es.subject_id=27 THEN 1 ELSE 0 END) as Sub_317,
					SUM(CASE WHEN es.subject_id=28 THEN 1 ELSE 0 END) as Sub_316,
					SUM(CASE WHEN es.subject_id=29 THEN 1 ELSE 0 END) as Sub_318,
					SUM(CASE WHEN es.subject_id=31 THEN 1 ELSE 0 END) as Sub_319,
					SUM(CASE WHEN es.subject_id=32 THEN 1 ELSE 0 END) as Sub_320,
					SUM(CASE WHEN es.subject_id=33 THEN 1 ELSE 0 END) as Sub_321,
					SUM(CASE WHEN es.subject_id=34 THEN 1 ELSE 0 END) as Sub_328,
					SUM(CASE WHEN es.subject_id=35 THEN 1 ELSE 0 END) as Sub_330,
					SUM(CASE WHEN es.subject_id=36 THEN 1 ELSE 0 END) as Sub_331,
					SUM(CASE WHEN es.subject_id=37 THEN 1 ELSE 0 END) as Sub_332,
					SUM(CASE WHEN es.subject_id=38 THEN 1 ELSE 0 END) as Sub_333,
					SUM(CASE WHEN es.subject_id=39 THEN 1 ELSE 0 END) as Sub_336,
					SUM(CASE WHEN es.subject_id=52 THEN 1 ELSE 0 END) as Sub_391,
					SUM(CASE WHEN es.subject_id=51 THEN 1 ELSE 0 END) as Sub_392,
					SUM(CASE WHEN es.subject_id=53 THEN 1 ELSE 0 END) as Sub_393,
					SUM(CASE WHEN es.subject_id=46 THEN 1 ELSE 0 END) as Sub_394,
					SUM(CASE WHEN es.subject_id=47 THEN 1 ELSE 0 END) as Sub_395,
					SUM(CASE WHEN es.subject_id=48 THEN 1 ELSE 0 END) as Sub_396
					FROM rs_aicenter_details ad
						LEFT JOIN  rs_students s ON s.ai_code = ad.ai_code AND s.deleted_at  IS NULL and s.stream = '$stream' AND s.exam_year = '$examyear' AND s.course= '$course' 

						LEFT JOIN rs_applications ap ON ap.student_id = s.id
						LEFT JOIN rs_exam_subjects es ON es.student_id= s.id AND es.deleted_at IS null
						WHERE
						ad.is_allow_for_admission = 1
					    AND ad.active = 1
						" . $extraConditions ." 
						".$eligible."
						group by ad.ai_code 
						ORDER BY
						ad.ai_code ASC"; 
				 
				$aicenterdatacode = DB::select($query);
				
				$i =1 ;
				foreach(@$aicenterdatacode as $data){
					$output[] = array(
						'id' => $i,
						'Ai_code' => @$data->Ai_code,
						'college_name' => @$data->college_name,
						'Sub_301' => @$data->Sub_301,
						'Sub_302' => @$data->Sub_302,
						'Sub_306' => @$data->Sub_306,
						'Sub_309' => @$data->Sub_309,
						'Sub_311' => @$data->Sub_311,
						'Sub_312' => @$data->Sub_312,
						'Sub_313' => @$data->Sub_313,
						'Sub_314' => @$data->Sub_314,
						'Sub_315' => @$data->Sub_315,
						'Sub_316' => @$data->Sub_316,
						'Sub_317' => @$data->Sub_317,
						'Sub_318' => @$data->Sub_318,
						'Sub_319' => @$data->Sub_319,
						'Sub_320' => @$data->Sub_320,
						'Sub_321' => @$data->Sub_321,
						'Sub_328' => @$data->Sub_328,
						'Sub_330' => @$data->Sub_330,
						'Sub_331' => @$data->Sub_331,
						'Sub_332' => @$data->Sub_332,
						'Sub_333' => @$data->Sub_333,
						'Sub_336' => @$data->Sub_336,
						'Sub_391' => @$data->Sub_391,
						'Sub_392' => @$data->Sub_392,
						'Sub_393' => @$data->Sub_393,
						'Sub_394' => @$data->Sub_394,
						'Sub_395' => @$data->Sub_395,
						'Sub_396' => @$data->Sub_396,
						
						
					);
					$i++;
				}
			}
		}else if($fresh_supp_option == 2){
			$output = array();
			if($course == 10){
				$query = "SELECT
				CONCAT(ad.ai_code,'-', college_name) AS college_name , ad.ai_code as  Ai_code,
				SUM(CASE WHEN ss.subject_id=1 THEN 1 ELSE 0 END) as Sub_201,
				SUM(CASE WHEN ss.subject_id=2 THEN 1 ELSE 0 END) as Sub_202,
				SUM(CASE WHEN ss.subject_id=3 THEN 1 ELSE 0 END) as Sub_212,
				SUM(CASE WHEN ss.subject_id=4 THEN 1 ELSE 0 END) as Sub_211,
				SUM(CASE WHEN ss.subject_id=5 THEN 1 ELSE 0 END) as Sub_213,
				SUM(CASE WHEN ss.subject_id=6 THEN 1 ELSE 0 END) as Sub_209,
				SUM(CASE WHEN ss.subject_id=7 THEN 1 ELSE 0 END) as Sub_229,
				SUM(CASE WHEN ss.subject_id=9 THEN 1 ELSE 0 END) as Sub_206,
				SUM(CASE WHEN ss.subject_id=10 THEN 1 ELSE 0 END) as Sub_207,
				SUM(CASE WHEN ss.subject_id=11 THEN 1 ELSE 0 END) as Sub_210,
				SUM(CASE WHEN ss.subject_id=12 THEN 1 ELSE 0 END) as Sub_214,
				SUM(CASE WHEN ss.subject_id=13 THEN 1 ELSE 0 END) as Sub_216,
				SUM(CASE WHEN ss.subject_id=16 THEN 1 ELSE 0 END) as Sub_225,
				SUM(CASE WHEN ss.subject_id=17 THEN 1 ELSE 0 END) as Sub_222,
				SUM(CASE WHEN ss.subject_id=30 THEN 1 ELSE 0 END) as Sub_215,
				SUM(CASE WHEN ss.subject_id=40 THEN 1 ELSE 0 END) as Sub_223,
				SUM(CASE WHEN ss.subject_id=41 THEN 1 ELSE 0 END) as Sub_208
				FROM rs_aicenter_details ad
				LEFT JOIN rs_supplementaries s ON ad.ai_code = s.ai_code and s.exam_month = '$stream' AND s.exam_year = '$examyear'	AND s.course= '$course' AND s.deleted_at  IS NULL".$eligible	."
				LEFT JOIN rs_applications ap ON ap.student_id = s.student_id
				LEFT JOIN rs_supplementary_subjects ss ON ss.supplementary_id= s.id AND ss.deleted_at IS null
				WHERE ad.is_allow_for_admission = 1
					AND ad.active = 1
					 " . $extraConditions ." 
					group by ad.ai_code 
					ORDER BY
					ad.ai_code ASC";
				
				$aicenterdatacode = DB::select($query);
				$i =1 ;
				foreach($aicenterdatacode as $data){
					$output[] = array(
						'id' => $i,
						'Ai_code' => @$data->Ai_code,
						'college_name' => @$data->college_name,
						'Sub_201' => @$data->Sub_201,
						'Sub_202' => @$data->Sub_202,
						'Sub_206' => @$data->Sub_206,
						'Sub_207' => @$data->Sub_207,
						'Sub_208' => @$data->Sub_208,
						'Sub_209' => @$data->Sub_209,
						'Sub_210' => @$data->Sub_210,
						'Sub_211' => @$data->Sub_211,
						'Sub_212' => @$data->Sub_212,
						'Sub_213' => @$data->Sub_213,
						'Sub_214' => @$data->Sub_214,
						'Sub_215' => @$data->Sub_215,
						'Sub_216' => @$data->Sub_216,
						'Sub_222' => @$data->Sub_222,
						'Sub_223' => @$data->Sub_223,
						'Sub_225' => @$data->Sub_225,
						'Sub_229' => @$data->Sub_229,					
					);
					$i++;
				}
			}elseif($course == 12){
				$query = "SELECT
					CONCAT(ad.ai_code,'-', college_name) AS college_name , ad.ai_code as  Ai_code,
					SUM(CASE WHEN ss.subject_id=18 THEN 1 ELSE 0 END) as Sub_301,
					SUM(CASE WHEN ss.subject_id=19 THEN 1 ELSE 0 END) as Sub_302,
					SUM(CASE WHEN ss.subject_id=20 THEN 1 ELSE 0 END) as Sub_306,
					SUM(CASE WHEN ss.subject_id=21 THEN 1 ELSE 0 END) as Sub_309,
					SUM(CASE WHEN ss.subject_id=22 THEN 1 ELSE 0 END) as Sub_311,
					SUM(CASE WHEN ss.subject_id=23 THEN 1 ELSE 0 END) as Sub_312,
					SUM(CASE WHEN ss.subject_id=24 THEN 1 ELSE 0 END) as Sub_313,
					SUM(CASE WHEN ss.subject_id=25 THEN 1 ELSE 0 END) as Sub_314,
					SUM(CASE WHEN ss.subject_id=26 THEN 1 ELSE 0 END) as Sub_315,
					SUM(CASE WHEN ss.subject_id=27 THEN 1 ELSE 0 END) as Sub_317,
					SUM(CASE WHEN ss.subject_id=28 THEN 1 ELSE 0 END) as Sub_316,
					SUM(CASE WHEN ss.subject_id=29 THEN 1 ELSE 0 END) as Sub_318,
					SUM(CASE WHEN ss.subject_id=31 THEN 1 ELSE 0 END) as Sub_319,
					SUM(CASE WHEN ss.subject_id=32 THEN 1 ELSE 0 END) as Sub_320,
					SUM(CASE WHEN ss.subject_id=33 THEN 1 ELSE 0 END) as Sub_321,
					SUM(CASE WHEN ss.subject_id=34 THEN 1 ELSE 0 END) as Sub_328,
					SUM(CASE WHEN ss.subject_id=35 THEN 1 ELSE 0 END) as Sub_330,
					SUM(CASE WHEN ss.subject_id=36 THEN 1 ELSE 0 END) as Sub_331,
					SUM(CASE WHEN ss.subject_id=37 THEN 1 ELSE 0 END) as Sub_332,
					SUM(CASE WHEN ss.subject_id=38 THEN 1 ELSE 0 END) as Sub_333,
					SUM(CASE WHEN ss.subject_id=39 THEN 1 ELSE 0 END) as Sub_336,
					SUM(CASE WHEN ss.subject_id=52 THEN 1 ELSE 0 END) as Sub_391,
					SUM(CASE WHEN ss.subject_id=51 THEN 1 ELSE 0 END) as Sub_392,
					SUM(CASE WHEN ss.subject_id=53 THEN 1 ELSE 0 END) as Sub_393,
					SUM(CASE WHEN ss.subject_id=46 THEN 1 ELSE 0 END) as Sub_394,
					SUM(CASE WHEN ss.subject_id=47 THEN 1 ELSE 0 END) as Sub_395,
					SUM(CASE WHEN ss.subject_id=48 THEN 1 ELSE 0 END) as Sub_396
					FROM rs_aicenter_details ad
				LEFT JOIN rs_supplementaries s ON ad.ai_code = s.ai_code and s.exam_month = '$stream' AND s.exam_year = '$examyear'			AND s.course= '$course' AND s.deleted_at  IS NULL  ".$eligible	."
				LEFT JOIN rs_applications ap ON ap.student_id = s.student_id
				LEFT JOIN rs_supplementary_subjects ss ON ss.supplementary_id= s.id AND ss.deleted_at IS null
				WHERE
					
					 ad.is_allow_for_admission = 1
					AND ad.active = 1
					" . $extraConditions ."
					group by ad.ai_code 
					ORDER BY
					ad.ai_code ASC";
				
				//print_r($query);die;
				$aicenterdatacode = DB::select($query);
				
				$i =1 ;
				foreach(@$aicenterdatacode as $data){
					$output[] = array(
						'id' => $i,
						'Ai_code' => @$data->Ai_code,
						'college_name' => @$data->college_name,
						'Sub_301' => @$data->Sub_301,
						'Sub_302' => @$data->Sub_302,
						'Sub_306' => @$data->Sub_306,
						'Sub_309' => @$data->Sub_309,
						'Sub_311' => @$data->Sub_311,
						'Sub_312' => @$data->Sub_312,
						'Sub_313' => @$data->Sub_313,
						'Sub_314' => @$data->Sub_314,
						'Sub_315' => @$data->Sub_315,
						'Sub_316' => @$data->Sub_316,
						'Sub_317' => @$data->Sub_317,
						'Sub_318' => @$data->Sub_318,
						'Sub_319' => @$data->Sub_319,
						'Sub_320' => @$data->Sub_320,
						'Sub_321' => @$data->Sub_321,
						'Sub_328' => @$data->Sub_328,
						'Sub_330' => @$data->Sub_330,
						'Sub_331' => @$data->Sub_331,
						'Sub_332' => @$data->Sub_332,
						'Sub_333' => @$data->Sub_333,
						'Sub_336' => @$data->Sub_336,
						'Sub_391' => @$data->Sub_391,
						'Sub_392' => @$data->Sub_392,
						'Sub_393' => @$data->Sub_393,
						'Sub_394' => @$data->Sub_394,
						'Sub_395' => @$data->Sub_395,
						'Sub_396' => @$data->Sub_396,
						
						
						
					);
					$i++;
				}
			}
		}else if($fresh_supp_option == 3){
			$output = array();
			if($course == 10){
				$aicenter_stream_subject_data = DB::select("SELECT
				CONCAT(ad.ai_code,'-', college_name) AS college_name , ad.ai_code as  Ai_code,
				SUM(CASE WHEN es.subject_id=1 THEN 1 ELSE 0 END) as Sub_201,
				SUM(CASE WHEN es.subject_id=2 THEN 1 ELSE 0 END) as Sub_202,
				SUM(CASE WHEN es.subject_id=3 THEN 1 ELSE 0 END) as Sub_212,
				SUM(CASE WHEN es.subject_id=4 THEN 1 ELSE 0 END) as Sub_211,
				SUM(CASE WHEN es.subject_id=5 THEN 1 ELSE 0 END) as Sub_213,
				SUM(CASE WHEN es.subject_id=6 THEN 1 ELSE 0 END) as Sub_209,
				SUM(CASE WHEN es.subject_id=7 THEN 1 ELSE 0 END) as Sub_229,
				SUM(CASE WHEN es.subject_id=9 THEN 1 ELSE 0 END) as Sub_206,
				SUM(CASE WHEN es.subject_id=10 THEN 1 ELSE 0 END) as Sub_207,
				SUM(CASE WHEN es.subject_id=11 THEN 1 ELSE 0 END) as Sub_210,
				SUM(CASE WHEN es.subject_id=12 THEN 1 ELSE 0 END) as Sub_214,
				SUM(CASE WHEN es.subject_id=13 THEN 1 ELSE 0 END) as Sub_216,
				SUM(CASE WHEN es.subject_id=16 THEN 1 ELSE 0 END) as Sub_225,
				SUM(CASE WHEN es.subject_id=17 THEN 1 ELSE 0 END) as Sub_222,
				SUM(CASE WHEN es.subject_id=30 THEN 1 ELSE 0 END) as Sub_215,
				SUM(CASE WHEN es.subject_id=40 THEN 1 ELSE 0 END) as Sub_223,
				SUM(CASE WHEN es.subject_id=41 THEN 1 ELSE 0 END) as Sub_208
				  FROM rs_aicenter_details ad
					LEFT JOIN rs_students s ON s.ai_code = ad.ai_code 
					LEFT JOIN rs_applications ap ON ap.student_id = s.id
					LEFT JOIN rs_exam_subjects es ON es.student_id= s.id AND es.deleted_at IS null
					WHERE
					 ad.is_allow_for_admission = 1
					AND ad.active = 1
					and s.stream = '$stream' 
					AND s.exam_year = '$examyear' 
					AND s.course= '$course'
					and s.deleted_at  IS NULL 
					" . $extraConditions ."  
					".$eligible."
					group by ad.ai_code 
					ORDER BY
					ad.ai_code ASC");
				
				$fresh_array = array();
				foreach($aicenter_stream_subject_data as $akey=>$aicenter_subject){
					if(!empty($aicenter_subject->Ai_code)){
						$fresh_array[$aicenter_subject->Ai_code] = $aicenter_subject;
					}
				}
					
				$aicenter_supp_subject_data = DB::select("SELECT
				CONCAT(ad.ai_code,'-', college_name) AS college_name , ad.ai_code as  Ai_code,
				SUM(CASE WHEN ss.subject_id=1 THEN 1 ELSE 0 END) as Sub_201,
				SUM(CASE WHEN ss.subject_id=2 THEN 1 ELSE 0 END) as Sub_202,
				SUM(CASE WHEN ss.subject_id=3 THEN 1 ELSE 0 END) as Sub_212,
				SUM(CASE WHEN ss.subject_id=4 THEN 1 ELSE 0 END) as Sub_211,
				SUM(CASE WHEN ss.subject_id=5 THEN 1 ELSE 0 END) as Sub_213,
				SUM(CASE WHEN ss.subject_id=6 THEN 1 ELSE 0 END) as Sub_209,
				SUM(CASE WHEN ss.subject_id=7 THEN 1 ELSE 0 END) as Sub_229,
				SUM(CASE WHEN ss.subject_id=9 THEN 1 ELSE 0 END) as Sub_206,
				SUM(CASE WHEN ss.subject_id=10 THEN 1 ELSE 0 END) as Sub_207,
				SUM(CASE WHEN ss.subject_id=11 THEN 1 ELSE 0 END) as Sub_210,
				SUM(CASE WHEN ss.subject_id=12 THEN 1 ELSE 0 END) as Sub_214,
				SUM(CASE WHEN ss.subject_id=13 THEN 1 ELSE 0 END) as Sub_216,
				SUM(CASE WHEN ss.subject_id=16 THEN 1 ELSE 0 END) as Sub_225,
				SUM(CASE WHEN ss.subject_id=17 THEN 1 ELSE 0 END) as Sub_222,
				SUM(CASE WHEN ss.subject_id=30 THEN 1 ELSE 0 END) as Sub_215,
				SUM(CASE WHEN ss.subject_id=40 THEN 1 ELSE 0 END) as Sub_223,
				SUM(CASE WHEN ss.subject_id=41 THEN 1 ELSE 0 END) as Sub_208
				FROM rs_aicenter_details ad
				LEFT JOIN rs_supplementaries s ON ad.ai_code = s.ai_code 
				LEFT JOIN rs_applications ap ON ap.student_id = s.student_id
				LEFT JOIN rs_supplementary_subjects ss ON ss.supplementary_id= s.id AND ss.deleted_at IS null
				WHERE
					
					 ad.is_allow_for_admission = 1
					AND ad.active = 1
					and s.exam_month = '$stream' 
					AND s.exam_year = '$examyear'			
					AND s.course= '$course' 
					AND s.deleted_at  IS NULL 
					". $extraConditions ." 
					".$eligible	."
					group by ad.ai_code 
					ORDER BY
					ad.ai_code ASC");
					
				$supp_array = array();
				foreach($aicenter_supp_subject_data as $dkey=>$aicenter_supp_subject){
					if(!empty($aicenter_supp_subject->Ai_code)){
						$supp_array[$aicenter_supp_subject->Ai_code] = $aicenter_supp_subject;
					}
				}
				
				// echo "<pre>"; print_r($fresh_array); echo "</pre>";
				// echo "<pre>"; print_r($supp_array); echo "</pre>";
				// die;
				
				$i =1 ;
				foreach($aiCenters as $ai_code =>$ai_name){
					$output[] = array(
						'id' => $i,
						'Ai_code' => @$ai_code,
						'college_name' => @$ai_name,
						
						'Sub_201' => @$fresh_array[$ai_code]->Sub_201 + @$supp_array[$ai_code]->Sub_201,
						'Sub_202' => @$fresh_array[$ai_code]->Sub_202 + @$supp_array[$ai_code]->Sub_202,
						'Sub_206' => @$fresh_array[$ai_code]->Sub_206 + @$supp_array[$ai_code]->Sub_206,
						'Sub_207' => @$fresh_array[$ai_code]->Sub_207 + @$supp_array[$ai_code]->Sub_207,
						'Sub_208' => @$fresh_array[$ai_code]->Sub_208 + @$supp_array[$ai_code]->Sub_208,
						'Sub_209' => @$fresh_array[$ai_code]->Sub_209 + @$supp_array[$ai_code]->Sub_209,
						'Sub_210' => @$fresh_array[$ai_code]->Sub_210 + @$supp_array[$ai_code]->Sub_210,
						'Sub_211' => @$fresh_array[$ai_code]->Sub_211 + @$supp_array[$ai_code]->Sub_211,
						'Sub_212' => @$fresh_array[$ai_code]->Sub_212 + @$supp_array[$ai_code]->Sub_212,
						'Sub_213' => @$fresh_array[$ai_code]->Sub_213 + @$supp_array[$ai_code]->Sub_213,
						'Sub_214' => @$fresh_array[$ai_code]->Sub_214 + @$supp_array[$ai_code]->Sub_214,
						'Sub_215' => @$fresh_array[$ai_code]->Sub_215 + @$supp_array[$ai_code]->Sub_215,
						'Sub_216' => @$fresh_array[$ai_code]->Sub_216 + @$supp_array[$ai_code]->Sub_216,
						'Sub_222' => @$fresh_array[$ai_code]->Sub_222 + @$supp_array[$ai_code]->Sub_222,
						'Sub_223' => @$fresh_array[$ai_code]->Sub_223 + @$supp_array[$ai_code]->Sub_223,
						'Sub_225' => @$fresh_array[$ai_code]->Sub_225 + @$supp_array[$ai_code]->Sub_225,
						'Sub_229' => @$fresh_array[$ai_code]->Sub_229 + @$supp_array[$ai_code]->Sub_229,
					);
					$i++;
				}
				
			}elseif($course == 12){
				
					
				$aicenter_stream_subject_data = DB::select("SELECT
					CONCAT(ad.ai_code,'-', college_name) AS college_name , ad.ai_code as  Ai_code,
					SUM(CASE WHEN es.subject_id=18 THEN 1 ELSE 0 END) as Sub_301,
					SUM(CASE WHEN es.subject_id=19 THEN 1 ELSE 0 END) as Sub_302,
					SUM(CASE WHEN es.subject_id=20 THEN 1 ELSE 0 END) as Sub_306,
					SUM(CASE WHEN es.subject_id=21 THEN 1 ELSE 0 END) as Sub_309,
					SUM(CASE WHEN es.subject_id=22 THEN 1 ELSE 0 END) as Sub_311,
					SUM(CASE WHEN es.subject_id=23 THEN 1 ELSE 0 END) as Sub_312,
					SUM(CASE WHEN es.subject_id=24 THEN 1 ELSE 0 END) as Sub_313,
					SUM(CASE WHEN es.subject_id=25 THEN 1 ELSE 0 END) as Sub_314,
					SUM(CASE WHEN es.subject_id=26 THEN 1 ELSE 0 END) as Sub_315,
					SUM(CASE WHEN es.subject_id=27 THEN 1 ELSE 0 END) as Sub_317,
					SUM(CASE WHEN es.subject_id=28 THEN 1 ELSE 0 END) as Sub_316,
					SUM(CASE WHEN es.subject_id=29 THEN 1 ELSE 0 END) as Sub_318,
					SUM(CASE WHEN es.subject_id=31 THEN 1 ELSE 0 END) as Sub_319,
					SUM(CASE WHEN es.subject_id=32 THEN 1 ELSE 0 END) as Sub_320,
					SUM(CASE WHEN es.subject_id=33 THEN 1 ELSE 0 END) as Sub_321,
					SUM(CASE WHEN es.subject_id=34 THEN 1 ELSE 0 END) as Sub_328,
					SUM(CASE WHEN es.subject_id=35 THEN 1 ELSE 0 END) as Sub_330,
					SUM(CASE WHEN es.subject_id=36 THEN 1 ELSE 0 END) as Sub_331,
					SUM(CASE WHEN es.subject_id=37 THEN 1 ELSE 0 END) as Sub_332,
					SUM(CASE WHEN es.subject_id=38 THEN 1 ELSE 0 END) as Sub_333,
					SUM(CASE WHEN es.subject_id=39 THEN 1 ELSE 0 END) as Sub_336,
					SUM(CASE WHEN es.subject_id=52 THEN 1 ELSE 0 END) as Sub_391,
					SUM(CASE WHEN es.subject_id=51 THEN 1 ELSE 0 END) as Sub_392,
					SUM(CASE WHEN es.subject_id=53 THEN 1 ELSE 0 END) as Sub_393,
					SUM(CASE WHEN es.subject_id=46 THEN 1 ELSE 0 END) as Sub_394,
					SUM(CASE WHEN es.subject_id=47 THEN 1 ELSE 0 END) as Sub_395,
					SUM(CASE WHEN es.subject_id=48 THEN 1 ELSE 0 END) as Sub_396
					FROM rs_aicenter_details ad
						LEFT JOIN  rs_students s ON s.ai_code = ad.ai_code 

						LEFT JOIN rs_applications ap ON ap.student_id = s.id
						LEFT JOIN rs_exam_subjects es ON es.student_id= s.id AND es.deleted_at IS null
						WHERE
						ad.is_allow_for_admission = 1
					    AND ad.active = 1
						AND s.deleted_at  IS NULL 
						and s.stream = '$stream' 
						AND s.exam_year = '$examyear' 
						AND s.course= '$course' 
						" . $extraConditions ."
						".$eligible."
						group by ad.ai_code 
						ORDER BY
						ad.ai_code ASC");
				
					
				$fresh_array = array();
				foreach($aicenter_stream_subject_data as $akey=>$aicenter_subject){
					if(!empty($aicenter_subject->Ai_code)){
						$fresh_array[$aicenter_subject->Ai_code] = $aicenter_subject;
					}
				}
					
				$aicenter_supp_subject_data = DB::select("SELECT
					CONCAT(ad.ai_code,'-', college_name) AS college_name , ad.ai_code as  Ai_code,
					SUM(CASE WHEN ss.subject_id=18 THEN 1 ELSE 0 END) as Sub_301,
					SUM(CASE WHEN ss.subject_id=19 THEN 1 ELSE 0 END) as Sub_302,
					SUM(CASE WHEN ss.subject_id=20 THEN 1 ELSE 0 END) as Sub_306,
					SUM(CASE WHEN ss.subject_id=21 THEN 1 ELSE 0 END) as Sub_309,
					SUM(CASE WHEN ss.subject_id=22 THEN 1 ELSE 0 END) as Sub_311,
					SUM(CASE WHEN ss.subject_id=23 THEN 1 ELSE 0 END) as Sub_312,
					SUM(CASE WHEN ss.subject_id=24 THEN 1 ELSE 0 END) as Sub_313,
					SUM(CASE WHEN ss.subject_id=25 THEN 1 ELSE 0 END) as Sub_314,
					SUM(CASE WHEN ss.subject_id=26 THEN 1 ELSE 0 END) as Sub_315,
					SUM(CASE WHEN ss.subject_id=27 THEN 1 ELSE 0 END) as Sub_317,
					SUM(CASE WHEN ss.subject_id=28 THEN 1 ELSE 0 END) as Sub_316,
					SUM(CASE WHEN ss.subject_id=29 THEN 1 ELSE 0 END) as Sub_318,
					SUM(CASE WHEN ss.subject_id=31 THEN 1 ELSE 0 END) as Sub_319,
					SUM(CASE WHEN ss.subject_id=32 THEN 1 ELSE 0 END) as Sub_320,
					SUM(CASE WHEN ss.subject_id=33 THEN 1 ELSE 0 END) as Sub_321,
					SUM(CASE WHEN ss.subject_id=34 THEN 1 ELSE 0 END) as Sub_328,
					SUM(CASE WHEN ss.subject_id=35 THEN 1 ELSE 0 END) as Sub_330,
					SUM(CASE WHEN ss.subject_id=36 THEN 1 ELSE 0 END) as Sub_331,
					SUM(CASE WHEN ss.subject_id=37 THEN 1 ELSE 0 END) as Sub_332,
					SUM(CASE WHEN ss.subject_id=38 THEN 1 ELSE 0 END) as Sub_333,
					SUM(CASE WHEN ss.subject_id=39 THEN 1 ELSE 0 END) as Sub_336,
					SUM(CASE WHEN ss.subject_id=52 THEN 1 ELSE 0 END) as Sub_391,
					SUM(CASE WHEN ss.subject_id=51 THEN 1 ELSE 0 END) as Sub_392,
					SUM(CASE WHEN ss.subject_id=53 THEN 1 ELSE 0 END) as Sub_393,
					SUM(CASE WHEN ss.subject_id=46 THEN 1 ELSE 0 END) as Sub_394,
					SUM(CASE WHEN ss.subject_id=47 THEN 1 ELSE 0 END) as Sub_395,
					SUM(CASE WHEN ss.subject_id=48 THEN 1 ELSE 0 END) as Sub_396
					FROM rs_aicenter_details ad
				LEFT JOIN rs_supplementaries s ON ad.ai_code = s.ai_code 
				LEFT JOIN rs_applications ap ON ap.student_id = s.student_id
				LEFT JOIN rs_supplementary_subjects ss ON ss.supplementary_id= s.id AND ss.deleted_at IS null
				WHERE
					
					 ad.is_allow_for_admission = 1
					AND ad.active = 1
					and s.exam_month = '$stream' 
					AND s.exam_year = '$examyear'			
					AND s.course= '$course' 
					AND s.deleted_at  IS NULL 
					" . $extraConditions ." 
					".$eligible	."
					group by ad.ai_code 
					ORDER BY
					ad.ai_code ASC");
					
				$supp_array = array();
				foreach($aicenter_supp_subject_data as $dkey=>$aicenter_supp_subject){
					if(!empty($aicenter_supp_subject->Ai_code)){
						$supp_array[$aicenter_supp_subject->Ai_code] = $aicenter_supp_subject;
					}
				}
				
				$i =1 ;
				foreach($aiCenters as $ai_code =>$ai_name){
					$output[] = array(
						'id' => $i,
						'Ai_code' => @$ai_code,
						'college_name' => @$ai_name,
						
						'Sub_301' => @$fresh_array[$ai_code]->Sub_301 + @$supp_array[$ai_code]->Sub_301,
						'Sub_302' => @$fresh_array[$ai_code]->Sub_302 + @$supp_array[$ai_code]->Sub_302,
						'Sub_306' => @$fresh_array[$ai_code]->Sub_306 + @$supp_array[$ai_code]->Sub_306,
						'Sub_309' => @$fresh_array[$ai_code]->Sub_309 + @$supp_array[$ai_code]->Sub_309,
						'Sub_311' => @$fresh_array[$ai_code]->Sub_311 + @$supp_array[$ai_code]->Sub_311,
						'Sub_312' => @$fresh_array[$ai_code]->Sub_312 + @$supp_array[$ai_code]->Sub_312,
						'Sub_313' => @$fresh_array[$ai_code]->Sub_313 + @$supp_array[$ai_code]->Sub_313,
						'Sub_314' => @$fresh_array[$ai_code]->Sub_314 + @$supp_array[$ai_code]->Sub_314,
						'Sub_315' => @$fresh_array[$ai_code]->Sub_315 + @$supp_array[$ai_code]->Sub_315,
						'Sub_316' => @$fresh_array[$ai_code]->Sub_316 + @$supp_array[$ai_code]->Sub_316,
						'Sub_317' => @$fresh_array[$ai_code]->Sub_317 + @$supp_array[$ai_code]->Sub_317,
						'Sub_318' => @$fresh_array[$ai_code]->Sub_318 + @$supp_array[$ai_code]->Sub_318,
						'Sub_319' => @$fresh_array[$ai_code]->Sub_319 + @$supp_array[$ai_code]->Sub_319,
						'Sub_320' => @$fresh_array[$ai_code]->Sub_320 + @$supp_array[$ai_code]->Sub_320,
						'Sub_321' => @$fresh_array[$ai_code]->Sub_321 + @$supp_array[$ai_code]->Sub_321,
						'Sub_328' => @$fresh_array[$ai_code]->Sub_328 + @$supp_array[$ai_code]->Sub_328,
						'Sub_330' => @$fresh_array[$ai_code]->Sub_330 + @$supp_array[$ai_code]->Sub_330,
						'Sub_331' => @$fresh_array[$ai_code]->Sub_331 + @$supp_array[$ai_code]->Sub_331,
						'Sub_332' => @$fresh_array[$ai_code]->Sub_332 + @$supp_array[$ai_code]->Sub_332,
						'Sub_333' => @$fresh_array[$ai_code]->Sub_333 + @$supp_array[$ai_code]->Sub_333,
						'Sub_336' => @$fresh_array[$ai_code]->Sub_336 + @$supp_array[$ai_code]->Sub_336,
						'Sub_391' => @$fresh_array[$ai_code]->Sub_391 + @$supp_array[$ai_code]->Sub_391,
						'Sub_392' => @$fresh_array[$ai_code]->Sub_392 + @$supp_array[$ai_code]->Sub_392,
						'Sub_393' => @$fresh_array[$ai_code]->Sub_393 + @$supp_array[$ai_code]->Sub_393,
						'Sub_394' => @$fresh_array[$ai_code]->Sub_394 + @$supp_array[$ai_code]->Sub_394,
						'Sub_395' => @$fresh_array[$ai_code]->Sub_395 + @$supp_array[$ai_code]->Sub_395,
						'Sub_396' => @$fresh_array[$ai_code]->Sub_396 + @$supp_array[$ai_code]->Sub_396,
						
						
					);
					$i++;
				}
			}
		} 
		return collect($output);
	}
	
	public function headings(): array{

		if($this->course  == 10){
		 return ["Sr. No.","Ai_Code","Ai Center Name","Sub_201","Sub_202","Sub_206","Sub_207","Sub_208","Sub_209","Sub_210","Sub_211","Sub_212","Sub_213","Sub_214","Sub_215","Sub_216","Sub_222","Sub_223","Sub_225","Sub_229"];	
		}
		elseif($this->course  == 12){
		return ["Sr. No.","Ai_Code","Ai_Center_Name","Sub_301","Sub_302","Sub_306","Sub_309","Sub_311","Sub_312","Sub_313","Sub_314","Sub_315","Sub_316","Sub_317","Sub_318","Sub_319","Sub_320","Sub_321","Sub_328","Sub_330","Sub_331","Sub_332","Sub_333","Sub_336","Sub_391","Sub_392","Sub_393","Sub_394","Sub_395","Sub_396"];
		}
	}
}