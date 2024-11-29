<?php

namespace App\Controller\Api;

use App\Entity\Order;
use App\Entity\User;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderService $orderService,
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
        $orders = $this->orderService->getOrders( self::ITEMS_PER_PAGE, $offset);

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
    public function userOrders(User $user, Request $request): Response
    {
        $page = $request->get('page', 0);

        // Добавлена простая пагинация.
        if ($page) {
            $offset = ($page - 1) * self::ITEMS_PER_PAGE;
        } else { // Чтобы не выводить все пока выведем по умолчанию только 10
            $offset = 0;
            //$page = 1;
        }

        $orders = $this->orderService->getUserOrders($user, self::ITEMS_PER_PAGE, $offset);

        return $this->json($orders, Response::HTTP_OK, context: [
            //AbstractNormalizer::GROUPS => ['products:api:list'],
            AbstractNormalizer::GROUPS => ['order:api:list'],
        ]);
    }

    #[Route('/order/create-order', name: 'api-order-create-order', methods: ['POST'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Create an order',
        content: new Model(type: Order::class)
    )]
    public function createOrder(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true);

        $order = $this->orderService->createOrder($payload);
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
        description: 'Delete order',
    )]
    public function delete(Order $order): Response
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }

    #[Route('/order/change-state/{order}', name: 'api-order-update', methods: ['patch'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Update order state',
    )]
    public function changeState(Order $order, Request $request): Response
    {
        $payload = json_decode($request->getContent(), true);
        $order = $this->orderService->changeOrderState($payload, $order);

        return $this->json($order, Response::HTTP_OK);
    }
}
