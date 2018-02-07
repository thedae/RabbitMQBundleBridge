<?php

namespace SimpleBus\RabbitMQBundleBridge\Event;

use PhpAmqpLib\Message\AMQPMessage;

class MessageConsumptionFailed extends AbstractMessageEvent
{
    /**
     * @var \Throwable
     */
    private $exception;

    public function __construct(AMQPMessage $message, \Throwable $exception)
    {
        parent::__construct($message);

        $this->exception = $exception;
    }

    public function exception()
    {
        return $this->exception;
    }
}
