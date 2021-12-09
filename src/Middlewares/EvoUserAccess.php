<?php
namespace EvolutionCMS\EvoUser\Middlewares;

use Closure;
use EvolutionCMS\EvoUser\Helpers\CheckAccess;

class EvoUserAccess
{
    public function handle($request, Closure $next, $serviceName = '', $id = false)
    {
        $params = $request->route()->parameters() ?? [];
        $access = (new CheckAccess($serviceName, $params))->checkRules();
        if(!$access) {
            return response()->json([ 'error' => 'access denied' ]);
        } else {
            return $next($request);
        }
    }
}

