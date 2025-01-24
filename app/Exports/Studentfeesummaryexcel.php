<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;

class Studentfeesummaryexcel implements FromCollection,WithHeadings
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
		$formId = "Student_Fees_Summary_Report";
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->getStudentFeeSummaryPay($formId, false);
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'college_name' => $data->college_name,
				'ai_code' => $data->ai_code,
				'number_of_student' => $data->number_of_student,
				'registration_fees' => $data->registration_fees,
				'online_services_fees' => $data->online_services_fees,
				'add_sub_fees' => $data->add_sub_fees,
				'forward_fees' => $data->forward_fees,
				'toc_fees' => $data->toc_fees,
				'practical_fees' => $data->practical_fees,
				'readm_exam_fees' => $data->readm_exam_fees,
				'late_fee' => $data->late_fee,
				'total' => $data->total,
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "College Name", "Number Of Student","Ai Code","Registration","Services","ADD Subject Fees","Forward Fees","Toc Fees","Practical Fees","eadm Exam Fees","Late Fees","Total"];
    }	 
	
	 
}


