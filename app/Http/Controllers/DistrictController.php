<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/8/2016
 * Time: 3:30 PM
 */

namespace App\Http\Controllers;


use App\Entities\District;
use App\Utils\Response;

class DistrictController extends RestController
{
    public function index()
    {
        if(!empty($this->request)){
            $apiName = $this->request->apiName;
            $countryId = $this->request->countryId;
            $divisionId = $this->request->divisionId;
            return $this->$apiName($countryId,$divisionId);
        }

    }

    private function getDistricts($countryId, $divisionId)
    {
        $districts = District::where('country_id',$countryId)
                                ->where('division_id',$divisionId)
                                ->get();

        $response = new Response($this->request->apiName);
        $response->setResponse(array(
           'count' => count($districts),
           'districts' => $districts
        ));

        return $response;
    }
}