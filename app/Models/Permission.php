<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Permission extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = ['id', 'name', 'guard_name'];
    protected $softDelete = true;
}
