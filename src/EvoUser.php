<?php

namespace EvolutionCMS\EvoUser;

use Illuminate\Support\Str;


class EvoUser
{
    public function __construct(){}

    public function do($ServiceName, $params = [], $config = [])
    {
        $config = array_merge($config, ['ResponseType' => 'array']);
        $className = $this->getClassName($ServiceName);
        $methodName = 'process';
        if(isset($params['methodName'])) {
            $methodName = $params['methodName'];
            unset($params['methodName']);
        }
        if ($ServiceName == 'user') {
            $response = $this->getCurrentUser($params);
        } else if (is_callable([ $className, $methodName ])) {
            $response = (new $className($config))->$methodName($params);
        } else {
            $response = ['error' => 'undefined method ' . $methodName . ' from ' . $className];
        }
        return $response;
    }

    protected function getClassName($ServiceName)
    {
        if(Str::contains($ServiceName, '-')) {
            $ServiceName = Str::replace('-', '_', $ServiceName);
            $ServiceName = Str::camel($ServiceName);
        }

        $ServiceName = Str::ucfirst($ServiceName);

        return __NAMESPACE__ . "\\Services\\" . $ServiceName;
    }

    protected function getCurrentUser($params)
    {
        if(empty($params)) {
            $params = ['web'];
        }
        foreach($params as $context) {
            $user = evo()->getLoginUserID($context);
            if(!empty($user)) return $user;
        }
        return null;
    }

}
