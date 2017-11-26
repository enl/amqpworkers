<?php

namespace AmqpWorkers;

use AmqpWorkers\Exception\ProducerNotProperlyConfigured;

interface ProducerInterface
{
    /**
     * @param mixed $payload
     * @todo: maybe add properties array as second parameter
     * @throws ProducerNotProperlyConfigured if queue nor exchange not given.
     */
    public function produce($payload);
}
