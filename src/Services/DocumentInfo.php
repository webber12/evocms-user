<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use EvolutionCMS\Models\SiteContent;

class DocumentInfo extends Service
{
    public function process($params = [])
    {
        $errors = [];
        $check = $this->checkDocument($params);

        if (!$check) {
            $errors['common'][] = trans('evocms-user-core::messages.common_access_denied');
            $response = ['status' => 'error', 'errors' => $errors];
        } else {
            $response = evo()->getDocumentObject('id', $params['id']);
        }
        return $this->makeResponse($response);
    }

    protected function checkDocument($params)
    {
        $res = SiteContent::where('createdby', $params['user'])
            ->where('id', $params['id'])
            ->where('deleted', 0)->get();
        return (count($res) == 1);
    }
}
