<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * class UserController
 */
#[Route('/api/users')]
class UserController extends AbstractController
{
    /**
     * Get all Users
     */
    #[Route(name: 'app_users_collection_get', methods:['GET'])]
    public function collection(
        Request $request,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        /**
         * @var User $connectedUser
         */
        $connectedUser = $this->getUser();
        /** @var int $page */
        $page = (int)($request->get('page', 1));
        /** @var int $limit */
        $limit = (int)$request->get('limit', 4);


        $idCache = "getUsersCollection-" . $page . "-" . $limit;
        $jsonUsersList = $cachePool->get(
            $idCache,
            function (ItemInterface $item) use ($connectedUser, $userRepository, $page, $limit, $serializer) {
                $item->tag("usersCache");
                if ($connectedUser->getRoles() === ['ROLE_ADMIN']) {
                    $repo = $userRepository->findAllWithPagination($page, $limit);
                } else {
                    $repo = $userRepository->findByWithPagination(['customer' => $connectedUser->getCustomer()], $page, $limit);
                }
                return $serializer->serialize(
                    $repo,
                    'json',
                    ['groups' => 'get']
                );
            }
        );
        return new JsonResponse(
            $jsonUsersList,
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Get One User by Id
     */
    #[Route('/{id}', name: 'app_users_item_get', methods:['GET'])]
    public function item(
        User $user,
        SerializerInterface $serializer,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        /**
         * @var User $connectedUser
         */
        $connectedUser = $this->getUser();
        if ($connectedUser->getRoles() === ['ROLE_ADMIN']) {
            return new JsonResponse(
                $serializer->serialize($user, 'json', ['groups' => 'get']),
                JsonResponse::HTTP_OK,
                [],
                true
            );
        }
        if ($connectedUser->getCustomer() !== $user->getCustomer()) {
            return new JsonResponse(
                null,
                JsonResponse::HTTP_UNAUTHORIZED
            );
        }

        return new JsonResponse(
            $serializer->serialize($user, 'json', ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Create a new User
     */
    #[Route(name: 'app_users_collection_post', methods:['POST'])]
    public function post(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $userPasswordHasher,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        /**
         * @var User $connectedUser
         */
        $connectedUser = $this->getUser();

        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($user);
        if ($errors->count() > 0) {
            return new JsonResponse(
                $serializer->serialize($errors, 'json'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );
        $user->setCustomer($connectedUser->getCustomer());
        $em->persist($user);

        $errors = $validator->validate($user);
        if ($errors->count() > 0) {
            return new JsonResponse(
                $serializer->serialize($errors, 'json'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        $cachePool->invalidateTags(['usersCache']);
        $em->flush();

        return new JsonResponse(
            $serializer->serialize(
                $user,
                'json',
                ['groups' => 'get']
            ),
            JsonResponse::HTTP_CREATED,
            ['Location' => $urlGenerator->generate('app_users_item_get', ['id' => $user->getId()])],
            true
        );
    }

    /**
     * Update User
     */
    #[Route('/{id}', name: 'app_users_item_put', methods:['PUT'])]
    public function put(
        User $user,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $userPasswordHasher,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        /**
         * @var User $connectedUser
         */
        $connectedUser = $this->getUser();

        if ($connectedUser->getCustomer() !== $user->getCustomer()) {
            return new JsonResponse(
                null,
                JsonResponse::HTTP_UNAUTHORIZED
            );
        }
        $userDataToChange = $serializer->deserialize(
            $request->getContent(),
            User::class,
            'json'
        );
        $user = $serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $user]
        );

        $errors = $validator->validate($user);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST);
        }
        if ($userDataToChange->getPassword()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );
        }
        $cachePool->invalidateTags(['usersCache']);
        $em->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }

    /**
     * Delete One User by Id
     */
    #[Route('/{id}', name: 'app_users_item_delete', methods:['DELETE'])]
    public function delete(
        User $user,
        EntityManagerInterface $em,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        /**
         * @var User $connectedUser
         */
        $connectedUser = $this->getUser();

        if ($connectedUser->getCustomer() !== $user->getCustomer()) {
            return new JsonResponse(
                null,
                JsonResponse::HTTP_UNAUTHORIZED
            );
        }
        $cachePool->invalidateTags(['usersCache']);
        $em->remove($user);
        $em->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }
}
