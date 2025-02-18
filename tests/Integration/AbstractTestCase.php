<?php

namespace App\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractTestCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->clearDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }


    private function clearDatabase(): void
    {
        $connection = $this->entityManager->getConnection();
        $schemaManager = $connection->createSchemaManager();
        $tables = $schemaManager->listTables();

        $connection->beginTransaction();

        try {
            foreach ($tables as $table) {
                $connection->executeStatement('TRUNCATE ' . $table->getName() . ' CASCADE');
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }
}
