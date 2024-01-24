<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use \EvolutionCMS\UserManager\Services\UserManager;
use EvolutionCMS\Models\UserAttribute;

class VerifyUser extends Service
{

    public function process($params = [])
    {
        $errors = [];
        if (request()->has('email')) {

            $data = $this->makeData();

            $customErrors = $this->makeCustomValidator($data);

            if (!empty($customErrors)) {
                $errors['customErrors'] = $customErrors;
            } else {
                try {
                    $user = $this->checkUser($data);
                    if($user && !$user->verified) {
                        if($this->getCfg("RegisterNotify", true) && !empty($data['email'])) {
                            //уведомление о регистрации
                            $subject = $this->getCfg("RegisterNotifySubject", '@CODE:' . evo()->getConfig('emailsubject'));
                            $body = $this->getCfg("RegisterNotifyBody", '@CODE:' . evo()->getConfig('websignupemail_message'));
                            if(!empty($this->getCfg('RegisterVerifyUser', false))) {
                                $verifiedKey = (new UserManager())->getVerifiedKey([ 'id' => $user->id ]);
                                $verifyText = $this->getCfg("RegisterVerifyText", '@CODE:Для завершения процесса регистрация перейдите по указанной ссылке [+url+]');
                                $verifyUrl = evo()->getConfig('site_url') . 'evocms-user/verify/' . $user->id . '/' . $verifiedKey->verified_key;
                                $verifyText = app('DLTemplate')->parseChunk($verifyText, [ 'url' => $verifyUrl ]);
                                $body .= '<br><br>' . $verifyText;
                            }
                            $fields = [
                                'uid' => $user->username,
                                'sname' => evo()->getConfig('site_name'),
                                'pwd' => 'Не высылается в целях безопасности',
                                'surl' => evo()->getConfig('site_url'),
                            ];
                            $params = [
                                'to' => $user->email,
                                'subject' => app('DLTemplate')->parseChunk($subject, $fields),
                                'body' => app('DLTemplate')->parseChunk($body, $fields),
                            ];
                            evo()->sendmail($params, '', []);
                        }
                    } else {
                        $errors['fail'][] = 'verify fail';
                    }
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
            // $response = [ 'status' => 'ok', 'message' => 'success verify' ];
        }
        return $this->makeResponse($response);
    }
    
    protected function checkUser($data)
    {
        $uid = false;
        $email = trim($data['email']);
        if(empty($email)) {
            return $uid;
        }
        $res = UserAttribute::where('email', $email)->first();
        
        return $res;
    }


    protected function makeData()
    {
        $email = $this->clean(request()->input("email"), "email");
        $data = ['email' => $email];
        $data = $this->injectAddFields($data);
        return $data;
    }
}

