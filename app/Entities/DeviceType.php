<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/1/2016
 * Time: 10:59 AM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class DeviceType extends Model
{
    protected $table = "device_types";

    const ANDROID="ANDROID";
    const IOS="IOS";
    const WEB="WEB";
    const STB="STB";

    /**
     * This Method should use to check to determine device Type
     * If device Type is Mobile then it returns true otherwise false
     * @return bool
     */
    public function isMobile()
    {
        if(in_array($this->device_name, array(self::ANDROID,self::IOS),true)){
            return true;
        }
        return false;
    }

    /**
     * This method should use to check to determine device Type
     * If device Type is Web(browser) then it returns true otherwise false
     * @return bool
     */
    public function isWeb()
    {
        if($this->device_name == self::WEB){
            return true;
        }
        return false;
    }

    /**
     * This method should use to check to determine device Type
     * If device Type is STB(Set-Top-Box) then it returns true otherwise false
     * @return bool
     */
    public function isSTB()
    {
        if($this->device_name == self::STB){
            return true;
        }
        return false;
    }
}