<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SeenProductsService
{

    const SEEN_PRODUCTS_LIMIT = 5;

    public function __construct(private ProductRepository $productRepository)
    {

    }

    /**
     * @param SessionInterface $session
     * @param int $excludeId
     * @return array Product
     *
     */
    function getUserSeenProducts(SessionInterface $session, int $excludeId): array
    {
        $seenProducts = $session->get('seenProducts');

        // Исключаем текущий товар из массива
        $seenProducts = array_filter($seenProducts, fn($sp) => $sp != $excludeId);

        return $this->productRepository->findBySeenProducts(self::SEEN_PRODUCTS_LIMIT, $seenProducts);
    }
}