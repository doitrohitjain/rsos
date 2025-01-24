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


class TheoryExaminerExlExport implements FromCollection,WithHeadings
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
		$title = "Theory Examiner List";
		$formId = ucfirst(str_replace(" ","_",$title));
		$current_exam_year=Config::get('global.current_admission_session_id');
		$custom_component_obj = new CustomComponent;
		$examiner_list = $custom_component_obj->getExamCentersDropdown();
		$theory_custom_component_obj= new ThoeryCustomComponent;
		$result = $theory_custom_component_obj->examiner_list($formId,false);
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
		        'Examiner Name ' => @$data['name'],
				'SSO'=> strtoupper(@$data['ssoid']),
				'Mobile'=> @$data['mobile'],
				'Designation'=> @$data['designation'],
				
            );
			$i++;
        }
		
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Examiner Name","SSO","Mobile","Designation"];
    }	 
	
	 
}