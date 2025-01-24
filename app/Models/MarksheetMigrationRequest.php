<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class MarksheetMigrationRequest extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable =
        ['student_id',
            'enrollment',
            'marksheet_type',
            'document_type',
            'support_document',
            'locksumbitted',
            'total_fees',
            'fee_paid_amount',
            'challan_tid',
            'submitted',
            'fee_status',
            'application_fee_date',
            'locksubmitted_date'
        ];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;


    public $revisedDocumentRule = [
        "marksheet_type" => "required",
        "document_type" => "required",
        "support_document" => "required",

        "name" => "required|regex:/^[\pL\s\-]+$/u|max:100",
        "dob" => "required",
        "father_name" => "required|regex:/^[\pL\s\-]+$/u|max:100",
        "mother_name" => "required|regex:/^[\pL\s\-]+$/u|max:100",

    ];
    public $DuplicateDocumentRule = [
        "marksheet_type" => "required",
        "document_type" => "required",
        "support_document" => "required",

    ];

    public $ruleslocksubmit = [
        'locksumbitted' => 'required',
    ];

    public $rulesapplicationandstudent = [
        'locksumbitted.required' => 'Please check the checkbox.'
    ];
}