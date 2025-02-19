<?php

namespace App\Entity;

use App\Repository\EpisodeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpisodeRepository::class)]
class Episode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $release_date = null;


    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'episode')]
    private ?Collection $reviews = null;

    #[ORM\ManyToOne(targetEntity: Movie::class, inversedBy: 'episodes')]
    #[ORM\JoinColumn(name: 'movie_id', referencedColumnName: 'id', nullable: false)]
    private Movie $movie;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->release_date;
    }

    public function setReleaseDate(\DateTimeInterface $release_date): static
    {
        $this->release_date = $release_date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReviews(): mixed
    {
        return $this->reviews;
    }

    /**
     * @param mixed $reviews
     */
    public function setReviews(mixed $reviews): void
    {
        $this->reviews = $reviews;
    }

    public function getMovie(): Movie
    {
        return $this->movie;
    }

    public function setMovie(Movie $movie): void
    {
        $this->movie = $movie;
    }
}
