<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use EventStore\SqliteEventStore;

final class SqliteEventStoreTest extends TestCase
{
    public function test_can_be_created_in_memory()
    {
        $eventStore = new SqliteEventStore();
        $this->assertInstanceOf(SqliteEventStore::class, $eventStore);
    }

    public function test_can_create_sqlite_file()
    {
        $filename = 'test.sqlite3';
        $eventStore = new SqliteEventStore($filename);
        $this->assertInstanceOf(SqliteEventStore::class, $eventStore);
        $this->assertFileExists($filename);
    }
}
