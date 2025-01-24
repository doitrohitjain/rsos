<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\ExamcenterDetail;
use App\Models\Subject;
use Session;
use App\Component\practicalCustomComponent;
use App\Component\CustomComponent;
use Config;

class PracticalExaminerListExport implements FromCollection,WithHeadings
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
		$title = "Practical Examiner Details";
		$formId = ucfirst(str_replace(" ","_",$title));
		
		$controllerObj = new Controller;
		$combo_name = 'exam_session';$exam_month_session = $controllerObj->master_details($combo_name);
		$combo_name = 'admission_sessions';$exam_year_session  = $controllerObj->master_details($combo_name);
		
		//$customComponent = new CustomComponent;
		//$master = $customComponent->getUsersData($formId);
		
		$practical_component_obj = new PracticalCustomComponent;
		$master = $practical_component_obj->getDeoExaminerListOrCount($formId);
		
		$i =1 ;
		foreach($master as $data){
			$roles = $data->getRoleNames()->toArray();
			$roles = implode(" ,",$roles);
			
			$exam_month_year = $exam_year_session[$data->exam_year]."/".@$exam_month_session[$data->exam_month];
			
			$output[] = array(
				'id' => $i,
				'ssoid' => @$data->ssoid,
				'email' => @$data->email,
				'roles' => @$roles,
				// 'exam_month_year' => @$exam_month_year,
				'college_name' => @$data->college_name,
			);
			$i++;
        }
		return collect($output);
    }
	
	public function headings(): array{
		// "Session Year/Month",
        return ["Sr. No.", "SSOID", "Email", "Roles","School Name"];
    }	 

}