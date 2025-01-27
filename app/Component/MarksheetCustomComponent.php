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
use App\models\ExamResult;
use App\models\Document;
use App\models\Address;
use App\models\Subject;
use App\models\User;
use App\models\Logs;
use Carbon\Carbon;
use Validator; 
use Session;
use Config;
use Cache;
use Auth;
use DB;
use phpqrcode\phpqrcode;
use App\models\Pastdata;
//use App\models\Student;
use App\models\Application;
use App\models\MarksheetPrint;
use App\models\CertificatePrint;
use App\Http\Traits\QRcode;
use File;


class MarksheetCustomComponent { 
  
	public function getYearInWord($number=null)
	{
		
		$dictionary  = array(	
			1                   => 'One',
			2                   => 'Two',
			3                   => 'Three',
			4                   => 'Four',
			5                   => 'Five',
			6                   => 'Six',
			7                   => 'Seven',
			8                   => 'Eight',
			9                   => 'Nine',
			'01'                   => 'One',
			'02'                   => 'Two',
			'03'                   => 'Three',
			'04'                   => 'Four',
			'05'                   => 'Five',
			'06'                   => 'Six',
			'07'                   => 'Seven',
			'08'                   => 'Eight',
			'09'                   => 'Nine',
			10                  => 'Ten',
			11                  => 'Eleven',
			12                  => 'Twelve',
			13                  => 'Thirteen',
			14                  => 'Fourteen',
			15                  => 'Fifteen',
			16                  => 'Sixteen',
			17                  => 'Seventeen',
			18                  => 'Eighteen',
			19                  => 'Nineteen',
			20                  => 'Twenty',			
			30                  => 'Thirty',
			40                  => 'Forty',
			50                  => 'Fifty',
			60                  => 'Sixty',
			70                  => 'Seventy',
			80                  => 'Eighty',
			90                  => 'Ninety'
			
		);	
		$arr = str_split($number, strlen($number)/2);
		$yearinword = "";
		if($arr[0] == 19)
		{
			$yearinword .= "Nineteen Hundred ";
		}
		else
		{
			$yearinword .= "Two Thousand ";
		}
		if($arr[1] > 20 && !in_array($arr[1],array(30,40,50,60,70,80,90)))
		{
			$narr = str_split($arr[1], strlen($arr[1])/2);
			$tens_arr = array(2=>'Twenty ',3=>'Thirty ',4=>'Forty ',5=>'Fifty ',6=>'Sixty ',7=>'Seventy ',8=>'Eighty ',9=>'Ninety ');
			$yearinword .= $tens_arr[$narr[0]];
			$yearinword .= $dictionary[$narr[1]];
			
		}else{
			if($arr[1] != '00')
			{
				$yearinword .= $dictionary[$arr[1]];
			}
		}
		return $yearinword;
	}
   public function getDObInWords($dob=null){
	if(!empty($dob) && $dob != "--"){
		
		$separator = substr($dob,2,1);
		$date_break = explode($separator,@$dob);
		$in_words = null;
		// $locale = 'en_US';
		// $fmt = numfmt_create($locale, NumberFormatter::SPELLOUT);
		
		if(isset($date_break[0]) && isset($date_break[1]) && isset($date_break[2])){
			$d = $date_break[0];
			$m = $date_break[1];
			$y = $date_break[2];
			
			// $in_words = (numfmt_format($fmt, $d));
			$in_words = ($this->getNumberToWordYear($d));
			$in_words .= ' ';
			$monthName = date('F', mktime(0, 0, 0, $m, 10));
			$in_words .= ($monthName);
			//$in_words .= ' and ';
			$in_words .= ' ';
			// $in_words .= (numfmt_format($fmt, $y));
			$in_words .= ($this->getYearInWord($y));
			$in_words .= ' ';
		} 
		
		$monthNum  = 3;
		$monthName = date('F', mktime(0, 0, 0, $monthNum, 10));
		// echo (ucwords($in_words));
		// die;
		return (ucwords($in_words));
	}else{
		return null;
	}
	
}
    public function getSerialNumber($enrollment){
        $pastInfo = Pastdata::where('ENROLLNO','=',$enrollment)->orderBy('id','asc')->first();
		//$pastInfo = $this->Pastdata->find('first',array('conditions'=>array('ENROLLNO'=>$enrollment),'order'=>array('id DESC')));		
		$findInFlag = 'Pastdata';
		$comination = null;
		$courseVal = null;
		$id = null;
		$sub_enrollment = substr($enrollment,-6,2);
		$v = 1;
		//$this->loadModel("MarksheetPrint");
		$data=MarksheetPrint::where('enrollment','=',$enrollment)->orderBy('id','DESC')->latest('version')->first();
		//$data = $this->MarksheetPrint->find('first', array('conditions' => array('enrollment' => $enrollment) ,'order' => array('MarksheetPrint.id DESC')));
		if(isset($data->version) && !empty($data->version)){
			$v = $data->version + 1;
		}
		$serialNumberMasterValue = Config::get('global.serialNumberMasterValue');
		if(empty($pastInfo)){ //fetch data from students
			$student = Student::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first();
			$application = Application::where('student_id','=',@$student->id)->orderBy('id','DESC')->first();
			$courseVal = @$application->course;
			$id = @$student->id;
		} else {
			$courseVal = @$pastInfo->CLASS;
			$id = @$pastInfo->id;
		}
		$comination = "M".$comination . $sub_enrollment . ($serialNumberMasterValue + $id) . $v ;
		$dataSave['version'] = $v;
		$dataSave['publish_date'] = date("d-m-Y");
		$dataSave['enrollment'] = $enrollment;
		$dataSave['serial_number'] = $comination;
		
        $data=MarksheetPrint::create($dataSave);
		
		// MarksheetPrint->save($dataSave);
		 
		return $comination;
	}

	public function getSerialNumbercertficate($enrollment){
        $pastInfo = Pastdata::where('ENROLLNO','=',$enrollment)->orderBy('id','asc')->first();
		//$pastInfo = $this->Pastdata->find('first',array('conditions'=>array('ENROLLNO'=>$enrollment),'order'=>array('id DESC')));		
		$findInFlag = 'Pastdata';
		$comination = null;
		$courseVal = null;
		$id = null;
		$sub_enrollment = substr($enrollment,-6,2);
		$v = 1;
		$data=CertificatePrint::where('enrollment','=',$enrollment)->orderBy('id','DESC')->latest('version')->first();
		if(isset($data->version) && !empty($data->version)){
			$v = $data->version + 1;
		}
		$serialNumberMasterValue = Config::get('global.serialNumberMasterValue');
		if(empty($pastInfo)){ //fetch data from students
			$student = Student::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first();
			$application = Application::where('student_id','=',@$student->id)->orderBy('id','DESC')->first();
			$courseVal = @$application->course;
			$id = @$student->id;
		} else {
			$courseVal = @$pastInfo->CLASS;
			$id = @$pastInfo->id;
		}
		$comination = "C".$comination . $sub_enrollment . ($serialNumberMasterValue + $id) . $v ;
		$dataSave['version'] = $v;
		$dataSave['publish_date'] = date("d-m-Y");
		$dataSave['enrollment'] = $enrollment;
		$dataSave['serial_number'] = $comination;
		
        $data=CertificatePrint::create($dataSave);
		
		// MarksheetPrint->save($dataSave);
		 
		return $comination;
	}

	
    public function getDisplayExamMonthYear($exam_month_year = null){
		$displaymonthyears = array('EX_YR','display_ex_month_year');
        $data = DB::table('display_month_years')->where('EX_YR','=',$exam_month_year)->orderBy('id','DESC')->first($displaymonthyears);
		//$data = DB::table('display_month_years')->find('list', array('fields' => array('EX_YR','display_ex_month_year'),'conditions' => array('EX_YR' => $exam_month_year)));
		if(isset($data->display_ex_month_year)){
			return $data->display_ex_month_year;
		}
		return false;
		
	}
	
    public function _getSubjectsCodeId($course=null){
		
			$condition=array();
			// $condition['deleted']=0;
			if(isset($course)){
				$condition['course'] = $course;
			}
			
            $subjectfields = ['subject_code','id'];
			
           // $list = Subject::where('course','=',$course)->orderBy('subject_type', 'asc')->orderBy('name','asc')->get($subjectfields);
		   $list = Subject::where('course','=',$course)->orderBy('subject_type', 'asc')->orderBy('name','asc')->pluck('id','subject_code');
		   return $list;

	}
	public function _getSubjectsForMarksheet($course=null)
	{
	
		$subjectfields = array('id','real_name','subject_code');			
		$list = Subject::where('course','=',$course)->orderBy('subject_type','asc')->orderBy('real_name','asc')->get($subjectfields);
		
		$listFinal = array();
		foreach($list as $v){
			$listFinal[$v->id] = " ". $v->subject_code . "  " . $v->real_name;
		}
			
		return $listFinal;
	}
    public function getSubjectMaxMarks($subject_id = null){
		
       $conditions = array('subject_id'=>$subject_id,'board_id'=>81,'type'=>'GT_MAX'); 	   
        $data = DB::table('toc_subject_masters')->where($conditions)->first();
      	
		//$data = $this->TocSubjectMaster->find('first',array('conditions' => array('subject_id' => $subject_id, 'board_id' => 81, 'type' => 'GT_MAX')));
		if(isset($data->value)){
			return $data->value;
		}
		return false;
	}
    public function numberInWord($num=null){
		$locale = 'en_US';
		// $fmt = numfmt_create($locale, NumberFormatter::SPELLOUT);
		// $in_words = (numfmt_format($fmt, $num));
		$in_words = $this->getNumberToWordYear($num);
		return (ucwords($in_words));
	}
    public function getNumberToWordYear($number=null){
		$string = null;
		
		$hyphen      = ' ';
		$conjunction = ' ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' point ';
		$dictionary  = array(
			0                   => 'zero',
			1                   => 'one',
			2                   => 'two',
			3                   => 'three',
			4                   => 'four',
			5                   => 'five',
			6                   => 'six',
			7                   => 'seven',
			8                   => 'eight',
			9                   => 'nine',
			'01'                   => 'one',
			'02'                   => 'two',
			'03'                   => 'three',
			'04'                   => 'four',
			'05'                   => 'five',
			'06'                   => 'six',
			'07'                   => 'seven',
			'08'                   => 'eight',
			'09'                   => 'nine',
			10                  => 'ten',
			11                  => 'eleven',
			12                  => 'twelve',
			13                  => 'thirteen',
			14                  => 'fourteen',
			15                  => 'fifteen',
			16                  => 'sixteen',
			17                  => 'seventeen',
			18                  => 'eighteen',
			19                  => 'nineteen',
			20                  => 'twenty',
			30                  => 'thirty',
			40                  => 'forty',
			50                  => 'fifty',
			60                  => 'sixty',
			70                  => 'seventy',
			80                  => 'eighty',
			90                  => 'ninety',
			100                 => 'hundred',
			1000                => 'thousand'
			//1000000             => 'million',
			//1000000000          => 'billion',
			//1000000000000       => 'trillion',
			//1000000000000000    => 'quadrillion',
			//1000000000000000000 => 'quintillion'
		);

		if (!is_numeric($number)) {
			return false;
		}

		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			trigger_error(
				'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
				E_USER_WARNING
			);
			return false;
		}

		if ($number < 0) {
			return $negative . $this->getNumberToWordYear(abs($number));
		}

