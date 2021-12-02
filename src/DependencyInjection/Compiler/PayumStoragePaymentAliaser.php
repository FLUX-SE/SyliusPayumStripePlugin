<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class PayumStoragePaymentAliaser implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $paymentClass = $container->getParameter('sylius.model.payment.class');
        $serviceIds = $container->findTaggedServiceIds('payum.storage');

        foreach (array_keys($serviceIds) as $serviceId) {
            $serviceDefinition = $container->findDefinition($serviceId);
            $modelClass = $this->findModelClassAttribute($serviceDefinition);
            if ($paymentClass === $modelClass) {
                $container->setAlias('payum.storage.flux_se_sylius_payment', $serviceId);

                return;
            }
        }
    }

    private function findModelClassAttribute(Definition $serviceDefinition): ?string
    {
        /** @var string[] $attributes */
        foreach ($serviceDefinition->getTag('payum.storage') as $attributes) {
            return $attributes['model_class'] ?? null;
        }

        return null;
    }
}
