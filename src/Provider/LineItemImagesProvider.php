<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Liip\ImagineBundle\Templating\FilterExtension;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class LineItemImagesProvider implements LineItemImagesProviderInterface
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

    public function getImageUrls(OrderItemInterface $orderItem): array
    {
        $product = $orderItem->getProduct();

        if (null === $product) {
            return [];
        }

        $imageUrl = $this->getImageUrlFromProduct($product);
        if (!$imageUrl) {
            return [];
        }

        return [
            $imageUrl,
        ];
    }

    public function getImageUrlFromProduct(ProductInterface $product): string
    {
        $path = $this->fallbackImage;

        if (false !== $product->getImages()->first()) {
            /** @var ProductImageInterface $first */
            $first = $product->getImages()->first();
            $path = $first->getPath();
            Assert::notNull($path, 'The first product image path should not be null !');
        }

        return $this->getUrlFromPath($path);
    }

    private function getUrlFromPath(string $path): string
    {
        // if given path is empty, InvalidParameterException will be thrown in filter action
        if (empty($path)) {
            return $this->fallbackImage;
        }

        try {
            $url = $this->filterExtension->filter($path, $this->filterName);
        } catch (\Exception $e) {
            return $this->fallbackImage;
        }

        // Localhost images are not displayed by Stripe because it cache it on a CDN
        if (0 !== preg_match('#//localhost#', $url)) {
            $url = $this->fallbackImage;
        }

        return $url;
    }
}
