<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/10/2016
 * Time: 11:06 AM
 */

namespace App\Http\Controllers;

use App\Entities\ApiSetting;
use App\Entities\BillingSubscriberTransaction;
use App\Entities\DbVersion;
use App\Entities\DeviceType;
use App\Entities\GeoAllowedCountryCode;
use App\Entities\GeoTerritory;
use App\Entities\LogConfirmCodeAttempt;
use App\Entities\LoginLog;
use App\Entities\LogRegistrationAttempt;
use App\Entities\Package;
use App\Entities\ServiceOperator;
use App\Entities\SmsPushLog;
use App\Entities\StreamerInstance;
use App\Entities\SubscriberProfile;
use App\Entities\FcmToken;

use App\Entities\UserPackage;
use App\User;
use App\Utils\AES_Engine;

use App\Utils\CacheDB;
use App\Utils\DetectTerritory;
use App\Utils\Email\Email;
use App\Utils\Helpers;
use App\Utils\Response;
use App\Utils\FcmUtil;

use App\Utils\SMSAPI\SSL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/*** Authorization Notes ***
 *  If GeoIP authorization active then GeoIp process will run during registration
 *      Reference Table api_settings
 *      Reference Column geo_ip_authorization, 0 = active, 1 = inactive
 *  IF Geo Territory authorization active then Geo Territory process will run during registration
 *      Reference Table api_settings
 *      Reference Column geo_territory_authorization, 0 = active, 1 = inactive
 */
class RegistrationController extends RestController
{
    const MINUTE = 10;
    const SECOND = 60;


