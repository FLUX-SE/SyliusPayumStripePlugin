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
     * @param $minkParameters
     */
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        string $routeName
    ) {
        parent::__construct($session, $minkParameters, $router);
        $this->routeName = $routeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotifyUrl(array $urlParameters): string
    {
        return $this->getUrl($urlParameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }
}
