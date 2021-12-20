<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Extension;

use FluxSE\SyliusPayumStripePlugin\Action\ConvertPaymentActionInterface;
use FluxSE\SyliusPayumStripePlugin\Factory\CancelPaymentIntentRequestFactoryInterface;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Request\Convert;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * This extension will cancel a PaymentIntent if there is an existant one
 * inside the payment details
 */
final class CancelExistingPaymentIntentExtension implements ExtensionInterface
{
    /** @var CancelPaymentIntentRequestFactoryInterface */
    private $cancelPaymentIntentRequestFactory;

    public function __construct(CancelPaymentIntentRequestFactoryInterface $cancelPaymentIntentRequestFactory)
    {
        $this->cancelPaymentIntentRequestFactory = $cancelPaymentIntentRequestFactory;
    }

    public function onPreExecute(Context $context): void
    {
    }

    public function onExecute(Context $context): void
    {
        $action = $context->getAction();

        if (false === $action instanceof ConvertPaymentActionInterface) {
            return;
        }

        $request = $context->getRequest();
        if (false === $request instanceof Convert) {
            return;
        }

        $payment = $request->getSource();
        if (false === $payment instanceof PaymentInterface) {
            return;
        }

        $details = $payment->getDetails();
        /** @var string|null $object */
        $object = $details['object'] ?? null;
        if (PaymentIntent::OBJECT_NAME !== $object) {
            return;
        }

        /** @var string|null $id */
        $id = $details['id'] ?? null;
        if (null === $id) {
            return;
        }

        $gateway = $context->getGateway();
        $cancelPaymentIntentRequest = $this->cancelPaymentIntentRequestFactory->createNew($id);

        try {
            $gateway->execute($cancelPaymentIntentRequest);
        } catch (ApiErrorException $e) {
            // Avoid error message like :
            //   "You cannot cancel this PaymentIntent because it has a status of canceled.
            //   Only a PaymentIntent with one of the following statuses may be canceled:
            //   requires_payment_method, requires_capture, requires_confirmation,
            //   requires_action, processing."
        }
    }

    public function onPostExecute(Context $context): void
    {
    }
}
