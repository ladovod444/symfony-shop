<?php

namespace App\Controller\Api;

use App\Dto\ProductDto;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;
#[OA\Tag(name: "Products api")]
#[Route('/api')]
class ProductController extends AbstractController
{
    const ITEMS_PER_PAGE = 10;

    public function __construct(
        private ProductRepository      $productRepository,
        private EntityManagerInterface $entityManager,
        private UserRepository         $userRepository,
        private ParameterBagInterface $parameterBag
    ) {

    }

    #[Route('/product/list', name: 'api-products-list', methods: ['GET'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Returns Product, yes!!!',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class, groups: ['full']))
        )
    )]
    public function index(Request $request): Response
    {
        $page = $request->get('page', 0);

        // Добавлена простая пагинация.
        if ($page && is_numeric($page)) {
            $offset = ($page - 1) * self::ITEMS_PER_PAGE;
        } else { // Чтобы не выводить все пока выведем по умолчанию только 10
            $offset = 0;
            //$page = 1;
        }

        //dd($this->parameterBag->get('app:api_per_age'));

        $products = $this->productRepository->findBy(
            [],
            ['id' => 'DESC'],
            //limit: self::ITEMS_PER_PAGE,
            limit: $this->parameterBag->get('app:api_per_age'),
            offset: $offset
        );

        return $this->json($products, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/product/dto', name: 'api-product-add-dto', methods: ['post'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Create a product',
        content:  new Model(type: ProductDto::class)
    )]
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

        return $this->json($product, Response::HTTP_CREATED, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/product/dto/{product}', name: 'api-product-update-dto', methods: ['put'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Update a product',
        content:  new Model(type: ProductDto::class)
    )]
    public function updateDto(Product $product, #[MapRequestPayload] ProductDto $productDto): Response
    {
        $product = Product::updateFromDto($productDto, $product);
        $this->entityManager->flush();

        return $this->json($product, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/product/{product}', name: 'api-product-delete', methods: ['delete'], format: 'json')]
    #[OA\Response(
        response: 204,
        description: 'Delete product',
//        content:  new Model(type: ProductDto::class)
    )]
    public function delete(Product $product): Response
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
