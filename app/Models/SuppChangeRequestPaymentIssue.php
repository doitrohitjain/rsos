<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class SuppChangeRequestPaymentIssue extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    public $guard_name = 'web';
    public $rules = [];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    protected $fillable = ['student_id', 'deleted_at', 'is_archived', 'supp_student_change_request_id', 'status', 'enrollment', 'created_at', 'updated_at', 'exam_year', 'exam_month', 'supp_id'];
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
