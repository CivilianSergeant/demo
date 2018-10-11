<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12/18/2016
 * Time: 3:31 PM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $table = "fcm_tokens";

    protected $fillable = ['fcm_token','user_id','logout_flag'];
}

