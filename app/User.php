<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/9/2016
 * Time: 3:30 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class User extends Model
{
    const SUBSCRIBER = 'Subscriber';
    const REGTYPE_BOTH = 'BOTH';
    const REGTYPE_PHONE = 'PHONE';
    const REGTYPE_EMAIL = 'EMAIL';

    protected $table = "users";

    protected $fillable = array('profile_id','username','email','password','role_id','user_status',
          'user_type', 'otp','token','is_iptv','clientType','iptv_token','reset_pass_expire','parent_id','created_by',
        'lat','lon','telco_id','service_operator_type','registration_type');

    /**
     * This method returns a relational object of a user profile
     * To get Subscriber's profile information
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getProfile()
    {
        return $this->belongsTo('App\Entities\SubscriberProfile','profile_id');
    }

    /**
     * This method returns a relational object
     * By using this method we can get subscribed packages of an user.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function getSubscribedPackages()
    {
        return $this->belongsToMany('App\Entities\Package','user_packages','user_id');
    }

    /**
     * This method returns a relational object
     * By using this method we can get subscribed programs of an user.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Programs()
    {
        return $this->belongsToMany('App\Entities\Program','user_programs','user_id');
    }
    
    /**
     * Get Subscriber's Balance
     * @return int
     */
    public function getBalance()
    {
        if(!empty($this->id)){
           $balance = DB::select("select if(GetSubscriberBal(".$this->id.") IS NOT NULL,GetSubscriberBal(".$this->id."),0) as balance");
           $balance = (!empty($balance))? array_shift($balance) : null;
           $balance = (!empty($balance))? $balance->balance : 0;
        }else{
            $balance = 0;
        }
        return $balance;
        
    }

}