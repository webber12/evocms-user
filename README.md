#### Установка:
```
Выполнить команду в папке core
php artisan package:installrequire webber12/evocms-user "*"
и выполнить в папке core команду
composer update
затем в этой же папке выполнить 
php artisan vendor:publish --provider="EvolutionCMS\EvoUser\EvoUserServiceProvider"
```
