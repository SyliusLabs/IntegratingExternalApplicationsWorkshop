<?php

declare(strict_types=1);

namespace Tests\Amqp\Producer;

use AppBundle\Amqp\Producer\CustomerPlacedOrderProducer;
use AppBundle\Event\CustomerPlacedOrder;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Prophecy\Prophecy\ObjectProphecy;

final class CustomerPlacedOrderProducerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_publishes_customer_placed_order_event_into_queue()
    {
        $event = CustomerPlacedOrder::occur('rocketarminek@test.com', 'xyz', ['mug']);
        $message = [
            'type' => 'customer_placed_order',
            'payload' => [
                'customerEmail' => $event->customerEmail(),
                'orderToken' => $event->orderToken(),
                'productsCodes' => $event->productsCodes()
            ]
        ];

        $messageEncoded = json_encode($message);

        /** @var ObjectProphecy|ProducerInterface $baseProducer */
        $baseProducer = $this->prophesize(ProducerInterface::class);
        $baseProducer->publish($messageEncoded)->shouldBeCalled();

        $customerPlaceOrderProducer = new CustomerPlacedOrderProducer($baseProducer->reveal());

        $customerPlaceOrderProducer->handleCustomerPlacedOrder($event);
    }
}
