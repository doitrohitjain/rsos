<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;


class Logs extends Authenticatable
{
    protected $table = 'logs';
    protected $fillable = ['user_id', 'log_date', 'table_name', 'log_type', 'data'];


}
