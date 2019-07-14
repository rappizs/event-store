<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventStore;

use Ramsey\Uuid\UuidInterface;
use EventStore\Storage\SqliteEventRepository;

class EventStore
{
    private $projectors = [];
    private $repo;

    public function __construct($filename = ":memory:")
    {
        $pdo = new \PDO("sqlite:" . $filename);
        $pdo->setAttribute(
            \PDO::ATTR_ERRMODE,
            \PDO::ERRMODE_EXCEPTION
        );

        $repo = new SqliteEventRepository($pdo);
        $this->repo = $repo;
    }

    public function createStream($type): EventStream
    {
        return $this->repo->createStream($type);
    }

    public function getStream(UuidInterface $id): EventStream
    {
        return $this->repo->getStream($id);
    }
    
    public function getStreamsForType(string $type): array
    {
        return $this->repo->getStreamsForType($type);
    }

    public function push(UuidInterface $streamId, Event $event): Event
    {
        $event->streamId = $streamId;
        $expectedVersion = $event->version - 1;
        $streamVersion = $this->repo->getVersionForStream($streamId);

        if ($streamVersion !== $expectedVersion) {
            throw new ConcurrencyException("StreamVersion ($streamVersion) is not equal with ExpectedVersion ($expectedVersion)");
        }

        $event = $this->repo->push($event);

        $nextVersion = $streamVersion + 1;

        $this->repo->incrementStream($streamId, $nextVersion);
        $this->publish($event);
        return $event;
    }

    public function addProjector(Projector $projector): void
    {
        $this->projectors[] = $projector;
    }

    private function publish(Event $e): void
    {
        foreach ($this->projectors as $p) {
            $p->project($e);
        }
    }
}
