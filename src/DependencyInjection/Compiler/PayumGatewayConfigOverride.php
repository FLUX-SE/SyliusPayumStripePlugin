<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class PayumGatewayConfigOverride implements CompilerPassInterface
{
    /** @param array<string, array> $gatewayConfigs */
    public function __construct(private array $gatewayConfigs)
    {
    }

    public function process(ContainerBuilder $container): void
    {
        $builder = $container->getDefinition('payum.builder');
        foreach ($this->gatewayConfigs as $gatewayName => $factoryConfig) {
            $builder->addMethodCall('addGatewayFactoryConfig', [$gatewayName, $factoryConfig]);
        }
    }
}
