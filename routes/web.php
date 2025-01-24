<?php
	use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\MasterReportController;
    use App\Http\Controllers\LoginController;
    use App\Http\Controllers\LandingController;
	use App\Http\Controllers\ApplicationController;
	use App\Http\Controllers\RevalPaymentController;
	use App\Http\Controllers\RevalMarksController;
	use App\Http\Controllers\DistrictController;
	use App\Http\Controllers\RoleController;
	use App\Http\Controllers\DgsController;
	use App\Http\Controllers\DataSettingController;
	use App\Http\Controllers\SmsController;
	use App\Http\Controllers\UserController;
	use App\Http\Controllers\StudentController;
	use App\Http\Controllers\CollegeController;
	use App\Http\Controllers\PermissionController;
	use App\Http\Controllers\ReportController;
	use App\Http\Controllers\PracticalReportController;
	use App\Http\Controllers\ReportMasterQueriesController;
	use App\Http\Controllers\ResignationsController;
	use App\Http\Controllers\AjaxController;
	use App\Http\Controllers\ExaminationReportController;
	use App\Http\Controllers\AdmissionReportsController;
	use App\Http\Controllers\PracticalController;
	use App\Http\Controllers\ExamLateFeeDatesController;
	use App\Http\Controllers\SessionalController;
	use App\Http\Controllers\PrepareSessionalController;
	use App\Http\Controllers\SuppPaymentController;
	use App\Http\Controllers\SchoolController;
	use App\Http\Controllers\ExamcenterDetailController;
	use App\Http\Controllers\ExamcenterAllotmentController;
	use App\Http\Controllers\LogController;
	use App\Http\Controllers\SupplementaryController;
	use App\Http\Controllers\RevalController;
	use App\Http\Controllers\PracticalExaminerController;
	use App\Http\Controllers\AdministrativeReportsController;
	use App\Http\Controllers\DeoController;
	use App\Http\Controllers\MappingExaminerController;
	use App\Http\Controllers\PaymentController;
	use App\Http\Controllers\ResultProcessController;
	use App\Http\Controllers\ImportProcessedResultController;
	use App\Http\Controllers\SuppResultProcessController;
	use App\Models\User;
	use App\Http\Controllers\TimeTableController;
	use App\Http\Controllers\ResultUpdateController;
	use App\Http\Controllers\TheoryExaminnerMappingController;
	use App\Http\Controllers\MarkingAbsentController;
	use App\Http\Controllers\AllotingCopiesExaminerController;
	use App\Http\Controllers\TheroryMarkSubmissionController;
	use App\Http\Controllers\TabingController;
	use App\Http\Controllers\TheoryReportController;
	use App\Http\Controllers\ResultUpdate2Controller;
	use App\Http\Controllers\StrCertificateMarksheetController;
	use App\Http\Controllers\PastdataUpdateController;
	use App\Http\Controllers\AicenterUserController;
	use App\Http\Controllers\GraphicalController;
	use App\Http\Controllers\BookRequirementController;
	use App\Http\Controllers\PageController;
    use App\Http\Controllers\BankMasterController;
	use App\Http\Controllers\ArrPrintForTestsController;
	use App\Http\Controllers\SuppAdminReportsController;
	use App\Http\Controllers\FreshStudentDocumentVeriyPaymentController;
	use App\Http\Controllers\MarksheetPaymentController;
	use App\Http\Controllers\ChangeRequestPaymentController;
	use App\Http\Controllers\SuppChangeRequestPaymentController;
	

	
	Route::resource('/', \App\Http\Controllers\LandingController::class);
	Route::any('qqrcode',[LandingController::class,'qqrcode'])->name('qqrcode');
	Route::any('developerPaymentVerify',[LandingController::class,'developerPaymentVerify'])->name('developerPaymentVerify');

	Route::any('tocWrongConversionCount',[LandingController::class,'tocWrongConversionCount'])->name('tocWrongConversionCount');
	Route::any('temp_reval_marks_setting',[LandingController::class,'temp_reval_marks_setting'])->name('temp_reval_marks_setting');
	
	
    Route::any('downloadStudentFeesExlmaltipule', [LandingController::class, 'downloadStudentFeesExlmaltipule'])->name('downloadStudentFeesExlmaltipule');
    Route::any('downloadRejectedStudentExl/{examyear}/{exammonth}', [LandingController::class, 'downloadRejectedStudentExl'])->name('downloadRejectedStudentExl');
    Route::any('summaryBookStockInformationDownloadExcel/{option}', [LandingController::class, 'summaryBookStockInformationDownloadExcel'])->name('summaryBookStockInformationDownloadExcel');
    Route::any('summaryRequriedBookInformationDownloadExcel/{option}', [LandingController::class, 'summaryRequriedBookInformationDownloadExcel'])->name('summaryRequriedBookInformationDownloadExcel');
	
	Route::any('hallticketqr', [ExaminationReportController::class,'hallticketqr'])->name('hallticketqr');
	Route::any('hallticketqrviews/{enrollment}', [ExaminationReportController::class,'hallticketqrviews'])->name('hallticketqrviews');
	
	
	
	Route::any('downloadrevalgeneratetemplateExl/{subject_id}', [RevalMarksController::class, 'downloadrevalgeneratetemplateExl'])->name('downloadrevalgeneratetemplateExl');
	Route::any('downloadmobileapp',[LandingController::class,'downloadmobileapp'])->name('downloadmobileapp');
	Route::any('rahul123',[LandingController::class,'rahul123'])->name('rahul123');
	Route::any('rahulimageupload',[LandingController::class,'rahulimageupload'])->name('rahulimageupload');
	Route::any('rahul_backup_database',[LandingController::class,'rahul_backup_database'])->name('rahul_backup_database');
	Route::any('rajMasterDataJson',[LandingController::class,'rajMasterDataJson'])->name('rajMasterDataJson');
	Route::any('studentWhoYetNotMappedSSO',[LandingController::class,'studentWhoYetNotMappedSSO'])->name('studentWhoYetNotMappedSSO');
	Route::any('publish_result',[LandingController::class,'publish_result'])->name('publish_result');
	Route::any('publish_publication_department_report',[LandingController::class,'publish_publication_department_report'])->name('publish_publication_department_report');
	Route::any('hall_ticket_api_download', [ExaminationReportController::class, 'hall_ticket_api_download'])->name('hall_ticket_api_download');
	Route::any('admitcarddownloadforall', [ExaminationReportController::class, 'AdmitCardDownloadForAll'])->name('admitcarddownloadforall');
	Route::any('downloadAdmitCard/{enrollment}', [ExaminationReportController::class, 'downloadAdmitCard'])->name('downloadAdmitCard');
	
	
	Route::any('hall_ticket_api_bulk_ganrate', [ExaminationReportController::class, 'hall_ticket_api_bulk_ganrate'])->name('hall_ticket_api_bulk_ganrate');
	
	Route::any('hall_ticket_single_bulk_generate', [ExaminationReportController::class, 'hall_ticket_single_bulk_generate'])->name('hall_ticket_single_bulk_generate');
	
	Route::any('hall_ticket_single_bulk_aicode_wise_generate', [ExaminationReportController::class, 'hall_ticket_single_bulk_aicode_wise_generate'])->name('hall_ticket_single_bulk_aicode_wise_generate');
	
	
	
	Route::any('hallticketsinglegeneratesuperadmin/{enrollment}', [ExaminationReportController::class, 'hallticketsinglegeneratesuperadmin'])->name('hallticketsinglegeneratesuperadmin');
	
	
	Route::any('photo_sign_convert_to_base', [LandingController::class, 'photo_sign_convert_to_base'])->name('photo_sign_convert_to_base');
	Route::any('googleindex', [RoleController::class, 'googleindex'])->name('googleindex');
	
	Route::any('developerSuppPaymentVerify',[LandingController::class,'developerSuppPaymentVerify'])->name('developerSuppPaymentVerify');
	Route::any('lrarlu', [LoginController::class, 'sdlogin'])->name('lrarl');
	Route::any('dgs_login', [LoginController::class, 'dgs_login'])->name('dgs_login');
	Route::any('dlrarlu', [LoginController::class, 'dsdlogin'])->name('dlrarl');
	Route::any('login', [LoginController::class, 'index']);
	Route::any('index', [LoginController::class, 'login'])->name('login');
	Route::any('error', [LoginController::class, 'error'])->name('error');
	Route::any('/', [LandingController::class, 'index'])->name('landing');
	Route::group(['prefix' => 'datapicker'], function () {
    Route::any('/', [LandingController::class, 'datapicker'])->name('datapicker');

	});
	//Route::any('mobile_view/{student_id}', [StudentController::class, 'mobile_view'])->name('mobile_view');
	Route::post('mobile_view', [StudentController::class, 'mobile_view'])->name('mobile_view');
	// Route::post('api_hall_ticket_for_mobile_single_enrollment_view', [ExaminationReportController::class, 'api_hall_ticket_for_mobile_single_enrollment_view'])->name('api_hall_ticket_for_mobile_single_enrollment_view');
	Route::post('hall_ticket_api_download', [ExaminationReportController::class, 'hall_ticket_api_download'])->name('hall_ticket_api_download');


	Route::any('extrasingleexcenterattendancesheet/{course}/{stream}/{districtid}/{ecenter}', [ExaminationReportController::class,'singleexcenterattendancesheet'])->name('extrasingleexcenterattendancesheet');
	Route::group(['prefix' => 'arrprintfortest'], function () {
		Route::any('ai_center_nominal_nr', [ArrPrintForTestsController::class, 'ai_center_nominal_nr'])->name('ai_center_nominal_nr');
		Route::any('arr_enrollment_fixcode_view_bulk/{subjectscode}/{stream}/{course}', [ArrPrintForTestsController::class, 'arr_enrollment_fixcode_view_bulk'])->name('arr_enrollment_fixcode_view_bulk');
		
		
		Route::any('ai_center_hall_ticket', [ArrPrintForTestsController::class, 'ai_center_hall_ticket'])->name('ai_center_hall_ticket');
		
		Route::any('exam_center_nominal_nr', [ArrPrintForTestsController::class, 'exam_center_nominal_nr'])->name('exam_center_nominal_nr');
		
		Route::any('exam_center_attendance_roll', [ArrPrintForTestsController::class, 'exam_center_attendance_roll'])->name('exam_center_attendance_roll');
		
		Route::any('exam_center_theory_roll', [ArrPrintForTestsController::class, 'exam_center_theory_roll'])->name('exam_center_theory_roll');
		
		Route::any('exam_center_practical_roll', [ArrPrintForTestsController::class, 'exam_center_practical_roll'])->name('exam_center_practical_roll');
		
		Route::any('exam_center_practical_signature_roll', [ArrPrintForTestsController::class, 'exam_center_practical_signature_roll'])->name('exam_center_practical_signature_roll');
		
		Route::any('exam_center_theory_signature_roll', [ArrPrintForTestsController::class, 'exam_center_theory_signature_roll'])->name('exam_center_theory_signature_roll');
		
	});
	Route::group(['prefix' => 'student'], function () { 
		Route::any('by_pass_otp_already_student/{student_id}/{ssoid}', [PageController::class, 'by_pass_otp_already_student'])->name('by_pass_otp_already_student');
		Route::any('already_student_otp/{student_id}/{ssoid}', [PageController::class, 'already_student_otp'])->name('already_student_otp');
		Route::any('resend_student_otp/{student_id}', [PageController::class, 'resend_student_otp'])->name('resend_student_otp');
		Route::any('select_aicenter', [PageController::class, 'select_aicenter'])->name('select_aicenter');
		Route::any('new_bothcourse_registraion/{course}', [PageController::class, 'new_bothcourse_registraion'])->name('new_bothcourse_registraion');
		Route::any('self_registration', [PageController::class, 'self_registration'])->name('self_registration');
		Route::any('dgs_self_registration', [PageController::class, 'dgs_self_registration'])->name('dgs_self_registration');
		Route::any('allreadystudent/{ssoid}', [PageController::class, 'allreadystudent'])->name('allreadystudent');
		// Route::any('ao_permanant_reject/{student_id}', [StudentController::class, 'ao_permanant_reject'])->name('ao_permanant_reject');
		Route::any('resend_student_otp_personal/{student_id}', [StudentController::class, 'resend_student_otp_personal'])->name('resend_student_otp_personal');
		Route::any('verify_documents/{student_id}', [StudentController::class, 'verify_documents'])->name('verify_documents');
		Route::any('rejected_verify_documents/{student_id}', [StudentController::class, 'rejected_verify_documents'])->name('rejected_verify_documents');

		Route::any('ao_verify_documents/{student_id}', [StudentController::class, 'ao_verify_documents'])->name('ao_verify_documents');
		Route::any('ao_rejected_verify_documents/{student_id}', [StudentController::class, 'ao_rejected_verify_documents'])->name('ao_rejected_verify_documents');
		
		Route::any('verification_trail/{student_id}', [StudentController::class, 'verification_trail'])->name('verification_trail');
		Route::any('dept_rejected_verify_documents/{student_id}', [StudentController::class, 'dept_rejected_verify_documents'])->name('dept_rejected_verify_documents');
		
// Route::get('select_aicenter', function() {  
		// 	return Redirect::route("landing")->with('error','Invalid accesss request!');
		// });  
		// Route::get('already_student_otp', function() {  
			// return Redirect::route("landing")->with('error','Invalid accesss request!');
		// });

		Route::any('new_term_conditions', [PageController::class, 'new_term_conditions'])->name('new_term_conditions');
		
		Route::any('fresh_form_doc_mark_verfication/{student_id}/{type}', [StudentController::class, 'fresh_form_doc_mark_verfication'])->name('fresh_form_doc_mark_verfication');
		
		// Route::post('new_term_conditions', [PageController::class, 'new_term_conditions'])->name('new_term_conditions');
		// Route::get('new_term_conditions', function() {  
		// 	return Redirect::route("landing")->with('error','Invalid accesss request!');
		// });
		// Route::get('new_term_conditions', [PageController::class, 'new_term_conditions'])->name('new_term_conditions');
	});
	
	Route::any('documentdownload/{student_id}/{type}/{marksheet_doc}', [SupplementaryController::class, 'documentdownload'])->name('documentdownload');
	Route::get('all_routes', [MasterReportController::class, 'routes']);
	Route::get('/hiddenlinks', [TabingController::class, 'index'])->name('hiddenlinks');
    Route::any('qr',[ResultUpdateController::class,'qr'])->name('qr');
	Route::any('qrcode',[ResultUpdateController::class,'qrcode'])->name('qrcode');
    Route::any('resultprevious',[ResultUpdateController::class,'getresultmarksheet'])->name('resultprevious');
	Route::any('direct_provisioanl_marksheet/{enrollment}',[ResultUpdateController::class,'direct_provisioanl_marksheet'])->name('direct_provisioanl_marksheet');
	Route::any('temprptest/{student_id}',[ResultUpdateController::class,'temprptest'])->name('temprptest');
	Route::any('completeFeesSummary',[MasterReportController::class,'completeFeesSummary'])->name('completeFeesSummary');
	Route::any('completeFeesSummarydownload',[MasterReportController::class,'completeFeesSummarydownload'])->name('completeFeesSummarydownload');
	Route::any('downloadcompleteFeesSummaryexcel',[MasterReportController::class,'downloadcompleteFeesSummaryexcel'])->name('downloadcompleteFeesSummaryexcel');
	Route::any('provisional_result_pdf',[ResultUpdateController::class,'provisional_result_pdf'])->name('provisional_result_pdf');
	Route::any('provisionalresult',[ResultUpdateController::class,'provisional_result'])->name('result');
	Route::any('checkresult',[ResultUpdateController::class,'view_result'])->name('view_result');
	Route::any('resultdownloadpdf/{enrollment}/{dob}',[ResultUpdateController::class,'resultProvisionaldownloadpdf'])->name('resultdownloadpdf');
	
	Route::any('revisedresult',[ResultUpdateController::class,'revisedresult'])->name('revisedresult');
	Route::any('result2',[ResultUpdate2Controller::class,'result2'])->name('result2');
	
	Route::any('oldresultdownloadpdf/{enrollment}/{dob}',[ResultUpdateController::class,'oldresultdownloadpdf'])->name('oldresultdownloadpdf');
	Route::group(['prefix' => 'supp_payments'], function () {
		Route::any('supp_admission_fee_payment', [SuppPaymentController::class, 'supp_admission_fee_payment'])->name('supp_admission_fee_payment');
	});
	Route::any('aicentres', [AicenterUserController::class, 'showListAicenters'])->name('aicentres');
	

	
	// Route::group(['prefix' => 'supp','middleware' => ['auth:user']], function () {
	Route::group(['prefix' => 'supp'], function () {
		Route::any('supp_find_enrollment', [SupplementaryController::class, 'supp_find_enrollment'])->name('supp_find_enrollment');
		Route::any('supp_doc_mark_verfication', [SupplementaryController::class, 'supp_doc_mark_verfication'])->name('supp_doc_mark_verfication');
		Route::any('supp_verfication_document', [SupplementaryController::class, 'supp_verfication_document'])->name('supp_verfication_document');
		Route::any('supp_subjects_details/{student_id}', [SupplementaryController::class, 'supp_subjects_details'])->name('supp_subjects_details');
		Route::any('supp_fees_details/{student_id}', [SupplementaryController::class, 'supp_fees_details'])->name('supp_fees_details');
		Route::any('supp_doc_mark_verfication/{supp_id}/{type}', [SupplementaryController::class, 'supp_doc_mark_verfication'])->name('supp_doc_mark_verfication');
		Route::any('supp_doc_mark_rejected_verfication/{supp_id}/{type}', [SupplementaryController::class, 'supp_doc_mark_rejected_verfication'])->name('supp_doc_mark_rejected_verfication'); 
		
		Route::any('supp_preview_details/{student_id}', [SupplementaryController::class, 'supp_preview_details'])->name('supp_preview_details');
		Route::any('supp_generate_student_pdf/{student_id}', [SupplementaryController::class, 'supp_generate_student_pdf'])->name('supp_generate_student_pdf');
		Route::any('supp_delete/{student_id}', [SupplementaryController::class, 'supp_delete'])->name('supp_delete');
		Route::any('supp_masterstudent', [SupplementaryController::class, 'supp_masterstudent'])->name('supp_masterstudent');
		Route::any('suppstudentrejectdelete/{student_id}', [SupplementaryController::class, 'suppstudentrejectdelete'])->name('suppstudentrejectdelete');
		Route::any('suppsubjectdelete/{supp_id}', [SupplementaryController::class, 'suppsubjectdelete'])->name('suppsubjectdelete');
		Route::any('revartVerifyStatus/{supp_id}/{role_id}', [SupplementaryController::class, 'revartVerifyStatus'])->name('revartVerifyStatus');
	});

	Route::group(['prefix' => 'reval'], function () {
		Route::any('reval_find_enrollment', [RevalController::class, 'reval_find_enrollment'])->name('reval_find_enrollment');
		Route::any('reval_subjects_details/{student_id}', [RevalController::class, 'reval_subjects_details'])->name('reval_subjects_details');
		Route::any('reval_preview_details/{student_id}', [RevalController::class, 'reval_preview_details'])->name('reval_preview_details');
		Route::get('reval_generate_student_pdf/{reval_id}', [RevalController::class, 'reval_generate_student_pdf'])->name('reval_generate_student_pdf');
	});
	
	Route::group(['prefix' => 'reval_marks'], function () {
		Route::any('reval_marks_listing', [RevalMarksController::class, 'reval_marks_listing'])->name('reval_marks_listing');
		Route::any('reval_obtained_marks', [RevalMarksController::class, 'reval_obtained_marks'])->name('reval_obtained_marks');
		Route::any('reval_rte_copies', [RevalMarksController::class, 'reval_rte_copies'])->name('reval_rte_copies');
		Route::any('reval_generate_template/{subject_id}', [RevalMarksController::class, 'reval_generate_template'])->name('reval_generate_template');
		
		Route::any('reval_generate_pdf_obtained_marks/{subject_id}', [RevalMarksController::class, 'reval_generate_pdf_obtained_marks'])->name('reval_generate_pdf_obtained_marks');
		Route::any('reval_generate_excel_obtained_marks', [RevalMarksController::class, 'reval_generate_excel_obtained_marks'])->name('reval_generate_excel_obtained_marks');
	});
	

	Route::any('downloadBulkDocument',[StrCertificateMarksheetController::class,'downloadBulkDocument'])->name('downloadBulkDocument');
	
	Route::group(['prefix' => 'supp'], function () {
		Route::get('supp_generate_student_pdf/{student_id}', [SupplementaryController::class, 'supp_generate_student_pdf'])->name('supp_generate_student_pdf');
	});

	Route::group(['prefix' => 'supp_result_process'], function () { 
		Route::any('show_combination/{offset}/{limit}', [SuppResultProcessController::class, 'show_combination'])->name('show_combination');
		Route::any('result_process_start/{is_supplementary}/{course}/{offset}/{limit}', [SuppResultProcessController::class, 'result_process_start'])->name('result_process_start');
		Route::any('update_practical_theory_marks/{is_supplementary}/{course}/{offset}/{limit}', [SuppResultProcessController::class, 'update_practical_theory_marks'])->name('update_practical_theory_marks');
		Route::any('calculate_theory_sessional_marks/{is_supplementary}/{course}/{offset}/{limit}', [SuppResultProcessController::class, 'calculate_theory_sessional_marks'])->name('calculate_theory_sessional_marks');
		Route::any('total_marks_update/{is_supplementary}/{course}/{offset}/{limit}', [SuppResultProcessController::class, 'total_marks_update'])->name('total_marks_update');
		Route::any('process_result/{is_supplementary}/{course}/{offset}/{limit}', [SuppResultProcessController::class, 'process_result'])->name('process_result'); 
		Route::any('final_result/{is_supplementary}/{course}/{offset}/{limit}', [SuppResultProcessController::class, 'final_result'])->name('final_result');
		Route::any('update_supply_students_replica/{is_supplementary}/{course}/{offset}/{limit}', [SuppResultProcessController::class, 'update_supply_students_replica'])->name('update_supply_students_replica');
	});

	Route::group(['prefix' => 'books_requirement'], function () {  
		Route::any('devloper_auto_retotaling_books_publication', [BookRequirementController::class, 'devloper_auto_retotaling_books_publication'])->name('devloper_auto_retotaling_books_publication');
		Route::any('booklisting', [BookRequirementController::class, 'index'])->name('booklisting');
		Route::any('bookadd', [BookRequirementController::class, 'bookadd'])->name('bookadd');
		Route::any('bookdelete/{id}', [BookRequirementController::class, 'bookDelete'])->name('bookdelete');
		Route::any('bookedit/{id}', [BookRequirementController::class, 'bookedit'])->name('bookedit');
		Route::any('letter_twelve_generate_report_pdf/{ai_code?}', [BookRequirementController::class, 'letter_twelve_generate_report_pdf'])->name('letter_twelve_generate_report_pdf');
		Route::any('downloadbookrequirementExportExcel', [BookRequirementController::class, 'downloadbookrequirementExportExcel'])->name('downloadbookrequirementExportExcel');
		Route::any('downloadbookrequirementExportExcels', [BookRequirementController::class, 'downloadbookrequirementExportExcels'])->name('downloadbookrequirementExportExcels');

		
		//Route::any('letter_thirteen_generate_report_pdf/{ai_code}', [BookRequirementController::class, 'letter_thirteen_generate_report_pdf'])->name('letter_thirteen_generate_report_pdf');
	});


	Route::group(['prefix' => 'result_process'], function () { 
		Route::any('show_combination/{offset}/{limit}', [ResultProcessController::class, 'show_combination'])->name('show_combination');
        Route::any('updateStudentAllotmentMarksFromCopyCheckingTable/{exam_year}/{exam_month}', [ResultProcessController::class, 'updateStudentAllotmentMarksFromCopyCheckingTable'])->name('updateStudentAllotmentMarksFromCopyCheckingTable');
        Route::any('result_process_start/{is_supplementary}/{course}/{offset}/{limit}', [ResultProcessController::class, 'result_process_start'])->name('result_process_start');
        Route::any('subjectWiseUpdateGraceMarks', [ResultProcessController::class, 'subjectWiseUpdateGraceMarks'])->name('subjectWiseUpdateGraceMarks');
		Route::any('update_practical_theory_marks/{is_supplementary}/{course}/{offset}/{limit}', [ResultProcessController::class, 'update_practical_theory_marks'])->name('update_practical_theory_marks');
		Route::any('calculate_theory_sessional_marks/{is_supplementary}/{course}/{offset}/{limit}', [ResultProcessController::class, 'calculate_theory_sessional_marks'])->name('calculate_theory_sessional_marks');
		Route::any('manage_toc_marks/{is_supplementary}/{course}/{offset}/{limit}', [ResultProcessController::class, 'manage_toc_marks'])->name('manage_toc_marks');
		Route::any('process_result/{is_supplementary}/{course}/{offset}/{limit}', [ResultProcessController::class, 'process_result'])->name('process_result'); 
		Route::any('final_result/{is_supplementary}/{course}/{offset}/{limit}', [ResultProcessController::class, 'final_result'])->name('final_result');
		Route::any('check_final_result/{student_id}', [ResultProcessController::class, 'check_final_result'])->name('check_final_result');
		Route::any('get_toppers', [ResultProcessController::class, 'get_toppers'])->name('get_toppers');
	});

	Route::group(['prefix' => 'import_prcessed_result'], function () { 
		Route::any('show_combination/{offset}/{limit}', [ImportProcessedResultController::class, 'show_combination'])->name('show_combination');
		Route::any('import_prepare_exam_subjects/{is_supplementary}/{course}/{offset}/{limit}', [ImportProcessedResultController::class, 'import_prepare_exam_subjects'])->name('import_prepare_exam_subjects');
		Route::any('import_prepare_results/{is_supplementary}/{course}/{offset}/{limit}', [ImportProcessedResultController::class, 'import_prepare_results'])->name('import_prepare_results');
		Route::any('import_provisional_subecjt_and_results/{exam_year}/{exam_month}',[ImportProcessedResultController::class,'import_provisional_subecjt_and_results'])->name('import_provisional_subecjt_and_results');
		Route::any('export_to_sessional_exam_subjects_from_exam_subjects/{exam_year}/{exam_month}',[ImportProcessedResultController::class,'export_to_sessional_exam_subjects_from_exam_subjects'])->name('export_to_sessional_exam_subjects_from_exam_subjects');
	});

	Route::group(['prefix' => 'payments'], function () { 
		Route::any('updateBulkOrgStudentFee', [PaymentController::class, 'updateBulkOrgStudentFee'])->name('updateBulkOrgStudentFee');
		Route::any('listing_payment_issues', [PaymentController::class, 'listing_payment_issues'])->name('listing_payment_issues');
		Route::any('verify_request/{enrollment}', [PaymentController::class, 'verify_request'])->name('verify_request');
		Route::any('bulk_verify_payment_issues/{isPaymentIssue}', [PaymentController::class, 'bulk_verify_payment_issues'])->name('bulk_verify_payment_issues');
		Route::any('bulk_find_duplicate_payment_issues', [PaymentController::class, 'bulk_find_duplicate_payment_issues'])->name('bulk_find_duplicate_payment_issues');
		Route::any('raise_request/{enrollment}', [PaymentController::class, 'raise_request'])->name('raise_request');
		Route::any('response', [PaymentController::class, 'response'])->name('response');
		Route::any('registration_fee/{enrollment}' , [PaymentController::class, 'registration_fee'])->name('registration_fee');
		Route::any('send_sample_sms_to_student/{mobile}' , [PaymentController::class, 'send_sample_sms_to_student'])->name('send_sample_sms_to_student');
		Route::any('admission_fee_payment', [PaymentController::class, 'admission_fee_payment'])->name('admission_fee_payment');
		Route::any('sendSMSMessageForFeePaid', [PaymentController::class, 'sendSMSMessageForFeePaid'])->name('sendSMSMessageForFeePaid');
	});
	
	Route::group(['prefix' => 'Change_Request_payments'], function () { 
		
		Route::any('change_request_registration_fee/{enrollment}' , [ChangeRequestPaymentController::class, 'change_request_registration_fee'])->name('change_request_registration_fee');
		Route::any('change_request_listing_payment_issues', [ChangeRequestPaymentController::class, 'change_request_listing_payment_issues'])->name('change_request_listing_payment_issues');
		Route::any('change_request_verify_request/{enrollment}', [ChangeRequestPaymentController::class, 'change_request_verify_request'])->name('change_request_verify_request');
		Route::any('change_request_raise_request/{enrollment}', [ChangeRequestPaymentController::class, 'change_request_raise_request'])->name('change_request_raise_request');
		Route::any('change_request_response', [ChangeRequestPaymentController::class, 'change_request_response'])->name('change_request_response');
		Route::any('change_request_admission_fee_payment', [ChangeRequestPaymentController::class, 'change_request_admission_fee_payment'])->name('change_request_admission_fee_payment');
	});

	Route::group(['prefix' => 'supp_payments'], function () {
		Route::any('supp_listing_payment_issues', [SuppPaymentController::class, 'supp_listing_payment_issues'])->name('supp_listing_payment_issues');
		Route::any('supp_verify_request/{enrollment}', [SuppPaymentController::class, 'supp_verify_request'])->name('supp_verify_request');
		Route::any('testrjsupp_verify_request/{enrollment}', [SuppPaymentController::class, 'testrjsupp_verify_request'])->name('testrjsupp_verify_request');
		Route::any('supp_bulk_verify_payment_issues/{isPaymentIssue}', [SuppPaymentController::class, 'supp_bulk_verify_payment_issues'])->name('supp_bulk_verify_payment_issues');
		Route::any('supp_bulk_find_duplicate_payment_issues', [SuppPaymentController::class, 'supp_bulk_find_duplicate_payment_issues'])->name('supp_bulk_find_duplicate_payment_issues');
		Route::any('supp_raise_request/{enrollment}', [SuppPaymentController::class, 'supp_raise_request'])->name('supp_raise_request');
		Route::any('supp_response', [SuppPaymentController::class, 'supp_response'])->name('supp_response');
		Route::any('supp_registration_fee/{enrollment}' , [SuppPaymentController::class, 'supp_registration_fee'])->name('supp_registration_fee');
		Route::any('supp_change_request_registration_fee/{enrollment}' , [SuppChangeRequestPaymentController::class, 'supp_change_request_registration_fee'])->name('supp_change_request_registration_fee');
		Route::any('supp_change_request_response', [SuppChangeRequestPaymentController::class, 'supp_change_request_response'])->name('supp_change_request_response');
		Route::any('supp_sendSMSMessageForFeePaid', [SuppPaymentController::class, 'supp_sendSMSMessageForFeePaid'])->name('supp_sendSMSMessageForFeePaid');
		Route::any('supp_change_request_verify_request/{enrollment}', [SuppChangeRequestPaymentController::class, 'supp_change_request_verify_request'])->name('supp_change_request_verify_request');
		Route::any('supp_change_request_admission_fee_payment', [SuppChangeRequestPaymentController::class, 'supp_change_request_admission_fee_payment'])->name('supp_change_request_admission_fee_payment');
		Route::any('supp_change_request_listing_payment_issues', [SuppChangeRequestPaymentController::class, 'supp_change_request_listing_payment_issues'])->name('supp_change_request_listing_payment_issues');
		Route::any('supp_change_request_raise_request/{enrollment}', [SuppChangeRequestPaymentController::class, 'supp_change_request_raise_request'])->name('supp_change_request_raise_request');
	});

	Route::group(['prefix' => 'reval_payments'], function () {
		Route::any('reval_listing_payment_issues', [RevalPaymentController::class, 'reval_listing_payment_issues'])->name('reval_listing_payment_issues');
		Route::any('reval_verify_request/{enrollment}', [RevalPaymentController::class, 'reval_verify_request'])->name('reval_verify_request');
		Route::any('testrjreval_verify_request/{enrollment}', [RevalPaymentController::class, 'testrjreval_verify_request'])->name('testrjreval_verify_request');
		Route::any('reval_bulk_verify_payment_issues/{isPaymentIssue}', [RevalPaymentController::class, 'reval_bulk_verify_payment_issues'])->name('reval_bulk_verify_payment_issues');
		Route::any('reval_bulk_find_duplicate_payment_issues', [RevalPaymentController::class, 'reval_bulk_find_duplicate_payment_issues'])->name('reval_bulk_find_duplicate_payment_issues');
		Route::any('reval_raise_request/{enrollment}', [RevalPaymentController::class, 'reval_raise_request'])->name('reval_raise_request');
		Route::any('reval_response', [RevalPaymentController::class, 'reval_response'])->name('reval_response');
		Route::any('reval_registration_fee/{enrollment}' , [RevalPaymentController::class, 'reval_registration_fee'])->name('reval_registration_fee');
		Route::any('reval_sendSMSMessageForFeePaid', [RevalPaymentController::class, 'reval_sendSMSMessageForFeePaid'])->name('reval_sendSMSMessageForFeePaid');
	}); 
	
	Route::group(['prefix' => 'marksheet_payments'], function () {
		
		Route::any('Marksheet_listing_payment_issues', [MarksheetPaymentController::class, 'Marksheet_listing_payment_issues'])->name('Marksheet_listing_payment_issues');
		Route::any('marksheet_verify_request/{enrollment}', [MarksheetPaymentController::class, 'marksheet_verify_request'])->name('marksheet_verify_request');
		//Route::any('testrjreval_verify_request/{enrollment}', [RevalPaymentController::class, 'testrjreval_verify_request'])->name('testrjreval_verify_request');
		//Route::any('reval_bulk_verify_payment_issues/{isPaymentIssue}', [RevalPaymentController::class, 'reval_bulk_verify_payment_issues'])->name('reval_bulk_verify_payment_issues');
		//Route::any('reval_bulk_find_duplicate_payment_issues', [RevalPaymentController::class, 'reval_bulk_find_duplicate_payment_issues'])->name('reval_bulk_find_duplicate_payment_issues');
		Route::any('MarksheetdownloadApplicationExl', [MarksheetPaymentController::class, 'MarksheetdownloadApplicationExl'])->name('MarksheetdownloadApplicationExl');
		
		Route::any('marksheet_raise_request/{enrollment}', [MarksheetPaymentController::class, 'marksheet_raise_request'])->name('marksheet_raise_request');
			Route::any('marksheet_response', [MarksheetPaymentController::class, 'marksheet_response'])->name('marksheet_response');
		Route::any('marksheet_registration_fee/{enrollment}' , [MarksheetPaymentController::class, 'marksheet_revsied_duplicate_registration_fee'])->name('marksheet_revsied_duplicate_registration_fee');
		//Route::any('reval_sendSMSMessageForFeePaid', [RevalPaymentController::class, 'reval_sendSMSMessageForFeePaid'])->name('reval_sendSMSMessageForFeePaid');
	}); 
		
	Route::group(['prefix' => 'fsdv_payments'], function () {
		Route::any('fsdv_listing_payment_issues', [FreshStudentDocumentVeriyPaymentController::class, 'fsdv_listing_payment_issues'])->name('fsdv_listing_payment_issues');
		Route::any('fsdv_verify_request/{enrollment}', [FreshStudentDocumentVeriyPaymentController::class, 'fsdv_verify_request'])->name('fsdv_verify_request');
		Route::any('testrjreval_verify_request/{enrollment}', [FreshStudentDocumentVeriyPaymentController::class, 'testrjreval_verify_request'])->name('testrjreval_verify_request');
		Route::any('fsdv_bulk_verify_payment_issues/{isPaymentIssue}', [FreshStudentDocumentVeriyPaymentController::class, 'fsdv_bulk_verify_payment_issues'])->name('fsdv_bulk_verify_payment_issues');
		Route::any('fsdv_bulk_find_duplicate_payment_issues', [FreshStudentDocumentVeriyPaymentController::class, 'fsdv_bulk_find_duplicate_payment_issues'])->name('fsdv_bulk_find_duplicate_payment_issues');
		Route::any('fsdv_raise_request/{enrollment}', [FreshStudentDocumentVeriyPaymentController::class, 'fsdv_raise_request'])->name('fsdv_raise_request');
		Route::any('fsdv_response', [FreshStudentDocumentVeriyPaymentController::class, 'fsdv_response'])->name('fsdv_response');
		Route::any('fsdv_registration_fee/{enrollment}' , [FreshStudentDocumentVeriyPaymentController::class, 'fsdv_registration_fee'])->name('fsdv_registration_fee');
		Route::any('fsdv_sendSMSMessageForFeePaid', [FreshStudentDocumentVeriyPaymentController::class, 'fsdv_sendSMSMessageForFeePaid'])->name('fsdv_sendSMSMessageForFeePaid');
	});
	
	Route::group(['prefix' => 'practical_reports'], function () {
		Route::any('practical_report_examiner_mapping', [PracticalReportController::class, 'practical_report_examiner_mapping'])->name('practical_report_examiner_mapping');
		Route::any('practical_report_student_wise', [PracticalReportController::class, 'practical_report_student_wise'])->name('practical_report_student_wise');
		Route::any('downloadpracticalreportexaminermappingexcel', [PracticalReportController::class, 'downloadpracticalreportexaminermappingexcel'])->name('downloadpracticalreportexaminermappingexcel');
		Route::any('practical_slot_report', [PracticalReportController::class, 'practical_Slot_Report'])->name('practical_slot_report');
		Route::any('downloadpracticalreportslotexcel', [PracticalReportController::class, 'downloadpracticalreportslotexcel'])->name('downloadpracticalreportslotexcel');
	});

	Route::group(['prefix' => 'theory_reports'], function () {
		Route::any('theory_report_examiner_mapping', [TheoryReportController::class, 'theory_report_examiner_mapping'])->name('theory_report_examiner_mapping');
		Route::any('theory_report_student_wise', [TheoryReportController::class, 'theory_report_student_wise'])->name('theory_report_student_wise');
		Route::any('downloadtheoryreportexaminermappingexcel', [TheoryReportController::class, 'downloadtheoryreportexaminermappingexcel'])->name('downloadtheoryreportexaminermappingexcel');
	});
 

	/* Auth start  */
	Route::any('/generateEnrollmentNumber/{stream}/{course}/{aicode}', [LandingController::class, 'generateEnrollmentNumber'])->name('generateEnrollmentNumber');

	
	Route::any('gen_fixcode_exam_center', [LandingController::class, 'gen_fixcode_exam_center'])->name('gen_fixcode_exam_center');
	Route::any('rohit', [LandingController::class, 'rohit'])->name('rohit');
	Route::any('rejected_student_with_reasons/{examyear}/{exammonth}/{format}', [LandingController::class, 'rejected_student_with_reasons'])->name('rejected_student_with_reasons');
	Route::any('brs_clearing_developer', [LandingController::class, 'brs_clearing_developer'])->name('brs_clearing_developer');
	Route::any('custom_sms_sent', [LandingController::class, 'custom_sms_sent'])->name('custom_sms_sent');
	Route::any('sendDBTDetailToJanAap', [LandingController::class, 'sendDBTDetailToJanAap'])->name('sendDBTDetailToJanAap');
	Route::any('my_current_ip', [LandingController::class, 'my_current_ip'])->name('my_current_ip');
	Route::any('my_mac_address', [LandingController::class, 'my_mac_address'])->name('my_mac_address');
	Route::any('admission_option', [LandingController::class, 'admission_option'])->name('admission_option');
	
	// Route::any('rp_generate_provisional_markseet_bulk', [LandingController::class, 'rp_generate_provisional_markseet_bulk'])->name('rp_generate_provisional_markseet_bulk');
	Route::any('rp_generate_provisional_markseet_bulk_ten_male', [LandingController::class, 'rp_generate_provisional_markseet_bulk_ten_male'])->name('rp_generate_provisional_markseet_bulk_ten_male');
	Route::any('rp_generate_provisional_markseet_bulk_ten_female', [LandingController::class, 'rp_generate_provisional_markseet_bulk_ten_female'])->name('rp_generate_provisional_markseet_bulk_ten_female');
	Route::any('rp_generate_provisional_markseet_bulk_twel_male', [LandingController::class, 'rp_generate_provisional_markseet_bulk_twel_male'])->name('rp_generate_provisional_markseet_bulk_twel_male');
	Route::any('rp_generate_provisional_markseet_bulk_twel_female', [LandingController::class, 'rp_generate_provisional_markseet_bulk_twel_female'])->name('rp_generate_provisional_markseet_bulk_twel_female');
	
	Route::any('studentWhoYetNotFeePaid', [LandingController::class, 'studentWhoYetNotFeePaid'])->name('studentWhoYetNotFeePaid');
	Route::any('studentWhoYetNotFeePaidYearExamMonthWise/{exam_year}/{exam_month}', [LandingController::class, 'studentWhoYetNotFeePaidexammonthwise'])->name('studentWhoYetNotFeePaidYearExamMonthWise');
	
	
	//Route::any('rahul/{type}', [LandingController::class, 'rahul'])->name('rahul');
	Route::any('rahul', [LandingController::class, 'rahul'])->name('rahul');
	Route::any('onlytest', [LandingController::class, 'onlytest'])->name('onlytest');
	Route::any('super_dashboard', [LandingController::class, 'super_dashboard'])->name('super_dashboard');
	Route::any('updateiseligible/{type}', [LandingController::class, 'updateiseligible'])->name('updateiseligible');
	Route::any('rahulexport/{course}', [LandingController::class, 'rahulexport'])->name('rahulexport');
	Route::any('/himmat', [LandingController::class, 'himmat'])->name('himmat');
	
	 

	Route::group(['prefix' => 'ajax'], function () {
		
		Route::any('_isAadharNumberExists/{aadhar_number}', [AjaxController::class, '_isAadharNumberExists'])->name('_isAadharNumberExists');
		
		Route::any('sdloginOTP', [AjaxController::class, 'sdloginOTP'])->name('sdloginOTP');
		Route::any('setCountDownTimerDetails', [AjaxController::class, 'setCountDownTimerDetails'])->name('setCountDownTimerDetails');
		Route::any('printscreen', [AjaxController::class, 'printScreen'])->name('printscreen');
		Route::any('updatePraticalMarks', [AjaxController::class, 'updatePraticalMarks'])->name('updatePraticalMarks');
		Route::any('grpahicalGetApplicationdata', [AjaxController::class, 'grpahicalGetApplicationdata'])->name('grpahicalGetApplicationdata');
		Route::any('htmlGrpahicalData', [AjaxController::class, 'htmlGrpahicalData'])->name('htmlGrpahicalData');
		
		Route::any('_isJanNumberExists/{jan_id}', [AjaxController::class, '_isJanNumberExists'])->name('_isJanNumberExists');
		Route::any('getJanAadharDetails/{jan_aadhar_number}', [AjaxController::class, 'getJanAadharDetails'])->name('getJanAadharDetails');
		Route::any('uploadDocument', [AjaxController::class, 'uploadDocument'])->name('uploadDocument');
		Route::any('ajaxSubjectValidation', [AjaxController::class, 'ajaxSubjectValidation'])->name('ajaxSubjectValidation');
		Route::any('ajaxVerificationTrailValidation', [AjaxController::class, 'ajaxVerificationTrailValidation'])->name('ajaxVerificationTrailValidation');
		Route::any('ajaxFacultySubjectValidation', [AjaxController::class, 'ajaxFacultySubjectValidation'])->name('ajaxFacultySubjectValidation');

		Route::any('district_by_state_id', [AjaxController::class, 'get_district_by_state_id'])->name('district_by_state_id');
		Route::any('set_current_session', [AjaxController::class, 'set_current_session'])->name('set_current_session');
		Route::any('set_student_multi_enrollment', [AjaxController::class, 'set_student_multi_enrollment'])->name('set_student_multi_enrollment');
		Route::any('set_current_role', [AjaxController::class, 'set_current_role'])->name('set_current_role');
		Route::any('checkRegistration', [AjaxController::class, 'checkRegistration'])->name('checkRegistration');
		Route::any('checkPersoanldetailValidation', [AjaxController::class, 'checkPersoanldetailValidation'])->name('checkPersoanldetailValidation');
		Route::any('checkresultstudent', [AjaxController::class, 'checkresultstudent'])->name('checkresultstudent');
		Route::any('checkcaptchastudent', [AjaxController::class, 'checkcaptchastudent'])->name('checkcaptchastudent');
		Route::any('checkresultstudentold', [AjaxController::class, 'checkresultstudentold'])->name('checkresultstudentold');
		Route::any('ajaxRevalObtainedMarksSubjectValidationAndSubmission', [AjaxController::class, 'ajaxRevalObtainedMarksSubjectValidationAndSubmission'])->name('ajaxRevalObtainedMarksSubjectValidationAndSubmission');
		Route::any('ajaxRevalRTISubmission', [AjaxController::class, 'ajaxRevalRTISubmission'])->name('ajaxRevalRTISubmission');
		Route::any('checkreviedresultstudent', [AjaxController::class, 'checkreviedresultstudent'])->name('checkreviedresultstudent');

		Route::any('checksessionaltudent', [AjaxController::class, 'checksessionaltudent'])->name('checksessionaltudent');

		Route::any('ajaxGenerateCaptcha', [AjaxController::class, 'ajaxGenerateCaptcha'])->name('ajaxGenerateCaptcha');
		
		Route::any('get_tehsil_by_district_id', [AjaxController::class, 'get_tehsil_by_district_id'])->name('get_tehsil_by_district_id');
		Route::any('get_block_by_district_id', [AjaxController::class, 'get_block_by_district_id'])->name('get_block_by_district_id');
		Route::any('checkAddressValidation', [AjaxController::class, 'checkAddressValidation'])->name('checkAddressValidation');
		Route::any('ajaxTocValidation', [AjaxController::class, 'ajaxTocValidation'])->name('ajaxTocValidation');
		Route::any('ajaxviewenrollments', [AjaxController::class, 'ajaxviewenrollments'])->name('ajaxviewenrollments');
		Route::any('ajaxSessinalMarksValidation', [AjaxController::class, 'ajaxSessinalMarksValidation'])->name('ajaxSessinalMarksValidation');
		Route::any('ajaxtest', [AjaxController::class, 'ajaxtest'])->name('ajaxtest');
		Route::any('ajaxshowPassFieldToc', [AjaxController::class, 'ajaxshowPassFieldToc'])->name('ajaxshowPassFieldToc');
		Route::any('ajaxExamSubjectValidation', [AjaxController::class, 'ajaxExamSubjectValidation'])->name('ajaxExamSubjectValidation');
		Route::any('ajaxRevalSubjectValidation', [AjaxController::class, 'ajaxRevalSubjectValidation'])->name('ajaxRevalSubjectValidation');
		Route::any('getStudentsCount', [AjaxController::class, 'getStudentsCount'])->name('getStudentsCount');
		Route::any('ajaxdeleteexamcenterallotment', [AjaxController::class, 'ajaxdeleteexamcenterallotment'])->name('ajaxdeleteexamcenterallotment');
		Route::any('getStudentsCountForExamcenter', [AjaxController::class, 'getStudentsCountForExamcenter'])->name('getStudentsCountForExamcenter');
		Route::any('practicalexaminer_destroy', [AjaxController::class, 'practicalexaminer_destroy'])->name('practicalexaminer_destroy');
		Route::any('deo_destroy', [AjaxController::class, 'deo_destroy'])->name('deo_destroy');
		Route::any('studentdetailsupdate', [AjaxController::class, 'studentdetailsupdate'])->name('studentdetailsupdate');
		Route::any('getmonthlabel', [AjaxController::class, 'getmonthlabel'])->name('getmonthlabel');
		Route::any('previousqualificationget', [AjaxController::class, 'previousqualificationget'])->name('previousqualificationget');
		Route::any('getdisadvantagegroup', [AjaxController::class, 'getdisadvantagegroup'])->name('getdisadvantagegroup');
		Route::any('getListBanksIfscCode', [AjaxController::class, 'getListBanksIfscCode'])->name('getListBanksIfscCode');
		Route::any('getBankBranchDetails', [AjaxController::class, 'getBankBranchDetails'])->name('getBankBranchDetails');
		Route::any('expectedstudentcountdata', [AjaxController::class, 'expectedstudentcountdata'])->name('expectedstudentcountdata');
		Route::any('ajaxPersonalDetilasValidation', [AjaxController::class, 'ajaxPersonalDetilasValidation'])->name('ajaxPersonalDetilasValidation');
		Route::any('ajaxchecktopdetail', [AjaxController::class, 'ajaxchecktopdetail'])->name('ajaxchecktopdetail');
		Route::any('ajaxsubjectsDetilasValidation', [AjaxController::class, 'ajaxsubjectsDetilasValidation'])->name('ajaxsubjectsDetilasValidation');
		
		Route::any('ajaxqueryValidation', [AjaxController::class, 'ajaxqueryValidation'])->name('ajaxqueryValidation');
		
		Route::any('ajaxPersonaladmtype', [AjaxController::class, 'ajaxPersonaladmtype'])->name('ajaxPersonaladmtype');
		Route::any('ajaxCoursesubjects', [AjaxController::class, 'ajaxCoursesubjects'])->name('ajaxCoursesubjects');

		Route::any('ajaxexamcentercode', [AjaxController::class, 'ajaxexamcentercode'])->name('ajaxexamcentercode');

		Route::any('ajaxPersonalborad', [AjaxController::class, 'ajaxPersonalborad'])->name('ajaxPersonalborad');
		Route::any('ajaxRsosFailYearsList', [AjaxController::class, 'ajaxRsosFailYearsList'])->name('ajaxRsosFailYearsList');
		Route::any('ajaxRsosPassYearsList', [AjaxController::class, 'ajaxRsosPassYearsList'])->name('ajaxRsosPassYearsList');
		Route::any('ajaxIsPracticalSubject', [AjaxController::class, 'ajaxIsPracticalSubject'])->name('ajaxIsPracticalSubject');
		Route::any('ajaxIsVerifyTocEnrollemnt', [AjaxController::class, 'ajaxIsVerifyTocEnrollemnt'])->name('ajaxIsVerifyTocEnrollemnt');
		Route::any('get_states', [AjaxController::class, 'get_states'])->name('get_states');
		Route::any('get_subject_faculty_wise', [AjaxController::class, 'get_subject_faculty_wise'])->name('get_subject_faculty_wise');
		Route::any('ajaxRegistrationDetilasValidation', [AjaxController::class, 'ajaxRegistrationDetilasValidation'])->name('ajaxRegistrationDetilasValidation');
		Route::any('ajaxUserDetilasValidation', [AjaxController::class, 'ajaxUserDetilasValidation'])->name('ajaxUserDetilasValidation');
		Route::any('ajaxrejecteddocumentDetilasValidation', [AjaxController::class, 'ajaxrejecteddocumentDetilasValidation'])->name('ajaxrejecteddocumentDetilasValidation');
		Route::any('ajaxcreatepracticalexaminerslotValidation', [AjaxController::class, 'ajaxcreatepracticalexaminerslotValidation'])->name('ajaxcreatepracticalexaminerslotValidation');
		Route::any('ajaxDocumentVerificationValidation', [AjaxController::class, 'ajaxDocumentVerificationValidation'])->name('ajaxDocumentVerificationValidation');
		Route::any('ajaxAicenterDetilasValidation', [AjaxController::class, 'ajaxAicenterDetilasValidation'])->name('ajaxAicenterDetilasValidation');
		Route::any('ajaxBooksRequrementDetilasValidation', [AjaxController::class, 'ajaxBooksRequrementDetilasValidation'])->name('ajaxBooksRequrementDetilasValidation');
		
		Route::any('ajaxallreadystudentDetilasValidation', [AjaxController::class, 'ajaxallreadystudentDetilasValidation'])->name('ajaxallreadystudentDetilasValidation');
		
		Route::any('ajaxMyProfileAicenterDetilasValidation', [AjaxController::class, 'ajaxMyProfileAicenterDetilasValidation'])->name('ajaxMyProfileAicenterDetilasValidation');
		Route::any('ajaxSuppSubjectValidation', [AjaxController::class, 'ajaxSuppSubjectValidation'])->name('ajaxSuppSubjectValidation');
		Route::any('ajaxSuppSubjectdocumentValidation', [AjaxController::class, 'ajaxSuppSubjectdocumentValidation'])->name('ajaxSuppSubjectdocumentValidation');
		Route::any('ajaxSuppFindEnrollmentValidation', [AjaxController::class, 'ajaxSuppFindEnrollmentValidation'])->name('ajaxSuppFindEnrollmentValidation');
		Route::any('getsubjects', [AjaxController::class, 'get_subjects'])->name('getsubjects');
		Route::any('getblock', [AjaxController::class, 'get_blocks'])->name('getblock');
		Route::any('getTempblock', [AjaxController::class, 'get_temp_blocks'])->name('getTempblock');
		Route::any('getaicode', [AjaxController::class, 'get_aicode'])->name('getaicode');
		Route::any('gettempaicode', [AjaxController::class, 'get_temp_aicode'])->name('gettempaicode');
		Route::any('blockgetaicode', [AjaxController::class, 'blockgetaicode'])->name('blockgetaicode');
		Route::any('getdistrictaicenter', [AjaxController::class, 'getdistrictaicenter'])->name('getdistrictaicenter');
		Route::any('getTempdistrictaicenter', [AjaxController::class, 'getTempdistrictaicenter'])->name('getTempdistrictaicenter');
		Route::any('updatevalue', [AjaxController::class, 'soft_deleted'])->name('updatevalue');
		Route::any('ajaxGetSSOIDDetials', [AjaxController::class, 'ajaxGetSSOIDDetials'])->name('ajaxGetSSOIDDetials');
		Route::any('ajaxMappingExaminerValidation', [AjaxController::class, 'ajaxMappingExaminerValidation'])->name('ajaxMappingExaminerValidation');
		Route::any('ajaxCheckSsoAlreadyExamCenter', [AjaxController::class, 'ajaxCheckSsoAlreadyExamCenter'])->name('ajaxCheckSsoAlreadyExamCenter');
		Route::any('get_appearing_student_count', [AjaxController::class, 'get_appearing_student_count'])->name('get_appearing_student_count');
		Route::any('getSSOIDDetialsByMappingExaminerTbl', [AjaxController::class, 'getSSOIDDetialsByMappingExaminerTbl'])->name('getSSOIDDetialsByMappingExaminerTbl');
		Route::any('getDataMarkingAbsentStudent', [AjaxController::class, 'getDataMarkingAbsentStudent'])->name('getDataMarkingAbsentStudent');
		Route::any('ajaxAllotingCopiesExaminerValidation', [AjaxController::class, 'ajaxAllotingCopiesExaminerValidation'])->name('ajaxAllotingCopiesExaminerValidation');
		Route::any('ajaxCourseExamcenters', [AjaxController::class, 'ajaxCourseExamcenters'])->name('ajaxCourseExamcenters');
		Route::any('ajaxCoursesubjectsfixcode', [AjaxController::class, 'ajaxCoursesubjectsfixcode'])->name('ajaxCoursesubjectsfixcode');
		Route::any('ajaxCourseExamcentersfixcode', [AjaxController::class, 'ajaxCourseExamcentersfixcode'])->name('ajaxCourseExamcentersfixcode');
		Route::any('ajaxPracticalExaminerValidation', [AjaxController::class, 'ajaxPracticalExaminerValidation'])->name('ajaxPracticalExaminerValidation');
		Route::any('ajaxMarkingAbsentValidation', [AjaxController::class, 'ajaxMarkingAbsentValidation'])->name('ajaxMarkingAbsentValidation');
		Route::any('ajaxPracticalValidation',[AjaxController::class, 'ajaxPracticalValidation'])->name('ajaxPracticalValidation');
		Route::any('getTheoryExaminer',[AjaxController::class, 'getTheoryExaminer'])->name('getTheoryExaminer');
		Route::any('ajaxMarkSubmmisionsValidation', [AjaxController::class, 'ajaxMarkSubmmisionsValidation'])->name('ajaxMarkSubmmisionsValidation');
		Route::any('ajaxAddPracticalValidation',[AjaxController::class,'ajaxAddPracticalValidation'])->name('ajaxAddPracticalValidation');
		Route::any('ajaxEditPracticalValidation',[AjaxController::class,'ajaxEditPracticalValidation'])->name('ajaxEditPracticalValidation');
		Route::any('ajaxAddDeoValidation',[AjaxController::class,'ajaxAddDeoValidation'])->name('ajaxAddDeoValidation');
		Route::any('ajaxEditDeoValidation',[AjaxController::class,'ajaxEditDeoValidation'])->name('ajaxEditDeoValidation');
		Route::any('getDeoListByDistrictId',[AjaxController::class,'getDeoListByDistrictId'])->name('getDeoListByDistrictId');
		Route::any('gettheorysubjects',[AjaxController::class,'gettheorysubjects'])->name('gettheorysubjects');
		Route::any('getallotssoid',[AjaxController::class,'getallotssoid'])->name('getallotssoid');
		Route::any('checkMarkingAbsentdata',[AjaxController::class,'checkMarkingAbsentdata'])->name('checkMarkingAbsentdata');
		Route::any('pastDataUpdataValidation',[AjaxController::class,'pastDataUpdataValidation'])->name('pastDataUpdataValidation');
		Route::any('updateStudentDetailsPrintValidation',[AjaxController::class,'updateStudentDetailsPrintValidation'])->name('updateStudentDetailsPrintValidation');
		Route::any('checkstudentpastdata',[AjaxController::class,'checkstudentpastdata'])->name('checkstudentpastdata');
		Route::any('searchstudentdata',[AjaxController::class,'searchstudentdata'])->name('searchstudentdata');
		Route::any('getstudentexamsubjectdata',[AjaxController::class,'getstudentexamsubjectdata'])->name('getstudentexamsubjectdata');
		Route::any('finalresultupdatevalidation',[AjaxController::class,'finalresultupdatevalidation'])->name('finalresultupdatevalidation');
		Route::any('updateStudentSubjectsDataValidation',[AjaxController::class,'updateStudentSubjectsDataValidation'])->name('updateStudentSubjectsDataValidation');
		Route::any('AddsubjectValidation',[AjaxController::class,'AddsubjectValidation'])->name('AddsubjectValidation');
		Route::any('getstudentsubjectdata',[AjaxController::class,'getstudentsubjectdata'])->name('getstudentsubjectdata');
		Route::any('pastSubjectDataUpdataValidation',[AjaxController::class,'pastSubjectDataUpdataValidation'])->name('pastSubjectDataUpdataValidation');
		Route::any('checkPublishBookdata',[AjaxController::class,'checkPublishBookdata'])->name('checkPublishBookdata');
		Route::any('setPagntorValue',[AjaxController::class,'setPagntorValue'])->name('setPagntorValue');
		Route::any('AjaxSelfRegistrationValidation',[AjaxController::class,'AjaxSelfRegistrationValidation'])->name('AjaxSelfRegistrationValidation');
	    Route::any('ajaxUpdateSsoDetilasValidations',[AjaxController::class,'ajaxUpdateSsoDetilasValidations'])->name('ajaxUpdateSsoDetilasValidations');
	    Route::any('ajaxViewCreateDate', [AjaxController::class, 'ajaxViewCreateDate'])->name('ajaxViewCreateDate');
	    Route::any('send_otp_to_student', [AjaxController::class, 'send_otp_to_student'])->name('send_otp_to_student');
	    Route::any('_verify_only_mobile_student_otp', [AjaxController::class, '_verify_only_mobile_student_otp'])->name('_verify_only_mobile_student_otp');
        Route::any('get_volume_by_subject',[AjaxController::class,'getVolumeBySubject'])->name('get_volume_by_subject');
		Route::any('ajaxCorrectionMarksheetValidation',[AjaxController::class,'ajaxCorrectionMarksheetValidation'])->name('ajaxCorrectionMarksheetValidation');
		Route::any('ajaxDgsAddStudensValidation',[AjaxController::class,'ajaxDgsAddStudensValidation'])->name('ajaxDgsAddStudensValidation');
		
	
});
	
	    Route::get('generate_student_pdf/{student_id}', [StudentController::class, 'generate_student_pdf'])->name('generate_student_pdf');
		Route::get('student_change_requests/{student_id}', [StudentController::class, 'student_change_requests'])->name('student_change_requests');
		Route::get('student_supp_change_requests/{student_id}', [ SupplementaryController::class, 'student_supp_change_requests'])->name('student_supp_change_requests');
		 Route::get('student_change_requests_approved/{student_id}', [StudentController::class, 'student_change_requests_approved'])->name('student_change_requests_approved');
		 Route::get('supp_student_change_requests_approveds/{student_id}', [SupplementaryController::class, 'supp_student_change_requests_approveds'])->name('supp_student_change_requests_approveds');
		 Route::get('student_change_requests_update_application/{student_id}', [StudentController::class, 'student_change_requests_update_application'])->name('student_change_requests_update_application');
		  Route::get('supp_student_change_requests_update_application/{student_id}', [SupplementaryController::class, 'supp_student_change_requests_update_application'])->name('supp_student_change_requests_update_application');
		 Route::get('student_change_requests_end_date_after_update_data/{exam_year}/{exam_month}', [StudentController::class, 'student_change_requests_end_date_after_update_data'])->name('student_change_requests_end_date_after_update_data');
	
	Route::group(['middleware' => ['auth:user,student']], function(){
	    Route::group(['prefix' => 'student'], function () {
		Route::any('registration', [StudentController::class, 'registration'])->name('registration');
		Route::any('student_history_details/{student_id}', [StudentController::class, 'student_history_details'])->name('student_history_details');
		Route::any('studentupdateaicenter/{student_id}', [StudentController::class, 'studentupdateaicenter'])->name('studentupdateaicenter');
		Route::any('download/{path}', [StudentController::class, 'download'])->name('download');
		Route::any('supp_download/{student_id}/{path}', [StudentController::class, 'supp_download'])->name('supp_download');
		Route::any('address_details/{student_id}', [StudentController::class, 'address_details'])->name('address_details');
		Route::any('request_to_dept/{student_id}', [StudentController::class, 'request_to_dept'])->name('request_to_dept');
		Route::any('bank_details/{student_id}', [StudentController::class, 'bank_details'])->name('bank_details');
		Route::any('persoanl_details/{student_id}', [StudentController::class, 'persoanl_details'])->name('persoanl_details');
		Route::any('update_basic_details/{student_id}', [StudentController::class, 'update_basic_details'])->name('update_basic_details');
		Route::any('student_mark_reject/{student_id}', [StudentController::class, 'student_mark_reject'])->name('student_mark_reject');
		// Route::any('document_details_store', [StudentController::class, 'document_details_store'])->name('document_details_store');
		Route::any('document_details/{student_id}', [StudentController::class, 'document_details'])->name('document_details');
		Route::any('rejected_document_details/{student_id}', [StudentController::class, 'rejected_document_details'])->name('rejected_document_details');
		Route::any('admission_subject_details/{student_id}', [StudentController::class, 'admission_subject_details'])->name('admission_subject_details');
		Route::any('toc_subject_details/{student_id}', [StudentController::class, 'toc_subject_details'])->name('toc_subject_details');
		Route::any('dev_toc_subject_details/{student_id}', [StudentController::class, 'dev_toc_subject_details'])->name('dev_toc_subject_details');
		Route::any('mobile_number_details_edit/{student_id}', [StudentController::class, 'mobile_number_details_edit'])->name('mobile_number_details_edit');
		Route::any('student_remove_ssoid/{student_id}', [StudentController::class, 'student_remove_ssoid'])->name('student_remove_ssoid');
		Route::any('exam_subject_details/{student_id}', [StudentController::class, 'exam_subject_details'])->name('exam_subject_details');
		Route::any('fee_details/{student_id}', [StudentController::class, 'fee_details'])->name('fee_details');
		Route::any('preview_details/{student_id}', [StudentController::class, 'preview_details'])->name('preview_details');
		
	
		
		Route::any('studentrejectdelete/{student_id}', [StudentController::class, 'studentrejectdelete'])->name('studentrejectdelete');
		Route::any('view_details/{student_id}', [StudentController::class, 'view_details'])->name('view_details');
		
		Route::any('studentsdashboards', [ApplicationController::class, 'studentsdashboards'])->name('studentsdashboards');
		
		
			
		Route::any('fresh_student_summary', [ApplicationController::class, 'fresh_student_summary'])->name('fresh_student_summary');
		
		Route::get('downloaddeactivedstudentsexcel', [StudentController::class, 'downloaddeactivedstudentsexcel'])->name('downloaddeactivedstudentsexcel');
		Route::any('printupdatestudentdetalis/{student_id}', [StudentController::class, 'UpdateStudentDetail'])->name('printupdatestudentdetalis');
		Route::any('searchstudentdetail', [StudentController::class, 'SearchStudentDetail'])->name('searchstudentdetail');
		Route::any('student_update_eligible/{enrollment}/{mark}', [StudentController::class, 'studentUpdateEligible'])->name('student_update_eligible');

	});
		Route::get('logout', [LoginController::class, 'logout'])->name('logout');
		Route::resource('roles', \App\Http\Controllers\RoleController::class);
		Route::resource('permissions', \App\Http\Controllers\PermissionController::class);
		Route::resource('districts', \App\Http\Controllers\DistrictController::class);
		Route::resource('subjects', \App\Http\Controllers\SubjectController::class);
        Route::resource('bank_masters', \App\Http\Controllers\BankMasterController::class);

		Route::group(['prefix' => 'bank_masters'], function () {
            Route::any('create', [BankMasterController::class, 'create'])->name('create');
			Route::any('edit/{id}', [BankMasterController::class, 'edit'])->name('edit');
		});
		Route::get('subjectsactive/{id}/{active}', [\App\Http\Controllers\SubjectController::class, 'subjectsactive'])->name('subjectsactive');
		Route::resource('colleges', \App\Http\Controllers\CollegeController::class);
		Route::resource('students', \App\Http\Controllers\StudentController::class);
		Route::get('studentdelete/{id}', [StudentController::class, 'destroy'])->name('studentdelete');
		Route::get('studentunlock/{id}', [StudentController::class, 'studentunlock'])->name('studentunlock');
		Route::get('deletestudent', [StudentController::class, 'deletestudent'])->name('deletestudent');
		Route::get('studentdeleteactive/{id}', [StudentController::class, 'studentdeleteactive'])->name('studentdeleteactive');
		Route::resource('applications', \App\Http\Controllers\ApplicationController::class);
		Route::any('querydownloadexcel/{querys}', [ApplicationController::class, 'querydownloadexcel'])->name('querydownloadexcel');
		
	
		Route::resource('allotments', \App\Http\Controllers\AllotmentController::class);
		Route::resource('users', \App\Http\Controllers\UserController::class);
		Route::resource('aicenterusers', \App\Http\Controllers\AicenterUserController::class);
		Route::get('query', [RoleController::class, 'query'])->name('query');
		Route::any('update_my_profile', [AicenterUserController::class, 'update_my_profile'])->name('update_my_profile');

		Route::get('aicentersorting', [AicenterUserController::class, 'index'])->name('aicentersorting');
		
		Route::get('usersdelete/{id}', [UserController::class, 'destroy'])->name('usersdelete');
		Route::get('deleteusers', [UserController::class, 'deleteusers'])->name('deleteusers');
		Route::get('userdeleteactive/{id}', [UserController::class, 'userdeleteactive'])->name('userdeleteactive');
		Route::any('aicenterdelete/{id}',[AicenterUserController::class,'aicenterdelete'])->name('aicenterdelete');
		Route::get('aicenterusersactive/{id}/{active}', [AicenterUserController::class, 'aicenterusersactive'])->name('aicenterusersactive');
		Route::any('livetableupdate', [SupplementaryController::class, 'livetableupdate'])->name('livetableupdate');
		Route::get('userdeleteactive/{id}', [UserController::class, 'userdeleteactive'])->name('userdeleteactive');
		Route::resource('districts', \App\Http\Controllers\DistrictController::class);
		Route::resource('reports', \App\Http\Controllers\ReportController::class);
		Route::get('exportr/{id}', [ReportController::class, 'export'])->name('exportr');
		Route::get('reporting_pdf/{id}', [ReportController::class, 'reporting_pdf'])->name('reporting_pdf');
		Route::resource('master_reports', \App\Http\Controllers\MasterReportController::class);
		
		
		Route::group(['prefix' => 'document_required'], function () {
			Route::any('/',[MasterReportController::class,'requiredDocList'])->name('requireddoclist');
			Route::any('add_doc_req',[MasterReportController::class,'addReqDoc'])->name('add_doc_req');
			Route::any('edit_doc_req/{id}',[MasterReportController::class,'editReqDoc'])->name('edit_doc_req');
		});
		Route::any('backupdblisting', [ReportController::class, 'backupdblisting'])->name('backupdblisting');
		Route::any('querysqldump', [ReportController::class, 'querysqldump'])->name('querysqldump');
		Route::any('backupdb', [ReportController::class, 'backupdb'])->name('backupdb');
		Route::any('administrative_custom_report', [ReportController::class, 'administrative_custom_report'])->name('administrative_custom_report');
		Route::any('administrative_custom_document_report', [ReportController::class, 'administrative_custom_document_report'])->name('administrative_custom_document_report');
		Route::any('queryediter', [ApplicationController::class, 'queryediter'])->name('queryediter');
		Route::any('queryediterget', [ApplicationController::class, 'queryediterget'])->name('queryediterget');
		Route::any('queryeditermulti', [ApplicationController::class, 'queryeditermulti'])->name('queryeditermulti');
		Route::any('dashboard', [ApplicationController::class, 'dashboard'])->name('dashboard');
		Route::any('dgs_dashboard', [ApplicationController::class, 'dgs_dashboard'])->name('dgs_dashboard');
		
		Route::any('alldocumentlist', [ReportController::class, 'alldocumentlist'])->name('alldocumentlist');
		Route::any('createalldocument', [ReportController::class, 'createalldocument'])->name('createalldocument');
		Route::any('alldocumentstore', [ReportController::class, 'alldocumentstore'])->name('alldocumentstore');
		Route::any('downloaddocument/{path}', [ReportController::class, 'downloaddocument'])->name('downloaddocument');
		Route::any('alldocumentedit/{path}', [ReportController::class, 'alldocumentedit'])->name('alldocumentedit');
		Route::any('alldocumentupdate', [ReportController::class, 'alldocumentupdate'])->name('alldocumentupdate');
		Route::any('alldocumentdestory/{id}', [ReportController::class, 'alldocumentdestory'])->name('alldocumentdestory');
		Route::any('last_years_student_subject_data_setting/{exam_year}/{exam_month}/{limit}', [AdministrativeReportsController::class, 'last_years_student_subject_data_setting'])->name('last_years_student_subject_data_setting');
		Route::any('generate_last_years_student_subject_data_setting', [AdministrativeReportsController::class, 'generate_last_years_student_subject_data_setting'])->name('generate_last_years_student_subject_data_setting');
		Route::any('download_last_years_student_subject_data_setting', [AdministrativeReportsController::class, 'download_last_years_student_subject_data_setting'])->name('download_last_years_student_subject_data_setting');
		Route::any('administrative_document_listing', [AdministrativeReportsController::class, 'administrative_document_listing'])->name('administrative_document_listing');
		Route::any('administrative_material_download', [AdministrativeReportsController::class, 'administrative_material_download'])->name('administrative_material_download');
		Route::any('resultsedit',[ReportController::class,'resultsedit'])->name('resultsedit');

		Route::group(['prefix' => 'super_admin'], function () {
			Route::any('dashboard', [ApplicationController::class, 'applicationdashboard'])->name('applicationdashboard');
		});
		Route::group(['prefix' => 'admin'], function () {
			Route::any('dashboard', [ApplicationController::class, 'admindashboard'])->name('admindashboard');
		});
		Route::group(['prefix' => 'ai_center'], function () {
			Route::any('dashboard', [UserController::class, 'dashboard'])->name('aicenterdashboard');
		});
		Route::group(['prefix' => 'verifier'], function () { 
			Route::any('dashboard', [ApplicationController::class, 'applicationverifyerdashboard'])->name('applicationverifyerdashboard');
		});
		Route::group(['prefix' => 'academicofficer'], function () { 
			Route::any('dashboard', [ApplicationController::class, 'applicationacademicofficer_dashboard'])->name('applicationacademicofficer_dashboard');
		}); 
		Route::group(['prefix' => 'college'], function () {
			Route::any('dashboard', [ApplicationController::class, 'dashboard'])->name('collegedashboard');
		});
		Route::group(['prefix' => 'student'], function () {
			Route::any('dashboard', [ApplicationController::class, 'student'])->name('studentdashboard');
		});
		Route::group(['prefix' => 'examcenter'], function () {
			Route::any('dashboard', [ApplicationController::class, 'examcenter'])->name('examcenterashboard');
		});
		Route::group(['prefix' => 'examiner'], function () {
			Route::any('dashboard', [ApplicationController::class, 'examiner'])->name('examinerdashboard');
		});
		
		Route::group(['prefix' => 'theroy_examiner'], function () {
			Route::any('dashboard', [ApplicationController::class, 'theroy_examiner'])->name('theroyexaminerdashboard');
		});

		Route::group(['prefix' => 'evaluation_depatment'], function () {
			Route::any('dashboard', [ApplicationController::class, 'evaluation'])->name('evaluationdashboard');
		});

		Route::group(['prefix' => 'report_master'], function () {
			Route::any('listing', [ReportMasterQueriesController::class, 'listing'])->name('listings');
			Route::any('create', [ReportMasterQueriesController::class, 'create'])->name('creates');
			Route::any('edit/{id}', [ReportMasterQueriesController::class,'edit'])->name('edit');
			Route::any('destory/{id}', [ReportMasterQueriesController::class,'destory'])->name('destory');
			Route::any('view/{id}', [ReportMasterQueriesController::class,'view'])->name('view');
			Route::any('export/{id}', [ReportMasterQueriesController::class,'export'])->name('export');
			Route::any('front_view', [ReportMasterQueriesController::class,'front_view'])->name('front_view');
		});


		

		Route::group(['prefix' => 'secrecy_depatment'], function () {
			Route::any('dashboard', [ApplicationController::class, 'secrecy'])->name('secrecydashboard');
		});
		Route::group(['prefix' => 'practical_examiner'], function () {
			Route::any('dashboard', [ApplicationController::class, 'practical_examiner'])->name('practicalexaminerdashboard');
		});
		Route::group(['prefix' => 'marksheet_verification'], function () {
			Route::any('dashboard', [ApplicationController::class, 'marksheetverificationdashboard'])->name('marksheetverificationdashboard');
		});
		
		Route::group(['prefix' => 'printer'], function () {
			Route::any('dashboard', [ApplicationController::class, 'printer'])->name('printerdashboard');
		});
		
		
		Route::group(['prefix' => 'examination_admin'], function () {
			Route::any('dashboard', [ApplicationController::class, 'examination_admin'])->name('examination_admin');
		});
		
		Route::group(['prefix' => 'application_verifier_admin_dashboard'], function () {
			Route::any('dashboard', [ApplicationController::class, 'application_verifier_admin_dashboard'])->name('application_verifier_admin_dashboard');
		});

		Route::group(['prefix' => 'rsos_officer_grade_1'], function () {
			Route::any('dashboard', [ApplicationController::class, 'rsos_officer_grade_1'])->name('rsosofficergrade1dashboard');
		});
		Route::group(['prefix' => 'rsos_officer_grade_2'], function () {
			Route::any('dashboard', [ApplicationController::class, 'rsos_officer_grade_2'])->name('rsosofficergrade2dashboard');
		});

		Route::group(['prefix' => 'publication_dept'], function () {
			Route::any('dashboard', [ApplicationController::class, 'publication_dept'])->name('publication_dept');
		});
		
		Route::group(['prefix' => 'rsos_officer_grade_3'], function () {
			Route::any('dashboard', [ApplicationController::class, 'rsos_officer_grade_3'])->name('rsosofficergrade3dashboard');
		});
		
		Route::group(['prefix' => 'rsos_officer_grade_4'], function () {
			Route::any('dashboard', [ApplicationController::class, 'rsos_officer_grade_4'])->name('rsosofficergrade4dashboard');
		});
		
		Route::group(['prefix' => 'rsos_officer_grade_5'], function () {
			Route::any('dashboard', [ApplicationController::class, 'rsos_officer_grade_5'])->name('rsosofficergrade5dashboard');
		});
		
		Route::group(['prefix' => 'deo'], function(){
			Route::any('dashboard', [ApplicationController::class, 'deo'])->name('deodashboard');
		});
		
		Route::group(['prefix' => 'secrecy'], function () {
			Route::any('dashboard', [ApplicationController::class, 'secrecy'])->name('secrecydashboard');
		});
		
		Route::group(['prefix' => 'examination_department'], function () {
			Route::any('dashboard', [ApplicationController::class, 'examination_department'])->name('examination_department');
		});
		
		Route::group(['prefix' => 'evaluation'], function () {
			Route::any('dashboard', [ApplicationController::class, 'evaluation'])->name('evaluationdashboard');
		});

		Route::group(['prefix' => 'data_settings'], function () {
			Route::any('setup_student_allotment_marks', [DataSettingController::class, 'setup_student_allotment_marks'])->name('setup_student_allotment_marks');
			Route::any('get_running_process_on_db', [DataSettingController::class, 'get_running_process_on_db'])->name('get_running_process_on_db');
		});
		
		 
	


		Route::group(['prefix' => 'sessional'], function () {
			Route::any('dashboard', [ApplicationController::class, 'sessional'])->name('sessionaldashboard');
			Route::any('find_enrollment', [SessionalController::class, 'find_enrollment'])->name('find_enrollment');
			Route::any('marks_details/{student_id}', [SessionalController::class, 'marks_details'])->name('marks_details');
			Route::any('marks_preview_details/{student_id}', [SessionalController::class, 'marks_preview_details'])->name('marks_preview_details');
		});

		Route::group(['prefix' => 'user'], function () {
			Route::any('downloaduserExl', [UserController::class, 'downloaduserExl'])->name('downloaduserExl');
			Route::any('downloadDeoExl', [UserController::class, 'downloadDeoExl'])->name('downloadDeoExl');
			Route::any('downloaduserPdf', [UserController::class, 'downloaduserPdf'])->name('downloaduserPdf');
			Route::any('aiCentersdownloadExl', [AicenterUserController::class, 'aiCentersdownloadExl'])->name('aiCentersdownloadExl');
		});
		
		Route::group(['prefix' => 'practical'], function(){
			Route::any('/', [PracticalController::class, 'index'])->name('practicals');
			Route::any('/mapped_examiner/{user_id}', [PracticalController::class, 'mapped_examiner'])->name('mapped_examiner');
			Route::any('/createpracticalexaminerslot/{user_examiner_map_id}', [PracticalController::class, 'createpracticalexaminerslot'])->name('createpracticalexaminerslot');
			Route::any('/create_slot/{user_examiner_map_id}', [PracticalController::class, 'create_slot'])->name('create_slot');
			Route::any('/add_marks/{user_examiner_map_id}', [PracticalController::class, 'add_marks'])->name('add_marks');
			Route::any('/old_add_marks/{user_examiner_map_id}', [PracticalController::class, 'old_add_marks'])->name('old_add_marks');
			Route::any('/add_marksunlock/{user_examiner_map_id}', [PracticalController::class, 'add_marksunlock'])->name('add_marksunlock');
			Route::any('/marks_entry_delete/{user_examiner_map_id}/{}', [PracticalController::class, 'marks_entry_delete'])->name('marks_entry_delete/user_examiner_map_id');
			Route::any('/practicalMarksSubmissionPdf/{user_examiner_map_id}', [PracticalController::class, 'practicalMarksSubmissionPdf'])->name('practicalMarksSubmissionPdf');
			Route::any('/examiner_marks_entries_preview/{id}', [PracticalController::class, 'examiner_marks_entries_preview'])->name('examiner_marks_entries_preview');
			Route::any('/examiner_marks_docupload/{id}', [PracticalController::class, 'examiner_marks_docupload'])->name('examiner_marks_docupload');
			Route::any('deleteSlot/{slot_id}', [PracticalController::class, 'deleteSlot'])->name('deleteSlot');
			Route::any('viewSlotData/{slot_id}', [PracticalController::class, 'viewSlotData'])->name('viewSlotData');
			
			
		});
		
		// single screen route
		Route::any('single_screen_dates', [PageController::class, 'single_screen_dates'])->name('single_screen_dates');
		Route::any('getSubModuleList', [PageController::class, 'getSubModuleList'])->name('getSubModuleList');
		Route::any('update_ajax_single_screen_details', [PageController::class, 'updateAjaxSingleScreenDetails'])->name('update_ajax_single_screen_details');
		
		Route::any('verfication_aicodes_details/{user_type}', [PageController::class, 'verfication_aicodes_details'])->name('verfication_aicodes_details');
		Route::any('verficationDataUpdate', [PageController::class, 'verficationDataUpdate'])->name('verficationDataUpdate');
		
		
		
		
		Route::group(['prefix' => 'practicalexaminers'], function () {
			Route::any('/', [PracticalExaminerController::class, 'index'])->name('practicalexaminer');
			Route::any('practicalexamineradd', [PracticalExaminerController::class, 'add'])->name('practicalexamineradd');
			Route::any('practicalexamineredit/{id}', [PracticalExaminerController::class,'edit'])->name('practicalexamineredit');
			Route::any('practicalexaminerdestory/{id}', [PracticalExaminerController::class,'practicalexaminerdestory'])->name('practicalexaminerdestory');
			Route::any('practicalexaminerview/{id}', [PracticalExaminerController::class,'view'])->name('practicalexaminerview');
			Route::any('examiner_mapping/{user_id}', [PracticalExaminerController::class, 'examiner_mapping'])->name('examiner_mapping');
			Route::any('examiner_mapping_list', [PracticalExaminerController::class, 'examiner_mapping_list'])->name('examiner_mapping_list');
			Route::any('examiner_mapping_practical_list/{practical_user_id}', [PracticalExaminerController::class, 'examiner_mapping_practical_list'])->name('examiner_mapping_practical_list');
			Route::any('downloadPracticalExaminerListExl', [PracticalExaminerController::class, 'downloadPracticalExaminerListExl'])->name('downloadPracticalExaminerListExl');
			Route::any('downloadPracticalExaminerListPdf', [PracticalExaminerController::class, 'downloadPracticalExaminerListPdf'])->name('downloadPracticalExaminerListPdf');
			Route::any('downloadExamCenterMappingExl', [PracticalExaminerController::class, 'downloadExamCenterMappingExl'])->name('downloadExamCenterMappingExl');
			Route::any('downloadExamCenterMappingPdf', [PracticalExaminerController::class, 'downloadExamCenterMappingPdf'])->name('downloadExamCenterMappingPdf');
		Route::any('practicalMarksUnlock/{id}/{type}', [PracticalExaminerController::class,'practicalMarksUnlock'])->name('practicalMarksUnlock');
		}); 

		
		Route::group(['prefix' => 'deo'], function () {
			Route::any('/', [DeoController::class, 'index'])->name('deo');
			Route::any('deocreate', [DeoController::class, 'store'])->name('deocreate');
			Route::any('deoedit/{id}', [DeoController::class, 'deoedit'])->name('deoedit');
			Route::any('deoshow/{id}', [DeoController::class, 'deoshow'])->name('deoshow');
			Route::any('downloaddeoExl', [DeoController::class, 'downloaddeoExl'])->name('downloaddeoExl');
		}); 

		Route::group(['prefix' => 'resultupdate'], function () {
			Route::any('/updateindex/{enrollment?}', [ResultUpdateController::class, 'index'])->name('updateindex');
			Route::any('editresult/{id}',[ResultUpdateController::class, 'edit_result'])->name('editresult');
			Route::any('finalupdate/{enrollment}',[ResultUpdateController::class, 'update_final_result'])->name('finalupdate');
			Route::any('updatemarksheets',[ResultUpdateController::class,'finddata'])->name('updatemarksheets');
			Route::any('printduplicatemarksheetcertificate/{enrollment}/',[ResultUpdateController::class,'Printmarksheetcertificate'])->name('printduplicatemarksheetcertificate');
			Route::any('downloadduplicatemarksheet/{type}/{enrollment}',[ResultUpdateController::class,'download_duplicate_marksheet_pdf'])->name('downloadduplicatemarksheet');
			Route::any('downloadduplicatecertificate/{type}/{enrollment}',[ResultUpdateController::class,'download_duplicate_certificate_pdf'])->name('downloadduplicatecertificate');
			Route::any('addsubject/{enrollment}',[ResultUpdateController::class,'addSubject'])->name('addsubject');
			Route::any('download_duplicate_certificate_pdf_new/{type}/{enrollment}',[ResultUpdateController::class,'download_duplicate_certificate_pdf_new'])->name('download_duplicate_certificate_pdf_new');
			Route::any('download_duplicate_marksheet_pdf_new/{type}/{enrollment}',[ResultUpdateController::class,'download_duplicate_marksheet_pdf_new'])->name('download_duplicate_marksheet_pdf_new');
			Route::any('direct_provisional_results',[ResultUpdateController::class,'direct_provisional_results'])->name('direct_provisional_results');
			Route::any('marksheet_correction/{student_id}',[ResultUpdateController::class,'marksheetCorreaction'])->name('marksheetCorreaction');
			Route::any('corr_marksheet_previews/{mmr_id}',[ResultUpdateController::class,'corr_marksheet_previews'])->name('corr_marksheet_previews');
			Route::any('marksheet_generate_student_pdf/{mmr_id}',[ResultUpdateController::class,'marksheet_generate_student_pdf'])->name('marksheet_generate_student_pdf');
		});

		
		Route::group(['prefix' => 'pastdataupdate'], function () {
			Route::any('Serach_Enrollment', [PastdataUpdateController::class, 'enrollmentserach'])->name('Serach_Enrollment');
	
			Route::any('pastenrollmentserach', [PastdataUpdateController::class, 'pastenrollmentserach'])->name('pastenrollmentserach');
			Route::any('upload_doc', [PastdataUpdateController::class, 'uploadDocAtAnyPath'])->name('upload_doc');
			Route::any('/{id}', [PastdataUpdateController::class, 'updatePastData'])->name('pastdataupdate');
			Route::any('subjectdata/{enrollment}', [PastdataUpdateController::class, 'updatePastStudentSubjecData'])->name('subjectdata');
			Route::any('updatePastStudentSubjectmarks/{id}', [PastdataUpdateController::class, 'updatePastStudentSubjectmarks'])->name('updatePastStudentSubjectmarks');
			Route::any('pastdatafinalupdate/{id}', [PastdataUpdateController::class, 'updatepastdatafinalresult'])->name('pastdatafinalupdate');
			
		});
		Route::group(['prefix' => 'updateresult'], function () {
		    Route::any('serachnerollment', [ResultUpdateController::class, 'serachEnrollment'])->name('serachnerollment');	
			Route::any('resultsprocess',[ResultUpdateController::class,'resultsprocess'])->name('resultsprocess');
		});

		Route::group(['prefix' => 'graphical'], function () {
		    Route::any('/', [GraphicalController::class, 'index'])->name('graphical');	
		  
		});


		Route::group(['prefix' => 'prepare_sessional'], function () {
			Route::any('dashboard', [ApplicationController::class, 'prepare_sessional'])->name('preparesessionaldashboard');
			Route::any('find_enrollment', [PrepareSessionalController::class, 'find_enrollment'])->name('prepare_find_enrollment');
			Route::any('marks_details/{student_id}', [PrepareSessionalController::class, 'marks_details'])->name('prepare_marks_details');
			Route::any('marks_preview_details/{student_id}', [PrepareSessionalController::class, 'marks_preview_details'])->name('prepare_marks_preview_details');
		});
		
		 
		
		Route::group(['prefix' => 'examination_report'], function () {
			Route::any('student_fees', [ExaminationReportController::class, 'student_fees'])->name('student_fees');
			Route::any('student_org_fees', [ExaminationReportController::class, 'student_org_fees'])->name('student_org_fees');
			Route::any('aicodewisesubjectsdatastudents', [ExaminationReportController::class, 'aicodewisesubjectsdatastudents'])->name('aicodewisesubjectsdatastudents');
			// book public reports
			Route::any('courseaurmediumwisebooksubjectsdatastudents', [ExaminationReportController::class, 'courseAurMediumWiseBookSubjectsDataStudents'])->name('courseaurmediumwisebooksubjectsdatastudents');
			
			Route::any('aicodewisesubjectsdatastudentsAndSupp', [ExaminationReportController::class, 'aicodewisesubjectsdatastudentsAndSupp'])->name('aicodewisesubjectsdatastudentsAndSupp');
			
			Route::any('districtwisecategorycount', [ExaminationReportController::class, 'districtwisecategorycount'])->name('districtwisecategorycount');
			
			Route::any('downloadaicodewisesubjectsdatastudents', [ExaminationReportController::class, 'downloadaicodewisesubjectsdatastudents'])->name('downloadaicodewisesubjectsdatastudents');
			
			Route::any('downloadaicodewisesubjectsdatastudentsSupp', [ExaminationReportController::class, 'downloadaicodewisesubjectsdatastudentsSupp'])->name('downloadaicodewisesubjectsdatastudentsSupp');
			
			//Route::any('downloaddistrictwisecategorycount', [ExaminationReportController::class, 'downloaddistrictwisecategorycount'])->name('downloadaicodewisesubjectsdatastudentsSupp');
			
			Route::any('aicodewisesubjectsdatastudentsmediumtype', [ExaminationReportController::class, 'aicodewisesubjectsdatastudentsmediumtype'])->name('aicodewisesubjectsdatastudentsmediumtype');
			Route::any('downloadaicodewisesubjectsdatastudentsmediumtype', [ExaminationReportController::class, 'downloadaicodewisesubjectsdatastudentsmediumtype'])->name('downloadaicodewisesubjectsdatastudentsmediumtype');
			Route::any('aicodewisesubjectsdataallotmentstudentsdata', [ExaminationReportController::class, 'aicodewisesubjectsdataallotmentstudentsdata'])->name('aicodewisesubjectsdataallotmentstudentsdata');
			Route::any('downloadaicodewisesubjectsdataallotmentstudentsdata', [ExaminationReportController::class, 'downloadaicodewisesubjectsdataallotmentstudentsdata'])->name('downloadaicodewisesubjectsdataallotmentstudentsdata');
			
			
			Route::any('getDownloadtocchecklistsingleaicode', [ExaminationReportController::class, 'getDownloadtocchecklistsingleaicode'])->name('getDownloadtocchecklistsingleaicode');
			Route::any('getDownloadfixcodesingle', [ExaminationReportController::class, 'getDownloadfixcodesingle'])->name('getDownloadfixcodesingle');
			Route::any('getDownloadtocchecklistzipdownload{course}/{stream}/{type}', [ExaminationReportController::class, 'getDownloadtocchecklistzipdownload'])->name('getDownloadtocchecklistzipdownload');
			Route::any('getDownloadfixcodezipdownload{course}/{stream}', [ExaminationReportController::class, 'getDownloadfixcodezipdownload'])->name('getDownloadfixcodezipdownload');
			
			Route::any('downloadTocCheckListsallaicenterwisePdf/{course}/{stream}', [ExaminationReportController::class, 'downloadTocCheckListsallaicenterwisePdf'])->name('downloadTocCheckListsallaicenterwisePdf');
			Route::any('allcenterwisetocchecklist', [ExaminationReportController::class, 'allcenterwisetocchecklist'])->name('allcenterwisetocchecklist');
			Route::any('aicentercoursewisestudentcount', [ExaminationReportController::class, 'aicentercoursewisestudentcount'])->name('aicentercoursewisestudentcount');
			Route::any('aicentercoursewisestudentcountexcel', [ExaminationReportController::class, 'aicentercoursewisestudentcountexcel'])->name('aicentercoursewisestudentcountexcel');
			Route::any('sessional_report', [ExaminationReportController::class, 'sessional_report'])->name('sessional_report');
			Route::any('sessional_report_h', [ExaminationReportController::class, 'sessional_report_h'])->name('sessional_report_h');
			Route::any('prepare_sessional_report_h', [ExaminationReportController::class, 'prepare_sessional_report_h'])->name('prepare_sessional_report_h');
			Route::any('downloadStudentFeesExl', [ExaminationReportController::class, 'downloadStudentFeesExl'])->name('downloadStudentFeesExl');
			Route::any('downloadStudentorgFeesExl', [ExaminationReportController::class, 'downloadStudentorgFeesExl'])->name('downloadStudentorgFeesExl');
			Route::any('downloadStudentFeesPdf', [ExaminationReportController::class, 'downloadStudentFeesPdf'])->name('downloadStudentFeesPdf');
			Route::any('studentchecklists', [ExaminationReportController::class, 'studentchecklists'])->name('studentchecklists');
			Route::any('downloadstudentchecklistsPdf', [ExaminationReportController::class,'downloadstudentchecklistsPdf'])->name('downloadstudentchecklistsPdf'); 
			//{course}/{stream}/{ai_code}/{late_fee}
			
			Route::any('downloadstudentchecklistsPdf1', [ExaminationReportController::class,'downloadstudentchecklistsPdf1'])->name('downloadstudentchecklistsPdf1');
			
			Route::any('downloadstudentchecklistsPdfselectaicode', [ExaminationReportController::class,'downloadstudentchecklistsPdfselectaicode'])->name('downloadstudentchecklistsPdfselectaicode');
			
			Route::any('fixcodeupdatestudentallotmentmarks/{limit}', [ExaminationReportController::class,'fixcodeupdatestudentallotmentmarks'])->name('fixcodeupdatestudentallotmentmarks');


			Route::any('tocChecklists', [ExaminationReportController::class, 'tocChecklists'])->name('tocChecklists');

			Route::any('downloadtocchecklistsPdf1', [ExaminationReportController::class,'downloadtocchecklistsPdf1'])->name('downloadtocchecklistsPdf1');

			Route::any('downloadTocCheckListsPdf/{course}/{stream}/{ai_code}', [ExaminationReportController::class,'downloadTocCheckListsPdf'])->name('downloadTocCheckListsPdf');

			Route::any('SupplementaryChecklists', [ExaminationReportController::class, 'SupplementaryChecklists'])->name('SupplementaryChecklists');
			

			Route::any('downloadsupplementaryCheckListsPdf/{course}/{stream}/{ai_code}', [ExaminationReportController::class,'downloadsupplementaryCheckListsPdf'])->name('downloadsupplementaryCheckListsPdf');

			Route::any('downloadsupplementarychecklistsPdf1', [ExaminationReportController::class,'downloadsupplementarychecklistsPdf1'])->name('downloadsupplementarychecklistsPdf1');

			Route::any('reportexamcenterwise', [ExaminationReportController::class, 'reportexamcenterwise'])->name('reportexamcenterwise');

			Route::any('reportexamcenterwisePdf', [ExaminationReportController::class,'reportexamcenterwisePdf'])->name('reportexamcenterwisePdf');
			Route::any('reportexamcenterwiseview', [ExaminationReportController::class,'reportexamcenterwiseview'])->name('reportexamcenterwiseview');
			Route::any('nominalnr', [ExaminationReportController::class,'nominalnrview'])->name('nominalnr');
			Route::any('nominalnrpdf/{course}/{stream}/{ai_code}', [ExaminationReportController::class,'Nominalnrpdf'])->name('nominalnrpdf');

			Route::any('NominalnrpdfDistrict/{course}/{stream}/{district}', [ExaminationReportController::class,'NominalnrpdfDistrict'])->name('NominalnrpdfDistrict');

            Route::any('aicenterwisestudentandfeesdata', [ExaminationReportController::class, 'aicenterwisestudentandfeesdata'])->name('aicenterwisestudentandfeesdata');

			Route::any('boardnrstudentenrollmentview', [ExaminationReportController::class,'board_nr_student_enrollment_view'])->name('boardnrstudentenrollmentview');
			Route::any('boardnrstudentenrollmentsheetexcel/{course}', [ExaminationReportController::class,'board_nr_student_enrollment_sheet_excel_download'])->name('boardnrstudentenrollmentsheetexcel');
			Route::any('nominalnrgenerateview', [ExaminationReportController::class,'Nominalnrgenerateview'])->name('nominalnrgenerateview');

			Route::any('hallticketbulkviews', [ExaminationReportController::class,'hallticketbulkviews'])->name('hallticketbulkviews');
			
			

			Route::any('downloadhallticketbulviewpdf', [ExaminationReportController::class,'downloadhallticketbulviewpdf'])->name('downloadhallticketbulviewpdf');



			Route::any('downloadnominalnrgenerateviewpdf', [ExaminationReportController::class,'downloadnominalnrgenerateviewpdf'])->name('downloadnominalnrgenerateviewpdf');
			
			Route::any('examcenter_material', [ExaminationReportController::class,'examcenter_material'])->name('examcenter_material');
			Route::any('examcenter_material_zip_download/{course}', [ExaminationReportController::class,'examcenter_material_zip_download'])->name('examcenter_material_zip_downlaod');
			Route::any('reportexamcenterwiseexcel', [ExaminationReportController::class,'reportexamcenterwiseexcel'])->name('reportexamcenterwiseexcel');
			Route::any('reportexamcenterwisesupplementarycountexcel', [ExaminationReportController::class,'reportexamcenterwisesupplementarycountexcel'])->name('reportexamcenterwisesupplementarycountexcel');
			Route::any('studentfeesummary', [ExaminationReportController::class,'getStudentFeeSummaryPay'])->name('studentfeesummary');
			Route::any('downloadstudentfeesummaryFeesExl', [ExaminationReportController::class,'downloadstudentfeesummaryFeesExl'])->name('downloadstudentfeesummaryFeesExl');
			Route::any('downloadstudentfeesummaryPdf', [ExaminationReportController::class,'downloadstudentfeesummaryPdf'])->name('downloadstudentfeesummaryPdf');
			Route::any('studentfeeorgsummary', [ExaminationReportController::class, 'studentfeeorgsummary'])->name('studentfeeorgsummary');
			Route::any('downloadstudentfeesorgummaryFeesExl', [ExaminationReportController::class, 'downloadstudentfeesorgummaryFeesExl'])->name('downloadstudentfeesorgummaryFeesExl');
			Route::any('downloadstudentfeesorgummaryPdf', [ExaminationReportController::class, 'downloadstudentfeesorgummaryPdf'])->name('downloadstudentfeesorgummaryPdf');
			Route::any('hall_ticket_bulk_download/{course}/{stream}/{ai_code}', [ExaminationReportController::class, 'hall_ticket_bulk_download'])->name('hall_ticket_bulk_download');

			Route::any('hall_ticket_bulk_enrollmentdownload/{course}/{stream}/{ai_code}/{enrollment}', [ExaminationReportController::class, 'hall_ticket_bulk_enrollmentdownload'])->name('hall_ticket_bulk_enrollmentdownload');
			
			Route::any('hall_ticket_single_enrollment_download', [ExaminationReportController::class, 'hall_ticket_single_enrollment_download'])->name('hall_ticket_single_enrollment_download');
			
			Route::any('hallticketsinglegeneratesuperadminenrollment', [ExaminationReportController::class, 'hallticketsinglegeneratesuperadminenrollment'])->name('hallticketsinglegeneratesuperadminenrollment');
			

			Route::any('hallticketbulkenrollmentviews', [ExaminationReportController::class,'hallticketbulkenrollmentviews'])->name('hallticketbulkenrollmentviews');

			Route::any('downloadhallticketbulviewpdfenrollment', [ExaminationReportController::class,'downloadhallticketbulviewpdfenrollment'])->name('downloadhallticketbulviewpdfenrollment');


			Route::any('hall_ticket_bulk_downloads_all/{course}/{stream}/{ai_code}', [ExaminationReportController::class, 'hall_ticket_bulk_downloads_all'])->name('hall_ticket_bulk_downloads_all');


			Route::any('hall_ticket_bulk_downloads_district/{course}/{stream}/{district}', [ExaminationReportController::class, 'hall_ticket_bulk_downloads_district'])->name('hall_ticket_bulk_downloads_district');

			Route::any('exam_student_rollwise_pdf/{course}/{stream}/{districtid}', [ExaminationReportController::class, 'exam_student_rollwise_pdf'])->name('exam_student_rollwise_pdf');

			Route::any('single_exam_student_rollwise_pdf/{course}/{stream}/{districtid}/{ecenter}', [ExaminationReportController::class, 'single_exam_student_rollwise_pdf'])->name('single_exam_student_rollwise_pdf');

			Route::any('exam_practical_student_rollwise_pdf/{course}/{stream}/{districtid}', [ExaminationReportController::class, 'exam_practical_student_rollwise_pdf'])->name('exam_practical_student_rollwise_pdf');

			Route::any('single_exam_practical_student_rollwise_pdf/{course}/{stream}/{districtid}/{ecenter}', [ExaminationReportController::class, 'single_exam_practical_student_rollwise_pdf'])->name('single_exam_practical_student_rollwise_pdf');

			Route::any('exam_center_nominal_roll_pdf/{course}/{stream}/{districtid}', [ExaminationReportController::class, 'exam_center_nominal_roll_pdf'])->name('exam_center_nominal_roll_pdf');

			Route::any('single_exam_center_nominal_roll_pdf/{course}/{stream}/{districtid}/{ecenter}', [ExaminationReportController::class, 'single_exam_center_nominal_roll_pdf'])->name('single_exam_center_nominal_roll_pdf');

			Route::any('single_exam_center_nominal_roll_pdf_view', [ExaminationReportController::class, 'single_exam_center_nominal_roll_pdf_view'])->name('single_exam_center_nominal_roll_pdf_view');

			Route::any('single_exam_center_nominal_roll_pdf_request', [ExaminationReportController::class, 'single_exam_center_nominal_roll_pdf_request'])->name('single_exam_center_nominal_roll_pdf_request');

			Route::any('single_exam_center_attendance_roll_pdf_view', [ExaminationReportController::class, 'single_exam_center_attendance_roll_pdf_view'])->name('single_exam_center_attendance_roll_pdf_view');

			Route::any('single_exam_center_attendance_roll_pdf_request', [ExaminationReportController::class, 'single_exam_center_attendance_roll_pdf_request'])->name('single_exam_center_attendance_roll_pdf_request');


			Route::any('single_exam_center_theorynominal_roll_pdf_view', [ExaminationReportController::class, 'single_exam_center_theorynominal_roll_pdf_view'])->name('single_exam_center_theorynominal_roll_pdf_view');

			Route::any('getDownloadnominalrollexamcenterwise', [ExaminationReportController::class, 'getDownloadnominalrollexamcenterwise'])->name('getDownloadnominalrollexamcenterwise');


			Route::any('single_exam_center_theorynominal_roll_pdf_request', [ExaminationReportController::class, 'single_exam_center_theorynominal_roll_pdf_request'])->name('single_exam_center_theorynominal_roll_pdf_request');


			Route::any('single_exam_center_practicalnominal_roll_pdf_view', [ExaminationReportController::class, 'single_exam_center_practicalnominal_roll_pdf_view'])->name('single_exam_center_practicalnominal_roll_pdf_view');

			Route::any('single_exam_center_practicalnominal_roll_pdf_request', [ExaminationReportController::class, 'single_exam_center_practicalnominal_roll_pdf_request'])->name('single_exam_center_practicalnominal_roll_pdf_request');

			Route::any('single_exam_center_theorysignaturenominal_roll_pdf_view', [ExaminationReportController::class, 'single_exam_center_theorysignaturenominal_roll_pdf_view'])->name('single_exam_center_theorysignaturenominal_roll_pdf_view');

			Route::any('single_exam_center_theorysignaturenominal_roll_pdf_request', [ExaminationReportController::class, 'single_exam_center_theorysignaturenominal_roll_pdf_request'])->name('single_exam_center_theorysignaturenominal_roll_pdf_request');


			Route::any('single_exam_center_practicalsignaturenominal_roll_pdf_view', [ExaminationReportController::class, 'single_exam_center_practicalsignaturenominal_roll_pdf_view'])->name('single_exam_center_practicalsignaturenominal_roll_pdf_view');

			Route::any('single_exam_center_practicalsignaturenominal_roll_pdf_request', [ExaminationReportController::class, 'single_exam_center_practicalsignaturenominal_roll_pdf_request'])->name('single_exam_center_practicalsignaturenominal_roll_pdf_request');


			Route::any('getDownloadnominalrollaicodecenterwise/{course}/{stream}', [ExaminationReportController::class,'getDownloadnominalrollaicodecenterwise'])->name('getDownloadnominalrollaicodecenterwise');

			Route::any('getDownloadhallticketaicodecenterwise/{course}/{stream}', [ExaminationReportController::class,'getDownloadhallticketaicodecenterwise'])->name('getDownloadhallticketaicodecenterwise');

			Route::any('getpublishaicentermaterial', [ExaminationReportController::class,'getpublishaicentermaterial'])->name('getpublishaicentermaterial');


			Route::any('getpublishaicentermaterialedit/{id}', [ExaminationReportController::class,'getpublishaicentermaterialedit'])->name('getpublishaicentermaterialedit');


			Route::any('excenterattendancesheet/{course}/{stream}/{districtid}', [ExaminationReportController::class,'excenterattendancesheet'])->name('excenterattendancesheet');

			Route::any('singleexcenterattendancesheet/{course}/{stream}/{districtid}/{ecenter}', [ExaminationReportController::class,'singleexcenterattendancesheet'])->name('singleexcenterattendancesheet');

			Route::any('exam_subjectpractical_student_rollwise_pdf/{course}/{stream}/{districtid}', [ExaminationReportController::class, 'exam_subjectpractical_student_rollwise_pdf'])->name('exam_subjectpractical_student_rollwise_pdf');

			Route::any('single_exam_subjectpractical_student_rollwise_pdf/{course}/{stream}/{districtid}/{ecenter}', [ExaminationReportController::class, 'single_exam_subjectpractical_student_rollwise_pdf'])->name('single_exam_subjectpractical_student_rollwise_pdf');

			Route::any('enrollment_fixcode_view/{subjectscode}/{stream}/{course}', [ExaminationReportController::class, 'enrollment_fixcode_view'])->name('enrollment_fixcode_view');

			Route::any('enrollment_fixcode_view_bulk/{subjectscode}/{stream}/{course}', [ExaminationReportController::class, 'enrollment_fixcode_view_bulk'])->name('enrollment_fixcode_view_bulk');

			Route::any('selectfixcodewisedata', [ExaminationReportController::class, 'selectfixcodewisedata'])->name('selectfixcodewisedata');

			Route::any('selected_enrollment_fixcode_view_bulk', [ExaminationReportController::class, 'selected_enrollment_fixcode_view_bulk'])->name('selected_enrollment_fixcode_view_bulk');

			Route::any('enrollment_fixcode_views', [ExaminationReportController::class, 'enrollment_fixcode_views'])->name('enrollment_fixcode_views');
 
			Route::any('enrollment_fixcode_views_requests', [ExaminationReportController::class, 'enrollment_fixcode_views_requests'])->name('enrollment_fixcode_views_requests');
  
			Route::any('single_enrollment_fixcode_view/{subjectscode}/{stream}/{course}/{ecenter}', [ExaminationReportController::class, 'single_enrollment_fixcode_view'])->name('single_enrollment_fixcode_view');
			Route::any('exam_subjectthory_student_rollwise_pdf/{course}/{stream}/{districtid}', [ExaminationReportController::class, 'exam_subjectthory_student_rollwise_pdf'])->name('exam_subjectthory_student_rollwise_pdf');
			Route::any('single_exam_subjectthory_student_rollwise_pdf/{course}/{stream}/{districtid}/{ecenter}', [ExaminationReportController::class, 'single_exam_subjectthory_student_rollwise_pdf'])->name('single_exam_subjectthory_student_rollwise_pdf');
			Route::any('examcentersubjectscount', [ExaminationReportController::class, 'examcentersubjectscount'])->name('examcentersubjectscount');
			Route::any('aicenterwisestudentandfeesdataexcel', [ExaminationReportController::class, 'aicenterwisestudentandfeesdataexcel'])->name('aicenterwisestudentandfeesdataexcel');
			Route::any('downloadsessionalexportExl', [ExaminationReportController::class, 'downloadsessionalexportExl'])->name('downloadsessionalexportExl');
			Route::any('downloadPreparesessionalexportExl', [ExaminationReportController::class, 'downloadPreparesessionalexportExl'])->name('downloadPreparesessionalexportExl');
			Route::any('aicentersubjectsexport/{course}', [ExaminationReportController::class, 'aicentersubjectsexport'])->name('aicentersubjectsexport');
			Route::any('fixcodegenerate/{stream}', [ExaminationReportController::class, 'fixcodegenerate'])->name('fixcodegenerate');
		
		}); 
	 
		Route::group(['prefix' => 'sms_manage'], function () { 
			Route::any('index', [SmsController::class, 'index'])->name('index');
		});
		
		Route::group(['prefix' => 'supp_admission_report'], function () {
			Route::any('supplementary_admin_student_applications', [SuppAdminReportsController::class, 'supplementary_admin_student_applications'])->name('supplementary_admin_student_applications');
			Route::any('supp_admin_preview_details/{student_id}', [SuppAdminReportsController::class, 'supp_admin_preview_details'])->name('supp_admin_preview_details');
			Route::get('supp_generate_admin_student_pdf/{student_id}', [SuppAdminReportsController::class, 'supp_generate_admin_student_pdf'])->name('supp_generate_admin_student_pdf');
        });
		
			
		Route::group(['prefix' => 'admission_report'], function () {
			Route::any('applications', [AdmissionReportsController::class, 'applications'])->name('applications');
			Route::any('verifying_student_applications', [AdmissionReportsController::class, 'verifying_student_applications'])->name('verifying_student_applications');
			Route::any('student_applications', [AdmissionReportsController::class, 'student_applications'])->name('student_applications');
			Route::any('student_locksumbited', [AdmissionReportsController::class, 'student_locksumbited_applications'])->name('student_locksumbited');
			Route::any('Practical_mapped', [AdmissionReportsController::class, 'Practical_mapped'])->name('Practical_mapped');
			Route::any('allstudent_locksumbited', [AdmissionReportsController::class, 'allstudent_locksumbited_applications'])->name('allstudent_locksumbited');
			Route::any('student_payment_details', [AdmissionReportsController::class, 'student_payment_details'])->name('student_payment_details');
			Route::any('allstudent_payment_details', [AdmissionReportsController::class, 'allstudent_payment_details'])->name('allstudent_payment_details');
			Route::any('student_not_pay_details', [AdmissionReportsController::class,'student_not_pay_payment_details'])->name('student_not_pay_details');
			Route::any('downloadallstudent_not_pay_payment_detailsExl', [AdmissionReportsController::class,'downloadallstudent_not_pay_payment_detailsExl'])->name('downloadallstudent_not_pay_payment_detailsExl');
			Route::any('downloadApplicationlocksubmitdataExl', [AdmissionReportsController::class,'downloadApplicationlocksubmitdataExl'])->name('downloadApplicationlocksubmitdataExl');
			Route::any('downloadallstudentzerofeespaydetailsApplicationExl', [AdmissionReportsController::class,'downloadallstudentzerofeespaydetailsApplicationExl'])->name('downloadallstudentzerofeespaydetailsApplicationExl');
			Route::any('downloadallstudentpaymentdetailsApplicationExl', [AdmissionReportsController::class,'downloadallstudentpaymentdetailsApplicationExl'])->name('downloadallstudentpaymentdetailsApplicationExl');
			Route::any('downloadsupplementarystudentlocksumbitedapplicationsExl', [AdmissionReportsController::class,'downloadsupplementarystudentlocksumbitedapplicationsExl'])->name('downloadsupplementarystudentlocksumbitedapplicationsExl');
			Route::any('downloadallsupplementarystudentnotpaypaymentdetailsExl', [AdmissionReportsController::class,'downloadallsupplementarystudentnotpaypaymentdetailsExl'])->name('downloadallsupplementarystudentnotpaypaymentdetailsExl');
			Route::any('downloadallsupplementarystudentpaymentdetailsExl', [AdmissionReportsController::class,'downloadallsupplementarystudentpaymentdetailsExl'])->name('downloadallsupplementarystudentpaymentdetailsExl');
			Route::any('allstudent_not_pay_details', [AdmissionReportsController::class,'allstudent_not_pay_payment_details'])->name('allstudent_not_pay_details');
			Route::any('allstudent_zero_fees_pay_details', [AdmissionReportsController::class,'allstudent_zero_fees_pay_details'])->name('allstudent_zero_fees_pay_details');
			Route::any('downloadApplicationExl', [AdmissionReportsController::class, 'downloadApplicationExl'])->name('downloadApplicationExl');
			Route::any('downloadFreshStudentVerifyingExl', [AdmissionReportsController::class, 'downloadFreshStudentVerifyingExl'])->name('downloadFreshStudentVerifyingExl');
			Route::any('downloadApplicationPdf', [AdmissionReportsController::class, 'downloadApplicationPdf'])->name('downloadApplicationPdf');
			
			Route::any('application_ai_center_wise_count', [AdmissionReportsController::class, 'application_ai_center_wise_count'])->name('application_ai_center_wise_count');
			Route::any('student_application_ai_center_wise_count', [AdmissionReportsController::class, 'student_application_ai_center_wise_count'])->name('student_application_ai_center_wise_count');
			Route::any('student_application_ai_center_course_wise_count', [AdmissionReportsController::class, 'student_application_ai_center_course_wise_count'])->name('student_application_ai_center_course_wise_count');
			Route::any('downloadstudentaicenterwisecountExl', [AdmissionReportsController::class, 'downloadstudentaicenterwisecountExl'])->name('downloadstudentaicenterwisecountExl');
			Route::any('downloadstudentaicentercoursewisecountExl', [AdmissionReportsController::class, 'downloadstudentaicentercoursewisecountExl'])->name('downloadstudentaicentercoursewisecountExl');
			Route::any('downloadstudentaicenterwisecountPdf', [AdmissionReportsController::class, 'downloadstudentaicenterwisecountPdf'])->name('downloadstudentaicenterwisecountPdf');
			Route::any('toc_students_ai_center_wise_count', [AdmissionReportsController::class, 'toc_students_ai_center_wise_count'])->name('toc_students_ai_center_wise_count');
			Route::any('subject_wise_student_count', [AdmissionReportsController::class, 'subject_wise_student_count'])->name('subject_wise_student_count');
			Route::any('subject_wise_student_count_steams', [AdmissionReportsController::class, 'subject_wise_student_count_steams'])->name('subject_wise_student_count_steams');
			Route::any('hall_ticket_view', [AdmissionReportsController::class, 'hall_ticket_view'])->name('hall_ticket_view');
			Route::any('supplementariesubjectreport', [AdmissionReportsController::class, 'supplementariesubjectreport'])->name('supplementariesubjectreport');
			Route::any('supplementariesubjectexcel', [AdmissionReportsController::class, 'supplementariesubjectexcel'])->name('supplementariesubjectexcel');
			Route::any('supplementariefeesreport', [AdmissionReportsController::class, 'supplementariefeesreport'])->name('supplementariefeesreport');
			Route::any('supplementariefeesexcel', [AdmissionReportsController::class, 'supplementariefeesexcel'])->name('supplementariefeesexcel');
			Route::any('supplementarieaicenterwisecount', [AdmissionReportsController::class, 'supplementarieaicenterwisecount'])->name('supplementarieaicenterwisecount');
			Route::any('supplementarieaicenterwisecountexcel', [AdmissionReportsController::class, 'supplementarieaicenterwisecountexcel'])->name('supplementarieaicenterwisecountexcel');
			Route::any('supplementariedownloadstudentaicenterwisecountPdf', [AdmissionReportsController::class, 'supplementariedownloadstudentaicenterwisecountPdf'])->name('supplementariedownloadstudentaicenterwisecountPdf');
			Route::any('Femaleandmalefeesexceldownload', [AdmissionReportsController::class, 'Femaleandmalefeesexceldownload'])->name('Femaleandmalefeesexceldownload');
			Route::any('supplementary_student_applications', [AdmissionReportsController::class, 'supplementary_student_applications'])->name('supplementary_student_applications');
			Route::any('change_request_student_applications', [AdmissionReportsController::class, 'change_request_student_applications'])->name('change_request_student_applications');
			Route::any('change_request_student_total_Generated', [AdmissionReportsController::class, 'change_request_student_total_Generated'])->name('change_request_student_total_Generated');
			Route::any('supp_change_request_student_total_Generated', [AdmissionReportsController::class, 'supp_change_request_student_total_Generated'])->name('supp_change_request_student_total_Generated');
			Route::any('supp_change_request_student_student_not_update_applications', [AdmissionReportsController::class, 'supp_change_request_student_student_not_update_applications'])->name('supp_change_request_student_student_not_update_applications');
			Route::any('supp_change_request_student_not_locksumbitted', [AdmissionReportsController::class, 'supp_change_request_student_not_locksumbitted'])->name('supp_change_request_student_not_locksumbitted');
			Route::any('supp_change_request_student_completed', [AdmissionReportsController::class, 'supp_change_request_student_completed'])->name('supp_change_request_student_completed');
			Route::any('supp_change_request_student_not_fees_pay', [AdmissionReportsController::class, 'supp_change_request_student_not_fees_pay'])->name('supp_change_request_student_not_fees_pay');
			
			Route::any('change_request_student_student_not_update_applications', [AdmissionReportsController::class, 'change_request_student_student_not_update_applications'])->name('change_request_student_student_not_update_applications');
			Route::any('change_request_student_not_locksumbitted', [AdmissionReportsController::class, 'change_request_student_not_locksumbitted'])->name('change_request_student_not_locksumbitted');
			Route::any('change_request_student_completed', [AdmissionReportsController::class, 'change_request_student_completed'])->name('change_request_student_completed');
			Route::any('change_request_student_not_fees_pay', [AdmissionReportsController::class, 'change_request_student_not_fees_pay'])->name('change_request_student_not_fees_pay');
			Route::any('supp_change_request_student_applications', [AdmissionReportsController::class, 'supp_change_request_student_applications'])->name('supp_change_request_student_applications');
			Route::any('supp_change_request_total_applications', [AdmissionReportsController::class, 'supp_change_request_total_applications'])->name('supp_change_request_total_applications');
			Route::any('supp_change_request_student_applications_not_Submitted', [AdmissionReportsController::class, 'supp_change_request_student_applications_not_Submitted'])->name('supp_change_request_student_applications_not_Submitted');
			Route::any('supp_change_request_student_applications_not_fees_pay', [AdmissionReportsController::class, 'supp_change_request_student_applications_not_fees_pay'])->name('supp_change_request_student_applications_not_fees_pay');
			Route::any('downloadchangerequertStudentExl', [AdmissionReportsController::class, 'downloadchangerequertStudentExl'])->name('downloadchangerequertStudentExl');
			Route::any('reval_student_applications', [AdmissionReportsController::class, 'reval_student_applications'])->name('reval_student_applications');
			Route::any('supplementary_student_locksumbited_applications', [AdmissionReportsController::class, 'supplementary_student_locksumbited_applications'])->name('supplementary_student_locksumbited_applications');
			Route::any('allsupplementary_student_payment_details', [AdmissionReportsController::class, 'allsupplementary_student_payment_details'])->name('allsupplementary_student_payment_details');
			Route::any('allsupplementary_student_not_pay_payment_details', [AdmissionReportsController::class, 'allsupplementary_student_not_pay_payment_details'])->name('allsupplementary_student_not_pay_payment_details');
			Route::any('supplementary_aicenter_student_payment_details', [AdmissionReportsController::class, 'supplementary_aicenter_student_payment_details'])->name('supplementary_aicenter_student_payment_details');
			Route::any('supplementary_aicenter_student_not_pay_payment_details', [AdmissionReportsController::class, 'supplementary_aicenter_student_not_pay_payment_details'])->name('supplementary_aicenter_student_not_pay_payment_details');
			Route::any('downloadSupplementaryApplicationExl', [AdmissionReportsController::class, 'downloadSupplementaryApplicationExl'])->name('downloadSupplementaryApplicationExl');
			Route::any('downloadRevalApplicationExl', [AdmissionReportsController::class, 'downloadRevalApplicationExl'])->name('downloadRevalApplicationExl');
			Route::any('downloadSupplementaryApplicationPdf', [AdmissionReportsController::class, 'downloadSupplementaryApplicationPdf'])->name('downloadSupplementaryApplicationPdf');
			Route::any('downloadsubjectwisecountExl', [AdmissionReportsController::class, 'downloadsubjectwisecountExl'])->name('downloadsubjectwisecountExl');

			Route::any('supplementary_subject_wise_student_count', [AdmissionReportsController::class, 'supplementary_subject_wise_student_count'])->name('supplementary_subject_wise_student_count');
			Route::any('aicodewisesubjectsdatastudentsall', [AdmissionReportsController::class, 'aicodewisesubjectsdatastudentsall'])->name('aicodewisesubjectsdatastudentsall');
			Route::any('downloadaicodewisesubjectsdatastudentsall', [AdmissionReportsController::class, 'downloadaicodewisesubjectsdatastudentsall'])->name('downloadaicodewisesubjectsdatastudentsall');
			Route::any('downloadsupplementarysubjectwisecountExl', [AdmissionReportsController::class, 'downloadsupplementarysubjectwisecountExl'])->name('downloadsupplementarysubjectwisecountExl');
			Route::any('downloadstudentupdatedataExl', [AdmissionReportsController::class, 'downloadstudentupdatedataExl'])->name('downloadstudentupdatedataExl');
			Route::any('aicenterWiseCountFreshAndSupplementary', [AdmissionReportsController::class, 'aicenterWiseCountFreshAndSupplementary'])->name('aicenterWiseCountFreshAndSupplementary');
			Route::any('revised_duplicate_report',[AdmissionReportsController::class,'getMarksheetRequestData'])->name('revised_duplicate_report');
		});
			
		Route::group(['prefix' => 'examdate'], function(){
			Route::any('add',[ExamLateFeeDatesController::class, 'add'])->name('examdateadd');
			Route::any('edit/{id}',[ExamLateFeeDatesController::class, 'edit'])->name('examdateedit');
			Route::any('index',[ExamLateFeeDatesController::class, 'index'])->name('examdateindex');
			
		});
			
		Route::group(['prefix' => 'examcenter_allotment'], function () {
			Route::any('examcenter_aicenter_mapping_stream/{id}', [ExamcenterAllotmentController::class, 'examcenter_aicenter_mapping_stream'])->name('examcenter_aicenter_mapping_stream');
			Route::any('examcenter_aicenter_mapping_stream1/{id}', [ExamcenterAllotmentController::class, 'examcenter_aicenter_mapping_stream1'])->name('examcenter_aicenter_mapping_stream1');
			Route::any('examcenter_aicenter_mapping_stream2/{id}', [ExamcenterAllotmentController::class, 'examcenter_aicenter_mapping_stream2'])->name('examcenter_aicenter_mapping_stream2');
			Route::any('preview_centerallotment_stream1/{id}', [ExamcenterAllotmentController::class, 'preview_centerallotment_stream1'])->name('preview_centerallotment_stream1');
			Route::any('preview_centerallotment_stream2/{id}', [ExamcenterAllotmentController::class, 'preview_centerallotment_stream2'])->name('preview_centerallotment_stream2');
			Route::any('studentenrollmentallotment/{ai_code}/{course}/{stream}/{examcenterdetailid}/{centerallotmentid}/{supplementary}', [ExamcenterAllotmentController::class, 'studentenrollmentallotment'])->name('studentenrollmentallotment');
			Route::any('suppstudentenrollmentallotment/{ai_code}/{course}/{stream}/{examcenterdetailid}/{centerallotmentid}/{supplementary}', [ExamcenterAllotmentController::class, 'suppstudentenrollmentallotment'])->name('suppstudentenrollmentallotment');
		});
		
		Route::group(['prefix' => 'examcenter_details'], function () {
			Route::any('listing', [ExamcenterDetailController::class, 'listing'])->name('listing');
			Route::any('center_student_allotment_report', [ExamcenterDetailController::class, 'center_student_allotment_report'])->name('center_student_allotment_report');
			Route::any('center_student_allotment_report_excel', [ExamcenterDetailController::class, 'center_student_allotment_report_excel'])->name('center_student_allotment_report_excel');
			Route::any('all_listing', [ExamcenterDetailController::class, 'all_listing'])->name('all_listing');
			Route::any('all_examcenterlisting', [ExamcenterDetailController::class, 'all_examcenterlisting'])->name('all_examcenterlisting');
			Route::any('excenterlisting', [ExamcenterDetailController::class, 'listing'])->name('excenterlisting');
			Route::any('examcenter_add', [ExamcenterDetailController::class, 'add'])->name('examcenter_add');
			Route::any('create_exam_center/{id}', [ExamcenterDetailController::class, 'add'])->name('create_exam_center');
			Route::any('mark_active/{id}', [ExamcenterDetailController::class, 'mark_active'])->name('mark_active');
			Route::any('edit/{id}', [ExamcenterDetailController::class, 'edit'])->name('edit');
			Route::any('edit/{id}', [ExamcenterDetailController::class, 'edit'])->name('examcenter_update');
			Route::any('editexamcenter/{id}', [ExamcenterDetailController::class, 'editexamcenter'])->name('editexamcenter');
			Route::any('examcenters_aicenter_unmaapeduserid/{id}', [ExamcenterDetailController::class, 'examcenters_aicenter_unmaapeduserid'])->name('examcenters_aicenter_unmaapeduserid');
			Route::any('delete/{id}', [ExamcenterDetailController::class, 'delete'])->name('delete');
			Route::any('downloadExamCenterExl', [ExamcenterDetailController::class, 'downloadExamCenterExl'])->name('downloadExamCenterExl');
			Route::any('examcenter_aicenter_unmaapeduserid/{id}', [ExamcenterDetailController::class, 'examcenter_aicenter_unmaapeduserid'])->name('examcenter_aicenter_unmaapeduserid');
		});

		
		
		Route::group(['prefix' => 'school'], function () {
			Route::any('listing', [SchoolController::class, 'listing'])->name('schoollisting');
			Route::any('create', [SchoolController::class, 'create'])->name('create');
			
			Route::any('add', [SchoolController::class, 'add'])->name('add');
			
			Route::any('edit/{id}', [SchoolController::class, 'edit'])->name('edit');
			Route::any('delete/{id}', [SchoolController::class, 'delete'])->name('delete');
			Route::any('schoolexportexcel', [SchoolController::class, 'schoolexportexcel'])->name('schoolexportexcel');
		});


		Route::group(['prefix' => 'tabletables'], function () {
			Route::any('tabletableslisting', [TimeTableController::class, 'tabletableslisting'])->name('tabletableslisting');
			Route::any('tabletablescreate', [TimeTableController::class, 'tabletablescreate'])->name('tabletablescreate');
			Route::any('tabletablesadd', [TimeTableController::class, 'tabletablesadd'])->name('tabletablesadd');
			Route::any('edit/{id}', [TimeTableController::class, 'edit'])->name('edit');
			Route::any('update/{id}', [TimeTableController::class, 'edit'])->name('tabletablesupdate');
			Route::any('delete/{id}', [TimeTableController::class, 'delete'])->name('delete');
			Route::any('timetableexportexcel', [TimeTableController::class, 'timetableexportexcel'])->name('timetableexportexcel');
		});
		
		Route::group(['prefix' => 'logs'], function () {
			Route::any('logslisting', [LogController::class, 'logslisting'])->name('logslisting');
			Route::any('downloadlogslistingexcel', [LogController::class, 'downloadlogslistingexcel'])->name('downloadlogslistingexcel');
			Route::any('logdebug', [LogController::class, 'logDebug'])->name('logdebug');
			Route::any('downloadlog', [LogController::class, 'downloadlog'])->name('downloadlog');
			Route::any('deletelog', [LogController::class, 'deletelog'])->name('deletelog');
			Route::any('viewlog', [LogController::class, 'viewlog'])->name('viewlog');

		});

		
		Route::group(['prefix' => 'mapping_examiners'], function () {
			Route::any('/', [MappingExaminerController::class, 'index'])->name('mapping_examiners');
			Route::any('add', [MappingExaminerController::class, 'add'])->name('mapping_examiners.add');
			Route::any('edit/{id}', [MappingExaminerController::class, 'edit'])->name('edit');
			Route::any('delete/{id}',[MappingExaminerController::class, 'delete'])->name('delete');
			Route::any('theoryExaminerListPdf', [MappingExaminerController::class, 'theoryExaminerListPdf'])->name('theoryExaminerListPdf');
			Route::any('theoryExaminerExcel', [MappingExaminerController::class, 'theoryExaminerExcel'])->name('theoryExaminerExcel');
		});
			
		Route::group(['prefix' => 'marking_absents'], function () {
			Route::any('/', [MarkingAbsentController::class, 'index'])->name('marking_absents');
			Route::any('add', [MarkingAbsentController::class, 'add'])->name('marking_absents.add');
			Route::any('edit/{id}', [MarkingAbsentController::class, 'edit'])->name('marking_absents.edit');
			Route::any('view/{id}', [MarkingAbsentController::class, 'view'])->name('marking_absents.view');
			Route::any('delete/{id}',[MarkingAbsentController::class, 'delete'])->name('marking_absents_delete');
			Route::any('markingAbsentListPdf', [MarkingAbsentController::class, 'markingAbsentListPdf'])->name('markingAbsentListPdf');
			Route::any('markingAbsentexceldownload', [MarkingAbsentController::class, 'markingAbsentexceldownload'])->name('markingAbsentexceldownload');
			Route::any('centerMarkingAbsentListPdf', [MarkingAbsentController::class, 'centerMarkingAbsentListPdf'])->name('centerMarkingAbsentListPdf');
		 	Route::any('get_appearing_student_listing', [MarkingAbsentController::class, 'get_appearing_student_listing'])->name('get_appearing_student_listing');
			Route::any('getAppearingStudent', [MarkingAbsentController::class, 'getAppearingStudent'])->name('getAppearingStudent');
		});

		Route::group(['prefix' => 'alloting_copies_examiners'], function () {
			Route::any('/', [AllotingCopiesExaminerController::class, 'index'])->name('alloting_copies_examiners');
			Route::any('/add', [AllotingCopiesExaminerController::class, 'add'])->name('alloting_copies_examiners.add');
			Route::any('edit/{id}', [AllotingCopiesExaminerController::class, 'edit'])->name('alloting_copies_edit');
			Route::any('delete/{id}',[AllotingCopiesExaminerController::class, 'delete'])->name('alloting_copies_delete');
			Route::any('allotingCopiesListPdf',[AllotingCopiesExaminerController::class,'allotingCopiesListPdf'])->name('allotingCopiesListPdf');
			Route::any('downloadallotingcopiesexaminerExl',[AllotingCopiesExaminerController::class,'downloadallotingcopiesexaminerExl'])->name('downloadallotingcopiesexaminerExl');
			Route::any('blankScoringSheetPdf/{id}', [AllotingCopiesExaminerController::class, 'blankScoringSheetPdf'])->name('blankScoringSheetPdf');
			Route::any('/unlock_theory_mark_entry/{id}', [AllotingCopiesExaminerController::class, 'unlockMarkEntry'])->name('unlock_theory_mark_entry');
		});
		
		Route::group(['prefix' => 'therory_mark_submissions'], function () {
			Route::any('/', [TheroryMarkSubmissionController::class, 'index'])->name('therory_mark_submissions');
			Route::any('/theory_add_marks/{id}', [TheroryMarkSubmissionController::class, 'add'])->name('theory_add_marks');
			Route::any('/theory_Edit_marks/{id}', [TheroryMarkSubmissionController::class, 'edit'])->name('theory_Edit_marks');
			Route::any('/theorymarkpreview/{id}', [TheroryMarkSubmissionController::class, 'theoryMarkSubmmisiosnPreview'])->name('theorymarkpreview');
			Route::any('/theory_mark_pdf/{id}', [TheroryMarkSubmissionController::class, 'theoryMarkPdf'])->name('theory_mark_pdf');
			
		});
		
		Route::group(['prefix' => 'dgs'],function(){
			Route::any('dgs_listing', [DgsController::class, 'listing'])->name('dgs_listing');
			Route::any('create_profile', [DgsController::class, 'create_profile'])->name('create_profile');
			Route::any('store_profile', [DgsController::class, 'store_profile'])->name('store_profile');
			
		
		});

	});
	
	
	Route::any('back_to_sso', [LandingController::class, 'back_to_sso'])->name('back_to_sso');
	Route::any('help_desk', [LandingController::class, 'help_desk'])->name('help_desk');
	Route::get('/clear-cache', function() { 
		Artisan::call('cache:clear');
		Artisan::call('route:clear');
		Artisan::call('config:cache');
		Artisan::call('view:clear');
		Artisan::call('optimize:clear');
		//Artisan::call('key:generate');
		return Redirect::back()->withErrors(['message' => 'Cache cleared!']);
		//return "<h1><center>Cache cleared !</center></h1>";
	}); 

	Route::get('/routes-list', function () {
		$routes = collect(Route::getRoutes())->map(function ($route) {
			return [
				'uri' => $route->uri(),
				'method' => implode('|', $route->methods()),
				'name' => $route->getName(),
				'action' => $route->getActionName(),
				'middleware' => $route->gatherMiddleware(),
			];
		});

		return view('routes.routes', ['routes' => $routes]);
	});

	Route::get('/config-clear-cache', function() {
		$exitCode = Artisan::call('config:clear');
		$exitCode = Artisan::call('cache:clear');
		$exitCode = Artisan::call('config:cache');
		return 'DONE'; //Return anything
	});
	Route::get('storage/{filename}', function ($filename)
	{
		$path = storage_path('logs/' . $filename);
		 
		if (!File::exists($path)) {
			abort(404);
		} 
		$file = File::get($path);
		$type = File::mimeType($path);

		$response = Response::make($file, 200);
		$response->header("Content-Type", $type);
		return $response;
	});

	Route::get('/check-db-summary', function() {  
		$dbDetails = DB::select('show databases;');
		
		echo "<strong><center><h1>Database Connected: </center></h1></strong>";
		try {
			\DB::connection()->getPDO();
			$item = \DB::connection()->getConfig();echo "<br>";
			
			 
			 
			$fld= "username";echo $fld . " :<strong> " .  $item[$fld];echo "</strong><br>";
			$fld= "port";echo $fld . " : " .  $item[$fld];echo "<br>";
			$fld= "host";echo $fld . " : " .  $item[$fld];echo "<br>";
			$fld= "database";echo $fld . " : " .  $item[$fld];echo "<br>";
			
		} catch (\Exception $e) {
			echo 'None';
		} 
		die;
		return Redirect::back()->withErrors(['message' => 'Cache cleared!']);
		//return "<h1><center>Cache cleared !</center></h1>";
	}); 
    