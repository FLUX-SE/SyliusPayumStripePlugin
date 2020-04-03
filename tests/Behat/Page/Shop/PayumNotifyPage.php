<?php

declare(strict_types=1);

namespace Tests\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Behat\Page\Shop;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Symfony\Component\Routing\RouterInterface;

class PayumNotifyPage extends SymfonyPage implements PayumNotifyPageInterface
{
    /** @var string */
    private $routeName;

    /**
     * @param Session $session
     * @param $minkParameters
     * @param RouterInterface $router
     * @param string $routeName
     */
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        string $routeName
    )
    {
        parent::__construct($session, $minkParameters, $router);
        $this->routeName = $routeName;
    }

    /**
     * {@inheritDoc}
     */
    public function getNotifyUrl(array $urlParameters): string
    {
        return $this->getUrl($urlParameters);
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }
}