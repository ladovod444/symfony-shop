<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Exceptions\ProductNotFound;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class OrderService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly UserRepository $userRepository,
        private readonly ProductRepository $productRepository,
        private readonly OrderRepository $orderRepository,
        private readonly ParameterBagInterface $parameterBag,
    )
    {
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return Order []
     */
    public function getOrders(int $limit = 10, int $offset = 0): array
    {
        $orders = $this->orderRepository->findBy(
            [],
            ['id' => 'DESC'],
            limit: $limit,
            offset: $offset
        );

        return $orders;
    }

    /**
     * @param User $owner
     * @param int $limit
     * @param int $offset
     * @return Order []
     */
    public function getUserOrders(User $owner, int $limit = 10, int $offset = 0): array {
        $orders = $this->orderRepository->findBy(
            ['owner' => $owner->getId()],
            ['id' => 'DESC'],
            limit: $limit,
            offset: $offset
        );

        return $orders;
    }

    /**
     * @param $payload array
     * @param Order $order
     * @return Order
     */
    public function changeOrderState(array $payload, Order $order): Order {
        $state = $payload['state'];

        $order->setStatus($state);
        $this->entityManager->flush();

        return $order;
    }

    /**
     * @param $payload array
     * @return Order
     */
    public function createOrder(array $payload): Order
    {
        $order_status = $this->parameterBag->get('app:order_created_status');

        $order = new Order();
        $user = $this->userRepository->findOneBy(['email' => $payload['mail']]);
        // Если нет user, то нужно создать
        if (!$user) {
            $user = new User();
            $user->setEmail($payload['mail']);

            $plainPassword = 'test';
            // encode the plain password
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));

            // @todo продумать отправку email вновь соазданному юзеру
//            $registerUserEvent = new RegisteredUserEvent($user);
//            $this->eventDispatcher->dispatch($registerUserEvent, RegisteredUserEvent::NAME);
            //$user->setEnabled(true);

            $this->entityManager->persist($user);


            // @todo нужно вынести код в Message ???
//            $id = $this->customerManager->createCustomer($user);
//            $user->setCustomerId($id);
//            $this->entityManager->flush();
        }
        $order->setOwner($user);
        $this->entityManager->persist($order);

        foreach ($payload['order'] as $order_item) {
            $orderItem = new OrderItem();
            $product = $this->productRepository->find($order_item['id']);
            //dd($product);
            if (null === $product) {
                throw new ProductNotFound($order_item['id']);
            }
            $orderItem->setProduct($this->productRepository->find($order_item['id']))
                ->setQuantity($order_item['quantity'])
                ->setOrd($order);
            $this->entityManager->persist($orderItem);
            $order->addOrderItem($orderItem);
        }
        $order->setStatus($order_status);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }
}