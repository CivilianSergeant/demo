<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/23/2016
 * Time: 11:19 AM
 */

namespace App\Http\Controllers;


use App\User;
use App\Utils\AES_Engine;
use App\Utils\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SubscriberController extends RestController
{
    public function index()
    {
        if (!empty($this->request)) {

            if(!empty($this->request->apiName)
                && !method_exists($this,$this->request->apiName)){

                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(100);
                $response->setErrorMsg('Api parameter apiName missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            if(empty($this->request->customerId)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('API Parameter customerId is missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            if(empty($this->request->password)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('API Parameter password is missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            try {
                $apiName = $this->request->apiName;
                return $this->$apiName();
            }catch(\Exception $ex){
                $response = new Response();
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }
        }else{
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg("Request no found");
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

    /**
     * This method will call during
     * Get Subscriber profile API request for following url
     * http:// [ ServerIp]: [serverPort]/subscriber-profile
     * @return string
     */
    public function getSubscriberProfile()
    {

        $user  = User::select('id as customer_id','username','profile_id','email','lat','lon','telco_id','service_operator_type','registration_type')->where('id',$this->request->customerId)
                       ->where('password',$this->request->password)
                       ->where('is_remote_access_enabled',0)
                       ->where('user_type',User::SUBSCRIBER)->first();
        if(!empty($user)){

            $result = DB::select("select if(GetSubscriberBal(" . $user->customer_id . ") IS NOT NULL,GetSubscriberBal(" . $user->customer_id . "),0) as balance");

            $user->getProfile;
            $response = new Response($this->request->apiName);
            $response->setResponse(array(
                'code'=>200,
                'notification' => true,
                'notificationType' => 1,
                'ads' => true,
                'adsType' => 1,
                'customer'=> $user,
                'balance' => (!empty($result) && !empty($result[0]->balance))? $result[0]->balance : 0
            ));
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }else{
            $response = new Response();
            $response->setStatus(1);
            $response->setErrorCode(109);
            $response->setErrorMsg("Sorry! No Account Found");
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

    }

    /**
     * This method will call during
     * Set Subscriber profile API request for following url
     * http:// [ ServerIp]: [serverPort]/subscriber-profile-update
     * @return string
     */
    public function subscriberProfileUpdate()
    {
        $user  = User::select('id','username','profile_id','email','lat','lon','telco_id','service_operator_type','registration_type')
            ->where('id',$this->request->customerId)
            ->where('password',$this->request->password)
            ->where('is_remote_access_enabled',0)
            ->where('user_type',User::SUBSCRIBER)->first();
        if(!empty($user)){

            $email = filter_var($this->request->email,FILTER_VALIDATE_EMAIL);

            if($email == FALSE){
                $response = new Response();
                $response->setStatus(1);
                $response->setErrorCode(110);
                $response->setErrorMsg("Email not valid");
                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            }

            if($user->registration_type != User::REGTYPE_EMAIL && $user->registration_type != User::REGTYPE_BOTH){
                if(!empty($email) && $user->email != $email) {
                    $emailExist = User::where('email', $email)->first();

                    if (empty($emailExist)) {
                        $user->email = $email;
                        $user->save();
                    } else {
                        $response = new Response();
                        $response->setStatus(1);
                        $response->setErrorCode(110);
                        $response->setErrorMsg("Email not available");
                        return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
                    }
                }
            }

            try {
                $subscriberProfile = $user->getProfile;
                if($user->registration_type != User::REGTYPE_PHONE && $user->registration_type != User::REGTYPE_BOTH){
                    $subscriberProfile->contact = $this->request->phoneNo;
                }
                $subscriberProfile->subscriber_name = $this->request->fullname;
                $subscriberProfile->address1 = $this->request->address;
                $subscriberProfile->save();

                $response = new Response($this->request->apiName);
                $response->setResponse(array(
                    'code' => 200,
                    'notification' => true,
                    'notificationType' => 1,
                    'ads' => true,
                    'adsType' => 1,
                    'systemTime' => date('YmdHis'),
                    'message' => 'Information Successfully updated',
                    'messageType' => 'TOAST'
                ));
                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            }catch(\Exception $ex){
                $response = new Response();
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

        }else{
            $response = new Response();
            $response->setStatus(1);
            $response->setErrorCode(109);
            $response->setErrorMsg("Sorry! No Account Found");
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

    }
}