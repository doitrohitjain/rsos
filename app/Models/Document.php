<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = ['student_id', 'photograph', 'signature', 'category_a', 'category_b', 'category_c', 'category_d', 'cast_certificate', 'disability', 'identiy_proof', 'pre_qualification', 'disadvantage_group', 'gender_id', 'iti_marksheet'];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;

    public function studentallotment()
    {
        return $this->belongsTo(StudentAllotment::class, 'student_id', 'student_id');
    }

}
