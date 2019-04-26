<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventStore;

use Ramsey\Uuid\UuidInterface;

interface EventInterface
{
    public function __construct(
        string        $type,
        array         $payload,
        int           $version,
        UuidInterface $id = null,
        UuidInterface $streamId = null,
        float         $occuredAt = null,
        float         $recordedAt = null
    );
    public function getId(): UuidInterface;
    public function getStreamId(): ?UuidInterface;
    public function setStreamId(UuidInterface $streamId): void;
    public function getType(): string;
    public function getPayload(): array;
    public function getVersion(): int;
    public function getOccuredAt(): float;
    public function getRecordedAt(): float;
    public function setRecordedAt(float $t): void;
}