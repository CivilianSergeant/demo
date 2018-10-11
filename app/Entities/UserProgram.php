<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/20/2016
 * Time: 11:32 AM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserProgram extends Model
{
    protected $table = "user_programs";

    protected $fillable = array(
        'user_id','program_id','status','charge_type','package_start_date','package_expire_date','duration','created_by','updated_by','parent_id'
    );

    /**
     * This method is used to check is user subscribed or not
     * it will return result on success or null on false
     * @param $user_id
     * @param $program_id
     * @return mixed|null
     */
    public static function isSubscribed($user_id,$program_id)
    {
        $sql = 'Select iptv_package_programs.id,iptv_package_programs.program_id,user_packages.user_id,
                user_packages.package_id,user_packages.no_of_days,
                user_packages.package_start_date,user_packages.package_expire_date from user_packages
                join iptv_package_programs on iptv_package_programs.package_id = user_packages.package_id
                where user_packages.user_id = '.$user_id.' AND iptv_package_programs.program_id = '.$program_id;

        $result = DB::select($sql);

        return (!empty($result))? array_shift($result) : null;
    }
    
    public function getPrograms()
    {
        return $this->belongsTo('App\Entities\Program', 'program_id');
    }
    
    public static function isContentProgramSubscribed($user_id)
    {
        $userPackages = self::where('user_id',$user_id)->get();
        $packages = array();
        if(!empty($userPackages)){
            foreach($userPackages as $i => $userPackage){
                $package = $userPackages[$i]->getPackage;
                if($package->isLive()){
                    unset($userPackages[$i]);
                }else if($package->isCatchup()){
                    $packages[Package::CATCHUP] = $userPackages[$i];
                }else{
                    $packages[Package::VOD] = $userPackages[$i];
                }
            }
        }
        return $packages;
    }
    
    public static function isProgramSubscribed($user,$program)
    {
        return self::where('user_id',$user->id)
                                 ->where('program_id',$program->id)->first();
                
    }

}