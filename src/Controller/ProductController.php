<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\SeenProductsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{

    public function __construct(private SeenProductsService $seenProductsService)
    {

    }

    #[Route('/product/{product}', name: 'app_product')]
    public function index(Product $product, Request $request): Response
    {

        // ПОКА для анонимных юзеров сделаем добавление товаров
        $session = $request->getSession();
        $seenProducts = $session->get('seenProducts') ?? [];

        if (!in_array($product->getId(), $seenProducts)) {
//            $seenProducts[] = $product->getId();
            array_unshift($seenProducts, $product->getId());
        }

        //
        $seenProducts = array_slice($seenProducts, 0, SeenProductsService::SEEN_PRODUCTS_LIMIT + 1);

        $session->set('seenProducts', $seenProducts);
//        var_dump($seenProducts);


        $excludeId = $product->getId();

        return $this->render('product/index.html.twig', [
            'product' => $product,
            'seenProducts' => $this->seenProductsService->getUserSeenProducts($session, $excludeId),
        ]);
    }
}
