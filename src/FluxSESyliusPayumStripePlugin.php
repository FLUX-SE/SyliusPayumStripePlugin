<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin;

use FluxSE\SyliusPayumStripePlugin\DependencyInjection\Compiler\LiveTwigComponentCompilerPass;
use FluxSE\SyliusPayumStripePlugin\DependencyInjection\Compiler\PayumGatewayConfigOverride;
use FluxSE\SyliusPayumStripePlugin\DependencyInjection\Compiler\PayumStoragePaymentAliaser;
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
            'stripe_js' => [
                'payum.template.layout' => '@SyliusPayum/layout.html.twig',
            ],
        ]));

        $container->addCompilerPass(new PayumStoragePaymentAliaser());
        // Before SyliusUiBundle compiler pass
        $container->addCompilerPass(new LiveTwigComponentCompilerPass(), priority: 501);

        parent::build($container);
    }

    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
