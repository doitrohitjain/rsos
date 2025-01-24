<?php

namespace App\Models;

use Config;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Session;
use Spatie\Permission\Traits\HasRoles;


class Student extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ["rejected_by_user_id", "is_approved_by_aicode", "last_updated_by_user_id", "is_self_filled", "deleted_by_user_id", "deleted_date_by_user", "deleted_reason", "is_eligible", "is_refund", "refund_datetime", "remarks", "active_remarks", "ai_code", "enrollment", "exam", "student_code", "ssoid", "password", "adm_type", "college_id", "user_role", "name", "first_name", "middle_name", "last_name", "name_hi", "father_name", "father_name_hi", "mother_name", "mother_name_hi", "dob", "gender_id", "mobile", "submitted", "challan_tid", "district_id", "status", "course", "stream", "email", "exam_year", "exam_month", "application_fee_date", "admission_fee_date", "created", "modified", "is_deleted", "deleted_at", "created_at", "updated_at", "is_jail_inmates", "user_id", "are_you_from_rajasthan", "extra_comb", "is_otp_verified", "otp", "is_verified", "verifer_user_id", "student_status_at_different_level", "is_verifier_verify", "is_department_verify", "verifier_verify_user_id", "department_verify_user_id", "verifier_verify_datetime", "department_verify_datetime", "book_learning_type_id", "department_status", "verifier_status", "ao_status", "student_change_requests", "update_change_requests_challan_tid", "update_change_requests_submitted", "is_doc_rejected", "stage", "request_to_dept_remarks", "ao_verify_user_id", "ao_verify_datetime", "is_ao_verify", "count_change_enrollment", "last_enrollment_before_change_req", "is_change_enrollment", "is_dgs", "username", "original_password"];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $rules = [
        'adm_type' => 'required|numeric',
        'course' => 'required|numeric',
        'stream' => 'required|numeric',
        'jan_aadhar_number' => 'required',
    ];

    public $rulesForRajasthan = [
        'adm_type' => 'required|numeric',
        'course' => 'required|numeric',
        'stream' => 'required|numeric',
        'jan_aadhar_number' => 'required',
    ];

    public $rulesNotForRajasthan = [
        'adm_type' => 'required|numeric',
        'course' => 'required|numeric',
        'stream' => 'required|numeric'
    ];


    public function application()
    {
        return $this->hasOne(Application::class);
    }

    public function changerequestoldfees()
    {
        return $this->hasOne(ChangeRequertOldStudentFees::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function admission_subject()
    {
        return $this->hasMany(AdmissionSubject::class);
    }

    public function document()
    {
        return $this->hasOne(Document::class);
    }

    public function exam_subject()
    {
        return $this->hasMany(ExamSubject::class);
    }

    public function reval_students()
    {
        return $this->hasOne(RevalStudent::class);
    }

    public function marksheet_migration_requests()
    {
        return $this->hasOne(MarksheetMigrationRequest::class);
    }

    public function reval_student_subjects()
    {
        return $this->hasMany(RevalStudentSubject::class);
    }

    public function revised_correction()
    {
        return $this->hasMany(RevisedCorrection::class);
    }


    public function prepare_exam_subject()
    {
        return $this->hasMany(SessionalExamSubject::class);
    }

    public function supplementary_subject()
    {
        return $this->hasMany(SupplementarySubject::class, 'student_id', 'id');
    }

    public function supplementary_subject_by_suppid()
    {
        return $this->hasMany(SupplementarySubject::class, 'supplementary_id', 'id');
    }

    public function exam_result()
    {
        return $this->hasMany(ExamResult::class, 'student_id', 'id');
    }


    public function supplementary()
    {
        return $this->hasOne(Supplementary::class);
    }

    public function toc_subject()
    {
        return $this->hasMany(TocMark::class);
    }

    public function studentfees()
    {
        return $this->hasOne(StudentFee::class);
    }

    public function studentfee()
    {
        return $this->hasOne(StudentFee::class);
    }

    public function toc()
    {
        return $this->hasOne(Toc::class);
    }

    public function bankdetils()
    {
        return $this->hasOne(BankDetail::class);
    }

    public function suppstudentfee()
    {
        return $this->hasOne(SuppStudentFee::class);
    }

    public function tocdetils()
    {
        return $this->hasOne(Toc::class);
    }

    public function studentallotment()
    {
        return $this->belongsTo(StudentAllotment::class, 'student_id', 'student_id');
    }


    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $AppModelObject = app('App\Models\AppModel');
        $combo_name = 'gender';
        $gender_id = $AppModelObject->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $AppModelObject->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $AppModelObject->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $AppModelObject->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $AppModelObject->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $AppModelObject->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $AppModelObject->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $AppModelObject->master_details($combo_name);
        $combo_name = 'employment';
        $employment = $AppModelObject->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $AppModelObject->master_details($combo_name);
        $combo_name = 'year';
        $year = $AppModelObject->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $AppModelObject->master_details($combo_name);
        $combo_name = 'course';
        $course = $AppModelObject->master_details($combo_name);
        // dd($attributes);
    }

    public $rulesfilter = [
        'stream' => 'required',
        'course' => 'required',
        'aicentermaterialyear' => 'required'
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //Base validation
    public $rulesapplicationandstudentdisability = [
        'first_name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
        'father_name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
        'mother_name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
        'adm_type' => 'required|numeric',
        'course' => 'required|numeric',
        'gender_id' => 'required|numeric',
        'nationality' => 'required|numeric',
        'religion_id' => 'required|numeric',
        'category_a' => 'required|numeric',
        'aadhar_number' => 'required|numeric|digits:12',
        // 'mobile' => 'required|digits:10|numeric',
        'disability' => 'required|numeric',
        'disadvantage_group' => 'required|numeric',
        'medium' => 'required|numeric',
        'rural_urban' => 'required|numeric',
        'employment' => 'required|numeric',
        'dob' => 'required',
        'stream' => 'required',
        'pre_qualification' => 'required|numeric',
        'ssoid' => 'required',
        'disability_percentage' => 'required|numeric',
        'ai_code' => 'required',
        // 'email' => 'required|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
        // 'email' => 'required|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
    ];

    public $rulesapplicationandstudent = [
        'first_name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
        'father_name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
        'mother_name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
        'adm_type' => 'required|numeric',
        'course' => 'required|numeric',
        'gender_id' => 'required|numeric',
        'nationality' => 'required|numeric',
        'religion_id' => 'required|numeric',
        'category_a' => 'required|numeric',
        'aadhar_number' => 'required|numeric|digits:12',
        // 'mobile' => 'required|digits:10|numeric',
        'disability' => 'required|numeric',
        'disadvantage_group' => 'required|numeric',
        'medium' => 'required|numeric',
        'rural_urban' => 'required|numeric',
        'employment' => 'required|numeric',
        'dob' => 'required',
        'stream' => 'required',
        'pre_qualification' => 'required|numeric',
        'ssoid' => 'required',
        'ai_code' => 'required',
        // 'email' => 'required|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
        // 'email' => 'required|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
    ];

    public $studentself = [
        'district_id' => 'required',
        'block_id' => 'required',
        'ai_code' => 'required'
    ];

    public $studentselfmessage = [
        'district_id.required' => 'District is Required.',
        'block_id.required' => 'Block is Required.',
        'ai_code.required' => 'AI Centre is required.'
    ];

    public $rulesapplicationandstudentEmail = [
        'email' => 'email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix'
    ];


    public $message = [
        'gender_id.required' => ' The gender field is required',
        'category_a.required' => 'The category field is required',
        'religion_id.required' => 'The religion field is required',
    ];

    //12 and In Rajasthan
    public $rulesapplicationandstudent12InRajasthan = [
        'year_pass' => 'required|numeric',
        'board' => 'required|numeric',
        'jan_aadhar_number' => 'required|numeric',
        // 'pre_qualification' =>'required|numeric',
    ];

    public $rulesapplicationandstudent12InRajasthandisability = [
        'year_pass' => 'required|numeric',
        'board' => 'required|numeric',
        'jan_aadhar_number' => 'required|numeric',
        'disability_percentage' => 'required|numeric',
        // 'pre_qualification' =>'required|numeric',
    ];
    //12 and Out Rajasthan
    public $rulesapplicationandstudent12OutOfRajasthan = [
        'year_pass' => 'required|numeric',
        'board' => 'required|numeric',
        // 'pre_qualification' =>'required|numeric',
    ];
    public $rulesapplicationandstudent12OutOfRajasthandisability = [
        'year_pass' => 'required|numeric',
        'board' => 'required|numeric',
        'disability_percentage' => 'required|numeric',
        // 'pre_qualification' =>'required|numeric',
    ];
    //10 and In Rajasthan
    public $rulesapplicationandstudent10InRajasthan = [
        'jan_aadhar_number' => 'required|numeric',
        'pre_qualification' => 'required|numeric',
    ];

    public $rulesapplicationandstudent10InRajasthandisability = [
        'jan_aadhar_number' => 'required|numeric',
        'pre_qualification' => 'required|numeric',
        'disability_percentage' => 'required|numeric',
    ];

    public $rulesapplicationandstudent10outRajasthandisability = [
        'disability_percentage' => 'required|numeric',
    ];


    public $forPaymentFindStudentBaseRule = [
        'mobile' => 'required|numeric',
        'dob' => 'required'
    ];

    public $forPaymentFindAadharStudentRule = [
        'aadhar_number' => 'required|numeric',
    ];

    public $forPaymentFindJanAadharStudentRule = [
        'jan_aadhar_number' => 'required|numeric',
    ];


    public $rulesRegistration = [
        'are_you_from_rajasthan' => 'required|numeric',
        'stream' => 'required|numeric',
        'adm_type' => 'required|numeric',
        'course' => 'required|numeric',
    ];

    public $registrationmessage = [
        'adm_type.required' => 'Admission Type is Required.',
    ];

    public $rulesRegistrationareyoufromrajasthan = [
        'jan_aadhar_number' => 'required',
        'member_number' => 'required|numeric',//"Please select student from your jan aadhar member list."
    ];

    public $rulesvalidatiton = [
        'deleted_reason' => 'required|numeric',
        'remarks' => 'required',
    ];

    public $rulessessionalmarks = [
        'enrollment' => 'required|numeric',

    ];


    public $updatedetailsprintrules = [
        'name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
        'father_name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
        'mother_name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
        'mobile' => 'required|digits:10|numeric',
        'dob' => 'required',
        'medium' => 'required',
        'photograph' => 'required',
        'signature' => 'required',

    ];
    public $updatedetailsprintmessage = [
        'name.required' => 'Student Name is Required.',
        'name.regex' => 'Enter Valid Student Name.',
        'father_name.required' => 'Father Name is Required.',
        'father_name.regex' => 'Enter Valid Father Name.',
        'mother_name.required' => 'Mother Name is Required.',
        'medium.required' => 'Medium is Required.',
        'mother_name.regex' => 'Enter Valid Mother Name.',
    ];


}
