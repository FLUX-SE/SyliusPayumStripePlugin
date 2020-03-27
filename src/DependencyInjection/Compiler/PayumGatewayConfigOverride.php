<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PayumGatewayConfigOverride implements CompilerPassInterface
{
    /** @var array */
    private $gatewayConfigs;

    /**
     * @param array $gatewayConfigs
     */
    public function __construct(array $gatewayConfigs)
    {
        $this->gatewayConfigs = $gatewayConfigs;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $builder = $container->getDefinition('payum.builder');
        foreach ($this->gatewayConfigs as $gatewayName => $factoryConfig) {
            $builder->addMethodCall('addGatewayFactoryConfig', [$gatewayName, $factoryConfig]);
        }
    }
}
