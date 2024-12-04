<?php

namespace App\Controller\Api;

use App\Dto\GenreDto;

use App\Entity\Genre;
use App\Service\GenreService;
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
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[OA\Tag(name: "Products api")]
#[Route('/api/v1')]
#[Security(name: "Bearer")]
class GenreController extends AbstractController
{
    const ITEMS_PER_PAGE = 10;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private GenreService $genreService
    ) {

    }

    #[Route('/genre/list', name: 'api-genres-list', methods: ['GET'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Returns Genres list',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Genre::class, groups: ['full']))
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
        $genres = $this->genreService->getGenres($page, $offset);

        return $this->json($genres, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/genre/{slug}', name: 'api-genre', methods: ['GET'], format: 'json')]
    #[OA\Parameter(
        name: "Slug",
        description: "Genre slug",
        in: "header",
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns a Genre',
        content: new Model(type: GenreDto::class)
    )]
    public function getGenre(#[MapEntity(mapping: ["slug" => "slug"])] Genre $genre): Response
    {
        return $this->json($genre, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/genre/dto', name: 'api-genre-add-dto', methods: ['post'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Create a genre',
        content: new Model(type: GenreDto::class)
    )]
    public function addDto(Request $request, #[MapRequestPayload] GenreDto $genreDto): Response
    {
        $genre = $this->genreService->createGenre($genreDto);

        return $this->json($genre, Response::HTTP_CREATED, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/genre/dto/{genre}', name: 'api-genre-update-dto', methods: ['put'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Update a genre',
        content: new Model(type: GenreDto::class)
    )]
    public function updateDto(Genre $genre, #[MapRequestPayload] GenreDto $genreDto): Response
    {
        $genre = $this->genreService->updateGenre($genreDto, $genre);

        return $this->json($genre, Response::HTTP_OK, context: [
            AbstractNormalizer::GROUPS => ['products:api:list'],
        ]);
    }

    #[Route('/genre/{genre}', name: 'api-genre-delete', methods: ['delete'], format: 'json')]
    #[OA\Response(
        response: 204,
        description: 'Delete genre',
//        content:  new Model(type: CategoryDto::class)
    )]
//    #[IsGranted('category_DELETE', 'category')]
    public function delete(
        Genre $genre
    ): Response {
        $this->entityManager->remove($genre);
        $this->entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
