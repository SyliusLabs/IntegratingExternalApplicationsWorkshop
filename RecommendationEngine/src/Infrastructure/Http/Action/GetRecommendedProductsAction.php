<?php

declare(strict_types=1);

namespace RecommendationEngine\Infrastructure\Http\Action;

use RecommendationEngine\Application\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GetRecommendedProductsAction
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $productCode = $request->attributes->get('productCode');
        $limit = (int) $request->query->get('limit', 10);

        return new JsonResponse($this->productRepository->getRecommendedProducts($productCode, $limit));
    }
}
