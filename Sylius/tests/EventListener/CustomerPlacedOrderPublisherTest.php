<?php

declare(strict_types=1);

namespace Tests\AppBundle\EventListener;

use AppBundle\Event\CustomerPlacedOrder;
use AppBundle\EventListener\CustomerPlacedOrderPublisher;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariant;
use Tests\AppBundle\Testing\TraceableMessageBusMiddleware;

final class CustomerPlacedOrderPublisherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TraceableMessageBusMiddleware
     */
    private $traceableMessageBusMiddleware;

    /**
     * @var CustomerPlacedOrderPublisher
     */
    private $publisher;

    public function setUp(): void
    {
        $this->traceableMessageBusMiddleware = new TraceableMessageBusMiddleware();
        $this->publisher = new CustomerPlacedOrderPublisher(
            new MessageBusSupportingMiddleware([$this->traceableMessageBusMiddleware])
        );
    }

    /**
     * @test
     */
    public function it_publishes_customer_placed_order_event_if_given_subject_is_order(): void
    {
        $resourceControllerEvent = new ResourceControllerEvent(
            $this->createOrderForCustomer(
                ['awesomeSyliusMug', 'awesomeSyliusT-shirt'],
                'rocketArminek@test.com',
                'orderToken'
            )
        );

        $this->publisher->publishCustomerPlacedOrder($resourceControllerEvent);

        $this->assertEquals([
            CustomerPlacedOrder::occur('rocketArminek@test.com', 'orderToken', ['awesomeSyliusMug', 'awesomeSyliusT-shirt'])
        ], $this->traceableMessageBusMiddleware->messages());
    }

    /**
     * @test
     */
    public function it_does_not_publish_placed_order_event_if_given_subject_is_not_order(): void
    {
        $resourceControllerEvent = new ResourceControllerEvent(
            new \stdClass()
        );

        $this->publisher->publishCustomerPlacedOrder($resourceControllerEvent);

        $this->assertEquals([], $this->traceableMessageBusMiddleware->messages());
    }

    /**
     * @param array $productVariantCodes
     * @param string $customerEmail
     * @param string $orderToken
     *
     * @return OrderInterface
     */
    private function createOrderForCustomer(array $productVariantCodes, string $customerEmail, string $orderToken): OrderInterface
    {
        $order = new Order();
        $order->setTokenValue($orderToken);

        $customer = new Customer();
        $customer->setEmail($customerEmail);

        foreach ($productVariantCodes as $productVariantCode) {
            $orderItem = new OrderItem();
            $productVariant = new ProductVariant();
            $productVariant->setCode($productVariantCode);
            $product = new Product();
            $product->setCode($productVariantCode);
            $productVariant->setProduct($product);
            $orderItem->setVariant($productVariant);

            $order->addItem($orderItem);
        }

        $order->setCustomer($customer);

        return $order;
    }
}
