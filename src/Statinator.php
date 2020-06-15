<?php

declare(strict_types=1);

namespace JeroenG\Statinator;

use JeroenG\Statinator\Repository\ActionRepositoryInterface;
use JeroenG\Statinator\Repository\ArrayActionRepository;

final class Statinator
{
    private array $config;
    private ActionRepositoryInterface $actionRepository;

    public function __construct(array $config = [], ?ActionRepositoryInterface $repository = null)
    {
        $this->config = $config;
        $this->actionRepository = $repository ?? new ArrayActionRepository();
    }

    public function getStates(): array
    {
        return $this->config['states'];
    }

    public function getTransitions(): array
    {
        return $this->config['transitions'];
    }

    public function setStates(array $states): void
    {
        $this->config['states'] = $states;
    }

    public function setTransitions(array $transitions): void
    {
        $this->config['transitions'] = $transitions;
    }

    public function get(StatableInterface $object): StateMachine
    {
        return new StateMachine($object, $this->config, $this->actionRepository);
    }

    public function onEntry(string $transition, $action): self
    {
        $this->config['transitions'][$transition]['on']['entry'][] = $action;

        return $this;
    }

    public function onExit(string $transition, $action): self
    {
        $this->config['transitions'][$transition]['on']['exit'][] = $action;

        return $this;
    }

    public function getActionsForTransition(string $transition): array
    {
        return $this->config['transitions'][$transition]['on'];
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
