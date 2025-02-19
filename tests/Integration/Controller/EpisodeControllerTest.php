<?php

namespace App\Tests\Integration\Controller;

use App\Controller\EpisodeController;
use App\Entity\Episode;
use App\Entity\Movie;
use App\Entity\Review;
use App\Exception\ParametersException;
use App\Tests\Integration\AbstractTestCase;
use App\Tests\Integration\AbstractWebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EpisodeControllerTest extends AbstractWebTestCase
{
    private EpisodeController $episodeController;
    private Movie $movie;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->episodeController = new EpisodeController($this->entityManager);
        $this->createMovie();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }

    protected function createMovie()
    {
        $movie = new Movie();
        $movie->setName('Rick and Morty');
        $this->entityManager->persist($movie);
        $this->entityManager->flush();
        $this->movie = $movie;
    }

    protected function createStaticEpisode(): Episode {
        $episode = new Episode();
        $episode->setName('S01E01');
        $episode->setReleaseDate(new \DateTime('2013-12-02'));
        $episode->setMovie($this->movie);
        $this->entityManager->persist($episode);
        $this->entityManager->flush();
        return $episode;
    }

    protected function createStaticReview(Episode $episode, int $score, float $sentiment_score, string $review_text, ?string $name = null, ?string $contact = null): Review
    {
        $review = new Review();
        $review->setEpisode($episode);
        $review->setScore($score);
        $review->setSentimentScore($sentiment_score);
        $review->setReviewText($review_text);
        $review->setName($name);
        $review->setContact($contact);
        $this->entityManager->persist($review);
        $this->entityManager->flush();
        return $review;
    }

    public function testSuccessCreateEpisode(): void {
        $result = $this->episodeController->createEpisode('S01E01', '2013-12-02', $this->movie->getId());

        $this->assertTrue($result);
        $episodeFromDatabase = $this->entityManager->getRepository(Episode::class)->findAll();
        $this->assertCount(1, $episodeFromDatabase);
        $this->assertSame('S01E01', $episodeFromDatabase[0]->getName());
        $this->assertSame('2013-12-02', $episodeFromDatabase[0]->getReleaseDate()->format('Y-m-d'));
        $this->assertSame($this->movie->getId(), $episodeFromDatabase[0]->getMovie()->getId());
        $this->assertEmpty($episodeFromDatabase[0]->getReviews());
    }

    public function testUnknownMovieId(): void {
        $this->expectException(ParametersException::class);
        $result = $this->episodeController->createEpisode('S01E01', '2013-12-02', $this->movie->getId() + 1);
    }

    public function testSuccessSummaryGetWithOneReview(): void {
        $episode = $this->createStaticEpisode();
        $review = $this->createStaticReview(
            $episode,
            3,
            0.5,
            'test',
        );

        $crawler = $this->client->request('GET', '/summary/' . $episode->getId());

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $result = json_decode($responseContent, true);
        $this->assertIsArray($result);

        $this->assertSame($episode->getName(), $result['summary']['name']);
        $this->assertSame('0.5', $result['summary']['average_score']);
        $this->assertSame($episode->getReleaseDate()->format('Y-m-d'), $result['summary']['release_date']);
        $this->assertCount(1, $result['summary']['latest_reviews']);

        $this->assertSame(3, $result['summary']['latest_reviews'][0]['score']);
        $this->assertSame('test', $result['summary']['latest_reviews'][0]['review_text']);
        $this->assertSame('0.5', $result['summary']['latest_reviews'][0]['sentiment_score']);
        $this->assertEmpty($result['summary']['latest_reviews'][0]['name']);
        $this->assertEmpty($result['summary']['latest_reviews'][0]['contact']);
        $this->assertNotEmpty($result['summary']['latest_reviews'][0]['created_at']);
    }

    public function testSuccessSummaryGetWithMoreThanThreeReviews(): void {
        $episode = $this->createStaticEpisode();
        $review1 = $this->createStaticReview(
            $episode,
            3,
            0.8,
            'test',
        );
        sleep(1);
        $review2 = $this->createStaticReview(
            $episode,
            4,
            0.8,
            'test2',
        );
        sleep(1);
        $review3 = $this->createStaticReview(
            $episode,
            4,
            0.7,
            'test3',
        );
        sleep(1);
        $review4 = $this->createStaticReview(
            $episode,
            1,
            0.1,
            'test4',
            'David',
            'test@gmail.com'
        );

        $crawler = $this->client->request('GET', '/summary/' . $episode->getId());

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $result = json_decode($responseContent, true);
        $this->assertIsArray($result);

        $this->assertSame($episode->getName(), $result['summary']['name']);
        $this->assertSame('0.6', $result['summary']['average_score']); // (4+4+1)/3
        $this->assertSame($episode->getReleaseDate()->format('Y-m-d'), $result['summary']['release_date']);
        $this->assertCount(3, $result['summary']['latest_reviews']);

        $this->assertSame(1, $result['summary']['latest_reviews'][0]['score']);
        $this->assertSame('test4', $result['summary']['latest_reviews'][0]['review_text']);
        $this->assertSame('0.1', $result['summary']['latest_reviews'][0]['sentiment_score']);
        $this->assertSame('David', $result['summary']['latest_reviews'][0]['name']);
        $this->assertSame('test@gmail.com', $result['summary']['latest_reviews'][0]['contact']);
        $this->assertNotEmpty($result['summary']['latest_reviews'][0]['created_at']);
    }
}
