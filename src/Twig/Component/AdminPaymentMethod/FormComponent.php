<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Twig\Component\AdminPaymentMethod;

use Sylius\Bundle\UiBundle\Twig\Component\LiveCollectionTrait;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Factory\PaymentMethodFactoryInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Webmozart\Assert\Assert;

#[AsLiveComponent]
class FormComponent
{
    use LiveCollectionTrait;
    use TemplatePropTrait;

    /** @use ResourceFormComponentTrait<PaymentMethodInterface> */
    use ResourceFormComponentTrait {
        initialize as public __construct;
    }

    /**
     * @param RepositoryInterface<PaymentMethodInterface> $paymentMethodRepository
     * @param PaymentMethodFactoryInterface<PaymentMethodInterface> $paymentMethodFactory
     */
    public function __construct(
        RepositoryInterface $paymentMethodRepository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        private readonly PaymentMethodFactoryInterface $paymentMethodFactory,
    ) {
        $this->initialize($paymentMethodRepository, $formFactory, $resourceClass, $formClass);
    }

    #[LiveProp(fieldName: 'factoryName')]
    public ?string $factoryName = null;

    protected function createResource(): ResourceInterface
    {
        Assert::notNull($this->factoryName, 'A factory name is required to create a new payment method.');

        return $this->paymentMethodFactory->createWithGateway($this->factoryName);
    }
}
