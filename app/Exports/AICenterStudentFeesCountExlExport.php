<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Session;
use Config;
use App\Component\CustomComponent;
use App\Models\ExamcenterDetail;
use App\Models\Subject;
use App\Exports\AICenterStudentFeesCountExlExport;
use App\Helper\CustomHelper;

class AICenterStudentFeesCountExlExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
	
	
	function __construct($request) {
		//$this->course = $request->course;
		$this->stream = $request->stream;
		$this->exam_year = $request->exam_year;
	 }
	 
	 
	public function collection(){
		ini_set('memory_limit', '3000M');
		ini_set('max_execution_time', '0');

		$stream = $this->stream;
		$examyear = $this->exam_year;
		
		   if($examyear > 123 ){
			$getdata = DB::select("SELECT u.ai_code AS AI_Code, u.college_name AS School_Name, 
			( SELECT count( tb2.ai_code ) FROM rs_students AS tb2 WHERE tb2.ai_code = tb1.ai_code AND tb2.course = 10 AND tb2.exam_year = '$examyear' AND tb2.exam_month = '$stream' AND tb2.is_eligible = 1 AND tb2.deleted_at IS NULL ) AS Tenth, 
			( SELECT count( tb3.ai_code ) FROM rs_students AS tb3 WHERE tb3.ai_code = tb1.ai_code AND tb3.course = 12 AND tb3.exam_year = '$examyear' AND tb3.exam_month = '$stream' AND tb3.is_eligible = 1 AND tb3.deleted_at IS NULL ) AS Twelveth,
			( SELECT sum(fees.total) FROM rs_students AS tb5 
			INNER JOIN rs_student_fees AS fees ON fees.student_id = tb5.id 
			WHERE tb5.ai_code = tb1.ai_code 
			AND tb5.course = 10 
			AND tb5.exam_year = '$examyear'
			AND tb5.exam_month = '$stream'
			AND tb5.is_eligible = 1 
			AND tb5.challan_tid is not null
			AND tb5.deleted_at IS NULL
			AND fees.deleted_at IS NULL ) AS Tenthfees,
			(SELECT sum(fees1.total) FROM rs_students AS tb6 
			INNER JOIN rs_student_fees AS fees1 ON fees1.student_id = tb6.id 
			WHERE tb6.ai_code = tb1.ai_code 
			AND tb6.course = 12
			AND tb6.exam_year = '$examyear'
			AND tb6.exam_month = '$stream'
			AND tb6.is_eligible = 1 
			AND tb6.challan_tid is not null
			AND tb6.deleted_at IS NULL
			AND fees1.deleted_at IS NULL ) AS Twelvethfees,
			( SELECT count( tb2.ai_code ) FROM rs_students AS tb2 WHERE tb2.ai_code = tb1.ai_code AND tb2.course = 10 AND tb2.exam_year = '$examyear' AND tb2.exam_month = $stream AND tb2.is_eligible = 1 AND tb2.deleted_at IS NULL) +
			( SELECT count( tb3.ai_code ) FROM rs_students AS tb3 WHERE tb3.ai_code = tb1.ai_code AND tb3.course = 12 AND tb3.exam_year = '$examyear' AND tb3.exam_month = $stream AND tb3.is_eligible = 1 AND tb3.deleted_at IS NULL ) AS Total,
			( SELECT sum(fees.total) FROM rs_students AS tb5 
			INNER JOIN rs_student_fees AS fees ON fees.student_id = tb5.id 
			WHERE tb5.ai_code = tb1.ai_code 
			AND tb5.course = 10 
			AND tb5.exam_year = '$examyear'
			AND tb5.exam_month = '$stream'
			AND tb5.is_eligible = 1 
			AND tb5.challan_tid is not null
			AND tb5.deleted_at IS NULL
			AND fees.deleted_at IS NULL )+
			(SELECT sum(fees1.total) FROM rs_students AS tb6 
			INNER JOIN rs_student_fees AS fees1 ON fees1.student_id = tb6.id 
			WHERE tb6.ai_code = tb1.ai_code 
			AND tb6.course = 12
			AND tb6.exam_year = '$examyear'
			AND tb6.exam_month = '$stream'
			AND tb6.is_eligible = 1 
			AND tb6.deleted_at IS NULL
			AND tb6.challan_tid is not null
			AND fees1.deleted_at IS NULL ) AS Totalfees
			FROM rs_aicenter_details AS u
			INNER JOIN rs_students AS tb1 ON tb1.ai_code = u.ai_code 
			WHERE tb1.exam_year = '$examyear'
			AND tb1.exam_month = '$stream'
			AND tb1.is_eligible = 1 
			AND tb1.ai_code IS NOT NULL 
			AND tb1.deleted_at IS NULL
			AND u.deleted_at IS NULL
			GROUP BY tb1.ai_code 
			ORDER BY CAST( tb1.ai_code AS SIGNED ) ");   
		   }else{
				$getdata = DB::select("SELECT u.ai_code AS AI_Code, u.college_name AS School_Name, 
			( SELECT count( tb2.ai_code ) FROM rs_students AS tb2 WHERE tb2.ai_code = tb1.ai_code AND tb2.course = 10 AND tb2.exam_year = '$examyear' AND tb2.exam_month = '$stream' AND tb2.submitted is not null  AND tb2.deleted_at IS NULL ) AS Tenth, 
			( SELECT count( tb3.ai_code ) FROM rs_students AS tb3 WHERE tb3.ai_code = tb1.ai_code AND tb3.course = 12 AND tb3.exam_year = '$examyear' AND tb3.exam_month = '$stream' AND tb3.submitted is not null  AND tb3.deleted_at IS NULL ) AS Twelveth,
			( SELECT sum(fees.total) FROM rs_students AS tb5 
			INNER JOIN rs_student_fees AS fees ON fees.student_id = tb5.id 
			WHERE tb5.ai_code = tb1.ai_code 
			AND tb5.course = 10 
			AND tb5.exam_year = '$examyear'
			AND tb5.exam_month = '$stream'
			AND tb5.submitted is not null  
			AND tb5.deleted_at IS NULL
			AND fees.deleted_at IS NULL ) AS Tenthfees,
			(SELECT sum(fees1.total) FROM rs_students AS tb6 
			INNER JOIN rs_student_fees AS fees1 ON fees1.student_id = tb6.id 
			WHERE tb6.ai_code = tb1.ai_code 
			AND tb6.course = 12
			AND tb6.exam_year = '$examyear'
			AND tb6.exam_month = '$stream'
			AND tb6.submitted is not null  
			AND tb6.deleted_at IS NULL
			AND fees1.deleted_at IS NULL ) AS Twelvethfees,
			( SELECT count( tb2.ai_code ) FROM rs_students AS tb2 WHERE tb2.ai_code = tb1.ai_code AND tb2.course = 10 AND tb2.exam_year = '$examyear' AND tb2.exam_month = $stream AND tb2.submitted is not null  AND tb2.deleted_at IS NULL) +
			( SELECT count( tb3.ai_code ) FROM rs_students AS tb3 WHERE tb3.ai_code = tb1.ai_code AND tb3.course = 12 AND tb3.exam_year = '$examyear' AND tb3.exam_month = $stream AND tb3.submitted is not null  AND tb3.deleted_at IS NULL ) AS Total,
			( SELECT sum(fees.total) FROM rs_students AS tb5 
			INNER JOIN rs_student_fees AS fees ON fees.student_id = tb5.id 
			WHERE tb5.ai_code = tb1.ai_code 
			AND tb5.course = 10 
			AND tb5.exam_year = '$examyear'
			AND tb5.exam_month = '$stream'
			AND tb5.submitted is not null  
			AND tb5.deleted_at IS NULL
			AND fees.deleted_at IS NULL )+
			(SELECT sum(fees1.total) FROM rs_students AS tb6 
			INNER JOIN rs_student_fees AS fees1 ON fees1.student_id = tb6.id 
			WHERE tb6.ai_code = tb1.ai_code 
			AND tb6.course = 12
			AND tb6.exam_year = '$examyear'
			AND tb6.exam_month = '$stream'
			AND tb6.submitted is not null 
			AND tb6.deleted_at IS NULL
			AND fees1.deleted_at IS NULL ) AS Totalfees
			FROM rs_aicenter_details AS u
			INNER JOIN rs_students AS tb1 ON tb1.ai_code = u.ai_code 
			WHERE tb1.exam_year = '$examyear'
			AND tb1.exam_month = '$stream'
			AND tb1.submitted is not null 
			AND tb1.ai_code IS NOT NULL 
			AND tb1.deleted_at IS NULL
			AND u.deleted_at IS NULL
			GROUP BY tb1.ai_code 
			ORDER BY CAST( tb1.ai_code AS SIGNED ) ");      
		   }
	
		    
			$i =1 ;
			foreach($getdata as $examcenter){
				$output[] = array(
					'id' => $i,
					'AI_Code' => @$examcenter->AI_Code,
					'School_Name' => @$examcenter->School_Name,
					'Tenth' => @$examcenter->Tenth,
					'Twelveth' => @$examcenter->Twelveth,
					'Tenthfees' => @$examcenter->Tenthfees,
					'Twelvethfees' => @$examcenter->Twelvethfees,
					'Total' => @$examcenter->Total,
					'Totalfees' => @$examcenter->Totalfees,
				);
				$i++;
			}
      return collect($output);
	}

	
	public function headings(): array{

      return ["Sr. No.","AI Code","School Name","10th","12th","10thfees","12thfees","Total","Total Fees"];	
		}
        
}