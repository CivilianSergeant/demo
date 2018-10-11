<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/8/2016
 * Time: 5:24 PM
 */

namespace App\Http\Controllers;


use App\Entities\PostCode;
use App\Utils\Response;

class PostCodeController extends RestController
{
    public function index()
    {
        if(!empty($this->request)){
            $apiName = $this->request->apiName;
            $countryId  = (!empty($this->request->countryId))? $this->request->countryId : null;
            $divisionId = (!empty($this->request->divisionId))? $this->request->divisionId : null;
            $districtId = (!empty($this->request->districtId))? $this->request->districtId : null;

            return $this->$apiName($countryId,$divisionId,$districtId);
        }else{
            return new Response();
        }
    }

    private function getPostCodes($countryId,$divisionId,$districtId)
    {
        $postCodes = PostCode::where('country_id',$countryId)
                            ->where('division_id',$divisionId)
                            ->where('district_id',$districtId)
                            ->get();

        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'count'=>count($postCodes),
            'postCodes' => $postCodes
        ));

        return $response;

    }
}