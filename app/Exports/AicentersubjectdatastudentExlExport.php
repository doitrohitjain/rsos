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
use Auth;
use App\Http\Controllers\Controller;
use App\Helper\CustomHelper;

class AicentersubjectdatastudentExlExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
	
	function __construct($request) {
		//$this->course = $request->course;
		$this->stream = $request->stream;
	
	 
	 }
	 
	public function collection(){
		 ini_set('memory_limit', '3000M');
		 ini_set('max_execution_time', '0');
		 $examyear = CustomHelper::_get_selected_sessions();
		 //$course =  $this->course;
		 $stream =  $this->stream;
		 $role_id = Session::get('role_id');
		 $developeradminRoleId = config("global.developer_admin");
		 $aiCenterRoleId = config("global.aicenter_id");
		 $ai_code = Auth::user()->ai_code;	 
		 $custom_controller_obj = new Controller;
		 $combo_name = 'gender';$gender = $custom_controller_obj->master_details($combo_name);
		 $custom_component_obj = new CustomComponent;
		 //$subjectList = $custom_component_obj->getSubjectByCoursePracticalTheoryexcel($course);
		 $subjectList = $custom_component_obj->getSubjectByCoursePracticalTheoryexcel();
		 if($role_id == $aiCenterRoleId){
		 $result = Student::where('is_eligible',1)->where('stream',$stream)->where('ai_code',$ai_code)->where('exam_year',$examyear)->get(['id','enrollment','name','father_name','dob','course','gender_id']);
		 }else if($role_id == $developeradminRoleId){
		 $result = Student::where('is_eligible',1)->where('stream',$stream)->where('exam_year',$examyear)->get(['id','enrollment','name','father_name','dob','course','gender_id']); 
		 }
		  $i =1;
		  $output=array();
		  foreach($result as $key => $data){ 
		 @$output[$i]['id'] = @$i;
		 @$output[$i]['enrollment'] = @$data->enrollment;
		 @$output[$i]['name'] = @$data->name ;
		 @$output[$i]['father_name'] = @$data->father_name;
		 @$output[$i]['dob'] = @$data->dob;
		 @$output[$i]['course'] = @$data->course;
		 @$output[$i]['gender_id'] = @$gender[$data->gender_id];
		 $k=1;
		 foreach ($data->exam_subject as $k => $subject_id){  
            @$output[$i]['subject_id'.$k] = @$subjectList[$subject_id->subject_id];
			$k++;
			
        }
		$i++;
    
    }
	
		return collect($output);
}
	
	public function headings(): array{
        return ["Sr. No.", "enrollment","Name","Father Name","DOB","Course","Gender","SUBJECT1","SUBJECT2","SUBJECT3","SUBJECT4","SUBJECT5","SUBJECT6","SUBJECT7"];
    }

	 
}