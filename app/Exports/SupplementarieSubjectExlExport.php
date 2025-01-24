<?php
namespace App\Exports;
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;
use Cache;

class SupplementarieSubjectExlExport implements FromCollection,WithHeadings
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
	 public function master_details($combo_name=null)
    {
		$condtions = null;
		$result = array();
		if(!empty($combo_name)){
			$condtions = ['status' => 1,'combo_name' => $combo_name];
		} 
		$mainTable = "masters"; 
		$cacheName = $mainTable. "_".$combo_name;
		Cache::forget($cacheName);
		if (Cache::has($cacheName)) { //Cache::forget($mainTable);
			$result = Cache::get($cacheName);
		}else{
			$result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) { 

				$result = DB::table($mainTable)->where($condtions)->get()->pluck('option_val','option_id'); 
				 
				return $result;
			});			
		}  
		return $result;
	}
	
    public function collection(){
		 $combo_name = 'categorya';$categorya = $this->master_details($combo_name);
		 $combo_name = 'stream_id';$stream_id = $this->master_details($combo_name);
		 $combo_name = 'adm_type';$adm_types = $this->master_details($combo_name);
		 $combo_name = 'course';$course = $this->master_details($combo_name);
		$output = array();
		$formId = "Supplementarie_Subject_Report";
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->supplementariesubjectreports($formId, false);
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'enrollment' => $data['enrollment'],
				'name' => $data['name'],
				'gender_id' => ($data['gender_id']==1)? "Male":"Female",
				'adm_type' => $data['adm_type'],
				'stream' => $data['stream'],
				'course' => $data['course'],
				'category_a' => $data->category_a
				
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "enrollment", "Name","Gender","Admission type","stream","course","Category"];
    }	 
	
	 
}