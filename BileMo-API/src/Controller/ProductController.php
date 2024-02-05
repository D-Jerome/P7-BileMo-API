<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * class CustomerController
 */
#[Route('/api/products')]
class ProductController
{
    #[Route(name: 'app_products', methods:["GET"])]
    public function show(ProductRepository $productRepository,SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($productRepository->findAll(), "json",['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }
}
