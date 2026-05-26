<?php

namespace app\controller;
declare(strict_types=1);

$app->get('/', app\controller\Home::class . ':home')->add(app\middleware\Middleware::web());
$app->get('/home', app\controller\Home::class . ':home')->add(app\middleware\Middleware::web());
$app->get('/login', app\controller\Login::class . ':login')->add(app\middleware\Middleware::web());

$app->group('/authentication', function (Slim\Routing\RouteCollectorProxy $group) {
    $group->post('/auth', app\controller\Login::class . ':authenticate'); // ← /authentication/auth
    $group->post('/register', app\controller\register::class . ':register');
});

$app->group('/usuario', function (Slim\Routing\RouteCollectorProxy $group) {
    $group->get('/lista', app\controller\User::class . ':list');
    $group->get('/detalhes/{id}', app\controller\User::class . ':details');
    $group->get('/detalhes', app\controller\User::class . ':details');
    $group->post('/insert', app\controller\User::class . ':insert');
    $group->post('/update', app\controller\User::class . ':update');
});

$app->group('/chat', function (Slim\Routing\RouteCollectorProxy $group) {
    $group->get('/lista', app\controller\User::class . ':list');
    $group->get('/conversa', app\controller\User::class . ':conversation');
});
