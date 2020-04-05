<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Liip\ImagineBundle\Templating\FilterExtension;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class LineItemImageProvider implements LineItemImageProviderInterface
{
    /** @var FilterExtension */
    private $filterExtension;

    /** @var string */
    private $filterName;

    /** @var string */
    private $fallbackImage;

    public function __construct(
        FilterExtension $filterExtension,
        string $filterName,
        string $fallbackImage
    ) {
        $this->filterExtension = $filterExtension;
        $this->filterName = $filterName;
        $this->fallbackImage = $fallbackImage;
    }

    /**
     * {@inheritdoc}
     */
    public function getImageUrl(OrderItemInterface $orderItem): ?string
    {
        $product = $orderItem->getProduct();

        if (null === $product) {
            return null;
        }

        return $this->getImageUrlFromProduct($product);
    }

    /**
     * {@inheritdoc}
     */
    public function getImageUrlFromProduct(ProductInterface $product): ?string
    {
        $path = $this->fallbackImage;
        if (false === $product->getImagesByType('main')->isEmpty()) {
            /** @var ProductImageInterface $first */
            $first = $product->getImagesByType('main')->first();
            $path = $first->getPath();
        }

        if (false !== $product->getImages()->first()) {
            /** @var ProductImageInterface $first */
            $first = $product->getImages()->first();
            $path = $first->getPath();
        }

        return $this->getUrlFromPath($path);
    }

    private function getUrlFromPath(string $path): string
    {
        $url = $this->filterExtension->filter($path, $this->filterName);

        // Localhost images are not displayed by Stripe because they cache it on a CDN
        if (false !== preg_match('#//localhost#', $url)) {
            $url = $this->fallbackImage;
        }

        return $url;
    }
}
