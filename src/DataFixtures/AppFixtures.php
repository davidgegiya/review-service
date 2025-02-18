<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\Entity\Movie;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $rickAndMortyMovie = new Movie();
        $rickAndMortyMovie->setName("Rick and Morty");
        $manager->persist($rickAndMortyMovie);
        $manager->flush();

        $firstEpisode = new Episode();
        $firstEpisode->setName("S01E01");
        $firstEpisode->setReleaseDate(new \DateTime("2013-12-02"));
        $firstEpisode->setMovieId($rickAndMortyMovie->getId());
        $manager->persist($firstEpisode);
        $manager->flush();

        $reviews = [
          [
              'score' => 5,
              'review_text' => 'Pretty good, I like it',
              'name' => 'David',
              'contact' => 'test@example.com'
          ],
            [
                'score' => 2,
                'review_text' => 'Dislike. Movie only for children with poor drawing'
            ],
            [
                'score' => 4,
                'review_text' => 'It was ok. Maybe a bit overrated'
            ]
        ];
        foreach ($reviews as $review) {
            $review = new Review();
            $review->setReviewText($review['review_text']);
            $review->setEpisode($firstEpisode);
            $review->setScore($review['score']);
            $manager->persist($review);
        }
        $manager->flush();
    }
}
