<?php

declare(strict_types=1);

namespace App\Domain;

final class Picture
{
    public function __construct(
        public int $id,
        public String $url,
        public String $quality,
    ) {
    }
}
