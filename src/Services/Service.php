<?php

namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Helpers\Response;
use Illuminate\Http\Request;
use EvolutionCMS\Models\UserAttribute;
use EvolutionCMS\UserManager\Services\UserManager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class Service
{
    protected $user = [];

    protected $errors = [];

    protected $config = [];

    public function __construct($config = [])
    {
        if(isset($_GET['logout'])) {
            (new UserManager())->logout();
            $logoutId = $this->getCfg( "LogoutRedirectId", 0);
            if (!empty($logoutId) && is_numeric($logoutId)) {
                evo()->sendRedirect(evo()->makeUrl($logoutId));
            }
        }

        $this->loadConfig($config);
        $this->loadCurrentUser();
    }

    public function process($params = [])
    {

    }

    protected function makeResponse($arr = [])
    {
        $classname = $this->getClassName();
        $responseType = isset($this->config[ $classname . 'ResponseType' ]) ? $this->config[ $classname . 'ResponseType' ] : 'json';
        switch($responseType) {
            case 'array':
                $response = $arr;
                break;
            default:
                $response = Response::send($arr);
                break;
        }
        return $response;
    }

    protected function loadCurrentUser()
    {
        //плейсхолдер ставится в момент проверки доступа в middleware EvoUserAccess
        $user = evo()->getPlaceholder('evocms-user');
        $this->user = !empty($user) ? $user : [];
        return true;
    }

    public function getUser()
    {
        //print_r($this->user);
        return $this->user;
    }

    protected function getCfg($key, $default = false)
    {
        if(array_key_exists($key, $this->config)) {
            $value = $this->config[$key];
        } else {
            $value = config( "evocms-user." . $key, $default);
        }
        return $value;
    }

    protected function clean($str)
    {
        return e(trim($str));
    }

    protected function injectAddFields($data = [])
    {
        $classname = $this->getClassName();
        $fields = $this->getCfg($classname . 'CustomFields', []);
        if(!empty($fields)) {
            foreach($fields as $field) {
                if (request()->has([$field])) {
                    $data[$field] = $this->clean(request()->input($field));
                }
            }
        }
        return $data;
    }

    protected function makeCustomValidator($data)
    {
        $errors = [];
        $classname = $this->getClassName();
        $rules = $this->getCfg($classname . 'CustomRules', []);
        $messages = $this->getCfg($classname . 'CustomMessages', []);
        if (!empty($rules) && !empty($messages)) {
            $validator = Validator::make($data, $rules, $messages);
            $errors = $validator->errors()->toArray();
        }
        return $errors;
    }

    protected function callPrepare($data)
    {
        $classname = $this->getClassName();
        $prepare = $this->getCfg($classname . 'Prepare', '');
        if (!empty($prepare) && is_callable($prepare)) {
            $data = call_user_func($prepare, $data);
        }
        return $data;
    }

    protected function getClassName()
    {
        return substr(strrchr(get_called_class(), "\\"), 1);
    }

    protected function reloadUser($uid)
    {
        $service = new UserManager();
        $user = $service->get($uid);
        $tvs = $service->getValues(['id' => $uid], false, false);
        $arr = $user->attributes->toArray();
        $arr['username'] = $user->username;
        $arr['role'] = evo()->db->getValue("SELECT role FROM " . evo()->getFullTablename("user_attributes") . " where internalKey=" . $arr['internalKey']);
        $arr['tvs'] = $tvs;
        return $arr;
    }

    protected function loadConfig($config)
    {
        $classname = $this->getClassName();
        foreach($config as $k => $v) {
            $k = Str::ucfirst(trim($k));
            if(strpos($k, $classname) === false || strpos($k, $classname) != 0) {
                $this->config[$classname . $k] = $v;
            } else {
                $this->config[$k] = $v;
            }
        }
        return;
    }

    protected function loadCustomConfig($path)
    {
        $classname = $this->getClassName();
        $customConfig = [];
        $path = EVO_CORE_PATH . 'custom/evocms-user/configs/' . $path;
        if(is_file($path)) {
            $rules = include_once($path);
            foreach($rules as $k => $v) {
                $k = Str::ucfirst(trim($k));
                if(strpos($k, $classname) === false || strpos($k, $classname) != 0) {
                    $customConfig[$classname . $k] = $v;
                } else {
                    $customConfig[$k] = $v;
                }
            }

        }
        $this->config = array_merge($this->config, $customConfig);
        return;
    }

}
