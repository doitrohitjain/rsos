<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class BankDetail extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['state_id', 'student_id', 'account_holder_name', 'branch_name', 'account_number', 'ifsc_code', 'bank_name', 'linked_mobile', 'is_mobile_verified', 'deleted_at', 'created_at', 'updated_at'];

    public $rules = [
        'account_holder_name' => 'required|regex:/^[\pL\s\-]+$/u|max:255',
        'branch_name' => 'required',
        'account_number' => 'required|numeric',
        'ifsc_code' => 'required',
        'bank_name' => 'required',
        'linked_mobile' => 'required|digits:10|numeric',
    ];

    public $dgsrules = [
        // 'account_holder_name' => 'regex:/^[\pL\s\-]+$/u|max:255',
        // 'branch_name' => 'required',
        // 'account_number' => 'numeric',
        // 'ifsc_code' => 'required',
        // 'bank_name' => 'required',
        // 'linked_mobile' => '|digits:10|numeric',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
