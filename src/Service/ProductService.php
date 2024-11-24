<?php

namespace App\Service;

use App\Dto\ProductDto;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ProductService
{

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ParameterBagInterface $parameterBag,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @param int $page
     * @param int $offset
     * @return ProductDto []
     */
    public function getProducts(int $page = 1, int $offset = 10): array
    {
        $products = $this->productRepository->findBy(
            [],
            ['id' => 'DESC'],
            //limit: self::ITEMS_PER_PAGE,
            limit: $this->parameterBag->get('app:api_per_age'),
            offset: $offset
        );

        if (!$page) {
            $products = $this->productRepository->findBy(
                [],
                ['id' => 'DESC'],
            //limit: self::ITEMS_PER_PAGE,
//                limit: $this->parameterBag->get('app:api_per_age'),
//                offset: $offset
            );
        }
        
        return array_map(
            fn(Product $item) => new ProductDto(
                $item->getTitle(),
                $item->getDescription(),
                $item->getSku(),
                $item->getCurrentPrice(),
                $item->getRegularPrice(),
                $item->getImage(),
            ),
            $products
        );
    }

    public function createProduct(ProductDto $productDto): Product
    {
        $user = $this->userRepository->findOneBy(['email' => 'ladovod@gmail.com']);
        $product = Product::createFromDto($user, $productDto, $this->userRepository);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    public function updateProduct(Product $product, ProductDto $productDto): Product
    {
        $product = Product::updateFromDto($productDto, $product);
        $this->entityManager->flush();

        return $product;
    }
}