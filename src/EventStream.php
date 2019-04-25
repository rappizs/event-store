<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventSourcing;

use Ramsey\Uuid\UuidInterface;

class EventStream
{
    private $id;
    private $type;
    private $version;
    private $createdAt;
    private $updatedAt;
    private $events;

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

    public function addEvent(EventInterface $event)
    {
        $this->events[] = $event;
    }

    public function addEventRange(array $events)
    {
        array_push($this->events, ...$events);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getEvents()
    {
        return $this->events;
    }
}
