<?php
/**
 * Description of OrderController
 *
 * @author Himel
 */
namespace App\Http\Controllers;


use App\Utils\Validation\PurchaseContentValidation;
use App\Entities\Order\CustomerOrder;
use App\Utils\AES_Engine;
use App\Utils\Response;
use App\Entities\Purchase\PurchaseManager;
use Illuminate\Support\Facades\Config;
class OrderController extends RestController{
    
    public function index()
    {
        if(!empty($this->request)){

            if(!empty($this->request->apiName)
                && !method_exists($this,$this->request->apiName)){

                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(100);
                $response->setErrorMsg('Api parameter apiName missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            try {
                $apiName = $this->request->apiName;
                
                return $this->$apiName();
            }catch(\Exception $ex){
                $response = new Response();
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg($ex->getMessage().'Line:'.$ex->getLine());
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
    
    public function getPackageOrderId()
    {
        
    }
    
    public function getContentOrderId()
    {
        $purchaseContentValidate  = new PurchaseContentValidation($this->request);
        try{
            
            $isValid = $purchaseContentValidate->validate();
            if($isValid instanceof Response){
                return AES_Engine::getEncrypt($isValid, Config::get('app.encryption_key'));
            }
            
            $user = $isValid->getUser();
            $content = $isValid->getContent();
            
            $balance = $user->getBalance();
            if(!PurchaseManager::isBalanceAvailable($balance,$content->individual_price)){
                $response = new Response();
                $response->setStatus(1);
                $response->setErrorCode(119);
                $response->setErrorMsg("Not Enough Balance");
                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            }
            
            $debitedAmount = $content->individual_price;
            $itemType      = CustomerOrder::ITEM_TYPE_PROGRAM;
            $transactionId = "TRS-".$user->id.$content->id.date("mdyhis");
            
            $customerOrder = new CustomerOrder();
            
            $customerOrder->transaction_id     = $transactionId;
            $customerOrder->customer_id        = $user->id;
            $customerOrder->item_id            = $content->id;
            $customerOrder->item_type          = $itemType; 
            $customerOrder->amount             = $debitedAmount;
            $customerOrder->transaction_status = CustomerOrder::TRS_STATUS_NEW;
            $successUrl = null;
            
            if($content instanceof \App\Entities\Program){
                $successUrl = "purchase-content-by-".strtolower($this->request->paymentMethod);
            }else{
                $successUrl = "purchase-package-by-".strtolower($this->request->paymentMethod);
            }
            
            $cancelUrl = "cancel-order";
            $failUrl   = "fail-order";
            
            $customerOrder->success_url        = $successUrl;
            $customerOrder->cancel_url         = "cancel-url";
            $customerOrder->fail_url           = "fail-url";
            $customerOrder->store_id           = "12";
            $customerOrder->save();
            
            $response = new Response($this->request->apiName);
            $response->setResponse([
               "notification"     => true,
               "notificationType" => 1,
               "ads"              => true,
               "adsType"          => 1,
               "balance"          => $balance, 
               "transactionId"    => $customerOrder->transaction_id,
               "chargedAmount"    => $customerOrder->amount,
               "success_url"      => $successUrl,
               "cancel_url"       => $cancelUrl,
               "fail_url"         => $failUrl  
            ]);
            
            return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            
        }catch(\Exception $ex){
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().', Line:'.$ex->getLine().', File:'.$ex->getFile());
            return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
        }
    }
}
