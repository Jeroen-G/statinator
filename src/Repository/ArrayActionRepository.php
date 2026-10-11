<?php

declare(strict_types=1);

namespace JeroenG\Statinator\Repository;

use JeroenG\Statinator\ActionableInterface;
use Throwable;

final class ArrayActionRepository implements ActionRepositoryInterface
{
    private array $ledger = [];

    public function add(ActionableInterface $actionable, ?Throwable $throwable = null): void
    {
        $this->ledger[] = [
            'state' => $actionable->getState(),
            'transition' => $actionable->getTransition(),
            'action' => get_class($actionable),
            'exception' => $throwable ? $throwable->getMessage() : false,
        ];
    }

    public function all(): iterable
    {
        return $this->ledger;
    }
}
