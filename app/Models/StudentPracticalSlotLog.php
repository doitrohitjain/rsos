<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class StudentPracticalSlotLog extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = false;
    public $guard_name = 'web';
    protected $fillable = ['table_primary_id', 'Table_name', 'table_data', 'user_id', 'user_role', 'user_ip_address', 'created_at', 'updated_at'];
    protected $table = 'student_practical_slot_logs';

    public $rules = [
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

}
