<?php

return [
    "customRules" => [
        'email' => 'required|email|unique:easynewsletter_subscribers',
    ],
    "customMessages" => [
        'email.required' =>  trans('evocms-user-core::messages.required_field', [ 'field' => 'e-mail' ]),
        'email.email' => trans('evocms-user-core::messages.valid_email', ['field' => 'e-mail' ]),
        'email.unique' => 'Данный email уже подписан на рассылку',
    ],
];
