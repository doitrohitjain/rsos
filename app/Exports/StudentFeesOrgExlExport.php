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

class StudentFeesOrgExlExport implements FromCollection,WithHeadings
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
		$formId = "Student_Fees_Report";
		$custom_component_obj = new CustomComponent;
		$aiCenters = $custom_component_obj->getAiCenters();
		$custom_controller_obj = new Controller;
		$combo_name = 'gender';$gender = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'categorya';$categorya = $custom_controller_obj->master_details($combo_name);
		$result = $custom_component_obj->getStudentOrgFees($formId, false);
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'enrollment' => $data['enrollment'],
				'name' => $data['name'],
				'course' => $data['course'],
				'category_a' => @$categorya[$data['category_a']],
				'gender_id' => @$gender[$data['gender_id']],
				'ai_code' => @$data['ai_code'],
				'ai_center_name' => @$aiCenters[$data['ai_code']],
				'org_registration_fees' => $data['org_registration_fees'],
				'org_online_services_fees' => $data['org_online_services_fees'],
				'org_add_sub_fees' => $data['org_add_sub_fees'],
				'org_forward_fees' => $data['org_forward_fees'],
				'org_toc_fees' => $data['org_toc_fees'],
				'org_practical_fees' => $data['org_practical_fees'],
				'org_readm_exam_fees' => $data['org_readm_exam_fees'],
				'org_late_fee' => $data['org_late_fee'],
				'org_total' => $data['org_total'],
            );
			$i++;
        }
        
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Enrollment", "Name","Course", "category","gender", "Ai Code", "Ai Code centername", "Registration","Services","ADD Subject Fees","Forward Fees","Toc Fees","Practical Fees","eadm Exam Fees","Late Fees","Total"];
    }	 
	
	 
}

