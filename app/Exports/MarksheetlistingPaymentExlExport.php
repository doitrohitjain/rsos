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

class MarksheetlistingPaymentExlExport implements FromCollection,WithHeadings
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
		$formId = "Marksheet_Payment_Issues_Report";
		$custom_controller_obj = new Controller;
		$combo_name = 'gender';$gender = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'midium';$midium = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'adm_type';$adm_types = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'yesno';$yesno = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'stream_id';$stream_id = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'course';$course = $custom_controller_obj->master_details($combo_name);
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->MarksheetPaymentIssuesData($formId, false);
		$AiCentersWithdistrictname = $custom_component_obj->getAiCentersWithdistrictname();
		$i =1 ;
		foreach($result as $data){
			
            $output[] = array(
				'id' => $i,
				'enrollment ' => @$data['enrollment'],
                'name' => @$data['name'],
				'gender_id' => @$gender[$data['gender_id']],
				'course' => @$course[$data['course']],
				'stream' => @$stream_id[$data['stream']],
				'total_fees'=> @$data['total_fees'],
				'is_archived'=> @$yesno[@$data['is_archived']],
				'challan_tid'=> @$data['challan_tid'],
				
            );
			$i++;
        }
		
	 
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Enrollment Numbe","Name","Gender","Course","Stream","Fee Amount","Is Resolved","Challan Number"];
    }	 
	
	 
}