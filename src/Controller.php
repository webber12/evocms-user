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
        if (empty($this->config) || $reload == false) {
            $this->config = $this->loadConfig();
        }
        return $this->config;
    }

    public function loadConfig()
    {
        $config = [];
        if (file_exists(__DIR__ . '/Configs/default.php')) {
            $config = include_once(__DIR__ . '/Configs/default.php');
        }
        if (file_exists(MODX_BASE_PATH . 'core/custom/evocms-user/configs/config.php')) {
            $custom = include_once(MODX_BASE_PATH . 'core/custom/evocms-user/configs/config.php');
            $config = array_merge($config, $custom);
        }
        return $config;
    }

    protected function loadService($name)
    {
        $service = !empty($this->config[$name . 'Service']) ? $this->config[$name . 'Service'] : "\\EvolutionCMS\\EvoUser\\Services\\" . $name;
        return new $service($this->config);
    }
}
