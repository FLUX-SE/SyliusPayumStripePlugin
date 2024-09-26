<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Abstraction\StateMachine;

interface StateMachineInterface
{
    public function getTransitionToState(object $subject, string $graphName, string $toState): ?string;

    public function apply(object $subject, string $graphName, string $transition, array $context = []): void;
}
