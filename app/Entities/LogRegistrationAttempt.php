<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/13/2016
 * Time: 1:00 PM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class LogRegistrationAttempt extends Model
{
    protected $table="log_registration_attempts";
    const GEOIP = "GEOIP";
    const GEOTERRITORY = "GEOTERRITORY";

    protected $fillable = ['mobile_number','email','lat','lon','device_type','parent_id','ip',
        'country_code','country_name','lookup'];
}