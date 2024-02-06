<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\User;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

#[AsEntityListener(event: Events::prePersist, entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, entity: User::class)]
class UserEntityListener
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    public function prePersist(User $user): void
    {
        $user->computeSlug($this->slugger);
    }

    public function preUpdate(User $user): void
    {
        $user->computeSlug($this->slugger);
    }
}
