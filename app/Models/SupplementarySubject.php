<?php

namespace App\Models;

use App\Helper\CustomHelper;
use Config;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplementarySubject extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $table = 'supplementary_subjects';
    protected $fillable = ['student_id'];
    protected $dates = ['deleted_at'];

    // protected $softDelete = true;

    public function Supplementary()
    {
        return $this->belongsTo(Supplementary::class, 'id', 'supplementary_id');
    }

    public function studentallotment()
    {
        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get("global.supp_current_exam_month_id");
        return $this->belongsTo(StudentAllotment::class, 'student_id', 'student_id')->where('exam_year', '=', $exam_year)->where('exam_month', '=', $exam_month)->whereNull('deleted_at');
    }


    public function student()
    {
        return $this->belongsTo(Student::class, 'id', 'student_id');
    }

}
