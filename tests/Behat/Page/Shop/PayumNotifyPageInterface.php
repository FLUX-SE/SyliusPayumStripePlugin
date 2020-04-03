<?php

declare(strict_types=1);

namespace Tests\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Behat\Page\Shop;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface PayumNotifyPageInterface extends SymfonyPageInterface
{
    /**
     * @param array $urlParameters
     *
     * @return string
     */
    public function getNotifyUrl(array $urlParameters): string;
}