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

class StudentFeesExlExport implements FromCollection,WithHeadings
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
		$result = $custom_component_obj->getStudentFees($formId, false);
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
				'registration_fees' => $data['registration_fees'],
				'online_services_fees' => $data['online_services_fees'],
				'add_sub_fees' => $data['add_sub_fees'],
				'forward_fees' => $data['forward_fees'],
				'toc_fees' => $data['toc_fees'],
				'practical_fees' => $data['practical_fees'],
				'readm_exam_fees' => $data['readm_exam_fees'],
				'late_fee' => $data['late_fee'],
				'total' => $data['total'],
            );
			$i++;
        }
        
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Enrollment", "Name","Course", "category","gender", "Ai Code", "Ai Code centername", "Registration","Services","ADD Subject Fees","Forward Fees","Toc Fees","Practical Fees","eadm Exam Fees","Late Fees","Total"];
    }	 
	
	 
}

