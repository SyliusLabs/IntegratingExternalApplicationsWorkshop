<?php

declare(strict_types=1);

namespace Tests\AppBundle\Event;

use AppBundle\Event\CustomerPlacedOrder;

final class CustomerPlacedOrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_is_immutable_fact_that_customer_has_placed_order()
    {
        $event = CustomerPlacedOrder::occur('rocketarminek@test.com', 'xyz', ['product1']);

        $this->assertEquals('xyz', $event->orderToken());
        $this->assertEquals('rocketarminek@test.com', $event->customerEmail());
        $this->assertEquals(['product1'], $event->productsCodes());
    }
}
