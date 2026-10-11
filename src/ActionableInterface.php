<?php

declare(strict_types=1);

namespace JeroenG\Statinator;

interface ActionableInterface
{
    public function execute(StateMachineInterface $stateMachine, string $transition): void;
    public function getState(): string;
    public function getTransition(): string;

    /**
     * Get the exception that was thrown during execution, if any.
     */
    public function getException(): ?Throwable;
}
