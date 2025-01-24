<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class Tabing extends Authenticatable
{
    use HasFactory, HasRoles;

    // use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = false;
    public $guard_name = 'web';
    protected $fillable = ['deleted_at', 'created_at', 'updated_at'];
    protected $table = '';

    public $rules = [
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
