<?php

namespace App\Http\Middleware;

use App\Models\Location;
use Closure;

class getLocation
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

        if($request->openid){
            return $next($request);
        }

        $ip=$request->ip();
        if(!Location::where('ip',$ip)->exists()){
            $area =getLocationByIp($ip);
            $device =getDevice();
            $net = getInternet()['net'];

            Location::create([
                'ip'=>$ip,
                'location'=>$area,
                'device'=>$device.'/'.$net
            ]);
        }else{
            $location = Location::where('ip',$ip)->first();
            if($location->block){
                return redirect('/err');
            }
        }

        return $next($request);
    }

}
