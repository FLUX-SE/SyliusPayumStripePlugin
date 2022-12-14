<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Webmozart\Assert\Assert;

class StripeContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var ExampleFactoryInterface */
    private $paymentMethodExampleFactory;

    /** @var EntityManagerInterface */
    private $paymentMethodManager;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ExampleFactoryInterface $paymentMethodExampleFactory,
        EntityManagerInterface $paymentMethodManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentMethodExampleFactory = $paymentMethodExampleFactory;
        $this->paymentMethodManager = $paymentMethodManager;
    }

    /**
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode and Stripe payment gateway
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode and Stripe payment gateway without using authorize
     */
    public function theStoreHasAPaymentMethodWithACodeAndStripePaymentGateway(
        string $paymentMethodName,
        string $paymentMethodCode,
        bool $useAuthorize = false
    ): void {
        $paymentMethod = $this->createPaymentMethodStripe(
            $paymentMethodName,
            $paymentMethodCode,
            'stripe_checkout_session',
            'Stripe Checkout Session'
        );

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig);

        $gatewayConfig->setConfig([
            'publishable_key' => 'pk_test_publishablekey',
            'secret_key' => 'sk_test_secretkey',
            'webhook_secret_keys' => [
                'whsec_test',
            ],
            'use_authorize' => $useAuthorize,
        ]);
        $this->paymentMethodManager->flush();
    }

    /**
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode and Stripe payment gateway using authorize
     */
    public function theStoreHasAPaymentMethodWithACodeAndStripePaymentGatewayUsingAuthorize(
        string $paymentMethodName,
        string $paymentMethodCode
    ): void {
        $this->theStoreHasAPaymentMethodWithACodeAndStripePaymentGateway($paymentMethodName, $paymentMethodCode, true);
    }

    private function createPaymentMethodStripe(
        string $name,
        string $code,
        string $factoryName,
        string $description = '',
        bool $addForCurrentChannel = true,
        int $position = null
    ): PaymentMethodInterface {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodExampleFactory->create([
            'name' => ucfirst($name),
            'code' => $code,
            'description' => $description,
            'gatewayName' => $factoryName,
            'gatewayFactory' => $factoryName,
            'enabled' => true,
            'channels' => ($addForCurrentChannel && $this->sharedStorage->has('channel')) ? [$this->sharedStorage->get('channel')] : [],
        ]);
        if (null !== $position) {
            $paymentMethod->setPosition($position);
        }
        $this->sharedStorage->set('payment_method', $paymentMethod);
        $this->paymentMethodRepository->add($paymentMethod);

        return $paymentMethod;
    }
}
