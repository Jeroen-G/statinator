<?php

declare(strict_types=1);

namespace JeroenG\Statinator\Repository;

use JeroenG\Statinator\ActionableInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class LogActionRepository implements ActionRepositoryInterface
{
    private array $ledger = [];
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function add(ActionableInterface $actionable, ?Throwable $throwable = null): void
    {
        $data = [
            'state' => $actionable->getState(),
            'transition' => $actionable->getTransition(),
            'action' => get_class($actionable),
            'exception' => $throwable ? $throwable->getMessage() : false,
        ];

        $this->ledger[] = $data;

        $successOrFailure = $throwable ? 'failure' : 'success';

        $this->logger->info("Action was execution with {$successOrFailure}", $data);
    }

    public function all(): iterable
    {
        return $this->ledger;
    }
}
