<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withSets([
        'vendor/sylius-labs/coding-standard/ecs.php',
    ])
    ->withPaths([
        'src',
        'tests/Behat',
    ])
;
