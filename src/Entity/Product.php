<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['products:api:list'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['products:api:list'])]
    private ?string $sku = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['products:api:list'])]
    private ?string $current_price = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['products:api:list'])]
    private ?string $regular_price = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['products:api:list'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['products:api:list'])]
    private ?string $image = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['products:api:list'])]
    private ?string $title = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): static
    {
        $this->sku = $sku;

        return $this;
    }

    public function getCurrentPrice(): ?string
    {
        return $this->current_price;
    }

    public function setCurrentPrice(string $current_price): static
    {
        $this->current_price = $current_price;

        return $this;
    }

    public function getRegularPrice(): ?string
    {
        return $this->regular_price;
    }

    public function setRegularPrice(string $regular_price): static
    {
        $this->regular_price = $regular_price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
