<?php

declare(strict_types=1);

namespace AppBundle\Amqp\Producer;

use AppBundle\Event\CustomerPlacedOrder;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

final class CustomerPlacedOrderProducer
{
    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * @param ProducerInterface $producer
     */
    public function __construct(ProducerInterface $producer)
    {
        $this->producer = $producer;
    }

    /**
     * @param CustomerPlacedOrder $event
     */
    public function handleCustomerPlacedOrder(CustomerPlacedOrder $event)
    {
        $message = [
            'type' => 'customer_placed_order',
            'payload' => [
                'customerEmail' => $event->customerEmail(),
                'orderToken' => $event->orderToken(),
                'productsCodes' => $event->productsCodes()
            ]
        ];

        $this->producer->publish(json_encode($message));
    }
}
