<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Api\Payum;

use Sylius\Component\Core\Model\PaymentInterface;

final readonly class AfterUrlProvider implements AfterUrlProviderInterface
{
    /**
     * @param array<string, string> $afterParameters
     */
    public function __construct(private string $afterPath, private array $afterParameters = [])
    {
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
