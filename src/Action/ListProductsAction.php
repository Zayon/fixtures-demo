<?php

declare(strict_types=1);

namespace App\Action;

use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ListProductsAction
{
    /** @var ProductRepository */
    private $productRepository;

    /** @var NormalizerInterface */
    private $normalizer;

    /** @var ProductCategoryRepository */
    private $productCategoryRepository;

    public function __construct(
        ProductRepository $productRepository,
        ProductCategoryRepository $productCategoryRepository,
        NormalizerInterface $normalizer
    ) {
        $this->productRepository = $productRepository;
        $this->normalizer = $normalizer;
        $this->productCategoryRepository = $productCategoryRepository;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $filter = [];
        $categoryName = $request->query->get('category');

        if (null !== $categoryName) {
            $category = $this->productCategoryRepository->findOneBy(['name' => $categoryName]);
            $filter = ['category' => $category];
        }

        $products = $this->productRepository->findBy($filter);

        $response = [
            'type' => 'list:Product',
            'items' => $this->normalizer->normalize($products, 'json', [
                'ignored_attributes' => [
                    '__initializer__',
                    '__cloner__',
                    '__isInitialized__',
                ]
            ]),
            'total' => \count($products),
        ];

        return new JsonResponse($response);
    }
}
