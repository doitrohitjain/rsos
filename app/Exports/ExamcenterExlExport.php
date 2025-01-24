<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Http\Controllers\Controller;
use App\Component\CustomComponent;

class ExamcenterExlExport implements FromCollection,WithHeadings
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
		$formId = "ExamCenter_Report";
		$custom_component_obj = new CustomComponent;
		$controller_obj = new Controller;
		$result = $custom_component_obj->getExamcenterData($formId, false);
		$district = $controller_obj->districtsByState();
		
		$i =1 ;
		foreach($result as $data){

            $output[] = array(
				'id' => $i,
				'ssoid' => @$data['userdata'][0]['ssoid'],
				'ecenter10' => $data['ecenter10'],
				'ecenter12' => $data['ecenter12'],
				'ai_code' => $data['ai_code'],
				'district' =>@$district[$data['district_id']],
				'exam_incharge' => $data['exam_incharge'],
				'mobile' => $data['mobile'],
				'center_supdt' => $data['center_supdt'],
				'cent_name' => $data['cent_name'],
				'cent_name' => $data['cent_name']
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.","ssoid", "10th", "12th","ai_code","District","exam incharge","mobile","center supdt","center name"];
    }	 

}