<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserExaminerMap extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = [
        'id',
        'user_deo_id',
        'user_practical_examiner_id',
        "examcenter_detail_id",
        "course",
        "subject_id",
        'update_marks_entry',
        "status",
        "stream",
        "is_lock_submit",
        "lock_submit_remark",
        "unlock_by",
        "document",
        "practical_lastpage_submitted_date",
        "practical_lock_submit_remark",
        "practical_lock_submit_user_id"

    ];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;

    public $rulesUserPracticalExaminer = [
        'user_deo_id' => 'required',
        'examcenter_detail_id' => 'required',
    ];

    public $practicalExaminerValidation = [
        'course' => 'required | numeric ',
        'examcenter_detail' => 'required | numeric ',
        'subject' => 'required | numeric ',
        'ssoid' => 'required | min:2 |max:30',
        'examiner_name' => 'required | min:2 |max:50',
        'email' => 'required | email',
        'mobile' => 'required | numeric | digits_between:6,15'
    ];

}
