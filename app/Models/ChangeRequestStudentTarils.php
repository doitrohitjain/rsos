<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class ChangeRequestStudentTarils extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    public $guard_name = 'web';
    public $rules = [];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    protected $fillable = ['student_id', 'exam_year', 'exam_month', 'student_change_request_id', 'challan_tid', 'prn', 'amount', 'change_request_status'];
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
