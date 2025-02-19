<?php

namespace App\Tests\Integration\Controller;

use App\Controller\MovieController;
use App\Entity\Movie;
use App\Tests\Integration\AbstractTestCase;
use App\Tests\Integration\AbstractWebTestCase;
use PHPUnit\Framework\TestCase;

class MovieControllerTest extends AbstractTestCase
{
    private MovieController $controller;
    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new MovieController($this->entityManager);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }

    public function testSuccessfulMovieCreate(): void {
        $name = 'Rick and Morty';
        $this->controller->createMovie($name);

        $this->assertCount(1, $this->entityManager->getRepository(Movie::class)->findAll());
        $this->assertSame($name, $this->entityManager->getRepository(Movie::class)->findAll()[0]->getName());
    }
}
