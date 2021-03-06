<?php

// Authentication routes...

$router->get('auth/login', [
    'as'   => 'auth.login',
    'uses' => 'Auth\AuthController@getLogin',
]);

$router->post('auth/login', 'Auth\AuthController@postLogin');

$router->get('auth/logout', [
    'as'   => 'auth.logout',
    'uses' => 'Auth\AuthController@logout',
]);

// Registration routes...
$router->get('auth/register', [
    'as'   => 'auth.register',
    'uses' => 'Auth\AuthController@getRegister',
]);
$router->post('auth/register', [
    'as'   => 'auth.register',
    'uses' => 'Auth\AuthController@postRegister',
]);

// Password reset link request routes...
$router->get('password/email', [
    'as'   => 'auth.password',
    'uses' => 'Auth\PasswordController@getEmail',
]);

$router->post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
$router->get('password/reset/{token}', 'Auth\PasswordController@getReset');

$router->post('password/reset', [
    'as'   => 'auth.reset',
    'uses' => 'Auth\PasswordController@postReset',
]);