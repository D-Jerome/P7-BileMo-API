<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * class CustomerController
 */
#[Route('/api/users')]
class UserController
{
    #[Route(name: 'app_users', methods:["GET"])]
    public function show(UserRepository $userRepository,SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($userRepository->findAll(), "json",['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }
}
