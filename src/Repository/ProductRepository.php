<?php

namespace App\Repository;

use App\Entity\Product;
use App\Filter\ProductFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByRecentlyViewedProducts($count, $recentlyViewedProducts): array
    {
        $products = $this->createQueryBuilder('p');

        return $products
//            ->leftJoin('App\Entity\RecentlyViewed', 'rw', Join::WITH, 'p.id = rw.product_id')
            ->where('p.id IN (:recentlyViewedProducts)')
            ->setParameter('recentlyViewedProducts', $recentlyViewedProducts)

            ->setMaxResults($count)

//            ->orderBy('rw.viewed', 'DESC')
            ->getQuery()
            ->getResult();

    }

    public function findByProductFilter(ProductFilter $productFilter): QueryBuilder
    {
        $products = $this->createQueryBuilder('p');

        if ($productFilter->getTitle()) {
            $products->where('p.title LIKE :title')
                ->setParameter('title', '%' . $productFilter->getTitle() . '%');
        }

        if ($productFilter->getCategory()) {
            $products->andWhere('p.category = :category')
                ->setParameter('category', $productFilter->getCategory());
        }

        if ($productFilter->getGenre()) {

            $requested_genres = $productFilter->getGenre();
            $genres = new ArrayCollection();
            foreach ($requested_genres as $genre) {
                $genres->add($genre);
            }

            // ВАЖНО:
            // Здесь сущность Product джойним с таблицей product_genre по
            // полю private Collection $genre;
            $products->join('p.genre', 'genres')
                ->andWhere('genres.id IN (:genres)')
                ->setParameter('genres', $genres);
        }

        $products->orderBy('p.id', 'DESC');
        // для пагинации вовращаем Query Builder
        return $products;
    }


    public function findAllGreaterThanPrice(int $price): array
    {
        $entityManager = $this->getEntityManager();

        // Doctrine Query Language
//        $query = $entityManager->createQuery(
//            'SELECT p
//            FROM App\Entity\Product p
//            WHERE p.regular_price > :price
//            ORDER BY p.regular_price ASC'
//        )->setParameter('price', $price);
//
//        // returns an array of Product objects
//        return $query->getResult();


        // QueryBuilder
        // automatically knows to select Products
        // the "p" is an alias you'll use in the rest of the query
//        $qb = $this->createQueryBuilder('p')
//            ->where('p.regular_price > :price')
//            ->setParameter('price', $price)
//            ->orderBy('p.regular_price', 'ASC');
//
////        if (!$includeUnavailableProducts) {
////            $qb->andWhere('p.available = TRUE');
////        }
//
//        $query = $qb->getQuery();
//
//        return $query->execute();


        // Querying with SQL
        // https://symfony.com/doc/current/doctrine.html#querying-with-sql
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM product p
            WHERE p.regular_price > :price
            ORDER BY p.regular_price ASC
            ';

        $resultSet = $conn->executeQuery($sql, ['price' => $price]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();

    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
