<?php

use EvolutionCMS\EvoUser\Controller;
use Illuminate\Support\Facades\Route;

Route::post('/evocms-user/auth', [Controller::class, 'Auth'])
    ->middleware('evocms-user-csrf')
    ->name('evocms-user-auth');

Route::post('/evocms-user/register', [Controller::class, 'Register'])
    ->middleware('evocms-user-csrf')
    ->name('evocms-user-register');

Route::post('/evocms-user/profile/{user}', [Controller::class, 'ProfileEdit'])
    ->where('user', '[0-9]+')
    ->middleware('evocms-user-csrf')
    ->middleware('evocms-user-access:ProfileEdit')
    ->name('evocms-user-profile-edit');

Route::get('/evocms-user/profile/{user}', [Controller::class, 'ProfileInfo'])
    ->where('user', '[0-9]+')
    ->middleware('evocms-user-csrf')
    ->middleware('evocms-user-access:ProfileInfo')
    ->name('evocms-user-profile-info');

Route::get('/evocms-user/documents', [Controller::class, 'DocumentList'])
    ->middleware('evocms-user-csrf')
    ->middleware('evocms-user-access:DocumentList')
    ->name('evocms-user-document-list');


Route::get('/evocms-user/documents/{user}', [Controller::class, 'DocumentListUser'])
    ->where('user', '[0-9]+')
    ->middleware('evocms-user-csrf')
    ->middleware('evocms-user-access:DocumentListUser')
    ->name('evocms-user-document-list-user');

Route::get('/evocms-user/documents/{user}/{id}', [Controller::class, 'DocumentInfo'])
    ->where('user', '[0-9]+')
    ->where('id', '[0-9]+')
    //->middleware('evocms-user-csrf')
    ->middleware('evocms-user-access:DocumentInfo')
    ->name('evocms-user-document-info');

Route::get('/evocms-user/document/{id}', [Controller::class, 'DocumentObject'])
    ->where('user', '[0-9]+')
    ->middleware('evocms-user-csrf')
    ->middleware('evocms-user-access:DocumentObject')
    ->name('evocms-user-document-object');

Route::post('/evocms-user/document/{id}', [Controller::class, 'DocumentEdit'])
    ->where('user', '[0-9]+')
    ->middleware('evocms-user-csrf')
    ->middleware('evocms-user-access:DocumentEdit')
    ->name('evocms-user-document-edit');

Route::post('/evocms-user/document', [Controller::class, 'DocumentCreate'])
    ->middleware('evocms-user-csrf')
    ->middleware('evocms-user-access:DocumentCreate')
    ->name('evocms-user-document-create');

//отправка формы без проверки прав
Route::post('/evocms-user/send-form', [Controller::class, 'SendForm'])
    ->middleware('evocms-user-csrf')
    ->name('evocms-user-send-form');

//отправка формы с проверкой роли авторизованного пользователя
Route::post('/evocms-user/send-form-auth', [Controller::class, 'SendForm'])
    ->middleware('evocms-user-csrf')
    ->middleware('evocms-user-access:SendFormAuth')
    ->name('evocms-user-send-form-auth');

Route::get('/evocms-user/orders', [Controller::class, 'OrderList'])
    ->middleware('evocms-user-csrf')
    ->middleware('evocms-user-access:OrderList')
    ->name('evocms-user-order-list');

Route::get('/evocms-user/orders/{user}', [Controller::class, 'OrderListUser'])
    ->where('user', '[0-9]+')
    ->middleware('evocms-user-csrf')
    ->middleware('evocms-user-access:OrderListUser')
    ->name('evocms-user-order-list-user');

Route::get('/evocms-user/order/{id}', [Controller::class, 'OrderInfo'])
    ->where('id', '[0-9]+')
    ->middleware('evocms-user-csrf')
    ->middleware('evocms-user-access:OrderInfo')
    ->name('evocms-user-order-info');

Route::post('/evocms-user/order/cancel/{id}', [Controller::class, 'OrderCancel'])
    ->where('id', '[0-9]+')
    ->middleware('evocms-user-csrf')
    ->middleware('evocms-user-access:OrderCancel')
    ->name('evocms-user-order-cancel');

Route::post('/evocms-user/order/repeat/{id}', [Controller::class, 'OrderRepeat'])
    ->where('id', '[0-9]+')
    ->middleware('evocms-user-csrf')
    ->middleware('evocms-user-access:OrderRepeat')
    ->name('evocms-user-order-repeat');

