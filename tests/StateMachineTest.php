<?php

declare(strict_types=1);

namespace JeroenG\Statinator\Tests;

use JeroenG\Statinator\Exception\TransitionActionFailedException;
use JeroenG\Statinator\Exception\TransitionNotAllowedException;
use JeroenG\Statinator\Statinator;
use JeroenG\Statinator\Tests\Fixtures\DummyObject;
use PHPUnit\Framework\TestCase;

class StateMachineTest extends TestCase
{
    public function test_an_initial_state_may_be_transitioned_to_the_next_state(): void
    {
        $statinator = new Statinator($this->getConfig());

        $object = new DummyObject('INITIAL_STATE');

        $sm = $statinator->get($object);

        $this->assertTrue($sm->can('MOVE'));

        $sm->apply('MOVE');

        $this->assertSame('NEXT_STATE', $sm->getState());
    }

    public function test_an_error_is_triggered_when_the_transition_is_not_allowed(): void
    {
        $statinator = new Statinator();

        $statinator->setStates([
            'INITIAL_STATE',
            'NEXT_STATE',
            'ANOTHER_STATE'
        ]);

        $statinator->setTransitions([
            'MOVE' => [
                'from' => ['INITIAL_STATE'],
                'to' => ['NEXT_STATE'],
            ]
        ]);

        $object = new DummyObject('ANOTHER_STATE');

        $sm = $statinator->get($object);

        $this->assertFalse($sm->can('MOVE'));

        $this->expectException(TransitionNotAllowedException::class);

        $sm->apply('MOVE');
    }

    public function test_a_transition_can_execute_actions_on_entry(): void
    {
        $statinator = new Statinator($this->getConfig());
        $statinator->onEntry('MOVE', fn ($object) => $object->triggered = true);

        $dummy = new DummyObject('INITIAL_STATE');

        $this->assertFalse($dummy->triggered);

        $sm = $statinator->get($dummy);

        $sm->apply('MOVE');

        $this->assertTrue($dummy->triggered);
        $this->assertSame('NEXT_STATE', $sm->getState());
    }

    public function test_a_transition_can_execute_actions_on_exit(): void
    {
        $statinator = new Statinator($this->getConfig());
        $statinator->onExit('MOVE', fn ($object) => $object->triggered = true);

        $dummy = new DummyObject('INITIAL_STATE');

        $this->assertFalse($dummy->triggered);

        $sm = $statinator->get($dummy);

        $sm->apply('MOVE');

        $this->assertTrue($dummy->triggered);
        $this->assertSame('NEXT_STATE', $sm->getState());
    }

    public function test_a_failing_entry_does_not_change_the_state(): void
    {
        $statinator = new Statinator($this->getConfig());
        $statinator->onEntry('MOVE', fn ($object) => $object->throwException());

        $dummy = new DummyObject('INITIAL_STATE');

        $this->assertFalse($dummy->triggered);

        $sm = $statinator->get($dummy);

        $this->expectException(TransitionActionFailedException::class);
        $sm->apply('MOVE');

        $this->assertFalse($dummy->triggered);
        $this->assertSame('INITIAL_STATE', $sm->getState());
    }

    public function test_a_failing_exit_does_not_change_the_state(): void
    {
        $statinator = new Statinator($this->getConfig());
        $statinator->onExit('MOVE', fn ($object) => $object->throwException());

        $dummy = new DummyObject('INITIAL_STATE');

        $this->assertFalse($dummy->triggered);

        $sm = $statinator->get($dummy);

        $this->expectException(TransitionActionFailedException::class);
        $sm->apply('MOVE');

        $this->assertFalse($dummy->triggered);
        $this->assertSame('INITIAL_STATE', $sm->getState());
    }
    private function getConfig(): array
    {
        return [
            'states' => [
                'INITIAL_STATE',
                'NEXT_STATE',
                'ANOTHER_STATE',
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
