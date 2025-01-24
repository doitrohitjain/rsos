<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPracticalExaminer extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = [
        'id',
        'user_deo_id',
        'user_id',
        'exam_month',
        'exam_year',
        'status'
    ];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;

    public $rulesUserPracticalExaminer = [
        'user_deo_id' => 'required',
        'user_id' => 'required',
    ];

}
