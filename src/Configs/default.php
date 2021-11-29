<?php

use Illuminate\Support\Facades\Lang;

return [
    //"LogoutRedirectId" => 1,
    //"AuthRedirectId" => 1,
    "AuthService" =>  "\\EvolutionCMS\\EvoUser\\Services\\Auth",
    "AuthCustomRules" => [
        'username' => 'required|min:5',
    ],
    "AuthCustomMessages" => [
        'username.required' => Lang::get("global.required_field", ['field' => 'username']),
        'username.min' => 'username не короче 5 знаков',
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
        'fullname.required' => Lang::get("global.required_field", ['field' => 'fullname']),
        'first_name.required' => Lang::get("global.required_field", ['field' => 'first_name']),
        'fullname.min' => 'полное имя не короче 6 знаков',
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
        'fullname.required' => Lang::get("global.required_field", ['field' => 'fullname']),
        'first_name.required' => Lang::get("global.required_field", ['field' => 'first_name']),
        'fullname.min' => 'полное имя не короче 6 знаков',
        'first_name.min' => 'имя не короче 6 знаков',
    ],
    "ProfilePrepare" => "classname::methodname",



];
