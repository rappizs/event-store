<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use EventStore\EventStore;
use EventStore\Event;

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
    }

    public function test_can_push_event()
    {
        $eventStore = new EventStore();
        $stream = $eventStore->createStream('TestStream');
        $event = new Event('TestEvent', ['data' => 100], 1);
        $eventStore->push($stream->id, $event);
    }
}
