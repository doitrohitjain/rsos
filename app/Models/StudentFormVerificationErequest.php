<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class StudentFormVerificationErequest extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    public $guard_name = 'web';
    public $rules = [];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    protected $fillable = ["id", "challan_tid", "transaction_id", "amount", "emitra_id", "student_id", "college_id", "rtype", "status", "response", "created_at", "updated_at", "prn", "service_id", "deleted_at", "ENCDATA", "student_verification_id"];
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
