<?php

namespace App\Http\Controllers;

use App\Component\BookRequirementCustomComponent;
use App\Component\CustomComponent;
use App\Component\PracticalCustomComponent;
use App\Component\ThoeryCustomComponent;
use App\Exports\FreshStudentSummaryExcelExport;
use App\Exports\MasterQuerieediterExcelExport;
use App\Helper\CustomHelper;
use App\models\Application;
use App\Models\DocumentVerification;
use App\models\ExamResult;
use App\models\ExamSubject;
use App\models\MasterQuery;
use App\Models\RevalStudent;
use App\models\Student;
use App\Models\StudentAllotment;
use App\Models\StudentDocumentVerification;
use App\Models\StudentFee;
use App\models\SuppChangeRequestStudents;
use App\Models\Supplementary;
use Auth;
use Config;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Redirect;

class ApplicationController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:application_dashboard', ['only' => ['applicationdashboard']]);//
        $this->middleware('permission:query-editer', ['only' => ['queryediter']]);
        $this->middleware('permission:query-editer-multi', ['only' => ['queryeditermulti']]);
        $this->middleware('permission:admin_dashboard', ['only' => ['admindashboard']]);
        $this->middleware('permission:examcenter_dashboard', ['only' => ['examcenter']]);
        $this->middleware('permission:examiner_dashboard', ['only' => ['examiner']]);
        $this->middleware('permission:theroy_examiner_dashboard', ['only' => ['theroy_examiner']]);
        $this->middleware('permission:practical_examiner_dashboard', ['only' => ['practical_examiner']]);
        $this->middleware('permission:printerdashboard', ['only' => ['printer']]);
        $this->middleware('permission:marksheet_verification_dashboard', ['only' => ['marksheetverificationdashboard']]);
        $this->middleware('permission:rsos_officer_grade_1_dashboard', ['only' => ['rsos_officer_grade_1']]);
        $this->middleware('permission:rsos_officer_grade_2_dashboard', ['only' => ['rsos_officer_grade_2']]);
        $this->middleware('permission:rsos_officer_grade_3_dashboard', ['only' => ['rsos_officer_grade_3']]);
        $this->middleware('permission:rsos_officer_grade_4_dashboard', ['only' => ['rsos_officer_grade_4']]);
        $this->middleware('permission:rsos_officer_grade_5_dashboard', ['only' => ['rsos_officer_grade_5']]);
        $this->middleware('permission:publication_dept_dashboard', ['only' => ['publication_dept']]);
    }

    public function index()
    {
        // code here
    }

    public function dashboard(Request $request)
    {

        $user_id = @Auth::user()->id;
        $result = DB::table('model_has_roles')
            ->where('model_id', $user_id)
            ->get();


        if (count($result) == 0) {
            return Redirect::to('/')->send()->with('error', "Your ssoid still not mapped with any role,Please contact with RSOS.");

        }
        // $current_admission_session_id = Config::get('global.current_admission_session_id');
        // Session::put("current_admission_sessions",$current_admission_session_id);


        if (count($request->all()) > 0) {
            $role_id = $request->role;
            /*if(!empty($request->enrollment)){
                Session::put('selected_student_enrollment_by_student',$request->enrollment);
            }*/
            //custom code start
            if (@$request->student_multi_login && $request->student_multi_login == true) {
                $ssoid = Auth::guard('student')->user()->ssoid;

                Session::flush();
                Auth::logout();

                $password = '123456789';
                $getstudentssoid = Student::where('ssoid', $ssoid)->where('id', @$request->enrollment)->first('ssoid');


                if (!empty($getstudentssoid)) {
                    $studentCredentials = $credentials = (['id' => @$request->enrollment, 'password' => $password]);
                    if (Auth::guard('student')->attempt($studentCredentials)) {
                        Session::put('role_id', $role_id);
                        //$enrollmentNumber = Auth::guard('student')->user()->enrollment;
						$enrollmentNumber = Auth::guard('student')->user()->id;
                        Session::put("selected_student_enrollment_by_student", $enrollmentNumber);
                        //Auth::guard('student')->user()->role_id = Session::get('role_id');
                        $user_id = @Auth::user()->id;
                    } else {

                    }
                } else {

                }
            }

            //custom code end


            $result = DB::table('model_has_roles')
                ->where('role_id', $role_id)
                ->where('model_id', $user_id)
                ->first();

            Session::put('role_id', $result->role_id);
            $this->setUpdatedRoleBasedSessionYear($role_id);
            $roleroutes = $this->_getRoleRoute($role_id);
            return redirect()->route($roleroutes->route);
            if ($result->role_id == $roleroutes->role_id) {
            }
        }
        $custom_component_obj = new CustomComponent;
        $result = $custom_component_obj->getRolesByUserId($user_id);
        $this->setUpdatedRoleBasedSessionYear($result[0]->role_id);
        $resultCount = count($result);
        if ($resultCount == 1) {
            $roleroutes = $this->_getRoleRoute($result[0]->role_id);
            if ($roleroutes->role_id == 71) {
                $roleroutes->route = "printerdashboard";
            }
            Session::put('role_id', $roleroutes->role_id);
            return redirect()->route($roleroutes->route);
        } else {
            return view('application.dashboard.allroledashboard', compact('result'));
        }
    }

   public function applicationdashboard(Request $request){ 
		$custom_component_obj = new CustomComponent;
        $role_id = @Session::get('role_id');
		$super_admin_id = Config::get("global.super_admin_id"); 
		
		$developer_admin  = Config::get("global.developer_admin");
        $current_session=CustomHelper::_get_selected_sessions();
        $combo_name = 'admission_sessions';$admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';$exam_month = $this->master_details($combo_name);
        $combo_name = 'application_dashboard';$application_dashboard = $this->master_details($combo_name);
        $counter = 0;
        $allowShow = false;
		if($role_id == $super_admin_id || $role_id == $developer_admin ){
            $allowShow = true;
            $counter = $custom_component_obj->countStudentPending();
        }

       
		// $total_registered_student = $custom_component_obj->getallStudentCount();
		// $total_lock_Submit_student = $custom_component_obj->getallStudentLockSubmitCount();       
		// $get_Student_payment_Count = $custom_component_obj->getallStudentpaymentCount();
		// $get_Student_payment_not_pay_Count = $custom_component_obj->getallStudentpaymentnotpayCount();
        // $get_Student_zero_fees_payment_Count = $custom_component_obj->getallStudentzerofeespaymentCount();
        // $eligible_get_Student_payment_not_pay_Count=$custom_component_obj->getallStudentEligibleCount();
        
        $combo_name = 'exam_month';$exam_monthall = $this->master_details($combo_name);
		$supp=array();

		$supp_exam_month = Config::get('global.supp_current_admission_exam_month');
		//$combo_name = 'exam_month';$exam_month = $this->master_details($combo_name);
		//if($supp_exam_month == 1){
            $aicenter_mapped_data = array();
            $exam_month = 1;
            $supp[$exam_month]['supplementary_total_registered_student'] =  $custom_component_obj->getsupplementaryallStudentCount(null,$exam_month);
            $supp[$exam_month]['supplementary_total_lock_Submit_student'] = $custom_component_obj->getsupplementaryallStudentLockSubmitCount(null,$exam_month);
            $supp[$exam_month]['supplementary_get_Student_payment_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentCount(null,$exam_month);
            $supp[$exam_month]['supplementary_get_Student_payment_not_pay_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentnotpayCount(null,$exam_month);
            $supp[$exam_month]['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallEligibleStudentpaymentCount(null,$exam_month);
            $supp[$exam_month]['supplementary_get_Eligiable_Students'] = $custom_component_obj->getEligibleSupplementaryallStudentCount(null,$exam_month);

            $supp[$exam_month]['supplementary_get_aicenter_not_verfied'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'1');
            $supp[$exam_month]['supplementary_get_aicenter_verified'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'2');
			$supp[$exam_month]['supplementary_get_aicenter_rejected'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'3');
			$supp[$exam_month]['supplementary_get_aicenter_clarification'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'4');

            $supp[$exam_month]['supplementary_get_department_not_verfied'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month,'1');
            $supp[$exam_month]['supplementary_get_department_verified'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month,'2');
            $supp[$exam_month]['supplementary_get_department_rejected'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month,'3');
			$supp[$exam_month]['supplementary_get_department_clarification'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month,'4');
            


        //}else{
            $exam_month = 2;
            $supp[$exam_month]['supplementary_total_registered_student'] =  $custom_component_obj->getsupplementaryallStudentCount(null,$exam_month);
            $supp[$exam_month]['supplementary_total_lock_Submit_student'] = $custom_component_obj->getsupplementaryallStudentLockSubmitCount(null,$exam_month);
            $supp[$exam_month]['supplementary_get_Student_payment_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentCount(null,$exam_month);
            $supp[$exam_month]['supplementary_get_Student_payment_not_pay_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentnotpayCount(null,$exam_month);
            $supp[$exam_month]['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallEligibleStudentpaymentCount(null,$exam_month);
            $supp[$exam_month]['supplementary_get_Eligiable_Students'] = $custom_component_obj->getEligibleSupplementaryallStudentCount(null,$exam_month);
            
            $supp[$exam_month]['supplementary_get_aicenter_not_verfied'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'1');
            $supp[$exam_month]['supplementary_get_aicenter_verified'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'2');
			$supp[$exam_month]['supplementary_get_aicenter_rejected'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'3');
			$supp[$exam_month]['supplementary_get_aicenter_clarification'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'4');

            $supp[$exam_month]['supplementary_get_department_not_verfied'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month,'1');
            $supp[$exam_month]['supplementary_get_department_verified'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month,'2');
            $supp[$exam_month]['supplementary_get_department_rejected'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month,'3');
			$supp[$exam_month]['supplementary_get_department_clarification'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month,'4');
			

        //}
        /*
		*/
        /*reval dashboard start*/
		
		    $aicenter_mapped_data = array();
            $exam_month = 1;
            $reval[$exam_month]['reval_total_registered_student'] =  $custom_component_obj->getRevalallStudentCount(null,$exam_month);
            $reval[$exam_month]['reval_total_lock_Submit_student'] = $custom_component_obj->getRevalallStudentLockSubmitCount(null,$exam_month);
            $reval[$exam_month]['reval_get_Student_payment_Count'] = $custom_component_obj->getRevalallStudentpaymentCount(null,$exam_month);
            $reval[$exam_month]['reval_get_Student_payment_not_pay_Count'] = $custom_component_obj->getRevalallStudentpaymentnotpayCount(null,$exam_month);
            $reval[$exam_month]['reval_get_Eligiable_Students'] = $custom_component_obj->getEligibleRevalaryallStudentCount(null,$exam_month);
			
			
			$exam_month = 2;
            $reval[$exam_month]['reval_total_registered_student'] =  $custom_component_obj->getRevalallStudentCount(null,$exam_month);
            $reval[$exam_month]['reval_total_lock_Submit_student'] = $custom_component_obj->getRevalallStudentLockSubmitCount(null,$exam_month);
            $reval[$exam_month]['reval_get_Student_payment_Count'] = $custom_component_obj->getRevalallStudentpaymentCount(null,$exam_month);
            $reval[$exam_month]['reval_get_Student_payment_not_pay_Count'] = $custom_component_obj->getRevalallStudentpaymentnotpayCount(null,$exam_month);
            $reval[$exam_month]['reval_get_Eligiable_Students'] = $custom_component_obj->getEligibleRevalaryallStudentCount(null,$exam_month);
        /* End */
		
		
		   /*change request dashboard start*/
            $exam_month = 1;
            $changerequest[$exam_month]['change_request_total_registered_student'] =  $custom_component_obj->getchangerequestallStudentCount($exam_month);
            $changerequest[$exam_month]['change_request_total_approved_registered_student'] = $custom_component_obj->getchangerequestallApprovedStudentCount($exam_month);
		  $changerequest[$exam_month]['change_request_total_generated'] = $custom_component_obj->getchangerequesttotalgeneratedCount($exam_month);
			$changerequest[$exam_month]['change_request_department_approval'] = $custom_component_obj->getchangerequestdepartmentapprovalCount($exam_month);
			$changerequest[$exam_month]['change_request_student_update_applications'] = $custom_component_obj->getchangerequeststudentupdateapplicationsCount($exam_month);
			$changerequest[$exam_month]['change_request_student_approval_not_click_update_applications'] = $custom_component_obj->getchangerequeststudentnotclickupdateapplications($exam_month);
			$changerequest[$exam_month]['change_request_student_not_locksumbitted'] = $custom_component_obj->getchangerequestnotlocksumbittedCount($exam_month);
			$changerequest[$exam_month]['change_request_student_locksumbitted'] = $custom_component_obj->getchangerequestlocksumbittedCount($exam_month);
			$changerequest[$exam_month]['change_request_student_locksumbitted_fees_not_pay'] = $custom_component_obj->getchangerequestlocksumbittedfeesnotpayCount($exam_month);
			$changerequest[$exam_month]['change_request_student_completed'] = $custom_component_obj->getchangerequeststudentcompleted($exam_month);
			
           
			$exam_month = 2;
            $changerequest[$exam_month]['change_request_total_registered_student'] =  $custom_component_obj->getchangerequestallStudentCount($exam_month);
            $changerequest[$exam_month]['change_request_total_approved_registered_student'] = $custom_component_obj->getchangerequestallApprovedStudentCount($exam_month);
		  $changerequest[$exam_month]['change_request_total_generated'] = $custom_component_obj->getchangerequesttotalgeneratedCount($exam_month);
			$changerequest[$exam_month]['change_request_department_approval'] = $custom_component_obj->getchangerequestdepartmentapprovalCount($exam_month);
			$changerequest[$exam_month]['change_request_student_update_applications'] = $custom_component_obj->getchangerequeststudentupdateapplicationsCount($exam_month);
			$changerequest[$exam_month]['change_request_student_approval_not_click_update_applications'] = $custom_component_obj->getchangerequeststudentnotclickupdateapplications($exam_month);
			$changerequest[$exam_month]['change_request_student_not_locksumbitted'] = $custom_component_obj->getchangerequestnotlocksumbittedCount($exam_month);
			$changerequest[$exam_month]['change_request_student_locksumbitted'] = $custom_component_obj->getchangerequestlocksumbittedCount($exam_month);
			$changerequest[$exam_month]['change_request_student_locksumbitted_fees_not_pay'] = $custom_component_obj->getchangerequestlocksumbittedfeesnotpayCount($exam_month);
			$changerequest[$exam_month]['change_request_student_completed'] = $custom_component_obj->getchangerequeststudentcompleted($exam_month);
			
          /* change request dashboard  End */
		 
		    /*supp change request dashboard start*/
            $exam_month = 1;
            $suppchangerequest[$exam_month]['supp_change_request_total_registered_student'] =  $custom_component_obj->getsuppchangerequestallStudentCount($exam_month);
            $suppchangerequest[$exam_month]['supp_change_request_total_approved_registered_student'] = $custom_component_obj->getsuppchangerequestallApprovedStudentCount($exam_month);
           
			$exam_month = 2;
            $suppchangerequest[$exam_month]['change_request_total_registered_student'] =  $custom_component_obj->getsuppchangerequestallStudentCount($exam_month);
            $suppchangerequest[$exam_month]['change_request_total_approved_registered_student'] = $custom_component_obj->getsuppchangerequestallApprovedStudentCount($exam_month);
          /* supp change request dashboard  End */
		
        $total_registered_examiner_maps = $total_registered_examiner = 0;
       
        $practical_component_obj = new PracticalCustomComponent;
        $total_registered_examiner = $practical_component_obj->getDeoExaminerListOrCount('',true);
		

        $role_id = Session::get('role_id');
        $verifier_id = config("global.verifier_id"); 
        $super_admin_id = config("global.super_admin_id");
        $academicofficer_id = config('global.academicofficer_id');
        
        /* Start */
            $applicationVerifyCount['1']['status'] = $applicationCount['1']['status'] = $application_dashboard[1];
            $applicationVerifyCount['2']['status'] = $applicationCount['2']['status'] = $application_dashboard[2];
             $applicationVerifyCount['total']['status'] = $applicationCount['total']['status'] = $application_dashboard[3];
			 // dd($applicationCount);

            if($applicationCount['1']['status'] == @$application_dashboard[1]){
                $exam_month = 1;
               $applicationCount['1']['total_registered_student'] = $custom_component_obj->getallStudentCount(null,1);

               $applicationCount['1']['total_lock_Submit_student'] =$custom_component_obj->getallStudentLockSubmitCount(null,1);
                $applicationCount['1']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount(null,1);
                $applicationCount['1']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount(null,1);
                $applicationCount['1']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount(null,1);
                $applicationCount['1']['get_sso_updated_student_count'] = $custom_component_obj->getallStudentWhoseSSOIdMapped(null,1);
                $applicationCount['1']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount(null,1); 
                
              
                $applicationVerifyCount[$exam_month]['fresh_get_verifier_pending'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$verifier_id,1);
				
                $applicationVerifyCount[$exam_month]['fresh_get_verifier_accepted'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$verifier_id,7);
                $applicationVerifyCount[$exam_month]['fresh_get_verifier_objected'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$verifier_id,8);
                $applicationVerifyCount[$exam_month]['fresh_get_verifier_clarification_first_appeal'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$verifier_id,9);
 
                $applicationVerifyCount[$exam_month]['fresh_get_verifier_all'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$verifier_id,'all');
                 
               $applicationVerifyCount[$exam_month]['fresh_get_ao_pending'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$academicofficer_id,1);
                
                $applicationVerifyCount[$exam_month]['fresh_get_ao_verfied'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$academicofficer_id,2);
                $applicationVerifyCount[$exam_month]['fresh_get_ao_rejected'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$academicofficer_id,3); 
                $applicationVerifyCount[$exam_month]['fresh_get_ao_clarification_first_appeal'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$academicofficer_id,9);

                $applicationVerifyCount[$exam_month]['fresh_get_ao_request_verifier_to_dept'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$academicofficer_id,5);
                $applicationVerifyCount[$exam_month]['fresh_get_ao_dept_clarification_to_verifier'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$academicofficer_id,6);
                $applicationVerifyCount[$exam_month]['fresh_get_ao_all'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$academicofficer_id,'all');
              
                $applicationVerifyCount[$exam_month]['fresh_get_ao_agree_with_verifier'] = $custom_component_obj->getAOAgreeeOrNotWithVerifier($exam_month,1);
				$applicationVerifyCount[$exam_month]['fresh_get_ao_not_agree_with_verifier'] = $custom_component_obj->getAOAgreeeOrNotWithVerifier($exam_month,2);
                
             
                // dd($super_admin_id);
                $applicationVerifyCount[$exam_month]['fresh_get_dept_pending'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$super_admin_id,1);

                      
                $applicationVerifyCount[$exam_month]['fresh_get_dept_verfied'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$super_admin_id,2);

                $applicationVerifyCount[$exam_month]['fresh_get_dept_rejected'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$super_admin_id,3);
                
                $applicationVerifyCount[$exam_month]['fresh_get_dept_clarification_second_appeal'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$super_admin_id,10); 
                $applicationVerifyCount[$exam_month]['fresh_get_dept_request_verifier_to_dept'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$super_admin_id,5);
                $applicationVerifyCount[$exam_month]['fresh_get_dept_dept_clarification_to_verifier'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$super_admin_id,6);

                $applicationVerifyCount[$exam_month]['fresh_get_dept_all'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$super_admin_id,'all'); 
                
                $applicationVerifyCount[$exam_month]['fresh_get_dept_is_permanent_rejected_by_dept'] = $custom_component_obj->getDeptVerificationPermanantRejectedPart($exam_month);
				
				//$applicationVerifyCount[$exam_month]['fresh_get_ao_rejected'] = $custom_component_obj->getAicenterVerificationPart($exam_month,3);
               


               // dd($applicationVerifyCount[$exam_month]['fresh_get_dept_all'] );
                
            }
            // dd($applicationVerifyCount);
            if(isset($applicationCount['2']['status']) && $applicationCount['2']['status'] == @$application_dashboard[2]){
                $exam_month = 2;
                $applicationCount['2']['total_registered_student'] = $custom_component_obj->getallStudentCount(null,2);
                $applicationCount['2']['total_lock_Submit_student'] =$custom_component_obj->getallStudentLockSubmitCount(null,2);
                $applicationCount['2']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount(null,2);   
                $applicationCount['2']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount(null,2);
                $applicationCount['2']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount(null,2);
                $applicationCount['2']['get_sso_updated_student_count'] = $custom_component_obj->getallStudentWhoseSSOIdMapped(null,2);
                $applicationCount['2']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount(null,2);
                
				
                $applicationVerifyCount[$exam_month]['fresh_get_verifier_pending'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$verifier_id,1);
                
                $applicationVerifyCount[$exam_month]['fresh_get_verifier_accepted'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$verifier_id,7);
                $applicationVerifyCount[$exam_month]['fresh_get_verifier_objected'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$verifier_id,8);
                $applicationVerifyCount[$exam_month]['fresh_get_verifier_all'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$verifier_id,'all');

                $applicationVerifyCount[$exam_month]['fresh_get_ao_agree_with_verifier'] = $custom_component_obj->getAOAgreeeOrNotWithVerifier($exam_month,1);
                $applicationVerifyCount[$exam_month]['fresh_get_ao_not_agree_with_verifier'] = $custom_component_obj->getAOAgreeeOrNotWithVerifier($exam_month,2);

                $applicationVerifyCount[$exam_month]['fresh_get_verifier_clarification_first_appeal'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$verifier_id,9);

                $applicationVerifyCount[$exam_month]['fresh_get_ao_pending'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$academicofficer_id,1);
                $applicationVerifyCount[$exam_month]['fresh_get_ao_verfied'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$academicofficer_id,2);
                $applicationVerifyCount[$exam_month]['fresh_get_ao_rejected'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$academicofficer_id,3);
				
				  
                $applicationVerifyCount[$exam_month]['fresh_get_ao_clarification_first_appeal'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$academicofficer_id,9);
                $applicationVerifyCount[$exam_month]['fresh_get_ao_request_verifier_to_dept'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$academicofficer_id,5);
                $applicationVerifyCount[$exam_month]['fresh_get_ao_dept_clarification_to_verifier'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$academicofficer_id,6);
                $applicationVerifyCount[$exam_month]['fresh_get_ao_all'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$academicofficer_id,'all');
                
                
                $applicationVerifyCount[$exam_month]['fresh_get_dept_pending'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$super_admin_id,1);
                $applicationVerifyCount[$exam_month]['fresh_get_dept_verfied'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$super_admin_id,2);
                $applicationVerifyCount[$exam_month]['fresh_get_dept_rejected'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$super_admin_id,3);$applicationVerifyCount[$exam_month]['fresh_get_dept_clarification_second_appeal'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$super_admin_id,10);
				
                $applicationVerifyCount[$exam_month]['fresh_get_dept_request_verifier_to_dept'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$super_admin_id,5);
                $applicationVerifyCount[$exam_month]['fresh_get_dept_dept_clarification_to_verifier'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$super_admin_id,6);
				
                $applicationVerifyCount[$exam_month]['fresh_get_dept_all'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data,$exam_month,$super_admin_id,'all');
                $applicationVerifyCount[$exam_month]['fresh_get_dept_is_permanent_rejected_by_dept'] = $custom_component_obj->getDeptVerificationPermanantRejectedPart($exam_month);
				//$applicationVerifyCount[$exam_month]['fresh_get_ao_rejected'] = $custom_component_obj->getAicenterVerificationPart($exam_month,3);
            }
          
            if(isset($applicationCount['total']['status']) && $applicationCount['total']['status'] == @$application_dashboard[3]){
                $fld="total_registered_student";$applicationCount['total'][$fld] = $applicationCount['1'][$fld]  + $applicationCount['2'][$fld];
                $fld="total_lock_Submit_student";$applicationCount['total'][$fld] = $applicationCount['1'][$fld]  + $applicationCount['2'][$fld];
                $fld="get_Student_payment_not_pay_Count";$applicationCount['total'][$fld] = $applicationCount['1'][$fld]  + $applicationCount['2'][$fld];
                $fld="get_Student_zero_fees_payment_Count";$applicationCount['total'][$fld] = $applicationCount['1'][$fld]  + $applicationCount['2'][$fld];
                $fld="get_Student_payment_Count";$applicationCount['total'][$fld] = $applicationCount['1'][$fld]  + $applicationCount['2'][$fld];
                $fld="eligible_get_Student_payment_not_pay_Count";$applicationCount['total'][$fld] = $applicationCount['1'][$fld]  + $applicationCount['2'][$fld]; 
                $fld="get_sso_updated_student_count";$applicationCount['total'][$fld] = $applicationCount['1'][$fld]  + $applicationCount['2'][$fld];
                
                
                $fld="fresh_get_verifier_pending";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld]; 
                $fld="fresh_get_verifier_accepted";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld]; 
                $fld="fresh_get_verifier_objected";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld]; 
                $fld="fresh_get_verifier_clarification_first_appeal";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld]; 
                $fld="fresh_get_verifier_all";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld];
                $fld="fresh_get_ao_agree_with_verifier";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld];
                $fld="fresh_get_ao_not_agree_with_verifier";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld];
                
                $fld="fresh_get_ao_pending";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld]; 
                $fld="fresh_get_ao_verfied";$applicationVerifyCount['total'][$fld] = @$applicationVerifyCount['1'][$fld]  + @$applicationVerifyCount['2'][$fld]; 
                $fld="fresh_get_ao_rejected";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld]; 
                $fld="fresh_get_ao_clarification_first_appeal";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld]; 
                $fld="fresh_get_ao_request_verifier_to_dept";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld]; 
                $fld="fresh_get_ao_dept_clarification_to_verifier";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld];
                $fld="fresh_get_ao_all";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld];
                
                
                $fld="fresh_get_dept_pending";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld]; 
                $fld="fresh_get_dept_verfied";$applicationVerifyCount['total'][$fld] = @$applicationVerifyCount['1'][$fld]  + @$applicationVerifyCount['2'][$fld]; 
                $fld="fresh_get_dept_rejected";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld]; 
                $fld="fresh_get_dept_clarification_second_appeal";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld]; 
                $fld="fresh_get_dept_request_verifier_to_dept";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld]; 
                $fld="fresh_get_dept_dept_clarification_to_verifier";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld];
                $fld="fresh_get_dept_all";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld];
                $fld="fresh_get_dept_is_permanent_rejected_by_dept";$applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld]  + $applicationVerifyCount['2'][$fld];$applicationVerifyCount['total']['fresh_get_ao_rejected'] = 
			$applicationVerifyCount['1']['fresh_get_ao_rejected']+ $applicationVerifyCount['2']['fresh_get_ao_rejected'];
            } 
        /* End */


        // dd($applicationVerifyCount);
        /*Practical mapped dashboard start*/
		    /*$aicenter_mapped_data = array();
            $exam_month = 1;
            $practicalmapped[$exam_month]['Practical_mapped_registered_count'] =  $custom_component_obj->getPracticalmappedallCount($exam_month);
			$practicalmapped[$exam_month]['Practical_mapped_count'] =  $custom_component_obj->getPracticalmappedCount($exam_month);
			$practicalmapped[$exam_month]['Practical_not_mapped_count'] =  $custom_component_obj->getPracticalnotmappedCount($exam_month);/*
            
        /* Practical mapped End */
		ksort($applicationCount);
		
		
       
		$auth_user_id = @Auth::user()->id; 
        $total_registered_examiner_maps = $custom_component_obj->getPracticalExaminerMaps($auth_user_id);
		return view('application.dashboard.superadmindashboard',compact('exam_month','allowShow','applicationVerifyCount','current_session','admission_sessions','counter','supp','reval','exam_monthall','applicationCount','changerequest','suppchangerequest'));
	}

    public function examcenter(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $user_id = @Auth::user()->id;

        // Code Due to examcenter dashboard count mismatch
        $custom_component_obj = new CustomComponent;
        $examCenterData = $custom_component_obj->getExamcenterDataByUserId($user_id);
        // Code Due to examcenter dashboard count mismatch

        //  $total_ai_codes_mapped_with_us = $custom_component_obj->getAiCodeByExamCenterDetailId($user_id);
        $total_ai_codes_mapped_with_us = $custom_component_obj->getAiCodeByExamCenterDetailId(@$examCenterData->id);

        return view('application.dashboard.examcenterdashboard', compact('total_ai_codes_mapped_with_us'));
    }

    public function examiner(Request $request)
    {
        return view('application.dashboard.examinerdashboard');
    }

    public function theroy_examiner(Request $request)
    {
        $theory_custom_component_obj = new ThoeryCustomComponent;
        $master = count($theory_custom_component_obj->allotinsubjectcopies(null, false));
        return view('application.dashboard.theroyexaminerdashboard', compact('master'));
    }

    public function practical_examiner(Request $request)
    {
        $PracticalCustomComponent = new PracticalCustomComponent;
        $total_examiner_mapping_count = $PracticalCustomComponent->getPracticalMappedCenterListOrCount($form_id = '', true);
        return view('application.dashboard.practicalexaminerdashboard', compact('total_examiner_mapping_count'));
    }

    public function examination_department(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $total_exam_centers = $custom_component_obj->getExamCentersCount();
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_monthall = $this->master_details($combo_name);
        $combo_name = 'application_dashboard';
        $application_dashboard = $this->master_details($combo_name);
        $current_session = CustomHelper::_get_selected_sessions();
        $supp = array();
        $aicenter_mapped_data = array();
        $change_request_current_supp_exam_month_id = Config::get("global.supp_current_admission_exam_month");
        $supp_exam_month = Config::get('global.supp_current_admission_exam_month');

        if ($supp_exam_month == 1) {
            $exam_month = $supp_exam_month;
            $supp[$exam_month]['supplementary_total_registered_student'] = $custom_component_obj->getsupplementaryallStudentCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_total_lock_Submit_student'] = $custom_component_obj->getsupplementaryallStudentLockSubmitCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_get_Student_payment_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_get_Student_payment_not_pay_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentnotpayCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_get_Eligiable_Students'] = $custom_component_obj->getEligibleSupplementaryallStudentCount($aicenter_mapped_data, $exam_month);
            // $supp[$exam_month]['supplementary_get_aicenter_not_verfied'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'1');
            // $supp[$exam_month]['supplementary_get_aicenter_verified'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'2');
            // $supp[$exam_month]['supplementary_get_aicenter_rejected'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'3');
            // $supp[$exam_month]['supplementary_get_aicenter_clarification'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'4');

            $supp[$exam_month]['supplementary_get_department_not_verfied'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month, '1');
            $supp[$exam_month]['supplementary_get_department_verified'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month, '2');
            $supp[$exam_month]['supplementary_get_department_rejected'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month, '3');
            $supp[$exam_month]['supplementary_get_department_clarification'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month, '4');
            $supp[$exam_month]['supplementary_get_department_per_rej'] = '';

        } else {
            $exam_month = $supp_exam_month;
            $supp[$exam_month]['supplementary_total_registered_student'] = $custom_component_obj->getsupplementaryallStudentCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_total_lock_Submit_student'] = $custom_component_obj->getsupplementaryallStudentLockSubmitCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_get_Student_payment_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_get_Student_payment_not_pay_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentnotpayCount($aicenter_mapped_data, $exam_month);
            $supp[$exam_month]['supplementary_get_Eligiable_Students'] = $custom_component_obj->getEligibleSupplementaryallStudentCount($aicenter_mapped_data, $exam_month);
            // $supp[$exam_month]['supplementary_get_aicenter_not_verfied'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'1');
            // $supp[$exam_month]['supplementary_get_aicenter_verified'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'2');
            // $supp[$exam_month]['supplementary_get_aicenter_rejected'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'3');
            // $supp[$exam_month]['supplementary_get_aicenter_clarification'] = $custom_component_obj->getPendingAiCenterLevelSupplementaryallStudentCount($aicenter_mapped_data,$exam_month,'4');

            $supp[$exam_month]['supplementary_get_department_not_verfied'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month, '1');
            $supp[$exam_month]['supplementary_get_department_verified'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month, '2');
            $supp[$exam_month]['supplementary_get_department_rejected'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month, '3');
            $supp[$exam_month]['supplementary_get_department_clarification'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month, '4');

            /* Per start */
            $selected_session = CustomHelper::_get_selected_sessions();
            $conditions['exam_year'] = $selected_session;
            if (!empty($exam_month) && $exam_month != null) {
                $conditions['exam_month'] = $exam_month;
            }
            $conditions['is_per_rejected'] = 1;
            $supplementary_get_department_per_rej = Supplementary::where($conditions)->count();
            $supp[$exam_month]['supplementary_get_department_per_rej'] = $supplementary_get_department_per_rej;
            /* Per end */


        }

        $applicationCount['1']['status'] = $application_dashboard[1];
        $applicationCount['2']['status'] = $application_dashboard[2];
        $applicationCount['total']['status'] = $application_dashboard[3];

        if ($applicationCount['1']['status'] == $application_dashboard[1]) {
            $applicationCount['1']['total_registered_student'] = $custom_component_obj->getallStudentCount(null, 1);
            $applicationCount['1']['total_lock_Submit_student'] = $custom_component_obj->getallStudentLockSubmitCount(null, 1);
            $applicationCount['1']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount(null, 1);
            $applicationCount['1']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount(null, 1);
            $applicationCount['1']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount(null, 1);
            $applicationCount['1']['get_sso_updated_student_count'] = $custom_component_obj->getallStudentWhoseSSOIdMapped(null, 1);
            $applicationCount['1']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount(null, 1);
        }

        if ($applicationCount['2']['status'] == $application_dashboard[2]) {
            $applicationCount['2']['total_registered_student'] = $custom_component_obj->getallStudentCount(null, 2);
            $applicationCount['2']['total_lock_Submit_student'] = $custom_component_obj->getallStudentLockSubmitCount(null, 2);
            $applicationCount['2']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount(null, 2);
            $applicationCount['2']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount(null, 2);
            $applicationCount['2']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount(null, 2);
            $applicationCount['2']['get_sso_updated_student_count'] = $custom_component_obj->getallStudentWhoseSSOIdMapped(null, 2);
            $applicationCount['2']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount(null, 2);
        }
        if ($applicationCount['total']['status'] == $application_dashboard[3]) {
            $fld = "total_registered_student";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "total_lock_Submit_student";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_payment_not_pay_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_zero_fees_payment_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_payment_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "eligible_get_Student_payment_not_pay_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_sso_updated_student_count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
        }

        /* End */

        /*supp change request dashboard start*/
        $exam_month = 1;
        $suppchangerequest[$exam_month]['supp_change_request_total_registered_student'] = $custom_component_obj->getsuppchangerequestallStudentCount($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_total_approved_registered_student'] = $custom_component_obj->getsuppchangerequestallApprovedStudentCount($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_total_generated'] = $custom_component_obj->getsuppchangerequestalltotalapply($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_department_approval'] = $custom_component_obj->getsuppchangerequestapproveddeparmentwise($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_student_update_applications'] = $custom_component_obj->getsuppchangerequestupdateapplicationsbutton($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_student_approval_not_click_update_applications'] = $custom_component_obj->getsuppchangerequeststudentnotclickupdateapplications($exam_month);

        $suppchangerequest[$exam_month]['supp_change_request_student_not_locksumbitted'] = $custom_component_obj->getsuppchangerequestallnotlocksumbittedStudentCount($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_student_locksumbitted'] = $custom_component_obj->getsuppchangerequestalllocksumbittedStudentCount($exam_month);

        $suppchangerequest[$exam_month]['supp_change_request_student_locksumbitted_fees_not_pay'] = $custom_component_obj->getsuppchangerequestallnotlocksumbittedfeespayStudentCount($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_student_completed'] = $custom_component_obj->getsuppchangerequeststudentsitecompleted($exam_month);

        $exam_month = 2;
        $suppchangerequest[$exam_month]['supp_change_request_total_registered_student'] = $custom_component_obj->getsuppchangerequestallStudentCount($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_total_approved_registered_student'] = $custom_component_obj->getsuppchangerequestallApprovedStudentCount($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_total_generated'] = $custom_component_obj->getsuppchangerequestalltotalapply($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_department_approval'] = $custom_component_obj->getsuppchangerequestapproveddeparmentwise($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_student_update_applications'] = $custom_component_obj->getsuppchangerequestupdateapplicationsbutton($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_student_approval_not_click_update_applications'] = $custom_component_obj->getsuppchangerequeststudentnotclickupdateapplications($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_student_not_locksumbitted'] = $custom_component_obj->getsuppchangerequestallnotlocksumbittedStudentCount($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_student_locksumbitted'] = $custom_component_obj->getsuppchangerequestalllocksumbittedStudentCount($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_student_locksumbitted_fees_not_pay'] = $custom_component_obj->getsuppchangerequestallnotlocksumbittedfeespayStudentCount($exam_month);
        $suppchangerequest[$exam_month]['supp_change_request_student_completed'] = $custom_component_obj->getsuppchangerequeststudentsitecompleted($exam_month);

        /* supp change request dashboard  End */


        //dd($exam_monthall);
        return view('application.dashboard.examination_department_dashboard', compact('current_session', 'admission_sessions', 'exam_monthall', 'supp', 'total_exam_centers', 'applicationCount', 'suppchangerequest', 'change_request_current_supp_exam_month_id'));
    }

    public function printer(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $role_id = @Session::get('role_id');
        $super_admin_id = Config::get("global.super_admin_id");
        $developer_admin = Config::get("global.developer_admin");
        $current_session = CustomHelper::_get_selected_sessions();
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $combo_name = 'application_dashboard';
        $application_dashboard = $this->master_details($combo_name);
        $counter = 0;
        $allowShow = false;
        if ($role_id == $super_admin_id || $role_id == $developer_admin) {
            $allowShow = true;
            $counter = $custom_component_obj->countStudentPending();
        }

        $combo_name = 'exam_month';
        $exam_monthall = $this->master_details($combo_name);
        $supp = array();
        $exam_month = 2;
        $supp[$exam_month]['supplementary_total_registered_student'] = $custom_component_obj->getsupplementaryallStudentCount(null, $exam_month);
        $supp[$exam_month]['supplementary_total_lock_Submit_student'] = $custom_component_obj->getsupplementaryallStudentLockSubmitCount(null, $exam_month);
        $supp[$exam_month]['supplementary_get_Student_payment_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentCount(null, $exam_month);
        $supp[$exam_month]['supplementary_get_Student_payment_not_pay_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentnotpayCount(null, $exam_month);
        $supp[$exam_month]['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallEligibleStudentpaymentCount(null, $exam_month);
        $supp[$exam_month]['supplementary_get_Eligiable_Students'] = $custom_component_obj->getEligibleSupplementaryallStudentCount(null, $exam_month);

        $total_registered_examiner_maps = $total_registered_examiner = 0;

        $practical_component_obj = new PracticalCustomComponent;
        $total_registered_examiner = $practical_component_obj->getDeoExaminerListOrCount('', true);
        /* Start */
        $applicationCount = array();
        $applicationCount['total']['status'] = true;
        if ($applicationCount['total']['status'] == true) {
            $applicationCount['total']['total_genrated_application'] = $custom_component_obj->getMarksheetMigrationData();
            $applicationCount['total']['total_lock_and_sunmitted_application'] = $custom_component_obj->getMarksheetMigrationData(null, 1, null);
            $applicationCount['total']['total_fee_paid_application'] = $custom_component_obj->getMarksheetMigrationData(null, 1, 1);
            $applicationCount['total']['total_genrated_revised_application'] = $custom_component_obj->getMarksheetMigrationData(1);
            $applicationCount['total']['total_lock_and_sunmitted_revised_application'] = $custom_component_obj->getMarksheetMigrationData(1, 1, null);
            $applicationCount['total']['total_fee_paid_revised_application'] = $custom_component_obj->getMarksheetMigrationData(1, 1, 1);
            $applicationCount['total']['total_genrated_duplicate_application'] = $custom_component_obj->getMarksheetMigrationData(2);
            $applicationCount['total']['total_lock_and_sunmitted_duplicate_application'] = $custom_component_obj->getMarksheetMigrationData(2, 1, null);
            $applicationCount['total']['total_fee_paid_duplicate_application'] = $custom_component_obj->getMarksheetMigrationData(2, 1, 1);

        }
        //@dd($applicationCount);
        /* End */

        $auth_user_id = @Auth::user()->id;
        $total_registered_examiner_maps = $custom_component_obj->getPracticalExaminerMaps($auth_user_id);

        return view('application.dashboard.printerdashboard', compact('exam_month', 'allowShow', 'current_session', 'admission_sessions', 'counter', 'supp', 'exam_monthall', 'applicationCount'));

    }

    public function marksheetverificationdashboard(Request $request)
    {
        return view('application.dashboard.marksheetverificationdashboard');
    }

    public function examination_admin(Request $request)
    {
        return view('application.dashboard.examinationadmindashboard');
    }

    public function oldoldapplication_verifyer_admin(Request $request)
    {
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_monthall = $this->master_details($combo_name);
        $total_registered_examiner_maps = $total_registered_examiner = 0;
        $current_session = CustomHelper::_get_selected_sessions();
        $allowShow = true;
        $custom_component_obj = new CustomComponent;
        $counter = $custom_component_obj->countStudentPending();
        /* Start */

        $applicationVerifyCount['total']['status'] = $applicationCount['total']['status'] = $application_dashboard[3];


        $exam_month = 1;

        $applicationVerifyCount[$exam_month]['fresh_get_verifier_pending'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 1);

        $applicationVerifyCount[$exam_month]['fresh_get_verifier_accepted'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 7);
        $applicationVerifyCount[$exam_month]['fresh_get_verifier_objected'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 8);
        $applicationVerifyCount[$exam_month]['fresh_get_verifier_clarification_first_appeal'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 9);

        $applicationVerifyCount[$exam_month]['fresh_get_verifier_all'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 'all');

        $applicationVerifyCount[$exam_month]['fresh_get_ao_pending'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 1);


        $applicationVerifyCount[$exam_month]['fresh_get_ao_verfied'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 2);
        $applicationVerifyCount[$exam_month]['fresh_get_ao_rejected'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 3);
        $applicationVerifyCount[$exam_month]['fresh_get_ao_clarification_first_appeal'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 9);

        $applicationVerifyCount[$exam_month]['fresh_get_ao_request_verifier_to_dept'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 5);
        $applicationVerifyCount[$exam_month]['fresh_get_ao_dept_clarification_to_verifier'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 6);
        $applicationVerifyCount[$exam_month]['fresh_get_ao_all'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 'all');

        $applicationVerifyCount[$exam_month]['fresh_get_ao_agree_with_verifier'] = $custom_component_obj->getAOAgreeeOrNotWithVerifier($exam_month, 1);
        $applicationVerifyCount[$exam_month]['fresh_get_ao_not_agree_with_verifier'] = $custom_component_obj->getAOAgreeeOrNotWithVerifier($exam_month, 2);


        // dd($super_admin_id);
        $applicationVerifyCount[$exam_month]['fresh_get_dept_pending'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $super_admin_id, 1);


        $applicationVerifyCount[$exam_month]['fresh_get_dept_verfied'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $super_admin_id, 2);

        $applicationVerifyCount[$exam_month]['fresh_get_dept_rejected'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $super_admin_id, 3);

        $applicationVerifyCount[$exam_month]['fresh_get_dept_clarification_second_appeal'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $super_admin_id, 10);
        $applicationVerifyCount[$exam_month]['fresh_get_dept_request_verifier_to_dept'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $super_admin_id, 5);
        $applicationVerifyCount[$exam_month]['fresh_get_dept_dept_clarification_to_verifier'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $super_admin_id, 6);

        $applicationVerifyCount[$exam_month]['fresh_get_dept_all'] = $custom_component_obj->getDeptVerificationPart($aicenter_mapped_data, $exam_month, $super_admin_id, 'all');

        $applicationVerifyCount[$exam_month]['fresh_get_dept_is_permanent_rejected_by_dept'] = $custom_component_obj->getDeptVerificationPermanantRejectedPart(1, $exam_month);


        // dd($applicationVerifyCount[$exam_month]['fresh_get_dept_all'] );


        return view('application.dashboard.applicationverifyeradmindashboard', compact('allowShow', 'exam_month', 'applicationVerifyCount', 'current_session', 'admission_sessions', 'counter', 'exam_monthall'));
    }

    public function student(Request $request)
    {
        $student = Auth::guard('student')->user()->id;
        return redirect()->route('persoanl_details', Crypt::encrypt($student))->with('message', 'Welcome Student.');
    }

    public function applicationverifyerdashboard()
    {

        $custom_component_obj = new CustomComponent;

        $auth_user_id = $verifier_user_id = Auth::user()->id;
        Session::put("ai_code", $auth_user_id);
        $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);


        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'application_dashboard';
        $application_dashboard = $this->master_details($combo_name);
        $current_session = CustomHelper::_get_selected_sessions();
        $combo_name = 'exam_month';
        $exam_monthall = $this->master_details($combo_name);

        $role_id = Session::get('role_id');
        $applicationVerifyCount['1']['status'] = $applicationCount['1']['status'] = $application_dashboard[1];
        $applicationVerifyCount['2']['status'] = $applicationCount['2']['status'] = $application_dashboard[2];
        $applicationVerifyCount['total']['status'] = $applicationCount['total']['status'] = $application_dashboard[3];


        if ($applicationCount['1']['status'] == @$application_dashboard[1]) {
            $exam_month = 1;

            $applicationVerifyCount[$exam_month]['fresh_get_verifier_pending'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 1);
            // dd($applicationVerifyCount);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_accepted'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 7);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_objected'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 8);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_all'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 'all');
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_clarification_first_appeal'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 9);
        }
        if ($applicationCount['2']['status'] == @$application_dashboard[2]) {
            $exam_month = 2;
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_pending'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 1);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_accepted'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 7);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_objected'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 8);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_all'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 'all');
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_clarification_first_appeal'] = $custom_component_obj->getVerificationPart($aicenter_mapped_data, $exam_month, 9);
        }

        if ($applicationCount['total']['status'] == @$application_dashboard[3]) {
            $fld = "fresh_get_verifier_pending";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_verifier_accepted";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_verifier_objected";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_verifier_all";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_verifier_clarification_first_appeal";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
        }
        /* End */
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $current_session = CustomHelper::_get_selected_sessions();


        return view('application.dashboard.applicationverifyerdashboard', compact('admission_sessions', 'exam_month', 'applicationVerifyCount', 'applicationCount', 'current_session', 'admission_sessions', 'role_id'));
    }

    public function studentsdashboards(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $allowHllTicketIps = $custom_component_obj->hallticketAllowIps();
        $isAllowForSuppApplicaitonForm = $this->_checkIsAllowStudentForSupplementaryApplicationForm($request);
        $getCheckAllowSuppForm = $this->_getCheckAllowSuppForm();
        $isAllowForRevalApplicaitonForm = $this->_checkIsAllowStudentForRevalApplicationForm($request);

        $correctionAllowOrNot = $custom_component_obj->_checkRevisedAllowOrNotAllow();
        $checkchangerequestsAllowOrNotAllow = $this->_checkchangerequestsAllowOrNotAllow();
        $allowchnagerequestAllowIps = $custom_component_obj->chnagerequestAllowIps();
        $checkchangerequestsssupplementariesAllowOrNotAllow = $this->_checkchangerequestssupplementariesAllowOrNotAllow();
        $chnagerequestsupplementariesallowIps = $custom_component_obj->chnagerequestsupplementariesAllowIps();
        $student_id = $auth_user_id = Auth::guard('student')->user()->id;
		
        // dd($student_id);
        $applicationdate = Application::where('student_id', @$student_id)->first();

        // $examResultsdata = ExamResult::where('student_id',@$student_id)->orderBy('id','DESC')->count();
        $examResultsdata = ExamResult::where('student_id', @$student_id)->orderBy('id', 'DESC')->first();

        $arrExamCancelledStudents = array(854268, 841818, 838422, 824633, 800811, 757721, 807547, 751416, 847044, 825056, 831227, 795553, 824644, 842317, 853059, 836823, 855297, 840522, 796064, 824893, 842323, 842419, 823742, 793242, 842796, 825097, 800836);

        if (in_array($student_id, $arrExamCancelledStudents)) {
            $isAllowForSuppApplicaitonForm = false;
        }
        if ($isAllowForSuppApplicaitonForm && @$examResultsdata->final_result && $examResultsdata->final_result == "RWH") {
            $isAllowForSuppApplicaitonForm = false;
        }


        if (@$examResultsdata->id && $examResultsdata->id > 0) {
            $examResultsdata = $examResultsdata->id;
        }

        if ($correctionAllowOrNot == true && $examResultsdata != 0) {
            $correctionAllowOrNot = true;
        } else {
            $correctionAllowOrNot = false;
        }

        $revalDetails = RevalStudent::where('student_id', @$student_id)->whereNotNull('rte_reval_notify_date')->orderBy('exam_year', 'desc')->first(['id', 'rte_reval_notify_date']);

        $revalCompleteDetails = RevalStudent::where('student_id', @$student_id)->orderBy('exam_year', 'desc')->first();


        $super_admin_id = Config::get("global.super_admin_id");
        $verifier_id = Config::get("global.verifier_id");
        $custom_component_obj = new CustomComponent;
        $dateIsOpen = false;
        $student_exam_month = Auth::guard('student')->user()->exam_month;

        if ($student_exam_month != 2) { //As per temp requried by rsos
            $dateIsOpen = $custom_component_obj->getDocumentVerificationAllowOrNot($super_admin_id);
        }

        $exam_years = CustomHelper::_get_selected_sessions();
        $exam_months = Config::get('global.supp_current_admission_exam_month');
        $getcurrentsuppverifydata = Supplementary::where('student_id', $student_id)->where('exam_year', $exam_years)->where('exam_month', $exam_months)->first(['is_aicenter_verify', 'is_per_rejected', 'is_department_verify', 'challan_tid']);


        //$getCurrentFreshVerifydata = Student::where('id',$student_id)->where('exam_year',$exam_years)->where('exam_month',$current_exam_month_id)->first(['is_verifier_verify','is_department_verify','verifier_status','department_status','ao_status','stage','is_doc_rejected','challan_tid']);
        $student_id_for_doc_upload = null;
        $ssoid = Auth::guard('student')->user()->ssoid;
        $student_id_for_doc_upload = Student::where('ssoid', $ssoid)->orderby('id', 'desc')->first(["id","student_change_requests"]);
		$changeRequestStatus = Auth::guard('student')->user()->student_change_requests;
		/*if(!empty($student_id_for_doc_upload)){
			$changeRequestStatus = @$student_id_for_doc_upload->student_change_requests;
		}*/
        $student_id_for_doc_upload = @$student_id_for_doc_upload->id;
        /* For Multi Exam Month*/
        $current_exam_month_id = Config::get("global.form_current_exam_month_id");
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $streams = collect();
        if (!empty($stream_id)) {
            $streams = array_keys($stream_id->toArray());
            $streams = collect(@$streams);
        }
        // $getCurrentFreshVerifydata = Student::where('id',$student_id)->where('exam_year',$exam_years)->whereIn('exam_month',$streams)->first(['is_verifier_verify','is_department_verify','verifier_status','department_status','ao_status','stage','course','is_doc_rejected','challan_tid']);
        $getCurrentFreshVerifydata = Student::where('id', $student_id_for_doc_upload)->first(['is_verifier_verify', 'is_department_verify', 'verifier_status', 'department_status', 'ao_status', 'stage', 'course', 'is_doc_rejected', 'challan_tid']);


        /* For Multi Exam Month End */

        /* For change requert*/
        $current_exam_month_id = Config::get("global.form_current_exam_month_id");
        $changerequertcurrentexamyear = Config::get("global.form_admission_academicyear_id");
        $changerequeststreamgatdata = DB::table('masters')->where('combo_name', '=', 'change_request_stream')->first();

        $studentconditions['applications.is_ready_for_verifying'] = 1;
        $studentconditions['students.exam_year'] = $changerequertcurrentexamyear;
        if (@$changerequeststreamgatdata->option_val == 1 || @$changerequeststreamgatdata->option_val == 2) {
            $studentconditions['students.exam_month'] = $changerequeststreamgatdata->option_val;
        }

        // $getstudentchangerequertdatas = Student::Join('applications', 'applications.student_id', '=', 'students.id')
        // ->where('students.id',$student_id)->where($studentconditions)->whereNull('students.student_change_requests')->count();
        $rawQ1 = " ( rs_students.ao_status in (2,3) or rs_students.department_status in (2,3)  ) ";
        $getstudentchangerequertdatas = Student::join('applications', 'applications.student_id', '=', 'students.id')
            ->where('students.id', $student_id)
            ->where($studentconditions)
            ->whereNull('students.student_change_requests')
            ->whereRaw($rawQ1)
            ->count();

        //dd($getstudentchangerequertdatas);
        //$getstudentchangerequertdatas = Student::where('id',$student_id)->where($conditions)->WhereNotNull('enrollment')->whereNull('student_change_requests')->count();
        @$changerequeststudentsdataid = DB::table('change_request_students')->where('student_id', $student_id)->orderBy('id', 'desc')->first(['exam_month', 'student_update_application']);
        @$changerequeststudentgetdatecount = DB::table('change_request_students')->where('student_id', $student_id)->orderBy('id', 'desc')->count();
        //$authstudentchangerequests = Auth::guard('student')->user()->student_change_requests;
        //$studentexammonthget = Auth::guard('student')->user()->exam_month;
        //$authstudentchangerequestsdepartmentstatus = Auth::guard('student')->user()->department_status;
        //$authstudentchangerequestsaostatus = Auth::guard('student')->user()->ao_status;
        /* For change requert End */

        /*For change requert supplementaries*/
        $current_supp_exam_month_id = Config::get("global.supp_current_admission_exam_month");
        $changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
        $conditions['exam_year'] = $changerequertcurrentsuppexamyear;
        $conditions['exam_month'] = $current_supp_exam_month_id;
        $conditions['locksumbitted'] = 1;
		$rawQ1 = " ( rs_supplementaries.is_department_verify in (2,3)) ";
        @$getsupplementarychangerequertdatas = Supplementary::where('student_id', $student_id)->where($conditions)->whereNull('supp_student_change_requests')->whereRaw($rawQ1)->whereNotNull('challan_tid')->count();

        @$getsupplementarychangerequertstudentdatas = Supplementary::where('student_id', $student_id)->where('exam_year', $changerequertcurrentsuppexamyear)->where('exam_month', $current_supp_exam_month_id)->first(['supp_student_change_requests', 'id', 'is_department_verify']);
		
        $suppchangerequeststudent = SuppChangeRequestStudents::where('student_id', $student_id)->where('supp_id', @$getsupplementarychangerequertstudentdatas->id)->where('exam_year', $changerequertcurrentsuppexamyear)->where('exam_month', $current_supp_exam_month_id)->orderBy('id', 'desc')->first('supp_student_update_application');
		
		$suppchangerequeststudentmorecount = SuppChangeRequestStudents::where('student_id', $student_id)->where('supp_id', @$getsupplementarychangerequertstudentdatas->id)->where('exam_year', $changerequertcurrentsuppexamyear)->where('exam_month', $current_supp_exam_month_id)->orderBy('id', 'desc')->count();

        /* For change requert End supplementaries */
        $countOfRejectionByDept = DocumentVerification::where('student_id', $student_id_for_doc_upload)->where('role_id', $super_admin_id)->where('is_permanent_rejected_by_dept', 1)->count();

        // dd($countOfRejectionByDept);
        $documentVerificationDetails = DocumentVerification::where('student_id', $student_id_for_doc_upload)->where('role_id', $verifier_id)->orderby("id", "desc")->first();

        $arrVerifications = $this->getVerificationOtherThenDocInputs();
        $onlyKeys = array_keys($arrVerifications);
        if (@$documentVerificationDetails) {
            $documentVerificationDetails = $documentVerificationDetails->toArray();
        }


        $documentInput = [];
        $verifier_id = Config::get('global.verifier_id');
        $academicofficer_id = Config::get('global.academicofficer_id');
        $super_admin_id = Config::get('global.super_admin_id');


        $getroleid = $master = DocumentVerification::where('student_id', $student_id_for_doc_upload)
            ->whereIn('role_id', [$super_admin_id, $academicofficer_id])
            ->orderby("id", "DESC")->first();
        $fieldBaseName = $fldNameAliase = "ao_";
        if (@$getroleid->role_id == @$super_admin_id) { //Acedmic Department
            $fieldBaseName = $fldNameAliase = "department_";
        }
        $var_documents_verification = $fldNameAliase . 'documents_verification';
        $var_upper_documents_verification = $fldNameAliase . 'upper_documents_verification';
        // @dd($getroleid);

        @$upper_documents_verification = @$getroleid->$var_upper_documents_verification;
        @$documents_verification = @$getroleid->$var_documents_verification;

        @$upper_documents_verification_arr = json_decode($upper_documents_verification, true);
        if (@$upper_documents_verification_arr) {
            @$upper_documents_verification_arr = array_filter($upper_documents_verification_arr, function ($value) {
                return $value == 2;
            });
        }


        $isValidJson = $this->isValidJson($documents_verification);
        $documents_verification_arr = [];
        if ($isValidJson) {
            $documents_verification_arr = json_decode($documents_verification, true);
        }
        $allow_keys = [];
        if (@$upper_documents_verification_arr) {
            $allow_keys = array_keys(@$upper_documents_verification_arr);
        }
        if (@$documents_verification_arr && @$allow_keys) {
            $documents_verification_arr = array_intersect_key($documents_verification_arr, array_flip($allow_keys));
        }
        $documents_verification_arr = $this->_getfreshVerNotRequriedDocInput($documents_verification_arr);
        $isAllowUploadForFreshAfterRemoveUnwanterRejectedDocInputs = false;
        if (@$documents_verification_arr) {
            $isAllowUploadForFreshAfterRemoveUnwanterRejectedDocInputs = true;
        }


        /* Old Bkp start */
        // $studentallowornot =false;
        // if(@$getCurrentFreshVerifydata->exam_year == $exam_years && @$applicationdate->locksumbitted && !empty($applicationdate->locksumbitted) && $applicationdate->locksumbitted == 1){
        //     if(@$getCurrentFreshVerifydata->challan_tid && $getCurrentFreshVerifydata->challan_tid != ""){
        //         $studentallowornot =true;
        //     }else{
        //         $studentTotalFeeDetails = StudentFee::where('student_id',$student_id)->first(['total','student_id']);
        //         if(@$studentTotalFeeDetails->total > 0){

        //         }else{
        //             $studentallowornot =true;
        //         }
        //     }
        // }else{
        //     $studentallowornot =true;
        // }
        /* Old Bkp end  */
        /* Update for multiple course in same ssoid start */
        $studentallowornot = false;
        $temp_ssoid = Auth::guard('student')->user()->ssoid;
        $counter = Student::where('ssoid', $temp_ssoid)->count();

        if ($counter > 1) {
            $temp_student_id = Student::where('ssoid', $temp_ssoid)->orderby('id', 'desc')->first();
            $temp_student_id = @$temp_student_id->id;
            $temp_getCurrentFreshVerifydata = Student::where('id', $temp_student_id)->where('exam_year', $exam_years)->whereIn('exam_month', $streams)->first(['is_verifier_verify', 'is_department_verify', 'verifier_status', 'department_status', 'ao_status', 'stage', 'is_doc_rejected', 'challan_tid']);
            $temp_applicationdate = Application::where('student_id', @$temp_student_id)->first();

            if (@$temp_applicationdate->is_ready_for_verifying && $temp_applicationdate->is_ready_for_verifying == 1) {
                if (@$temp_getCurrentFreshVerifydata->challan_tid && $temp_getCurrentFreshVerifydata->challan_tid != "") {
                    $studentallowornot = true;
                } else {
                    $temp_studentTotalFeeDetails = StudentFee::where('student_id', $temp_student_id)->first(['total', 'student_id']);
                    if (@$temp_studentTotalFeeDetails->total > 0) {
                    } else {
                        $studentallowornot = true;
                    }
                }
            }
            $student_id = $temp_student_id;
        } else {
            $gendar_id = Auth::guard('student')->user()->gender_id;
            $temp_studentTotalFeeDetails = StudentFee::where('student_id', $student_id)->first(['total', 'student_id']);

            if (@$applicationdate->is_ready_for_verifying && !empty($applicationdate->is_ready_for_verifying) && $applicationdate->is_ready_for_verifying == 1) {
                if (@$getCurrentFreshVerifydata->challan_tid && $getCurrentFreshVerifydata->challan_tid != "") {
                    $studentallowornot = true;
                } else {
                    $temp_studentTotalFeeDetails = StudentFee::where('student_id', $student_id)->first(['total', 'student_id']);

                    if (@$temp_studentTotalFeeDetails->total > 0) {

                    } else {
                        $studentallowornot = true;
                    }
                }
            }
        }
        /* Update for multiple course in same ssoid end */
        // echo $studentallowornot;die;
        Session::put('studentallowornot', $studentallowornot);

        $inputInterMids = array();
        if (!empty($documentVerificationDetails)) {
            foreach (@$documentVerificationDetails as $k => $v) {
                if (str_contains(@$k, "_is_verify")) {
                    if (!str_contains(@$k, "_is_verify_remarks")) {
                        if ($v == 2) {
                            $k = ucfirst(str_replace("verifier_", "", str_replace("_is_verify", "", $k))) . " details";
                            $inputInterMids[] = $k;
                        }

                    }
                }
            }
        }
        $markedRejectedOtherThenDocs = implode(",", @$inputInterMids);
        $isFinallyRejectedByDept = false;
        if ($countOfRejectionByDept > 0) {
            $isFinallyRejectedByDept = true;
        }

        $studentDocumentVerificaitonIsEligibleDetails = StudentDocumentVerification::where('student_id', $student_id)->where('is_eligible_for_verify', 1)->orderby("id", "desc")->first(["id", "is_eligible_for_verify"]);

        $documentVerificationDetailsTemp = DocumentVerification::where('student_id', $student_id)->orderby("id", "desc")->first();

        $studentDocumentVerificaitonData = array();
        if (@$studentDocumentVerificaitonIsEligibleDetails->id) {
            $studentDocumentVerificaitonData = StudentDocumentVerification::where('student_id', $student_id)->where('id', @$studentDocumentVerificaitonIsEligibleDetails->id)->orderby("id", "desc")->first();
        } else {
            $studentDocumentVerificaitonData = StudentDocumentVerification::where('student_id', $student_id)->where('student_verification_id', @$documentVerificationDetailsTemp->id)->orderby("id", "desc")->first();
        }
        $current_student_ssoid = Auth::guard('student')->user()->ssoid;

        $combo_name = 'supp_form_student_level_allow_or_not';
        $supp_form_student_level_allow_or_not = $this->master_details($combo_name);

        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);


        $isAllowAddEnrollment = true;
        $isAllowAddEnrollment = Student::where('ssoid', $current_student_ssoid)->count();
        if ($isAllowAddEnrollment >= 4) {
            $isAllowAddEnrollment = false;
        }

        $student_admit_card_download_exam_year = Config::get('global.student_admit_card_download_exam_year');
        $student_admit_card_download_exam_month = Config::get('global.student_admit_card_download_exam_month');
        $data['student_allotment'] = StudentAllotment::
        where('student_id', $student_id)
            ->where('exam_year', $student_admit_card_download_exam_year)
            ->where('exam_month', $student_admit_card_download_exam_month)
            ->first();

        $isAllowToShowAdmitCardDownloadForStudent = $this->getIsAllowToShowAdmitCardDownloadForStudent(@$data['student_allotment']);

        $formStatus = 'Apply ';
        $formApplyHindiTxt = 'करने  ';
        if (!empty(@$getcurrentsuppverifydata->challan_tid)) {
            $formStatus = 'View ';
            $formApplyHindiTxt = ' की स्थिति जानने के लिए  ';
        }

        /* Start */
        $aicodes = array("01117");
        $allowedStudentIds = Student::
        where('verifier_status', 1)
            ->whereIn('ai_code', $aicodes)
            ->pluck("id");
        // dd($allowedStudentIds);
        $studentdata = Student::
        where('id', $student_id)
            ->first();

        if (@$allowedStudentIds) {
            $allowedStudentIds = $allowedStudentIds->toArray();
        }
        $isAllowToUpdateAiCode = in_array($student_id, $allowedStudentIds);
        /* End */
		$combo_name = 'result_session';
        $result_session = $this->master_details($combo_name);
        $current_exam_month_id = Config::get('global.current_result_session_month_id');
        $result_session = $result_session[$current_exam_month_id];
		
		$resultCheckStatus = $custom_component_obj->checkAllowProvisionResult();
        $showStatus = $this->_getCheckAllowToCheckResult();
        return view('application.dashboard.studentsdashboards', compact('result_session','resultCheckStatus','showStatus','changerequeststreamgatdata', 'isAllowToUpdateAiCode', 'markedRejectedOtherThenDocs', 'isFinallyRejectedByDept', 'revalCompleteDetails', 'revalDetails', 'isAllowToShowAdmitCardDownloadForStudent', 'isAllowAddEnrollment', 'current_student_ssoid', 'supp_form_student_level_allow_or_not', 'auth_user_id', 'student_id', 'isAllowForSuppApplicaitonForm', 'admission_sessions', 'isAllowForRevalApplicaitonForm', 'exam_month', 'isAllowUploadForFreshAfterRemoveUnwanterRejectedDocInputs', 'dateIsOpen', 'studentallowornot', 'getcurrentsuppverifydata', 'getCurrentFreshVerifydata', 'formStatus', 'formApplyHindiTxt', 'studentDocumentVerificaitonData', 'allowHllTicketIps', 'correctionAllowOrNot', 'getstudentchangerequertdatas', 'checkchangerequestsAllowOrNotAllow', 'allowchnagerequestAllowIps', 'changerequeststudentsdataid', 'checkchangerequestsssupplementariesAllowOrNotAllow', 'chnagerequestsupplementariesallowIps', 'getsupplementarychangerequertdatas', 'getsupplementarychangerequertstudentdatas', 'getCheckAllowSuppForm', 'suppchangerequeststudent', 'changerequeststudentgetdatecount','changeRequestStatus','suppchangerequeststudentmorecount'));
    }

    public function _checkIsAllowStudentForSupplementaryApplicationForm(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $auth_user_id = null;
        $isAllowForSuppApplicaitonForm = true;
        $isStudent = $custom_component_obj->_getIsStudentLogin();
        $formOpenAllowOrNot = $custom_component_obj->checkAnySuppEntryAllowOrNot();


        if (@$isStudent) {
            //dd(Session::get('role_id')); 41
            $student_id = $auth_user_id = @Auth::user()->id;
            $combo_name = 'supp_form_student_level_allow_or_not';
            $supp_form_student_level_allow_or_not = $this->master_details($combo_name);
            if (@$supp_form_student_level_allow_or_not) {
                $supp_form_student_level_allow_or_not = $supp_form_student_level_allow_or_not->toArray();
                if ($supp_form_student_level_allow_or_not[1] != 1) {
                    $isAllowForSuppApplicaitonForm = false;//Date is closed from master
                }
            }

            if ($formOpenAllowOrNot) {

            } else {
                $isAllowForSuppApplicaitonForm = false;
                //Date is closed from master   //$errMsg = 'पूरक आवेदन पत्र की तिथि समाप्त कर दी गई है।(The supplementary Application form date is closed.)';
            }


            /* check allow to apply for supp start */
            $master = Student::where('id', $student_id)->first();
            /* Enrolment Year allowed */
            $enrollment_year = $custom_component_obj->getYearFromEnrollment($master->enrollment);
            $supp_allow_years_list = $custom_component_obj->getSuppAllowedYear($master->stream, $master->exam_year);
            if (!in_array($enrollment_year, $supp_allow_years_list)) {
                $isAllowForSuppApplicaitonForm = false;//$errMsg = 'ध्यान! आपको चालू वर्ष के लिए पूरक परीक्षा फॉर्म भरने की अनुमति नहीं है।(Attention! You are not allowed to fill out supplementary exam form for the current year.)';
            }
            /* Enrolment Year allowed end */


            if (empty($master->id)) {
                $isAllowForSuppApplicaitonForm = false;//Failed! Student Enrollment not found,Please check student enrollment details.
            }
            if (empty($master->stream)) {
                $isAllowForSuppApplicaitonForm = false;//Failed! Student stream not found, Please check student stream details
            }
            if (@$master->course && $master->course == 12) {
                $isInValidStudent = $custom_component_obj->_checkIsInValidStudentTweleveSupp($master->id);
                if ($isInValidStudent) {
                    $isAllowForSuppApplicaitonForm = false;//Failed! You are passed previous qualifcation but passing years yet not completed 1.5 years so that you are not allow to fill the form.(असफल! आपने पिछली योग्यता उत्तीर्ण कर ली है, लेकिन डेढ़ वर्ष पूरे नहीं हुए हैं, इसलिए आपको फॉर्म भरने की अनुमति नहीं दी जाएगी।)'
                }
            }

            $examResDeleareOrNot = $custom_component_obj->checkExamResDeleareOrNotByEnrollment($master->enrollment);
            if (!$examResDeleareOrNot) {
                $isAllowForSuppApplicaitonForm = false;//Result not found
            }
            $ExamSubjectYearArr = ExamSubject::where('enrollment', $request->enrollment)->latest('exam_year')->first('exam_year', 'exam_month');
            $exam_year_latest = null;
            if (!empty(@$ExamSubjectYearArr->exam_year)) {
                $exam_year_latest = @$ExamSubjectYearArr->exam_year;
            }
            if (!empty($request->enrollment) && $master['adm_type'] == 3) {
                // please change convert to row query to eloquent query
                $query = "select count(*) as counter from `rs_exam_subjects` where `enrollment` = " . $request->enrollment . " and (`final_result` != 'PASS' and `final_result` != 'P' and `final_result` != 'p') and `deleted_at` IS NULL and `exam_year` = " . $exam_year_latest;
                $passedResultCountResult = DB::select($query);
                $passedResultCount = 0;
                if (@$passedResultCountResult[0]->counter) {
                    $passedResultCount = $passedResultCountResult[0]->counter;
                }
                if ($passedResultCount == 0) {
                    $isAllowForSuppApplicaitonForm = false;//'Failed! You are already passed in all subject'
                }
            }
            $examResultDetails = ExamResult::where('final_result', '=', "PASS")->where('student_id', '=', $student_id)->orderBy('exam_year', 'desc')->orderBy('exam_month', 'asc')->first();
            if (@$examResultDetails->student_id) {
                $isAllowForSuppApplicaitonForm = false;//'Student Already Passed in final result
            }

            /* check allow to apply for supp end */
        }
        return $isAllowForSuppApplicaitonForm;
    }

    public function sessional(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $sessionalcount = $custom_component_obj->getSessionalcount();
        return view('application.dashboard.sessionaldashboard', compact('sessionalcount'));
    }

    public function prepare_sessional(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $sessionalcount = $custom_component_obj->getPrepareSessionalcount();
        return view('application.dashboard.preparesessionaldashboard', compact('sessionalcount'));
    }


    public function rsos_officer_grade_1(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $role_id = @Session::get('role_id');
        $super_admin_id = Config::get("global.super_admin_id");
        $developer_admin = Config::get("global.developer_admin");
        $current_session = CustomHelper::_get_selected_sessions();
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $combo_name = 'application_dashboard';
        $application_dashboard = $this->master_details($combo_name);
        $counter = 0;
        $allowShow = false;
        if ($role_id == $super_admin_id || $role_id == $developer_admin) {
            $allowShow = true;
            $counter = $custom_component_obj->countStudentPending();
        }

        // $total_registered_student = $custom_component_obj->getallStudentCount();
        // $total_lock_Submit_student = $custom_component_obj->getallStudentLockSubmitCount();
        // $get_Student_payment_Count = $custom_component_obj->getallStudentpaymentCount();
        // $get_Student_payment_not_pay_Count = $custom_component_obj->getallStudentpaymentnotpayCount();
        // $get_Student_zero_fees_payment_Count = $custom_component_obj->getallStudentzerofeespaymentCount();
        // $eligible_get_Student_payment_not_pay_Count=$custom_component_obj->getallStudentEligibleCount();

        $combo_name = 'exam_month';
        $exam_monthall = $this->master_details($combo_name);
        $supp = array();
        /*
		$exam_month = 1;
		$supp[$exam_month]['supplementary_total_registered_student'] =  $custom_component_obj->getsupplementaryallStudentCount(null,$exam_month);
        $supp[$exam_month]['supplementary_total_lock_Submit_student'] = $custom_component_obj->getsupplementaryallStudentLockSubmitCount(null,$exam_month);
        $supp[$exam_month]['supplementary_get_Student_payment_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentCount(null,$exam_month);
        $supp[$exam_month]['supplementary_get_Student_payment_not_pay_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentnotpayCount(null,$exam_month);
        $supp[$exam_month]['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallEligibleStudentpaymentCount(null,$exam_month);
        $supp[$exam_month]['supplementary_get_Eligiable_Students'] = $custom_component_obj->getEligibleSupplementaryallStudentCount(null,$exam_month);
		*/

        $exam_month = 2;
        $supp[$exam_month]['supplementary_total_registered_student'] = $custom_component_obj->getsupplementaryallStudentCount(null, $exam_month);
        $supp[$exam_month]['supplementary_total_lock_Submit_student'] = $custom_component_obj->getsupplementaryallStudentLockSubmitCount(null, $exam_month);
        $supp[$exam_month]['supplementary_get_Student_payment_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentCount(null, $exam_month);
        $supp[$exam_month]['supplementary_get_Student_payment_not_pay_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentnotpayCount(null, $exam_month);
        $supp[$exam_month]['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallEligibleStudentpaymentCount(null, $exam_month);
        $supp[$exam_month]['supplementary_get_Eligiable_Students'] = $custom_component_obj->getEligibleSupplementaryallStudentCount(null, $exam_month);

        $total_registered_examiner_maps = $total_registered_examiner = 0;

        $practical_component_obj = new PracticalCustomComponent;
        $total_registered_examiner = $practical_component_obj->getDeoExaminerListOrCount('', true);

        /* Start */
        $applicationCount['1']['status'] = $application_dashboard[1];
        $applicationCount['2']['status'] = $application_dashboard[2];
        $applicationCount['total']['status'] = $application_dashboard[3];

        if ($applicationCount['1']['status'] == $application_dashboard[1]) {
            $applicationCount['1']['total_registered_student'] = $custom_component_obj->getallStudentCount(null, 1);
            $applicationCount['1']['total_lock_Submit_student'] = $custom_component_obj->getallStudentLockSubmitCount(null, 1);
            $applicationCount['1']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount(null, 1);
            $applicationCount['1']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount(null, 1);
            $applicationCount['1']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount(null, 1);
            $applicationCount['1']['get_sso_updated_student_count'] = $custom_component_obj->getallStudentWhoseSSOIdMapped(null, 1);
            $applicationCount['1']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount(null, 1);
        }

        if ($applicationCount['2']['status'] == $application_dashboard[2]) {
            $applicationCount['2']['total_registered_student'] = $custom_component_obj->getallStudentCount(null, 2);
            $applicationCount['2']['total_lock_Submit_student'] = $custom_component_obj->getallStudentLockSubmitCount(null, 2);
            $applicationCount['2']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount(null, 2);
            $applicationCount['2']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount(null, 2);
            $applicationCount['2']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount(null, 2);
            $applicationCount['2']['get_sso_updated_student_count'] = $custom_component_obj->getallStudentWhoseSSOIdMapped(null, 2);
            $applicationCount['2']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount(null, 2);
        }
        if ($applicationCount['total']['status'] == $application_dashboard[3]) {
            $fld = "total_registered_student";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "total_lock_Submit_student";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_payment_not_pay_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_zero_fees_payment_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_payment_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "eligible_get_Student_payment_not_pay_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_sso_updated_student_count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
        }

        /* End */
        $auth_user_id = @Auth::user()->id;
        $total_registered_examiner_maps = $custom_component_obj->getPracticalExaminerMaps($auth_user_id);

        return view('application.dashboard.rsosofficergrade1dashboard', compact('exam_month', 'allowShow', 'current_session', 'admission_sessions', 'counter', 'supp', 'exam_monthall', 'applicationCount'));

    }

    public function rsos_officer_grade_2(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $master = count($custom_component_obj->getAicenterData());
        return view('application.dashboard.rsosofficergrade2dashboard', compact('master'));
    }

    public function publication_dept(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $BookRequirementCustomComponent = new BookRequirementCustomComponent();
        $master = count($custom_component_obj->getAicenterData());
        $data = count($BookRequirementCustomComponent->getBooksRequrementData());
        return view('application.dashboard.publicationdeptdashboard', compact('master', 'data'));
    }

    public function dgs_dashboard(Request $request)
    {
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $current_session = CustomHelper::_get_selected_sessions();
        $custom_component_obj = new CustomComponent;
        $combo_name = 'exam_month';
        $exam_monthall = $this->master_details($combo_name);
        $combo_name = 'application_dashboard';
        $application_dashboard = $this->master_details($combo_name);
        $applicationCount['1']['status'] = $application_dashboard[1];
        $applicationCount['2']['status'] = $application_dashboard[2];
        $applicationCount['total']['status'] = $application_dashboard[3];
        if ($applicationCount['1']['status'] == $application_dashboard[1]) {
            $applicationCount['1']['total_registered_student'] = $custom_component_obj->getDSGStudentData(1);

        }
        if ($applicationCount['2']['status'] == $application_dashboard[2]) {
            $applicationCount['2']['total_registered_student'] = $custom_component_obj->getDSGStudentData(2);
        }


        if ($applicationCount['total']['status'] == $application_dashboard[3]) {
            $fld = "total_registered_student";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];

        }


        return view('application.dashboard.dgs_dashboard', compact('applicationCount', 'admission_sessions', 'current_session', 'exam_monthall'));
    }


    public function rsos_officer_grade_3(Request $request)
    {
        return view('application.dashboard.rsosofficergrade3dashboard');
    }

    public function rsos_officer_grade_4(Request $request)
    {
        return view('application.dashboard.rsosofficergrade4dashboard');
    }

    public function rsos_officer_grade_5(Request $request)
    {
        return view('application.dashboard.rsosofficergrade5dashboard');
    }

    public function deo(Request $request)
    {
        $custom_component_obj = new CustomComponent;

        $title = "Practical Examiner Details";
        $formId = ucfirst(str_replace(" ", "_", $title));
        //$data = $custom_component_obj->getUsersData($formId,true);
        // $total_registered_examiner = count($data);

        $practical_component_obj = new PracticalCustomComponent;
        $total_registered_examiner = $practical_component_obj->getDeoExaminerListOrCount($formId, true);

        $auth_user_id = @Auth::user()->id;
        $total_registered_examiner_maps = $custom_component_obj->getPracticalExaminerMaps($auth_user_id);

        return view('application.dashboard.deodashboard', compact('total_registered_examiner_maps', 'total_registered_examiner'));
    }

    public function secrecy(Request $request)
    {
        $theory_custom_component_obj = new ThoeryCustomComponent;
        $master = count($theory_custom_component_obj->getMarkingAbsentStudentList(null, false));
        return view('application.dashboard.secrecydashboard', compact('master'));
    }

    public function evaluation(Request $request)
    {
        $theory_custom_component_obj = new ThoeryCustomComponent;
        $master = count($theory_custom_component_obj->examiner_list(null, false));
        $master2 = count($theory_custom_component_obj->getAllotingExaminerList(null, false));

        $current_session = CustomHelper::_get_selected_sessions();
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);

        $custom_component_obj = new CustomComponent;
        /*reval dashboard start*/
        $aicenter_mapped_data = array();
        $combo_name = 'reval_exam_month';
        $reval_exam_month = $this->master_details($combo_name);
        $reval_exam_month = $reval_exam_month[1];

        if ($reval_exam_month == 1) {
            $exam_month = 1;
            $reval[$exam_month]['reval_total_registered_student'] = $custom_component_obj->getRevalallStudentCount(null, $exam_month);
            $reval[$exam_month]['reval_total_lock_Submit_student'] = $custom_component_obj->getRevalallStudentLockSubmitCount(null, $exam_month);
            $reval[$exam_month]['reval_get_Student_payment_Count'] = $custom_component_obj->getRevalallStudentpaymentCount(null, $exam_month);
            $reval[$exam_month]['reval_get_Student_payment_not_pay_Count'] = $custom_component_obj->getRevalallStudentpaymentnotpayCount(null, $exam_month);
            $reval[$exam_month]['reval_get_Eligiable_Students'] = $custom_component_obj->getEligibleRevalaryallStudentCount(null, $exam_month);
        } else if ($reval_exam_month == 2) {
            $exam_month = 2;
            $reval[$exam_month]['reval_total_registered_student'] = $custom_component_obj->getRevalallStudentCount(null, $exam_month);
            $reval[$exam_month]['reval_total_lock_Submit_student'] = $custom_component_obj->getRevalallStudentLockSubmitCount(null, $exam_month);
            $reval[$exam_month]['reval_get_Student_payment_Count'] = $custom_component_obj->getRevalallStudentpaymentCount(null, $exam_month);
            $reval[$exam_month]['reval_get_Student_payment_not_pay_Count'] = $custom_component_obj->getRevalallStudentpaymentnotpayCount(null, $exam_month);
            $reval[$exam_month]['reval_get_Eligiable_Students'] = $custom_component_obj->getEligibleRevalaryallStudentCount(null, $exam_month);
        }
        /* End */
        return view('application.dashboard.evaluationdashboard', compact('admission_sessions', 'current_session', 'exam_month', 'master', 'reval', 'master2'));
    }

    public function admindashboard(Request $request)
    {
        $custom_component_obj = new CustomComponent;
        $role_id = @Session::get('role_id');
        $super_admin_id = Config::get("global.super_admin_id");
        $developer_admin = Config::get("global.developer_admin");
        $current_session = CustomHelper::_get_selected_sessions();
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $combo_name = 'application_dashboard';
        $application_dashboard = $this->master_details($combo_name);
        $counter = 0;
        $allowShow = false;
        if ($role_id == $super_admin_id || $role_id == $developer_admin) {
            $allowShow = true;
            $counter = $custom_component_obj->countStudentPending();
        }

        // $total_registered_student = $custom_component_obj->getallStudentCount();
        // $total_lock_Submit_student = $custom_component_obj->getallStudentLockSubmitCount();
        // $get_Student_payment_Count = $custom_component_obj->getallStudentpaymentCount();
        // $get_Student_payment_not_pay_Count = $custom_component_obj->getallStudentpaymentnotpayCount();
        // $get_Student_zero_fees_payment_Count = $custom_component_obj->getallStudentzerofeespaymentCount();
        // $eligible_get_Student_payment_not_pay_Count=$custom_component_obj->getallStudentEligibleCount();

        $combo_name = 'exam_month';
        $exam_monthall = $this->master_details($combo_name);
        $supp = array();
        /*
		$exam_month = 1;
		$supp[$exam_month]['supplementary_total_registered_student'] =  $custom_component_obj->getsupplementaryallStudentCount(null,$exam_month);
        $supp[$exam_month]['supplementary_total_lock_Submit_student'] = $custom_component_obj->getsupplementaryallStudentLockSubmitCount(null,$exam_month);
        $supp[$exam_month]['supplementary_get_Student_payment_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentCount(null,$exam_month);
        $supp[$exam_month]['supplementary_get_Student_payment_not_pay_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentnotpayCount(null,$exam_month);
        $supp[$exam_month]['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallEligibleStudentpaymentCount(null,$exam_month);
        $supp[$exam_month]['supplementary_get_Eligiable_Students'] = $custom_component_obj->getEligibleSupplementaryallStudentCount(null,$exam_month);
		*/
        $exam_month = 2;
        $supp[$exam_month]['supplementary_total_registered_student'] = $custom_component_obj->getsupplementaryallStudentCount(null, $exam_month);
        $supp[$exam_month]['supplementary_total_lock_Submit_student'] = $custom_component_obj->getsupplementaryallStudentLockSubmitCount(null, $exam_month);
        $supp[$exam_month]['supplementary_get_Student_payment_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentCount(null, $exam_month);
        $supp[$exam_month]['supplementary_get_Student_payment_not_pay_Count'] = $custom_component_obj->getsupplementaryallStudentpaymentnotpayCount(null, $exam_month);
        $supp[$exam_month]['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallEligibleStudentpaymentCount(null, $exam_month);
        $supp[$exam_month]['supplementary_get_Eligiable_Students'] = $custom_component_obj->getEligibleSupplementaryallStudentCount(null, $exam_month);

        $total_registered_examiner_maps = $total_registered_examiner = 0;
		
		$supp[$exam_month]['supplementary_get_department_not_verfied'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month,'1');
            $supp[$exam_month]['supplementary_get_department_verified'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month,'2');
            $supp[$exam_month]['supplementary_get_department_rejected'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month,'3');
			$supp[$exam_month]['supplementary_get_department_clarification'] = $custom_component_obj->getPendingDepartmentLevelSupplementaryallStudentCount($exam_month,'4');

        $practical_component_obj = new PracticalCustomComponent;
        $total_registered_examiner = $practical_component_obj->getDeoExaminerListOrCount('', true);


        /* Start */
        // $applicationCount['total']['status'] = $application_dashboard[3];
        // $applicationCount['total']['total_registered_student'] =$custom_component_obj->getallStudentCount();
        // $applicationCount['total']['total_lock_Submit_student'] = $custom_component_obj->getallStudentLockSubmitCount();
        // $applicationCount['total']['get_Student_payment_not_pay_Count'] =$custom_component_obj->getallStudentpaymentnotpayCount();
        // $applicationCount['total']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount();
        // $applicationCount['total']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount();
        // $applicationCount['total']['get_sso_updated_student_count'] = $custom_component_obj->getallStudentWhoseSSOIdMapped();
        // $applicationCount['total']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount();

        // $applicationCount['2']['status'] = $application_dashboard[2];
        // $applicationCount['2']['total_registered_student'] = $custom_component_obj->getallStudentCount(null,2);
        // $applicationCount['2']['total_lock_Submit_student'] =$custom_component_obj->getallStudentLockSubmitCount(null,2);
        // $applicationCount['2']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount(null,2);
        // $applicationCount['2']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount(null,2);
        // $applicationCount['2']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount(null,2);
        // $applicationCount['2']['get_sso_updated_student_count'] = $custom_component_obj->getallStudentWhoseSSOIdMapped(null,2);
        // $applicationCount['2']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount(null,2);


        // $applicationCount['1']['status'] = $application_dashboard[1];
        // $applicationCount['1']['total_registered_student'] = $custom_component_obj->getallStudentCount(null,1);
        // $applicationCount['1']['total_lock_Submit_student'] =$custom_component_obj->getallStudentLockSubmitCount(null,1);
        // $applicationCount['1']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount(null,1);
        // $applicationCount['1']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount(null,1);
        // $applicationCount['1']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount(null,1);
        // $applicationCount['1']['get_sso_updated_student_count'] = $custom_component_obj->getallStudentWhoseSSOIdMapped(null,1);
        // $applicationCount['1']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount(null,1);


        $applicationCount['1']['status'] = $application_dashboard[1];
        $applicationCount['2']['status'] = $application_dashboard[2];
        $applicationCount['total']['status'] = $application_dashboard[3];

        if ($applicationCount['1']['status'] == $application_dashboard[1]) {
            $applicationCount['1']['total_registered_student'] = $custom_component_obj->getallStudentCount(null, 1);
            $applicationCount['1']['total_lock_Submit_student'] = $custom_component_obj->getallStudentLockSubmitCount(null, 1);
            $applicationCount['1']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount(null, 1);
            $applicationCount['1']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount(null, 1);
            $applicationCount['1']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount(null, 1);
            $applicationCount['1']['get_sso_updated_student_count'] = $custom_component_obj->getallStudentWhoseSSOIdMapped(null, 1);
            $applicationCount['1']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount(null, 1);
        }

        if ($applicationCount['2']['status'] == $application_dashboard[2]) {
            $applicationCount['2']['total_registered_student'] = $custom_component_obj->getallStudentCount(null, 2);
            $applicationCount['2']['total_lock_Submit_student'] = $custom_component_obj->getallStudentLockSubmitCount(null, 2);
            $applicationCount['2']['get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentpaymentnotpayCount(null, 2);
            $applicationCount['2']['get_Student_zero_fees_payment_Count'] = $custom_component_obj->getallStudentzerofeespaymentCount(null, 2);
            $applicationCount['2']['get_Student_payment_Count'] = $custom_component_obj->getallStudentpaymentCount(null, 2);
            $applicationCount['2']['get_sso_updated_student_count'] = $custom_component_obj->getallStudentWhoseSSOIdMapped(null, 2);
            $applicationCount['2']['eligible_get_Student_payment_not_pay_Count'] = $custom_component_obj->getallStudentEligibleCount(null, 2);
        }

        if ($applicationCount['total']['status'] == $application_dashboard[3]) {
            $fld = "total_registered_student";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "total_lock_Submit_student";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_payment_not_pay_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_zero_fees_payment_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_Student_payment_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "get_sso_updated_student_count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
            $fld = "eligible_get_Student_payment_not_pay_Count";
            $applicationCount['total'][$fld] = $applicationCount['1'][$fld] + $applicationCount['2'][$fld];
        }

        /* End */
        $auth_user_id = @Auth::user()->id;
        $total_registered_examiner_maps = $custom_component_obj->getPracticalExaminerMaps($auth_user_id);

        return view('application.dashboard.superadmindashboard', compact('exam_month', 'allowShow', 'current_session', 'admission_sessions', 'counter', 'supp', 'exam_monthall', 'applicationCount'));

    }


    public function queryeditermulti(Request $request)
    {
        $query = null;
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        if (!empty($request->queryeditermulti)) {
            $queries = $request->queryeditermulti;
        } else {
            return view('queryediter.queryeditermulti');
        }
        $queries = explode(":az:", $queries);

        // Get connection object and set the charset
        $host = Config::get('global.DB_HOST');
        $user = Config::get('global.DB_USERNAME');
        $pass = Config::get('global.DB_PASSWORD');
        $name = Config::get('global.DB_DATABASE');
        $conn = mysqli_connect($host, $user, $pass, $name);
        $conn->set_charset("utf8");
        $suceess = 0;
        $failed = 0;
        $total = 0;
        $counter = 1;
        foreach (@$queries as $k => $statement) {
            $total++;
            if (!empty($statement)) {
                $query = $statement . " ;";
            }
            $result = mysqli_query($conn, $query);
            if (@$result) {
                $output[$k]['query'] = $query;
                $output[$k]['status'] = true;
                $suceess++;
            } else {
                $output[$k]['query'] = $query;
                $output[$k]['status'] = false;
                $output[$k]['error'] = mysqli_error($conn);
                $failed++;
            }
            //echo "Completed " . $counter . "<br>";
            $counter++;
        }

        $conn->close();

        echo "<h2>Total " . $total . " and Success " . $suceess . " and Failed <span style='color:red;'>" . $failed . "</span></h2>";
        echo '<br>';
        echo "<h2><a href='" . route('queryeditermulti') . "' >Please Click Here To Try Again New Multiple Queries</a></h2>";
        echo '<br>';
        $html = "<table width='100%' border=1>";
        $html .= "<tr>";
        $html .= "<th>";
        $html .= "#";
        $html .= "</th>";
        $html .= "<th>";
        $html .= "Query";
        $html .= "</th>";
        $html .= "<th>";
        $html .= "Status";
        $html .= "</th>";
        $html .= "<th>";
        $html .= "Error";
        $html .= "</th>";
        $html .= "</tr>";

        foreach (@$output as $k => $v) {
            $html .= "<tr>";
            $html .= "<td>";
            $html .= $k + 1;
            $html .= "</td>";
            $html .= "<td>";
            $html .= $v['query'];
            $html .= "</td>";
            $html .= "<td>";
            $html .= $v['status'];
            $html .= "</td>";
            $html .= "<td>";
            $html .= @$v['error'];
            $html .= "</td>";
            $html .= "</tr>";
        }
        $html .= "</table>";
        echo $html;
        die;
    }

    public function queryediter(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $query = null;
        if (!empty($request->queryediter)) {
            $query = $statement = $request->queryediter;
        } else if (!empty($request->query_back)) {
            $query = $statement = $request->query_back;

            return view('queryediter.queryediter', compact('query'));
        } else {
            return view('queryediter.queryediter', compact('query'));
        }
        // Get connection object and set the charset
        $host = Config::get('global.DB_HOST');
        $user = Config::get('global.DB_USERNAME');
        $pass = Config::get('global.DB_PASSWORD');
        $name = Config::get('global.DB_DATABASE');

        $conn = mysqli_connect($host, $user, $pass, $name);
        $conn->set_charset("utf8");
        $sqlScript = "";
        $response = null;
        $inputs['sql'] = $query;
        $isValidSqlResponse = $this->_checkIsValidSql($inputs['sql']);
        if (!$isValidSqlResponse['status'] && !empty($isValidSqlResponse['errors'])) {
            return redirect()->back()->withInput($request->all())->with('error', 'Error : ' . $isValidSqlResponse['errors']);;
        }

        if (!empty($statement)) {
            $query = $statement . " ;";
        }
        $result = mysqli_query($conn, $query);
        echo ' <script>
        function copyToClipboard(elem) {
            elem.focus();
            elem.select(); 
            document.execCommand("copy");
            }
        </script>';
        if (isset($result) && !empty($result)) {

            $row_cnt = @$result->num_rows;
            $userData = array('text' => $query, 'result_counts' => $row_cnt, 'status' => '1', 'errorr' => '');

            echo '<br>';
            echo '<br> 
            SQL Query  : <span>' . $query . '</span> <br><br><input type="text" onClick="copyToClipboard(this)" style="width: 20%;" value="' . $query . '" id="myInput">';

            echo '<br><br> Total Records Count : ';
            echo $row_cnt;
            echo '<br><br> ';


            if (@$result->num_rows > 0) {
                Session::put('query_get_alls', $query);
                //$get = Session::put('query_get_alls',$query);
                // @dd($get);

                echo "<form method='post' action=" . route('queryediter') . ">
                <input type='hidden' name='query_back' value='" . $query . "'>
				<input type='hidden' name='_token' value='" . csrf_token() . "'>
				<button type='submit' class='buttonY'>Please Click Here To Try Again New</button>
                </form>";

                echo '<br>';
                echo '<input type=button value="Copy Result" class="button" onClick="copytable()">';

                //session set
                echo "<a class='button' style='background-color:blue;' href='" . route('querydownloadexcel', $query) . "'>Download Excel</a>";
                echo '<script type="text/javascript"> 
                function copytable(el) {
                    el = "stats";
                    var urlField = document.getElementById(el)   
                    var range = document.createRange()
                    range.selectNode(urlField)
                    window.getSelection().addRange(range) 
                    document.execCommand("copy")
                }
                
                </script> <style>

				.buttonY{
					background: linear-gradient(45deg,#e4ad12,#d3ce31)!important;
                    border: none;
                    color: white;
                    padding: 15px 32px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 16px;
                    margin: 4px 2px;
                    cursor: pointer;
				}
				.button {
                    background-color: #4CAF50; /* Green */
                    border: none;
                    color: white;
                    padding: 15px 32px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 16px;
                    margin: 4px 2px;
                    cursor: pointer;
                  }
                  </style>';


                echo '<span id="pwd_spn" class="password-span">';
                echo "<table border='1' id=stats>";
                $counter = 0;
                $fields = array();
                while ($row = @$result->fetch_array()) {

                    $counterMax = count($row);
                    $counterMaxFinal = $counterMax / 2;

                    if ($counter == 0) {
                        echo "<tr>";
                        $tempRow = array_keys($row);
                        $tempRowCount = count(array_keys($row));
                        $tempCounter = 0;
                        $rCount = 0;
                        for ($r = 0; $r < $tempRowCount; $r++) {
                            if ($tempCounter % 2 != 0) {
                                $fields[] = $tempRow[$r];
                            }
                            $tempCounter++;
                            $rCount++;
                        }
                        echo "<pre>Fields Name List : ";
                        print_r($fields);
                        echo "<br>";
                        $fieldsCounter = count($fields);
                        foreach ($fields as $keyTemp => $valueTemp) {
                            echo "<th>";
                            if (isset($valueTemp)) {
                                echo $valueTemp;
                            }
                            echo "</th>";
                        }
                        echo "</tr>";
                    }

                    echo "<tr>";

                    for ($r = 0; $r < $counterMaxFinal; $r++) {
                        echo "<td>";
                        if (isset($row[$r])) {
                            echo $row[$r];
                        }
                        echo "</td>";
                    }
                    // print_r($row);
                    $counter++;
                    echo "</tr>";
                }

                echo "</table>";
                echo "</span>";
            } else {
                echo "Completed or 0 results";
                echo "<h2><a href='" . route('queryediter') . "' >Please Click Here To Try Again New</a></h2>";
                echo '<br>';
            }
        } else {
            echo 'According To System Your entered wrong Query : ';
            echo '<br>';
            echo '<br> Error Is : ';
            echo mysqli_error($conn);
            echo '<br>';
            echo '<br> 
            SQL Query  : <span>' . $query . '</span> <br><br><input type="text" onClick="copyToClipboard(this)" style="width: 20%;" value="' . $query . '" id="myInput">';

            echo '<br>';
            echo '<br>
            <style>
            
            </style>
           ';

            echo '<br>';


            $userData = array('text' => $query, 'status' => '0', 'errorr' => mysqli_error($conn));

            echo "<h2><a href='" . route('queryediter') . "' >Please Click Here To Try Again New</a></h2>";
            echo '<br>';
        }
        MasterQuery::create($userData);
        $conn->close();
        die;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function querydownloadexcel($querys = null, $type = "xlsx")
    {
        $fileName = 'all_data';
        return Excel::download(new MasterQuerieediterExcelExport($querys), $fileName . '_' . date("d-m-Y") . '.xlsx');
    }

    public function queryediterget(Request $request, $type = "xlsx")
    {
        $querys = $request->queryediter;
        $inputs['sql'] = $querys;
        $isValidSqlResponse = $this->_checkIsValidSql($inputs['sql']);
        if (!$isValidSqlResponse['status'] && !empty($isValidSqlResponse['errors'])) {
            return redirect()->back()->withInput($request->all())->with('error', 'Error : ' . $isValidSqlResponse['errors']);;
        }
        if (empty($querys)) {
            return Redirect::back()->with('error', 'plase Enter Query');
        }
        $fileName = 'all_data';
        return Excel::download(new MasterQuerieediterExcelExport($querys), $fileName . '_' . date("d-m-Y") . '.xlsx');
    }

    public function documentdownload()
    {
        dd('test');
    }

    public function fresh_student_summary(Request $request)
    {
        $page_title = 'Fresh Student Summary';
        if (count($request->all()) > 0) {
            if ($request->exam_year == null) {
                $exam_year = Config::get("global.form_admission_academicyear_id");
            }

            $studentData = DB::select('call getFreshAppFinalSummary(?,?,?,?,?,?,?,?)', array($request->exam_year, $request->course, $request->stream, $request->locksumbitted, $request->is_eligible, $request->isonlyissue, $request->startlimitinput, $request->endlimitinput));
            $studentDataCols = array();
            if (@$studentData[0]) {
                $studentDataTemp = ($studentData[0]);
                foreach ($studentDataTemp as $k => $v) {
                    $studentDataCols[] = $k;
                }
            }
            $fileName = "fresh_student_summary";
            return Excel::download(new FreshStudentSummaryExcelExport($studentDataCols, $studentData), $fileName . '_' . date("d-m-Y") . '.xlsx');
        }
        return view('application.dashboard.fresh_student_summary', compact('page_title'));

    }


    public function applicationacademicofficer_dashboard()
    {
        $custom_component_obj = new CustomComponent;
        $auth_user_id = $verifier_user_id = Auth::user()->id;
        Session::put("ai_code", $auth_user_id);
        $role_id = Session::get('role_id');
        $verifier_id = config("global.verifier_id");
        $academicofficer_id = config("global.academicofficer_id");
        $aicenter_mapped_data = array();
        if ($role_id == $verifier_id) {
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
        } else if ($role_id == $academicofficer_id) {
            $auth_user_id = Auth::user()->id;
            $aicenter_mapped_data = $custom_component_obj->getAOMappedAiCodes($auth_user_id);
        }

        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'application_dashboard';
        $application_dashboard = $this->master_details($combo_name);
        $current_session = CustomHelper::_get_selected_sessions();
        $combo_name = 'exam_month';
        $exam_monthall = $this->master_details($combo_name);

        $applicationVerifyCount['1']['status'] = $applicationCount['1']['status'] = $application_dashboard[1];
        $applicationVerifyCount['2']['status'] = $applicationCount['2']['status'] = $application_dashboard[2];
        $applicationVerifyCount['total']['status'] = $applicationCount['total']['status'] = $application_dashboard[3];

        if ($applicationCount['1']['status'] == @$application_dashboard[1]) {
            $exam_month = 1;

            $applicationVerifyCount[$exam_month]['fresh_get_verifier_clarification_first_appeal'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 9);

            $applicationVerifyCount[$exam_month]['fresh_get_verifier_pending'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 1);

            $applicationVerifyCount[$exam_month]['fresh_get_verifier_accepted'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 7);

            $applicationVerifyCount[$exam_month]['fresh_get_verifier_objected'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 8);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_clarification_first_appeal'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 9);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_all'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 'all');

            $applicationVerifyCount[$exam_month]['fresh_get_ao_pending'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 1);
            $applicationVerifyCount[$exam_month]['fresh_get_ao_verfied'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 2);
            $applicationVerifyCount[$exam_month]['fresh_get_ao_rejected'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 3);

            $applicationVerifyCount[$exam_month]['fresh_get_ao_clarification_first_appeal'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 9);
            $applicationVerifyCount[$exam_month]['fresh_get_ao_request_verifier_to_dept'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 5);
            $applicationVerifyCount[$exam_month]['fresh_get_ao_dept_clarification_to_verifier'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 6);
            $applicationVerifyCount[$exam_month]['fresh_get_ao_all'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 'all');

            $applicationVerifyCount[$exam_month]['fresh_get_ao_agree_with_verifier'] = $custom_component_obj->getAOAgreeeOrNotWithVerifier($exam_month, 1, $aicenter_mapped_data);
            $applicationVerifyCount[$exam_month]['fresh_get_ao_not_agree_with_verifier'] = $custom_component_obj->getAOAgreeeOrNotWithVerifier($exam_month, 2, $aicenter_mapped_data);

        }


        if (isset($applicationCount['2']['status']) && $applicationCount['2']['status'] == @$application_dashboard[2]) {
            $exam_month = 2;
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_pending'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 1);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_accepted'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 7);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_objected'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 8);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_all'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 'all');
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_clarification_first_appeal'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 9);


            $applicationVerifyCount[$exam_month]['fresh_get_ao_pending'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 1);
            $applicationVerifyCount[$exam_month]['fresh_get_ao_verfied'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 2);
            $applicationVerifyCount[$exam_month]['fresh_get_ao_rejected'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 3);
            $applicationVerifyCount[$exam_month]['fresh_get_ao_clarification_first_appeal'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 9);
            $applicationVerifyCount[$exam_month]['fresh_get_ao_request_verifier_to_dept'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 5);
            $applicationVerifyCount[$exam_month]['fresh_get_ao_dept_clarification_to_verifier'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 6);
            $applicationVerifyCount[$exam_month]['fresh_get_ao_all'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $academicofficer_id, 'all');


            $applicationVerifyCount[$exam_month]['fresh_get_ao_agree_with_verifier'] = $custom_component_obj->getAOAgreeeOrNotWithVerifier($exam_month, 1, $aicenter_mapped_data);
            $applicationVerifyCount[$exam_month]['fresh_get_ao_not_agree_with_verifier'] = $custom_component_obj->getAOAgreeeOrNotWithVerifier($exam_month, 2, $aicenter_mapped_data);

        }
        if (isset($applicationCount['total']['status']) && $applicationCount['total']['status'] == @$application_dashboard[3]) {
            $fld = "fresh_get_verifier_pending";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_verifier_clarification_first_appeal";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_verifier_accepted";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_verifier_objected";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_verifier_all";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];

            $fld = "fresh_get_ao_pending";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_ao_verfied";
            $applicationVerifyCount['total'][$fld] = @$applicationVerifyCount['1'][$fld] + @$applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_ao_rejected";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_ao_clarification_first_appeal";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_ao_request_verifier_to_dept";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_ao_dept_clarification_to_verifier";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_ao_all";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_ao_not_agree_with_verifier";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_ao_agree_with_verifier";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
        }
        /* End */
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $current_session = CustomHelper::_get_selected_sessions();
        return view('application.dashboard.applicationacademicofficer_dashboard', compact('admission_sessions', 'exam_month', 'applicationVerifyCount', 'applicationCount', 'current_session', 'admission_sessions', 'role_id'));
    }

    public function application_verifier_admin_dashboard()
    {
        $custom_component_obj = new CustomComponent;
        $auth_user_id = $verifier_user_id = Auth::user()->id;
        Session::put("ai_code", $auth_user_id);

        $role_id = Session::get('role_id');
        $verifier_id = config("global.verifier_id");
        $academicofficer_id = config("global.academicofficer_id");

        $aicenter_mapped_data = array();
        if ($role_id == $verifier_id) {
            $aicenter_mapped_data = $custom_component_obj->getVerifierMappedAiCodes($auth_user_id);
        }

        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'application_dashboard';
        $application_dashboard = $this->master_details($combo_name);
        $current_session = CustomHelper::_get_selected_sessions();
        $combo_name = 'exam_month';
        $exam_monthall = $this->master_details($combo_name);


        $applicationVerifyCount['1']['status'] = $applicationCount['1']['status'] = $application_dashboard[1];
        $applicationVerifyCount['2']['status'] = $applicationCount['2']['status'] = $application_dashboard[2];
        $applicationVerifyCount['total']['status'] = $applicationCount['total']['status'] = $application_dashboard[3];

        if ($applicationCount['1']['status'] == @$application_dashboard[1]) {
            $exam_month = 1;
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_clarification_first_appeal'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 9);

            $applicationVerifyCount[$exam_month]['fresh_get_verifier_pending'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 1);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_accepted'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 7);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_objected'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 8);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_clarification_first_appeal'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 9);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_all'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 'all');

        }


        if (isset($applicationCount['2']['status']) && $applicationCount['2']['status'] == @$application_dashboard[2]) {
            $exam_month = 2;
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_pending'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 1);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_accepted'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 7);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_objected'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 8);
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_all'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 'all');
            $applicationVerifyCount[$exam_month]['fresh_get_verifier_clarification_first_appeal'] = $custom_component_obj->getAOVerificationPart($aicenter_mapped_data, $exam_month, $verifier_id, 9);


        }
        if (isset($applicationCount['total']['status']) && $applicationCount['total']['status'] == @$application_dashboard[3]) {
            $fld = "fresh_get_verifier_pending";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_verifier_clarification_first_appeal";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_verifier_accepted";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_verifier_objected";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];
            $fld = "fresh_get_verifier_all";
            $applicationVerifyCount['total'][$fld] = $applicationVerifyCount['1'][$fld] + $applicationVerifyCount['2'][$fld];

        }
        /* End */
        $combo_name = 'admission_sessions';
        $admission_sessions = $this->master_details($combo_name);
        $combo_name = 'exam_month';
        $exam_month = $this->master_details($combo_name);
        $current_session = CustomHelper::_get_selected_sessions();
        // dd($applicationVerifyCount);
        return view('application.dashboard.applicationverifyeradmindashboard', compact('admission_sessions', 'exam_month', 'applicationVerifyCount', 'applicationCount', 'current_session', 'admission_sessions', 'role_id'));
    }

}








