<?php

declare(strict_types=1);

use App\Application\Controllers\FileController;
use App\Application\Controllers\ProcessaCorridaController;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;


///////////////////////////////////////////// CONTROLLER DEPENDENCIES //////////////////////////////////////////////

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        FileController::class => function (ContainerInterface $container) {
            return new FileController(
                $container->get(LoggerInterface::class),
            );
        },

    ]);
};