		$string = $fraction = null;

		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}

		switch (true) {
			case $number < 21:
				$string = @$dictionary[$number];
				break;
			case $number < 100:
				$tens   = ((int) ($number / 10)) * 10;
				$units  = $number % 10;
				$string = $dictionary[$tens];
				if ($units) {
					$string .= $hyphen . $dictionary[$units];
				}
				break;
			case $number < 1000:
				$hundreds  = $number / 100;
				$remainder = $number % 100;
				$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
				if ($remainder) {
					$string .= $conjunction . $this->getNumberToWordYear($remainder);
				}
				break;
			default:
				$baseUnit = pow(1000, floor(log($number, 1000)));
				$numBaseUnits = (int) ($number / $baseUnit);
				$remainder = $number % $baseUnit;
				$string = $this->getNumberToWordYear($numBaseUnits) . ' ' . $dictionary[$baseUnit];
				if ($remainder) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= $this->getNumberToWordYear($remainder);
				}
				break;
		}

		if (null !== $fraction && is_numeric($fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $number) {
				$words[] = $dictionary[$number];
			}
			$string .= implode(' ', $words);
		}

		return (ucwords($string));
		 
	}
    public function getGradeOfMarks($num=null){
		$grade = null;
		if($num >= 91 && $num <= 100){
			$grade = "A";
		}else if($num >= 81 && $num <= 90){
			$grade = "B";
		}else if($num >= 71 && $num <= 80){
			$grade = "C";
		}else if($num >= 61 && $num <= 70){
			$grade = "D";
		}else if($num >= 51 && $num <= 60){
			$grade = "E";
		}else if($num >= 41 && $num <= 50){
			$grade = "F";
		}else if($num >= 33 && $num <= 40){
			$grade = "G";
		}else if($num >= 16 && $num <= 32){
			$grade = "H";
		}else if($num >= 0 && $num <= 15){
			$grade = "I";
		} 
		return (ucwords($grade));
	}

	public function getpastdata($enrollment=null){
		$pastdata=Pastdata::where('ENROLLNO','=',$enrollment)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
		return $pastdata;
	}


	public function getstudentdata($enrollment=null){
		$conditions=[
			'enrollment'=>$enrollment,
		];
		$datastudent=Student::where($conditions)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
		if(!empty(@$datastudent->exam_year) && @$datastudent->exam_year<=123 || @$datastudent->exam_year==0){
            $studentdata=Student::where('enrollment','=',$enrollment)->whereNull('deleted_at')->whereNotNull('challan_tid')->orderBy('id', 'DESC')->first();
		}else{
			$studentdata=Student::where('enrollment','=',$enrollment)->whereNull('deleted_at')->where('is_eligible','=',1)->orderBy('id', 'DESC')->first();
		}
		if(!empty($studentdata)){
			return $studentdata;
		}else{
			return false;
		}
		
	}

	public function getexamsubjectsdata($enrollment=null){
		$data = DB::table('exam_subjects')->join('students','exam_subjects.student_id','=','students.id')
		->join('subjects','exam_subjects.subject_id','=','subjects.id')
		->whereNull('students.deleted_at')
		->where([['students.enrollment', '=',$enrollment]])->orderBy('exam_subjects.exam_year','Desc')->orderBy('exam_subjects.exam_month','asc')->orderBy('subjects.subject_code','asc')->get(array('exam_subjects.*','students.enrollment','students.stream'));
		return $data;
	}

	public function getexamsubjectresultnullcount($enrollment=null){
		$conditions=['students.enrollment'=>$enrollment];
		$data = DB::table('exam_subjects')->join('students','exam_subjects.student_id','=','students.id')
		->whereNull('students.deleted_at')->whereNull('exam_subjects.deleted_at')
		->where($conditions)
		->where(function($query) {
			$query->where('exam_subjects.final_result','')
				->orWhere('exam_subjects.final_result',NULL);
		})->get(array('exam_subjects.*','students.enrollment','students.stream'));
		return $data;
	}

	public function getallexamsubjectsdata($enrollment=null,$exam_year=null,$exam_month=null){
		if(@$exam_year && @$exam_month){
			$conditions=['students.enrollment'=>$enrollment,'exam_subjects.exam_year'=>$exam_year,'exam_subjects.exam_month'=>$exam_month];
		}else{
			$conditions=['students.enrollment'=>$enrollment];
		}

		$data = ExamSubject::join('students','exam_subjects.student_id','=','students.id')->
		join('subjects','subjects.id','=','exam_subjects.subject_id')
		->whereNull('students.deleted_at')->whereNull('exam_subjects.deleted_at')
		->where($conditions)->orderBy('subject_code','asc')->get(array('exam_subjects.*','students.enrollment','students.stream'));
		return $data; 
	}

	public function getstudentsubject($enrollment=null){
		$data = ExamSubject::join('students','exam_subjects.student_id','=','students.id')
		->whereNull('students.deleted_at')->whereNull('exam_subjects.deleted_at')
		->where([['students.enrollment', '=',$enrollment]])->orderBy('exam_subjects.subject_id','asc')->pluck('exam_subjects.subject_id');
		return $data; 
	}
	public function previousqualificationget($course=null,$stream=null,$admtype=null)
	{
		$obj_controller= new Controller();
		$result = DB::table('master_previous_qualifications')->where('course',$course)
		->where('adm_type',$admtype)->where('stream',$stream)
		->first('previous_qualification_name'); 
		$result = explode(",",@$result->previous_qualification_name);
		$combo_name = 'pre-qualifi';$pre_qualifi = $obj_controller->master_details($combo_name);
		$finalArr = array();
		foreach(@$result as $v ){
			$finalArr[$v] = $pre_qualifi[$v];
		} 
		return $finalArr;
    }

	public function previousboardget($admtype=null)
	{	$obj_controller= new Controller();
		$boards = $obj_controller->getAdmissionTypeBords($admtype);
		return $boards;
    }

	public function qrcode($value,$enrollment){
		$qrcdeo = new QRcode();
		$directory_path = public_path("qrcode/enrollment");
		$directory = File::makeDirectory($directory_path,$mode = 0777, true, true);
		$value = $value;
		$ecc  = 'L';
		$file = public_path("qrcode/enrollment/$enrollment.png");
		$pixel_size =4;
		$frame_size = 2;
		$qrcdeo->png($value,$file,$ecc,$pixel_size,$frame_size);
	}
	 
	/* Marksheet test case start */ 
		public function testCaseSubjectAndFinalResultChecking(){
			$mainFinalArr = array();
			$enrollments = $this->tempgetIssueMarksheetStudents();
			
			foreach($enrollments as $k => $enrollment){
				$mainFinalArr[$enrollment] = $this->testCaseSingleMarksheetDetails($enrollment);
				
				
			} 
			$response = array();
			foreach($mainFinalArr as $k => $value){ 
				$concat = null;
				foreach($value['examSubjectsMarksData'] as $k=> $subjectDetails){
					$concat = $subjectDetails->student_id . "_". $subjectDetails->subject_id . "_". $subjectDetails->total_marks . "_". $subjectDetails->final_result;
					$response[$value['student']['id'] . "_" . $subjectDetails->subject_id]['id'] = $value['student']['id'];
					$response[$value['student']['id'] . "_" . $subjectDetails->subject_id]['enrollment'] = $value['student']['enrollment'];
					$response[$value['student']['id'] . "_" . $subjectDetails->subject_id]['course'] = $value['student']['course'];
					$response[$value['student']['id'] . "_" . $subjectDetails->subject_id]['final_result'] = $value['final_result']['final_result'];
					$response[$value['student']['id'] . "_" . $subjectDetails->subject_id]['total_marks'] = $value['final_result']['total_marks'];
					$response[$value['student']['id'] . "_" . $subjectDetails->subject_id]['exam_year'] = $value['final_result']['exam_year'];
					$response[$value['student']['id'] . "_" . $subjectDetails->subject_id]['exam_month'] = $value['final_result']['exam_month'];
					$response[$value['student']['id'] . "_" . $subjectDetails->subject_id]['concat'] = $concat; 
				} 
				
			}
			$html = "";
			$html .= "<table width='100%' border='1px;'>";
			$html .= "<tr>";
			$html .= "<th>Id</th>";
			$html .= "<th>Enrollment</th>";
			$html .= "<th>Course</th>";
			$html .= "<th>Final Result</th>";
			$html .= "<th>Total Marks</th>";
			$html .= "<th>Exam Year</th>";
			$html .= "<th>Exam Month</th>";
			$html .= "<th>Concat</th>";
			$html .= "</tr>";
			foreach($response as $student_id => $data){
				$html .= "<tr>"; 
				$html .= "<td>" . $data['id'] ."</td>";
				$html .= "<td>" . $data['enrollment'] ."</td>";
				$html .= "<td>" . $data['course'] ."</td>";
				$html .= "<td>" . $data['final_result'] ."</td>";
				$html .= "<td>" . $data['total_marks'] ."</td>";
				$html .= "<td>" . $data['exam_year'] ."</td>";
				$html .= "<td>" . $data['exam_month'] ."</td>";
				$html .= "<td>" . $data['concat'] ."</td>";
				$html .= "</tr>";
			}
			$html .= "</table>"; 
			echo $html;die;
		}


		public function tempgetIssueMarksheetStudents(){
			$studentIds = array("01001212098","01001224044","01001212043","01001224032","01001224012","01001224040","01001224013","01001224015","01001224004","01001225002","01001213076","01001213009","01001193164","01001213083","01001213034","01001203108","01006192294","01006212077","01006213005","01018212071","01018202164","01018212038","01018192026","01018212123","01018212045","01018202068","01018213068","01018213178","01018213148","01018213197","01018203062","01018193031","01018213184","01018213179","01018193162","01018213105","01018225001","01020224011","01020212021","01020224033","01020224031","01020224087","01020212065","01020212075","01020224098","01020224106","01020194007","01020224004","01020212062","01020212123","01020224095","01020224041","01020224025","01020224052","01020224032","01020224021","01020212224","01020224103","01020202097","01020224037","01020212166","01020224003","01020224023","01020212351","01020192214","01020224015","01020212024","01020224101","01020224009","01020224002","01020212348","01020212217","01020224043","01020213021","01020213115","01020213004","01020213314","01020213041","01020213175","01020225005","01020213045","01020225001","01020213039","01020213012","01020213042","01020203204","01020213252","01020213054","01020213240","01020213320","01020213108","01020213294","01020203028","01020213186","01020213325","01027212056","01027192177","01027212061","01027192177","01033224033","01033224045","01033224057","01033224042","01033224010","01033203017","01033213071","01033225001","01033213051","01033193015","01033213066","01045203044","01045213031","01046224034","01046192060","01046224033","01046212039","01046192043","01046202054","01046212183","01046212025","01046224050","01046192085","01046224002","01046212066","01046202204","01046202213","01046224020","01046212038","01046202053","01046202152","01046213019","01046203089","01046213008","01053224030","01053224063","01053212030","01053224053","01053224080","01053224080","01053224085","01053224028","01053213161","01053213192","01053213160","01053193018","01053213154","01053213202","01053213191","01053225001","01053213112","01053213080","01053225003","01053195021","01053225005","01053213040","01053213216","01059192180","01059212140","01059212162","01059224001","01059212071","01059224017","01059225002","01059203092","01061212087","01061212088","01061212082","01061212089","01061192022","01064224027","01064224002","01064202144","01064182190","01064202130","01064225001","01064183100","01064193039","01065202323","01065182293","01065202273","01065212134","01065212063","01065193013","01065213253","01065213022","01065213166","01065213003","01065213125","01066224012","01066212021","01066224015","01066213020","01066213047","01074224043","01074182163","01074224046","01074224028","01074224050","01074224011","01074224004","01074224025","01074212079","01074224007","01074224042","01074224032","01074224019","01074202122","01074213076","01074213143","01074213157","01074213033","01074213176","01075224048","01075212011","01075224013","01075224026","01075224034","01075224017","01075224008","01075224024","01075224002","01075224016","01075224038","01075224011","01075202058","01075195002","01075213020","01075183004","01076212086","01076212054","01076224028","01076212035","01076224041","01076224032","01076224012","01076182133","01076213012","01076203115","01076225003","01076225002","01076225004","01076225001","01078212022","01078202029","01078212030","01078192071","01078213049","01078213004","01079224013","01079224014","01079212053","01079212104","01079212052","01080192017","01104213022","01104193042","01109224003","02002224005","02002224002","02019212003","02019202055","02019192057","02019202027","02019192033","02035224015","02035224013","02035224008","02035212041","02035212064","02035212009","02035212059","02035224012","02035224005","02035202012","02035224011","02035213071","02035183024","02053213029","02065202002","02065202003","02091212021","02091213022","02091213001","02091213052","02096192015","02096202095","02096194019","02096212014","02096202082","02109224002","02109224001","02109224003","02109213009","02116182052","02116212009","02116213013","02116213012","02123224005","02123224013","02135202002","02135224022","02135224023","02135224046","02135224007","02135224029","02135182042","02135224025","02135202175","02135182061","02135212043","02135182100","02135224030","02135212074","02135182042","02135213090","02135213047","02135213093","02137224020","02137224015","02137224019","02137224006","02137213027","02137203039","02137213022","02137213020","02139202064","02139212063","02139202077","02139212066","02139224003","02139212010","02139224015","02139224006","02139202038","02139224007","02139224004","02139224021","02139224014","02139212051","02139224018","02139224012","02139224019","02139224022","02139192106","02139224023","02139224010","02139213010","02139213045","02139213040","02139203027","02139213042","02139213061","02140224008","02140213002","02140213029","03001192166","03001224005","03001202060","03001202006","03001183045","03001193030","03001213007","03001203017","03001213050","03001225009","03001203016","03002224027","03002212033","03002224011","03002224026","03002224029","03002224016","03002202020","03002182024","03002224005","03002212034","03002202017","03002224008","03002182076","03002224006","03002203043","03004202019","03004224003","03004192012","03004213043","03004213005","03041202020","03041202055","03041202020","03041212035","03041213123","03041213113","03041213105","03041213117","03041213097","03041213036","03041193076","03041213078","03041193087","03041213024","03041213112","03041213098","03041213082","03041213037","03046212139","03046212121","03046212083","03046202010","03046212043","03046202010","03046212033","03046212099","03046203124","03046213100","03046203178","03046213149","03046203049","03046203064","03046203192","03046183175","03046213061","03046183105","03046213156","04001192184","04001212231","04001212325","04001192244","04001212504","04001212583","04001212229","04001212139","04001182101","04001212111","04001212299","04001212112","04001212075","04001212281","04001212363","04001212517","04001213108","04001213054","04001203010","04001213324","04001213407","04002202049","04006224004","04006203095","04020202004","04020192176","04020213033","04020193219","04020213208","04020213094","04020183294","04020213176","04020213205","04020213138","04020213137","04020213140","04020213027","04021212165","04021212060","04021202033","04021212158","04021212170","04021212125","04021212148","04021202323","04021212130","04021213257","04021193149","04021213208","04021213254","04021213227","04021213049","04021213266","04021213017","04021213073","04021213265","04021213035","04021213110","04021213029","04021203173","04021213177","04021213130","04021213111","04022182147","04022182635","04022212082","04022182306","04022212149","04022212107","04022182656","04022202734","04022202018","04022212043","04022212084","04022212350","04022212176","04022212112","04022193108","04022193003","04022203254","04022203144","04022203305","04022213113","04022183264","04022203059","04022213296","04022203004","04022213259","04022213003","04022203471","04025182457","04025212236","04025224001","04025212402","04025202177","04025224021","04025224005","04025212067","04025224003","04025192438","04025213458","04025213420","04025213441","04025213352","04025193444","04025203102","04025213381","04025203002","04025213440","04025225009","04025203529","04025193321","04025213001","04025203521","04025213073","04032202273","04032212008","04032224001","04032202051","04032212148","04032192065","04032212156","04032192200","04032212086","04032202121","04032203002","04032213091","04032213134","04032213181","04032213092","04032213087","04032213119","04032213144","04032213150","04032203052","04034212080","04034212039","04034202066","04045212050","04045182131","04045202265","04045212046","04045202532","04045212334","04045202099","04045212333","04045212213","04045212295","04045202454","04045213183","04045213241","04047212066","04047212157","04047202062","04047182271","04047213271","04047213272","04049212246","04051212025","04051212016","04051212088","04051212050","04051212052","04051212121","04051213059","04053212088","04053213019","04057224027","04057212110","04057212116","04057212074","04057212069","04057212109","04057224042","04057212002","04057212133","04057224007","04057213151","04059212054","04059212086","04071213001","04077213007","05016224023","05016224056","05016224015","05016224051","05016202183","05016224036","05016224052","05016224057","05016224029","05016212087","05016224058","05016213068","05021212008","05037203001","05037203009","05051224041","05051224018","05051212023","05051224001","05051224033","05051224026","05051212036","05051224017","05051224039","05051224028","05051224032","05051224002","05051224029","05051224031","05051224034","05051224009","05051203070","05051225001","05051225003","05051213044","05051193028","05051213037","05051193041","05051213038","05051213043","05073212010","05073212038","05073203014","05073213072","05073213110","05073213036","05073213042","05098224006","05099224012","05099224025","05099224001","05099202060","05099212078","05099224011","05099202034","05099202068","05099224032","05099203050","05099225002","05099203020","05099213049","05099213073","05099213041","05101212012","06002224013","06002192188","06002224001","06002224041","06002192251","06002224025","06002224052","06002224017","06002182040","06002224015","06002193220","06002193093","06002203071","06002183267","06002203102","06002203115","06006212025","06006224028","06006224013","06006213043","06006213118","06006225003","06010212009","06010224004","06010212007","06010212109","06010224021","06010224013","06010213078","06010225002","06010213120","06010213087","06014202048","06019224010","06019213026","06019203034","06022182071","06022202024","06022182166","06022183007","06023182214","06023182120","06023212028","06023212006","06023224003","06023224005","06023224008","06023224007","06023213037","06023213043","06023213038","06031212012","06031224012","06031224034","06031203028","06035202083","06035213080","06035213078","06035213042","06035213028","06035213076","06035213058","06035213088","06044224002","06044224011","06044224005","06044224040","06044224013","06044224038","06044224003","06044224012","06044224001","06044224014","06044202025","06044224041","06044224009","06044224033","06044224015","06044224029","06044192104","06044224016","06044224026","06044224030","06046224007","06046224025","06046213020","06046213032","06046203052","06046213033","06047202095","06047224007","06047224004","06047203011","06047213086","06047213064","06047225002","06047225003","06047225001","06059193005","06060212064","06060224026","06060224001","06060202092","06060224019","06060224020","06060202013","06060224003","06060224011","06060213024","06060203048","06060213055","06060213026","06060203049","06060203010","06060213023","06060203038","06061224014","06061192086","06061224031","06061224022","06061224021","06061224009","06061224032","06061224019","06061212075","06061182255","06061202062","06061202031","06061224004","06061224002","06061183210","06061193116","06061213101","06061183170","06061225001","06061213049","06061213111","06062212042","06062212065","06062213040","06062213041","06062213048","06063224001","06063224008","06063212022","06063212018","06063202097","06063224009","06063224002","06063224014","06063183049","06063213093","06063213110","06063213120","06064224008","06065212043","06065212010","06065213060","06065213063","06065213058","06066212115","06066212102","06066212027","06066203126","06066213070","06067202060","06067224017","06067224003","06067224001","06067224015","06067212049","06067224012","06067224014","06067224008","06067225002","06067213039","06067213041","06068202108","06069212025","06069213020","06069193076","06069213019","06070202049","06070202012","06070213084","06070213064","06070213066","06070213042","06070213065","06070203016","06070213082","06070213075","06070213069","06070193038","06070213083","06071212047","06071212014","06071213078","06071213030","06071203006","06071203044","06072224001","06072212056","06072212051","06072213112","06072213086","06072213133","06072213111","06072213075","06072203011","06072213116","06072203050","06073212080","06074224024","06074224009","06074212021","06074224012","06074224011","06075192078","06075212033","06076224018","06076224001","06076224020","06076224005","06076203025","06078224022","06078224071","06078224039","06078224030","06078224043","06078212113","06078212037","06078224060","06078213002","06078213088","06080224018","06080213011","06080213045","07001212006","07001224013","07003212040","07003212038","07003212021","07003182079","07003213014","07003203012","07003213025","07003193109","07003203034","07009224007","07009202103","07009224013","07009224061","07009224005","07009192019","07009224032","07009224010","07009224012","07010192182","07010224038","07010212143","07010202430","07010212022","07010202314","07010203075","07010203042","07010193193","07020224001","07033224019","07033224010","07033212001","07033212003","07033224009","07033224013","07033224007","07033202279","07033224016","07033224023","07033212017","07033224014","07033224012","07033224003","07033212113","07033212021","07033213010","07033213088","07033213108","07033213093","07033213009","07044224026","07044224012","07044224031","07044224006","07044212050","07044224034","07044224035","07044212044","07044202065","07044224032","07044224004","07044224029","07044212065","07044224003","07044224022","07044212051","07044202050","07044224009","07044224025","07044224007","07044224015","07044212064","07044212057","07044212061","07063224003","07063212067","07063202057","07063224020","07063224014","07063182193","07063202036","07063192012","07063224008","07063224018","07063224047","07063224063","07063212045","07063224002","07063224011","07063224019","07063224012","07063212069","07063224007","07063224053","07063224009","07063224060","07063224039","07063212102","07063224010","07063192014","07063192250","07063212056","07063224059","07063213058","07063213056","07063213037","07063183046","07063213083","07063183092","07063213090","07063203038","07063183097","07063213089","07063213098","08003224022","08003224018","08003224002","08003224007","08003224021","08003224020","08003212004","08003224008","08003224011","08003212016","08003212012","08003213038","08003213036","08003213014","08008224008","08008224013","08008212046","08008202118","08008212181","08008213021","08017212089","08017192035","08017202301","08017224012","08017224017","08017224022","08017202007","08017202153","08017224013","08017213141","08017213119","08017213147","08017193046","08017213135","08017225002","08017213136","08017213118","08017213131","08018202021","08018192011","08018224024","08018202010","08018212004","08018224023","08018212048","08018224013","08018224002","08018224025","08018202023","08018224021","08018202049","08018224017","08018192002","08018213004","08018203008","08018213012","08019212028","08019224025","08019202153","08019224003","08019193013","08019203004","08020224023","08020224026","08020202023","08020224012","08020224030","08020192127","08020224016","08020192066","08020202108","08020224031","08020224018","08020224011","08020224032","08020212033","08020212061","08020213041","08020213042","08020203047","08020203024","08020213023","08020213025","08020213011","08020203022","08020225001","08022192024","08022224002","08022224007","08022212133","08022192048","08034224016","08034224043","08034224018","08034212097","08034224038","08034212132","08034202251","08034202251","08034212003","08034224034","08034202139","08034213078","08034213071","09001224048","09001224046","09001192109","09001224042","09001224078","09001224049","09001212043","09001224086","09001212111","09001224065","09001224028","09001224045","09001224059","09001212094","09001224012","09001224043","09001224062","09001224076","09001224050","09001212042","09001224054","09001192017","09001224094","09001224085","09001224061","09001224051","09001202031","09001224063","09001224089","09001224097","09001224044","09001212065","09001224092","09001224019","09001212044","09001212024","09001224073","09001224091","09001213021","09001213080","09001213123","09001203039","09001213047","09001203114","09001213071","09001183117","09001203076","09001213106","09001213055","09001213133","09001203030","09001213127","09001213114","09001213009","09004202045","09004224011","09004224013","09004224002","09004213020","09006213147","09006213119","09006213133","09006213120","09006203001","09014224005","09014192039","09017212086","09017202136","09017212040","09017212090","09017224001","09017224036","09017224024","09017212095","09017213112","09017213127","09017183200","09017213082","09017213148","09017183168","09017213084","09017225001","09017213022","09017213081","09017213068","09017225004","09017203052","09017213001","09017225007","09022224003","09022212037","09022213016","09022213031","09024224011","09024224006","09024224020","09024224012","09024224012","09024192165","09024224013","09024213067","09032192093","09032213046","09035192030","09035213046","09035183105","09035213036","09042224024","09042212020","09044224017","09049193125","09049193042","09049193067","09049213016","09049193041","09049213014","09049193137","09049213027","09050212048","09050202022","09050192009","09050202127","09050212009","09050193019","09051212010","09051224014","09051224028","09051213018","09051203015","09052192032","09052213057","09052203040","09053212021","09053212054","09053224007","09053224015","09053224024","09053224021","09053224016","09053213008","09053193068","09054202068","09054224013","09054203070","09054225001","09054203073","09054213099","09054203029","09054213030","09054193130","09054203098","09055212032","09055224008","09055203010","09055203038","09057212021","09057212026","09057182247","09057212107","09057192072","09057212024","09057224032","09057213237","09057213209","09057213214","09059224039","09059224033","09059224035","09059224078","09059224063","09059192003","09059224060","09059224021","09059224077","09059224032","09059224064","09059224071","09060212049","09060212043","09060212006","09060212017","09060202026","09060212015","09060224004","09060224009","09060212047","09060224005","09060212041","09060213065","09060213084","09060213067","09060213069","09060213070","09060213048","09060213063","09060213079","09060213056","09061192072","09061224001","09061224029","09061192060","09061224016","09061224018","09061213007","09061213006","09061213064","09061213070","09062193025","09062213029","09063212017","09063224007","09063212004","09063225005","10001212180","10001224015","10001192115","10001202024","10001202162","10001202085","10001224001","10001202093","10001224013","10001224021","10001202168","10001213050","10001213042","10001213211","10001213253","10001213058","10001213209","10001195027","10001213057","10001213143","10003224007","10003224004","10003212013","10003224003","10003202001","10003212012","10025212043","10025202120","10025202141","10025212108","10025192007","10025213051","10025203030","10025213087","10027224016","10027212010","10027202078","10027224019","10027202013","10027212031","10027224022","10027212017","10027224009","10027224002","10027224003","10027224014","10027202136","10027224015","10027224013","10027224007","10027202136","10027212045","10027225002","10027213054","10027213030","10027213013","10027213033","10027213042","10027213056","10027213031","10027213064","10027213044","10027213032","10034224001","10034224003","10034224008","10034212025","10034224006","10034224007","10034202007","10034224004","10034224002","10034202054","10034224005","10034213001","10034203054","10047213097","10047213075","10047183036","10066212020","10066212144","10066212099","10066212113","10066212054","10066212146","10066192066","10066212081","10066202022","10066213138","10066213143","10066213070","10066213119","10066213137","10066193142","10066213173","10066213114","10066213139","10066213186","10066183069","10066213171","10066213130","10066213120","10066213134","10066213142","10066193009","10066213169","10066213115","10066213129","10066213188","10066213133","10066213136","10066213141","10066183070","10066213172","10066213111","10066213127","10066213153","10066203028","10066213191","10066213145","10066213060","10066213066","10066213077","10066213013","10068224002","10068212010","10068212015","10068203010","10071212028","10071212026","11001212006","11001192004","11001202016","11001213067","11001213088","11001213073","11001213078","11001203004","11001225008","11014212055","11014212021","11014212073","11014212015","11014212011","11014203024","11014183089","11014213024","11014203023","11014183121","11014213018","11014213004","11037192121","11037212052","11037212040","11037192015","11037202017","11037193206","11037193084","11037213136","11037213164","11044192044","11044202103","11044224014","11044212117","11044192023","11044212110","11044224013","11044212154","11044224004","11044224019","11044224003","11044212072","11044192111","11044212038","11044224010","11044202121","11044212125","11044182312","11044212032","11044182145","11044202051","11044212057","11044224012","11044212094","11044224009","11044212048","11044212093","11044202109","11044212057","11044212116","11044224020","11044193210","11044193220","11044213109","11044183253","11044225006","11044225003","11044213130","11044183187","11044213176","11044225012","11044213077","11044213063","11044183326","11044213038","11044225004","11045224025","11045224044","11045212129","11045224017","11045224022","11045182232","11045213168","11045213024","11048213021","11049212073","11049212077","11049212052","11049213131","11049203070","11049193051","11051212018","11051183012","11051213032","11051213022","11051213036","11051213024","11053193023","12002192078","12003224012","12003224003","12003213049","12003225002","12003213079","12011224105","12011224081","12011202316","12011224024","12011182224","12011224083","12011212034","12011224067","12011224103","12011224107","12011202358","12011224092","12011224066","12011224050","12011224053","12011224055","12011224111","12011202317","12011212145","12011224138","12011224065","12011224033","12011224051","12011212215","12011224135","12011224058","12011224127","12011224144","12011224091","12011224052","12011224005","12011224012","12011224110","12011224106","12011202296","12011212327","12011224139","12011202424","12011212287","12011212159","12011212091","12011224010","12011224086","12011225006","12011203118","12011193153","12011213205","12011193149","12011213238","12011213098","12011213110","12011213043","12011213134","12011213193","12018212012","12018212007","12018212002","12018193026","12018203010","12018213074","12027224001","12027224014","12027224026","12027224013","12027224031","12027212094","12027202159","12027224025","12027224010","12027212157","12027212111","12027224017","12027212161","12027224023","12027225003","12027213063","12027193008","12046212009","12046224011","12046224006","12046202016","12046212028","12046202024","12058224015","12058202050","12058202055","12058212036","12058224029","12058212008","12058212019","12058202046","12058202051","12058224011","12058224020","12058224019","12058202026","12058193079","12058213048","12058213001","12058225010","12058183034","12058183094","12058213063","12079212097","12079212008","12079212076","12079224055","12079212189","12079224082","12079224083","12079224072","12079212181","12079212190","12079224034","12079224053","12079213012","12079213078","12079213031","12079213033","12079213036","12103212004","12103224002","12103192015","12103212065","12103212005","12103212040","12103212057","12103212035","12103192028","12103224018","12103192028","12103224024","12103224004","12103212020","12103203021","12103193052","12103213104","12103213003","12103213061","12103213039","12103203022","12103193029","12103183050","12103213102","12103213091","12103213068","12103193026","12103213069","12103213044","12103213107","12103213025","12103213002","12110224019","12110224024","12110224020","12110225003","12110225001","12110203146","12113224011","12113224012","12113224013","12113202119","12113203079","12113203008","12113203054","12113213101","12114224046","12114224019","12114224028","12114224008","12114224062","12114224031","12114224051","12114224047","12114224060","12114224018","12114224009","12114224020","12114224029","12114225002","12114213111","12114213096","12114213108","12114213091","12114213088","12114213095","12121224006","12121224002","12121213003","12153224004","12153224007","12153224033","12153202014","12153224022","12153224032","12153224028","12153224027","12153224025","12153213023","12153213025","12153203062","12153213007","12155182092","12155224006","12155202134","12155203082","12155213062","12188212016","12188224004","12188212056","12188213061","12188203075","12188203111","12188213134","12188203062","12190202041","12190212004","12190203037","12190213042","12207224021","12207224051","12207224061","12207224038","12207224063","12207224071","12207224074","12207224052","12207212063","12207224073","12207224030","12207224046","12207224055","12207224034","12207203231","12207213212","12207213126","12207225001","12207203223","12207213209","12207213103","12207213243","12212203021","12218224015","12218202046","12218212081","12218213057","12218213104","12218213014","12218213107","12218213125","12218183024","12218213123","12218213112","12219224031","12219224009","12219202107","12219224003","12219224001","12219213061","12219213091","12219213097","12219213112","12219213086","12219213115","12219213105","12219213069","12219203088","12219213030","12219213035","12219213114","12219213117","12219213060","12219203090","12219213116","12219213068","12219213137","12219213138","12219203050","12219213087","12219213024","12219213074","12219213089","12220224019","12220224038","12220202078","12220224004","12220212055","12220224025","12220224024","12220213096","12220225005","12220225004","12220225003","12221203001","12221225002","12222224006","12222213003","12223213025","12224212035","12224224023","12224212094","12224212087","12224212093","12224212123","12224224039","12224213188","12224213172","12224213173","12224203076","12224213101","12224213029","12224213092","12224213140","12224213240","12224213079","12224193230","12224213068","12224213162","12224213192","12224213208","12224213090","12224213220","12224183128","12224213205","12224213089","12224203015","12224193044","12230212014","12230202031","12230212016","12230202039","12230213058","12230213063","12232212081","12232224005","12232212086","12232192085","12232203066","12232213076","12232213105","12232213055","12232213093","12234224003","12234224027","12234224004","12234212105","12234224033","12234224029","12234202060","12234224030","12234224034","12234224011","12234224040","12234224031","12234212067","12234212054","12234224032","12234224012","12234212022","12234213095","12234213025","12234193069","12234203010","12234225008","12262224011","12262224014","12262224012","12262224027","12262224013","12262224045","12262224004","12262224015","12262224015","12262224024","12262213023","12262213109","12262213172","12262225001","12262213148","12262225002","12262213055","12262213114","12262213127","12262213083","12262213084","12262213176","12262213101","12262213057","12270224001","12270224007","12270224008","12273224019","12273224012","12273192027","12273212033","12273213022","12273203053","12273203046","12273213036","12273203004","12273193015","12273203015","12276224065","12276224042","12276224028","12276224017","12276202135","12276224008","12276224020","12276224067","12276224052","12276213018","12276225007","12276225010","12276225005","12276225009","12281224025","12281224027","12281224084","12281224053","12281224023","12281224026","12281224085","12281224039","12281224052","12281224038","12281224063","12281224086","12281224018","12281224054","12281224059","12281224009","12281224071","12281224030","12281224015","12281224017","12281225014","12281213006","12281213009","12281225006","12281193107","12281213053","12281213080","12281213097","12281213015","12281213046","12281213105","12281213049","12281213012","12281213079","12281213059","12282212036","12282224012","12282224009","12282224005","12282224011","12282224008","12282213013","12282213037","12282213025","12283224013","12283224022","12283194036","12283224016","12283224024","12284224007","12284224001","12284224030","12284224026","12284224015","12284224018","12284224024","12284202061","12284212030","12284224011","12284212094","12284224004","12284212090","12284224006","12284224020","12284212088","12284212091","12284213017","12284225004","12284213018","12284213019","12284213002","12285212009","12285225002","12285225001","12285213056","12285213077","12285213031","12286224071","12286212087","12286212025","12286224032","12286212115","12286224049","12286212007","12286224043","12286212015","12286224067","12286224011","12286224035","12286224031","12286224052","12286224062","12286212086","12286212018","12286212136","12286212085","12286213040","12286213063","12286213002","12286225005","12286213058","12286213035","12288213006","12288203005","12288203004","12289224005","12289224009","12290224005","12290212020","12290213015","12290213003","12292224025","12296212017","12296212027","13001202231","13001212006","13001213245","13001213260","13001213271","13001213229","13001213246","13001213190","13001203014","13003212153","13003192164","13007212013","13007212125","13007212119","13007202037","13007202151","13007203026","13007203081","13007203003","13007203033","13011213017","14001212263","14001224008","14001224036","14001212101","14001212262","14001212326","14001212307","14001212286","14001182078","14001224026","14001224006","14001224005","14001192129","14001202047","14001213267","14001213260","14001203021","14001203168","14001213242","14001183044","14001213302","14001213125","14001213141","14001213199","14001213143","14001225004","14001193261","14001213096","14001213255","14001213086","14001213029","14001213226","14001213246","14001193170","14002202262","14002213117","14002193046","14002213085","14002213098","14002213138","14002213096","14004212060","14004212034","14004202531","14004192177","14004212153","14004212083","14004202444","14004212126","14004192006","14004202225","14004212002","14004212143","14004202310","14004202223","14004212479","14004212406","14004202491","14004202204","14004212144","14004202235","14004202236","14004212027","14004203107","14004213200","14004213233","14004213250","14004213184","14004213067","14004213201","14004213248","14004193050","14004213291","14004213260","14004213192","14006212174","14006212162","14006213041","14006213063","14006213185","14006213038","14007212124","14007224001","14007212056","14007213137","14012212167","14012202196","14012212077","14012212095","14012212118","14012203035","14012213009","14012193046","14022212047","14022202318","14022202194","14022212154","14022213061","14022213181","14022213168","14022203163","14028202190","14028202277","14028212180","14028212199","14028212138","14028212142","14028212087","14028212129","14028212093","14028213228","14028213173","14028213186","14028213177","14028213192","14028213202","14028213253","14028213183","14028213193","14028203141","14028203045","14028203149","14028213225","14028213175","14028213203","14028213236","14028213188","14028213250","14028213172","14028213189","14028213190","14028213180","14029212123","14029213038","15001183176","15005202001","15061213027","15061213030","15061213031","15115212006","15115212011","15133202005","15133212021","15133212018","15133212018","15133213041","15133213033","15133213053","15133213017","15133213018","15133213020","15133213046","15133213013","15133213006","16001212163","16001212176","16001202096","16001212196","16001202097","16001202085","16001212228","16001212045","16001224001","16001202048","16001202107","16001224002","16001212183","16001212162","16001212171","16001193008","16001193007","16001193009","16001193071","16001213102","16005224012","16005224016","16005224029","16005224002","16005224013","16005224019","16005202004","16005224006","16005212038","16005224026","16005224015","16005224028","16005224024","16005224017","16005224010","16005224009","16005224011","16005203013","16005193014","16015192118","16015212025","16015212012","16015182068","16015212247","16015212098","16015212062","16015212036","16015183021","16015203006","16017192167","16017202063","16017212172","16017212016","16017212325","16017202183","16017212174","16017213169","16017203096","16017213279","16017213095","16017213282","16024192065","16024213018","16024213013","16024213001","16027224039","16027224101","16027224011","16027224042","16027224091","16027224100","16027224105","16027224012","16027224056","16027224078","16027224047","16027224099","16027224076","16027224103","16027224004","16027224064","16027224053","16027224046","16027224107","16027224010","16027224033","16027224095","16027224037","16027224093","16027224016","16027224034","16027224065","16027224043","16027224089","16027224084","16027224030","16027224059","16027224104","16027224055","16027224023","16027224083","16027224106","16027224015","16027213050","16027225004","16027203005","16027225003","16039212033","16039202109","16039182306","16050224009","16051224005","16051212013","16051192020","16051212019","16051213056","16051213053","16051193002","16051213055","16051213037","16051213054","16052212095","16052224010","16052224017","16052212014","16052224018","16052224004","16052224020","17003212129","17003213086","17003213082","17003213064","17003213062","17004192134","17004202107","17004213271","17004213189","17004213178","17005212012","17006202163","17006212173","17006202161","17006212174","17006202162","17006202101","17006212134","17006202115","17006213022","17006213061","17006213071","17006213002","17006203034","17006193034","17006213066","17006213119","17006213021","17007212202","17007212004","17007212215","17007212226","17007212003","17007212170","17007212208","17007212216","17007203097","17007213153","17007213162","17007213072","17008212132","17008202051","17008213101","17008213010","17008213089","17009202051","17011212083","17011212055","17011202001","17011224002","17011202001","17011213038","17011203029","17011213010","17011213030","17011213043","17011213014","17011225003","17011213031","17014212037","17014203024","17014213017","17022224015","17022224013","17022212104","17022212119","17022212033","17022224001","17022212189","17022212103","17022224040","17022202015","17022212165","17022212046","17022224036","17022202204","17022202307","17022224021","17022212155","17022224034","17022212093","17022224038","17022224010","17022212164","17022224002","17022212108","17022212035","17022212159","17022224028","17022224011","17022224035","17022224014","17022212118","17022212188","17022224007","17022224003","17022224004","17022212205","17022224012","17022212092","17022212048","17022212109","17022212193","17022224032","17022224041","17022202100","17022213092","17022213018","17022213080","17022225005","17022203012","17022213001","17022213134","17022213132","17022203173","17022213015","17022213143","17022213013","17022213083","17022213146","17022213180","17022203064","17022213179","17022213028","17022213076","17022213149","17026212008","17026212033","17026212002","17026202017","17026212020","17028202030","17028202076","17028213090","17028213118","17028203078","17028203033","17028203145","17041212015","17041213088","17041193109","17041193015","17075202003","17075184001","17075224001","17075224002","17075213022","17077212180","17077212046","17077212094","17077213237","17077213078","17077213206","17077203101","17079212095","17079212057","17079192140","17079212020","17079212039","17079212087","17079203019","17079213165","17079213098","17108212102","17108212031","17108212030","17108212104","17108212048","17108212110","17108202061","17108212078","17108212005","17108212027","17108193003","17109212048","17109202109","17109213064","17114212255","17114213245","17114213241","17114213024","17117212054","17117212059","17117224002","17117213032","17117213037","17117213004","17117213039","17125212115","17125212054","17125212090","18005202081","18005212034","18005212015","18005202064","18005212029","18005212087","18005213053","18005213019","18005213057","18005213006","18005213079","18005213050","18005213083","18005213089","18005213082","18005213067","18005213065","18005213058","18005213088","18005213043","18036193010","18036193013","18037212054","18037212027","18037212030","18037213080","18037213069","18037213076","18037213081","18038224051","18038212022","18038224019","18038224013","18038224002","18038202064","18038224044","18038202033","18038202146","18038224017","18038212002","18038224021","18038224024","18038202121","18038224052","18038224008","18038212035","18038224006","18038224012","18038224036","18038202111","18038192053","18038224043","18038224016","18038224014","18038224009","18038224023","18038182014","18038224034","18038224037","18038224048","18038224046","18038224042","18038224032","18038224025","18038224027","18038224005","18038224007","18038224040","18038213054","18038213062","18038213058","18038213059","18038213045","18038213069","18053193011","18053203005","18061202013","18061194037","18061183015","18063224013","18063224007","18063212003","18063224011","18063213020","18067224008","18067224013","18067224009","18067193063","18073224007","18073224009","18073224006","18073224012","18073224010","18073202028","18073224015","18073224003","18073224011","18073212013","18073224010","18073224001","18073213007","18074202020","18074212013","18074213027","18074213029","18074213021","18074213012","19001213061","19001193098","19001213020","19001225001","19002212100","19002212093","19002212040","19002212027","19002212086","19002212030","19002203043","19024212036","19024212063","19024212100","19024183064","19024213055","19024193088","19027212038","19027212024","19027212012","19027212013","19027212031","19027203029","19027203012","19027203031","19027213005","19027203017","19027213063","19027213071","19027213062","19037203021","19042212054","19042212088","19055182033","19055213035","19062202108","19062212004","19062212002","19062212032","19062212003","19062212034","19062212014","19062203014","19062203042","19062203107","19062213066","19062213039","19062213083","19062203028","19062213044","19067212028","19067224027","19067192038","19067224017","19067224011","19067224008","19067224025","19067224028","19067224029","19067224026","19067224024","19067202161","19067213168","19067213162","19067213165","19067225003","19067193021","19067213037","19067213119","19067213181","19092213072","19103212003","19103202136","19103202140","19103212039","19103212018","19103202114","19103213041","20005224003","20005212125","20005202175","20005212005","20005212084","20005213113","20005193010","20005203223","20005193255","20005193220","20014224005","20014224012","20014224016","20014202014","20014224010","20014224015","20014224007","20014212109","20018203077","20018213084","20018193022","20018193102","20021224025","20021212163","20021224037","20021212129","20021212245","20021224005","20021212063","20021212031","20021212071","20021224013","20021212175","20021202161","20021212166","20021224038","20021224048","20021212044","20021212103","20021212077","20021224045","20021213022","20021213212","20021213218","20021213193","20021213081","20021213234","20021213060","20021203172","20021213070","20021213085","20021213209","20021213202","20021213152","20021213216","20021203129","20021203005","20021213075","20021213229","20021213105","20021213219","20021213175","20021213199","20021213217","20021225002","20021213210","20021213126","20021213194","20021213184","20021213034","20021213222","20021213057","20021213190","20021213226","20021213200","20021193251","20021213101","20021213179","20021213023","20021213227","20021213079","20021183049","20021213102","20021213033","20021213231","20021203047","20021203009","20021213208","20028224003","20028224013","20028202170","20028224004","20028202088","20028224005","20028224002","20028182116","20028202189","20028202114","20028212036","20028224012","20028212047","20028213087","20028183157","20028213099","20028203041","20031202268","20031202309","20031224016","20031224002","20031182144","20031202262","20031224012","20031212047","20031202308","20031212073","20031224010","20031212046","20031202159","20031202082","20031224030","20031212083","20031183272","20031213128","20031225001","20031203004","20031213092","20031213011","20031213003","20031203205","20031193239","20031225002","20031213086","20035212097","20035212118","20035212015","20035212063","20035202131","20035212056","20035212114","20035202131","20035212058","20035193049","20035203143","20035203060","20035193153","20035213044","20039224011","20039224005","20039202138","20039213099","20039213091","20039225002","20045224015","20045224028","20045224040","20045224033","20045224003","20045213106","20045213195","20045213203","20045213013","20045213174","20045213213","20045213046","20045213186","20045213177","20045213221","20045213008","20045203036","20045213139","20045213172","20045213048","20045213052","20053212032","20053212061","20053224008","20053193269","20055192061","20055224002","20055212112","20055212189","20055212029","20055202218","20055192110","20055225004","20055213242","20055213203","20055183094","20055213221","20055193056","20056202008","20056212075","20056202065","20056213112","20056213004","20056213104","20056213105","20056213113","20056213110","20056213114","20056213109","20057212117","20057224019","20057212046","20057212127","20057224014","20057224012","20057224015","20057212090","20057203015","20057213170","20057193074","20057213043","20057193082","20057213192","20057213112","20057213008","20058224021","20058183008","20059224001","20059224004","20059212008","20059212005","20059202060","20059213064","20059203114","20059213097","20059213011","20059225003","20059213169","20059213045","20059225004","20059213038","20059213105","20059213098","20059213131","20059183173","20059213070","20059203004","20060212103","20060212052","20060212033","20060212017","20060212138","20060213057","20060213282","20060213157","20060213239","20060213149","20060213251","20060213123","20060213042","20060213164","20060213143","20060213125","20060213269","20060203339","20060213257","20060213062","20060213179","20060213207","20060213185","20060213140","20060213136","20062212042","20062212094","20062212013","20062212104","20062202064","20062213006","20062213042","20062213061","20067224001","21001224005","21001224014","21001224007","21001224016","21001202035","21001213026","21001213003","21001225001","21001203042","21002224005","21003224029","21003224053","21003224068","21003224045","21003212061","21003192112","21003212089","21003224025","21003224073","21003212108","21003224060","21003224022","21003212078","21003224065","21003224046","21003224040","21003202166","21003224010","21003224064","21003212106","21003224026","21003213169","21003203181","21003213166","21003193205","21003213067","21003213149","21003213127","21003193051","21003213186","21003213071","21003213162","21003193027","21039224047","21039224015","21039224020","21039224019","21039192036","21039212029","21039224017","21039224024","21039213010","21039213054","21039213027","21039213002","21045224004","21045224002","21045224007","21045224012","21045224006","21045212034","21045224013","21045224011","21045224003","21045213005","21045213057","21045193026","21045213059","21045203016","21045193047","21045213017","21045213004","21045213019","21045213011","21045213037","21045213062","21045213058","21051224037","21051224001","21051224014","21051202046","21051224041","21051224016","21051212105","21051224002","21051202051","21051224015","21051224005","21051202053","21051224053","21051202039","21051224024","21051202040","21051224026","21051224003","21051224074","21051213107","21051213015","21051213017","21051225009","21051203024","21057225001","21061224012","21061224006","21061224025","21061224015","21061224019","21061224037","21061224036","21061224024","21061224027","21061224002","21061202012","21061202114","21061192011","21061224003","21061212042","21061224033","21061212048","21061213020","21061213024","21061225003","21061225006","21061213042","21061213003","21061213037","21061213002","21062224006","21062202404","21062224002","21062202420","21062192044","21062202228","21062202419","21062203056","21062213031","21062213032","21062225003","21062213005","21062213045","21062185005","21062213038","21062193026","21064224026","21064224003","21064224033","21064224018","21064224038","21064224027","21064224007","21064224013","21064224002","21064224005","21064224020","21064224016","21064224035","21064224022","21064224021","21064224034","21064224039","22001224006","22001202013","22001212009","22001212042","22001202064","22001192017","22001213019","22011212004","22011202009","22011212003","22011213010","22011213004","22035212023","22035212012","22035202008","22035224002","22035224003","22035212050","22035212113","22035212036","22035213134","22035213048","22035213127","22035213133","22035213151","22035213118","22035225001","22035213150","22035213125","22035193088","22035213057","22082212058","22082224005","22082212059","22118212021","22118212052","22118213055","22125212022","23002224046","23002224015","23002212008","23002224017","23002212037","23002202033","23002224044","23002212018","23002212033","23002224048","23002213057","23002213015","23003224013","23003202254","23003224002","23003202100","23003202189","23003224009","23003192039","23003212051","23003212081","23003202094","23003202053","23003213021","23003203065","23003213054","23003213069","23003213038","23003213024","23003203130","23003225001","23003193012","23003193019","23007182037","23007183003","23007193118","23007203033","23013224011","23013212085","23013212070","23013202048","23013212011","23013203088","23013213011","23013213020","23013193078","23013225002","23014203011","23016193144","23016213049","23016213120","23016183202","23016203005","23016213189","23016213166","23016213170","23017202096","23017202096","23017202195","23017195002","23017203016","23018212058","23018202081","23019202279","23019212127","23019182098","23019212100","23019193112","23019213029","23019213043","23019203013","23020212033","23020212023","23021212017","23022182144","23022212091","23022202250","23023212071","23023224004","23023212130","23023212115","23023202027","23023212004","23023182144","23023212050","23023212056","23023202001","23023202257","23023202088","23023213005","23023213108","23023213016","23023213118","23023193039","23023203028","23023213120","23023213096","23023213002","23023213119","23023213115","23024212017","23025212048","23025224002","23025202256","23025202323","23025212172","23025212111","23025213013","23025193078","23025213033","23025213047","23025213001","23025213037","23026212063","23026212020","23026224029","23026183027","23026225002","23028203044","23028213067","23028213023","23030192084","23030212036","23030212018","23030212021","23030212015","23030212029","23030212054","23030192093","23030213068","23030203068","23030213103","23030213038","23030213072","23030213035","23030213104","23030213042","23030203056","23030213087","23031212024","23032224002","23032212001","23032224005","23032212013","23032212005","23032213009","23032213054","23032203037","23032213044","23032213002","23032213046","23032213018","23032213061","24001212251","24001212200","24001202054","24001212220","24001224010","24001224007","24001224015","24001224004","24001224009","24001224014","24001212254","24001202045","24001192349","24001212204","24001224023","24001213151","24001183045","24001203010","24001213150","24001203042","24001213023","24001213172","24001193389","24001203034","24006212205","24006212103","24006202004","24006192123","24006202008","24006194075","24006213112","24006213215","24006213032","24006225002","24006213176","24006193132","24006213190","24006203044","24006225001","24006213213","24006213165","24006183024","24006213020","24006213164","24006203013","24006183115","24009212078","24009212033","24009202110","24009212032","24009212146","24009193042","24009213039","24022212092","24022212014","24022212083","24022212055","24022212088","24022203060","24022203070","24022213064","24022203059","24022213063","24022213044","24022203057","24022213027","24030224016","24030182012","24030224005","24030212090","24030203046","24030213045","24030203022","24030193002","24040212023","24040202122","24040202123","24040213017","24040213023","24040203071","24040193092","24040213040","24040193119","24040203077","24053212042","24053212039","24053212047","24053212077","24053212052","24053212076","24053212011","24053213038","24057202041","24057202042","24061192058","24061212110","24061224029","24061212070","24061212143","24061224031","24061212145","24061202069","24061212160","24061192008","24061192015","24061212108","24061203049","24062224003","24062224025","24062224021","24062224023","24062213055","24062213155","24062213140","24062213154","24062203102","24062183010","24062213189","24062213160","24062213178","24062213124","24062213175","24062213153","24062213180","24062213098","24063192039","24063224015","24063224027","24063202167","24063212020","24063224003","24063212089","24063202024","24063212002","24063224005","24063182023","24063202276","24063224010","24063212003","24063212008","24063224012","24063202181","24063203030","24063225003","24063203080","24063225005","24063213082","24066224005","24066212038","24066224023","24066212076","24066213011","24066213013","24066213012","25001224063","25001224052","25001224029","25001224006","25001224026","25001224065","25001212058","25001224043","25001212054","25001224046","25001224031","25001224062","25001224036","25001224053","25001224047","25001224050","25001212105","25001224037","25001224049","25001224005","25001212121","25001212125","25001224061","25001224051","25001224013","25001224060","25001194048","25001224061","25001224033","25001224044","25001212077","25001224032","25001202133","25001224054","25001213068","25001213184","25001225009","25001225002","25001213154","25002224039","25002212035","25002224070","25002224041","25002202051","25002224031","25002224059","25002224050","25002224010","25002224063","25002224032","25002224004","25002224043","25002224011","25002224052","25002224074","25002224064","25002224015","25002224008","25002212110","25002212111","25002224045","25002224072","25002224053","25002224033","25002224009","25002224003","25002224057","25002212004","25002224060","25002224012","25002224018","25002224076","25002212058","25002224002","25002202271","25002224005","25002202200","25002224044","25002212030","25002224019","25002224047","25002212081","25002224029","25002224049","25002203041","25002213004","25002225006","25002213002","25002203061","25002213094","25002225003","25002213089","25004225001","25004225003","25005212010","25005213021","25007202026","25007212072","25007213091","25007213067","25007213066","25012212190","25012212178","25012183161","25012213169","25012203093","25012213108","25012213158","25012193174","25012213241","25012203190","25015224026","25015224047","25015224016","25015212001","25015224014","25015212012","25015224005","25015224001","25015224009","25015224040","25015224018","25015224004","25015224025","25015224011","25015224034","25015224045","25015224039","25015224036","25015224035","25015224038","25015224049","25015224020","25015203001","25018224008","25018224001","25018224038","25018224009","25018224020","25018224011","25018224021","25018224002","25018224032","25018192063","25018224015","25018224031","25018182061","25018224040","25018224028","25018182061","25018224037","25018213044","25018213006","25018213047","25018213041","25020212003","25020213042","25020213056","25020213015","25020213025","25020213048","25020213012","25020213053","25020213057","25052212037","25052213006","25052213057","25052213052","25052213035","25052213002","25052213064","25052213070","26001224037","26001224023","26001224011","26001224026","26001224019","26001224036","26001192306","26001212187","26001224068","26001224045","26001192035","26001224025","26001224032","26001192181","26001224064","26001203279","26001193501","26001203032","26001203247","26001183357","26001213275","26001225010","26001213225","26001225007","26004224035","26004212033","26004182020","26004224031","26004212021","26004224028","26004212057","26004224032","26004224018","26004212014","26004202004","26004224011","26004224027","26004212052","26004212044","26004183084","26004213139","26004203007","26004213043","26004213134","26004213052","26004193041","26010224045","26010224026","26010224013","26010224055","26010224065","26010224040","26010224036","26010224025","26010212033","26010224020","26010224039","26010224079","26010224051","26010224033","26010224062","26010224024","26010224021","26010224006","26010224038","26010224010","26010212023","26010212071","26010224028","26010224035","26010224073","26010224052","26010213031","26010225004","26010193077","26010193040","26010193064","26010225005","26020212106","26020224002","26020224001","26020224006","26020212378","26020212361","26020182131","26020212348","26020202047","26020212023","26020183141","26020213025","26020213011","26020203180","26020213115","26020213102","26020213155","26020213170","26020213181","26020213177","26024224046","26024224037","26024212073","26024202136","26024224052","26024224025","26024224055","26024224026","26024224007","26024224071","26024224036","26024225001","26024203005","26024203164","26024225007","26024225011","26024225009","26024225004","26024203158","26024213123","26024203193","26024203001","26028224063","26028202475","26028192281","26028202412","26028224130","26028224087","26028224160","26028224169","26028212097","26028212170","26028224133","26028224033","26028202149","26028212004","26028224061","26028224017","26028194094","26028224065","26028224070","26028212205","26028192126","26028224117","26028224091","26028212101","26028192350","26028224096","26028224031","26028224043","26028224122","26028224054","26028194086","26028224064","26028224074","26028224018","26028224067","26028212108","26028224088","26028212098","26028212251","26028212207","26028224121","26028212253","26028224053","26028212057","26028212005","26028224134","26028212072","26028224173","26028224015","26028224176","26028212244","26028224040","26028192489","26028224097","26028224032","26028212061","26028224044","26028212138","26028224131","26028224120","26028192345","26028212252","26028202479","26028224132","26028224094","26028212058","26028202500","26028212060","26028202094","26028224069","26028212232","26028202418","26028224138","26028213106","26028203062","26028203144","26028203092","26028213274","26028213201","26028213307","26028203060","26028203220","26028213118","26028213226","26028213115","26028193338","26028213282","26028203072","26028213269","26028213347","26028213092","26028203029","26028213296","26028203116","26028213212","26028213227","26028213286","26028203088","26028203024","26028213117","26028213225","26028213293","26028203030","26028213158","26028213016","26028213315","26028203262","26028203096","26028213157","26028213068","26028203278","26028203031","26028193171","26029212120","26029192185","26029182136","26029202286","26029213078","26029213091","26029213081","26029203149","26029213014","26031212087","26031224003","26031193197","26031213096","26031213144","26031213127","26031225001","26031213129","26031225003","26031213001","26031213021","26031213111","26031213059","26031213004","26031213132","26031213149","26037202135","26037203135","26037203153","26037213016","26037213147","26039212030","26039212138","26039202147","26039212115","26039212008","26039212122","26039212134","26039182078","26039212069","26039202031","26039213086","26039203032","26039213029","26039213055","26045202171","26045225004","26045203148","26060202343","26060212054","26060193040","26060213087","26060213049","26060213099","26063224011","26063224010","26063213056","26066212033","26066212029","26066212027","26066212041","26066202049","26066203058","26066213002","26066193013","26066203057","26066183085","26066213039","26066183156","26067224001","26067202175","26067202244","26067192034","26067212006","26067182025","26067182087","26067202084","26067212005","26067202093","26067182148","26067213017","26067213088","26067213087","26067213045","26067213078","26067213073","26068193102","26068183024","26069224015","26069224004","26069224008","26070224021","26070212024","26070224013","26070224052","26070182089","26070224014","26070192054","26070203035","26070193049","26070213071","26070203067","26070213042","26070213094","26070213098","26071224027","26071224041","26071224060","26071224049","26071224063","26071224005","26071224028","26071224032","26071202017","26071202070","26071224037","26071224004","26071224062","26071224048","26071202071","26071224064","26071224053","26071202090","26071224025","26072202038","26072202014","26072202005","26072192132","26072202187","26072202188","26072182007","26072193190","26072203056","26072213010","26073202108","26073202073","26073224024","26073182060","26073224014","26073224025","26073212146","26073212030","26073203065","26075212093","26075224019","26075224010","26075212052","26075224011","26075224021","26075213057","26075193006","26075225002","26075183144","26075193068","26075193007","26075213058","26075193104","26076224006","26077224038","26077224029","26077224037","26077224051","26077224040","26077224053","26077224046","26077212077","26077224030","26077224024","26077224016","26077202042","26077192184","26077224048","26077224049","26077224006","26077224003","26077213151","26077213038","26077213155","26077213119","26077213071","26077213090","26077213094","26077225009","26079224004","26090224021","26090224020","26090224017","26090224007","26090224004","26090224019","26090224006","26090224005","26090224010","26090224013","26090224014","26090224002","26090224008","27005212003","27005212013","27005213008","27017224009","27017224001","27017224004","27017224032","27017224023","27017224024","27017224007","27017224010","27017224033","27017224002","27017224029","27017225001","28001184005","28001202011","28001212036","28001213181","28001213053","28001225012","28001203037","28001213246","28001213198","28001213235","28010224040","28010224043","28010202211","28010224052","28010224030","28010224038","28010224065","28010224033","28010224037","28010212094","28010224066","28010224051","28010224015","28010224031","28010224026","28010202273","28010224017","28010224064","28010224048","28010224014","28010224025","28010212097","28010224007","28010224050","28010202228","28010224057","28010225001","28010225003","28010213076","28010203090","28026202126","28026193026","28026195005","28041224028","28041224037","28041224032","28041224011","28041224042","28041212004","28041213074","28041203062","28041213016","28067224001","28067224002","28076224024","28076224033","28076224030","28076224017","28076224021","28076224008","28076224023","28076224044","28076224031","28076224012","28076224011","28076203002","28076225002","28078224047","28078192007","28078202002","28078224040","28078224015","28078212028","28078212005","28078224038","28078202019","28078202085","28078224016","28078202117","28078212030","28078224028","28078224032","28078224003","28078224034","28078224041","28078224017","28078224035","28078192013","28078224025","28078213001","28078213027","28078213026","28078213038","28078225001","28078225003","28079224004","28079224012","28079224013","28079212116","28079212088","28079202208","28079192131","28079212087","28079212052","28079212091","28079212106","28079212007","28079194200","28079212069","28079213104","28079213158","28079213157","28079213173","28079213153","28079213102","28079213134","28080224051","28080202101","28080224097","28080224038","28080224012","28080224045","28080224049","28080224072","28080224081","28080224022","28080224073","28080224006","28080224083","28080224009","28080212037","28080224044","28080224059","28080224087","28080212132","28080212121","28080224096","28080224023","28080224094","28080224027","28080212106","28080224076","28080202350","28080202366","28080224048","28080224079","28080224025","28080224011","28080224031","28080224032","28080224102","28080224103","28080224024","28080224004","28080224037","28080224040","28080224043","28080224015","28080224053","28080224007","28080212055","28080212075","28080202357","28080224085","28080224041","28080202348","28080202353","28080213092","28080213114","28080203215","28080213028","28080203038","28080213115","28080213086","28080203186","28080203139","28080193012","28080213091","28082224050","28082224008","28082224065","28082224062","28082224054","28082224020","28082212049","28082202025","28082224068","28082224036","28082224067","28082202109","28082212150","28082202349","28082212126","28082212153","28082192187","28082212164","28082202192","28082182034","28082224063","28082202349","28082224072","28082224075","28082224026","28082224005","28082192191","28082194026","28082224059","28082213174","28082183084","28082213219","28082213129","28082193248","28082225006","28082213240","28082213175","28082213054","28082213131","28082193237","28082213287","28082213271","28082213053","28082213087","28082213015","28082213213","28082213222","28082213280","28082203330","28082213100","28082213211","28082203070","28082213037","28082213208","28082203123","28082203065","28082183039","28082203277","28082213014","28082213185","28083224014","28083224012","28083224006","28083192017","28083224008","28083194013","28083224009","28083212013","28084213019","28084213025","28085224012","28085224004","28085224009","28085224008","28085224007","28085224005","28085213011","28085203035","28085213009","28085203055","28087212003","28087202043","28087212041","28087202014","28087202118","28087202002","28087212016","28087212023","28087182211","28087202180","28087182211","28087203077","28087193060","28087213033","28087203098","28087203071","28088212003","28088212007","28089224001","28089202194","28089212086","28089212044","28089212011","28089224002","28089212022","28089224004","28089224006","28089212004","28089212084","28089202157","28089203145","28089203059","28089203025","28089225004","28089213057","28089213041","28089213163","28089203073","28089213048","28089213109","28089203001","28089213162","28089213113","28089203156","28089203071","28089203154","28089203084","28089225001","28089203158","28089213095","28089203143","28089203070","28089203131","28093224033","28093224012","28093224056","28093224007","28093224066","28093224015","28093224019","28093224025","28093224065","28093224075","28093224047","29001192072","29001212002","29001192024","29001192003","29001202060","29001193108","29001203003","29001203040","29012212018","29017224005","29017224008","29017213080","29017213072","29017193075","29017213078","29017213071","29017213070","29017213079","29020224003","29020192007","29020224017","29020224004","29020224008","29020224019","29020224014","29020212002","29020224016","29020213001","29020213013","29023224001","29023202002","29023224003","29023212021","29023213050","29023213062","29023213060","29023213021","29028224061","29028224054","29028224072","29028224052","29028224058","29028224069","29028224049","29028224053","29028224062","29028224005","29028224030","29028202052","29028224017","29028212071","29028224019","29028224011","29028212110","29028213104","29028213101","29028213086","29028213110","29028213099","29028213100","29028213109","29028213108","29028213102","29033213008","29036182068","29036212089","29036224012","29036212069","29036224027","29036212003","29036224023","29036224026","29036224016","29036225002","29036203034","29036213041","29036213047","29037224014","29037212043","29037224001","29037224003","29037202014","29037224011","29037224002","29037224006","29037183014","29037213036","29039212012","29039202020","29039213009","30001212040","30001203174","30001213183","30001203036","30001213010","30001213175","30001203002","30001213052","30001213113","30004212049","30004213011","30004203037","30004213010","30004203039","30004213125","30006192048","30006212015","30006213055","30007194012","30007224014","30007224011","30007183009","30007213119","30007213125","30007213116","30007213120","30007213099","30007225002","30008212033","30008213088","30008213114","30008213113","30009212070","30010213009","30011212013","30011213002","30015224004","30015224006","30015202046","30015213037","30019213067","30019213065","30022182226","30022224015","30022212026","30022224016","30022224006","30022212052","30022213077","30022225002","30022213050","30022213063","30023212425","30023224098","30023224091","30023202203","30023212240","30023224084","30023212260","30023212421","30023182514","30023212428","30023182514","30023213236","30023213410","30023225025","30023203030","30023213414","30023225010","30023213218","30023225013","30023225007","30023213383","30023225018","30023225023","30024212100","30024212021","30024212159","30027202082","30027224047","30027213102","30028212037","30028213090","30028213029","30028213001","30028213057","30028213026","30029212008","31001212044","31008202217","31008202178","31008212085","31008212125","31008202118","31008212176","31008212090","31008193013","31008203126","31032212009","31050202148","31050212033","31050192048","31050213060","31051224003","31051224004","31051212018","31051224001","31053224002","31057225001","32001212053","32001203001","32001213060","32001203009","32001213049","32019224033","32019224025","32019224034","32019224026","32019224032","32019224019","32019224014","32019224020","32019224006","32019212049","32019224021","32019213018","32019213016","32019213035","32019225002","32019213045","32019203044","32019213050","32019203056","32035212018","32035212027","32035213088","32035213112","32035213099","32035213104","32035213092","32035213111","32035213089","32035213041","32039224010","32039202080","33001224090","33001224009","33001224043","33001224094","33001224001","33001212085","33001182325","33001224057","33001224020","33001224108","33001224059","33001224040","33001224027","33001224003","33001192221","33001224077","33001224067","33001193156","33001203012","33001193143","33001203133","33001213122","33001225004","33001225008","33001213083","33002212004","33002194010","33002224001","33002193008","33002203004","33002195006","33003224035","33003224003","33003194023","33003224005","33003212003","33003224015","33003224014","33003224037","33004224086","33004224077","33004224052","33004224039","33004224060","33004224083","33004224041","33004224065","33004194077","33004224069","33004224062","33004224032","33004224050","33004212104","33004224005","33004182152","33004212008","33004212116","33004213141","33004213294","33004213343","33004213321","33004213358","33004225002","33004203067","33004213288","33004213297","33004213105","33004213312","33004203161","33004213214","33004213286","33004213289","33004193001","33004213175","33004225014","33004213347","33004213162","33004213263","33004213316","33004225008","33005182164","33005193008","33005213026","33006212011","33006213013","33007202079","33007192088","33007212054","33007224002","33007182237","33007192219","33007182265","33007224010","33007192196","33007224021","33007212017","33007212061","33007213128","33007193100","33007225002","33007193101","33007213076","33007213068","33009224026","33009224008","33010212052","33010192048","33010192021","33010213083","33010213077");
			return $studentIds;
		} 

		public function testCaseSingleMarksheetDetails($enrollment=null){
			$marksheet_type = 'pass';
			$marksheet_component_obj = new MarksheetCustomComponent;
			$current_admission_session_id = Config::get('global.current_admission_session_id');
			$exam_month = Config::get('global.current_exam_month_id');
			$pastdataurl=config("global.pastdata_document");
			$pastdatadocument=config("global.PAST_DATA_DOCUMENT");
			$title = "Single Marksheet";
			$table_id = "Single Marksheet";
			$formId = ucfirst(str_replace(" ","-",$title));
			$studentdata=Student::where('enrollment',$enrollment)->first(['id','enrollment']);
			$documents = '';
			$totalMarks = 0;
			$grandFinalTotalMarks = 0;
			
			if($enrollment == null || $enrollment == ''){
				return false;
				return redirect()->route('downloadBulkDocument')->with('message',"Enrollment is not in correct format.");
			} 
			
			$serial_number = $marksheet_component_obj->getSerialNumber(@$enrollment);
			
			$examresultfields = array('final_result','exam_month','exam_year','total_marks','percent_marks','additional_subjects');
			$final_result = ExamResult::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first($examresultfields);
			if(empty($final_result)){
				return false;
			}
			$pastInfo = Pastdata::where('ENROLLNO','=',$enrollment)->orderBy('id','DESC')->first();
			if(!empty($pastInfo) && !empty($final_result)){
				$field=["pastdatas.ENROLLNO  as enrollment","pastdatas.CLASS  as course","pastdatas.NAME as name" ,"pastdatas.FNAME as father_name","pastdatas.MNAME as mother_name", "pastdatas.DOB as dob"];
				$student = Pastdata::where('ENROLLNO','=',$enrollment)->orderBy('id','DESC')->first($field);
			}else{
				$student = Student::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first();
			}
			$combination = '';
			$resultDate = '';
			if(isset($final_result->exam_month) && isset($final_result->exam_year)){
				$combination = $final_result->exam_month . ' '. $final_result->exam_year;
			}
			$courseVal = '';
			$resultsyntax = array('999'=>'AB','666'=>'SYCP','777'=>'SYCT','888'=>'SYC','P'=>'P');
			
			if(!empty($student)){ 
				$application = Application::where('student_id','=',@$student->id)->orderBy('id','DESC')->first();
				
				$student->display_exam_month_year = $marksheet_component_obj->getDisplayExamMonthYear($combination);			
				$newexamresultfields = array('exam_year','exam_month','result_date');
				$final_result_data = ExamResult::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first($newexamresultfields);
				if(isset($final_result_data->result_date) && $final_result_data->result_date != ""){
					 $resultDate = date("d-m-Y", strtotime(@$final_result_data->result_date));
					//$dtarr = explode('-',@$final_result_data->result_date);
					//$resultDate = $dtarr[2]."-".$dtarr[1]."-".$dtarr[0];
					
					
				}
				
				$documents = Document::where('student_id','=',$student->id)->orderBy('id','DESC')->first();
				$courseVal = $student->course;			
				
				$address = Address::where('student_id','=',$student->id)->orderBy('id','DESC')->first();	
				
				$findInFlag = 'Student';
				$dtarr = explode('-',$student->dob);
				$student->dob = $dtarr[2]."-".$dtarr[1]."-".$dtarr[0];
			} else {
				if(empty($final_result)){
					$courseVal = $pastInfo->CLASS; 
					$subjectCodeIds = $marksheet_component_obj->_getSubjectsCodeId($courseVal);
					
					$final_result->final_result = @$pastInfo->RESULT;
					$final_result->total_marks = @$pastInfo->TOTAL_MARK;
					$final_result->percent_marks = @$pastInfo->Percentage;
					
					$dtarr = explode('-',@$pastInfo->ResultDate);
					$resultDate = $dtarr[2]."-".$dtarr[1]."-".$dtarr[0];
					
					if(isset($combination) && $combination != ''){
						$student->display_exam_month_year = $marksheet_component_obj->getDisplayExamMonthYear($combination);
					}
				} else {
					// $stuexam=ExamSubject::where('student_id',@$studentdata->id)->latest('exam_year')->first(['exam_year','exam_month']);
					
					if(@$final_result->exam_year){
						$examSubjectsMarksData = $marksheet_component_obj->getallexamsubjectsdata($enrollment,$final_result->exam_year,$final_result->exam_month);
					   
					}else{
						$examSubjectsMarksData = array();
					}
					
					$newexamresultfields = array('exam_year','exam_month','result_date');
					$final_result_data = ExamResult::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first($examresultfields);	
					
					//$newexamresultfields = array('exam_year','exam_month','result_date');
					//$final_result_data = ExamResult::where('enrollment','=',$enrollment)->orderBy('id','DESC')->first($examresultfields);			
					
					if(isset($final_result_data->result_date) && $final_result_data->result_date != ""){
						$resultDate = $final_result_data->result_date;
					}
					$student->display_exam_month_year = $marksheet_component_obj->getDisplayExamMonthYear($combination);
				}
				
				$student->ai_code = substr($pastInfo->ENROLLNO,0,5);
				$ai_code = $student->ai_code;    
				$student->enrollment = $pastInfo->ENROLLNO;
				$student->name = $pastInfo->NAME;
				$student->father_name = $pastInfo->FNAME;
				$student->mother_name = $pastInfo->MNAME;
				$pastInfo->DOB = $new_date = $pastInfo->DOB;//date('d/m/Y', strtotime($pastInfo['Pastdata']['DOB']));
				$student->dob = $pastInfo->DOB;
				@$dtarr = explode('-',$student['dob']);
				@$student->dob = @$dtarr[2]."-".@$dtarr[1]."-".@$dtarr[0];
				
				@$student->course = $pastInfo->CLASS;
				@$student->yy = $yy = substr($pastInfo->ENROLLNO,5,2);
				@$student->student_code =  $st_code = substr($pastInfo->ENROLLNO,7);
				@$courseVal = $pastInfo->CLASS; 
				@$student->display_exam_month_year=$pastInfo->EX_YR;
				@$addressTemp = $pastInfo->ADDRESS;
				if(isset($pastInfo->DISTRICT) && !empty($pastInfo->DISTRICT)){
					@$addressTemp .= ','.$pastInfo->DISTRICT;
				}
				if(isset($pastInfo->STATE) && !empty($pastInfo->STATE)){
					@$addressTemp .= ','.$pastInfo->STATE;
				}
				if(isset($pastInfo->PIN) && !empty($pastInfo->PIN)){
					@$addressTemp .= '-'.$pastInfo->PIN;
				}
				@$address->address1 = $addressTemp;
				@$address->address2 = '';
				@$address->address3 = '';
				@$address->city_name = '';
				@$address->pincode = '';
				
				if(isset($pastInfo->MOBILE) && !empty($pastInfo->MOBILE)){
					@$student->mobile = $pastInfo->MOBILE;
				}
				if($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'GENERAL_ADM' || 	$pastInfo->ERTYPE == 'STREAM2'){
					$student->adm_type = 1;
				}else if($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'READMISSION'){
					$student['adm_type'] = 2;
				}else if($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'PARTADMISSION'){
					$student->adm_type = 3;
				}else if($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'IMPROVEMENT'){
					$student->adm_type = 4;				
				}else if($pastInfo->ERTYPE != '' && $pastInfo->ERTYPE == 'SUPPLEMENTARY'){
					$student->adm_type = 1;	//11 for supplementary ertype and admission type			
				}else{ //if ertype in pastdata table is balnk or null then use gen_adm adm_type
					$student->adm_type = 1;
				}
			}
			
			$sub_enrollment = substr($enrollment,-6,2);
			if($sub_enrollment >= '17'){
				//$stuexam1=ExamSubject::where('student_id',@$studentdata->id)->orderBy('exam_year', 'DESC')->orderBy('exam_month','DESC')->first(['exam_year','exam_month']);
				$examSubjectsMarksData =$marksheet_component_obj->getallexamsubjectsdata($enrollment,$final_result->exam_year,$final_result->exam_month);
			
			}else{
				$examsubjectfields = array('id', 'subject_id','final_theory_marks','final_practical_marks','sessional_marks_reil_result','total_marks','final_result');
				$examSubjectsMarksData = ExamSubject::where('enrollment','=',$enrollment)->orderBy('subject_id','desc')->get($examsubjectfields);
			}
			$examsubjectcount=count($examSubjectsMarksData);
			$subjectCodeIds = $marksheet_component_obj->_getSubjectsCodeId($courseVal);
			if(($examsubjectcount == 0)){
				$examSubjectsMarksData=array();
				if(isset($pastInfo->FRES1) && $pastInfo->FRES1 != ''){
					$examSubjectsMarksData[0]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB1];
					$examSubjectsMarksData[0]['final_theory_marks'] = $pastInfo->FTM1;
					$examSubjectsMarksData[0]['final_practical_marks'] = $pastInfo->FPM1;
					$examSubjectsMarksData[0]['sessional_marks_reil_result'] = $pastInfo->fst1;
					$examSubjectsMarksData[0]['total_marks'] = $pastInfo->FTOT1;
					$examSubjectsMarksData[0]['final_result'] = $pastInfo->FRES1;
					$examSubjectsMarksData[0]['max_marks'] = $marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB1]);
					$examSubjectsMarksData[0]['num_words'] = $marksheet_component_obj->numberInWord($examSubjectsMarksData[0]['total_marks']);	
					$examSubjectsMarksData[0]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[0]['total_marks']);
				}

				if(isset($pastInfo->FRES2) && $pastInfo->FRES2 != '' ){
					$examSubjectsMarksData[1]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB2];
					$examSubjectsMarksData[1]['final_theory_marks'] = $pastInfo->FTM2;
					$examSubjectsMarksData[1]['final_practical_marks'] = $pastInfo->FPM2;
					$examSubjectsMarksData[1]['sessional_marks_reil_result'] = $pastInfo->fst2;
					$examSubjectsMarksData[1]['total_marks'] = $pastInfo->FTOT2;
					$examSubjectsMarksData[1]['final_result'] = $pastInfo->FRES2;			
					$examSubjectsMarksData[1]['max_marks'] =  $marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB2]);
					$examSubjectsMarksData[1]['num_words'] = $marksheet_component_obj->numberInWord($examSubjectsMarksData[1]['total_marks']);	
					$examSubjectsMarksData[1]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[1]['total_marks']);
				}

				if(isset($pastInfo->FRES3) && $pastInfo->FRES3 != ''){
					$examSubjectsMarksData[2]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB3];
					$examSubjectsMarksData[2]['final_theory_marks'] = $pastInfo->FTM3;
					$examSubjectsMarksData[2]['final_practical_marks'] = $pastInfo->FPM3;
					$examSubjectsMarksData[2]['sessional_marks_reil_result'] = $pastInfo->fst3;
					$examSubjectsMarksData[2]['total_marks'] = $pastInfo->FTOT3;
					$examSubjectsMarksData[2]['final_result'] = $pastInfo->FRES3;
					$examSubjectsMarksData[2]['max_marks'] = $marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB3]);
					$examSubjectsMarksData[2]['num_words'] = $marksheet_component_obj->numberInWord($examSubjectsMarksData[2]['total_marks']);	
					$examSubjectsMarksData[2]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[2]['total_marks']);
				}

				if(isset($pastInfo->FRES4) && $pastInfo->FRES4 != ''){
					$examSubjectsMarksData[3]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB4];
					$examSubjectsMarksData[3]['final_theory_marks'] = $pastInfo->FTM4;
					$examSubjectsMarksData[3]['final_practical_marks'] = $pastInfo->FPM4;
					$examSubjectsMarksData[3]['sessional_marks_reil_result'] = $pastInfo->fst4;
					$examSubjectsMarksData[3]['total_marks'] = $pastInfo->FTOT4;
					$examSubjectsMarksData[3]['final_result'] = $pastInfo->FRES4;				
					$examSubjectsMarksData[3]['max_marks'] = $marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB4]);
					$examSubjectsMarksData[3]['num_words'] = $marksheet_component_obj->numberInWord($examSubjectsMarksData[3]['total_marks']);	
					$examSubjectsMarksData[3]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[3]['total_marks']);
				}

				if(isset($pastInfo->FRES5) && $pastInfo->FRES5 != ''){
					$examSubjectsMarksData[4]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB5];
					$examSubjectsMarksData[4]['final_theory_marks'] = $pastInfo->FTM5;
					$examSubjectsMarksData[4]['final_practical_marks'] = $pastInfo->FPM5;
					$examSubjectsMarksData[4]['sessional_marks_reil_result'] = $pastInfo->fst5;
					$examSubjectsMarksData[4]['total_marks'] = $pastInfo->FTOT5;
					$examSubjectsMarksData[4]['final_result'] = $pastInfo->FRES5;			
					$examSubjectsMarksData[4]['max_marks'] = $marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB5]);
					$examSubjectsMarksData[4]['num_words'] = $marksheet_component_obj->numberInWord($examSubjectsMarksData[4]['total_marks']);	
					$examSubjectsMarksData[4]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[4]['total_marks']);				
				}

				if(isset($pastInfo->FRES6) && $pastInfo->FRES6 != ''){
					$examSubjectsMarksData[5]['subject_id'] = @$subjectCodeIds[@$pastInfo->EX_SUB9];
					$examSubjectsMarksData[5]['final_theory_marks'] = $pastInfo->FTM6;
					$examSubjectsMarksData[5]['final_practical_marks'] = $pastInfo->FPM6;
					$examSubjectsMarksData[5]['sessional_marks_reil_result'] = $pastInfo->fst6;
					$examSubjectsMarksData[5]['total_marks'] = $pastInfo->FTOT6;
					$examSubjectsMarksData[5]['final_result'] = $pastInfo->FRES6;			
					$examSubjectsMarksData[5]['max_marks'] = $marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB9]);
					$examSubjectsMarksData[5]['num_words'] = $marksheet_component_obj->numberInWord($examSubjectsMarksData[5]['total_marks']);	
					$examSubjectsMarksData[5]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[5]['total_marks']);
				}

				if(isset($pastInfo->FRES7) && $pastInfo->FRES7 != ''){
					$examSubjectsMarksData[6]['subject_id'] = $subjectCodeIds[$pastInfo->EX_SUB7];
					$examSubjectsMarksData[6]['final_theory_marks'] = $pastInfo->FTM7;
					$examSubjectsMarksData[6]['final_practical_marks'] = $pastInfo->FPM7;
					$examSubjectsMarksData[6]['sessional_marks_reil_result'] = $pastInfo->fst7;
					$examSubjectsMarksData[6]['total_marks'] = $pastInfo->FTOT7;
					$examSubjectsMarksData[6]['final_result'] = $pastInfo->FRES7;			
					$examSubjectsMarksData[6]['max_marks'] = $marksheet_component_obj->getSubjectMaxMarks($subjectCodeIds[$pastInfo->EX_SUB7]);
					$examSubjectsMarksData[6]['num_words'] = $marksheet_component_obj->numberInWord($examSubjectsMarksData[6]['total_marks']);	
					$examSubjectsMarksData[6]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($examSubjectsMarksData[6]['total_marks']);
				}

			}else{
				if(!empty($examSubjectsMarksData)){			
					$k=0;
					foreach($examSubjectsMarksData as $v){
						$examSubjectsMarksData[$k]['max_marks'] = $marksheet_component_obj->getSubjectMaxMarks($v->subject_id);
						$examSubjectsMarksData[$k]['num_words'] = $marksheet_component_obj->numberInWord($v->total_marks);
						$examSubjectsMarksData[$k]['grade_marks'] = $marksheet_component_obj->getGradeOfMarks($v->total_marks);
						$totalMarks = $totalMarks + $examSubjectsMarksData[$k]['max_marks'] ;
						$grandFinalTotalMarks = $grandFinalTotalMarks + $v->total_marks;
						$k++;
					}
				} 
			}
			
			$dobInWords = null;
			if(@($student->dob)){
				$dobInWords = $marksheet_component_obj->getDObInWords($student->dob);
			}
				
			$subjects = $marksheet_component_obj->_getSubjectsForMarksheet($courseVal);
			// Get Barcode code 
			$imagepath = asset('public/barcode/enrollment/'.$enrollment.'.png');
			$custom_component_obj = new CustomComponent;
			$barcode = $custom_component_obj->barcode($enrollment);
			$barcode_img = '<img src="'.$imagepath.'" alt=barcode-'.$enrollment.' style="font-size:0;position:relative;width:132px;height:20px;" >'; 
			// Get Barcode code 
			$marksheet_type = 'Issued On '; 
			 
			$finalArr['student'] = $student;
			$finalArr['examSubjectsMarksData'] = $examSubjectsMarksData;
			$finalArr['subjects'] = $subjects;
			$finalArr['final_result'] = $final_result;
			return $finalArr;
		}

	/* Marksheet test case end */
	
	 public function qrbcode($value){
		$qrcdeo = new QRcode();
		$directory_path = public_path("qrcode/enrollment");
		$directory = File::makeDirectory($directory_path,$mode = 0777, true, true);
		$value = $value;
		$ecc  = 'L';
		$file = public_path("qrcode/enrollment/rahul.png");
		$pixel_size =4;
		$frame_size = 2;
		$qrcdeo->png($value,$file,$ecc,$pixel_size,$frame_size);
	}
	

	
}


