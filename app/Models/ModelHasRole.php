<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class ModelHasRole extends Authenticatable
{
    use Loggable;

    public $timestamps = false;
    protected $fillable = ['role_id', 'model_type', 'model_id',];

    public function roleasign()
    {
        return $this->belongsTo(User::class, 'id');
    }


}
