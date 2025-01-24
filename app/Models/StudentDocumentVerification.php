<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDocumentVerification extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $table = 'student_document_verifications';

    protected $fillable = ['student_id','iti_marksheet', 'iti_pre_qualification','photograph', 'signature', 'category_a', 'category_b', 'category_c', 'category_d', 'cast_certificate', 'disability', 'disadvantage_group', 'pre_qualification', 'identiy_proof', 'minority', 'all_document', 'remarks', 'verifer_user_id', 'deleted_at', 'created_at', 'updated_at', 'is_eligible_for_verify', 'student_verification_id', 'toc_subjects', 'admission_subjects', 'exam_subjects'];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;

}
