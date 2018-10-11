<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/9/2016
 * Time: 10:44 AM
 */

namespace App\Http\Controllers;


use App\Entities\Road;
use App\Utils\Response;

class RoadController extends RestController
{
    public function index()
    {

        if(!empty($this->request)){
            $apiName = $this->request->apiName;
            $countryId  = (!empty($this->request->countryId))? $this->request->countryId : null;
            $divisionId = (!empty($this->request->divisionId))? $this->request->divisionId : null;
            $districtId = (!empty($this->request->districtId))? $this->request->districtId : null;
            $areaId = (!empty($this->request->areaId))? $this->request->areaId : null;
            $subAreaId = (!empty($this->request->subAreaId))? $this->request->subAreaId : null;
            return $this->$apiName($countryId,$divisionId,$districtId,$areaId,$subAreaId);
        }else{
            return new Response();
        }
    }

    public function getRoads($countryId,$divisionId,$districtId,$areaId,$subAreaId)
    {
        $roads = Road::where('country_id',$countryId)
                        ->where('division_id',$divisionId)
                        ->where('district_id',$districtId)
                        ->where('area_id',$areaId)
                        ->where('sub_area_id',$subAreaId)
                        ->get();

        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'count' => count($roads),
            'roads' => $roads
        ));

        return $response;
    }
}