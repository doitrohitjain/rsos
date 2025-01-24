<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class DbtStudent extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    protected $table = 'dbt_students';
    public $guard_name = 'web';
    protected $fillable = ["student_id", "exam_year", "exam_month", "entitlementId", "entitlementMemId", "janaadhaarId", "janaadhaarMemId", "transactionId", "dueTransactionId", "aadharNo", "eid", "bankAccNo", "is_saved", "ifsc", "micr", "paymentAmount", "paymentDate", "response", "deleted_at", "created_at", "updated_at"];

    protected $casts = [
        'created_at' => 'datetime',
    ];

}
