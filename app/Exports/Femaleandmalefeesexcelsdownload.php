<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;
use App\Helper\CustomHelper;
class Femaleandmalefeesexcelsdownload implements FromCollection,WithHeadings
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
		$conditions["exam_year"] = CustomHelper::_get_selected_sessions();
		$result = Student::join('applications', 'applications.student_id', '=', 'students.id')->where('students.exam_year',$conditions)->where('students.gender_id',2)->where('applications.locksumbitted',1)->get();
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'name' => $data['name'],
				'enrollment'=> $data['enrollment'],
				'course'=> $data['course'],
				'gender_id' => ($data['gender_id']==1)? "Male":"Female",
				'medium' => ($data['medium']==1)? "Hindi":"English",
				'locksumbitted' => ($data['locksumbitted']==1)? "Yes":"No",
				'fee amount'=> $data['fee_paid_amount'],
				'challan tid'=> $data['challan_tid'],
				'submitted'=> $data['submitted'],
				
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.", "Name","enrollment","Course","Gender","Medium","Lock & Submit","fee amount","challan tid","submitted",];
    }	 
	
	 
}