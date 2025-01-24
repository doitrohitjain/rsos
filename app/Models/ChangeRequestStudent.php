<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class ChangeRequestStudent extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['student_id', 'exam_year', 'exam_month', 'ssoid', 'student_change_requests', 'enrollment', 'is_eligible', 'locksubmitted_date', 'locksumbitted', 'student_update_application', 'student_update_application_date', 'deparment_approved_date'];


}
