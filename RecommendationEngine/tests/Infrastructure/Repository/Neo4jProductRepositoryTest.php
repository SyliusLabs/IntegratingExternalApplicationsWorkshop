<?php

declare(strict_types=1);

namespace Tests\RecommendationEngine\Infrastructure\Repository;

use PHPUnit\Framework\Assert;
use RecommendationEngine\Application\Repository\ProductRepository;
use RecommendationEngine\Domain\Event\CustomerPlacedOrder;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class Neo4jProductRepositoryTest extends KernelTestCase
{
    /** @var MessageBus */
    private $eventBus;

    /** @var ProductRepository */
    private $productRepository;

    /** @test */
    public function it_returns_recommended_products(): void
    {
        $this->eventBus->handle(new CustomerPlacedOrder('rocket@arminek.koluszki', 'order', ['product1', 'product2']));
        $this->eventBus->handle(new CustomerPlacedOrder('rocket@arminek.koluszki', 'order', ['product3', 'product1', 'product2']));
        $this->eventBus->handle(new CustomerPlacedOrder('rocket@arminek.koluszki', 'order', ['product1', 'product3']));
        $this->eventBus->handle(new CustomerPlacedOrder('rocket@arminek.koluszki', 'order', ['product1', 'product4']));
        $this->eventBus->handle(new CustomerPlacedOrder('rocket@arminek.koluszki', 'order', ['product3', 'product1']));
        $this->eventBus->handle(new CustomerPlacedOrder('rocket@arminek.koluszki', 'order', ['product1', 'product5']));
        $this->eventBus->handle(new CustomerPlacedOrder('rocket@arminek.koluszki', 'order', ['product3', 'product6']));

        Assert::assertSame(['product3', 'product2', 'product4'], $this->productRepository->getRecommendedProducts('product1', 3));
    }

    /** {@inheritdoc} */
    protected function setUp(): void
    {
        self::bootKernel();

        $this->eventBus = static::$kernel->getContainer()->get('event_bus');
        $this->productRepository = static::$kernel->getContainer()->get('recommendation_engine.application.repository.product');
    }

    /** @before */
    protected function purgeDatabase(): void
    {
        static::$kernel->getContainer()->get('neo4j.entity_manager.default')->getDatabaseDriver()->run('MATCH (n) DETACH DELETE n;');
    }
}
