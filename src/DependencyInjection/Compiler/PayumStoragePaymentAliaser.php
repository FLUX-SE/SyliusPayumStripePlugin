<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PayumStoragePaymentAliaser implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $paymentClass = $container->getParameter('sylius.model.payment.class');
        $serviceIds = $container->findTaggedServiceIds('payum.storage');
        /** @var string $serviceId */
        foreach (array_keys($serviceIds) as $serviceId) {
            $serviceDefinition = $container->findDefinition($serviceId);
            /** @var string[] $attributes */
            foreach ($serviceDefinition->getTag('payum.storage') as $attributes) {
                $modelClass = $attributes['model_class'] ?? null;
                if (null === $modelClass) {
                    continue;
                }
                if ($paymentClass !== $modelClass) {
                    continue;
                }

                $container->setAlias('payum.storage.flux_se_sylius_payment', $serviceId);

                return;
            }
        }
    }
}
