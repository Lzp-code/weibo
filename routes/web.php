<?php

Route::get('/','StaticPagesController@home')->name('home');
Route::get('/help','StaticPagesController@help')->name('help');
Route::get('/about','StaticPagesController@about')->name('about');

Route::get('signup','UsersController@create')->name('signup');
Route::resource('users', 'UsersController');
//Route::get('/users/{user}','UsersController@show')->name('users.show');

Route::get('login','SessionsController@create')->name('login');
Route::post('login','SessionsController@store')->name('login');
Route::delete('logout','SessionController@destory')->name('logout');