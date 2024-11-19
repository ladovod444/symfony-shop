<?php

namespace App\Controller\Api;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Event\RegisteredUserEvent;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

#[OA\Tag(name: "Orders api")]
#[Route('/api/v1')]
#[Security(name: "Bearer")]
class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderItemRepository    $orderItemRepository,
        private readonly ProductRepository      $productRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $userRepository,
        private readonly OrderRepository        $orderRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EventDispatcherInterface     $eventDispatcher
    ) {

    }

    const ITEMS_PER_PAGE = 10;

    // По сути данный action нужен только для тестирования
    #[Route('/order/list', name: 'api-order-list', methods: ['GET'], format: 'json')]
    public function index(Request $request): Response
    {
        $page = $request->get('page', 0);

        // Добавлена простая пагинация.
        if ($page) {
            $offset = ($page - 1) * self::ITEMS_PER_PAGE;
        } else { // Чтобы не выводить все пока выведем по умолчанию только 10
            $offset = 0;
            //$page = 1;
        }
        $orders = $this->orderRepository->findBy(
            [],
            ['id' => 'DESC'],
            limit: self::ITEMS_PER_PAGE,
            offset: $offset
        );

        return $this->json($orders, Response::HTTP_OK, context: [
            //AbstractNormalizer::GROUPS => ['products:api:list'],
            AbstractNormalizer::GROUPS => ['order:api:list'],
        ]);
    }

    /**
     * User Orders.
     * @param User $user
     * @param Request $request
     * @return Response
     */
    #[Route('/order/user/{user}', name: 'api-order-user-orders', methods: ['GET'], format: 'json')]
    public function indexOrder(User $user, Request $request): Response
    {
        $page = $request->get('page', 0);

        // Добавлена простая пагинация.
        if ($page) {
            $offset = ($page - 1) * self::ITEMS_PER_PAGE;
        } else { // Чтобы не выводить все пока выведем по умолчанию только 10
            $offset = 0;
            //$page = 1;
        }
        $orders = $this->orderRepository->findBy(
            ['owner' => $user->getId()],
            ['id' => 'DESC'],
            limit: self::ITEMS_PER_PAGE,
            offset: $offset
        );

        return $this->json($orders, Response::HTTP_OK, context: [
            //AbstractNormalizer::GROUPS => ['products:api:list'],
            AbstractNormalizer::GROUPS => ['order:api:list'],
        ]);
    }

    #[Route('/order/create-order', name: 'api-order-create-order', methods: ['POST'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Create an Order',
        content:  new Model(type: Order::class)
    )]
    public function createOrder(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true);

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
            $this->entityManager->flush();
        }
        $order->setOwner($user);
        $this->entityManager->persist($order);

        foreach ($payload['order'] as $order_item) {
            $orderItem = new OrderItem();
            $orderItem->setProduct($this->productRepository->find($order_item['id']))
                ->setQuantity($order_item['quantity'])
                ->setOrd($order);
            $this->entityManager->persist($orderItem);
            $order->addOrderItem($orderItem);
        }
        $order->setStatus('created');
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $this->json($order, Response::HTTP_CREATED, context: [
            AbstractNormalizer::GROUPS => ['order:api:list'],
        ]);
    }

    /*
    #[Route('/api/order-item/dto', name: 'api-order-add-dto', methods: ['post'], format: 'json')]
    //    #[OA\Response(
    //        response: 200,
    //        description: 'Create a product',
    //        content:  new Model(type: ProductDto::class)
    //    )]
    public function addDto(Request $request, #[MapRequestPayload] OrderItemDto $orderItemDto): Response
    {

        //dd($orderItemDto);
        $user = $this->userRepository->findOneBy(['email' => 'ladovod@gmail.com']);

        //$order = $this->orderRepository->find(1);
        //dd($order);

        // При добавлении
        // Если нет еще Order, то по сути его нужно создать
        $orderItem = OrderItem::createFromDto(
            $user,
            $orderItemDto,
            $this->orderRepository,
            $this->productRepository,
            $this->entityManager,
            $this->orderItemRepository
        );

        //dd($orderItem);

        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();

        return $this->json($orderItem, Response::HTTP_CREATED, context: [
            AbstractNormalizer::GROUPS => ['user_order:api:list'],
        ]);
    }

    // При обновлении Order Item обычно обновляется кол-во,
    // т.е. товар или добавили в корзину еще раз или уже в корзине увеличили кол-во
    #[Route('/api/order/dto/{order_item}', name: 'api-order-update-dto', methods: ['patch'], format: 'json')]
    public function updateDto(OrderItem $order_item, #[MapRequestPayload] OrderItemDto $orderItemDto): Response
    {
        $orderItem = OrderItem::updateFromDto(
            $orderItemDto,
            $order_item
        );

        $this->entityManager->flush();

        return $this->json($orderItem, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['user_order:api:list'],
        ]);
    }
    */

    #[Route('/order/{order}', name: 'api-order-delete', methods: ['delete'], format: 'json')]
    #[OA\Response(
        response: 204,
        description: 'Delete order item',
    )]
    public function delete(Order $order): Response
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
