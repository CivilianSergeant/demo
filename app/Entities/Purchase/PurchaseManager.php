<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entities\Purchase;

use App\Entities\PaymentGateways\ScratchCard;
use App\Entities\PaymentGateways\Gateways\Wallet;
use App\Entities\UserPackage;
use App\Entities\UserProgram;
class PurchaseManager {
    
//    public static function debitBalance($paymentGateway)
//    {
//        try{
//            if($paymentGateway instanceof ScratchCard){

                // credit scratch card amount to user balance
    //            $transactionStatus = $paymentGateway->credit();
    //            if(!empty($transactionStatus->getStatus())){
    //                // Update Scratch Card Info
    //                
    //                
    //                $debited = $paymentGateway->debit($data);
    //            }
//            }else if ($paymentGateway instanceof Wallet){
//
//                return $paymentGateway->debit();
//            }
//        }catch(\Exception $ex){
//            return new \App\Utils\Response($ex->getMessage());
//        }
//    }
    
    public static function isBalanceAvailable($balance,$price)
    {
        if($balance >= $price){
            return true;
        }
        
        return false;
    }
    
    
    public static function subscribePackage($user,$package,$duration,$startDateObject,$currentTimestamp)
    {
        date_default_timezone_set('Asia/Dhaka');
        $newSubscription = new UserPackage();
        $newSubscription->user_id =  $user->id;
        $newSubscription->package_id = $package->id;
        $newSubscription->status = 1;
        $newSubscription->package_start_date = date("Y-m-d H:i:s",$currentTimestamp);
        $startDateObject->add(new \DateInterval('P' . $duration . 'D'));
        $expireDate = date("Y-m-d 23:59:59",$startDateObject->getTimestamp());
        $newSubscription->package_expire_date = $expireDate;
        $newSubscription->no_of_days = $duration;
        $newSubscription->user_package_type_id = 1;
        $newSubscription->parent_id = $user->parent_id;
        $newSubscription->created_by = $user->created_by;
        $newSubscription->save();
        return $newSubscription;
    }
    
    public static function subscribeProgram($user,$content,$duration,$currentTimestamp)
    {
        date_default_timezone_set('Asia/Dhaka');
        $newSubscription = new UserProgram();
        $newSubscription->user_id =  $user->id;
        $newSubscription->program_id = $content->id;
        $newSubscription->status = 1;
        $newSubscription->program_start_date = date("Y-m-d H:i:s",$currentTimestamp);
        $time = explode(":",$content->duration);
        $hour = (!empty($time[0]))? $time[0] : 0;
        $min  = (!empty($time[1]))? $time[1] : 0;
        $sec  = (!empty($time[2]))? $time[2] : 0;
        
        $totalSeconds = ($hour*(3600)) + ($min*60) + $sec + $duration;

        $expireDate = date("Y-m-d H:i:s",$currentTimestamp+$totalSeconds);
        $newSubscription->program_expire_date = $expireDate;
        $newSubscription->duration = gmdate('H:i:s',$totalSeconds);
        $newSubscription->parent_id = $user->parent_id;
        $newSubscription->created_by = $user->created_by;
        $newSubscription->save();
        return $newSubscription;
    }
}
