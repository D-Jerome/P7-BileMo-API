<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * class CustomerController
 */
#[Route('/api/customers')]
class CustomerController extends AbstractController
{
    /**
     * Get all Customers
     */
    #[Route(name: 'app_customers_collection_get', methods:['GET'])]
    #[IsGranted('ROLE_COMPANY_ADMIN', message: 'You are not allowed to access')]
    public function collection(
        Request $request,
        CustomerRepository $customerRepository,
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
        if ($connectedUser->getRoles() === ['ROLE_ADMIN']) {
            $repo = $customerRepository->findAllWithPagination($page, $limit);
        } else {
            $repo = $customerRepository->findBy(['id' => $connectedUser->getCustomer()]);
        }
        $idCache = "getCustomersCollection-" . $page . "-" . $limit;
        $customersList = $cachePool->get(
            $idCache,
            function (ItemInterface $item) use ($repo) {
                $item->tag("customersCache");
                return $repo;
            }
        );



        return new JsonResponse(
            $serializer->serialize($customersList, 'json', ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Get One Customer by Id
     */
    #[Route('/{id}', name: 'app_customers_item_get', methods:['GET'])]
    #[IsGranted('ROLE_COMPANY_ADMIN', message: 'You are not allowed to access')]
    public function item(
        Customer $customer,
        SerializerInterface $serializer,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        /**
         * @var User $connectedUser
         */
        $connectedUser = $this->getUser();

        if ($connectedUser->getRoles() === ['ROLE_ADMIN']) {
            return new JsonResponse(
                $serializer->serialize($customer, 'json', ['groups' => 'get']),
                JsonResponse::HTTP_OK,
                [],
                true
            );
        }

        if ($connectedUser->getCustomer() !== $customer) {
            return new JsonResponse(
                null,
                JsonResponse::HTTP_UNAUTHORIZED
            );
        }

        return new JsonResponse(
            $serializer->serialize($customer, 'json', ['groups' => 'get']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Create a new Customer
     */
    #[Route(name: 'app_customers_collection_post', methods:['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access')]
    public function post(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        /** @var Customer $customer */
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');

        $errors = $validator->validate($customer);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $em->persist($customer);

        $errors = $validator->validate($customer);
        if ($errors->count() > 0) {
            return new JsonResponse(
                $serializer->serialize($errors, 'json'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        $cachePool->invalidateTags(['customersCache']);
        $em->flush();

        return new JsonResponse(
            $serializer->serialize($customer, 'json', ['groups' => 'get']),
            JsonResponse::HTTP_CREATED,
            ['Location' => $urlGenerator->generate('app_customers_item_get', ['id' => $customer->getId()])],
            true
        );
    }

    /**
     * Update Customer
     */
    #[Route('/{id}', name: 'app_customers_item_put', methods:['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access')]
    public function put(
        Customer $customer,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        $customer = $serializer->deserialize(
            $request->getContent(),
            Customer::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $customer]
        );

        $errors = $validator->validate($customer);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST);
        }
        $cachePool->invalidateTags(['customersCache']);

        $em->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }

    /**
     * Delete One Customer by Id
     */
    #[Route('/{id}', name: 'app_customers_item_delete', methods:['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access')]
    public function delete(
        Customer $customer,
        EntityManagerInterface $em,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {

        $cachePool->invalidateTags(['customersCache']);

        $em->remove($customer);
        $em->flush();


        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }
}
