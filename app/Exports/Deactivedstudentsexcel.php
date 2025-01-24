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

class Deactivedstudentsexcel implements FromCollection,WithHeadings
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
		$formId = "Deactived_Students";
		$custom_controller_obj = new Controller;
		$combo_name = 'adm_type';$adm_types = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'stream_id';$stream_id = $custom_controller_obj->master_details($combo_name);
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->getDeleteStudentdata($formId, false);
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'name' => $data['name'],
				'ai_code'=> $data['ai_code'],
				'enrollment'=> $data['enrollment'],
				'gender_id' => ($data['gender_id']==1)? "Male":"Female",
				'course'=> $data['course'],
				'adm_type'=> $adm_types[$data['adm_type']],
				'stream'=> $stream_id[$data['stream']],
				'medium' => ($data['medium']==1)? "Hindi":"English",
				'locksumbitted' => ($data['application']['locksumbitted']==1)? "Yes":"No",
				'fee amount'=> $data['application']['fee_paid_amount'],
				'challan tid'=> $data['challan_tid'],
				'submitted'=> $data['submitted'],
				'deleted_at'=> $data['deleted_at'],
				
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Name","ai_code","enrollment","gender","Course","adm type","stream","Medium","Lock & Submit","fee amount","challan tid","submitted","deleted_at"];
    }	 
	
	 
}



								
									