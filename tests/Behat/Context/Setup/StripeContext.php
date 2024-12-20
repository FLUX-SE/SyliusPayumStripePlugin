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
    public function __construct(private readonly SharedStorageInterface $sharedStorage, private readonly PaymentMethodRepositoryInterface $paymentMethodRepository, private readonly ExampleFactoryInterface $paymentMethodExampleFactory, private readonly EntityManagerInterface $paymentMethodManager)
    {
    }

    /**
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode and Stripe Checkout Session payment gateway
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode and Stripe Checkout Session payment gateway without using authorize
     */
    public function theStoreHasAPaymentMethodWithACodeAndStripeCheckoutSessionPaymentGateway(
        string $paymentMethodName,
        string $paymentMethodCode,
        bool $useAuthorize = false,
    ): void {
        $paymentMethod = $this->createPaymentMethodStripe(
            $paymentMethodName,
            $paymentMethodCode,
            'stripe_checkout_session',
            'Stripe Checkout Session',
        );

        $this->createPaymentMethod($paymentMethod, $useAuthorize);
    }

    /**
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode and Stripe Checkout Session payment gateway using authorize
     */
    public function theStoreHasAPaymentMethodWithACodeAndStripeCheckoutSessionPaymentGatewayUsingAuthorize(
        string $paymentMethodName,
        string $paymentMethodCode,
    ): void {
        $this->theStoreHasAPaymentMethodWithACodeAndStripeCheckoutSessionPaymentGateway($paymentMethodName, $paymentMethodCode, true);
    }

    /**
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode and Stripe JS payment gateway
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode and Stripe JS payment gateway without using authorize
     */
    public function theStoreHasAPaymentMethodWithACodeAndStripeJsPaymentGateway(
        string $paymentMethodName,
        string $paymentMethodCode,
        bool $useAuthorize = false,
    ): void {
        $paymentMethod = $this->createPaymentMethodStripe(
            $paymentMethodName,
            $paymentMethodCode,
            'stripe_js',
            'Stripe JS',
        );

        $this->createPaymentMethod($paymentMethod, $useAuthorize);
    }

    /**
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode and Stripe JS payment gateway using authorize
     */
    public function theStoreHasAPaymentMethodWithACodeAndStripeJsPaymentGatewayUsingAuthorize(
        string $paymentMethodName,
        string $paymentMethodCode,
    ): void {
        $this->theStoreHasAPaymentMethodWithACodeAndStripeJsPaymentGateway($paymentMethodName, $paymentMethodCode, true);
    }

    private function createPaymentMethodStripe(
        string $name,
        string $code,
        string $factoryName,
        string $description = '',
        bool $addForCurrentChannel = true,
        int $position = null,
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

    private function createPaymentMethod(PaymentMethodInterface $paymentMethod, bool $useAuthorize): void
    {
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
}
