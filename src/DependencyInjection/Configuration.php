<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const CONFIG_ROOT_NAME = 'prometee_sylius_payum_stripe_session_checkout';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(self::CONFIG_ROOT_NAME);
        $rootNode = $treeBuilder->getRootNode();

        $this->addGlobalSection($rootNode);

        return $treeBuilder;
    }

    protected function addGlobalSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('line_item_image')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('imagine_filter')
                            ->defaultValue('sylius_shop_product_original')
                            ->info('This is the imagine filter use to get the image displayed on Stripe Checkout Session (default: the filter uses into `@ShopBundle/Product/Show/_images.html.twig`)')
                        ->end()
                        ->booleanNode('fallback_image')
                            ->defaultValue('https://placehold.it/400x300')
                            ->info('Fallback image used when no image is set on a product and also when you test this plugin from a private web server (ex: from localhost)')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
