<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/24/2016
 * Time: 12:39 PM
 */

namespace App\Utils\Validation;


use App\Entities\PaymentGateways\PaymentGatewayManager;
use App\Entities\Program;
use App\Entities\Package;
use App\User;
use App\Utils\Response;


class PurchaseContentValidation extends Validation
{
    protected $user;
    protected $package;
    protected $content;
    
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function validate()
    {
        
        // validate user 
        $validateUser = $this->validateUser();
        if($validateUser != null){
            return $validateUser;
        }
        if(!empty($this->request->packageId)){
           $response = $this->validatePackageId();
        }else if(!empty($this->request->contentId)){
           $response = $this->validateContentId();
        }
        if($response != null){
            return $response;
        }
        
        $paymentMethod = $this->validatePaymentMethod();
        if($paymentMethod != null){
            return $paymentMethod;
        }
        return $this;
    }
    
    public function validateDeviceType()
    {
        if(empty($this->request->deviceType)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter deviceType is missing');
            return $response;
        }
        return null;
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
    
    public function validateContentId()
    {
        if(empty($this->request->contentId)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter contentId is missing');
            return $response;
        }

        $this->content = Program::find($this->request->contentId);
        if($this->content->individual_price <= 0){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(100);
            $response->setErrorMsg('Sorry! The Content is not allowed for individual purchase');
            $response->setDebugCode(100);
            $response->setDebugMsg('Sorry! The Content is not allowed for individual purchase');
            return $response;
        }
        
        if(empty($this->content)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(110);
            $response->setErrorMsg('Sorry! Content not found');
            $response->setDebugCode(100);
            $response->setDebugMsg('Sorry! Content not found');
            return $response;
        }
        return null;
    }
    
    public function validatePackageId()
    {
        if(empty($this->request->packageId)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter packageId is missing');
            return $response;
        }

        $this->package = Package::find($this->request->packageId);
        if(empty($this->package)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(110);
            $response->setErrorMsg('Sorry! Package not found');
            $response->setDebugCode(100);
            $response->setDebugMsg('Sorry! Package not found');
            return $response;
        }
        return null;
    }
    
    public function validatePaymentMethod()
    {
        if(empty($this->request->paymentMethod)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter paymentMethod is missing');
            return $response;
        }
        
        $paymentMethods = [PaymentGatewayManager::SCRATCH_CARD,
            PaymentGatewayManager::BKASH,PaymentGatewayManager::MT_CHARGE,
            PaymentGatewayManager::MASTER_CARD,PaymentGatewayManager::VISA_CARD,PaymentGatewayManager::WALLET];

        if(!in_array($this->request->paymentMethod,$paymentMethods,true)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter paymentMethod should be one of ['.implode(',',$paymentMethods).']');
            return $response;
        }
        return null;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function getPackage()
    {
        return $this->package;
    }
    
    public function getContent()
    {
        return $this->content;
    }
}