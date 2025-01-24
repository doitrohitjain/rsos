<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class ChangeRequestErequest extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['student_id', 'payment_user_id', 'deleted_at', 'rtype', 'prn', 'student_change_request_id', 'service_id', 'created_at', 'updated_at',];

    public $rules = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
