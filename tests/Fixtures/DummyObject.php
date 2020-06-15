<?php

declare(strict_types=1);

namespace JeroenG\Statinator\Tests\Fixtures;

use JeroenG\Statinator\StatableInterface;
use RuntimeException;

final class DummyObject implements StatableInterface
{
    public bool $triggered = false;
    private string $currentState;

    public function __construct(string $initialState)
    {
        $this->currentState = $initialState;
    }

    public function getState(): string
    {
        return $this->currentState;
    }

    public function setState(string $to): void
    {
        $this->currentState = $to;
    }

    public function throwException(): void
    {
        throw new RuntimeException('Exception thrown');
    }
}
