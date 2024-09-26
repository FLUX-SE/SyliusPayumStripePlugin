<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Abstraction\StateMachine;

use SM\Factory\FactoryInterface;
use Sylius\Resource\StateMachine\StateMachineInterface as SyliusStateMachineInterface;
use Webmozart\Assert\Assert;

final class WinzouStateMachine implements StateMachineInterface
{
    private FactoryInterface $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function getTransitionToState(object $subject, string $graphName, string $toState): ?string
    {
        $stateMachine = $this->factory->get($subject, $graphName);
        Assert::isInstanceOf($stateMachine, SyliusStateMachineInterface::class);

        return $stateMachine->getTransitionToState($toState);
    }

    public function apply(object $subject, string $graphName, string $transition, array $context = []): void
    {
        $stateMachine = $this->factory->get($subject, $graphName);

        $stateMachine->apply($transition);
    }
}
