<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/9/2016
 * Time: 12:32 PM
 */

namespace App\Http\Controllers;


use App\Entities\ApiSetting;
use App\Entities\DeviceType;
use App\Entities\OrganizationInfo;
use App\Entities\Package;
use App\Entities\PaymentGateways\PaymentGatewayManager;
use App\User;
use App\Utils\AES_Engine;
use App\Utils\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
class PackageController extends RestController
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
            $response->setDebugMsg("Request no found");
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

    /**
     * This method will call during
     * Get Packages API request for following url
     * http:// [ ServerIp]: [serverPort]/packages
     * @return string
     */
    public function getPackages()
    {

        $typeValues = array('LIVE','VOD','CATCHUP','NULL');
        if(!in_array($this->request->type,$typeValues,true)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('type values must be one of this ['.implode(',',$typeValues).']');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if(empty($this->request->type)){

            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter type missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if(empty($this->request->deviceType)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter deviceType is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        /*$isCommercial = (!empty($this->request->isCommercial))? $this->request->isCommercial : null;
        if(empty($isCommercial) || $isCommercial == 'false' || $isCommercial == false){
            $isCommercial = 0;
        }else{
            $isCommercial = 1;
        }*/

        $appSetting = ApiSetting::find(1);
        $organization = OrganizationInfo::find(1);
        $deviceType = DeviceType::find($this->request->deviceType);

        $select = array('id','package_name',
            'package_type','duration','price',DB::raw('GetIptvPackageProgramsCount(iptv_packages.id) as programs')
        );

        if(!empty($deviceType) && $deviceType->isMobile()){
            $select[] = 'package_mobile_logo';
            $select[] = 'package_poster_mobile';

        }

        if(!empty($deviceType) && ($deviceType->isSTB()|| $deviceType->isWeb())){
            $select[] = 'package_stb_logo';
            $select[] = 'package_poster_stb';
        }



        $packages = Package::select($select);

        $type = $this->request->type;
        if($type == "LIVE"){
            $packages = $packages->where('package_type','!=',Package::VOD)
                ->where('package_type','!=',Package::CATCHUP);
        }else{
            if($type != "NULL")
                $packages = $packages->where('package_type',$type);
        }

        if(isset($this->request->limit) && isset($this->request->offset)){

            $packages = $packages->take($this->request->limit)
                ->skip($this->request->offset);
        }

        $packages = $packages->get();

        $countSelect = "select count(id) as total from iptv_packages";

        if($type == "LIVE"){
            $countSelect .= " WHERE package_type != '".Package::VOD."' AND package_type != '".Package::CATCHUP."'";
        }else{
            if($type != "NULL")
                $countSelect .= " WHERE package_type = '".$type."'";
        }


        $totalCount = DB::select($countSelect);

        if(!empty($packages)){
            foreach($packages as $i => $package){

                if(!empty($deviceType) && $deviceType->isMobile()) {
                    $packages[$i]->package_mobile_logo  = (!empty($package->package_mobile_logo))? $appSetting->default_image_path.$package->package_mobile_logo : $appSetting->default_image_path.$organization->default_pkg_logo_mobile;
                    $packages[$i]->package_poster_mobile  =  (!empty($package->package_poster_mobile))? $appSetting->default_image_path.$package->package_poster_mobile : $appSetting->default_image_path.$organization->default_pkg_poster_mobile;
                }

                if(!empty($deviceType) && ($deviceType->isSTB() || $deviceType->isWeb())){
                    $packages[$i]->package_stb_logo  = (!empty($package->package_stb_logo))? $appSetting->default_image_path.$package->package_stb_logo : $appSetting->default_image_path.$organization->default_pkg_logo_stb;
                    $packages[$i]->package_poster_stb  =  (!empty($package->package_poster_stb))? $appSetting->default_image_path.$package->package_poster_stb : $appSetting->default_image_path.$organization->default_pkg_poster_stb;
                }


            }
        }

        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'type'  => $type,
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1,
            'count' => count($packages),
            'totalCount' => (!empty($totalCount))? $totalCount[0]->total : 0,
            'packages' => (!empty($packages) && count($packages))? $packages : null
        ));

        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
    }

    /**
     * This method will call during
     * Get Package Details API request for following url
     * http:// [ ServerIp]: [serverPort]/package-details
     * @return string
     */
    public function getPackageDetails()
    {

        if(empty($this->request->packageId)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter packageId is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if(empty($this->request->deviceType)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter deviceType is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $appSetting = ApiSetting::find(1);
        $organization = OrganizationInfo::find(1);
        $deviceType = DeviceType::find($this->request->deviceType);

        $select = array('id','package_name','package_type','duration','price');

        if(!empty($deviceType) && $deviceType->isMobile()){
            $select[] = 'package_mobile_logo';
            $select[] = 'package_poster_mobile';

        }

        if(!empty($deviceType) && $deviceType->isSTB()){
            $select[] = 'package_stb_logo';
            $select[] = 'package_poster_stb';
        }

        $package = Package::select($select)->where('id',$this->request->packageId)->first();

        if(empty($package)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter packageId is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }


        try {
            // checking device type for logo and poster
            if(!empty($deviceType) && $deviceType->isMobile()) {
                $package->package_mobile_logo    = (!empty($package->package_mobile_logo))? $appSetting->default_image_path.$package->package_mobile_logo : $appSetting->default_image_path.$organization->default_pkg_logo_mobile;
                $package->package_poster_mobile  =  (!empty($package->package_poster_mobile))? $appSetting->default_image_path.$package->package_poster_mobile : $appSetting->default_image_path.$organization->default_pkg_poster_mobile;
            }

            if(!empty($deviceType) && $deviceType->isSTB()) {
                $package->package_stb_logo    = (!empty($package->package_stb_logo))? $appSetting->default_image_path.$package->package_stb_logo : $appSetting->default_image_path.$organization->default_pkg_logo_stb;
                $package->package_poster_stb  =  (!empty($package->package_poster_stb))? $appSetting->default_image_path.$package->package_poster_stb : $appSetting->default_image_path.$organization->default_pkg_poster_stb;
            }

            if ($package->isContent()) {
                $programs = $package->ContentPrograms();
                $package->programs = (!empty($programs) && count($programs))? $programs : null;
            } else {

                $programs = $package->Programs()->get();
                $package->programs = (!empty($programs) && count($programs))? $programs : null;

            }
        }catch(\Exception $ex){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

        }

        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'code'=>200,
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1,
            'package'=> (!empty($package)) ? $package : null
        ));
        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));

    }

    /**
     * This method will call during
     * Get Subscribed packages API request for following url
     * http:// [ ServerIp]: [serverPort]/subscribed-packages
     * @return string
     */
    public function getSubscribedPackages()
    {
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

        $user = User::where('id',$this->request->customerId)
                    ->where('password',$this->request->password)
                    ->first();
        
        if(empty($user)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(109);
            $response->setErrorMsg('Account Not Found');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if($user->id == 1){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('Access Forbidden');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        $appSetting = ApiSetting::find(1);
        $organization = OrganizationInfo::find(1);
        $deviceType = DeviceType::find($this->request->deviceType);

        $packageSelectionFilter = array(
            'iptv_packages.id','package_name','package_type','duration','price','package_start_date','package_expire_date',DB::raw('GetIptvPackageProgramsCount(iptv_packages.id) as programs')
        );

        if(!empty($deviceType) && $deviceType->isMobile()){
            $packageSelectionFilter[] = 'package_mobile_logo';
            $packageSelectionFilter[] = 'package_poster_mobile';

        }

        if(!empty($deviceType) && $deviceType->isSTB()){
            $packageSelectionFilter[] = 'package_stb_logo';
            $packageSelectionFilter[] = 'package_poster_stb';
        }

        $subscribedPackages = $user->getSubscribedPackages()
                                   ->select($packageSelectionFilter);

        if(isset($this->request->limit) && isset($this->request->offset)){
            $subscribedPackages = $subscribedPackages->take($this->request->limit)
                            ->skip($this->request->offset);
        }

        $subscribedPackages = $subscribedPackages->get();

        if(!empty($subscribedPackages)){
            // checking device type to set default logo and poster
            foreach($subscribedPackages as $package){
                if(!empty($deviceType) && $deviceType->isMobile()) {
                    $package->package_mobile_logo    = (!empty($package->package_mobile_logo))? $appSetting->default_image_path.$package->package_mobile_logo : $appSetting->default_image_path.$organization->default_pkg_logo_mobile;
                    $package->package_poster_mobile  =  (!empty($package->package_poster_mobile))? $appSetting->default_image_path.$package->package_poster_mobile : $appSetting->default_image_path.$organization->default_pkg_poster_mobile;
                }

                if(!empty($deviceType) && $deviceType->isSTB()) {
                    $package->package_stb_logo    = (!empty($package->package_stb_logo))? $appSetting->default_image_path.$package->package_stb_logo : $appSetting->default_image_path.$organization->default_pkg_logo_stb;
                    $package->package_poster_stb  =  (!empty($package->package_poster_stb))? $appSetting->default_image_path.$package->package_poster_stb : $appSetting->default_image_path.$organization->default_pkg_poster_stb;
                }
            }
        }

        $result = DB::select("select if(GetSubscriberBal(".$user->id.") IS NOT NULL,GetSubscriberBal(16),0) as balance");

        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'count' => 200,
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1,
            'balance'=> (!empty($result) && !empty($result[0]->balance))? $result[0]->balance : 0,
            'subscribedPackages' => (!empty($subscribedPackages) && count($subscribedPackages))? $subscribedPackages : null
        ));
        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));


    }

    public function purchasePakcage()
    {
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

        $user = User::where('id',$this->request->customerId)
            ->where('password',$this->request->password)
            ->first();

        if(empty($user)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setErrorCode(109);
            $response->setErrorMsg('Account Not Found');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if($user->id == 1){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('Access Forbidden');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if(empty($this->request->packageId)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter packageId is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if(empty($this->request->startDate)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter startDate is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if(empty($this->request->endDate)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter endDate is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if(empty($this->request->amount)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter amount is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if(empty($this->request->purchaseType)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter purchaseType is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if($this->request->purchaseType == PaymentGatewayManager::SCRATCH_CARD){
            if(empty($this->request->serialNo)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('API Parameter serialNo is missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            if(empty($this->request->cardNo)){
                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('API Parameter cardNo is missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }
        }

        if(empty($this->request->deviceType)){
            $response = new Response($this->request->apiName);
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg('API Parameter deviceType is missing');
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }




    }
}