<?php
use Ouzo\Routing\Route;

Route::get('/', 'index#index');
Route::allowAll('/users', 'users', array('except' => array('new', 'select_outbound_for_user')));
Route::get('/agents/index', 'agents#index');
Route::post('/agents/index', 'agents#index');
Route::allowAll('/photos', 'photos');
Route::any('/agents/index', 'agents#index');
Route::resource('phones');
Route::get('/agents', 'agents#index', array('as' => 'my_name'));
Route::get('/agents/show/id/:id/call_id/:call_id', 'agents#show');