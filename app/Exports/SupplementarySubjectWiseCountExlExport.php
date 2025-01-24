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

class SupplementarySubjectWiseCountExlExport implements FromCollection,WithHeadings
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
		$formId = "Supplementary_Subject_Wise_Student_Report";
		$custom_component_obj = new CustomComponent;
		$controller_obj = new Controller;
		$result = $custom_component_obj->getsupplementaryStudentCountSubjectWise($formId, false);
		$subject_list =  $controller_obj->subjectList();
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'subject_id' => $subject_list[$data->subject_id],
				'total'=> $data->total,
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Subjects","total students",];
    }	 
	
	 
}