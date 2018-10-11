<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Utils\Validation;
use App\Utils\Response;
use App\Entities\ScratchCardDetail;
class ScratchCardValidation extends Validation{
    
    private $scratchCard;


    public function __construct($request) {
        parent::__construct($request);
    }
    
    public function validate()
    {   
        $isValid = $this->validateSerialAndCardNo();
        if($isValid != null){
            return $isValid;
        }
        
        $isCardExist = $this->validateScratchCard();
        if($isCardExist != null){
            return $isCardExist;
        }
        return null;
    }
    
    private function validateSerialAndCardNo()
    {
        if(empty($this->request->serialNo)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter serialNo is missing');
            return $response;
        }

        if(empty($this->request->cardNo)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter cardNo is missing');
            return $response;
        }

        return null;
        
    }
    
    private function validateScratchCard()
    {
       
        $this->scratchCard = ScratchCardDetail::getCardBySerialAndCard($this->request->serialNo, $this->request->cardNo);
        if(empty($this->scratchCard)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(120);
            $response->setErrorMsg("Card number or Serial number not valid");
            return $response;
        }

        return null;
    }
    
    
    
    public function getScratchCard()
    {
        return $this->scratchCard;
    }
     
}
