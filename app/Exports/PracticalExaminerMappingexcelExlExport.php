<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;
use App\Component\PracticalCustomComponent;
use App\Http\Controllers\Controller;

class PracticalExaminerMappingexcelExlExport implements FromCollection,WithHeadings
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
		$title = "Subject Practical Examiner Status Report";
        $formId = ucfirst(str_replace(" ","_",$title));
		$custom_component_obj = new CustomComponent;
		$practicalCustomComponent = new practicalCustomComponent();	
		$controller_obj = new Controller;
		$subject_list = $controller_obj->subjectList();
		$yes_no = $controller_obj->master_details('yesno'); 
		$user_deo_id = $practicalCustomComponent->getDEOList(); 
		$deo_district_id = $controller_obj->districtsByState(6);
		$combo_name = 'course';$course = $controller_obj->master_details($combo_name);
		$examCenterList =  $examcenter_datails_dropdown = collect($custom_component_obj->getExamCenterWithBothCourseCode()); 



		$user_practical_examiner_id = $practicalCustomComponent->getPracticalExaminerList();

		$result = $practicalCustomComponent->getPracticalExaminerMapping($formId,false);
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => @$i,
				'subject_id' =>@$subject_list[@$data['subject_id']],
				'user_deo_id'=> @$user_deo_id[@$data['user_deo_id']],
				'user_practical_examiner_id'=> @$user_practical_examiner_id[@$data['user_practical_examiner_id']],
				'course' => @$course[@$data['course']],
				'examcenter_detail_id' => @$examCenterList[@$data['examcenter_detail_id']],
				'is_lock_submit'=> @$yes_no[@$data['is_lock_submit']],
				'is_signed_pdf'=> @$yes_no[@$data['is_signed_pdf']],
				
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Subject Name","DEO","Examiner Practical","Course","Exam Center","Is Lock & Submit","Is Signed PDF"];
    }	 
	
	 
}