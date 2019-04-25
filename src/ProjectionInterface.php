<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventSourcing;

interface ProjectionInterface
{
    public function handle(array $state, Event $e): array;
}