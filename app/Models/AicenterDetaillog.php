<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Session;
use Spatie\Permission\Traits\HasRoles;

class AicenterDetaillog extends Authenticatable
{
    use HasFactory, HasRoles;

    protected $table = 'aicenter_detail_logs';

    protected $fillable = [
        'ssoid',
        'college_name',
        'ai_code',
        'exam_year',
        'exam_month',
        'district_id',
        'block_id',
        'principal_name',
        'nodal_officer_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

}