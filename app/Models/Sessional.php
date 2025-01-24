<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class Sessional extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['sessional_marks', 'student_id'];

    public $rules = [
        'sessional_marks' => 'required|numeric'
    ];

    public function application()
    {
        return $this->hasOne(Application::class);
    }

    public function admission_subject()
    {
        return $this->hasMany(AdmissionSubject::class);
    }

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $AppModelObject = app('App\Models\AppModel');
        $combo_name = 'gender';
        $gender_id = $AppModelObject->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $AppModelObject->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $AppModelObject->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $AppModelObject->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $AppModelObject->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $AppModelObject->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $AppModelObject->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $AppModelObject->master_details($combo_name);
        $combo_name = 'employment';
        $employment = $AppModelObject->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $AppModelObject->master_details($combo_name);
        $combo_name = 'year';
        $year = $AppModelObject->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $AppModelObject->master_details($combo_name);
        $combo_name = 'course';
        $course = $AppModelObject->master_details($combo_name);
    }

}
