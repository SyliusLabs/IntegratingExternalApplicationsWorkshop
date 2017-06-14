<?php

declare(strict_types=1);

namespace Tests\RecommendationEngine\Domain\Model;

use GraphAware\Neo4j\OGM\Common\Collection;
use PHPUnit\Framework\TestCase;
use RecommendationEngine\Domain\Model\Order;

final class OrderTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_products(): void
    {
        $products = new Collection([]);
        $order = new Order();
        $order->setProducts($products);

        $this->assertEquals($products, $order->getProducts());
    }

    /**
     * @test
     */
    public function it_has_token(): void
    {
        $order = new Order();
        $order->setToken('xyz');

        $this->assertEquals('xyz', $order->getToken());
    }
}
