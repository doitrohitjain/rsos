<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;

class supplementaryexamcenterwiseexcel implements FromCollection,WithHeadings
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
		$formId = "Exam_Center_Student_Allotment";
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->getExamcenterAllotmentDataForReport($formId, false);
		$i =1 ;
		foreach($result as $data){
		  foreach ($data->examcenterallotments as $examcenterallotmentsdetails){
            $output[] = array(
				'id' => $i,
				'cent_name' => $data['cent_name'],
				'ecenter10' => $data['ecenter10'],
				'ecenter12' => $data['ecenter12'],
				'capacity' => $data['capacity'],
				'ai_code' => $examcenterallotmentsdetails['ai_code'],
				'student_supp_10' => $examcenterallotmentsdetails['student_supp_10'],
				'student_supp_12' => $examcenterallotmentsdetails['student_supp_12'],
				'total' => $examcenterallotmentsdetails['student_supp_10'] + $examcenterallotmentsdetails['student_supp_12'],
				
            );
			$i++;
        }
    }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Center_name","ecenter10", "ecenter12","capacity","ai_code","student_supp_10","student_supp_12","total"];
    }	 

}