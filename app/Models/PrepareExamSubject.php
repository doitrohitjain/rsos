<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class  PrepareExamSubject extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['id', 'enrollment', 'course', 'subject_id', 'is_grace_marks_given', 'grace_marks', 'subject_code', 'theory_marks', 'old_theory_marks', 'practical_marks', 'sessional_marks', 'sessional_marks_reil_result', 'total_marks', 'old_total_marks', 'final_result', 'old_final_result', 'exam_year', 'exam_month', 'subject_type', 'toc', 'is_supplementary', 'is_examsub_migrated', 'deleted_at', 'flg', 'is_theory_mark_updated', 'practical_flag', 'is_skip_fourhundred', 'skip_fiftheen', 'is_subject_change_data', 'new_subject_taken_changed', 'is_total_mark_updated', 'is_final_res_updated', 'is_th_sess_updated', 'student_id', 'is_eligible', 'remarks', 'created_at', 'updated_at', 'status', 'ai_code', 'subject_name'];

    // public $rules = [
    // 	'subject_id' => 'required',
    // ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $sessionalmarksrules = [
        'sessional_marks' => 'required|numeric'
    ];

    public function tocSubejctStudent()
    {
        return $this->hasOneThrough(
            ExamSubject::class,
            Student::class,
            'id',
            'student_id',
        );
    }

    public function Subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function studentallotment()
    {
        return $this->belongsTo(StudentAllotment::class, 'student_id', 'student_id');
    }
}
