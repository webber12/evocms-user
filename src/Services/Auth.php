<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Helpers\URL;
use EvolutionCMS\EvoUser\Services\Service;
use \EvolutionCMS\UserManager\Services\UserManager;

class Auth extends Service
{
    public function process($params = [])
    {
        $errors = [];

        if (request()->has(['username', 'password'])) {

            $data = $this->makeData();

            $customErrors = $this->makeCustomValidator($data);

            if (!empty($customErrors)) {
                $errors['customErrors'] = $customErrors;
            } else {
                $data = $this->callPrepare($data);
                try {
                    $user = (new UserManager())->login($data, true, false);
                } catch (\EvolutionCMS\Exceptions\ServiceValidationException $exception) {
                    $validateErrors = $exception->getValidationErrors(); //Получаем все ошибки валидации
                    $errors['validation'] = $validateErrors;
                } catch (\EvolutionCMS\Exceptions\ServiceActionException $exception) {
                    $errors['common'][] = $exception->getMessage();
                }
            }
        } else {
            $errors['common'][] = 'no required fields';
        }
        if (!empty($errors)) {
            $response = ['status' => 'error', 'errors' => $errors];
        } else {
            $response = ['status' => 'ok', 'message' => $this->trans('message_success_auth')];

            $redirectId = $this->getCfg('AuthRedirectId');
            $response['redirect'] = URL::makeUrl($redirectId);
        }
        return $this->makeResponse($response);
    }

    protected function makeData()
    {
        $username = $this->clean(request()->input("username"));
        $password = $this->clean(request()->input("password"));
        $data = ['username' => $username, 'password' => $password];
        return $data;
    }
}
