<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Abstraction\StateMachine;

use Sylius\Abstraction\StateMachine\StateMachineInterface as SyliusAbstractionStateMachineInterface;

final class CompositeStateMachine implements StateMachineInterface
{
    private SyliusAbstractionStateMachineInterface $stateMachine;

    public function __construct(SyliusAbstractionStateMachineInterface $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    public function getTransitionToState(object $subject, string $graphName, string $toState): ?string
    {
        return $this->stateMachine->getTransitionToState($subject, $graphName, $toState);
    }

    public function apply(object $subject, string $graphName, string $transition, array $context = []): void {
        $this->stateMachine->apply($subject, $graphName, $transition, $context);
    }
}
