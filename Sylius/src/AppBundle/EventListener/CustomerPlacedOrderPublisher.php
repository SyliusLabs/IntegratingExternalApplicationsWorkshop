<?php

declare(strict_types=1);

namespace AppBundle\EventListener;

use AppBundle\Event\CustomerPlacedOrder;
use Doctrine\Common\Collections\Collection;
use SimpleBus\Message\Bus\MessageBus;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class CustomerPlacedOrderPublisher
{
    /**
     * @var MessageBus
     */
    public $eventBus;

    /**
     * @param MessageBus $eventBus
     */
    public function __construct(MessageBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function publishCustomerPlacedOrder(ResourceControllerEvent $event): void
    {
        $order = $event->getSubject();

        if ($order instanceof OrderInterface) {
            $this->eventBus->handle(
                CustomerPlacedOrder::occur(
                    $order->getCustomer()->getEmail(),
                    $order->getTokenValue(),
                    $this->getProductsCodesFromOrderItems($order->getItems())
                )
            );
        }
    }

    /**
     * @param Collection|OrderItemInterface[] $orderItems
     *
     * @return array
     */
    private function getProductsCodesFromOrderItems(Collection $orderItems): array
    {
        $productVariantCodes = $orderItems->map(function (OrderItemInterface $orderItem) {
            return $orderItem->getProduct()->getCode();
        });

        return $productVariantCodes->toArray();
    }
}
