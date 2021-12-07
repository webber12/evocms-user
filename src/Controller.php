<?php
namespace EvolutionCMS\EvoUser;

use EvolutionCMS\UserManager\Services\UserManager;

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

    public function ProfileEdit($user)
    {
        return $this->loadService(__FUNCTION__)->process([ 'user' => $user ]);
    }

    public function ProfileInfo($user)
    {
        return $this->loadService(__FUNCTION__)->process([ 'user' => $user ]);
    }

    public function DocumentList()
    {
        return $this->loadService(__FUNCTION__)->process();
    }

    public function DocumentListUser($user)
    {
        return $this->loadService(__FUNCTION__)->process([ 'user' => $user ]);
    }

    public function DocumentInfo($user, $id)
    {
        return $this->loadService(__FUNCTION__)->process([ 'user' => $user, 'id' => $id ]);
    }

    public function DocumentObject($id)
    {
        return $this->loadService(__FUNCTION__)->process([ 'id' => $id ]);
    }

    public function DocumentCreate()
    {
        return $this->loadService(__FUNCTION__)->process();
    }

    public function DocumentEdit($id)
    {
        return $this->loadService(__FUNCTION__)->process([ 'id' => $id ]);
    }

    public function SendForm()
    {
        return $this->loadService(__FUNCTION__)->process();
    }

    public function OrderList()
    {
        return $this->loadService(__FUNCTION__)->process();
    }

    public function OrderListUser($user)
    {
        return $this->loadService(__FUNCTION__)->process([ 'user' => $user ]);
    }

    public function OrderInfo($id)
    {
        return $this->loadService(__FUNCTION__)->process([ 'id' => $id ]);
    }

    public function OrderCancel($id)
    {
        return $this->loadService(__FUNCTION__)->process([ 'id' => $id ]);
    }

    public function OrderRepeat($id)
    {
        return $this->loadService(__FUNCTION__)->process([ 'id' => $id ]);
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
        /*$config = [];
        if (file_exists(__DIR__ . '/Configs/default.php')) {
            $config = include_once(__DIR__ . '/Configs/default.php');
        }
        if (file_exists(MODX_BASE_PATH . 'core/custom/evocms-user/configs/evouser.php')) {
            $custom = include_once(MODX_BASE_PATH . 'core/custom/evocms-user/configs/evouser.php');
            $config = array_merge($config, $custom);
            evo()->logEvent(1,1,print_r($config, 1), 'config2');
        }
        return $config;
        */
    }

    protected function loadService($name)
    {
        $serviceName = config('evouser.' . $name . 'Service');
        $service = !empty($serviceName) ? $serviceName : "\\EvolutionCMS\\EvoUser\\Services\\" . $name;
        return new $service($this->config);
    }

}
