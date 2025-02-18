<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class EpisodeDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly ?string $name,

        #[Assert\NotBlank]
        #[Assert\Date]
        #[Assert\Regex(
            pattern: "/^\d{4}-\d{2}-\d{2}$/",
            message: "The date should be in the format 'YYYY-MM-DD'."
        )]
        public readonly string $releaseDate,

        #[Assert\NotBlank]
        #[Assert\Type(type: "integer")]
        public readonly int $movieId
    )
    { }
}