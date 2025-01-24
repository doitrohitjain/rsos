<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class StudentLog extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = false;
    public $guard_name = 'web';
    protected $fillable = ['id', 'student_id', 'table_primary_id', 'table_name', 'table_data', 'user_id', 'user_role', 'user_ip_address', 'form_type', 'deleted_at', 'created_at', 'updated_at'];
    protected $table = 'student_logs';

    public $rules = [
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

}
