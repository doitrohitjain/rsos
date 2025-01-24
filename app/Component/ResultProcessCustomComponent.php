<?php 
namespace App\Component;
use App\Http\Controllers\Controller;
use App\Models\AllotingCopiesExaminer;
use App\Models\MarkingAbsentStudent;
use App\Models\StudentAllotmentMark;
use App\Helper\CustomHelper; 
use App\models\Student;
use App\models\ExamSubject;
use App\models\supplementary;
use App\models\StudentAllotment;
use App\models\Subject;
use App\models\User;
use App\models\TocSubjectMaster;
use App\models\Logs;
use Carbon\Carbon;
use Validator; 
use Session;
use Config;
use Cache;
use Auth;
use DB; 

class ResultProcessCustomComponent { 
    public function sample($course_id,$examcenter_detail_id,$subject_id){
    } 

    public function getSubjectMaxMinMarksMaster($subjectid=null,$boardid=null){
        $conditions = array();
		$conditions['status'] = 1;
		$conditions['subject_id'] = $subjectid;
		$conditions['board_id'] = $boardid; 
		
		$cacheName = "subjectMaxMinMarks_". $subjectid . "_" . $boardid;
		if (Cache::has($cacheName)) { 
			$maxmarksarr = Cache::get($cacheName);
		}else{ 
			$maxmarksarr = Cache::rememberForever($cacheName, function () use ($conditions) {
		   		return $maxmarksarr = TocSubjectMaster::where($conditions)->pluck('value','type')->toArray();
			});			
		}
		return $maxmarksarr;
	}


	public function getpraticalsubjects($course=null){
		$praticalsubjects=DB::table('subjects')->where('course','=','10')->to_array();
      
	}
}





	




