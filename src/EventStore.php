<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventStore;

use Ramsey\Uuid\UuidInterface;

interface EventStore
{
    public function createStream($type): EventStream;
    public function getStream(UuidInterface $id): EventStream;
    public function getStreamsForType(string $type): array;
    public function push(UuidInterface $streamId, Event $event): Event;
    public function addProjector(Projector $projector): void;
    private function publish(Event $e): void;
}
