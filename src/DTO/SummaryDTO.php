<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SummaryDTO
{
    #[Assert\NotBlank]
    public ?string $name = null;
    #[Assert\NotBlank]
    public ?string $release_date = null;
    #[Assert\NotBlank]
    public ?string $average_score = null;
    #[Assert\NotBlank]
    public ?array $latest_reviews = null;

    /**
     * @param string|null $name
     * @param string|null $release_date
     * @param string|null $average_score
     * @param array|null $latest_reviews
     */
    public function __construct(?string $name, ?string $release_date, ?string $average_score, ?array $latest_reviews)
    {
        $this->name = $name;
        $this->release_date = $release_date;
        $this->average_score = $average_score;
        $this->latest_reviews = $latest_reviews;
    }


}