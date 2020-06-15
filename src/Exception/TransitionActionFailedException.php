<?php

declare(strict_types=1);

namespace JeroenG\Statinator\Exception;

use Exception;

final class TransitionActionFailedException extends Exception
{
    public function __construct(string $transition, $previous = null)
    {
        $message = "An action in the transition {$transition} caused an exception";
        parent::__construct($message, 0, $previous);
    }
}
