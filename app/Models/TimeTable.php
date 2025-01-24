<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeTable extends Model
{
    use HasFactory;

    //use SoftDeletes;
    use Loggable;

    protected $table = 'timetables';

    protected $fillable = ['course'
        , 'subjects'
        , 'subject_type'
        , 'exam_date'
        , 'exam_time_start'
        , 'exam_time_end'
        , 'status'
        , 'exam_event'
        , 'exam_year'
        , 'stream'

    ];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;

    public $rules = [
        'course' => 'required',
        'subjects' => 'required',
        'exam_time_start' => 'required',
        'exam_time_end' => 'required',
        'exam_date' => 'required',
        'stream' => 'required',
    ];

}
