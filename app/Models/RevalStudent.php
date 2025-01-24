<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class RevalStudent extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ["id", "reval_type", "remarks", "is_offline_payment_mode", "deleted_by_user_id", "deleted_date_by_user", "deleted_reason", "is_refund", "refund_datetime", "is_eligible", "student_id", "ai_code", "exam_year", "exam_month", "stream", "course", "marksheet_doc", "subject_change_fees", "exam_fees", "practical_fees", "forward_fees", "online_fees", "late_fees", "total_fees", "submitted", "challan_tid", "application_fee_date", "created_at", "updated_at", "is_deleted", "subject_new_change", "deleted_at", "locksumbitted", "status", "enrollment", "dob", "locksubmitted_date", "fee_status", "user_id", "fee_paid_amount", "last_updated_by_user_id", "is_self_filled"];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $ruleslocksubmit = [
        'locksumbitted' => 'required',
    ];

    public $rulesapplicationandstudent = [
        'locksumbitted.required' => 'Please check the checkbox.'
    ];

}
