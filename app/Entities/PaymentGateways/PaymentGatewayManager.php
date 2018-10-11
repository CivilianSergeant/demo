<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/22/2016
 * Time: 1:47 PM
 */

namespace App\Entities\PaymentGateways;

use App\User;
use App\Utils\Response;
use Illuminate\Support\Facades\DB;
use App\Entities\PaymentGateways\Gateways\Wallet;
use App\Entities\PaymentGateways\GatewayModels\WalletModel;
use App\Entities\PaymentGateways\Gateways\ScratchCard;
use App\Entities\PaymentGateways\GatewayModels\ScratchCardModel;

class PaymentGatewayManager
{
    const SCRATCH_CARD = 'SCRATCHCARD';
    const BKASH        = 'BKASH';
    const MT_CHARGE    = 'MTCHARGE';
    const VISA_CARD    = 'VISACARD';
    const MASTER_CARD  = 'MASTERCARD';
    const WALLET       = 'WALLET';

    public static function getPaymentGateway($request)
    {
        
        if($request->paymentMethod == self::SCRATCH_CARD){
            
            return new ScratchCard($request);
        }else if($request->paymentMethod == self::BKASH){
            return new Bkash($request);
        }else if($request->paymentMethod == self::WALLET){
            return new Wallet();
        }
    }
    
    public static function getPaymentModel($paymentMethod)
    {
        if($paymentMethod == self::WALLET){
            return new WalletModel();
        }else if($paymentMethod == self::SCRATCH_CARD){
            return new ScratchCardModel();
        }
    }

}