<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemImagesProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemImagesProviderInterface;
use Liip\ImagineBundle\Templating\FilterExtension;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class LineItemImagesProviderSpec extends ObjectBehavior
{
    public function let(FilterExtension $filterExtension): void
    {
        $this->beConstructedWith(
            $filterExtension,
            'my_filter',
            'https://somewhere-online.tld/fallbak.jpg'
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(LineItemImagesProvider::class);
        $this->shouldHaveType(LineItemImagesProviderInterface::class);
    }

    public function it_get_image_urls(
        OrderItemInterface $orderItem,
        ProductInterface $product,
        ProductImageInterface $productImage,
        FilterExtension $filterExtension
    ): void {
        $productImage->getPath()->willReturn('/path/image.jpg');
        $orderItem->getProduct()->willReturn($product);
        $product->getImages()->willReturn(new ArrayCollection([
            $productImage->getWrappedObject(),
        ]));
        $filterExtension
            ->filter('/path/image.jpg', 'my_filter')
            ->willReturn('https://somewhere-online.tld/path/image.jpg');

        $this->getImageUrlFromProduct($product)->shouldReturn('https://somewhere-online.tld/path/image.jpg');

        $this->getImageUrls($orderItem)->shouldReturn([
            'https://somewhere-online.tld/path/image.jpg',
        ]);
    }

    public function it_get_fallback_image_urls_on_localhost(
        OrderItemInterface $orderItem,
        ProductInterface $product,
        ProductImageInterface $productImage,
        FilterExtension $filterExtension
    ): void {
        $productImage->getPath()->willReturn('/path/image.jpg');
        $orderItem->getProduct()->willReturn($product);
        $product->getImages()->willReturn(new ArrayCollection([
            $productImage->getWrappedObject(),
        ]));
        $filterExtension
            ->filter('/path/image.jpg', 'my_filter')
            ->willReturn('https://localhost/path/image.jpg');

        $this->getImageUrlFromProduct($product)->shouldReturn('https://somewhere-online.tld/fallbak.jpg');

        $this->getImageUrls($orderItem)->shouldReturn([
            'https://somewhere-online.tld/fallbak.jpg',
        ]);
    }

    public function it_get_fallback_image_urls__when_there_is_no_image(
        OrderItemInterface $orderItem,
        ProductInterface $product,
        FilterExtension $filterExtension
    ): void {
        $orderItem->getProduct()->willReturn($product);
        $product->getImages()->willReturn(new ArrayCollection());
        $filterExtension
            ->filter('https://somewhere-online.tld/fallbak.jpg', 'my_filter')
            ->willReturn('https://somewhere-online.tld/fallbak.jpg');

        $this->getImageUrlFromProduct($product)->shouldReturn('https://somewhere-online.tld/fallbak.jpg');

        $this->getImageUrls($orderItem)->shouldReturn([
            'https://somewhere-online.tld/fallbak.jpg',
        ]);
    }
}
