<?php

namespace App\Http\Middleware;

use Closure;
use Response;

class IPMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)

    {
        $allowed_ip_addresses = "192.168.1.104, 127.0.0.1,85.148.209.9,141.95.24.80"; // add IP's by comma separated
        $ipsAllow = explode(',', preg_replace('/\s+/', '', $allowed_ip_addresses));

        // check ip is allowed
        if (count($ipsAllow) >= 1) {

            if (!in_array(request()->ip(), $ipsAllow)) {
                // return response
                return Response::json(array(
                    'success' => false,
                    'message' => 'You are blocked to call API!'
                ));

            }

        }

        return $next($request);

    }
}