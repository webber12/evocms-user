<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\DocumentManager\Services\DocumentManager;
use EvolutionCMS\EvoUser\Services\Service;
use EvolutionCMS\Models\SiteContent;

class DocumentEdit extends Service
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
                $data = $this->callPrepare($data);
                $currentDoc = $this->getCurrentDocument($params['id']);
                $data['id'] = $params['id'];
                $data['editedby'] = $currentUser['id'];
                $data['editedon'] = date("U");
                $data['alias'] = $data['alias'] ?? $currentDoc['alias'];
                $data['published'] = $data['published'] ?? $currentDoc['published'];
                $data['parent'] = $data['parent'] ?? $currentDoc['parent'];
                try {
                    $document = (new DocumentManager())->edit($data);
                } catch (\EvolutionCMS\Exceptions\ServiceValidationException $exception) {
                    $validateErrors = $exception->getValidationErrors(); //Получаем все ошибки валидации
                    $errors['validation'] = $validateErrors;
                } catch (\EvolutionCMS\Exceptions\ServiceActionException $exception) {
                    $errors['common'][] = $exception->getMessage();
                }
            }
        } else {
            $errors['common'][] = trans('evocms-user-core::messages.common_required_fields');
        }
        if (!empty($errors)) {
            $response = ['status' => 'error', 'errors' => $errors];
        } else {
            $response = ['status' => 'ok', 'message' => trans('evocms-user-core::messages.message_resource_edited')];
        }
        return $this->makeResponse($response);
    }

    protected function makeData()
    {
        $data = [];
        foreach ($_POST as $k => $v) {
            $k = e($k);
            $v = is_string($v) ? $this->clean($v, $k) : $this->clean(implode('||', $v), $k);
            $data[$k] = $v;
        }
        return $data;
    }

    protected function getCurrentDocument($id)
    {
        return SiteContent::query()->withTrashed()->find($id)->toArray();
    }
}
