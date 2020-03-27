<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface LineItemImageProviderInterface
{
    /**
     * @param OrderItemInterface $orderItem
     *
     * @return string|null
     */
    public function getImageUrl(OrderItemInterface $orderItem): ?string;

    /**
     * @param ProductInterface $product
     *
     * @return string|null
     */
    public function getImageUrlFromProduct(ProductInterface $product): ?string;
}
