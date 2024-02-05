<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * class CustomerController
 */
#[Route('/api/customers')]
class CustomerController
{
    #[Route(name: 'app_customers', methods:["GET"])]
    public function show(CustomerRepository $customerRepository,SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($customerRepository->findAll(), "json",['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }
}
