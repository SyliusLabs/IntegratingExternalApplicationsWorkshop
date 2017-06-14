<?php

declare(strict_types=1);

namespace RecommendationEngine\Infrastructure\Repository;

use GraphAware\Common\Result\Record;
use GraphAware\Neo4j\OGM\EntityManagerInterface;
use RecommendationEngine\Application\Repository\ProductRepository;
use RecommendationEngine\Domain\Model\Product;

final class Neo4jProductRepository implements ProductRepository
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** {@inheritdoc} */
    public function getRecommendedProducts(string $productCode, int $limit): iterable
    {
        $result = $this->entityManager->getDatabaseDriver()->run('
            MATCH (product:Product)<-[:HAS]-(order)-[:HAS]->(anotherProduct:Product)
            WHERE product.code={productCode}
            AND NOT anotherProduct.code=product.code
            RETURN anotherProduct.code, COUNT(*) as occurence
            ORDER BY occurence DESC
            LIMIT {limit}
        ', ['productCode' => $productCode, 'limit' => $limit]);

        return array_map(function (Record $record) {
            return $record->values()[0];
        }, $result->records());
    }
}
