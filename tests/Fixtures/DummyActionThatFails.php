<?php

declare(strict_types=1);

namespace JeroenG\Statinator\Tests\Fixtures;

use JeroenG\Statinator\ActionableInterface;
use JeroenG\Statinator\StateMachineInterface;
use RuntimeException;

final class DummyActionThatFails implements ActionableInterface
{
    private StateMachineInterface $stateMachine;
    private string $transition;

    public function getState(): string
    {
        return $this->stateMachine->getState();
    }

    public function execute(StateMachineInterface $stateMachine, string $transition): void
    {
        $this->stateMachine = $stateMachine;
        $this->transition = $transition;

        $this->stateMachine->getSubject()->triggered = true;

        throw new RuntimeException('Exception thrown');
    }

    public function getTransition(): string
    {
        return $this->transition;
    }
}
