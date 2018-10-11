<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/9/2016
 * Time: 4:23 PM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = "iptv_sub_categories";
    protected $hidden = ['parent_id','created_by','updated_by','created_at','updated_at'];
    /*public function category()
    {
        return $this->belongsTo('\App\Entities\Category','category_id');
    }*/
}