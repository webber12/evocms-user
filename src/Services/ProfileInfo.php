<?php
namespace EvolutionCMS\EvoUser\Services;

use EvolutionCMS\EvoUser\Services\Service;
use Illuminate\Http\Request;
use \EvolutionCMS\UserManager\Services\UserManager;


class ProfileInfo extends Service
{

    public function process($uid = 0)
    {
        $user = $this->reloadUser($uid);
        return $this->makeResponse($user);
    }

}
