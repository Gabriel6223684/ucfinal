<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Define o caminho base do projeto
$app->setBasePath('/ucfinal');

// Middlewares globais
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

// CORREÇÃO: Caminho limpo para o arquivo de rotas
require __DIR__ . '/../app/routes/routes.php';

$app->run();
