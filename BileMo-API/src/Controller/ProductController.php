<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * class ProductController
 */
#[Route('/api/products')]
class ProductController
{
   /**
     * Get all Products
     *
     * @param Product $product
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     *
     */
    #[Route(name: 'app_products_collection_get', methods:["GET"])]
    public function collection(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($productRepository->findAll(), "json", ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Get One Product by Id
     *
     * @param Product $product
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     *
     */
    #[Route('/{id}', name: 'app_products_item_get', methods:["GET"])]
    public function item(Product $product, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($product, "json", ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }


    /**
     * Create a new Product
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface  $validator
     * @return JsonResponse
     *
     */
    #[Route(name: 'app_products_collection_post', methods:["POST"])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access' )]
    public function post(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface  $validator
    ): JsonResponse {
        /** @var Product $product */
        $product = $serializer->deserialize($request->getContent(), Product::class, 'json');
        
        $errors = $validator->validate($product);
        if ($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors,"json"), JsonResponse::HTTP_BAD_REQUEST);
        }
        
        $em->persist($product);

        $errors = $validator->validate($product);
        if ($errors->count() > 0){
            return new JsonResponse(
                $serializer->serialize($errors,"json"),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        $em->flush();

        return new JsonResponse(
            $serializer->serialize($product, "json", ['groups' => 'get']),
            JsonResponse::HTTP_CREATED,
            ["Location" => $urlGenerator->generate('app_Products_item_get', ["id" => $product->getId()])],
            true
        );
    }

    /**
     * Update Product
     *
     * @param Product $product
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param ValidatorInterface  $validator
     * @return JsonResponse
     *
     */
    #[Route('/{id}', name: 'app_products_item_put', methods:["PUT"])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access' )]
    public function put(
        Product $product,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface  $validator
    ): JsonResponse {

        $product = $serializer->deserialize(
            $request->getContent(),
            Product::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $product ]
        );
        
        $errors = $validator->validate($product);
        if ($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors,"json"), JsonResponse::HTTP_BAD_REQUEST);
        }

        $em->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }

    /**
     * Delete One Product by Id
     *
     * @param Product $product
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     *
     */
    #[Route('/{id}', name: 'app_products_item_delete', methods:["DELETE"])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access' )]
    public function delete(Product $product, EntityManagerInterface $em): JsonResponse
    {

        $em->remove($product);
        $em->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }
}
