<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/17/2016
 * Time: 5:14 PM
 */

namespace App\Http\Controllers;



use App\Entities\PaymentGateways\Gateways\Bkash;
use App\Utils\AES_Engine;
use App\Utils\Response;
use Illuminate\Support\Facades\Config;

class PaymentGatewayController extends RestController
{
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

    public function getBkashInfo()
    {
        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'code' => 200,
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1,
            'bkashInfo' => (new Bkash())->getInfo()
        ));
        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
    }

    public function getBkashVerification()
    {
        $response = new Response($this->request->apiName);
        $response->setResponse(array(
           'code' => 200,
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1
        ));
        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
    }

    
}