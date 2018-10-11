<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/8/2016
 * Time: 11:32 AM
 */

namespace App\Http\Controllers;


use App\Utils\AES_Engine;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
class SimulatorController extends Controller
{
    const EXPIRE_IN_SEC = 60;

    public function __construct()
    {
        $this->middleware('doc');
    }

    public function index()
    {
        $routes = Config::get('app.api_routes');
        return view('simulator.index',array('routes'=>$routes));
    }

    public function getJsonRequest(Request $request)
    {

        $route = $request->get('route');
        if(!empty($route)){
            try{

                if(file_exists(base_path('templates/'.$route.'.json'))){
                    $contents = file_get_contents(base_path('templates/'.$route.'.json'));
                    echo trim($contents);
                }else{
                    echo 'template not found for request';
                }

            }catch(\Exception $ex){
                echo $ex->getMessage();
            }
        }

    }

    public function getDocumentationPage(Request $request)
    {
        $route = $request->get('route');
        return view('shared.'.$route,[]);
    }

    public function simulate(Request $request)
    {
        $url  = $request->get('req_url');
        $url  = str_replace('http://','',$url);
        $http = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')? 'https://':'http://';
        $url  = $http.$_SERVER['SERVER_NAME'].'/'.trim($url);

        $json = trim($request->get('req_json'));

        $data = AES_Engine::getEncrypt($json,Config::get('app.encryption_key'));
        file_put_contents('text.txt',$data);
        //echo AES_Engine::getDecrypt($data,Config::get('app.encryption_key'));
        //echo $url;
        $ch = curl_init($url);
        $data = array('data'=>$data);
        //dd($data);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HTTPGET,false);
        $response = curl_exec($ch);
        echo $response;die();
       // echo (!empty($response))? AES_Engine::getDecrypt($response,Config::get('app.encryption_key')) : 'Please Check Parameters are correct';
        curl_close($ch);

    }

    public function write(Request $request)
    {

        $remoteAddrs = array(
            array("192.168.4.250",11211),
            array("192.168.4.101",11211),
            array("192.168.4.102",11211),
            array("192.168.4.103",11211),
            array("192.168.4.104",11211),
            /*array("103.68.104.101",11211),
            array("103.68.104.102",11211)*/
        );

        $data = array(
            'streamerId' => 1,
            'customerId' => 1,
            'loginStartTime' => date('y-m-d H:i:s'),
            'userIp' => $_SERVER['REMOTE_ADDR'],
            'customerToken' => 'SIeriskfiwksfdliIeriskfiwksfdli',
            'watchTime' => date('y-m-d H:i:s'),
            'channelName' => 'abc',
            'bitRate' => '',
            'duration' => 0
        );

        foreach($remoteAddrs as $i=> $addr){
            try{
                $memcached = new \Memcached;
                $memcached->addServer($addr[0],$addr[1]);
                $userInfo = $memcached->get('user_info');
                if(!empty($userInfo)){

                    if(!array_key_exists(1,$userInfo)) {
                        $temp = $memcached->get('user_info');
                        $temp[1] = $data;
                        // $write = $memcached->set('user_info',$temp,self::EXPIRE_IN_SEC);
                        if ($memcached->set('user_info', $temp, self::EXPIRE_IN_SEC)) {
                            echo 'Successfully write at ' . implode(":", $addr) . '<br/>';
                        } else {
                            echo 'Cannot write at ' . implode(":", $addr) . '<br/>';
                        }
                    }



                }else{
                    /*for($i=0; $i<20000;$i++){
                        $temp[$i] = $data;
                    }*/

                    $temp[1] = $data;

                    if ($memcached->set('user_info',$temp, self::EXPIRE_IN_SEC)) {
                        echo 'Successfully write at ' . implode(":", $addr) . '<br/>';
                    } else {
                        echo 'Cannot write at ' . implode(":", $addr) . '<br/>';
                    }
                }

                $memcached->resetServerList();

            }catch (\Exception $ex){
                echo "<br/>".$ex->getMessage();
            }
        }


    }

    public function read(Request $request)
    {

        $remoteAddrs = array(
            array("192.168.4.250",11211),
            array("192.168.4.101",11211),
            array("192.168.4.102",11211),
            array("192.168.4.103",11211),
            array("192.168.4.104",11211),
            /*array("103.68.104.101",11211),
            array("103.68.104.102",11211)*/

        );
        $memcached = new \Memcached;
        foreach($remoteAddrs as $addr){

            echo "Read From : ".implode(":",$addr)."<br/>";
            $memcached->addServer($addr[0],$addr[1]);
            echo '<pre>';
            $val = $memcached->get('user_info');
            print_r($val);
            echo '<hr/>';
            //


            echo '<hr/>';
            $status = $memcached->getStats();

            $ipPort = implode(":",$addr);
            if(!empty($status) && !empty($status[$ipPort])){


                $memInfo = $status[$ipPort];
                //print_r($memInfo);
                $connections = $memcached->get('user_info');
                $userCount = (!empty($memcached->get('user_info')))? count($memcached->get('user_info')) : 0;


                echo "Pid: ". $memInfo['pid'] . "<br>";
                echo "Up Time: ". $memInfo['uptime'] . "<br>";
                echo "Current Connections: ". $memInfo['curr_connections'] . "<br>";
                echo "Total Connections: ". $memInfo['total_connections'] . "<br>";
                echo "Bytes Read: ". $memInfo['bytes_read'] . "<br>";
                echo "Bytes Written: ". $memInfo['bytes_written'] . "<br>";
                echo "Limit Max Bytes: ". $memInfo['limit_maxbytes'] . "<br>";
                echo "Total User: ". $userCount . "<br>";

                echo "<hr>";

                echo "<pre>";
                print_r($status);
            }

            $memcached->resetServerList();
        }




    }
}