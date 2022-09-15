<?php

namespace EvolutionCMS\EvoUser;

use EvolutionCMS\EvoUser\Helpers\CheckAccess;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;


class EvoUser
{

    public $checkAccess = true;

    public function __construct(){}

    public function withoutRules()
    {
        $this->checkAccess = false;
        return $this;
    }

    public function do($serviceName, $params = [], $config = [])
    {
        $serviceName = Str::ucfirst($serviceName);
        $config = array_merge($config, ['ResponseType' => 'array']);
        $className = $this->getClassName($serviceName);
        $methodName = 'process';
        if(isset($params['methodName'])) {
            $methodName = $params['methodName'];
            unset($params['methodName']);
        }
        if ($serviceName == 'User') {
            $response = $this->getCurrentUser($params);
        } else if (class_exists($className) && is_callable([ new $className($config), $methodName ])) {
            if ($this->checkAccess == true) {
                $access = (new CheckAccess($serviceName, $params))->checkRules();
                if (!$access) {
                    $response = ['error' => 'access denied'];
                } else {
                    $response = (new $className($config))->$methodName($params);
                }
            } else {
                $response = (new $className($config))->$methodName($params);
            }
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
