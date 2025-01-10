<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker;

use Mockery;
use Mockery\MockInterface;
use Payum\Core\Action\ActionInterface;

final class PayumActionMocker
{
    /**
     * @template T of ActionInterface
     * @param class-string<T> $className
     */
    public function __invoke(string $name, string $className): MockInterface
    {
        /** @var null|(MockInterface&T) $mock */
        $mock = Mockery::fetchMock($name);

        return $mock ?? Mockery::namedMock($name, $className);
    }
}
