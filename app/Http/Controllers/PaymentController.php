<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Http\Controllers;

use App\Entities\ScratchCardDetail;
use App\Entities\PaymentGateways\PaymentGatewayManager;
use App\Entities\PaymentGateways\PaymentGateway;
use App\Utils\AES_Engine;
use App\Utils\Validation\AddAmountValidation;
use App\Utils\Response;

use Illuminate\Support\Facades\Config;

class PaymentController extends RestController {
    
    public function index()
    {
        if (!empty($this->request)) {

            if (empty($this->request->apiName)
                && !method_exists($this, $this->request->apiName)
            ) {

                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(100);
                $response->setErrorMsg('Api parameter apiName missing');
                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            }


            try {
                $apiName = $this->request->apiName;
                return $this->$apiName();
            }catch(\Exception $ex){
                $response = new Response();
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg($ex->getMessage().', Line:'.$ex->getLine());
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
    
    public function addAmountByScratchCard()
    {
        // validate request
        $validateRequest = new AddAmountValidation($this->request);
        
        $isValid = $validateRequest->validate();
        if($isValid instanceof Response){
            return AES_Engine::getEncrypt($isValid, Config::get('app.encryption_key'));
        }
        
        $user = $isValid->getUser();
        $scratchCard = $isValid->getPaymentMethod()->getScratchCard();
        
        // Inquiry Scratch Card Authorized
        $isAuthorized = ScratchCardDetail::isAuthorized($user, $scratchCard);
        if($isAuthorized instanceof Response){
            return AES_Engine::getEncrypt($isAuthorized, Config::get('app.encryption_key'));
        }
        
        // credit balance
        $paymentGateway = PaymentGatewayManager::getPaymentGateway($this->request);
        $paymentModel   = PaymentGatewayManager::getPaymentModel($this->request->paymentMethod);
        $paymentModel->getCreditModel($user, $scratchCard);
        $paymentGateway->setModel($paymentModel);
        $transaction = $paymentGateway->credit();
        
        // set scratch card as used
        ScratchCardDetail::setAsUsedCard($user,$scratchCard);
        
        
        $response = new Response($this->request->apiName);
        $response->setResponse([
            'code' =>  200,
            'notification'=>true,
            'notificationType'=>1,
            'ads'=>true,
            'adsType'=>1,
            'balance' => $transaction->balance,
            'transaction' => $transaction,
            'message'=>'Amount added succesfully',
            'messageType'=>'TOAST'
                
        ]);
        
        return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
        
    }
}
