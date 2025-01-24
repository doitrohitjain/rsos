<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class SmsManagement extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $table = 'sms_managements';
    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ["id", "mobile", "sms", "template_id", "extra", "count", "status", "created_at", "updated_at", "deleted_at", "group"];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
 