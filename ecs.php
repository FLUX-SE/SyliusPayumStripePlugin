<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $config): void {
    $config->import(__DIR__ . '/vendor/sylius-labs/coding-standard/ecs.php');

    $config->paths([
        'src',
        'tests/Behat',
    ]);

    // PHP 7 compatibility
    $config->ruleWithConfiguration(TrailingCommaInMultilineFixer::class, ['elements' => ['arrays']]);

};
