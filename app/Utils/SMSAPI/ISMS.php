<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 9/28/2016
 * Time: 5:42 PM
 */

namespace App\Utils\SMSAPI;


interface ISMS
{
    public function sendSMS($phoneNo,$message);
}