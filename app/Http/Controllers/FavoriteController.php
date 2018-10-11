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
use App\Entities\FavoriteContent;
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


class FavoriteController extends RestController
{
    public function index()
    {
        if(!empty($this->request)){

            if(!empty($this->request->apiName)
                && !method_exists($this,$this->request->apiName)){

                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(100);
                $response->setErrorMsg('Api parameter apiName missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }


            if(empty($this->request->customerId)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('API Parameter customerId is missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            if(empty($this->request->password)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('API Parameter password is missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

           /* if(empty($this->request->limit)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('API Parameter limit missing, limit should not be 0 or null');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }*/

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
     * Set Favorites API request for following url
     * http:// [ ServerIp]: [serverPort]/set-favorites
     * @return string
     */
    public function setFavorites()
    {
        // check if contentId is null then return debug message
        if(empty($this->request->contentId)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter contentId missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
        
        if(!isset($this->request->isFavorite)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter flag missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $parentId = (!empty($this->request->parentId))? $this->request->parentId : 1;
        $user = User::where('id',$this->request->customerId)
                ->where('password',$this->request->password)
                ->where('parent_id',$parentId)
                ->first();

        if(empty($user)){

            $response = new Response();
            $response->setStatus(1);
            $response->setErrorCode(109);
            $response->setErrorMsg("Account not found");
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

        }

        $contentIds = $this->request->contentId;
        $contentIds = explode(",",$contentIds);

        if(!empty($contentIds)){

            try {

                // for each content id with customer id store data as favorite program
                
                foreach ($contentIds as $contentId) {
                    if($this->request->isFavorite){
                        $favoriteContent = new FavoriteContent(array(
                            'customer_id' => $user->id,
                            'content_id' => $contentId
                        ));
                        
                        $favoriteContent->save();
                    }else{
                        $favoriteContent = FavoriteContent::where('customer_id',$user->id)->where('content_id',$contentId)->first();
                        $favoriteContent->delete();
                    }   
                    
                }
                
                

                $result = $user->getBalance();//DB::select("select if(GetSubscriberBal(".$user->id.") IS NOT NULL,GetSubscriberBal(16),0) as balance");

                $response = new Response($this->request->apiName);
                $response->setResponse(array(
                    'code'  => 200,
                    'notification' => true,
                    'notificationType' => 1,
                    'ads'     => true,
                    'adsType' => 1,
                    'balance' => (!empty($result))? $result : 0,
                    'message' => 'Content successfully added to favorite list',
                    'messageType' => 'VIEW'
                ));

                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

            }catch(\Exception $ex){

                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

            }

        }else{

            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($contentIds);
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

        }

    }

    /**
     * This method will call during
     * Get Favorite Contents API request for following url
     * http:// [ ServerIp]: [serverPort]/favorite-contents
     * @return string
     */
    public function getFavoriteContents()
    {

        $contentValidate= new GetContentsValidation($this->request);
        try{
            $parentId = (!empty($this->request->parentId))? $this->request->parentId : 1;
            $user = User::where('id',$this->request->customerId)
                        ->where('password',$this->request->password)
                        ->where('parent_id',$parentId)
                        ->first();

            $userValidResponse = $contentValidate->validateUser($user);
            if($userValidResponse instanceof Response){
                return AES_Engine::getEncrypt($userValidResponse, Config::get('app.encryption_key'));
            }

            $appSetting = ApiSetting::where('parent_id',$parentId)->first();
            $organization = OrganizationInfo::where('parent_id',$parentId)->first();
            $subscriberBalance = $user->getBalance();
            $deviceType = DeviceType::find($user->clientType);

            $programs = ContentManager::getFavoriteContents($this->request, $user->id, $deviceType, $appSetting);

            $totalCount = ContentManager::countFavoriteContents($user->id);

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
                       // unset($programs[$i]->favorite);
                        $userPackage = $userPackages[Program::CATCHUP];
                    }else if(Program::isVod($program)){
                       // unset($programs[$i]->favorite);
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
                'channels' => (!empty($programs) && count($programs))? $programs : null,
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