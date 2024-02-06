<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[UniqueEntity('slug')]
class Customer
{
    /**
     * [Description for $id]
     */
    #[ORM\Id]
    #[ORM\Column(type:'integer')]
    #[ORM\GeneratedValue()]
    #[Groups(["get"])]
    private ?int $id = null;

    /**
     * [Description for $name]
     */
    #[ORM\Column()]
    #[Groups(["get"])]
    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    #[Assert\Length(max: 255)]
    private string $name;

    /**
     * [Description for $slug]
     */
    #[ORM\Column()]
    private string $slug;


    /**
     * Get the value of id
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of slug
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }



    public function computeSlug(SluggerInterface $slugger): void
    {
        $this->slug = (string) $slugger->slug((string) $this->getName())->lower();
    }
}
