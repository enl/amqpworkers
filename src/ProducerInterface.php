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

    /**
     * Returns `true` if producer is properly configured. Throws exception otherwise.
     * Function is public just because Consumer needs to check if given producer configured before consuming the queue.
     *
     * @return bool
     * @throws ProducerNotProperlyConfigured
     */
    public function selfCheck();
}
