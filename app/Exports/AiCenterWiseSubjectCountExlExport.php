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

class AiCenterWiseSubjectCountExlExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
	
	
	
	function __construct($data) {
        $this->data = $data;
	}
	
	
    public function collection(){
		$results=$this->data;
		$i =1 ;
		foreach($results  as $data){
			$output[] = array(
			   "ai_code"=>@$data['Ai_code'],
			   "Course10"=>@$data['Course10'],
			   "Course12"=>@$data['Course12'],
			   "Total"=>@$data['Total'],
			);
			$i++;
			
		}
		
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Ai Code", "Course10","Course12","Total"];
    }	 
	
	 
}