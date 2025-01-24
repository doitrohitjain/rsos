<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Logs;
use Session;
use App\Component\CustomComponent;

class LogsExlExport implements FromCollection,WithHeadings
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
		$formId = "Logs_Details";
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->getLogsData($formId, false);
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'user_id' => $data['user_id'],
				'log_date' => $data['log_date'],
				'table_name' => $data['table_name'],
				'log_type' => $data['log_type'],
				'data' => $data['data'],
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "User Id", "Log Date","Table Name","Log Type","All Data"];
    }	 
	
	 
}