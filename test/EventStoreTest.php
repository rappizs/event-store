<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use EventStore\EventStore;
use EventStore\Event;
use Ramsey\Uuid\UuidInterface;
use EventStore\ConcurrencyException;

final class EventStoreTest extends TestCase
{
    public function test_can_be_created_in_memory()
    {
        $eventStore = new EventStore();
        $this->assertInstanceOf(EventStore::class, $eventStore);
    }

    public function test_can_create_sqlite_file()
    {
        $filename = 'test.sqlite3';
        $eventStore = new EventStore($filename);
        $this->assertInstanceOf(EventStore::class, $eventStore);
        $this->assertFileExists($filename);
        unlink($filename);
    }

    public function test_can_push_event()
    {
        $eventStore = new EventStore();
        $stream = $eventStore->createStream('TestStream');
        $payload = ['data' => 100];
        $type = 'TestEvent';
        $version = 1;
        $event = new Event($type, $payload, $version);
        $_event = $eventStore->push($stream->id, clone $event);
        
        $this->assertInstanceOf(Event::class, $event);
        $this->assertEmpty($event->recordedAt);
        $this->assertEmpty($event->streamId);
        $this->assertInstanceOf(UuidInterface::class, $event->id);
        $this->assertIsFloat($event->occuredAt);
        $this->assertSame($payload, $event->payload);
        $this->assertSame($version, $event->version);
        $this->assertSame($type, $event->type);

        $this->assertInstanceOf(Event::class, $_event);
        $this->assertIsFloat($_event->recordedAt);
        $this->assertInstanceOf(UuidInterface::class, $_event->streamId);
        $this->assertInstanceOf(UuidInterface::class, $_event->id);
        $this->assertIsFloat($_event->occuredAt);
        $this->assertSame($payload, $_event->payload);
        $this->assertSame($version, $_event->version);
        $this->assertSame($type, $_event->type);
    }

    public function test_concurrency_exception()
    {
        $this->expectException(ConcurrencyException::class);
        $eventStore = new EventStore();
        $stream = $eventStore->createStream('TestStream');
        $payload = ['data' => 100];
        $type = 'TestEvent';
        $event = new Event($type, $payload, 1);
        $eventStore->push($stream->id, $event);
        $eventStore->push($stream->id, $event);
    }

    public function test_can_not_push_event_with_wrong_version()
    {
        $this->expectException(ConcurrencyException::class);
        $eventStore = new EventStore();
        $stream = $eventStore->createStream('TestStream');
        $payload = ['data' => 100];
        $type = 'TestEvent';
        $event = new Event($type, $payload, -20);
        $eventStore->push($stream->id, $event);
    }

    public function test_can_get_streams_by_type()
    {
        $eventStore = new EventStore();
        $type = 'User';
        $testStream = $eventStore->createStream($type);
        $streams = $eventStore->getStreamsByType($type);
        $this->assertEquals($testStream->createdAt, $streams[0]->createdAt);
    }
}
