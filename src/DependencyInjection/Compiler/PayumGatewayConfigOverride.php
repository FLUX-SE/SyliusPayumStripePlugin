<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PayumGatewayConfigOverride implements CompilerPassInterface
{
    /** @var array<string, array> */
    private $gatewayConfigs;

    /** @param array<string, array> $gatewayConfigs */
    public function __construct(array $gatewayConfigs)
    {
        $this->gatewayConfigs = $gatewayConfigs;
    }

    public function process(ContainerBuilder $container): void
    {
        $builder = $container->getDefinition('payum.builder');
        foreach ($this->gatewayConfigs as $gatewayName => $factoryConfig) {
            $builder->addMethodCall('addGatewayFactoryConfig', [$gatewayName, $factoryConfig]);
        }
    }
}
