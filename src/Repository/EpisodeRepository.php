<?php

namespace App\Repository;

use App\DTO\SummaryDTO;
use App\Entity\Episode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Episode>
 */
class EpisodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Episode::class);
    }

    public function getSummary(int $id): ?SummaryDTO {
        // Here we use raw SQL to implement window functionality
        $sql = "
            SELECT 
                e.release_date, 
                e.name as episode_name, 
                AVG(r.sentiment_score) OVER() AS avg_score, 
                r.review_text, 
                r.score, 
                r.name, 
                r.contact, 
                r.sentiment_score, 
                r.created_at 
            FROM episode e
            INNER JOIN review r ON e.id = r.episode_id
            WHERE e.id = :id
            ORDER BY r.created_at DESC
            LIMIT 3
        ";

        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery();
        $result = $result->fetchAllAssociative();

        if(empty($result)){
            return null;
        }

        $summary = new SummaryDTO(
            $result[0]['episode_name'],
            $result[0]['release_date'],
            $result[0]['avg_score'],
            array_map(function($review){
                return [
                    'score' => $review['score'],
                    'review_text' => $review['review_text'],
                    'sentiment_score' => $review['sentiment_score'],
                    'name' => $review['name'],
                    'created_at' => $review['created_at'],
                    'contact' => $review['contact'],
                ];
            }, $result)
        );

        return $summary;
    }
}
