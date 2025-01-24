<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAllotmentHardDelete extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = ['student_id', 'enrollment', 'examcenter_detail_id', 'center_allotment_id', 'ai_code', 'course', 'stream', 'supplementary', 'exam_year', 'exam_month', 'exam_event', 'fixcode2', 'deleted', 'temp_flg'];
    protected $dates = ['deleted_at'];
    protected $table = 'student_allotments';
    protected $softDelete = true;


    public function examsubject()
    {
        return $this->hasMany(ExamSubject::class, 'student_id', 'student_id');
    }


    public function Supplementary()
    {
        return $this->hasMany(Supplementary::class, 'student_id', 'student_id');
    }


    public function Supplementarysubjects()
    {
        return $this->hasMany(SupplementarySubject::class, 'student_id', 'student_id');
    }


    public function document()
    {
        return $this->hasMany(Document::class, 'student_id', 'student_id');
    }


    public function student()
    {
        return $this->hasMany(Student::class, 'id', 'student_id');
    }

    public function examcenter()
    {
        return $this->hasMany(ExamcenterDetail::class, 'id', 'examcenter_detail_id');
    }

}
