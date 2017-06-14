<?php

declare(strict_types=1);

namespace RecommendationEngine\Domain\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Product")
 */
class Product
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
    private $code;

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
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }
}
