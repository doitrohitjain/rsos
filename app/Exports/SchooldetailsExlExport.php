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
use App\Models\School;

class SchooldetailsExlExport implements FromCollection,WithHeadings
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
		$formId = "School_Report";
        $custom_controller_obj = new Controller;
		$district = $custom_controller_obj->districtsByState();
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->getSchoolData($formId,false); 
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'District' => $district[$data['District']],
				'School'=> $data['School'],
				'PrincipalName'=> $data['PrincipalName'],
				'MobileNo' => $data['MobileNo'],
				'PrincipalOrHeadmasterEmail' => $data['PrincipalOrHeadmasterEmail'],
				'School_Type' => $data['School_Type'],
				'School_Category'=> $data['School_Category'],
				
				
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "District","School","PrincipalName","MobileNo","PrincipalEmail","School Type","School Category"];
    }	 
	
	 
}

