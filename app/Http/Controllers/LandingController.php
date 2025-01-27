<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\MarksheetCustomComponent;
use App\Component\RevalMarksComponent;
use App\Component\ThoeryCustomComponent;
use App\Exports\MultipleSheetsExlExport;
use App\Exports\RecursiveIteratorIterator;
use App\Exports\RejectedStudentExlExport;
use App\Exports\SummaryBookRequriedInformationExlExport;
use App\Exports\SummaryBookStockInformationExlExport;
use App\Helper\CustomHelper;
use App\Models\Application;
use App\Models\BankMaster;
use App\Models\DbtStudent;
use App\Models\Document;
use App\Models\ExamSubject;
use App\Models\ProvisionalExamResult;
use App\Models\RevalStudent;
use App\Models\RevalStudentSubject;
use App\Models\Student;
use App\Models\StudentAllotment;
use App\Models\StudentAllotmentMark;
use App\Models\StudentDocumentVerification;
use Config;
use App\Models\StudentFee;
use App\Models\StudentPracticalSlots;
use App\Models\Supplementary;
use App\Models\TocMark;
use App\Models\VerificationMaster;
use Auth;
use Cache;
use DB;
use File;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Response;
use Session;
use Validator;


class LandingController extends Controller
{


    public $marksheet_component_obj = "";

    function __construct()
    {

        $this->marksheet_component_obj = new MarksheetCustomComponent;
    }

    public function store(Request $request)
    {
        //
        ini_set('max_execution_time', 180);
        $limit = $request->get('qrcode_quantity');
        for ($i = 1; $i <= $limit; $i++) {
            # code...
            $qrcode = new Qrcode();
            $qrcode->user_id = Auth::user()->id;
            //$qrcode->user_id = 1;
            $corres_connection = str_random(7);
            $qrcode->correspond_code = $corres_connection;
            $qrcode->qrcode_note = $request->get('qrcode_note');
            $qrcode->save();
        }
        $request->session()->flash('alert-success', 'QR code Successfully Created!');
        return Redirect::to('admin/qrcode/create');
    }







    /*public function rahul(){
		ini_set('memory_limit', '3000M');
	    ini_set('max_execution_time', '0');
	    $users = DB::table('student_allotments')->where('exam_year',124)->whereIn('ai_code',[17114,16017])->where('exam_month',1)->get();
	 	$i = 1;
		foreach($users as $userss){
			$createtesttable =DB::table('student_allotment_marks')->where('exam_year',124)->where('exam_month',1)->where('student_id',$userss->student_id)->update(['fixcode' =>$userss->fixcode]);
			$i++;
			//return view('landing.pages',compact('text','file','ecc','pixel_Size'));
		}
		echo  "Today is Done " . date("Y/m/d") . "<br>";
	}*/

    //$master = Student::
    //with('application','document','address','toc','studentfees','admission_subject','toc_subject','exam_subject')

    public function publish_publication_department_report()
    {
        return view('landing.publish_publication_department_report');
    }

    public function rahul(Request $request)
    {

        $datas = DB::table('test_temp')->get(['querystr'])->limit(10);
        dd($datas);
        $i = 1;
        foreach ($datas as $data) {
            //$data
            $i++;
            //return view('landing.pages',compact('text','file','ecc','pixel_Size'));
        }
        echo $i . "<br>";
        die;
        $string = '12345678911';

        $payload = base64_encode($string);

        dd($payload);

        $decrypted_id = base64_decode($payload);


        dd($decrypted_id);

        $conditions = array();
        $conditions['student_allotment_marks.exam_year'] = 125;
        $conditions['student_allotment_marks.exam_month'] = 1;
        $conditions['student_allotments.exam_year'] = 125;
        $conditions['student_allotments.exam_month'] = 1;
        $conditions['user_examiner_maps.exam_year'] = 125;
        $conditions['user_examiner_maps.exam_month'] = 1;
        $conditions['subjects.practical_type'] = 1;
        $total_lock_submit_all_student = DB::table('student_allotment_marks')->select('student_allotment_marks.id')->join('Subjects', 'Subjects.id', '=', 'student_allotment_marks.subject_id')->join('student_allotments', 'student_allotments.student_id', '=', 'student_allotment_marks.student_id')->leftjoin('user_examiner_maps', 'user_examiner_maps.id', '=', 'student_allotment_marks.user_examiner_map_id')->where($conditions)->whereNull('subjects.deleted_at')->whereNull('student_allotments.deleted_at')->whereNull('user_examiner_maps.deleted_at')->whereNull('student_allotment_marks.deleted_at')->groupBy('student_allotment_marks.subject_id')->get();


        dd($total_lock_submit_all_student);
        //$total_lock_submit_all_student = StudentAllotmentMark::join('applications', 'applications.student_id', '=', 'students.id')


        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        unset($admission_sessions['121'], $admission_sessions['124'], $admission_sessions['123'], $admission_sessions['122'], $admission_sessions['120'], $admission_sessions['126'], $admission_sessions['13']);

        dd($admission_sessions);


        $raul = '10068233002';

        $const = array('10068233002', '15001233130');
        if (in_array($raul, $const)) {
            echo "Match found";
        }


        //$data = Student::select('id', 'enrollment', 'ai_code')->with(['Application' => function ($query) {
        //$query->select('student_id', 'enrollment');
        // }])->limit(1)->get();

        //$data = Student::joinWith('Application')->limit(1)->get();

        dd($data);


        //$status = $this->checkIsStudentVerificationAllowAtAICenter("2023-04-10 14:00:00");
        //dd($status);

        //$enrollment = null;
        //$student_id='821872';
        //if(@$student_id){
        //$student = Student::where('id',$student_id)->first();
        //if(@$student->enrollment){
        //return $student->enrollment;
        //}

        //$student_code = $this->_getStCode($student->stream,$student->course,$student->ai_code);
        //$enrollment = $this->_generateEnrollment($student->stream,$student->course,$student->//ai_code);
        //$studentenrollmentnum = ['is_eligible' => 1,'enrollment' => $enrollment,'student_code' => $student_code];
        //$studentenrollmentnum = Student::where('id',$student_id)->update($studentenrollmentnum);

        //$applicationarray = ['enrollment' => $enrollment];
        //$applicationarray = Application::where('student_id',$student_id)->update($applicationarray);
        //$smsStatus = $this->_sendLockSubmittedMessage($student_id);
        //}
        dd(random_int(100, 999));


        //$rahuls = DB::getSchemaBuilder()->hasTable('rsos_years');
        //$rahuls = DB::getSchemaBuilder()->getAllTables();
        //$rahuls = DB::getSchemaBuilder()->getColumnlisting('rsos_years');

        //$rahuls = request()->fullurl();
        //$rahuls = request()->fullurlWithQuery(['salable' => 'yes']);
        //$rahuls = request()->fullurlWithQuery(['page']);
        //$rahuls = request()->host();
        //$rahuls = request()->ip();
        //$rahuls = request()->ips();
        //$rahuls = request()->method();
        //$rahuls = request()->url();
        //$rahuls = request()->path();
        //$rahuls = request()->schemeAndhttpHost();
        //$rahuls = request()->segments();
        //$rahuls = request()->segments();

        dd($rahuls);

        return view('practical.test');

        $date_time_start = "2024-04-22 09:40:00";
        $date_time_end = "2024-04-22 12:40:00";
        $start = strtotime($date_time_start);
        $end = strtotime($date_time_end);
        $hours = ($end - $start) / (60 * 60);
        echo $hours;
        die;


        $current_exam_year = 125;
        $current_exam_month = 1;
        $user_examiner_map_id = '5044';


        $current_exam_year = CustomHelper::_get_selected_sessions();
        $current_exam_month = Config::get("global.current_exam_month_id");

        $StudentPracticalSlotsgetcount = StudentPracticalSlots::where('date_time_start2', '<', $date_time_start)->where('date_time_end', '>', $date_time_start)->orWhere('date_time_end', '<', $date_time_end)->orWhere('date_time_start', '>', $date_time_start)->where('exam_year', 125)->where('exam_month', $current_exam_month)->where('examiner_mapping_id', 5044)->get();

        dd($StudentPracticalSlotsgetcount);


        $data = Student::where('exam_year', 125)->cursor('10');

        //$data = Student::select('id', 'enrollment', 'ai_code')->with(['exam_subject' => function ($query) {
        //$query->select('student_id', 'enrollment');
        //}])->limit(1)->get();
        $data = Student::where('ssoid', '87lok')->where(function ($query) {
            return $query
                ->where('is_eligible', 1)
                ->orWhere('is_eligible', null);
        })
            ->get();

        dd($data);

        $exconditions['student_id'] = 597528;
        $exconditions['subject_id'] = 16;
        $examSubjectDetails = ExamSubject::whereNull('deleted_at')->where($exconditions)->orderBy("exam_year", "DESC")->get(['enrollment', 'student_id', 'subject_id', 'final_practical_marks', 'final_theory_marks', 'sessional_marks', 'sessional_marks_reil_result', 'subject_type', 'total_marks', 'final_result', 'exam_year', 'exam_month']);

        // $examSubjectDetails = ExamSubject::whereNull('deleted_at')->where($exconditions)->orderBy("exam_year","DESC")->orderBy("exam_month","asc")->get(['enrollment','student_id','subject_id','final_practical_marks','final_theory_marks','sessional_marks','sessional_marks_reil_result','subject_type','total_marks','final_result','exam_year','exam_month']);


        dd($examSubjectDetails->toArray());


        $exconditions['student_id'] = 597528;
        $exconditions['subject_id'] = 16;
        $examSubjectDetails = DB::table('exam_subjects')->whereNull('deleted_at')->where($exconditions)->groupBy("subject_id")->orderBy('exam_year', 'DESC')->orderBy('exam_month', 'DESC')->get(['enrollment', 'student_id', 'subject_id', 'final_practical_marks', 'final_theory_marks', 'sessional_marks', 'sessional_marks_reil_result', 'subject_type', 'total_marks', 'final_result', 'exam_year', 'exam_month']);


        $employee = DB::table('exam_subjects')->where('student_id', 597528)->where('subject_id', 16)->groupBy('subject_id')->orderBy('final_result', 'desc')->get();

        dd($employee);

        // $examSubjectDetails = ExamSubject::whereNull('deleted_at')->where($exconditions)->orderBy("exam_year","DESC")->orderBy("exam_month","asc")->get(['enrollment','student_id','subject_id','final_practical_marks','final_theory_marks','sessional_marks','sessional_marks_reil_result','subject_type','total_marks','final_result','exam_year','exam_month']);


        dd($examSubjectDetails->toArray());


        //$users = DB::table('students')
        //->whereExists(function ($query) {
        //$query->select(DB::raw(1))
        //->from('applications')
        //->whereRaw('rs_applications.student_id = rs_students.id');
        //})
        //->limit(1)->get();

        $users = Student::addSelect(['name' => Application::select('student_id')
            ->whereColumn('student_id', 'students.id')
            ->orderBy('created_at', 'desc')
            ->limit(1)
        ])->get();


        //$data = Student::select('id', 'enrollment', 'ai_code')->with(['Application' => function ($query) {
        //$query->select('student_id', 'enrollment');
        //}])->limit(1)->get();

        //$users = Student::query()
        // ->select('students.id')
        //->addSelect(DB::raw('(SELECT created_at FROM rs_applications WHERE rs_applications.student_id = rs_students.id ORDER BY created_at DESC LIMIT 1) as lastPost'))
        //->limit(1)->get();


        //$users = Student::query()->addSelect(['name' => Application::query()
        //->select('created_at')
        //->whereColumn('student_id', 'students.id')])->limit(1)->get();


        dd($users);


        //echo $_SERVER['HTTP_USER_AGENT'];
        //$uas = $request->header('HTTP_USER_AGENT');

//$ua = $request->header('User-Agent');
//dd($uas);
        // $bank_id = 40;
        //$showStatus = $this->_getListBanksName();
        // $showStatus = $this->_getListBanksIfscCode($bank_id);

        //dd($showStatus);

        //dd($request->ip());


    }

    /*public function rahul($type=null){
			if($type ==1){
			$getdata = DB::table('students')
			 ->select('students.enrollment','students.id as student','applications.student_id as application','bank_details.student_id as bank_details','documents.student_id as documents','admission_subjects.student_id as admission_subjects','toc.student_id as toc','toc_marks.student_id as toc_marks','exam_subjects.student_id as exam_subjects','student_fees.student_id as student_fees','applications.toc as tocyesandnot')
			 ->where('students.exam_year',124)->where('students.exam_month',1)->whereNotNull('students.challan_tid' )->where('students.is_eligible',1)->where('applications.locksumbitted',1)
			->leftjoin('applications', 'applications.student_id', '=', 'students.id')->leftjoin('bank_details', 'bank_details.student_id', '=', 'students.id')->leftjoin('documents', 'documents.student_id', '=', 'students.id')->leftjoin('admission_subjects', 'admission_subjects.student_id', '=', 'students.id')->leftjoin('toc', 'toc.student_id', '=', 'students.id')->leftjoin('toc_marks', 'toc_marks.student_id', '=', 'students.id')->leftjoin('exam_subjects', 'exam_subjects.student_id', '=', 'students.id')->leftjoin('student_fees', 'student_fees.student_id', '=', 'students.id')->
			whereNull('students.deleted_at')->whereNull('applications.deleted_at')->whereNull('bank_details.deleted_at')->whereNull('documents.deleted_at')->whereNull('admission_subjects.deleted_at')->whereNull('toc.deleted_at')->whereNull('toc_marks.deleted_at')->whereNull('exam_subjects.deleted_at')->whereNull('student_fees.deleted_at')->groupBy('admission_subjects.student_id')->groupBy('exam_subjects.student_id')->groupBy('toc_marks.student_id')->paginate(20);
			return view('landing.showenrollment',compact('getdata','type'));

		  }else if($type ==2){
			$getdata = DB::table('students')
			 ->select('students.enrollment','students.id as student','applications.student_id as application','bank_details.student_id as bank_details','documents.student_id as documents','admission_subjects.student_id as admission_subjects','toc.student_id as toc','toc_marks.student_id as toc_marks','exam_subjects.student_id as exam_subjects','student_fees.student_id as student_fees','applications.toc as tocyesandnot')
			 ->where('students.exam_year',124)->where('students.exam_month',1)->whereNotNull('students.challan_tid' )->where('students.is_eligible',0)->where('applications.locksumbitted',1)
			->leftjoin('applications', 'applications.student_id', '=', 'students.id')->leftjoin('bank_details', 'bank_details.student_id', '=', 'students.id')->leftjoin('documents', 'documents.student_id', '=', 'students.id')->leftjoin('admission_subjects', 'admission_subjects.student_id', '=', 'students.id')->leftjoin('toc', 'toc.student_id', '=', 'students.id')->leftjoin('toc_marks', 'toc_marks.student_id', '=', 'students.id')->leftjoin('exam_subjects', 'exam_subjects.student_id', '=', 'students.id')->leftjoin('student_fees', 'student_fees.student_id', '=', 'students.id')->
			whereNull('students.deleted_at')->whereNull('applications.deleted_at')->whereNull('bank_details.deleted_at')->whereNull('documents.deleted_at')->whereNull('admission_subjects.deleted_at')->whereNull('toc.deleted_at')->whereNull('toc_marks.deleted_at')->whereNull('exam_subjects.deleted_at')->whereNull('student_fees.deleted_at')->groupBy('admission_subjects.student_id')->groupBy('exam_subjects.student_id')->groupBy('toc_marks.student_id')->paginate(20);
			return view('landing.showenrollment',compact('getdata','type'));
		   }else if($type ==3){
			$getdata = DB::table('students')
			 ->select('students.enrollment','students.id as student')
			->join('applications', 'applications.student_id', '=', 'students.id')->join('student_fees', 'student_fees.student_id', '=', 'students.id')->where('students.exam_year',124)->where('students.exam_month',1)->where('students.is_eligible',0)->where('applications.locksumbitted',1)->where('students.are_you_from_rajasthan',2)->where('student_fees.total',0)->where('students.gender_id',2)->
			whereNull('students.deleted_at')->whereNull('applications.deleted_at')->whereNull('student_fees.deleted_at')->paginate(20);
			return view('landing.showenrollment',compact('getdata','type'));
		   }




		  /*$getdata =  DB::table('students')->join('applications', 'applications.student_id', '=', 'students.id')->where('students.exam_year',124)->where('students.exam_month',1)->whereNotNull('students.challan_tid')->where('students.is_eligible',1)->where('applications.locksumbitted',1)->whereNull('students.deleted_at')->pluck('students.id','students.id');

		   $i = 0;
		   $final_data = array();
		   foreach($getdata as $getdatas){
		   $studentget = student::join('applications', 'applications.student_id', '=', 'students.id')->Where('students.id', '=', $getdatas)->whereNull('students.deleted_at')->whereNull('applications.deleted_at')->first(['students.enrollment','applications.toc']);
		   $student = student::Where('id', '=', $getdatas)->whereNull('deleted_at')->count();
		   $application = Application::Where('student_id', '=', $getdatas)->whereNull('deleted_at')->count();
		   $bank = BankDetail::Where('student_id', '=', $getdatas)->whereNull('deleted_at')->count();
		   $toc = Toc::Where('student_id', '=', $getdatas)->whereNull('deleted_at')->count();
		   $tocmark = TocMark::Where('student_id', '=', $getdatas)->whereNull('deleted_at')->count();
		   $examsubject = ExamSubject::Where('student_id', '=', $getdatas)->whereNull('deleted_at')->count();
		   $document = Document::Where('student_id', '=', $getdatas)->whereNull('deleted_at')->count();
		   $StudentFee = StudentFee::Where('student_id', '=', $getdatas)->whereNull('deleted_at')->count();
		   $AdmissionSubject = AdmissionSubject::Where('student_id', '=', $getdatas)->whereNull('deleted_at')->count();

			    if(!empty(($studentget->toc == 1) &&($tocmark == 0 || $toc == 0 || $student == 0 || $application == 0 || $bank == 0 || $examsubject ==0 || $document == 0|| $StudentFee == 0 || $AdmissionSubject == 0))){
					$final_data[$i]['studentget']=$studentget->enrollment;
                    $final_data[$i]['student']=$student;
					$final_data[$i]['application']=$application;
					$final_data[$i]['bank']=$bank;
					$final_data[$i]['toc']=$toc;
					$final_data[$i]['tocmark']=$tocmark;
					$final_data[$i]['examsubject']=$examsubject;
					$final_data[$i]['document']=$document;
					$final_data[$i]['StudentFee']=$StudentFee;
					$final_data[$i]['AdmissionSubject']=$AdmissionSubject;
					$i++;
				}else if($student == 0 || $application == 0 || $bank == 0 || $examsubject ==0 || $document == 0|| $StudentFee == 0 || $AdmissionSubject == 0){
				    $final_data[$i]['studentget']=$studentget->enrollment;
                    $final_data[$i]['student']=$student;
					$final_data[$i]['application']=$application;
					$final_data[$i]['bank']=$bank;
					$final_data[$i]['examsubject']=$examsubject;
					$final_data[$i]['document']=$document;
					$final_data[$i]['StudentFee']=$StudentFee;
					$final_data[$i]['AdmissionSubject']=$AdmissionSubject;
					$i++;
				}


		  }*/


    public function photo_sign_convert_to_base()
    {
        $exam_year = CustomHelper::_get_selected_sessions();

        $exam_month = Config::get('global.current_exam_month_id');
        $studentIds = DB::table("students")->where("exam_year", $exam_year)->where("exam_month", $exam_month)->where("is_eligible", 1)->pluck('id', 'id');

        $students = Document::whereIn('student_id', $studentIds)->get(["id", "student_id", "photograph", "signature"]);

        foreach ($students as $k => $student) {
            $finalArr = array();
            $fld = "photograph";
            $path = asset('public/documents/' . $student->student_id . '/' . $student->$fld);
            $realpath_image = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $student->student_id . DIRECTORY_SEPARATOR . $student->$fld;

            if (!empty(@$student->$fld) && file_exists($realpath_image)) {

                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = base64_encode($data);
                $newField = "base_" . $fld;
                $finalArr[$newField] = $base64;
            }
            $fld = "signature";
            $path = asset('public/documents/' . $student->student_id . '/' . $student->$fld);
            $realpath_image = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $student->student_id . DIRECTORY_SEPARATOR . $student->$fld;
            if (!empty(@$student->$fld) && file_exists($realpath_image)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = base64_encode($data);
                $newField = "base_" . $fld;
                $finalArr[$newField] = $base64;
            }
            if (@$finalArr) {

                Document::where('id', $student->id)->update($finalArr);
            }
        }

        echo "Done";
        die;
    }


    public function updateiseligible($type = null)
    {
        if ($type == 1) {

            $getdata = DB::table('students')
                ->join('applications', 'applications.student_id', '=', 'students.id')->join('student_fees', 'student_fees.student_id', '=', 'students.id')->where('students.exam_year', 124)->where('students.exam_month', 1)->where('students.is_eligible', 0)->where('applications.locksumbitted', 1)->where('students.are_you_from_rajasthan', 2)->where('student_fees.total', 0)->where('students.gender_id', 2)->
                whereNull('students.deleted_at')->whereNull('applications.deleted_at')->whereNull('student_fees.deleted_at')->pluck('students.id as student');
        } elseif ($type == 2) {

            $getdata = DB::table('students')
                ->where('students.exam_year', 124)->where('students.exam_month', 1)->whereNotNull('students.challan_tid')->where('students.is_eligible', 0)
                ->join('applications', 'applications.student_id', '=', 'students.id')->
                whereNull('students.deleted_at')->whereNull('applications.deleted_at')->where('applications.locksumbitted', 1)->pluck('students.id as student');
        }


        foreach ($getdata as $key => $val) {

            DB::table('students')->where('exam_year', 124)->where('exam_month', 1)
                ->where('id', $val)
                ->update(['is_eligible' => 1]);

        }
        echo "Today is Done " . date("Y/m/d") . "<br>";
    }


    //$aicodearr = $this->StudentAllotment->find('all',array('fields'=>array('DISTINCT `StudentAllotment`.`ai_code`','COUNT(enrollment)'),'conditions'=>$aicode_conditions,'group'=>'ai_code','order'=>'COUNT(enrollment) DESC'));


    public function index()
    {

        $custom_component_obj = new CustomComponent;
        $AllowAdmitCardAll = $custom_component_obj->getIsAllowToShowAdmitCardDownloadForAll();
        $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult();
        $showStatus = $this->_getCheckAllowToCheckResult();
        $allowYearCombo = $custom_component_obj->_getAllowYearCombo();
        $allowHllTicketIps = $custom_component_obj->hallticketAllowIps();

        $showSuppStatus = $this->_getCheckAllowToCheckSupp();
        $showrevisedStatus = $this->_getCheckAllowToCheckRevisedResult();
        $combo_name = 'result_session';
        $result_session = $this->master_details($combo_name);
        $current_exam_month_id = Config::get('global.current_result_session_month_id');
        $result_session = $result_session[$current_exam_month_id];
        return view('landing.welcome', compact('resultCheckStatus', 'showSuppStatus', 'allowYearCombo', 'result_session', 'showStatus', 'showrevisedStatus', 'AllowAdmitCardAll', 'allowHllTicketIps'));
        //return view('landing.welcome');
    }

