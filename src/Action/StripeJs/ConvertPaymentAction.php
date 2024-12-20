<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Action\StripeJs;

use FluxSE\SyliusPayumStripePlugin\Provider\StripeJs\DetailsProviderInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Convert;
use Sylius\Component\Core\Model\PaymentInterface;

final readonly class ConvertPaymentAction implements ConvertPaymentActionInterface
{
    public function __construct(private DetailsProviderInterface $detailsProvider)
    {
    }

    /** @param Convert $request */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        $details = $this->detailsProvider->getDetails($payment);

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
