<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Page\External;

use ArrayAccess;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\Page;
use Payum\Core\Security\TokenInterface;
use RuntimeException;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpKernel\HttpKernelBrowser;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Page\Shop\PayumNotifyPageInterface;

final class StripePage extends Page implements StripePageInterface
{
    /** @var string[] */
    private array $deadTokens = [];

    /**
     * @param array<string, mixed>|ArrayAccess<string, mixed> $minkParameters
     */
    public function __construct(
        Session $session,
        $minkParameters,
        private readonly RepositoryInterface $securityTokenRepository,
        private readonly HttpKernelBrowser $client,
        private readonly PayumNotifyPageInterface $payumNotifyPage,
        private readonly string $gatewayName,
    ) {
        parent::__construct($session, $minkParameters);
    }

    /**
     * @throws DriverException
     */
    public function captureOrAuthorizeThenGoToAfterUrl(): void
    {
        try {
            $token = $this->findToken();
        } catch (RuntimeException) {
            // No easy way to know if we need authorize or not
            $token = $this->findToken('authorize');
        }

        // Capture or Authorize
        $this->getDriver()->visit($token->getTargetUrl());

        $this->getDriver()->visit($token->getAfterUrl());
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

    public function notify(string $content): void
    {
        $notifyToken = $this->findToken('notify');

        $notifyUrl = $this->payumNotifyPage->getNotifyUrl([
            'gateway' => $this->gatewayName,
        ]);

        $payload = sprintf($content, $notifyToken->getHash());
        $this->client->request(
            'POST',
            $notifyUrl,
            [],
            [],
            $this->generateSignature($payload),
            $payload,
        );
    }

    private function findToken(string $type = 'capture'): TokenInterface
    {
        $foundToken = null;
        /** @var TokenInterface[] $tokens */
        $tokens = $this->securityTokenRepository->findAll();
        foreach ($tokens as $token) {
            if (in_array($token->getHash(), $this->deadTokens, true)) {
                continue;
            }

            if (!str_contains($token->getTargetUrl(), $type)) {
                continue;
            }

            $foundToken = $token;
        }

        if (null === $foundToken) {
            throw new RuntimeException('Cannot find token, check if you are after proper checkout steps');
        }

        // Sometimes the token found is an already consumed one. Here we compare
        // the $foundToken->getAfterUrl() with all tokens to see if the token
        // concerned by the after url is alive, if not we save it to a dead list
        // and retry to found the right token
        if ($type !== 'notify') {
            $relatedToken = null;
            foreach ($tokens as $token) {
                if (!str_contains($foundToken->getAfterUrl(), $token->getHash())) {
                    continue;
                }
                $relatedToken = $token;
            }

            if (null === $relatedToken) {
                $this->deadTokens[] = $foundToken->getHash();

                return $this->findToken($type);
            }
        }

        return $foundToken;
    }

    /**
     * @param array<string, string> $urlParameters
     */
    protected function getUrl(array $urlParameters = []): string
    {
        return 'https://stripe.com';
    }
}
