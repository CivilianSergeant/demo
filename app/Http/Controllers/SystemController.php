<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/1/2016
 * Time: 12:32 PM
 */

namespace App\Http\Controllers;


use App\Entities\CurrentViewer;
use App\Entities\DbVersion;
use App\Entities\DeviceType;
use App\Entities\OrganizationInfo;
use App\Entities\Program;
use App\Entities\ServiceOperator;
use App\Entities\StreamerInstance;
use App\Entities\ApiSetting;
use App\User;
use App\Utils\AES_Engine;
use App\Utils\CacheDB;
use App\Utils\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class SystemController extends RestController
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
     * Get Heart Beat API request for following url
     * http:// [ ServerIp]: [serverPort]/heart-beat
     * @return string
     */
    public function heartBeat()
    {

        if(empty($this->request->type)){

            $typeValues = array('FOREGROUND','BACKGROUND');
            if(!in_array($this->request->type,$typeValues,true)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('type values must be one of this ['.implode(',',$typeValues).']');
                return AES_Engine::getEncrypt($response,Config::key('app.encryption_key'));
            }

            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('type is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if(empty($this->request->customerId)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('customerId is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if(empty($this->request->password)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugMsg(100);
            $response->setDebugMsg('password is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $user = User::where('id',$this->request->customerId)
                    ->where('password',$this->request->password)->first();

        if(!empty($user)){

            if($user->id == 1){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('Access Forbidden');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            $user->heart_beat = time();
            $user->heart_beat_type = $this->request->type;
            $user->lat = (!empty($this->request->lat))? $this->request->lat : 0;
            $user->lon = (!empty($this->request->lon))? $this->request->lon : 0;
            $user->save();

            $dbVersion = DbVersion::select('channel_db_version',
                'vod_db_version','notification_db_version','catchup_db_version','package_db_version','category_db_version')
                ->first();

            // Set Memcached Data
            try{
                // $streamers = StreamerInstance::where('is_active',1)->get();
                $cacheDB = new CacheDB();
                $cacheDB->pushToMemcached($user, null);

            }catch(\Exception $ec) {
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg($ec->getMessage());
                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            }

            $result = DB::select("select if(GetSubscriberBal(".$user->id.") IS NOT NULL,GetSubscriberBal(".$user->id."),0) as balance");

            $response = new Response($this->request->apiName);
            $response->setResponse(array(
                'code'=>200,
                'notification' => true,
                'notificationType' => 1,
                'ads' => true,
                'adsType' => 1,
                'balance' => (!empty($result) && !empty($result[0]->balance))? $result[0]->balance : 0,
                'sessionToken' => $user->iptv_token,
                'dbVersion' => (!empty($dbVersion))? $dbVersion : null,
                'systemTime'=>date('YmdHis'),
                'message'=>"Response OK",
                'messageType'=>"NONE"
            ));

            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

        }else{

            $response = new Response();
            $response->setStatus(1);
            $response->setErrorCode(109);
            $response->setErrorMsg('No Account Found');
            $response->setDebugCode(100);
            $response->setDebugMsg('User account not exist');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }


    }

    /**
     * This method will call during
     * Get Viewing Content API request for following url
     * http:// [ ServerIp]: [serverPort]/viewing-content
     * @return string
     */
    public function viewingContent()
    {
            if(empty($this->request->contentId)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('contentId is missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            $program = Program::find($this->request->contentId);
            if(empty($program)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorMsg(110);
                $response->setErrorMsg("Content missing");
                $response->setDebugCode(100);
                $response->setDebugMsg('No program found with contentId '.$this->request->contentId);
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            if(empty($this->request->type)){

                $typeValues = array('CHANNEL','VOD','CATCHUP');
                if(!in_array($this->request->type,$typeValues,true)){
                    $response = new Response($this->request->apiName);
                    $response->setStatus(1);
                    $response->setDebugCode(100);
                    $response->setDebugMsg('type values must be one of this ['.implode(',',$typeValues).']');
                    return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
                }

                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('type is missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            if(empty($this->request->customerId)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('customerId is missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            if(empty($this->request->password)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('password is missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            $user = User::where('id',$this->request->customerId)
                        ->where('password',$this->request->password)
                        ->first();
            //$program = Program::find($this->request->contentId);

            if(!empty($user)){

                // Set Memcached Data
                try{
                    //$streamers = StreamerInstance::where('is_active',1)->get();
                    $cacheDB = new CacheDB();
                    $cacheDB->pushToMemcached($user, null,array(
                        'watchTime' => date('Y-m-d H:i:s'),
                        'channelName' => (!empty($program))? $program->program_name : $this->request->contentId
                    ));

                }catch(\Exception $ec) {
                    $response = new Response($this->request->apiName);
                    $response->setStatus(1);
                    $response->setDebugCode(100);
                    $response->setDebugMsg($ec->getMessage());
                    return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
                }

                $user->lat = (!empty($this->request->lat))? $this->request->lat : 0;
                $user->lon = (!empty($this->request->lon))? $this->request->lon : 0;
                $user->save();

                $currentViewer = new CurrentViewer(array(
                    'customer_id'  => $user->id,
                    'content_id'   => $this->request->contentId,
                    'content_type' => $this->request->type,
                    'lat'          => (!empty($this->request->lat))? $this->request->lat : 0,
                    'lon'          => (!empty($this->request->lon))? $this->request->lon : 0
                ));

                $currentViewer->save();

                // increment view count
                $program->view_count = ($program->view_count + 1);
                $program->save();

                $dbVersion = DbVersion::select('channel_db_version','vod_db_version','notification_db_version',
                    'catchup_db_version','package_db_version','category_db_version')
                    ->first();

                $result = DB::select("select if(GetSubscriberBal(".$user->id.") IS NOT NULL,GetSubscriberBal(".$user->id."),0) as balance");
                $categoryName = null;
                if(!empty($this->request->categoryId) && !empty($this->request->subCategoryId)){
                    $categoryName = DB::table('iptv_category_programs')->select('category_name','sub_category_name')
                            ->join('iptv_categories','iptv_categories.id','=','iptv_category_programs.category_id')
                            ->join('iptv_sub_categories','iptv_sub_categories.id','=','iptv_category_programs.sub_category_id')
                            ->where('iptv_category_programs.program_id',$this->request->contentId)->first();
                            
                }

                $response = new Response($this->request->apiName);
                $response->setResponse(array(
                    'code'=>200,
                    'notification' => true,
                    'notificationType' => 1,
                    'ads' => true,
                    'adsType' => 1,
                    'balance'     => (!empty($result) && !empty($result[0]->balance))? $result[0]->balance : 0,
                    'dbVersion'   => (!empty($dbVersion))? $dbVersion : null,
                    'breadcrumbs' => $categoryName,
                    'systemTime'  => date('YmdHis'),
                    'message'     => "Response OK",
                    'messageType' => "NONE"
                ));
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

            }else{
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(109);
                $response->setErrorMsg('No Account Found');
                $response->setDebugCode(100);
                $response->setDebugMsg('User account not exist');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }



    }

    public function getSystemSettings()
    {
        $serviceOperators = ServiceOperator::select('telco_id')->get();
        $telcoIds = array();

        if(!empty($serviceOperators)){
            foreach($serviceOperators as $serviceOperator){
                if(!in_array($serviceOperator->telco_id,$telcoIds,true)){
                    $telcoIds[] = $serviceOperator->telco_id;
                }
            }
        }

        $deviceTypes = DeviceType::all();

        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1,
            'serviceOperators' => (!empty($telcoIds))? implode(',',$telcoIds) : null,
            'deviceTypes'      => (!empty($deviceTypes) && count($deviceTypes))? $deviceTypes : null,
            'confirmCodeTemplate' => Config::get('app.confirmCodeTemplate')
        ));

        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
    }
    
    public function getAboutUs()
    {
        if(empty($this->request->parentId)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg("No Parent ID found");
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        $organization = OrganizationInfo::where('parent_id',$this->request->parentId)->first();
        $apiSetting   = ApiSetting::where('parent_id',$this->request->parentId)->first();
        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'code' => 200,
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1,
            'aboutUs' => $apiSetting->default_image_path.$organization->about_us
        ));
        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
    }

}