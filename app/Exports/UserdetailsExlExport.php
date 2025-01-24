<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;
use App\Models\User;

class UserdetailsExlExport implements FromCollection,WithHeadings
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
		$formId = "Users_Details";
		$custom_component_obj = new CustomComponent;
		$result = $custom_component_obj->getUsersData($formId, false);
		$i =1 ;
        $exam_year_session = DB::table('masters')->where('status',1)->where('combo_name','admission_sessions')->get()->pluck('option_val','option_id'); ;
		$exam_month_session = DB::table('masters')->where('status',1)->where('combo_name','exam_session')->get()->pluck('option_val','option_id'); ;
		
		foreach($result as $key => $user){
			$roles = $user->getRoleNames()->toArray();
			$roles = implode(" ,",$roles);
            $output[] = array(
				'id' => $i,
				'ssoid' => $user['ssoid'],
				'email' => $user['email'],
				'Roles' => $roles,
				'exam_year' => @$exam_year_session[$user->exam_year],
				'exam_month' => @$exam_month_session[$user->exam_month],
				
            );
			$i++;
		}
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "ssoid", "email","Roles","exam_year","exam_month"];
    }	 
	
	 
}

