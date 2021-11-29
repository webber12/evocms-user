<?php

use EvolutionCMS\EvoUser\Controller;
use Illuminate\Support\Facades\Route;

Route::get('/evocms-user/auth', [Controller::class, 'Auth']);
   // ->name('evouser::auth');

Route::post('/evocms-user/auth', [Controller::class, 'Auth']);

Route::post('/evocms-user/register', [Controller::class, 'Register']);

Route::post('/evocms-user/profile', [Controller::class, 'Profile']);

/*
Route::get('/evouser/auth/{id?}', [Auth::class, 'document'])
    ->whereNumber('id')
    ->name('evouser::index');
*/
