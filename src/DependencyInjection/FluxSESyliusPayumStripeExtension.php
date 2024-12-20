<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class FluxSESyliusPayumStripeExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration([], $container);
        assert(null !== $configuration);
        $configs = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'flux_se.sylius_payum_stripe.refund.disabled',
            $configs['refund_disabled'],
        );
        $container->setParameter(
            'flux_se.sylius_payum_stripe.payment_method_types',
            $configs['payment_method_types'],
        );
        $container->setParameter(
            'flux_se.sylius_payum_stripe.line_item_image.imagine_filter',
            $configs['line_item_image']['imagine_filter'],
        );
        $container->setParameter(
            'flux_se.sylius_payum_stripe.line_item_image.fallback_image',
            $configs['line_item_image']['fallback_image'],
        );
        $container->setParameter(
            'flux_se.sylius_payum_stripe.line_item_image.localhost_pattern',
            $configs['line_item_image']['localhost_pattern'],
        );

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(dirname(__DIR__) . '/../config'),
        );
        $loader->load('services.yaml');
    }
}
