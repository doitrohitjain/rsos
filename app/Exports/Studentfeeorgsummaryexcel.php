<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;

class Studentfeeorgsummaryexcel implements FromCollection,WithHeadings
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
		$formId = "Student_Fees_Org_Summary_Report";
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->getStudentFeeSummaryOrg($formId, false);
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'college_name' => $data->college_name,
				'ai_code' => $data->ai_code,
				'number_of_student' => $data->number_of_student,
				'org_registration_fees' => $data->org_registration_fees,
				'org_online_services_fees' => $data->org_online_services_fees,
				'org_add_sub_fees' => $data->org_add_sub_fees,
				'org_forward_fees' => $data->org_forward_fees,
				'org_toc_fees' => $data->org_toc_fees,
				'org_practical_fees' => $data->org_practical_fees,
				'org_readm_exam_fees' => $data->org_readm_exam_fees,
				'org_late_fee' => $data->org_late_fee,
				'org_total' => $data->org_total,
				'registration_fees' => $data->registration_fees,
				'online_services_fees' => $data->online_services_fees,
				'add_sub_fees' => $data->add_sub_fees,
				'forward_fees' => $data->forward_fees,
				'toc_fees' => $data->toc_fees,
				'practical_fees' => $data->practical_fees,
				'readm_exam_fees' => $data->readm_exam_fees,
				'late_fee' => $data->late_fee,
				'total' => $data->total,
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "College Name", "Number Of Student","Ai Code","Org Registration"," OrgServices","Org ADD Subject Fees","Org Forward Fees","Org Toc Fees","Org Practical Fees","Org readm Exam Fees"," Org Late Fees","Org Total","Registration","Services","ADD Subject Fees","Forward Fees","Toc Fees","Practical Fees","readm Exam Fees","Late Fees","Total"];
    }	 
	
	 
}


