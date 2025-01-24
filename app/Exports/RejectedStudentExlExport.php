<?php
namespace App\Exports;
  
use App\Models\VerificationMaster;
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Session;
use Config;
use App\Component\CustomComponent;
use App\Models\ExamcenterDetail;
use App\Models\Subject;
use App\Exports\RejectedStudentExlExport;
use App\Helper\CustomHelper;

class RejectedStudentExlExport implements FromCollection,WithHeadings,ShouldAutoSize
{
   
	function __construct($exam_year,$exam_month) { 
		$this->exam_year = $exam_year; 
		$this->exam_month = $exam_month; 
	 }
	public function collection(){
		ini_set('memory_limit', '3000M');
		ini_set('max_execution_time', '0');

		 
		$exam_year = $this->exam_year; 
		$exam_month = $this->exam_month; 
		
		
		
		$qtemp = 'SELECT s.id FROM rs_students s left JOIN rs_change_request_students ch ON ch.student_id = s.id INNER JOIN rs_applications a ON a.student_id = s.id  AND a.is_ready_for_verifying = 1 WHERE s.exam_year = ' . $exam_year  . ' AND s.exam_month = ' . $exam_month  . ' AND s.ao_status = 3 AND (s.department_status != 2 OR s.department_status IS NULL) union SELECT s.id FROM rs_students s INNER JOIN rs_applications a ON a.student_id = s.id  AND a.is_ready_for_verifying = 1 WHERE s.exam_year = ' . $exam_year  . ' AND s.exam_month = ' . $exam_month  . ' AND (s.department_status = 3);';
		
		//echo $qtemp;die;
		
		
		$rTemp = DB::select($qtemp); 
		$studentsList = array();
		foreach($rTemp as $k1 => $v1){
			$studentsList[$v1->id] = $v1->id;
		}

		
		$studentsList = (implode(",",$studentsList));
		//echo $studentsList;die;
		$q1 = 'SELECT MAX(sv.id),s.ssoid,sv.id,s.ai_code,s.course,s.mobile,s.id,s.name, s.is_eligible,s.enrollment,ch.student_update_application,s.student_change_requests,s.ao_status,s.department_status,sv.* FROM rs_students s INNER JOIN rs_change_request_students ch ON ch.student_id = s.id INNER JOIN (SELECT student_id, MAX(id) AS max_id FROM rs_student_verifications WHERE deleted_at IS NULL GROUP BY student_id) latest_sv ON latest_sv.student_id = s.id INNER JOIN rs_student_verifications sv ON sv.id = latest_sv.max_id WHERE s.exam_year = ' . $exam_year  . ' AND s.exam_month = ' . $exam_month  . '  AND sv.deleted_at IS NULL AND s.id in ( '.  $studentsList .') AND s.deleted_at IS NULL GROUP BY s.id ORDER BY sv.id desc LIMIT 50000;';
			 //echo $q1;die;
		 
		$r1 = DB::select($q1); 
		$list = []; 
		$controller_obj = new Controller;
		
		$verificationLabels = $controller_obj->getVerificationDetailedLabels();
		$studentDetails = array();
		$conditions = array();
      	$verficationmasterdata =  VerificationMaster::where($conditions)->orderBy("id","DESC")->get();
		$verficationmasterdata = $verficationmasterdata->toArray();
		 
		$verficationmasterdataFinal = array();
		foreach($verficationmasterdata as $key => $value){
			$verficationmasterdataFinal[$value['main_document_id']][$value['field_id']] = $value['field_name'];
		}
		
		foreach($r1 as $k1 => $v1){
			 // dd($v1);
			if(@$v1->department_documents_verification || @$v1->ao_documents_verification){
				
				$data = json_decode($v1->department_documents_verification,true);
				$rejectedBy="Department";
				if(@$data && !empty($data)){
					
				}else{
					// dd($v1);
					$data = json_decode($v1->ao_documents_verification,true);
					$rejectedBy="AO";
				}
				
				foreach($data as $ik => $iv){
					foreach($iv as $tk => $tv){
						if($tv != 1){
							// $list[$ik][$tk] = $tv;
							$studentDetails[$v1->id]['rejectedBy'] = $rejectedBy;
							$studentDetails[$v1->id]['course'] = $v1->course;
							$studentDetails[$v1->id]['ai_code'] = $v1->ai_code;
							$studentDetails[$v1->id]['mobile'] = $v1->mobile;
							$studentDetails[$v1->id]['ssoid'] = $v1->ssoid;
							$studentDetails[$v1->id]['name'] = $v1->name;
							$studentDetails[$v1->id]['list'][$ik][$tk] = $tv;
							// $studentDetails[$ik][$tk]['name'] = $v1->name;
							// $list[$ik][$tk]['name'] = $v1->name;
							
							/* Change Request Data Start */
								//apply for CR in CRS Table new entry and set student_update_application = null
								$studentDetails[$v1->id]['pending_at_department'] = ($v1->student_change_requests == 1)? "Yes":"No";
								//apply for CR student set with 1 Col student_change_requests i.e. Only Request
								//$studentDetails[$v1->id]['student_change_requests'] = $v1->student_change_requests;
								
								//Dept Approve for CR student set with 2 Col student_change_requests i.e. Approve Request
								//Dept Approve for CR student set with CRS Table student_change_requests = 2
								
								//Student Click on Update Application in Change Request Table student_update_application = 1 i.e. set with null ao_status,verifier_status,dept_status,is_ready_for_verifying,is_eligible,locksubmitted							
								$studentDetails[$v1->id]['pending_at_student'] = ($v1->student_update_application != 1 )? "Yes":"No";
							/* Change Request Data End */
						}
					}
				}
			}
		} 
		
		$i =1 ;
		foreach($studentDetails as $k => $value){
			
			$data = $value['list'];
			
			$output[$i] = array(
				'id' => $i,
				'name' => @$value['name'],
				'ai_code' => @$value['ai_code'],
				'course' => @$value['course'] . 'th',
				'ssoid' => @$value['ssoid'],
				'mobile' => @$value['mobile']
			);
			$arrList = array();
			foreach($data as $ik => $iv){ 
				$arrList[$ik] = @$verificationLabels[$ik]['hindi_name'];
			}
			$output[$i]['pending_at_department'] = @$value['pending_at_department']; 
			$output[$i]['pending_at_student'] = @$value['pending_at_student'];
			$output[$i]['rejected'] = implode(",",$arrList); 
			$i++;
		}
		 
		
      return collect($output);
	} 
	public function headings(): array{ 
		return ["Sr. No.","Name","AI Code","Class","SSO","Mobile","Pending At Department","Pending At Student","Rejected Documents"];
        
    }

	 
}

