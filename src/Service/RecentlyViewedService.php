<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class RecentlyViewedService
{

    const RECENTLY_VIEWED_PRODUCTS_LIMIT = 4;

    public function __construct(private ProductRepository $productRepository, private Connection $connection)
    {

    }

    /**
     * @param SessionInterface $session
     * @param int $excludeId
     * @return array Product
     *
     */
    function getUserRecentlyViewedProducts(SessionInterface $session, int $excludeId, ?User $user): array
    {
        // Анонимный пользователь
        if ($user === null) {
            // Получим данные из сессии
            $recentlyViewedProducts = $this->getAnonymousRecentlyViewedProducts($session, $excludeId);
        } else {
            // Получим данные из таблицы recently_viewed
            $recentlyViewedProducts = $this->getAuthenticatedRecentlyViewedProducts($user, $excludeId);
        }

//        dd($recentlyViewedProducts);

        // Исключаем текущий товар из массива
        $recentlyViewedProducts = array_filter($recentlyViewedProducts, fn($rvp) => $rvp != $excludeId);

        return $this->productRepository->findByRecentlyViewedProducts(
            self::RECENTLY_VIEWED_PRODUCTS_LIMIT,
            $recentlyViewedProducts
        );
    }

    private function getAnonymousRecentlyViewedProducts($session, int $excludeId): array
    {
        $recentlyViewedProducts = $session->get('recentlyViewedProducts') ?? [];

        if (!in_array($excludeId, $recentlyViewedProducts)) {
            array_unshift($recentlyViewedProducts, $excludeId);
        }

//        $recentlyViewedProducts = array_slice($recentlyViewedProducts, 0, self::RECENTLY_VIEWED_PRODUCTS_LIMIT + 1);

        $session->set('recentlyViewedProducts', $recentlyViewedProducts);
        $recentlyViewedProducts = $session->get('recentlyViewedProducts');

        return $recentlyViewedProducts;
    }

    /**
     * @throws Exception
     */
    private function getAuthenticatedRecentlyViewedProducts(User $user, int $excludeId): array
    {
        // Получаем последние просмотренные товары.
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('product_id')
            ->from('recently_viewed'/** @type MODEL */)
            ->where('user_id = :user_id')
            ->orderBy('viewed', 'DESC')
//            ->setMaxResults(5)
            ->setParameter('user_id', $user->getId());
        $recentlyViewedProducts = $queryBuilder->fetchFirstColumn();
//       dd($recentlyViewedProducts);

        // Добавляем (или обновляем) имеющийся результат в таблицу.
        $sql = "INSERT INTO recently_viewed (id, user_id, product_id, viewed) 
            VALUES (DEFAULT, :user_id, :product_id, :viewed)
            ON CONFLICT(user_id, product_id) DO UPDATE SET viewed = :viewed";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('product_id', $excludeId);
        $stmt->bindValue('viewed', time());
        $stmt->bindValue('user_id', $user->getId());
        $stmt->executeQuery();
        $recentlyViewedProducts = array_slice($recentlyViewedProducts, 0, self::RECENTLY_VIEWED_PRODUCTS_LIMIT + 1);

        return $recentlyViewedProducts;

    }

}