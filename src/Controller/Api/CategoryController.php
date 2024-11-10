<?php

namespace App\Controller\Api;

use App\Dto\CategoryDto;
use App\Entity\Category;

use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
class CategoryController extends AbstractController
{
    const ITEMS_PER_PAGE = 10;

    public function __construct(
        private CategoryRepository      $categoryRepository,
        private EntityManagerInterface $entityManager,
        private UserRepository         $userRepository,
        private ParameterBagInterface $parameterBag
    ) {

    }

    #[Route('/category/list', name: 'api-categories-list', methods: ['GET'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Returns Categories list',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Category::class, groups: ['full']))
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
                //limit: self::ITEMS_PER_PAGE,
//                limit: $this->parameterBag->get('app:api_per_age'),
//                offset: $offset
            );
        }

        return $this->json($categories, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/category/{category}', name: 'api-category', methods: ['GET'], format: 'json')]
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
        description: 'Returns Category, yes!!!',
        content:  new Model(type: CategoryDto::class)
    )]
    public function getCategory(Category $category): Response
    {
        if (null === $category) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }
        return $this->json($category, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/category/dto', name: 'api-category-add-dto', methods: ['post'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Create a category',
        content:  new Model(type: CategoryDto::class)
    )]
    public function addDto(Request $request, #[MapRequestPayload] CategoryDto $CategoryDto): Response
    //                         #[MapRequestPayload(
    //                          // acceptFormat: 'json',
    //                          // resolver: 'App\Resolver\categoryResolver',
    //                         )] CategoryDto $CategoryDto): Response
    {
        $category = Category::createFromDto($CategoryDto);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $this->json($category, Response::HTTP_CREATED, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/category/dto/{category}', name: 'api-category-update-dto', methods: ['put'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Update a category',
        content:  new Model(type: CategoryDto::class)
    )]
    public function updateDto(Category $category, #[MapRequestPayload] CategoryDto $CategoryDto): Response
    {
        $category = Category::updateFromDto($CategoryDto, $category);
        $this->entityManager->flush();

        return $this->json($category, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/category/{category}', name: 'api-category-delete', methods: ['delete'], format: 'json')]
    #[OA\Response(
        response: 204,
        description: 'Delete category',
//        content:  new Model(type: CategoryDto::class)
    )]
//    #[IsGranted('category_DELETE', 'category')]
    public function delete(Category $category): Response
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
