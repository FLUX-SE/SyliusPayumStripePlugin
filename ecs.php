<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/vendor/sylius-labs/coding-standard/ecs.php');

    $services = $containerConfigurator->services();
    /**
     * Was added to fix this exception:
     *
     * PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException: [binary_operator_spaces] Invalid configuration:
     * The options "align_double_arrow", "align_equals" do not exist. Defined options are: "default", "operators".
     * in vendor/friendsofphp/php-cs-fixer/src/AbstractFixer.php on line 155
     */
    $services->set(BinaryOperatorSpacesFixer::class);

    /**
     * Miss configured fixer into sylius-labs/coding-standard v4.1.0
     */
    $services->set(ClassAttributesSeparationFixer::class);
};
