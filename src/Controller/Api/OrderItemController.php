<?php

namespace App\Controller\Api;

use App\Dto\OrderItemDto;
use App\Entity\OrderItem;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class OrderItemController extends AbstractController
{
    public function __construct(
        private readonly OrderItemRepository    $orderItemRepository,
        private readonly ProductRepository      $productRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $userRepository,
        private readonly OrderRepository        $orderRepository,
    ) {

    }
    const ITEMS_PER_PAGE = 10;

    // По сути данный action нужен только для тестирования
    #[Route('/api/order-item/list', name: 'api-order-items-list', methods: ['GET'], format: 'json')]
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
        $products = $this->orderItemRepository->findBy(
            [],
            ['id' => 'DESC'],
            limit: self::ITEMS_PER_PAGE,
            offset: $offset
        );

        return $this->json($products, Response::HTTP_OK, context: [
            //AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/api/order-item/dto', name: 'api-order-item-add-dto', methods: ['post'], format: 'json')]
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
        $orderItem = OrderItem::createFromDto($user,
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
}