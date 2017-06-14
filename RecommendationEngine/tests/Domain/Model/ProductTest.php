<?php

declare(strict_types=1);

namespace Tests\RecommendationEngine\Domain\Model;

use PHPUnit\Framework\TestCase;
use RecommendationEngine\Domain\Model\Product;

final class ProductTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_code(): void
    {
        $product = new Product();
        $product->setCode('awesome_sylius_mug');

        $this->assertEquals('awesome_sylius_mug', $product->getCode());
    }
}
