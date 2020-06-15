<?php

declare(strict_types=1);

namespace JeroenG\Statinator;

interface ActionableInterface
{
    public function execute(StateMachineInterface $stateMachine, string $transition): void;
    public function getState(): string;
    public function getTransition(): string;
}
