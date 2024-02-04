<?php

namespace App\Entity;

Class Product
{
    /**
     * [Description for $id]
     *
     * @var int|null
     */
    private ?int $id;

    /**
     * [Description for $brand]
     *
     * @var string
     */
    private string $brand;

    /**
     * [Description for $name]
     *
     * @var string
     */
    private string $name;

    /**
     * [Description for $description]
     *
     * @var string
     */
    private string $description;

    /**
     * [Description for $reference]
     *
     * @var string
     */
    private string $reference;

    /**
     * [Description for $createdAt]
     *
     * @var \DateTimeInterface
     */
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
    
    /**
     * Get the value of id
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Get the value of brand
     *
     * @return string
     */
    public function getBrand(): string {
        return $this->brand;
    }

    /**
     * Set the value of brand
     *
     * @param string $brand
     *
     * @return self
     */
    public function setBrand(string $brand): self {
        $this->brand = $brand;
        return $this;
    }

    /**
     * Get the value of name
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the value of description
     *
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the value of reference
     *
     * @return string
     */
    public function getReference(): string {
        return $this->reference;
    }

    /**
     * Set the value of reference
     *
     * @param string $reference
     *
     * @return self
     */
    public function setReference(string $reference): self {
        $this->reference = $reference;
        return $this;
    }

    /**
     * Get the value of createdAt
     *
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param \DateTimeInterface $createdAt
     *
     * @return self
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }
}