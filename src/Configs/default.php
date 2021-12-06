<?php

return [
    //"LogoutRedirectId" => 1,
    //"AuthRedirectId" => 1,
    "AuthService" =>  "\\EvolutionCMS\\EvoUser\\Services\\Auth",
    "AuthCustomRules" => [
        'username' => 'required|min:5',
    ],
    "AuthCustomMessages" => [
        'username.required' =>  trans('evocms-user-core::messages.required_field', [ 'field' => 'Username' ]),
        'username.min' =>  trans('evocms-user-core::messages.minlength', [ 'field' => 'Username', 'num' => 5 ]),
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
        'fullname.min' => trans('evocms-user-core:messages.test'),
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
    "CommonAccessRules" => [
        'context' => 'web',
        'current' => true,
        'roles' => [ 1 ],
        'custom' => "classname::methodname",
    ],
    "ProfileInfoAccessRules" => [

    ],

];
