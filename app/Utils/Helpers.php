<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 9/29/2016
 * Time: 10:46 AM
 */

namespace App\Utils;


class Helpers
{
    public static function test($data,$flag=0)
    {
        echo '<pre>';

        if($flag){
            var_dump($data);
        }else{
            print_r($data);
        }
        die();
    }
}