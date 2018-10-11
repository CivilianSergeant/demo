<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/8/2016
 * Time: 1:12 PM
 */

namespace App\Http\Controllers;


use App\Entities\DeviceType;
use App\Utils\AES_Engine;
use App\Utils\Response;
use Illuminate\Support\Facades\Config;

class DeviceTypeController extends RestController
{
    public function index()
    {
        if(!empty($this->request)){
            if(!empty($this->request->apiName)
                && !method_exists($this,$this->request->apiName)){

                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('Api not exist');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            $apiName = $this->request->apiName;
            return $this->$apiName();
        }else{
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('Request not found');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

    /**
     * This method will call during
     * Get Device Types API request for following url
     * http:// [ ServerIp]: [serverPort]/device-types
     * @return string
     */
    public function getDeviceTypes()
    {
        $deviceTypes = DeviceType::all();
        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'count' => count($deviceTypes),
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1,
            'deviceTypes' => $deviceTypes
        ));
        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
    }
}