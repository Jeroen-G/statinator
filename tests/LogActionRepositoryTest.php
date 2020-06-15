<?php

declare(strict_types=1);

namespace JeroenG\Statinator\Tests;

use JeroenG\Statinator\Exception\TransitionActionFailedException;
use JeroenG\Statinator\Repository\LogActionRepository;
use JeroenG\Statinator\StateMachine;
use JeroenG\Statinator\Statinator;
use JeroenG\Statinator\Tests\Fixtures\DummyAction;
use JeroenG\Statinator\Tests\Fixtures\DummyActionThatFails;
use JeroenG\Statinator\Tests\Fixtures\DummyObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;
use RuntimeException;

class LogActionRepositoryTest extends TestCase
{
    public function test_it_can_store_a_successful_action(): void
    {
        $config = $this->getConfig();
        $action = new DummyAction();
        $logger = new TestLogger();
        $repository = new LogActionRepository($logger);
        $expected = [
            [
                'state' => 'INITIAL_STATE',
                'transition' => 'MOVE',
                'action' => DummyAction::class,
                'exception' => false,
            ]
        ];

        $sm = new StateMachine(new DummyObject('INITIAL_STATE'), $config, $repository);
        $action->execute($sm, 'MOVE');
        $repository->add($action);

        $this->assertTrue($logger->hasInfoThatContains('Action was execution with success'));
        $this->assertSame($expected, $repository->all());
    }

    public function test_it_can_store_a_failed_action(): void
    {
        $config = $this->getConfig();
        $action = new DummyActionThatFails();
        $logger = new TestLogger();
        $repository = new LogActionRepository($logger);
        $expected = [
            [
                'state' => 'INITIAL_STATE',
                'transition' => 'MOVE',
                'action' => DummyAction::class,
                'exception' => 'Exception thrown',
            ]
        ];

        $sm = new StateMachine(new DummyObject('INITIAL_STATE'), $config, $repository);

        $this->expectException(RuntimeException::class);
        $action->execute($sm, 'MOVE');
        $repository->add($action);

        $this->assertTrue($logger->hasInfoThatContains('Action was execution with failure'));
        $this->assertSame($expected, $repository->all());
    }

    public function test_a_state_machine_stores_an_action(): void
    {
        $config = $this->getConfig();
        $expected = [
            [
                'state' => 'INITIAL_STATE',
                'transition' => 'MOVE',
                'action' => DummyAction::class,
                'exception' => false,
            ], [
                'state' => 'INITIAL_STATE',
                'transition' => 'MOVE',
                'action' => DummyActionThatFails::class,
                'exception' => 'Exception thrown',
            ]
        ];

        $logger = new TestLogger();
        $repository = new LogActionRepository($logger);

        $statinator = new Statinator($config);
        $statinator->onEntry('MOVE', DummyAction::class);
        $statinator->onExit('MOVE', DummyActionThatFails::class);

        $dummy = new DummyObject('INITIAL_STATE');
        $sm = new StateMachine($dummy, $statinator->getConfig(), $repository);

        $this->expectException(TransitionActionFailedException::class);
        $sm->apply('MOVE');

        $this->assertTrue($dummy->triggered);
        $this->assertTrue($logger->hasInfoThatContains('Action was execution with success'));
        $this->assertSame($expected, $repository->all());
    }

    public function test_a_state_machine_does_not_store_a_callable_action(): void
    {
        $config = $this->getConfig();

        $logger = new TestLogger();
        $repository = new LogActionRepository($logger);

        $statinator = new Statinator($config);
        $statinator->onExit('MOVE', fn ($object) => $object->triggered = true);

        $dummy = new DummyObject('INITIAL_STATE');
        $sm = new StateMachine($dummy, $statinator->getConfig(), $repository);

        $sm->apply('MOVE');

        $this->assertTrue($dummy->triggered);
        $this->assertFalse($logger->hasInfoThatContains('Action was execution with success'));
        $this->assertSame([], $repository->all());
    }

    private function getConfig(): array
    {
        return [
            'states' => ['INITIAL_STATE', 'NEXT_STATE'],
            'transitions' => [
                'MOVE' => [
                    'from' => ['INITIAL_STATE'],
                    'to' => ['NEXT_STATE'],
                ],
            ],
        ];
    }
}
