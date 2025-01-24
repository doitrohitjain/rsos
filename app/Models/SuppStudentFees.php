<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuppStudentFees extends Model
{
    use HasFactory;

    // use SoftDeletes;
    use Loggable;

    protected $table = 'supp_student_fees';
    protected $fillable = ['id', 'student_id', 'supplementary_id', 'total', 'deleted_at', 'created_at', 'updated_at'];

    protected $dates = ['deleted_at'];
    // protected $softDelete = true;

}
