<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/10/2016
 * Time: 11:54 AM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class SubscriberProfile extends Model
{
    protected $table = "subscriber_profiles";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subscriber_name', 'address1','address2','country_id','division_id','district_id',
        'area_id','sub_area_id','road_id','contact','token','parent_id'
    ];

    protected $hidden = ['lat','lon','is_foc','foc_control_room','foc_others','reference_type','reference_id',
    'remarks','is_hide','parent_id','created_by','token','created_at','updated_at','updated_by'];
}