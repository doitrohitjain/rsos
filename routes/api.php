<?php
	use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\MasterReportController;
    use App\Http\Controllers\LoginController;
    use App\Http\Controllers\WebapiController;
    use App\Http\Controllers\WebapicopycheckingController;
    use App\Http\Controllers\WebapiupdatenewController;
    use App\Http\Controllers\WebapinewController;
    use App\Http\Controllers\LandingController;
	use App\Http\Controllers\ApplicationController;
	use App\Http\Controllers\DistrictController;
	use App\Http\Controllers\RoleController;
	use App\Http\Controllers\UserController;
	use App\Http\Controllers\StudentController;
	use App\Http\Controllers\CollegeController;
	use App\Http\Controllers\PermissionController;
	use App\Http\Controllers\ReportController;
	use App\Http\Controllers\ResignationsController;
	use App\Http\Controllers\AjaxController;
	use App\Http\Controllers\ExaminationReportController;
	use App\Http\Controllers\AdmissionReportsController;
	use App\Http\Controllers\ExamLateFeeDatesController;
	use App\Http\Controllers\SessionalController;
	use App\Http\Controllers\SchoolController;
	use App\Http\Controllers\ExamcenterDetailController;
	use App\Http\Controllers\ExamcenterAllotmentController;
	use App\Http\Controllers\LogController;
	use App\Http\Controllers\SupplementaryController;
	use App\Http\Controllers\PracticalExaminerController;
	use App\Http\Controllers\DeoController;
	use App\Http\Controllers\TheoryExaminerController;

	
	
    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */
   
	Route::resource('/', \App\Http\Controllers\LandingController::class);
	Route::any('apisdlogin', [LoginController::class, 'sdlogin'])->name('apisdlogin');
	Route::get('all_routes', [MasterReportController::class, 'routes']);
	Route::get('banks_master', [MasterReportController::class, 'banks_master'])->name('banks_master');

	Route::post('api_student_exam_subjects', [WebapiController::class, 'api_student_exam_subjects'])->name('api_student_exam_subjects');
	Route::post('api_set_student_sessional_exam_subject_marks', [WebapiController::class, 'api_set_student_sessional_exam_subject_marks'])->name('api_set_student_sessional_exam_subject_marks');
	Route::post('api_master_subjects', [WebapiController::class, 'api_master_subjects'])->name('api_master_subjects');
	Route::post('api_student_application_form_pdf', [WebapiController::class, 'api_student_application_form_pdf'])->name('api_student_application_form_pdf');
	Route::post('api_student_admit_card_pdf', [WebapiController::class, 'api_student_admit_card_pdf'])->name('api_student_admit_card_pdf');
	Route::post('api_student_application_form_view', [WebapiController::class, 'api_student_application_form_view'])->name('api_student_application_form_view');
	Route::post('api_student_login', [WebapiController::class, 'api_student_login'])->name('api_student_login');
	
	Route::post('update_new_api_student_exam_subjects', [WebapiupdatenewController::class, 'update_new_api_student_exam_subjects'])->name('update_new_api_student_exam_subjects');
	Route::post('update_new_api_set_bulk_student_sessional_exam_subject_marks_by_mobile_admin', [WebapiupdatenewController::class, 'update_new_api_set_bulk_student_sessional_exam_subject_marks_by_mobile_admin'])->name('update_new_api_set_bulk_student_sessional_exam_subject_marks_by_mobile_admin');
	Route::post('update_new_api_send_sms', [WebapiupdatenewController::class, 'update_new_api_send_sms'])->name('update_new_api_send_sms');
	Route::post('update_new_api_set_student_sessional_exam_subject_marks', [WebapiupdatenewController::class, 'update_new_api_set_student_sessional_exam_subject_marks'])->name('update_new_api_set_student_sessional_exam_subject_marks');
	Route::post('update_new_api_master_subjects', [WebapiupdatenewController::class, 'update_new_api_master_subjects'])->name('update_new_api_master_subjects');
	Route::post('update_new_api_student_application_form_pdf', [WebapiupdatenewController::class, 'update_new_api_student_application_form_pdf'])->name('update_new_api_student_application_form_pdf');
	Route::post('update_api_student_admit_card_mobile_view', [WebapiupdatenewController::class, 'update_api_student_admit_card_mobile_view'])->name('update_api_student_admit_card_mobile_view');
	Route::post('update_api_hall_ticket_for_mobile_single_enrollment_view', [WebapiupdatenewController::class, 'update_api_hall_ticket_for_mobile_single_enrollment_view'])->name('update_api_hall_ticket_for_mobile_single_enrollment_view');
	Route::post('update_new_api_student_application_form_view', [WebapiupdatenewController::class, 'update_new_api_student_application_form_view'])->name('update_new_api_student_application_form_view');
	Route::post('update_new_api_is_valid_secure_token', [WebapiupdatenewController::class, 'update_new_api_is_valid_secure_token'])->name('update_new_api_is_valid_secure_token');
	Route::post('update_new_api_student_login', [WebapiupdatenewController::class, 'update_new_api_student_login'])->name('update_new_api_student_login');
	Route::post('update_new_api_student_admit_card_pdf', [WebapiupdatenewController::class, 'update_new_api_student_admit_card_pdf'])->name('update_new_api_student_admit_card_pdf');


	Route::post('new_api_student_exam_subjects', [WebapinewController::class, 'new_api_student_exam_subjects'])->name('new_api_student_exam_subjects');
	Route::post('new_api_set_bulk_student_sessional_exam_subject_marks_by_mobile_admin', [WebapinewController::class, 'new_api_set_bulk_student_sessional_exam_subject_marks_by_mobile_admin'])->name('new_api_set_bulk_student_sessional_exam_subject_marks_by_mobile_admin');
	Route::post('new_api_send_sms', [WebapinewController::class, 'new_api_send_sms'])->name('new_api_send_sms');
	Route::post('new_api_set_student_sessional_exam_subject_marks', [WebapinewController::class, 'new_api_set_student_sessional_exam_subject_marks'])->name('new_api_set_student_sessional_exam_subject_marks');
	Route::post('new_api_master_subjects', [WebapinewController::class, 'new_api_master_subjects'])->name('new_api_master_subjects');
	Route::post('new_api_student_application_form_pdf', [WebapinewController::class, 'new_api_student_application_form_pdf'])->name('new_api_student_application_form_pdf');
	Route::post('api_student_admit_card_mobile_view', [WebapinewController::class, 'api_student_admit_card_mobile_view'])->name('api_student_admit_card_mobile_view');		
	// -------------------------05062024---------------------------
	Route::post('api_hall_ticket_for_mobile_single_enrollment_view', [WebapinewController::class, 'api_hall_ticket_for_mobile_single_enrollment_view'])->name('api_hall_ticket_for_mobile_single_enrollment_view');
	Route::post('new_api_student_application_form_view', [WebapinewController::class, 'new_api_student_application_form_view'])->name('new_api_student_application_form_view');
	Route::post('new_api_student_login', [WebapinewController::class, 'new_api_student_login'])->name('new_api_student_login');
	Route::post('new_api_student_admit_card_pdf', [WebapinewController::class, 'new_api_student_admit_card_pdf'])->name('new_api_student_admit_card_pdf');

	Route::get('test', [MasterReportController::class, 'test'])->name('test');
	
	Route::prefix('copy_checking')->group(function () {
		Route::post('master_student_enrollments', [WebapicopycheckingController::class, 'master_student_enrollments'])->name('master_student_enrollments');
		Route::post('master_subjects', [WebapicopycheckingController::class, 'master_subjects'])->name('master_subjects');
		Route::post('master_theory_examiners', [WebapicopycheckingController::class, 'master_theory_examiners'])->name('master_theory_examiners');
		Route::post('set_student_theory_marks', [WebapicopycheckingController::class, 'set_student_theory_marks'])->name('set_student_theory_marks');
		Route::post('master_students_subjects', [WebapicopycheckingController::class, 'master_students_subjects'])->name('master_students_subjects');
	});