<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/9/2016
 * Time: 12:31 PM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = "iptv_packages";

    const FREE = 'FREE';
    const LIVE = 'LIVE';
    const CATCHUP = 'CATCHUP';
    const VOD = 'VOD';

    protected $hidden = ['parent_id','created_by','created_at','updated_at','updated_by','pivot'];

    /**
     * If package type Catchup or VoD then its content so it returns true
     * otherwise it returns false
     * @return bool
     */
    public function isContent()
    {
        if(!empty($this->package_type))
        {
            if($this->package_type == self::CATCHUP || $this->package_type == self::VOD){
                return true;
            }
        }

        return false;
    }

    /**
     * It returns belongs to many relationship object which will fetch
     * Collection of Program Object from mapping table `iptv_package_programs` relations
     * @return mixed
     */
    public function Programs()
    {
            return $this->belongsToMany('App\Entities\Program','iptv_package_programs','package_id')
                        ->select('program_id as id','program_name','individual_price');
    }

    /**
     * It returns collection of program object
     * according to given type
     * possible types are CATCHUP | VOD
     * @return mixed
     */
    public function ContentPrograms()
    {
        if(!empty($this->package_type)){
            return Program::select('id','program_name','individual_price')
                        ->where('type',$this->package_type)
                        ->get();
        }
    }
    
    public function isLive()
    {
        if(!empty($this->id) && $this->package_type == self::LIVE){
            return true;
        }
        return false;
    }
    
    public function isCatchup()
    {
        if(!empty($this->id) && $this->package_type == self::CATCHUP){
            return true;
        }
        return false;
    }
    
    public function isVod()
    {
        if(!empty($this->id) && $this->package_type == self::VOD){
            return true;
        }
        return false;
    }
}