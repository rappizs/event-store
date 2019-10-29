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

interface IEventRepository
{
    public function getStream(UuidInterface $id): EventStream;
    public function getStreams(): array;
    public function getEventsByStream(UuidInterface $id): array;
    public function getStreamsByType(string $type): array;
    public function getEvents(): array;
    public function createStream($type): EventStream;
    public function getVersionForStream($streamId): int;
    public function push(Event $event);
    public function incrementStream($streamId, $nextVersion);
}
