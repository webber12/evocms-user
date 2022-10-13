<?php
//возвращаем массив кастомных параметров пакета

return [
/* показ списка заказов разруливается только ролями
    "OrderListAccessRules" => [
        'roles' => [ 1, 2 ]
    ],
    //роли и группы устанавливаются при регистрации вручную. по-умолчанию данные поля удаляются
    "RegisterPrepare" => function($data){
        $data['role_id'] = 5;
        $data['user_groups'] = [ 4, 7 ];
        return $data;
    },
    "ProfileEditCustomFields" => [ 'fullname' ], //для редактирования профиля поле fullname обязательно
*/
];