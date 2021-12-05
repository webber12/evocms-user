<?php
namespace EvolutionCMS\EvoUser;

class Controller
{
    protected $config = [];

    public function __construct()
    {
        $this->getConfig();
    }

    public function Auth()
    {
        return $this->loadService(__FUNCTION__)->process();
    }

    public function Register()
    {
        return $this->loadService(__FUNCTION__)->process();
    }

    public function Profile()
    {
        return $this->loadService(__FUNCTION__)->process();
    }

    public function getConfig($reload = false)
    {
        if (empty($this->config) || $reload == true) {
            $this->config = $this->loadConfig();
        }
        return $this->config;
    }

    public function loadConfig()
    {
        return [];
    }

    protected function loadService($name)
    {
        $serviceName = config('evouser.' . $name . 'Service');
        $service = !empty($serviceName) ? $serviceName : "\\EvolutionCMS\\EvoUser\\Services\\" . $name;
        return new $service($this->config);
    }
}
