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

class RevalApplicationExlExport implements FromCollection,WithHeadings
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
		$formId = "Reval_Applicaiton_Report";
		$custom_component_obj = new CustomComponent;
		$custom_controller_obj = new Controller;
		$result = $custom_component_obj->getRevalApplicationData($formId, false);
		$combo_name = 'yesno';$yesno = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'reval_types';$reval_types = $custom_controller_obj->master_details($combo_name);
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'name' => @$data['name'],
                'SSO' =>  @$data['ssoid'],
                'Is Eligible'=>$yesno[@$data['is_eligible']],
				'enrollment'=> @$data['enrollment'],
				'course'=> @$data['course'],
				'gender_id' => (@$data['gender_id']==1)? "Male":"Female",
				'reval_type' => $reval_types[$data['reval_type']],
				'locksumbitted' => (@$data['locksumbitted']==1)? "Yes":"No",
				'is_self_filleḍ' =>@$yesno[$data['is_self_filled']],
				'fee amount'=> @$data['total_fees'],
				'challan tid'=> @$data['challan_tid'],
				'submitted'=> @$data['submitted'],
				
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Name",'SSO',"Is Eligible","enrollment","Course","Gender","Reval type","Lock & Submit",'is self filleḍ',"fee amount","challan tid","submitted"];
    }	 
	
	 
}