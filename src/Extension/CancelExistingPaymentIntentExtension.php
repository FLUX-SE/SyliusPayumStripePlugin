<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Extension;

use FluxSE\SyliusPayumStripePlugin\Action\ConvertPaymentActionInterface;
use FluxSE\SyliusPayumStripePlugin\Factory\AllSessionRequestFactoryInterface;
use FluxSE\SyliusPayumStripePlugin\Factory\ExpireSessionRequestFactoryInterface;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Request\Convert;
use Stripe\Checkout\Session;
use Stripe\Collection;
use Stripe\PaymentIntent;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * This extension will cancel a PaymentIntent if there is an existant one
 * inside the payment details
 *
 * UPDATE [09/2022] : Instead of canceling the PaymentIntent now it will Expire the related session
 *
 * @see https://stripe.com/docs/api/payment_intents/cancel
 * You cannot cancel the PaymentIntent for a Checkout Session. Expire the Checkout Session instead
 * @see https://github.com/FLUX-SE/SyliusPayumStripePlugin/issues/32
 */
final readonly class CancelExistingPaymentIntentExtension implements ExtensionInterface
{
    public function __construct(private ExpireSessionRequestFactoryInterface $expireSessionRequestFactory, private AllSessionRequestFactoryInterface $allSessionRequestFactory)
    {
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

        //Retrieve the corresponding Session
        $allSessionRequest = $this->allSessionRequestFactory->createNew();
        $allSessionRequest->setParameters([
            'payment_intent' => $id,
        ]);

        $gateway->execute($allSessionRequest);

        /** @var Collection $sessions */
        $sessions = $allSessionRequest->getApiResources();
        /** @var Session|null $session */
        $session = $sessions->first();
        if (null === $session) {
            return;
        }

        if (Session::STATUS_OPEN !== $session->status) {
            return;
        }

        $expireSessionRequest = $this->expireSessionRequestFactory->createNew($session->id);
        $gateway->execute($expireSessionRequest);
    }

    public function onPostExecute(Context $context): void
    {
    }
}
