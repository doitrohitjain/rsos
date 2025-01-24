<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;
use App\Http\Controllers\Controller;

class FreshStudentVerifyingExlExport implements FromCollection,WithHeadings,ShouldAutoSize
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
		$role_id = @Session::get('role_id'); 
		$aicenter_id = config("global.aicenter_id");
		$super_admin_id = config("global.super_admin_id");
		$academicofficer_id = config("global.academicofficer_id");
		$title = "Fresh Student Verification";
        $formId = ucfirst(str_replace(" ","_",$title));
		$output = array();
		$custom_controller_obj = new Controller;
		$combo_name = 'gender';$gender = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'midium';$midium = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'adm_type';$adm_types = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'yes_no_2';$yesno = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'stream_id';$stream_id = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'fresh_student_verfication_status';$fresh_student_verfication_status = $custom_controller_obj->master_details($combo_name);
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->getVerifyStudentData($formId, false);
		$AiCentersWithdistrictname = $custom_component_obj->getAiCentersWithdistrictname();
		$i =1 ;
		
		foreach($result as $data){
            $output[$i] = array(
				'id' => $i,
				'name' => @$data['name'],
                'ssoid' => @$data['ssoid'],
				'mobile' => @$data['mobile'],
				'ai_code'=> @$AiCentersWithdistrictname[@$data['ai_code']],
				'ai_code'=>  @$data['ai_code'],
				'enrollment'=> @$data['enrollment'],
				'course'=> @$data['course'],
				'gender_id' => @$gender[$data['gender_id']], 
            ); 
			if($role_id == $super_admin_id || $role_id == $academicofficer_id){  
				$output[$i]['medium'] = (@$data['medium']==1)? "Hindi":"English";
				$output[$i]['is_self_filleḍ'] = @$yesno[$data['is_self_filled']];
				$output[$i]['submitted'] =  @$data['submitted'];
			}

			$output[$i]['is_verifier_verify'] = @$yesno[$data['is_verifier_verify']];
			$output[$i]['verifier_status'] = @$fresh_student_verfication_status[@$data['verifier_status']];
			$output[$i]['verifier_verify_datetime'] = @$data['verifier_verify_datetime'];

			
			if($role_id == $super_admin_id || $role_id == $academicofficer_id){  
				$output[$i]['is_ao_verify'] = @$yesno[$data['is_ao_verify']];
				$output[$i]['ao_status'] = @$fresh_student_verfication_status[@$data['ao_status']];
				$output[$i]['ao_verify_datetime'] = @$data['ao_verify_datetime'];

				$output[$i]['is_department_verify'] = @$yesno[$data['is_department_verify']];
				$output[$i]['department_status'] = @$fresh_student_verfication_status[@$data['department_status']];
				$output[$i]['department_verify_datetime'] = @$data['department_verify_datetime'];
			}
			$i++;
        }
 
		return collect($output); 
    }
	
	public function headings(): array{
		$role_id = @Session::get('role_id'); 
		$aicenter_id = config("global.aicenter_id");
		$academicofficer_id = config("global.academicofficer_id");
		$super_admin_id = config("global.super_admin_id");
		$fields = array("Sr. No.", "Name","SSO","Mobile Number","Ai Code","AI Center District Name","Enrollment","Course","Gender");
		if($role_id == $super_admin_id || $role_id == $academicofficer_id){ 
			$fields[] = "Medium";
			$fields[] = "Is Self Filled"; 
			$fields[] = "Submitted"; 
		}

		$fields[] = "Verifier Verify";
		$fields[] = "Verifier Status";
		$fields[] = "Verifier Verify Datetime";
		
		if($role_id == $super_admin_id || $role_id == $academicofficer_id){ 
			$fields[] = "Academic Officer Verify";
			$fields[] = "Academic Officer Status";
			$fields[] = "Academic Officer Verify Datetime";

			$fields[] = "Department Verify";
			$fields[] = "Department Status";
			$fields[] = "Department Verify Datetime";
		}
		return $fields;
    }	 
	
	 
}