<?php

declare(strict_types=1);

namespace JeroenG\Statinator\Tests;

use JeroenG\Statinator\Statinator;
use JeroenG\Statinator\Tests\Fixtures\DummyAction;
use JeroenG\Statinator\Tests\Fixtures\DummyObject;
use PHPUnit\Framework\TestCase;

class StatinatorTest extends TestCase
{
    public function test_it_accepts_configuration(): void
    {
        $config = $this->getConfig();

        $statinator = new Statinator($config);

        $this->assertSame($config['states'], $statinator->getStates());
        $this->assertSame($config['transitions'], $statinator->getTransitions());
    }

    public function test_it_accepts_setters(): void
    {
        $config = $this->getConfig();

        $statinator = new Statinator();

        $statinator->setStates([
            'INITIAL_STATE',
            'NEXT_STATE'
        ]);

        $statinator->setTransitions([
            'MOVE' => [
                'from' => ['INITIAL_STATE'],
                'to' => ['NEXT_STATE'],
            ]
        ]);

        $this->assertSame($config['states'], $statinator->getStates());
        $this->assertSame($config['transitions'], $statinator->getTransitions());
    }

    public function test_it_gets_the_state_machine_for_an_object(): void
    {
        $config = $this->getConfig();

        $statinator = new Statinator($config);

        $dummy = new DummyObject('INITIAL_STATE');

        $sm = $statinator->get($dummy);

        $this->assertSame('INITIAL_STATE', $sm->getState());
    }

    public function test_it_allows_actions_to_be_defined_for_transitions(): void
    {
        $config = $this->getConfig();

        $statinator = new Statinator($config);

        $statinator
            ->onEntry('MOVE', fn ($a) => $a++)
            ->onExit('MOVE', fn ($a) => $a++);

        $actions = $statinator->getActionsForTransition('MOVE');

        $this->assertArrayHasKey('entry', $actions);
        $this->assertArrayHasKey('exit', $actions);
        $this->assertIsCallable($actions['entry'][0]);
        $this->assertIsCallable($actions['exit'][0]);
    }

    public function test_configured_actionable_is_instantiated(): void
    {
        $config = $this->getConfig();

        $statinator = new Statinator($config);

        $statinator->onEntry('MOVE', DummyAction::class);

        $dummy = new DummyObject('INITIAL_STATE');

        $sm = $statinator->get($dummy);

        $sm->apply('MOVE');

        $this->assertTrue($dummy->triggered);
    }
    private function getConfig(): array
    {
        return [
            'states' => [
                'INITIAL_STATE',
                'NEXT_STATE'
            ],
            'transitions' => [
                'MOVE' => [
                    'from' => ['INITIAL_STATE'],
                    'to' => ['NEXT_STATE'],
                ],
            ],
        ];
    }
}
