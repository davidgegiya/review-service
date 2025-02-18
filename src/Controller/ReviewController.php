<?php

namespace App\Controller;

use App\DTO\ReviewDTO;
use App\Entity\Episode;
use App\Entity\Review;
use App\Service\SentimentAnalyzer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReviewController extends AbstractController
{
    #[Route('/review', name: 'make_review', methods: ['POST'])]
    public function makeReview(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        SentimentAnalyzer $sentimentAnalyzer
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $dto = new ReviewDTO(
            $data['score'] ?? null,
            $data['review_text'] ?? null,
            $data['name'] ?? null,
            $data['contact'] ?? null,
            $data['episode_id'] ?? null,
        );

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $episode = $em->getRepository(Episode::class)->find($dto->episodeId);
        if (!$episode) {
            return new JsonResponse(['error' => 'Episode not found'], 404);
        }

        $sentimentScore = $sentimentAnalyzer->analyze($dto->reviewText);

        $review = new Review();
        $review->setScore($dto->score);
        $review->setReviewText($dto->reviewText);
        $review->setSentimentScore($sentimentScore);
        $review->setEpisode($episode);
        $review->setName($dto->name);
        $review->setContact($dto->contact);

        $em->persist($review);
        $em->flush();

        return new JsonResponse([
            'message' => 'Review created successfully',
            'sentiment_score' => $sentimentScore
        ], 201);
    }
}