<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin;

use Prometee\SyliusPayumStripeCheckoutSessionPlugin\DependencyInjection\Compiler\PayumGatewayConfigOverride;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PrometeeSyliusPayumStripeCheckoutSessionPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new PayumGatewayConfigOverride([
            'stripe_checkout_session' => [
                'payum.template.layout' => '@SyliusPayum/layout.html.twig',
            ],
        ]));

        parent::build($container);
    }
}
