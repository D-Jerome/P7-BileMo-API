<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * class CustomerController
 *
 */
#[Route('/api/customers')]
class CustomerController
{
    /**
     * [Description for collection]
     *
     * @param CustomerRepository $customerRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     *
     */
    #[Route(name: 'app_customers_list', methods: ["GET"])]
    public function collection(CustomerRepository $customerRepository, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($customerRepository->findAll(), "json"),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }
}
