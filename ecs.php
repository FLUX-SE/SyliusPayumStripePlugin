<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/vendor/sylius-labs/coding-standard/ecs.php');

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SKIP, [
        __DIR__ . '/vendor/*',
        __DIR__ . '/node_modules/*',
        __DIR__ . '/tests/Application/config/*/bundles.php',
        __DIR__ . '/tests/Application/config/bundles.php',
        __DIR__ . '/tests/Application/node_modules/*',
        __DIR__ . '/tests/Application/var/*',
    ]);
};
