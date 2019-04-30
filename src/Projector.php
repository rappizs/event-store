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

    private $projection;
    private $position;
    private $status;
    private $state;
    private $eventStream;

    /**
     * Creates a projector for an event stream.
     *
     * @param Projection $projection
     * @param array $state Initial state.
     * @param array $eventStream If empty array is provided then project for every stream.
     */
    public function __construct(Projection $projection, EventStream $eventStream, array $state = []) {
        $this->projection = $projection;
        $this->position = 0;
        $this->status = self::STATUS_READY;
        $this->state = $state;
        $this->eventStream = $eventStream;
    }

    public function project(Event $e)
    {
        $this->eventStream->addEvent($e);
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
