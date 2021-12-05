<?php

return [
    //"LogoutRedirectId" => 1,
    //"AuthRedirectId" => 1,
    "AuthService" =>  "\\EvolutionCMS\\EvoUser\\Services\\Auth",
    "AuthCustomRules" => [
        'username' => 'required|min:6',
    ],
    "AuthCustomMessages" => [
        'username.required' =>  trans('evousercore::messages.fieldrequired', [ 'field' => 'Username' ]),
        'username.min' =>  trans('evousercore::messages.minlength', [ 'field' => 'Username', 'num' => 5 ]),
    ],
    "AuthPrepare" => "classname::methodname",

    //"RegisterRedirectId" => 1,
    "RegisterService" =>  "\\EvolutionCMS\\EvoUser\\Services\\Register",
    "RegisterCustomFields" => [ 'first_name', 'fullname' ],
    "RegisterCustomRules" => [
        'fullname' => 'required|min:6',
        'first_name' => 'required|min:6'
    ],
    "RegisterCustomMessages" => [
        'fullname.min' => trans('evouser:messages.test'),
        'first_name.min' => 'имя не короче 6 знаков',
    ],
    "RegisterPrepare" => "classname::methodname",

    "ProfileService" =>  "\\EvolutionCMS\\EvoUser\\Services\\Profile",
    "ProfileCustomFields" => [ 'first_name' ],
    "ProfileCustomRules" => [
        'fullname' => 'required|min:6',
        'first_name' => 'required|min:6'
    ],
    "ProfileCustomMessages" => [
        'fullname.min' => 'полное имя не короче 6 знаков',
        'first_name.min' => 'имя не короче 6 знаков',
    ],
    "ProfilePrepare" => "classname::methodname",

];
