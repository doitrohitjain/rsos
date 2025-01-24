<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;
use App\Component\RevalMarksComponent;
use App\Http\Controllers\Controller;

class RevalGeneratExlExport implements FromCollection,WithHeadings
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
		$subject_id=0;
		$reval_marks_component_obj = new RevalMarksComponent;
		$custom_controller_obj = new Controller;
		$formId = Session::get('formId');
		$combo_name = 'exam_month';$exam_month_master = $custom_controller_obj->master_details($combo_name);
		$combo_name = 'reval_exam_year';$reval_exam_year =  $custom_controller_obj->master_details($combo_name);
		$combo_name = 'reval_exam_month';$reval_exam_month =  $custom_controller_obj->master_details($combo_name);
		$reval_exam_year = @$reval_exam_year[1];
		$reval_exam_month = @$reval_exam_month[1];
		$resultsyntax = array('999' => 'AB','666' => 'SYCP','777' => 'SYCT','888'=>'SYC','P'=>'PASS','p'=>'PASS');
		$combo_name = 'admission_sessions';$admission_sessions = $custom_controller_obj->master_details($combo_name);
		$subjectCodes = $custom_controller_obj->subjectCodeList();
		$result = $reval_marks_component_obj->getRevalMarksPdfData($subject_id,$formId); 
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'enrollment' => $data['enrollment'],
				'studentfixcode' => $data['studentfixcode'],
				'centerfixcode' => $data['centerfixcode'],
				'subject_id' => @$subjectCodes[$data['subject_id']],
				'marks_on_answer_book_before_reval' => @$data['marks_on_answer_book_before_reval'],
				'final_theory_marks' =>[$data['final_theory_marks']],
				'sessional_marks' =>(@$data['sessional_marks'] == 999)? "0":@$data['sessional_marks'],
				'final_practical_marks' =>(@$data['sessional_marks'] == 999)? "0":@$data['final_practical_marks'],
				'total_marks' => $data['total_marks'],
				'final_result' =>@$resultsyntax[$data['final_result']],
				
            );
			$i++;
        }
        
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Enrollment", "Student Fixcode","Center Fixcode", "Marks On Answer Book before reval","Marks On Answer Book after reval", "Remarks", "Theory Marks in Result", "Sessional Marks in Result","Practical Marks in Result","Total Marks in Result","Final Result in Result"];
    }	 
	
	 
}

