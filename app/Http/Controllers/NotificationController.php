<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12/18/2016
 * Time: 4:08 PM
 */

namespace App\Http\Controllers;


use App\Entities\FcmToken;
use App\Utils\AES_Engine;
use App\Utils\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Utils\FcmUtil;
use App\Entities\FcmDeviceGroup;

class NotificationController extends RestController
{
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
            $response->setDebugMsg('Request not found');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

    public function setFcmToken()
    {
        if(empty($this->request->token)){
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('FCM Token attribute is empty');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        
        $fcmToken = FcmToken::where(['fcm_token'=>$this->request->token])->first();
        if(!empty($fcmToken)){
            if($fcmToken->user_id == 0){
                if($this->request->customerId>0){
                    $fcmToken->user_id = $this->request->customerId;
                    $fcmToken->logout_flag = 0;
                } else{
                    $fcmToken->user_id = 0;
                }
//                $fcmToken->user_id = ($this->request->customerId>0)? $this->request->customerId : 0;
                
            }else{
                $fcmToken->fcm_token = $this->request->token;
                $fcmToken->logout_flag = 0;
                
            }
            
        }else{  // when record not found by fcm token

            if($this->request->customerId > 0){ // if user id exist in request
                $fcmToken = FcmToken::where(['user_id'=>$this->request->customerId])->first();
                
                if(!empty($fcmToken)){ // if record found
                    $fcmToken->fcm_token = $this->request->token;
                }else{
                    // for new token for new user with user id defined
                    $fcmToken = new FcmToken(array('fcm_token'=>$this->request->token,'user_id'=>$this->request->customerId));
                }
                $fcmToken->logout_flag=0;
            }else{
                // for new token for new user without user id defined
                $fcmToken = new FcmToken(array('fcm_token'=>$this->request->token,'user_id'=>0));
            }
        }
        
        
        
        $deviceGroup = FcmDeviceGroup::where('count','<',  FcmDeviceGroup::DEVICE_LIMIT)->first();
        //return AES_Engine::getEncrypt(new Response($deviceGroup),Config::get('app.encryption_key'));
        if(!empty($deviceGroup)){
                        
            $foundToken = FcmToken::where('fcm_token',$fcmToken->fcm_token)->where('user_id',$fcmToken->user_id)->first();
            
            $fcmResponse = FcmUtil::addToDeviceGroup($deviceGroup->group_name, $deviceGroup->fcm_device_group, [$fcmToken->fcm_token]);

            if($fcmResponse instanceof \stdClass && !empty($fcmResponse->notification_key)){
                if($fcmToken->user_id == 0){
                    $deviceGroup->count += 1;
                }
                $fcmToken->device_group_id = $deviceGroup->id;
            }

            $deviceGroup->save();
            

        }else{
            $countDeviceGroup = FcmDeviceGroup::count();
            $countDeviceGroup++;

            $newDeviceGroup = new FcmDeviceGroup();
            $newDeviceGroup->group_name = 'VEL_BOX_'.$countDeviceGroup;

            $fcmResponse = FcmUtil::createGroup($newDeviceGroup->group_name, [$fcmToken->fcm_token]);

            if(!empty($fcmResponse->notification_key)){
                $newDeviceGroup->fcm_device_group = $fcmResponse->notification_key; // here add new fcm notificationKey
                $newDeviceGroup->count = 1;
                $newDeviceGroup->save();
                $fcmToken->device_group_id = $newDeviceGroup->id;
            }

        }
        
        $fcmToken->save();
        
        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'code'=>200,
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1,
            'message' => 'Token Register successfully',
            'messageType' => 'NONE'
        ));
        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

    }
    
    public function sendNotification($token, $payload){
        $url = "https://fcm.googleapis.com/fcm/send";
        $fields = array(
            'to' => $token,  //"/topics/test",//$tokens,
            'data' => $payload
        );
        $headers = array(
            'Authorization:key=AAAA0DnS4uY:APA91bHKg8YgqL0oybwlz-C1B5y3b2AkTOPI7sfJ6RC2iGwpJ79M9LcpHW1RgxXTsqgMeeTj2QQ_IWA8QUBlvU3Q3_7fxPUgFRTdrCLIesvicBtepe3O9arxjuo383ryaMWbubnh-AVtheTpvVu3K1nB810277UkjA',
            'Content-Type:application/json'
        );
        
        $result = $this->_sendFCMRequest($url, $headers, $fields);
        return $result;
    }
    
    private function _sendFCMRequest($url,$headers,$fields)
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));
        $result = curl_exec($ch);
        if($result === FALSE){
            throw new \Exception('CURL FAILED '. curl_error($ch));
        }

        curl_close($ch);
        return $result;
    }

}