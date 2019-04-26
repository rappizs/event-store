<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventStore;

interface ProjectionInterface
{
    public function handle(array $state, Event $e): array;
}