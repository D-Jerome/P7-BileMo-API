<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Product
{
    /**
     * [Description for $id]
     */
    #[ORM\Id]
    #[ORM\Column(type:'integer')]
    #[ORM\GeneratedValue]
    private ?int $id;

    /**
     * [Description for $brand]
     */
    #[ORM\Column]
    private string $brand;

    /**
     * [Description for $name]
     */
    #[ORM\Column]
    private string $name;

    /**
     * [Description for $description]
     */
    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    /**
     * [Description for $reference]
     */
    #[ORM\Column]
    private string $reference;

    /**
     * [Description for $createdAt]
     */
    #[ORM\Column(type:'datetime_immutable')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of brand
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * Set the value of brand
     */
    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
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
     * Get the value of description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of reference
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * Set the value of reference
     */
    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
