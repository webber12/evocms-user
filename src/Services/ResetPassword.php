<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use EvolutionCMS\Models\UserAttribute;
use \EvolutionCMS\UserManager\Services\UserManager;

class ResetPassword extends Service
{
    public function process($params = [])
    {
        $errors = [];
        if (request()->has(['email'])) {
            //шаг 1 - генерация хэша по email
            $response = $this->makeHashResponse();
        }
        if (request()->has(['hash', 'password', 'password_confirmation'])) {
            //шаг 2 - генерация хэша по email
            $response = $this->makeChangePasswordResponse();
        }
        return $this->makeResponse($response);
    }

    protected function makeHashResponse()
    {
        if (request()->has(['email'])) {

            $data = $this->makeData();

            $uid = $this->checkUser($data);

            if(!$uid) {
                $customErrors = ['email' => [ $this->trans('custom_user_na') ]];
            }

            if (!empty($customErrors)) {
                $errors['customErrors'] = $customErrors;
            } else {
                try {
                    $hash = (new UserManager())->repairPassword([ 'id' => $uid ]);

                    $sendmailParams = $this->makeSendmailParams($data, $hash);

                    if(!evo()->sendmail($sendmailParams, '', ($_FILES ?? []))) {
                        $errors['fail'][] = $this->trans('common_form_sending_error');
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
            $response = [ 'status' => 'error', 'errors' => $errors ];
        } else {
            $response = [ 'status' => 'ok', 'message' => $this->trans('message_profile_remind1'), 'step' => 1 ];
        }
        return $response;
    }

    protected function makeChangePasswordResponse()
    {
        if (request()->has(['hash', 'password', 'password_confirmation'])) {
            $data = $this->makeData();

            $customErrors = $this->makeCustomValidator($data);

            if (!empty($customErrors)) {
                $errors['customErrors'] = $customErrors;
            } else {
                try {
                    $hash = (new UserManager())->hashChangePassword($data);
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
            $response = ['status' => 'ok', 'message' => $this->trans('message_profile_remind1'), 'step' => 2];
        }
        return $response;
    }

    protected function makeData()
    {
        if (request()->has(['email'])) {
            $email = $this->clean(request()->input("email"), "email");
            $data = ['email' => $email];
        }
        if (request()->has(['hash'])) {
            $fields = ['hash', 'password', 'password_confirmation'];
            foreach($fields as $field) {
                $data[$field] = $this->clean(request()->input($field), $field);
            }
        }
        return $data;
    }

    protected function checkUser($data)
    {
        $uid = false;
        $email = trim($data['email']);
        if(empty($email)) {
            return $uid;
        }
        $res = UserAttribute::select(['internalKey'])->where('email', $email)->limit(1)->get()->pluck('internalKey')->toArray();
        if(count($res) > 0) {
            $uid = $res[0];
        }
        return $uid;
    }

    protected function makeSendmailParams($data, $hash) {
        $ResetPasswordPageId = $this->getCfg('ResetPasswordPageId', evo()->getConfig('site_start'));
        $url = MODX_SITE_URL . ltrim(evo()->makeUrl($ResetPasswordPageId), '/') . '?hash=' . $hash;

        return [
            'to' => trim($data['email']),
            'subject' =>  $this->getCfg('ResetPasswordSubject', $this->trans('message_profile_remind_email_subj')),
            'body' => $this->getCfg('ResetPasswordText', $this->trans('message_profile_remind_email_text')) . ' ' . $url,
        ];
    }
}
