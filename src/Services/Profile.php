<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use Illuminate\Http\Request;
use EvolutionCMS\EvoUser\Helpers\Response;
use \EvolutionCMS\UserManager\Services\UserManager;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;



class Profile extends Service
{

    public function process()
    {
        if($this->checkErrors()) {
            return $this->makeResponse($this->errors);
            die();
        }
        $errors = [];

        if (request()->has(['fullname'])) {

            $data = $this->makeData();

            $customErrors = $this->makeCustomValidator($data);

            evo()->logEvent(1,1,json_encode($data), 'profile  data');
            evo()->logEvent(1,1,json_encode($this->user), 'profile user');

            if (!empty($customErrors)) {
                $errors['customErrors'] = $customErrors;
            } else if (empty($data['id'])) {
                $errors['accessError'] = 'access denied';
            } else {
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
        $response = $this->makeResponse($response);
        //print_r($data);
        return;
    }


    protected function makeData()
    {
        $fullname = $this->clean(request()->input("fullname"));
        $data = ['fullname' => $fullname];
        $data = $this->injectAddFields($data);
        $data['id'] = $this->user['id'] ?? 0;
        return $data;
    }


}


