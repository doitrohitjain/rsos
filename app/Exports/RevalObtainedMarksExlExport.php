<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Http\Controllers\Controller;
use App\Component\RevalMarksComponent;
use App\Models\User;

class RevalObtainedMarksExlExport implements FromCollection,WithHeadings
{						
	public function collection(){
		$output = array();
		$formId = "Reval_Student_Subject_Obtained_Marks_Entries"; 
		$reval_marks_component_obj = new RevalMarksComponent;
		$controllerObj = new Controller;
		
		$result = $reval_marks_component_obj->getRevalMarksApplicationData($formId, false); 
		
		
		$i =1 ; 
		
		$subject_list =  $controllerObj->subjectList();
		$subjectCodes = $controllerObj->subjectCodeList();
		$combo_name = 'gender';$gender_id = $controllerObj->master_details($combo_name);
		$yes_no = $controllerObj->master_details('yesno');
		
		foreach($result as $key => $v){ 
			$final_result = "N/A";$fld='final_result';  
			if(isset($resultsyntax[@$v->$$fld])){
				$final_result = $resultsyntax[@$v->$$fld];  
			}  
			$final_result_after_reval = "N/A";$fld='final_result_after_reval';  
			if(isset($resultsyntax[@$v->$$fld])){
				$final_result_after_reval = $resultsyntax[@$v->$$fld];  
			}  
			if( @$v->final_practical_marks == 999){
				$v->final_practical_marks = 0; 
			}
			if( @$v->sessional_marks == 999){
				$v->sessional_marks = 0; 
			} 
            $output[] = array(
				'Sr.No.' => $i,
				'Enrollment' =>  @$v->enrollment,
				'Student Fixcode' => @$v->studentfixcode,
				'Center Fixcode' => @$v->centerfixcode,
				'Sub Code' =>@$subjectCodes[$v->subject_id], 
				'Marks on the Answer sheet before Reval' => @$v->marks_on_answer_book_before_reval,
				'Marks on the Answer sheet after Reval' => @$v->theory_marks_in_reval,
				'Remarks' => @$v->reval_type_of_mistake,  
				'Theory Marks in result before Reval' => @$v->final_theory_marks,
				'Theory Marks in the result after Reval' => @$v->final_theory_marks_after_reval, 
				'Sessional Marks in the Result' => @$v->sessional_marks,
				'Practical Marks in the Result' => @$v->final_practical_marks,
				'Total Marks before Reval' => @$v->total_marks,
				'Total Marks after Reval' => @$v->total_marks_after_reval,
				'Final Result before Reval' => @$final_result,
				'Final Result after Reval' => @$final_result_after_reval,
            ); 
			 
			 
			$i++;
		}
		return collect($output); 
    } 
	public function headings(): array{
        return ['Sr.No.','Enrollment','Student Fixcode','Center Fixcode','Sub Code','Marks on the Answer sheet before Reval','Marks on the Answer sheet afterReval','Remarks' ,  'Theory Marks in result before Reval','Theory Marks in the result after Reval','SessionalMarks in the Result','Practical Marks in the Result','Total Marks before Reval','Total Marks after Reval','Final Result beforeReval','Final Result after Reval'];
    }	 
	 
}

