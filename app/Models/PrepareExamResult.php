<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class PrepareExamResult extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['id', 'student_id', 'enrollment', 'final_result', 'result_date', 'revised', 'total_marks', 'percent_marks', 'additional_subjects', 'reval_result_changed', 'is_supplementary', 'is_examresult_migrated', 'is_temp_examresult', 'remarks', 'status', 'ai_code', 'exam_year', 'exam_month', 'created_at', 'updated_at', 'is_eligible', 'course', 'deleted_at'];

    public $rules = [
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
