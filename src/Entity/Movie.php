<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;
    #[ORM\OneToMany(targetEntity: Episode::class, mappedBy: 'movie')]
    private ?Collection $episodes = null;

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

    public function getEpisodes(): ?Collection
    {
        return $this->episodes;
    }

    public function setEpisodes(?Collection $episodes): void
    {
        $this->episodes = $episodes;
    }
}
