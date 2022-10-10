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

```$profile = app('evouser')->do('ProfileInfo', [ 'user' => $currentUser ]);```


По умолчанию при вызове сервиса проверяются права доступа, заданные в конфигурации, но данную проверку можно отключить
Получение списка документов пользователя с id=7 без проверки прав доступа

```
$documents = app('evouser')->withoutRules()->do('DocumentListUser', [ 'user' => 7 ]);
```


Список произвольных опубликованных документов с шаблоном 3, словом "товар" в заголовке и price>=20 (tv) - постранично
```
$documents = app('evouser')->do('DocumentList', [], [
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
$documentsUser = app('evouser')->do('DocumentListUser', [ 'user' => $currentUser ], [
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

```$document = app('evouser')->do('DocumentObject', [ 'id' => 2 ]);```

Получение списка заказов текущего пользователя

```$orders = app('evouser')->do('OrderList', [ 'user' => $currentUser ]);```

Получение информации о заказе с id=4 (данные о заказе, списке товаров и истории

```$order = app('evouser')->do('OrderInfo', [ 'id' => 4 ]);```

#### Пример формы авторизации
```
<form data-evocms-user-action="auth">
    <p><b>ВХОД</b></p>
    @csrf
    <input type="text" name="username">
    <div data-error data-error-username></div>
    <input type="text" name="password">
    <div data-error data-error-password></div>
    <div data-error data-error-common></div>
    <input type="submit" value="ok">
</form>
```

#### Пример формы редактирования профиля текущего пользователя
```
    Вы вошли как <b><a href="?logout">{{ $user['username'] }}</a></b>
    <hr><hr>
    <form data-evocms-user-action="profile" data-evocms-user-user="{{ $user['id'] }}">
        <p><b>Редактирование</b></p>
        @csrf
        <input type="text" name="fullname" value="{{ $user['fullname'] ?? '' }}">
        <div data-error data-error-fullname></div>
        <input type="text" name="first_name" value="{{ $user['first_name'] ?? '' }}">
        <div data-error data-error-first_name></div>
        <div data-error data-error-common></div>
        <input type="submit" value="ok">
    </form>
```

#### Пример формы создания документа c TV image
```
<form data-evocms-user-action="document">
    <p><b>СОЗДАНИЕ ДОКУМЕНТА</b></p>
    @csrf
    <input type="text" name="pagetitle">
    <div data-error data-error-pagetitle></div>
    <input type="text" name="longtitle">
    <div data-error data-error-longtitle></div>
    <input type="text" name="image">
    <div data-error data-error-image></div>
    <div data-error data-error-common></div>
    <input type="submit" value="ok">
</form>
```

#### Пример редактирования документа с id=7
```
<form data-evocms-user-action="document" data-evocms-user-id="7">
    <p><b>РЕДАКТИРОВАНИЕ ДОКУМЕНТА</b></p>
    @csrf
    <input type="text" name="pagetitle">
    <div data-error data-error-pagetitle></div>
    <input type="text" name="longtitle">
    <div data-error data-error-longtitle></div>
    <input type="text" name="image">
    <div data-error data-error-image></div>
    <div data-error data-error-common></div>
    <input type="submit" value="ok">
</form>
```

#### Пример формы для повторения заказа с id=2 (добавление в корзину товаров этого заказа)
```
<form data-evocms-user-action="order/repeat" data-evocms-user-id="2">
    <p><b>Повторение заказа номер 2 выглядит так</b></p>
    @csrf
    <div data-error data-error-common></div>
    <input type="submit" value="ok">
</form>
```

#### Пример jQuery-скрипта перегрузки страницы после успешной авторизации
```
    $(document).on("evocms-user-auth-success", function(e, actionUser, actionId, element, msg){
        location.reload();
    })
```

#### Пример jQuery-скрипта для обработки редактирования профиля
```
    $(document).on("evocms-user-profile-before", function(e, actionUser, actionId, element){
        alert('сейчас отправим данные пользователя ' + actionUser + ' на редактирование и подождем, что будет');
    })
    $(document).on("evocms-user-profile-error", function(e, actionUser, actionId, element, msg){
        console.log(msg);
        alert('ошибки при редактировании профиля, загляните в консоль за подробностями');
    })
    $(document).on("evocms-user-profile-success", function(e, actionUser, actionId, element, msg){
        alert('профиль пользователя ' + actionUser + ' успешно отредактирован');
    })
```

#### Пример формы для добавления email в модуль рассылки EasyNewsLetter
```
<form data-evocms-user-action="easynewsletter">
    @csrf
    <input type="email" name="email" placeholder="Введите ваш e-mail" required>
    <div data-error data-error-email></div>
    <div data-error data-error-common></div>
</form>
```

#### Пример скрипта для оповещения об успешной подписке EasyNewsLetter
```
    $(document).on("evocms-user-easynewsletter-success", function(e, actionUser, actionId, element, msg){
        element.find('[data-error-common]').html('<span class="success">Вы успешно подписались на рассылку!</span>');
    })
```

