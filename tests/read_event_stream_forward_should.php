<?php

/**
 * This file is part of `prooph/event-store-client`.
 * (c) 2018-2021 Alexander Miertsch <kontakt@codeliner.ws>
 * (c) 2018-2021 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\EventStoreClient;

use Generator;
use Prooph\EventStore\Exception\InvalidArgumentException;
use Prooph\EventStore\ExpectedVersion;
use Prooph\EventStore\RecordedEvent;
use Prooph\EventStore\ResolvedEvent;
use Prooph\EventStore\SliceReadStatus;
use Prooph\EventStore\StreamEventsSlice;
use Prooph\EventStore\StreamPosition;
use ProophTest\EventStoreClient\Helper\EventDataComparer;
use ProophTest\EventStoreClient\Helper\TestEvent;

class read_event_stream_forward_should extends EventStoreConnectionTestCase
{
    /** @test */
    public function throw_if_count_le_zero(): Generator
    {
        $stream = 'read_event_stream_forward_should_throw_if_count_le_zero';

        $this->expectException(InvalidArgumentException::class);

        $this->connection->readStreamEventsForwardAsync(
            $stream,
            0,
            0,
            false
        );
    }

    /** @test */
    public function throw_if_start_lt_zero(): Generator
    {
        $stream = 'read_event_stream_forward_should_throw_if_start_lt_zero';

        $this->expectException(InvalidArgumentException::class);

        $this->connection->readStreamEventsForwardAsync(
            $stream,
            -1,
            1,
            false
        );
    }

    /** @test */
    public function notify_using_status_code_if_stream_not_found(): Generator
    {
        $stream = 'read_event_stream_forward_should_notify_using_status_code_if_stream_not_found';

        $read = yield $this->connection->readStreamEventsForwardAsync(
            $stream,
            StreamPosition::START,
            1,
            false
        );
        \assert($read instanceof StreamEventsSlice);

        $this->assertTrue(SliceReadStatus::streamNotFound()->equals($read->status()));
    }

    /** @test */
    public function notify_using_status_code_if_stream_was_deleted(): Generator
    {
        $stream = 'read_event_stream_forward_should_notify_using_status_code_if_stream_was_deleted';

        yield $this->connection->deleteStreamAsync($stream, ExpectedVersion::NO_STREAM, true);

        $read = yield $this->connection->readStreamEventsForwardAsync(
            $stream,
            StreamPosition::START,
            1,
            false
        );
        \assert($read instanceof StreamEventsSlice);

        $this->assertTrue(SliceReadStatus::streamDeleted()->equals($read->status()));
    }

    /** @test */
    public function return_no_events_when_called_on_empty_stream(): Generator
    {
        $stream = 'read_event_stream_forward_should_return_single_event_when_called_on_empty_stream';

        $read = yield $this->connection->readStreamEventsForwardAsync(
            $stream,
            StreamPosition::START,
            1,
            false
        );
        \assert($read instanceof StreamEventsSlice);

        $this->assertCount(0, $read->events());
    }

    /** @test */
    public function return_empty_slice_when_called_on_non_existing_range(): Generator
    {
        $stream = 'read_event_stream_forward_should_return_empty_slice_when_called_on_non_existing_range';

        $testEvents = TestEvent::newAmount(10);
        yield $this->connection->appendToStreamAsync(
            $stream,
            ExpectedVersion::NO_STREAM,
            $testEvents
        );

        $read = yield $this->connection->readStreamEventsForwardAsync(
            $stream,
            11,
            5,
            false
        );
        \assert($read instanceof StreamEventsSlice);

        $this->assertCount(0, $read->events());
    }

    /** @test */
    public function return_partial_slice_if_no_enough_events_in_stream(): Generator
    {
        $stream = 'read_event_stream_forward_should_return_partial_slice_if_no_enough_events_in_stream';

        $testEvents = TestEvent::newAmount(10);
        yield $this->connection->appendToStreamAsync(
            $stream,
            ExpectedVersion::NO_STREAM,
            $testEvents
        );

        $read = yield $this->connection->readStreamEventsForwardAsync(
            $stream,
            9,
            5,
            false
        );
        \assert($read instanceof StreamEventsSlice);

        $this->assertCount(1, $read->events());
    }

    /** @test */
    public function throw_when_got_int_max_value_as_max_count(): Generator
    {
        $this->expectException(InvalidArgumentException::class);

        $this->connection->readStreamEventsForwardAsync(
            'foo',
            StreamPosition::START,
            \PHP_INT_MAX,
            false
        );
    }

    /** @test */
    public function return_events_in_same_order_as_written(): Generator
    {
        $stream = 'read_event_stream_forward_should_return_events_in_same_order_as_written';

        $testEvents = TestEvent::newAmount(10);
        yield $this->connection->appendToStreamAsync(
            $stream,
            ExpectedVersion::NO_STREAM,
            $testEvents
        );

        $read = yield $this->connection->readStreamEventsForwardAsync(
            $stream,
            StreamPosition::START,
            10,
            false
        );
        \assert($read instanceof StreamEventsSlice);

        $events = \array_map(
            fn (ResolvedEvent $e): RecordedEvent => $e->event(),
            $read->events()
        );

        $this->assertTrue(EventDataComparer::allEqual($testEvents, $events));
    }

    /** @test */
    public function be_able_to_read_slice_from_arbitrary_position(): Generator
    {
        $stream = 'read_event_stream_forward_should_be_able_to_read_slice_from_arbitrary_position';

        $testEvents = TestEvent::newAmount(10);
        yield $this->connection->appendToStreamAsync(
            $stream,
            ExpectedVersion::NO_STREAM,
            $testEvents
        );

        $read = yield $this->connection->readStreamEventsForwardAsync(
            $stream,
            5,
            2,
            false
        );
        \assert($read instanceof StreamEventsSlice);

        $events = \array_map(
            fn (ResolvedEvent $e): RecordedEvent => $e->event(),
            $read->events()
        );

        $this->assertTrue(EventDataComparer::allEqual(
            \array_slice($testEvents, 5, 2),
            $events
        ));
    }
}
