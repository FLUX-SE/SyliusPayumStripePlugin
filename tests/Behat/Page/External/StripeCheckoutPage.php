<?php

declare(strict_types=1);

namespace Tests\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Behat\Page\External;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\Page;
use Payum\Core\Security\TokenInterface;
use RuntimeException;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpKernel\HttpKernelBrowser;
use Tests\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Behat\Page\Shop\PayumNotifyPageInterface;

final class StripeCheckoutPage extends Page implements StripeCheckoutPageInterface
{
    /** @var RepositoryInterface */
    private $securityTokenRepository;

    /** @var HttpKernelBrowser */
    private $client;

    /** @var PayumNotifyPageInterface */
    private $payumNotifyPage;

    /**
     * @param $minkParameters
     */
    public function __construct(
        Session $session,
        $minkParameters,
        RepositoryInterface $securityTokenRepository,
        HttpKernelBrowser $client,
        PayumNotifyPageInterface $payumNotifyPage
    ) {
        parent::__construct($session, $minkParameters);

        $this->securityTokenRepository = $securityTokenRepository;
        $this->client = $client;
        $this->payumNotifyPage = $payumNotifyPage;
    }

    /**
     * {@inheritdoc}
     *
     * @throws DriverException
     */
    public function capture(): void
    {
        $afterToken = $this->findToken();

        $this->getDriver()->visit($afterToken->getTargetUrl());
    }

    /**
     * @return string[]
     */
    private function generateSignature(string $payload): array
    {
        $now = time();
        $webhookSecretKey = 'whsec_test';

        $signedPayload = sprintf('%s.%s', $now, $payload);
        $signature = hash_hmac('sha256', $signedPayload, $webhookSecretKey);

        $sigHeader = sprintf('t=%s,', $now);
        $sigHeader .= sprintf('v1=%s,', $signature);
        // Useless but here to tests legacy too
        $sigHeader .= 'v0=6ffbb59b2300aae63f272406069a9788598b792a944a07aba816edb039989a39';

        return [
            'HTTP_STRIPE_SIGNATURE' => $sigHeader,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function notify(string $content): void
    {
        $notifyToken = $this->findToken(false);

        $notifyUrl = $this->payumNotifyPage->getNotifyUrl([
            'gateway' => 'stripe_checkout_session',
        ]);

        $payload = sprintf($content, $notifyToken->getHash());
        $this->client->request(
            'POST',
            $notifyUrl,
            [],
            [],
            $this->generateSignature($payload),
            $payload
        );
    }

    private function findToken(bool $afterType = true): TokenInterface
    {
        /** @var TokenInterface $token */
        foreach ($this->securityTokenRepository->findAll() as $token) {
            if ($afterType && null === $token->getAfterUrl()) {
                return $token;
            }

            if (!$afterType && null !== $token->getAfterUrl()) {
                return $token;
            }
        }

        throw new RuntimeException('Cannot find token, check if you are after proper checkout steps');
    }

    /**
     * {@inheritdoc}
     */
    protected function getUrl(array $urlParameters = []): string
    {
        return 'https://stripe.com';
    }
}
