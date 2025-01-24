<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;
use Config;
use App\Http\Controllers\Controller;

class BoardNrStudentEnrollmentExcel implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
	
	
	   protected $id;
		function __construct($id) {
        $this->id = $id;
		}
		
	public function collection(){
		$output = array();
		$formId = "Admission_Report";
		$course = $this->id;
		$stream = Config::get("global.defaultStreamId");
		$exam_year = Config::get("global.current_admission_session_id");
		$controller_obj = new Controller;
		$spSuppName = $controller_obj->_getBoardNRReportData($course,$stream,$exam_year);		
		$spStreamName = "Call GetSubjectwiseExamcenterReport(" .$course .",".$stream.",".$exam_year.")";
		//$spSuppName = "Call GetSupplementarySubjectwiseExamcenterReport(" . $course . ",".$stream.",".$exam_year.")";
		$examcenters = DB::select($spStreamName);

		//$suppexamCenters = DB::select($spSuppName);;
		$i =1 ;
		foreach(@$spSuppName as $result){
			dd($result);
            $output[] = array(
				'id' => $i,
				'cent_Name' => $result->cent_Name,
				'CenterCode' => $result->CenterCode,
				'district' => $result->district_id
            );
			$i++;
        }
		
		
		
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Center Name", "Center Code","District"];
    }	 
	
	 
}