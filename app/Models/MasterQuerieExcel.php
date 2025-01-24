<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterQuerieExcel extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = ['text', 'status', 'title', 'pdf', 'excel', 'link_text', 'is_link', 'serial_number', 'select_sql_url'];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;

    public $query = [
        'status' => 'required|numeric',
        'pdf' => 'required|numeric',
        'excel' => 'required|numeric',
        'title' => 'required',
        'text' => 'required',
        'link_text' => 'required',
        'is_link' => 'required|numeric',
        'serial_number' => 'required|numeric',
        'name' => 'required|unique:permissions',
        'role' => 'required',
        'select_sql_url' => 'required',
    ];

    public $querys = [
        'serial_number' => 'required|numeric|unique:master_querie_excels',
        'name' => 'required|unique:permissions',
        'role' => 'required',
        'select_sql_url' => 'required',
    ];

    public $querymessage = [
        'name.required' => 'Permissions is Required.',
        'name.unique' => 'Permission has been allready taken.'
    ];

}
