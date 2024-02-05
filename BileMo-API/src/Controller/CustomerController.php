<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * class CustomerController
 */
#[Route('/api/customers')]
class CustomerController
{
    /**
     * Get all Customers
     *
     * @param Customer $customer
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     *
     */
    #[Route(name: 'app_customers_collection_get', methods:["GET"])]
    public function collection(CustomerRepository $customerRepository, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($customerRepository->findAll(), "json", ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Get One Customer by Id
     *
     * @param Customer $customer
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     *
     */
    #[Route('/{id}', name: 'app_customers_item_get', methods:["GET"])]
    public function item(Customer $customer, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($customer, "json", ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }


    /**
     * Create a new Customer
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return JsonResponse
     *
     */
    #[Route(name: 'app_customers_collection_post', methods:["POST"])]
    public function post(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator
    ): JsonResponse {
        /** @var Customer $customer */
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $em->persist($customer);
        $em->flush();

        return new JsonResponse(
            $serializer->serialize($customer, "json", ['groups' => 'get']),
            JsonResponse::HTTP_CREATED,
            ["Location" => $urlGenerator->generate('app_customers_item_get', ["id" => $customer->getId()])],
            true
        );
    }

    /**
     * Update Customer
     *
     * @param Customer $customer
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     *
     */
    #[Route('/{id}', name: 'app_customers_item_put', methods:["PUT"])]
    public function put(
        Customer $customer,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em
    ): JsonResponse {

        $customer = $serializer->deserialize(
            $request->getContent(),
            Customer::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $customer ]
        );
        $em->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }

    /**
     * Delete One Customer by Id
     *
     * @param Customer $customer
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     *
     */
    #[Route('/{id}', name: 'app_customers_item_delete', methods:["DELETE"])]
    public function delete(Customer $customer, EntityManagerInterface $em): JsonResponse
    {

        $em->remove($customer);
        $em->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }
}
