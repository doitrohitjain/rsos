<?php

namespace App\Models;

use Cache;
use DB;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class AppModel extends Model
{
    // use HasFactory;
    // use SoftDeletes;
    use Loggable;

    // protected $fillable = ['code','name','name_mangal','version','is_active',];
    // protected $dates = ['deleted_at'];
    // protected $softDelete = true;


    public function master_details($combo_name = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($combo_name)) {
            $condtions = ['status' => 1, 'combo_name' => $combo_name];
        }
        $mainTable = "masters";
        $cacheName = $mainTable . "_" . $combo_name;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {


                $result = DB::table($mainTable)->where($condtions)->get()->pluck('option_id')->toArray();
                $result = implode(",", $result);
                return $result;
            });
        }
        return $result;

    }


}
