<?php

namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Helpers\Response;
use Illuminate\Http\Request;
use EvolutionCMS\Models\UserAttribute;
use EvolutionCMS\UserManager\Services\UserManager;
use Illuminate\Support\Facades\Validator;

class Service
{
    protected $user = [];

    protected $errors = [];

    protected $config = [];

    public function __construct($config = [])
    {
        $this->config = $config;
        if(isset($_GET['logout'])) {
            (new UserManager())->logout();
            $logoutId = $this->getCfg( "LogoutRedirectId", 0);
            if (!empty($logoutId) && is_numeric($logoutId)) {
                evo()->sendRedirect(evo()->makeUrl($logoutId));
            }
        }
        $this->loadUser();
        if(!$this->checkAccess()) {
            $this->errors[] = 'access denied';
        }
    }

    public function process()
    {

    }

    protected function makeResponse($arr = [])
    {
        $response = Response::send($arr);
        return $response;
    }

    protected function checkAccess()
    {
        $flag = false;
        if(!empty($this->user) && $this->checkAccessRules()) {
            $flag = true;
        }
        return $flag;
    }

    protected function loadUser()
    {
        $userId = $this->isLogged();
        //echo $userId;
        if(!empty($userId)) {
            $flag = true;
            $service = new UserManager();
            $user = $service->get($userId);
            $tvs = $service->getValues(['id' => $userId], false, false);
            $attributes = $user->attributes->toArray();
            $this->user = $attributes;
            $this->user['username'] = $user->username;
            $this->user['role'] = evo()->db->getValue("SELECT role FROM " . evo()->getFullTablename("user_attributes") . " where internalKey=" . $attributes['internalKey']);
            $this->user['tvs'] = $tvs;
        }
        return true;
    }

    protected function checkAccessRules()
    {
        return true;
    }

    protected function isLogged()
    {
        return evo()->getLoginUserId('web');
    }

    protected function checkErrors()
    {
        return !empty($this->errors);
    }

    public function getUser()
    {
        return $this->user;
    }

    protected function getCfg($key, $default = false)
    {
        return config( "evouser." . $key, $default);
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
}
