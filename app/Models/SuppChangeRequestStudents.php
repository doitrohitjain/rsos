<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class SuppChangeRequestStudents extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    public $guard_name = 'web';
    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    protected $fillable = ['student_id', 'exam_year', 'exam_month', 'supp_student_change_requests', 'supp_student_update_application', 'supp_id', 'deparment_approved_date', 'student_update_application_date'];


}
