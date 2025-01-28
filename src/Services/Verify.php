<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Helpers\URL;
use EvolutionCMS\EvoUser\Services\Service;
use EvolutionCMS\Models\User;
use \EvolutionCMS\UserManager\Services\UserManager;

class Verify extends Service
{
    public function process($params = [])
    {
        try {
            $user = User::find($params['user']);
            if ($user) {
                (new UserManager())->verified(['username' => $user->username, 'verified_key' => $params['key']]);
            }
        } catch (\EvolutionCMS\Exceptions\ServiceValidationException $exception) {
            $validateErrors = $exception->getValidationErrors(); //Получаем все ошибки валидации
            $errors['validation'] = $validateErrors;
        } catch (\EvolutionCMS\Exceptions\ServiceActionException $exception) {
            $errors['common'][] = $exception->getMessage();
        }

        $redirectId = $this->getCfg('RegisterVerifyUserPageId', evo()->getConfig('site_start'));

        if (!empty($errors)) {
            $url = URL::makeUrl($redirectId, '', 'fail');
        } else {
            if ($this->getCfg("VerifyUserAuth", false) && !empty($user)) {
                $auth = (new UserManager())->loginById(['id' => $user->id]);
            }

            $url = URL::makeUrl($redirectId, '', 'success');
        }

        if (!empty($url)) {
            $response = redirect($url);
        }

        return $response;
    }
}
