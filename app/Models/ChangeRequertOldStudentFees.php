<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ChangeRequertOldStudentFees extends Authenticatable
{

    use Loggable;

    protected $fillable = ['student_id', 'adm_type', 'stream', 'session', 'registration_fees', 'online_services_fees', 'add_sub_fees', 'forward_fees', 'toc_fees', 'practical_fees', 'readm_exam_fees', 'late_fee', 'total', 'old_challan_tid', 'student_change_request_id'];
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function Student()
    {
        return $this->belongsTo(Student::class);
    }
}


