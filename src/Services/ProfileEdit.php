<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use \EvolutionCMS\UserManager\Services\UserManager;
use Illuminate\Support\Facades\Validator;



class ProfileEdit extends Service
{
    public function process($uid = 0)
    {
        $errors = [];

        if (request()->has(['fullname'])) {

            $data = $this->makeData();

            $data['id'] = $uid;

            $customErrors = $this->makeCustomValidator($data);

            if (!empty($customErrors)) {
                $errors['customErrors'] = $customErrors;
            }/* else if (empty($data['id'])) {
                $errors['accessError'] = 'access denied';
            } */else {
                $data = $this->callPrepare($data);
                try {
                    $user = (new UserManager())->edit($data, true, false);
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
            $response = [ 'status' => 'error', 'errors' => $errors ];
        } else {
            $response = [ 'status' => 'ok', 'message' => 'success edit' ];
            $redirectId = $this->getCfg('RegisterRedirectId');
            if(!empty($redirectId) && is_numeric($redirectId)) {
                $response['redirect'] = evo()->makeUrl($redirectId);
            }
        }
        return $this->makeResponse($response);
    }


    protected function makeData()
    {
        $fullname = $this->clean(request()->input("fullname"));
        $data = ['fullname' => $fullname];
        $data = $this->injectAddFields($data);
        return $data;
    }


}


