<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use Illuminate\Support\Facades\DB;

class EasyNewsLetter extends Service
{

    public function process($params = [])
    {
        $errors = [];

        if (request()->has(['email'])) {
            $this->loadCustomConfig('forms/easynewsletter.php');
            $data = $this->makeData();
            $customErrors = $this->makeCustomValidator($data);

            if (!empty($customErrors)) {
                $errors['customErrors'] = $customErrors;
            } else {
                //тут будем сохранять
                $id = DB::table('easynewsletter_subscribers')->insertGetId($data);
                if (!$id) {
                    $errors['common'][] = trans('evocms-user-core::messages.common_server_error');
                }
            }
        } else {
            $errors['common'][] = trans('evocms-user-core::messages.common_required_field', ['field' => 'Email']);
        }
        if (!empty($errors)) {
            $response = ['status' => 'error', 'errors' => $errors];
        } else {
            $response = ['status' => 'ok', 'message' => trans('evocms-user-core::messages.message_form_sent')];
        }
        return $this->makeResponse($response);
    }

    protected function makeData()
    {
        $defaults = [
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'lastnewsletter' => '',
            'created' => date("Y-m-d"),
        ];
        $data = [];
        $fields = array_keys($defaults);
        foreach ($fields as $field) {
            if (request()->has($field)) {
                $data[$field] = e(request()->input($field));
            }
        }
        $data = array_merge(
            $defaults,
            $data,
        );
        return $data;
    }
}
