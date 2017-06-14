<?php

declare(strict_types=1);

namespace RecommendationEngine\Domain\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * @OGM\Node(label="Customer")
 */
class Customer
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    private $id;

    /**
     * @var string
     *
     * @OGM\Property(type="string")
     */
    private $email;

    /**
     * @var Collection|Order[]
     *
     * @OGM\Relationship(type="PLACED", direction="OUTGOING", collection=true, mappedBy="customer", targetEntity="Order")
     */
    private $orders;

    public function __construct()
    {
        $this->orders = new Collection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * @param Order $order
     */
    public function addOrder(Order $order): void
    {
        $this->orders->add($order);
    }

    /**
     * @param Order $order
     */
    public function removeOrder(Order $order): void
    {
        $this->orders->removeElement($order);
    }

    /**
     * @param Collection|Order[] $orders
     */
    public function setOrders(Collection $orders): void
    {
        $this->orders = $orders;
    }
}
