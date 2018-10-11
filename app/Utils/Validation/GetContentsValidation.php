<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Utils\Validation;

use App\Utils\Response;
/**
 * Description of GetContentsValidation
 *
 * @author Himel
 */
class GetContentsValidation extends Validation{
    
    public function __construct($request)
    {
        parent::__construct($request);
    }
    
    public function validate() {
        
        $validateContentType = $this->validateContentType();
        if($validateContentType != null){
            return $validateContentType;
        }
        
        $validateUserCredential = $this->validateUserCredential();
        if($validateUserCredential != null){
            return $validateUserCredential;
        }
    }
    
    public function validateUser($user)
    {
        if(empty($user)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(109);
            $response->setErrorMsg('No Account Found');
            return $response;
        }

        if($user->id == 1){  // send this reponse if user id == 1 , checking is it superadmin account
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg("Access Forbidden");
            return $response;
        }
        return null;
        
    }
    
    public function validateContentType()
    {
        if(empty($this->request->type)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter type missing');
            return $response;
        }
        
        $typeValues = array('LIVE','VOD','CATCHUP','NULL');
        if(!in_array($this->request->type,$typeValues,true)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('type values must be one of this ['.implode(',',$typeValues).']');
            return $response;
        }
        
        return null;
    }
    
    public function validateUserCredential()
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
    
    

}
