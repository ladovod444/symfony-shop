<?php

namespace App\Service;

use App\Dto\GenreDto;
use App\Entity\Genre;
use App\Repository\GenreRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GenreService
{
    public function __construct(
        private readonly GenreRepository $genreRepository,
        private readonly ParameterBagInterface $parameterBag,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param int $page
     * @param int $offset
     * @return GenreDto []
     */
    public function getGenres(int $page = 1, int $offset = 10): array
    {
        $genres = $this->genreRepository->findBy(
            [],
            ['id' => 'DESC'],
            //limit: self::ITEMS_PER_PAGE,
            limit: $this->parameterBag->get('app:api_per_age'),
            offset: $offset
        );

        if (!$page) {
            $genres = $this->genreRepository->findBy(
                [],
                ['id' => 'DESC'],
            );
        }

        return array_map(
            fn(Genre $item) => new GenreDto(
                $item->getId(),
                $item->getTitle(),
                $item->getSlug(),
            ),
            $genres
        );
    }

    public function createGenre(GenreDto $genreDto): Genre
    {
        $genre = Genre::createFromDto($genreDto);

        $this->entityManager->persist($genre);
        $this->entityManager->flush();

        return $genre;
    }

    public function updateGenre(GenreDto $genreDto, Genre $genre): Genre
    {
        $genre = Genre::updateFromDto($genreDto, $genre);
        $this->entityManager->flush();

        return $genre;
    }
}