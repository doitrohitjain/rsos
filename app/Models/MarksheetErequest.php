<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class MarksheetErequest extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ["id", "challan_tid", "transaction_id", "amount", "emitra_id", "student_id", "college_id", "rtype", "status", "respoe", "created_at", "updated_at", "prn", "service_id", "deleted_at", "ENCDATA", "marksheet_migration_requests_id"];

    public $rules = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
