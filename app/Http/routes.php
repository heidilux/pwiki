<?php

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

Route::get('/', 'WikiController@showLanding');

Route::get('create/{page?}', 'WikiController@showCreatePage')->where('page', '(.*)');
Route::post('create/{page}/{nextPage?}', 'WikiController@createPage')->where('nextPage', '(.*)');

Route::get('edit/{page}/{nextPage?}', 'WikiController@showEditPage')->where('nextPage', '(.*)');
Route::post('edit/{page}/{nextPage?}', 'WikiController@editPage')->where('nextPage', '(.*)');

Route::get('delete/{page?}', 'WikiController@deletePage')->where('page', '(.*)');
Route::post('delete-links', ['as' => 'deleteLinks', 'uses' => 'WikiController@deleteLinks']);

Route::post('create-link/{page?}', ['as' => 'create-link', 'uses' => 'WikiController@createLink'])->where('page', '(.*)');

Route::get('{page}/{nextPage?}', 'WikiController@showPage')->where('nextPage', '(.*)');

