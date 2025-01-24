<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;


class StudentUpdate extends Authenticatable
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['student_id', 'is_udpate', 'deleted_at', 'created_at', 'updated_at'];

    public function student()
    {
        return $this->hasMany(Student::class, 'id', 'student_id');
    }

}
