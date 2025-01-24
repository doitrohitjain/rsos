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

class StudentAicenterWiseCountExlExport implements FromCollection,WithHeadings
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
				'district_name' => @$district_list[@$data['district_id']],
				'student_all_by_aicode_count' => $custom_helper_obj->_getStudentAiCodeWise($data->ai_code,$conditionstream),
				'student_lock_submit_by_aicode_count' => $custom_helper_obj->_getStudentLockSubmttedAiCodeWise($data->ai_code,$conditionstream),
				'student_non_lock_submit_by_aicode_count' => $custom_helper_obj->_getStudentAiCodeWise($data->ai_code,$conditionstream) - $custom_helper_obj->_getStudentLockSubmttedAiCodeWise($data->ai_code,$conditionstream),
				'student_fee_paid' => $custom_helper_obj->_getStudentFeePaidAiCodeWise($data->ai_code,$conditionstream),
				'student_locked_but_notfee_paid' => $custom_helper_obj->_getStudentnotFeePaidAiCodeWisebutlocksumbitted($data->ai_code,$conditionstream),
				
            );
			
			$i++;
        }
		
		return collect($output);
    
    }
	
	public function headings(): array{
		return ["Sr. No.", "Ai code", "Name","District","Registred","Locked & Submit","Not Locked & Submit","Fee Paid","Locked But Not Fee Paid"];
    }	 
	
	 
}