<?php

declare(strict_types=1);

namespace App\Infrastructure\Api;

final class PublicAd
{
    public function __construct(
        public int $id,
        public String $typology,
        public String $description,
        public array $pictureUrls,
        public int $houseSize,
        public ?int $gardenSize = null,
    ) {
    }
}
