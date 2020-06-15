<?php

declare(strict_types=1);

namespace JeroenG\Statinator\Exception;

use Exception;

final class TransitionNotAllowedException extends Exception
{
    public function __construct(string $transition, string $state)
    {
        parent::__construct("Transition {$transition} is not allowed given the state {$state}");
    }
}
