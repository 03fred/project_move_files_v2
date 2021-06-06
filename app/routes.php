<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Models\Bootstrap;

return function (App $app) {

    $container = $app->getContainer();

    Bootstrap::load($container);

    $app->addBodyParsingMiddleware();

    $app->group('/process-file', function (Group $group) use ($container) {
        $group->post('', 'App\Application\Controllers\FileController:process');
    });
};

$app->run();
