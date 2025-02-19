<?php

namespace App\Tests\Integration\Controller;

use App\Controller\EpisodeController;
use App\Controller\ReviewController;
use App\Entity\Episode;
use App\Entity\Movie;
use App\Entity\Review;
use App\Tests\Integration\AbstractWebTestCase;
use PHPUnit\Framework\TestCase;

class ReviewControllerTest extends AbstractWebTestCase
{
    private ReviewController $reviewController;
    private Episode $episode;
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->reviewController = new ReviewController();
        $this->init();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }

    protected function init(): void {
        $movie = new Movie();
        $movie->setName('Rick and Morty');
        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        $episode = new Episode();
        $episode->setName('S01E01');
        $episode->setMovie($movie);
        $episode->setReleaseDate(new \DateTime("2013-12-02"));
        $this->entityManager->persist($episode);
        $this->entityManager->flush();
        $this->episode = $episode;
    }

    public function testReviewCreatesSuccessfully() {
        $this->client->request('POST', '/review', [
            'score' => 5,
            'reviewText' => 'Good',
            'episodeId' => $this->episode->getId(),
        ]);

        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(201);
        $this->assertJson($response->getContent());

        $json = json_decode($response->getContent(), true);
        $this->assertNotNull($json);

        $this->assertArrayHasKey('sentiment_score', $json);
        $this->assertArrayHasKey('message', $json);
        $this->assertIsFloat($json['sentiment_score']);

        $reviews = $this->entityManager->getRepository(Review::class)->findAll();
        $this->assertCount(1, $reviews);
        $this->assertNotEmpty($reviews[0]->getCreatedAt());
        $this->assertNotEmpty($reviews[0]->getSentimentScore());

        $this->client->request('POST', '/review', [
            'score' => 1,
            'reviewText' => 'Bad',
            'episodeId' => $this->episode->getId(),
            'contact' => 'test@gmail.com',
            'name' => 'David'
        ]);

        $reviews = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(Review::class, 'r')
            ->where('r.contact is not null')
            ->getQuery()
            ->getResult();
        $this->assertCount(1, $reviews);
        $this->assertNotEmpty($reviews[0]->getCreatedAt());
        $this->assertNotEmpty($reviews[0]->getSentimentScore());
        $this->assertNotEmpty($reviews[0]->getContact());
        $this->assertNotEmpty($reviews[0]->getName());
    }

    public function testIncorrectScoreType() {
        $this->client->request('POST', '/review', [
            'score' => 't',
            'reviewText' => 'Good',
            'episodeId' => $this->episode->getId(),
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testScoreNotInRange() {
        $this->client->request('POST', '/review', [
            'score' => 6,
            'reviewText' => 'Good',
            'episodeId' => $this->episode->getId(),
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testMissingScore() {
        $this->client->request('POST', '/review', [
            'reviewText' => 'Good',
            'episodeId' => $this->episode->getId(),
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testMissingReviewText() {
        $this->client->request('POST', '/review', [
            'score' => 5,
            'episodeId' => $this->episode->getId(),
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testMissingEpisodeId() {
        $this->client->request('POST', '/review', [
            'score' => 5,
            'reviewText' => 'Good'
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testEpisodeIdIncorrectFormat() {
        $this->client->request('POST', '/review', [
            'score' => 5,
            'reviewText' => 'Good',
            'episodeId' => 't',
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testUnknownEpisodeId() {
        $this->client->request('POST', '/review', [
            'score' => 5,
            'reviewText' => 'Good',
            'episodeId' => 777,
        ]);
        $this->assertResponseStatusCodeSame(404);
    }
}
