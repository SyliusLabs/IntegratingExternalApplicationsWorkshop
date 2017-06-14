<?php

declare(strict_types=1);

namespace Tests\AppBundle;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

final class TraceableProducer implements ProducerInterface
{
    /** @var ProducerInterface */
    private $decoratedProducer;

    /** @var boolean */
    private $tracing = false;

    /** @var array */
    private $producedMessages = [];

    public function __construct(ProducerInterface $decoratedProducer)
    {
        $this->decoratedProducer = $decoratedProducer;
    }

    /** {@inheritdoc} */
    public function publish($msgBody, $routingKey = '', $additionalProperties = []): void
    {
        $this->decoratedProducer->publish($msgBody, $routingKey, $additionalProperties);

        if ($this->tracing) {
            $this->producedMessages[] = $msgBody;
        }
    }

    public function startTracing(): void
    {
        $this->tracing = true;
    }

    public function stopTracing(): void
    {
        $this->tracing = false;
    }

    public function getProducedMessages(): array
    {
        $producedMessages = $this->producedMessages;

        $this->producedMessages = [];

        return $producedMessages;
    }
}
