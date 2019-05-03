<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventStore;

use Ramsey\Uuid\UuidInterface;

class DomainEventStream implements EventStream
{
    private $id;
    private $type;
    private $version;
    private $createdAt;
    private $updatedAt;
    private $events;
    private $projectors = [];

    public function __construct(
        UuidInterface $id,
        string $type,
        int $version,
        float $createdAt
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->version = $version;
        $this->createdAt = $createdAt;
        $this->updatedAt = null;
        $this->events = [];
    }

    public function addEvent(EventInterface $event): void
    {
        $this->events[] = $event;
    }

    public function addEventRange(array $events): void
    {
        array_push($this->events, ...$events);
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getCreatedAt(): float
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): float
    {
        return $this->updatedAt;
    }

    public function getEvents(): array
    {
        return $this->events;
    }
}
