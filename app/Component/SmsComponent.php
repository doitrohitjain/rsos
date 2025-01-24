<?php 
namespace App\Component;
use DB;
use Cache;
use Config;
use Session;
use App\Helper\CustomHelper; 
use App\models\SmsManagement;
use App\Models\ExamcenterAllotment;
use App\Models\StudentAllotment;
use App\models\Student;
use App\models\School;
use App\models\StudentUpdate;
use App\models\User;
use App\models\Supplementary;
use App\models\StudentFee;
use App\models\SuppPaymentIssue;
use App\Models\PaymentIssue;
use App\models\TocMark;
use App\models\Logs;
use App\Models\AllotingCopiesExaminer;
use App\Models\MarkingAbsentStudent;
use App\models\ExamResult;
use App\models\ModelHasRole;
use App\models\ExamcenterDetail;
use App\models\MasterQuerieExcel;
use App\models\CenterAllotment;  
use App\models\MasterAdminDocument;
use App\models\ExamSubject; 
use Validator; 
use Auth; 
use App\Http\Controllers\Controller;
use App\models\SupplementarySubject;  
use App\models\ExamLateFeeDate;
use Carbon\Carbon;
use App\models\AiCenterMap;
use App\Models\TimeTable;
use App\Models\UserExaminerMap;
use App\Component\CustomComponent; 
use App\models\Document;
use App\models\Address;
use App\models\Subject;
use PDF;
use Response;
use Route;
use Illuminate\Support\Facades\Crypt;

class SmsComponent { 
    public function getListData($formId=null,$isPaginate=true){ 
		$conditions = Session::get($formId. '_conditions'); 
		$rawExtraCond2 = $rawConditions =  $rawExtraCond = $rawQueryDateTime = $rawExtraCondMergedAiCodes = $rawExtraCondAdd = 1;
		if(!empty($conditions['students.isssoid'])){
			if($conditions['students.isssoid'] >= 0){
				if($conditions['students.isssoid'] == 1){
					$rawExtraCond = ' rs_students.ssoid is not null ';				
				}else {
					$rawExtraCond = ' rs_students.ssoid is null ';
				}
				unset($conditions['students.isssoid']);
			}
		} 
		 
		
		$startenddataarr=null;
		$symbol = Session::get($formId. '_symbol');
        $symbols = Session::get($formId. '_symbols'); 
        $symbolis = Session::get($formId. 'symbolis');  
	    $orderByRaw = Session::get($formId. '_orderByRaw');
		// $rawConditions = Session::get($formId. '_rawConditions');
		$rawConditions = 1;
		
		$arraykeys=array_keys($conditions);
		
		/* Start End Date */
			$rawQueryDateTime = 1;
			$table_name = "students";$field_name = "created_at";
			if(@$conditions[$table_name.'.start_date'] || @$conditions[$table_name. '.end_date'] ){			
				$rawQueryDateTime = CustomHelper::getStartAndEndDate(@$conditions[$table_name.'.start_date'],@$conditions[$table_name.'.end_date'],$table_name,$field_name);
				unset($conditions[$table_name.".start_date"]);
				unset($conditions[$table_name.".end_date"]);
			} 
		/* Start End Date */
			 
		if(in_array('sms_managements.enrollmentgen',$arraykeys)){
			unset($conditions["sms_managements.enrollmentgen"]);
			if(@$symbol){
				$tempCond = array('sms_managements.enrollment',$symbol,null);
			}
		} 
		$master = array();
		$fields = array('sms_managements.*');
		
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = SmsManagement::where($conditions)
						->whereRaw(@$rawExtraCond)
						->paginate($defaultPageLimit,$fields);
		}else{ 
			$master = SmsManagement::where($conditions)
						->whereRaw(@$rawExtraCond)
						->get($fields);
		}
		
		return $master; 
		
	}

	 
}


