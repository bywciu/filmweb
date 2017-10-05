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
namespace Bywciu\Filmweb\Handlers;

use Bywciu\Filmweb\Common\ApiException;
use Bywciu\Filmweb\Common\BasicHandler;

/**
 * Class FilmVideos
 * @package Bywciu\Filmweb\Handlers
 */
class FilmVideos extends BasicHandler
{
    /**
     * FilmVideos constructor.
     */
    public function __construct()
    {
        $this->methodName = 'getFilmVideos';
        $this->responseKeys = [
            'imagePath', 'imagePathMedium', 'videoPath'
        ];

        parent::__construct();
    }

    /**
     * Make request
     *
     * @param int $filmId
     * @param int $page
     * @param int $limit
     *
     * @return array
     * @throws ApiException
     */
    public function getData($filmId, $page = 0, $limit = 100)
    {
        try {
            $methodString = $this->getMethodString($filmId, $page, $limit);
            $clientResponse = $this->client->request('get', '', [
                'query' => $this->prepareRequest($methodString)
            ]);

            return $this->parseResponse($clientResponse->getBody()->getContents());
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * Return parsed filmweb response
     *
     * @param string $response
     *
     * @return array
     * @throws \Exception
     */
    public function parseResponse($response)
    {
        try {
            $return = [];
            $parsedResponse = parent::parseResponse($response);

            if (!empty($parsedResponse)) {
                foreach ($parsedResponse as $entry) {
                    $return[] = $this->mapper($entry, $this->responseKeys);
                }
            }

            return $return;
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }
}
