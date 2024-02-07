<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * class ProductController
 */
#[Route('/api/products')]
class ProductController
{
    /**
     * Get all Products
     */
    #[Route(name: 'app_products_collection_get', methods:['GET'])]
    public function collection(
        Request $request,
        ProductRepository $productRepository,
        SerializerInterface $serializer,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        /** @var int $page */
        $page = (int)$request->get('page', 1);
        /** @var int $limit */
        $limit = (int)$request->get('limit', 4);
        /** @var string $brand */
        $brand = htmlspecialchars((string)$request->get('brand'));


        if (!$brand) {
            $repo = $productRepository->findAllWithPagination($page, $limit);
        } else {
            $repo = $productRepository->findByWithPagination($brand, $page, $limit);
        }
        if ($repo === []) {
            return new JsonResponse(
                $repo,
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $idCache = "getCustomersCollection-" . $page . "-" . $limit;
        $productsList = $cachePool->get(
            $idCache,
            function (ItemInterface $item) use ($repo) {
                $item->tag("productsCache");
                return $repo;
            }
        );
        
        return new JsonResponse(
            $serializer->serialize($productsList, 'json', ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Get One Product by Id
     */
    #[Route('/{id}', name: 'app_products_item_get', methods:['GET'])]
    public function item(
        Product $product, 
        SerializerInterface $serializer,
        TagAwareCacheInterface $cachePool
        ): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($product, 'json', ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Create a new Product
     */
    #[Route(name: 'app_products_collection_post', methods:['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access')]
    public function post(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        /** @var Product $product */
        $product = $serializer->deserialize($request->getContent(), Product::class, 'json');

        $errors = $validator->validate($product);
        if ($errors->count() > 0) {
            return new JsonResponse(
                $serializer->serialize($errors, 'json'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $em->persist($product);

        $errors = $validator->validate($product);
        if ($errors->count() > 0) {
            return new JsonResponse(
                $serializer->serialize($errors, 'json'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        $cachePool->invalidateTags(['productsCache']);
        $em->flush();

        return new JsonResponse(
            $serializer->serialize($product, 'json', ['groups' => 'get']),
            JsonResponse::HTTP_CREATED,
            ['Location' => $urlGenerator->generate(
                'app_Products_item_get',
                ['id' => $product->getId()]
            )
            ],
            true
        );
    }

    /**
     * Update Product
     */
    #[Route('/{id}', name: 'app_products_item_put', methods:['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access')]
    public function put(
        Product $product,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        $product = $serializer->deserialize(
            $request->getContent(),
            Product::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $product]
        );

        $errors = $validator->validate($product);
        if ($errors->count() > 0) {
            return new JsonResponse(
                $serializer->serialize($errors, 'json'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        $cachePool->invalidateTags(['productsCache']);
        $em->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }

    /**
     * Delete One Product by Id
     */
    #[Route('/{id}', name: 'app_products_item_delete', methods:['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access')]
    public function delete(
        Product $product, 
        EntityManagerInterface $em,
        TagAwareCacheInterface $cachePool
        ): JsonResponse
    {
        $cachePool->invalidateTags(['productsCache']);
        
        $em->remove($product);
        $em->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }
}
