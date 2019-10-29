<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventStore\Storage;

abstract class PdoEventRepository implements IEventRepository
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->ensureTables();
    }

    abstract public function ensureTables(): void;
}
