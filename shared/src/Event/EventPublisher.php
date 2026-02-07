<?php

declare(strict_types=1);

namespace HRPayroll\Shared\Event;

use Ramsey\Uuid\Uuid;

/**
 * Event Publisher for publishing domain events to message broker
 */
class EventPublisher
{
    private MessageBrokerInterface $broker;
    private string $serviceName;

    public function __construct(MessageBrokerInterface $broker, string $serviceName)
    {
        $this->broker = $broker;
        $this->serviceName = $serviceName;
    }

    /**
     * Publish an event to the message broker
     */
    public function publish(string $eventType, array $data, string $version = '1.0'): void
    {
        $event = [
            'eventId' => Uuid::uuid4()->toString(),
            'eventType' => $eventType,
            'version' => $version,
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::RFC3339),
            'source' => $this->serviceName,
            'data' => $data
        ];

        $this->broker->publish($eventType, $event);
    }

    /**
     * Publish multiple events in batch
     */
    public function publishBatch(array $events): void
    {
        foreach ($events as $event) {
            $this->publish(
                $event['eventType'],
                $event['data'],
                $event['version'] ?? '1.0'
            );
        }
    }
}
