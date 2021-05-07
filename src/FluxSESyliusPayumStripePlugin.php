<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin;

use FluxSE\SyliusPayumStripePlugin\DependencyInjection\Compiler\PayumGatewayConfigOverride;
use FluxSE\SyliusPayumStripePlugin\DependencyInjection\Compiler\PayumStoragePaymentAliaser;
use FluxSE\SyliusPayumStripePlugin\DependencyInjection\Compiler\WinzouStateMachineCallbacksModifier;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FluxSESyliusPayumStripePlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new PayumGatewayConfigOverride([
            'stripe_checkout_session' => [
                'payum.template.layout' => '@SyliusPayum/layout.html.twig',
            ],
        ]));

        $container->addCompilerPass(new WinzouStateMachineCallbacksModifier());
        $container->addCompilerPass(new PayumStoragePaymentAliaser());

        parent::build($container);
    }
}
