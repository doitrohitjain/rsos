<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;
use App\Http\Controllers\Controller;
use App\Models\Subject;

class TimetabledetailsExlExport implements FromCollection,WithHeadings
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
		$formId = "Time_Sheet_Details";
        $custom_controller_obj = new Controller;
        $combo_name = 'course';$course = $custom_controller_obj->master_details($combo_name);
        $combo_name = 'exam_time_table_start_end_time';$exam_time_table_start_end_time = $custom_controller_obj->master_details($combo_name);
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->getTimeTablesdatas($formId,false); 
		$allsubjects = Subject::pluck('name','id');
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'Course' => @$course[$data['course']],
				'allsubjects'=> @$allsubjects[$data['subjects']],
				'exam_date'=> @$data['exam_date'],
				'exam_time_start'=>@$exam_time_table_start_end_time[$data['exam_time_start']],
				'exam_time_end' =>@$exam_time_table_start_end_time[$data['exam_time_end']],
			);
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Course","subjects","exam date","exam time start","exam time end"];
    }	 
	
	 
}

