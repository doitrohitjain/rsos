<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Session;
use Config;
use App\Component\CustomComponent;
use App\Models\ExamcenterDetail;
use App\Models\Subject;
use App\Exports\CenterCountExlExport;
use App\Helper\CustomHelper;

class CenterCountExlExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
	
	
	function __construct($request) {
		//$this->course = $request->course;
		$this->stream = $request->stream;
		$this->course = $request->course;
	 }
	public function collection(){
		ini_set('memory_limit', '3000M');
		ini_set('max_execution_time', '0');

		$examyear = CustomHelper::_get_selected_sessions();
		$stream = $this->stream;
		$course = $this->course;
		$centercode = 'ecenter'.$course;
		
		$custom_component_obj = new CustomComponent;
		 
		$examcenterRecord = $custom_component_obj->_getExamCenterCodeForSummary($centercode,$course);
		
		if($course == 10) {
			$aicenter_stream_subject_data = DB::select("SELECT ed.cent_name as cent_name,ed.id as Center_code,
				SUM(CASE WHEN es.subject_id=1 THEN 1 ELSE 0 END) as Sub_201,
				SUM(CASE WHEN es.subject_id=2 THEN 1 ELSE 0 END) as Sub_202, 
				SUM(CASE WHEN es.subject_id=9 THEN 1 ELSE 0 END) as Sub_206, 
				SUM(CASE WHEN es.subject_id=10 THEN 1 ELSE 0 END) as Sub_207,
				SUM(CASE WHEN es.subject_id=41 THEN 1 ELSE 0 END) as Sub_208,
				SUM(CASE WHEN es.subject_id=6 THEN 1 ELSE 0 END) as Sub_209, 
				SUM(CASE WHEN es.subject_id=11 THEN 1 ELSE 0 END) as Sub_210,
				SUM(CASE WHEN es.subject_id=4 THEN 1 ELSE 0 END) as Sub_211,
				SUM(CASE WHEN es.subject_id=3 THEN 1 ELSE 0 END) as Sub_212, 
				SUM(CASE WHEN es.subject_id=5 THEN 1 ELSE 0 END) as Sub_213, 
				SUM(CASE WHEN es.subject_id=12 THEN 1 ELSE 0 END) as Sub_214, 
				SUM(CASE WHEN es.subject_id=30 THEN 1 ELSE 0 END) as Sub_215,
				SUM(CASE WHEN es.subject_id=13 THEN 1 ELSE 0 END) as Sub_216, 
				SUM(CASE WHEN es.subject_id=17 THEN 1 ELSE 0 END) as Sub_222,
				SUM(CASE WHEN es.subject_id=40 THEN 1 ELSE 0 END) as Sub_223,
				SUM(CASE WHEN es.subject_id=16 THEN 1 ELSE 0 END) as Sub_225,
				SUM(CASE WHEN es.subject_id=7 THEN 1 ELSE 0 END) as Sub_229 
				FROM rs_examcenter_details ed INNER JOIN rs_student_allotments sa ON sa.examcenter_detail_id= ed.id 
				INNER JOIN rs_exam_subjects es ON es.student_id= sa.student_id AND es.deleted_at IS null 
				INNER JOIN rs_students s ON s.id= sa.student_id 
				WHERE sa.exam_month= '$stream'
				AND sa.exam_year = '$examyear'
				AND sa.course= '$course'
				AND sa.supplementary =0 
				AND sa.deleted_at IS NULL group by sa.examcenter_detail_id ORDER BY sa.examcenter_detail_id ASC");
			
			$fresh_array = array();
			foreach($aicenter_stream_subject_data as $akey=>$aicenter_subject){
				if(!empty($aicenter_subject->Center_code)){
					$fresh_array[$aicenter_subject->Center_code] = $aicenter_subject;
				}
			}
			
			$aicenter_supp_subject_data = DB::select("SELECT ed.cent_name as cent_name,ed.id as Center_code, 
				SUM(CASE WHEN ess.subject_id=1 THEN 1 ELSE 0 END) as Sub_201,
				SUM(CASE WHEN ess.subject_id=2 THEN 1 ELSE 0 END) as Sub_202, 
				SUM(CASE WHEN ess.subject_id=9 THEN 1 ELSE 0 END) as Sub_206, 
				SUM(CASE WHEN ess.subject_id=10 THEN 1 ELSE 0 END) as Sub_207,
				SUM(CASE WHEN ess.subject_id=41 THEN 1 ELSE 0 END) as Sub_208,
				SUM(CASE WHEN ess.subject_id=6 THEN 1 ELSE 0 END) as Sub_209, 
				SUM(CASE WHEN ess.subject_id=11 THEN 1 ELSE 0 END) as Sub_210,
				SUM(CASE WHEN ess.subject_id=4 THEN 1 ELSE 0 END) as Sub_211,
				SUM(CASE WHEN ess.subject_id=3 THEN 1 ELSE 0 END) as Sub_212, 
				SUM(CASE WHEN ess.subject_id=5 THEN 1 ELSE 0 END) as Sub_213, 
				SUM(CASE WHEN ess.subject_id=12 THEN 1 ELSE 0 END) as Sub_214, 
				SUM(CASE WHEN ess.subject_id=30 THEN 1 ELSE 0 END) as Sub_215,
				SUM(CASE WHEN ess.subject_id=13 THEN 1 ELSE 0 END) as Sub_216, 
				SUM(CASE WHEN ess.subject_id=17 THEN 1 ELSE 0 END) as Sub_222,
				SUM(CASE WHEN ess.subject_id=40 THEN 1 ELSE 0 END) as Sub_223,
				SUM(CASE WHEN ess.subject_id=16 THEN 1 ELSE 0 END) as Sub_225,
				SUM(CASE WHEN ess.subject_id=7 THEN 1 ELSE 0 END) as Sub_229 
				FROM rs_examcenter_details ed INNER JOIN rs_student_allotments sa ON sa.examcenter_detail_id= ed.id 
				INNER JOIN rs_supplementaries ss ON ss.student_id= sa.student_id 
				INNER JOIN rs_supplementary_subjects ess ON ess.supplementary_id = ss.id  AND ess.deleted_at IS null 
				WHERE sa.exam_month= '$stream'
				AND sa.exam_year = '$examyear'
				AND ss.exam_month= '$stream'
				AND ss.exam_year = '$examyear'
				AND sa.course= '$course' 
				AND sa.supplementary =1 
				AND ss.is_eligible =1
				AND sa.deleted_at IS NULL 
				AND ss.deleted_at IS NULL group by sa.examcenter_detail_id ORDER BY sa.examcenter_detail_id ASC");
			$supp_array = array();
			foreach($aicenter_supp_subject_data as $dkey=>$aicenter_supp_subject){
				if(!empty($aicenter_supp_subject->Center_code)){
					$supp_array[$aicenter_supp_subject->Center_code] = $aicenter_supp_subject;
				}
			}  
			$i =1 ; 
			foreach($examcenterRecord as $examcenter){
				$Center_code = @$examcenter->id;
				$output[] = array(
					'id' => $i,
					'cent_name' => @$examcenter->cent_name,
					'centercodes' => @$examcenter->ecenter10,
					'Sub_201' => @$fresh_array[$Center_code]->Sub_201 + @$supp_array[$Center_code]->Sub_201,
					'Sub_202' => @$fresh_array[$Center_code]->Sub_202 + @$supp_array[$Center_code]->Sub_202,
					'Sub_206' => @$fresh_array[$Center_code]->Sub_206 + @$supp_array[$Center_code]->Sub_206,
					'Sub_207' => @$fresh_array[$Center_code]->Sub_207 + @$supp_array[$Center_code]->Sub_207,
					'Sub_208' => @$fresh_array[$Center_code]->Sub_208 + @$supp_array[$Center_code]->Sub_208,
					'Sub_209' => @$fresh_array[$Center_code]->Sub_209 + @$supp_array[$Center_code]->Sub_209,
					'Sub_210' => @$fresh_array[$Center_code]->Sub_210 + @$supp_array[$Center_code]->Sub_210,
					'Sub_211' => @$fresh_array[$Center_code]->Sub_211 + @$supp_array[$Center_code]->Sub_211,
					'Sub_212' => @$fresh_array[$Center_code]->Sub_212 + @$supp_array[$Center_code]->Sub_212,
					'Sub_213' => @$fresh_array[$Center_code]->Sub_213 + @$supp_array[$Center_code]->Sub_213,
					'Sub_214' => @$fresh_array[$Center_code]->Sub_214 + @$supp_array[$Center_code]->Sub_214,
					'Sub_215' => @$fresh_array[$Center_code]->Sub_215 + @$supp_array[$Center_code]->Sub_215,
					'Sub_216' => @$fresh_array[$Center_code]->Sub_216 + @$supp_array[$Center_code]->Sub_216,
					'Sub_222' => @$fresh_array[$Center_code]->Sub_222 + @$supp_array[$Center_code]->Sub_222,
					'Sub_223' => @$fresh_array[$Center_code]->Sub_223 + @$supp_array[$Center_code]->Sub_223,
					'Sub_225' => @$fresh_array[$Center_code]->Sub_225 + @$supp_array[$Center_code]->Sub_225,
					'Sub_229' => @$fresh_array[$Center_code]->Sub_229 + @$supp_array[$Center_code]->Sub_229,
				);
				$i++;
			}
			
		}elseif($course == 12){
			$aicenter_stream_subject_data = DB::select("SELECT ed.cent_name as cent_name,ed.id as Center_code,
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
				FROM rs_examcenter_details ed INNER JOIN rs_student_allotments sa ON sa.examcenter_detail_id= ed.id 
				INNER JOIN rs_exam_subjects es ON es.student_id= sa.student_id AND es.deleted_at IS null 
				INNER JOIN rs_students s ON s.id= sa.student_id 
				WHERE sa.exam_month= '$stream'
				AND sa.exam_year = '$examyear'
				AND sa.course= '$course'
				AND sa.supplementary =0 
				AND sa.deleted_at IS NULL group by sa.examcenter_detail_id ORDER BY sa.examcenter_detail_id ASC");
			$aicenter_stream_subject_data=[];

			$fresh_array = array();
			foreach($aicenter_stream_subject_data as $akey=>$aicenter_subject){
				if(!empty($aicenter_subject->Center_code)){
					$fresh_array[$aicenter_subject->Center_code] = $aicenter_subject;
				}
			}

			$aicenter_supp_subject_data = DB::select("SELECT ed.cent_name as cent_name,ed.id as Center_code, 
				SUM(CASE WHEN ess.subject_id=18 THEN 1 ELSE 0 END) as Sub_301,
				SUM(CASE WHEN ess.subject_id=19 THEN 1 ELSE 0 END) as Sub_302,
				SUM(CASE WHEN ess.subject_id=20 THEN 1 ELSE 0 END) as Sub_306,
				SUM(CASE WHEN ess.subject_id=21 THEN 1 ELSE 0 END) as Sub_309,
				SUM(CASE WHEN ess.subject_id=22 THEN 1 ELSE 0 END) as Sub_311,
				SUM(CASE WHEN ess.subject_id=23 THEN 1 ELSE 0 END) as Sub_312,
				SUM(CASE WHEN ess.subject_id=24 THEN 1 ELSE 0 END) as Sub_313,
				SUM(CASE WHEN ess.subject_id=25 THEN 1 ELSE 0 END) as Sub_314,
				SUM(CASE WHEN ess.subject_id=26 THEN 1 ELSE 0 END) as Sub_315,
				SUM(CASE WHEN ess.subject_id=27 THEN 1 ELSE 0 END) as Sub_317,
				SUM(CASE WHEN ess.subject_id=28 THEN 1 ELSE 0 END) as Sub_316,
				SUM(CASE WHEN ess.subject_id=29 THEN 1 ELSE 0 END) as Sub_318,
				SUM(CASE WHEN ess.subject_id=31 THEN 1 ELSE 0 END) as Sub_319,
				SUM(CASE WHEN ess.subject_id=32 THEN 1 ELSE 0 END) as Sub_320,
				SUM(CASE WHEN ess.subject_id=33 THEN 1 ELSE 0 END) as Sub_321,
				SUM(CASE WHEN ess.subject_id=34 THEN 1 ELSE 0 END) as Sub_328,
				SUM(CASE WHEN ess.subject_id=35 THEN 1 ELSE 0 END) as Sub_330,
				SUM(CASE WHEN ess.subject_id=36 THEN 1 ELSE 0 END) as Sub_331,
				SUM(CASE WHEN ess.subject_id=37 THEN 1 ELSE 0 END) as Sub_332,
				SUM(CASE WHEN ess.subject_id=38 THEN 1 ELSE 0 END) as Sub_333,
				SUM(CASE WHEN ess.subject_id=39 THEN 1 ELSE 0 END) as Sub_336,
				SUM(CASE WHEN ess.subject_id=52 THEN 1 ELSE 0 END) as Sub_391,
				SUM(CASE WHEN ess.subject_id=51 THEN 1 ELSE 0 END) as Sub_392,
				SUM(CASE WHEN ess.subject_id=53 THEN 1 ELSE 0 END) as Sub_393,
				SUM(CASE WHEN ess.subject_id=46 THEN 1 ELSE 0 END) as Sub_394,
				SUM(CASE WHEN ess.subject_id=47 THEN 1 ELSE 0 END) as Sub_395,
				SUM(CASE WHEN ess.subject_id=48 THEN 1 ELSE 0 END) as Sub_396
				FROM rs_examcenter_details ed INNER JOIN rs_student_allotments sa ON sa.examcenter_detail_id= ed.id 
				INNER JOIN rs_supplementaries ss ON ss.student_id= sa.student_id 
				INNER JOIN rs_supplementary_subjects ess ON ess.supplementary_id= ss.id  AND ess.deleted_at IS null 
				WHERE sa.exam_month= '$stream'
				AND sa.exam_year = '$examyear'
				AND sa.course= '$course'
				AND ss.exam_month= '$stream'
				AND ss.exam_year = '$examyear'
				AND sa.supplementary =1 
				AND ss.is_eligible =1
				AND sa.deleted_at IS NULL 
				AND ss.deleted_at IS NULL group by sa.examcenter_detail_id ORDER BY sa.examcenter_detail_id ASC");
				
			$supp_array = array();
			foreach($aicenter_supp_subject_data as $dkey=>$aicenter_supp_subject){
				if(!empty($aicenter_supp_subject->Center_code)){
					$supp_array[$aicenter_supp_subject->Center_code] = $aicenter_supp_subject;
				}
			}
			
			$i =1 ;
			foreach($examcenterRecord as $examcenter){
				$Center_code = @$examcenter->id;
				$output[] = array(
					'id' => $i,
					'cent_name' => @$examcenter->cent_name,
					'centercodes' => @$examcenter->ecenter12,
					'Sub_301' => @$fresh_array[$Center_code]->Sub_301 + @$supp_array[$Center_code]->Sub_301,
					'Sub_302' => @$fresh_array[$Center_code]->Sub_302 + @$supp_array[$Center_code]->Sub_302,
					'Sub_306' => @$fresh_array[$Center_code]->Sub_306 + @$supp_array[$Center_code]->Sub_306,
					'Sub_309' => @$fresh_array[$Center_code]->Sub_309 + @$supp_array[$Center_code]->Sub_309,
					'Sub_311' => @$fresh_array[$Center_code]->Sub_311 + @$supp_array[$Center_code]->Sub_311,
					'Sub_312' => @$fresh_array[$Center_code]->Sub_312 + @$supp_array[$Center_code]->Sub_312,
					'Sub_313' => @$fresh_array[$Center_code]->Sub_313 + @$supp_array[$Center_code]->Sub_313,
					'Sub_314' => @$fresh_array[$Center_code]->Sub_314 + @$supp_array[$Center_code]->Sub_314,
					'Sub_315' => @$fresh_array[$Center_code]->Sub_315 + @$supp_array[$Center_code]->Sub_315,
					'Sub_316' => @$fresh_array[$Center_code]->Sub_316 + @$supp_array[$Center_code]->Sub_316,
					'Sub_317' => @$fresh_array[$Center_code]->Sub_317 + @$supp_array[$Center_code]->Sub_317,
					'Sub_318' => @$fresh_array[$Center_code]->Sub_318 + @$supp_array[$Center_code]->Sub_318,
					'Sub_319' => @$fresh_array[$Center_code]->Sub_319 + @$supp_array[$Center_code]->Sub_319,
					'Sub_320' => @$fresh_array[$Center_code]->Sub_320 + @$supp_array[$Center_code]->Sub_320,
					'Sub_321' => @$fresh_array[$Center_code]->Sub_321 + @$supp_array[$Center_code]->Sub_321,
					'Sub_328' => @$fresh_array[$Center_code]->Sub_328 + @$supp_array[$Center_code]->Sub_328,
					'Sub_330' => @$fresh_array[$Center_code]->Sub_330 + @$supp_array[$Center_code]->Sub_330,
					'Sub_331' => @$fresh_array[$Center_code]->Sub_331 + @$supp_array[$Center_code]->Sub_331,
					'Sub_332' => @$fresh_array[$Center_code]->Sub_332 + @$supp_array[$Center_code]->Sub_332,
					'Sub_333' => @$fresh_array[$Center_code]->Sub_333 + @$supp_array[$Center_code]->Sub_333,
					'Sub_336' => @$fresh_array[$Center_code]->Sub_336 + @$supp_array[$Center_code]->Sub_336,
					'Sub_391' => @$fresh_array[$Center_code]->Sub_391 + @$supp_array[$Center_code]->Sub_391,
					'Sub_392' => @$fresh_array[$Center_code]->Sub_392 + @$supp_array[$Center_code]->Sub_392,
					'Sub_393' => @$fresh_array[$Center_code]->Sub_393 + @$supp_array[$Center_code]->Sub_393,
					'Sub_394' => @$fresh_array[$Center_code]->Sub_394 + @$supp_array[$Center_code]->Sub_394,
					'Sub_395' => @$fresh_array[$Center_code]->Sub_395 + @$supp_array[$Center_code]->Sub_395,
					'Sub_396' => @$fresh_array[$Center_code]->Sub_396 + @$supp_array[$Center_code]->Sub_396
					
					
				);
				$i++;
			}
		}
		
      return collect($output);
	}

	
	public function headings(): array{

		if($this->course  == 10){
			return ["Sr. No.","Exam_center_code","Exam_center","Sub_201","Sub_202","Sub_206","Sub_207","Sub_208","Sub_209","Sub_210","Sub_211","Sub_212","Sub_213","Sub_214","Sub_215","Sub_216","Sub_222","Sub_223","Sub_225","Sub_229"];	
		}
		elseif($this->course  == 12){
			return ["Sr. No.","Exam_center_code","Exam_center","Sub_301","Sub_302","Sub_306","Sub_309","Sub_311","Sub_312","Sub_313","Sub_314","Sub_315","Sub_316","Sub_317","Sub_318","Sub_319","Sub_320","Sub_321","Sub_328","Sub_330","Sub_331","Sub_332","Sub_333","Sub_336","Sub_391","Sub_392","Sub_393","Sub_394","Sub_395","Sub_396"];	
		}
        
    }

	 
}