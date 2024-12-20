<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Exception;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;

final readonly class LineItemImagesProvider implements LineItemImagesProviderInterface
{
    public function __construct(private CacheManager $imagineCacheManager, private string $filterName, private string $fallbackImage, private string $localhostPattern = '#//localhost#')
    {
    }

    public function getImageUrls(OrderItemInterface $orderItem): array
    {
        $product = $orderItem->getProduct();

        if (null === $product) {
            return [];
        }

        $imageUrl = $this->getImageUrlFromProduct($product);
        if ('' === $imageUrl) {
            return [];
        }

        return [
            $imageUrl,
        ];
    }

    public function getImageUrlFromProduct(ProductInterface $product): string
    {
        $path = '';

        /** @var ProductImageInterface|false $firstImage */
        $firstImage = $product->getImages()->first();
        if (false !== $firstImage) {
            $first = $firstImage;
            $path = $first->getPath();
        }

        if (null === $path) {
            return $this->fallbackImage;
        }

        if ('' === $path) {
            return $this->fallbackImage;
        }

        return $this->getUrlFromPath($path);
    }

    private function getUrlFromPath(string $path): string
    {
        // if given path is empty, InvalidParameterException will be thrown in filter action
        if ('' === $path) {
            return $this->fallbackImage;
        }

        try {
            $url = $this->imagineCacheManager->getBrowserPath($path, $this->filterName);
        } catch (Exception) {
            return $this->fallbackImage;
        }

        if ('' === $this->localhostPattern) {
            return $url;
        }

        if (0 !== preg_match($this->localhostPattern, $url)) {
            $url = $this->fallbackImage;
        }

        return $url;
    }
}
