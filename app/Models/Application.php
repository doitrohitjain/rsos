<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class Application extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ["selected_faculty", "is_multiple_faculty", "faculty_type_id", "student_name", "student_id", "enrollment", "jan_aadhar_number", "category_a", "disability", "disadvantage_group", "domicile", "medium", "is_minority", "applicationfor", "fee_status", "locksumbitted", "submitted", "midium", "rural_urban", "employment", "pre_qualification", "board", "board_name", "toc", "is_toc_marked", "email", "age", "autosubmit", "landline_std", "landline", "transaction_id", "nationality", "latefee", "year_pass", "aadhar_number", "bhamashah_number", "class_type", "religion_id", "rejected", "change_latefees", "exam_year", "deleted_at", "created_at", "updated_at", "jan_id", "locksubmitted_date", "status", "change_request_status", "change_request_fee_status", "change_request_fee_paid_amount", "fee_paid_amount", "is_ready_for_verifying", "disability_percentage"];

    public $rules = [
        'locksumbitted' => 'required|numeric',

    ];

    public $rulesapplicationandstudent = [
        'locksumbitted' => 'required',
        'Declaration' => 'required',

    ];


}
