<?php

use EvolutionCMS\EvoUser\Controller;
use Illuminate\Support\Facades\Route;

Route::post('/evocms-user/auth', [Controller::class, 'Auth']);

Route::post('/evocms-user/register', [Controller::class, 'Register']);

Route::post('/evocms-user/profile', [Controller::class, 'Profile']);

