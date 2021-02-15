<?php

declare(strict_types=1);

namespace App\Infrastructure\Api;

use App\Infrastructure\Api\PublicAd;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Core\Ads;

final class PublicListingController
{
    public function __invoke(): JsonResponse
    {
        try {
            $adsCore = new Ads();
            $ads =  $adsCore->getAds();
            $publicAds = [];
            foreach ($ads as $ad) {
                $pictureUrls = $adsCore->getPicturesUrlByAd($ad);
                $publicAd = new PublicAd(
                    $ad->id,
                    $ad->typology,
                    $ad->description,
                    $pictureUrls,
                    $ad->houseSize,
                    $ad->gardenSize,
                );

                array_push($publicAds, $publicAd);
            }
            return new JsonResponse(['ads' => $publicAds, 'msg' => 'OK'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['ads'=>[], 'msg'=>"Sorry, in this moment we are not available. Please try again in a few hours"], 500);
        }

    }
}
