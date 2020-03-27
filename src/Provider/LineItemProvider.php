<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderItemInterface;

final class LineItemProvider implements LineItemProviderInterface
{
    /** @var LineItemImageProviderInterface */
    private $itemImageProvider;

    /**
     * @param LineItemImageProviderInterface $lineItemImageProvider
     */
    public function __construct(LineItemImageProviderInterface $lineItemImageProvider)
    {
        $this->itemImageProvider = $lineItemImageProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getLineItem(OrderItemInterface $orderItem): ?array
    {
        $order = $orderItem->getOrder();

        if (null === $order) {
            return null;
        }

        $imageUrl = $this->itemImageProvider->getImageUrl($orderItem);
        $images = [];
        if (null !== $imageUrl) {
            $images[] = $imageUrl;
        }

        return [
            'amount' => $orderItem->getTotal(),
            'currency' => $order->getCurrencyCode(),
            'name' => $orderItem->getVariantName() ?? $orderItem->getProductName(),
            'quantity' => $orderItem->getQuantity(),
            'images' => $images,
        ];
    }
}
