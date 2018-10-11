<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/9/2016
 * Time: 11:21 AM
 */

namespace App\Http\Controllers;


use App\Entities\ApiSetting;
use App\Entities\DeviceType;
use App\Entities\MapStreamerInstance;
use App\Entities\OrganizationInfo;
use App\Entities\Program;
use App\Entities\ServiceOperator;
use App\Entities\UserPackage;
use App\Entities\UserProgram;
use App\Entities\Contents\ContentManager;
use App\User;
use App\Utils\AES_Engine;
use App\Utils\Response;
use App\Utils\Validation\GetContentsValidation;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use stdClass;


class ProgramController extends RestController
{
    const FEATURE_ITEM_LIMIT = 15;
    const NEWLY_UPLOADED_LIMIT = 20;
    const MOST_VIEWD_LIMIT = 20;
    
    public function index()
    {
        if(!empty($this->request)){

            if(!empty($this->request->apiName)
                && !method_exists($this,$this->request->apiName)){

                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(100);
                $response->setErrorMsg('Api parameter apiName is missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }



            

            if(empty($this->request->limit)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('API Parameter limit missing, limit should not be 0 or null');
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

    /**
     * This method will call during
     * Get Contents API request for following url
     * http:// [ ServerIp]: [serverPort]/contents
     * @return string
     */
    public function getContents()
    {

        $contentValidate= new GetContentsValidation($this->request);
        try{

            $contentValidateResponse = $contentValidate->validate();
            
            if($contentValidateResponse instanceof Response){
                return AES_Engine::getEncrypt($contentValidateResponse,Config::get('app.encryption_key'));
            }
            
            $user = User::where('id',$this->request->customerId)
                        ->where('password',$this->request->password)
                        ->first();
            
            $userValidateResponse = $contentValidate->validateUser($user);
            if($userValidateResponse instanceof Response){
                return AES_Engine::getEncrypt($userValidateResponse,Config::get('app.encryption_key'));
            }
            
           // $type = $this->request->type;

            // get default system info
            $organization = OrganizationInfo::find(1);

            // get default api settings
            $appSetting = ApiSetting::find(1);
            
            $subscriberBalance = $user->getBalance(); 
            $deviceType = DeviceType::find($user->clientType);

            $programs = ContentManager::getContents($this->request, $user->id, $deviceType, $appSetting);
//            $response = new Response($programs);
//            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            // total count
            $totalCount = ContentManager::countContents($this->request);

            $userPackages = UserPackage::isContentPackageSubscribed($user->id);
            
            $selectHlsFilter = ContentManager::getHlsFilter($deviceType);
            
            if(!empty($programs)){
                foreach($programs as $i=> $program){
                       
                    
                    
                    if(empty($program->water_mark_url)){
                        $programs[$i]->water_mark_url = $appSetting->default_image_path.$organization->water_mark_url;
                    }

                    ContentManager::setVideoShareLink($programs[$i], $program, $appSetting, $organization);
                    ContentManager::setDefaultLogoAndPoster($programs[$i], $program, $deviceType, $appSetting, $organization);

                    //$programs[$i]->serviceOperators = ServiceOperator::whereIn('telco_id',explode(",",$program->service_operator_id))->get();
                    
                    $programs[$i]->hlsLinks = MapStreamerInstance::select($selectHlsFilter)->where('program_id',$program->id)->get();
                    
                    if(count($programs[$i]->hlsLinks)==0){
                        
                        $programs[$i]->hlsLinks = ContentManager::getDefaultHlsLinks($deviceType, $organization);
                    }

                    if(Program::isLive($program)){
                        $userPackage = UserPackage::isSubscribed($user->id,$program->id);
                    }else if(Program::isCatchup($program)){
                        //unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::CATCHUP];
                    }else if(Program::isVod($program)){
                        //unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::VOD];
                    }
                    
                    


                    if(!empty($userPackage)){
                        $programs[$i]->subscription = true;
                        $programs[$i]->expireTime = $userPackage->package_expire_date;

                        // checking is subscribed package expired by current time
                        if(strtotime($userPackage->package_expire_date) < time()){

                           $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                        }

                    }else{
                        $programs[$i]->subscription = false;
                        
                        $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);
                    }
                    
                    $programs[$i]->individual_purchase = false;
                    if($program->individual_price > 0){
                        $subscribedProgram = UserProgram::isProgramSubscribed($user, $program);
                        if(!empty($subscribedProgram)){
                            if(strtotime($subscribedProgram->program_expire_date) > time()){ 
                                $programs[$i]->individual_purchase = true;
                            }else{
                                $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                            } 
                        }else{
                            $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);
                        } 
                    }


                }
            }


            $response = new Response($this->request->apiName);
            $response->setResponse(array(
                'code'=>200,
                'notification' => true,
                'notificationType' => 1,
                'ads' => true,
                'adsType' => 1,
                'balance'  => $subscriberBalance,
                'count'    => count($programs),
                'totalCount' => $totalCount,
                'channels'   => (!empty($programs) && count($programs))? $programs : null,
                'systemTime' => date('YmdHis')
                
            ));

            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

        }catch(\Exception $ex){
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

    /**
     * This method will call during
     * Get Contents API request for following url
     * http:// [ ServerIp]: [serverPort]/popular-contents
     * @return string
     */
    public function getPopularContents()
    {
        $contentValidate= new GetContentsValidation($this->request);
        
        try{
            
            $credValidResponse = $contentValidate->validateUserCredential();
            if($credValidResponse instanceof Response){
                return AES_Engine::getEncrypt($credValidResponse, Config::get('app.encryption_key'));
            }
            $user = User::where('id',$this->request->customerId)
                        ->where('password',$this->request->password)
                        ->first();
            
            
            $userValidResponse = $contentValidate->validateUser($user);
            if($userValidResponse instanceof  Response){
                return AES_Engine::getEncrypt($userValidResponse, Config::get('app.encryption_key'));
            }
            
            // get default system info
            $organization = OrganizationInfo::find(1);

            // get default api settings
            $appSetting = ApiSetting::find(1);

            $subscriberBalance = $user->getBalance();
            $deviceType = DeviceType::find($user->clientType);

            $programs = ContentManager::getPopularContents($this->request, $user->id, $deviceType, $appSetting);
            // total count
            $totalCount = self::MOST_VIEWD_LIMIT; //ContentManager::countPopularContent($this->request);

            $selectHlsFilter = ContentManager::getHlsFilter($deviceType);    
            
            $userPackages = UserPackage::isContentPackageSubscribed($user->id);

            if(!empty($programs)){
                foreach($programs as $i=> $program){
                    
                    $programs[$i]->individual_purchase = ($program->individual_price > 0)? false : true;

                    if(empty($program->water_mark_url)){
                        $programs[$i]->water_mark_url = $appSetting->default_image_path.$organization->water_mark_url;
                    }

                    ContentManager::setVideoShareLink($programs[$i], $program, $appSetting, $organization);
                    ContentManager::setDefaultLogoAndPoster($programs[$i], $program, $deviceType, $appSetting, $organization);
                    //$programs[$i]->serviceOperators = ServiceOperator::whereIn('telco_id',explode(",",$program->service_operator_id))->get();
                    $programs[$i]->hlsLinks = MapStreamerInstance::select($selectHlsFilter)->where('program_id',$program->id)->get();
                    
                   
                    if(count($programs[$i]->hlsLinks)==0){                   
                        $programs[$i]->hlsLinks = ContentManager::getDefaultHlsLinks($deviceType, $organization);
                    }
                    //$userPackage = UserPackage::isSubscribed($user->id,$program->id);
                    if(Program::isLive($program)){
                        $userPackage = UserPackage::isSubscribed($user->id,$program->id);
                    }else if(Program::isCatchup($program)){
                        //unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::CATCHUP];
                    }else if(Program::isVod($program)){
                        //unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::VOD];
                    }    


                    if(!empty($userPackage)){
                        $programs[$i]->subscription = true;
                        $programs[$i]->expireTime = $userPackage->package_expire_date;
                        // checking is subscribed package expired by current time
                        if(strtotime($userPackage->package_expire_date) < time()){
                            $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                        }
                    }else{
                        $programs[$i]->subscription = false;
                        $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);
                    }
                    
                    $programs[$i]->individual_purchase = false;
                    if($program->individual_price > 0){
                        $subscribedProgram = UserProgram::isProgramSubscribed($user, $program);
                        if(!empty($subscribedProgram)){
                            if(strtotime($subscribedProgram->program_expire_date) > time()){ 
                                $programs[$i]->individual_purchase = true;
                            }else{
                                $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                            } 
                        }else{
                            $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);
                        } 
                    }


                }
            }


            $response = new Response($this->request->apiName);

            $response->setResponse(array(
                'code'=>200,
                'notification' => true,
                'notificationType' => 1,
                'ads' => true,
                'adsType' => 1,
                'balance'  => $subscriberBalance,
                'count'    => count($programs),
                'totalCount' => $totalCount,
                'channels'   => (!empty($programs) && count($programs))? $programs : null,
                'systemTime' => date('YmdHis')
            ));

            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

        }catch(\Exception $ex){
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

    /**
     * This method will call during
     * Get Feature Contents API request for following url
     * http:// [ ServerIp]: [serverPort]/feature-contents
     * @return string
     */
    public function getFeatureContents()
    {
        $contentValidate= new GetContentsValidation($this->request);
        try{

            $contentValidateResponse = $contentValidate->validate();
            if($contentValidateResponse instanceof Response){
                return AES_Engine::getEncrypt($contentValidateResponse,Config::get('app.encryption_key'));
            }
            
            $user = User::where('id',$this->request->customerId)
                            ->where('password',$this->request->password)
                            ->first();
            
            $userValidateResponse = $contentValidate->validateUser($user);
            if($userValidateResponse instanceof Response){
                return AES_Engine::getEncrypt($userValidateResponse,Config::get('app.encryption_key'));
            }

            //$type = $this->request->type;

            // get default system info
            $organization = OrganizationInfo::find(1);

            // get default api settings
            $appSetting = ApiSetting::find(1);

            $subscriberBalance = $user->getBalance();
            $deviceType = DeviceType::find($user->clientType);
            
            $programs = ContentManager::getFeatureContents($this->request,$user->id, $deviceType, $appSetting);

            //total count
            $totalCount = ContentManager::countFeatureContent($this->request);
            $selectHlsFilter = ContentManager::getHlsFilter($deviceType);
            
            $userPackages = UserPackage::isContentPackageSubscribed($user->id);

            if(!empty($programs)){
                foreach($programs as $i=> $program){
                    
                    //$programs[$i]->individual_purchase = ($program->individual_price > 0)? false : true;

                    if(empty($program->water_mark_url)){
                        $programs[$i]->water_mark_url = $appSetting->default_image_path.$organization->water_mark_url;
                    }
                    
                    ContentManager::setVideoShareLink($programs[$i], $program, $appSetting, $organization);
                    ContentManager::setDefaultLogoAndPoster($programs[$i], $program, $deviceType, $appSetting, $organization);
 

                    //$programs[$i]->serviceOperators = ServiceOperator::whereIn('telco_id',explode(",",$program->service_operator_id))->get();
                    $programs[$i]->hlsLinks = MapStreamerInstance::select($selectHlsFilter)->where('program_id',$program->id)->get();

                    if(count($programs[$i]->hlsLinks)==0){

                        $programs[$i]->hlsLinks = ContentManager::getDefaultHlsLinks($deviceType, $organization);
                    }


                    //$userPackage = UserPackage::isSubscribed($user->id,$program->id);
                    if(Program::isLive($program)){
                        $userPackage = UserPackage::isSubscribed($user->id,$program->id);
                    }else if(Program::isCatchup($program)){
                        //unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::CATCHUP];
                    }else if(Program::isVod($program)){
                        //unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::VOD];
                    }

                    if(!empty($userPackage)){
                        $programs[$i]->subscription = true;
                        $programs[$i]->expireTime = $userPackage->package_expire_date;

                        if(strtotime($userPackage->package_expire_date) < time()){

                            $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                        }

                    }else{
                        $programs[$i]->subscription = false;
                        $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);

                    }
                    
                    
                    $programs[$i]->individual_purchase = false;
                    if($program->individual_price > 0){
                        $subscribedProgram = UserProgram::isProgramSubscribed($user, $program);
                        if(!empty($subscribedProgram)){
                            if(strtotime($subscribedProgram->program_expire_date) > time()){ 
                                $programs[$i]->individual_purchase = true;
                            }else{
                                $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                            } 
                        }else{
                            $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);
                        } 
                    }
                }
            }


            $response = new Response($this->request->apiName);

            $response->setResponse(array(
                'code'=>200,
                'notification' => true,
                'notificationType' => 1,
                'ads' => true,
                'adsType' => 1,
                'balance'  => $subscriberBalance,
                'count'    => count($programs),
                'totalCount'=> $totalCount,
                'channels'  => (!empty($programs) && count($programs))? $programs : null,
                'systemTime' => date('YmdHis')
            ));

            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }catch(\Exception $ex){
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

    /**
     * This method will call during
     * Get History Contents API request for following url
     * http:// [ ServerIp]: [serverPort]/history-contents
     * @return string
     */
    public function getHistoryContents()
    {

        $contentValidate= new GetContentsValidation($this->request);
        try{
            $credValidResponse = $contentValidate->validateUserCredential();
            if($credValidResponse instanceof Response){
                return AES_Engine::getEncrypt($credValidResponse, Config::get('app.encryption_key'));
            }
            $user = User::where('id',$this->request->customerId)
                            ->where('password',$this->request->password)
                            ->first();
            $userValidResponse = $contentValidate->validateUser($user);
            if($userValidResponse instanceof Response){
                return AES_Engine::getEncrypt($userValidResponse, Config::get('app.encryption_key'));
            }

            $appSetting = ApiSetting::find(1);
            $organization = OrganizationInfo::find(1);
            $subscriberBalance = $user->getBalance();

            $deviceType = DeviceType::find($user->clientType);           

            // total count
            
            $programs   = ContentManager::getHistoryContents($this->request,$user->id, $deviceType, $appSetting);
           // return AES_Engine::getEncrypt(new Response(),Config::get('app.encryption_key'));
            $totalCount = ContentManager::countHistoryContents($this->request);
            $selectHlsFilter = ContentManager::getHlsFilter($deviceType);
            
            $userPackages = UserPackage::isContentPackageSubscribed($user->id);

            if(!empty($programs)){
                foreach($programs as $i=> $program){
                    
                    

                    if(empty($program->water_mark_url)){
                        $programs[$i]->water_mark_url = $appSetting->default_image_path.$organization->water_mark_url;
                    }

                    ContentManager::setVideoShareLink($programs[$i], $program, $appSetting, $organization);
                    ContentManager::setDefaultLogoAndPoster($programs[$i], $program, $deviceType, $appSetting, $organization);

                    //$programs[$i]->serviceOperators = ServiceOperator::whereIn('telco_id',explode(",",$program->service_operator_id))->get();
                    $programs[$i]->hlsLinks = MapStreamerInstance::select($selectHlsFilter)->where('program_id',$program->id)->get();

                    if(count($programs[$i]->hlsLinks)==0){
                        $programs[$i]->hlsLinks = ContentManager::getDefaultHlsLinks($deviceType, $organization);
                    }


                    //$userPackage = UserPackage::isSubscribed($user->id,$program->id);
                    if(Program::isLive($program)){
                        $userPackage = UserPackage::isSubscribed($user->id,$program->id);
                    }else if(Program::isCatchup($program)){
                        //unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::CATCHUP];
                    }else if(Program::isVod($program)){
                        //unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::VOD];
                    }


                    if(!empty($userPackage)){
                        $programs[$i]->subscription = true;
                        $programs[$i]->expireTime = $userPackage->package_expire_date;

                        if(strtotime($userPackage->package_expire_date) < time()){
                            $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                        }
                    }else{
                        $programs[$i]->subscription = false;
                        $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);

                    }
                    
                    $programs[$i]->individual_purchase = false;
                    if($program->individual_price > 0){
                        $subscribedProgram = UserProgram::isProgramSubscribed($user, $program);
                        if(!empty($subscribedProgram)){
                            if(strtotime($subscribedProgram->program_expire_date) > time()){ 
                                $programs[$i]->individual_purchase = true;
                            }else{
                                $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                            } 
                        }else{
                            $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);
                        } 
                    }
                }
            }


            $response = new Response($this->request->apiName);

            $response->setResponse(array(
                'code'=>200,
                'notification' => true,
                'notificationType' => 1,
                'ads' => true,
                'adsType' => 1,
                'balance'  => $subscriberBalance,
                'count'    => count($programs),
                'totalCount' => $totalCount,
                'channels'   => (!empty($programs) && count($programs))? $programs : null,
                'systemTime' => date('YmdHis')
            ));

            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }catch(\Exception $ex){
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

    /**
     * This method will call during
     * Get Relative Contents API request for following url
     * http:// [ ServerIp]: [serverPort]/relative-contents
     * @return string
     */
    public function getRelativeContents()
    {

//        if(empty($this->request->videoTag)){
//            $response = new Response($this->request->apiName);
//            $response->setStatus(1);
//            $response->setDebugCode(100);
//            $response->setDebugMsg('API Parameter videoTag is missing');
//            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
//        }

        $contentValidate= new GetContentsValidation($this->request);
        
        try{

            $credValidResponse = $contentValidate->validateUserCredential();
            if($credValidResponse instanceof Response){
                return AES_Engine::getEncrypt($credValidResponse, Config::get('app.encryption_key'));
            }
            $user = User::where('id',$this->request->customerId)
                        ->where('password',$this->request->password)
                        ->first();
            
            $userValidResponse = $contentValidate->validateUser($user);
            if($userValidResponse instanceof Response){
                return AES_Engine::getEncrypt($userValidResponse, Config::get('app.encryption_key'));
            }
            
            $appSetting = ApiSetting::find(1);
            $organization = OrganizationInfo::find(1);
            $subscriberBalance = $user->getBalance();

            $deviceType = DeviceType::find($user->clientType);
            
            $programs = ContentManager::getRelativeContents($this->request, $user->id, $deviceType, $appSetting);

            $totalCount = ContentManager::countRelativeContents($this->request);
            
            $selectHlsFilter = ContentManager::getHlsFilter($deviceType);
            
            $userPackages = UserPackage::isContentPackageSubscribed($user->id);
            
            if(!empty($programs)){
                foreach($programs as $i=> $program){
                    
                    

                    if(empty($program->water_mark_url)){
                        $programs[$i]->water_mark_url = $appSetting->default_image_path.$organization->water_mark_url;
                    }

                    ContentManager::setVideoShareLink($programs[$i], $program, $appSetting, $organization);
                    ContentManager::setDefaultLogoAndPoster($programs[$i], $program, $deviceType, $appSetting, $organization);

                    //$programs[$i]->serviceOperators = ServiceOperator::whereIn('telco_id',explode(",",$program->service_operator_id))->get();
                    $programs[$i]->hlsLinks = MapStreamerInstance::select($selectHlsFilter)->where('program_id',$program->id)->get();

                    if(count($programs[$i]->hlsLinks)==0){
                        $programs[$i]->hlsLinks = ContentManager::getDefaultHlsLinks($deviceType, $organization);
                    }
                    
                    if(Program::isLive($program)){
                        $userPackage = UserPackage::isSubscribed($user->id,$program->id);
                    }else if(Program::isCatchup($program)){
                       // unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::CATCHUP];
                    }else if(Program::isVod($program)){
                       // unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::VOD];
                    }  
                    
                    if(!empty($userPackage)){ // if user subscribed
                        $programs[$i]->subscription = true;
                        $programs[$i]->expireTime = $userPackage->package_expire_date;
                        if(strtotime($userPackage->package_expire_date) < time()){
                            $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                        }
                    }else{ // if user not subscribed
                        $programs[$i]->subscription = false;
                        $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);
                    }
                    
                    $programs[$i]->individual_purchase = false;
                    if($program->individual_price > 0){
                        $subscribedProgram = UserProgram::isProgramSubscribed($user, $program);
                        if(!empty($subscribedProgram)){
                            if(strtotime($subscribedProgram->program_expire_date) > time()){ 
                                $programs[$i]->individual_purchase = true;
                            }else{
                                $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                            } 
                        }else{
                            $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);
                        } 
                    }
                }
            }


            $response = new Response($this->request->apiName);

            $response->setResponse(array(
                'code'=>200,
                'notification' => true,
                'notificationType' => 1,
                'ads' => true,
                'adsType' => 1,
                'balance'  => $subscriberBalance,
                'count'    => count($programs),
                'totalCount'=> $totalCount,
                'channels' => (!empty($programs) && count($programs)) ? $programs : null,
                'systemTime' => date('YmdHis')
            ));

            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }catch(\Exception $ex){
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }
    
    /**
     * This method will call during
     * Get Relative Contents API request for following url
     * http:// [ ServerIp]: [serverPort]/relative-contents
     * @return string
     */
    public function getRelativeContentsExt()
    {

//        if(empty($this->request->videoTag)){
//            $response = new Response($this->request->apiName);
//            $response->setStatus(1);
//            $response->setDebugCode(100);
//            $response->setDebugMsg('API Parameter videoTag is missing');
//            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
//        }

        $contentValidate= new GetContentsValidation($this->request);
        
        try{

            $credValidResponse = $contentValidate->validateUserCredential();
            if($credValidResponse instanceof Response){
                return AES_Engine::getEncrypt($credValidResponse, Config::get('app.encryption_key'));
            }
            $user = User::where('id',$this->request->customerId)
                        ->where('password',$this->request->password)
                        ->first();
            
            $userValidResponse = $contentValidate->validateUser($user);
            if($userValidResponse instanceof Response){
                return AES_Engine::getEncrypt($userValidResponse, Config::get('app.encryption_key'));
            }
            
            $appSetting = ApiSetting::find(1);
            $organization = OrganizationInfo::find(1);
            $subscriberBalance = $user->getBalance();

            $deviceType = DeviceType::find($user->clientType);
            $playingProgram = (!empty($this->request->contentId))? Program::find($this->request->contentId) : null;
            
            $programs = ContentManager::getExtRelativeContents($this->request, $user->id, $deviceType, $appSetting,$playingProgram);
            
            $totalCount = ContentManager::countExtRelativeContents($this->request,$playingProgram);
            //return AES_Engine::getEncrypt(new Response($programs),Config::get('app.encryption_key'));
            $selectHlsFilter = ContentManager::getHlsFilter($deviceType);
            
            $userPackages = UserPackage::isContentPackageSubscribed($user->id);
            
            if(!empty($programs)){
                foreach($programs as $i=> $program){
                    
                    

                    if(empty($program->water_mark_url)){
                        $programs[$i]->water_mark_url = $appSetting->default_image_path.$organization->water_mark_url;
                    }

                    ContentManager::setVideoShareLink($programs[$i], $program, $appSetting, $organization);
                    ContentManager::setDefaultLogoAndPoster($programs[$i], $program, $deviceType, $appSetting, $organization);

                    //$programs[$i]->serviceOperators = ServiceOperator::whereIn('telco_id',explode(",",$program->service_operator_id))->get();
                    $programs[$i]->hlsLinks = MapStreamerInstance::select($selectHlsFilter)->where('program_id',$program->id)->get();

                    if(count($programs[$i]->hlsLinks)==0){
                        $programs[$i]->hlsLinks = ContentManager::getDefaultHlsLinks($deviceType, $organization);
                    }
                    
                    if(Program::isLive($program)){
                        $userPackage = UserPackage::isSubscribed($user->id,$program->id);
                    }else if(Program::isCatchup($program)){
                        unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::CATCHUP];
                    }else if(Program::isVod($program)){
                        unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::VOD];
                    }  
                    
                    if(!empty($userPackage)){ // if user subscribed
                        $programs[$i]->subscription = true;
                        $programs[$i]->expireTime = $userPackage->package_expire_date;
                        if(strtotime($userPackage->package_expire_date) < time()){
                            $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                        }
                    }else{ // if user not subscribed
                        $programs[$i]->subscription = false;
                        $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);
                    }
                    
                    $programs[$i]->individual_purchase = false;
                    if($program->individual_price > 0){
                        $subscribedProgram = UserProgram::isProgramSubscribed($user, $program);
                        if(!empty($subscribedProgram)){
                            if(strtotime($subscribedProgram->program_expire_date) > time()){ 
                                $programs[$i]->individual_purchase = true;
                            }else{
                                $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                            } 
                        }else{
                            $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);
                        } 
                    }
                }
            }


            $response = new Response($this->request->apiName);

            $response->setResponse(array(
                'code'=>200,
                'notification' => true,
                'notificationType' => 1,
                'ads' => true,
                'adsType' => 1,
                'balance'  => $subscriberBalance,
                'count'    => count($programs),
                'totalCount'=> $totalCount,
                'channels' => (!empty($programs) && count($programs)) ? $programs : null,
                'systemTime' => date('YmdHis')
            ));

            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }catch(\Exception $ex){
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

    /**
     * This method will call during
     * Get Search Contents API request for following url
     * http:// [ ServerIp]: [serverPort]/search-contents
     * @return string
     */
    public function getSearchContents()
    {

        if(empty($this->request->keyword)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter keyword is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
        $contentValidate= new GetContentsValidation($this->request);
        try{

            $credValidResponse = $contentValidate->validateUserCredential();
            if($credValidResponse instanceof Response){
                return AES_Engine::getEncrypt($credValidResponse, Config::get('app.encryption_key'));
            }
            $user = User::where('id',$this->request->customerId)
                        ->where('password',$this->request->password)
                        ->first();

            $userValidResponse = $contentValidate->validateUser($user);
            if($userValidResponse instanceof Response){
                return AES_Engine::getEncrypt($userValidResponse, Config::get('app.encryption_key'));
            }
            
            // get default api setting
            $appSetting = ApiSetting::find(1);
            $organization = OrganizationInfo::find(1);
            $subscriberBalance = $user->getBalance();
            $deviceType = DeviceType::find($user->clientType);
            
            $programs = ContentManager::getSearchContents($this->request,$user->id, $deviceType, $appSetting);
            // total count
            $totalCount = ContentManager::countSearchContents($this->request);

            $selectHlsFilter = ContentManager::getHlsFilter($deviceType);
            
            $userPackages = UserPackage::isContentPackageSubscribed($user->id);

            if(!empty($programs)){
                foreach($programs as $i=> $program){
                    
                    

                    if(empty($program->water_mark_url)){
                        $programs[$i]->water_mark_url = $appSetting->default_image_path.$organization->water_mark_url;
                    }

                    ContentManager::setVideoShareLink($programs[$i], $program, $appSetting, $organization);
                    ContentManager::setDefaultLogoAndPoster($programs[$i], $program, $deviceType, $appSetting, $organization);

                    $programs[$i]->hlsLinks = MapStreamerInstance::select($selectHlsFilter)->where('program_id',$program->id)->get();

                    if(count($programs[$i]->hlsLinks)==0){

                        $programs[$i]->hlsLinks = ContentManager::getDefaultHlsLinks($deviceType, $organization);
                    }

                    if(Program::isLive($program)){
                        $userPackage = UserPackage::isSubscribed($user->id,$program->id);
                    }else if(Program::isCatchup($program)){
                        //unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::CATCHUP];
                    }else if(Program::isVod($program)){
                        //unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::VOD];
                    }  

                    if(!empty($userPackage)){ // if user subscribed
                        $programs[$i]->subscription = true;
                        $programs[$i]->expireTime = $userPackage->package_expire_date;

                        if(strtotime($userPackage->package_expire_date) < time()){

                            $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                        }

                    }else{ // if user not subscribed
                        $programs[$i]->subscription = false;
                        $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);

                    }
                    
                    $programs[$i]->individual_purchase = false;
                    if($program->individual_price > 0){
                        $subscribedProgram = UserProgram::isProgramSubscribed($user, $program);
                        if(!empty($subscribedProgram)){
                            if(strtotime($subscribedProgram->program_expire_date) > time()){ 
                                $programs[$i]->individual_purchase = true;
                            }else{
                                $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                            } 
                        }else{
                            $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);
                        } 
                    }
                }
            }


            $response = new Response($this->request->apiName);

            $response->setResponse(array(
                'code'=>200,
                'notification' => true,
                'notificationType' => 1,
                'ads' => true,
                'adsType' => 1,
                'balance'  => $subscriberBalance,
                'count'    => count($programs),
                'totalCount'=> $totalCount,
                'channels'  => (!empty($programs) && count($programs))? $programs : null,
                'systemTime' => date('YmdHis')
            ));

            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }catch(\Exception $ex){
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

    /**
     * This method will call during
     * Get Newly Uploaded Contents API request for following url
     * http:// [ ServerIp]: [serverPort]/newly-uploaded-contents
     * @return string
     */
    public function getNewlyUploadedContents()
    {

        $contentValidate= new GetContentsValidation($this->request);
        try{
            $credValidResponse = $contentValidate->validateUserCredential();
            if($credValidResponse instanceof Response){
                return AES_Engine::getEncrypt($credValidResponse, Config::get('app.encryption_key'));
            }
            $user = User::where('id',$this->request->customerId)
                ->where('password',$this->request->password)
                ->first();
            
            $userValidResponse = $contentValidate->validateUser($user);
            if($userValidResponse instanceof Response){
                return AES_Engine::getEncrypt($userValidResponse, Config::get('app.encryption_key'));
            }

            // get default system info
            $organization = OrganizationInfo::find(1);

            // get default api settings
            $appSetting = ApiSetting::find(1);
            
            $subscriberBalance = $user->getBalance();
            $deviceType = DeviceType::find($user->clientType);
            
            $programs = ContentManager::getNewlyUploadedContents($this->request, $user->id, $deviceType, $appSetting);
            // total count
            $totalCount = self::NEWLY_UPLOADED_LIMIT; //ContentManager::countNewlyUploadedContents($this->request);

            $selectHlsFilter = ContentManager::getHlsFilter($deviceType);
            
            $userPackages = UserPackage::isContentPackageSubscribed($user->id);

            if(!empty($programs)){
                foreach($programs as $i=> $program){
                    
                    

                    if(empty($program->water_mark_url)){
                        $programs[$i]->water_mark_url = $appSetting->default_image_path.$organization->water_mark_url;
                    }

                    ContentManager::setVideoShareLink($programs[$i], $program, $appSetting, $organization);
                    ContentManager::setDefaultLogoAndPoster($programs[$i], $program, $deviceType, $appSetting, $organization);

                    //$programs[$i]->serviceOperators = ServiceOperator::whereIn('telco_id',explode(",",$program->service_operator_id))->get();
                    $programs[$i]->hlsLinks = MapStreamerInstance::select($selectHlsFilter)->where('program_id',$program->id)->get();
                    
                    if(count($programs[$i]->hlsLinks)==0){
                        $programs[$i]->hlsLinks = ContentManager::getDefaultHlsLinks($deviceType, $organization);
                    }

                    if(Program::isLive($program)){
                        $userPackage = UserPackage::isSubscribed($user->id,$program->id);
                    }else if(Program::isCatchup($program)){
                      //  unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::CATCHUP];
                    }else if(Program::isVod($program)){
                      //  unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::VOD];
                    }

                    if(!empty($userPackage)){
                        $programs[$i]->subscription = true;
                        $programs[$i]->expireTime = $userPackage->package_expire_date;

                        // checking is subscribed package expired by current time
                        if(strtotime($userPackage->package_expire_date) < time()){
                            $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                        }

                    }else{
                        $programs[$i]->subscription = false;
                        $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);
                    }
                    
                    $programs[$i]->individual_purchase = false;
                    if($program->individual_price > 0){
                        $subscribedProgram = UserProgram::isProgramSubscribed($user, $program);
                        if(!empty($subscribedProgram)){
                            if(strtotime($subscribedProgram->program_expire_date) > time()){ 
                                $programs[$i]->individual_purchase = true;
                            }else{
                                $programs[$i]->hlsLinks = ContentManager::getExpiredHlsLinks($deviceType, $organization);
                            } 
                        }else{
                            $programs[$i]->hlsLinks = ContentManager::getUnsubscribedHlsLinks($deviceType, $organization);
                        } 
                    }
                }
            }


            $response = new Response($this->request->apiName);

            $response->setResponse(array(
                'code'=>200,
                'notification' => true,
                'notificationType' => 1,
                'ads' => true,
                'adsType' => 1,
                'balance'  => $subscriberBalance,
                'count'    => count($programs),
                'totalCount' => $totalCount,
                'channels'   => (!empty($programs) && count($programs))? $programs : null,
                'systemTime' => date('YmdHis')
            ));

            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

        }catch(\Exception $ex){
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

}