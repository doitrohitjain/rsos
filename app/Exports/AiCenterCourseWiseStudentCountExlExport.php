<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use Config;
use App\Component\CustomComponent;
use App\Models\ExamcenterDetail;
use App\Models\Subject;
use App\Exports\CenterCountExlExport;
use App\Helper\CustomHelper;

class AiCenterCourseWiseStudentCountExlExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
	
	function __construct($request) {
		$this->stream = $request->stream;
	
	 
	 }
	 
	public function collection(){
		ini_set('memory_limit', '3000M');
		ini_set('max_execution_time', '0');
 
		$examyear = CustomHelper::_get_selected_sessions();
		$stream =  $this->stream;
	    $aicenter_stream_subject_data = DB::select("SELECT s.ai_code,ad.college_name, SUM(CASE WHEN s.course =10 THEN 1 ELSE 0 END) as Course10, SUM(CASE WHEN s.course =12 THEN 1 ELSE 0 END) as Course12, count(*) as Total FROM rs_students s INNER JOIN rs_aicenter_details ad ON ad.ai_code =s.ai_code INNER JOIN rs_applications app ON app.student_id=s.id INNER JOIN rs_toc t ON t.student_id=s.id WHERE s.exam_year = '$examyear' AND s.exam_month='$stream' AND s.deleted_at is null AND app.deleted_at is null AND t.deleted_at is null and s.is_eligible=1 and ad.active=1 and app.toc=1 and t.exam_year = '$examyear' AND t.exam_month='$stream' and ad.deleted_at is null group by ad.ai_code");
		
		$i =1 ;
				foreach($aicenter_stream_subject_data as $data){
					$output[] = array(
						'id' => $i,
						'ai_code' => @$data->ai_code,
					    'college_name' => @$data->college_name,
						'Course10' => @$data->Course10,
						'Course12' => @$data->Course12,
						'Total' => @$data->Total,
					);
					$i++;
				}
		
		return collect($output);
	}

	
	public function headings(): array{

      return ["Sr. No.","Ai_Code","College Name","Course10","Course12","Total"];	
		
        
    }

	 
}