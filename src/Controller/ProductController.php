<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\RecentlyViewedService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{

    public function __construct(private RecentlyViewedService $recentlyViewedService)
    {

    }

    #[Route('/product/{product}', name: 'app_product')]
    public function index(Product $product, Request $request): Response
    {

        // @todo
        // установить бандл для uuid и добавить поле для Product
        // По возможности создать ProductEvent и связать его с Product ???
        // добавить message и messageHandler
        // добавить таблицу seen_products
        // для авторизованного юзера добавлять в таблицу записи:
        //      product  -> user
        //               -> user
        //               -> user

        // ПОКА для анонимных юзеров сделаем добавление товаров
        $user = $this->getUser();
        $session = $request->getSession();
        $excludeId = $product->getId();

        return $this->render('product/index.html.twig', [
            'product' => $product,
            'seenProducts' => $this->recentlyViewedService->getUserRecentlyViewedProducts($session, $excludeId, $user),
        ]);
    }
}
