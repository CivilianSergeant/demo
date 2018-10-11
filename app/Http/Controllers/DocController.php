<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 9/26/2016
 * Time: 1:44 PM
 */

namespace app\Http\Controllers;



use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DocController extends Controller
{
    public function __construct()
    {
        $this->middleware('doc');
    }

    public function index()
    {
        return view('doc.index',[]);
    }

    public function apiHome()
    {
        return view('doc.api-home',[]);
    }

    public function processFlow()
    {
        return view('doc.process-flow',[]);
    }

    public function register(Request $request)
    {
        $route = $request->server->get('REQUEST_URI');
        $route = explode("/",substr($route,1));
        return view('doc.api-doc',['route'=>$route[1]]);
    }

    public function confirm(Request $request)
    {
        $route = $request->server->get('REQUEST_URI');
        $route = explode("/",substr($route,1));
        return view('doc.api-doc',['route'=>$route[1]]);
    }

    public function programs(Request $request)
    {
        $route = $request->server->get('REQUEST_URI');
        $route = explode("/",substr($route,1));
        return view('doc.api-doc',['route'=>$route[1]]);
    }

    /*public function vodPrograms()
    {
        return view('doc.vod-programs',[]);
    }

    public function catchupPrograms()
    {
        return view('doc.catchup-programs',[]);
    }*/

    public function packages(Request $request)
    {
        $route = $request->server->get('REQUEST_URI');
        $route = explode("/",substr($route,1));
        return view('doc.api-doc',['route'=>$route[1]]);
    }

    /*public function vodPackages()
    {
        return view('doc.vod-packages',[]);
    }

    public function catchupPackages()
    {
        return view('doc.catchup-packages',[]);
    }*/

    public function categories(Request $request)
    {
        $route = $request->server->get('REQUEST_URI');
        $route = explode("/",substr($route,1));
        return view('doc.api-doc',['route'=>$route[1]]);
    }

    /*public function vodCategories()
    {
        return view('doc.vod-categories',[]);
    }

    public function catchupCategories()
    {
        return view('doc.catchup-categories',[]);
    }*/

    public function subCategorise(Request $request)
    {
        $route = $request->server->get('REQUEST_URI');
        $route = explode("/",substr($route,1));
        return view('doc.api-doc',['route'=>$route[1]]);
    }

    public function heartBeat(Request $request)
    {
        $route = $request->server->get('REQUEST_URI');
        $route = explode("/",substr($route,1));
        return view('doc.api-doc',['route'=>$route[1]]);

    }

    public function viewingChannel(Request $request)
    {
        $route = $request->server->get('REQUEST_URI');
        $route = explode("/",substr($route,1));
        return view('doc.api-doc',['route'=>$route[1]]);
    }
}