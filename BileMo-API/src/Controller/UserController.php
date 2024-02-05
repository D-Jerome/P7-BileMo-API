<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * class UserController
 */
#[Route('/api/users')]
class UserController
{
    /**
     * Get all Users
     *
     * @param User $user
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     *
     */
    #[Route(name: 'app_users_collection_get', methods:["GET"])]
    public function collection(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($userRepository->findAll(), "json", ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Get One User by Id
     *
     * @param User $user
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     *
     */
    #[Route('/{id}', name: 'app_users_item_get', methods:["GET"])]
    public function item(User $user, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($user, "json", ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }


    /**
     * Create a new User
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return JsonResponse
     *
     */
    #[Route(name: 'app_users_collection_post', methods:["POST"])]
    public function post(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator
    ): JsonResponse {
        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $em->persist($user);
        $em->flush();

        return new JsonResponse(
            $serializer->serialize($user, "json", ['groups' => 'get']),
            JsonResponse::HTTP_CREATED,
            ["Location" => $urlGenerator->generate('app_users_item_get', ["id" => $user->getId()])],
            true
        );
    }

    /**
     * Update User
     *
     * @param User $user
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     *
     */
    #[Route('/{id}', name: 'app_users_item_put', methods:["PUT"])]
    public function put(
        User $user,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em
    ): JsonResponse {

        $user = $serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $user ]
        );
        $em->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }

    /**
     * Delete One User by Id
     *
     * @param User $user
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     *
     */
    #[Route('/{id}', name: 'app_users_item_delete', methods:["DELETE"])]
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {

        $em->remove($user);
        $em->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }
}
