<?php
namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $table = "login_logs";

    protected $fillable = array('user_id','session_token','user_ip','device_type','lat','lon','parent_id');
}