<?php

namespace App\Entity;

use App\Dto\OrderItemDto;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user_order:api:list', 'order:api:list'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['user_order:api:list', 'order:api:list'])]
    private ?int $quantity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user_order:api:list', 'order:api:list'])]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user_order:api:list'])]
    private ?Order $ord = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getOrd(): ?Order
    {
        return $this->ord;
    }

    public function setOrd(?Order $ord): void
    {
        $this->ord = $ord;
    }

    public static function createFromDto(
        User $user,
        OrderItemDto $dto,
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager,
        OrderItemRepository $orderItemRepository
    ): static {
        // ЕСЛИ заказ уже создан - это наверно должно быть Update

        $orderItem = new self();
        $orderItem->setQuantity($dto->quantity);
        $product = $productRepository->find($dto->product);
        $orderItem->setProduct($product);

        if (null !== $dto->order) {
            $order = $orderRepository->find((int)$dto->order);
            if (null === $order) {
                $order = new Order();
                $order->setOwner($user);
                $entityManager->persist($order);
                $entityManager->flush();
            }
        }

        $orderItem->setOrd($order);
        return $orderItem;
    }

    public static function updateFromDto(OrderItemDto $dto, OrderItem $orderItem): static
    {
        if ($dto->quantity) {
            $quantity = $dto->quantity;
            $orderItem->setQuantity($quantity);
        }

        return $orderItem;
    }

}
