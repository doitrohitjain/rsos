<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class SuppChangeRequestErequest extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    public $guard_name = 'web';
    public $rules = [];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    protected $fillable = ['student_id', 'payment_user_id', 'deleted_at', 'rtype', 'prn', 'supp_student_change_request_id', 'service_id', 'created_at', 'updated_at', 'supp_id', 'exam_year', 'exam_month'];
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
