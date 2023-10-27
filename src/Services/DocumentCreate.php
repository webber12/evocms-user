<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\DocumentManager\Services\DocumentManager;
use EvolutionCMS\EvoUser\Services\Service;


class DocumentCreate extends Service
{

    public function process($params = [])
    {
        $errors = [];

        $currentUser = $this->getUser();

        if (request()->has(['pagetitle'])) {

            $data = $this->makeData();

            $customErrors = $this->makeCustomValidator($data);

            if (!empty($customErrors)) {
                $errors['customErrors'] = $customErrors;
            } else {
                $defaults = $this->getCfg("DocumentCreateDefaults", []);
                $data = array_merge($defaults, $data);
                $data['createdby'] = $currentUser['id'] ?? 0;
                $data['createdon'] = date("U");
                if(empty($data['template'])) {
                    $data['template'] = evo()->getConfig('default_template');
                }
                $data = $this->callPrepare($data);
                try {
                    $document = (new DocumentManager())->create($data);
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
            $response = [ 'status' => 'ok', 'message' => 'success document create' ];
        }
        return $this->makeResponse($response);
    }

    protected function makeData()
    {
        $data = [];
        foreach($_POST as $k => $v) {
            $k = e($k);
            $v = is_string($v) ? $this->clean($v, $k) : $this->clean(implode('||', $v), $k);
            $data[$k] = $v;
        }
        return $data;
    }
}
