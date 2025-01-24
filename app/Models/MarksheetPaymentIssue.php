<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class MarksheetPaymentIssue extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['marksheet_migration_requests_id', 'student_id', 'deleted_at', 'is_archived', 'status', 'enrollment', 'created_at', 'updated_at'];

    public $rules = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
