<?php

declare(strict_types=1);

namespace Tests\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Behat\Page\External;

use FriendsOfBehat\PageObjectExtension\Page\Page;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpKernel\HttpKernelBrowser;

final class StripeCheckoutPage extends Page implements StripeCheckoutPageInterface
{
    /** @var RepositoryInterface */
    private $securityTokenRepository;

    /** @var HttpKernelBrowser */
    private $client;

    public function setClient(HttpKernelBrowser $client): void
    {
        $this->client = $client;
    }

    public function setSecurityTokenRepository(RepositoryInterface $securityTokenRepository): void
    {
        $this->securityTokenRepository = $securityTokenRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function pay($data = [])
    {
        $this->getDriver()->visit($this->findToken()->getAfterUrl() . '?' . http_build_query($data));
    }

    /**
     * {@inheritdoc}
     */
    public function notify(array $data): void
    {
        $notifyToken = $this->findToken('notify');
        $this->client->request('GET', $notifyToken->getAfterUrl(), $data);
    }

    /**
     * {@inheritdoc}
     */
    public function cancel()
    {
        $this->getDriver()->visit($this->findToken()->getTargetUrl());
    }

    private function findToken(string $type = 'capture'): TokenInterface
    {
        $tokens = [];
        /** @var TokenInterface $token */
        foreach ($this->securityTokenRepository->findAll() as $token) {
            if (strpos($token->getTargetUrl(), $type)) {
                $tokens[] = $token;
            }
        }
        if (count($tokens) > 0) {
            return end($tokens);
        }

        throw new \RuntimeException("Cannot find $type token, check if you are after proper checkout steps");
    }

    protected function getUrl(array $urlParameters = []): string
    {
        return 'http://localhost';
    }
}
