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
use App\Component\ThoeryCustomComponent;
use App\Models\AllotingCopiesExaminer;

class TheoryExaminerMappingexcelExlExport implements FromCollection,WithHeadings
{
	public $custom_component_obj= "";
	public $allotinCopiesexaminer= "";
	public $theory_custom_component_obj= " ";
    function __construct(){
	
		$this->custom_component_obj= new CustomComponent;
		$this->theory_custom_component_obj= new ThoeryCustomComponent;
		$this->allotinCopiesexaminer = new AllotingCopiesExaminer;
	}
	
	
    public function collection(){
		$output = array();
		$formId = "Examiner_Mapping_Theory_Report";
		$custom_component_obj = new CustomComponent;
		$controller_obj = new Controller;
		$combo_name = 'course';$course = $controller_obj->master_details($combo_name);
		$subject_list = $controller_obj->subjectList();
		$result = $this->theory_custom_component_obj->getAllotingExaminerListreports($formId,false); 

		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'examcenter_detail_id' =>$data['examcenter_detail_id'],
				'course_id'=> $course[$data['course_id']],
				'subject_id'=> $subject_list[$data['subject_id']],
				'name' => $data['name'],
				'mobile' => $data['mobile'],
				'total_students_appearing'=> @$data['total_students_appearing'],
				'total_copies_of_subject'=> @$data['total_copies_of_subject'],
				'total_absent'=> @$data['total_absent'],
				'total_nr'=> @$data['total_nr'],
				'allotment_date'=> @$data['allotment_date'],
				
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Exam Center Fixcode","Course","Subject","Examiner Name","Mobile","Total Students Appearing","Total Copies of the subject","Total Absent","Total NR","Date Of Allotment"];
    }	 
	
	 
}


