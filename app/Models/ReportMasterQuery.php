<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportMasterQuery extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = ['permission_id', 'id', 'title', 'permissions', 'remarks', 'sql', 'url', 'tooltip_text', 'status', 'is_pdf', 'is_excel', 'deleted_at', 'created_at', 'updated_at', 'is_show_link', 'report_category_id', 'is_sql', 'link_text', 'serial_number'];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;


    public function rolehaspermission()
    {
        return $this->hasMany(RoleHasPermission::class, 'permission_id', 'permission_id');
    }

    public $query = [
        'is_show_link' => 'required',
        'status' => 'required|numeric',
        'title' => 'required',
        // 'serial_number'=>'required|numeric',
        'link_text' => 'required',
        'sql' => 'required',
        'permissions' => 'required|unique:report_master_queries',
        'role' => 'required',
        'is_sql' => 'required',
        'report_category_id' => 'required',
        'is_show_link' => 'required'
    ];

    public $querys = [
        'is_show_link' => 'required',
        'status' => 'required|numeric',
        'title' => 'required',
        'link_text' => 'required',
        // 'serial_number'=>'required|numeric',
        'permissions' => 'required|unique:report_master_queries',
        'role' => 'required',
        'is_sql' => 'required',
        'report_category_id' => 'required',
        'url' => 'required',

    ];

    public $querynotpermissions = [
        'is_show_link' => 'required',
        'status' => 'required|numeric',
        'title' => 'required',
        // 'serial_number'=>'required|numeric',
        'link_text' => 'required',
        'sql' => 'required',
        //'permissions'=>'required|unique:report_master_queries',
        'role' => 'required',
        'is_sql' => 'required',
        'report_category_id' => 'required',

    ];

    public $querysnotpermissions = [
        'is_show_link' => 'required',
        'status' => 'required|numeric',
        'title' => 'required',
        'link_text' => 'required',
        // 'serial_number'=>'required|numeric',
        //'permissions'=>'required|unique:report_master_queries',
        'role' => 'required',
        'is_sql' => 'required',
        'report_category_id' => 'required',
        'url' => 'required',

    ];
    public $queryss = [
        'is_show_link' => 'required',
        'status' => 'required|numeric',
        'title' => 'required',
        'link_text' => 'required',
        // 'serial_number'=>'required|numeric',
        'permissions' => 'required|unique:report_master_queries',
        'role' => 'required',
        'is_sql' => 'required',
        'report_category_id' => 'required',

    ];

    public $querymessage = [
        'name.required' => 'Permissions is Required.',
        'name.unique' => 'Permission has been allready taken.',
        'is_show_link.required' => 'Is show link to Department is Required.'
    ];
}
