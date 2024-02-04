<?php

namespace App\Entity;

Class Customer
{
    /**
     * [Description for $id]
     *
     * @var int|null
     */
    private ?int $id;

    /**
     * [Description for $name]
     *
     * @var string
     */
    private string $name;

    /**
     * [Description for $slug]
     *
     * @var string
     */
    private string $slug;


    /**
     * Get the value of id
     *
     * @return ?int
     */
    public function getId(): ?int {
        return $this->id;
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
     * Get the value of slug
     *
     * @return string
     */
    public function getSlug(): string {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @param string $slug
     *
     * @return self
     */
    public function setSlug(string $slug): self {
        $this->slug = $slug;
        return $this;
    }
}