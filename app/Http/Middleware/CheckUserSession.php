<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/1/2016
 * Time: 3:12 PM
 */

namespace App\Http\Middleware;

use Closure;

class CheckUserSession
{
    public function handle($request,Closure $next)
    {
        if(!$request->getSession()->has('user_session')){
            return redirect('/');
        }

        return $next($request);
    }
}