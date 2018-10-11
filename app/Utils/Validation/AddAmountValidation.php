<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Utils\Validation;
use App\Entities\PaymentGateways\PaymentGatewayManager;
use App\User;
use App\Utils\Response;
class AddAmountValidation extends Validation{
    
    protected $user;
    protected $paymentMethod;
    
    public function __construct($request) {
        parent::__construct($request);
    }
    
    public function validate() {
        
        $isValidUser = $this->validateUser();
        if($isValidUser != null){
            return $isValidUser;
        }
        
        $isPaymentMethodValid = $this->validatePaymentMethod();
        if($isPaymentMethodValid != null){
            return $isPaymentMethodValid;
        }
        
        if($this->request->paymentMethod == PaymentGatewayManager::SCRATCH_CARD){
            
            $scratchCardValidate = new ScratchCardValidation($this->request);
            $response = $scratchCardValidate->validate();
            if($response instanceof Response){
                return $response;
            }
            $this->paymentMethod = $scratchCardValidate;
        }
        return $this;
    }
    
    public function validatePaymentMethod()
    {
        
        if(empty($this->request->paymentMethod)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugCode('Payment Method should not empty');
            return $response;
        }
        $paymentMethods = [PaymentGatewayManager::SCRATCH_CARD,  PaymentGatewayManager::VISA_CARD,  
            PaymentGatewayManager::MT_CHARGE, PaymentGatewayManager::MASTER_CARD,  PaymentGatewayManager::BKASH];
        
        if(!in_array($this->request->paymentMethod, $paymentMethods,true)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('Payment should be one of these ['. implode(',',$paymentMethods).']');
            return $response;
        }
    }
    
    public function validateUser()
    {
        $credValid = $this->validateCredentials();
        if($credValid != null)
        {
            return $credValid;
        }
        // Checking is User account exist and valid
        $this->user = User::where('id',$this->request->customerId)->where('password',$this->request->password)->first();
        if(empty($this->user)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(109);
            $response->setErrorMsg('Account Not Found');
            return $response;
        }

        if($this->user->id == 1){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('Access Forbidden');
            return $response;
        }
        return null;
    }
    
    public function validateCredentials()
    {
        if(empty($this->request->customerId)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter customerId is missing');
            return $response;
        }

        if(empty($this->request->password)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter password is missing');
            return $response;
        }
        return null;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }
    
    
    
    

}
