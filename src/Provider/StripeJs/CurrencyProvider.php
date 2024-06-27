<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider\StripeJs;

use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;

final class CurrencyProvider implements CurrencyProviderInterface
{
    public function getCurrency(PaymentInterface $payment): string
    {
        $currencyCode = $payment->getCurrencyCode();

        Assert::notNull($currencyCode);

        return $currencyCode;
    }
}
