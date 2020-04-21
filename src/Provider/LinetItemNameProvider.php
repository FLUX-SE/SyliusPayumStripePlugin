<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use LogicException;
use Sylius\Component\Core\Model\OrderItemInterface;

class LinetItemNameProvider implements LinetItemNameProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItemName(OrderItemInterface $orderItem): string
    {
        $itemName = $orderItem->getProductName() ?? $orderItem->getVariantName();

        if (null === $itemName || '' === $itemName) {
            throw new LogicException(
                'The line item name is null or empty, please provide an "$orderItem" with a `productName` or a `variantName` !'
            );
        }

        return $itemName;
    }
}
