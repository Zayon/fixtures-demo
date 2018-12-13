<?php

declare(strict_types=1);

namespace App\Action;

use App\Entity\Product;
use App\Repository\ProductCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddProductAction
{
    /** @var ProductCategoryRepository */
    private $productCategoryRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        ProductCategoryRepository $productCategoryRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->productCategoryRepository = $productCategoryRepository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request)
    {
        $payload = json_decode($request->getContent(), true);

        $category = $this->productCategoryRepository->find($payload['category']);

        if (null === $category) {
            throw new \Exception('Unknown Category');
        }

        $product = (new Product())
            ->setName($payload['name'])
            ->setPrice($payload['price'])
            ->setCategory($category);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return new Response('Product created');
    }
}
