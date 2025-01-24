<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamcenterAllotment extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = ['id', 'examcenter_detail_id', 'ai_code', 'course', 'student_strem1_10',
        'student_strem1_12', 'student_strem2_10', 'student_strem2_12', 'student_supp_10', 'student_supp_12',
        'total_of_10', 'total_of_12', 'supp_total', 'stream1_total', 'stream2_total', 'total', 'stream', 'exam_year', 'exam_month', 'student_code_from_10', 'student_code_to_10', 'student_code_from_12', 'student_code_to_12'];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    protected $table = 'center_allotments';

    public $rulesexamcenterdetils = [
        'stream' => 'required',
    ];

    // public function examcenter_details()
    // {
    //    return $this->belongsTo(ExamcenterDetail::class, 'id');
    // }

}
