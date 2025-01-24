<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;
use App\Helper\CustomHelper;
use App\Http\Controllers\Controller;

class StudentAicenterCourseWiseCountExlExport implements FromCollection,WithHeadings
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
		$formId = "AiCenters_Wise_Admission_Report";
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->getWithPaginationAiCenters($formId, false);
		$custom_helper_obj = new CustomHelper;
		
		$conditionstream = Session::get($formId. '_conditionstream');
		
		$i =1 ;
		$controller_obj = new Controller;
		$district_list = $controller_obj->districtsByState(6);
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'ai_code' => $data['ai_code'],
				'college_name' => $data['college_name'],
				'getStudentLockSubmttedcourse10AiCodeWise' => $custom_helper_obj->_getStudentLockSubmttedcourse10AiCodeWise($data->ai_code,$conditionstream),
				'getStudentLockSubmttedcourse12AiCodeWise' => $custom_helper_obj->_getStudentLockSubmttedcourse12AiCodeWise($data->ai_code,$conditionstream),
            );
			
			$i++;
        }
		
		return collect($output);
    
    }
	
	public function headings(): array{
		return ["Sr. No.", "Ai code", "Name","Locked & Submit 10th","Locked & Submit 12th"];
    }	 
	
	 
}