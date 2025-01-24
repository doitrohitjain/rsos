<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AllotingCopiesExaminer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['marking_absent_student_id', 'user_id', 'allotment_date', 'theory_lastpage_submitted_date',
        'is_changed', 'changed_by_user_id', 'changed_date', 'marks_entry_completed_date'];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;

    public $rules = [
        'examcenter_detail_id' => 'required',
        'course_id' => 'required',
        'subject_id' => 'required',
        'Totalstudentsappearing' => 'required',
        'totalcopiesofthesubject' => 'required',
        'total_absent' => 'required',
        'Total_nr' => 'required',
        'ssoid' => 'required',
        'name' => 'required',
        'mobile' => 'required',
    ];

    public $message = [
        'allotment_date.required' => " allotment_date is Required",
        'examcenter_detail_id.required' => 'Exam center is required.',
        'course_id.required' => 'Course is required.',
        'subject_id.required' => 'Subjects is required.',
        'Totalstudentsappearing.required' => 'Total Student Appering  is required.',
        'totalcopiesofthesubject.required' => 'Total Copies is required.',
        'total_absent.required' => 'Total Absent  is required.',
        'Total_nr.required' => 'Total NR  is required.',
        'ssoid.required' => 'Sso Id is required.',
        'name.required' => 'Name is required.',
        'mobile.required' => 'Mobile is required.',
    ];


}



