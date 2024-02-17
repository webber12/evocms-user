<?php

return [
    // default
    "FrontJS" => 'assets/plugins/evocms-user/script.js',

    //"LogoutRedirectId" => 1,
    //"AuthRedirectId" => 1,
    "AuthService" => "\\EvolutionCMS\\EvoUser\\Services\\Auth",
    "AuthCustomRules" => [
        'username' => 'required|min:5',
    ],
    "AuthCustomMessages" => [
        'username.required' => trans('evocms-user-core::messages.common_required_field', ['field' => 'Username']),
        'username.min' => trans('evocms-user-core::messages.validate_minlength', ['field' => 'Username', 'num' => 5]),
    ],
    "AuthPrepare" => "classname::methodname",

    //"RegisterRedirectId" => 1,
    "RegisterService" => "\\EvolutionCMS\\EvoUser\\Services\\Register",
    "RegisterCustomFields" => ['first_name', 'fullname'],
    "RegisterCustomRules" => [
        'fullname' => 'required|min:6',
        'first_name' => 'required|min:6',
    ],
    "RegisterCustomMessages" => [
        'fullname.min' => trans('evocms-user-core::messages.test'),
        'first_name.min' => 'имя не короче 6 знаков',
    ],
    "RegisterPrepare" => function ($data) {unset($data['role_id']);unset($data['user_groups']);return $data;},

    "ProfileEditService" => "\\EvolutionCMS\\EvoUser\\Services\\ProfileEdit",
    "ProfileEditCustomFields" => ['fullname'],
    "ProfileEditCustomRules" => [
        'fullname' => 'required|min:4',
    ],
    "ProfileEditCustomMessages" => [
        'fullname.min' => 'полное имя не короче 4 символов',
    ],
    "ProfileEditPrepare" => "classname::methodname",
    "CommonAccessRules" => [
        'context' => 'web',
        'current' => true,
        'roles' => [1],
        'custom' => "classname::methodname",
    ],
    "ProfileInfoAccessRules" => [

    ],

    "DocumentListService" => "\\EvolutionCMS\\EvoUser\\Services\\DocumentList",
    "DocumentListDisplay" => 10,
    "DocumentListSortBy" => "id",
    "DocumentListSortDir" => "DESC",
    "DocumentListFields" => "id,pagetitle,longtitle,alias,createdby",
    "DocumentListTvs" => "image",
    "DocumentListOnlyActive" => false,
    "DocumentListShowUndeleted" => true,

    "DocumentListUserService" => "\\EvolutionCMS\\EvoUser\\Services\\DocumentListUser",
    "DocumentListUserService" => "\\EvolutionCMS\\EvoUser\\Services\\DocumentListUser",
    "DocumentListUserDisplay" => 15,
    "DocumentListUserSortBy" => "id",
    "DocumentListUserSortDir" => "DESC",
    "DocumentListUserFields" => "id,pagetitle,longtitle,alias,createdby",
    "DocumentListUserTvs" => "image",
    "DocumentListUserOnlyActive" => false,
    "DocumentListUserShowUndeleted" => true,

    "DocumentInfoService" => "\\EvolutionCMS\\EvoUser\\Services\\DocumentInfo",

    "DocumentObjectService" => "\\EvolutionCMS\\EvoUser\\Services\\DocumentObject",
    "DocumentObjectOnlyActive" => false,
    "DocumentObjectShowUndeleted" => true,

    "DocumentCreateService" => "\\EvolutionCMS\\EvoUser\\Services\\DocumentCreate",
    "DocumentCreateDefaults" => [
        'pagetitle' => trans('evocms-user-core::messages.text_new_document'),
        'template' => 0,
        'parent' => 0,
        'published' => 0,

    ],
    "DocumentCreateCustomRules" => [
        'pagetitle' => 'required|min:6',
    ],
    "DocumentCreateCustomMessages" => [
        'pagetitle.required' => trans('evocms-user-core::messages.common_required_field', ['field' => 'Pagetitle']),
        'pagetitle.min' => trans('evocms-user-core::messages.validate_minlength', ['num' => 6, 'field' => 'Pagetitle']),
    ],

    "DocumentEditCustomRules" => [
        'pagetitle' => 'required|min:6',
    ],
    "DocumentEditCustomMessages" => [
        'pagetitle.required' => trans('evocms-user-core::messages.common_required_field', ['field' => 'Pagetitle']),
        'pagetitle.min' => trans('evocms-user-core::messages.validate_minlength', ['num' => 6, 'field' => 'Pagetitle']),
    ],

    "SendFormAuthAccessRules" => [
        'roles' => [2],
        'custom' => "classname::methodname",
    ],
/*
"OrderListPrepare" => function($data, $modx, $DL, $eDL) use ($fields, &$index) {
//$data['custom_field'] = 'custom_field_from_prepare';
return $data;
},
 */
    "OrderCancelStatus" => 5,
    "OrderCancelAvailableStatuses" => [1, 2], //отменить можно только новый заказ и заказ в обработке

    "OrderRepeatCartName" => trans('evocms-user-core::messages.text_cart_name'),
];
