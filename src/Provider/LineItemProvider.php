<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class LineItemProvider implements LineItemProviderInterface
{
    /** @var LineItemImagesProviderInterface */
    private $lineItemImagesProvider;

    /** @var LinetItemNameProviderInterface */
    private $lineItemNameProvider;

    public function __construct(
        LineItemImagesProviderInterface $lineItemImagesProvider,
        LinetItemNameProviderInterface $lineItemNameProvider
    ) {
        $this->lineItemImagesProvider = $lineItemImagesProvider;
        $this->lineItemNameProvider = $lineItemNameProvider;
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
            'amount' => $orderItem->getTotal()/$orderItem->getQuantity(),
            'currency' => $order->getCurrencyCode(),
            'name' => $this->lineItemNameProvider->getItemName($orderItem),
            'quantity' => $orderItem->getQuantity(),
            'images' => $this->lineItemImagesProvider->getImageUrls($orderItem),
        ];
    }
}
