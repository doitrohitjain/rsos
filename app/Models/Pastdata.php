<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class Pastdata extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['id', 'ENROLLNO', 'deleted_at', 'created_at', 'updated_at', 'EX_YR'];

    public $rules = [
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];


    public $updaterules = [
        'NAME' => 'required|regex:/(^[a-zA-Z\s]+$)+/',
        'FNAME' => 'required|regex:/(^[a-zA-Z\s]+$)+/',
        'MNAME' => 'required|regex:/(^[a-zA-Z\s]+$)+/',
        'DOB' => 'required',
        'ResultDate' => 'required',
        'EX_YR' => 'required',
    ];


    public $final_result = [

        'RESULT' => 'required',
        'TOTAL_MARK' => 'required|numeric|between:1,500',
        'Percentage' => 'required|numeric|between:0,100',
    ];

    public $exsub1 = [
        'fst1' => 'required|numeric',
        'FTM1' => 'required|numeric|int',
        'FPM1' => 'required|numeric|int',
        'FTOT1' => 'required|numeric',
        'FRES1' => 'required'
    ];


    public $exsub2 = [
        'fst2' => 'required|numeric',
        'FTM2' => 'required|numeric|int',
        'FPM2' => 'required|numeric|int',
        'FTOT2' => 'required|numeric',
        'FRES2' => 'required'
    ];
    public $exsub3 = [
        'fst3' => 'required|numeric',
        'FTM3' => 'required|numeric|int',
        'FPM3' => 'required|numeric|int',
        'FTO3' => 'required|numeric',
        'FRES1' => 'required'
    ];
    public $exsub4 = [
        'fst4' => 'required|numeric',
        'FTM4' => 'required|numeric|int',
        'FPM4' => 'required|numeric|int',
        'FTOT4' => 'required|numeric',
        'FRES4' => 'required'
    ];
    public $exsub5 = [
        'fst5' => 'required|numeric',
        'FTM5' => 'required|numeric|int',
        'FPM5' => 'required|numeric|int',
        'FTOT5' => 'required|numeric',
        'FRES5' => 'required'
    ];
    public $exsub9 = [
        'fst6' => 'required|numeric',
        'FTM6' => 'required|numeric|int',
        'FPM6' => 'required|numeric|int',
        'FTOT6' => 'required|numeric',
        'FRES6' => 'required'
    ];

    public $exsub7 = [
        'fst7' => 'required|numeric',
        'FTM7' => 'required|numeric|int',
        'FPM7' => 'required|numeric|int',
        'FTOT7' => 'required|numeric',
        'FRES7' => 'required'
    ];


    public $message1 = [
        'total_marks.required' => 'total_marks is required',
        'RESULT.required' => 'final_result is required',
        'Percentage.required' => 'Percentage is required',
        'Percentage.numeric' => 'Enter number only.',

    ];
    public $message = [
        'NAME.required' => 'Name is Required.',
        'NAME.regex' => 'Enter Valid Name.',
        'FNAME.required' => 'father Name is Required.',
        'FNAME.regex' => 'Enter Valid Father Name.',
        'MNAME.required' => 'Mother Name is Required.',
        'MNAME.regex' => 'Enter Valid Mother Name.',
        'DOB.required' => 'Date of Birth is required',
        'ResultDate.required' => 'ResultDate is Required.',
        'EX_YR.required' => 'Exam month year is Required.',
    ];


}
