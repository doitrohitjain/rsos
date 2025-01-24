<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class RoleHasPermission extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = ['role_id', 'permission_id'];
    protected $softDelete = true;
}