    public function index()
    {
        if(!empty($this->request)){


            if(!empty($this->request->apiName)
                    && !method_exists($this,$this->request->apiName)){

                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('Api not exist');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            try{
                date_default_timezone_set('Asia/Dhaka');
                $apiName = $this->request->apiName;
                return $this->$apiName();

            }catch(\Exception $ex1){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg($ex1->getMessage().',Line:'.$ex1->getLine());
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

        }else{
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('Request not found');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

    /**
     * This method will call during
     * Registration Subscriber API request for following url
     * http:// [ ServerIp]: [serverPort]/registration-subscriber
     */
    public function registrationSubscriber()
    {

        // Check is deviceType set with
        // otherwise generate a debug error
        if(empty($this->request->deviceType)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('deviceType is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }


        // Check is name set with request
        // otherwise generate a debug error
        if(empty($this->request->name)){

            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(101);
            $response->setErrorMsg('Name Property is required');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }


        $name = $this->request->name;

        // Check is phoneNo set with request
        // otherwise generate a debug error
        if(empty($this->request->phoneNo)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(102);
            $response->setErrorMsg('Phone Number Property is required');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $phoneNo = trim($this->request->phoneNo);

        // Check is phone number is not equal to 14 digits
        // if its not equal to 14 digit generate Error code
        if(strlen($phoneNo) != 14){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(103);
            $response->setErrorMsg('Phone Number is Invalid');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $phoneNo = str_split($phoneNo);
        $plusChar = $phoneNo[0];

        // Check is there plus sign into the phone number
        // if + sign is not present with phone number then generate error code
        if($plusChar != "+"){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(103);
            $response->setErrorMsg('Phone Number is Invalid');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $countryCode = $phoneNo[1].$phoneNo[2];
        $operator    = $phoneNo[3].$phoneNo[4].$phoneNo[5];

        // Check for 88 country code exist at the start of phone number
        // if not found generate error code
        if($countryCode != '88'){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(103);
            $response->setErrorMsg('Phone Number is Invalid');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        // Check if number prefix is exist within the list of predefined prefix
        // if not found generate error
        if(!in_array($operator,array("017","019","018","015","016","011","013"),true)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(103);
            $response->setErrorMsg('Phone Number is Invalid');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $phoneNo = $this->request->phoneNo;

        // Check if email is exist with request
        // if exist then filter email is a valid email address or not
        // sending error code if its not a valid email address
        if(!empty($this->request->email)){
            if(!filter_var($this->request->email,FILTER_VALIDATE_EMAIL)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(104);
                $response->setErrorMsg('E-mail is Invalid');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }
        }

        $email = $this->request->email;

        // Check if serviceOperatorType is not found
        // then generate debug code
        if(empty($this->request->serviceOperatorType)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('serviceOperatorType is Missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        // Check if phone number contain character
        // then generate debug code
        if(preg_match('/[a-zA-Z]/',$phoneNo)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg("Dummy phone number not allowed");
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
        if(!empty($this->request->password) && strlen($this->request->password) < 8){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(120);
            $response->setErrorMsg("Password should be at least 8 character");
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $tokenInfo = time().$phoneNo.$email;
        $token = md5($tokenInfo);

        $otp = $this->generateOTP();
        $domain = 'VIEWERS TV PASSCODE IS: '; //$_SERVER['SERVER_NAME'];
        $otpText = $domain. $otp;

        $fullName = $name;
        $userName = $phoneNo;
        $iptvToken = md5($otp);
        $authorize = false;
        $password = (!empty($this->request->password))? $this->request->password : $userName;   
        $apiSetting = ApiSetting::find(1);

        // Geo IP detection
        try{

            $allowedCountryCodes = GeoAllowedCountryCode::where('is_active',0)->get();

            $geoIPResult =  DB::select("SELECT start_ip4_address,netmask,country_code,country_name
                    FROM geoip
                    WHERE
                    INET_ATON('".$_SERVER['REMOTE_ADDR']."') BETWEEN begin_ip_num AND end_ip_num AND is_active=0
                    LIMIT 1
                    ");


            if(!empty($geoIPResult[0])){  // If IP Matched

                if(!$apiSetting->geo_ip_authorization){ // is system allow ip checking

                    if(!empty($allowedCountryCodes)){
                        foreach($allowedCountryCodes as $allowedCountryCode){
                            if($geoIPResult[0]->country_code == $allowedCountryCode->country_code){
                                $authorize = true;
                                break;
                            }
                        }
                    }
                }else{
                    $authorize = true;
                }

                $logRegistrationAttempt = new LogRegistrationAttempt(array(
                    'mobile_number' => $phoneNo,
                    'email'         => $email,
                    'lat'           => $this->request->lat,
                    'lon'           => $this->request->lon,
                    'device_type'   => $this->request->deviceType,
                    'parent_id'     => 1,
                    'ip'            => $_SERVER['REMOTE_ADDR'],
                    'country_code'  => $geoIPResult[0]->country_code,
                    'country_name'  => $geoIPResult[0]->country_name,
                    'lookup'        => LogRegistrationAttempt::GEOIP
                ));

                $logRegistrationAttempt->save();

            }else{

                // territory block

                $territories = GeoTerritory::where('is_active',0)->get();
                $x = $this->request->lat;
                $y = $this->request->lon;
                $territory = DetectTerritory::detect($x,$y,$territories);
                if(!empty($territory)){
                    if(!$apiSetting->geo_territory_authorization){ // is system allow terrytory checking
                        if(!empty($allowedCountryCodes)) {
                            foreach ($allowedCountryCodes as $allowedCountryCode) {
                                if ($territory->country_code == $allowedCountryCode->country_code) {
                                    $authorize = true;
                                    break;
                                }
                            }
                        }

                    }else{
                        $authorize = true;
                    }

                }

                $logRegistrationAttempt = new LogRegistrationAttempt(array(
                    'mobile_number' => $phoneNo,
                    'email'         => $email,
                    'lat'           => $this->request->lat,
                    'lon'           => $this->request->lon,
                    'device_type'   => $this->request->deviceType,
                    'parent_id'     => 1,
                    'ip'            => $_SERVER['REMOTE_ADDR'],
                    'country_code'  => (!empty($territory))? $territory->country_code : null,
                    'country_name'  => (!empty($territory))? $territory->territory_name : null,
                    'lookup'        => LogRegistrationAttempt::GEOTERRITORY
                ));

                $logRegistrationAttempt->save();
            }



        }catch(\Exception $ex){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().', Line:'.$ex->getLine());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $message  = ($authorize)? "Check SMS/Email for confirmation Code" : "Your territory is not allowed";
        $messageType = ($authorize)? Response::VIEW : Response::DIALOG;

        $phoneNoExist = User::where('username',$phoneNo)->first();
        if(!empty($phoneNo) && !empty($email)){

            if(!empty($phoneNoExist)){
                if($phoneNoExist->email != $email){
                    $response = new Response($this->request->apiName);
                    $response->setStatus(1);
                    $response->setErrorCode(105);
                    $response->setErrorMsg("Phone No and Email is doesn't match");
                    return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                }
            }
        }


        if(!empty($phoneNoExist)){  // IF record found by given phone number

            $phoneNoExist->otp = $otp;
            $phoneNoExist->reset_pass_expire = date('Y-m-d H:i:s',time() + (self::MINUTE * self::SECOND));
            $phoneNoExist->iptv_token = $iptvToken;
            $phoneNoExist->save();

            if($authorize){  // IF authorized then send SMS / Email
                try {
                    $ssl = new SSL();
                    $smsSentResponse = $ssl->sendSMS($phoneNo, $otpText);
                    $smsPushLog = new SmsPushLog($smsSentResponse);
                    $smsPushLog->save();


                }catch(\Exception $essl){
                    
                    $response = new Response($this->request->apiName);
                    $response->setStatus(1);
                    $response->setErrorCode(100);
                    $response->setErrorMsg('Unable to sent OTP');
                    $response->setDebugCode(100);
                    $response->setDebugMsg($essl->getMessage());
                    return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                }

                // Email
                if(!empty($email)){
                    try{

                        $subject = "Viewers Entertainment Confirmation Code";
                        $emailContent = $otpText;
                        Email::sendEmail($email,$subject,$emailContent);

                    }catch (\Exception $exMail){
                        $response = new Response($this->request->apiName);
                        $response->setStatus(1);
                        $response->setDebugCode(100);
                        $response->setDebugMsg($exMail->getMessage());
                        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                    }
                }
            }

            $response = new Response($this->request->apiName);
            $response->setResponse(array(
                'code' => 200,
                'notification' => true,
                'notificationType' => 1,
                'ads' => true,
                'adsType' => 1,
                'authorize' => $authorize,
                'message' => $message,
                'messageType' => $messageType
            ));
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

        }

        // IF not record found for given phone number
        try{

            $save_profile_data = array(
                'subscriber_name' => $fullName,
                'address1'        => null,
                'address2'        => null,
                'country_id'      => null,
                'division_id'     => null,
                'district_id'     => null,
                'area_id'         => null,
                'sub_area_id'     => null,
                'road_id'         => null,
                'contact'         => $phoneNo,
                'token'           => $token,
                'is_foc'          => (!empty($this->request->parentId) && ($this->request->parentId == 1))? 1 : 0, 
                'parent_id'       => (!empty($this->request->parentId))? $this->request->parentId : 1,
                'created_by'      => 1,
            );

            $subscriber = new SubscriberProfile($save_profile_data);
            $subscriber->save();

            if($subscriber->id){

                $telco = null;
                if(strtoupper($this->request->serviceOperatorType) == "TELCO")
                {
                    $telcoPrefix = substr($phoneNo,0,6);
                    $telco = ServiceOperator::where('telco_prefix',$telcoPrefix)->first();
                }

               $deviceType = DeviceType::find($this->request->deviceType);
               $userData = array(

                    'profile_id' => $subscriber->id,
                    'username'   => $userName,
                    'email'      => $email,
                    'password'   => md5($password),
                    'user_status'=> 0,
                    'user_type'  => User::SUBSCRIBER,
                    'role_id'    => 5,
                    'token'      => $token,
                    'otp'        => $otp,
                    'is_iptv'    => 1,
                    'clientType' => (!empty($deviceType))? $deviceType->id : null,
                    'iptv_token' => $iptvToken,
                    'reset_pass_expire' => date('Y-m-d H:i:s',time() + (self::MINUTE * self::SECOND)),
                    'parent_id' => (!empty($this->request->parent_id))? $this->request->parent_id : 1,
                    'created_by' => 1,
                    'lat' => (!empty($this->request->lat)) ? $this->request->lat : 0,
                    'lon' => (!empty($this->request->lon)) ? $this->request->lon : 0,
                    'service_operator_type' => $this->request->serviceOperatorType,
                    'telco_id' => (!empty($telco))? $telco->telco_id : null,
                );

                if(!empty($this->request->phoneNo) && !empty($email)){
                    $userData['registration_type'] = User::REGTYPE_BOTH;
                }elseif (!empty($this->request->phoneNo) && empty($email)){
                    $userData['registration_type'] = User::REGTYPE_PHONE;
                }elseif(empty($this->request->phoneNo) && !empty($email)){
                    $userData['registration_type'] = User::REGTYPE_EMAIL;
                }

                $user = new User($userData);
                $user->save();

                if ($user->id) {

                    if($authorize){ // IF authorize then Send SMS / Email
                        try {

                            // send sms
                            $ssl = new SSL();
                            $smsSentResponse = $ssl->sendSMS($phoneNo, $otpText);
                            $smsPushLog = new SmsPushLog($smsSentResponse);
                            $smsPushLog->save();

                        }catch(\Exception $essl2){

                            $response = new Response($this->request->apiName);
                            $response->setStatus(1);
                            $response->setErrorMsg(100);
                            $response->setErrorMsg('Unable to sent OTP after registration');
                            $response->setDebugCode(100);
                            $response->setDebugMsg($essl2->getMessage());
                            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                        }

                        // Email
                        if(!empty($email)) {
                            try {

                                $subject = "Viewers Entertainment Confirmation Code";
                                $emailContent = $otpText;
                                Email::sendEmail($email, $subject, $emailContent);

                            } catch (\Exception $exMail) {
                                $response = new Response($this->request->apiName);
                                $response->setStatus(1);
                                $response->setDebugCode(100);
                                $response->setDebugMsg($exMail->getMessage());
                                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
                            }
                        }
                    }


                    $response = new Response($this->request->apiName);
                    $response->setResponse(array(
                        'code' => 200,
                        'notification' => true,
                        'notificationType' => 1,
                        'ads' => true,
                        'adsType' => 1,
                        'authorize' => $authorize,
                        'message' => $message,
                        'messageType' => $messageType
                    ));

                    return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

                } else {
                    $response = new Response($this->request->apiName);
                    $response->setStatus(1);
                    $response->setErrorCode(100);
                    $response->setErrorMsg('Registration Failed. Unable to create Subscriber Account');
                    $response->setDebugCode(100);
                    $response->setDebugMsg('Unable to Create User Account into DB');
                    return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                }


            }else{

                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(100);
                $response->setErrorMsg('Registration Failed. Unable to create Subscriber Profile');
                $response->setDebugCode(100);
                $response->setDebugMsg('Unable to Create Subscriber Profile into DB');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

        }catch(\Exception $ex2){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex2->getMessage());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }


    }

    /**
     * This method will call during
     * Confirm Code API request for following url
     * http:// [ ServerIp]: [serverPort]/confirm-code
     * @return string
     */
    public function confirmCode()
    {

        // check if confirm code exist
        // if not then generate error and debug code
        if(empty($this->request->code)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(106);
            $response->setErrorMsg('Code is Required');
            $response->setDebugCode(100);
            $response->setDebugMsg('Parameter code is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        // Check is deviceType set with
        // otherwise generate a debug error
        if(empty($this->request->deviceType)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('deviceType is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $streamers = StreamerInstance::where('is_active',1)->get();

        // Check if streamer exist or not if not
        // then generate debug code
        if(empty($streamers)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('Streamer Instance Not Found');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        // Get all db version from database
        $dbVersion = DbVersion::select('channel_db_version','vod_db_version',
            'notification_db_version','catchup_db_version','package_db_version','category_db_version')
            ->first();

        // Get user by given confirm code
        $user = User::where('otp',$this->request->code)->first();
        $authorize = false;

        $apiSetting = ApiSetting::find(1);

        // Geo IP detection
        try{

            $allowedCountryCodes = GeoAllowedCountryCode::where('is_active',0)->get();

            $geoIPResult =  DB::select("SELECT start_ip4_address,netmask,country_code,country_name
                    FROM geoip
                    WHERE
                    INET_ATON('".$_SERVER['REMOTE_ADDR']."') BETWEEN begin_ip_num AND end_ip_num AND is_active=0
                    LIMIT 1
                    ");

            if(!empty($geoIPResult[0])){  // If IP Matched

                if(!$apiSetting->geo_ip_authorization){ // is system allow to check ip

                    if(!empty($allowedCountryCodes)){
                        foreach($allowedCountryCodes as $allowedCountryCode){
                            if($geoIPResult[0]->country_code == $allowedCountryCode->country_code){
                                $authorize = true;
                                break;
                            }
                        }
                    }
                }else{
                    $authorize = true;
                }

                $profile = (!empty($user))? $user->getProfile : null;
                $logConfirmCodeAttempt = new LogConfirmCodeAttempt(array(
                    'mobile_number' => (!empty($profile))? $profile->contact : null,
                    'email'         => (!empty($user))? $user->email : null,
                    'confirm_code'  => $this->request->code,
                    'lat'           => $this->request->lat,
                    'lon'           => $this->request->lon,
                    'device_type'   => $this->request->deviceType,
                    'parent_id'     => 1,
                    'ip'            => $_SERVER['REMOTE_ADDR'],
                    'country_code'  => (!empty($geoIPResult[0]))? $geoIPResult[0]->country_code : null,
                    'country_name'  => (!empty($geoIPResult[0]))? $geoIPResult[0]->country_name : null,
                    'lookup'        => LogConfirmCodeAttempt::GEOIP
                ));

                $logConfirmCodeAttempt->save();

            }else{

                // territory block

                $territories = GeoTerritory::where('is_active',0)->get();
                $x = $this->request->lat;
                $y = $this->request->lon;
                $territory = DetectTerritory::detect($x,$y,$territories);
                if(!empty($territory)){

                    if(!$apiSetting->geo_terrytory_authorization){ // is system allow to check territory
                        if(!empty($allowedCountryCodes)) {
                            foreach ($allowedCountryCodes as $allowedCountryCode) {
                                if ($territory->country_code == $allowedCountryCode->country_code) {
                                    $authorize = true;
                                    break;
                                }
                            }
                        }
                    }else{
                        $authorize = true;
                    }
                }

                $profile = (!empty($user))? $user->getProfile : null;
                $logConfirmCodeAttempt = new LogConfirmCodeAttempt(array(
                    'mobile_number' => (!empty($profile))? $profile->contact : null,
                    'email'         => (!empty($user))? $user->email : null,
                    'confirm_code'  => $this->request->code,
                    'lat'           => $this->request->lat,
                    'lon'           => $this->request->lon,
                    'device_type'   => $this->request->deviceType,
                    'parent_id'     => 1,
                    'ip'            => $_SERVER['REMOTE_ADDR'],
                    'country_code'  => (!empty($territory))? $territory->country_code : null,
                    'country_name'  => (!empty($territory))? $territory->territory_name : null,
                    'lookup'        => LogConfirmCodeAttempt::GEOTERRITORY
                ));

                $logConfirmCodeAttempt->save();
            }

        }catch(\Exception $ex){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().', Line:'.$ex->getLine());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if(!empty($user)){

            // if user don't have access permission remotely then
            // send response with inactive account message
            if($user->is_remote_access_enabled){
                $authorize = false;
                $response = new Response($this->request->apiName);
                $response->setStatus(0);
                $response->setResponse(array(
                    "code" => 200,
                    "notification" => true,
                    "notificationType" => 1,
                    "ads" => true,
                    "adsType"      => 1,
                    "authorize"    => $authorize,
                    "customerId"   => $user->id,
                    "password"     => $user->password,
                    "customerName" => $user->getProfile->subscriber_name,
                    "sessionToken" => $user->iptv_token,
                    "balance"      => 0,
                    "systemTime"   => date('YmdHis'),
                    "dbVersion"    => $dbVersion,
                    'message'      => "Your account is inactive",
                    'messageType'  => "DIALOG"
                ));
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            $reset_pass_expire = strtotime($user->reset_pass_expire);

            // Check if reset_pass_expire is not expired
            // then perform following instructions
            if($reset_pass_expire > time()){
                $otp = $this->generateOTP();  // random generated 6 digi value

                $user->user_status = 1;
               // $user->password = md5($otp.time());
                $user->iptv_token  = md5($otp.time().$otp);
                $user->otp = null;
                $user->reset_pass_expire = null;
                $user->lat = (!empty($this->request->lat))? $this->request->lat : 0;
                $user->lon = (!empty($this->request->lon))? $this->request->lon : 0;
                $user->clientType = $this->request->deviceType;
                $user->save();

                // Set Memcached Data
                try{

                    $cacheDB = new CacheDB();
                    $cacheDB->pushToMemcached($user, $streamers);

                }catch(\Exception $ec) {
                    $response = new Response($this->request->apiName);
                    $response->setStatus(1);
                    $response->setDebugCode(100);
                    $response->setDebugMsg($ec->getMessage());
                    return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
                }

                $result = DB::select("select if(GetSubscriberBal(".$user->id.") IS NOT NULL,GetSubscriberBal(16),0) as balance");

                // Subscribe All Free Packages
                $packages = Package::where('not_deleteable',1)->get();

                if(!empty($packages)){
                    foreach($packages as $package){
                        $userPackage = UserPackage::firstOrNew(array('user_id'=>$user->id,'package_id'=>$package->id));
                        if(empty($userPackage->id)){

                            $today = date('Y-m-d H:i:s');
                            $start_date_object = new \DateTime($today);
                            $expire_date_object = $start_date_object;
                            $expire_date_object->add(new \DateInterval('P' . $package->duration . 'D'));

                            $userPackage->charge_type = 0;
                            $userPackage->package_start_date = $today;
                            $userPackage->package_expire_date = $expire_date_object->format('Y-m-d 23:59:59');
                            $userPackage->no_of_days = $package->duration;
                            $userPackage->user_package_type_id = 1;
                            $userPackage->parent_id = 1;

                            try {

                                $userPackage->save();

                                $billingSubscriberTransaction = new BillingSubscriberTransaction();
                                $billingSubscriberTransaction->pairing_id = 0;
                                $billingSubscriberTransaction->subscriber_id = $user->id;
                                $billingSubscriberTransaction->lco_id = 1;
                                $billingSubscriberTransaction->package_id = $package->id;
                                $billingSubscriberTransaction->payment_method_id = 1;
                                $billingSubscriberTransaction->transaction_types = 'D';
                                $billingSubscriberTransaction->credit = 0;
                                $billingSubscriberTransaction->debit = 0;
                                $billingSubscriberTransaction->balance = (!empty($result)) ? $result[0]->balance : 0;
                                $billingSubscriberTransaction->user_package_assign_type_id = 1;
                                $billingSubscriberTransaction->transaction_date = date('Y-m-d H:i:s');
                                $billingSubscriberTransaction->parent_id = 1;
                                $billingSubscriberTransaction->save();

                            }catch(\Exception $ex){
                                $response = new Response();
                                $response->setDebugCode(100);
                                $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
                                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                            }

                        }
                    }
                }

                // Save Login Logs
                $loginLogs = new LoginLog(array(
                    'user_id' => $user->id,
                    'session_token' => $user->iptv_token,
                    'user_ip' => $_SERVER['REMOTE_ADDR'],
                    'device_type' => $this->request->deviceType,
                    'lat'=> (!empty($this->request->lat))? $this->request->lat : 0,
                    'lon'=> (!empty($this->request->lon))? $this->request->lon : 0,
                    'parent_id' => (!empty($this->request->parent_id))? $this->request->parent_id : 1
                ));

                $loginLogs->save();
                
                // logut from other device
                $fcm_results = null;
                if(!empty($this->request->fcmToken)){
                    $requestedFcmToken = FcmToken::where('fcm_token',$this->request->fcmToken)->first();
                    $requestedFcmToken->logout_flag = 0;
                    $requestedFcmToken->save();
                    
                    $otherTokens = FcmToken::where('fcm_token','!=',$this->request->fcmToken)->where('user_id',$user->id)->get();
                    // if any records found send logout notification to them and delete those from db and fcm_device_group
                    if(!empty($otherTokens)){
//                        $payload = array(
//                            // 'image'              => "http://connectingmass.com/notification_test/jspromise.jpg",
//                            'notificationHeader' => 'New Login',
//                            'notificationText'   => 'Your account has been logged-in from another device',
//                            'resourceUrl'        => '',
//                            'notificationType'   => 'LOGOUT'
//                        );
                        foreach($otherTokens as $otherToken){
                            $otherToken->logout_flag = 1;
                            $otherToken->save();
                            //$r = FcmUtil::sendNotification($otherToken->fcm_token, $payload);
                            try{
                                
                            }catch(\Exception $ex){
                                return new Response($ex->getMessage());
                            }
                        }
                    }
                }

                $response = new Response($this->request->apiName);
                $response->setResponse(array(
                    "code" => 200,
                    "notification" => true,
                    "notificationType" => 1,
                    "ads" => true,
                    "adsType" => 1,
                    "customerId"=> $user->id,
                    "authorize"=> true,
                    "password" => $user->password,
                    "sessionToken"=> $user->iptv_token,
                    "customerName" => $user->getProfile->subscriber_name,
                    "systemTime" => date('YmdHis'),
                    "balance"  => $user->getBalance(),
                    "dbVersion"=>$dbVersion,
                    'message' => "Login Succesfull",
                    'messageType' => "TOAST",
                    
                ));

                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

            }else{
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(107);
                $response->setErrorMsg('Confirmation Code Expired');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }
        }else{
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(108);
            $response->setErrorMsg('Invalid Code');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

    /**
     * This method will call during
     * Api Login API request for following url
     * http:// [ ServerIp]: [serverPort]/api-login
     * @return string
     */
    public function apiLogin()
    {
        // Check if device Type is not available with the request
        // then generate debug code
        if(empty($this->request->deviceType)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('deviceType is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        // Check if customer id is not available with the request
        // then generate debug code
        if(empty($this->request->customerId)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('customerId is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        // Check if password not exist with the
        // request then generate debug code
        if(empty($this->request->password)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('password is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        // get all streamers
        $streamers = StreamerInstance::where('is_active',1)->get();

        // Check if streamers not exist
        // then generate debug code
        if(empty($streamers)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('Streamer Instance Not Found');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        // Get latest db version from database
        $dbVersion = DbVersion::select('channel_db_version','vod_db_version',
            'notification_db_version','catchup_db_version', 'package_db_version','category_db_version')
            ->first();

        $deviceType = DeviceType::find($this->request->deviceType);

        $user = User::where('password',$this->request->password);

        if($deviceType->isMobile()){
            $user = $user->where('id',$this->request->customerId);
        }

        if($deviceType->isWeb() || $deviceType->isSTB()){
            $user = $user->where('username',$this->request->customerId);
        }

        $user = $user->first();
        
//        if(!empty($user) && $user->clientType != $deviceType->id){
//            $deviceName = null;
//            if($deviceType->isMobile()){
//                $deviceName = 'Mobile';
//            }else if($deviceType->isWeb()){
//                $deviceName = 'Web';
//            }else if($deviceType->isSTB()){
//                $deviceName = 'STB';
//            }
//            $response = new Response($this->request->apiName);
//            $response->setStatus(1);
//            $response->setErrorCode(112);
//            $response->setErrorMsg('Sorry! Your account was logged in from '.$deviceName);
//            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
//        }

        // initial authorize variable by default false
        $authorize = false;

        // get default api settings
        $apiSetting = ApiSetting::find(1);


        // Geo IP detection
        try{

            $allowedCountryCodes = GeoAllowedCountryCode::where('is_active',0)->get();

            $geoIPResult =  DB::select("SELECT start_ip4_address,netmask,country_code,country_name
                    FROM geoip
                    WHERE
                    INET_ATON('".$_SERVER['REMOTE_ADDR']."') BETWEEN begin_ip_num AND end_ip_num AND is_active=0
                    LIMIT 1
                    ");

            if(!empty($geoIPResult[0])){  // If IP Matched

                if(!$apiSetting->geo_ip_authorization){ // is system allow to check ip

                    if(!empty($allowedCountryCodes)){
                        foreach($allowedCountryCodes as $allowedCountryCode){
                            if($geoIPResult[0]->country_code == $allowedCountryCode->country_code){
                                $authorize = true;
                                break;
                            }
                        }
                    }
                }else{
                    $authorize = true;
                }

            }else{

                // territory block

                $territories = GeoTerritory::where('is_active',0)->get();
                $x = $this->request->lat;
                $y = $this->request->lon;
                $territory = DetectTerritory::detect($x,$y,$territories);
                if(!empty($territory)){

                    if(!$apiSetting->geo_terrytory_authorization){ // is system allow to check territory
                        if(!empty($allowedCountryCodes)) {
                            foreach ($allowedCountryCodes as $allowedCountryCode) {
                                if ($territory->country_code == $allowedCountryCode->country_code) {
                                    $authorize = true;
                                    break;
                                }
                            }
                        }
                    }else{
                        $authorize = true;
                    }
                }
            }

        }catch(\Exception $ex){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().', Line:'.$ex->getLine());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }


        if(!empty($user)){

            if($user->is_remote_access_enabled){  // if user account is remotely inactive
                $authorize = false;
                $response = new Response($this->request->apiName);
                $response->setStatus(0);
                $response->setResponse(array(
                    "code" => 200,
                    "notification" => true,
                    "notificationType" => 1,
                    "ads" => true,
                    "adsType" => 1,
                    "authorize"=>$authorize,
                    "customerId"=> $user->id,
                    "sessionToken"=> $user->iptv_token,
                    "customerName" => $user->getProfile->subscriber_name,
                    "systemTime" => date("YmdHis"),
                    "balance" => 0,
                    "dbVersion"=> (!empty($dbVersion))? $dbVersion : null,
                    'message' => "Your account is inactive",
                    'messageType' => "DIALOG"
                ));
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            $newIpToken = $this->generateOTP();
            $oldToken = $user->iptv_token;
            $user->user_status = 1;
            $user->iptv_token  = md5($newIpToken.time().$newIpToken);
            $user->otp = null;
            $user->reset_pass_expire = null;
            $user->lat = (!empty($this->request->lat))? $this->request->lat : 0;
            $user->lon = (!empty($this->request->lon))? $this->request->lon : 0;
            $user->clientType = $this->request->deviceType;
            $user->save();

            // Set Memcached Data
            try{

                $cacheDB = new CacheDB();
                $cacheDB->pushToMemcached($user, $oldToken);

            }catch(\Exception $ec) {
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg($ec->getMessage());
                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            }


            // Save Login Logs
            $loginLogs = new LoginLog(array(
                'user_id' => $user->id,
                'session_token' => $user->iptv_token,
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'device_type' => $user->clientType,
                'lat' => (!empty($this->request->lat)) ? $this->request->lat : 0,
                'lon' => (!empty($this->request->lon)) ? $this->request->lon : 0,
                'parent_id' => (!empty($user->parent_id)) ? $user->parent_id : 1
            ));

            $loginLogs->save();

            //$result = DB::select("select if(GetSubscriberBal(".$user->id.") IS NOT NULL,GetSubscriberBal(".$user->id."),0) as balance");
            
            // logut from other device
            $fcm_results = null;
            if(!empty($this->request->fcmToken)){
                $requestedFcmToken = FcmToken::where('fcm_token',$this->request->fcmToken)->first();
                $requestedFcmToken->logout_flag = 0;
                $requestedFcmToken->save();
                    
                $otherTokens = FcmToken::where(['user_id'=>$user->id])->where('fcm_token','!=',$this->request->fcmToken)->get();
                // if any records found send logout notification to them and delete those from db and fcm_device_group
                if(!empty($otherTokens)){
//                    $payload = array(
//                        // 'image'              => "http://connectingmass.com/notification_test/jspromise.jpg",
//                        'notificationHeader' => 'New Login',
//                        'notificationText'   => 'Your account has been logged-in from another device',
//                        'resourceUrl'        => '',
//                        'notificationType'   => 'LOGOUT'
//                    );
                    foreach($otherTokens as $otherToken){

                       // $r = FcmUtil::sendNotification($otherToken->fcm_token, $payload);
                        try{
                            $otherToken->logout_flag = 1;
                            $otherToken->save();
    //                    $result = json_decode($r);
    //                    if(!empty($result->failure)){
    //                        $otherToken->delete();
    //                    }
                        //$fcm_results[] = $r;
                        }catch(\Exception $ex){
                            return new Response($ex->getMessage());
                        }
                    }
                }
            }
            

            $response = new Response($this->request->apiName);
            $response->setResponse(array(
                "code" => 200,
                "notification" => true,
                "notificationType" => 1,
                "ads"          => true,
                "adsType"      => 1,
                "customerId"   => $user->id,
                "authorize"    => true,
                "sessionToken" => $user->iptv_token,
                "customerName" => $user->getProfile->subscriber_name,
                "systemTime"   => date('YmdHis'),
                "balance"      => $user->getBalance(),
                "dbVersion"    => (!empty($dbVersion))? $dbVersion : null,
                'message'      => "Login Succesfull",
                'messageType'  => "NONE"
               // 'fcmNotificationResponse' => $fcm_results
                
            ));

            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));


        }else{

            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(109);
            $response->setErrorMsg('No Account found');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

        }


    }

    /**
     * This method will call during
     * Registration Subscriber API request for following url
     * http:// [ ServerIp]: [serverPort]/registration-subscriber
     */
    public function reRegistration()
    {

        // Check is deviceType set with
        // otherwise generate a debug error
        if(empty($this->request->deviceType)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('deviceType is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        //$name = $this->request->name;

        // Check is phoneNo set with request
        // otherwise generate a debug error
        if(empty($this->request->phoneNo)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(102);
            $response->setErrorMsg('Phone Number Property is required');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $phoneNo = trim($this->request->phoneNo);

        // Check is phone number is not equal to 14 digits
        // if its not equal to 14 digit generate Error code
        if(strlen($phoneNo) != 14){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(103);
            $response->setErrorMsg('Phone Number is Invalid');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $phoneNo = str_split($phoneNo);
        $plusChar = $phoneNo[0];

        // Check is there plus sign into the phone number
        // if + sign is not present with phone number then generate error code
        if($plusChar != "+"){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(103);
            $response->setErrorMsg('Phone Number is Invalid');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $countryCode = $phoneNo[1].$phoneNo[2];
        $operator    = $phoneNo[3].$phoneNo[4].$phoneNo[5];

        // Check for 88 country code exist at the start of phone number
        // if not found generate error code
        if($countryCode != '88'){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(103);
            $response->setErrorMsg('Phone Number is Invalid');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        // Check if number prefix is exist within the list of predefined prefix
        // if not found generate error
        if(!in_array($operator,array("017","019","018","015","016","011","013"),true)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(103);
            $response->setErrorMsg('Phone Number is Invalid');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $phoneNo = $this->request->phoneNo;

        // Check if email is exist with request
        // if exist then filter email is a valid email address or not
        // sending error code if its not a valid email address
        if(!empty($this->request->email)){
            if(!filter_var($this->request->email,FILTER_VALIDATE_EMAIL)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(104);
                $response->setErrorMsg('E-mail is Invalid');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }
        }

        $email = $this->request->email;

        // Check if serviceOperatorType is not found
        // then generate debug code
        if(empty($this->request->serviceOperatorType)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('serviceOperatorType is Missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        // Check if phone number contain character
        // then generate debug code
        if(preg_match('/[a-zA-Z]/',$phoneNo)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg("Dummy phone number not allowed");
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $tokenInfo = time().$phoneNo.$email;
        $token = md5($tokenInfo);

        $otp = $this->generateOTP();
        $domain = 'VIEWERS TV PASSCODE IS: '; //$_SERVER['SERVER_NAME'];
        $otpText = $domain. $otp;

        //$fullName = $name;
        $userName = $phoneNo;
        $iptvToken = $password = md5($otp);
        $authorize = false;

        $apiSetting = ApiSetting::find(1);



        // Geo IP detection
        try{

            $allowedCountryCodes = GeoAllowedCountryCode::where('is_active',0)->get();

            $geoIPResult =  DB::select("SELECT start_ip4_address,netmask,country_code,country_name
                    FROM geoip
                    WHERE
                    INET_ATON('".$_SERVER['REMOTE_ADDR']."') BETWEEN begin_ip_num AND end_ip_num AND is_active=0
                    LIMIT 1
                    ");


            if(!empty($geoIPResult[0])){  // If IP Matched

                if(!$apiSetting->geo_ip_authorization){ // is system allow ip checking

                    if(!empty($allowedCountryCodes)){
                        foreach($allowedCountryCodes as $allowedCountryCode){
                            if($geoIPResult[0]->country_code == $allowedCountryCode->country_code){
                                $authorize = true;
                                break;
                            }
                        }
                    }
                }else{
                    $authorize = true;
                }

                $logRegistrationAttempt = new LogRegistrationAttempt(array(
                    'mobile_number' => $phoneNo,
                    'email'         => $email,
                    'lat'           => $this->request->lat,
                    'lon'           => $this->request->lon,
                    'device_type'   => $this->request->deviceType,
                    'parent_id'     => 1,
                    'ip'            => $_SERVER['REMOTE_ADDR'],
                    'country_code'  => $geoIPResult[0]->country_code,
                    'country_name'  => $geoIPResult[0]->country_name,
                    'lookup'        => LogRegistrationAttempt::GEOIP
                ));

                $logRegistrationAttempt->save();

            }else{

                // territory block

                $territories = GeoTerritory::where('is_active',0)->get();
                $x = $this->request->lat;
                $y = $this->request->lon;
                $territory = DetectTerritory::detect($x,$y,$territories);
                if(!empty($territory)){
                    if(!$apiSetting->geo_territory_authorization){ // is system allow terrytory checking
                        if(!empty($allowedCountryCodes)) {
                            foreach ($allowedCountryCodes as $allowedCountryCode) {
                                if ($territory->country_code == $allowedCountryCode->country_code) {
                                    $authorize = true;
                                    break;
                                }
                            }
                        }

                    }else{
                        $authorize = true;
                    }

                }

                $logRegistrationAttempt = new LogRegistrationAttempt(array(
                    'mobile_number' => $phoneNo,
                    'email'         => $email,
                    'lat'           => $this->request->lat,
                    'lon'           => $this->request->lon,
                    'device_type'   => $this->request->deviceType,
                    'parent_id'     => 1,
                    'ip'            => $_SERVER['REMOTE_ADDR'],
                    'country_code'  => (!empty($territory))? $territory->country_code : null,
                    'country_name'  => (!empty($territory))? $territory->territory_name : null,
                    'lookup'        => LogRegistrationAttempt::GEOTERRITORY
                ));

                $logRegistrationAttempt->save();
            }



        }catch(\Exception $ex){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().', Line:'.$ex->getLine());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $message  = ($authorize)? "Check SMS/Email for confirmation Code" : "Your territory is not allowed";
        $messageType = ($authorize)? Response::VIEW : Response::DIALOG;

        $phoneNoExist = User::where('username',$phoneNo)->first();
        if(!empty($phoneNo) && !empty($email)){

            if(!empty($phoneNoExist)){
                if($phoneNoExist->email != $email){
                    $response = new Response($this->request->apiName);
                    $response->setStatus(1);
                    $response->setErrorCode(105);
                    $response->setErrorMsg("Phone No and Email is doesn't match");
                    return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                }
            }
        }


        if(!empty($phoneNoExist)){  // IF record found by given phone number

            $phoneNoExist->otp = $otp;
            $phoneNoExist->reset_pass_expire = date('Y-m-d H:i:s',time() + (self::MINUTE * self::SECOND));
            $phoneNoExist->iptv_token = $iptvToken;
            $phoneNoExist->save();

            if($authorize){  // IF authorized then send SMS / Email
                try {
                    $ssl = new SSL();
                    $smsSentResponse = $ssl->sendSMS($phoneNo, $otpText);
                    $smsPushLog = new SmsPushLog($smsSentResponse);
                    $smsPushLog->save();


                }catch(\Exception $essl){
                    $response = new Response($this->request->apiName);
                    $response->setStatus(1);
                    $response->setErrorCode(100);
                    $response->setErrorMsg('Unable to sent OTP');
                    $response->setDebugCode(100);
                    $response->setDebugMsg($essl->getMessage());
                    return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                }

                // Email
                if(!empty($email)){
                    try{

                        $subject = "Viewers Entertainment Confirmation Code";
                        $emailContent = $otpText;
                        Email::sendEmail($email,$subject,$emailContent);

                    }catch (\Exception $exMail){
                        $response = new Response($this->request->apiName);
                        $response->setStatus(1);
                        $response->setDebugCode(100);
                        $response->setDebugMsg($exMail->getMessage());
                        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                    }
                }
            }

            $response = new Response($this->request->apiName);
            $response->setResponse(array(
                'code' => 200,
                'notification' => true,
                'notificationType' => 1,
                'ads' => true,
                'adsType' => 1,
                'authorize' => $authorize,
                'message' => $message,
                'messageType' => $messageType
            ));
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

        }

        // IF not record found for given phone number
        try{

            $save_profile_data = array(
                //'subscriber_name' => $fullName,
                'address1'        => null,
                'address2'        => null,
                'country_id'      => null,
                'division_id'     => null,
                'district_id'     => null,
                'area_id'         => null,
                'sub_area_id'     => null,
                'road_id'         => null,
                'contact'         => $phoneNo,
                'token'           => $token,
                'is_foc'          => (!empty($this->request->parentId) && ($this->request->parentId == 1))? 1 : 0, 
                'parent_id'       => (!empty($this->request->parentId))? $this->request->parentId : 1,
                'created_by'      => 1,
            );

            $subscriber = new SubscriberProfile($save_profile_data);
            $subscriber->save();

            if($subscriber->id){

                $telco = null;
                if(strtoupper($this->request->serviceOperatorType) == "TELCO")
                {
                    $telcoPrefix = substr($phoneNo,0,6);
                    $telco = ServiceOperator::where('telco_prefix',$telcoPrefix)->first();
                }

                $deviceType = DeviceType::find($this->request->deviceType);
                $userData = array(

                    'profile_id' => $subscriber->id,
                    'username'   => $userName,
                    'email'      => $email,
                    'password'   => md5($phoneNo),
                    'user_status'=> 0,
                    'user_type'  => User::SUBSCRIBER,
                    'role_id'    => 5,
                    'token'      => $token,
                    'otp'        => $otp,
                    'is_iptv'    => 1,
                    'clientType' => (!empty($deviceType))? $deviceType->id : null,
                    'iptv_token' => $iptvToken,
                    'reset_pass_expire' => date('Y-m-d H:i:s',time() + (self::MINUTE * self::SECOND)),
                    'parent_id' => (!empty($this->request->parent_id))? $this->request->parent_id : 1,
                    'created_by' => 1,
                    'lat' => (!empty($this->request->lat)) ? $this->request->lat : 0,
                    'lon' => (!empty($this->request->lon)) ? $this->request->lon : 0,
                    'service_operator_type' => $this->request->serviceOperatorType,
                    'telco_id' => (!empty($telco))? $telco->telco_id : null
                );
                
                if(!empty($this->request->phoneNo) && !empty($email)){
                    $userData['registration_type'] = User::REGTYPE_BOTH;
                }elseif (!empty($this->request->phoneNo) && empty($email)){
                    $userData['registration_type'] = User::REGTYPE_PHONE;
                }elseif(empty($this->request->phoneNo) && !empty($email)){
                    $userData['registration_type'] = User::REGTYPE_EMAIL;
                }

                $user = new User($userData);
                $user->save();

                if ($user->id) {

                    if($authorize){ // IF authorize then Send SMS / Email
                        try {

                            // send sms
                            $ssl = new SSL();
                            $smsSentResponse = $ssl->sendSMS($phoneNo, $otpText);
                            $smsPushLog = new SmsPushLog($smsSentResponse);
                            $smsPushLog->save();

                        }catch(\Exception $essl2){

                            $response = new Response($this->request->apiName);
                            $response->setStatus(1);
                            $response->setErrorMsg(100);
                            $response->setErrorMsg('Unable to sent OTP after registration');
                            $response->setDebugCode(100);
                            $response->setDebugMsg($essl2->getMessage());
                            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                        }

                        // Email
                        if(!empty($email)) {
                            try {

                                $subject = "Viewers Entertainment Confirmation Code";
                                $emailContent = $otpText;
                                Email::sendEmail($email, $subject, $emailContent);

                            } catch (\Exception $exMail) {
                                $response = new Response($this->request->apiName);
                                $response->setStatus(1);
                                $response->setDebugCode(100);
                                $response->setDebugMsg($exMail->getMessage());
                                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
                            }
                        }
                    }


                    $response = new Response($this->request->apiName);
                    $response->setResponse(array(
                        'code' => 200,
                        'notification' => true,
                        'notificationType' => 1,
                        'ads' => true,
                        'adsType' => 1,
                        'authorize' => $authorize,
                        'message' => $message,
                        'messageType' => $messageType
                    ));

                    return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

                } else {
                    $response = new Response($this->request->apiName);
                    $response->setStatus(1);
                    $response->setErrorCode(100);
                    $response->setErrorMsg('Registration Failed. Unable to create Subscriber Account');
                    $response->setDebugCode(100);
                    $response->setDebugMsg('Unable to Create User Account into DB');
                    return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                }


            }else{

                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(100);
                $response->setErrorMsg('Registration Failed. Unable to create Subscriber Profile');
                $response->setDebugCode(100);
                $response->setDebugMsg('Unable to Create Subscriber Profile into DB');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

        }catch(\Exception $ex2){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex2->getMessage());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }


    }
    
    
    public function signIn()
    {
        // Check if device Type is not available with the request
        // then generate debug code
        if(empty($this->request->deviceType)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('deviceType is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        // Check if customer id is not available with the request
        // then generate debug code
        if(empty($this->request->customerId)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('customerId is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        // Check if password not exist with the
        // request then generate debug code
        if(empty($this->request->password)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('password is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        // Get latest db version from database
        $dbVersion = DbVersion::select('channel_db_version','vod_db_version',
            'notification_db_version','catchup_db_version', 'package_db_version','category_db_version')
            ->first();

        $deviceType = DeviceType::find($this->request->deviceType);

        $user = User::where('username',$this->request->customerId)
                ->where('password',md5($this->request->password))
                ->first();

        // initial authorize variable by default false
        $authorize = false;

        // get default api settings
        $apiSetting = ApiSetting::find(1);


        // Geo IP detection
        try{

            $allowedCountryCodes = GeoAllowedCountryCode::where('is_active',0)->get();

            $geoIPResult =  DB::select("SELECT start_ip4_address,netmask,country_code,country_name
                    FROM geoip
                    WHERE
                    INET_ATON('".$_SERVER['REMOTE_ADDR']."') BETWEEN begin_ip_num AND end_ip_num AND is_active=0
                    LIMIT 1
                    ");

            if(!empty($geoIPResult[0])){  // If IP Matched

                if(!$apiSetting->geo_ip_authorization){ // is system allow to check ip

                    if(!empty($allowedCountryCodes)){
                        foreach($allowedCountryCodes as $allowedCountryCode){
                            if($geoIPResult[0]->country_code == $allowedCountryCode->country_code){
                                $authorize = true;
                                break;
                            }
                        }
                    }
                }else{
                    $authorize = true;
                }

            }else{

                // territory block

                $territories = GeoTerritory::where('is_active',0)->get();
                $x = $this->request->lat;
                $y = $this->request->lon;
                $territory = DetectTerritory::detect($x,$y,$territories);
                if(!empty($territory)){

                    if(!$apiSetting->geo_terrytory_authorization){ // is system allow to check territory
                        if(!empty($allowedCountryCodes)) {
                            foreach ($allowedCountryCodes as $allowedCountryCode) {
                                if ($territory->country_code == $allowedCountryCode->country_code) {
                                    $authorize = true;
                                    break;
                                }
                            }
                        }
                    }else{
                        $authorize = true;
                    }
                }
            }

        }catch(\Exception $ex){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().', Line:'.$ex->getLine());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }


        if(!empty($user)){

            if($user->is_remote_access_enabled){  // if user account is remotely inactive
                $authorize = false;
                $response = new Response($this->request->apiName);
                $response->setStatus(0);
                $response->setResponse(array(
                    "code" => 200,
                    "notification" => true,
                    "notificationType" => 1,
                    "ads" => true,
                    "adsType" => 1,
                    "authorize"=>$authorize,
                    "customerId"=> $user->id,
                    "password" => $user->password,
                    "sessionToken"=> $user->iptv_token,
                    "customerName" => $user->getProfile->subscriber_name,
                    "systemTime" => date("YmdHis"),
                    "balance" => $user->getBalance(),
                    "dbVersion"=> (!empty($dbVersion))? $dbVersion : null,
                    'message' => "Your account is inactive",
                    'messageType' => "DIALOG",
                ));
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            $newIpToken = $this->generateOTP();
            $oldToken = $user->iptv_token;
            $user->user_status = 1;
            $user->iptv_token  = md5($newIpToken);
            $user->otp = null;
            $user->reset_pass_expire = null;
            $user->lat = (!empty($this->request->lat))? $this->request->lat : 0;
            $user->lon = (!empty($this->request->lon))? $this->request->lon : 0;
            $user->clientType = $this->request->deviceType;
            $user->save();

            // Set Memcached Data
            try{

                $cacheDB = new CacheDB();
                $cacheDB->pushToMemcached($user, $oldToken);

            }catch(\Exception $ec) {
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg($ec->getMessage());
                $response->setErrorMsg("Sorry! Unable to loggin right now. Some problem occured, please try again later");
                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            }


            // Save Login Logs
            $loginLogs = new LoginLog(array(
                'user_id' => $user->id,
                'session_token' => $user->iptv_token,
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'device_type' => $user->clientType,
                'lat' => (!empty($this->request->lat)) ? $this->request->lat : 0,
                'lon' => (!empty($this->request->lon)) ? $this->request->lon : 0,
                'parent_id' => (!empty($user->parent_id)) ? $user->parent_id : 1
            ));

            $loginLogs->save();

            //$result = DB::select("select if(GetSubscriberBal(".$user->id.") IS NOT NULL,GetSubscriberBal(16),0) as balance");
            
            // logut from other device
            $fcm_results = null;
            if(!empty($this->request->fcmToken)){
                $requestedFcmToken = FcmToken::where('fcm_token',$this->request->fcmToken)->first();
                $requestedFcmToken->logout_flag = 0;
                $requestedFcmToken->save();
                    
                $otherTokens = FcmToken::where(['user_id'=>$user->id])->where('fcm_token','!=',$this->request->fcmToken)->get();
                // if any records found send logout notification to them and delete those from db and fcm_device_group
                if(!empty($otherTokens)){
//                    $payload = array(
//                        // 'image'              => "http://connectingmass.com/notification_test/jspromise.jpg",
//                        'notificationHeader' => 'New Login',
//                        'notificationText'   => 'Your account has been logged-in from another device',
//                        'resourceUrl'        => '',
//                        'notificationType'   => 'LOGOUT'
//                    );
                    foreach($otherTokens as $otherToken){

                       // $r = FcmUtil::sendNotification($otherToken->fcm_token, $payload);
                        try{
                            $otherToken->logout_flag = 1;
                            $otherToken->save();
    //                    $result = json_decode($r);
    //                    if(!empty($result->failure)){
    //                        $otherToken->delete();
    //                    }
                        //$fcm_results[] = $r;
                        }catch(\Exception $ex){
                            return new Response($ex->getMessage());
                        }
                    }
                }
            }
            

            $response = new Response($this->request->apiName);
            $response->setResponse(array(
                "code" => 200,
                "notification" => true,
                "notificationType" => 1,
                "ads"          => true,
                "adsType"      => 1,
                "customerId"   => $user->id,
                "authorize"    => true,
                "password"     => $user->password,
                "sessionToken" => $user->iptv_token,
                "customerName" => $user->getProfile->subscriber_name,
                "systemTime"   => date('YmdHis'),
                'deviceType'   => $user->clientType,
                "balance"      => $user->getBalance(),
                "dbVersion"    => (!empty($dbVersion))? $dbVersion : null,
                'message'      => "Login Succesfull",
                'messageType'  => "NONE"
                
            ));

            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));


        }else{

            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(109);
            $response->setErrorMsg('No Account found');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

        }


    }
    
    public function forgotPassword()
    {
        if(!empty($this->request->phoneNo)){
            $phoneNo = trim($this->request->phoneNo);

            // Check is phone number is not equal to 14 digits
            // if its not equal to 14 digit generate Error code
            if(strlen($phoneNo) != 14){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(103);
                $response->setErrorMsg('Phone Number is Invalid');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            $phoneNo = str_split($phoneNo);
            $plusChar = $phoneNo[0];

            // Check is there plus sign into the phone number
            // if + sign is not present with phone number then generate error code
            if($plusChar != "+"){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(103);
                $response->setErrorMsg('Phone Number is Invalid');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            $countryCode = $phoneNo[1].$phoneNo[2];
            $operator    = $phoneNo[3].$phoneNo[4].$phoneNo[5];

            // Check for 88 country code exist at the start of phone number
            // if not found generate error code
            if($countryCode != '88'){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(103);
                $response->setErrorMsg('Phone Number is Invalid');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            // Check if number prefix is exist within the list of predefined prefix
            // if not found generate error
            if(!in_array($operator,array("017","019","018","015","016","011","013"),true)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(103);
                $response->setErrorMsg('Phone Number is Invalid');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }
        }

        $parentId = (!empty($this->request->parentId))? $this->request->parentId : 1;
        //$apiSetting = ApiSetting::find($parentId);
        $phoneNo = $this->request->phoneNo;
        
        
        $otp = $this->generateOTP();
        $domain = 'VIEWERS TV PASSCODE IS: ';
        $otpText = $domain. $otp;
        $apiMessage = "Confirm Code for Reset password link sent to your ";
        //$weblink = 'To Reset your web account password please go to '.$apiSetting->website.'#confirm-password';
        $user = null;
        
        if(!empty($phoneNo)){
            $user = User::where('username',$phoneNo)->where('parent_id',$parentId)->first();
        }
        
        if(empty($user)){
            $response = new Response($this->request->apiName);
            $response->setErrorCode(109);
            $response->setErrorMsg('No Account Found');
            return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
        }
        
        $user->otp = $otp;
        $user->reset_pass_expire = date('Y-m-d H:i:s',time()+(self::MINUTE*self::SECOND));
        $user->save();
        
        if(!empty($phoneNo)){
            
            try {
                $ssl = new SSL();
                $message = $otpText;
                $smsSentResponse = $ssl->sendSMS($phoneNo, $message);
                $smsPushLog = new SmsPushLog($smsSentResponse);
                $smsPushLog->save();
                $apiMessage.= " phone number ".$phoneNo;

            }catch(\Exception $essl){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(100);
                $response->setErrorMsg('Unable to sent OTP');
                $response->setDebugCode(100);
                $response->setDebugMsg($essl->getMessage());
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }
        }
        
        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'code' => 200,
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1,
            'message' => $apiMessage,
            'messageType' => 'VIEW'
        ));
        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
    }
    
    public function resetPassword()
    {
        if(empty($this->request->deviceType)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('Parameter deviceType is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
        if(empty($this->request->code)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(106);
            $response->setErrorMsg('Code is Required');
            $response->setDebugCode(100);
            $response->setDebugMsg('Parameter code is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
        if(empty($this->request->password)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(115);
            $response->setErrorMsg('Reset Password is Required');
            $response->setDebugCode(100);
            $response->setDebugMsg('Parameter password is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
        if(empty($this->request->confirmPassword)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(116);
            $response->setErrorMsg('Reset confirm password is Required');
            $response->setDebugCode(100);
            $response->setDebugMsg('Parameter confirmPassword is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
        if($this->request->password != $this->request->confirmPassword){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(117);
            $response->setErrorMsg('Password and Confirm Password is not matched');
            $response->setDebugCode(100);
            $response->setDebugMsg('Parameter confirmPassword is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
        if(strlen($this->request->password)<8){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(120);
            $response->setErrorMsg('Password length should not less than 8 characters');
            $response->setDebugCode(100);
            $response->setDebugMsg('Parameter confirmPassword is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key')); 
        }
        
        $user = User::where('otp',$this->request->code)->first();
        if(empty($user)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(108);
            $response->setErrorMsg('Invalid Confirm Code');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
        $apiMessage = "Your password successfully changed";
        if($user->otp == $this->request->code){
            $deviceType = DeviceType::find($this->request->deviceType);
            $currentTimestamp = time();
            $resetPassExpire  = strtotime($user->reset_pass_expire);
            if($resetPassExpire > $currentTimestamp){
                $newToken = $this->generateOTP();
                $oldToken = $user->iptv_token;
                $user->password = md5($this->request->password);
                $user->iptv_token = md5($newToken.time());
                $user->otp = null;
                $user->reset_pass_expire = null;
                $user->clientType = $deviceType->id;
                $user->save();
                
                // Set Memcached Data
                try{

                    $cacheDB = new CacheDB();
                    $cacheDB->pushToMemcached($user, $oldToken);

                }catch(\Exception $ec) {
                    $response = new Response($this->request->apiName);
                    $response->setStatus(1);
                    $response->setDebugCode(100);
                    $response->setDebugMsg($ec->getMessage());
                    return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
                }
                
            }else{
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(107);
                $response->setErrorMsg('Confirmation Code Expired');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key')); 
            }
        }else{
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(108);
            $response->setErrorMsg('Invalid Confirm Code');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'code' => 200,
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1,
            'message' => $apiMessage,
            'messageType' => 'VIEW'
        ));
        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        
    }
    
    
    public function changePassword()
    {
        if(empty($this->request->deviceType)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('Parameter deviceType is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
        if(empty($this->request->customerId)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(106);
            $response->setErrorMsg('customerId is Required');
            $response->setDebugCode(100);
            $response->setDebugMsg('Parameter code is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
       
        
        if(empty($this->request->oldPassword)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(115);
            $response->setErrorMsg('Old Password is Required');
            $response->setDebugCode(100);
            $response->setDebugMsg('Parameter Old password is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
        if(empty($this->request->newPassword)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(116);
            $response->setErrorMsg('newPassword is Required');
            $response->setDebugCode(100);
            $response->setDebugMsg('Parameter New Password is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
        if(strlen($this->request->newPassword)<8){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(120);
            $response->setErrorMsg('Password length should not less than 8 characters');
            $response->setDebugCode(100);
            $response->setDebugMsg('Password length should not less than 8 characters');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key')); 
        }
        
        $user = User::find($this->request->customerId);
        if(empty($user)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(108);
            $response->setErrorMsg('Invalid Confirm Code');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
        $apiMessage = "Your password successfully changed";
        if($user->password == md5(trim($this->request->oldPassword))){
            
            $deviceType = DeviceType::find($this->request->deviceType);

            $newToken = $this->generateOTP();
            $oldToken = $user->iptv_token;
            $user->password = md5($this->request->newPassword);
            $user->iptv_token = md5($newToken.time());
            $user->otp = null;
            $user->reset_pass_expire = null;
            $user->clientType = $deviceType->id;
            $user->save();

            // Set Memcached Data
            try{

                $cacheDB = new CacheDB();
                $cacheDB->pushToMemcached($user, $oldToken);

            }catch(\Exception $ec) {
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg($ec->getMessage());
                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            }

        }else{
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(121);
            $response->setErrorMsg('Sorry! Old Password not matched');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key')); 
        }
        
        
        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'code' => 200,
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1,
            'password' => $user->password,
            'message' => $apiMessage,
            'messageType' => 'VIEW'
        ));
        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        
    }

    /**
     * Generate Random OTP
     * range between 1 to 999999
     * @param int $len
     * @return null|string
     *
     */
    private function generateOTP($len=6)
    {
        $otp = null;
        for($i=0; $i<$len;$i++){
            $otp .= rand(1,9);
        }
        return $otp;
    }
}