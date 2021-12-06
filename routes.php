<?php

use EvolutionCMS\EvoUser\Controller;
use Illuminate\Support\Facades\Route;

Route::post('/evocms-user/auth', [Controller::class, 'Auth'])
    ->name('evocms-user-auth');

Route::post('/evocms-user/register', [Controller::class, 'Register'])
    ->name('evocms-user-register');

Route::post('/evocms-user/profile/{user}', [Controller::class, 'ProfileEdit'])
    ->where('user', '[0-9]+')
    ->middleware('evocms-user-access:ProfileEdit')
    ->name('evocms-user-profile-edit');

Route::get('/evocms-user/profile/{user}', [Controller::class, 'ProfileInfo'])
    ->where('user', '[0-9]+')
    ->middleware('evocms-user-access:ProfileInfo')
    ->name('evocms-user-profile-info');

Route::get('/evocms-user/documents/{user}', [Controller::class, 'DocumentList'])
    ->where('user', '[0-9]+')
    ->middleware('evocms-user-access:DocumentList')
    ->name('evocms-user-documents-list');

Route::get('/evocms-user/documents/{user}/{id}', [Controller::class, 'DocumentInfo'])
    ->where('user', '[0-9]+')
    ->where('id', '[0-9]+')
    ->middleware('evocms-user-access:DocumentInfo')
    ->name('evocms-user-documents-info');

Route::get('/evocms-user/document/{id}', [Controller::class, 'DocumentObject'])
    ->where('user', '[0-9]+')
    ->middleware('evocms-user-access:DocumentObject')
    ->name('evocms-user-document-object');

Route::post('/evocms-user/document', [Controller::class, 'DocumentCreate'])
    ->middleware('evocms-user-access:DocumentCreate')
    ->name('evocms-user-document-create');

