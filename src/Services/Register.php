<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use \EvolutionCMS\UserManager\Services\UserManager;


class Register extends Service
{

    public function process($params = [])
    {
        $errors = [];
        if (request()->has('email') && (request()->has('password') || request()->has('sendEmail') )) {

            $data = $this->makeData();

            $customErrors = $this->makeCustomValidator($data);

            if (!empty($customErrors)) {
                $errors['customErrors'] = $customErrors;
            } else {
                $data = $this->callPrepare($data);
                try {
                    $user = (new UserManager())->create($data, true, false);
                    if(!empty($user->id) && !empty($data['role_id'])) {
                        //$data['role_id'] готовим в RegisterPrepare
                        $user = (new UserManager())->setRole([ 'id' => $user->id, 'role' => $data['role_id'] ]);
                        //$data['user_groups'] готовим в RegisterPrepare
                        if(!empty($data['user_groups'])) {
                            $user = (new UserManager())->setGroups([ 'id' => $user->id, 'groups' => $data['user_groups'] ]);
                        }
                        //сохраняем TV пользователя
                        $userTVs = (new UserManager())->saveValues(array_merge($data, [ 'id' => $user->id ]), true, false);
                        if(request()->has('sendEmail')) {
                            //необходимо сгенерировать пароль и выслать его на почту
                            $password = (new UserManager())->generateAndSavePassword([ 'id' => $user->id ]);
                            $body = $this->getCfg("RegisterSendPasswordBody", "@CODE:Ваш пароль доступа к сайту " . evo()->getConfig('site_name') . ": [+password+]");
                            $params = [
                                'to' => $data['email'],
                                'subject' => $this->getCfg("RegisterSendPasswordSubject", "Пароль доступа к сайту " . evo()->getConfig('site_name')),
                                'body' => app('DLTemplate')->parseChunk($body, [ 'password' => $password ]),
                            ];
                            evo()->sendmail($params, '', []);
                        }
                        if($this->getCfg("RegisterNotify", true) && !empty($data['email'])) {
                            //уведомление о регистрации
                            $subject = $this->getCfg("RegisterNotifySubject", '@CODE:' . evo()->getConfig('emailsubject'));
                            $body = $this->getCfg("RegisterNotifyBody", '@CODE:' . evo()->getConfig('websignupemail_message'));
                            $fields = [
                                'uid' => $data['username'],
                                'sname' => evo()->getConfig('site_name'),
                                'pwd' => 'Не высылается в целях безопасности',
                                'surl' => evo()->getConfig('site_url'),
                            ];
                            $params = [
                                'to' => $data['email'],
                                'subject' => app('DLTemplate')->parseChunk($subject, $fields),
                                'body' => app('DLTemplate')->parseChunk($body, $fields),
                            ];
                            evo()->sendmail($params, '', []);
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
        //если пароль есть - берем его, если нет - задаем случайный (затем все равно сгенерируем случайный и отправим на email
        $password = request()->has("password") ? $this->clean(request()->input("password")) : md5(microtime() . rand(100000, 1000000));
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

