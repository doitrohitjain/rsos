<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class  ExamSubject extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['student_id', 'enrollment', 'sessional_marks', 'subject_id', 'adm_type',
        'is_additional', 'exam_month', 'exam_year', 'course', 'stream', 'sessional_marks',
        'sessional_marks_reil_result', 'final_practical_marks', 'final_theory_marks', 'total_marks',
        'final_result', 'is_sessional_mark_entered', 'remarks'];

    // public $rules = [
    // 	'subject_id' => 'required',
    // ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];


    public $sessionalmarksrules = [
        'sessional_marks' => 'required|numeric'
    ];

    public $addsubjectarules = [
        'subject_id' => 'required',
        'exam_year' => 'required',
        'exam_month' => 'required'

    ];

    public $addsubjectmessage = [
        'exam_year.required' => 'Exam Year is required',
        'exam_month.required' => 'Exam month is required',
        'subject_id.required' => 'Subject is required',
    ];


    public $rules = [
        'remarks' => 'required',
        'sessional_marks' => 'required|numeric',
        'sessional_marks_reil_result' => 'required|numeric',
        'final_practical_marks' => 'required|numeric|int',
        'final_theory_marks' => 'required|numeric|int',
        'total_marks' => 'required|numeric',
        'final_result' => 'required'

    ];

    public $message = [
        'remarks.required' => 'Remarks Marks is required',
        'sessional_marks.required' => 'Sessional Marks is required',
        'sessional_marks_reil_result.required' => 'sessional_marks_reil_result is required.',
        'final_practical_marks.required' => 'final_practical_marks is required.',
        'final_theory_marks.required' => 'final_theory_marks is required.',
        'total_marks.required' => 'total_marks is required.',
        'final_result.required' => 'final_result is required.',
        'sessional_marks.numeric' => 'Please enter number only',
        'sessional_marks_reil_result.numeric' => 'Please enter number only.',
        'final_practical_marks.numeric' => 'Please enter number only.',
        'final_theory_marks.numeric' => 'Please enter number only.',
        'total_marks.numeric' => 'Please enter number only.',


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
