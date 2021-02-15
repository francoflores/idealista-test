<?php

declare(strict_types=1);

namespace App\Infrastructure\Core;

use App\Domain\Ad;
use App\Domain\Picture;
use App\Infrastructure\Persistence\InFileSystemPersistence;
use JetBrains\PhpStorm\Pure;

class Ads
{
    private array $ads;
    private array $pictures;

    const POINT_RELEVANT = 40;

    public function __construct()
    {
        $dataPersistence = new InFileSystemPersistence();
        $this->ads = $dataPersistence->getAds();
        $this->pictures = $dataPersistence->getPictures();
    }

    /**
     * Search all pictures url of and particular ad.
     * @param $ad Ad object
     * @return array pictures urls
     */
    public function getPicturesUrlByAd(Ad $ad): array
    {
        $picturesUrl = [];
        foreach ($ad->pictures as $pictureId)
        {
            $pictureIndex = array_search($pictureId, array_column($this->pictures, 'id'));
            if($pictureIndex !== false) {
                array_push($picturesUrl, $this->pictures[$pictureIndex]->url);
            }
        }

        return $picturesUrl;
    }

    /**
     *  Filter ads by index
     *      0 returns all index.
     *      1 returns relevant ads
     *      2 returns irrelevant ads
     *
     * Note: relevant ads are those who have 40 point of score or more.
     * @param int $filter
     * @return array
     */
    public function getAds($filter = 1): array
    {
        $ads = $this->mathScore();
        $adsFilter = [];
        switch ($filter) {
            case 0:
                $adsFilter = $ads;
                break;
            case 1:
                $adsFilter = array_filter($ads, function ($ad) { return $ad->score >= self::POINT_RELEVANT; });
                usort($adsFilter, function($a, $b) { return $a->score < $b->score; });
                break;
            case 2:
                $adsFilter = array_filter($ads, function ($ad) { return $ad->score < self::POINT_RELEVANT; });
                usort($adsFilter, function($a, $b) { return $a->score < $b->score; });
                break;
            default:
                break;
        }

        return $adsFilter;
    }

    /**
     *  This function calculates the ads score
     * @return array
     */
    public function mathScore(): array
    {
        $ads = $this->ads;
        $pictures = $this->pictures;
        foreach ($ads as $ad) {
            $score = 0;
            $score += $this->checkDescription($ad);
            $score += $this->checkPictures($ad, $pictures);
            $score += $this->isComplete($ad)? 40:0;
            $ad->score = $score;
            $ad->irrelevantSince = $ad->score < 40 ? (new \DateTimeImmutable()):null;
        }

        return $ads;
    }

    private function checkDescription(Ad $ad): int
    {
        $score = 0;
        if(preg_replace('~\x{00a0}~','',$ad->description)  != '') {
            $score += 5;
            $numWords = str_word_count($ad->description, 0);
            if($ad->typology == 'FLAT') {
                $score += ($numWords >= 20 && $numWords <=49) ? 10:($numWords >= 50? 30:0);
            } else if($ad->typology == 'CHALET') {
                $score += $numWords >= 50 ? 20:0;
            }
            $score += str_contains(strtoupper($ad->description), ' LUMINOSO ' ) ? 5:0;
            $score += str_contains(strtoupper($ad->description), ' NUEVO ' ) ? 5:0;
            $score += str_contains(strtoupper($ad->description), ' CÉNTRICO ' ) ? 5:0;
            $score += str_contains(strtoupper($ad->description), ' REFORMADO ' ) ? 5:0;
            $score += str_contains(strtoupper($ad->description), ' ÁTICO ' ) ? 5:0;
        }

        return $score;
    }

    private function getPictureById($pictures, $pictureId): Picture | null
    {
        foreach ($pictures as $picture) {
            if($picture->id == $pictureId) {
                return $picture;
            }
        }
        return null;
    }

    #[Pure] private function checkPictures(Ad $ad, array $pictures): int
    {
        $score = 0;
        if(count($ad->pictures) > 0) {
            foreach ($ad->pictures as $pictureId) {
                $picture = $this->getPictureById($pictures, $pictureId);
                $score += $picture->quality == 'HD'? 20:10;
            }
        }
        else {
            $score = -10;
        }

        return $score;
    }

    private function isComplete(Ad $ad): bool
    {
        $hasDescription = preg_replace('~\x{00a0}~','',$ad->description) != '';
        if(
            (!$hasDescription && $ad->typology != 'GARAGE')
            || count($ad->pictures) == 0
        ) {
            return false;
        }

        if($ad->typology == 'FLAT') {
            return !($ad->houseSize == null || $ad->houseSize == 0);
        } else if($ad->typology == 'CHALET') {
            return !(($ad->houseSize == null || $ad->houseSize == 0) && ($ad->gardenSize == null || $ad->gardenSize == 0));
        }

        return true;

    }
}