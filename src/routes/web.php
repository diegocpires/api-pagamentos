<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'customers'], function() use ($router) {
    $router->get('/', 'CustomerController@index');
    $router->get('/{customerId}', 'CustomerController@get');
    $router->post('/', 'CustomerController@insert');
    $router->put('/{customerId}', 'CustomerController@update');
    $router->delete('/{customerId}', 'CustomerController@delete');
});

$router->post('/transaction', 'TransactionController@index');
