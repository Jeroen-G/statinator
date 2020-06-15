<?php

declare(strict_types=1);

namespace JeroenG\Statinator\Tests\Fixtures;

use JeroenG\Statinator\ActionableInterface;
use JeroenG\Statinator\StateMachineInterface;

final class DummyAction implements ActionableInterface
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
    }

    public function getTransition(): string
    {
        return $this->transition;
    }
}
