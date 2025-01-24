<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamcenterDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = ['stream', 'ecenter10', 'ecenter12', 'capacity', 'ai_code', 'cent_name', 'std_code', 'phone_off', 'phone_res', 'center_supdt',
        'mobile_centsupdt', 'exam_incharge', 'mobile', 'email', 'cent_add1', 'cent_add2', 'district_id',
        'pin', 'police_station', 'ps_distance', 'accountno', 'bank_name', 'bank_ifsc', 'active', 'sec_ansbook', 'srsec_ansbook',
        'practical_ansbook', 'exam_year', 'exam_month', 'school_id', 'user_id'];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;

    public function district()
    {
        //return $this->belongsTo(District::class);
    }

    public $rulesexamcenterdetils = [
        'ecenter10' => 'required',
        'ecenter12' => 'required',
        'capacity' => 'required|numeric',
        'cent_name' => 'required',
        'std_code' => 'required',
        'phone_res' => 'required',
        'center_supdt' => 'required|',
        'mobile_centsupdt' => 'required|digits:10|numeric',
        'exam_incharge' => 'required',
        'mobile' => 'required|digits:10|numeric',
        'email' => 'required|email',
        'cent_add1' => 'required',
        'cent_add2' => 'required',
        'district_id' => 'required|numeric',
        'pin' => 'required|numeric',
        'police_station' => 'required',
        'accountno' => 'required|numeric',
        'bank_name' => 'required',
        'bank_ifsc' => 'required'
    ];


    public function examcenterallotments()
    {
        return $this->hasMany(ExamcenterAllotment::class, 'examcenter_detail_id', 'id');
    }

    public function userdata()
    {
        return $this->hasMany(User::class, 'id', 'user_id');
    }


    public function studentallotment()
    {
        return $this->belongsTo(StudentAllotment::class, 'student_id', 'student_id');
    }

    public function getFullNameAttribute()
    {
        return $this->ecenter10 . '-' . $this->ecenter12 . '-' . $this->cent_name;
    }

    public function getFullNameEcenter10CodeAttribute()
    {
        return $this->ecenter10 . ' - ' . $this->cent_name;
    }

    public function getFullNameEcenter12CodeAttribute()
    {
        return $this->ecenter12 . ' - ' . $this->cent_name;
    }


}
