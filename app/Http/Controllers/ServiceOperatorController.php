<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/8/2016
 * Time: 1:12 PM
 */

namespace App\Http\Controllers;


use App\Entities\ServiceOperator;
use App\Utils\AES_Engine;
use App\Utils\Response;
use Illuminate\Support\Facades\Config;

class ServiceOperatorController extends RestController
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
            try {
                $apiName = $this->request->apiName;
                return $this->$apiName();
            }catch(\Exception $ex){
                $response = new Response();
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }
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
     * Get Service Operators API request for following url
     * http:// [ ServerIp]: [serverPort]/service-operators
     * @return string
     */
    public function getServiceOperators()
    {
        $serviceOperators = ServiceOperator::all();
        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'count' => count($serviceOperators),
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1,
            'serviceOperators' => (!empty($serviceOperators) && count($serviceOperators))? $serviceOperators : null
        ));
        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
    }

    public function getServiceOperatorIds()
    {
        $serviceOperators = ServiceOperator::select('telco_id')->get();
        $response = new Response($this->request->apiName);
        $telcoIds = array();

        if(!empty($serviceOperators)){
            foreach($serviceOperators as $serviceOperator){
                if(!in_array($serviceOperator->telco_id,$telcoIds,true)){
                    $telcoIds[] = $serviceOperator->telco_id;
                }
            }
        }

        $response->setResponse(array(
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1,
            'serviceOperatorId' => (!empty($telcoIds))? implode(',',$telcoIds) : null
        ));
        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
    }
}