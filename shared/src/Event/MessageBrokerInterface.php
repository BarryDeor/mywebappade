<?php

declare(strict_types=1);

namespace HRPayroll\Shared\Event;

/**
 * Interface for message broker implementations (RabbitMQ, Kafka, etc.)
 */
interface MessageBrokerInterface
{
    /**
     * Publish a message to a topic/exchange
     */
    public function publish(string $topic, array $message): void;

    /**
     * Subscribe to a topic/exchange
     */
    public function subscribe(string $topic, callable $handler): void;

    /**
     * Acknowledge message processing
     */
    public function acknowledge(string $messageId): void;

    /**
     * Reject and requeue a message
     */
    public function reject(string $messageId, bool $requeue = true): void;

    /**
     * Close connection
     */
    public function close(): void;
}
