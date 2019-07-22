<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventStore\Storage;

use Ramsey\Uuid\UuidInterface;
use EventStore\EventStream;
use EventStore\Event;

abstract class EventRepository
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->ensureTables();
    }

    abstract public function getStream(UuidInterface $id): EventStream;
    abstract public function getEventsByStream(UuidInterface $id): array;
    abstract public function getStreamsByType(string $type): array;
    abstract public function getEvents(): array;
    abstract public function createStream($type): EventStream;
    abstract public function getVersionForStream($streamId): int;
    abstract public function push(Event $event);
    abstract public function incrementStream($streamId, $nextVersion);
    abstract public function ensureTables(): void;
}
