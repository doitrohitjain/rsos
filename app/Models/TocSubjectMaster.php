<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class TocSubjectMaster extends Authenticatable
{
    use HasFactory, HasRoles;

    // use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = false;
    public $guard_name = 'web';
    protected $fillable = ['subject_id'];

    public $rules = [
        'subject_id' => 'required',
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
