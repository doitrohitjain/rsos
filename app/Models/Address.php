<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class Address extends Authenticatable
{
    use HasFactory, HasRoles;
    use SoftDeletes;
    use Loggable;

    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    public $guard_name = 'web';
    protected $fillable = ['is_both_same', 'student_id', 'address1', 'address2', 'address3', 'state_id', 'state_name', 'district_id', 'district_name', 'tehsil_id', 'tehsil_name', 'block_id', 'block_name', 'city_name', 'pincode', 'current_address1', 'current_address2', 'current_address3', 'current_state_id', 'current_state_name', 'current_district_id', 'current_district_name', 'current_tehsil_id', 'current_tehsil_name', 'current_block_id', 'current_block_name', 'current_city_name', 'current_pincode', 'deleted_at', 'created_at', 'updated_at'];

    const allslots = array('1', '2', '3', '6');


    public $forRajasthanAddressValidation = [
        'address1' => 'required|min:2|max:255',
        'state_id' => 'required',
        'district_id' => 'required',
        'tehsil_id' => 'required',
        'block_id' => 'required',
        'city_name' => 'required | min:2 |max:30',
        'pincode' => 'required | numeric | digits_between:6,8'

    ];

    public $outOfRajasthanAddressValidation = [
        'address1' => 'required|min:2|max:255',
        'state_id' => 'required',
        'district_id' => 'required',
        'tehsil_name' => 'required',
        'block_name' => 'required',
        'city_name' => 'required | min:2 |max:30',
        'pincode' => 'required | numeric | digits_between:6,8'
    ];


    public $permanatforRajasthanCurrentAddressValidation = [
        'address1' => 'required|min:2|max:255',
        'state_id' => 'required',
        'district_id' => 'required',
        'tehsil_id' => 'required',
        'block_id' => 'required',
        'city_name' => 'required | min:2 |max:30',
        'pincode' => 'required | numeric | digits_between:6,8'
    ];

    public $permanatoutOfRajasthanCurrentAddressValidation = [
        'address1' => 'required|min:2|max:255',
        'state_id' => 'required',
        'district_id' => 'required',
        'tehsil_name' => 'required',
        'block_name' => 'required',
        'city_name' => 'required | min:2 |max:30',
        'pincode' => 'required | numeric | digits_between:6,8'
    ];


    public $correspondanceforRajasthanCurrentAddressValidation = [
        'current_address1' => 'required|min:2|max:255',
        'current_state_id' => 'required',
        'current_district_id' => 'required',
        'current_tehsil_id' => 'required',
        'current_block_id' => 'required',
        'current_city_name' => 'required | min:2 |max:30',
        'current_pincode' => 'required | numeric | digits_between:6,8'
    ];

    public $correspondanceoutOfRajasthanCurrentAddressValidation = [
        'current_address1' => 'required|min:2|max:255',
        'current_state_id' => 'required',
        'current_district_id' => 'required',
        'current_tehsil_name' => 'required',
        'current_block_name' => 'required',
        'current_city_name' => 'required | min:2 |max:30',
        'current_pincode' => 'required | numeric | digits_between:6,8'
    ];

    public $customMessage = [
        'address1.required' => 'Address 1 is required.',
        'state_id.required' => 'State is required.',
        'tehsil_id.required' => 'Teshil is required.',
        'pincode.required' => 'Pincode is required.',
        'city_name.required' => 'City is required.',
        'district_id.required' => 'District is required.',
        'block_id.required' => 'Block is required.',
        'block_name.required' => 'Block is required.',
        'tehsil_name.required' => 'Teshil is required.',
        'current_address1.required' => 'Correspondence Address 1 is required.',
        'current_state_id.required' => 'Correspondence State is required.',
        'current_tehsil_id.required' => 'Correspondence Teshil is required.',
        'current_pincode.required' => 'Correspondence Pincode is required.',
        'current_city_name.required' => 'Correspondence City is required.',
        'current_district_id.required' => 'Correspondence District is required.',
        'current_block_id.required' => 'Correspondence Block is required.',
        'current_block_name.required' => 'Correspondence Block is required.',
        'current_tehsil_name.required' => 'Correspondence Teshil is required.',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
