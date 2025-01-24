<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;


class PageDetail extends Authenticatable
{

    public $rules = [
        'enrollment' => 'required|numeric',
        'dob' => 'required',
        'captcha' => 'required',
    ];

    public $message = [
        'enrollment.required' => ' The enrollment field is required',
        'dob.required' => 'The dob field is required',
        'captcha.required' => 'The captcha field is required',
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
