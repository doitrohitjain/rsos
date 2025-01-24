<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    //
    protected $fillable = [
        "id", "start_date", "end_date", "generated_by", "details", "created_at", "updated_at",
    ];

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

}
