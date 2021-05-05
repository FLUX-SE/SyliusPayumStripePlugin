<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class WinzouStateMachineCallbacksModifier implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $refundDisabled = $container->getParameter('flux_se.sylius_payum_stripe.refund.disabled');

        $container->prependExtensionConfig('winzou_state_machine', [
            'sylius_payment' => [
                'callbacks' => [
                    'before' => [
                        'flux_se.sylius_payum_stripe_refund' => [
                            'disabled' => $refundDisabled
                        ],
                    ],
                ],
            ],
        ]);
    }
}
