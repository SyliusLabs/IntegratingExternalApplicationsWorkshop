<?php

declare(strict_types=1);

namespace RecommendationEngine\Application\Repository;

interface ProductRepository
{
    /**
     * @param string $productCode
     * @param int $limit
     *
     * @return iterable
     */
    public function getRecommendedProducts(string $productCode, int $limit): iterable;
}
