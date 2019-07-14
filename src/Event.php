<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventStore;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Represents each changes of the application state.
 */
class Event
{
    public $id;
    public $streamId;
    public $type;
    public $payload;
    public $version;
    public $occuredAt;
    public $recordedAt;

    public function __construct(
        string        $type,
        array         $payload,
        int           $version,
        UuidInterface $id = null,
        UuidInterface $streamId = null,
        float         $occuredAt = null,
        float         $recordedAt = null
    ) {
        $this->type = $type;
        $this->payload = $payload;
        $this->version = $version;
        $this->id = $id ?? Uuid::uuid4();
        $this->streamId = $streamId;
        $this->occuredAt = $occuredAt ?? \microtime(true);
    }
}
