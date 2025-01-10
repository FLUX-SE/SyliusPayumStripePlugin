<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const CONFIG_ROOT_NAME = 'flux_se_sylius_payum_stripe';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::CONFIG_ROOT_NAME);
        $rootNode = $treeBuilder->getRootNode();
        $this->addGlobalSection($rootNode);

        return $treeBuilder;
    }

    protected function addGlobalSection(ArrayNodeDefinition $node): void
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('refund_disabled')
                    ->defaultTrue()
                    ->info('Enable/Disable the refund state machine callback')
                ->end()
                ->arrayNode('payment_method_types')
                    ->scalarPrototype()->end()
                    ->info('Other possible values https://stripe.com/docs/api/checkout/sessions/create#create_checkout_session-payment_method_types')
                ->end()
                ->arrayNode('line_item_image')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('imagine_filter')
                            ->defaultValue('sylius_shop_product_original')
                            ->info('This is the Imagine filter used to get the image displayed on Stripe Checkout Session (default: the filter uses into `@ShopBundle/templates/product/show/page/info/overview/images.html.twig`)')
                        ->end()
                        ->scalarNode('fallback_image')
                            ->defaultValue('https://placehold.co/300')
                            ->info('Fallback image used when no image is set on a product and also when you test this plugin from a private web server (ex: from localhost)')
                        ->end()
                        ->scalarNode('localhost_pattern')
                            ->defaultValue('#://(localhost|127.0.0.1|[^:/]+\.wip|[^:/]+\.local)[:/]#')
                            ->info('Stripe does not display localhost images because it caches them on a CDN, this preg_match() pattern will allow the line item image provider to test it if the image is from a localhost network or not.')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
