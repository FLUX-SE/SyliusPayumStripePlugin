<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Action;

use FluxSE\SyliusPayumStripePlugin\Provider\DetailsProviderInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Convert;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class ConvertPaymentAction implements ConvertPaymentActionInterface
{
    /** @var DetailsProviderInterface */
    private $detailsProvider;

    public function __construct(DetailsProviderInterface $detailsProvider)
    {
        $this->detailsProvider = $detailsProvider;
    }

    /** @param Convert $request */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        $details = $payment->getDetails();

        $id = $details['id'];
        if (is_string($id) && $id !== '') {
            $request->setResult($details);
            return;
        }

        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        $details = $this->detailsProvider->getDetails($order);

        $request->setResult($details);
    }

    public function supports($request): bool
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() === 'array'
        ;
    }
}
