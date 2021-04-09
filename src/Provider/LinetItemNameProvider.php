<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderItemInterface;

class LinetItemNameProvider implements LinetItemNameProviderInterface
{
    public function getItemName(OrderItemInterface $orderItem): string
    {
        $itemName = $this->buildProductName($orderItem);

        return sprintf('%sx - %s', $orderItem->getQuantity(), $itemName);
    }

    protected function buildProductName(OrderItemInterface $orderItem): string
    {
        $variantName = (string) $orderItem->getVariantName();
        $productName = (string) $orderItem->getProductName();

        if (empty($variantName)) {
            return $productName;
        }

        if (empty($productName)) {
            return $variantName;
        }

        if ($productName === $variantName) {
            return $productName;
        }

        return sprintf('%s %s', $productName, $variantName);
    }
}
