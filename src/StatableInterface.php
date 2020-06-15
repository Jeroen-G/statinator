<?php

declare(strict_types=1);

namespace JeroenG\Statinator;

interface StatableInterface
{
    public function getState(): string;
    public function setState(string $to): void;
}
