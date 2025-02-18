<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ReviewDTO
{
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 5)]
    public int $score;

    #[Assert\NotBlank]
    public string $reviewText;

    public ?string $name = null;

    public ?string $contact = null;
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer', message: "Episode ID must be an integer")]
    public ?int $episodeId = null;

    /**
     * @param int $score
     * @param string $reviewText
     * @param string|null $name
     * @param string|null $contact
     * @param int|null $episodeId
     */
    public function __construct(int $score, string $reviewText, ?string $name, ?string $contact, ?int $episodeId)
    {
        $this->score = $score;
        $this->reviewText = $reviewText;
        $this->name = $name;
        $this->contact = $contact;
        $this->episodeId = $episodeId;
    }


}