<?php

declare(strict_types=1);

namespace RecommendationEngine\Application\Projector;

use GraphAware\Neo4j\OGM\EntityManagerInterface;
use RecommendationEngine\Domain\Event\CustomerPlacedOrder;
use RecommendationEngine\Domain\Model\Customer;
use RecommendationEngine\Domain\Model\Order;
use RecommendationEngine\Domain\Model\Product;

final class CustomerPlacedOrderProjector
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param CustomerPlacedOrder $event
     */
    public function handleCustomerPlacedOrder(CustomerPlacedOrder $event): void
    {
        $customer = $this->provideCustomer($event->customerEmail());

        $order = new Order();
        $order->setToken($event->orderToken());

        foreach ($event->productsCodes() as $productCode) {
            $order->addProduct($this->provideProduct($productCode));
        }

        $customer->addOrder($order);

        $this->entityManager->persist($customer);
        $this->entityManager->flush();
    }

    /**
     * @param string $customerEmail
     *
     * @return Customer
     */
    private function provideCustomer(string $customerEmail): Customer
    {
        $customerRepository = $this->entityManager->getRepository(Customer::class);
        $customer = $customerRepository->findOneBy(['email' => $customerEmail]);
        if (null === $customer) {
            $customer = new Customer();
            $customer->setEmail($customerEmail);
        }

        return $customer;
    }

    /**
     * @param string $productCode
     *
     * @return Product
     */
    private function provideProduct(string $productCode): Product
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['code' => $productCode]);
        if (null === $product) {
            $product = new Product();
            $product->setCode($productCode);
        }

        return $product;
    }
}
