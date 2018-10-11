<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 9/28/2016
 * Time: 6:29 PM
 */

namespace App\Utils;




class CacheDB
{

    const EXPIRE_IN_SEC = 3600;

    public function pushToMemcached($user, $oldToken=null,$watchOptions=null)
    {

        $remoteAddrs = array();
        $datas = array();


        // uncomment following if you want to push data to remote memcached
        /*foreach($streamers as $i=> $streamer){


            $remoteAddrs[$i] = array($streamer->instance_local_ip,11211);
            $datas[$i] = array(
                'sId' => $streamer->id,
                'cId' => $user->id,
                'lST' => date('Y-m-d H:i:s'),
                'uIp' => $_SERVER['REMOTE_ADDR'],
                'cT' => $user->iptv_token,
                'wT' => (!empty($watchOptions))? $watchOptions['watchTime'] : null,
                'cN' => (!empty($watchOptions))? $watchOptions['channelName'] : null,
                'bR' => '',
                'dR' => 0
            );
        }*/

        // use following block of code for local memcache
        $remoteAddrs[] = array("192.168.4.250",11211);
        $datas[] = array(
            'sId' => 0,
            'cId' => $user->id,
            'lST' => date('Y-m-d H:i:s'),
            'uIp' => $_SERVER['REMOTE_ADDR'],
            //'cT' => $user->iptv_token,
            'wT' => (!empty($watchOptions))? $watchOptions['watchTime'] : 'N/A',
            'cN' => (!empty($watchOptions))? $watchOptions['channelName'] : 'N/A',
            'bR' => 0,
            'dR' => 0
        );

        // pushing to memcache
        foreach($remoteAddrs as $i=> $addr){
            try{
                $memcached = new \Memcached;

                $memcached->addServer($addr[0],$addr[1]);
                /*if(!empty($oldToken)){
                    $memcached->delete($oldToken);
                }*/
                $userInfo = $memcached->get($user->iptv_token);
                $data = $datas[$i];

                if(!empty($userInfo)){


                    /*if(!array_key_exists($user->id,$userInfo)) {

                        $temp = $memcached->get('user_info');
                        $temp[$user->id] = $data;
                        // $write = $memcached->set('user_info',$temp,self::EXPIRE_IN_SEC);
                        if (!$memcached->set($user->id, $temp, self::EXPIRE_IN_SEC)) {
                            file_put_contents('storage/logs/log.txt','Cannot write at ' . implode(":", $addr)."\r\n",FILE_APPEND);
                        }
                    }else{*/
                        $temp = explode(",",$userInfo);
                        $temp[0] = $data['sId'];
                        $temp[1] = $data['cId'];
                        $temp[2] = $data['lST'];
                        $temp[3] = $data['uIp'];
                        //$temp[4] = $data['cT'];

                        if(!empty($watchOptions)){
                            $temp[5] = $data['wT'];
                            $temp[6] = $data['cN'];
                        }

                        if (!$memcached->set($user->iptv_token, implode(',',$temp), self::EXPIRE_IN_SEC)) {
                            file_put_contents('storage/logs/log.txt','Cannot write at ' . implode(":", $addr)."\r\n",FILE_APPEND);
                        }
                   // }

                }else{


                    if (!$memcached->set($user->iptv_token,implode(',',$data), self::EXPIRE_IN_SEC)) {
                        file_put_contents('storage/logs/log.txt','Cannot write at ' . implode(":", $addr)."\r\n",FILE_APPEND);
                    }
                }

                $memcached->resetServerList();

            }catch (\Exception $ex){
                throw new \Exception("Unable to connect to ".implode(":", $addr));
            }
        }

        return true;
    }
}