<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Routing\Route;

Route::get('/', 'index', 'index');
Route::allowAll('/users', 'users', ['except' => ['new', 'select_outbound_for_user']]);
Route::get('/agents/index', 'agents', 'index');
Route::post('/agents', 'agents', 'update');
Route::allowAll('/photos', 'photos');
Route::resource('phones', 'phones');
Route::get('/agents', 'agents', 'index', ['as' => 'my_name']);
Route::get('/agents/show/id/:id/call_id/:call_id', 'agents', 'show');
