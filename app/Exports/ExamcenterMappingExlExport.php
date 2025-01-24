<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\ExamcenterDetail;
use App\Models\Subject;
use Session;
use App\Component\practicalCustomComponent;
use Config;

class ExamcenterMappingExlExport implements FromCollection,WithHeadings
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
		$output = array();
		$table_id = "Examiner_map_List";
		$formId = ucfirst(str_replace(" ","_",$table_id));
		
		$examcenter_list = ExamcenterDetail::pluck('cent_name','id');
		$subject_list = Subject::pluck('name','id');
		$combo_name = 'course'; 
		$Controller = new Controller;
		$course_dropdown = $Controller->master_details($combo_name);
		$current_exam_year = Config::get("global.admission_academicyear_id");
		$current_exam_month = Config::get("global.current_exam_month_id");
		$combo_name = 'admission_sessions';$exam_year_arr = $Controller->master_details($combo_name);
		$combo_name = 'exam_session';$exam_month_arr = $Controller->master_details($combo_name);
		 
		$practicalCustomComponent = new practicalCustomComponent;
		$master = $practicalCustomComponent->getExaminerMappingList($formId,false);
		
		$i =1 ;
		foreach($master as $data){
			if(isset($exam_year_arr[@$current_exam_year]) && isset($exam_month_arr[@$current_exam_month])){ 
				$current_exam_year_data = $exam_year_arr[@$current_exam_year]."/".$exam_month_arr[@$current_exam_month]; 
			}else { 
				$current_exam_year_data = "-"; 
			} 
			
			if(isset($examcenter_list[@$data->examcenter_detail_id])){ 
				$examcenter_detail_id = $examcenter_list[@$data->examcenter_detail_id]; 
			} else { 
				$examcenter_detail_id = "-"; 
			}
			
			
			if(isset($subject_list[$data->subject_id])){ 
				$subject_id= $subject_list[$data->subject_id]; 
			}else { 
				$subject_id= "-"; 
			}
			
			$StudentCountArr = $practicalCustomComponent->getPracticalStudentList(@$data->examcenter_detail_id,$data->subject_id,false);
			if(@$StudentCountArr){
				$studentCount = count($StudentCountArr);
			} else {
				$studentCount = "-";
			}
			
			$fld="is_lock_submit";
			// if(@$yesno[$data->$fld]){
			if(@$data->$fld){
				$is_lock_submit = "Yes";
			}else{
				$is_lock_submit = "No";
			}
		
			$fld="document";
			if(@$data->$fld){
				$document = "Yes";
			}else{
				$document = "No";
			}
		
			
            $output[] = array(
				'id' => $i,
				// 'current_exam_year' => $current_exam_year_data,
				'examcenter_detail_id' => $examcenter_detail_id,
				'ecenter10' => @$data->ecenter10,
				'ecenter12' => @$data->ecenter12,
				'name' => @$data->name,
				'ssoid' => @$data->ssoid,
				'course' => @$data->course."th",
				'stream' => @$data->stream,
				'mobile' => @$data->mobile,
				'subject_id' => $subject_id,
				'student_count' => $studentCount,
				'is_lock_submit' => $is_lock_submit,
				'document' => $document,
			 );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
		//"Academic Year",
        return ["Sr. No.",  "Exam Center Name","ecenter10","ecenter12","Examiner Name","Examiner SSOID","Course","Stream","Mobile Number","Subject","No of Student","Is Lock & Submitted","Is Signed PDF Upload"];
    }	 

}