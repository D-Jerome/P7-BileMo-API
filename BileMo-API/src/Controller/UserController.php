<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function collection(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        /**
         * @var User $connectedUser
         */
        $connectedUser = $this->getUser();

        if ($connectedUser->getRoles() === ['ROLE_ADMIN']) {
            $repo = $userRepository->findAll();
        } else {
            $repo = $userRepository->findBy(['customer' => $connectedUser->getCustomer()]);
        }

        return new JsonResponse(
            $serializer->serialize(
                $repo,
                'json',
                ['groups' => 'get']
            ),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Get One User by Id
     */
    #[Route('/{id}', name: 'app_users_item_get', methods:['GET'])]
    public function item(User $user, SerializerInterface $serializer): JsonResponse
    {
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
        UserPasswordHasherInterface $userPasswordHasher
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

        $em->flush();

        return new JsonResponse(
            $serializer->serialize(
                $user, 'json', ['groups' => 'get']),
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
        UserPasswordHasherInterface $userPasswordHasher
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
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
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

        $em->remove($user);
        $em->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }
}
