<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;
use App\Component\PracticalCustomComponent;
use App\Http\Controllers\Controller;

class PracticalSlotexcelExlExport implements FromCollection,WithHeadings,ShouldAutoSize
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
		$formId = "Practical_Slot_Report";
		$custom_component_obj = new CustomComponent;
		$controller_obj = new Controller;
		$subject_list = $controller_obj->subjectList();
		$practicalCustomComponent = new practicalCustomComponent();	
		$yes_no = $controller_obj->master_details('yesno'); 
		$deo_district_id = $controller_obj->districtsByState(6);
		$combo_name = 'course';$course = $controller_obj->master_details($combo_name);
		$examCenterList =  $examcenter_datails_dropdown = collect($custom_component_obj->getExamCenterWithBothCourseCode()); 

		$result = $practicalCustomComponent->getPraticalSlotData($formId);
		
	
	
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => @$i,
				'examcenter_detail_id' =>@$examCenterList[@$data['examcenter_detail_id']],
				'course'=> @$course[@$data['course']],
				'subject_id' => @$subject_list[@$data['subject_id']],
				'ssoid' => @$data['ssoid'],
				'batch_student_count'=> @$data['batch_student_count'],
				'date_time_start'=> date('d-m-Y h:i A',strtotime(@$data['date_time_start'])),
				'date_time_end'=> date('d-m-Y h:i A',strtotime(@$data['date_time_end'])),
				'entry_done'=> @$yes_no[@$data['entry_done']],
				'skip_slot'=> @$yes_no[@$data['skip_slot']],
				
			
				
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Exam Center","Course","Subject Name","Examiner SSO","Batch Student Count","Start Date Time","End Date Time","Slot Lock & Submit","Skip Slot"];
    }	 
	
	 
}