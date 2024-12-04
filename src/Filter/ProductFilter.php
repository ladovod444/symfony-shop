<?php

namespace App\Filter;

class ProductFilter
{
    private ?string $title = null;
    private ?int $category = null;
    private array|null $genre = [];

    public function getGenre(): array|null
    {
        return $this->genre;
    }

    public function setGenre(array|null $genre): void
    {
        $this->genre = $genre;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getCategory(): ?int
    {
        return $this->category;
    }

    public function setCategory(int $category): void
    {
        $this->category = $category;
    }

}