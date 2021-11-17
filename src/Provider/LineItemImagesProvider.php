<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Exception;
use Liip\ImagineBundle\Templating\LazyFilterRuntime;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class LineItemImagesProvider implements LineItemImagesProviderInterface
{
    /** @var LazyFilterRuntime */
    private $filterRuntime;

    /** @var string */
    private $filterName;

    /** @var string */
    private $fallbackImage;

    /** @var string */
    private $localhostPattern;

    public function __construct(
        LazyFilterRuntime $filterRuntime,
        string $filterName,
        string $fallbackImage,
        string $localhostPattern = '#//localhost#'
    ) {
        $this->filterRuntime = $filterRuntime;
        $this->filterName = $filterName;
        $this->fallbackImage = $fallbackImage;
        $this->localhostPattern = $localhostPattern;
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
            $url = $this->filterRuntime->filter($path, $this->filterName);
        } catch (Exception $e) {
            return $this->fallbackImage;
        }

        if (0 !== preg_match($this->localhostPattern, $url)) {
            $url = $this->fallbackImage;
        }

        return $url;
    }
}
