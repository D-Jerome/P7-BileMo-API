<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function collection(CustomerRepository $customerRepository, SerializerInterface $serializer): JsonResponse
    {
        /**
         * @var User $connectedUser
         */
        $connectedUser = $this->getUser();

        if ($connectedUser->getRoles() === ['ROLE_ADMIN']) {
            $repo = $customerRepository->findAll();
        } else {
            $repo = $customerRepository->findBy(['id' => $connectedUser->getCustomer()]);
        }

        return new JsonResponse(
            $serializer->serialize($repo, 'json', ['groups' => 'get']),
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
    public function item(Customer $customer, SerializerInterface $serializer): JsonResponse
    {
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
        ValidatorInterface $validator
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
        ValidatorInterface $validator
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
