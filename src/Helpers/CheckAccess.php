<?php
namespace EvolutionCMS\EvoUser\Helpers;

use EvolutionCMS\Models\SiteContent;
use EvolutionCMS\UserManager\Services\UserManager;

class CheckAccess
{
    protected $serviceName;

    protected $params;

    public function __construct($serviceName = '', $params = [])
    {
        $this->params = $params;
        $this->serviceName = $serviceName;
    }

    public function checkRules()
    {
        $flag = false;
        $rules = $this->getRules();
        // если проверка отменена в правилах, просто возвращаем true
        // и при необходимости разруливаем на уровне кастомной валидации
        if (isset($rules['withoutRules']) && $rules['withoutRules'] === true) {
            return true;
        }

        $qParams = $this->getParamsFromQuery();
        if (!empty($rules)) {
            $user = $this->isLogged($rules);
            if (!empty($user)) {
                $role = $user['role'];
                $roles = $rules['roles'] ?? [];
                $flag = $this->checkCurrent($user['id'], $qParams['user'], $rules) || $this->checkRoles($role, $roles);
                if (!$flag && !empty($rules['custom']) && is_callable($rules['custom'])) {
                    $flag = call_user_func($rules['custom'], ['user' => $user, 'rules' => $rules]);
                }
            }
        }
        return $flag;
    }

    protected function getRules()
    {
        $default = config('evocms-user.CommonAccessRules', []);
        $custom = config('evocms-user.' . $this->serviceName . 'AccessRules', []);
        return array_merge($default, $custom);
    }

    protected function isLogged($rules)
    {
        $user = [];
        $context = !empty($rules['context']) ? $rules['context'] : 'web';
        if ($context == 'all') {
            $uid = evo()->getLoginUserID('web') ?: evo()->getLoginUserID('mgr');
        } else {
            $uid = evo()->getLoginUserID($context);
        }
        if ($uid) {
            $user = $this->loadUser($uid);
        }
        return $user;
    }

    protected function loadUser($uid, $reload = true)
    {
        $user = $this->getUser($uid);
        if (empty($user) || ($user['id'] != $uid && $reload)) {
            $user = $this->reloadUser($uid);
        }
        $this->setUser($user);
        return $user;
    }

    protected function getUser()
    {
        $user = evo()->getPlaceholder('evocms-user') ?: '[]';
        return json_decode($user, 1);
    }

    protected function setUser($user)
    {
        return evo()->setPlaceholder('evocms-user', json_encode($user));
    }

    protected function reloadUser($uid)
    {
        $service = new UserManager();
        $user = $service->get($uid);
        $tvs = $service->getValues(['id' => $uid], false, false);
        $arr = $user->attributes->toArray();
        $arr['username'] = $user->username;
        $arr['id'] = $user->id;
        $arr['role'] = evo()->db->getValue("SELECT role FROM " . evo()->getFullTablename("user_attributes") . " where internalKey=" . $arr['internalKey']);
        $arr['tvs'] = $tvs;
        return $arr;
    }

    protected function checkCurrent($uid, $query_user, $rules)
    {
        $flag = true;
        if (!empty($rules['current'])) {
            //если правило задано и текущий юзер не равен запрашиваемому
            if ($query_user != $uid) {
                $flag = false;
            }
        } elseif (array_key_exists('current', $rules) && $rules['current'] == false) {
            //правило прямо задано в false
            $flag = false;
        } elseif (!array_key_exists('current', $rules)) {
            //правило вообще не задано
            $flag = false;
        }
        return $flag;
    }

    protected function checkRoles($role, $roles)
    {
        $flag = true;
        if (empty($role)) {
            $flag = false;
        } else {
            if (!empty($roles) && !empty($role)) {
                $flag = in_array($role, $roles);
            }
        }
        return $flag;
    }

    protected function getParamsFromQuery()
    {
        $arr = [];
        $user = $this->params['user'] ?? -1;
        $id = $this->params['id'] ?? -1;
        switch ($this->serviceName) {
            //есть id документа, берем id владельца
            case 'DocumentEdit':
            case 'DocumentObject':
                $arr['id'] = $id;
                $res = SiteContent::select(['createdby'])
                    ->where('id', $id);
                if (config("evocms-user." . $this->serviceName . "OnlyActive", false)) {
                    $res = $res->active();
                }
                if (config("evocms-user." . $this->serviceName . "ShowUndeleted", true)) {
                    $res = $res->where('deleted', 0);
                }
                $res = $res->limit(1)->get()->toArray();
                $uid = -1;
                if (count($res) == 1) {
                    $uid = $res[0]['createdby'];
                }
                $arr['user'] = $uid;
                break;
            //просматривают все документы только роли
            case 'DocumentList':
            //документы могут создавать только по роли
            case 'DocumentCreate':
            //отправка форм также разруливается ролями
            case 'SendFormAuth':
            //список всех заказов разруливается ролями
            case 'OrderList':
                $arr['user'] = -1;
                break;

            case 'OrderInfo':
            case 'OrderCancel':
            case 'OrderRepeat':
                $arr['id'] = $id;
                $uid = evo()->db->getValue("select customer_id from " . evo()->getFullTableName("commerce_orders") . " where id=" . $id . " LIMIT 0,1");
                $arr['user'] = $uid ?: 0;
                break;
            case 'DocumentInfo':
                $arr['id'] = $id;
                $arr['user'] = $user;
                break;
            default:
                $arr['user'] = $user;
                break;
        }
        return $arr;
    }
}
