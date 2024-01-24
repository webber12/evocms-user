<?php

namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use EvolutionCMS\Models\User;
use \EvolutionCMS\UserManager\Services\UserManager;

class Verify extends Service
{
    public function process($params = [])
    {
        try {
            $user = User::find($params['user']);
            if($user) {
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
            $response = redirect(evo()->makeUrl($redirectId, '', 'fail'));
        } else {
            if(!empty($redirectId) && is_numeric($redirectId)) {
                $response = redirect(evo()->makeUrl($redirectId, '', 'success'));
            }
        }

        return $response;
    }
}
