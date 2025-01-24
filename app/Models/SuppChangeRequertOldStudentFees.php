<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SuppChangeRequertOldStudentFees extends Authenticatable
{

    use Loggable;

    protected $fillable = ['student_id', 'supp_id', 'supp_student_change_request_id', 'subject_change_fees', 'exam_fees', 'practical_fees', 'forward_fees', 'online_fees', 'late_fees', 'total_fees', 'old_challan_tid'];
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function Supplementary()
    {
        return $this->belongsTo(Supplementary::class);
    }
}


