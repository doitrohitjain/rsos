<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class TheoryExaminer extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['student_id', 'address1', 'address2', 'address3', 'state_id', 'state_name', 'district_id', 'district_name', 'tehsil_id', 'tehsil_name', 'block_id', 'block_name', 'city_name', 'pincode', 'deleted_at', 'created_at', 'updated_at'];

    const allslots = array('1', '2', '3', '6');

    public $rules = [
        'exam_year' => 'required|min:2|max:70',
        'exam_session' => 'required|min:2|max:70',
        'examiner_name' => 'required|min:2|max:70',
        'sso_id' => 'required|min:2|max:70',
        'mobile' => 'required | numeric | min:2|max:70',
        'designation' => 'required|min:2|max:70',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
