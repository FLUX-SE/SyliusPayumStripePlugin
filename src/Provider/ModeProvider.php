<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Stripe\Checkout\Session;
use Sylius\Component\Core\Model\OrderInterface;

final class ModeProvider implements ModeProviderInterface
{
    public function getMode(OrderInterface $order): string
    {
        return Session::MODE_PAYMENT;
    }
}
