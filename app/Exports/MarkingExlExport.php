<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Component\ThoeryCustomComponent;
use App\Component\CustomComponent;
use Config;

class MarkingExlExport implements FromCollection,WithHeadings
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
		$formId = "Marking_Absent_List";
		$current_exam_year=Config::get('global.current_admission_session_id');
		$theory_custom_component_obj= new ThoeryCustomComponent;
		$custom_component_obj= new CustomComponent;
		$examiner_list=$custom_component_obj->getExamCentersDropdown();
		$result = $theory_custom_component_obj->getMarkingAbsentStudentList($formId,false);
		$exam_year=$theory_custom_component_obj->getDatatomaster('admission_sessions',$current_exam_year);	
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'examcenter_detail_id' =>$examiner_list[$data['examcenter_detail_id']],
				'course_id'=> $data['course_id'],
				'subject_name'=> $data['subject_name'],
				'total_students_appearing'=> $data['total_students_appearing'],
				'total_copies_of_subject'=> $data['total_copies_of_subject'],
				'total_absent'=> $data['total_absent'],
				'total_nr'=> $data['total_nr'],
				
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Exam Center","Course","Subject","Students Appearing","Copies of the subject","Total Absent","Total NR",];
    }	 
	
	 
}