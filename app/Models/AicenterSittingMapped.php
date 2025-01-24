<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AicenterSittingMapped extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['aicode', 'stream', 'exam_year'];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;

}
