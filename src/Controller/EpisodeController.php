<?php

namespace App\Controller;

use App\DTO\EpisodeDTO;
use App\Entity\Episode;
use App\Entity\Movie;
use App\Exception\ParametersException;
use App\Repository\EpisodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EpisodeController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Create new episode
     * @param string $name
     * @param string $release_date
     * @param int $movieId
     * @return bool
     * @throws \DateMalformedStringException
     * @throws ParametersException
     */
    public function createEpisode(
        string $name, string $release_date, int $movieId
    ): bool {
        $movie = $this->em->getRepository(Movie::class)->find($movieId);
        if (!$movie) {
            throw new ParametersException('Movie with id ' . $movieId . ' was not found');
        }

        $episode = new Episode();
        $episode->setName($name);
        $episode->setReleaseDate(new \DateTime($release_date));
        $episode->setMovieId($movieId);

        $this->em->persist($episode);
        $this->em->flush();
        return true;
    }

    #[Route('/summary/{id}', name: 'get_summary', methods: ['GET'])]
    public function getSummary(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        EpisodeRepository $episodeRepository
    ): JsonResponse {
        $id = $request->attributes->get('id');
        if ( !is_numeric($id) ) {
            return new JsonResponse(['errors' => 'Incorrect id format. Should be an integer'], Response::HTTP_BAD_REQUEST);
        }

        $episode = $em->getRepository(Episode::class)->find((int)$id);
        if (!$episode) {
            return new JsonResponse(['error' => 'Episode not found'], 404);
        }

        $summary = $episodeRepository->getSummary($episode->getId());
        if ( !$summary ) {
            return new JsonResponse(['summary' => []], 200);
        }
        return new JsonResponse(['summary' => $summary], 200);
    }
}