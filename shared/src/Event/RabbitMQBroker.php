<?php

declare(strict_types=1);

namespace HRPayroll\Shared\Event;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;

/**
 * RabbitMQ implementation of MessageBrokerInterface
 */
class RabbitMQBroker implements MessageBrokerInterface
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;
    private string $exchangeName;

    public function __construct(
        string $host,
        int $port,
        string $user,
        string $password,
        string $vhost = '/',
        string $exchangeName = 'hr_payroll_events'
    ) {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
        $this->channel = $this->connection->channel();
        $this->exchangeName = $exchangeName;

        // Declare topic exchange
        $this->channel->exchange_declare(
            $this->exchangeName,
            'topic',
            false,
            true,
            false
        );
    }

    public function publish(string $topic, array $message): void
    {
        $messageBody = json_encode($message, JSON_THROW_ON_ERROR);
        
        $amqpMessage = new AMQPMessage($messageBody, [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'timestamp' => time(),
            'message_id' => $message['eventId'] ?? uniqid('msg_', true)
        ]);

        $this->channel->basic_publish($amqpMessage, $this->exchangeName, $topic);
    }

    public function subscribe(string $topic, callable $handler): void
    {
        $queueName = 'queue_' . str_replace('.', '_', $topic);
        
        // Declare queue
        $this->channel->queue_declare($queueName, false, true, false, false);
        
        // Bind queue to exchange with routing key
        $this->channel->queue_bind($queueName, $this->exchangeName, $topic);

        $callback = function (AMQPMessage $msg) use ($handler) {
            $data = json_decode($msg->body, true, 512, JSON_THROW_ON_ERROR);
            $handler($data, $msg->getDeliveryTag());
        };

        $this->channel->basic_consume($queueName, '', false, false, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function acknowledge(string $messageId): void
    {
        $this->channel->basic_ack((int)$messageId);
    }

    public function reject(string $messageId, bool $requeue = true): void
    {
        $this->channel->basic_reject((int)$messageId, $requeue);
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}
