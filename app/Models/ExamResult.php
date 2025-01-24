<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class ExamResult extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['id', 'student_id', 'enrollment', 'final_result', 'exam_year_id', 'exam_year', 'exam_month', 'result_date', 'revised', 'total_marks', 'percent_marks', 'is_temp_examresult', 'is_deleted', 'supplementary', 'additional_subjects', 'deleted_at', 'created_at', 'updated_at', 'remarks'];
    public $rules = [
        'remarks' => 'required',
        'total_marks' => 'required|numeric|between:1,500',
        'final_result' => 'required',
        'percent_marks' => 'required|numeric|between:0,100',

    ];

    public $message = [
        'remarks.required' => 'Remarks is required',
        'total_marks.required' => 'total_marks is required',
        'final_result.required' => 'final_result is required',
        'percent_marks.required' => 'Percentage is required',
        'total_marks.numeric' => 'Enter number only.',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'id', 'student_id');
    }


}
