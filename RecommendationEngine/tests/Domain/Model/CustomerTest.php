<?php

declare(strict_types=1);

namespace Tests\RecommendationEngine\Domain\Model;

use GraphAware\Neo4j\OGM\Common\Collection;
use PHPUnit\Framework\TestCase;
use RecommendationEngine\Domain\Model\Customer;
use RecommendationEngine\Domain\Model\Order;

final class CustomerTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_email(): void
    {
        $customer = new Customer();
        $customer->setEmail('rocketarminek@test.com');

        $this->assertEquals('rocketarminek@test.com', $customer->getEmail());
    }

    /**
     * @test
     */
    public function it_has_orders(): void
    {
        $orders = new Collection([new Order()]);
        $customer = new Customer();
        $customer->setOrders($orders);

        $this->assertEquals($orders, $customer->getOrders());
    }
}
