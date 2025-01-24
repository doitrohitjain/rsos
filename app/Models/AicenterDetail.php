<?php

namespace App\Models;

use App\Helper\CustomHelper;
use Config;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Session;
use Spatie\Permission\Traits\HasRoles;

class AicenterDetail extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';

    protected $fillable = [
        'ai_code',
        'ssoid',
        'email',
        'designation',
        'name',
        'school_account_ifsc',
        'password',
        'mobile',
        'type',
        'district_id',
        'college_name',
        'school_account_bank_name',
        'district_name',
        'exam_year',
        'exam_month',
        'pincode',
        'school_account_number',
        'principal_name',
        'principal_mobile_number',
        'nodal_officer_name',
        'nodal_officer_mobile_number',
        'block_id',
        'user_id',
        'active',
        'temp_district_id',
        'temp_block_id',
        'temp_district_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function studentAllByAicode()
    {
        $selected_session = CustomHelper::_get_selected_sessions();
        return $this->hasManyThrough(
            Application::class,
            Student::class,
            'ai_code',
            'student_id',
            'ai_code',
            'id'
        )->where('students.exam_year', $selected_session)->where('applications.exam_year', $selected_session);
    }

    public function studentLockSubmitByAicode()
    {
        $selected_session = CustomHelper::_get_selected_sessions();
        return $this->hasManyThrough(
            Application::class,
            Student::class,
            'ai_code',
            'student_id',
            'ai_code',
            'id'
        )->where('locksumbitted', 1)->where('applications.exam_year', $selected_session)->where('students.exam_year', $selected_session);
    }

    public function studentNonLockSubmitByAicode()
    {
        $selected_session = CustomHelper::_get_selected_sessions();
        return $this->hasManyThrough(
            Application::class,
            Student::class,
            'ai_code',
            'student_id',
            'ai_code',
            'id'
        )->where('locksumbitted', 0)->where('applications.exam_year', $selected_session)->where('students.exam_year', $selected_session);
    }


    public function supplementarystudentAllByAicode()
    {
        return $this->hasManyThrough(
            Supplementary::class,
            Student::class,
            'ai_code',
            'student_id',
            'ai_code',
            'id'
        );
    }

    public function supplementarystudentLockSubmitByAicode()
    {
        return $this->hasManyThrough(
            Supplementary::class,
            Student::class,
            'ai_code',
            'student_id',
            'ai_code',
            'id'
        )->where('locksumbitted', 1);
    }

    public function supplementarystudentNonLockSubmitByAicode()
    {
        return $this->hasManyThrough(
            Supplementary::class,
            Student::class,
            'ai_code',
            'student_id',
            'ai_code',
            'id'
        )->where('locksumbitted', 0);
    }


    public function student()
    {
        return $this->hasMany(Student::class);
    }

    public function userrole()
    {
        return $this->hasMany(ModelHasRole::class, 'model_id');
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $rules = [
        'ssoid' => 'required',
        'name' => 'required',
        'email' => 'required|email',
        'mobile' => 'required|min:10|numeric',
        'college_name' => 'required'
    ];

    public $updaterules = [
        'email' => 'required|email',
        'name' => 'required',
        'mobile' => 'required|min:10|numeric',
        'college_name' => 'required'
    ];

    public $deoCreaterules = [
        'ssoid' => 'required',
        'name' => 'required',
        'email' => 'required|email',
        'mobile' => 'required|min:10|numeric',
        'district_id' => 'required'
    ];


    public $deoUpdaterules = [
        'email' => 'required|email',
        'name' => 'required',
        'mobile' => 'required|min:10|numeric',
        'district_id' => 'required',
    ];


    public $myprofilemakerules = [
        'school_account_bank_name' => 'required',
        'school_account_number' => 'required|numeric',
        'school_account_ifsc' => 'required',
        'principal_name' => 'required',
        'principal_mobile_number' => 'required|numeric',
        'email' => 'required|email',
        'nodal_officer_name' => 'required|min:2|max:255',
        'nodal_officer_mobile_number' => 'required|numeric',
        'pincode' => 'required|numeric',
        // 'district_id'=>'required',
        // 'block_id'=>'required',
        'temp_district_id' => 'required',
        'temp_block_id' => 'required',
    ];

    public $uersdeomakerules = [
        'college_name' => 'required|min:2|max:255',
        'ai_code' => 'required|numeric',
        'ssoid' => 'required|unique:users|min:2|max:255',
        'email' => 'required|email|unique:users|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
        'district_id' => 'required',
        'block_id' => 'required',
        'pincode' => 'required|numeric',
        'school_account_number' => 'required|numeric',
        'school_account_ifsc' => 'required',
        'principal_name' => 'required',
        'principal_mobile_number' => 'required|numeric',
        'nodal_officer_name' => 'required|min:2|max:255',
        'nodal_officer_mobile_number' => 'required|numeric',
        'school_account_bank_name' => 'required',
    ];

    public $uersmakerules = [
        'college_name' => 'required|min:2|max:255',
        'ai_code' => 'required|numeric',
        'ssoid' => 'required|unique:aicenter_details|',
        'email' => 'required|email|unique:aicenter_details|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
        'district_id' => 'required',
        'block_id' => 'required',
        'pincode' => 'required|numeric',
        'school_account_number' => 'required|numeric',
        'school_account_ifsc' => 'required',
        'principal_name' => 'required',
        'principal_mobile_number' => 'required|numeric',
        'nodal_officer_name' => 'required|min:2|max:255',
        'nodal_officer_mobile_number' => 'required|numeric',
        'school_account_bank_name' => 'required',
    ];


    public $uerseditmakerules = [
        'college_name' => 'required|min:2|max:255',
        'ai_code' => 'required|numeric',
        'ssoid' => 'required',
        'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
        'district_id' => 'required',
        'block_id' => 'required',
        'pincode' => 'required|numeric',
        'school_account_number' => 'required|numeric',
        'school_account_ifsc' => 'required',
        'principal_name' => 'required',
        'principal_mobile_number' => 'required|numeric',
        'nodal_officer_name' => 'required|min:2|max:255',
        'nodal_officer_mobile_number' => 'required|numeric',
        'school_account_bank_name' => 'required',
    ];


    public $useraicenterrulemessage = [

        'college_name.required' => 'AI Center Name is Required.',
        'ai_code.required' => 'Ai code is Required.',
        'ssoid.required' => 'SSO is Required.',
        'email.required' => 'Email is Required.',
        'district_id.required' => 'District Name is Required.',
        'block_id.required' => 'Block Name is Required.',
        'temp_district_id.required' => 'District Name is Required.',
        'temp_block_id.required' => 'Block Name is Required.',
        'pincode.required' => 'Pincode is Required.',
        'school_account_number.required' => 'AI Center Account Number is Required.',
        'school_account_ifsc.required' => 'AI Center Account IFSC Code is Required.',
        'principal_name.required' => 'Principal Name is Required.',
        'principal_mobile_number.required' => 'Principal Mobile Number is Required.',
        'nodal_officer_name.required' => 'Nodal Officer Name is Required.',
        'nodal_officer_mobile_number.required' => 'Nodal Officer Mobile Number is Required.',
        'school_account_bank_name.required' => 'Bank Name is Required.',
    ];


    public $mapping_examiner = [
        'ssoid' => 'required',
        'name' => 'required',
        'mobile' => 'required|numeric',
        'designation' => 'required'
    ];


    public $mapping_examiner_messages = [
        'ssoid.required' => 'ssoid is Required.',
        'name.required' => 'name is Required.',
        'mobile.required' => 'mobile is Required.',
        'mobile.numeric' => 'Please enter valid mobile number.',
        'designation.required' => 'designation is Required.',
    ];
}
