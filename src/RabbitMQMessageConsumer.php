<?php

namespace SimpleBus\RabbitMQBundleBridge;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer;
use SimpleBus\RabbitMQBundleBridge\Event\Events;
use SimpleBus\RabbitMQBundleBridge\Event\MessageConsumed;
use SimpleBus\RabbitMQBundleBridge\Event\MessageConsumptionFailed;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RabbitMQMessageConsumer implements ConsumerInterface
{
    /**
     * @var SerializedEnvelopeConsumer
     */
    private $consumer;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(SerializedEnvelopeConsumer $consumer, EventDispatcherInterface $eventDispatcher)
    {
        $this->consumer = $consumer;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute(AMQPMessage $msg)
    {
        try {
            $this->consumer->consume($msg->body);

            $this->eventDispatcher->dispatch(
                Events::MESSAGE_CONSUMED,
                new MessageConsumed($msg)
            );

            return self::MSG_ACK;
        } catch (\Throwable $exception) {
            $this->eventDispatcher->dispatch(
                Events::MESSAGE_CONSUMPTION_FAILED,
                new MessageConsumptionFailed($msg, $exception)
            );

            return self::MSG_REJECT;
        }
    }
}
