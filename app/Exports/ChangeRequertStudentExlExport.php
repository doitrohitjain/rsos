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

class ChangeRequertStudentExlExport implements FromCollection,WithHeadings
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
		$formId = "Change_Request_Student_Applications";
		$custom_controller_obj = new Controller;
		$combo_name = 'gender';$gender = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'midium';$midium = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'adm_type';$adm_types = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'yesno';$yesno = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'stream_id';$stream_id = $custom_controller_obj->master_details($combo_name);
		$student_change_request = array("1"=>"Pending", "2"=>"Approved");
		$student_change_requests = collect($student_change_request);
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->getchangerequestallStudentdata($formId, false);
		$AiCentersWithdistrictname = $custom_component_obj->getAiCentersWithdistrictname();
		$i =1 ;
		foreach($result as $data){							
            $output[] = array(
				'id' => $i,
				'enrollment'=> @$data['enrollment'],
				'ssoid' => @$data['ssoid'],
				'ai_code'=> @$data['ai_code'],
				'name' => @$data['name'],
				'gender_id' => @$gender[$data['gender_id']],
				'course'=> @$data['course'],
				'stream' => @$stream_id[$data['stream']],
				'adm_type' => @$adm_types[$data['adm_type']],
				'student_change_requests' => @$student_change_requests[$data['student_change_requests']],
            );
			$i++;
        }
		
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Enrollment","SSO","AI Center Code","Name","Gender","Course","Stream","Adm Type","Student Change Requests"];
    }	 
	
	 
}