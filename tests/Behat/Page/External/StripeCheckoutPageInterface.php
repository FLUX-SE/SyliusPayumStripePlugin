<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Page\External;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface StripeCheckoutPageInterface extends PageInterface
{
    public function capture(): void;

    public function notify(string $content): void;
}
