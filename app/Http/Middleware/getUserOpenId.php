<?php

namespace App\Http\Middleware;

use Closure;

class getUserOpenId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        //获取openid

        //用户同意授权,获取code
        $url ='';





        return $next($request);
    }
}
