<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/8/2016
 * Time: 1:00 PM
 */

namespace App\Http\Controllers;


use App\Entities\Division;
use App\Utils\Response;

class DivisionController extends RestController
{
    public function index()
    {
        if(!empty($this->request)){
            $apiName = $this->request->apiName;
            return $this->$apiName($this->request->countryId);
        }
    }

    private function getDivisions($countryId){

        $divisions = Division::where('country_id',$countryId)->get();

        $response = new Response($this->request->apiName);
        $response->setResponse(array(
            'count' => count($divisions),
            'divisions' => $divisions
        ));

        return $response;
    }
}