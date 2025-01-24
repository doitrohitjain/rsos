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

class AicentersubjectCountStudentExlExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
	
	
	function __construct($request) {
       $this->course = $request->course;
       $this->stream = $request->stream;
       $this->midium = $request->midium;
    
	}
	
	   public function collection(){
		ini_set('memory_limit', '3000M');
		ini_set('max_execution_time', '0');

		$examyear = CustomHelper::_get_selected_sessions();
	
		$course =  $this->course;
		$stream =  $this->stream;
		$midium =  $this->midium;
		
		$custom_component_obj = new CustomComponent;
		$subjectList = $custom_component_obj->subjectListcode($course);

		$aicenters = $custom_component_obj->getAiCenters();

      	$k=1;
        foreach($aicenters  as $aicenterid => $aicentervalue){

			$final_data = array();
			
			$final_data[$k]['ai_code']=@$aicenterid;
			$final_data[$k]['aicentername']=@$aicentervalue;
			$i = 0;
			foreach($subjectList  as $subjectid => $subjectname){

			$conditions["students.exam_year"] = $examyear;
			$conditions["students.stream"] = $stream;
			$conditions["students.course"] = $course;
			$conditions["applications.medium"] = $midium;
			$conditions["students.ai_code"] = $aicenterid;
			$conditions["students.is_eligible"] = 1;
			$conditions['exam_subjects.subject_id'] = $subjectid;
			

				$studentData = array();
				$studentData = DB::table('students')->join('exam_subjects', 'exam_subjects.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')->where($conditions)->whereNull('students.deleted_at')->whereNull('applications.deleted_at')->whereNull('exam_subjects.deleted_at')->get()->count();
         		$data = $studentData;
					
					//$final_data[$k]['subject_id'] = $subjectid;
					$final_data[$k]['subject_'.$subjectList[$subjectid]]=$data;
					//$final_data[$subjectid]=$data;
					$i++;

				}

			
        $k++;
    	$p = 1 ;
		foreach($final_data as $datakey=>$dataval){
			$subjectstr = array('id'=>$k-1);
			unset($dataval['subject_id']);
			foreach($dataval as $dkey=>$dval)
			{
				$subjectstr[$dkey]=	$dval;
			}
			$output[] = $subjectstr;
			$p++;
        }

            }

           

      return collect($output);
	}

	
	public function headings(): array{

		if($this->course == 10){
		 return ["Sr. No.","Ai_Code","Ai Center Name","Sub_201","Sub_202","Sub_206","Sub_207","Sub_208","Sub_209","Sub_210","Sub_211","Sub_212","Sub_213","Sub_214","Sub_215","Sub_216","Sub_222","Sub_223","Sub_225","Sub_229"];	
		}
		elseif($this->course == 12){
		return ["Sr. No.","Ai_Code","Ai_Center_Name","Sub_301","Sub_302","Sub_306","Sub_309","Sub_311","Sub_312","Sub_313","Sub_314","Sub_315","Sub_316","Sub_317","Sub_318","Sub_319","Sub_320","Sub_321","Sub_328","Sub_330","Sub_331","Sub_332","Sub_333","Sub_336"];	
		}
        
    }
}
