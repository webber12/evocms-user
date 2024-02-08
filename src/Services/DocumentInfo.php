<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use EvolutionCMS\Models\SiteContent;

class DocumentInfo extends Service
{
    public function process($params = [])
    {
        $check = $this->checkDocument($params);
        if (!$check) {
            $response = ['error' => $this->trans('common_access_denied')];
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
