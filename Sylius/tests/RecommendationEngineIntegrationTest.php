<?php

declare(strict_types=1);

namespace Tests\AppBundle;

use PHPUnit\Framework\Assert;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariant;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class RecommendationEngineIntegrationTest extends KernelTestCase
{
    /** @var EventDispatcher */
    private $eventDispatcher;

    /** @var TraceableProducer */
    private $publisher;

    /** {@inheritdoc} */
    protected function setUp(): void
    {
        self::bootKernel();

        $this->eventDispatcher = static::$kernel->getContainer()->get('event_dispatcher');
        $this->publisher = static::$kernel->getContainer()->get('app.producer.sylius_workshop_croatia');
    }

    /** @test */
    public function it_creates_customer_placed_order_based_on_post_complete_order_resource_controller_event()
    {
        $this->publisher->startTracing();

        $this->eventDispatcher->dispatch('sylius.order.post_complete', new ResourceControllerEvent(
            $this->createOrderForCustomer(
                'rocketArminek@test.com',
                'orderToken',
                ['awesomeSyliusMug', 'awesomeSyliusT-shirt']
            )
        ));

        $this->publisher->stopTracing();

        Assert::assertSame([json_encode([
            'type' => 'customer_placed_order',
            'payload' => [
                'customerEmail' => 'rocketArminek@test.com',
                'orderToken' => 'orderToken',
                'productsCodes' => ['awesomeSyliusMug', 'awesomeSyliusT-shirt'],
            ],
        ])], $this->publisher->getProducedMessages());
    }

    /**
     * @param string $customerEmail
     * @param string $orderToken
     * @param array $productVariantCodes
     *
     * @return OrderInterface
     */
    private function createOrderForCustomer(string $customerEmail, string $orderToken, array $productVariantCodes): OrderInterface
    {
        $order = new Order();
        $order->setTokenValue($orderToken);
        $order->setLocaleCode('pl_PL');
        $order->setChannel(new Channel());

        $customer = new Customer();
        $customer->setEmail($customerEmail);
        $order->setCustomer($customer);

        foreach ($productVariantCodes as $productVariantCode) {
            $product = new Product();
            $product->setCode($productVariantCode);

            $productVariant = new ProductVariant();
            $productVariant->setCode($productVariantCode);
            $product->addVariant($productVariant);

            $orderItem = new OrderItem();
            $orderItem->setVariant($productVariant);
            $order->addItem($orderItem);
        }

        return $order;
    }
}
