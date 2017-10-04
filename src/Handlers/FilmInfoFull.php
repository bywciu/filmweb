<?php
/**
 * @author Mateusz Bywalec
 * @copyright (c) 2017 bywciu
 * @description filmweb.pl API Bridge
 * @version 1.1
 * @link http://bywciu.com/
 * @link https://github.com/bywciu/filmweb
 * @license MIT
 */
namespace Bywciu\Filmweb\Handlers;

use Bywciu\Filmweb\Common\ApiException;
use Bywciu\Filmweb\Common\BasicHandler;

/**
 * Class FilmInfoFull
 * @package Bywciu\Filmweb\Handlers
 */
class FilmInfoFull extends BasicHandler
{
    /**
     * @var array
     */
    private $videoKeys = [
        'videoImageUrl', 'videoUrl', 'videoHDUrl', 'video480pUrl', 'ageRestriction'
    ];

    /**
     * FilmInfoFull constructor.
     */
    public function __construct()
    {
        $this->methodName = 'getFilmInfoFull';
        $this->responseKeys = [
            0 => 'title',
            1 => 'originalTitle',
            2 => 'avgRate',
            3 => 'votesCount',
            4 => 'genres',
            5 => 'year',
            6 => 'duration',
            7 => 'commentsCount',
            8 => 'forumUrl',
            9 => 'hasReview',
            10 => 'hasDescription',
            11 => 'imagePath',
            12 => 'video',
            13 => 'premiereWorld',
            14 => 'premiereCountry',
            15 => 'filmType',
            16 => 'seasonsCount',
            17 => 'episodesCount',
            18 => 'countriesString',
            19 => 'synopsis',
            23 => 'recommends',
            28 => 'premiereWorldPublic',
            29 => 'premiereCountryPublic'
        ];

        parent::__construct();
    }

    /**
     * Make request
     *
     * @param int $filmId
     *
     * @return array
     * @throws \Exception
     */
    public function getData($filmId)
    {
        try {
            $methodString = $this->getMethodString($filmId);
            $clientResponse = $this->client->request('get', '', [
                'query' => $this->prepareRequest($methodString)
            ]);

            return $this->mapper($this->parseResponse($clientResponse->getBody()->getContents()), $this->responseKeys);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * Map fields with response
     *
     * @param array $response
     * @param array $fields
     *
     * @return array
     * @throws ApiException
     */
    public function mapper($response, $fields = []) {
        if (empty($response)) {
            throw new ApiException('No content received', 204);
        }

        $return = [];
        foreach ($response as $i => $item) {
            // imagePath
            if ($i === 11 && !empty($item)) {
                $return[!empty($fields[$i]) ? $fields[$i] : $i] = $this->assetsUrls['filmImageUrl'] . $item;
            }
            // videosJson
            else if ($i === 12 && is_array($item)) {
                foreach($item as $k => $video) {
                    $return[!empty($this->videoKeys[$k]) ? $this->videoKeys[$k] : $k] = $video;
                }
            }
            else {
                $return[!empty($fields[$i]) ? $fields[$i] : $i] = $item;
            }
        }

        return $return;
    }
}
