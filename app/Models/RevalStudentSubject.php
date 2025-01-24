<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class RevalStudentSubject extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ["id", "student_id", "reval_id", "subject_id", "is_additional_subject", "status", "deleted_at", "created_at", "updated_at", "exam_year", "exam_month", "final_result", "total_marks", "sessional_marks", "final_theory_marks", "final_practical_marks", "final_result_after_reval", "total_marks_after_reval", "final_theory_marks_after_reval", "reval_rte_status"];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
