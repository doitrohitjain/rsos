<?php 
namespace App\Component;
use DB;
use Cache;
use Config;
use Session;
use App\Helper\CustomHelper; 
use App\models\Logs;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Component\CustomComponent;
use Validator; 
use Auth;
use App\Models\UserExaminerMap; 
use App\Models\Student; 
use App\Models\ExamcenterDetail; 
use App\Models\StudentAllotmentMark; 
use Illuminate\Support\Facades\Crypt;
use App\Models\UserPracticalExaminer;
use App\Models\User;
use App\models\PublicationBook;

class BookRequirementCustomComponent { 

	public function getBooksRequrementData($formId=null,$isPaginate=false){
		$conditions = Session::get($formId. '_conditions');
      	$master = array();
		if($isPaginate){
			$defaultPageLimit = config("global.defaultPageLimit");
			$master = PublicationBook::Join('aicenter_details', 'aicenter_details.ai_code', '=', 'publication_books.ai_code')->Join('subjects', 'subjects.id', '=', 'publication_books.subject_id')
			->where($conditions)->orderBy('aicenter_details.ai_code', 'asc')->orderBy('publication_books.course', 'asc')->orderBy('subjects.subject_code', 'asc')->orderBy('publication_books.subject_volume_id', 'asc')
			->paginate($defaultPageLimit,array('publication_books.id','publication_books.ai_code','publication_books.subject_volume_id','publication_books.exam_year',
        'publication_books.exam_month','publication_books.is_eligible',
		'publication_books.lock_submitted','publication_books.user_id','publication_books.last_update_by_user_id','publication_books.subject_id','publication_books.course','publication_books.subject_code','publication_books.hindi_auto_student_count','publication_books.english_auto_student_count','publication_books.hindi_last_year_book_stock_count','publication_books.english_last_year_book_stock_count','publication_books.hindi_required_book_count','publication_books.english_required_book_count'));
		}else{
			$master = PublicationBook::Join('aicenter_details', 'aicenter_details.ai_code', '=', 'publication_books.ai_code')->Join('subjects', 'subjects.id', '=', 'publication_books.subject_id')->where($conditions)->orderBy('aicenter_details.ai_code', 'asc')->orderBy('publication_books.course', 'asc')->orderBy('subjects.subject_code', 'asc')->orderBy('publication_books.subject_volume_id', 'asc')->get(array('publication_books.id','publication_books.ai_code','publication_books.subject_volume_id','publication_books.exam_year',
        'publication_books.exam_month','publication_books.is_eligible',
		'publication_books.lock_submitted','publication_books.user_id','publication_books.last_update_by_user_id','publication_books.subject_id','publication_books.course','publication_books.subject_code','publication_books.hindi_auto_student_count','publication_books.english_auto_student_count','publication_books.hindi_last_year_book_stock_count','publication_books.english_last_year_book_stock_count','publication_books.hindi_required_book_count','publication_books.english_required_book_count'));
		}
		return $master; 
	}

	

	public function getDataForPdfBooksRequrementData($ai_code=null){
		$exam_year = Config::get("global.current_books_requirement_exam_year");
		$exam_month = Config::get("global.current_books_requirement_exam_month");
		//$exam_year =124;
		//$exam_month = 1;
		$result = DB::select('call getBooksRequirementData(?,?,?)',array($exam_year,$exam_month,$ai_code));
				
		if(@$result[0]){
			return $result;
		}
		return false;
	}

		
	public function bookDataAllReadyExists($course=null,$subject_id=null,$aicode=null,$medium_id=null,$volume_id=null,$publicbookid=null){
		$current_exam_year = Config::get("global.current_books_requirement_exam_year");
		$current_exam_month = Config::get("global.current_books_requirement_exam_month");

		$conditions=[
			'exam_year'=>$current_exam_year,
			'exam_month'=>$current_exam_month,
			'subject_id'=>$subject_id,
			'course'=>$course,
			'ai_code'=>$aicode,
			'subject_volume_id'=>$volume_id
		];
		if(@$publicbookid){
			$existsdata=PublicationBook::where($conditions)->where('id', "!=", $publicbookid)->get();
		}else{
			$existsdata=PublicationBook::where($conditions)->get();
		}
		
		return $existsdata;
	}

	public function letter_twelve_generate_report_pdf(Request $request,$ai_code=0) {  
		ini_set('memory_limit', '3000M');
		ini_set('max_execution_time', '0');
		$aiCenters = null; 
		$bookRequirementCustomComponent = new BookRequirementCustomComponent();	
        //path for save pdf public\files\books_requirement\125\2\12 and file name should be ai_code_12
		$result = $bookRequirementCustomComponent->getDataForPdfBooksRequrementData();
		return view('books_requrement.letter_twelve_generate_report_pdf',compact('result'));
	}
	public function letter_thirteen_generate_report_pdf(Request $request,$ai_code=0) {  
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '0');
		$bookRequirementCustomComponent = new BookRequirementCustomComponent();
		$result = $bookRequirementCustomComponent->getDataForPdfBooksRequrementData($ai_code); 
		//path for save pdf public\files\books_requirement\125\2\13 and file name should be ai_code_13
		return view('books_requrement.letter_thirteen_generate_report_pdf',compact('result'));
	}
	
	
	
	
	
	
}


