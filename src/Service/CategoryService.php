<?php

namespace App\Service;

use App\Dto\CategoryDto;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly ParameterBagInterface $parameterBag,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param int $page
     * @param int $offset
     * @return CategoryDto []
     */
    public function getCategories(int $page = 1, int $offset = 10): array
    {
        $categories = $this->categoryRepository->findBy(
            [],
            ['id' => 'DESC'],
            //limit: self::ITEMS_PER_PAGE,
            limit: $this->parameterBag->get('app:api_per_age'),
            offset: $offset
        );

        if (!$page) {
            $categories = $this->categoryRepository->findBy(
                [],
                ['id' => 'DESC'],
            );
        }

        return array_map(
            fn(Category $item) => new CategoryDto(
                $item->getId(),
                $item->getTitle(),
                $item->getSlug(),
            ),
            $categories
        );
    }

    public function createCategory(CategoryDto $categoryDto): Category
    {
        $category = Category::createFromDto($categoryDto);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    public function updateCategory(CategoryDto $categoryDto, Category $category): Category
    {
        $category = Category::updateFromDto($categoryDto, $category);
        $this->entityManager->flush();

        return $category;
    }
}