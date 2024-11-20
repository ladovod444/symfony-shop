<?php

namespace App\Entity;

use App\Dto\ProductDto;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;
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
    #[Groups(['products:api:list', 'user_order:api:list', 'order:api:list'])]
    private ?string $sku = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['products:api:list', 'order:api:list'])]
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
    #[Groups(['products:api:list'])]
    private ?User $user_id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['products:api:list', 'user_order:api:list', 'order:api:list'])]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category = null;

    #[ORM\Column(nullable: true)]
    private ?int $retailcrm_id = null;

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

    public function getUser(): ?User
    {
        return $this->user_id;
    }

    public function setUser(?User $user_id): static
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

    public static function createFromDto(
      UserInterface|User|null $user, ProductDto $productDto, UserRepository $userRepository
    ): static
    {
        if ($user === null) {
            $user = $userRepository->findOneBy(['id'=> 1]);
        }

        $product = new self();
        $product->setTitle($productDto->title)
            ->setSku($productDto->sku)
            ->setCurrentPrice($productDto->current_price)
            ->setRegularPrice($productDto->regular_price)
            ->setDescription($productDto->description)
            ->setImage($productDto->image)
            ->setUser($user);

        return $product;
    }

    public static function updateFromDto(ProductDto $productDto, Product $product): static
    {
        $product->setTitle($productDto->title)
            ->setSku($productDto->sku)
            ->setCurrentPrice($productDto->current_price)
            ->setRegularPrice($productDto->regular_price)
            ->setDescription($productDto->description)
            ->setImage($productDto->image);
            //->setUserId($user);
        return $product;
    }

    public function __toString(): string
    {
        return $this->getTitle() ?? '';
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getRetailcrmId(): ?int
    {
        return $this->retailcrm_id;
    }

    public function setRetailcrmId(?int $retailcrm_id): static
    {
        $this->retailcrm_id = $retailcrm_id;

        return $this;
    }
}
