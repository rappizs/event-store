<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventStore;

use Ramsey\Uuid\UuidInterface;

interface EventStream
{
    public function addEvent(Event $event): void;
    public function addEventRange(array $events): void;
    public function getId(): UuidInterface;
    public function getType(): string;
    public function getVersion(): int;
    public function getCreatedAt(): float;
    public function getUpdatedAt(): ?float;
    public function getEvents(): array;
    public function toJson(): string;
}
