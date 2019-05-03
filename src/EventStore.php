<?php
/**
 * @copyright 2019 Benjamin J. Ferge
 * @license LGPL
 */

declare(strict_types=1);

namespace EventStore;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class EventStore
{
    private $pdo;
    private $projectors = [];

    private const CREATE_TABLE_EVENTS = <<<SQL
CREATE TABLE IF NOT EXISTS events (
    id BLOB PRIMARY KEY,
    stream_id INTEGER,
    type TEXT,
    payload JSON,
    version INTEGER,
    occured_at REAL,
    recorded_at REAL)
SQL;

    private const CREATE_TABLE_SNAPSHOTS = <<<SQL
CREATE TABLE IF NOT EXISTS snapshots (
    id BLOB PRIMARY KEY,
    stream_id INTEGER,
    state JSON,
    version INTEGER)
SQL;

    private const CREATE_TABLE_STREAMS = <<<SQL
CREATE TABLE IF NOT EXISTS streams (
    id BLOB PRIMARY KEY,
    type TEXT,
    version INTEGER,
    created_at REAL,
    updated_at REAL)
SQL;

    public function __construct($filename = ":memory:")
    {
        $this->pdo = new \PDO("sqlite:" . $filename);
        $this->pdo->setAttribute(
            \PDO::ATTR_ERRMODE,
            \PDO::ERRMODE_EXCEPTION
        );
    }

    public function addProjector(Projector $projector)
    {
        $this->projectors[] = $projector;
    }

    public function createStream($type)
    {
        $ins_stream = <<<SQL
            INSERT INTO streams (id, type, version, created_at, updated_at)
            VALUES (:id, :type, :version, :created_at, :updated_at)
SQL;
        $stmt = $this->pdo->prepare($ins_stream);
        $version = 0;
        $createdAt = microtime(true);
        $streamId = Uuid::uuid4();
        $result = $stmt->execute([
            "id" => $streamId,
            "type" => $type,
            "version" => $version,
            "created_at" => $createdAt,
            "updated_at" => null
        ]);
        $stream = new EventStream($streamId, $type, $version, $createdAt);
        return $stream;
    }

    public function getStream(UuidInterface $id)
    {
        $qry = "SELECT * FROM streams WHERE id = ?";
        $stmt = $this->pdo->prepare($qry);
        $result = $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $this->loadStream($row);
    }
    
    public function getStreamsForType(string $type)
    {
        $qry = "SELECT * FROM streams WHERE type = ?";
        $stmt = $this->pdo->prepare($qry);
        $result = $stmt->execute([$type]);
        
        $streams = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC))
        {
            $streams[] = $this->loadStream($row);
        }
        return $streams;
    }

    private function loadStream($row)
    {
        $stream = new EventStream(
            Uuid::fromString($row["id"]),
            $row["type"],
            (int)$row["version"],
            (float)$row["created_at"],
            (float)$row["updated_at"]
        );
        
        $qry = "SELECT * FROM events WHERE stream_id = ?";
        $stmt = $this->pdo->prepare($qry);
        $result = $stmt->execute([$row["id"]]);

        if (!$result) {
        }

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $event = new Event(
                $row["type"],
                json_decode($row["payload"], true),
                (int)$row["version"],
                $row["id"],
                $stream->getId(),
                (float)$row["occured_at"],
                (float)$row["recorded_at"]
            );
            $stream->addEvent($event);
        }
        
        return $stream;
    }

    public function push(UuidInterface $streamId, Event $event)
    {
        $type = $event->getType();
        $payload = $event->getPayload();
        $expectedVersion = $event->getVersion() - 1;
        $select_version = <<<SQL
            SELECT version FROM streams
            WHERE id = :streamId
SQL;
        $stmt = $this->pdo->prepare($select_version);
        $stmt->execute(compact("streamId"));
        $streamVersion = (int)$stmt->fetchColumn();

        if ($streamVersion !== $expectedVersion) {
            throw new ConcurrencyException("StreamVersion ($streamVersion) is not equal with ExpectedVersion ($expectedVersion)");
        }

        $ins_event = <<<SQL
            INSERT INTO events (id, stream_id, type, payload, version, occured_at)
            VALUES (:eventId, :streamId, :type, :jsonPayload, :expectedVersion, :occuredAt)
SQL;

        $stmt = $this->pdo->prepare($ins_event);
        $occuredAt = $event->getOccuredAt();
        $event->setRecordedAt(microtime(true));
        $recordedAt = $event->getRecordedAt();
        $jsonPayload = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        $eventId = $event->getId();
        $stmt->execute(
            compact("eventId", "streamId", "type", "jsonPayload", "expectedVersion", "occuredAt")
        );

        $nextVersion = $streamVersion + 1;

        $update_stream = <<<SQL
            UPDATE streams SET version = :nextVersion
            WHERE id = :streamId
SQL;
        
        $stmt = $this->pdo->prepare($update_stream);
        $stmt->execute(compact("nextVersion", "streamId"));

        $this->publish($event);
        return $event;
    }

    private function publish(Event $e)
    {
        foreach ($this->projectors as $p) {
            $p->project($e);
        }
    }

    public function ensureTables()
    {
        try {
            $this->pdo->beginTransaction();
            $this->pdo->exec(self::CREATE_TABLE_EVENTS);
            $this->pdo->exec(self::CREATE_TABLE_STREAMS);
            $this->pdo->exec(self::CREATE_TABLE_SNAPSHOTS);
            $this->pdo->commit();
        } catch (\PDOException $ex) {
            $this->pdo->rollBack();
            throw $ex;
        }
    }
}
