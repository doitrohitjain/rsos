<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class MarkingAbsentStudent extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['exam_year', 'exam_session', 'examcenter_detail_id', 'total_students_appearing', 'total_copies_of_subject', 'total_absent', 'total_nr', 'course_id', 'subject_id', 'subject_name', 'updated_at', 'created_at'];

    public $rules = [
        // 'exam_year' => 'required',
        // 'exam_session' => 'required',
        'examcenter_detail_id' => 'required',
        'course_id' => 'required',
        'subject_id' => 'required',
        'total_students_appearing' => 'required',
        'total_copies_of_subject' => 'required',
        'total_absent' => 'required',
        'total_nr' => 'required',

    ];

    public $message = [
        'examcenter_detail_id.required' => 'Exam center is required.',
        'course_id.required' => 'Course is required.',
        'subject_id.required' => 'Subjects is required.',
        'total_students_appearing.required' => 'Total Student Appering  is required.',
        'total_copies_of_subject.required' => 'Total Copies of Student is required.',
        'total_absent.required' => 'Total Absent  is required.',
        'total_nr.required' => 'Total NR  is required.',
        'exam_year.required' => 'Exam Year is Required.',
        'exam_session.required' => 'Exam Sessions is required.',

    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
