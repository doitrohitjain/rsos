<?php

namespace App\Models;

use App\Helper\CustomHelper;
use Config;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplementary extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $table = 'supplementaries';
    protected $fillable = ['id', 'student_id', 'is_self_filled', 'last_updated_by_user_id', 'adm_type', 'stream', 'ai_code', 'course', 'exam',
        'marksheet', 'subject_change_fees', 'exam_fees', 'enrollment', 'dob', 'is_eligible',
        'practical_fees', 'forward_fees', 'online_fees', 'late_fees', 'total_fees',
        'submitted', 'challan_tid', 'application_fee_date', 'exam_year', 'exam_month', 'status',
        'is_moved_in_sam', 'is_moved_in_pses', 'subject_new_change', 'is_per_rejected', 'marksheet_doc', 'is_department_verify', 'is_aicenter_verify', 'sec_marksheet_doc', 'user_id', 'origional_subject_id', 'supp_student_change_requests'];

    protected $dates = ['deleted_at'];
    protected $softDelete = true;

    public $rules = [
        'locksumbitted' => 'required',
    ];

    public function SupplementarySubject()
    {
        return $this->hasMany(SupplementarySubject::class, 'supplementary_id', 'id');
    }

    public function SuppChangeRequestOldFees()
    {
        return $this->hasOne(SuppChangeRequertOldStudentFees::class, 'supp_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentallotment()
    {
        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get("global.supp_current_exam_month_id");
        return $this->belongsTo(StudentAllotment::class, 'student_id', 'student_id')->where('exam_year', '=', $exam_year)->where('exam_month', '=', $exam_month)->whereNull('deleted_at');
    }

    public function supplementary_subject_by_suppid()
    {
        return $this->hasMany(SupplementarySubject::class, 'supplementary_id', 'id');
    }

    public $rulesvalidatiton = [
        'deleted_reason' => 'required|numeric',
        'remarks' => 'required',
    ];

    public $rulesuppsvalidatiton = [
        'marksheet_doc' => 'required|mimes:jpg,png,jpeg,gif,pdf,svg|between:10,100',
        'sec_marksheet_doc' => 'required|mimes:jpg,png,jpeg,gif,pdf,svg|between:10,100',
    ];

    public $rulesuppsvalidatitons = [
        'sec_marksheet_doc' => 'required|mimes:jpg,png,jpeg,gif,pdf,svg|between:10,100'
    ];


}
