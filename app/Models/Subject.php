<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = ['real_name', 'name', 'faculty_type_id', 'subject_type', 'sessional_max_marks', 'practical_type',
        'course', 'subject_code', 'theory_max_marks', 'practical_max_marks', 'practical_min_marks',
        'sessional_min_marks', 'theory_min_marks', 'is_science_faculty', 'is_commerce_faculty', 'is_arts_faculty', 'is_allow_faculty', 'is_agricultre_faculty'];

    protected $dates = ['deleted_at'];
    protected $softDelete = true;


    public $rulesapplicationandstudent = [
        'real_name' => 'required',
        'name' => 'required',
        'subject_type' => 'required',
        'sessional_max_marks' => 'required|numeric',
        'sessional_min_marks' => 'required|numeric',
        'practical_type' => 'required|numeric',
        'practical_max_marks' => 'required|numeric',
        'practical_min_marks' => 'required|numeric',
        'course' => 'required|numeric',
        'subject_code' => 'required|numeric',
        'theory_max_marks' => 'required|numeric',
        'theory_min_marks' => 'required|numeric',


    ];

    public $rulesapplicationandstudents = [
        'real_name' => 'required',
        'name' => 'required',
        'subject_type' => 'required',
        'sessional_max_marks' => 'required|numeric',
        'sessional_min_marks' => 'required|numeric',
        'practical_type' => 'required|numeric',
        'course' => 'required|numeric',
        'subject_code' => 'required|numeric',
        'theory_max_marks' => 'required|numeric',
        'theory_min_marks' => 'required|numeric',
    ];
}
