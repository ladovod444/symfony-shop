<?php

namespace App\Entity;

use App\Dto\GenreDto;
use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['products:api:list'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['products:api:list'])]
    private ?string $slug = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'genre')]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->addGenre($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            $product->removeGenre($this);
        }

        return $this;
    }

    public static function createFromDto(GenreDto $genreDto): static
    {
        $genre = new self();
        $genre->setTitle($genreDto->title)
            ->setSlug($genreDto->slug);

        return $genre;
    }

    public static function updateFromDto(GenreDto $genreDto, Genre $genre): static
    {
        $genre->setTitle($genreDto->title)
            ->setSlug($genreDto->slug);
        return $genre;
    }

    public function __toString(){
        return $this->title;
    }
}
