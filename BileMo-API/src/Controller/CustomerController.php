<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class CustomerController
 */
#[Route('/api/customers')]
class CustomerController
{
    #[Route(name: 'app_customers', methods:["GET"])]
    public function collection(): JsonResponse
    {
        return new JsonResponse([]);
    }
}
