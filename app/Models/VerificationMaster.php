<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerificationMaster extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    protected $fillable = ['adm_type', 'course', 'main_document_id', 'field_id', 'field_name', 'status', 'form_filled_tbl', 'form_filled_ref'];

}
