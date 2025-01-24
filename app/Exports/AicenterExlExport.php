<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;
use App\Models\User;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class AicenterExlExport implements FromCollection,WithHeadings,ShouldAutoSize
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
	
	public function columnFormats(): array
  {
    return [
	    'K' => NumberFormat::FORMAT_NUMBER,
    ];
  }
	
									
	public function collection(){
		$output = array();
		$formId = "Users_Details";
		$custom_component_obj = new CustomComponent;
		$custom_controller_obj = new Controller;
		$district_list = $custom_controller_obj->districtsByState(6);
		$block_list = $custom_controller_obj->block_details();
		$actives = array("0"=>"INActive", "1"=>"Active");
		$result = $custom_component_obj->getAicenterData($formId, false);

		$i =1 ;
       
		foreach($result as $key => $user){
			$roles = $user->getRoleNames()->toArray();
			$roles = implode(" ,",$roles);
            $output[] = array(
				'id' => $i,
				'ai_code 	' => @$user['ai_code'],
				'college_name' => @$user['college_name'],
				'district_id' => @$district_list[@$user->district_id],
				'block_id' => @$block_list[@$user->block_id],
				'temp_district_id' => @$district_list[@$user->temp_district_id],
				'temp_block_id' => @$block_list[@$user->temp_block_id],
				'principal_name' => @$user['principal_name'],
				'pincode' => @$user['pincode'],
				'principal_mobile_number' => @$user['principal_mobile_number'],
				'nodal_officer_name' => @$user['nodal_officer_name'],
				'nodal_officer_mobile_number' => @$user['nodal_officer_mobile_number'],
				'ssoid' => @$user['ssoid'],
				'school_account_number' => @$user['school_account_number'],
				'school_account_bank_name' => @$user['school_account_bank_name'],
				'school_account_ifsc' => @$user['school_account_ifsc'],
				'active' => @$actives[$user['active']],
				
            );
			$i++;
		}
	
		return collect($output);
     
    }
	 
	public function headings(): array{
        return ["Sr. No.", "Ai Code","Ai Center Name","District","Block","New District","New Block","Principal Name","PinCode","Principal Mobile Number","Nodal Officer Name","Nodal Officer Mobile Number","SSOID","AI Center Account Number","AI Center Bank name","AI Center IFSC Code","Status"];
    }	 
	
	 
}

