<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * [Description AppFixtures]
 */
class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * [Description for load]
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        for ($j =0; $j <= 5; $j++){
            $customer = new Customer;
            $customer->setName($faker->company());
            $customerList[] = $customer;
            $manager->persist($customer);
        }

        for($i = 0; $i <= 10; ++$i) {
            $user = new User();
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
            $user->setEmail($faker->companyEmail());
            $user->setRoles(['ROLE_USER']);
            $user->setCustomer($customerList[array_rand($customerList)]);
            $manager->persist($user);
        }

        for($i = 0; $i <= 20; ++$i) {
            $product = new Product();
            $product->setBrand($faker->company());
            $product->setName($faker->word(6));
            $product->setReference($faker->uuid());
            $product->setDescription($faker->realText($faker->numberBetween(200, 300)));
            $manager->persist($product);
        }
        $manager->flush();
    }

}
