<?php

namespace App\Models;

use App\Helper\CustomHelper;
use Config;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAllotment extends Model
{
    use HasFactory;

    //use SoftDeletes;
    use Loggable;

    protected $fillable = ['student_id', 'enrollment', 'examcenter_detail_id', 'center_allotment_id', 'ai_code', 'course', 'stream', 'supplementary', 'exam_year', 'exam_month', 'exam_event', 'fixcode2', 'deleted', 'temp_flg'];
    protected $dates = ['deleted_at'];
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
        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get("global.supp_current_exam_month_id");
        return $this->hasMany(SupplementarySubject::class, 'student_id', 'student_id')->where('exam_year', '=', $exam_year)->where('exam_month', '=', $exam_month)->whereNull('deleted_at');
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
