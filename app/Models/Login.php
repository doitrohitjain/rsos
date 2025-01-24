<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    use HasFactory;
    use Loggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $fillable = ['ssoid', 'password'];


}


