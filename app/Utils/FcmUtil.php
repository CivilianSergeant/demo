<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Utils;

/**
 * Description of FcmUtil
 *
 * @author Himel
 */
class FcmUtil {
    
    const API_KEY = 'AAAA0DnS4uY:APA91bHKg8YgqL0oybwlz-C1B5y3b2AkTOPI7sfJ6RC2iGwpJ79M9LcpHW1RgxXTsqgMeeTj2QQ_IWA8QUBlvU3Q3_7fxPUgFRTdrCLIesvicBtepe3O9arxjuo383ryaMWbubnh-AVtheTpvVu3K1nB810277UkjA';
    const SENDER_ID = '894323319526';
    
    public static function sendNotification($token, $payload){
        $url = "https://fcm.googleapis.com/fcm/send";
        $fields = array(
            'to' => $token,  //"/topics/test",//$tokens,
            'data' => $payload
        );
        $headers = array(
            'Authorization:key='.self::API_KEY,
            'Content-Type:application/json'
        );
        
        $result = self::_sendFCMRequest($url, $headers, $fields);
        return json_decode($result);
    }
    
    public static function createGroup($groupName,$deviceTokens)
    {
        $url = "https://android.googleapis.com/gcm/notification";
        $fields = array(
            'operation' => "create",
            'notification_key_name' => $groupName,
            'registration_ids' => $deviceTokens
        );
        $headers = array(
            'Authorization:key='.self::API_KEY,
            'project_id:'.self::SENDER_ID,
            'Content-Type:application/json'
        );
        
        $result = self::_sendFCMRequest($url, $headers, $fields);
        return json_decode($result);
        
    }
    
    public static function addToDeviceGroup($groupName,$notificationToken,$deviceTokens)
    {
        $url = "https://android.googleapis.com/gcm/notification";
        $fields = array(
            'operation' => "add",
            'notification_key_name' => $groupName,
            'notification_key' => $notificationToken,
            'registration_ids' => $deviceTokens
        );
        
        $headers = array(
            'Authorization:key='.self::API_KEY,
            'project_id:'.self::SENDER_ID,
            'Content-Type:application/json'
        );
        
        $result = self::_sendFCMRequest($url, $headers, $fields);
        return json_decode($result);
        
    }
    
    private static function _sendFCMRequest($url,$headers,$fields)
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));
        $result = curl_exec($ch);
        if($result === FALSE){
            throw new \Exception('CURL FAILED '. curl_error($ch));
        }

        curl_close($ch);
        return $result;
    }
}
