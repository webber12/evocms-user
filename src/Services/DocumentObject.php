<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;

class DocumentObject extends Service
{
    public function process($params = [])
    {
        $response = evo()->getDocumentObject('id', $params['id']);
        return $this->makeResponse($response);
    }
}
