<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * @deprecated Use StripeGatewayConfigurationType::class instead.
 */
class StripeCheckoutSessionGatewayConfigurationType extends AbstractType
{
    public function __construct()
    {
        @trigger_error(
            sprintf(
                '"%s" class has been deprecated please use "%s" instead.',
                __CLASS__,
                StripeGatewayConfigurationType::class
            ),
            \E_USER_DEPRECATED
        );
    }

    public function getParent(): ?string
    {
        return StripeGatewayConfigurationType::class;
    }
}
