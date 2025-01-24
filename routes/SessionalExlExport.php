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

class SessionalExlExport implements FromCollection,WithHeadings
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
		$formId = "Sessional_Report";
		$custom_controller_obj = new Controller;
		$subject_list_name =  $custom_controller_obj->subjectListName();
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->getSessionalDataForExcel($formId, false);
		$result = $result->toArray();
		$fianlArr = null;
		foreach(@$result as $k => $data){
			$fianlArr[$data['id']]['name'] = $data['name'];
			$fianlArr[$data['id']]['ai_code'] = $data['ai_code'];
			$fianlArr[$data['id']]['enrollment'] = $data['enrollment'];
			$fianlArr[$data['id']]['is_sessional_mark_entered'] = "No";
			if($data['is_sessional_mark_entered']){
				$fianlArr[$data['id']]['is_sessional_mark_entered'] = "Yes";
			}
			$fianlArr[$data['id']]['subjects'][] = $subject_list_name[$data['subject_id']]." (".$data['sessional_marks'] . ")";
		}
		$i =1 ;
		if(isset($fianlArr) && !empty($fianlArr)){
			foreach(@$fianlArr as $k => $data){
				$output[$k]['id'] = $i;
				$output[$k]['name'] = $data['name'];
				$output[$k]['ai_code'] = $data['ai_code'];
				$output[$k]['enrollment'] = $data['enrollment']; 
				$output[$k]['is_sessional_mark_entered'] = $data['is_sessional_mark_entered'];
				$subjects = $data["subjects"];
				$counter = count($subjects);
				$count=0;
				foreach(@$subjects as $subject){
					$count++;
					$output[$k]['subjects'.$count] = $subject . "";
				} 
				$i++;
			}
		}
		return collect(@$output);
    }
	
	public function headings(): array{
        return ["Sr. No.", "Name",'Ai Code',"enrollment",'Is sessional Mark Entered',"subject1","subject2","subject3","subject4","subject5","subject6","subject7"];
    }	 
	
	 
}