<?php

namespace App\DataFixtures;

use App\Controller\EpisodeController;
use App\Controller\MovieController;
use App\Controller\ReviewController;
use App\Entity\Episode;
use App\Entity\Movie;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class AppFixtures extends Fixture
{
    private EpisodeController $episodeController;
    private MovieController $movieController;
    private ReviewController $reviewController;

    public function __construct(EpisodeController $episodeController, MovieController $movieController, ReviewController $reviewController)
    {
        $this->episodeController = $episodeController;
        $this->movieController = $movieController;
        $this->reviewController = $reviewController;
    }
    public function load(ObjectManager $manager): void
    {
        $this->movieController->createMovie("Rick and Morty");
        $movie = $manager->getRepository(Movie::class)->findAll()[0];
        $this->episodeController->createEpisode("S01E01","2013-12-02", $movie->getId());

        $reviews = [
          [
              'score' => 5,
              'review_text' => 'Pretty good, I like it',
              'name' => 'David',
              'contact' => 'test@example.com',
              'sentiment_score' => 0.8
          ],
            [
                'score' => 2,
                'review_text' => 'Dislike. Movie only for children with poor drawing',
                'sentiment_score' => 0.1
            ],
            [
                'score' => 4,
                'review_text' => 'It was ok. Maybe a bit overrated',
                'sentiment_score' => 0.5
            ]
        ];
        $episode = $manager->getRepository(Episode::class)->findAll()[0];
        foreach ($reviews as $object) {
            $review = new Review();
            $review->setReviewText($object['review_text']);
            $review->setEpisode($episode);
            $review->setScore($object['score']);
            $review->setContact($object['contact'] ?? '');
            $review->setName($object['name'] ?? '');
            $review->setSentimentScore($object['sentiment_score']);
            $manager->persist($review);
        }
        $manager->flush();
    }
}
