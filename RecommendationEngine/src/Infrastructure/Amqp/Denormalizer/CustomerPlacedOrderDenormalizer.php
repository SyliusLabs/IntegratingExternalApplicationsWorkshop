<?php

declare(strict_types=1);

namespace RecommendationEngine\Infrastructure\Amqp\Denormalizer;

use PhpAmqpLib\Message\AMQPMessage;
use RecommendationEngine\Domain\Event\CustomerPlacedOrder;
use SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer\DenormalizationFailedException;
use SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer\DenormalizerInterface;

final class CustomerPlacedOrderDenormalizer implements DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(AMQPMessage $message): bool
    {
        try {
            $this->denormalize($message);

            return true;
        } catch (DenormalizationFailedException $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(AMQPMessage $message): CustomerPlacedOrder
    {
        $denormalizedMessage = json_decode($message->getBody(), true);
        $this->assertMessageBody($denormalizedMessage);

        return new CustomerPlacedOrder(
            $denormalizedMessage['payload']['customerEmail'],
            $denormalizedMessage['payload']['orderToken'],
            $denormalizedMessage['payload']['productsCodes']
        );
    }

    /**
     * @param array $denormalizedMessage
     *
     * @throws DenormalizationFailedException
     */
    private function assertMessageBody(array $denormalizedMessage): void
    {
        if (
            !(isset(
                $denormalizedMessage['type'],
                $denormalizedMessage['payload'],
                $denormalizedMessage['payload']['customerEmail'],
                $denormalizedMessage['payload']['orderToken'],
                $denormalizedMessage['payload']['productsCodes']
            ) &&
            'customer_placed_order' === $denormalizedMessage['type'])
        ) {
            throw new DenormalizationFailedException();
        }
    }
}
