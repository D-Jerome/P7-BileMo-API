<?php

namespace App\Entity;

use App\Entity\Customer;

class User
{
    /**
     * [Description for $id]
     *
     * @var int|null
     */
    private ?int $id;

    /**
     * [Description for $email]
     *
     * @var string
     */
    private string $email;

    /**
     * [Description for $password]
     *
     * @var string
     */
    private string $password;

    /**
     * [Description for $createdAt]
     *
     * @var \DateTimeInterface
     */
    private \DateTimeInterface $createdAt;

    /**
     * [Description for $customer]
     *
     * @var Customer
     */
    private Customer $customer;

    /**
     * [Description for __construct]
     *
     *
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }


    /**
     * Get the value of id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * Get the value of email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param string $email
     *
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get the value of password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @param string $password
     *
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get the value of createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get the value of customer
     *
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * Set the value of customer
     *
     * @param Customer $customer
     *
     * @return self
     */
    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }
}
