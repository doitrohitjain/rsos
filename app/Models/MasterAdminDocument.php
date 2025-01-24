<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterAdminDocument extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = ['text', 'status', 'title', 'doc_type', 'link_text', 'is_link', 'document', 'serial_number'];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;
}
