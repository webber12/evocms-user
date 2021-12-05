<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use Illuminate\Http\Request;
use EvolutionCMS\EvoUser\Helpers\Response;
use \EvolutionCMS\UserManager\Services\UserManager;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;



class Register extends Service
{

    public function process($uid = 0)
    {
        $errors = [];
        if (request()->has(['email', 'password'])) {

            $data = $this->makeData();

            $customErrors = $this->makeCustomValidator($data);

            if (!empty($customErrors)) {
                $errors['customErrors'] = $customErrors;
            } else {
                $data = $this->callPrepare($data);
                try {
                    $user = (new UserManager())->create($data, true, false);
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
            $response = [ 'status' => 'ok', 'message' => 'success reg' ];
            $redirectId = $this->getCfg('RegisterRedirectId');
            if(!empty($redirectId) && is_numeric($redirectId)) {
                $response['redirect'] = evo()->makeUrl($redirectId);
            }
        }
        return $this->makeResponse($response);
    }


    protected function makeData()
    {
        $email = $this->clean(request()->input("email"));
        $password = $this->clean(request()->input("password"));
        if(request()->has(['username'])) {
            $username = $this->clean(request()->input("username"));
        } else {
            $username = $email;
        }
        if(request()->has(['password_confirmation'])) {
            $password_confirmation = $this->clean(request()->input("password_confirmation"));
        } else {
            $password_confirmation = $password;
        }

        $data = ['username' => $username, 'password' => $password, 'password_confirmation' => $password_confirmation, 'email' => $email];
        $data = $this->injectAddFields($data);
        return $data;
    }


}

