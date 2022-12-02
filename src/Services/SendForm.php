<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use \EvolutionCMS\UserManager\Services\UserManager;


class SendForm extends Service
{

    //custom config for formid from file
    protected $customConfig = [];

    protected $formId = false;

    public function process($params = [])
    {

        $errors = [];

        if (request()->has(['formid'])) {

            $this->formId = e(request()->input('formid'));

            $this->loadCustomConfig('forms/' . $this->formId . '.php');

            $data = $this->makeData();

            $customErrors = $this->makeCustomValidator($data);

            if (!empty($customErrors)) {
                $errors['customErrors'] = $customErrors;
            } else {
                $data = $this->callPrepare($data);
                $reportTpl = $this->getCfg("SendFormReportTpl", "CODE:default template [+name+] [+email+] [+message+]");
                $params = [
                    'to' => !empty($data['to']) ? $data['to'] : $this->getCfg("SendFormTo"),
                    'subject' => !empty($data['subject']) ? $data['subject'] : $this->getCfg("SendFormSubject", "Letter form site"),
                    'body' => app('DLTemplate')->parseChunk($reportTpl, $data),
                ];
                if(!evo()->sendmail($params, '', ($data['attachments'] ?? []))) {
                    $errors['common'][] = 'form sending error';
                }
            }
        } else {
            $errors['common'][] = 'no required formid field';
        }
        if (!empty($errors)) {
            $response = [ 'status' => 'error', 'errors' => $errors ];
        } else {
            $response = [ 'status' => 'ok', 'message' => 'success form send' ];
        }
        return $this->makeResponse($response);
    }

    protected function makeData()
    {
        $data = [];
        foreach($_POST as $k => $v) {
            $k = e($k);
            $v = is_string($v) ? $this->clean($v) : $this->clean(implode('||', $v));
            $data[$k] = $v;
        }
        return $data;
    }

}
