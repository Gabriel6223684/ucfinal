<?php

declare(strict_types=1);

/** @var \Slim\App $app */

// Usando caminhos absolutos com a barra inicial '\' para não ter erro de interpretação
use Slim\Routing\RouteCollectorProxy;

// Rotas Principais
$app->get('/', \app\controller\Home::class . ':home')->add(\app\middleware\Middleware::web());
$app->get('/home', \app\controller\Home::class . ':home')->add(\app\middleware\Middleware::web());
$app->get('/login', \app\controller\Login::class . ':login')->add(\app\middleware\Middleware::web());
$app->get('/profile', \app\controller\Profile::class . ':profile')->add(\app\middleware\Middleware::web());

// Grupo de Autenticação
$app->group('/authentication', function (RouteCollectorProxy $group) {
    $group->post('/auth', \app\controller\Login::class . ':authenticate');
    $group->post('/register', \app\controller\Login::class . ':register');
});

// Grupo de Usuário
$app->group('/usuario', function (RouteCollectorProxy $group) {
    $group->get('/lista', \app\controller\User::class . ':list');
    $group->get('/detalhes/{id}', \app\controller\User::class . ':details');
    $group->get('/detalhes', \app\controller\User::class . ':details');
    $group->post('/insert', \app\controller\User::class . ':insert');
    $group->post('/update', \app\controller\User::class . ':update');
});

// Grupo de Chat
$app->group('/chat', function (RouteCollectorProxy $group) {
    $group->get('/lista', \app\controller\User::class . ':list');
    $group->get('/conversa', \app\controller\User::class . ':conversation');
});
