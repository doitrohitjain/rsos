<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class Toc extends Authenticatable
{
    use HasFactory, HasRoles;

    // use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = false;
    public $guard_name = 'web';
    protected $fillable = ['year_fail', 'year_pass', 'board', 'roll_no', 'deleted_at', 'created_at', 'updated_at'];
    protected $table = 'toc';

    public $rules = [
        // 'student_id' => 'required|numeric',
        //'year_fail' => 'required',
        //'year_pass' => 'required',
        //'board' => 'required',
        //'roll_no' => 'required',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function toc_marks()
    {
        return $this->hasMany(TocMark::class);
    }
}
