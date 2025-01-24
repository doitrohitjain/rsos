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
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Cache;

class SupplementarieFeesExlExport implements FromCollection,WithHeadings,ShouldAutoSize
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
		$formId = "Supplementaries_Fees_Report";
		$custom_component_obj = new CustomComponent;
		$custom_controller_obj = new Controller;
		$combo_name = 'exam_month';$exam_month = $custom_controller_obj->master_details($combo_name);

		$result = $custom_component_obj->supplementariefeesreports($formId, false);
		
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'ai_code' => $data['ai_code'],
				'exam_month' =>$exam_month[$data['exam_month']],
				'enrollment' => $data['enrollment'],
				'subject_change_fees' => $data['subject_change_fees'],
				'exam_fees' => $data['exam_fees'],
				'practical_fees' => $data['practical_fees'],
				'forward_fees' => $data['forward_fees'],
				'online_fees' => $data['online_fees'],
				'late_fees' => $data['late_fees'],
				'total_fees' => $data['total_fees'],
				'application_fee_date'=> date('d-m-y h:m:s', strtotime($data['application_fee_date']))
            );
			$i++;
        }
		return collect($output);
    
    }
	
	public function headings(): array{
        return ["Sr. No.","Ai Code","Exam Month","enrollment", "Subject Change Fees","Exam Fees","Practical Fees","Forward Fees","Online Fees","Late Fees","Total Fees","Payment Date"];
    }	 
	
	 
}