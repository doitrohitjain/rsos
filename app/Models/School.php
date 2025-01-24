<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['NICCode'
        , 'UDISE'
        , 'BoardAffiliationCode'
        , 'District'
        , 'Block'
        , 'BLKCD'
        , 'Panchayat'
        , 'PANCD'
        , 'Village'
        , 'VILCD'
        , 'AssembalyName'
        , 'Loksabha'
        , 'School'
        , 'PrincipalName'
        , 'MobileNo'
        , 'PrincipalOrHeadmasterEmail'
        , 'SchoolEmailID'
        , 'Is_ElectricityConnection'
        , 'Is_InternetConnection'
        , 'Category'
        , 'Urban_Rural'
        , 'IFMS_ID'
        , 'IS_PEEO'
        , 'TSPArea'
        , 'ICT_Phase'
        , 'School_Type'
        , 'School_Category'
        , 'Vocational'
        , 'AdarshPhase'
        , 'Is_Uthakrasth'
        , 'ICT_Phase2'
        , 'Is_Uthakrasth2'
        , 'School_Management'
        , 'PEEO_NIC_code'
        , 'PEEO_Code'
        , 'SchoolEstablishmentYear'
        , 'PStoUPSYear'
        , 'UPStoSecYear1'
        , 'SecToSrSecYear'
        , 'id'
        , 'deleted'
        , 'deleted_date'
        , 'examcenter_created'
    ];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;

    public function district()
    {
        return $this->belongsTo(District::class);
    }

}
