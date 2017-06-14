<?php

declare(strict_types=1);

namespace RecommendationEngine\Domain\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * @OGM\Node(label="Order")
 */
class Order
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
    private $token;

    /**
     * @var Collection|Product[]
     *
     * @OGM\Relationship(type="HAS", direction="OUTGOING", collection=true, targetEntity="Product")
     */
    private $products;

    public function __construct()
    {
        $this->products = new Collection();
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
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @param Product $product
     */
    public function addProduct(Product $product): void
    {
        $this->products->add($product);
    }

    /**
     * @param Product $product
     */
    public function removeProduct(Product $product): void
    {
        $this->products->removeElement($product);
    }

    /**
     * @param Collection|Product[] $products
     */
    public function setProducts(Collection $products): void
    {
        $this->products = $products;
    }
}
