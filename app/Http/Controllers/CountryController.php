<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/7/2016
 * Time: 4:23 PM
 */

namespace App\Http\Controllers;

use App\Entities\Country;
use App\Utils\AES_Engine;
use App\Utils\Response;
use Illuminate\Support\Facades\Config;

class CountryController extends RestController
{
    public function index()
    {
        if(!empty($this->request)){
            $apiName = $this->request->apiName;
            return $this->$apiName();
        }

    }

    private function getCountries()
    {
        $countries = Country::all();

        $response = new Response($this->request->apiName);
        $response->setResponse(array(
           'count'=>count($countries),
           'countries'=>$countries
        ));

        return $response;
    }
}