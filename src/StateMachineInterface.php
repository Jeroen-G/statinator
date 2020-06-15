<?php

declare(strict_types=1);

namespace JeroenG\Statinator;

interface StateMachineInterface
{
    public function can(string $transition): bool;
    public function apply(string $transition): void;
    public function getState(): string;
    public function getSubject(): StatableInterface;
}
