<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/1/2016
 * Time: 4:33 PM
 */

namespace App\Http\Middleware;

use Closure;

class LoggedIn
{
    public function handle($request, Closure $next)
    {
        if($request->getSession()->has('user_session')){
            return redirect('doc');
        }
        return redirect('/');
    }
}