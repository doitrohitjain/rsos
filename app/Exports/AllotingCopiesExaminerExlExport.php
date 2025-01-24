<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Component\ThoeryCustomComponent;
use Config;
use App\Component\CustomComponent;


class AllotingCopiesExaminerExlExport implements FromCollection,WithHeadings
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
		$formId = "Alloting_Copies_Examiner_List";
		$current_exam_year=Config::get('global.current_admission_session_id');
		$custom_component_obj = new CustomComponent;
		$examiner_list = $custom_component_obj->getExamCentersDropdown();
		$theory_custom_component_obj= new ThoeryCustomComponent;
		$result = $theory_custom_component_obj->getAllotingExaminerList($formId,false);
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
		        'examcenter_detail_id' => @$examiner_list[@$data['examcenter_detail_id']],
				'course_id'=> @$data['course_id'],
				'subject_name'=> @$data['subject_name'],
				'ssoid'=> @$data['ssoid'],
				'name'=> @$data['name'],
				'mobile'=> @$data['mobile'],
				'total_students_appearing'=> @$data['total_students_appearing'],
				'total_copies_of_subject'=> @$data['total_copies_of_subject'],
				'total_absent'=> @$data['total_absent'],
				'total_nr'=> @$data['total_nr'],
				'allotment_date'=> date("d-m-Y h:i:sa", strtotime(@$data['allotment_date'])),
				
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Exam Center Fixcode","Course","Subject","SSO","Examiner Name","Mobile","Total Students Appearing","Total Copies of the subject","Total Absent","Total NR","Date Of Allotment",];
    }	 
	
	 
}