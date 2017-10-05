<?php
/**
 * @author Mateusz Bywalec
 * @copyright (c) 2017 bywciu
 * @description filmweb.pl API Bridge
 * @version 1.1.5
 * @link http://bywciu.com/
 * @link https://github.com/bywciu/filmweb
 * @license MIT
 */

namespace Bywciu\Filmweb;

/**
 * Class Helper
 * @package Bywciu\Filmweb
 */
/**
 * Class Helper
 * @package Bywciu\Filmweb
 */
class Helper
{
    /**
     * @var Api
     */
    private $api;

    /**
     * Helper constructor.
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * Get user's "want to see" list with movie's info
     *
     * @param int $userId
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function getUserFilmsWantToSeeInfo($userId = null, $page = 0, $limit = 20)
    {
        $filmsWantToSee = $this->api->getUserFilmsWantToSee($userId, $page, $limit);
        $filmsWantToSeeChunked = array_chunk($filmsWantToSee, 20);
        $filmsWantToSeeInfoChunked = [];
        $filmsWantToSeeInfo = [];

        if (!empty($filmsWantToSeeChunked)) {
            foreach ($filmsWantToSeeChunked as $chunk) {
                $filmIds = [];

                foreach ($chunk as $film) {
                    $filmIds[] = $film['filmId'];
                }

                $filmsWantToSeeInfoChunked[] = $this->api->getFilmsInfoShort($filmIds);
            }

            foreach ($filmsWantToSeeInfoChunked as $chunk) {
                foreach ($chunk as $film) {
                    $filmsWantToSeeInfo[] = $film;
                }
            }

            $filmsWantToSee = $this->array_merge_callback(
                $filmsWantToSee,
                $filmsWantToSeeInfo,
                function ($item1, $item2) {
                    return $item1['filmId'] === $item2['filmId'];
                }
            );
        }

        return $filmsWantToSee;
    }

    /**
     * Merge two arrays by compare function
     *
     * @param array $array1
     * @param array $array2
     * @param callback $callback
     *
     * @return array
     */
    private function array_merge_callback($array1, $array2, $callback)
    {
        $result = [];

        foreach ($array1 as $item1) {
            foreach ($array2 as $item2) {
                if ($callback($item1, $item2)) {
                    $result[] = array_merge($item1, $item2);
                }
            }
        }

        return $result;
    }
}
