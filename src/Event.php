<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventStore;

use Ramsey\Uuid\UuidInterface;

interface Event
{
    public function getId(): UuidInterface;
    public function getStreamId(): ?UuidInterface;
    public function setStreamId(UuidInterface $streamId): void;
    public function getType(): string;
    public function getPayload(): array;
    public function getVersion(): int;
    public function getOccuredAt(): float;
    public function getRecordedAt(): float;
    public function setRecordedAt(float $t): void;
    public function toJson(): string;
}