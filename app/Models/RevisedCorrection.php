<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class RevisedCorrection extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;


    protected $fillable =
        ['marksheet_migration_request_id',
            'student_id',
            'correction_field',
            'correct_value',
            'incorrect_value',];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;


    public $revisedDocumentRule = [
        "marskeet_type" => "required",
        "document_type" => "required",
        "name" => "required",
        "dob" => "required",
        "father_name" => "required",
        "mother_name" => "required",

    ];
    public $DuplicateDocumentRule = [
        "marskeet_type" => "required",
        "document_type" => "required",

    ];
}