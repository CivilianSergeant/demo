<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/1/2016
 * Time: 11:33 AM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class FavoriteContent extends Model
{
    protected $table = "iptv_favorite_programs";

    protected $fillable = ['content_id','customer_id'];
}