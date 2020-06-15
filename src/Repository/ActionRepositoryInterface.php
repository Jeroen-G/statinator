<?php

declare(strict_types=1);

namespace JeroenG\Statinator\Repository;

use JeroenG\Statinator\ActionableInterface;
use Throwable;

interface ActionRepositoryInterface
{
    public function add(ActionableInterface $actionable, ?Throwable $throwable = null): void;
    public function all(): iterable;
}
