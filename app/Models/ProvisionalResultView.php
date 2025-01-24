<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProvisionalResultView extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    protected $fillable = ["student_id","enrollment","course","student_name","father_name","mother_name","dob","date","gender_id","mobile","subject_name","subject_code","practical_type","is_additional","final_theory_marks","final_practical_marks","sessional_marks_reil_result","subject_total_marks","subject_result","final_result","total_marks","exam_year","exam_month","percent_marks","additional_subjects","supplementary","result_date","revised","remarks","created_at","updated_at","deleted_at"];

}
