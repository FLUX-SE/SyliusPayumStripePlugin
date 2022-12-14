<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->import(__DIR__ . '/vendor/sylius-labs/coding-standard/ecs.php');

    $services = $ecsConfig->services();

    // PHP 7 compatibility
    $services
        ->set(TrailingCommaInMultilineFixer::class)
        ->call('configure', [['elements' => ['arrays']]])
    ;
};
