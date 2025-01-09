<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemImagesProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemImagesProviderInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class LineItemImagesProviderTest extends TestCase
{
    private CacheManager&MockObject $imagineCacheManagerMock;

    private LineItemImagesProvider $lineItemImagesProvider;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->imagineCacheManagerMock = $this->createMock(CacheManager::class);
        $this->lineItemImagesProvider = new LineItemImagesProvider($this->imagineCacheManagerMock, 'my_filter', 'https://somewhere-online.tld/fallbak.jpg');
    }

    public function testInitializable(): void
    {
        $this->assertInstanceOf(LineItemImagesProvider::class, $this->lineItemImagesProvider);
        $this->assertInstanceOf(LineItemImagesProviderInterface::class, $this->lineItemImagesProvider);
    }

    /**
     * @throws Exception
     */
    public function testGetImageUrls(): void
    {
        /** @var OrderItemInterface&MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductInterface&MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var ProductImageInterface&MockObject $productImageMock */
        $productImageMock = $this->createMock(ProductImageInterface::class);
        $productImageMock->expects(self::atLeastOnce())->method('getPath')->willReturn('/path/image.jpg');
        $orderItemMock->expects(self::atLeastOnce())->method('getProduct')->willReturn($productMock);
        $productMock->expects(self::atLeastOnce())->method('getImages')->willReturn(new ArrayCollection([
            $productImageMock,
        ]));
        $this->imagineCacheManagerMock->expects(self::atLeastOnce())->method('getBrowserPath')->with('/path/image.jpg', 'my_filter')
            ->willReturn('https://somewhere-online.tld/path/image.jpg');
        $this->assertSame('https://somewhere-online.tld/path/image.jpg', $this->lineItemImagesProvider->getImageUrlFromProduct($productMock));

        $imageUrls = $this->lineItemImagesProvider->getImageUrls($orderItemMock);

        $this->assertSame([
            'https://somewhere-online.tld/path/image.jpg',
        ], $imageUrls);
    }

    /**
     * @throws Exception
     */
    public function testGetFallbackImageUrlsOnLocalhost(): void
    {
        /** @var OrderItemInterface&MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductInterface&MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var ProductImageInterface&MockObject $productImageMock */
        $productImageMock = $this->createMock(ProductImageInterface::class);
        $productImageMock->expects(self::atLeastOnce())->method('getPath')->willReturn('/path/image.jpg');
        $orderItemMock->expects(self::atLeastOnce())->method('getProduct')->willReturn($productMock);
        $productMock->expects(self::atLeastOnce())->method('getImages')->willReturn(new ArrayCollection([
            $productImageMock,
        ]));
        $this->imagineCacheManagerMock->expects(self::exactly(2))
            ->method('getBrowserPath')
            ->with('/path/image.jpg', 'my_filter')
            ->willReturn('https://localhost/path/image.jpg')
        ;
        $this->assertSame('https://somewhere-online.tld/fallbak.jpg', $this->lineItemImagesProvider->getImageUrlFromProduct($productMock));

        $imageUrls = $this->lineItemImagesProvider->getImageUrls($orderItemMock);

        $this->assertSame([
            'https://somewhere-online.tld/fallbak.jpg',
        ], $imageUrls);
    }

    /**
     * @throws Exception
     */
    public function testGetFallbackImageUrlsWhenThereIsNoImage(): void
    {
        /** @var OrderItemInterface&MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductInterface&MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        $orderItemMock->expects(self::atLeastOnce())->method('getProduct')->willReturn($productMock);
        $productMock->expects(self::atLeastOnce())->method('getImages')->willReturn(new ArrayCollection());

        $imageUrlFromProduct = $this->lineItemImagesProvider->getImageUrlFromProduct($productMock);
        $this->assertSame('https://somewhere-online.tld/fallbak.jpg', $imageUrlFromProduct);

        $imageUrls = $this->lineItemImagesProvider->getImageUrls($orderItemMock);
        $this->assertSame(['https://somewhere-online.tld/fallbak.jpg'], $imageUrls);
    }
}
