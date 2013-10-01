<?php
use Ouzo\Routing\Route;

Route::get('/', 'index#index');
Route::allowAll('/users', 'users', array('new', 'select_outbound_for_user'));
Route::get('/agents/index', 'agents#index');
Route::post('/agents/index', 'agents#index');
Route::allowAll('/photos', 'photos');
Route::any('/agents/index', 'agents#index');
Route::resource('phones');