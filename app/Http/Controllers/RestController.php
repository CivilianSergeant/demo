<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/8/2016
 * Time: 11:32 AM
 */

namespace App\Http\Controllers;
use App\Utils\AES_Engine;

use App\Utils\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class RestController extends Controller
{
    protected $request;
    protected $route;

    public function __construct(Request $request)
    {


        $requestUri = substr($request->getRequestUri(),1);
        $requestUri = explode("/",$requestUri);

        if(!empty($requestUri) && !empty($requestUri[0]) && !empty($_POST['data'])){


            $all = $request->all();
            $data = $all['data'];


//            $this->request = json_decode(AES_Engine::getDecrypt($data,"1234567891234567"));
            //$decData = json_decode(AES_Engine::getDecrypt($data,"1234567891234567"));

            $this->request= json_decode(AES_Engine::getDecrypt(trim(str_replace(" ", "+",$data)), Config::get('app.encryption_key')));

            // $this->request = RequestParser::parse($data);
            //file_put_contents('test.txt',print_r($this->request,true));
            //var_dump($this->request);

            if(!empty($this->request)){
                if(preg_match('/(get|Get)/',$this->request->apiName)){
                    $api_name = strtolower(substr($this->request->apiName,3));
                }else{
                    $api_name =strtolower(trim($this->request->apiName));
                }
                $this->route = strtolower(str_replace("-","",$requestUri[0]));

                if($api_name != $this->route){
                    $response = new Response();
                    $response->setStatus(1);
                    $response->setDebugCode(100);
                    $response->setDebugMsg('Api not found '.$api_name.', route: '.$this->route);
                    echo AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
                   /* echo AES_Engine::getEncrypt(json_encode(array(
                        'status'=>1,
                        //'request_uri'=> $request->getRequestUri(),
                        'api_name'   => $this->request->apiName,
                        'response'   => null,
                        'errorCode'  => 0,
                        'errorMessage' => '',
                        'debugCode'  => 100,
                        'debugMsg'   => ,

                    )), Config::get('app.encryption_key'));*/
                    exit;
                }

                if(empty($this->request->appId)){

                    $response = new Response();
                    $response->setStatus(1);
                    $response->setDebugCode(100);
                    $response->setDebugMsg('API is not access-able without appId');
                    echo AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                    exit;

                }else{
                    if(Config::get('app.appId') != $this->request->appId){
                        $response = new Response();
                        $response->setStatus(1);
                        $response->setDebugCode(100);
                        $response->setDebugMsg('API is not access-able, appId is invalid');
                        echo AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                        exit;
                    }
                }

                if(empty($this->request->appSecurityCode)){
                    $response = new Response();
                    $response->setStatus(1);
                    $response->setDebugCode(100);
                    $response->setDebugMsg('API is not access-able without appSecurityCode');
                    echo AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                    exit;
                }else{
                    if(Config::get('app.appSecurityCode') != $this->request->appSecurityCode){
                        $response = new Response();
                        $response->setStatus(1);
                        $response->setDebugCode(100);
                        $response->setDebugMsg('API is not access-able, appSecurityCode is invalid');
                        echo AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                        exit;
                    }
                }

            }else{
                $response = new Response();
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('Invalid request, JSON might not valid');
                echo AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                exit;
            }

        }

    }
}