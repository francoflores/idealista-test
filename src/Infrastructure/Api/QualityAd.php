<?php

declare(strict_types=1);

namespace App\Infrastructure\Api;

use DateTimeImmutable;

final class QualityAd
{
    public function __construct(
        public int $id,
        public String $typology,
        public String $description,
        public array $pictureUrls,
        public int $houseSize,
        public ?int $gardenSize = null,
        public ?int $score = null,
        public ?DateTimeImmutable $irrelevantSince = null,
    ) {
    }
}
