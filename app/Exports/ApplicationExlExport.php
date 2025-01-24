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

class ApplicationExlExport implements FromCollection,WithHeadings
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
		$formId = "Admission_Report";
		$custom_controller_obj = new Controller;
		$combo_name = 'gender';$gender = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'midium';$midium = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'adm_type';$adm_types = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'yesno';$yesno = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'stream_id';$stream_id = $custom_controller_obj->master_details($combo_name);
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->getApplicationData($formId, false);
		$AiCentersWithdistrictname = $custom_component_obj->getAiCentersWithdistrictname();
		$i =1 ;
		foreach($result as $data){
			
            $output[] = array(
				'id' => $i,
				'name' => @$data['name'],
                'ssoid' => @$data['ssoid'],
				'ai_code'=> @$AiCentersWithdistrictname[@$data['ai_code']],
				'enrollment'=> @$data['enrollment'],
				'course'=> @$data['course'],
				'gender_id' => @$gender[$data['gender_id']],
				'medium' => (@$data['medium']==1)? "Hindi":"English",
                'is_self_filleḍ' =>@$yesno[$data['is_self_filled']],
				'locksumbitted' =>(@$data['locksumbitted']==1)? "Yes":"No",
                'is_otp_verified' =>(@$data['is_otp_verified']==1)? "Yes":"No",
				'fee amount'=> @$data['fee_paid_amount'],
				'challan tid'=> @$data['challan_tid'],
				'submitted'=> @$data['submitted'],
				
            );
			$i++;
        }
		
	 
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Name","SSO","AI Center District Name","Enrollment","Course","Gender","Medium","Is Self Filled","Lock & Submit","Is Mobile OTP Verified","Fee Amount","Challan Tid","Submitted",];
    }	 
	
	 
}