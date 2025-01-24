<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DocumentVerification extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $table = 'student_verifications';

    protected $fillable =
        ["student_id", "status", "deleted_at", "created_at", "updated_at", "verifier_verify_user_id", "ao_status", "ao_remark", "ao_verify_user_id", "ao_verify_datetime", "department_verify_user_id", "department_verify_datetime", "verifier_verify_datetime", "verifier_status", "department_status", "verifier_remark", "department_remark", "amount", "is_fee_paid", "submitted", "challan_tid", "fee_date", "role_id", "fee_status", "fee_paid_amount", "is_permanent_rejected_by_dept", "ao_documents_verification", "verifier_documents_verification", "department_documents_verification", "verifier_upper_documents_verification", "ao_upper_documents_verification", "department_upper_documents_verification", "is_ao_agree_with_verifier"];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;

    /* protected static function boot(){
        parent::boot();
        static::saving(function ($model) {
            $your_json_field = 'ao_documents_verification';if(@$model->$your_json_field && $model->$your_json_field != ""){ $model->$your_json_field = Crypt::encryptString($model->$your_json_field);}
            $your_json_field = 'verifier_documents_verification';if(@$model->$your_json_field && $model->$your_json_field != ""){ $model->$your_json_field = Crypt::encryptString($model->$your_json_field);}
            $your_json_field = 'department_documents_verification';if(@$model->$your_json_field && $model->$your_json_field != ""){ $model->$your_json_field = Crypt::encryptString($model->$your_json_field);}
            $your_json_field = 'verifier_upper_documents_verification';if(@$model->$your_json_field && $model->$your_json_field != ""){ $model->$your_json_field = Crypt::encryptString($model->$your_json_field);}
            $your_json_field = 'ao_upper_documents_verification';if(@$model->$your_json_field && $model->$your_json_field != ""){ $model->$your_json_field = Crypt::encryptString($model->$your_json_field);}
            $your_json_field = 'department_upper_documents_verification';if(@$model->$your_json_field && $model->$your_json_field != ""){ $model->$your_json_field = Crypt::encryptString($model->$your_json_field);}
        });
        static::retrieved(function ($model) {
            $your_json_field = 'ao_documents_verification';if(@$model->$your_json_field && $model->$your_json_field != ""){ $model->$your_json_field = Crypt::decryptString($model->$your_json_field);}
            $your_json_field = 'verifier_documents_verification';if(@$model->$your_json_field && $model->$your_json_field != ""){ $model->$your_json_field = Crypt::decryptString($model->$your_json_field);}
            $your_json_field = 'department_documents_verification';if(@$model->$your_json_field && $model->$your_json_field != ""){ $model->$your_json_field = Crypt::decryptString($model->$your_json_field);}
            $your_json_field = 'verifier_upper_documents_verification';if(@$model->$your_json_field && $model->$your_json_field != ""){ $model->$your_json_field = Crypt::decryptString($model->$your_json_field);}
            $your_json_field = 'ao_upper_documents_verification';if(@$model->$your_json_field && $model->$your_json_field != ""){ $model->$your_json_field = Crypt::decryptString($model->$your_json_field);}
            $your_json_field = 'department_upper_documents_verification';if(@$model->$your_json_field && $model->$your_json_field != ""){ $model->$your_json_field = Crypt::decryptString($model->$your_json_field);}
        });
    } */


    public $aicenterDocVerifyRule = [
        // 'photograph' => 'required',
        // 'signature' => 'required',
        // 'category_a' => 'required',
        // 'category_b' => 'required',
        //'remarks' => 'required',
    ];

    public $aicenterDocVerifyRuleMessage = [
        // 'photograph.required' => 'Photograph is required.',
        // 'signature.required' => 'Signature is required.',
        // 'category_a.required' => 'DOB Certificate is required.',
        // 'category_b.required' => 'Address Proof Certificate  is required.',
        //'remarks.required' => 'Rejection Reason is required.',

    ];

    public $deptDocVerifyRule = [
        // 'photograph' => 'required',
        // 'signature' => 'required',
        // 'category_a' => 'required',
        // 'category_b' => 'required',
        //'remarks' => 'required',
    ];

    public $deptDocVerifyRuleMessage = [
        // 'photograph.required' => 'Photograph is required.',
        // 'signature.required' => 'Signature is required.',
        // 'category_a.required' => 'DOB Certificate is required.',
        // 'category_b.required' => 'Address Proof Certificate  is required.',
        //'remarks.required' => 'Rejection Reason is required.',

    ];


    public $uerseditmakerules = [
        // 'photograph' => 'required',
        // 'signature' => 'required',
        // 'category_a' => 'required',
        // 'category_b' => 'required',

    ];


    public $uersmakerulesmessages = [
        // 'photograph.required' => 'Photograph is required.',
        // 'signature.required' => 'Signature is required.',
        // 'category_a.required' => 'DOB Certificate is required.',
        // 'category_b.required' => 'Address Proof Certificate  is required.',

    ];

}
