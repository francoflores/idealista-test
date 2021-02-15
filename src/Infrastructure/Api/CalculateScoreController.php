<?php

declare(strict_types=1);

namespace App\Infrastructure\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Core\Ads;

final class CalculateScoreController
{
    public function __invoke(): JsonResponse
    {
        try {
            $adsCore = new Ads();
            $adsScored = $adsCore->mathScore();
            return new JsonResponse(['ads' => $adsScored, 'msg' => 'OK'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['ads'=>[], 'msg'=>"Sorry, in this moment we can't calculate the scores. Please try again in a few hours"], 500);
        }
    }
}
