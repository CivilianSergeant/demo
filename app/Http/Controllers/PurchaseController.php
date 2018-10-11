<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/24/2016
 * Time: 12:13 PM
 */

namespace App\Http\Controllers;


use App\Entities\PaymentGateways\PaymentGatewayManager;
use App\Entities\Purchase\PurchaseManager;
use App\Entities\PaymentGateways\PaymentGateway;
use App\Entities\UserPackage;
use App\Entities\UserProgram;
use App\User;
use App\Utils\AES_Engine;
use App\Utils\Response;
use App\Utils\Validation\PurchaseContentValidation;
use Illuminate\Support\Facades\Config;

class PurchaseController extends RestController
{


    public function index()
    {
        if (!empty($this->request)) {

            if (!empty($this->request->apiName)
                && !method_exists($this, $this->request->apiName)
            ) {

                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(100);
                $response->setErrorMsg('Api parameter apiName is missing');
                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            }

            try {
                $apiName = $this->request->apiName;
                return $this->$apiName();
            } catch (\Exception $ex) {
                $response = new Response();
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg($ex->getMessage() . 'Line:' . $ex->getLine().', File:'.$ex->getFile());
                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            }

        } else {
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg("Request no found");
            return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
        }
    }

    public function purchasePackageByWallet()
    {
        
        $purchaseContentValidate  = new PurchaseContentValidation($this->request);
        try{
            //Validate Purchase Request
            
            $isValid = $purchaseContentValidate->validate();
            if($isValid instanceof Response){
                return AES_Engine::getEncrypt($isValid, Config::get('app.encryption_key'));
            }
            $user = $isValid->getUser();
            $package = $isValid->getPackage();

            
            //Inquiry Subscription
            $subscribedPackage = UserPackage::isPackageSubscribed($user, $package);
            $duration = $package->duration;
            $currentTimestamp = time(); 
            $startDate = date("Y-m-d",$currentTimestamp);
            $startDateObject = new \DateTime($startDate);
            
            if(!empty($subscribedPackage)){
                
                $expireTimestamp = strtotime($subscribedPackage->package_expire_date);
                if($expireTimestamp > $currentTimestamp){
                    
                    $expireDate = $subscribedPackage->package_expire_date;
                    $expireDateObject = new \DateTime(substr($expireDate, 0, 10));
                    
                    $expireDiff = date_diff($startDateObject, $expireDateObject);
                    
                    $duration += $expireDiff->days;
                }
            }
            
            //Inquiry Balance
            $balance = $user->getBalance();
            if(!PurchaseManager::isBalanceAvailable($balance,$package->price)){
                $response = new Response();
                $response->setStatus(1);
                $response->setErrorCode(119);
                $response->setErrorMsg("Not Enough Balance");
                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            }
            
            
            
            // Debit Balance
            $paymentGateway = PaymentGatewayManager::getPaymentGateway($this->request);
            $paymentModel   = PaymentGatewayManager::getPaymentModel($this->request->paymentMethod);
            
            $paymentModel->setPurchasePackageModel($user, $package, $balance, $currentTimestamp);
            $paymentGateway->setPaymentModel($paymentModel);
            
            $transaction = $paymentGateway->debit();
            
            // Subscribe package
            if(!empty($transaction->id)){
                $subscription = PurchaseManager::subscribePackage($user, $package, $duration, $startDateObject, $currentTimestamp);

                if(!empty($subscribedPackage)){
                    $subscribedPackage->delete();
                }
            }
            
            
            // sent response
            $response = new Response();
            $response->setResponse([
               "notification"=> true,
               "notificationType"=> 1,
               "ads"=> true,
               "adsType"=> 1,
               "balance"=> $transaction->balance, 
               "transaction" => [
                   'id'        => $transaction->id,
                   'packageId' => $transaction->package_id,
                   'price'     => $transaction->debit,
                   'packageStartDate'    => $subscription->package_start_date,
                   'packageExpireDate'   => $subscription->package_expire_date,
                   'duration'            => $subscription->no_of_days,
                   'transactionDate'     => $transaction->transaction_date
                ],
                'message' => 'Package was sucssfully subscribed',
                'messageType' => 'TOAST'
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
    
    public function purchaseContentByWallet()
    {
        
        $purchaseContentValidate  = new PurchaseContentValidation($this->request);
        try{
            //Validate Purchase Request
            
            $isValid = $purchaseContentValidate->validate();
            if($isValid instanceof Response){
                return AES_Engine::getEncrypt($isValid, Config::get('app.encryption_key'));
            }
            $user = $isValid->getUser();
            $content = $isValid->getContent();

            
            //Inquiry Subscription
            //$subscribedPackage = UserPackage::isPackageSubscribed($user, $package);
            $subscribedContent = UserProgram::isProgramSubscribed($user, $content);
            $duration = 0;
            $currentTimestamp = time(); 
            date_default_timezone_set('Asia/Dhaka');
            $startDate = date("Y-m-d",$currentTimestamp);
            $startDateObject = new \DateTime($startDate);
            
            if(!empty($subscribedContent)){
                
                $expireTimestamp = strtotime($subscribedContent->program_expire_date);
                if($expireTimestamp > $currentTimestamp){
                    
                      $remainSeconds = ($expireTimestamp - $currentTimestamp);
                      $remainDuration = gmdate('H:i:s',$remainSeconds);
                      $time = explode(":",$remainDuration);
                      $hour = (!empty($time[0]))? $time[0] : 0;
                      $min  = (!empty($time[1]))? $time[1] : 0;
                      $sec  = (!empty($time[2]))? $time[2] : 0;
                    
                      $duration = ($hour * 3600) + ($min * 60) + $sec;
                }
            }
            
            //Inquiry Balance
            $balance = $user->getBalance();
            if(!PurchaseManager::isBalanceAvailable($balance,$content->individual_price)){
                $response = new Response();
                $response->setStatus(1);
                $response->setErrorCode(119);
                $response->setErrorMsg("Not Enough Balance");
                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            }

            // Debit Balance
            $paymentGateway = PaymentGatewayManager::getPaymentGateway($this->request);
            
            $paymentModel   = PaymentGatewayManager::getPaymentModel($this->request->paymentMethod);
            
            $paymentModel->setPurchaseProgramModel($user, $content, $balance, $currentTimestamp);
            $paymentGateway->setPaymentModel($paymentModel);
            
            $transaction = $paymentGateway->debit();
            //$response = new Response($transaction);
            // return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            // Subscribe package
            if(!empty($transaction->id)){
                $subscription = PurchaseManager::subscribeProgram($user, $content, $duration, $currentTimestamp);

                if(!empty($subscribedContent)){
                    $subscribedContent->delete();
                }
            }
            
            
            // sent response
            $response = new Response();
            $response->setResponse([
               "notification"=> true,
               "notificationType"=> 1,
               "ads"=> true,
               "adsType"=> 1,
               "balance"=> $transaction->balance, 
               "transaction" => [
                   'id'        => $transaction->id,
                   'contentId' => $transaction->package_id,
                   'price'     => $transaction->debit,
                   'programStartDate'    => $subscription->program_start_date,
                   'programExpireDate'   => $subscription->program_expire_date,
                   'duration'            => $subscription->duration,
                   'transactionDate'     => $transaction->transaction_date
                ],
                'message' => 'Content was sucssfully subscribed',
                'messageType' => 'TOAST'
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