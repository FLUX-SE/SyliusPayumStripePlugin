<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderItemInterface;

class LinetItemNameProvider implements LinetItemNameProviderInterface
{
    public function getItemName(OrderItemInterface $orderItem): string
    {
        $itemName = $this->buildItemName($orderItem);

        return sprintf('%sx - %s', $orderItem->getQuantity(), $itemName);
    }

    protected function buildItemName(OrderItemInterface $orderItem): string
    {
        $variantName = (string) $orderItem->getVariantName();
        $productName = (string) $orderItem->getProductName();

        if ('' === $variantName) {
            return $productName;
        }

        $product = $orderItem->getProduct();

        if (null === $product) {
            return $variantName;
        }

        if (false === $product->hasOptions()) {
            return $variantName;
        }

        return sprintf('%s %s', $productName, $variantName);
    }
}
