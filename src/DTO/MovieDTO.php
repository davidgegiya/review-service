<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class MovieDTO
{
    #[Assert\NotBlank]
    public ?string $name = null;

    /**
     * @param string|null $name
     */
    public function __construct(?string $name)
    {
        $this->name = $name;
    }


}