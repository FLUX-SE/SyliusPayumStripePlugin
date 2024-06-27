<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider\StripeJs;

use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;

final class AmountProvider implements AmountProviderInterface
{
    public function getAmount(PaymentInterface $payment): int
    {
        $amount = $payment->getAmount();

        Assert::notNull($amount);

        return $amount;
    }
}
