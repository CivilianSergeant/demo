<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/9/2016
 * Time: 3:30 PM
 */

namespace App\Http\Controllers;


use App\Entities\Category;
use App\Entities\SubCategory;
use App\Utils\AES_Engine;
use App\Utils\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CategoryController extends RestController
{
    const PARENT_ID = 1;
    
    public function index()
    {
        if(!empty($this->request)){

            if(!empty($this->request->apiName)
                && !method_exists($this,$this->request->apiName)){

                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg('Api parameter apiName missing');
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

            try {
                $apiName = $this->request->apiName;
                return $this->$apiName();
            }catch(\Exception $ex){
                $response = new Response();
                $response->setDebugCode(100);
                $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }

        }else{
            $response = new Response();
            $response->setDebugCode(100);
            $response->setDebugMsg("Request no found");
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

    /**
     * This method will call during
     * Get Categories API request for following url
     * http:// [ ServerIp]: [serverPort]/categories
     * @param $type
     * @return string
     */
    public function getCategories()
    {
        $parentId = (!empty($this->request->parentId))? $this->request->parentId : self::PARENT_ID;
        $categories = Category::select('id','category_name')->where('type','LIVE')->where('parent_id',$parentId);
        $liveCategories = $categories->get();

        $categories = Category::select('id','category_name')->where('type','VOD')->where('parent_id',$parentId);
        $vodCategories = $categories->get();

        $categories = Category::select('id','category_name')->where('type','CATCHUP')->where('parent_id',$parentId);
        $catchupCategories = $categories->get();


        // Fetching Subcategories for Live category
        // and setting null with checking is there an
        // empty array while getting no sub category
        foreach($liveCategories as $i => $liveCategory){
            $subCategories = $liveCategory->subcategories()->get();
            $liveCategories[$i]->sub_categories = (!empty($subCategories) && count($subCategories))? $subCategories : null;
        }

        // Fetching Subcategories for Vod category
        // and setting null with checking is there an
        // empty array while getting no sub category
        foreach($vodCategories as $i => $vodCategory){
            $subCategories = $vodCategory->subcategories()->get();
            $vodCategories[$i]->sub_categories = (!empty($subCategories) && count($subCategories))? $subCategories : null;
        }

        // Fetching Subcategories for catchup category
        // and setting null with checking is there an
        // empty array while getting no sub category
        foreach($catchupCategories as $i => $catchupCategory){
            $subCategories = $catchupCategory->subcategories()->get();
            $catchupCategories[$i]->sub_categories = (!empty($subCategories) && count($subCategories))? $subCategories : null;
        }


        // return response on success
        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'code'  => 200,
            'notification' => true,
            'notificationType' => 1,
            'ads' => true,
            'adsType' => 1,
            'categories' => [
                'channels' => (!empty($liveCategories) && count($liveCategories))? $liveCategories : null,
                'vod'      => (!empty($vodCategories) && count($vodCategories))? $vodCategories : null,
                'catchup'  => (!empty($catchupCategories) && count($catchupCategories))? $catchupCategories : null
            ]
        ));
        return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
    }


}