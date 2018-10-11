<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/9/2016
 * Time: 3:30 PM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = "iptv_categories";
    const LIVE = 'LIVE';
    const CATCHUP = 'CATCHUP';
    const VOD = 'VOD';

    protected $hidden = ['parent_id','created_by','created_at','updated_at'];

    /**
     * We should use this as object's attribute to get collection of subcategories
     * of a category object
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subcategories()
    {
        return $this->hasMany('App\Entities\SubCategory','category_id','id');
    }


}