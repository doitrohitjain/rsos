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

class supplementarieArieaicenterWiseCountexcelExport implements FromCollection,WithHeadings
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
		$formId = "AiCenters_Wise_Supplementarie_Report";
		$custom_component_obj = new CustomComponent;
		$custom_helper_obj = new CustomHelper;
		$result = $custom_component_obj->getSupplementarCountAiCenterWise($formId, false);
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'ai_code' => $data['ai_code'],
				'college_name' => $data['college_name'],
				'student_all_by_aicode_count' => $custom_helper_obj->_getStudentSupplementaryAiCodeWise($data->ai_code),
				'student_lock_submit_by_aicode_count' => $custom_helper_obj->_getSupplementaryStudentLockSubmttedAiCodeWise($data->ai_code),
				'student_non_lock_submit_by_aicode_count' => $custom_helper_obj->_getStudentSupplementaryAiCodeWise($data->ai_code) - $custom_helper_obj->_getSupplementaryStudentLockSubmttedAiCodeWise($data->ai_code),
				'student_fee_paid' => $custom_helper_obj->_getSupplementaryStudentFeePaidAiCodeWise($data->ai_code),
				'student_locked_but_notfee_paid' => $custom_helper_obj->_getSupplementaryStudentLockSubmttedAiCodeWise($data->ai_code) - $custom_helper_obj->_getSupplementaryStudentFeePaidAiCodeWise($data->ai_code),
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
		return ["Sr. No.", "Ai code", "Name", "Registred","Locked & Submit","Not Locked & Submit","Fee Paid","Locked But Not Fee Paid"];
    }	 
	
	 
}