<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/7/2016
 * Time: 4:59 PM
 */

namespace App\Utils;


class RequestParser
{
    public static function parse($request)
    {
        //print_r($request);exit;
        $data = AES_Engine::getDecrypt($request,"1234567891234567");

        return json_decode($data);
    }
}