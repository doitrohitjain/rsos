<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class ResultTopper extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ["id", "course", "district_rank", "gender_id", "total_marks", "percent_marks", "student_id", "enrollment", "final_result", "district_id", "block_id", "type", "rank", "father_name", "mother_name", "dob", "name", "exam_year", "exam_month", "created_at", "updated_at", "deleted_at"];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}


