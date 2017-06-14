<?php

declare(strict_types=1);

namespace Tests\RecommendationEngine\Application\Projector;

use GraphAware\Neo4j\OGM\EntityManagerInterface;
use RecommendationEngine\Application\Projector\CustomerPlacedOrderProjector;
use RecommendationEngine\Domain\Event\CustomerPlacedOrder;
use RecommendationEngine\Domain\Model\Customer;
use RecommendationEngine\Domain\Model\Order;
use RecommendationEngine\Domain\Model\Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CustomerPlacedOrderProjectorTest extends KernelTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CustomerPlacedOrderProjector
     */
    private $customerPlacedOrderProjector;

    /**
     * @test
     */
    public function it_create_projection_of_customer_which_has_placed_order(): void
    {
        $this->customerPlacedOrderProjector->handleCustomerPlacedOrder(new CustomerPlacedOrder(
            'rocketarminek@test.com',
            'xyz',
            ['awesome_sylius_mug']
        ));

        $productRepository = $this->entityManager->getRepository(Product::class);
        $customerRepository = $this->entityManager->getRepository(Customer::class);
        $orderRepository = $this->entityManager->getRepository(Order::class);

        $product = $productRepository->findOneBy(['code' => 'awesome_sylius_mug']);
        $customer = $customerRepository->findOneBy(['email' => 'rocketarminek@test.com']);
        $order = $orderRepository->findOneBy(['token' => 'xyz']);

        $this->assertEquals('awesome_sylius_mug', $product->getCode());
        $this->assertEquals('rocketarminek@test.com', $customer->getEmail());
        $this->assertEquals('xyz', $order->getToken());
    }

    /**
     * @test
     */
    public function it_updates_customer_history_when_he_placed_another_order(): void
    {
        $this->customerPlacedOrderProjector->handleCustomerPlacedOrder(new CustomerPlacedOrder(
            'rocketarminek@test.com',
            'xyz',
            ['awesome_sylius_mug']
        ));

        $this->customerPlacedOrderProjector->handleCustomerPlacedOrder(new CustomerPlacedOrder(
            'rocketarminek@test.com',
            'abcd',
            ['awesome_sylius_mug']
        ));

        $productRepository = $this->entityManager->getRepository(Product::class);
        $customerRepository = $this->entityManager->getRepository(Customer::class);
        $orderRepository = $this->entityManager->getRepository(Order::class);

        $product = $productRepository->findOneBy(['code' => 'awesome_sylius_mug']);
        $customer = $customerRepository->findOneBy(['email' => 'rocketarminek@test.com']);
        $firstOrder = $orderRepository->findOneBy(['token' => 'xyz']);
        $secondOrder = $orderRepository->findOneBy(['token' => 'abcd']);

        $this->assertEquals('awesome_sylius_mug', $product->getCode());
        $this->assertEquals('rocketarminek@test.com', $customer->getEmail());
        $this->assertEquals('xyz', $firstOrder->getToken());
        $this->assertEquals('abcd', $secondOrder->getToken());
    }

    /**
     * @test
     */
    public function it_updates_customer_history_when_he_placed_another_order_with_different_product(): void
    {
        $this->customerPlacedOrderProjector->handleCustomerPlacedOrder(new CustomerPlacedOrder(
            'rocketarminek@test.com',
            'xyz',
            ['awesome_sylius_mug']
        ));

        $this->customerPlacedOrderProjector->handleCustomerPlacedOrder(new CustomerPlacedOrder(
            'rocketarminek@test.com',
            'abcd',
            ['awesome_sylius_t_shirt']
        ));

        $this->customerPlacedOrderProjector->handleCustomerPlacedOrder(new CustomerPlacedOrder(
            'rocketarminek@test.com',
            'bcde',
            ['awesome_sylius_mug', 'awesome_sylius_t_shirt', 'awesome_sylius_pug']
        ));

        $productRepository = $this->entityManager->getRepository(Product::class);
        $customerRepository = $this->entityManager->getRepository(Customer::class);
        $orderRepository = $this->entityManager->getRepository(Order::class);

        $firstProduct = $productRepository->findOneBy(['code' => 'awesome_sylius_mug']);
        $secondProduct = $productRepository->findOneBy(['code' => 'awesome_sylius_t_shirt']);
        $thirdProduct = $productRepository->findOneBy(['code' => 'awesome_sylius_pug']);
        $customer = $customerRepository->findOneBy(['email' => 'rocketarminek@test.com']);
        $firstOrder = $orderRepository->findOneBy(['token' => 'xyz']);
        $secondOrder = $orderRepository->findOneBy(['token' => 'abcd']);
        $thirdOrder = $orderRepository->findOneBy(['token' => 'bcde']);

        $this->assertEquals('awesome_sylius_mug', $firstProduct->getCode());
        $this->assertEquals('awesome_sylius_t_shirt', $secondProduct->getCode());
        $this->assertEquals('awesome_sylius_pug', $thirdProduct->getCode());
        $this->assertEquals('rocketarminek@test.com', $customer->getEmail());
        $this->assertEquals('xyz', $firstOrder->getToken());
        $this->assertEquals('abcd', $secondOrder->getToken());
        $this->assertEquals('bcde', $thirdOrder->getToken());
    }

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = static::$kernel->getContainer()->get('neo4j.entity_manager.default');
        $this->customerPlacedOrderProjector = static::$kernel->getContainer()->get('recommendation_engine.application.projector.customer_placed_order');
    }

    /**
     * @before
     */
    protected function purgeDatabase(): void
    {
        $this->entityManager->getDatabaseDriver()->run('MATCH (n) DETACH DELETE n;');
    }
}
