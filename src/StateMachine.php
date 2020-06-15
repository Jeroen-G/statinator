<?php

declare(strict_types=1);

namespace JeroenG\Statinator;

use JeroenG\Statinator\Exception\TransitionActionFailedException;
use JeroenG\Statinator\Exception\TransitionNotAllowedException;
use JeroenG\Statinator\Repository\ActionRepositoryInterface;
use Throwable;

final class StateMachine implements StateMachineInterface
{
    private array $config;
    private StatableInterface $subject;
    private ActionRepositoryInterface $repository;

    public function __construct(StatableInterface $object, array $config, ActionRepositoryInterface $repository)
    {
        $this->subject = $object;
        $this->config = $config;
        $this->repository = $repository;
    }

    public function can(string $transition): bool
    {
        $configuredTransition = $this->config['transitions'][$transition];
        $currentState = $this->subject->getState();

        return isset($configuredTransition)
            && in_array($currentState, $configuredTransition['from'], true);
    }

    public function apply(string $transition): void
    {
        if (!$this->can($transition)) {
            throw new TransitionNotAllowedException($transition, $this->getState());
        }

        $state = $this->config['transitions'][$transition]['to'][0];

        $this->executeOnEntryOfTransition($transition);

        $this->setState($state);

        $this->executeOnExitOfTransition($transition);
    }

    public function getState(): string
    {
        return $this->subject->getState();
    }

    public function getSubject(): StatableInterface
    {
        return $this->subject;
    }

    private function setState(string $to): void
    {
        $this->subject->setState($to);
    }

    private function executeOnEntryOfTransition(string $transition): void
    {
        if (!empty($this->config['transitions'][$transition]['on']['entry'])) {
            foreach ($this->config['transitions'][$transition]['on']['entry'] as $action) {
                $this->executeAction($action, $transition);
            }
        }
    }

    private function executeOnExitOfTransition(string $transition): void
    {
        if (!empty($this->config['transitions'][$transition]['on']['exit'])) {
            foreach ($this->config['transitions'][$transition]['on']['exit'] as $action) {
                $this->executeAction($action, $transition);
            }
        }
    }

    /**
     * @param ActionableInterface|callable $action
     * @param string $transition
     * @throws TransitionActionFailedException
     */
    private function executeAction($action, string $transition): void
    {
        try {
            if (is_callable($action)) {
                $action($this->subject);

                return;
            }

            if (class_exists($action)) {
                $action = new $action();
            }

            if ($action instanceof ActionableInterface) {
                $action->execute($this, $transition);
                $this->repository->add($action);
            }
        } catch (Throwable $exception) {
            if ($action instanceof ActionableInterface) {
                $this->repository->add($action, $exception);
            }

            throw new TransitionActionFailedException($transition, $exception);
        }
    }
}
