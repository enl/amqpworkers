<?php

namespace AmqpWorkers;

use PhpAmqpLib\Connection\AbstractConnection;

/**
 * BatchProducer treats given message as a list of messages and send them to RabbitMQ channel
 *
 * @package AmqpWorkers
 * @author Alex Panshin <deadyaga@gmail.com>
 * @since 1.0
 */
class BatchProducer extends Producer
{
    private $limit = 0;
    private $queued = 0;

    public static function factory(AbstractConnection $connection, $limit = 100)
    {
        /** @var BatchProducer $producer */
        $producer = parent::factory($connection);
        $producer->limit = $limit;
    }

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

            $this->queued++;

            if ($this->limit && $this->limit > $this->queued) {
                $this->flush();
            }
        }

        $this->flush();
    }

    protected function flush()
    {
        $this->getChannel()->publish_batch();
        $this->queued = 0;
    }
}
