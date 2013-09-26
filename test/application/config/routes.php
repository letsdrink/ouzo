<?php
use Ouzo\Routing\Route;
Route::any('/', 'Index#index');
Route::allowAll('/users', 'users');
Route::get('/users/add', 'users#add');
Route::post('/users/save', 'users#save');
Route::resource('users');