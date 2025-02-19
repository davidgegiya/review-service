<?php

namespace App\Controller;

use App\DTO\MovieDTO;
use App\DTO\ReviewDTO;
use App\Entity\Movie;
use App\Exception\ParametersException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validation;

class MovieController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * Create movie by name
     * @param string $name
     * @return bool
     */
    public function createMovie(string $name): bool {
        $movie = new Movie();
        $movie->setName($name);

        $this->em->persist($movie);
        $this->em->flush();
        return true;
    }
}