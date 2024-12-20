<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Api\Payum;

use Sylius\Component\Core\Model\PaymentInterface;

final class AfterUrlProvider implements AfterUrlProviderInterface
{
    private string $afterPath;

    private array $afterParameters;

    public function __construct(
        string $afterPath,
        array $afterParameters = [],
    ) {
        $this->afterPath = $afterPath;
        $this->afterParameters = $afterParameters;
    }

    public function getAfterPath(PaymentInterface $payment): string
    {
        return $this->afterPath;
    }

    public function getAfterParameters(PaymentInterface $payment): array
    {
        return $this->afterParameters;
    }
}