    public function super_dashboard()
    {
        return view('landing.super_dashboard');
    }

    public function gen_fixcode_exam_center(Request $request)
    {
        $isNextRoundRequire = $this->genFixcodeCenter(true);
        if ($isNextRoundRequire == false) {
            echo "<h1>Done</h1>";
        } else {
            echo "Something is wrong.";
        }
        die;
    }


    public function downloadmobileapp(Request $request)
    {
        $root = Config::get("global.APP_URL");
        $url = public_path() . "/app-assets/images/RSOS_APK.apk";
        return Response::download($url);
    }

    public function downloadRejectedStudentExl(Request $request, $exam_year = null, $exam_month = null, $type = "xlsx")
    {

        $application_exl_data = new RejectedStudentExlExport($exam_year, $exam_month);

        $filename = "Rejected Student With Reasons " . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }


    public function brs_clearing_developer(Request $request)
    {
        //$students = array(841152,842016,842140);
        //$part = "rsos/reval/reval_subjects_details/";

        //Fresh
        $students = array(891796, 896963, 930572, 961189, 961231, 961514, 961669, 964359, 965478, 970551, 972730, 974582, 974998, 976328, 988215);
        $part = "rsos/payments/verify_request/";

        //Supp
        //$students = array(719237,518558,570952,623343,778754,424385,467846,442598,761646,697216,584311,850332,830761,695859,488533,752476,793234,683601,849607,667529,563812,735470,735417,499909,717798,706685,807127,719462,497044,406357,651977,519693,829672,782187,519160,852547,611022,792604,854943,498367,680542,820428,);
        //$part = "rsos/supp_payments/supp_registration_fee/";


        $domain = "https://rsosadmission.rajasthan.gov.in/rsos/";
        if (@$_SERVER['APP_URL']) {
            if (isset($_SERVER['APP_URL']) && !empty($_SERVER['APP_URL'])) {
                $domain = $_SERVER['APP_URL'];
            }
        }

        $links = [];
        foreach ($students as $k => $student_id) {
            $details = Student::where('id', $student_id)->first(['name', 'enrollment']);
            $links[$details->enrollment . '_' . @$details->name . '_' . $student_id] = $domain . "/" . $part . "" . Crypt::encrypt($student_id);
            /* $details = Student::where('id',$student_id)->first(['id','ssoid','name','enrollment']);

			$links[$details->ssoid . ' _' . $details->enrollment . '_' . @$details->name . '_' . $details->id ] = $domain ."/" . $part . "". Crypt::encrypt($details->enrollment);  */
        }

        $html = "<table border='1'>";
        $html .= "<tr>";
        $html .= "<th>";
        $html .= "#";
        $html .= "</th>";
        $html .= "<th>";
        $html .= "Enroll";
        $html .= "</th>";
        $html .= "<th>";
        $html .= "Link";
        $html .= "</th>";
        $html .= "</tr>";
        $counter = 0;
        foreach ($links as $k => $link) {
            $counter++;
            $html .= "<tr>";
            $html .= "<td>";
            $html .= $counter;
            $html .= "</td>";

            $html .= "<td>";
            $html .= $k;
            $html .= "</td>";
            $html .= "<td>";
            $html .= "<a href='" . $link . "'>Verify Payment</a>";
            $html .= "</td>";
            $html .= "</tr>";
        }
        $html .= "</table>";


        echo "<pre>";
        print_r($html);
        die;
    }

    public function rejected_student_with_reasons(Request $request, $examyear = null, $exammonth = null, $format = 'html')
    {
        if (@$examyear && @$exammonth) {

        } else {
            return false;
        }
        //$q1 = 'SELECT MAX(sv.id),s.ai_code,s.id,s.name,s.is_eligible,s.enrollment,s.ao_status,s.department_status,sv.* FROM rs_students s INNER JOIN rs_student_verifications sv ON sv.student_id = s.id WHERE s.exam_year = ' . $examyear  . ' AND s.exam_month = ' . $exammonth  . ' AND concat(s.ao_status,s.department_status) not LIKE "%2%" AND sv.deleted_at IS NULL AND s.deleted_at IS null GROUP BY s.id  ORDER BY sv.id desc LIMIT 1000;';
        //$qtemp = 'SELECT s.id FROM rs_students s WHERE s.exam_year = ' . $examyear  . ' AND s.exam_month = ' . $exammonth  . ' AND s.ao_status = 3 AND (s.department_status != 2 OR s.department_status IS NULL) union SELECT s.id FROM rs_students s WHERE s.exam_year = ' . $examyear  . ' AND s.exam_month = ' . $exammonth  . ' AND (s.department_status = 3);';

        $qtemp = 'SELECT s.id FROM rs_students s INNER JOIN rs_applications a ON a.student_id = s.id  AND a.is_ready_for_verifying = 1 WHERE s.exam_year = ' . $examyear . ' AND s.exam_month = ' . $exammonth . ' AND s.ao_status = 3 AND (s.department_status != 2 OR s.department_status IS NULL) union SELECT s.id FROM rs_students s INNER JOIN rs_applications a ON a.student_id = s.id  AND a.is_ready_for_verifying = 1 WHERE s.exam_year = ' . $examyear . ' AND s.exam_month = ' . $exammonth . ' AND (s.department_status = 3);';

        $rTemp = DB::select($qtemp);
        $studentsList = array();
        foreach ($rTemp as $k1 => $v1) {
            $studentsList[$v1->id] = $v1->id;
        }


        $studentsList = (implode(",", $studentsList));
        //echo $studentsList;die;
        //print_r(json_encode($studentsList));die;
        $q1 = 'SELECT MAX(sv.id),s.ssoid,sv.id,s.ai_code,s.mobile, s.name, s.is_eligible,s.enrollment,s.ao_status,s.department_status,sv.* FROM rs_students s INNER JOIN (SELECT student_id, MAX(id) AS max_id FROM rs_student_verifications WHERE deleted_at IS NULL GROUP BY student_id) latest_sv ON latest_sv.student_id = s.id INNER JOIN rs_student_verifications sv ON sv.id = latest_sv.max_id WHERE s.exam_year = ' . $examyear . ' AND s.exam_month = ' . $exammonth . '  AND sv.deleted_at IS NULL AND s.id in ( ' . $studentsList . ') AND s.deleted_at IS NULL GROUP BY s.id ORDER BY sv.id desc LIMIT 1000;';
        //echo $q1;die;

        $r1 = DB::select($q1);
        $list = [];
        $verificationLabels = $this->getVerificationDetailedLabels();
        $studentDetails = array();
        $conditions = array();
        $verficationmasterdata = VerificationMaster::where($conditions)->orderBy("id", "DESC")->get();
        $verficationmasterdata = $verficationmasterdata->toArray();

        $verficationmasterdataFinal = array();
        foreach ($verficationmasterdata as $key => $value) {
            $verficationmasterdataFinal[$value['main_document_id']][$value['field_id']] = $value['field_name'];
        }

        foreach ($r1 as $k1 => $v1) {

            if (@$v1->department_documents_verification || @$v1->ao_documents_verification) {

                $data = json_decode($v1->department_documents_verification, true);
                $rejectedBy = "Department";
                if (@$data && !empty($data)) {

                } else {
                    // dd($v1);
                    $data = json_decode($v1->ao_documents_verification, true);
                    $rejectedBy = "AO";
                }

                foreach ($data as $ik => $iv) {
                    foreach ($iv as $tk => $tv) {
                        if ($tv != 1) {
                            // $list[$ik][$tk] = $tv;
                            $studentDetails[$v1->id]['rejectedBy'] = $rejectedBy;
                            $studentDetails[$v1->id]['mobile'] = $v1->mobile;
                            $studentDetails[$v1->id]['ssoid'] = $v1->ssoid;
                            $studentDetails[$v1->id]['name'] = $v1->name;
                            $studentDetails[$v1->id]['list'][$ik][$tk] = $tv;
                            // $studentDetails[$ik][$tk]['name'] = $v1->name;
                            // $list[$ik][$tk]['name'] = $v1->name;
                        }
                    }
                }
            }
        }

        $html = "<table border='1'>";
        $html .= "<tr>";
        $html .= "<th>Student Sr. No.</th>";
        $html .= "<th>Name</th>";
        $html .= "<th>SSO</th>";
        $html .= "<th>Mobile</th>";
        $html .= "<th>Rejected By</th>";
        $html .= "<th>Rejected Documents</th>";

        $html .= "</tr>";
        $maincounter = 1;

        foreach ($studentDetails as $stk => $stv) {
            $list = $studentDetails[$stk]['list'];
            $counter = 1;
            $html .= "<tr>";
            $html .= "<td>" . $maincounter++ . "</td>";
            $html .= "<td>" . @$studentDetails[$stk]['name'] . "</td>";
            $html .= "<td>" . @$studentDetails[$stk]['ssoid'] . "</td>";
            $html .= "<td>" . @$studentDetails[$stk]['mobile'] . "</td>";
            $html .= "<td>" . @$studentDetails[$stk]['rejectedBy'] . "</td>";
            $html .= "<td>";
            $html .= "<table width='100%' border='1'>";
            // $html .= "<tr>";
            // $html .= "<th>Sr. No.</th>";
            // $html .= "<th>Document Name</th>";
            // $html .= "<th>Rejected Reason</th>";
            // $html .= "</tr>";
            $maxCount = count($list);
            $counterTemp = 0;
            foreach ($list as $k => $v) {
                $counterTemp++;
                $html .= "<tr >";
                // $html .= "<td>" . $counter++ . "</td>";
                // $html .= "<td>" . @$verificationLabels[$k]['hindi_name'] . "</td>";
                if ($counterTemp == $maxCount) {
                    $html .= @$verificationLabels[$k]['hindi_name'];
                } else {
                    $html .= @$verificationLabels[$k]['hindi_name'] . ", ";
                }
                // $html .= "<td>";
                // $html .= "<table width='100%' broder='0'>";
                $icoutner = 0;
                foreach ($v as $ik => $iv) {
                    $icoutner++;
                    if ($iv == 2) {
                        // $html .= "<tr>";$html .= "<td>" . $icoutner . ". ";
                        // $html .= @$verficationmasterdataFinal[$k][$ik] . " ";
                        // $html .= "</td>";
                        // $html .= "</tr>";
                    }
                }
                // $html .= "</table>";
                // $html .= "</td>";
                $html .= "</tr>";
            }

            $html .= "</table>";
            $html .= "</td>";
            $html .= "</tr>";
        }
        $html .= "</table> <style> 
		  td { 
			word-wrap: break-word; /* Allows text to wrap within the cell */ 
			vertical-align: top; /* Aligns text to the top of the cell */ 
		  } 
		</style>";


        $fileName = "rejected_student_with_reasons_" . $examyear . "_" . $exammonth . "_" . now();
        if ($format == 'pdf') {

            $pdf = PDF::loadHTML($html);
            return $pdf->download($fileName . '.pdf');
        } else if ($format == 'html') {
            echo $html;
            die;
        } else if ($format == 'excel') {
            return Excel::download(new RejectedStudentExlExport(compact("verificationLabels", "verficationmasterdataFinal", "studentDetails")), $fileName . '.xlsx');
        }

        return view('landing.rejected_student_with_reasons', compact("verificationLabels", "verficationmasterdataFinal", "studentDetails"));
    }

    public function detailed_rejected_student_with_reasons(Request $request, $examyear = null, $exammonth = null, $format = 'html')
    {
        if (@$examyear && @$exammonth) {

        } else {
            return false;
        }
        //$q1 = 'SELECT MAX(sv.id),s.ai_code,s.id,s.name,s.is_eligible,s.enrollment,s.ao_status,s.department_status,sv.* FROM rs_students s INNER JOIN rs_student_verifications sv ON sv.student_id = s.id WHERE s.exam_year = ' . $examyear  . ' AND s.exam_month = ' . $exammonth  . ' AND concat(s.ao_status,s.department_status) not LIKE "%2%" AND sv.deleted_at IS NULL AND s.deleted_at IS null GROUP BY s.id  ORDER BY sv.id desc LIMIT 1000;';
        $q1 = 'SELECT MAX(sv.id),sv.id,s.ai_code,s.id,s.name, s.is_eligible,s.enrollment,s.ao_status,s.department_status,sv.* FROM rs_students s INNER JOIN (SELECT student_id, MAX(id) AS max_id FROM rs_student_verifications WHERE deleted_at IS NULL GROUP BY student_id) latest_sv ON latest_sv.student_id = s.id INNER JOIN rs_student_verifications sv ON sv.id = latest_sv.max_id WHERE s.exam_year = ' . $examyear . ' AND s.exam_month = ' . $exammonth . ' AND concat(s.ao_status,s.department_status) not LIKE "%2%" AND sv.deleted_at IS NULL AND s.deleted_at IS NULL GROUP BY s.id ORDER BY sv.id desc LIMIT 1000;';

        //echo $q1;die;
        $r1 = DB::select($q1);
        $list = [];
        $verificationLabels = $this->getVerificationDetailedLabels();
        $studentDetails = array();
        $conditions = array();
        $verficationmasterdata = VerificationMaster::where($conditions)->orderBy("id", "DESC")->get();
        $verficationmasterdata = $verficationmasterdata->toArray();

        $verficationmasterdataFinal = array();
        foreach ($verficationmasterdata as $key => $value) {
            $verficationmasterdataFinal[$value['main_document_id']][$value['field_id']] = $value['field_name'];
        }

        foreach ($r1 as $k1 => $v1) {

            if (@$v1->department_documents_verification) {

                $data = json_decode($v1->department_documents_verification, true);

                foreach ($data as $ik => $iv) {
                    foreach ($iv as $tk => $tv) {
                        if ($tv != 1) {
                            // $list[$ik][$tk] = $tv;
                            $studentDetails[$v1->id]['name'] = $v1->name;
                            $studentDetails[$v1->id]['list'][$ik][$tk] = $tv;
                            // $studentDetails[$ik][$tk]['name'] = $v1->name;
                            // $list[$ik][$tk]['name'] = $v1->name;
                        }
                    }
                }
            }
        }

        $html = "<table border='1'>";
        $html .= "<tr>";
        $html .= "<th>Student Sr. No.</th>";
        $html .= "<th>Name</th>";
        $html .= "<th>Rejected Documents</th>";

        $html .= "</tr>";
        $maincounter = 1;

        foreach ($studentDetails as $stk => $stv) {
            $list = $studentDetails[$stk]['list'];
            $counter = 1;
            $html .= "<tr>";
            $html .= "<td>" . $maincounter++ . "</td>";
            $html .= "<td>" . $studentDetails[$stk]['name'] . "</td>";
            $html .= "<td>";
            $html .= "<table width='100%' border='1'>";
            $html .= "<tr>";
            $html .= "<th>Sr. No.</th>";
            $html .= "<th>Document Name</th>";
            $html .= "<th>Rejected Reason</th>";
            $html .= "</tr>";
            foreach ($list as $k => $v) {
                $html .= "<tr >";
                $html .= "<td>" . $counter++ . "</td>";
                $html .= "<td>" . @$verificationLabels[$k]['hindi_name'] . "</td>";
                $html .= "<td>";
                $html .= "<table width='100%' broder='1'>";
                $icoutner = 0;
                foreach ($v as $ik => $iv) {
                    $icoutner++;
                    if ($iv == 2) {
                        $html .= "<tr>";
                        $html .= "<td>" . $icoutner . ". ";
                        $html .= @$verficationmasterdataFinal[$k][$ik] . " ";
                        $html .= "</td>";
                        $html .= "</tr>";
                    }
                }
                $html .= "</table>";
                $html .= "</td>";
                $html .= "</tr>";
            }

            $html .= "</table>";
            $html .= "</td>";
            $html .= "</tr>";
        }
        $html .= "</table> <style> 
		  td { 
			word-wrap: break-word; /* Allows text to wrap within the cell */ 
			vertical-align: top; /* Aligns text to the top of the cell */ 
		  } 
		</style>";


        $fileName = "rejected_student_with_reasons_" . $examyear . "_" . $exammonth . "_" . now();
        if ($format == 'pdf') {

            $pdf = PDF::loadHTML($html);
            return $pdf->download($fileName . '.pdf');
        } else if ($format == 'html') {
            echo $html;
            die;
        } else if ($format == 'excel') {
            return Excel::download(new RejectedStudentExlExport(compact("verificationLabels", "verficationmasterdataFinal", "studentDetails")), $fileName . '.xlsx');
        }

        return view('landing.rejected_student_with_reasons', compact("verificationLabels", "verficationmasterdataFinal", "studentDetails"));
    }

