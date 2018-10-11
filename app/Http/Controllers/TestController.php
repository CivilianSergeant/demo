<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/13/2016
 * Time: 10:36 AM
 */

namespace App\Http\Controllers;


use App\Entities\GeoTerritory;
use App\Utils\DetectTerritory;
use App\Utils\Email\Email;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Entities\FcmDeviceGroup;
use App\Utils\FcmUtil;
class TestController extends Controller
{
    public function index()
    {
        try {
            /*$q = DB::select("SELECT start_ip4_address,netmask,country_code,country_name
FROM geoip
WHERE
INET_ATON('174.36.207.186') BETWEEN begin_ip_num AND end_ip_num
LIMIT 1
");
            dd($q[0]->country_code);


            $resultSet = GeoTerritory::where('is_active',0)->get();

            //$resultSet = DB::select($sql);
            //$res = mysql_query($sql);
            //$stid = oci_parse($conn, $sql);

            $x = '26.007513'; //'23.643096';
            $y = '91.142105'; //'90.386284';

            $teritory_id = null;

            $teritory_id = DetectTerritory::detect($x,$y,$resultSet);
            echo (!empty($teritory_id))? $teritory_id : 'None';*/


//            $sent = Email::sendEmail("mehearaz.uddin@nexdecade.com","Test Subject","Test Message Content");
//            var_dump($sent);
//            echo ($sent==true)? "E-mail Sent" : "E-mail not Sent";
              //  $fcmDeviceGroup = new FcmDeviceGroup();
//            $deviceGroup = FcmDeviceGroup::where('count','<',  FcmDeviceGroup::DEVICE_LIMIT)->first();
//            //dd($deviceGroup);
//            
//            $tokens = array('cLQVzth5q_A:APA91bHyrrpfUjKX6P4X3TaMIV81zPxetevF4uvQ7eajLu22LeKrZzIQYhRwqEW_eyPWJSC5HXMwpuWMqHaEGL0qzVsqrdr-gUuxU07nM3JuZx17Fl2sDbHQXk2CUd_GEYpoMUTPqes0');
//
//            $result = FcmUtil::addToDeviceGroup($deviceGroup->group_name, $deviceGroup->fcm_device_group, $tokens);
            print_r(1);

        } catch (\Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function checkSession(Request $request)
    {
        if($request->getSession()){
            if($request->getSession()->has('user_session')){

                return new Response(1);
            }
        }

        return new Response(0);
    }

    public function memcachedWrite()
    {


        try{

            error_reporting(E_ALL);
            $mem = new \Memcached();
            $mem->addServer("192.168.4.250", 11211);

            $result = $mem->get(19);

            print_R($result);

            /*$data = array(
                'sId' => 1,
                'cId' => 1333,
                'lst' => date('Y-m-d H:i:s'),
                'uIp' => $_SERVER['REMOTE_ADDR'],
                'ct' => 'eee80f834a6e15b47db06fb70e75bada',
                'wt' => date('Y-m-d H:i:s'),
                'ch' => 1234,
                'br' => 3939,
                'dr' => 1991
            );*/

            //$mem->set(18, implode(',',$data),20) or die("Couldn't save anything to memcached...");
            
            
        }catch(Exception $ex){
            echo json_decode(array('status'=>400,'Sorry ! Please try again later'));
            exit;
        }
    }
    
    public function sendNotification($token, $payload){
        $url = "https://fcm.googleapis.com/fcm/send";
        $fields = array(
            'to' => $token,  //"/topics/test",//$tokens,
            'data' => $payload
        );
        $headers = array(
            'Authorization:key=AAAA0DnS4uY:APA91bHKg8YgqL0oybwlz-C1B5y3b2AkTOPI7sfJ6RC2iGwpJ79M9LcpHW1RgxXTsqgMeeTj2QQ_IWA8QUBlvU3Q3_7fxPUgFRTdrCLIesvicBtepe3O9arxjuo383ryaMWbubnh-AVtheTpvVu3K1nB810277UkjA',
            'Content-Type:application/json'
        );
        
        $result = $this->_sendFCMRequest($url, $headers, $fields);
        $result = json_decode($result);
        echo $result->success;
    }
    
    private function _sendFCMRequest($url,$headers,$fields)
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));
        $result = curl_exec($ch);
        if($result === FALSE){
            throw new \Exception('CURL FAILED '. curl_error($ch));
        }

        curl_close($ch);
        return $result;
    }
}