<?php


namespace AmqpWorkers;


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
