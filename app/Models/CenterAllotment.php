<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CenterAllotment extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = ['examcenter_detail_id', '	ai_code', 'course', 'student_strem1_10', 'student_strem2_10', 'student_strem1_12', 'student_strem2_12',
        'student_supp_10', 'student_supp_12', 'total_of_10', 'total_of_12', 'supp_total', 'stream1_total', 'stream2_total', 'student_code_from_10', 'student_code_to_10', 'student_code_from_12', 'student_code_to_12', 'total', 'stream', 'exam_event', 'exam_year', 'status', 'exam_month', 'is_student_strem1_10', 'is_student_strem1_12', 'is_student_strem2_10', 'is_student_strem2_12', 'is_student_supp_10', 'is_student_supp_12'];

    protected $dates = ['deleted_at'];
    protected $softDelete = true;


    public function examcenterdetails()
    {
        return $this->belongsTo(ExamcenterDetail::class, 'id', 'examcenter_detail_id');
    }

}
