<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventStore;

use Ramsey\Uuid\UuidInterface;

class EventStream
{
    public $id;
    public $type;
    public $version;
    public $createdAt;
    public $updatedAt;
    public $events;
    public $projectors = [];

    public function __construct(
        UuidInterface $id,
        string $type,
        int $version,
        float $createdAt,
        array $events = []
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->version = $version;
        $this->createdAt = $createdAt;
        $this->updatedAt = null;
        $this->events = [];
    }
}
