<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

final class LineItemsProvider implements LineItemsProviderInterface
{
    /** @var LineItemProviderInterface */
    private $lineItemProvider;

    /**
     * @param LineItemProviderInterface $lineItemProvider
     */
    public function __construct(LineItemProviderInterface $lineItemProvider)
    {
        $this->lineItemProvider = $lineItemProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getLineItems(OrderInterface $order): ?array
    {
        $lineItems = [];
        foreach ($order->getItems() as $orderItem) {

            $lineItem = $this->lineItemProvider->getLineItem($orderItem);
            if (null !== $lineItem) {
                $lineItems[] = $lineItem;
            }
        }

        return $lineItems;
    }
}
