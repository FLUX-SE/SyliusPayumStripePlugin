<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\DependencyInjection\Compiler;

use FluxSE\SyliusPayumStripePlugin\Twig\Component\AdminPaymentMethod\FormComponent;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class LiveTwigComponentCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('sylius_admin.twig.component.payment_method.form');
        if (!$definition->hasTag('sylius.twig_component')) {
            return;
        }

        $tagConfig = $definition->getTag('sylius.twig_component');

        $definition
            ->setClass(FormComponent::class)
            ->addArgument(new Reference('sylius.custom_factory.payment_method'))
            ->addTag('sylius.live_component.admin', $tagConfig[0])
            ->clearTag('sylius.twig_component')
        ;
    }
}
