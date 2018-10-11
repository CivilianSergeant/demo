<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/9/2016
 * Time: 11:47 AM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    const LIVE = 'LIVE';
    const VOD = 'VOD';
    const CATCHUP = 'CATCHUP';

    protected $table = "iptv_programs";

    protected $hidden = ['pivot'];

    /**
     * This method returns a relational object
     * which could be use to get HLS Links
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getHLSLinks()
    {
        return $this->hasMany('App\Entities\MapStreamerInstance','program_id','id');
    }
    
    public static function isLive($program = null){
        if(!empty($program)){
            if($program->type == self::LIVE){
                return true;
            }
        }
        return false;
    }
    
    public static function isCatchup($program = null){
        if(!empty($program)){
            if($program->type == self::CATCHUP){
                return true;
            }
        }
        return false;
    }
    
    public static function isVod($program = null){
        if(!empty($program)){
            if($program->type == self::VOD){
                return true;
            }
        }
        return false;
    }
}