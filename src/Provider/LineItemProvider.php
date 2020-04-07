<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class LineItemProvider implements LineItemProviderInterface
{
    /** @var LineItemImagesProviderInterface */
    private $lineItemImagesProvider;

    public function __construct(LineItemImagesProviderInterface $lineItemImagesProvider)
    {
        $this->lineItemImagesProvider = $lineItemImagesProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getLineItem(OrderItemInterface $orderItem): ?array
    {
        /** @var OrderInterface|null $order */
        $order = $orderItem->getOrder();

        if (null === $order) {
            return null;
        }

        return [
            'amount' => $orderItem->getTotal(),
            'currency' => $order->getCurrencyCode(),
            'name' => $orderItem->getVariantName() ?? $orderItem->getProductName(),
            'quantity' => $orderItem->getQuantity(),
            'images' => $this->lineItemImagesProvider->getImageUrls($orderItem),
        ];
    }
}
