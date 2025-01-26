<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Helpers\URL;
use EvolutionCMS\EvoUser\Services\Service;
use \EvolutionCMS\UserManager\Services\UserManager;

class ProfileEdit extends Service
{
    public function process($params = [])
    {
        $errors = [];

        $uid = $params['user'] ?? 0;

        if (request()->has(['fullname']) || request()->has(['edit_profile'])) {
            $data = $this->makeData();

            $data['id'] = $uid;

            $customErrors = $this->makeCustomValidator($data);

            if (!empty($customErrors)) {
                $errors['customErrors'] = $customErrors;
            } /* else if (empty($data['id'])) {
                $errors['accessError'] = 'access denied';
            } */else {
                $data = $this->callPrepare($data);
                try {
                    $user = (new UserManager())->edit($data, true, false);
                    if (!empty($user)) {
                        $user = json_decode($user, 1);
                        if (!empty($user['id'])) {
                            //сохраняем TV пользователя
                            $userTVs = (new UserManager())->saveValues($data, true, false);
                        }
                        if (request()->has(['chpwd'])) {
                            //пытаемся сменить пароль
                            try {
                                $hash = (new UserManager())->changePassword($data);
                            } catch (\EvolutionCMS\Exceptions\ServiceValidationException $exception) {
                                $validateErrors = $exception->getValidationErrors(); //Получаем все ошибки валидации
                                $errors['validation'] = $validateErrors;
                            } catch (\EvolutionCMS\Exceptions\ServiceActionException $exception) {
                                $errors['common'][] = $exception->getMessage();
                            }
                        }
                    }
                } catch (\EvolutionCMS\Exceptions\ServiceValidationException $exception) {
                    $validateErrors = $exception->getValidationErrors(); //Получаем все ошибки валидации
                    $errors['validation'] = $validateErrors;
                } catch (\EvolutionCMS\Exceptions\ServiceActionException $exception) {
                    $errors['common'][] = $exception->getMessage();
                }
            }
        } else {
            $errors['common'][] = $this->trans('common_required_fields');
        }
        if (!empty($errors)) {
            $response = ['status' => 'error', 'errors' => $errors];
        } else {
            $response = ['status' => 'ok', 'message' => $this->trans('message_profile_edited')];

            $redirectId = $this->getCfg('ProfileEditRedirectId');
            $response['redirect'] = URL::makeUrl($redirectId);
        }
        return $this->makeResponse($response);
    }

    protected function makeData()
    {
        $data = [];
        if (request()->has(['fullname'])) {
            $fullname = $this->clean(request()->input("fullname"), 'fullname');
            $data = ['fullname' => $fullname];
        }
        $data = $this->injectAddFields($data);
        return $data;
    }
}
