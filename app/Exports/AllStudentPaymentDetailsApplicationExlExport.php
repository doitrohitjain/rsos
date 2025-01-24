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


class AllStudentPaymentDetailsApplicationExlExport implements FromCollection,WithHeadings
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
		$combo_name = 'stream_id';$stream_id = $custom_controller_obj->master_details($combo_name);
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->getallStudentPaymentData($formId, false);
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'enrollment'=> $data['enrollment'],
				'name' => $data['name'],
		        'gender_id' => $gender[$data['gender_id']],
				'course'=> $data['course'],
				'stream' => $stream_id[$data['stream']],
				'adm_type' => $adm_types[$data['adm_type']],
				'challan_tid' => $data['challan_tid'],
				'fee_paid_amount' => $data['fee_paid_amount'],
				'submitted' => $data['submitted'],
				'ai_code' => $data['ai_code'],
				
				
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Enrollment","Name","Gender","Course","Stream","Admission","Challan Number","FeePaidAmount","Submitted","Ai Center",];
    }	 
	
	 
}