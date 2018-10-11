<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Utils\Response;
class ScratchCardDetail extends Model{
    
    protected $table = "scratch_card_detail";
    
    public static function getCardBySerialAndCard($serialNo,$cardNo)
    {
        return DB::table('scratch_card_detail')
                ->select('scratch_card_detail.id','card_info_id','value','serial_no','card_no','scratch_card_detail.parent_id','group_id','lco_id')
                ->leftJoin('scratch_card_info','scratch_card_info.id','=','scratch_card_detail.card_info_id')
                ->where('card_no',$cardNo)->where('serial_no',$serialNo)
                ->where('scratch_card_detail.is_active',1)
                ->where('scratch_card_detail.is_suspended',0)
                ->where('scratch_card_detail.is_used',0)
                ->where('scratch_card_detail.subscriber_id',0)
                ->first();
    }
    
    public static function setAsUsedCard($user,$scratchCard)
    {
        $obj = self::find($scratchCard->id);
        $obj->subscriber_id = $user->id;
        $obj->is_used = 1;
        $obj->save();
        return true;
    }
    
    public static function isAuthorized($user,$scratchCard){
        if(empty($scratchCard)){
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg("Scratch Card Info empty");
            return $response;
        }
        if(($user->parent_id != $scratchCard->group_id) && ($user->parent_id != $scratchCard->lco_id)){
            $response = new Response();
            $response->setStatus(1);
            $response->setErrorCode(111);
            $response->setErrorMsg("Card No and Serial No combination doesn't match with your Service Provider");
            return $response;
        }
        return true;
    }
    
}
