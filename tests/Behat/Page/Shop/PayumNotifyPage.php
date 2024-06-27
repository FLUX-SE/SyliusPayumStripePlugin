<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Page\Shop;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Symfony\Component\Routing\RouterInterface;

class PayumNotifyPage extends SymfonyPage implements PayumNotifyPageInterface
{
    private string $routeName;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        string $routeName
    ) {
        parent::__construct($session, $minkParameters, $router);
        $this->routeName = $routeName;
    }

    public function getNotifyUrl(array $urlParameters): string
    {
        return $this->getUrl($urlParameters);
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }
}
