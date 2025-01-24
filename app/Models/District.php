<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;


    protected $fillable = ['state_id', 'division_id', 'code', 'name', 'name_mangal', 'version', 'is_active',];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;

    public function state()
    {
        return $this->belongsTo(State::class);
    }

}
