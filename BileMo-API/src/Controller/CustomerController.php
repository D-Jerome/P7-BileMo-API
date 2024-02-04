<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class CustomerController
 *
 */
#[Route('/api/customers')]
class CustomerController
{
    /**
     * [Description for showList]
     *
     * @return JsonResponse
     *
     */
    #[Route( name: 'app_customers_list', methods: ["GET"])]
    public function collection(CustomerRepository $customerRepository): JsonResponse
    {
        return new JsonResponse($customerRepository->findAll());
    }
}
