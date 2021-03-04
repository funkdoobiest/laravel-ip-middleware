<?php

namespace Orkhanahmadov\LaravelIpMiddleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;

class BlacklistMiddleware extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param array $blacklist
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$blacklist)
    {
        if ($this->shouldCheck() && IpUtils::checkIp($this->clientIp($request), $this->ipList($blacklist)) ) {
            $this->abort();
        }

        return $next($request);
    }
}
