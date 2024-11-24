<?php

namespace App\Controller\Api;

use App\Dto\ProductDto;
use App\Entity\Product;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;
#[OA\Tag(name: "Products api")]
#[Route('/api/v1')]
#[Security(name: "Bearer")]
class ProductController extends AbstractController
{
    const ITEMS_PER_PAGE = 10;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductService        $productService,
    ) {

    }

    #[Route('/product/list', name: 'api-products-list', methods: ['GET'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Returns Products list, yes!!!',
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

        $products = $this->productService->getProducts($offset, $offset);
        return $this->json($products, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/product/{product}', name: 'api-product', methods: ['GET'], format: 'json')]
    #[OA\Parameter(
        name: "Accept-Language",
        description: "Set language parameter by RFC2616 <https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4>",
        in: "header",
//        OA\Schema(
//            type="string"
//        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns Product, yes!!!',
        content:  new Model(type: ProductDto::class)
    )]
    public function getProduct(Product $product): Response
    {
        if (null === $product) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }
        return $this->json($product, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/product/dto', name: 'api-product-add-dto', methods: ['post'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Create a product',
        content:  new Model(type: ProductDto::class)
    )]
    public function addDto(#[MapRequestPayload] ProductDto $productDto): Response
    //                         #[MapRequestPayload(
    //                          // acceptFormat: 'json',
    //                          // resolver: 'App\Resolver\ProductResolver',
    //                         )] ProductDto $ProductDto): Response
    {

        $product = $this->productService->createProduct($productDto);

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
        $product = $this->productService->updateProduct($product, $productDto);

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
//    #[IsGranted('PRODUCT_DELETE', 'product')]
    public function delete(Product $product): Response
    {
        //dd($product);
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
