<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class StudentTheoryCopyCheckingMark extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    public $guard_name = 'web';
    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    protected $fillable = ["id", "student_allotment_id", "student_allotment_marks_id", "student_id", "subject_id", "theory_max_marks", "final_theory_marks", "theory_absent", "theory_examiner_id", "is_theory_marks_entered_by_api", "theory_marks_update_count", "created_at", "updated_at", "deleted_at", "exam_year", "exam_month", "extra"];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
