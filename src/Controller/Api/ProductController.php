<?php

namespace App\Controller\Api;

use App\Dto\ProductDto;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ProductController extends AbstractController
{
    const ITEMS_PER_PAGE = 10;

    public function __construct(
        private ProductRepository      $productRepository,
        private EntityManagerInterface $entityManager,
        private UserRepository         $userRepository
    ) {

    }

    #[Route('/api/products', name: 'products')]
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
        $products = $this->productRepository->findBy(
            [],
            ['id' => 'DESC'],
            limit: self::ITEMS_PER_PAGE,
            offset: $offset
        );

        return $this->json($products, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/api/product/dto', name: 'api-product-add-dto', methods: ['post'], format: 'json')]
    public function addDto(Request $request, #[MapRequestPayload] ProductDto $ProductDto): Response
    //                         #[MapRequestPayload(
    //                          // acceptFormat: 'json',
    //                          // resolver: 'App\Resolver\ProductResolver',
    //                         )] ProductDto $ProductDto): Response
    {
        //dd($ProductDto);
        $user = $this->userRepository->findOneBy(['email' => 'ladovod@gmail.com']);
        $product = Product::createFromDto($user, $ProductDto);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->json($product, Response::HTTP_CREATED);
    }

    #[Route('/api/product/dto/{product}', name: 'api-product-update-dto', methods: ['put'], format: 'json')]
    public function updateDto(Product $product, #[MapRequestPayload] ProductDto $productDto): Response
    {
        $product = Product::updateFromDto($productDto, $product);
        $this->entityManager->flush();

        return $this->json($product, Response::HTTP_OK);
    }

    #[Route('/api/product/{product}', name: 'api-product-delete', methods: ['delete'], format: 'json')]
    public function delete(Product $product): Response
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