    public function rohit(Request $request)
    {


        $objController = new Controller();
        $combo_name = 'reval_start_date';
        $reval_start_date = $objController->master_details($combo_name);
        $reval_start_date=@$reval_start_date[1];
        $isValid = false;
        $objController = new Controller();
        $combo_name = 'reval_start_date';
        $reval_start_date= $objController->master_details($combo_name);
        $combo_name = 'reval_end_date';
        echo "<pre>";
        $reval_end_date = $objController->master_details($combo_name);
        $reval_end_date=@$reval_end_date[1];
        print_r($reval_start_date);
        print_r($reval_end_date);
        print_r(strtotime(@$reval_start_date));

        if(strtotime(date("Y-m-d H:i:s")) >= strtotime(@$reval_start_date) &&  strtotime(date("Y-m-d H:i:s")) <= strtotime(@$reval_end_date)){
            $isValid = true;
        }

        $custom_component_obj = new CustomComponent;
        $isAllowForFsdvApplicaitonForm = false;

        $auth_user_id = null;
        $checkRevalAllow = $custom_component_obj->revalAllowOrNot();
        dd($checkRevalAllow);
        die;
        $estudent_id = 'Rohit';
        // $encryptedValue = Crypt::encrypt($estudent_id);
        $encryptedValue = 'sseyJpdiI7RkZWZkUyWHFMKzlRanVoa3FZckJZZGc9PSIsInZhbHVlIjoiVks1ZTVMTTJzYWtVY2YxZCtKUWhZUT09IiwibWFjIjoiNDQ3MmU2M2NlZmVlN2Q5NzBhNTJjNTVkYzU1NTg4MTlkYmIzOTJkZGZiZTI5YjlhNDdiMzAwMWI0Njk1N2JlYyIsInRhZyI6IiJ9';

        // echo $encryptedValue;die;

        $checkEncryptedValueStatus = $this->checkEncryptedValue($encryptedValue);

        if (@$checkEncryptedValueStatus) {
            echo "Valid";
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or corrupted encrypted value.',
            ], 400);
        }
        die;


        /*$results = $result = $this->getStudentEnrollmentList();
		dd($results);
		$student_id = 856249;
		$subject_id = 18;
		$theory_examiner_id = 829;
		$final_theory_marks = null; //null, 236
		$theory_absent = 'on'; //on off

		$result = $this->getSubmitStudentsTheoryMarks($student_id,$subject_id,$final_theory_marks,$theory_absent,$theory_examiner_id);

		dd(json_encode($result));



		$indexItem = 'revalMarksDefaultPageLimit';$indexValue = '40';
		$response = $this->updateInGlobalVariable($indexItem,$indexValue);
		dd($response);
		$response = $this->getFromGlobalVariable($indexItem);
		dd($response);
		// $indexItem = 'revalMarksDefaultPageLimit';

		$response['status'] = false;
		$response['error'] = null;
		// Path to the global.php file
		$filePath = config_path('backend_changeable_global.php');

		if (file_exists($filePath)) {
			// Include the existing configuration
			$config = include $filePath;

			$indexItem = 'revalMarksDefaultPageLimit';
			$indexValue = '999999';

			// dd($config);
			// Step 2: Check if the 'config' array exists and update 'key2'
				if (isset($config[$indexItem])) {
					$config[$indexItem] = $indexValue; // Update the specific key in the array
				}else if(isset($config[0][$indexItem])){
					$config[0][$indexItem] = $indexValue;
				}
				$newContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";  // Convert array back to PHP code format

			// Step 3: Write the updated content back to the file
				if ($newContent !== null) {
					file_put_contents($filePath, $newContent);
					$response['status'] = true;
				} else {
					$response['error'] =  $indexItem . " does not write in the file.";
				}

		}else{
			$response['error'] =  'File not not found.';
		}
		dd($response);
		return $response;



		// Log::info('This is an info message.');
		// die;
		ini_set('max_execution_time', 0);
		ini_set("memory_limit", "-1");

		// $students = array(933035,958272);
		// $part = "rsos/payments/verify_request/";

		$arr1 = ["01001","01006","01018","01020","01027","01033","01045","01065","01066","01067","01073","01076","01079","01104","01111","01114","01115","01116","01117","01118","01119","01120","01121","01122","01123","01124","01125","01126","02001","02002","02019","02035","02065","02096","02109","02116","02123","02131","02140","02144","02145","02146","02147","02148","02149","02150","02151","02152","02153","02154","03001","03002","03004","03017","03028","03037","03041","03046","03047","03048","03050","03052","03054","03056","03058","03059","03060","03061","03062","03063","03064","03065","03066","03068","04001","04002","04006","04032","04034","04045","04049","04059","04061","04063","04065","04067","04073","04075","04077","04078","04079","04080","04081","04082","04083","04084","04085","04086"];
		$arr2 = ["04087","04088","04089","04090","04091","04092","04093","04094","04095","04096","04097","04098","04099","04100","05016","05037","05076","05098","05099","05100","05104","05105","05106","05107","05108","05109","05110","05111","05112","06002","06006","06010","06014","06019","06035","06046","06047","06059","06060","06061","06062","06063","06064","06065","06066","06071","06073","06074","06075","06076","06077","06078","06079","06080","06082","06083","06084","06085","06086","06087","06088","06089","06090","06091","06092","06093","06094","06095","06096","07001","07003","07009","07010","07019","07020","07033","07044","07063","07065","07067","07069","07070","07071","07072","07073","07074","07075","07076","07077","07078","07079","08003","08008","08017","08018","08019","08020","08022","08034","08035","08036","08037","08038","08039","08040","08041","08042","09001","09004","09006","09009","09014","09017","09022","09024","09032","09035","09042","09044","09049","09050","09051","09052","09053","09054"];
		$arr3 = ["09055","09056","09057","09058","09059","09060","09061","09062","09063","09064","09065","09066","09067","09068","09069","09070","09071","09072","09073","09074","09075","09076","09077","09078","09079","10001","10003","10025","10027","10034","10047","10066","10068","10071","10073","10074","10075","10076","10077","10078","10079","10080","10081","10082","10083","10084","11001","11014","11021","11037","11040","11044","11045","11046","11047","11048","11049","11050","11051","11052","11053","11055","11057","11058","11059","11060","11061","11062","11063","11064","11065","11066","11067","12003","12011","12027","12046","12048","12058","12079","12103","12110","12113","12220","12230","12280","12281","12289","12290","12297","12298","12299","12300","12301","12302","12303","12304","12305","12306","12307","13001","13003","13004","13005","13006","13007","13010","13011","13013","13014","13015","13016","13017","14001","14002","14004","14007","14022","14024","14026","14028","14029","14034","14035","14036","14037","14038","14039","14040","14041","14042","15001","15005","15061","15115","15135","15137","15139","15141","15143","15144","15145","15146","15147","15148","15149","15150","15151","15152","16001","16005","16015","16017","16024","16027","16039","16050","16051","16052","16053","16055","16058","16060","16061","16062","16063","16066","16067","16069","17002","17011","17014","17026","17028","17041","17112","17136","17137","17138","17139","17140","17141","18005","18016","18036","18037","18038","18053","18061","18063","18067","18073","18074","18075","18076","18077","18078","18079","18080","18081","19001","19002","19024","19042","19055","19092","19103","19105","19114","19115","19116","19117","19118","19119","19120","19121","20005","20010","20014","20018","20021","20031","20035","20039","20045","20053","20054","20055","20056","20057","20058","20060","20062","20063","20064","20067","20068","20069","20070","20073","20074","21001"];
		$arr4 = ["21002","21003","21045","21051","21064","21065","21066","21067","21068","21069","21070","21071","21072","21073","21074","22001","22011","22014","22082","22118","22119","22121","22123","22126","22127","22128","22129","22130","22131","22132","22133","23002","23003","23007","23013","23014","23016","23017","23018","23019","23020","23021","23022","23023","23024","23025","23026","23028","23029","23030","23031","23032","23034","23035","24001","24009","24022","24030","24053","24061","24063","24064","24067","24068","24069","24070","25001","25002","25004","25005","25007","25012","25015","25018","25020","25052","25053","25054","25055","25056","25057","25058","26001","26004","26010","26020","26024","26028","26031","26039","26045","26060","26063","26066","26067","26068","26069","26070","26071","26073","26074","26075","26076","26077","26079","26080","26082","26084","26086","26090","26094","26095","27005","27014","27015","27016","27017","27018","27019","27020","27021","27022","27024","27025","27026","28001","28010","28026","28041","28067","28075","28076","28077","28082","28083","28084","28088","28089","28094","28095","28096","29001","29012","29017","29020","29023","29028","29033","29036","29037","29038","29039","29040","29041","29042","29043","29044","30001","30004","30006","30007","30008","30009","30010","30011","30015","30019","30022","30023","30024","30025","30026","30027","30028","30029","30030","30031","30032","30033","30034","31001","31006","31008","31032","31050","31051","31053","31055","31057","31059","31060","31061","31062","31063","31064","31065","31066","31067","31068","31069","32001","32006","32011","32035","32036","32039","32041","32042","32047","32048","32049","32050","32051","32052","32053","32054","32055","33001","33002","33003","33004","33005","33006","33007","33009","33010","33011","33013","33014","33015","34001","34002","34003","34004","34005","34006","34007","34008","34009","34010","34011","34040","34057","34062","34066","35001","35002","35003","35004","35005","35006","35007","35008","35009","35010","35020","35021","35022","35025","35047"];
		$arr5 = ["35048","35051","35053","35055","35057","35071","36001","36002","36003","36004","36005","36006","36007","36008","36009","36010","36011","36013","36028","36046","36059","36060","36061","36067","36078","36080","36105","36106","36107","36108","37001","37002","37003","37004","37005","37006","37007","37008","37051","37053","37064","37074","37075","37107","37109","37113","38001","38002","38003","38004","38005","38006","38007","38008","38009","38021","38051","38073","38101","38103","39001","39002","39003","39004","39005","39006","39007","39027","39037","39062","39067","39109","39111","39113","40001","40002","40003","40004","40005","40006","40007"];
		$arr6 = ["40008","40009","40010","40011","40012","40013","40014","40015","40155","40218","41001","41002","41003","41004","41019","41023","41039","41057","42001","42002","42003","42004","42006","42007","42008","42009","42010","42011","42012","42013","42014","42114","42121","42141","42144","42188","42190","42207","42219","42221","42222","42223","42273","42288","42292","42294","42295","43001","43002","43003","43004","43005","43006","43007","43008","43009","43010","43079","43110","43111","43114","43121","43123","43125","43126","43127","43128","43129","43130","43131","43132","44001","44002","44003","44004","44005","44006","44007","44008","44009","44010","44020","44053","44075","44077","44108","44109","44113","44115","44117"];
		$arr7 = ["44119","45001","45002","45003","45004","45005","45006","45007","45008","45009","45088","45091","45135","45142","45143","46001","46002","46003","46004","46005","46006","46007","46008","46009","46010","46011","46053","46067","46075","46076","46153","46212","46296","47001","47002","47003","47004","47005","47006","47008","47035","47051","47109","47125","47133","48001","48002","48003","48004","48005","48006","48007","48008","48009","48029","48037","48072","48081","48088","48092","49001","49002","49003","49004","49005","49006","49007","49008","49009","49010","49011","49012","49014","49016","49018","49031","49033","50001","50002","50003","50004","50005","50006","50007","50022","50023","50031","50044","50068","50069","50070","50072"];


		$arr1 = ["01001","01006","01018","01020","01027","01033","01045","01065","01066","01067","01073","01076","01079","01104","01111","01114","01115","01116","01117","01118","01119","01120","01121","01122","01123","01124","01125","01126","02001","02002","02019","02035","02065","02096","02109","02116","02123","02131","02140","02144","02145","02146","02147","02148","02149","02150","02151","02152","02153","02154","03001","03002","03004","03017","03028","03037","03041","03046","03047","03048","03050","03052","03054","03056","03058","03059","03060","03061","03062","03063","03064","03065","03066","03068","04001","04002","04006","04032","04034","04045","04049","04059","04061","04063","04065","04067","04073","04075","04077","04078","04079","04080","04081","04082","04083","04084","04085","04086"
		,"04087","04088","04089","04090","04091","04092","04093","04094","04095","04096","04097","04098","04099","04100","05016","05037","05076","05098","05099","05100","05104","05105","05106","05107","05108","05109","05110","05111","05112","06002","06006","06010","06014","06019","06035","06046","06047","06059","06060","06061","06062","06063","06064","06065","06066","06071","06073","06074","06075","06076","06077","06078","06079","06080","06082","06083","06084","06085","06086","06087","06088","06089","06090","06091","06092","06093","06094","06095","06096","07001","07003","07009","07010","07019","07020","07033","07044","07063","07065","07067","07069","07070","07071","07072","07073","07074","07075","07076","07077","07078","07079","08003","08008","08017","08018","08019","08020","08022","08034","08035","08036","08037","08038","08039","08040","08041","08042","09001","09004","09006","09009","09014","09017","09022","09024","09032","09035","09042","09044","09049","09050","09051","09052","09053","09054"
		,"09055","09056","09057","09058","09059","09060","09061","09062","09063","09064","09065","09066","09067","09068","09069","09070","09071","09072","09073","09074","09075","09076","09077","09078","09079","10001","10003","10025","10027","10034","10047","10066","10068","10071","10073","10074","10075","10076","10077","10078","10079","10080","10081","10082","10083","10084","11001","11014","11021","11037","11040","11044","11045","11046","11047","11048","11049","11050","11051","11052","11053","11055","11057","11058","11059","11060","11061","11062","11063","11064","11065","11066","11067","12003","12011","12027","12046","12048","12058","12079","12103","12110","12113","12220","12230","12280","12281","12289","12290","12297","12298","12299","12300","12301","12302","12303","12304","12305","12306","12307","13001","13003","13004","13005","13006","13007","13010","13011","13013","13014","13015","13016","13017","14001","14002","14004","14007","14022","14024","14026","14028","14029","14034","14035","14036","14037","14038","14039","14040","14041","14042","15001","15005","15061","15115","15135","15137","15139","15141","15143","15144","15145","15146","15147","15148","15149","15150","15151","15152","16001","16005","16015","16017","16024","16027","16039","16050","16051","16052","16053","16055","16058","16060","16061","16062","16063","16066","16067","16069","17002","17011","17014","17026","17028","17041","17112","17136","17137","17138","17139","17140","17141","18005","18016","18036","18037","18038","18053","18061","18063","18067","18073","18074","18075","18076","18077","18078","18079","18080","18081","19001","19002","19024","19042","19055","19092","19103","19105","19114","19115","19116","19117","19118","19119","19120","19121","20005","20010","20014","20018","20021","20031","20035","20039","20045","20053","20054","20055","20056","20057","20058","20060","20062","20063","20064","20067","20068","20069","20070","20073","20074","21001"
		,"21002","21003","21045","21051","21064","21065","21066","21067","21068","21069","21070","21071","21072","21073","21074","22001","22011","22014","22082","22118","22119","22121","22123","22126","22127","22128","22129","22130","22131","22132","22133","23002","23003","23007","23013","23014","23016","23017","23018","23019","23020","23021","23022","23023","23024","23025","23026","23028","23029","23030","23031","23032","23034","23035","24001","24009","24022","24030","24053","24061","24063","24064","24067","24068","24069","24070","25001","25002","25004","25005","25007","25012","25015","25018","25020","25052","25053","25054","25055","25056","25057","25058","26001","26004","26010","26020","26024","26028","26031","26039","26045","26060","26063","26066","26067","26068","26069","26070","26071","26073","26074","26075","26076","26077","26079","26080","26082","26084","26086","26090","26094","26095","27005","27014","27015","27016","27017","27018","27019","27020","27021","27022","27024","27025","27026","28001","28010","28026","28041","28067","28075","28076","28077","28082","28083","28084","28088","28089","28094","28095","28096","29001","29012","29017","29020","29023","29028","29033","29036","29037","29038","29039","29040","29041","29042","29043","29044","30001","30004","30006","30007","30008","30009","30010","30011","30015","30019","30022","30023","30024","30025","30026","30027","30028","30029","30030","30031","30032","30033","30034","31001","31006","31008","31032","31050","31051","31053","31055","31057","31059","31060","31061","31062","31063","31064","31065","31066","31067","31068","31069","32001","32006","32011","32035","32036","32039","32041","32042","32047","32048","32049","32050","32051","32052","32053","32054","32055","33001","33002","33003","33004","33005","33006","33007","33009","33010","33011","33013","33014","33015","34001","34002","34003","34004","34005","34006","34007","34008","34009","34010","34011","34040","34057","34062","34066","35001","35002","35003","35004","35005","35006","35007","35008","35009","35010","35020","35021","35022","35025","35047"
		,"35048","35051","35053","35055","35057","35071","36001","36002","36003","36004","36005","36006","36007","36008","36009","36010","36011","36013","36028","36046","36059","36060","36061","36067","36078","36080","36105","36106","36107","36108","37001","37002","37003","37004","37005","37006","37007","37008","37051","37053","37064","37074","37075","37107","37109","37113","38001","38002","38003","38004","38005","38006","38007","38008","38009","38021","38051","38073","38101","38103","39001","39002","39003","39004","39005","39006","39007","39027","39037","39062","39067","39109","39111","39113","40001","40002","40003","40004","40005","40006","40007"
		,"40008","40009","40010","40011","40012","40013","40014","40015","40155","40218","41001","41002","41003","41004","41019","41023","41039","41057","42001","42002","42003","42004","42006","42007","42008","42009","42010","42011","42012","42013","42014","42114","42121","42141","42144","42188","42190","42207","42219","42221","42222","42223","42273","42288","42292","42294","42295","43001","43002","43003","43004","43005","43006","43007","43008","43009","43010","43079","43110","43111","43114","43121","43123","43125","43126","43127","43128","43129","43130","43131","43132","44001","44002","44003","44004","44005","44006","44007","44008","44009","44010","44020","44053","44075","44077","44108","44109","44113","44115","44117"
		,"44119","45001","45002","45003","45004","45005","45006","45007","45008","45009","45088","45091","45135","45142","45143","46001","46002","46003","46004","46005","46006","46007","46008","46009","46010","46011","46053","46067","46075","46076","46153","46212","46296","47001","47002","47003","47004","47005","47006","47008","47035","47051","47109","47125","47133","48001","48002","48003","48004","48005","48006","48007","48008","48009","48029","48037","48072","48081","48088","48092","49001","49002","49003","49004","49005","49006","49007","49008","49009","49010","49011","49012","49014","49016","49018","49031","49033","50001","50002","50003","50004","50005","50006","50007","50022","50023","50031","50044","50068","50069","50070","50072"];

		// $arr1 = ["01020"];

		// dd($arr1);

		$q1 = 'select subject_id , volume,course FROM rs_book_volume_masters bv INNER JOIN rs_subjects s ON s.id = bv.subject_id where bv.deleted_at is null group by subject_id,volume  order by subject_id,volume;';
		$q2 = 'select ai_code,subject_id,subject_volume_id from rs_publication_books where exam_year = 126 and deleted_at is null and ai_code in ( ' . implode(",",$arr1) .  ' );';
		$q3 = 'select ai_code from rs_aicenter_details ac where ac.deleted_at is null and ac.is_allow_for_admission = 1 and ac.ai_code in ( ' . implode(",",$arr1) .  ' );';


		$r1 = DB::select($q1);
		$r2 = DB::select($q2);
		$r3 = DB::select($q3);
		$isMissing = [];
		$isPresent = [];
		$presentComb = [];
		$needComb = [];
		 // dd($r1);
		foreach($r3 as $k3 => $v3){
			foreach($r1 as $k1 => $v1){
				$needComb[] = $v3->ai_code . "_" . $v1->subject_id . "_" . $v1->volume;
				foreach($r2 as $k2 => $v2){
					// $needComb[] = $v3->ai_code . "_" . $v1->subject_id . "_" . $v1->volume  . "_" . $v1->course;
					// $presentComb[] = $v2->ai_code . "_" . $v2->subject_id . "_" . $v2->subject_volume_id. "_" . $v1->course;
					$presentComb[] = $v2->ai_code . "_" . $v2->subject_id . "_" . $v2->subject_volume_id;
				}
			}
		}
		echo "<pre> Need : <br>";

		$needComb = array_unique($needComb);
		foreach(array_unique($needComb) as $k => $v){
			// echo $v . "<br>";
		}
		echo "<pre> <br> Present : <br>";
		$presentComb = array_unique($presentComb);
		foreach(array_unique($presentComb) as $k => $v){
			// echo $v . "<br>";
		}
		// die;

		// dd($needComb);
		// dd($presentComb);
		echo "<pre> <br> Missing : <br>";
		$missingComb = array_diff($needComb,$presentComb);

		// dd($missingComb);

		$exam_year = Config::get("global.current_sessional_exam_year");
		$exam_month = Config::get("global.current_sessional_exam_month");
		$documentPath = "apibulkmarks" . DIRECTORY_SEPARATOR  . $exam_year . DIRECTORY_SEPARATOR  . $exam_month .  DIRECTORY_SEPARATOR ;
		$responseFileName = "temp_query.txt";

		foreach(array_unique($missingComb) as $k => $v){
			$detail = explode("_",$v);
			// echo "<br>" . $v . "<br>" ;
			$q1 = "\n" . "INSERT INTO rs_publication_books (" ."exam_month,exam_year,ai_code, subject_volume_id, subject_id".") VALUES (1,126,'" . @$detail[0] . "'," . @$detail[2] . "," . @$detail[1] . ");";
			// echo $q1;
			echo "Writed in file";
			// Log::warning($q1);
			file_put_contents(public_path($documentPath). $responseFileName, $q1, FILE_APPEND);
		}
		die;

		$domain = "https://rsosadmission.rajasthan.gov.in/rsos/";
		if(@$_SERVER['APP_URL']){
			if(isset($_SERVER['APP_URL']) && !empty($_SERVER['APP_URL'])){
				$domain = $_SERVER['APP_URL'];
			}
		}
		$links = [];
		foreach($students as $k => $student_id){
			$details = Student::where('id',$student_id)->first(['name','enrollment']);
			$links[$details->enrollment . '_' . @$details->name . '_' . $student_id ] = $domain ."/" . $part . "". Crypt::encrypt($student_id);
		}

		$html = "<table border='1'>";
		$html .= "<tr>";
		$html .= "<th>";
		$html .= "#";
		$html .= "</th>";
		$html .= "<th>";
		$html .= "Enroll";
		$html .= "</th>";
		$html .= "<th>";
		$html .= "Link";
		$html .= "</th>";
		$html .= "</tr>";
		$counter = 0;
		foreach($links as $k => $link){
			$counter++;
			$html .= "<tr>";
			$html .= "<td>";
			$html .= $counter;
			$html .= "</td>";

			$html .= "<td>";
			$html .= $k;
			$html .= "</td>";
			$html .= "<td>";
			$html .= $link;
			$html .= "</td>";
			$html .= "</tr>";
		}
		$html .= "</table>";

		echo "<pre>";
		print_r($html);
		die;

		// return view('landing.rohit');
		$custom_component_obj = new CustomComponent;
		$student_id = 856260;
		$inputs = $request->all();

		$details = $custom_component_obj->getStudentVerificaitonMainDocDetails($student_id);
		// dd($student_id);
		$saveDetails = $custom_component_obj->saveStudentVerificaitonMainDocDetails($inputs);
		// dd($details);

		$verLabelOnlyForMainDocItems = $custom_component_obj->getStudentVerificaitonMainDocDetails();
		$verLabelOnlyForMainDocItems = $custom_component_obj->getLabelOnlyForVerificaitonMainDocLists();
		$adm_type = 1;$course = 10;$gender_id = 1;$main_document_id=3;
		$verMainDocItems = $custom_component_obj->getVerificaitonMainDocLists($adm_type,$course,$gender_id,$main_document_id);
		$saveDetails = null;
		// $saveDetails["verifier_documents_verification"] = $verMainDocItems;
		// $saveDetails = json_encode($saveDetails); //verifier_status
		//saveStudentVerificaitonMainDocDetails() , getStudentVerificaitonMainDocDetails()

		dd($verMainDocItems);
		echo "Test"; //SELECT * FROM `lrsos`.`rs_verification_masters`
		die;


		$exam_year=126;$course=0;$stream=0; $locksumbitted=1;$is_eligible=0; $isonlyissue=0;$limitinput=10;
		$studentData = DB::select('call getFreshAppFinalSummary(?,?,?,?,?,?,?)',array($exam_year, $course, $stream, $locksumbitted, $is_eligible, $isonlyissue, $limitinput));
		dd($studentData);

		$exam_year = Config::get("global.current_sessional_exam_year");
		$exam_month = Config::get("global.current_sessional_exam_month");
		$limit = "20000";
		$details = SessionalExamSubject::Join('students', 'sessional_exam_subjects.student_id', '=', 'students.id')
			->where('sessional_exam_subjects.exam_month',$exam_month)->where('sessional_exam_subjects.exam_year',$exam_year)
			->limit($limit)
			->
			get(['students.enrollment','sessional_exam_subjects.subject_id','sessional_exam_subjects.sessional_marks as obtained_marks'])
			;



		$finalArr = array();

		if(@$details){
			foreach($details as $k => $v){
				$finalArr[$k]['enrollment'] =  (string)@$v->enrollment;
				$finalArr[$k]['subject_id'] =  (string)@$v->subject_id ;
				$finalArr[$k]['obtained_marks'] =  (string)@$v->obtained_marks;
			}
		}
		$details = json_encode($finalArr);
		print_r($details);
		die;


		$ssoid = "RAJENDRASINGHRAO1986";
		$dob = "1986-12-10";
		$start_time = microtime(true);
		$curl = curl_init();
		curl_setopt_array($curl, array(
		   CURLOPT_URL => 'https://rsosadmission.rajasthan.gov.in/rsos/api/new_api_student_login',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_SSL_VERIFYPEER => false,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => array('ssoid' => $ssoid,'dob' => $dob,'token' => 'IUAjJCVeJiooKWRvaXRj'),
		  CURLOPT_HTTPHEADER => array( ),
		));


		$response = curl_exec($curl);

		curl_close($curl);

		$end_time = microtime(true);
		$total_time = $end_time - $start_time;
		$milliseconds = intval($total_time * 1000) . " ms";
		$start_time = date("Y-m-d H:i:s", substr($start_time, 0, 10));
		$end_time = date("Y-m-d H:i:s", substr($end_time, 0, 10));

		echo "<br><h2><u>1. Login API Input</u></h2><br>";
		echo "SSOID" . $ssoid;
		echo " and DOB" . $dob;
		echo "<br><h2><u>Time</u></h2><br>";
		echo "Start : " . $start_time . " <br> End : " .  $end_time . " <br> Take Time : <h2>" . $milliseconds . "</h2>";
		echo "<br><h2><u>Response</u></h2><br>";
		echo ($response);
		$response = json_decode($response,true);

		$ssoid = "RAJENDRASINGHRAO1986";
		$token = "IUAjJCVeJiooKWRvaXRj";
		$dob = "1986-12-10";
		$secure_token = $response['secure_token'];
		$start_time = microtime(true);
		$curl = curl_init();
		curl_setopt_array($curl, array(
		   CURLOPT_URL => 'https://rsosadmission.rajasthan.gov.in/rsos/api/new_api_student_exam_subjects',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_SSL_VERIFYPEER => false,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => array('ssoid' => $ssoid,'dob' => $dob,'token' => $token,'secure_token' => $secure_token),
		  CURLOPT_HTTPHEADER => array( ),
		));


		$response = curl_exec($curl);

		curl_close($curl);

		$end_time = microtime(true);
		$total_time = $end_time - $start_time;
		$milliseconds = intval($total_time * 1000) . " ms";
		$start_time = date("Y-m-d H:i:s", substr($start_time, 0, 10));
		$end_time = date("Y-m-d H:i:s", substr($end_time, 0, 10));

		echo "<br>------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>";
		echo "<br><h2><u>2. Exam Subject API Input</u></h2><br>";
		echo "SSOID " . $ssoid;
		echo " and DOB " . $dob;
		echo " and secure_token " . $secure_token;
		echo "<br><h2><u>Time</u></h2><br>";
		echo "Start : " . $start_time . " <br> End : " .  $end_time . " <br> Take Time : <h2>" . $milliseconds . "</h2>";
		echo "<br><h2><u>Response</u></h2><br>";
		echo ($response);






		die;




		$start_time = $this->start_time();
		$this->end_time($start_time);
		die;

		$url = Config::get("global.APP_URL") . "public/app-assets/images/RSOS_APK.zip";
		echo '<a style="display:none;" href="' . $url .'" target="_blank" id="link">RSOS APK</a>  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
		<script type="text/javascript">
		$(document).ready(function(){
		   document.getElementById("link").click();
		});</script>';
		die;

		$isNextRoundRequire = $this->genFixcodeCenter(true);
		if($isNextRoundRequire == false){
			echo "<h1>Done</h1>";
		}
		die;


		/* student not allowed start */
        //$role_id = @Session::get('role_id');
        //if($role_id == Config::get('global.student')){
        //return redirect()->route('studentsdashboards')->with('error', 'Failed! You are not allowed!');
        //}
        /* student not allowed end ->where("student_id", 619630) */

        /*$studentIds = Supplementary::where("exam_year", 125)->where("exam_month", 1)->limit(5000)->get(['enrollment','total_fees','locksumbitted','student_id']);

		$finalArr = array();
		echo "<table class='table' width='100%' border='1'>";
		echo "<tr>";
		echo "<td>";
			echo "Student Id";
		echo "</td>";
		echo "<td>";
			echo "Enrollment";
		echo "</td>";
		echo "<td>";
			echo "Supp Table Fee";
		echo "</td>";

		echo "<td>";
			echo "Cal Fee";
		echo "</td>";
echo "<td>";
			echo "Is Locked";
		echo "</td>";
		echo "<td>";
			echo "Diff";
		echo "</td>";
		echo "</tr>";

		foreach($studentIds as $value){

			$total_fees = $value->total_fees;
			$student_id = $value->student_id;
			$enrollment = $value->enrollment;
			$locksumbitted = $value->locksumbitted;

			$CustomComponent = new CustomComponent;
			$estudent_id = $student_id;

			$master = $CustomComponent->_getSuppFeeDetailsForDispaly($student_id);
			if($total_fees != $master['final_fees']){
				$finalArr[$student_id] = $master['final_fees'];
				echo "<tr>";
				echo "<td>";
					echo $student_id;
				echo "</td>";
				echo "<td>";
					echo @$enrollment;
				echo "</td>";

				echo "<td>";
					echo $total_fees;
				echo "</td>";
echo "<td>";
					echo $locksumbitted;
				echo "</td>";
echo "<td>";
					echo $master['final_fees'];
				echo "</td>";
				echo "<td>";
					echo $total_fees - $master['final_fees'] ;
				echo "</td>";
				echo "</tr>";
			}
		}
		echo "</table>"; */
    }

    public function checkEncryptedValue($encryptedValue = null)
    {
        if (@$encryptedValue) {
            try {

                // Attempt to decrypt the value
                if ($this->validPayload($encryptedValue)) {
                    //$decryptedValue = 'Not Valid';
                } else {
                    $decryptedValue = Crypt::decrypt($encryptedValue);
                }

                dd($decryptedValue);
                return true;
            } catch (DecryptException $e) {
                return false;
            }
        } else {
            return false;
        }
        return false;
    }

    public function validPayload($payload)
    {
        try {
            $decrypted = decrypt($payload);
        } catch (DecryptException $e) {
            var_dump($e);
        }
    }

    public function againoldrohit()
    {
        $student_id = '589260';
        $estudent_id = Crypt::encrypt($student_id);
        echo($estudent_id);
        die;
        $data = $this->_getStudentHistoryDetails($estudent_id);
        dd($data);
    }

    public function temp_reval_marks_setting()
    {
        $q1 = 'select 
			concat("update rs_reval_student_subjects set sessional_marks = ",es.sessional_marks_reil_result , " where student_id = ", es.student_id , " and subject_id = ", es.subject_id , ";" ) as q,	rss.sessional_marks,rss.subject_id,rss.student_id,rss.total_marks,rss.final_theory_marks,rss.final_result,	es.sessional_marks_reil_result,es.subject_id,es.student_id,es.total_marks,es.final_theory_marks,es.final_result
			from rs_reval_student_subjects rss
			inner join rs_provisional_exam_subjects es on es.student_id = rss.student_id and es.subject_id = rss.subject_id and 
			es.exam_year = 125 and es.exam_month = 1 and es.deleted_at is null and rss.deleted_at is null
			where rss.student_id in (
			select student_id from rs_reval_students where deleted_at is null 
			) limit 80000;';
        //student_id = 744940  and
        $results = DB::select($q1);
        // dd($results);
        foreach ($results as $result) {
            DB::select($result->q);
        }
        print_r("Marks updated");

        // dd($results);
        $conditions = array();
        $masterDetails = RevalStudent::Join('students', 'students.id', '=', 'reval_students.student_id')
            ->Join('student_allotments', 'student_allotments.student_id', '=', 'reval_students.student_id')
            ->Join('reval_student_subjects', 'reval_students.id', '=', 'reval_student_subjects.reval_id')
            ->Join("student_allotment_marks", function ($join) {
                $join->on("student_allotments.id", "=", "student_allotment_marks.student_allotment_id");
                $join->on("student_allotment_marks.subject_id", "=", "reval_student_subjects.subject_id");
            })
            ->whereNull("reval_student_subjects.deleted_at")
            ->whereNull("student_allotment_marks.deleted_at")
            ->whereNull("reval_students.deleted_at")
            // ->whereIn("reval_students.student_id", [779811])
            ->where($conditions)
            // ->limit(5)
            ->get(
                [
                    "student_allotment_marks.id as student_allotment_marks_id"
                    , "reval_student_subjects.id as reval_student_subjects_id"
                    , "student_allotment_marks.subject_code as subject_code"
                    , "student_allotment_marks.reval_final_theory_marks as val"
                ]
            );

        // dd($masterDetails);
        foreach ($masterDetails as $k => $detail) {
            $detail = $detail->toArray();
            $response[$k] = $this->TempCalAndSubmissionOfRevalmarks($detail);
            // dd($response);
        }
        dd("Done");
    }

    public function TempCalAndSubmissionOfRevalmarks($urlinputs = null)
    {
        $status = false;
        $error = null;
        $data = null;

        /* Logic Start */
        $student_allotment_marks_id = @$urlinputs['student_allotment_marks_id'];
        $revalStSubId = @$urlinputs['reval_student_subjects_id'];
        $revalSubjectId = $this->subjectIdByCode(@$urlinputs['subject_code']);
        $item = "reval_final_theory_marks";//(@$urlinputs['item']);
        $val = (@$urlinputs['val']);


        $inputs['key'] = Crypt::encrypt($urlinputs['student_allotment_marks_id']);
        // dd($revalStSubId);
        $inputs['revalStSubId'] = Crypt::encrypt($urlinputs['reval_student_subjects_id']);
        $inputs['revalSubjectId'] = $revalSubjectId;
        $inputs['item'] = $item;
        $inputs['val'] = $val;

        // $student_allotment_marks_id = Crypt::decrypt(@$inputs['key']);
        // $revalStSubId = Crypt::decrypt(@$inputs['revalStSubId']);
        // $revalSubjectId = $controller_obj->subjectIdByCode(@$inputs['revalStSubCode']);
        // $item = @$inputs['item'];
        // $valTheoryMarksInput = @$inputs['val'];

        $saveData = null;
        if ($item == "reval_final_theory_marks" || $item == "reval_type_of_mistake") {
            $saveData[$item] = $val;
        } else {
            $error = "Something is wrong!";
            $output = array('status' => false, 'error' => $error, 'data' => null);
            return response()->json($output);
        }


        $theory_custom_component_obj = new ThoeryCustomComponent;
        $getMaxMarks = $theory_custom_component_obj->getTheoryMaxMarks(@$revalSubjectId);

        $maxmarks = @$getMaxMarks->theory_max_marks;
        if ($item == "reval_final_theory_marks") {
            if ($val > $maxmarks) {
                $error = "Theory marks should be less then equal to max marks(" . $maxmarks . ").";
                $output = array('status' => false, 'error' => $error, 'data' => null);
                return response()->json($output);
            }
        }


        $oldRevalStudentSubject = RevalStudentSubject::where("id", "=", $revalStSubId)->first();
        $oldStudentAllotmentMarks = StudentAllotmentMark::where("id", "=", $student_allotment_marks_id)->first();


        $reval_custom_component_obj = new RevalMarksComponent;
        // dd($item);
        if ($item == "reval_final_theory_marks") {

            $responae_reval_process_result = $reval_custom_component_obj->reval_process_result($inputs);
            // dd($responae_reval_process_result);
            $RevalStudentSubjectSaveData['final_theory_marks_after_reval'] = $responae_reval_process_result['new_theory_marks'];
            $RevalStudentSubjectSaveData['is_grace_marks_given_after_reval'] = $responae_reval_process_result['is_grace_marks_given_after_reval'];
            $RevalStudentSubjectSaveData['grace_marks_after_reval'] = $responae_reval_process_result['grace_marks_after_reval'];
            $responae_reval_process_result['final_result_after_reval'] = $this->checkValType($responae_reval_process_result['final_result_after_reval']);

            $RevalStudentSubjectSaveData['final_result_after_reval'] = $responae_reval_process_result['final_result_after_reval'];
            $RevalStudentSubjectSaveData['total_marks_after_reval'] = $responae_reval_process_result['total_marks_after_reval'];


            // dd($RevalStudentSubjectSaveData);
            $revalStudentSubjectUpdateStatus = RevalStudentSubject::where("id", "=", $revalStSubId)->update($RevalStudentSubjectSaveData);
        }

        $saveData = array();

        if ($item == "reval_final_theory_marks") {
            $saveData["reval_any_change"] = true;
            $saveData["reval_difference_after_reval"] = "";
            $saveData["reval_is_subject_result_change"] = true;
            $saveData["reval_is_subject_marks_entered"] = true;

            $fld = "final_result_after_reval";
            $saveData["reval_change_in_result"] = true;
            if ($oldRevalStudentSubject[$fld] != $responae_reval_process_result[$fld]) {
                $saveData["reval_change_in_result"] = false;
                $saveData["reval_is_subject_result_change"] = false;
            }
        }
        $studentAllotmentMarks = StudentAllotmentMark::where("id", "=", $student_allotment_marks_id)->update($saveData);

        $studentAllotmentMarks = StudentAllotmentMark::where("id", "=", $student_allotment_marks_id)->first();
        $studentAllotment = StudentAllotment::where("id", "=", $studentAllotmentMarks->student_allotment_id)->first();
        $revalStudentSubject = RevalStudentSubject::where("id", "=", $revalStSubId)->first();

        $data['final_theory_marks_before_reval'] = @$revalStudentSubject->final_theory_marks;
        $data['final_result_before_reval'] = @$revalStudentSubject->final_result;
        $data['total_marks_before_reval'] = @$revalStudentSubject->total_marks;
        $data['sessional_marks_before_reval'] = @$revalStudentSubject->sessional_marks;
        $data['final_practical_marks_before_reval'] = @$revalStudentSubject->final_practical_marks;

        $data['final_result_after_reval'] = @$revalStudentSubject->final_result_after_reval;
        $data['total_marks_after_reval'] = @$revalStudentSubject->total_marks_after_reval;
        $data['final_theory_marks_after_reval'] = @$revalStudentSubject->final_theory_marks_after_reval;
        $data['msg'] = "Updated!";
        $status = true;
        /* Logic End */

        $output = array('status' => $status, 'error' => $error, 'data' => $data, 'extra' => @$responae_reval_process_result);

        return response()->json($output);
    }

    public function checkValType($value = null)
    {
        if ($value != '' && $value != NULL) {
            if ($value == 'AB' || $value == 'A') {
                return 999; //For AB(Absent) value
            } else if ($value == 'SYCP') {
                return 666; //For SYCP value
            } else if ($value == 'SYCT') {
                return 777; //For SYCT value
            } else if ($value == 'SYC') {
                return 888; //For SYC(Supplementary) value
            } else if ($value == 'WH') {
                return 222; //For SYC(Supplementary) value
            } else if ($value == 'RW') {
                return 333; //For SYC(Supplementary) value
            } else if ($value == 'RWH') {
                return 444; //For SYC(Supplementary) value
            } else {
                return $value;
            }
        } else {
            return 0;
        }
    }

    public function multi_custom_sms_sent()
    {

        // $templateID = "1107170900912088833";
        // $sms = "R-CAT is providing up to 100% Scholarships on IT Courses with Globally recognised Certifications. Last Date to apply for Quiz-A-Thon- 2024(1) is 22 May, 2024. Register now at: https://rcat.rajasthan.gov.in/content/raj/rcat/en/quizathon.html. - R-CAT, GoR";
        // $mobiles = array("8946919241","9461017852");
        // $templateID = null;
        // foreach($mobiles as $k => $mobile){
        // $smsStatus = $this->_sendSMS($mobile,$sms,$templateID);
        // echo $k+1 . "<br>";
        // }
        $mobiles = array("8946919241");
        $templateID = "1107170919247305853";
        $sms = "Dear ‌Rajdeep Patel Your registration of R-CAT, Quiz-A-Thon- 2024(1) is pending. Please login with your SSO ID and complete the registration before 22 May, 2024 - R-CAT, GoR.";
        foreach ($mobiles as $k => $mobile) {
            $smsStatus = $this->_sendSMS($mobile, $sms, $templateID);
            echo $k + 1 . "<br>";
        }
        echo "<br>Done";
        die;
    }

    public function sms_utility(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $page_title = 'Student Registration';
        $inputs = $request->all();
        $status = false;
        $data = false;
        $error = null;
        $response = array('status' => false, "data" => $data, "error" => $error);
        $documentPath = "sendmobilesms" . DIRECTORY_SEPARATOR;

        $inputFileName = "send_sms.json";
        $path = $_SERVER['HTTP_HOST'] . DIRECTORY_SEPARATOR . 'rsos' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $documentPath . $inputFileName;
        $path = "http://10.68.181.213/rsos/public/sendmobilesms/updated_live_send_sms.json"; //updated_live_send_sms.json

        $data = file_get_contents($path, "r");

        $isJson = $this->isJson($data);
        $isValidJsonName = "invalid";
        if (@$isJson) {
            $isValidJsonName = "valid";
        }
        $inputFile = @$_FILES['bulk_inputs'];
        $datetime = date("_d_m_Y_H_i_s");
        $inputName = $isValidJsonName . "_" . @$_FILES['bulk_inputs']['name'] . $datetime . ".json";
        $responseFileName = "response_" . @$_FILES['bulk_inputs']['name'] . $datetime . ".json";


        // $request->bulk_inputs->move(public_path($documentPath), $inputName);
        if ($isJson) {
        } else {
            $error = 'Invalid JSON input in file.';
            $response = array('status' => $status, "data" => 'Json File', "error" => $error);
            file_put_contents(public_path($documentPath) . $responseFileName, json_encode($response));
            return $response;
        }
        $bulk_inputs = json_decode($data, true);


        $isValidInputString = array();
        $totalUpdated = 0;
        foreach (@$bulk_inputs as $key => $input) {
            if (@$input["mobile"] && @$input["sms"] && @$input["template_id"]) {
                $status = true;
                $mobile = @$input["mobile"];
                $templateID = @$input["template_id"];
                $sms = @$input["sms"];
                $smsResp = $this->_sendSMS($mobile, $sms, $templateID);
                $totalUpdated++;
            } else {
                continue;
            }
        }

        $data = "Inputs";
        if (@$isValidInputString) {
            $error = $isValidInputString;
        } else {
            $data = "Total of " . @$totalUpdated . " sms sent.";
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        // file_put_contents(public_path($documentPath). $responseFileName, json_encode($response));
        echo json_encode($response);
        die;

        return view('landing.sms_utility', compact("response"));
    }

    public function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function custom_sms_sent()
    {


        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        // echo "TTT";die;
        $sms = "Dear Laxmi Kant Tanwar,Congratulations on successfully qualifying Quiz-A-Thon Exam, You have achieved 50% Scholarship and we have Scheduled Online Counselling on 11.09.2024 & Time is 10.30 AM  Link for Online Counselling has been shared on your mail,Best Wishes - RCAT, GoR";
        $mobile = "8946919241";
        $templateID = "1107167576482678034";//10k
        $smsStatus = $this->_sendSMS($mobile, $sms, $templateID);
        dd($mobile);
        echo "Stoped";
        die;

        // $sms = "Dear Laxmi Kant Tanwar,Congratulations on successfully qualifying Quiz-A-Thon Exam, You have achieved 50% Scholarship and we have Scheduled Online Counselling on 11.09.2024 & Time is 10.30 AM  Link for Online Counselling has been shared on your mail,Best Wishes - RCAT, GoR";
        // $mobile = "8946919241";

        // $templateID = "1107167576482678034";//offline

        // $smsStatus = $this->_sendSMS($mobile,$sms,$templateID);
        // dd($mobile);
        // echo "Stoped"; 9461017852
        // die;


        // echo "Stoped";die;

        $mobiles = DB::table('itjobfair_details')->select('id', 'mobile', 'sms', 'template_id')
            ->whereIn("extra", ["online", "offline", "rohit"])
            //->whereIn("mobile",["9587655668"]) //9461017852  9587655668
            // ->where('id',1)
            ->limit(25000)->get();

        // dd(($mobiles)); http://10.68.181.213/rsos/custom_sms_sent
        // $mobiles = array();
        // $templateID = '1107170919247305853';

        // $templateID = '1007965086450341439';
        foreach ($mobiles as $k => $data) {
            $sms = $data->sms;
            // $sms = "Dear Vishal Prajapat, Congratulations on successfully qualifying Quiz-A-Thon 2024(3) Exam. You have achieved 50% Scholarship. Your Counselling Date is 25-11-2024 to 27-11-2024, Counselling Venue is R-CAT Campus Old campus JNVU & Time is 10.30 AM";
            $mobile = $data->mobile;

            $templateID = $data->template_id;
            // $mobile= "8946919241";
            // dd($templateID);
            // $templateID = '1007335231510093740';
            $smsStatus = $this->_sendSMS($mobile, $sms, $templateID);
            // dd($smsStatus);
            echo $sms . "<br>" . $mobile . " " . $templateID . " " . $k + 1 . "<br>";
        }
        echo "<br>Done";
        die;


        $inputs['isAllRejected'] = 1;
        /* sms send start */
        if (@$inputs['isAllRejected'] == 1) {
            $details = $this->getVerifcaionSMSSend('deficient', 'eng');
            $detailsHindi = $this->getVerifcaionSMSSend('deficient', 'hindi');
        } else {
            $details = $this->getVerifcaionSMSSend('approved', 'eng');
            $detailsHindi = $this->getVerifcaionSMSSend('approved', 'hindi');
        }
        $detailsHindi['mobile'] = $details['mobile'] = "8946919241";

        if ($details['mobile'] != null && $details['sms'] != null && $details['templateID'] != "") {
            $smsStatus = $this->_sendSMS($details['mobile'], $details['sms'], $details['templateID']);
        }
        if ($detailsHindi['mobile'] != null && $detailsHindi['sms'] != null && $detailsHindi['templateID'] != "") {
            $smsStatus = $this->_sendSMS($detailsHindi['mobile'], $detailsHindi['sms'], $detailsHindi['templateID']);
        }
        /* sms send end  */
        echo "Done";
        die;

        $details = $this->getVerifcaionSMSSend('deficient', 'hindi');
        dd($details);
        $details = $this->getVerifcaionSMSSend('approved', 'hindi');
        dd($details);

    }

    public function himmat()
    {
        $student_id = "720639";
        $master_id = "280";
        $this->_movementOfFreshDocuemnts($student_id, $master_id);

        die;
        $ssoid = "RAJENDRASINGHRAO1986";
        $dob = "1986-12-10";
        $start_time = microtime(true);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://rsosadmission.rajasthan.gov.in/rsos/api/new_api_student_login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('ssoid' => $ssoid, 'dob' => $dob, 'token' => 'IUAjJCVeJiooKWRvaXRj'),
            CURLOPT_HTTPHEADER => array(),
        ));


        $response = curl_exec($curl);

        curl_close($curl);

        $end_time = microtime(true);
        $total_time = $end_time - $start_time;
        $milliseconds = intval($total_time * 1000) . " ms";
        $start_time = date("Y-m-d H:i:s", substr($start_time, 0, 10));
        $end_time = date("Y-m-d H:i:s", substr($end_time, 0, 10));

        echo "<br><h2><u>Input</u></h2><br>";
        echo "SSOID" . $ssoid;
        echo " and DOB" . $dob;
        echo "<br><h2><u>Time</u></h2><br>";
        echo "Start : " . $start_time . " <br> End : " . $end_time . " <br> Take Time : <h2>" . $milliseconds . "</h2>";
        echo "<br><h2><u>Response</u></h2><br>";
        echo($response);
        die;


        $student_id = '623402';
        $estudent_id = Crypt::encrypt($student_id);
        echo($estudent_id);
        die;
        $data = $this->_getStudentHistoryDetails($estudent_id);
        dd($data);
        // $t=time();
        // echo($t . "<br>");
        // $date = date("Y-m-d H:i:s",$t);
        // $date = "10:10:05";
        // $newDate = date('Y-m-d H:i:s', strtotime($date. ' +10 minutes'));
        // echo $newDate . "<br>";
        // die;
        // $student_id = '717659';
        // $smsStatus = $this->_sendLockSubmittedMessage($student_id);
        // echo "FFFF";die;

        $toc_coditions['toc.exam_year'] = 125;
        $toc_coditions['toc.exam_month'] = 1;
        //$toc_coditions_students = array();

        //$toc_data = DB::table('toc')->select('id','student_id','board','course')->whereIn('student_id',$toc_coditions_students)->where($toc_coditions)->whereNull('deleted_at')->get();

        $toc_data = DB::table('toc')->select('id', 'student_id', 'board', 'course')->where($toc_coditions)->whereNull('deleted_at')->get();


        $row_update = 0;
        if (!empty($toc_data)) {
            foreach ($toc_data as $key => $toc) {
                $toc_id = $toc->id;
                $toc_board = $toc->board;

                if ($toc_board == 59 || $toc_board == 60) {
                    continue;
                }

                $toc_marks_coditions['toc_marks.toc_id'] = $toc_id;
                $toc_marks_data = DB::table('toc_marks')->where($toc_marks_coditions)->whereNull('deleted_at')->get();

                $toc_custom_data = array();

                foreach ($toc_marks_data as $keytwo => $toc_marks) {
                    // dd($toc_marks->id);
                    if (isset($toc_marks->subject_id) && !empty($toc_marks->subject_id)) {
                        if (@$toc_marks->theory) {
                        } else {
                            $toc_marks->theory = 0;
                        }
                        if (@$toc_marks->practical) {
                        } else {
                            $toc_marks->practical = 0;
                        }

                        $toc_custom_data['conv_practical'] = $this->_getCONVTocMarkPractical($toc_board, $toc_marks->subject_id, $toc_marks->practical);
                        $toc_custom_data['conv_theory'] = $this->_getCONVTocMarkTheory($toc_board, $toc_marks->subject_id, $toc_marks->theory, $toc_custom_data['conv_practical'], @$toc_marks->practical);
                        $toc_custom_data['conv_total_marks'] = ($toc_custom_data['conv_theory'] + $toc_custom_data['conv_practical']);

                        if ($toc->course == 10) {
                            $ddSave['TocMark']['conv_total_marks'] = $toc_marks->total_marks;
                            if ($toc->board == 56 && ($toc_marks->subject_id == 3 || $toc_marks->subject_id == 4)) {
                                $toc_custom_data['conv_practical'] = $ddSave['TocMark']['conv_practical'] = round(($toc_marks->total_marks * 15) / 100, 0);

                                $toc_custom_data['conv_theory'] = $ddSave['TocMark']['conv_theory'] = round(($toc_marks->total_marks - $ddSave['TocMark']['conv_practical']), 0);
                                $toc_custom_data['conv_total_marks'] = $ddSave['TocMark']['conv_total_marks'] = $toc_marks->total_marks;
                            }
                            if ($toc->board == 15 && ($toc_marks->subject_id == 3 || $toc_marks->subject_id == 4)) {
                                $toc_custom_data['conv_practical'] = $ddSave['TocMark']['conv_practical'] = round(($toc_marks->total_marks * 15) / 100, 0);
                                $toc_custom_data['conv_theory'] = $ddSave['TocMark']['conv_theory'] = round(($toc_marks->total_marks - $ddSave['TocMark']['conv_practical']), 0);
                                $toc_custom_data['conv_total_marks'] = $ddSave['TocMark']['conv_total_marks'] = $toc_marks->total_marks;
                            }
                        }
                        TocMark::where('id', '=', $toc_marks->id)->update($toc_custom_data);
                        unset($toc_custom_data);
                    }
                }
                $row_update++;
                echo "Updated TOC Row Count : " . $row_update;
                echo "</br>";
            }
        }
        echo "</br> Script End ";
        die;
    }

    public function _movementOfFreshDocuemnts($student_id = null, $master_id = null)
    {
        try {
            $combo_name = 'student_document_path';
            $student_document_path = $this->master_details($combo_name);
            $studentDocumentPath = $student_document_path[1] . $student_id . '/';

            $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
            $current_exam_month_id = Config::get("global.form_current_exam_month_id");

            $combo_name = 'marked_rejected_move_documents';
            $marked_rejected_move_documents = $this->master_details($combo_name);
            $markedRejectedMoveDocumentPath = @$marked_rejected_move_documents[1] . $current_admission_session_id . DIRECTORY_SEPARATOR . $current_exam_month_id . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$master_id . DIRECTORY_SEPARATOR;
            $combo_name = 'student_verification_documents';

            $student_verification_documents = $this->master_details($combo_name);
            $studentVerificationDocumentPath = @$student_verification_documents[1] . $current_admission_session_id . DIRECTORY_SEPARATOR . $current_exam_month_id . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$master_id . DIRECTORY_SEPARATOR;


            // cut from "main document" to paste into "marked_rejected_move_documents".
            // copy from student verification document "student_verification_documents" to paste in main "main document".


            $studentDocumentVerificationDetails = StudentDocumentVerification::where('student_id', $student_id)->where('id', $master_id)->orderby("id", "desc")->first(['photograph', 'signature', 'category_a', 'category_b', 'category_c', 'category_d', 'pre_qualification', 'disability']);

            $folder_path = public_path($markedRejectedMoveDocumentPath);
            File::makeDirectory($folder_path, $mode = 0777, true, true);
            $custom_data = array(
                'photograph' => $studentDocumentVerificationDetails->photograph,
                'signature' => $studentDocumentVerificationDetails->signature,
                'category_a' => $studentDocumentVerificationDetails->category_a,
                'category_b' => $studentDocumentVerificationDetails->category_b,
                'category_c' => $studentDocumentVerificationDetails->category_c,
                'category_d' => $studentDocumentVerificationDetails->category_d,
                'pre_qualification' => $studentDocumentVerificationDetails->pre_qualification,
                'disability' => $studentDocumentVerificationDetails->disability,
            );
            foreach ($custom_data as $getdata) {
                if (!empty($getdata)) {
                    $marked_rejected_move_documents = $markedRejectedMoveDocumentPath . $getdata;
                    $studentDocumentPaths = $studentDocumentPath . $getdata;

                    $student_verification_documents = $studentVerificationDocumentPath . $getdata;
                    $studentDocumentPathss = $studentDocumentPath . $getdata;

                    $marked_rejected_move_documents = public_path($marked_rejected_move_documents);
                    $studentDocumentPaths = public_path($studentDocumentPaths);

                    $student_verification_documents = public_path($student_verification_documents);
                    $studentDocumentPathss = public_path($studentDocumentPathss);

                    if (file_exists($studentDocumentPaths)) {
                        $move = File::move($studentDocumentPaths, $marked_rejected_move_documents);
                    } else {
                        $isValid = false;
                    }
                    if (file_exists($student_verification_documents)) {
                        $move = File::copy($student_verification_documents, $studentDocumentPathss);
                    } else {
                        $isValid = false;
                    }

                }
            }
        } catch (Exception $e) {
            if (!($e instanceof SQLException)) {
                app()->make(\App\Exceptions\Handler::class)->report($e);
            }
            return redirect()->back()->with('error', 'Failed! Document Can Not Been Uploaded!');
        }
    }


    //print_r($studentDocumentPath);
    //echo "<br>";
    //print_r($markedRejectedMoveDocumentPath);
    //echo "<br>";
    //print_r($studentVerificationDocumentPath);
    //die;
    //main doc copy into the id of rs_student_document_verifications

    // cut from "main document" to paste into "marked_rejected_move_documents".
    // copy from student verification document "student_verification_documents" to paste in main "main document".


    //$combo_name = 'current_folder_year';$current_folder_year = $this->master_details($combo_name);
    //$current_folder_year = $this->getCurrentYearFolderName();
    //$combo_name = 'student_supplementary_document_path';$student_document_path = //$this->master_details($combo_name);
    //$exam_month = Config::get('global.supp_current_admission_exam_month');

    //$suppVerifcationData = DB::table('supplementary_verifications')->where('supplementary_id',@$supp_id)->   whereNull('deleted_at')->orderBy('id','desc','aicenter_rejected_marksheet_document','department_rejected_marksheet_document')->first();

    //$supplementaryDetails =DB::table('supplementary_verification_documents')->where('supplementary_id', $supp_id)->orderBy('id','DESC')->first(['id','supp_doc','sec_marksheet_doc']);
    ///$custom_data = null;
    //if(@$suppVerifcationData->aicenter_rejected_marksheet_document == 3 || //@$suppVerifcationData->department_rejected_marksheet_document == 3){
    //$custom_data = array(
    //'marksheet_doc'=>$supplementaryDetails->supp_doc,
    //'sec_marksheet_doc'=>$supplementaryDetails->sec_marksheet_doc,
    //);

    //}elseif(@$suppVerifcationData->aicenter_rejected_marksheet_document == 2 || @$suppVerifcationData->department_rejected_marksheet_document == 2){
    // $custom_data = array(
    //'marksheet_doc'=>$supplementaryDetails->supp_doc,
    //);

    //}elseif(@$suppVerifcationData->aicenter_rejected_marksheet_document == 1 || @$suppVerifcationData-> department_rejected_marksheet_document == 1){
    //if($course == 10){
    //$custom_data = array(
    //'marksheet_doc'=>$supplementaryDetails->supp_doc,
    //);
    //}
    //if($course == 12){
    //$custom_data = array(
    //'sec_marksheet_doc'=>$supplementaryDetails->sec_marksheet_doc,
    //);
    //}
    //}


    /*if(!empty($custom_data)){

			$isValid = true;

			if(!empty($custom_data['marksheet_doc'])){
				$basePathStudentDocumentPathOld = $student_document_path[2].$current_folder_year.'/'.$exam_month.'/'.$student_id.'/';
				$basePathStudentDocumentPathNew = $student_document_path[1].$current_folder_year.'/'.$exam_month.'/'.$student_id.'/';

				$basePathStudentDocumentPathOld = $basePathStudentDocumentPathOld . $custom_data['marksheet_doc'];

				$folder_path = public_path($basePathStudentDocumentPathNew);

				File::makeDirectory($folder_path, $mode = 0777, true, true);

				$basePathStudentDocumentPathNew = $basePathStudentDocumentPathNew . $custom_data['marksheet_doc'];

				$new_path = public_path($basePathStudentDocumentPathNew);
				$old_path = public_path($basePathStudentDocumentPathOld);

				if(file_exists($old_path)){
					$move = File::copy($old_path, $new_path);
				}else{
					$isValid = false;
				}
			}
			if(!empty($custom_data['sec_marksheet_doc'])){
				$basePathStudentDocumentPathOld = $student_document_path[2].$current_folder_year.'/'.$exam_month.'/'.$student_id.'/';
				$basePathStudentDocumentPathNew = $student_document_path[1].$current_folder_year.'/'.$exam_month.'/'.$student_id.'/';
				$basePathStudentDocumentPathOld = $basePathStudentDocumentPathOld . $custom_data['sec_marksheet_doc'];
				$folder_path = public_path($basePathStudentDocumentPathNew);
				File::makeDirectory($folder_path, $mode = 0777, true, true);
				$basePathStudentDocumentPathNew = $basePathStudentDocumentPathNew . $custom_data['sec_marksheet_doc'];
				$new_path = public_path($basePathStudentDocumentPathNew);
				$old_path = public_path($basePathStudentDocumentPathOld);

				if(file_exists($old_path)){
					$move = File::copy($old_path, $new_path);
				}else{
					$isValid = false;
				}
			}
			if($isValid){
				$supplementariesupdatedata = DB::table('supplementaries')->where('id',@$supp_id)->where('student_id',$student_id)->update($custom_data);
			}
		}
		return true;
	}
}*/


    public function oldhimmat()
    {
        return view('landing.barcode-jquery');
        die;

        $toc_coditions['toc.exam_year'] = 124;
        $toc_coditions['toc.stream'] = 2;
        $toc_data = DB::table('toc')->select('id', 'student_id')->where($toc_coditions)->whereNull('deleted_at')->get();
        @dd($toc_data);
        $row_update = 0;
        if (!empty($toc_data)) {
            foreach ($toc_data as $toc) {
                $toc_id = $toc->id;
                $toc_board = $toc->board;

                $toc_marks_coditions['toc_marks.toc_id'] = $toc_id;
                $toc_marks_data = DB::table('toc_marks')->where($toc_marks_coditions)->whereNull('deleted_at')->get();

                $toc_custom_data = array();
                foreach ($toc_marks_data as $toc_marks) {
                    if (isset($each['subject_id']) && !empty($each['subject_id'])) {
                        $toc_custom_data[$key]['conv_practical'] = $this->_getCONVTocMarkPractical($toc_board, $toc_marks['subject_id'], $toc_marks['practical']);
                        $toc_custom_data[$key]['conv_theory'] = $this->_getCONVTocMarkTheory($toc_board, $toc_marks['subject_id'], $toc_marks['theory'], $toc_custom_data[$key]['conv_practical'], $toc_marks['practical']);
                        $toc_custom_data[$key]['conv_total_marks'] = ($toc_custom_data[$key]['conv_theory'] + $toc_custom_data[$key]['conv_practical']);
                    }
                }
                TocMark::where('id', '=', $toc_id)->update($toc_custom_data);
                $row_update++;
                echo "Updated TOC Row Count : " . $row_update;
                echo "</br>";
            }
        }
        echo "</br> Screipt End ";
        die;
    }

    /*
	public function rahul(){
		$enrollment = '27016223001';

		$imagepath = asset('public/barcode/'.$enrollment.'.png');
		$custom_component_obj = new CustomComponent;
		$barcode = $custom_component_obj->barcode($enrollment);
		echo '<img src="'.$imagepath.'" alt=barcode-'.$enrollment.' style="font-size:0;position:relative;width:132px;height:20px;" >';
		die;
	}
	*/

    public function phpBarcode()
    {
        // if(isset($_POST['generate_barcode'])){

        // $text = (isset($_GET["generate_barcode"])?$_GET["generate_barcode"]:"0");
        $text = '27016223001';
        $filepath = "";
        $size = 50;
        $orientation = "horizontal";
        $code_type = "code128";
        $print = false;
        $sizefactor = 1;

        // This function call can be copied into your project and can be made from anywhere in your code

        $custom_component_obj = new CustomComponent;
        $barcode = $custom_component_obj->barcode($filepath, $text, $size, $orientation, $code_type, $print, $sizefactor);
        echo $barcode;
        die;
        // }
        return view('landing.barcode');
    }

    public function jqueryBarcode()
    {
        return view('landing.barcode-jquery');
        die;

        $toc_coditions['toc.exam_year'] = 124;
        $toc_coditions['toc.stream'] = 2;
        $toc_data = DB::table('toc')->select('id', 'student_id')->where($toc_coditions)->whereNull('deleted_at')->get();
        @dd($toc_data);
        $row_update = 0;
        if (!empty($toc_data)) {
            foreach ($toc_data as $toc) {
                $toc_id = $toc->id;
                $toc_board = $toc->board;

                $toc_marks_coditions['toc_marks.toc_id'] = $toc_id;
                $toc_marks_data = DB::table('toc_marks')->where($toc_marks_coditions)->whereNull('deleted_at')->get();

                $toc_custom_data = array();
                foreach ($toc_marks_data as $toc_marks) {
                    if (isset($each['subject_id']) && !empty($each['subject_id'])) {
                        $toc_custom_data[$key]['conv_practical'] = $this->_getCONVTocMarkPractical($toc_board, $toc_marks['subject_id'], $toc_marks['practical']);
                        $toc_custom_data[$key]['conv_theory'] = $this->_getCONVTocMarkTheory($toc_board, $toc_marks['subject_id'], $toc_marks['theory'], $toc_custom_data[$key]['conv_practical'], $toc_marks['practical']);
                        $toc_custom_data[$key]['conv_total_marks'] = ($toc_custom_data[$key]['conv_theory'] + $toc_custom_data[$key]['conv_practical']);
                    }
                }
                TocMark::where('id', '=', $toc_id)->update($toc_custom_data);
                $row_update++;
                echo "Updated TOC Row Count : " . $row_update;
                echo "</br>";
            }
        }
        echo "</br> Screipt End ";
        die;
    }

    public function tocWrongConversionCount()
    {
        $toc_coditions['toc.exam_year'] = 125;
        $toc_coditions['toc.exam_month'] = 1;
        $toc_data = DB::table('toc')->select('id', 'student_id', 'board')->where($toc_coditions)->whereNull('deleted_at')->get();

        $wrong_toc = 0;
        $student_arr = array();
        if (!empty($toc_data)) {
            foreach (@$toc_data as $toc) {
                $toc_id = @$toc->id;
                $toc_board = @$toc->board;

                if (!empty($toc_id)) {
                    $toc_marks_coditions['toc_marks.toc_id'] = $toc_id;
                    $toc_marks_data = DB::table('toc_marks')->where($toc_marks_coditions)->get();
                    // @dd($toc_marks_data);

                    if (!empty($toc_marks_data)) {
                        foreach ($toc_marks_data as $toc_marks) {


                            if (!empty(@$toc_marks->subject_id)) {

                                $theoryMaxMarks = DB::table('toc_subject_masters')
                                    ->where('board_id', '=', $toc_board)
                                    ->where('subject_id', '=', @$toc_marks->subject_id)
                                    ->where('type', 'like', "%TH_MAX%")->first();
                                //->where('type','=',"TH_MAX")->first();

                                $practicalMaxMarks = DB::table('toc_subject_masters')
                                    ->where('board_id', '=', $toc_board)
                                    ->where('subject_id', '=', @$toc_marks->subject_id)
                                    ->where('type', 'like', "%PR_MAX%")->first();

                                if (!empty($theoryMaxMarks->value) && !empty($practicalMaxMarks->value) && (@$toc_marks->theory > @$theoryMaxMarks->value) || (@$toc_marks->practical > @$practicalMaxMarks->value)) {

                                    if (!in_array($toc_marks->student_id, $student_arr)) {
                                        echo "MaxT : " . @$theoryMaxMarks->value . " theory : " . @$toc_marks->theory . ", MaxP : " . @$practicalMaxMarks->value . "  Practical : " . @$toc_marks->practical . "board_id :" . @$toc_board . "subject_id :" . @$toc_marks->subject_id;
                                        $student_arr[] = $toc_marks->student_id;
                                        echo "  student id : " . @$toc_marks->student_id . "</br>";
                                        @$wrong_toc++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        echo "Wrong TOC Count : " . $wrong_toc . "</br>";
        echo "</br> Screipt End ";
        die;
    }

    public function tocConversionMarksUpdate()
    {
        echo "test";
        die;
        $toc_coditions['toc.exam_year'] = 124;
        $toc_coditions['toc.exam_month'] = 2;
        $toc_data = DB::table('toc')->select('id', 'student_id', 'board')->where($toc_coditions)->whereNull('deleted_at')->get();

        $row_update = 0;
        if (!empty($toc_data)) {
            foreach (@$toc_data as $toc) {
                $toc_id = @$toc->id;
                $toc_board = @$toc->board;

                if (!empty($toc_id)) {
                    $toc_marks_coditions['toc_marks.toc_id'] = $toc_id;
                    $toc_marks_data = DB::table('toc_marks')->where($toc_marks_coditions)->get();
                    // @dd($toc_marks_data);

                    if (!empty($toc_marks_data)) {
                        foreach ($toc_marks_data as $toc_marks) {
                            if (!empty(@$toc_marks->subject_id)) {
                                $toc_custom_data = array();
                                $toc_custom_data['conv_practical'] = $this->_getCONVTocMarkPractical($toc_board, $toc_marks->subject_id, $toc_marks->practical);
                                $toc_custom_data['conv_theory'] = $this->_getCONVTocMarkTheory($toc_board, $toc_marks->subject_id, $toc_marks->theory, $toc_custom_data['conv_practical'], $toc_marks->practical);
                                $toc_custom_data['conv_total_marks'] = ($toc_custom_data['conv_theory'] + $toc_custom_data['conv_practical']);
                                $row_update++;

                                // echo "<pre>"; print_r($toc_custom_data); echo "</pre>";
                                TocMark::where('id', '=', $toc_marks->id)->update($toc_custom_data);
                                echo "Updated TOC Row Count : " . $row_update;
                                echo "</br>";
                            }
                        }
                    }
                }
            }
        }
        echo "</br> Screipt End ";
        die;
    }

    public function himmat2()
    {
        $toc_coditions['toc.exam_year'] = 124;
        $toc_coditions['toc.stream'] = 2;
        $toc_data = DB::table('toc')->select('id', 'student_id')->where($toc_coditions)->whereNull('deleted_at')->get();
        @dd($toc_data);
        $row_update = 0;
        if (!empty($toc_data)) {
            foreach ($toc_data as $toc) {
                $toc_id = $toc->id;
                $toc_board = $toc->board;

                $toc_marks_coditions['toc_marks.toc_id'] = $toc_id;
                $toc_marks_data = DB::table('toc_marks')->where($toc_marks_coditions)->whereNull('deleted_at')->get();

                $toc_custom_data = array();
                foreach ($toc_marks_data as $toc_marks) {
                    if (isset($each['subject_id']) && !empty($each['subject_id'])) {
                        $toc_custom_data[$key]['conv_practical'] = $this->_getCONVTocMarkPractical($toc_board, $toc_marks['subject_id'], $toc_marks['practical']);
                        $toc_custom_data[$key]['conv_theory'] = $this->_getCONVTocMarkTheory($toc_board, $toc_marks['subject_id'], $toc_marks['theory'], $toc_custom_data[$key]['conv_practical'], $toc_marks['practical']);
                        $toc_custom_data[$key]['conv_total_marks'] = ($toc_custom_data[$key]['conv_theory'] + $toc_custom_data[$key]['conv_practical']);
                    }
                }
                TocMark::where('id', '=', $toc_id)->update($toc_custom_data);
                $row_update++;
                echo "Updated TOC Row Count : " . $row_update;
                echo "</br>";
            }
        }
        echo "</br> Screipt End ";
        die;
    }

    public function back_to_sso()
    {
        $url = $BACK_TO_SSO_URL = Config::get("global.BACK_TO_SSO_URL");
        $token = Session::get('userdetils');
        echo '<form action="' . $url . '" method="POST"   id="myFormBackSSO" style="display:none;">
		@csrf
		<input type="hidden" name="userdetails" value="' . $token . '"><script type="text/javascript">document.getElementById("myFormBackSSO").submit();</script>
		</form>';
        exit;
    }


    public function help_desk()
    {
        $links = array();
        return view('landing.help_desk', compact('links'));
    }


    public function generateEnrollmentNumber($stream = null, $course = null, $aicode = null)
    {
        $enrollmentno = $this->_generateEnrollment($stream, $course, $aicode);
        echo "Stream -> " . $stream;
        echo "<br>";
        echo "course -> " . $course;
        echo "<br>";
        echo "aicode -> " . $aicode;
        echo "<br>";
        echo "Enrollmentno -> " . $enrollmentno;
    }


    public function my_mac_address(Request $request)
    {
        // $status = $this->_isAllowMacAddress();
        // if($status){
        // 	echo "Valid";
        // }else{
        // 	echo "Not Valid";
        // }
        //  echo "<br>";
        $myMacAddr = $this->_my_mac_address();
        echo "My Mac Address : <br>" . $myMacAddr;
        die;
    }

    public function oldrohittwo(Request $request)
    {

        $inputs = array();
        $inputs['entitlementId'] = "100221129050919987";
        $inputs['entitlementMemId'] = "100221129050919987";
        $inputs['janaadhaarId'] = "4736270700";
        $inputs['janaadhaarMemId'] = "33576964970";
        $inputs['transactionId'] = "100221129050919987";
        $inputs['dueTransactionId'] = "100221129050919987";
        $inputs['aadharNo'] = "332604223449";
        $inputs['paymentAmount'] = "450";
        $inputs['paymentDate'] = "11/10/2023";

        $response = $this->_sendTransationDetailsForDBTToJanAadhar($inputs);
        dd($response);

        //$status = $this->_sendOTPToStudent(587397);
        // $status = $this->_sendTestSMS(587397);
        $status = $this->_sendOTPToStudent(587397);
        //587412 aashish number
        //587397 rohit number
        //589826 vijay sir

        dd($status);
        // $status = $this->_verifyStudentOTP(587397,276815,"rrrr");
        $str = "PRN=125Z844106Z110426::REQTIMESTAMP=20231222150341000::AMOUNT=1325::RECEIPTNO=23000270520::TRANSACTIONID=230000279616::PAIDAMOUNT=1335.00::EMITRATIMESTAMP=20231222150341156::RPPTXNID=336415::RPPTIMESTAMP=20231222150347154::PAYMENTMODE=Rajasthan Payment PlatForm::PAYMENTMODEBID=336415_20231222150347544::RESPONSECODE=200::RESPONSEMESSAGE=Transaction successful::UDF1=844106::UDF2=9887620388::CHECKSUM=883b53c14004de6fe24a897131044074b6b91ee1bad3dcbdd025582ed79316a5";
        $str = explode("UDF2=", $str);
        $str = substr(@$str[1], 0, 2);
        dd($str);


        $notValidStudents = $this->getNotValidAfterDatePaymentRecivedStudentIds();
        dd($d);
        $MarksheetCustomComponent = new MarksheetCustomComponent;
        $MarksheetCustomComponent->testCaseSubjectAndFinalResultChecking();
        echo "Done";
        die;
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        DB::statement("truncate table rs_bank_masters;");

        $pathBankMaster = $path = public_path("response.json");    //response
        $jsonBankMaster = file_get_contents($pathBankMaster);
        $json_dataBankMaster = json_decode($jsonBankMaster, true);
        $bankName = array();
        $bankHindiName = array();
        if ($json_dataBankMaster['IsSuccess']) {
            if ($json_dataBankMaster['TotalRecordsCount'] > 0) {
                $totalDataArray = count($json_dataBankMaster['Data']);
                if ($totalDataArray > 0) {
                    foreach ($json_dataBankMaster['Data'] as $k => $v) {
                        $bankName[$v['BANK_ID']] = $v['BANK_NAME'];
                        $bankHindiName[$v['BANK_ID']] = $v['BANKNAME_MANGAL'];
                    }
                }
            }
        }

        $path = $path = public_path("full_bank_response.json"); //bank_response full_bank_response
        $json = file_get_contents($path);
        $json_data = json_decode($json, true);
        if ($json_data['IsSuccess']) {
            if ($json_data['TotalRecordsCount'] > 0) {
                $totalDataArray = count($json_data['Data']);

                if ($totalDataArray > 0) {
                    $coutner = 0;
                    foreach ($json_data['Data'] as $k => $v) {
                        $coutner++;
                        $dataSave = array();
                        $fld = 'name';
                        $dataSave[$fld] = @$bankName[$v['BANK_ID']];
                        $fld = 'BANK_NAME';
                        $dataSave[$fld] = @$bankName[$v['BANK_ID']];
                        $fld = 'BANKNAME_MANGAL';
                        $dataSave[$fld] = @$bankHindiName[$v['BANK_ID']];
                        $fld = 'BANK_BRANCH_ID';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'BRANCH_ADDRESS';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'IFSC_CODE';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'MICR';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'CREATION_DATE';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'MODIFICATION_DATE';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'VERSION';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'IS_ACTIVE';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'IS_DELETED';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'BANK_ID';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'REMARKS';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'STATE_ID';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'BRANCH';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'BRANCH_MANGAL';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'PARENT_BANKBRANCH_ID';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'MERGE_DATE';
                        $dataSave[$fld] = @$v[$fld];
                        BankMaster::create($dataSave);
                        echo $coutner . "<br>";
                    }
                }
            }
        }
        echo "Done";
        die;
    }


    public function rajMasterDataJson(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        DB::statement("truncate table rs_bank_masters;");
        $pathBankMaster = $path = public_path("response.json");    //response
        $jsonBankMaster = file_get_contents($pathBankMaster);
        $json_dataBankMaster = json_decode($jsonBankMaster, true);
        $bankName = array();
        $bankHindiName = array();
        if ($json_dataBankMaster['IsSuccess']) {
            if ($json_dataBankMaster['TotalRecordsCount'] > 0) {
                $totalDataArray = count($json_dataBankMaster['Data']);
                if ($totalDataArray > 0) {
                    foreach ($json_dataBankMaster['Data'] as $k => $v) {
                        $bankName[$v['BANK_ID']] = $v['BANK_NAME'];
                        $bankHindiName[$v['BANK_ID']] = $v['BANKNAME_MANGAL'];
                    }
                }
            }
        }

        $path = $path = public_path("full_bank_response.json"); //bank_response full_bank_response
        $json = file_get_contents($path);
        $json_data = json_decode($json, true);
        if ($json_data['IsSuccess']) {
            if ($json_data['TotalRecordsCount'] > 0) {
                $totalDataArray = count($json_data['Data']);

                if ($totalDataArray > 0) {
                    $coutner = 0;
                    foreach ($json_data['Data'] as $k => $v) {
                        $coutner++;
                        $dataSave = array();
                        $fld = 'name';
                        $dataSave[$fld] = @$bankName[$v['BANK_ID']];
                        $fld = 'BANK_NAME';
                        $dataSave[$fld] = @$bankName[$v['BANK_ID']];
                        $fld = 'BANKNAME_MANGAL';
                        $dataSave[$fld] = @$bankHindiName[$v['BANK_ID']];
                        $fld = 'BANK_BRANCH_ID';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'BRANCH_ADDRESS';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'IFSC_CODE';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'MICR';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'CREATION_DATE';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'MODIFICATION_DATE';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'VERSION';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'IS_ACTIVE';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'IS_DELETED';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'BANK_ID';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'REMARKS';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'STATE_ID';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'BRANCH';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'BRANCH_MANGAL';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'PARENT_BANKBRANCH_ID';
                        $dataSave[$fld] = @$v[$fld];
                        $fld = 'MERGE_DATE';
                        $dataSave[$fld] = @$v[$fld];
                        BankMaster::create($dataSave);
                        echo $coutner . "<br>";
                    }
                }
            }
        }
        echo "Done";
        die;
    }

    public function oldrohit(Request $request)
    {

        $mobile = "8946919241";
        $templateID = "1107170195073312967";
        $sms = 'अभ्यार्थी संदेश प्राप्त होने के 3 दिवस के अंदर संदर्भ केंद्र पर जाकर अनिवार्य रूप से अपनी SSOID अपडेट करावे अन्यथा वे परीक्षा से वंचित रह सकते हैं।-RSOS,GoR';
        return $this->_sendSMS($mobile, $sms, $templateID);

        //$status = $this->_sendOTPToStudent(587397);
        // $status = $this->_verifyStudentOTP(587397,276815,"rrrr");


        // $student_id = 596572;
        // echo "1";
        // $this->_sendSupplementaryLockSubmittedMessage($student_id);
        // echo "2";
        // $this->_sendPaymentSubmitMessage($student_id);
        // die;

        $course = '12';
        $roll_number = '2608607';
        $year = '2023';
        $response = $this->_getBoardResult($course, $roll_number, $year);
        print_r($response);
        die;
        if (str_contains(@$_SERVER['HTTP_USER_AGENT'], "Android")) {
            echo "<center>Your request will be responded soon....you are in queque..please wait......</center>";
            die;
        }
        $ip = Config::get('global.CURRENT_IP');
        echo "My Ip : " . $ip;
        die;
        $getdata = DB::table('students')
            ->join('student_fees', 'student_fees.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')->
            where('students.exam_year', 125)->where('applications.locksumbitted', 1)->where('student_fees.total', '>', 0)->whereNull('students.challan_tid')->whereNull('students.enrollment')->
            whereNull('students.deleted_at')->whereNull('student_fees.deleted_at')->whereNull('applications.deleted_at')->count();

        dd($getdata);
    }

    public function studentWhoYetNotFeePaid(Request $request)
    {
        $form_current_admission_session_id = Config::get("global.form_current_admission_session_id");
        $students = DB::table('students')
            ->join('student_fees', 'student_fees.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')->
            where('students.exam_year', $form_current_admission_session_id)->where('applications.locksumbitted', 1)->where('student_fees.total', '>', 0)->whereNull('students.challan_tid')->whereNull('students.enrollment')->
            whereNull('students.deleted_at')->whereNull('student_fees.deleted_at')->whereNull('applications.deleted_at')->pluck('students.mobile', 'students.id');
        $counter = 0;

        foreach ($students as $student_id => $mobile) {
            $counter++;
            $smsStatus = $this->_sendLockSubmittedMessage($student_id);
            echo "<br>" . $counter . " Mobile " . $mobile;
        }
        echo "<br>" . "Total : " . $counter;
        die;
    }

    public function oldrightstudentWhoYetNotMappedSSO(Request $request)
    {
        $form_current_admission_session_id = Config::get("global.form_current_admission_session_id");
        $students = DB::table('students')
            ->join('student_fees', 'student_fees.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')->
            where('students.exam_year', $form_current_admission_session_id)->where('applications.locksumbitted', 1)->where('student_fees.total', '>', 0)->whereNull('students.challan_tid')->whereNull('students.enrollment')->
            whereNull('students.deleted_at')->whereNull('students.ssoid')->whereNull('student_fees.deleted_at')->whereNull('applications.deleted_at')->pluck('students.mobile', 'students.id');
        $counter = 0;

        foreach ($students as $student_id => $mobile) {
            $counter++;
            $smsStatus = $this->_sendSSOMappMessage($student_id);
            echo "<br>" . $counter . " Mobile " . $mobile;
        }
        echo "<br>" . "Total : " . $counter;
        die;
    }

    public function studentWhoYetNotMappedSSO(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", -1);
        //587412 aashish number
        //587397 rohit number
        //589826 vijay sir

        $form_current_admission_session_id = Config::get("global.form_current_admission_session_id");
        $students = DB::table('students')->join('applications', 'applications.student_id', '=', 'students.id')->where('students.exam_year', $form_current_admission_session_id)->where('students.exam_month', 1)->where('applications.locksumbitted', 1)->where('students.is_eligible', 1)->whereNull('students.deleted_at')->whereNull('students.ssoid')->whereNull('applications.deleted_at')->pluck('students.mobile', 'students.id');

        $counter = 0;

        foreach ($students as $student_id => $mobile) {
            $counter++;
            $smsStatus = $this->_sendSSOMappMessage($student_id);
            echo "<br>" . $counter . " Mobile " . $mobile;
        }
        echo "<br>" . "Total : " . $counter;
        die;
    }

    public function studentWhoYetNotFeePaidYearExamMonthWise(Request $request, $exam_year = null, $exam_month = null)
    {
        if (@$exam_year && @$exam_month) {
            $students = DB::table('students')
                ->join('student_fees', 'student_fees.student_id', '=', 'students.id')->join('applications', 'applications.student_id', '=', 'students.id')->
                where('students.exam_year', $exam_year)->where('students.exam_month', $exam_month)->where('applications.locksumbitted', 1)->where('student_fees.total', '>', 0)->whereNull('students.challan_tid')->whereNull('students.enrollment')->
                whereNull('students.deleted_at')->whereNull('student_fees.deleted_at')->whereNull('applications.deleted_at')->pluck('students.mobile', 'students.id');
            $counter = 0;

            foreach ($students as $student_id => $mobile) {
                $counter++;
                $smsStatus = $this->_sendLockSubmittedMessage($student_id);
                echo "<br>" . $counter . " Mobile " . $mobile;
            }
            echo "<br>" . "Total : " . $counter;
        } else {
            echo "Invalid Year and Month";
        }
        die;
    }


    public function lokesh(Request $request)
    {
        $aicode_conditions['student_allotments.exam_year'] = 124;
        $aicode_conditions['student_allotments.stream'] = 2;
        $master = DB::table('student_allotments')->select('DISTINCT `StudentAllotment`.`ai_code`', DB::raw('count(rs_student_allotments.enrollment) as student_count'))
            ->where($aicode_conditions)->groupBy('student_allotments.ai_code')->orderBy('DESC')->get();
        dd($master);
        //$aicodearr = $this->StudentAllotment->find('all',array('fields'=>array('DISTINCT `StudentAllotment`.`ai_code`','COUNT(enrollment)'),'conditions'=>$aicode_conditions,'group'=>'ai_code','order'=>'COUNT(enrollment) DESC'));
    }

    public function _callCurl($url, $data = false, $method = '')
    {
        $curl = curl_init();
        $url = $url;
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // curl_setopt($curl, CURLOPT_USERPWD, "madarsa.test:Test@1234");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }








    // 	public function rahul(){
    // 	ini_set('memory_limit', '3000M');
    // 	ini_set('max_execution_time', '0');

    // 		$centercodeandids = ExamcenterDetail::where('exam_year',124)->where('stream',2)->limit('1')->pluck('ecenter10','id')->toArray();

    // 		$centercodeandNames = ExamcenterDetail::where('exam_year',124)->where('stream',2)->pluck('cent_name','id')->toArray();
    //             $subjectList = Subject::where('course',10)->orderBy('subject_code','ASC')->pluck('name','id');

    //               $subjectList1 = Subject::where('course',10)->orderBy('id','ASC')->pluck('subject_code','id');

    //            foreach($centercodeandids  as $centerid => $centervalue){

    // 		$final_data = array();
    // 		$i = 0;
    // 		foreach($subjectList  as $subjectid => $subjectname){

    //            $conditions["student_allotments.exam_year"] = 124;
    // 		$conditions["student_allotments.stream"] = 2;
    // 		$conditions["student_allotments.course"] = 10;
    // 		$conditions["student_allotments.examcenter_detail_id"] = $centerid;
    // 		$conditions['exam_subjects.subject_id'] = $subjectid;
    // 		$conditions['student_allotments.supplementary'] = 0;


    // 		$supp_conditions["student_allotments.exam_year"] = 124;
    // 		$supp_conditions["student_allotments.stream"] = 2;
    // 		$supp_conditions["student_allotments.course"] = 10;
    // 		$supp_conditions["student_allotments.examcenter_detail_id"] = $centerid;
    // 		$supp_conditions['supplementary_subjects.subject_id'] = $subjectid;
    // 		$supp_conditions['student_allotments.supplementary'] = 1;

    // 		$studentData = array();
    // 				$studentData = DB::table('student_allotments')->select('student_allotments.enrollment','student_allotments.student_id','students.name','examcenter_details.ecenter10','examcenter_details.ecenter12','examcenter_details.cent_name')->join('exam_subjects', 'exam_subjects.student_id', '=', 'student_allotments.student_id')
    // 					->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
    // 					->join("students",function($join){
    // 						$join->on("student_allotments.student_id" , "=" ,"students.id");
    // 						$join->whereRaw("(rs_students.is_eligible = 1)");
    // 					})->where($conditions)->count();

    // 				$SuppStudentData = array();
    // 				$SuppStudentData = DB::table('student_allotments')->select('student_allotments.enrollment','student_allotments.student_id','students.name','examcenter_details.ecenter10','examcenter_details.ecenter12','examcenter_details.cent_name')
    // 					->join('supplementary_subjects', 'supplementary_subjects.student_id', '=', 'student_allotments.student_id')
    // 					->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
    // 					->join('students', 'students.id', '=', 'student_allotments.student_id')
    // 					->join("supplementaries",function($join){
    // 						$join->on("supplementaries.student_id" , "=" ,"student_allotments.student_id");
    // 						$join->whereRaw("(rs_supplementaries.submitted IS NOT NULL AND rs_supplementaries.challan_tid IS NOT NULL  AND rs_supplementaries.fee_status = 1 AND rs_supplementaries.locksumbitted = 1 )");
    // 					})->where($supp_conditions)->count();

    // 				$data = $studentData + $SuppStudentData;
    // 				$final_data[$i]['subject_id']=$subjectid;
    // 				$final_data[$i]['studentData']=$data;
    // 				$final_data[$i]['cent_code']=$centervalue;
    // 				$final_data[$i]['cent_name']=@$centercodeandNames[@$centerid];
    // 				$i++;


    // 	}

    // }

    // 	}

    // public function examcentersubjectscount($course= null){
    // 	$examcenter_exl_data = (new CenterCountExlExport($course));
    //        return Excel::download($examcenter_exl_data, 'examcentersubjectscount.xlsx');
    // }

    // public function aicentersubjectsexport($course= null){
    // 	$examcenter_exl_data = (new AicentersubjectCountExlExport($course));
    //        return Excel::download($examcenter_exl_data, 'Aicentersubjectscount.xlsx');
    // }


    // 	public function hall_ticket_bulk_download($course=null,$district_id=null){
    // 	$title = "Hall Ticket Report";
    // 	$stream = Config::get("global.defaultStreamId");
    // 	$custom_component_obj = new CustomComponent;
    // 	if(@$district_id){
    // 		$aiCenters = $custom_component_obj->getAiCentersByDistrictId($district_id);
    // 	}else{
    // 		$aiCenters = $custom_component_obj->getAiCenters();
    // 	}

    // 	$subject_list =  $this->subjectCodeList();
    // 	$combo_name = 'categorya';$categorya = $this->master_details($combo_name);
    // 	$combo_name = 'current_folder_year';$current_folder_year = $this->master_details($combo_name);
    // 	$subreportname = "Hall Ticket";
    // 	$aicentermaterial ="aicentermaterial";
    // 	$aicode = [];

    // 	$subjects = DB::table('subjects')->where(array('deleted'=>0))->orderBy('subject_code')->pluck('subject_code','id');
    // 	$subjects10 = DB::table('subjects')->where(array('deleted'=>0))->where(array('course'=>10))->orderBy('subject_code')->pluck('name','id');
    // 	$subjects12 = DB::table('subjects')->where(array('deleted'=>0))->where(array('course'=>12))->orderBy('subject_code')->pluck('name','id');
    // 	$practicalsubjects12 = DB::table('subjects')->where(array('deleted'=>0))->where(array('practical_type'=>1))->orderBy('subject_code')
    // 	->pluck('subject_code','id')->toArray();

    // 	foreach($aiCenters  as $key => $value){
    // 		$aicodetemp = $key;
    // 		$ai_code = $aicode  =  $key;
    // 		$districtnmae = District::pluck('name','id');

    // 		$aicodedistrictid = User::where('ai_code',$aicode)->groupBy('district_id')->get('district_id')->toarray();
    // 		$aicodedistrictid1 = $districtnmae[$aicodedistrictid['0']['district_id']];
    // 		$reportname = $aicodetemp;

    // 		/* Master data get start */
    // 			$conditions = array();
    // 			$conditionSupplementary = array();
    // 			if(isset($ai_code) && !empty($ai_code)){
    // 				$conditions []= ['students.ai_code', '=', $ai_code];
    // 				$conditions []= ['students.is_eligible', '=', 1];
    // 				$conditions []= ['students.exam_year', '=', Config::get('global.current_admission_session_id')];
    // 				$conditions []= ['students.stream', '=', $stream];
    // 				if(isset($course) && !empty($course)){
    // 					$conditions['students.course'] = $course;
    // 					$conditionSupplementary[] = ['supplementaries.course', '=', $course];
    // 				}
    // 				$aiCenterDetail = User::where('ai_code',$ai_code)->first();
    // 			} else {
    // 				return redirect()->route('hall_ticket_form')->with('error', 'Oop"s! Did you really think you are allowed to see that?');
    // 			}


    // 			/* Suplementary Data Start  */
    // 			$conditionSupplementary []= ['supplementaries.ai_code', '=', $ai_code];
    // 			$conditionSupplementary []= ['supplementaries.exam_year', '=', config::get('global.admission_academicyear_id')];
    // 			$conditionSupplementary []= ['supplementaries.exam_month', '=', config::get('global.current_exam_month_id')];
    // 			$conditionSupplementary []= ['supplementaries.submitted','!=', 'NULL'];
    // 			$conditionSupplementary []= ['supplementaries.fee_status', '=', 1];


    // 			$suppStudents = array();
    // 			$suppStudents = Supplementary::select('supplementaries.*','students.*','students.dob',
    // 				'students.course','applications.category_a',
    // 				'examcenter_details.id','examcenter_details.ecenter10','examcenter_details.ecenter12',
    // 				'examcenter_details.cent_name','examcenter_details.cent_add1','examcenter_details.cent_add2',
    // 				'examcenter_details.cent_add3',
    // 				'addresses.district_name','addresses.tehsil_name',
    // 				'documents.id','documents.student_id','documents.photograph','documents.signature')
    // 				->join('students', 'students.id', '=', 'supplementaries.student_id')
    // 				->join('documents', 'documents.student_id', '=', 'students.id')
    // 				->join('addresses', 'addresses.student_id', '=', 'students.id')
    // 				->join('applications', 'applications.student_id', '=', 'students.id')
    // 				->join('student_allotments', 'student_allotments.student_id', '=', 'supplementaries.student_id')
    // 				->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
    // 				->where($conditionSupplementary)
    // 				->orderBy('supplementaries.student_id','ASC')
    // 				->groupBy('students.id')
    // 				->get();
    // 			/* Suplementary Data End */

    // 			/* Student  Data Start */
    // 				$students = array();
    // 				$students = Student::select(
    // 					'students.id','students.ai_code','students.enrollment','students.name','students.father_name',
    // 					'students.mother_name','students.mobile','students.name_hi','students.stream',
    // 					'applications.category_a','students.dob','students.course',
    // 					'addresses.district_name','addresses.tehsil_name',
    // 					'documents.photograph','documents.signature',
    // 					'examcenter_details.ecenter10','examcenter_details.ecenter12',
    // 					'examcenter_details.cent_name','examcenter_details.cent_add1','examcenter_details.cent_add2',
    // 					'examcenter_details.cent_add3',

    // 				)
    // 				->join('applications', 'applications.student_id', '=', 'students.id')
    // 				->join('documents', 'documents.student_id', '=', 'students.id')
    // 				->join('addresses', 'addresses.student_id', '=', 'students.id')
    // 				->join('student_allotments', 'student_allotments.student_id', '=', 'students.id')
    // 				->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
    // 				->where($conditions)
    // 				->groupBy('students.id')
    // 				->orderBy('students.id','ASC')->get();
    // 			/* Student  Data End */
    // 		// @dd($students);
    // 		/* Master data get end */

    // 		/* Supplementray Data Set in Array End */
    // 			$dataSave = array();
    // 			$key = 0;

    // 			if(isset($suppStudents) && !empty($suppStudents)){
    // 				foreach($suppStudents as $suppKey => $suppStudent){
    // 					$dataSave[$suppStudent->course][$key]['index'] = $suppKey;

    // 					$ai_code = @$suppStudent->ai_code;
    // 					$dataSave[$suppStudent->course][$key]['type'] = 'Supplementary';

    // 					$dataSave[$suppStudent->course][$key]['id'] = $suppStudent->id;
    // 					$dataSave[$suppStudent->course][$key]['student_id'] = $suppStudent->student_id;
    // 					$dataSave[$suppStudent->course][$key]['ai_code'] = $suppStudent->ai_code;
    // 					$dataSave[$suppStudent->course][$key]['enrollment'] = $suppStudent->enrollment;

    // 					$dataSave[$suppStudent->course][$key]['name'] = $suppStudent->name;
    // 					$dataSave[$suppStudent->course][$key]['father_name'] = $suppStudent->father_name;
    // 					$dataSave[$suppStudent->course][$key]['mother_name'] = $suppStudent->mother_name;
    // 					//pr($suppStudent);die;
    // 					$dataSave[$suppStudent->course][$key]['category_a'] = $suppStudent->category_a;

    // 					if(isset($suppStudent->course) && !empty($suppStudent->course)){
    // 						$dataSave[$suppStudent->course][$key]['course'] = $suppStudent->course;
    // 					}else{
    // 						$dataSave[$suppStudent->course][$key]['course'] = '';
    // 					}
    // 					if(isset($suppStudent->dob) && !empty($suppStudent->dob)){
    // 						if(strpos($suppStudent->dob,'-')){
    // 							$ndobarr = explode('-',$suppStudent->dob);
    // 							$ndob = $ndobarr[2]."/".$ndobarr[1]."/".$ndobarr[0];
    // 							$dataSave[$suppStudent->course][$key]['dob'] = $ndob;
    // 						}else{
    // 							$dataSave[$suppStudent->course][$key]['dob'] = $suppStudent->dob;
    // 						}
    // 					}else{
    // 						$dataSave[$suppStudent->course][$key]['dob'] = '';
    // 					}
    // 					$dataSave[$suppStudent->course][$key]['stream'] = $suppStudent->stream;
    // 					$dataSave[$suppStudent->course][$key]['exam_subjects'] =  null;
    // 					$dataSave[$suppStudent->course][$key]['exam_subjects'] = $this->getSubjectDetailForHallTicketSupp($suppStudent->student_id);


    // 					if(isset($suppStudent->photograph) && !empty($suppStudent->photograph)){
    // 						$dataSave[$suppStudent->course][$key]['photograph'] = $suppStudent->photograph;
    // 						$dataSave[$suppStudent->course][$key]['signature'] = $suppStudent->signature;
    // 					}else{
    // 						$dataSave[$suppStudent->course][$key]['photograph'] = '';
    // 						$dataSave[$suppStudent->course][$key]['signature'] = '';
    // 					}

    // 					if(isset($suppStudent->district_name) && $suppStudent->district_name != ''){
    // 						$dataSave[$suppStudent->course][$key]['district'] = $suppStudent->district_name;
    // 					}else{
    // 						$dataSave[$suppStudent->course][$key]['district'] = '';
    // 					}
    // 					if(isset($suppStudent->tehsil_name) && $suppStudent->tehsil_name != ''){
    // 						$dataSave[$suppStudent->course][$key]['tehsil'] = $suppStudent['Address']['tehsil_name'];
    // 					}else{
    // 						$dataSave[$suppStudent->course][$key]['tehsil'] = '';
    // 					}
    // 					$dataSave[$suppStudent->course][$key]['ecenter10'] = $suppStudent->ecenter10;
    // 					$dataSave[$suppStudent->course][$key]['ecenter12'] = $suppStudent->ecenter12;
    // 					$dataSave[$suppStudent->course][$key]['cent_name'] = $suppStudent->cent_name;
    // 					$dataSave[$suppStudent->course][$key]['cent_add1'] = $suppStudent->cent_add1;
    // 					$dataSave[$suppStudent->course][$key]['cent_add2'] = $suppStudent->cent_add2;
    // 					$dataSave[$suppStudent->course][$key]['cent_add3'] = $suppStudent->cent_add3;
    // 					$key++;
    // 				}
    // 			}
    // 		/* Supplementray Data Set in Array End */
    // 			 // dd($dataSave);

    // 		/* Student Data Set in Array Start */
    // 			if(isset($students) && !empty($students)){
    // 				foreach($students as $stKey => $student){

    // 					$ai_code = @$suppStudent->ai_code;
    // 					$dataSave[$student->course][$key]['type'] = 'Student';
    // 					$dataSave[$student->course][$key]['index'] = $stKey;
    // 					$dataSave[$student->course][$key]['id'] = $student->id;
    // 					$dataSave[$student->course][$key]['student_id'] = $student->id;
    // 					$dataSave[$student->course][$key]['ai_code'] = $student->ai_code;
    // 					$dataSave[$student->course][$key]['enrollment'] = $student->enrollment;
    // 					$dataSave[$student->course][$key]['name'] = $student->name;
    // 					$dataSave[$student->course][$key]['father_name'] = $student->father_name;
    // 					$dataSave[$student->course][$key]['mother_name'] = $student->mother_name;
    // 					$dataSave[$student->course][$key]['category_a'] = $student->category_a;

    // 					if(isset($student->course) && !empty($student->course)){
    // 						$dataSave[$student->course][$key]['course'] = $student->course;
    // 					}else{
    // 						$dataSave[$student->course][$key]['course'] = '';
    // 					}
    // 					if(isset($student->dob) && !empty($student->dob)){
    // 						$dataSave[$student->course][$key]['dob'] = $student->dob;
    // 					}else{
    // 						$dataSave[$student->course][$key]['dob'] = '';
    // 					}
    // 					$dataSave[$student->course][$key]['stream'] = $student->stream;

    // 					$dataSave[$student->course][$key]['exam_subjects'] = $this->getSubjectDetailForHallTicket($student->id);


    // 					if(isset($student->photograph) && !empty($student->photograph)){
    // 						$dataSave[$student->course][$key]['photograph'] = $student->photograph;
    // 						$dataSave[$student->course][$key]['signature'] = $student->signature;
    // 					}else{
    // 						$dataSave[$student->course][$key]['photograph'] = '';
    // 						$dataSave[$student->course][$key]['signature'] = '';
    // 					}
    // 					if(isset($student->district_name) && $student->district_name != ''){
    // 						$dataSave[$student->course][$key]['district'] = $student->district_name;
    // 					}else{
    // 						$dataSave[$student->course][$key]['district'] = '';
    // 					}
    // 					if(isset($student->tehsil_name) && $student->tehsil_name != ''){
    // 						$dataSave[$student->course][$key]['tehsil'] = $student->tehsil_name;
    // 					}else{
    // 						$dataSave[$student->course][$key]['tehsil'] = '';
    // 					}

    // 					$dataSave[$student->course][$key]['ecenter10'] = $student->ecenter10;
    // 					$dataSave[$student->course][$key]['ecenter12'] = $student->ecenter12;
    // 					$dataSave[$student->course][$key]['cent_name'] = $student->cent_name;
    // 					$dataSave[$student->course][$key]['cent_add1'] = $student->cent_add1;
    // 					$dataSave[$student->course][$key]['cent_add2'] = $student->cent_add2;
    // 					$dataSave[$student->course][$key]['cent_add3'] = $student->cent_add3;
    // 					$key++;
    // 				}
    // 			}
    // 		/* Student Data Set in Array End */
    // 		$current_year = @$current_folder_year[1];

    // 		$combo_name = 'student_document_path';$student_document_path = $this->master_details($combo_name);
    // 		$studentDocumentPath = $student_document_path[1];

    // 		$students = $dataSave;
    // 		// return view('examination_reports.hall_ticket_view',compact('current_year','stream','subjects','subjects10','studentDocumentPath','subjects12','practicalsubjects12','subject_list','categorya','aicode','students','subreportname','reportname','course'));

    // 		$pdf =  PDF::loadView('examination_reports.hall_ticket_view',compact('current_year','stream','subjects','subjects10','studentDocumentPath','subjects12','practicalsubjects12','subject_list','categorya','aicode','students','subreportname','reportname','course'));
    // 		$path = public_path("files/reports/" . $current_year. "/stream" . $stream . "/". $aicentermaterial . "/".$course ."/". $aicodedistrictid1. "/". $aicodetemp. "/");
    // 		File::makeDirectory($path, $mode = 0777, true, true);
    // 		$pdf->setOption('footer-right', 'Page [page] of [toPage]');

    // 		$filename = 'hall_ticket_' . time() .'_'. $aicodetemp . '.pdf';
    // 		$completepath = $path .$filename;

    // 		$pdf->save($completepath,$pdf,true);
    // 		return( Response::download( $completepath ) );
    // 	}
    // }


    function datapicker()
    {
        return view('landing.datapicker');
    }

    public function CheckAllowurl()
    {
    }

    public function rp_generate_provisional_markseet_bulk_ten_male()
    { // 10 1
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $custom_component_obj = new CustomComponent;
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'result_session';
        $result_session = $this->master_details($combo_name);
        $combo_name = 'result_type';
        $result_type = $this->master_details($combo_name);
        $current_exam_month_id = Config::get('global.current_result_session_month_id');
        $result_session = $result_session[$current_exam_month_id];

        $subject_list = $this->subjectList();
        $offset = 0;
        $limit = 200000;

        $conditionsCourse = ['students.course' => 10, 'students.gender_id' => 1];

        $fields = array('provisional_exam_results.id', 'students.enrollment', 'students.dob', 'provisional_exam_results.student_id');
        $studentsList = ProvisionalExamResult::
        join('students', 'provisional_exam_results.student_id', '=', 'students.id')
            ->where($conditionsCourse)
            ->where('provisional_exam_results.revised', "!=", 21)
            ->where('provisional_exam_results.exam_year', 124)->where('provisional_exam_results.exam_month', 1)->take($limit)->skip($offset)->get($fields);
        // ->where('students.id',711251)
        $counter = 0;
        foreach ($studentsList as $student) {
            $enrollment = $student->enrollment;
            $id = $student->id;
            $dob = $student->dob;
            $students = $custom_component_obj->getStudentProvisionalResult($enrollment, $dob);
            if (!empty($students)) {
                $studentexamsubjects = $custom_component_obj->getStudentProvisionalResultSubject($students->student_id);
                if (@$studentexamsubjects) {
                    $pdf = PDF::loadView('resultupdate.provisionalmarksheet', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type'));
                    $filename = $enrollment . '.pdf';
                    $path = public_path("files/provisional_marksheet/124/1/");
                    $completepath = $path . $filename;
                    $pdf->save($completepath, $pdf, true);

                    $saveData = array();
                    $saveData = ['revised' => 21];

                    ProvisionalExamResult::where('id', $id)->update($saveData);

                    echo $counter . " " . $students->student_id . "<br>";
                }
            }
        }
        echo "<br> $counter " . "Done";
        die;

    }

    public function rp_generate_provisional_markseet_bulk_ten_female()
    { // 10 2
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $custom_component_obj = new CustomComponent;
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'result_session';
        $result_session = $this->master_details($combo_name);
        $combo_name = 'result_type';
        $result_type = $this->master_details($combo_name);
        $current_exam_month_id = Config::get('global.current_result_session_month_id');
        $result_session = $result_session[$current_exam_month_id];

        $subject_list = $this->subjectList();
        $offset = 0;
        $limit = 200000;

        $conditionsCourse = ['students.course' => 10, 'students.gender_id' => 2];

        $fields = array('provisional_exam_results.id', 'students.enrollment', 'students.dob', 'provisional_exam_results.student_id');
        $studentsList = ProvisionalExamResult::
        join('students', 'provisional_exam_results.student_id', '=', 'students.id')
            ->where($conditionsCourse)
            ->where('provisional_exam_results.revised', "!=", 21)
            ->where('provisional_exam_results.exam_year', 124)->where('provisional_exam_results.exam_month', 1)->take($limit)->skip($offset)->get($fields);
        // ->where('students.id',711251)
        $counter = 0;
        foreach ($studentsList as $student) {
            $enrollment = $student->enrollment;
            $id = $student->id;
            $dob = $student->dob;
            $students = $custom_component_obj->getStudentProvisionalResult($enrollment, $dob);
            if (!empty($students)) {
                $studentexamsubjects = $custom_component_obj->getStudentProvisionalResultSubject($students->student_id);
                if (@$studentexamsubjects) {
                    $pdf = PDF::loadView('resultupdate.provisionalmarksheet', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type'));
                    $filename = $enrollment . '.pdf';
                    $path = public_path("files/provisional_marksheet/124/1/");
                    $completepath = $path . $filename;
                    $pdf->save($completepath, $pdf, true);

                    $saveData = array();
                    $saveData = ['revised' => 21];

                    ProvisionalExamResult::where('id', $id)->update($saveData);

                    echo $counter . " " . $students->student_id . "<br>";
                }
            }
        }
        echo "<br> $counter " . "Done";
        die;

    }


    public function rp_generate_provisional_markseet_bulk_twel_male()
    { // 12 1
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $custom_component_obj = new CustomComponent;
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'result_session';
        $result_session = $this->master_details($combo_name);
        $combo_name = 'result_type';
        $result_type = $this->master_details($combo_name);
        $current_exam_month_id = Config::get('global.current_result_session_month_id');
        $result_session = $result_session[$current_exam_month_id];

        $subject_list = $this->subjectList();
        $offset = 0;
        $limit = 200000;

        $conditionsCourse = ['students.course' => 12, 'students.gender_id' => 1];

        $fields = array('provisional_exam_results.id', 'students.enrollment', 'students.dob', 'provisional_exam_results.student_id');
        $studentsList = ProvisionalExamResult::
        join('students', 'provisional_exam_results.student_id', '=', 'students.id')
            ->where($conditionsCourse)
            ->where('provisional_exam_results.revised', "!=", 21)
            ->where('provisional_exam_results.exam_year', 124)->where('provisional_exam_results.exam_month', 1)->take($limit)->skip($offset)->get($fields);
        // ->where('students.id',711251)
        $counter = 0;
        foreach ($studentsList as $student) {
            $enrollment = $student->enrollment;
            $id = $student->id;
            $dob = $student->dob;
            $students = $custom_component_obj->getStudentProvisionalResult($enrollment, $dob);
            if (!empty($students)) {
                $studentexamsubjects = $custom_component_obj->getStudentProvisionalResultSubject($students->student_id);
                if (@$studentexamsubjects) {
                    $pdf = PDF::loadView('resultupdate.provisionalmarksheet', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type'));
                    $filename = $enrollment . '.pdf';
                    $path = public_path("files/provisional_marksheet/124/1/");
                    $completepath = $path . $filename;
                    $pdf->save($completepath, $pdf, true);

                    $saveData = array();
                    $saveData = ['revised' => 21];

                    ProvisionalExamResult::where('id', $id)->update($saveData);

                    echo $counter . " " . $students->student_id . "<br>";
                }
            }
        }
        echo "<br> $counter " . "Done";
        die;

    }

    public function rp_generate_provisional_markseet_bulk_twel_female()
    { // 12 2
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $custom_component_obj = new CustomComponent;
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'result_session';
        $result_session = $this->master_details($combo_name);
        $combo_name = 'result_type';
        $result_type = $this->master_details($combo_name);
        $current_exam_month_id = Config::get('global.current_result_session_month_id');
        $result_session = $result_session[$current_exam_month_id];

        $subject_list = $this->subjectList();
        $offset = 0;
        $limit = 10000;

        $conditionsCourse = ['students.course' => 10, 'students.gender_id' => 2];

        $fields = array('provisional_exam_results.id', 'students.enrollment', 'students.dob', 'provisional_exam_results.student_id');
        $studentsList = ProvisionalExamResult::
        join('students', 'provisional_exam_results.student_id', '=', 'students.id')
            ->where($conditionsCourse)
            ->where('provisional_exam_results.revised', "!=", 21)
            ->where('provisional_exam_results.exam_year', 124)->where('provisional_exam_results.exam_month', 1)->take($limit)->skip($offset)->get($fields);

        $counter = 0;
        foreach ($studentsList as $student) {
            $enrollment = $student->enrollment;
            $id = $student->id;
            $dob = $student->dob;
            $students = $custom_component_obj->getStudentProvisionalResult($enrollment, $dob);
            if (!empty($students)) {
                $studentexamsubjects = $custom_component_obj->getStudentProvisionalResultSubject($students->student_id);
                if (@$studentexamsubjects) {
                    $pdf = PDF::loadView('resultupdate.provisionalmarksheet', compact('students', 'studentexamsubjects', 'course', 'result_session', 'subject_list', 'result_type'));
                    $filename = $enrollment . '.pdf';
                    $path = public_path("files/provisional_marksheet/124/1/");
                    $completepath = $path . $filename;
                    $pdf->save($completepath, $pdf, true);

                    $saveData = array();
                    $saveData = ['revised' => 21];

                    ProvisionalExamResult::where('id', $id)->update($saveData);

                    echo $counter . " " . $students->student_id . "<br>";
                }
            }
        }
        echo "<br> $counter " . "Done";
        die;

    }

    public function admission_option(Request $request)
    {
        $combo_name = 'apply_type_for_admissions';
        $result = DB::table('masters')->where('combo_name', $combo_name)->get();
        if (count($request->all()) > 0) {
            if ($request->option_id == '1') {
                return redirect()->route('landing')->with('message', 'Self Registration Coming Soon.');
            } else {
                return redirect()->route('aicentres');
            }
        }
        return view('landing.admission_option', compact('result'));


    }


    public function developerPaymentVerify()
    {
        $studetIds = array(
            "845908" => "23567586141",
            "846445" => "23567770890",
            "846926" => "23568412820",
            "822913" => "23569167920",
            "855374" => "23569230438",
            "851727" => "23569245457",
            "855619" => "23569249544",
            "852457" => "23569254197",
            "855101" => "23569257787",
            "851770" => "23569257867",
            "856117" => "23569259489",
        );
        // $studetIds = array();
        foreach ($studetIds as $student_id => $challan_number) {
            $student = Student::where("id", $student_id)->first();
            $application = Application::where("student_id", $student_id)->first();
            $studentFees = StudentFee::where("student_id", $student_id)->first(); //total

            $updatestudent = Student::find($student_id);
            $updatestudent->challan_tid = $challan_number;
            if (isset($challan_number) && !empty($challan_number)) {
                $enrollment = $this->_setEnrollmentAndIsEligiable($student_id);
            }
            $currentDateTime = date('Y-m-d H:i:s');
            $updatestudent->application_fee_date = $currentDateTime;
            $updatestudent->submitted = $currentDateTime;
            $updatestudent->remarks = '';
            $updatestudent->save();

            $updateApplication = Application::where('student_id', $student_id)->first();

            $updateApplication->status = 1;
            $updateApplication->fee_status = 1;
            $updateApplication->locksumbitted = 1;
            $updateApplication->fee_paid_amount = @$studentFees->total;
            $updateApplication->save();
            //students table filed
            // is_eligible = 1
            // submitted
            // challan_tid
            // application_fee_date
            // enrollment
            // student_code


            //application table filed
            // enrollment
            // fee_status = 1
            // locksumbitted
            // status
            // fee_paid_amount

            echo "<br>" . $student_id;

        }
        echo "<br> Done";
        die;
    }

    public function developerSuppPaymentVerify()
    {

        $studetIds = array(
            '443725' => '23560417874',
            '555970' => '23561262658',
        );
        // $studetIds = array();
        foreach ($studetIds as $student_id => $challan_number) {
            $current_exam_year = Config::get('global.form_supp_current_admission_session_id');
            $current_exam_month = Config::get('global.supp_current_admission_exam_month');
            $supp = Supplementary::where('student_id', $student_id)->where('exam_year', $current_exam_year)->where('exam_month', $current_exam_month)->first(['id', 'total_fees']);
            $updateSupplementary = Supplementary::where('student_id', $student_id)->where('exam_year', $current_exam_year)->where('exam_month', $current_exam_month)->where('id', $supp->id)->first();
            $updateSupplementary->submitted = date('Y-m-d H:i:s');
            $updateSupplementary->remarks = 'Payment migrated';
            $updateSupplementary->fee_status = 1;
            $updateSupplementary->challan_tid = $challan_number;
            $updateSupplementary->is_eligible = 1;
            $updateSupplementary->fee_paid_amount = @$supp->total_fees;
            $updateSupplementary->save();

            echo "<br>" . $student_id;

        }
        echo "<br> Done";
        die;

    }

    public function sendDBTDetailToJanAap()
    {
        // DbtStudent
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '0');
        $dbtStudents = DB::table('dbt_students')->where('is_saved', '!=', 'Y')->where('exam_year', 125)->limit(3)->get();
        $i = 1;
        foreach ($dbtStudents as $dbtStudent) {
            $response = $this->_sendTransationDetailsForDBTToJanAadhar($dbtStudent);
            $is_saved = json_decode($response, true);
            $is_saved = $is_saved['transaction']['isSaved'];
            $dbtStudentArray = ['response' => $response, 'is_saved' => $is_saved];

            $status = DbtStudent::where('id', $dbtStudent->id)->update($dbtStudentArray);
        }
        echo "<h1>Done : " . count($dbtStudents) . " </h1>";
        die;
    }

    public function onlytest(Request $request)
    {

        return view('landing.onlytest');

    }

    public function hall_ticket_api_download(Request $request)
    {
        $stream = Config::get("global.defaultStreamId");
        $exam_monthyear = Config::get("global.student_admit_card_download_exam_year");
        @$enrollment = Crypt::decrypt($inputs->enrollment);
        $finedstudentallotment = StudentAllotment::where('enrollment', $enrollment)->where('exam_year', $exam_monthyear)->where('exam_month', $stream)->first(['course', 'ai_code']);
        if (empty($finedstudentallotment)) {
            return Redirect::back()->with('error', 'Enrollment not Found');
        }
        @$course = $finedstudentallotment->course;
        @$ai_code = $finedstudentallotment->ai_code;
        $title = "Hall Ticket Report";
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'exam_time_table_start_end_time';
        $exam_time_table_start_end_time = $this->master_details($combo_name);
        $current_folder_year = $this->getCurrentYearFolderName();
        $subreportname = "Hall Ticket";
        $aicentermaterial = "aicentermaterial";
        $practicalsubjects = $this->subjectPracticalCodeList();
        $subjects = $this->subjectCodeList();
        $practicalsubjects12 = $practicalsubjects->toArray();
        //$subjects = DB::table('subjects')->where(array('deleted'=>0))->orderBy('subject_code')->pluck('subject_code','id');
        //$subjects10 = DB::table('subjects')->where(array('deleted'=>0))->where(array('course'=>10))->orderBy('subject_code')->pluck('name','id');
        //$subjects12 = DB::table('subjects')->where(array('deleted'=>0))->where(array('course'=>12))->orderBy('subject_code')->pluck('name','id');
        //$practicalsubjects12 = DB::table('subjects')->where(array('deleted'=>0))->where(array('practical_type'=>1))->orderBy('subject_code')
        //->pluck('subject_code','id')->toArray();
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        /* Master data get start */
        $conditions = array();
        $conditionSupplementary = array();
        if (isset($ai_code) && !empty($ai_code)) {
            $conditions [] = ['student_allotments.ai_code', '=', $ai_code];
            $conditions [] = ['student_allotments.supplementary', '=', 0];
            $conditions [] = ['students.is_eligible', '=', 1];
            $conditions [] = ['student_allotments.enrollment', '=', $enrollment];
            $conditions [] = ['student_allotments.exam_year', '=', $exam_monthyear];
            $conditions [] = ['student_allotments.exam_month', '=', $stream];
        } else {
            return redirect()->route('hall_ticket_form')->with('error', 'Oop"s! Did you really think you are allowed to see that?');
        }
        /* Suplementary Data Start  */
        $conditionSupplementary [] = ['student_allotments.ai_code', '=', $ai_code];
        $conditionSupplementary [] = ['student_allotments.exam_year', '=', $exam_monthyear];
        $conditionSupplementary [] = ['student_allotments.supplementary', '=', 1];
        $conditionSupplementary [] = ['student_allotments.exam_month', '=', $stream];
        $conditionSupplementary [] = ['supplementaries.is_eligible', '=', 1];
        $conditionSupplementary [] = ['student_allotments.enrollment', '=', $enrollment];
        $conditionSupplementary [] = ['supplementaries.exam_year', '=', $exam_monthyear];
        $conditionSupplementary [] = ['supplementaries.exam_month', '=', $stream];
        $suppStudents = array();
        $suppStudents = StudentAllotment::select('students.id', 'students.ai_code', 'students.enrollment', 'students.name', 'students.father_name', 'student_allotments.student_id',
            'students.mother_name', 'students.mobile', 'students.name_hi', 'students.stream',
            'applications.category_a', 'students.dob', 'students.course',
            'addresses.district_name', 'addresses.tehsil_name',
            'documents.photograph', 'documents.signature',
            'examcenter_details.ecenter10', 'examcenter_details.ecenter12',
            'examcenter_details.cent_name', 'examcenter_details.cent_add1', 'examcenter_details.cent_add2',
            'examcenter_details.cent_add3')
            ->join('students', 'students.id', '=', 'student_allotments.student_id')
            ->join('documents', 'documents.student_id', '=', 'student_allotments.student_id')
            ->join('addresses', 'addresses.student_id', '=', 'student_allotments.student_id')
            ->join('applications', 'applications.student_id', '=', 'student_allotments.student_id')
            ->join('supplementaries', 'supplementaries.student_id', '=', 'student_allotments.student_id')
            ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
            ->where($conditionSupplementary)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('addresses.deleted_at')->whereNull('applications.deleted_at')->whereNull('supplementaries.deleted_at')->whereNull('examcenter_details.deleted_at')
            ->orderBy('student_allotments.student_id', 'ASC')
            ->groupBy('student_allotments.student_id')
            ->get();
        /* Suplementary Data End */
        /* Student  Data Start */
        $students = array();
        $students = StudentAllotment::select(
            'students.id', 'students.ai_code', 'students.enrollment', 'students.name', 'students.father_name', 'student_allotments.student_id',
            'students.mother_name', 'students.mobile', 'students.name_hi', 'students.stream',
            'applications.category_a', 'students.dob', 'students.course',
            'addresses.district_name', 'addresses.tehsil_name',
            'documents.photograph', 'documents.signature',
            'examcenter_details.ecenter10', 'examcenter_details.ecenter12',
            'examcenter_details.cent_name', 'examcenter_details.cent_add1', 'examcenter_details.cent_add2',
            'examcenter_details.cent_add3',

        )
            ->join('applications', 'applications.student_id', '=', 'student_allotments.student_id')
            ->join('documents', 'documents.student_id', '=', 'student_allotments.student_id')
            ->join('addresses', 'addresses.student_id', '=', 'student_allotments.student_id')
            ->join('students', 'students.id', '=', 'student_allotments.student_id')
            ->join('examcenter_details', 'examcenter_details.id', '=', 'student_allotments.examcenter_detail_id')
            ->where($conditions)->whereNull('student_allotments.deleted_at')->whereNull('students.deleted_at')->whereNull('documents.deleted_at')->whereNull('addresses.deleted_at')->whereNull('applications.deleted_at')->whereNull('examcenter_details.deleted_at')
            ->groupBy('student_allotments.student_id')
            ->orderBy('student_allotments.student_id', 'ASC')->get();
        /* Student  Data End */
        /* Master data get end */


        /* Supplementray Data Set in Array End */
        $dataSave = array();
        $key = 0;
        if (isset($suppStudents) && !empty($suppStudents)) {
            foreach ($suppStudents as $suppKey => $suppStudent) {
                $dataSave[$suppStudent->course][$key]['index'] = $suppKey;
                $ai_code = @$suppStudent->ai_code;
                $dataSave[$suppStudent->course][$key]['type'] = 'Supplementary';

                $dataSave[$suppStudent->course][$key]['id'] = $suppStudent->id;
                $dataSave[$suppStudent->course][$key]['student_id'] = $suppStudent->student_id;

                $dataSave[$suppStudent->course][$key]['ai_code'] = $suppStudent->ai_code;
                $dataSave[$suppStudent->course][$key]['enrollment'] = $suppStudent->enrollment;

                $dataSave[$suppStudent->course][$key]['name'] = $suppStudent->name;
                $dataSave[$suppStudent->course][$key]['father_name'] = $suppStudent->father_name;
                $dataSave[$suppStudent->course][$key]['mother_name'] = $suppStudent->mother_name;
                //pr($suppStudent);die;
                $dataSave[$suppStudent->course][$key]['category_a'] = $suppStudent->category_a;

                if (isset($suppStudent->course) && !empty($suppStudent->course)) {
                    $dataSave[$suppStudent->course][$key]['course'] = $suppStudent->course;
                } else {
                    $dataSave[$suppStudent->course][$key]['course'] = array();
                }
                if (isset($suppStudent->dob) && !empty($suppStudent->dob)) {
                    if (strpos($suppStudent->dob, '-')) {
                        $ndobarr = explode('-', $suppStudent->dob);
                        $ndob = $ndobarr[2] . "-" . $ndobarr[1] . "-" . $ndobarr[0];
                        $dataSave[$suppStudent->course][$key]['dob'] = $ndob;
                    } else {
                        $dataSave[$suppStudent->course][$key]['dob'] = $suppStudent->dob;
                    }
                } else {
                    $dataSave[$suppStudent->course][$key]['dob'] = array();
                }
                $dataSave[$suppStudent->course][$key]['stream'] = $suppStudent->stream;
                $dataSave[$suppStudent->course][$key]['exam_subjects'] = null;
                $dataSave[$suppStudent->course][$key]['exam_subjects'] = $this->getSubjectDetailForHallTicketSupp($suppStudent->student_id);

                if (isset($suppStudent->photograph) && !empty($suppStudent->photograph)) {
                    $dataSave[$suppStudent->course][$key]['photograph'] = $suppStudent->photograph;
                    $dataSave[$suppStudent->course][$key]['signature'] = $suppStudent->signature;
                } else {
                    $dataSave[$suppStudent->course][$key]['photograph'] = '';
                    $dataSave[$suppStudent->course][$key]['signature'] = '';
                }

                if (isset($suppStudent->district_name) && $suppStudent->district_name != '') {
                    $dataSave[$suppStudent->course][$key]['district'] = $suppStudent->district_name;
                } else {
                    $dataSave[$suppStudent->course][$key]['district'] = null;
                }

                if (isset($suppStudent->tehsil_name) && $suppStudent->tehsil_name != '') {
                    $dataSave[$suppStudent->course][$key]['tehsil'] = $suppStudent->district_name;
                } else {
                    $dataSave[$suppStudent->course][$key]['tehsil'] = null;
                }
                $dataSave[$suppStudent->course][$key]['ecenter10'] = $suppStudent->ecenter10;
                $dataSave[$suppStudent->course][$key]['ecenter12'] = $suppStudent->ecenter12;
                $dataSave[$suppStudent->course][$key]['cent_name'] = $suppStudent->cent_name;
                $dataSave[$suppStudent->course][$key]['cent_add1'] = $suppStudent->cent_add1;
                $dataSave[$suppStudent->course][$key]['cent_add2'] = $suppStudent->cent_add2;
                $dataSave[$suppStudent->course][$key]['cent_add3'] = $suppStudent->cent_add3;
                $key++;
            }

        }
        /* Supplementray Data Set in Array End */
        //dd($dataSave);

        /* Student Data Set in Array Start */
        if (isset($students) && !empty($students)) {
            foreach ($students as $stKey => $student) {

                $ai_code = @$suppStudent->ai_code;
                $dataSave[$student->course][$key]['type'] = 'Student';
                $dataSave[$student->course][$key]['index'] = $stKey;
                $dataSave[$student->course][$key]['id'] = $student->id;
                $dataSave[$student->course][$key]['student_id'] = $student->student_id;
                $dataSave[$student->course][$key]['ai_code'] = $student->ai_code;
                $dataSave[$student->course][$key]['enrollment'] = $student->enrollment;
                $dataSave[$student->course][$key]['name'] = $student->name;
                $dataSave[$student->course][$key]['father_name'] = $student->father_name;
                $dataSave[$student->course][$key]['mother_name'] = $student->mother_name;
                $dataSave[$student->course][$key]['category_a'] = $student->category_a;

                if (isset($student->course) && !empty($student->course)) {
                    $dataSave[$student->course][$key]['course'] = $student->course;
                } else {
                    $dataSave[$student->course][$key]['course'] = array();
                }
                if (isset($student->dob) && !empty($student->dob)) {
                    if (strpos($student->dob, '-')) {
                        $ndobarr = explode('-', $student->dob);
                        $ndob = $ndobarr[2] . "-" . $ndobarr[1] . "-" . $ndobarr[0];
                        $dataSave[$student->course][$key]['dob'] = $ndob;
                    } else {
                        $dataSave[$student->course][$key]['dob'] = $student->dob;
                    }
                } else {
                    $dataSave[$student->course][$key]['dob'] = array();
                }
                $dataSave[$student->course][$key]['stream'] = $student->stream;

                $dataSave[$student->course][$key]['exam_subjects'] = $this->getSubjectDetailForHallTicket($student->student_id);

                if (isset($student->photograph) && !empty($student->photograph)) {
                    $dataSave[$student->course][$key]['photograph'] = $student->photograph;
                    $dataSave[$student->course][$key]['signature'] = $student->signature;
                } else {
                    $dataSave[$student->course][$key]['photograph'] = '';
                    $dataSave[$student->course][$key]['signature'] = '';
                }
                if (isset($student->district_name) && $student->district_name != '') {
                    $dataSave[$student->course][$key]['district'] = $student->district_name;
                } else {
                    $dataSave[$student->course][$key]['district'] = null;
                }
                if (isset($student->tehsil_name) && $student->tehsil_name != '') {
                    $dataSave[$student->course][$key]['tehsil'] = $student->tehsil_name;
                } else {
                    $dataSave[$student->course][$key]['tehsil'] = null;
                }

                $dataSave[$student->course][$key]['ecenter10'] = $student->ecenter10;
                $dataSave[$student->course][$key]['ecenter12'] = $student->ecenter12;
                $dataSave[$student->course][$key]['cent_name'] = $student->cent_name;
                $dataSave[$student->course][$key]['cent_add1'] = $student->cent_add1;
                $dataSave[$student->course][$key]['cent_add2'] = $student->cent_add2;
                $dataSave[$student->course][$key]['cent_add3'] = $student->cent_add3;
                $key++;
            }

        }

        /* Student Data Set in Array End */

        $current_year = @$current_folder_year;
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1];
        $students = $dataSave;
        return view('examination_reports.hall_ticket_view', compact('current_year', 'stream', 'subjects', 'studentDocumentPath', 'practicalsubjects12', 'categorya', 'students', 'subreportname', 'reportname', 'course', 'exam_session', 'exam_time_table_start_end_time'));

    }


    public function summaryBookStockInformationDownloadExcel($querys = null, $type = "xlsx")
    {
        $fileName = 'SummaryBookStockInformationExlExport';

        $lbl = "";
        if ($querys == 1) {
            $lbl = "10_Hindi";
        } else if ($querys == 2) {
            $lbl = "10_English";
        } else if ($querys == 3) {
            $lbl = "12_Hindi";
        } else if ($querys == 4) {
            $lbl = "12_English";
        } else {
            echo "Something is Wrong.";
            die;
        }
        return Excel::download(new SummaryBookStockInformationExlExport($querys), $fileName . '_' . $lbl . '_' . date("d-m-Y") . '.xlsx');

    }

    public function summaryRequriedBookInformationDownloadExcel($querys = null, $type = "xlsx")
    {
        $fileName = 'SummaryRequriedBookInformationDownloadExcel';

        $lbl = "";
        if ($querys == 1) {
            $lbl = "10_Hindi";
        } else if ($querys == 2) {
            $lbl = "10_English";
        } else if ($querys == 3) {
            $lbl = "12_Hindi";
        } else if ($querys == 4) {
            $lbl = "12_English";
        } else {
            echo "Something is Wrong.";
            die;
        }

        return Excel::download(new SummaryBookRequriedInformationExlExport($querys), $fileName . '_' . $lbl . '_' . date("d-m-Y") . '.xlsx');

    }

    public function rahul123(Request $request)
    {
		
        $course = 12;
        if ($course == 12) {
            $q1 = "SELECT CONCAT( u.ai_code, '-', u.college_name ) AS college_name, u.district_id, u.ai_code AS ai_code, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 18 AND pb.subject_volume_id = 1 ) AS Sub_301_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 18 AND pb.subject_volume_id = 2 ) AS Sub_301_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 19 AND pb.subject_volume_id = 1 ) AS Sub_302_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 19 AND pb.subject_volume_id = 2 ) AS Sub_302_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 20 AND pb.subject_volume_id = 1 ) AS Sub_306_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 20 AND pb.subject_volume_id = 2 ) AS Sub_306_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 21 AND pb.subject_volume_id = 1 ) AS Sub_309_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 21 AND pb.subject_volume_id = 2 ) AS Sub_309_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 22 AND pb.subject_volume_id = 1 ) AS Sub_311_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 22 AND pb.subject_volume_id = 2 ) AS Sub_311_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 23 AND pb.subject_volume_id = 1 ) AS Sub_312_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 23 AND pb.subject_volume_id = 2 ) AS Sub_312_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 23 AND pb.subject_volume_id = 3 ) AS Sub_312_3, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 24 AND pb.subject_volume_id = 1 ) AS Sub_313_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 24 AND pb.subject_volume_id = 2 ) AS Sub_313_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 24 AND pb.subject_volume_id = 3 ) AS Sub_313_3, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 25 AND pb.subject_volume_id = 1 ) AS Sub_314_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 25 AND pb.subject_volume_id = 2 ) AS Sub_314_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 25 AND pb.subject_volume_id = 3 ) AS Sub_314_3, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 26 AND pb.subject_volume_id = 1 ) AS Sub_315_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 28 AND pb.subject_volume_id = 1 ) AS Sub_316_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 28 AND pb.subject_volume_id = 2 ) AS Sub_316_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 27 AND pb.subject_volume_id = 1 ) AS Sub_317_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 27 AND pb.subject_volume_id = 2 ) AS Sub_317_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 27 AND pb.subject_volume_id = 3 ) AS Sub_317_3, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 29 AND pb.subject_volume_id = 1 ) AS Sub_318_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 29 AND pb.subject_volume_id = 2 ) AS Sub_318_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 31 AND pb.subject_volume_id = 1 ) AS Sub_319_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 31 AND pb.subject_volume_id = 2 ) AS Sub_319_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 32 AND pb.subject_volume_id = 1 ) AS Sub_320_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 32 AND pb.subject_volume_id = 2 ) AS Sub_320_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 32 AND pb.subject_volume_id = 3 ) AS Sub_320_3, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 33 AND pb.subject_volume_id = 1 ) AS Sub_321_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 33 AND pb.subject_volume_id = 2 ) AS Sub_321_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 33 AND pb.subject_volume_id = 3 ) AS Sub_321_3, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 34 AND pb.subject_volume_id = 1 ) AS Sub_328_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 34 AND pb.subject_volume_id = 2 ) AS Sub_328_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 35 AND pb.subject_volume_id = 1 ) AS Sub_330_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 36 AND pb.subject_volume_id = 1 ) AS Sub_331_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 37 AND pb.subject_volume_id = 1 ) AS Sub_332_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 37 AND pb.subject_volume_id = 2 ) AS Sub_332_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 37 AND pb.subject_volume_id = 3 ) AS Sub_332_3, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 38 AND pb.subject_volume_id = 1 ) AS Sub_333_1, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 38 AND pb.subject_volume_id = 2 ) AS Sub_333_2, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 38 AND pb.subject_volume_id = 3 ) AS Sub_333_3, ( SELECT hindi_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 39 AND pb.subject_volume_id = 1 ) AS Sub_336_1 FROM rs_aicenter_details u LEFT JOIN rs_publication_books pb ON pb.ai_code = u.ai_code AND pb.deleted_at IS NULL AND pb.exam_month = $exam_month AND pb.exam_year = $exam_year AND pb.course = 12 WHERE u.deleted_at IS NULL GROUP BY u.ai_code ORDER BY u.ai_code ASC;";
            $q2 = "SELECT CONCAT( u.ai_code, '-', u.college_name ) AS college_name, u.district_id, u.ai_code AS ai_code, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 18 AND pb.subject_volume_id = 1 ) AS Sub_301_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 18 AND pb.subject_volume_id = 2 ) AS Sub_301_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 19 AND pb.subject_volume_id = 1 ) AS Sub_302_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 19 AND pb.subject_volume_id = 2 ) AS Sub_302_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 20 AND pb.subject_volume_id = 1 ) AS Sub_306_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 20 AND pb.subject_volume_id = 2 ) AS Sub_306_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 21 AND pb.subject_volume_id = 1 ) AS Sub_309_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 21 AND pb.subject_volume_id = 2 ) AS Sub_309_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 22 AND pb.subject_volume_id = 1 ) AS Sub_311_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 22 AND pb.subject_volume_id = 2 ) AS Sub_311_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 23 AND pb.subject_volume_id = 1 ) AS Sub_312_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 23 AND pb.subject_volume_id = 2 ) AS Sub_312_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 23 AND pb.subject_volume_id = 3 ) AS Sub_312_3, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 24 AND pb.subject_volume_id = 1 ) AS Sub_313_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 24 AND pb.subject_volume_id = 2 ) AS Sub_313_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 24 AND pb.subject_volume_id = 3 ) AS Sub_313_3, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 25 AND pb.subject_volume_id = 1 ) AS Sub_314_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 25 AND pb.subject_volume_id = 2 ) AS Sub_314_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 25 AND pb.subject_volume_id = 3 ) AS Sub_314_3, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 26 AND pb.subject_volume_id = 1 ) AS Sub_315_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 28 AND pb.subject_volume_id = 1 ) AS Sub_316_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 28 AND pb.subject_volume_id = 2 ) AS Sub_316_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 27 AND pb.subject_volume_id = 1 ) AS Sub_317_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 27 AND pb.subject_volume_id = 2 ) AS Sub_317_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 27 AND pb.subject_volume_id = 3 ) AS Sub_317_3, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 29 AND pb.subject_volume_id = 1 ) AS Sub_318_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 29 AND pb.subject_volume_id = 2 ) AS Sub_318_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 31 AND pb.subject_volume_id = 1 ) AS Sub_319_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 31 AND pb.subject_volume_id = 2 ) AS Sub_319_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 32 AND pb.subject_volume_id = 1 ) AS Sub_320_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 32 AND pb.subject_volume_id = 2 ) AS Sub_320_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 32 AND pb.subject_volume_id = 3 ) AS Sub_320_3, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 33 AND pb.subject_volume_id = 1 ) AS Sub_321_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 33 AND pb.subject_volume_id = 2 ) AS Sub_321_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 33 AND pb.subject_volume_id = 3 ) AS Sub_321_3, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 34 AND pb.subject_volume_id = 1 ) AS Sub_328_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 34 AND pb.subject_volume_id = 2 ) AS Sub_328_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 35 AND pb.subject_volume_id = 1 ) AS Sub_330_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 36 AND pb.subject_volume_id = 1 ) AS Sub_331_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 37 AND pb.subject_volume_id = 1 ) AS Sub_332_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 37 AND pb.subject_volume_id = 2 ) AS Sub_332_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 37 AND pb.subject_volume_id = 3 ) AS Sub_332_3, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 38 AND pb.subject_volume_id = 1 ) AS Sub_333_1, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 38 AND pb.subject_volume_id = 2 ) AS Sub_333_2, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 38 AND pb.subject_volume_id = 3 ) AS Sub_333_3, ( SELECT english_last_year_book_stock_count FROM rs_publication_books AS pb WHERE pb.ai_code = u.ai_code AND pb.subject_id = 39 AND pb.subject_volume_id = 1 ) AS Sub_336_1 FROM rs_aicenter_details u LEFT JOIN rs_publication_books pb ON pb.ai_code = u.ai_code AND pb.deleted_at IS NULL AND pb.exam_month = $exam_month AND pb.exam_year = $exam_year AND pb.course = 12 WHERE u.deleted_at IS NULL GROUP BY u.ai_code ORDER BY u.ai_code ASC;";
        }


        echo 'Test';
        die;


        if (count($request->all()) > 0) {
            $files = 'photograph';
            $filename = $files . "." . $request->image->getClientOriginalExtension();
            $image = Image::make($request->image)->fit(400, 400, null)->encode();
            $image->save("public/comp/{$filename}");

        }
        return view('landing.imagess12');
    }

    public function rahul_backup_database()
    {
        $mysqlHostName = '10.68.128.254';
        $mysqlUserName = 'hteapp';
        $mysqlPassword = 'hteapp@123#';
        $DbName = 'lrsos';
        $backup_name = "backup.sql";
        $tables = array("users", "villages", "migrations", "failed_jobs", "password_resets"); //here your tables...

        $connect = new \PDO("mysql:host=$mysqlHostName;dbname=$DbName;charset=utf8", "$mysqlUserName", "$mysqlPassword", array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $get_all_table_query = "rs_students";
        $statement = $connect->prepare($get_all_table_query);
        $statement->execute();
        $result = $statement->fetchAll();

        $output = '';
        foreach ($tables as $table) {
            $show_table_query = "SHOW CREATE TABLE " . $table . "";
            $statement = $connect->prepare($show_table_query);
            $statement->execute();
            $show_table_result = $statement->fetchAll();

            foreach ($show_table_result as $show_table_row) {
                $output .= "\n\n" . $show_table_row["Create Table"] . ";\n\n";
            }
            $select_query = "SELECT * FROM " . $table . "";
            $statement = $connect->prepare($select_query);
            $statement->execute();
            $total_row = $statement->rowCount();

            for ($count = 0; $count < $total_row; $count++) {
                $single_result = $statement->fetch(\PDO::FETCH_ASSOC);
                $table_column_array = array_keys($single_result);
                $table_value_array = array_values($single_result);
                $output .= "\nINSERT INTO $table (";
                $output .= "" . implode(", ", $table_column_array) . ") VALUES (";
                $output .= "'" . implode("','", $table_value_array) . "');\n";
            }
        }
        $file_name = 'database_backup_on_' . date('y-m-d') . '.sql';
        $file_handle = fopen($file_name, 'w+');
        fwrite($file_handle, $output);
        fclose($file_handle);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file_name));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_name));
        ob_clean();
        flush();
        readfile($file_name);
        unlink($file_name);
    }

    public function publish_result()
    {
        $custom_component_obj = new CustomComponent;
        $AllowAdmitCardAll = $custom_component_obj->getIsAllowToShowAdmitCardDownloadForAll();
        $resultCheckStatus = $custom_component_obj->checkAllowProvisionResult();
        $allowYearCombo = $custom_component_obj->_getAllowYearCombo();
        $allowHllTicketIps = $custom_component_obj->hallticketAllowIps();

        $showStatus = $this->_getCheckAllowToCheckResult();
        $showSuppStatus = $this->_getCheckAllowToCheckSupp();
        $showrevisedStatus = $this->_getCheckAllowToCheckRevisedResult();
        $combo_name = 'result_session';
        $result_session = $this->master_details($combo_name);
        // dd($result_session);
        $current_exam_month_id = Config::get('global.current_result_session_month_id');
        $result_session = $result_session[$current_exam_month_id];
        return view('landing.publish_result', compact('resultCheckStatus', 'showSuppStatus', 'allowYearCombo', 'result_session', 'showStatus', 'showrevisedStatus', 'AllowAdmitCardAll', 'allowHllTicketIps'));
        //return view('landing.publish_result');
    }

    public function rahulimageupload(Request $request)
    {

        if ($request->hasfile('image')) {
            $app = 'http://10.68.181.213/rsos/public/';

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move($app);

        }


        return view('landing.rahulimageupload');
    }

    public function downloadStudentFeesExlmaltipule(Request $request, $type = "xlsx")
    {
        $application_exl_data = new MultipleSheetsExlExport;
        $filename = 'application_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($application_exl_data, $filename);
    }

    public function qqrcode(Request $request)
    {
        $url = URL::to("https://rsos.rajasthan.gov.in/");//Rohit
        $marksheet_component_obj = new MarksheetCustomComponent;
        $qrcode = $this->marksheet_component_obj->qrbcode($url);
        $imagepath = asset('public/qrcode/enrollment/rahul.png');
        $barcode_img = '<img src="' . $imagepath . '" style="font-size:0;position:relative;width:65px;height:65px;" >';
		

        return view('landing.imagess', compact('barcode_img'));
    }
	

}

	