<?php

namespace App\Controller\Sandbox;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestProductController extends AbstractController
{
    #[Route('/test/product/{product}', name: 'app_test_product')]
    public function index(Product $product): Response
    {

        //echo $product;  die();

        return $this->render('test_product/index.html.twig', [
            'controller_name' => 'TestProductController',
        ]);
    }

    #[Route('/test/product-json/{product}', name: 'app_test_product_json', methods: ['GET'])]
    public function indexJson(Product $product): Response
    {

        //echo $product;  die();

        return $this->json(
           $product->getTitle(),
           Response::HTTP_OK,
            []
        );

//        return $this->render('test_product/index.html.twig', [
//            'controller_name' => 'TestProductController',
//        ]);
    }

    #[Route('/test/product/offer-id/{offer_id}', name: 'app_test_product_offer_id')]
    public function indexByOfferId(Product $product): Response
    {
        echo $product;  //die();
        return $this->render('test_product/index.html.twig', [
            'controller_name' => 'TestProductController',
        ]);
    }

    #[Route('/test/product/price/{price}', name: 'app_test_product_by_price')]
    public function indexByPriceGreater(int $price, ProductRepository $productRepository): Response
    {

        $products = $productRepository->findAllGreaterThanPrice($price);

        dd($products);

        //echo $price;  die();
        return $this->render('test_product/index.html.twig', [
            'controller_name' => 'TestProductController',
        ]);
    }

    // findAllGreaterThanPrice
}
