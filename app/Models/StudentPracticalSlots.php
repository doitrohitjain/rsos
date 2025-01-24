<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentPracticalSlots extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['examiner_mapping_id', 'date_time_start', 'date_time_end', 'batch_student_count', 'entry_done', 'exam_year', 'exam_month'];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;

    public $rules = [
        'date_time_start' => 'required',
        'date_time_end' => 'required|after:start_date',
        'batch_student_count' => 'required|numeric',
    ];

    public $uersmakerulesmessage = [
        'date_time_start.required' => 'Date Time Start is required.',
        'date_time_end.required' => 'Date Time End is required.',
        'batch_student_count.required' => 'Batch Student Count is required.',

    ];

}
