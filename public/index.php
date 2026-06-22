<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

define('DIR_VIEWS', dirname(__DIR__) . '/app/views');
define('EXT_VIEWS', '.html');

$app = AppFactory::create();

// Define o caminho base do projeto
$app->setBasePath('/ucfinal');

// Middlewares globais
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

require __DIR__ . '/../app/routes/routes.php';

$app->run();
