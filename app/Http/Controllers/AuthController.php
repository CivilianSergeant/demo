<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/1/2016
 * Time: 1:51 PM
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;


class AuthController extends Controller
{

    public function __construct()
    {
        //$this->middleware('logged.in',['except'=>'logout']);
    }

    public function index(Request $request)
    {

        if($request->getSession()->has('user_session')){
            return redirect('simulator');
        }
        return view('auth.index');
    }

    public function authenticate(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');


        if($username == Config::get('app.username') && $password == Config::get('app.password')){
            $request->getSession()->set('user_session',md5($username.$password));
            return redirect('simulator');
        }else{
            return redirect('/')->with('error','Invalid Login information')->withInput();
        }

    }

    public function logout(Request $request)
    {
        $request->getSession()->clear();
        return redirect('/');
    }
}