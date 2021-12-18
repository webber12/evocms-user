#### Установка:
добавить в секцию require файла core/custom/composer.json строку

```"webber12/evocms-user": "*"```

 и выполнить в папке core команду (чтобы обновить только указанный пакет)
 
```composer update webber12/evocms-user```

затем в этой же папке выполнить (если пакет ранее не устанавливался)

```php artisan vendor:publish --provider="EvolutionCMS\EvoUser\EvoUserServiceProvider"```


#### Некоторые примеры использования в контроллере:
Получение id текущего авторизованного пользователя в порядке следования контекстов (возвращается первый найденный)

```$currentUser = app('evouser')->do('user', ['web', 'mgr']);```

Данные профиля заданного пользователя

```$profile = app('evouser')->do('profileInfo', [ 'user' => $currentUser ]);```


По умолчанию при вызове сервиса проверяются права доступа, заданные в конфигурации, но данную проверку можно отключить
Получение списка документов пользователя с id=7 без проверки прав доступа

```
$documents = app('evouser')->withoutRules()->do('DocumentListUser', [ 'user' => 7 ]);
```


Список произвольных опубликованных документов с шаблоном 3, словом "товар" в заголовке и price>=20 (tv) - постранично
```
$documents = app('evouser')->do('documentList', [], [
    'fields' => 'id,pagetitle',
    'tvs' => 'price,image',
    'onlyActive' => true,
    'display' => 2,
    'filters' => [
        'pagetitle' => 'товар',
        'template' => 3,
        'price' => '>=20',
    ]
]);
```

Список документов, созданных текущим пользователем (с фильтром и постраничным выводом)
```
$documentsUser = app('evouser')->do('documentListUser', [ 'user' => $currentUser ], [
    'fields' => 'id,pagetitle',
    'tvs' => 'price,image',
    'onlyActive' => true,
    'display' => 15,
    'filters' => [
        'pagetitle' => 'товар',
        'price' => '>=10',
    ]
]);
```

Получение объекта $documentObject документа с id=2

```$document = app('evouser')->do('documentObject', [ 'id' => 2 ]);```

Получение списка заказов текущего пользователя

```$orders = app('evouser')->do('orderList', [ 'user' => $currentUser ]);```

Получение информации о заказе с id=4 (данные о заказе, списке товаров и истории

```$order = app('evouser')->do('orderInfo', [ 'id' => 4 ]);```

test
