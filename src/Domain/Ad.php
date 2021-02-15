<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

final class Ad
{
    public function __construct(
        public int $id,
        public String $typology,
        public String $description,
        public array $pictures,
        public int $houseSize,
        public ?int $gardenSize = null,
        public ?int $score = null,
        public ?DateTimeImmutable $irrelevantSince = null,
    ) {
    }
}
