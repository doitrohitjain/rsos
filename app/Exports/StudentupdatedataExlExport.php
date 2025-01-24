<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use App\Models\StudentUpdate;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;
use App\Http\Controllers\Controller;

class StudentupdatedataExlExport implements FromCollection,WithHeadings
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
		$result = StudentUpdate::
		Join('students', 'students.id', '=', 'student_updates.student_id')->where('student_updates.is_update', 1)
		->LeftJoin('student_fees', 'student_fees.student_id', '=', 'student_updates.student_id')->where('student_updates.is_update', 1)
		->whereNull('student_fees.student_id')
		->get(["student_updates.student_id","student_updates.is_update","student_updates.created_at","student_fees.updated_at","students.name","students.enrollment",]);

		
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'enrollment' => @$data['enrollment'],
				'name' => @$data['name'],
				'student_id' => $data['student_id'],
				'is_update'=> $data['is_update'],
				'created_at'=> $data['created_at'],
				'updated_at'=> $data['updated_at'],
			
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.","Enrollment","Name","Student_id","is_update","created_at","updated_at"];
    }	 
	
	 
}