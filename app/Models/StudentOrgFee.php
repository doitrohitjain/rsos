<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class StudentOrgFee extends Authenticatable
{

    use Loggable;

    protected $fillable = ['student_id', 'adm_type', 'stream', 'session', 'org_registration_fees', 'org_online_services_fees', 'org_add_sub_fees', 'org_forward_fees', 'org_toc_fees', 'org_practical_fees', 'org_readm_exam_fees', 'org_late_fee', 'org_total'];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
