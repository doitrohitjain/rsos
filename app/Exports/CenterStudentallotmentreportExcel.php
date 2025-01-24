<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use App\Models\AicenterDetail;
use App\Models\ExamcenterDetail;
use Session;
use App\Component\CustomComponent;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Helper\CustomHelper;

class CenterStudentallotmentreportExcel implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
	
	/*
	protected $id;
	function __construct($id) {
       // $this->id = $id;
	}
	*/
	
									
	public function collection(){
		ini_set('memory_limit', '10000M');
		ini_set('max_execution_time', '0');
		$exam_year =CustomHelper::_get_selected_sessions();
		$exam_month = config("global.CenterAllotmentStreamId");
		$aiCenters = AicenterDetail::where('active',1)->pluck('ai_code','ai_code')->toArray();
	    $aiCentersName = AicenterDetail::where('active',1)->pluck('college_name','ai_code')->toArray();
		
		$studentData = DB::select("select course,ai_code,count(id) as count from rs_students where exam_year = $exam_year and exam_month = $exam_month and is_eligible = 1 and deleted_at IS null GROUP BY ai_code,course;");
		
		$examcenteretail = ExamcenterDetail::orderBy('examcenter_details.id','ASC')->get(['id','cent_name']);
		$examcenteretail10 = ExamcenterDetail::orderBy('examcenter_details.id','ASC')->pluck('ecenter10','id');
		$examcenteretail12 = ExamcenterDetail::orderBy('examcenter_details.id','ASC')->pluck('ecenter12','id'); 
		
		
		$studentDataFinal = array();
		foreach($studentData as $studentKey => $student){
			$studentDataFinal[$student->ai_code][$student->course] = $student->count;
		} 
		$supplementaryData = DB::select("select course,ai_code,count(id) as count from rs_supplementaries where exam_year = $exam_year and exam_month = $exam_month and is_eligible = 1 and deleted_at IS null  GROUP BY ai_code,course;");
		
		$supplementaryDataFinal = array();
		foreach($supplementaryData as $studentKey => $student){
			$supplementaryDataFinal[$student->ai_code][$student->course] = $student->count;
		}
		$examCenters = array();
		$studentAllotmentData = DB::select("select examcenter_detail_id,course,ai_code,count(id) as count from rs_student_allotments where exam_year = $exam_year and exam_month = $exam_month and deleted_at IS null GROUP BY ai_code,course;");
	
		$examCenetIds = array();
		$studentAllotmentDataFinal = array();
		foreach($studentAllotmentData as $studentKey => $student){
			$studentAllotmentDataFinal[$student->ai_code][$student->course] = $student->count;
		} 
		$custom_component_obj = new CustomComponent;
		$finalArr = array();
		foreach($aiCenters as $id => $ai_code){
			$examcenters = null;
			$html = null;
			$finalArr[10] = array();
			$finalArr[12] = array();
			if(@$ai_code){
				$examcentersTemp = $custom_component_obj->_getexamcenterdatils($ai_code);
				if(@$examcentersTemp){ 
					$examcentersTemp = json_decode($examcentersTemp,true); 
					foreach($examcentersTemp as $data){ 
						if(@$data['examcenter_detail_id']){
							if(@$data['course']){
								if($data['course'] == 10 && @$data['examcenter_detail_id']){
									$finalArr[10][] = @$examcenteretail10[@$data['examcenter_detail_id']];
								}
								if($data['course'] == 12 && @$data['examcenter_detail_id']){
									$finalArr[12][] = @$examcenteretail12[@$data['examcenter_detail_id']];
								}
							}
						}
					}
				}
				
			}
			if(@$finalArr[10]){
				$html .= "(10th :" . implode(",",$finalArr[10]) . ")";
			}
			
			if(@$finalArr[12]){
				$html .= "(12th :" . implode(",",$finalArr[12]) . ")";
			}
			
			
			 
			$examcenters = $html;
			
			$finalArr[$ai_code] = array();  
			$finalArr[$ai_code]['other']['ai_name']=  $aiCentersName[$ai_code];
			$finalArr[$ai_code]['other']['ai_code']= $ai_code; 
			$finalArr[$ai_code]['other']['examcenters']= @$examcenters; 
			$finalArr[$ai_code]['student'][10] = 0;
			$finalArr[$ai_code]['student'][12] = 0;
			$finalArr[$ai_code]['supplementary'][10] = 0;
			$finalArr[$ai_code]['supplementary'][12] = 0;
			$finalArr[$ai_code]['student_allotment'][10] = 0;
			$finalArr[$ai_code]['student_allotment'][12] = 0;
			$finalArr[$ai_code]['reaming_student_allotment'][10] = 0; 		
			$finalArr[$ai_code]['reaming_student_allotment'][12] = 0; 	
			
			if(@$studentDataFinal[$ai_code]){
				$finalArr[$ai_code]['student'][10] = @$studentDataFinal[$ai_code][10];	
				$finalArr[$ai_code]['student'][12] = @$studentDataFinal[$ai_code][12];	
			} 
			if(@$supplementaryDataFinal[$ai_code]){
				$finalArr[$ai_code]['supplementary'][10] = @$supplementaryDataFinal[$ai_code][10];	
				$finalArr[$ai_code]['supplementary'][12] = @$supplementaryDataFinal[$ai_code][12];	
			} 
			
			$finalArr[$ai_code]['total_student'][10] = $finalArr[$ai_code]['student'][10] + $finalArr[$ai_code]['supplementary'][10];
			$finalArr[$ai_code]['total_student'][12] = $finalArr[$ai_code]['student'][12] + $finalArr[$ai_code]['supplementary'][12]; 
			$finalArr[$ai_code]['total'] = $finalArr[$ai_code]['total_student'][10] + $finalArr[$ai_code]['total_student'][12];
			
			if(@$studentAllotmentDataFinal[$ai_code]){
				$finalArr[$ai_code]['student_allotment'][10] = @$studentAllotmentDataFinal[$ai_code][10];	
				$finalArr[$ai_code]['student_allotment'][12] = @$studentAllotmentDataFinal[$ai_code][12];	
			} 
			if(@$studentDataFinal[$ai_code]){
				$finalArr[$ai_code]['reaming_student_allotment'][10] = 			
				(( $finalArr[$ai_code]['student'][10] + $finalArr[$ai_code]['supplementary'][10]) - $finalArr[$ai_code]['student_allotment'][10]);
				$finalArr[$ai_code]['reaming_student_allotment'][12] = (( $finalArr[$ai_code]['student'][12] + $finalArr[$ai_code]['supplementary'][12]) - $finalArr[$ai_code]['student_allotment'][12]);
			} 
		}

		$i =1 ;
       foreach($finalArr as $data){
            $output[] = array(
				'id' => $i,
				'ai_name' => @$data['other']['ai_name'],
				'ai_code' => @$data['other']['ai_code'],
				'exam_center' => @$data['other']['examcenters'],
				'eligiable_Fresh_10' => @$data['student']['10'] ,
				'eligiable_Fresh_12' => @$data['student']['12'] ,
				'eligiable_Supplementary_10' => @$data['supplementary']['10'], 
				'eligiable_Supplementary_12' => @$data['supplementary']['12'], 
				'eligiable_Total_fresh+supp_10' => @$data['total_student']['10'], 
				'eligiable_Total_fresh+supp_12' => @$data['total_student']['12'] ,
				'eligiable_Total_fresh+supp_10_12' => @$data['total'],
				'total_Allotted_fresh+supp_10' => @$data['student_allotment']['10'], 
				'total_Allotted_fresh+supp_12' => @$data['student_allotment']['12'], 
				'total_Allotted_fresh+supp_10+12' => @$data['student_allotment']['10'] +  @$data['student_allotment']['12'],
				'total_Reaming_fresh+supp_10' => @$data['reaming_student_allotment']['10'], 
				'total_Reaming_fresh+supp_12' => @$data['reaming_student_allotment']['12'], 
				'total_Reaming_fresh+supp_10+12' => @$data['reaming_student_allotment']['10'] +  @$data['reaming_student_allotment']['12'],
				
            );
			$i++;
		}
		return collect($output);
     
    }
	 
	public function headings(): array{
        return ["Sr. No.", "Ai Center","Ai Code","Exam Center","Eligiable Fresh 10","Eligiable Fresh 12","Eligiable Supplementary 10","Eligiable Supplementary 12","Eligiable Total(fresh+supp) 10","Eligiable Total(fresh+supp) 12","Eligiable Total(fresh+supp) (10+12)","Total Allotted(fresh+supp) 10","Total Allotted(fresh+supp) 12","Total Allotted(fresh+supp) (10+12)","Total Reaming(fresh+supp) 10","Total Reaming(fresh+supp) 12","Total Reaming(fresh+supp) (10+12)"];
    }	 
	
	 
}