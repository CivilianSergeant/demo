<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/8/2016
 * Time: 4:13 PM
 */

namespace App\Http\Controllers;


use App\Entities\Area;
use App\Entities\SubArea;
use App\Utils\Response;

class AreaController extends RestController
{
    public function index()
    {
        if(!empty($this->request)){
            $apiName = $this->request->apiName;
            $countryId  = (!empty($this->request->countryId))? $this->request->countryId : null;
            $divisionId = (!empty($this->request->divisionId))? $this->request->divisionId : null;
            $districtId = (!empty($this->request->districtId))? $this->request->districtId : null;

            return $this->$apiName($countryId,$divisionId,$districtId);
        }

    }

    public function getAreas($countryId, $divisionId, $districtId)
    {
        $areas = Area::where('country_id',$countryId)
                    ->where('division_id',$divisionId)
                    ->where('district_id',$districtId)
                    ->get();

        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'count' => count($areas),
            'areas' => $areas
        ));

        return $response;
    }

    public function subAreas()
    {
        if(!empty($this->request)){
            $apiName = $this->request->apiName;
            $countryId  = (!empty($this->request->countryId))? $this->request->countryId : null;
            $divisionId = (!empty($this->request->divisionId))? $this->request->divisionId : null;
            $districtId = (!empty($this->request->districtId))? $this->request->districtId : null;
            $areaId = (!empty($this->request->areaId))? $this->request->areaId : null;
            return $this->$apiName($countryId,$divisionId,$districtId,$areaId);
        }
    }

    private function getSubAreas($countryId,$divisionId,$districtId,$areaId)
    {
        $subAreas = SubArea::where('country_id',$countryId)
                        ->where('division_id',$divisionId)
                        ->where('district_id',$districtId)
                        ->where('area_id',$areaId)
                        ->get();

        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'count'=>count($subAreas),
            'subAreas' => $subAreas
        ));

        return $response;

    }
}