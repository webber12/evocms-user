<?php
namespace EvolutionCMS\EvoUser\Middlewares;

use Closure;
use EvolutionCMS\Models\SiteContent;
use EvolutionCMS\Models\UserAttribute;
use EvolutionCMS\UserManager\Services\UserManager;

class EvoUserAccess
{
    public function handle($request, Closure $next, $action = '', $id = false)
    {
        $access = $this->checkRules($action, $id);
        if(!$access) {
            return response()->json([ 'error' => 'access denied' ]);
        } else {
            return $next($request);
        }
    }

    protected function checkRules($action)
    {
        $flag = false;
        $rules = $this->getRules($action);
        $qParams = $this->getParamsFromQuery($action);
        if(!empty($rules)) {
            $user = $this->isLogged($rules);
            if(!empty($user)) {
                $role = $user['role'];
                $roles = $rules['roles'] ?? [];
                $flag = $this->checkCurrent($user['id'], $qParams['user'], $rules) || $this->checkRoles($role, $roles);
                if(!$flag && !empty($rules['custom']) && is_callable($rules['custom'])) {
                    $flag = call_user_func($rules['custom'], [ 'user' => $user, 'rules' => $rules ]);
                }
            }
        }
        return $flag;
    }

    protected function getRules($action)
    {
        $default = config('evocms-user.CommonAccessRules', []);
        $custom = config('evocms-user.' . $action . 'AccessRules', []);
        return array_merge($default, $custom);
    }

    protected function isLogged($rules)
    {
        $user = [];
        $context = !empty($rules['context']) ? $rules['context'] : 'web';
        if($context == 'all') {
            $uid = evo()->getLoginUserID('web') ?: evo()->getLoginUserID('mgr');
        } else {
            $uid = evo()->getLoginUserID($context);
        }
        if($uid) {
            $user = $this->loadUser($uid);
        }
        return $user;
    }

    protected function loadUser($uid, $reload = true)
    {
        $user = $this->getUser($uid);
        if(empty($user) || ($user['id'] != $uid && $reload)) {
            $user = $this->reloadUser($uid);
        }
        $this->setUser($user);
        return $user;
    }

    protected function getUser()
    {
        return evo()->getPlaceholder('evocms-user');
    }

    protected function setUser($user)
    {
        return evo()->setPlaceholder('evocms-user', $user);
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

    protected function checkCurrent($uid, $query_user, $rules)
    {
        $flag = true;
        if(!empty($rules['current'])) {
            if ($query_user != $uid) {
                $flag = false;
            }
        }
        return $flag;
    }

    protected function checkRoles($role, $roles)
    {
        $flag = true;
        if(empty($role)) {
            $flag = false;
        } else {
            if (!empty($roles) && !empty($role)) {
                $flag = in_array($role, $roles);
            }
        }
        return $flag;
    }

    protected function getParamsFromQuery($action = '')
    {
        $arr = [];
        $q = trim(request()->input('q'), '/');
        $q = explode('/', $q);
        switch ($action) {
            case 'DocumentCreate':
                //документы могут создавать только по роли
                $arr['user'] = -1;
                break;
            case 'DocumentObject':
                $arr['id'] = array_pop($q);
                $res = SiteContent::select(['createdby'])
                    ->where('id', $arr['id']);
                if(config( "evocms-user.DocumentObjectOnlyActive", false)) {
                    $res = $res->active();
                }
                if(config( "evocms-user.DocumentObjectShowUndeleted", true)) {
                    $res = $res->where('deleted', 0);
                }
                $res = $res->limit(1)->get()->toArray();
                $uid = -1;
                if(count($res) == 1) {
                    $uid = $res[0]['createdby'];
                }
                $arr['user'] = $uid;
                break;
            case 'DocumentInfo':
                $arr['id'] = array_pop($q);
                $arr['user'] = array_pop($q);
                break;
            default:
                $arr['user'] = array_pop($q);
                break;
        }
        return $arr;
    }
}

