<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class ExamLateFeeDate extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['stream', 'gender_id', 'latefee_extra_days', 'ordering', 'is_supplementary', 'from_date', 'to_date', 'late_fee', 'deleted_at', 'created_at', 'updated_at'];

    public $rules = [
        'stream' => 'required|numeric',
        'gender_id' => 'required|numeric',
        //'latefee_extra_days'=>'required|numeric',
        //'ordering'=>'required|numeric',
        'is_supplementary' => 'required|numeric',
        'from_date' => 'required|date',
        'to_date' => 'required|date',
        'late_fee' => 'required | numeric'
    ];

}
