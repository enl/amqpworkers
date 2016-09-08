<?php

namespace AmqpWorkers;

/**
 * BatchProducer treats given message as a list of messages and send them to RabbitMQ channel
 *
 * @package AmqpWorkers
 * @author Alex Panshin <deadyaga@gmail.com>
 * @since 1.0
 */
class BatchProducer extends Producer
{
    /**
     * @param array|\Traversable $messages
     * @throws \AmqpWorkers\Exception\ProducerNotProperlyConfigured
     */
    public function produce($messages)
    {
        $channel = $this->getChannel();

        foreach ($messages as $payload) {
            if ($this->isExchange()) {
                $channel->batch_basic_publish($this->createMessage($payload), $this->exchange->getName());
            } else {
                $channel->batch_basic_publish($this->createMessage($payload), '', $this->queue->getName());
            }
        }

        $channel->publish_batch();
    }
}
