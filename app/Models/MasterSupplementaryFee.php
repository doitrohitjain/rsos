<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterSupplementaryFee extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $table = 'supplementaries';
    protected $fillable = ['id'];

    protected $dates = ['deleted_at'];
    protected $softDelete = true;

}
