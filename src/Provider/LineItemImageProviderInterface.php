<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface LineItemImageProviderInterface
{
    public function getImageUrl(OrderItemInterface $orderItem): ?string;

    public function getImageUrlFromProduct(ProductInterface $product): ?string;
}
