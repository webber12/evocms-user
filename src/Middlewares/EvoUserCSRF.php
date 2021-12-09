<?php
namespace EvolutionCMS\EvoUser\Middlewares;

use Closure;

class EvoUserCSRF
{
    public function handle($request, Closure $next)
    {
        $access = $this->checkCSRF();
        if (!$access) {
            return response()->json(['error' => 'access denied by csrf']);
        } else {
            return $next($request);
        }
    }

    protected function checkCSRF()
    {
        $flag = false;
        $token = csrf_token();
        $_token = request()->input('_token', false);
        if (!empty($token) && !empty($_token) && $token == $_token) {
            $flag = true;
        }
        return $flag;
    }
}
