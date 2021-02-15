<?php

declare(strict_types=1);

namespace App\Infrastructure\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Core\Ads;
use App\Infrastructure\Api\QualityAd;

final class QualityListingController
{
    public function __invoke($filter): JsonResponse
    {
        try {
            $adsCore = new Ads();
            $ads =  $adsCore->getAds($filter);
            $qualityAds = [];
            foreach ($ads as $ad) {
                $pictureUrls = $adsCore->getPicturesUrlByAd($ad);
                $qualityAd = new QualityAd(
                    $ad->id,
                    $ad->typology,
                    $ad->description,
                    $pictureUrls,
                    $ad->houseSize,
                    $ad->gardenSize,
                    $ad->score,
                    $ad->irrelevantSince
                );

                array_push($qualityAds, $qualityAd);
            }
            return new JsonResponse(['ads' => $qualityAds]);
        } catch (\Exception $e) {
            return new JsonResponse(['ads'=>[], 'msg'=>"Sorry, in this moment we are not available. Please try again in a few hours"], 500);
        }

    }
}
