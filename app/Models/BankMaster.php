<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class BankMaster extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ["id", "name", "deleted_at", "created_at", "updated_at", "sort", "BANK_BRANCH_ID", "BRANCH_ADDRESS", "IFSC_CODE", "MICR", "CREATION_DATE", "MODIFICATION_DATE", "MINLENGTH", "MAXLENGTH", "VERSION", "IS_ACTIVE", "IS_DELETED", "BANK_ID", "DISTRICT_ID", "REMARKS", "STATE_ID", "BRANCH", "BRANCH_MANGAL", "PARENT_BANKBRANCH_ID", "MERGE_DATE", "BANK_NAME", "BANKNAME_MANGAL"];


    protected $casts = [
        'created_at' => 'datetime',
    ];
}
