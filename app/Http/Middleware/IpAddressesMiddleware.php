<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\IpUtils;

//Models:
use App\Models\IpAccess;

class IpAddressesMiddleware{
    /**
     * Handle an incoming request.
     * Tuto: https://laraveldaily.com/check-access-laravel-project-ip-address/
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $valid_ips = IpAccess::select('id', 'ip_start', 'ip_end')
        ->where('company_id', session('currentCompany'))
        ->get();

        foreach($valid_ips as $ip){
            $range = [$ip->ip_start, $ip->ip_end];
            // if(!$this->isValidIp($ip) && !$this->isValidIpRange($ip, $range)){
            //     return redirect('/');
            // }
            if(!$this->isValidIpRange($request->ip, $range)){
                return redirect('/');
            }
        }


        //04-02-2022: PENDIENTE DE ACABAR

        return $next($request);
    }

    /**
     * Check if the given IP is valid.
     *
     * @param $ip
     * @return bool
     */
    protected function isValidIp($ip){
        return in_array($ip, $this->ips);
    }

    /**
     * Check if the ip is in the given IP-range.
     *
     * @param $ip
     * @return bool
     */
    protected function isValidIpRange($ip, $range){
        return IpUtils::checkIp($ip, $this->ipRanges);
    }
}
