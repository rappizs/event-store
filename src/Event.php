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
class Event implements EventInterface
{
    private $id;
    private $streamId;
    private $type;
    private $payload;
    private $version;
    private $occuredAt;
    private $recordedAt;

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

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getStreamId(): ?UuidInterface
    {
        return $this->streamId;
    }

    public function setStreamId(UuidInterface $streamId): void
    {
        $this->streamId = $streamId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getOccuredAt(): float
    {
        return $this->occuredAt;
    }

    public function getRecordedAt(): float
    {
        return $this->recordedAt;
    }

    public function setRecordedAt($recordedAt): void
    {
        $this->recordedAt = $recordedAt;
    }
}
