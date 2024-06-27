<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Api\Payum;

use Payum\Core\Reply\ReplyInterface;
use Sylius\Component\Core\Model\PaymentInterface;

interface ProcessorInterface
{
    /**
     * @return array{'reply': ReplyInterface|null, "details": array}
     */
    public function __invoke(PaymentInterface $payment, bool $useAuthorize): array;
}
