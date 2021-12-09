#### Установка:
добавить в секцию require файла core/custom/composer.json строку и выполнить в папке core команду composer update
```"webber12/evocms-user": "*"```

затем в этой же папке выполнить (если пакет ранее не устанавливался)
```php artisan vendor:publish --provider="EvolutionCMS\EvoUser\EvoUserServiceProvider"```


#### Некоторые примеры использования в контроллере:
Получение id текущего авторизованного пользователя в порядке следования контекстов (возвращается первый найденный)
```$currentUser = app('evouser')->do('user', ['web', 'mgr']);```

Данные профиля заданного пользователя:
```$profile = app('evouser')->do('profileInfo', [ 'user' => $currentUser ]);```

Список документов, созданных текущим пользователем (с фильтром и постраничным выводом)
```$documents = app('evouser')->do('documentList', [ 'user' => $currentUser ], [
    'fields' => 'id,pagetitle',
    'tvs' => 'price,image',
    'onlyActive' => true,
    'display' => 15,
    'filters' => [
        'pagetitle' => 'товар',
        'price' => '>=10',
    ]
]);```

Получение объекта $documentObject документа с id=2
```$document = app('evouser')->do('documentObject', [ 'id' => 2 ]);```

Получение списка заказов текущего пользователя
```$orders = app('evouser')->do('orderList', [ 'user' => $currentUser ]);```

Получение информации о заказе с id=4 (данные о заказе, списке товаров и истории
```$order = app('evouser')->do('orderInfo', [ 'id' => 4 ]);```

