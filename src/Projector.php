<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventStore;

class Projector
{
    const STATUS_OK = 0;
    const STATUS_BROKEN = 1;
    const STATUS_STALLED = 2;
    const STATUS_READY = 3;

    const VERBOSE = 1;

    private $projection;
    private $position;
    private $status;
    private $state;
    private $eventStream;
    private $verbose;

    /**
     * Creates a projector for an event stream.
     *
     * @param Projection $projection
     * @param $state Initial state.
     * @param array $eventStream If empty array is provided then project for every stream.
     */
    public function __construct(Projection $projection, EventStream $eventStream, $state = null, int $position = 0, int $options = null) {
        $this->projection = $projection;
        $this->position = $position;
        $this->status = self::STATUS_READY;
        $this->state = $state;
        $this->eventStream = $eventStream;
        $this->verbose = $options & self::VERBOSE;
    }

    public function project(Event $e)
    {
        if ($this->verbose) {
            echo "Projecting ".$e->getType()."\n";
        }
        $this->eventStream->events[] = $e;
        $this->state = $this->projection->handle($this->state, $e);
        $this->position++;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getEventStream()
    {
        return $this->eventStream;
    }

    public function getPosition()
    {
        return $this->position;
    }
}
