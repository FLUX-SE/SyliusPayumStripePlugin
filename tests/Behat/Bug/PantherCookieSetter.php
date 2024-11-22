<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Bug;

use Behat\Mink\Driver\PantherDriver;
use Behat\Mink\Session;
use Sylius\Behat\Service\Setter\CookieSetterInterface;

final class PantherCookieSetter implements CookieSetterInterface
{
    private Session $minkSession;

    private CookieSetterInterface $decoratedCookieSetter;

    public function __construct(
        Session $minkSession,
        CookieSetterInterface $decoratedCookieSetter
    ) {
        $this->decoratedCookieSetter = $decoratedCookieSetter;
        $this->minkSession = $minkSession;
    }

    public function setCookie($name, $value): void
    {
        $driver = $this->minkSession->getDriver();

        if ($driver instanceof PantherDriver) {
            if (!$driver->isStarted()) {
                $driver->start();
            }
        }

        $this->decoratedCookieSetter->setCookie($name, $value);
    }
}
