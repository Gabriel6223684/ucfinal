<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Carrega variáveis de ambiente (se usar .env)
// (new Dotenv\Dotenv(__DIR__ . '/../'))->safeLoad();

$app = AppFactory::create();

$app->setBasePath('/ucfinal');

// Middlewares globais
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Rotas
require __DIR__ . '/../app//routes/routes.php';

$app->run();
